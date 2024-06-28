<?php

/* @author    2codeThemes
*  @package   WPQA/options
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function wpqa_admin_setting_options($default = "") {
	if (is_admin() || $default == "default") {
		$options = array();

		$wpqa_options_roles = wpqa_options_roles();

		$activate_currencies = wpqa_options("activate_currencies");
		$multi_currencies = wpqa_options("multi_currencies");
		$wp_editor_settings = array("media_buttons" => true,"textarea_rows" => 10);

		// Background Defaults
		$background_defaults = array(
			'color'      => '',
			'image'      => '',
			'repeat'     => 'repeat',
			'position'   => 'top center',
			'attachment' => 'scroll'
		);

		// Pull all the pages into an array
		$options_pages = array();
		$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
		$options_pages[''] = 'Select a page:';
		foreach ($options_pages_obj as $page) {
			$options_pages[$page->ID] = $page->post_title;
		}
		
		// Pull all the sidebars into an array
		$new_sidebars = wpqa_registered_sidebars();
		
		// Menus
		$menus = array();
		$all_menus = get_terms('nav_menu',array('hide_empty' => true));
		foreach ($all_menus as $menu) {
		    $menus[$menu->term_id] = $menu->name;
		}
		
		// Pull all the roles into an array
		global $wp_roles;
		$new_roles = array();
		foreach ($wp_roles->roles as $key => $value) {
			$new_roles[$key] = $value['name'];
		}
		
		// Share
		$share_array = array(
			"share_facebook" => array("sort" => "Facebook","value" => "share_facebook"),
			"share_twitter"  => array("sort" => "Twitter","value" => "share_twitter"),
			"share_linkedin" => array("sort" => "LinkedIn","value" => "share_linkedin"),
			"share_whatsapp" => array("sort" => "WhatsApp","value" => "share_whatsapp"),
		);

		// Currencies
		$currencies = array(
			'USD' => 'USD',
			'EUR' => 'EUR',
			'GBP' => 'GBP',
			'JPY' => 'JPY',
			'CAD' => 'CAD',
			'INR' => 'INR',
			'TRY' => 'TRY',
			'BRL' => 'BRL',
			'HUF' => 'HUF',
			'BDT' => 'BDT',
			'AUD' => 'AUD',
			'IDR' => 'IDR'
		);

		$currencies = apply_filters("wpqa_currencies",$currencies);

		// Knowledgebase
		$activate_knowledgebase = apply_filters("wpqa_activate_knowledgebase",false);

		// Single or home pages
		$single_home_pages = array(
			'home_page'     => esc_html__('Home page','wpqa'),
			'all_pages'     => esc_html__('All site pages','wpqa'),
			'all_posts'     => esc_html__('All single post pages','wpqa'),
			'all_questions' => esc_html__('All single question pages','wpqa'),
			'custom_pages'  => esc_html__('Custom pages','wpqa'),
		);

		if ($activate_knowledgebase == true) {
			$single_home_pages = wpqa_array_insert_after($single_home_pages,"all_questions",array('all_knowledgebases' => esc_html__('All single article pages','wpqa')));
		}
		
		// If using image radio buttons, define a directory path
		$imagepath =  get_template_directory_uri().'/admin/images/';
		$imagepath_theme =  get_template_directory_uri().'/images/';
		$imagepath_wpqa =  plugin_dir_url(dirname(__FILE__)).'images/';

		$options[] = array(
			'name' => esc_html__('General settings','wpqa'),
			'id'   => 'general',
			'icon' => 'admin-site',
			'type' => 'heading'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_before_general_setting',$options);

		$options[] = array(
			'name' => esc_html__('Activate the lightbox at the site','wpqa'),
			'desc' => esc_html__('Select ON if you want to active the lightbox at the site.','wpqa'),
			'id'   => 'active_lightbox',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate scroll to up button at the site','wpqa'),
			'desc' => esc_html__('Select ON if you want to activate scroll to top button at the site.','wpqa'),
			'id'   => 'go_up_button',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the ask question button at the site','wpqa'),
			'desc' => esc_html__('Select ON if you want to activate the ask question button at the site next to scroll to top button.','wpqa'),
			'id'   => 'ask_button',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the font awesome at the site','wpqa'),
			'desc' => esc_html__('Select ON if you want to active the font awesome at the site.','wpqa'),
			'id'   => 'active_awesome',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable loader','wpqa'),
			'desc' => esc_html__('Select ON to enable loader.','wpqa'),
			'id'   => 'loader',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Save IP address','wpqa'),
			'desc' => esc_html__('Select ON to save the IP address while adding questions, posts, groups, group posts, messages, payments, reports, requests, and user registrations.','wpqa'),
			'id'   => 'save_ip_address',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => sprintf(esc_html__('Type the date format %1$s see this link %2$s.','wpqa'),'<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">','</a>'),
			'desc' => esc_html__('Type here your date format.','wpqa'),
			'id'   => 'date_format',
			'std'  => 'F j, Y',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => sprintf(esc_html__('Type the time format %1$s see this link %2$s.','wpqa'),'<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">','</a>'),
			'desc' => esc_html__('Type here your time format.','wpqa'),
			'id'   => 'time_format',
			'std'  => 'g:i a',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate format date ago','wpqa'),
			'desc' => esc_html__('Select ON if you want to activate the format date ago.','wpqa'),
			'id'   => 'format_date_ago',
			'type' => 'checkbox'
		);

		$format_date_ago_types_std = array(
			"comments"       => 'comments',
			"answers"        => 'answers',
			"posts"          => 'posts',
			"questions"      => 'questions',
			"group_posts"    => 'group_posts',
			"group_comments" => 'group_comments',
		);

		if ($activate_knowledgebase == true) {
			$format_date_ago_types_std["knowledgebases"] = "knowledgebases";
		}

		$format_date_ago_types_options = array(
			"comments"       => esc_html__('Comments','wpqa'),
			"answers"        => esc_html__('Answers','wpqa'),
			"posts"          => esc_html__('Posts','wpqa'),
			"questions"      => esc_html__('Questions','wpqa'),
			"group_posts"    => esc_html__('Group Posts','wpqa'),
			"group_comments" => esc_html__("Group Comments","wpqa"),
		);

		if ($activate_knowledgebase == true) {
			$format_date_ago_types_options["knowledgebases"] = esc_html__('Articles','wpqa');
		}

		$options[] = array(
			'name'      => esc_html__("Choose the types you want to activate the format date ago.","wpqa"),
			'id'        => 'format_date_ago_types',
			'type'      => 'multicheck',
			'std'       => $format_date_ago_types_std,
			'options'   => $format_date_ago_types_options,
			'condition' => 'format_date_ago:not(0)',
		);
		
		$options[] = array(
			'name'    => esc_html__('Excerpt type','wpqa'),
			'desc'    => esc_html__('Choose form here the excerpt type.','wpqa'),
			'id'      => 'excerpt_type',
			'std'     => 'words',
			'type'    => "select",
			'options' => array(
				'words'      => esc_html__('Words','wpqa'),
				'characters' => esc_html__('Characters','wpqa')
			)
		);
		
		$options[] = array(
			'name' => esc_html__('Hide the top bar for WordPress','wpqa'),
			'desc' => esc_html__('Select ON if you want to hide the top bar for WordPress.','wpqa'),
			'id'   => 'top_bar_wordpress',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$roles_no_admin = $new_roles;
		unset($roles_no_admin["administrator"]);
		
		$options[] = array(
			'name'      => esc_html__("Choose the roles you don't want to show the WordPress admin top bar.","wpqa"),
			'id'        => 'top_bar_groups',
			'type'      => 'multicheck',
			'options'   => $roles_no_admin,
			'condition' => 'top_bar_wordpress:not(0)',
			'std'       => array('wpqa_under_review' => 'wpqa_under_review','ban_group' => 'ban_group','activation' => 'activation','subscriber' => 'subscriber','author' => 'author'),
		);
		
		$options[] = array(
			'name' => esc_html__('Do you like to redirect unlogged users from WordPress admin?','wpqa'),
			'desc' => esc_html__('Select ON if you want to redirect the unlogged users from the WordPress admin to the theme login page.','wpqa'),
			'id'   => 'redirect_wp_admin_unlogged',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Do you need to redirect user from WordPress admin?','wpqa'),
			'desc' => esc_html__('Select ON if you want to redirect the user from the WordPress admin.','wpqa'),
			'id'   => 'redirect_wp_admin',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__("Choose the roles you don't want to show the WordPress admin.","wpqa"),
			'id'        => 'redirect_groups',
			'type'      => 'multicheck',
			'options'   => $roles_no_admin,
			'condition' => 'redirect_wp_admin:not(0)',
			'std'       => array('wpqa_under_review' => 'wpqa_under_review','ban_group' => 'ban_group','activation' => 'activation','subscriber' => 'subscriber','author' => 'author'),
		);
		
		$options[] = array(
			'name' => esc_html__('Enable SEO options','wpqa'),
			'desc' => esc_html__('Select ON to enable SEO options.','wpqa'),
			'id'   => 'seo_active',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Share image','wpqa'),
			'desc'      => esc_html__('This is the share image','wpqa'),
			'id'        => 'fb_share_image',
			'condition' => 'seo_active:not(0)',
			'type'      => 'upload'
		);
		
		$options[] = array(
			'name' => esc_html__('Head code','wpqa'),
			'desc' => esc_html__('Paste your Google analytics code in the box','wpqa'),
			'id'   => 'head_code',
			'type' => 'textarea'
		);

		$options[] = array(
			'name' => esc_html__('Footer code','wpqa'),
			'desc' => esc_html__('Paste footer code in the box','wpqa'),
			'id'   => 'footer_code',
			'type' => 'textarea'
		);
		
		$options[] = array(
			'name' => esc_html__('SEO keywords','wpqa'),
			'desc' => esc_html__('Paste your keywords in the box','wpqa'),
			'id'   => 'the_keywords',
			'type' => 'textarea'
		);
		
		$options[] = array(
			'name' => esc_html__('WordPress login logo','wpqa'),
			'desc' => esc_html__('This is the logo that appears on the default WordPress login page','wpqa'),
			'id'   => 'login_logo',
			'std'  => $imagepath_theme.(has_discy()?"logo-footer.png":"logo-mini.png"),
			'type' => 'upload'
		);
		
		$options[] = array(
			'name' => esc_html__('WordPress login logo height','wpqa'),
			"id"   => "login_logo_height",
			"type" => "sliderui",
			'std'  => (has_discy()?'45':'30'),
			"step" => "1",
			"min"  => "0",
			"max"  => "300"
		);
		
		$options[] = array(
			'name' => esc_html__('WordPress login logo width','wpqa'),
			"id"   => "login_logo_width",
			"type" => "sliderui",
			'std'  => (has_discy()?'166':'30'),
			"step" => "1",
			"min"  => "0",
			"max"  => "300"
		);
		
		if (!function_exists('wp_site_icon') || !has_site_icon()) {
			$options[] = array(
				'name' => esc_html__('Custom favicon','wpqa'),
				'desc' => esc_html__("Upload the site's favicon here , You can create new favicon here favicon.","wpqa"),
				'id'   => 'favicon',
				'std'  => $imagepath_theme."favicon.png",
				'type' => 'upload'
			);
			
			$options[] = array(
				'name' => esc_html__('Custom favicon for iPhone','wpqa'),
				'desc' => esc_html__('Upload your custom iPhone favicon','wpqa'),
				'id'   => 'iphone_icon',
				'type' => 'upload'
			);
			
			$options[] = array(
				'name' => esc_html__('Custom iPhone retina favicon','wpqa'),
				'desc' => esc_html__('Upload your custom iPhone retina favicon','wpqa'),
				'id'   => 'iphone_icon_retina',
				'type' => 'upload'
			);
			
			$options[] = array(
				'name' => esc_html__('Custom favicon for iPad','wpqa'),
				'desc' => esc_html__('Upload your custom iPad favicon','wpqa'),
				'id'   => 'ipad_icon',
				'type' => 'upload'
			);
			
			$options[] = array(
				'name' => esc_html__('Custom iPad retina favicon','wpqa'),
				'desc' => esc_html__('Upload your custom iPad retina favicon','wpqa'),
				'id'   => 'ipad_icon_retina',
				'type' => 'upload'
			);
		}
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options = apply_filters("wpqa_options_after_general_setting",$options,$options_pages);
		
		$options[] = array(
			'name' => esc_html__('Under construction','wpqa'),
			'id'   => 'construction',
			'type' => 'heading',
			'icon' => 'admin-tools',
		);

		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate under construction','wpqa'),
			'desc' => esc_html__('Select ON to enable under construction.','wpqa'),
			'id'   => 'under_construction',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'under_construction:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Upload the background','wpqa'),
			'desc'    => esc_html__('Upload the background for the under construction page','wpqa'),
			'id'      => 'construction_background',
			'type'    => 'background',
			'options' => array('color' => '','image' => ''),
			'std'     => array(
				'color' => '#272930',
				'image' => $imagepath_theme."register.png"
			)
		);
		
		$options[] = array(
			"name" => esc_html__('Choose the background opacity','wpqa'),
			"desc" => esc_html__('Choose the background opacity from here','wpqa'),
			"id"   => "construction_opacity",
			"type" => "sliderui",
			'std'  => 30,
			"step" => "5",
			"min"  => "0",
			"max"  => "100"
		);
		
		$options[] = array(
			'name' => esc_html__('The headline','wpqa'),
			'desc' => esc_html__('Type the Headline from here','wpqa'),
			'id'   => 'construction_headline',
			'type' => 'text',
			'std'  => 'Coming soon'
		);
		
		$options[] = array(
			'name' => esc_html__('The paragraph','wpqa'),
			'desc' => esc_html__('Type the Paragraph from here','wpqa'),
			'id'   => 'construction_paragraph',
			'type' => 'textarea',
			'std'  => 'The site is under construction and something great is coming soon.'
		);
		
		$options[] = array(
			'name' => esc_html__('Construction redirect','wpqa'),
			'desc' => esc_html__('Type the link of the construction redirect','wpqa'),
			'id'   => 'construction_redirect',
			'type' => 'text',
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$header_settings = array(
			"header_s"     => esc_html__('Header setting','wpqa'),
			"call_action"  => esc_html__('Call to action','wpqa'),
			"breadcrumb_s" => esc_html__('Breadcrumbs','wpqa'),
		);
		
		$options[] = array(
			'name'    => esc_html__('Header settings','wpqa'),
			'id'      => 'header',
			'type'    => 'heading',
			'icon'    => 'menu',
			'std'     => 'header_s',
			'options' => apply_filters(wpqa_prefix_theme."_header_settings",$header_settings)
		);
		
		$options[] = array(
			'name' => esc_html__('Header setting','wpqa'),
			'id'   => 'header_s',
			'type' => 'heading-2'
		);

		$options = apply_filters('wpqa_header_setting',$options,$options_pages,$new_roles,$imagepath,$imagepath_theme,$new_sidebars,$menus);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Call to action','wpqa'),
			'id'   => 'call_action',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the call to action','wpqa'),
			'desc' => esc_html__('Select ON to enable the call to action.','wpqa'),
			'id'   => 'call_action',
			'std'  => (has_discy()?'on':0),
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'type'      => 'heading-2',
			'condition' => 'call_action:not(0)'
		);
		
		$options[] = array(
			'name'    => esc_html__('The call to action works at all the pages, custom pages, or home page only?','wpqa'),
			'id'      => 'action_home_pages',
			'options' => $single_home_pages,
			'std'     => 'home_page',
			'type'    => 'radio'
		);

		$options[] = array(
			'name'      => esc_html__('Page ids','wpqa'),
			'desc'      => esc_html__('Type from here the page ids','wpqa'),
			'id'        => 'action_pages',
			'type'      => 'text',
			'condition' => 'action_home_pages:is(custom_pages)'
		);
		
		$options[] = array(
			'name'    => esc_html__('Action skin','wpqa'),
			'desc'    => esc_html__('Choose the action skin.','wpqa'),
			'id'      => 'action_skin',
			'std'     => 'dark',
			'type'    => 'radio',
			'options' => array("light" => esc_html__("Light","wpqa"),"dark" => esc_html__("Dark","wpqa"),"colored" => esc_html__("Colored","wpqa"))
		);
		
		$options[] = array(
			'name'    => esc_html__('Action style','wpqa'),
			'desc'    => esc_html__('Choose action style from here.','wpqa'),
			'id'      => 'action_style',
			'options' => array(
				'style_1'  => 'Style 1',
				'style_2'  => 'Style 2',
			),
			'std'     => 'style_1',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name'    => esc_html__('Action image or video','wpqa'),
			'id'      => 'action_image_video',
			'options' => array(
				'image' => esc_html__('Image','wpqa'),
				'video' => esc_html__('Video','wpqa'),
			),
			'std'     => 'image',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'action_image_video:not(video)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Upload the background','wpqa'),
			'id'      => 'action_background',
			'type'    => 'background',
			'options' => array('color' => '','image' => ''),
			'std'     => array(
				'image' => $imagepath_theme."action.png"
			)
		);
		
		$options[] = array(
			"name" => esc_html__('Choose the background opacity','wpqa'),
			"desc" => esc_html__('Choose the background opacity from here','wpqa'),
			"id"   => "action_opacity",
			"type" => "sliderui",
			'std'  => 50,
			"step" => "5",
			"min"  => "0",
			"max"  => "100"
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'action_image_video:is(video)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Video type','wpqa'),
			'id'      => 'action_video_type',
			'type'    => 'select',
			'options' => array(
				'youtube'  => esc_html__("Youtube","wpqa"),
				'vimeo'    => esc_html__("Vimeo","wpqa"),
				'daily'    => esc_html__("Dailymotion","wpqa"),
				'facebook' => esc_html__("Facebook video","wpqa"),
				'tiktok'   => esc_html__("TikTok video","wpqa"),
				'html5'    => esc_html__("HTML 5","wpqa"),
				'embed'    => esc_html__("Custom embed","wpqa"),
			),
			'std'     => 'youtube',
			'desc'    => esc_html__('Choose from here the video type','wpqa'),
		);
		
		$options[] = array(
			'name'      => esc_html__('Custom embed','wpqa'),
			'desc'      => esc_html__('Put your Custom embed html','wpqa'),
			'id'        => "action_custom_embed",
			'type'      => 'textarea',
			'cols'      => "40",
			'rows'      => "8",
			'condition' => 'action_video_type:is(embed)'
		);
		
		$options[] = array(
			'name'      => esc_html__('Video ID','wpqa'),
			'id'        => 'action_video_id',
			'desc'      => esc_html__('Put the Video ID here: https://www.youtube.com/watch?v=JuyB7NO0EYY Ex: "JuyB7NO0EYY"','wpqa'),
			'type'      => 'text',
			'operator'  => 'or',
			'condition' => 'action_video_type:is(youtube),'.'action_video_type:is(vimeo),'.'action_video_type:is(daily),'.'action_video_type:is(facebook),'.'action_video_type:is(tiktok)'
		);
		
		$options[] = array(
			'name'      => esc_html__('Mp4 video','wpqa'),
			'id'        => 'action_video_mp4',
			'desc'      => esc_html__('Put mp4 video here','wpqa'),
			'type'      => 'text',
			'condition' => 'action_video_type:is(html5)'
		);
		
		$options[] = array(
			'name'      => esc_html__('M4v video','wpqa'),
			'id'        => 'action_video_m4v',
			'desc'      => esc_html__('Put m4v video here','wpqa'),
			'type'      => 'text',
			'condition' => 'action_video_type:is(html5)'
		);
		
		$options[] = array(
			'name'      => esc_html__('Webm video','wpqa'),
			'id'        => 'action_video_webm',
			'desc'      => esc_html__('Put webm video here','wpqa'),
			'type'      => 'text',
			'condition' => 'action_video_type:is(html5)'
		);
		
		$options[] = array(
			'name'      => esc_html__('Ogv video','wpqa'),
			'id'        => 'action_video_ogv',
			'desc'      => esc_html__('Put ogv video here','wpqa'),
			'type'      => 'text',
			'condition' => 'action_video_type:is(html5)'
		);
		
		$options[] = array(
			'name'      => esc_html__('Wmv video','wpqa'),
			'id'        => 'action_video_wmv',
			'desc'      => esc_html__('Put wmv video here','wpqa'),
			'type'      => 'text',
			'condition' => 'action_video_type:is(html5)'
		);
		
		$options[] = array(
			'name'      => esc_html__('Flv video','wpqa'),
			'id'        => 'action_video_flv',
			'desc'      => esc_html__('Put flv video here','wpqa'),
			'type'      => 'text',
			'condition' => 'action_video_type:is(html5)'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('The headline','wpqa'),
			'desc' => esc_html__('Type the Headline from here','wpqa'),
			'id'   => 'action_headline',
			'type' => 'text',
			'std'  => "Share & grow the world's knowledge!"
		);
		
		$options[] = array(
			'name'     => esc_html__('The paragraph','wpqa'),
			'desc'     => esc_html__('Type the Paragraph from here','wpqa'),
			'id'       => 'action_paragraph',
			'type'     => apply_filters(wpqa_prefix_theme.'_action_paragraph','textarea'),
			'std'      => 'We want to connect the people who have knowledge to the people who need it, to bring together people with different perspectives so they can understand each other better, and to empower everyone to share their knowledge.'
		);
		
		$options[] = array(
			'name'    => esc_html__('Action button','wpqa'),
			'desc'    => esc_html__('Choose Action button style from here.','wpqa'),
			'id'      => 'action_button',
			'options' => array(
				'signup'   => esc_html__('Create A New Account','wpqa'),
				'login'    => esc_html__('Login','wpqa'),
				'question' => esc_html__('Ask A Question','wpqa'),
				'poll'     => esc_html__('Add A Poll','wpqa'),
				'post'     => esc_html__('Add A Post','wpqa'),
				'custom'   => esc_html__('Custom link','wpqa'),
			),
			'std'     => 'signup',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'action_button:is(custom)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'action_button_target',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name' => esc_html__('Type the button link','wpqa'),
			'id'   => 'action_button_link',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Type the button text','wpqa'),
			'id'   => 'action_button_text',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'    => esc_html__('The call to action works for "Unlogged users", "Logged users" or both','wpqa'),
			'desc'    => esc_html__('Choose the call to action works for "Unlogged users", "Logged users" or both.','wpqa'),
			'id'      => 'action_logged',
			'options' => array(
				'unlogged' => esc_html__('Unlogged users','wpqa'),
				'logged'   => esc_html__('Logged users','wpqa'),
				'both'     => esc_html__('Both','wpqa'),
			),
			'std'     => 'unlogged',
			'type'    => 'radio',
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Breadcrumbs','wpqa'),
			'id'   => 'breadcrumb_s',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Breadcrumbs','wpqa'),
			'desc' => esc_html__('Select ON to enable the breadcrumbs.','wpqa'),
			'id'   => 'breadcrumbs',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'breadcrumbs:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Breadcrumbs style','wpqa'),
			'id'      => 'breadcrumbs_style',
			'options' => array(
				'style_1' => esc_html__('Style 1','wpqa'),
				'style_2' => esc_html__('Style 2','wpqa'),
			),
			'std'     => 'style_1',
			'type'    => 'radio',
		);

		$options[] = array(
			'name'      => esc_html__('Breadcrumbs skin','wpqa'),
			'desc'      => esc_html__('Choose the breadcrumbs skin.','wpqa'),
			'id'        => 'breadcrumbs_skin',
			'std'       => 'light',
			'type'      => 'radio',
			'condition' => 'breadcrumbs_style:is(style_2)',
			'options'   => array("light" => esc_html__("Light","wpqa"),"dark" => esc_html__("Dark","wpqa"),"colored" => esc_html__("Colored","wpqa"))
		);

		$options[] = array(
			'name' => esc_html__("Remove the h1 title for the posts and questions on the inner page to don't duplicate it","wpqa"),
			'desc' => esc_html__("Select ON to enable to remove the h1 title for the posts and questions on the inner page to don't duplicate it.","wpqa"),
			'id'   => 'breadcrumbs_content_title',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Breadcrumbs separator','wpqa'),
			'desc' => esc_html__('Add your breadcrumbs separator.','wpqa'),
			'id'   => 'breadcrumbs_separator',
			'std'  => '/',
			'type' => 'text',
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Posts at header or footer','wpqa'),
			'id'   => 'posts_header',
			'icon' => 'grid-view',
			'type' => 'heading',
		);

		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the posts area or not','wpqa'),
			'desc' => esc_html__('Select ON to enable the posts area.','wpqa'),
			'id'   => 'blog_h',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'blog_h:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('The posts area works after header or before footer?','wpqa'),
			'id'      => 'blog_h_where',
			'options' => array(
				'header' => esc_html__('After header','wpqa'),
				'footer' => esc_html__('Before footer','wpqa'),
			),
			'std'     => 'footer',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name'    => esc_html__('The posts area works at all the pages, custom pages, or home page only?','wpqa'),
			'id'      => 'blog_h_home_pages',
			'options' => $single_home_pages,
			'std'     => 'home_page',
			'type'    => 'radio'
		);

		$options[] = array(
			'name'      => esc_html__('Page ids','wpqa'),
			'desc'      => esc_html__('Type from here the page ids','wpqa'),
			'id'        => 'blog_h_pages',
			'type'      => 'text',
			'condition' => 'blog_h_home_pages:is(custom_pages)'
		);
		
		$options[] = array(
			'name' => esc_html__('The title','wpqa'),
			'desc' => esc_html__('Type from here the title','wpqa'),
			'id'   => 'blog_h_title',
			'type' => 'text',
			'std'  => 'Latest News & Updates'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the more post button','wpqa'),
			'desc' => esc_html__('Select ON to enable the button.','wpqa'),
			'id'   => 'blog_h_button',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'blog_h_button:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('The text for the button','wpqa'),
			'desc' => esc_html__('Type from here the text for the button','wpqa'),
			'id'   => 'blog_h_button_text',
			'type' => 'text',
			'std'  => 'Explore Our Blog'
		);
		
		$options[] = array(
			'name'    => esc_html__('Blog page','wpqa'),
			'desc'    => esc_html__('Select the blog page','wpqa'),
			'id'      => 'blog_h_page',
			'type'    => 'select',
			'options' => $options_pages
		);
		
		$options[] = array(
			'name' => esc_html__("Type the blog link if you don't like a page","wpqa"),
			'id'   => 'blog_h_link',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Posts number','wpqa'),
			'id'   => 'blog_h_post_number',
			'std'  => 3,
			'type' => 'text'
		);
		
		$options[] = array(
			'name'    => esc_html__('Post style','wpqa'),
			'desc'    => esc_html__('Choose post style from here.','wpqa'),
			'id'      => 'blog_h_post_style',
			'options' => array(
				'style_1' => esc_html__('1 column','wpqa'),
				'style_2' => esc_html__('List style','wpqa'),
				'style_3' => esc_html__('Columns','wpqa'),
			),
			'std'   => 'style_3',
			'type'  => 'radio',
		);

		$options[] = array(
			'name'    => esc_html__('Order by','wpqa'),
			'desc'    => esc_html__('Select the post order by.','wpqa'),
			'id'      => "orderby_post_h",
			'std'     => "recent",
			'type'    => "radio",
			'options' => array(
				'recent'  => esc_html__('Recent','wpqa'),
				'popular' => esc_html__('Most Commented','wpqa'),
				'random'  => esc_html__('Random','wpqa'),
			)
		);
		
		$options[] = array(
			'name'    => esc_html__('Order','wpqa'),
			'id'      => 'order_post_h',
			'std'     => "DESC",
			'type'    => 'radio',
			'options' => array(
				'DESC' => esc_html__('Descending','wpqa'),
				'ASC'  => esc_html__('Ascending','wpqa'),
			),
		);
		
		$options[] = array(
			'name'    => esc_html__('Display by','wpqa'),
			'id'      => "post_display_h",
			'type'    => 'select',
			'options' => array(
				'lasts'	             => esc_html__('Lasts','wpqa'),
				'single_category'    => esc_html__('Single category','wpqa'),
				'categories'         => esc_html__('Multiple categories','wpqa'),
				'exclude_categories' => esc_html__('Exclude categories','wpqa'),
				'custom_posts'	     => esc_html__('Custom posts','wpqa'),
			),
			'std'     => 'lasts',
		);
		
		$options[] = array(
			'name'      => esc_html__('Single category','wpqa'),
			'id'        => 'post_single_category_h',
			'type'      => 'select_category',
			'condition' => 'post_display_h:is(single_category)',
		);
		
		$options[] = array(
			'name'      => esc_html__('Post categories','wpqa'),
			'desc'      => esc_html__('Select the post categories.','wpqa'),
			'id'        => "post_categories_h",
			'type'      => 'multicheck_category',
			'condition' => 'post_display_h:is(categories)',
		);
		
		$options[] = array(
			'name'      => esc_html__('Post exclude categories','wpqa'),
			'desc'      => esc_html__('Select the post exclude categories.','wpqa'),
			'id'        => "post_exclude_categories_h",
			'type'      => 'multicheck_category',
			'condition' => 'post_display_h:is(exclude_categories)',
		);
		
		$options[] = array(
			'name'      => esc_html__('Post ids','wpqa'),
			'desc'      => esc_html__('Type the post ids.','wpqa'),
			'id'        => "post_posts_h",
			'type'      => 'text',
			'condition' => 'post_display_h:is(custom_posts)',
		);
		
		$options[] = array(
			'name'      => esc_html__("Activate the masonry style?","wpqa"),
			'id'        => 'blog_h_masonry_style',
			'type'      => 'checkbox',
			'condition' => 'blog_h_post_style:is(style_3)',
		);
		
		$options[] = array(
			'name' => esc_html__('Choose a custom setting for the posts','wpqa'),
			'id'   => 'blog_h_custom_home_blog',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'blog_h_custom_home_blog:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Hide the featured image in the loop','wpqa'),
			'desc' => esc_html__('Select ON to hide the featured image in the loop.','wpqa'),
			'id'   => 'blog_h_featured_image',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'id'        => "blog_h_sort_meta_title_image",
			'condition' => 'blog_h_post_style:is(style_3)',
			'std'       => array(
							array("value" => "image",'name' => esc_html__('Image','wpqa'),"default" => "yes"),
							array("value" => "meta_title",'name' => esc_html__('Meta and title','wpqa'),"default" => "yes"),
						),
			'type'      => "sort",
			'options'   => array(
							array("value" => "image",'name' => esc_html__('Image','wpqa'),"default" => "yes"),
							array("value" => "meta_title",'name' => esc_html__('Meta and title','wpqa'),"default" => "yes"),
						)
		);
		
		$options[] = array(
			'name' => esc_html__('Read more enable or disable','wpqa'),
			'id'   => 'blog_h_read_more',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Excerpt post','wpqa'),
			'desc' => esc_html__('Put here the excerpt post.','wpqa'),
			'id'   => 'blog_h_post_excerpt',
			'std'  => 40,
			'type' => 'text'
		);
		
		$options[] = array(
			'name'    => esc_html__('Select the meta options','wpqa'),
			'id'      => 'blog_h_post_meta',
			'type'    => 'multicheck',
			'std'     => array(
				"category_post" => "category_post",
				"title_post"    => "title_post",
				"author_by"     => "author_by",
				"post_date"     => "post_date",
				"post_comment"  => "post_comment",
				"post_views"    => "post_views",
			),
			'options' => array(
				"category_post" => esc_html__('Category post - Work at 1 column only','wpqa'),
				"title_post"    => esc_html__('Title post','wpqa'),
				"author_by"     => esc_html__('Author by - Work at 1 column only','wpqa'),
				"post_date"     => esc_html__('Date meta','wpqa'),
				"post_comment"  => esc_html__('Comment meta','wpqa'),
				"post_views"    => esc_html__("Views stats","wpqa"),
			)
		);
		
		$options[] = array(
			'name'      => esc_html__('Select the share options','wpqa'),
			'id'        => 'blog_h_post_share',
			'condition' => 'blog_h_post_style:not(style_3)',
			'type'      => 'multicheck',
			'sort'      => 'yes',
			'std'       => $share_array,
			'options'   => $share_array
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_before_slider_setting',$options);

		$options[] = array(
			'name' => esc_html__('Slider settings','wpqa'),
			'id'   => 'slider',
			'icon' => 'images-alt2',
			'type' => 'heading',
		);

		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the slider or not','wpqa'),
			'desc' => esc_html__('Select ON to enable the slider.','wpqa'),
			'id'   => 'slider_h',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'slider_h:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Slider works at all the pages, custom pages, or home page only?','wpqa'),
			'id'      => 'slider_h_home_pages',
			'options' => $single_home_pages,
			'std'     => 'home_page',
			'type'    => 'radio'
		);

		$options[] = array(
			'name'      => esc_html__('Page ids','wpqa'),
			'desc'      => esc_html__('Type from here the page ids','wpqa'),
			'id'        => 'slider_h_pages',
			'type'      => 'text',
			'condition' => 'slider_h_home_pages:is(custom_pages)'
		);

		$options[] = array(
			'name'    => esc_html__('Slider works for "Unlogged users", "Logged users" or both','wpqa'),
			'id'      => 'slider_h_logged',
			'options' => array(
				'unlogged' => esc_html__('Unlogged users','wpqa'),
				'logged'   => esc_html__('Logged users','wpqa'),
				'both'     => esc_html__('Both','wpqa'),
			),
			'std'     => 'both',
			'type'    => 'radio',
		);

		$options[] = array(
			'name'    => esc_html__('Choose the slider that works with the theme or add your custom slider by inserting the code or shortcodes','wpqa'),
			'id'      => 'custom_slider',
			'options' => array(
				'slider' => esc_html__('Theme slider','wpqa'),
				'custom' => esc_html__('Custom slider','wpqa'),
			),
			'std'     => 'slider',
			'type'    => 'radio',
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'custom_slider:is(slider)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Slider height','wpqa'),
			"id"   => "slider_height",
			"type" => "sliderui",
			"step" => "50",
			"min"  => "400",
			"max"  => "1000",
			"std"  => "500"
		);

		$slide_elements = array(
			array(
				"type" => "color",
				"id"   => "color",
				"name" => esc_html__('Color','wpqa')
			),
			array(
				"type" => "upload",
				"id"   => "image",
				"name" => esc_html__('Image','wpqa')
			),
			array(
				"type"  => "slider",
				"name"  => esc_html__('Choose the background opacity','wpqa'),
				"id"    => "opacity",
				"std"   => "0",
				"step"  => "1",
				"min"   => "0",
				"max"   => "100",
				"value" => "0"
			),
			array(
				"type"    => "radio",
				"id"      => "align",
				"name"    => esc_html__('Align','wpqa'),
				'options' => array(
					'left'   => esc_html__('Left','wpqa'),
					'center' => esc_html__('Center','wpqa'),
					'right'  => esc_html__('Right','wpqa'),
				),
				'std'     => 'left',
			),
			array(
				"type"      => "radio",
				"id"        => "login",
				"name"      => esc_html__('Login or Signup','wpqa'),
				'options'   => array(
					'none'   => esc_html__('None','wpqa'),
					'login'  => esc_html__('Login','wpqa'),
					'signup' => esc_html__('Signup','wpqa'),
				),
				'condition' => '[%id%]align:not(center),[%id%]button_block:not(block)',
				'std'       => 'login',
			),
			array(
				"type" => "text",
				"id"   => "title",
				"name" => esc_html__('Title','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "title_2",
				"name" => esc_html__('Second title','wpqa')
			),
			array(
				"type" => "textarea",
				"id"   => "paragraph",
				"name" => esc_html__('Paragraph','wpqa')
			),
			array(
				"type"    => "radio",
				"id"      => "button_block",
				"name"    => esc_html__('Button or Block','wpqa'),
				'options' => array(
					'none'   => esc_html__('None','wpqa'),
					'button' => esc_html__('button','wpqa'),
					'block'  => esc_html__('Block','wpqa'),
				),
				'std'     => 'none',
			),
			array(
				"type"      => "radio",
				"id"        => "block",
				"name"      => esc_html__('Block','wpqa'),
				'options'   => array(
					'search'   => esc_html__('Search','wpqa'),
					'question' => esc_html__('Ask A Question','wpqa'),
				),
				'condition' => '[%id%]button_block:is(block)',
				'std'       => 'search',
			),
			array(
				"type"      => "radio",
				"id"        => "button",
				"name"      => esc_html__('Button','wpqa'),
				'options'   => array(
					'signup'   => esc_html__('Create A New Account','wpqa'),
					'login'    => esc_html__('Login','wpqa'),
					'question' => esc_html__('Ask A Question','wpqa'),
					'poll'     => esc_html__('Add A Poll','wpqa'),
					'post'     => esc_html__('Add A Post','wpqa'),
					'custom'   => esc_html__('Custom link','wpqa'),
				),
				'condition' => '[%id%]button_block:is(button)',
				'std'       => 'signup',
			),
			array(
				"type"      => "radio",
				"id"        => "button_style",
				"name"      => esc_html__('Button style','wpqa'),
				'options'   => array(
					'style_1' => esc_html__('Style 1','wpqa'),
					'style_2' => esc_html__('Style 2','wpqa'),
					'style_3' => esc_html__('Style 3','wpqa'),
				),
				'condition' => '[%id%]button_block:is(button)',
				'std'       => 'style_1',
			),
			array(
				'div'       => 'div',
				'condition' => '[%id%]button:is(custom),[%id%]button_block:is(button)',
				'type'      => 'heading-2'
			),
			array(
				'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'      => 'button_target',
				'std'     => "new_page",
				'type'    => 'select',
				'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			),
			array(
				'name' => esc_html__('Type the button link','wpqa'),
				'id'   => 'button_link',
				'type' => 'text'
			),
			array(
				'name' => esc_html__('Type the button text','wpqa'),
				'id'   => 'button_text',
				'type' => 'text'
			),
			array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			),
		);
		
		$options[] = array(
			'id'      => "add_slides",
			'type'    => "elements",
			'button'  => esc_html__('Add a new slide','wpqa'),
			'hide'    => "yes",
			'options' => $slide_elements,
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'id'        => "custom_slides",
			'type'      => "textarea",
			'name'      => esc_html__('Add your custom slide or shortcode','wpqa'),
			'condition' => 'custom_slider:is(custom)',
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_after_slider_setting',$options);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options = apply_filters('wpqa_after_slider_setting',$options,$options_pages,$new_roles,$imagepath,$imagepath_theme,$new_sidebars,$menus);

		$options[] = array(
			'name' => esc_html__('Responsive settings','wpqa'),
			'id'   => 'responsive',
			'icon' => 'smartphone',
			'type' => 'heading'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('The mobile apps bar enable or disable?','wpqa'),
			'desc' => esc_html__('Select ON to enable the mobile apps bar.','wpqa'),
			'id'   => 'mobile_bar_apps',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'mobile_bar_apps:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Mobile apps bar skin','wpqa'),
			'desc'    => esc_html__('Choose the mobile apps bar skin.','wpqa'),
			'id'      => 'mobile_apps_bar_skin',
			'std'     => 'light',
			'type'    => 'radio',
			'options' => array("dark" => esc_html__("Dark","wpqa"),"light" => esc_html__("Light","wpqa"),"colored" => esc_html__("Colored","wpqa"))
		);

		$options[] = array(
			'name' => esc_html__('Type your iPhone app link','wpqa'),
			'id'   => 'mobile_bar_apps_iphone',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Type your Android app link','wpqa'),
			'id'   => 'mobile_bar_apps_android',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'      => esc_html__('Stop fixed header in mobile','wpqa'),
			'desc'      => esc_html__('Select ON to stop fixed header in mobile.','wpqa'),
			'id'        => 'header_fixed_responsive',
			'condition' => 'header_fixed:not(0)',
			'type'      => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Icon for the login or signup in the mobile header','wpqa'),
			'desc' => esc_html__('Type from here the icon of the login or signup button in the mobile header.','wpqa'),
			'id'   => 'header_responsive_icon',
			'std'  => 'icon-lock',
			'type' => 'text'
		);

		$options[] = array(
			'name'    => esc_html__('Button at mobile for the unlogged case','wpqa'),
			'desc'    => esc_html__('Choose button type at the mobile display for the unlogged case from here.','wpqa'),
			'id'      => 'mobile_sign',
			'options' => array(
				'login'  => esc_html__('Login','wpqa'),
				'signup' => esc_html__('Signup','wpqa'),
			),
			'std'     => 'login',
			'type'    => 'radio'
		);

		$options[] = array(
			'name' => esc_html__('Mobile button on the mobile menu enable or disable?','wpqa'),
			'desc' => esc_html__('Select ON to enable the mobile button on the mobile menu.','wpqa'),
			'id'   => 'mobile_button_menu',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'mobile_button_menu:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Mobile button on the mobile menu','wpqa'),
			'desc'    => esc_html__('Choose mobile button on the mobile menu type from here.','wpqa'),
			'id'      => 'mobile_button_menu_type',
			'options' => array(
				'question' => esc_html__('Ask A Question','wpqa'),
				'poll'     => esc_html__('Add A Poll','wpqa'),
				'post'     => esc_html__('Add A Post','wpqa'),
				'group'    => esc_html__('Create A Group','wpqa'),
				'custom'   => esc_html__('Custom link','wpqa'),
			),
			'std'     => 'question',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'mobile_button_menu_type:is(custom)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'mobile_button_menu_target',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name' => esc_html__('Type the button link','wpqa'),
			'id'   => 'mobile_button_menu_link',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Type the button text','wpqa'),
			'id'   => 'mobile_button_menu_text',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Choose the mobile menu skin','wpqa'),
			'id'   => "mobile_menu",
			'std'  => "dark",
			'type' => "images",
			'options' => array(
				'dark'  => $imagepath.'menu_dark.jpg',
				'gray'  => $imagepath.'sidebar_no.jpg',
				'light' => $imagepath.'menu_light.jpg',
			)
		);
		
		$options[] = array(
			'name' => esc_html__('Mobile bar enable or disable?','wpqa'),
			'desc' => esc_html__('Select ON to enable the mobile bar.','wpqa'),
			'id'   => 'mobile_bar',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'mobile_bar:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Mobile button','wpqa'),
			'desc'    => esc_html__('Choose mobile button type from here.','wpqa'),
			'id'      => 'mobile_button',
			'options' => array(
				'question' => esc_html__('Ask A Question','wpqa'),
				'poll'     => esc_html__('Add A Poll','wpqa'),
				'post'     => esc_html__('Add A Post','wpqa'),
				'group'    => esc_html__('Create A Group','wpqa'),
				'custom'   => esc_html__('Custom link','wpqa'),
			),
			'std'     => 'question',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'mobile_button:is(custom)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'mobile_button_target',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name' => esc_html__('Type the button link','wpqa'),
			'id'   => 'mobile_button_link',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Type the button text','wpqa'),
			'id'   => 'mobile_button_text',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		if (has_discy()) {
			$options[] = array(
				'name' => esc_html__('Activate a custom mobile menu or not?','wpqa'),
				'desc' => esc_html__('Select ON to enable the custom mobile menu.','wpqa'),
				'id'   => 'active_mobile_menu',
				'type' => 'checkbox'
			);

			$options[] = array(
				'name'      => esc_html__('Sort the mobile menus','wpqa'),
				'id'        => "sort_mobile_menus",
				'condition' => 'active_mobile_menu:not(on)',
				'std'       => array(
								array("value" => "left",'name' => esc_html__('Left menu','wpqa'),"default" => "yes"),
								array("value" => "top",'name' => esc_html__('Top menu','wpqa'),"default" => "yes"),
							),
				'type'      => "sort",
				'options'   => array(
								array("value" => "left",'name' => esc_html__('Left menu','wpqa'),"default" => "yes"),
								array("value" => "top",'name' => esc_html__('Top menu','wpqa'),"default" => "yes"),
							)
			);
			
			$options[] = array(
				'name'      => esc_html__('Choose from here which menu will show at mobile for "unlogged users".','wpqa'),
				'id'        => 'mobile_menu',
				'type'      => 'select',
				'condition' => 'active_mobile_menu:not(0)',
				'options'   => $menus
			);
			
			$options[] = array(
				'name'      => esc_html__('Choose from here which menu will show at mobile for "logged in users".','wpqa'),
				'id'        => 'mobile_menu_logged',
				'type'      => 'select',
				'condition' => 'active_mobile_menu:not(0)',
				'options'   => $menus
			);
		}
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'    => esc_html__('Post settings','wpqa'),
			'id'      => 'posts',
			'icon'    => 'admin-page',
			'type'    => 'heading',
			'std'     => 'post_loop',
			'options' => array(
				"post_loop"         => esc_html__('Posts & Loop setting','wpqa'),
				"add_edit_delete_p" => esc_html__('Add - Edit - Delete','wpqa'),
				"post_meta"         => esc_html__('Post meta settings','wpqa'),
				"inner_pages"       => esc_html__('Inner page settings','wpqa'),
				"share_setting"     => esc_html__('Share setting','wpqa'),
				"related_setting"   => esc_html__('Related setting','wpqa')
			)
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'post_loop',
			'name' => esc_html__('Posts & Loop setting','wpqa')
		);
		
		$options[] = array(
			'name'    => esc_html__('Post style','wpqa'),
			'desc'    => esc_html__('Choose post style from here.','wpqa'),
			'id'      => 'post_style',
			'options' => array(
				'style_1' => esc_html__('1 column','wpqa'),
				'style_2' => esc_html__('List style','wpqa'),
				'style_3' => esc_html__('Columns','wpqa'),
			),
			'std'     => 'style_1',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name'      => esc_html__("Activate the masonry style?","wpqa"),
			'id'        => 'post_masonry_style',
			'type'      => 'checkbox',
			'condition' => 'post_style:is(style_3)',
		);
		
		$options[] = array(
			'name' => esc_html__('Category description enable or disable','wpqa'),
			'desc' => esc_html__('Select ON to enable the category description in the category page.','wpqa'),
			'id'   => 'category_description',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Category rss enable or disable','wpqa'),
			'desc'      => esc_html__('Select ON to enable the category rss in the category page.','wpqa'),
			'id'        => 'category_rss',
			'std'       => 'on',
			'condition' => 'category_description:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Tag description enable or disable','wpqa'),
			'desc' => esc_html__('Select ON to enable the tag description in the tag page.','wpqa'),
			'id'   => 'tag_description',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Tag rss enable or disable','wpqa'),
			'desc'      => esc_html__('Select ON to enable the tag rss in the tag page.','wpqa'),
			'id'        => 'tag_rss',
			'std'       => 'on',
			'condition' => 'tag_description:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Hide the featured image in the loop','wpqa'),
			'desc' => esc_html__('Select ON to hide the featured image in the loop.','wpqa'),
			'id'   => 'featured_image_loop_post',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'id'        => "sort_meta_title_image",
			'condition' => 'post_style:is(style_3)',
			'std'       => array(
							array("value" => "image",'name' => esc_html__('Image','wpqa'),"default" => "yes"),
							array("value" => "meta_title",'name' => esc_html__('Meta and title','wpqa'),"default" => "yes"),
						),
			'type'      => "sort",
			'options'   => array(
							array("value" => "image",'name' => esc_html__('Image','wpqa'),"default" => "yes"),
							array("value" => "meta_title",'name' => esc_html__('Meta and title','wpqa'),"default" => "yes"),
						)
		);
		
		$options[] = array(
			'name' => esc_html__('Read more enable or disable','wpqa'),
			'id'   => 'read_more',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Excerpt post','wpqa'),
			'desc' => esc_html__('Put here the excerpt post.','wpqa'),
			'id'   => 'post_excerpt',
			'std'  => 40,
			'type' => 'text'
		);
		
		$options[] = array(
			'name'    => esc_html__('Pagination style','wpqa'),
			'desc'    => esc_html__('Choose pagination style from here.','wpqa'),
			'id'      => 'post_pagination',
			'options' => array(
				'standard'        => esc_html__('Standard','wpqa'),
				'pagination'      => esc_html__('Pagination','wpqa'),
				'load_more'       => esc_html__('Load more','wpqa'),
				'infinite_scroll' => esc_html__('Infinite scroll','wpqa'),
				'none'            => esc_html__('None','wpqa'),
			),
			'std'     => 'pagination',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'add_edit_delete_p',
			'name' => esc_html__('Add, edit and delete post','wpqa')
		);
		
		$options[] = array(
			'name' => esc_html__('Send schedule mails for the users as a list with recent posts','wpqa'),
			'id'   => 'post_schedules',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'post_schedules:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Schedule mails time','wpqa'),
			'id'      => 'post_schedules_time',
			'type'    => 'multicheck',
			'std'     => array("daily" => "daily","weekly" => "weekly","monthly" => "monthly"),
			'options' => array(
				"daily" => esc_html__("Daily","wpqa"),
				"weekly" => esc_html__("Weekly","wpqa"),
				"monthly" => esc_html__("Monthly","wpqa")
			)
		);

		$options[] = array(
			"name" => esc_html__("Set the hour to send the mail at this hour","wpqa"),
			"id"   => "schedules_time_hour_post",
			"type" => "sliderui",
			'std'  => 12,
			"step" => "1",
			"min"  => "1",
			"max"  => "24"
		);

		$options[] = array(
			'name'    => esc_html__('Select the day to send the mail at this day','wpqa'),
			'id'      => 'schedules_time_day_post',
			'type'    => "select",
			'std'     => "saturday",
			'options' => array(
				'saturday'  => esc_html__('Saturday','wpqa'),
				'sunday'    => esc_html__('Sunday','wpqa'),
				'monday'    => esc_html__('Monday','wpqa'),
				'tuesday'   => esc_html__('Tuesday','wpqa'),
				'wednesday' => esc_html__('Wednesday','wpqa'),
				'thursday'  => esc_html__('Thursday','wpqa'),
				'friday'    => esc_html__('Friday','wpqa')
			)
		);
		
		$options[] = array(
			'name'    => esc_html__('Send schedule mails for custom roles to send a list with recent posts','wpqa'),
			'id'      => 'post_schedules_groups',
			'type'    => 'multicheck',
			'std'     => array("editor" => "editor","administrator" => "administrator","author" => "author","contributor" => "contributor","subscriber" => "subscriber"),
			'options' => $wpqa_options_roles
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name'    => esc_html__("Choose the way of the sending emails and notifications","wpqa"),
			'desc'    => esc_html__("Make the send mail to the users and send notification from the site or with schedule cron job","wpqa"),
			'id'      => 'way_sending_notifications_posts',
			'std'     => 'site',
			'type'    => 'radio',
			'options' => array(
				"site"    => esc_html__("From the site","wpqa"),
				"cronjob" => esc_html__("Schedule cron job","wpqa")
			)
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'way_sending_notifications_posts:is(cronjob)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Schedule mails time','wpqa'),
			'id'      => 'schedules_time_notification_posts',
			'type'    => 'radio',
			'std'     => "hourly",
			'options' => array(
				"daily"       => esc_html__("One time daily","wpqa"),
				"twicedaily"  => esc_html__("Twice times daily","wpqa"),
				"hourly"      => esc_html__("Hourly","wpqa"),
				"twicehourly" => esc_html__("Each 30 minutes","wpqa")
			)
		);

		$options[] = array(
			"name"      => esc_html__("Set the hour to send the mail at this hour","wpqa"),
			"id"        => "schedules_time_hour_notification_post",
			'condition' => 'schedules_time_notification_posts:is(daily),schedules_time_notification_posts:is(twicedaily)',
			'operator'  => 'or',
			"type"      => "sliderui",
			'std'       => 12,
			"step"      => "1",
			"min"       => "1",
			"max"       => "24"
		);

		$options[] = array(
			"name" => esc_html__("Set the number of the posts you want to send on the one time","wpqa"),
			"id"   => "schedules_number_post",
			"type" => "sliderui",
			'std'  => 10,
			"step" => "1",
			"min"  => "1",
			"max"  => "100"
		);
		
		$options[] = array(
			'name'      => esc_html__('Note: if you choose one time daily or twice times daily we will make it visible hourly to check if there are any more posts that need to send the notifications.','wpqa'),
			'condition' => 'schedules_time_notification_posts:is(daily),schedules_time_notification_posts:is(twicedaily)',
			'operator'  => 'or',
			'type'      => 'info'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose the way of the sending mails and notifications of a new post','wpqa'),
			'id'      => 'send_email_and_notification_post',
			'options' => array(
				'both'       => esc_html__('Both with the same options','wpqa'),
				'separately' => esc_html__('Separately','wpqa'),
			),
			'std'     => 'both',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'send_email_and_notification_post:is(both)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Send mail and notification to the users about the notification of a new post','wpqa'),
			'desc' => esc_html__('Send mail and notification enable or disable.','wpqa'),
			'id'   => 'send_email_new_post_both',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Send mail and notification for custom roles about the notification of a new post','wpqa'),
			'id'        => 'send_email_post_groups_both',
			'type'      => 'multicheck',
			'condition' => 'send_email_new_post_both:not(0)',
			'std'       => array("editor" => "editor","administrator" => "administrator","author" => "author","contributor" => "contributor","subscriber" => "subscriber"),
			'options'   => $wpqa_options_roles
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'send_email_and_notification_post:is(separately)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Send mail to the users about the notification of a new post','wpqa'),
			'desc' => esc_html__('Send mail enable or disable.','wpqa'),
			'id'   => 'send_email_new_post',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Send mail for custom roles about the notification of a new post','wpqa'),
			'id'        => 'send_email_post_groups',
			'type'      => 'multicheck',
			'condition' => 'send_email_new_post:not(0)',
			'std'       => array("editor" => "editor","administrator" => "administrator","author" => "author","contributor" => "contributor","subscriber" => "subscriber"),
			'options'   => $wpqa_options_roles
		);
		
		$options[] = array(
			'name' => esc_html__('Send notification to the users about the notification of a new post','wpqa'),
			'desc' => esc_html__('Send notification enable or disable.','wpqa'),
			'id'   => 'send_notification_new_post',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Send notification for custom roles about the notification of a new post','wpqa'),
			'id'        => 'send_notification_post_groups',
			'type'      => 'multicheck',
			'condition' => 'send_notification_new_post:not(0)',
			'std'       => array("editor" => "editor","administrator" => "administrator","author" => "author","contributor" => "contributor","subscriber" => "subscriber"),
			'options'   => $wpqa_options_roles
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'way_sending_notifications_posts:is(cronjob)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('The link of the notification after sending it to users will go to?','wpqa'),
			'id'      => 'notifications_posts_link',
			'std'     => "home",
			'type'    => 'select',
			'options' => array("home" => esc_html__("Home","wpqa"),"custom_page" => esc_html__("Custom page","wpqa"),"custom_link" => esc_html__("Custom link","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__("Type the link if you don't like above","wpqa"),
			'id'        => 'notifications_posts_custom_page',
			'condition' => 'notifications_posts_link:is(custom_page)',
			'type'      => 'select',
			'options'   => $options_pages
		);
		
		$options[] = array(
			'name'      => esc_html__("Type the link if you don't like above","wpqa"),
			'id'        => 'notifications_posts_custom_link',
			'condition' => 'notifications_posts_link:is(custom_link)',
			'type'      => 'text'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Add posts','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Add post slug','wpqa'),
			'desc' => esc_html__('Put the add post slug.','wpqa'),
			'id'   => 'add_posts_slug',
			'std'  => 'add-post',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => '<a href="'.wpqa_add_post_permalink().'" target="_blank">'.esc_html__('The Link For The Add Post Page.','wpqa').'</a>',
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the add post with popup','wpqa'),
			'desc' => esc_html__('Add post with popup enable or disable.','wpqa'),
			'id'   => 'active_post_popup',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Any one can add post without register','wpqa'),
			'desc' => esc_html__('Any one can add post without register enable or disable.','wpqa'),
			'id'   => 'add_post_no_register',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose post status for users only','wpqa'),
			'desc'    => esc_html__('Choose post status after user publish the post.','wpqa'),
			'id'      => 'post_publish',
			'options' => array("publish" => esc_html__("Auto publish","wpqa"),"draft" => esc_html__("Need a review","wpqa")),
			'std'     => 'draft',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name'      => esc_html__('Choose post status for "unlogged user" only','wpqa'),
			'desc'      => esc_html__('Choose post status after "unlogged user" publish the post.','wpqa'),
			'id'        => 'post_publish_unlogged',
			'options'   => array("publish" => esc_html__("Auto publish","wpqa"),"draft" => esc_html__("Need a review","wpqa")),
			'std'       => 'draft',
			'type'      => 'radio',
			'condition' => 'add_post_no_register:not(0)',
		);
		
		$options[] = array(
			'name'      => esc_html__('Send mail when the post needs a review','wpqa'),
			'desc'      => esc_html__('Mail for posts review enable or disable.','wpqa'),
			'id'        => 'send_email_draft_posts',
			'std'       => 'on',
			'operator'  => 'or',
			'condition' => 'post_publish:not(publish),post_publish_unlogged:not(publish)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Auto Approve posts for the users that have a previously approved posts.','wpqa'),
			'id'        => 'approved_posts',
			'condition' => 'post_publish:not(publish)',
			'type'      => 'checkbox'
		);

		$add_post_items = array(
			"tags_post"      => array("sort" => esc_html__('Post Tags','wpqa'),"value" => "tags_post"),
			"featured_image" => array("sort" => esc_html__('Post featured image','wpqa'),"value" => "featured_image"),
			"content_post"   => array("sort" => esc_html__('Post content','wpqa'),"value" => "content_post"),
			"terms_active"   => array("sort" => esc_html__('Terms of Service and Privacy Policy','wpqa'),"value" => ""),
		);
		
		$options[] = array(
			'name'    => esc_html__("Select what to show at Add post form","wpqa"),
			'id'      => 'add_post_items',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $add_post_items,
			'options' => $add_post_items
		);
		
		$options[] = array(
			'name'      => esc_html__('Enable or disable the editor for details in add post form','wpqa'),
			'id'        => 'editor_post_details',
			'condition' => 'add_post_items:has(content_post)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'add_post_items:has(terms_active)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Terms of Service and Privacy Policy','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Select the checked by default option','wpqa'),
			'desc' => esc_html__('Select ON if you want to checked it by default.','wpqa'),
			'id'   => 'terms_checked_post',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'terms_active_target_post',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'    => esc_html__('Terms page','wpqa'),
			'desc'    => esc_html__('Select the terms page','wpqa'),
			'id'      => 'terms_page_post',
			'type'    => 'select',
			'options' => $options_pages
		);
		
		$options[] = array(
			'name' => esc_html__("Type the terms link if you don't like a page","wpqa"),
			'id'   => 'terms_link_post',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate Privacy Policy','wpqa'),
			'desc' => esc_html__('Select ON if you want to activate Privacy Policy.','wpqa'),
			'id'   => 'privacy_policy_post',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'privacy_policy_post:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'privacy_active_target_post',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'    => esc_html__('Privacy Policy page','wpqa'),
			'desc'    => esc_html__('Select the privacy policy page','wpqa'),
			'id'      => 'privacy_page_post',
			'type'    => 'select',
			'options' => $options_pages
		);
		
		$options[] = array(
			'name' => esc_html__("Type the privacy policy link if you don't like a page","wpqa"),
			'id'   => 'privacy_link_post',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Limitations for title','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Add minimum limit for the number of letters for the post title, like 15, 20, if you leave it empty, it will be not important','wpqa'),
			'id'   => 'post_title_min_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Add limit for the number of letters for the post title, like 140, 200, if you leave it empty, it will be unlimited','wpqa'),
			'id'   => 'post_title_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'add_post_items:has(tags_post)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Limitations for tags','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Add minimum limit for the number of letters for the post tag word, like 15, 20, if you leave it empty it will be not important','wpqa'),
			'id'   => 'post_tags_min_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Add word limit for the number of letters for the post tag, like 140, 200, if you leave it empty will be unlimited','wpqa'),
			'id'   => 'post_tags_limit',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Add minimum limit for the number of items for the post tags, like 2, 4, if you leave it empty will be not important','wpqa'),
			'id'   => 'post_tags_number_min_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Add limit for the number of items for the post tags, like 4, 6, if you leave it empty it will be unlimited','wpqa'),
			'id'   => 'post_tags_number_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'add_post_items:has(content_post)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Limitations for content','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Add minimum limit for the number of letters for the post content, like 15, 20, if you leave it empty it will be not important','wpqa'),
			'id'   => 'post_content_min_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Add limit for the number of letters for the post content, like 140, 200, if you leave it empty it will be unlimited','wpqa'),
			'id'   => 'post_content_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Edit posts','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('The users can edit the posts?','wpqa'),
			'id'   => 'can_edit_post',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'can_edit_post:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Edit post slug','wpqa'),
			'desc' => esc_html__('Put the edit post slug.','wpqa'),
			'id'   => 'edit_posts_slug',
			'std'  => 'edit-post',
			'type' => 'text'
		);
		
		$options[] = array(
			'name'    => esc_html__('After editing post, auto approve the post or need to be approved again.','wpqa'),
			'desc'    => esc_html__('Press ON to auto approve','wpqa'),
			'options' => array("publish" => esc_html__("Auto publish","wpqa"),"draft" => esc_html__("Need a review","wpqa")),
			'id'      => 'post_approved',
			'type'    => 'radio',
			'std'     => 'publish'
		);
		
		$options[] = array(
			'name' => esc_html__('After edit post change the URL from the title?','wpqa'),
			'desc' => esc_html__('Press ON to edit the URL','wpqa'),
			'id'   => 'change_post_url',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Delete posts','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate user can delete the posts','wpqa'),
			'desc' => esc_html__('Select ON if you want the user can delete the posts.','wpqa'),
			'id'   => 'post_delete',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('When the user deleted a post, trash it or delete it for ever?','wpqa'),
			'id'        => 'delete_post',
			'options'   => array(
				'delete' => esc_html__('Delete','wpqa'),
				'trash'  => esc_html__('Trash','wpqa'),
			),
			'std'       => 'delete',
			'condition' => 'post_delete:not(0)',
			'type'      => 'radio'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Post meta settings','wpqa'),
			'id'   => 'post_meta',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Select the meta options','wpqa'),
			'id'      => 'post_meta',
			'type'    => 'multicheck',
			'std'     => array(
				"category_post" => "category_post",
				"title_post"    => "title_post",
				"author_by"     => "author_by",
				"post_date"     => "post_date",
				"post_comment"  => "post_comment",
				"post_views"    => "post_views",
			),
			'options' => array(
				"category_post" => esc_html__('Category post - Work at 1 column only','wpqa'),
				"title_post"    => esc_html__('Title post','wpqa'),
				"author_by"     => esc_html__('Author by - Work at 1 column only','wpqa'),
				"post_date"     => esc_html__('Date meta','wpqa'),
				"post_comment"  => esc_html__('Comment meta','wpqa'),
				"post_views"    => esc_html__("Views stats","wpqa"),
			)
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'inner_pages',
			'name' => esc_html__('Inner page settings','wpqa')
		);
		
		$order_sections = array(
			"author"        => array("sort" => esc_html__('About the author','wpqa'),"value" => "author"),
			"next_previous" => array("sort" => esc_html__('Next and Previous articles','wpqa'),"value" => "next_previous"),
			"advertising"   => array("sort" => esc_html__('Advertising','wpqa'),"value" => "advertising"),
			"related"       => array("sort" => esc_html__('Related articles','wpqa'),"value" => "related"),
			"comments"      => array("sort" => esc_html__('Comments','wpqa'),"value" => "comments"),
		);
		
		$options[] = array(
			'name'    => esc_html__('Sort your sections','wpqa'),
			'id'      => 'order_sections',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $order_sections,
			'options' => $order_sections
		);
		
		$options[] = array(
			'name' => esc_html__('Hide the featured image in the single post','wpqa'),
			'desc' => esc_html__('Select ON to hide the featured image in the single post.','wpqa'),
			'id'   => 'featured_image',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Featured image style','wpqa'),
			'desc'    => esc_html__('Featured image style from here.','wpqa'),
			'id'      => 'featured_image_style',
			'std'     => 'default',
			'options' => array(
				'default' => 'Default',
				'style_270'   => '270x180',
				'style_140'   => '140x140',
				'custom_size' => esc_html__('Custom size','wpqa'),
			),
			'type'    => 'radio'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'featured_image_style:is(custom_size)',
			'div'       => 'div'
		);
			
		$options[] = array(
			'name' => esc_html__('Featured image width','wpqa'),
			"id"   => "featured_image_width",
			"type" => "sliderui",
			"step" => "1",
			"min"  => "140",
			"max"  => "500"
		);
		
		$options[] = array(
			'name' => esc_html__('Featured image height','wpqa'),
			"id"   => "featured_image_height",
			"type" => "sliderui",
			"step" => "1",
			"min"  => "140",
			"max"  => "500"
		);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end',
				'div'  => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Tags enable or disable','wpqa'),
			'id'   => 'post_tags',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		if (has_himer() || has_knowly()) {
			$options[] = array(
				'name' => esc_html__('Newsletter enable or disable','wpqa'),
				'id'   => 'newsletter_blog',
				'std'  => 'on',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name'      => esc_html__('Newsletter action','wpqa'),
				'id'        => 'newsletter_action',
				'condition' => 'newsletter_blog:is(on)',
				'type'      => 'text'
			);
		}
		
		$options[] = array(
			'name'      => esc_html__('Navigation post for the same category only?','wpqa'),
			'desc'      => esc_html__('Navigation post (next and previous posts) for the same category only?','wpqa'),
			'id'        => 'post_nav_category',
			'condition' => 'order_sections:has(next_previous)',
			'std'       => 'on',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Share setting','wpqa'),
			'id'   => 'share_setting',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Select the share options','wpqa'),
			'id'      => 'post_share',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $share_array,
			'options' => $share_array
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Related setting','wpqa'),
			'id'   => 'related_setting',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name'      => esc_html__('Activate it first from Inner page settings.','wpqa'),
			'condition' => 'order_sections:has_not(related)',
			'type'      => 'info'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'order_sections:has(related)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name'    => esc_html__('Related style','wpqa'),
			'desc'    => esc_html__('Type related post style from here.','wpqa'),
			'id'      => 'related_style',
			'std'     => 'style_1',
			'options' => array(
				'style_1' => 'Style 1',
				'links'   => 'Style 2',
			),
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name' => esc_html__('Related posts number','wpqa'),
			'desc' => esc_html__('Type the number of related posts from here.','wpqa'),
			'id'   => 'related_number',
			'std'  => 2,
			'type' => 'text'
		);
		
		$options[] = array(
			'name'      => esc_html__('Related posts number at sidebar','wpqa'),
			'desc'      => esc_html__('Type related posts number at sidebar from here.','wpqa'),
			'id'        => 'related_number_sidebar',
			'std'       => 3,
			'condition' => 'related_style:not(links)',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name'      => esc_html__('Related posts number at full width','wpqa'),
			'desc'      => esc_html__('Type related posts number at full width from here.','wpqa'),
			'id'        => 'related_number_full',
			'std'       => 4,
			'condition' => 'related_style:not(links)',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name'    => esc_html__('Query type','wpqa'),
			'desc'    => esc_html__('Select what will the related posts show.','wpqa'),
			'id'      => 'query_related',
			'std'     => 'categories',
			'options' => array(
				'categories' => esc_html__('Posts in the same categories','wpqa'),
				'tags'       => esc_html__('Posts in the same tags (If not find any tags will show by the same categories)','wpqa'),
				'author'     => esc_html__('Posts by the same author','wpqa'),
			),
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name' => esc_html__('Excerpt title in related posts','wpqa'),
			'desc' => esc_html__('Type excerpt title in related posts from here.','wpqa'),
			'id'   => 'excerpt_related_title',
			'std'  => '10',
			'type' => 'text'
		);
		
		$options[] = array(
			'name'      => esc_html__('Comment in related enable or disable','wpqa'),
			'id'        => 'comment_in_related',
			'std'       => 'on',
			'condition' => 'related_style:not(links)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Date in related enable or disable','wpqa'),
			'id'        => 'date_in_related',
			'std'       => 'on',
			'condition' => 'related_style:not(links)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$questions_settings = array(
			"general_setting"   => esc_html__('General settings','wpqa'),
			"question_slug"     => esc_html__('Question slugs','wpqa'),
			"add_edit_delete"   => esc_html__('Add - Edit - Delete','wpqa'),
			"question_meta"     => esc_html__('Question meta settings','wpqa'),
			"question_category" => esc_html__('Questions category settings','wpqa'),
			"question_tag"      => esc_html__('Questions tag settings','wpqa'),
			"questions_loop"    => esc_html__('Questions & Loop settings','wpqa'),
			"inner_question"    => esc_html__('Inner question','wpqa'),
			"share_setting_q"   => esc_html__('Share setting','wpqa')
		);

		$options[] = array(
			'name'    => esc_html__('Question settings','wpqa'),
			'id'      => 'question',
			'icon'    => 'editor-help',
			'type'    => 'heading',
			'std'     => 'general_setting',
			'options' => apply_filters(wpqa_prefix_theme."_questions_settings",$questions_settings)
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'general_setting',
			'name' => esc_html__('General settings','wpqa')
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_before_question_general_setting',$options);
		
		if (has_discy()) {
			$options[] = array(
				'name' => esc_html__('Select ON if you need to choose the question at simple layout','wpqa'),
				'id'   => 'question_simple',
				'type' => 'checkbox'
			);
		}
		
		$options[] = array(
			'name'    => esc_html__('Ajax file load from admin or theme','wpqa'),
			'desc'    => esc_html__('Choose ajax file load from admin or theme.','wpqa'),
			'id'      => 'ajax_file',
			'std'     => 'admin',
			'type'    => 'select',
			'options' => array("admin" => esc_html__("Admin","wpqa"),"theme" => esc_html__("Theme","wpqa"))
		);
		
		$options[] = array(
			'name' => esc_html__('Show filter at categories and archive pages','wpqa'),
			'desc' => esc_html__('Select ON to enable the filter at categories and archive pages.','wpqa'),
			'id'   => 'category_filter',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Do you need to activate you might like options?','wpqa'),
			'desc' => esc_html__('Select ON if you want to activate you might like for the questions and answers.','wpqa'),
			'id'   => 'might_like',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Do you need to show the questions based on the date and answers updated?','wpqa'),
			'desc' => esc_html__('Select ON if you want to display the questions based on recently added and recent answers added.','wpqa'),
			'id'   => 'updated_answers',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'      => esc_html__('After new answer has been added, move this question to the top. It works for the recent questions, feed, and questions for you tabs or pages.','wpqa'),
			'condition' => 'updated_answers:not(0)',
			'type'      => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Do you need to hide the content only for the private question?','wpqa'),
			'desc' => esc_html__('Select ON if you want to hide the content only for the private question.','wpqa'),
			'id'   => 'private_question_content',
			'type' => 'checkbox'
		);
		
		if (has_himer() || has_knowly() || has_questy()) {
			$options[] = array(
				'name' => esc_html__('Activate the reaction in site?','wpqa'),
				'desc' => esc_html__('Reaction enable or disable.','wpqa'),
				'id'   => 'active_reaction',
				'std'  => "on",
				'type' => 'checkbox'
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => 'active_reaction:not(0)',
				'type'      => 'heading-2'
			);

			$reaction_items = array(
				"love"  => array("sort" => esc_html__('Love','wpqa'),"value" => "love"),
				"hug"   => array("sort" => esc_html__('Hug','wpqa'),"value" => "hug"),
				"haha"  => array("sort" => esc_html__('Haha','wpqa'),"value" => "haha"),
				"wow"   => array("sort" => esc_html__('Wow','wpqa'),"value" => "wow"),
				"sad"   => array("sort" => esc_html__('Sad','wpqa'),"value" => "sad"),
				"angry" => array("sort" => esc_html__('Angry','wpqa'),"value" => "angry"),
			);
			
			$options[] = array(
				'name'    => esc_html__('Reaction','wpqa'),
				'id'      => 'reaction_items',
				'type'    => 'multicheck',
				'sort'    => 'yes',
				'std'     => $reaction_items,
				'options' => $reaction_items
			);
			
			$options[] = array(
				'name'    => esc_html__('Choose the reaction style for the questions on the loop?','wpqa'),
				'id'      => 'reaction_question_style',
				'std'     => "style_1",
				'type'    => 'radio',
				'options' => array(
					"style_1" => esc_html__("Style 1","wpqa"),
					"style_2" => esc_html__("Style 2","wpqa")
				)
			);
			
			$options[] = array(
				'name'    => esc_html__('Choose the reaction style for the questions on the single question pages?','wpqa'),
				'id'      => 'reaction_single_question_style',
				'std'     => "style_1",
				'type'    => 'radio',
				'options' => array(
					"style_1" => esc_html__("Style 1","wpqa"),
					"style_2" => esc_html__("Style 2","wpqa")
				)
			);

			$options[] = array(
				'name' => esc_html__('Activate the reaction on the answers?','wpqa'),
				'desc' => esc_html__('Reaction enable or disable in the answers.','wpqa'),
				'id'   => 'active_reaction_answers',
				'std'  => "on",
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
		}
		
		$options[] = array(
			'name' => esc_html__('Activate the best answer for the normal users in site?','wpqa'),
			'desc' => esc_html__('Best answer enable or disable.','wpqa'),
			'id'   => 'active_best_answer',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Activate user can choose own answer as the best answer','wpqa'),
			'desc'      => esc_html__('User can choose own answer as the best answer enable or disable.','wpqa'),
			'id'        => 'best_answer_userself',
			'std'       => "on",
			'condition' => 'active_best_answer:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the points system in site?','wpqa'),
			'desc' => esc_html__('The points system enable or disable.','wpqa'),
			'id'   => 'active_points',
			'std'  => "on",
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_points:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the points sort with specific days?','wpqa'),
			'desc' => esc_html__('The points sort with day, week, month or year enable or disable.','wpqa'),
			'id'   => 'active_points_specific',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the bump question','wpqa'),
			'desc' => esc_html__('Select ON if you want the bump question.','wpqa'),
			'id'   => 'question_bump',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'question_bump:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Add the points users must add to allow them to bump up the question.','wpqa'),
			'id'   => 'question_bump_points',
			'std'  => 0,
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Make the points for the bump question go to the user who has the best answer','wpqa'),
			'id'   => 'bump_best_answer',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('When the question or answer is deleted, if it has the best answer - remove it from the stats and user points?','wpqa'),
			'desc' => esc_html__('Select ON if you want to remove the best answer from the user points.','wpqa'),
			'id'   => 'remove_best_answer_stats',
			'type' => 'checkbox'
		);

		/*
		$options[] = array(
			'name' => esc_html__('Activate the extract link data?','wpqa'),
			'desc' => esc_html__('The extract link data enable or disable.','wpqa'),
			'id'   => 'extract_link',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the extract link data to save at cache?','wpqa'),
			'id'   => 'extract_link_cache',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__("Choose the cache limit for the links.","wpqa"),
			'id'        => 'extract_link_cache_limit',
			'std'       => 'month',
			'type'      => 'radio',
			'condition' => 'extract_link_cache:not(0)',
			'options'   => array(
				"day"   => esc_html__("Day","wpqa"),
				"week"  => esc_html__("Week","wpqa"),
				"month" => esc_html__("Month","wpqa"),
				"year"  => esc_html__("Year","wpqa")
			)
		);
		*/
		
		$options[] = array(
			'name' => esc_html__('Activate the mention in site?','wpqa'),
			'desc' => esc_html__('Activate the mention enable or disable.','wpqa'),
			'id'   => 'active_mention',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the reports in site?','wpqa'),
			'desc' => esc_html__('Activate the reports enable or disable.','wpqa'),
			'id'   => 'active_reports',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_reports:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the reports in site for the "logged in users" only?','wpqa'),
			'desc' => esc_html__('Activate the reports in site for the "logged in users" only enable or disable.','wpqa'),
			'id'   => 'active_logged_reports',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_points:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the users having certain points can move the question or answer to trash or draft by reporting.','wpqa'),
			'id'   => 'active_trash_reports',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_trash_reports:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Move the question or answer to trash or draft when reported.','wpqa'),
			'id'      => 'trash_draft_reports',
			'options' => array("trash" => esc_html__("Trash","wpqa"),"draft" => esc_html__("Draft","wpqa")),
			'type'    => 'select'
		);
		
		$options[] = array(
			'name' => esc_html__('Add the points to allow the users which will let them move the question or answer to trash or draft when reported.','wpqa'),
			'id'   => 'trash_reports_points',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Add minimum of the points if anyone which have them, their questions or answers will not move to trash or draft.','wpqa'),
			'id'   => 'reports_min_points',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Whitelist questions.','wpqa'),
			'desc' => esc_html__('Add here the whitelist question, Any questions here will not move to trash or draft.','wpqa'),
			'id'   => 'whitelist_questions',
			'type' => 'textarea'
		);
		
		$options[] = array(
			'name' => esc_html__('Whitelist answers.','wpqa'),
			'desc' => esc_html__('Add here the whitelist answers, Any answers here will not move to trash or draft.','wpqa'),
			'id'   => 'whitelist_answers',
			'type' => 'textarea'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate poll for user only?','wpqa'),
			'desc' => esc_html__('Select ON if you want to allow poll to users only.','wpqa'),
			'id'   => 'poll_user_only',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Activate the vote in the site?','wpqa'),
			'desc' => esc_html__('The vote for questions and answers in the site enable or disable.','wpqa'),
			'id'   => 'active_vote',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_vote:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Activate the vote in the site for the "unlogged users"?','wpqa'),
			'desc' => esc_html__('The vote for questions and answers in the site for the "unlogged users" enable or disable.','wpqa'),
			'id'   => 'active_vote_unlogged',
			'std'  => "on",
			'type' => 'checkbox',
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose style of the question vote','wpqa'),
			'desc'    => esc_html__('Choose the style of the question vote.','wpqa'),
			'id'      => 'vote_style',
			'options' => array("inside" => esc_html__("Inside box content","wpqa"),"outside" => esc_html__("Outside box content","wpqa")),
			'std'     => (has_discy()?'inside':'outside'),
			'type'    => 'radio'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the separator for the numbers at the site?','wpqa'),
			'id'   => 'active_separator',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'      => esc_html__('Number separator','wpqa'),
			'desc'      => esc_html__('Add your number separator.','wpqa'),
			'id'        => 'number_separator',
			'std'       => ',',
			'type'      => 'text',
			'condition' => 'active_separator:not(0)'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Question slugs','wpqa'),
			'id'   => 'question_slug',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Questions archive slug','wpqa'),
			'desc' => esc_html__('Add your questions archive slug.','wpqa'),
			'id'   => 'archive_question_slug',
			'std'  => 'questions',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Click ON, if you need to remove the question slug and choose "Post name" from WordPress Settings/Permalinks.','wpqa'),
			'id'   => 'remove_question_slug',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Click ON, if you need to make the question slug with number instant of title and choose "Post name" from WordPress Settings/Permalinks.','wpqa'),
			'id'   => 'question_slug_numbers',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Question slug','wpqa'),
			'desc'      => esc_html__('Add your question slug.','wpqa'),
			'id'        => 'question_slug',
			'std'       => wpqa_questions_type,
			'condition' => 'remove_question_slug:not(on)',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Question category slug','wpqa'),
			'desc' => esc_html__('Add your question category slug.','wpqa'),
			'id'   => 'category_question_slug',
			'std'  => wpqa_question_categories,
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Question tag slug','wpqa'),
			'desc' => esc_html__('Add your question tag slug.','wpqa'),
			'id'   => 'tag_question_slug',
			'std'  => 'question-tag',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'add_edit_delete',
			'name' => esc_html__('Add, edit and delete question','wpqa')
		);
		
		$options[] = array(
			'name' => esc_html__('Any one can ask question without register','wpqa'),
			'desc' => esc_html__('Any one can ask question without register enable or disable.','wpqa'),
			'id'   => 'ask_question_no_register',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Charge points for question settings','wpqa'),
			'desc' => esc_html__('Select ON if you want to charge points from users for asking questions.','wpqa'),
			'id'   => 'question_points_active',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'question_points_active:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Charge points for questions','wpqa'),
			'desc' => esc_html__("How many points should be taken from the user's account for asking questions.","wpqa"),
			'id'   => 'question_points',
			'std'  => '5',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Point back to the user when they select the best answer','wpqa'),
			'id'   => 'point_back',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Or type here the point the user should get back','wpqa'),
			'desc'      => esc_html__('Or type here the point user should get back. Type 0 to return all the points.','wpqa'),
			'id'        => 'point_back_number',
			'condition' => 'point_back:not(0)',
			'std'       => '0',
			'type'      => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose question status for users only','wpqa'),
			'desc'    => esc_html__('Choose question status after the user publishes the question.','wpqa'),
			'id'      => 'question_publish',
			'options' => array("publish" => esc_html__("Auto publish","wpqa"),"draft" => esc_html__("Need a review","wpqa")),
			'std'     => 'publish',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name'      => esc_html__('Choose question status for "unlogged user" only','wpqa'),
			'desc'      => esc_html__('Choose question status after "unlogged user" publish the question.','wpqa'),
			'id'        => 'question_publish_unlogged',
			'options'   => array("publish" => esc_html__("Auto publish","wpqa"),"draft" => esc_html__("Need a review","wpqa")),
			'std'       => 'draft',
			'type'      => 'radio',
			'condition' => 'ask_question_no_register:not(0)',
		);
		
		$options[] = array(
			'name'      => esc_html__('Send mail when the question needs a review','wpqa'),
			'desc'      => esc_html__('Mail for questions review enable or disable.','wpqa'),
			'id'        => 'send_email_draft_questions',
			'std'       => 'on',
			'operator'  => 'or',
			'condition' => 'question_publish:not(publish),question_publish_unlogged:not(publish)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Auto approve for the users who have a previously approved question.','wpqa'),
			'id'        => 'approved_questions',
			'condition' => 'question_publish:not(publish)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Send schedule mails for the users as a list with recent questions','wpqa'),
			'id'   => 'question_schedules',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'question_schedules:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Schedule mails time','wpqa'),
			'id'      => 'question_schedules_time',
			'type'    => 'multicheck',
			'std'     => array("daily" => "daily","weekly" => "weekly","monthly" => "monthly"),
			'options' => array(
				"daily" => esc_html__("Daily","wpqa"),
				"weekly" => esc_html__("Weekly","wpqa"),
				"monthly" => esc_html__("Monthly","wpqa")
			)
		);

		$options[] = array(
			"name" => esc_html__("Set the hour to send the mail at this hour","wpqa"),
			"id"   => "schedules_time_hour",
			"type" => "sliderui",
			'std'  => 12,
			"step" => "1",
			"min"  => "1",
			"max"  => "24"
		);

		$options[] = array(
			'name'    => esc_html__('Select the day to send the mail at this day','wpqa'),
			'id'      => 'schedules_time_day',
			'type'    => "select",
			'std'     => "saturday",
			'options' => array(
				'saturday'  => esc_html__('Saturday','wpqa'),
				'sunday'    => esc_html__('Sunday','wpqa'),
				'monday'    => esc_html__('Monday','wpqa'),
				'tuesday'   => esc_html__('Tuesday','wpqa'),
				'wednesday' => esc_html__('Wednesday','wpqa'),
				'thursday'  => esc_html__('Thursday','wpqa'),
				'friday'    => esc_html__('Friday','wpqa')
			)
		);
		
		$options[] = array(
			'name'    => esc_html__('Send schedule mails for custom roles to send a list with recent questions','wpqa'),
			'id'      => 'question_schedules_groups',
			'type'    => 'multicheck',
			'std'     => array("editor" => "editor","administrator" => "administrator","author" => "author","contributor" => "contributor","subscriber" => "subscriber"),
			'options' => $wpqa_options_roles
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name'    => esc_html__("Choose the way of the sending emails and notifications","wpqa"),
			'desc'    => esc_html__("Make the send mail to the users and send notification from the site or with schedule cron job","wpqa"),
			'id'      => 'way_sending_notifications_questions',
			'std'     => 'site',
			'type'    => 'radio',
			'options' => array(
				"site"    => esc_html__("From the site","wpqa"),
				"cronjob" => esc_html__("Schedule cron job","wpqa")
			)
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'way_sending_notifications_questions:is(cronjob)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Schedule mails time','wpqa'),
			'id'      => 'schedules_time_notification_questions',
			'type'    => 'radio',
			'std'     => "hourly",
			'options' => array(
				"daily"       => esc_html__("One time daily","wpqa"),
				"twicedaily"  => esc_html__("Twice times daily","wpqa"),
				"hourly"      => esc_html__("Hourly","wpqa"),
				"twicehourly" => esc_html__("Each 30 minutes","wpqa")
			)
		);

		$options[] = array(
			"name"      => esc_html__("Set the hour to send the mail at this hour","wpqa"),
			"id"        => "schedules_time_hour_notification_question",
			'condition' => 'schedules_time_notification_questions:is(daily),schedules_time_notification_questions:is(twicedaily)',
			'operator'  => 'or',
			"type"      => "sliderui",
			'std'       => 12,
			"step"      => "1",
			"min"       => "1",
			"max"       => "24"
		);

		$options[] = array(
			"name" => esc_html__("Set the number of the questions you want to send on the one time","wpqa"),
			"id"   => "schedules_number_question",
			"type" => "sliderui",
			'std'  => 10,
			"step" => "1",
			"min"  => "1",
			"max"  => "100"
		);
		
		$options[] = array(
			'name'      => esc_html__('Note: if you choose one time daily or twice times daily we will make it visible hourly to check if there are any more questions that need to send the notifications.','wpqa'),
			'condition' => 'schedules_time_notification_questions:is(daily),schedules_time_notification_questions:is(twicedaily)',
			'operator'  => 'or',
			'type'      => 'info'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose the way of the sending mails and notifications of a new question','wpqa'),
			'id'      => 'send_email_and_notification_question',
			'options' => array(
				'both'       => esc_html__('Both with the same options','wpqa'),
				'separately' => esc_html__('Separately','wpqa'),
			),
			'std'     => 'both',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'send_email_and_notification_question:is(both)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Send mail and notification to the users about the notification of a new question','wpqa'),
			'desc' => esc_html__('Send mail and notification enable or disable.','wpqa'),
			'id'   => 'send_email_new_question_both',
			'type' => 'checkbox'
		);

		$send_notification_question_groups = wpqa_options("send_notification_question_groups");
		$send_notification_question_groups = (is_array($send_notification_question_groups) && !empty($send_notification_question_groups)?$send_notification_question_groups:wpqa_options("send_email_question_groups"));
		
		$options[] = array(
			'name'      => esc_html__('Send mail and notification for custom roles about the notification of a new question','wpqa'),
			'id'        => 'send_email_question_groups_both',
			'type'      => 'multicheck',
			'condition' => 'send_email_new_question_both:not(0)',
			'std'       => $send_notification_question_groups,
			'options'   => $wpqa_options_roles
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'send_email_and_notification_question:is(separately)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Send mail to the users about the notification of a new question','wpqa'),
			'desc' => esc_html__('Send mail enable or disable.','wpqa'),
			'id'   => 'send_email_new_question',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Send mail for custom roles about the notification of a new question','wpqa'),
			'id'        => 'send_email_question_groups',
			'type'      => 'multicheck',
			'condition' => 'send_email_new_question:not(0)',
			'std'       => array("editor" => "editor","administrator" => "administrator","author" => "author","contributor" => "contributor","subscriber" => "subscriber"),
			'options'   => $wpqa_options_roles
		);
		
		$options[] = array(
			'name' => esc_html__('Send notification to the users about the notification of a new question','wpqa'),
			'desc' => esc_html__('Send notification enable or disable.','wpqa'),
			'id'   => 'send_notification_new_question',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Send notification for custom roles about the notification of a new question','wpqa'),
			'id'        => 'send_notification_question_groups',
			'type'      => 'multicheck',
			'condition' => 'send_notification_new_question:not(0)',
			'std'       => $send_notification_question_groups,
			'options'   => $wpqa_options_roles
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'way_sending_notifications_questions:is(cronjob)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('The link of the notification after sending it to users will go to?','wpqa'),
			'id'      => 'notifications_questions_link',
			'std'     => "home",
			'type'    => 'select',
			'options' => array("home" => esc_html__("Home","wpqa"),"custom_page" => esc_html__("Custom page","wpqa"),"custom_link" => esc_html__("Custom link","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__("Type the link if you don't like above","wpqa"),
			'id'        => 'notifications_questions_custom_page',
			'condition' => 'notifications_questions_link:is(custom_page)',
			'type'      => 'select',
			'options'   => $options_pages
		);
		
		$options[] = array(
			'name'      => esc_html__("Type the link if you don't like above","wpqa"),
			'id'        => 'notifications_questions_custom_link',
			'condition' => 'notifications_questions_link:is(custom_link)',
			'type'      => 'text'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Ask questions','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Make the ask question form works with popup','wpqa'),
			'desc' => esc_html__('Select ON if you want to make the ask question form works with popup.','wpqa'),
			'id'   => 'ask_question_popup',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Ask question slug','wpqa'),
			'desc' => esc_html__('Put the ask question slug.','wpqa'),
			'id'   => 'add_questions_slug',
			'std'  => 'add-question',
			'type' => 'text'
		
		);

		$options[] = array(
			'name' => '<a href="'.wpqa_add_question_permalink().'" target="_blank">'.esc_html__('Link For The Ask Question Page.','wpqa').'</a>',
			'type' => 'info'
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_after_question_link',$options);
		
		$ask_question_items = array(
			"title_question"       => array("sort" => esc_html__('Question Title','wpqa'),"value" => "title_question"),
			"categories_question"  => array("sort" => esc_html__('Question Categories','wpqa'),"value" => "categories_question"),
			"tags_question"        => array("sort" => esc_html__('Question Tags','wpqa'),"value" => "tags_question"),
			"poll_question"        => array("sort" => esc_html__('Question Poll','wpqa'),"value" => "poll_question"),
			"attachment_question"  => array("sort" => esc_html__('Question Attachment','wpqa'),"value" => "attachment_question"),
			"featured_image"       => array("sort" => esc_html__('Featured image','wpqa'),"value" => "featured_image"),
			"comment_question"     => array("sort" => esc_html__('Question content','wpqa'),"value" => "comment_question"),
			"anonymously_question" => array("sort" => esc_html__('Ask Anonymously','wpqa'),"value" => "anonymously_question"),
			"video_desc_active"    => array("sort" => esc_html__('Video Description','wpqa'),"value" => "video_desc_active"),
			"private_question"     => array("sort" => esc_html__('Private Question','wpqa'),"value" => "private_question"),
			"remember_answer"      => array("sort" => esc_html__('Remember Answer','wpqa'),"value" => "remember_answer"),
			"terms_active"         => array("sort" => esc_html__('Terms of Service and Privacy Policy','wpqa'),"value" => "terms_active"),
		);
		
		$ask_question_items_std = $ask_question_items;
		unset($ask_question_items_std["attachment_question"]);
		
		$options[] = array(
			'name'    => esc_html__("Select what to show at ask question form","wpqa"),
			'id'      => 'ask_question_items',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $ask_question_items_std,
			'options' => $ask_question_items
		);
		
		$options[] = array(
			'name'      => esc_html__('Activate suggested questions in the title when user is typing the question','wpqa'),
			'id'        => 'suggest_questions',
			'condition' => 'ask_question_items:has(title_question)',
			'type'      => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_question_items:has_not(title_question)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Excerpt type for title from the content','wpqa'),
			'desc'    => esc_html__('Choose form here the excerpt type.','wpqa'),
			'id'      => 'title_excerpt_type',
			'type'    => "select",
			'options' => array(
				'words'      => esc_html__('Words','wpqa'),
				'characters' => esc_html__('Characters','wpqa')
			)
		);

		$options[] = array(
			'name' => esc_html__('Excerpt title from the content','wpqa'),
			'desc' => esc_html__('Put here the excerpt title from the content.','wpqa'),
			'id'   => 'title_excerpt',
			'std'  => 10,
			'type' => 'text'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);

		$options[] = array(
			'name'    => esc_html__('Select the checked by default options at ask a new question','wpqa'),
			'id'      => 'add_question_default',
			'type'    => 'multicheck',
			'std'     => array(
				"notified" => "notified",
			),
			'options' => array(
				"poll"            => esc_html__('Poll','wpqa'),
				"multicheck_poll" => esc_html__('Multicheck Poll','wpqa'),
				"poll_image"      => esc_html__('Poll with image','wpqa'),
				"video"           => esc_html__('Video','wpqa'),
				"notified"        => esc_html__('Notified','wpqa'),
				"private"         => esc_html__("Private question","wpqa"),
				"anonymously"     => esc_html__("Ask anonymously","wpqa"),
				"terms"           => esc_html__("Terms","wpqa"),
				"sticky"          => esc_html__("Sticky","wpqa"),
			)
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_question_items:has(categories_question)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Category setting','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Make the category field required or not','wpqa'),
			'id'   => 'question_category_required',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__("Category at ask question form single, multi, ajax 1 or ajax 2","wpqa"),
			'desc'    => esc_html__("Choose how category is shown at ask question form single, multi or ajax","wpqa"),
			'id'      => 'category_single_multi',
			'std'     => 'single',
			'type'    => 'radio',
			'options' => array(
				"single" => "Single",
				"multi"  => "Multi",
				"ajax"   => "Ajax 1",
				"ajax_2" => "Ajax 2"
			)
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_question_items:has(poll_question)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Poll setting','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Choose from the Users Settings/Permissions which role you want to allow them to poll','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the poll only for the questions by default','wpqa'),
			'id'   => 'poll_only',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the multicheck on the poll','wpqa'),
			'id'   => 'multicheck_poll',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate image in the poll','wpqa'),
			'id'   => 'poll_image',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'poll_image:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the title in the poll images','wpqa'),
			'id'   => 'poll_image_title',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Make the title in the poll images required','wpqa'),
			'id'        => 'poll_image_title_required',
			'condition' => 'poll_image_title:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_question_items:has(comment_question)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Question content setting','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Details in ask question form is required','wpqa'),
			'id'   => 'comment_question',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable or disable the editor for details in ask question form','wpqa'),
			'id'   => 'editor_question_details',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_question_items:has(terms_active)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Terms of Service and Privacy Policy','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'terms_active_target',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'    => esc_html__('Terms page','wpqa'),
			'desc'    => esc_html__('Select the terms page','wpqa'),
			'id'      => 'terms_page',
			'type'    => 'select',
			'options' => $options_pages
		);
		
		$options[] = array(
			'name' => esc_html__("Type the terms link if you don't like a page","wpqa"),
			'id'   => 'terms_link',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate Privacy Policy','wpqa'),
			'desc' => esc_html__('Select ON if you want to activate Privacy Policy.','wpqa'),
			'id'   => 'privacy_policy',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'privacy_policy:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'privacy_active_target',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'    => esc_html__('Privacy Policy page','wpqa'),
			'desc'    => esc_html__('Select the privacy policy page','wpqa'),
			'id'      => 'privacy_page',
			'type'    => 'select',
			'options' => $options_pages
		);
		
		$options[] = array(
			'name' => esc_html__("Type the privacy policy link if you don't like a page","wpqa"),
			'id'   => 'privacy_link',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_question_items:has(title_question)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Limitations for title','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Add minimum limit for the number of letters for the question title, like 15, 20, if you leave it empty, it will be not important','wpqa'),
			'id'   => 'question_title_min_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Add limit for the number of letters for the question title, like 140, 200, if you leave it empty, it will be unlimited','wpqa'),
			'id'   => 'question_title_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_question_items:has(tags_question)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Limitations for tags','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Add minimum limit for the number of letters for the question tag word, like 15, 20, if you leave it empty it will be not important','wpqa'),
			'id'   => 'question_tags_min_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Add word limit for the number of letters for the question tag, like 140, 200, if you leave it empty will be unlimited','wpqa'),
			'id'   => 'question_tags_limit',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Add minimum limit for the number of items for the question tags, like 2, 4, if you leave it empty will be not important','wpqa'),
			'id'   => 'question_tags_number_min_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Add limit for the number of items for the question tags, like 4, 6, if you leave it empty it will be unlimited','wpqa'),
			'id'   => 'question_tags_number_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_question_items:has(poll_question)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Limitations for poll','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Add minimum limit for the number of letters for the question poll title, like 140, 200, if you leave it empty it will be unlimited','wpqa'),
			'id'   => 'question_poll_min_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Add maximum limit for the number of letters for the question poll title, like 140, 200, if you leave it empty it will be unlimited','wpqa'),
			'id'   => 'question_poll_limit',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Add minimum limit for the number of items for the question poll title, like 2, 4, if you leave it empty it will be not important','wpqa'),
			'id'   => 'question_poll_number_min_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Add limit for the number of items for the question poll title, like 4, 6, if you leave it empty it will be unlimited','wpqa'),
			'id'   => 'question_poll_number_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_question_items:has(comment_question)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Limitations for content','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Add minimum limit for the number of letters for the question content, like 15, 20, if you leave it empty it will be not important','wpqa'),
			'id'   => 'question_content_min_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Add limit for the number of letters for the question content, like 140, 200, if you leave it empty it will be unlimited','wpqa'),
			'id'   => 'question_content_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Edit questions','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate user can edit the questions','wpqa'),
			'desc' => esc_html__('Select ON if you want the user to be able to edit the questions.','wpqa'),
			'id'   => 'question_edit',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'question_edit:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Edit question slug','wpqa'),
			'desc' => esc_html__('Put the edit question slug.','wpqa'),
			'id'   => 'edit_questions_slug',
			'std'  => 'edit-question',
			'type' => 'text'
		);
		
		$options[] = array(
			'name'    => esc_html__('After editing auto approve question or need to be approved again?','wpqa'),
			'desc'    => esc_html__('Press ON to auto approve','wpqa'),
			'id'      => 'question_approved',
			'options' => array("publish" => esc_html__("Auto publish","wpqa"),"draft" => esc_html__("Need a review","wpqa")),
			'std'     => 'publish',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name' => esc_html__('After the question is edited change the URL from the title?','wpqa'),
			'desc' => esc_html__('Press ON to edit the URL','wpqa'),
			'id'   => 'change_question_url',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Delete questions','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate user can delete the questions','wpqa'),
			'desc' => esc_html__('Select ON if you want the user to be able to delete the questions.','wpqa'),
			'id'   => 'question_delete',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('When the users delete the question send to the trash or delete it forever?','wpqa'),
			'id'        => 'delete_question',
			'std'       => 'delete',
			'condition' => 'question_delete:not(0)',
			'type'      => 'radio',
			'options'   => array(
				'delete' => esc_html__('Delete','wpqa'),
				'trash'  => esc_html__('Trash','wpqa'),
			)
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Question meta settings','wpqa'),
			'id'   => 'question_meta',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON if you want to activate the vote with meta.','wpqa'),
			'id'   => 'question_meta_vote',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON if you want icons only at the question meta.','wpqa'),
			'id'   => 'question_meta_icon',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Select the meta options','wpqa'),
			'id'      => 'question_meta',
			'type'    => 'multicheck',
			'std'     => array(
				"author_by"         => "author_by",
				"question_date"     => "question_date",
				"asked_to"          => "asked_to",
				"category_question" => "category_question",
				"question_answer"   => "question_answer",
				"question_views"    => "question_views",
				"bump_meta"         => "bump_meta",
			),
			'options' => array(
				"author_by"         => esc_html__('Author by','wpqa'),
				"question_date"     => esc_html__('Date meta','wpqa'),
				"asked_to"          => esc_html__('Asked to meta','wpqa'),
				"category_question" => esc_html__('Category question','wpqa'),
				"question_answer"   => esc_html__('Answer meta','wpqa'),
				"question_views"    => esc_html__('Views stats','wpqa'),
				"bump_meta"         => esc_html__('Bump question meta','wpqa'),
			)
		);
		
		$options[] = array(
			'name' => esc_html__('Activate user can add the question to favorites','wpqa'),
			'desc' => esc_html__('Select ON if you want the user can add the questions to favorites.','wpqa'),
			'id'   => 'question_favorite',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate user can follow the questions','wpqa'),
			'desc' => esc_html__('Select ON if you want the user can follow the questions.','wpqa'),
			'id'   => 'question_follow',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the follow button at questions loop','wpqa'),
			'desc' => esc_html__('Select ON if you want to activate the follow button at questions loop.','wpqa'),
			'id'   => 'question_follow_loop',
			'type' => 'checkbox'
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_after_question_follow_loop',$options);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Questions category settings','wpqa'),
			'id'   => 'question_category',
			'type' => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Category description enable or disable','wpqa'),
			'desc' => esc_html__('Select ON to enable the category description in the category page.','wpqa'),
			'id'   => 'question_category_description',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'      => esc_html__('Category rss enable or disable','wpqa'),
			'desc'      => esc_html__('Select ON to enable the category rss in the category page.','wpqa'),
			'id'        => 'question_category_rss',
			'std'       => 'on',
			'condition' => 'question_category_description:not(0)',
			'type'      => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Activate the points by category?','wpqa'),
			'desc' => esc_html__('The points for categories enable or disable.','wpqa'),
			'id'   => 'active_points_category',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Activate the follow for categories and tags?','wpqa'),
			'desc' => esc_html__('Follow for categories and tags enable or disable.','wpqa'),
			'id'   => 'follow_category',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		if (has_discy()) {
			$cat_style_pages = array(
				"with_icon"     => esc_html__("With icon","wpqa"),
				"icon_color"    => esc_html__("With icon and colors","wpqa"),
				'with_icon_1'   => esc_html__('With icon 2','wpqa'),
				'with_icon_2'   => esc_html__('With colored icon','wpqa'),
				'with_icon_3'   => esc_html__('With colored icon and box','wpqa'),
				'with_icon_4'   => esc_html__('With colored icon and box 2','wpqa'),
				'with_cover_1'  => esc_html__('With cover','wpqa'),
				'with_cover_2'  => esc_html__('With cover and icon','wpqa'),
				'with_cover_3'  => esc_html__('With cover and small icon','wpqa'),
				'with_cover_4'  => esc_html__('With big cover','wpqa'),
				'with_cover_5'  => esc_html__('With big cover and icon','wpqa'),
				'with_cover_6'  => esc_html__('With big cover and small icon','wpqa'),
				'simple_follow' => esc_html__('Simple with follow','wpqa'),
				'simple'        => esc_html__('Simple','wpqa'),
			);
		}else {
			$cat_style_pages = array(
				"with_icon"     => esc_html__("With icon","wpqa"),
				"icon_color"    => esc_html__("With icon and colors","wpqa"),
				'with_icon_1'   => esc_html__('With icon 2','wpqa'),
				'with_icon_2'   => esc_html__('With colored icon','wpqa'),
				'with_icon_3'   => esc_html__('With colored icon and box','wpqa'),
				'with_icon_4'   => esc_html__('With colored icon and box 2','wpqa'),
				'with_cover_1'  => esc_html__('With cover','wpqa'),
				'with_cover_3'  => esc_html__('With cover and small icon','wpqa'),
				'with_cover_4'  => esc_html__('With big cover','wpqa'),
				'with_cover_6'  => esc_html__('With big cover and small icon','wpqa'),
				'simple_follow' => esc_html__('Simple with follow','wpqa'),
				'simple'        => esc_html__('Simple','wpqa'),
			);
		}
		
		$options[] = array(
			'name'    => esc_html__('Categories style at home and search pages','wpqa'),
			'desc'    => esc_html__('Choose the categories style.','wpqa'),
			'id'      => 'cat_style_pages',
			'options' => $cat_style_pages,
			'std'     => 'simple_follow',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name' => esc_html__('Request a new category','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Activate the users to request a new category','wpqa'),
			'id'   => 'allow_user_to_add_category',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'allow_user_to_add_category:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Activate the unlogged users to request a new category.','wpqa'),
			'id'   => 'add_category_no_register',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Add category slug','wpqa'),
			'desc' => esc_html__('Put the add category slug.','wpqa'),
			'id'   => 'add_category_slug',
			'std'  => 'add-category',
			'type' => 'text'
		);

		$options[] = array(
			'name' => '<a href="'.wpqa_add_category_permalink().'" target="_blank">'.esc_html__('The Link For The Add Category Page.','wpqa').'</a>',
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Send mail when the category needs a review','wpqa'),
			'desc' => esc_html__('Mail for category review enable or disable.','wpqa'),
			'id'   => 'send_email_add_category',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Category cover','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Activate the cover for categories?','wpqa'),
			'desc' => esc_html__('Cover for categories enable or disable.','wpqa'),
			'id'   => 'active_cover_category',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_cover_category:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Cover full width or fixed','wpqa'),
			'desc'    => esc_html__('Choose the cover to make it work with full width or fixed.','wpqa'),
			'id'      => 'cover_category_fixed',
			'options' => array(
				'normal' => esc_html__('Full width','wpqa'),
				'fixed'  => esc_html__('Fixed','wpqa'),
			),
			'std'     => 'normal',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name'    => esc_html__('Select the share options','wpqa'),
			'id'      => 'cat_share',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $share_array,
			'options' => $share_array
		);

		$options[] = array(
			'name' => esc_html__('Default cover enable or disable.','wpqa'),
			'desc' => esc_html__("Select ON to upload your default cover for the categories which doesn't have cover.","wpqa"),
			'id'   => 'default_cover_cat_active',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Upload default cover for the categories.','wpqa'),
			'id'        => 'default_cover_cat',
			'condition' => 'default_cover_cat_active:not(0)',
			'type'      => 'upload'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Category tabs','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Activate the tabs for questions categories?','wpqa'),
			'desc' => esc_html__('The tabs for questions categories enable or disable.','wpqa'),
			'id'   => 'tabs_category',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'tabs_category:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Put here the exclude categories ids','wpqa'),
			'id'   => 'exclude_categories',
			'type' => 'text'
		);

		$category_tabs = array(
			"recent-questions"   => array("sort" => esc_html__('Recent Questions','wpqa'),"value" => "recent-questions"),
			"most-answers"       => array("sort" => esc_html__('Most Answered','wpqa'),"value" => "most-answers"),
			"answers"            => array("sort" => esc_html__('Answers','wpqa'),"value" => "answers"),
			"no-answers"         => array("sort" => esc_html__('No Answers','wpqa'),"value" => "no-answers"),
			"most-visit"         => array("sort" => esc_html__('Most Visited','wpqa'),"value" => "most-visit"),
			"most-vote"          => array("sort" => esc_html__('Most Voted','wpqa'),"value" => "most-vote"),
			"random"             => array("sort" => esc_html__('Random Questions','wpqa'),"value" => "random"),
			"question-bump"      => array("sort" => esc_html__('Bump Question','wpqa'),"value" => ""),
			"new-questions"      => array("sort" => esc_html__('New Questions','wpqa'),"value" => ""),
			"sticky-questions"   => array("sort" => esc_html__('Sticky Questions','wpqa'),"value" => ""),
			"polls"              => array("sort" => esc_html__('Poll Questions','wpqa'),"value" => ""),
			"followed"           => array("sort" => esc_html__('Followed Questions','wpqa'),"value" => ""),
			"favorites"          => array("sort" => esc_html__('Favorite Questions','wpqa'),"value" => ""),
			
			"recent-questions-2" => array("sort" => esc_html__('Recent Questions With Time','wpqa'),"value" => ""),
			"most-answers-2"     => array("sort" => esc_html__('Most Answered With Time','wpqa'),"value" => ""),
			"answers-2"          => array("sort" => esc_html__('Answers With Time','wpqa'),"value" => ""),
			"no-answers-2"       => array("sort" => esc_html__('No Answers With Time','wpqa'),"value" => ""),
			"most-visit-2"       => array("sort" => esc_html__('Most Visited With Time','wpqa'),"value" => ""),
			"most-vote-2"        => array("sort" => esc_html__('Most Voted With Time','wpqa'),"value" => ""),
			"random-2"           => array("sort" => esc_html__('Random Questions With Time','wpqa'),"value" => ""),
			"question-bump-2"    => array("sort" => esc_html__('Bump Question With Time','wpqa'),"value" => ""),
			"new-questions-2"    => array("sort" => esc_html__('New Questions With Time','wpqa'),"value" => ""),
			"sticky-questions-2" => array("sort" => esc_html__('Sticky Questions With Time','wpqa'),"value" => ""),
			"polls-2"            => array("sort" => esc_html__('Poll Questions With Time','wpqa'),"value" => ""),
			"followed-2"         => array("sort" => esc_html__('Followed Questions With Time','wpqa'),"value" => ""),
			"favorites-2"        => array("sort" => esc_html__('Favorite Questions With Time','wpqa'),"value" => ""),
		);

		if (has_himer() || has_knowly() || has_questy()) {
			$category_tabs['most-reacted'] = array("sort" => esc_html__('Most Reacted','wpqa'),"value" => "");

			$category_tabs['most-reacted-2'] = array("sort" => esc_html__('Most Reacted with time','wpqa'),"value" => "");
		}

		$options[] = array(
			'name'    => esc_html__('Select the tabs you want to show','wpqa'),
			'id'      => 'category_tabs',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $category_tabs,
			'options' => $category_tabs
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'category_tabs:has(recent-questions-2),category_tabs:has(most-answers-2),category_tabs:has(question-bump-2),category_tabs:has(new-questions-2),category_tabs:has(sticky-questions-2),category_tabs:has(polls-2),category_tabs:has(followed-2),category_tabs:has(favorites-2),category_tabs:has(answers-2),category_tabs:has(most-visit-2),category_tabs:has(most-vote-2),category_tabs:has(random-2),category_tabs:has(no-answers-2)'.(has_himer() || has_knowly() || has_questy()?',category_tabs:has(most-reacted-2)':''),
			'operator'  => 'or',
			'type'      => 'heading-2'
		);

		$orderby_answers = array(
			'recent' => esc_html__('Recent','wpqa'),
			'oldest' => esc_html__('Oldest','wpqa'),
			'votes'  => esc_html__('Voted','wpqa'),
		);

		if (has_himer() || has_knowly() || has_questy()) {
			$orderby_answers['reacted'] = esc_html__('Reacted','wpqa');
		}

		$options[] = array(
			'name'      => esc_html__('Order by','wpqa'),
			'desc'      => esc_html__('Select the answers order by.','wpqa'),
			'id'        => "orderby_answers",
			'std'       => "recent",
			'condition' => 'category_tabs:has(answers)',
			'type'      => "radio",
			'options'   => $orderby_answers
		);

		$options[] = array(
			'type' => 'info',
			'name' => esc_html__('Time frame for the tabs','wpqa')
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for recent questions tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for recent questions tab.','wpqa'),
			'id'        => "date_recent_questions",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(recent-questions-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for most answered tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for most answered tab.','wpqa'),
			'id'        => "date_most_answered",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(most-answers-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for bump question tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for bump question tab.','wpqa'),
			'id'        => "date_question_bump",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(question-bump-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for answers tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for answers tab.','wpqa'),
			'id'        => "date_answers",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(answers-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for most visited tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for most visited tab.','wpqa'),
			'id'        => "date_most_visited",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(most-visit-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for most voted tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for most voted tab.','wpqa'),
			'id'        => "date_most_voted",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(most-vote-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		if (has_himer() || has_knowly() || has_questy()) {
			$options[] = array(
				'name'      => esc_html__('Specific date for most reacted tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for most reacted tab.','wpqa'),
				'id'        => "date_most_reacted",
				'std'       => "all",
				'type'      => "radio",
				'condition' => 'category_tabs:has(most-reacted-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);
		}

		$options[] = array(
			'name'      => esc_html__('Specific date for no answers tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for no answers tab.','wpqa'),
			'id'        => "date_no_answers",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(no-answers-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for random questions tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for random questions tab.','wpqa'),
			'id'        => "date_random_questions",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(random-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for new questions tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for new questions tab.','wpqa'),
			'id'        => "date_new_questions",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(new-questions-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for sticky questions tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for sticky questions tab.','wpqa'),
			'id'        => "date_sticky_questions",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(sticky-questions-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for poll questions tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for poll questions tab.','wpqa'),
			'id'        => "date_poll_questions",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(polls-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for followed questions tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for followed questions tab.','wpqa'),
			'id'        => "date_followed_questions",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(followed-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for favorite questions tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for favorite questions tab.','wpqa'),
			'id'        => "date_favorites_questions",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'category_tabs:has(favorites-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'category_tabs:has(recent-questions),category_tabs:has(most-answers),category_tabs:has(question-bump),category_tabs:has(new-questions),category_tabs:has(sticky-questions),category_tabs:has(polls),category_tabs:has(followed),category_tabs:has(favorites),category_tabs:has(answers),category_tabs:has(most-visit),category_tabs:has(most-vote),category_tabs:has(random),category_tabs:has(no-answers),category_tabs:has(recent-questions-2),category_tabs:has(most-answers-2),category_tabs:has(question-bump-2),category_tabs:has(new-questions-2),category_tabs:has(sticky-questions-2),category_tabs:has(polls-2),category_tabs:has(followed-2),category_tabs:has(favorites-2),category_tabs:has(answers-2),category_tabs:has(most-visit-2),category_tabs:has(most-vote-2),category_tabs:has(random-2),category_tabs:has(no-answers-2)'.(has_himer() || has_knowly() || has_questy()?',category_tabs:has(most-reacted),category_tabs:has(most-reacted-2)':''),
			'operator'  => 'or',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'type' => 'info',
			'name' => esc_html__('Custom setting for the slugs','wpqa')
		);

		$options[] = array(
			'name'      => esc_html__('Recent questions slug','wpqa'),
			'id'        => 'recent_questions_slug',
			'std'       => 'recent-questions',
			'condition' => 'category_tabs:has(recent-questions)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most answered slug','wpqa'),
			'id'        => 'most_answers_slug',
			'std'       => 'most-answered',
			'condition' => 'category_tabs:has(most-answers)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Bump question slug','wpqa'),
			'id'        => 'question_bump_slug',
			'std'       => 'question-bump',
			'condition' => 'category_tabs:has(question-bump)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('New questions slug','wpqa'),
			'id'        => 'question_new_slug',
			'std'       => 'new',
			'condition' => 'category_tabs:has(new-questions)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question sticky slug','wpqa'),
			'id'        => 'question_sticky_slug',
			'std'       => 'sticky',
			'condition' => 'category_tabs:has(sticky-questions)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question polls slug','wpqa'),
			'id'        => 'question_polls_slug',
			'std'       => 'polls',
			'condition' => 'category_tabs:has(polls)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question followed slug','wpqa'),
			'id'        => 'question_followed_slug',
			'std'       => 'followed',
			'condition' => 'category_tabs:has(followed)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question favorites slug','wpqa'),
			'id'        => 'question_favorites_slug',
			'std'       => 'favorites',
			'condition' => 'category_tabs:has(favorites)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Answers slug','wpqa'),
			'id'        => 'category_answers_slug',
			'std'       => 'answers',
			'condition' => 'category_tabs:has(answers)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most visited slug','wpqa'),
			'id'        => 'most_visit_slug',
			'std'       => 'most-visited',
			'condition' => 'category_tabs:has(most-visit)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most voted slug','wpqa'),
			'id'        => 'most_vote_slug',
			'std'       => 'most-voted',
			'condition' => 'category_tabs:has(most-vote)',
			'type'      => 'text'
		);

		if (has_himer() || has_knowly() || has_questy()) {
			$options[] = array(
				'name'      => esc_html__('Most reacted slug','wpqa'),
				'id'        => 'most_reacted_slug',
				'std'       => 'most-reacted',
				'condition' => 'category_tabs:has(most-reacted)',
				'type'      => 'text'
			);
		}

		$options[] = array(
			'name'      => esc_html__('Random slug','wpqa'),
			'id'        => 'random_slug',
			'std'       => 'random',
			'condition' => 'category_tabs:has(random)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('No answers slug','wpqa'),
			'id'        => 'no_answers_slug',
			'std'       => 'no-answers',
			'condition' => 'category_tabs:has(no-answers)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Recent questions with time slug','wpqa'),
			'id'        => 'recent_questions_slug_2',
			'std'       => 'recent-questions-time',
			'condition' => 'category_tabs:has(recent-questions-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most answered with time slug','wpqa'),
			'id'        => 'most_answers_slug_2',
			'std'       => 'most-answered-time',
			'condition' => 'category_tabs:has(most-answers-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Bump question with time slug','wpqa'),
			'id'        => 'question_bump_slug_2',
			'std'       => 'question-bump-time',
			'condition' => 'category_tabs:has(question-bump-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('New questions with time slug','wpqa'),
			'id'        => 'question_new_slug_2',
			'std'       => 'new-time',
			'condition' => 'category_tabs:has(new-questions-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question sticky with time slug','wpqa'),
			'id'        => 'question_sticky_slug_2',
			'std'       => 'sticky-time',
			'condition' => 'category_tabs:has(sticky-questions-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question polls with time slug','wpqa'),
			'id'        => 'question_polls_slug_2',
			'std'       => 'polls-time',
			'condition' => 'category_tabs:has(polls-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question followed with time slug','wpqa'),
			'id'        => 'question_followed_slug_2',
			'std'       => 'followed-time',
			'condition' => 'category_tabs:has(followed-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question favorites with time slug','wpqa'),
			'id'        => 'question_favorites_slug_2',
			'std'       => 'favorites-time',
			'condition' => 'category_tabs:has(favorites-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Answers with time slug','wpqa'),
			'id'        => 'answers_slug_2',
			'std'       => 'answers-time',
			'condition' => 'category_tabs:has(answers-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most visited with time slug','wpqa'),
			'id'        => 'most_visit_slug_2',
			'std'       => 'most-visited-time',
			'condition' => 'category_tabs:has(most-visit-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most voted with time slug','wpqa'),
			'id'        => 'most_vote_slug_2',
			'std'       => 'most-voted-time',
			'condition' => 'category_tabs:has(most-vote-2)',
			'type'      => 'text'
		);

		if (has_himer() || has_knowly() || has_questy()) {
			$options[] = array(
				'name'      => esc_html__('Most reacted with time slug','wpqa'),
				'id'        => 'most_reacted_slug_2',
				'std'       => 'most-reacted-time',
				'condition' => 'category_tabs:has(most-reacted-2)',
				'type'      => 'text'
			);
		}

		$options[] = array(
			'name'      => esc_html__('Random with time slug','wpqa'),
			'id'        => 'random_slug_2',
			'std'       => 'random-time',
			'condition' => 'category_tabs:has(random-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('No answers with time slug','wpqa'),
			'id'        => 'no_answers_slug_2',
			'std'       => 'no-answers-time',
			'condition' => 'category_tabs:has(no-answers-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Questions tag settings','wpqa'),
			'id'   => 'question_tag',
			'type' => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Tag description enable or disable','wpqa'),
			'desc' => esc_html__('Select ON to enable the tag description in the tag page.','wpqa'),
			'id'   => 'question_tag_description',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Tag rss enable or disable','wpqa'),
			'desc'      => esc_html__('Select ON to enable the tag rss in the tag page.','wpqa'),
			'id'        => 'question_tag_rss',
			'std'       => 'on',
			'condition' => 'question_tag_description:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Tag tabs','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Activate the tabs for questions tags?','wpqa'),
			'desc' => esc_html__('The tabs for questions tags enable or disable.','wpqa'),
			'id'   => 'tabs_tag',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'tabs_tag:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Put here the exclude tags ids','wpqa'),
			'id'   => 'exclude_tags',
			'type' => 'text'
		);

		$tag_tabs = array(
			"recent-questions"   => array("sort" => esc_html__('Recent Questions','wpqa'),"value" => "recent-questions"),
			"most-answers"       => array("sort" => esc_html__('Most Answered','wpqa'),"value" => "most-answers"),
			"answers"            => array("sort" => esc_html__('Answers','wpqa'),"value" => "answers"),
			"no-answers"         => array("sort" => esc_html__('No Answers','wpqa'),"value" => "no-answers"),
			"most-visit"         => array("sort" => esc_html__('Most Visited','wpqa'),"value" => "most-visit"),
			"most-vote"          => array("sort" => esc_html__('Most Voted','wpqa'),"value" => "most-vote"),
			"random"             => array("sort" => esc_html__('Random Questions','wpqa'),"value" => "random"),
			"question-bump"      => array("sort" => esc_html__('Bump Question','wpqa'),"value" => ""),
			"new-questions"      => array("sort" => esc_html__('New Questions','wpqa'),"value" => ""),
			"sticky-questions"   => array("sort" => esc_html__('Sticky Questions','wpqa'),"value" => ""),
			"polls"              => array("sort" => esc_html__('Poll Questions','wpqa'),"value" => ""),
			"followed"           => array("sort" => esc_html__('Followed Questions','wpqa'),"value" => ""),
			"favorites"          => array("sort" => esc_html__('Favorite Questions','wpqa'),"value" => ""),
			
			"recent-questions-2" => array("sort" => esc_html__('Recent Questions With Time','wpqa'),"value" => ""),
			"most-answers-2"     => array("sort" => esc_html__('Most Answered With Time','wpqa'),"value" => ""),
			"answers-2"          => array("sort" => esc_html__('Answers With Time','wpqa'),"value" => ""),
			"no-answers-2"       => array("sort" => esc_html__('No Answers With Time','wpqa'),"value" => ""),
			"most-visit-2"       => array("sort" => esc_html__('Most Visited With Time','wpqa'),"value" => ""),
			"most-vote-2"        => array("sort" => esc_html__('Most Voted With Time','wpqa'),"value" => ""),
			"random-2"           => array("sort" => esc_html__('Random Questions With Time','wpqa'),"value" => ""),
			"question-bump-2"    => array("sort" => esc_html__('Bump Question With Time','wpqa'),"value" => ""),
			"new-questions-2"    => array("sort" => esc_html__('New Questions With Time','wpqa'),"value" => ""),
			"sticky-questions-2" => array("sort" => esc_html__('Sticky Questions With Time','wpqa'),"value" => ""),
			"polls-2"            => array("sort" => esc_html__('Poll Questions With Time','wpqa'),"value" => ""),
			"followed-2"         => array("sort" => esc_html__('Followed Questions With Time','wpqa'),"value" => ""),
			"favorites-2"        => array("sort" => esc_html__('Favorite Questions With Time','wpqa'),"value" => ""),
		);

		if (has_himer() || has_knowly() || has_questy()) {
			$tag_tabs['most-reacted'] = array("sort" => esc_html__('Most Reacted','wpqa'),"value" => "");

			$tag_tabs['most-reacted-2'] = array("sort" => esc_html__('Most Reacted with time','wpqa'),"value" => "");
		}

		$options[] = array(
			'name'    => esc_html__('Select the tabs you want to show','wpqa'),
			'id'      => 'tag_tabs',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $tag_tabs,
			'options' => $tag_tabs
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'tag_tabs:has(recent-questions-2),tag_tabs:has(most-answers-2),tag_tabs:has(question-bump-2),tag_tabs:has(new-questions-2),tag_tabs:has(sticky-questions-2),tag_tabs:has(polls-2),tag_tabs:has(followed-2),tag_tabs:has(favorites-2),tag_tabs:has(answers-2),tag_tabs:has(most-visit-2),tag_tabs:has(most-vote-2),tag_tabs:has(random-2),tag_tabs:has(no-answers-2)'.(has_himer() || has_knowly() || has_questy()?',tag_tabs:has(most-reacted-2)':''),
			'operator'  => 'or',
			'type'      => 'heading-2'
		);

		$orderby_answers = array(
			'recent' => esc_html__('Recent','wpqa'),
			'oldest' => esc_html__('Oldest','wpqa'),
			'votes'  => esc_html__('Voted','wpqa'),
		);

		if (has_himer() || has_knowly() || has_questy()) {
			$orderby_answers['reacted'] = esc_html__('Reacted','wpqa');
		}

		$options[] = array(
			'name'      => esc_html__('Order by','wpqa'),
			'desc'      => esc_html__('Select the answers order by.','wpqa'),
			'id'        => "orderby_answers_tag",
			'std'       => "recent",
			'condition' => 'tag_tabs:has(answers)',
			'type'      => "radio",
			'options'   => $orderby_answers
		);

		$options[] = array(
			'type' => 'info',
			'name' => esc_html__('Time frame for the tabs','wpqa')
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for recent questions tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for recent questions tab.','wpqa'),
			'id'        => "date_recent_questions_tag",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'tag_tabs:has(recent-questions-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for most answered tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for most answered tab.','wpqa'),
			'id'        => "date_most_answered_tag",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'tag_tabs:has(most-answers-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for bump question tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for bump question tab.','wpqa'),
			'id'        => "date_question_bump_tag",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'tag_tabs:has(question-bump-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for answers tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for answers tab.','wpqa'),
			'id'        => "date_answers_tag",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'tag_tabs:has(answers-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for most visited tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for most visited tab.','wpqa'),
			'id'        => "date_most_visited_tag",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'tag_tabs:has(most-visit-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for most voted tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for most voted tab.','wpqa'),
			'id'        => "date_most_voted_tag",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'tag_tabs:has(most-vote-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		if (has_himer() || has_knowly() || has_questy()) {
			$options[] = array(
				'name'      => esc_html__('Specific date for most reacted tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for most reacted tab.','wpqa'),
				'id'        => "date_most_reacted_tag",
				'std'       => "all",
				'type'      => "radio",
				'condition' => 'tag_tabs:has(most-reacted-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);
		}

		$options[] = array(
			'name'      => esc_html__('Specific date for no answers tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for no answers tab.','wpqa'),
			'id'        => "date_no_answers_tag",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'tag_tabs:has(no-answers-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for random questions tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for random questions tab.','wpqa'),
			'id'        => "date_random_questions_tag",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'tag_tabs:has(random-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for new questions tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for new questions tab.','wpqa'),
			'id'        => "date_new_questions_tag",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'tag_tabs:has(new-questions-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for sticky questions tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for sticky questions tab.','wpqa'),
			'id'        => "date_sticky_questions_tag",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'tag_tabs:has(sticky-questions-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for poll questions tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for poll questions tab.','wpqa'),
			'id'        => "date_poll_questions_tag",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'tag_tabs:has(polls-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for followed questions tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for followed questions tab.','wpqa'),
			'id'        => "date_followed_questions_tag",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'tag_tabs:has(followed-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'name'      => esc_html__('Specific date for favorite questions tab.','wpqa'),
			'desc'      => esc_html__('Select the specific date for favorite questions tab.','wpqa'),
			'id'        => "date_favorites_questions_tag",
			'std'       => "all",
			'type'      => "radio",
			'condition' => 'tag_tabs:has(favorites-2)',
			'options'   => array(
				'all'   => esc_html__('All The Time','wpqa'),
				'24'    => esc_html__('Last 24 Hours','wpqa'),
				'48'    => esc_html__('Last 2 Days','wpqa'),
				'72'    => esc_html__('Last 3 Days','wpqa'),
				'96'    => esc_html__('Last 4 Days','wpqa'),
				'120'   => esc_html__('Last 5 Days','wpqa'),
				'144'   => esc_html__('Last 6 Days','wpqa'),
				'week'  => esc_html__('Last Week','wpqa'),
				'month' => esc_html__('Last Month','wpqa'),
				'year'  => esc_html__('Last Year','wpqa'),
			)
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'tag_tabs:has(recent-questions),tag_tabs:has(most-answers),tag_tabs:has(question-bump),tag_tabs:has(new-questions),tag_tabs:has(sticky-questions),tag_tabs:has(polls),tag_tabs:has(followed),tag_tabs:has(favorites),tag_tabs:has(answers),tag_tabs:has(most-visit),tag_tabs:has(most-vote),tag_tabs:has(random),tag_tabs:has(no-answers),tag_tabs:has(recent-questions-2),tag_tabs:has(most-answers-2),tag_tabs:has(question-bump-2),tag_tabs:has(new-questions-2),tag_tabs:has(sticky-questions-2),tag_tabs:has(polls-2),tag_tabs:has(followed-2),tag_tabs:has(favorites-2),tag_tabs:has(answers-2),tag_tabs:has(most-visit-2),tag_tabs:has(most-vote-2),tag_tabs:has(random-2),tag_tabs:has(no-answers-2)'.(has_himer() || has_knowly() || has_questy()?',tag_tabs:has(most-reacted),tag_tabs:has(most-reacted-2)':''),
			'operator'  => 'or',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'type' => 'info',
			'name' => esc_html__('Custom setting for the slugs','wpqa')
		);

		$options[] = array(
			'name'      => esc_html__('Recent questions slug','wpqa'),
			'id'        => 'recent_questions_slug_tag',
			'std'       => 'recent-questions',
			'condition' => 'tag_tabs:has(recent-questions)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most answered slug','wpqa'),
			'id'        => 'most_answers_slug_tag',
			'std'       => 'most-answered',
			'condition' => 'tag_tabs:has(most-answers)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Bump question slug','wpqa'),
			'id'        => 'question_bump_slug_tag',
			'std'       => 'question-bump',
			'condition' => 'tag_tabs:has(question-bump)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('New questions slug','wpqa'),
			'id'        => 'question_new_slug_tag',
			'std'       => 'new',
			'condition' => 'tag_tabs:has(new-questions)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question sticky slug','wpqa'),
			'id'        => 'question_sticky_slug_tag',
			'std'       => 'sticky',
			'condition' => 'tag_tabs:has(sticky-questions)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question polls slug','wpqa'),
			'id'        => 'question_polls_slug_tag',
			'std'       => 'polls',
			'condition' => 'tag_tabs:has(polls)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question followed slug','wpqa'),
			'id'        => 'question_followed_slug_tag',
			'std'       => 'followed',
			'condition' => 'tag_tabs:has(followed)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question favorites slug','wpqa'),
			'id'        => 'question_favorites_slug_tag',
			'std'       => 'favorites',
			'condition' => 'tag_tabs:has(favorites)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Answers slug','wpqa'),
			'id'        => 'tag_answers_slug_tag',
			'std'       => 'answers',
			'condition' => 'tag_tabs:has(answers)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most visited slug','wpqa'),
			'id'        => 'most_visit_slug_tag',
			'std'       => 'most-visited',
			'condition' => 'tag_tabs:has(most-visit)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most voted slug','wpqa'),
			'id'        => 'most_vote_slug_tag',
			'std'       => 'most-voted',
			'condition' => 'tag_tabs:has(most-vote)',
			'type'      => 'text'
		);

		if (has_himer() || has_knowly() || has_questy()) {
			$options[] = array(
				'name'      => esc_html__('Most reacted slug','wpqa'),
				'id'        => 'most_reacted_slug_tag',
				'std'       => 'most-reacted',
				'condition' => 'tag_tabs:has(most-reacted)',
				'type'      => 'text'
			);
		}

		$options[] = array(
			'name'      => esc_html__('Random slug','wpqa'),
			'id'        => 'random_slug_tag',
			'std'       => 'random',
			'condition' => 'tag_tabs:has(random)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('No answers slug','wpqa'),
			'id'        => 'no_answers_slug_tag',
			'std'       => 'no-answers',
			'condition' => 'tag_tabs:has(no-answers)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Recent questions with time slug','wpqa'),
			'id'        => 'recent_questions_slug_2_tag',
			'std'       => 'recent-questions-time',
			'condition' => 'tag_tabs:has(recent-questions-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most answered with time slug','wpqa'),
			'id'        => 'most_answers_slug_2_tag',
			'std'       => 'most-answered-time',
			'condition' => 'tag_tabs:has(most-answers-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Bump question with time slug','wpqa'),
			'id'        => 'question_bump_slug_2_tag',
			'std'       => 'question-bump-time',
			'condition' => 'tag_tabs:has(question-bump-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('New questions with time slug','wpqa'),
			'id'        => 'question_new_slug_2_tag',
			'std'       => 'new-time',
			'condition' => 'tag_tabs:has(new-questions-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question sticky with time slug','wpqa'),
			'id'        => 'question_sticky_slug_2_tag',
			'std'       => 'sticky-time',
			'condition' => 'tag_tabs:has(sticky-questions-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question polls with time slug','wpqa'),
			'id'        => 'question_polls_slug_2_tag',
			'std'       => 'polls-time',
			'condition' => 'tag_tabs:has(polls-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question followed with time slug','wpqa'),
			'id'        => 'question_followed_slug_2_tag',
			'std'       => 'followed-time',
			'condition' => 'tag_tabs:has(followed-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Question favorites with time slug','wpqa'),
			'id'        => 'question_favorites_slug_2_tag',
			'std'       => 'favorites-time',
			'condition' => 'tag_tabs:has(favorites-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Answers with time slug','wpqa'),
			'id'        => 'answers_slug_2_tag',
			'std'       => 'answers-time',
			'condition' => 'tag_tabs:has(answers-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most visited with time slug','wpqa'),
			'id'        => 'most_visit_slug_2_tag',
			'std'       => 'most-visited-time',
			'condition' => 'tag_tabs:has(most-visit-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Most voted with time slug','wpqa'),
			'id'        => 'most_vote_slug_2_tag',
			'std'       => 'most-voted-time',
			'condition' => 'tag_tabs:has(most-vote-2)',
			'type'      => 'text'
		);

		if (has_himer() || has_knowly() || has_questy()) {
			$options[] = array(
				'name'      => esc_html__('Most reacted with time slug','wpqa'),
				'id'        => 'most_reacted_slug_2_tag',
				'std'       => 'most-reacted-time',
				'condition' => 'tag_tabs:has(most-reacted-2)',
				'type'      => 'text'
			);
		}

		$options[] = array(
			'name'      => esc_html__('Random with time slug','wpqa'),
			'id'        => 'random_slug_2_tag',
			'std'       => 'random-time',
			'condition' => 'tag_tabs:has(random-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('No answers with time slug','wpqa'),
			'id'        => 'no_answers_slug_2_tag',
			'std'       => 'no-answers-time',
			'condition' => 'tag_tabs:has(no-answers-2)',
			'type'      => 'text'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'questions_loop',
			'name' => esc_html__('Questions & Loop settings','wpqa')
		);
		
		$options[] = array(
			'name'      => esc_html__('Columns in the archive, taxonomy and tags pages','wpqa'),
			'id'		=> "question_columns",
			'type'		=> 'radio',
			'options'	=> array(
				'style_1' => esc_html__('1 column','wpqa'),
				'style_2' => esc_html__('2 columns','wpqa')." - ".esc_html__('Works with sidebar, full width, and left menu only.','wpqa'),
			),
			'std'		=> 'style_1'
		);
		
		$options[] = array(
			'name'      => esc_html__("Activate the masonry style?","wpqa"),
			'id'        => 'masonry_style',
			'type'      => 'checkbox',
			'condition' => 'question_columns:is(style_2)',
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the author image in questions loop?','wpqa'),
			'desc' => esc_html__('Enable or disable author image in questions loop?','wpqa'),
			'id'   => 'author_image',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the vote in loop?','wpqa'),
			'desc' => esc_html__('Enable or disable vote in loop?','wpqa'),
			'id'   => 'vote_question_loop',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Select ON to hide the dislike at questions loop','wpqa'),
			'desc'      => esc_html__('If you put it ON the dislike will not show.','wpqa'),
			'id'        => 'question_loop_dislike',
			'condition' => 'vote_question_loop:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to hide the excerpt in questions','wpqa'),
			'id'   => 'excerpt_questions',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'excerpt_questions:not(on)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Excerpt question','wpqa'),
			'desc' => esc_html__('Put here the excerpt question.','wpqa'),
			'id'   => 'question_excerpt',
			'std'  => 40,
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to active the read more button in questions','wpqa'),
			'id'   => 'read_more_question',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Select ON to activate the read more by jQuery in questions','wpqa'),
			'id'        => 'read_jquery_question',
			'type'      => 'checkbox',
			'condition' => 'read_more_question:not(0)',
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to activate to see some answers and add a new answer by jQuery in questions','wpqa'),
			'id'   => 'answer_question_jquery',
			'type' => 'checkbox',
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Video','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Show the video at the question loop','wpqa'),
			'desc' => esc_html__('Select ON if you want to show the video of the question on the loop.','wpqa'),
			'id'   => 'video_desc_active_loop',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'video_desc_active_loop:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Video position at the question loop','wpqa'),
			'desc'    => esc_html__('Choose the video position.','wpqa'),
			'id'      => 'video_desc_loop',
			'options' => array("before" => "Before content","after" => "After content"),
			'std'     => 'after',
			'type'    => 'select'
		);
		
		$options[] = array(
			'name' => esc_html__('Set the video to 100%?','wpqa'),
			'desc' => esc_html__('Select ON if you want to set the video to 100%.','wpqa'),
			'id'   => 'video_desc_100_loop',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			"name"      => esc_html__("Set the width for the video for the questions","wpqa"),
			"id"        => "video_description_width",
			'condition' => 'video_desc_100_loop:not(on)',
			"type"      => "sliderui",
			'std'       => 260,
			"step"      => "1",
			"min"       => "50",
			"max"       => "600"
		);
		
		$options[] = array(
			"name" => esc_html__("Set the height for the video for the questions","wpqa"),
			"id"   => "video_description_height",
			"type" => "sliderui",
			'std'  => 500,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);

		$options[] = array(
			'name' => esc_html__('Featured image','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to show featured image in the questions','wpqa'),
			'id'   => 'featured_image_loop',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'featured_image_loop:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to enable the lightbox for featured image','wpqa'),
			'id'   => 'featured_image_question_lightbox',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			"name" => esc_html__("Set the width for the featured image for the questions","wpqa"),
			"id"   => "featured_image_question_width",
			"type" => "sliderui",
			'std'  => 260,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			"name" => esc_html__("Set the height for the featured image for the questions","wpqa"),
			"id"   => "featured_image_question_height",
			"type" => "sliderui",
			'std'  => 185,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			'name'    => esc_html__('Featured image position','wpqa'),
			'desc'    => esc_html__('Choose the featured image position.','wpqa'),
			'id'      => 'featured_position',
			'options' => array("before" => "Before content","after" => "After content"),
			'std'     => 'before',
			'type'    => 'radio'
		);

		$options[] = array(
			'name' => esc_html__('Poll','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to show the poll in questions loop','wpqa'),
			'id'   => 'question_poll_loop',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Poll position','wpqa'),
			'desc'    => esc_html__('Choose the poll position.','wpqa'),
			'id'      => 'poll_position',
			'options' => array("before" => "Before featured image","after" => "After featured image","before_content" => "Before content","after_content" => "After content"),
			'std'     => 'before',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable or disable Tags at loop?','wpqa'),
			'id'   => 'question_tags_loop',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => (has_himer() || has_knowly() || has_questy()?esc_html__('Activate the answer at the loop by best answer, most voted, most reacted, last answer or first answer','wpqa'):esc_html__('Activate the answer at the loop by best answer, most voted, last answer or first answer','wpqa')),
			'id'   => 'question_answer_loop',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'question_answer_loop:not(0)',
			'type'      => 'heading-2'
		);

		$answer_show = array(
			'best'   => esc_html__('Best answer','wpqa'),
			'vote'   => esc_html__('Most voted','wpqa'),
			'last'   => esc_html__('Last answer','wpqa'),
			'oldest' => esc_html__('First answer','wpqa'),
		);

		if (has_himer() || has_knowly() || has_questy()) {
			$answer_show['reacted'] = esc_html__('Most reacted','wpqa');
		}
		
		$options[] = array(
			'name'    => esc_html__('Answer type','wpqa'),
			'desc'    => esc_html__("Choose what's the answer you need to show from here.","wpqa"),
			'id'      => 'question_answer_show',
			'options' => $answer_show,
			'std'     => 'best',
			'type'    => 'radio'
		);

		$options[] = array(
			'name'    => esc_html__('Answer place','wpqa'),
			'desc'    => esc_html__("Choose where's the answer to be placed - before or after question meta.","wpqa"),
			'id'      => 'question_answer_place',
			'options' => array(
				'before' => esc_html__('Before question meta','wpqa'),
				'after'  => esc_html__('After question meta','wpqa'),
			),
			'std'     => 'before',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'    => esc_html__('Pagination style','wpqa'),
			'desc'    => esc_html__('Choose pagination style from here.','wpqa'),
			'id'      => 'question_pagination',
			'options' => array(
				'standard'        => esc_html__('Standard','wpqa'),
				'pagination'      => esc_html__('Pagination','wpqa'),
				'load_more'       => esc_html__('Load more','wpqa'),
				'infinite_scroll' => esc_html__('Infinite scroll','wpqa'),
				'none'            => esc_html__('None','wpqa'),
			),
			'std'     => 'pagination',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Inner question','wpqa'),
			'id'   => 'inner_question',
			'type' => 'heading-2'
		);

		$options = apply_filters("wpqa_before_inner_questions",$options);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_question_items:has(video_desc_active)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Video','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name'    => esc_html__('Video position','wpqa'),
			'desc'    => esc_html__('Choose the video position.','wpqa'),
			'id'      => 'video_desc',
			'options' => array("before" => esc_html__("Before content","wpqa"),"after" => esc_html__("After content","wpqa")),
			'std'     => 'after',
			'type'    => 'select'
		);
		
		$options[] = array(
			"name" => esc_html__("Set the height for the video for the questions","wpqa"),
			"id"   => "video_desc_height",
			"type" => "sliderui",
			'std'  => 500,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Featured image','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to show featured image in the single question','wpqa'),
			'id'   => 'featured_image_single',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'featured_image_single:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			"name" => esc_html__("Set the width for the featured image for the questions","wpqa"),
			"id"   => "featured_image_inner_question_width",
			"type" => "sliderui",
			'std'  => 260,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			"name" => esc_html__("Set the height for the featured image for the questions","wpqa"),
			"id"   => "featured_image_inner_question_height",
			"type" => "sliderui",
			'std'  => 185,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'    => esc_html__('Featured image position','wpqa'),
			'desc'    => esc_html__('Choose the featured image position.','wpqa'),
			'id'      => 'featured_position_single',
			'options' => array("before" => "Before content","after" => "After content"),
			'std'     => 'before',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name'    => esc_html__('Poll position','wpqa'),
			'desc'    => esc_html__('Choose the poll position.','wpqa'),
			'id'      => 'poll_position_single',
			'options' => array("before" => "Before featured image","after" => "After featured image","before_content" => "Before content","after_content" => "After content"),
			'std'     => 'before',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the author image in single?','wpqa'),
			'desc' => esc_html__('Author image in single enable or disable.','wpqa'),
			'id'   => 'author_image_single',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the vote in single?','wpqa'),
			'desc' => esc_html__('Vote in single enable or disable.','wpqa'),
			'id'   => 'vote_question_single',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Select ON to hide the dislike at questions single','wpqa'),
			'desc'      => esc_html__('If you put it ON the dislike will not show.','wpqa'),
			'id'        => 'question_single_dislike',
			'condition' => 'vote_question_single:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate close and open questions','wpqa'),
			'desc' => esc_html__('Select ON if you want activate close and open questions.','wpqa'),
			'id'   => 'question_close',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate close and open questions for the admin only','wpqa'),
			'desc' => esc_html__('Select ON if you want activate close and open questions for the admin only.','wpqa'),
			'id'   => 'question_close_admin',
			'type' => 'checkbox'
		);
		
		if (has_discy()) {
			$options[] = array(
				'name'      => esc_html__('Share style at the inner question page.','wpqa'),
				'id'        => 'share_style',
				'std'       => 'style_1',
				'type'      => 'radio',
				'condition' => 'question_simple:not(on)',
				'options'   => array(
					"style_1" => esc_html__("Style 1","wpqa"),
					"style_2" => esc_html__("Style 2","wpqa"),
				)
			);
		}
		
		$options[] = array(
			'name' => esc_html__('Tags at single question enable or disable','wpqa'),
			'desc' => esc_html__('Select ON if you want active tags at single question.','wpqa'),
			'id'   => 'question_tags',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Navigation question enable or disable','wpqa'),
			'desc' => esc_html__('Navigation question (next and previous questions) enable or disable.','wpqa'),
			'id'   => 'question_navigation',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Navigation question for the same category only?','wpqa'),
			'desc'      => esc_html__('Navigation question (next and previous questions) for the same category only?','wpqa'),
			'id'        => 'question_nav_category',
			'condition' => 'question_navigation:not(0)',
			'std'       => 'on',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Answers enable or disable','wpqa'),
			'desc' => esc_html__('Select ON if you want activate the answers.','wpqa'),
			'id'   => 'question_answers',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		if (has_himer()) {
			$options[] = array(
				'name' => esc_html__('Buttons of the answers for genders or count answers enable or disable','wpqa'),
				'id'   => 'button_of_gender',
				'std'  => 'on',
				'type' => 'checkbox'
			);

			$options[] = array(
				'name' => esc_html__('Sticky question bar enable or disable','wpqa'),
				'id'   => 'sticky_question_bar',
				'std'  => 'on',
				'type' => 'checkbox'
			);
		}
		
		$options[] = array(
			'name' => esc_html__('Related questions','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Related questions after content enable or disable','wpqa'),
			'desc' => esc_html__('Select ON if you want to activate the related questions after the content.','wpqa'),
			'id'   => 'question_related',
			'std'  => (has_himer() || has_knowly()?'on':0),
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'question_related:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Related position','wpqa'),
			'desc'    => esc_html__('Choose the related position from here.','wpqa'),
			'id'      => 'question_related_position',
			'std'     => 'after_content',
			'options' => array(
				'after_content' => esc_html__('After content','wpqa'),
				'after_answers' => esc_html__('After answers','wpqa'),
			),
			'type'    => 'radio'
		);
		
		if (has_himer() || has_knowly()) {
			$options[] = array(
				'name'    => esc_html__('Related style','wpqa'),
				'desc'    => esc_html__('Choose the related post style from here.','wpqa'),
				'id'      => 'question_related_style',
				'std'     => 'box_style',
				'options' => array(
					'box_style' => esc_html__('Box style','wpqa'),
					'links'     => esc_html__('Links style','wpqa'),
				),
				'type'    => 'radio'
			);
			
			$options[] = array(
				'name'      => esc_html__('Make the related questions as slider','wpqa'),
				'id'        => 'question_related_slider',
				'type'      => 'checkbox',
				'std'       => 'on',
				'condition' => 'question_related_style:is(box_style)'
			);
		}

		$options[] = array(
			'name' => esc_html__('Number of items to show','wpqa'),
			'id'   => 'related_number_question',
			'type' => 'text',
			'std'  => (has_himer() || has_knowly()?'6':'5'),
		);
		
		$options[] = array(
			'name'    => esc_html__('Query type','wpqa'),
			'id'      => 'query_related_question',
			'options' => array(
				'categories' => esc_html__('Questions in the same categories','wpqa'),
				'tags'       => esc_html__('Questions in the same tags (If not found, questions with the same categories will be shown)','wpqa'),
				'author'     => esc_html__('Questions by the same author','wpqa'),
			),
			'std'     => 'categories',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name' => esc_html__('Excerpt title in related questions','wpqa'),
			'desc' => esc_html__('Type excerpt title in related questions from here.','wpqa'),
			'id'   => 'related_title_question',
			'std'  => '20',
			'type' => 'text'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Share setting','wpqa'),
			'id'   => 'share_setting_q',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Select the share options','wpqa'),
			'id'      => 'question_share',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $share_array,
			'options' => $share_array
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_after_questions_layout',$options);

		if ($activate_knowledgebase == true) {
			$knowledgebases_settings = array(
				"general_setting_k"     => esc_html__('General settings','wpqa'),
				"knowledgebase_slug"    => esc_html__('Knowledgebase slugs','wpqa'),
				"knowledgebase_meta"    => esc_html__('Knowledgebase meta settings','wpqa'),
				"knowledgebases_loop"   => esc_html__('Knowledgebases & Loop settings','wpqa'),
				"inner_knowledgebase"   => esc_html__('Inner knowledgebase','wpqa'),
				"share_setting_k"       => esc_html__('Share setting','wpqa')
			);

			$options[] = array(
				'name'    => esc_html__('Knowledgebase settings','wpqa'),
				'id'      => 'knowledgebase',
				'icon'    => 'buddicons-forums',
				'type'    => 'heading',
				'std'     => 'general_setting_k',
				'options' => apply_filters(wpqa_prefix_theme."_knowledgebases_settings",$knowledgebases_settings)
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'id'   => 'general_setting_k',
				'name' => esc_html__('General settings','wpqa')
			);

			$options = apply_filters(wpqa_prefix_theme.'_options_before_knowledgebase_general_setting',$options);
			
			$options[] = array(
				'name' => esc_html__('Category description enable or disable','wpqa'),
				'desc' => esc_html__('Select ON to enable the category description in the category page.','wpqa'),
				'id'   => 'knowledgebase_category_description',
				'std'  => 'on',
				'type' => 'checkbox'
			);

			$options[] = array(
				'name'      => esc_html__('Category rss enable or disable','wpqa'),
				'desc'      => esc_html__('Select ON to enable the category rss in the category page.','wpqa'),
				'id'        => 'knowledgebase_category_rss',
				'std'       => 'on',
				'condition' => 'knowledgebase_category_description:not(0)',
				'type'      => 'checkbox'
			);
			
			$options[] = array(
				'name' => esc_html__('Tag description enable or disable','wpqa'),
				'desc' => esc_html__('Select ON to enable the tag description in the tag page.','wpqa'),
				'id'   => 'knowledgebase_tag_description',
				'std'  => 'on',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name'      => esc_html__('Tag rss enable or disable','wpqa'),
				'desc'      => esc_html__('Select ON to enable the tag rss in the tag page.','wpqa'),
				'id'        => 'knowledgebase_tag_rss',
				'std'       => 'on',
				'condition' => 'knowledgebase_tag_description:not(0)',
				'type'      => 'checkbox'
			);
		
			$options[] = array(
				'name' => esc_html__('Do you need to hide the content only for the private article?','wpqa'),
				'desc' => esc_html__('Select ON if you want to hide the content only for the private article.','wpqa'),
				'id'   => 'private_knowledgebase_content',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name' => esc_html__('Category cover','wpqa'),
				'type' => 'info'
			);

			$options[] = array(
				'name' => esc_html__('Activate the cover for categories?','wpqa'),
				'desc' => esc_html__('Cover for categories enable or disable.','wpqa'),
				'id'   => 'active_cover_category_kb',
				'type' => 'checkbox'
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => 'active_cover_category_kb:not(0)',
				'type'      => 'heading-2'
			);

			$options[] = array(
				'name'    => esc_html__('Cover full width or fixed','wpqa'),
				'desc'    => esc_html__('Choose the cover to make it work with full width or fixed.','wpqa'),
				'id'      => 'cover_category_fixed_kb',
				'options' => array(
					'normal' => esc_html__('Full width','wpqa'),
					'fixed'  => esc_html__('Fixed','wpqa'),
				),
				'std'     => 'normal',
				'type'    => 'radio'
			);
			
			$options[] = array(
				'name'    => esc_html__('Select the share options','wpqa'),
				'id'      => 'cat_share_kb',
				'type'    => 'multicheck',
				'sort'    => 'yes',
				'std'     => $share_array,
				'options' => $share_array
			);

			$options[] = array(
				'name' => esc_html__('Default cover enable or disable.','wpqa'),
				'desc' => esc_html__("Select ON to upload your default cover for the categories which doesn't have cover.","wpqa"),
				'id'   => 'default_cover_cat_active_kb',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name'      => esc_html__('Upload default cover for the categories.','wpqa'),
				'id'        => 'default_cover_cat_kb',
				'condition' => 'default_cover_cat_active_kb:not(0)',
				'type'      => 'upload'
			);

			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name' => esc_html__('Knowledgebase slugs','wpqa'),
				'id'   => 'knowledgebase_slug',
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Knowledgebases archive slug','wpqa'),
				'desc' => esc_html__('Add your knowledgebases archive slug.','wpqa'),
				'id'   => 'archive_knowledgebase_slug',
				'std'  => 'knowledgebases',
				'type' => 'text'
			);

			$options[] = array(
				'name' => esc_html__('Click ON, if you need to remove the knowledgebase slug and choose "Post name" from WordPress Settings/Permalinks.','wpqa'),
				'id'   => 'remove_knowledgebase_slug',
				'type' => 'checkbox'
			);

			$options[] = array(
				'name' => esc_html__('Click ON, if you need to make the knowledgebase slug with number instant of title and choose "Post name" from WordPress Settings/Permalinks.','wpqa'),
				'id'   => 'knowledgebase_slug_numbers',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name'      => esc_html__('Knowledgebase slug','wpqa'),
				'desc'      => esc_html__('Add your knowledgebase slug.','wpqa'),
				'id'        => 'knowledgebase_slug',
				'std'       => wpqa_knowledgebase_type,
				'condition' => 'remove_knowledgebase_slug:not(on)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name' => esc_html__('Knowledgebase category slug','wpqa'),
				'desc' => esc_html__('Add your knowledgebase category slug.','wpqa'),
				'id'   => 'category_knowledgebase_slug',
				'std'  => 'kb',
				'type' => 'text'
			);
			
			$options[] = array(
				'name' => esc_html__('Knowledgebase tag slug','wpqa'),
				'desc' => esc_html__('Add your knowledgebase tag slug.','wpqa'),
				'id'   => 'tag_knowledgebase_slug',
				'std'  => 'kb-tag',
				'type' => 'text'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name' => esc_html__('Knowledgebase meta settings','wpqa'),
				'id'   => 'knowledgebase_meta',
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Meta style','wpqa'),
				'desc'    => esc_html__('Choose meta style from here.','wpqa'),
				'id'      => 'knowledgebase_meta_style',
				'options' => array(
					'style_1' => esc_html__('Style 1','wpqa'),
					'style_2' => esc_html__('Style 2','wpqa'),
				),
				'std'     => 'style_1',
				'type'    => 'radio'
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON if you want icons only at the article meta.','wpqa'),
				'id'   => 'knowledgebase_meta_icon',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name'    => esc_html__('Select the meta options','wpqa'),
				'id'      => 'knowledgebase_meta',
				'type'    => 'multicheck',
				'std'     => array(
					"author_by"              => "author_by",
					"knowledgebase_date"     => "knowledgebase_date",
					"category_knowledgebase" => "category_knowledgebase",
					"knowledgebase_views"    => "knowledgebase_views",
					"knowledgebase_votes"    => "knowledgebase_votes",
					"knowledgebase_read"     => "knowledgebase_read",
					"knowledgebase_print"    => "knowledgebase_print",
				),
				'options' => array(
					"author_by"              => esc_html__('Author by','wpqa'),
					"knowledgebase_date"     => esc_html__('Date meta','wpqa'),
					"category_knowledgebase" => esc_html__('Category article','wpqa'),
					"knowledgebase_views"    => esc_html__('Views stats','wpqa'),
					"knowledgebase_votes"    => esc_html__('Votes','wpqa'),
					"knowledgebase_read"     => esc_html__('Read time','wpqa'),
					"knowledgebase_print"    => esc_html__('Print','wpqa'),
				)
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'id'   => 'knowledgebases_loop',
				'name' => esc_html__('Knowledgebases & Loop settings','wpqa')
			);
			
			$options[] = array(
				'name'      => esc_html__('Knowledgebases styles','wpqa'),
				'id'		=> "knowledgebase_style",
				'type'		=> 'radio',
				'options'	=> array(
					'article_1' => esc_html__('Article stlye 1','wpqa'),
					'article_2' => esc_html__('Article stlye 2 - With boxed layout','wpqa'),
				),
				'std'		=> 'article_1'
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON to show the article icon beside the title','wpqa'),
				'id'   => 'icon_knowledgebases',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON to hide the excerpt in articles','wpqa'),
				'id'   => 'excerpt_knowledgebases',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name'      => esc_html__('Excerpt article','wpqa'),
				'desc'      => esc_html__('Put here the excerpt article.','wpqa'),
				'id'        => 'knowledgebase_excerpt',
				'condition' => 'excerpt_knowledgebases:not(on)',
				'std'       => 40,
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'    => esc_html__('Pagination style','wpqa'),
				'desc'    => esc_html__('Choose pagination style from here.','wpqa'),
				'id'      => 'knowledgebase_pagination',
				'options' => array(
					'standard'        => esc_html__('Standard','wpqa'),
					'pagination'      => esc_html__('Pagination','wpqa'),
					'load_more'       => esc_html__('Load more','wpqa'),
					'infinite_scroll' => esc_html__('Infinite scroll','wpqa'),
					'none'            => esc_html__('None','wpqa'),
				),
				'std'     => 'pagination',
				'type'    => 'radio'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name' => esc_html__('Inner knowledgebase','wpqa'),
				'id'   => 'inner_knowledgebase',
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Activate the author image in single?','wpqa'),
				'desc' => esc_html__('Author image in single enable or disable.','wpqa'),
				'id'   => 'knowledgebase_author_image_single',
				'std'  => "on",
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name' => esc_html__('Tags at single article enable or disable','wpqa'),
				'desc' => esc_html__('Select ON if you want active tags at single article.','wpqa'),
				'id'   => 'knowledgebase_tags',
				'std'  => 'on',
				'type' => 'checkbox'
			);

			$options[] = array(
				'name'    => esc_html__('Navigation article enable or disable','wpqa'),
				'desc'    => esc_html__('Navigation article (next and previous articles) enable or disable.','wpqa'),
				'id'      => 'knowledgebase_navigation',
				'type'    => 'multicheck',
				'std'     => array(
					"breadcrumbs"   => "breadcrumbs",
					"after_content" => "after_content",
				),
				'options' => array(
					"breadcrumbs"   => esc_html__('Breadcrumbs','wpqa'),
					"after_content" => esc_html__("After content","wpqa"),
				)
			);
			
			$options[] = array(
				'name'      => esc_html__('Navigation article for the same category only?','wpqa'),
				'desc'      => esc_html__('Navigation article (next and previous articles) for the same category only?','wpqa'),
				'id'        => 'knowledgebase_nav_category',
				'condition' => 'knowledgebase_navigation:has(breadcrumbs),knowledgebase_navigation:has(after_content)',
				'operator'  => 'or',
				'std'       => 'on',
				'type'      => 'checkbox'
			);

			$options[] = array(
				'name' => esc_html__('Didn\'t find answer section','wpqa'),
				'type' => 'info'
			);
			
			$options[] = array(
				'name' => esc_html__('Didn\'t find answer section enable or disable','wpqa'),
				'id'   => 'didnt_find_answer',
				'std'  => 'on',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => 'didnt_find_answer:not(0)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Didn\'t find answer style','wpqa'),
				'id'      => 'didnt_find_answer_style',
				'options' => array(
					'style_1' => esc_html__('Style 1 - buttons for yes and no','wpqa'),
					'style_2' => esc_html__('Style 2 - buttons for yes and no (The same of style 1 but with small change on the title)','wpqa'),
					'style_3' => esc_html__('Style 3 - react rate','wpqa'),
					'style_4' => esc_html__('Style 4 - react rate','wpqa'),
				),
				'std'     => 'style_1',
				'type'    => 'radio'
			);
			
			$options[] = array(
				'name' => esc_html__('Didn\'t find answer link enable or disable','wpqa'),
				'desc' => esc_html__('Didn\'t find answer link to set page for it.','wpqa'),
				'id'   => 'didnt_find_answer_link',
				'std'  => 'on',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => 'didnt_find_answer_link:not(0)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Didn\'t find answer page','wpqa'),
				'id'      => 'didnt_find_answer_page',
				'options' => array(
					'contact'  => esc_html__('Contact page (If you activate it will assign it)','wpqa'),
					'question' => esc_html__('Ask question page','wpqa'),
					'custom'   => esc_html__('Custom link','wpqa'),
				),
				'std'     => 'contact',
				'type'    => 'radio'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => 'didnt_find_answer_page:is(custom)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__("Type the link if you don't like above","wpqa"),
				'id'   => 'didnt_find_answer_custom',
				'type' => 'text'
			);
			
			$options[] = array(
				'name' => esc_html__("Type the text of the link","wpqa"),
				'id'   => 'didnt_find_answer_text',
				'type' => 'text'
			);

			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name' => esc_html__('Related articles','wpqa'),
				'type' => 'info'
			);
			
			$options[] = array(
				'name' => esc_html__('Related articles after content enable or disable','wpqa'),
				'desc' => esc_html__('Select ON if you want to activate the related articles after the content.','wpqa'),
				'id'   => 'knowledgebase_related',
				'std'  => (has_himer() || has_knowly()?'on':0),
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => 'knowledgebase_related:not(0)',
				'type'      => 'heading-2'
			);

			$options[] = array(
				'name' => esc_html__('Number of items to show','wpqa'),
				'id'   => 'related_number_knowledgebase',
				'type' => 'text',
				'std'  => (has_himer() || has_knowly()?'6':'5'),
			);
			
			$options[] = array(
				'name'    => esc_html__('Query type','wpqa'),
				'id'      => 'query_related_knowledgebase',
				'options' => array(
					'categories' => esc_html__('Articles in the same categories','wpqa'),
					'tags'       => esc_html__('Articles in the same tags (If not found, articles with the same categories will be shown)','wpqa'),
					'author'     => esc_html__('Articles by the same author','wpqa'),
				),
				'std'     => 'categories',
				'type'    => 'radio'
			);
			
			$options[] = array(
				'name' => esc_html__('Excerpt title in related articles','wpqa'),
				'desc' => esc_html__('Type excerpt title in related articles from here.','wpqa'),
				'id'   => 'related_title_knowledgebase',
				'std'  => '20',
				'type' => 'text'
			);

			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name' => esc_html__('Share setting','wpqa'),
				'id'   => 'share_setting_k',
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Select the share options','wpqa'),
				'id'      => 'knowledgebase_share',
				'type'    => 'multicheck',
				'sort'    => 'yes',
				'std'     => $share_array,
				'options' => $share_array
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);

			$options = apply_filters(wpqa_prefix_theme.'_options_after_knowledgebases_layout',$options);
		}

		$options[] = array(
			'name'    => esc_html__('Popup share','wpqa'),
			'id'      => 'popup_share',
			'icon'    => 'share',
			'type'    => 'heading',
		);

		$options[] = array(
			'type' => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Activate the popup share for the posts and questions?','wpqa'),
			'desc' => esc_html__('Popup share for the posts and questions enable or disable.','wpqa'),
			'id'   => 'active_popup_share',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_popup_share:not(0)',
			'type'      => 'heading-2'
		);

		$popup_share_pages_std = array(
			"questions" => "questions",
			"posts"     => "posts",
		);

		if ($activate_knowledgebase == true) {
			$popup_share_pages_std["knowledgebases"] = "knowledgebases";
		}

		$popup_share_pages_options = array(
			"questions" => esc_html__('Questions','wpqa'),
			"posts"     => esc_html__("Posts","wpqa"),
		);

		if ($activate_knowledgebase == true) {
			$popup_share_pages_options["knowledgebases"] = esc_html__("Knowledgebases","wpqa");
		}

		$options[] = array(
			'name'    => esc_html__('Select which page do you need it to work','wpqa'),
			'id'      => 'popup_share_pages',
			'type'    => 'multicheck',
			'std'     => $popup_share_pages_std,
			'options' => $popup_share_pages_options
		);

		$options[] = array(
			'name'    => esc_html__('Popup share works for "unlogged users", "logged in users", or "unlogged users" and "logged in users"','wpqa'),
			'id'      => 'popup_share_users',
			'std'     => 'both',
			'type'    => 'radio',
			'options' => array(
				"unlogged" => esc_html__('Unlogged users','wpqa'),
				"logged"   => esc_html__('Logged users','wpqa'),
				"both"     => esc_html__('Unlogged and logged in users','wpqa')
			)
		);

		$options[] = array(
			'name'    => esc_html__('Popup share shows only for the owner only or for all','wpqa'),
			'id'      => 'popup_share_type',
			'std'     => 'all',
			'type'    => 'radio',
			'options' => array(
				"all"   => esc_html__('For all','wpqa'),
				"owner" => esc_html__('Owner','wpqa')
			)
		);

		$options[] = array(
			'name'    => esc_html__('Popup share works when visiting the questions and posts or when scroll down to comments or to the adding comment box','wpqa'),
			'id'      => 'popup_share_visits',
			'std'     => 'visit',
			'type'    => 'radio',
			'options' => array(
				"visit"  => esc_html__('Visiting','wpqa'),
				"scroll" => esc_html__('Scroll down','wpqa')
			)
		);

		$options[] = array(
			"name"      => esc_html__("How many seconds to show the popup share for?","wpqa"),
			"desc"      => esc_html__("Type here the seconds to show the popup share and leave it to 0 to show when open the question or post.","wpqa"),
			"id"        => "popup_share_seconds",
			"type"      => "sliderui",
			'std'       => "30",
			"step"      => "1",
			"min"       => "0",
			"max"       => "60",
			"condition" => "popup_share_visits:is(visit)",
		);

		$options[] = array(
			'name'    => esc_html__('Popup share shows per day, week, month, or forever','wpqa'),
			'id'      => 'popup_share_shows',
			'std'     => 'day',
			'type'    => 'radio',
			'options' => array(
				"day"     => esc_html__('Day','wpqa'),
				"week"    => esc_html__('Week','wpqa'),
				"month"   => esc_html__('Month','wpqa'),
				"forever" => esc_html__('Forever','wpqa')
			)
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options[] = array(
			'name'    => esc_html__('Category moderators','wpqa'),
			'id'      => 'category_moderators',
			'icon'    => 'businessperson',
			'type'    => 'heading',
		);

		$options[] = array(
			'type' => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Activate the moderators','wpqa'),
			'desc' => esc_html__('Moderators enable or disable.','wpqa'),
			'id'   => 'active_moderators',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_moderators:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('User pending questions slug','wpqa'),
			'desc' => esc_html__('Put the user pending questions slug.','wpqa'),
			'id'   => 'pending_questions_slug',
			'std'  => 'pending-questions',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('User pending posts slug','wpqa'),
			'desc' => esc_html__('Put the user pending posts slug.','wpqa'),
			'id'   => 'pending_posts_slug',
			'std'  => 'pending-posts',
			'type' => 'text'
		);

		$options[] = array(
			'name'    => esc_html__('Select the moderators permissions','wpqa'),
			'id'      => 'moderators_permissions',
			'type'    => 'multicheck',
			'std'     => array(
				"delete"  => "delete",
				"close"   => "close",
				"best"    => "best",
				"approve" => "approve",
				"edit"    => "edit",
				"ban"     => "ban",
			),
			'options' => array(
				"delete"  => esc_html__('Delete questions or posts','wpqa'),
				"close"   => esc_html__('Close and open questions','wpqa'),
				"best"    => esc_html__('Choose and cancel the best answers','wpqa'),
				"approve" => esc_html__('Approve questions or posts','wpqa'),
				"edit"    => esc_html__('Edit questions or posts','wpqa'),
				"ban"     => esc_html__("Ban users","wpqa"),
			)
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$paymeny_setting = array(
			"payments_settings" => esc_html__('Payment setting','wpqa'),
			"pay_to_ask"        => esc_html__('Pay to ask','wpqa'),
			"pay_to_sticky"     => esc_html__('Pay for sticky question','wpqa'),
			"pay_to_answer"     => esc_html__('Pay to answer','wpqa'),
			"subscriptions"     => esc_html__('Subscriptions','wpqa'),
			"pay_to_post"       => esc_html__('Pay to post','wpqa'),
			"pay_create_group"  => esc_html__('Pay to create group','wpqa'),
			"buy_points"        => esc_html__('Buy points','wpqa'),
			"pay_to_users"      => esc_html__('Pay to users','wpqa'),
			"coupons_setting"   => esc_html__('Coupon settings','wpqa'),
		);

		$options[] = array(
			'name'    => esc_html__('Payment settings','wpqa'),
			'id'      => 'payment_setting',
			'icon'    => 'tickets-alt',
			'type'    => 'heading',
			'std'     => 'payments_settings',
			'options' => apply_filters(wpqa_prefix_theme."_payment_setting",$paymeny_setting)
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'payments_settings',
			'name' => esc_html__('Payment setting','wpqa')
		);

		$options[] = array(
			'name' => esc_html__('Checkout slug','wpqa'),
			'desc' => esc_html__('Put the checkout slug.','wpqa'),
			'id'   => 'checkout_slug',
			'std'  => 'checkout',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable the unlogged users to pay','wpqa'),
			'desc' => esc_html__('Click ON to activate unlogged users can pay and register on the same step not the logged users only can pay.','wpqa'),
			'id'   => 'unlogged_pay',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the payment page on the sampe page or new page','wpqa'),
			'id'      => 'payment_pages_target',
			'std'     => 'new_page',
			'type'    => 'radio',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name' => esc_html__('Enable the transactions page for the users','wpqa'),
			'desc' => esc_html__('Click ON to activate the transactions page for the users to show their transactions on the site.','wpqa'),
			'id'   => 'transactions_page',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => '<a href="'.wpqa_get_checkout_permalink().'" target="_blank">'.esc_html__('The Link For The Checkout Page.','wpqa').'</a>',
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable the transactions of the payments with points saved in the statements','wpqa'),
			'desc' => esc_html__('Click ON to activate the transactions of the payments with points saved in the statements.','wpqa'),
			'id'   => 'save_pay_by_points',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Payment style','wpqa'),
			'desc'    => esc_html__('Choose the payment style for the design.','wpqa'),
			'id'      => 'payment_style',
			'std'     => 'style_1',
			'type'    => 'radio',
			'options' => array(
				"style_1" => esc_html__('Style 1','wpqa'),
				"style_2" => esc_html__('Style 2','wpqa')
			)
		);

		$options[] = array(
			'name'     => esc_html__('Custom text after the payment button','wpqa'),
			'id'       => 'custom_text_payment',
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);

		$payment_methods = array(
			"paypal"  => array("sort" => esc_html__('PayPal','wpqa'),"value" => "paypal"),
			"stripe"  => array("sort" => esc_html__('Stripe','wpqa'),"value" => "stripe"),
			"bank"    => array("sort" => esc_html__('Bank Transfer','wpqa'),"value" => "bank"),
			"custom"  => array("sort" => esc_html__('Custom Payment','wpqa'),"value" => "custom"),
			"custom2" => array("sort" => esc_html__('Custom Payment 2','wpqa'),"value" => "custom2"),
		);

		if (class_exists('woocommerce')) {
			//$payment_methods["woocommerce"] = array("sort" => esc_html__('WooCommerce','wpqa'),"value" => "woocommerce");
		}

		$payment_methods_std = array(
			"paypal" => array("sort" => esc_html__('PayPal','wpqa'),"value" => "paypal"),
			"stripe" => array("sort" => esc_html__('Stripe','wpqa'),"value" => "stripe"),
		);
		
		$options[] = array(
			'name'    => esc_html__('Select the payment methods','wpqa'),
			'id'      => 'payment_methods',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $payment_methods_std,
			'options' => $payment_methods,
		);
		
		if (class_exists('woocommerce')) {
			$options[] = array(
				'div'       => 'div',
				'condition' => 'payment_methods:has(woocommerce)',
				'type'      => 'heading-2'
			);

			$options[] = array(
				'name' => esc_html__('WooCommerce Payment','wpqa'),
				'type' => 'info'
			);

			$options = apply_filters("wpqa_options_woocommerce_settings",$options);

			if (!defined('wpqa_woo_plugin_version')) {
				$options[] = array(
					'name'  => esc_html__('All the options on this page, if you do not buy the WPQA WooCommerce Payment, will not work.','wpqa'),
					'type'  => 'info',
					'alert' => 'alert-message-warning'
				);

				$options[] = array(
					'name' => '<a href="https://2code.info/WPQA-woo/" target="_blank">'.esc_html__('For more information and buying the WPQA WooCommerce Payment','wpqa').'</a>',
					'type' => 'info'
				);

				$options[] = array(
					"name" => esc_html__("WooCommerce payment tab name","wpqa"),
					"id"   => "woocommerce_payment_tab",
					"type" => "text",
					'std'  => "WooCommerce payment"
				);

				$options[] = array(
					'div'       => 'div',
					'condition' => 'payment_style:is(style_1)',
					'type'      => 'heading-2'
				);

				$options[] = array(
					'name' => esc_html__('Upload the WooCommerce image of tab.','wpqa'),
					'id'   => 'woocommerce_tab_image',
					'type' => 'upload'
				);

				$options[] = array(
					"name" => esc_html__("Choose the width of the tab image","wpqa"),
					"id"   => "woocommerce_tab_image_width",
					"type" => "sliderui",
					'std'  => "100",
					"step" => "50",
					"min"  => "50",
					"max"  => "300"
				);

				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);

				$options[] = array(
					'name' => esc_html__('Upload the WooCommerce image of the bottom image.','wpqa'),
					'id'   => 'woocommerce_payment_image',
					'type' => 'upload'
				);

				$order_statuses = wc_get_order_statuses();
				$gateways = WC()->payment_gateways->get_available_payment_gateways();
				if (is_array($gateways) && !empty($gateways) && is_array($order_statuses) && !empty($order_statuses)) {
					$options[] = array(
						'name' => esc_html__('The default option is recommended. This option applies to the WooCommerce default status on order completion. If you want different order status on order completion, you can use any of the following Processing, On Hold, or Completed as the order status.','wpqa'),
						'type' => 'info'
					);
					foreach ($gateways as $key => $value) {
						$options[] = array(
							'name'    => esc_html__('Order statuses for the completion order for:','wpqa')." ".$value->method_title,
							'id'      => 'order_statuses_'.$key,
							'std'     => 'wc-completed',
							'type'    => "select",
							'options' => $order_statuses
						);
					}
				}
			}

			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
		}
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'payment_methods:has(paypal)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('PayPal','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'payment_style:is(style_1)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Upload the custom image of tab.','wpqa'),
			'id'   => 'paypal_tab_image',
			'type' => 'upload'
		);

		$options[] = array(
			"name" => esc_html__("Choose the width of the tab image","wpqa"),
			"id"   => "paypal_tab_image_width",
			"type" => "sliderui",
			'std'  => "100",
			"step" => "50",
			"min"  => "50",
			"max"  => "300"
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Upload your PayPal logo','wpqa'),
			'desc' => esc_html__('Upload your custom logo for the PayPal.','wpqa'),
			'id'   => 'paypal_logo',
			'std'  => $imagepath_theme."logo.png",
			'type' => 'upload',
		);

		$options[] = array(
			'std'      => esc_url(home_url('/'))."?action=paypal",
			'name'     => esc_html__("Put this link at IPN","wpqa"),
			'readonly' => 'readonly',
			'type'     => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable PayPal sandbox','wpqa'),
			'desc' => esc_html__('PayPal sandbox can be used to test payments.','wpqa'),
			'id'   => 'paypal_sandbox',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'paypal_sandbox:is(on)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__("PayPal email","wpqa"),
			'desc' => esc_html__("put your PayPal email","wpqa"),
			'id'   => 'paypal_email_sandbox',
			'std'  => get_bloginfo("admin_email"),
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__("PayPal Identity Token","wpqa"),
			'desc' => esc_html__("Add your PayPal Identity Token","wpqa"),
			'id'   => 'identity_token_sandbox',
			'type' => 'text'
		);

		$options[] = array(
			'name' => sprintf(__('Enter your PayPal API credentials. Learn how to access your <a target="_blank" href="%s">PayPal API Credentials</a>.','wpqa'),'https://www.paypal.com/businessprofile/mytools/apiaccess/firstparty/signature'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__("Live API username","wpqa"),
			'desc' => esc_html__("Add your PayPal live API username","wpqa"),
			'id'   => 'paypal_api_username_sandbox',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__("Live API password","wpqa"),
			'desc' => esc_html__("Add your PayPal live API password","wpqa"),
			'id'   => 'paypal_api_password_sandbox',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__("Live API signature","wpqa"),
			'desc' => esc_html__("Add your PayPal live API signature","wpqa"),
			'id'   => 'paypal_api_signature_sandbox',
			'type' => 'text'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'paypal_sandbox:not(on)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__("PayPal email","wpqa"),
			'desc' => esc_html__("put your PayPal email","wpqa"),
			'id'   => 'paypal_email',
			'std'  => get_bloginfo("admin_email"),
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__("PayPal Identity Token","wpqa"),
			'desc' => esc_html__("Add your PayPal Identity Token","wpqa"),
			'id'   => 'identity_token',
			'type' => 'password'
		);

		$options[] = array(
			'name' => sprintf(__('Enter your PayPal API credentials. Learn how to access your <a target="_blank" href="%s">PayPal API Credentials</a>.','wpqa'),'https://www.paypal.com/businessprofile/mytools/apiaccess/firstparty/signature'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__("Live API username","wpqa"),
			'desc' => esc_html__("Add your PayPal live API username","wpqa"),
			'id'   => 'paypal_api_username',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__("Live API password","wpqa"),
			'desc' => esc_html__("Add your PayPal live API password","wpqa"),
			'id'   => 'paypal_api_password',
			'type' => 'password'
		);
		
		$options[] = array(
			'name' => esc_html__("Live API signature","wpqa"),
			'desc' => esc_html__("Add your PayPal live API signature","wpqa"),
			'id'   => 'paypal_api_signature',
			'type' => 'password'
		);

		$options[] = array(
			'name' => esc_html__('Upload the custom image of the bottom image.','wpqa'),
			'id'   => 'paypal_payment_image',
			'type' => 'upload'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'payment_methods:has(stripe)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Stripe','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'payment_style:is(style_1)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Upload the custom image of tab.','wpqa'),
			'id'   => 'stripe_tab_image',
			'type' => 'upload'
		);

		$options[] = array(
			"name" => esc_html__("Choose the width of the tab image","wpqa"),
			"id"   => "stripe_tab_image_width",
			"type" => "sliderui",
			'std'  => "100",
			"step" => "50",
			"min"  => "50",
			"max"  => "300"
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'    => esc_html__('Stripe style input','wpqa'),
			'id'      => 'stripe_style_input',
			'std'     => '1_input',
			'type'    => 'radio',
			'options' => array("1_input" => esc_html__("One input for visa","wpqa"),"3_inputs" => esc_html__("Show card number, expiry date, and CVC separately","wpqa"))
		);
		
		$options[] = array(
			'name' => esc_html__('Enable Stripe test','wpqa'),
			'desc' => esc_html__('Stripe test can be used to test payments.','wpqa'),
			'id'   => 'stripe_test',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'stripe_test:is(on)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Your Stripe testing API Publishable key, obtained from your Stripe dashboard','wpqa'),
			'id'   => 'test_publishable_key',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Your Stripe testing API Secret, obtained from your Stripe dashboard','wpqa'),
			'id'   => 'test_secret_key',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Your Stripe testing webhook endpoint URL, obtained from your Stripe dashboard','wpqa'),
			'id'   => 'test_webhook_secret',
			'type' => 'text'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'stripe_test:not(on)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Your Stripe API Publishable key, obtained from your Stripe dashboard','wpqa'),
			'id'   => 'publishable_key',
			'type' => 'password'
		);

		$options[] = array(
			'name' => esc_html__('Your Stripe API Secret, obtained from your Stripe dashboard','wpqa'),
			'id'   => 'secret_key',
			'type' => 'password'
		);

		$options[] = array(
			'name' => esc_html__('Your Stripe webhook endpoint URL, obtained from your Stripe dashboard','wpqa'),
			'id'   => 'webhook_secret',
			'type' => 'password'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'std'      => esc_url(home_url('/'))."?action=stripe",
			'name'     => esc_html__("Put this link at webhooks","wpqa"),
			'readonly' => 'readonly',
			'type'     => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Activate the address info','wpqa'),
			'desc' => esc_html__("Select ON to active the address info, it's very important for some countries to activate it.","wpqa"),
			'id'   => 'stripe_address',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Upload the custom image of the bottom image.','wpqa'),
			'id'   => 'stripe_payment_image',
			'type' => 'upload'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'payment_methods:has(bank)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'payment_style:is(style_1)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Upload the custom image of tab.','wpqa'),
			'id'   => 'bank_tab_image',
			'type' => 'upload'
		);

		$options[] = array(
			"name" => esc_html__("Choose the width of the tab image","wpqa"),
			"id"   => "bank_tab_image_width",
			"type" => "sliderui",
			'std'  => "100",
			"step" => "50",
			"min"  => "50",
			"max"  => "300"
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Bank transfer','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name'     => esc_html__('Bank transfer details','wpqa'),
			'id'       => 'bank_transfer_details',
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);

		$options[] = array(
			'name' => esc_html__('Upload the custom image of the bottom image.','wpqa'),
			'id'   => 'bank_payment_image',
			'type' => 'upload'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'payment_methods:has(custom)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Custom Payment','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			"name" => esc_html__("Custom payment tab name","wpqa"),
			"id"   => "custom_payment_tab",
			"type" => "text",
			'std'  => "Custom payment"
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'payment_style:is(style_1)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Upload the custom image of tab.','wpqa'),
			'id'   => 'custom_tab_image',
			'type' => 'upload'
		);

		$options[] = array(
			"name" => esc_html__("Choose the width of the tab image","wpqa"),
			"id"   => "custom_tab_image_width",
			"type" => "sliderui",
			'std'  => "100",
			"step" => "50",
			"min"  => "50",
			"max"  => "300"
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'     => esc_html__('Custom payment details','wpqa'),
			'id'       => 'custom_payment_details',
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);

		$options[] = array(
			'name' => esc_html__('Upload the custom image of the bottom image.','wpqa'),
			'id'   => 'custom_payment_image',
			'type' => 'upload'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'payment_methods:has(custom2)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Custom Payment 2','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			"name" => esc_html__("Custom payment tab name","wpqa"),
			"id"   => "custom_payment_tab2",
			"type" => "text",
			'std'  => "Custom payment"
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'payment_style:is(style_1)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Upload the custom image of tab.','wpqa'),
			'id'   => 'custom2_tab_image',
			'type' => 'upload'
		);

		$options[] = array(
			"name" => esc_html__("Choose the width of the tab image","wpqa"),
			"id"   => "custom2_tab_image_width",
			"type" => "sliderui",
			'std'  => "100",
			"step" => "50",
			"min"  => "50",
			"max"  => "300"
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'     => esc_html__('Custom payment details','wpqa'),
			'id'       => 'custom_payment_details2',
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);

		$options[] = array(
			'name' => esc_html__('Upload the custom image of the bottom image.','wpqa'),
			'id'   => 'custom2_payment_image',
			'type' => 'upload'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Currencies','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name'    => esc_html__('Default currency code','wpqa'),
			'desc'    => esc_html__('Choose form here the default currency code.','wpqa'),
			'id'      => 'currency_code',
			'std'     => 'USD',
			'type'    => "select",
			'options' => $currencies
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the multi currencies','wpqa'),
			'desc' => esc_html__('Select ON to activate multi currencies.','wpqa'),
			'id'   => 'activate_currencies',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'       => esc_html__('Select the multi currencies','wpqa'),
			'id'         => 'multi_currencies',
			'type'       => 'multicheck',
			'strtolower' => 'not',
			'condition'  => 'activate_currencies:not(0)',
			'options'    => $currencies,
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Pay to ask','wpqa'),
			'id'   => 'pay_to_ask',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Pay to ask question','wpqa'),
			'desc' => esc_html__('Select ON to activate pay to ask question.','wpqa'),
			'id'   => 'pay_ask',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'pay_ask:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Payment way','wpqa'),
			'desc'    => esc_html__('Choose the payment way for the ask question','wpqa'),
			'id'      => 'payment_type_ask',
			'std'     => 'payments',
			'type'    => 'radio',
			'options' => array(
				"payments"        => esc_html__('Payment methods','wpqa'),
				"points"          => esc_html__('By points','wpqa'),
				"payments_points" => esc_html__('Payment methods and points','wpqa')
			)
		);

		$options[] = array(
			'name'    => esc_html__('Question payment style','wpqa'),
			'desc'    => esc_html__('Choose the asking question payment style','wpqa'),
			'id'      => 'ask_payment_style',
			'std'     => 'once',
			'type'    => 'radio',
			'options' => array(
				"once"     => esc_html__('Once payment','wpqa'),
				"packages" => esc_html__('Packages payment','wpqa')
			)
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_payment_style:is(packages)',
			'type'      => 'heading-2'
		);

		if (is_array($multi_currencies) && !empty($multi_currencies)) {
			foreach ($multi_currencies as $key_currency => $value_currency) {
				if ($value_currency != "0") {
					$ask_packages_price_multi[] = array(
						"name"      => esc_html__("With price for","wpqa")." ".$value_currency,
						"id"        => "package_price_".strtolower($value_currency),
						'condition' => 'payment_type_ask:not(points),activate_currencies:not(0)',
						"type"      => "text",
					);
				}
			}
		}

		$ask_packages_price = array(array(
			"type"      => "text",
			"id"        => "package_price",
			'condition' => 'payment_type_ask:not(points),activate_currencies:not(on)',
			"name"      => esc_html__('With price','wpqa')
		));

		$ask_packages_array = array(
			array(
				"type" => "text",
				"id"   => "package_name",
				"name" => esc_html__('Package name','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "package_description",
				"name" => esc_html__('Package description','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "package_posts",
				"name" => esc_html__('Package questions','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "package_points",
				"name" => esc_html__('With points','wpqa')
			),
			array(
				'type' => 'checkbox',
				"id"   => "sticky",
				"name" => esc_html__('Make any question in this package sticky','wpqa')
			),
			array(
				"type"      => "slider",
				"name"      => esc_html__("How many days would you like to make the question sticky?","wpqa"),
				"id"        => "days_sticky",
				"std"       => "7",
				"step"      => "1",
				"min"       => "1",
				"max"       => "365",
				"value"     => "1",
				'condition' => '[%id%]sticky:is(on)',
			),
		);

		$ask_packages_price_multi = (isset($ask_packages_price_multi) && is_array($ask_packages_price_multi) && !empty($ask_packages_price_multi)?$ask_packages_price_multi:array());
		$ask_packages_elements = array_merge($ask_packages_array,$ask_packages_price,$ask_packages_price_multi);

		$options[] = array(
			'id'      => "ask_packages",
			'type'    => "elements",
			'sort'    => "no",
			'hide'    => "yes",
			'button'  => esc_html__('Add a new package','wpqa'),
			'options' => $ask_packages_elements,
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_payment_style:not(packages)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			"name"      => esc_html__("What's the price to ask a new question?","wpqa"),
			"desc"      => esc_html__("Type here price to ask a new question","wpqa"),
			"id"        => "pay_ask_payment",
			"type"      => "text",
			'condition' => 'payment_type_ask:not(points),activate_currencies:not(on)',
			'std'       => 10
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'payment_type_ask:not(points),activate_currencies:not(0)',
			'type'      => 'heading-2'
		);
		
		if (is_array($multi_currencies) && !empty($multi_currencies)) {
			$options[] = array(
				'name' => esc_html__("What's the price to ask a new question?","wpqa"),
				'type' => 'info'
			);
			foreach ($multi_currencies as $key_currency => $value_currency) {
				if ($value_currency != "0") {
					$options[] = array(
						"name" => esc_html__("Price for","wpqa")." ".$value_currency,
						"id"   => "pay_ask_payment_".strtolower($value_currency),
						"type" => "text",
						'std'  => 10
					);
				}
			}
		}

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			"name"      => esc_html__("How many points to ask a new question?","wpqa"),
			"desc"      => esc_html__("Type here points of the payment to ask a new question","wpqa"),
			"id"        => "ask_payment_points",
			"type"      => "text",
			'condition' => 'payment_type_ask:is(points),payment_type_ask:is(payments_points)',
			'operator'  => 'or',
			'std'       => 20
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Pay for sticky question','wpqa'),
			'id'   => 'pay_to_sticky',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Pay for sticky question at the top','wpqa'),
			'desc' => esc_html__('Select ON to active the pay for sticky question.','wpqa'),
			'id'   => 'pay_to_sticky',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'pay_to_sticky:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Payment way','wpqa'),
			'desc'    => esc_html__('Choose the payment way for the sticky the question','wpqa'),
			'id'      => 'payment_type_sticky',
			'std'     => 'payments',
			'type'    => 'radio',
			'options' => array(
				"payments"        => esc_html__('Payment methods','wpqa'),
				"points"          => esc_html__('By points','wpqa'),
				"payments_points" => esc_html__('Payment methods and points','wpqa')
			)
		);

		$options[] = array(
			"name"      => esc_html__("What is the price to make the question sticky?","wpqa"),
			"desc"      => esc_html__("Type here the price of the payment to make the question sticky.","wpqa"),
			"id"        => "pay_sticky_payment",
			"type"      => "text",
			'condition' => 'payment_type_sticky:not(points),activate_currencies:not(on)',
			'std'       => 5
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'payment_type_ask:not(points),activate_currencies:not(0)',
			'type'      => 'heading-2'
		);
		
		if (is_array($multi_currencies) && !empty($multi_currencies)) {
			$options[] = array(
				'name' => esc_html__("What is the price to make the question sticky?","wpqa"),
				'type' => 'info'
			);
			foreach ($multi_currencies as $key_currency => $value_currency) {
				if ($value_currency != "0") {
					$options[] = array(
						"name" => esc_html__("Price for","wpqa")." ".$value_currency,
						"id"   => "pay_sticky_payment_".strtolower($value_currency),
						"type" => "text",
						'std'  => 5
					);
				}
			}
		}

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			"name"      => esc_html__("How many points to make the question sticky?","wpqa"),
			"desc"      => esc_html__("Type here points of the payment to sticky the question","wpqa"),
			"id"        => "sticky_payment_points",
			"type"      => "text",
			'condition' => 'payment_type_sticky:is(points),payment_type_sticky:is(payments_points)',
			'operator'  => 'or',
			'std'       => 10
		);
		
		$options[] = array(
			"name" => esc_html__("How many days would you like to make the question sticky?","wpqa"),
			"desc" => esc_html__("Type here days of the payment to sticky the question.","wpqa"),
			"id"   => "days_sticky",
			"type" => "sliderui",
			'std'  => "7",
			"step" => "1",
			"min"  => "1",
			"max"  => "365"
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Pay to answer','wpqa'),
			'id'   => 'pay_to_answer',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Pay to add answer','wpqa'),
			'desc' => esc_html__('Select ON to activate pay to answer.','wpqa'),
			'id'   => 'pay_answer',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'pay_answer:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Payment way','wpqa'),
			'desc'    => esc_html__('Choose the payment way for the answer','wpqa'),
			'id'      => 'payment_type_answer',
			'std'     => 'payments',
			'type'    => 'radio',
			'options' => array(
				"payments"        => esc_html__('Payment methods','wpqa'),
				"points"          => esc_html__('By points','wpqa'),
				"payments_points" => esc_html__('Payment methods and points','wpqa')
			)
		);
		
		$options[] = array(
			"name"      => esc_html__("What's the price to add a new answer?","wpqa"),
			"desc"      => esc_html__("Type here price to add a new answer","wpqa"),
			"id"        => "pay_answer_payment",
			"type"      => "text",
			'condition' => 'payment_type_answer:not(points),activate_currencies:not(on)',
			'std'       => 10
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'payment_type_answer:not(points),activate_currencies:not(0)',
			'type'      => 'heading-2'
		);
		
		if (is_array($multi_currencies) && !empty($multi_currencies)) {
			$options[] = array(
				'name' => esc_html__("What's the price to add a new answer?","wpqa"),
				'type' => 'info'
			);
			foreach ($multi_currencies as $key_currency => $value_currency) {
				if ($value_currency != "0") {
					$options[] = array(
						"name" => esc_html__("Price for","wpqa")." ".$value_currency,
						"id"   => "pay_answer_payment_".strtolower($value_currency),
						"type" => "text",
						'std'  => 10
					);
				}
			}
		}
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			"name"      => esc_html__("How many points to add a new answer?","wpqa"),
			"desc"      => esc_html__("Type here points of the payment to add a new answer","wpqa"),
			"id"        => "answer_payment_points",
			"type"      => "text",
			'condition' => 'payment_type_answer:is(points),payment_type_answer:is(payments_points)',
			'operator'  => 'or',
			'std'       => 20
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$subscriptions_group = $wpqa_options_roles;
		unset($subscriptions_group["administrator"]);
		
		$options[] = array(
			'name' => esc_html__('Subscriptions','wpqa'),
			'id'   => 'subscriptions',
			'type' => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Subscriptions','wpqa'),
			'desc' => esc_html__('Select ON to activate subscriptions.','wpqa'),
			'id'   => 'subscriptions_payment',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'subscriptions_payment:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Subscriptions slug','wpqa'),
			'desc' => esc_html__('Put the subscriptions slug.','wpqa'),
			'id'   => 'subscriptions_slug',
			'std'  => 'subscriptions',
			'type' => 'text'
		);

		$options[] = array(
			'name' => '<a href="'.wpqa_subscriptions_permalink().'" target="_blank">'.esc_html__('The Link For The Subscriptions Page.','wpqa').'</a>',
			'type' => 'info'
		);

		$options[] = array(
			'name' => '<a href="https://2code.info/docs/'.wpqa_prefix_theme.'/subscription/" target="_blank">'.esc_html__('To make the paid subscriptions work well, check this link.','wpqa').'</a>',
			'type' => 'info'
		);

		$options[] = array(
			'name'    => esc_html__('Payment way','wpqa'),
			'desc'    => esc_html__('Choose the payment way for the subscriptions','wpqa'),
			'id'      => 'payment_type_subscriptions',
			'std'     => 'payments',
			'type'    => 'radio',
			'options' => array(
				"payments"        => esc_html__('Payment methods','wpqa'),
				"points"          => esc_html__('By points','wpqa'),
				"payments_points" => esc_html__('Payment methods and points','wpqa')
			)
		);

		$options = apply_filters('wpqa_after_subscriptions_payment',$options);

		$options[] = array(
			'name' => esc_html__('Cancel the subscription','wpqa'),
			'desc' => esc_html__('Select ON to active the cancel subscription button for the users.','wpqa'),
			'id'   => 'cancel_subscription',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Change the subscription plans','wpqa'),
			'desc' => esc_html__('Select ON to activate the change subscription plans for the users.','wpqa'),
			'id'   => 'change_subscription',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Trial subscription plans','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name'    => esc_html__('Choose the role of the trial subscriptions','wpqa'),
			'id'      => 'trial_group',
			'std'     => 'author',
			'type'    => 'select',
			'options' => $subscriptions_group
		);

		$options[] = array(
			'name' => esc_html__('Allow the users to try the subscription plans','wpqa'),
			'desc' => esc_html__('Select ON to activate to allow the users to try the subscription plans.','wpqa'),
			'id'   => 'trial_subscription',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'trial_subscription:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Select the options for the free trial subscriptions','wpqa'),
			'id'      => 'trial_subscription_plan',
			'type'    => 'radio',
			'std'     => 'hour',
			'options' => array(
				"hour"  => esc_html__('Hour','wpqa'),
				"week"  => esc_html__('Week','wpqa'),
				"month" => esc_html__('Month','wpqa'),
			)
		);

		$options[] = array(
			'name' => esc_html__('Choose the number of hours, weeks, or months for the trial plan','wpqa'),
			"id"   => "trial_subscription_rang",
			"type" => "sliderui",
			'std'  => '2',
			"step" => "1",
			"min"  => "1",
			"max"  => "10"
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Reward subscription','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Allow the users to join the subscription plans based on the activities','wpqa'),
			'desc' => esc_html__('Select ON to allow the users to join the subscription plans based on activities like asking questions and adding answers.','wpqa'),
			'id'   => 'reward_subscription',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'reward_subscription:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Choose the role of the reward subscriptions','wpqa'),
			'id'      => 'reward_group',
			'std'     => 'author',
			'type'    => 'select',
			'options' => $subscriptions_group
		);

		$options[] = array(
			'name'    => esc_html__('Select the plan to allow the user to join it automatically based on the activities','wpqa'),
			'id'      => 'reward_subscription_plan',
			'type'    => 'radio',
			'std'     => 'month',
			'options' => array(
				"week"  => esc_html__('Week','wpqa'),
				"month" => esc_html__('Month','wpqa'),
			)
		);

		$options[] = array(
			'name' => esc_html__('Choose the number of weeks, or months for the reward plan','wpqa'),
			"id"   => "reward_subscription_rang",
			"type" => "sliderui",
			'std'  => '1',
			"step" => "1",
			"min"  => "1",
			"max"  => "12"
		);

		$options[] = array(
			'name' => esc_html__("Note: anything you don't need for the reward subscription only put on it 0","wpqa"),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Choose the number of questions in the month to join the paid subscription plan','wpqa'),
			"id"   => "reward_questions_subscription",
			"type" => "text",
			'std'  => 40,
		);

		$options[] = array(
			'name' => esc_html__('Choose the number of answers in the month to join the paid subscription plan','wpqa'),
			"id"   => "reward_answers_subscription",
			"type" => "text",
			'std'  => 100,
		);

		$options[] = array(
			'name' => esc_html__('Choose the number of best answers in the month to join the paid subscription plan','wpqa'),
			"id"   => "reward_best_answers_subscription",
			"type" => "text",
			'std'  => 20,
		);

		$options[] = array(
			'name' => esc_html__('Choose the number of posts in the month to join the paid subscription plan','wpqa'),
			"id"   => "reward_posts_subscription",
			"type" => "text",
			'std'  => 30,
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Subscription plans','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Switch between yearly or monthly plans button','wpqa'),
			'desc' => esc_html__('Select ON to activate the switch between yearly or monthly plans.','wpqa'),
			'id'   => 'switch_yearly_monthly_plans',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'      => esc_html__('If you activate this switch, It will show only the yearly or monthly plans','wpqa'),
			'condition' => 'switch_yearly_monthly_plans:is(on)',
			'type'      => 'info'
		);

		$options[] = array(
			'name'    => esc_html__('Plans design style','wpqa'),
			'desc'    => esc_html__('Choose the plans design style','wpqa'),
			'id'      => 'subscriptions_style',
			'std'     => 'style_1',
			'type'    => 'radio',
			'options' => array(
				"style_1" => esc_html__('Style 1','wpqa'),
				"style_2" => esc_html__('Style 2','wpqa')
			)
		);

		$subscribe_monthly_info = $subscribe_monthly = $subscribe_3months_info = $subscribe_3months = $subscribe_6months_info = $subscribe_6months = $subscribe_yearly_info = $subscribe_yearly = $subscribe_lifetime_info = $subscribe_lifetime = array();

		$subscription_1 = array(
			array(
				"id"   => "id",
				'type' => 'uniq_id',
			),
			array(
				'name' => esc_html__('Plan name','wpqa'),
				'id'   => 'subscriptions_name',
				'type' => 'text',
			),
			array(
				'name'    => esc_html__('Paid role for the subscriptions','wpqa'),
				'desc'    => esc_html__('Select the paid role for the subscriptions','wpqa'),
				'id'      => 'subscriptions_group',
				'std'     => 'author',
				'type'    => 'select',
				'options' => $subscriptions_group
			),
			array(
				'name' => esc_html__('Popular plan','wpqa'),
				'id'   => 'popular',
				'type' => 'checkbox',
			),
			array(
				'name'    => esc_html__('Select the options for the subscriptions','wpqa'),
				'id'      => 'subscriptions_options',
				'type'    => 'multicheck',
				'std'     => array(
					"monthly"  => "monthly",
					"3months"  => "3months",
					"6months"  => "6months",
					"yearly"   => "yearly",
					"lifetime" => "lifetime",
				),
				'options' => array(
					"monthly"  => esc_html__('Monthly','wpqa'),
					"3months"  => esc_html__('Three months','wpqa'),
					"6months"  => esc_html__('Six months','wpqa'),
					"yearly"   => esc_html__('Yearly','wpqa'),
					"lifetime" => esc_html__('Lifetime','wpqa'),
				)
			),
			array(
				'div'       => 'div',
				'condition' => 'activate_currencies:not(on)',
				'type'      => 'heading-2'
			),
			array(
				"name"      => esc_html__("What's the price to subscribe monthly?",'wpqa'),
				"id"        => "subscribe_monthly",
				"type"      => "text",
				'condition' => '[%id%]subscriptions_options:has(monthly)',
				'std'       => 10
			),
			array(
				"name"      => esc_html__("What's the price to subscribe for three months?",'wpqa'),
				"id"        => "subscribe_3months",
				"type"      => "text",
				'condition' => '[%id%]subscriptions_options:has(3months)',
				'std'       => 25
			),
			array(
				"name"      => esc_html__("What's the price to subscribe for six months?",'wpqa'),
				"id"        => "subscribe_6months",
				"type"      => "text",
				'condition' => '[%id%]subscriptions_options:has(6months)',
				'std'       => 45
			),
			array(
				"name"      => esc_html__("What's the price to subscribe yearly?",'wpqa'),
				"id"        => "subscribe_yearly",
				"type"      => "text",
				'condition' => '[%id%]subscriptions_options:has(yearly)',
				'std'       => 80
			),
			array(
				"name"      => esc_html__("What's the price to subscribe lifetime?",'wpqa'),
				"id"        => "subscribe_lifetime",
				"type"      => "text",
				'condition' => '[%id%]subscriptions_options:has(lifetime)',
				'std'       => 200
			),
			array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			),
			array(
				'div'       => 'div',
				'condition' => 'activate_currencies:not(0)',
				'type'      => 'heading-2'
			),
			array(
				'div'       => 'div',
				'condition' => 'subscriptions_options:has(monthly)',
				'condition' => '[%id%]subscriptions_options:has(monthly)',
				'type'      => 'heading-2'
			),
		);
		
		if (is_array($multi_currencies) && !empty($multi_currencies)) {
			$subscribe_monthly_info = array(
				array(
					'name' => esc_html__("What's the price to subscribe monthly?","wpqa"),
					'type' => 'info'
				)
			);
			foreach ($multi_currencies as $key_currency => $value_currency) {
				if ($value_currency != "0") {
					$subscribe_monthly = array(
						array(
							"name" => esc_html__("Price for","wpqa")." ".$value_currency,
							"id"   => "subscribe_monthly_".strtolower($value_currency),
							"type" => "text",
							'std'  => 10
						)
					);
				}
			}
		}

		$subscription_2 = array(
			array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			),
			array(
				'div'       => 'div',
				'condition' => '[%id%]subscriptions_options:has(3months)',
				'type'      => 'heading-2'
			)
		);
		
		if (is_array($multi_currencies) && !empty($multi_currencies)) {
			$subscribe_3months_info = array(
				array(
					'name' => esc_html__("What's the price to subscribe for three months?","wpqa"),
					'type' => 'info'
				)
			);
			foreach ($multi_currencies as $key_currency => $value_currency) {
				if ($value_currency != "0") {
					$subscribe_3months = array(
						array(
							"name" => esc_html__("Price for","wpqa")." ".$value_currency,
							"id"   => "subscribe_3months_".strtolower($value_currency),
							"type" => "text",
							'std'  => 25
						)
					);
				}
			}
		}

		$subscription_3 = array(
			array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			),
			array(
				'div'       => 'div',
				'condition' => '[%id%]subscriptions_options:has(3months)',
				'type'      => 'heading-2'
			)
		);
		
		if (is_array($multi_currencies) && !empty($multi_currencies)) {
			$subscribe_6months_info = array(
				array(
					'name' => esc_html__("What's the price to subscribe for six months?","wpqa"),
					'type' => 'info'
				)
			);
			foreach ($multi_currencies as $key_currency => $value_currency) {
				if ($value_currency != "0") {
					$subscribe_6months = array(
						array(
							"name" => esc_html__("Price for","wpqa")." ".$value_currency,
							"id"   => "subscribe_6months_".strtolower($value_currency),
							"type" => "text",
							'std'  => 45
						)
					);
				}
			}
		}

		$subscription_4 = array(
			array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			),
			array(
				'div'       => 'div',
				'condition' => '[%id%]subscriptions_options:has(yearly)',
				'type'      => 'heading-2'
			)
		);
			
		if (is_array($multi_currencies) && !empty($multi_currencies)) {
			$subscribe_yearly_info = array(
				array(
					'name' => esc_html__("What's the price to subscribe yearly?","wpqa"),
					'type' => 'info'
				)
			);
			foreach ($multi_currencies as $key_currency => $value_currency) {
				if ($value_currency != "0") {
					$subscribe_yearly = array(
						array(
							"name" => esc_html__("Price for","wpqa")." ".$value_currency,
							"id"   => "subscribe_yearly_".strtolower($value_currency),
							"type" => "text",
							'std'  => 80
						)
					);
				}
			}
		}

		$subscription_5 = array(
			array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			),
			array(
				'div'       => 'div',
				'condition' => '[%id%]subscriptions_options:has(lifetime)',
				'type'      => 'heading-2'
			)
		);
			
		if (is_array($multi_currencies) && !empty($multi_currencies)) {
			$subscribe_lifetime_info = array(
				array(
					'name' => esc_html__("What's the price to subscribe lifetime?","wpqa"),
					'type' => 'info'
				)
			);
			foreach ($multi_currencies as $key_currency => $value_currency) {
				if ($value_currency != "0") {
					$subscribe_lifetime = array(
						array(
							"name" => esc_html__("Price for","wpqa")." ".$value_currency,
							"id"   => "subscribe_lifetime_".strtolower($value_currency),
							"type" => "text",
							'std'  => 200
						)
					);
				}
			}
		}

		$subscription_6 = array(
			array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			),
			array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			),
			array(
				'div'       => 'div',
				'condition' => 'payment_type_subscriptions:has(points),payment_type_subscriptions:has(payments_points)',
				'operator'  => 'or',
				'type'      => 'heading-2'
			),
			array(
				'name' => esc_html__("Price with points to allow the users to subscribe","wpqa"),
				'type' => 'info'
			),
			array(
				"name"      => esc_html__("What's the points to subscribe monthly?",'wpqa'),
				"id"        => "subscribe_monthly_points",
				"type"      => "text",
				'condition' => '[%id%]subscriptions_options:has(monthly)',
				'std'       => 100
			),
			array(
				"name"      => esc_html__("What's the points to subscribe for three months?",'wpqa'),
				"id"        => "subscribe_3months_points",
				"type"      => "text",
				'condition' => '[%id%]subscriptions_options:has(3months)',
				'std'       => 250
			),
			array(
				"name"      => esc_html__("What's the points to subscribe for six months?",'wpqa'),
				"id"        => "subscribe_6months_points",
				"type"      => "text",
				'condition' => '[%id%]subscriptions_options:has(6months)',
				'std'       => 400
			),
			array(
				"name"      => esc_html__("What's the points to subscribe yearly?",'wpqa'),
				"id"        => "subscribe_yearly_points",
				"type"      => "text",
				'condition' => '[%id%]subscriptions_options:has(yearly)',
				'std'       => 700
			),
			array(
				"name"      => esc_html__("What's the points to subscribe for lifetime?",'wpqa'),
				"id"        => "subscribe_lifetime_points",
				"type"      => "text",
				'condition' => '[%id%]subscriptions_options:has(lifetime)',
				'std'       => 2000
			),
			array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			),
			array(
				'id'        => "subscription_features",
				'type'      => "elements",
				'condition' => 'subscriptions_style:is(style_2)',
				'button'    => esc_html__('Add a new feature','wpqa'),
				'hide'      => "yes",
				'options'   => array(
					array(
						"type" => "text",
						"id"   => "feature",
						"name" => esc_html__('Add a new feature','wpqa'),
					),
				),
			)
		);

		$subscription_plans = array_merge($subscription_1,$subscribe_monthly_info,$subscribe_monthly,$subscription_2,$subscribe_3months_info,$subscribe_3months,$subscription_3,$subscribe_6months_info,$subscribe_6months,$subscription_4,$subscribe_yearly_info,$subscribe_yearly,$subscription_5,$subscribe_lifetime_info,$subscribe_lifetime,$subscription_6);

		$subscription_std = array(
			1 => array(
				"id" => 1,
				"subscriptions_name" => esc_html__("Paid membership","wpqa"),
				"subscriptions_group" => wpqa_options("subscriptions_group"),
				"subscriptions_options" => array(
					"monthly"  => "monthly",
					"3months"  => "3months",
					"6months"  => "6months",
					"yearly"   => "yearly",
					"lifetime" => "lifetime"
				),
				"subscribe_monthly" => wpqa_options("subscribe_monthly"),
				"subscribe_3months" => wpqa_options("subscribe_3months"),
				"subscribe_6months" => wpqa_options("subscribe_6months"),
				"subscribe_yearly" => wpqa_options("subscribe_yearly"),
				"subscribe_lifetime" => wpqa_options("subscribe_lifetime"),
				"subscribe_monthly_points" => wpqa_options("subscribe_monthly_points"),
				"subscribe_3months_points" => wpqa_options("subscribe_3months_points"),
				"subscribe_6months_points" => wpqa_options("subscribe_6months_points"),
				"subscribe_yearly_points" => wpqa_options("subscribe_yearly_points"),
				"subscribe_lifetime_points" => wpqa_options("subscribe_lifetime_points"),
			)
		);

		$options[] = array(
			'id'      => "subscription_plans",
			'type'    => "elements",
			'button'  => esc_html__('Add a new pan','wpqa'),
			'hide'    => "yes",
			'options' => $subscription_plans,
			'std'     => $subscription_std
		);

		$options[] = array(
			'name' => esc_html__('Subscriptions features','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Custom features for the subscriptions','wpqa'),
			'desc' => esc_html__('Select ON to activate the custom features for the subscriptions.','wpqa'),
			'id'   => 'activate_features_subscriptions',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'activate_features_subscriptions:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Payment way','wpqa'),
			'desc'    => esc_html__('Choose the payment way for the add post','wpqa'),
			'id'      => 'features_subscriptions_type',
			'std'     => 'permissions',
			'type'    => 'radio',
			'options' => array(
				"permissions" => esc_html__('Permissions','wpqa'),
				"list"        => esc_html__('List','wpqa'),
				"text"        => esc_html__('Custom text','wpqa')
			)
		);

		$options[] = array(
			'name'       => esc_html__('Features subscriptions text','wpqa'),
			'id'         => 'features_subscriptions_text',
			'type'       => 'editor',
			'condition'  => 'features_subscriptions_type:is(text)',
			'settings'   => $wp_editor_settings
		);

		$options[] = array(
			'id'        => "features_subscriptions",
			'type'      => "elements",
			'button'    => esc_html__('Add a new item','wpqa'),
			'condition' => 'features_subscriptions_type:is(list)',
			'hide'      => "yes",
			'options'   => array(
				array(
					"type" => "text",
					"id"   => "text",
					"name" => esc_html__('Item','wpqa'),
				),
			),
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('FAQs for the subscriptions','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('FAQs for the subscriptions','wpqa'),
			'desc' => esc_html__('Select ON to activate the FAQs for the subscriptions.','wpqa'),
			'id'   => 'activate_faqs_subscriptions',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'activate_faqs_subscriptions:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'     => esc_html__('FAQs subscriptions text','wpqa'),
			'id'       => 'faqs_subscriptions_text',
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);

		$options[] = array(
			'id'      => "faqs_subscriptions",
			'type'    => "elements",
			'button'  => esc_html__('Add a new faq','wpqa'),
			'hide'    => "yes",
			'options' => array(
				array(
					"type" => "text",
					"id"   => "text",
					"name" => esc_html__('Title','wpqa'),
				),
				array(
					"type" => "textarea",
					"id"   => "textarea",
					"name" => esc_html__('Content','wpqa'),
				),
			),
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Pay to post','wpqa'),
			'id'   => 'pay_to_post',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Pay to add post','wpqa'),
			'desc' => esc_html__('Select ON to activate the pay to add post.','wpqa'),
			'id'   => 'pay_post',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'pay_post:not(0)',
			'type'      => 'heading-2'
		);

		$options = apply_filters(wpqa_prefix_theme."_filter_inner_pay_post",$options);

		$options[] = array(
			'name'    => esc_html__('Payment way','wpqa'),
			'desc'    => esc_html__('Choose the payment way for the add post','wpqa'),
			'id'      => 'payment_type_post',
			'std'     => 'payments',
			'type'    => 'radio',
			'options' => array(
				"payments"        => esc_html__('Payment methods','wpqa'),
				"points"          => esc_html__('By points','wpqa'),
				"payments_points" => esc_html__('Payment methods and points','wpqa')
			)
		);

		$options[] = array(
			'name'    => esc_html__('Post payment style','wpqa'),
			'desc'    => esc_html__('Choose the adding post payment style','wpqa'),
			'id'      => 'post_payment_style',
			'std'     => 'once',
			'type'    => 'radio',
			'options' => array(
				"once"     => esc_html__('Once payment','wpqa'),
				"packages" => esc_html__('Packages payment','wpqa')
			)
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'post_payment_style:is(packages)',
			'type'      => 'heading-2'
		);

		if (is_array($multi_currencies) && !empty($multi_currencies)) {
			foreach ($multi_currencies as $key_currency => $value_currency) {
				if ($value_currency != "0") {
					$post_packages_price_multi[] = array(
						"name"      => esc_html__("With price for","wpqa")." ".$value_currency,
						"id"        => "package_price_".strtolower($value_currency),
						'condition' => 'payment_type_post:not(points),activate_currencies:not(0)',
						"type"      => "text",
					);
				}
			}
		}

		$post_packages_price = array(array(
			"type"      => "text",
			"id"        => "package_price",
			'condition' => 'payment_type_post:not(points),activate_currencies:not(on)',
			"name"      => esc_html__('With price','wpqa')
		));

		$post_packages_array = array(
			array(
				"type" => "text",
				"id"   => "package_name",
				"name" => esc_html__('Package name','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "package_description",
				"name" => esc_html__('Package description','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "package_posts",
				"name" => esc_html__('Package posts','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "package_points",
				"name" => esc_html__('With points','wpqa')
			),
			array(
				'type' => 'checkbox',
				"id"   => "sticky",
				"name" => esc_html__('Make any post in this package sticky','wpqa')
			),
			array(
				"type"      => "slider",
				"name"      => esc_html__("How many days would you like to make the post sticky?","wpqa"),
				"id"        => "days_sticky",
				"std"       => "7",
				"step"      => "1",
				"min"       => "1",
				"max"       => "365",
				"value"     => "1",
				'condition' => '[%id%]sticky:is(on)',
			),
		);

		$post_packages_price_multi = (isset($post_packages_price_multi) && is_array($post_packages_price_multi) && !empty($post_packages_price_multi)?$post_packages_price_multi:array());
		$post_packages_elements = array_merge($post_packages_array,$post_packages_price,$post_packages_price_multi);

		$options[] = array(
			'id'      => "post_packages",
			'type'    => "elements",
			'sort'    => "no",
			'hide'    => "yes",
			'button'  => esc_html__('Add a new package','wpqa'),
			'options' => $post_packages_elements,
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'post_payment_style:not(packages)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			"name"      => esc_html__("What's the price to add a new post?","wpqa"),
			"desc"      => esc_html__("Type here price to add a new post","wpqa"),
			"id"        => "pay_post_payment",
			"type"      => "text",
			'condition' => 'payment_type_post:not(points),activate_currencies:not(on)',
			'std'       => 10
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'payment_type_post:not(points),activate_currencies:not(0)',
			'type'      => 'heading-2'
		);
		
		if (is_array($multi_currencies) && !empty($multi_currencies)) {
			$options[] = array(
				'name' => esc_html__("What's the price to add a new post?","wpqa"),
				'type' => 'info'
			);
			foreach ($multi_currencies as $key_currency => $value_currency) {
				if ($value_currency != "0") {
					$options[] = array(
						"name" => esc_html__("Price for","wpqa")." ".$value_currency,
						"id"   => "pay_post_payment_".strtolower($value_currency),
						"type" => "text",
						'std'  => 10
					);
				}
			}
		}

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			"name"      => esc_html__("How many points to add a new post?","wpqa"),
			"desc"      => esc_html__("Type here points of the payment to add a new post","wpqa"),
			"id"        => "post_payment_points",
			"type"      => "text",
			'condition' => 'payment_type_post:is(points),payment_type_post:is(payments_points)',
			'operator'  => 'or',
			'std'       => 20
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Pay to create group','wpqa'),
			'id'   => 'pay_create_group',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Pay to create group','wpqa'),
			'desc' => esc_html__('Select ON to activate the pay to create group.','wpqa'),
			'id'   => 'pay_group',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'pay_group:not(0)',
			'type'      => 'heading-2'
		);

		$options = apply_filters(wpqa_prefix_theme."_filter_inner_pay_group",$options);

		$options[] = array(
			'name'    => esc_html__('Payment way','wpqa'),
			'desc'    => esc_html__('Choose the payment way for the create group','wpqa'),
			'id'      => 'payment_type_group',
			'std'     => 'payments',
			'type'    => 'radio',
			'options' => array(
				"payments"        => esc_html__('Payment methods','wpqa'),
				"points"          => esc_html__('By points','wpqa'),
				"payments_points" => esc_html__('Payment methods and points','wpqa')
			)
		);

		$options[] = array(
			'name'    => esc_html__('Group payment style','wpqa'),
			'desc'    => esc_html__('Choose the creating group payment style','wpqa'),
			'id'      => 'group_payment_style',
			'std'     => 'once',
			'type'    => 'radio',
			'options' => array(
				"once"     => esc_html__('Once payment','wpqa'),
				"packages" => esc_html__('Packages payment','wpqa')
			)
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'group_payment_style:is(packages)',
			'type'      => 'heading-2'
		);

		if (is_array($multi_currencies) && !empty($multi_currencies)) {
			foreach ($multi_currencies as $key_currency => $value_currency) {
				if ($value_currency != "0") {
					$group_packages_price_multi[] = array(
						"name"      => esc_html__("With price for","wpqa")." ".$value_currency,
						"id"        => "package_price_".strtolower($value_currency),
						'condition' => 'payment_type_group:not(points),activate_currencies:not(0)',
						"type"      => "text",
					);
				}
			}
		}

		$group_packages_price = array(array(
			"type"      => "text",
			"id"        => "package_price",
			'condition' => 'payment_type_group:not(points),activate_currencies:not(on)',
			"name"      => esc_html__('With price','wpqa')
		));

		$group_packages_array = array(
			array(
				"type" => "text",
				"id"   => "package_name",
				"name" => esc_html__('Package name','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "package_description",
				"name" => esc_html__('Package description','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "package_posts",
				"name" => esc_html__('Package groups','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "package_points",
				"name" => esc_html__('With points','wpqa')
			),
		);

		$group_packages_price_multi = (isset($group_packages_price_multi) && is_array($group_packages_price_multi) && !empty($group_packages_price_multi)?$group_packages_price_multi:array());
		$group_packages_elements = array_merge($group_packages_array,$group_packages_price,$group_packages_price_multi);

		$options[] = array(
			'id'      => "group_packages",
			'type'    => "elements",
			'sort'    => "no",
			'hide'    => "yes",
			'button'  => esc_html__('Add a new package','wpqa'),
			'options' => $group_packages_elements,
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'group_payment_style:not(packages)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			"name"      => esc_html__("What's the price to create a new group?","wpqa"),
			"desc"      => esc_html__("Type here price to create a new group","wpqa"),
			"id"        => "pay_group_payment",
			"type"      => "text",
			'condition' => 'payment_type_group:not(points),activate_currencies:not(on)',
			'std'       => 10
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'payment_type_group:not(points),activate_currencies:not(0)',
			'type'      => 'heading-2'
		);
		
		if (is_array($multi_currencies) && !empty($multi_currencies)) {
			$options[] = array(
				'name' => esc_html__("What's the price to create a new group?","wpqa"),
				'type' => 'info'
			);
			foreach ($multi_currencies as $key_currency => $value_currency) {
				if ($value_currency != "0") {
					$options[] = array(
						"name" => esc_html__("Price for","wpqa")." ".$value_currency,
						"id"   => "pay_group_payment_".strtolower($value_currency),
						"type" => "text",
						'std'  => 10
					);
				}
			}
		}

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			"name"      => esc_html__("How many points to create a new group?","wpqa"),
			"desc"      => esc_html__("Type here points of the payment to create a new group","wpqa"),
			"id"        => "group_payment_points",
			"type"      => "text",
			'condition' => 'payment_type_group:is(points),payment_type_group:is(payments_points)',
			'operator'  => 'or',
			'std'       => 20
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Buy points','wpqa'),
			'id'   => 'buy_points',
			'type' => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Buy points','wpqa'),
			'desc' => esc_html__('Select ON to activate buy points.','wpqa'),
			'id'   => 'buy_points_payment',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'buy_points_payment:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Buy points slug','wpqa'),
			'desc' => esc_html__('Put the buy points slug.','wpqa'),
			'id'   => 'buy_points_slug',
			'std'  => 'buy-points',
			'type' => 'text'
		);

		$options[] = array(
			'name' => '<a href="'.wpqa_buy_points_permalink().'" target="_blank">'.esc_html__('The Link For The Buy Points Page.','wpqa').'</a>',
			'type' => 'info'
		);

		if (is_array($multi_currencies) && !empty($multi_currencies)) {
			foreach ($multi_currencies as $key_currency => $value_currency) {
				if ($value_currency != "0") {
					$buy_points_price_multi[] = array(
						"name"      => esc_html__("With price for","wpqa")." ".$value_currency,
						"id"        => "package_price_".strtolower($value_currency),
						'condition' => 'activate_currencies:not(0)',
						"type"      => "text",
					);
				}
			}
		}

		$buy_points_price = array(array(
			"type"      => "text",
			"id"        => "package_price",
			'condition' => 'activate_currencies:not(on)',
			"name"      => esc_html__('With price','wpqa')
		));

		$buy_points_array = array(
			array(
				"type" => "text",
				"id"   => "package_name",
				"name" => esc_html__('Package name','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "package_points",
				"name" => esc_html__('Points','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "package_description",
				"name" => esc_html__('Package description','wpqa')
			)
		);

		$buy_points_price_multi = (isset($buy_points_price_multi) && is_array($buy_points_price_multi) && !empty($buy_points_price_multi)?$buy_points_price_multi:array());
		$buy_points_elements = array_merge($buy_points_array,$buy_points_price,$buy_points_price_multi);
		
		$options[] = array(
			'id'      => "buy_points",
			'type'    => "elements",
			'sort'    => "no",
			'hide'    => "yes",
			'button'  => esc_html__('Add a new package','wpqa'),
			'options' => $buy_points_elements,
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Pay to users','wpqa'),
			'id'   => 'pay_to_users',
			'type' => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Pay money to users','wpqa'),
			'desc' => esc_html__('Select ON to activate pay money to users.','wpqa'),
			'id'   => 'activate_pay_to_users',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'activate_pay_to_users:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Choose from the Users Settings/Permissions which role you want to pay for them','wpqa'),
			'type' => 'info'
		);

		$edit_profile_items_5 = array(
			'paypal'   => array('sort' => esc_html__('PayPal','wpqa'),'value' => 'paypal'),
			'payoneer' => array('sort' => esc_html__('Payoneer','wpqa'),'value' => 'payoneer'),
			'bank'     => array('sort' => esc_html__('Bank Transfer','wpqa'),'value' => 'bank'),
			"crypto"   => array("sort" => esc_html__('Cryptocurrency','wpqa'),"value" => ""),
		);
		
		$options[] = array(
			'name'    => esc_html__('Select what to show at edit profile to pay money for the users section','wpqa'),
			'id'      => 'edit_profile_items_5',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $edit_profile_items_5,
			'options' => $edit_profile_items_5
		);

		$options[] = array(
			'name' => esc_html__("Number of points to be converted to money?","wpqa"),
			'id'   => 'pay_minimum_points',
			'type' => 'text',
			'std'  => 100
		);

		$options[] = array(
			'name' => esc_html__("What's the money equal to the number of points?","wpqa"),
			'id'   => 'money_to_points',
			'type' => 'text',
			'std'  => 1
		);

		$options[] = array(
			'name' => esc_html__("What's the minimum money to allow the user to make the payment?","wpqa"),
			'id'   => 'pay_minimum_money',
			'type' => 'text',
			'std'  => 50
		);

		$options[] = array(
			'name' => esc_html__("What's the maximum money to allow the user to make the payment?","wpqa"),
			'id'   => 'pay_maximum_money',
			'type' => 'text',
			'std'  => 500
		);

		$options[] = array(
			'name' => esc_html__('Choose from the Users Settings/Permissions which role you want to exclude them from the maximum payment','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_before_coupons_setting',$options);
		
		$options[] = array(
			'name' => esc_html__('Coupon settings','wpqa'),
			'id'   => 'coupons_setting',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the Coupons','wpqa'),
			'desc' => esc_html__('Select ON to activate the coupons.','wpqa'),
			'id'   => 'active_coupons',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_coupons:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Show the free coupons when making any payment?','wpqa'),
			'desc' => esc_html__('Select ON to show the free coupons.','wpqa'),
			'id'   => 'free_coupons',
			'type' => 'checkbox'
		);
		
		$coupon_elements = array(
			array(
				"type" => "text",
				"id"   => "coupon_name",
				"name" => esc_html__('Coupons name','wpqa')
			),
			array(
				"type"    => "select",
				"id"      => "coupon_type",
				"name"    => esc_html__('Discount type','wpqa'),
				"options" => array("discount" => esc_html__("Discount","wpqa"),"percent" => esc_html__("% Percent","wpqa"))
			),
			array(
				"type" => "text",
				"id"   => "coupon_amount",
				"name" => esc_html__('Amount','wpqa')
			),
			array(
				"type" => "date",
				"id"   => "coupon_date",
				"name" => esc_html__('Expiry date','wpqa')
			)
		);
		
		$options[] = array(
			'id'      => "coupons",
			'type'    => "elements",
			'sort'    => "no",
			'hide'    => "yes",
			'button'  => esc_html__('Add a new coupon','wpqa'),
			'options' => $coupon_elements,
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_after_coupons_setting',$options);

		$groups_settings = array(
			"general_setting_g" => esc_html__('General settings','wpqa'),
			"group_slugs"       => esc_html__('Group slugs','wpqa'),
			"add_edit_delete_g" => esc_html__('Add - Edit - Delete','wpqa'),
			"group_posts"       => esc_html__('Group posts','wpqa')
		);

		$options[] = array(
			'name'    => esc_html__('Group settings','wpqa'),
			'id'      => 'group',
			'icon'    => 'groups',
			'type'    => 'heading',
			'std'     => 'general_setting_g',
			'options' => apply_filters(wpqa_prefix_theme."_groups_settings",$groups_settings)
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'general_setting_g',
			'name' => esc_html__('General settings','wpqa')
		);

		$options[] = array(
			'name' => esc_html__('Activate the groups','wpqa'),
			'id'   => 'active_groups',
			'type' => 'checkbox',
			'std'  => 'on'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_groups:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'      => esc_html__('Visits for group pages enable or disable.','wpqa'),
			'id'        => 'groups_visits',
			'std'       => 'on',
			'condition' => 'active_post_stats:not(0)',
			'type'      => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Activate the group rules in the top page of the group','wpqa'),
			'id'   => 'active_rules_groups',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'    => esc_html__('Pagination style','wpqa'),
			'desc'    => esc_html__('Choose pagination style from here.','wpqa'),
			'id'      => 'group_pagination',
			'options' => array(
				'standard'        => esc_html__('Standard','wpqa'),
				'pagination'      => esc_html__('Pagination','wpqa'),
				'load_more'       => esc_html__('Load more','wpqa'),
				'infinite_scroll' => esc_html__('Infinite scroll','wpqa'),
				'none'            => esc_html__('None','wpqa'),
			),
			'std'     => 'pagination',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'group_slugs',
			'name' => esc_html__('Group slugs','wpqa')
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_groups:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Groups archive slug','wpqa'),
			'desc' => esc_html__('Add your groups archive slug.','wpqa'),
			'id'   => 'archive_group_slug',
			'std'  => 'groups',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Click ON, if you need to remove the group slug and choose "Post name" from WordPress Settings/Permalinks.','wpqa'),
			'id'   => 'remove_group_slug',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Click ON, if you need to make the group slug with number instant of title and choose "Post name" from WordPress Settings/Permalinks.','wpqa'),
			'id'   => 'group_slug_numbers',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'      => esc_html__('Group slug','wpqa'),
			'desc'      => esc_html__('Add your group slug.','wpqa'),
			'id'        => 'group_slug',
			'std'       => 'group',
			'condition' => 'remove_group_slug:not(on)',
			'type'      => 'text'
		);

		$options[] = array(
			'name' => esc_html__('User requests slug','wpqa'),
			'desc' => esc_html__('Add your user requests slug.','wpqa'),
			'id'   => 'group_requests_slug',
			'std'  => 'user-requests',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Group posts slug','wpqa'),
			'desc' => esc_html__('Add your group posts slug.','wpqa'),
			'id'   => 'group_posts_slug',
			'std'  => 'group-pending-posts',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Group users slug','wpqa'),
			'desc' => esc_html__('Add your group users slug.','wpqa'),
			'id'   => 'group_users_slug',
			'std'  => 'group-users',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Group admins slug','wpqa'),
			'desc' => esc_html__('Add your group admins slug.','wpqa'),
			'id'   => 'group_admins_slug',
			'std'  => 'group-admins',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Blocked user slug','wpqa'),
			'desc' => esc_html__('Add your blocked user slug.','wpqa'),
			'id'   => 'blocked_users_slug',
			'std'  => 'blocked-users',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('View group post slug','wpqa'),
			'desc' => esc_html__('Add your view group post slug.','wpqa'),
			'id'   => 'view_group_posts_slug',
			'std'  => 'view-post-group',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Edit group post slug','wpqa'),
			'desc' => esc_html__('Add your edit group post slug.','wpqa'),
			'id'   => 'edit_group_posts_slug',
			'std'  => 'edit-post-group',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'add_edit_delete_g',
			'name' => esc_html__('Add - Edit - Delete','wpqa')
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_groups:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Add groups','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Add group slug','wpqa'),
			'desc' => esc_html__('Put the add group slug.','wpqa'),
			'id'   => 'add_groups_slug',
			'std'  => 'add-group',
			'type' => 'text'
		
		);

		$options[] = array(
			'name' => '<a href="'.wpqa_add_group_permalink().'" target="_blank">'.esc_html__('Link For The Add Group Page.','wpqa').'</a>',
			'type' => 'info'
		);

		$options[] = array(
			'name'    => esc_html__('Choose group status for users only','wpqa'),
			'desc'    => esc_html__('Choose group status after the user publishes the group.','wpqa'),
			'id'      => 'group_publish',
			'options' => array("publish" => esc_html__("Auto publish","wpqa"),"draft" => esc_html__("Need a review","wpqa")),
			'std'     => 'publish',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name'      => esc_html__('Send mail when the group needs a review','wpqa'),
			'desc'      => esc_html__('Mail for groups review enable or disable.','wpqa'),
			'id'        => 'send_email_draft_groups',
			'std'       => 'on',
			'condition' => 'group_publish:not(publish)',
			'type'      => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Activate Terms of Service and privacy policy page?','wpqa'),
			'desc' => esc_html__('Select ON if you want active Terms of Service and privacy policy page.','wpqa'),
			'id'   => 'terms_active_group',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'terms_active_group:is(on)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Terms of Service and Privacy Policy','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'terms_active_target_group',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'    => esc_html__('Terms page','wpqa'),
			'desc'    => esc_html__('Select the terms page','wpqa'),
			'id'      => 'terms_page_group',
			'type'    => 'select',
			'options' => $options_pages
		);
		
		$options[] = array(
			'name' => esc_html__("Type the terms link if you don't like a page","wpqa"),
			'id'   => 'terms_link_group',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate Privacy Policy','wpqa'),
			'desc' => esc_html__('Select ON if you want to activate Privacy Policy.','wpqa'),
			'id'   => 'privacy_policy_group',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'privacy_policy_group:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'privacy_active_target_group',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'    => esc_html__('Privacy Policy page','wpqa'),
			'desc'    => esc_html__('Select the privacy policy page','wpqa'),
			'id'      => 'privacy_page_group',
			'type'    => 'select',
			'options' => $options_pages
		);
		
		$options[] = array(
			'name' => esc_html__("Type the privacy policy link if you don't like a page","wpqa"),
			'id'   => 'privacy_link_group',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Edit groups','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate user can edit the groups','wpqa'),
			'desc' => esc_html__('Select ON if you want the user to be able to edit the groups.','wpqa'),
			'id'   => 'group_edit',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'group_edit:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Edit group slug','wpqa'),
			'desc' => esc_html__('Put the edit group slug.','wpqa'),
			'id'   => 'edit_groups_slug',
			'std'  => 'edit-group',
			'type' => 'text'
		);
		
		$options[] = array(
			'name'    => esc_html__('After editing auto approve group or need to be approved again?','wpqa'),
			'desc'    => esc_html__('Press ON to auto approve','wpqa'),
			'id'      => 'group_approved',
			'options' => array("publish" => esc_html__("Auto publish","wpqa"),"draft" => esc_html__("Need a review","wpqa")),
			'std'     => 'publish',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name' => esc_html__('After the group is edited change the URL from the title?','wpqa'),
			'desc' => esc_html__('Press ON to edit the URL','wpqa'),
			'id'   => 'change_group_url',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Delete groups','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate user can delete the groups','wpqa'),
			'desc' => esc_html__('Select ON if you want the user to be able to delete the groups.','wpqa'),
			'id'   => 'group_delete',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('When the users delete the group send to the trash or delete it forever?','wpqa'),
			'id'        => 'delete_group',
			'options'   => array(
				'delete' => esc_html__('Delete','wpqa'),
				'trash'  => esc_html__('Trash','wpqa'),
			),
			'std'       => 'delete',
			'condition' => 'group_delete:not(0)',
			'type'      => 'radio'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'group_posts',
			'name' => esc_html__('Group posts','wpqa')
		);
		
		if (has_himer() || has_knowly() || has_questy()) {
			$options[] = array(
				'div'       => 'div',
				'condition' => 'active_reaction:not(0)',
				'type'      => 'heading-2'
			);

			$options[] = array(
				'name' => esc_html__('Activate the reaction on the group posts?','wpqa'),
				'desc' => esc_html__('Reaction enable or disable in the group posts.','wpqa'),
				'id'   => 'active_reaction_group_posts',
				'std'  => "on",
				'type' => 'checkbox'
			);

			$options[] = array(
				'name' => esc_html__('Activate the reaction on the group comments?','wpqa'),
				'desc' => esc_html__('Reaction enable or disable in the group comments.','wpqa'),
				'id'   => 'active_reaction_group_comments',
				'std'  => "on",
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
		}

		$options[] = array(
			'name'      => esc_html__('Send mail when the posts on the group posts needs a review','wpqa'),
			'desc'      => esc_html__('Mail for posts on the group posts review enable or disable.','wpqa'),
			'id'        => 'send_email_draft_group_posts',
			'std'       => 'on',
			'type'      => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Activate user can edit the group posts','wpqa'),
			'desc' => esc_html__('Select ON if you want the user to be able to edit the group posts.','wpqa'),
			'id'   => 'posts_edit',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('After editing auto approve group posts or need to be approved again?','wpqa'),
			'desc'      => esc_html__('Press ON to auto approve','wpqa'),
			'id'        => 'posts_approved',
			'condition' => 'posts_edit:not(0)',
			'options'   => array("publish" => esc_html__("Auto publish","wpqa"),"draft" => esc_html__("Need a review","wpqa")),
			'std'       => 'publish',
			'type'      => 'radio'
		);
		
		$options[] = array(
			'name'    => esc_html__("Choose if you want to allow the users to see the users on the group for public or private groups.","wpqa"),
			'id'      => 'view_users_group',
			'type'    => 'multicheck',
			'options' => array(
				"public"  => esc_html__("Public","wpqa"),
				"private" => esc_html__("Private","wpqa"),
			),
		);
		
		$options[] = array(
			'name' => esc_html__('Enable or disable the editor for details in group posts form','wpqa'),
			'id'   => 'editor_group_posts',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'    => esc_html__('Pagination style','wpqa'),
			'desc'    => esc_html__('Choose pagination style from here.','wpqa'),
			'id'      => 'group_posts_pagination',
			'options' => array(
				'standard'        => esc_html__('Standard','wpqa'),
				'pagination'      => esc_html__('Pagination','wpqa'),
				'load_more'       => esc_html__('Load more','wpqa'),
				'infinite_scroll' => esc_html__('Infinite scroll','wpqa'),
				'none'            => esc_html__('None','wpqa'),
			),
			'std'     => 'pagination',
			'type'    => 'radio'
		);

		$options[] = array(
			'name' => esc_html__('Featured image for the group posts','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable or disable the featured image in group posts form','wpqa'),
			'id'   => 'featured_image_group_posts',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'featured_image_group_posts:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to enable the lightbox for featured image','wpqa'),
			'desc' => esc_html__('Select ON to enable the lightbox for featured image.','wpqa'),
			'id'   => 'featured_image_group_posts_lightbox',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			"name" => esc_html__("Set the width for the featured image for the answers","wpqa"),
			"id"   => "featured_image_group_posts_width",
			"type" => "sliderui",
			'std'  => 260,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			"name" => esc_html__("Set the height for the featured image for the answers","wpqa"),
			"id"   => "featured_image_group_posts_height",
			"type" => "sliderui",
			'std'  => 185,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable or disable the editor for the comments in the group posts','wpqa'),
			'id'   => 'editor_group_post_comments',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Featured image for the comments on the group posts','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable or disable the featured image for the comments in the group posts','wpqa'),
			'id'   => 'featured_image_group_post_comments',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'featured_image_group_post_comments:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to enable the lightbox for featured image','wpqa'),
			'desc' => esc_html__('Select ON to enable the lightbox for featured image.','wpqa'),
			'id'   => 'featured_image_group_post_comments_lightbox',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			"name" => esc_html__("Set the width for the featured image for the answers","wpqa"),
			"id"   => "featured_image_group_post_comments_width",
			"type" => "sliderui",
			'std'  => 260,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			"name" => esc_html__("Set the height for the featured image for the answers","wpqa"),
			"id"   => "featured_image_group_post_comments_height",
			"type" => "sliderui",
			'std'  => 185,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Edit group posts and comments','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name'    => esc_html__("Choose the roles you allow for the owner of the group and moderators.","wpqa"),
			'id'      => 'edit_delete_posts_comments',
			'type'    => 'multicheck',
			'options' => array(
				"edit"   => esc_html__("Edit posts and comments","wpqa"),
				"delete" => esc_html__("Delete posts and comments","wpqa"),
			),
		);

		$options[] = array(
			'name' => esc_html__('Delete group posts','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate user can delete the group posts','wpqa'),
			'desc' => esc_html__('Select ON if you want the user to be able to delete the group posts.','wpqa'),
			'id'   => 'posts_delete',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('When the users delete the group posts send to the trash or delete it forever?','wpqa'),
			'id'        => 'delete_posts',
			'options'   => array(
				'delete' => esc_html__('Delete','wpqa'),
				'trash'  => esc_html__('Trash','wpqa'),
			),
			'std'       => 'delete',
			'condition' => 'posts_delete:not(0)',
			'type'      => 'radio'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_after_groups_layout',$options);

		$options[] = array(
			'name' => esc_html__('Captcha settings','wpqa'),
			'id'   => 'captcha',
			'icon' => 'admin-network',
			'type' => 'heading'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Captcha enable or disable (in ask question form)','wpqa'),
			'id'   => 'the_captcha',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Captcha enable or disable (in add group form)','wpqa'),
			'id'   => 'the_captcha_group',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Captcha enable or disable (in add post form)','wpqa'),
			'id'   => 'the_captcha_post',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Captcha enable or disable (in register form)','wpqa'),
			'id'   => 'the_captcha_register',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Captcha enable or disable (in login form)','wpqa'),
			'id'   => 'the_captcha_login',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Captcha enable or disable (in forgot password form)','wpqa'),
			'id'   => 'the_captcha_password',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Captcha enable or disable (in answer form)','wpqa'),
			'id'   => 'the_captcha_answer',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Captcha enable or disable (in comment form)','wpqa'),
			'id'   => 'the_captcha_comment',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Captcha enable or disable (in send message form)','wpqa'),
			'id'   => 'the_captcha_message',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Captcha enable or disable (in add a new category form)','wpqa'),
			'id'   => 'the_captcha_category',
			'type' => 'checkbox'
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_captcha',$options);
		$captcha_condition_users = apply_filters(wpqa_prefix_theme.'_captcha_condition_users','the_captcha:not(0),the_captcha_group:not(0),the_captcha_post:not(0),the_captcha_category:not(0),the_captcha_answer:not(0),the_captcha_comment:not(0),the_captcha_message:not(0)');
		$captcha_condition = apply_filters(wpqa_prefix_theme.'_captcha_condition','the_captcha:not(0),the_captcha_group:not(0),the_captcha_post:not(0),the_captcha_category:not(0),the_captcha_register:not(0),the_captcha_login:not(0),the_captcha_password:not(0),the_captcha_answer:not(0),the_captcha_comment:not(0),the_captcha_message:not(0)');
		
		$options[] = array(
			'name'      => esc_html__('Captcha works for "unlogged users" or "unlogged and logged" users','wpqa'),
			'id'        => 'captcha_users',
			'std'       => 'unlogged',
			'operator'  => 'or',
			'condition' => $captcha_condition_users,
			'type'      => 'radio',
			'options'   => 
				array(
					"unlogged" => esc_html__('Unlogged users','wpqa'),
					"both"     => esc_html__('Unlogged and logged in users','wpqa')
			)
		);
		
		$options[] = array(
			'div'       => 'div',
			'operator'  => 'or',
			'condition' => $captcha_condition,
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Captcha style','wpqa'),
			'desc'    => esc_html__('Choose the captcha style','wpqa'),
			'id'      => 'captcha_style',
			'std'     => 'question_answer',
			'type'    => 'radio',
			'options' => 
				array(
					"question_answer"  => esc_html__('Question and answer','wpqa'),
					"normal_captcha"   => esc_html__('Normal captcha','wpqa'),
					"google_recaptcha" => esc_html__('Google reCaptcha','wpqa')
			)
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'captcha_style:is(google_recaptcha)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => sprintf(esc_html__('You can get the reCaptcha v2 site and secret keys from: %s','wpqa'),'<a href="https://www.google.com/recaptcha/admin/" target="_blank">'.esc_html__('here','wpqa').'</a>'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Site key reCaptcha','wpqa'),
			'id'   => 'site_key_recaptcha',
			'type' => 'text',
		);
		
		$options[] = array(
			'name' => esc_html__('Secret key reCaptcha','wpqa'),
			'id'   => 'secret_key_recaptcha',
			'type' => 'text',
		);
		
		$options[] = array(
			'name' => sprintf(esc_html__('You can get the reCaptcha language code from: %s','wpqa'),'<a href="https://developers.google.com/recaptcha/docs/language/" target="_blank">'.esc_html__('here','wpqa').'</a>'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('ReCaptcha language','wpqa'),
			'id'   => 'recaptcha_language',
			'type' => 'text',
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'captcha_style:is(question_answer)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Captcha answer enable or disable in forms','wpqa'),
			'id'   => 'show_captcha_answer',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Captcha question','wpqa'),
			'desc' => esc_html__('put the Captcha question','wpqa'),
			'id'   => 'captcha_question',
			'type' => 'text',
			'std'  => "What is the capital of Egypt?"
		);
		
		$options[] = array(
			'name' => esc_html__('Captcha answer','wpqa'),
			'desc' => esc_html__('put the Captcha answer','wpqa'),
			'id'   => 'captcha_answer',
			'type' => 'text',
			'std'  => "Cairo"
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$user_settings = array(
			"setting_profile"    => esc_html__('General Setting','wpqa'),
			"user_slugs"         => esc_html__('User Slugs','wpqa'),
			"register_setting"   => esc_html__('Register Setting','wpqa'),
			"login_setting"      => esc_html__('Login Setting','wpqa'),
			"edit_profile"       => esc_html__('Edit Profile','wpqa'),
			"forgot_password"    => esc_html__('Forgot Password','wpqa'),
			"ask_users"          => esc_html__('Ask Users','wpqa'),
			"referral_setting"   => esc_html__('Referral setting','wpqa'),
			"popup_notification" => esc_html__('Popup Notification','wpqa'),
			"permissions"        => esc_html__('Permissions','wpqa'),
			"author_setting"     => esc_html__('Author Setting','wpqa')
		);
		
		$options[] = array(
			'name'    => esc_html__('User settings','wpqa'),
			'id'      => 'user',
			'icon'    => 'admin-users',
			'type'    => 'heading',
			'std'     => 'setting_profile',
			'options' => apply_filters(wpqa_prefix_theme."_user_settings",$user_settings)
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'setting_profile',
			'name' => esc_html__('General Setting','wpqa')
		);
		
		$options[] = array(
			'name' => esc_html__('Index the author pages enable or disable.','wpqa'),
			'id'   => 'index_author',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Select ON to index the other author pages not the main profile page only.','wpqa'),
			'id'        => 'index_author_pages',
			'condition' => 'index_author:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Breadcrumbs for author pages enable or disable.','wpqa'),
			'id'        => 'breadcrumbs_author',
			'std'       => 'on',
			'condition' => 'breadcrumbs:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Visits for author pages enable or disable.','wpqa'),
			'id'        => 'author_visits',
			'std'       => 'on',
			'condition' => 'active_post_stats:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Add author info as a widget.','wpqa'),
			'id'   => 'author_widget',
			'std'  => (has_himer() || has_knowly()?'on':0),
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Author info enable or disable.','wpqa'),
			'id'        => 'author_box',
			'std'       => 'on',
			'condition' => 'author_widget:not(on)',
			'type'      => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'author_widget:is(on)',
			'type'      => 'heading-2'
		);

		$author_widgets = array(
			"about"       => array("sort" => esc_html__('About User','wpqa'),"value" => "about"),
			"statistics"  => array("sort" => esc_html__('User Statistics','wpqa'),"value" => "statistics"),
			"information" => array("sort" => esc_html__('User Information','wpqa'),"value" => "information"),
			"social"      => array("sort" => esc_html__('User Social','wpqa'),"value" => "social"),
			//"awards"      => array("sort" => esc_html__('Awards','wpqa'),"value" => "awards"),
		);
		
		$options[] = array(
			'name'    => esc_html__('Select the sections on the author widgets','wpqa'),
			'id'      => 'author_widgets',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $author_widgets,
			'options' => $author_widgets
		);
		
		$options[] = array(
			'name'      => esc_html__('Lable of the pro user enable or disable.','wpqa'),
			'desc'      => esc_html__('For paid membership will show pro user or for the normal users show the role.','wpqa'),
			'id'        => 'author_role',
			'std'       => 'on',
			'condition' => 'author_widgets:has(about)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Select the user social widget style','wpqa'),
			'id'        => 'author_social_widget',
			'condition' => 'author_widgets:has(social)',
			'options'   => array(
				'style_1' => esc_html__('Style 1','wpqa'),
				'style_2' => esc_html__('Style 2','wpqa'),
			),
			'std'       => 'style_1',
			'type'      => 'radio'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Block users for each other enable or disable.','wpqa'),
			'id'   => 'block_users',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Report users for each other enable or disable.','wpqa'),
			'id'   => 'report_users',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the pop up at the author image in the site?','wpqa'),
			'desc' => esc_html__('Pop up at the author image in site enable or disable.','wpqa'),
			'id'   => 'author_image_pop',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Profile picture setting','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Default image profile enable or disable.','wpqa'),
			'desc' => esc_html__("Select ON to upload your default image for the user who has not uploaded the image profile.","wpqa"),
			'id'   => 'default_image_active',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'default_image_active:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Upload default image profile for the user.','wpqa'),
			'id'   => 'default_image',
			'std'  => $imagepath_wpqa."default-image.png",
			'type' => 'upload'
		);
		
		$options[] = array(
			'name' => esc_html__('Upload default image profile for the user females.','wpqa'),
			'id'   => 'default_image_females',
			'std'  => $imagepath_wpqa."default-image-females.png",
			'type' => 'upload'
		);
		
		$options[] = array(
			'name' => esc_html__('Upload default image profile for the anonymous users.','wpqa'),
			'id'   => 'default_image_anonymous',
			'type' => 'upload'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name'      => esc_html__('Add the maximum size for the profile picture, Add it with KB, for 1 MB add 1024.','wpqa'),
			'desc'      => esc_html__('Add the maximum size for the profile picture, Leave it empty if you need it unlimited size.','wpqa'),
			'id'        => 'profile_picture_size',
			'condition' => 'register_items:has(image_profile),edit_profile_items_1:has(image_profile)',
			'operator'  => 'or',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Cover picture setting','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Cover image enable or disable.','wpqa'),
			'id'   => 'cover_image',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'cover_image:is(on)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Default cover enable or disable.','wpqa'),
			'desc' => esc_html__("Select ON to upload your default cover for the user who has not uploaded the cover profile.","wpqa"),
			'id'   => 'default_cover_active',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Upload default cover for the user.','wpqa'),
			'id'        => 'default_cover',
			'condition' => 'default_cover_active:not(0)',
			'type'      => 'upload'
		);
		
		$options[] = array(
			'name'      => esc_html__('Upload default cover for the user females.','wpqa'),
			'id'        => 'default_cover_females',
			'condition' => 'default_cover_active:not(0)',
			'type'      => 'upload'
		);
		
		$options[] = array(
			'name'      => esc_html__('Add the maximum size for the profile picture, Add it with KB, for 1 MB add 1024.','wpqa'),
			'desc'      => esc_html__('Add the maximum size for the profile picture, Leave it empty if you need it unlimited size.','wpqa'),
			'id'        => 'profile_cover_size',
			'condition' => 'register_items:has(cover),edit_profile_items_1:has(cover)',
			'operator'  => 'or',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name'      => esc_html__('Cover full width or fixed','wpqa'),
			'desc'      => esc_html__('Choose the cover to make it work with full width or fixed.','wpqa'),
			'id'        => 'cover_fixed',
			'options'   => array(
				'normal' => esc_html__('Full width','wpqa'),
				'fixed'  => esc_html__('Fixed','wpqa'),
			),
			'std'       => 'normal',
			'condition' => 'cover_image:is(on)',
			'type'      => 'radio'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'      => esc_html__('Activate other at the gender.','wpqa'),
			'id'        => 'gender_other',
			'condition' => 'register_items:has(gender),edit_profile_items_1:has(gender)',
			'operator'  => 'or',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the notifications system in site?','wpqa'),
			'desc' => esc_html__('Activate the notifications system enable or disable.','wpqa'),
			'id'   => 'active_notifications',
			'std'  => "on",
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_notifications:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose the roles or users for the custom notification','wpqa'),
			'desc'    => esc_html__('Choose from here which roles or users you want to send the custom notification.','wpqa'),
			'id'      => 'notification_groups_users',
			'options' => array(
				'groups' => esc_html__('Roles','wpqa'),
				'users'  => esc_html__('Users','wpqa'),
			),
			'std'     => 'groups',
			'type'    => 'radio'
		);

		$options[] = array(
			'name'      => esc_html__("Choose the roles that you want to send the custom notification.","wpqa"),
			'id'        => 'custom_notification_groups',
			'condition' => 'notification_groups_users:not(users)',
			'type'      => 'multicheck',
			'options'   => $new_roles,
			'std'       => array('administrator' => 'administrator','editor' => 'editor','contributor' => 'contributor','subscriber' => 'subscriber','author' => 'author'),
		);

		$options[] = array(
			'name'      => esc_html__('Specific user ids','wpqa'),
			'id'        => 'notification_specific_users',
			'condition' => 'notification_groups_users:is(users)',
			'type'      => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Custom notification','wpqa'),
			'id'   => 'custom_notification',
			'std'  => 'Welcome',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('You must save your options before sending the notification.','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => '<a href="#" class="button button-primary send-custom-notification">'.esc_html__('Send the custom notification','wpqa').'</a>',
			'type' => 'info'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the activity log site?','wpqa'),
			'desc' => esc_html__('Activate the activity log enable or disable.','wpqa'),
			'id'   => 'active_activity_log',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_notifications:not(0),active_activity_log:not(0),active_points:not(0),active_reports:not(0)',
			'operator'  => 'or',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the deletions of the old notifications, activities, reports, and points?','wpqa'),
			'desc' => esc_html__('Activate the deletions of the old notifications, activities, reports, and points enable or disable.','wpqa'),
			'id'   => 'deletions_notifications_activities',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'deletions_notifications_activities:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose the time of the old deletions of the old notifications, activities, reports, and points','wpqa'),
			'desc'    => esc_html__('Choose from here which time you want to delete the old notifications, activities, reports, and points.','wpqa'),
			'id'      => 'time_deletions_notifications_activities',
			'options' => array(
				'month'   => esc_html__('Month','wpqa'),
				'2months' => esc_html__('2 Months','wpqa'),
				'year'    => esc_html__('Year','wpqa'),
			),
			'std'     => '2months',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose which type you want to delete','wpqa'),
			'desc'    => esc_html__('Choose from here which type you want to delete the notifications, activities, reports, or points.','wpqa'),
			'id'      => 'type_deletions_notifications_activities',
			'options' => array(
				'notification' => esc_html__('Notifications','wpqa'),
				'activity'     => esc_html__('Activities','wpqa'),
				'report'       => esc_html__('Reports','wpqa'),
				'point'        => esc_html__('Points','wpqa'),
			),
			'std'     => array('notification' => 'notification','activity' => 'activity','report' => 'report','point' => 'point'),
			'type'    => 'multicheck'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'    => esc_html__('Select the user stats','wpqa'),
			'id'      => 'user_stats',
			'type'    => 'multicheck',
			'std'     => array(
				"questions"    => "questions",
				"answers"      => "answers",
				"best_answers" => "best_answers",
				"points"       => "points",
				"i_follow"     => "i_follow",
				"followers"    => "followers",
			),
			'options' => array(
				"questions"    => esc_html__('Questions','wpqa'),
				"answers"      => esc_html__('Answers','wpqa'),
				"best_answers" => esc_html__('Best Answers','wpqa'),
				"points"       => esc_html__('Points','wpqa'),
				"groups"       => esc_html__('Groups','wpqa'),
				"group_posts"  => esc_html__('Group Posts','wpqa'),
				"posts"        => esc_html__('Posts','wpqa'),
				"comments"     => esc_html__('Comments','wpqa'),
				"i_follow"     => esc_html__('Authors I Follow','wpqa'),
				"followers"    => esc_html__('Followers','wpqa'),
			)
		);

		$options[] = array(
			'name' => esc_html__('The settings of the profile page tabs on the WordPress menus, on menu named Profile Page Tabs','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name'    => esc_html__('Select the columns in the user admin','wpqa'),
			'id'      => 'user_meta_admin',
			'type'    => 'multicheck',
			'options' => array(
				"phone"        => esc_html__('Phone','wpqa'),
				"country"      => esc_html__('Country','wpqa'),
				"age"          => esc_html__('Age','wpqa'),
				"points"       => esc_html__('Points','wpqa'),
				"registration" => esc_html__('Registration date','wpqa'),
				"invitation"   => esc_html__('Invitation','wpqa'),
			)
		);
		
		$options[] = array(
			'name'    => esc_html__('Users style at followed and search pages','wpqa'),
			'desc'    => esc_html__('Choose the users style at followed and search pages.','wpqa'),
			'id'      => 'user_style_pages',
			'options' => array(
				'columns'       => esc_html__('Columns','wpqa'),
				'small_grid'    => esc_html__('Small grid with follow','wpqa'),
				'simple_follow' => esc_html__('Simple with follow','wpqa'),
				'small'         => esc_html__('Small','wpqa'),
				'grid'          => esc_html__('Grid','wpqa'),
				'normal'        => esc_html__('Normal','wpqa'),
			),
			'std'     => 'columns',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name'      => esc_html__("Activate the masonry style?","wpqa"),
			'id'        => 'masonry_user_style',
			'type'      => 'checkbox',
			'condition' => 'user_style_pages:is(small_grid),user_style_pages:is(columns),user_style_pages:is(small),user_style_pages:is(grid)',
			'operator'  => 'or',
		);
		
		$options[] = array(
			'name' => esc_html__('Users per page at home, followed and search pages','wpqa'),
			'desc' => esc_html__('Put the users per page at home, followed and search pages.','wpqa'),
			'id'   => 'users_per_page',
			'std'  => '10',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'user_slugs',
			'name' => esc_html__('User Slugs','wpqa')
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_user_slugs',$options);

		// $options[] = array(
		// 	'name' => esc_html__('Click ON, if you need to remove the profile slug and choose "Post name" from WordPress Settings/Permalinks.','wpqa'),
		// 	'id'   => 'remove_profile_slug',
		// 	'type' => 'checkbox'
		// );

		$options[] = array(
			'name' => esc_html__('Click ON, if you need to make the profile slug with number instant of title and choose "Post name" from WordPress Settings/Permalinks.','wpqa'),
			'id'   => 'profile_slug_numbers',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'remove_profile_slug:not(on)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('User profile slug','wpqa'),
			'desc' => esc_html__('Put the user profile slug.','wpqa'),
			'id'   => 'profile_slug',
			'std'  => 'profile',
			'type' => 'text'
		);
		
		$options[] = array(
			'name'    => esc_html__('Profile by login or nicename','wpqa'),
			'desc'    => esc_html__('Choose the user profile page work by login or nicename.','wpqa'),
			'id'      => 'profile_type',
			'options' => array(
				'nicename' => esc_html__('Nicename','wpqa'),
				'login'    => esc_html__('Login name','wpqa'),
			),
			'std'     => 'nicename',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Login slug','wpqa'),
			'desc' => esc_html__('Put the login slug.','wpqa'),
			'id'   => 'login_slug',
			'std'  => 'log-in',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Signup slug','wpqa'),
			'desc' => esc_html__('Put the signup slug.','wpqa'),
			'id'   => 'signup_slug',
			'std'  => 'sign-up',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Lost password slug','wpqa'),
			'desc' => esc_html__('Put the lost password slug.','wpqa'),
			'id'   => 'lost_password_slug',
			'std'  => 'lost-password',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Settings page slug','wpqa'),
			'desc' => esc_html__('Put the settings page slug.','wpqa'),
			'id'   => 'settings_slug',
			'std'  => 'settings',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Edit profile slug','wpqa'),
			'desc' => esc_html__('Put the edit profile slug.','wpqa'),
			'id'   => 'edit_slug',
			'std'  => 'edit',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Change password profile slug','wpqa'),
			'desc' => esc_html__('Put the change password slug.','wpqa'),
			'id'   => 'password_slug',
			'std'  => 'change-password',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Privacy profile slug','wpqa'),
			'desc' => esc_html__('Put the privacy profile slug.','wpqa'),
			'id'   => 'privacy_slug',
			'std'  => 'privacy',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Withdrawals profile slug','wpqa'),
			'desc' => esc_html__('Put the withdrawals profile slug.','wpqa'),
			'id'   => 'withdrawals_slug',
			'std'  => 'withdrawals',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Financial profile slug','wpqa'),
			'desc' => esc_html__('Put the financial profile slug.','wpqa'),
			'id'   => 'financial_slug',
			'std'  => 'financial',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Transactions profile slug','wpqa'),
			'desc' => esc_html__('Put the transactions profile slug.','wpqa'),
			'id'   => 'transactions_slug',
			'std'  => 'transactions',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Mails profile slug','wpqa'),
			'desc' => esc_html__('Put the mails profile slug.','wpqa'),
			'id'   => 'mails_slug',
			'std'  => 'mails',
			'type' => 'hidden'
		);
		
		$options[] = array(
			'name' => esc_html__('Delete profile slug','wpqa'),
			'desc' => esc_html__('Put the delete profile slug.','wpqa'),
			'id'   => 'delete_slug',
			'std'  => 'delete',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User followers slug','wpqa'),
			'desc' => esc_html__('Put the user followers slug.','wpqa'),
			'id'   => 'followers_slug',
			'std'  => 'followers',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User following slug','wpqa'),
			'desc' => esc_html__('Put the user following slug.','wpqa'),
			'id'   => 'following_slug',
			'std'  => 'following',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User blocking slug','wpqa'),
			'desc' => esc_html__('Put the user blocking slug.','wpqa'),
			'id'   => 'blocking_slug',
			'std'  => 'blocking',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User notifications slug','wpqa'),
			'desc' => esc_html__('Put the user notifications slug.','wpqa'),
			'id'   => 'notifications_slug',
			'std'  => 'notifications',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User activities slug','wpqa'),
			'desc' => esc_html__('Put the user activities slug.','wpqa'),
			'id'   => 'activities_slug',
			'std'  => 'activities',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User questions slug','wpqa'),
			'desc' => esc_html__('Put the user questions slug.','wpqa'),
			'id'   => 'questions_slug',
			'std'  => 'questions',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User answers slug','wpqa'),
			'desc' => esc_html__('Put the user answers slug.','wpqa'),
			'id'   => 'answers_slug',
			'std'  => 'answers',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User best answers slug','wpqa'),
			'desc' => esc_html__('Put the user best answers slug.','wpqa'),
			'id'   => 'best_answers_slug',
			'std'  => 'best-answers',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User groups slug','wpqa'),
			'desc' => esc_html__('Put the user groups slug.','wpqa'),
			'id'   => 'groups_slug',
			'std'  => 'groups',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User joined groups slug','wpqa'),
			'desc' => esc_html__('Put the user joined groups slug.','wpqa'),
			'id'   => 'joined_groups_slug',
			'std'  => 'joined-groups',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User managed groups slug','wpqa'),
			'desc' => esc_html__('Put the user managed groups slug.','wpqa'),
			'id'   => 'managed_groups_slug',
			'std'  => 'managed-groups',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User points slug','wpqa'),
			'desc' => esc_html__('Put the user points slug.','wpqa'),
			'id'   => 'points_slug',
			'std'  => 'points',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User polls slug','wpqa'),
			'desc' => esc_html__('Put the user polls slug.','wpqa'),
			'id'   => 'polls_slug',
			'std'  => 'polls',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User asked slug','wpqa'),
			'desc' => esc_html__('Put the user asked slug.','wpqa'),
			'id'   => 'asked_slug',
			'std'  => 'asked',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User asked questions slug','wpqa'),
			'desc' => esc_html__('Put the user asked questions slug.','wpqa'),
			'id'   => 'asked_questions_slug',
			'std'  => 'asked-questions',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User my asked questions slug','wpqa'),
			'desc' => esc_html__('Put the user my asked questions slug.','wpqa'),
			'id'   => 'my_asked_slug',
			'std'  => 'my-asked',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User paid questions slug','wpqa'),
			'desc' => esc_html__('Put the user paid questions slug.','wpqa'),
			'id'   => 'paid_questions_slug',
			'std'  => 'paid-questions',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User followed slug','wpqa'),
			'desc' => esc_html__('Put the user followed slug.','wpqa'),
			'id'   => 'followed_slug',
			'std'  => 'followed',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User favorites slug','wpqa'),
			'desc' => esc_html__('Put the user favorites slug.','wpqa'),
			'id'   => 'favorites_slug',
			'std'  => 'favorites',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User posts slug','wpqa'),
			'desc' => esc_html__('Put the user posts slug.','wpqa'),
			'id'   => 'posts_slug',
			'std'  => 'posts',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User comments slug','wpqa'),
			'desc' => esc_html__('Put the user comments slug.','wpqa'),
			'id'   => 'comments_slug',
			'std'  => 'comments',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User followers questions slug','wpqa'),
			'desc' => esc_html__('Put the user followers questions slug.','wpqa'),
			'id'   => 'followers_questions_slug',
			'std'  => 'followers-questions',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User followers answers slug','wpqa'),
			'desc' => esc_html__('Put the user followers answers slug.','wpqa'),
			'id'   => 'followers_answers_slug',
			'std'  => 'followers-answers',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User followers posts slug','wpqa'),
			'desc' => esc_html__('Put the user followers posts slug.','wpqa'),
			'id'   => 'followers_posts_slug',
			'std'  => 'followers-posts',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User followers comments slug','wpqa'),
			'desc' => esc_html__('Put the user followers comments slug.','wpqa'),
			'id'   => 'followers_comments_slug',
			'std'  => 'followers-comments',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'register_setting',
			'name' => esc_html__('Register Setting','wpqa')
		);
		
		$options[] = array(
			'name'    => esc_html__('Register','wpqa'),
			'desc'    => esc_html__('Choose the status of the register on your site.','wpqa'),
			'id'      => 'activate_register',
			'std'     => 'enabled',
			'options' => array(
				"enabled"  => esc_html__("Enabled","wpqa"),
				"disabled" => esc_html__("Disabled","wpqa"),
			),
			'type'    => 'radio'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'activate_register:not(disabled)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Make the register works without ajax','wpqa'),
			'desc' => esc_html__('Select ON if you want to make the register works without ajax to avoid the problems with the cache plugin.','wpqa'),
			'id'   => 'stop_register_ajax',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Make the register works with popup','wpqa'),
			'desc' => esc_html__('Select ON if you want to make the register works with popup.','wpqa'),
			'id'   => 'register_popup',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Register in default role','wpqa'),
			'desc'    => esc_html__('Select the default role when users registered.','wpqa'),
			'id'      => 'default_group',
			'std'     => 'subscriber',
			'type'    => 'select',
			'options' => $wpqa_options_roles
		);
		
		$options[] = array(
			'name'    => esc_html__('Add the black list emails or any domain to stop them from registering into the site','wpqa'),
			'id'      => "black_list_emails",
			'type'    => "elements",
			'sort'    => "no",
			'hide'    => "yes",
			'button'  => esc_html__('Add a new email','wpqa'),
			'options' => array(
				array(
					"type" => "text",
					"id"   => "email",
					"name" => esc_html__("Email or domain","wpqa")
				)
			),
		);
		
		$options[] = array(
			'name'    => esc_html__('Add the black list of words to stop anyone to register on the site with these words','wpqa'),
			'id'      => "block_words_register",
			'type'    => "elements",
			'sort'    => "no",
			'hide'    => "yes",
			'button'  => esc_html__('Add a new word','wpqa'),
			'options' => array(
				array(
					"type" => "text",
					"id"   => "name",
					"name" => esc_html__("Name","wpqa")
				)
			),
		);
		
		$options[] = array(
			'name'    => esc_html__('After register go to?','wpqa'),
			'id'      => 'after_register',
			'std'     => "same_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"home" => esc_html__("Home","wpqa"),"profile" => esc_html__("Profile","wpqa"),"custom_link" => esc_html__("Custom link","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__("Type the link if you don't like above","wpqa"),
			'id'        => 'after_register_link',
			'condition' => 'after_register:is(custom_link)',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Send a welcome email when the user is registered','wpqa'),
			'desc' => esc_html__('Welcome mail enable or disable.','wpqa'),
			'id'   => 'send_welcome_mail',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('The membership under review?','wpqa'),
			'desc' => esc_html__('Select ON to review the users before the registration is completed.','wpqa'),
			'id'   => 'user_review',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Send mail when user needs a review','wpqa'),
			'desc'      => esc_html__('Mail for user review enable or disable.','wpqa'),
			'id'        => 'send_email_users_review',
			'std'       => 'on',
			'condition' => 'user_review:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Confirm email after registering enable or disable','wpqa'),
			'id'   => 'confirm_email',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Click ON to remove the confirm password from the register form.','wpqa'),
			'id'   => 'comfirm_password',
			'type' => 'checkbox'
		);

		$allow_duplicate_names = array(
			"nickname"     => array("sort" => esc_html__('Nickname','wpqa'),"value" => ""),
			"display_name" => array("sort" => esc_html__('Display Name','wpqa'),"value" => ""),
		);
		
		$options[] = array(
			'name'    => esc_html__("Choose if you want the nickname and display name to be duplicate or not","wpqa"),
			'id'      => 'allow_duplicate_names',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $allow_duplicate_names,
			'options' => $allow_duplicate_names
		);

		$options[] = array(
			'name' => esc_html__('Add the maximum length for the username, leave it empty if you need it unlimited.','wpqa'),
			'id'   => 'username_register_maximum',
			'type' => 'text'
		);

		$register_items = array(
			"username"      => array("sort" => esc_html__('Username','wpqa'),"value" => "username","default" => "yes"),
			"email"         => array("sort" => esc_html__('E-mail','wpqa'),"value" => "email","default" => "yes"),
			"password"      => array("sort" => esc_html__('Password','wpqa'),"value" => "username","default" => "yes"),
			"nickname"      => array("sort" => esc_html__('Nickname','wpqa'),"value" => "nickname"),
			"first_name"    => array("sort" => esc_html__('First Name','wpqa'),"value" => "first_name"),
			"last_name"     => array("sort" => esc_html__('Last Name','wpqa'),"value" => "last_name"),
			"display_name"  => array("sort" => esc_html__('Display Name','wpqa'),"value" => "display_name"),
			"image_profile" => array("sort" => esc_html__('Image Profile','wpqa'),"value" => "image_profile"),
			"cover"         => array("sort" => esc_html__('Cover','wpqa'),"value" => "cover"),
			"country"       => array("sort" => esc_html__('Country','wpqa'),"value" => "country"),
			"city"          => array("sort" => esc_html__('City','wpqa'),"value" => "city"),
			"phone"         => array("sort" => esc_html__('Phone','wpqa'),"value" => "phone"),
			"gender"        => array("sort" => esc_html__('Gender','wpqa'),"value" => "gender"),
			"age"           => array("sort" => esc_html__('Age','wpqa'),"value" => "age"),
		);
		$register_items_std = array(
			"username"      => array("sort" => esc_html__('Username','wpqa'),"value" => "username","default" => "yes"),
			"email"         => array("sort" => esc_html__('E-mail','wpqa'),"value" => "email","default" => "yes"),
			"password"      => array("sort" => esc_html__('Password','wpqa'),"value" => "username","default" => "yes"),
			"nickname"      => array("sort" => esc_html__('Nickname','wpqa'),"value" => ""),
			"first_name"    => array("sort" => esc_html__('First Name','wpqa'),"value" => ""),
			"last_name"     => array("sort" => esc_html__('Last Name','wpqa'),"value" => ""),
			"display_name"  => array("sort" => esc_html__('Display Name','wpqa'),"value" => ""),
			"image_profile" => array("sort" => esc_html__('Image Profile','wpqa'),"value" => ""),
			"cover"         => array("sort" => esc_html__('Cover','wpqa'),"value" => ""),
			"country"       => array("sort" => esc_html__('Country','wpqa'),"value" => ""),
			"city"          => array("sort" => esc_html__('City','wpqa'),"value" => ""),
			"phone"         => array("sort" => esc_html__('Phone','wpqa'),"value" => ""),
			"gender"        => array("sort" => esc_html__('Gender','wpqa'),"value" => ""),
			"age"           => array("sort" => esc_html__('Age','wpqa'),"value" => ""),
		);

		$options[] = array(
			'name'    => esc_html__("Select what to show at register form","wpqa"),
			'id'      => 'register_items',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'options' => apply_filters(wpqa_prefix_theme."_options_register_items",$register_items),
			'std'     => $register_items_std
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'register_items:has(first_name),register_items:has(last_name),register_items:has(display_name),register_items:has(image_profile),register_items:has(cover),register_items:has(gender),register_items:has(country),register_items:has(city),register_items:has(phone),register_items:has(age)',
			'operator'  => 'or',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Required setting','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name'      => esc_html__('First name in register is required.','wpqa'),
			'id'        => 'first_name_required_register',
			'condition' => 'register_items:has(first_name)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Last name in register is required.','wpqa'),
			'id'        => 'last_name_required_register',
			'condition' => 'register_items:has(last_name)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Display name in register is required.','wpqa'),
			'id'        => 'display_name_required_register',
			'condition' => 'register_items:has(display_name)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Profile picture in register is required','wpqa'),
			'id'        => 'profile_picture_required_register',
			'condition' => 'register_items:has(image_profile)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Profile cover in register is required','wpqa'),
			'id'        => 'profile_cover_required_register',
			'condition' => 'register_items:has(cover),cover_image:is(on)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Gender in register is required.','wpqa'),
			'id'        => 'gender_required_register',
			'condition' => 'register_items:has(gender)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Country in register is required.','wpqa'),
			'id'        => 'country_required_register',
			'condition' => 'register_items:has(country)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('City in register is required.','wpqa'),
			'id'        => 'city_required_register',
			'condition' => 'register_items:has(city)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Phone in register is required.','wpqa'),
			'id'        => 'phone_required_register',
			'condition' => 'register_items:has(phone)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Age in register is required.','wpqa'),
			'id'        => 'age_required_register',
			'condition' => 'register_items:has(age)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options = apply_filters('wpqa_options_after_required_register',$options);
		
		$options[] = array(
			'name' => esc_html__('Terms of Service and Privacy Policy','wpqa'),
			'type' => 'info',
		);
		
		$options[] = array(
			'name' => esc_html__('Activate Terms of Service and privacy policy page?','wpqa'),
			'desc' => esc_html__('Select ON if you want active Terms of Service and privacy policy page.','wpqa'),
			'id'   => 'terms_active_register',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'terms_active_register:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Select the checked by default option','wpqa'),
			'desc' => esc_html__('Select ON if you want to checked it by default.','wpqa'),
			'id'   => 'terms_checked_register',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'terms_active_target_register',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'    => esc_html__('Terms page','wpqa'),
			'desc'    => esc_html__('Select the terms page','wpqa'),
			'id'      => 'terms_page_register',
			'type'    => 'select',
			'options' => $options_pages
		);
		
		$options[] = array(
			'name' => esc_html__("Type the terms link if you don't like a page","wpqa"),
			'id'   => 'terms_link_register',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate Privacy Policy','wpqa'),
			'desc' => esc_html__('Select ON if you want to activate Privacy Policy.','wpqa'),
			'id'   => 'privacy_policy_register',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'privacy_policy_register:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'privacy_active_target_register',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'    => esc_html__('Privacy Policy page','wpqa'),
			'desc'    => esc_html__('Select the privacy policy page','wpqa'),
			'id'      => 'privacy_page_register',
			'type'    => 'select',
			'options' => $options_pages
		);
		
		$options[] = array(
			'name' => esc_html__("Type the privacy policy link if you don't like a page","wpqa"),
			'id'   => 'privacy_link_register',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to allow for the users register with space','wpqa'),
			'id'   => 'allow_spaces',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'    => esc_html__('Select the default options when registered','wpqa'),
			'id'      => 'register_default_options',
			'type'    => 'multicheck',
			'std'     => array(
				"show_point_favorite"     => "show_point_favorite",
				"question_schedules"      => "question_schedules",
				"post_schedules"          => "post_schedules",
				"received_email"          => "received_email",
				"received_email_post"     => "received_email_post",
				"received_message"        => "received_message",
				"new_payment_mail"        => "new_payment_mail",
				"send_message_mail"       => "send_message_mail",
				"answer_on_your_question" => "answer_on_your_question",
				"answer_question_follow"  => "answer_question_follow",
				"notified_reply"          => "notified_reply",
				"notification_mention"    => "notification_mention",
			),
			'options' => array(
				"show_point_favorite"     => esc_html__('Show the private pages','wpqa'),
				"question_schedules"      => esc_html__('Send schedule mails for the users as a list with recent questions','wpqa'),
				"post_schedules"          => esc_html__('Send schedule mails for the users as a list with recent posts','wpqa'),
				"received_email"          => esc_html__('Send mail when user ask a new question','wpqa'),
				"received_email_post"     => esc_html__('Send mail when user add a new post','wpqa'),
				"received_message"        => esc_html__("Received message from another users","wpqa"),
				"new_payment_mail"        => esc_html__("Send mail when made new payment","wpqa"),
				"send_message_mail"       => esc_html__("Send mail when any user send message","wpqa"),
				"answer_on_your_question" => esc_html__("Send mail when any user answer on your question","wpqa"),
				"answer_question_follow"  => esc_html__("Send mail when any user answer on your following question","wpqa"),
				"notified_reply"          => esc_html__("Send mail when any user reply on your answer","wpqa"),
				"notification_mention"    => esc_html__("Send mail when any user mentioned you","wpqa"),
				"unsubscribe_mails"       => esc_html__("Unsubscribe form all the mails","wpqa"),
			)
		);
		
		$options[] = array(
			'name' => esc_html__('Signup setting','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name'    => esc_html__('Signup popup style','wpqa'),
			'desc'    => esc_html__('Choose signup pop up style from here.','wpqa'),
			'id'      => 'signup_style',
			'options' => array(
				'style_1' => esc_html__('Style 1','wpqa'),
				'style_2' => esc_html__('Style 2','wpqa'),
			),
			'std'     => 'style_1',
			'type'    => 'radio'
		);

		if (has_discy()) {
				$options[] = array(
				'div'       => 'div',
				'condition' => 'signup_style:not(style_2)',
				'type'      => 'heading-2'
			);
		}

		$options[] = array(
			'name'    => esc_html__('Logo for the signup pop up','wpqa'),
			'id'      => 'logo_signup',
			'type'    => 'upload',
			'std'     => (has_himer() || has_knowly()?$imagepath_theme."logo-mini.png":""),
			'options' => array("height" => "logo_signup_height","width" => "logo_signup_width"),
		);

		$options[] = array(
			'name' => esc_html__('Logo retina for the signup pop up','wpqa'),
			'id'   => 'logo_signup_retina',
			'std'  => (has_himer() || has_knowly()?$imagepath_theme."logo-mini.png":""),
			'type' => 'upload'
		);
		
		$options[] = array(
			'name' => esc_html__('Logo height','wpqa'),
			"id"   => "logo_signup_height",
			"type" => "sliderui",
			'std'  => '30',
			"step" => "1",
			"min"  => "0",
			"max"  => "80"
		);
		
		$options[] = array(
			'name' => esc_html__('Logo width','wpqa'),
			"id"   => "logo_signup_width",
			"type" => "sliderui",
			'std'  => '30',
			"step" => "1",
			"min"  => "0",
			"max"  => "170"
		);

		if (has_discy()) {
			$options[] = array(
				'name' => esc_html__('Text for the signup popup after the logo or the normal text','wpqa'),
				'id'   => 'text_signup',
				'type' => 'text'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => 'signup_style:is(style_2)',
				'type'      => 'heading-2'
			);

			$options[] = array(
				'name' => esc_html__('Signup image','wpqa'),
				'id'   => 'signup_image',
				'type' => 'upload'
			);
			
			$options[] = array(
				"type" => "textarea",
				"id"   => "signup_details",
				"std"  => "Sign Up to our social questions and Answers Engine to ask questions, answer people's questions, and connect with other people.",
				"name" => esc_html__('Details for signup pop up','wpqa')
			);

			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
		}else {
			$options[] = array(
				"type" => "textarea",
				"id"   => "signup_details",
				"std"  => "Sign up to join our community!",
				"name" => esc_html__('Details for signup pop up','wpqa')
			);

			$options[] = array(
				'name'      => esc_html__('Signup image','wpqa'),
				'id'        => 'signup_image',
				'condition' => 'signup_style:is(style_2)',
				'type'      => 'upload'
			);
		}
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'login_setting',
			'name' => esc_html__('Login Setting','wpqa')
		);
		
		$options[] = array(
			'name'    => esc_html__('Login','wpqa'),
			'desc'    => esc_html__('Choose the status of the login on your site.','wpqa'),
			'id'      => 'activate_login',
			'std'     => 'enabled',
			'options' => array(
				"enabled"  => esc_html__("Enabled","wpqa"),
				"disabled" => esc_html__("Disabled","wpqa"),
			),
			'type'    => 'radio'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'activate_login:not(disabled)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Make the login works without ajax','wpqa'),
			'desc' => esc_html__('Select ON if you want to make the login works without ajax to avoid the problems with the cache plugin.','wpqa'),
			'id'   => 'stop_login_ajax',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Make the login works with popup','wpqa'),
			'desc' => esc_html__('Select ON if you want to make the login works with popup.','wpqa'),
			'id'   => 'login_popup',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('After login go to?','wpqa'),
			'id'      => 'after_login',
			'std'     => "same_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"home" => esc_html__("Home","wpqa"),"profile" => esc_html__("Profile","wpqa"),"custom_link" => esc_html__("Custom link","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__("Type the link if you don't like above","wpqa"),
			'id'        => 'after_login_link',
			'condition' => 'after_login:is(custom_link)',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name'    => esc_html__('After Log out go to?','wpqa'),
			'id'      => 'after_logout',
			'std'     => "same_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"home" => esc_html__("Home","wpqa"),"custom_link" => esc_html__("Custom link","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__("Type the link if you don't like above","wpqa"),
			'id'        => 'after_logout_link',
			'condition' => 'after_logout:is(custom_link)',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate after x view of pages will need to login','wpqa'),
			'id'   => 'activate_need_to_login',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'      => esc_html__('After how many view pages will need to login?','wpqa'),
			"id"        => "need_login_pages",
			'condition' => 'activate_need_to_login:not(0)',
			"type"      => "sliderui",
			'std'       => '2',
			"step"      => "1",
			"min"       => "1",
			"max"       => "10"
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'group',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'group',
			'id'   => 'login_setting',
			'condition' => 'activate_login:not(disabled)',
			'name' => esc_html__('Login popup style','wpqa')
		);
		
		$options[] = array(
			'name'    => esc_html__('Login popup style','wpqa'),
			'desc'    => esc_html__('Choose login popup style from here.','wpqa'),
			'id'      => 'login_style',
			'options' => array(
				'style_1' => esc_html__('Style 1','wpqa'),
				'style_2' => esc_html__('Style 2','wpqa'),
			),
			'std'     => 'style_1',
			'type'    => 'radio'
		);

		if (has_discy()) {
			$options[] = array(
				'div'       => 'div',
				'condition' => 'login_style:not(style_2)',
				'type'      => 'heading-2'
			);
		}

		$options[] = array(
			'name'    => esc_html__('Logo for the login popup','wpqa'),
			'id'      => 'logo_login',
			'type'    => 'upload',
			'std'     => (has_himer() || has_knowly()?$imagepath_theme."logo-mini.png":""),
			'options' => array("height" => "logo_login_height","width" => "logo_login_width"),
		);

		$options[] = array(
			'name' => esc_html__('Logo retina for the login popup','wpqa'),
			'id'   => 'logo_login_retina',
			'type' => 'upload',
			'std'  => (has_himer() || has_knowly()?$imagepath_theme."logo-mini.png":""),
		);
		
		$options[] = array(
			'name' => esc_html__('Logo height','wpqa'),
			"id"   => "logo_login_height",
			"type" => "sliderui",
			'std'  => '30',
			"step" => "1",
			"min"  => "0",
			"max"  => "80"
		);
		
		$options[] = array(
			'name' => esc_html__('Logo width','wpqa'),
			"id"   => "logo_login_width",
			"type" => "sliderui",
			'std'  => '30',
			"step" => "1",
			"min"  => "0",
			"max"  => "170"
		);

		if (has_discy()) {
			$options[] = array(
				'name' => esc_html__('Text for the login popup after the logo or the normal text','wpqa'),
				'id'   => 'text_login',
				'type' => 'text'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => 'login_style:is(style_2)',
				'type'      => 'heading-2'
			);

			$options[] = array(
				'name' => esc_html__('Login image','wpqa'),
				'id'   => 'login_image',
				'type' => 'upload'
			);
			
			$options[] = array(
				"type" => "textarea",
				"id"   => "login_details",
				"std"  => "Login to our social questions & Answers Engine to ask questions answer people's questions & connect with other people.",
				"name" => esc_html__('Details for login popup','wpqa')
			);

			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
		}else {
			$options[] = array(
				"type" => "textarea",
				"id"   => "login_details",
				"std"  => "Please sign in to your account!",
				"name" => esc_html__('Details for login popup','wpqa')
			);

			$options[] = array(
				'name'      => esc_html__('Login image','wpqa'),
				'id'        => 'login_image',
				'condition' => 'login_style:is(style_2)',
				'type'      => 'upload'
			);
		}

		$options[] = array(
			'type' => 'group',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'group',
			'id'   => 'login_setting',
			'name' => esc_html__('Unlogged page setting','wpqa')
		);

		$options[] = array(
			'name' => esc_html__('Make the site for the logged users only?','wpqa'),
			'desc' => esc_html__('Select ON to activate the site for the logged users only.','wpqa'),
			'id'   => 'site_users_only',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'site_users_only:not(0)',
			'type'      => 'heading-2'
		);

		$login_pages = array(
			"questions" => esc_html__('Questions','wpqa'),
			"posts"     => esc_html__("Posts","wpqa"),
		);

		if ($activate_knowledgebase == true) {
			$login_pages["knowledgebases"] = esc_html__("Knowledgebases","wpqa");
		}

		$options[] = array(
			'name'    => esc_html__('Select which pages do you need it to work for the unlogged users','wpqa'),
			'id'      => 'login_pages',
			'type'    => 'multicheck',
			'options' => $login_pages
		);

		$options[] = array(
			'name'    => esc_html__('Form style','wpqa'),
			'desc'    => esc_html__('Choose the form style from here.','wpqa'),
			'id'      => 'form_style',
			'options' => array(
				'login'    => esc_html__('Login','wpqa'),
				'register' => esc_html__('Register','wpqa'),
			),
			'std'     => 'login',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name'    => esc_html__('Page style','wpqa'),
			'desc'    => esc_html__('Choose page style from here.','wpqa'),
			'id'      => 'register_style',
			'options' => array(
				'style_1'  => 'Style 1',
				'style_2'  => 'Style 2',
			),
			'std'     => 'style_1',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name'    => esc_html__('Upload the background','wpqa'),
			'desc'    => esc_html__('Upload the background for the unlogged page','wpqa'),
			'id'      => 'register_background',
			'type'    => 'background',
			'options' => array('color' => '','image' => ''),
			'std'     => array(
				'color' => '#272930',
				'image' => $imagepath_theme."register.png"
			)
		);
		
		$options[] = array(
			"name" => esc_html__('Choose the background opacity','wpqa'),
			"desc" => esc_html__('Choose the background opacity from here','wpqa'),
			"id"   => "register_opacity",
			"type" => "sliderui",
			'std'  => 30,
			"step" => "5",
			"min"  => "0",
			"max"  => "100"
		);
		
		$options[] = array(
			'name'    => esc_html__("Choose from here which menu will show for unlogged users.","wpqa"),
			'id'      => 'register_menu',
			'type'    => 'select',
			'options' => $menus
		);
		
		$options[] = array(
			'name' => esc_html__('The headline','wpqa'),
			'desc' => esc_html__('Type the Headline from here','wpqa'),
			'id'   => 'register_headline',
			'type' => 'text',
			'std'  => "Join the world's biggest Q & A network!"
		);
		
		$options[] = array(
			'name' => esc_html__('The paragraph','wpqa'),
			'desc' => esc_html__('Type the Paragraph from here','wpqa'),
			'id'   => 'register_paragraph',
			'type' => 'textarea',
			'std'  => "Login to our social questions & Answers Engine to ask questions answer people's questions & connect with other people."
		);

		if (has_himer() || has_knowly()) {
			$options[] = array(
				'name' => esc_html__('Big button on the header enable or disable?','wpqa'),
				'desc' => esc_html__('Select ON to enable the big button.','wpqa'),
				'id'   => 'register_big_button',
				'std'  => 'on',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => 'register_big_button:not(0)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'      => 'register_big_button_target',
				'std'     => "new_page",
				'type'    => 'select',
				'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			);
			
			$options[] = array(
				'name' => esc_html__('Type the button link','wpqa'),
				'id'   => 'register_big_button_link',
				'type' => 'text'
			);
			
			$options[] = array(
				'name' => esc_html__('Type the button text','wpqa'),
				'id'   => 'register_big_button_text',
				'type' => 'text'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'name'    => esc_html__('First button','wpqa'),
				'desc'    => esc_html__('Choose button type.','wpqa'),
				'id'      => 'register_first_button',
				'options' => array(
					'off'    => esc_html__('OFF','wpqa'),
					'login'  => esc_html__('Login','wpqa'),
					'signup' => esc_html__('Signup','wpqa'),
				),
				'std'     => 'signup',
				'type'    => 'radio'
			);
			
			$options[] = array(
				'name' => esc_html__('Second button enable or disable?','wpqa'),
				'desc' => esc_html__('Select ON to enable the second button.','wpqa'),
				'id'   => 'register_second_button',
				'std'  => 'on',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => 'register_second_button:not(0)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'      => 'register_second_button_target',
				'std'     => "new_page",
				'type'    => 'select',
				'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			);
			
			$options[] = array(
				'name' => esc_html__('Type the button link','wpqa'),
				'id'   => 'register_second_button_link',
				'type' => 'text'
			);
			
			$options[] = array(
				'name' => esc_html__('Type the button text','wpqa'),
				'id'   => 'register_second_button_text',
				'type' => 'text'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
		}
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$allow_post_types = array(
			"posts"     => array("sort" => esc_html__('Posts','wpqa'),"value" => ""),
			"questions" => array("sort" => esc_html__('Questions','wpqa'),"value" => ""),
		);

		if ($activate_knowledgebase == true) {
			$allow_post_types["knowledgebases"] = array("sort" => esc_html__('Knowledgebases','wpqa'),"value" => "");
		}
		
		$options[] = array(
			'name'      => esc_html__("Choose if you want the posts or questions work for the unlogged users","wpqa"),
			'id'        => 'allow_post_types',
			'type'      => 'multicheck',
			'sort'      => 'yes',
			'condition' => 'activate_need_to_login:not(0),site_users_only:not(0)',
			'operator'  => 'or',
			'std'       => $allow_post_types,
			'options'   => $allow_post_types
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'edit_profile',
			'name' => esc_html__('Edit Profile','wpqa')
		);
		
		$edit_profile_items_1 = array(
			"email"         => array("sort" => esc_html__('E-mail','wpqa'),"value" => "email","default" => "yes"),
			"nickname"      => array("sort" => esc_html__('Nickname','wpqa'),"value" => "nickname"),
			"first_name"    => array("sort" => esc_html__('First Name','wpqa'),"value" => "first_name"),
			"last_name"     => array("sort" => esc_html__('Last Name','wpqa'),"value" => "last_name"),
			"display_name"  => array("sort" => esc_html__('Display Name','wpqa'),"value" => "display_name"),
			"image_profile" => array("sort" => esc_html__('Image Profile','wpqa'),"value" => "image_profile"),
			"cover"         => array("sort" => esc_html__('Cover','wpqa'),"value" => "cover"),
			"country"       => array("sort" => esc_html__('Country','wpqa'),"value" => "country"),
			"city"          => array("sort" => esc_html__('City','wpqa'),"value" => "city"),
			"phone"         => array("sort" => esc_html__('Phone','wpqa'),"value" => "phone"),
			"gender"        => array("sort" => esc_html__('Gender','wpqa'),"value" => "gender"),
			"age"           => array("sort" => esc_html__('Age','wpqa'),"value" => "age"),
		);
		
		$options[] = array(
			'name'    => esc_html__("Select what to show at edit profile at the Basic Information section","wpqa"),
			'id'      => 'edit_profile_items_1',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $edit_profile_items_1,
			'options' => apply_filters(wpqa_prefix_theme."_options_edit_profile_items_1",$edit_profile_items_1),
		);
		
		$options[] = array(
			'name' => esc_html__('Confirm email after editing email enable or disable','wpqa'),
			'id'   => 'confirm_edit_email',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('You need to activate the cover option from the User settings/General Setting','wpqa'),
			'condition' => 'cover_image:not(on),edit_profile_items_1:has(cover)',
			'type'      => 'info'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'edit_profile_items_1:has(first_name),edit_profile_items_1:has(last_name),edit_profile_items_1:has(display_name),edit_profile_items_1:has(image_profile),edit_profile_items_1:has(cover),edit_profile_items_1:has(gender),edit_profile_items_1:has(country),edit_profile_items_1:has(city),edit_profile_items_1:has(phone),edit_profile_items_1:has(age)',
			'operator'  => 'or',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Required setting','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name'      => esc_html__('First name in edit profile is required.','wpqa'),
			'id'        => 'first_name_required',
			'condition' => 'edit_profile_items_1:has(first_name)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Last name in edit profile is required.','wpqa'),
			'id'        => 'last_name_required',
			'condition' => 'edit_profile_items_1:has(last_name)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Display name in edit profile is required.','wpqa'),
			'id'        => 'display_name_required',
			'condition' => 'edit_profile_items_1:has(display_name)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Profile picture in edit profile is required','wpqa'),
			'id'        => 'profile_picture_required',
			'condition' => 'edit_profile_items_1:has(image_profile)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Profile cover in edit profile is required','wpqa'),
			'id'        => 'profile_cover_required',
			'condition' => 'edit_profile_items_1:has(cover)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Gender in edit profile is required.','wpqa'),
			'id'        => 'gender_required',
			'condition' => 'edit_profile_items_1:has(gender)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Country in edit profile is required.','wpqa'),
			'id'        => 'country_required',
			'condition' => 'edit_profile_items_1:has(country)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('City in edit profile is required.','wpqa'),
			'id'        => 'city_required',
			'condition' => 'edit_profile_items_1:has(city)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Phone in edit profile is required.','wpqa'),
			'id'        => 'phone_required',
			'condition' => 'edit_profile_items_1:has(phone)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Age in edit profile is required.','wpqa'),
			'id'        => 'age_required',
			'condition' => 'edit_profile_items_1:has(age)',
			'type'      => 'checkbox'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options = apply_filters('wpqa_options_after_required_edit_profile',$options);
		
		$options[] = array(
			'name' => esc_html__('Social Profiles section','wpqa'),
			'type' => 'info',
		);
		
		$edit_profile_items_2 = array(
			"facebook"  => array("sort" => esc_html__('Facebook','wpqa'),"value" => "facebook"),
			"twitter"   => array("sort" => esc_html__('Twitter','wpqa'),"value" => "twitter"),
			"tiktok"    => array("sort" => esc_html__('TikTok','wpqa'),"value" => "tiktok"),
			"youtube"   => array("sort" => esc_html__('Youtube','wpqa'),"value" => "youtube"),
			"vimeo"     => array("sort" => esc_html__('Vimeo','wpqa'),"value" => "vimeo"),
			"linkedin"  => array("sort" => esc_html__('Linkedin','wpqa'),"value" => "linkedin"),
			"instagram" => array("sort" => esc_html__('Instagram','wpqa'),"value" => "instagram"),
			"pinterest" => array("sort" => esc_html__('Pinterest','wpqa'),"value" => "pinterest"),
		);
		
		$options[] = array(
			'name'    => esc_html__("Select what to show at edit profile at the Social Profiles section","wpqa"),
			'id'      => 'edit_profile_items_2',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $edit_profile_items_2,
			'options' => $edit_profile_items_2
		);
		
		$options[] = array(
			'name' => esc_html__('About Me section','wpqa'),
			'type' => 'info',
		);
		
		$edit_profile_items_3 = array(
			"website"            => array("sort" => esc_html__('Website','wpqa'),"value" => "website"),
			"bio"                => array("sort" => esc_html__('Professional Bio','wpqa'),"value" => "bio"),
			"profile_credential" => array("sort" => esc_html__('Profile credential','wpqa'),"value" => "profile_credential"),
			"private_pages"      => array("sort" => esc_html__('Private Pages','wpqa'),"value" => "private_pages"),
			"received_message"   => array("sort" => esc_html__('Received message from the users','wpqa'),"value" => "received_message"),
		);
		
		$options[] = array(
			'name'    => esc_html__("Select what to show at edit profile at the About Me section","wpqa"),
			'id'      => 'edit_profile_items_3',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $edit_profile_items_3,
			'options' => $edit_profile_items_3
		);

		$options[] = array(
			'name'      => esc_html__('URL in edit profile form is required','wpqa'),
			'id'        => 'url_required_profile',
			'condition' => 'edit_profile_items_3:has(website)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Editor enable or disable for professional bio','wpqa'),
			'id'        => 'bio_editor',
			'condition' => 'edit_profile_items_3:has(bio)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'edit_profile_items_3:has(profile_credential)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Profile credential in edit profile is required.','wpqa'),
			'id'   => 'profile_credential_required',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Add the maximum length for the profile credential, leave it empty if you need it unlimited.','wpqa'),
			'id'   => 'profile_credential_maximum',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => (has_discy()?esc_html__('Custom categories at the left menu','wpqa'):esc_html__('Custom categories at the second menu','wpqa')),
			'type' => 'info',
		);
		
		$options[] = array(
			'name' => (has_discy()?esc_html__('Do you need to allow the users to add a custom categories at the left menu?','wpqa'):esc_html__('Do you need to allow the users to add a custom categories at the second menu?','wpqa')),
			'desc' => (has_discy()?esc_html__('Select ON if you need the users to add a custom categories at the left menu.','wpqa'):esc_html__('Select ON if you need the users to add a custom categories at the second menu.','wpqa')),
			'id'   => 'custom_left_menu',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => (has_discy()?esc_html__('Put from here after which number of the items you need to show the custom categories for the left menu','wpqa'):esc_html__('Put from here after which number of the items you need to show the custom categories for the second menu','wpqa')),
			'id'        => 'left_menu_category_after',
			'std'       => '2',
			'condition' => 'custom_left_menu:not(0)',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Mails section','wpqa'),
			'type' => 'info',
		);

		$edit_profile_items_4 = array(
			"question_schedules"      => array("sort" => esc_html__('Send schedule mails for the users as a list with recent questions','wpqa'),"value" => "question_schedules"),
			"post_schedules"          => array("sort" => esc_html__('Send schedule mails for the users as a list with recent posts','wpqa'),"value" => "post_schedules"),
			"send_emails"             => array("sort" => esc_html__('Send mail when any users ask questions','wpqa'),"value" => "send_emails"),
			"send_emails_post"        => array("sort" => esc_html__('Send mail when any users add posts','wpqa'),"value" => "send_emails_post"),
			"new_payment_mail"        => array("sort" => esc_html__('Send mail when made new payment','wpqa'),"value" => "new_payment_mail"),
			"send_message_mail"       => array("sort" => esc_html__('Send mail when any user send message','wpqa'),"value" => "send_message_mail"),
			"answer_on_your_question" => array("sort" => esc_html__('Send mail when any user answer on your question','wpqa'),"value" => "answer_on_your_question"),
			"answer_question_follow"  => array("sort" => esc_html__('Send mail when any user answer on your following question','wpqa'),"value" => "answer_question_follow"),
			"notified_reply"          => array("sort" => esc_html__('Send mail when any user reply on your answer','wpqa'),"value" => "notified_reply"),
			"notification_mention"    => array("sort" => esc_html__('Send mail when any user mentioned you','wpqa'),"value" => "notification_mention"),
			"unsubscribe_mails"       => array("sort" => esc_html__('Unsubscribe form all the mails','wpqa'),"value" => "unsubscribe_mails"),
		);
		
		$options[] = array(
			'name'    => esc_html__("Select what to show at edit profile at the mails section","wpqa"),
			'id'      => 'edit_profile_items_4',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $edit_profile_items_4,
			'options' => $edit_profile_items_4
		);
		
		$options[] = array(
			'name' => esc_html__('Privacy account','wpqa'),
			'type' => 'info',
		);

		$options[] = array(
			'name' => esc_html__('Do you like to allow the users to choose their privacy?','wpqa'),
			'id'   => 'privacy_account',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		$privacy_options = array(
			"public"  => esc_html__('Public','wpqa'),
			"members" => esc_html__("All members","wpqa"),
			"me"      => esc_html__("Only me","wpqa"),
		);

		$privacy_array = array(
			"email"      => esc_html__("Email","wpqa"),
			"country"    => esc_html__("Country","wpqa"),
			"city"       => esc_html__("City","wpqa"),
			"phone"      => esc_html__("Phone","wpqa"),
			"gender"     => esc_html__("Gender","wpqa"),
			"age"        => esc_html__("Age","wpqa"),
			"social"     => esc_html__("Social links","wpqa"),
			"website"    => esc_html__("Website","wpqa"),
			"bio"        => esc_html__("Biography","wpqa"),
			"credential" => esc_html__("Profile credential","wpqa")
		);

		foreach ($privacy_array as $key_privacy => $value_privacy) {
			$options[] = array(
				'name'    => $value_privacy,
				'id'      => 'privacy_'.$key_privacy,
				'type'    => 'select',
				'options' => $privacy_options,
				'std'     => ($key_privacy == "email"?"me":"public")
			);
		}

		$options[] = array(
			'name' => esc_html__('Delete account','wpqa'),
			'type' => 'info',
		);

		$options[] = array(
			'name' => esc_html__('Do you like to allow the users to delete their account?','wpqa'),
			'id'   => 'delete_account',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'      => esc_html__("Choose the roles you need to allow them to delete their account.","wpqa"),
			'id'        => 'delete_account_groups',
			'condition' => 'delete_account:not(0)',
			'type'      => 'multicheck',
			'options'   => $roles_no_admin,
			'std'       => array('editor' => 'editor','contributor' => 'contributor','subscriber' => 'subscriber','author' => 'author'),
		);
		
		$options[] = array(
			'name' => esc_html__('Profile Strength / To use this feature you need to add widget "Profile Strength"','wpqa'),
			'type' => 'info',
		);

		$profile_strength_std = apply_filters(wpqa_prefix_theme."_filter_profile_strength_std",array(
				"avatar"       => "avatar",
				"cover"        => "cover",
				"credential"   => "credential",
				"follow_cats"  => "follow_cats",
				"follow_tags"  => "follow_tags",
				"follow_user"  => "follow_user",
				"ask_question" => "ask_question",
				"answer"       => "answer",
			)
		);

		$profile_strength = apply_filters(wpqa_prefix_theme."_filter_profile_strength",array(
				"avatar"       => esc_html__('User Avatar','wpqa'),
				"cover"        => esc_html__('User Cover','wpqa'),
				"credential"   => esc_html__('Profile Credential','wpqa'),
				"follow_cats"  => esc_html__("Follow Categories","wpqa"),
				"follow_tags"  => esc_html__("Follow Tags","wpqa"),
				"follow_user"  => esc_html__("Follow Users","wpqa"),
				"ask_question" => esc_html__("Ask first question","wpqa"),
				"answer"       => esc_html__("Add Answers","wpqa"),
			)
		);

		$options[] = array(
			'name'    => esc_html__('Select the items for the profile strength','wpqa'),
			'id'      => 'profile_strength',
			'type'    => 'multicheck',
			'std'     => $profile_strength_std,
			'options' => $profile_strength
		);

		$options[] = array(
			'name'      => esc_html__('Select the number for the profile strength for the following categories','wpqa'),
			'id'        => 'profile_follow_cats',
			'type'      => 'text',
			'std'       => '3',
			'condition' => 'profile_strength:has(follow_cats)',
		);

		$options[] = array(
			'name'      => esc_html__('Select the number for the profile strength for the following tags','wpqa'),
			'id'        => 'profile_follow_tags',
			'type'      => 'text',
			'std'       => '3',
			'condition' => 'profile_strength:has(follow_tags)',
		);

		$options[] = array(
			'name'      => esc_html__('Select the number for the profile strength for the following users','wpqa'),
			'id'        => 'profile_follow_users',
			'type'      => 'text',
			'std'       => '3',
			'condition' => 'profile_strength:has(follow_user)',
		);

		$options[] = array(
			'name'      => esc_html__('Select the number for the profile strength for the answers on the questions','wpqa'),
			'id'        => 'profile_answer',
			'type'      => 'text',
			'std'       => '3',
			'condition' => 'profile_strength:has(answer)',
		);
		
		$options[] = array(
			"name"      => esc_html__('The percent for avatar','wpqa'),
			"id"        => "percent_avatar",
			"type"      => "sliderui",
			'std'       => 10,
			"step"      => "1",
			"min"       => "1",
			"condition" => "profile_strength:has(avatar)",
			"max"       => "100"
		);
		
		$options[] = array(
			"name"      => esc_html__('The percent for cover','wpqa'),
			"id"        => "percent_cover",
			"type"      => "sliderui",
			'std'       => 10,
			"step"      => "1",
			"min"       => "1",
			"condition" => "profile_strength:has(cover)",
			"max"       => "100"
		);
		
		$options[] = array(
			"name"      => esc_html__('The percent for credential','wpqa'),
			"id"        => "percent_credential",
			"type"      => "sliderui",
			'std'       => 10,
			"step"      => "1",
			"min"       => "1",
			"condition" => "profile_strength:has(credential)",
			"max"       => "100"
		);
		
		$options[] = array(
			"name"      => esc_html__('The percent for follow cats','wpqa'),
			"id"        => "percent_follow_cats",
			"type"      => "sliderui",
			'std'       => 20,
			"step"      => "1",
			"min"       => "1",
			"condition" => "profile_strength:has(follow_cats)",
			"max"       => "100"
		);
		
		$options[] = array(
			"name"      => esc_html__('The percent for follow tags','wpqa'),
			"id"        => "percent_follow_tags",
			"type"      => "sliderui",
			'std'       => 20,
			"step"      => "1",
			"min"       => "1",
			"condition" => "profile_strength:has(follow_tags)",
			"max"       => "100"
		);
		
		$options[] = array(
			"name"      => esc_html__('The percent for follow user','wpqa'),
			"id"        => "percent_follow_user",
			"type"      => "sliderui",
			'std'       => 20,
			"step"      => "1",
			"min"       => "1",
			"condition" => "profile_strength:has(follow_user)",
			"max"       => "100"
		);
		
		$options[] = array(
			"name"      => esc_html__('The percent for ask question','wpqa'),
			"id"        => "percent_ask_question",
			"type"      => "sliderui",
			'std'       => 10,
			"step"      => "1",
			"min"       => "1",
			"condition" => "profile_strength:has(ask_question)",
			"max"       => "100"
		);
		
		$options[] = array(
			"name"      => esc_html__('The percent for answer','wpqa'),
			"id"        => "percent_answer",
			"type"      => "sliderui",
			'std'       => 20,
			"step"      => "1",
			"min"       => "1",
			"condition" => "profile_strength:has(answer)",
			"max"       => "100"
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_after_percent_answer',$options);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'forgot_password',
			'name' => esc_html__('Forgot Password','wpqa')
		);

		$options[] = array(
			'name'      => esc_html__('You must activate the login at your site to see the options from "Users settings/Login Setting".','wpqa'),
			'type'      => 'info',
			'condition' => 'activate_login:is(disabled)',
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'activate_login:not(disabled)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Make the forgot password works with popup','wpqa'),
			'desc' => esc_html__('Select ON if you want to make the forgot password works with popup.','wpqa'),
			'id'   => 'lost_pass_popup',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Limitations of sending email while user requests forgot password per hour','wpqa'),
			"id"   => "limit_forgot_password",
			"type" => "sliderui",
			'std'  => '5',
			"step" => "1",
			"min"  => "0",
			"max"  => "20"
		);
		
		$options[] = array(
			'name'    => esc_html__('Forgot password pop up style','wpqa'),
			'desc'    => esc_html__('Choose Forgot password pop up style from here.','wpqa'),
			'id'      => 'pass_style',
			'options' => array(
				'style_1' => esc_html__('Style 1','wpqa'),
				'style_2' => esc_html__('Style 2','wpqa'),
			),
			'std'     => 'style_1',
			'type'    => 'radio'
		);

		if (has_discy()) {
			$options[] = array(
				'div'       => 'div',
				'condition' => 'pass_style:not(style_2)',
				'type'      => 'heading-2'
			);
		}

		$options[] = array(
			'name'    => esc_html__('Logo for the forgot password pop up','wpqa'),
			'id'      => 'logo_pass',
			'type'    => 'upload',
			'std'     => (has_himer() || has_knowly()?$imagepath_theme."logo-mini.png":""),
			'options' => array("height" => "logo_pass_height","width" => "logo_pass_width"),
		);

		$options[] = array(
			'name' => esc_html__('Logo retina for the forgot password pop up','wpqa'),
			'id'   => 'logo_pass_retina',
			'std'  => (has_himer() || has_knowly()?$imagepath_theme."logo-mini.png":""),
			'type' => 'upload'
		);
		
		$options[] = array(
			'name' => esc_html__('Logo height','wpqa'),
			"id"   => "logo_pass_height",
			"type" => "sliderui",
			'std'  => '30',
			"step" => "1",
			"min"  => "0",
			"max"  => "80"
		);
		
		$options[] = array(
			'name' => esc_html__('Logo width','wpqa'),
			"id"   => "logo_pass_width",
			"type" => "sliderui",
			'std'  => '30',
			"step" => "1",
			"min"  => "0",
			"max"  => "170"
		);

		if (has_discy()) {
			$options[] = array(
				'name' => esc_html__('Text for the forgot password pop up after the logo or the normal text','wpqa'),
				'id'   => 'text_pass',
				'type' => 'text'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => 'pass_style:is(style_2)',
				'type'      => 'heading-2'
			);

			$options[] = array(
				'name' => esc_html__('Forgot password image','wpqa'),
				'id'   => 'pass_image',
				'type' => 'upload'
			);
			
			$options[] = array(
				"type" => "textarea",
				"id"   => "pass_details",
				"std"  => "Lost your password? Please enter your email address. You will receive a link and will create a new password via email.",
				"name" => esc_html__('Details for forgot password pop up','wpqa')
			);

			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
		}else {
			$options[] = array(
				"type" => "textarea",
				"id"   => "pass_details",
				"std"  => "Lost your password? Please enter your email address. You will receive a link and will create a new password via email.",
				"name" => esc_html__('Details for forgot password pop up','wpqa')
			);

			$options[] = array(
				'name'      => esc_html__('Forgot password image','wpqa'),
				'id'        => 'pass_image',
				'condition' => 'pass_style:is(style_2)',
				'type'      => 'upload'
			);
		}

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'ask_users',
			'name' => esc_html__('Ask Users','wpqa')
		);
		
		$options[] = array(
			'name' => esc_html__('Ask question to the users','wpqa'),
			'desc' => esc_html__('Any one can ask question to the users enable or disable.','wpqa'),
			'id'   => 'ask_question_to_users',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_question_to_users:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Click ON, if you need to remove the asked question slug and choose "Post name" from WordPress Settings/Permalinks.','wpqa'),
			'id'   => 'remove_asked_question_slug',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Click ON, if you need to make the asked question slug with number instant of title and choose "Post name" from WordPress Settings/Permalinks.','wpqa'),
			'id'   => 'asked_question_slug_numbers',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Asked question slug','wpqa'),
			'desc'      => esc_html__('Add your asked question slug.','wpqa'),
			'id'        => 'asked_question_slug',
			'std'       => wpqa_asked_questions_type,
			'condition' => 'remove_asked_question_slug:not(on)',
			'type'      => 'text'
		);
		
		$ask_user_items = array(
			"title_question"       => array("sort" => esc_html__('Question Title','wpqa'),"value" => "title_question"),
			"comment_question"     => array("sort" => esc_html__('Question content','wpqa'),"value" => "comment_question"),
			"anonymously_question" => array("sort" => esc_html__('Ask Anonymously','wpqa'),"value" => "anonymously_question"),
			"private_question"     => array("sort" => esc_html__('Private Question','wpqa'),"value" => "private_question"),
			"remember_answer"      => array("sort" => esc_html__('Remember Answer','wpqa'),"value" => "remember_answer"),
			"terms_active"         => array("sort" => esc_html__('Terms of Service and Privacy Policy','wpqa'),"value" => "terms_active"),
		);
		
		$options[] = array(
			'name'    => esc_html__("Select what to show at ask user question form","wpqa"),
			'id'      => 'ask_user_items',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $ask_user_items,
			'options' => $ask_user_items
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_user_items:has_not(title_question)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Excerpt type for title from the content','wpqa'),
			'desc'    => esc_html__('Choose form here the excerpt type.','wpqa'),
			'id'      => 'title_excerpt_type_user',
			'type'    => "select",
			'options' => array(
				'words'      => esc_html__('Words','wpqa'),
				'characters' => esc_html__('Characters','wpqa')
			)
		);

		$options[] = array(
			'name' => esc_html__('Excerpt title from the content','wpqa'),
			'desc' => esc_html__('Put here the excerpt title from the content.','wpqa'),
			'id'   => 'title_excerpt_user',
			'std'  => 10,
			'type' => 'text'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);

		$options[] = array(
			'name'      => esc_html__('Select the checked by default options at ask user question form','wpqa'),
			'id'        => 'add_question_default_user',
			'type'      => 'multicheck',
			'condition' => 'ask_user_items:has(anonymously_question),ask_user_items:has(private_question),ask_user_items:has(remember_answer),ask_user_items:has(terms_active)',
			'operator'  => 'or',
			'std'       => array(
				"notified" => "notified",
			),
			'options' => array(
				"notified"    => esc_html__('Notified','wpqa'),
				"private"     => esc_html__("Private question","wpqa"),
				"anonymously" => esc_html__("Ask anonymously","wpqa"),
				"terms"       => esc_html__("Terms","wpqa"),
			)
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_user_items:has(comment_question)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Details in ask question form is required','wpqa'),
			'id'   => 'content_ask_user',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable or disable the editor for details in ask question form','wpqa'),
			'id'   => 'editor_ask_user',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'ask_user_items:has(terms_active)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Terms of Service and Privacy Policy','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'terms_active_user_target',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'    => esc_html__('Terms page','wpqa'),
			'desc'    => esc_html__('Select the terms page','wpqa'),
			'id'      => 'terms_page_user',
			'type'    => 'select',
			'options' => $options_pages
		);
		
		$options[] = array(
			'name' => esc_html__("Type the terms link if you don't like a page","wpqa"),
			'id'   => 'terms_link_user',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate Privacy Policy','wpqa'),
			'desc' => esc_html__('Select ON if you want to activate Privacy Policy.','wpqa'),
			'id'   => 'privacy_policy_user',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'privacy_policy_user:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'privacy_active_target_user',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'    => esc_html__('Privacy Policy page','wpqa'),
			'desc'    => esc_html__('Select the privacy policy page','wpqa'),
			'id'      => 'privacy_page_user',
			'type'    => 'select',
			'options' => $options_pages
		);
		
		$options[] = array(
			'name' => esc_html__("Type the privacy policy link if you don't like a page","wpqa"),
			'id'   => 'privacy_link_user',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'referral_setting',
			'name' => esc_html__('Referral Setting','wpqa')
		);
		
		$options[] = array(
			'name' => esc_html__('Activate referrals to the users','wpqa'),
			'desc' => esc_html__('Any one can send referral to the users enable or disable.','wpqa'),
			'id'   => 'active_referral',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_referral:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__("Referrals slug","wpqa"),
			'desc' => esc_html__("Select the referrals slug","wpqa"),
			'id'   => 'referrals_slug',
			'type' => 'text',
			'std'  => 'referrals'
		);
		
		if ((has_himer() || has_knowly()) && !has_questy()) {
			$options[] = array(
				'name' => esc_html__('Upload the referrals background.','wpqa'),
				'id'   => 'referrals_background',
				'type' => 'upload'
			);
		}
		
		$options[] = array(
			'name' => esc_html__('The headline','wpqa'),
			'desc' => esc_html__('Type the Headline from here','wpqa'),
			'id'   => 'referrals_headline',
			'type' => 'text',
			'std'  => 'Spread the word. Earn points.'
		);
		
		$options[] = array(
			'name' => esc_html__('The paragraph','wpqa'),
			'desc' => esc_html__('Type the Paragraph from here','wpqa'),
			'id'   => 'referrals_paragraph',
			'type' => 'textarea',
			'std'  => 'We have a number of ways to help spread the word to your friends and family, Choose whatever works best for you.'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the share on referrals','wpqa'),
			'desc' => esc_html__('Share enable or disable for referrals','wpqa'),
			'id'   => 'referrals_share_on',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'      => esc_html__('Select the share options','wpqa'),
			'id'        => 'referrals_share',
			'condition' => 'referrals_share_on:not(0)',
			'type'      => 'multicheck',
			'sort'      => 'yes',
			'std'       => $share_array,
			'options'   => $share_array
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('FAQs for the referrals','wpqa'),
			'desc' => esc_html__('Select ON to activate the FAQs for the referrals.','wpqa'),
			'id'   => 'activate_faqs_referrals',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'activate_faqs_referrals:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name'     => esc_html__('FAQs referrals text','wpqa'),
			'id'       => 'faqs_referrals_text',
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);

		$options[] = array(
			'id'      => "faqs_referrals",
			'type'    => "elements",
			'button'  => esc_html__('Add a new faq','wpqa'),
			'hide'    => "yes",
			'options' => array(
				array(
					"type" => "text",
					"id"   => "text",
					"name" => esc_html__('Title','wpqa'),
				),
				array(
					"type" => "textarea",
					"id"   => "textarea",
					"name" => esc_html__('Content','wpqa'),
				),
			),
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'popup_notification',
			'name' => esc_html__('Popup Notification','wpqa')
		);
		
		$options[] = array(
			'name' => esc_html__('Note: the last popup notification only will show not all the popup notifications.','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name'    => esc_html__('Show the popup notification for the user one time only or forever','wpqa'),
			'id'      => 'popup_notification_time',
			'options' => array(
				'one_time' => esc_html__('One time','wpqa'),
				'for_ever' => esc_html__('Forever','wpqa'),
			),
			'std'     => 'one_time',
			'type'    => 'radio'
		);

		$options[] = array(
			'name'    => esc_html__('Show the popup notification at all the pages, custom pages, or home page only?','wpqa'),
			'id'      => 'popup_notification_home_pages',
			'options' => $single_home_pages,
			'std'     => 'home_page',
			'type'    => 'radio'
		);

		$options[] = array(
			'name'      => esc_html__('Page ids','wpqa'),
			'desc'      => esc_html__('Type from here the page ids','wpqa'),
			'id'        => 'popup_notification_pages',
			'type'      => 'text',
			'condition' => 'popup_notification_home_pages:is(custom_pages)'
		);

		$options[] = array(
			'name'    => esc_html__('Choose the roles or users for the popup notification','wpqa'),
			'desc'    => esc_html__('Choose from here which roles or users you want to send the popup notification.','wpqa'),
			'id'      => 'popup_notification_groups_users',
			'options' => array(
				'groups' => esc_html__('Roles','wpqa'),
				'users'  => esc_html__('Users','wpqa'),
			),
			'std'     => 'groups',
			'type'    => 'radio'
		);

		$options[] = array(
			'name'      => esc_html__("Choose the roles you need to send the popup notification.","wpqa"),
			'id'        => 'popup_notification_groups',
			'condition' => 'popup_notification_groups_users:not(users)',
			'type'      => 'multicheck',
			'options'   => $new_roles,
			'std'       => array('administrator' => 'administrator','editor' => 'editor','contributor' => 'contributor','subscriber' => 'subscriber','author' => 'author'),
		);

		$options[] = array(
			'name'      => esc_html__('Specific user ids','wpqa'),
			'id'        => 'popup_notification_specific_users',
			'condition' => 'popup_notification_groups_users:is(users)',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Popup notification text','wpqa'),
			'id'       => 'popup_notification_text',
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);

		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'popup_notification_button_target',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name' => esc_html__('Type the button link','wpqa'),
			'id'   => 'popup_notification_button_url',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Type the button text','wpqa'),
			'id'   => 'popup_notification_button_text',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('You must save your options before sending the popup notification.','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => '<a href="#" class="button button-primary send-popup-notification">'.esc_html__('Send the popup notification','wpqa').'</a>',
			'type' => 'info'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'permissions',
			'name' => esc_html__('Permissions','wpqa')
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to be able to add a custom permission.','wpqa'),
			'id'   => 'custom_permission',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'custom_permission:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Unlogged users','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to be able to ask a question.','wpqa'),
			'id'   => 'ask_question',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to be able to add a poll.','wpqa'),
			'id'   => 'add_poll',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to be able to ask other users questions.','wpqa'),
			'id'   => 'ask_other_users',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to be able to view other questions.','wpqa'),
			'id'   => 'show_question',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to auto approve the questions when media has been attached.','wpqa'),
			'id'   => 'approve_question_media',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to be able to add a category.','wpqa'),
			'id'   => 'add_category',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to be able to add an answer.','wpqa'),
			'id'   => 'add_answer',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to be able to view other answers.','wpqa'),
			'id'   => 'show_answer',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to auto approve the answers when media has been attached.','wpqa'),
			'id'   => 'approve_answer_media',
			'std'  => "on",
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Select ON to be able to add a group.','wpqa'),
			'id'   => 'add_group',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to be able to add a post.','wpqa'),
			'id'   => 'add_post',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to be able to view other posts.','wpqa'),
			'id'   => 'show_post',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to be able to add a comment.','wpqa'),
			'id'   => 'add_comment',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to be able to view other comments.','wpqa'),
			'id'   => 'show_comment',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to be able to send message.','wpqa'),
			'id'   => 'send_message',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to be able to upload images with image button on the WordPress editor.','wpqa'),
			'id'   => 'upload_images',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		if ($activate_knowledgebase == true) {
			$options[] = array(
				'name' => esc_html__('Select ON to be able to view other articles.','wpqa'),
				'id'   => 'show_knowledgebase',
				'std'  => "on",
				'type' => 'checkbox'
			);
		}
		
		$options[] = array(
			'name' => esc_html__('Setting for the user roles & Add a new role','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'id'   => 'roles',
			'type' => 'roles'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'author_setting',
			'name' => esc_html__('Author Setting','wpqa')
		);

		if (has_himer() || has_knowly()) {
			$options[] = array(
				'name'  => esc_html__('Show the author pages at the top second menu?','wpqa'),
				'id'    => 'author_tabs',
				'std'   => 'on',
				'type'  => 'checkbox',
			);
		}
		
		$options[] = array(
			'name'    => esc_html__('Post style','wpqa'),
			'desc'    => esc_html__('Choose post style from here.','wpqa'),
			'id'      => 'author_post_style',
			'options' => array(
				'default'  => esc_html__('Default','wpqa'),
				'style_1'  => esc_html__('1 column','wpqa'),
				'style_2'  => esc_html__('List style','wpqa'),
				'style_3'  => esc_html__('Columns','wpqa'),
			),
			'std'     => 'default',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'id'        => "author_sort_meta_title_image",
			'condition' => 'author_post_style:is(style_3)',
			'std'       => array(
							array("value" => "image",'name' => esc_html__('Image','wpqa'),"default" => "yes"),
							array("value" => "meta_title",'name' => esc_html__('Meta and title','wpqa'),"default" => "yes"),
						),
			'type'      => "sort",
			'options'   => array(
							array("value" => "image",'name' => esc_html__('Image','wpqa'),"default" => "yes"),
							array("value" => "meta_title",'name' => esc_html__('Meta and title','wpqa'),"default" => "yes"),
						)
		);

		if (has_himer() || has_knowly()) {
			$options[] = array(
				'name'    => esc_html__('Sidebar style','wpqa'),
				'id'      => 'author_sidebar_style',
				'std'     => 'style_2',
				'type'    => 'radio',
				'options' => array(
					'style_1' => esc_html__('Style 1','wpqa'),
					'style_2' => esc_html__('Style 2','wpqa'),
				)
			);
		}
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_end_of_users',$options);
		
		$options[] = array(
			'name' => esc_html__('Message settings','wpqa'),
			'icon' => 'email-alt',
			'type' => 'heading'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate messages to the users','wpqa'),
			'desc' => esc_html__('Any one can send message to the users enable or disable.','wpqa'),
			'id'   => 'active_message',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_message:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__("Messages slug","wpqa"),
			'desc' => esc_html__("Select the messages slug","wpqa"),
			'id'   => 'messages_slug',
			'type' => 'text',
			'std'  => 'messages'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose message status','wpqa'),
			'desc'    => esc_html__('Choose message status after user publish the message.','wpqa'),
			'id'      => 'message_publish',
			'options' => array("publish" => esc_html__("Auto publish","wpqa"),"draft" => esc_html__("Need a review","wpqa")),
			'std'     => 'draft',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name' => esc_html__('Any one can send message without register','wpqa'),
			'desc' => esc_html__('Any one can send message without register enable or disable.','wpqa'),
			'id'   => 'send_message_no_register',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable or disable the featured image in send message form','wpqa'),
			'id'   => 'featured_image_message',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'featured_image_message:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to enable the lightbox for featured image','wpqa'),
			'desc' => esc_html__('Select ON to enable the lightbox for featured image.','wpqa'),
			'id'   => 'featured_image_message_lightbox',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			"name" => esc_html__("Set the width for the featured image for the message","wpqa"),
			"id"   => "featured_image_message_width",
			"type" => "sliderui",
			'std'  => 260,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			"name" => esc_html__("Set the height for the featured image for the message","wpqa"),
			"id"   => "featured_image_message_height",
			"type" => "sliderui",
			'std'  => 185,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Details in send message form is required','wpqa'),
			'id'   => 'comment_message',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable or disable the editor for details in send message form','wpqa'),
			'id'   => 'editor_message_details',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Send mail after message has been sent?','wpqa'),
			'id'   => 'send_email_message',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate user can delete the messages','wpqa'),
			'desc' => esc_html__('Select ON if you want the user to be able to delete the messages.','wpqa'),
			'id'   => 'message_delete',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate user can see the message in the notification','wpqa'),
			'desc' => esc_html__('Select ON if you want the user to be notified if the message is seen.','wpqa'),
			'id'   => 'seen_message',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		$roles_user_only = $new_roles;
		unset($roles_user_only["wpqa_under_review"]);
		unset($roles_user_only["ban_group"]);
		unset($roles_user_only["activation"]);

		$options[] = array(
			'name'    => esc_html__('Choose the roles or users for the custom message','wpqa'),
			'desc'    => esc_html__('Choose from here which roles or users you want to send the custom message.','wpqa'),
			'id'      => 'message_groups_users',
			'options' => array(
				'groups' => esc_html__('Roles','wpqa'),
				'users'  => esc_html__('Users','wpqa'),
			),
			'std'     => 'groups',
			'type'    => 'radio'
		);

		$options[] = array(
			'name'      => esc_html__("Choose the roles you need to send the custom message.","wpqa"),
			'id'        => 'custom_message_groups',
			'condition' => 'message_groups_users:not(users)',
			'type'      => 'multicheck',
			'options'   => $new_roles,
			'std'       => array('administrator' => 'administrator','editor' => 'editor','contributor' => 'contributor','subscriber' => 'subscriber','author' => 'author'),
		);

		$options[] = array(
			'name'      => esc_html__('Specific user ids','wpqa'),
			'id'        => 'message_specific_users',
			'condition' => 'message_groups_users:is(users)',
			'type'      => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Custom message title','wpqa'),
			'id'   => 'title_custom_message',
			'std'  => 'Welcome',
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Custom message','wpqa'),
			'id'       => 'custom_message',
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'name' => esc_html__('You must save your options before send the message.','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => '<a href="#" class="button button-primary send-custom-message">'.esc_html__('Send the custom message','wpqa').'</a>',
			'type' => 'info'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$badges_setting = array(
			"badges" => esc_html__('Badge settings','wpqa'),
			"points" => esc_html__('Point settings','wpqa')
		);
		
		$options[] = array(
			'name'    => esc_html__('Badges & Point settings','wpqa'),
			'id'      => 'badges',
			'icon'    => 'star-filled',
			'type'    => 'heading',
			'std'     => 'badges',
			'options' => apply_filters(wpqa_prefix_theme."_badges_setting",$badges_setting)
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'badges',
			'name' => esc_html__('Badge settings','wpqa')
		);
		
		$options[] = array(
			"type" => "textarea",
			"id"   => "badges_details",
			"name" => esc_html__('Details for badges','wpqa')
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose the badges style','wpqa'),
			'desc'    => esc_html__('Choose from here the badges style.','wpqa'),
			'id'      => 'badges_style',
			'options' => array(
				"by_points" => esc_html__("By points","wpqa"),
				"by_groups" => esc_html__("By roles","wpqa"),
				"by_groups_points" => esc_html__("By roles and points","wpqa"),
				"by_questions" => esc_html__("By questions","wpqa"),
				"by_answers" => esc_html__("By answers","wpqa")
			),
			'std'     => 'by_points',
			'type'    => 'select'
		);
		
		$badge_elements = array(
			array(
				"type" => "text",
				"id"   => "badge_name",
				"name" => esc_html__('Badge name','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "badge_points",
				"name" => esc_html__('Points','wpqa')
			),
			array(
				"type" => "color",
				"id"   => "badge_color",
				"name" => esc_html__('Color','wpqa')
			),
			array(
				"type" => "textarea",
				"id"   => "badge_details",
				"name" => esc_html__('Details','wpqa')
			)
		);
		
		$options[] = array(
			'id'        => "badges",
			'type'      => "elements",
			'sort'      => "no",
			'hide'      => "yes",
			'button'    => esc_html__('Add a new badge','wpqa'),
			'options'   => $badge_elements,
			'condition' => 'badges_style:is(by_points)',
		);
		
		$badge_elements = array(
			array(
				"type" => "text",
				"id"   => "badge_name",
				"name" => esc_html__('Badge name','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "badge_questions",
				"name" => esc_html__('Questions','wpqa')
			),
			array(
				"type" => "color",
				"id"   => "badge_color",
				"name" => esc_html__('Color','wpqa')
			),
			array(
				"type" => "textarea",
				"id"   => "badge_details",
				"name" => esc_html__('Details','wpqa')
			)
		);
		
		$options[] = array(
			'id'        => "badges_questions",
			'type'      => "elements",
			'sort'      => "no",
			'hide'      => "yes",
			'button'    => esc_html__('Add a new badge','wpqa'),
			'options'   => $badge_elements,
			'condition' => 'badges_style:is(by_questions)',
		);
		
		$badge_elements = array(
			array(
				"type" => "text",
				"id"   => "badge_name",
				"name" => esc_html__('Badge name','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "badge_answers",
				"name" => esc_html__('Answers','wpqa')
			),
			array(
				"type" => "color",
				"id"   => "badge_color",
				"name" => esc_html__('Color','wpqa')
			),
			array(
				"type" => "textarea",
				"id"   => "badge_details",
				"name" => esc_html__('Details','wpqa')
			)
		);
		
		$options[] = array(
			'id'        => "badges_answers",
			'type'      => "elements",
			'sort'      => "no",
			'hide'      => "yes",
			'button'    => esc_html__('Add a new badge','wpqa'),
			'options'   => $badge_elements,
			'condition' => 'badges_style:is(by_answers)',
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'badges_style:is(by_groups)',
			'type'      => 'heading-2'
		);
		
		$badges_roles = $new_roles;
		unset($badges_roles["activation"]);
		unset($badges_roles["wpqa_under_review"]);
		unset($badges_roles["ban_group"]);
		
		$badge_elements = array(
			array(
				"type"    => "select",
				"id"      => "badge_name",
				"options" => $badges_roles,
				"name"    => esc_html__('Badge name','wpqa')
			),
			array(
				"type" => "color",
				"id"   => "badge_color",
				"name" => esc_html__('Color','wpqa')
			),
		);
		
		$options[] = array(
			'id'      => "badges_groups",
			'type'    => "elements",
			'sort'    => "no",
			'hide'    => "yes",
			'button'  => esc_html__('Add a new badge','wpqa'),
			'options' => $badge_elements,
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'badges_style:is(by_groups_points)',
			'type'      => 'heading-2'
		);
		
		$badge_elements = array(
			array(
				"type"    => "text",
				"id"      => "badge_name",
				"name"    => esc_html__('Badge name','wpqa')
			),
			array(
				"type"    => "select",
				"id"      => "badge_group",
				"options" => $badges_roles,
				"name"    => esc_html__('Badge role','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "badge_points",
				"name" => esc_html__('Points','wpqa')
			),
			array(
				"type" => "color",
				"id"   => "badge_color",
				"name" => esc_html__('Color','wpqa')
			),
			array(
				"type" => "textarea",
				"id"   => "badge_details",
				"name" => esc_html__('Details','wpqa')
			)
		);
		
		$options[] = array(
			'id'      => "badges_groups_points",
			'type'    => "elements",
			'sort'    => "no",
			'hide'    => "yes",
			'button'  => esc_html__('Add a new badge','wpqa'),
			'options' => $badge_elements,
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'id'   => 'points',
			'name' => esc_html__('Point settings','wpqa')
		);
		
		$options[] = array(
			'name'      => esc_html__('You must activate the points at your site to see the options from "Question settings/General settings".','wpqa'),
			'type'      => 'info',
			'condition' => 'active_points:not(on)'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_points:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			"type" => "textarea",
			"id"   => "points_details",
			"name" => esc_html__('Details for points','wpqa')
		);
		
		$options[] = array(
			'name' => esc_html__('Points for ask a new question (put it 0 for off the option)','wpqa'),
			'desc' => esc_html__('put the points choose for ask a new question','wpqa'),
			'id'   => 'point_add_question',
			'type' => 'text',
			'std'  => 0
		);
		
		$options[] = array(
			'name' => esc_html__('Points for add a new post (put it 0 for off the option)','wpqa'),
			'desc' => esc_html__('put the points choose for add a new post','wpqa'),
			'id'   => 'point_add_post',
			'type' => 'text',
			'std'  => 0
		);
		
		$options[] = array(
			'name' => esc_html__('Points for choosing the best answer','wpqa'),
			'desc' => esc_html__('put the points for choosing the best answer','wpqa'),
			'id'   => 'point_best_answer',
			'type' => 'text',
			'std'  => 5
		);
		
		$options[] = array(
			'name' => esc_html__('Points voting question','wpqa'),
			'desc' => esc_html__('put the points voting question','wpqa'),
			'id'   => 'point_voting_question',
			'type' => 'text',
			'std'  => 1
		);
		
		$options[] = array(
			'name' => esc_html__('Points for choosing a poll on the question','wpqa'),
			'desc' => esc_html__('put the points for choosing a poll on the question','wpqa'),
			'id'   => 'point_poll_question',
			'type' => 'text',
			'std'  => 1
		);
		
		$options[] = array(
			'name' => esc_html__('Points add answer','wpqa'),
			'desc' => esc_html__('put the points add answer','wpqa'),
			'id'   => 'point_add_comment',
			'type' => 'text',
			'std'  => 2
		);
		
		$options[] = array(
			'name' => esc_html__('Points voting answer','wpqa'),
			'desc' => esc_html__('put the points voting answer','wpqa'),
			'id'   => 'point_voting_answer',
			'type' => 'text',
			'std'  => 1
		);
		
		$options[] = array(
			'name' => esc_html__('Points following user','wpqa'),
			'desc' => esc_html__('put the points following user','wpqa'),
			'id'   => 'point_following_me',
			'type' => 'text',
			'std'  => 1
		);
		
		$options[] = array(
			'name' => esc_html__('Points for a new user','wpqa'),
			'desc' => esc_html__('put the points for a new user','wpqa'),
			'id'   => 'point_new_user',
			'type' => 'text',
			'std'  => 20
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_referral:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Points for a new referral','wpqa'),
			'desc' => esc_html__('put the points for a new referral','wpqa'),
			'id'   => 'points_referral',
			'type' => 'text',
			'std'  => 10
		);
		
		$options[] = array(
			'name' => esc_html__('Points for a new referral for paid membership','wpqa'),
			'desc' => esc_html__('put the points for a new for paid membership','wpqa'),
			'id'   => 'referral_membership',
			'type' => 'text',
			'std'  => 20
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'      => esc_html__('Points to add one of your each social media accounts.','wpqa'),
			'desc'      => esc_html__('put the points to add one of your each social media accounts','wpqa'),
			'id'        => 'points_social',
			'operator'  => 'or',
			'condition' => 'edit_profile_items_2:has(facebook),edit_profile_items_2:has(tiktok),edit_profile_items_2:has(twitter),edit_profile_items_2:has(youtube),edit_profile_items_2:has(vimeo),edit_profile_items_2:has(linkedin),edit_profile_items_2:has(instagram),edit_profile_items_2:has(pinterest)',
			'type'      => 'text',
			'std'       => 1
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_end_of_points',$options);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_after_points_setting',$options);
			
		$options[] = array(
			'name'    => esc_html__('Comments & Answers','wpqa'),
			'id'      => 'comment_answer',
			'icon'    => 'admin-comments',
			'type'    => 'heading',
			'std'     => 'comments_setting',
			'options' => array(
				"comments_setting" => esc_html__('Comments','wpqa'),
				"answers_setting"  => esc_html__('Answers','wpqa')
			)
		);
		
		$options[] = array(
			'name' => esc_html__('Comment settings','wpqa'),
			'id'   => 'comments_setting',
			'type' => 'heading-2'
		);

		// $options[] = array(
		// 	'name'    => esc_html__("Choose the way of the sending emails and notifications","wpqa"),
		// 	'desc'    => esc_html__("Make the send mail to the users and send notification from the site or with schedule cron job","wpqa"),
		// 	'id'      => 'way_sending_notifications_comments',
		// 	'std'     => 'site',
		// 	'type'    => 'radio',
		// 	'options' => array(
		// 		"site"    => esc_html__("From the site","wpqa"),
		// 		"cronjob" => esc_html__("Schedule cron job","wpqa")
		// 	)
		// );

		// $options[] = array(
		// 	'div'       => 'div',
		// 	'condition' => 'way_sending_notifications_comments:is(cronjob)',
		// 	'type'      => 'heading-2'
		// );

		// $options[] = array(
		// 	'name'    => esc_html__('Schedule mails time','wpqa'),
		// 	'id'      => 'schedules_time_notification_comments',
		// 	'type'    => 'radio',
		// 	'std'     => "hourly",
		// 	'options' => array(
		// 		"daily"       => esc_html__("One time daily","wpqa"),
		// 		"twicedaily"  => esc_html__("Twice times daily","wpqa"),
		// 		"hourly"      => esc_html__("Hourly","wpqa"),
		// 		"twicehourly" => esc_html__("Each 30 minutes","wpqa")
		// 	)
		// );

		// $options[] = array(
		// 	"name"      => esc_html__("Set the hour to send the mail at this hour","wpqa"),
		// 	"id"        => "schedules_time_hour_notification_comment",
		// 	'condition' => 'schedules_time_notification_comments:is(daily),schedules_time_notification_comments:is(twicedaily)',
		// 	'operator'  => 'or',
		// 	"type"      => "sliderui",
		// 	'std'       => 12,
		// 	"step"      => "1",
		// 	"min"       => "1",
		// 	"max"       => "24"
		// );

		// $options[] = array(
		// 	"name" => esc_html__("Set the number of the comments you want to send on the one time","wpqa"),
		// 	"id"   => "schedules_number_comment",
		// 	"type" => "sliderui",
		// 	'std'  => 10,
		// 	"step" => "1",
		// 	"min"  => "1",
		// 	"max"  => "100"
		// );
		
		// $options[] = array(
		// 	'name'      => esc_html__('Note: if you choose one time daily or twice times daily we will make it visible hourly to check if there are any more comments that need to send the notifications.','wpqa'),
		// 	'condition' => 'schedules_time_notification_comments:is(daily),schedules_time_notification_comments:is(twicedaily)',
		// 	'operator'  => 'or',
		// 	'type'      => 'info'
		// );

		// $options[] = array(
		// 	'type' => 'heading-2',
		// 	'end'  => 'end',
		// 	'div'  => 'div'
		// );

		if (has_himer()) {
			$options[] = array(
				'name' => esc_html__('Activate the gender on the comments and answers','wpqa'),
				'desc' => esc_html__('Select ON if you want to active the gender on the comments and answers.','wpqa'),
				'id'   => 'gender_answers',
				'std'  => 'on',
				'type' => 'checkbox'
			);

			$options[] = array(
				'name'      => esc_html__('Activate the other on the comments and answers','wpqa'),
				'desc'      => esc_html__('Select ON if you want to active the other on the comments and answers, the other means the user answer or comment without choose the gender.','wpqa'),
				'id'        => 'gender_answers_other',
				'std'       => 'on',
				'condition' => 'gender_answers:is(on)',
				'type'      => 'checkbox'
			);
		}

		$options[] = array(
			'name'    => esc_html__('Add a new comment form','wpqa'),
			'id'      => 'place_comment_form',
			'options' => array(
				'before' => esc_html__('Before comments','wpqa'),
				'after'  => esc_html__('After comments','wpqa')
			),
			'std'     => 'before',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable or disable to hide replies and show them by jQuery','wpqa'),
			'id'   => 'show_replies',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable or disable to count the comments or answers only with replies or not','wpqa'),
			'id'   => 'count_comment_only',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Note: if you need to show the load more or infinite scroll the comments you must activate from Settings/Discussion/Break comments into pages with, and add any number, also choose the displated by default, first.','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name'    => esc_html__('Pagination style','wpqa'),
			'desc'    => esc_html__('Choose pagination style from here.','wpqa'),
			'id'      => 'comment_pagination',
			'options' => array(
				'standard'        => esc_html__('Standard','wpqa'),
				'pagination'      => esc_html__('Pagination','wpqa'),
				'load_more'       => esc_html__('Load more','wpqa'),
				'infinite_scroll' => esc_html__('Infinite scroll','wpqa'),
			),
			'std'     => 'pagination',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name'    => esc_html__('Select the share options to show at the comments/answers','wpqa'),
			'id'      => 'comment_share',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $share_array,
			'options' => $share_array
		);
		
		$options[] = array(
			'name' => esc_html__('Enable or disable the editor for details in comment form','wpqa'),
			'id'   => 'comment_editor',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable or disable the editor for the replies on comments/answers','wpqa'),
			'id'   => 'activate_editor_reply',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Add your minimum limit of the number of letters for the comment, like 15, 20, if you leave it empty it will make it not important','wpqa'),
			'id'   => 'comment_min_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Add your limit of the number of letters for the comment, like 140, 200, if you leave it empty it will make it unlimited','wpqa'),
			'id'   => 'comment_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the author image in comments/answers?','wpqa'),
			'desc' => esc_html__('Author image in comments/answers enable or disable.','wpqa'),
			'id'   => 'answer_image',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate Terms of Service and privacy policy page?','wpqa'),
			'desc' => esc_html__('Select ON if you want active Terms of Service and privacy policy page.','wpqa'),
			'id'   => 'terms_active_comment',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'terms_active_comment:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Terms of Service and Privacy Policy','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Select the checked by default option','wpqa'),
			'desc' => esc_html__('Select ON if you want to checked it by default.','wpqa'),
			'id'   => 'terms_checked_comment',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'terms_active_target_comment',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'    => esc_html__('Terms page','wpqa'),
			'desc'    => esc_html__('Select the terms page','wpqa'),
			'id'      => 'terms_page_comment',
			'type'    => 'select',
			'options' => $options_pages
		);
		
		$options[] = array(
			'name' => esc_html__("Type the terms link if you don't like a page","wpqa"),
			'id'   => 'terms_link_comment',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate Privacy Policy','wpqa'),
			'desc' => esc_html__('Select ON if you want to activate Privacy Policy.','wpqa'),
			'id'   => 'privacy_policy_comment',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'privacy_policy_comment:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'      => 'privacy_active_target_comment',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'    => esc_html__('Privacy Policy page','wpqa'),
			'desc'    => esc_html__('Select the privacy policy page','wpqa'),
			'id'      => 'privacy_page_comment',
			'type'    => 'select',
			'options' => $options_pages
		);
		
		$options[] = array(
			'name' => esc_html__("Type the privacy policy link if you don't like a page","wpqa"),
			'id'   => 'privacy_link_comment',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Note: if you need all the comments/answers manually approved, From Settings/Discussion/Comment must be manually approved.','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose comments/answers status for unlogged user only','wpqa'),
			'desc'    => esc_html__('Choose comments/answers status after unlogged user publish the comments/answers.','wpqa'),
			'id'      => 'comment_unlogged',
			'options' => array("publish" => esc_html__("Auto publish","wpqa"),"draft" => esc_html__("Need a review","wpqa")),
			'std'     => 'draft',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name' => esc_html__('Send mail when comment/answer need a review','wpqa'),
			'desc' => esc_html__('Mail for comment/answer review enable or disable.','wpqa'),
			'id'   => 'send_email_draft_comments',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Edit comments/answers','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('User can edit the comments/answers?','wpqa'),
			'id'   => 'can_edit_comment',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'can_edit_comment:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			"name" => esc_html__('User can edit the comments/answers after x hours','wpqa'),
			"desc" => esc_html__('If you want the user to edit it all the time leave It to 0','wpqa'),
			"id"   => "can_edit_comment_after",
			"type" => "sliderui",
			'std'  => 1,
			"step" => "1",
			"min"  => "0",
			"max"  => "24"
		);
		
		$options[] = array(
			'name' => esc_html__('Edit comments/answers slug','wpqa'),
			'desc' => esc_html__('Put the edit comments/answers slug.','wpqa'),
			'id'   => 'edit_comments_slug',
			'std'  => 'edit-comment',
			'type' => 'text'
		);
		
		$options[] = array(
			'name'    => esc_html__('After editing comments/answers, auto approve or need to be approved again?','wpqa'),
			'desc'    => esc_html__('Press ON to auto approve','wpqa'),
			'id'      => 'comment_approved',
			'options' => array("publish" => esc_html__("Auto publish","wpqa"),"draft" => esc_html__("Need a review","wpqa")),
			'std'     => 'publish',
			'type'    => 'radio',
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Delete comments/answers','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('User can delete the comments/answers?','wpqa'),
			'id'   => 'can_delete_comment',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('When the users delete the comments/answers send it to the trash or delete it forever?','wpqa'),
			'id'        => 'delete_comment',
			'options'   => array(
				'delete' => esc_html__('Delete','wpqa'),
				'trash'  => esc_html__('Trash','wpqa'),
			),
			'std'       => 'delete',
			'condition' => 'can_delete_comment:not(0)',
			'type'      => 'radio'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Answer settings','wpqa'),
			'id'   => 'answers_setting',
			'type' => 'heading-2'
		);

		// $options[] = array(
		// 	'name'    => esc_html__("Choose the way of the sending emails and notifications","wpqa"),
		// 	'desc'    => esc_html__("Make the send mail to the users and send notification from the site or with schedule cron job","wpqa"),
		// 	'id'      => 'way_sending_notifications_answers',
		// 	'std'     => 'site',
		// 	'type'    => 'radio',
		// 	'options' => array(
		// 		"site"    => esc_html__("From the site","wpqa"),
		// 		"cronjob" => esc_html__("Schedule cron job","wpqa")
		// 	)
		// );

		// $options[] = array(
		// 	'div'       => 'div',
		// 	'condition' => 'way_sending_notifications_answers:is(cronjob)',
		// 	'type'      => 'heading-2'
		// );

		// $options[] = array(
		// 	'name'    => esc_html__('Schedule mails time','wpqa'),
		// 	'id'      => 'schedules_time_notification_answers',
		// 	'type'    => 'radio',
		// 	'std'     => "hourly",
		// 	'options' => array(
		// 		"daily"       => esc_html__("One time daily","wpqa"),
		// 		"twicedaily"  => esc_html__("Twice times daily","wpqa"),
		// 		"hourly"      => esc_html__("Hourly","wpqa"),
		// 		"twicehourly" => esc_html__("Each 30 minutes","wpqa")
		// 	)
		// );

		// $options[] = array(
		// 	"name"      => esc_html__("Set the hour to send the mail at this hour","wpqa"),
		// 	"id"        => "schedules_time_hour_notification_answer",
		// 	'condition' => 'schedules_time_notification_answers:is(daily),schedules_time_notification_answers:is(twicedaily)',
		// 	'operator'  => 'or',
		// 	"type"      => "sliderui",
		// 	'std'       => 12,
		// 	"step"      => "1",
		// 	"min"       => "1",
		// 	"max"       => "24"
		// );

		// $options[] = array(
		// 	"name" => esc_html__("Set the number of the answers you want to send on the one time","wpqa"),
		// 	"id"   => "schedules_number_answer",
		// 	"type" => "sliderui",
		// 	'std'  => 10,
		// 	"step" => "1",
		// 	"min"  => "1",
		// 	"max"  => "100"
		// );
		
		// $options[] = array(
		// 	'name'      => esc_html__('Note: if you choose one time daily or twice times daily we will make it visible hourly to check if there are any more answers that need to send the notifications.','wpqa'),
		// 	'condition' => 'schedules_time_notification_answers:is(daily),schedules_time_notification_answers:is(twicedaily)',
		// 	'operator'  => 'or',
		// 	'type'      => 'info'
		// );

		// $options[] = array(
		// 	'type' => 'heading-2',
		// 	'end'  => 'end',
		// 	'div'  => 'div'
		// );

		$options[] = array(
			'name'    => esc_html__('Add a new answer form','wpqa'),
			'id'      => 'place_answer_form',
			'options' => array(
				'before' => esc_html__('Before answers','wpqa'),
				'after'  => esc_html__('After answers','wpqa')
			),
			'std'     => 'before',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable or disable the editor for details in the answer','wpqa'),
			'id'   => 'answer_editor',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Allow for the user to answer one time per question','wpqa'),
			'id'   => 'answer_per_question',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'      => esc_html__('Limitation of replies.','wpqa'),
			'desc'      => esc_html__('Choose the style of replies you want to allow for the users.','wpqa'),
			'id'        => 'replies_per_question',
			'options'   => array(
				'no_reply'  => esc_html__('No allowed and will count the one answer for the limitation','wpqa'),
				'one_reply' => esc_html__('One reply and one answer','wpqa'),
				'unlimited' => esc_html__('Unlimited replies','wpqa')
			),
			'std'       => 'no_reply',
			'condition' => 'answer_per_question:not(0)',
			'type'      => 'radio'
		);
		
		$options[] = array(
			'name'    => esc_html__('Answer with question title style at the answers page.','wpqa'),
			'desc'    => esc_html__('Choose the answers with question title style at the answers page.','wpqa'),
			'id'      => 'answer_question_style',
			'options' => array('style_1' => 'Style 1','style_2' => 'Style 2','style_3' => 'Style 3'),
			'std'     => 'style_1',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name' => esc_html__('Add your minimum limit for the number of letters for the answer, like 15, 20, if you leave it empty it will make it not important','wpqa'),
			'id'   => 'answer_min_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Add your limit for the number of letters for the answer, like 140, 200, if you leave it empty it will make it unlimited','wpqa'),
			'id'   => 'answer_limit',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Enable or disable the read more in the answer by jQuery','wpqa'),
			'id'   => 'read_more_answer',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Note: if you need to show the load more or infinite scroll the comments you must activate from Settings/Discussion/Break comments into pages with, and add any number, also choose the displated by default, first.','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name'    => esc_html__('Pagination style','wpqa'),
			'desc'    => esc_html__('Choose pagination style from here.','wpqa'),
			'id'      => 'answer_pagination',
			'options' => array(
				'standard'        => esc_html__('Standard','wpqa'),
				'pagination'      => esc_html__('Pagination','wpqa'),
				'load_more'       => esc_html__('Load more','wpqa'),
				'infinite_scroll' => esc_html__('Infinite scroll','wpqa'),
			),
			'std'     => 'pagination',
			'type'    => 'radio'
		);
		
		$answers_tabs = array(
			"votes"  => array("sort" => esc_html__('Voted','wpqa'),"value" => "votes"),
			"oldest" => array("sort" => esc_html__('Oldest','wpqa'),"value" => "oldest"),
			"recent" => array("sort" => esc_html__('Recent','wpqa'),"value" => "recent"),
			"random" => array("sort" => esc_html__('Random','wpqa'),"value" => ""),
		);

		if (has_himer() || has_knowly() || has_questy()) {
			$answers_tabs["reacted"] = array("sort" => esc_html__('Reacted','wpqa'),"value" => "");
		}
		
		$options[] = array(
			'name'    => esc_html__('Tabs at the answers','wpqa'),
			'desc'    => esc_html__('Select the tabs at the answers on the question page.','wpqa'),
			'id'      => 'answers_tabs',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $answers_tabs,
			'options' => $answers_tabs
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to activate the vote at answers','wpqa'),
			'desc' => esc_html__('Select ON to enable the vote at the answers.','wpqa'),
			'id'   => 'active_vote_answer',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Select ON to hide the dislike at answers','wpqa'),
			'desc'      => esc_html__('If you put it ON the dislike will not show.','wpqa'),
			'id'        => 'show_dislike_answers',
			'condition' => 'active_vote_answer:not(0)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Add answer anonymously','wpqa'),
			'desc' => esc_html__('Select ON to enable the answer anonymously.','wpqa'),
			'id'   => 'answer_anonymously',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Add a private answer','wpqa'),
			'desc' => esc_html__('Select ON to enable the private answer.','wpqa'),
			'id'   => 'private_answer',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Activate the user who added the question to see private answer or not?','wpqa'),
			'desc'      => esc_html__('Select ON if you want active the user who added the question to see private answer.','wpqa'),
			'id'        => 'private_answer_user',
			'condition' => 'private_answer:not(0)',
			'type'      => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Video','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Video in the answer form','wpqa'),
			'desc' => esc_html__('Select ON to enable the video in the answer form.','wpqa'),
			'id'   => 'answer_video',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'answer_video:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Video position at answer','wpqa'),
			'desc'    => esc_html__('Choose the video position.','wpqa'),
			'id'      => 'video_answer_position',
			'options' => array("before" => "Before content","after" => "After content"),
			'std'     => 'after',
			'type'    => 'select'
		);
		
		$options[] = array(
			'name' => esc_html__('Set the video to 100%?','wpqa'),
			'desc' => esc_html__('Select ON if you want to set the video to 100%.','wpqa'),
			'id'   => 'video_answer_100',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			"name"      => esc_html__("Set the width for the video for the answer","wpqa"),
			"id"        => "video_answer_width",
			'condition' => 'video_answer_100:not(on)',
			"type"      => "sliderui",
			'std'       => 260,
			"step"      => "1",
			"min"       => "50",
			"max"       => "600"
		);
		
		$options[] = array(
			"name" => esc_html__("Set the height for the video for the answer","wpqa"),
			"id"   => "video_answer_height",
			"type" => "sliderui",
			'std'  => 500,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);

		$options[] = array(
			'name' => esc_html__('Attachment','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Attachment in the answer form','wpqa'),
			'desc' => esc_html__('Select ON to enable the attachment in the answer form.','wpqa'),
			'id'   => 'attachment_answer',
			'type' => 'checkbox'
		);

		$options[] = array(
			'name' => esc_html__('Featured image','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Featured image in the answer form','wpqa'),
			'desc' => esc_html__('Select ON to enable the featured image in the answer form.','wpqa'),
			'id'   => 'featured_image_answer',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'featured_image_answer:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to show featured image in the question answers','wpqa'),
			'desc' => esc_html__('Select ON to enable the featured image in the question answers.','wpqa'),
			'id'   => 'featured_image_question_answers',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to show featured image in the answers tab, answers template, answers at profile or answers is search','wpqa'),
			'desc' => esc_html__('Select ON to enable the featured image in the answers.','wpqa'),
			'id'   => 'featured_image_in_answers',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Select ON to enable the lightbox for featured image','wpqa'),
			'desc' => esc_html__('Select ON to enable the lightbox for featured image.','wpqa'),
			'id'   => 'featured_image_answers_lightbox',
			'std'  => "on",
			'type' => 'checkbox'
		);
		
		$options[] = array(
			"name" => esc_html__("Set the width for the featured image for the answers","wpqa"),
			"id"   => "featured_image_answer_width",
			"type" => "sliderui",
			'std'  => 260,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			"name" => esc_html__("Set the height for the featured image for the answers","wpqa"),
			"id"   => "featured_image_answer_height",
			"type" => "sliderui",
			'std'  => 185,
			"step" => "1",
			"min"  => "50",
			"max"  => "600"
		);
		
		$options[] = array(
			'name'    => esc_html__('Featured image position','wpqa'),
			'desc'    => esc_html__('Choose the featured image position.','wpqa'),
			'id'      => 'featured_answer_position',
			'options' => array("before" => "Before content","after" => "After content"),
			'std'     => 'before',
			'type'    => 'select'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Search Settings','wpqa'),
			'id'   => 'search_setting',
			'icon' => 'search',
			'type' => 'heading',
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Search slug','wpqa'),
			'desc' => esc_html__('Put the search slug.','wpqa'),
			'id'   => 'search_slug',
			'std'  => 'search',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => '<a href="'.wpqa_get_search_permalink().'" target="_blank">'.esc_html__('The Link For The Search Page.','wpqa').'</a>',
			'type' => 'info'
		);
		
		$search_attrs = array(
			"questions"              => array("sort" => esc_html__('Questions','wpqa'),"value" => "questions"),
			"answers"                => array("sort" => esc_html__('Answers','wpqa'),"value" => "answers"),
			wpqa_question_categories => array("sort" => esc_html__('Question categories','wpqa'),"value" => wpqa_question_categories),
			wpqa_question_tags       => array("sort" => esc_html__('Question tags','wpqa'),"value" => wpqa_question_tags),
			"groups"                 => array("sort" => esc_html__('Groups','wpqa'),"value" => "groups"),
			"posts"                  => array("sort" => esc_html__('Posts','wpqa'),"value" => "posts"),
			"comments"               => array("sort" => esc_html__('Comments','wpqa'),"value" => "comments"),
			"category"               => array("sort" => esc_html__('Post categories','wpqa'),"value" => "category"),
			"post_tag"               => array("sort" => esc_html__('Post tags','wpqa'),"value" => "post_tag"),
			"users"                  => array("sort" => esc_html__('Users','wpqa'),"value" => "users"),
		);

		if ($activate_knowledgebase == true) {
			$search_attrs['knowledgebases'] = array("sort" => esc_html__('Knowledgebases','wpqa'),"value" => "knowledgebases");
			$search_attrs[wpqa_knowledgebase_categories] = array("sort" => esc_html__('Knowledgebase categories','wpqa'),"value" => wpqa_knowledgebase_categories);
			$search_attrs[wpqa_knowledgebase_tags] = array("sort" => esc_html__('Knowledgebase tags','wpqa'),"value" => wpqa_knowledgebase_tags);
		}
		
		$options[] = array(
			'name'    => esc_html__('Select the search options','wpqa'),
			'desc'    => esc_html__('Select the search options on the search page.','wpqa'),
			'id'      => 'search_attrs',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => apply_filters(wpqa_prefix_theme."_search_attrs",$search_attrs),
			'options' => apply_filters(wpqa_prefix_theme."_search_attrs",$search_attrs)
		);

		$default_search = array(
			"questions"              => esc_html__("Questions","wpqa"),
			"answers"                => esc_html__("Answers","wpqa"),
			wpqa_question_categories => esc_html__("Question categories","wpqa"),
			wpqa_question_tags       => esc_html__("Question tags","wpqa"),
			"groups"                 => esc_html__("Groups","wpqa"),
			"posts"                  => esc_html__("Posts","wpqa"),
			"comments"               => esc_html__("Comments","wpqa"),
			"category"               => esc_html__("Post categories","wpqa"),
			"post_tag"               => esc_html__("Post tags","wpqa"),
			"users"                  => esc_html__("Users","wpqa"),
		);

		if ($activate_knowledgebase == true) {
			$default_search = wpqa_array_insert_after($default_search,wpqa_question_tags,array(wpqa_knowledgebase_tags => esc_html__('Knowledgebase tags','wpqa')));
			$default_search = wpqa_array_insert_after($default_search,wpqa_question_tags,array(wpqa_knowledgebase_categories => esc_html__('Knowledgebase categories','wpqa')));
			$default_search = wpqa_array_insert_after($default_search,wpqa_question_tags,array('knowledgebases' => esc_html__('Knowledgebases','wpqa')));
		}
		
		$options[] = array(
			'name'    => esc_html__('Default search','wpqa'),
			'desc'    => esc_html__("Choose what's the default search","wpqa"),
			'id'      => 'default_search',
			'type'    => 'select',
			'stc'     => 'questions',
			'options' => $default_search
		);

		$options[] = array(
			'name' => esc_html__("Choose the live search enable or disable","wpqa"),
			'id'   => "live_search",
			'type' => "checkbox",
			'std'  => "on",
		);

		$options[] = array(
			'name' => esc_html__("Include the asked questions on the search or not","wpqa"),
			'id'   => "asked_questions_search",
			'type' => "checkbox",
		);

		$options[] = array(
			'name'      => esc_html__('Search result number','wpqa'),
			'desc'      => esc_html__('Type the search result number from here.','wpqa'),
			'id'        => 'search_result_number',
			'condition' => 'live_search:not(0)',
			'std'       => '5',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name'    => esc_html__('Tags style at search page','wpqa'),
			'desc'    => esc_html__('Choose the tags style.','wpqa'),
			'id'      => 'tag_style_pages',
			'options' => array(
				'simple_follow' => esc_html__('Simple with follow','wpqa'),
				'advanced'      => esc_html__('Advanced','wpqa'),
				'simple'        => esc_html__('Simple','wpqa'),
			),
			'std'     => 'simple_follow',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name' => esc_html__('Show the user filter at search page.','wpqa'),
			'id'   => 'user_filter',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Show the category search at category pages.','wpqa'),
			'id'   => 'cat_search',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Show the tag search at tag pages.','wpqa'),
			'id'   => 'tag_search',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Show the group search at group pages.','wpqa'),
			'id'   => 'group_search',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$email_settings = array(
			"general_mail"   => esc_html__('General setting','wpqa'),
			"users_mails"    => esc_html__('Users mails','wpqa'),
			"payments_mails" => esc_html__('Payments mails','wpqa'),
			"posts_mails"    => esc_html__('Posts and questions mails','wpqa'),
			"groups_mails"   => esc_html__('Groups mails','wpqa'),
			"comments_mails" => esc_html__('Comments mails','wpqa'),
			"other_mails"    => esc_html__('Other mails','wpqa'),
		);
		
		$options[] = array(
			'name'    => esc_html__('Mail settings','wpqa'),
			'id'      => 'mails_settings',
			'type'    => 'heading',
			'icon'    => 'email',
			'std'     => 'general_mail',
			'options' => $email_settings
		);
		
		$options[] = array(
			'name' => esc_html__('General setting','wpqa'),
			'id'   => 'general_mail',
			'type' => 'heading-2'
		);

		$options[] = array(
			'name'    => esc_html__('Email template style','wpqa'),
			'id'      => 'email_style',
			'std'     => 'style_1',
			'type'    => 'radio',
			'options' => array("style_1" => "Style 1","style_2" => "Style 2")
		);
		
		$options[] = array(
			'name' => esc_html__("Custom logo for mail template","wpqa"),
			'desc' => esc_html__("Upload your custom logo for mail template","wpqa"),
			'id'   => 'logo_email_template',
			'std'  => $imagepath_theme."logo-light.png",
			'type' => 'upload'
		);
		
		$options[] = array(
			'name'      => esc_html__('Background Color for the mail template','wpqa'),
			'id'        => 'background_email',
			'condition' => 'email_style:not(style_2)',
			'type'      => 'color',
			'std'       => '#272930'
		);

		$options[] = array(
			'name' => esc_html__('Custom email custom display name','wpqa'),
			'id'   => 'custom_mail_name',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__("Add your email custom display name","wpqa"),
			'desc'      => esc_html__("Add it professional name, like 2code","wpqa"),
			'id'        => 'mail_name',
			'condition' => 'custom_mail_name:not(0)',
			'std'       => get_bloginfo('name'),
			'type'      => 'text'
		);

		$options[] = array(
			'name' => esc_html__('SMTP mail enable or disable','wpqa'),
			'id'   => 'mail_smtp',
			'type' => 'checkbox'
		);

		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'mail_smtp:not(0)',
			'div'       => 'div'
		);

		$parse = parse_url(get_site_url());
		
		$options[] = array(
			'name' => esc_html__("Add your email for mail template","wpqa"),
			'desc' => esc_html__("Add it professional email, like no_reply@2code.info","wpqa"),
			'id'   => 'email_template',
			'std'  => "no_reply@".$parse['host'],
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('SMTP mail host','wpqa'),
			'id'   => 'mail_host',
			'type' => 'text',
		);

		$options[] = array(
			'name' => esc_html__('SMTP mail port','wpqa'),
			'id'   => 'mail_port',
			'type' => 'text',
		);

		$options[] = array(
			'name' => esc_html__('SMTP mail username','wpqa'),
			'id'   => 'mail_username',
			'type' => 'text',
		);

		$options[] = array(
			'name' => esc_html__('SMTP mail password','wpqa'),
			'id'   => 'mail_password',
			'type' => 'password',
		);

		$options[] = array(
			'name'    => esc_html__('SMTP mail secure','wpqa'),
			'id'      => 'mail_secure',
			'std'     => 'ssl',
			'type'    => 'radio',
			'options' => array("ssl" => "SSL","tls" => "TLS","none" => esc_html__("No Encryption","wpqa"))
		);
		
		$options[] = array(
			'name'  => esc_html__('SMTP Authentication','wpqa'),
			'id'    => 'smtp_auth',
			'std'   => 'on',
			'type'  => 'checkbox'
		);
		
		$options[] = array(
			'name'  => esc_html__('Disable SSL Certificate Verification','wpqa'),
			'id'    => 'disable_ssl',
			'type'  => 'checkbox'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__("Add your email to receive the different mails","wpqa"),
			'id'   => 'email_template_to',
			'std'  => get_bloginfo("admin_email"),
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Mail footer enable or disable','wpqa'),
			'id'   => 'active_footer_email',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_footer_email:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Social for the mail in the footer enable or disable','wpqa'),
			'id'   => 'social_footer_email',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__("Add your copyrights for your mail templates","wpqa"),
			'id'   => 'copyrights_for_email',
			'std'  => '&copy; '.date('Y').' '.wpqa_name_theme.'. All Rights Reserved<br>With Love by <a href="https://2code.info/" target="_blank">2code</a>.',
			'type' => 'textarea'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variables work at the all templates</h4>
			<p>[%blogname%] - The site title.</p>
			<p>[%site_url%] - The site URL.</p>
			<p>[%messages_url%] - The messages URL page.</p>',
		);
		
		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variables work for the custom email template only</h4>
			<p>[%user_login%] - The user login name.</p>
			<p>[%user_name%] - The user name.</p>
			<p>[%user_nicename%] - The user nice name.</p>
			<p>[%display_name%] - The user display name.</p>
			<p>[%user_email%] - The user email.</p>
			<p>[%user_profile%] - The user profile URL.</p>',
		);
		
		$roles_user_only = $new_roles;
		unset($roles_user_only["wpqa_under_review"]);
		unset($roles_user_only["ban_group"]);
		unset($roles_user_only["activation"]);
		
		$options[] = array(
			'name'    => esc_html__('Choose the roles or users for the custom mail','wpqa'),
			'desc'    => esc_html__('Choose from here which roles or users you want to send the custom mail.','wpqa'),
			'id'      => 'mail_groups_users',
			'options' => array(
				'groups' => esc_html__('Roles','wpqa'),
				'users'  => esc_html__('Users','wpqa'),
			),
			'std'     => 'groups',
			'type'    => 'radio'
		);

		$options[] = array(
			'name'      => esc_html__("Choose the roles you need to send the custom mail for them.","wpqa"),
			'id'        => 'custom_mail_groups',
			'condition' => 'mail_groups_users:not(users)',
			'type'      => 'multicheck',
			'options'   => $new_roles,
			'std'       => array('administrator' => 'administrator','editor' => 'editor','contributor' => 'contributor','subscriber' => 'subscriber','author' => 'author'),
		);

		$options[] = array(
			'name'      => esc_html__('Specific user ids','wpqa'),
			'id'        => 'mail_specific_users',
			'condition' => 'mail_groups_users:is(users)',
			'type'      => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Custom image for the custom mail','wpqa'),
			'id'   => 'custom_image_mail',
			'type' => 'upload'
		);

		$options[] = array(
			'name' => esc_html__('Custom mail title','wpqa'),
			'id'   => 'title_custom_mail',
			'std'  => 'Welcome',
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Custom mail template','wpqa'),
			'id'       => 'email_custom_mail',
			'std'      => "<p>Hi [%display_name%]</p><p>Welcome to our site.</p><p>[%blogname%]</p><p><a href='[%site_url%]'>[%site_url%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'name' => esc_html__('You must save your options before send the message.','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => '<a href="#" class="button button-primary send-custom-mail">'.esc_html__('Send the custom mail','wpqa').'</a>',
			'type' => 'info'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Users mails','wpqa'),
			'id'   => 'users_mails',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variables work for all next templates</h4>
			<p>[%user_login%] - The user login name.</p>
			<p>[%user_name%] - The user name.</p>
			<p>[%user_nicename%] - The user nice name.</p>
			<p>[%display_name%] - The user display name.</p>
			<p>[%user_email%] - The user email.</p>
			<p>[%user_profile%] - The user profile URL.</p>
			<p>[%display_name_sender%] - The user display name.</p>
			<p>[%user_profile_sender%] - The user profile URL.</p>',
		);
		
		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variable works at Reset password and Confirm mail</h4>
			<p>[%confirm_link_email%] - Confirm mail for the user to reset the password at reset password template and at the confirm mail template is confirm mail to active the user.</p>',
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'confirm_email:not(0),confirm_edit_email:not(0)',
			'operator'  => 'or',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'confirm_email:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Confirm mail title','wpqa'),
			'id'   => 'title_confirm_link',
			'std'  => "Confirm account",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Confirm mail template','wpqa'),
			'id'       => 'email_confirm_link',
			'std'      => "<p>Hi there</p><p>Your registration has been successful! To confirm your account, kindly click on 'Activate' below.</p><p><a href='[%confirm_link_email%]'>Activate</a></p><p>If the link above does not work, Please use your browser to go to:</p><p>[%confirm_link_email%]</p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'confirm_edit_email:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Confirm edit mail title','wpqa'),
			'id'   => 'title_confirm_edit_email_link',
			'std'  => "Confirm account",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Confirm edit mail template','wpqa'),
			'id'       => 'edit_email_confirm_link',
			'std'      => "<p>Hi there</p><p>You edited your email! To confirm your email, kindly click on 'Activate' below.</p><p><a href='[%confirm_link_email%]'>Activate</a></p><p>If the link above does not work, Please use your browser to go to:</p><p>[%confirm_link_email%]</p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);

		$options[] = array(
			'name' => esc_html__('Edited email title','wpqa'),
			'id'   => 'title_edited_email_link',
			'std'  => "Edited email",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Edited email template','wpqa'),
			'id'       => 'edited_email_link',
			'std'      => "<p>Hi there</p><p>You edited your email successfully!</p><p><a href='[%site_url%]'>[%blogname%]</a></p><p>[%site_url%]</p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Confirm mail 2 title','wpqa'),
			'id'   => 'title_confirm_link_2',
			'std'  => "Confirm account",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Confirm mail 2 template','wpqa'),
			'id'       => 'email_confirm_link_2',
			'std'      => "<p>Hi there</p><p>This is the link to activate your membership</p><p><a href='[%confirm_link_email%]'>Activate</a></p><p>If the link above does not work, Please use your browser to go to:</p><p>[%confirm_link_email%]</p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'send_welcome_mail:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('New welcome mail title','wpqa'),
			'id'   => 'title_welcome_mail',
			'std'  => 'Welcome',
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('New welcome mail template','wpqa'),
			'id'       => 'email_welcome_mail',
			'std'      => "<p>Hi [%display_name%]</p><p>Welcome to our site.</p><p>[%blogname%]</p><p><a href='[%site_url%]'>[%site_url%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'user_review:not(0),send_email_users_review:not(0)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('New user for review title','wpqa'),
			'id'   => 'title_review_user',
			'std'  => "New user for review",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('New user for review template','wpqa'),
			'id'       => 'email_review_user',
			'std'      => "<p>Hi there</p><p>There is a new user for the review named [%user_name%]</p><p><a href='[%users_link%]'>Review him</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'user_review:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Approve user title','wpqa'),
			'id'   => 'title_approve_user',
			'std'  => "Confirm account",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Approve user template','wpqa'),
			'id'       => 'email_approve_user',
			'std'      => "<p>Hi there</p><p>The admin was activated your account.</p><p><a href='[%site_url%]'>[%blogname%]</a></p><p>[%site_url%]</p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Reset password title','wpqa'),
			'id'   => 'title_new_password',
			'std'  => "Reset your password",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Reset password template','wpqa'),
			'id'       => 'email_new_password',
			'std'      => "<p>Someone requested that the password be reset for the following account:</p><p>Username: '[%display_name%]' ([%user_login%]).</p><p>If this was a mistake, just ignore this mail and nothing will happen.</p><p>To reset your password, visit the following address:</p><p><a href='[%confirm_link_email%]'>Click here to reset your password</a></p><p>If the link above does not work, Please use your browser to go to:</p><p>[%confirm_link_email%]</p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variable works at this template only</h4>
			<p>[%reset_password%] - The user password.</p>',
		);
		
		$options[] = array(
			'name' => esc_html__('Reset password 2 title','wpqa'),
			'id'   => 'title_new_password_2',
			'std'  => "Reset your password",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Reset password 2 template','wpqa'),
			'id'       => 'email_new_password_2',
			'std'      => "<p>You are: [%display_name%] ([%user_login%])</p><p>The New Password: [%reset_password%]</p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'report_users:not(0)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Report user title','wpqa'),
			'id'   => 'title_report_user',
			'std'  => "Report User",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Report user template','wpqa'),
			'id'       => 'email_report_user',
			'std'      => "<p>Hi there</p><p>Abuse has been reported on the use of the following user</p><p><a href='[%user_profile_sender%]'>[%display_name_sender%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'edit_profile_items_4:has(notification_mention)',
			'div'       => 'div'
		);

		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variables work at Mention notification</h4>
			<p>[%display_name%] - The user display name.</p>
			<p>[%user_profile%] - The user profile URL.</p>
			<p>[%answer_url%] - Show the question/post/answer/comment link.</p>
			<p>[%post_title%] - Show the post title.</p>',
		);
		
		$options[] = array(
			'name' => esc_html__('Mention notification title','wpqa'),
			'id'   => 'title_notification_mention',
			'std'  => "User mentioned you",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Mention notification template','wpqa'),
			'id'       => 'email_notification_mention',
			'std'      => "<p>Hi there</p><p>We would tell you <a href='[%user_profile%]'>[%display_name%]</a> mentioned you.</p><p><a href='[%answer_url%]'>[%post_title%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Payments mails','wpqa'),
			'id'   => 'payments_mails',
			'type' => 'heading-2'
		);

		$options[] = array(
			'name'      => sprintf(esc_html__('Note: if you need this mail template, From %s Settings/Payment settings/Activate one of the payments functions.','wpqa'),wpqa_name_theme),
			'condition' => 'buy_points_payment:not(on),buy_points_payment:not(on),pay_ask:not(on),pay_to_sticky:not(on)',
			'type'      => 'info'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'operator'  => 'or',
			'condition' => 'buy_points_payment:not(0),buy_points_payment:not(0),pay_ask:not(0),pay_to_sticky:not(0)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variables work at this template only</h4>
			<p>[%item_price%] - Show the item price.</p>
			<p>[%item_name%] - Show the item name.</p>
			<p>[%item_currency%] - Show the item currency.</p>
			<p>[%payer_email%] - Show the payer email.</p>
			<p>[%first_name%] - Show the payer first name.</p>
			<p>[%last_name%] - Show the payer last name.</p>
			<p>[%item_transaction%] - Show the transaction id.</p>
			<p>[%date%] - Show the payment date.</p>
			<p>[%time%] - Show the payment time.</p>',
		);
		
		$options[] = array(
			'name' => esc_html__('New payment title','wpqa'),
			'id'   => 'title_new_payment',
			'std'  => "Instant Payment Notification - Received Payment",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('New payment template','wpqa'),
			'id'       => 'email_new_payment',
			'std'      => "<p>An instant payment notification was successfully received</p><p>With [%item_price%] [%item_currency%]</p><p>From [%payer_email%] [%first_name%] - [%last_name%] on [%date%] at [%time%]</p><p>The item transaction id [%item_transaction%]</p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Posts and questions mails','wpqa'),
			'id'   => 'posts_mails',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'active_message:not(0),send_email_message:not(0)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variables work at Send message template and New questions template to show the details for the received user</h4>
			<p>[%user_login%] - The user login name.</p>
			<p>[%user_name%] - The user name.</p>
			<p>[%user_nicename%] - The user nice name.</p>
			<p>[%display_name%] - The user display name.</p>
			<p>[%user_email%] - The user email.</p>
			<p>[%user_profile%] - The user profile URL.</p>',
		);
		
		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variables work at Send message template and New questions template to show the details for the sender user</h4>
			<p>[%user_login_sender%] - The user login name.</p>
			<p>[%user_name_sender%] - The user name.</p>
			<p>[%user_nicename_sender%] - The user nice name.</p>
			<p>[%display_name_sender%] - The user display name.</p>
			<p>[%user_email_sender%] - The user email.</p>
			<p>[%user_profile_sender%] - The user profile URL.</p>',
		);
		
		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variable works at this template only</h4>
			<p>[%messages_title%] - Show the message title.</p>',
		);
		
		$options[] = array(
			'name' => esc_html__('Send message title','wpqa'),
			'id'   => 'title_new_message',
			'std'  => "New message",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Send message template','wpqa'),
			'id'       => 'email_new_message',
			'std'      => "<p>Hi there</p><p>There is a new message</p><p><a href='[%messages_url%]'>[%messages_title%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type'      => 'heading-2',
			'operator'  => 'or',
			'condition' => 'send_email_new_question:not(0),send_email_draft_questions:not(0),send_email_draft_posts:not(0)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'operator'  => 'or',
			'condition' => 'question_schedules:not(0),way_sending_notifications_questions:is(cronjob)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Recent questions as schedules title','wpqa'),
			'id'   => 'title_question_schedules',
			'std'  => "Recent questions",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Recent questions as schedules template','wpqa'),
			'id'       => 'email_question_schedules',
			'std'      => "<p>Hi there</p><p>There are new questions</p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'operator'  => 'or',
			'condition' => 'post_schedules:not(0),way_sending_notifications_posts:is(cronjob)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Recent posts as schedules title','wpqa'),
			'id'   => 'title_post_schedules',
			'std'  => "Recent posts",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Recent posts as schedules template','wpqa'),
			'id'       => 'email_post_schedules',
			'std'      => "<p>Hi there</p><p>There are new posts</p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'      => esc_html__('Content after schedules mails for the recent posts and recent questions','wpqa'),
			'id'        => 'schedule_content_post',
			'condition' => 'post_schedules:not(0),question_schedules:not(0),way_sending_notifications_posts:is(cronjob),way_sending_notifications_questions:is(cronjob)',
			'operator'  => 'or',
			'std'       => "<p>[%blogname%]</p><p>[%site_url%]</p>",
			'type'      => 'editor',
			'settings'  => $wp_editor_settings
		);

		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variables work for all next templates</h4>
			<p>[%post_title%] - Show the post title.</p>
			<p>[%post_link%] - Show the post link.</p>
			<p>[%the_author_post%] - Show the post author.</p>',
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'send_email_new_post:not(0),send_email_new_post_both:not(0)',
			'operator'  => 'or',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('New post title','wpqa'),
			'id'   => 'title_new_posts',
			'std'  => "New post",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('New post template','wpqa'),
			'id'       => 'email_new_posts',
			'std'      => "<p>Hi there</p><p>There is a new post</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'send_email_new_question:not(0),send_email_new_question_both:not(0)',
			'operator'  => 'or',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('New question title','wpqa'),
			'id'   => 'title_new_questions',
			'std'  => "New question",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('New question template','wpqa'),
			'id'       => 'email_new_questions',
			'std'      => "<p>Hi there</p><p>There is a new question</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'send_email_draft_questions:not(0)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('New question for review title','wpqa'),
			'id'   => 'title_new_draft_questions',
			'std'  => "New question for review",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('New question for review template','wpqa'),
			'id'       => 'email_draft_questions',
			'std'      => "<p>Hi there</p><p>There is a new question for the review</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'send_email_draft_posts:not(0)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('New post for review title','wpqa'),
			'id'   => 'title_new_draft_posts',
			'std'  => "New post for review",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('New post for review template','wpqa'),
			'id'       => 'email_draft_posts',
			'std'      => "<p>Hi there</p><p>There is a new post for the review</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'active_reports:not(0)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Report question title','wpqa'),
			'id'   => 'title_report_question',
			'std'  => "Report Question",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Report question template','wpqa'),
			'id'       => 'email_report_question',
			'std'      => "<p>Hi there</p><p>Abuse has been reported on the use of the following question</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Groups mails','wpqa'),
			'id'   => 'groups_mails',
			'type' => 'heading-2'
		);

		$options[] = array(
			'type'      => 'heading-2',
			'operator'  => 'or',
			'condition' => 'send_email_draft_groups:not(0),send_email_draft_group_posts:not(0)',
			'div'       => 'div'
		);

		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'send_email_draft_groups:not(0)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('New group for review title','wpqa'),
			'id'   => 'title_new_draft_groups',
			'std'  => "New group for review",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('New group for review template','wpqa'),
			'id'       => 'email_draft_groups',
			'std'      => "<p>Hi there</p><p>There is a new group for the review</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'send_email_draft_group_posts:not(0)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('New post on the group for review title','wpqa'),
			'id'   => 'title_new_draft_group_posts',
			'std'  => "New post on the group for review",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('New post on the group for review template','wpqa'),
			'id'       => 'email_draft_group_posts',
			'std'      => "<p>Hi there</p><p>There is a new post on the group for the review</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Comments mails','wpqa'),
			'id'   => 'comments_mails',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'operator'  => 'or',
			'condition' => 'active_reports:not(0),ask_question_items:has(remember_answer),question_follow:not(0),send_email_draft_comments:not(0)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variables work at Send message template and New questions template to show the details for the received user</h4>
			<p>[%user_login%] - The user login name.</p>
			<p>[%user_name%] - The user name.</p>
			<p>[%user_nicename%] - The user nice name.</p>
			<p>[%display_name%] - The user display name.</p>
			<p>[%user_email%] - The user email.</p>
			<p>[%user_profile%] - The user profile URL.</p>',
		);
		
		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variables work at Notified answer template and Follow question template to show the details for the sender user</h4>
			<p>[%user_login_sender%] - The user login name.</p>
			<p>[%user_name_sender%] - The user name.</p>
			<p>[%user_nicename_sender%] - The user nice name.</p>
			<p>[%display_name_sender%] - The user display name.</p>
			<p>[%user_email_sender%] - The user email.</p>
			<p>[%user_profile_sender%] - The user profile URL.</p>',
		);

		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variables work for all next templates</h4>
			<p>[%post_title%] - Show the post title.</p>
			<p>[%post_link%] - Show the post link.</p>
			<p>[%the_author_post%] - Show the post author.</p>',
		);
		
		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variables work at Report answer, Notified answer and Follow question</h4>
			<p>[%answer_url%] - Show the answer link.</p>
			<p>[%the_name%] - Show the answer author.</p>',
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'active_reports:not(0)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Report answer title','wpqa'),
			'id'   => 'title_report_answer',
			'std'  => "Report Answer",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Report answer template','wpqa'),
			'id'       => 'email_report_answer',
			'std'      => "<p>Hi there</p><p>Abuse has been reported on the use of the following comment</p><p><a href='[%answer_url%]'>[%post_title%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'ask_question_items:has(remember_answer)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Notified answer title','wpqa'),
			'id'   => 'title_notified_answer',
			'std'  => "Answer to your question",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Notified answer template','wpqa'),
			'id'       => 'email_notified_answer',
			'std'      => "<p>Hi there</p><p>We would tell you [%the_author_post%] That the new post was added on a common theme by [%the_name%] Entitled [%the_name%] [%post_title%]</p><p>Click on the link below to go to the topic</p><p><a href='[%answer_url%]'>[%post_title%]</a></p><p>There may be more of Posts and we hope the answer to encourage members and get them to help.</p><p>Accept from us Sincerely</p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'     => esc_html__('Notified reply on the answer title','wpqa'),
			'id'       => 'title_notified_reply',
			'std'      => "Reply to your answer",
			'type'     => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Notified reply on the answer template','wpqa'),
			'id'       => 'email_notified_reply',
			'std'      => "<p>Hi there</p><p>There is a new reply to your following answer</p><p><a href='[%answer_link%]'>[%post_title%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'question_follow:not(0)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Follow question title','wpqa'),
			'id'   => 'title_follow_question',
			'std'  => "New answer on your following question",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('Follow question template','wpqa'),
			'id'       => 'email_follow_question',
			'std'      => "<p>Hi there</p><p>There is a new answer to your following question</p><p><a href='[%answer_url%]'>[%post_title%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'send_email_draft_comments:not(0)',
			'div'       => 'div'
		);

		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variable works for this template</h4>
			<p>[%comment_link%] - To review the comment/answer.</p>',
		);
		
		$options[] = array(
			'name' => esc_html__('New comment/answer for review title','wpqa'),
			'id'   => 'title_new_draft_comments',
			'std'  => "New comment for review",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('New comment/answer for review template','wpqa'),
			'id'       => 'email_draft_comments',
			'std'      => "<p>Hi there</p><p>There is a new comment for the review on this post [%post_title%]</p><p><a href='[%comment_link%]'>Review it</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Other mails','wpqa'),
			'id'   => 'other_mails',
			'type' => 'heading-2'
		);

		$options[] = array(
			'name'      => sprintf(esc_html__('Note: if you need this mail template, From %s Settings/Question settings/Questions category settings/Activate the users to request a new category && Send mail when the category needs a review.','wpqa'),wpqa_name_theme),
			'operator'  => 'or',
			'condition' => 'send_email_add_category:not(on),allow_user_to_add_category:not(on)',
			'type'      => 'info'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'send_email_add_category:not(0),allow_user_to_add_category:not(0)',
			'div'       => 'div'
		);

		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variables work at this template only</h4>
			<p>[%category_link%] - Review the categories link.</p>
			<p>[%category_name%] - The category name.</p>',
		);

		$options[] = array(
			'name' => esc_html__('New category for review title','wpqa'),
			'id'   => 'title_add_category',
			'std'  => "New category for review",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('New category for review template','wpqa'),
			'id'       => 'email_add_category',
			'std'      => "<p>Hi there</p><p>There is a new category for the review</p><p><a href='[%category_link%]'>[%category_name%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variables work at this template only</h4>
			<p>[%user_login%] - The user login name.</p>
			<p>[%user_name%] - The user name.</p>
			<p>[%user_nicename%] - The user nice name.</p>
			<p>[%display_name%] - The user display name.</p>
			<p>[%user_email%] - The user email.</p>
			<p>[%user_profile%] - The user profile URL.</p>
			<p>[%invitation_link%] - The invitation URL.</p>',
		);

		$options[] = array(
			'name' => esc_html__('New invitation','wpqa'),
			'id'   => 'title_new_invitation',
			'std'  => "New invitation",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('New invitation template','wpqa'),
			'id'       => 'email_new_invitation',
			'std'      => "<p>Hi there</p><p>There is a new invitation for you from your friend [%display_name%]</p><p><a href='[%invitation_link%]'>Join to [%blogname%] site</a></p><p><a href='[%invitation_link%]'>[%invitation_link%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);

		$options[] = array(
			'type'    => 'content',
			'content' => '<h4>Variables work at this template only</h4>
			<p>[%request_link%] - Review the request link.</p>
			<p>[%request_name%] - The request name.</p>',
		);

		$options[] = array(
			'name' => esc_html__('New request for review title','wpqa'),
			'id'   => 'title_new_request',
			'std'  => "New request for review",
			'type' => 'text'
		);
		
		$options[] = array(
			'name'     => esc_html__('New request for review template','wpqa'),
			'id'       => 'email_new_request',
			'std'      => "<p>Hi there</p><p>There is a new request for the review</p><p><a href='[%request_link%]'>[%request_name%]</a></p>",
			'type'     => 'editor',
			'settings' => $wp_editor_settings
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'    => esc_html__('Social Settings','wpqa'),
			'id'      => 'social',
			'icon'    => 'share',
			'type'    => 'heading',
			'std'     => 'social',
			'options' => array(
				"social"          => esc_html__('Social Setting','wpqa'),
				"add_sort_social" => esc_html__('Add & sort social','wpqa'),
				"social_api"      => esc_html__('Social media API','wpqa')
			)
		);
		
		$options[] = array(
			'name' => esc_html__('Social Setting','wpqa'),
			'id'   => 'social',
			'type' => 'heading-2'
		);
		
		if (has_himer() || has_knowly()) {
			$social = array(
				array('name' => esc_html__('Facebook','wpqa'),"value" => "facebook","icon" => "social-facebook","default" => "yes"),
				array('name' => esc_html__('Twitter','wpqa'),"value" => "twitter","icon" => "social-twitter","default" => "yes"),
				array('name' => esc_html__('TikTok','wpqa'),"value" => "tiktok","icon" => " fab fa-tiktok","default" => "yes"),
				array('name' => esc_html__('Linkedin','wpqa'),"value" => "linkedin","icon" => "social-linkedin","default" => "yes"),
				array('name' => esc_html__('Dribbble','wpqa'),"value" => "dribbble","icon" => "social-dribbble","default" => "yes"),
				array('name' => esc_html__('Youtube','wpqa'),"value" => "youtube","icon" => "social-youtube","default" => "yes"),
				array('name' => esc_html__('Vimeo','wpqa'),"value" => "vimeo","icon" => "social-vimeo","default" => "yes"),
				array('name' => esc_html__('Skype','wpqa'),"value" => "skype","icon" => "social-skype","default" => "yes"),
				array('name' => esc_html__('Soundcloud','wpqa'),"value" => "soundcloud","icon" => " fab fa-soundcloud","default" => "yes"),
				array('name' => esc_html__('Instagram','wpqa'),"value" => "instagram","icon" => "social-instagram","default" => "yes"),
				array('name' => esc_html__('Pinterest','wpqa'),"value" => "pinterest","icon" => "social-pinterest","default" => "yes"),
				array('name' => esc_html__('Rss','wpqa'),"value" => "rss","icon" => "social-rss","default" => "yes")
			);
		}else if (has_discy()) {
			$social = array(
				array('name' => esc_html__('Facebook','wpqa'),"value" => "facebook","icon" => "facebook","default" => "yes"),
				array('name' => esc_html__('Twitter','wpqa'),"value" => "twitter","icon" => "twitter","default" => "yes"),
				array('name' => esc_html__('TikTok','wpqa'),"value" => "tiktok","icon" => " fab fa-tiktok","default" => "yes"),
				array('name' => esc_html__('Linkedin','wpqa'),"value" => "linkedin","icon" => "linkedin","default" => "yes"),
				array('name' => esc_html__('Dribbble','wpqa'),"value" => "dribbble","icon" => "dribbble","default" => "yes"),
				array('name' => esc_html__('Youtube','wpqa'),"value" => "youtube","icon" => "play","default" => "yes"),
				array('name' => esc_html__('Vimeo','wpqa'),"value" => "vimeo","icon" => "vimeo","default" => "yes"),
				array('name' => esc_html__('Skype','wpqa'),"value" => "skype","icon" => "skype","default" => "yes"),
				array('name' => esc_html__('Soundcloud','wpqa'),"value" => "soundcloud","icon" => "soundcloud","default" => "yes"),
				array('name' => esc_html__('Instagram','wpqa'),"value" => "instagram","icon" => "instagram","default" => "yes"),
				array('name' => esc_html__('Pinterest','wpqa'),"value" => "pinterest","icon" => "pinterest","default" => "yes"),
				array('name' => esc_html__('Rss','wpqa'),"value" => "rss","icon" => "rss","default" => "yes")
			);
		}
		
		if (isset($social) && is_array($social) && !empty($social)) {
			foreach ($social as $key => $value) {
				if ($value["value"] != "rss") {
					$options[] = array(
						'name' => sprintf(esc_html__('%s URL','wpqa'),esc_html($value["name"])),
						'desc' => sprintf('Type the %s URL from here.',esc_html($value["name"])),
						'id'   => $value["value"].'_icon_h',
						'std'  => '#',
						'type' => 'text'
					);
				}else {
					$options[] = array(
						'name' => esc_html__('Rss enable or disable','wpqa'),
						'id'   => 'rss_icon_h',
						'std'  => 'on',
						'type' => 'checkbox'
					);
					
					$options[] = array(
						'name'      => esc_html__('RSS URL if you want change the default URL','wpqa'),
						'desc'      => esc_html__('Type the RSS URL if you want change the default URL or leave it empty to enable the default URL.','wpqa'),
						'id'        => 'rss_icon_h_other',
						'condition' => 'rss_icon_h:not(0)',
						'type'      => 'text'
					);
				}
			}
		}
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Add a new social item','wpqa'),
			'id'   => 'add_sort_social',
			'type' => 'heading-2'
		);
		
		$elements = array(
			array(
				"type" => "text",
				"id"   => "name",
				"name" => esc_html__('Name','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "url",
				"name" => esc_html__('URL','wpqa')
			),
			array(
				"type" => "text",
				"id"   => "icon",
				"name" => wpqa_icons_text_url()
			)
		);
		
		$options[] = array(
			'id'      => "add_social",
			'type'    => "elements",
			'button'  => esc_html__('Add Custom Social','wpqa'),
			'options' => $elements,
			'title'   => "name",
			'addto'   => "sort_social"
		);
		
		if (isset($social) && is_array($social) && !empty($social)) {
			$options[] = array(
				'id'      => "sort_social",
				'std'     => $social,
				'type'    => "sort",
				'options' => $social,
				'delete'  => "yes",
				'getthe'  => $elements
			);
		}
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Social media API','wpqa'),
			'id'   => 'social_api',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Facebook app id.','wpqa'),
			'id'   => 'facebook_app_id',
			'type' => 'text'
		);
		
		// $options[] = array(
		// 	'name' => esc_html__('Soundcloud client id.','wpqa'),
		// 	'desc' => esc_html__('Type here the Soundcloud client id.','wpqa'),
		// 	'id'   => 'soundcloud_client_id',
		// 	'type' => 'text'
		// );
		
		$options[] = array(
			'name' => esc_html__('Behance access token.','wpqa'),
			'desc' => esc_html__('Type here the Behance access token.','wpqa'),
			'id'   => 'behance_api_key',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Google API.','wpqa'),
			'desc' => esc_html__('Type here the Google API.','wpqa'),
			'id'   => 'google_api',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Instagram session.','wpqa'),
			'desc' => esc_html__('Type here the Instagram session.','wpqa'),
			'id'   => 'instagram_sessionid',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => sprintf(esc_html__('Dribbble app data (Make app from: https://dribbble.com/account/applications/new), At Callback URL add %1$s this link %2$s','wpqa'),'<a href="'.admin_url('admin.php?page=options&api=dribbble').'">','</a>'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Dribbble Client ID.','wpqa'),
			'id'   => 'dribbble_client_id',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Dribbble Client Secret.','wpqa'),
			'id'   => 'dribbble_client_secret',
			'type' => 'text'
		);
		
		$dribbble_client_id = wpqa_options('dribbble_client_id');
		$options[] = array(
			'name' => '<a href="https://dribbble.com/oauth/authorize?client_id='.$dribbble_client_id.'" target="_blank">'.esc_html__('Get the access token from here.','wpqa').'</a>',
			'type' => 'info'
		);
		
		$options[] = array(
			'id'   => 'dribbble_access_token',
			'type' => 'hidden'
		);
		
		$options[] = array(
			'name' => esc_html__('Twitter app data.','wpqa'),
			'type' => 'info'
		);
		
		$options[] = array(
			'name' => esc_html__('Twitter consumer key','wpqa'),
			'id'   => 'twitter_consumer_key',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Twitter consumer secret','wpqa'),
			'id'   => 'twitter_consumer_secret',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Envato token.','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Envato token','wpqa'),
			'id'   => 'envato_token',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Advertising','wpqa'),
			'id'   => 'advertising',
			'icon' => 'admin-post',
			'type' => 'heading'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Advertising at 404 pages enable or disable','wpqa'),
			'id'   => 'adv_404',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Do you need to show the advertising on custom pages?','wpqa'),
			'id'   => 'custom_ads_show',
			'type' => 'checkbox'
		);

		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'custom_ads_show:not(0)',
			'div'       => 'div'
		);

		$adv_work_on_options = array(
			'home'          => esc_html__('Home page','wpqa'),
			'pages'         => esc_html__('Pages','wpqa'),
			'posts'         => esc_html__('Posts','wpqa'),
			'questions'     => esc_html__('Questions','wpqa'),
			'groups'        => esc_html__('Groups','wpqa'),
			'post_type'     => esc_html__('All other custom post types','wpqa'),
			'q_categories'  => esc_html__('Question Categories','wpqa'),
			'q_tags'        => esc_html__('Question Tags','wpqa'),
			'categories'    => esc_html__('Post Categories','wpqa'),
			'tags'          => esc_html__('Post Tags','wpqa'),
			'main_profile'  => esc_html__('Main profile page','wpqa'),
			'other_profile' => esc_html__('Other profile pages','wpqa'),
		);

		if ($activate_knowledgebase == true) {
			$adv_work_on_options = wpqa_array_insert_after($adv_work_on_options,'tags',array('k_tags' => esc_html__('Knowledgebase tags','wpqa')));
			$adv_work_on_options = wpqa_array_insert_after($adv_work_on_options,'tags',array('k_categories' => esc_html__('Knowledgebase categories','wpqa')));
			$adv_work_on_options = wpqa_array_insert_after($adv_work_on_options,'tags',array('knowledgebases' => esc_html__('Knowledgebases','wpqa')));
		}

		$adv_work_on_std = array('home' => 'home','posts' => 'posts','questions' => 'questions','pages' => 'pages','groups' => 'groups','post_type' => 'post_type','q_categories' => 'q_categories','q_tags' => 'q_tags','categories' => 'categories','tags' => 'tags','main_profile' => 'main_profile');
		
		if ($activate_knowledgebase == true) {
			$adv_work_on_std["knowledgebases"] = "knowledgebases";
			$adv_work_on_std["k_categories"] = "k_categories";
			$adv_work_on_std["k_tags"] = "k_tags";
		}
		
		$options[] = array(
			'name'    => esc_html__('Advertising work on?','wpqa'),
			'id'      => 'adv_work_on',
			'std'     => 'custom_image',
			'type'    => 'multicheck',
			'options' => $adv_work_on_options,
			'std'     => $adv_work_on_std,
		);

		$options[] = array(
			'name' => esc_html__('Put here the exclude pages, questions, posts, and groups ids','wpqa'),
			'id'   => 'ads_exclude_pages',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Put here the exclude question categories, post categories, question tag, and post tags ids','wpqa'),
			'id'   => 'ads_exclude_taxes',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Put here the exclude user ids','wpqa'),
			'id'   => 'ads_exclude_users',
			'type' => 'text'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Advertising after header','wpqa'),
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Advertising type','wpqa'),
			'id'      => 'header_adv_type_1',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Image URL','wpqa'),
			'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
			'id'        => 'header_adv_img_1',
			'condition' => 'header_adv_type_1:is(custom_image)',
			'type'      => 'upload'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising URL','wpqa'),
			'id'        => 'header_adv_href_1',
			'std'       => '#',
			'condition' => 'header_adv_type_1:is(custom_image)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'header_adv_link_1',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'header_adv_type_1:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising Code html (Ex: Google ads)','wpqa'),
			'id'        => 'header_adv_code_1',
			'condition' => 'header_adv_type_1:not(custom_image)',
			'type'      => 'textarea'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Advertising in post or question','wpqa'),
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Advertising type','wpqa'),
			'id'      => 'share_adv_type',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Image URL','wpqa'),
			'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
			'id'        => 'share_adv_img',
			'condition' => 'share_adv_type:is(custom_image)',
			'type'      => 'upload'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising URL','wpqa'),
			'id'        => 'share_adv_href',
			'std'       => '#',
			'condition' => 'share_adv_type:is(custom_image)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'share_adv_link',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'share_adv_type:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising Code html (Ex: Google ads)','wpqa'),
			'id'        => 'share_adv_code',
			'condition' => 'share_adv_type:not(custom_image)',
			'type'      => 'textarea'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Advertising after left menu','wpqa'),
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Advertising type','wpqa'),
			'id'      => 'left_menu_adv_type',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Image URL','wpqa'),
			'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
			'id'        => 'left_menu_adv_img',
			'condition' => 'left_menu_adv_type:is(custom_image)',
			'type'      => 'upload'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising URL','wpqa'),
			'id'        => 'left_menu_adv_href',
			'std'       => '#',
			'condition' => 'left_menu_adv_type:is(custom_image)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'left_menu_adv_link',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'left_menu_adv_type:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising Code html (Ex: Google ads)','wpqa'),
			'id'        => 'left_menu_adv_code',
			'condition' => 'left_menu_adv_type:not(custom_image)',
			'type'      => 'textarea'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Advertising after content','wpqa'),
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Advertising type','wpqa'),
			'id'      => 'content_adv_type',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Image URL','wpqa'),
			'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
			'id'        => 'content_adv_img',
			'condition' => 'content_adv_type:is(custom_image)',
			'type'      => 'upload'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising URL','wpqa'),
			'id'        => 'content_adv_href',
			'std'       => '#',
			'condition' => 'content_adv_type:is(custom_image)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'content_adv_link',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'content_adv_type:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising Code html (Ex: Google ads)','wpqa'),
			'id'        => 'content_adv_code',
			'condition' => 'content_adv_type:not(custom_image)',
			'type'      => 'textarea'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Between questions or posts','wpqa'),
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Display after x posts or questions','wpqa'),
			'id'   => 'between_questions_position',
			'std'  => '2',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Repeat adv?','wpqa'),
			'desc' => esc_html__('Select ON to enable repeat advertising.','wpqa'),
			'id'   => 'between_adv_type_repeat',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Advertising type','wpqa'),
			'id'      => 'between_adv_type',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Image URL','wpqa'),
			'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
			'id'        => 'between_adv_img',
			'condition' => 'between_adv_type:is(custom_image)',
			'type'      => 'upload'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising URL','wpqa'),
			'id'        => 'between_adv_href',
			'std'       => '#',
			'condition' => 'between_adv_type:is(custom_image)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'between_adv_link',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'between_adv_type:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising Code html (Ex: Google ads)','wpqa'),
			'id'        => 'between_adv_code',
			'condition' => 'between_adv_type:not(custom_image)',
			'type'      => 'textarea'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Between comments or answers','wpqa'),
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Display after x comments or answers','wpqa'),
			'id'   => 'between_comments_position',
			'std'  => '2',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Repeat adv?','wpqa'),
			'desc' => esc_html__('Select ON to enable repeat advertising.','wpqa'),
			'id'   => 'between_comments_adv_type_repeat',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Count the replies too? or the main comment or answer only?','wpqa'),
			'desc' => esc_html__('Select ON to enable count the replies too.','wpqa'),
			'id'   => 'between_comments_adv_type_replies',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Advertising type','wpqa'),
			'id'      => 'between_comments_adv_type',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Image URL','wpqa'),
			'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
			'id'        => 'between_comments_adv_img',
			'condition' => 'between_comments_adv_type:is(custom_image)',
			'type'      => 'upload'
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising URL','wpqa'),
			'id'        => 'between_comments_adv_href',
			'std'       => '#',
			'condition' => 'between_comments_adv_type:is(custom_image)',
			'type'      => 'text'
		);

		$options[] = array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'between_comments_adv_link',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'between_comments_adv_type:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Advertising Code html (Ex: Google ads)','wpqa'),
			'id'        => 'between_comments_adv_code',
			'condition' => 'between_comments_adv_type:not(custom_image)',
			'type'      => 'textarea'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options = apply_filters('wpqa_options_after_ads',$options);
		
		$options[] = array(
			'name'    => esc_html__('Footer settings','wpqa'),
			'id'      => 'footer',
			'icon'    => 'tagcloud',
			'type'    => 'heading',
			'std'     => 'footer_general',
			'options' => array(
				"footer_general"  => esc_html__('General setting','wpqa'),
				"footer_main"     => esc_html__('Main Footer setting','wpqa'),
				"footer_bottom"   => esc_html__('Bottom footer setting','wpqa'),
				"footer_sort"     => esc_html__('Sort footer elements','wpqa')
			)
		);
		
		$options[] = array(
			'name' => esc_html__('General setting','wpqa'),
			'id'   => 'footer_general',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Footer style','wpqa'),
			'desc'    => esc_html__('Choose the footer style.','wpqa'),
			'id'      => 'footer_style',
			'std'     => 'footer',
			'type'    => 'radio',
			'options' => array("footer" => esc_html__("Normal footer","wpqa"),"sidebar" => esc_html__("After sidebar","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Footer skin','wpqa'),
			'desc'      => esc_html__('Choose the footer skin.','wpqa'),
			'id'        => 'footer_skin',
			'std'       => 'dark',
			'type'      => 'radio',
			'condition' => 'footer_style:not(sidebar)',
			'options'   => array("dark" => esc_html__("Dark","wpqa"),"light" => esc_html__("Light","wpqa"))
		);
		
		$options[] = array(
			'name'      => esc_html__('Footer menu enable or disable','wpqa'),
			'id'        => 'active_footer_menu',
			'std'       => 'on',
			'condition' => 'footer_style:not(footer)',
			'type'      => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__("Choose from here what's menu will show after sidebar.","wpqa"),
			'id'        => 'footer_menu',
			'type'      => 'select',
			'condition' => 'footer_style:not(footer),active_footer_menu:not(0)',
			'options'   => $menus
		);
		
		$options[] = array(
			'name'    => esc_html__("Select what to hide in mobile for the footer","wpqa"),
			'id'      => 'hide_footer_mobile',
			'type'    => 'multicheck',
			'options' => array(
				"top_footer"    => esc_html__('Top Footer','wpqa'),
				"bottom_footer"    => esc_html__('Bottom Footer','wpqa')
			)
		);
		
		$options[] = array(
			'name'      => esc_html__('Copyrights','wpqa'),
			'desc'      => esc_html__('Put the copyrights of footer.','wpqa'),
			'id'        => 'footer_copyrights',
			'std'       => '&copy; '.date('Y').' '.wpqa_name_theme.'. All Rights Reserved<br>With Love by <a href="https://2code.info/" target="_blank">2code</a>.',
			'operator'  => 'or',
			'condition' => 'footer_style:not(footer),bottom_footer:not(0)',
			'type'      => 'textarea'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Main Footer setting','wpqa'),
			'id'   => 'footer_main',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name'      => esc_html__('The main footer work when you choose the footer style as normal footer.','wpqa'),
			'condition' => 'footer_style:not(footer)',
			'type'      => 'info'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'footer_style:not(sidebar)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Top footer enable or disable','wpqa'),
			'id'   => 'top_footer',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'top_footer:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Footer widget icons enable or disable','wpqa'),
			'id'   => 'footer_widget_icons',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Top footer padding top','wpqa'),
			"id"   => "top_footer_padding_top",
			"type" => "sliderui",
			"std"  => "0",
			"step" => "1",
			"min"  => "0",
			"max"  => "100"
		);
		
		$options[] = array(
			'name' => esc_html__('Top footer padding bottom','wpqa'),
			"id"   => "top_footer_padding_bottom",
			"type" => "sliderui",
			"std"  => "0",
			"step" => "1",
			"min"  => "0",
			"max"  => "100"
		);
		
		$options[] = array(
			'name'    => esc_html__('Footer Layout','wpqa'),
			'desc'    => esc_html__('Footer columns Layout.','wpqa'),
			'id'      => "footer_layout",
			'std'     => "footer_5c",
			'type'    => "images",
			'options' => array(
				'footer_1c' => $imagepath.'footer_1c.jpg',
				'footer_2c' => $imagepath.'footer_2c.jpg',
				'footer_3c' => $imagepath.'footer_3c.jpg',
				'footer_4c' => $imagepath.'footer_4c.jpg',
				'footer_5c' => $imagepath.'footer_5c.jpg')
		);
		
		$footer_elements = array(
			array(
				"type" => "color",
				"id"   => "background_color",
				"name" => esc_html__('Background color','wpqa')
			),
			array(
				"type"  => "slider",
				"id"    => "padding_top",
				"name"  => esc_html__('Padding top','wpqa'),
				"std"   => "0",
				"step"  => "1",
				"min"   => "0",
				"max"   => "100",
				"value" => "0"
			),
			array(
				"type"  => "slider",
				"id"    => "padding_bottom",
				"name"  => esc_html__('Padding bottom','wpqa'),
				"std"   => "0",
				"step"  => "1",
				"min"   => "0",
				"max"   => "100",
				"value" => "0"
			),
			array(
				"type"    => "images",
				"id"      => "layout",
				"name"    => esc_html__('Layout','wpqa'),
				'std'     => "footer_5c",
				'options' => array(
					'footer_1c' => $imagepath.'footer_1c.jpg',
					'footer_2c' => $imagepath.'footer_2c.jpg',
					'footer_3c' => $imagepath.'footer_3c.jpg',
					'footer_4c' => $imagepath.'footer_4c.jpg',
					'footer_5c' => $imagepath.'footer_5c.jpg')
			),
			array(
				"type"      => "select",
				"id"        => "first_column",
				"name"      => esc_html__('Select first column','wpqa'),
				'condition' => '[%id%]layout:is(footer_1c),[%id%]layout:is(footer_2c),[%id%]layout:is(footer_3c),[%id%]layout:is(footer_4c),[%id%]layout:is(footer_5c)',
				'operator'  => 'or',
				'options'   => $new_sidebars
			),
			array(
				"type"      => "select",
				"id"        => "second_column",
				"name"      => esc_html__('Select second column','wpqa'),
				'condition' => '[%id%]layout:is(footer_2c),[%id%]layout:is(footer_3c),[%id%]layout:is(footer_4c),[%id%]layout:is(footer_5c)',
				'operator'  => 'or',
				'options'   => $new_sidebars
			),
			array(
				"type"      => "select",
				"id"        => "third_column",
				"name"      => esc_html__('Select third column','wpqa'),
				'condition' => '[%id%]layout:is(footer_3c),[%id%]layout:is(footer_4c),[%id%]layout:is(footer_5c)',
				'operator'  => 'or',
				'options'   => $new_sidebars
			),
			array(
				"type"      => "select",
				"id"        => "fourth_column",
				"name"      => esc_html__('Select fourth column','wpqa'),
				'condition' => '[%id%]layout:is(footer_4c),[%id%]layout:is(footer_5c)',
				'operator'  => 'or',
				'options'   => $new_sidebars
			),
			array(
				"type"      => "select",
				"id"        => "fifth_column",
				"name"      => esc_html__('Select fifth column','wpqa'),
				'condition' => '[%id%]layout:is(footer_5c)',
				'operator'  => 'or',
				'options'   => $new_sidebars
			),
		);
		
		$options[] = array(
			'id'      => "add_footer",
			'type'    => "elements",
			'button'  => esc_html__('Add a new footer level','wpqa'),
			'hide'    => "yes",
			'options' => $footer_elements,
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Bottom footer setting','wpqa'),
			'id'   => 'footer_bottom',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name'      => esc_html__('The bottom footer work when you choose the footer style as normal footer.','wpqa'),
			'condition' => 'footer_style:not(footer)',
			'type'      => 'info'
		);
		
		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'footer_style:not(sidebar)',
			'div'       => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Bottom footer enable or disable','wpqa'),
			'id'   => 'bottom_footer',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'bottom_footer:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Bottom footer padding top','wpqa'),
			"id"   => "footer_padding_top",
			"type" => "sliderui",
			"std"  => "0",
			"step" => "1",
			"min"  => "0",
			"max"  => "100"
		);
		
		$options[] = array(
			'name' => esc_html__('Bottom footer padding bottom','wpqa'),
			"id"   => "footer_padding_bottom",
			"type" => "sliderui",
			"std"  => "0",
			"step" => "1",
			"min"  => "0",
			"max"  => "100"
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Sort the footer elements','wpqa'),
			'id'   => 'footer_sort',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name'      => esc_html__('The sort footer elements work when you choose the footer style as normal footer.','wpqa'),
			'condition' => 'footer_style:not(footer)',
			'type'      => 'info'
		);

		$sort_footer_elements = array(
			array("value" => "top_footer",'name' => esc_html__('Top footer','wpqa'),"default" => "yes"),
			array("value" => "bottom_footer",'name' => esc_html__('Bottom footer','wpqa'),"default" => "yes")
		);
		
		if (isset($sort_footer_elements) && is_array($sort_footer_elements) && !empty($sort_footer_elements)) {
			$options[] = array(
				'id'        => "sort_footer_elements",
				'condition' => 'footer_style:not(sidebar)',
				'std'       => $sort_footer_elements,
				'type'      => "sort",
				'options'   => array(
								array("value" => "top_footer",'name' => esc_html__('Top footer','wpqa'),"default" => "yes"),
								array("value" => "bottom_footer",'name' => esc_html__('Bottom footer','wpqa'),"default" => "yes")
							)
			);
		}
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Advanced settings','wpqa'),
			'id'   => "advanced_setting",
			'icon' => 'upload',
			'type' => 'heading',
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'id'   => 'uniqid_cookie',
			'std'  => wpqa_token(15),
			'type' => 'hidden'
		);

		$options = apply_filters('wpqa_options_advanced_setting',$options,$options_pages,$new_roles,$imagepath,$imagepath_theme,$new_sidebars,$menus);
		
		$options[] = array(
			'name' => esc_html__('Do you need to activate the views at your site?','wpqa'),
			'id'   => 'active_post_stats',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'active_post_stats:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Post meta stats field.','wpqa'),
			'desc' => esc_html__('Change this if you have used a post views plugin before.','wpqa'),
			'id'   => 'post_meta_stats',
			'std'  => 'post_stats',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Do you need to activate cache for views at your site?','wpqa'),
			'id'   => 'cache_post_stats',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Activate the visits at the site work by cookie','wpqa'),
			'desc' => esc_html__('Select ON if you want to active the cookie for the visits at the site.','wpqa'),
			'id'   => 'visit_cookie',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('User meta avatar field.','wpqa'),
			'desc' => esc_html__('Change this if you have used a user avatar or social plugin before.','wpqa'),
			'id'   => 'user_meta_avatar',
			'std'  => 'your_avatar',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('User meta cover field.','wpqa'),
			'desc' => esc_html__('Change this if you have used a user cover or social plugin before.','wpqa'),
			'id'   => 'user_meta_cover',
			'std'  => 'your_cover',
			'type' => 'text'
		);

		$html_content = '<a class="button fix-site-counts" href="'.admin_url("admin.php?page=options&rows=fixed").'">'.esc_html__("Fix all the site counts","wpqa").'</a>';
		$options[] = array(
			'name' => $html_content,
			'type' => 'info'
		);

		$html_content = '<a class="button recreate-menus" href="'.admin_url("admin.php?page=options&create=menus").'">'.esc_html__("Recreate the two menus of the Header Profile Menu and Profile Page Tabs","wpqa").'</a>';
		$options[] = array(
			'name' => $html_content,
			'type' => 'info'
		);

		$options[] = array(
			'name' => esc_html__('Activate the deletions of the old spam, pending, and trash?','wpqa'),
			'desc' => esc_html__('Activate the deletions of the old spam, pending, and trash of questions, posts, groups, group posts, messages, reports, points, notifications, activities, comments, and answers enable or disable.','wpqa'),
			'id'   => 'deletions_spam_trash',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'deletions_spam_trash:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose the type of the old spam, pending, and trash','wpqa'),
			'desc'    => esc_html__('Choose from here which time you want to delete the old spam, pending, and trash of questions, posts, groups, group posts, messages, reports, points, notifications, activities, comments, and answers.','wpqa'),
			'id'      => 'kind_of_deletions_spam_trash',
			'options' => array(
				'spam'    => esc_html__('Spam','wpqa'),
				'pending' => esc_html__('Pending','wpqa'),
				'trash'   => esc_html__('Trash','wpqa'),
			),
			'std'     => array('spam' => 'spam','pending' => 'pending','trash' => 'trash'),
			'type'    => 'multicheck'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose the time of the old spam, pending, and trash','wpqa'),
			'desc'    => esc_html__('Choose from here which time you want to delete the old spam, pending, and trash of questions, posts, groups, group posts, messages, reports, points, notifications, activities, comments, and answers.','wpqa'),
			'id'      => 'time_deletions_spam_trash',
			'options' => array(
				'month'   => esc_html__('Month','wpqa'),
				'2months' => esc_html__('2 Months','wpqa'),
				'year'    => esc_html__('Year','wpqa'),
			),
			'std'     => '2months',
			'type'    => 'radio'
		);

		$type_deletions = wpqa_custom_post_types();
		if (is_array($type_deletions) && !empty($type_deletions)) {
			$type_deletions["comment"] = esc_html__('Comments','wpqa');
			$type_deletions["answer"] = esc_html__('Answers','wpqa');

			$type_deletions_std = array(wpqa_questions_type => wpqa_questions_type,wpqa_asked_questions_type => wpqa_asked_questions_type,'post' => 'post','group' => 'group','posts' => 'posts','message' => 'message','notification' => 'notification','activity' => 'activity','report' => 'report','point' => 'point','comment' => 'comment','answer' => 'answer');

			if ($activate_knowledgebase == true) {
				$type_deletions_std[wpqa_knowledgebase_type] = wpqa_knowledgebase_type;
			}
		
			$options[] = array(
				'name'    => esc_html__('Choose which type you want to delete','wpqa'),
				'desc'    => esc_html__('Choose from here which type you want to delete old spam, pending, and trash of questions, posts, groups, group posts, messages, reports, points, notifications, activities, comments, or answers.','wpqa'),
				'id'      => 'type_deletions_spam_trash',
				'options' => $type_deletions,
				'std'     => $type_deletions_std,
				'type'    => 'multicheck'
			);
		}
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Activate the deletions of the old under review and activation users?','wpqa'),
			'desc' => esc_html__('Activate the deletions of the old under review and activation users enable or disable.','wpqa'),
			'id'   => 'deletions_users',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'deletions_users:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose the type of the old under review and activation users','wpqa'),
			'desc'    => esc_html__('Choose from here which time you want to delete the old under review and activation users.','wpqa'),
			'id'      => 'type_deletions_users',
			'options' => array(
				'wpqa_under_review' => esc_html__('Under Review','wpqa'),
				'activation'        => esc_html__('Activation','wpqa'),
			),
			'std'     => array('wpqa_under_review' => 'wpqa_under_review','activation' => 'activation'),
			'type'    => 'multicheck'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose the time of the old under review and activation users','wpqa'),
			'desc'    => esc_html__('Choose from here which time you want to old under review and activation users.','wpqa'),
			'id'      => 'time_deletions_users',
			'options' => array(
				'month'   => esc_html__('Month','wpqa'),
				'2months' => esc_html__('2 Months','wpqa'),
				'year'    => esc_html__('Year','wpqa'),
			),
			'std'     => '2months',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('If you want to export setting please refresh the page before that','wpqa'),
			'type' => 'info'
		);

		$options[] = array(
			'name' => '<a href="'.add_query_arg(array('page' => 'options','backup' => 'settings'),admin_url('admin.php')).'" class="button button-primary backup-settings">'.esc_html__('Backup your settings','wpqa').'</a>',
			'type' => 'info'
		);

		$options[] = array(
			'name'   => esc_html__('Export Setting','wpqa'),
			'desc'   => esc_html__('Copy this to saved file','wpqa'),
			'id'     => 'export_setting',
			'export' => wpqa_export_options(),
			'type'   => 'export'
		);

		$options[] = array(
			'name' => esc_html__('Import Setting','wpqa'),
			'desc' => esc_html__('Put here the import setting','wpqa'),
			'id'   => 'import_setting',
			'type' => 'import'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Styling','wpqa'),
			'icon' => 'art',
			'type' => 'heading',
			'link' => admin_url('admin.php?page=styling'),
		);

		$options = apply_filters('wpqa_after_options_setting',$options,$options_pages,$new_roles,$imagepath,$imagepath_theme,$new_sidebars,$menus);

		return $options;
	}
}?>