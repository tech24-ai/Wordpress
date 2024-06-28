<?php get_header();
	include locate_template("includes/header-part.php");
	$its_post_type = wpqa_questions_type;
	$paged         = discy_paged();
	$active_sticky = true;
	$custom_args   = array("post_type" => wpqa_questions_type);
	$show_sticky   = true;
	include locate_template("theme-parts/loop.php");
	include locate_template("includes/footer-part.php");
get_footer();?>