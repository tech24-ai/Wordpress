<?php if (is_page()) {
	$tag_sort  = discy_post_meta("tag_sort");
	$tag_style = discy_post_meta("tag_style");
	$tag_order = discy_post_meta("tag_order");
	$tags_tax  = discy_post_meta("tags_tax");
	$number    = discy_post_meta("tags_per_page");
}else {
	$tag_style = discy_options("tag_style_pages");
	$tag_sort  = (isset($_GET["tag_filter"]) && $_GET["tag_filter"] != ""?esc_html($_GET["tag_filter"]):"count");
	$tag_order = "DESC";
	$tags_tax  = (has_wpqa() && wpqa_is_search()?wpqa_search_type():"");
}

if ($tags_tax == 'post' || $tags_tax == 'post_tag') {
	$tag_type = 'post_tag';
	$post_type_tags = 'post';
	if ($tag_style == "simple_follow") {
		$tag_style = "simple";
	}
}else {
	$tag_type = wpqa_question_tags;
	$post_type_tags = wpqa_questions_type;
}

$tag_sort = (isset($_GET["tag_filter"]) && $_GET["tag_filter"] != ""?esc_html($_GET["tag_filter"]):(isset($tag_sort) && $tag_sort != ""?$tag_sort:"count"));

$theme_sidebar = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebars("sidebar_where"):"");
$follow_category = discy_options("follow_category");

$search_value = (has_wpqa()?wpqa_search():"");
if ($search_value != "") {
	$search_args = array('search' => $search_value);
}else {
	$search_args = array();
}

$number = (isset($number) && $number > 0?$number:apply_filters('discy_tags_per_page',4*get_option('posts_per_page',10)));
$paged  = discy_paged();
$offset     = ($paged-1)*$number;
$tag_order  = (isset($_GET["tag_filter"]) && $_GET["tag_filter"] == "name"?"ASC":$tag_order);
$tag_sort   = ($tag_sort == "followers"?"meta_value_num":$tag_sort);
$meta_query = ($tag_sort == "meta_value_num"?array('meta_query' => array("relation" => "or",array("key" => "tag_follow_count","compare" => "NOT EXISTS"),array("key" => "tag_follow_count","value" => 0,"compare" => ">="))):array());
$tags       = get_terms($tag_type,array_merge($search_args,$meta_query,array('hide_empty' => 0)));
$terms      = get_terms($tag_type,array_merge($search_args,$meta_query,array(
	'orderby'    => $tag_sort,
	'order'      => $tag_order,
	'number'     => $number,
	'offset'     => $offset,
	'hide_empty' => 0
)));

$all_tag_pages = ceil(count($tags)/$number);
if (!empty($terms) && !is_wp_error($terms)) {
	echo '<div class="'.($follow_category == "on" && $tag_style == "simple_follow" && ($tags_tax == wpqa_question_tags || $tags_tax == wpqa_questions_type)?'row row-warp cats-sections tags-sections':'tagcloud '.($tag_style == "advanced"?"row row-warp":"tagcloud-simple")).'">';
		foreach ($terms as $term) {
			include locate_template("theme-parts/show-tags.php");
		}
	echo '</div>';
	if ($all_tag_pages > 1) {
		$pagination_args = array(
			'current'   => max(1, $paged),
			'total'     => $all_tag_pages,
			'prev_text' => '<i class="icon-left-open"></i>',
			'next_text' => '<i class="icon-right-open"></i>',
		);
		if (!get_option('permalink_structure')) {
			$pagination_args['base'] = esc_url_raw(add_query_arg('paged','%#%'));
		}
		if (has_wpqa() && wpqa_is_search()) {
			$pagination_args['format'] = '?page=%#%';
		}
		echo '<div class="main-pagination"><div class="pagination">'.paginate_links($pagination_args).'</div></div>';
	}
}else {
	$no_tags = true;
}

if ($search_value != "" && isset($no_tags) && $no_tags == true && has_wpqa() && wpqa_plugin_version >= "5.9.8") {
	include wpqa_get_template("search-none.php","theme-parts/");
}?>