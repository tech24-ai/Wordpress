<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Get cover name */
function wpqa_cover_name() {
	$cover = wpqa_options("user_meta_cover");
	$cover = apply_filters("wpqa_user_meta_cover",$cover);
	$cover = preg_replace('/[^a-z\-\_]/','',strtolower($cover));
	$cover = ($cover != ""?$cover:"your_cover");
	return $cover;
}
/* Get user cover url */
if (!function_exists('wpqa_get_cover_url')) :
	function wpqa_get_cover_url($your_cover,$size,$title) {
		$cover_num = false;
		if (isset($your_cover) && is_array($your_cover) && isset($your_cover["id"])) {
			$your_cover = $your_cover["id"];
		}

		if (isset($your_cover) && $your_cover != "" && is_numeric($your_cover) && $your_cover > 0) {
			$img_url = wp_get_attachment_image($your_cover);
			if ($img_url != "") {
				$cover_num = true;
			}
		}else {
			if ($your_cover != "") {
				$get_attachment_id = wpqa_get_attachment_id($your_cover);
				if (isset($get_attachment_id) && $get_attachment_id != "" && is_numeric($get_attachment_id) && $your_cover > 0) {
					$cover_num = true;
					$your_cover = $get_attachment_id;
				}
			}
		}
		
		if ($your_cover > 0 && $cover_num == true) {
			$cover = wpqa_get_aq_resize_img_url($size,$size,$your_cover);
		}else {
			if (is_array($your_cover) && isset($your_cover['url']) && $your_cover['url'] != "") {
				$cover = wpqa_get_aq_resize_url($your_cover,$size,$size);
			}
		}
		return (isset($cover) && $cover != ""?$cover:"");
	}
endif;
/* Get user cover image */
if (!function_exists('wpqa_get_user_cover_image')) :
	function wpqa_get_user_cover_image ($your_cover,$size,$user_name) {
		$cover = "<img class='cover-".$size." photo' alt='".esc_attr($user_name)."' title='".esc_attr($user_name)."' width='".$size."' height='".$size."' src='".wpqa_get_cover_url($your_cover,$size,$user_name)."'>";
		return $cover;
	}
endif;
/* Get user cover link */
if (!function_exists('wpqa_get_user_cover_link')) :
	function wpqa_get_user_cover_link ($args = array()) {
		$defaults = array(
			'user_id'   => '',
			'size'      => '',
			'user_name' => '',
			'user'      => '',
			'post'      => '',
			'comment'   => '',
		);
		
		$args = wp_parse_args($args,$defaults);
		
		$user_id   = $args['user_id'];
		$size      = $args['size'];
		$user_name = $args['user_name'];
		$user      = $args['user'];
		$post      = $args['post'];
		$comment   = $args['comment'];
		
		$user_name = ($user_name == "" && $user_id > 0?get_the_author_meta('display_name',$user_id):$user_name);
		
		if (!empty($comment)) {
			$user_name = ($user_id > 0?$user_name:$comment->comment_author);
		}

		$user_meta_cover = wpqa_cover_name();
		
		$your_cover = get_the_author_meta($user_meta_cover,$user_id);
		if ((($your_cover && !is_array($your_cover)) || (is_array($your_cover) && isset($your_cover["id"]) && $your_cover["id"] != 0)) && $user_id > 0) {
			$cover = wpqa_get_cover_url($your_cover,$size,$user_name);
		}else {
			$default_cover_active = wpqa_options("default_cover_active");
			if ($default_cover_active == "on") {
				$default_cover = wpqa_image_url_id(wpqa_options("default_cover"));
				$default_cover_females = wpqa_image_url_id(wpqa_options("default_cover_females"));
				if ($default_cover_females != "") {
					$gender = get_the_author_meta('gender',$user_id);
					$default_cover = ($gender == "Female" || $gender == 2?$default_cover_females:$default_cover);
				}
				
				if ($default_cover_active == "on" && $default_cover != "") {
					$cover = wpqa_get_aq_resize_url($default_cover,$size,$size);
				}
			}
		}
		if (isset($cover)) {
			return $cover;
		}
	}
endif;
/* Get user cover */
if (!function_exists('wpqa_get_user_cover')) :
	function wpqa_get_user_cover ($args = array()) {
		$defaults = array(
			'user_id'   => '',
			'size'      => '',
			'user_name' => '',
			'user'      => '',
			'post'      => '',
			'comment'   => '',
		);
		
		$args = wp_parse_args($args,$defaults);

		$size      = $args['size'];
		$user_name = $args['user_name'];

		$user_cover_link = wpqa_get_user_cover_link($args);
		
		$cover = ($user_cover_link != ""?"<img class='cover-".$size." photo' alt='".esc_attr($user_name)."' title='".esc_attr($user_name)."' width='".$size."' height='".$size."' src='".$user_cover_link."'>":"");
		return $cover;
	}
