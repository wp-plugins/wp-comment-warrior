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
	$warrior_options['widget_min_counts'] = intval($_POST['widget_min_counts']);
	if ($warrior_options['widget_min_counts']<0)
		$warrior_options['widget_min_counts'] = 0;
	$warrior_options['period_type'] = intval($_POST['period_type']);	// 自然月、自然周、多少天
	$warrior_options['period_length'] = intval($_POST['period_length']);
	if ($warrior_options['period_length']<0)
		$warrior_options['period_length'] = 0;
	$warrior_options['show_trophy'] = intval($_POST['show_trophy']);
	$warrior_options['archive_type'] = intval($_POST['archive_type']);
	$warrior_options['widget_css'] = $_POST['widget_css'];

	// since V0.3
	$warrior_options['email_blacklist'] = $_POST['email_blacklist'];
	$warrior_options['url_blacklist'] = $_POST['url_blacklist'];

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
} else if (!empty($_POST['Reset'])) {
	$text = '<font color="red">'. __('Comment Warrior Options', 'wp-comment-warrior') .  ' ' . __('Resetted', 'wp-comment-warrior') . '</font><br />';
}

$warrior_options = get_option('warrior_options');
$b = $warrior_options['email_blacklist'];
echo (strpos($b, '*'));
echo str_replace('*', '%', $warrior_options['email_blacklist']);
// Init Options
$default = array('max_num'=>'10', 'widget_min_counts'=>'0',
	'period_type'=>'0', 'period_length'=>'30', 
	'show_commentator_type'=>'2', 'warrior_img_size'=>'32',
	'comment_counts_template'=>__('(%COMMENT_COUNT% comments in %PERIOD%)','wp-comment-warrior'), 
	'show_comment_counts'=>'1', 
	'widget_css'=>'.commentwarrior li *{vertical-align:middle;}' . chr(13) . chr(10) .
		'.commentwarrior li{border:none; float:left; width:50%;}' . chr(13) . chr(10) .
		'.commentwarrior li img{margin-right:5px;}' . chr(13) . chr(10) .
		'.commentwarrior img, .commentwarrior img.avatar{padding:2px 5px 2px 5px; border:1px solid #DDD;}',
	'show_trophy'=>'1', 'cup_image_url'=>'', 'cup_image_width'=>'0', 'cup_image_height'=>'0',
	'email_blacklist'=>'', 'url_blacklist'=>'');
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
	if ($warrior_options['show_commentator_type'] == 0) {
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
	
	jQuery(".helpinfotoggle").click(function(){
		var aid = this.id;
		var did = aid.replace(/show/i, '');
		did = did.replace(/toggle/i, '');
		jQuery("#" + did).toggle();
	});
	 
/*	jQuery("#showcuphelptoggle").click(function() {
	 	jQuery("#cuphelp").toggle();
	});

	jQuery("#showemailbanhelptoggle").click(function() {
		jQuery("#emailbanhelp").toggle();
	});

	jQuery("#showurlbanhelptoggle").click(function() {
		jQuery("#urlbanhelp").toggle();
	});*/
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
		if (type == 0)
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
		jQuery("#menufilter").removeClass('activemenu');
		jQuery("#menucup").removeClass('activemenu');
		jQuery("#divmenuwidget").hide();
		jQuery("#divmenufilter").hide();
		jQuery("#divmenucup").hide();
	}
	
	/* ]]> */
