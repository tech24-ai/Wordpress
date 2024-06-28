<?php
update_option( 'discy_ep_license_status', 'valid' );
update_option( 'discy_ep_license_key', 'license_key' );
if (is_admin() && isset($_GET['activated']) && $pagenow == "themes.php") {
	flush_rewrite_rules(true);
	wp_redirect(admin_url('admin.php?page=registration'));
	exit;
}
define('discy_framework_dir',get_template_directory_uri().'/admin/');

/* Require files */
require_once 'custom-functions.php';

require_once locate_template("admin/plugins/class-tgm-plugin-activation.php");
require_once locate_template("admin/plugins/plugins.php");
if (is_admin() && (isset($_GET['page']) && $_GET['page'] == "options") && ($pagenow == "admin.php" || $pagenow == "themes.php")) {
	require_once locate_template("admin/options.php");
}
require_once locate_template("admin/widgets.php");
require_once locate_template("admin/functions/main_functions.php");
require_once locate_template("admin/functions/resizer.php");
require_once locate_template("admin/functions/widget_functions.php");
require_once locate_template("admin/functions/nav_menu.php");
require_once locate_template("admin/functions/register_post.php");

/* Updater */
require_once get_template_directory().'/admin/updater/elitepack-config.php';

/* Widgets */
include locate_template("admin/widgets/about.php");
include locate_template("admin/widgets/adv-120x240.php");
include locate_template("admin/widgets/adv-120x600.php");
include locate_template("admin/widgets/adv-125x125.php");
include locate_template("admin/widgets/adv-234x60.php");
include locate_template("admin/widgets/adv-250x250.php");
include locate_template("admin/widgets/facebook.php");
include locate_template("admin/widgets/social.php");
include locate_template("admin/widgets/subscribe.php");
include locate_template("admin/widgets/video.php");

