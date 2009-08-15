=== Wordpress Comment Warrior ===
Contributors: Flarefox
Donate link: 
Tags: comments, warrior,period,stats
Requires at least: 2.7
Tested up to: 2.8
Stable tag: trunk

Show relevant comment warriors in different period. It also can insert an honor cup for comment warrior in his or her comments.

== Description ==

= This plugin has two purposes =

Firstly, stat comment warriors periodically, and show relevant warriors for different period. For example, when open an article posted in Jul. 2009, widget will list warriors in Jul. 2009.

Secondly, if a comment's author is a warrior, an cup image can be shown in the warrior's comment.

Dunno what is a comment warrior? Pls refer to FAQ Section:-)

= Options =

Period can be `calendar month`, `calendar year` or `custom days`.

Display style of warrior list can be `only text`, `only image` or `image and text`.

You can also choose whether to show `comment counts`, whether to show `cup image`, `image size of warrior`, `image of cup`, `image size of cup` and so on.


= And more =

Find more information in `FAQ` section.

If you have any question or suggestion, please post here or mail to flarefox at 163 dot com. I will response as soon as possible. Thank you!

== Installation ==

1. Upload `wp-comment-warrior` folder to the `/wp-content/plugins/` directory

2. Activate the plugin through the 'Plugins' menu in WordPress

3. Add widget in admin panel or a piece of codes anywhere. Please refer to FAQ section.

== Screenshots ==

1. Widget(image and text) preview

2. Cup preview

3. Widget Settings

2. Cup Settings

== Frequently Asked Questions ==

= What is comment warrior? =

In 2005 and 2006 I played world of warcraft for several months. That's why I name *the most active commentators* as WARRIORS!

= How to use the plugin? =

After install and activate it, you can add `Comment Warrior` Widget in admin panel, or you can add below codes anywhere:

`<?php if function_exists('show_comment_warrior') show_comment_warrior(); ?>`

A `ul-li` list will be generated.

= The widget's style is urgely, or it doesn't display at all! =

This problem is probably caused by css style. You can modify comment-warrior.css in plugin folder.

= Option page doesn't work, why? =

This plugin's option page need jQuery. I have tested it in wordpress 2.7 and 2.8. I dunno if other versions support jQuery too.

= How to show cup image? =

Well, it's a little complicated. You must manually insert below codes into a right place in comments.php or elsewhere. For example, you can append them to *comment time* or *comment author picture*.

`<?php if function_exists('get_cup') get_cup($comment->comment_author_email); ?>`

An img tag will be generated.

= How to find the right place to add cup codes? =

Here is a sample walkthrough.

1. Open comments.php in your theme folder, then search `wp_list_comments`.

2. If find, check args of wp_list_comments function.

3. If the args contain some string like `callback=custom_list_comments`, the right place is just in function `custom_list_comments`.

4. If the args don't contain `callback`, the right place lies in function start_el in wp-includes/comment-template.php.

5. If not find `wp_list_comments`, the right place is somewhere in comment.php in the theme folder.

*I admit it may be not simple. If anyone have a better idea such as auto insert the cup image, please mail to `flarefox at 163 dot com`. I would appreciate it very much!*

== Changelog ==

= 0.1.66 =
* Rewrite option page with jQuery, thus all elements are ajaxed.
* Add some option items.
* 

= 0.0.89 =
* Works on wordpress 2.8 and 2.7. 