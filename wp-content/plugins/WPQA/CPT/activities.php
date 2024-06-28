<?php

/* @author    2codeThemes
*  @package   WPQA/CPT
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Activities post type */
function wpqa_activity_post_types_init() {
	$active_activity_log = wpqa_options("active_activity_log");
	if ($active_activity_log == "on") {
		register_post_type( 'activity',
			array(
				'label' => esc_html__('Activities','wpqa'),
				'labels' => array(
					'name'               => esc_html__('Activities','wpqa'),
					'singular_name'      => esc_html__('Activities','wpqa'),
					'menu_name'          => esc_html__('Activities','wpqa'),
					'name_admin_bar'     => esc_html__('Activities','wpqa'),
					'edit_item'          => esc_html__('Edit Activity','wpqa'),
					'all_items'          => esc_html__('All Activities','wpqa'),
					'search_items'       => esc_html__('Search Activities','wpqa'),
					'parent_item_colon'  => esc_html__('Parent Activity:','wpqa'),
					'not_found'          => esc_html__('No Activities Found.','wpqa'),
					'not_found_in_trash' => esc_html__('No Activities Found in Trash.','wpqa'),
				),
				'description'         => '',
				'public'              => false,
				'show_ui'             => true,
				'capability_type'     => 'post',
				'capabilities'        => array('create_posts' => 'do_not_allow'),
				'map_meta_cap'        => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => false,
				'hierarchical'        => false,
				'query_var'           => false,
				'show_in_rest'        => false,
				'has_archive'         => false,
				'menu_position'       => 5,
				'menu_icon'           => "dashicons-book-alt",
				'supports'            => array('title','editor'),
			)
		);
	}
}
add_action( 'wpqa_init', 'wpqa_activity_post_types_init', 2 );
/* Admin columns for post types */
function wpqa_activity_columns($old_columns) {
	$columns = array();
	$columns["cb"]        = "<input type=\"checkbox\">";
	$columns["content_a"] = esc_html__("Activity","wpqa");
	$columns["author_a"]  = esc_html__("Author","wpqa");
	$columns["date_a"]    = esc_html__("Date","wpqa");
	return $columns;
}
add_filter('manage_edit-activity_columns','wpqa_activity_columns');
function wpqa_activity_custom_columns($column) {
	global $post;
	switch ( $column ) {
		case 'content_a' :
			$activity_result = wpqa_notification_activity_result($post,"activity","admin");
			echo wpqa_show_activities($activity_result,"","");
			if (!empty($activity_result["post_id"])) {
				$get_post = get_post($activity_result["post_id"]);
				$get_the_permalink = get_the_permalink($activity_result["post_id"]);
				$get_post_status = (isset($get_post->post_status)?$get_post->post_status:"");
				$get_post_type = (isset($get_post->post_type)?$get_post->post_type:"");
			}
			if (!empty($activity_result["comment_id"])) {
				$get_comment = get_comment($activity_result["comment_id"]);
				if (!empty($get_comment)) {
					echo '<a class="tooltip_s" data-title="'.(isset($get_post_type) && ($get_post_type == wpqa_questions_type || $get_post_type == wpqa_asked_questions_type)?esc_html__("View answer","wpqa"):esc_html__("View comment","wpqa")).'" href="'.admin_url('edit.php?post_type=activity&author='.$post->post_author).'"><i class="dashicons dashicons-admin-comments"></i></a>';
				}
			}else if (!empty($activity_result["post_id"])) {
				if ($get_post_status != "trash" && !empty($get_the_permalink)) {
					if (isset($get_post_type) && ($get_post_type == wpqa_questions_type || $get_post_type == wpqa_asked_questions_type)) {
						$view_post = esc_html__("View question","wpqa");
					}else if (isset($get_post_type) && $get_post_type == "posts") {
						$view_post = esc_html__("View group post","wpqa");
					}else {
						$view_post = esc_html__("View post","wpqa");
					}
					echo '<a class="tooltip_s" data-title="'.$view_post.'" href="'.admin_url('edit.php?post_type=activity&author='.$post->post_author).'"><i class="dashicons dashicons-editor-help"></i></a>';
				}
			}
		break;
		case 'author_a' :
			$user_name = get_the_author_meta('display_name',$post->post_author);
			if ($user_name != "") {
				echo '<a target="_blank" href="'.wpqa_profile_url((int)$post->post_author).'"><strong>'.$user_name.'</strong></a><a class="tooltip_s" data-title="'.esc_html__("View activities","wpqa").'" href="'.admin_url('edit.php?post_type=activity&author='.$post->post_author).'"><i class="dashicons dashicons-book-alt"></i></a>';
			}else {
				esc_html_e("Deleted user","wpqa");
			}
		break;
		case 'date_a' :
			$date_format = wpqa_options("date_format");
			$date_format = ($date_format?$date_format:get_option("date_format"));
			$human_time_diff = human_time_diff(get_the_time('U'), current_time('timestamp'));
			echo ($human_time_diff." ".esc_html__("ago","wpqa")." - ".esc_html(get_the_time($date_format)));
		break;
	}
}
add_action('manage_activity_posts_custom_column','wpqa_activity_custom_columns',2);
function wpqa_activity_primary_column($default,$screen) {
	if ('edit-activity' === $screen) {
		$default = 'content_a';
	}
	return $default;
}
add_filter('list_table_primary_column','wpqa_activity_primary_column',10,2);
add_filter('manage_edit-activity_sortable_columns','wpqa_activity_sortable_columns');
function wpqa_activity_sortable_columns($defaults) {
	$defaults['date_a'] = 'date';
	return $defaults;
}
/* Activity details */
add_filter('bulk_actions-edit-activity','wpqa_bulk_actions_activity');
function wpqa_bulk_actions_activity($actions) {
	unset($actions['edit']);
	return $actions;
}
add_filter('bulk_post_updated_messages','wpqa_bulk_updated_messages_activity',1,2);
function wpqa_bulk_updated_messages_activity($bulk_messages,$bulk_counts) {
	if (get_current_screen()->post_type == "activity") {
		$bulk_messages['post'] = array(
			'deleted' => _n('%s activity permanently deleted.','%s activities permanently deleted.',$bulk_counts['deleted'],'wpqa'),
			'trashed' => _n('%s activity trashed.','%s activities trashed.',$bulk_counts['trashed'],'wpqa'),
		);
	}
	return $bulk_messages;
}
add_filter('post_row_actions','wpqa_row_actions_activity',1,2);
function wpqa_row_actions_activity($actions,$post) {
	if ($post->post_type == "activity") {
		unset($actions['trash']);
		unset($actions['view']);
		unset($actions['edit']);
		$actions['inline hide-if-no-js'] = "";
	}
	return $actions;
}
function wpqa_activity_filter() {
	global $post_type;
	if ($post_type == 'activity') {
		$from = (isset($_GET['date-from']) && $_GET['date-from'])?$_GET['date-from'] :'';
		$to = (isset($_GET['date-to']) && $_GET['date-to'])?$_GET['date-to']:'';
		$data_js = " data-js='".json_encode(array("changeMonth" => true,"changeYear" => true,"yearRange" => "2018:+00","dateFormat" => "yy-mm-dd"))."'";

		echo '<span class="site-form-date"><input class="site-date" type="text" name="date-from" placeholder="'.esc_html__("Date From","wpqa").'" value="'.esc_attr($from).'" '.$data_js.'></span>
		<span class="site-form-date"><input class="site-date" type="text" name="date-to" placeholder="'.esc_html__("Date To","wpqa").'" value="'.esc_attr($to).'" '.$data_js.'></span>';
	}
}
add_action('restrict_manage_posts','wpqa_activity_filter');
function wpqa_activity_posts_query($query) {
	global $post_type,$pagenow;
	if ($pagenow == 'edit.php' && $post_type == 'activity') {
		if (!empty($_GET['date-from']) && !empty($_GET['date-to'])) {
			$query->query_vars['date_query'][] = array(
				'after'     => sanitize_text_field($_GET['date-from']),
				'before'    => sanitize_text_field($_GET['date-to']),
				'inclusive' => true,
				'column'    => 'post_date'
			);
		}
		if (!empty($_GET['date-from']) && empty($_GET['date-to'])) {
			$today = sanitize_text_field($_GET['date-from']);
			$today = explode("-",$today);
			$query->query_vars['date_query'] = array(
				'year'  => $today[0],
				'month' => $today[1],
				'day'   => $today[2],
			);
		}
		if (empty($_GET['date-from']) && !empty($_GET['date-to'])) {
			$today = sanitize_text_field($_GET['date-to']);
			$today = explode("-",$today);
			$query->query_vars['date_query'] = array(
				'year'  => $today[0],
				'month' => $today[1],
				'day'   => $today[2],
			);
		}
		$orderby = $query->get('orderby');
		if ($orderby == 'date_a') {
			$query->query_vars('orderby','date');
		}
	}
}
add_action('pre_get_posts','wpqa_activity_posts_query');
function wpqa_months_dropdown_activity($return,$post_type) {
	if ($post_type == "activity") {
		$return = true;
	}
	return $return;
}
add_filter("disable_months_dropdown","wpqa_months_dropdown_activity",1,2);
/* Remove filter */
function wpqa_manage_activity_tablenav($which) {
	if ($which == "top") {
		global $post_type,$pagenow;
		if ($pagenow == 'edit.php' && $post_type == 'activity') {
			$date_from = (isset($_GET['date-from'])?esc_html($_GET['date-from']):'');
			$date_to = (isset($_GET['date-to'])?esc_html($_GET['date-to']):'');
			if ($date_from != "" || $date_to != "") {
				echo '<a class="button" href="'.admin_url('edit.php?post_type=activity').'">'.esc_html__("Remove filters","wpqa").'</a>';
			}
		}
	}
}
add_filter("manage_posts_extra_tablenav","wpqa_manage_activity_tablenav");
/* Show activities */
if (!function_exists('wpqa_show_activities')) :
	function wpqa_show_activities($activity_array,$show_date = "") {
		$output = "";
		if ($show_date == "on") {
			$class = (isset($activity_array["text"])?preg_replace('/[^a-zA-Z0-9._\-]/','',strtolower($activity_array["text"])):"");
			$output .= "<li class='notifications__item d-flex".(isset($activity_array["user_id"])?" ".wpqa_get_gender_class($activity_array["user_id"],(isset($activity_array["post_id"])?$activity_array["post_id"]:0),(isset($activity_array["comment_id"])?$activity_array["comment_id"]:0)):'').($class != ""?" notifications__".$class:"")."'>";
		}
		
		$result_icon = apply_filters("wpqa_activities_icon",false,$activity_array["text"]);
		if ($result_icon != "") {
			$output .= "<i class='".$result_icon."'></i>";
		}else if ($activity_array["text"] == "approved_category") {
			$output .= "<i class='icon-folder'></i>";
		}else if ($activity_array["text"] == "question_vote_up" || $activity_array["text"] == "answer_vote_up") {
			$output .= "<i class='icon-up-dir'></i>";
		}else if ($activity_array["text"] == "question_vote_down" || $activity_array["text"] == "answer_vote_down") {
			$output .= "<i class='icon-down-dir'></i>";
		}else if ($activity_array["text"] == "select_best_answer" || $activity_array["text"] == "cancel_best_answer" || $activity_array["text"] == "add_answer" || $activity_array["text"] == "add_comment" || $activity_array["text"] == "report_answer" || $activity_array["text"] == "approved_answer" || $activity_array["text"] == "approved_comment") {
			$output .= "<i class='icon-comment'></i>";
		}else if ($activity_array["text"] == "question_reaction_like" || $activity_array["text"] == "answer_reaction_like" || $activity_array["text"] == "posts_reaction_like" || $activity_array["text"] == "comment_reaction_like" || $activity_array["text"] == "question_remove_reaction" || $activity_array["text"] == "answer_remove_reaction" || $activity_array["text"] == "posts_remove_reaction" || $activity_array["text"] == "comment_remove_reaction") {
			$output .= "<i class='icon-thumbsup'></i>";
		}else if ($activity_array["text"] == "question_reaction_love" || $activity_array["text"] == "answer_reaction_love" || $activity_array["text"] == "posts_reaction_love" || $activity_array["text"] == "comment_reaction_love") {
			$output .= "<i class='fa-solid fa-heart'></i>";
		}else if ($activity_array["text"] == "question_reaction_hug" || $activity_array["text"] == "answer_reaction_hug" || $activity_array["text"] == "posts_reaction_hug" || $activity_array["text"] == "comment_reaction_hug") {
			$output .= "<i class='fa-solid fa-face-kiss-wink-heart'></i>";
		}else if ($activity_array["text"] == "question_reaction_haha" || $activity_array["text"] == "answer_reaction_haha" || $activity_array["text"] == "posts_reaction_haha" || $activity_array["text"] == "comment_reaction_haha") {
			$output .= "<i class='fa-solid fa-face-laugh-squint'></i>";
		}else if ($activity_array["text"] == "question_reaction_wow" || $activity_array["text"] == "answer_reaction_wow" || $activity_array["text"] == "posts_reaction_wow" || $activity_array["text"] == "comment_reaction_wow") {
			$output .= "<i class='fa-solid fa-face-surprise'></i>";
		}else if ($activity_array["text"] == "question_reaction_sad" || $activity_array["text"] == "answer_reaction_sad" || $activity_array["text"] == "posts_reaction_sad" || $activity_array["text"] == "comment_reaction_sad") {
			$output .= "<i class='fa-solid fa-face-frown-open'></i>";
		}else if ($activity_array["text"] == "question_reaction_angry" || $activity_array["text"] == "answer_reaction_angry" || $activity_array["text"] == "posts_reaction_angry" || $activity_array["text"] == "comment_reaction_angry") {
			$output .= "<i class='fa-solid fa-face-tired'></i>";
		}else if (!empty($activity_array["post_id"])) {
			$output .= "<i class='icon-sound'></i>";
		}else if (!empty($activity_array["comment_id"])) {
			$output .= "<i class='icon-comment'></i>";
		}else if ($activity_array["text"] == "add_message") {
			$output .= "<i class='icon-mail'></i>";
		}else if ((!empty($activity_array["another_user_id"]) || !empty($activity_array["username"])) && $activity_array["text"] != "admin_add_points" && $activity_array["text"] != "admin_remove_points") {
			$output .= "<i class='icon-user'></i>";
		}else if ($activity_array["text"] == "gift_site" || $activity_array["text"] == "admin_add_points") {
			$output .= "<i class='icon-bucket'></i>";
		}else if ($activity_array["text"] == "admin_remove_points") {
			$output .= "<i class='icon-star-empty'></i>";
		}else if ($activity_array["text"] == "delete_inbox_message" || $activity_array["text"] == "delete_send_message" || $activity_array["text"] == "action_comment" || $activity_array["text"] == "action_post" || $activity_array["text"] == "delete_reason" || $activity_array["text"] == "delete_question" || $activity_array["text"] == "delete_post" || $activity_array["text"] == "delete_answer" || $activity_array["text"] == "delete_comment" || $activity_array["text"] == "delete_group" || $activity_array["text"] == "delete_posts") {
			$output .= "<i class='icon-cancel'></i>";
		}else if ($activity_array["text"] == "posts_like") {
			$output .= "<i class='icon-heart'></i>";
		}else if ($activity_array["text"] == "posts_unlike") {
			$output .= "<i class='icon-heart-empty'></i>";
		}else if ($activity_array["text"] == "accept_invite") {
			$output .= "<i class='icon-check'></i>";
		}else if ($activity_array["text"] == "decline_invite") {
			$output .= "<i class='icon-cancel'></i>";
		}else if ($activity_array["text"] == "add_group" || $activity_array["text"] == "add_posts" || $activity_array["text"] == "approved_group" || $activity_array["text"] == "approved_posts") {
			$output .= "<i class='icon-network'></i>";
		}else {
			$output .= "<i class='icon-check'></i>";
		}
				
		$output .= "<div class='notification__body'>";
		if (!empty($activity_array["another_user_id"])) {
			$wpqa_profile_url = wpqa_profile_url($activity_array["another_user_id"]);
			$display_name = get_the_author_meta('display_name',$activity_array["another_user_id"]);
		}
		
		if ((!empty($activity_array["another_user_id"]) || !empty($activity_array["username"])) && $activity_array["text"] != "add_message" && $activity_array["text"] != "admin_add_points" && $activity_array["text"] != "admin_remove_points" && $activity_array["text"] != "user_follow" && $activity_array["text"] != "ban_user" && $activity_array["text"] != "unban_user" && $activity_array["text"] != "block_user" && $activity_array["text"] != "unblock_user" && $activity_array["text"] != "user_unfollow" && $activity_array["text"] != "report_user") {
			if (isset($display_name) && $display_name != "") {
				if (!empty($activity_array["another_user_id"])) {
					$output .= '<a class="author__name" href="'.esc_url($wpqa_profile_url).'">'.esc_html($display_name).'</a>';
				}
				if (!empty($activity_array["username"])) {
					$output .= esc_html($activity_array["username"])." ";
				}
				$output .= esc_html__("has","wpqa")." ";
			}else {
				$output .= esc_html__("Deleted user","wpqa")." - ";
			}
		}
		
		if (!empty($activity_array["post_id"])) {
			if ($activity_array["text"] == "add_group" || $activity_array["text"] == "delete_group" || $activity_array["text"] == "delete_posts" || $activity_array["text"] == "posts_like" || $activity_array["text"] == "posts_unlike" || $activity_array["text"] == "accept_invite" || $activity_array["text"] == "decline_invite" || $activity_array["text"] == "add_posts" || $activity_array["text"] == "approved_group" || $activity_array["text"] == "approved_posts" || $activity_array["text"] == "posts_reaction_like" || $activity_array["text"] == "posts_reaction_haha" || $activity_array["text"] == "posts_reaction_hug" || $activity_array["text"] == "posts_reaction_sad" || $activity_array["text"] == "posts_reaction_wow" || $activity_array["text"] == "posts_reaction_angry" || $activity_array["text"] == "posts_reaction_love" || $activity_array["text"] == "posts_remove_reaction") {
				$get_the_permalink = wpqa_custom_permalink($activity_array["post_id"],"view_group_posts","view_group_post");
			}else {
				$get_the_permalink = get_the_permalink($activity_array["post_id"]);
			}
			$get_post = get_post($activity_array["post_id"]);
			$get_post_status = (isset($get_post->post_status)?$get_post->post_status:"");
		}
		if (!empty($activity_array["comment_id"])) {
			$get_comment = get_comment($activity_array["comment_id"]);
			if (($activity_array["text"] == "add_comment" || $activity_array["text"] == "comment_reaction_like" || $activity_array["text"] == "comment_reaction_haha" || $activity_array["text"] == "comment_reaction_hug" || $activity_array["text"] == "comment_reaction_sad" || $activity_array["text"] == "comment_reaction_wow" || $activity_array["text"] == "comment_reaction_angry" || $activity_array["text"] == "comment_reaction_love" || $activity_array["text"] == "comment_remove_reaction") && isset($get_post->post_type) && $get_post->post_type == "posts") {
				$get_the_permalink = wpqa_custom_permalink($activity_array["post_id"],"view_group_posts","view_group_post");
			}
		}
		if (!empty($activity_array["post_id"]) && !empty($activity_array["comment_id"]) && $get_post_status != "trash" && isset($get_comment) && $get_comment->comment_approved != "spam" && $get_comment->comment_approved != "trash") {
			$output .= '<a class="notification__question notification__question-dark" href="'.esc_url($get_the_permalink.(isset($activity_array["comment_id"])?"#comment-".$activity_array["comment_id"]:"")).'">';
		}
		if (!empty($activity_array["post_id"]) && empty($activity_array["comment_id"]) && $get_post_status != "trash" && isset($get_the_permalink) && $get_the_permalink != "") {
			$output .= '<a class="notification__question notification__question-dark" href="'.esc_url($get_the_permalink).'">';
		}
			
			$result_text = apply_filters("wpqa_activities_text",false,$activity_array["text"]);
			if ($result_text != "") {
				$output .= $result_text;
			}else if ($activity_array["text"] == "poll_question") {
				$output .= esc_html__("Poll at question","wpqa");
			}else if ($activity_array["text"] == "question_vote_up") {
				$output .= esc_html__("Voted up question.","wpqa");
			}else if ($activity_array["text"] == "question_vote_down") {
				$output .= esc_html__("Voted down question.","wpqa");
			}else if ($activity_array["text"] == "answer_vote_up") {
				$output .= esc_html__("Voted up answer.","wpqa");
			}else if ($activity_array["text"] == "answer_vote_down") {
				$output .= esc_html__("Voted down answer.","wpqa");
			}else if ($activity_array["text"] == "user_follow") {
				$output .= esc_html__("You have followed","wpqa");
			}else if ($activity_array["text"] == "ban_user") {
				$output .= esc_html__("You have banned user","wpqa");
			}else if ($activity_array["text"] == "unban_user") {
				$output .= esc_html__("You have unbanned user","wpqa");
			}else if ($activity_array["text"] == "block_user") {
				$output .= esc_html__("You have blocked user","wpqa");
			}else if ($activity_array["text"] == "unblock_user") {
				$output .= esc_html__("You have unblocked user","wpqa");
			}else if ($activity_array["text"] == "user_unfollow") {
				$output .= esc_html__("You have unfollowed","wpqa");
			}else if ($activity_array["text"] == "bump_question") {
				$output .= esc_html__("You have bumped your question.","wpqa");
			}else if ($activity_array["text"] == "report_question") {
				$output .= esc_html__("You have reported a question.","wpqa");
			}else if ($activity_array["text"] == "report_answer") {
				$output .= esc_html__("You have reported an answer.","wpqa");
			}else if ($activity_array["text"] == "report_user") {
				$output .= esc_html__("You have reported a user.","wpqa");
			}else if ($activity_array["text"] == "select_best_answer") {
				$output .= esc_html__("You have chosen the best answer.","wpqa");
			}else if ($activity_array["text"] == "cancel_best_answer") {
				$output .= esc_html__("You have canceled the best answer.","wpqa");
			}else if ($activity_array["text"] == "closed_question") {
				$output .= esc_html__("You have closed the question.","wpqa");
			}else if ($activity_array["text"] == "opend_question") {
				$output .= esc_html__("You have opend the question.","wpqa");
			}else if ($activity_array["text"] == "follow_question") {
				$output .= esc_html__("You have followed the question.","wpqa");
			}else if ($activity_array["text"] == "unfollow_question") {
				$output .= esc_html__("You have unfollowed the question.","wpqa");
			}else if ($activity_array["text"] == "question_favorites") {
				$output .= esc_html__("You have added a question at favorites.","wpqa");
			}else if ($activity_array["text"] == "question_remove_favorites") {
				$output .= esc_html__("You have removed a question from favorites.","wpqa");
			}else if ($activity_array["text"] == "add_answer") {
				$output .= esc_html__("You have added an answer.","wpqa");
			}else if ($activity_array["text"] == "add_comment") {
				$output .= esc_html__("You have added a comment.","wpqa");
			}else if ($activity_array["text"] == "approved_answer") {
				$output .= esc_html__("Your answer is pending for review.","wpqa");
			}else if ($activity_array["text"] == "approved_comment") {
				$output .= esc_html__("Your comment is pending for review.","wpqa");
			}else if ($activity_array["text"] == "add_question") {
				$output .= esc_html__("Added a new question.","wpqa");
			}else if ($activity_array["text"] == "add_post") {
				$output .= esc_html__("Add a new post.","wpqa");
			}else if ($activity_array["text"] == "approved_question") {
				$output .= esc_html__("Your question is pending for review.","wpqa");
			}else if ($activity_array["text"] == "approved_message") {
				$output .= esc_html__("Your message is pending for review.","wpqa");
			}else if ($activity_array["text"] == "approved_post") {
				$output .= esc_html__("Your post is pending for review.","wpqa");
			}else if ($activity_array["text"] == "approved_category") {
				$output .= esc_html__("Your category is pending for review.","wpqa");
			}else if ($activity_array["text"] == "delete_question") {
				$output .= esc_html__("You have deleted a question.","wpqa");
			}else if ($activity_array["text"] == "delete_post") {
				$output .= esc_html__("You have deleted a post.","wpqa");
			}else if ($activity_array["text"] == "delete_answer") {
				$output .= esc_html__("You have deleted an answer.","wpqa");
			}else if ($activity_array["text"] == "delete_comment") {
				$output .= esc_html__("You have deleted a comment.","wpqa");
			}else if ($activity_array["text"] == "add_message") {
				$output .= esc_html__("You have sent a message for","wpqa");
				if (!empty($activity_array["another_user_id"]) || !empty($activity_array["username"])) {
					if (isset($display_name) && $display_name != "") {
						if (!empty($activity_array["another_user_id"])) {
							$output .= ' <a class="author__name" href="'.esc_url($wpqa_profile_url).'">'.esc_html($display_name).'</a>.';
						}
						if (!empty($activity_array["username"])) {
							$output .= esc_html($activity_array["username"]).".";
						}
					}else {
						$output .= esc_html__("Delete user","wpqa").".";
					}
				}
			}else if ($activity_array["text"] == "delete_inbox_message") {
				$output .= esc_html__("You have deleted your inbox message","wpqa");
			}else if ($activity_array["text"] == "delete_send_message") {
				$output .= esc_html__("You have deleted your sent message","wpqa");
			}else if ($activity_array["text"] == "delete_group") {
				$output .= esc_html__("You have deleted a group.","wpqa");
			}else if ($activity_array["text"] == "delete_posts") {
				$output .= esc_html__("You have deleted a group post.","wpqa");
			}else if ($activity_array["text"] == "posts_like") {
				$output .= esc_html__("You have liked a group post.","wpqa");
			}else if ($activity_array["text"] == "posts_unlike") {
				$output .= esc_html__("You have unliked a group post.","wpqa");
			}else if ($activity_array["text"] == "accept_invite") {
				$output .= esc_html__("You have accepted the group invite.","wpqa");
			}else if ($activity_array["text"] == "decline_invite") {
				$output .= esc_html__("You have declined the group invite.","wpqa");
			}else if ($activity_array["text"] == "add_group") {
				$output .= esc_html__("Added a new group.","wpqa");
			}else if ($activity_array["text"] == "add_posts") {
				$output .= esc_html__("Added a new group post.","wpqa");
			}else if ($activity_array["text"] == "approved_group") {
				$output .= esc_html__("Your group is pending for review.","wpqa");
			}else if ($activity_array["text"] == "approved_posts") {
				$output .= esc_html__("Your group post is pending for review.","wpqa");
			}else if ($activity_array["text"] == "question_reaction_like" || $activity_array["text"] == "question_reaction_love" || $activity_array["text"] == "question_reaction_hug" || $activity_array["text"] == "question_reaction_haha" || $activity_array["text"] == "question_reaction_wow" || $activity_array["text"] == "question_reaction_sad" || $activity_array["text"] == "question_reaction_angry") {
				$output .= esc_html__("You reacted a question.","wpqa");
			}else if ($activity_array["text"] == "answer_reaction_like" || $activity_array["text"] == "answer_reaction_love" || $activity_array["text"] == "answer_reaction_hug" || $activity_array["text"] == "answer_reaction_haha" || $activity_array["text"] == "answer_reaction_wow" || $activity_array["text"] == "answer_reaction_sad" || $activity_array["text"] == "answer_reaction_angry") {
				$output .= esc_html__("You reacted an answer.","wpqa");
			}else if ($activity_array["text"] == "posts_reaction_like" || $activity_array["text"] == "posts_reaction_love" || $activity_array["text"] == "posts_reaction_hug" || $activity_array["text"] == "posts_reaction_haha" || $activity_array["text"] == "posts_reaction_wow" || $activity_array["text"] == "posts_reaction_sad" || $activity_array["text"] == "posts_reaction_angry") {
				$output .= esc_html__("You reacted a group post.","wpqa");
			}else if ($activity_array["text"] == "comment_reaction_like" || $activity_array["text"] == "comment_reaction_love" || $activity_array["text"] == "comment_reaction_hug" || $activity_array["text"] == "comment_reaction_haha" || $activity_array["text"] == "comment_reaction_wow" || $activity_array["text"] == "comment_reaction_sad" || $activity_array["text"] == "comment_reaction_angry") {
				$output .= esc_html__("You reacted a comment.","wpqa");
			}else if ($activity_array["text"] == "question_remove_reaction") {
				$output .= esc_html__("You removed the react from a question.","wpqa");
			}else if ($activity_array["text"] == "answer_remove_reaction") {
				$output .= esc_html__("You removed the react from an answer.","wpqa");
			}else if ($activity_array["text"] == "posts_remove_reaction") {
				$output .= esc_html__("You removed the react from a group post.","wpqa");
			}else if ($activity_array["text"] == "comment_remove_reaction") {
				$output .= esc_html__("You removed the react from a comment.","wpqa");
			}else {
				$output .= $activity_array["text"];
			}
		if ((!empty($activity_array["post_id"]) && !empty($activity_array["comment_id"]) && $get_post_status != "trash" && isset($get_comment) && $get_comment->comment_approved != "spam" && $get_comment->comment_approved != "trash") || (!empty($activity_array["post_id"]) && empty($activity_array["comment_id"]) && $get_post_status != "trash" && isset($get_the_permalink) && $get_the_permalink != "")) {
			$output .= '</a>';
		}
		if (is_super_admin($activity_array["user_id"]) && !empty($activity_array["post_id"]) && !empty($activity_array["comment_id"])) {
			if (isset($get_comment) && $get_comment->comment_approved == "spam") {
				$output .= " ".esc_html__('( Spam )','wpqa');
			}else if ($get_post_status == "trash" || (isset($get_comment) && $get_comment->comment_approved == "trash")) {
				$output .= " ".esc_html__('( Trashed )','wpqa');
			}else if (empty($get_comment)) {
				$output .= " ".esc_html__('( Deleted )','wpqa');
			}
		}
		if (is_super_admin($activity_array["user_id"]) && !empty($activity_array["post_id"]) && empty($activity_array["comment_id"])) {
			if ($get_post_status == "trash") {
				$output .= " ".esc_html__('( Trashed )','wpqa');
			}else if (empty($get_the_permalink)) {
				$output .= " ".esc_html__('( Deleted )','wpqa');
			}
		}
		if (!empty($activity_array["more_text"])) {
			$output .= " - ".esc_html($activity_array["more_text"]).".";
		}
		if (($activity_array["text"] == "ban_user" || $activity_array["text"] == "unban_user" || $activity_array["text"] == "block_user" || $activity_array["text"] == "unblock_user" || $activity_array["text"] == "user_follow" || $activity_array["text"] == "user_unfollow") && !empty($activity_array["another_user_id"])) {
			$output .= ' <a class="author__name" href="'.wpqa_profile_url($activity_array["another_user_id"]).'">'.get_the_author_meta('display_name',$activity_array["another_user_id"]).'</a>.';
		}
		
		if ($show_date == "on") {
			$output .= "<span class='notifications-date notification__date d-block mt-2'>".$activity_array["time"]."</span>
			</div></li>";
		}
		return $output;	
	}
