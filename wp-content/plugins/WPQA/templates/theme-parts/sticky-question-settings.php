<?php if (isset($sticky_questions) && is_array($sticky_questions) && !empty($sticky_questions) && $paged == 1) {
	foreach ($sticky_questions as $value) {
		$value = (int)$value;
		if ($value > 0) {
			$get_the_permalink = get_the_permalink($value);
			if ($get_the_permalink != "") {
				$questions_sticky[] = $value;
			}
		}
	}
	if (isset($questions_sticky)) {
		$uniqueValues = array_unique($questions_sticky);
		$questions_sticky = array_intersect_key($questions_sticky, $uniqueValues);
		update_option('sticky_questions',$questions_sticky);
	}
	if (isset($custom_args) && is_array($custom_args) && !empty($custom_args)) {
		$custom_args = $custom_args;
	}else {
		$custom_args = array();
	}

	$block_users = wpqa_options("block_users");
	$author__not_in = array();
	if ($block_users == "on") {
		$user_id = get_current_user_id();
		if ($user_id > 0) {
			$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
			if (is_array($get_block_users) && !empty($get_block_users)) {
				$author__not_in = array("author__not_in" => $get_block_users);
			}
		}
	}
	
	$query_sticky_meta = array("key" => "sticky","compare" => "=","value" => 1);
	$custom_show_sticky = apply_filters(wpqa_prefix_theme.'_show_sticky',false);
	$questions_sticky_isset = (isset($questions_sticky)?$questions_sticky:array());
	
	if ((isset($wp_page_template) && $wp_page_template == "template-question.php" && isset($orderby_post) && $orderby_post == "polls") || (isset($_GET["type"]) && $_GET["type"] == "poll") || $custom_show_sticky == true || (isset($first_one) && ($first_one == "polls" || $first_one == "polls-time" || $first_one == "poll-feed" || $first_one == "poll-feed-time")) || (isset($_GET["show"]) && ($_GET["show"] == "polls" || $_GET["show"] == "polls-time" || $_GET["show"] == "poll-feed" || $_GET["show"] == "poll-feed-time"))) {
		$sticky_args = array_merge($custom_args,$author__not_in,array("nopaging" => true,"post_type" => wpqa_questions_type,"post__in" => $questions_sticky_isset,"meta_query" => array(array('relation' => 'AND',array("key" => "question_poll","value" => "on","compare" => "LIKE"),$query_sticky_meta))));
		$custom_show_sticky = apply_filters(wpqa_prefix_theme.'_sticky_args',$sticky_args,$custom_args,$questions_sticky_isset,$query_sticky_meta);
	}else {
		$sticky_args = array_merge($custom_args,$author__not_in,array("nopaging" => true,"post_type" => wpqa_questions_type,"post__in" => $questions_sticky_isset,"meta_query" => $query_sticky_meta));
	}
	
	if (isset($questions_sticky_isset) && is_array($questions_sticky_isset) && !empty($questions_sticky_isset)) {
		query_posts($sticky_args);
		$k_ad_p = isset($GLOBALS['k_ad_p'])?$GLOBALS['k_ad_p']:-1;
		$zero_index = isset($GLOBALS['zero_index'])?$GLOBALS['zero_index']:0;
		$post_count = isset($GLOBALS['post_count'])?$GLOBALS['post_count']:0;
		if (have_posts() ) :
			$is_questions_sticky = true;
		endif;
	}
}

if (isset($sticky_only) && $sticky_only == true && isset($show_custom_error) && $show_custom_error == true && (!isset($is_questions_sticky))) {
	include wpqa_get_template("content-none.php","theme-parts/");
}?>