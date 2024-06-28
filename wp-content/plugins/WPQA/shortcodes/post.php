<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

ob_start();
/* Post menus */
add_action('admin_menu','wpqa_add_admin_post');
function wpqa_add_admin_post() {
	add_submenu_page('edit.php',esc_html__('Comments','wpqa'),esc_html__('Comments','wpqa'),'manage_options','edit-comments.php?comment_status=comments');
}
/* wpqa_add_post_attr */
function wpqa_add_post_attr($atts, $content = null) {
	$a = shortcode_atts( array(
	    'popup' => ''
	), $atts );
	$out = '';
	$activate_login = wpqa_options("activate_login");
	$add_post_no_register = wpqa_options("add_post_no_register");
	$add_post = wpqa_options("add_post");
	$custom_permission = wpqa_options("custom_permission");
	$pay_post = wpqa_options("pay_post");
	$unlogged_pay = wpqa_options("unlogged_pay");
	$user_id = get_current_user_id();
	if (is_user_logged_in()) {
		$user_is_login = get_userdata($user_id);
		$user_login_group = wpqa_get_user_group($user_is_login);
		$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
		$confirm_email = wpqa_users_confirm_mail();
	}
	$out_payment = '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Please make a payment to be able to add post.","wpqa").'</p></div>';
	$post_payment_style = wpqa_options("post_payment_style");
	if ($post_payment_style == "packages") {
		$out_payment .= wpqa_packages_payment($user_id,"post_packages","payment_type_post");
	}else {
		$payment_pages_target = wpqa_options("payment_pages_target");
		$payment_pages_target = ($payment_pages_target == "new_page"?"_blank":"_self");
		$out_payment .= '<a href="'.wpqa_checkout_link("add_post").'" class="button-default btn btn__primary" target="'.$payment_pages_target.'">'.esc_html__("Pay to add a post","wpqa").'</a>';
	}

	$out .= apply_filters("wpqa_before_add_post_conditions",$out,$out_payment);
	if (($custom_permission != "on" && ((isset($user_login_group) && $user_login_group == "wpqa_under_review") || (isset($user_login_group) && $user_login_group == "activation"))) || ($custom_permission == "on" && (is_user_logged_in() && !is_super_admin($user_id) && empty($roles["add_post"])) || (!is_user_logged_in() && $add_post != "on"))) {
		$out .= wpqa_paid_subscriptions_message(esc_html__("Sorry, you do not have permission to add post.","wpqa"));
		if ($activate_login != 'disabled' && !is_user_logged_in()) {
			$out .= do_shortcode("[wpqa_login".(isset($a["popup"]) && $a["popup"] == "popup" && (has_himer() || has_knowly())?" register='button'":"")."]");
		}
	}else if (!is_user_logged_in() && (($pay_post != "on" && $add_post_no_register != "on") || ($pay_post == "on" && $unlogged_pay != "on"))) {
		if ($activate_login != 'disabled') {
			$out = wpqa_login_message(esc_html__("You must login to add post.","wpqa"),"","",(isset($a["popup"]) && $a["popup"] == "popup" && (has_himer() || has_knowly())?" register='button'":""));
		}else {
			$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to add post.","wpqa").'</p></div>';
		}
	}else if (isset($confirm_email) && $confirm_email == "yes") {
		$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to add post.","wpqa").'</p></div>';
	}else {
		if (!is_user_logged_in() && $pay_post == "on" && $unlogged_pay != "on") {
			if ($activate_login != 'disabled') {
				$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i>'.esc_html__("You must login to add post.","wpqa").'</p></div>
				'.do_shortcode("[wpqa_login".(isset($a["popup"]) && $a["popup"] == "popup" && (has_himer() || has_knowly())?" register='button'":"")."]");
			}else {
				$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to add post.","wpqa").'</p></div>';
			}
		}else {
			$points_user = (int)(is_user_logged_in()?get_user_meta($user_id,"points",true):0);
			$_allow_to_post = (int)(isset($user_id) && $user_id != ""?get_user_meta($user_id,$user_id."_allow_to_post",true):"");
			$protocol = is_ssl() ? 'https' : 'http';
			$return_url = wp_unslash($protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			if ($user_id > 0 && isset($_POST["process"]) && ($_POST["process"] == "post" || $_POST["process"] == "buy_posts")) {
				if (isset($_POST["points"]) && $_POST["points"] > 0) {
					$points_price = (int)$_POST["points"];
					$points_user = (int)(is_user_logged_in()?get_user_meta($user_id,"points",true):0);
					if ($points_price <= $points_user) {
						wpqa_add_points($user_id,$points_price,"-",($_POST["process"] == "buy_posts"?"buy_posts_points":"post_points"));
						/* Insert a new payment */
						$item_no = esc_html($_POST["process"]);
						$item_id = (isset($_POST["item_id"]) && $_POST["item_id"] != ""?esc_html($_POST["item_id"]):"");
						if ($_POST["process"] == "add_post") {
							$payment_description = esc_attr__("Add a new post","wpqa");
						}else {
							$packages_payment = wpqa_options("post_packages");
							if (isset($packages_payment) && is_array($packages_payment)) {
								$packages_payment = array_values($packages_payment);
								$found_key = trim(array_search($item_id,array_column($packages_payment,'package_posts')));
								if (isset($packages_payment[$found_key]) && is_array($packages_payment[$found_key]) && !empty($packages_payment[$found_key])) {
									$package_name = $packages_payment[$found_key]["package_name"];
								}
							}
							$payment_description = esc_attr__("Buy posts","wpqa").(isset($package_name) && $package_name != ""?" - ".$package_name:"");
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
						if ($_POST["process"] == "buy_posts") {
							$message = esc_html__("You have just bought to add posts by points.","wpqa");
						}else {
							$message = esc_html__("You have just bought to add a post by points.","wpqa");
						}
						wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.$message.'</p></div>','wpqa_session');
					}else {
						wpqa_not_enough_points();
						wp_safe_redirect(esc_url($return_url));
						die();
					}
				}
				/* Number allow to add post */
				if ($_allow_to_post == "" || $_allow_to_post < 0) {
					$_allow_to_post = 0;
				}
				if ($_POST["process"] == "buy_posts" && isset($_POST["buy_package"])) {
					do_action("wpqa_buy_posts",$user_id);
					$buy_package = (int)$_POST["buy_package"];
					$_allow_to_post = $_allow_to_post+$buy_package;
					wpqa_update_sticky_numbers("post_packages",$user_id,$buy_package);
				}else {
					$_allow_to_post++;
				}
				update_user_meta($user_id,$user_id."_allow_to_post",$_allow_to_post);
				wp_safe_redirect(esc_url($return_url));
				die();
			}

			$out .= apply_filters("wpqa_before_buy_posts",$out,$user_id);
			$allow_to_add_post = apply_filters("wpqa_allow_to_add_post",true);

			if ($allow_to_add_post == true && !is_super_admin($user_id) && isset($_allow_to_post) && (int)$_allow_to_post < 1 && $pay_post == "on" && ($custom_permission != "on" || ($custom_permission == "on" && empty($roles["add_post_payment"])))) {
				$out .= $out_payment;
			}else {
				$out .= wpqa_add_edit_post("add",(isset($a["popup"]) && $a["popup"] == "popup"?"popup":false));
			}
		}
	}
	return $out;
}
/* wpqa_edit_post_attr */
function wpqa_edit_post_attr() {
	$out = '';
	if (!is_user_logged_in()) {
		$activate_login = wpqa_options("activate_login");
		if ($activate_login != 'disabled') {
			$out .= wpqa_login_message(esc_html__("You must login to edit post.","wpqa"));
		}else {
			$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to edit post.","wpqa").'</p></div>';
		}
	}else {
		$can_edit_post = wpqa_options("can_edit_post");
		$user_id = get_current_user_id();
		$get_post = (int)get_query_var(apply_filters('wpqa_edit_posts','edit_post'));
		$get_post_p = get_post($get_post);
		if (isset($get_post) && $get_post != 0 && $get_post_p && $get_post_p->post_type == "post") {
			$is_super_admin = is_super_admin($user_id);
			$moderator_categories = wpqa_user_moderator_categories($user_id,$get_post);
			$moderators_permissions = wpqa_user_moderator($user_id);
			$edit = ($is_super_admin || ((($user_id == $get_post_p->post_author && $get_post_p->post_author > 0)) && $can_edit_post == "on") || ($moderator_categories == true && isset($moderators_permissions['edit']) && $moderators_permissions['edit'] == "edit")?true:false);
			if ($edit == true || $is_super_admin) {
				if ($get_post_p->post_author != 0 || $is_super_admin) {
					if (($get_post_p->post_author == $user_id && $user_id != 0 && $get_post_p->post_status == "publish") || $is_super_admin) {
						$out .= wpqa_add_edit_post("edit");
					}else {
						$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you can't edit this post.","wpqa").'</p></div>';
					}
				}else {
					$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you can't edit this post.","wpqa").'</p></div>';
				}
			}else {
				$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to edit post.","wpqa").'</p></div>';
			}
		}else {
			$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, no post selected or not found.","wpqa").'</p></div>';
		}
	}
	return $out;
}
/* wpqa_add_edit_post */
function wpqa_add_edit_post($type,$popup = false) {
	global $post_add,$post_edit;
	$add_post_no_register = wpqa_options("add_post_no_register");
	$add_post_items = wpqa_options("add_post_items");
	$tags_post = (isset($add_post_items["tags_post"]["value"]) && $add_post_items["tags_post"]["value"] == "tags_post"?"on":"");
	$featured_image = (isset($add_post_items["featured_image"]["value"]) && $add_post_items["featured_image"]["value"] == "featured_image"?"on":"");
	$content_post = (isset($add_post_items["content_post"]["value"]) && $add_post_items["content_post"]["value"] == "content_post"?"on":"");
	$editor_post_details = wpqa_options("editor_post_details");
	$rand = rand(1,1000);
	
	if ($type == "edit") {
		$get_post = (int)get_query_var(apply_filters('wpqa_edit_posts','edit_post'));
		$get_post_p = get_post($get_post);
		$p_tag = "";
		if ($terms = wp_get_object_terms( $get_post, 'post_tag' )) :
			$terms_array = array();
			foreach ($terms as $term) :
				$terms_array[] = $term->name;
				$p_tag = implode(' , ', $terms_array);
			endforeach;
		endif;
		
		$category = wp_get_post_terms($get_post,'category',array("fields" => "ids"));
		if (isset($category) && is_array($category) && isset($category[0])) {
			$category = $category[0];
		}
	}
	
	$out = apply_filters('wpqa_add_edit_post_before_form',false,$type,$post_add,$post_edit,(isset($get_post)?$get_post:0)).
	'<form class="form-post wpqa_form" method="post" enctype="multipart/form-data">'.(isset($_POST["form_type"]) && $_POST["form_type"] == $type."_post"?apply_filters('wpqa_'.$type.'_post',$type):"").'
		<div class="form-inputs clearfix">';
			if ($type == "add" && !is_user_logged_in() && $add_post_no_register == "on") {
				$out .= '<p>
					<label for="post-username-'.$rand.'">'.esc_html__("Username","wpqa").'<span class="required">*</span></label>
					<input name="username" id="post-username-'.$rand.'" class="form-control the-username" type="text" value="'.(isset($post_add['username'])?$post_add['username']:'').'">
					<i class="icon-user"></i>
					<span class="form-description">'.esc_html__("Please type your username.","wpqa").'</span>
				</p>
				
				<p>
					<label for="post-email-'.$rand.'">'.esc_html__("E-Mail","wpqa").'<span class="required">*</span></label>
					<input name="email" id="post-email-'.$rand.'" class="form-control the-email" type="text" value="'.(isset($post_add['email'])?$post_add['email']:'').'">
					<i class="icon-mail"></i>
					<span class="form-description">'.esc_html__("Please type your E-Mail.","wpqa").'</span>
				</p>';
			}
			
			$out .= '<p>
				<label for="post-title-'.$rand.'">'.esc_html__("Post Title","wpqa").'<span class="required">*</span></label>
				<input class="form-control" type="text" name="title" id="post-title-'.$rand.'" value="'.($type == "add" && isset($post_add['title'])?wp_unslash(esc_html($post_add['title'])):($type == "edit"?(isset($post_edit['title'])?wp_unslash(esc_html($post_edit['title'])):esc_html($get_post_p->post_title)):"")).'">
				<i class="icon-chat"></i>
				<span class="form-description">'.esc_html__("Please choose an appropriate title for the post.","wpqa").'</span>
			</p>';
			
			$out .= '<p>
				<label for="post-category-'.$rand.'">'.esc_html__("Category","wpqa").'<span class="required">*</span></label>
				'.wpqa_select_categories($rand,($type == "add" && isset($post_add['category'])?$post_add['category']:($type == "edit"?(isset($post_edit['category'])?$post_edit['category']:$category):(isset($_GET["category"])?(int)$_GET["category"]:(is_category()?(int)get_query_var('wpqa_term_id'):"")))),null,($type == "edit"?$get_post:""),'category').'
				<i class="icon-folder"></i>
				<span class="form-description">'.esc_html__("Please choose the appropriate section so your post can be easily searched.","wpqa").'</span>
			</p>'.apply_filters('wpqa_add_edit_post_after_category',false,$type,$post_add,$post_edit,(isset($get_post)?$get_post:0));
			
			if (isset($add_post_items) && is_array($add_post_items)) {
				foreach ($add_post_items as $sort_key => $sort_value) {
					$out = apply_filters("wpqa_post_sort",$out,"add_post_items",$add_post_items,$sort_key,$sort_value,$type,$post_add,$post_edit,(isset($get_post)?$get_post:0));
					if ($sort_key == "tags_post" && isset($add_post_items["tags_post"]["value"]) && $add_post_items["tags_post"]["value"] == "tags_post") {
						$out .= '<p class="wpqa_tag">
							<label for="post_tag-'.$rand.'">'.esc_html__("Tags","wpqa").'</label>
							<input type="text" class="form-control input post_tag" name="post_tag" id="post_tag-'.$rand.'" value="'.($type == "add" && isset($post_add['post_tag'])?wp_unslash(sanitize_text_field($post_add['post_tag'])):($type == "edit"?(isset($post_edit['post_tag'])?wp_unslash(sanitize_text_field($post_edit['post_tag'])):sanitize_text_field($p_tag)):"")).'" data-seperator=",">
							<span class="form-description">'.esc_html__("Please choose suitable Keywords Ex: ","wpqa").'<span class="color">'.esc_html__("post, video","wpqa").'</span>.</span>
						</p>'.apply_filters('wpqa_add_edit_post_after_tags',false,$type,$post_add,$post_edit,(isset($get_post)?$get_post:0));
					}else if ($sort_key == "featured_image" && isset($add_post_items["featured_image"]["value"]) && $add_post_items["featured_image"]["value"] == "featured_image") {
						if ($type == "edit") {
							$_thumbnail_id = get_post_meta($get_post,"_thumbnail_id",true);
							if ($_thumbnail_id != "") {
								$out .= '<div class="clearfix"></div>
								<div class="wpqa-delete-image d-flex align-items-center mb-4">
									<span class="wpqa-delete-image-span mr-4">'.wpqa_get_aq_resize_img(250,250,"",$_thumbnail_id,"no","").'</span>
									<div class="clearfix"></div>
									<div class="button-default wpqa-remove-image btn btn__danger btn__small__width" data-name="_thumbnail_id" data-type="post_meta" data-id="'.$get_post.'" data-image="'.$_thumbnail_id.'" data-nonce="'.wp_create_nonce("wpqa_remove_image").'">'.esc_html__("Delete","wpqa").'</div>
									<div class="loader_2 loader_4"></div>
								</div>';
							}
						}
						$out .= '<label for="attachment-'.$rand.'">'.apply_filters("wpqa_filter_featured_image",esc_html__("Featured image","wpqa")).'</label>
						<div class="fileinputs">
							<input type="file" class="file" name="attachment" id="attachment-'.$rand.'">
							<div class="fakefile">
								<button type="button">'.esc_html__("Select file","wpqa").'</button>
								<span>'.esc_html__("Browse","wpqa").'</span>
							</div>
							<i class="icon-camera"></i>
						</div>'.apply_filters('wpqa_add_edit_post_after_featured_image',false,$type,$post_add,$post_edit,(isset($get_post)?$get_post:0));
					}else if ($sort_key == "content_post" && isset($add_post_items["content_post"]["value"]) && $add_post_items["content_post"]["value"] == "content_post") {
						$out .= '<div class="wpqa_textarea'.($editor_post_details == "on"?"":" wpqa_textarea_p").'">
							<label for="post-details-'.$rand.'">'.apply_filters("wpqa_details_post_language",esc_html__("Details","wpqa")).($content_post == "on"?'<span class="required">*</span>':'').'</label>';
							$post_value = ($type == "add" && isset($post_add['comment'])?wpqa_esc_textarea($post_add['comment']):($type == "edit"?(isset($post_edit['comment'])?wpqa_esc_textarea($post_edit['comment']):$get_post_p->post_content):""));
							if ($editor_post_details == "on") {
								$settings = array("textarea_name" => "comment","media_buttons" => true,"textarea_rows" => 10);
								$settings = apply_filters('wpqa_post_editor_setting',$settings);
								ob_start();
								wp_editor($post_value,"post-details-".$rand,$settings);
								$editor_contents = ob_get_clean();
								
								$out .= '<div class="the-details the-textarea">'.$editor_contents.'</div>';
							}else {
								$out .= '<textarea name="comment" id="post-details-'.$rand.'" class="form-control the-textarea" aria-required="true" cols="58" rows="8">'.$post_value.'</textarea>
								<i class="icon-pencil"></i>';
							}
						$out .= '</div>';
					}else if ($type == "add" && $sort_key == "terms_active" && isset($add_post_items["terms_active"]["value"]) && $add_post_items["terms_active"]["value"] == "terms_active") {
						$terms_checked_post = wpqa_options("terms_checked_post");
						if ((isset($post_add['agree_terms']) && $post_add['agree_terms'] == "on") || ($terms_checked_post == "on" && empty($post_add))) {
							$active_terms = true;
						}
						$terms_link_post = wpqa_options("terms_link_post");
						$terms_page_post = wpqa_options('terms_page_post');
						$terms_active_target_post = wpqa_options('terms_active_target_post');
						$privacy_policy_post = wpqa_options('privacy_policy_post');
						$privacy_active_target_post = wpqa_options('privacy_active_target_post');
						$privacy_page_post = wpqa_options('privacy_page_post');
						$privacy_link_post = wpqa_options('privacy_link_post');
						$out .= '<p class="wpqa_checkbox_p">
							<label for="agree_terms-'.$rand.'">
								<span class="wpqa_checkbox"><input type="checkbox" id="agree_terms-'.$rand.'" name="agree_terms" value="on" '.(isset($active_terms)?"checked='checked'":"").'></span>
								<span class="wpqa_checkbox_span">'.sprintf(esc_html__('By posting, you agreed to the %1$s Terms of Service %2$s %3$s.','wpqa'),'<a target="'.($terms_active_target_post == "same_page"?"_self":"_blank").'" href="'.esc_url(isset($terms_link_post) && $terms_link_post != ""?$terms_link_post:(isset($terms_page_post) && $terms_page_post != ""?get_page_link($terms_page_post):"#")).'">','</a>',($privacy_policy_post == "on"?" ".sprintf(esc_html__('and %1$s Privacy Policy %2$s','wpqa'),'<a target="'.($privacy_active_target_post == "same_page"?"_self":"_blank").'" href="'.esc_url(isset($privacy_link_post) && $privacy_link_post != ""?$privacy_link_post:(isset($privacy_page_post) && $privacy_page_post != ""?get_page_link($privacy_page_post):"#")).'">','</a>'):"")).'<span class="required">*</span></span>
							</label>
						</p>';
					}
				}
			}

			$out .= apply_filters('wpqa_add_edit_post_after_details',false,$type,$post_add,$post_edit,(isset($get_post)?$get_post:0));
			
			if ($type == "add") {
				$out .= '<div class="form-inputs clearfix">
					'.wpqa_add_captcha(wpqa_options("the_captcha_post"),"post",$rand).'
				</div>';
			}
		
		$out .= '</div>
		
		<p class="form-submit mb-0">';
			if ($type == "edit") {
				$out .= '<input type="hidden" name="ID" value="'.$get_post.'">';
			}
			if ($popup == "popup") {
				$out .= '<input type="hidden" name="post_popup" value="popup">';
			}
			if (isset($_GET["page"]) && $_GET["page"] == "pending") {
				$out .= '<input type="hidden" name="pending" value="post">';
			}
			$out .= '<input type="hidden" name="form_type" value="'.$type.'_post">
			<input type="hidden" name="wpqa_'.$type.'_post_nonce" value="'.wp_create_nonce("wpqa_".$type."_post_nonce").'">
			'.(wpqa_input_button() == "button"?'<button type="submit" class="btn btn__primary btn__block btn__large__height button-default button-hide-click">'.($type == "add"?esc_html__("Publish Your Post","wpqa"):esc_html__("Submit","wpqa")).'</button>':'<input type="submit" value="'.($type == "add"?esc_html__("Publish Your Post","wpqa"):esc_html__("Submit","wpqa")).'" class="button-default button-hide-click">').'
			<span class="load_span"><span class="loader_2"></span></span>
		</p>
	
	</form>';
	return $out;
}
/* wpqa_add_post */
function wpqa_add_post($type) {
	if (isset($_POST["form_type"]) && $_POST["form_type"] == "add_post") :
		$return = wpqa_process_new_posts($_POST);
		if (is_wp_error($return)) :
   			return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
   		else :
   			$get_post = get_post($return);
   			$post_author = $get_post->post_author;
   			$post_type = $get_post->post_type;
   			if ($post_type == "post") {
   				$user_id = get_current_user_id();
   				if (is_user_logged_in()) {
   					$post_publish = wpqa_options("post_publish");
   				}else {
   					$post_publish = wpqa_options("post_publish_unlogged");
   				}
				$approved_posts = wpqa_options("approved_posts");
				$post_status = "publish";
				if ($post_publish == "draft" && !is_super_admin($user_id)) {
					$post_status = "draft";
					if ($approved_posts == "on") {
						$posts_count = wpqa_count_posts_meta("post",$user_id);
						if ($posts_count > 0) {
							$post_status = "publish";
						}
					}
				}
	   			$custom_permission = wpqa_options("custom_permission");
	   			if ($custom_permission == "on" && is_user_logged_in() && !is_super_admin($user_id)) {
					$user_is_login = get_userdata($user_id);
					$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
					$post_status = (isset($roles["approve_post"]) && $roles["approve_post"] == 1?"publish":"draft");
	   			}
				
				if ($post_status == "draft") {
					$send_email_draft_posts = wpqa_options("send_email_draft_posts");
					if ($send_email_draft_posts == "on") {
						$send_text = wpqa_send_mail(
							array(
								'content' => wpqa_options("email_draft_posts"),
								'post_id' => $return,
							)
						);
						$email_title = wpqa_options("title_new_draft_posts");
						$email_title = ($email_title != ""?$email_title:esc_html__("New post for review","wpqa"));
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
					wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Your post was successfully added, It's under review.","wpqa").'</p></div>','wpqa_session');
					if ($user_id > 0) {
						wpqa_notifications_activities($user_id,"","","","","approved_post","activities");
					}
					wp_redirect(esc_url(home_url('/')));
				}else {
					if ($user_id > 0) {
						wpqa_notifications_activities($user_id,"","",$return,"","add_post","activities");
					}
					$not_user = ($post_author > 0?$post_author:0);
					$the_author = 0;
					if ($post_author == 0) {
						$the_author = get_post_meta($return,'post_username',true);
					}
					wpqa_post_publish($get_post,$not_user);
					wpqa_notifications_add_post($return,$the_author,$not_user,$user_id);
					wpqa_post_after_approved($get_post,$not_user);
					$way_sending_notifications = wpqa_options("way_sending_notifications_posts");
					$schedules_time_notification = wpqa_options("schedules_time_notification_posts");
					if ($way_sending_notifications == "cronjob" && $schedules_time_notification != "") {
						update_post_meta($return,'wpqa_post_scheduled_email','yes');
						update_post_meta($return,'wpqa_post_scheduled_notification','yes');
					}
					wpqa_count_posts($post_type,$post_author,"+");
					wp_redirect(get_permalink($return));
				}
			}
			exit;
   		endif;
	endif;
}
add_filter('wpqa_add_post','wpqa_add_post');
/* wpqa_process_new_posts */
function wpqa_process_new_posts($data) {
	global $post_add;
	@set_time_limit(0);
	$errors = new WP_Error();
	$post_add = array();
	$user_id = get_current_user_id();
	$form_type = (isset($data["form_type"]) && $data["form_type"] != ""?$data["form_type"]:"");
	if ($form_type == "add_post") {
		$pay_post = wpqa_options("pay_post");
		$add_post_no_register = wpqa_options("add_post_no_register");
		$custom_permission = wpqa_options("custom_permission");
		$add_post = wpqa_options("add_post");
		if (is_user_logged_in()) {
			$user_is_login = get_userdata($user_id);
			$user_login_group = wpqa_get_user_group($user_is_login);
			$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
		}
		
		$fields = array(
			'title','comment','category','post_tag','wpqa_captcha','username','email','agree_terms'
		);

		$fields = apply_filters('wpqa_add_post_fields',$fields,"add");
		
		foreach ($fields as $field) :
			if (isset($data[$field])) $post_add[$field] = $data[$field]; else $post_add[$field] = '';
		endforeach;

		if (!isset($data['mobile']) && (!isset($data['wpqa_add_post_nonce']) || !wp_verify_nonce($data['wpqa_add_post_nonce'],'wpqa_add_post_nonce'))) {
			$errors->add('nonce-error','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There is an error, Please reload the page and try again.","wpqa"));
		}
		
		if (($custom_permission != "on" && ((isset($user_login_group) && $user_login_group == "wpqa_under_review") || (isset($user_login_group) && $user_login_group == "activation"))) || ($custom_permission == "on" && (is_user_logged_in() && !is_super_admin($user_id) && empty($roles["add_post"])) || (!is_user_logged_in() && $add_post != "on"))) {
			$errors->add('required','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Sorry, you do not have permission to add post.","wpqa"));
			if (!is_user_logged_in()) {
				$errors->add('required','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("You must login to add post.","wpqa"));
			}
		}else if (!is_user_logged_in() && $add_post_no_register != "on") {
			$errors->add('required','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("You must login to add post.","wpqa"));
		}else {
			if (!is_user_logged_in() && $pay_post == "on") {
				$errors->add('required','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("You must login to add post.","wpqa"));
			}else {
				if (!is_super_admin($user_id) && isset($_allow_to_ask) && (int)$_allow_to_ask < 1 && $pay_post == "on" && ($custom_permission != "on" || ($custom_permission == "on" && empty($roles["add_post_payment"])))) {
					$errors->add('required','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("You need to pay first.","wpqa"));
				}
			}
		}

		if (!is_user_logged_in() && $add_post_no_register == "on" && $user_id == 0) {
			if (empty($post_add['username'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (username).","wpqa"));
			if (empty($post_add['email'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (email).","wpqa"));
			if (!is_email(trim($post_add['email']))) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Please write correctly email.","wpqa"));
		}
		
		/* Validate Required Fields */
		$add_post_items = wpqa_options("add_post_items");
		do_action('wpqa_add_post_errors',$errors,$post_add,"add",$add_post_items,$user_id);
		
		/* Featured image */
	
		$attachment = '';
	
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
			
		if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
			$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/webp","image/gif","image/tiff","image/ico");
			if (!isset($data['mobile']) && !in_array($_FILES['attachment']['type'],$types)) :
				$errors->add('upload-error',esc_html__("Attachment Error! Please upload image only.","wpqa"));
				return $errors;
			endif;

			$attachment = wp_handle_upload($_FILES['attachment'],array('test_form' => false),current_time('mysql'));

			if (isset($attachment['error'])) :
				$errors->add('upload-error',esc_html__("Attachment Error: ","wpqa") . $attachment['error']);
				return $errors;
			endif;
			
		endif;
		
		if (sizeof($errors->errors) > 0) return $errors;
		
		/* Create post */

		if (is_user_logged_in()) {
			$post_publish = wpqa_options("post_publish");
		}else {
			$post_publish = wpqa_options("post_publish_unlogged");
		}
		$approved_posts = wpqa_options("approved_posts");
		$post_status = "publish";
		if ($post_publish == "draft" && !is_super_admin($user_id)) {
			$post_status = "draft";
			if ($approved_posts == "on") {
				$posts_count = wpqa_count_posts_meta("post",$user_id);
				if ($posts_count > 0) {
					$post_status = "publish";
				}
			}
		}
		if ($custom_permission == "on" && is_user_logged_in() && !is_super_admin($user_id)) {
			$post_status = (isset($roles["approve_post"]) && $roles["approve_post"] == 1?"publish":"draft");
		}
		$editor_post_details = wpqa_options("editor_post_details");

		$data_post = array(
			'post_content' => ($editor_post_details == "on"?wpqa_esc_textarea($post_add['comment']):wpqa_esc_textarea($post_add['comment'])),
			'post_title'   => esc_html($post_add['title']),
			'post_status'  => $post_status,
			'post_author'  => (!is_user_logged_in() && $add_post_no_register == "on"?0:$user_id),
			'post_type'    => 'post',
		);
			
		$post_id = wp_insert_post($data_post);
			
		if ($post_id == 0 || is_wp_error($post_id)) wp_die(esc_html__("Error in post.","wpqa"));
		
		$terms = array();
		if ($post_add['category']) $terms[] = get_term_by('id',(is_array($post_add['category'])?end($post_add['category']):$post_add['category']),'category')->slug;
		if (sizeof($terms) > 0) wp_set_object_terms($post_id,$terms,'category');
		
		/* Featured image */
		
		if (isset($attachment['type']) && isset($attachment['file'])) :
			$attachment_data = array(
				'post_mime_type' => $attachment['type'],
				'post_title'	 => preg_replace('/\.[^.]+$/','',basename($attachment['file'])),
				'post_content'   => '',
				'post_status'	=> 'inherit',
				'post_author'	=> (!is_user_logged_in() && $add_post_no_register == "on"?0:$user_id)
			);
			$attachment_id = wp_insert_attachment($attachment_data,$attachment['file'],$post_id);
			$attachment_metadata = wp_generate_attachment_metadata($attachment_id,$attachment['file']);
			wp_update_attachment_metadata($attachment_id, $attachment_metadata);
			$set_post_thumbnail = set_post_thumbnail($post_id,$attachment_id);
			if (!$set_post_thumbnail) {
				add_post_meta($post_id,'added_file',$attachment_id,true);
			}
		endif;
		
		/* Tags */
		
		if (isset($post_add['post_tag'])) :
			$tags = explode(',',trim(stripslashes($post_add['post_tag'])));
			$tags = array_map('strtolower',$tags);
			$tags = array_map('trim',$tags);
			wp_set_object_terms($post_id,(sizeof($tags) > 0?$tags:array()),'post_tag');
		endif;
		
		if (!is_user_logged_in() && $add_post_no_register == "on" && $user_id == 0) {
			$post_username = sanitize_text_field($post_add['username']);
			$post_email = sanitize_text_field($post_add['email']);
			update_post_meta($post_id,'post_username',$post_username);
			update_post_meta($post_id,'post_email',$post_email);
		}else {
			$pay_post = wpqa_options("pay_post");
			if ($pay_post == "on") {
				$_allow_to_post = (int)get_user_meta($user_id,$user_id."_allow_to_post",true);
				if ($_allow_to_post == "" || $_allow_to_post < 0) {
					$_allow_to_post = 0;
				}
				if ($_allow_to_post > 0) {
					$_allow_to_post--;
				}
				update_user_meta($user_id,$user_id."_allow_to_post",$_allow_to_post);
				do_action("wpqa_update_payment_status",$user_id,$post_id,"post");
				if ($_allow_to_post > 0) {
					update_post_meta($post_id,'_paid_post','paid');
				}
			}
		}

		if (is_user_logged_in()) {
			$_allow_to_sticky_post = (int)get_user_meta($user_id,$user_id."_allow_to_sticky_post",true);
			$_sticky_numbers = get_user_meta($user_id,$user_id."_sticky_numbers_post",true);
			if ($_allow_to_sticky_post > 0 && is_array($_sticky_numbers) && !empty($_sticky_numbers)) {
				$_allow_to_sticky_post = (int)get_user_meta($user_id,$user_id."_allow_to_sticky_post",true);
				if ($_allow_to_sticky_post == "" || $_allow_to_sticky_post < 0) {
					$_allow_to_sticky_post = 0;
				}
				if ($_allow_to_sticky_post > 0) {
					$_allow_to_sticky_post--;
				}
				update_user_meta($user_id,$user_id."_allow_to_sticky_post",$_allow_to_sticky_post);
				do_action("wpqa_update_payment_status",$user_id,$post_id,"post");
				$k = 0;
				foreach ($_sticky_numbers as $key => $value) {$k++;
					if ($k == 1 && isset($value["numbers"]) && $value["numbers"] > 0) {
						$days_sticky = $value["days"];
						$_sticky_numbers[$key]["numbers"] = $value["numbers"]-1;
						if ($_sticky_numbers[$key]["numbers"] <= 0) {
							unset($_sticky_numbers[$key]);
						}
					}
				}
				update_user_meta($user_id,$user_id."_sticky_numbers_post",$_sticky_numbers);
				if (isset($days_sticky) && $days_sticky > 0) {
					update_post_meta($post_id,"paid_post_with_sticky",true);
					update_post_meta($post_id,"start_sticky_time",strtotime(date("Y-m-d")));
					update_post_meta($post_id,"end_sticky_time",strtotime(date("Y-m-d",strtotime(date("Y-m-d")." +$days_sticky days"))));
				}
			}
		}
		
		$sticky_posts = get_option("sticky_posts");
		if (isset($days_sticky) && $days_sticky > 0) {
			update_post_meta($post_id,'sticky',1);
			if (is_array($sticky_posts)) {
				if (!in_array($post_id,$sticky_posts)) {
					$array_merge = array_merge($sticky_posts,array($post_id));
					update_option("sticky_posts",$array_merge);
				}
			}else {
				update_option("sticky_posts",array($post_id));
			}
		}
		
		do_action('wpqa_finished_add_post',$post_id,$post_add,"add","post");
		
		/* Successful */
		return $post_id;
	}
}
/* wpqa_edit_post */
function wpqa_edit_post($type) {
	if (isset($_POST["form_type"]) && $_POST["form_type"] == "edit_post") :
		$return = wpqa_process_edit_posts($_POST);
		if (is_wp_error($return)) :
   			return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
   		else :
   			$user_id = get_current_user_id();
   			$get_post = get_post($return);
   			$post_status = $get_post->post_status;
   			$moderators_permissions = wpqa_user_moderator($user_id);

			if ($post_status != "draft" || ($post_status == "draft" && isset($moderators_permissions['edit']) && $moderators_permissions['edit'] == "edit") || is_super_admin($user_id)) {
				wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Edited successfully.","wpqa").'</p></div>','wpqa_session');
				if ($post_status == "draft" && isset($moderators_permissions['edit']) && $moderators_permissions['edit'] == "edit") {
					wp_redirect(wpqa_pending_posts_permalink());
				}else {
					wp_redirect(get_permalink($return));
				}
			}else {
   				wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Your post was successfully added, It's under review.","wpqa").'</p></div>','wpqa_session');
   				wp_redirect(esc_url(home_url('/')));
   			}
			exit;
   		endif;
	endif;
}
add_filter('wpqa_edit_post','wpqa_edit_post');
/* wpqa_process_edit_posts */
function wpqa_process_edit_posts($data) {
	global $post_edit;
	@set_time_limit(0);
	$errors = new WP_Error();
	$post_edit = array();
	$user_id = get_current_user_id();
	
	if (isset($data['mobile'])) {
		$get_post = (int)$data['post_id'];
	}else {
		$get_post = (int)get_query_var(apply_filters('wpqa_edit_posts','edit_post'));
	}

	$fields = array(
		'title','comment','category','attachment','post_tag','pending'
	);


	$fields = apply_filters('wpqa_edit_post_fields',$fields,"edit");
	
	foreach ($fields as $field) :
		if (isset($data[$field])) $post_edit[$field] = $data[$field]; else $post_edit[$field] = '';
	endforeach;
	
	/* Validate Required Fields */

	if (!isset($data['mobile']) && (!isset($data['wpqa_edit_post_nonce']) || !wp_verify_nonce($data['wpqa_edit_post_nonce'],'wpqa_edit_post_nonce'))) {
		$errors->add('nonce-error','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There is an error, Please reload the page and try again.","wpqa"));
	}
	$get_post_p = get_post($get_post);
	
	if (isset($get_post) && $get_post != 0 && $get_post_p && $get_post_p->post_type == "post") {
		if (($get_post_p->post_author != $user_id || $get_post_p->post_status != "publish") && !is_super_admin($user_id)) {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Sorry, you can't edit this post.","wpqa"));
		}
	}else {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Sorry post not selected or not found.","wpqa"));
	}
	$add_post_items = wpqa_options("add_post_items");
	do_action('wpqa_edit_post_errors',$errors,$post_edit,"edit",$add_post_items,$user_id);
		
	/* Featured image */
	
	$attachment = '';

	require_once(ABSPATH . 'wp-admin/includes/image.php');
	require_once(ABSPATH . 'wp-admin/includes/file.php');
		
	if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
		$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/webp","image/gif","image/tiff","image/ico");
		if (!isset($data['mobile']) && !in_array($_FILES['attachment']['type'],$types)) :
			$errors->add('upload-error',esc_html__("Attachment Error! Please upload image only.","wpqa"));
			return $errors;
		endif;

		$attachment = wp_handle_upload($_FILES['attachment'],array('test_form' => false),current_time('mysql'));

		if (isset($attachment['error'])) :
			$errors->add('upload-error',esc_html__("Attachment Error: ","wpqa") . $attachment['error']);
			return $errors;
		endif;
		
	endif;

	if (sizeof($errors->errors) > 0) return $errors;
	
	$post_id = $get_post;
	
	$post_approved = wpqa_options("post_approved");
	
	/* Edit post */
	
	$post_name = array();
	$change_post_url = wpqa_options("change_post_url");
	if ($change_post_url == "on") {
		$post_name = array('post_name' => sanitize_text_field($post_edit['title']));
	}
	$editor_post_details = wpqa_options("editor_post_details");
	
	$post_status = ($post_approved != "draft" || is_super_admin($user_id)?"publish":"draft");
	$data_post = array(
		'ID'           => sanitize_text_field($post_id),
		'post_content' => ($editor_post_details == "on"?wpqa_esc_textarea($post_edit['comment']):wpqa_esc_textarea($post_edit['comment'])),
		'post_title'   => esc_html($post_edit['title']),
		'post_status'  => $post_status,
	);
	
	wp_update_post(array_merge($post_name,$data_post));

	wpqa_after_edit_post_approved($get_post_p,$get_post_p->post_author);
	
	$terms = array();
	if ($post_edit['category']) $terms[] = get_term_by('id',$post_edit['category'],'category')->slug;
	if (sizeof($terms) > 0) wp_set_object_terms($post_id,$terms,'category');
		
	/* Featured image */
	
	if (isset($attachment['type']) && isset($attachment['file'])) :
		$attachment_data = array(
			'post_mime_type' => $attachment['type'],
			'post_title'     => preg_replace('/\.[^.]+$/','',basename($attachment['file'])),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_author'    => (!is_user_logged_in() && $add_post_no_register == "on"?0:$user_id)
		);
		$attachment_id = wp_insert_attachment($attachment_data,$attachment['file'],$post_id);
		$attachment_metadata = wp_generate_attachment_metadata($attachment_id,$attachment['file']);
		wp_update_attachment_metadata($attachment_id, $attachment_metadata);
		set_post_thumbnail($post_id,$attachment_id);
	endif;
	
	/* Tags */
	
	if (isset($post_edit['post_tag'])) :
		$tags = explode(',',trim(stripslashes($post_edit['post_tag'])));
		$tags = array_map('strtolower',$tags);
		$tags = array_map('trim',$tags);
		wp_set_object_terms($post_id,(sizeof($tags) > 0?$tags:array()),'post_tag');
	endif;

	if ($post_edit['pending'] == "post") {
		$point_add_post = (int)wpqa_options("point_add_post");
		$active_points = wpqa_options("active_points");
		if ($get_post_p->post_author > 0 && $point_add_post > 0 && $active_points == "on") {
			$get_points_before = get_post_meta($post_id,"get_points_before",true);
			if ($get_points_before != "yes") {
				update_post_meta($post_id,"get_points_before","yes");
				wpqa_add_points($get_post_p->post_author,$point_add_post,"+","add_post",$post_id);
			}
		}
	}

	do_action('wpqa_finished_edit_post',$post_id,$post_edit,"edit");
	
	/* Successful */
	return $post_id;
}
/* Post errors */
add_action("wpqa_add_post_errors","wpqa_add_edit_post_errors",1,5);
add_action("wpqa_edit_post_errors","wpqa_add_edit_post_errors",1,5);
function wpqa_add_edit_post_errors($errors,$posted,$type,$add_post_items,$user_id) {
	$content_post = (isset($add_post_items["content_post"]["value"]) && $add_post_items["content_post"]["value"] == "content_post"?"on":"");
	$terms_active = (isset($add_post_items["terms_active"]["value"]) && $add_post_items["terms_active"]["value"] == "terms_active"?"on":"");
	$post_title_min_limit = wpqa_options("post_title_min_limit");
	$post_title_limit = wpqa_options("post_title_limit");
	$post_title = strip_tags($posted['title']);
	$post_title = str_replace('<p>','',$post_title);
	$post_title = str_replace('</p>','',$post_title);
	$post_title = str_replace('<br>','',$post_title);
	$post_title = str_replace('<br data-mce-bogus="1">','',$post_title);
	if (empty($posted['title'])) {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (title).","wpqa"));
	}

	if ($post_title_min_limit > 0 && strlen($post_title) < $post_title_min_limit) {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.sprintf(esc_html__("Sorry, The minimum characters for post title is %s.","wpqa"),$post_title_min_limit));
	}

	if ($post_title_limit > 0 && strlen($post_title) > $post_title_limit) {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.sprintf(esc_html__("Sorry, The maximum characters for post title is %s.","wpqa"),$post_title_limit));
	}

	if (empty($posted['category']) || $posted['category'] == '-1') {
		$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (category).","wpqa"));
	}

	if (isset($add_post_items["tags_post"]["value"]) && $add_post_items["tags_post"]["value"] == "tags_post") {
		$post_tags_min_limit = (int)wpqa_options("post_tags_min_limit");
		$post_tags_limit = (int)wpqa_options("post_tags_limit");
		$post_tags_number_min_limit = (int)wpqa_options("post_tags_number_min_limit");
		$post_tags_number_limit = (int)wpqa_options("post_tags_number_limit");
		if (!empty($posted['post_tag'])) {
			$tags = explode(',',trim(stripslashes($posted['post_tag'])));
			$tags = array_map('strtolower',$tags);
			$tags = array_map('trim',$tags);
		}else {
			$tags = array();
		}
		if ($post_tags_number_min_limit > 0 && sizeof($tags) < $post_tags_number_min_limit) {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.sprintf(esc_html__("Sorry, The minimum tags for post is %s tags.","wpqa"),$post_tags_number_min_limit));
		}
		if ($post_tags_number_limit > 0 && $post_tags_number_limit < sizeof($tags)) {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.sprintf(esc_html__("Sorry, The maximum tags for post is %s tags.","wpqa"),$post_tags_number_limit));
		}
		if (($post_tags_min_limit > 0 || $post_tags_limit > 0) && is_array($tags) && !empty($tags)) {
			foreach ($tags as $value) {
				$value = trim($value);
				if ($post_tags_min_limit > 0 && strlen($value) < $post_tags_min_limit) {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.sprintf(esc_html__("Sorry, The minimum characters for post tag is %s.","wpqa"),$post_tags_min_limit));
				}
				if ($post_tags_limit > 0 && strlen($value) > $post_tags_limit) {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.sprintf(esc_html__("Sorry, The maximum characters for post tag is %s.","wpqa"),$post_tags_limit));
				}
			}
		}
	}

	if ($content_post == "on") {
		if (empty($posted['comment'])) {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (details).","wpqa"));
		}

		$post_content_min_limit = wpqa_options("post_content_min_limit");
		$post_content_limit = wpqa_options("post_content_limit");
		$post_content = strip_tags($posted['comment']);
		$post_content = str_replace('<p>','',$post_content);
		$post_content = str_replace('</p>','',$post_content);
		$post_content = str_replace('<br>','',$post_content);
		$post_content = str_replace('<br data-mce-bogus="1">','',$post_content);
		if ($post_content_min_limit > 0 && strlen($post_content) < $post_content_min_limit) {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.sprintf(esc_html__("Sorry, The minimum characters for post content is %s.","wpqa"),$post_content_min_limit));
		}
		if ($post_content_limit > 0 && strlen($post_content) > $post_content_limit) {
			$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.sprintf(esc_html__("Sorry, The maximum characters for post content is %s.","wpqa"),$post_content_limit));
		}
	}

	if ($type == "add") {
		wpqa_check_captcha(wpqa_options("the_captcha_post"),"post",$posted,$errors);

		if ($terms_active == "on" && isset($posted['agree_terms']) && $posted['agree_terms'] != "on") {
			$errors->add('required-terms',esc_html__("There are required fields (Agree of the terms).","wpqa"));
		}
	}
	return $errors;
}
/* Post content */
add_action('wpqa_post_content','wpqa_post_content',1,4);
if (!function_exists('wpqa_post_content')) :
	function wpqa_post_content($post_id,$user_id,$post_author) {
		$_paid_post = get_post_meta($post_id,"_paid_post",true);
		$end_sticky_time = get_post_meta($post_id,"end_sticky_time",true);
		if ((is_super_admin($user_id) || ($post_author > 0 && $user_id == $post_author)) && (isset($_paid_post) && $_paid_post == "paid")) {
			echo '<div class="alert-message message-paid-post"><i class="icon-lamp"></i><p> '.esc_html__("This is a paid post.","wpqa").'</p></div>';
		}
		if (is_sticky()) {
			if ((is_super_admin($user_id) || ($post_author > 0 && $user_id == $post_author)) && ($end_sticky_time != "" && $end_sticky_time >= strtotime(date("Y-m-d")))) {
				echo '<div class="alert-message message-paid-sticky"><i class="icon-lamp"></i><p>'.esc_html__('This post will "sticky" to','wpqa').': '.date("Y-m-d",$end_sticky_time).'</p></div>';
			}
		}
	}
endif;
/* Post content loop */
add_action('wpqa_post_content','wpqa_post_content_loop');
add_action('wpqa_post_content_loop','wpqa_post_content_loop');
if (!function_exists('wpqa_post_content_loop')) :
	function wpqa_post_content_loop($post_id) {
		$end_sticky_time  = get_post_meta($post_id,"end_sticky_time",true);
		if ($end_sticky_time != "" && $end_sticky_time < strtotime(date("Y-m-d"))) {
			delete_post_meta($post_id,"start_sticky_time");
			delete_post_meta($post_id,"end_sticky_time");
			delete_post_meta($post_id,'sticky');
			$sticky_posts = get_option('sticky_posts');
			if (is_array($sticky_posts) && in_array($post_id,$sticky_posts)) {
				$sticky_posts = wpqa_remove_item_by_value($sticky_posts,$post_id);
				update_option('sticky_posts',$sticky_posts);
			}
		}
	}
endif;?>