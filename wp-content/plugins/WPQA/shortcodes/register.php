<?php

/* @author    2codeThemes
*  @package   WPQA/shortcodes
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Signup attr */
if (!function_exists('wpqa_signup_attr')) :
	function wpqa_signup_attr($atts, $content = null) {
		global $posted;
		$a = shortcode_atts( array(
			'dark_button' => '',
			'login' => '',
			'un-login' => '',
		), $atts );
		$out = '';
		if (is_user_logged_in()) {
			$out .= wpqa_login_already();
		}else {
			$protocol = is_ssl() ? 'https' : 'http';
			$rand_r = rand(1,1000);
			$activate_login = wpqa_options("activate_login");
			$register_items = wpqa_options("register_items");
			$comfirm_password = wpqa_options("comfirm_password");
			$stop_register_ajax = wpqa_options("stop_register_ajax");
			$filter_social_login = apply_filters("wpqa_filter_social_login",false);
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
			$out .= '<form method="post" class="signup_form wpqa_form'.(((isset($register_items["image_profile"]) && isset($register_items["image_profile"]["value"]) && $register_items["image_profile"]["value"] == "image_profile") || (isset($register_items["cover"]) && isset($register_items["cover"]["value"]) && $register_items["cover"]["value"] == "cover")) || $stop_register_ajax == "on"?' wpqa-no-ajax':'').apply_filters("wpqa_filter_form_class",false).'" enctype="multipart/form-data">'.apply_filters('wpqa_register_form',false).'
				<div class="wpqa_error_desktop'.(isset($posted) && is_array($posted) && !empty($posted)?" wpqa_hide":"").'"><div class="wpqa_error"></div></div>
				<div class="wpqa_success"></div>
				<div class="form-inputs clearfix">'.
					apply_filters('wpqa_register_before_username',false,$posted);
					$register_items["username"] = array("sort" => esc_html__("Username","wpqa"),"value" => "username");
					$register_items["password"] = array("sort" => esc_html__("Password","wpqa"),"value" => "password");
					if (isset($register_items) && is_array($register_items) && !empty($register_items)) {
						foreach ($register_items as $sort_key => $sort_value) {
							$out = apply_filters("wpqa_register_sort",$out,"register_items",$register_items,$sort_key,$sort_value,"register",$posted);
							if ($sort_key == "username" && isset($sort_value["value"]) && $sort_value["value"] == "username") {
								$out .= '<p class="'.$sort_key.'_field">
									<label for="user_name_'.$rand_r.'">'.esc_html__("Username","wpqa").'<span class="required">*</span></label>
									<input type="text" class="required-item form-control" name="user_name" id="user_name_'.$rand_r.'" value="'.(isset($posted["user_name"])?$posted["user_name"]:"").'">
									<i class="icon-user"></i>
								</p>'.apply_filters('wpqa_register_after_username',false,$posted);
							}else if ($sort_key == "password" && isset($sort_value["value"]) && $sort_value["value"] == "password") {
								$out .= '<p class="'.$sort_key.'_field">
									<label for="pass1_'.$rand_r.'">'.esc_html__("Password","wpqa").'<span class="required">*</span></label>
									<input type="password" class="required-item form-control" name="pass1" id="pass1_'.$rand_r.'" autocomplete="off">
									<i class="icon-lock-open"></i>
								</p>';
								if ($comfirm_password != "on") {
									$out .= '<p class="'.$sort_key.'_2_field">
										<label for="pass2_'.$rand_r.'">'.esc_html__("Confirm Password","wpqa").'<span class="required">*</span></label>
										<input type="password" class="required-item form-control" name="pass2" id="pass2_'.$rand_r.'" autocomplete="off">
										<i class="icon-lock"></i>
									</p>';
								}
								$out .= apply_filters('wpqa_register_after_password',false,$posted);
							}
							$out .= wpqa_register_edit_fields($sort_key,$sort_value,"register",$rand_r);
						}
					}
					
					$out .= wpqa_add_captcha(wpqa_options("the_captcha_register"),"register",$rand_r);
					
					$terms_active_register = wpqa_options("terms_active_register");
					if ($terms_active_register == "on") {
						$terms_checked_register = wpqa_options("terms_checked_register");
						if ((isset($posted['agree_terms']) && $posted['agree_terms'] == "on") || ($terms_checked_register == "on" && empty($posted))) {
							$active_terms = true;
						}
						$terms_link_register = wpqa_options("terms_link_register");
						$terms_page_register = wpqa_options('terms_page_register');
						$terms_active_target_register = wpqa_options('terms_active_target_register');
						$privacy_policy_register = wpqa_options('privacy_policy_register');
						$privacy_active_target_register = wpqa_options('privacy_active_target_register');
						$privacy_page_register = wpqa_options('privacy_page_register');
						$privacy_link_register = wpqa_options('privacy_link_register');
						$out .= '<p class="wpqa_checkbox_p">
							<label for="agree_terms-'.$rand_r.'">
								<span class="wpqa_checkbox"><input type="checkbox" id="agree_terms-'.$rand_r.'" name="agree_terms" value="on" '.(isset($active_terms) == "on"?"checked='checked'":"").'></span>
								<span class="wpqa_checkbox_span">'.sprintf(esc_html__('By registering, you agree to the %1$s Terms of Service %2$s %3$s.','wpqa'),'<a target="'.($terms_active_target_register == "same_page"?"_self":"_blank").'" href="'.esc_url(isset($terms_link_register) && $terms_link_register != ""?$terms_link_register:(isset($terms_page_register) && $terms_page_register != ""?get_page_link($terms_page_register):"#")).'">','</a>',($privacy_policy_register == "on"?" ".sprintf(esc_html__('and %1$s Privacy Policy %2$s','wpqa'),'<a target="'.($privacy_active_target_register == "same_page"?"_self":"_blank").'" href="'.esc_url(isset($privacy_link_register) && $privacy_link_register != ""?$privacy_link_register:(isset($privacy_page_register) && $privacy_page_register != ""?get_page_link($privacy_page_register):"#")).'">','</a>'):"")).'<span class="required">*</span></span>
							</label>
						</p>';
					}
				$out .= '</div>

				<div class="clearfix"></div>
				<div class="wpqa_error_mobile'.(isset($posted) && is_array($posted) && !empty($posted)?" wpqa_hide":"").'"><div class="wpqa_error"></div></div>

				<p class="form-submit d-flex align-items-center justify-content-between mb-0">
					<span class="load_span"><span class="loader_2"></span></span>
					'.(wpqa_input_button() == "button"?'<button type="submit" name="register" class="button-default btn btn__primary'.(isset($a["dark_button"]) && $a["dark_button"] == "dark_button"?" dark_button":"").'">'.esc_html__("Signup","wpqa").'</button>':'<input type="submit" name="register" value="'.esc_attr__("Signup","wpqa").'" class="button-default'.(isset($a["dark_button"]) && $a["dark_button"] == "dark_button"?" dark_button":"").'">').
					($activate_login != 'disabled' && isset($a["login"]) && $a["login"] == "button"?(wpqa_input_button() == "button"?'<button type="button" class="'.(isset($a["un-login"]) && $a["un-login"] == true?"login-panel-un":"login-panel").' button-default btn btn__secondary">'.esc_attr__("Sign In","wpqa").'</button>':'<input type="button" class="login-panel button-default" value="'.esc_attr__("Sign In","wpqa").'">'):'').'
				</p>

				<input type="hidden" name="form_type" value="wpqa-signup">
				<input type="hidden" name="action" value="wpqa_ajax_signup_process">
				<input type="hidden" name="redirect_to" value="'.esc_url(wp_unslash($protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])).'">
				'.wp_referer_field().
				apply_filters('wpqa_signup_form_hidden',false,$posted).'
			</form>';
		}
		return $out;
	}
