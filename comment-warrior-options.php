<?php
/*
+----------------------------------------------------------------+
|                                                                |
|	WordPress 2.8 Plugin: WP-Comment-Warrior                 |
|	Copyright (c) 2009 FlareFox                                       |
|                                                                |
|	File Written By:                                               |
|	- FlareFox
|	- http://mathelite.cn                                          |
|                                                                |
|	File Information:                                              |
|	- comment warrior Options Page                                |
|	- wp-content/plugins/wp-comment-warrior
		/wp-comment-warrior-options.php                             |
|                                                                |
+----------------------------------------------------------------+
*/


### Variables Variables Variables
$base_name = plugin_basename('wp-comment-warrior/comment-warrior-options.php');
$base_page = 'admin.php?page='.$base_name;
$id = intval($_GET['id']);
$mode = trim($_GET['mode']);


### Form Processing
// Update Options
if(!empty($_POST['Submit'])) {
	$warrior_options = array();
	$warrior_options['max_num'] = intval($_POST['max_num']);
	if ($warrior_options['max_num']<=0)
		$warrior_options['max_num'] = 10;
	$warrior_options['period_type'] = intval($_POST['period_type']);	// 自然月、自然周、多少天
	$warrior_options['period_length'] = intval($_POST['period_length']);
	if ($warrior_options['period_length']<=0)
		$warrior_options['period_length'] = 30;
	$warrior_options['show_trophy'] = intval($_POST['show_trophy']);
	$warrior_options['archive_type'] = intval($_POST['archive_type']);
	$warrior_options['cup_image_url'] = $_POST['cup_image_url'];
	$warrior_options['cup_image_width'] = intval($_POST['cup_image_width']);
	$warrior_options['cup_image_height'] = intval($_POST['cup_image_height']);
	$warrior_options['show_commentator_type'] = intval($_POST['show_commentator_type']);
	$warrior_options['warrior_img_size'] = intval($_POST['warrior_img_size']);
	if ($warrior_options['warrior_img_size']<=0)
		$warrior_options['warrior_img_size'] = 32;
	$warrior_options['comment_counts_template'] = ($_POST['comment_counts_template'] == '') ? '(%COMMENT_COUNT%)' : $_POST['comment_counts_template'];
	$warrior_options['show_comment_counts'] = intval($_POST['show_comment_counts']);
	
	$update_warrior_queries = array();
	$update_warrior_text = array();
	$update_warrior_queries[] = update_option('warrior_options', $warrior_options);
	$update_warrior_text[] = __('Comment Warrior Options', 'wp-comment-warrior');
	$i=0;
	$text = '';
	foreach($update_warrior_queries as $update_warrior_query) {
		if($update_warrior_query) {
			$text .= '<font color="green">'.$update_warrior_text[$i].' '.__('Updated', 'wp-comment-warrior').'</font><br />';
		}
		$i++;
	}
	if(empty($text)) {
		$text = '<font color="red">'.__('No Comment Warrior Option Updated', 'wp-comment-warrior').'</font>';
	} else {
		update_comment_warrior();
	}
}

$warrior_options = get_option('warrior_options');
// Init Options
$default = array('max_num'=>'10', 'period_type'=>'0', 'period_length'=>'30', 'show_trophy'=>'1', 
'show_commentator_type'=>'2', 'warrior_img_size'=>'32','comment_counts_template'=>'(%COMMENT_COUNT% comments in %PERIOD%)', 
'show_comment_counts'=>'1');
$bisdirty = FALSE;
foreach($default as $k=>$v) {
	if (!isset($warrior_options[$k])) {
		$warrior_options[$k] = $v;
		$bisdirty = TRUE;
	}
}
if ($bisdirty)
	update_option('warrior_options', $warrior_options);
