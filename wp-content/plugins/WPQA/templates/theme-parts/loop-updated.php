<?php include wpqa_get_template("sticky-question-settings.php","theme-parts/");
$paged   = wpqa_paged();
$offset  = ($paged -1) * $post_number;
$current = max(1,$paged);
if (is_tax(wpqa_question_categories) && isset($category_id) && $category_id > 0) {
	$post_display = "single_category";
	$all_tax_updated = $category_id;
}else if (is_tax(wpqa_question_tags) && isset($category_id) && $category_id > 0) {
	$post_display = "single_category";
	$all_tax_updated = $category_id;
}
$sticky_questions = apply_filters(wpqa_prefix_theme."_sticky_questions",get_option('sticky_questions'));
$sticky_questions_sql = (is_array($sticky_questions) && !empty($sticky_questions)?"AND $wpdb->posts.ID NOT IN (".implode(",",$sticky_questions).")":"");
$blocked_users = (isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)?"AND $wpdb->posts.post_author NOT IN (".implode(",",$get_block_users).")":"");
$specific_date = (isset($specific_date) && $specific_date != "" && $specific_date != "all"?$specific_date.' ago':"");
$date = ($specific_date != ""?"AND ($wpdb->posts.post_date > '".date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"). $specific_date))."')":"");
$post_display = (isset($display_r) && $display_r?$display_r:$post_display);
$custom_catagories_updated = (isset($custom_catagories_updated) && is_array($custom_catagories_updated) && !empty($custom_catagories_updated)?$custom_catagories_updated:(isset($custom_catagories_updated) && !is_array($custom_catagories_updated) && $custom_catagories_updated != ""?array($custom_catagories_updated):""));
$include_posts = (isset($post_display) && $post_display == "custom_posts"?"AND $wpdb->posts.ID IN (".$custom_posts_updated.")":"");
$custom_catagories_query = (isset($post_display) && ($post_display == "single_category" || $post_display == "categories") && is_array($custom_catagories_updated) && !empty($custom_catagories_updated)?" AND $wpdb->term_relationships.term_taxonomy_id IN (".implode(",",$custom_catagories_updated).")":(isset($post_display) && $post_display == "exclude_categories" && is_array($custom_catagories_updated) && !empty($custom_catagories_updated)?" NOT IN (".implode(",",$custom_catagories_updated).")":""));
$custom_catagories_updated = (isset($custom_catagories_updated) && $custom_catagories_updated != ""?"AND $wpdb->term_relationships.term_taxonomy_id".$custom_catagories_query:"");
$custom_catagories_updated = (isset($all_tax_updated) && $all_tax_updated != ""?"AND $wpdb->term_relationships.term_taxonomy_id IN (".$all_tax_updated.")":$custom_catagories_updated);
$post_type_feed = apply_filters(wpqa_prefix_theme."_post_type_feed",false,$wpdb);
$custom_catagories_where = ($custom_catagories_updated != "" || $post_type_feed != ""?"LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) ":"");
$feed_updated = "COALESCE((SELECT MAX(comment_date) FROM $wpdb->comments wpc WHERE wpc.comment_post_id = $wpdb->posts.id),$wpdb->posts.post_date)";
$custom_where = (isset($is_poll_feed) && $is_poll_feed == true?"AND ( mt2.meta_key = 'question_poll' AND mt2.meta_value = 'on')":"")."
AND ($wpdb->posts.post_type = '".wpqa_questions_type."' ".$post_type_feed.")
AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')";

$count_total = $total_query = $wpdb->get_var($wpdb->prepare(
	"SELECT COUNT(*)
	from (
		SELECT DISTINCT $wpdb->posts.ID
		FROM $wpdb->posts
		$custom_catagories_where
		".(isset($is_poll_feed) && $is_poll_feed == true?"LEFT JOIN $wpdb->postmeta AS mt2 ON ($wpdb->posts.ID = mt2.post_id )":"")."
		WHERE 1=%s
		$custom_catagories_updated $sticky_questions_sql $blocked_users $include_posts $date $custom_where
	) derived_count_table",1)
);

$query_sql = $wpdb->prepare(
	"SELECT DISTINCT $wpdb->posts.*
	FROM $wpdb->posts
	$custom_catagories_where
	".(isset($is_poll_feed) && $is_poll_feed == true?"LEFT JOIN $wpdb->postmeta AS mt2 ON ($wpdb->posts.ID = mt2.post_id )":"")."
	WHERE 1=%s
	$custom_catagories_updated $sticky_questions_sql $blocked_users $include_posts $date $custom_where
	ORDER BY $feed_updated $order_post
	LIMIT $post_number OFFSET $offset",
	1
);

$query = $wpdb->get_results($query_sql);
if (is_array($query) && !empty($query)) :?>
	<div class="post-articles<?php echo (isset($its_post_type) && (wpqa_questions_type == $its_post_type || wpqa_asked_questions_type == $its_post_type)?" question-articles".(isset($question_columns) && $question_columns == "style_2" && isset($count_total) && $count_total > 0?" row row-boot row-warp":""):"").($post_pagination == "none"?" no-pagination":"").(isset($blog_h) && $blog_h == "blog_h"?" post-articles-blog-h":"").(isset($post_style) && $post_style == "style_3" && isset($count_total) && $count_total > 0?" row row-boot row-warp":"").(isset($masonry_style) && $masonry_style == "on" && isset($count_total) && $count_total > 0?" isotope":"")?>">
		<?php include wpqa_get_template("sticky-question.php","theme-parts/");
		foreach ($query as $post) {
			setup_postdata($post->ID);
			$k_ad_p++;
			include wpqa_get_template("loop-action.php","theme-parts/");
		}?>
	</div>
<?php else :
	include wpqa_get_template("content-none.php","theme-parts/");
endif;
wpqa_load_pagination(array(
	"post_pagination" => (isset($post_pagination)?$post_pagination:"pagination"),
	"max_num_pages" => (isset($total_query)?ceil($total_query/$post_number):""),
	"it_answer_pagination" => (isset($it_answer_pagination)?$it_answer_pagination:false),
	"its_post_type" => (isset($its_post_type)?$its_post_type:false),
	"wpqa_query" => (isset($query_wp)?$query_wp:null),
));
if (is_array($query) && !empty($query)) {
	wp_reset_postdata();
}?>