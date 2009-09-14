<?php
/*
 Plugin Name: wp-comment-warrior
 Plugin URI: http://www.mathelite.cn/archives/wordpress-comment-warrior-plugin.html
 Version: 0.3.15
 Author: flarefox
 Description: Show the most active commenters. The time filter can be calender month, calender year or custom days. 
 Author URI: http://mathelite.cn/
*/
?>
<?php
/*  
		Copyright 2009  FlareFox  (email : flarefox@163.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
?>
<?php
$ffox_lvmct_version = '0.3.15';
$ffox_lvmct_date = '2009.09.15';
/*
 Load WP-Config File If This File Is Called Directly
*/
if (!function_exists('add_action'))
{
	$wp_root = dirname(dirname(dirname(dirname(__FILE__))));
	if (file_exists($wp_root.'/wp-load.php')) {
		require_once($wp_root.'/wp-load.php');
	} else {
		require_once($wp_root.'/wp-config.php');
	}
}

/*
 Get WP Path and URL
*/
if ( !defined('WP_CONTENT_URL') )
    define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') )
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
$pluginpath = WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__));
$pluginurl = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__));

/*
 Create Text Domain For Translations
*/
add_action('init', 'ffox_comment_warrior_textdomain');
function ffox_comment_warrior_textdomain()
{
	load_plugin_textdomain('wp-comment-warrior', false, 'wp-comment-warrior');
}

/*
 Function: comment warrior Option Menu
*/
add_action('admin_menu', 'commentwarrior_menu');
function commentwarrior_menu()
{
		add_options_page(__('Comment Warrior', 'wp-comment-warrior'), __('Comment Warrior', 'wp-comment-warrior'), 'manage_options', 'wp-comment-warrior/comment-warrior-options.php') ;
}

/*
 Find comment warrior and return them as an array.
*/
function get_comment_warrior()
{
//	if (!is_single() and !is_page())
//		return false;
	global $post;
	// 获得评论日期
	$curtime = time();
	$postdate = empty($post) ? date('Y-m-d 12:00:00', $curtime) : $post->post_date;
	$key = 'current';
	$warrior_options = get_option('warrior_options');
	$periodtype = intval($warrior_options['period_type']);
	switch($periodtype) {
		case 0:	//Calendar Month
			if (date('Y-m', $curtime) != get_the_time('Y-m'))
				$key = get_the_time('Y-m');
			break;
		case 1:	// Calendar Year
			if (date('Y', $curtime) != get_the_time('Y'))
				$key = get_the_time('Y');
			break;
		case 2: // Custom days
			$key = 'current';
			break;
	}
	if (!is_single() and !is_day() and !is_month() and !is_year())
		$key = 'current';
		
	$key .= '-data';

	$result = $warrior_options[$key];
	if (empty($result)) {
			$result = calc_comment_warrior($postdate, $periodtype);
			$warrior_options[$key] = $result;
			update_option('warrior_options', $warrior_options);
	}
	return $result;
}

function get_warrior_period() {
	// 获得评论日期
	$warrior_options = get_option('warrior_options');
	$periodtype = intval($warrior_options['period_type']);
	$result = '';
	if (is_single() or is_day() or is_month() or is_year()) {
		global $post;
		switch($periodtype) {
			case 0:	//Calendar Month
				$result = get_the_time(__('M. Y', 'wp-comment-warrior'));
				break;
			case 1:	// Calendar Year
					$result = get_the_time(__('the year Y', 'wp-comment-warrior'));
				break;
			case 2: // Custom days
					if ( $warrior_options['period_length'] <= 0 )
						$result = __('all days', 'wp-comment-warrior');
					else
						$result = sprintf(__('recent %d days', 'wp-comment-warrior'), $warrior_options['period_length']);
				break;
		}
	} else {
		$curtime = time();
		switch($periodtype) {
			case 0:	//Calendar Month
				$result = date(__('M. Y', 'wp-comment-warrior'), $curtime);
				break;
			case 1:	// Calendar Year
					$result = date(__('the year Y', 'wp-comment-warrior'), $curtime);
				break;
			case 2: // Custom days
					if ( $warrior_options['period_length'] <= 0 )
						$result = __('all days', 'wp-comment-warrior');
					else
					$result = sprintf(__('recent %d days', 'wp-comment-warrior'), $warrior_options['period_length']);
				break;
		}
	}
	
	return $result;
}

