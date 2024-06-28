<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Answers tab */
add_action('admin_menu','wpqa_add_admin_answer');
function wpqa_add_admin_answer() {
	add_comments_page(esc_html__('Comments','wpqa'),esc_html__('Comments','wpqa'),'moderate_comments','edit-comments.php?comment_status=comments');
	$active_groups = wpqa_options("active_groups");
	if ($active_groups == "on") {
		add_comments_page(esc_html__('Group Comments','wpqa'),esc_html__('Group Comments','wpqa'),'moderate_comments','edit-comments.php?comment_status=group-comments');
	}
	add_comments_page(esc_html__('Answers','wpqa'),esc_html__('Answers','wpqa'),'moderate_comments','edit-comments.php?comment_status=answers');
	add_comments_page(esc_html__('Best Answers','wpqa'),esc_html__('Best Answers','wpqa'),'moderate_comments','edit-comments.php?comment_status=best-answers');
}
/* Pre comment approved */
add_filter('pre_comment_approved','wpqa_pre_comment_approved','99',2);
if (!function_exists('wpqa_pre_comment_approved')) :
	function wpqa_pre_comment_approved($approved,$commentdata) {
		if (!is_user_logged_in() && $approved != "spam") {
			$comment_unlogged = wpqa_options("comment_unlogged");
			$approved = ($comment_unlogged == "draft"?0:1);
		}
		$custom_permission = wpqa_options("custom_permission");
		$user_id = get_current_user_id();
		if (!empty($commentdata['user_id'])) {
			global $wpdb;
			$user_is_login = get_userdata($commentdata['user_id']);
			$post_author = $wpdb->get_var($wpdb->prepare("SELECT post_author FROM $wpdb->posts WHERE ID = %d LIMIT 1",$commentdata['comment_post_ID']));
		}
		if ($custom_permission == "on" && !is_super_admin($user_id)) {
			if (is_user_logged_in()) {
				$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
				$get_post_type = get_post_type($commentdata["comment_post_ID"]);
				if ($get_post_type == wpqa_questions_type || $get_post_type == wpqa_asked_questions_type) {
					$approved = (isset($roles["approve_answer"]) && $roles["approve_answer"] == 1?1:0);
					$approve_answer_media = (isset($roles["approve_answer_media"]) && $roles["approve_answer_media"] == 1?"on":0);
					if ((isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) || (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name']))) :
						$answer_attached = true;
					endif;
				}else {
					$approved = (isset($roles["approve_comment"]) && $roles["approve_comment"] == 1?1:0);
				}
			}else {
				$approve_answer_media = wpqa_options("approve_answer_media");
				if ((isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) || (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name']))) :
					$answer_attached = true;
				endif;
			}
		}
		if (isset($answer_attached) && isset($approve_answer_media)) {
			$approved = ($approve_answer_media === "on"?1:0);
		}

		if (isset($user_is_login) && (is_super_admin($user_id) || $commentdata['user_id'] == $post_author || $user_is_login->has_cap('moderate_comments'))) {
			$approved = 1;
		}else {
			if (check_comment($commentdata['comment_author'],$commentdata['comment_author_email'],$commentdata['comment_author_url'],$commentdata['comment_content'],$commentdata['comment_author_IP'],$commentdata['comment_agent'],$commentdata['comment_type'])) {
				$approved = 1;
			}else {
				$approved = 0;
			}

			if (wp_check_comment_disallowed_list($commentdata['comment_author'],$commentdata['comment_author_email'],$commentdata['comment_author_url'],$commentdata['comment_content'],$commentdata['comment_author_IP'],$commentdata['comment_agent'])) {
				$approved = EMPTY_TRASH_DAYS?'trash':'spam';
			}
		}
		return $approved;
	}
endif;
/* Pre process comment */
add_filter('preprocess_comment','wpqa_comment_question_before');
if (!function_exists('wpqa_comment_question_before')) :
	function wpqa_comment_question_before($commentdata) {
		$post_id = $commentdata["comment_post_ID"];
		$get_post_type_comment = get_post_type($post_id);
		if (!is_admin() && ($get_post_type_comment == wpqa_questions_type || $get_post_type_comment == wpqa_asked_questions_type || $get_post_type_comment == "post" || $get_post_type_comment == "page")) {
			$the_captcha = 0;
			if ($get_post_type_comment == wpqa_questions_type || $get_post_type_comment == wpqa_asked_questions_type) {
				$the_captcha = wpqa_options("the_captcha_answer");
			}else {
				$the_captcha = wpqa_options("the_captcha_comment");
			}
			$the_captcha = (!isset($_POST['mobile'])?$the_captcha:0);
			$captcha_users = wpqa_options("captcha_users");
			$captcha_style = wpqa_options("captcha_style");
			$captcha_question = wpqa_options("captcha_question");
			$captcha_answer = wpqa_options("captcha_answer");
			$activate_editor_reply = wpqa_options("activate_editor_reply");
			if (isset($commentdata["comment_parent"]) && $commentdata["comment_parent"] == 0 && $activate_editor_reply != "on") {
				$reply_only = true;
			}
			if (isset($reply_only) && $the_captcha === "on" && ($captcha_users == "both" || ($captcha_users == "unlogged" && !is_user_logged_in()))) {
				if ($captcha_style == "google_recaptcha") {
					if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
						$secretKey = wpqa_options("secret_key_recaptcha");
						$data = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['g-recaptcha-response']);
						if (is_wp_error($data)) {
							if (defined('DOING_AJAX') && DOING_AJAX) {
								die(sprintf(esc_html__('%1$s ERROR %2$s: Robot verification failed, Please try again.','wpqa'),'<strong>','</strong>'));
							}else {
								wp_die(sprintf(esc_html__('%1$s ERROR %2$s: Robot verification failed, Please try again.','wpqa'),'<strong>','</strong>'));
							}
							exit;
						}else {
							$json = json_decode($data['body'],true);
						}
						if ((isset($json["success"]) && $json["success"] == true) || (isset($json["error-codes"]) && isset($json["error-codes"][0]) && $json["error-codes"][0] == "timeout-or-duplicate")) {
							//success
						}else {
							if (defined('DOING_AJAX') && DOING_AJAX) {
								die(sprintf(esc_html__('%1$s ERROR %2$s: Robot verification failed, Please try again.','wpqa'),'<strong>','</strong>'));
							}else {
								wp_die(sprintf(esc_html__('%1$s ERROR %2$s: Robot verification failed, Please try again.','wpqa'),'<strong>','</strong>'));
							}
							exit;
						}
					}else {
						if (defined('DOING_AJAX') && DOING_AJAX) {
							die(sprintf(esc_html__('%1$s ERROR %2$s: Please check on the reCAPTCHA box.','wpqa'),'<strong>','</strong>'));
						}else {
							wp_die(sprintf(esc_html__('%1$s ERROR %2$s: Please check on the reCAPTCHA box.','wpqa'),'<strong>','</strong>'));
						}
						exit;
					}
				}else {
					if (empty($_POST["wpqa_captcha"])) {
						if (defined('DOING_AJAX') && DOING_AJAX) {
							die(sprintf(esc_html__('%1$s ERROR %2$s: Please type a captcha.','wpqa'),'<strong>','</strong>'));
						}else {
							wp_die(sprintf(esc_html__('%1$s ERROR %2$s: Please type a captcha.','wpqa'),'<strong>','</strong>'));
						}
						exit;
					}
					if ($captcha_style == "question_answer") {
						if ($captcha_answer != $_POST["wpqa_captcha"]) {
							if (defined('DOING_AJAX') && DOING_AJAX)
								die(esc_html__('The captcha is incorrect, Please try again.','wpqa'));
							else
								wp_die(esc_html__('The captcha is incorrect, Please try again.','wpqa'));
							exit;
						}
					}else {
						if ($get_post_type_comment == wpqa_questions_type || $get_post_type_comment == wpqa_asked_questions_type) {
							$name_of_code_captcha = "wpqa_code_captcha_answer";
						}else {
							$name_of_code_captcha = "wpqa_code_captcha_comment";
						}
						if (isset($_SESSION[$name_of_code_captcha]) && $_SESSION[$name_of_code_captcha] != $_POST["wpqa_captcha"]) {
							if (defined('DOING_AJAX') && DOING_AJAX)
								die(esc_html__('The captcha is incorrect, Please try again.','wpqa'));
							else
								wp_die(esc_html__('The captcha is incorrect, Please try again.','wpqa'));
							exit;
						}
					}
				}
			}

			if ($get_post_type_comment == wpqa_questions_type || $get_post_type_comment == wpqa_asked_questions_type) {
				$user_id = get_current_user_id();
				$answer_per_question = wpqa_options("answer_per_question");
				if ($answer_per_question == "on") {
					$allow_to_do = wpqa_allow_to_answer_reply($post_id,$get_post_type_comment,$user_id);
					$allow_to_answer = $allow_to_do["allow_to_answer"];
					$allow_to_reply = $allow_to_do["allow_to_reply"];
					if ($allow_to_answer == true && $allow_to_reply != true && isset($commentdata["comment_parent"]) && $commentdata["comment_parent"] == 0) {
						wp_die(esc_html__("You have already answered this question.","wpqa"));
						exit;
					}
					if ($allow_to_answer == true && $allow_to_reply == true && $commentdata["comment_parent"] > 0) {
						wp_die(esc_html__("You have already replied this question.","wpqa"));
						exit;
					}
				}

				$answer_video = wpqa_options("answer_video");
				if ($answer_video == "on" && isset($_POST['video_answer_description']) && $_POST['video_answer_description'] == "on" && empty($_POST['video_answer_id'])) {
					wp_die(esc_html__("There are required fields (Video ID).","wpqa"));
					exit;
				}

				$answer_anonymously = wpqa_options("answer_anonymously");
				if ($answer_anonymously == "on") {
					if (isset($_POST['anonymously_answer']) && $_POST['anonymously_answer'] == "on") {
						$commentdata["user_ID"] = 0;
						$commentdata["user_id"] = 0;
						$commentdata["comment_author"] = "";
						$commentdata["comment_author_email"] = "";
						$commentdata["comment_author_url"] = "";
					}
				}

				if (is_user_logged_in()) {
					$user_is_login = get_userdata($user_id);
					$user_login_group = wpqa_get_user_group($user_is_login);
					$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
					$user_limit = wpqa_check_user_limit($user_id,$user_is_login,"answer");
					$expired_limit = (!is_super_admin($user_id) && isset($user_limit["expired"]) && $user_limit["expired"] == "expired"?"expired":"not");
				}

				$pay_answer = wpqa_options("pay_answer");
				$custom_pay_answer = get_post_meta($post_id,"custom_pay_answer",true);
				if ($custom_pay_answer == "on") {
					$pay_answer = get_post_meta($post_id,"pay_answer",true);
				}
				$pay_answer = apply_filters('wpqa_pay_answer',$pay_answer);
				if ($pay_answer == "on") {
					$custom_permission = wpqa_options("custom_permission");
					$check_pay_to_answer = wpqa_check_pay_to_answer($user_id,$custom_permission,(isset($roles) && is_array($roles)?$roles:array()),$post_id);
					if ($check_pay_to_answer == true) {
						wp_die(esc_html__("You need to pay first.","wpqa"));
					}
				}

				if (isset($expired_limit) && $expired_limit == "expired") {
					if (defined('DOING_AJAX') && DOING_AJAX) {
						die(wpqa_paid_subscriptions_message(esc_html__("You have reached the maximum for adding answers.","wpqa"),"warning"));
					}else {
						wp_die(wpqa_paid_subscriptions_message(esc_html__("You have reached the maximum for adding answers.","wpqa"),"warning"));
					}
				}
			}

			if ($get_post_type_comment == wpqa_questions_type || $get_post_type_comment == wpqa_asked_questions_type || $get_post_type_comment == "posts") {
				if (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) :
					require_once(ABSPATH . 'wp-admin/includes/image.php');
					require_once(ABSPATH . 'wp-admin/includes/file.php');
					$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/webp","image/gif","image/tiff","image/ico");
					if (!isset($_POST['mobile']) && !in_array($_FILES['featured_image']['type'],$types)) :
						wp_die(esc_html__("Attachment Error! Please upload image only.","wpqa"));
						exit;
					endif;
				endif;
			}

			$terms_active_comment = wpqa_options("terms_active_comment");
			if (!isset($reply_only) && $terms_active_comment == "on" && isset($_POST['agree_terms']) && $_POST['agree_terms'] != "on") {
				wp_die(esc_html__("There are required fields (Agree of the terms).","wpqa"));
				exit;
			}
		}
		$comment_editor = "comment_editor";
		if ($get_post_type_comment == wpqa_questions_type || $get_post_type_comment == wpqa_asked_questions_type) {
			$comment_editor = "answer_editor";
		}else if ($get_post_type_comment == "posts") {
			$comment_editor = "editor_group_post_comments";
		}
		$comment_editor = wpqa_options($comment_editor);
		$commentdata["comment_content"] = ($comment_editor == "on"?wpqa_esc_textarea($commentdata['comment_content']):wpqa_esc_textarea($commentdata['comment_content']));
		return $commentdata;
	}
endif;
/* Comment question */
add_action('comment_post','wpqa_comment_question');
if (!function_exists('wpqa_comment_question')) :
	function wpqa_comment_question($comment_id) {
		$get_comment = get_comment($comment_id);
		$post_id = $get_comment->comment_post_ID;
		$get_post = get_post($post_id);
		$comment_user_id = $get_comment->user_id;
		$post_type = $get_post->post_type;
		$get_current_user_id = get_current_user_id();
		if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type || $post_type == "posts") {
			if ((isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) || (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name']))) :
				require_once(ABSPATH . 'wp-admin/includes/media.php');
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				require_once(ABSPATH . 'wp-admin/includes/file.php');
			endif;

			if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
				$comment_attachment = wp_handle_upload($_FILES['attachment'],array('test_form' => false),current_time('mysql'));
				if (isset($comment_attachment['error'])) :
					wp_die('Attachment Error: ' . $comment_attachment['error']);
					exit;
				endif;
				$comment_attachment_data = array(
					'post_mime_type' => $comment_attachment['type'],
					'post_title'	 => preg_replace('/\.[^.]+$/','',basename($comment_attachment['file'])),
					'post_content'   => '',
					'post_status'	=> 'inherit',
					'post_author'	=> (isset($anonymously_answer)?0:($comment_user_id != "" || $comment_user_id != 0?$comment_user_id:0))
				);
				$comment_attachment_id = wp_insert_attachment($comment_attachment_data,$comment_attachment['file'],$post_id);
				$comment_attachment_metadata = wp_generate_attachment_metadata($comment_attachment_id,$comment_attachment['file']);
				wp_update_attachment_metadata($comment_attachment_id, $comment_attachment_metadata);
				update_comment_meta($comment_id,'added_file',$comment_attachment_id);
			endif;
			
			if (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) :
				$comment_featured_image = wp_handle_upload($_FILES['featured_image'],array('test_form' => false),current_time('mysql'));
				if (isset($comment_featured_image['error'])) :
					wp_die(esc_html__("Attachment Error:","wpqa").' '.$comment_featured_image['error']);
					exit;
				endif;
				$comment_featured_image_data = array(
					'post_mime_type' => $comment_featured_image['type'],
					'post_title'	 => preg_replace('/\.[^.]+$/','',basename($comment_featured_image['file'])),
					'post_content'   => '',
					'post_status'	 => 'inherit',
					'post_author'	 => (isset($anonymously_answer)?0:($get_current_user_id > 0?$get_current_user_id:0))
				);
				$comment_featured_image_id = wp_insert_attachment($comment_featured_image_data,$comment_featured_image['file'],$post_id);
				$comment_featured_image_metadata = wp_generate_attachment_metadata($comment_featured_image_id,$comment_featured_image['file']);
				wp_update_attachment_metadata($comment_featured_image_id, $comment_featured_image_metadata);
				update_comment_meta($comment_id,'featured_image',$comment_featured_image_id);
			endif;
		}
		if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
			$private_answer = wpqa_options("private_answer");
			if ($private_answer == "on") {
				if (isset($_POST['private_answer']) && $_POST['private_answer'] == "on") {
					update_comment_meta($comment_id,'private_answer',1);
				}
			}
			$answer_anonymously = wpqa_options("answer_anonymously");
			if ($answer_anonymously == "on") {
				if (isset($_POST['anonymously_answer']) && $_POST['anonymously_answer'] == "on") {
					$anonymously_answer = true;
				}
			}
			update_comment_meta($comment_id,'comment_type',"question");
			update_comment_meta($comment_id,'comment_vote',0);
			update_comment_meta($comment_id,'wpqa_reactions_count',0);
			$question_user_id = get_post_meta($post_id,"user_id",true);
			if ($question_user_id != "" && $question_user_id > 0) {
				update_comment_meta($comment_id,"answer_question_user","answer_question_user");
			}

			$answer_video = wpqa_options("answer_video");
			if ($answer_video == "on") {
				if (isset($_POST['video_answer_description']))
					update_comment_meta($comment_id,'video_answer_description',esc_html($_POST['video_answer_description']));
				
				if (isset($_POST['video_answer_type']))
					update_comment_meta($comment_id,'video_answer_type',esc_html($_POST['video_answer_type']));
					
				if (isset($_POST['video_answer_id']))
					update_comment_meta($comment_id,'video_answer_id',esc_html($_POST['video_answer_id']));
			}

			if (isset($anonymously_answer)) {
				update_comment_meta($comment_id,'anonymously_user',($get_current_user_id > 0?$get_current_user_id:"anonymously"));
			}
			
			wpqa_answer_notifications($get_comment,$get_post,$comment_id,$post_id,$comment_user_id);
		}else {
			if ($post_type == "posts") {
				update_comment_meta($comment_id,'comment_type',"comment_group");
			}
			if ($get_comment->comment_approved == 1) {
				wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Your comment has been added successfully.","wpqa").'</p></div>','wpqa_session');
				if ($comment_user_id > 0) {
					wpqa_notifications_activities($comment_user_id,"","",$post_id,$comment_id,"add_comment","activities");
				}
			}else {
				wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Your comment has been added successfully, It's under review.","wpqa").'</p></div>','wpqa_session');
				if ($comment_user_id > 0) {
					wpqa_notifications_activities($comment_user_id,"","","","","approved_comment","activities");
				}
			}
		}
		do_action("wpqa_action_after_add_comment",$comment_id,$post_type,$post_id);
		$gender = get_user_meta($get_current_user_id,'gender',true);
		$display_name = get_the_author_meta('display_name',$get_current_user_id);
		if ($gender != "") {
			update_comment_meta($comment_id,"wpqa_comment_gender",$gender);
		}
		if ($display_name != "") {
			update_comment_meta($comment_id,"wpqa_comment_author",$display_name);
		}
		wpqa_after_add_comment($get_comment,$comment_id,$post_id);
		if ($get_comment->comment_approved == 1) {
			update_comment_meta($comment_id,'comment_approved_before','yes');
		}
		wpqa_set_comment_cookies($get_comment,false);
	}
endif;
/* Answer notifications */
function wpqa_answer_notifications($get_comment,$get_post,$comment_id,$post_id,$comment_user_id) {
	$approved = wp_get_comment_status($comment_id);
	if ($approved == 1 || $approved == "approved") {
		if ($comment_user_id > 0) {
			wpqa_notifications_activities($comment_user_id,"","",$post_id,$comment_id,"add_answer","activities","","answer","","answer");
		}
		$post_type = $get_post->post_type;
		if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
			$way_sending_notifications = wpqa_options("way_sending_notifications_answers");
			$schedules_time_notification = wpqa_options("schedules_time_notification_answers");
			if ($way_sending_notifications == "cronjob" && $schedules_time_notification != "") {
				update_comment_meta($comment_id,'wpqa_comment_scheduled_email','yes');
				update_comment_meta($comment_id,'wpqa_comment_scheduled_notification','yes');
			}
		}
		update_post_meta($post_id,"comment_count",$get_post->comment_count);
		wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Your answer has been added successfully.","wpqa").'</p></div>','wpqa_session');
		wpqa_notifications_add_answer($get_comment,$get_post);
	}else {
		wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Your answer has been added successfully, It's under review.","wpqa").'</p></div>','wpqa_session');
		if ($comment_user_id > 0) {
			wpqa_notifications_activities($comment_user_id,"","","","","approved_answer","activities","","answer","","answer");
		}
	}

	if ($comment_user_id > 0) {
		wpqa_update_for_you($comment_user_id,$post_id);
	}
}
/* Count and send mails for comments */
function wpqa_after_add_comment($get_comment,$comment_id,$post_id) {
	$pay_answer = wpqa_options("pay_answer");
	if ($pay_answer == "on" || $get_comment->comment_approved == 1) {
		$get_post = get_post($post_id);
		$post_type = $get_post->post_type;
	}
	$user_id = get_current_user_id();
	$comment_user_id = $get_comment->user_id;
	if ($pay_answer == "on") {
		if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
			$_allow_to_answer = (int)get_user_meta($user_id,$user_id."_allow_to_answer",true);
			if ($_allow_to_answer > 0) {
				update_comment_meta($comment_id,'_paid_answer','paid');
			}
			if ($_allow_to_answer == "" || $_allow_to_answer < 0) {
				$_allow_to_answer = 0;
			}
			if ($_allow_to_answer > 0) {
				$_allow_to_answer--;
			}
			update_user_meta($user_id,$user_id."_allow_to_answer",$_allow_to_answer);
			delete_user_meta($user_id,"pay_to_answer_".$post_id);
			do_action("wpqa_update_payment_status",$user_id,$post_id,"question");
		}
	}
	if ($get_comment->comment_approved == 1) {
		$count_post_all = get_post_meta($post_id,"count_post_all",true);
		$count_post_comments = get_post_meta($post_id,"count_post_comments",true);
		$gender_author = get_user_meta($user_id,'gender',true);
		if ($gender_author == 1) {
			$male_comment_count = get_post_meta($post_id,"male_comment_count",true);
			$male_count_comments = get_post_meta($post_id,"male_count_comments",true);
		}else if ($gender_author == 2) {
			$female_comment_count = get_post_meta($post_id,"female_comment_count",true);
			$female_count_comments = get_post_meta($post_id,"female_count_comments",true);
		}else {
			$other_comment_count = get_post_meta($post_id,"other_comment_count",true);
			$other_count_comments = get_post_meta($post_id,"other_count_comments",true);
		}
		if ($count_post_all === "") {
			$count_post_all = wpqa_comment_counter($post_id,"parent_child");
			$count_post_comments = wpqa_comment_counter($post_id,"parent");
			if ($gender_author == 1) {
				$male_comment_count = wpqa_comment_counter($post_id,"parent_child","male_comment_count");
				$male_count_comments = wpqa_comment_counter($post_id,"parent","male_count_comments");
			}else if ($gender_author == 2) {
				$female_comment_count = wpqa_comment_counter($post_id,"parent_child","female_comment_count");
				$female_count_comments = wpqa_comment_counter($post_id,"parent","female_count_comments");
			}else {
				$other_comment_count = wpqa_comment_counter($post_id,"parent_child","other_comment_count");
				$other_count_comments = wpqa_comment_counter($post_id,"parent","other_count_comments");
			}
		}else {
			$count_post_all++;
			wpqa_notification_mention($get_comment->comment_content,"comment",$comment_id,$comment_user_id,$post_id);
			if ($gender_author == 1) {
				$male_comment_count++;
			}else if ($gender_author == 2) {
				$female_comment_count++;
			}else {
				$other_comment_count++;
			}
		}
		if ($get_comment->comment_parent == 0) {
			$count_post_comments++;
			if ($gender_author == 1) {
				$male_count_comments++;
			}else if ($gender_author == 2) {
				$female_count_comments++;
			}else {
				$other_count_comments++;
			}
		}
		update_post_meta($post_id,"count_post_all",$count_post_all);
		update_post_meta($post_id,"count_post_comments",$count_post_comments);
		if ($gender_author == 1) {
			update_post_meta($post_id,"male_comment_count",($male_comment_count < 0?0:$male_comment_count));
			update_post_meta($post_id,"male_count_comments",($male_count_comments < 0?0:$male_count_comments));
		}else if ($gender_author == 2) {
			update_post_meta($post_id,"female_comment_count",($female_comment_count < 0?0:$female_comment_count));
			update_post_meta($post_id,"female_count_comments",($female_count_comments < 0?0:$female_count_comments));
		}else {
			update_post_meta($post_id,"other_comment_count",($other_comment_count < 0?0:$other_comment_count));
			update_post_meta($post_id,"other_count_comments",($other_count_comments < 0?0:$other_count_comments));
		}
		/* Count comments or answers */
		$comment_user_id = $get_comment->user_id;
		if ($comment_user_id > 0 && ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type || $post_type == "post" || $post_type == "posts")) {
			if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
				$meta = "wpqa_answers_count";
			}else if ($post_type == "posts") {
				$meta = "wpqa_group_comments_count";
			}else {
				$meta = "wpqa_comments_count";
			}
			$count_meta = (int)get_user_meta($comment_user_id,$meta,true);
			$count_meta++;
			update_user_meta($comment_user_id,$meta,($count_meta < 0?0:$count_meta));
		}
		/* Count answers for asked questions */
		$get_user_id = get_post_meta($post_id,"user_id",true);
		if ($get_user_id > 0) {
			$count_site_asked_question_answers = get_option("wpqa_count_site_asked_question_answers");
			$count_site_asked_question_answers++;
			update_option("wpqa_count_site_asked_question_answers",$count_site_asked_question_answers);
			if ($comment_user_id > 0) {
				$count_meta = (int)get_user_meta($comment_user_id,"wpqa_count_asked_question_answers",true);
				$count_meta++;
				update_user_meta($comment_user_id,"wpqa_count_asked_question_answers",($count_meta < 0?0:$count_meta));
			}
		}
	}else {
		$send_email_draft_comments = wpqa_options("send_email_draft_comments");
		if ($send_email_draft_comments == "on") {
			$send_text = wpqa_send_mail(
				array(
					'content'    => wpqa_options("email_draft_comments"),
					'post_id'    => $post_id,
					'comment_id' => $comment_id,
				)
			);
			$email_title = wpqa_options("title_new_draft_comments");
			$email_title = ($email_title != ""?$email_title:esc_html__("New comment for review","wpqa"));
			$email_title = wpqa_send_mail(
				array(
					'content'    => $email_title,
					'title'      => true,
					'break'      => '',
					'post_id'    => $post_id,
					'comment_id' => $comment_id,
				)
			);
			wpqa_send_mails(
				array(
					'title'   => $email_title,
					'message' => $send_text,
				)
			);
		}
	}
}
/* Approve comment callback */
add_action('transition_comment_status','wpqa_approve_comment_callback',10,3);
if (!function_exists('wpqa_approve_comment_callback')) :
	function wpqa_approve_comment_callback($new_status,$old_status,$comment) {
		if ($old_status != $new_status) {
			$comment_user_id = $comment->user_id;
			$gender_author = get_user_meta($comment_user_id,'gender',true);
			$post_id = $comment->comment_post_ID;
			$get_post = get_post($post_id);
			$post_type = $get_post->post_type;
			$count_post_all = get_post_meta($post_id,"count_post_all",true);
			$count_post_comments = get_post_meta($post_id,"count_post_comments",true);
			if ($gender_author == 1) {
				$male_comment_count = get_post_meta($post_id,"male_comment_count",true);
				$male_count_comments = get_post_meta($post_id,"male_count_comments",true);
			}else if ($gender_author == 2) {
				$female_comment_count = get_post_meta($post_id,"female_comment_count",true);
				$female_count_comments = get_post_meta($post_id,"female_count_comments",true);
			}else {
				$other_comment_count = get_post_meta($post_id,"other_comment_count",true);
				$other_count_comments = get_post_meta($post_id,"other_count_comments",true);
			}
			if ($count_post_all === "") {
				$count_post_all = wpqa_comment_counter($post_id,"parent_child");
				$count_post_comments = wpqa_comment_counter($post_id,"parent");
				if ($gender_author == 1) {
					$male_comment_count = wpqa_comment_counter($post_id,"parent_child","male_comment_count");
					$male_count_comments = wpqa_comment_counter($post_id,"parent","male_count_comments");
				}else if ($gender_author == 2) {
					$female_comment_count = wpqa_comment_counter($post_id,"parent_child","female_comment_count");
					$female_count_comments = wpqa_comment_counter($post_id,"parent","female_count_comments");
				}else {
					$other_comment_count = wpqa_comment_counter($post_id,"parent_child","other_comment_count");
					$other_count_comments = wpqa_comment_counter($post_id,"parent","other_count_comments");
				}
			}else {
				if ($new_status == "approved") {
					$count_post_all++;
					if ($gender_author == 1) {
						$male_comment_count++;
					}else if ($gender_author == 2) {
						$female_comment_count++;
					}else {
						$other_comment_count++;
					}
					if ($comment->comment_parent == 0) {
						$count_post_comments++;
						if ($gender_author == 1) {
							$male_count_comments++;
						}else if ($gender_author == 2) {
							$female_count_comments++;
						}else {
							$other_count_comments++;
						}
					}
				}else if ($old_status == "approved") {
					$count_post_all--;
					if ($gender_author == 1) {
						$male_comment_count--;
					}else if ($gender_author == 2) {
						$female_comment_count--;
					}else {
						$other_comment_count--;
					}
					if ($comment->comment_parent == 0) {
						$count_post_comments--;
						if ($gender_author == 1) {
							$male_count_comments--;
						}else if ($gender_author == 2) {
							$female_count_comments--;
						}else {
							$other_count_comments--;
						}
					}
				}
			}
			update_post_meta($post_id,"count_post_all",($count_post_all < 0?0:$count_post_all));
			update_post_meta($post_id,"count_post_comments",($count_post_comments < 0?0:$count_post_comments));
			if ($gender_author == 1) {
				update_post_meta($post_id,"male_comment_count",($male_comment_count < 0?0:$male_comment_count));
				update_post_meta($post_id,"male_count_comments",($male_count_comments < 0?0:$male_count_comments));
			}else if ($gender_author == 2) {
				update_post_meta($post_id,"female_comment_count",($female_comment_count < 0?0:$female_comment_count));
				update_post_meta($post_id,"female_count_comments",($female_count_comments < 0?0:$female_count_comments));
			}else {
				update_post_meta($post_id,"other_comment_count",($other_comment_count < 0?0:$other_comment_count));
				update_post_meta($post_id,"other_count_comments",($other_count_comments < 0?0:$other_count_comments));
			}
			/* Count comments or answers */
			$comment_id = $comment->comment_ID;
			if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
				$best_answer_comment = get_comment_meta($comment_id,"best_answer_comment",true);
				if (isset($best_answer_comment) && isset($comment_id) && $best_answer_comment == "best_answer_comment") {
					$count_best_answer = true;
				}
				if (isset($count_best_answer)) {
					$wpqa_best_answer = (int)get_option("wpqa_best_answer");
				}
				if ($new_status == "approved") {
					if (isset($count_best_answer)) {
						$wpqa_best_answer++;
						update_post_meta($post_id,"the_best_answer",$comment_id);
					}
				}else if ($old_status == "approved") {
					if (isset($count_best_answer) && $new_status != "trash" && $new_status != "delete") {
						$wpqa_best_answer--;
						delete_post_meta($post_id,"the_best_answer");
					}
				}
				if (isset($count_best_answer)) {
					update_option("wpqa_best_answer",($wpqa_best_answer < 0?0:$wpqa_best_answer));
				}
			}
			if ($comment_user_id > 0 && ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type || $post_type == "post" || $post_type == "posts")) {
				if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
					$meta = "wpqa_answers_count";
				}else if ($post_type == "posts") {
					$meta = "wpqa_group_comments_count";
				}else {
					$meta = "wpqa_comments_count";
				}
				$count_meta = (int)get_user_meta($comment_user_id,$meta,true);
				if (isset($count_best_answer)) {
					$wpqa_count_best_answers = (int)get_user_meta($comment_user_id,"wpqa_count_best_answers",true);
				}
				if ($new_status == "approved") {
					$count_meta++;
					if (isset($count_best_answer)) {
						$wpqa_count_best_answers++;
						update_post_meta($post_id,"the_best_answer",$comment_id);
					}
				}else if ($old_status == "approved") {
					$count_meta--;
					if (isset($count_best_answer) && $new_status != "trash" && $new_status != "delete") {
						$wpqa_count_best_answers--;
						delete_post_meta($post_id,"the_best_answer");
					}
				}
				update_user_meta($comment_user_id,$meta,($count_meta < 0?0:$count_meta));
				if (isset($count_best_answer)) {
					update_user_meta($comment_user_id,"wpqa_count_best_answers",($wpqa_count_best_answers < 0?0:$wpqa_count_best_answers));
				}
			}
			/* Count answers for asked questions */
			if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
				$get_user_id = get_post_meta($post_id,"user_id",true);
				if ($get_user_id > 0) {
					$count_site_asked_question_answers = get_option("wpqa_count_site_asked_question_answers");
					if ($new_status == "approved") {
						$count_site_asked_question_answers++;
					}else if ($old_status == "approved" && $new_status != "trash" && $new_status != "delete") {
						$count_site_asked_question_answers--;
					}
					update_option("wpqa_count_site_asked_question_answers",$count_site_asked_question_answers);
					if ($comment_user_id > 0) {
						$count_meta = (int)get_user_meta($comment_user_id,"wpqa_count_asked_question_answers",true);
						if ($new_status == "approved") {
							$count_meta++;
						}else if ($old_status == "approved" && $new_status != "trash" && $new_status != "delete") {
							$count_meta--;
						}
						update_user_meta($comment_user_id,"wpqa_count_asked_question_answers",($count_meta < 0?0:$count_meta));
					}
				}
			}
			if ($new_status == 'approved') {
				$comment_approved_before = get_comment_meta($comment_id,'comment_approved_before',true);
				if ($comment_approved_before != "yes") {
					wpqa_notification_mention($comment->comment_content,"comment",$comment_id,$comment_user_id,$post_id);
					$time = current_time('mysql');
					$update_data = array('comment_ID' => $comment_id);
					$update_data['comment_date'] = $time;
					$update_data['comment_date_gmt'] = $time;
					wp_update_comment($update_data);
					if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
						if ($comment_user_id > 0) {
							wpqa_notifications_activities($comment_user_id,"","",$post_id,$comment_id,"approved_answer","notifications","","answer");
						}
						wpqa_notifications_add_answer($comment,$get_post);
					}else {
						if ($comment->user_id > 0) {
							wpqa_notifications_activities($comment->user_id,"","",$post_id,$comment_id,"approved_comment","notifications");
						}
					}
					update_comment_meta($comment_id,'comment_approved_before','yes');
					if ($post_type == "posts") {
						update_comment_meta($comment_id,'comment_type',"comment_group");
					}
					if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
						update_comment_meta($comment_id,'comment_type',"question");
						update_comment_meta($comment_id,'comment_vote',0);
						if ($post_type == wpqa_asked_questions_type) {
							$question_user_id = get_post_meta($post_id,"user_id",true);
							if ($question_user_id != "" && $question_user_id > 0) {
								update_comment_meta($comment_id,"answer_question_user","answer_question_user");
							}
						}
						$way_sending_notifications = wpqa_options("way_sending_notifications_answers");
						$schedules_time_notification = wpqa_options("schedules_time_notification_answers");
						if ($way_sending_notifications == "cronjob" && $schedules_time_notification != "") {
							update_comment_meta($comment_id,'wpqa_comment_scheduled_email','yes');
							update_comment_meta($comment_id,'wpqa_comment_scheduled_notification','yes');
						}
					}
				}
			}
		}
	}
