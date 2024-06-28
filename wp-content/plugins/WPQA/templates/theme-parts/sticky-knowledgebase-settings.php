<?php if (isset($sticky_knowledgebases) && is_array($sticky_knowledgebases) && !empty($sticky_knowledgebases) && $paged == 1) {
	foreach ($sticky_knowledgebases as $value) {
		$value = (int)$value;
		if ($value > 0) {
			$get_the_permalink = get_the_permalink($value);
			if ($get_the_permalink != "") {
				$knowledgebases_sticky[] = $value;
			}
		}
	}
	if (isset($knowledgebases_sticky)) {
		$uniqueValues = array_unique($knowledgebases_sticky);
		$knowledgebases_sticky = array_intersect_key($knowledgebases_sticky, $uniqueValues);
		update_option('sticky_knowledgebases',$knowledgebases_sticky);
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
	$knowledgebases_sticky_isset = (isset($knowledgebases_sticky)?$knowledgebases_sticky:array());
	
	$sticky_args = array_merge($custom_args,$author__not_in,array("nopaging" => true,"post_type" => wpqa_knowledgebase_type,"post__in" => $knowledgebases_sticky_isset,"meta_query" => $query_sticky_meta));
	
	if (isset($knowledgebases_sticky_isset) && is_array($knowledgebases_sticky_isset) && !empty($knowledgebases_sticky_isset)) {
		query_posts($sticky_args);
		$k_ad_p = isset($GLOBALS['k_ad_p'])?$GLOBALS['k_ad_p']:-1;
		$zero_index = isset($GLOBALS['zero_index'])?$GLOBALS['zero_index']:0;
		$post_count = isset($GLOBALS['post_count'])?$GLOBALS['post_count']:0;
		if (have_posts() ) :
			$is_knowledgebases_sticky = true;
		endif;
	}
}

if (isset($sticky_only) && $sticky_only == true && isset($show_custom_error) && $show_custom_error == true && (!isset($is_knowledgebases_sticky))) {
	include wpqa_get_template("content-none.php","theme-parts/");
}?>