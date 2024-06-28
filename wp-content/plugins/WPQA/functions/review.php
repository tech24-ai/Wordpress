<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Review posts and questions */
if (!function_exists('wpqa_review_post')) :
	function wpqa_review_post($post,$is_super_admin,$moderators_permissions) {
		$post_type = $post->post_type;
		if ($is_super_admin || (isset($moderators_permissions['delete']) && $moderators_permissions['delete'] == "delete") || (isset($moderators_permissions['approve']) && $moderators_permissions['approve'] == "approve") || (isset($moderators_permissions['edit']) && $moderators_permissions['edit'] == "edit") || (isset($moderators_permissions['ban']) && $moderators_permissions['ban'] == "ban")) {?>
			<a class="meta-answer review-post btn btn__primary btn__sm" href="#"><?php echo ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?esc_html__("Review the question","wpqa"):esc_html__("Review the post","wpqa"))?></a>
			<?php if ($is_super_admin || (isset($moderators_permissions['approve']) && $moderators_permissions['approve'] == "approve")) {
				echo '<a data-nonce="'.wp_create_nonce("pending_nonce").'" class="meta-answer pending-post-meta a-pending-post wpqa_hide btn btn__success btn__sm" href="#"><i class="icon-cog"></i>'.esc_html__("Approve","wpqa").'</a>';
			}
			if ($is_super_admin || (isset($moderators_permissions['edit']) && $moderators_permissions['edit'] == "edit")) {
				echo '<a class="meta-answer pending-post-meta e-pending-post wpqa_hide btn btn__primary btn__sm" href="'.esc_url_raw(add_query_arg(array("page" => "pending"),wpqa_edit_permalink($post->ID,$post_type))).'"><i class="icon-pencil"></i>'.esc_html__("Edit","wpqa").'</a>';
			}
			if ($is_super_admin || (isset($moderators_permissions['delete']) && $moderators_permissions['delete'] == "delete")) {
				echo '<a data-nonce="'.wp_create_nonce("pending_nonce").'" class="meta-answer pending-post-meta d-pending-post wpqa_hide btn btn__danger btn__sm delete-pending-'.$post_type.'" href="#"><i class="icon-trash"></i>'.esc_html__("Delete","wpqa").'</a>';
			}
			if ($is_super_admin || (isset($moderators_permissions['ban']) && $moderators_permissions['ban'] == "ban")) {
				$post_author = $post->post_author;
				$not_user = $anonymously_user = 0;
				if ($post_author > 0) {
					$not_user = $post_author;
				}else {
					$anonymously_user = get_post_meta($post->ID,'anonymously_user',true);
				}
				$if_user_id = get_user_by("id",($anonymously_user > 0?$anonymously_user:$not_user));
				$if_ban = (isset($if_user_id->caps["ban_group"]) && $if_user_id->caps["ban_group"] == 1?true:false);
				echo '<a data-nonce="'.wp_create_nonce("pending_nonce").'" class="meta-answer pending-post-meta wpqa_hide btn btn__sm '.($if_ban?"u-pending-post btn__success":"b-pending-post btn__secondary").'" href="#"><i class="'.($if_ban?"icon-back":"icon-cancel-circled").'"></i><span>'.($if_ban?esc_html__("Unban user","wpqa"):esc_html__("Ban user","wpqa")).'</span></a>';
			}
		}
	}
endif;
/* Approve question or post */
if (!function_exists('wpqa_pending_post')) :
	function wpqa_pending_post() {
		check_ajax_referer('pending_nonce','pending_nonce');
		$user_id = get_current_user_id();
		$is_super_admin = is_super_admin($user_id);
		$pending_type = esc_html($_POST['pending_type']);
		$moderators_permissions = wpqa_user_moderator($user_id);
		if ((($pending_type == "ban" || $pending_type == "unban") && ($is_super_admin || (isset($moderators_permissions['ban']) && $moderators_permissions['ban'] == "ban")) || ($pending_type == "delete" && ($is_super_admin || isset($moderators_permissions['delete']) && $moderators_permissions['delete'] == "delete"))) || ($pending_type == "approve" && ($is_super_admin || (isset($moderators_permissions['approve']) && $moderators_permissions['approve'] == "approve")))) {
			$post_id = (int)$_POST['post_id'];
			$get_post = get_post($post_id);
			$post_id = (isset($get_post->ID) && $get_post->ID > 0?$get_post->ID:0);
			$post_type = $get_post->post_type;
			if ($post_id > 0) {
				$post_author = $get_post->post_author;
				$not_user = $anonymously_user = 0;
				if ($post_author > 0) {
					$not_user = $post_author;
				}else {
					$anonymously_user = get_post_meta($post_id,'anonymously_user',true);
				}
			}
		}
		if (($pending_type == "ban" || $pending_type == "unban") && ($is_super_admin || (isset($moderators_permissions['ban']) && $moderators_permissions['ban'] == "ban"))) {
			if ($post_id > 0 && ($post_author > 0 || $anonymously_user > 0)) {
				wpqa_ban_unban_user(($anonymously_user > 0?$anonymously_user:$not_user),$pending_type,$user_id);
			}
		}else if (($pending_type == "delete" && ($is_super_admin || (isset($moderators_permissions['delete']) && $moderators_permissions['delete'] == "delete"))) || ($pending_type == "approve" && ($is_super_admin || (isset($moderators_permissions['approve']) && $moderators_permissions['approve'] == "approve")))) {
			if ($post_id > 0) {
				$post_status = ($pending_type == 'approve'?'publish':'trash');
				$update_data = array(
					'ID'          => (int)$post_id,
					'post_status' => $post_status,
				);
				remove_action('save_post','wpqa_save_post');
				wp_update_post($update_data);
				$post_approved_before = get_post_meta($post_id,'post_approved_before',true);
				if ($pending_type == 'approve') {
					if ($post_approved_before != "yes") {
						$post_username = get_post_meta($post_id,$post_type.'_username',true);
						$post_email = get_post_meta($post_id,$post_type.'_email',true);
						if ($post_username == "") {
							$post_no_username = get_post_meta($post_id,$post_type.'_no_username',true);
						}
						if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
							wpqa_notifications_ask_question($post_id,$post_username,get_post_meta($post_id,"user_id",true),$not_user,$anonymously_user,$user_id,true);
						}
						if ($post_type == "post") {
							wpqa_notifications_add_post($post_id,$post_username,$not_user,$user_id,true);
						}
						wpqa_post_publish($get_post,$not_user);
						wpqa_post_after_approved($get_post,(isset($not_user)?$not_user:$post_author));
					}
					if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type || $post_type == "post") {
						$way_sending_notifications = wpqa_options("way_sending_notifications_".$post_type."s");
						$schedules_time_notification = wpqa_options("schedules_time_notification_".$post_type."s");
						if ($way_sending_notifications == "cronjob" && $schedules_time_notification != "") {
							update_post_meta($post_id,'wpqa_post_scheduled_email','yes');
							update_post_meta($post_id,'wpqa_post_scheduled_notification','yes');
						}
					}
				}
				if ((($post_approved_before != "yes" && $pending_type == 'approve') || $pending_type == 'delete') && (($not_user > 0 && $not_user != $user_id) || ($anonymously_user > 0 && $anonymously_user != $user_id))) {
					wpqa_notifications_activities(($anonymously_user > 0?$anonymously_user:$not_user),$user_id,"",$post_id,"",($pending_type == "approve"?'approved_'.$post_type:'delete_'.$post_type),"notifications","",$post_type);
				}
			}
		}
		die();
	}
