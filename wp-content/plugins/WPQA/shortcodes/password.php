<?php

/* @author    2codeThemes
*  @package   WPQA/shortcodes
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Password shortcode */
if (!function_exists('wpqa_lost_pass')) :
	function wpqa_lost_pass($atts, $content = null) {
		$protocol = is_ssl() ? 'https' : 'http';
		$a = shortcode_atts( array(
		    'dark_button' => '',
		    'login' => '',
		    'un-login' => '',
		    'text' => '',
		), $atts );
		$out = '';
		if (is_user_logged_in()) {
			$out .= wpqa_login_already();
		}else {
			$rand_w = rand(1,1000);
			if ($a["text"] == "") {
				$out .= '<p>'.esc_html__("Lost your password? Please enter your email address. You will receive a link and will create a new password via email.","wpqa").'</p>';
			}
			$out .= '<form method="post" class="wpqa-lost-password wpqa_form">'.apply_filters('wpqa_password_form',false).'
				<div class="wpqa_error_desktop wpqa_hide"><div class="wpqa_error"></div></div>
				<div class="wpqa_success"></div>
				<div class="form-inputs clearfix">
					<p>
						<label for="user_mail_'.$rand_w.'">'.esc_html__("E-Mail","wpqa").'<span class="required">*</span></label>
						<input type="email" class="required-item form-control" name="user_mail" id="user_mail_'.$rand_w.'">
						<i class="icon-mail"></i>
					</p>'.
					wpqa_add_captcha(wpqa_options("the_captcha_password"),"password",$rand_w).'
				</div>

				<div class="clearfix"></div>
				<div class="wpqa_error_mobile wpqa_hide"><div class="wpqa_error"></div></div>

				<p class="form-submit d-flex align-items-center justify-content-between mb-0">
					<span class="load_span"><span class="loader_2"></span></span>
					'.(wpqa_input_button() == "button"?'<button type="submit" class="button-default btn btn__primary'.(isset($a["dark_button"]) && $a["dark_button"] == "dark_button"?" dark_button":"").'">'.esc_html__("Reset","wpqa").'</button>':'<input type="submit" value="'.esc_attr__("Reset","wpqa").'" class="button-default'.(isset($a["dark_button"]) && $a["dark_button"] == "dark_button"?" dark_button":"").'">').
					(isset($a["login"]) && $a["login"] == "button"?(wpqa_input_button() == "button"?'<button type="button" class="'.(isset($a["un-login"]) && $a["un-login"] == true?"login-panel-un":"login-panel").' button-default btn btn__secondary">'.esc_attr__("Sign In","wpqa").'</button>':'<input type="button" class="login-panel button-default" value="'.esc_attr__("Sign In","wpqa").'">'):'').'
				</p>
				<input type="hidden" name="form_type" value="wpqa_forget">
				<input type="hidden" name="action" value="wpqa_ajax_password_process">
				<input type="hidden" name="redirect_to" value="'.esc_url(wp_unslash($protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])).'">
			</form>';
		}
		return $out;
	}