endif;
/* Signup jQuery */
if (!function_exists('wpqa_signup_jquery')) :
	function wpqa_signup_jquery($data = array()) {
		global $posted;
		$data = (is_array($data) && !empty($data)?$data:$_POST);
		$allow_spaces = wpqa_options("allow_spaces");
		$username_register_maximum = (int)wpqa_options("username_register_maximum");
		$register_items = wpqa_options("register_items");
		$comfirm_password = wpqa_options("comfirm_password");
		$errors = new WP_Error();
		if ( isset( $_REQUEST['redirect_to'] ) ) $redirect_to = esc_url_raw($_REQUEST['redirect_to']); else $redirect_to = esc_url(home_url('/'));
		// Process signup form
		$posted = array(
			'user_name'         => (isset($data['user_name']) && $data['user_name'] != ""?sanitize_text_field($data['user_name']):""),
			'email'             => (isset($data['email']) && $data['email'] != ""?sanitize_text_field(trim($data['email'])):""),
			'pass1'             => (isset($data['pass1']) && $data['pass1'] != ""?sanitize_text_field($data['pass1']):""),
			'pass2'             => (isset($data['pass2']) && $data['pass2'] != ""?sanitize_text_field($data['pass2']):""),
			'agree_terms'       => (isset($data['agree_terms']) && $data['agree_terms'] != ""?sanitize_text_field($data['agree_terms']):""),
			'wpqa_captcha'      => (isset($data['wpqa_captcha']) && $data['wpqa_captcha'] != ""?sanitize_text_field($data['wpqa_captcha']):""),
			'nickname'          => (isset($data['nickname']) && $data['nickname'] != ""?sanitize_text_field($data['nickname']):""),
			'first_name'        => (isset($data['first_name']) && $data['first_name'] != ""?sanitize_text_field($data['first_name']):""),
			'last_name'         => (isset($data['last_name']) && $data['last_name'] != ""?sanitize_text_field($data['last_name']):""),
			'display_name'      => (isset($data['display_name']) && $data['display_name'] != ""?sanitize_text_field($data['display_name']):""),
			'country'           => (isset($data['country']) && $data['country'] != ""?sanitize_text_field($data['country']):""),
			'city'              => (isset($data['city']) && $data['city'] != ""?sanitize_text_field($data['city']):""),
			'phone'             => (isset($data['phone']) && $data['phone'] != ""?sanitize_text_field($data['phone']):""),
			'gender'            => (isset($data['gender']) && $data['gender'] != ""?sanitize_text_field($data['gender']):""),
			'age'               => (isset($data['age']) && $data['age'] != ""?sanitize_text_field($data['age']):""),
			'subscribe_plan'    => (isset($data['subscribe_plan']) && $data['subscribe_plan'] != ""?sanitize_text_field($data['subscribe_plan']):""),
			'redirect_to'       => esc_url_raw($data['redirect_to']),
		);

		$posted = apply_filters('wpqa_register_posted',$posted);
		$posted = array_map('stripslashes', $posted);
		
		$posted['username'] = sanitize_user((isset($posted['username'])?$posted['username']:""));
		// Validation
		
		if ( empty($posted['user_name']) ) {
			$errors->add('required-username',esc_html__("Please enter your name.","wpqa"));
		}
		if ( $allow_spaces != "on" && $posted['user_name'] == trim($posted['user_name']) && strpos($posted['user_name'], ' ') !== false ) {
			$errors->add('error-username',esc_html__("Please enter your name without any spaces.","wpqa"));
		}
		if ( $username_register_maximum > 0 && strlen($posted['user_name']) > $username_register_maximum ) {
			$errors->add('error-username-limit','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Sorry, The maximum characters for the username is","wpqa")." ".$username_register_maximum);
		}
		if ( empty($posted['email']) ) {
			$errors->add('required-email',esc_html__("Please enter your email.","wpqa"));
		}
		if ( empty($posted['pass1']) ) {
			$errors->add('required-pass1',esc_html__("Please enter your password.","wpqa"));
		}
		if ( $comfirm_password != "on" && empty($posted['pass2']) ) {
			$errors->add('required-pass2',esc_html__("Please rewrite password.","wpqa"));
		}
		if ( $comfirm_password != "on" && $posted['pass1'] !== $posted['pass2'] ) {
			$errors->add('required-pass1',esc_html__("Password does not match.","wpqa"));
		}

		do_action('wpqa_register_errors_main',$errors,$posted,$register_items,"register",0,$data);
		
		if (!isset($data["mobile"])) {
			wpqa_check_captcha(wpqa_options("the_captcha_register"),"register",$posted,$errors);
		}
		
		$terms_active_register = wpqa_options("terms_active_register");
		if ($terms_active_register == "on" && $posted['agree_terms'] != "on") {
			$errors->add('required-terms', esc_html__("There are required fields (Agree of the terms).","wpqa"));
		}
		// Check the username
		if ( username_exists( $posted['user_name'] ) ) :
			$errors->add('registered-username',esc_html__("This username is already registered.","wpqa"));
		endif;
		// Check the e-mail address
		if ( !is_email( $posted['email'] ) ) :
			$errors->add('right-email',esc_html__("Please write correctly email.","wpqa"));
		elseif ( email_exists( $posted['email'] ) ) :
			$errors->add('registered-email',esc_html__("This email is already registered, please choose another one.","wpqa"));
		endif;

		wpqa_black_list_emails_words($posted['email'],$posted['user_name'],$errors);
		
		do_action('wpqa_register_errors',$errors,$posted,$register_items,"register");
		
		// Result
		$result = array();
		if ( !$errors->get_error_code() ) :
			do_action('register_post', $posted['user_name'], $posted['email'], $errors);
			$errors = apply_filters( 'registration_errors', $errors, $posted['user_name'], $posted['email'] );
			// if there are no errors, let's create the user account
			if ( !$errors->get_error_code() ) :
				$user_id = wp_create_user($posted['user_name'],$posted['pass1'],$posted['email']);
				if (is_wp_error($user_id)) {
					$errors->add('error', sprintf('<strong>'.esc_html__('Error:','wpqa').'</strong> '.esc_html__('Sorry, You can not register. Please contact the webmaster','wpqa').': %s',get_option('admin_email')));
					$result['success'] = 0;
					foreach ($errors->errors as $error) {
						if (!isset($data["mobile"])) {
							$result['error'] = $error[0];
							break;
						}
					}
				}else {
					do_action("wpqa_before_register",$user_id,$posted);
					do_action("wpqa_after_register",$user_id,$posted,isset($_FILES)?$_FILES:array(),"register");
					
					if (isset($data["mobile"])) {
						return $user_id;
					}else {
						update_user_meta($user_id,"wpqa_registered_site","done");
						$activate_login = wpqa_options("activate_login");
						if ($activate_login != "disabled") {
							$secure_cookie = is_ssl() ? true : false;
							wp_cache_delete($user_id,'users');
							wp_cache_delete($posted['user_name'],'userlogins');
							wp_clear_auth_cookie();
							wp_set_auth_cookie($user_id,true,$secure_cookie);

							wpqa_ip_address(0,$user_id);
							
							$after_register = wpqa_options("after_register");
							$after_register_link = wpqa_options("after_register_link");
							$subscribe_plan = get_user_meta($user_id,"subscribe_plan",true);
							if ($subscribe_plan != "") {
								$redirect_to = esc_url(wpqa_subscriptions_permalink())."#li-subscribe-".$subscribe_plan;
							}else if (isset($posted['redirect_to']) && $after_register == "same_page") {
								$redirect_to = esc_url_raw($posted['redirect_to']);
							}else if (isset($user_id) && $user_id > 0 && $after_register == "profile") {
								$redirect_to = wpqa_profile_url($user_id);
							}else if ($after_register == "custom_link" && $after_register_link != "") {
								$redirect_to = esc_url($after_register_link);
							}else {
								$redirect_to = esc_url(home_url('/'));
							}
						}else {
							$redirect_to = esc_url(home_url('/'));
							if (!isset($data["mobile"])) {
								wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("You are registered now, and maybe the admin will contact you.","wpqa").'</p></div>','wpqa_session');
							}
						}

						$redirect_to = apply_filters("wpqa_register_redirect",$redirect_to,(isset($user_id) && $user_id > 0?$user_id:0),$posted);

						if (wpqa_is_ajax()) {
							$result['success'] = 1;
							$result['redirect'] = $redirect_to;
						}else {
							wp_safe_redirect($redirect_to);
							die();
						}
					}
				}
			else :
				if (!isset($data["mobile"]) && wpqa_is_ajax()) {
					$result['success'] = 0;
					foreach ($errors->errors as $error) {
						$result['error'] = $error[0];
						break;
					}
				}
			endif;
		else :
			if (!isset($data["mobile"]) && wpqa_is_ajax()) {
				$result['success'] = 0;
				foreach ($errors->errors as $error) {
					$result['error'] = $error[0];
					break;
				}
			}
		endif;
		if (!isset($data["mobile"]) && wpqa_is_ajax()) {
			echo json_encode($result);
			die();
		}
		return $errors;
	}
