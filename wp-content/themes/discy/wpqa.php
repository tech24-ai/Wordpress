<?php get_header();
	include locate_template("includes/header-part.php");
	$theme_sidebar_all = $theme_sidebar = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebars("sidebar_where"):"");
	wpqa_content();
	include locate_template("includes/footer-part.php");
get_footer();?>