<?php

/* @author    2codeThemes
*  @package   WPQA/framework
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Mobile options */
function wpqa_other_plugin() {
	if (is_admin() && !function_exists('mobile_api_options')) {
		add_filter("wpqa_options_after_general_setting","wpqa_mobile_setting_options");
	}
}
add_action('wpqa_init','wpqa_other_plugin');
function wpqa_mobile_setting_options($options) {
	$directory_uri = get_template_directory_uri();
	$imagepath_theme =  $directory_uri.'/images/';

	$more_info = '<a href="https://2code.info/mobile-apps/" target="_blank">'.esc_html__('For more information and buying the mobile APP','wpqa').'</a>';

	// Pull all the pages into an array
	$not_template_pages = array();
	$args = array('post_type' => 'page','nopaging' => true,"meta_query" => array('relation' => 'OR',array("key" => "_wp_page_template","compare" => "NOT EXISTS"),array("key" => "_wp_page_template","compare" => "=","value" => ''),array("key" => "_wp_page_template","compare" => "=","value" => 'default')));
	$not_template_pages[''] = 'Select a page:';
	$the_query = new WP_Query($args);
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$page_post = $the_query->post;
			$not_template_pages[$page_post->ID] = $page_post->post_title;
		}
	}
	wp_reset_postdata();

	// Pull all the pages into an array
	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}

	// Pull all the roles into an array
	global $wp_roles;
	$new_roles = array();
	foreach ($wp_roles->roles as $key => $value) {
		$new_roles[$key] = $value['name'];
	}

	$array_std = array(
		"category"        => "category",
		"date"            => "date",
		"author_image"    => "author_image",
		"author"          => "author",
		"question_vote"   => "question_vote",
		"tags"            => "tags",
		"answer_button"   => "answer_button",
		"answers_count"   => "answers_count",
		"views_count"     => "views_count",
		"followers_count" => "followers_count",
		"favourite"       => "favourite",
	);

	$array_options = array(
		"category"        => esc_html__('Category','wpqa'),
		"date"            => esc_html__('Date','wpqa'),
		"author_image"    => esc_html__('Author Image','wpqa'),
		"author"          => esc_html__('Author','wpqa'),
		"question_vote"   => esc_html__('Question vote','wpqa'),
		"poll"            => esc_html__('Poll','wpqa'),
		"tags"            => esc_html__('Tags','wpqa'),
		"answer_button"   => esc_html__('Answer button','wpqa'),
		"answers_count"   => esc_html__('Answers count','wpqa'),
		"views_count"     => esc_html__('Views count','wpqa'),
		"followers_count" => esc_html__('Followers count','wpqa'),
		"favourite"       => esc_html__('Favourite','wpqa'),
	);

	$array_single_std = array(
		"category"        => "category",
		"date"            => "date",
		"author_image"    => "author_image",
		"author"          => "author",
		"question_vote"   => "question_vote",
		"tags"            => "tags",
		"answer_button"   => "answer_button",
		"answers_count"   => "answers_count",
		"views_count"     => "views_count",
		"followers_count" => "followers_count",
		"favourite"       => "favourite",
		"share"           => "share",
	);

	$array_single_options = array(
		"category"        => esc_html__('Category','wpqa'),
		"date"            => esc_html__('Date','wpqa'),
		"author_image"    => esc_html__('Author Image','wpqa'),
		"author"          => esc_html__('Author','wpqa'),
		"question_vote"   => esc_html__('Question vote','wpqa'),
		"tags"            => esc_html__('Tags','wpqa'),
		"answer_button"   => esc_html__('Answer button','wpqa'),
		"answers_count"   => esc_html__('Answers count','wpqa'),
		"views_count"     => esc_html__('Views count','wpqa'),
		"followers_count" => esc_html__('Followers count','wpqa'),
		"favourite"       => esc_html__('Favourite','wpqa'),
		"share"           => esc_html__('Share','wpqa'),
	);

	$array_post_std = array(
		"category"        => "category",
		"date"            => "date",
		"author_image"    => "author_image",
		"author"          => "author",
		"tags"            => "tags",
		"comment_button"  => "comment_button",
		"comments_count"  => "comments_count",
		"views_count"     => "views_count",
	);

	$array_post_options = array(
		"category"        => esc_html__('Category','wpqa'),
		"date"            => esc_html__('Date','wpqa'),
		"author_image"    => esc_html__('Author Image','wpqa'),
		"author"          => esc_html__('Author','wpqa'),
		"tags"            => esc_html__('Tags','wpqa'),
		"comment_button"  => esc_html__('Comment button','wpqa'),
		"comments_count"  => esc_html__('Comments count','wpqa'),
		"views_count"     => esc_html__('Views count','wpqa'),
	);

	$array_single_post_std = array(
		"category"        => "category",
		"date"            => "date",
		"author_image"    => "author_image",
		"author"          => "author",
		"tags"            => "tags",
		"comment_button"  => "comment_button",
		"comments_count"  => "comments_count",
		"views_count"     => "views_count",
		"share"           => "share",
	);

	$array_single_post_options = array(
		"category"        => esc_html__('Category','wpqa'),
		"date"            => esc_html__('Date','wpqa'),
		"author_image"    => esc_html__('Author Image','wpqa'),
		"author"          => esc_html__('Author','wpqa'),
		"tags"            => esc_html__('Tags','wpqa'),
		"comment_button"  => esc_html__('Comment button','wpqa'),
		"comments_count"  => esc_html__('Comments count','wpqa'),
		"views_count"     => esc_html__('Views count','wpqa'),
		"share"           => esc_html__('Share','wpqa'),
	);

	$mobile_applications = array(
		"request_app"         => esc_html__('Request my APP','wpqa'),
		"general_mobile"      => esc_html__('General settings','wpqa'),
		"guide_pages"         => esc_html__('Guide pages','wpqa'),
		"setting_page"        => esc_html__('Setting page','wpqa'),
		"header_mobile"       => esc_html__('Mobile header','wpqa'),
		"bottom_bar"          => esc_html__('Bottom bar','wpqa'),
		"side_navbar"         => esc_html__('Side navbar','wpqa'),
		"mobile_question"     => esc_html__('Ask questions','wpqa'),
		"ads_mobile"          => esc_html__('Advertising','wpqa'),
		"app_notifications"   => esc_html__('Notifications','wpqa'),
		"captcha_mobile"      => esc_html__('Captcha settings','wpqa'),
		"home_mobile"         => esc_html__('Home settings','wpqa'),
		"categories_mobile"   => esc_html__('Categories settings','wpqa'),
		"search_mobile"       => esc_html__('Search settings','wpqa'),
		"favourites_mobile"   => esc_html__('Favourites settings','wpqa'),
		"followed_questions"  => esc_html__('Followed Questions','wpqa'),
		"questions_mobile"    => esc_html__('Questions page settings','wpqa'),
		"users_mobile"        => esc_html__('Users settings','wpqa'),
		"comments_mobile"     => esc_html__('Comments and answers','wpqa'),
		"blog_mobile"         => esc_html__('Blog settings','wpqa'),
		"single_mobile"       => esc_html__('Single question settings','wpqa'),
		"single_post_mobile"  => esc_html__('Single post settings','wpqa'),
		"styling_mobile"      => esc_html__('Mobile styling','wpqa'),
		"lang_mobile"         => esc_html__('Language settings','wpqa'),
		"mobile_icons"        => esc_html__('Icons settings','wpqa'),
		"mobile_construction" => esc_html__('Under construction','wpqa')
	);

	$array_comment_std = array(
		"author_image" => "author_image",
		"author"       => "author",
	);

	$array_comment_options = array(
		"author_image" => esc_html__('Author Image','wpqa'),
		"author"       => esc_html__('Author','wpqa'),
	);

	$mobile_applications = apply_filters("mobile_api_applications_options",$mobile_applications);

	$options[] = array(
		'name'    => esc_html__('Mobile APP','wpqa'),
		'id'      => 'mobile_applications',
		'type'    => 'heading',
		'icon'    => 'phone',
		'new'     => true,
		'std'     => 'request_app',
		'options' => $mobile_applications
	);
	
	$options[] = array(
		'name' => esc_html__('Request my APP','wpqa'),
		'id'   => 'request_app',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name'  => esc_html__('All the options on this page, if you do not buy the app, will not work.','wpqa'),
		'type'  => 'info',
		'alert' => 'alert-message-warning'
	);

	$options[] = array(
		'name' => esc_html__('Activate a custom URL for your site different than the main URL','wpqa'),
		'desc' => esc_html__('Something like with www or without it, or with https or with http','wpqa'),
		'id'   => 'activate_custom_baseurl',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Type your custom URL for your site different than the main URL','wpqa'),
		'id'        => 'custom_baseurl',
		'std'       => esc_url(home_url('/')),
		'condition' => 'activate_custom_baseurl:not(0)',
		'type'      => 'text'
	);

	$options[] = array(
		'name' => esc_html__('App Name','wpqa'),
		'desc' => esc_html__("Your app's name shown on Play Store and App Store","wpqa"),
		'id'   => 'app_name',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Upload the application icon and it must be (1024*1024px), PNG and NOT transparent','wpqa'),
		'id'   => 'application_icon',
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('App bundle id','wpqa'),
		'desc' => esc_html__("It must be small letters (from 'a' to 'z'), like info.2code.app","wpqa"),
		'id'   => 'app_bundle_id',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('App IOS bundle id','wpqa'),
		'desc' => esc_html__("It must be small letters (from 'a' to 'z'), like info.2code.app","wpqa"),
		'id'   => 'app_ios_bundle_id',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Application splash screen background color (hex code, ex: #FFFFFF)','wpqa'),
		'id'   => 'splash_screen_background',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Upload the application splash screen and it must be (512*512px), PNG and NOT transparent','wpqa'),
		'id'   => 'application_splash_screen',
		'type' => 'upload',
	);

	$options[] = array(
		'name'  => '<a href="https://2code.info/docs/mobile/apple-ios-app/" target="_blank">'.esc_html__('You can get the Issuer ID, KEY ID, Password of APP-SPECIFIC PASSWORDS and AuthKey file from here and these are required if you need the IOS version.','wpqa').'</a>',
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Issuer ID *','wpqa'),
		'id'   => 'app_issuer_id',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Key ID *','wpqa'),
		'id'   => 'app_key_id',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Add the AuthKey file content, this file for the IOS app *','wpqa'),
		'id'   => 'authkey_content',
		'type' => 'textarea',
	);

	$options[] = array(
		'name'  => esc_html__('Small notifications icon for android.','wpqa'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the small notifications icon for android','wpqa'),
		'id'   => 'android_notification',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'android_notification:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('The color of the small notifications icon for android (hex code, ex: #FFFFFF)','wpqa'),
		'id'   => 'android_notification_color',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('The icon of the small notifications icon for android and it must be (20*20px), PNG and transparent','wpqa'),
		'id'   => 'android_notification_icon',
		'type' => 'upload',
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
		'name' => esc_html__('General settings','wpqa'),
		'id'   => 'general_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name'  => sprintf(esc_html__('You can get the icons to use it in the app from: %s','wpqa'),'<a href="https://2code.info/mobile/icons/" target="_blank">'.esc_html__('here','wpqa').'</a>'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the force update','wpqa'),
		'desc' => esc_html__('The force update to allow the users must update the app to continue using it','wpqa'),
		'id'   => 'force_update',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'force_update:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Last Android version','wpqa'),
		'id'   => 'android_version',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Last IOS version','wpqa'),
		'id'   => 'ios_version',
		'type' => 'text',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('The app language','wpqa'),
		'id'   => 'app_lang',
		'type' => 'text',
		'std'  => 'en',
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the circle button to ask a question or add a post','wpqa'),
		'id'   => 'addaction_mobile',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$addaction_mobile_action = apply_filters("mobile_api_addaction_button",array("question" => esc_html__("Ask a question","wpqa"),"post" => esc_html__("Add a post","wpqa")));

	$options[] = array(
		'name'      => esc_html__('Choose the circle button to ask a question or add a post','wpqa'),
		'id'        => 'addaction_mobile_action',
		'std'       => 'question',
		'options'   => $addaction_mobile_action,
		'condition' => 'addaction_mobile:not(0)',
		'type'      => 'radio'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to activate the follow questions on the app','wpqa'),
		'id'   => 'mobile_setting_follow_questions',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to hide the dislike on the app','wpqa'),
		'id'   => 'mobile_setting_dislike',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to make the app for the logged users only.','wpqa'),
		'id'   => 'mobile_logged_only',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'  => esc_html__('The next two options to make the changes for your app live, and the other users will see the changes after making refresh or reopen the app.','wpqa'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'    => esc_html__("Choose the roles you need to show for them the live change for the app","wpqa"),
		'id'      => 'mobile_live_change_groups',
		'type'    => 'multicheck',
		'options' => $new_roles,
		'std'     => array('administrator' => 'administrator','editor' => 'editor','contributor' => 'contributor'),
	);

	$options[] = array(
		'name' => esc_html__('Add more specific user ids to show the live change','wpqa'),
		'id'   => 'mobile_live_change_specific_users',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable to show the parent categories with child category','wpqa'),
		'desc' => esc_html__('Show the parent categories with child category, in following categories page, ask question form, and categories page','wpqa'),
		'id'   => 'mobile_parent_categories',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Write the number of categories which show in the categories page or add 0 to show all of them','wpqa'),
		'id'   => 'mobile_categories_page',
		'std'  => 0,
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Add the height you want to show for the featured image in the app','wpqa'),
		'id'   => 'mobile_featured_image',
		'std'  => 200,
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Guide pages','wpqa'),
		'id'   => 'guide_pages',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('Enable or disable the guide pages','wpqa'),
		'id'   => 'onboardmodels_mobile',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'onboardmodels_mobile:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Upload the image for first guide page','wpqa'),
		'id'   => 'onboardmodels_img_1_mobile',
		'std'  => $imagepath_theme."1.png",
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('Add the title for first guide page','wpqa'),
		'id'   => 'onboardmodels_title_1_mobile',
		'std'  => "Welcome",
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the sub title for first guide page','wpqa'),
		'id'   => 'onboardmodels_subtitle_1_mobile',
		'std'  => "Lorem Ipsum is simply dummy text of the printing and typesetting industry",
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Upload the image for second guide page','wpqa'),
		'id'   => 'onboardmodels_img_2_mobile',
		'std'  => $imagepath_theme."2.png",
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('Add the title for second guide page','wpqa'),
		'id'   => 'onboardmodels_title_2_mobile',
		'std'  => "You are here",
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the sub title for second guide page','wpqa'),
		'id'   => 'onboardmodels_subtitle_2_mobile',
		'std'  => "Lorem Ipsum is simply dummy text of the printing and typesetting industry",
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Upload the image for third guide page','wpqa'),
		'id'   => 'onboardmodels_img_3_mobile',
		'std'  => $imagepath_theme."3.png",
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('Add the title for third guide page','wpqa'),
		'id'   => 'onboardmodels_title_3_mobile',
		'std'  => "Continue to ".wpqa_name_theme,
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the sub title for third guide page','wpqa'),
		'id'   => 'onboardmodels_subtitle_3_mobile',
		'std'  => "Lorem Ipsum is simply dummy text of the printing and typesetting industry",
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
		'name' => esc_html__('Setting page','wpqa'),
		'id'   => 'setting_page',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the text size','wpqa'),
		'id'   => 'text_size_app',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the rate app','wpqa'),
		'id'   => 'rate_app',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the edit profile page','wpqa'),
		'id'   => 'edit_profile_app',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the notifications page','wpqa'),
		'id'   => 'notifications_page_app',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the users to stop the notifications or not on the app','wpqa'),
		'id'   => 'activate_stop_notification',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options = apply_filters("mobile_api_options_in_settings_page",$options);

	$options[] = array(
		'name' => esc_html__('Enable or disable the about us page','wpqa'),
		'id'   => 'about_us_app',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Choose the about us page','wpqa'),
		'id'        => 'about_us_page_app',
		'type'      => 'select',
		'condition' => 'about_us_app:not(0)',
		'options'   => $not_template_pages
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the privacy policy page','wpqa'),
		'id'   => 'privacy_policy_app',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'  => esc_html__('You must choose the privacy page.','wpqa'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'      => esc_html__('Choose the privacy policy page','wpqa'),
		'id'        => 'privacy_policy_page_app',
		'type'      => 'select',
		'condition' => 'privacy_policy_app:not(0)',
		'options'   => $not_template_pages
	);

	$options[] = array(
		'name'  => esc_html__('You must choose the terms page.','wpqa'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'    => esc_html__('Choose the terms and conditions page','wpqa'),
		'id'      => 'terms_page_app',
		'type'    => 'select',
		'options' => $not_template_pages
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the FAQs page','wpqa'),
		'id'   => 'faqs_app',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Choose the FAQs page','wpqa'),
		'id'        => 'faqs_page_app',
		'type'      => 'select',
		'condition' => 'faqs_app:not(0)',
		'options'   => $options_pages
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the contact us page','wpqa'),
		'id'   => 'contact_us_app',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the share app','wpqa'),
		'id'   => 'share_app',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Share title','wpqa'),
		'id'   => 'share_title',
		'std'  => wpqa_name_theme,
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Share image','wpqa'),
		'id'   => 'share_image',
		'std'  => $directory_uri."/screenshot.png",
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('Share android URL','wpqa'),
		'id'   => 'share_android',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Share IOS URL','wpqa'),
		'id'   => 'share_ios',
		'type' => 'text',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Mobile header','wpqa'),
		'id'   => 'header_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name'    => esc_html__('Logo position','wpqa'),
		'id'      => 'mobile_logo_position',
		'std'     => 'start',
		'type'    => 'radio',
		'options' => array("start" => esc_html__("Left","wpqa"),"center" => esc_html__("Center","wpqa"))
	);

	$options[] = array(
		'name' => esc_html__('Upload the logo','wpqa'),
		'id'   => 'mobile_logo',
		'std'  => $imagepath_theme."logo-light-2x.png",
		'type' => 'upload',
	);
	
	$options[] = array(
		'name' => esc_html__('Upload the dark logo','wpqa'),
		'id'   => 'mobile_logo_dark',
		'std'  => $imagepath_theme."logo-colored.png",
		'type' => 'upload',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options = apply_filters("mobile_api_after_header_settings",$options);

	$options[] = array(
		'name' => esc_html__('Bottom bar','wpqa'),
		'id'   => 'bottom_bar',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('Enable or disable the bottom bar','wpqa'),
		'id'   => 'bottom_bar_activate',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'bottom_bar_activate:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'  => esc_html__('You must choose 4 items only to show in the bottom bar.','wpqa'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$main_pages = array(
		"home"            => esc_html__('Home','wpqa'),
		"ask"             => esc_html__('Ask Question','wpqa'),
		"categories"      => esc_html__('Question Categories','wpqa'),
		"favorite"        => esc_html__('Favorite','wpqa'),
		"followed"        => esc_html__('Followed Questions','wpqa'),
		"settings"        => esc_html__('Settings','wpqa'),
		"questions"       => esc_html__('Questions','wpqa'),
		"polls"           => esc_html__('Polls','wpqa'),
		"blog"            => esc_html__('Blog','wpqa'),
		"users"           => esc_html__('Users','wpqa'),
		"post_categories" => esc_html__('Post Categories','wpqa'),
		"search"          => esc_html__('Search','wpqa'),
		"contact_us"      => esc_html__('Contact Us','wpqa'),
		"post"            => esc_html__('Add Post','wpqa'),
		"points"          => esc_html__('Badges and points','wpqa'),
		"answers"         => esc_html__('Answers','wpqa'),
		"comments"        => esc_html__('Comments','wpqa'),
		"notifications"   => esc_html__('Notifications','wpqa'),
		"editProfile"     => esc_html__('Edit Profile','wpqa'),
	);
	$main_pages = apply_filters("mobile_api_options_main_pages",$main_pages);

	$bottom_bar_elements = array(
		array(
			"type"    => "radio",
			"id"      => "type",
			"name"    => esc_html__('Type','wpqa'),
			'options' => array(
				'main'       => esc_html__('Main page','wpqa'),
				'q_category' => esc_html__('Question category','wpqa'),
				'p_category' => esc_html__('Post category','wpqa'),
				'page'       => esc_html__('Page','wpqa'),
				'webview'    => esc_html__('Webview page','wpqa'),
			),
			'std'     => 'main',
		),
		array(
			"type"      => "select",
			"id"        => "main",
			"name"      => esc_html__('Main pages','wpqa'),
			'options'   => $main_pages,
			"condition" => "[%id%]type:is(main)",
			'std'       => 'home',
		),
		array(
			"type"      => "select",
			"id"        => "feed",
			"name"      => esc_html__('Feed page','wpqa'),
			'options'   => $options_pages,
			"condition" => "[%id%]type:is(main),[%id%]main:is(feed)",
			'std'       => 'home',
		),
		array(
			"type"        => "select_category",
			'option_none' => esc_html__('Select a Category','wpqa'),
			"id"          => "q_category",
			"taxonomy"    => wpqa_question_categories,
			"name"        => esc_html__('Question category','wpqa'),
			"condition"   => "[%id%]type:is(q_category)",
		),
		array(
			"type"        => "select_category",
			'option_none' => esc_html__('Select a Category','wpqa'),
			"id"          => "p_category",
			"taxonomy"    => "category",
			"name"        => esc_html__('Post category','wpqa'),
			"condition"   => "[%id%]type:is(p_category)",
		),
		array(
			"type"      => "select",
			"id"        => "page",
			"options"   => $not_template_pages,
			"name"      => esc_html__('Page','wpqa'),
			"condition" => "[%id%]type:is(page)",
		),
		array(
			"type"      => "select",
			"id"        => "webview",
			"options"   => $options_pages,
			"name"      => esc_html__('Webview Page','wpqa'),
			"condition" => "[%id%]type:is(webview)",
		),
		array(
			"type"      => "text",
			"id"        => "link",
			"name"      => esc_html__('Or add your custom link','wpqa'),
			"condition" => "[%id%]type:is(webview)"
		),
		array(
			"type" => "text",
			"id"   => "title",
			"name" => esc_html__('New title','wpqa')
		),
		array(
			"type" => "text",
			"id"   => "icon",
			"name" => esc_html__('Icon','wpqa')
		),
	);

	$old_bottom_bar = array();
	$mobile_bottom_bar = wpqa_options("mobile_bottom_bar");
	if (!is_array($mobile_bottom_bar) || (is_array($mobile_bottom_bar) && empty($mobile_bottom_bar))) {
		$mobile_bottom_bar = array(
			"home" => "home",
			"categories" => "categories",
			"favorite" => "favorite",
			"settings" => "settings",
		);
	}
	if (is_array($mobile_bottom_bar) && !empty($mobile_bottom_bar)) {
		foreach ($mobile_bottom_bar as $key => $value) {
			if ($value != "" && $value == $key) {
				if ($key == "ask") {
					$icon = "0xe826";
				}else if ($key == "home") {
					$icon = "0xe800";
				}else if ($key == "categories") {
					$icon = "0xe801";
				}else if ($key == "favorite") {
					$icon = "0xe803";
				}else if ($key == "settings") {
					$icon = "0xe804";
				}else if ($key == "blog") {
					$icon = "0xedcb";
				}else if ($key == "post") {
					$icon = "0xeb90";
				}else if ($key == "points") {
					$icon = "0xe827";
				}
				$old_bottom_bar[] = array(
					"type" => "main",
					"main" => $key,
					"icon" => $icon
				);
			}
		}
	}
	
	$options[] = array(
		'id'      => "add_bottom_bars",
		'type'    => "elements",
		'button'  => esc_html__('Add a new link','wpqa'),
		'hide'    => "yes",
		'std'     => $old_bottom_bar,
		'options' => $bottom_bar_elements,
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
		'name' => esc_html__('Side navbar','wpqa'),
		'id'   => 'side_navbar',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the side navbar','wpqa'),
		'id'   => 'side_navbar_activate',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'side_navbar_activate:not(0)',
		'type'      => 'heading-2'
	);

	$sidenav_elements = array(
		array(
			"type"    => "radio",
			"id"      => "type",
			"name"    => esc_html__('Type','wpqa'),
			'options' => array(
				'main'       => esc_html__('Main page','wpqa'),
				'q_category' => esc_html__('Question category','wpqa'),
				'p_category' => esc_html__('Post category','wpqa'),
				'page'       => esc_html__('Page','wpqa'),
				'webview'    => esc_html__('Webview page','wpqa'),
			),
			'std'     => 'main',
		),
		array(
			"type"      => "select",
			"id"        => "main",
			"name"      => esc_html__('Main pages','wpqa'),
			'options'   => $main_pages,
			"condition" => "[%id%]type:is(main)",
			'std'       => 'home',
		),
		array(
			"type"      => "select",
			"id"        => "feed",
			"name"      => esc_html__('Feed page','wpqa'),
			'options'   => $options_pages,
			"condition" => "[%id%]type:is(main),[%id%]main:is(feed)",
			'std'       => 'home',
		),
		array(
			"type"        => "select_category",
			'option_none' => esc_html__('Select a Category','wpqa'),
			"id"          => "q_category",
			"taxonomy"    => wpqa_question_categories,
			"name"        => esc_html__('Question category','wpqa'),
			"condition"   => "[%id%]type:is(q_category)",
		),
		array(
			"type"        => "select_category",
			'option_none' => esc_html__('Select a Category','wpqa'),
			"id"          => "p_category",
			"taxonomy"    => "category",
			"name"        => esc_html__('Post category','wpqa'),
			"condition"   => "[%id%]type:is(p_category)",
		),
		array(
			"type"      => "select",
			"id"        => "page",
			"options"   => $not_template_pages,
			"name"      => esc_html__('Page','wpqa'),
			"condition" => "[%id%]type:is(page)",
		),
		array(
			"type"      => "select",
			"id"        => "webview",
			"options"   => $options_pages,
			"name"      => esc_html__('Webview Page','wpqa'),
			"condition" => "[%id%]type:is(webview)",
		),
		array(
			"type"      => "text",
			"id"        => "link",
			"name"      => esc_html__('Or add your custom link','wpqa'),
			"condition" => "[%id%]type:is(webview)"
		),
		array(
			"type" => "text",
			"id"   => "title",
			"name" => esc_html__('New title','wpqa')
		),
		array(
			"type" => "text",
			"id"   => "icon",
			"name" => esc_html__('Icon','wpqa')
		),
	);

	$old_side_nav = array();
	$mobile_side_navbar = wpqa_options("mobile_side_navbar");
	if (!is_array($mobile_side_navbar) || (is_array($mobile_side_navbar) && empty($mobile_side_navbar))) {
		$mobile_side_navbar = array(
			"home"       => array("sort" => esc_html__('Home','wpqa'),"value" => "home"),
			"ask"        => array("sort" => esc_html__('Ask Question','wpqa'),"value" => "ask"),
			"categories" => array("sort" => esc_html__('Categories','wpqa'),"value" => "categories"),
			"favorite"   => array("sort" => esc_html__('Favorite','wpqa'),"value" => "favorite"),
			"settings"   => array("sort" => esc_html__('Settings','wpqa'),"value" => "settings"),
			"blog"       => array("sort" => esc_html__('Blog','wpqa'),"value" => "blog"),
			"post"       => array("sort" => esc_html__('Add Post','wpqa'),"value" => "post"),
			"points"     => array("sort" => esc_html__('Badges and points','wpqa'),"value" => "points"),
		);
	}
	if (is_array($mobile_side_navbar) && !empty($mobile_side_navbar)) {
		foreach ($mobile_side_navbar as $key => $value) {
			if (isset($value["value"]) && $value["value"] != "" && $value["value"] == $key) {
				if ($key == "ask") {
					$icon = "0xe826";
				}else if ($key == "home") {
					$icon = "0xe800";
				}else if ($key == "categories") {
					$icon = "0xe801";
				}else if ($key == "favorite") {
					$icon = "0xe803";
				}else if ($key == "settings") {
					$icon = "0xe804";
				}else if ($key == "blog") {
					$icon = "0xedcb";
				}else if ($key == "post") {
					$icon = "0xeb90";
				}else if ($key == "points") {
					$icon = "0xe827";
				}
				$old_side_nav[] = array(
					"type" => "main",
					"main" => $key,
					"icon" => $icon
				);
			}
		}
	}
	
	$options[] = array(
		'id'      => "add_sidenavs",
		'type'    => "elements",
		'button'  => esc_html__('Add a new link','wpqa'),
		'hide'    => "yes",
		'std'     => $old_side_nav,
		'options' => $sidenav_elements,
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
		'name' => esc_html__('Ask questions','wpqa'),
		'id'   => 'mobile_question',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Write the number of categories which show in the ask question form or add 0 to show all of them','wpqa'),
		'id'   => 'mobile_question_categories',
		'std'  => 0,
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Advertising','wpqa'),
		'id'   => 'ads_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Acivate the advertising','wpqa'),
		'id'   => 'mobile_adv',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'mobile_adv:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob Android id','wpqa'),
		'id'   => 'ad_mob_android',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob IOS id','wpqa'),
		'id'   => 'ad_mob_ios',
		'type' => 'text',
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
		'type'      => 'group',
		'id'        => 'ads_mobile',
		'condition' => 'mobile_adv:not(0)',
		'name'      => esc_html__('Interstitial adv','wpqa')
	);

	$options[] = array(
		'name' => esc_html__('Activate the mobile interstitial adv','wpqa'),
		'id'   => 'mobile_interstitial_adv',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'mobile_interstitial_adv:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob Android id for the interstitial','wpqa'),
		'id'   => 'ad_interstitial_android',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob IOS id for the interstitial','wpqa'),
		'id'   => 'ad_interstitial_ios',
		'type' => 'text',
	);
	
	$options[] = array(
		"name" => esc_html__('Choose how many time will open the ad, you can leave it 0 to open the ad each time opened the questions and posts','wpqa'),
		"id"   => "ad_interstitial_count",
		"type" => "sliderui",
		'std'  => 0,
		"step" => "1",
		"min"  => "0",
		"max"  => "10"
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
		'type'      => 'group',
		'id'        => 'ads_mobile',
		'condition' => 'mobile_adv:not(0)',
		'name'      => esc_html__('Rewarded adv','wpqa')
	);

	$options[] = array(
		'name' => esc_html__('Activate the mobile rewarded adv','wpqa'),
		'id'   => 'mobile_rewarded_adv',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'mobile_rewarded_adv:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob Android id for the rewarded','wpqa'),
		'id'   => 'ad_rewarded_android',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob IOS id for the rewarded','wpqa'),
		'id'   => 'ad_rewarded_ios',
		'type' => 'text',
	);
	
	$options[] = array(
		"name" => esc_html__('Choose how many time will open the ad, you can leave it 0 to open the ad each time opened the questions and posts','wpqa'),
		"id"   => "ad_rewarded_count",
		"type" => "sliderui",
		'std'  => 0,
		"step" => "1",
		"min"  => "0",
		"max"  => "10"
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
		'type'      => 'group',
		'id'        => 'ads_mobile',
		'condition' => 'mobile_adv:not(0)',
		'name'      => esc_html__('Banner adv','wpqa')
	);

	$options[] = array(
		'name' => esc_html__('Activate the mobile banner adv','wpqa'),
		'id'   => 'mobile_banner_adv',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'mobile_banner_adv:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob Android id for the banner','wpqa'),
		'id'   => 'ad_banner_android',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob IOS id for the banner','wpqa'),
		'id'   => 'ad_banner_ios',
		'type' => 'text',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$mobile_api_options = get_option(wpqa_options);
	$banner_top = (isset($mobile_api_options["banner_top"])?$mobile_api_options["banner_top"]:"");
	$banner_bottom = (isset($mobile_api_options["banner_bottom"])?$mobile_api_options["banner_bottom"]:"");
	$banner_after_post = (isset($mobile_api_options["banner_after_post"])?$mobile_api_options["banner_after_post"]:"");
	$banner_webview = (isset($mobile_api_options["banner_webview"])?$mobile_api_options["banner_webview"]:"");

	$options[] = array(
		'name'      => esc_html__('Select where do you need to activate the ads','wpqa'),
		'id'        => 'mobile_ads',
		'condition' => 'mobile_banner_adv:not(0)',
		'type'      => 'multicheck',
		'std'       => array(
			"top"            => ($banner_top == 'on'?"top":""),
			"bottom"         => ($banner_bottom == 'on'?"bottom":""),
			"post_top"       => "post_top",
			"post_bottom"    => "post_bottom",
			"after_post"     => ($banner_after_post == 'on'?"after_post":""),
			"banner_webview" => ($banner_webview == 'on'?"banner_webview":""),
		),
		'options' => array(
			"top"             => esc_html__('Banner ad in the top','wpqa'),
			"bottom"          => esc_html__('Banner ad in the bottom','wpqa'),
			"before_home"     => esc_html__('Banner ad before home','wpqa'),
			"after_home"      => esc_html__('Banner ad after home','wpqa'),
			"post_top"        => esc_html__('Banner ad on the post or question in the top','wpqa'),
			"post_bottom"     => esc_html__('Banner ad on the post or question in the bottom','wpqa'),
			"before_comments" => esc_html__('Banner ad before the comments or answers on the posts or questions','wpqa'),
			"after_post"      => esc_html__('Banner ad after the post or question','wpqa'),
			"banner_posts"    => esc_html__('Banner ad after each x number of posts and questions','wpqa'),
			"banner_comments" => esc_html__('Banner ad after each x number of comments and answers','wpqa'),
			"banner_webview"  => esc_html__('Banner ad on the webview page','wpqa'),
		)
	);

	$array_ads = array(
		"top" => array("title" => esc_html__('Banner ad in the top','wpqa'),"key" => "top","value" => esc_html__('Activate custom HTML or custom image for the top ad','wpqa')),
		"bottom" => array("title" => esc_html__('Banner ad in the bottom','wpqa'),"key" => "bottom","value" => esc_html__('Activate custom HTML or custom image for the bottom ad','wpqa')),
		"before_home" => array("title" => esc_html__('Banner ad before home','wpqa'),"key" => "before_home","value" => esc_html__('Activate custom HTML or custom image for the before home ad','wpqa')),
		"after_home" => array("title" => esc_html__('Banner ad after home','wpqa'),"key" => "after_home","value" => esc_html__('Activate custom HTML or custom image for the after home ad','wpqa')),
		"post_top" => array("title" => esc_html__('Banner ad on the post or question in the top','wpqa'),"key" => "post_top","value" => esc_html__('Activate custom HTML or custom image on the post or question in the top','wpqa')),
		"post_bottom" => array("title" => esc_html__('Banner ad on the post or question in the bottom','wpqa'),"key" => "post_bottom","value" => esc_html__('Activate custom HTML or custom image on the post or question in the bottom','wpqa')),
		"before_comments" => array("title" => esc_html__('Banner ad before the comments or answers on the posts or questions','wpqa'),"key" => "before_comments","value" => esc_html__('Activate custom HTML or custom image on before the comments or answers on the posts or questions','wpqa')),
		"after_post" => array("title" => esc_html__('Banner ad after the post or question','wpqa'),"key" => "after_post","value" => esc_html__('Activate custom HTML or custom image on after the post or question','wpqa')),
		"posts" => array("title" => esc_html__('Banner ad after each x number of posts and questions','wpqa'),"key" => "banner_posts","value" => esc_html__('Activate custom HTML or custom image for the posts ad','wpqa'),"position" => esc_html__('Display after x posts and questions','wpqa')),
		"comments" => array("title" => esc_html__('Banner ad after each x number of comments and answers','wpqa'),"key" => "banner_comments","value" => esc_html__('Activate custom HTML or custom image for the comments ad','wpqa'),"position" => esc_html__('Display after x comments and answers','wpqa')),
		"banner_webview" => array("title" => esc_html__('Banner ad on the webview page','wpqa'),"key" => "banner_webview","value" => esc_html__('Activate custom HTML or custom image the webview page','wpqa')),
	);

	if (is_array($array_ads) && !empty($array_ads)) {
		$options[] = array(
			'type' => 'group',
			'end'  => 'end'
		);

		foreach ($array_ads as $key => $value) {
			$options[] = array(
				'type'      => 'group',
				'id'        => 'ads_mobile',
				'condition' => 'mobile_adv:not(0),mobile_banner_adv:not(0),mobile_ads:has('.$value["key"].')',
				'name'      => $value["title"]
			);

			$options[] = array(
				'name'    => esc_html__('Select the size of the ad','wpqa'),
				'id'      => 'mobile_ad_'.$key.'_size',
				'type'    => 'select',
				'std'     => 'BANNER',
				'options' => array("BANNER" => esc_html__("Banner","wpqa"),"LARGE_BANNER" => esc_html__("Large Banner","wpqa"),"MEDIUM_RECTANGLE" => esc_html__("Medium Rectangle","wpqa"),"FULL_BANNER" => esc_html__("Full Size Banner","wpqa"),"LEADERBOARD" => esc_html__("Leaderboard","wpqa"),"adaptive_banner" => esc_html__("Adaptive banner","wpqa"),"SMART_BANNER" => esc_html__("Smart banner","wpqa"))
			);

			$options[] = array(
				"name"      => esc_html__('Choose the width of the Adaptive banner or leave it 0 to make it full width','wpqa'),
				"id"        => "mobile_ad_".$key."_size_width",
				"type"      => "sliderui",
				'condition' => 'mobile_ad_'.$key.'_size:is(adaptive_banner)',
				'std'       => 0,
				"step"      => "5",
				"min"       => "0",
				"max"       => "400"
			);

			if (isset($value["position"])) {
				$options[] = array(
					'name' => $value["position"],
					'id'   => 'mobile_ad_'.$key.'_position',
					'std'  => '2',
					'type' => 'text'
				);
			}

			$options[] = array(
				'name' => $value["value"],
				'id'   => 'mobile_ad_html_'.$key.'',
				'type' => 'checkbox'
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => 'mobile_ad_html_'.$key.':not(0)',
				'type'      => 'heading-2'
			);

			$options[] = array(
				'name'    => esc_html__('Advertising type','wpqa'),
				'id'      => 'mobile_ad_html_'.$key.'_type',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','wpqa'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
				'id'        => 'mobile_ad_html_'.$key.'_img',
				'condition' => 'mobile_ad_html_'.$key.'_type:is(custom_image)',
				'type'      => 'upload'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising URL','wpqa'),
				'id'        => 'mobile_ad_html_'.$key.'_href',
				'std'       => '#',
				'condition' => 'mobile_ad_html_'.$key.'_type:is(custom_image)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html','wpqa'),
				'id'        => 'mobile_ad_html_'.$key.'_code',
				'condition' => 'mobile_ad_html_'.$key.'_type:not(custom_image)',
				'type'      => 'textarea'
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
		}
		$options[] = array(
			'type' => 'html',
			'html'  => '<div><div>'
		);
	}
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Captcha settings','wpqa'),
		'id'   => 'captcha_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable reCaptcha','wpqa'),
		'id'   => 'activate_captcha_mobile',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'activate_captcha_mobile:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Select where do you need to activate the captcha','wpqa'),
		'id'      => 'captcha_positions',
		'type'    => 'multicheck',
		'std'     => array(
			"login"    => "login",
			"register" => "register",
		),
		'options' => array(
			"login"    => esc_html__('Sign in','wpqa'),
			"register" => esc_html__('Sign up','wpqa'),
			"answer"   => esc_html__('Add a new answer','wpqa'),
			"question" => esc_html__('Ask a new question','wpqa'),
		)
	);

	$options[] = array(
		'name'  => sprintf(esc_html__('You can get the reCaptcha v2 site and secret keys from: %s','wpqa'),'<a href="https://www.google.com/recaptcha/admin/" target="_blank">'.esc_html__('here','wpqa').'</a> > <a href="https://2code.d.pr/DUAKq5" target="_blank">'.esc_html__('like that','wpqa').'</a>'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'  => sprintf(esc_html__('Add this in the domain option: %s','wpqa'),'recaptcha-flutter-plugin.firebaseapp.com'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('Site key reCaptcha','wpqa'),
		'id'   => 'site_key_recaptcha_mobile',
		'type' => 'text',
	);
	
	$options[] = array(
		'name' => esc_html__('Secret key reCaptcha','wpqa'),
		'id'   => 'secret_key_recaptcha_mobile',
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
		'name' => esc_html__('Notifications','wpqa'),
		'id'   => 'app_notifications',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name'    => esc_html__("Choose the tabs on the notifications page.","wpqa"),
		'id'      => 'mobile_notifications_tabs',
		'type'    => 'multicheck',
		'std'     => array(
			"unread" => "unread",
			"all"    => "all",
		),
		'options' => array(
			"unread" => esc_html__('Unread','wpqa'),
			"all"    => esc_html__('All','wpqa'),
		)
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable push notifications','wpqa'),
		'id'   => 'push_notifications',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'push_notifications:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'  => '<a href="https://2code.info/docs/mobile/push-notifications-key/" target="_blank">'.esc_html__('You can get the key from here.','wpqa').'</a>',
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Add the app key','wpqa'),
		'id'   => 'app_key',
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
		'name' => esc_html__('Home settings','wpqa'),
		'id'   => 'home_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$pages = get_pages(array('meta_key' => '_wp_page_template','meta_value' => 'template-home.php'));
	
	$options[] = array(
		'name'    => esc_html__('Choose the home page','wpqa'),
		'id'      => 'home_page_app',
		'type'    => 'select',
		'std'     => (isset($pages) && isset($pages[0]) && isset($pages[0]->ID)?$pages[0]->ID:''),
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the homepage','wpqa'),
		'id'   => 'count_posts_home',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Pagination style','wpqa'),
		'desc'    => esc_html__('Choose pagination style for the first tab on homepage from here.','wpqa'),
		'id'      => 'load_more_home',
		'options' => array(
			'load_more'       => esc_html__('Load more','wpqa'),
			'infinite_scroll' => esc_html__('Infinite scroll','wpqa'),
		),
		'std'     => 'load_more',
		'type'    => 'radio'
	);

	$options[] = array(
		'name'    => esc_html__('Select the home options for questions','wpqa'),
		'id'      => 'mobile_setting_home',
		'type'    => 'multicheck',
		'std'     => $array_std,
		'options' => $array_options
	);

	$options[] = array(
		'name'    => esc_html__('Select the home options for posts','wpqa'),
		'id'      => 'mobile_setting_home_posts',
		'type'    => 'multicheck',
		'std'     => $array_post_std,
		'options' => $array_post_options
	);

	$options[] = array(
		'name'      => esc_html__('Activate the ad in the first tab in the top','wpqa'),
		'id'        => 'ads_mobile_top',
		'condition' => 'mobile_adv:not(0)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Activate the ad in the first tab in the bottom','wpqa'),
		'id'        => 'ads_mobile_bottom',
		'condition' => 'mobile_adv:not(0)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Categories settings','wpqa'),
		'id'   => 'categories_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the categories','wpqa'),
		'id'   => 'count_posts_categories',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the categories options for questions','wpqa'),
		'id'      => 'mobile_setting_categories',
		'type'    => 'multicheck',
		'std'     => $array_std,
		'options' => $array_options
	);

	$options[] = array(
		'name'    => esc_html__('Select the categories for posts','wpqa'),
		'id'      => 'mobile_setting_categories_posts',
		'type'    => 'multicheck',
		'std'     => $array_post_std,
		'options' => $array_post_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Search settings','wpqa'),
		'id'   => 'search_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options = apply_filters("mobile_api_before_search_question_settings",$options);

	$options[] = array(
		'name' => esc_html__('Items per page in the search','wpqa'),
		'id'   => 'count_posts_search',
		'std'  => "3",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the search options for questions','wpqa'),
		'id'      => 'mobile_setting_search',
		'type'    => 'multicheck',
		'std'     => $array_std,
		'options' => $array_options
	);

	$options = apply_filters("mobile_api_after_search_question_settings",$options);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Favourites settings','wpqa'),
		'id'   => 'favourites_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the favourite page','wpqa'),
		'id'   => 'count_posts_favourites',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the setting of the favourite page','wpqa'),
		'id'      => 'mobile_setting_favourites',
		'type'    => 'multicheck',
		'std'     => $array_std,
		'options' => $array_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Followed Questions','wpqa'),
		'id'   => 'followed_questions',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the followed page','wpqa'),
		'id'   => 'count_posts_followed',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the setting of the followed page','wpqa'),
		'id'      => 'mobile_setting_followed',
		'type'    => 'multicheck',
		'std'     => $array_std,
		'options' => $array_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Questions page settings','wpqa'),
		'id'   => 'questions_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the questions page','wpqa'),
		'id'   => 'count_posts_questions',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the setting of the blog page','wpqa'),
		'id'      => 'mobile_setting_questions',
		'type'    => 'multicheck',
		'std'     => $array_std,
		'options' => $array_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Users settings','wpqa'),
		'id'   => 'users_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name'    => esc_html__("Choose the timeframe to allow the user stay login before logout automatically.","wpqa"),
		'id'      => 'mobile_time_login',
		'type'    => 'radio',
		'options' => array(
			'hours'  => esc_html__('Hours','wpqa'),
			'days' => esc_html__('Days','wpqa'),
		),
		'std'     => "days",
	);
	
	$options[] = array(
		"name"      => esc_html__('Choose the hours to stay login','wpqa'),
		"id"        => "mobile_time_login_hours",
		"type"      => "sliderui",
		'condition' => 'mobile_time_login:is(hours)',
		'std'       => 1,
		"step"      => "1",
		"min"       => "0",
		"max"       => "100"
	);
	
	$options[] = array(
		"name"      => esc_html__('Choose the days to stay login','wpqa'),
		"id"        => "mobile_time_login_days",
		"type"      => "sliderui",
		'condition' => 'mobile_time_login:is(days)',
		'std'       => 7,
		"step"      => "1",
		"min"       => "0",
		"max"       => "1000"
	);

	$options[] = array(
		'name' => esc_html__('Do you want to add a custom link of the signup button on the app?','wpqa'),
		'id'   => 'activate_custom_register_link',
		'type' => 'checkbox',
	);
	
	$options[] = array(
		'name'      => esc_html__('Add the custom link','wpqa'),
		'desc'      => esc_html__('Type the custom link of the register button from here.','wpqa'),
		'id'        => 'custom_register_link',
		'condition' => 'activate_custom_register_link:not(0)',
		'type'      => 'text'
	);

	$options[] = array(
		'name'    => esc_html__("Choose the style of the social icons style on the user profile page.","wpqa"),
		'id'      => 'mobile_social_icon_style',
		'type'    => 'radio',
		'options' => array(
			'icons' => esc_html__('Icons','wpqa'),
			'links' => esc_html__('Links','wpqa'),
		),
		'std'     => "icons",
	);

	$options[] = array(
		'name' => esc_html__('Write the number of users which show in the following steps in the register and edit profile pages.','wpqa'),
		'id'   => 'mobile_api_following_pages',
		'std'  => 6,
		'type' => 'text'
	);

	$options[] = array(
		'name'    => esc_html__("Choose the roles you need to show for the users in the users page.","wpqa"),
		'id'      => 'mobile_users_roles_page',
		'type'    => 'multicheck',
		'options' => $new_roles,
		'std'     => array('administrator' => 'administrator','editor' => 'editor','contributor' => 'contributor','subscriber' => 'subscriber','author' => 'author'),
	);

	$options[] = array(
		'name' => esc_html__('Write the number of users which show in the users page.','wpqa'),
		'id'   => 'mobile_users_page',
		'std'  => 6,
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Comments settings','wpqa'),
		'id'   => 'comments_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the comments or answers page','wpqa'),
		'id'   => 'count_comments_mobile',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the the setting of the comments for the blog posts','wpqa'),
		'id'      => 'mobile_setting_comments',
		'type'    => 'multicheck',
		'std'     => $array_comment_std,
		'options' => $array_comment_options
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the vote on answers page','wpqa'),
		'id'   => 'vote_answer_mobile',
		'std'  => 'on',
		'type' => 'checkbox',
	);

	$options[] = array(
		'name'    => esc_html__('Answer sort','wpqa'),
		'id'      => 'mobile_answers_sort',
		'std'     => 'voted',
		'type'    => 'radio',
		'options' => array("voted" => esc_html__("Voted","wpqa"),"oldest" => esc_html__("Oldest","wpqa"),"recent" => esc_html__("Recent","wpqa"))
	);

	$options[] = array(
		'name'    => esc_html__('Select the the setting of the answers for the questions','wpqa'),
		'id'      => 'mobile_setting_answers',
		'type'    => 'multicheck',
		'std'     => $array_comment_std,
		'options' => $array_comment_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Blog settings','wpqa'),
		'id'   => 'blog_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the blog page','wpqa'),
		'id'   => 'count_posts_blog',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the setting of the blog page','wpqa'),
		'id'      => 'mobile_setting_blog',
		'type'    => 'multicheck',
		'std'     => $array_post_std,
		'options' => $array_post_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Single question settings','wpqa'),
		'id'   => 'single_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name'    => esc_html__('Tye menu style of report, delete and close for questions or report and delete for answers','wpqa'),
		'id'      => 'menu_style_of_report',
		'std'     => 'menu',
		'options' => array(
			'menu'  => 'Menu style',
			'icons' => 'With icons',
		),
		'type'    => 'radio'
	);

	$options[] = array(
		'name'    => esc_html__('Select the the setting of the single question page','wpqa'),
		'id'      => 'mobile_setting_single',
		'type'    => 'multicheck',
		'std'     => $array_single_std,
		'options' => $array_single_options
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the related questions?','wpqa'),
		'id'   => 'app_related_questions',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'app_related_questions:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Related style','wpqa'),
		'desc'    => esc_html__('Type related question style from here.','wpqa'),
		'id'      => 'app_related_style_questions',
		'std'     => 'with_images',
		'options' => array(
			'with_images' => 'With images',
			'list_style'  => 'List style',
		),
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Related questions number','wpqa'),
		'desc' => esc_html__('Type the number of related questions from here.','wpqa'),
		'id'   => 'app_related_number_questions',
		'std'  => 5,
		'type' => 'text'
	);
	
	$options[] = array(
		'name'    => esc_html__('Query type','wpqa'),
		'desc'    => esc_html__('Select what will the related questions show.','wpqa'),
		'id'      => 'app_query_related_questions',
		'std'     => 'categories',
		'options' => array(
			'categories' => esc_html__('Questions in the same categories','wpqa'),
			'tags'       => esc_html__('Questions in the same tags (If not find any tags will show by the same categories)','wpqa'),
			'author'     => esc_html__('Questions by the same author','wpqa'),
		),
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
		'name' => esc_html__('Single post settings','wpqa'),
		'id'   => 'single_post_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name'    => esc_html__('Select the the setting of the single post page','wpqa'),
		'id'      => 'mobile_setting_single_post',
		'type'    => 'multicheck',
		'std'     => $array_single_post_std,
		'options' => $array_single_post_options
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the related posts?','wpqa'),
		'id'   => 'app_related_posts',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'app_related_posts:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Related style','wpqa'),
		'desc'    => esc_html__('Type related post style from here.','wpqa'),
		'id'      => 'app_related_style',
		'std'     => 'with_images',
		'options' => array(
			'with_images' => 'With images',
			'list_style'  => 'List style',
		),
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Related posts number','wpqa'),
		'desc' => esc_html__('Type the number of related posts from here.','wpqa'),
		'id'   => 'app_related_number',
		'std'  => 5,
		'type' => 'text'
	);
	
	$options[] = array(
		'name'    => esc_html__('Query type','wpqa'),
		'desc'    => esc_html__('Select what will the related posts show.','wpqa'),
		'id'      => 'app_query_related',
		'std'     => 'categories',
		'options' => array(
			'categories' => esc_html__('Posts in the same categories','wpqa'),
			'tags'       => esc_html__('Posts in the same tags (If not find any tags will show by the same categories)','wpqa'),
			'author'     => esc_html__('Posts by the same author','wpqa'),
		),
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
		'name' => esc_html__('Mobile styling','wpqa'),
		'id'   => 'styling_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);
	
	$options[] = array(
		'name'    => esc_html__('APP skin by default','wpqa'),
		'id'      => 'app_skin',
		'std'     => 'light',
		'type'    => 'radio',
		'options' => array("light" => esc_html__("Light","wpqa"),"dark" => esc_html__("Dark","wpqa"))
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the users to choose their skin from the settings page?','wpqa'),
		'id'   => 'activate_switch_mode',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the users to choose their skin from the header icon?','wpqa'),
		'id'   => 'activate_dark_from_header',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the border bottom color only for the inputs?','wpqa'),
		'id'   => 'activate_input_border_bottom',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'  => esc_html__('Light mode settings.','wpqa'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'      => esc_html__('Input background color','wpqa'),
		'id'        => 'inputsbackgroundcolor',
		'type'      => 'color',
		'condition' => 'activate_input_border_bottom:is(0)',
		'std'       => '#000000'
	);

	$options[] = array(
		'name'      => esc_html__('Input border bottom color','wpqa'),
		'id'        => 'input_border_bottom_color',
		'type'      => 'color',
		'condition' => 'activate_input_border_bottom:not(0)',
		'std'       => '#000000'
	);

	$options[] = array(
		'name' => esc_html__('Login, signup, and forgot password background color','wpqa'),
		'id'   => 'loginbackground',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Header Background color','wpqa'),
		'id'   => 'appbarbackgroundcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Tabs Background color','wpqa'),
		'id'   => 'tabbarbackgroundcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar Background color','wpqa'),
		'id'   => 'bottombarbackgroundcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Header Text color','wpqa'),
		'id'   => 'appbarcolor',
		'type' => 'color',
		'std'  => '#283952'
	);

	$options[] = array(
		'name' => esc_html__('Tabs underline/border color','wpqa'),
		'id'   => 'tabbarindicatorcolor',
		'type' => 'color',
		'std'  => '#2d6ff7'
	);

	$options[] = array(
		'name' => esc_html__('Tabs text color','wpqa'),
		'id'   => 'tabbartextcolor',
		'type' => 'color',
		'std'  => '#6D737C'
	);

	$options[] = array(
		'name' => esc_html__('Tabs Active color','wpqa'),
		'id'   => 'tabbaractivetextcolor',
		'type' => 'color',
		'std'  => '#283952'
	);

	$options[] = array(
		'name' => esc_html__('Checkboxes active color','wpqa'),
		'id'   => 'checkboxactivecolor',
		'type' => 'color',
		'std'  => '#505050'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar text color','wpqa'),
		'id'   => 'bottombarinactivecolor',
		'type' => 'color',
		'std'  => '#6D737C'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar Active color','wpqa'),
		'id'   => 'bottombaractivecolor',
		'type' => 'color',
		'std'  => '#2d6ff7'
	);

	$options[] = array(
		'name' => esc_html__('Primary color','wpqa'),
		'id'   => 'mobile_primary',
		'type' => 'color',
		'std'  => '#2d6ff7'
	);

	$options[] = array(
		'name' => esc_html__('Secondary color','wpqa'),
		'id'   => 'mobile_secondary',
		'type' => 'color',
		'std'  => '#283952'
	);

	$options[] = array(
		'name' => esc_html__('Meta color','wpqa'),
		'id'   => 'secondaryvariant',
		'type' => 'color',
		'std'  => '#6D737C'
	);

	$options[] = array(
		'name' => esc_html__('Side navbar background','wpqa'),
		'id'   => 'mobile_background',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Side navbar color','wpqa'),
		'id'   => 'sidemenutextcolor',
		'type' => 'color',
		'std'  => '#333739'
	);

	$options[] = array(
		'name' => esc_html__('Background','wpqa'),
		'id'   => 'scaffoldbackgroundcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Button color','wpqa'),
		'id'   => 'buttontextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Divider color','wpqa'),
		'id'   => 'dividercolor',
		'type' => 'color',
		'std'  => '#EEEEEE'
	);

	$options[] = array(
		'name' => esc_html__('Shadow color','wpqa'),
		'id'   => 'shadowcolor',
		'type' => 'color',
		'std'  => '#000000'
	);

	$options[] = array(
		'name' => esc_html__('Button background color','wpqa'),
		'id'   => 'buttonsbackgroudcolor',
		'type' => 'color',
		'std'  => '#2d6ff7'
	);

	$options[] = array(
		'name' => esc_html__('Settings page background color','wpqa'),
		'id'   => 'settingbackgroundcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Settings page text color','wpqa'),
		'id'   => 'settingtextcolor',
		'type' => 'color',
		'std'  => '#333739'
	);

	$options[] = array(
		'name' => esc_html__('Error background color','wpqa'),
		'id'   => 'errorcolor',
		'type' => 'color',
		'std'  => '#dd3333'
	);

	$options[] = array(
		'name' => esc_html__('Error text color','wpqa'),
		'id'   => 'errortextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Alert background color','wpqa'),
		'id'   => 'alertcolor',
		'type' => 'color',
		'std'  => '#FDEDD3'
	);

	$options[] = array(
		'name' => esc_html__('Alert text color','wpqa'),
		'id'   => 'alerttextcolor',
		'type' => 'color',
		'std'  => '#f5a623'
	);

	$options[] = array(
		'name' => esc_html__('Success background color','wpqa'),
		'id'   => 'successcolor',
		'type' => 'color',
		'std'  => '#4be1ab'
	);

	$options[] = array(
		'name' => esc_html__('Success text color','wpqa'),
		'id'   => 'successtextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Tooltip Menu color','wpqa'),
		'id'   => 'tooltipmenucolor',
		'type' => 'color',
		'std'  => '#FFFFFF'
	);

	$options[] = array(
		'name' => esc_html__('Highlight background color','wpqa'),
		'id'   => 'highlightcolor',
		'type' => 'color',
		'std'  => '#2d6ff7'
	);

	$options[] = array(
		'name' => esc_html__('Highlight text color','wpqa'),
		'id'   => 'highlighttextcolor',
		'type' => 'color',
		'std'  => '#FFFFFF'
	);

	$options[] = array(
		'name' => esc_html__('Close question button background color','wpqa'),
		'id'   => 'closequestionbackgroundcolor',
		'type' => 'color',
		'std'  => '#EEEEEE'
	);

	$options[] = array(
		'name' => esc_html__('Close question button color','wpqa'),
		'id'   => 'closequestionbuttoncolor',
		'type' => 'color',
		'std'  => '#333739'
	);

	$options[] = array(
		'name' => esc_html__('Open question button background color','wpqa'),
		'id'   => 'openquestionbackgroundcolor',
		'type' => 'color',
		'std'  => '#EEEEEE'
	);

	$options[] = array(
		'name' => esc_html__('Open question button color','wpqa'),
		'id'   => 'openquestionbuttoncolor',
		'type' => 'color',
		'std'  => '#333739'
	);

	$options[] = array(
		'name' => esc_html__('Favourite color','wpqa'),
		'id'   => 'favouritecolor',
		'type' => 'color',
		'std'  => '#2d6ff7'
	);

	$options[] = array(
		'name' => esc_html__('Un favourite color','wpqa'),
		'id'   => 'unfavouritecolor',
		'type' => 'color',
		'std'  => '#6D737C'
	);

	$options[] = array(
		'name' => esc_html__('Best answer color','wpqa'),
		'id'   => 'bestanswercolor',
		'type' => 'color',
		'std'  => '#26aa6c'
	);

	$options[] = array(
		'name' => esc_html__('Add best answer color','wpqa'),
		'id'   => 'addbestanswercolor',
		'type' => 'color',
		'std'  => '#2d6ff7'
	);

	$options[] = array(
		'name' => esc_html__('Remove best answer color','wpqa'),
		'id'   => 'removebestanswercolor',
		'type' => 'color',
		'std'  => '#AA0000'
	);

	$options[] = array(
		'name' => esc_html__('Verified icon color','wpqa'),
		'id'   => 'verifiedcolor',
		'type' => 'color',
		'std'  => '#5890ff'
	);

	$options[] = array(
		'name'  => esc_html__('Dark mode settings.','wpqa'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'      => esc_html__('Input background color','wpqa'),
		'id'        => 'dark_inputsbackgroundcolor',
		'type'      => 'color',
		'condition' => 'activate_input_border_bottom:is(0)',
		'std'       => '#2c2c2c'
	);

	$options[] = array(
		'name'      => esc_html__('Input border bottom color','wpqa'),
		'id'        => 'dark_input_border_bottom_color',
		'type'      => 'color',
		'condition' => 'activate_input_border_bottom:not(0)',
		'std'       => '#232323'
	);

	$options[] = array(
		'name' => esc_html__('Login, signup, and forgot password background color','wpqa'),
		'id'   => 'dark_loginbackground',
		'type' => 'color',
		'std'  => '#1a1a1a'
	);

	$options[] = array(
		'name' => esc_html__('Header Background color','wpqa'),
		'id'   => 'dark_appbarbackgroundcolor',
		'type' => 'color',
		'std'  => '#252525'
	);

	$options[] = array(
		'name' => esc_html__('Tabs Background color','wpqa'),
		'id'   => 'dark_tabbarbackgroundcolor',
		'type' => 'color',
		'std'  => '#1a1a1a'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar Background color','wpqa'),
		'id'   => 'dark_bottombarbackgroundcolor',
		'type' => 'color',
		'std'  => '#252525'
	);

	$options[] = array(
		'name' => esc_html__('Header Text color','wpqa'),
		'id'   => 'dark_appbarcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Tabs underline/border color','wpqa'),
		'id'   => 'dark_tabbarindicatorcolor',
		'type' => 'color',
		'std'  => '#7c7c7c'
	);

	$options[] = array(
		'name' => esc_html__('Tabs text color','wpqa'),
		'id'   => 'dark_tabbartextcolor',
		'type' => 'color',
		'std'  => '#7c7c7c'
	);

	$options[] = array(
		'name' => esc_html__('Tabs Active color','wpqa'),
		'id'   => 'dark_tabbaractivetextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Checkboxes active color','wpqa'),
		'id'   => 'dark_checkboxactivecolor',
		'type' => 'color',
		'std'  => '#7c7c7c'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar text color','wpqa'),
		'id'   => 'dark_bottombarinactivecolor',
		'type' => 'color',
		'std'  => '#7c7c7c'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar Active color','wpqa'),
		'id'   => 'dark_bottombaractivecolor',
		'type' => 'color',
		'std'  => '#F0F8FF'
	);

	$options[] = array(
		'name' => esc_html__('General color','wpqa'),
		'id'   => 'dark_mobile_primary',
		'type' => 'color',
		'std'  => '#2d6ff7'
	);

	$options[] = array(
		'name' => esc_html__('Primary color','wpqa'),
		'id'   => 'dark_mobile_secondary',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Meta color','wpqa'),
		'id'   => 'dark_secondaryvariant',
		'type' => 'color',
		'std'  => '#7c7c7c'
	);

	$options[] = array(
		'name' => esc_html__('Side navbar background','wpqa'),
		'id'   => 'dark_mobile_background',
		'type' => 'color',
		'std'  => '#1a1a1a'
	);

	$options[] = array(
		'name' => esc_html__('Side navbar color','wpqa'),
		'id'   => 'dark_sidemenutextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Background','wpqa'),
		'id'   => 'dark_scaffoldbackgroundcolor',
		'type' => 'color',
		'std'  => '#1a1a1a'
	);

	$options[] = array(
		'name' => esc_html__('Button color','wpqa'),
		'id'   => 'dark_buttontextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Divider color','wpqa'),
		'id'   => 'dark_dividercolor',
		'type' => 'color',
		'std'  => '#333333'
	);

	$options[] = array(
		'name' => esc_html__('Shadow color','wpqa'),
		'id'   => 'dark_shadowcolor',
		'type' => 'color',
		'std'  => '#2F4F4F'
	);

	$options[] = array(
		'name' => esc_html__('Button background color','wpqa'),
		'id'   => 'dark_buttonsbackgroudcolor',
		'type' => 'color',
		'std'  => '#2d6ff7'
	);

	$options[] = array(
		'name' => esc_html__('Settings page background color','wpqa'),
		'id'   => 'dark_settingbackgroundcolor',
		'type' => 'color',
		'std'  => '#232323'
	);

	$options[] = array(
		'name' => esc_html__('Settings page text color','wpqa'),
		'id'   => 'dark_settingtextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Error background color','wpqa'),
		'id'   => 'dark_errorcolor',
		'type' => 'color',
		'std'  => '#dd3333'
	);

	$options[] = array(
		'name' => esc_html__('Error text color','wpqa'),
		'id'   => 'dark_errortextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Alert background color','wpqa'),
		'id'   => 'dark_alertcolor',
		'type' => 'color',
		'std'  => '#FDEDD3'
	);

	$options[] = array(
		'name' => esc_html__('Alert text color','wpqa'),
		'id'   => 'dark_alerttextcolor',
		'type' => 'color',
		'std'  => '#f5a623'
	);

	$options[] = array(
		'name' => esc_html__('Success background color','wpqa'),
		'id'   => 'dark_successcolor',
		'type' => 'color',
		'std'  => '#4be1ab'
	);

	$options[] = array(
		'name' => esc_html__('Success text color','wpqa'),
		'id'   => 'dark_successtextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Tooltip Menu color','wpqa'),
		'id'   => 'dark_tooltipmenucolor',
		'type' => 'color',
		'std'  => '#333739'
	);

	$options[] = array(
		'name' => esc_html__('Highlight background color','wpqa'),
		'id'   => 'dark_highlightcolor',
		'type' => 'color',
		'std'  => '#2d6ff7'
	);

	$options[] = array(
		'name' => esc_html__('Highlight text color','wpqa'),
		'id'   => 'dark_highlighttextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Close question button background color','wpqa'),
		'id'   => 'dark_closequestionbackgroundcolor',
		'type' => 'color',
		'std'  => '#333333'
	);

	$options[] = array(
		'name' => esc_html__('Close question button color','wpqa'),
		'id'   => 'dark_closequestionbuttoncolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Open question button background color','wpqa'),
		'id'   => 'dark_openquestionbackgroundcolor',
		'type' => 'color',
		'std'  => '#333333'
	);

	$options[] = array(
		'name' => esc_html__('Open question button color','wpqa'),
		'id'   => 'dark_openquestionbuttoncolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Favourite color','wpqa'),
		'id'   => 'dark_favouritecolor',
		'type' => 'color',
		'std'  => '#2d6ff7'
	);

	$options[] = array(
		'name' => esc_html__('Un favourite color','wpqa'),
		'id'   => 'dark_unfavouritecolor',
		'type' => 'color',
		'std'  => '#6D737C'
	);

	$options[] = array(
		'name' => esc_html__('Best answer color','wpqa'),
		'id'   => 'dark_bestanswercolor',
		'type' => 'color',
		'std'  => '#26aa6c'
	);

	$options[] = array(
		'name' => esc_html__('Add best answer color','wpqa'),
		'id'   => 'dark_addbestanswercolor',
		'type' => 'color',
		'std'  => '#2d6ff7'
	);

	$options[] = array(
		'name' => esc_html__('Remove best answer color','wpqa'),
		'id'   => 'dark_removebestanswercolor',
		'type' => 'color',
		'std'  => '#AA0000'
	);

	$options[] = array(
		'name' => esc_html__('Verified icon color','wpqa'),
		'id'   => 'dark_verifiedcolor',
		'type' => 'color',
		'std'  => '#5890ff'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Language settings','wpqa'),
		'id'   => 'lang_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options = apply_filters("mobile_api_language_options",$options);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Icons settings','wpqa'),
		'id'   => 'mobile_icons',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name'  => sprintf(esc_html__('You can get the icons to use it in the app from: %s','wpqa'),'<a href="https://2code.info/mobile/icons/" target="_blank">'.esc_html__('here','wpqa').'</a>'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'      => esc_html__('Add a new question icon','wpqa'),
		'id'        => 'mobile_addaction_question',
		'condition' => 'addaction_mobile_action:is(question)',
		'std'       => "0xe965",
		'type'      => 'text'
	);

	$options[] = array(
		'name'      => esc_html__('Add a new post icon','wpqa'),
		'id'        => 'mobile_addaction_post',
		'condition' => 'addaction_mobile_action:is(post)',
		'std'       => "0xf0ca",
		'type'      => 'text'
	);

	$options[] = array(
		'name'      => esc_html__('Add a new group icon','wpqa'),
		'id'        => 'mobile_addaction_group',
		'condition' => 'addaction_mobile_action:is(group)',
		'std'       => "0xe963",
		'type'      => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Answers icon','wpqa'),
		'id'   => 'mobile_answers_icon',
		'std'  => "0xe907",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Best answers icon','wpqa'),
		'id'   => 'mobile_best_answers_icon',
		'std'  => "0xe906",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Delete icon','wpqa'),
		'id'   => 'mobile_delete_icon',
		'std'  => "0xf041",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Edit icon','wpqa'),
		'id'   => 'mobile_edit_icon',
		'std'  => "0xee29",
		'type' => 'text'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'menu_style_of_report:is(icons)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Close question icon','wpqa'),
		'id'   => 'mobile_close_icon',
		'std'  => "0xedf1",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Open question icon','wpqa'),
		'id'   => 'mobile_open_icon',
		'std'  => "0xedf0",
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Add favourite icon','wpqa'),
		'id'   => 'mobile_favourite_icon',
		'std'  => "0xe9cb",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Remove favourite icon','wpqa'),
		'id'   => 'mobile_unfavourite_icon',
		'std'  => "0xe931",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Views icon','wpqa'),
		'id'   => 'mobile_views_icon',
		'std'  => "fa-eye",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Do you want to change the verified icon?','wpqa'),
		'id'   => 'activate_verified_icon',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Verified icon','wpqa'),
		'id'        => 'mobile_verified_icon',
		'condition' => 'activate_verified_icon:not(0)',
		'std'       => "0xef82",
		'type'      => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Do you want to change the vote icons?','wpqa'),
		'id'   => 'activate_vote_icons',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'activate_vote_icons:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Upvote icon','wpqa'),
		'id'   => 'mobile_upvote_icon',
		'std'  => "0xe825",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Downvote icon','wpqa'),
		'id'   => 'mobile_downvote_icon',
		'std'  => "0xe824",
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
		'name' => esc_html__('Under construction','wpqa'),
		'id'   => 'mobile_construction',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the under construction on the mobile apps','wpqa'),
		'id'   => 'activate_mobile_construction',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'activate_mobile_construction:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Type your title on the construction page','wpqa'),
		'id'   => 'construction_title',
		'std'  => 'CLOSED!',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Type your content on the construction page','wpqa'),
		'id'   => 'construction_content',
		'std'  => 'This app is coming soon',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Upload the image on the construction page','wpqa'),
		'id'   => 'construction_image',
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the icon on the construction page','wpqa'),
		'id'   => 'activate_construction_icon',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the button on the construction page','wpqa'),
		'id'   => 'activate_construction_button',
		'std'  => 'on',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'activate_construction_button:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Type your text on the button','wpqa'),
		'id'   => 'construction_button_text',
		'std'  => 'Contact',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Type your link on the button','wpqa'),
		'id'   => 'construction_button_url',
		'std'  => 'https://2code.info/',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('The color of the button on the construction page (hex code, ex: #FFFFFF)','wpqa'),
		'id'   => 'construction_button_color',
		'type' => 'color',
		'std'  => '#2d6ff7'
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

	return $options;
}?>