?>
<script type="text/javascript">
/* <![CDATA[*/
jQuery(document).ready(function(){
<?php
	if ($warrior_options['show_trophy'] == 1) {
		echo 'jQuery("#cupoptions").show();';
		echo 'refreshcup();';
	}
	if ($warrior_options['period_type'] == 2) {
		echo 'jQuery("#customperiodtype").show();';
	}
	if ($warrior_options['show_comment_counts'] == 1) {
		echo 'jQuery("#showcommentcounts").show();';
	}
	if ($warrior_options['show_commentator_type'] != 1) {
		echo 'jQuery("#showwarriorimgsize").hide();';
	}
?>
	 clearallmenuanddiv();
	 jQuery("#menuwidget").addClass("activemenu");
	 jQuery('#divmenuwidget').show();
	 
/*	 jQuery(".menulist").mouseenter(function() {
	 	jQuery(this).css("background-color","#CCC");
	 	
	 });
	 jQuery(".menulist").mouseleave(function() {
	 	jQuery(this).css("background-color","#FFF");
	 });*/
	 jQuery(".menulist").click(function() {
	 	clearallmenuanddiv();
	 	jQuery("#div"+this.id).show();
	 	jQuery(this).addClass("activemenu");
	 });
	 
	 jQuery("#showcuphelptoggle").click(function() {
	 	jQuery("#showcuphelp").toggle();
	 });
});
	function periodtypechanged(type) {
		if (type == 2)
			jQuery("#customperiodtype").show();
		else
			jQuery("#customperiodtype").hide();
	}
	function showcupchanged(type) {
		if (type == 1) {
			jQuery("#cupoptions").show();
			refreshcup();
		}
		else
			jQuery("#cupoptions").hide();
	}
	function showcommentcountschanged(type) {
		if (type == 1)
			jQuery("#showcommentcounts").show();
		else
			jQuery("#showcommentcounts").hide();
	}
	function showwarriortypechanged(type) {
		if (type != 1)
			jQuery("#showwarriorimgsize").hide();
		else
			jQuery("#showwarriorimgsize").show();
	}
	function refreshcup() {
		var url =  jQuery("#cup_image_url").attr('value');
		if (url == '')
			url = "<?php echo $pluginurl . '/cup.jpg'; ?>";
		var width =  jQuery("#cup_image_width").attr('value');
		var height =  jQuery("#cup_image_height").attr('value');
		var html = '<img src="' + url + '"';
		if (width > 0)
			html += ' width="' + width + 'px"';
		if (height > 0)
			html += ' height="' + height + 'px"';
		html += '/' + '>';
		jQuery("#refreshcupimg").html(html);
	}
	
	function clearallmenuanddiv() {
		jQuery("#menuwidget").removeClass('activemenu');
		jQuery("#menucup").removeClass('activemenu');
		jQuery("#divmenuwidget").hide();
		jQuery("#divmenucup").hide();
	}
	
	/* ]]> */
</script>
<style type="text/css">
#optionmenu ul{list-style:none; margin:15px 8px;cursor:pointer}
#optionmenu li{font-size:14px; float:left; width:auto;padding:5px 12px; border:1px solid #CCC; background-color:#FFF;}
#optionmenu li:hover{background-color:#CCC}
#optionmenu .activemenu{color:green;background-color:#CCC}
</style>
<?php
	$imageurl = (!isset($warrior_options['cup_image_url']) or $warrior_options['cup_image_url'] == 0) ? 
			$pluginurl . '/cup.jpg' : $warrior_options['cup_image_url'];
	$imagewidth = (!isset($warrior_options['cup_image_width']) or $warrior_options['cup_image_width'] == 0) ? 
			0 : $warrior_options['cup_image_width'];
	$imageheight = (!isset($warrior_options['cup_image_height']) or $warrior_options['cup_image_height'] == 0) ? 
			0 : $warrior_options['cup_image_height'];
