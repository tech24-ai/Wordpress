<?php $comments_open = apply_filters("discy_comments_open",true);
if ($comments_open == true && comments_open()) {
	$can_add_answer = apply_filters("wpqa_can_add_answer",true,$user_id,$custom_permission,(isset($roles)?$roles:array()),$post);
	if ($can_add_answer == true) {
		$answer_per_question = discy_options("answer_per_question");
		$allow_to_do = (has_wpqa() && wpqa_plugin_version >= "6.1.1"?wpqa_allow_to_answer_reply($post->ID,$post_type,$user_id):array("allow_to_answer" => true,"allow_to_reply" => true));
		$allow_to_answer = $allow_to_do["allow_to_answer"];
		$allow_to_reply = $allow_to_do["allow_to_reply"];
		if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
			if ($answer_per_question != "on" || ($allow_to_answer == true && $allow_to_reply != true) || ($allow_to_answer == true && $allow_to_reply == true)) {
				$allow_answer = true;
			}
		}
		echo '<div id="respond-all" class="'.($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?(isset($allow_answer)?'':'discy_hide'):'').(isset($edit_delete)?' respond-edit-delete discy_hide':'').'">';
			$comment_editor = discy_options(($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?'answer_editor':'comment_editor'));
			$fields =  array(
				'author' => '<div class="form-input"><input type="text" name="author" value="" id="comment_name" aria-required="true" placeholder="'.esc_attr__('Your Name',"discy").'">'.($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?'<i class="icon-user"></i>':'').'</div>',
				'email'  => '<div class="form-input form-input-last"><input type="email" name="email" value="" id="comment_email" aria-required="true" placeholder="'.esc_attr__('Email',"discy").'">'.($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?'<i class="icon-mail"></i>':'').'</div>',
				'url'    => '<div class="form-input form-input-full"><input type="url" name="url" value="" id="comment_url" placeholder="'.esc_attr__('URL',"discy").'">'.($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?'<i class="icon-link"></i>':'').'</div>',
			);

			if (isset($comment_editor) && $comment_editor == "on") {
				$settings = array("textarea_name" => "comment","media_buttons" => true,"textarea_rows" => 10);
				$settings = apply_filters('wpqa_comment_editor_setting',$settings);
				ob_start();
				$rand = rand(1,1000);
				wp_editor("","comment-".$rand,$settings);
				$comment_contents = ob_get_clean();
			}else {
				$comment_contents = '<textarea id="comment" name="comment" aria-required="true" placeholder="'.apply_filters("discy_filter_textarea_comment".($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?"_question":""),($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?esc_html_x("Answer","Answer textarea","discy"):esc_html_x("Comment","Comment textarea","discy"))).'"></textarea>'.($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?'<i class="icon-pencil"></i>':'');
			}

			$activate_register = discy_options("activate_register");
			$activate_login = discy_options("activate_login");

			$comment_form_args = array(
				'must_log_in'          => ($activate_login != 'disabled'?'<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?esc_html__("You must login to add an answer.","discy"):esc_html__("You must login to add a new comment.","discy")).'</p></div>'.do_shortcode("[wpqa_login]".'<div class="pop-footer pop-footer-comments">'.(has_wpqa() && $activate_register != 'disabled'?wpqa_signup_in_popup():'').'</div>'):''),
				'logged_in_as'         =>  '<p class="comment-login">'.esc_html__('Logged in as',"discy").'<a class="comment-login-login" href="'.esc_url(has_wpqa()?wpqa_profile_url($user_id):"").'"><i class="icon-user"></i>'.esc_html($user_identity).'</a><a class="comment-login-logout" href="'.wp_logout_url(get_permalink()).'" title="'.esc_attr__("Log out of this account","discy").'"><i class="icon-logout"></i>'.esc_html__('Log out',"discy").'</a></p>',
				'title_reply'          => ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?esc_html__("Leave an answer","discy"):esc_html__("Leave a comment","discy")),
				'title_reply_to'       => ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?esc_html__("Leave an reply to %s","discy"):esc_html__("Leave a comment to %s","discy")),
				'title_reply_before'   => (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && !is_user_logged_in() && !get_option('comment_registration')?'<div class="button-default show-answer-form">'.esc_html__("Leave an answer","discy").'</div>':'').'<h3 class="section-title'.(($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && !is_user_logged_in()?' comment-form-hide':'').'">',
				'title_reply_after'    => '</h3>',
				'class_form'           => 'post-section comment-form'.(($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && !is_user_logged_in()?' comment-form-hide':'').($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?' answers-form':''),
				'comment_notes_after'  => '',
				'comment_notes_before' => '',
				'comment_field'        => '<div class="form-input form-textarea'.(isset($comment_editor) && $comment_editor == "on"?" form-comment-editor":" form-comment-normal").'">'.$comment_contents.'</div>',
				'fields'               => apply_filters('comment_form_default_fields',$fields),
				'label_submit'         => esc_html__("Submit","discy"),
				'class_submit'         => 'button-default button-hide-click'.($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?' button-default-question':''),
				'cancel_reply_before'  => '<div class="cancel-comment-reply">',
				'cancel_reply_after'   => '</div>',
				'format'               => 'html5'
			);
			comment_form(apply_filters("discy_filter_comment_form",$comment_form_args,$post_type),$post->ID);
		echo '</div>';
	}
}else {
	do_action("discy_action_if_comments_closed");
}?>