endif;
/* Password jQuery */
if (!function_exists('wpqa_pass_jquery')) :
	function wpqa_pass_jquery($data) {
		$uniqid_cookie = wpqa_options("uniqid_cookie");
		$limit_forgot_password = (int)wpqa_options("limit_forgot_password");
		$limit_forgot_password = ($limit_forgot_password > 0?$limit_forgot_password:0);
		$cookie_name = $uniqid_cookie.'wpqa_forgot_password'.date("d-m-y-g");
		$data = apply_filters("wpqa_forgot_password_data",$data);
		$errors = new WP_Error();
		if ( isset( $_REQUEST['redirect_to'] ) ) $redirect_to = esc_url_raw($_REQUEST['redirect_to']); else $redirect_to = esc_url(home_url('/'));
		// Errors
		if ( !isset($data['mobile']) && is_user_logged_in() ) :
			$user_id = get_current_user_id();
			$errors->add('already_logged', sprintf(esc_html__('You are already logged in, If you want to change your password go to %1$s edit profile %2$s.','wpqa'),'<a href="'.esc_url(wpqa_edit_profile_permalink()).'">','</a>'));
		elseif ( !isset($data['user_mail']) ) :
			$errors->add('empty_email', sprintf(esc_html__('%1$s ERROR %2$s: please insert your email.','wpqa'),'<strong>','</strong>'));
		elseif ( !email_exists($data['user_mail']) ) :
			$errors->add('invalid_email', sprintf(esc_html__('%1$s ERROR %2$s: there is no user registered with that email address.','wpqa'),'<strong>','</strong>'));
		elseif ($limit_forgot_password > 0 && isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] >= $limit_forgot_password) :
			$errors->add('limit', sprintf(esc_html__('%1$s ERROR %2$s: sorry, your limit for requesting forgot password ended, please wait some time and try again.','wpqa'),'<strong>','</strong>'));
		elseif (isset($data['user_mail']) && $data['user_mail'] != "") :
			$get_user_by_mail = get_user_by('email',esc_html($data['user_mail']));
			if (!isset($get_user_by_mail->ID)) :
				$errors->add('invalid_email', sprintf(esc_html__('%1$s ERROR %2$s: there is no user registered with that email address.','wpqa'),'<strong>','</strong>'));
			endif;
		endif;

		wpqa_check_captcha(wpqa_options("the_captcha_password"),"password",$data,$errors);

		// Result
		$result = array();
		if ($errors->get_error_code()) {
			if (wpqa_is_ajax()) {
				$result['success'] = 0;
				$result['error'] = $errors->get_error_message();
			}else {
				return $errors;
			}
		}else {
			$result['success'] = 1;
			if ($data['form_type']) :
				unset($data["form_type"]);
			endif;
			$user_data = array();
			$user_data["get_user_id"] = $get_user_by_mail->ID;
			$user_data["user_mail"] = $data['user_mail'];
			if (isset($data['user_email'])) {
				$user_data["user_email"] = $data['user_email'];
			}
			$user_data["display_name"] = $get_user_by_mail->display_name;
			$user_data = apply_filters("wpqa_forgot_password_user_data",$user_data);
			wpqa_send_password_link($get_user_by_mail);
			if ($limit_forgot_password > 0) {
				$limit_forgot_password_number = 0;
				if (isset($_COOKIE[$cookie_name])) {
					if ($_COOKIE[$cookie_name] != "") {
						$limit_forgot_password_number = (int)$_COOKIE[$cookie_name];
						$limit_forgot_password_number++;
					}
					unset($_COOKIE[$cookie_name]);
					setcookie($cookie_name,"",-1,COOKIEPATH,COOKIE_DOMAIN);
				}
				setcookie($cookie_name,$limit_forgot_password_number,time()+HOUR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
			}
			$lost_message = esc_html__('Check your email please.','wpqa');
			if (wpqa_is_ajax()) {
				$result['redirect'] = $redirect_to;
				$result['done'] = $lost_message;
			}else if (!isset($data['mobile'])) {
				wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.$lost_message.'</p></div>','wpqa_session');
				wp_safe_redirect($redirect_to);
				die();
			}else if (isset($data['mobile'])) {
				return $lost_message;
			}
		}
		if (wpqa_is_ajax()) {
			echo json_encode($result);
			die();
		}else if (isset($lost_message)) {
			return $lost_message;
		}
	}
endif;
/* Password process */
if (!function_exists('wpqa_pass_process')) :
	function wpqa_pass_process() {
		if (isset($_POST['form_type']) && $_POST['form_type'] == "wpqa_forget") :
			$return = wpqa_pass_jquery($_POST);
			if (is_wp_error($return)) :
	   			return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
	   		endif;
		endif;
	}
