<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Get group cover */
add_action("wpqa_group_cover","wpqa_group_cover");
function wpqa_group_cover() {
	if (wpqa_is_edit_groups() || is_singular("group") || wpqa_is_group_requests() || wpqa_is_group_users() || wpqa_is_group_admins() || wpqa_is_blocked_users() || wpqa_is_posts_group() || wpqa_is_view_posts_group() || wpqa_is_edit_posts_group()) {
		$group_cover_activate = "on";
		if ($group_cover_activate == "on") {
			$group_id = wpqa_group_id();
			if ($group_id > 0) {
				$user_id = get_current_user_id();
				$is_super_admin = is_super_admin($user_id);
				$get_group = get_post($group_id);
				if (isset($get_group->ID)) {
					$blocked_users_array = get_post_meta($group_id,"blocked_users_array",true);
					$group_moderators = get_post_meta($group_id,"group_moderators",true);
					$blocked_users_array = (is_array($blocked_users_array)?$blocked_users_array:array());
					if ($is_super_admin || ($user_id > 0 && is_array($group_moderators) && in_array($user_id,$group_moderators)) || ($user_id > 0 && $user_id == $get_group->post_author) || !in_array($user_id,$blocked_users_array)) {
						$get_permalink = get_permalink($group_id);
						$the_title = $get_group->post_title;
						$group_privacy = get_post_meta($group_id,"group_privacy",true);
						$group_image = get_post_meta($group_id,"group_image",true);
						$group_users = (int)get_post_meta($group_id,"group_users",true);
						$group_posts = (int)get_post_meta($group_id,"group_posts",true);
						$group_users_array = get_post_meta($group_id,"group_users_array",true);
						$group_requests_array = get_post_meta($group_id,"group_requests_array",true);
						$group_invitations = get_post_meta($group_id,"group_invitations",true);
						$group_invitations = (is_array($group_invitations) && !empty($group_invitations)?$group_invitations:array());
						$group_cover = get_post_meta($group_id,"group_cover",true);
						if (($group_cover && !is_array($group_cover)) || (is_array($group_cover) && isset($group_cover["id"]) && $group_cover["id"] != 0)) {
							$group_cover_img = wpqa_get_cover_url($group_cover,"","");
						}
						if (wpqa_is_view_posts_group()) {
							$is_view_posts_group = true;
						}
						do_action("wpqa_action_before_group_cover",$group_id);
						echo '<div class="group_cover '.(isset($group_cover_img) && $group_cover_img != ""?"group_has_cover":"group_no_cover").'">
							<div class="cover-opacity"></div>
							<div class="the-main-container container-boot">
								<div class="group_cover_content">
									<div class="group_cover_content_first">
										<div class="group_small_image">';
										if ((is_array($group_image) && isset($group_image["id"])) || (!is_array($group_image) && $group_image != "")) {
											echo wpqa_get_aq_resize_img(100,100,"",(is_array($group_image) && isset($group_image["id"])?$group_image["id"]:$group_image),"no",$the_title,"srcset","rounded-circle");
										}
										echo '</div>
										<div class="name">
											<h1>'.(isset($is_view_posts_group)?"<a href='".$get_permalink."'>":"").$the_title.(isset($is_view_posts_group)?"</a>":"").'</h1></a>
											<small>'.($group_privacy == "public"?"<i class='icon-lock-open'></i>".esc_html__("Public group","wpqa"):"<i class='icon-lock'></i>".esc_html__("Private group","wpqa")).'</small>
										</div>
									</div>
									<div class="group_cover_content_second">
										<div class="wpqa-cover-right">';
											if ($is_super_admin || ($user_id > 0 && $user_id == $get_group->post_author)) {
												echo '<div><a href="'.esc_url_raw(add_query_arg(array("activate_delete" => true,"delete" => $group_id,"wpqa_delete_nonce" => wp_create_nonce("wpqa_delete_nonce")),$get_permalink)).'" class="button-default btn btn__sm btn__danger delete-group" data-id="'.$group_id.'">'.esc_html__("Delete","wpqa").'</a></div>';
											}else if (!$is_super_admin && $user_id > 0) {
												if (is_array($group_users_array) && in_array($user_id,$group_users_array)) {
													$join_leave_text = esc_html__("Leave","wpqa");
													$join_leave_class = "user_in_group btn__danger";
												}else {
													if (is_array($group_requests_array) && in_array($user_id,$group_requests_array)) {
														$join_leave_text = esc_html__("Cancel the request","wpqa");
														$join_leave_class = "cancel_request_group btn__danger";
													}else {
														if (is_array($group_invitations) && in_array($user_id,$group_invitations)) {
															$join_leave_text = esc_html__("Accept invite","wpqa");
															$join_leave_class = "accept_invite btn__success";
															$join_leave_text_2 = esc_html__("Decline invite","wpqa");
															$join_leave_class_2 = "decline_invite btn__danger";
														}else {
															$join_leave_text = esc_html__("Join","wpqa");
															if ($group_privacy == "public") {
																$join_leave_class = "user_out_group btn__primary";
															}else {
																$join_leave_class = "request_group btn__primary";
															}
														}
													}
												}
												echo '<div class="group_join">
													<div class="cover_loader wpqa_hide"><div class="small_loader loader_2"></div></div>
													<a href="#" class="button-default btn btn__sm hide_button_too '.$join_leave_class.'" data-nonce="'.wp_create_nonce("wpqa_group_nonce").'" data-id="'.$group_id.'">'.$join_leave_text.'</a>';
													if (isset($join_leave_class_2)) {
														echo '<a href="#" class="button-default btn btn__sm hide_button_too '.$join_leave_class_2.'" data-nonce="'.wp_create_nonce("wpqa_group_nonce").'" data-id="'.$group_id.'">'.$join_leave_text_2.'</a>';
													}
												echo '</div>';
											}
											$active_post_stats = wpqa_options("active_post_stats");
											$groups_visits = wpqa_options("groups_visits");
											if ($active_post_stats == "on" && $groups_visits == "on") {
												$post_stats = wpqa_get_post_stats($group_id);
												echo '<div class="wpqa-cover-buttons wpqa-cover-posts"><i class="icon-eye"></i><span class="cover-count">'.wpqa_count_number($post_stats).'</span>'._n("View","Views",$post_stats,"wpqa").'</div>';
											}
											echo '<div class="wpqa-cover-buttons wpqa-cover-users"><i class="icon-users"></i><span class="cover-count">'.wpqa_count_number($group_users).'</span>'._n("User","Users",$group_users,"wpqa").'</div>
											<div class="wpqa-cover-buttons wpqa-cover-posts"><i class="icon-book-open"></i><span class="cover-count">'.wpqa_count_number($group_posts).'</span>'._n("Post","Posts",$group_posts,"wpqa").'</div>
										</div>
									</div>
								</div>
							</div>
						</div>';
					}
				}
			}
		}
	}
}
/* Group tabs */
add_action("wpqa_group_tabs","wpqa_group_tabs",1,5);
function wpqa_group_tabs($group_id,$user_id,$is_super_admin,$post_author,$group_moderators) {
	$group_privacy = get_post_meta($group_id,"group_privacy",true);
	$view_users_group = wpqa_options("view_users_group");
	$edit_group = wpqa_options("group_edit");
	if (isset($view_users_group[$group_privacy]) && $view_users_group[$group_privacy] == $group_privacy) {
		$group_users_array = get_post_meta($group_id,"group_users_array",true);
	}
	if ($is_super_admin || (isset($view_users_group[$group_privacy]) && $view_users_group[$group_privacy] == $group_privacy && isset($group_users_array) && is_array($group_users_array) && !empty($group_users_array) && in_array($user_id,$group_users_array)) || ($edit_group == "on" && $post_author == $user_id && $user_id > 0) || ($user_id > 0 && is_array($group_moderators) && in_array($user_id,$group_moderators))) {
		$list_child = "li";
		echo '<div class="wrap-tabs"><div class="menu-tabs"><ul class="menu flex menu-tabs-desktop navbar-nav navbar-secondary">';
			do_action("wpqa_group_inner_tabs",$group_id,$list_child,$user_id,$is_super_admin,$post_author,$group_moderators,$group_privacy,$edit_group);
		echo '</ul></div></div>';
		$list_child = "option";
		echo '<div class="wpqa_hide mobile-tabs"><span class="styled-select"><select class="form-control home_categories">';
			do_action("wpqa_group_inner_tabs",$group_id,$list_child,$user_id,$is_super_admin,$post_author,$group_moderators,$group_privacy,$edit_group);
		echo '</select></span></div>';
	}
}
/* Group inner tabs */
add_action("wpqa_group_inner_tabs","wpqa_group_inner_tabs",1,8);
function wpqa_group_inner_tabs($group_id,$list_child,$user_id,$is_super_admin,$post_author,$group_moderators,$group_privacy,$edit_group) {
	$custom_permission = wpqa_options("custom_permission");
	if ($custom_permission == "on") {
		if (is_user_logged_in()) {
			$user_is_login = get_userdata($user_id);
			$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
			$edit_other_groups = (isset($roles["edit_other_groups"]) && $roles["edit_other_groups"] == 1?true:false);
		}
	}
	$custom_moderators_permissions = get_user_meta($user_id,prefix_author."custom_moderators_permissions",true);
	if ($custom_moderators_permissions == "on") {
		$wpqa_user_permissions = wpqa_user_permissions($user_id);
		$edit_other_groups = (isset($wpqa_user_permissions['edit_groups']) && $wpqa_user_permissions['edit_groups'] == "edit_groups"?true:false);
	}
	if ($is_super_admin || ($edit_group == "on" && $post_author == $user_id && $user_id > 0) || ($user_id > 0 && is_array($group_moderators) && in_array($user_id,$group_moderators))) {
		$moderators = true;
	}
	if (isset($moderators)) {
		$group_pages = array("group","edit","group_requests","pending_posts","group_users","group_admins","blocked_users");
		if ($group_privacy == "public") {
			$group_pages = array_diff($group_pages,array("group_requests"));
		}
		if (!$is_super_admin && $user_id > 0 && $user_id != $post_author) {
			$group_pages = array_diff($group_pages,array("group_admins"));
		}
	}else {
		$group_pages = array("group","group_users");
	}
	$group_pages = apply_filters("wpqa_group_pages",$group_pages,$group_id);
	if (isset($group_pages) && is_array($group_pages) && !empty($group_pages)) {
		foreach ($group_pages as $key) {
			do_action("wpqa_action_edit_group_pages",$group_pages,$key);
			if ($key == "group") {
				$last_url = get_permalink($group_id);
				$selected = (is_singular("group")?true:"");
			}else if ($key == "edit") {
				$last_url = wpqa_edit_permalink($group_id,"group");
				$selected = (wpqa_is_edit_groups()?true:"");
			}else if ($key == "group_requests") {
				$last_url = wpqa_custom_permalink($group_id,"group_requests","group_request");
				$selected = (wpqa_is_group_requests()?true:"");
			}else if ($key == "pending_posts") {
				$last_url = wpqa_custom_permalink($group_id,"group_posts","pending_post");
				$selected = (wpqa_is_posts_group()?true:"");
			}else if ($key == "group_users") {
				$last_url = wpqa_custom_permalink($group_id,"group_users","group_user");
				$selected = (wpqa_is_group_users()?true:"");
			}else if ($key == "group_admins") {
				$last_url = wpqa_custom_permalink($group_id,"group_admins","group_admin");
				$selected = (wpqa_is_group_admins()?true:"");
			}else if ($key == "blocked_users") {
				$last_url = wpqa_custom_permalink($group_id,"blocked_users","blocked_user");
				$selected = (wpqa_is_blocked_users()?true:"");
			}
			if (isset($last_url) && $last_url != "") {
				if ($list_child == "li") {?>
					<li class='menu-item <?php echo "li_group_".$key.(isset($selected) && $selected == true?" active-tab":"")?>'>
						<a href="<?php echo esc_url($last_url)?>">
				<?php }else {?>
					<option<?php echo (isset($selected) && $selected == true?" selected='selected'":"")?> value="<?php echo esc_url($last_url)?>">
				<?php }
			}

			if ($key == "group") {
				esc_html_e("Discussion","wpqa");
			}else if ($key == "edit") {
				if ($is_super_admin || ($edit_group == "on" && ($user_id > 0 && ((isset($edit_other_groups) && $edit_other_groups == true && isset($moderators)) || ($user_id == $post_author))))) {
					esc_html_e("Edit","wpqa");
				}else {
					esc_html_e("Edit rules","wpqa");
				}
			}else if ($key == "group_requests") {
				esc_html_e("Requests","wpqa");
				$group_requests_array = get_post_meta($group_id,"group_requests_array",true);
				$group_requests_array = (is_array($group_requests_array) && !empty($group_requests_array)?$group_requests_array:array());
				$group_requests_array = count($group_requests_array);
				echo ($group_requests_array > 0?"<span class='notifications-number asked-count notifications-count".($group_requests_array <= 99?"":" notifications-number-super")."'>".($group_requests_array <= 99?$group_requests_array:"99+")."</span>":"");
			}else if ($key == "pending_posts") {
				esc_html_e("Group Posts","wpqa");
				$count_posts_by_type = wpqa_count_group_posts_by_type("posts","draft",$group_id);
				echo ($count_posts_by_type > 0?"<span class='notifications-number asked-count notifications-count".($count_posts_by_type <= 99?"":" notifications-number-super")."'>".($count_posts_by_type <= 99?$count_posts_by_type:"99+")."</span>":"");
			}else if ($key == "group_users") {
				esc_html_e("Users","wpqa");
			}else if ($key == "group_admins") {
				esc_html_e("Admins","wpqa");
			}else if ($key == "blocked_users") {
				esc_html_e("Blocked Users","wpqa");
			}
			if (isset($last_url) && $last_url != "") {
				if ($list_child == "li") {?>
						</a>
					</li>
				<?php }else {?>
					</option>
				<?php }
				$last_url = "";
			}
		}
	}
}
/* After added group */
add_action("wpqa_after_added_group","wpqa_after_added_group",1,2);
function wpqa_after_added_group($group_id,$user_id) {
	update_post_meta($group_id,"group_posts",0);
	update_post_meta($group_id,"group_users",1);
	update_post_meta($group_id,"group_users_array",array($user_id));
	update_post_meta($group_id,"group_moderators",array($user_id));
	
	$user_group_join = get_user_meta($user_id,"groups_array",true);
	$user_group_join = (is_array($user_group_join) && !empty($user_group_join)?$user_group_join:array());
	update_user_meta($user_id,"groups_array",array_merge($user_group_join,array($group_id)));
}
/* Redirect to the group */
add_filter('comment_post_redirect','wpqa_group_comment_redirect',1,2);
function wpqa_group_comment_redirect($location,$commentdata) {
	if (!isset($commentdata) || empty($commentdata->comment_post_ID)) {
		return $location;
	}
	$group_id = (int)get_post_meta($commentdata->comment_post_ID,"group_id",true);
	if ($group_id > 0) {
		$location = wpqa_custom_permalink($commentdata->comment_post_ID,"view_group_posts","view_group_post")."#comment-".$commentdata->comment_ID;
	}
	return $location;
}
/* Join group */
if (!function_exists('wpqa_join_group')) :
	function wpqa_join_group($allow_it = false) {
		$group_id = (int)$_POST['group_id'];
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			$group_requests_array = get_post_meta($group_id,"group_requests_array",true);
			$user_id = get_current_user_id();
			if (is_array($group_requests_array) && in_array($user_id,$group_requests_array)) {
				$update = wpqa_update_user_group_meta($user_id,"groups_array",$group_id);
				$update = wpqa_update_group_meta($group_id,"group_users_array",$user_id);
				if ($update == true) {
					wpqa_count_group_users($group_id);
				}
			}
		}
	}
