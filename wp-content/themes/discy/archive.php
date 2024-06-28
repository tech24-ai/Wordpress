<?php get_header();
	include locate_template("includes/header-part.php");
	do_action("discy_before_archive_action");
	include locate_template("theme-parts/loop.php");
	do_action("discy_after_archive_action");
	include locate_template("includes/footer-part.php");
get_footer();?>