endif;
add_action('wpqa_password_form','wpqa_pass_process');
/* Lostpassword URL */
add_filter('lostpassword_url','wpqa_lostpassword_url',10,0);
if (!function_exists('wpqa_lostpassword_url')) :
	function wpqa_lostpassword_url() {
		$under_construction = wpqa_under_construction();
		if ($under_construction != "on" && isset($GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php') {
			return wpqa_lost_password_permalink();
		}else {
			return wpqa_lost_password_permalink().'" class="lost-passwords';
		}
	}
endif;
/* Remove text */
add_filter('gettext','wpqa_remove_text');
if (!function_exists('wpqa_remove_text')) :
	function wpqa_remove_text($text) {
		return $text;
	}
endif;
/* Change password email */
add_filter("send_password_change_email","wpqa_password_changed",1,2);
function wpqa_password_changed($return,$user) {
	if (isset($user["ID"])) {
		update_user_meta($user["ID"],"password_changed","changed");
	}
	return false;
}
/* Reset password */
add_filter('wpqa_init','wpqa_reset_password');
if (!function_exists('wpqa_reset_password')) :
	function wpqa_reset_password() {
		if (isset($_GET['reset_password']) && isset($_GET['u'])) {
			$user_reset = (int)esc_html($_GET['u']);
			if (!is_user_logged_in()) :
				$reset_password = get_user_meta($user_reset,"reset_password",true);
				$get_reset_password = esc_html($_GET['reset_password']);
				if ($reset_password == $get_reset_password && $get_reset_password != "") :
					wpqa_send_password($user_reset);
					wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Check your email, Your password has been reset.","wpqa").'</p></div>','wpqa_session');
					wp_safe_redirect(esc_url(home_url('/')));
					die();
				else :
					wpqa_session('<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Your password reset link has expired or not valid. Please request a new link","wpqa").' <a href="'.wpqa_lost_password_permalink().'" class="reset-password">'.esc_html__( 'Reset Password', 'wpqa' ).'</a>.</p></div>','wpqa_session');
					wp_safe_redirect(esc_url(home_url('/')));
					die();
				endif;
			else :
				$user_id = get_current_user_id();
				$if_user_id = get_user_by("id",$user_id);
				if (isset($if_user_id->caps["activation"]) && $if_user_id->caps["activation"] == 1) :
					// Not activation!
				else :
					wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.sprintf(esc_html__('You are already logged in, If you want to change your password go to %1$s edit profile %2$s.','wpqa'),'<a href="'.esc_url(wpqa_edit_profile_permalink()).'">','</a>').'</p></div>','wpqa_session');
					wp_safe_redirect(esc_url(home_url('/')));
					die();
				endif;
			endif;
		}
	}
endif;
/* Send password link */
if (!function_exists('wpqa_send_password_link')) :
	function wpqa_send_password_link($user) {
		$user_id = $user->ID;
		$display_name = $user->display_name;
		$user_email = $user->user_email;
		$rand_a = wpqa_token(15);
		$get_reset_password = get_user_meta($user_id,"reset_password",true);
		if ($get_reset_password == "") :
			update_user_meta($user_id,"reset_password",$rand_a);
			$get_reset_password = $rand_a;
		endif;
		$confirm_link_email = esc_url_raw(add_query_arg(array("u" => $user_id,"reset_password" => $get_reset_password),esc_url(home_url('/'))));
		$send_text = wpqa_send_mail(
			array(
				'content'            => wpqa_options("email_new_password"),
				'user_id'            => $user_id,
				'confirm_link_email' => $confirm_link_email,
			)
		);
		$email_title = wpqa_options("title_new_password");
		$email_title = ($email_title != ""?$email_title:esc_html__("Reset your password","wpqa"));
		$email_title = wpqa_send_mail(
			array(
				'content'            => $email_title,
				'title'              => true,
				'break'              => '',
				'user_id'            => $user_id,
				'confirm_link_email' => $confirm_link_email,
			)
		);
		wpqa_send_mails(
			array(
				'toEmail'     => esc_html($user_email),
				'toEmailName' => esc_html($display_name),
				'title'       => $email_title,
				'message'     => $send_text,
			)
		);
	}
endif;
/* Send password */
if (!function_exists('wpqa_send_password')) :
	function wpqa_send_password($user_id) {
		delete_user_meta($user_id,"reset_password");
		$pw = wpqa_token(15);
		wp_set_password($pw,$user_id);
		$author_user_email = get_the_author_meta("user_email",$user_id);
		$author_user_email = apply_filters("wpqa_user_email_reset_password",$author_user_email,$user_id);
		$author_display_name = get_the_author_meta("display_name",$user_id);
		$send_text = wpqa_send_mail(
			array(
				'content'        => wpqa_options("email_new_password_2"),
				'user_id'        => $user_id,
				'reset_password' => $pw,
			)
		);
		$email_title = wpqa_options("title_new_password_2");
		$email_title = ($email_title != ""?$email_title:esc_html__("Reset your password","wpqa"));
		$email_title = wpqa_send_mail(
			array(
				'content'        => $email_title,
				'title'          => true,
				'break'          => '',
				'user_id'        => $user_id,
				'reset_password' => $pw,
			)
		);
		wpqa_send_mails(
			array(
				'toEmail'     => $author_user_email,
				'toEmailName' => $author_display_name,
				'title'       => $email_title,
				'message'     => $send_text,
			)
		);
	}
endif;?>