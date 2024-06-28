<?php /* Header part */
$site_users_only    = (has_wpqa() && wpqa_plugin_version >= "5.9.7" && !wpqa_is_bot()?wpqa_site_users_only():"");
$under_construction = (has_wpqa()?wpqa_under_construction():"");
$wp_page_template   = discy_post_meta("_wp_page_template","",false);
$logo_display       = discy_options("logo_display");
$logo_img           = discy_image_url_id(discy_options("logo_img"));
$retina_logo        = discy_image_url_id(discy_options("retina_logo"));
$logo_height        = discy_options("logo_height");
$logo_width         = discy_options("logo_width");
$skin_switcher      = discy_options("skin_switcher");
if ($skin_switcher == "on") {
	$custom_dark_logo = discy_options("custom_dark_logo");
	if ($custom_dark_logo == "on") {
		$dark_logo_img = discy_image_url_id(discy_options("dark_logo_img"));
		$dark_retina_logo = discy_image_url_id(discy_options("dark_retina_logo"));
	}
}
if ($site_users_only == "yes" || $under_construction == "on" || $wp_page_template == "template-landing.php") {
	include locate_template("includes/login-page.php");
	get_footer();
	die();
}else {
	include locate_template("includes/header-code.php");
}?>