endif;
add_action('wp_ajax_wpqa_join_group','wpqa_join_group');
add_action('wp_ajax_nopriv_wpqa_join_group','wpqa_join_group');
/* Leave group */
if (!function_exists('wpqa_leave_group')) :
	function wpqa_leave_group($allow_it = false) {
		$group_id = (int)$_POST['group_id'];
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			$user_id = get_current_user_id();
			$update = wpqa_update_user_group_meta($user_id,"groups_array",$group_id,"remove");
			$update = wpqa_update_group_meta($group_id,"group_users_array",$user_id,"remove");
			if ($update == true) {
				wpqa_update_moderator_group($group_id,$user_id,"remove");
				wpqa_count_group_users($group_id,"remove");
			}
		}
	}
endif;
add_action('wp_ajax_wpqa_leave_group','wpqa_leave_group');
add_action('wp_ajax_nopriv_wpqa_leave_group','wpqa_leave_group');
/* Request to join to group */
if (!function_exists('wpqa_request_group')) :
	function wpqa_request_group($allow_it = false) {
		$group_id = (int)$_POST['group_id'];
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			$user_id = get_current_user_id();
			$update = wpqa_update_user_group_meta($user_id,"group_requests_array",$group_id);
			$update = wpqa_update_group_meta($group_id,"group_requests_array",$user_id);
			if ($update == true) {
				$group_moderators = get_post_meta($group_id,"group_moderators",true);
				foreach ($group_moderators as $value) {
					wpqa_notifications_activities($value,$user_id,"",$group_id,"","request_group","notifications","","group");
				}
				$group_requests = (int)get_post_meta($group_id,"group_requests",true);
				$group_requests = ($group_requests != "" || $group_requests > 0?$group_requests:0);
				$group_requests++;
				$group_requests = update_post_meta($group_id,"group_requests",$group_requests);
			}
		}
	}
