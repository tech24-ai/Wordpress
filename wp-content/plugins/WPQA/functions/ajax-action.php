<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Site skin */
function wpqa_site_skin() {
	$site_skin = esc_html($_POST['site_skin']);
	if (is_user_logged_in()) {
		$user_id = get_current_user_id();
		update_user_meta($user_id,'wpqa_get_dark',$site_skin);
	}else {
		$uniqid_cookie = wpqa_options('uniqid_cookie');
		if (isset($_COOKIE[$uniqid_cookie.'wpqa_get_dark']) && $_COOKIE[$uniqid_cookie.'wpqa_get_dark'] == $site_skin) {
			unset($_COOKIE[$uniqid_cookie.'wpqa_get_dark']);
			setcookie($uniqid_cookie.'wpqa_get_dark',"",-1,COOKIEPATH,COOKIE_DOMAIN);
		}
		setcookie($uniqid_cookie.'wpqa_get_dark',$site_skin,time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
	}
	die();
}
add_action('wp_ajax_wpqa_site_skin','wpqa_site_skin');
add_action('wp_ajax_nopriv_wpqa_site_skin','wpqa_site_skin');
/* Fix the counter */
function wpqa_confirm_fix_comments() {
	$post_id = (int)$_POST["post_id"];
	wpqa_update_comments_count($post_id);
	die();
}
add_action('wp_ajax_wpqa_confirm_fix_comments','wpqa_confirm_fix_comments');
/* Delete the history */
function wpqa_confirm_delete_history() {
	$history_name = esc_html($_POST["history_name"]);
	$user_id = (int)$_POST["user_id"];
	$the_currency = get_option("the_currency");

	if ($history_name != "" && $user_id > 0) {
		$whats_type = $history_name;
		if ($whats_type == "notification" || $whats_type == "activity") {
			$args = array(
				"nopaging"   => true,
				"post_type"  => $whats_type,
				"author"     => $user_id
			);
			$get_posts = get_posts($args);
			foreach ($get_posts as $history_post) {
				wp_delete_post($history_post->ID,true);
			}
		}else {
			$_whats_types = get_user_meta($user_id,$user_id."_".$whats_type,true);
			if (isset($_whats_types) && $_whats_types > 0) {
				$count = ceil($_whats_types > 100?($_whats_types/100):$_whats_types);
				for ($i = 1; $i <= $count; $i++) {
					delete_user_meta($user_id,$user_id."_".$whats_type."_".$i);
					sleep(1);
				}
			}
			delete_user_meta($user_id,$user_id."_".$whats_type);
			if ($history_name == "points") {
				$start_of_week = get_option("start_of_week");
				if ($start_of_week == 0) {
					$start_of_week = "Sunday";
				}else if ($start_of_week == 1) {
					$start_of_week = "Monday";
				}else if ($start_of_week == 2) {
					$start_of_week = "Tuesday";
				}else if ($start_of_week == 3) {
					$start_of_week = "Wednesday";
				}else if ($start_of_week == 4) {
					$start_of_week = "Thursday";
				}else if ($start_of_week == 5) {
					$start_of_week = "Friday";
				}else if ($start_of_week == 6) {
					$start_of_week = "Saturday";
				}
				delete_user_meta($user_id,$whats_type);
				delete_user_meta($user_id,"points_date_".date("j-n-Y"));
				delete_user_meta($user_id,"points_date_".date("Y-m-d H:i:s",strtotime($start_of_week.' this week')));
				delete_user_meta($user_id,"points_date_".date("n-Y"));
				delete_user_meta($user_id,"points_date_".date("Y"));
				$categories_user_points = get_user_meta($user_id,"categories_user_points",true);
				if (is_array($categories_user_points) && !empty($categories_user_points)) {
					foreach ($categories_user_points as $category) {
						delete_user_meta($user_id,"points_category".$category);
						$_whats_types = get_user_meta($user_id,$user_id."_points_category".$category,true);
						if (isset($_whats_types) && $_whats_types > 0) {
							$count = ceil($_whats_types > 100?($_whats_types/100):$_whats_types);
							for ($i = 1; $i <= $count; $i++) {
								delete_user_meta($user_id,$user_id."_points_category_".$category.$i);
								delete_user_meta($user_id,"points_category".$category."_date_".date("j-n-Y"));
								delete_user_meta($user_id,"points_category".$category."_date_".date("Y-m-d H:i:s",strtotime($start_of_week.' this week')));
								delete_user_meta($user_id,"points_category".$category."_date_".date("n-Y"));
								delete_user_meta($user_id,"points_category".$category."_date_".date("Y"));
								sleep(1);
							}
							delete_user_meta($user_id,$user_id."_points_category".$category);
						}
					}
					delete_user_meta($user_id,"categories_user_points");
				}
			}
		}
	}
	die();
}
add_action('wp_ajax_wpqa_confirm_delete_history','wpqa_confirm_delete_history');
/* Fix the counts */
function wpqa_confirm_fix_counts() {
	if (isset($_POST["user_id"])) {
		$user_id = (int)$_POST["user_id"];
		wpqa_fix_counts($user_id);
	}else {
		wpqa_fix_counts();
	}
	die();
}
add_action('wp_ajax_wpqa_confirm_fix_counts','wpqa_confirm_fix_counts');
/* Send the custom mail */
add_action('wp_ajax_wpqa_send_custom_mail','wpqa_send_custom_mail');
function wpqa_send_custom_mail() {
	$mail_groups_users = wpqa_options("mail_groups_users");
	$custom_mail_groups = wpqa_options("custom_mail_groups");
	$mail_specific_users = wpqa_options("mail_specific_users");
	$specific_users = explode(",",$mail_specific_users);
	$groups_users = ($mail_groups_users == "users"?(is_array($specific_users) && !empty($specific_users)?$specific_users:array()):(isset($custom_mail_groups) && is_array($custom_mail_groups)?$custom_mail_groups:array()));
	$role_include = ($mail_groups_users == "users"?"include":"role__in");
	$email_title = wpqa_options("title_custom_mail");
	$email_title = ($email_title != ""?$email_title:esc_html__("Welcome","wpqa"));
	$users = get_users(array("meta_query" => array('relation' => 'OR',array("key" => "unsubscribe_mails","compare" => "NOT EXISTS"),array("key" => "unsubscribe_mails","compare" => "!=","value" => "on")),$role_include => $groups_users,"fields" => array("ID","user_email","display_name")));
	if (isset($users) && is_array($users) && !empty($users)) {
		foreach ($users as $key => $value) {
			$user_id = $value->ID;
			$send_text = wpqa_send_mail(
				array(
					'content'          => wpqa_options("email_custom_mail"),
					'received_user_id' => $user_id,
				)
			);
			$email_title = wpqa_send_mail(
				array(
					'content'          => $email_title,
					'title'            => true,
					'break'            => '',
					'received_user_id' => $user_id,
				)
			);
			$last_message_email = wpqa_email_code($send_text,"email_custom_mail");
			wpqa_send_mails(
				array(
					'toEmail'     => esc_html($value->user_email),
					'toEmailName' => esc_html($value->display_name),
					'title'       => $email_title,
					'message'     => $last_message_email,
					'email_code'  => 'code',
				)
			);
		}
	}
	die();
}
/* Send the custom notification */
add_action('wp_ajax_wpqa_send_custom_notification','wpqa_send_custom_notification');
function wpqa_send_custom_notification() {
	$active_notifications = wpqa_options("active_notifications");
	$notification_groups_users = wpqa_options("notification_groups_users");
	$custom_notification_groups = wpqa_options("custom_notification_groups");
	$notification_specific_users = wpqa_options("notification_specific_users");
	$specific_users = explode(",",$notification_specific_users);
	$groups_users = ($notification_groups_users == "users"?(is_array($specific_users) && !empty($specific_users)?$specific_users:array()):(isset($custom_notification_groups) && is_array($custom_notification_groups)?$custom_notification_groups:array()));
	$role_include = ($notification_groups_users == "users"?"include":"role__in");
	$custom_notification = wpqa_options("custom_notification");
	if ($active_notifications == "on" && $custom_notification != "") {
		$users = get_users(array($role_include => $groups_users,"fields" => array("ID")));
		if (isset($users) && is_array($users) && !empty($users)) {
			foreach ($users as $key => $value) {
				wpqa_notifications_activities($value->ID,"","","","",$custom_notification,"notifications");
			}
		}
	}
	die();
}
/* Send the custom message */
add_action('wp_ajax_wpqa_send_custom_message','wpqa_send_custom_message');
function wpqa_send_custom_message() {
	$active_message = wpqa_options("active_message");
	$message_groups_users = wpqa_options("message_groups_users");
	$custom_message_groups = wpqa_options("custom_message_groups");
	$message_specific_users = wpqa_options("message_specific_users");
	$specific_users = explode(",",$message_specific_users);
	$groups_users = ($message_groups_users == "users"?(is_array($specific_users) && !empty($specific_users)?$specific_users:array()):(isset($custom_message_groups) && is_array($custom_message_groups)?$custom_message_groups:array()));
	$role_include = ($message_groups_users == "users"?"include":"role__in");
	$title_custom_message = wpqa_options("title_custom_message");
	$custom_message = wpqa_options("custom_message");
	if ($active_message == "on" && ($title_custom_message != "" || $custom_message != "")) {
		$user_id = get_current_user_id();
		$users = get_users(array($role_include => $groups_users,"fields" => array("ID","user_email","display_name")));
		if (isset($users) && is_array($users) && !empty($users)) {
			$users_array = array();
			$insert_data = array(
				'post_content' => $custom_message,
				'post_title'   => sanitize_text_field($title_custom_message),
				'post_status'  => 'publish',
				'post_author'  => $user_id,
				'post_type'	   => 'message',
			);
			$post_id = wp_insert_post($insert_data);
			foreach ($users as $key => $value) {
				$users_array[] = $value->ID;
				update_post_meta($post_id,'message_user_'.$value->ID,$value->ID);
				update_post_meta($post_id,'message_not_new_'.$value->ID,"no");
				$new_messages_count = (int)get_user_meta((int)$value->ID,"wpqa_new_messages_count",true);
				$new_messages_count++;
				update_user_meta((int)$value->ID,"wpqa_new_messages_count",$new_messages_count);
				wpqa_notifications_activities($value->ID,"","","","","add_message","activities","","message");
				$header_messages = wpqa_options("header_messages");
				$header_style = wpqa_options("header_style");
				$show_message_area = ($header_messages == "on" && $header_style == "simple"?"on":0);
				wpqa_notifications_activities($value->ID,$user_id,"","","","add_message_user","notifications","","message",($show_message_area === "on"?false:true));
				$send_text = wpqa_send_mail(
					array(
						'content'          => wpqa_options("email_new_message"),
						'user_id'          => $value->ID,
						'post_id'          => $post_id,
						'sender_user_id'   => $user_id,
						'received_user_id' => $value->ID,
					)
				);
				$email_title = wpqa_options("title_new_message");
				$email_title = ($email_title != ""?$email_title:esc_html__("New message","wpqa"));
				$email_title = wpqa_send_mail(
					array(
						'content'          => $email_title,
						'title'            => true,
						'break'            => '',
						'user_id'          => $value->ID,
						'post_id'          => $post_id,
						'sender_user_id'   => $user_id,
						'received_user_id' => $value->ID,
					)
				);
				$unsubscribe_mails = get_the_author_meta('unsubscribe_mails',$value->ID);
				$send_message_mail = get_the_author_meta('send_message_mail',$value->ID);
				if ($unsubscribe_mails != "on" && $send_message_mail == "on") {
					wpqa_send_mails(
						array(
							'toEmail'     => $value->user_email,
							'toEmailName' => $value->display_name,
							'title'       => $email_title,
							'message'     => $send_text,
						)
					);
				}
			}
			if ($message_groups_users == "groups") {
				update_post_meta($post_id,'message_groups_array',$groups_users);
			}
			update_post_meta($post_id,'message_user_array',$users_array);
		}
	}
	die();
}
/* Send the popup notification */
add_action('wp_ajax_wpqa_send_popup_notification','wpqa_send_popup_notification');
function wpqa_send_popup_notification() {
	$post_id = (int)$_POST["post_id"];
	$custom_popup_notification = ($post_id > 0?get_post_meta($post_id,prefix_meta."custom_popup_notification",true):"");
	if ($custom_popup_notification == "on") {
		$popup_notification_text = get_post_meta($post_id,prefix_meta."popup_notification_text",true);
	}else {
		$popup_notification_text = wpqa_options("popup_notification_text");
	}
	if ($popup_notification_text != "") {
		if ($custom_popup_notification == "on") {
			$popup_notification_time = get_post_meta($post_id,prefix_meta."popup_notification_time",true);
			$popup_notification_groups_users = get_post_meta($post_id,prefix_meta."popup_notification_groups_users",true);
			$popup_notification_groups = get_post_meta($post_id,prefix_meta."popup_notification_groups",true);
			$popup_notification_specific_users = get_post_meta($post_id,prefix_meta."popup_notification_specific_users",true);
			$popup_notification_button_text = get_post_meta($post_id,prefix_meta."popup_notification_button_text",true);
			$popup_notification_button_url = get_post_meta($post_id,prefix_meta."popup_notification_button_url",true);
			$popup_notification_button_target = get_post_meta($post_id,prefix_meta."popup_notification_button_target",true);
			$popup_notification_home_pages = "";
			$popup_notification_pages = "";
		}else {
			$popup_notification_time = wpqa_options("popup_notification_time");
			$popup_notification_home_pages = wpqa_options("popup_notification_home_pages");
			$popup_notification_pages = wpqa_options("popup_notification_pages");
			$popup_notification_groups_users = wpqa_options("popup_notification_groups_users");
			$popup_notification_groups = wpqa_options("popup_notification_groups");
			$popup_notification_specific_users = wpqa_options("popup_notification_specific_users");
			$popup_notification_button_text = wpqa_options("popup_notification_button_text");
			$popup_notification_button_url = wpqa_options("popup_notification_button_url");
			$popup_notification_button_target = wpqa_options("popup_notification_button_target");
		}
		$specific_users = explode(",",$popup_notification_specific_users);
		$groups_users = ($popup_notification_groups_users == "users"?(is_array($specific_users) && !empty($specific_users)?$specific_users:array()):(isset($popup_notification_groups) && is_array($popup_notification_groups)?$popup_notification_groups:array()));
		$role_include = ($popup_notification_groups_users == "users"?"include":"role__in");
		$users = get_users(array($role_include => $groups_users,"fields" => array("ID")));
		if (isset($users) && is_array($users) && !empty($users)) {
			foreach ($users as $key => $value) {
				wpqa_notifications_activities($value->ID,"","","","",$popup_notification_text,"pop_notification","","","",
					array(
						"custom_notification" => $custom_popup_notification,
						"post_id"             => $post_id,
						"date_years"          => date_i18n('Y/m/d',current_time('timestamp')),
						"date_hours"          => date_i18n('g:i a',current_time('timestamp')),
						"time"                => current_time('timestamp'),
						"user_id"             => $value->ID,
						"text"                => $popup_notification_text,
						"notification_time"   => $popup_notification_time,
						"home_pages"          => $popup_notification_home_pages,
						"pages"               => $popup_notification_pages,
						"button_text"         => $popup_notification_button_text,
						"button_url"          => $popup_notification_button_url,
						"button_target"       => $popup_notification_button_target,
					)
				);
			}
		}
	}
	die();
}
/* Confirm delete attachment */
if (!function_exists('wpqa_confirm_delete_attachment')) :
	function wpqa_confirm_delete_attachment() {
		$attachment_id     = (int)$_POST["attachment_id"];
		$post_id           = (int)$_POST["post_id"];
		$single_attachment = esc_html($_POST["single_attachment"]);
		if ($single_attachment == "Yes") {
			wp_delete_attachment($attachment_id,true);
			delete_post_meta($post_id,'added_file');
		}else {
			$attachment_m = get_post_meta($post_id,'attachment_m',true);
			if (isset($attachment_m) && is_array($attachment_m) && !empty($attachment_m)) {
				foreach ($attachment_m as $key => $value) {
					if ($value["added_file"] == $attachment_id) {
						unset($attachment_m[$key]);
						wp_delete_attachment($value["added_file"],true);
					}
				}
			}
			if (isset($attachment_m) && is_array($attachment_m) && !empty($attachment_m)) {
				update_post_meta($post_id,'attachment_m',$attachment_m);
			}else {
				delete_post_meta($post_id,'attachment_m');
			}
		}
		die();
	}
endif;
add_action('wp_ajax_wpqa_confirm_delete_attachment','wpqa_confirm_delete_attachment');
add_action('wp_ajax_nopriv_wpqa_confirm_delete_attachment','wpqa_confirm_delete_attachment');
/* Question poll */
if (!function_exists('wpqa_question_poll')) :
	function wpqa_question_poll($data = array()) {
		$mobile = (is_array($data) && !empty($data)?true:false);
		$data = (is_array($data) && !empty($data)?$data:$_POST);
		$uniqid_cookie = wpqa_options("uniqid_cookie");
		$poll_user_only = wpqa_options("poll_user_only");
		$post_id = (int)$data['post_id'];
		$user_id = get_current_user_id();
		if (!is_user_logged_in() && $poll_user_only == "on") {
			$poll_error = "must_login";
		}else {
			$multicheck_poll = get_post_meta($post_id,"multicheck_poll",true);
			if ($multicheck_poll == "on") {
				$pollfrom = $data['pollfrom'];
			}else {
				$poll_id = (int)$data['poll_id'];
				$pollfrom[] = array('value' => $poll_id);
			}
			$question_poll = get_post_meta($post_id,'wpqa_question_poll',true);
			$question_poll = (is_array($question_poll) && !empty($question_poll)?$question_poll:array());
			$no_poll       = "";
			$poll_ids      = array();
			
			$asks = get_post_meta($post_id,"ask",true);
			
			$wpqa_poll = get_post_meta($post_id,"wpqa_poll",true);
			$wpqa_poll = (is_array($wpqa_poll) && !empty($wpqa_poll)?$wpqa_poll:array());
			
			if (isset($asks)) {
				foreach ($asks as $key_ask => $value_ask) {
					$wpqa_poll[$key_ask] = array(
						"id"       => (int)$asks[$key_ask]["id"],
						"title"    => (isset($value_ask["title"])?esc_html($value_ask["title"]):""),
						"value"    => (isset($asks[$key_ask]["value"]) && $asks[$key_ask]["value"] != ""?$asks[$key_ask]["value"]:(isset($wpqa_poll[$key_ask]["value"])?$wpqa_poll[$key_ask]["value"]:0)),
						"user_ids" => (isset($asks[$key_ask]["user_ids"])?$asks[$key_ask]["user_ids"]:(isset($wpqa_poll[$key_ask]["user_ids"])?$wpqa_poll[$key_ask]["user_ids"]:array()))
					);
				}
			}

			if (is_array($pollfrom) && !empty($pollfrom)) {
				foreach ($pollfrom as $key_form => $value_form) {
					if (isset($value_form["value"])) {
						$value_id    = str_replace("poll_","",esc_html($value_form["value"]));
						$poll_ids[]  = $value_id;
					}
				}
				$poll_ids = implode("_",$poll_ids);

				foreach ($pollfrom as $key_form => $value_form) {
					if (isset($value_form["value"])) {
						$value_id    = str_replace("poll_","",esc_html($value_form["value"]));
						$found_value = array_search($value_id,array_column($wpqa_poll,'id'));
						$found_key   = $found_value+1;
					}

					if ($found_key == $wpqa_poll[$found_key]["id"]) {
						$needle     = (isset($found_key) && isset($wpqa_poll[$found_key])?$wpqa_poll[$found_key]:array());
						$poll_title = (isset($needle["title"])?$needle["title"]:"");
						$value      = (isset($needle["value"])?$needle["value"]:"");
						$user_ids   = (isset($needle["user_ids"])?$needle["user_ids"]:"");
						$user_ids_end = $user_ids;
						if ((in_array($user_id,$wpqa_poll[$found_key]["user_ids"]) && $user_id > 0) || ($user_id == 0 && isset($_COOKIE[$uniqid_cookie.'wpqa_question_poll_'.$post_id.'_'.$poll_ids]) && $_COOKIE[$uniqid_cookie.'wpqa_question_poll_'.$post_id.'_'.$poll_ids] == "wpqa_yes_poll_it")) {
							$no_poll = "no_poll";
						}else {
							if ($value == "") {
								$value_end = 1;
							}else {
								$value_end = $value+1;
							}

							if ((is_array($user_ids) && empty($user_ids)) || !is_array($user_ids) || $user_ids == "") {
								$user_ids_end = array($user_id);
							}else if (is_array($user_ids) && !in_array($user_id,$user_ids)) {
								$user_ids_end = array_merge($user_ids,array($user_id));
							}
						}

						$wpqa_poll[$found_key] = array("id" => $wpqa_poll[$found_key]["id"],"value" => (isset($value_end) && $value_end > 0?$value_end:0),"user_ids" => $user_ids_end);
					}
				}
			}
			
			if ($no_poll != "no_poll") {
				update_post_meta($post_id,'wpqa_poll',$wpqa_poll);
				$question_poll_num = get_post_meta($post_id,'question_poll_num',true);
				$question_poll_num++;
				update_post_meta($post_id,'question_poll_num',$question_poll_num);
				
				$get_post = get_post($post_id);
				$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
				if (($get_post->post_author > 0 && $get_post->post_author != $user_id) || ($anonymously_user > 0 && $anonymously_user != $user_id)) {
					wpqa_notifications_activities(($get_post->post_author > 0?$get_post->post_author:$anonymously_user),($user_id > 0?$user_id:0),($user_id > 0?"":"unlogged"),$post_id,"","poll_question","notifications",$poll_title,wpqa_questions_type);
				}
				if ($user_id > 0) {
					wpqa_notifications_activities($user_id,"","",$post_id,"","poll_question","activities",$poll_title,wpqa_questions_type);
					$point_poll_question = wpqa_options("point_poll_question");
					if ($point_poll_question > 0) {
						wpqa_add_points($user_id,$point_poll_question,"+","polling_question",$post_id);
					}
				}
				$update = true;
			}else {
				if ($mobile == true) {
					return "no_poll";
				}
				$poll_error = "no_poll";
			}

			if (isset($update)) {
				if (is_user_logged_in()) {
					if (empty($question_poll)) {
						update_post_meta($post_id,"wpqa_question_poll",array($user_id));
					}else if (is_array($question_poll) && !in_array($user_id,$question_poll)) {
						update_post_meta($post_id,"wpqa_question_poll",array_merge($question_poll,array($user_id)));
					}
				}else {
					if (isset($_COOKIE[$uniqid_cookie.'wpqa_question_poll'.$post_id]) && $_COOKIE[$uniqid_cookie.'wpqa_question_poll'.$post_id] == "wpqa_yes_poll") {
						unset($_COOKIE[$uniqid_cookie.'wpqa_question_poll'.$post_id]);
						setcookie($uniqid_cookie.'wpqa_question_poll'.$post_id,"",-1,COOKIEPATH,COOKIE_DOMAIN);
					}
					setcookie($uniqid_cookie.'wpqa_question_poll'.$post_id,"wpqa_yes_poll",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
					setcookie($uniqid_cookie.'wpqa_question_poll_'.$post_id.'_'.$poll_ids,"wpqa_yes_poll_it",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
				}
			}

			if ($mobile == true) {
				return $wpqa_poll;
			}
		}
		if (isset($poll_error) && $poll_error != "") {
			if ($mobile == true) {
				return $poll_error;
			}else {
				echo ($poll_error);
			}
		}else {
			if ($mobile == true && isset($wpqa_poll)) {
				return $wpqa_poll;
			}
			echo wpqa_show_poll_results($post_id,$user_id,"results");
		}
		die();
	}
endif;
add_action('wp_ajax_wpqa_question_poll','wpqa_question_poll');
add_action('wp_ajax_nopriv_wpqa_question_poll','wpqa_question_poll');
/* Question vote up */
if (!function_exists('wpqa_question_vote_up')) :
	function wpqa_question_vote_up($data = array()) {
		$mobile = (is_array($data) && !empty($data)?true:false);
		$data = (is_array($data) && !empty($data)?$data:$_POST);
		$get_current_user_id = get_current_user_id();
		$id = (int)$data['id'];

		$count_up = get_post_meta($id,'wpqa_question_vote_up',true);
		$count_down = get_post_meta($id,'wpqa_question_vote_down',true);
		$count = get_post_meta($id,'question_vote',true);
		if ($count == "") {
			$count = 0;
		}
		$count_up = (isset($count_up) && is_array($count_up) && !empty($count_up)?$count_up:array());
		$count_down = (isset($count_down) && is_array($count_down) && !empty($count_down)?$count_down:array());
		
		if ((is_user_logged_in() && is_array($count_up) && in_array($get_current_user_id,$count_up)) || (!is_user_logged_in() && isset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_question_vote_up'.$id]) && $_COOKIE[wpqa_options("uniqid_cookie").'wpqa_question_vote_up'.$id] == "wpqa_yes")) {
			if ($mobile == true) {
				return esc_html__('Sorry, you cannot vote on the same question more than once.','wpqa');
			}
			echo "no_vote_more".wpqa_count_number($count);
		}else {
			$get_post = get_post($id);
			$user_id = $get_post->post_author;
			$point_voting_question = (int)wpqa_options("point_voting_question");
			$active_points = wpqa_options("active_points");
			
			if ($user_id != $get_current_user_id && $user_id > 0 && $point_voting_question > 0 && $active_points == "on") {
				$add_votes = get_user_meta($user_id,"add_votes_all",true);
				if ($add_votes == "" || $add_votes == 0) {
					update_user_meta($user_id,"add_votes_all",1);
				}else {
					update_user_meta($user_id,"add_votes_all",$add_votes+1);
				}
				wpqa_add_points($user_id,$point_voting_question,"+","voting_question",$id);
			}

			$gender_author = get_user_meta($get_current_user_id,'gender',true);
			$key = "other";
			if ($gender_author == 1) {
				$key = "male";
			}else if ($gender_author == 2) {
				$key = "female";
			}
			$value_up = 'wpqa_question_gender_'.$key.'_vote_up';
			$value_down = 'wpqa_question_gender_'.$key.'_vote_down';
			$count_gender_up = get_post_meta($id,$value_up,true);
			$count_gender_down = get_post_meta($id,$value_down,true);

			if (is_user_logged_in() && is_array($count_down) && in_array($get_current_user_id,$count_down)) {
				$count_gender_down--;
				update_post_meta($id,$value_down,($count_gender_down > 0?$count_gender_down:0));
				$count_down = wpqa_remove_item_by_value($count_down,$get_current_user_id);
				update_post_meta($id,"wpqa_question_vote_down",$count_down);
				$wpqa_question_vote_down = true;
			}
			
			if (!is_user_logged_in() && isset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_question_vote_down'.$id]) && $_COOKIE[wpqa_options("uniqid_cookie").'wpqa_question_vote_down'.$id] == "wpqa_yes") {
				unset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_question_vote_down'.$id]);
				setcookie(wpqa_options("uniqid_cookie").'wpqa_question_vote_down'.$id,"",-1,COOKIEPATH,COOKIE_DOMAIN);
				$wpqa_question_vote_down = true;
			}
			
			$count++;
			$update = update_post_meta($id,'question_vote',$count);

			if ($update && !isset($wpqa_question_vote_down)) {
				if (is_user_logged_in()) {
					if (empty($count_up)) {
						$update = update_post_meta($id,"wpqa_question_vote_up",array($get_current_user_id));
					}else if (is_array($count_up) && !in_array($get_current_user_id,$count_up)) {
						$update = update_post_meta($id,"wpqa_question_vote_up",array_merge($count_up,array($get_current_user_id)));
					}
					$count_gender_up++;
					update_post_meta($id,$value_up,$count_gender_up);
				}else {
					setcookie(wpqa_options("uniqid_cookie").'wpqa_question_vote_up'.$id,"wpqa_yes",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
				}

				wpqa_update_for_you($get_current_user_id,$id);
			}
			
			$anonymously_user = get_post_meta($id,"anonymously_user",true);
			if (($get_current_user_id > 0 && $user_id > 0) || ($get_current_user_id > 0 && $anonymously_user > 0)) {
				wpqa_notifications_activities(($user_id > 0?$user_id:$anonymously_user),$get_current_user_id,"",$id,"","question_vote_up","notifications","",wpqa_questions_type);
			}
			if ($get_current_user_id > 0) {
				wpqa_notifications_activities($get_current_user_id,"","",$id,"","question_vote_up","activities","",wpqa_questions_type);
			}
			if ($mobile == true) {
				return wpqa_count_number($count);
			}
			echo wpqa_count_number($count);
		}
		die();
	}
endif;
add_action('wp_ajax_wpqa_question_vote_up','wpqa_question_vote_up');
add_action('wp_ajax_nopriv_wpqa_question_vote_up','wpqa_question_vote_up');
/* Question vote down */
if (!function_exists('wpqa_question_vote_down')) :
	function wpqa_question_vote_down($data = array()) {
		$mobile = (is_array($data) && !empty($data)?true:false);
		$data = (is_array($data) && !empty($data)?$data:$_POST);
		$get_current_user_id = get_current_user_id();
		$id = (int)$data['id'];
		$count_up = get_post_meta($id,'wpqa_question_vote_up',true);
		$count_down = get_post_meta($id,'wpqa_question_vote_down',true);
		$count = get_post_meta($id,'question_vote',true);
		if ($count == "") {
			$count = 0;
		}
		$count_up = (isset($count_up) && is_array($count_up) && !empty($count_up)?$count_up:array());
		$count_down = (isset($count_down) && is_array($count_down) && !empty($count_down)?$count_down:array());
		
		if ((is_user_logged_in() && is_array($count_down) && in_array($get_current_user_id,$count_down)) || (!is_user_logged_in() && isset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_question_vote_down'.$id]) && $_COOKIE[wpqa_options("uniqid_cookie").'wpqa_question_vote_down'.$id] == "wpqa_yes")) {
			if ($mobile == true) {
				return esc_html__('Sorry, you cannot vote on the same question more than once.','wpqa');
			}
			echo "no_vote_more".wpqa_count_number($count);
		}else {
			$get_post = get_post($id);
			$user_id = $get_post->post_author;
			$point_voting_question = (int)wpqa_options("point_voting_question");
			$active_points = wpqa_options("active_points");
			
			if ($user_id != $get_current_user_id && $user_id > 0 && $point_voting_question > 0 && $active_points == "on") {
				$add_votes = get_user_meta($user_id,"add_votes_all",true);
				if ($add_votes == "" || $add_votes == 0) {
					update_user_meta($user_id,"add_votes_all",1);
				}else {
					update_user_meta($user_id,"add_votes_all",$add_votes+1);
				}
				wpqa_add_points($user_id,$point_voting_question,"-","voting_question",$id);
			}

			$gender_author = get_user_meta($get_current_user_id,'gender',true);
			$key = "other";
			if ($gender_author == 1) {
				$key = "male";
			}else if ($gender_author == 2) {
				$key = "female";
			}
			$value_up = 'wpqa_question_gender_'.$key.'_vote_up';
			$value_down = 'wpqa_question_gender_'.$key.'_vote_down';
			$count_gender_up = get_post_meta($id,$value_up,true);
			$count_gender_down = get_post_meta($id,$value_down,true);

			if (is_user_logged_in() && is_array($count_up) && in_array($get_current_user_id,$count_up)) {
				$count_gender_up--;
				update_post_meta($id,$value_up,($count_gender_up > 0?$count_gender_up:0));
				$count_up = wpqa_remove_item_by_value($count_up,$get_current_user_id);
				update_post_meta($id,"wpqa_question_vote_up",$count_up);
				$wpqa_question_vote_up = true;
			}
			
			if (!is_user_logged_in() && isset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_question_vote_up'.$id]) && $_COOKIE[wpqa_options("uniqid_cookie").'wpqa_question_vote_up'.$id] == "wpqa_yes") {
				unset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_question_vote_up'.$id]);
				setcookie(wpqa_options("uniqid_cookie").'wpqa_question_vote_up'.$id,"",-1,COOKIEPATH,COOKIE_DOMAIN);
				$wpqa_question_vote_up = true;
			}
			
			$count--;
			$update = update_post_meta($id,'question_vote',$count);

			if ($update && !isset($wpqa_question_vote_up)) {
				if (is_user_logged_in()) {
					if (empty($count_down)) {
						$update = update_post_meta($id,"wpqa_question_vote_down",array($get_current_user_id));
					}else if (is_array($count_down) && !in_array($get_current_user_id,$count_down)) {
						$update = update_post_meta($id,"wpqa_question_vote_down",array_merge($count_down,array($get_current_user_id)));
					}
					$count_gender_down++;
					update_post_meta($id,$value_down,$count_gender_down);
				}else {
					setcookie(wpqa_options("uniqid_cookie").'wpqa_question_vote_down'.$id,"wpqa_yes",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
				}

				wpqa_remove_for_you($get_current_user_id,$id);
			}
			
			$anonymously_user = get_post_meta($id,"anonymously_user",true);
			if (($get_current_user_id > 0 && $user_id > 0) || ($get_current_user_id > 0 && $anonymously_user > 0)) {
				wpqa_notifications_activities(($user_id > 0?$user_id:$anonymously_user),$get_current_user_id,"",$id,"","question_vote_down","notifications","",wpqa_questions_type);
			}
			if ($get_current_user_id > 0) {
				wpqa_notifications_activities($get_current_user_id,"","",$id,"","question_vote_down","activities","",wpqa_questions_type);
			}
			if ($mobile == true) {
				return wpqa_count_number($count);
			}
			echo wpqa_count_number($count);
		}
		die();
	}
endif;
add_action('wp_ajax_wpqa_question_vote_down','wpqa_question_vote_down');
add_action('wp_ajax_nopriv_wpqa_question_vote_down','wpqa_question_vote_down');
/* Comment vote up */
if (!function_exists('wpqa_comment_vote_up')) :
	function wpqa_comment_vote_up($data = array()) {
		$mobile = (is_array($data) && !empty($data)?true:false);
		$data = (is_array($data) && !empty($data)?$data:$_POST);
		$get_current_user_id = get_current_user_id();
		$id = (int)$data['id'];
		$count = get_comment_meta($id,'comment_vote',true);
		$count_up = get_comment_meta($id,'wpqa_comment_vote_up',true);
		$count_down = get_comment_meta($id,'wpqa_comment_vote_down',true);
		$count_up = (isset($count_up) && is_array($count_up) && !empty($count_up)?$count_up:array());
		$count_down = (isset($count_down) && is_array($count_down) && !empty($count_down)?$count_down:array());
		
		if (isset($count) && is_array($count) && isset($count["vote"])) {
			update_comment_meta($id,'comment_vote',$count["vote"]);
			$count = get_comment_meta($id,'comment_vote',true);
		}
		
		$count = (!empty($count)?$count:0);
		
		if ($count == "") {
			$count = 0;
		}
		
		if ((is_user_logged_in() && is_array($count_up) && in_array($get_current_user_id,$count_up)) || (!is_user_logged_in() && isset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_comment_vote_up'.$id]) && $_COOKIE[wpqa_options("uniqid_cookie").'wpqa_comment_vote_up'.$id] == "wpqa_yes_comment")) {
			if ($mobile == true) {
				return esc_html__('Sorry, you cannot vote on the same answer more than once.','wpqa');
			}
			echo "no_vote_more".wpqa_count_number($count);
		}else {
			$get_comment = get_comment($id);
			$post_id = $get_comment->comment_post_ID;
			$active_points = wpqa_options("active_points");
			$point_voting_answer = (int)wpqa_options("point_voting_answer");
			$user_votes_id = $get_comment->user_id;
			
			if ($user_votes_id != $get_current_user_id && $user_votes_id > 0 && $point_voting_answer > 0 && $active_points == "on") {
				$add_votes = get_user_meta($user_votes_id,"add_votes_all",true);
				if ($add_votes == "" || $add_votes == 0) {
					update_user_meta($user_votes_id,"add_votes_all",1);
				}else {
					update_user_meta($user_votes_id,"add_votes_all",$add_votes+1);
				}
				wpqa_add_points($user_votes_id,$point_voting_answer,"+","voting_answer",$post_id,$id);
			}
			
			$anonymously_user = get_comment_meta($id,"anonymously_user",true);
			if (($get_current_user_id > 0 && $user_votes_id > 0) || ($get_current_user_id > 0 && $anonymously_user > 0)) {
				wpqa_notifications_activities(($user_votes_id > 0?$user_votes_id:$anonymously_user),$get_current_user_id,"",$post_id,$id,"answer_vote_up","notifications","","answer");
			}

			if ($get_current_user_id > 0) {
				wpqa_notifications_activities($get_current_user_id,"","",$post_id,$id,"answer_vote_up","activities","","answer");
			}

			$gender_author = get_user_meta($get_current_user_id,'gender',true);
			$key = "other";
			if ($gender_author == 1) {
				$key = "male";
			}else if ($gender_author == 2) {
				$key = "female";
			}
			$value_up = 'wpqa_comment_gender_'.$key.'_vote_up';
			$value_down = 'wpqa_comment_gender_'.$key.'_vote_down';
			$count_gender_up = get_comment_meta($id,$value_up,true);
			$count_gender_down = get_comment_meta($id,$value_down,true);
			
			if (is_user_logged_in() && is_array($count_down) && in_array($get_current_user_id,$count_down)) {
				$count_gender_down--;
				update_comment_meta($id,$value_down,($count_gender_down > 0?$count_gender_down:0));
				$count_down = wpqa_remove_item_by_value($count_down,$get_current_user_id);
				update_comment_meta($id,"wpqa_comment_vote_down",$count_down);
				$wpqa_comment_vote_down = true;
			}
			
			if (!is_user_logged_in() && isset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_comment_vote_down'.$id]) && $_COOKIE[wpqa_options("uniqid_cookie").'wpqa_comment_vote_down'.$id] == "wpqa_yes_comment") {
				unset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_comment_vote_down'.$id]);
				setcookie(wpqa_options("uniqid_cookie").'wpqa_comment_vote_down'.$id,"",-1,COOKIEPATH,COOKIE_DOMAIN);
				$wpqa_comment_vote_down = true;
			}
			
			$count++;
			$update = update_comment_meta($id,'comment_vote',$count);

			if ($update && !isset($wpqa_comment_vote_down)) {
				if (is_user_logged_in()) {
					if (empty($count_up)) {
						$update = update_comment_meta($id,"wpqa_comment_vote_up",array($get_current_user_id));
					}else if (is_array($count_up) && !in_array($get_current_user_id,$count_up)) {
						$update = update_comment_meta($id,"wpqa_comment_vote_up",array_merge($count_up,array($get_current_user_id)));
					}
					$count_gender_up++;
					update_comment_meta($id,$value_up,$count_gender_up);
				}else {
					setcookie(wpqa_options("uniqid_cookie").'wpqa_comment_vote_up'.$id,"wpqa_yes_comment",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
				}

				wpqa_update_for_you($get_current_user_id,$post_id);
			}
			if ($mobile == true) {
				return wpqa_count_number($count);
			}
			echo wpqa_count_number($count);
		}
		die();
	}
endif;
add_action('wp_ajax_wpqa_comment_vote_up','wpqa_comment_vote_up');
add_action('wp_ajax_nopriv_wpqa_comment_vote_up','wpqa_comment_vote_up');
/* Comment vote down */
if (!function_exists('wpqa_comment_vote_down')) :
	function wpqa_comment_vote_down($data = array()) {
		$mobile = (is_array($data) && !empty($data)?true:false);
		$data = (is_array($data) && !empty($data)?$data:$_POST);
		$get_current_user_id = get_current_user_id();
		$id = (int)$data['id'];
		$count = get_comment_meta($id,'comment_vote',true);
		$count_up = get_comment_meta($id,'wpqa_comment_vote_up',true);
		$count_down = get_comment_meta($id,'wpqa_comment_vote_down',true);
		$count_up = (isset($count_up) && is_array($count_up) && !empty($count_up)?$count_up:array());
		$count_down = (isset($count_down) && is_array($count_down) && !empty($count_down)?$count_down:array());
		
		if (isset($count) && is_array($count) && isset($count["vote"])) {
			update_comment_meta($id,'comment_vote',$count["vote"]);
			$count = get_comment_meta($id,'comment_vote',true);
		}
		
		$count = (!empty($count)?$count:0);
		
		if ($count == "") {
			$count = 0;
		}
		
		if ((is_user_logged_in() && is_array($count_down) && in_array($get_current_user_id,$count_down)) || (!is_user_logged_in() && isset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_comment_vote_down'.$id]) && $_COOKIE[wpqa_options("uniqid_cookie").'wpqa_comment_vote_down'.$id] == "wpqa_yes_comment")) {
			if ($mobile == true) {
				return esc_html__('Sorry, you cannot vote on the same answer more than once.','wpqa');
			}
			echo "no_vote_more".wpqa_count_number($count);
		}else {
			$get_comment = get_comment($id);
			$post_id = $get_comment->comment_post_ID;
			$active_points = wpqa_options("active_points");
			$point_voting_answer = (int)wpqa_options("point_voting_answer");
			$user_votes_id = $get_comment->user_id;
			
			if ($user_votes_id != $get_current_user_id && $user_votes_id > 0 && $point_voting_answer > 0 && $active_points == "on") {
				$add_votes = get_user_meta($user_votes_id,"add_votes_all",true);
				if ($add_votes == "" || $add_votes == 0) {
					update_user_meta($user_votes_id,"add_votes_all",1);
				}else {
					update_user_meta($user_votes_id,"add_votes_all",$add_votes+1);
				}
				wpqa_add_points($user_votes_id,$point_voting_answer,"-","voting_answer",$post_id,$id);
			}
			
			$anonymously_user = get_comment_meta($id,"anonymously_user",true);
			if (($get_current_user_id > 0 && $user_votes_id > 0) || ($get_current_user_id > 0 && $anonymously_user > 0)) {
				wpqa_notifications_activities(($user_votes_id > 0?$user_votes_id:$anonymously_user),$get_current_user_id,"",$post_id,$id,"answer_vote_down","notifications","","answer");
			}
			
			if ($get_current_user_id > 0) {
				wpqa_notifications_activities($get_current_user_id,"","",$post_id,$id,"answer_vote_down","activities","","answer");
			}

			$gender_author = get_user_meta($get_current_user_id,'gender',true);
			$key = "other";
			if ($gender_author == 1) {
				$key = "male";
			}else if ($gender_author == 2) {
				$key = "female";
			}
			$value_up = 'wpqa_comment_gender_'.$key.'_vote_up';
			$value_down = 'wpqa_comment_gender_'.$key.'_vote_down';
			$count_gender_up = get_comment_meta($id,$value_up,true);
			$count_gender_down = get_comment_meta($id,$value_down,true);
			
			if (is_user_logged_in() && is_array($count_up) && in_array($get_current_user_id,$count_up)) {
				$count_gender_up--;
				update_comment_meta($id,$value_up,($count_gender_up > 0?$count_gender_up:0));
				$count_up = wpqa_remove_item_by_value($count_up,$get_current_user_id);
				update_comment_meta($id,"wpqa_comment_vote_up",$count_up);
				$wpqa_comment_vote_up = true;
			}
			
			if (!is_user_logged_in() && isset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_comment_vote_up'.$id]) && $_COOKIE[wpqa_options("uniqid_cookie").'wpqa_comment_vote_up'.$id] == "wpqa_yes_comment") {
				unset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_comment_vote_up'.$id]);
				setcookie(wpqa_options("uniqid_cookie").'wpqa_comment_vote_up'.$id,"",-1,COOKIEPATH,COOKIE_DOMAIN);
				$wpqa_comment_vote_up = true;
			}
			
			$count--;
			$update = update_comment_meta($id,'comment_vote',$count);

			if ($update && !isset($wpqa_comment_vote_up)) {
				if (is_user_logged_in()) {
					if (empty($count_down)) {
						$update = update_comment_meta($id,"wpqa_comment_vote_down",array($get_current_user_id));
					}else if (is_array($count_down) && !in_array($get_current_user_id,$count_down)) {
						$update = update_comment_meta($id,"wpqa_comment_vote_down",array_merge($count_down,array($get_current_user_id)));
					}
					$count_gender_down++;
					update_comment_meta($id,$value_down,$count_gender_down);
				}else {
					setcookie(wpqa_options("uniqid_cookie").'wpqa_comment_vote_down'.$id,"wpqa_yes_comment",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
				}

				wpqa_remove_for_you($get_current_user_id,$post_id);
			}
			if ($mobile == true) {
				return wpqa_count_number($count);
			}
			echo wpqa_count_number($count);
		}
		die();
	}
endif;
add_action('wp_ajax_wpqa_comment_vote_down','wpqa_comment_vote_down');
add_action('wp_ajax_nopriv_wpqa_comment_vote_down','wpqa_comment_vote_down');
/* Following you */
if (!function_exists('wpqa_following_you_ajax')) :
	function wpqa_following_you_ajax() {
		$following_nonce = esc_html($_POST["following_nonce"]);
		if (wp_verify_nonce($following_nonce,'wpqa_following_nonce')) {
			wpqa_following_you($_POST);
		}
	}
endif;
add_action('wp_ajax_wpqa_following_you_ajax','wpqa_following_you_ajax');
add_action('wp_ajax_nopriv_wpqa_following_you_ajax','wpqa_following_you_ajax');
if (!function_exists('wpqa_following_you')) :
	function wpqa_following_you($data = array()) {
		$user_id = get_current_user_id();
		$following_you_id = (int)$data["following_var_id"];
		if ($user_id > 0 && $user_id != $following_you_id) {
			$following_me_get = get_user_meta($user_id,"following_me",true);
			$following_me_get = (isset($following_me_get) && is_array($following_me_get) && !empty($following_me_get)?$following_me_get:array());
			
			if (isset($following_me_get) && !empty($following_me_get)) {
				$update = update_user_meta($user_id,"following_me",array_merge($following_me_get,array($following_you_id)));
			}else if (is_array($following_me_get) && !in_array($following_you_id,$following_me_get)) {
				$update = update_user_meta($user_id,"following_me",array($following_you_id));
			}
			
			if (isset($update)) {
				$active_points = wpqa_options("active_points");
				$point_following_me = (int)wpqa_options("point_following_me");
				
				$count_following_me = get_user_meta($user_id,"count_following_me",true);
				$count_following_me = ($count_following_me != "" || $count_following_me > 0?$count_following_me:count($following_me_get));
				$count_following_me++;
				$count_following_me = update_user_meta($user_id,"count_following_me",$count_following_me);

				if ($active_points == "on" && $point_following_me > 0) {
					wpqa_add_points($following_you_id,$point_following_me,"+","user_follow",0,0,$user_id);
				}
				
				if ($user_id > 0 && $following_you_id > 0) {
					wpqa_notifications_activities($following_you_id,$user_id,"","","","user_follow","notifications");
				}
				if ($user_id > 0) {
					wpqa_notifications_activities($user_id,$following_you_id,"","","","user_follow","activities");
				}
				
				$following_you_get = get_user_meta($following_you_id,"following_you",true);
				$following_you_get = (isset($following_you_get) && is_array($following_you_get) && !empty($following_you_get)?$following_you_get:array());
				
				if (isset($following_you_get) && !empty($following_you_get)) {
					$update = update_user_meta($following_you_id,"following_you",array_merge($following_you_get,array($user_id)));
				}else if (is_array($following_you_get) && !in_array($user_id,$following_you_get)) {
					$update = update_user_meta($following_you_id,"following_you",array($user_id));
				}

				$count_following_you = get_user_meta($following_you_id,"count_following_you",true);
				$count_following_you = ($count_following_you != "" || $count_following_you > 0?$count_following_you:count($following_you_get));
				$count_following_you++;
				$count_following_you = update_user_meta($following_you_id,"count_following_you",$count_following_you);
			}
			
			if (!isset($data["mobile"])) {
				$following_me = get_user_meta($following_you_id,"following_you",true);
				$following_me = (is_array($following_me) && !empty($following_me)?get_users(array('fields' => 'ID','include' => $following_me,'orderby' => 'registered')):array());
				echo (isset($following_me) && is_array($following_me)?wpqa_count_number(count($following_me)):0);
				die();
			}
		}
	}
endif;
/* Following not */
if (!function_exists('wpqa_following_not_ajax')) :
	function wpqa_following_not_ajax() {
		$following_nonce = esc_html($_POST["following_nonce"]);
		if (wp_verify_nonce($following_nonce,'wpqa_following_nonce')) {
			wpqa_following_not($_POST);
		}
	}
endif;
add_action('wp_ajax_wpqa_following_not_ajax','wpqa_following_not_ajax');
add_action('wp_ajax_nopriv_wpqa_following_not_ajax','wpqa_following_not_ajax');
if (!function_exists('wpqa_following_not')) :
	function wpqa_following_not($data = array()) {
		$user_id = get_current_user_id();
		$following_not_id = (int)$data["following_var_id"];
		if ($user_id > 0 && $user_id != $following_not_id) {
			$active_points = wpqa_options("active_points");
			$point_following_me = (int)wpqa_options("point_following_me");
			
			$following_me = get_user_meta($user_id,"following_me",true);
			$following_me = (isset($following_me) && is_array($following_me) && !empty($following_me)?$following_me:array());
			
			if (is_array($following_me) && in_array($following_not_id,$following_me)) {
				$count_following_me = get_user_meta($user_id,"count_following_me",true);
				$count_following_me = ($count_following_me != "" || $count_following_me > 0?$count_following_me:count($following_me));
				$count_following_me--;
				$count_following_me = update_user_meta($user_id,"count_following_me",$count_following_me);

				$remove_following_me = wpqa_remove_item_by_value($following_me,$following_not_id);
				update_user_meta($user_id,"following_me",$remove_following_me);
				if ($active_points == "on" && $point_following_me > 0) {
					wpqa_add_points($following_not_id,$point_following_me,"-","user_unfollow",0,0,$user_id);
				}
				
				if ($user_id > 0 && $following_not_id > 0) {
					wpqa_notifications_activities($following_not_id,$user_id,"","","","user_unfollow","notifications");
				}
				if ($user_id > 0) {
					wpqa_notifications_activities($user_id,$following_not_id,"","","","user_unfollow","activities");
				}
				
				$following_you = get_user_meta($following_not_id,"following_you",true);
				$following_you = (isset($following_you) && is_array($following_you) && !empty($following_you)?$following_you:array());
				if (isset($following_you) && !empty($following_you)) {
					$remove_following_you = wpqa_remove_item_by_value($following_you,$user_id);
					update_user_meta($following_not_id,"following_you",$remove_following_you);
				}

				$count_following_you = get_user_meta($following_not_id,"count_following_you",true);
				$count_following_you = ($count_following_you != "" || $count_following_you > 0?$count_following_you:count($following_you));
				$count_following_you--;
				$count_following_you = update_user_meta($following_not_id,"count_following_you",$count_following_you);
			}

			if (!isset($data["mobile"])) {
				$following_you = get_user_meta($following_not_id,"following_you",true);
				$following_you = (is_array($following_you) && !empty($following_you)?get_users(array('fields' => 'ID','include' => $following_you,'orderby' => 'registered')):array());
				echo (isset($following_you) && is_array($following_you)?wpqa_count_number(count($following_you)):0);
				die();
			}
		}
	}
endif;
/* Add point to question */
if (!function_exists('wpqa_add_point')) :
	function wpqa_add_point() {
		$input_add_point = (int)$_POST["input_add_point"];
		$post_id = (int)$_POST["post_id"];
		$user_id = get_current_user_id();
		$points_user = (int)get_user_meta($user_id,"points",true);
		$question_bump_points  = (int)wpqa_options("question_bump_points");
		$get_post = get_post($post_id);
		if ($user_id != $get_post->post_author) {
			$return = esc_html__("Sorry it was a mistake! This is not a your question.","wpqa");
		}else if ($points_user >= $input_add_point) {
			if ($input_add_point == "") {
				$return = esc_html__("You must enter a numeric value and a value greater than zero.","wpqa");
			}else if ($input_add_point <= 0) {
				$return = esc_html__("You must enter a numeric value and a value greater than zero.","wpqa");
			}else if ($input_add_point < $question_bump_points) {
				$return = sprintf(__("Sorry, you can't bumping your question with less than %s points.","wpqa"),$question_bump_points);
			}else {
				$question_points = (int)get_post_meta($post_id,"question_points",true);
				if ($question_points == 0) {
					$question_points = $input_add_point;
				}else {
					$question_points = $input_add_point+$question_points;
				}
				update_post_meta($post_id,"question_points",$question_points);
				wpqa_add_points($user_id,$input_add_point,"-","bump_question",$post_id);
				$return = "get_points";
				if ($user_id > 0) {
					wpqa_notifications_activities($user_id,"","",$post_id,"","bump_question","activities","",wpqa_questions_type);
				}
			}
		}else {
			$return = esc_html__("Your points are insufficient.","wpqa");
		}
		if (isset($_POST["mobile"])) {
			return $return;
		}else {
			echo ($return);
		}
		if (!isset($_POST["mobile"])) {
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_add_point','wpqa_add_point');
add_action('wp_ajax_nopriv_wpqa_add_point','wpqa_add_point');
/* Report question */
if (!function_exists('wpqa_report_q')) :
	function wpqa_report_q($data = array()) {
		$mobile = (is_array($data) && !empty($data)?true:false);
		$data = (is_array($data) && !empty($data)?$data:$_POST);
		if ($mobile != true) {
			check_ajax_referer('wpqa_report_nonce','wpqa_report_nonce');
		}
		$post_id  = (int)$data['post_id'];
		$get_post = get_post($post_id);
		$explain  = esc_html($data['explain']);
		$user_id  = get_current_user_id();
		if ($get_post->post_type == wpqa_questions_type || $get_post->post_type == wpqa_asked_questions_type) {
			/* New */
			$new_reports = get_option("new_reports");
			if ($new_reports == "" || !$new_reports) {
				$new_reports = 0;
			}
			$new_reports++;
			$update = update_option('new_reports',$new_reports);

			$new_question_reports = get_option("new_question_reports");
			if ($new_question_reports == "" || !$new_question_reports) {
				$new_question_reports = 0;
			}
			$new_question_reports++;
			$update = update_option('new_question_reports',$new_question_reports);

			if ($user_id > 0 && is_user_logged_in()) {
				$name_last = "";
				$id_last = $user_id;
			}else {
				$name_last = 1;
				$id_last = "";
			}

			$insert_data = array(
				'post_content' => $explain,
				'post_title'   => "",
				'post_status'  => "publish",
				'post_author'  => ($id_last > 0?$id_last:0),
				'post_type'    => 'report'
			);
			$insert_post_id = wp_insert_post($insert_data);
			if ($insert_post_id == 0 || is_wp_error($insert_post_id)) {
				error_log(esc_html__("Error in post.","wpqa"));
			}else {
				$variables = array(
					"report_post_id"    => $post_id,
					"report_type"       => "question",
					"report_the_author" => $name_last,
					"report_new"        => 1,
				);
				foreach ($variables as $key => $value) {
					update_post_meta($insert_post_id,$key,$value);
				}
				wpqa_ip_address($insert_post_id);
			}

			$send_text = wpqa_send_mail(
				array(
					'content' => wpqa_options("email_report_question"),
					'post_id' => $post_id,
				)
			);
			$email_title = wpqa_options("title_report_question");
			$email_title = ($email_title != ""?$email_title:esc_html__("Report Question","wpqa"));
			$email_title = wpqa_send_mail(
				array(
					'content' => $email_title,
					'title'   => true,
					'break'   => '',
					'post_id' => $post_id,
				)
			);
			wpqa_send_mails(
				array(
					'title'   => $email_title,
					'message' => $send_text,
				)
			);
			do_action("wpqa_report_question",$get_post,$explain,$user_id);
			if ($user_id > 0) {
				$active_trash_reports = wpqa_options("active_trash_reports");
				$active_points = wpqa_options("active_points");
				if ($active_trash_reports == "on" && $active_points == "on") {
					$trash_reports_points = (int)wpqa_options("trash_reports_points");
					$points = (int)get_user_meta($user_id,"points",true);
					if ($trash_reports_points > 0 && $points >= $trash_reports_points) {
						$post_author = $get_post->post_author;
						$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
						
						$reports_min_points = (int)wpqa_options("reports_min_points");
						$points_2 = (int)($reports_min_points > 0?get_user_meta(($post_author > 0?$post_author:$anonymously_user),"points",true):0);
						
						$whitelist_questions = wpqa_options("whitelist_questions");
						$whitelist_questions = ($whitelist_questions != ""?explode(",",$whitelist_questions):array());
						$trash_by_report = false;
						if ($reports_min_points > 0 && $points_2 < $reports_min_points) {
							$trash_by_report = true;
						}
						
						if (in_array($post_id,$whitelist_questions)) {
							$trash_by_report = false;
						}
						
						if ($trash_by_report == true) {
							$trash_draft_reports = wpqa_options("trash_draft_reports");
							if ($trash_draft_reports == "draft") {
								global $wpdb;
								$wpdb->update($wpdb->posts,array('post_status' => 'draft'),array('ID' => $post_id));
								clean_post_cache($post_id);
								wp_transition_post_status('draft','publish',$get_post);
								if ($mobile == true) {
									$mobile_message = "reviewed_question";
								}else {
									if (!session_id() && !headers_sent()) {
										session_start();
									}
									wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("You reported a question. It will be reviewed shortly, The question is under review.","wpqa").'</p></div>','wpqa_session');
								}
								wpqa_notifications_activities(($post_author > 0?$post_author:$anonymously_user),"","","","","question_review","notifications","",wpqa_questions_type);
							}else {
								wp_trash_post($post_id);
								if ($mobile == true) {
									$mobile_message = "successfully";
								}else {
									if (!session_id() && !headers_sent()) {
										session_start();
									}
									wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Deleted successfully.","wpqa").'</p></div>','wpqa_session');
								}
								wpqa_notifications_activities(($post_author > 0?$post_author:$anonymously_user),"","","","","delete_question","notifications","",wpqa_questions_type);
							}
							echo "deleted_report";
						}
					}
				}
				wpqa_notifications_activities($user_id,"","",$post_id,"","report_question","activities","",wpqa_questions_type);
				if ($mobile == true) {
					$mobile_message = "thank_you";
				}else {
					if (!session_id() && !headers_sent()) {
						session_start();
					}
					wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Thank you, your report will be reviewed shortly.","wpqa").'</p></div>','wpqa_session');
				}
				if (isset($mobile_message)) {
					return $mobile_message;
				}
			}
		}else {
			do_action("wpqa_report_post",$get_post,$explain,$user_id);
		}
		if ($mobile != true) {
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_report_q','wpqa_report_q');
add_action('wp_ajax_nopriv_wpqa_report_q','wpqa_report_q');
/* Report comment */
if (!function_exists('wpqa_report_c')) :
	function wpqa_report_c($data = array()) {
		$mobile = (is_array($data) && !empty($data)?true:false);
		$data = (is_array($data) && !empty($data)?$data:$_POST);
		if ($mobile != true) {
			check_ajax_referer('wpqa_report_nonce','wpqa_report_nonce');
		}
		$comment_id = (int)$data['report_id'];
		$explain    = esc_html($data['explain']);
		$post_id    = (int)$data['post_id'];
		$get_post   = get_post($post_id);
		$user_id    = get_current_user_id();
		if ($get_post->post_type == wpqa_questions_type || $get_post->post_type == wpqa_asked_questions_type) {
			/* option */
			/* New */
			$new_reports = get_option("new_reports");
			if ($new_reports == "" || !$new_reports) {
				$new_reports = 0;
			}
			$new_reports++;
			$update = update_option('new_reports',$new_reports);

			$new_answer_reports = get_option("new_answer_reports");
			if ($new_answer_reports == "" || !$new_answer_reports) {
				$new_answer_reports = 0;
			}
			$new_answer_reports++;
			$update = update_option('new_answer_reports',$new_answer_reports);

			if ($user_id > 0 && is_user_logged_in()) {
				$name_last = "";
				$id_last = $user_id;
			}else {
				$name_last = 1;
				$id_last = "";
			}

			$insert_data = array(
				'post_content' => $explain,
				'post_title'   => "",
				'post_status'  => "publish",
				'post_author'  => ($id_last > 0?$id_last:0),
				'post_type'    => 'report'
			);
			$insert_post_id = wp_insert_post($insert_data);
			if ($insert_post_id == 0 || is_wp_error($insert_post_id)) {
				error_log(esc_html__("Error in post.","wpqa"));
			}else {
				$variables = array(
					"report_post_id"    => $post_id,
					"report_comment_id" => $comment_id,
					"report_type"       => "answer",
					"report_the_author" => $name_last,
					"report_new"        => 1,
				);
				foreach ($variables as $key => $value) {
					update_post_meta($insert_post_id,$key,$value);
				}
				wpqa_ip_address($insert_post_id);
			}

			$send_text = wpqa_send_mail(
				array(
					'content'    => wpqa_options("email_report_answer"),
					'post_id'    => $post_id,
					'comment_id' => $comment_id,
				)
			);
			$email_title = wpqa_options("title_report_answer");
			$email_title = ($email_title != ""?$email_title:esc_html__("Report Answer","wpqa"));
			$email_title = wpqa_send_mail(
				array(
					'content'    => $email_title,
					'title'      => true,
					'break'      => '',
					'post_id'    => $post_id,
					'comment_id' => $comment_id,
				)
			);
			wpqa_send_mails(
				array(
					'title'   => $email_title,
					'message' => $send_text,
				)
			);
			do_action("wpqa_report_answer",$comment_id,$get_post,$explain,$user_id);
			if ($user_id > 0) {
				$active_trash_reports = wpqa_options("active_trash_reports");
				$active_points = wpqa_options("active_points");
				if ($active_trash_reports == "on" && $active_points == "on") {
					$trash_reports_points = (int)wpqa_options("trash_reports_points");
					$points = (int)get_user_meta($user_id,"points",true);
					if ($trash_reports_points > 0 && $points >= $trash_reports_points) {
						$get_comment = get_comment($comment_id);
						$comment_user = $get_comment->user_id;
						$anonymously_user = get_comment_meta($comment_id,'anonymously_user',true);
						
						$reports_min_points = (int)wpqa_options("reports_min_points");
						$points_2 = (int)($reports_min_points > 0?get_user_meta(($comment_user > 0?$comment_user:$anonymously_user),"points",true):0);
						
						$whitelist_answers = wpqa_options("whitelist_answers");
						$whitelist_answers = ($whitelist_answers != ""?explode(",",$whitelist_answers):array());
						$trash_by_report = false;
						if ($reports_min_points > 0 && $points_2 < $reports_min_points) {
							$trash_by_report = true;
						}
						
						if (in_array($comment_id,$whitelist_answers)) {
							$trash_by_report = false;
						}
						
						if ($trash_by_report == true) {
							$trash_draft_reports = wpqa_options("trash_draft_reports");
							if ($trash_draft_reports == "draft") {
								wp_set_comment_status($comment_id,0);
								if ($mobile == true) {
									$mobile_message = "reviewed_answer";
								}else {
									if (!session_id() && !headers_sent()) {
										session_start();
									}
									wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("You reported an answer. It will be reviewed shortly, The answer is under review.","wpqa").'</p></div>','wpqa_session');
								}
								wpqa_notifications_activities(($get_comment->user_id > 0?$get_comment->user_id:$anonymously_user),"","","","","answer_review","notifications","","answer");
							}else {
								wp_trash_comment($comment_id);
								if ($mobile == true) {
									$mobile_message = "successfully";
								}else {
									if (!session_id() && !headers_sent()) {
										session_start();
									}
									wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Deleted successfully.","wpqa").'</p></div>','wpqa_session');
								}
								wpqa_notifications_activities(($get_comment->user_id > 0?$get_comment->user_id:$anonymously_user),"","","","","delete_answer","notifications","","answer");
							}
							if ($mobile != true) {
								echo "deleted_report";
							}
						}
					}
				}
				wpqa_notifications_activities($user_id,"","",$post_id,$comment_id,"report_answer","activities","","answer");
				if ($mobile == true) {
					$mobile_message = "thank_you";
				}else {
					if (!session_id() && !headers_sent()) {
						session_start();
					}
					wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Thank you, your report will be reviewed shortly.","wpqa").'</p></div>','wpqa_session');
				}
				if (isset($mobile_message)) {
					return $mobile_message;
				}
			}
		}else {
			do_action("wpqa_report_comment",$comment_id,$get_post,$explain,$user_id);
		}
		if ($mobile != true) {
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_report_c','wpqa_report_c');
add_action('wp_ajax_nopriv_wpqa_report_c','wpqa_report_c');
/* Report user */
if (!function_exists('wpqa_report_user')) :
	function wpqa_report_user($data = array()) {
		$mobile = (is_array($data) && !empty($data)?true:false);
		$data = (is_array($data) && !empty($data)?$data:$_POST);
		if ($mobile != true) {
			check_ajax_referer('wpqa_report_nonce','wpqa_report_nonce');
		}
		$author_id = (int)$data['report_id'];
		$explain   = esc_html($data['explain']);
		$user_id   = get_current_user_id();
		$wpqa_profile_url = wpqa_profile_url($author_id);
		$display_name = get_the_author_meta('display_name',$author_id);
		if ($wpqa_profile_url != "" && $display_name != "") {
			/* option */
			/* New */
			$new_reports = get_option("new_reports");
			if ($new_reports == "" || !$new_reports) {
				$new_reports = 0;
			}
			$new_reports++;
			$update = update_option('new_reports',$new_reports);

			$new_user_reports = get_option("new_user_reports");
			if ($new_user_reports == "" || !$new_user_reports) {
				$new_user_reports = 0;
			}
			$new_user_reports++;
			$update = update_option('new_user_reports',$new_user_reports);

			if ($user_id > 0 && is_user_logged_in()) {
				$name_last = "";
				$id_last = $user_id;
			}else {
				$name_last = 1;
				$id_last = "";
			}

			$insert_data = array(
				'post_content' => $explain,
				'post_title'   => "",
				'post_status'  => "publish",
				'post_author'  => ($id_last > 0?$id_last:0),
				'post_type'    => 'report'
			);
			$insert_post_id = wp_insert_post($insert_data);
			if ($insert_post_id == 0 || is_wp_error($insert_post_id)) {
				error_log(esc_html__("Error in post.","wpqa"));
			}else {
				$variables = array(
					"report_user_id"    => $author_id,
					"report_type"       => "user",
					"report_the_author" => $name_last,
					"report_new"        => 1,
				);
				foreach ($variables as $key => $value) {
					update_post_meta($insert_post_id,$key,$value);
				}
				wpqa_ip_address($insert_post_id);
			}

			$send_text = wpqa_send_mail(
				array(
					'content'        => wpqa_options("email_report_user"),
					'sender_user_id' => $author_id,
				)
			);
			$email_title = wpqa_options("title_report_user");
			$email_title = ($email_title != ""?$email_title:esc_html__("Report User","wpqa"));
			$email_title = wpqa_send_mail(
				array(
					'content'        => $email_title,
					'title'          => true,
					'break'          => '',
					'sender_user_id' => $author_id,
				)
			);
			wpqa_send_mails(
				array(
					'title'   => $email_title,
					'message' => $send_text,
				)
			);
			do_action("wpqa_report_user",$author_id,$explain,$user_id);
			if ($user_id > 0) {
				wpqa_notifications_activities($user_id,$author_id,"","","","report_user","activities","","user");
				if ($mobile == true) {
					$mobile_message = "thank_you";
				}else {
					if (!session_id() && !headers_sent()) {
						session_start();
					}
					wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Thank you, your report will be reviewed shortly.","wpqa").'</p></div>','wpqa_session');
				}
				if (isset($mobile_message)) {
					return $mobile_message;
				}
			}
		}
		if ($mobile != true) {
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_report_user','wpqa_report_user');
add_action('wp_ajax_nopriv_wpqa_report_user','wpqa_report_user');
/* Choose best answer */
if (!function_exists('wpqa_best_answer_a')) :
	function wpqa_best_answer_a() {
		if (!isset($_POST["mobile"])) {
			check_ajax_referer('wpqa_best_answer_nonce','wpqa_best_answer_nonce');
		}
		$comment_id = (int)$_POST['comment_id'];
		$get_comment = get_comment($comment_id);
		$user_id = $get_comment->user_id;
		$post_id = $get_comment->comment_post_ID;
		$the_best_answer = get_post_meta($post_id,"the_best_answer",true);
		if (isset($the_best_answer) && $the_best_answer != "" && $the_best_answer > 0) {
			if (!isset($_POST["mobile"])) {
				echo esc_html($the_best_answer);
			}
		}else {
			$get_current_user_id = get_current_user_id();
			$is_super_admin = is_super_admin($get_current_user_id);
			$get_post = get_post($post_id);
			$user_author = $get_post->post_author;
			$user_best_answer_filter = apply_filters("wpqa_user_best_answer_filter",true);
			$active_best_answer = wpqa_options("active_best_answer");
			$best_answer_userself = wpqa_options("best_answer_userself");
			$user_best_answer = esc_html(get_the_author_meta('user_best_answer',$get_current_user_id));
			$moderators_permissions = wpqa_user_moderator($get_current_user_id);
			$moderator_categories = wpqa_user_moderator_categories($get_current_user_id,$post_id);
			if ($user_best_answer_filter == true && ((is_user_logged_in() && $active_best_answer == "on" && $get_current_user_id > 0 && (($user_id != $get_current_user_id && $get_current_user_id == $user_author) || ($best_answer_userself == "on" && $user_id == $get_current_user_id && $get_current_user_id == $user_author))) || $user_best_answer == "on" || ($moderator_categories == true && isset($moderators_permissions['best']) && $moderators_permissions['best'] == "best") || $is_super_admin)) {
				update_post_meta($post_id,"the_best_answer",$comment_id);
				$active_points = wpqa_options("active_points");
				if ($user_id > 0) {
					if ($user_id != $user_author && $active_points == "on") {
						$point_best_answer = (int)wpqa_options("point_best_answer");
						$point_best_answer = apply_filters("wpqa_point_best_answer",$point_best_answer,$post_id);
						$bump_best_answer = wpqa_options("bump_best_answer");
						if ($bump_best_answer == "on") {
							$question_points = (int)get_post_meta($post_id,"question_points",true);
							$point_best_answer = ($question_points > 0?$question_points:$point_best_answer);
						}
						if ($point_best_answer > 0) {
							wpqa_add_points($user_id,$point_best_answer,"+","select_best_answer",$post_id,$comment_id);
						}
					}
					
					$best_answer_user = get_user_meta($user_id,"best_answer_user",true);
					if (empty($best_answer_user)) {
						update_user_meta($user_id,"best_answer_user",array($comment_id));
					}else if (is_array($best_answer_user) && !in_array($comment_id,$best_answer_user)) {
						update_user_meta($user_id,"best_answer_user",array_merge($best_answer_user,array($comment_id)));
					}
					$wpqa_count_best_answers = (int)get_user_meta($user_id,"wpqa_count_best_answers",true);
					$wpqa_count_best_answers++;
					update_user_meta($user_id,"wpqa_count_best_answers",($wpqa_count_best_answers < 0?0:$wpqa_count_best_answers));
				}
				$wpqa_best_answer = (int)get_option("wpqa_best_answer");
				$wpqa_best_answer++;
				update_option("wpqa_best_answer",($wpqa_best_answer < 0?0:$wpqa_best_answer));
				update_comment_meta($comment_id,"best_answer_comment","best_answer_comment");
				
				$point_back_option = wpqa_options("point_back");
				$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
				if ($point_back_option == "on" && $active_points == "on" && ($is_super_admin || ($user_id != $user_author && $user_author > 0) || ($user_id != $anonymously_user && $anonymously_user > 0))) {
					$point_back_number = (int)wpqa_options("point_back_number");
					$point_back = get_post_meta($post_id,"point_back",true);
					$what_point = (int)get_post_meta($post_id,"what_point",true);
					
					if ($point_back_number > 0) {
						$what_point = $point_back_number;
					}
					
					if ($point_back == "yes" && ($user_author > 0 || $anonymously_user > 0)) {
						$author_points = ($anonymously_user > 0?$anonymously_user:$user_author);
						$what_point = (int)($what_point > 0?$what_point:wpqa_options("question_points"));
						wpqa_add_points($author_points,$what_point,"+","point_back",$post_id,$comment_id);
						if ($user_author > 0 || $anonymously_user > 0) {
							wpqa_notifications_activities(($user_author > 0?$user_author:$anonymously_user),"","",$post_id,$comment_id,"point_back","notifications");
						}
					}
				}
				
				$anonymously_user = get_comment_meta($comment_id,"anonymously_user",true);
				if (($user_id > 0 && $get_current_user_id > 0 && $user_id != $get_current_user_id) || ($anonymously_user > 0 && $get_current_user_id > 0 && $anonymously_user != $get_current_user_id)) {
					wpqa_notifications_activities(($user_id > 0?$user_id:$anonymously_user),$get_current_user_id,"",$post_id,$comment_id,"select_best_answer","notifications","","answer");
				}
				if ($get_current_user_id > 0) {
					wpqa_notifications_activities($get_current_user_id,"","",$post_id,$comment_id,"select_best_answer","activities","","answer");
				}
				do_action("wpqa_after_choose_best_answer",$post_id);
			}
			if (!isset($_POST["mobile"])) {
				echo "best";
			}
		}
		if (!isset($_POST["mobile"])) {
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_best_answer_a','wpqa_best_answer_a');
add_action('wp_ajax_nopriv_wpqa_best_answer_a','wpqa_best_answer_a');
/* Remove best answer */
if (!function_exists('wpqa_best_answer_re')) :
	function wpqa_best_answer_re() {
		if (!isset($_POST["mobile"])) {
			check_ajax_referer('wpqa_best_answer_nonce','wpqa_best_answer_nonce');
		}
		$comment_id = (int)$_POST['comment_id'];
		$get_comment = get_comment($comment_id);
		$user_id = $get_comment->user_id;
		$post_id = $get_comment->comment_post_ID;
		$the_best_answer = get_post_meta($post_id,"the_best_answer",true);
		if (isset($the_best_answer) && $the_best_answer != "" && $the_best_answer > 0 && $the_best_answer == $comment_id) {
			$get_current_user_id = get_current_user_id();
			$is_super_admin = is_super_admin($get_current_user_id);
			$get_post = get_post($post_id);
			$user_author = $get_post->post_author;
			$user_best_answer_filter = apply_filters("wpqa_user_best_answer_filter",true);
			$active_best_answer = wpqa_options("active_best_answer");
			$best_answer_userself = wpqa_options("best_answer_userself");
			$user_best_answer = esc_html(get_the_author_meta('user_best_answer',$get_current_user_id));
			$moderators_permissions = wpqa_user_moderator($get_current_user_id);
			$moderator_categories = wpqa_user_moderator_categories($get_current_user_id,$post_id);
			if ($user_best_answer_filter == true && ((is_user_logged_in() && $active_best_answer == "on" && $get_current_user_id > 0 && (($user_id != $get_current_user_id && $get_current_user_id == $user_author) || ($best_answer_userself == "on" && $user_id == $get_current_user_id && $get_current_user_id == $user_author))) || $user_best_answer == "on" || ($moderator_categories == true && isset($moderators_permissions['best']) && $moderators_permissions['best'] == "best") || $is_super_admin)) {
				delete_post_meta($post_id,"the_best_answer");
				$active_points = wpqa_options("active_points");
				if ($user_id > 0) {
					if ($user_id != $user_author && $active_points == "on") {
						$point_best_answer = (int)wpqa_options("point_best_answer");
						$point_best_answer = apply_filters("wpqa_point_best_answer",$point_best_answer,$post_id);
						$bump_best_answer = wpqa_options("bump_best_answer");
						if ($bump_best_answer == "on") {
							$question_points = (int)get_post_meta($post_id,"question_points",true);
							$point_best_answer = ($question_points > 0?$question_points:$point_best_answer);
						}
						if ($point_best_answer > 0) {
							wpqa_add_points($user_id,$point_best_answer,"-","cancel_best_answer",$post_id,$comment_id);
						}
					}
					$best_answer_user = get_user_meta($user_id,"best_answer_user",true);
					if (isset($best_answer_user)) {
						$remove_best_answer_user = wpqa_remove_item_by_value($best_answer_user,$comment_id);
						update_user_meta($user_id,"best_answer_user",$remove_best_answer_user);
					}
					$wpqa_count_best_answers = (int)get_user_meta($user_id,"wpqa_count_best_answers",true);
					$wpqa_count_best_answers--;
					update_user_meta($user_id,"wpqa_count_best_answers",($wpqa_count_best_answers < 0?0:$wpqa_count_best_answers));
				}
				$wpqa_best_answer = (int)get_option("wpqa_best_answer");
				$wpqa_best_answer--;
				update_option("wpqa_best_answer",($wpqa_best_answer < 0?0:$wpqa_best_answer));
				delete_comment_meta($comment_id,"best_answer_comment");
				
				$point_back_option = wpqa_options("point_back");
				$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
				if ($point_back_option == "on" && $active_points == "on" && ($is_super_admin || ($user_id != $user_author && $user_author > 0) || ($user_id != $anonymously_user && $anonymously_user > 0))) {
					$point_back_number = (int)wpqa_options("point_back_number");
					$point_back = get_post_meta($post_id,"point_back",true);
					$what_point = (int)get_post_meta($post_id,"what_point",true);
					
					if ($point_back_number > 0) {
						$what_point = $point_back_number;
					}
					
					if ($point_back == "yes" && ($user_author > 0 || $anonymously_user > 0)) {
						$author_points = ($anonymously_user > 0?$anonymously_user:$user_author);
						$what_point = (int)($what_point > 0?$what_point:wpqa_options("question_points"));
						wpqa_add_points($author_points,$what_point,"-","point_removed",$post_id,$comment_id);
					}
					
					if ($user_author > 0 || $anonymously_user > 0) {
						wpqa_notifications_activities(($user_author > 0?$user_author:$anonymously_user),"","",$post_id,$comment_id,"point_removed","notifications");
					}
				}
				
				$anonymously_user = get_comment_meta($comment_id,"anonymously_user",true);
				if (($user_id > 0 && $get_current_user_id > 0 && $user_id != $get_current_user_id) || ($anonymously_user > 0 && $get_current_user_id > 0 && $anonymously_user != $get_current_user_id)) {
					wpqa_notifications_activities(($user_id > 0?$user_id:$anonymously_user),$get_current_user_id,"",$post_id,$comment_id,"cancel_best_answer","notifications","","answer");
				}
				if ($get_current_user_id > 0) {
					wpqa_notifications_activities($get_current_user_id,"","",$post_id,$comment_id,"cancel_best_answer","activities","","answer");
				}
				do_action("wpqa_after_remove_best_answer",$post_id);
			}
			if (!isset($_POST["mobile"])) {
				echo "best";
			}
		}else {
			if (!isset($_POST["mobile"])) {
				echo "remove_best";
			}
		}
		if (!isset($_POST["mobile"])) {
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_best_answer_re','wpqa_best_answer_re');
add_action('wp_ajax_nopriv_wpqa_best_answer_re','wpqa_best_answer_re');
/* Question close */
if (!function_exists('wpqa_question_close')) :
	function wpqa_question_close($post_id = 0) {
		if (!isset($_POST["mobile"])) {
			check_ajax_referer('wpqa_open_close_nonce','wpqa_open_close_nonce');
		}
		$post_id = (int)($post_id > 0?$post_id:$_POST['post_id']);
		$get_post = get_post($post_id);
		$user_author = $get_post->post_author;
		$user_id = get_current_user_id();
		$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
		$moderators_permissions = wpqa_user_moderator($user_id);
		$moderator_categories = wpqa_user_moderator_categories($user_id,$post_id);
		if (($user_author > 0 && $user_author == $user_id) || ($anonymously_user == $user_id && $user_id > 0) || is_super_admin($user_id) || ($moderator_categories == true && isset($moderators_permissions['close']) && $moderators_permissions['close'] == "close")) {
			update_post_meta($post_id,'closed_question',1);
			if ($user_id > 0) {
				wpqa_notifications_activities($user_id,"","",$post_id,"","closed_question","activities","",wpqa_questions_type);
			}
		}
		if (!isset($_POST["mobile"])) {
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_question_close','wpqa_question_close');
add_action('wp_ajax_nopriv_wpqa_question_close','wpqa_question_close');
/* Question open */
if (!function_exists('wpqa_question_open')) :
	function wpqa_question_open($post_id = 0) {
		if (!isset($_POST["mobile"])) {
			check_ajax_referer('wpqa_open_close_nonce','wpqa_open_close_nonce');
		}
		$post_id = (int)($post_id > 0?$post_id:$_POST['post_id']);
		$get_post = get_post($post_id);
		$user_author = $get_post->post_author;
		$user_id = get_current_user_id();
		$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
		$moderators_permissions = wpqa_user_moderator($user_id);
		$moderator_categories = wpqa_user_moderator_categories($user_id,$post_id);
		if (($user_author > 0 && $user_author == $user_id) || ($anonymously_user == $user_id && $user_id > 0) || is_super_admin($user_id) || ($moderator_categories == true && isset($moderators_permissions['close']) && $moderators_permissions['close'] == "close")) {
			delete_post_meta($post_id,'closed_question');
			if ($user_id > 0) {
				wpqa_notifications_activities($user_id,"","",$post_id,"","opend_question","activities","",wpqa_questions_type);
			}
		}
		if (!isset($_POST["mobile"])) {
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_question_open','wpqa_question_open');
add_action('wp_ajax_nopriv_wpqa_question_open','wpqa_question_open');
/* Question follow */
if (!function_exists('wpqa_question_follow')) :
	function wpqa_question_follow() {
		$post_id = (int)$_POST['post_id'];
		$user_id = get_current_user_id();
		
		$following_questions_user = get_user_meta($user_id,"following_questions",true);
		if (empty($following_questions_user)) {
			$update = update_user_meta($user_id,"following_questions",array($post_id));
		}else if (is_array($following_questions_user) && !in_array($post_id,$following_questions_user)) {
			$update = update_user_meta($user_id,"following_questions",array_merge($following_questions_user,array($post_id)));
		}
		
		$following_questions = get_post_meta($post_id,"following_questions",true);
		if (empty($following_questions)) {
			$update = update_post_meta($post_id,"following_questions",array($user_id));
		}else if (is_array($following_questions) && !in_array($user_id,$following_questions)) {
			$update = update_post_meta($post_id,"following_questions",array_merge($following_questions,array($user_id)));
		}
		
		$get_post = get_post($post_id);
		$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
		if (($user_id > 0 && $get_post->post_author > 0) || ($user_id > 0 && $anonymously_user > 0)) {
			wpqa_notifications_activities(($get_post->post_author > 0?$get_post->post_author:$anonymously_user),$user_id,"",$post_id,"","follow_question","notifications","",wpqa_questions_type);
		}
		if ($user_id > 0) {
			wpqa_notifications_activities($user_id,"","",$post_id,"","follow_question","activities","",wpqa_questions_type);
		}
		$get_question_followers = get_post_meta($post_id,"following_questions",true);
		$get_question_followers = (is_array($get_question_followers) && !empty($get_question_followers)?get_users(array('fields' => 'ID','include' => $get_question_followers,'orderby' => 'registered')):array());
		if (!isset($_POST["mobile"])) {
			echo (is_array($get_question_followers) && is_array($get_question_followers) && isset($get_question_followers)?wpqa_count_number(count($get_question_followers)):0);
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_question_follow','wpqa_question_follow');
add_action('wp_ajax_nopriv_wpqa_question_follow','wpqa_question_follow');
/* Question unfollow */
if (!function_exists('wpqa_question_unfollow')) :
	function wpqa_question_unfollow() {
		$post_id = (int)$_POST['post_id'];
		$user_id = get_current_user_id();
		
		$following_questions_user = get_user_meta($user_id,"following_questions",true);
		if (isset($following_questions_user) && !empty($following_questions_user)) {
			$remove_following_questions_user = wpqa_remove_item_by_value($following_questions_user,$post_id);
			update_user_meta($user_id,"following_questions",$remove_following_questions_user);
		}
		
		$following_questions = get_post_meta($post_id,"following_questions",true);
		if (isset($following_questions) && !empty($following_questions)) {
			$remove_following_questions = wpqa_remove_item_by_value($following_questions,$user_id);
			update_post_meta($post_id,"following_questions",$remove_following_questions);
		}
		
		$get_post = get_post($post_id);
		$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
		if (($user_id > 0 && $get_post->post_author > 0) || ($user_id > 0 && $anonymously_user > 0)) {
			wpqa_notifications_activities(($get_post->post_author > 0?$get_post->post_author:$anonymously_user),$user_id,"",$post_id,"","unfollow_question","notifications","",wpqa_questions_type);
		}
		if ($user_id > 0) {
			wpqa_notifications_activities($user_id,"","",$post_id,"","unfollow_question","activities","",wpqa_questions_type);
		}
		$get_question_followers = get_post_meta($post_id,"following_questions",true);
		$get_question_followers = (is_array($get_question_followers) && !empty($get_question_followers)?get_users(array('fields' => 'ID','include' => $get_question_followers,'orderby' => 'registered')):array());
		if (!isset($_POST["mobile"])) {
			echo (is_array($get_question_followers) && is_array($get_question_followers) && isset($get_question_followers)?wpqa_count_number(count($get_question_followers)):0);
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_question_unfollow','wpqa_question_unfollow');
add_action('wp_ajax_nopriv_wpqa_question_unfollow','wpqa_question_unfollow');
/* Add question to favorite */
if (!function_exists('wpqa_add_favorite')) :
	function wpqa_add_favorite() {
		$post_id = (int)$_POST['post_id'];
		$user_id = get_current_user_id();
		
		$favorites_questions = get_post_meta($post_id,"favorites_questions",true);
		if (empty($favorites_questions)) {
			$update = update_post_meta($post_id,"favorites_questions",array($user_id));
		}else if (is_array($favorites_questions) && !in_array($user_id,$favorites_questions)) {
			$update = update_post_meta($post_id,"favorites_questions",array_merge($favorites_questions,array($user_id)));
		}
		
		$_favorites = get_user_meta($user_id,$user_id."_favorites",true);
		if (empty($_favorites)) {
			$update = update_user_meta($user_id,$user_id."_favorites",array($post_id));
		}else if (is_array($_favorites) && !in_array($post_id,$_favorites)) {
			$update = update_user_meta($user_id,$user_id."_favorites",array_merge($_favorites,array($post_id)));
		}
		
		$count = get_post_meta($post_id,'question_favorites',true);
		if (isset($update)) {
			if ($count == "") {
				$count = 0;
			}
			$count++;
			$update = update_post_meta($post_id,'question_favorites',$count);
			
			$get_post = get_post($post_id);
			$post_author = $get_post->post_author;
			$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
			if (($user_id > 0 && $post_author > 0) || ($user_id > 0 && $anonymously_user > 0)) {
				wpqa_notifications_activities(($post_author > 0?$post_author:$anonymously_user),$user_id,"",$post_id,"","question_favorites","notifications","",wpqa_questions_type);
			}
			if ($user_id > 0) {
				wpqa_notifications_activities($user_id,"","",$post_id,"","question_favorites","activities","",wpqa_questions_type);
			}

			wpqa_update_for_you($user_id,$post_id);
		}
		if (!isset($_POST["mobile"])) {
			echo wpqa_count_number($count);
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_add_favorite','wpqa_add_favorite');
add_action('wp_ajax_nopriv_wpqa_add_favorite','wpqa_add_favorite');
/* Remove question from the favorite */
if (!function_exists('wpqa_remove_favorite')) :
	function wpqa_remove_favorite() {
		$post_id = (int)$_POST['post_id'];
		$user_id = get_current_user_id();
		
		$favorites_questions = get_post_meta($post_id,"favorites_questions",true);
		if (isset($favorites_questions) && !empty($favorites_questions)) {
			$remove_favorites_questions = wpqa_remove_item_by_value($favorites_questions,$user_id);
			update_post_meta($post_id,"favorites_questions",$remove_favorites_questions);
		}
		
		$_favorites = get_user_meta($user_id,$user_id."_favorites",true);
		if (is_array($_favorites) && in_array($post_id,$_favorites)) {
			$remove_item = wpqa_remove_item_by_value($_favorites,$post_id);
			$update = update_user_meta($user_id,$user_id."_favorites",$remove_item);
		}
		
		$count = get_post_meta($post_id,'question_favorites',true);
		if (isset($update)) {
			if ($count == "") {
				$count = 0;
			}
			$count--;
			if ($count < 0) {
				$count = 0;
			}
			$update = update_post_meta($post_id,'question_favorites',$count);
			
			$get_post = get_post($post_id);
			$post_author = $get_post->post_author;
			$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
			if (($user_id > 0 && $post_author > 0) || ($user_id > 0 && $anonymously_user > 0)) {
				wpqa_notifications_activities(($post_author > 0?$post_author:$anonymously_user),$user_id,"",$post_id,"","question_remove_favorites","notifications","",wpqa_questions_type);
			}
			if ($user_id > 0) {
				wpqa_notifications_activities($user_id,"","",$post_id,"","question_remove_favorites","activities","",wpqa_questions_type);
			}

			wpqa_remove_for_you($user_id,$post_id);
		}
		if (!isset($_POST["mobile"])) {
			echo wpqa_count_number($count);
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_remove_favorite','wpqa_remove_favorite');
add_action('wp_ajax_nopriv_wpqa_remove_favorite','wpqa_remove_favorite');
/* Update notifications */
if (!function_exists('wpqa_update_notifications')) :
	function wpqa_update_notifications() {
		$user_id = get_current_user_id();
		delete_user_meta($user_id,$user_id.'_new_notification');
		if (!isset($_POST["mobile"])) {
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_update_notifications','wpqa_update_notifications');
add_action('wp_ajax_nopriv_wpqa_update_notifications','wpqa_update_notifications');
/* Login Ajax process */
if (!function_exists('wpqa_ajax_login_process')) :
	function wpqa_ajax_login_process() {
		wpqa_login_jquery();
		die();
	}
endif;
add_action('wp_ajax_wpqa_ajax_login_process','wpqa_ajax_login_process');
add_action('wp_ajax_nopriv_wpqa_ajax_login_process','wpqa_ajax_login_process');
/* Ajax signup process */
if (!function_exists('wpqa_ajax_signup_process')) :
	function wpqa_ajax_signup_process() {
		wpqa_signup_jquery();
		die();
	}
endif;
add_action('wp_ajax_wpqa_ajax_signup_process','wpqa_ajax_signup_process');
add_action('wp_ajax_nopriv_wpqa_ajax_signup_process','wpqa_ajax_signup_process');
/* Ajax password process */
if (!function_exists('wpqa_ajax_password_process')) :
	function wpqa_ajax_password_process() {
		wpqa_pass_jquery($_POST);
		die();
	}
endif;
add_action('wp_ajax_wpqa_ajax_password_process','wpqa_ajax_password_process');
add_action('wp_ajax_nopriv_wpqa_ajax_password_process','wpqa_ajax_password_process');
/* Delete image */
if (!function_exists('wpqa_remove_image')) :
	function wpqa_remove_image() {
		check_ajax_referer('wpqa_remove_image','wpqa_remove_image');
		$image_name = esc_html($_POST["image_name"]);
		$image_type = esc_html($_POST["image_type"]);
		$meta_id    = (int)esc_html($_POST["meta_id"]);
		$image_id   = (isset($_POST["image_id"])?(int)esc_html($_POST["image_id"]):"");
		$user_id    = get_current_user_id();
		if ($image_id > 0 || ($image_id == "" && $image_name == "added_file")) {
			if ($image_type == "post_meta" && $meta_id > 0 && $image_name != "") {
				$get_post_meta = get_post_meta($meta_id,$image_name,true);
				if ($get_post_meta == $image_id) {
					$delete_post_meta = true;
				}
			}else if ($image_type == "comment_meta" && $meta_id > 0 && $image_name != "") {
				$get_comment_meta = get_comment_meta($meta_id,$image_name,true);
				if ($get_comment_meta == $image_id || ($image_id == "" && $image_name == "added_file")) {
					$delete_comment_meta = true;
				}
			}else if ($image_type == "user_meta" && $user_id > 0 && $image_name != "") {
				$user_meta_avatar = wpqa_avatar_name();
				$your_avatar = get_the_author_meta($user_meta_avatar,$user_id);
				if ((is_array($your_avatar) && isset($your_avatar["id"]) && $your_avatar["id"] == $image_id) || (!is_array($your_avatar) && is_numeric($your_avatar) && $your_avatar == $image_id)) {
					$delete_avatar = true;
				}

				$user_meta_cover = wpqa_cover_name();
				$your_cover = get_the_author_meta($user_meta_cover,$user_id);
				if ((is_array($your_cover) && isset($your_cover["id"]) && $your_cover["id"] == $image_id) || (!is_array($your_cover) && is_numeric($your_cover) && $your_cover == $image_id)) {
					$delete_cover = true;
				}
			}
			if (($image_type == "post_meta" && isset($delete_post_meta)) || ($image_type == "comment_meta" && isset($delete_comment_meta)) || ($image_type == "user_meta" && (isset($delete_avatar) || isset($delete_cover)))) {
				$get_post_meta = get_post_meta($image_id,"wpqa_image_names",true);
				if (is_array($get_post_meta) && !empty($get_post_meta)) {
					foreach ($get_post_meta as $value) {
						if (!unlink($value)) {
							error_log("Cannot be deleted due to an error");
						}
					}
				}
				delete_post_meta($image_id,"wpqa_image_names");
				wp_delete_attachment($image_id,true);
			}
		}
		if ($image_type == "post_meta" && $meta_id > 0 && $image_name != "" && isset($delete_post_meta)) {
			delete_post_meta($meta_id,$image_name);
		}else if ($image_type == "comment_meta" && $meta_id > 0 && $image_name != "" && isset($delete_comment_meta)) {
			delete_comment_meta($meta_id,$image_name);
		}else if ($image_type == "user_meta" && $user_id > 0 && $image_name != "" && (isset($delete_avatar) || isset($delete_cover))) {
			delete_user_meta($user_id,$image_name);
			if ($image_name == $user_meta_cover) {
				echo wpqa_get_user_cover(array("user_id" => $user_id,"size" => 100,"user_name" => get_the_author_meta('display_name',$user_id)));
			}else {
				echo wpqa_get_user_avatar(array("user_id" => $user_id,"size" => 100,"user_name" => get_the_author_meta('display_name',$user_id)));
			}
		}
		die();
	}
endif;
add_action('wp_ajax_wpqa_remove_image','wpqa_remove_image');
add_action('wp_ajax_nopriv_wpqa_remove_image','wpqa_remove_image');
/* Custom search for users */
if (!function_exists('wpqa_custom_search_users_live')) :
	function wpqa_custom_search_users_live($user_query) {
		global $wpdb;
		$block_users = wpqa_options("block_users");
		if ($block_users == "on") {
			$user_id = get_current_user_id();
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
			}
		}
		$block_users_sql = (isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)?" AND $wpdb->users.ID NOT IN (".implode(",",$get_block_users).") ":"");
		$search_value = $user_query->query_vars;
		if (is_array($search_value) && isset($search_value['search'])) {
			$search_value = str_replace("*","",$search_value['search']);
		}
		$search_value = apply_filters("wpqa_search_value_filter",$search_value);
		$user_query->query_where .= " 
		OR (ID IN (SELECT user_id FROM $wpdb->usermeta
		WHERE (($wpdb->usermeta.meta_key='nickname' OR $wpdb->usermeta.meta_key='first_name' OR $wpdb->usermeta.meta_key='last_name')
			AND ($wpdb->usermeta.meta_value LIKE '".$search_value."' OR $wpdb->usermeta.meta_value RLIKE '".$search_value."'))
		)
		OR ($wpdb->users.ID LIKE '".$search_value."' OR $wpdb->users.ID RLIKE '".$search_value."') 
		OR ($wpdb->users.user_email LIKE '".$search_value."' OR $wpdb->users.user_email RLIKE '".$search_value."') 
		OR ($wpdb->users.user_url LIKE '".$search_value."' OR $wpdb->users.user_url RLIKE '".$search_value."') 
		OR ($wpdb->users.display_name LIKE '".$search_value."' OR $wpdb->users.display_name RLIKE '".$search_value."') 
		OR ($wpdb->users.user_login LIKE '".$search_value."' OR $wpdb->users.user_login RLIKE '".$search_value."') 
		OR ($wpdb->users.user_nicename LIKE '".$search_value."' OR $wpdb->users.user_nicename RLIKE '".$search_value."'))".$block_users_sql;
	}
endif;
if (!function_exists('wpqa_custom_search_users')) :
	function wpqa_custom_search_users($user_query) {
		if (is_search() || wpqa_is_search()) {
			global $wpdb;
			$block_users = wpqa_options("block_users");
			if ($block_users == "on") {
				$user_id = get_current_user_id();
				if ($user_id > 0) {
					$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
				}
			}
			$block_users_sql = (isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)?" AND $wpdb->users.ID NOT IN (".implode(",",$get_block_users).") ":"");
			$search_value = wpqa_search();
			$search_value = apply_filters("wpqa_search_value_filter",$search_value);
			$user_query->query_where .= " 
			OR (ID IN (SELECT user_id FROM $wpdb->usermeta
			WHERE (($wpdb->usermeta.meta_key='nickname' OR $wpdb->usermeta.meta_key='first_name' OR $wpdb->usermeta.meta_key='last_name')
				AND ($wpdb->usermeta.meta_value LIKE '".$search_value."' OR $wpdb->usermeta.meta_value RLIKE '".$search_value."'))
			)
			OR ($wpdb->users.ID LIKE '".$search_value."' OR $wpdb->users.ID RLIKE '".$search_value."') 
			OR ($wpdb->users.user_email LIKE '".$search_value."' OR $wpdb->users.user_email RLIKE '".$search_value."') 
			OR ($wpdb->users.user_url LIKE '".$search_value."' OR $wpdb->users.user_url RLIKE '".$search_value."') 
			OR ($wpdb->users.display_name LIKE '".$search_value."' OR $wpdb->users.display_name RLIKE '".$search_value."') 
			OR ($wpdb->users.user_login LIKE '".$search_value."' OR $wpdb->users.user_login RLIKE '".$search_value."') 
			OR ($wpdb->users.user_nicename LIKE '".$search_value."' OR $wpdb->users.user_nicename RLIKE '".$search_value."'))".$block_users_sql;
		}
	}
endif;
/* Live search */
if (!function_exists('wpqa_live_search')) :
	function wpqa_live_search() {
		global $post;
		$search_type          = (isset($_POST["search_type"])?esc_html($_POST["search_type"]):"");
		$search_type          = (isset($search_type) && $search_type != ""?$search_type:wpqa_options("default_search"));
		$search_value         = wp_unslash(sanitize_text_field($_POST["search_value"]));
		$suggest_questions    = (isset($_POST["suggest_questions"])?esc_html($_POST["suggest_questions"]):"");
		$search_result_number = wpqa_options("search_result_number");
		$number               = (isset($search_result_number) && $search_result_number > 0?$search_result_number*2:apply_filters('users_per_page',get_option('posts_per_page')));
		$k_search             = 0;
		$meta_query           = array();
		$cat_type = ($search_type == 'post' || $search_type == 'category'?'category':($search_type == wpqa_questions_type || $search_type == wpqa_question_categories?wpqa_question_categories:wpqa_knowledgebase_categories));
		$tag_type = ($search_type == 'post' || $search_type == 'post_tag'?'post_tag':($search_type == wpqa_questions_type || $search_type == wpqa_question_tags?wpqa_question_tags:wpqa_knowledgebase_tags));
		if ($search_value != "") {
			echo "<div class='result-div'>
				<ul class='list-unstyled mb-0 results-list'>";
					if ($search_type == "answers" || $search_type == "comments") {
						$user_id = get_current_user_id();
						$comments_all = get_comments(array("status" => "approve",'search' => $search_value,'number' => $number,"meta_query" => array('relation' => 'AND',array("key" => "answer_question_private","compare" => "NOT EXISTS")),'post_type' => ($search_type == "answers"?wpqa_questions_type:"post")));
						if (!empty($comments_all)) {
							foreach ($comments_all as $comment) {
								$k_search++;
								if ($search_result_number >= $k_search) {
									$get_post = get_post($comment->comment_post_ID);
									$post_author = $get_post->post_author;
									$yes_private = wpqa_private($comment->comment_post_ID,$post_author,$user_id);
									$yes_private_answer = wpqa_private_answer($comment->comment_ID,$comment->user_id,$user_id,$post_author);
									if ($yes_private == 1 && $yes_private_answer == 1) {
										echo '<li><a href="'.get_permalink($comment->comment_post_ID).'#comment-'.$comment->comment_ID.'">'.str_ireplace($search_value,"<strong>".$search_value."</strong>",wp_html_excerpt($comment->comment_content,60)).'</a></li>';
									}
								}else {
									echo "<li><a href='".esc_url(wpqa_search_link(urlencode(urldecode(trim($search_value))),$search_type))."'>".esc_html__("View all results.","wpqa")."</a></li>";
									exit;
								}
							}
						}else {
							$show_no_found = true;
						}
					}else if ($search_type == "users") {
						add_action('pre_user_query','wpqa_custom_search_users_live');
						$author__not_in = array();
						$block_users = wpqa_options("block_users");
						if ($block_users == "on") {
							$user_id = get_current_user_id();
							if ($user_id > 0) {
								$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
								if (is_array($get_block_users) && !empty($get_block_users)) {
									$author__not_in = $get_block_users;
								}
							}
						}
						$args = array(
							'meta_query' => array('relation' => 'OR',array("key" => "first_name","value" => $search_value,"compare" => "RLIKE")),
							'orderby'    => "user_registered",
							'order'      => "DESC",
							'search'     => '*'.$search_value.'*',
							'number'     => $number,
							'fields'     => 'ID',
							'exclude'    => $author__not_in
						);

						$query = new WP_User_Query($args);
						$query = $query->get_results();
						if (isset($query) && !empty($query)) {
							foreach ($query as $user) {
								$k_search++;
								if ($search_result_number >= $k_search) {
									$display_name = get_the_author_meta('display_name',$user);
									echo '<li>
										<a class="get-results" href="'.wpqa_profile_url($user).'" title="'.$display_name.'">'.wpqa_get_user_avatar(array("user_id" => $user,"size" => 29,"user_name" => $display_name)).'</a>
										<a href="'.wpqa_profile_url($user).'" title="'.$display_name.'">'.str_ireplace($search_value,"<strong>".$search_value."</strong>",$display_name).'</a>
									</li>';
								}else {
									echo "<li><a href='".esc_url(wpqa_search_link(urlencode(urldecode(trim($search_value))),$search_type))."'>".esc_html__("View all results.","wpqa")."</a></li>";
									exit;
								}
							}
						}else {
							$show_no_found = true;
						}
						remove_action('pre_user_query','wpqa_custom_search_users_live');
					}else if ($search_type == wpqa_question_categories || $search_type == wpqa_knowledgebase_categories || $search_type == "category" || $search_type == wpqa_question_tags || $search_type == wpqa_knowledgebase_tags || $search_type == "post_tag") {
						$exclude = apply_filters('wpqa_exclude_question_category',array());
						$terms = get_terms(($search_type == wpqa_question_categories || $search_type == wpqa_knowledgebase_categories || $search_type == "category"?$cat_type:$tag_type),array_merge($exclude,array(
							'orderby'    => "count",
							'order'      => "DESC",
							'number'     => apply_filters(($search_type == wpqa_question_categories || $search_type == wpqa_knowledgebase_categories || $search_type == "category"?"wpqa_cats_per_page":"wpqa_tags_per_page"),2*$number),
							'hide_empty' => 0,
							'search'     => $search_value
						)));

						if (!empty($terms) && !is_wp_error($terms)) {
							foreach ($terms as $term) {
								$k_search++;
								if ($search_result_number >= $k_search) {
									echo '<li><a href="'.get_term_link($term).'">'.str_ireplace($search_value,"<strong>".$search_value."</strong>",$term->name).'</a></li>';
								}else {
									echo "<li><a href='".esc_url(wpqa_search_link(urlencode(urldecode(trim($search_value))),$search_type))."'>".esc_html__("View all results.","wpqa")."</a></li>";
									exit;
								}
							}
						}else {
							$show_no_found = true;
						}
					}else if ($search_type == "groups") {
						$post_type_array = array('group');
						$search_query = new wp_query(array('s' => $search_value,'post_type' => $post_type_array,'post_status' => 'publish','posts_per_page' => $number));
						if ($search_query->have_posts()) :
							while ( $search_query->have_posts() ) : $search_query->the_post();
								$k_search++;
								if ($search_result_number >= $k_search) {
									$the_title = get_the_title($post->ID);
									$group_image = get_post_meta($post->ID,"group_image",true);
									echo "<li>";
										if ((is_array($group_image) && isset($group_image["id"])) || (!is_array($group_image) && $group_image != "")) {
											echo "<a class='get-results' href='".get_permalink($post->ID)."'>".wpqa_get_aq_resize_img(29,29,"",(is_array($group_image) && isset($group_image["id"])?$group_image["id"]:$group_image),"no",$the_title)."</a>";
										}
										echo "<a href='".get_permalink($post->ID)."'>".str_ireplace($search_value,"<strong>".$search_value."</strong>",$the_title)."</a>
									</li>";
								}else {
									echo "<li><a href='".esc_url(wpqa_search_link(urlencode(urldecode(trim($search_value))),$search_type))."'>".esc_html__("View all results.","wpqa")."</a></li>";
									exit;
								}
							endwhile;
						else :
							$show_no_found = true;
						endif;
						wp_reset_postdata();
					}else {
						$filter_search_type = apply_filters("wpqa_search_type_filter",false,$search_type);
						if ($filter_search_type == true) {
							$post_type_array = apply_filters("wpqa_search_post_type_array","",$search_type);
						}else if ($search_type == "posts") {
							$post_type_array = array('post');
						}else if ($search_type == "knowledgebases") {
							$post_type_array = array(wpqa_knowledgebase_type);
						}else {
							$search_type = "questions";
							$post_type_array = array(wpqa_questions_type);
						}
						if ($search_type == "questions") {
							$asked_questions_search = wpqa_options("asked_questions_search");
							$meta_query = ($asked_questions_search == "on"?array("meta_query" => array("relation" => "or",array("key" => "user_id","compare" => "EXISTS"),array("key" => "question_private","compare" => "NOT EXISTS"),array("key" => "user_id","compare" => "NOT EXISTS"))):array("meta_query" => array(array("key" => "question_private","compare" => "NOT EXISTS"))));
							$post_type_array = ($asked_questions_search == "on"?array(wpqa_questions_type,wpqa_asked_questions_type):$post_type_array);
						}
						$block_users = wpqa_options("block_users");
						$author__not_in = array();
						if ($block_users == "on") {
							$user_id = get_current_user_id();
							if ($user_id > 0) {
								$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
								if (is_array($get_block_users) && !empty($get_block_users)) {
									$author__not_in = array("author__not_in" => $get_block_users);
								}
							}
						}
						$search_query = new wp_query(array_merge($meta_query,$author__not_in,array('s' => $search_value,'posts_per_page' => $number,'post_type' => $post_type_array,'post_status' => 'publish')));
						if ($search_query->have_posts()) :
							if ($suggest_questions == "suggest-questions") {
								echo "<li class='suggest-questions-li'>".esc_html__("Some suggested questions for you","wpqa")."</li>";
							}
							while ( $search_query->have_posts() ) : $search_query->the_post();
								$k_search++;
								if ($search_result_number >= $k_search) {
									echo "<li><a".($suggest_questions == "suggest-questions"?" target='_blank'":"")." href='".get_permalink($post->ID)."'>".str_ireplace($search_value,"<strong>".$search_value."</strong>",strip_tags(get_the_title($post->ID)))."</a>".apply_filters("wpqa_live_search_posts",false,$post->ID)."</li>";
								}else {
									echo "<li><a".($suggest_questions == "suggest-questions"?" target='_blank'":"")." href='".esc_url(wpqa_search_link(urlencode(urldecode(trim($search_value))),$search_type))."'>".esc_html__("View all results.","wpqa")."</a></li>";
									exit;
								}
							endwhile;
						else :
							$show_no_found = true;
							if ($suggest_questions == "suggest-questions") {
								echo "no_suggest_questions";
								$show_no_found = false;
							}
						endif;
						wp_reset_postdata();
					}
					if (isset($show_no_found) && $show_no_found == true) {
						echo "<li class='no-search-result'>".esc_html__("No results found.","wpqa")."</li>";
					}
				echo "</ul>
			</div>";
		}
		die();
	}
endif;
add_action('wp_ajax_wpqa_live_search','wpqa_live_search');
add_action('wp_ajax_nopriv_wpqa_live_search','wpqa_live_search');
/* Ban and unban users */
if (!function_exists('wpqa_ban_user')) :
	function wpqa_ban_user () {
		check_ajax_referer('ban_nonce','ban_nonce');
		$get_current_user_id = get_current_user_id();
		$is_super_admin = is_super_admin($get_current_user_id);
		$moderators_permissions = wpqa_user_moderator($get_current_user_id);
		if ($is_super_admin || (isset($moderators_permissions['ban']) && $moderators_permissions['ban'] == "ban")) {
			$user_id = (int)$_POST['user_id'];
			$ban_type = esc_html($_POST['ban_type']);
			wpqa_ban_unban_user($user_id,$ban_type,$get_current_user_id);
		}
		die();
	}
endif;
add_action('wp_ajax_wpqa_ban_user','wpqa_ban_user');
add_action('wp_ajax_nopriv_wpqa_ban_user','wpqa_ban_user');
/* Block and unblock users */
if (!function_exists('wpqa_block_user')) :
	function wpqa_block_user () {
		if (!isset($_POST["mobile"])) {
			check_ajax_referer('block_nonce','block_nonce');
		}
		$get_current_user_id = get_current_user_id();
		$user_id = (int)$_POST['user_id'];
		$block_type = esc_html($_POST['block_type']);
		if ($get_current_user_id > 0 && $user_id != $get_current_user_id) {
			$get_block_users = get_user_meta($get_current_user_id,"wpqa_block_users",true);
			if ($block_type == "block") {
				if (empty($get_block_users)) {
					update_user_meta($get_current_user_id,"wpqa_block_users",array($user_id));
					$add_notification = true;
				}else if (is_array($get_block_users) && !in_array($user_id,$get_block_users)) {
					update_user_meta($get_current_user_id,"wpqa_block_users",array_merge($get_block_users,array($user_id)));
					$add_notification = true;
				}
				if (isset($add_notification)) {
					wpqa_notifications_activities($get_current_user_id,$user_id,"","","","block_user","activities");
				}
			}else {
				if (is_array($get_block_users) && in_array($user_id,$get_block_users)) {
					$get_block_users = wpqa_remove_item_by_value($get_block_users,$user_id);
					update_user_meta($get_current_user_id,"wpqa_block_users",$get_block_users);
					wpqa_notifications_activities($get_current_user_id,$user_id,"","","","unblock_user","activities");
				}
			}
		}
		if (!isset($_POST["mobile"])) {
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_block_user','wpqa_block_user');
add_action('wp_ajax_nopriv_wpqa_block_user','wpqa_block_user');
/* Finishe the follow */
if (!function_exists('wpqa_finish_follow')) :
	function wpqa_finish_follow () {
		$post_id = (int)$_POST['post_id'];
		$user_id = get_current_user_id();
		$wp_page_template = get_post_meta($post_id,"_wp_page_template",true);
		if ($wp_page_template == "template-home.php") {
			$home_feed = get_post_meta($post_id,prefix_meta."home_feed",true);
			$number_of_users = get_post_meta($post_id,prefix_meta."users_home_feed",true);
			$number_of_categories = get_post_meta($post_id,prefix_meta."categories_home_feed",true);
			$number_of_tags = get_post_meta($post_id,prefix_meta."tags_home_feed",true);
		}else {
			$home_feed = get_post_meta($post_id,prefix_meta."feed",true);
			$number_of_users = get_post_meta($post_id,prefix_meta."users_feed",true);
			$number_of_categories = get_post_meta($post_id,prefix_meta."categories_feed",true);
			$number_of_tags = get_post_meta($post_id,prefix_meta."tags_feed",true);
		}

		$following_me = get_user_meta($user_id,"following_me",true);
		$block_users = wpqa_options("block_users");
		$author__not_in = array();
		if ($block_users == "on") {
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
			}
		}
		if (is_array($following_me) && !empty($following_me) && isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)) {
			$following_me = array_diff($following_me,$get_block_users);
		}
		$user_cat_follow = get_user_meta($user_id,"user_cat_follow",true);
		$user_tag_follow = get_user_meta($user_id,"user_tag_follow",true);

		$user_following_if = ((isset($home_feed["users"]["value"]) && $home_feed["users"]["value"] === "0") || $number_of_users == 0 || ($number_of_users > 0 && is_array($following_me) && count($following_me) >= $number_of_users)?"yes":"no");
		$cat_following_if = ((isset($home_feed["cats"]["value"]) && $home_feed["cats"]["value"] === "0") || $number_of_categories == 0 || ($number_of_categories > 0 && is_array($user_cat_follow) && count($user_cat_follow) >= $number_of_categories)?"yes":"no");
		$tag_following_if = ((isset($home_feed["tags"]["value"]) && $home_feed["tags"]["value"] === "0") || $number_of_tags == 0 || ($number_of_tags > 0 && is_array($user_tag_follow) && count($user_tag_follow) >= $number_of_tags)?"yes":"no");

		$user_count_already = (is_array($following_me)?count($following_me):0);
		$cat_count_already = (is_array($user_cat_follow)?count($user_cat_follow):0);
		$tag_count_already = (is_array($user_tag_follow)?count($user_tag_follow):0);

		$user_following = (is_array($following_me) && !empty($following_me)?implode(",",$following_me):"");
		$user_cat_follow = apply_filters("wpqa_user_cat_follow",$user_cat_follow);
		$cat_following = (is_array($user_cat_follow) && !empty($user_cat_follow)?implode(",",$user_cat_follow):"");
		$tag_following = (is_array($user_tag_follow) && !empty($user_tag_follow)?implode(",",$user_tag_follow):"");
		$all_following = ($cat_following != ""?$cat_following:"").($cat_following != "" && $tag_following != ""?",":"").($tag_following != ""?$tag_following:"");

		echo ($user_following_if == "yes" && $cat_following_if == "yes" && $tag_following_if == "yes" && ($all_following != "" || $user_following != "")?1:0);
		die();
	}
endif;
add_action('wp_ajax_wpqa_finish_follow','wpqa_finish_follow');
add_action('wp_ajax_nopriv_wpqa_finish_follow','wpqa_finish_follow');
/* Request money */
if (!function_exists('wpqa_request_money')) :
	function wpqa_request_money () {
		$custom_points_value = (int)$_POST['custom_points_value'];
		$user_id = get_current_user_id();
		$pay_minimum_points = (int)wpqa_options("pay_minimum_points");
		$money_to_points = floatval(wpqa_options("money_to_points"));
		$money_to_points = ($money_to_points <> 0?$money_to_points:1);
		$pay_minimum_money = floatval(wpqa_options("pay_minimum_money"));
		$points_user = (int)get_user_meta($user_id,"points",true);
		$result = array();
		if ($custom_points_value > 0 && $custom_points_value >= $pay_minimum_points) {
			if ($points_user >= $custom_points_value) {
				$last_money = floatval($custom_points_value/($pay_minimum_points/$money_to_points));
				if ($last_money >= $pay_minimum_money) {
					$result["success"] = floatval($last_money);
				}else {
					$result["success"] = 0;
					$result["error"] = 'not_enough_money';
				}
			}else {
				$result["success"] = 0;
				$result["error"] = 'not_enough_points';
			}
		}else {
			$result["success"] = 0;
			$result["error"] = 'not_min_points';
		}
		echo json_encode($result);
		die();
	}
endif;
add_action('wp_ajax_wpqa_request_money','wpqa_request_money');
add_action('wp_ajax_nopriv_wpqa_request_money','wpqa_request_money');
/* Author image popup */
if (!function_exists('wpqa_get_author_image_pop')) :
	function wpqa_get_author_image_pop () {
		$user_id = (int)$_POST['user_id'];
		$owner = false;
		$get_current_user_id = get_current_user_id();
		if ($get_current_user_id == $user_id) {
			$owner = true;
		}
		echo wpqa_author($user_id,"columns_pop",$owner);
		die();
	}
endif;
add_action('wp_ajax_wpqa_get_author_image_pop','wpqa_get_author_image_pop');
add_action('wp_ajax_nopriv_wpqa_get_author_image_pop','wpqa_get_author_image_pop');
/* Author actions */
if (!function_exists('wpqa_user_actions')) :
	function wpqa_user_actions() {
		$user_id = (int)$_POST['user_id'];
		$actions = esc_html($_POST['actions']);
		$get_current_user_id = get_current_user_id();
		if (is_super_admin($get_current_user_id)) {
			$default_group = wpqa_options("default_group");
			$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
			$default_group = apply_filters("wpqa_default_group",$default_group,$user_id);
			if ($actions == "remove_subscription") {
				$trial_subscribe = get_user_meta($user_id,"trial_subscribe",true);
				$points_subscribe = get_user_meta($user_id,"points_subscribe",true);
				wpqa_stop_subscription($user_id,0,($trial_subscribe == "" && $points_subscribe == ""?"cancel":""));
			}else {
				$user_data = get_userdata($user_id);
				$activate_user_meta = ($actions == "activate_user"?"activate_user":"approve_user");
				$activate_user = get_user_meta($user_id,$activate_user_meta,true);
				if ($activate_user == "") {
					$send_text = wpqa_send_mail(
						array(
							'content' => wpqa_options("email_approve_user"),
							'user_id' => $user_id,
						)
					);
					$email_title = wpqa_options("title_approve_user");
					$email_title = ($email_title != ""?$email_title:esc_html__("Confirm account","wpqa"));
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
							'toEmail'     => esc_html($user_data->user_email),
							'toEmailName' => esc_html($user_data->display_name),
							'title'       => $email_title,
							'message'     => $send_text,
						)
					);
					update_user_meta($user_id,$activate_user_meta,"on");
					update_user_meta($user_id,"user_activated","activated");
					do_action("wpqa_user_activated",$user_id);
					if ($actions == "activate_user") {
						delete_user_meta($user_id,"activation");
					}
				}
				do_action("wpqa_user_register_after_saved",$user_id);
				$nicename_nickname = (isset($user_data->nickname) && $user_data->nickname != ""?sanitize_text_field($user_data->nickname):(isset($user_data->user_name) && $user_data->user_name != ""?sanitize_text_field($user_data->user_name):$user_data->user_login));
				wp_update_user(array('ID' => $user_id,'user_nicename' => $nicename_nickname,'nickname' => $nicename_nickname,'role' => $default_group));
			}
			delete_user_meta($user_id,"wpqa_default_group");
			do_action("wpqa_after_registration",$user_id);
		}
		die();
	}
endif;
add_action('wp_ajax_wpqa_user_actions','wpqa_user_actions');
add_action('wp_ajax_nopriv_wpqa_user_actions','wpqa_user_actions');
/* Author actions */
if (!function_exists('wpqa_ajax_important_notices')) :
	function wpqa_ajax_important_notices () {
		$widget_id = esc_html($_POST["widget_id"]);
		if (is_user_logged_in()) {
			update_user_meta(get_current_user_id(),"wpqa_important_notices_".$widget_id,"wpqa_yes_hidden");
		}else {
			if (isset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_important_notices_'.$widget_id]) && $_COOKIE[wpqa_options("uniqid_cookie").'wpqa_important_notices_'.$widget_id] == "wpqa_yes_hidden") {
				unset($_COOKIE[wpqa_options("uniqid_cookie").'wpqa_important_notices_'.$widget_id]);
				setcookie(wpqa_options("uniqid_cookie").'wpqa_important_notices_'.$widget_id,"",-1,COOKIEPATH,COOKIE_DOMAIN);
			}
			setcookie(wpqa_options("uniqid_cookie").'wpqa_important_notices_'.$widget_id,"wpqa_yes_hidden",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
		}
		die();
	}
endif;
add_action('wp_ajax_wpqa_ajax_important_notices','wpqa_ajax_important_notices');
add_action('wp_ajax_nopriv_wpqa_ajax_important_notices','wpqa_ajax_important_notices');
/* Cancel edit email */
if (!function_exists('wpqa_cancel_edit_email')) :
	function wpqa_cancel_edit_email () {
		$user_id = (int)$_POST["id"];
		$nonce = esc_html($_POST["nonce"]);
		if (wp_verify_nonce($nonce,'wpqa_cancel_edit_email')) {
			delete_user_meta($user_id,"activation");
			delete_user_meta($user_id,"wpqa_edit_email");
		}
		die();
	}
endif;
add_action('wp_ajax_wpqa_cancel_edit_email','wpqa_cancel_edit_email');
add_action('wp_ajax_nopriv_wpqa_cancel_edit_email','wpqa_cancel_edit_email');?>