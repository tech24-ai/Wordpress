<?php get_header();
	include locate_template("includes/header-part.php");
	if (has_wpqa() && wpqa_plugin_version >= "5.9.8") {
		include wpqa_get_template("content-none.php","theme-parts/");
	}
	include locate_template("includes/footer-part.php");
get_footer();?>