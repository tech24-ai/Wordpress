<?php

/* @author    2codeThemes
*  @package   WPQA/templates/profile
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$wpqa_sidebar = wpqa_sidebars("sidebar_where");
$last_one = (isset($last_one) && $last_one != ""?$last_one:"");
if (wpqa_is_user_blocking() || $last_one == "blocking") {
	$user_style_pages = wpqa_options("user_style_pages");
	$masonry_user_style = wpqa_options("masonry_user_style");
	$get_users = get_user_meta($wpqa_user_id,"wpqa_block_users",true);
	$blocking_class = "blocking";
	if (isset($get_users) && is_array($get_users) && !empty($get_users)) {
		include wpqa_get_template("users.php","profile/");
	}else {
		echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("You don't have any blocking users yet.","wpqa").'</p></div>';
	}
}?>