endif;
/* Illegal usernames and emails */
function wpqa_black_list_emails_words($email,$user_name,$errors) {
	$black_list_emails = wpqa_options("black_list_emails");
	if (is_array($black_list_emails) && !empty($black_list_emails)) {
		foreach ($black_list_emails as $value) {
			if (isset($value["email"]) && $value["email"] != "" && strpos($email,$value["email"]) !== false) {
				$errors->add('wrong-email',esc_html__("Sorry, This email or domain are not allowing to register, Please try another email.","wpqa"));
			}
		}
	}

	$block_words_register = wpqa_options("block_words_register");
	if (is_array($block_words_register) && !empty($block_words_register)) {
		foreach ($block_words_register as $value) {
			if (isset($value["name"]) && $value["name"] != "" && (!validate_username($user_name) || $value["name"] == $user_name)) {
				$errors->add('wrong-name',esc_html__("Sorry, This name is not allowing to register, Please try another name.","wpqa"));
			}
		}
	}
}
add_action("register_post","wpqa_register_post",1,3);
if (!function_exists('wpqa_register_post')) :
	function wpqa_register_post($sanitized_user_login,$user_email,$errors) {
		wpqa_black_list_emails_words($user_email,$sanitized_user_login,$errors);
	}
endif;
/* Signup process */
if (!function_exists('wpqa_signup_process')) :
	function wpqa_signup_process($data = array()) {
		$data = (is_array($data) && !empty($data)?$data:$_POST);
		if (isset($data['form_type']) && $data['form_type'] == "wpqa-signup") :
			$return = wpqa_signup_jquery($data);
			if (is_wp_error($return)) :
				return '<div class="wpqa_error wpqa_error_register">'.$return->get_error_message().'</div>';
			endif;
		endif;
	}
