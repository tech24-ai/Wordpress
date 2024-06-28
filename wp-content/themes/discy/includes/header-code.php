<?php do_action("discy_action_before_header");
$activate_header = discy_options("activate_header");
$activate_header = ($activate_header == ""?"on":$activate_header);
$activate_register = discy_options("activate_register");
$activate_login = discy_options("activate_login");
$loader_option = discy_options("loader");
$mobile_bar = discy_options("mobile_bar");
$header_responsive_icon = discy_options("header_responsive_icon");
$mobile_sign = discy_options("mobile_sign");
$header_boxed = discy_options("header_boxed");
$header_height = discy_options("header_height");
$header_style = discy_options("header_style");
$header_skin = discy_options("header_skin");
$header_fixed = discy_options("header_fixed");
$search_value = apply_filters("wpqa_get_search_filter",false);
$header_search = discy_options("header_search");
$big_search = discy_options("big_search");
$live_search = discy_options('live_search');
$header_user_login = discy_options("header_user_login");
$user_login_style = discy_options("user_login_style");
$active_moderators = discy_options("active_moderators");
$header_notifications = discy_options("header_notifications");
$active_notifications = discy_options("active_notifications");
$notifications_style = discy_options("notifications_style");
$active_activity_log = discy_options("active_activity_log");
$active_referral = discy_options("active_referral");
$active_message = discy_options("active_message");
$header_messages = discy_options("header_messages");
$messages_style = discy_options("messages_style");
$active_points = discy_options("active_points");
$discoura_style = discy_options("discoura_style");
$custom_main_menu = discy_post_meta("custom_main_menu");
if ((is_single() || is_page()) && $custom_main_menu == "custom") {
	$main_menu = discy_post_meta("main_menu");
}
$custom_page_setting = discy_post_meta("custom_page_setting");
if ((is_single() || is_page()) && isset($custom_page_setting) && $custom_page_setting == "on") {
	$breadcrumbs = discy_post_meta("breadcrumbs");
}else {
	$breadcrumbs = discy_options("breadcrumbs");
}
$breadcrumbs_style = discy_options("breadcrumbs_style");
if ($discoura_style == "on") {
	$header_style = "simple";
	$header_skin = "light";
}?>
<div class="background-cover"></div>
<?php if ($loader_option == "on") {?>
	<div class="loader"><i class="loader_html fa-spin"></i></div>
<?php }
$user_id = get_current_user_id();
$wpqa_profile_url = (has_wpqa() && $user_id > 0?wpqa_profile_url($user_id):"");
$is_super_admin = is_super_admin($user_id);

if (has_wpqa()) {
	$tax_filter   = apply_filters("discy_before_question_category",false);
	$tax_question = apply_filters("discy_question_category",wpqa_question_categories);
	$category_id  = "";
	if (is_category() || is_single() || is_tax(wpqa_question_categories) || $tax_filter == true) {
		if (is_tax(wpqa_question_categories) || $tax_filter == true) {
			$tax_id = get_term_by('slug',get_query_var('term'),$tax_question);
			$category_id = (isset($tax_id->term_id)?$tax_id->term_id:"");
		}else if (is_category()) {
			$category_id = esc_html(get_query_var('cat'));
		}else if (is_single()) {
			if (is_singular(wpqa_questions_type)) {
				$get_category = get_the_terms(get_the_ID(),wpqa_question_categories);
			}else {
		    	$get_category = get_the_category(get_the_ID());
		    }
		    if (is_array($get_category) && isset($get_category[0]) && isset($get_category[0]->term_id)) {
		    	$category_single_id = $get_category[0]->term_id;
		    	$custom_logo = discy_term_meta("custom_logo",$category_single_id);
		    	if (isset($custom_logo) && $custom_logo == "on") {
		    		$logo_single = discy_term_meta("logo_single",$category_single_id);
		    		if ($logo_single == "on") {
		    			$category_id = $category_single_id;
		    		}
		    	}
		    }
		}
		$custom_logo = discy_term_meta("custom_logo",$category_id);
		if ($custom_logo == "on") {
			$logo_display = discy_term_meta("logo_display",$category_id);
			$logo_img     = discy_image_url_id(discy_term_meta("logo_img",$category_id));
			$retina_logo  = discy_image_url_id(discy_term_meta("retina_logo",$category_id));
			$logo_height  = discy_term_meta("logo_height",$category_id);
			$logo_width   = discy_term_meta("logo_width",$category_id);
		}
	}
}