endif;
add_action('wp_ajax_wpqa_request_group','wpqa_request_group');
add_action('wp_ajax_nopriv_wpqa_request_group','wpqa_request_group');
/* Cancel request to the group */
if (!function_exists('wpqa_cancel_request_group')) :
	function wpqa_cancel_request_group($allow_it = false) {
		$group_id = (int)$_POST['group_id'];
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			$user_id = get_current_user_id();
			$update = wpqa_update_user_group_meta($user_id,"group_requests_array",$group_id,"remove");
			$update = wpqa_update_group_meta($group_id,"group_requests_array",$user_id,"remove");
			if ($update == true) {
				$group_requests = (int)get_post_meta($group_id,"group_requests",true);
				$group_requests = ($group_requests != "" || $group_requests > 0?$group_requests:0);
				$group_requests--;
				$group_requests = update_post_meta($group_id,"group_requests",($group_requests <= 0?0:$group_requests));
			}
		}
	}
endif;
add_action('wp_ajax_wpqa_cancel_request_group','wpqa_cancel_request_group');
add_action('wp_ajax_nopriv_wpqa_cancel_request_group','wpqa_cancel_request_group');
/* Approve the request */
if (!function_exists('wpqa_approve_request_group')) :
	function wpqa_approve_request_group($allow_it = false) {
		$group_id = (int)$_POST['group_id'];
		$user_id = (int)$_POST['user_id'];
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			$get_current_user_id = get_current_user_id();
			$is_super_admin = is_super_admin($get_current_user_id);
			$group_moderators = get_post_meta($group_id,"group_moderators",true);
			$post = get_post($group_id);
			if ($get_current_user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($get_current_user_id,$group_moderators)) {
				$allow_group_moderators = true;
			}
			if ($is_super_admin || $post->post_author == $get_current_user_id || isset($allow_group_moderators)) {
				$group_requests_array = get_post_meta($group_id,"group_requests_array",true);
				if (is_array($group_requests_array) && in_array($user_id,$group_requests_array)) {
					$update = wpqa_update_user_group_meta($user_id,"groups_array",$group_id);
					$update = wpqa_update_group_meta($group_id,"group_users_array",$user_id);
					if ($update == true) {
						wpqa_notifications_activities($user_id,$get_current_user_id,"",$group_id,"","approve_request_group","notifications","","group");
						wpqa_count_group_users($group_id);
					}
					$update = wpqa_update_group_meta($group_id,"group_requests_array",$user_id,"remove");
				}
			}
		}
	}