endif;
/* All best answer */
if (!function_exists('wpqa_all_best_answers')) :
	function wpqa_all_best_answers() {
		global $wpdb;
		$comments = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments INNER JOIN $wpdb->commentmeta ON ( $wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id ) WHERE ( comment_approved = '0' OR comment_approved = '1' ) AND ( $wpdb->commentmeta.meta_key = 'best_answer_comment' AND $wpdb->commentmeta.meta_value = 'best_answer_comment' )");
		return $comments;
	}
endif;
/* All comments by post type */
if (!function_exists('wpqa_all_comments_of_post_type')) :
	function wpqa_all_comments_of_post_type($post_type = null) {
		global $wpdb;
		$post_type = (is_array($post_type)?$post_type:($post_type != ""?$post_type:"post"));
		$custom_post_type = "AND (";
		if (is_array($post_type)) {
			$key = 0;
			foreach ($post_type as $value) {
				if ($key != 0) {
					$custom_post_type .= " OR ";
				}
				$custom_post_type .= "post_type = '$value'";
				$key++;
			}
		}else {
			$custom_post_type .= "post_type = '$post_type'";
		}
		$custom_post_type .= ")";
		$comments = $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_post_ID in (SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' ".$custom_post_type.") AND comment_approved = '1'");
		return $comments;
	}
endif;
/* All comments counter */
function wpqa_comment_counter($post_id,$parent = "parent_child",$meta = "") {
	global $wpdb;
	$parent_sql = ($parent == "parent"?"AND $wpdb->comments.comment_parent = 0":"");
	$meta_inner = ($meta != ""?"INNER JOIN $wpdb->commentmeta ON ( $wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id )":"");
	if ($meta == "male_comment_count" || $meta == "male_count_comments") {
		$meta_value = "1";
	}else if ($meta == "female_comment_count" || $meta == "female_count_comments") {
		$meta_value = "2";
	}else if ($meta == "other_comment_count" || $meta == "other_count_comments") {
		$meta_value = "3";
	}
	$meta = ($meta != "" && isset($meta_value)?"AND ($wpdb->commentmeta.meta_key = 'wpqa_comment_gender' AND $wpdb->commentmeta.meta_value = '$meta_value')":"");
	$count = $wpdb->get_var("SELECT COUNT(comment_post_id) AS count FROM $wpdb->comments $meta_inner WHERE $wpdb->comments.comment_approved = 1 AND $wpdb->comments.comment_post_ID = $post_id $parent_sql $meta");
	return $count;
}
/* Update the comments count */
function wpqa_update_comments_count($post_id) {
	// count_post_comments - male_count_comments - female_count_comments - other_count_comments parent only
	// count_post_all - male_comment_count - female_comment_count - other_comment_count parent and child comments too
	$count_post_all = wpqa_comment_counter($post_id,"parent_child"); // With child comments too
	$count_post_comments = wpqa_comment_counter($post_id,"parent"); // The parents only
	update_post_meta($post_id,"count_post_all",$count_post_all);
	update_post_meta($post_id,"count_post_comments",$count_post_comments);

	$male_comment_count = wpqa_comment_counter($post_id,"parent_child","male_comment_count");
	$male_count_comments = wpqa_comment_counter($post_id,"parent","male_count_comments");
	update_post_meta($post_id,"male_comment_count",($male_comment_count < 0?0:$male_comment_count));
	update_post_meta($post_id,"male_count_comments",($male_count_comments < 0?0:$male_count_comments));

	$female_comment_count = wpqa_comment_counter($post_id,"parent_child","female_comment_count");
	$female_count_comments = wpqa_comment_counter($post_id,"parent","female_count_comments");
	update_post_meta($post_id,"female_comment_count",($female_comment_count < 0?0:$female_comment_count));
	update_post_meta($post_id,"female_count_comments",($female_count_comments < 0?0:$female_count_comments));

	$other_comment_count = wpqa_comment_counter($post_id,"parent_child","other_comment_count");
	$other_count_comments = wpqa_comment_counter($post_id,"parent","other_count_comments");
	update_post_meta($post_id,"other_comment_count",($other_comment_count < 0?0:$other_comment_count));
	update_post_meta($post_id,"other_count_comments",($other_count_comments < 0?0:$other_count_comments));
}
/* Count the comments */
function wpqa_count_comments($post_id,$return = "count_post_all",$count_meta = "like_comments_only") {
	if ($count_meta == "like_comments_only") {
		$count_comment_only = wpqa_options("count_comment_only");
		if ($return == "male_comment_count" || $return == "male_count_comments") {
			$return = ($count_comment_only == "on"?"male_count_comments":"male_comment_count");
		}else if ($return == "female_comment_count" || $return == "female_count_comments") {
			$return = ($count_comment_only == "on"?"female_count_comments":"female_comment_count");
		}else if ($return == "other_comment_count" || $return == "other_count_comments") {
			$return = ($count_comment_only == "on"?"other_count_comments":"other_comment_count");
		}else {
			$return = ($count_comment_only == "on"?"count_post_comments":"count_post_all");
		}
	}
	$count_post_all = get_post_meta($post_id,"count_post_all",true);
	if ($count_post_all === "") {
		wpqa_update_comments_count($post_id);
	}
	$block_count = wpqa_count_blocked_comments($post_id,$return);
	$count = (int)get_post_meta($post_id,$return,true);
	$count = (int)($count-$block_count);
	$count = ($count > 0?$count:0);
	return $count;
}
/* Count the blocked comments */
function wpqa_count_blocked_comments($post_id,$return = "count_post_all") {
	$block_count = 0;
	$block_users = wpqa_options("block_users");
	if ($block_users == "on") {
		$user_id = get_current_user_id();
		if ($user_id > 0) {
			$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
			if (is_array($get_block_users) && !empty($get_block_users)) {
				$author__in = array("author__in" => $get_block_users);
				$parent = ($return == "count_post_all"?array():array('parent' => 0));
				$count_comment_only = wpqa_options("count_comment_only");
				if ($return == "male_comment_count" || $return == "male_count_comments") {
					$meta_value = "1";
				}else if ($return == "female_comment_count" || $return == "female_count_comments") {
					$meta_value = "2";
				}else if ($return == "other_comment_count" || $return == "other_count_comments") {
					$meta_value = "3";
				}
				$meta_query = (isset($meta_value)?array("meta_query" => array(array("key" => "wpqa_comment_gender","value" => $meta_value,"compare" => "="))):array());
				$comments_args = array_merge($meta_query,$author__in,$parent,array('post_id' => $post_id,'status' => 'approve'));
				$get_comments = get_comments($comments_args);
				$block_count = (is_array($get_comments) && !empty($get_comments)?count($get_comments):0);
			}
		}
	}
	$count = (int)($block_count > 0?$block_count:0);
	return $count;
}
/* Action delete comment */
add_action('wpqa_init','wpqa_delete_comment');
if (!function_exists('wpqa_delete_comment')) :
	function wpqa_delete_comment() {
		if (isset($_GET["mobile"]) || (isset($_GET['wpqa_delete_nonce']) && wp_verify_nonce($_GET['wpqa_delete_nonce'],'wpqa_delete_nonce') && !is_admin() && isset($_GET["delete_comment"]) && $_GET["delete_comment"] != "")) {
			$comment_id  = (int)(isset($_GET["delete_comment"])?$_GET["delete_comment"]:(isset($_GET['id'])?$_GET['id']:""));
			$get_comment = get_comment($comment_id);
			if (isset($get_comment) && $comment_id > 0 && isset($get_comment->comment_approved) && $get_comment->comment_approved == 1) {
				$comment_user_id    = $get_comment->user_id;
				$post_id            = $get_comment->comment_post_ID;
				$user_id            = get_current_user_id();
				$comment_type       = get_comment_meta($comment_id,"comment_type",true);
				$can_delete_comment = wpqa_options("can_delete_comment");
				$delete_comment     = wpqa_options("delete_comment");
				$is_super_admin     = is_super_admin($user_id);
				
				if ($comment_type == "comment_group") {
					$group_id = get_post_meta($post_id,"group_id",true);
					$group_moderators = get_post_meta($group_id,"group_moderators",true);
					$group_id = get_post_meta($post_id,"group_id",true);
					$group_moderators = get_post_meta($group_id,"group_moderators",true);
					if ($user_id > 0 && is_array($group_moderators) && in_array($user_id,$group_moderators)) {
						$activate_delete = true;
					}
				}

				if (isset($activate_delete) || ($comment_user_id > 0 && $comment_user_id == $user_id && $can_delete_comment == "on") || $is_super_admin) {
					$comment_type = ($comment_type == "question"?"answer":"comment");
					if ($user_id > 0) {
						wpqa_notifications_activities($user_id,"","","","","delete_".$comment_type,"activities","",$comment_type);
					}
					if ($comment_user_id > 0 && $user_id != $comment_user_id) {
						wpqa_notifications_activities($comment_user_id,"","","","","delete_".$comment_type,"notifications","",$comment_type);
					}
					do_action("wpqa_after_deleted_comment",$comment_id);
					if ($delete_comment == "trash" && !$is_super_admin) {
						wp_trash_comment($comment_id);
					}else {
						wp_delete_comment($comment_id,true);
					}
					if (!isset($_GET["mobile"])) {
						wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Deleted successfully.","wpqa").'</p></div>','wpqa_session');
						$protocol    = is_ssl() ? 'https' : 'http';
						$redirect_to = esc_url(remove_query_arg(array('activate_delete','delete_comment','wpqa_delete_nonce'),wp_unslash($protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])));
						$redirect_to = (isset($_GET["page"]) && esc_html($_GET["page"]) != ""?esc_html($_GET["page"]):$redirect_to);
						if ($comment_type == "comment_group") {
							$redirect_to = wpqa_custom_permalink($post_id,"view_group_posts","view_group_post");
						}
						wp_redirect($redirect_to);
						exit;
					}
				}
			}
		}
	}
