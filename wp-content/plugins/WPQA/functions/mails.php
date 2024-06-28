<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Send mails */
if (!function_exists('wpqa_send_mails')) :
	function wpqa_send_mails($args = array()) {
		$defaults = array(
			'toEmail'       => '',
			'toEmailName'   => '',
			'title'         => '',
			'message'       => '',
			'email_code'    => '',
		);
		
		$args = wp_parse_args($args,$defaults);

		$toEmail       = $args['toEmail'];
		$toEmailName   = $args['toEmailName'];
		$title         = $args['title'];
		$message       = $args['message'];
		$email_code    = $args['email_code'];
		
		$toEmail = ($toEmail != ""?$toEmail:wpqa_options("email_template_to"));
		$toEmail = ($toEmail != ""?$toEmail:get_bloginfo("admin_email"));
		$toEmailName = ($toEmailName != ""?$toEmailName:get_bloginfo('name'));
		
		$toEmail = apply_filters("wpqa_sendemail_to",$toEmail);
		$toEmailName = apply_filters("wpqa_sendemail_toname",$toEmailName);
		if ($email_code == "") {
			$message = wpqa_email_code($message);
		}
		add_filter('wp_mail_content_type','wpqa_set_content_type');
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail($toEmail,htmlspecialchars_decode($title),$message,$headers);
	}
endif;
/* PHPMailer action */
add_action('phpmailer_init','wpqa_wp_phpmailer');
if (!function_exists('wpqa_wp_phpmailer')) :
	function wpqa_wp_phpmailer($phpmailer) {
		$email_template = wpqa_options("email_template");
		$mail_smtp = wpqa_options("mail_smtp");
		if ($mail_smtp == "on") {
			$mail_host = wpqa_options("mail_host");
			$mail_username = wpqa_options("mail_username");
			$mail_password = wpqa_options("mail_password");
			$mail_secure = wpqa_options("mail_secure");
			$mail_port = wpqa_options("mail_port");
			$disable_ssl = wpqa_options("disable_ssl");
			$smtp_auth = wpqa_options("smtp_auth");

			$phpmailer->isSMTP();     
			$phpmailer->Host = $mail_host;
			$phpmailer->SMTPAuth = ($smtp_auth == "on"?true:false);
			$phpmailer->Port = $mail_port;
			$phpmailer->SMTPSecure = $mail_secure;
			$phpmailer->Username = $mail_username;
			$phpmailer->Password = $mail_password;
			$phpmailer->Sender = $email_template;
			$phpmailer->From = $email_template;
		}
		if (isset($_POST['show_from_email']) && $_POST['show_from_email'] != "") {
			$phpmailer->addReplyTo(esc_html($_POST['show_from_email']),(isset($_POST['show_from_name']) && $_POST['show_from_name'] != ""?esc_html($_POST['show_from_name']):""));
		}
		$bloginfo_name = get_bloginfo('name');
		$custom_mail_name = wpqa_options('custom_mail_name');
		$mail_name = wpqa_options('mail_name');
		$mail_name = ($custom_mail_name == "on" && $mail_name != ""?$mail_name:$bloginfo_name);
		$phpmailer->FromName = $mail_name;
		return $phpmailer;
	}
endif;
if (!function_exists('wpqa_set_content_type')) :
	function wpqa_set_content_type() {
		return "text/html";
	}
