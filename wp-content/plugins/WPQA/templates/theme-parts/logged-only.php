<?php $logged_only = wpqa_post_meta("logged_only");
if ($logged_only == "on" && !is_user_logged_in()) {
	echo wpqa_login_message(esc_html__("Sorry, log in to see the content.","wpqa"),"warning","block","register_2");
	get_footer();
	die();
}?>