?>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } else echo '<div id="message" class="updated fade"><p>'.__('You can config comment warrior plugin on this page.','wp-comment-warrior').'</p></div>'; ?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo plugin_basename(__FILE__); ?>">
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e('Comment Warrior Options Page', 'wp-comment-warrior'); ?></h2>
	<div id="optionmenu"><ul>
	<li id="menuwidget" class="menulist" ><?php _e('Widget Settings','wp-comment-warrior'); ?></li>
	<li id="menucup" class="menulist"><?php _e('Cup Settings','wp-comment-warrior'); ?></li>
	</ul></div>
	<div style="clear:both;"></div>
	<div id="divmenuwidget">
	<table class="form-table">
		 <tr>
			<td valign="top" width="20%"><?php _e('Comment Warrior Num:', 'wp-comment-warrior'); ?></td>
			<td valign="top">
				<input type="text" id="max_num" name="max_num" size="30" value="<?php _e($warrior_options['max_num']); ?>" />
			</td>
		</tr>
		 <tr>
			<td valign="top" width="20%"><?php _e('Warrior Show Style:', 'wp-comment-warrior'); ?></td>
			<td valign="top">
				<select name="show_commentator_type" size="1" onchange="showwarriortypechanged(this.value)">
					<option value="2"<?php selected('2', $warrior_options['show_commentator_type']); ?>><?php _e('Name and Image', 'wp-comment-warrior'); ?></option>
					<option value="1"<?php selected('1', $warrior_options['show_commentator_type']); ?>><?php _e('Image', 'wp-comment-warrior'); ?></option>
					<option value="0"<?php selected('0', $warrior_options['show_commentator_type']); ?>><?php _e('Name', 'wp-comment-warrior'); ?></option>
				</select>&nbsp;&nbsp;&nbsp;&nbsp;
		<span id="showwarriorimgsize">
			<?php _e('Image Size: ', 'wp-comment-warrior'); ?><input type="text" id="warrior_img_size" name="warrior_img_size" size="3" value="<?php _e($warrior_options['warrior_img_size']); ?>" /><?php _e('px','wp-comment-warrior') ?></span>
			</td>
		</tr>
		 <tr>
			<td valign="top" width="20%"><?php _e('Show Comment Counts:', 'wp-comment-warrior'); ?></td>
			<td valign="top">
				<select name="show_comment_counts" size="1" onchange="showcommentcountschanged(this.value)">
					<option value="0"<?php selected('0', $warrior_options['show_comment_counts']); ?>><?php _e('No', 'wp-comment-warrior'); ?></option>
					<option value="1"<?php selected('1', $warrior_options['show_comment_counts']); ?>><?php _e('Yes', 'wp-comment-warrior'); ?></option>
				</select>
			</td>
		</tr>
		<tr id="showcommentcounts" style="display:none;">
			<td valign="top" width="20%">
				<?php _e("Comment Counts Template:", 'wp-comment-warrior'); ?>
			</td>
			<td>
				<input type="text" id="comment_counts_template" size="30" name="comment_counts_template" value="<?php _e($warrior_options['comment_counts_template']); ?>" />
				<br />
				<?php _e('<font color="#ff0000">%COMMENT_COUNT%</font> : comment counts', 'wp-comment-warrior'); ?>
				<br />
				<?php _e('<font color="#ff0000">%PERIOD%</font> : stats period', 'wp-comment-warrior'); ?>
			</td>
		 <tr>
			<td valign="top" width="20%"><?php _e('Stat Period Type:', 'wp-comment-warrior'); ?></td>
			<td valign="top">
				<select name="period_type" size="1" onchange="periodtypechanged(this.value)">
					<option value="0"<?php selected('0', $warrior_options['period_type']); ?>><?php _e('Calendar Month', 'wp-comment-warrior'); ?></option>
					<option value="1"<?php selected('1', $warrior_options['period_type']); ?>><?php _e('Calendar Year', 'wp-comment-warrior'); ?></option>
					<option value="2"<?php selected('2', $warrior_options['period_type']); ?>><?php _e('Custom', 'wp-comment-warrior'); ?></option>
				</select>
				<span style="display:none" id="customperiodtype">
					<input type="text" id="period_length" name="period_length" size="5" value="<?php _e($warrior_options['period_length']); ?>" /> <?php _e('days','wp-comment-warrior'); ?>
				</span>
			</td>
		</tr>
	</table>
	</div>
	<div id="divmenucup">
	<table class="form-table">
		<tr>
			<td valign="top" width="20%">
			<a id="showcuphelptoggle" title="<?php _e('Toogle Help Info','wp-comment-warrior'); ?>"><?php _e('Show Cup:', 'wp-comment-warrior'); ?></a>
			</td>
			<td valign="top">
				<select name="show_trophy" size="1" onchange="showcupchanged(this.value)">
					<option value="0"<?php selected('0', $warrior_options['show_trophy']); ?>><?php _e('No', 'wp-comment-warrior'); ?></option>
					<option value="1"<?php selected('1', $warrior_options['show_trophy']); ?>><?php _e('Yes', 'wp-comment-warrior'); ?></option>
				</select>
			</td>
		</tr>
	</table>
	<div style="display:none" id="cupoptions">
	<div id="showcuphelp" style="margin:0 10px;font-size:11px;color:#F00;display:none">
		<?php _e('To show the cup in comment list, you must insert the function "get_cup($comment->comment_author_email)" into some proper place in comments.php. <br />For additional information, please refer to <a target="_blank" href="http://www.mathelite.cn/archives/wordpress-plugin-comment-warrior.html">Readme</a>.', 'wp-comment-warrior');?>
	</div>
	<table  class="form-table">
		<tr>
			<td valign="top" width="20%">
				<?php _e('Cup Image Url:', 'wp-comment-warrior'); ?>
			</td>
			<td valign="top">
			  <input type="text" onblur="refreshcup()" id="cup_image_url" name="cup_image_url" size="20" value="<?php _e($warrior_options['cup_image_url']); ?>" />
			  <?php _e('Leave blank to use default image.', 'wp-comment-warrior');?>
  		</td>
  	</tr>
		<tr>
			<td valign="top" width="20%">
				<?php _e('Cup Image Width(px):', 'wp-comment-warrior'); ?>
			</td>
			<td valign="top">
			  <input type="text" onblur="refreshcup()" id="cup_image_width" name="cup_image_width" size="20" value="<?php _e($warrior_options['cup_image_width']); ?>" />
			  <?php _e('To keep the original width, input 0 or leave blank.', 'wp-comment-warrior');?>
  		</td>
  	</tr>
		<tr>
			<td valign="top" width="20%">
				<?php _e('Cup Image Height(px):', 'wp-comment-warrior'); ?>
			</td>
			<td valign="top">
			  <input type="text" onblur="refreshcup()" id="cup_image_height" name="cup_image_height" size="20" value="<?php _e($warrior_options['cup_image_height']); ?>" />
			  <?php _e('To keep the original height, input 0 or leave blank.', 'wp-comment-warrior');?>
  		</td>
  	</tr>
  	<tr>
	  	<td><?php _e('Cup Image Preview:','wp-comment-warrior'); ?></td>
		<td><div id="refreshcupimg"></div></td>
  	</tr>
	</table>
	</div>
	</div>
	<p class="submit">
		<input type="submit" name="Submit" class="button" value="<?php _e('Save Changes', 'wp-comment-warrior'); ?>" />
	</p>
</div>
</form>
<table class="form-table" style="border:1px solid #CCC; width:auto;margin-left:10px">
	<tr><td width="50%"><?php _e('Author:', 'wp-comment-warrior'); ?></td><td><a href="http://mathelite.cn">FlareFox</a></td></tr>
	<tr><td width="50%"><?php _e('Version:', 'wp-comment-warrior'); ?></td><td><?php echo $ffox_lvmct_version; ?></td></tr>
	<tr><td width="50%"><?php _e('Date:', 'wp-comment-warrior'); ?></td><td><?php echo $ffox_lvmct_date; ?></td></tr>
</table>