endif;
add_action('wp_ajax_wpqa_pending_post','wpqa_pending_post');
add_action('wp_ajax_nopriv_wpqa_pending_post','wpqa_pending_post');
/* Delete question or post */
if (!function_exists('wpqa_delete_question_post')) :
	function wpqa_delete_question_post() {
		check_ajax_referer('wpqa_delete_nonce','wpqa_delete_nonce');
		$data_id = (int)$_POST["data_id"];
		$data_div = esc_html($_POST["data_div"]);
		$get_post = get_post($data_id);
		$post_author = $get_post->post_author;
		$post_type = $get_post->post_type;
		$anonymously_user = get_post_meta($data_id,"anonymously_user",true);
		if ($post_author > 0 || $anonymously_user > 0) {
			wpqa_notifications_activities(($post_author > 0?$post_author:$anonymously_user),"","","","","delete_".$post_type,"notifications",$data_div,($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?wpqa_questions_type:""));
		}
		do_action("wpqa_after_deleted_post",$get_post);
		wp_delete_post($data_id,true);
		die();
	}
endif;
add_action('wp_ajax_wpqa_delete_question_post','wpqa_delete_question_post');
add_action('wp_ajax_nopriv_wpqa_delete_question_post','wpqa_delete_question_post');
/* Delete comment or answer */
if (!function_exists('wpqa_delete_comment_answer')) :
	function wpqa_delete_comment_answer() {
		check_ajax_referer('wpqa_delete_nonce','wpqa_delete_nonce');
		$data_id = (int)$_POST["data_id"];
		$data_div = esc_html($_POST["data_div"]);
		$comment_type = get_comment_meta($data_id,'comment_type',true);
		$get_comment = get_comment($data_id);
		$anonymously_user = get_comment_meta($data_id,'anonymously_user',true);
		if ($get_comment->user_id > 0 || $anonymously_user > 0) {
			wpqa_notifications_activities(($get_comment->user_id > 0?$get_comment->user_id:$anonymously_user),"","","","","delete_".($comment_type == "question"?"answer":"comment"),"notifications",$data_div,($comment_type == "question"?"answer":"comment"));
		}
		wp_delete_comment($data_id,true);
		do_action("wpqa_after_deleted_comment",$get_comment);
		die();
	}
endif;
add_action('wp_ajax_wpqa_delete_comment_answer','wpqa_delete_comment_answer');
add_action('wp_ajax_nopriv_wpqa_delete_comment_answer','wpqa_delete_comment_answer');
/* Ban and unban users */
function wpqa_ban_unban_user($user_id,$ban_type,$get_current_user_id) {
	$ban_name = "ban_group";
	if ($ban_type == "ban") {
		$if_user_id = get_user_by("id",$user_id);
		$default_group = wpqa_get_user_group($if_user_id);
		update_user_meta($user_id,$ban_name,$user_group);
		$ban_group = get_role($ban_name);
		if (!isset($ban_group)) {
			add_role($ban_name,esc_html__("Ban group","wpqa"),array('read' => false));
		}
		wp_update_user(array('ID' => $user_id,'role' => $ban_name));
		wpqa_notifications_activities($get_current_user_id,$user_id,"","","","ban_user","activities");
	}else {
		$ban_user = get_user_meta($user_id,$ban_name,true);
		$default_group = wpqa_options("default_group");
		$default_group = ($default_group != ""?$default_group:"subscriber");
		$ban_user = ($ban_user != ""?$ban_user:$default_group);
		wp_update_user(array('ID' => $user_id,'role' => $ban_user));
		wpqa_notifications_activities($get_current_user_id,$user_id,"","","","unban_user","activities");
	}
}?>