endif;
add_action('wp_ajax_wpqa_approve_request_group','wpqa_approve_request_group');
add_action('wp_ajax_nopriv_wpqa_approve_request_group','wpqa_approve_request_group');
/* Decline the request */
if (!function_exists('wpqa_decline_request_group')) :
	function wpqa_decline_request_group($allow_it = false) {
		$group_id = (int)$_POST['group_id'];
		$user_id = (int)$_POST['user_id'];
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			$get_current_user_id = get_current_user_id();
			$is_super_admin = is_super_admin($get_current_user_id);
			$group_moderators = get_post_meta($group_id,"group_moderators",true);
			$post = get_post($group_id);
			if ($get_current_user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($get_current_user_id,$group_moderators)) {
				$allow_group_moderators = true;
			}
			if ($is_super_admin || $post->post_author == $get_current_user_id || isset($allow_group_moderators)) {
				$update = wpqa_update_group_meta($group_id,"group_requests_array",$user_id,"remove");
				if ($update == true) {
					wpqa_notifications_activities($user_id,$get_current_user_id,"",$group_id,"","decline_request_group","notifications","","group");
				}
			}
		}
	}
endif;
add_action('wp_ajax_wpqa_decline_request_group','wpqa_decline_request_group');
add_action('wp_ajax_nopriv_wpqa_decline_request_group','wpqa_decline_request_group');
/* Approve all the requests */
if (!function_exists('wpqa_approve_request_all_group')) :
	function wpqa_approve_request_all_group($allow_it = false) {
		$group_id = (int)$_POST['group_id'];
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			$user_id = get_current_user_id();
			$is_super_admin = is_super_admin($user_id);
			$group_moderators = get_post_meta($group_id,"group_moderators",true);
			$post = get_post($group_id);
			if ($user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($user_id,$group_moderators)) {
				$allow_group_moderators = true;
			}
			if ($is_super_admin || $post->post_author == $user_id || isset($allow_group_moderators)) {
				$group_requests_array = get_post_meta($group_id,"group_requests_array",true);
				if (is_array($group_requests_array) && !empty($group_requests_array)) {
					foreach ($group_requests_array as $value) {
						$update = wpqa_update_user_group_meta($value,"groups_array",$group_id);
						$update = wpqa_update_group_meta($group_id,"group_users_array",$value);
						if ($update == true) {
							wpqa_notifications_activities($value,$user_id,"",$group_id,"","approve_request_group","notifications","","group");
						}
					}
					if ($update == true) {
						wpqa_count_group_users($group_id,"add",count($group_requests_array));
					}
					delete_post_meta($group_id,"group_requests_array");
				}
			}
		}
	}