/* Body classes */
add_filter('body_class','discy_body_classes');
if (!function_exists('discy_body_classes')) {
	function discy_body_classes($classes) {
		if (is_single() || is_page()) {
			$custom_page_setting = discy_post_meta("custom_page_setting");
			if (has_wpqa() && (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type))) {
				$question_answers = discy_options("question_answers");
				if ($custom_page_setting == "on") {
					$question_answers = discy_post_meta("post_comments");
				}
				
				if ((comments_open() || get_comments_number()) && $question_answers == "on") {
					// Answers
				}else {
					$classes[] = 'question-no-answers';
				}
			}
			
			if (isset($custom_page_setting) && $custom_page_setting == "on") {
				$breadcrumbs = discy_post_meta("breadcrumbs");
			}else {
				$breadcrumbs = discy_options("breadcrumbs");
			}
			
			$classes[] = ($breadcrumbs == "on"?"page-with-breadcrumbs":"page-no-breadcrumbs");
		}
		
		if ((is_page() || is_single()) && !is_home() && !is_front_page()) {
			$classes[] = 'single_page';
			if (!is_page_template()) {
				$classes[] = 'single_page_no';
			}
		}
		$site_users_only = (has_wpqa()?wpqa_site_users_only():"");
		$under_construction = (has_wpqa()?wpqa_under_construction():"");
		$wp_page_template = discy_post_meta("_wp_page_template","",false);
		$classes[] = ($wp_page_template == "template-landing.php" || $under_construction == "on" || $site_users_only == "yes"?"main_users_only":"main_for_all");
		$active_lightbox = discy_options("active_lightbox");
		if ($active_lightbox == "on") {
			$classes[] = 'active-lightbox';
		}
		
		$discoura_style = discy_options("discoura_style");
		if ($discoura_style == "on") {
			$classes[] = "discoura";
		}
		$site_style = discy_options("site_style");
		if ($site_style == "none") {
			$classes[] = "discy-not-boxed";
		}
		if ($site_style == "style_1" || $site_style == "style_2" || $site_style == "style_3" || $site_style == "style_4") {
			$classes[] = "discy-boxed";
		}
		if ($site_style == "style_1") {
			$classes[] = "discy-boxed-1";
		}
		if ($site_style == "style_2") {
			$classes[] = "discy-boxed-2";
		}
		if ($site_style == "style_3" || $site_style == "style_4") {
			$classes[] = "discy-boxed-3";
		}
		if ($site_style == "style_4") {
			$classes[] = "discy-boxed-4";
		}
		if ($site_style == "style_2" || $site_style == "style_4") {
			$classes[] = "discy-boxed-mix";
		}
		$site_width = discy_options("site_width");
		if ($site_width >= 1180) {
			$classes[] = "discy-custom-width";
		}
		$left_area = discy_options("left_area");
		if ($left_area == "sidebar") {
			$classes[] = "discy-left-sidebar";
		}
		$active_awesome = discy_options("active_awesome");
		if ($active_awesome == "on") {
			$classes[] = "activate-font-awesome";
		}
		return $classes;
	}
}
/* discy_fonts_url */
function discy_fonts_url() {
	$font_url = '';
	$show_fonts = apply_filters("discy_show_fonts",true);
	if ($show_fonts == true) {
		if ('off' !== _x('on','Google font: on or off','discy')) {
			$main_font   = discy_options("main_font");
			$second_font = discy_options("second_font");
			$earlyaccess_main = discy_earlyaccess_fonts($main_font["face"]);
			$earlyaccess_second = discy_earlyaccess_fonts($second_font["face"]);
			$safe_fonts  = array(
				'arial'      => 'Arial',
				'verdana'    => 'Verdana',
				'trebuchet'  => 'Trebuchet',
				'times'      => 'Times New Roman',
				'tahoma'     => 'Tahoma',
				'geneva'     => 'Geneva',
				'georgia'    => 'Georgia',
				'palatino'   => 'Palatino',
				'helvetica'  => 'Helvetica',
				'museo_slab' => 'Museo Slab'
			);
			if ((isset($second_font["face"]) && $earlyaccess_second != "earlyaccess" && (($second_font["face"] != "Default font" && $second_font["face"] != "default" && $second_font["face"] != "") || $second_font["face"] == "default" || $second_font["face"] == "Default font" || $second_font["face"] == "") && !in_array($second_font["face"],$safe_fonts)) || (isset($main_font["face"]) && $earlyaccess_main != "earlyaccess" && (($main_font["face"] != "Default font" && $main_font["face"] != "default" && $main_font["face"] != "") || $main_font["face"] == "default" || $main_font["face"] != "Default font" || $main_font["face"] == "") && !in_array($main_font["face"],$safe_fonts))) {
				$font_url = "https://fonts.googleapis.com/css2?family=".((is_rtl()?"Droid+Arabic+Kufi:wght@400;600;700&family=":"").(isset($second_font["face"]) && $second_font["face"] != "Default font" && $second_font["face"] != "default" && $second_font["face"] != ""?str_ireplace("+"," ",$second_font["face"]):'Open Sans').':wght@400;600;700&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese&amp;display=swap&family='.(isset($main_font["face"]) && $main_font["face"] != "Default font" && $main_font["face"] != "default" && $main_font["face"] != ""?str_ireplace("+"," ",$main_font["face"]):'Roboto').':wght@400;600;700&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese&amp;display=swap');
			}
		}
	}
	return $font_url;
}
/* discy_scripts_styles */
if (!function_exists('discy_scripts_styles')) {
	function discy_scripts_styles() {
		do_action("discy_scripts_styles");
		$search_type = (has_wpqa() && wpqa_is_search()?wpqa_search_type():"");
		wp_enqueue_style('discy-entypo',get_template_directory_uri().'/css/entypo/entypo.css');
		wp_enqueue_style('prettyPhoto',get_template_directory_uri().'/css/prettyPhoto.css');
		$active_awesome = discy_options("active_awesome");
		if ($active_awesome == "on") {
			wp_enqueue_style('discy-font-awesome',get_template_directory_uri( __FILE__ ).'/css/fontawesome/css/fontawesome-all.min.css');
		}
		wp_enqueue_style('discy-main-style',get_template_directory_uri().'/style.css','',null,'all');
		$main_font = discy_options("main_font");
		$second_font = discy_options("second_font");
		if (isset($main_font["face"])) {
			$earlyaccess_main = discy_earlyaccess_fonts($main_font["face"]);
			if ($earlyaccess_main == "earlyaccess") {
				$main_font_style = strtolower(str_replace("+","",$main_font["face"]));
				wp_enqueue_style('discy-'.$main_font_style,'https://fonts.googleapis.com/earlyaccess/'.$main_font_style.'.css');
			}else {
				$fonts_loaded = true;
				wp_enqueue_style('discy-fonts',discy_fonts_url(),array(),discy_theme_version);
			}
		}
		if (isset($second_font["face"])) {
			$earlyaccess_second = discy_earlyaccess_fonts($second_font["face"]);
			if ($earlyaccess_second == "earlyaccess") {
				$second_font_style = strtolower(str_replace("+","",$second_font["face"]));
				wp_enqueue_style('discy-'.$second_font_style,'https://fonts.googleapis.com/earlyaccess/'.$second_font_style.'.css');
			}else {
				if (!isset($fonts_loaded)) {
					wp_enqueue_style('discy-fonts',discy_fonts_url(),array(),discy_theme_version);
				}
			}
		}
		$discoura_style = discy_options("discoura_style");
		$site_style = discy_options("site_style");
		$active_groups = discy_options("active_groups");
		$activate_pay_to_users = discy_options("activate_pay_to_users");
		if (is_rtl()) {
			wp_enqueue_style('discy-basic-css',get_template_directory_uri().'/css/rtl-basic.css',array(),discy_theme_version);
			wp_enqueue_style('discy-main-css',get_template_directory_uri().'/css/rtl.css',array(),discy_theme_version);
			wp_enqueue_style('discy-vars-css',get_template_directory_uri().'/css/rtl-vars.css',array(),discy_theme_version);
			if (has_wpqa() && $active_groups == "on" && (wpqa_is_add_groups() || wpqa_is_edit_groups() || is_singular("group") || wpqa_is_view_posts_group() || wpqa_is_edit_posts_group() || wpqa_is_group_requests() || wpqa_is_group_users() || wpqa_is_group_admins() || wpqa_is_blocked_users() || wpqa_is_posts_group() || wpqa_is_user_groups() || wpqa_is_user_joined_groups() || wpqa_is_user_managed_groups() || is_post_type_archive("group") || is_page_template("template-groups.php") || $search_type == "groups")) {
				wp_enqueue_style('discy-groups-css',get_template_directory_uri().'/css/rtl-groups.css',array(),discy_theme_version);
			}
			if (has_wpqa() && $activate_pay_to_users == "on" && (wpqa_is_user_financial_profile() || wpqa_is_user_withdrawals_profile()) && wpqa_is_user_owner()) {
				wp_enqueue_style('discy-edit-css',get_template_directory_uri().'/css/rtl-edit.css',array(),discy_theme_version);
			}
			if ($discoura_style == "on") {
				wp_enqueue_style('discy-discoura-css',get_template_directory_uri().'/css/rtl-discoura.css',array(),discy_theme_version);
			}
			if ($site_style == "style_1" || $site_style == "style_2" || $site_style == "style_3" || $site_style == "style_4") {
				wp_enqueue_style('discy-boxed-css',get_template_directory_uri().'/css/rtl-boxed-style.css',array(),discy_theme_version);
			}
			wp_enqueue_style('discy-responsive',get_template_directory_uri()."/css/rtl-responsive.css",array(),discy_theme_version);
			wp_enqueue_style('discy-rtl',get_template_directory_uri().'/rtl.css',array(),discy_theme_version);
		}else {
			wp_enqueue_style('discy-basic-css',get_template_directory_uri().'/css/basic.css',array(),discy_theme_version);
			wp_enqueue_style('discy-main-css',get_template_directory_uri().'/css/main.css',array(),discy_theme_version);
			wp_enqueue_style('discy-vars-css',get_template_directory_uri().'/css/vars.css',array(),discy_theme_version);
			if (has_wpqa() && $active_groups == "on" && (wpqa_is_add_groups() || wpqa_is_edit_groups() || is_singular("group") || wpqa_is_view_posts_group() || wpqa_is_edit_posts_group() || wpqa_is_group_requests() || wpqa_is_group_users() || wpqa_is_group_admins() || wpqa_is_blocked_users() || wpqa_is_posts_group() || wpqa_is_user_groups() || wpqa_is_user_joined_groups() || wpqa_is_user_managed_groups() || is_post_type_archive("group") || is_page_template("template-groups.php") || $search_type == "groups")) {
				wp_enqueue_style('discy-groups-css',get_template_directory_uri().'/css/groups.css',array(),discy_theme_version);
			}
			if (has_wpqa() && $activate_pay_to_users == "on" && (wpqa_is_user_financial_profile() || wpqa_is_user_withdrawals_profile()) && wpqa_is_user_owner()) {
				wp_enqueue_style('discy-edit-css',get_template_directory_uri().'/css/edit.css',array(),discy_theme_version);
			}
			if ($discoura_style == "on") {
				wp_enqueue_style('discy-discoura-css',get_template_directory_uri().'/css/discoura.css',array(),discy_theme_version);
			}
			if ($site_style == "style_1" || $site_style == "style_2" || $site_style == "style_3" || $site_style == "style_4") {
				wp_enqueue_style('discy-boxed-css',get_template_directory_uri().'/css/boxed-style.css',array(),discy_theme_version);
			}
			wp_enqueue_style('discy-responsive',get_template_directory_uri()."/css/responsive.css",array(),discy_theme_version);
		}
		wp_enqueue_style('discy-dark-css',get_template_directory_uri().'/css/dark.css',array(),discy_theme_version);

		/* Custom CSS */

		$custom_css = apply_filters("discy_custom_inline_css","");

		$custom_css .= '@font-face {font-family:"entypo";font-display:swap;src:url('.get_template_directory_uri().'/css/entypo/entypo.woff2) format("woff2");font-weight:normal;font-style:normal;}';

		if (discy_options("header_fixed_responsive") == "on") {
			$custom_css .= '@media only screen and (max-width: 479px) {
				.header.fixed-nav {
					position: relative !important;
				}
			}';
		}
		
		$site_width = (int)discy_options("site_width");
		if ($site_width >= 1180) {
			$custom_css .= '@media (min-width: '.($site_width+30).'px) {
				.discy-custom-width .the-main-container,
				.discy-custom-width .main_center .the-main-inner,
				.discy-custom-width .main_center .hide-main-inner,
				.discy-custom-width .main_center main.all-main-wrap,
				.discy-custom-width .main_right main.all-main-wrap,
				.discy-custom-width .main_full main.all-main-wrap,
				.discy-custom-width .main_full .the-main-inner,
				.discy-custom-width .main_full .hide-main-inner,
				.discy-custom-width .main_left main.all-main-wrap {
					width: '.$site_width.'px;
				}
				.discy-custom-width main.all-main-wrap,.discy-custom-width .menu_left .the-main-inner,.discy-custom-width .menu_left .hide-main-inner {
					width: '.(970+$site_width-1170).'px;
				}
				.discy-custom-width.discy-boxed-3 .menu_left .the-main-inner {
					width: '.(970+$site_width-1170).'px !important;
				}
				.discy-custom-width .the-main-inner,.discy-custom-width .hide-main-inner {
					width: '.(691+$site_width-1170).'px;
				}
				.discy-custom-width .left-header {
					width: '.(890+$site_width-1170).'px;
				}
				.discy-custom-width .mid-header {
					width: '.((685+$site_width-1170)).'px;
				}
				.discy-custom-width .main_sidebar .hide-main-inner,.discy-custom-width .main_right .hide-main-inner,.discy-custom-width .main_right .the-main-inner,.discy-custom-width .main_left .the-main-inner,.discy-custom-width .main_left .hide-main-inner,.discy-custom-width .main_left .hide-main-inner {
					width: '.(891+$site_width-1170).'px;
				}
				.discy-custom-width.discy-left-sidebar .menu_sidebar main.all-main-wrap,.discy-custom-width.discy-left-sidebar .menu_left .the-main-inner,.discy-custom-width.discy-left-sidebar .menu_left .hide-main-inner,.discy-custom-width.discy-left-sidebar .menu_left main.all-main-wrap {
					width: '.((970+$site_width-1170)-30).'px;
				}
				.discy-custom-width.discy-left-sidebar .menu_sidebar .the-main-inner,.discy-custom-width.discy-left-sidebar .menu_sidebar .hide-main-inner,.discy-custom-width.discy-left-sidebar .menu_left .hide-main-inner {
					width: '.((691+$site_width-1170)-30).'px;
				}
				.discy-custom-width.discy-left-sidebar .menu_sidebar .mid-header,.discy-custom-width.discy-left-sidebar .menu_left .mid-header {
					width: '.((685+$site_width-1170)-30).'px;
				}
			}';
		}
		
		/* Fonts */
		
		if (isset($main_font["face"]) && $main_font["face"] != "default" && $main_font["face"] != "Default font" && $main_font["face"] != "") {
			$main_font["face"] = str_replace("+"," ",$main_font["face"]);
			$custom_css .= '
			h1,h2,h3,h4,h5,h6,.post-title,.post-title-2,.post-title-3,.widget-posts .user-notifications > div > ul li div h3,.related-widget .user-notifications > div > ul li div h3,.widget-posts .user-notifications > div > ul li div h3 a,.related-widget .user-notifications > div > ul li div h3 a,.accordion .accordion-title,.button-sign-in,.button-sign-up,nav.nav ul li,.menu-tabs > ul > li > a,.nav_menu > ul li a,.nav_menu > div > ul li a,.nav_menu > div > div > ul li a,.question-content-text,.widget-title,.widgettitle,.user-not-normal .user-area .user-content > .user-inner h4,.about-text,.widget li,.credits,.post-content-text,.button-default,.button-default-2,.button-default-3,a.meta-answer,.load-more a,.post-read-more,.question-read-less,.edit-link,.delete-link,.pop-footer,.post-contact form .form-input span,.pagination-wrap .no-comments,.user-follow-profile,.user-area .user-content > .user-inner p,.user-area .bio_editor,.category-description > p,.social-ul li .user_follow_3 a,.social-ul li.ban-unban-user a,.social-ul li.block-unblock-user a,.social-ul li a.block_message,.answers-tabs .section-title,.answers-tabs-inner li,.mobile-menu,.mobile-aside li a,.vote_result,.stats-value,.user-stats > ul > li > div > div span,.cat-sections a,.commentlist li.comment .comment-body .comment-text .comment-author a,.point-div span,.ed_button.poll_results,.ed_button.poll_polls,.comment-respond > .section-title,.fileinputs span,.no-results p,.post-author,.related-post .section-title,.navigation-content a,.alert-message,.wpqa-open-click,.question-author-un,.call-action-unlogged p,.panel-image-inner p {
				font-family: "'.$main_font["face"].'";
			}';
		}
		
		if (isset($second_font["face"]) && $second_font["face"] != "default" && $second_font["face"] != "Default font" && $second_font["face"] != "") {
			$second_font["face"] = str_replace("+"," ",$second_font["face"]);
			$custom_css .= '
			body,.section-title,textarea,input[type="text"],input[type="password"],input[type="datetime"],input[type="datetime-local"],input[type="date"],input[type="month"],input[type="time"],input[type="week"],input[type="number"],input[type="email"],input[type="url"],input[type="search"],input[type="tel"],input[type="color"],.post-meta,.article-question .post-meta,.article-question .footer-meta li,.badge-span,.widget .user-notifications > div > ul li a,.widget .user-notifications > ul li a,.users-widget .user-section-small .user-data ul li,.user-notifications > div > ul li span.notifications-date,.user-notifications > ul li span.notifications-date,.tagcloud a,.wp-block-tag-cloud a,.wpqa_form label,.wpqa_form .lost-password,.post-contact form p,.post-contact form .form-input,.follow-count,.progressbar-title span,.poll-num span,.social-followers,.notifications-number,.widget .widget-wrap .stats-inner li .stats-text,.breadcrumbs,.points-section ul li p,.progressbar-title,.poll-num,.badges-section ul li p {
				font-family: "'.$second_font["face"].'";
			}';
		}
		
		wp_enqueue_style('discy-custom-css',get_template_directory_uri().'/css/custom.css',array(),discy_theme_version);
		wp_add_inline_style('discy-custom-css',$custom_css);
		
		wp_enqueue_script("html5",get_template_directory_uri()."/js/html5.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("modernizr",get_template_directory_uri()."/js/modernizr.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("discy-flex-menu",get_template_directory_uri()."/js/flexMenu.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("discy-scrollbar",get_template_directory_uri()."/js/scrollbar.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("discy-theia",get_template_directory_uri()."/js/theia.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("discy-owl",get_template_directory_uri()."/js/owl.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("discy-match-height",get_template_directory_uri()."/js/matchHeight.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("discy-pretty-photo",get_template_directory_uri()."/js/prettyPhoto.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("discy-tabs",get_template_directory_uri()."/js/tabs.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("discy-tipsy",get_template_directory_uri()."/js/tipsy.js",array("jquery"),'1.0.0',true);
		wp_enqueue_script("discy-isotope",get_template_directory_uri()."/js/isotope.js",array("jquery"),'1.0.0',true);
		$captcha_style = discy_options("captcha_style");
		if ($captcha_style == "google_recaptcha") {
			$recaptcha_language = discy_options("recaptcha_language");
			wp_enqueue_script("discy-recaptcha", "https://www.google.com/recaptcha/api.js".($recaptcha_language != ""?"?hl=".$recaptcha_language:""),array("jquery"),'1.0.0',true);
		}
		wp_enqueue_script("discy-custom-js",get_template_directory_uri()."/js/custom.js",array("jquery","imagesloaded"),discy_theme_version,true);
		
		if (is_singular() && comments_open() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}
	}
}
add_action('wp_enqueue_scripts','discy_scripts_styles');
function discy_skin($skin) {
	if (!empty($skin) && $skin != "skin" && $skin != "default" && $skin != "") {
		wp_enqueue_style('discy-skin-'.$skin,get_template_directory_uri()."/css/skins/".$skin.".css",array(),discy_theme_version);
	}else if ($skin == "skin" || $skin == "default" || $skin == "") {
		wp_enqueue_style('discy-skin-default',get_template_directory_uri()."/css/skins/skins.css",array(),discy_theme_version);
	}
}
/* discy_load_theme */
if (!function_exists('discy_load_theme')) {
	function discy_load_theme() {
		/* Default RSS feed links */
		add_theme_support('automatic-feed-links');
		/* Post Thumbnails */
		add_theme_support('post-thumbnails');
		set_post_thumbnail_size(830, 550, true);
		set_post_thumbnail_size(330, 250, true);
		set_post_thumbnail_size(1080, 565, true);
		set_post_thumbnail_size(690, 430, true);
		set_post_thumbnail_size(360, 202, true);
		add_image_size('discy_img_1', 830, 550, array( 'center', 'top' ));
		add_image_size('discy_img_2', 330, 250, array( 'center', 'top' ));
		add_image_size('discy_img_3', 1080, 565, array( 'center', 'top' ));
		add_image_size('discy_img_4', 690, 430, array( 'center', 'top' ));
		add_image_size('discy_img_5', 360, 202, array( 'center', 'top' ));
		/* Valid HTML5 */
		add_theme_support('html5', array('search-form', 'comment-form', 'comment-list'));
		/* This theme uses its own gallery styles */
		add_filter('use_default_gallery_style', '__return_false');
		/* add title-tag */
		add_theme_support('title-tag');
		/* Load lang languages */
		load_theme_textdomain("discy",get_template_directory().'/languages');
		/* add post-thumbnails */
		add_theme_support('post-thumbnails');
	}
}
add_action('after_setup_theme','discy_load_theme');
/* wp head */
add_action('wp_head','discy_head');
if (!function_exists('discy_head')) {
	function discy_head() {
		if (!function_exists('wp_site_icon') || !has_site_icon()) {
			$default_favicon    = get_template_directory_uri()."/images/favicon.png";
			$favicon            = discy_image_url_id(discy_options("favicon"));
			$iphone_icon        = discy_image_url_id(discy_options("iphone_icon"));
			$iphone_icon_retina = discy_image_url_id(discy_options("iphone_icon_retina"));
			$ipad_icon          = discy_image_url_id(discy_options("ipad_icon"));
			$ipad_icon_retina   = discy_image_url_id(discy_options("ipad_icon_retina"));
			
			echo '<link rel="shortcut icon" href="'.esc_url((isset($favicon) && $favicon != ""?$favicon:$default_favicon)).'" type="image/x-icon">' ."\n";
		
			/* Favicon iPhone */
			if (isset($iphone_icon) && $iphone_icon != "") {
				echo '<link rel="apple-touch-icon-precomposed" href="'.esc_url($iphone_icon).'">' ."\n";
			}
		
			/* Favicon iPhone 4 Retina display */
			if (isset($iphone_icon_retina) && $iphone_icon_retina != "") {
				echo '<link rel="apple-touch-icon-precomposed" sizes="114x114" href="'.esc_url($iphone_icon_retina).'">' ."\n";
			}
		
			/* Favicon iPad */
			if (isset($ipad_icon) && $ipad_icon != "") {
				echo '<link rel="apple-touch-icon-precomposed" sizes="72x72" href="'.esc_url($ipad_icon).'">' ."\n";
			}
		
			/* Favicon iPad Retina display */
			if (isset($ipad_icon_retina) && $ipad_icon_retina != "") {
				echo '<link rel="apple-touch-icon-precomposed" sizes="144x144" href="'.esc_url($ipad_icon_retina).'">' ."\n";
			}
		}

		$primary_color = discy_options("primary_color");
		if ($primary_color != "") {
			$skin = $primary_color;
		}else {
			$skins = array("skin" => "#2d6ff7","violet" => "#9349b1","blue" => "#00aeef","bright_red" => "#fa4b2a","cyan" => "#058b7b","green" => "#81b441","red" => "#e91802");
			$site_skin = discy_options('site_skin');
			if ($site_skin == "skin" || $site_skin == "default" || $site_skin == "") {
				$skin = $skins["skin"];
			}else {
				$skin = $skins[$site_skin];
			}
		}
		if (isset($skin) && $skin != "") {
			echo '<meta name="theme-color" content="'.$skin.'">
			<meta name="msapplication-navbutton-color" content="'.$skin.'">
			<meta name="apple-mobile-web-app-capable" content="yes">
			<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">';
		}
	}
}
/* Content Width */
if (!isset($content_width)) {
	$content_width = 1170;
}
/* discy_meta */
if (!function_exists('discy_meta')) {
	function discy_meta($date = "",$category = "",$comment = "",$asked = "",$icons = "",$views = "",$post_id = 0,$post = object) {
		$post_id = ($post_id > 0?$post_id:get_the_ID());
		$post_type = (isset($post->post_type)?$post->post_type:get_post_type($post_id));
		if ($date == "on") {
			$is_question = (wpqa_questions_type === $post_type || wpqa_asked_questions_type === $post_type?true:false);
			$time_string = '<span class="entry-date published">%1$s</span>';
			$format_date_ago = discy_options("format_date_ago");
			$format_date_ago_types = discy_options("format_date_ago_types");
			if ($format_date_ago == "on" && (($post_type == "post" && isset($format_date_ago_types["posts"]) && $format_date_ago_types["posts"] == "posts") || ($is_question && isset($format_date_ago_types["questions"]) && $format_date_ago_types["questions"] == "questions"))) {
				if ($is_question) {
					$date_string = esc_html__("Asked:","discy");
				}else {
					$date_string = esc_html__("Posted:","discy");
				}
				$human_time_diff = (is_rtl()?esc_html__("ago","discy")." ":"").human_time_diff(get_the_time('U',$post_id),current_time('timestamp')).(is_rtl()?"":" ".esc_html__("ago","discy"));
				$time_string = sprintf($time_string,$human_time_diff);
			}else {
				if ($is_question) {
					$date_string = esc_html__("Asked:","discy");
				}else {
					$date_string = esc_html__("On:","discy");
				}
				$time_string = sprintf($time_string,esc_html(get_the_time(discy_date_format,$post_id)));
			}
			$posted_on = (isset($date_string)?$date_string:'').'<span class="date-separator"></span> '.($is_question?'<a href="'.get_the_permalink($post_id).'"'.(is_single()?' itemprop="url"':'').'>':'').$time_string.($is_question?'</a>':'');
			echo '<span class="post-date">'.$posted_on;
			if (is_single() && $is_question) {
				$get_the_time = get_the_time('c',$post_id);
				$puplished_date = ($get_the_time?$get_the_time:get_the_modified_date('c',$post_id));
				echo '<span class="discy_hide" itemprop="dateCreated" datetime="'.$puplished_date.'">'.$puplished_date.'</span>
				<span class="discy_hide" itemprop="datePublished" datetime="'.$puplished_date.'">'.$puplished_date.'</span>';
			}
			echo '</span>';
		}
		
		if ($category == "on" && 'post' === $post_type) {
			$categories_list = get_the_category_list(', ');
			if ($categories_list) {
				$posted_in = sprintf('<span class="post-cat">'.esc_html__('Posted in %1$s','discy').'</span>',$categories_list);
				echo '<span class="byline"> '.$posted_in.'</span>';
			}
		}
		if (wpqa_asked_questions_type === $post_type && $asked == "on") {
			$get_question_user_id = discy_post_meta("user_id","",false);
			if ($get_question_user_id != "" && $get_question_user_id > 0) {
				$display_name = get_the_author_meta('display_name',$get_question_user_id);
				if (has_wpqa() && isset($display_name) && $display_name != "") {
					echo '<span class="asked-to">'.esc_html__("Asked to","discy").': <a href="'.wpqa_profile_url($get_question_user_id).'">'.esc_html($display_name).'</a></span>';
				}
			}
		}
		if (wpqa_questions_type === $post_type && $category == "on") {
			$first_span = '<span class="byline"><span class="post-cat">'.esc_html__('In:','discy').' ';
			$second_span = '</span></span>';
			$out = '';
			$get_the_term_list = get_the_term_list($post_id,wpqa_question_categories,$first_span,apply_filters('discy_separator_categories',', '),$second_span);
			if (!isset($get_the_term_list->errors) && $get_the_term_list != "") {
				$out .= $get_the_term_list;
			}else {
				$category_meta = discy_post_meta("category_meta","",false);
				$term = get_term_by('slug',esc_html($category_meta),wpqa_question_categories);
				if (isset($term->slug)) {
					$out .= $first_span;
					$get_term_link = get_term_link($term);
					if (is_string($get_term_link)) {
						$out .= '<a href="'.$get_term_link.'">'.$term->name.'</a>';
					}
					$out .= $second_span;
				}else if ($category_meta != "") {
					$out .= $first_span.esc_html($category_meta).$second_span;
				}
			}
			echo apply_filters("discy_show_categroies",$out,$post_id,$first_span,$second_span);
			do_action("discy_after_question_category",$post_id);
		}
		do_action("discy_meta_before_comment",$post_id,$post_type,$category);
		$count_post_all = (int)(has_wpqa()?wpqa_count_comments($post_id):get_comments_number());
		if ($comment == "on" && !post_password_required() && ((isset($post->comment_status) && $post->comment_status == "open") || $count_post_all > 0)) {
			if (wpqa_questions_type === $post_type || wpqa_asked_questions_type === $post_type) {
				echo ($icons == "on"?"<span class='number'>".discy_count_number($count_post_all)."</span>":"").
				(is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)?"<span itemprop='answerCount' class='number discy_hide'>".$count_post_all."</span>":"");
				if ($icons != "on") {
					echo " <span class='question-span'>".sprintf(_n("%s Answer","%s Answers",$count_post_all,"discy"),$count_post_all)."</span>";
				}
			}else {?>
				<span class="post-comment">
					<?php esc_html_e('Comments: ','discy');?>
					<a href="<?php echo get_the_permalink($post_id)?>#comments">
						<?php echo discy_count_number($count_post_all);?>
					</a>
				</span>
			<?php }
		}
		$active_post_stats = discy_options("active_post_stats");
		if (has_wpqa() && 'post' === $post_type && $views == "on" && $active_post_stats == "on") {
			global $post;?>
			<span class="post-views">
				<?php echo esc_html__('Views:','discy').' ';
				echo discy_count_number(wpqa_get_post_stats($post->ID))?>
			</span>
		<?php }
	}
}
/* Update the plugin to the last version */
function discy_maintenance_mode() {
	$wpqa_custom_queries = get_option("wpqa_custom_queries");
	$active_groups = discy_options("active_groups");
	$active_message = discy_options("active_message");
	$active_notifications = discy_options("active_notifications");
	$active_activity_log = discy_options("active_activity_log");
	if (!isset($wpqa_custom_queries["asked_questions_convert"]) || !isset($wpqa_custom_queries["asked_question_answers"]) || !isset($wpqa_custom_queries["site_asked_question_answers"]) || !isset($wpqa_custom_queries["best_answer"]) || !isset($wpqa_custom_queries["questions"]) || !isset($wpqa_custom_queries["posts"]) || !isset($wpqa_custom_queries["answers"]) || !isset($wpqa_custom_queries["comments"]) || ($active_groups == "on" && !isset($wpqa_custom_queries["groups"])) || ($active_groups == "on" && !isset($wpqa_custom_queries["group_posts"])) || ($active_groups == "on" && !isset($wpqa_custom_queries["group_comments"])) || ($active_notifications == "on" && !isset($wpqa_custom_queries["notification"])) || ($active_activity_log == "on" && !isset($wpqa_custom_queries["activity"]))) {
		$stop_it = true;
	}
	if (has_wpqa() && (!function_exists('wpqa_custom_queries') || (!is_super_admin() && isset($stop_id)))) {
		wp_die('<h1>Under Maintenance</h1><br />Website under planned maintenance. Please check back later, also please make sure you update the WPQA plugin to the last version.');
	}
}
add_action('get_header','discy_maintenance_mode');?>