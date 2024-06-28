<?php

/* @author    2codeThemes
*  @package   WPQA/CPT
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Message post type */
function wpqa_message_post_types_init() {
	$active_message = wpqa_options('active_message');
	if ($active_message == "on") {
		$featured_image_message = wpqa_options("featured_image_message");
		$thumbnail = ($featured_image_message == "on"?array("thumbnail"):array());

			register_post_type( 'message',
				array(
					'label' => esc_html__('Messages','wpqa'),
						'labels' => array(
					'name'               => esc_html__('Messages','wpqa'),
					'singular_name'      => esc_html__('Messages','wpqa'),
					'menu_name'          => esc_html__('Messages','wpqa'),
					'name_admin_bar'     => esc_html__('Messages','wpqa'),
					'add_new'            => esc_html__('Add New','wpqa'),
					'add_new_item'       => esc_html__('Add New Message','wpqa'),
					'new_item'           => esc_html__('New Message','wpqa'),
					'edit_item'          => esc_html__('Edit Message','wpqa'),
					'view_item'          => esc_html__('View Message','wpqa'),
					'view_items'         => esc_html__('View Messages','wpqa'),
					'all_items'          => esc_html__('All Messages','wpqa'),
					'search_items'       => esc_html__('Search Messages','wpqa'),
					'parent_item_colon'  => esc_html__('Parent Message:','wpqa'),
					'not_found'          => esc_html__('No Messages Found.','wpqa'),
					'not_found_in_trash' => esc_html__('No Messages Found in Trash.','wpqa'),
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
				'menu_icon'           => "dashicons-email",
				'supports'            => array_merge($thumbnail,array('title','editor')),
			)
		);
	}
}
add_action( 'wpqa_init', 'wpqa_message_post_types_init', 0 );
function wpqa_message_updated_messages($messages) {
	global $post_ID;
	$messages['message'] = array(
		0 => '',
		1 => '',
	);
	return $messages;
}
add_filter('post_updated_messages','wpqa_message_updated_messages');
/* Admin columns for post types */
function wpqa_message_columns($old_columns) {
	$columns = array();
	$columns["cb"]       = "<input type=\"checkbox\">";
	$columns["title"]    = esc_html__("Title","wpqa");
	$columns["content"]  = esc_html__("Content","wpqa");
	$columns["author_m"] = esc_html__("Author","wpqa");
	$columns["to_user"]  = esc_html__("To user/groups/s","wpqa");
	$columns["date"]     = esc_html__("Date","wpqa");
	$columns["delete"]   = esc_html__("User deleted?","wpqa");
	return $columns;
}
add_filter('manage_edit-message_columns', 'wpqa_message_columns');
function wpqa_message_custom_columns($column) {
	global $post;
	$to_user = get_post_meta($post->ID,'message_user_id',true);
	$message_groups_array = get_post_meta($post->ID,'message_groups_array',true);
	$message_user_array = get_post_meta($post->ID,'message_user_array',true);
	$to_user_array = (is_array($message_user_array) && !empty($message_user_array)?$message_user_array:($to_user != ""?array($to_user):""));
	switch ( $column ) {
		case 'author_m' :
			$display_name = get_the_author_meta('display_name',$post->post_author);
			if ($post->post_author > 0) {
				echo '<a href="edit.php?post_type=message&author='.$post->post_author.'">'.$display_name.'</a>';
			}else {
				echo get_post_meta($post->ID,'message_username',true)."<br>".get_post_meta($post->ID,'message_email',true);
			}
			$save_ip_address = wpqa_options("save_ip_address");
			if ($save_ip_address == "on") {
				$get_ip_address = get_post_meta($post->ID,'wpqa_ip_address',true);
				if ($get_ip_address != "") {
					echo "<br>".$get_ip_address;
				}
			}
		break;
		case 'content' :
			echo esc_html($post->post_content);
		break;
		case 'to_user' :
			if (is_array($message_groups_array) && !empty($message_groups_array)) {
				foreach ($message_groups_array as $key => $value) {
					if ($value != '0') {
						echo ucfirst(str_ireplace("_"," ",$key)).'<br>';
					}
				}
			}else {
				if (is_array($to_user_array) && !empty($to_user_array)) {
					foreach ($to_user_array as $value) {
						$display_name_user = get_the_author_meta('display_name',$value);
						echo '<a href="'.wpqa_profile_url($value).'">'.$display_name_user.'</a><br>';
					}
				}
			}
		break;
		case 'delete' :
			$delete_send_message = get_post_meta($post->ID,"delete_send_message",true);
			$delete_inbox_message = get_post_meta($post->ID,"delete_inbox_message",true);
			$message_user_array = get_post_meta($post->ID,'message_user_array',true);
			if ($delete_inbox_message == 1 || $delete_inbox_message == "on") {
				if (is_array($message_user_array)) {
					esc_html_e("One of the users has deleted his inbox message.","wpqa").'<br>';
				}else {
					$display_name_user = get_the_author_meta('display_name',$to_user);
					echo '<a href="'.wpqa_profile_url($to_user).'">'.$display_name_user.'</a> '.esc_html__("has deleted his inbox message.","wpqa").'<br>';
				}
			}
			if ($delete_send_message == 1 || $delete_inbox_message == 1 || $delete_send_message == "on" || $delete_inbox_message == "on") {
				if (($delete_send_message == 1 && $delete_inbox_message == 1) || ($delete_send_message == "on" && $delete_inbox_message == "on")) {
					echo '<br>';
				}
				if ($delete_send_message == 1 || $delete_send_message == "on") {
					$display_name = get_the_author_meta('display_name',$post->post_author);
					echo '<a href="'.wpqa_profile_url($post->post_author).'">'.$display_name.'</a> '.esc_html__("has deleted his sent message.","wpqa");
				}
			}
			if ($delete_inbox_message != 1 && $delete_send_message != 1 && $delete_inbox_message != "on" && $delete_send_message != "on") {
				echo '<span aria-hidden="true">—</span><span class="screen-reader-text">'.esc_html__("Message has not been deleted","wpqa").'</span>';
			}
		break;
	}
}
add_action('manage_message_posts_custom_column','wpqa_message_custom_columns',2);
/* Send message shortcode */
function wpqa_send_message_shortcode($atts, $content = null) {
	global $message_add;
	$a = shortcode_atts( array(
			'popup' => '',
	), $atts );
	$out = '';
	$activate_login = wpqa_options("activate_login");
	$send_message = wpqa_options("send_message");
	$send_message_no_register = wpqa_options("send_message_no_register");
	$custom_permission = wpqa_options("custom_permission");
	$user_id = get_current_user_id();
	
	if (is_user_logged_in()) {
		$user_is_login = get_userdata($user_id);
		$user_login_group = wpqa_get_user_group($user_is_login);
		$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
	}
	
	if (($custom_permission != "on" && ((isset($user_login_group) && $user_login_group == "wpqa_under_review") || (isset($user_login_group) && $user_login_group == "activation"))) || ($custom_permission == "on" && (is_user_logged_in() && !is_super_admin($user_id) && empty($roles["send_message"])) || (!is_user_logged_in() && $send_message != "on"))) {
		$out .= wpqa_paid_subscriptions_message(esc_html__("Sorry, you do not have permission to send message.","wpqa"));
		if ($activate_login != 'disabled' && !is_user_logged_in()) {
			$out .= do_shortcode("[wpqa_login]");
		}
	}else if (!is_user_logged_in() && $send_message_no_register != "on") {
		if ($activate_login != 'disabled') {
			$out .= wpqa_login_message(esc_html__("You must login to send a message.","wpqa"),"","","register_2");
		}else {
			$out .= wpqa_paid_subscriptions_message(esc_html__("Sorry, you do not have permission to send message.","wpqa"));
		}
	}else {
		$post_type = (isset($_POST["post_type"]) && $_POST["post_type"] != ""?esc_html($_POST["post_type"]):"");
		$get_user_id = (int)(isset($message_add['user_id'])?$message_add['user_id']:0);
		if (wpqa_is_user_profile()) {
			$get_user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
		}
		
		if (!wpqa_is_user_messages() && is_user_logged_in() && $user_id == $get_user_id && $get_user_id > 0) {
			echo '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("You can't send message for yourself.","wpqa").'</p></div>';
		}else {
			$comment_message = wpqa_options("comment_message");
			$editor_message_details = wpqa_options("editor_message_details");
			$rand = rand(1,1000);
			$out .= '
			<form class="form-post wpqa_form" method="post" enctype="multipart/form-data">'.apply_filters('wpqa_new_message','').'
				<div class="form-inputs clearfix">';
					if (!is_user_logged_in() && $send_message_no_register == "on") {
						$out .= '<p>
							<label for="message-username-'.$rand.'">'.esc_html__("Username","wpqa").'<span class="required">*</span></label>
							<input name="username" id="message-username-'.$rand.'" class="the-username form-control" type="text" value="'.(isset($message_add['username'])?$message_add['username']:'').'">
							<i class="icon-user"></i>
							<span class="form-description">'.esc_html__("Please type your username.","wpqa").'</span>
						</p>
						
						<p>
							<label for="message-email-'.$rand.'">'.esc_html__("E-Mail","wpqa").'<span class="required">*</span></label>
							<input name="email" id="message-email-'.$rand.'" class="the-email form-control" type="text" value="'.(isset($message_add['email'])?$message_add['email']:'').'">
							<i class="icon-mail"></i>
							<span class="form-description">'.esc_html__("Please type your E-Mail.","wpqa").'</span>
						</p>';
					}
					$out .= '<p>
						<label for="message-title-'.$rand.'">'.esc_html__("Message Title","wpqa").'<span class="required">*</span></label>
						<input name="title" id="message-title-'.$rand.'" class="the-title form-control" type="text" value="'.(isset($message_add['title'])?wp_unslash(esc_html($message_add['title'])):"").'">
						<i class="icon-chat"></i>
					</p>';

					$featured_image_message = wpqa_options("featured_image_message");
					if ($featured_image_message == "on") {
						$out .= '<div class="question-multiple-upload question-upload-featured">
							<label for="featured_image-'.$rand.'">'.esc_html__("Featured image","wpqa").'</label>
							<div class="clearfix"></div>
							<div class="fileinputs">
								<input type="file" class="file" name="featured_image" id="featured_image-'.$rand.'">
								<i class="icon-camera"></i>
								<div class="fakefile">
									<button type="button">'.esc_html__("Select file","wpqa").'</button>
									<span>'.esc_html__("Browse","wpqa").'</span>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>';
					}
					
					$out .= '<div class="wpqa_textarea'.($editor_message_details == "on"?"":" wpqa_textarea_p").'">
						<label for="message-details-'.$rand.'">'.apply_filters("wpqa_filter_details_message",esc_html__("Details","wpqa")).($comment_message == "on"?'<span class="required">*</span>':'').'</label>';
						$message_value = (isset($message_add['comment'])?wpqa_esc_textarea($message_add['comment']):"");
						if ($editor_message_details == "on") {
							$settings = array("textarea_name" => "comment","media_buttons" => true,"textarea_rows" => 10);
							$settings = apply_filters('wpqa_message_editor_setting',$settings);
							ob_start();
							wp_editor($message_value,'message-details-'.$rand,$settings);
							$editor_contents = ob_get_clean();
							$out .= '<div class="the-details the-textarea">'.$editor_contents.'</div>';
						}else {
							$out .= '<textarea name="comment" id="message-details-'.$rand.'" class="the-textarea form-control" aria-required="true" cols="58" rows="8">'.$message_value.'</textarea>
							<i class="icon-pencil"></i>';
						}
						$out .= '<span class="form-description">'.esc_html__("Type the description thoroughly and in details.","wpqa").'</span>
					</div>';
					
					$out .= wpqa_add_captcha(wpqa_options("the_captcha_message"),"message",$rand);
					
				$out .= '</div>
				
				<p class="form-submit mb-0">
					<input type="hidden" name="post_type" value="send_message">
					<input type="hidden" name="wpqa_message_nonce" value="'.wp_create_nonce("wpqa_message_nonce").'">';

					if (isset($a["popup"]) && $a["popup"] == "popup") {
						$out .= '<input type="hidden" name="message_popup" value="popup">';
					}
					$out .= '<input type="hidden" name="form_type" value="send_message">';
					if ($get_user_id > 0) {
						$out .= '<input type="hidden" name="user_id" value="'.$get_user_id.'">';
					}
					$out .= (wpqa_input_button() == "button"?'<button type="submit" class="btn btn__primary btn__block btn__large__height button-default send-message button-hide-click">'.esc_html__("Send Your Message","wpqa").'</button>':'<input type="submit" value="'.esc_html__("Send Your Message","wpqa").'" class="button-default send-message button-hide-click">').'
					<span class="load_span"><span class="loader_2"></span></span>
				</p>
			
			</form>';
		}
	}
	return $out;
}
/* New message */
function wpqa_new_message() {
	if (isset($_POST["form_type"]) && $_POST["form_type"] == "send_message") :
		$return = wpqa_process_new_messages($_POST);
		if (is_wp_error($return)) :
			return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
		else :
			$get_post = get_post($return);
			if ($get_post->post_type == "message") {
				$user_id = get_current_user_id();
				$get_message_user = get_post_meta($return,"message_user_id",true);
				if (is_super_admin($user_id) || $get_post->post_status == "publish") {
					wpqa_notification_send_message($get_post,$user_id,$get_message_user);
					wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("The message has been sent successfully.","wpqa").'</p></div>','wpqa_session');
				}else {
					wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Your message has been sent successfully, The message is under review.","wpqa").'</p></div>','wpqa_session');
				}
				$redirect_url = apply_filters("wpqa_filter_message_redirect",(wpqa_is_user_messages()?wpqa_messages_permalink():wpqa_profile_url($get_message_user)),$return);
				wp_redirect(esc_url($redirect_url));
				exit;
			}
			exit;
		endif;
	endif;
}
add_filter('wpqa_new_message','wpqa_new_message');
/* Notification send message */
function wpqa_notification_send_message($get_post,$user_id,$get_message_user) {
	if ($user_id != $get_message_user && $get_message_user > 0) {
		$message_username = get_post_meta($get_post->ID,'message_username',true);
		if ($get_post->post_author != $get_message_user && $get_message_user > 0) {
			$header_messages = wpqa_options("header_messages");
			$header_style = wpqa_options("header_style");
			$show_message_area = ($header_messages == "on" && $header_style == "simple"?"on":0);
			wpqa_notifications_activities($get_message_user,$get_post->post_author,($get_post->post_author == 0?$message_username:""),"","","add_message_user","notifications","","message",($show_message_area === "on"?false:true));
		}
		if ($user_id > 0) {
			wpqa_notifications_activities($user_id,$get_message_user,"","","","add_message","activities","","message");
		}
		$send_email_message = wpqa_options("send_email_message");
		if ($send_email_message == "on") {
			$user = get_userdata($get_message_user);
			$send_text = wpqa_send_mail(
				array(
					'content'          => wpqa_options("email_new_message"),
					'user_id'          => $get_message_user,
					'post_id'          => $get_post->ID,
					'sender_user_id'   => $get_post->post_author,
					'received_user_id' => $user->ID,
				)
			);
			$email_title = wpqa_options("title_new_message");
			$email_title = ($email_title != ""?$email_title:esc_html__("New message","wpqa"));
			$email_title = wpqa_send_mail(
				array(
					'content'          => $email_title,
					'title'            => true,
					'break'            => '',
					'user_id'          => $get_message_user,
					'post_id'          => $get_post->ID,
					'sender_user_id'   => $get_post->post_author,
					'received_user_id' => $user->ID,
				)
			);
			$unsubscribe_mails = get_the_author_meta('unsubscribe_mails',$user->ID);
			$send_message_mail = get_the_author_meta('send_message_mail',$user->ID);
			if ($unsubscribe_mails != "on" && $send_message_mail == "on") {
				wpqa_send_mails(
					array(
						'toEmail'     => esc_html($user->user_email),
						'toEmailName' => esc_html($user->display_name),
						'title'       => $email_title,
						'message'     => $send_text,
					)
				);
			}
		}
	}
}
/* Process new messages */
function wpqa_process_new_messages($data) {
	global $message_add;
	@set_time_limit(0);
	$errors = new WP_Error();
	$message_add = array();
	
	$post_type = (isset($data["post_type"]) && $data["post_type"] != ""?$data["post_type"]:"");
	
	if ($post_type == "send_message") {
		$fields = array(
			'title','featured_image','comment','wpqa_captcha','username','email','user_id'
		);

		$fields = apply_filters('wpqa_send_message_fields',$fields);
		
		foreach ($fields as $field) :
			if (isset($data[$field])) $message_add[$field] = $data[$field]; else $message_add[$field] = '';
		endforeach;

		if (!isset($data['mobile']) && (!isset($data['wpqa_message_nonce']) || !wp_verify_nonce($data['wpqa_message_nonce'],'wpqa_message_nonce'))) {
			$errors->add('nonce-error','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There is an error, Please reload the page and try again.","wpqa"));
		}
		
		$featured_image_message = wpqa_options("featured_image_message");
		$custom_permission = wpqa_options("custom_permission");
		$send_message_no_register = wpqa_options("send_message_no_register");
		$send_message = wpqa_options("send_message");
		$user_id = get_current_user_id();
		if (is_user_logged_in()) {
			$user_is_login = get_userdata($user_id);
			$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
		}
		
		if (($custom_permission == "on" && is_user_logged_in() && !is_super_admin($user_id) && empty($roles["send_message"])) || ($custom_permission == "on" && !is_user_logged_in() && $send_message != "on")) {
			$errors->add('required','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Sorry, you do not have permission to send message.","wpqa"));
			if (!is_user_logged_in()) {
				$errors->add('required','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("You must login to send a message.","wpqa"));
			}
		}else if (!is_user_logged_in() && $send_message_no_register != "on") {
			$errors->add('required','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("You must login to send a message.","wpqa"));
		}else if ($message_add['user_id'] == $user_id) {
			$errors->add('required','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("You can't send message for yourself.","wpqa"));
		}else if ($message_add['user_id'] == "") {
			$errors->add('required','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There is an error.","wpqa"));
		}
		
		if (!is_user_logged_in() && $send_message_no_register == "on" && $user_id == 0) {
			if (empty($message_add['username'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (username).","wpqa"));
			if (empty($message_add['email'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (email).","wpqa"));
			if (!is_email(trim($message_add['email']))) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Please write correctly email.","wpqa"));
		}
		
		/* Validate Required Fields */
		
		if (empty($message_add['title'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (title).","wpqa"));
		
		/* Featured image */

		if ($featured_image_message == "on") {
			$featured_image = '';

			require_once(ABSPATH . 'wp-admin/includes/image.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			
			if (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) :
				$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/webp","image/gif","image/tiff","image/ico");
				if (!isset($data['mobile']) && !in_array($_FILES['featured_image']['type'],$types)) :
					$errors->add('upload-error',esc_html__("Attachment Error! Please upload image only.","wpqa"));
					return $errors;
				endif;
				
				$featured_image = wp_handle_upload($_FILES['featured_image'],array('test_form' => false),current_time('mysql'));
				
				if (isset($featured_image['error'])) :
					$errors->add('upload-error',esc_html__("Attachment Error: ","wpqa") . $featured_image['error']);
					return $errors;
				endif;
			endif;
		}
		
		$comment_message = wpqa_options("comment_message");
		if ($comment_message == "on") {
			if (empty($message_add['comment'])) $errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (content).","wpqa"));
		}
		
		wpqa_check_captcha(wpqa_options("the_captcha_message"),"message",$data,$errors);

		do_action('wpqa_send_message_errors',$errors,$data);
		
		if (sizeof($errors->errors) > 0) return $errors;
		
		$message_publish = wpqa_options("message_publish");
		$editor_message_details = wpqa_options("editor_message_details");
		
		/* Create message */

		$insert_data = array(
			'post_content' => ($editor_message_details == "on"?wpqa_esc_textarea($message_add['comment']):wpqa_esc_textarea($message_add['comment'])),
			'post_title'   => sanitize_text_field($message_add['title']),
			'post_status'  => ($message_publish == "publish" || is_super_admin($user_id)?"publish":"draft"),
			'post_author'  => (!is_user_logged_in() && $send_message_no_register == "on"?0:$user_id),
			'post_type'	   => 'message',
		);
		
		$post_id = wp_insert_post($insert_data);
			
		if ($post_id == 0 || is_wp_error($post_id)) wp_die(esc_html__("Error in message.","wpqa"));
		
		/* Featured image */
		
		if (isset($featured_image['type']) && isset($featured_image['file'])) :
			$featured_image_data = array(
				'post_mime_type' => $featured_image['type'],
				'post_title'	 => preg_replace('/\.[^.]+$/','',basename($featured_image['file'])),
				'post_content'   => '',
				'post_status'	 => 'inherit',
				'post_author'    => $user_id,
			);
			$featured_image_id = wp_insert_attachment($featured_image_data,$featured_image['file'],$post_id);
			$featured_image_metadata = wp_generate_attachment_metadata($featured_image_id,$featured_image['file']);
			wp_update_attachment_metadata($featured_image_id,$featured_image_metadata);
			set_post_thumbnail($post_id,$featured_image_id);
		endif;
		
		if (!is_user_logged_in() && $send_message_no_register == "on" && $user_id == 0) {
			$message_username = sanitize_text_field($message_add['username']);
			$message_email = sanitize_text_field($message_add['email']);
			update_post_meta($post_id,'message_username',$message_username);
			update_post_meta($post_id,'message_email',$message_email);
		}

		update_post_meta($post_id,'message_user_id',(int)$message_add['user_id']);
		update_post_meta($post_id,'message_new','on');
		$new_messages_count = (int)get_user_meta((int)$message_add['user_id'],"wpqa_new_messages_count",true);
		$new_messages_count++;
		update_user_meta((int)$message_add['user_id'],"wpqa_new_messages_count",$new_messages_count);
		
		do_action('wpqa_new_messages',$post_id);
		do_action('wpqa_finished_send_message',$post_id,$data,"add","message");
	}
	if ($post_type == "send_message") {
		/* Successful */
		return $post_id;
	}
}
/* View message */
function wpqa_message_view() {
	global $post;
	$seen_message = wpqa_options("seen_message");
	$message_id = (int)$_POST["message_id"];
	$message_show = esc_html($_POST["message_show"]);
	$user_id = get_current_user_id();
	if ($message_show == "send" || $message_show == "sent") {
		$attrs = array("author" => $user_id,"meta_query" => array(array("key" => "delete_send_message","compare" => "NOT EXISTS"),array("key" => "message_user_array","compare" => "NOT EXISTS")));
	}else {
		$attrs = array("meta_query" => array('relation' => 'AND',array("key" => "delete_inbox_message","compare" => "NOT EXISTS"),array('relation' => 'OR',array("key" => "message_user_id","compare" => "=","value" => $user_id),array("key" => "message_user_".$user_id,"compare" => "EXISTS"))));
	}
	$args = array_merge(array("p" => $message_id,"post_type" => "message"),$attrs);
	$the_query = new WP_Query($args);
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$post_author = $post->post_author;
			$seen_message_done = get_user_meta($post_author,'seen_message_done',true);
			$message_user_id = get_post_meta($message_id,'message_user_id',true);
			$message_user_array = get_post_meta($message_id,'message_user_array',true);
			$message_new = get_post_meta($message_id,'message_new',true);
			$message_not_new = get_post_meta($message_id,'message_not_new_'.$user_id,true);
			$message_not_new = (isset($message_not_new) && $message_not_new != "" && $message_not_new != "no"?$message_not_new:"no");
			if ($seen_message_done != "on" || $message_new == 1 || $message_new == "on" || $message_not_new == "no" && ($user_id == $message_user_id || (is_array($message_user_array) && !empty($message_user_array) && in_array($user_id,$message_user_array)))) {
				if (is_array($message_user_array) && !empty($message_user_array) && in_array($user_id,$message_user_array)) {
					update_post_meta($message_id,'message_not_new_'.$user_id,"yes");
				}else if ($user_id != $post_author && $user_id > 0) {
					delete_post_meta($message_id,'message_new');
				}
				if ($user_id != $post_author && $user_id > 0) {
					if ($seen_message == "on" && $seen_message_done != "on") {
						update_user_meta($post_author,"seen_message_done","on");
						wpqa_notifications_activities($post_author,$user_id,"","","","seen_message","notifications","","message");
					}
					$new_messages_count = (int)get_user_meta($user_id,"wpqa_new_messages_count",true);
					$new_messages_count--;
					update_user_meta($user_id,"wpqa_new_messages_count",($new_messages_count <= 0?0:$new_messages_count));
				}
			}
			if (!isset($_POST["mobile"])) {
				$featured_image_message = wpqa_options("featured_image_message");
				if ($featured_image_message == "on") {
					$featured_image = get_post_meta($message_id,"_thumbnail_id",true);
					if ($featured_image != "") {
						$img_url = wp_get_attachment_url($featured_image,"full");
						if ($img_url != "") {
							$featured_image_message_lightbox = wpqa_options("featured_image_message_lightbox");
							$featured_image_message_width = wpqa_options("featured_image_message_width");
							$featured_image_message_height = wpqa_options("featured_image_message_height");
							$featured_image_message_width = ($featured_image_message_width != ""?$featured_image_message_width:260);
							$featured_image_message_height = ($featured_image_message_height != ""?$featured_image_message_height:185);
							$link_url = ($featured_image_message_lightbox == "on"?$img_url:"");
							$last_image = wpqa_get_aq_resize_img($featured_image_message_width,$featured_image_message_height,"",$featured_image);
							if (isset($last_image) && $last_image != "") {
								echo "<div class='featured_image_message'>".($link_url != ""?"<a href='".$link_url."'>":"").$last_image.($link_url != ""?"</a>":"")."</div>
								<div class='clearfix'></div>";
							}
						}
					}
				}
				echo "<div>".nl2br(get_the_content($post->ID));
					do_action("wpqa_after_message_content",$message_id,$post_author,$user_id);
				echo "</div>";
			}
		}
	}
	wp_reset_postdata();
	if (!isset($_POST["mobile"])) {
		die();
	}
}
add_action('wp_ajax_wpqa_message_view','wpqa_message_view');
add_action('wp_ajax_nopriv_wpqa_message_view','wpqa_message_view');
/* Reply message */
function wpqa_message_reply() {
	$message_id = (int)$_POST["message_id"];
	$get_message = get_post($message_id);
	if (isset($get_message->ID) && $get_message->ID > 0) {
		$get_the_title = $get_message->post_title;
		$current_user = get_current_user_id();
		$message_user_id = get_post_meta($message_id,'message_user_id',true);
		if ($get_the_title != "" && $current_user > 0 && $current_user == $message_user_id) {
			echo str_ireplace(esc_html__("RE:","wpqa")." ".esc_html__("RE:","wpqa")." ".esc_html__("RE:","wpqa")." ",esc_html__("RE:","wpqa")." ".esc_html__("RE:","wpqa")." ",esc_html__("RE:","wpqa")." ".$get_the_title);
			if (!isset($_POST["mobile"])) {
				die();
			}
		}
	}
}
add_action('wp_ajax_wpqa_message_reply','wpqa_message_reply');
add_action('wp_ajax_nopriv_wpqa_message_reply','wpqa_message_reply');
/* Block message */
function wpqa_block_message() {
	if (!isset($_POST["mobile"])) {
		check_ajax_referer('block_message_nonce','block_message_nonce');
	}
	$user_id      = (int)$_POST["user_id"];
	$current_user = get_current_user_id();
	
	$user_block_message = get_user_meta($current_user,"user_block_message",true);
	if (empty($user_block_message)) {
		update_user_meta($current_user,"user_block_message",array($user_id));
	}else {
		update_user_meta($current_user,"user_block_message",array_merge($user_block_message,array($user_id)));
	}
	if (!isset($_POST["mobile"])) {
		die();
	}
}
add_action('wp_ajax_wpqa_block_message','wpqa_block_message');
add_action('wp_ajax_nopriv_wpqa_block_message','wpqa_block_message');
/* Unblock message */
function wpqa_unblock_message() {
	if (!isset($_POST["mobile"])) {
		check_ajax_referer('block_message_nonce','block_message_nonce');
	}
	$user_id      = (int)$_POST["user_id"];
	$current_user = get_current_user_id();
	
	$user_block_message = get_user_meta($current_user,"user_block_message",true);
	$remove_user_block_message = wpqa_remove_item_by_value($user_block_message,$user_id);
	update_user_meta($current_user,"user_block_message",$remove_user_block_message);
	if (!isset($_POST["mobile"])) {
		die();
	}
}
add_action('wp_ajax_wpqa_unblock_message','wpqa_unblock_message');
add_action('wp_ajax_nopriv_wpqa_unblock_message','wpqa_unblock_message');
/* Show messages li */
if (!function_exists('wpqa_get_messages')) :
	function wpqa_get_messages($user_id,$item_number,$more_button,$count = false,$more_button_ul = false) {
		global $post;
		$output = '';
		$time_format = wpqa_options("time_format");
		$time_format = ($time_format?$time_format:get_option("time_format"));
		$date_format = wpqa_options("date_format");
		$date_format = ($date_format?$date_format:get_option("date_format"));
		if ($count == true) {
			$num = (int)get_user_meta($user_id,"wpqa_new_messages_count",true);
			$num = (isset($num) && $num != "" && $num > 0?$num:0);
			if (isset($num) && $num != "" && $num > 0) {
				$num = ($num <= 99?$num:"99+");
				$output .= '<span class="notifications-number">'.$num.'</span>';
			}
		}
		if ($more_button_ul == false) {
			$output .= '<div>';
		}
		$output .= '<ul class="list-unstyled mb-0">';
		$args = array('post_type' => 'message','posts_per_page' => $item_number,"meta_query" => array('relation' => 'AND',array("key" => "delete_inbox_message","compare" => "NOT EXISTS"),array("key" => "message_user_id","compare" => "=","value" => $user_id)));
		$messages_query = new WP_Query( $args );
		if ($messages_query->have_posts()) {
			while ( $messages_query->have_posts() ) { $messages_query->the_post();
				$message_new = get_post_meta($post->ID,'message_new',true);
				$post_author = $post->post_author;
				$output .= '<li class="notifications__item d-flex '.wpqa_get_gender_class($post_author,$post->ID).'">';
					$display_name = get_the_author_meta('display_name',$post_author);
					$output .= wpqa_get_avatar_link(array("user_id" => $post_author,"user_name" => $display_name,"size" => 27,"span" => "span","class" => "rounded-circle")).
					'<div class="notification__body">';
						if ($post_author > 0) {
							$output .= '<a class="author__name" href="'.wpqa_profile_url($post_author).'">'.$display_name.'</a>';
						}else {
							$deleted_user = ($post_author > 0 && $display_name != ""?$display_name:($post_author == 0?get_post_meta($post->ID,'message_username',true):"delete"));
							$output .= ($deleted_user == "delete" || $deleted_user == ""?esc_html__("[Deleted User]","wpqa"):$deleted_user);
						}
						$output .= ' '.esc_html__("has","wpqa").' <a class="notification__question notification__question-dark" href="'.esc_url(wpqa_messages_permalink()).'">'.esc_html__("sent a message for you.","wpqa").'</a>
						<span class="notifications-date notification__date d-block mt-2">'.sprintf(esc_html__('%1$s at %2$s','wpqa'),get_the_time($date_format),get_the_time($time_format)).'</span>
					</div>
				</li>';
			}
			if ($more_button == "on" && $more_button_ul == true) {
				$output .= '<li class="notifications__item d-flex align-items-center justify-content-center notification__show-all"><a href="'.esc_url(wpqa_messages_permalink()).'">'.esc_html__("Show all messages.","wpqa").'</a></li>';
			}
			$output .= '</ul>';
			if ($more_button == "on" && $more_button_ul == false) {
				$output .= '<a href="'.esc_url(wpqa_messages_permalink()).'">'.esc_html__("Show all messages.","wpqa").'</a>';
			}
		}else {
			$output .= '<li class="notifications__item d-flex align-items-center justify-content-center"><div class="notification__body">'.esc_html__("There are no messages yet.","wpqa").'</div></li></ul>';
		}
		if ($more_button_ul == false) {
			$output .= '</div>';
		}
		wp_reset_postdata();
		return $output;
	}
endif;
/* Delete messages */
function wpqa_delete_messages($post_id,$post_author,$user_id,$message_user_id,$message_user_array) {
	if (($post_author > 0 && $post_author == $user_id) || $message_user_id == $user_id || (is_array($message_user_array) && !empty($message_user_array) && in_array($user_id,$message_user_array))) {
		wpqa_notifications_activities($user_id,"","","","",($message_user_id == $user_id || (is_array($message_user_array) && !empty($message_user_array) && in_array($user_id,$message_user_array))?"delete_inbox_message":"delete_send_message"),"activities","","message");
		if (is_array($message_user_array) && !empty($message_user_array) && in_array($user_id,$message_user_array)) {
			$remove_message_user_array = wpqa_remove_item_by_value($message_user_array,$user_id);
			update_post_meta($post_id,"message_user_array",$remove_message_user_array);
			delete_post_meta($post_id,"message_user_".$user_id);
		}
		if ($post_author == $user_id || ($message_user_id == $user_id || (is_array($message_user_array) && !empty($message_user_array) && in_array($user_id,$message_user_array)))) {
			if ($post_author == $user_id) {
				update_post_meta($post_id,"delete_send_message",1);
			}else {
				update_post_meta($post_id,"delete_inbox_message",1);
			}
		}
		$message_new = get_post_meta($post_id,'message_new',true);
		$message_not_new = get_post_meta($post_id,'message_not_new_'.$post_author,true);
		$message_not_new = (isset($message_not_new) && $message_not_new != "" && $message_not_new != "no"?$message_not_new:"no");
		if (($message_new == 1 || $message_new == "on" || $message_not_new == "no") && $post_author != $message_user_id) {
			$count_new_message = (int)get_user_meta($message_user_id,"wpqa_new_messages_count",true);
			$count_new_message--;
			update_user_meta($message_user_id,"wpqa_new_messages_count",($count_new_message <= 0?0:$count_new_message));
		}
		if (!isset($_GET["mobile"])) {
			wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Deleted successfully.","wpqa").'</p></div>','wpqa_session');
			$protocol = is_ssl() ? 'https' : 'http';
			$redirect_to = wp_unslash($protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			$redirect_to = (isset($_GET["page"]) && esc_html($_GET["page"]) != ""?esc_html($_GET["page"]):$redirect_to);
			wp_redirect((isset($_GET["show"]) && $_GET["show"] == "send"?esc_url_raw(add_query_arg("show","send"),wpqa_messages_permalink()):esc_url_raw(wpqa_messages_permalink())));
			exit;
		}
	}
}?>