endif;
add_action('wp_ajax_wpqa_approve_request_all_group','wpqa_approve_request_all_group');
add_action('wp_ajax_nopriv_wpqa_approve_request_all_group','wpqa_approve_request_all_group');
/* Decline all the requests */
if (!function_exists('wpqa_decline_request_all_group')) :
	function wpqa_decline_request_all_group($allow_it = false) {
		$group_id = (int)$_POST['group_id'];
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			$user_id = get_current_user_id();
			$is_super_admin = is_super_admin($user_id);
			$group_moderators = get_post_meta($group_id,"group_moderators",true);
			$post = get_post($group_id);
			if ($user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($user_id,$group_moderators)) {
				$allow_group_moderators = true;
			}
			if ($is_super_admin || $post->post_author == $user_id || isset($allow_group_moderators)) {
				$group_requests_array = get_post_meta($group_id,"group_requests_array",true);
				if (is_array($group_requests_array) && !empty($group_requests_array)) {
					foreach ($group_requests_array as $value) {
						wpqa_notifications_activities($value,$user_id,"",$group_id,"","decline_request_group","notifications","","group");
					}
					delete_post_meta($group_id,"group_requests_array");
				}
			}
		}
	}
endif;
add_action('wp_ajax_wpqa_decline_request_all_group','wpqa_decline_request_all_group');
add_action('wp_ajax_nopriv_wpqa_decline_request_all_group','wpqa_decline_request_all_group');
/* Accept invite */
if (!function_exists('wpqa_accept_invite')) :
	function wpqa_accept_invite($allow_it = false) {
		$group_id = (int)(isset($_POST["group_id"])?$_POST["group_id"]:0);
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			$user_id = get_current_user_id();
			if ($group_id > 0 && $user_id > 0) {
				$update = wpqa_update_user_group_meta($user_id,"groups_array",$group_id);
				$update = wpqa_update_group_meta($group_id,"group_users_array",$user_id);
				$update = wpqa_update_user_group_meta($user_id,"group_invitations",$group_id,"remove");
				$update = wpqa_update_group_meta($group_id,"group_invitations",$user_id,"remove");
				if ($update == true) {
					wpqa_count_group_users($group_id);
					wpqa_notifications_activities($user_id,"","",$group_id,"","accept_invite","activities","","group");
				}
			}
			if (!isset($_POST["mobile"])) {
				die();
			}
		}
	}
endif;
add_action('wp_ajax_wpqa_accept_invite','wpqa_accept_invite');
add_action('wp_ajax_nopriv_wpqa_accept_invite','wpqa_accept_invite');
/* Decline Invite */
if (!function_exists('wpqa_decline_invite')) :
	function wpqa_decline_invite($allow_it = false) {
		$group_id = (int)$_POST['group_id'];
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			$user_id = get_current_user_id();
			$update = wpqa_update_user_group_meta($user_id,"group_invitations",$group_id,"remove");
			$update = wpqa_update_group_meta($group_id,"group_invitations",$user_id,"remove");
			if ($update == true) {
				wpqa_notifications_activities($user_id,"","",$group_id,"","decline_invite","activities","","group");
			}
		}
	}
endif;
add_action('wp_ajax_wpqa_decline_invite','wpqa_decline_invite');
add_action('wp_ajax_nopriv_wpqa_decline_invite','wpqa_decline_invite');
/* Like the post */
if (!function_exists('wpqa_posts_like')) :
	function wpqa_posts_like($data = array()) {
		$mobile = (is_array($data) && !empty($data)?true:false);
		$data = (is_array($data) && !empty($data)?$data:$_POST);
		$post_id = (int)$data['post_id'];
		$user_id = get_current_user_id();
		$update = wpqa_update_group_meta($post_id,"posts_like",$user_id);
		$update = wpqa_update_user_group_meta($user_id,"posts_likes",$post_id);
		$posts_like = get_post_meta($post_id,"posts_like",true);
		$count = (is_array($posts_like)?count($posts_like):0);
		if ($update == true) {
			$get_post = get_post($post_id);
			$post_author = $get_post->post_author;
			if ($user_id > 0 && $post_author > 0 && $post_author != $user_id) {
				wpqa_notifications_activities($post_author,$user_id,"",$post_id,"","posts_like","notifications","","posts");
			}
			if ($user_id > 0) {
				wpqa_notifications_activities($user_id,"","",$post_id,"","posts_like","activities","","posts");
			}
		}
		if (isset($data["mobile"])) {
			return wpqa_count_number($count);
		}else {
			echo wpqa_count_number($count);
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_posts_like','wpqa_posts_like');
add_action('wp_ajax_nopriv_wpqa_posts_like','wpqa_posts_like');
/* Unlike the post */
if (!function_exists('wpqa_posts_unlike')) :
	function wpqa_posts_unlike($data = array()) {
		$mobile = (is_array($data) && !empty($data)?true:false);
		$data = (is_array($data) && !empty($data)?$data:$_POST);
		$post_id = (int)$data['post_id'];
		$user_id = get_current_user_id();
		$update = wpqa_update_group_meta($post_id,"posts_like",$user_id,"remove");
		$update = wpqa_update_user_group_meta($user_id,"posts_likes",$post_id,"remove");
		$posts_like = get_post_meta($post_id,"posts_like",true);
		$count = (is_array($posts_like)?count($posts_like):0);
		if ($update == true && $user_id > 0) {
			wpqa_notifications_activities($user_id,"","",$post_id,"","posts_unlike","activities","","posts");
		}
		if (isset($data["mobile"])) {
			return wpqa_count_number($count);
		}else {
			echo wpqa_count_number($count);
			die();
		}
	}
endif;
add_action('wp_ajax_wpqa_posts_unlike','wpqa_posts_unlike');
add_action('wp_ajax_nopriv_wpqa_posts_unlike','wpqa_posts_unlike');
/* Block users */
if (!function_exists('wpqa_block_user_group')) :
	function wpqa_block_user_group($allow_it = false) {
		$group_id = (int)$_POST['group_id'];
		$user_id = (int)$_POST['user_id'];
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			$update = wpqa_update_group_meta($group_id,"blocked_users_array",$user_id);
			if ($update == true) {
				wpqa_notifications_activities($user_id,"","",$group_id,"","blocked_group","notifications","","group");
			}
		}
	}
endif;
add_action('wp_ajax_wpqa_block_user_group','wpqa_block_user_group');
add_action('wp_ajax_nopriv_wpqa_block_user_group','wpqa_block_user_group');
/* Unblock users */
if (!function_exists('wpqa_unblock_user_group')) :
	function wpqa_unblock_user_group($allow_it = false) {
		$group_id = (int)$_POST['group_id'];
		$user_id = (int)$_POST['user_id'];
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			$update = wpqa_update_group_meta($group_id,"blocked_users_array",$user_id,"remove");
			if ($update == true) {
				wpqa_notifications_activities($user_id,"","",$group_id,"","unblocked_group","notifications","","group");
			}
		}
	}
