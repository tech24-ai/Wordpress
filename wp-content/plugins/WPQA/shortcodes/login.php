<?php

/* @author    2codeThemes
*  @package   WPQA/shortcodes
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Login shortcode */
if (!function_exists('wpqa_login')) :
	function wpqa_login ($atts, $content = null) {
		global $posted;
		$protocol = is_ssl() ? 'https' : 'http';
		$a = shortcode_atts( array(
		    'forget'   => 'forget',
		    'register' => '',
		    'un-login' => '',
		), $atts );
		$out = '';
		if (is_user_logged_in()) {
			$out .= wpqa_login_already();
		}else {
			$rand_l = rand(1,1000);
			$activate_register = wpqa_options("activate_register");
			$stop_login_ajax = wpqa_options("stop_login_ajax");
			$filter_social_login = apply_filters("wpqa_filter_social_login",false);
			$out .= apply_filters("wpqa_filter_before_login_form",false);
			if ($filter_social_login != "" || shortcode_exists('wpqa_social_login') || shortcode_exists('rdp-linkedin-login') || shortcode_exists('oa_social_login') || shortcode_exists('login_me_now_google_button') || shortcode_exists('miniorange_social_login') || shortcode_exists('xs_social_login') || shortcode_exists('wordpress_social_login') || shortcode_exists('apsl-login') || shortcode_exists('apsl-login-lite') || shortcode_exists('nextend_social_login')) {
				$out .= '<div class="wpqa_login_social">';
					$out .= ($filter_social_login != ""?$filter_social_login:"").
					(shortcode_exists('wpqa_social_login')?do_shortcode("[wpqa_social_login]"):"").
					(shortcode_exists('rdp-linkedin-login')?do_shortcode("[rdp-linkedin-login]"):"").
					(shortcode_exists('oa_social_login')?do_shortcode("[oa_social_login]"):"").
					(shortcode_exists('login_me_now_google_button')?do_shortcode("[login_me_now_google_button]"):"").
					(shortcode_exists('miniorange_social_login')?do_shortcode("[miniorange_social_login]"):"").
					(shortcode_exists('xs_social_login')?do_shortcode("[xs_social_login]"):"").
					(shortcode_exists('wordpress_social_login')?do_shortcode("[wordpress_social_login]"):"").
					(shortcode_exists('apsl-login')?do_shortcode("[apsl-login]"):"").
					(shortcode_exists('apsl-login-lite')?do_shortcode("[apsl-login-lite]"):"").
					(shortcode_exists('nextend_social_login')?do_shortcode("[nextend_social_login]"):"");
					$out .= '<div class="wpqa_login_social_div"><span>'.esc_html__("or use","wpqa").'</span></div>
				</div>';
			}
			$out .= '<form class="wpqa_form login-form wpqa_login'.($stop_login_ajax == "on"?" wpqa-no-ajax":"").apply_filters("wpqa_filter_login_form_class",false).'" method="post">
				'.apply_filters('wpqa_login_form',false).'
				<div class="wpqa_error_desktop'.(isset($posted) && is_array($posted) && !empty($posted)?" wpqa_hide":"").'"><div class="wpqa_error"></div></div>
				
				<div class="form-inputs clearfix">
					<p class="login-text">
						<label for="username_'.$rand_l.'">'.apply_filters("wpqa_username_login",esc_html__("Username or email","wpqa")).'<span class="required">*</span></label>
						<input id="username_'.$rand_l.'" class="required-item form-control" autocomplete="username" type="text" name="log" value="'.(isset($_POST["log"])?esc_attr($_POST["log"]):"").'">
						<i class="icon-user"></i>
					</p>
					<p class="login-password">
						<label for="password_'.$rand_l.'">'.esc_html__("Password","wpqa").'<span class="required">*</span></label>
						<input id="password_'.$rand_l.'" class="required-item form-control" autocomplete="current-password" type="password" name="pwd">
						<i class="icon-lock-open"></i>
					</p>'.
					
					wpqa_add_captcha(wpqa_options("the_captcha_login"),"login",$rand_l).'
					
				</div>
				
				<div class="d-flex align-items-center justify-content-between mb-4 login-form-data">
					<div class="wpqa_checkbox_p rememberme normal_label d-flex align-items-center mb-1">
						<label class="mb-0"><span class="wpqa_checkbox"><input type="checkbox" name="rememberme" value="forever" checked="checked"></span> <span class="wpqa_checkbox_span">'.esc_html__("Remember Me!","wpqa").'</span></label>
					</div>
					
					'.(isset($a["forget"]) && $a["forget"] == "false"?'':'<a href="'.wpqa_lost_password_permalink().'" class="font-weight-bold color-dark mb-1'.(isset($a["un-login"]) && $a["un-login"] == true?" lost-password-login":" lost-password").'">'.esc_html__("Forgot Password?","wpqa").'</a>').'
				</div>

				<div class="clearfix"></div>
				<div class="wpqa_error_mobile'.(isset($posted) && is_array($posted) && !empty($posted)?" wpqa_hide":"").'"><div class="wpqa_error"></div></div>

				<p class="form-submit login-submit d-flex align-items-center justify-content-between mb-0">
					<span class="load_span"><span class="loader_2"></span></span>
					'.(wpqa_input_button() == "button"?'<button type="submit" class="button-default login-submit btn btn__primary">'.esc_html__("Login","wpqa").'</button>':'<input type="submit" value="'.esc_attr__("Login","wpqa").'" class="button-default login-submit">').
					($activate_register != 'disabled' && isset($a["register"]) && $a["register"] == "button"?(wpqa_input_button() == "button"?'<button type="button" class="signup '.(isset($a["un-login"]) && $a["un-login"] == true?"signup-panel-un":"signup-panel").' button-default btn btn__secondary">'.esc_attr__("Register","wpqa").'</button>':'<input type="button" class="signup signup-panel button-default" value="'.esc_attr__("Register","wpqa").'">'):'').'
				</p>
				
				<input type="hidden" name="redirect_to" value="'.esc_url(wp_unslash($protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])).'">
				<input type="hidden" name="form_type" value="wpqa-login">
				<input type="hidden" name="action" value="wpqa_ajax_login_process">
				'.apply_filters('wpqa_login_form_hidden',false,$posted).'
			</form>';
		}
		return $out;
	}
