<?php $order_feed = "DESC";
$theme_sidebar = wpqa_sidebars("sidebar_where");
$get_current_user_id = get_current_user_id();
$follow_category = wpqa_options("follow_category");
if ($wp_page_template == "template-home.php") {
	$home_feed = wpqa_post_meta("home_feed",$post_id_main);
	$user_sort = wpqa_post_meta("user_sort_home_feed",$post_id_main);
	$user_style = wpqa_post_meta("user_style_home_feed",$post_id_main);
	$masonry_style = wpqa_post_meta("masonry_user_style_home_feed",$post_id_main);
	$users_per = wpqa_post_meta("users_per_home_feed",$post_id_main);
	$cat_sort = wpqa_post_meta("cat_sort_home_feed",$post_id_main);
	$cat_style = wpqa_post_meta("cat_style_home_feed",$post_id_main);
	$cat_per = wpqa_post_meta("cat_per_home_feed",$post_id_main);
	$tag_sort = wpqa_post_meta("tag_sort_home_feed",$post_id_main);
	$tag_per = wpqa_post_meta("tag_per_home_feed",$post_id_main);
	$number_of_users = wpqa_post_meta("users_home_feed",$post_id_main);
	$number_of_categories = wpqa_post_meta("categories_home_feed",$post_id_main);
	$number_of_tags = wpqa_post_meta("tags_home_feed",$post_id_main);
	$users_slider = wpqa_post_meta("users_slider_home_feed",$post_id_main);
	$cats_slider = wpqa_post_meta("cats_slider_home_feed",$post_id_main);
	$tags_slider = wpqa_post_meta("tags_slider_home_feed",$post_id_main);
	$users_more = wpqa_post_meta("users_more_home_feed",$post_id_main);
	$cats_more = wpqa_post_meta("cats_more_home_feed",$post_id_main);
	$tags_more = wpqa_post_meta("tags_more_home_feed",$post_id_main);
	$custom_link_users = wpqa_post_meta("custom_link_users_home_feed",$post_id_main);
	$custom_link_cats = wpqa_post_meta("custom_link_cats_home_feed",$post_id_main);
	$custom_link_tags = wpqa_post_meta("custom_link_tags_home_feed",$post_id_main);
}else {
	$home_feed = wpqa_post_meta("feed",$post_id_main);
	$user_sort = wpqa_post_meta("user_sort_feed",$post_id_main);
	$user_style = wpqa_post_meta("user_style_feed",$post_id_main);
	$masonry_style = wpqa_post_meta("masonry_user_style_feed",$post_id_main);
	$users_per = wpqa_post_meta("users_per_feed",$post_id_main);
	$cat_sort = wpqa_post_meta("cat_sort_feed",$post_id_main);
	$cat_style = wpqa_post_meta("cat_style_feed",$post_id_main);
	$cat_per = wpqa_post_meta("cat_per_feed",$post_id_main);
	$tag_sort = wpqa_post_meta("tag_sort_feed",$post_id_main);
	$tag_per = wpqa_post_meta("tag_per_feed",$post_id_main);
	$number_of_users = wpqa_post_meta("users_feed",$post_id_main);
	$number_of_categories = wpqa_post_meta("categories_feed",$post_id_main);
	$number_of_tags = wpqa_post_meta("tags_feed",$post_id_main);
	$users_slider = wpqa_post_meta("users_slider_feed",$post_id_main);
	$cats_slider = wpqa_post_meta("cats_slider_feed",$post_id_main);
	$tags_slider = wpqa_post_meta("tags_slider_feed",$post_id_main);
	$users_more = wpqa_post_meta("users_more_feed",$post_id_main);
	$cats_more = wpqa_post_meta("cats_more_feed",$post_id_main);
	$tags_more = wpqa_post_meta("tags_more_feed",$post_id_main);
	$custom_link_users = wpqa_post_meta("custom_link_users_feed",$post_id_main);
	$custom_link_cats = wpqa_post_meta("custom_link_cats_feed",$post_id_main);
	$custom_link_tags = wpqa_post_meta("custom_link_tags_feed",$post_id_main);
}
$cat_sort = ($cat_sort == "followers"?"meta_value_num":$cat_sort);
$tag_sort = ($tag_sort == "followers"?"meta_value_num":$tag_sort);
$show_custom_error = true;
$paged = wpqa_paged();
$offset = ($paged -1) * $post_number;
if (isset($specific_date)) {
	if ($specific_date == "24" || $specific_date == "48" || $specific_date == "72" || $specific_date == "96" || $specific_date == "120" || $specific_date == "144") {
		$specific_date = $specific_date." hours";
	}else if ($specific_date == "week" || $specific_date == "month" || $specific_date == "year") {
		$specific_date = "1 ".$specific_date;
	}
}
$specific_date_feed = (isset($specific_date) && $specific_date != "" && $specific_date != "all"?date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." -".$specific_date)):"");

$following_me = get_user_meta($user_id,"following_me",true);
if (is_array($following_me) && !empty($following_me) && isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)) {
	$following_me = array_diff($following_me,$get_block_users);
}
$user_cat_follow = get_user_meta($user_id,"user_cat_follow",true);
$user_tag_follow = get_user_meta($user_id,"user_tag_follow",true);

$user_following = (is_array($following_me) && !empty($following_me)?implode(",",$following_me):"");
$user_cat_follow = apply_filters("wpqa_user_cat_follow",$user_cat_follow);
$cat_following = (is_array($user_cat_follow) && !empty($user_cat_follow)?implode(",",$user_cat_follow):"");
$tag_following = (is_array($user_tag_follow) && !empty($user_tag_follow)?implode(",",$user_tag_follow):"");
$all_following = ($cat_following != ""?$cat_following:"").($cat_following != "" && $tag_following != ""?",":"").($tag_following != ""?$tag_following:"");

$user_following_if = ((isset($home_feed["users"]["value"]) && $home_feed["users"]["value"] === "0") || $number_of_users == 0 || ($number_of_users > 0 && is_array($following_me) && count($following_me) >= $number_of_users)?"yes":"no");
$cat_following_if = ((isset($home_feed["cats"]["value"]) && $home_feed["cats"]["value"] === "0") || $number_of_categories == 0 || ($number_of_categories > 0 && is_array($user_cat_follow) && count($user_cat_follow) >= $number_of_categories)?"yes":"no");
$tag_following_if = ((isset($home_feed["tags"]["value"]) && $home_feed["tags"]["value"] === "0") || $number_of_tags == 0 || ($number_of_tags > 0 && is_array($user_tag_follow) && count($user_tag_follow) >= $number_of_tags)?"yes":"no");

$user_count_already = (is_array($following_me)?count($following_me):0);
$cat_count_already = (is_array($user_cat_follow)?count($user_cat_follow):0);
$tag_count_already = (is_array($user_tag_follow)?count($user_tag_follow):0);
?>