endif;
add_action('wp_ajax_wpqa_unblock_user_group','wpqa_unblock_user_group');
add_action('wp_ajax_nopriv_wpqa_unblock_user_group','wpqa_unblock_user_group');
/* Remove user */
if (!function_exists('wpqa_remove_user_group')) :
	function wpqa_remove_user_group($allow_it = false) {
		$group_id = (int)$_POST['group_id'];
		$user_id = (int)$_POST['user_id'];
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			$update = wpqa_update_user_group_meta($user_id,"groups_array",$group_id,"remove");
			$update = wpqa_update_group_meta($group_id,"group_users_array",$user_id,"remove");
			if ($update == true) {
				wpqa_update_moderator_group($group_id,$user_id,"remove");
				wpqa_notifications_activities($user_id,"","",$group_id,"","removed_user_group","notifications","","group");
				wpqa_count_group_users($group_id,"remove");
			}
		}
	}
endif;
add_action('wp_ajax_wpqa_remove_user_group','wpqa_remove_user_group');
add_action('wp_ajax_nopriv_wpqa_remove_user_group','wpqa_remove_user_group');
/* Search to add a new moderator or invite user */
if (!function_exists('wpqa_new_user_group')) :
	function wpqa_new_user_group() {
		$user_value = wp_unslash(sanitize_text_field($_POST["user_value"]));
		$group_id = (int)$_POST["group_id"];
		$invite = sanitize_text_field($_POST["invite"]);
		$group_users_array = get_post_meta($group_id,"group_users_array",true);
		$group_users_array = (is_array($group_users_array) && !empty($group_users_array)?$group_users_array:array());
		$group_invitations = get_post_meta($group_id,"group_invitations",true);
		$group_invitations = (is_array($group_invitations) && !empty($group_invitations)?$group_invitations:array());
		$group_moderators = get_post_meta($group_id,"group_moderators",true);
		$group_moderators = (is_array($group_moderators) && !empty($group_moderators)?$group_moderators:array());
		$result_number = 10;
		$k_search      = 0;
		if ($user_value != "") {
			echo "<div class='result-div'>
				<ul class='list-unstyled mb-0 results-list'>";
					$number = (isset($result_number) && $result_number > 0?$result_number:apply_filters('users_per_page',get_option('posts_per_page')));
					$args = array(
						'orderby'    => "user_registered",
						'order'      => "DESC",
						'search'     => '*'.$user_value.'*',
						'number'     => $number,
						'fields'     => 'ID',
					);
					$invite_array = ($invite == "invite"?array('exclude' => array_merge($group_invitations,$group_users_array)):array('include' => $group_users_array));
					$query = new WP_User_Query(array_merge($invite_array,$args));
					$query = $query->get_results();
					if ($invite != "invite") {
						$query = array_diff($query,$group_moderators);
					}
					if (isset($query) && !empty($query)) {
						foreach ($query as $user) {
							$k_search++;
							if ($result_number >= $k_search) {
								$display_name = get_the_author_meta('display_name',$user);
								echo '<li>
									<a class="get-results add-user-to-input" data-id="'.$user.'" href="#" title="'.$display_name.'">'.wpqa_get_user_avatar(array("user_id" => $user,"size" => 29,"user_name" => $display_name,"class" => "rounded-circle")).'</a>
									<a class="add-user-to-input" data-id="'.$user.'" href="#" title="'.$display_name.'">'.str_ireplace($user_value,"<strong>".$user_value."</strong>",$display_name).'</a>
								</li>';
							}
						}
					}else {
						$show_no_found = true;
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
add_action('wp_ajax_wpqa_new_user_group','wpqa_new_user_group');
add_action('wp_ajax_nopriv_wpqa_new_user_group','wpqa_new_user_group');
/* Assign a new moderator or invite user */
if (!function_exists('wpqa_add_group_user')) :
	function wpqa_add_group_user($allow_it = false) {
		$group_id = (int)(isset($_POST["group_id"])?$_POST["group_id"]:0);
		$user_id = (int)(isset($_POST["user_id"])?$_POST["user_id"]:0);
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			if ($group_id > 0 && $user_id > 0) {
				$moderator = sanitize_text_field($_POST["moderator"]);
				if ($moderator == "moderator") {
					wpqa_update_moderator_group($group_id,$user_id,"add");
					wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("The moderator has added successfully.","wpqa").'</p></div>','wpqa_session');
				}else {
					$update = wpqa_update_group_meta($group_id,"group_invitations",$user_id);
					$update = wpqa_update_user_group_meta($user_id,"group_invitations",$group_id);
					if ($update == true) {
						wpqa_notifications_activities($user_id,get_current_user_id(),"",$group_id,"","add_group_invitations","notifications","","group");
						wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("The invite has sent successfully.","wpqa").'</p></div>','wpqa_session');
					}
				}
			}
			if (!isset($_POST["mobile"])) {
				die();
			}
		}
	}
