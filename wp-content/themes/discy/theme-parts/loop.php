<?php if (has_wpqa() && wpqa_plugin_version >= "5.9.8") {
	include wpqa_get_template("loop.php","theme-parts/");
}else {
	$k_ad_p = -1;
	$zero_index = $post_count = 0;
	include locate_template("includes/loop-setting.php");
	include locate_template("theme-parts/content.php");
	wp_reset_postdata();?>
	<div class="pagination-wrap pagination-post no-pagination-wrap">
		<div class="main-pagination">
			<div class="pagination">
				<?php echo paginate_links();?>
			</div>
		</div>
	</div>
<?php }?>