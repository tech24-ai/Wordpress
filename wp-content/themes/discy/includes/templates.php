<?php include locate_template("includes/header-part.php");
include wpqa_get_template("logged-only.php","theme-parts/");
$wp_page_template = discy_post_meta("_wp_page_template",$post_id_main,false);
$loop_page = "loop.php";
if ($wp_page_template == "template-knowledgebase.php") {
	$its_post_type = wpqa_knowledgebase_type;
}else if ($wp_page_template == "template-question.php") {
	$its_post_type = wpqa_questions_type;
}else if ($wp_page_template == "template-groups.php") {
	$loop_page = "loop-groups.php";
}else if ($wp_page_template == "template-blog.php") {
	do_action("discy_before_blog_action");
}else if ($wp_page_template == "template-contact.php") {
	echo "<div class='post-content-text'>";
}
if ($wp_page_template == "template-home.php") {
	include wpqa_get_template("tabs.php","theme-parts/");
}else {
	include locate_template("theme-parts/".$loop_page."");
}
if ($wp_page_template == "template-blog.php") {
	do_action("discy_after_blog_action");
}else if ($wp_page_template == "template-contact.php") {
	echo "</div>";
}
include locate_template("includes/footer-part.php");?>