<?php get_header();
	include locate_template("includes/header-part.php");
	if (isset($_POST) && !empty($_POST)) {
		$show_on_front = get_option("show_on_front");
		if ($show_on_front == "page") {
			$page_on_front = get_option("page_on_front");
			if (is_numeric($page_on_front)) {
				$wp_page_template = discy_post_meta("_wp_page_template",$page_on_front,false);
				$page_id = $post_id_main = $page_on_front;
				if ($wp_page_template == "template-home.php") {
					$is_home_template = true;
				}
			}
		}
	}
	
	if (isset($is_home_template) && has_wpqa() && wpqa_plugin_version >= "5.9.8") {
		include wpqa_get_template("tabs.php","theme-parts/");
	}else {
		include locate_template("theme-parts/loop.php");
	}
	include locate_template("includes/footer-part.php");
	include locate_template("includes/footer-part.php");
get_footer();?>