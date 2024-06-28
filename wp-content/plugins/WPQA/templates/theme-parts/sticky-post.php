<?php if (isset($all_sticky_posts) && is_array($all_sticky_posts) && !empty($all_sticky_posts) && $paged == 1) {
	foreach ($all_sticky_posts as $value) {
		$value = (int)$value;
		if ($value > 0) {
			$get_the_permalink = get_the_permalink($value);
			if ($get_the_permalink != "") {
				$posts_sticky[] = $value;
			}
		}
	}
	if (isset($posts_sticky)) {
		$uniqueValues = array_unique($posts_sticky);
		$posts_sticky = array_intersect_key($posts_sticky, $uniqueValues);
		update_option('sticky_posts',$posts_sticky);
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
	$posts_sticky_isset = (isset($posts_sticky)?$posts_sticky:array());
	$args = array_merge($custom_args,$author__not_in,array("nopaging" => true,"post_type" => "post","post__in" => $posts_sticky_isset,"meta_query" => $query_sticky_meta));
	
	if (isset($posts_sticky_isset) && is_array($posts_sticky_isset) && !empty($posts_sticky_isset)) {
		query_posts($args);
		$k_ad_p = isset($GLOBALS['k_ad_p'])?$GLOBALS['k_ad_p']:-1;
		$zero_index = isset($GLOBALS['zero_index'])?$GLOBALS['zero_index']:0;
		$post_count = isset($GLOBALS['post_count'])?$GLOBALS['post_count']:0;
		if (have_posts() ) :
			$is_posts_sticky = true;
			while (have_posts() ) : the_post();
				$k_ad_p++;
				include locate_template("theme-parts/content.php");
			endwhile;
		endif;
		wp_reset_query();
	}
}

if (isset($sticky_only) && $sticky_only == true && isset($show_custom_error) && $show_custom_error == true && (!isset($is_posts_sticky))) {
	include wpqa_get_template("content-none.php","theme-parts/");
}?>