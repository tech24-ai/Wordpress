<?php $site_skin = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_dark_skin():"");
$site_users_only = (has_wpqa() && wpqa_plugin_version >= "5.9.7" && !wpqa_is_bot()?wpqa_site_users_only():"");
$under_construction = (has_wpqa()?wpqa_under_construction():"");
$wp_page_template = discy_post_meta("_wp_page_template","",false);
$theme_data = wp_get_theme();
$theme_version = !empty($theme_data['Version'])?' '.$theme_data['Version']:'';?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php echo esc_attr($site_skin)."-skin ".($site_users_only == "yes" || $under_construction == "on" || $wp_page_template == "template-landing.php"?"site-html-login ":"")?>no-svg">
<head>
	<meta charset="<?php bloginfo('charset');?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo('pingback_url');?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="generator" content="<?php echo esc_attr($theme_data.$theme_version)?>">
	<?php wp_head();?>
</head>
<body <?php body_class();?>>
	<?php wp_body_open();?>