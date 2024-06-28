<?php

/* @author    2codeThemes
*  @package   WPQA/shortcodes
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* wpqa_edit_comment_attr */
function wpqa_edit_comment_attr() {
	$comment_id = (int)get_query_var(apply_filters('wpqa_edit_comments','edit_comment'));
	$get_comment = get_comment($comment_id);
	$user_id = get_current_user_id();
	$get_post = array();
	if (isset($comment_id) && $comment_id != 0 && is_object($get_comment)) {
		$post_id = $get_comment->comment_post_ID;
		$comment_user_id = $get_comment->user_id;
		$get_post = get_post($post_id);
		$can_edit_comment = wpqa_options("can_edit_comment");
		$can_edit_comment_after = wpqa_options("can_edit_comment_after");
		$can_edit_comment_after = (int)(isset($can_edit_comment_after) && $can_edit_comment_after > 0?$can_edit_comment_after:0);
		if (version_compare(phpversion(), '5.3.0', '>')) {
			$time_now = strtotime(current_time( 'mysql' ),date_create_from_format('Y-m-d H:i',current_time( 'mysql' )));
		}else {
			list($year, $month, $day, $hour, $minute, $second) = sscanf(current_time( 'mysql' ), '%04d-%02d-%02d %02d:%02d:%02d');
			$datetime = new DateTime("$year-$month-$day $hour:$minute:$second");
			$time_now = strtotime($datetime->format('r'));
		}
		$time_edit_comment = strtotime('+'.$can_edit_comment_after.' hour',strtotime($get_comment->comment_date));
		$time_end = ($time_now-$time_edit_comment)/60/60;
	}
	$out = '';
	if (isset($comment_id) && $comment_id != 0 && $get_post) {
		$is_super_admin = is_super_admin($user_id);
		$edit_delete_posts_comments = wpqa_options("edit_delete_posts_comments");
		$activate_edit = wpqa_group_edit_comments($post_id,$is_super_admin,$can_edit_comment,$comment_user_id,$user_id,$edit_delete_posts_comments);
		if ($activate_edit == true || $is_super_admin || ($can_edit_comment == "on" && $get_comment->comment_approved == 1 && $comment_user_id == $user_id && $comment_user_id > 0 && ($can_edit_comment_after == 0 || $time_end <= $can_edit_comment_after))) {
			$attachment_answer = wpqa_options("attachment_answer");
			$featured_image_answer = wpqa_options("featured_image_answer");
			$featured_image_group_post_comments = wpqa_options("featured_image_group_post_comments");
			$post_type = $get_post->post_type;
			$out .= '<form class="wpqa_form edit-comment-form" method="post" enctype="multipart/form-data">'.apply_filters('wpqa_edit_comment','edit_comment').
				apply_filters('wpqa_edit_comment_fields',false,"edit",$comment_id,$get_post).'
				<div class="form-inputs clearfix">
					<p>
						<label>'.esc_html_x("Comment","Edit comment page","wpqa").'<span class="required">*</span></label>';
						$comment_editor = "comment_editor";
						if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
							$comment_editor = "answer_editor";
						}else if ($post_type == "posts") {
							$comment_editor = "editor_group_post_comments";
						}
						$comment_editor = wpqa_options($comment_editor);
						$comment_value = (isset($_POST['comment'])?wpqa_esc_textarea($_POST['comment']):$get_comment->comment_content);
						if ($comment_editor == "on") {
							$settings = array("textarea_name" => "comment","media_buttons" => true,"textarea_rows" => 10);
							$settings = apply_filters('wpqa_edit_comment_editor_setting',$settings);
							ob_start();
							$rand = rand(1,1000);
							wp_editor($comment_value,"comment-".$rand,$settings);
							$comment_contents = ob_get_clean();
						}else {
							$comment_contents = '<textarea class="form-control" cols="58" rows="8" name="comment" aria-required="true">'.$comment_value.'</textarea>';
						}
						$out .= $comment_contents;
					$out .= '</p>
				</div>
				
				<p class="form-submit mb-0">
					<input type="hidden" name="form_type" value="edit_comment">
					<input type="hidden" name="comment_id" value="'.$comment_id.'">
					<input type="hidden" name="wpqa_comment_nonce" value="'.wp_create_nonce("wpqa_comment_nonce").'">
					'.(wpqa_input_button() == "button"?'<button type="submit" class="button-default edit-comment button-hide-click btn btn__primary btn__block btn__large__height">'.esc_attr__("Edit Comment","wpqa").'</button>':'<input type="submit" value="'.esc_attr__("Edit Comment","wpqa").'" class="button-default edit-comment button-hide-click">').'
					<span class="load_span"><span class="loader_2"></span></span>
				</p>
			</form>';
		}else {
			$out .= '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("You are not allowed to edit this comment.","wpqa").'</p></div>';
		}
	}else {
		$out .= '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry, no comment has been selected or not found.","wpqa").'</p></div>';
	}
	
	return $out;
}?>