/* Head content */
do_action("wpqa_head_content");
$confirm_email = (has_wpqa()?wpqa_users_confirm_mail():"");

$mobile_bar_apps = discy_options("mobile_bar_apps");
$mobile_apps_bar_skin = discy_options("mobile_apps_bar_skin");
$mobile_bar_apps_iphone = discy_options("mobile_bar_apps_iphone");
$mobile_bar_apps_android = discy_options("mobile_bar_apps_android");

$logged_only = discy_post_meta("logged_only");

$gender_class = '';
if (is_user_logged_in()) {
	$is_user_logged_in = true;
	$gender = get_the_author_meta('gender',$user_id);
	$gender_class = ($gender !== ''?($gender == "male" || $gender == 1?"male":"").($gender == "female" || $gender == 2?"female":"").($gender == "other" || $gender == 3?"other":""):'');
}

if ($activate_header == "on") {
	$header_classes = array();
	if ($header_fixed == "on") {
		$header_classes[] = 'fixed-enabled';
	}
	if (is_array($header_classes) && !empty($header_classes)) {
		$header_classes = " ".implode(" ",$header_classes);
	}
}?>

<div id="wrap" class="<?php echo (isset($is_user_logged_in)?"wrap-login":"wrap-not-login").(isset($header_classes) && !is_array($header_classes) && $header_classes != ""?$header_classes:"").($logged_only == "on"?" wrap-logged-only":"").($gender_class != ""?" wrap-gender-".$gender_class:"").apply_filters("discy_filter_wrap_css","")?>"<?php echo (has_wpqa() && (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type))?' itemscope itemtype="https://schema.org/QAPage"':'')?>>
	<?php if ($activate_header == "on") {?>
		<div class="hidden-header<?php echo ($header_search != "on"?" header-no-search":"").($big_search == "on" && $header_search == "on"?" header-big-search":"").($header_style == "simple"?" header-simple":"").($header_height == "small"?" header-2":"")." header-".$header_skin.($confirm_email != "yes" && $mobile_bar == "on"?" mobile_bar_active":"").($mobile_bar_apps == "on" && ($mobile_bar_apps_iphone != "" || $mobile_bar_apps_android != "")?" mobile_apps_bar_active":"")?>">
			<?php $mobile_bar_layout = "top";
			include locate_template("header-parts/mobile-bar.php");?>
			<header class="header" itemscope="" itemtype="https://schema.org/WPHeader">
				<div class="the-main-container header-container">
					<div class="mobile-menu">
						<div class="mobile-menu-click" data-menu="mobile-menu-main">
							<i class="icon-menu"></i>
						</div>
					</div><!-- End mobile-menu -->
					<?php if ($header_style == "simple") {
						include locate_template("header-parts/logo.php");
					}?>
					<div class="right-header float_r">
						<?php if (has_wpqa() && !isset($is_user_logged_in)) {
							if ($activate_register != "disabled" && $mobile_sign == "signup") {
								$title_mobile_button = esc_html__("Sign Up","discy");
								$class_mobile_button = "up";
								$hook_mobile_button = "signup";
								$url_mobile_button = wpqa_signup_permalink();
							}else if (has_wpqa() && $activate_login != 'disabled') {
								$title_mobile_button = esc_html__("Sign In","discy");
								$class_mobile_button = "in";
								$hook_mobile_button = "login";
								$url_mobile_button = wpqa_login_permalink();
							}
							if (isset($title_mobile_button) && isset($class_mobile_button) && isset($hook_mobile_button) && isset($url_mobile_button)) {?>
								<a title="<?php echo esc_attr($title_mobile_button)?>" class="sign-in-lock mob-sign-<?php echo esc_attr($class_mobile_button).apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_'.esc_attr($hook_mobile_button),'')?>" href="<?php echo esc_url($url_mobile_button)?>" data-toggle="modal"><i class="<?php echo ($header_responsive_icon != ""?$header_responsive_icon:"icon-lock")?>"></i></a>
							<?php }
							if ($activate_login != 'disabled') {?><a class="button-default button-sign-in<?php echo apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_login','')?>" href="<?php echo (has_wpqa()?wpqa_login_permalink():"#")?>" data-toggle="modal"><?php esc_html_e('Sign In','discy')?></a><?php }if ($activate_register != "disabled") {?><a class="button-default<?php echo ($header_skin == "colored"?"":"-2")?> button-sign-up<?php echo apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_signup','')?>" href="<?php echo (has_wpqa()?wpqa_signup_permalink():"#")?>"><?php esc_html_e('Sign Up','discy')?></a><?php }?>
						<?php }else {
							$header_profile_menu = 'header_profile_menu';
							$locations = get_nav_menu_locations();
							if (isset($locations[$header_profile_menu])) {
						        $header_profile_items = wp_get_nav_menu_items($locations[$header_profile_menu]);
						        if (is_array($header_profile_items) && !empty($header_profile_items)) {
									foreach ($header_profile_items as $menu_key => $menu_value) {
										if (isset($menu_value->url) && $menu_value->url == "#wpqa-notifications") {
											$wpqa_notifications_item = true;
										}else if (isset($menu_value->url) && $menu_value->url == "#wpqa-messages") {
											$wpqa_messages_item = true;
										}else if (isset($menu_value->url) && $menu_value->url == "#wpqa-pending-questions") {
											$wpqa_pending_questions_item = true;
										}else if (isset($menu_value->url) && $menu_value->url == "#wpqa-pending-posts") {
											$wpqa_pending_posts_item = true;
										}else if (isset($menu_value->url) && $menu_value->url == "#wpqa-followers_questions") {
											$wpqa_followers_questions_item = true;
										}
									}
								}
							}
							if (!isset($locations[$header_profile_menu])) {
								$user_link_only = true;
							}?>
							<div class="user-login-area<?php echo ($active_notifications == "on"?"":" no-notifications")?>">
								<?php if (has_wpqa() && $header_notifications == "on" && $active_notifications == "on" && $discoura_style != "on") {?>
									<div class="notifications-area user-notifications<?php echo ($notifications_style == "dark"?" user-notifications-2":"")?> float_r">
										<span class="notifications-click notifications--click"></span>
										<i class="icon-bell"></i>
										<?php $notifications_number = discy_options("notifications_number");
										echo wpqa_get_notifications($user_id,$notifications_number,"on",true)?>
									</div><!-- End user-notifications -->
								<?php }
								if (has_wpqa() && $header_messages == "on" && $active_message == "on" && $discoura_style != "on" && $header_style == "simple") {?>
									<div class="user-messages user-notifications<?php echo ($messages_style == "dark"?" user-notifications-2":"")?> float_r">
										<span class="notifications-click messages-click"></span>
										<i class="icon-mail"></i>
										<?php $messages_number = discy_options("messages_number");
										echo wpqa_get_messages($user_id,$messages_number,"on",true)?>
									</div><!-- End user-messages -->
								<?php }
								
								if ($header_user_login == "on") {
									$display_name = get_the_author_meta('display_name',$user_id);
									if (has_wpqa() && $active_message == "on" && isset($wpqa_messages_item)) {
										$num_message = (int)(isset($user_id)?get_user_meta($user_id,"wpqa_new_messages_count",true):0);
									}
									$num_message = (isset($num_message) && $num_message != "" && $num_message > 0?$num_message:0);
									if (isset($wpqa_notifications_item)) {
										$num_notification = (int)get_user_meta($user_id,$user_id.'_new_notification',true);
									}
									$num_notification = (isset($num_notification) && $num_notification > 0?$num_notification:0);
									if (has_wpqa() && ($is_super_admin || $active_moderators == "on") && (isset($wpqa_pending_questions_item) || isset($wpqa_pending_posts_item))) {
										$user_moderator = get_user_meta($user_id,prefix_author."user_moderator",true);
										if ($is_super_admin || $user_moderator == "on") {
											$moderator_categories = get_user_meta($user_id,prefix_author."moderator_categories",true);
											if ($is_super_admin || (is_array($moderator_categories) && !empty($moderator_categories))) {
												$num_pending_questions = $num_pending_posts = 0;
												if (isset($wpqa_pending_questions_item)) {
													if ($is_super_admin || in_array("q-0",$moderator_categories)) {
														$num_pending_questions = wpqa_count_posts_by_type(array(wpqa_questions_type,wpqa_asked_questions_type),"draft");
													}else {
														$moderator_categories_questions = wpqa_remove_item_by_value($moderator_categories,"p-0");
														if (is_array($moderator_categories_questions) && !empty($moderator_categories_questions)) {
															$num_pending_questions = wpqa_count_posts_by_user(0,wpqa_questions_type,"draft",$moderator_categories_questions);
														}
													}
												}
												if (isset($wpqa_pending_posts_item)) {
													if ($is_super_admin || in_array("p-0",$moderator_categories)) {
														$num_pending_posts = wpqa_count_posts_by_type("post","draft");
													}else {
														$moderator_categories_posts = wpqa_remove_item_by_value($moderator_categories,"q-0");
														if (is_array($moderator_categories_posts) && !empty($moderator_categories_posts)) {
															$num_pending_posts = wpqa_count_posts_by_user(0,"post","draft",$moderator_categories_posts);
														}
													}
												}
												$num_pending = $num_pending_questions+$num_pending_posts;
											}
										}
									}
									$num_pending = (isset($num_pending) && $num_pending != "" && $num_pending > 0?$num_pending:0);
									$num_all = $num_message+$num_notification+$num_pending;
									$num_all = ($num_all != "" && $num_all > 0?($num_all <= 99?$num_all:"99+"):"");?>
									<div class="user-login-click<?php echo ($user_login_style == "dark"?" user-login-click-2":"").(isset($wpqa_followers_questions_item)?" user-login-bigger":"")?> float_r">
										<?php if (!isset($user_link_only)) {?>
											<span class="user-click<?php echo apply_filters("discy_user_click_class",false)?>"<?php echo apply_filters("discy_user_click_attr",false)?>></span>
										<?php }else {?>
											<a href="<?php echo (isset($wpqa_profile_url)?$wpqa_profile_url:"")?>" class="user-click"></a>
										<?php }?>
										<div class="user-image float_l">
											<?php echo ($num_all != ""?'<span class="notifications-number">'.wpqa_count_number($num_all).'</span>':'');
											$user_avatar_header = apply_filters("discy_user_avatar_header",true);
											if ($user_avatar_header == true) {
												do_action("wpqa_user_avatar",array("user_id" => $user_id,"size" => apply_filters("discy_header_profile_size","29"),"name" => $display_name));
											}else {
												do_action("discy_action_user_avatar_header",$user_id);
											}?>
										</div>
										<div class="user-login float_l">
											<span><?php echo esc_html_e("Welcome","discy")?></span><br>
											<div class="float_l"><?php echo esc_html($display_name)?></div>
										</div>
										<?php include locate_template("header-parts/header-actions.php");?>
									</div><!-- End user-login-click -->
								<?php }?>
							</div><!-- End user-login-area -->
						<?php }
						if (has_wpqa() && isset($is_user_logged_in) && $header_style == "simple") {
							$header_button = discy_options("header_button");
							$header_button = ($header_button != ""?$header_button:"question");
							if ($header_button == "question") {
								$filter_class = "question";
								$header_button_class = "wpqa-question";
								$header_button_link = (has_wpqa()?wpqa_add_question_permalink():"#");
								$header_button_text = esc_html__("Ask Question","discy");
							}else if ($header_button == "poll") {
								$filter_class = "question";
								$header_button_class = "wpqa-question";
								$header_button_link = (has_wpqa()?esc_url_raw(add_query_arg(array("type" => "poll"),wpqa_add_question_permalink())):"#");
								$header_button_text = esc_html__("Add Poll","discy");
							}else if ($header_button == "post") {
								$filter_class = "post";
								$header_button_class = "wpqa-post";
								$header_button_link = (has_wpqa()?wpqa_add_post_permalink():"#");
								$header_button_text = esc_html__("Add Post","discy");
							}else {
								$filter_class = $header_button_class = "";
								$header_button_target = discy_options("header_button_target");
								$header_button_link = discy_options("header_button_link");
								$header_button_text = discy_options("header_button_text");
							}
							$header_button_target = ($header_button == "custom" && isset($header_button_target) && $header_button_target == "new_page"?"_blank":"_self");?>
							<a target="<?php echo esc_attr($header_button_target)?>" class="button-default simple-header-button <?php echo esc_attr($header_button_class)?> <?php echo apply_filters('wpqa_pop_up_class','').(isset($filter_class) && $filter_class != ''?apply_filters('wpqa_pop_up_class_'.$filter_class,''):'')?>" href="<?php echo esc_url($header_button_link)?>"><?php echo esc_html($header_button_text)?></a>
						<?php }?>
					</div><!-- End right-header -->
					<div class="left-header float_l">
						<h2 class="screen-reader-text site_logo"><?php echo esc_html(get_bloginfo('name','display'))?></h2>
						<?php if ($header_style != "simple") {
							include locate_template("header-parts/logo.php");
						}?>
						<div class="mid-header float_l">
							<?php do_action("discy_action_before_search");
							if ($header_search == "on") {
								$show_search = apply_filters("discy_show_search",true);?>
								<div class="header-search float_r">
									<?php if ($show_search == true) {?>
										<form role="search" class="searchform main-search-form" method="get" action="<?php do_action("wpqa_search_permalink")?>">
											<div class="search-wrapper">
												<input type="search"<?php echo ($live_search == "on"?" class='live-search live-search-icon' autocomplete='off'":"")?> placeholder="<?php esc_attr_e('Type Search Words','discy')?>" name="search" value="<?php echo do_action("wpqa_get_search")?>">
												<?php if ($live_search == "on") {?>
													<div class="loader_2 search_loader"></div>
													<div class="search-results results-empty"></div>
												<?php }?>
												<input type="hidden" name="search_type" class="search_type" value="<?php do_action("wpqa_search_type")?>">
												<div class="search-click"></div>
												<button type="submit" aria-label="<?php esc_attr_e('Search','discy')?>"><i class="icon-search"></i></button>
											</div>
										</form>
									<?php }?>
								</div><!-- End header-search -->
							<?php }
							do_action("discy_action_after_search");
							include locate_template("header-parts/navbar-nav.php");?>
						</div><!-- End mid-header -->
					</div><!-- End left-header -->
				</div><!-- End the-main-container -->
			</header><!-- End header -->
			<?php $mobile_bar_layout = "bottom";
			include locate_template("header-parts/mobile-bar.php");?>
		</div><!-- End hidden-header -->
		<?php include locate_template("header-parts/mobile-menu.php");
	}

	do_action("discy_action_before_slider");

	$silder_file = apply_filters("discy_filter_silder_file",true);
	if ($silder_file == true) {
		include locate_template("header-parts/slider.php");
	}

	do_action("discy_action_after_slider");

	$filter_call_action = apply_filters("discy_filter_call_action",true);
	if ($filter_call_action == true) {
		include locate_template("header-parts/call-action.php");
	}

	$filter_cover_image = apply_filters("discy_filter_cover_image",true);
	if ($filter_cover_image == true) {
		do_action("wpqa_cover_image");
	}

	$filter_category_cover = apply_filters("discy_filter_category_cover",true);
	if ($filter_category_cover == true) {
		do_action("wpqa_category_cover");
	}

	$filter_group_cover = apply_filters("discy_filter_group_cover",true);
	if ($filter_group_cover == true) {
		do_action("wpqa_group_cover");
	}

	$blog_h_where = discy_options("blog_h_where");
	if ($blog_h_where == "header") {
		include locate_template("includes/blog-header-footer.php");
	}
	$wpqa_sidebars = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebars():"main_sidebar main_right col-boot-lg-8");
	$hide_sidebar_mobile = discy_options("hide_sidebar_mobile");?>
	<div class="main-content">
		<div class="discy-inner-content <?php echo ($confirm_email == "yes" || $site_users_only == "yes"?"main_full":$wpqa_sidebars).($hide_sidebar_mobile == "on"?" hide-mobile-sidebar":"")?>">
			<?php do_action("discy_after_inner_content");
			$update_profile = (has_wpqa() && isset($is_user_logged_in)?wpqa_update_profile($user_id):"");
			$cover_image = discy_options("cover_image");
			if (has_wpqa() && $cover_image == "on" && wpqa_is_user_profile() && !wpqa_is_user_owner()) {
				$hide_right_breadcrumb = true;
			}
			/* Adv */
			$adv_404 = discy_options("adv_404");
			if (is_404() && $adv_404 == "on") {
				$adv_404 = "on";
			}else {
				$adv_404 = "";
			}
			if ($breadcrumbs_style == "style_2") {
				if (!is_home() && !is_front_page() && isset($breadcrumbs) && $breadcrumbs == "on" && $confirm_email != "yes" && $site_users_only != "yes") {
					discy_breadcrumbs(($update_profile == "yes"?esc_html__("Edit profile","discy"):""),($update_profile == "yes" || (isset($hide_right_breadcrumb))?false:true),$breadcrumbs_style);
				}
				/* Header adv */
				if (has_wpqa() && wpqa_plugin_version >= "5.8" && (($adv_404 != "on" && is_404()) || !is_404())) {
					echo wpqa_ads("header_adv_type_1","header_adv_link_1","header_adv_code_1","header_adv_href_1","header_adv_img_1","","on","aalan-header","on");
				}
			}?>
			<div class="the-main-container the-wrap-container">
				<main class="all-main-wrap discy-site-content float_l">
					<div class="the-main-inner float_l">
						<?php do_action("discy_after_discy_main_inner");
						if ($breadcrumbs_style != "style_2" && !is_home() && !is_front_page() && isset($breadcrumbs) && $breadcrumbs == "on" && $confirm_email != "yes" && $site_users_only != "yes") {
							discy_breadcrumbs(($update_profile == "yes"?esc_html__("Edit profile","discy"):""),($update_profile == "yes" || (isset($hide_right_breadcrumb))?false:true),$breadcrumbs_style);
						}
						if (has_wpqa()) {
							/* Session */
							do_action("wpqa_show_session");
							/* Check the user account */
							wpqa_check_user_account(true,true);
							/* Header content */
							do_action("wpqa_header_content",array("user_id" => (isset($user_id)?$user_id:0),"update_profile" => $update_profile));
							if (has_wpqa() && wpqa_plugin_version < "5.9.8" && is_super_admin(isset($user_id)?$user_id:0) && defined("wpqa_new_plugin_version") && wpqa_plugin_version != wpqa_new_plugin_version) {?>
								<div class="alert-message alert-message-error">
									<i class="icon-cancel"></i>
									<p>
										<span><?php esc_html_e("Required update plugin:","discy")?> <em>WPQA - The WordPress Questions And Answers Plugin</em>.</span>
										<span><a href="<?php echo admin_url('admin.php?page=install-required-plugins&plugin_status=update')?>"><?php echo esc_html_x("Begin updating plugin","Update plugin link","discy")?></a></span>
									</p>
								</div>
							<?php }
						}
						if ($breadcrumbs_style != "style_2") {
							/* Header adv */
							if (has_wpqa() && wpqa_plugin_version >= "5.8" && (($adv_404 != "on" && is_404()) || !is_404())) {
								echo wpqa_ads("header_adv_type_1","header_adv_link_1","header_adv_code_1","header_adv_href_1","header_adv_img_1","","on","aalan-header","on");
							}
						}?>
						<div class="clearfix"></div>