endif;
/* Before trash comment */
add_action("trash_comment","wpqa_trash_comment");
function wpqa_trash_comment($comment_id) {
	if ($comment_id > 0) {
		$best_answer_comment = get_comment_meta($comment_id,"best_answer_comment",true);
		if (isset($best_answer_comment) && isset($comment_id) && $best_answer_comment == "best_answer_comment") {
			$comment = get_comment($comment_id);
			$comment_user_id = $comment->user_id;
			$post_id = $comment->comment_post_ID;
			if ($comment->comment_approved == 1) {
				$wpqa_best_answer = (int)get_option("wpqa_best_answer");
				$wpqa_best_answer--;
				delete_post_meta($post_id,"the_best_answer");
				update_option("wpqa_best_answer",($wpqa_best_answer < 0?0:$wpqa_best_answer));
				if ($comment_user_id > 0) {
					$wpqa_count_best_answers = (int)get_user_meta($comment_user_id,"wpqa_count_best_answers",true);
					$wpqa_count_best_answers--;
					update_user_meta($comment_user_id,"wpqa_count_best_answers",($wpqa_count_best_answers < 0?0:$wpqa_count_best_answers));
				}
			}
		}
	}
}
/* Before delete comment */
add_action('delete_comment','wpqa_before_delete_comment');
if (!function_exists('wpqa_before_delete_comment')) :
	function wpqa_before_delete_comment($comment_id) {
		if ($comment_id > 0) {
			$get_comment = get_comment($comment_id);
			$post_id = $get_comment->comment_post_ID;
			$user_id = $get_comment->user_id;
			$args = array(
				"nopaging"   => true,
				"post_type"  => "report",
				"meta_query" => array(
					array(
						"key"     => "report_comment_id",
						"value"   => $comment_id,
						"compare" => "=",
					)
				)
			);
			$get_posts = get_posts($args);
			$best_answer_comment = get_comment_meta($comment_id,"best_answer_comment",true);
			if (isset($best_answer_comment) && isset($comment_id) && $best_answer_comment == "best_answer_comment" && $get_comment->comment_approved == 1) {
				$wpqa_best_answer = (int)get_option("wpqa_best_answer");
				$wpqa_best_answer--;
				delete_post_meta($post_id,"the_best_answer");
				update_option("wpqa_best_answer",($wpqa_best_answer < 0?0:$wpqa_best_answer));
				if ($user_id > 0) {
					$wpqa_count_best_answers = (int)get_user_meta($user_id,"wpqa_count_best_answers",true);
					$wpqa_count_best_answers--;
					update_user_meta($user_id,"wpqa_count_best_answers",($wpqa_count_best_answers < 0?0:$wpqa_count_best_answers));
				}
			}
			foreach ($get_posts as $report_post) {
				wp_delete_post($report_post->ID,true);
			}
		}
		$remove_best_answer_stats = wpqa_options("remove_best_answer_stats");
		$active_points = wpqa_options("active_points");
		if ($remove_best_answer_stats == "on" && $active_points == "on") {
			$comment_approved_before = get_comment_meta($comment_id,'comment_approved_before',true);
			if ($comment_approved_before == "yes") {
				$post_id = $get_comment->comment_post_ID;
				$user_id = $get_comment->user_id;
				$best_answer_comment = get_comment_meta($comment_id,"best_answer_comment",true);
				$user_author = get_post_field('post_author',$post_id);
				if ($user_id > 0) {
					$point_best_answer = (int)wpqa_options("point_best_answer");
					$point_best_answer = apply_filters("wpqa_point_best_answer",$point_best_answer,$post_id);
					$point_add_comment = (int)wpqa_options("point_add_comment");
					if ($user_id != $user_author && $point_add_comment > 0) {
						wpqa_add_points($user_id,$point_add_comment,"-","delete_answer",$post_id,$comment_id);
					}
				}
				
				if (isset($best_answer_comment) && isset($comment_id) && $best_answer_comment == "best_answer_comment") {
					if ($user_id > 0 && $point_best_answer > 0) {
						wpqa_add_points($user_id,$point_best_answer,"-","delete_best_answer",$post_id,$comment_id);
					}
					
					$point_back_option = wpqa_options("point_back");
					if ($point_back_option == "on" && $user_id != $user_author) {
						$point_back_number = (int)wpqa_options("point_back_number");
						$point_back = get_post_meta($post_id,"point_back",true);
						$what_point = (int)get_post_meta($post_id,"what_point",true);
						
						if ($point_back_number > 0) {
							$what_point = $point_back_number;
						}
						
						if ($point_back == "yes" && $user_author > 0) {
							$what_point = (int)($what_point > 0?$what_point:wpqa_options("question_points"));
							wpqa_add_points($user_author,$what_point,"-","point_removed",$post_id,$comment_id);
						}
						
						if ($user_author > 0) {
							wpqa_notifications_activities($user_author,"","","","","point_removed","notifications");
						}
					}
				}
			}
		}
	}