endif;
/* Send mail template */
if (!function_exists('wpqa_send_mail')) :
	function wpqa_send_mail($args = array()) {
		$defaults = array(
			'content'            => '',
			'title'              => '',
			'break'              => 'break',
			'user_id'            => 0,
			'post_id'            => 0,
			'comment_id'         => 0,
			'reset_password'     => '',
			'confirm_link_email' => '',
			'item_price'         => '',
			'item_name'          => '',
			'item_currency'      => '',
			'payer_email'        => '',
			'first_name'         => '',
			'last_name'          => '',
			'item_transaction'   => '',
			'date'               => '',
			'time'               => '',
			'category'           => '',
			'custom'             => '',
			'sender_user_id'     => '',
			'received_user_id'   => 0,
			'invitation_link'    => '',
			'request'            => '',
		);
		
		$args = wp_parse_args($args,$defaults);
		
		$content            = $args['content'];
		$title              = $args['title'];
		$break              = $args['break'];
		$user_id            = $args['user_id'];
		$post_id            = $args['post_id'];
		$comment_id         = $args['comment_id'];
		$reset_password     = $args['reset_password'];
		$confirm_link_email = $args['confirm_link_email'];
		$item_price         = $args['item_price'];
		$item_name          = $args['item_name'];
		$item_currency      = $args['item_currency'];
		$payer_email        = $args['payer_email'];
		$first_name         = $args['first_name'];
		$last_name          = $args['last_name'];
		$item_transaction   = $args['item_transaction'];
		$date               = $args['date'];
		$time               = $args['time'];
		$category           = $args['category'];
		$custom             = $args['custom'];
		$sender_user_id     = $args['sender_user_id'];
		$received_user_id   = $args['received_user_id'];
		$invitation_link    = $args['invitation_link'];
		$request            = $args['request'];

		$content = str_ireplace('[%blogname%]', '<span class="mail-class-blogname">'.get_bloginfo('name').'</span>', $content);
		$content = str_ireplace('[%site_url%]', esc_url(home_url('/')), $content);
		
		if ($user_id > 0) {
			$user = new WP_User($user_id);
			$content = str_ireplace('[%messages_url%]' , esc_url(wpqa_messages_permalink()), $content);
			$content = str_ireplace('[%user_login%]'   , '<span class="mail-class-user_login">'.$user->user_login.'</span>', $content);
			$content = str_ireplace('[%user_name%]'    , '<span class="mail-class-user_name">'.$user->user_login.'</span>', $content);
			$content = str_ireplace('[%user_nicename%]', '<span class="mail-class-user_nicename">'.ucfirst($user->user_nicename).'</span>', $content);
			$content = str_ireplace('[%display_name%]' , '<span class="mail-class-display_name">'.ucfirst($user->display_name).'</span>', $content);
			$content = str_ireplace('[%user_email%]'   , '<span class="mail-class-user_email">'.$user->user_email.'</span>', $content);
			$content = str_ireplace('[%user_profile%]' , wpqa_profile_url($user->ID), $content);
			$content = str_ireplace('[%users_link%]'   , admin_url("users.php?role=wpqa_under_review"), $content);
		}
		
		if ($sender_user_id == "anonymous") {
			$content = str_ireplace('[%user_login_sender%]'   , '<span class="mail-class-user_login_sender">'.esc_html__("Anonymous","wpqa").'</span>', $content);
			$content = str_ireplace('[%user_name_sender%]'    , '<span class="mail-class-user_name_sender">'.esc_html__("Anonymous","wpqa").'</span>', $content);
			$content = str_ireplace('[%user_nicename_sender%]', '<span class="mail-class-user_nicename_sender">'.esc_html__("Anonymous","wpqa").'</span>', $content);
			$content = str_ireplace('[%display_name_sender%]' , '<span class="mail-class-display_name_sender">'.esc_html__("Anonymous","wpqa").'</span>', $content);
			$content = str_ireplace('[%user_email_sender%]'   , '<span class="mail-class-user_email_sender">'.esc_html__("Anonymous","wpqa").'</span>', $content);
			$content = str_ireplace('[%user_profile_sender%]' , esc_url(home_url('/')), $content);
		}else if (is_numeric($sender_user_id) && $sender_user_id > 0) {
			$user = new WP_User($sender_user_id);
			$content = str_ireplace('[%user_login_sender%]'   , '<span class="mail-class-user_login_sender">'.$user->user_login.'</span>', $content);
			$content = str_ireplace('[%user_name_sender%]'    , '<span class="mail-class-user_name_sender">'.$user->user_login.'</span>', $content);
			$content = str_ireplace('[%user_nicename_sender%]', '<span class="mail-class-user_nicename_sender">'.ucfirst($user->user_nicename).'</span>', $content);
			$content = str_ireplace('[%display_name_sender%]' , '<span class="mail-class-display_name_sender">'.ucfirst($user->display_name).'</span>', $content);
			$content = str_ireplace('[%user_email_sender%]'   , '<span class="mail-class-user_email_sender">'.$user->user_email.'</span>', $content);
			$content = str_ireplace('[%user_profile_sender%]' , wpqa_profile_url($user->ID), $content);
		}else {
			if (is_object($sender_user_id)) {
				$content = str_ireplace('[%user_login_sender%]'   , '<span class="mail-class-user_login_sender">'.$sender_user_id->comment_author.'</span>', $content);
				$content = str_ireplace('[%user_name_sender%]'    , '<span class="mail-class-user_name_sender">'.$sender_user_id->comment_author.'</span>', $content);
				$content = str_ireplace('[%user_nicename_sender%]', '<span class="mail-class-user_nicename_sender">'.ucfirst($sender_user_id->comment_author).'</span>', $content);
				$content = str_ireplace('[%display_name_sender%]' , '<span class="mail-class-display_name_sender">'.ucfirst($sender_user_id->comment_author).'</span>', $content);
				$content = str_ireplace('[%user_email_sender%]'   , '<span class="mail-class-user_email_sender">'.$sender_user_id->comment_author_email.'</span>', $content);
				$content = str_ireplace('[%user_profile_sender%]' , esc_url(($sender_user_id->comment_author_url != ''?$sender_user_id->comment_author_url:home_url('/'))), $content);
			}
		}
		
		if ($received_user_id > 0) {
			$user = new WP_User($received_user_id);
			$content = str_ireplace('[%user_login%]'   , '<span class="mail-class-user_login">'.$user->user_login.'</span>', $content);
			$content = str_ireplace('[%user_name%]'    , '<span class="mail-class-user_name">'.$user->user_login.'</span>', $content);
			$content = str_ireplace('[%user_nicename%]', '<span class="mail-class-user_nicename">'.ucfirst($user->user_nicename).'</span>', $content);
			$content = str_ireplace('[%display_name%]' , '<span class="mail-class-display_name">'.ucfirst($user->display_name).'</span>', $content);
			$content = str_ireplace('[%user_email%]'   , '<span class="mail-class-user_email">'.$user->user_email.'</span>', $content);
			$content = str_ireplace('[%user_profile%]' , wpqa_profile_url($user->ID), $content);
		}
		
		if ($reset_password != '') {
			$content = str_ireplace('[%reset_password%]', $reset_password, $content);
		}
		if ($confirm_link_email != '') {
			$content = str_ireplace('[%confirm_link_email%]', $confirm_link_email, $content);
		}
		
		if ($comment_id > 0) {
			$get_comment = get_comment($comment_id);
			if ($post_id > 0) {
				$post = get_post($post_id);
			}
			$get_permalink = (isset($post->post_type) && $post->post_type == "posts"?wpqa_custom_permalink($post_id,"view_group_posts","view_group_post"):get_permalink($post_id));
			$content = str_ireplace('[%comment_link%]', admin_url("edit-comments.php?comment_status=moderated"), $content);
			$content = str_ireplace('[%answer_link%]' , $get_permalink.'#li-comment-'.$comment_id, $content);
			$content = str_ireplace('[%answer_url%]'  , $get_permalink.'#li-comment-'.$comment_id, $content);
			$content = str_ireplace('[%comment_url%]' , $get_permalink.'#li-comment-'.$comment_id, $content);
			$content = str_ireplace('[%the_name%]'    , '<span class="mail-class-the_name">'.$get_comment->comment_author.'</span>', $content);
		}
		
		if ($post_id > 0) {
			if (!isset($post) || (isset($post) && !is_object($post))) {
				$post = get_post($post_id);
			}
			$get_permalink = (isset($post->post_type) && $post->post_type == "posts"?wpqa_custom_permalink($post_id,"view_group_posts","view_group_post"):get_permalink($post_id));
			$get_title = (isset($post->post_type) && $post->post_type == "posts"?wpqa_excerpt(10,"words",false,"return","yes",$post->post_content):$post->post_title);
			$content = str_ireplace('[%messages_title%]', '<span class="mail-class-messages_title">'.$get_title.'</span>', $content);
			$content = str_ireplace('[%question_title%]', '<span class="mail-class-question_title">'.$get_title.'</span>', $content);
			$content = str_ireplace('[%post_title%]'    , '<span class="mail-class-post_title">'.$get_title.'</span>', $content);
			$content = str_ireplace('[%answer_url%]'    , ($post->post_status == 'publish'?$get_permalink:admin_url('post.php?post='.$post_id.'&action=edit')), $content);
			$content = str_ireplace('[%question_link%]' , ($post->post_status == 'publish'?$get_permalink:admin_url('post.php?post='.$post_id.'&action=edit')), $content);
			$content = str_ireplace('[%post_link%]'     , ($post->post_status == 'publish'?$get_permalink:admin_url('post.php?post='.$post_id.'&action=edit')), $content);
			if ($post->post_author > 0) {
				$get_the_author = get_user_by("id",$post->post_author);
				$the_author_post = $get_the_author->display_name;
			}else {
				$the_author_post = get_post_meta($post_id,($post->post_type == wpqa_questions_type || $post->post_type == wpqa_asked_questions_type?'question_username':'post_username'),true);
				$the_author_post = ($the_author_post != ''?$the_author_post:esc_html__("Anonymous","wpqa"));
			}
			$content = str_ireplace('[%the_author_question%]', '<span class="mail-class-the_author_question">'.$the_author_post.'</span>', $content);
			$content = str_ireplace('[%the_author_post%]'    , '<span class="mail-class-the_author_post">'.$the_author_post.'</span>', $content);
		}

		$content = str_ireplace('[%item_price%]', '<span class="mail-class-item_price">'.($item_price != ''?$item_price:esc_html__('Free','wpqa')).'</span>', $content);
		if ($item_name != '') {
			$content = str_ireplace('[%item_name%]', '<span class="mail-class-item_name">'.$item_name.'</span>', $content);
		}
		if ($item_currency != '') {
			$content = str_ireplace('[%item_currency%]', '<span class="mail-class-item_currency">'.$item_currency.'</span>', $content);
		}
		if ($payer_email != '') {
			$content = str_ireplace('[%payer_email%]', '<span class="mail-class-payer_email">'.$payer_email.'</span>', $content);
		}
		if ($first_name != '') {
			$content = str_ireplace('[%first_name%]', '<span class="mail-class-first_name">'.$first_name.'</span>', $content);
		}else if (isset($user) && isset($user->display_name)) {
			$content = str_ireplace('[%first_name%]', '<span class="mail-class-first_name">'.ucfirst($user->display_name).'</span>', $content);
		}else {
			$content = str_ireplace('[%first_name%]', '', $content);
		}
		if ($last_name != '') {
			$content = str_ireplace('[%last_name%]', '<span class="mail-class-last_name">'.$last_name.'</span>', $content);
		}else {
			$content = str_ireplace('[%last_name%]', '', $content);
		}
		$content = str_ireplace('[%item_transaction%]', '<span class="mail-class-item_transaction">'.($item_transaction != ''?$item_transaction:esc_html__('Free','wpqa')) .'</span>', $content);
		if ($date != '') {
			$content = str_ireplace('[%date%]', '<span class="mail-class-date">'.$date.'</span>', $content);
		}
		if ($time != '') {
			$content = str_ireplace('[%time%]', '<span class="mail-class-time">'.$time.'</span>', $content);
		}
		if ($category != '') {
			$content = str_ireplace('[%category_link%]', admin_url('edit.php?post_type=request&request=category'), $content);
			$content = str_ireplace('[%category_name%]', '<span class="mail-class-category_name">'.$category.'</span>', $content);
		}
		if ($request != '') {
			$content = str_ireplace('[%request_link%]', admin_url('edit.php?post_type=request'), $content);
			$content = str_ireplace('[%request_name%]', '<span class="mail-class-request_name">'.$request.'</span>', $content);
		}
		if ($invitation_link != '') {
			$content = str_ireplace('[%invitation_link%]', $invitation_link, $content);
		}
		if ($custom != '') {
			$custom_content = apply_filters('wpqa_filter_send_email',false);
			$content = str_ireplace('[%custom_link%]', $custom_content, $content);
			$content = str_ireplace('[%custom_name%]', '<span class="mail-class-custom_name">'.$custom.'</span>', $content);
		}
		$break = apply_filters("wpqa_email_template_break",$break);
		if ($break == "break") {
			$return = nl2br(wpqa_kses_stip($content,"yes"));
		}else {
			if ($title == true) {
				$return = strip_tags(stripslashes($content));
			}else {
				$return = stripslashes($content);
			}
		}
		return $return;
	}