endif;
/* Login process */
if (!function_exists('wpqa_login_process')) :
	function wpqa_login_process() {
		if (isset($_POST['form_type']) && $_POST['form_type'] == "wpqa-login") :
			$return = wpqa_login_jquery();
			if (is_wp_error($return)) :
	   			return '<div class="wpqa_error wpqa_error_login">'.$return->get_error_message().'</div>';
	   		endif;
		endif;
	}
endif;
add_filter('wpqa_login_form','wpqa_login_process');
/* Login jQuery */
if (!function_exists('wpqa_login_jquery')) :
	function wpqa_login_jquery() {
		if ( isset( $_REQUEST['redirect_to'] ) ) $redirect_to = esc_url_raw($_REQUEST['redirect_to']); else $redirect_to = esc_url(home_url('/'));

		$errors = new WP_Error();

		$posted = array(
			'log'              => sanitize_text_field($_POST['log']),
			'pwd'              => sanitize_text_field($_POST['pwd']),
			'rememberme'       => (isset($_POST['rememberme']) && $_POST['rememberme'] != ""?esc_html($_POST['rememberme']):""),
			'redirect_to'      => esc_url_raw($_POST['redirect_to']),
			'wpqa_captcha'     => (isset($_POST['wpqa_captcha']) && $_POST['wpqa_captcha'] != ""?sanitize_text_field($_POST['wpqa_captcha']):""),
		);
		$posted = apply_filters('wpqa_login_posted',$posted);
		$posted = array_map('stripslashes', $posted);

		wpqa_check_captcha(wpqa_options("the_captcha_login"),"login",$posted,$errors);

		// Check the username
		if ( !$posted['log'] ) :
			$errors->add('empty_username', sprintf(esc_html__('%1$s ERROR %2$s: Please insert your name.','wpqa'),'<strong>','</strong>'));
		elseif ( !$posted['pwd'] ) :
			$errors->add('empty_password', sprintf(esc_html__('%1$s ERROR %2$s: Please insert your password.','wpqa'),'<strong>','</strong>'));
		endif;

		$result = array();
		if ( !$errors->get_error_code() ) {
			$data                  = array();
			$data['user_login']    = $posted['log'];
			$data['user_password'] = $posted['pwd'];
			$data['remember']      = $posted['rememberme'];
			$secure_cookie         = is_ssl() ? true : false;
			$user = wp_signon($data,$secure_cookie);

			$after_login = wpqa_options("after_login");
			$after_login_link = wpqa_options("after_login_link");
			if (isset($posted['redirect_to']) && $after_login == "same_page") {
				$redirect_to = esc_url_raw($posted['redirect_to']);
			}else if (isset($user->ID) && $user->ID > 0 && $after_login == "profile") {
				$redirect_to = wpqa_profile_url($user->ID);
			}else if ($after_login == "custom_link" && $after_login_link != "") {
				$redirect_to = esc_url($after_login_link);
			}else {
				$redirect_to = esc_url(home_url('/'));
			}
			
			if (isset($user->ID)) {
				do_action('wp_login',$user->user_login,$user);
				wp_set_current_user($user->ID);
				wp_set_auth_cookie($user->ID,true);
				wpqa_fix_counts($user->ID);
			}

			$redirect_to = apply_filters("wpqa_login_redirect",$redirect_to,(isset($user->ID) && $user->ID > 0?$user->ID:0),$posted);
			
			if (wpqa_is_ajax()) {
				if ( !is_wp_error($user) ) {
					$result['success'] = 1;
					$result['redirect'] = $redirect_to;
				}else {
					$result['success'] = 0;
					foreach ($user->errors as $error) {
						$result['error'] = $error[0];
						break;
					}
				}
				echo json_encode($result);
				die();
			}else {
				if (!is_wp_error($user)) {
					wp_redirect($redirect_to);
					exit;
				}
			}
			return $user;
		}else {
			$result['success'] = 0;
			foreach ($errors->errors as $error) {
				$result['error'] = $error[0];
				break;
			}
			if (wpqa_is_ajax()) {
				echo json_encode($result);
				die();
			}else {
				return $errors;
			}
		}
	}
