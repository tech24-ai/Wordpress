<?php /* Remove demo meta */
add_action('save_post','wpqa_remove_demo_meta');
function wpqa_remove_demo_meta($post_id) {
	delete_post_meta($post_id,'theme_import_demo');
}
/* Check if the One Click Demo Import plugin is active */
if (class_exists('OCDI_Plugin')) {
	add_action('admin_init','wpqa_disable_ocdi_plugin');
}
function wpqa_disable_ocdi_plugin() {
	if (wp_doing_ajax()) {
		return;
	}
	deactivate_plugins(plugin_basename('one-click-demo-import/one-click-demo-import.php'));
}
/* Branding */
add_filter('pt-ocdi/disable_pt_branding','__return_true');
/* Demo page setting */
add_filter('pt-ocdi/plugin_page_setup','wpqa_plugin_page_setup');
function wpqa_plugin_page_setup($default_settings) {
	$support_activate = wpqa_updater();
	if ($support_activate) {
		$default_settings['parent_slug'] = 'options';
		$default_settings['page_title']  = esc_html__('Demo Import','wpqa');
		$default_settings['menu_title']  = esc_html__('Demo Import','wpqa');
		$default_settings['capability']  = 'manage_options';
		$default_settings['menu_slug']   = 'demo-import';
	}
	return $default_settings;
}
/* Confirm dialog */
add_filter('pt-ocdi/confirmation_dialog_options','my_theme_ocdi_confirmation_dialog_options',10,1);
function my_theme_ocdi_confirmation_dialog_options($options) {
	return array_merge($options,array(
		'width'       => 600,
		'height'      => 600,
		'dialogClass' => 'framework-demo-dialog',
		'resizable'   => false,
		'modal'       => false,
	));
}
/* Demo files */
add_filter('pt-ocdi/import_files','wpqa_import_files');
function wpqa_import_files() {
	$show_demos = get_transient('wpqa_import_demos');
	if (isset($show_demos) && is_array($show_demos) && !empty($show_demos)) {
		return $show_demos;
	}
	if (array_key_exists('page',$_GET) && $_GET['page'] == 'demo-import') {
		$file_path = "https://cdn.2code.info/demos.php?demo=".wpqa_name_theme;
		if ($file_path != "") {
			$response = wp_remote_get($file_path,20);
			$values = (is_array($response) && isset($response["body"])?$response["body"]:"");
			$show_demos = json_decode($values,true);
			if (isset($show_demos) && is_array($show_demos) && !empty($show_demos)) {
				set_transient('wpqa_import_demos', $show_demos, 60*60*24);
			}
		}
		if (isset($show_demos) && is_array($show_demos) && !empty($show_demos)) {
			$demos = true;
		}else {
			$file_path = plugin_dir_url(dirname(__FILE__)).'demos/'.wpqa_name_theme.'.json';
			if ($file_path != "") {
				$response = wp_remote_get($file_path,20);
				$values = (is_array($response) && isset($response["body"])?$response["body"]:"");
				$show_demos = json_decode($values,true);
				if (isset($show_demos) && is_array($show_demos) && !empty($show_demos)) {
					set_transient('wpqa_import_demos', $show_demos, 60*60*24);
					$demos = true;
				}
			}
		}
	}
	$show_demos = (isset($demos)?$show_demos:array());
	return $show_demos;
}
/* Before import the demo */
add_action("pt-ocdi/before_widgets_import","wpqa_before_widgets_import");
function wpqa_before_widgets_import($selected_import) {
	//wp_set_sidebars_widgets(get_option("old_sidebar_widgets"));
	$sidebar_widgets = wp_get_sidebars_widgets();
	update_option("old_sidebar_widgets",$sidebar_widgets);
	update_option("sidebars_widgets","");
}
/* After import the demo */
add_action('pt-ocdi/after_import','wpqa_after_import_setup');
function wpqa_after_import_setup($selected_import) {
	// Demo name
	update_option("demo_import_name",$selected_import['import_file_name']);

	// Old options
	update_option("old_import_demo_options",get_option(wpqa_options));

	// Old menus
	update_option("old_nav_menu_locations",get_nav_menu_locations());

	// Update options
	$file_path = $selected_import['import_options_file_url'];
	if ($file_path != "") {
		$response = wp_remote_get($file_path,20);
		$values = (isset($response["body"])?$response["body"]:"");
		if ($values != "") {
			$admin_email = get_bloginfo("admin_email");
			$parse = parse_url(get_site_url());
			$data = base64_decode($values);
			$data = json_decode($data,true);
			$empty_values = array("paypal_email_sandbox","identity_token_sandbox","paypal_api_username_sandbox","paypal_api_password_sandbox","paypal_api_signature_sandbox","identity_token","paypal_api_username","paypal_api_password","paypal_api_signature","test_publishable_key","test_secret_key","test_webhook_secret","publishable_key","secret_key","webhook_secret","app_issuer_id","app_key_id","authkey_content","app_bundle_id","app_name","construction_redirect","facebook_app_id","soundcloud_client_id","behance_api_key","google_api","instagram_sessionid","dribbble_client_id","dribbble_client_secret","dribbble_access_token","twitter_consumer_key","twitter_consumer_secret","envato_token","site_key_recaptcha","secret_key_recaptcha");
			$permissions_values = array("ask_question","add_poll","ask_other_users","show_question","approve_question","approve_question_media","add_category","add_answer","show_answer","approve_answer","approve_answer_media","add_group","approve_group","add_post","show_post","approve_post","show_comment","approve_comment","add_comment","send_message","upload_files","pay_to_user","pay_maximum");
			$array_options = array(wpqa_options,"sidebars");
			foreach ($array_options as $option) {
				if (isset($data[$option])) {
					$data[$option]["login_slug"] = "log-in";
					$data[$option]["paypal_email"] = $admin_email;
					$data[$option]["paypal_email_sandbox"] = $admin_email;
					$data[$option]["email_template_to"] = $admin_email;
					$data[$option]["email_template"] = "no_reply@".$parse['host'];
					$data[$option]["black_list_emails"] = array();
					$data[$option]["application_splash_screen"]["id"] = $data[$option]["application_splash_screen"]["url"] = $data[$option]["application_icon"]["id"] = $data[$option]["application_icon"]["url"] = "";
					foreach ($empty_values as $value) {
						$data[$option][$value] = "";
					}
					$data[$option]["user_meta_avatar"] = "your_avatar";
					if (isset($data[$option]["roles"]) && is_array($data[$option]["roles"]) && !empty($data[$option]["roles"])) {
						foreach ($data[$option]["roles"] as $roles_key => $roles_value) {
							$is_group = get_role($roles_value["id"]);
							foreach ($permissions_values as $permission) {
								$data[$option]["roles"][$roles_key][$permission] = "on";
								if (isset($is_group)) {
									$is_group->add_cap($permission);
								}
							}
						}
					}
					update_option($option,$data[$option]);
				}else {
					delete_option($option);
				}
			}
			update_option("FlushRewriteRules",true);
		}
	}

	// Assign menus to their locations.
	$profile_menus = array("header_profile_menu" => get_term_by('name','Header Profile Menu','nav_menu'),"profile_page_menu" => get_term_by('name','Profile Page Tabs','nav_menu'));
	if (has_himer() || has_knowly()) {
		$menus = array("first" => get_term_by('name','Header Unlogged','nav_menu'),"second" => get_term_by('name','Header Logged','nav_menu'));
		$menus = array_merge($menus,$profile_menus);
		set_theme_mod('nav_menu_locations',array('header_menu' => $menus["first"]->term_id,'header_menu_login' => $menus["second"]->term_id,'header_profile_menu' => $menus["header_profile_menu"]->term_id,'profile_page_menu' => $menus["profile_page_menu"]->term_id));
	}else if (has_discy()) {
		$menus = array("first" => get_term_by('name','EXPLORE not login','nav_menu'),"second" => get_term_by('name','EXPLORE','nav_menu'),"third" => get_term_by('name','Header','nav_menu'),"fourth" => get_term_by('name','Header login','nav_menu'));
		$menus = array_merge($menus,$profile_menus);
		set_theme_mod('nav_menu_locations',array('discy_explore' => $menus["first"]->term_id,'discy_explore_login' => $menus["second"]->term_id,'header_menu' => $menus["third"]->term_id,'header_menu_login' => $menus["fourth"]->term_id,'header_profile_menu' => $menus["header_profile_menu"]->term_id,'profile_page_menu' => $menus["profile_page_menu"]->term_id));
	}

	if (isset($menus) && is_array($menus) && !empty($menus)) {
		foreach ($menus as $key_menu => $value_menu) {
			if (isset($value_menu->term_id)) {
				$array_menu = wp_get_nav_menu_items($value_menu->term_id);
				if (has_himer()) {
					$own_url = array('https://2code.info/demo/themes/Himer/main/','https://2code.info/demo/themes/Himer/rtl/','https://2code.info/demo/themes/Himer/nogender/','https://2code.info/demo/themes/Himer/leftmenu/');
				}else if (has_discy()) {
					$own_url = array('https://2code.info/demo/themes/Discy/Main/','https://2code.info/demo/themes/Discy/Try/','https://2code.info/demo/themes/Discy/RTL/','https://2code.info/demo/themes/Discy/Boxed/');
				}
				if (is_array($array_menu) && !empty($array_menu)) {
					foreach ($array_menu as $key => $value) {
						foreach ($own_url as $url) {
							if (strpos($value->url,$url) !== false) {
								update_post_meta($value->ID,'_menu_item_url',str_ireplace($own_url,esc_url(home_url('/')),$value->url));
							}
						}
					}
				}
			}
		}
	}

	// Assign front page and posts page (blog page).
	$front_page_id = get_page_by_path('home',OBJECT,'page');
	update_option('show_on_front','page');
	if (isset($front_page_id->ID)) {
		update_option('page_on_front',$front_page_id->ID);
		if (has_himer() || has_knowly()) {
			update_option("home_page_id",$front_page_id->ID);
			update_option("tabs_menu_select","second_menu");
		}
	}

	// Delete default wordpress data
	$hello_post_id = get_page_by_path('hello-world!',OBJECT,'post');
	$hello_post_id_ar = get_page_by_path('أهلاً-بالعالم',OBJECT,'post');

	// remove hello world post
	if (isset($hello_post_id->ID)) {
		wp_delete_post($hello_post_id->ID,true);
	}

	// remove hello world post
	if (isset($hello_post_id_ar->ID)) {
		wp_delete_post($hello_post_id_ar->ID,true);
	}

	$sample_page_id = get_page_by_path('sample-page',OBJECT,'page');
	$sample_page_id_ar = get_page_by_path('مثال-على صفحة',OBJECT,'page');

	// remove sample page
	if (isset( $sample_page_id->ID)) {
		wp_delete_post($sample_page_id->ID,true);
	}

	// remove sample page
	if (isset($sample_page_id_ar->ID)) {
		wp_delete_post($sample_page_id_ar->ID,true);
	}

	if (has_himer() || has_knowly()) {
		$sickty_name = "what-five-marvel-characters-do-you-choose-to-ensure-your-safety";
	}else if (has_discy()) {
		$sickty_name = "is-this-statement-i-see-him-last-night-can-be-understood-as-i-saw-him-last-night";
	}
	if (isset($sickty_name)) {
		$sticky_post_id = get_page_by_path($sickty_name,OBJECT,wpqa_questions_type);
		if (isset($sticky_post_id->ID)) {
			$post_id = $sticky_post_id->ID;
			update_post_meta($post_id,"sticky",1);
			$sticky_posts = get_option('sticky_posts');
			if (is_array($sticky_posts)) {
				if (!in_array($post_id,$sticky_posts)) {
					$array_merge = array_merge($sticky_posts,array($post_id));
					update_option("sticky_posts",$array_merge);
				}
			}else {
				update_option("sticky_posts",array($post_id));
			}
			$sticky_questions = get_option('sticky_questions');
			if (is_array($sticky_questions)) {
				if (!in_array($post_id,$sticky_questions)) {
					$array_merge = array_merge($sticky_questions,array($post_id));
					update_option("sticky_questions",$array_merge);
				}
			}else {
				update_option("sticky_questions",array($post_id));
			}
		}
	}
}
/* Header in the demo page */
add_action("pt-ocdi/plugin_page_header","wpqa_plugin_page_header");
function wpqa_plugin_page_header() {
	echo '<div id="framework-registration-wrap" class="framework-demos-container"><div class="framework-dash-container framework-dash-container-medium"><div class="postbox"><h2><span class="dashicons dashicons-yes library-icon-key"></span><span>'.esc_html__('Choose the demo which you want to import','wpqa').'</span></h2><div class="inside"><div class="main">';
}
/* Footer in the demo page */
add_action("pt-ocdi/plugin_page_footer","wpqa_plugin_page_footer");
function wpqa_plugin_page_footer() {
	echo '</div></div></div></div></div>';
}
/* Title in the demo page */
add_filter('pt-ocdi/plugin_intro_text','wpqa_plugin_page_title');
add_filter("pt-ocdi/plugin_page_title","wpqa_plugin_page_title");
function wpqa_plugin_page_title() {
	echo '';
}?>