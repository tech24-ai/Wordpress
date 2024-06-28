<?php $loop_setting           = "knowledgebase-setting";
$post_id_main                 = (isset($post_id_main)?$post_id_main:"");
$knowledgebase_style          = wpqa_options("knowledgebase_style");
$icon_knowledgebases          = wpqa_options("icon_knowledgebases");
$knowledgebase_meta_icon      = wpqa_options("knowledgebase_meta_icon");
$knowledgebase_meta_style     = wpqa_options("knowledgebase_meta_style");
$knowledgebase_meta           = wpqa_options("knowledgebase_meta");
$author_image                 = wpqa_options("author_image");
$author_image_single          = wpqa_options("knowledgebase_author_image_single");
$excerpt_type                 = wpqa_options("excerpt_type");
$knowledgebase_excerpt        = wpqa_options("knowledgebase_excerpt");
$excerpt_knowledgebases       = wpqa_options("excerpt_knowledgebases");
$knowledgebase_tags           = wpqa_options("knowledgebase_tags");
$post_share                   = wpqa_options("knowledgebase_share");
$post_pagination              = wpqa_options("knowledgebase_pagination");
$didnt_find_answer            = wpqa_options("didnt_find_answer");
$didnt_find_answer_style      = wpqa_options("didnt_find_answer_style");
$knowledgebase_related        = wpqa_options("knowledgebase_related");
$related_number_knowledgebase = wpqa_options("related_number_knowledgebase");
$query_related_knowledgebase  = wpqa_options("query_related_knowledgebase");
$related_title_knowledgebase  = wpqa_options("related_title_knowledgebase");
if (isset($wp_page_template) && $wp_page_template == "template-home.php") {
	$page_tamplate        = true;
	$post_pagination      = wpqa_post_meta("pagination_home",$post_id_main);
	$custom_home_knowledgebase = wpqa_post_meta("custom_home_knowledgebase",$post_id_main);
	if ($custom_home_knowledgebase == "on") {
		$knowledgebase_style    = wpqa_post_meta("knowledgebase_style_h",$post_id_main);
		$icon_knowledgebases    = wpqa_post_meta("icon_knowledgebases_h",$post_id_main);
		$author_image           = wpqa_post_meta("author_image_h",$post_id_main);
		$excerpt_knowledgebases = wpqa_post_meta("excerpt_knowledgebases_h",$post_id_main);
		$knowledgebase_excerpt  = wpqa_post_meta("knowledgebase_excerpt_h",$post_id_main);
		$knowledgebase_meta     = wpqa_post_meta("knowledgebase_meta_h",$post_id_main);
	}
}else if (isset($wp_page_template) && $wp_page_template == "template-knowledgebase.php") {
	$custom_knowledgebase_setting = wpqa_post_meta("custom_knowledgebase_setting",$post_id_main);
	if ($custom_knowledgebase_setting == "on") {
		$page_tamplate          = true;
		$knowledgebase_style    = wpqa_post_meta("knowledgebase_style_k",$post_id_main);
		$icon_knowledgebases    = wpqa_post_meta("icon_knowledgebases_k",$post_id_main);
		$author_image           = wpqa_post_meta("author_image_k",$post_id_main);
		$knowledgebase_meta     = wpqa_post_meta("knowledgebase_meta_k",$post_id_main);
		$knowledgebase_excerpt  = wpqa_post_meta("knowledgebase_excerpt_k",$post_id_main);
		$excerpt_knowledgebases = wpqa_post_meta("excerpt_knowledgebases_k",$post_id_main);
		$post_pagination        = wpqa_post_meta("knowledgebase_pagination_k",$post_id_main);
		$post_number            = wpqa_post_meta("knowledgebase_number_k",$post_id_main);
	}
}else if (is_tax(wpqa_knowledgebase_categories)) {
	$custom_knowledgebase_setting = wpqa_term_meta("custom_knowledgebase_setting",$category_id);
	$page_tamplate  = true;
	if ($custom_knowledgebase_setting == "on") {
		$knowledgebase_style    = wpqa_term_meta("knowledgebase_style",$category_id);
		$icon_knowledgebases    = wpqa_term_meta("icon_knowledgebases",$category_id);
		$knowledgebase_meta     = wpqa_term_meta("knowledgebase_meta",$category_id);
		$author_image           = wpqa_term_meta("author_image_k",$category_id);
		$knowledgebase_excerpt  = wpqa_term_meta("knowledgebase_excerpt",$category_id);
		$knowledgebase_excerpt  = ($knowledgebase_excerpt != ""?$knowledgebase_excerpt:"40");
		$excerpt_knowledgebases = wpqa_term_meta("excerpt_knowledgebases",$category_id);
		$post_pagination        = wpqa_term_meta("knowledgebase_pagination",$category_id);
		$post_number            = wpqa_term_meta("knowledgebase_number",$category_id);
	}
}else if (is_single()) {
	$custom_page_setting = wpqa_post_meta("custom_page_setting",$post_id_main);
	if ($custom_page_setting == "on") {
		$knowledgebase_meta           = wpqa_post_meta("post_meta",$post_id_main);
		$author_image_single          = wpqa_post_meta("author_image_single",$post_id_main);
		$knowledgebase_tags           = wpqa_post_meta("post_tags",$post_id_main);
		$post_share                   = wpqa_post_meta("post_share",$post_id_main);
		$didnt_find_answer            = wpqa_post_meta("didnt_find_answer",$post_id_main);
		$didnt_find_answer_style      = wpqa_post_meta("didnt_find_answer_style",$post_id_main);
		$knowledgebase_related        = wpqa_post_meta("knowledgebase_related",$post_id_main);
		$related_number_knowledgebase = wpqa_post_meta("related_number",$post_id_main);
		$query_related_knowledgebase  = wpqa_post_meta("query_related",$post_id_main);
		$related_title_knowledgebase  = wpqa_post_meta("excerpt_related_title",$post_id_main);
	}
}
$post_number            = (isset($post_number) && $post_number != ""?$post_number:get_option("posts_per_page"));
$author_by              = (isset($knowledgebase_meta["author_by"]) && $knowledgebase_meta["author_by"] == "author_by"?"on":"");
$knowledgebase_date     = (isset($knowledgebase_meta["knowledgebase_date"]) && $knowledgebase_meta["knowledgebase_date"] == "knowledgebase_date"?"on":(isset($knowledgebase_meta["post_date"]) && $knowledgebase_meta["post_date"] == "post_date"?"on":""));
$category_knowledgebase = (isset($knowledgebase_meta["category_knowledgebase"]) && $knowledgebase_meta["category_knowledgebase"] == "category_knowledgebase"?"on":(isset($knowledgebase_meta["category_post"]) && $knowledgebase_meta["category_post"] == "category_post"?"on":""));
$knowledgebase_views    = (isset($knowledgebase_meta["knowledgebase_views"]) && $knowledgebase_meta["knowledgebase_views"] == "knowledgebase_views"?"on":"");
$knowledgebase_votes    = (isset($knowledgebase_meta["knowledgebase_votes"]) && $knowledgebase_meta["knowledgebase_votes"] == "knowledgebase_votes"?"on":"");
$knowledgebase_read     = (isset($knowledgebase_meta["knowledgebase_read"]) && $knowledgebase_meta["knowledgebase_read"] == "knowledgebase_read"?"on":"");
$knowledgebase_print    = (isset($knowledgebase_meta["knowledgebase_print"]) && $knowledgebase_meta["knowledgebase_print"] == "knowledgebase_print"?"on":"");
$theme_sidebar_all      = $theme_sidebar = wpqa_sidebars("sidebar_where");
?>