endif;
add_action('wp_ajax_wpqa_add_group_user','wpqa_add_group_user');
add_action('wp_ajax_nopriv_wpqa_add_group_user','wpqa_add_group_user');
/* Remove moderator */
if (!function_exists('wpqa_remove_moderator_group')) :
	function wpqa_remove_moderator_group($allow_it = false) {
		$group_id = (int)$_POST['group_id'];
		$user_id = (int)$_POST['user_id'];
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			$update = wpqa_update_moderator_group($group_id,$user_id,"remove");
		}
	}
endif;
add_action('wp_ajax_wpqa_remove_moderator_group','wpqa_remove_moderator_group');
add_action('wp_ajax_nopriv_wpqa_remove_moderator_group','wpqa_remove_moderator_group');
/* Agree the posts */
if (!function_exists('wpqa_agree_posts_group')) :
	function wpqa_agree_posts_group($allow_it = false) {
		$post_id = (int)$_POST['group_id'];
		$group_id = get_post_meta($post_id,"group_id",true);
		$user_id = (int)$_POST['user_id'];
		$group_nonce = (isset($_POST["group_nonce"])?esc_html($_POST["group_nonce"]):"");
		if (wp_verify_nonce($group_nonce,'wpqa_group_nonce')) {
			$allow_it = true;
		}
		if ($allow_it == true) {
			$get_current_user_id = get_current_user_id();
			$group_moderators = get_post_meta($group_id,"group_moderators",true);
			if (is_super_admin($get_current_user_id) || (is_array($group_moderators) && !empty($group_moderators) && in_array($get_current_user_id,$group_moderators))) {
				remove_action('save_post','wpqa_save_post');
				$update_data = array(
					'ID'          => $post_id,
					'post_status' => 'publish',
				);
				wp_update_post($update_data);
				$post_approved_before = get_post_meta($post_id,"post_approved_before",true);
				if ($post_approved_before != "yes") {
					wpqa_post_after_approved($post_id,$user_id);
					wpqa_notifications_activities($user_id,$get_current_user_id,"",$post_id,"","approved_posts","notifications","","posts");
				}
			}
		}
	}
endif;
add_action('wp_ajax_wpqa_agree_posts_group','wpqa_agree_posts_group');
add_action('wp_ajax_nopriv_wpqa_agree_posts_group','wpqa_agree_posts_group');
/* Update group meta */
function wpqa_update_group_meta($group_id,$meta,$value,$type = "add") {
	$update_meta = get_post_meta($group_id,$meta,true);
	if ($type == "remove") {
		if (isset($update_meta) && !empty($update_meta) && in_array($value,$update_meta)) {
			update_post_meta($group_id,$meta,wpqa_remove_item_by_value($update_meta,$value));
			$update = true;
		}
	}else {
		if (empty($update_meta)) {
			update_post_meta($group_id,$meta,array($value));
			$update = true;
		}else if (is_array($update_meta) && !in_array($value,$update_meta)) {
			update_post_meta($group_id,$meta,array_merge($update_meta,array($value)));
			$update = true;
		}
	}
	return (isset($update) && $update == true?$update:false);
}
/* Update user group meta */
function wpqa_update_user_group_meta($user_id,$meta,$value,$type = "add") {
	$update_meta = get_user_meta($user_id,$meta,true);
	if ($type == "remove") {
		if (isset($update_meta) && !empty($update_meta) && in_array($value,$update_meta)) {
			update_user_meta($user_id,$meta,wpqa_remove_item_by_value($update_meta,$value));
			$update = true;
		}
	}else {
		if (empty($update_meta)) {
			update_user_meta($user_id,$meta,array($value));
			$update = true;
		}else if (is_array($update_meta) && !in_array($value,$update_meta)) {
			update_user_meta($user_id,$meta,array_merge($update_meta,array($value)));
			$update = true;
		}
	}
	return (isset($update) && $update == true?$update:false);
}
/* Update moderator */
function wpqa_update_moderator_group($group_id,$user_id,$type = "add") {
	if ($type == "add") {
		$update = wpqa_update_user_group_meta($user_id,"groups_moderator_array",$group_id);
		$update = wpqa_update_group_meta($group_id,"group_moderators",$user_id);
		if ($update == true) {
			wpqa_notifications_activities($user_id,"","",$group_id,"","add_group_moderator","notifications","","group");
		}
	}else {
		$update = wpqa_update_user_group_meta($user_id,"groups_moderator_array",$group_id,"remove");
		$update = wpqa_update_group_meta($group_id,"group_moderators",$user_id,"remove");
		if ($update == true) {
			wpqa_notifications_activities($user_id,"","",$group_id,"","remove_group_moderator","notifications","","group");
		}
	}
	return (isset($update) && $update == true?$update:false);
}
/* Count group users */
function wpqa_count_group_users($group_id,$type = "add",$count = 1) {
	$group_users = (int)get_post_meta($group_id,"group_users",true);
	$group_users = ($group_users != "" || $group_users > 0?$group_users:0);
	if ($type == "remove") {
		$group_users = $group_users-$count;
	}else {
		$group_users = $group_users+$count;
	}
	$group_users = update_post_meta($group_id,"group_users",($group_users <= 0?0:$group_users));
}
/* Check if can edit comments */
function wpqa_group_edit_comments($post_id,$is_super_admin,$can_edit_comment,$comment_user_id,$get_current_user_id,$edit_delete_posts_comments) {
	$group_id = get_post_meta($post_id,"group_id",true);
	$group_moderators = get_post_meta($group_id,"group_moderators",true);
	if ($is_super_admin || ($can_edit_comment == "on" && $comment_user_id > 0 && $comment_user_id == $get_current_user_id) || (isset($edit_delete_posts_comments["edit"]) && $edit_delete_posts_comments["edit"] == "edit" && is_array($group_moderators) && in_array($get_current_user_id,$group_moderators))) {
		return true;
	}
}
/* Check if can delete comments */
function wpqa_group_delete_comments($post_id,$is_super_admin,$can_delete_comment,$comment_user_id,$get_current_user_id,$edit_delete_posts_comments) {
	$group_id = get_post_meta($post_id,"group_id",true);
	$group_moderators = get_post_meta($group_id,"group_moderators",true);
	if ($is_super_admin || ($can_delete_comment == "on" && $comment_user_id > 0 && $comment_user_id == $get_current_user_id) || (isset($edit_delete_posts_comments["delete"]) && $edit_delete_posts_comments["delete"] == "delete" && is_array($group_moderators) && in_array($get_current_user_id,$group_moderators))) {
		return true;
	}
}
/* Count group posts by type and id */
if (!function_exists('wpqa_count_group_posts_by_type')) :
	function wpqa_count_group_posts_by_type($post_type = 'post',$post_status = "publish",$group_id = 0) {
		$args = array(
			'post_type'   => $post_type,
			'post_status' => $post_status
		);
		$group_array = ($group_id > 0?array("meta_query" => array(array("type" => "numeric","key" => "group_id","value" => (int)$group_id,"compare" => "="))):array());
		$the_query = new WP_Query(array_merge($group_array,$args));
		return $the_query->found_posts;
		wp_reset_postdata();
	}
