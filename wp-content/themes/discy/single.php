<?php get_header();
	include locate_template("includes/header-part.php");
	if (has_wpqa() && wpqa_plugin_version >= "5.9.8") {
		include wpqa_get_template("logged-only.php","theme-parts/");
	}
	$page_id = $post_id_main = $post->ID;
	$theme_sidebar_all = $theme_sidebar = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebars("sidebar_where"):"");
	$remove_question_slug = discy_options("remove_question_slug");
	$remove_asked_question_slug = discy_options("remove_asked_question_slug");
	if (($remove_question_slug == "on" || $remove_asked_question_slug == "on") && is_singular("post")) {
		$array_data = array("p" => $page_id);
		$discy_query = new WP_Query($array_data);
	}
	$its_post_type = $post->post_type;
	if (has_wpqa() && wpqa_plugin_version >= "5.9.8") {
		$loop_page = "loop-setting.php";
		if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) {
			$loop_page = "question-setting.php";
		}else if (is_singular(wpqa_knowledgebase_type)) {
			$loop_page = "knowledgebase-setting.php";
		}
		include wpqa_get_template($loop_page,"includes/");
	}else {
		include locate_template("includes/loop-setting.php");
	}
	if ( (($remove_question_slug == "on" || $remove_asked_question_slug == "on") && isset($discy_query) && $discy_query->have_posts()) || have_posts() ) :?>
		<div class="post-articles<?php echo (has_wpqa() && (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type))?" question-articles".(isset($question_columns) && $question_columns == "style_2" && isset($masonry_style) && $masonry_style == "on"?" isotope":""):"")?>">
			<?php if (($remove_question_slug == "on" || $remove_asked_question_slug == "on") && isset($discy_query) && $discy_query->have_posts()) :
				while ($discy_query->have_posts()) : $discy_query->the_post();
					do_action("discy_action_before_post_content",$post->ID,$post->post_author);
					do_action("wpqa_action_before_post_content",$post->ID,$post->post_author);
					include locate_template("theme-parts/content.php");
					do_action("discy_action_after_post_content",$post->ID,$post->post_author);
					do_action("wpqa_action_after_post_content",$post->ID,$post->post_author);
				endwhile;
			else :
				while ( have_posts() ) : the_post();
					do_action("discy_action_before_post_content",$post->ID,$post->post_author);
					do_action("wpqa_action_before_post_content",$post->ID,$post->post_author);
					$has_have_posts = true;
					include locate_template("theme-parts/loop.php");
					do_action("discy_action_after_post_content",$post->ID,$post->post_author);
					do_action("wpqa_action_after_post_content",$post->ID,$post->post_author);
				endwhile;
			endif;?>
		</div><!-- End post-articles -->
	<?php elseif (has_wpqa() && wpqa_plugin_version >= "5.9.8") :
		include wpqa_get_template("content-none.php","theme-parts/");
	endif;
	if (($remove_question_slug == "on" || $remove_asked_question_slug == "on") && isset($discy_query)) {
		wp_reset_postdata();
	}
	include locate_template("includes/footer-part.php");
get_footer();?>