endif;
/* Emails */
if (!function_exists('wpqa_email_code')) :
	function wpqa_email_code($content,$mail = "",$schedule = "",$user_id = "",$post_type = "",$number = 10) {
		$active_footer_email = wpqa_options("active_footer_email");
		$social_footer_email = wpqa_options("social_footer_email");
		$copyrights_for_email = wpqa_options("copyrights_for_email");
		$logo_email_template = wpqa_image_url_id(wpqa_options("logo_email_template"));
		$custom_image_mail = wpqa_image_url_id(wpqa_options("custom_image_mail"));
		$background_email = wpqa_options("background_email");
		$background_email = ($background_email != ""?$background_email:"#272930");
		$email_style = wpqa_options("email_style");
		$social_td = $recent_posts = '';
		if ($social_footer_email == "on") {
			$sort_social = wpqa_options("sort_social");
			$social = array(
				array("name" => "Facebook",   "value" => "facebook",   "icon" => "facebook"),
				array("name" => "Twitter",    "value" => "twitter",    "icon" => "twitter"),
				array("name" => "tiktok",     "value" => "tiktok",     "icon" => "tiktok"),
				array("name" => "Linkedin",   "value" => "linkedin",   "icon" => "linkedin"),
				array("name" => "Dribbble",   "value" => "dribbble",   "icon" => "dribbble"),
				array("name" => "Youtube",    "value" => "youtube",    "icon" => "play"),
				array("name" => "Vimeo",      "value" => "vimeo",      "icon" => "vimeo"),
				array("name" => "Skype",      "value" => "skype",      "icon" => "skype"),
				array("name" => "WhatsApp",   "value" => "whatsapp",   "icon" => "whatsapp"),
				array("name" => "Soundcloud", "value" => "soundcloud", "icon" => "soundcloud"),
				array("name" => "Instagram",  "value" => "instagram",  "icon" => "instagram"),
				array("name" => "Pinterest",  "value" => "pinterest",  "icon" => "pinterest")
			);
			if (is_array($sort_social) && !empty($sort_social)) {
				$k = 0;
				foreach ($sort_social as $key_r => $value_r) {$k++;
					if (isset($sort_social[$key_r]["value"])) {
						$sort_social_value = $sort_social[$key_r]["value"];
						$social_icon_h = wpqa_options($sort_social_value."_icon_h");
						if ($sort_social_value != "rss" && $social_icon_h != "") {
							$social_url = ($sort_social_value == "skype"?"skype:":"").($sort_social_value == "whatsapp"?"whatsapp://send?abid=":"").($sort_social_value != "skype" && $sort_social_value != "whatsapp"?esc_url($social_icon_h):$social_icon_h).($sort_social_value == "skype"?"?call":"").($sort_social_value == "whatsapp"?"&text=".esc_html__("Hello","wpqa"):"");
							if ($email_style == "style_2") {
								$social_td .= '<a href="'.$social_url.'" title="'.$value_r["name"].'" style="color:#707478; margin-right:10px;font-size:14px;font-weight:400;">'.$value_r["name"].'</a>';
							}else {
								$social_td .= '<a href="'.$social_url.'" title="'.$value_r["name"].'" style="color:#707478; margin-right:10px;font-size:14px;font-weight:400;"><img alt="'.$value_r["name"].'" width="32" height="32" src="'.get_template_directory_uri().'/images/social/'.$value_r["value"].'.png" style="line-height:100%;outline:none;text-decoration:none;border:none"></a>';
							}
						}
					}
				}
			}
		}

		$primary_color = wpqa_options("primary_color");
		if ($primary_color != "") {
			$skin = $primary_color;
		}else {
			$skins = array("skin" => wpqa_theme_color(),"violet" => "#9349b1","blue" => "#00aeef","bright_red" => "#fa4b2a","cyan" => "#058b7b","green" => "#81b441","red" => "#e91802");
			$site_skin = wpqa_options('site_skin');
			if ($site_skin == "skin" || $site_skin == "default" || $site_skin == "") {
				$skin = $skins["skin"];
			}else {
				$skin = $skins[$site_skin];
			}
		}

		if ($schedule != "" || $post_type == "question_cronjob" || $post_type == "post_cronjob" || $post_type == "answer_cronjob") {
			$block_users = wpqa_options("block_users");
			$author__not_in = array($user_id);
			if ($block_users == "on") {
				if ($user_id > 0) {
					$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
					if (is_array($get_block_users) && !empty($get_block_users)) {
						$author__not_in = array_merge($get_block_users,array($user_id));
					}
				}
			}
		}

		if ($schedule != "" || $post_type == "question_cronjob" || $post_type == "post_cronjob") {
			if ($post_type == "question_cronjob" || $post_type == "post_cronjob") {
				$post_type = ($post_type == "question_cronjob"?wpqa_questions_type:"post");
			}
			global $post;
			$schedule_content = wpqa_options("schedule_content".($post_type == "post"?"_post":""));
			if ($schedule == "daily") {
				$specific_date = "24 hours ago";
			}else if ($schedule == "weekly") {
				$specific_date = "1 week ago";
			}else if ($schedule == "monthly") {
				$specific_date = "1 month ago";
			}else if ($schedule == "twicedaily") {
				$specific_date = "12 hours ago";
			}else if ($schedule == "hourly") {
				$specific_date = "1 hours ago";
			}else if ($schedule == "twicehourly") {
				$specific_date = "30 minutes ago";
			}
			$ask_question_to_users = wpqa_options("ask_question_to_users");
			$ask_user_meta_query = array(
				"meta_query" => array(
					array("key" => "wpqa_post_scheduled_email","value" => "yes"),
				)
			);
			if ($ask_question_to_users == "on") {
				$ask_user_meta_query = array(
					"meta_query" => array(
						'relation' => 'or',
						array(
							'relation' => 'and',
							array("key" => "user_id","compare" => "NOT EXISTS"),
							array("key" => "wpqa_post_scheduled_email","value" => "yes")
						),
						array(
							'relation' => 'and',
							array("key" => "user_id","compare" => "EXISTS"),
							array("key" => "count_post_all","value" => "1","compare" => ">="),
							array("key" => "wpqa_post_scheduled_email","value" => "yes")
						)
					)
				);
			}
			$recent_posts_query = new WP_Query(array_merge($ask_user_meta_query,array('author__not_in' => $author__not_in,'date_query' => array(array('after' => $specific_date,'inclusive' => true)),'post_type' => ($post_type == wpqa_questions_type?array($post_type,wpqa_asked_questions_type):$post_type),'ignore_sticky_posts' => 1,'cache_results' => false,'no_found_rows' => true,'posts_per_page' => $number)));
			if ($recent_posts_query->have_posts()) :
				while ( $recent_posts_query->have_posts() ) : $recent_posts_query->the_post();
					delete_post_meta($post->ID,"wpqa_post_scheduled_email");
					if ($email_style == "style_2") {
						$recent_posts .= '<p><a href="'.get_permalink($post->ID).'" style="font-size:20px; color:#475568;font-weight:bold;margin-top:30px;line-height:24px;text-decoration: none;" class="hover">'.get_the_title($post->ID).'</a></p>';
					}else {
						$recent_posts .= '<tr>
							<td>
								<p style="font-size:14px;color:'.$skin.';line-height:120%;margin-top:0;margin-bottom:10px;"><a style="text-decoration:none;color:#26333b" href="'.get_permalink($post->ID).'">'.get_the_title($post->ID).'</a></p>
							</td>
						</tr>';
					}
				   endwhile;
			else :
				return 'no_'.$post_type;
			endif;
			wp_reset_postdata();
		}

		$is_rtl = is_rtl();

		$first_email_part = apply_filters('wpqa_email_first_part','<!doctype html>
		<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
			<head>
				<title></title>
				<!--[if !mso]><!-- -->
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<!--<![endif]-->
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<style type="text/css">
					#outlook a {
						padding: 0;
					}
					body {
						margin: 0;
						padding: 0;
						-webkit-text-size-adjust: 100%;
						-ms-text-size-adjust: 100%;
					}
					table,td {
						border-collapse: collapse;
						mso-table-lspace: 0pt;
						mso-table-rspace: 0pt;
					}
					img {
						border: 0;
						height: auto;
						line-height: 100%;
						outline: none;
						text-decoration: none;
						-ms-interpolation-mode: bicubic;
					}
					p {
						display: block;
						margin: 13px 0;
						line-height: 24px;
					}
					a.hover:hover {
						color: '.$skin.' !important;
					}
					/* Ar-Style */
					.rtl-css {
						text-align: right !important;
					}
				</style>
				<!--[if mso]>
				<xml>
					<o:OfficeDocumentSettings>
					<o:AllowPNG/>
					<o:PixelsPerInch>96</o:PixelsPerInch>
					</o:OfficeDocumentSettings>
				</xml>
				<![endif]-->
				<!--[if lte mso 11]>
				<style type="text/css">
					.mj-outlook-group-fix {
						width:100% !important;
					}
				</style>
				<![endif]-->
				<!--[if !mso]><!-->
				<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet" type="text/css">
				<style type="text/css">
					@import url(https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap);
				</style>
				<!--<![endif]-->
				<style type="text/css">
				@media only screen and (min-width:480px) {
					.mj-column-per-100 {
						width: 100% !important;
						max-width: 100%;
					}
				}
				</style>
				<style type="text/css">
				@media only screen and (max-width:480px) {
					table.mj-full-width-mobile {
						width: 100% !important;
					}
					td.mj-full-width-mobile {
						width: auto !important;
					}
					.wrapper {
						margin: 0 10px 0 10px !important;
					}
					p {
						line-height: 26px !important;
					}
				}
				</style>
			</head>
			<body style="background-color:#eeeeee;">
				<div style="background-color:#eeeeee;">
					<!--[if mso | IE]>
					<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px;" width="600">
						<tr>
							<td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
								<![endif]-->
								<div style="margin:0px auto;max-width:600px;">
									<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
										<tbody>
											<tr>
												<td style="direction:'.($is_rtl?'rtl':'ltr').';font-size:0px;padding:20px 0;text-align:center;">
													<!--[if mso | IE]>
													<table role="presentation" border="0" cellpadding="0" cellspacing="0">
														'.($email_style == "style_2"?'<tr>
															<td width="600px">
																<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px;" width="600">
																	<tr>
																		<td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
																			<![endif]-->
																			<div style="margin:0px auto;border-radius:12px 12px 0 0;max-width:600px;">
																				<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;border-radius:12px 12px 0 0;">
																					<tbody>
																						<tr>
																							<td style="direction:'.($is_rtl?'rtl':'ltr').';font-size:0px;padding:0 0 0 0;text-align:center;">
																								<!--[if mso | IE]>
																								<table role="presentation" border="0" cellpadding="0" cellspacing="0">
																									<tr>
																										<td style="vertical-align:top;width:600px;">
																											<![endif]-->
																											<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:'.($is_rtl?'rtl':'ltr').';display:inline-block;vertical-align:top;width:100%;">
																												<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																													<tbody>
																														<tr>
																															<td style="vertical-align:top;padding:0 0 30px 0;">
																																<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																																	<tr>
																																		<td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
																																			<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
																																				<tbody>
																																					<tr>
																																						<td style="width:140px;">
																																							<a href="'.esc_url(home_url('/')).'" target="_blank">'.($logo_email_template != ''?'<img style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="140" height="auto" alt="'.esc_attr(get_option('blogname')).'" src="'.$logo_email_template.'">':'').'</a>
																																						</td>
																																					</tr>
																																				</tbody>
																																			</table>
																																		</td>
																																	</tr>
																																</table>
																															</td>
																														</tr>
																													</tbody>
																												</table>
																											</div>
																											<!--[if mso | IE]>
																										</td>
																									</tr>
																								</table>
																								<![endif]-->
																							</td>
																						</tr>
																					</tbody>
																				</table>
																			</div>
																			<!--[if mso | IE]>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>':'').'
														<tr>
															<td width="600px">
																<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px;" width="600">
																	<tr>
																		<td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
																			<![endif]-->
																			<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;'.($email_style == "style_2"?"border-radius:12px;":"").'max-width:600px;" class="wrapper">
																				<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;'.($email_style == "style_2"?"border-radius:12px;":"border:solid 1px #d9d9d9;").'">
																					<tbody>
																						<tr>
																							<td style="direction:'.($is_rtl?'rtl':'ltr').';font-size:0px;padding:0;text-align:center;">
																								<!--[if mso | IE]>
																								<table role="presentation" border="0" cellpadding="0" cellspacing="0">
																									<tr>
																										<td style="vertical-align:top;width:600px;">
																											<![endif]-->
																											<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:'.($is_rtl?'rtl':'ltr').';display:inline-block;vertical-align:top;width:100%;"'.($is_rtl?' class="rtl-css"':'').'>
																												<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																													<tbody>
																														<tr>
																															<td style="vertical-align:top;'.($email_style == "style_2"?'padding-top: 20px;':'padding: 20px;').'">
																																<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																																	'.($email_style == "style_2"?'':'
																																	<tr style="padding:0 20px;width:100%;background-color:'.$background_email.';">
																																		<td style="vertical-align:top;width:600px;">
																																			<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:'.($is_rtl?'rtl':'ltr').';display:inline-block;vertical-align:top;width:100%;">
																																				<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																																					<tbody>
																																						<tr>
																																							<td style="vertical-align:top;">
																																								<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																																									<tr>
																																										<td align="center" style="font-size:0px;padding:30px;word-break:break-word;">
																																											<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
																																												<tbody>
																																													<tr>
																																														<td style="width:140px;">
																																															<a href="'.esc_url(home_url('/')).'" target="_blank">'.($logo_email_template != ''?'<img style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="140" height="auto" alt="'.esc_attr(get_option('blogname')).'" src="'.$logo_email_template.'">':'').'</a>
																																														</td>
																																													</tr>
																																												</tbody>
																																											</table>
																																										</td>
																																									</tr>
																																								</table>
																																							</td>
																																						</tr>
																																					</tbody>
																																				</table>
																																			</div>
																																		</td>
																																	</tr>').'
																																	'.($mail == 'email_custom_mail' && $custom_image_mail != ''?'<tr>
																																	<td style="line-height:32px;padding:20px 20px 20px;text-align:center;" valign="baseline"><a href="'.esc_url(home_url('/')).'" target="_blank">'.($custom_image_mail != ''?'<img alt="'.esc_attr(get_option('blogname')).'" src="'.$custom_image_mail.'">':'').'</a></td>
																																	</tr>':'').'
																																	<tr>
																																		<td align="'.($is_rtl?'right':'left').'" style="font-size:0px;padding:10px '.($email_style == "style_2"?"25px":"0").' 20px;word-break:break-word;"'.($is_rtl?' class="rtl-css"':'').'>
																																			<div style="font-family:Roboto, sans-serif;font-size:14px;line-height:25px;text-align:'.($is_rtl?'right':'left').';color:#000000;"'.($is_rtl?' class="rtl-css"':'').'>
																																				');
		
		$content_email_part = apply_filters('wpqa_email_content_part',$content);
		$second_email_part = apply_filters('wpqa_email_second_part','
																																			</div>
																																		</td>
																																	</tr>');
		$recent_posts_email_part = apply_filters('wpqa_email_recent_posts_part',(isset($recent_posts) && $recent_posts != ''?$recent_posts:'').($schedule != ''?(isset($schedule_content) && $schedule_content != ''?wpqa_send_mail(array('content' => $schedule_content)):''):''));
		$third_email_part = apply_filters('wpqa_email_third_part','
																																			
																																</table>
																															</td>
																														</tr>
																													</tbody>
																												</table>
																											</div>
																											<!--[if mso | IE]>
																										</td>
																									</tr>
																								</table>
																								<![endif]-->
																							</td>
																						</tr>
																					</tbody>
																				</table>
																			</div>
																			<!--[if mso | IE]>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
														'.($active_footer_email == 'on'?'
															'.(isset($social_td) && $social_td != ''?'
															<tr>
																<td width="600px">
																	<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px;" width="600">
																		<tr>
																			<td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
																				<![endif]-->
																				<div style="margin:0px auto;max-width:600px;">
																					<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
																						<tbody>
																							<tr>
																								<td style="direction:'.($is_rtl?'rtl':'ltr').';font-size:0px;padding:0;text-align:center;">
																									<!--[if mso | IE]>
																									<table role="presentation" border="0" cellpadding="0" cellspacing="0">
																										<tr>
																											<td style="vertical-align:top;width:600px;">
																												<![endif]-->
																												<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:'.($is_rtl?'rtl':'ltr').';display:inline-block;vertical-align:top;width:100%;">
																													<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																														<tbody>
																															<tr>
																																<td style="vertical-align:top;padding-top:20px;">
																																	<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																																		<tr>
																																			<td align="left" style="font-size:0px;padding:10px 25px;padding-bottom:5px;word-break:break-word;">
																																				<table cellpadding="0" cellspacing="0" width="100%" border="0" style="color:#000000;font-family:Roboto, sans-serif;font-size:13px;line-height:22px;table-layout:auto;width:100%;border:none;">
																																				<th style="padding:0">'.$social_td.'</th>
																																				</table>
																																			</td>
																																		</tr>
																																	</table>
																																</td>
																															</tr>
																														</tbody>
																													</table>
																												</div>
																												<!--[if mso | IE]>
																											</td>
																										</tr>
																									</table>
																									<![endif]-->
																								</td>
																							</tr>
																						</tbody>
																					</table>
																				</div>
																				<!--[if mso | IE]>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
															':'').
															($copyrights_for_email != ""?'
															<tr>
																<td width="600px">
																	<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px;" width="600">
																		<tr>
																			<td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
																				<![endif]-->
																				<div style="margin:0px auto;max-width:600px;">
																					<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
																						<tbody>
																							<tr>
																								<td style="direction:'.($is_rtl?'rtl':'ltr').';font-size:0px;padding:0;text-align:center;">
																									<!--[if mso | IE]>
																									<table role="presentation" border="0" cellpadding="0" cellspacing="0">
																										<tr>
																											<td style="vertical-align:top;width:600px;">
																												<![endif]-->
																												<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:'.($is_rtl?'rtl':'ltr').';display:inline-block;vertical-align:top;width:100%;"'.($is_rtl?' class="rtl-css"':'').'>
																													<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																														<tbody>
																															<tr>
																																<td style="vertical-align:top;padding-top:0;">
																																	<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																																		<tr>
																																			<td align="center" style="font-size:0px;padding:0;padding-bottom:20px;word-break:break-word;">
																																				<div style="font-family:Roboto, sans-serif;font-size:14px;line-height:25px;text-align:center;color:#707478;">
																																					<p>'.$copyrights_for_email.'</p>
																																				</div>
																																			</td>
																																		</tr>
																																	</table>
																																</td>
																															</tr>
																														</tbody>
																													</table>
																												</div>
																												<!--[if mso | IE]>
																											</td>
																										</tr>
																									</table>
																									<![endif]-->
																								</td>
																							</tr>
																						</tbody>
																					</table>
																				</div>
																				<!--[if mso | IE]>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
															':'').'
														':'').'
													</table>
													<![endif]-->
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<!--[if mso | IE]>
							</td>
						</tr>
					</table>
					<![endif]-->
				</div>
			</body>
		</html>');
		return $first_email_part.$content_email_part.$second_email_part.$recent_posts_email_part.$third_email_part;
	}
endif;
/* Send admin notification */
if (!function_exists('wpqa_send_admin_notification')) :
	function wpqa_send_admin_notification($post_id,$post_title) {
		$blogname = get_option('blogname');
		$email = get_option('admin_email');
		$headers = "MIME-Version: 1.0\r\n" . "From: ".$blogname." "."<".$email.">\n" . "Content-Type: text/HTML; charset=\"" . get_option('blog_charset') . "\"\r\n";
		$message = esc_html__('Hello there,','wpqa').'<br/><br/>'. 
		esc_html__('A new post has been submitted in','wpqa').' '.$blogname.' site. '.esc_html__('Please find details below:','wpqa').'<br/><br/>'.
		
		'Post title: '.$post_title.'<br/><br/>';
		$post_author_name = get_post_meta($post_id,'ap_author_name',true);
		$post_author_email = get_post_meta($post_id,'ap_author_email',true);
		$post_author_url = get_post_meta($post_id,'ap_author_url',true);
		if ($post_author_name != ''){
			$message .= 'Post Author Name: '.$post_author_name.'<br/><br/>';
		}
		if ($post_author_email != ''){
			$message .= 'Post Author Email: '.$post_author_email.'<br/><br/>';
		}
		if ($post_author_url != ''){
			$message .= 'Post Author URL: '.$post_author_url.'<br/><br/>';
		}
		
		$message .= '____<br/><br/>
		'.esc_html__('To take action (approve/reject)- please go here:','wpqa').'<br/>'
		.admin_url().'post.php?post='.$post_id.'&action=edit <br/><br/>
		
		'.esc_html__('Thank You','wpqa');
		$title = esc_html__('New Post Submission','wpqa');
		wp_mail($email,$title,$message,$headers);
	}
endif;
/* Schedule mail */
function wpqa_schedule_mails($schedule,$type = "question",$number = 10,$activate_schedules = false,$schedules_groups = "") {
	$post_schedules = wpqa_options($type."_schedules");
	if ($post_schedules == "on" || $activate_schedules == true) {
		$schedules_groups = ($schedules_groups != ""?$schedules_groups:$type."_schedules_groups");
		$post_schedules_groups = wpqa_options($schedules_groups);
		$email_title = wpqa_options("title_".$type."_schedules");
		$email_title = ($email_title != ""?$email_title:($type == "post"?esc_html__("Recent posts","wpqa"):esc_html__("Recent questions","wpqa")));
		$mail_user_meta_key = $type."_schedules";
		if ($activate_schedules == true) {
			if ($type == "question") {
				$mail_user_meta_key = "received_email";
			}else {
				$mail_user_meta_key = "received_email_post";
			}
		}
		$users = get_users(array("meta_query" => array("relation" => "AND",array("key" => $mail_user_meta_key,"compare" => "=","value" => "on"),array('relation' => 'OR',array("key" => "unsubscribe_mails","compare" => "NOT EXISTS"),array("key" => "unsubscribe_mails","compare" => "!=","value" => "on"))),"role__not_in" => array("wpqa_under_review","activation","ban_group"),"role__in" => (isset($post_schedules_groups) && is_array($post_schedules_groups)?$post_schedules_groups:array()),"fields" => array("ID","user_email","display_name")));
		if (isset($users) && is_array($users) && !empty($users)) {
			foreach ($users as $key => $value) {
				$user_id = $value->ID;
				$send_text = wpqa_send_mail(
					array(
						'content'          => wpqa_options("email_".$type."_schedules"),
						'received_user_id' => $user_id,
					)
				);
				$email_title = wpqa_send_mail(
					array(
						'content'          => $email_title,
						'title'            => true,
						'break'            => '',
						'received_user_id' => $user_id,
					)
				);
				$last_message_email = wpqa_email_code($send_text,"email_".$type."_schedules",$schedule,$user_id,$type,$number);
				if ($last_message_email != "no_question" && $last_message_email != "no_post") {
					wpqa_send_mails(
						array(
							'toEmail'     => esc_html($value->user_email),
							'toEmailName' => esc_html($value->display_name),
							'title'       => $email_title,
							'message'     => $last_message_email,
							'email_code'  => 'code',
						)
					);
				}
			}
		}
	}
}
/* Cron schedules */
add_filter("cron_schedules","wpqa_cron_schedules");
if (!function_exists('wpqa_cron_schedules')) :
	function wpqa_cron_schedules($schedules) {
		$schedules['weekly'] = array(
			'interval' => WEEK_IN_SECONDS,
			'display'  => esc_html__('Once Weekly','wpqa'),
		);
		$schedules['monthly'] = array(
			'interval' => MONTH_IN_SECONDS,
			'display'  => esc_html__('Once Monthly','wpqa'),
		);
		$schedules['twicehourly'] = array(
			'interval' => HOUR_IN_SECONDS/2,
			'display'  => esc_html__('Twice Hourly','wpqa'),
		);
		return $schedules;
	}
endif;
/* Schedule mails */
add_action("wpqa_init","wpqa_action_schedule_mails");
function wpqa_action_schedule_mails() {
	$question_schedules = wpqa_options("question_schedules");
	if ($question_schedules == "on") {
		$wpqa_schedules_time = get_option("wpqa_schedules_time");
		$schedules_time_hour = wpqa_options("schedules_time_hour");
		$schedules_time_day = wpqa_options("schedules_time_day");
		if ($wpqa_schedules_time == "") {
			wp_clear_scheduled_hook("wpqa_scheduled_mails_daily");
			wp_clear_scheduled_hook("wpqa_scheduled_mails_weekly");
			wp_clear_scheduled_hook("wpqa_scheduled_mails_monthly");
			$wpqa_schedules_time = ($schedules_time_day != "" && $schedules_time_hour != ""?strtotime(date("Y").'-'.date("m")." next ".$schedules_time_day." ".$schedules_time_hour.":00"):time());
			update_option("wpqa_schedules_time",$wpqa_schedules_time);
		}
		
		if (!wp_next_scheduled('wpqa_scheduled_mails_daily')) {
			wp_schedule_event($wpqa_schedules_time,'daily','wpqa_scheduled_mails_daily');
		}
		if (!wp_next_scheduled('wpqa_scheduled_mails_weekly')) {
			wp_schedule_event($wpqa_schedules_time,'weekly','wpqa_scheduled_mails_weekly');
		}
		if (!wp_next_scheduled('wpqa_scheduled_mails_monthly')) {
			wp_schedule_event($wpqa_schedules_time,'monthly','wpqa_scheduled_mails_monthly');
		}
	}
	$post_schedules = wpqa_options("post_schedules");
	if ($post_schedules == "on") {
		$wpqa_schedules_time_post = get_option("wpqa_schedules_time_post");
		$schedules_time_hour_post = wpqa_options("schedules_time_hour_post");
		$schedules_time_day_post = wpqa_options("schedules_time_day_post");
		if ($wpqa_schedules_time_post == "") {
			wp_clear_scheduled_hook("wpqa_scheduled_mails_daily_post");
			wp_clear_scheduled_hook("wpqa_scheduled_mails_weekly_post");
			wp_clear_scheduled_hook("wpqa_scheduled_mails_monthly_post");
			$wpqa_schedules_time_post = ($schedules_time_day_post != "" && $schedules_time_hour_post != ""?strtotime(date("Y").'-'.date("m")." next ".$schedules_time_day_post." ".$schedules_time_hour_post.":00"):time());
			update_option("wpqa_schedules_time_post",$wpqa_schedules_time_post);
		}
		
		if (!wp_next_scheduled('wpqa_scheduled_mails_daily_post')) {
			wp_schedule_event($schedules_time_day_post,'daily','wpqa_scheduled_mails_daily_post');
		}
		if (!wp_next_scheduled('wpqa_scheduled_mails_weekly_post')) {
			wp_schedule_event($schedules_time_day_post,'weekly','wpqa_scheduled_mails_weekly_post');
		}
		if (!wp_next_scheduled('wpqa_scheduled_mails_monthly_post')) {
			wp_schedule_event($schedules_time_day_post,'monthly','wpqa_scheduled_mails_monthly_post');
		}
	}
	$way_sending_notifications_questions = wpqa_options("way_sending_notifications_questions");
	$way_sending_notifications_posts = wpqa_options("way_sending_notifications_posts");
	$way_sending_notifications_answers = wpqa_options("way_sending_notifications_answers");
	if ($way_sending_notifications_questions == "cronjob" || $way_sending_notifications_posts == "cronjob" || $way_sending_notifications_answers == "cronjob") {
		$wpqa_schedules_time_notification_question = get_option("wpqa_schedules_time_notification_question");
		$schedules_time_hour_notification_question = wpqa_options("schedules_time_hour_notification_question");
		$schedules_time_notification_questions = wpqa_options("schedules_time_notification_questions");
		$schedules_time_notification_posts = wpqa_options("schedules_time_notification_posts");
		$schedules_time_notification_answers = wpqa_options("schedules_time_notification_answers");
		if (($schedules_time_notification_questions == "daily" || $schedules_time_notification_questions == "twicedaily") && $wpqa_schedules_time_notification_question == "") {
			if ($schedules_time_notification_questions == "daily") {
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_daily_question");
			}
			if ($schedules_time_notification_questions == "twicedaily") {
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_twicedaily_question");
			}
			$wpqa_schedules_time_notification_question = ($schedules_time_hour_notification_question != ""?strtotime(date("Y-m-d")." ".$schedules_time_hour_notification_question.":00"):time());
			update_option("wpqa_schedules_time_notification_question",$wpqa_schedules_time_notification_question);
		}
		$wpqa_schedules_time_notification_post = get_option("wpqa_schedules_time_notification_post");
		$schedules_time_hour_notification_post = wpqa_options("schedules_time_hour_notification_post");
		if (($schedules_time_notification_posts == "daily" || $schedules_time_notification_posts == "twicedaily") && $wpqa_schedules_time_notification_post == "") {
			if ($schedules_time_notification_posts == "daily") {
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_daily_post");
			}
			if ($schedules_time_notification_posts == "twicedaily") {
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_twicedaily_post");
			}
			$wpqa_schedules_time_notification_post = ($schedules_time_hour_notification_post != ""?strtotime(date("Y-m-d")." ".$schedules_time_hour_notification_post.":00"):time());
			update_option("wpqa_schedules_time_notification_post",$wpqa_schedules_time_notification_post);
		}
		$wpqa_schedules_time_notification_answer = get_option("wpqa_schedules_time_notification_answer");
		$schedules_time_hour_notification_answer = wpqa_options("schedules_time_hour_notification_answer");
		if (($schedules_time_notification_answers == "daily" || $schedules_time_notification_answers == "twicedaily") && $wpqa_schedules_time_notification_answer == "") {
			if ($schedules_time_notification_answers == "daily") {
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_daily_answer");
			}
			if ($schedules_time_notification_answers == "twicedaily") {
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_twicedaily_answer");
			}
			$wpqa_schedules_time_notification_answer = ($schedules_time_hour_notification_answer != ""?strtotime(date("Y-m-d")." ".$schedules_time_hour_notification_answer.":00"):time());
			update_option("wpqa_schedules_time_notification_answer",$wpqa_schedules_time_notification_answer);
		}
		if ($schedules_time_notification_questions == "daily" && !wp_next_scheduled('wpqa_scheduled_notification_mails_daily_question')) {
			wp_schedule_event($wpqa_schedules_time_notification_question,'daily','wpqa_scheduled_notification_mails_daily_question');
		}
		if ($schedules_time_notification_questions == "twicedaily" && !wp_next_scheduled('wpqa_scheduled_notification_mails_twicedaily_question')) {
			wp_schedule_event($wpqa_schedules_time_notification_question,'twicedaily','wpqa_scheduled_notification_mails_twicedaily_question');
		}
		if ($schedules_time_notification_posts == "daily" && !wp_next_scheduled('wpqa_scheduled_notification_mails_daily_post')) {
			wp_schedule_event($wpqa_schedules_time_notification_post,'daily','wpqa_scheduled_notification_mails_daily_post');
		}
		if ($schedules_time_notification_posts == "twicedaily" && !wp_next_scheduled('wpqa_scheduled_notification_mails_twicedaily_post')) {
			wp_schedule_event($wpqa_schedules_time_notification_post,'twicedaily','wpqa_scheduled_notification_mails_twicedaily_post');
		}
		if ($schedules_time_notification_answers == "daily" && !wp_next_scheduled('wpqa_scheduled_notification_mails_daily_answer')) {
			wp_schedule_event($wpqa_schedules_time_notification_answer,'daily','wpqa_scheduled_notification_mails_daily_answer');
		}
		if ($schedules_time_notification_answers == "twicedaily" && !wp_next_scheduled('wpqa_scheduled_notification_mails_twicedaily_answer')) {
			wp_schedule_event($wpqa_schedules_time_notification_answer,'twicedaily','wpqa_scheduled_notification_mails_twicedaily_answer');
		}
		if ($schedules_time_notification_questions == "hourly" || $schedules_time_notification_questions == "twicehourly" || $schedules_time_notification_posts == "hourly" || $schedules_time_notification_posts == "twicehourly" || $schedules_time_notification_answers == "hourly" || $schedules_time_notification_answers == "twicehourly") {
			if (($schedules_time_notification_questions == "hourly" || $schedules_time_notification_posts == "hourly" || $schedules_time_notification_answers == "hourly") && !wp_next_scheduled('wpqa_scheduled_notification_mails_hourly')) {
				wp_schedule_event(time(),'hourly','wpqa_scheduled_notification_mails_hourly');
			}
			if (($schedules_time_notification_questions == "twicehourly" || $schedules_time_notification_posts == "twicehourly" || $schedules_time_notification_answers == "twicehourly") && !wp_next_scheduled('wpqa_scheduled_notification_mails_twicehourly')) {
				wp_schedule_event(time(),'twicehourly','wpqa_scheduled_notification_mails_twicehourly');
			}
		}
	}
}
/* Daily mails for questions */
add_action('wpqa_scheduled_mails_daily','wpqa_scheduled_mails_daily');
function wpqa_scheduled_mails_daily() {
	$question_schedules = wpqa_options("question_schedules");
	$question_schedules_time = wpqa_options("question_schedules_time");
	if ($question_schedules == "on" && is_array($question_schedules_time) && in_array("daily",$question_schedules_time)) {
		wpqa_schedule_mails("daily","question");
	}
}
/* Weekly mails for questions */
add_action('wpqa_scheduled_mails_weekly','wpqa_scheduled_mails_weekly');
function wpqa_scheduled_mails_weekly() {
	$question_schedules = wpqa_options("question_schedules");
	$question_schedules_time = wpqa_options("question_schedules_time");
	if ($question_schedules == "on" && is_array($question_schedules_time) && in_array("weekly",$question_schedules_time)) {
		wpqa_schedule_mails("weekly","question");
	}
}
/* Monthly mails for questions */
add_action('wpqa_scheduled_mails_monthly','wpqa_scheduled_mails_monthly');
function wpqa_scheduled_mails_monthly() {
	$question_schedules = wpqa_options("question_schedules");
	$question_schedules_time = wpqa_options("question_schedules_time");
	if ($question_schedules == "on" && is_array($question_schedules_time) && in_array("monthly",$question_schedules_time)) {
		wpqa_schedule_mails("monthly","question");
	}
}
/* Daily mails for posts */
add_action('wpqa_scheduled_mails_daily_post','wpqa_scheduled_mails_daily_post');
function wpqa_scheduled_mails_daily_post() {
	$post_schedules = wpqa_options("post_schedules");
	$post_schedules_time = wpqa_options("post_schedules_time");
	if ($post_schedules == "on" && is_array($post_schedules_time) && in_array("daily",$post_schedules_time)) {
		wpqa_schedule_mails("daily","post");
	}
}
/* Weekly mails for posts */
add_action('wpqa_scheduled_mails_weekly_post','wpqa_scheduled_mails_weekly_post');
function wpqa_scheduled_mails_weekly_post() {
	$post_schedules = wpqa_options("post_schedules");
	$post_schedules_time = wpqa_options("post_schedules_time");
	if ($post_schedules == "on" && is_array($post_schedules_time) && in_array("weekly",$post_schedules_time)) {
		wpqa_schedule_mails("weekly","post");
	}
}
/* Monthly mails for posts */
add_action('wpqa_scheduled_mails_monthly_post','wpqa_scheduled_mails_monthly_post');
function wpqa_scheduled_mails_monthly_post() {
	$post_schedules = wpqa_options("post_schedules");
	$post_schedules_time = wpqa_options("post_schedules_time");
	if ($post_schedules == "on" && is_array($post_schedules_time) && in_array("monthly",$post_schedules_time)) {
		wpqa_schedule_mails("monthly","post");
	}
}
/* Notifications and mails */
add_action('wpqa_scheduled_notification_mails_daily_question','wpqa_scheduled_notification_mails');
add_action('wpqa_scheduled_notification_mails_twicedaily_question','wpqa_scheduled_notification_mails');
add_action('wpqa_scheduled_notification_mails_daily_post','wpqa_scheduled_notification_mails');
add_action('wpqa_scheduled_notification_mails_twicedaily_post','wpqa_scheduled_notification_mails');
add_action('wpqa_scheduled_notification_mails_daily_answer','wpqa_scheduled_notification_mails');
add_action('wpqa_scheduled_notification_mails_twicedaily_answer','wpqa_scheduled_notification_mails');
add_action('wpqa_scheduled_notification_mails_hourly','wpqa_scheduled_notification_mails');
add_action('wpqa_scheduled_notification_mails_twicehourly','wpqa_scheduled_notification_mails');
function wpqa_scheduled_notification_mails() {
	$way_sending_notifications_questions = wpqa_options("way_sending_notifications_questions");
	$schedules_time_notification_questions = wpqa_options("schedules_time_notification_questions");
	if ($way_sending_notifications_questions == "cronjob" && $schedules_time_notification_questions != "") {
		$send_email_and_notification_question = wpqa_options("send_email_and_notification_question");
		$send_email_new_question_value = "send_email_new_question";
		$send_email_question_groups_value = "send_email_question_groups";
		$send_notification_new_question_value = "send_notification_new_question";
		$send_notification_question_groups_value = "send_notification_question_groups";

		if ($send_email_and_notification_question == "both") {
			$send_email_new_question_value = $send_notification_new_question_value = "send_email_new_question_both";
			$send_email_question_groups_value = $send_notification_question_groups_value = "send_email_question_groups_both";
		}
		$send_email_new_question = wpqa_options($send_email_new_question_value);
		$send_email_question_groups = wpqa_options($send_email_question_groups_value);
		$send_notification_new_question = wpqa_options($send_notification_new_question_value);
		$send_notification_question_groups = wpqa_options($send_notification_question_groups_value);
		if ($send_email_new_question == "on" && is_array($send_email_question_groups)) {
			wpqa_schedule_notification_mails_posts($schedules_time_notification_questions,"question",$send_email_question_groups_value,($send_notification_new_question == "on" && is_array($send_notification_question_groups)?$send_notification_question_groups_value:""));
		}
	}
	$way_sending_notifications_posts = wpqa_options("way_sending_notifications_posts");
	$schedules_time_notification_posts = wpqa_options("schedules_time_notification_posts");
	if ($way_sending_notifications_posts == "cronjob" && $schedules_time_notification_posts != "") {
		$send_email_and_notification_post = wpqa_options("send_email_and_notification_post");
		$send_email_new_post_value = "send_email_new_post";
		$send_email_post_groups_value = "send_email_post_groups";
		$send_notification_new_post_value = "send_notification_new_post";
		$send_notification_post_groups_value = "send_notification_post_groups";
		if ($send_email_and_notification_post == "both") {
			$send_email_new_post_value = $send_notification_new_post_value = "send_email_new_post_both";
			$send_email_post_groups_value = $send_notification_post_groups_value = "send_email_post_groups_both";
		}
		$send_email_new_post = wpqa_options($send_email_new_post_value);
		$send_email_post_groups = wpqa_options($send_email_post_groups_value);
		$send_notification_new_post = wpqa_options($send_notification_new_post_value);
		$send_notification_post_groups = wpqa_options($send_notification_post_groups_value);
		if ($send_email_new_post == "on" && is_array($send_email_post_groups)) {
			wpqa_schedule_notification_mails_posts($schedules_time_notification_posts,"post",$send_email_post_groups_value,($send_notification_new_post == "on" && is_array($send_notification_post_groups)?$send_notification_post_groups_value:""));
		}
	}
	$way_sending_notifications_answers = wpqa_options("way_sending_notifications_answers");
	$schedules_time_notification_answers = wpqa_options("schedules_time_notification_answers");
	if ($way_sending_notifications_answers == "cronjob" && $schedules_time_notification_answers != "") {
		wpqa_schedule_notification_mails_answers($schedules_time_notification_answers);
	}
}
/* Schedule mail and notifications */
function wpqa_schedule_notification_mails_posts($schedule,$type = "question",$schedules_groups = "",$notifications_groups = "") {
	$way_sending_notifications = wpqa_options("way_sending_notifications_".$type."s");
	if ($way_sending_notifications == "cronjob") {
		$schedules_number = wpqa_options("schedules_number_".$type);
		wpqa_schedule_mails($schedule,$type,$schedules_number,true,$schedules_groups);
		if ($notifications_groups != "") {
			if ($schedule == "daily") {
				$specific_date = "24 hours ago";
			}else if ($schedule == "weekly") {
				$specific_date = "1 week ago";
			}else if ($schedule == "monthly") {
				$specific_date = "1 month ago";
			}else if ($schedule == "twicedaily") {
				$specific_date = "12 hours ago";
			}else if ($schedule == "hourly") {
				$specific_date = "1 hours ago";
			}else if ($schedule == "twicehourly") {
				$specific_date = "30 minutes ago";
			}
			if (isset($specific_date)) {
				$ask_question_to_users = wpqa_options("ask_question_to_users");
				$ask_user_meta_query = array(
					"meta_query" => array(
						array("key" => "wpqa_post_scheduled_notification","value" => "yes"),
					)
				);
				if ($ask_question_to_users == "on") {
					$ask_user_meta_query = array(
						"meta_query" => array(
							'relation' => 'or',
							array(
								'relation' => 'and',
								array("key" => "user_id","compare" => "NOT EXISTS"),
								array("key" => "wpqa_post_scheduled_notification","value" => "yes")
							),
							array(
								'relation' => 'and',
								array("key" => "user_id","compare" => "EXISTS"),
								array("key" => "count_post_all","value" => "1","compare" => ">="),
								array("key" => "wpqa_post_scheduled_notification","value" => "yes")
							)
						)
					);
				}
				$recent_posts_query = new WP_Query(array_merge($ask_user_meta_query,array('date_query' => array(array('after' => $specific_date,'inclusive' => true)),'post_type' => ($type == wpqa_questions_type?array($type,wpqa_asked_questions_type):$type),'ignore_sticky_posts' => 1,'cache_results' => false,'no_found_rows' => true,'posts_per_page' => 2)));
				if ($recent_posts_query->have_posts() ) {
					while ($recent_posts_query->have_posts()) {
						$recent_posts_query->the_post();
						$_post = $recent_posts_query->post;
						delete_post_meta($_post->ID,"wpqa_post_scheduled_notification");
					}
				}
				$count_total = (int)$recent_posts_query->post_count;
				if ($count_total > 0) {
					if ($count_total == 1) {
						$post = (isset($recent_posts_query->posts) && isset($recent_posts_query->posts["0"])?$recent_posts_query->posts["0"]:"");
						$post_id = (isset($post->ID)?$post->ID:"");
						$post_author = (isset($post->post_author)?$post->post_author:"");
						$the_author = 0;
						if ($post_author == 0) {
							$the_author = get_post_meta($post_id,'question_username',true);
						}
						$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
						$not_user = ($post_author > 0?$post_author:0);
					}
					$send_notification_groups = wpqa_options($notifications_groups);
					$users = get_users(array("role__not_in" => array("wpqa_under_review","activation","ban_group"),"role__in" => (isset($send_notification_groups) && is_array($send_notification_groups)?$send_notification_groups:array()),"fields" => array("ID")));
					if (isset($users) && is_array($users) && !empty($users)) {
						foreach ($users as $key => $value) {
							$user_id = $value->ID;
							if ($count_total > 1 || ($count_total == 1 && isset($not_user) && $user_id > 0 && $user_id != $not_user)) {
								wpqa_notifications_activities($user_id,($count_total > 1?"":(isset($not_user)?$not_user:"")),($count_total > 1?"":(isset($the_author)?$the_author:"")),($count_total > 1?"":(isset($post_id)?$post_id:"")),"","cronjob_".$type.($count_total > 1?"s":""),"notifications","",$type);
							}
						}
					}
				}
				wp_reset_postdata();
			}
		}
	}
}
/* Schedule mail and notifications for answers */
function wpqa_schedule_notification_mails_answers($schedule) {
	$way_sending_notifications = wpqa_options("way_sending_notifications_answers");
	if ($way_sending_notifications == "cronjob") {
		$schedules_number_answer = wpqa_options("schedules_number_answer");
	}
}?>