/*
 When a comment is posted, update datafile
*/
add_action('comment_post', update_comment_warrior);
function update_comment_warrior()
{
		$warrior_options = get_option('warrior_options');
		$periodtype = intval($warrior_options['period_type']);
		$postdate = date('Y-m-d', time());
		$result = calc_comment_warrior($postdate, $periodtype);
		$warrior_options['current-data'] = $result;
		update_option('warrior_options', $warrior_options);
}

/*
 
*/
class comment_warrior
{
	var $name;
	var $email;
	var $url;
	var $counts;
	var $date;
	
	function comment_warrior($n, $e, $u, $c, $d)
	{
		$this->name = $n;
		$this->email = $e;
		$this->url = $u;
		$this->counts = $c;
		$this->date = $d;
	}
}

/*
 Calc comment warrior from database
*/
function calc_comment_warrior($date, $periodtype)
{
	// 从SQL数据库中取出某个月份评论最多的客人
	global $wpdb;
	$warrior_options = get_option('warrior_options');
	$identity="comment_author_email";
	$interval = ( !isset($warrior_options['period_length']) ) ? 30 : $warrior_options['period_length'];
	switch($periodtype) {
		case 0:
			$timefilter = " MONTH(comment_date)=MONTH('".$date."') and YEAR(comment_date)=YEAR('".$date."')";
			break;
		case 1:
			$timefilter = " YEAR(comment_date)=YEAR('".$date."')";
			break;
		case 2:
			if ($interval == 0)
				$timefilter = "1=1";
			else
				$timefilter = " comment_date>SubDate(now(), Interval " . $interval. " day)";
			break;
	}
	$passwordpost = " AND post_password=''";
	$userexclude = " AND user_id='0' AND comment_author_email<>'" . get_option('admin_email')."'";
	// Since 0.3: Add Email blacklist
	if (!empty($warrior_options['email_blacklist'])) {
		$blacklists = explode(chr(13) . chr(10), $warrior_options['email_blacklist']);
		foreach($blacklists as $b) {
			if (empty($b)) continue;
			if (strpos($b, '*') !== false) {
				$userexclude .= " AND comment_author_email not like '" . str_replace('*', '%', $b) . "'";
			} else 
				$userexclude .= " AND comment_author_email<>'" . $b . "'";
		}
	}
	// Since 0.3: Add Url blacklist
	if (!empty($warrior_options['url_blacklist'])) {
		$blacklists = explode(chr(13) . chr(10), $warrior_options['url_blacklist']);
		foreach($blacklists as $b) {
			if (empty($b)) continue;
			if (strpos($b, '*') !== false) {
				$userexclude .= " AND comment_author_url not like '" . str_replace('*', '%', $b) . "'";
			} else 
				$userexclude .= " AND comment_author_url<>'" . $b . "'";
		}
	}
	$approved = " AND comment_approved='1'";
	$shownumber = ( !isset($warrior_options['max_num']) or $warrior_options['max_num'] == 0 ) ? 10 : $warrior_options['max_num'];
	$query = "SELECT COUNT(" . $identity . ") AS cmtcount, comment_author, comment_date, comment_author_email, comment_author_url 
				      FROM (SELECT * FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts 
					ON ($wpdb->posts.ID=$wpdb->comments.comment_post_ID) 
					WHERE ". $timefilter . $userexclude . $passwordpost . $approved . " ORDER BY comment_date DESC) AS tempcmt 
				      GROUP BY " . $identity . " ORDER BY cmtcount DESC LIMIT " . $shownumber;

	$sql = $wpdb->get_results($query);
	for($i=0; $i<sizeof($sql); $i++) {
		if ( isset($warrior_options['widget_min_counts']) and ($sql[$i]->cmtcount < $warrior_options['widget_min_counts']) )
			break;
		$result[$i] = new comment_warrior($sql[$i]->comment_author, 
			$sql[$i]->comment_author_email, $sql[$i]->comment_author_url, $sql[$i]->cmtcount, $sql[$i]->comment_date);
	}
	return $result;
}