endif;
add_action("wpqa_cover_image","wpqa_cover_image");
function wpqa_cover_image() {
	$cover_image = wpqa_options("cover_image");
	if ($cover_image == "on" && wpqa_is_user_profile() && !wpqa_is_user_edit_profile() && !wpqa_is_user_password_profile() && !wpqa_is_user_privacy_profile() && !wpqa_is_user_withdrawals_profile() && !wpqa_is_user_financial_profile() && !wpqa_is_user_mails_profile() && !wpqa_is_user_delete_profile()) {
		$author_widget = wpqa_options("author_widget");
		if (wpqa_is_main_edit_profile_pages()) {
			$user_id = get_current_user_id();
		}else {
			$user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
		}
		if ($user_id > 0) {
			$display_name = get_the_author_meta("display_name",$user_id);
			$profile_credential = get_the_author_meta('profile_credential',$user_id);
			$privacy_credential = wpqa_check_user_privacy($user_id,"credential");
			$ask_question_to_users = wpqa_options("ask_question_to_users");
			$cover_fixed = wpqa_options("cover_fixed");
			$owner = wpqa_is_user_owner();
			$following_you = get_user_meta($user_id,"following_you",true);
			$following_you = (is_array($following_you) && !empty($following_you)?get_users(array('fields' => 'ID','include' => $following_you,'orderby' => 'registered')):array());
			$add_questions = (int)wpqa_count_posts_meta(wpqa_questions_type,$user_id);
			$active_points_category = wpqa_options("active_points_category");
			if ($active_points_category == "on") {
				$categories_user_points = get_user_meta($user_id,"categories_user_points",true);
				if (is_array($categories_user_points) && !empty($categories_user_points)) {
					foreach ($categories_user_points as $category) {
						$points_category_user[$category] = (int)get_user_meta($user_id,"points_category".$category,true);
					}
					arsort($points_category_user);
					$first_category = (is_array($points_category_user)?key($points_category_user):"");
					$first_points = reset($points_category_user);
				}
			}
			echo "<div class='wpqa-profile-cover wpqa-profile-cover-only ".($author_widget == "on"?"wpqa-profile-widget":"wpqa-profile-not-widget").($cover_fixed == "fixed"?" wpqa-cover-fixed container-boot":"").($privacy_credential == true && $profile_credential != ""?" cover-with-credential":"")."'>
				<div>
					<div class='wpqa-cover-background".($cover_fixed == "fixed"?" the-main-container":"")."'>
						<div class='cover-opacity'></div>
						<div class='wpqa-cover-inner".($cover_fixed == "fixed"?"":" the-main-container container-boot")."'>
							<div class='wpqa-cover-content'>";
								if ($author_widget != "on") {
									echo "<div class='post-section user-area user-advanced user-cover'>
										<div class='post-inner'>
											<div class='user-head-area'>
												".wpqa_get_avatar_link(array("user_id" => $user_id,"size" => (has_himer() || has_knowly()?130:84),"span" => "span","class" => "rounded-circle"))."
											</div>
											<div class='user-content'>
												<div class='user-inner'>
													<h4><a href='".esc_url(wpqa_profile_url($user_id))."'>".$display_name."</a>".wpqa_verified_user($user_id)."</h4>";
													if (isset($first_category)) {
														echo apply_filters("wpqa_cover_before_badge",false,$first_category);
													}
													echo wpqa_get_badge($user_id,"",(isset($first_points) && $first_points != ""?$first_points:""));
													if ($privacy_credential == true && $profile_credential != "") {
														echo "<span class='profile-credential'>".esc_html($profile_credential)."</span>";
													}
													do_action("wpqa_cover_after_profile_credential",$user_id);
												echo "</div>
											</div>
										</div>
									</div>
									<div class='wpqa-cover-right'>";
										$get_current_user_id = get_current_user_id();
										$is_super_admin      = is_super_admin($get_current_user_id);
										$user_super_admin    = is_super_admin($user_id);
										$active_moderators   = wpqa_options("active_moderators");
										if ($active_moderators == "on") {
											$moderator_categories = get_user_meta($get_current_user_id,prefix_author."moderator_categories",true);
											$moderator_categories = (is_array($moderator_categories) && !empty($moderator_categories)?$moderator_categories:array());
											$pending_posts = ($is_super_admin || (isset($moderator_categories) && is_array($moderator_categories) && !empty($moderator_categories))?true:false);
											$moderators_permissions = wpqa_user_moderator($get_current_user_id);
											$if_user_id = get_user_by("id",$user_id);
											$user_group = wpqa_get_user_group($if_user_id);
											$moderators_available = ($is_super_admin || ($user_group != "administrator" && $pending_posts == true && isset($moderators_permissions['ban']) && $moderators_permissions['ban'] == "ban")?true:false);
										}
										$block_users = wpqa_options("block_users");
										$report_users = wpqa_options("report_users");
										if ($owner || (!$user_super_admin && !$owner && (($report_users == "on" && $get_current_user_id > 0) || (isset($moderators_available) && $moderators_available == true) || ($block_users == "on" && $get_current_user_id > 0)))) {
											echo "<div class='question-list-details'>
												<i class='icon-dot-3'></i>
												<ul class='question-link-list'>";
													if ($owner) {
														echo "<li class='edit-profile-cover'><a href='".wpqa_edit_profile_permalink()."'><i class='icon-cog'></i>".esc_html__("Edit profile","wpqa")."</a></li>";
													}
													if (!$user_super_admin && $block_users == "on" && !$owner && $get_current_user_id > 0) {
														$get_block_users = get_user_meta($get_current_user_id,"wpqa_block_users",true);
														$if_block = (is_array($get_block_users) && !empty($get_block_users) && in_array($user_id,$get_block_users)?true:false);
														echo "<li class='block-unblock-user'><span class='small_loader loader_2'></span><a class='".($if_block?"unblock-user":"block-user")."' data-nonce='".wp_create_nonce("block_nonce")."' href='#' data-id='".$user_id."'><i class='".($if_block?"icon-back":"icon-block")."'></i><span>".($if_block?esc_html__("Unblock user","wpqa"):esc_html__("Block user","wpqa"))."</span></a></li>";
													}
													if (!$user_super_admin && !$owner && (isset($moderators_available) && $moderators_available == true)) {
														$if_ban = (isset($if_user_id->caps["ban_group"]) && $if_user_id->caps["ban_group"] == 1?true:false);
														echo "<li class='ban-unban-user'><span class='small_loader loader_2'></span><a class='".($if_ban?"unban-user":"ban-user")."' data-nonce='".wp_create_nonce("ban_nonce")."' href='#' data-id='".$user_id."'><i class='".($if_ban?"icon-back":"icon-cancel-circled")."'></i><span>".($if_ban?esc_html__("Unban user","wpqa"):esc_html__("Ban user","wpqa"))."</span></a></li>";
													}
													if (!$user_super_admin && $report_users == "on" && !$owner && $get_current_user_id > 0) {
														echo "<li class='report_activated report-user-li'><span class='small_loader loader_2'></span><a class='report_user' href='".$user_id."'><i class='icon-flag'></i><span>".esc_html__("Report user","wpqa")."</span></a></li>";
													}
												echo "</ul>
											</div>";
										}
										$user_follwers = (int)(is_array($following_you)?count($following_you):0);
										echo wpqa_following($user_id,"style_4",$owner,"","","btn btn__sm btn__success","btn btn__sm btn__danger").
										($ask_question_to_users == "on" && !$owner?"<div class='ask-question'><a href='".esc_url(wpqa_add_question_permalink("user"))."' class='button-default ask-question-user btn btn__sm btn__secondary'>".esc_html__("Ask","wpqa")." ".$display_name."</a></div>":"").
										wpqa_message_button($user_id,"text",$owner,"","btn__sm")."
										<div class='empty-cover-div'></div>";
										$author_visits = wpqa_options("author_visits");
										if ($author_visits == "on") {
											$author_stats = wpqa_get_post_stats(0,$user_id);
											echo "<div class='wpqa-cover-buttons wpqa-cover-visits'><i class='icon-eye'></i><span class='cover-count'>".wpqa_count_number($author_stats)." </span>"._n("Visit","Visits",$author_stats,"wpqa")."</div>";
										}
										echo "<div class='wpqa-cover-buttons wpqa-cover-followers'><i class='icon-users'></i><span class='cover-count follow-cover-count'>".wpqa_count_number($user_follwers)." </span>"._n("Follower","Followers",$user_follwers,"wpqa")."</div>
										<div><a class='wpqa-cover-buttons wpqa-cover-questions' href='".wpqa_get_profile_permalink($user_id,"questions")."'><i class='icon-book-open'></i><span class='cover-count'>".wpqa_count_number($add_questions)."</span> "._n("Question","Questions",$add_questions,"wpqa")."</a></div>
										".apply_filters("wpqa_profile_cover_buttons",false,$user_id,$owner)."
									</div>";
								}
							echo "</div>
							<div class='clearfix'></div>
						</div>
					</div>
				</div>
			</div><!-- End wpqa-profile-cover -->";
		}
	}
}?>