endif;
/* Comment attachment */
add_filter("wpqa_comment_fields","wpqa_comment_attachment",1,2);
add_filter("wpqa_edit_comment_fields","wpqa_comment_attachment",1,4);
if (!function_exists('wpqa_comment_attachment')) :
	function wpqa_comment_attachment($out,$type,$comment_id = 0,$get_post = object) {
		if (isset($get_post) && isset($get_post->ID)) {
			$post = $get_post;
		}
		if ((is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || (isset($post) && isset($post->post_type) && (wpqa_questions_type == $post->post_type || wpqa_asked_questions_type == $post->post_type))) || (is_singular("posts") || (isset($post) && isset($post->post_type) && 'posts' == $post->post_type))) {
			if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || (isset($post) && isset($post->post_type) && (wpqa_questions_type == $post->post_type || wpqa_asked_questions_type == $post->post_type))) {
				$attachment_answer = wpqa_options("attachment_answer");
				if ($attachment_answer == "on") {
					if ($type == "edit") {
						$added_file = get_comment_meta($comment_id,"added_file",true);
						if ($added_file != "") {
							$out .= '<ul class="wpqa-delete-attachment"><li><a class="btn btn__primary btn__sm" target="_blank" href="'.wp_get_attachment_url($added_file).'"><i class="icon-attach"></i>'.esc_html__('Attachment','wpqa').'</a> <a class="wpqa-remove-image btn btn__danger btn__sm" data-name="added_file" data-type="comment_meta" data-id="'.$comment_id.'" data-nonce="'.wp_create_nonce("wpqa_remove_image").'" href="#"><i class="icon-trash"></i>'.esc_html__('Delete','wpqa').'</a><div class="loader_2 loader_4"></div></li></ul>';
						}
					}
					$out .= '<div class="wpqa_form wpqa_attachment_comment">
						<label for="attachment">'.esc_html__('Attachment','wpqa').'</label>
						<div class="fileinputs">
							<input type="file" name="attachment" id="attachment">
							<div class="fakefile">
								<button type="button">'.esc_html__('Select file','wpqa').'</button>
								<span>'.esc_html__('Browse','wpqa').'</span>
							</div>
							<i class="icon-camera"></i>
						</div>
					</div>
					<div class="clearfix"></div>';
				}
			}
			
			if (isset($post) && isset($post->post_type) && 'posts' == $post->post_type) {
				$featured_image_answer = wpqa_options("featured_image_group_post_comments");
			}else {
				$featured_image_answer = wpqa_options("featured_image_answer");
			}
			if ($featured_image_answer == "on") {
				if ($type == "edit") {
					$featured_image = get_comment_meta($comment_id,"featured_image",true);
					if ($featured_image != "") {
						$out .= '<div class="clearfix"></div>
						<div class="wpqa-delete-image d-flex align-items-center mb-4">
							<span class="wpqa-delete-image-span mr-4">'.wpqa_get_aq_resize_img(250,250,"",$featured_image,"no","").'</span>
							<div class="clearfix"></div>
							<div class="button-default wpqa-remove-image btn btn__danger btn__small__width" data-name="featured_image" data-type="comment_meta" data-id="'.$comment_id.'" data-image="'.$featured_image.'" data-nonce="'.wp_create_nonce("wpqa_remove_image").'">'.esc_html__("Delete","wpqa").'</div>
							<div class="loader_2 loader_4"></div>
						</div>';
					}
				}
				$out .= '<div class="wpqa_form wpqa_featured_comment">
					<label for="featured_image">'.esc_html__('Featured image','wpqa').'</label>
					<div class="fileinputs">
						<input type="file" name="featured_image" id="featured_image">
						<div class="fakefile">
							<button type="button">'.esc_html__('Select file','wpqa').'</button>
							<span>'.esc_html__('Browse','wpqa').'</span>
						</div>
						<i class="icon-camera"></i>
					</div>
				</div>
				<div class="clearfix"></div>';
			}
		}
		if ($comment_id > 0) {
			return $out;
		}else {
			echo ($out);
		}
	}
endif;
/* Anonymously answer */
add_filter("comment_form_field_comment","wpqa_comment_anonymously_answer",1);
function wpqa_comment_anonymously_answer($fields) {
	$answer_anonymously = wpqa_options("answer_anonymously");
	if ($answer_anonymously == "on" && (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type))) {
		$default_image_anonymous = wpqa_image_url_id(wpqa_options("default_image_anonymous"));
		$fields .= '<div class="clearfix"></div>
		<p class="wpqa_checkbox_p ask_anonymously_p">
			<label for="anonymously_answer">
				<span class="wpqa_checkbox"><input type="checkbox" id="anonymously_answer" class="ask_anonymously" name="anonymously_answer" value="on"'.(isset($_POST['anonymously_answer']) && $_POST['anonymously_answer'] == "on"?" checked='checked'":"").'></span>
				<span class="wpqa_checkbox_span">'.esc_html__("Answer Anonymously","wpqa").'</span>';
				if (is_user_logged_in()) {
					$user_id = get_current_user_id();
					$display_name = get_the_author_meta("display_name",$user_id);
					$fields .= '<span class="anonymously_span ask_named">'.wpqa_get_user_avatar(array("user_id" => (isset($user_id) && $user_id > 0?$user_id:0),"size" => 25,"user_name" => (isset($user_id) && $user_id > 0?$display_name:""))).'<span>'.$display_name.' '.esc_html__("answers","wpqa").'</span>
					</span>
					<span class="anonymously_span ask_none">
						<img alt="'.esc_attr__("Anonymous","wpqa").'" src="'.($default_image_anonymous != ""?wpqa_get_aq_resize_url(esc_url($default_image_anonymous),25,25):plugin_dir_url(dirname(__FILE__)).'images/avatar.png').'">
						<span>'.esc_html__("Anonymous answers","wpqa").'</span>
					</span>';
				}
			$fields .= '</label>
		</p>';
	}
	return $fields;
}
/* Private answer */
add_filter("wpqa_comment_fields","wpqa_comment_private_answer",2,4);
add_filter("wpqa_edit_comment_fields","wpqa_comment_private_answer",2,4);
function wpqa_comment_private_answer($fields,$type,$comment_id = 0,$get_post = object) {
	if (isset($get_post) && isset($get_post->ID)) {
		$post = $get_post;
	}else {
		global $post;
	}
	$private_answer = wpqa_options("private_answer");
	if (isset($post) && isset($post->post_type) && (wpqa_questions_type == $post->post_type || wpqa_asked_questions_type == $post->post_type) && $private_answer == "on") {
		if ($comment_id > 0) {
			$get_private_answer = get_comment_meta($comment_id,"private_answer",true);
		}
		$fields .= '<div class="clearfix"></div>
		<p class="wpqa_checkbox_p ask_private_answer_p">
			<label for="private_answer">
				<span class="wpqa_checkbox"><input type="checkbox" id="private_answer" class="ask_anonymously" name="private_answer" value="on"'.($type == "add" && isset($_POST['private_answer']) && $_POST['private_answer'] == "on"?" checked='checked'":($type == "edit" && ((isset($_POST['private_answer']) && $_POST['private_answer'] == "on") || (empty($_POST['private_answer']) && $get_private_answer == 1))?" checked='checked'":"")).'></span>
				<span class="wpqa_checkbox_span">'.esc_html__("Private answer?","wpqa").'</span';
			$fields .= '</label>
		</p>';
	}
	if ($comment_id > 0) {
		return $fields;
	}else {
		echo ($fields);
	}
}
/* Comment captcha */
add_filter("comment_form_field_comment","wpqa_comment_captcha",3);
if (!function_exists('wpqa_comment_captcha')) :
	function wpqa_comment_captcha($fields) {
		global $post;
		$the_captcha = wpqa_options("the_captcha_".(isset($post) && isset($post->post_type) && (wpqa_questions_type == $post->post_type || wpqa_asked_questions_type == $post->post_type)?"answer":"comment"));
		$fields .= '<div class="wpqa_error"></div>'.wpqa_add_captcha($the_captcha,(isset($post) && isset($post->post_type) && (wpqa_questions_type == $post->post_type || wpqa_asked_questions_type == $post->post_type)?"answer":"comment"),rand(0000,9999),"comment");
		return $fields;
	}