/*
 * Add css style
 * Since 0.3: 
 * If there isn't wp-comment-warrior-css.css in theme folder, use css text from options instead of css file.
*/
add_action('wp_head', 'comment_warrior_stylesheets');
function comment_warrior_stylesheets() {
	if(@file_exists(TEMPLATEPATH.'/wp-comment-warrior.css')) {
		wp_enqueue_style('wp-comment-warrior', get_stylesheet_directory_uri().'/wp-comment-warrior.css', false, '0.30', 'all');
	} else {
		$warrior_options = get_option('warrior_options');
		if (!isset($warrior_options['widget_css'])) {
			$warrior_options['widget_css'] = '.commentwarrior li *{vertical-align:middle;}' . chr(13) . chr(10) .
			'.commentwarrior li{border:none; float:left; width:50%;}' . chr(13) . chr(10) .
			'.commentwarrior li img{margin-right:5px;}' . chr(13) . chr(10) .
			'.commentwarrior img, .commentwarrior img.avatar{padding:2px 5px 2px 5px; border:1px solid #DDD;}';
		}
?>
<style type="text/css">
<?php echo $warrior_options['widget_css']; ?>
</style>
<?php
	}
}
/*add_action('wp_print_styles', 'comment_warrior_stylesheets');
function comment_warrior_stylesheets() {
	if(@file_exists(TEMPLATEPATH.'/wp-comment-warrior-css.css')) {
		wp_enqueue_style('wp-comment-warrior', get_stylesheet_directory_uri().'/comment-warrior.css', false, '0.30', 'all');
	} else {
		wp_enqueue_style('wp-comment-warrior', plugins_url('wp-comment-warrior/comment-warrior.css'), false, '0.30', 'all');
	}	
}
*/

/*
 Generate html for comment warrior.
*/
function show_comment_warrior()
{
	$warriors = get_comment_warrior();
	if (empty($warriors)) {
		echo __('No comment warriors now.', 'wp-comment-warrior');
		return;
	}
	$warrior_options = get_option('warrior_options');
	$img_size = empty($warrior_options['warrior_img_size']) ? 32 : $warrior_options['warrior_img_size'];
	if (intval($warrior_options['show_commentator_type']) == 1) {
		echo '<div class="commentwarrior">';
		foreach($warriors as $c) {
			$alt = $c->name;
			$countstyle = '';
			if ($warrior_options['show_comment_counts'] == 1) {
				$countstyle = str_replace('%COMMENT_COUNT%', $c->counts, $warrior_options['comment_counts_template']);
				$countstyle = str_replace('%PERIOD%', get_warrior_period(), $countstyle);
			}
			$alt .= $countstyle;
			if (empty($c->url))
				echo get_avatar($c->email, $img_size, '', $alt);
			else
				echo '<a href="' . $c->url . '" title="' . $alt . '">' . get_avatar($c->email, $img_size, '', $alt) . '</a>';
		}
		echo '</div>';
	}
	else {
		echo '<ul class="commentwarrior">';
		foreach($warriors as $c) {
			$alt = $c->name;
			$countstyle = '';
			if ($warrior_options['show_comment_counts'] == 1) {
				$countstyle = str_replace('%COMMENT_COUNT%', $c->counts, $warrior_options['comment_counts_template']);
				$countstyle = str_replace('%PERIOD%', get_warrior_period(), $countstyle);
			}
			$alt .= $countstyle;
			if (!empty($c->url))
				echo '<li><a href="' . $c->url . '" title="' . $alt. '">';
			else
				echo '<li title="' . $alt . '">';
			if (intval($warrior_options['show_commentator_type']) == 0)
				echo $c->name;
			else
				echo get_avatar($c->email, $img_size, '', $alt) . $c->name;
			if (!empty($c->url))
				echo '</a></li>';
			else
				echo '</li>';
		}
		echo '</ul>';
	}
	echo '<div style="clear:both"></div>';
}

/* 
 * Since V0.3: 
 * use get_avatar API instead.

function get_warrior_avatar($email, $size){
	$avatar_default = get_option('avatar_default');
	if ( empty($avatar_default) ){
		$default = 'wavatar';
	}else{
		$default = $avatar_default;
	}
	if ( 'wavatar' == $default ){
		$default = "http://www.gravatar.com/wavatar/ad516503a11cd5ca435acc9bb6523536?s={$size}"; 
	}elseif ( 'blank' == $default ){
		$default = includes_url('images/blank.gif');
	}elseif ( !empty($email) && 'avatar_default' == $default ){
		$default = '';
	}elseif ( 'avatar_default' == $default ){
		$default = "http://www.gravatar.com/avatar/s={$size}";
	}elseif ( empty($email) ){
		$default = "http://www.gravatar.com/avatar/?d=$default&amp;s={$size}";
	}	
	if ( !empty($email) ) {
		$out = 'http://www.gravatar.com/avatar/';
		$out .= md5( strtolower( $email ) );
		$out .= '?s='.$size;
		$out .= '&amp;d=' . urlencode( $default );

		$rating = get_option('avatar_rating');
		if ( !empty( $rating ) )
			$out .= "&amp;r={$rating}";
		$avatar = $out;
	} else {
		$avatar = $default;
	}
	return $avatar;
}
*/