</script>
<style type="text/css">
#optionmenu ul{list-style:none; margin:15px 8px;cursor:pointer}
#optionmenu li{font-size:14px; float:left; width:auto;padding:5px 12px; border:1px solid #CCC; background-color:#FFF;}
#optionmenu li:hover{background-color:#CCC}
#optionmenu .activemenu{color:green;background-color:#CCC}
.helpinfotoggle{cursor:pointer;}
</style>
<?php
//	$imageurl = (!isset($warrior_options['cup_image_url']) or $warrior_options['cup_image_url'] == '') ? 
//			$pluginurl . '/cup.jpg' : $warrior_options['cup_image_url'];
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
	<li id="menufilter" class="menulist"><?php _e('Filter Settings', 'wp-comment-warrior'); ?></li>
	<li id="menucup" class="menulist"><?php _e('Cup Settings','wp-comment-warrior'); ?></li>
	</ul></div>
	<div style="clear:both;"></div>
	<div id="divmenuwidget">
	<table class="form-table">
		 <tr>
			<td valign="top" width="20%"><?php _e('Comment Warrior Num:', 'wp-comment-warrior'); ?><a class="helpinfotoggle" id="showwarriornumhelptoggle" title="<?php _e('Toggle Help Info', 'wp-commment-warrior') ?>">(?)</a>
			<div id="warriornumhelp" style="display:none"><?php _e('<em>How many warriors will be listed?</em>','wp-comment-warrior'); ?></div>
			</td>
			<td valign="top">
				<input type="text" id="max_num" name="max_num" size="30" value="<?php _e($warrior_options['max_num']); ?>" />
			</td>
		</tr>
		 <tr>
			<td valign="top" width="20%"><?php _e('Warrior Show Style:', 'wp-comment-warrior'); ?><a class="helpinfotoggle" id="showwarriorstylehelptoggle" title="<?php _e('Toggle Help Info', 'wp-commment-warrior') ?>">(?)</a>
			<div id="warriorstylehelp" style="display:none"><?php _e('<em>Three methods to show your warriors.</em>','wp-comment-warrior'); ?></div>
			</td>
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
			<td valign="top" width="20%"><?php _e('Show Comment Counts:', 'wp-comment-warrior'); ?><a class="helpinfotoggle" id="showcommentcounthelptoggle" title="<?php _e('Toggle Help Info', 'wp-commment-warrior') ?>">(?)</a>
			<div id="commentcounthelp" style="display:none"><?php _e('<em>Whether to show warrior\'s comments counts when mouse over the list.</em>','wp-comment-warrior'); ?></div>
			</td>
			<td valign="top">
				<select name="show_comment_counts" size="1" onchange="showcommentcountschanged(this.value)">
					<option value="0"<?php selected('0', $warrior_options['show_comment_counts']); ?>><?php _e('No', 'wp-comment-warrior'); ?></option>
					<option value="1"<?php selected('1', $warrior_options['show_comment_counts']); ?>><?php _e('Yes', 'wp-comment-warrior'); ?></option>
				</select>
			</td>
		</tr>
		<tr id="showcommentcounts" style="display:none;">
			<td valign="top" width="20%">
				<?php _e("Comment Counts Template:", 'wp-comment-warrior'); ?><a class="helpinfotoggle" id="showcmtcnttemphelptoggle" title="<?php _e('Toggle Help Info', 'wp-commment-warrior') ?>">(?)</a>
			<div id="cmtcnttemphelp" style="display:none"><?php _e('<em>You can use two variables:</em><br /><span style="color:#ff0000">%COMMENT_COUNT%</span> : comment counts<br /><span style="color:#ff0000">%PERIOD%</span> : stats period<br /><em>For example:</em><br />%COMMENT_COUNT% in %PERIOD%','wp-comment-warrior'); ?></div>
			</td>
			<td>
				<input type="text" id="comment_counts_template" size="50" name="comment_counts_template" value="<?php _e($warrior_options['comment_counts_template']); ?>" />
			</td>
		 <tr>
			<td valign="top" width="20%"><?php _e('Stat Period Type:', 'wp-comment-warrior'); ?><a class="helpinfotoggle" id="showstatperiodhelptoggle" title="<?php _e('Toggle Help Info', 'wp-commment-warrior') ?>">(?)</a>
			<div id="statperiodhelp" style="display:none"><?php _e('<em>You can choose CALENDAR MONTH, CALENDAR YEAR or CUSTOM DAYS.<br />Custom 0 days equals to all days.</em>','wp-comment-warrior'); ?></div>
			</td>
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
		 <tr>
			<td valign="top" width="20%"><?php _e('CSS Style:', 'wp-comment-warrior'); ?><a class="helpinfotoggle" id="showcssstylehelptoggle" title="<?php _e('Toggle Help Info', 'wp-commment-warrior') ?>">(?)</a>
			<div id="cssstylehelp" style="display:none"><?php _e('<em>Since V0.3, css style is saved in options table.<br />Note: if there is a "wp-comment-warrior.css" file in current theme folder, the file will be used.</em>','wp-comment-warrior'); ?></div>
			</td>
			<td valign="top">
				<textarea id="widget_css" name="widget_css" cols="50" rows="10"><?php _e($warrior_options['widget_css']); ?></textarea>
			</td>
		</tr>
	</table>
	</div>
	<div id="divmenufilter">
	<table class="form-table">
		<tr>
			<td valign="top" width="20%">
				<?php _e('Minimum Comment Counts:', 'wp-comment-warrior'); ?><a class="helpinfotoggle" id="showmincommentcounthelptoggle" title="<?php _e('Toggle Help Info', 'wp-commment-warrior') ?>">(?)</a>
				<div id="mincommentcounthelp" style="display:none"><?php _e('<em>A warrior must comment how many times at least.</em>','wp-comment-warrior'); ?></div>
			</td>
			<td valign="top">
				<input type="text" id="widget_min_counts" name="widget_min_counts" size="30" value="<?php _e($warrior_options['widget_min_counts']); ?>" />
			</td>
		</tr>
		<tr>
			<td valign="top" width="20%">
				<?php _e('EMail Banned List:', 'wp-comment-warrior'); ?><a class="helpinfotoggle" id="showemailbanhelptoggle" title="<?php _e('Toggle Help Info', 'wp-commment-warrior') ?>">(?)</a>
				<div id="emailbanhelp" style="display:none">
				<?php _e('<em><strong>Per email per line.</strong><br />You can input <span style="color:#FF0000">FULL TEXT</span> (d@d.com) or <span style="color:#FF0000">USE *</span>, for example:<br />*@d.com will ban d.com.<br />d@* will ban d\'s emails.<br />*com will ban com domains.<br />and so on.</em>', 'wp-comment-warrior'); ?></div>
			</td>
			<td valign="top">
				<textarea id="email_blacklist" name="email_blacklist" cols="30" rows="10"><?php _e($warrior_options['email_blacklist']); ?></textarea>
			</td>
		</tr>
		<tr>
			<td valign="top" width="20%">
				<?php _e('URL Banned List:', 'wp-comment-warrior'); ?><a class="helpinfotoggle" id="showurlbanhelptoggle" title="<?php _e('Toggle Help Info', 'wp-commment-warrior') ?>">(?)</a>
				<div id="urlbanhelp" style="display:none">
				<?php _e('<em><strong>Per url per line.</strong><br />You can input <span style="color:#FF0000">FULL TEXT</span> (http://www.d.com) or <span style="color:#FF0000">USE *</span>, for example:<br />*.d.com will ban all sub sites of d.com.<br />*d* will ban all urls include "d".<br />*com will ban com domains.<br />and so on.</em>', 'wp-comment-warrior'); ?></div>
			</td>
			<td valign="top">
				<textarea id="url_blacklist" name="url_blacklist" cols="30" rows="10"><?php _e($warrior_options['url_blacklist']); ?></textarea>
			</td>
		</tr>
	</table>