endif;
add_filter('wpqa_register_form','wpqa_signup_process');
/* Default group */
function wpqa_default_group() {
	$activate_review_users = apply_filters("wpqa_activate_review_users",false);
	$user_review = wpqa_options("user_review");
	$confirm_email = wpqa_options("confirm_email");
	$default_group = wpqa_options("default_group");
	$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
	$default_group = ($activate_review_users == true || ($user_review == "on" && $confirm_email != "on")?"wpqa_under_review":$default_group);
	$default_group = apply_filters("wpqa_register_default_group",$default_group);
}
/* Before registration */
add_action("wpqa_before_register","wpqa_before_register",1,2);
function wpqa_before_register($user_id,$posted) {
	$activate_review_users = apply_filters("wpqa_activate_review_users",false);
	$user_review = wpqa_options("user_review");
	$confirm_email = wpqa_options("confirm_email");
	if ($confirm_email == "on") {
		$rand_a = wpqa_token(15);
		update_user_meta($user_id,"activation",$rand_a);
		$confirm_link = esc_url_raw(add_query_arg(array("u" => $user_id,"activate" => $rand_a),esc_url(home_url('/'))));
		$send_text = wpqa_send_mail(
			array(
				'content'            => wpqa_options("email_confirm_link"),
				'user_id'            => $user_id,
				'confirm_link_email' => $confirm_link,
			)
		);
		$email_title = wpqa_options("title_confirm_link");
		$email_title = ($email_title != ""?$email_title:esc_html__("Confirm account","wpqa"));
		$email_title = wpqa_send_mail(
			array(
				'content'            => $email_title,
				'title'              => true,
				'break'              => '',
				'user_id'            => $user_id,
				'confirm_link_email' => $confirm_link,
			)
		);
		wpqa_send_mails(
			array(
				'toEmail'     => sanitize_text_field($posted['email']),
				'toEmailName' => sanitize_text_field($posted['user_name']),
				'title'       => $email_title,
				'message'     => $send_text,
			)
		);
		if (!isset($posted['mobile'])) {
			wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Check your email please to activate your membership.","wpqa").'</p></div>','wpqa_session');
		}
	}else {
		$default_group = wpqa_options("default_group");
		$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
		$default_group = ($activate_review_users == true || ($user_review == "on" && $confirm_email != "on")?"wpqa_under_review":$default_group);
		$default_group = apply_filters("wpqa_register_default_group",$default_group);
	}
	if ($activate_review_users == true || ($user_review == "on" && $confirm_email != "on")) {
		$send_email_users_review = wpqa_options("send_email_users_review");
		if ($send_email_users_review == "on") {
			$send_text = wpqa_send_mail(
				array(
					'content' => wpqa_options("email_review_user"),
					'user_id' => $user_id,
				)
			);
			$email_title = wpqa_options("title_review_user");
			$email_title = ($email_title != ""?$email_title:esc_html__("New user for review","wpqa"));
			$email_title = wpqa_send_mail(
				array(
					'content' => $email_title,
					'title'   => true,
					'break'   => '',
					'user_id' => $user_id,
				)
			);
			wpqa_send_mails(
				array(
					'title'   => $email_title,
					'message' => $send_text,
				)
			);
		}
	}
	$nickname = ($posted['nickname'] != ""?$posted['nickname']:$posted['user_name']);
	$nickname = sanitize_title($nickname);
	$display_name = ($posted['display_name'] != ""?$posted['display_name']:$posted['user_name']);
	wp_update_user(array('ID' => $user_id,'role' => ($confirm_email == 'on'?'activation':$default_group),'user_nicename' => $nickname,'nickname' => $nickname,'display_name' => $display_name));
}
/* After registration */
add_action('user_register','wpqa_user_registration',1,1);
function wpqa_user_registration($user_id) {
	$activate_review_users = apply_filters("wpqa_activate_review_users",false);
	$user_review = wpqa_options("user_review");
	$confirm_email = wpqa_options("confirm_email");
	if ($activate_review_users == true || ($user_review == "on" && $confirm_email != "on") || ($user_review == "on" && $confirm_email == "on")) {
		$ask_under_review = get_role("wpqa_under_review");
		if (!isset($ask_under_review)) {
			add_role("wpqa_under_review",esc_html__("Under review","wpqa"),array('read' => false));
		}
	}
	if ($confirm_email == "on") {
		$default_group = 'activation';
		$activation = get_role("activation");
		if (!isset($activation)) {
			add_role("activation",esc_html__("Activation","wpqa"),array('read' => false));
		}
	}else {
		$default_group = wpqa_options("default_group");
		$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
		$default_group = ($activate_review_users == true || ($user_review == "on" && $confirm_email != "on")?"wpqa_under_review":$default_group);
		$default_group = apply_filters("wpqa_register_default_group",$default_group);
	}
	if ($default_group == "wpqa_under_review" || $default_group == "activation") {
		update_user_meta($user_id,"wpqa_default_group",$default_group);
	}
	if (isset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_referral'])) {
		update_user_meta($user_id,"wpqa_register_referral",sanitize_text_field($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_referral']));
		unset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_referral']);
		setcookie(wpqa_options("uniqid_cookie").'wpqa_referral',"",-1,COOKIEPATH,COOKIE_DOMAIN);
	}
}
/* After registration */
add_action('user_register','wpqa_after_registration',2,1);
add_action('wpqa_after_registration','wpqa_after_registration',1,1);
function wpqa_after_registration($user_id) {
	$register_default_options = wpqa_options("register_default_options");
	$default_options = array("show_point_favorite","received_email","received_email_post","received_message","unsubscribe_mails","new_payment_mail","send_message_mail","answer_on_your_question","answer_question_follow","notified_reply","notification_mention","question_schedules","post_schedules");
	foreach ($default_options as $key) {
		if (is_array($register_default_options) && in_array($key,$register_default_options)) {
			update_user_meta($user_id,$key,"on");
		}
	}
	$meta_stats = wpqa_get_meta_stats();
	update_user_meta($user_id,$meta_stats,0);
	$wpqa_default_group = get_user_meta($user_id,"wpqa_default_group",true);
	if ($wpqa_default_group == "wpqa_under_review" || $wpqa_default_group == "activation") {
		// Not activated or under review
	}else {
		do_action("wpqa_register_welcome",$user_id);
	}
}
/* Welcome register */
add_action("nsl_register_new_user","wpqa_register_welcome");
add_action("oa_social_login_action_before_user_login","wpqa_register_welcome");
//add_action("oa_social_login_action_before_user_login","wpqa_register_welcome");
add_action("mo_user_register","wpqa_register_welcome");
add_action("wsl_hook_process_login_after_wp_insert_user","wpqa_register_welcome");
add_action("APSL_createUser","wpqa_register_welcome");
add_action("wpqa_register_welcome","wpqa_register_welcome");
function wpqa_register_welcome($user_id) {
	$user_id = (is_object($user_id)?$user_id->ID:$user_id);
	$array = array("wpqa_count_best_answers","wpqa_questions_count","wpqa_posts_count","wpqa_answers_count","wpqa_comments_count","wpqa_groups_count","wpqa_group_posts_count","wpqa_group_comments_count","wpqa_notification_count","wpqa_activity_count");
	foreach ($array as $value) {
		$wpqa_count = get_user_meta($user_id,$value,true);
		if ($wpqa_count == "") {
			update_user_meta($user_id,$value,0);
		}
	}
	wpqa_send_welcome_mail($user_id);
	$active_points = wpqa_options("active_points");
	wpqa_add_gift_points($user_id,$active_points);

	$active_referral = wpqa_options("active_referral");
	$points_referral = (int)wpqa_options("points_referral");
	$wpqa_register_referral = get_user_meta($user_id,"wpqa_register_referral",true);
	if ($user_id > 0 && $points_referral > 0 && $active_points == "on" && $active_referral == "on" && $wpqa_register_referral != "") {
		$invite = sanitize_text_field($wpqa_register_referral);
		$user = get_users(array("number" => 1,"count_total" => false,"meta_query" => array("relation" => "OR",array("key" => "wpqa_referral","value" => $invite),array("key" => $invite))));
		$user_id_invite = (int)(isset($user[0]) && isset($user[0]->ID)?$user[0]->ID:0);
		if ($user_id_invite > 0) {
			wpqa_add_points($user_id_invite,$points_referral,"+","points_referral");
			wpqa_add_points($user_id_invite,$points_referral,"+","points_referral",0,0,0,"points_referral");
			wpqa_notifications_activities($user_id_invite,$user_id,"","","","points_referral","notifications",$points_referral." "._n("Point","Points",$points_referral,"wpqa"));
			$invite_meta = get_user_meta($user_id_invite,$invite,true);
			if (is_array($invite_meta) && !empty($invite_meta) && ((isset($invite_meta["status"]) && $invite_meta["status"] == "pending") || (!isset($invite_meta["status"])))) {
				$invite_meta["status"] = "completed";
				$invite_meta["points"] = $points_referral;
				update_user_meta($user_id_invite,$invite,$invite_meta);
				wpqa_add_points($user_id_invite,1,"+","",0,0,0,"invitations_completed",false);
				wpqa_add_points($user_id_invite,1,"-","",0,0,0,"invitations_pending",false);
				update_user_meta($user_id,"wpqa_invitations",$user_id_invite);
			}else {
				$rand = wpqa_token(15);
				$user_email = get_the_author_meta("user_email",$user_id);
				update_user_meta($user_id_invite,$rand,array("email" => $user_email,"status" => "completed","points" => $points_referral,"resend" => 0));
				$points_referrals_meta = get_user_meta($user_id_invite,"points_referrals",true);
				if (empty($points_referrals_meta)) {
					$update = update_user_meta($user_id_invite,"points_referrals",array($rand));
				}else if (is_array($points_referrals_meta) && !in_array($rand,$points_referrals_meta)) {
					$update = update_user_meta($user_id_invite,"points_referrals",array_merge($points_referrals_meta,array($rand)));
				}
				wpqa_add_points($user_id_invite,1,"+","",0,0,0,"invitations_completed",false);
				update_user_meta($user_id,"wpqa_invitations",$user_id_invite);
			}
			delete_user_meta($user_id,"wpqa_register_referral");
		}
	}
}
/* Send welcome mail */
function wpqa_send_welcome_mail($user_id) {
	$send_welcome_mail = wpqa_options("send_welcome_mail");
	if ($send_welcome_mail == "on") {
		$welcome_mail = get_user_meta($user_id,"welcome_mail",true);
		if ($welcome_mail == "") {
			$send_text = wpqa_send_mail(
				array(
					'content' => wpqa_options("email_welcome_mail"),
					'user_id' => $user_id,
				)
			);
			$email_title = wpqa_options("title_welcome_mail");
			$email_title = ($email_title != ""?$email_title:esc_html__("Welcome","wpqa"));
			$email_title = wpqa_send_mail(
				array(
					'content' => $email_title,
					'title'   => true,
					'break'   => '',
					'user_id' => $user_id,
				)
			);
			$user_email = get_the_author_meta("user_email",$user_id);
			$display_name = get_the_author_meta("display_name",$user_id);
			if ($user_email != "") {
				wpqa_send_mails(
					array(
						'toEmail'     => sanitize_text_field($user_email),
						'toEmailName' => sanitize_text_field($display_name),
						'title'       => $email_title,
						'message'     => $send_text,
					)
				);
			}
			update_user_meta($user_id,"welcome_mail","done");
		}
	}
}
/* Add gift points */
function wpqa_add_gift_points($user_id,$active_points) {
	$point_new_user = (int)wpqa_options("point_new_user");
	if ($user_id > 0 && $active_points == "on" && $point_new_user > 0) {
		$gift_site = get_user_meta($user_id,"gift_site",true);
		if ($gift_site == "") {
			wpqa_add_points($user_id,$point_new_user,"+","gift_site");
			wpqa_notifications_activities($user_id,"","","","","gift_site","notifications",$point_new_user." "._n("Point","Points",$point_new_user,"wpqa"));
			update_user_meta($user_id,"gift_site","done");
		}
	}
}
/* Sanitize user */
add_filter('sanitize_user','wpqa_sanitize_user',10,3);
if (!function_exists('wpqa_sanitize_user')) :
	function wpqa_sanitize_user($username,$raw_username,$strict) {
		if (!$strict) {
			return $username;
		}
		return wpqa_pre_user_nicename($raw_username);
	}
endif;
/* Per user nicename */
add_filter('pre_user_nickname','wpqa_pre_user_nicename');
add_filter('pre_user_nicename','wpqa_pre_user_nicename');
if (!function_exists('wpqa_pre_user_nicename')) :
	function wpqa_pre_user_nicename($username) {
		$username = esc_html(urldecode($username));
		$username = str_ireplace('','-',$username);
		return $username;
	}
endif;
/* Register URL */
add_filter('register_url','wpqa_register_url',10,1);
function wpqa_register_url() {
	return wpqa_signup_permalink();
}
/* Stop sent WordPress mail */
add_action('wpqa_init','wpqa_send_new_user_notifications');
function wpqa_send_new_user_notifications() {
	$new_user_notifications = apply_filters("wpqa_new_user_notifications",true);
	if ($new_user_notifications == true) {
		remove_action('register_new_user','wp_send_new_user_notifications');
	}
}
/* Register and edit profile fields */
function wpqa_register_edit_fields($key_items,$value_items,$type,$rand,$user = object,$valid_form = false) {
	$valid_form = (($valid_form == true && $type == "edit") || $type == "register"?true:false);
	$type_name = ($type == "register"?"_register":"");
	$out = '';
	$key_id = $key_items;
	if ($key_items == "image_profile") {
		$user_meta_avatar = wpqa_avatar_name();
		$key_id = "profile_picture";
	}else if ($key_items == "cover") {
		$user_meta_cover = wpqa_cover_name();
		$key_id = "profile_cover";
	}
	$key_required = wpqa_options($key_id."_required".$type_name);
	if ($type == "edit") {
		if ($key_items == "image_profile") {
			$key_id = $user_meta_avatar;
		}else if ($key_items == "cover") {
			$key_id = $user_meta_cover;
		}
		$user_meta = get_user_meta($user->ID,$key_id,true);
	}
	$readonly = ($type == "edit"?" readonly='readonly'":"");
	if ($key_items == "email" && isset($value_items["value"]) && $value_items["value"] == "email") {
		$out .= '<p class="'.$key_items.'_field">
			<label for="'.$key_items.'_'.$rand.'">'.esc_html__("E-Mail","wpqa").'<span class="required">*</span></label>
			<input'.$readonly.' class="form-control" autocomplete="email" type="text" name="'.$key_items.'" id="'.$key_items.'_'.$rand.'" value="'.(isset($_POST[$key_items]) && $valid_form == true?esc_attr($_POST[$key_items]):($type == "edit"?esc_attr($user->user_email):"")).'">
			<i class="icon-mail"></i>
		</p>';
		if ($type == "edit") {
			$confirm_edit_email = wpqa_options("confirm_edit_email");
			if ($confirm_edit_email == "on") {
				$edit_email = get_user_meta($user->ID,"wpqa_edit_email",true);
				if ($edit_email != "") {
					$out .= '<div class="alert-message alert-message-warning alert-confirm-email"><i class="icon-flag"></i><p>'.sprintf(esc_html__('There is a pending change of the email to %1$s. %2$s Cancel %3$s','wpqa'),$edit_email,'<a class="cancel-edit-email" data-nonce="'.wp_create_nonce("wpqa_cancel_edit_email").'" data-id="'.$user->ID.'" href="'.wpqa_edit_profile_permalink().'">','</a>').'</p></div>';
				}
			}
		}
	}else if ($key_items == "nickname" && isset($value_items["value"]) && $value_items["value"] == "nickname") {
		$out .= '<p class="'.$key_items.'_field">
			<label for="'.$key_items.'_'.$rand.'">'.esc_html__("Nickname","wpqa").'<span class="required">*</span></label>
			<input'.$readonly.' class="form-control" name="'.$key_items.'" id="'.$key_items.'_'.$rand.'" type="text" value="'.(isset($_POST[$key_items]) && $valid_form == true?esc_attr($_POST[$key_items]):($type == "edit"?esc_attr($user->nickname):"")).'">
			<i class="icon-vcard"></i>
		</p>';
	}else if ($key_items == "first_name" && isset($value_items["value"]) && $value_items["value"] == "first_name") {
		$out .= '<p class="'.$key_items.'_field">
			<label for="'.$key_items.'_'.$rand.'">'.esc_html__("First Name","wpqa").($key_required == "on"?'<span class="required">*</span>':'').'</label>
			<input class="form-control'.($key_required == "on"?' required-item':'').'"'.$readonly.' name="'.$key_items.'" id="'.$key_items.'_'.$rand.'" type="text" value="'.(isset($_POST[$key_items]) && $valid_form == true?esc_attr($_POST[$key_items]):($type == "edit"?esc_attr($user->first_name):"")).'">
			<i class="icon-user"></i>
		</p>';
	}else if ($key_items == "last_name" && isset($value_items["value"]) && $value_items["value"] == "last_name") {
		$out .= '<p class="'.$key_items.'_field">
			<label for="'.$key_items.'_'.$rand.'">'.esc_html__("Last Name","wpqa").($key_required == "on"?'<span class="required">*</span>':'').'</label>
			<input class="form-control'.($key_required == "on"?' required-item':'').'"'.$readonly.' name="'.$key_items.'" id="'.$key_items.'_'.$rand.'" type="text" value="'.(isset($_POST[$key_items]) && $valid_form == true?esc_attr($_POST[$key_items]):($type == "edit"?esc_attr($user->last_name):"")).'">
			<i class="icon-users"></i>
		</p>';
	}else if ($key_items == "display_name" && isset($value_items["value"]) && $value_items["value"] == "display_name") {
		$out .= '<p class="'.$key_items.'_field">
			<label for="'.$key_items.'_'.$rand.'">'.esc_html__("Display Name","wpqa").($key_required == "on"?'<span class="required">*</span>':'').'</label>
			<input class="form-control'.($key_required == "on"?' required-item':'').'"'.$readonly.' name="'.$key_items.'" id="'.$key_items.'_'.$rand.'" type="text" value="'.(isset($_POST[$key_items]) && $valid_form == true?esc_attr($_POST[$key_items]):($type == "edit"?esc_attr($user->display_name):"")).'">
			<i class="icon-user"></i>
		</p>';
		$out .= apply_filters('wpqa_edit_profile_after_names',false,($type == "edit"?$user->ID:""));
	}else if ($key_items == "image_profile" && isset($value_items["value"]) && $value_items["value"] == "image_profile") {
		if (isset($user->ID) && $user->ID > 0) {
			$out .= '<div class="clearfix"></div>
			<div class="author-image profile-image d-flex align-items-center mb-4">
				<span class="author-image-span wpqa-delete-image-span uploded-img mr-4">'.wpqa_get_user_avatar(array("user_id" => $user->ID,"size" => 100,"user_name" => $user->display_name)).'</span>';
				if (isset($user->ID) && $user->ID > 0 && ((!is_array($user_meta) && $user_meta != "") || (is_array($user_meta) && isset($user_meta["id"]) && $user_meta["id"] != 0))) {
					$out .= '<div class="clearfix"></div>
					<div class="button-default wpqa-remove-image btn btn__danger btn__small__width" data-name="'.$user_meta_avatar.'" data-type="user_meta" data-id="'.$user->ID.'" data-image="'.(is_array($user_meta) && isset($user_meta["id"]) && $user_meta["id"] != 0?$user_meta['id']:$user_meta).'" data-nonce="'.wp_create_nonce("wpqa_remove_image").'">'.esc_html__("Delete","wpqa").'</div>
					<div class="loader_2 loader_4"></div>';
				}
			$out .= '</div>';
		}
		$out .= '<label for="your_avatar_'.$rand.'">'.esc_html__('Profile Picture','wpqa').($key_required == "on"?'<span class="required">*</span>':'').'</label>
		<div class="fileinputs">
			<input type="file" name="'.$user_meta_avatar.'" id="your_avatar_'.$rand.'">
			<div class="fakefile">
				<button type="button">'.esc_html__('Select file','wpqa').'</button>
				<span>'.esc_html__('Browse','wpqa').'</span>
			</div>
			<i class="icon-camera"></i>
		</div>
		<div class="clearfix"></div>';
	}else if ($key_items == "cover" && isset($value_items["value"]) && $value_items["value"] == "cover") {
		if (isset($user->ID) && $user->ID > 0 && ((!is_array($user_meta) && $user_meta != "") || (is_array($user_meta) && isset($user_meta["id"]) && $user_meta["id"] != 0))) {
			$out .= '<div class="clearfix"></div>
			<div class="author-image profile-image d-flex align-items-center mb-4">
				<span class="author-image-span wpqa-delete-image-span uploded-img mr-4">'.wpqa_get_user_cover(array("user_id" => $user->ID,"size" => 100,"user_name" => $user->display_name)).'</span>
					<div class="clearfix"></div>
					<div class="button-default wpqa-remove-image btn btn__danger btn__small__width" data-name="'.$user_meta_cover.'" data-type="user_meta" data-id="'.$user->ID.'" data-image="'.(is_array($user_meta) && isset($user_meta["id"]) && $user_meta["id"] != 0?$user_meta['id']:$user_meta).'" data-nonce="'.wp_create_nonce("wpqa_remove_image").'">'.esc_html__("Delete","wpqa").'</div>
					<div class="loader_2 loader_4"></div>
			</div>';
		}
		$out .= '<label for="your_cover_'.$rand.'">'.esc_html__('Cover Picture','wpqa').($key_required == "on"?'<span class="required">*</span>':'').'</label>
		<div class="fileinputs">
			<input type="file" name="'.$user_meta_cover.'" id="your_cover_'.$rand.'">
			<div class="fakefile">
				<button type="button">'.esc_html__('Select file','wpqa').'</button>
				<span>'.esc_html__('Browse','wpqa').'</span>
			</div>
			<i class="icon-camera"></i>
		</div>
		<div class="clearfix"></div>';
	}else if ($key_items == "country" && isset($value_items["value"]) && $value_items["value"] == "country") {
		$get_countries = apply_filters('wpqa_get_countries',false);
		$out .= '<p class="'.$key_items.'_field">
			<label for="'.$key_items.'_'.$rand.'">'.esc_html__("Country","wpqa").($key_required == "on"?'<span class="required">*</span>':'').'</label>
			<span class="styled-select">
				<select name="'.$key_items.'" id="'.$key_items.'_'.$rand.'" class="form-control wpqa-custom-select '.($key_required == "on"?'required-item':'').'">
					<option value="">'.esc_html__( 'Select a country&hellip;', 'wpqa' ).'</option>';
						foreach( $get_countries as $key => $value ) {
							$out .= '<option value="' . esc_attr( $key ) . '"' . selected( (isset($_POST[$key_items]) && $valid_form == true?esc_attr($_POST[$key_items]):($type == "edit"?esc_attr($user_meta):"")), esc_attr( $key ), false ) . '>' . esc_attr( $value ) . '</option>';
						}
				$out .= '</select>
			</span>
			<i class="icon-location"></i>
		</p>';
	}else if ($key_items == "city" && isset($value_items["value"]) && $value_items["value"] == "city") {
		$out .= '<p class="'.$key_items.'_field">
			<label for="'.$key_items.'_'.$rand.'">'.esc_html__("City","wpqa").($key_required == "on"?'<span class="required">*</span>':'').'</label>
			<input class="form-control'.($key_required == "on"?' required-item':'').'"'.$readonly.' type="text" name="'.$key_items.'" id="'.$key_items.'_'.$rand.'" value="'.(isset($_POST[$key_items]) && $valid_form == true?esc_attr($_POST[$key_items]):($type == "edit"?esc_attr($user_meta):"")).'">
			<i class="icon-address"></i>
		</p>';
	}else if ($key_items == "phone" && isset($value_items["value"]) && $value_items["value"] == "phone") {
		$out .= apply_filters('wpqa_'.$type.'_before_phone',false,($type == "edit"?$user->ID:"")).'<p class="'.$key_items.'_field">
			<label for="'.$key_items.'_'.$rand.'">'.esc_html__("Phone","wpqa").($key_required == "on"?'<span class="required">*</span>':'').'</label>
			<input class="form-control'.($key_required == "on"?' required-item':'').'"'.$readonly.' type="text" name="'.$key_items.'" id="'.$key_items.'_'.$rand.'" value="'.(isset($_POST[$key_items]) && $valid_form == true?esc_attr($_POST[$key_items]):($type == "edit"?esc_attr($user_meta):"")).'">
			<i class="icon-phone"></i>
		</p>'.apply_filters('wpqa_'.$type.'_after_phone',false,($type == "edit"?$user->ID:""));
	}else if ($key_items == "gender" && isset($value_items["value"]) && $value_items["value"] == "gender") {
		$last_gender = (isset($_POST[$key_items]) && $valid_form == true && $_POST[$key_items]?esc_html($_POST[$key_items]):($type == "edit"?esc_html($user_meta):""));
		$gender_other = wpqa_options("gender_other");
		$out .= '<p class="'.$key_items.'_field wpqa_radio_p"><label>'.esc_html__("Gender","wpqa").($key_required == "on"?'<span class="required">*</span>':'').'</label></p>
		<div class="wpqa_radio_div '.($gender_other == "on"?'custom-radio-other':'custom-radio-container d-flex').'">
			<p class="wpqa_radio'.($gender_other == "on"?'':' custom-control custom-radio').'">
				<input'.($gender_other == "on"?'':' class="custom-control-input"').' id="'.$key_items.'_male_'.$rand.'" name="'.$key_items.'" type="radio" value="1"'.(($type != "edit" && !isset($_POST[$key_items])) || $last_gender == "male" || $last_gender == "1"?' checked="checked"':'').'>
				<label class="male_radio_label" for="'.$key_items.'_male_'.$rand.'">'.esc_html__("Male","wpqa").'</label>
			</p>
			<p class="wpqa_radio'.($gender_other == "on"?'':' custom-control custom-radio').'">
				<input'.($gender_other == "on"?'':' class="custom-control-input"').' id="'.$key_items.'_female_'.$rand.'" name="'.$key_items.'" type="radio" value="2"'.($last_gender == "female" || $last_gender == "2"?' checked="checked"':'').'>
				<label class="female_radio_label" for="'.$key_items.'_female_'.$rand.'">'.esc_html__("Female","wpqa").'</label>
			</p>';
			if ($gender_other == "on") {
				$out .= '<p>
					<span class="wpqa_radio"><input id="'.$key_items.'_other_'.$rand.'" name="'.$key_items.'" type="radio" value="3"'.($last_gender == "other" || $last_gender == "3"?' checked="checked"':'').'></span>
					<label class="other_radio_label" for="'.$key_items.'_other_'.$rand.'">'.esc_html__("Other","wpqa").'</label>
				</p>';
			}
			$out .= '<div class="clearfix"></div>
		</div>';
	}else if ($key_items == "age" && isset($value_items["value"]) && $value_items["value"] == "age") {
		$out .= '<p class="'.$key_items.'_field">
			<label for="'.$key_items.'_'.$rand.'">'.esc_html__("Age","wpqa").($key_required == "on"?'<span class="required">*</span>':'').'</label>
			<input'.$readonly.' type="text" class="form-control age-datepicker'.($key_required == "on"?' required-item':'').'" name="'.$key_items.'" id="'.$key_items.'_'.$rand.'" value="'.(isset($_POST[$key_items]) && $valid_form == true?esc_attr($_POST[$key_items]):($type == "edit"?esc_attr($user_meta):"")).'">
			<i class="icon-globe"></i>
		</p>';
	}
	return $out;
}
/* Register and edit profile errors */
add_action("wpqa_register_errors_main","wpqa_register_edit_profile_errors",1,6);
add_action("wpqa_edit_profile_errors_main","wpqa_register_edit_profile_errors",1,6);
function wpqa_register_edit_profile_errors($errors,$posted,$sort,$type,$user_id = 0,$data = array()) {
	$data = (is_array($data) && !empty($data)?$data:$_POST);
	$nickname = (isset($sort["nickname"]["value"]) && $sort["nickname"]["value"] == "nickname"?"on":0);
	$first_name = (isset($sort["first_name"]["value"]) && $sort["first_name"]["value"] == "first_name"?"on":0);
	$last_name = (isset($sort["last_name"]["value"]) && $sort["last_name"]["value"] == "last_name"?"on":0);
	$display_name = (isset($sort["display_name"]["value"]) && $sort["display_name"]["value"] == "display_name"?"on":0);
	$profile_picture = (isset($sort["image_profile"]["value"]) && $sort["image_profile"]["value"] == "image_profile"?"on":0);
	$profile_cover = (isset($sort["cover"]["value"]) && $sort["cover"]["value"] == "cover"?"on":0);
	$country = (isset($sort["country"]["value"]) && $sort["country"]["value"] == "country"?"on":0);
	$city = (isset($sort["city"]["value"]) && $sort["city"]["value"] == "city"?"on":0);
	$phone = (isset($sort["phone"]["value"]) && $sort["phone"]["value"] == "phone"?"on":0);
	$gender = (isset($sort["gender"]["value"]) && $sort["gender"]["value"] == "gender"?"on":0);
	$age = (isset($sort["age"]["value"]) && $sort["age"]["value"] == "age"?"on":0);
	$type_name = ($type == "register"?"_register":"");

	$allow_duplicate_names = wpqa_options("allow_duplicate_names");
	$allow_nickname = (isset($allow_duplicate_names["nickname"]["value"]) && $allow_duplicate_names["nickname"]["value"] == "nickname"?"on":0);
	$allow_display_name = (isset($allow_duplicate_names["display_name"]["value"]) && $allow_duplicate_names["display_name"]["value"] == "display_name"?"on":0);
	$first_name_required = wpqa_options("first_name_required".$type_name);
	$last_name_required = wpqa_options("last_name_required".$type_name);
	$display_name_required = wpqa_options("display_name_required".$type_name);
	$profile_picture_required = wpqa_options("profile_picture_required".$type_name);
	$profile_cover_required = wpqa_options("profile_cover_required".$type_name);
	$country_required = wpqa_options("country_required".$type_name);
	$city_required = wpqa_options("city_required".$type_name);
	$phone_required = wpqa_options("phone_required".$type_name);
	$gender_required = wpqa_options("gender_required".$type_name);
	$age_required = wpqa_options("age_required".$type_name);

	$user_meta_avatar = wpqa_avatar_name();
	$user_meta_cover = wpqa_cover_name();
	$profile_picture_size = (int)wpqa_options("profile_picture_size");
	$profile_cover_size = (int)wpqa_options("profile_cover_size");

	if ($type == "edit") { 
		$get_your_avatar = get_user_meta($user_id,$user_meta_avatar,true);
		$get_your_cover = get_user_meta($user_id,$user_meta_cover,true);
		$confirm_edit_email = wpqa_options("confirm_edit_email");
		if ($confirm_edit_email == "on") {
			$user = get_userdata($user_id);
			$user_email = $user->user_email;
			// Check the e-mail address
			if (!is_email($data['email'])) {
				$errors->add('right-email',esc_html__("Please write correctly email.","wpqa"));
			}else if ($data['email'] != $user_email && email_exists($data['email'])) {
				$errors->add('registered-email',esc_html__("This email is already registered, please choose another one.","wpqa"));
			}
		}
	}
	if (empty($data['nickname']) && $nickname === "on") {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Nickname).","wpqa"));
	}
	if ($allow_nickname !== "on" && isset($data['nickname']) && $data['nickname'] != "") {
		global $wpdb;
		$check_nickname = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->users as users, $wpdb->usermeta as meta WHERE users.ID = meta.user_id AND meta.meta_key = 'nickname' AND meta.meta_value = %s AND users.ID <> %d",$data['nickname'],$user_id));
		if ($check_nickname > 0) {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("This nickname is already available.","wpqa"));
		}
	}
	if (empty($data['first_name']) && $first_name === "on" && $first_name_required == "on") {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (First name).","wpqa"));
	}
	if (empty($data['last_name']) && $last_name === "on" && $last_name_required == "on") {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Last name).","wpqa"));
	}
	if (empty($data['display_name']) && $display_name === "on" && $display_name_required == "on") {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Display name).","wpqa"));
	}
	if ($allow_display_name !== "on" && isset($data['display_name']) && $data['display_name'] != "") {
		global $wpdb;
		$check_display_name = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->users WHERE display_name = %s AND ID <> %d",$data['display_name'],$user_id));
		if ($check_display_name > 0) {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("This display name is already available.","wpqa"));
		}
	}

	if (empty($_FILES[$user_meta_avatar]['name']) && ((empty($get_your_avatar) && $type == "edit") || $type == "register") && $profile_picture === "on" && $profile_picture_required == "on") {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Profile picture).","wpqa"));
	}
	if (empty($_FILES[$user_meta_cover]['name']) && ((empty($get_your_cover) && $type == "edit") || $type == "register") && $profile_cover === "on" && $profile_cover_required == "on") {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Cover picture).","wpqa"));
	}
	if (isset($_FILES[$user_meta_avatar]) && !empty($_FILES[$user_meta_avatar]['name'])) :
		$mime = $_FILES[$user_meta_avatar]["type"];
		$file_tmp = $_FILES[$user_meta_avatar]['tmp_name'];
		$size = filesize($file_tmp);
		if (!isset($data['mobile']) && $mime != 'image/jpeg' && $mime != 'image/jpg' && $mime != 'image/png' && $mime != 'image/webp') {
			$errors->add('upload-error', esc_html__('Error type, Please upload: jpg, jpeg or png','wpqa'));
		}else if ($profile_picture_size > 0 && $size > ($profile_picture_size*1000)) {
			$errors->add('upload-error', sprintf(esc_html__('Error size, The maximum size is %s MB.','wpqa'),floor($profile_picture_size/1000)));
		}
	endif;

	if (isset($_FILES[$user_meta_cover]) && !empty($_FILES[$user_meta_cover]['name'])) :
		$mime = $_FILES[$user_meta_cover]["type"];
		$file_tmp = $_FILES[$user_meta_cover]['tmp_name'];
		$size = filesize($file_tmp);
		if (!isset($data['mobile']) && $mime != 'image/jpeg' && $mime != 'image/jpg' && $mime != 'image/png' && $mime != 'image/webp') {
			$errors->add('upload-error', esc_html__('Error type, Please upload: jpg, jpeg or png','wpqa'));
		}else if ($profile_cover_size > 0 && $size > ($profile_cover_size*1000)) {
			$errors->add('upload-error', sprintf(esc_html__('Error size, The maximum size is %s MB.','wpqa'),floor($profile_cover_size/1000)));
		}
	endif;

	if (empty($data['country']) && $country === "on" && $country_required == "on") {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Country).","wpqa"));
	}
	if (empty($data['city']) && $city === "on" && $city_required == "on") {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (City).","wpqa"));
	}
	if (empty($data['phone']) && $phone === "on" && $phone_required == "on") {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Phone).","wpqa"));
	}
	if (empty($data['gender']) && $gender === "on" && $gender_required == "on") {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Gender).","wpqa"));
	}
	if (empty($data['age']) && $age === "on" && $age_required == "on") {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Age).","wpqa"));
	}
	return $errors;
}
/* Register and edit profile updated */
add_action("wpqa_personal_update_profile","wpqa_register_edit_profile_updated",1,4);
add_action("wpqa_after_register","wpqa_register_edit_profile_updated",1,4);
function wpqa_register_edit_profile_updated($user_id,$posted,$files,$type) {
	$user_meta_avatar = wpqa_avatar_name();
	$user_meta_cover = wpqa_cover_name();
	if (isset($files[$user_meta_avatar]) || isset($files[$user_meta_cover])) {
		require_once(ABSPATH.'wp-admin/includes/image.php');
		require_once(ABSPATH.'wp-admin/includes/file.php');
	}

	if (isset($files[$user_meta_avatar]) && !empty($files[$user_meta_avatar]['name'])) :
		$your_avatar = wp_handle_upload($files[$user_meta_avatar],array('test_form' => false),current_time('mysql'));
		if ($your_avatar && isset($your_avatar["url"])) :
			$filename = $your_avatar["file"];
			$filetype = wp_check_filetype( basename( $filename ), null );
			$wp_upload_dir = wp_upload_dir();
			
			$attachment = array(
				'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
				'post_mime_type' => $filetype['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);
			$attach_id = wp_insert_attachment($attachment,$filename);
			$attach_data = wp_generate_attachment_metadata($attach_id,$filename);
			wp_update_attachment_metadata($attach_id,$attach_data);
			$meta_for_avatar = $attach_id;
		endif;
		if (isset($your_avatar['error']) && $your_avatar) :
			if (isset($errors->add)) {
				$errors->add('upload-error', esc_html__('Error in upload the image : ','wpqa') . $your_avatar['error']);
				if ($errors->get_error_code()) return $errors;
			}
			return $errors;
		endif;
	elseif ($type == "edit") :
		$get_your_avatar = get_user_meta($user_id,$user_meta_avatar,true);
		$meta_for_avatar = $get_your_avatar;
	endif;
	if (isset($meta_for_avatar)) {
		update_user_meta($user_id,$user_meta_avatar,$meta_for_avatar);
	}

	if (isset($files[$user_meta_cover]) && !empty($files[$user_meta_cover]['name'])) :
		$your_cover = wp_handle_upload($files[$user_meta_cover],array('test_form' => false),current_time('mysql'));
		if ($your_cover && isset($your_cover["url"])) :
			$filename = $your_cover["file"];
			$filetype = wp_check_filetype( basename( $filename ), null );
			$wp_upload_dir = wp_upload_dir();
			
			$attachment = array(
				'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
				'post_mime_type' => $filetype['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);
			$attach_id = wp_insert_attachment($attachment,$filename);
			$attach_data = wp_generate_attachment_metadata($attach_id,$filename);
			wp_update_attachment_metadata($attach_id,$attach_data);
			$meta_for_cover = $attach_id;
		endif;
		if (isset($your_cover['error']) && $your_cover) :
			if (isset($errors->add)) {
				$errors->add('upload-error', esc_html__('Error in upload the image : ','wpqa') . $your_cover['error']);
				if ($errors->get_error_code()) return $errors;
			}
			return $errors;
		endif;
	elseif ($type == "edit") :
		$get_your_cover = get_user_meta($user_id,$user_meta_cover,true);
		$meta_for_cover = $get_your_cover;
	endif;
	if (isset($meta_for_cover)) {
		update_user_meta($user_id,$user_meta_cover,$meta_for_cover);
	}

	if ($type == "register") {
		$array_posts = array("first_name","last_name","country","city","phone","gender","age","subscribe_plan");
		foreach ($array_posts as $key => $value) {
			if (isset($posted[$value]) && $posted[$value] != "") {
				update_user_meta($user_id,$value,sanitize_text_field($posted[$value]));
			}
		}
	}
}
function wpqa_register_user_payment() {
	$output = '';
	$unlogged_pay = wpqa_options("unlogged_pay");
	if ($unlogged_pay == "on" && !is_user_logged_in()) {
		$output .= '<div class="register-payment-unlogged">
			<h3 class="post-title-3">'.esc_html__("Billing details","wpqa").'</h3>
			<form class="wpqa_form">
				<div class="wpqa_error_desktop wpqa_hide"><div class="wpqa_error"></div></div>
				<div class="wpqa_success"></div>
				<div class="form-inputs clearfix">
					<p class="first_name_payment_field">
						<label for="first_name_payment">'.esc_html__("First Name","wpqa").'<span class="required">*</span></label>
						<input class="form-control required-item" name="first_name_payment" id="first_name_payment" type="text">
						<i class="icon-user"></i>
					</p>
					<p class="last_name_payment_field">
						<label for="last_name_payment">'.esc_html__("Last Name","wpqa").'<span class="required">*</span></label>
						<input class="form-control required-item" name="last_name_payment" id="last_name_payment" type="text">
						<i class="icon-users"></i>
					</p>
					<p class="email_payment_field">
						<label for="email_payment">'.esc_html__("E-Mail","wpqa").'<span class="required">*</span></label>
						<input class="form-control required-item" autocomplete="email" type="text" name="email_payment" id="email_payment">
						<i class="icon-mail"></i>
					</p>
					<input type="hidden" name="action" value="wpqa_register_payment">
				</div>
			</form>
		</div>
		<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__('Check your email, the password will be sent to you.','wpqa').'</p></div>';
	}
	return $output;
}
add_action('wp_ajax_wpqa_register_payment','wpqa_register_payment');
add_action('wp_ajax_nopriv_wpqa_register_payment','wpqa_register_payment');
function wpqa_register_payment() {
	$result = array();
	if (!is_user_logged_in()) {
		$posted = (isset($data) && is_array($data) && !empty($data)?$data:$_POST);
		$posted = apply_filters('wpqa_register_posted',$posted);
		$posted = array_map('stripslashes',$posted);

		$first_name = (isset($posted["first_name_payment"])?sanitize_text_field($posted["first_name_payment"]):"");
		$last_name = (isset($posted["last_name_payment"])?sanitize_text_field($posted["last_name_payment"]):"");
		$user_email = (isset($posted["email_payment"])?sanitize_text_field($posted["email_payment"]):"");

		$errors = new WP_Error();
		if ($first_name == "") {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (First name).","wpqa"));
		}
		if ($last_name == "") {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Last name).","wpqa"));
		}
		if ($user_email == "") {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (email).","wpqa"));
		}
		if (!is_email($user_email)) {
			$errors->add('right-email',esc_html__("Please write correctly email.","wpqa"));
		}else if (email_exists($posted['email_payment'])) {
			$errors->add('registered-email',esc_html__("An account is already registered with your email address.","wpqa")." <a href='".wpqa_login_permalink()."' class='login-panel-document".apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_login','')."' target='_blank'>".esc_html__("Please log in.","wpqa")."</a>");
		}

		$errors = apply_filters('wpqa_register_payment_errors',$errors,$posted);

		if ($errors->get_error_code()) {
			$result['success'] = 0;
			foreach ($errors->errors as $error) {
				$result['error'] = $error[0];
				break;
			}
		}else {
			$user_name = trim(isset($first_name) && $first_name != ""?$first_name:"").trim(isset($last_name) && $last_name != ""?".".$last_name:"");
			$email_explode = explode("@",$user_email);
			$user_name = trim($user_name != ""?$user_name:(isset($email_explode[0])?$email_explode[0]:$user_email));
			$user_login = strtolower($user_name);
			$random_password = wp_generate_password(12,false);
			$default_group = wpqa_options("default_group");
			$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
			if (username_exists($user_login)) {
				$i = 1;
				$user_login_tmp = $user_login;
			do {
				$user_login_tmp = $user_login."_".($i++);
			}while(username_exists ($user_login_tmp));
				$user_login = $user_login_tmp;
			}

			$display_name = sanitize_text_field(trim(isset($first_name)?$first_name:"")." ".trim(isset($last_name)?$last_name:""));

			$user_data = apply_filters('wpqa_payment_insert_user',array(
				'user_login'   => $user_login,
				'display_name' => $display_name,
				'user_email'   => $user_email,
				'user_pass'    => $random_password,
				'first_name'   => trim(isset($first_name)?$first_name:""),
				'last_name'    => trim(isset($last_name)?$last_name:""),
				'role'         => $default_group
			));

			$result["name"] = $display_name;
			$result["email"] = $user_email;

			$user_id = wp_insert_user($user_data);
			if (is_wp_error($user_id)) {
				$result['success'] = 0;
				foreach ($user_id->errors as $error) {
					$result['error'] = $error[0];
					break;
				}
			}else {
				$result['success'] = 1;
				update_user_meta($user_id,"wpqa_registered_site","done");
				update_user_meta($user_id,"wpqa_stripe_nonce","done");
				do_action("wpqa_register_welcome",$user_id);
				wpqa_send_password($user_id);
				$activate_login = wpqa_options("activate_login");
				if ($activate_login != "disabled") {
					$secure_cookie = is_ssl() ? true : false;
					wp_cache_delete($user_id,'users');
					wp_cache_delete($user_login,'userlogins');
					wp_clear_auth_cookie();
					wp_set_auth_cookie($user_id,true,$secure_cookie);
					wpqa_ip_address(0,$user_id);
				}
			}
		}
	}else {
		$result['success'] = 1;
	}
	echo json_encode(apply_filters('wpqa_json_register_payment',$result));
	die();
}?>