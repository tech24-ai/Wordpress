<?php get_header();
	include locate_template("includes/header-part.php");
	$breadcrumbs = discy_options('breadcrumbs');
	$tag_des     = discy_options('question_tag_description');
	$tag_rss     = discy_options("question_tag_rss");
	$tag_desc    = tag_description();
	$tax_id      = get_term_by('slug',get_query_var('term'),esc_html(get_query_var('taxonomy')));
	$category_id = $tax_id->term_id;
	if ($tag_des == "on" && !empty($tag_desc)) {?>
		<div class="question-category post-section category-description">
			<h4><?php echo esc_html__("Tag","discy").": ".esc_html(single_tag_title("", false));?></h4>
			<?php if ($tag_rss == "on") {?>
				<a class="category-rss-i tooltip-n" title="<?php esc_attr_e("Tag feed","discy")?>" href="<?php echo esc_url(get_tag_feed_link(esc_attr(get_query_var('tag_id'))))?>"><i class="icon-rss"></i></a>
			<?php }
			echo ($tag_desc);?>
		</div><!-- End post -->
	<?php }
	if ($breadcrumbs != "on") {
		echo "<div class='follow-tag'>".wpqa_follow_cat_button($category_id,get_current_user_id(),'tag')."</div>";
	}
	$its_post_type = wpqa_questions_type;
	$paged         = discy_paged();
	$active_sticky = true;
	$custom_args   = array('tax_query' => array(array('taxonomy' => wpqa_question_tags,'field' => 'id','terms' => $category_id)));
	$tabs_tag      = discy_options("tabs_tag");
	if ($tabs_tag == "on") {
		$exclude_tags = discy_options("exclude_tags");
		$exclude_tags = ($exclude_tags != ""?explode(",",$exclude_tags):array());
		$tab_tag = (is_array($exclude_tags) && !in_array($category_id,$exclude_tags)?true:false);
	}
	if (isset($tab_tag) && $tab_tag == true && has_wpqa() && wpqa_plugin_version >= "5.9.8") {
		include wpqa_get_template("tabs.php","theme-parts/");
	}else {
		$active_sticky = $show_sticky = true;
		include locate_template("theme-parts/loop.php");
	}
	include locate_template("includes/footer-part.php");
get_footer();?>