<!--	<?php _e('<span style="color:#ff0000">Note: "d.com" is ONLY an example! That does NOT mean it is some "bad" site.</span>', 'wp-comment-warrior');?> -->
	</div>
	<div id="divmenucup">
	<table class="form-table">
		<tr>
			<td valign="top" width="20%">
			<?php _e('Show Cup:', 'wp-comment-warrior'); ?><a class="helpinfotoggle" id="showcuphelptoggle" title="<?php _e('Toogle Help Info','wp-comment-warrior'); ?>">(?)</a>
			<div id="cuphelp" style="display:none"><?php _e('<em>To show cups in comment list, you must insert function "get_cup($email)" into right place in comments.php. <br />For additional information, please refer to <a target="_blank" href="http://www.mathelite.cn/archives/wordpress-plugin-comment-warrior-en.html">Readme</a>.</em>', 'wp-comment-warrior');?></div>
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
<!--		<input type="submit" name="Reset" class="button" value="<?php _e('Reset', 'wp-comment-warrior'); ?>" /> -->
		<input type="submit" name="Submit" class="button" value="<?php _e('Save Changes', 'wp-comment-warrior'); ?>" />
	</p>
</div>
</form>
<table class="form-table" style="border:1px solid #CCC; width:auto;margin-left:10px">
	<tr><td width="50%"><?php _e('Author:', 'wp-comment-warrior'); ?></td><td><a href="http://mathelite.cn">FlareFox</a></td></tr>
	<tr><td width="50%"><?php _e('Version:', 'wp-comment-warrior'); ?></td><td><?php echo $ffox_lvmct_version; ?></td></tr>
	<tr><td width="50%"><?php _e('Date:', 'wp-comment-warrior'); ?></td><td><?php echo $ffox_lvmct_date; ?></td></tr>
</table>