endif;
/* Login already message */
if (!function_exists('wpqa_login_already')) :
	function wpqa_login_already() {
		$user_id = get_current_user_id();
		$username = get_the_author_meta('display_name',$user_id);
		return '<div class="wpqa-login-already"><p>'.sprintf(esc_html__('Hello %1$s (not %2$s? %3$s Log out %4$s)','wpqa'),'<strong>'.$username.'</strong>','<strong>'.$username.'</strong>','<a href="'.wpqa_get_logout().'">','</a>').'</p><p>'.sprintf(esc_html__('From your profile you can view your %1$s recent questions %2$s, %3$s edit your password and profile details %4$s.','wpqa'),'<a href="'.wpqa_profile_url($user_id).'">','</a>','<a href="'.wpqa_edit_profile_permalink().'">','</a>').'</p></div>';
	}
endif;
/* Login URL */
add_filter('login_url','wpqa_login_url',10,1);
if (!function_exists('wpqa_login_url')) :
	function wpqa_login_url($login_url) {
		$activate_login = wpqa_options("activate_login");
		$under_construction = wpqa_under_construction();
		$redirect_wp_admin_unlogged = wpqa_options("redirect_wp_admin_unlogged");
		if ($activate_login != "disabled" && $under_construction != "on" && $redirect_wp_admin_unlogged == "on") {
			return wpqa_login_permalink();
		}else {
			return $login_url;
		}
	}
endif;
/* Login redirect */
add_action('wpqa_init','wpqa_login_redirect',10,1);
if (!function_exists('wpqa_login_redirect')) :
	function wpqa_login_redirect() {
		$activate_login = wpqa_options("activate_login");
		$under_construction = wpqa_under_construction();
		$redirect_wp_admin_unlogged = wpqa_options("redirect_wp_admin_unlogged");
		if ($activate_login != "disabled" && $under_construction != "on" && $redirect_wp_admin_unlogged == "on" && isset($GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php' && (!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] == 'login'))) {
			wp_safe_redirect(wpqa_login_permalink());
			exit;
		}
		if ($under_construction != "on" && is_admin() && !wpqa_is_ajax() && is_user_logged_in()) {
			$redirect_wp_admin = wpqa_options("redirect_wp_admin");
			$user_id = get_current_user_id();
			$user_info = get_userdata($user_id);
			$user_group = wpqa_get_user_group($user_info);
			if ($under_construction != "on" && ($user_group == "ban_group" || $redirect_wp_admin == "on")) {
				$redirect_groups = wpqa_options("redirect_groups");
				if ($under_construction != "on" && ($user_group == "ban_group" || (is_array($redirect_groups) && in_array($user_group,$redirect_groups)))) {
					wp_safe_redirect(esc_url(home_url('/')));
					exit;
				}
			}
		}
	}
endif;
/* Logout redirect */
add_filter('allowed_redirect_hosts','wpdocs_allow_ms_parent_redirect');
function wpdocs_allow_ms_parent_redirect($allowed) {
	$after_logout = wpqa_options("after_logout");
	$after_logout_link = wpqa_options("after_logout_link");
	if ($after_logout == "custom_link" && $after_logout_link != "") {
		$redirect_to = esc_url_raw($after_logout_link);
		$parse = parse_url($redirect_to);
	    $allowed[] = $parse['host'];
	}
    return $allowed;
}
/* Login message */
add_filter('wpqa_login_message','wpqa_login_message');
function wpqa_login_message($message,$alert = 'error',$block = '',$register = '') {
	if ($register == "register_2") {
		$register = " register_2='yes'";
	}
	if ($alert == 'info') {
		$alert = '';
		$icon = 'icon-lamp';
	}else if ($alert == 'warning') {
		$alert = 'alert-message-warning';
		$icon = 'icon-flag';
	}else {
		$alert = 'alert-message-error';
		$icon = 'icon-cancel';
	}
	$out = ($block == "block"?'<div class="wpqa-default-template block-section-div">':'').'<div class="alert-message '.$alert.'"><i class="'.$icon.'"></i><p>'.$message.'</p></div>'.do_shortcode("[wpqa_login".$register."]").($block == "block"?'</div>':'');
    return $out;
}?>