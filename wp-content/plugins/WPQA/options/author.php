<?php

/* @author    2codeThemes
*  @package   WPQA/options
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Author options */
function wpqa_admin_author($user_id = "") {
	global $pagenow;
	$options = array();
	if ((isset($_REQUEST["user_action"]) && $_REQUEST["user_action"] == "edit_profile") || (is_admin() && ($pagenow == "profile.php" || (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != "") && $pagenow == "user-edit.php"))) {
		$options = apply_filters('wpqa_the_author_options',$options,$user_id,$pagenow);
	}
	return $options;
}
/* Options */
add_filter("wpqa_the_author_options","wpqa_the_author_options",1,3);
function wpqa_the_author_options($options,$user_id,$pagenow) {
	if ((isset($_REQUEST["user_action"]) && $_REQUEST["user_action"] == "edit_profile") || (is_admin() && ($pagenow == "profile.php" || (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != "") && $pagenow == "user-edit.php"))) {
		$active_message = wpqa_options("active_message");
		$subscriptions_payment = wpqa_options("subscriptions_payment");
		$user = get_userdata($user_id);
		$user_group = wpqa_get_user_group($user);
		$get_current_user_id = get_current_user_id();
		
		if (is_super_admin($get_current_user_id)) {
			$pay_to_user = wpqa_pay_to_user($user_id,$user);
			if ($pay_to_user == true) {
				$financial_payments = get_user_meta($user_id,"financial_payments",true);
				if ($financial_payments != "") {
					if ($financial_payments == "paypal") {
						$paypal_email = get_user_meta($user_id,"paypal_email",true);
					}else if ($financial_payments == "payoneer") {
						$payoneer_email = get_user_meta($user_id,"payoneer_email",true);
					}else if ($financial_payments == "bank") {
						$bank_account_holder = get_user_meta($user_id,"bank_account_holder",true);
						$bank_your_address = get_user_meta($user_id,"bank_your_address",true);
						$bank_name = get_user_meta($user_id,"bank_name",true);
						$bank_address = get_user_meta($user_id,"bank_address",true);
						$bank_swift_iban = get_user_meta($user_id,"bank_swift_iban",true);
						$bank_account_number = get_user_meta($user_id,"bank_account_number",true);
						$bank_extra_note = get_user_meta($user_id,"bank_extra_note",true);
					}else if ($financial_payments == "crypto") {
						$crypto_token_name = get_user_meta($user_id,"crypto_token_name",true);
						$crypto_wallet_address = get_user_meta($user_id,"crypto_wallet_address",true);
					}
				}
				if ($financial_payments == "") {
					$show_payment_message = true;
				}else if ($financial_payments != "") {
					if ($financial_payments == "paypal" && $paypal_email == "") {
						$show_payment_message = true;
					}else if ($financial_payments == "payoneer" && $payoneer_email == "") {
						$show_payment_message = true;
					}else if ($financial_payments == "bank") {
						if ($bank_account_holder == "" || $bank_your_address == "" || $bank_name == "" || $bank_address == "" || $bank_swift_iban == "" || $bank_account_number == "") {
							$show_payment_message = true;
						}
					}else if ($financial_payments == "crypto") {
						if ($crypto_token_name == "" || $crypto_wallet_address == "") {
							$show_payment_message = true;
						}
					}
				}
				if (!isset($show_payment_message)) {
					$options[] = array(
						'name' => esc_html__('User payments','wpqa'),
						'type' => 'heading-2'
					);

					$payment_html = '<div id="user-payment-methods" class="custom-meta-field">
						<ul>';
							if ($financial_payments == "paypal") {
								$payment_html .= '<li><span class="dashicons dashicons-email-alt"></span> '.esc_html__("PayPal","wpqa").': '.$paypal_email.'<br></li>';
							}else if ($financial_payments == "payoneer") {
								$payment_html .= '<li><span class="dashicons dashicons-email-alt"></span> '.esc_html__("Payoneer","wpqa").': '.$payoneer_email.'<br></li>';
							}else if ($financial_payments == "bank") {
								$payment_html .= '<li><span class="dashicons dashicons-admin-users"></span> '.esc_html__("Name of the Account Holder","wpqa").': '.$bank_account_holder.'<br><br></li>
								<li><span class="dashicons dashicons-location-alt"></span> '.esc_html__("Your Address","wpqa").': '.$bank_your_address.'<br><br></li>
								<li><span class="dashicons dashicons-flag"></span> '.esc_html__("Bank Name","wpqa").': '.$bank_name.'<br><br></li>
								<li><span class="dashicons dashicons-location"></span> '.esc_html__("Bank Address","wpqa").': '.$bank_address.'<br><br></li>
								<li><span class="dashicons dashicons-edit-large"></span> '.esc_html__("SWIFT/IBAN Code","wpqa").': '.$bank_swift_iban.'<br><br></li>
								<li><span class="dashicons dashicons-id"></span> '.esc_html__("Account Number","wpqa").': '.$bank_account_number.'</li>';
								if ($bank_extra_note != "") {
									$payment_html .= '<li><br><span class="dashicons dashicons-edit-large"></span> '.esc_html__("Extra note","wpqa").': '.$bank_extra_note.'</li>';
								}
							}else if ($financial_payments == "crypto") {
								$payment_html .= '<li><span class="dashicons dashicons-id"></span> '.esc_html__("Coin/Token Name","wpqa").': '.$crypto_token_name.'<br><br></li>
								<li><span class="dashicons dashicons-edit-large"></span> '.esc_html__("Wallet Address","wpqa").': '.$crypto_wallet_address.'</li>';
							}
						$payment_html .= '</ul>
						<div class="no-user-question"></div>';
					$payment_html .= '</div>';
					
					$options[] = array(
						'type'    => 'content',
						'content' => $payment_html,
					);

					$options[] = array(
						'type' => 'heading-2',
						'end'  => 'end'
					);
				}
			}

			$options = apply_filters(wpqa_prefix_theme.'_options_before_admin_moderation_all',$options,$user_id);

			$options[] = array(
				'name' => esc_html__('Admin moderation','wpqa'),
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'id'    => 'from_admin',
				'std'   => 'yes',
				'type'  => 'hidden',
				'unset' => 'unset',
			);

			$options = apply_filters(wpqa_prefix_theme.'_options_before_admin_moderation',$options,$user_id);

			$active_points = wpqa_options("active_points");
			if ($active_points == "on") {
				$_whats_types = get_user_meta($user_id,$user_id."_points",true);
				if (isset($_whats_types) && $_whats_types > 0) {
					$html_content = '<a class="button delete-rows" data-history="points" data-user="'.$user_id.'" href="'.admin_url("user-edit.php?user_id=".$user_id."&rows=delete&delete_type=points").'">'.esc_html__("Delete all the points history","wpqa").'</a>';
					$options[] = array(
						'name' => $html_content,
						'type' => 'info'
					);
				}
			}

			$html_content = '<a class="button fixed-counts" data-user="'.$user_id.'" href="'.admin_url("user-edit.php?user_id=".$user_id."&rows=fixed").'">'.esc_html__("Fix all the user counts","wpqa").'</a>';
			$options[] = array(
				'name' => $html_content,
				'type' => 'info'
			);

			$if_user_id = get_user_by("id",$user_id);
			$package_subscribe = get_user_meta($user_id,"package_subscribe",true);
			if ((($subscriptions_payment == "on" && $package_subscribe != "") || (isset($if_user_id->caps["activation"]) && $if_user_id->caps["activation"] == 1) || (isset($if_user_id->caps["wpqa_under_review"]) && $if_user_id->caps["wpqa_under_review"] == 1)) && $get_current_user_id != $user_id) {
				$protocol = is_ssl() ? 'https' : 'http';
				$options[] = array(
					'std'   => urldecode(wp_unslash($protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])),
					'id'    => 'redirect_to',
					'type'  => 'hidden',
					'unset' => 'unset',
				);
				if ($subscriptions_payment == "on" && $package_subscribe != "") {
					$html_content = '<a class="button user-action-buttons" data-action="remove_subscription" data-user="'.$user_id.'" href="'.admin_url("user-edit.php?user_id=".$user_id).'">'.esc_html__("Remove from the subscription","wpqa").'</a>';
					$options[] = array(
						'name' => $html_content,
						'type' => 'info'
					);
				}else if (isset($if_user_id->caps["activation"]) && $if_user_id->caps["activation"] == 1) {
					$html_content = '<a class="button user-action-buttons" data-action="activate_user" data-user="'.$user_id.'" href="'.admin_url("user-edit.php?user_id=".$user_id).'">'.esc_html__("Activate this user","wpqa").'</a>';
					$options[] = array(
						'name' => $html_content,
						'type' => 'info'
					);
				}else {
					$html_content = '<a class="button user-action-buttons" data-action="approve_user" data-user="'.$user_id.'" href="'.admin_url("user-edit.php?user_id=".$user_id).'">'.esc_html__("Approve this user","wpqa").'</a>';
					$options[] = array(
						'name' => $html_content,
						'type' => 'info'
					);
				}
			}else if ($subscriptions_payment == "on" && $package_subscribe == "") {
				$options[] = array(
					'name'  => esc_html__('Select ON to add this user to subscription plan.','wpqa'),
					'id'    => 'add_subscription',
					'type'  => 'checkbox',
					'unset' => 'unset',
				);
				
				$role_plans = wpqa_subscription_role_options();

				$options[] = array(
					'name'      => esc_html__('Subscription plan','wpqa'),
					'id'        => 'subscription_plan',
					'type'      => 'radio',
					'condition' => 'add_subscription:not(0)',
					'unset'     => 'unset',
					'std'       => 'monthly',
					'options'   => $role_plans
				);
				
			}
			
			$options[] = array(
				'name' => esc_html__('Check ON if you need to assign a custom badge for this user.','wpqa'),
				'id'   => 'custom_user_badge',
				'type' => 'checkbox',
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => 'custom_user_badge:not(0)',
				'type'      => 'heading-2'
			);

			$options[] = array(
				'name' => esc_html__('Add the custom badge name.','wpqa'),
				'id'   => 'custom_badge_name',
				'type' => 'text',
			);

			$options[] = array(
				'name' => esc_html__('Add the custom badge color.','wpqa'),
				'id'   => 'custom_badge_color',
				'type' => 'color',
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'name' => esc_html__('Check ON if you need this user to be a verified user.','wpqa'),
				'id'   => 'verified_user',
				'type' => 'checkbox',
			);

			$active_moderators = wpqa_options("active_moderators");
			if (!is_super_admin($user_id) && $active_moderators == "on") {
				$options[] = array(
					'name' => esc_html__('Choose this user as a moderator.','wpqa'),
					'id'   => prefix_author.'user_moderator',
					'type' => 'checkbox',
				);

				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_author.'user_moderator:not(0)',
					'type'      => 'heading-2'
				);
				
				$options[] = array(
					'name'        => esc_html__('Choose the question categories you want this user to moderate.','wpqa'),
					'id'          => prefix_author.'question_categories_show',
					'type'        => 'custom_addition',
					'addto'       => prefix_author.'moderator_categories',
					'toadd'       => 'yes',
					'taxonomy'    => wpqa_question_categories,
					'show_option' => esc_html__('All Question Categories','wpqa'),
					'option_none' => 'q-0'
				);

				$options[] = array(
					'name'        => esc_html__('Choose the post categories you want this user to moderate.','wpqa'),
					'id'          => prefix_author.'post_categories_show',
					'type'        => 'custom_addition',
					'addto'       => prefix_author.'moderator_categories',
					'toadd'       => 'yes',
					'taxonomy'    => 'category',
					'show_option' => esc_html__('All Post Categories','wpqa'),
					'option_none' => 'p-0'
				);

				$moderator_categories = get_user_meta($user_id,prefix_author."moderator_categories",true);
				$moderator_categories = (is_array($moderator_categories) && !empty($moderator_categories)?$moderator_categories:array());
				foreach ($moderator_categories as $key_cat => $value_cat) {
					$moderator_categories["cat-".$value_cat] = array("cat" => "yes","value" => $value_cat);
				}
				
				$options[] = array(
					'id'      => prefix_author.'moderator_categories',
					'type'    => 'multicheck',
					'sort'    => 'yes',
					'unset'   => 'unset',
					'val'     => $moderator_categories,
					'options' => array()
				);
				
				$options[] = array(
					'name' => esc_html__('Check ON if you need to choose custom moderators permissions for this user.','wpqa'),
					'id'   => prefix_author.'custom_moderators_permissions',
					'type' => 'checkbox',
				);

				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_author.'custom_moderators_permissions:not(0)',
					'type'      => 'heading-2'
				);

				$options[] = array(
					'name'    => esc_html__('Select the custom permissions for this user','wpqa'),
					'id'      => prefix_author.'moderators_permissions',
					'type'    => 'multicheck',
					'std'     => array(
						"delete"      => "delete",
						"close"       => "close",
						"best"        => "best",
						"approve"     => "approve",
						"edit"        => "edit",
						"ban"         => "ban",
						"edit_groups" => "edit_groups",
					),
					'options' => array(
						"delete"      => esc_html__('Delete questions','wpqa'),
						"close"       => esc_html__('Close and open questions','wpqa'),
						"best"        => esc_html__('Choose and cancel the best answers','wpqa'),
						"approve"     => esc_html__('Approve questions','wpqa'),
						"edit"        => esc_html__('Edit questions','wpqa'),
						"ban"         => esc_html__("Ban users","wpqa"),
						"edit_groups" => esc_html__("Edit the other groups with the full editing permissions","wpqa"),
					)
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
			}
			
			if (!is_super_admin($user_id) && $active_message == "on") {
				$options[] = array(
					'name' => esc_html__('Block this user to send messages?','wpqa'),
					'id'   => 'block_message',
					'type' => 'checkbox',
				);
			}
			
			$active_points = wpqa_options("active_points");
			if ($active_points == "on") {
				$options[] = array(
					'name'    => esc_html__('Add or remove points for the user','wpqa'),
					'id'      => 'add_remove_point',
					'type'    => 'select',
					'unset'   => 'unset',
					'options' => array('add' => esc_html__('Add','wpqa'),'remove' => esc_html__('Remove','wpqa'))
				);
				
				$options[] = array(
					'name'  => esc_html__('The points','wpqa'),
					'id'    => 'the_points',
					'type'  => 'text',
					'unset' => 'unset',
				);
				
				$options[] = array(
					'name'  => esc_html__('The reason','wpqa'),
					'id'    => 'the_reason',
					'type'  => 'text',
					'unset' => 'unset',
				);
			}
			
			if ($get_current_user_id != $user_id) {
				$options[] = array(
					'name'  => esc_html__('Check ON if you need this user to be able to Choose or Remove the best answer','wpqa'),
					'id'    => 'user_best_answer',
					'type'  => 'checkbox',
				);
				
				$options[] = array(
					'id'    => 'admin',
					'std'   => 'save',
					'type'  => 'hidden',
					'unset' => 'unset',
				);
			}

			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
		}

		$options[] = array(
			'name' => esc_html__('Author Setting','wpqa'),
			'type' => 'heading-2'
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_before_author_setting',$options,$user_id);
		
		if (current_user_can('upload_files')) {
			$user_meta_avatar = wpqa_options("user_meta_avatar");
			$user_meta_avatar = apply_filters("wpqa_user_meta_avatar",$user_meta_avatar);
			$user_meta_avatar = ($user_meta_avatar != ""?$user_meta_avatar:"you_avatar");
			$options[] = array(
				'name' => esc_html__('Your avatar','wpqa'),
				'id'   => $user_meta_avatar,
				'type' => 'upload'
			);

			$cover_image = wpqa_options("cover_image");
			if ($cover_image == "on") {
				$user_meta_cover = wpqa_options("user_meta_cover");
				$user_meta_cover = apply_filters("wpqa_user_meta_cover",$user_meta_cover);
				$user_meta_cover = ($user_meta_cover != ""?$user_meta_cover:"your_cover");
				$options[] = array(
					'name' => esc_html__('Your cover','wpqa'),
					'id'   => $user_meta_cover,
					'type' => 'upload'
				);
			}
		}
		
		$options[] = array(
			'name' => esc_html__('Add profile credential','wpqa'),
			'id'   => 'profile_credential',
			'type' => 'text',
		);
		
		$options[] = array(
			'name'    => esc_html__('Country','wpqa'),
			'id'      => 'country',
			'first'   => esc_html__('Select a country&hellip;','wpqa'),
			'type'    => 'select',
			'class'   => 'framework-custom-menu',
			'options' => apply_filters('wpqa_get_countries',false)
		);
		
		$options[] = array(
			'name' => esc_html__('City','wpqa'),
			'id'   => 'city',
			'type' => 'text',
		);
		
		$options[] = array(
			'name' => esc_html__('Age','wpqa'),
			'id'   => 'age',
			'type' => 'date',
			'js'   => array("changeMonth" => true,"changeYear" => true,"yearRange" => "-90:+00","dateFormat" => "yy-mm-dd"),
		);

		$options = apply_filters(wpqa_prefix_theme.'_options_before_phone_setting',$options,$user_id);
		
		$options[] = array(
			'name' => esc_html__('Phone','wpqa'),
			'id'   => 'phone',
			'type' => 'text',
		);
		
		$gender_other = wpqa_options("gender_other");
		$gender_other = ($gender_other == "on"?array('3' => esc_html__('Other','wpqa')):array());
		$gender_options = array('1' => esc_html__('Male','wpqa'),'2' => esc_html__('Female','wpqa'))+$gender_other;
		$gender = get_user_meta($user_id,"gender",true);
		
		$options[] = array(
			'name'    => esc_html__('Gender','wpqa'),
			'id'      => 'gender',
			'type'    => 'radio',
			'val'     => ($gender == "male" || $gender == 1?1:"").($gender == "female" || $gender == 2?2:"").($gender == "other" || $gender == 3?3:""),
			'options' => $gender_options
		);
		
		$options[] = array(
			'name' => esc_html__('Show your private pages for all the users? (Points, favorite and followed pages).','wpqa'),
			'id'   => 'show_point_favorite',
			'type' => 'checkbox',
		);
		
		if ($active_message == "on") {
			$options[] = array(
				'name'      => esc_html__('Would you like to receive message from other users?','wpqa'),
				'id'        => 'received_message',
				'condition' => prefix_author.'unsubscribe_mails:not(on)',
				'type'      => 'checkbox',
			);
		}
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Mail settings','wpqa'),
			'type' => 'heading-2'
		);

		$options[] = array(
			'name'      => esc_html__('Would you like to receive an email for the new payments?','wpqa'),
			'id'        => 'new_payment_mail',
			'std'       => 'on',
			'condition' => 'unsubscribe_mails:not(on)',
			'type'      => 'checkbox',
		);

		$options[] = array(
			'name'      => esc_html__('Would you like to receive an email when you receive new messages?','wpqa'),
			'id'        => 'send_message_mail',
			'std'       => 'on',
			'condition' => 'unsubscribe_mails:not(on)',
			'type'      => 'checkbox',
		);

		$options[] = array(
			'name'      => esc_html__('Would you like to receive an email when new answers are added to your questions?','wpqa'),
			'id'        => 'answer_on_your_question',
			'std'       => 'on',
			'condition' => 'unsubscribe_mails:not(on)',
			'type'      => 'checkbox',
		);

		$options[] = array(
			'name'      => esc_html__('Would you like to receive an email when new answers are added to the question that you follow?','wpqa'),
			'id'        => 'answer_question_follow',
			'std'       => 'on',
			'condition' => 'unsubscribe_mails:not(on)',
			'type'      => 'checkbox',
		);

		$options[] = array(
			'name'      => esc_html__('Would you like to receive an email when new replies are added to your answers?','wpqa'),
			'id'        => 'notified_reply',
			'std'       => 'on',
			'condition' => 'unsubscribe_mails:not(on)',
			'type'      => 'checkbox',
		);

		$options[] = array(
			'name'      => esc_html__('Would you like to receive an email when user mentioned you?','wpqa'),
			'id'        => 'notification_mention',
			'std'       => 'on',
			'condition' => 'unsubscribe_mails:not(on)',
			'type'      => 'checkbox',
		);
		
		$question_schedules = wpqa_options("question_schedules");
		$question_schedules_groups = wpqa_options("question_schedules_groups");
		if ($question_schedules == "on" && is_array($question_schedules_groups) && in_array($user_group,$question_schedules_groups)) {
			$options[] = array(
				'name'      => esc_html__('Would you like to receive scheduled mails for the recent questions?','wpqa'),
				'id'        => 'question_schedules',
				'condition' => 'unsubscribe_mails:not(on)',
				'type'      => 'checkbox',
			);
		}
		
		$send_email_and_notification_question = wpqa_options("send_email_and_notification_question");
		$send_email_new_question_value = "send_email_new_question";
		$send_email_question_groups_value = "send_email_question_groups";
		if ($send_email_and_notification_question == "both") {
			$send_email_new_question_value = "send_email_new_question_both";
			$send_email_question_groups_value = "send_email_question_groups_both";
		}
		$send_email_new_question = wpqa_options($send_email_new_question_value);
		$send_email_question_groups = wpqa_options($send_email_question_groups_value);
		if ($send_email_new_question == "on" && is_array($send_email_question_groups) && in_array($user_group,$send_email_question_groups)) {
			$options[] = array(
				'name'      => esc_html__('Would you like to receive an email when new questions are added?','wpqa'),
				'id'        => 'received_email',
				'condition' => 'unsubscribe_mails:not(on)',
				'std'       => 'on',
				'type'      => 'checkbox',
			);
		}
		
		$post_schedules = wpqa_options("post_schedules");
		$post_schedules_groups = wpqa_options("post_schedules_groups");
		if ($post_schedules == "on" && is_array($post_schedules_groups) && in_array($user_group,$post_schedules_groups)) {
			$options[] = array(
				'name'      => esc_html__('Would you like to receive scheduled mails for the recent posts?','wpqa'),
				'id'        => 'post_schedules',
				'condition' => 'unsubscribe_mails:not(on)',
				'type'      => 'checkbox',
			);
		}
		
		$send_email_and_notification_post = wpqa_options("send_email_and_notification_post");
		$send_email_new_post_value = "send_email_new_post";
		$send_email_post_groups_value = "send_email_post_groups";
		if ($send_email_and_notification_post == "both") {
			$send_email_new_post_value = "send_email_new_post_both";
			$send_email_post_groups_value = "send_email_post_groups_both";
		}
		$send_email_new_post = wpqa_options($send_email_new_post_value);
		$send_email_post_groups = wpqa_options($send_email_post_groups_value);
		if ($send_email_new_post == "on" && is_array($send_email_post_groups) && in_array($user_group,$send_email_post_groups)) {
			$options[] = array(
				'name'      => esc_html__('Would you like to receive an email when new posts are added?','wpqa'),
				'id'        => 'received_email_post',
				'condition' => 'unsubscribe_mails:not(on)',
				'std'       => 'on',
				'type'      => 'checkbox',
			);
		}

		$options[] = array(
			'name' => esc_html__('Would you like to unsubscribe from all the mails?','wpqa'),
			'id'   => 'unsubscribe_mails',
			'type' => 'checkbox',
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Social Networking','wpqa'),
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Facebook','wpqa'),
			'id'   => 'facebook',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Twitter','wpqa'),
			'id'   => 'twitter',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('TikTok','wpqa'),
			'id'   => 'tiktok',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Linkedin','wpqa'),
			'id'   => 'linkedin',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Pinterest','wpqa'),
			'id'   => 'pinterest',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Instagram','wpqa'),
			'id'   => 'instagram',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Youtube','wpqa'),
			'id'   => 'youtube',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Vimeo','wpqa'),
			'id'   => 'vimeo',
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		if (is_super_admin($get_current_user_id)) {
			$options[] = array(
				'name' => esc_html__('Advertising','wpqa'),
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'type' => 'info',
				'name' => esc_html__('Advertising after header 1','wpqa')
			);
			
			$options[] = array(
				'name'    => esc_html__('Advertising type','wpqa'),
				'id'      => prefix_author.'header_adv_type_1',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','wpqa'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
				'id'        => prefix_author.'header_adv_img_1',
				'condition' => prefix_author.'header_adv_type_1:is(custom_image)',
				'type'      => 'upload'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising url','wpqa'),
				'id'        => prefix_author.'header_adv_href_1',
				'std'       => '#',
				'condition' => prefix_author.'header_adv_type_1:is(custom_image)',
				'type'      => 'text'
			);

			$options[] = array(
				'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'        => prefix_author.'header_adv_link_1',
				'std'       => "new_page",
				'type'      => 'select',
				'condition' => prefix_author.'header_adv_type_1:is(custom_image)',
				'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
				'id'        => prefix_author.'header_adv_code_1',
				'condition' => prefix_author.'header_adv_type_1:is(display_code)',
				'type'      => 'textarea'
			);
			
			$options[] = array(
				'type' => 'info',
				'name' => esc_html__('Advertising after left menu','wpqa')
			);
			
			$options[] = array(
				'name'    => esc_html__('Advertising type','wpqa'),
				'id'      => prefix_author.'left_menu_adv_type',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','wpqa'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
				'id'        => prefix_author.'left_menu_adv_img',
				'type'      => 'upload',
				'condition' => prefix_author.'left_menu_adv_type:is(custom_image)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising url','wpqa'),
				'id'        => prefix_author.'left_menu_adv_href',
				'std'       => '#',
				'type'      => 'text',
				'condition' => prefix_author.'left_menu_adv_type:is(custom_image)'
			);

			$options[] = array(
				'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'        => prefix_author.'left_menu_adv_link',
				'std'       => "new_page",
				'type'      => 'select',
				'condition' => prefix_author.'left_menu_adv_type:is(custom_image)',
				'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
				'id'        => prefix_author.'left_menu_adv_code',
				'type'      => 'textarea',
				'condition' => prefix_author.'left_menu_adv_type:is(display_code)'
			);
			
			$options[] = array(
				'type' => 'info',
				'name' => esc_html__('Advertising after content','wpqa')
			);
			
			$options[] = array(
				'name'    => esc_html__('Advertising type','wpqa'),
				'id'      => prefix_author.'content_adv_type',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','wpqa'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
				'id'        => prefix_author.'content_adv_img',
				'type'      => 'upload',
				'condition' => prefix_author.'content_adv_type:is(custom_image)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising url','wpqa'),
				'id'        => prefix_author.'content_adv_href',
				'std'       => '#',
				'type'      => 'text',
				'condition' => prefix_author.'content_adv_type:is(custom_image)'
			);

			$options[] = array(
				'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'        => prefix_author.'content_adv_link',
				'std'       => "new_page",
				'type'      => 'select',
				'condition' => prefix_author.'content_adv_type:is(custom_image)',
				'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
				'id'        => prefix_author.'content_adv_code',
				'type'      => 'textarea',
				'condition' => prefix_author.'content_adv_type:is(display_code)'
			);
			
			$options[] = array(
				'name' => esc_html__('Between questions or posts','wpqa'),
				'type' => 'info'
			);
			
			$options[] = array(
				'name'    => esc_html__('Advertising type','wpqa'),
				'id'      => prefix_author.'between_adv_type',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','wpqa'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
				'id'        => prefix_author.'between_adv_img',
				'condition' => prefix_author.'between_adv_type:is(custom_image)',
				'type'      => 'upload'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising url','wpqa'),
				'id'        => prefix_author.'between_adv_href',
				'std'       => '#',
				'condition' => prefix_author.'between_adv_type:is(custom_image)',
				'type'      => 'text'
			);

			$options[] = array(
				'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'        => prefix_author.'between_adv_link',
				'std'       => "new_page",
				'type'      => 'select',
				'condition' => prefix_author.'between_adv_type:is(custom_image)',
				'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html (Ex: Google ads)','wpqa'),
				'id'        => prefix_author.'between_adv_code',
				'condition' => prefix_author.'between_adv_type:not(custom_image)',
				'type'      => 'textarea'
			);
			
			$options[] = array(
				'name' => esc_html__('Between comments or answers','wpqa'),
				'type' => 'info'
			);
			
			$options[] = array(
				'name'    => esc_html__('Advertising type','wpqa'),
				'id'      => prefix_author.'between_comments_adv_type',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','wpqa'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
				'id'        => prefix_author.'between_comments_adv_img',
				'condition' => prefix_author.'between_comments_adv_type:is(custom_image)',
				'type'      => 'upload'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising url','wpqa'),
				'id'        => prefix_author.'between_comments_adv_href',
				'std'       => '#',
				'condition' => prefix_author.'between_comments_adv_type:is(custom_image)',
				'type'      => 'text'
			);

			$options[] = array(
				'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'        => prefix_author.'between_comments_adv_link',
				'std'       => "new_page",
				'type'      => 'select',
				'condition' => prefix_author.'between_comments_adv_type:is(custom_image)',
				'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html (Ex: Google ads)','wpqa'),
				'id'        => prefix_author.'between_comments_adv_code',
				'condition' => prefix_author.'between_comments_adv_type:not(custom_image)',
				'type'      => 'textarea'
			);

			$options = apply_filters('wpqa_options_after_author_ads',$options,$user_id);
		}
	}
	return $options;
}?>