endif;
/* Terms and privacy */
add_filter("comment_form_field_comment","wpqa_comment_terms",4);
function wpqa_comment_terms($fields) {
	$terms_active_comment = wpqa_options("terms_active_comment");
	if ($terms_active_comment == "on") {
		$terms_checked_comment = wpqa_options("terms_checked_comment");
		if ((isset($_POST['agree_terms']) && $_POST['agree_terms'] == "on") || ($terms_checked_comment == "on" && empty($_POST))) {
			$active_terms = true;
		}
		$terms_link_comment = wpqa_options("terms_link_comment");
		$terms_page_comment = wpqa_options('terms_page_comment');
		$terms_active_target_comment = wpqa_options('terms_active_target_comment');
		$privacy_policy_comment = wpqa_options('privacy_policy_comment');
		$privacy_active_target_comment = wpqa_options('privacy_active_target_comment');
		$privacy_page_comment = wpqa_options('privacy_page_comment');
		$privacy_link_comment = wpqa_options('privacy_link_comment');
		$fields .= '<div class="clearfix"></div>
		<div class="wpqa_form wpqa_terms_comment">
			<p class="wpqa_checkbox_p">
				<label for="agree_terms_comment">
					<span class="wpqa_checkbox"><input type="checkbox" id="agree_terms_comment" name="agree_terms" value="on" '.(isset($active_terms) == "on"?"checked='checked'":"").'></span>
					<span class="wpqa_checkbox_span">'.sprintf((is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)?esc_html__('By answering, you agree to the %1$s Terms of Service %2$s %3$s.','wpqa'):esc_html__('By commenting, you agree to the %1$s Terms of Service %2$s %3$s.','wpqa')),'<a target="'.($terms_active_target_comment == "same_page"?"_self":"_blank").'" href="'.esc_url(isset($terms_link_comment) && $terms_link_comment != ""?$terms_link_comment:(isset($terms_page_comment) && $terms_page_comment != ""?get_page_link($terms_page_comment):"#")).'">','</a>',($privacy_policy_comment == "on"?" ".sprintf(esc_html__('and %1$s Privacy Policy %2$s','wpqa'),'<a target="'.($privacy_active_target_comment == "same_page"?"_self":"_blank").'" href="'.esc_url(isset($privacy_link_comment) && $privacy_link_comment != ""?$privacy_link_comment:(isset($privacy_page_comment) && $privacy_page_comment != ""?get_page_link($privacy_page_comment):"#")).'">','</a>'):"")).'<span class="required">*</span></span>
				</label>
			</p>
		</div>';
	}
	return $fields;
}
/* Comment video */
add_filter("wpqa_comment_fields","wpqa_comment_video",3,4);
add_filter("wpqa_edit_comment_fields","wpqa_comment_video",3,4);
if (!function_exists('wpqa_comment_video')) :
	function wpqa_comment_video($out,$type,$comment_id = 0,$get_post = object) {
		if (isset($get_post) && isset($get_post->ID)) {
			$post = $get_post;
		}else {
			global $post;
		}

		$posted_video = array();
		
		$fields = array(
			'video_answer_description','video_answer_type','video_answer_id'
		);
		
		foreach ($fields as $field) :
			if (isset($_POST[$field])) $posted_video[$field] = $_POST[$field]; else $posted_video[$field] = '';
		endforeach;

		$answer_video = wpqa_options("answer_video");
		if (isset($post) && isset($post->post_type) && (wpqa_questions_type == $post->post_type || wpqa_asked_questions_type == $post->post_type) && $answer_video == "on") {
			if ($type == "edit") {
				$video_answer_description = get_comment_meta($comment_id,"video_answer_description",true);
				$video_answer_type = get_comment_meta($comment_id,"video_answer_type",true);
				$video_answer_id = get_comment_meta($comment_id,"video_answer_id",true);
			}
			
			$out .= '<div class="wpqa_form wpqa_video_comment">
				<p class="wpqa_checkbox_p">
					<label for="video_answer_description">
						<span class="wpqa_checkbox"><input type="checkbox" id="video_answer_description" class="video_answer_description_input" name="video_answer_description" value="on"'.($type == "add" && isset($posted_video['video_answer_description']) && $posted_video['video_answer_description'] == "on"?" checked='checked'":($type == "edit" && ((isset($posted_video['video_answer_description']) && $posted_video['video_answer_description'] == "on") || (empty($posted_video['video_answer_description']) && $video_answer_description == "on"))?" checked='checked'":"")).'></span>
						<span class="wpqa_checkbox_span">'.esc_html__("Add a Video to describe the problem better.","wpqa").'</span>
					</label>
				</p>

				<div class="video_answer_description wpqa_hide"'.($type == "add" && isset($posted_video['video_answer_description']) && $posted_video['video_answer_description'] == "on"?" style='display:block;'":($type == "edit" && ((isset($posted_video['video_answer_description']) && $posted_video['video_answer_description'] == "on") || $video_answer_description == "on")?" style='display:block;'":"")).'>
					<p>
						<label for="video_answer_type">'.esc_html__("Video type","wpqa").'</label>
						<span class="styled-select">
							<select class="form-control" id="video_answer_type" name="video_answer_type">
								<option value="youtube"'.($type == "add" && isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "youtube"?' selected="selected"':($type == "edit"?((isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "youtube") || (isset($video_answer_type) && $video_answer_type == "youtube")?' selected="selected"':''):'')).'>Youtube</option>
								<option value="vimeo"'.($type == "add" && isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "vimeo"?' selected="selected"':($type == "edit"?((isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "vimeo") || (isset($video_answer_type) && $video_answer_type == "vimeo")?' selected="selected"':''):'')).'>Vimeo</option>
								<option value="daily"'.($type == "add" && isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "daily"?' selected="selected"':($type == "edit"?((isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "daily") || (isset($video_answer_type) && $video_answer_type == "daily")?' selected="selected"':''):'')).'>Dailymotion</option>
								<option value="facebook"'.($type == "add" && isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "facebook"?' selected="selected"':($type == "edit"?((isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "facebook") || (isset($video_answer_type) && $video_answer_type == "facebook")?' selected="selected"':''):'')).'>Facebook</option>
								<option value="tiktok"'.($type == "add" && isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "tiktok"?' selected="selected"':($type == "edit"?((isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "tiktok") || (isset($video_answer_type) && $video_answer_type == "tiktok")?' selected="selected"':''):'')).'>TikTok</option>
							</select>
						</span>
						<i class="icon-video"></i>
						<span class="form-description">'.esc_html__("Choose from here the video type.","wpqa").'</span>
					</p>
					
					<p>
						<label for="video_answer_id">'.esc_html__("Video ID","wpqa").'</label>
						<input name="video_answer_id" id="video_answer_id" class="form-control video_answer_id" type="text" value="'.esc_attr($type == "add" && isset($posted_video['video_answer_id'])?$posted_video['video_answer_id']:($type == "edit"?(isset($posted_video['video_answer_id']) && $posted_video['video_answer_id'] != ""?$posted_video['video_answer_id']:$video_answer_id):"")).'">
						<i class="icon-play"></i>
						<span class="form-description">'.esc_html__('Put Video ID here: https://www.youtube.com/watch?v=sdUUx5FdySs Ex: "sdUUx5FdySs".','wpqa').'</span>
					</p>
				</div>
			</div>
			<div class="clearfix"></div>';
		}
		if ($comment_id > 0) {
			return $out;
		}else {
			echo ($out);
		}
	}
