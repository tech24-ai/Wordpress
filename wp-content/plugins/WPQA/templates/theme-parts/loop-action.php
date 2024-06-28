<?php 
do_action(wpqa_prefix_theme."_before_include_content",$post);
$loop_page = "content.php";
if ($post->post_type == wpqa_questions_type || $post->post_type == wpqa_asked_questions_type) {
	$loop_page = "content-question.php";
}else if ($post->post_type == wpqa_knowledgebase_type) {
	$loop_page = "content-knowledgebase.php";
}
if (!isset($loop_setting) || (isset($loop_setting) && $loop_setting != str_replace(".php","",$loop_page))) {
	include locate_template("theme-parts/".$loop_page);
}
do_action(wpqa_prefix_theme."_after_include_content",$post);
$zero_index = apply_filters("wpqa_zero_index",(isset($zero_index)?$zero_index:0));
$post_count = apply_filters("wpqa_post_count",(isset($post_count)?$post_count:0));?>