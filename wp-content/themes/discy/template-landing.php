<?php /* Template Name: Landing */
get_header();
	include locate_template("includes/header-part.php");
	$page_id = $post_id_main = $post->ID;
	$wp_page_template = discy_post_meta("_wp_page_template",$post_id_main,false);
	include locate_template("includes/footer-part.php");
get_footer();?>