endif;
/* Can add answers */
add_filter("wpqa_can_add_answer","wpqa_can_add_answer",1,5);
function wpqa_can_add_answer($return,$user_id,$custom_permission,$roles,$post) {
	$post_id = $post->ID;
	$post_type = $post->post_type;
	$post_author = $post->post_author;
	$is_super_admin = is_super_admin($user_id);
	if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
		$question_category = wp_get_post_terms($post_id,wpqa_question_categories,array("fields" => "all"));
		if (isset($question_category[0])) {
			$category_new = get_term_meta($question_category[0]->term_id,prefix_terms."new",true);
			$category_special = get_term_meta($question_category[0]->term_id,prefix_terms."special",true);
		}
		$add_answer = wpqa_options("add_answer");
	}else {
		$add_comment = wpqa_options("add_comment");
	}
	if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
		$anonymously_user = get_post_meta($post_id,'anonymously_user',true);
		$yes_new = 1;
		if (have_comments()) {
			if (isset($question_category[0]) && $category_new == "on") {
				$yes_new = 0;
				if ($user_id > 0 && $post_author != $user_id && $anonymously_user != $user_id) {
					$yes_new = 1;
				}
				if ($is_super_admin) {
					$yes_new = 0;
				}
			}else {
				$yes_new = 0;
			}
		}else {
			if (isset($question_category[0]) && $category_new == "on") {
				if (isset($post_author) && $user_id > 0 && (($post_author == $user_id) || ($anonymously_user == $user_id))) {
					$yes_new = 1;
				}
				if ($post_id && $user_id > 0 && (($post_author == $user_id) || ($anonymously_user == $user_id))) {
					$yes_new = 1;
				}
			}else if (isset($question_category[0]) && $category_new != "on") {
				$yes_new = 0;
			}
			
			if (empty($question_category[0]) || $is_super_admin) {
				$yes_new = 0;
			}
		}
	}

	$no_allow_to_answer = true;
	if (($post_type != wpqa_questions_type && $post_type != wpqa_asked_questions_type) || (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && $yes_new != 1)) {
		if ((($post_type != wpqa_questions_type && $post_type != wpqa_asked_questions_type) && ($is_super_admin || $custom_permission != "on" || (is_user_logged_in() && $custom_permission == "on" && isset($roles["add_comment"]) && $roles["add_comment"] == 1) || (!is_user_logged_in() && $add_comment == "on"))) || (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && ($is_super_admin || $custom_permission != "on" || (is_user_logged_in() && $custom_permission == "on" && isset($roles["add_answer"]) && $roles["add_answer"] == 1) || (!is_user_logged_in() && $add_answer == "on")))) {
			$yes_special = 1;
			if (have_comments()) {
				$yes_special = 0;
			}else {
				if (isset($question_category[0]) && $category_special == "on") {
					if (isset($post_author) && $user_id > 0 && (($post_author == $user_id) || ($anonymously_user == $user_id))) {
						$yes_special = 1;
					}
				}else if (isset($question_category[0]) && $category_special != "on") {
					$yes_special = 0;
				}
				
				if (!isset($question_category[0]) || $is_super_admin) {
					$yes_special = 0;
				}
			}
			if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && $yes_special == 1) {
				$no_allow_to_answer = false;
				echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry this question is a special, The admin must answer first.","wpqa").'</p></div>';
			}else {
				$answer_per_question = wpqa_options("answer_per_question");
				if ($answer_per_question == "on") {
					$answers_question = get_comments(array('post_id' => $post_id,'user_id' => $user_id,'parent' => 0));
					$allow_to_do = wpqa_allow_to_answer_reply($post_id,$post_type,$user_id);
					$allow_to_answer = $allow_to_do["allow_to_answer"];
					$allow_to_reply = $allow_to_do["allow_to_reply"];
					if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
						if ($answer_per_question != "on" || ($allow_to_answer == true && $allow_to_reply != true) || ($allow_to_answer == true && $allow_to_reply == true)) {
							$allow_answer = true;
						}else {
							$edit_delete = '';
							if (isset($answers_question[0]) && isset($answers_question[0]->comment_approved) && $answers_question[0]->comment_approved == 1) {
								$can_edit_comment = wpqa_options("can_edit_comment");
								$can_edit_comment_after = wpqa_options("can_edit_comment_after");
								$can_edit_comment_after = (int)(isset($can_edit_comment_after) && $can_edit_comment_after > 0?$can_edit_comment_after:0);
								if (version_compare(phpversion(), '5.3.0', '>')) {
									$time_now = strtotime(current_time('mysql'),date_create_from_format('Y-m-d H:i',current_time('mysql')));
								}else {
									list($year, $month, $day, $hour, $minute, $second) = sscanf(current_time('mysql'),'%04d-%02d-%02d %02d:%02d:%02d');
									$datetime = new DateTime("$year-$month-$day $hour:$minute:$second");
									$time_now = strtotime($datetime->format('r'));
								}
								$time_edit_comment = strtotime('+'.$can_edit_comment_after.' hour',strtotime($answers_question[0]->comment_date));
								$time_end = ($time_now-$time_edit_comment)/60/60;
								$can_delete_comment = wpqa_options("can_delete_comment");
								$comment_id = $answers_question[0]->comment_ID;
								$comment_user_id = $answers_question[0]->user_id;
								if (($can_edit_comment == "on" && $comment_user_id == $user_id && $comment_user_id != 0 && $user_id != 0 && ($can_edit_comment_after == 0 || $time_end <= $can_edit_comment_after))) {
										$edit_delete .= "<a class='comment-edit-link edit-comment' href='".wpqa_edit_permalink($comment_id,"comment")."'>".esc_html__("Edit your answer.","wpqa")."</a>";
									}
									if ($can_delete_comment == "on" && $comment_user_id == $user_id && $comment_user_id > 0 && $user_id > 0) {
										$edit_delete .= "<a class='delete-comment delete-answer' href='".esc_url_raw(add_query_arg(array('activate_delete' => true,'delete_comment' => $comment_id,"wpqa_delete_nonce" => wp_create_nonce("wpqa_delete_nonce")),get_permalink($answers_question[0]->comment_post_ID)))."'>".esc_html__("Delete your answer.","wpqa")."</a>";
									}
							}
							$no_allow_to_answer = false;
							echo '<div class="alert-message alert-message-warning alert-answer-question"><i class="icon-flag"></i><p>'.esc_html__("You have already answered this question.","wpqa").' '.$edit_delete.'</p></div>';
						}
					}
				}
			}
		}else {
			$no_allow_to_answer = false;
			$activate_login = wpqa_options("activate_login");
			if ($activate_login != 'disabled' && !is_user_logged_in()) {
				$activate_register = wpqa_options("activate_register");
				echo '<div id="respond">'.wpqa_login_message(($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?esc_html__("You must login to add an answer.","wpqa"):esc_html__("You must login to add a comment.","wpqa")),"warning").($activate_register != 'disabled'?'<div class="pop-footer pop-footer-comments">'.wpqa_signup_in_popup().'</div></div>':'');
			}else {
				echo wpqa_paid_subscriptions_message(esc_html__("Sorry, you do not have permission to answer to this question.","wpqa"));
			}
		}
	}else {
		$no_allow_to_answer = false;
		echo wpqa_paid_subscriptions_message(esc_html__("Sorry, you do not have permission to answer to this question.","wpqa"));
	}

	if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
		if (isset($allow_to_reply)) {
			$no_allow_to_answer = true;
		}
	}else {
		$no_allow_to_answer = $no_allow_to_answer;
	}

	if ($no_allow_to_answer == false) {
		$return = false;
	}else {
		if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
			$closed_question = get_post_meta($post_id,"closed_question",true);
			$user_is_login = get_userdata($user_id);
			$user_limit = wpqa_check_user_limit($user_id,$user_is_login,"answer");
			$expired_limit = (!$is_super_admin && isset($user_limit["expired"]) && $user_limit["expired"] == "expired"?"expired":"not");
		}
		$closed_post = apply_filters("wpqa_closed_post",false,$post);

		$pay_answer = wpqa_options("pay_answer");
		$unlogged_pay = wpqa_options("unlogged_pay");
		$custom_pay_answer = get_post_meta($post_id,"custom_pay_answer",true);
		if ($custom_pay_answer == "on") {
			$pay_answer = get_post_meta($post_id,"pay_answer",true);
		}
		$pay_answer = apply_filters('wpqa_pay_answer',$pay_answer);
		if (isset($closed_post) && $closed_post == 1) {
			$return = false;
			do_action("wpqa_closed_post_text");
		}else if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && isset($closed_question) && $closed_question == 1) {
			$return = false;
			echo '<div class="alert-message alert-message-warning alert-close-question"><i class="icon-flag"></i><p>'.esc_html__("Sorry this question is closed.","wpqa").'</p></div>';
		}else if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && $pay_answer == "on" && !is_user_logged_in() && $unlogged_pay != "on") {
			$return = false;
			$activate_login = wpqa_options("activate_login");
			if ($activate_login != 'disabled') {
				echo '<div id="respond">'.wpqa_login_message(($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?esc_html__("You must login to add an answer.","wpqa"):esc_html__("You must login to add a comment.","wpqa")),"warning").'</div>';
			}else {
				echo '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to answer to this question.","wpqa").'</p></div>';
			}
		}else if (isset($expired_limit) && $expired_limit == "expired") {
			$return = false;
			echo wpqa_paid_subscriptions_message(esc_html__("You have reached the maximum for adding answers.","wpqa"),"warning");
		}else {
			if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
				$return = false;
				do_action("wpqa_pay_to_answer",$user_id);
			}
			$allow_to_add_answer = apply_filters("wpqa_allow_to_add_answer",true,$user_id,(isset($custom_permission)?$custom_permission:""),(isset($roles)?$roles:array()),(isset($post_id)?$post_id:0),$post_type);
		}
		if ((isset($allow_to_add_answer) && $allow_to_add_answer == true && ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type)) || ($post_type != wpqa_questions_type && $post_type != wpqa_asked_questions_type)) {
			$return = true;
		}
	}
	return $return;
}
/* Notifications add answer */
if (!function_exists('wpqa_notifications_add_answer')) :
	function wpqa_notifications_add_answer($comment,$get_post) {
		$way_sending_notifications = wpqa_options("way_sending_notifications_answers");
		if ($way_sending_notifications != "cronjob") {
			$comment_id = $comment->comment_ID;
			$post_id = $comment->comment_post_ID;
			$comment_user_id = $comment->user_id;
			$post_author = $get_post->post_author;
			$post_title = $get_post->post_title;
			$remember_answer = get_post_meta($post_id,"remember_answer",true);
			$user_id_question = get_post_meta($post_id,"user_id",true);
			
			if ($remember_answer == "on" && (($post_author > 0 && $post_author != $comment_user_id) || ($post_author == 0 && $comment_user_id == 0))) {
				if ($post_author > 0) {
					$the_mail = get_the_author_meta("user_email",$post_author);
					$the_author = get_the_author_meta("display_name",$post_author);
				}else {
					$anonymously_user     = get_post_meta($post_id,"anonymously_user",true);
					$anonymously_question = get_post_meta($post_id,"anonymously_question",true);
					if (($anonymously_question == "on" || $anonymously_question == 1) && $anonymously_user != "") {
						$the_mail = get_the_author_meta("user_email",$anonymously_user);
						$the_author = get_the_author_meta("display_name",$anonymously_user);
					}else {
						$the_mail = get_post_meta($post_id,'question_email',true);
						$the_author = get_post_meta($post_id,'question_username',true);
					}
				}
				
				if ($the_mail != "") {
					$send_text = wpqa_send_mail(
						array(
							'content'          => wpqa_options("email_notified_answer"),
							'post_id'          => $post_id,
							'comment_id'       => $comment_id,
							'sender_user_id'   => ($comment_user_id > 0?$comment_user_id:$comment),
							'received_user_id' => (isset($anonymously_user) && $anonymously_user > 0?$anonymously_user:$post_author),
						)
					);
					$email_title = wpqa_options("title_notified_answer");
					$email_title = ($email_title != ""?$email_title:esc_html__("Answer to your question","wpqa"));
					$email_title = wpqa_send_mail(
						array(
							'content'          => $email_title,
							'title'            => true,
							'break'            => '',
							'post_id'          => $post_id,
							'comment_id'       => $comment_id,
							'sender_user_id'   => ($comment_user_id > 0?$comment_user_id:$comment),
							'received_user_id' => (isset($anonymously_user) && $anonymously_user > 0?$anonymously_user:$post_author),
						)
					);
					$unsubscribe_mails = get_the_author_meta('unsubscribe_mails',(isset($anonymously_user) && $anonymously_user > 0?$anonymously_user:$post_author));
					$answer_on_your_question = get_the_author_meta('answer_on_your_question',(isset($anonymously_user) && $anonymously_user > 0?$anonymously_user:$post_author));
					if ($unsubscribe_mails != "on" && $answer_on_your_question == "on") {
						wpqa_send_mails(
							array(
								'toEmail'     => $the_mail,
								'toEmailName' => $the_author,
								'title'       => $email_title,
								'message'     => $send_text,
							)
						);
					}
				}
			}
			
			$question_follow = wpqa_options("question_follow");
			$following_questions = get_post_meta($post_id,"following_questions",true);
			if ($question_follow == "on" && isset($following_questions) && is_array($following_questions)) {
				$email_follow_question = wpqa_options("email_follow_question");
				$email_title = wpqa_options("title_follow_question");
				$email_title = ($email_title != ""?$email_title:esc_html__("Hi there","wpqa"));
				foreach ($following_questions as $user) {
					if ($user_id_question != $user && $user > 0 && $comment_user_id != $user) {
						$author_user_email = get_the_author_meta("user_email",$user);
						if ($author_user_email != "") {
							$author_display_name = get_the_author_meta("display_name",$user);
							$send_text = wpqa_send_mail(
								array(
									'content'          => $email_follow_question,
									'post_id'          => $post_id,
									'comment_id'       => $comment_id,
									'sender_user_id'   => ($comment_user_id > 0?$comment_user_id:$comment),
									'received_user_id' => $user,
								)
							);
							$email_title = wpqa_send_mail(
								array(
									'content'          => $email_title,
									'title'            => true,
									'break'            => '',
									'post_id'          => $post_id,
									'comment_id'       => $comment_id,
									'sender_user_id'   => ($comment_user_id > 0?$comment_user_id:$comment),
									'received_user_id' => $user,
								)
							);
							wpqa_notifications_activities($user,$comment_user_id,($comment_user_id == 0?$comment->comment_author:0),$post_id,$comment_id,"answer_question_follow","notifications","","answer");
							$unsubscribe_mails = get_the_author_meta('unsubscribe_mails',$user);
							$answer_question_follow = get_the_author_meta('answer_question_follow',$user);
							if ($unsubscribe_mails != "on" && $answer_question_follow == "on") {
								wpqa_send_mails(
									array(
										'toEmail'     => esc_html($author_user_email),
										'toEmailName' => esc_html($author_display_name),
										'title'       => $email_title,
										'message'     => $send_text,
									)
								);
							}
						}
					}
				}
			}
			
			$active_points = wpqa_options("active_points");
			if ($comment_user_id != 0) {
				if ($comment_user_id != $post_author && $active_points == "on") {
					$point_add_comment = (int)wpqa_options("point_add_comment");
					if ($point_add_comment > 0) {
						wpqa_add_points($comment_user_id,$point_add_comment,"+","answer_question",$post_id,$comment_id);
					}
				}
				
				$add_answer = get_user_meta($comment_user_id,"add_answer_all",true);
				$add_answer_m = get_user_meta($comment_user_id,"add_answer_m_".date_i18n('m_Y',current_time('timestamp')),true);
				$add_answer_d = get_user_meta($comment_user_id,"add_answer_d_".date_i18n('d_m_Y',current_time('timestamp')),true);
				if ($add_answer_d == "" || $add_answer_d == 0) {
					update_user_meta($comment_user_id,"add_answer_d_".date_i18n('d_m_Y',current_time('timestamp')),1);
				}else {
					update_user_meta($comment_user_id,"add_answer_d_".date_i18n('d_m_Y',current_time('timestamp')),$add_answer_d+1);
				}
				
				if ($add_answer_m == "" || $add_answer_m == 0) {
					update_user_meta($comment_user_id,"add_answer_m_".date_i18n('m_Y',current_time('timestamp')),1);
				}else {
					update_user_meta($comment_user_id,"add_answer_m_".date_i18n('m_Y',current_time('timestamp')),$add_answer_m+1);
				}
				
				if ($add_answer == "" || $add_answer == 0) {
					update_user_meta($comment_user_id,"add_answer_all",1);
				}else {
					update_user_meta($comment_user_id,"add_answer_all",$add_answer+1);
				}
			}

			$user_is_comment = get_post_meta($post_id,"user_is_comment",true);
			$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
			
			if ($comment->comment_parent > 0) {
				$get_comment_reply_1 = get_comment($comment->comment_parent);
				wpqa_notification_reply_answer($get_comment_reply_1,$get_post,$post_id,$following_questions,$anonymously_user,$comment_user_id);
				if ($get_comment_reply_1->comment_parent > 0) {
					$get_comment_reply_2 = get_comment($get_comment_reply_1->comment_parent);
					wpqa_notification_reply_answer($get_comment_reply_2,$get_post,$post_id,$following_questions,$anonymously_user,$comment_user_id);
				}
			}
			
			if (($post_author > 0 && $comment_user_id != $post_author && ($comment->comment_parent == 0 || ($comment->comment_parent > 0 && $get_comment_reply_1->user_id > 0 && $get_comment_reply_1->user_id != $post_author))) || ($anonymously_user > 0 && $comment_user_id != $anonymously_user && ($comment->comment_parent == 0 || ($comment->comment_parent > 0 && $get_comment_reply_1->user_id > 0 && $get_comment_reply_1->user_id != $anonymously_user)))) {
				wpqa_notifications_activities(($post_author > 0?$post_author:$anonymously_user),$comment_user_id,($comment_user_id == 0?$comment->comment_author:0),$post_id,$comment_id,"answer_question","notifications","","answer");
			}
			if ($user_id_question != "") {
				if ($user_id_question != $comment_user_id) {
					wpqa_notifications_activities($user_id_question,$comment_user_id,($comment_user_id == 0?$comment->comment_author:0),$post_id,$comment_id,"answer_asked_question","notifications","","answer");
				}
				if ($user_is_comment != true && $user_id_question == $comment_user_id) {
					update_post_meta($post_id,"user_is_comment",true);
				}
			}
		}
	}
endif;
/* Notification for the reply on the answer */
function wpqa_notification_reply_answer($comment,$get_post,$post_id,$following_questions,$anonymously_user,$comment_user_id) {
	$question_follow = wpqa_options("question_follow");
	$not_in_the_follow = true;
	if ($question_follow == "on" && isset($following_questions) && is_array($following_questions) && in_array($comment->user_id,$following_questions)) {
		$not_in_the_follow = false;
	}
	$anonymously_user_comment = get_comment_meta($comment->comment_ID,"anonymously_user",true);
	$last_comment_user_id = (isset($anonymously_user_comment) && $anonymously_user_comment > 0?$anonymously_user_comment:$comment->user_id);
	$last_post_user_id = (isset($anonymously_user) && $anonymously_user > 0?$anonymously_user:$get_post->post_author);
	if ((($comment->user_id > 0 && $comment->user_id != $comment_user_id && $comment->user_id != $last_post_user_id) || ($anonymously_user_comment > 0 && $anonymously_user_comment != $comment_user_id && $anonymously_user_comment != $last_post_user_id)) && $not_in_the_follow == true) {
		$the_mail = get_the_author_meta("user_email",$last_comment_user_id);
		$the_author = get_the_author_meta("display_name",$last_comment_user_id);
		if ($the_mail != "") {
			$send_text = wpqa_send_mail(
				array(
					'content'          => wpqa_options("email_notified_reply"),
					'post_id'          => $post_id,
					'comment_id'       => $comment->comment_ID,
					'sender_user_id'   => ($comment_user_id > 0?$comment_user_id:$comment),
					'received_user_id' => $last_comment_user_id,
				)
			);
			$email_title = wpqa_options("title_notified_reply");
			$email_title = ($email_title != ""?$email_title:esc_html__("Reply to your answer","wpqa"));
			$email_title = wpqa_send_mail(
				array(
					'content'          => $email_title,
					'title'            => true,
					'break'            => '',
					'post_id'          => $post_id,
					'comment_id'       => $comment->comment_ID,
					'sender_user_id'   => ($comment_user_id > 0?$comment_user_id:$comment),
					'received_user_id' => $last_comment_user_id,
				)
			);
			$unsubscribe_mails = get_the_author_meta('unsubscribe_mails',$last_comment_user_id);
			$notified_reply = get_the_author_meta('notified_reply',$last_comment_user_id);
			wpqa_notifications_activities($last_comment_user_id,$comment_user_id,"",$post_id,$comment->comment_ID,"reply_answer","notifications","","answer");
			if ($unsubscribe_mails != "on" && $notified_reply == "on") {
				wpqa_send_mails(
					array(
						'toEmail'     => $the_mail,
						'toEmailName' => $the_author,
						'title'       => $email_title,
						'message'     => $send_text,
					)
				);
			}
		}
	}
}
/* Clean HTML codes */
add_filter('comment_text','wpqa_remove_html_codes',0);
if (!function_exists('wpqa_remove_html_codes')) :
	function wpqa_remove_html_codes($comment_text) {
		$comment_text = do_shortcode($comment_text);
		add_filter('embed_oembed_discover','__return_false',999);
		$comment_text = $GLOBALS['wp_embed']->autoembed($comment_text);
		remove_filter('embed_oembed_discover','__return_false',999);
		return $comment_text;
	}
endif;
/* Comment limit */
if (!function_exists('wpqa_comment_limit')) :
	function wpqa_comment_limit() {
		$comment_limit = (int)$_POST["comment_limit"];
		$comment_min_limit = (int)$_POST["comment_min_limit"];
		$comment_text = strip_tags($_POST["comment_text"]);
		$comment_text = str_replace('<p>','',$comment_text);
		$comment_text = str_replace('</p>','',$comment_text);
		$comment_text = str_replace('<br>','',$comment_text);
		$comment_text = str_replace('<br data-mce-bogus="1">','',$comment_text);
		if ($comment_min_limit > 0 && strlen($comment_text) < $comment_min_limit) {
			echo "wpqa_min_error";
		}
		if ($comment_limit > 0 && strlen($comment_text) > $comment_limit) {
			echo "wpqa_error";
		}
		die();
	}
