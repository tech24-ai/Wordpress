<?php $loop_setting = "loop-setting";
$post_id_main = (isset($post_id_main)?$post_id_main:"");
$theme_sidebar = wpqa_sidebars("sidebar_where");
if ($theme_sidebar == "full") {
	$post_columns = " col4 col-boot-sm-4";
}else {
	$post_columns = " col6 col-boot-sm-6";
}
$post_style             = wpqa_options("post_style");
$masonry_style          = wpqa_options("post_masonry_style");
$sort_meta_title_image  = wpqa_options("sort_meta_title_image");
$posts_meta             = wpqa_options("post_meta");
$featured_image         = wpqa_options("featured_image_loop_post");
$featured_image_single  = wpqa_options("featured_image");
$featured_image_style   = wpqa_options("featured_image_style");
$featured_image_width   = wpqa_options("featured_image_width");
$featured_image_height  = wpqa_options("featured_image_height");
$excerpt_type           = wpqa_options("excerpt_type");
$post_excerpt           = wpqa_options("post_excerpt");
$read_more              = wpqa_options("read_more");
$post_share             = wpqa_options("post_share");
$order_sections         = wpqa_options("order_sections");
$post_nav_category      = wpqa_options("post_nav_category");
$post_tags              = wpqa_options("post_tags");
$newsletter_blog        = wpqa_options("newsletter_blog");
$newsletter_action      = wpqa_options("newsletter_action");
$related_number         = wpqa_options("related_number");
$related_number_sidebar = wpqa_options("related_number_sidebar");
$related_number_full    = wpqa_options("related_number_full");
$query_related          = wpqa_options("query_related");
$excerpt_related_title  = wpqa_options("excerpt_related_title");
$comment_in_related     = wpqa_options("comment_in_related");
$date_in_related        = wpqa_options("date_in_related");
$related_style          = wpqa_options("related_style");
if (isset($blog_h) && $blog_h == "blog_h") {
	$post_columns            = " col4 col-boot-sm-4";
	$blog_h_custom_home_blog = wpqa_options("blog_h_custom_home_blog");
	$post_number             = wpqa_options("blog_h_post_number");
	$post_style              = wpqa_options("blog_h_post_style");
	$masonry_style           = wpqa_options("blog_h_masonry_style");
	if ($blog_h_custom_home_blog == "on") {
		$sort_meta_title_image  = wpqa_options("blog_h_sort_meta_title_image");
		$featured_image         = wpqa_options("blog_h_featured_image");
		$read_more              = wpqa_options("blog_h_read_more");
		$post_excerpt           = wpqa_options("blog_h_post_excerpt");
		$post_excerpt           = (isset($post_excerpt) && $post_excerpt != ""?$post_excerpt:5);
		$posts_meta             = wpqa_options("blog_h_post_meta");
		$post_share             = wpqa_options("blog_h_post_share");
	}
}else if (isset($wp_page_template) && $wp_page_template == "template-home.php") {
	$page_tamplate    = true;
	$post_pagination  = wpqa_post_meta("pagination_home",$post_id_main);
	$custom_home_blog = wpqa_post_meta("custom_home_blog",$post_id_main);
	if ($custom_home_blog == "on") {
		$post_style             = wpqa_post_meta("post_style_h",$post_id_main);
		$masonry_style          = wpqa_post_meta("masonry_style_h",$post_id_main);
		$sort_meta_title_image  = wpqa_post_meta("sort_meta_title_image_h",$post_id_main);
		$featured_image         = wpqa_post_meta("featured_image_h",$post_id_main);
		$read_more              = wpqa_post_meta("read_more_h",$post_id_main);
		$post_excerpt           = wpqa_post_meta("post_excerpt_h",$post_id_main);
		$post_excerpt           = (isset($post_excerpt) && $post_excerpt != ""?$post_excerpt:5);
		$posts_meta             = wpqa_post_meta("post_meta_h",$post_id_main);
		$post_share             = wpqa_post_meta("post_share_h",$post_id_main);
	}
}else if (isset($wp_page_template) && $wp_page_template == "template-blog.php") {
	$custom_blog_setting = wpqa_post_meta("custom_blog_setting",$post_id_main);
	if ($custom_blog_setting == "on") {
		$page_tamplate         = true;
		$post_style            = wpqa_post_meta("post_style_b",$post_id_main);
		$masonry_style         = wpqa_post_meta("masonry_style_b",$post_id_main);
		$sort_meta_title_image = wpqa_post_meta("sort_meta_title_image_b",$post_id_main);
		$featured_image        = wpqa_post_meta("featured_image_b",$post_id_main);
		$read_more             = wpqa_post_meta("read_more_b",$post_id_main);
		$post_excerpt          = wpqa_post_meta("post_excerpt_b",$post_id_main);
		$post_excerpt          = (isset($post_excerpt) && $post_excerpt != ""?$post_excerpt:5);
		$post_number           = wpqa_post_meta("post_number_b",$post_id_main);
		$post_pagination       = wpqa_post_meta("post_pagination_b",$post_id_main);
		$posts_meta            = wpqa_post_meta("post_meta_b",$post_id_main);
		$post_share            = wpqa_post_meta("post_share_b",$post_id_main);
	}
}else if (is_author() || wpqa_is_user_profile()) {
	$author_post_style = wpqa_options("author_post_style");
	if ($author_post_style != "default") {
		$post_style            = $author_post_style;
		$sort_meta_title_image = wpqa_options("author_sort_meta_title_image");
	}
}else if (is_category()) {
	$custom_blog_setting = wpqa_term_meta("custom_blog_setting",$category_id);
	if ($custom_blog_setting == "on") {
		$page_tamplate         = true;
		$post_style            = wpqa_term_meta("post_style",$category_id);
		$masonry_style         = wpqa_term_meta("masonry_style",$category_id);
		$sort_meta_title_image = wpqa_term_meta("sort_meta_title_image",$category_id);
		$featured_image        = wpqa_term_meta("featured_image_loop_post",$category_id);
		$post_excerpt          = wpqa_term_meta("post_excerpt",$category_id);
		$post_excerpt          = ($post_excerpt != ""?$post_excerpt:"40");
		$post_share            = wpqa_term_meta("post_share",$category_id);
		$posts_meta            = wpqa_term_meta("post_meta",$category_id);
		$read_more             = wpqa_term_meta("read_more",$category_id);
		$post_pagination       = wpqa_term_meta("post_pagination",$category_id);
		$post_number           = wpqa_term_meta("post_number",$category_id);
	}
}else if (is_single() || is_page()) {
	$custom_sections = wpqa_post_meta("custom_sections",$post_id_main);
	$custom_page_setting = wpqa_post_meta("custom_page_setting",$post_id_main);
	if ($custom_sections == "on") {
		$order_sections = wpqa_post_meta("order_sections",$post_id_main);
	}
	if (is_single()) {
		$featured_image = $featured_image_single;
	}
	$featured_image_style_p = wpqa_post_meta("featured_image_style",$post_id_main);
	if ($featured_image_style_p != "default") {
		$featured_image_width   = wpqa_post_meta("featured_image_width",$post_id_main);
		$featured_image_height  = wpqa_post_meta("featured_image_height",$post_id_main);
	}
	$featured_image_style   = ($featured_image_style_p != "default"?$featured_image_style_p:$featured_image_style);
	if ($custom_page_setting == "on") {
		$featured_image        = wpqa_post_meta("featured_image",$post_id_main);
		$post_title            = wpqa_post_meta("post_title",$post_id_main);
		$title_post_style      = wpqa_post_meta("post_title_style",$post_id_main);
		$title_post_icon       = wpqa_post_meta("post_title_icon",$post_id_main);
		$posts_meta            = wpqa_post_meta("post_meta",$post_id_main);
		$post_tags             = wpqa_post_meta("post_tags",$post_id_main);
		$newsletter_blog       = wpqa_post_meta("newsletter_blog",$post_id_main);
		$newsletter_action     = wpqa_post_meta("newsletter_action",$post_id_main);
		$post_share            = wpqa_post_meta("post_share",$post_id_main);
		$related_number        = wpqa_post_meta("related_number",$post_id_main);
		$related_number_full   = $related_number_sidebar = $related_number;
		$query_related         = wpqa_post_meta("query_related",$post_id_main);
		$excerpt_related_title = wpqa_post_meta("excerpt_related_title",$post_id_main);
		$comment_in_related    = wpqa_post_meta("comment_in_related",$post_id_main);
		$date_in_related       = wpqa_post_meta("date_in_related",$post_id_main);
		$related_style         = wpqa_post_meta("related_style",$post_id_main);
		$post_nav_category     = wpqa_post_meta("post_nav_category",$post_id_main);
	}else if (is_page()) {
		$featured_image = "";
	}
}
$post_columns  = apply_filters(wpqa_prefix_theme."_post_columns_loop_settings",$post_columns,(isset($blog_h)?$blog_h:""));
$post_number   = (isset($post_number) && $post_number != ""?$post_number:get_option("posts_per_page"));
$category_post = (isset($posts_meta["category_post"]) && $posts_meta["category_post"] == "category_post"?"on":"");
$title_post    = (isset($posts_meta["title_post"]) && $posts_meta["title_post"] == "title_post"?"on":(isset($post_title)?$post_title:""));
$author_by     = (isset($posts_meta["author_by"]) && $posts_meta["author_by"] == "author_by"?"on":"");
$post_date     = (isset($posts_meta["post_date"]) && $posts_meta["post_date"] == "post_date"?"on":"");
$post_comment  = (isset($posts_meta["post_comment"]) && $posts_meta["post_comment"] == "post_comment"?"on":"");
$post_views    = (isset($posts_meta["post_views"]) && $posts_meta["post_views"] == "post_views"?"on":"");?>