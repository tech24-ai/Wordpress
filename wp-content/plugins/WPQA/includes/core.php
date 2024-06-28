<?php

/* @author    2codeThemes
*  @package   WPQA/includes
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Dark skin */
function wpqa_dark_skin() {
	$all_skin_l        = wpqa_options("site_skin_l");
	$tax_archive       = apply_filters(wpqa_prefix_theme.'_tax_archive',false);
	$tax_filter        = apply_filters(wpqa_prefix_theme."_before_question_category",false);
	$specific_page     = wpqa_is_specific_page();
	$search_type       = (wpqa_is_search()?wpqa_search_type():"");
	$search_no_default = (wpqa_is_search()?wpqa_search_type_without_default():"");
	$wpqa_group_id     = (wpqa_group_id() > 0?wpqa_group_id():"");

	if (is_page() || is_single() || $search_type == "posts" || $search_type == "questions" || $search_type == "knowledgebases" || $search_type == "groups" || $wpqa_group_id > 0) {
		global $post;
	}

	if (($specific_page == "search" && $search_no_default == "") || ($specific_page != "" && $specific_page != "search")) {
		$skin_l = wpqa_options($specific_page."_skin_l");
	}else if (is_author() || wpqa_is_user_profile()) {
		$skin_l = wpqa_options("author_skin_l");
	}else if (is_category() || is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true) {
		if (is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true) {
			$category_id = (int)get_query_var('wpqa_term_id');
		}else {
			$category_id = esc_html(get_query_var('cat'));
		}
		$cat_skin_l = get_term_meta($category_id,prefix_terms."cat_skin_l",true);
		$cat_skin_l = ($cat_skin_l != ""?$cat_skin_l:"default");
		if (is_category() && ($cat_skin_l == "" || $cat_skin_l == "default")) {
			$cat_skin_l = wpqa_options("post_skin_l");
		}
		if ((is_tax(wpqa_question_categories) || $tax_filter == true) && ($cat_skin_l == "" || $cat_skin_l == "default")) {
			$cat_skin_l = wpqa_options("question_skin_l");
		}
		if (is_tax(wpqa_knowledgebase_categories) && ($cat_skin_l == "" || $cat_skin_l == "default")) {
			$cat_skin_l = wpqa_options("knowledgebase_skin_l");
		}
		if (isset($cat_skin_l)) {
			$skin_l = $cat_skin_l;
		}
	}else if (is_tag() || (is_archive() && !is_post_type_archive(wpqa_questions_type) && !is_post_type_archive(wpqa_knowledgebase_type) && !is_post_type_archive("group") && $tax_archive != true && !is_tax(wpqa_question_tags) && !is_tax(wpqa_knowledgebase_tags))) {
		$skin_l = wpqa_options("post_skin_l");
	}else if ($search_type == "questions" || is_tax(wpqa_question_tags) || is_post_type_archive(wpqa_questions_type)) {
		$skin_l = wpqa_options("question_skin_l");
	}else if (is_tax(wpqa_knowledgebase_tags) || is_post_type_archive(wpqa_knowledgebase_type)) {
		$skin_l = wpqa_options("knowledgebase_skin_l");
	}else if ($search_type == "groups" || is_post_type_archive("group") || $wpqa_group_id > 0) {
		$skin_l = wpqa_options("group_skin_l");
	}else if (is_single() || $search_type == "posts" || $search_type == "questions" || $search_type == "knowledgebases" || $search_type == "groups" || $wpqa_group_id > 0 || is_page()) {
		$post_skin_l = get_post_meta($post->ID,prefix_meta."post_skin_l",true);
		if ($post_skin_l == "" || $post_skin_l == "default") {
			if (is_singular("post") || $search_type == "posts") {
				$post_skin_l = wpqa_options("post_skin_l");
			}
			if ($search_type == "questions" || is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) {
				$post_skin_l = wpqa_options("question_skin_l");
			}
			if ($search_type == "knowledgebases" || is_singular(wpqa_knowledgebase_type)) {
				$post_skin_l = wpqa_options("knowledgebase_skin_l");
			}
			if ($search_type == "groups" || $wpqa_group_id > 0) {
				$post_skin_l = wpqa_options("group_skin_l");
			}
			if ((is_singular("post") || $search_type == "posts") && $post_skin_l != "default" && $post_skin_l != "") {
				$skin_l = $post_skin_l;
			}else if ((is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || $search_type == "questions" || is_singular(wpqa_knowledgebase_type) || $search_type == "knowledgebases") && $post_skin_l != "default" && $post_skin_l != "") {
				$skin_l = $post_skin_l;
			}else if ($wpqa_group_id > 0 && $post_skin_l != "default" && $post_skin_l != "") {
				$skin_l = $post_skin_l;
			}else {
				$skin_l = $all_skin_l;
			}
		}else {
			$skin_l = $post_skin_l;
		}
		if (is_singular("post") || $search_type == "posts" || is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || $search_type == "questions" || is_singular(wpqa_knowledgebase_type) || $search_type == "knowledgebases") {
			$post_type = $post->post_type;
			$tax = wpqa_get_tax_name($post_type);
			$get_category = wp_get_post_terms($post->ID,$tax,array("fields" => "ids"));
			if (isset($get_category[0])) {
				$category_single_id = $get_category[0];
			}
			if (isset($category_single_id)) {
				$setting_single = get_term_meta($category_single_id,prefix_terms."setting_single",true);
				if ($setting_single == "on") {
					$skin_l = get_term_meta($category_single_id,prefix_terms."cat_skin_l",true);
					$skin_l = ($skin_l != ""?$skin_l:"default");
				}
			}
		}
	}else {
		$skin_l = $all_skin_l;
	}
	$class = (isset($skin_l) && $skin_l == "default"?$all_skin_l:$skin_l);
	$class = ($class == "dark"?"dark":"light");
	$skin_switcher = wpqa_options("skin_switcher");
	if ($skin_switcher == "on") {
		if (is_user_logged_in()) {
			$user_id = get_current_user_id();
			$get_dark = get_user_meta($user_id,'wpqa_get_dark',true);
		}else {
			$uniqid_cookie = wpqa_options('uniqid_cookie');
			$get_dark = (isset($_COOKIE[$uniqid_cookie.'wpqa_get_dark'])?$_COOKIE[$uniqid_cookie.'wpqa_get_dark']:'');
		}
		$class = ($get_dark != ''?($get_dark == 'dark'?'dark':'light'):$class);
	}
	$class = (isset($_GET['skin']) && $_GET['skin'] != ""?($_GET['skin'] == 'dark'?'dark':'light'):$class);
	return $class;
}
/* Sidebars */
function wpqa_sidebar_layout($sidebar = '') {
	$tax_filter        = apply_filters(wpqa_prefix_theme."_before_question_category",false);
	$specific_page     = wpqa_is_specific_page();
	$search_type       = (wpqa_is_search()?wpqa_search_type():"");
	$search_no_default = (wpqa_is_search()?wpqa_search_type_without_default():"");
	$wpqa_group_id     = (wpqa_group_id() > 0?wpqa_group_id():"");

	if (($specific_page == "search" && $search_no_default == "") || ($specific_page != "" && $specific_page != "search")) {
		$sidebar_style = wpqa_options($specific_page."_sidebar".$sidebar);
	}

	if (is_author() || wpqa_is_user_profile()) {
		$sidebar_style = wpqa_options("author_sidebar".$sidebar);
	}

	if (is_page() || is_single() || $search_type == "posts" || $search_type == "questions" || $search_type == "knowledgebases" || $search_type == "groups" || $wpqa_group_id > 0) {
		global $post;
	}

	$group_sidebar = wpqa_options("group_sidebar".$sidebar);
	$question_sidebar = wpqa_options("question_sidebar".$sidebar);
	$knowledgebase_sidebar = wpqa_options("knowledgebase_sidebar".$sidebar);
	$post_sidebar  = wpqa_options("post_sidebar".$sidebar);
	$home_page_sidebar = wpqa_options("sidebar_home".$sidebar);
	if ($home_page_sidebar == "default" || $home_page_sidebar == "" || !is_active_sidebar($home_page_sidebar)) {
		$home_page_sidebar = 'sidebar_default'.$sidebar;
	}

	if (is_category() || is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true) {
		if (is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true) {
			$category_id = (int)get_query_var('wpqa_term_id');
		}else {
			$category_id = esc_html(get_query_var('cat'));
		}
		$sidebar_style = get_term_meta($category_id,prefix_terms."cat_sidebar".$sidebar,true);
		$sidebar_style = ($sidebar_style != ""?$sidebar_style:"default");
		if (is_category()) {
			if ($sidebar_style == "" || $sidebar_style == "default") {
				$sidebar_style = $post_sidebar;
			}
		}
		if (is_tax(wpqa_question_categories) || $tax_filter == true) {
			if ($sidebar_style == "" || $sidebar_style == "default") {
				$sidebar_style = $question_sidebar;
			}
		}
		if (is_tax(wpqa_knowledgebase_categories)) {
			if ($sidebar_style == "" || $sidebar_style == "default") {
				$sidebar_style = $knowledgebase_sidebar;
			}
		}
	}

	if (is_single() || $search_type == "posts" || $search_type == "questions" || $search_type == "knowledgebases" || $search_type == "groups" || $wpqa_group_id > 0 || is_page()) {
		$what_sidebar = get_post_meta($post->ID,prefix_meta."what_sidebar".$sidebar,true);
		if (is_singular("post") || $search_type == "posts") {
			if ($what_sidebar == "default" || $what_sidebar == "") {
				$what_sidebar = $post_sidebar;
			}
		}
		if ($wpqa_group_id > 0 || $search_type == "groups") {
			if ($what_sidebar == "default" || $what_sidebar == "") {
				$what_sidebar = $group_sidebar;
			}
		}
		if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || $search_type == "questions") {
			if ($what_sidebar == "default" || $what_sidebar == "") {
				$what_sidebar = $question_sidebar;
			}
		}
		if (is_singular(wpqa_knowledgebase_type) || $search_type == "knowledgebases") {
			if ($what_sidebar == "default" || $what_sidebar == "") {
				$what_sidebar = $knowledgebase_sidebar;
			}
		}
		if (is_singular("post") || $search_type == "posts" || is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || $search_type == "questions" || is_singular(wpqa_knowledgebase_type) || $search_type == "knowledgebases") {
			$post_type = $post->post_type;
			$tax = wpqa_get_tax_name($post_type);
			$get_category = wp_get_post_terms($post->ID,$tax,array("fields" => "ids"));
			if (isset($get_category[0])) {
				$category_single_id = $get_category[0];
			}
			if (isset($category_single_id)) {
				$setting_single = get_term_meta($category_single_id,prefix_terms."setting_single",true);
				if ($setting_single == "on") {
					$what_sidebar = get_term_meta($category_single_id,prefix_terms."cat_sidebar".$sidebar,true);
					$what_sidebar = ($what_sidebar != ""?$what_sidebar:"default");
				}
			}
		}
	}

	if (($specific_page == "search" && $search_no_default == "") || ($specific_page != "" && $specific_page != "search")) {
		if ($sidebar_style != "" && is_active_sidebar($sidebar_style)) {
			$dynamic_sidebar = $sidebar_style;
		}else {
			$dynamic_sidebar = $home_page_sidebar;
		}
	}else if ((is_author() || wpqa_is_user_profile()) && $sidebar_style != "default" && $sidebar_style != "") {
		if ($sidebar_style != "" && is_active_sidebar($sidebar_style)) {
			$dynamic_sidebar = $sidebar_style;
		}else {
			$dynamic_sidebar = $home_page_sidebar;
		}
	}else if ((is_category() || is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true) && $sidebar_style != "default" && $sidebar_style != "") {
		if (is_active_sidebar($sidebar_style)) {
			$dynamic_sidebar = $sidebar_style;
		}else {
			$dynamic_sidebar = $home_page_sidebar;
		}
	}else if ((is_tag() || (is_archive() && !is_post_type_archive(wpqa_questions_type) && !is_post_type_archive(wpqa_knowledgebase_type) && !is_post_type_archive("group") && !is_tax(wpqa_question_tags) && !is_tax(wpqa_knowledgebase_tags))) && $post_sidebar != "default" && $post_sidebar != "") {
		if (is_active_sidebar($post_sidebar)) {
			$dynamic_sidebar = $post_sidebar;
		}else {
			$dynamic_sidebar = $home_page_sidebar;
		}
	}else if (is_post_type_archive("group") && $group_sidebar != "default" && $group_sidebar != "") {
		if (is_active_sidebar($group_sidebar)) {
			$dynamic_sidebar = $group_sidebar;
		}else {
			$dynamic_sidebar = $home_page_sidebar;
		}
	}else if ((is_tax(wpqa_question_tags) || is_post_type_archive(wpqa_questions_type)) && $question_sidebar != "default" && $question_sidebar != "") {
		if (is_active_sidebar($question_sidebar)) {
			$dynamic_sidebar = $question_sidebar;
		}else {
			$dynamic_sidebar = $home_page_sidebar;
		}
	}else if ((is_tax(wpqa_knowledgebase_tags) || is_post_type_archive(wpqa_knowledgebase_type)) && $knowledgebase_sidebar != "default" && $knowledgebase_sidebar != "") {
		if (is_active_sidebar($knowledgebase_sidebar)) {
			$dynamic_sidebar = $knowledgebase_sidebar;
		}else {
			$dynamic_sidebar = $home_page_sidebar;
		}
	}else if ((is_single() || $search_type == "posts" || $search_type == "questions" || $search_type == "knowledgebases" || $search_type == "groups" || $wpqa_group_id > 0 || is_page()) && $what_sidebar != "default" && $what_sidebar != "") {
		if (is_active_sidebar($what_sidebar)) {
			$dynamic_sidebar = $what_sidebar;
		}else {
			$dynamic_sidebar = $home_page_sidebar;
		}
	}else  {
		$dynamic_sidebar = $home_page_sidebar;
	}

	$return = (isset($dynamic_sidebar)?$dynamic_sidebar:"");
	
	return $return;
}
function wpqa_sidebars($return = 'sidebar_dir') {
	$specific_page     = wpqa_is_specific_page();
	$search_type       = (wpqa_is_search()?wpqa_search_type():"");
	$search_no_default = (wpqa_is_search()?wpqa_search_type_without_default():"");
	$tax_archive       = apply_filters(wpqa_prefix_theme.'_tax_archive',false);
	$tax_filter        = apply_filters(wpqa_prefix_theme."_before_question_category",false);
	$wpqa_group_id     = (wpqa_group_id() > 0?wpqa_group_id():"");
	$sidebar_layout    = "";
	
	$menu_sidebar      = "menu_sidebar".(has_himer() || has_knowly()?" col-boot-lg-7":"");
	$page_right        = "main_sidebar main_right".(has_himer() || has_knowly()?" col-boot-lg-8":"");
	$page_left         = "main_sidebar main_left".(has_himer() || has_knowly()?" col-boot-lg-8":"");
	$page_full_width   = "main_full".(has_himer() || has_knowly()?" col-boot-lg-12":"");
	$page_centered     = "main_full main_center".(has_himer() || has_knowly()?" col-boot-lg-8":"");
	$menu_left         = "menu_left".(has_himer() || has_knowly()?" col-boot-lg-10":"");

	if (is_page() || is_single() || $search_type == "posts" || $search_type == "questions" || $search_type == "knowledgebases" || $search_type == "groups" || $wpqa_group_id > 0) {
		global $post;
	}
	
	if (is_author() || wpqa_is_user_profile()) {
		$author_sidebar_layout = wpqa_options('author_sidebar_layout');
	}else if (is_category() || is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true) {
		if (is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true) {
			$category_id = (int)get_query_var('wpqa_term_id');
		}else {
			$category_id = esc_html(get_query_var('cat'));
		}
		$cat_sidebar_layout = get_term_meta($category_id,prefix_terms."cat_sidebar_layout",true);
		$cat_sidebar_layout = ($cat_sidebar_layout != ""?$cat_sidebar_layout:"default");
		if (is_category() && ($cat_sidebar_layout == "" || $cat_sidebar_layout == "default")) {
			$cat_sidebar_layout = wpqa_options("post_sidebar_layout");
		}
		if ((is_tax(wpqa_question_categories) || $tax_filter == true) && ($cat_sidebar_layout == "" || $cat_sidebar_layout == "default")) {
			$cat_sidebar_layout = wpqa_options("question_sidebar_layout");
		}
		if (is_tax(wpqa_knowledgebase_categories) && ($cat_sidebar_layout == "" || $cat_sidebar_layout == "default")) {
			$cat_sidebar_layout = wpqa_options("knowledgebase_sidebar_layout");
		}
	}else if (is_tag() || (is_archive() && !is_post_type_archive(wpqa_questions_type) && !is_post_type_archive(wpqa_knowledgebase_type) && !is_post_type_archive("group") && $tax_archive != true && !is_tax(wpqa_question_tags) && !is_tax(wpqa_knowledgebase_tags))) {
		$cat_sidebar_layout = wpqa_options("post_sidebar_layout");
	}else if ($search_type == "questions" || is_tax(wpqa_question_tags) || is_post_type_archive(wpqa_questions_type)) {
		$cat_sidebar_layout = wpqa_options("question_sidebar_layout");
	}else if ($search_type == "knowledgebases" || is_tax(wpqa_knowledgebase_tags) || is_post_type_archive(wpqa_knowledgebase_type)) {
		$cat_sidebar_layout = wpqa_options("knowledgebase_sidebar_layout");
	}else if ($search_type == "groups" || is_post_type_archive("group") || $wpqa_group_id > 0) {
		$cat_sidebar_layout = wpqa_options("group_sidebar_layout");
	}else if (is_single() || $search_type == "posts" || $search_type == "questions" || $search_type == "knowledgebases" || $search_type == "groups" || $wpqa_group_id > 0 || is_page()) {
		$sidebar_post = get_post_meta($post->ID,prefix_meta."sidebar",true);
		if ($sidebar_post == "" || $sidebar_post == "default") {
			if (is_singular("post") || $search_type == "posts") {
				$cat_sidebar_layout = wpqa_options("post_sidebar_layout");
			}
			if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || $search_type == "questions") {
				$cat_sidebar_layout = wpqa_options("question_sidebar_layout");
			}
			if (is_singular(wpqa_knowledgebase_type) || $search_type == "knowledgebases") {
				$cat_sidebar_layout = wpqa_options("knowledgebase_sidebar_layout");
			}
			if ($wpqa_group_id > 0 || $search_type == "groups") {
				$cat_sidebar_layout = wpqa_options("group_sidebar_layout");
			}
			if ((is_singular("post") || $search_type == "posts") && $cat_sidebar_layout != "default" && $cat_sidebar_layout != "") {
				$sidebar_post = $cat_sidebar_layout;
			}else if ((is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || $search_type == "questions" || is_singular(wpqa_knowledgebase_type) || $search_type == "knowledgebases") && $cat_sidebar_layout != "default" && $cat_sidebar_layout != "") {
				$sidebar_post = $cat_sidebar_layout;
			}else if (($wpqa_group_id > 0 || $search_type == "groups") && $cat_sidebar_layout != "default" && $cat_sidebar_layout != "") {
				$sidebar_post = $cat_sidebar_layout;
			}else {
				$sidebar_post = wpqa_options("sidebar_layout");
			}
		}
		if ((is_singular("post") || $search_type == "posts") || is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || $search_type == "questions" || is_singular(wpqa_knowledgebase_type) || $search_type == "knowledgebases") {
			$post_type = $post->post_type;
			$tax = wpqa_get_tax_name($post_type);
			$get_category = wp_get_post_terms($post->ID,$tax,array("fields" => "ids"));
			if (isset($get_category[0])) {
				$category_single_id = $get_category[0];
			}
			if (isset($category_single_id)) {
				$setting_single = get_term_meta($category_single_id,prefix_terms."setting_single",true);
				if ($setting_single == "on") {
					$sidebar_post = get_term_meta($category_single_id,prefix_terms."cat_sidebar_layout",true);
					$sidebar_post = ($sidebar_post != ""?$sidebar_post:"default");
				}
			}
		}
	}else {
		$sidebar_layout = wpqa_options('sidebar_layout');
	}
	
	if (($specific_page == "search" && $search_no_default == "") || ($specific_page != "" && $specific_page != "search")) {
		$sidebar_layout = wpqa_options($specific_page.'_sidebar_layout');
		if ($sidebar_layout == "" || $sidebar_layout == "default") {
			$sidebar_layout = wpqa_options("sidebar_layout");
		}
		if ($sidebar_layout == 'centered') {
			$sidebar_dir = $page_centered;
		}else if ($sidebar_layout == 'menu_sidebar') {
			$sidebar_dir = $menu_sidebar;
		}else if ($sidebar_layout == 'menu_left') {
			$sidebar_dir = $menu_left;
		}else if ($sidebar_layout == 'left') {
			$sidebar_dir = $page_left;
		}else if ($sidebar_layout == 'right') {
			$sidebar_dir = $page_right;
		}else if ($sidebar_layout == 'full') {
			$sidebar_dir = $page_full_width;
		}else {
			$sidebar_dir = (has_discy()?$menu_sidebar:$page_right);
		}
	}else if (is_author() || wpqa_is_user_profile()) {
		if ($author_sidebar_layout == "" || $author_sidebar_layout == "default") {
			$author_sidebar_layout = wpqa_options("sidebar_layout");
		}
		if ($author_sidebar_layout == 'centered') {
			$sidebar_dir = $page_centered;
		}else if ($author_sidebar_layout == 'menu_sidebar') {
			$sidebar_dir = $menu_sidebar;
		}else if ($author_sidebar_layout == 'menu_left') {
			$sidebar_dir = $menu_left;
		}else if ($author_sidebar_layout == 'left') {
			$sidebar_dir = $page_left;
		}else if ($author_sidebar_layout == 'right') {
			$sidebar_dir = $page_right;
		}else if ($author_sidebar_layout == 'full') {
			$sidebar_dir = $page_full_width;
		}else {
			$sidebar_dir = (has_discy()?$menu_sidebar:$page_right);
		}
	}else if (is_category() || is_tag() || (is_archive() && !is_post_type_archive(wpqa_questions_type) && !is_post_type_archive(wpqa_knowledgebase_type) && !is_post_type_archive("group")) || is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true || $tax_archive == true || is_tax(wpqa_question_tags) || is_post_type_archive(wpqa_questions_type) || $search_type == "questions" || is_tax(wpqa_knowledgebase_tags) || is_post_type_archive(wpqa_knowledgebase_type) || $search_type == "knowledgebases" || ($wpqa_group_id > 0 || is_post_type_archive("group") || $search_type == "groups")) {
		if (!isset($cat_sidebar_layout) || $cat_sidebar_layout == "" || $cat_sidebar_layout == "default") {
			$cat_sidebar_layout = wpqa_options("sidebar_layout");
		}
		if ($cat_sidebar_layout == 'centered') {
			$sidebar_dir = $page_centered;
		}else if ($cat_sidebar_layout == 'menu_sidebar') {
			$sidebar_dir = $menu_sidebar;
		}else if ($cat_sidebar_layout == 'menu_left') {
			$sidebar_dir = $menu_left;
		}else if ($cat_sidebar_layout == 'left') {
			$sidebar_dir = $page_left;
		}else if ($cat_sidebar_layout == 'right') {
			$sidebar_dir = $page_right;
		}else if ($cat_sidebar_layout == 'full') {
			$sidebar_dir = $page_full_width;
		}else {
			$sidebar_dir = (has_discy()?$menu_sidebar:$page_right);
		}
	}else if (is_single() || $search_type == "posts" || $search_type == "questions" || $search_type == "knowledgebases" || $search_type == "groups" || $wpqa_group_id > 0 || is_page()) {
		$sidebar_dir = '';
		if (isset($sidebar_post) && $sidebar_post != "default" && $sidebar_post != "") {
			if ($sidebar_post == 'centered') {
				$sidebar_dir = $page_centered;
			}else if ($sidebar_post == 'menu_sidebar') {
				$sidebar_dir = $menu_sidebar;
			}else if ($sidebar_post == 'menu_left') {
				$sidebar_dir = $menu_left;
			}else if ($sidebar_post == 'left') {
				$sidebar_dir = $page_left;
			}else if ($sidebar_post == 'right') {
				$sidebar_dir = $page_right;
			}else if ($sidebar_post == 'full') {
				$sidebar_dir = $page_full_width;
			}else {
				$sidebar_dir = (has_discy()?$menu_sidebar:$page_right);
			}
		}else {
			$sidebar_dir = (has_discy()?$menu_sidebar:$page_right);
		}
	}else {
		$sidebar_layout = wpqa_options('sidebar_layout');
		if ($sidebar_layout == 'centered') {
			$sidebar_dir = $page_centered;
		}else if ($sidebar_layout == 'menu_sidebar') {
			$sidebar_dir = $menu_sidebar;
		}else if ($sidebar_layout == 'menu_left') {
			$sidebar_dir = $menu_left;
		}else if ($sidebar_layout == 'left') {
			$sidebar_dir = $page_left;
		}else if ($sidebar_layout == 'right') {
			$sidebar_dir = $page_right;
		}else if ($sidebar_layout == 'full') {
			$sidebar_dir = $page_full_width;
		}else {
			$sidebar_dir = (has_discy()?$menu_sidebar:$page_right);
		}
	}
	
	if ($return == "sidebar_where") {
		if (strpos($sidebar_dir,'main_full main_center') !== false) {
			$sidebar_where = 'centered';
		}else if ($sidebar_dir == $menu_sidebar) {
			$sidebar_where = 'menu_sidebar';
		}else if ($sidebar_dir == $menu_left) {
			$sidebar_where = 'menu_left';
		}else if ($sidebar_dir == $page_full_width) {
			$sidebar_where = 'full';
		}else {
			$sidebar_where = 'sidebar';
		}
		return apply_filters(wpqa_prefix_theme."_sidebars_where",$sidebar_where);
	}else {
		return apply_filters(wpqa_prefix_theme."_sidebars_dir",$sidebar_dir);
	}
}
/* Custom inline css */
add_filter(wpqa_prefix_theme."_custom_inline_css","wpqa_custom_inline_css");
function wpqa_custom_inline_css($custom_css) {
	global $post;
	if (is_tax() || is_category()) {
		if (is_category()) {
			$category_id = esc_html(get_query_var('cat'));
		}else {
			$category_id = (int)get_query_var('wpqa_term_id');
		}
	}
	
	$site_skin = wpqa_options('site_skin');
	$color_skin_function = $site_skin;

	$tax_archive       = apply_filters(wpqa_prefix_theme.'_tax_archive',false);
	$tax_filter        = apply_filters(wpqa_prefix_theme.'_before_question_category',false);
	$wpqa_group_id     = (wpqa_group_id() > 0?wpqa_group_id():'');
	$specific_page     = wpqa_is_specific_page();
	$search_type       = (wpqa_is_search()?wpqa_search_type():"");
	$search_no_default = (wpqa_is_search()?wpqa_search_type_without_default():"");

	if (is_page() || is_single() || $search_type == "posts" || $search_type == "questions" || $search_type == "knowledgebases" || $search_type == "groups" || $wpqa_group_id > 0) {
		$is_page_single = true;
		$custom_call_action = get_post_meta($post->ID,prefix_meta.'custom_call_action',true);
		$custom_sliders = get_post_meta($post->ID,prefix_meta.'custom_sliders',true);
	}
	
	$background_color  = $background_pattern = $background_type = $background_full = '';
	$custom_background                    = array();
	
	$post_background_type                 = wpqa_options('post_background_type');
	$post_background_pattern              = wpqa_options('post_background_pattern');
	$post_custom_background               = wpqa_options('post_custom_background');
	$post_full_screen_background          = wpqa_options('post_full_screen_background');
	$post_background_color                = wpqa_options('post_background_color');
	
	$question_background_type             = wpqa_options('question_background_type');
	$question_background_pattern          = wpqa_options('question_background_pattern');
	$question_custom_background           = wpqa_options('question_custom_background');
	$question_full_screen_background      = wpqa_options('question_full_screen_background');
	$question_background_color            = wpqa_options('question_background_color');
	
	$knowledgebase_background_type        = wpqa_options('knowledgebase_background_type');
	$knowledgebase_background_pattern     = wpqa_options('knowledgebase_background_pattern');
	$knowledgebase_custom_background      = wpqa_options('knowledgebase_custom_background');
	$knowledgebase_full_screen_background = wpqa_options('knowledgebase_full_screen_background');
	$knowledgebase_background_color       = wpqa_options('knowledgebase_background_color');
	
	$group_background_type                = wpqa_options('group_background_type');
	$group_background_pattern             = wpqa_options('group_background_pattern');
	$group_custom_background              = wpqa_options('group_custom_background');
	$group_full_screen_background         = wpqa_options('group_full_screen_background');
	$group_background_color               = wpqa_options('group_background_color');
	
	if (($specific_page == "search" && $search_no_default == "") || ($specific_page != "" && $specific_page != "search")) {
		$color_skin         = wpqa_options($specific_page.'_skin');
		$primary_color_s    = wpqa_options($specific_page.'_primary_color');
		$background_type    = wpqa_options($specific_page."_background_type");
		$custom_background  = wpqa_options($specific_page."_custom_background");
		$background_pattern = wpqa_options($specific_page."_background_pattern");
		$background_color   = wpqa_options($specific_page."_background_color");
		$background_full    = wpqa_options($specific_page."_full_screen_background");
	}else if (is_author() || wpqa_is_user_profile()) {
		$color_skin         = wpqa_options('author_skin');
		$primary_color_a    = wpqa_options('author_primary_color');
		$background_type    = wpqa_options("author_background_type");
		$custom_background  = wpqa_options("author_custom_background");
		$background_pattern = wpqa_options("author_background_pattern");
		$background_color   = wpqa_options("author_background_color");
		$background_full    = wpqa_options("author_full_screen_background");
	}else if (is_category() || is_tag() || (is_archive() && !is_post_type_archive(wpqa_questions_type) && !is_post_type_archive(wpqa_knowledgebase_type) && !is_post_type_archive("group")) || is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true || $tax_archive == true || is_tax(wpqa_question_tags) || is_post_type_archive(wpqa_questions_type) || $search_type == "questions" || is_tax(wpqa_knowledgebase_tags) || is_post_type_archive(wpqa_knowledgebase_type) || $search_type == "knowledgebases" || is_post_type_archive("group") || $search_type == "groups" || $wpqa_group_id > 0) {
		if (is_category() || is_tag() || (is_archive() && !is_post_type_archive(wpqa_questions_type) && !is_post_type_archive(wpqa_knowledgebase_type) && !is_post_type_archive("group") && $tax_archive != true && !is_tax(wpqa_question_tags) && !is_tax(wpqa_knowledgebase_tags))) {
			$background_type = $post_background_type;
		}
		if (is_tax(wpqa_question_categories) || $tax_filter == true || is_tax(wpqa_question_tags) || is_post_type_archive(wpqa_questions_type) || $search_type == "questions") {
			$background_type = $question_background_type;
		}
		if (is_tax(wpqa_knowledgebase_categories) || $tax_filter == true || is_tax(wpqa_knowledgebase_tags) || is_post_type_archive(wpqa_knowledgebase_type) || $search_type == "knowledgebases") {
			$background_type = $knowledgebase_background_type;
		}
		if (is_post_type_archive("group") || $search_type == "groups" || $wpqa_group_id > 0) {
			$background_type = $group_background_type;
		}
		if (is_tag() || (is_archive() && !is_category() && !is_tax(wpqa_question_categories) && !is_tax(wpqa_knowledgebase_categories) && $tax_filter == true && !is_post_type_archive(wpqa_questions_type) && !is_post_type_archive(wpqa_knowledgebase_type) && !is_post_type_archive("group") && $tax_archive != true && !is_tax(wpqa_question_tags) && !is_tax(wpqa_knowledgebase_tags)) || $search_type == "posts") {
			$cat_skin           = wpqa_options('post_skin');
			$primary_color_c    = wpqa_options('post_primary_color');
			$background_type    = $post_background_type;
			$background_pattern = $post_background_pattern;
			$custom_background  = $post_custom_background;
			$background_full    = $post_full_screen_background;
			$background_color   = $post_background_color;
		}else if (is_post_type_archive("group") || $search_type == "groups" || $wpqa_group_id > 0) {
			$cat_skin           = wpqa_options('group_skin');
			$primary_color_c    = wpqa_options('group_primary_color');
			$background_type    = $group_background_type;
			$background_pattern = $group_background_pattern;
			$custom_background  = $group_custom_background;
			$background_full    = $group_full_screen_background;
			$background_color   = $group_background_color;
		}else if (is_tax(wpqa_question_tags) || is_post_type_archive(wpqa_questions_type) || $search_type == "questions") {
			$cat_skin           = wpqa_options('question_skin');
			$primary_color_c    = wpqa_options('question_primary_color');
			$background_type    = $question_background_type;
			$background_pattern = $question_background_pattern;
			$custom_background  = $question_custom_background;
			$background_full    = $question_full_screen_background;
			$background_color   = $question_background_color;
		}else if (is_tax(wpqa_knowledgebase_tags) || is_post_type_archive(wpqa_knowledgebase_type) || $search_type == "knowledgebases") {
			$cat_skin           = wpqa_options('knowledgebase_skin');
			$primary_color_c    = wpqa_options('knowledgebase_primary_color');
			$background_type    = $knowledgebase_background_type;
			$background_pattern = $knowledgebase_background_pattern;
			$custom_background  = $knowledgebase_custom_background;
			$background_full    = $knowledgebase_full_screen_background;
			$background_color   = $knowledgebase_background_color;
		}else if (isset($category_id)) {
			$cat_skin        = get_term_meta($category_id,prefix_terms.'cat_skin',true);
			$cat_skin        = ($cat_skin != ""?$cat_skin:"default");
			$primary_color_c = get_term_meta($category_id,prefix_terms.'cat_primary_color',true);
			$background_type = get_term_meta($category_id,prefix_terms.'cat_background_type',true);
			if ($background_type == "custom_background" || $background_type == "patterns") {
				$background_type     = get_term_meta($category_id,prefix_terms.'cat_background_type',true);
				$background_pattern  = get_term_meta($category_id,prefix_terms.'cat_background_pattern',true);
				$custom_background   = get_term_meta($category_id,prefix_terms.'cat_custom_background',true);
				$background_img      = (isset($custom_background["image"])?$custom_background["image"]:"");
				$background_color    = ($background_type == "patterns"?get_term_meta($category_id,prefix_terms.'cat_background_color',true):(isset($custom_background["color"])?$custom_background["color"]:""));
				$background_repeat   = (isset($custom_background["repeat"])?$custom_background["repeat"]:"");
				$background_fixed    = (isset($custom_background["attachment"])?$custom_background["attachment"]:"");
				$background_position = (isset($custom_background["position"])?$custom_background["position"]:"");
				$background_full     = get_term_meta($category_id,prefix_terms.'cat_full_screen_background',true);
			}else if (is_category() && ($background_type == "default" || $background_type == "")) {
				$background_type    = $post_background_type;
				$background_pattern = $post_background_pattern;
				$custom_background  = $post_custom_background;
				$background_full    = $post_full_screen_background;
				$background_color   = $post_background_color;
			}else if ((is_tax(wpqa_question_categories) || $tax_filter == true) && ($background_type == "default" || $background_type == "")) {
				$background_type    = $question_background_type;
				$background_pattern = $question_background_pattern;
				$custom_background  = $question_custom_background;
				$background_full    = $question_full_screen_background;
				$background_color   = $question_background_color;
			}else if (is_tax(wpqa_knowledgebase_categories) && ($background_type == "default" || $background_type == "")) {
				$background_type    = $knowledgebase_background_type;
				$background_pattern = $knowledgebase_background_pattern;
				$custom_background  = $knowledgebase_custom_background;
				$background_full    = $knowledgebase_full_screen_background;
				$background_color   = $knowledgebase_background_color;
			}
			
			if (is_category() || is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true) {
				if (is_category()) {
					if ($primary_color_c == "" && ($cat_skin == "" || $cat_skin == "default")) {
						$primary_color_c = wpqa_options('post_primary_color');
					}
					if ($cat_skin == "" || $cat_skin == "default") {
						$cat_skin = wpqa_options('post_skin');
					}
				}
				
				if (is_tax(wpqa_question_categories) || $tax_filter == true) {
					if ($primary_color_c == "" && ($cat_skin == "" || $cat_skin == "default")) {
						$primary_color_c = wpqa_options('question_primary_color');
					}
					if ($cat_skin == "" || $cat_skin == "default") {
						$cat_skin = wpqa_options('question_skin');
					}
				}
				
				if (is_tax(wpqa_knowledgebase_categories)) {
					if ($primary_color_c == "" && ($cat_skin == "" || $cat_skin == "default")) {
						$primary_color_c = wpqa_options('knowledgebase_primary_color');
					}
					if ($cat_skin == "" || $cat_skin == "default") {
						$cat_skin = wpqa_options('knowledgebase_skin');
					}
				}
			}
		}
	}else if (is_single() || $search_type == "posts" || $search_type == "questions" || $search_type == "knowledgebases" || $search_type == "groups" || is_page() || $wpqa_group_id > 0) {
		$primary_color_p             = get_post_meta($post->ID,prefix_meta.'primary_color',true);
		$color_skin                  = get_post_meta($post->ID,prefix_meta.'skin',true);
		$post_primary_color          = wpqa_options("post_primary_color");
		$group_primary_color         = wpqa_options("group_primary_color",$wpqa_group_id);
		$question_primary_color      = wpqa_options("question_primary_color");
		$knowledgebase_primary_color = wpqa_options("knowledgebase_primary_color");
		$post_skin                   = wpqa_options("post_skin");
		$group_skin                  = wpqa_options("group_skin",$wpqa_group_id);
		$question_skin               = wpqa_options("question_skin");
		$knowledgebase_skin          = wpqa_options("knowledgebase_skin");
		$background_type             = get_post_meta($post->ID,prefix_meta.'background_type',true);
		$background_pattern          = get_post_meta($post->ID,prefix_meta.'background_pattern',true);
		$custom_background           = get_post_meta($post->ID,prefix_meta.'custom_background',true);
		if (is_singular("post") || $search_type == "posts" || $search_type == "questions" || $search_type == "knowledgebases" || $search_type == "groups") {
			if ($post_primary_color != "" && $post_primary_color != "default") {
				$primary_color_p = $post_primary_color;
			}
			if ($post_skin != "" && $post_skin != "default") {
				$color_skin = $post_skin;
			}
		}
		if ($wpqa_group_id > 0) {
			if ($group_primary_color != "" && $group_primary_color != "default") {
				$primary_color_p = $group_primary_color;
			}
			if ($group_skin != "" && $group_skin != "default") {
				$color_skin = $group_skin;
			}
		}
		if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) {
			if ($question_primary_color != "" && $question_primary_color != "default") {
				$primary_color_p = $question_primary_color;
			}
			if ($question_skin != "" && $question_skin != "default") {
				$color_skin = $question_skin;
			}
		}
		if (is_singular(wpqa_knowledgebase_type)) {
			if ($knowledgebase_primary_color != "" && $knowledgebase_primary_color != "default") {
				$primary_color_p = $knowledgebase_primary_color;
			}
			if ($knowledgebase_skin != "" && $knowledgebase_skin != "default") {
				$color_skin = $knowledgebase_skin;
			}
		}
		if ($background_type != "" && $background_type != "default" && $background_type != "none") {
			$background_color   = get_post_meta($post->ID,prefix_meta.'background_color',true);
			$background_full    = get_post_meta($post->ID,prefix_meta.'full_screen_background',true);
		}else if ((is_singular("post") || $search_type == "posts") && ($background_type == "default" || $background_type == "")) {
			$background_type    = $post_background_type;
			$background_pattern = $post_background_pattern;
			$custom_background  = $post_custom_background;
			$background_full    = $post_full_screen_background;
			$background_color   = $post_background_color;
		}else if (($wpqa_group_id > 0 || $search_type == "groups" || is_post_type_archive("group")) && ($background_type == "default" || $background_type == "")) {
			$background_type    = $group_background_type;
			$background_pattern = $group_background_pattern;
			$custom_background  = $group_custom_background;
			$background_full    = $group_full_screen_background;
			$background_color   = $group_background_color;
		}else if ((is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || $search_type == "questions") && ($background_type == "default" || $background_type == "")) {
			$background_type    = $question_background_type;
			$background_pattern = $question_background_pattern;
			$custom_background  = $question_custom_background;
			$background_full    = $question_full_screen_background;
			$background_color   = $question_background_color;
		}else if ((is_singular(wpqa_knowledgebase_type) || $search_type == "knowledgebases") && ($background_type == "default" || $background_type == "")) {
			$background_type    = $knowledgebase_background_type;
			$background_pattern = $knowledgebase_background_pattern;
			$custom_background  = $knowledgebase_custom_background;
			$background_full    = $knowledgebase_full_screen_background;
			$background_color   = $knowledgebase_background_color;
		}
		if (is_singular("post") || $search_type == "posts" || is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || $search_type == "questions" || is_singular(wpqa_knowledgebase_type) || $search_type == "knowledgebases") {
			$post_type = $post->post_type;
			$tax = wpqa_get_tax_name($post_type);
			$get_category = wp_get_post_terms($post->ID,$tax,array("fields" => "ids"));
			if (isset($get_category[0]) && $get_category[0] != "") {
				$category_single_id = $get_category[0];
			}
			if (isset($category_single_id)) {
				$setting_single = get_term_meta($category_single_id,prefix_terms.'setting_single',true);
				if ($setting_single == "on") {
					$color_skin      = get_term_meta($category_single_id,prefix_terms.'cat_skin',true);
					$color_skin      = ($color_skin != ""?$color_skin:"default");
					$primary_color_p = get_term_meta($category_single_id,prefix_terms.'cat_primary_color',true);
					$background_type = get_term_meta($category_single_id,prefix_terms.'cat_background_type',true);
					if ($background_type == "custom_background" || $background_type == "patterns") {
						$background_pattern  = get_term_meta($category_single_id,prefix_terms.'cat_background_pattern',true);
						$custom_background   = get_term_meta($category_single_id,prefix_terms.'cat_custom_background',true);
						$background_img      = (isset($custom_background["image"])?$custom_background["image"]:"");
						$background_color    = ($background_type == "patterns"?get_term_meta($category_single_id,prefix_terms.'cat_background_color',true):(isset($custom_background["color"])?$custom_background["color"]:""));
						$background_repeat   = (isset($custom_background["repeat"])?$custom_background["repeat"]:"");
						$background_fixed    = (isset($custom_background["attachment"])?$custom_background["attachment"]:"");
						$background_position = (isset($custom_background["position"])?$custom_background["position"]:"");
						$background_full     = get_term_meta($category_single_id,prefix_terms.'cat_full_screen_background',true);
					}
				}
			}
		}
	}
	
	if ($background_type != "default" && $background_type != "") {
		$custom_css .= wpqa_backgrounds($custom_background,$background_type,$background_pattern,$background_color,$background_full);
	}else {
		$custom_css .= wpqa_backgrounds(wpqa_options("custom_background"),wpqa_options("background_type"),wpqa_options("background_pattern"),wpqa_options("background_color"),wpqa_options("full_screen_background"));
	}
	
	if ((($specific_page == "search" && $search_no_default == "") || ($specific_page != "" && $specific_page != "search")) && isset($primary_color_s) && $primary_color_s != "") {
		$wpqa_all_css_color = $primary_color_s;
	}else if ((($specific_page == "search" && $search_no_default == "") || ($specific_page != "" && $specific_page != "search")) && isset($primary_color_s) && $primary_color_s == "") {
		if ($color_skin != "default" && $color_skin != "") {
			$color_skin_function = $color_skin;
		}else {
			$primary_color = wpqa_options("primary_color");
			if ($primary_color != "") {
				$wpqa_all_css_color = $primary_color;
			}
		}
	}else if ((is_author() || wpqa_is_user_profile()) && isset($primary_color_a) && $primary_color_a != "") {
		$wpqa_all_css_color = $primary_color_a;
	}else if ((is_author() || wpqa_is_user_profile()) && isset($primary_color_a) && $primary_color_a == "") {
		if ($color_skin != "default" && $color_skin != "") {
			$color_skin_function = $color_skin;
		}else {
			$primary_color = wpqa_options("primary_color");
			if ($primary_color != "") {
				$wpqa_all_css_color = $primary_color;
			}
		}
	}else if ((is_category() || is_tag() || (is_archive() && !is_post_type_archive(wpqa_questions_type) && !is_post_type_archive(wpqa_knowledgebase_type) && !is_post_type_archive("group")) || is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true || $tax_archive == true || is_tax(wpqa_question_tags) || is_post_type_archive(wpqa_questions_type) || $search_type == "questions" || is_tax(wpqa_knowledgebase_tags) || is_post_type_archive(wpqa_knowledgebase_type) || $search_type == "knowledgebases" || is_post_type_archive("group") || $search_type == "groups" || $wpqa_group_id > 0) && isset($primary_color_c) && $primary_color_c == "") {
		if ($cat_skin != "default" && $cat_skin != "") {
			$color_skin_function = $cat_skin;
		}else {
			$primary_color = wpqa_options("primary_color");
			if ($primary_color != "") {
				$wpqa_all_css_color = $primary_color;
			}
		}
	}else if ((is_category() || is_tag() || (is_archive() && !is_post_type_archive(wpqa_questions_type) && !is_post_type_archive(wpqa_knowledgebase_type) && !is_post_type_archive("group")) || is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true || $tax_archive == true || is_tax(wpqa_question_tags) || is_post_type_archive(wpqa_questions_type) || $search_type == "questions" || is_tax(wpqa_knowledgebase_tags) || is_post_type_archive(wpqa_knowledgebase_type) || $search_type == "knowledgebases" || is_post_type_archive("group") || $search_type == "groups" || $wpqa_group_id > 0) && isset($primary_color_c) && $primary_color_c != "") {
		$wpqa_all_css_color = $primary_color_c;
	}else if ((is_single() || $search_type == "posts" || $search_type == "questions" || $search_type == "knowledgebases" || $search_type == "groups" || is_page()) && isset($primary_color_p) && $primary_color_p == "") {
		if ($color_skin != "default" && $color_skin != "") {
			$color_skin_function = $color_skin;
		}else {
			$primary_color = wpqa_options("primary_color");
			if ($primary_color != "") {
				$wpqa_all_css_color = $primary_color;
			}
		}
	}else if ((is_single() || $search_type == "posts" || $search_type == "questions" || $search_type == "knowledgebases" || $search_type == "groups" || is_page()) && isset($primary_color_p) && $primary_color_p != "") {
		$wpqa_all_css_color = $primary_color_p;
	}else {
		$primary_color = wpqa_options("primary_color");
		if ($primary_color != "") {
			$wpqa_all_css_color = $primary_color;
		}
	}

	$skin_switcher = wpqa_options("skin_switcher");
	if ($skin_switcher == "on") {
		$custom_dark_color = wpqa_options("custom_dark_color");
		if ($custom_dark_color == "on") {
			$dark_color = wpqa_options("dark_color");
			if ($dark_color != "") {
				$custom_css .= wpqa_all_css_color($dark_color,".dark-skin ");
			}
		}
	}

	if (isset($wpqa_all_css_color) && $wpqa_all_css_color != "") {
		$custom_css .= wpqa_all_css_color($wpqa_all_css_color);
		if (isset($dark_color) && $dark_color != "") {
			$custom_css .= wpqa_all_css_color($dark_color," .dark-skin ");
		}
	}else if (isset($color_skin_function)) {
		wpqa_skin($color_skin_function);
	}
	
	$site_users_only = wpqa_site_users_only();
	$under_construction = wpqa_under_construction();
	if (is_page()) {
		$wp_page_template = get_post_meta($post->ID,"_wp_page_template",true);
	}
	if ($under_construction == "on") {
		$register_background = wpqa_options("construction_background");
	}else if (isset($wp_page_template) && $wp_page_template == "template-landing.php") {
		$register_background = get_post_meta($post->ID,prefix_meta.'register_background',true);
	}else {
		$register_background = wpqa_options("register_background");
	}
	if (($site_users_only == "yes" || $under_construction == "on" || (isset($wp_page_template) && $wp_page_template == "template-landing.php")) && !empty($register_background)) {
		if ($under_construction == "on") {
			$register_opacity = (int)get_post_meta($post->ID,prefix_meta.'construction_opacity',true);
		}else if (isset($wp_page_template) && $wp_page_template == "template-landing.php") {
			$register_opacity = (int)get_post_meta($post->ID,prefix_meta.'register_opacity',true);
		}else {
			$register_opacity = (int)wpqa_options("register_opacity");
		}
		$register_background_color = (isset($register_background["color"])?$register_background["color"]:"");
		$register_background_image = $register_background["image"];
		if ((!empty($register_background_image) && wpqa_image_url_id($register_background_image) != "") || $register_background_color != "") {
			$custom_css .= '.login-page-cover,.dark-skin .login-page-cover {';
				if ($register_background_color != "") {
					$custom_css .= 'background-color: '.esc_attr($register_background_color).';';
				}
				if (!empty($register_background_image) && wpqa_image_url_id($register_background_image) != "") {
					$custom_css .= 'background-image: url("'.esc_attr(wpqa_image_url_id($register_background_image)).'") ;
					filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'.esc_attr(wpqa_image_url_id($register_background_image)).'",sizingMethod="scale");
					-ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.esc_attr(wpqa_image_url_id($register_background_image)).'\',sizingMethod=\'scale\')";';
				}
			$custom_css .= '}';
		}
		if ($register_background_color != '') {
			$custom_css .= '.login-opacity,.dark-skin .login-opacity,.bg-overlay:before {
				background-color: '.esc_attr($register_background_color).';';
				if ($register_opacity != '') {
					$custom_css .= '-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=esc_attr($register_opacity))";
					filter: alpha(opacity=esc_attr($register_opacity));
					-moz-opacity: '.esc_attr($register_opacity/100).';
					-khtml-opacity: '.esc_attr($register_opacity/100).';
					opacity: '.esc_attr($register_opacity/100).';';
				}
			$custom_css .= '}';
		}
	}

	if (isset($is_page_single) && isset($custom_call_action) && $custom_call_action == "on") {
		$action_image_video = get_post_meta($post->ID,prefix_meta.'action_image_video',true);
		$action_background = get_post_meta($post->ID,prefix_meta.'action_background',true);
		$action_logged = get_post_meta($post->ID,prefix_meta.'action_logged',true);
	}else {
		$action_image_video = wpqa_options("action_image_video");
		$action_background = wpqa_options("action_background");
		$action_logged = wpqa_options("action_logged");
	}

	if ($action_image_video != "video" && !empty($action_background) && ((is_user_logged_in() && ($action_logged == "logged" || $action_logged == "both")) || (!is_user_logged_in() && ($action_logged == "unlogged" || $action_logged == "both")))) {
		$action_opacity = (int)wpqa_options("action_opacity");
		$action_background_color = (isset($action_background["color"])?$action_background["color"]:"");
		$action_background_image = $action_background["image"];
		if ((!empty($action_background_image) && wpqa_image_url_id($action_background_image) != "") || $action_background_color != "") {
			$custom_css .= '.call-action-unlogged,.dark-skin .call-action-unlogged {';
				if ($action_background_color != "") {
					$custom_css .= 'background-color: '.esc_attr($action_background_color).' !important;';
				}
				if (!empty($action_background_image) && wpqa_image_url_id($action_background_image) != "") {
					$custom_css .= 'background-image: url("'.esc_attr(wpqa_image_url_id($action_background_image)).'") ;
					filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'.esc_attr(wpqa_image_url_id($action_background_image)).'",sizingMethod="scale");
					-ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.esc_attr(wpqa_image_url_id($action_background_image)).'\',sizingMethod=\'scale\')";
					background-size: cover;';
				}
			$custom_css .= '}';
		}
		if ($action_background_color != '') {
			$custom_css .= '.call-action-opacity,.dark-skin .call-action-opacity {
				'.($action_background_color != ''?'':'').'background-color: '.esc_attr($action_background_color).';';
				if ($action_opacity != '') {
					$custom_css .= '-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=esc_attr($action_opacity))";
					filter: alpha(opacity=esc_attr($action_opacity));
					-moz-opacity: '.esc_attr($action_opacity/100).';
					-khtml-opacity: '.esc_attr($action_opacity/100).';
					opacity: '.esc_attr($action_opacity/100).';';
				}
			$custom_css .= '}';
		}
	}

	if (isset($is_page_single) && isset($custom_sliders) && $custom_sliders == "on") {
		$slider_h_logged = apply_filters(wpqa_prefix_theme."_slider_logged",get_post_meta($post->ID,prefix_meta.'slider_h_logged',true));
	}else {
		$slider_h_logged = apply_filters(wpqa_prefix_theme."_slider_logged",wpqa_options("slider_h_logged"));
	}
	if ((is_user_logged_in() && ($slider_h_logged == "logged" || $slider_h_logged == "both")) || (!is_user_logged_in() && ($slider_h_logged == "unlogged" || $slider_h_logged == "both"))) {
		if (isset($is_page_single)) {
			$custom_sliders = get_post_meta($post->ID,prefix_meta.'custom_sliders',true);
		}
		if (isset($is_page_single) && isset($custom_sliders) && $custom_sliders == "on") {
			$slider_h = apply_filters(wpqa_prefix_theme."_slider",get_post_meta($post->ID,prefix_meta.'slider_h',true));
		}else {
			$slider_h = apply_filters(wpqa_prefix_theme."_slider",wpqa_options("slider_h"));
			$slider_h_home_pages = apply_filters(wpqa_prefix_theme."_slider_h_home_pages",wpqa_options("slider_h_home_pages"));
			$slider_h_pages = apply_filters(wpqa_prefix_theme."_slider_home_pages",wpqa_options("slider_h_pages"));
			$slider_h_pages = explode(",",$slider_h_pages);
		}
		if ($slider_h == "on" && ((isset($custom_sliders) && $custom_sliders == "on") || (((is_front_page() || is_home()) && $slider_h_home_pages == "home_page") || $slider_h_home_pages == "all_pages" || ($slider_h_home_pages == "all_posts" && is_singular("post")) || ($slider_h_home_pages == "all_questions" && (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type))) || ($slider_h_home_pages == "all_knowledgebases" && (is_singular(wpqa_knowledgebase_type) || is_singular(wpqa_asked_questions_type))) || ($slider_h_home_pages == "custom_pages" && is_page() && isset($slider_h_pages) && is_array($slider_h_pages) && isset($post->ID) && in_array($post->ID,$slider_h_pages))))) {
			if (isset($is_page_single) && isset($custom_sliders) && $custom_sliders == "on") {
				$custom_slider = get_post_meta($post->ID,prefix_meta.'custom_slider',true);
			}else {
				$custom_slider = wpqa_options("custom_slider");
			}
			if ($custom_slider != "custom") {
				if (isset($is_page_single) && isset($custom_sliders) && $custom_sliders == "on") {
					$slider_height = apply_filters(wpqa_prefix_theme."_slider_height",get_post_meta($post->ID,prefix_meta.'slider_height',true));
				}else {
					$slider_height = apply_filters(wpqa_prefix_theme."_slider_height",wpqa_options("slider_height"));
				}
				$slider_height = (int)$slider_height;
				$custom_css .= '.slider-wrap,.slider-inner {
					min-height: '.$slider_height.'px;
				}';
				if (isset($is_page_single) && isset($custom_sliders) && $custom_sliders == "on") {
					$add_slides = apply_filters(wpqa_prefix_theme."_sliders",get_post_meta($post->ID,prefix_meta.'add_slides',true));
				}else {
					$add_slides = apply_filters(wpqa_prefix_theme."_sliders",wpqa_options("add_slides"));
				}
				if (is_array($add_slides) && !empty($add_slides)) {
					foreach ($add_slides as $key => $value) {
						$color   = (isset($value["color"])?$value["color"]:"");
						$image   = (isset($value["image"])?$value["image"]:"");
						$opacity = (isset($value["opacity"])?(int)$value["opacity"]:"");
						if ($color != '' || (!empty($image) && isset($image["id"]))) {
							$custom_css .= '.slider-item-'.$key.' .slider-inner,.dark-skin .slider-item-'.$key.' .slider-inner {
								'.($color != ''?'background-color: '.esc_attr($color).';':'').
								(wpqa_image_url_id($image) != ''?'background-image: url('.wpqa_image_url_id($image).');':'').
							'}';
						}
						if ($color != '' && $opacity != '' && $opacity > 0) {
							$custom_css .= '.slider-item-'.$key.' .slider-opacity {
								'.($color != ''?'background-color: '.esc_attr($color).';':'');
								if ($opacity != '') {
									$custom_css .= '-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=esc_attr($opacity))";
									filter: alpha(opacity=esc_attr($opacity));
									-moz-opacity: '.esc_attr($opacity/100).';
									-khtml-opacity: '.esc_attr($opacity/100).';
									opacity: '.esc_attr($opacity/100).';';
								}
							$custom_css .= '}';
						}
					}
				}
			}
		}
	}

	$cover_image = wpqa_options("cover_image");
	if ($cover_image == "on" && wpqa_is_user_profile() && !wpqa_is_user_edit_profile()) {
		$user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
		$cover_link = wpqa_get_user_cover_link(array("user_id" => $user_id));
		if ($cover_link != "") {
			$custom_css .= '.wpqa-cover-background,.dark-skin .wpqa-cover-background {background-image: url('.$cover_link.');}';
		}
	}

	$show_category_cover = apply_filters("wpqa_show_category_cover",false);
	if (is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $show_category_cover != "") {
		$custom_cat_cover = get_term_meta($category_id,prefix_terms."custom_cat_cover",true);
		if ($custom_cat_cover == "on") {
			$cat_cover = get_term_meta($category_id,prefix_terms."cat_cover",true);
		}else {
			$cat_cover = wpqa_options("active_cover_category".(is_tax(wpqa_knowledgebase_categories)?"_kb":""));
		}
		if ($cat_cover == "on") {
			$category_color = get_term_meta($category_id,prefix_terms."category_color",true);
			$cover_link = wpqa_get_cat_cover_link(array("tax_id" => $category_id,"cat_name" => esc_html(get_query_var('wpqa_term_name')),"cat_tax" => (is_tax(wpqa_knowledgebase_categories)?wpqa_knowledgebase_categories:wpqa_question_categories)));
			if ($cover_link != "") {
				$custom_css .= '.wpqa-cover-background,.dark-skin .wpqa-cover-background {background-image: url('.$cover_link.');}';
			}
			if ($category_color != "") {
				$custom_css .= '.cover-cat-span {background-color: '.$category_color.'}';
			}
		}
	}

	if (wpqa_is_edit_groups() || is_singular("group") || wpqa_is_group_requests() || wpqa_is_group_users() || wpqa_is_group_admins() || wpqa_is_blocked_users() || wpqa_is_posts_group() || wpqa_is_view_posts_group() || wpqa_is_edit_posts_group()) {
		$wpqa_group_id = wpqa_group_id();
		$group_cover = get_post_meta($wpqa_group_id,'group_cover',true);
		if (($group_cover && !is_array($group_cover)) || (is_array($group_cover) && isset($group_cover["id"]) && $group_cover["id"] != 0)) {
			$group_cover_img = wpqa_get_cover_url($group_cover,"","");
		}
		if (isset($group_cover_img) && $group_cover_img != "") {
			$custom_css .= '.group_cover,.dark-skin .group_cover {background-image: url('.$group_cover_img.');}';
		}
	}
	
	if (!is_user_logged_in()) {
		$login_style = wpqa_options("login_style");
		if ($login_style == "style_2") {
			$login_image = wpqa_image_url_id(wpqa_options("login_image"));
			if ($login_image != "") {
				$custom_css .= '#login-panel .panel-image-content,.dark-skin #login-panel .panel-image-content {background-image: url('.$login_image.');}';
			}
		}

		$signup_style = wpqa_options("signup_style");
		if ($signup_style == "style_2") {
			$signup_image = wpqa_image_url_id(wpqa_options("signup_image"));
			if ($signup_image != "") {
				$custom_css .= '#signup-panel .panel-image-content,.dark-skin #signup-panel .panel-image-content {background-image: url('.$signup_image.');}';
			}
		}

		$pass_style = wpqa_options("pass_style");
		if ($pass_style == "style_2") {
			$pass_image = wpqa_image_url_id(wpqa_options("pass_image"));
			if ($pass_image != "") {
				$custom_css .= '#lost-password .panel-image-content,.dark-skin #lost-password .panel-image-content {background-image: url('.$pass_image.');}';
			}
		}
	}
	
	/* General typography */
	
	$custom_css .= wpqa_general_typography("general_typography","body,p");
	$custom_css .= wpqa_general_color('general_link_color','a','color');
	
	for ($i = 1; $i <= 6; $i++) {
		$custom_css .= wpqa_general_typography("h".$i,"h".$i);
	}
	
	/* Post type */

	if (is_singular("post")) {
		$wpqa_quote_color = get_post_meta($post->ID,prefix_meta.'quote_color',true);
		$wpqa_quote_icon_color = get_post_meta($post->ID,prefix_meta.'quote_icon_color',true);
		$quote_icon_color = (isset($wpqa_quote_icon_color) && $wpqa_quote_icon_color != ""?"style='color:".$wpqa_quote_icon_color.";'":(isset($post_head_background) && $post_head_background != "" && empty($post_head_background_img)?"style='color:#FFF;'":""));
		$wpqa_link_icon_color = get_post_meta($post->ID,prefix_meta.'link_icon_color',true);
		$link_icon_color = (isset($wpqa_link_icon_color) && $wpqa_link_icon_color != ""?"style='color:".$wpqa_link_icon_color.";'":(isset($post_head_background) && $post_head_background != "" && empty($post_head_background_img)?"style='color:#FFF;'":""));
		$wpqa_link_icon_hover_color = get_post_meta($post->ID,prefix_meta.'link_icon_hover_color',true);
		$wpqa_link_hover_color = get_post_meta($post->ID,prefix_meta.'link_hover_color',true);
	
		$custom_css .= wpqa_css_post_type("quote",$wpqa_quote_color,$quote_icon_color,$post->ID);
		$custom_css .= wpqa_css_post_type("link","","",$post->ID,$link_icon_color,$wpqa_link_icon_hover_color,$wpqa_link_hover_color);
	}
	
	/* Hide footer on mobile */

	$custom_css .= '@media only screen and (max-width: 600px) {.hide-mobile-top-footer .top-footer,.hide-mobile-bottom-footer .bottom-footer,.hide-mobile-sidebar .warp-sidebar,.hide-mobile-sidebar .sidebar,.hide-mobile-sidebar .hide-sidebar{display: none}}';
	
	/* Custom CSS */
	
	if (isset($is_page_single)) {
		$footer_css = get_post_meta($post->ID,prefix_meta.'footer_css',true);
		if ($footer_css != "") {
			$custom_css .= stripslashes(wpqa_esc_textarea($footer_css));
		}
	}
	return $custom_css;
}
/* WP head */
add_action('wp_head','wpqa_head');
if (!function_exists('wpqa_head')) {
	function wpqa_head() {
		/* Seo */
		$the_keywords = wpqa_options("the_keywords");
		$seo_active   = wpqa_options("seo_active");
		$seo_active_filter = apply_filters(wpqa_prefix_theme."_filter_seo_active",true);
		if ($seo_active == "on" && $seo_active_filter == true) {
			$tax_filter   = apply_filters(wpqa_prefix_theme."_before_question_category",false);
			$wpqa_group_id = (wpqa_group_id() > 0?wpqa_group_id():"");
			echo '<meta property="og:site_name" content="'.htmlspecialchars(get_bloginfo('name')).'">'."\n";
			echo '<meta property="og:type" content="website">'."\n";
			
			if (!is_home() && !is_front_page() && (is_single() || is_page())) {
				global $post;
				$get_post = get_post($post->ID);
				$title = $get_post->post_title;
				$php_version = explode('.', phpversion());
				if (count($php_version) && $php_version[0] >= 5) {
					$title = html_entity_decode($title,ENT_QUOTES,'UTF-8');
				}else {
					$title = html_entity_decode($title,ENT_QUOTES);
				}
				$description = wpqa_excerpt(40,wpqa_excerpt_type,false,"return","yes",$get_post->post_content);
				$og_title = htmlspecialchars($title);
				$og_url = get_permalink($post->ID);
				$og_description = htmlspecialchars($description);
				$og_image = wpqa_image_for_share();
				if (is_singular(wpqa_questions_type) || is_singular(wpqa_knowledgebase_type)) {
					$post_type = $post->post_type;
					$tax = wpqa_get_tax_name($post_type,'tag');
					if ($terms = wp_get_object_terms($post->ID,$tax)) {
						$the_tags_post = '';
						$terms_array = array();
						foreach ($terms as $term) :
							$the_tags_post .= $term->name . ',';
						endforeach;
						$og_keywords = trim($the_tags_post,',');
					}
				}else {
					$posttags = get_the_tags($post->ID);
					if ($posttags) {
						$the_tags_post = '';
						foreach ($posttags as $tag) {
							$the_tags_post .= $tag->name . ',';
						}
						$og_keywords = trim($the_tags_post,',');
					}
				}
			}else if ($wpqa_group_id > 0) {
				$og_title = get_the_title($wpqa_group_id);
				$og_url = get_permalink($wpqa_group_id);
				$group_cover_activate = "on";
				if ($group_cover_activate == "on") {
					$wpqa_group_id = (wpqa_group_id() > 0?wpqa_group_id():"");
					$group_cover = get_post_meta($wpqa_group_id,"group_cover",true);
					if (($group_cover && !is_array($group_cover)) || (is_array($group_cover) && isset($group_cover["id"]) && $group_cover["id"] != 0)) {
						$group_cover_img = wpqa_get_cover_url($group_cover,"","");
					}
					if (isset($group_cover_img) && $group_cover_img != "") {
						$og_image = ($group_cover_img != ""?$group_cover_img:"");
					}
				}
			}else if (is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true) {
				$tax_id = (int)get_query_var("wpqa_term_id");
				$og_title = esc_html(get_query_var("wpqa_term_name"));
				$og_url = get_term_link($tax_id,(is_tax(wpqa_question_categories)?wpqa_question_categories:wpqa_knowledgebase_categories));
				$custom_cat_cover = get_term_meta($tax_id,prefix_terms."custom_cat_cover",true);
				if ($custom_cat_cover == "on") {
					$cat_cover = get_term_meta($tax_id,prefix_terms."cat_cover",true);
				}else {
					$cat_cover = wpqa_options("active_cover_category".(is_tax(wpqa_knowledgebase_categories)?"_kb":""));
				}
				if ($cat_cover == "on") {
					$cover_link = wpqa_get_cat_cover_link(array("tax_id" => $tax_id,"cat_name" => esc_html(get_query_var('wpqa_term_name')),"cat_tax" => (is_tax(wpqa_knowledgebase_categories)?wpqa_knowledgebase_categories:wpqa_question_categories)));
					$og_image = ($cover_link != ""?$cover_link:"");
				}
			}else if (wpqa_is_user_profile() && !wpqa_is_user_edit_profile()) {
				$user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
				$display_name = get_the_author_meta('display_name',$user_id);
				$og_title = ($display_name != ""?$display_name:"");
				$og_url = wpqa_profile_url($user_id);
				$cover_image = wpqa_options("cover_image");
				if ($cover_image == "on") {
					$cover_link = wpqa_get_user_cover_link(array("user_id" => $user_id,"user_name" => get_the_author_meta('display_name',$user_id)));
					$og_image = ($cover_link != ""?$cover_link:"");
				}
				$meta_description = get_user_meta($user_id,"description",true);
				if ($meta_description != "") {
					$og_description = htmlspecialchars(strip_tags($meta_description));
				}
			}else {
				$og_title = get_bloginfo('name');
				$og_url = esc_url(home_url('/'));
				$og_description = get_bloginfo('description');
				$og_keywords = wpqa_kses_stip($the_keywords);
			}
			if (is_category() || is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true) {
				$category_desc = category_description();
				if ($category_desc != "") {
					$og_description = htmlspecialchars(strip_tags($category_desc));
				}
			}else if (is_tag() || is_tax(wpqa_question_tags) || is_tax(wpqa_knowledgebase_tags)) {
				$tag_desc = tag_description();
				if ($tag_desc != "") {
					$og_description = htmlspecialchars(strip_tags($tag_desc));
				}
			}
			if (isset($og_title) && $og_title != "") {
				echo '<meta property="og:title" content="'.$og_title.'">'."\n";
				echo '<meta name="twitter:title" content="'.$og_title.'">'."\n";
			}
			if (isset($og_description) && $og_description != "") {
				echo '<meta name="description" content="'.$og_description.'">'."\n";
				echo '<meta property="og:description" content="'.$og_description.'">'."\n";
				echo '<meta name="twitter:description" content="'.$og_description.'">'."\n";
			}
			if (isset($og_keywords) && $og_keywords != "") {
				echo "<meta name='keywords' content='".wpqa_kses_stip($og_keywords)."'>" ."\n";
			}
			if (isset($og_url) && $og_url != "" && is_string($og_url)) {
				echo '<meta property="og:url" content="'.$og_url.'">'."\n";
			}
			if (!isset($og_image) || (isset($og_image) && $og_image == "")) {
				$fb_share_image = wpqa_image_url_id(wpqa_options("fb_share_image"));
				$last_og_image = (!empty($fb_share_image)?$fb_share_image:"");
				$last_og_image = apply_filters(wpqa_prefix_theme."_filter_og_image",$last_og_image);
				$og_image = ($last_og_image != ""?$last_og_image:"");
			}
			$og_image = apply_filters(wpqa_prefix_theme."_og_image",$og_image);
			if (isset($og_image) && $og_image != "") {
				echo '<meta property="og:image" content="'.$og_image.'">' . "\n";
				echo '<meta name="twitter:image" content="'.$og_image.'">' . "\n";
			}
		}
		
		/* head_code */
		$head_code = wpqa_options("head_code");
		if ($head_code != "") {
			echo stripslashes($head_code);
		}
	}
}
/* Footer code */
if (!function_exists('wpqa_footer')) {
	function wpqa_footer() {
		$footer_code = wpqa_options("footer_code");
		if ($footer_code != "") {
			echo stripslashes($footer_code);
		}
	}
}
add_action('wp_footer','wpqa_footer');
/* All css color */
function wpqa_all_css_color($color_1,$skin = '') {
	if (has_knowly()) {
		$wpqa_all_css_color = '
		'.$skin.'::-moz-selection {
			background: '.esc_attr($color_1).';
		}
		'.$skin.'::selection {
			background: '.esc_attr($color_1).';
		}
		'.$skin.'.background-color,'.$skin.'.breadcrumbs.breadcrumbs_2.breadcrumbs-colored,'.$skin.'.go-up,'.$skin.'.widget_calendar tbody a,'.$skin.'.widget_calendar caption,'.$skin.'.submit-1:hover,'.$skin.'input[type="submit"]:not(.button-default):not(.button-primary):hover,'.$skin.'.post-pagination > span,'.$skin.'.post-pagination > span:hover,'.$skin.'.post-img-lightbox:hover i,'.$skin.'.pop-header,'.$skin.'.fileinputs:hover span,'.$skin.'.progressbar-percent,'.$skin.'.move-poll-li:hover,'.$skin.'.stats-inner li:before,'.$skin.'.ui-datepicker-header,'.$skin.'.ui-datepicker-current-day,'.$skin.'.wpqa-following .user-follower > ul > li.user-following h4 i,'.$skin.'.wpqa-followers .user-follower > ul > li.user-followers h4 i,'.$skin.'.header-colored .header,'.$skin.'.header-simple .header .button-sign-up,'.$skin.'.call-action-unlogged.call-action-colored,'.$skin.'.button-default.slider-button-style_2:hover,'.$skin.'.slider-inner .button-default.slider-button-style_3:hover,'.$skin.'.slider-wrap .owl-controls .owl-buttons > div:hover,'.$skin.'.slider-ask-form:hover input[type="submit"],'.$skin.'.panel-image-opacity,'.$skin.'.panel-image-content .button-default:hover,'.$skin.'.cover-cat-span,'.$skin.'.cat-section-icon,'.$skin.'.slider-feed-wrap .slider-owl .owl-controls .owl-buttons > div:hover,'.$skin.'.group-item .group_avatar img,'.$skin.'.group-item .group_avatar .group_img,'.$skin.'.group_cover .group_cover_content .group_cover_content_first img,'.$skin.'.content_group_item_header img,'.$skin.'.content_group_item_embed a img,'.$skin.'.comment_item img,'.$skin.'.author_group_cover,'.$skin.'.author_group__content ul li a:hover,'.$skin.'.mobile-bar-apps-colored .mobile-bar-content,'.$skin.'.btn.btn__main:before,'.$skin.'.slider-button-style_1,'.$skin.'.hero-button-style_1,'.$skin.'.btn__info,'.$skin.'#scrollTopBtn,'.$skin.'.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable,'.$skin.'.points__form .radio-control .styled-radio:checked + label:after,'.$skin.'.btn__primary,'.$skin.'.post-password-form input[type="submit"],'.$skin.'.wp-block-search .wp-block-search__button,'.$skin.'.widget_search .search-submit,'.$skin.'.kb-after-tags .social-icons li a,'.$skin.'.tags-list li a:hover,'.$skin.'.tags-list li a.active,'.$skin.'.tagcloud a:hover,'.$skin.'.tagcloud a.active,'.$skin.'.wp-block-tag-cloud a:hover,'.$skin.'.article-navs__item:hover i,'.$skin.'.switch-button-checkbox + .switch-button-label:before,'.$skin.'.pricing-plan .subscriptions-plan.li-subscribe-popular span {
			background-color: '.esc_attr($color_1).';
		}
		'.$skin.'a,'.$skin.'.color,'.$skin.'.color.activate-link,'.$skin.'.user-login-click > ul li a:hover,'.$skin.'.nav_menu > ul li a:hover,'.$skin.'.nav_menu > div > ul li a:hover,'.$skin.'.nav_menu > div > div > ul li a:hover,'.$skin.'.user-notifications > div > a:hover,'.$skin.'.post-author,'.$skin.'.post-title a:hover,'.$skin.'.logo-name:hover,'.$skin.'.commentlist ul.comment-reply li a:hover,'.$skin.'.post-content-text a,'.$skin.'blockquote cite,'.$skin.'.category-description > a,'.$skin.'.category-description > div > a,'.$skin.'.active-favorite a i,'.$skin.'.question-link-list li a:hover,'.$skin.'.progressbar-title span,'.$skin.'.bottom-footer a,'.$skin.'.user-data ul li a:hover,'.$skin.'.user-notifications div ul li span.question-title a:hover,'.$skin.'.widget-posts .user-notifications > div > ul li div h3 a:hover,'.$skin.'.related-widget .user-notifications > div > ul li div h3 a:hover,'.$skin.'.widget-posts .user-notifications > div > ul li a:hover,'.$skin.'.related-widget .user-notifications > div > ul li a:hover,'.$skin.'.widget-title-tabs .tabs li a:hover,'.$skin.'.about-text a,'.$skin.'.footer .about-text a,'.$skin.'.answers-tabs-inner li a:hover,'.$skin.'.mobile-aside li a:hover,'.$skin.'.stats-text,'.$skin.'.wpqa-following .user-follower > ul > li.user-following h4,'.$skin.'.wpqa-followers .user-follower > ul > li.user-followers h4,'.$skin.'.nav_menu ul li.current_page_item > a,'.$skin.'.nav_menu ul li.current-menu-item > a,'.$skin.'.nav_menu ul li.active-tab > a,'.$skin.'.nav_menu ul li.current_page_item > a,'.$skin.'.article-question .question-share .post-share > ul li a:hover,'.$skin.'.ask-box-question:hover,'.$skin.'.ask-box-question:hover i,'.$skin.'.wpqa-login-already a,'.$skin.'.question-content-text a:not(.btn),'.$skin.'.discoura nav.nav ul li a:hover,'.$skin.'.discoura nav.nav ul li:hover a,'.$skin.'.discoura nav.nav ul li.current_page_item a,'.$skin.'.discoura nav.nav ul li.current-menu-item a,'.$skin.'nav.nav ul li.wpqa-notifications-nav ul li a,'.$skin.'nav.nav .wpqa-notifications-nav ul li li a:hover,'.$skin.'nav.nav ul li.current_page_item.wpqa-notifications-nav li a,'.$skin.'nav.nav ul li.current-menu-item.wpqa-notifications-nav li a,'.$skin.'.group-item .group_statistics a:hover,'.$skin.'.group-item .group_statistics div:hover,'.$skin.'.footer.footer-light .related-widget .user-notifications > div > ul li div h3 a:hover,'.$skin.'#section-points .notification__question.notification__question-dark:hover,'.$skin.'.community-card .community__links a:hover,'.$skin.'.widget li.tweet-item a,'.$skin.'.questions-list li a:hover,'.$skin.'.alert-outlined.alert-info,'.$skin.'.navbar .sub-menu.notifications-dropdown-menu .him-user .author__name,'.$skin.'.him-user .notification__body .author__name,'.$skin.'li.notifications__item.notification__show-all a:hover,'.$skin.'.panel-pop .go-login,'.$skin.'.panel-pop .go-register,'.$skin.'.post-item .post__title a:hover,'.$skin.'a.post-title:hover,'.$skin.'.post-title a:hover,'.$skin.'.accordion-item .accordion__title:hover,'.$skin.'.accordion-item .accordion__title a:hover,'.$skin.'.footer-form .footer-form__btn:hover,'.$skin.'.commentlist .comment .custom-post-link:hover,'.$skin.'.commentlist li .comment-text a:hover,'.$skin.'.comment .comment-question-title a:hover,'.$skin.'.navigation-content > a:hover,'.$skin.'.article-header > .question-header .post-cat a:hover,'.$skin.'.question-footer .question-vote a:hover,'.$skin.'.group-card .group__name a:hover,'.$skin.'.content_group_item_header .title h3 a:hover,'.$skin.'.article-post-only .post-meta a:hover,'.$skin.'.current_balance strong,'.$skin.'.referrals-card .referrals__banner .referrals__output .copy__btn,'.$skin.'.referral-invitation > div > a,'.$skin.'#wpqa-badge h3,'.$skin.'#wpqa-notification h3,'.$skin.'.widget .author__name a,'.$skin.'.related-knowledgebases.related-post-links li a:hover,'.$skin.'.post-item .post__meta-cat a:hover,'.$skin.'.footer-form .footer-form__btn:hover,'.$skin.'.dark-skin .pop-footer-subscriptions-2 a,'.$skin.'.article-header > .knowledgebase-header .post-cat a:hover,'.$skin.'.knowledgebase-meta-list.knowledgebase-footer .footer-meta > li > i,'.$skin.'.knowledgebase-meta-list.knowledgebase-footer .footer-meta > li > a > i,'.$skin.'.knowledgebase-meta-list.knowledgebase-footer .post-cat > a:hover,'.$skin.'.dark-skin .knowledgebase-meta-list.knowledgebase-footer .byline .post-cat > a:hover,'.$skin.'.pricing-plan .subscriptions-plan ul.features li .fa-check-circle {
			color: '.esc_attr($color_1).';
		}
		'.$skin.'.widget-posts .user-notifications > div > ul li div h3 a:hover,'.$skin.'.related-widget .user-notifications > div > ul li a:hover {
			color: '.esc_attr($color_1).' !important;
		}
		'.$skin.'.loader_html,'.$skin.'.submit-1:hover,'.$skin.'.author-image-span,'.$skin.'.badge-span,'.$skin.'input[type="submit"]:not(.button-default):not(.button-primary):hover,'.$skin.'blockquote,'.$skin.'.loader_2,'.$skin.'.loader_3,'.$skin.'.user_follow_yes,'.$skin.'.user-follow-profile .user_block_yes .small_loader,'.$skin.'.user_follow_3.user_block_yes .small_loader,'.$skin.'.user-follow-profile .user_follow_yes .small_loader,'.$skin.'.user_follow_3.user_block_yes .small_loader,'.$skin.'.wpqa_poll_image img.wpqa_poll_image_select,'.$skin.'.wpqa-delete-image > span,'.$skin.'.slider-feed-wrap .slider-owl .owl-controls .owl-buttons > div:hover,'.$skin.'.discoura nav.nav ul li a:hover,'.$skin.'.discoura nav.nav ul li:hover a,'.$skin.'.discoura nav.nav ul li.current_page_item a,'.$skin.'.discoura nav.nav ul li.current-menu-item a,'.$skin.'.user_follow_3.user_follow_yes .small_loader,'.$skin.'.user_follow_3.user_block_yes .small_loader,'.$skin.'.footer-form .form-control:focus,'.$skin.'.points__form .radio-control .styled-radio:checked + label:before,'.$skin.'.kb-after-tags .social-icons li a,'.$skin.'.rates__list li:hover,'.$skin.'.rates__list li.rates__activated,'.$skin.'.tags-list li a:hover,'.$skin.'.tags-list li a.active,'.$skin.'.tagcloud a:hover,'.$skin.'.tagcloud a.active,'.$skin.'.wp-block-tag-cloud a:hover,'.$skin.'.article-navs__item:hover,'.$skin.'.pricing-plan .subscriptions-plan.li-subscribe-popular {
			border-color: '.esc_attr($color_1).';
		}';
	}else if (has_himer()) {
		$wpqa_all_css_color = '
		'.$skin.'::-moz-selection {
			background: '.esc_attr($color_1).';
		}
		'.$skin.'::selection {
			background: '.esc_attr($color_1).';
		}
		'.$skin.'.background-color,'.$skin.'.breadcrumbs.breadcrumbs_2.breadcrumbs-colored,'.$skin.'.go-up,'.$skin.'.widget_calendar tbody a,'.$skin.'.widget_calendar caption,'.$skin.'.submit-1:hover,'.$skin.'input[type="submit"]:not(.button-default):not(.button-primary):hover,'.$skin.'.post-pagination > span,'.$skin.'.post-pagination > span:hover,'.$skin.'.post-img-lightbox:hover i,'.$skin.'.pop-header,'.$skin.'.fileinputs:hover span,'.$skin.'.progressbar-percent,'.$skin.'.move-poll-li:hover,'.$skin.'.stats-inner li:before,'.$skin.'.ui-datepicker-header,'.$skin.'.ui-datepicker-current-day,'.$skin.'.wpqa-following .user-follower > ul > li.user-following h4 i,'.$skin.'.wpqa-followers .user-follower > ul > li.user-followers h4 i,'.$skin.'.header-colored .header,'.$skin.'.header-simple .header .button-sign-up,'.$skin.'.call-action-unlogged.call-action-colored,'.$skin.'.button-default.slider-button-style_2:hover,'.$skin.'.slider-inner .button-default.slider-button-style_3:hover,'.$skin.'.slider-wrap .owl-controls .owl-buttons > div:hover,'.$skin.'.slider-ask-form:hover input[type="submit"],'.$skin.'.panel-image-opacity,'.$skin.'.panel-image-content .button-default:hover,'.$skin.'.cover-cat-span,'.$skin.'.cat-section-icon,'.$skin.'.slider-feed-wrap .slider-owl .owl-controls .owl-buttons > div:hover,'.$skin.'.group-item .group_avatar img,'.$skin.'.group-item .group_avatar .group_img,'.$skin.'.group_cover .group_cover_content .group_cover_content_first img,'.$skin.'.content_group_item_header img,'.$skin.'.content_group_item_embed a img,'.$skin.'.comment_item img,'.$skin.'.author_group_cover,'.$skin.'.author_group__content ul li a:hover,'.$skin.'.mobile-bar-apps-colored .mobile-bar-content,'.$skin.'.btn.btn__main:before,'.$skin.'.slider-button-style_1,'.$skin.'.hero-button-style_1,'.$skin.'.btn__info,'.$skin.'#scrollTopBtn,'.$skin.'.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable,'.$skin.'.points__form .radio-control .styled-radio:checked + label:after,'.$skin.'.switch-button-checkbox + .switch-button-label:before,'.$skin.'.pricing-plan .subscriptions-plan.li-subscribe-popular span {
			background-color: '.esc_attr($color_1).';
		}
		'.$skin.'.color,'.$skin.'.color.activate-link,'.$skin.'a:hover,'.$skin.'.user-login-click > ul li a:hover,'.$skin.'.nav_menu > ul li a:hover,'.$skin.'.nav_menu > div > ul li a:hover,'.$skin.'.nav_menu > div > div > ul li a:hover,'.$skin.'.user-notifications > div > a:hover,'.$skin.'.post-author,'.$skin.'.post-title a:hover,'.$skin.'.logo-name:hover,'.$skin.'.commentlist ul.comment-reply li a:hover,'.$skin.'.post-content-text a,'.$skin.'blockquote cite,'.$skin.'.category-description > a,'.$skin.'.category-description > div > a,'.$skin.'.active-favorite a i,'.$skin.'.question-link-list li a:hover,'.$skin.'.progressbar-title span,'.$skin.'.bottom-footer a,'.$skin.'.user-data ul li a:hover,'.$skin.'.user-notifications div ul li span.question-title a:hover,'.$skin.'.widget-posts .user-notifications > div > ul li div h3 a:hover,'.$skin.'.related-widget .user-notifications > div > ul li div h3 a:hover,'.$skin.'.widget-posts .user-notifications > div > ul li a:hover,'.$skin.'.related-widget .user-notifications > div > ul li a:hover,'.$skin.'.widget-title-tabs .tabs li a:hover,'.$skin.'.about-text a,'.$skin.'.footer .about-text a,'.$skin.'.answers-tabs-inner li a:hover,'.$skin.'.mobile-aside li a:hover,'.$skin.'.stats-text,'.$skin.'.wpqa-following .user-follower > ul > li.user-following h4,'.$skin.'.wpqa-followers .user-follower > ul > li.user-followers h4,'.$skin.'.nav_menu ul li.current_page_item > a,'.$skin.'.nav_menu ul li.current-menu-item > a,'.$skin.'.nav_menu ul li.active-tab > a,'.$skin.'.nav_menu ul li.current_page_item > a,'.$skin.'.article-question .question-share .post-share > ul li a:hover,'.$skin.'.ask-box-question:hover,'.$skin.'.ask-box-question:hover i,'.$skin.'.wpqa-login-already a,'.$skin.'.question-content-text a:not(.btn),'.$skin.'.discoura nav.nav ul li a:hover,'.$skin.'.discoura nav.nav ul li:hover a,'.$skin.'.discoura nav.nav ul li.current_page_item a,'.$skin.'.discoura nav.nav ul li.current-menu-item a,'.$skin.'nav.nav ul li.wpqa-notifications-nav ul li a,'.$skin.'nav.nav .wpqa-notifications-nav ul li li a:hover,'.$skin.'nav.nav ul li.current_page_item.wpqa-notifications-nav li a,'.$skin.'nav.nav ul li.current-menu-item.wpqa-notifications-nav li a,'.$skin.'.group-item .group_statistics a:hover,'.$skin.'.group-item .group_statistics div:hover,'.$skin.'.footer.footer-light .related-widget .user-notifications > div > ul li div h3 a:hover,'.$skin.'#section-points .notification__question.notification__question-dark:hover,'.$skin.'.community-card .community__links a:hover,'.$skin.'.widget li.tweet-item a,'.$skin.'.questions-list li a:hover,'.$skin.'.alert-outlined.alert-info,'.$skin.'.navbar .sub-menu.notifications-dropdown-menu .him-user .author__name,'.$skin.'.him-user .notification__body .author__name,'.$skin.'li.notifications__item.notification__show-all a:hover,'.$skin.'.panel-pop .go-login,'.$skin.'.panel-pop .go-register,'.$skin.'.post-item .post__title a:hover,'.$skin.'a.post-title:hover,'.$skin.'.post-title a:hover,'.$skin.'.accordion-item .accordion__title:hover,'.$skin.'.accordion-item .accordion__title a:hover,'.$skin.'.footer-form .footer-form__btn:hover,'.$skin.'.commentlist .comment .custom-post-link:hover,'.$skin.'.commentlist li .comment-text a:hover,'.$skin.'.comment .comment-question-title a:hover,'.$skin.'.navigation-content > a:hover,'.$skin.'.article-header > .question-header .post-cat a:hover,'.$skin.'.question-footer .question-vote a:hover,'.$skin.'.group-card .group__name a:hover,'.$skin.'.content_group_item_header .title h3 a:hover,'.$skin.'.article-post-only .post-meta a:hover,'.$skin.'.current_balance strong,'.$skin.'.referrals-card .referrals__banner .referrals__output .copy__btn,'.$skin.'.referral-invitation > div > a,'.$skin.'#wpqa-badge h3,'.$skin.'#wpqa-notification h3,'.$skin.'.widget .author__name a,'.$skin.'.post-item .post__meta-cat a:hover,'.$skin.'.footer-form .footer-form__btn:hover,'.$skin.'.dark-skin .pop-footer-subscriptions-2 a,'.$skin.'.pricing-plan .subscriptions-plan ul.features li .fa-times-circle {
			color: '.esc_attr($color_1).';
		}
		'.$skin.'.widget-posts .user-notifications > div > ul li div h3 a:hover,.related-widget .user-notifications > div > ul li a:hover {
			color: '.esc_attr($color_1).' !important;
		}
		'.$skin.'.loader_html,'.$skin.'.submit-1:hover,'.$skin.'.author-image-span,'.$skin.'.badge-span,'.$skin.'input[type="submit"]:not(.button-default):not(.button-primary):hover,'.$skin.'blockquote,'.$skin.'.loader_2,'.$skin.'.loader_3,'.$skin.'.user_follow_yes,'.$skin.'.user-follow-profile .user_block_yes .small_loader,'.$skin.'.user_follow_3.user_block_yes .small_loader,'.$skin.'.user-follow-profile .user_follow_yes .small_loader,'.$skin.'.user_follow_3.user_block_yes .small_loader,'.$skin.'.wpqa_poll_image img.wpqa_poll_image_select,'.$skin.'.wpqa-delete-image > span,'.$skin.'.slider-feed-wrap .slider-owl .owl-controls .owl-buttons > div:hover,'.$skin.'.discoura nav.nav ul li a:hover,'.$skin.'.discoura nav.nav ul li:hover a,'.$skin.'.discoura nav.nav ul li.current_page_item a,'.$skin.'.discoura nav.nav ul li.current-menu-item a,'.$skin.'.user_follow_3.user_follow_yes .small_loader,'.$skin.'.user_follow_3.user_block_yes .small_loader,'.$skin.'.footer-form .form-control:focus,'.$skin.'.points__form .radio-control .styled-radio:checked + label:before,'.$skin.'.pricing-plan .subscriptions-plan.li-subscribe-popular {
			border-color: '.esc_attr($color_1).';
		}';
	}else if (has_discy()) {
		$wpqa_all_css_color = '
		'.$skin.'::-moz-selection {
			background: '.esc_attr($color_1).';
		}
		'.$skin.'::selection {
			background: '.esc_attr($color_1).';
		}
		'.$skin.'.background-color,'.$skin.'.breadcrumbs.breadcrumbs_2.breadcrumbs-colored,'.$skin.'.button-default,'.$skin.'.button-default-2:hover,'.$skin.'.go-up,'.$skin.'.widget_calendar tbody a,'.$skin.'.widget_calendar caption,'.$skin.'.tagcloud a:hover,'.$skin.'.wp-block-tag-cloud a:hover,'.$skin.'.submit-1:hover,'.$skin.'.widget_search .search-submit:hover,'.$skin.'.user-area .social-ul li a,'.$skin.'.pagination .page-numbers.current,'.$skin.'.page-navigation-before a:hover,'.$skin.'.load-more a:hover,'.$skin.'input[type="submit"]:not(.button-default):not(.button-primary):hover,'.$skin.'.post-pagination > span,'.$skin.'.post-pagination > span:hover,'.$skin.'.post-img-lightbox:hover i,'.$skin.'.pop-header,'.$skin.'.fileinputs:hover span,'.$skin.'a.meta-answer:hover,'.$skin.'.question-navigation a:hover,'.$skin.'.progressbar-percent,'.$skin.'.button-default-3:hover,'.$skin.'.move-poll-li,'.$skin.'li.li-follow-question,'.$skin.'.user_follow_yes,'.$skin.'.user_block_yes,'.$skin.'.social-ul li a:hover,'.$skin.'.user-follow-profile a,'.$skin.'.cat-sections:before,'.$skin.'.stats-inner li:before,'.$skin.'.cat-sections:before,'.$skin.'.ui-datepicker-header,'.$skin.'.ui-datepicker-current-day,'.$skin.'.wpqa-following .user-follower > ul > li.user-following h4 i,'.$skin.'.wpqa-followers .user-follower > ul > li.user-followers h4 i,'.$skin.'.header-colored .header,'.$skin.'.footer-light .social-ul li a,'.$skin.'.header-simple .header .button-sign-up,'.$skin.'.call-action-unlogged.call-action-colored,'.$skin.'.button-default.slider-button-style_2:hover,'.$skin.'.slider-inner .button-default.slider-button-style_3:hover,'.$skin.'.slider-wrap .owl-controls .owl-buttons > div:hover,'.$skin.'.slider-ask-form:hover input[type="submit"],'.$skin.'.panel-image-opacity,'.$skin.'.panel-image-content .button-default:hover,'.$skin.'.cover-cat-span,'.$skin.'.cat-section-icon,'.$skin.'.feed-title i,'.$skin.'.slider-feed-wrap .slider-owl .owl-controls .owl-buttons > div:hover,'.$skin.'.group-item .group_avatar img,'.$skin.'.group-item .group_avatar .group_img,'.$skin.'.group_cover .group_cover_content .group_cover_content_first img,'.$skin.'.content_group_item_header img,'.$skin.'.content_group_item_embed a img,'.$skin.'.comment_item img,'.$skin.'.author_group_cover,'.$skin.'.author_group__content ul li a:hover,'.$skin.'.mobile-bar-apps-colored .mobile-bar-content,'.$skin.'.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable,'.$skin.'.notifications-count,'.$skin.'.switch-button-checkbox + .switch-button-label:before,'.$skin.'.pricing-plan .subscriptions-plan.li-subscribe-popular span {
			background-color: '.esc_attr($color_1).';
		}
		.color,'.$skin.'.color.activate-link,'.$skin.'a:hover,'.$skin.'.user-login-click > ul li a:hover,'.$skin.'.nav_menu > ul li a:hover,'.$skin.'.nav_menu > div > ul li a:hover,'.$skin.'.nav_menu > div > div > ul li a:hover,'.$skin.'.user-notifications > div > a:hover,'.$skin.'.user-notifications > ul li a,'.$skin.'.user-notifications > div > a:hover,'.$skin.'.user-notifications > ul li a,'.$skin.'.post-meta a,'.$skin.'.post-author,'.$skin.'.post-title a:hover,'.$skin.'.logo-name:hover,'.$skin.'.user-area .user-content > .user-inner h4 > a,'.$skin.'.commentlist li.comment .comment-body .comment-text .comment-author a,'.$skin.'.commentlist ul.comment-reply li a:hover,'.$skin.'.commentlist li .comment-text a,'.$skin.'.post-content-text a,'.$skin.'blockquote cite,'.$skin.'.category-description > h4,'.$skin.'.category-description > a,'.$skin.'.pop-footer a,'.$skin.'.question-poll,'.$skin.'.active-favorite a i,'.$skin.'.question-link-list li a:hover,'.$skin.'.question-link-list li a:hover i,'.$skin.'.poll-num span,'.$skin.'.progressbar-title span,'.$skin.'.bottom-footer a,'.$skin.'.user-questions > div > i,'.$skin.'.referral-completed > div > i,'.$skin.'.user-data ul li a:hover,'.$skin.'.user-notifications div ul li span.question-title a:hover,'.$skin.'.widget-posts .user-notifications > div > ul li div h3 a:hover,'.$skin.'.related-widget .user-notifications > div > ul li div h3 a:hover,'.$skin.'.widget-posts .user-notifications > div > ul li a:hover,'.$skin.'.related-widget .user-notifications > div > ul li a:hover,'.$skin.'.widget-title-tabs .tabs li a:hover,'.$skin.'.about-text a,'.$skin.'.footer .about-text a,'.$skin.'.answers-tabs-inner li a:hover,'.$skin.'.mobile-aside li a:hover,'.$skin.'.stats-text,'.$skin.'.wpqa-following .user-follower > ul > li.user-following h4,'.$skin.'.wpqa-followers .user-follower > ul > li.user-followers h4,'.$skin.'.nav_menu ul li.current_page_item > a,'.$skin.'.nav_menu ul li.current-menu-item > a,'.$skin.'.nav_menu ul li.active-tab > a,'.$skin.'.nav_menu ul li.current_page_item > a,'.$skin.'.article-question .question-share .post-share > ul li a:hover,'.$skin.'.ask-box-question:hover,'.$skin.'.ask-box-question:hover i,'.$skin.'.wpqa-login-already a,'.$skin.'.cat_follow_done .button-default-4.follow-cat-button,'.$skin.'.button-default-4.follow-cat-button:hover,'.$skin.'.question-content-text a,'.$skin.'.discoura nav.nav ul li a:hover,'.$skin.'.discoura nav.nav ul li:hover a,'.$skin.'.discoura nav.nav ul li.current_page_item a,'.$skin.'.discoura nav.nav ul li.current-menu-item a,'.$skin.'nav.nav ul li.wpqa-notifications-nav ul li a,'.$skin.'nav.nav .wpqa-notifications-nav ul li li a:hover,'.$skin.'nav.nav ul li.current_page_item.wpqa-notifications-nav li a,'.$skin.'nav.nav ul li.current-menu-item.wpqa-notifications-nav li a,'.$skin.'.group-item .group_statistics a:hover,'.$skin.'.group-item .group_statistics div:hover,'.$skin.'.footer.footer-light .related-widget .user-notifications > div > ul li div h3 a:hover,'.$skin.'.user-notifications > div > ul li a,'.$skin.'.dark-skin .nav_menu > div > ul li.current-menu-item > a,'.$skin.'.dark-skin .nav_menu > div > ul li li.current-menu-item > a,'.$skin.'.dark-skin .nav_menu > div > ul li li > a:hover,'.$skin.'.dark-skin .wpqa_checkbox_span a,'.$skin.'.dark-skin .pop-footer-subscriptions-2 a,'.$skin.'.pricing-plan .subscriptions-plan ul.features li .fa-times-circle {
			color: '.esc_attr($color_1).';
		}
		.loader_html,'.$skin.'.submit-1:hover,'.$skin.'.widget_search .search-submit:hover,'.$skin.'.author-image-span,'.$skin.'.badge-span,'.$skin.'input[type="submit"]:not(.button-default):not(.button-primary):hover,'.$skin.'blockquote,'.$skin.'.question-poll,'.$skin.'.loader_2,'.$skin.'.loader_3,'.$skin.'.question-navigation a:hover,'.$skin.'li.li-follow-question,'.$skin.'.user_follow.user_follow_yes,'.$skin.'.user-follow-profile .user_block_yes .small_loader,'.$skin.'.user_follow_3.user_block_yes .small_loader,'.$skin.'.user-follow-profile .user_follow_yes .small_loader,'.$skin.'.user_follow_3.user_block_yes .small_loader,'.$skin.'.tagcloud a:hover,'.$skin.'.wp-block-tag-cloud a:hover,'.$skin.'.pagination .page-numbers.current,'.$skin.'.wpqa_poll_image img.wpqa_poll_image_select,'.$skin.'.wpqa-delete-image > span,'.$skin.'.cat_follow_done .button-default-4.follow-cat-button,'.$skin.'.button-default-4.follow-cat-button:hover,'.$skin.'.slider-feed-wrap .slider-owl .owl-controls .owl-buttons > div:hover,'.$skin.'.discoura nav.nav ul li a:hover,'.$skin.'.discoura nav.nav ul li:hover a,'.$skin.'.discoura nav.nav ul li.current_page_item a,'.$skin.'.discoura nav.nav ul li.current-menu-item a,'.$skin.'.user_follow_3.user_follow_yes .small_loader,'.$skin.'.user_follow_3.user_block_yes .small_loader,'.$skin.'.pricing-plan .subscriptions-plan.li-subscribe-popular {
			border-color: '.esc_attr($color_1).';
		}';
	}
	return (isset($wpqa_all_css_color)?$wpqa_all_css_color:'');
}?>