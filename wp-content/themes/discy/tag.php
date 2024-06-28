<?php get_header();
	include locate_template("includes/header-part.php");
	$tag_des     = discy_options('tag_description');
	$tag_rss     = discy_options("tag_rss");
	$tag_desc    = tag_description();
	$category_id = (int)get_query_var('wpqa_term_id');
	if ($tag_des == "on" && !empty($tag_desc)) {?>
		<div class="post-section category-description">
			<h4><?php echo esc_html__("Tag","discy").": ".esc_html(single_tag_title("", false));?></h4>
			<?php if ($tag_rss == "on") {?>
				<a class="category-rss-i tooltip-n" title="<?php esc_attr_e("Tag feed","discy")?>" href="<?php echo esc_url(get_tag_feed_link(esc_attr(get_query_var('tag_id'))))?>"><i class="icon-rss"></i></a>
			<?php }
			echo ($tag_desc);?>
		</div><!-- End post -->
	<?php }
	include locate_template("theme-parts/loop.php");
	include locate_template("includes/footer-part.php");
get_footer();?>