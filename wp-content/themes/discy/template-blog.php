<?php /* Template Name: Blog */
get_header();
	$page_id = $post_id_main = $post->ID;
	include locate_template("includes/templates.php");
get_footer();?>