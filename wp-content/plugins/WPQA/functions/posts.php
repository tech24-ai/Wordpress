<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Notifications add post */
if (!function_exists('wpqa_notifications_add_post')) :
	function wpqa_notifications_add_post($post_id,$post_username,$not_user,$get_current_user_id,$approved = false) {
		$way_sending_notifications = wpqa_options("way_sending_notifications_posts");
		if ($way_sending_notifications != "cronjob") {
			$send_email_and_notification_post = wpqa_options("send_email_and_notification_post");
			$send_email_new_post_value = "send_email_new_post";
			$send_email_post_groups_value = "send_email_post_groups";
			if ($send_email_and_notification_post == "both") {
				$send_email_new_post_value = "send_email_new_post_both";
				$send_email_post_groups_value = "send_email_post_groups_both";
			}
			$send_email_new_post = wpqa_options($send_email_new_post_value);
			$the_author = 0;
			if ($not_user == 0) {
				$the_author = $post_username;
			}
			$email_title = wpqa_options("title_new_posts");
			$email_title = ($email_title != ""?$email_title:esc_html__("New post","wpqa"));
			if ($send_email_new_post == "on") {
				$user_group = wpqa_options($send_email_post_groups_value);
				$users = get_users(array("meta_query" => array('relation' => 'AND',array("key" => "received_email_post","compare" => "=","value" => "on"),array('relation' => 'OR',array("key" => "unsubscribe_mails","compare" => "NOT EXISTS"),array("key" => "unsubscribe_mails","compare" => "!=","value" => "on"))),"role__in" => (isset($user_group) && is_array($user_group)?$user_group:array()),"fields" => array("ID","user_email","display_name")));
				if (isset($users) && is_array($users) && !empty($users)) {
					foreach ($users as $key => $value) {
						$another_user_id = $value->ID;
						if ($get_current_user_id != $another_user_id && $another_user_id > 0 && $not_user != $another_user_id) {
							$send_text = wpqa_send_mail(
								array(
									'content'          => wpqa_options("email_new_posts"),
									'post_id'          => $post_id,
									'sender_user_id'   => $not_user,
									'received_user_id' => $another_user_id,
								)
							);
							$email_title = wpqa_send_mail(
								array(
									'content'          => $email_title,
									'title'            => true,
									'break'            => '',
									'post_id'          => $post_id,
									'sender_user_id'   => $not_user,
									'received_user_id' => $another_user_id,
								)
							);
							wpqa_send_mails(
								array(
									'toEmail'     => esc_html($value->user_email),
									'toEmailName' => esc_html($value->display_name),
									'title'       => $email_title,
									'message'     => $send_text,
								)
							);
							if ($send_email_and_notification_post == "both" && $approved == false) {
								wpqa_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_post","notifications","","post");
							}
						}
					}
				}
			}
			if ($send_email_and_notification_post == "separately") {
				$send_notification_new_post = wpqa_options("send_notification_new_post");
				if ($send_notification_new_post == "on") {
					$user_group = wpqa_options("send_notification_post_groups");
					$users = get_users(array("role__in" => (isset($user_group) && is_array($user_group)?$user_group:array()),"fields" => array("ID","user_email","display_name")));
					if (isset($users) && is_array($users) && !empty($users)) {
						foreach ($users as $key => $value) {
							$another_user_id = $value->ID;
							if ($get_current_user_id != $another_user_id && $another_user_id > 0 && $not_user != $another_user_id && $approved == false) {
								wpqa_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_post","notifications","","post");
							}
						}
					}
				}
			}
		}
	}
endif;?>