endif;
add_action("wp_ajax_nopriv_wpqa_comment_limit","wpqa_comment_limit");
add_action("wp_ajax_wpqa_comment_limit","wpqa_comment_limit");
/* Private answer */
if (!function_exists('wpqa_private_answer')) :
	function wpqa_private_answer($comment_id,$first_user,$second_user,$post_author) {
		$yes_private_answer = 0;
		$private_answer = wpqa_options("private_answer");
		$get_private_answer = get_comment_meta($comment_id,'private_answer',true);
		
		if ($private_answer == "on") {
			$private_answer_user = wpqa_options("private_answer_user");
			if (($get_private_answer == 1 && $private_answer_user == "on" && $second_user == $post_author && $post_author > 0) || (($get_private_answer == 1 && isset($first_user) && $first_user > 0 && $first_user == $second_user) || $get_private_answer != 1)) {
				$yes_private_answer = 1;
			}
		}else {
			$yes_private_answer = 1;
		}
		
		if (is_super_admin($second_user)) {
			$yes_private_answer = 1;
		}
		return $yes_private_answer;
	}
endif;
/* Add loader after submit button */
add_filter("comment_form_submit_button","wpqa_comment_form_submit_button");
if (!function_exists('wpqa_comment_form_submit_button')) :
	function wpqa_comment_form_submit_button($submit_field) {
		return $submit_field.'<span class="clearfix"></span><span class="load_span"><span class="loader_2"></span></span>';
	}
endif;
/* Comment video */
add_filter('wp_video_extensions','wpqa_video_extensions');
if (!function_exists('wpqa_video_extensions')) :
	function wpqa_video_extensions($exts) {
		$exts[] = 'mov';
		$exts[] = 'avi';
		$exts[] = 'wmv';
		return $exts;
	}
endif;
/* Comment login */
add_filter(wpqa_prefix_theme."_filter_comment_form","wpqa_filter_comment_form",1,2);
function wpqa_filter_comment_form($comments_args,$post_type) {
	$filter_social_login = apply_filters("wpqa_filter_social_login",false);
	if ($filter_social_login != "" || shortcode_exists('wpqa_social_login') || shortcode_exists('rdp-linkedin-login') || shortcode_exists('oa_social_login') || shortcode_exists('login_me_now_google_button') || shortcode_exists('miniorange_social_login') || shortcode_exists('xs_social_login') || shortcode_exists('wordpress_social_login') || shortcode_exists('apsl-login') || shortcode_exists('apsl-login-lite') || shortcode_exists('nextend_social_login')) {
		$out = '<div class="wpqa_login_social">';
			$out .= ($filter_social_login != ""?$filter_social_login:"").
			(shortcode_exists('wpqa_social_login')?do_shortcode("[wpqa_social_login]"):"").
			(shortcode_exists('rdp-linkedin-login')?do_shortcode("[rdp-linkedin-login]"):"").
			(shortcode_exists('oa_social_login')?do_shortcode("[oa_social_login]"):"").
			(shortcode_exists('login_me_now_google_button')?do_shortcode("[login_me_now_google_button]"):"").
			(shortcode_exists('miniorange_social_login')?do_shortcode("[miniorange_social_login]"):"").
			(shortcode_exists('xs_social_login')?do_shortcode("[xs_social_login]"):"").
			(shortcode_exists('wordpress_social_login')?do_shortcode("[wordpress_social_login]"):"").
			(shortcode_exists('apsl-login')?do_shortcode("[apsl-login]"):"").
			(shortcode_exists('apsl-login-lite')?do_shortcode("[apsl-login-lite]"):"").
			(shortcode_exists('nextend_social_login')?do_shortcode("[nextend_social_login]"):"");
		$out .= '</div>';
		$comments_args["must_log_in"] = $comments_args["must_log_in"].$out;
	}
	return $comments_args;
}
/* Add replies button */
add_action("wpqa_action_show_replies","wpqa_action_show_replies",1,3);
add_action(wpqa_prefix_theme."_action_show_replies","wpqa_action_show_replies",1,3);
function wpqa_action_show_replies($comment,$answer,$post_type) {
	$show_replies = wpqa_options("show_replies");
	if ($show_replies == "on") {
		$comment_has_replies = (int)get_comments(array('parent' => $comment->comment_ID,'count' => true));
		if ($comment_has_replies > 0) {
			echo "<li class='show-replies-li'><a class='show-replies' href='#'><i class='icon-comment'></i>".sprintf(_n("%s Reply","%s Replies",$comment_has_replies,"wpqa"),$comment_has_replies)."</a></li>";
		}
	}
}
/* Comment data */
add_filter("comment_form_field_comment","wpqa_comment_data");
if (!function_exists('wpqa_comment_data')) :
	function wpqa_comment_data($comment) {
		return apply_filters('wpqa_comment_fields',false,"add").apply_filters('wpqa_comment_extra_fields',false).$comment;
	}
endif;
/* Remove private answers from API */
add_filter('rest_prepare_comment','wpqa_remove_user_answers',10,3);
if (!function_exists('wpqa_remove_user_answers')) :
	function wpqa_remove_user_answers($data,$post,$request) {
		$_data = $data->data;
		$params = $request->get_params();
		if (!isset($params['id'])) {
			$answer_question_user    = get_comment_meta($_data['id'],"answer_question_user",true);
			$answer_question_private = get_comment_meta($_data['id'],"answer_question_private",true);
			if ($answer_question_user != "" || $answer_question_private != "") {
				unset($_data);
			}
		}
		$data->data = $_data;
		return $data;
	}
endif;
/* Edit comment */
if (!function_exists('wpqa_edit_comment')) :
	function wpqa_edit_comment($edit) {
		if (isset($_POST["form_type"]) && $_POST["form_type"] == "edit_comment") :
			$return = wpqa_process_edit_comments($_POST);
			if (is_wp_error($return)) :
				return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
			else :
				$approved = wp_get_comment_status($return);
				if ($approved == 1 || $approved == "approved") {
					wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Edited successfully.","wpqa").'</p></div>','wpqa_session');
				}else {
					wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Your answer was successfully edited, It's under review.","wpqa").'</p></div>','wpqa_session');
				}
				$comment_type = get_comment_meta($return,"comment_type",true);
				if ($comment_type == "comment_group") {
					$get_comment = get_comment($return);
					$redirect_to = wpqa_custom_permalink($get_comment->comment_post_ID,"view_group_posts","view_group_post").'#comment-'.$return;
				}else {
					$redirect_to = get_comment_link($return);
				}
				wp_redirect($redirect_to);
				exit;
			endif;
		endif;
	}
