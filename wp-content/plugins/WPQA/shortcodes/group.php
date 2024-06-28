<?php

/* @author    2codeThemes
*  @package   WPQA/shortcodes
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

ob_start();
/* Add & Edit group */
function wpqa_add_edit_group($type) {
	global $group_add,$group_edit;
	$user_id = get_current_user_id();
	$is_super_admin = is_super_admin($user_id);
	$rand = rand(1,1000);

	if ($type == "add") {
		$the_captcha_group = wpqa_options("the_captcha_group");
	}else {
		$get_group = (int)get_query_var(apply_filters('wpqa_edit_groups','edit_group'));
		$get_post_g = get_post($get_group);
	}
	
	$out = apply_filters('wpqa_add_edit_group_before_form',false,$type,$group_add,$group_edit,(isset($get_group)?$get_group:0)).
	'<form class="form-post wpqa_form" method="post" enctype="multipart/form-data">'.(isset($_POST["form_type"]) && $_POST["form_type"] == $type."_group"?apply_filters('wpqa_'.$type.'_group',$type):"").'
		<div class="form-inputs clearfix">';
			if ($type == "edit") {
				$g_group_privacy = get_post_meta($get_group,"group_privacy",true);
				$g_group_invitation = get_post_meta($get_group,"group_invitation",true);
				$g_group_allow_posts = get_post_meta($get_group,"group_allow_posts",true);
				$g_group_approval = get_post_meta($get_group,"group_approval",true);
				$g_group_comments = get_post_meta($get_group,"group_comments",true);
				$g_group_rules = get_post_meta($get_group,"group_rules",true);
				$group_moderators = get_post_meta($get_group,"group_moderators",true);
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
			}

			if ($user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($user_id,$group_moderators)) {
				$moderators = true;
			}
			
			if ($type == "add" || ($type == "edit" && ($is_super_admin || ($user_id > 0 && ((isset($edit_other_groups) && $edit_other_groups == true && isset($moderators)) || ($user_id == $get_post_g->post_author)))))) {
				$see_all = true;
			}

			if (isset($see_all)) {
				$out .= '<p>
					<label for="group-title-'.$rand.'">'.esc_html__("Group Title","wpqa").'<span class="required">*</span></label>
					<input name="title" id="group-title-'.$rand.'" class="form-control the-title" type="text" value="'.($type == "add" && isset($group_add['title'])?wp_unslash(esc_html($group_add['title'])):($type == "edit"?(isset($group_edit['title'])?wp_unslash(esc_html($group_edit['title'])):esc_html($get_post_g->post_title)):"")).'"'.apply_filters("wpqa_group_title_attrs",false).'>
					<i class="icon-chat"></i>
					<span class="form-description">'.esc_html__("Please choose an appropriate title for the group so it can be answered easily.","wpqa").'</span>
				</p>'.
				apply_filters('wpqa_add_edit_group_after_title',false,$type,$group_add,$group_edit,(isset($get_group)?$get_group:0));
				
				$group_privacy = (isset($_POST["group_privacy"]) && $_POST["group_privacy"]?esc_html($_POST["group_privacy"]):($type == "edit"?esc_html($g_group_privacy):""));
				$out .= '<div class="form-group row-boot align-items-center mb-4">
					<p class="col-boot-6 col-boot-sm-4 col-boot-md-3 text-right group_privacy_field wpqa_radio_p"><label>'.esc_html__("Group privacy","wpqa").'<span class="required">*</span></label></p>
					<div class="wpqa_radio_div wpqa_radio_group col-boot-6 col-boot-sm-8 col-boot-md-9">
						<p class="d-inline-flex mr-3">
							<span class="wpqa_radio"><input id="group_privacy_public_'.$rand.'" name="group_privacy" type="radio" value="public"'.($group_privacy == "public" || ($type == "add" && empty($_POST))?' checked="checked"':'').'></span>
							<label for="group_privacy_public_'.$rand.'">'.esc_html__("Public group","wpqa").'</label>
						</p>
						<p class="d-inline-flex">
							<span class="wpqa_radio"><input id="group_privacy_private_'.$rand.'" name="group_privacy" type="radio" value="private"'.($group_privacy == "private"?' checked="checked"':'').'></span>
							<label for="group_privacy_private_'.$rand.'">'.esc_html__("Private group","wpqa").'</label>
						</p>';
						$out .= '<div class="clearfix"></div>
					</div>
				</div>';

				$group_invitation = (isset($_POST["group_invitation"]) && $_POST["group_invitation"]?esc_html($_POST["group_invitation"]):($type == "edit"?esc_html($g_group_invitation):""));
				$out .= '<div class="form-group row-boot align-items-center mb-4">
					<p class="col-boot-6 col-boot-sm-4 col-boot-md-3 text-right group_invitation_field wpqa_radio_p"><label>'.esc_html__("Group invitation","wpqa").'<span class="required">*</span></label></p>
					<div class="wpqa_radio_div wpqa_radio_group col-boot-6 col-boot-sm-8 col-boot-md-9">
						<p class="d-inline-flex mr-3">
							<span class="wpqa_radio"><input id="group_invitation_all_'.$rand.'" name="group_invitation" type="radio" value="all"'.($group_invitation == "all" || ($type == "add" && empty($_POST))?' checked="checked"':'').'></span>
							<label for="group_invitation_all_'.$rand.'">'.esc_html__("All group members","wpqa").'</label>
						</p>
						<p class="d-inline-flex mr-3">
							<span class="wpqa_radio"><input id="group_invitation_admin_moderators_'.$rand.'" name="group_invitation" type="radio" value="admin_moderators"'.($group_invitation == "admin_moderators"?' checked="checked"':'').'></span>
							<label for="group_invitation_admin_moderators_'.$rand.'">'.esc_html__("Admin and moderators","wpqa").'</label>
						</p>
						<p class="d-inline-flex">
							<span class="wpqa_radio"><input id="group_invitation_admin_'.$rand.'" name="group_invitation" type="radio" value="admin"'.($group_invitation == "admin"?' checked="checked"':'').'></span>
							<label for="group_invitation_admin_'.$rand.'">'.esc_html__("Admin only","wpqa").'</label>
						</p>';
						$out .= '<div class="clearfix"></div>
					</div>
				</div>';

				$group_allow_posts = (isset($_POST["group_allow_posts"]) && $_POST["group_allow_posts"]?esc_html($_POST["group_allow_posts"]):($type == "edit"?esc_html($g_group_allow_posts):""));
				$out .= '<div class="form-group row-boot align-items-center mb-4">
					<p class="col-boot-6 col-boot-sm-4 col-boot-md-3 text-right group_allow_posts_field wpqa_radio_p"><label>'.esc_html__("Group posts","wpqa").'<span class="required">*</span></label></p>
					<div class="wpqa_radio_div wpqa_radio_group col-boot-6 col-boot-sm-8 col-boot-md-9">
						<p class="d-inline-flex mr-3">
							<span class="wpqa_radio"><input id="group_allow_posts_all_'.$rand.'" name="group_allow_posts" type="radio" value="all"'.($group_allow_posts == "all" || ($type == "add" && empty($_POST))?' checked="checked"':'').'></span>
							<label for="group_allow_posts_all_'.$rand.'">'.esc_html__("All group members","wpqa").'</label>
						</p>
						<p class="d-inline-flex mr-3">
							<span class="wpqa_radio"><input id="group_allow_posts_admin_moderators_'.$rand.'" name="group_allow_posts" type="radio" value="admin_moderators"'.($group_allow_posts == "admin_moderators"?' checked="checked"':'').'></span>
							<label for="group_allow_posts_admin_moderators_'.$rand.'">'.esc_html__("Admin and moderators","wpqa").'</label>
						</p>
						<p class="d-inline-flex">
							<span class="wpqa_radio"><input id="group_allow_posts_admin_'.$rand.'" name="group_allow_posts" type="radio" value="admin"'.($group_allow_posts == "admin"?' checked="checked"':'').'></span>
							<label for="group_allow_posts_admin_'.$rand.'">'.esc_html__("Admin only","wpqa").'</label>
						</p>';
						$out .= '<div class="clearfix"></div>
					</div>
				</div>';

				if ($type == "add" && ((isset($group_add['group_approval']) && $group_add['group_approval'] == "on") || empty($group_add))) {
					$active_group_approval = true;
				}else if ($type == "edit" && ((isset($group_edit['group_approval']) && $group_edit['group_approval'] == "on") || (!isset($group_edit['group_approval']) && $g_group_approval == "on"))) {
					$active_group_approval = true;
				}

				$out .= apply_filters('wpqa_add_edit_group_before_group_approval',false,$type,$group_add,$group_edit,(isset($get_group)?$get_group:0)).'
				<div class="form-group row row-boot row-warp align-items-center mb-0">
					<p class="col col12 col-boot-sm-8 col-boot-md-9 offset-md-3 offset-sm-4 wpqa_checkbox_p add_group_approval_p wpqa_hide"'.((empty($_POST) && $group_allow_posts == "all") || (isset($_POST["group_allow_posts"]) && $_POST["group_allow_posts"] == "all")?" style='display: block'":"").'>
						<label for="group_approval-'.$rand.'">
							<span class="wpqa_checkbox"><input type="checkbox" id="group_approval-'.$rand.'" class="group_approval" name="group_approval" value="on"'.(isset($active_group_approval)?" checked='checked'":"").'></span>
							<span class="wpqa_checkbox_span">'.esc_html__("Auto approval for the posts in this group?","wpqa").'</span>
						</label>
					</p>
				</div>';

				if ($type == "edit") {
					$group_image = get_post_meta($get_group,"group_image",true);
					if ((is_array($group_image) && isset($group_image["id"])) || (!is_array($group_image) && $group_image != "")) {
						$out .= '<div class="clearfix"></div>
						<div class="wpqa-delete-image d-flex align-items-center mb-4">
							<span class="author-image-span wpqa-delete-image-span mr-4">'.wpqa_get_aq_resize_img(250,250,"",(is_array($group_image) && isset($group_image["id"])?$group_image["id"]:(!is_array($group_image) && $group_image != ""?$group_image:"")),"no","").'</span>
							<div class="clearfix"></div>
							<div class="button-default wpqa-remove-image btn btn__danger btn__small__width" data-name="group_image" data-type="post_meta" data-id="'.$get_group.'" data-image="'.(is_array($group_image) && isset($group_image["id"]) && $group_image["id"] != 0?$group_image['id']:(!is_array($group_image) && $group_image != ""?$group_image:"")).'" data-nonce="'.wp_create_nonce("wpqa_remove_image").'">'.esc_html__("Delete","wpqa").'</div>
							<div class="loader_2 loader_4"></div>
						</div>';
					}
				}

				if ($type == "add" && ((isset($group_add['group_comments']) && $group_add['group_comments'] == "on") || empty($group_add))) {
					$active_group_comments = true;
				}else if ($type == "edit" && ((isset($group_edit['group_comments']) && $group_edit['group_comments'] == "on") || (!isset($group_edit['group_comments']) && $g_group_comments == "on"))) {
					$active_group_comments = true;
				}
				$out .= apply_filters('wpqa_add_edit_group_before_group_comments',false,$type,$group_add,$group_edit,(isset($get_group)?$get_group:0)).'
				<div class="form-group row row-boot row-warp align-items-center mb-0">
					<p class="col col12 col-boot-sm-8 col-boot-md-9 offset-md-3 offset-sm-4 wpqa_checkbox_p add_group_comments_p">
						<label for="group_comments-'.$rand.'">
							<span class="wpqa_checkbox"><input type="checkbox" id="group_comments-'.$rand.'" class="group_comments" name="group_comments" value="on"'.(isset($active_group_comments)?" checked='checked'":"").'></span>
							<span class="wpqa_checkbox_span">'.esc_html__("Activate comments in this group?","wpqa").'</span>
						</label>
					</p>
				</div>';

				$out .= '<div class="question-multiple-upload question-upload-featured">
					<label for="group_image-'.$rand.'">'.esc_html__("Upload the group photo, that represents this group","wpqa").'</label>
					<div class="clearfix"></div>
					<div class="fileinputs">
						<input type="file" class="file" name="group_image" id="group_image-'.$rand.'">
						<i class="icon-camera"></i>
						<div class="fakefile">
							<button type="button">'.esc_html__("Select file","wpqa").'</button>
							<span>'.esc_html__("Browse","wpqa").'</span>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>'.apply_filters('wpqa_add_edit_group_after_group_image',false,$type,$group_add,$group_edit,(isset($get_group)?$get_group:0));

				if ($type == "edit") {
					$group_cover = get_post_meta($get_group,"group_cover",true);
					if ((is_array($group_cover) && isset($group_cover["id"])) || (!is_array($group_cover) && $group_cover != "")) {
						$out .= '<div class="clearfix"></div>
						<div class="wpqa-delete-image d-flex align-items-center mb-4">
							<span class="author-image-span wpqa-delete-image-span mr-4">'.wpqa_get_aq_resize_img(250,250,"",(is_array($group_cover) && isset($group_cover["id"])?$group_cover["id"]:(!is_array($group_cover) && $group_cover != ""?$group_cover:"")),"no","").'</span>
							<div class="clearfix"></div>
							<div class="button-default wpqa-remove-image btn btn__danger btn__small__width" data-name="group_cover" data-type="post_meta" data-id="'.$get_group.'" data-image="'.(is_array($group_cover) && isset($group_cover["id"]) && $group_cover["id"] != 0?$group_cover['id']:(!is_array($group_cover) && $group_cover != ""?$group_cover:"")).'" data-nonce="'.wp_create_nonce("wpqa_remove_image").'">'.esc_html__("Delete","wpqa").'</div>
							<div class="loader_2 loader_4"></div>
						</div>';
					}
				}
				$out .= '<div class="question-multiple-upload question-upload-featured">
					<label for="group_cover-'.$rand.'">'.esc_html__("Upload the group cover","wpqa").'</label>
					<div class="clearfix"></div>
					<div class="fileinputs">
						<input type="file" class="file" name="group_cover" id="group_cover-'.$rand.'">
						<i class="icon-camera"></i>
						<div class="fakefile">
							<button type="button">'.esc_html__("Select file","wpqa").'</button>
							<span>'.esc_html__("Browse","wpqa").'</span>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>'.apply_filters('wpqa_add_edit_group_after_group_cover',false,$type,$group_add,$group_edit,(isset($get_group)?$get_group:0));
			}

			$out .= '<div class="wpqa_textarea">
				<label for="group_rules-'.$type.'-'.$rand.'">'.esc_html__("Group rules","wpqa").'</label>';
				$settings = array("textarea_name" => "group_rules","media_buttons" => true,"textarea_rows" => 10,array("tinymce" => array("theme_advanced_disable" => "bold,italic,underline,bullist,numlist,link,unlink,forecolor,undo,redo")));
				$settings = apply_filters('wpqa_group_rules_editor_setting',$settings);
				ob_start();
				wp_editor(($type == "add" && isset($group_add['group_rules'])?wpqa_esc_textarea($group_add['group_rules']):($type == "edit"?(isset($group_edit['group_rules'])?wpqa_esc_textarea($group_edit['group_rules']):wpqa_esc_textarea($g_group_rules)):"")),"group_rules-".$type.'-'.$rand,$settings);
				$editor_contents = ob_get_clean();
				$out .= '<div class="the-details the-textarea">'.$editor_contents.'</div>
				<span class="form-description">'.esc_html__("Type the group rules thoroughly and in details.","wpqa").'</span>
			</div>'.
			apply_filters('wpqa_add_edit_group_after_rules',false,$type,$group_add,$group_edit,(isset($get_group)?$get_group:0));

			if ($type == "add" && $the_captcha_group == "on") {
				$out .= wpqa_add_captcha($the_captcha_group,"group",$rand);
			}
			$terms_active_group = wpqa_options("terms_active_group");
			if ($terms_active_group == "on" && $type == "add") {
				if (isset($group_add['terms_active']) && $group_add['terms_active'] == "on") {
					$active_terms = true;
				}
				$terms_link = wpqa_options("terms_link_group");
				$terms_page = wpqa_options('terms_page_group');
				$terms_active_target = wpqa_options('terms_active_target_group');
				$privacy_policy = wpqa_options('privacy_policy_group');
				$privacy_active_target = wpqa_options('privacy_active_target_group');
				$privacy_page = wpqa_options('privacy_page_group');
				$privacy_link = wpqa_options('privacy_link_group');
				$out .= '<p class="wpqa_checkbox_p">
					<label for="terms_active-'.$rand.'">
						<span class="wpqa_checkbox"><input type="checkbox" id="terms_active-'.$rand.'" name="terms_active" value="on" '.(isset($active_terms)?"checked='checked'":"").'></span>
						<span class="wpqa_checkbox_span">'.sprintf(esc_html__('By adding your group, you agree to the %1$s Terms of Service %2$s %3$s.','wpqa'),'<a target="'.($terms_active_target == "same_page"?"_self":"_blank").'" href="'.esc_url(isset($terms_link) && $terms_link != ""?$terms_link:(isset($terms_page) && $terms_page != ""?get_page_link($terms_page):"#")).'">','</a>',($privacy_policy == "on"?" ".sprintf(esc_html__('and %1$s Privacy Policy %2$s','wpqa'),'<a target="'.($privacy_active_target == "same_page"?"_self":"_blank").'" href="'.esc_url(isset($privacy_link) && $privacy_link != ""?$privacy_link:(isset($privacy_page) && $privacy_page != ""?get_page_link($privacy_page):"#")).'">','</a>'):"")).'<span class="required">*</span></span>
					</label>
				</p>';
			}
		$out .= '</div>
		
		<p class="form-submit mb-0">';
			if ($type == "edit") {
				$out .= '<input type="hidden" name="ID" value="'.$get_group.'">';
				$group_moderators = get_post_meta($get_group,"group_moderators",true);
			}
			if ($type == "edit" && !$is_super_admin && $user_id != $get_post_g->post_author && $user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($user_id,$group_moderators)) {
				$submit_button = esc_html__("Update the rules","wpqa");
			}else if ($type == "add") {
				$submit_button = esc_html__("Publish Your Group","wpqa");
			}else {
				$submit_button = esc_html__("Edit Group","wpqa");
			}
			$out .= '<input type="hidden" name="form_type" value="'.$type.'_group">
			<input type="hidden" name="wpqa_'.$type.'_group_nonce" value="'.wp_create_nonce("wpqa_".$type."_group_nonce").'">
			'.(wpqa_input_button() == "button"?'<button type="submit" class="btn btn__primary btn__block btn__large__height button-default button-hide-click">'.$submit_button.'</button>':'<input type="submit" value="'.$submit_button.'" class="button-default button-hide-click">').'
			<span class="load_span"><span class="loader_2"></span></span>
		</p>
	
	</form>';
	return $out;
}
/* Get group status */
function wpqa_get_group_status($user_id) {
	if (is_user_logged_in()) {
		$group_publish = wpqa_options("group_publish");
	}
	$group_status = "publish";
	if ($group_publish == "draft") {
		$group_status = "draft";
	}
	$custom_permission = wpqa_options("custom_permission");
	if ($custom_permission == "on") {
		if (is_user_logged_in()) {
			$user_is_login = get_userdata($user_id);
			$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
			$group_status = (isset($roles["approve_group"]) && $roles["approve_group"] == 1?"publish":"draft");
		}
	}
	if (is_super_admin($user_id)) {
		$group_status = "publish";
	}
	return (isset($group_status)?$group_status:false);
}
/* Add group */
function wpqa_add_group($type) {
	if (isset($_POST["form_type"]) && $_POST["form_type"] == "add_group") :
		$return = wpqa_process_new_groups($_POST);
		if (is_wp_error($return)) :
			return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
		else :
			$get_group = get_post($return);
			$post_author = $get_group->post_author;
			$post_type = $get_group->post_type;
			if ($post_type == "group") {
				$get_current_user_id = get_current_user_id();
				if ($get_group->post_status == "draft") {
					wpqa_notification_group($return,$get_current_user_id);
					wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Your group was successfully added, It's under review.","wpqa").'</p></div>','wpqa_session');
					wp_redirect(esc_url(home_url('/')));
				}else {
					wpqa_post_after_approved($get_group);
					wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Your group has been added successfully.","wpqa").'</p></div>','wpqa_session');
					if ($get_current_user_id > 0) {
						wpqa_notifications_activities($get_current_user_id,"","",$return,"","add_group","activities","","group");
					}
					wpqa_post_publish($get_group,$post_author);
					wpqa_count_posts($post_type,$post_author,"+");
					wp_redirect(get_permalink($return));
				}
			}
			exit;
		endif;
	endif;
}
add_filter('wpqa_add_group','wpqa_add_group');
/* Notification group */
function wpqa_notification_group($return,$get_current_user_id) {
	$send_email_draft_groups = wpqa_options("send_email_draft_groups");
	if ($send_email_draft_groups == "on") {
		$send_text = wpqa_send_mail(
			array(
				'content' => wpqa_options("email_draft_groups"),
				'post_id' => $return,
			)
		);
		$email_title = wpqa_options("title_new_draft_groups");
		$email_title = ($email_title != ""?$email_title:esc_html__("New group for review","wpqa"));
		$email_title = wpqa_send_mail(
			array(
				'content' => $email_title,
				'title'   => true,
				'break'   => '',
				'post_id' => $return,
			)
		);
		wpqa_send_mails(
			array(
				'title'   => $email_title,
				'message' => $send_text,
			)
		);
	}
	if ($get_current_user_id > 0) {
		wpqa_notifications_activities($get_current_user_id,"","","","","approved_group","activities","","group");
	}
}
/* Process new groups */
function wpqa_process_new_groups($data) {
	global $group_add;
	@set_time_limit(0);
	$errors = new WP_Error();
	$group_add = array();
	$form_type = (isset($data["form_type"]) && $data["form_type"] != ""?$data["form_type"]:"");
	if ($form_type == "add_group") {
		$user_id = get_current_user_id();
		
		$fields = array(
			'title','group_privacy','group_invitation','group_allow_posts','group_approval','group_image','group_cover','group_comments','group_rules','wpqa_captcha','terms_active'
		);
		
		$fields = apply_filters("wpqa_add_group_fields",$fields,"add");
		
		foreach ($fields as $field) :
			if (isset($data[$field])) $group_add[$field] = $data[$field]; else $group_add[$field] = '';
		endforeach;

		if (!isset($data['mobile']) && (!isset($data['wpqa_add_group_nonce']) || !wp_verify_nonce($data['wpqa_add_group_nonce'],'wpqa_add_group_nonce'))) {
			$errors->add('nonce-error','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There is an error, Please reload the page and try again.","wpqa"));
		}
		
		$custom_permission = wpqa_options("custom_permission");
		$add_group = wpqa_options("add_group");
		if (is_user_logged_in()) {
			$user_is_login = get_userdata($user_id);
			$user_login_group = wpqa_get_user_group($user_is_login);
			$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
		}
		if (($custom_permission != "on" && ((isset($user_login_group) && $user_login_group == "wpqa_under_review") || (isset($user_login_group) && $user_login_group == "activation"))) || ($custom_permission == "on" && (is_user_logged_in() && !is_super_admin($user_id) && empty($roles["add_group"])) || (!is_user_logged_in() && $add_group != "on"))) {
			$errors->add('required','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Sorry, you do not have permission to add group.","wpqa"));
			if (!is_user_logged_in()) {
				$errors->add('required','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("You must login to add group.","wpqa"));
			}
		}else if (!is_user_logged_in()) {
			$errors->add('required','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("You must login to add group.","wpqa"));
		}
		
		/* Validate Required Fields */
		do_action("wpqa_add_group_errors",$errors,$data,"add");
		
		/* Group photo */

		$group_image = '';

		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		
		if (isset($_FILES['group_image']) && !empty($_FILES['group_image']['name'])) :
			$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/webp","image/gif","image/tiff","image/ico");
			if (!isset($data['mobile']) && !in_array($_FILES['group_image']['type'],$types)) :
				$errors->add('upload-error',esc_html__("Attachment Error! Please upload image only.","wpqa"));
				return $errors;
			endif;
			
			$group_image = wp_handle_upload($_FILES['group_image'],array('test_form' => false),current_time('mysql'));
			
			if (isset($group_image['error'])) :
				$errors->add('upload-error',esc_html__("Attachment Error: ","wpqa") . $group_image['error']);
				return $errors;
			endif;
		endif;
		
		/* Group cover */

		$group_cover = '';
		
		if (isset($_FILES['group_cover']) && !empty($_FILES['group_cover']['name'])) :
			$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/webp","image/gif","image/tiff","image/ico");
			if (!isset($data['mobile']) && !in_array($_FILES['group_cover']['type'],$types)) :
				$errors->add('upload-error',esc_html__("Attachment Error! Please upload image only.","wpqa"));
				return $errors;
			endif;
			
			$group_cover = wp_handle_upload($_FILES['group_cover'],array('test_form' => false),current_time('mysql'));
			
			if (isset($group_cover['error'])) :
				$errors->add('upload-error',esc_html__("Attachment Error: ","wpqa") . $group_cover['error']);
				return $errors;
			endif;
		endif;
		
		if (sizeof($errors->errors) > 0) return $errors;
		
		$group_status = wpqa_get_group_status($user_id);
		
		/* Create group */
		
		$insert_data = array(
			'post_title'   => esc_html($group_add['title']),
			'post_status'  => $group_status,
			'post_author'  => $user_id,
			'post_type'	   => 'group',
		);
			
		$group_id = wp_insert_post($insert_data);
			
		if ($group_id == 0 || is_wp_error($group_id)) wp_die(esc_html__("Error in group.","wpqa"));
		
		if ($group_add['group_privacy']) {
			update_post_meta($group_id,'group_privacy',esc_html($group_add['group_privacy']));
		}
		
		if ($group_add['group_invitation']) {
			update_post_meta($group_id,'group_invitation',esc_html($group_add['group_invitation']));
		}
		
		if ($group_add['group_allow_posts']) {
			update_post_meta($group_id,'group_allow_posts',esc_html($group_add['group_allow_posts']));
		}
		
		if ($group_add['group_approval']) {
			update_post_meta($group_id,'group_approval',esc_html($group_add['group_approval']));
		}
		
		if ($group_add['group_comments']) {
			update_post_meta($group_id,'group_comments',esc_html($group_add['group_comments']));
		}
		
		if ($group_add['group_rules']) {
			update_post_meta($group_id,'group_rules',wpqa_esc_textarea($group_add['group_rules']));
		}
		
		/* Group photo */
		
		if (isset($group_image['type']) && isset($group_image['file'])) :
			$group_image_data = array(
				'post_mime_type' => $group_image['type'],
				'post_title'	 => preg_replace('/\.[^.]+$/','',basename($group_image['file'])),
				'post_content'   => '',
				'post_status'	 => 'inherit',
				'post_author'    => $user_id,
			);
			$group_image_id = wp_insert_attachment($group_image_data,$group_image['file'],$group_id);
			$group_image_metadata = wp_generate_attachment_metadata($group_image_id,$group_image['file']);
			wp_update_attachment_metadata($group_image_id, $group_image_metadata);
			update_post_meta($group_id,"group_image",$group_image_id);
		endif;
		
		/* Group cover */
		
		if (isset($group_cover['type']) && isset($group_cover['file'])) :
			$group_cover_data = array(
				'post_mime_type' => $group_cover['type'],
				'post_title'	 => preg_replace('/\.[^.]+$/','',basename($group_cover['file'])),
				'post_content'   => '',
				'post_status'	 => 'inherit',
				'post_author'    => $user_id,
			);
			$group_cover_id = wp_insert_attachment($group_cover_data,$group_cover['file'],$group_id);
			$group_cover_metadata = wp_generate_attachment_metadata($group_cover_id,$group_cover['file']);
			wp_update_attachment_metadata($group_cover_id, $group_cover_metadata);
			update_post_meta($group_id,"group_cover",$group_cover_id);
		endif;

		$pay_group = wpqa_options("pay_group");
		if ($pay_group == "on") {
			$_allow_to_group = (int)get_user_meta($user_id,"_allow_to_group",true);
			if ($_allow_to_group == "" || $_allow_to_group < 0) {
				$_allow_to_group = 0;
			}
			if ($_allow_to_group > 0) {
				$_allow_to_group--;
			}
			update_user_meta($user_id,"_allow_to_group",$_allow_to_group);
			do_action("wpqa_update_payment_status",$user_id,$group_id,"posts");
			if ($_allow_to_group > 0) {
				update_post_meta($group_id,'_paid_group','paid');
			}
		}
		
		do_action("wpqa_after_added_group",$group_id,$user_id);
		
		do_action("wpqa_finished_add_group",$group_id,$data,"add","group");
		
		/* Successful */
		return $group_id;
	}
}
/* Group */
function wpqa_group() {
	$out = '';
	$active_groups = wpqa_options("active_groups");
	if ($active_groups == "on") {
		$activate_login = wpqa_options("activate_login");
		$pay_group = wpqa_options("pay_group");
		$unlogged_pay = wpqa_options("unlogged_pay");
		$custom_permission = wpqa_options("custom_permission");
		$add_group = wpqa_options("add_group");
		$user_id = get_current_user_id();
		
		if (is_user_logged_in()) {
			$user_is_login = get_userdata($user_id);
			$user_login_group = wpqa_get_user_group($user_is_login);
			$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
			$confirm_email = wpqa_users_confirm_mail();
		}

		$out_payment = '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Please make a payment to be able to create a group.","wpqa").'</p></div>';
		$group_payment_style = wpqa_options("group_payment_style");
		if ($group_payment_style == "packages") {
			$out_payment .= wpqa_packages_payment($user_id,"group_packages","payment_type_group");
		}else {
			$payment_pages_target = wpqa_options("payment_pages_target");
			$payment_pages_target = ($payment_pages_target == "new_page"?"_blank":"_self");
			$out_payment .= '<a href="'.wpqa_checkout_link("add_group").'" class="button-default btn btn__primary" target="'.$payment_pages_target.'">'.esc_html__("Pay to create a group","wpqa").'</a>';
		}

		if (!is_user_logged_in() && $pay_group == "on" && $unlogged_pay != "on") {
			if ($activate_login != 'disabled') {
				$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i>'.esc_html__("You must login to create group.","wpqa").'</p></div>
				'.do_shortcode("[wpqa_login".(isset($a["popup"]) && $a["popup"] == "popup" && has_himer() || has_knowly()?" register='button'":"")."]");
			}else {
				$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to add group","wpqa").'.</p></div>';
			}
		}else {
			$points_user = (int)(is_user_logged_in()?get_user_meta($user_id,"points",true):0);
			$_allow_to_group = (int)(isset($user_id) && $user_id != ""?get_user_meta($user_id,"_allow_to_group",true):"");
			$protocol = is_ssl() ? 'https' : 'http';
			$return_url = wp_unslash($protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			if ($user_id > 0 && isset($_POST["process"]) && ($_POST["process"] == "group" || $_POST["process"] == "buy_groups")) {
				if (isset($_POST["points"]) && $_POST["points"] > 0) {
					$points_price = (int)$_POST["points"];
					$points_user = (int)(is_user_logged_in()?get_user_meta($user_id,"points",true):0);
					if ($points_price <= $points_user) {
						wpqa_add_points($user_id,$points_price,"-",($_POST["process"] == "buy_groups"?"buy_groups_points":"group_points"));
						/* Insert a new payment */
						$item_no = esc_html($_POST["process"]);
						$item_id = (isset($_POST["item_id"]) && $_POST["item_id"] != ""?esc_html($_POST["item_id"]):"");
						if ($_POST["process"] == "add_group") {
							$payment_description = esc_attr__("Create a new group","wpqa");
						}else {
							$packages_payment = wpqa_options("group_packages");
							if (isset($packages_payment) && is_array($packages_payment)) {
								$packages_payment = array_values($packages_payment);
								$found_key = trim(array_search($item_id,array_column($packages_payment,'package_posts')));
								if (isset($packages_payment[$found_key]) && is_array($packages_payment[$found_key]) && !empty($packages_payment[$found_key])) {
									$package_name = $packages_payment[$found_key]["package_name"];
								}
							}
							$payment_description = esc_attr__("Buy groups","wpqa").(isset($package_name) && $package_name != ""?" - ".$package_name:"");
						}
						$save_pay_by_points = wpqa_options("save_pay_by_points");
						if ($save_pay_by_points == "on") {
							$array = array (
								'item_no'    => $item_no,
								'item_name'  => $payment_description,
								'item_price' => 0,
								'first_name' => get_the_author_meta("first_name",$user_id),
								'last_name'  => get_the_author_meta("last_name",$user_id),
								'points'     => $points_price,
								'custom'     => 'wpqa_'.$item_no.'-'.$item_id,
							);
							if (isset($_POST["buy_package"])) {
								$array["payment_package"] = esc_html($_POST["buy_package"]);
							}
							wpqa_insert_statement($array,$user_id);
						}
						if ($_POST["process"] == "buy_groups") {
							$message = esc_html__("You have just bought to create groups by points.","wpqa");
						}else {
							$message = esc_html__("You have just bought to create a group by points.","wpqa");
						}
						wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.$message.'</p></div>','wpqa_session');
					}else {
						wpqa_not_enough_points();
						wp_safe_redirect(esc_url($return_url));
						die();
					}
				}
				/* Number allow to add group */
				if ($_allow_to_group == "" || $_allow_to_group < 0) {
					$_allow_to_group = 0;
				}
				if ($_POST["process"] == "buy_groups" && isset($_POST["buy_package"])) {
					do_action("wpqa_buy_groups",$user_id);
					$buy_package = (int)$_POST["buy_package"];
					$_allow_to_group = $_allow_to_group+$buy_package;
				}else {
					$_allow_to_group++;
				}
				update_user_meta($user_id,"_allow_to_group",$_allow_to_group);
				wp_safe_redirect(esc_url($return_url));
				die();
			}

			$out .= apply_filters("wpqa_before_buy_groups",$out,$user_id);
			$allow_to_add_group = apply_filters("wpqa_allow_to_add_group",true);

			if ($allow_to_add_group == true && !is_super_admin($user_id) && isset($_allow_to_group) && (int)$_allow_to_group < 1 && $pay_group == "on" && ($custom_permission != "on" || ($custom_permission == "on" && empty($roles["add_group_payment"])))) {
				$out .= $out_payment;
			}else {
				if (($custom_permission != "on" && ((isset($user_login_group) && $user_login_group == "wpqa_under_review") || (isset($user_login_group) && $user_login_group == "activation"))) || ($custom_permission == "on" && (is_user_logged_in() && !is_super_admin($user_id) && empty($roles["add_group"])) || (!is_user_logged_in() && $add_group != "on"))) {
					$out .= wpqa_paid_subscriptions_message(esc_html__("Sorry, you do not have permission to add group","wpqa").(!is_user_logged_in()?", ".esc_html__("You must login to add group","wpqa"):'').'.');
					if ($activate_login != 'disabled' && !is_user_logged_in()) {
						$out .= do_shortcode("[wpqa_login]");
					}
				}else if (!is_user_logged_in()) {
					if ($activate_login != 'disabled') {
						$out .= wpqa_login_message(esc_html__("You must login to add group.","wpqa"));
					}else {
						$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to add group","wpqa").'.</p></div>';
					}
				}else if (isset($confirm_email) && $confirm_email == "yes") {
					$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to add group","wpqa").'.</p></div>';
				}else {
					$out .= wpqa_add_edit_group("add");
				}
			}
		}
	}else {
		$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, this page is not available.","wpqa").'</p></div>';
	}
	return $out;
}
/* Edit group attrs */
function wpqa_edit_group_attr() {
	$out = '';
	if (!is_user_logged_in()) {
		$activate_login = wpqa_options("activate_login");
		if ($activate_login != 'disabled') {
			$out .= wpqa_login_message(esc_html__("You must login to edit group.","wpqa"),"warning");
		}else {
			$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to edit group.","wpqa").'</p></div>';
		}
	}else {
		$edit_group = wpqa_options("group_edit");
		$user_id = get_current_user_id();
		$is_super_admin = is_super_admin($user_id);
		if ($edit_group == "on" || $is_super_admin) {
			$get_group = (int)get_query_var(apply_filters('wpqa_edit_groups','edit_group'));
			$get_post_g = get_post($get_group);
			if (isset($get_post_g->ID) && $get_post_g->ID > 0 && $get_post_g->post_type == "group") {
				$group_moderators = get_post_meta($get_post_g->ID,"group_moderators",true);
				if ($user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($user_id,$group_moderators)) {
					$moderators = true;
				}
				if ($get_post_g->post_author > 0 || $is_super_admin || isset($moderators)) {
					$allow_to_edit_group = apply_filters("wpqa_allow_to_edit_group",true,$get_group);
					if ($allow_to_edit_group == true && (($get_post_g->post_author == $user_id && $user_id != 0 && $get_post_g->post_status == "publish") || $is_super_admin || isset($moderators))) {
						do_action("wpqa_group_tabs",$get_group,$user_id,$is_super_admin,$get_post_g->post_author,$group_moderators);
						$out .= wpqa_add_edit_group("edit");
					}else {
						$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry you can't edit this group.","wpqa").'</p></div>';
					}
				}else {
					$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry you can't edit this group.","wpqa").'</p></div>';
				}
			}else {
				$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry no group has been selected or not found.","wpqa").'</p></div>';
			}
		}else {
			$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to edit group.","wpqa").'</p></div>';
		}
	}
	return $out;
}
/* Edit group */
function wpqa_edit_group() {
	if (isset($_POST["form_type"]) && $_POST["form_type"] == "edit_group") :
		$return = wpqa_process_edit_groups($_POST);
		if (is_wp_error($return)) :
			return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
		else :
			$user_id = get_current_user_id();
			$get_post_g = get_post($return);
			$post_author = $get_post_g->post_author;
			$post_status = $get_post_g->post_status;
			$group_moderators = get_post_meta($return,"group_moderators",true);

			if ($post_status != "draft" || is_super_admin($user_id) || ($user_id != $post_author && $user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($user_id,$group_moderators))) {
				wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Edited successfully.","wpqa").'</p></div>','wpqa_session');
				wp_redirect(get_permalink($return));
			}else {
				wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Your group has been Edited successfully. The group is under review.","wpqa").'</p></div>','wpqa_session');
				wp_redirect(esc_url(home_url('/')));
			}
			exit;
		endif;
	endif;
}
add_filter('wpqa_edit_group','wpqa_edit_group');
/* Process edit groups */
function wpqa_process_edit_groups($data) {
	global $group_edit;
	@set_time_limit(0);
	$errors = new WP_Error();
	$group_edit = array();
	$form_type = (isset($data["form_type"]) && $data["form_type"] != ""?$data["form_type"]:"");
	if ($form_type == "edit_group") {
		if (isset($data['mobile'])) {
			$get_group = (int)$data['group_id'];
		}else {
			$get_group = (int)get_query_var(apply_filters('wpqa_edit_groups','edit_group'));
		}

		$fields = array(
			'title','group_privacy','group_invitation','group_allow_posts','group_approval','group_image','group_cover','group_comments','group_rules'
		);
		
		$fields = apply_filters("wpqa_edit_group_fields",$fields,"edit");
		
		foreach ($fields as $field) :
			if (isset($data[$field])) $group_edit[$field] = $data[$field]; else $group_edit[$field] = '';
		endforeach;
		
		/* Validate Required Fields */

		if (!isset($data['mobile']) && (!isset($data['wpqa_edit_group_nonce']) || !wp_verify_nonce($data['wpqa_edit_group_nonce'],'wpqa_edit_group_nonce'))) {
			$errors->add('nonce-error','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There is an error, Please reload the page and try again.","wpqa"));
		}

		$get_post_g = get_post($get_group);
		$user_id = get_current_user_id();
		$is_super_admin = is_super_admin($user_id);
		if (isset($get_group) && $get_group != 0 && $get_post_g && $get_post_g->post_type == "group") {
			$group_moderators = get_post_meta($get_group,"group_moderators",true);
			if ($user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($user_id,$group_moderators)) {
				$moderators = true;
			}
			if (($get_post_g->post_author == $user_id && $user_id != 0 && $get_post_g->post_status == "publish") || isset($moderators) || $is_super_admin) {
				// Yes, you can edit.
			}else {
				$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Sorry you can't edit this group.","wpqa"));
			}
		}else {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Sorry no group selected or not found.","wpqa"));
		}

		if ($is_super_admin || ($user_id > 0 && $user_id == $get_post_g->post_author)) {
			do_action("wpqa_edit_group_errors",$errors,$group_edit,"edit");
		}
		
		if (sizeof($errors->errors) > 0) return $errors;
		
		$group_id = $get_group;

		$group_approved = wpqa_options("group_approved");
		
		/* Edit group */
		
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
		if ($is_super_admin || ($user_id > 0 && (($user_id == $get_post_g->post_author) || (isset($edit_other_groups) && $edit_other_groups == true && isset($moderators))))) {
			$post_name = array();
			$change_group_url = wpqa_options("change_group_url");
			if ($change_group_url == "on") {
				$post_name = array('post_name' => sanitize_text_field($group_edit['title']));
			}
			
			$post_status = ($group_approved != "draft" || $is_super_admin || ($user_id != $get_post_g->post_author && isset($moderators))?"publish":"draft");
			$edit_data = array(
				'ID'           => (int)sanitize_text_field($group_id),
				'post_title'   => esc_html($group_edit['title']),
				'post_status'  => $post_status,
				'post_author'  => $get_post_g->post_author,
			);
			
			wp_update_post(array_merge($post_name,$edit_data));
			
			if ($group_edit['group_privacy'] && $group_edit['group_privacy'] != "") {
				update_post_meta($group_id,'group_privacy',esc_html($group_edit['group_privacy']));
			}else {
				delete_post_meta($group_id,'group_privacy');
			}
			
			if ($group_edit['group_invitation'] && $group_edit['group_invitation'] != "") {
				update_post_meta($group_id,'group_invitation',esc_html($group_edit['group_invitation']));
			}else {
				delete_post_meta($group_id,'group_invitation');
			}
			
			if ($group_edit['group_allow_posts'] && $group_edit['group_allow_posts'] != "") {
				update_post_meta($group_id,'group_allow_posts',esc_html($group_edit['group_allow_posts']));
			}else {
				delete_post_meta($group_id,'group_allow_posts');
			}
			
			if ($group_edit['group_approval'] && $group_edit['group_approval'] != "") {
				update_post_meta($group_id,'group_approval',esc_html($group_edit['group_approval']));
			}else {
				delete_post_meta($group_id,'group_approval');
			}
			
			if ($group_edit['group_comments'] && $group_edit['group_comments'] != "") {
				update_post_meta($group_id,'group_comments',esc_html($group_edit['group_comments']));
			}else {
				delete_post_meta($group_id,'group_comments');
			}
			
			/* Group photo */
			
			$group_image = '';

			require_once(ABSPATH . 'wp-admin/includes/image.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			
			if (isset($_FILES['group_image']) && !empty($_FILES['group_image']['name'])) :
				$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/webp","image/gif","image/tiff","image/ico");
				if (!isset($data['mobile']) && !in_array($_FILES['group_image']['type'],$types)) :
					$errors->add('upload-error',esc_html__("Attachment Error! Please upload image only.","wpqa"));
					return $errors;
				endif;
				
				$group_image = wp_handle_upload($_FILES['group_image'],array('test_form' => false),current_time('mysql'));
				
				if (isset($group_image['error'])) :
					$errors->add('upload-error',esc_html__("Attachment Error: ","wpqa") . $group_image['error']);
					return $errors;
				endif;
				
			endif;
			if ($group_image) :
				$group_image_data = array(
					'post_mime_type' => $group_image['type'],
					'post_title'     => preg_replace('/\.[^.]+$/','',basename($group_image['file'])),
					'post_content'   => '',
					'post_status'    => 'inherit',
					'post_author'    => $user_id
				);
				$group_image_id = wp_insert_attachment($group_image_data,$group_image['file'],$group_id);
				$group_image_metadata = wp_generate_attachment_metadata($group_image_id,$group_image['file']);
				wp_update_attachment_metadata($group_image_id, $group_image_metadata);
				update_post_meta($group_id,"group_image",$group_image_id);
			endif;
			
			/* Group cover */
			
			$group_cover = '';
			
			if (isset($_FILES['group_cover']) && !empty($_FILES['group_cover']['name'])) :
				$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/webp","image/gif","image/tiff","image/ico");
				if (!isset($data['mobile']) && !in_array($_FILES['group_cover']['type'],$types)) :
					$errors->add('upload-error',esc_html__("Attachment Error! Please upload image only.","wpqa"));
					return $errors;
				endif;
				
				$group_cover = wp_handle_upload($_FILES['group_cover'],array('test_form' => false),current_time('mysql'));
				
				if (isset($group_cover['error'])) :
					$errors->add('upload-error',esc_html__("Attachment Error: ","wpqa") . $group_cover['error']);
					return $errors;
				endif;
				
			endif;
			if ($group_cover) :
				$group_cover_data = array(
					'post_mime_type' => $group_cover['type'],
					'post_title'     => preg_replace('/\.[^.]+$/','',basename($group_cover['file'])),
					'post_content'   => '',
					'post_status'    => 'inherit',
					'post_author'    => $user_id
				);
				$group_cover_id = wp_insert_attachment($group_cover_data,$group_cover['file'],$group_id);
				$group_cover_metadata = wp_generate_attachment_metadata($group_cover_id,$group_cover['file']);
				wp_update_attachment_metadata($group_cover_id, $group_cover_metadata);
				update_post_meta($group_id,"group_cover",$group_cover_id);
			endif;
		}
		
		if ($group_edit['group_rules'] && $group_edit['group_rules'] != "") {
			update_post_meta($group_id,'group_rules',wpqa_esc_textarea($group_edit['group_rules']));
		}else {
			delete_post_meta($group_id,'group_rules');
		}

		do_action("wpqa_finished_edit_group",$group_id,$group_edit,"edit");
		
		/* Successful */
		return $group_id;
	}
}
/* Group errors */
add_action("wpqa_add_group_errors","wpqa_add_edit_group_errors",1,3);
add_action("wpqa_edit_group_errors","wpqa_add_edit_group_errors",1,3);
function wpqa_add_edit_group_errors($errors,$posted,$type) {
	$terms_active_group = wpqa_options("terms_active_group");
	if (empty($posted['title'])) {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (title).","wpqa"));
	}

	if (empty($posted['group_privacy'])) {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (group privacy).","wpqa"));
	}

	if (empty($posted['group_invitation'])) {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (group invitation).","wpqa"));
	}

	if (empty($posted['group_allow_posts'])) {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (group posts).","wpqa"));
	}

	if ($type == "add") {
		wpqa_check_captcha(wpqa_options("the_captcha_group"),"group",$posted,$errors);

		if ($terms_active_group == "on" && isset($posted['terms_active']) && $posted['terms_active'] != "on") {
			$errors->add('required-terms',esc_html__("There are required fields (Agree of the terms).","wpqa"));
		}
	}

	return $errors;
}?>