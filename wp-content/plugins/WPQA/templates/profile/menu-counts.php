<?php

/* @author    2codeThemes
*  @package   WPQA/templates/profile
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$wpqa_user_id = (isset($wpqa_user_id)?$wpqa_user_id:(isset($user_id)?$user_id:0));
if ($tab_item == "asked_questions") {
	$ask_question_to_users = wpqa_options("ask_question_to_users");
	if ($ask_question_to_users == "on") {
		$asked_questions = wpqa_count_asked_question($wpqa_user_id,"=");
		echo ($asked_questions > 0 && wpqa_is_user_owner()?"<span class='notifications-number notifications-count asked-count".($asked_questions <= 99?"":" notifications-number-super")."'>".($asked_questions <= 99?$asked_questions:"99+")."</span>":"");
	}
}else if ($tab_item == "pending-questions" || $tab_item == "pending-posts") {
	$is_super_admin = is_super_admin($wpqa_user_id);
	$active_moderators = wpqa_options("active_moderators");
	if (($is_super_admin || $active_moderators == "on") && ($tab_item == "pending-questions" || $tab_item == "pending-posts")) {
		$user_moderator = get_user_meta($wpqa_user_id,prefix_author."user_moderator",true);
		if ($is_super_admin || $user_moderator == "on") {
			$moderator_categories = get_user_meta($wpqa_user_id,prefix_author."moderator_categories",true);
			if ($is_super_admin || (is_array($moderator_categories) && !empty($moderator_categories))) {
				$num_pending_questions = $num_pending_posts = 0;
				if ($tab_item == "pending-questions") {
					if ($is_super_admin || in_array("q-0",$moderator_categories)) {
						$num_pending_questions = wpqa_count_posts_by_type(array(wpqa_questions_type,wpqa_asked_questions_type),"draft");
					}else {
						$moderator_categories_questions = wpqa_remove_item_by_value($moderator_categories,"p-0");
						if (is_array($moderator_categories_questions) && !empty($moderator_categories_questions)) {
							$num_pending_questions = wpqa_count_posts_by_user(0,wpqa_questions_type,"draft",$moderator_categories_questions);
						}
					}
				}
				if ($tab_item == "pending-posts") {
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
		if ($tab_item == "pending-questions") {
			$num_pending_questions = (isset($num_pending_questions) && $num_pending_questions > 0?($num_pending_questions <= 99?$num_pending_questions:"99+"):"");
			echo ($num_pending_questions != ''?'<span class="notifications-number notifications-count">'.wpqa_count_number($num_pending_questions).'</span>':'');
		}
		if ($tab_item == "pending-posts") {
			$num_pending_posts = (isset($num_pending_posts) && $num_pending_posts > 0?($num_pending_posts <= 99?$num_pending_posts:"99+"):"");
			echo ($num_pending_posts != ''?'<span class="notifications-number notifications-count">'.wpqa_count_number($num_pending_posts).'</span>':'');
		}
	}
}else if ($tab_item == "notifications") {
	$active_notifications = wpqa_options("active_notifications");
	if ($active_notifications == "on") {
		$num_notification = (int)get_user_meta($wpqa_user_id,$wpqa_user_id.'_new_notification',true);
		$num_notification = ($num_notification > 0?($num_notification <= 99?$num_notification:"99+"):"");
		echo ($num_notification != ''?'<span class="notifications-number notifications-count">'.wpqa_count_number($num_notification).'</span>':'');
	}
}else if ($tab_item == "messages") {
	$active_message = wpqa_options("active_message");
	if ($active_message == "on") {
		$num_message = (int)($wpqa_user_id > 0?get_user_meta($wpqa_user_id,"wpqa_new_messages_count",true):0);
		$num_message = ($num_message > 0?($num_message <= 99?$num_message:"99+"):'');
		echo ($num_message != ''?'<span class="notifications-number notifications-count">'.wpqa_count_number($num_message).'</span>':'');
	}
}