endif;
add_filter('wpqa_edit_comment','wpqa_edit_comment');
/* Process edit comments */
if (!function_exists('wpqa_process_edit_comments')) :
	function wpqa_process_edit_comments($data) {
		global $edit_comment_post;
		@set_time_limit(0);
		$errors = new WP_Error();
		$edit_comment_post = array();
		$form_type = (isset($data["form_type"]) && $data["form_type"] != ""?$data["form_type"]:"");
		if ($form_type == "edit_comment") {
			$fields = array(
				'wpqa_comment_nonce','comment_id','comment','video_answer_description','video_answer_type','video_answer_id','private_answer'
			);
			
			foreach ($fields as $field) :
				if (isset($data[$field])) $edit_comment_post[$field] = $data[$field]; else $edit_comment_post[$field] = '';
			endforeach;

			/* Validate Required Fields */
			$comment_id = (isset($edit_comment_post['comment_id'])?(int)$edit_comment_post['comment_id']:0);
			$get_comment = get_comment($comment_id);
			$comment_user_id = $get_comment->user_id;
			$get_current_user_id = get_current_user_id();
			$is_super_admin = is_super_admin($get_current_user_id);
			$get_post = array();
			if (isset($comment_id) && $comment_id != 0 && is_object($get_comment)) {
				$post_id = $get_comment->comment_post_ID;
				$get_post = get_post($post_id);
			}
			$get_post_type_comment = $get_post->post_type;

			if (!isset($data['mobile']) && (!isset($edit_comment_post['wpqa_comment_nonce']) || !wp_verify_nonce($edit_comment_post['wpqa_comment_nonce'],'wpqa_comment_nonce'))) {
				$errors->add('required-1','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are an error, Please try again.","wpqa"));
			}
			
			$comment_editor = wpqa_options(($get_post_type_comment == wpqa_questions_type || $get_post_type_comment == wpqa_asked_questions_type?"answer_editor":"comment_editor"));
			$comment_content = (isset($edit_comment_post["comment"])?($comment_editor == "on"?wpqa_esc_textarea($edit_comment_post['comment']):wpqa_esc_textarea($edit_comment_post['comment'])):"");
			
			if (isset($comment_id) && $comment_id != 0 && $get_post) {
				$can_edit_comment = wpqa_options("can_edit_comment");
				if ($get_post_type_comment == "posts") {
					$edit_delete_posts_comments = wpqa_options("edit_delete_posts_comments");
					$activate_edit = wpqa_group_edit_comments($post_id,$is_super_admin,$can_edit_comment,$comment_user_id,$get_current_user_id,$edit_delete_posts_comments);
				}else if ($is_super_admin || ($can_edit_comment == "on" && $comment_user_id == $get_current_user_id && $get_current_user_id > 0)) {
					$activate_edit = true;
				}
				if ($activate_edit != true) {
					$errors->add('required-2','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("You are not allowed to edit this comment.","wpqa"));
				}
			}else {
				$errors->add('required-3','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Sorry, no comment has been selected or not found.","wpqa"));
			}
			
			if (empty($comment_content)) $errors->add('required-4','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (comment).","wpqa"));

			$comment_limit = (int)wpqa_options(($get_post_type_comment == wpqa_questions_type || $get_post_type_comment == wpqa_asked_questions_type?"answer_limit":"comment_limit"));
			$comment_min_limit = (int)wpqa_options(($get_post_type_comment == wpqa_questions_type || $get_post_type_comment == wpqa_asked_questions_type?"answer_min_limit":"comment_min_limit"));
			$comment_text = strip_tags($comment_content);
			$comment_text = str_replace('<p>','',$comment_text);
			$comment_text = str_replace('</p>','',$comment_text);
			$comment_text = str_replace('<br>','',$comment_text);
			$comment_text = str_replace('<br data-mce-bogus="1">','',$comment_text);
			if ($comment_min_limit > 0 && strlen($comment_text) < $comment_min_limit) {
				$errors->add('required-5','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Sorry, The minimum characters is","wpqa")." ".$comment_min_limit);
			}
			if ($comment_limit > 0 && strlen($comment_text) > $comment_limit) {
				$errors->add('required-5','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Sorry, The maximum characters is","wpqa")." ".$comment_limit);
			}

			$answer_video = wpqa_options("answer_video");
			if ($answer_video == "on" && isset($edit_comment_post['video_answer_description']) && $edit_comment_post['video_answer_description'] == "on" && empty($edit_comment_post['video_answer_id'])) {
				$errors->add('required-6','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Video ID).","wpqa"));
			}

			$attachment_answer = wpqa_options("attachment_answer");
			$featured_image_answer = wpqa_options("featured_image_answer");
			if (($attachment_answer == "on" && isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) || ($featured_image_answer == "on" && isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name']))) {
				require_once(ABSPATH . 'wp-admin/includes/media.php');
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				require_once(ABSPATH . 'wp-admin/includes/file.php');
			}

			if ($attachment_answer == "on" && isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) {
				$comment_attachment = wp_handle_upload($_FILES['attachment'],array('test_form' => false),current_time('mysql'));
				if (isset($comment_attachment['error'])) :
					$errors->add('Attachment Error: ' . $comment_attachment['error']);
				endif;
			}

			if ($featured_image_answer == "on" && isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) :
				$comment_featured_image = '';
				$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/webp","image/gif","image/tiff","image/ico");
				if (!isset($data['mobile']) && !in_array($_FILES['featured_image']['type'],$types)) :
					$errors->add('upload-error',esc_html__("Attachment Error! Please upload image only.","wpqa"));
				endif;
				
				$comment_featured_image = wp_handle_upload($_FILES['featured_image'],array('test_form' => false),current_time('mysql'));
				
				if (isset($comment_featured_image['error'])) :
					$errors->add('upload-error',esc_html__("Attachment Error: ","wpqa") . $comment_featured_image['error']);
				endif;
				
			endif;

			if (sizeof($errors->errors) > 0) return $errors;
			
			/* Edit comment */
			$comment_approved = wpqa_options("comment_approved");
			$update_data = array('comment_ID' => $comment_id,'comment_content' => $comment_content);
			if ((($get_post_type_comment != "posts" && $comment_approved == "publish") || ($get_post_type_comment == "posts" && $activate_edit == true && $comment_approved == "publish")) || $is_super_admin) {
				$update_data['comment_approved'] = 1;
			}else {
				$update_data['comment_approved'] = 0;
			}
			
			wp_update_comment($update_data);

			wpqa_notification_mention($comment_content,"comment",$comment_id,$comment_user_id,$post_id);
			
			update_comment_meta($comment_id,"edit_comment","edited");

			if ($answer_video == "on") {
				if ($edit_comment_post['video_answer_description'] && $edit_comment_post['video_answer_description'] != "") {
					update_comment_meta($comment_id,'video_answer_description',esc_html($edit_comment_post['video_answer_description']));
				}else {
					delete_comment_meta($comment_id,'video_answer_description');
				}
				
				if ($edit_comment_post['video_answer_type']) {
					update_comment_meta($comment_id,'video_answer_type',esc_html($edit_comment_post['video_answer_type']));
				}
					
				if ($edit_comment_post['video_answer_id']) {
					update_comment_meta($comment_id,'video_answer_id',esc_html($edit_comment_post['video_answer_id']));
				}
			}
			
			if (isset($edit_comment_post['private_answer'])) {
				if ($edit_comment_post['private_answer'] == "on") {
					update_comment_meta($comment_id,'private_answer',1);
				}else {
					delete_comment_meta($comment_id,'private_answer');
				}
			}

			/* Attachment */

			if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
				$comment_attachment_data = array(
					'post_mime_type' => $comment_attachment['type'],
					'post_title'	 => preg_replace('/\.[^.]+$/','',basename($comment_attachment['file'])),
					'post_content'   => '',
					'post_status'	=> 'inherit',
					'post_author'	=> ($comment_user_id != "" || $comment_user_id != 0?$comment_user_id:0)
				);
				$comment_attachment_id = wp_insert_attachment($comment_attachment_data,$comment_attachment['file'],$post_id);
				$comment_attachment_metadata = wp_generate_attachment_metadata($comment_attachment_id,$comment_attachment['file']);
				wp_update_attachment_metadata($comment_attachment_id, $comment_attachment_metadata);
				update_comment_meta($comment_id,'added_file',$comment_attachment_id);
			endif;

			/* Featured image */
			
			if ($featured_image_answer == "on" && isset($comment_featured_image)) {
				$comment_featured_image_data = array(
					'post_mime_type' => $comment_featured_image['type'],
					'post_title'	 => preg_replace('/\.[^.]+$/','',basename($comment_featured_image['file'])),
					'post_content'   => '',
					'post_status'	 => 'inherit',
					'post_author'	 => ($comment_user_id > 0?$comment_user_id:0)
				);
				$comment_featured_image_id = wp_insert_attachment($comment_featured_image_data,$comment_featured_image['file'],$post_id);
				$comment_featured_image_metadata = wp_generate_attachment_metadata($comment_featured_image_id,$comment_featured_image['file']);
				wp_update_attachment_metadata($comment_featured_image_id, $comment_featured_image_metadata);
				update_comment_meta($comment_id,'featured_image',$comment_featured_image_id);
			}
		
			do_action('wpqa_edit_comments',$comment_id);
			
			/* Successful */
			return $comment_id;
		}
	}
endif;
/* Comment columns */
if (!function_exists('wpqa_comment_columns')) :
	function wpqa_comment_columns ($columns) {
		if (isset($_GET['comment_status']) && $_GET['comment_status'] == "group-comments") {
			$columns = array_merge( $columns, array(
				'answers' => esc_html__('Group Comments','wpqa'),
			));
		}else if (isset($_GET['comment_status']) && $_GET['comment_status'] == "best-answers") {
			$columns = array_merge( $columns, array(
				'answers' => esc_html__('Best Answers','wpqa'),
			));
		}else if (isset($_GET['comment_status']) && $_GET['comment_status'] == "answers") {
			$columns = array_merge( $columns, array(
				'answers' => esc_html__('Answers','wpqa'),
			));
		}else if (!isset($_GET['comment_status']) || (isset($_GET['comment_status']) && $_GET['comment_status'] != "comments")) {
			$columns = array_merge( $columns, array(
				'answers' => esc_html__('Answer/Comment','wpqa'),
			));
		}
		return $columns;
	}
endif;
add_filter('manage_edit-comments_columns','wpqa_comment_columns');
if (!function_exists('wpqa_comment_column')) :
	function wpqa_comment_column ($column,$comment_ID) {
		switch ( $column ) {
			case 'answers':
				$comment_type = get_comment_meta($comment_ID,"comment_type",true);
				if (isset($comment_type) && $comment_type == "comment_group") {
					echo "<a href='".admin_url('edit-comments.php?comment_status=group-comments')."' class='green-background-span gray-background-span'>".esc_html__('Group Comments','wpqa')."</a>";
				}else if (isset($comment_type) && $comment_type == "question") {
					if (!isset($_GET['comment_status']) || (isset($_GET['comment_status']) && $_GET['comment_status'] != "best-answers")) {
						echo "<a href='".admin_url('edit-comments.php?comment_status=answers')."' class='green-background-span orange-background-span'>".apply_filters("wpqa_answer_language",esc_html_x('Answer','Admin answer link','wpqa'))."</a>";
						$anonymously_user = (int)get_comment_meta($comment_ID,'anonymously_user',true);
						if ($anonymously_user > 0) {
							$display_name_anonymous = get_the_author_meta('display_name',$anonymously_user);
							echo " - <a href='".wpqa_profile_url($anonymously_user)."' target='_blank'>".$display_name_anonymous."</a>";
						}
					}
					$best_answer_comment = get_comment_meta($comment_ID,"best_answer_comment",true);
					if ($best_answer_comment == "best_answer_comment") {
						echo "<a href='".admin_url('edit-comments.php?comment_status=best-answers')."' class='green-background-span margin_l_20'>".esc_html__('Best answer','Admin best answer link','wpqa')."</a>";
					}
				}else {
					echo "<a href='".admin_url('edit-comments.php?comment_status=comments')."' class='green-background-span gray-background-span'>".esc_html_x('Comment','Admin comment link','wpqa')."</a>";
				}
			break;
		}
	}
endif;
add_filter('manage_comments_custom_column','wpqa_comment_column',10,2);
/* Edit comment admin */
add_action ('edit_comment','wpqa_edit_comment_admin');
if (!function_exists('wpqa_edit_comment_admin')) :
	function wpqa_edit_comment_admin($comment_id) {
		if (isset($_POST["delete_reason"]) && $_POST["delete_reason"] != "") {
			update_comment_meta($comment_id,"delete_reason",esc_html($_POST["delete_reason"]));
		}
	}
endif;
/* Meta boxes comment */
add_action('add_meta_boxes_comment','wpqa_meta_boxes_comment');
if (!function_exists('wpqa_meta_boxes_comment')) :
	function wpqa_meta_boxes_comment($comment) {
		$answer_question = get_post_type($comment->comment_post_ID);
		if ($answer_question == wpqa_questions_type || $answer_question == wpqa_asked_questions_type || $answer_question == "post") {?>
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<div id="delete-comment" class="postbox">
					<button type="button" class="handlediv" aria-expanded="true"><span class="toggle-indicator" aria-hidden="true"></span></button>
					<h2 class="hndle ui-sortable-handle"><span><?php esc_html_e('Reason if you need to remove it.','wpqa')?></span></h2>
					<div class="inside">
						<table class="form-table editcomment">
							<tbody>
								<tr>
									<td class="first" style="width: 10px;"><label for="delete_reason"><?php esc_html_e('Reason:','wpqa')?></label></td>
									<td>
										<input id="delete_reason" name="delete_reason" class="code" type="text" value="<?php echo esc_attr(get_comment_meta($comment->comment_ID,"delete_reason",true));?>" style="width: 98%;">
									</td>
								</tr>
							</tbody>
						</table>
						<br>
						<div class="submitbox"><a href="#" class="submitdelete delete-comment-answer" data-div-id="delete_reason" data-id="<?php echo esc_attr($comment->comment_ID);?>" data-action="wpqa_delete_comment_answer" data-nonce="<?php echo wp_create_nonce("wpqa_delete_nonce")?>" data-location="<?php echo esc_url(($answer_question == wpqa_questions_type || $answer_question == wpqa_asked_questions_type?admin_url('edit-comments.php?comment_status=all&amp;answers=1'):admin_url('edit-comments.php?comment_status=all&amp;comments=1')))?>"><?php esc_html_e('Delete?','wpqa')?></a></div>
					</div>
				</div>
			</div>
		<?php }
	}
endif;
/* Comments exclude */
add_action('current_screen','wpqa_comments_exclude',10,2);
if (!function_exists('wpqa_comments_exclude')) :
	function wpqa_comments_exclude($screen) {
		if ($screen->id != 'edit-comments')
			return;
		if (isset($_GET['comment_status']) && $_GET['comment_status'] == "group-comments") {
			add_action('pre_get_comments','wpqa_list_group_comments',10,1);
		}else if (isset($_GET['comment_status']) && $_GET['comment_status'] == "best-answers") {
			add_action('pre_get_comments','wpqa_list_best_answers',10,1);
		}else if (isset($_GET['comment_status']) && $_GET['comment_status'] == "answers") {
			add_action('pre_get_comments','wpqa_list_answers',10,1);
		}else if (isset($_GET['comment_status']) && $_GET['comment_status'] == "comments") {
			add_action('pre_get_comments','wpqa_list_comments',10,1);
		}
	}
endif;
if (!function_exists('wpqa_list_group_comments')) :
	function wpqa_list_group_comments($query) {
		$query->query_vars['post_type'] = "posts";
	}
endif;
if (!function_exists('wpqa_list_comments')) :
	function wpqa_list_comments($query) {
		$query->query_vars['post_type'] = "post";
	}
endif;
if (!function_exists('wpqa_list_answers')) :
	function wpqa_list_answers($query) {
		$query->query_vars['post_type'] = wpqa_questions_type;
	}
endif;
if (!function_exists('wpqa_list_best_answers')) :
	function wpqa_list_best_answers($query) {
		$query->query_vars['post_type'] = wpqa_questions_type;
		$query->query_vars['meta_key'] = "best_answer_comment";
		$query->query_vars['meta_value'] = "best_answer_comment";
	}
endif;
add_filter('comment_status_links','wpqa_new_answers_page_link');
if (!function_exists('wpqa_new_answers_page_link')) :
	function wpqa_new_answers_page_link($status_links) {
		$answers_count = wpqa_all_comments_of_post_type(array(wpqa_questions_type,wpqa_asked_questions_type));
		$status_links['comments'] = '<a href="edit-comments.php?comment_status=comments"'.(isset($_GET['comment_status']) && $_GET['comment_status'] == "comments"?' class="current"':'').'>'.esc_html__('Comments','wpqa').' ('.wpqa_all_comments_of_post_type("post").')</a>';
		$status_links['group-comments'] = '<a href="edit-comments.php?comment_status=group-comments"'.(isset($_GET['comment_status']) && $_GET['comment_status'] == "group-comments"?' class="current"':'').'>'.esc_html__('Group Comments','wpqa').' ('.wpqa_all_comments_of_post_type("posts").')</a>';
		$status_links['answers'] = '<a href="edit-comments.php?comment_status=answers"'.(isset($_GET['comment_status']) && $_GET['comment_status'] == "answers"?' class="current"':'').'>'.esc_html__('Answers','wpqa').' ('.$answers_count.')</a>';
		$status_links['best-answers'] = '<a href="edit-comments.php?comment_status=best-answers"'.(isset($_GET['comment_status']) && $_GET['comment_status'] == "best-answers"?' class="current"':'').'>'.esc_html__('Best Answers','wpqa').' ('.wpqa_all_best_answers().')</a>';
		return $status_links;
	}
endif;
/* Stop mails with IP */
add_filter("comment_notification_recipients","wpqa_comment_notification_recipients");
function wpqa_comment_notification_recipients() {
	return array();
}
add_filter("notify_moderator","wpqa_notify_moderator");
function wpqa_notify_moderator() {
	return false;
}
/* Duplicate comments */
add_filter('duplicate_comment_id','wpqa_duplicate_comment_id',9,1);
function wpqa_duplicate_comment_id($dupe_id) {
	if ($dupe_id > 0) {
		$anonymously_user = get_comment_meta($dupe_id,'anonymously_user',true);
		if ($anonymously_user != "") {
			return 0;
		}
	}else {
		return $dupe_id;
	}
}
/* Count replies */
function wpqa_count_replies($post_id,$user_id = 0,$author = "",$email = "") {
	if ($user_id > 0) {
		$all_answers_question = get_comments(array('post_id' => $post_id,'user_id' => $user_id,'status' => 'approve'));
		$answers_question = get_comments(array('post_id' => $post_id,'user_id' => $user_id,'parent' => 0,'status' => 'approve'));
		$all_answers_question_count = (is_array($all_answers_question) && !empty($all_answers_question)?count($all_answers_question):0);
		$answers_question_count = (is_array($answers_question) && !empty($answers_question)?count($answers_question):0);
	}else {
		global $wpdb;
		$all_answers_question_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_author = %s AND comment_author_email = %s AND comment_post_ID = %s AND comment_approved = '1'",$author,$email,$post_id));
		$answers_question_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_parent = 0 AND comment_author = %s AND comment_author_email = %s AND comment_post_ID = %s AND comment_approved = '1'",$author,$email,$post_id));
	}
	$replies_question = $all_answers_question_count-$answers_question_count;
	return array("answers" => $answers_question_count,"replies" => $replies_question);
}
/* Allow to reply */
function wpqa_allow_to_reply($post_id,$user_id = 0,$author = "",$email = "") {
	$allow_to_reply = false;
	$answer_per_question = wpqa_options("answer_per_question");
	$replies_per_question = wpqa_options("replies_per_question");
	if ($answer_per_question == "on" && !is_super_admin($user_id) && $post_id > 0) {
		$wpqa_count_replies = wpqa_count_replies($post_id,$user_id,$author,$email);
		$answers_question = $wpqa_count_replies["answers"];
		$replies_question = $wpqa_count_replies["replies"];
		if (($replies_per_question == "one_reply" && $replies_question == 0) || $replies_per_question == "unlimited") {
			$allow_to_reply = true;
		}
	}
	return $allow_to_reply;
}
/* Allow to answer and reply */
function wpqa_allow_to_answer_reply($post_id,$post_type,$user_id) {
	$answer_per_question = wpqa_options("answer_per_question");
	if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && $answer_per_question == "on") {
		$author = $email = "";
		if ($user_id == 0) {
			if (isset($_COOKIE) && is_array($_COOKIE) && !empty($_COOKIE)) {
				foreach ($_COOKIE as $key => $value) {
					if (strpos($key,'comment_author_'.$post_id.'_') !== false) {
						$author = $value;
					}
					if (strpos($key,'comment_author_email_'.$post_id.'_') !== false) {
						$email = $value;
					}
				}
			}
		}
		$answers_question = wpqa_count_replies($post_id,$user_id,$author,$email);
		$answers_question_count = (isset($answers_question["answers"])?(int)$answers_question["answers"]:0);
		$allow_to_reply = wpqa_allow_to_reply($post_id,$user_id,$author,$email);
		if ($answers_question_count == 0) {
			$allow_to_answer = true;
		}
		if (is_super_admin($user_id)) {
			$allow_to_answer = $allow_to_reply = true;
		}
	}else {
		$allow_to_answer = $allow_to_reply = true;
	}
	return array("allow_to_answer" => (isset($allow_to_answer)?$allow_to_answer:false),"allow_to_reply" => (isset($allow_to_reply)?$allow_to_reply:false));
}
/* Add comment cookies */
function wpqa_set_comment_cookies($comment,$cookies_consent = true) {
	$post_id = $comment->comment_post_ID;
	if (false === $cookies_consent) {
		$past = time() - YEAR_IN_SECONDS;
		setcookie('comment_author_'.$post_id.'_'.COOKIEHASH,' ',$past,COOKIEPATH,COOKIE_DOMAIN);
		setcookie('comment_author_email_'.$post_id.'_'.COOKIEHASH,' ',$past,COOKIEPATH,COOKIE_DOMAIN);
		setcookie('comment_author_url_'.$post_id.'_'.COOKIEHASH,' ',$past,COOKIEPATH,COOKIE_DOMAIN);
	}
	$comment_cookie_lifetime = time() + apply_filters('comment_cookie_lifetime',30000000);
	$secure = ('https' === parse_url(home_url(),PHP_URL_SCHEME));
	setcookie('comment_author_'.$post_id.'_'.COOKIEHASH,$comment->comment_author,$comment_cookie_lifetime,COOKIEPATH,COOKIE_DOMAIN,$secure);
	setcookie('comment_author_email_'.$post_id.'_'.COOKIEHASH,$comment->comment_author_email,$comment_cookie_lifetime,COOKIEPATH,COOKIE_DOMAIN,$secure);
	setcookie('comment_author_url_'.$post_id.'_'.COOKIEHASH,esc_url($comment->comment_author_url),$comment_cookie_lifetime,COOKIEPATH,COOKIE_DOMAIN,$secure);
}?>