endif;
/* Invite users */
add_action("wpqa_group_invite_users","wpqa_group_invite_users",1,7);
function wpqa_group_invite_users($group_id,$is_super_admin,$post_author,$user_id,$group_invitation,$group_moderators,$group_users_array) {
	if (is_user_logged_in() && ($is_super_admin || ($post_author == $user_id && $user_id > 0) || (($group_invitation == "all" || $group_invitation == "admin_moderators") && $user_id > 0 && is_array($group_moderators) && in_array($user_id,$group_moderators)) || ($group_invitation == "all" && is_array($group_users_array) && in_array($user_id,$group_users_array)))) {?>
		<div class="page-section add-user-form">
			<h2 class="post-title-2"><i class="icon-vcard"></i><?php esc_html_e("Invite a new user","wpqa")?></h2>
			<div class="row row-warp row-boot">
				<div class="col col9 col-boot col-boot-sm-9">
					<input data-id="<?php echo (int)$group_id?>" type="text" placeholder="<?php esc_html_e("Type a name or email","wpqa")?>" class="add-new-user form-control">
					<div class="loader_2 search_loader user_loader"></div>
					<div class="live-search-results mt-2 search-results results-empty user-results"></div>
				</div>
				<div class="col col3 col-boot col-boot-sm-3 button-user-col user-col-not-activate">
					<div></div>
					<a type="text" data-nonce="<?php echo wp_create_nonce("wpqa_group_nonce")?>" class="button-default button-hide-click new-user-button btn btn__primary w-100"><?php esc_html_e("Send invite","wpqa")?></a>
				<span class="load_span"><span class="loader_2"></span></span>
				</div>
			</div>
		</div>
	<?php }
}
/* In group posts page */
function wpqa_is_group_posts() {
	if (wpqa_is_edit_groups() || is_singular("group") || wpqa_is_group_requests() || wpqa_is_group_users() || wpqa_is_group_admins() || wpqa_is_blocked_users() || wpqa_is_posts_group() || wpqa_is_view_posts_group() || wpqa_is_edit_posts_group()) {
		return true;
	}
}
/* Get group id */
function wpqa_group_id() {
	if (is_singular("group") || wpqa_is_edit_groups() || wpqa_is_group_requests() || wpqa_is_group_users() || wpqa_is_group_admins() || wpqa_is_blocked_users() || wpqa_is_posts_group() || wpqa_is_view_posts_group() || wpqa_is_edit_posts_group()) {
		if (wpqa_is_edit_groups()) {
			$group_id = (int)get_query_var(apply_filters('wpqa_edit_groups','edit_group'));
		}else if (wpqa_is_group_requests()) {
			$group_id = (int)get_query_var(apply_filters('wpqa_group_requests','group_request'));
		}else if (wpqa_is_group_users()) {
			$group_id = (int)get_query_var(apply_filters('wpqa_group_users','group_user'));
		}else if (wpqa_is_group_admins()) {
			$group_id = (int)get_query_var(apply_filters('wpqa_group_admins','group_admin'));
		}else if (wpqa_is_blocked_users()) {
			$group_id = (int)get_query_var(apply_filters('wpqa_blocked_users','blocked_user'));
		}else if (wpqa_is_posts_group()) {
			$group_id = (int)get_query_var(apply_filters('wpqa_posts_group','post_group'));
		}else if (wpqa_is_view_posts_group()) {
			$post_id = (int)get_query_var(apply_filters('wpqa_view_posts_group','view_post_group'));
			$group_id = (int)get_post_meta($post_id,"group_id",true);
		}else if (wpqa_is_edit_posts_group()) {
			$post_id = (int)get_query_var(apply_filters('wpqa_edit_posts_group','edit_post_group'));
			$group_id = (int)get_post_meta($post_id,"group_id",true);
		}else {
			global $post;
			$group_id = $post->ID;
		}
		return $group_id;
	}
}
/* Pagination in group page */
add_action('template_redirect','wpqa_group_pagination',0);
function wpqa_group_pagination() {
	if (is_singular('group')) {
		global $wp_query;
		$page = (int)$wp_query->get('page');
		if ($page > 1) {
			$wp_query->set('page',1);
			$wp_query->set('paged',$page);
		}
		remove_action('template_redirect','redirect_canonical');
	}
}?>