/*
 Generate show cup html.
 if you want to show cup in the comment section, you must:
 1. Enable the option in admin panel;
 2. insert <?php get_cup(***) ?> into a php file, such as comments.php
 You can check how it looks in http://www.mathelite.cn
*/
function get_cup($email){
	global $pluginurl;
	$warrior_options = get_option('warrior_options');
	if (!isset($warrior_options['show_trophy']) or $warrior_options['show_trophy'] == 0)
		return '';
	
	$imageurl = (!isset($warrior_options['cup_image_url']) or $warrior_options['cup_image_url'] == 0) ? 
			$pluginurl . '/cup.jpg' : $warrior_options['cup_image_url'];
	$imagewidth = (!isset($warrior_options['cup_image_width']) or $warrior_options['cup_image_width'] == 0) ? 
			0 : $warrior_options['cup_image_width'];
	$imageheight = (!isset($warrior_options['cup_image_height']) or $warrior_options['cup_image_height'] == 0) ? 
			0 : $warrior_options['cup_image_height'];
	$result = get_comment_warrior();
	if(empty($result)){
		return FALSE;
	}
	foreach($result as $c) {
		if ($c->email == $email) {
			$counts = $c->counts;
			$style= ' style'."=".'"cursor:pointer;"';
			if (!is_single() and !is_day() and !is_month() and !is_year()) {
				$year = date(__('Y' ,'wp-comment-warrior'), time());
				$month = date(__('M Y' ,'wp-comment-warrior'), time());
			} else {
				$year = date(__('Y' ,'wp-comment-warrior'), strtotime($c->date));
				$month = date(__('M Y' ,'wp-comment-warrior'), strtotime($c->date));
			}
			$cup = '';
			switch(intval($warrior_options['period_type'])) {
				case 0:
					$alt = sprintf(__('Comment Warrior! In the month %s, this person received the honor for %d comments.','wp-comment-warrior'), $month, $counts);
					break;
				case 1:
					$alt = sprintf(__('Comment Warrior! In the year %s, this person received the honor for %d comments.','wp-comment-warrior'), $year, $counts);
					break;
				case 2:
					if (!isset($warrior_options['period_length']) or $warrior_options['period_length'] == 0) {
						$alt = sprintf(__('Comment Warrior! This person received the honor for %d comments up to now.','wp-comment-warrior'), $counts);
					}
					else {
						$alt = sprintf(__('Comment Warrior! In the last %s days, this person received the honor for %d comments.','wp-comment-warrior'), $warrior_options['period_length'], $counts);
					}
					break;
			}
			$title = $alt;
			$cup .= '<img ';
			if ($imagewidth != 0)
				$cup .= 'width="' . $imagewidth . 'px" ';
			if ($imageheight != 0)
				$cup .= 'height="' . $imageheight . 'px" ';
			$cup .='src="' . $imageurl . '" alt="' . $alt .'" title="' . $title . '" />';
			echo $cup;
			break;
		}
	}
}

/*
 Widget for comment warrior
*/
class WP_Widget_commentwarrior extends WP_Widget
{
	// Constructor
	function WP_Widget_commentwarrior() {
		$widget_ops = array('description' => __('Show top N active commentators during a selected period.', 'wp-comment-warrior'));
		$this->WP_Widget('commentwarrior', __('Comment Warrior', 'wp-comment-warrior'), $widget_ops);
	}

	// Display Widget
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', esc_attr($instance['title']));
		echo $before_widget.$before_title.$title.$after_title;
		show_comment_warrior();
		echo $after_widget;
	}

	// When Widget Control Form Is Posted
	function update($new_instance, $old_instance) {
		if (!isset($new_instance['submit'])) {
			return false;
		}
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	// DIsplay Widget Control Form
	function form($instance) {
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('title' => __('Comment Warrior', 'wp-comment-warrior')));
		$title = esc_attr($instance['title']);
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-comment-warrior'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>
		</p>
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php
	}
}

/*
 Init WP-commentwarrior Widget
*/
add_action('widgets_init', 'widget_commentwarrior_init');
function widget_commentwarrior_init() {
	register_widget('WP_Widget_commentwarrior');
}
?>