endif;
/* Show activities li */
if (!function_exists('wpqa_get_activities')) :
	function wpqa_get_activities($user_id,$item_number,$more_button,$more_button_ul = false,$ul_class = '') {
		$output = '';
		if ($more_button_ul == false) {
			$output .= '<div>';
		}
		$output .= '<ul'.($ul_class != ""?' class="'.$ul_class.'"':'').'>';
		$args = array('author' => $user_id,'post_type' => 'activity','posts_per_page' => $item_number);
		$activities_query = new WP_Query( $args );
		if ($activities_query->have_posts()) {
			while ( $activities_query->have_posts() ) { $activities_query->the_post();
				$activity_post = $activities_query->post;
				$activity_result = wpqa_notification_activity_result($activity_post,"activity");
				$output .= wpqa_show_activities($activity_result,"on");
			}
			if ($more_button == "on" && $more_button_ul == true) {
				$output .= "<li class='notifications__item d-flex align-items-center justify-content-center notification__show-all'><a href='".esc_url(wpqa_activities_permalink())."'>".esc_html__("Show all activities.","wpqa")."</a></li>";
			}
			$output .= "</ul>";
			if ($more_button == "on" && $more_button_ul == false) {
				$output .= "<a href='".esc_url(wpqa_activities_permalink())."'>".esc_html__("Show all activities.","wpqa")."</a>";
			}
		}else {
			$output .= "<li class='notifications__item d-flex align-items-center justify-content-center'><div class='notification__body'>".esc_html__("There are no activities yet.","wpqa")."</div></li></ul>";
		}
		if ($more_button_ul == false) {
			$output .= '</div>';
		}
		wp_reset_postdata();
		return $output;
	}
endif;?>