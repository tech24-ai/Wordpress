<?php $templates = true;
if (isset($blog_h) && $blog_h == "blog_h") {
	$taxonomy                = 'category';
	$post_type               = 'post';
	$post_number             = wpqa_options("blog_h_post_number");
	$orderby_post            = wpqa_options("orderby_post_h");
	$order_post              = wpqa_options("order_post_h");
	$post_display            = wpqa_options("post_display_h");
	$post_single_category    = wpqa_options("post_single_category_h");
	$post_categories         = wpqa_options("post_categories_h");
	$post_exclude_categories = wpqa_options("post_exclude_categories_h");
	$post_posts              = wpqa_options("post_posts_h");
}else if ((((isset($tab_category) && $tab_category == true) || (isset($tab_tag) && $tab_tag == true))) || (isset($wp_page_template) && $wp_page_template == "template-home.php")) {
	include wpqa_get_template("slugs.php","includes/");
	if (isset($first_one) && $first_one != "") {
		if ((isset($tab_category) && $tab_category == true) || (isset($tab_tag) && $tab_tag == true)) {
			if (isset($category_id) && $category_id > 0) {
				$term = get_term($category_id);
			}
			$is_tag = (isset($term) && isset($term->taxonomy) && $term->taxonomy == wpqa_question_tags?"tag":"category");
			$is_tag_dash = ($is_tag == "tag"?"_tag":"");
			if ($first_one == $recent_questions_slug_2) {
				$specific_date = wpqa_options("date_recent_questions".$is_tag_dash);
			}else if ($first_one == $most_answers_slug_2) {
				$specific_date = wpqa_options("date_most_answered".$is_tag_dash);
			}else if ($first_one == $no_answers_slug_2) {
				$specific_date = wpqa_options("date_no_answers".$is_tag_dash);
			}else if ($first_one == $most_visit_slug_2) {
				$specific_date = wpqa_options("date_most_visited".$is_tag_dash);
			}else if ($first_one == $most_reacted_slug_2) {
				$specific_date = wpqa_options("date_most_reacted".$is_tag_dash);
			}else if ($first_one == $most_vote_slug_2) {
				$specific_date = wpqa_options("date_most_voted".$is_tag_dash);
			}else if ($first_one == $random_slug_2) {
				$specific_date = wpqa_options("date_random_questions".$is_tag_dash);
			}else if ($first_one == $question_new_slug_2) {
				$specific_date = wpqa_options("date_new_questions".$is_tag_dash);
			}else if ($first_one == $question_sticky_slug_2) {
				$specific_date = wpqa_options("date_sticky_questions".$is_tag_dash);
			}else if ($first_one == $question_polls_slug_2) {
				$specific_date = wpqa_options("date_poll_questions".$is_tag_dash);
			}else if ($first_one == $question_followed_slug_2) {
				$specific_date = wpqa_options("date_followed_questions".$is_tag_dash);
			}else if ($first_one == $question_favorites_slug_2) {
				$specific_date = wpqa_options("date_favorites_questions".$is_tag_dash);
			}else if ($first_one == $recent_posts_slug_2) {
				$specific_date = wpqa_options("date_recent_posts".$is_tag_dash);
			}else if ($first_one == $posts_visited_slug_2) {
				$specific_date = wpqa_options("date_posts_visited".$is_tag_dash);
			}else if ($first_one == $question_bump_slug_2) {
				$specific_date = wpqa_options("date_question_bump".$is_tag_dash);
			}else if ($first_one == $answers_slug_2) {
				$specific_date = wpqa_options("date_answers".$is_tag_dash);
			}
			$orderby_answers = wpqa_options("orderby_answers".$is_tag_dash);
		}else {
			if ($first_one == $feed_slug_2) {
				$specific_date = wpqa_post_meta("date_feed");
			}else if ($first_one == $recent_questions_slug_2) {
				$specific_date = wpqa_post_meta("date_recent_questions");
			}else if ($first_one == $questions_for_you_slug_2) {
				$specific_date = wpqa_post_meta("date_questions_for_you");
			}else if ($first_one == $most_answers_slug_2) {
				$specific_date = wpqa_post_meta("date_most_answered");
			}else if ($first_one == $no_answers_slug_2) {
				$specific_date = wpqa_post_meta("date_no_answers");
			}else if ($first_one == $most_visit_slug_2) {
				$specific_date = wpqa_post_meta("date_most_visited");
			}else if ($first_one == $most_reacted_slug_2) {
				$specific_date = wpqa_post_meta("date_most_reacted");
			}else if ($first_one == $most_vote_slug_2) {
				$specific_date = wpqa_post_meta("date_most_voted");
			}else if ($first_one == $random_slug_2) {
				$specific_date = wpqa_post_meta("date_random_questions");
			}else if ($first_one == $question_new_slug_2) {
				$specific_date = wpqa_post_meta("date_new_questions");
			}else if ($first_one == $question_sticky_slug_2) {
				$specific_date = wpqa_post_meta("date_sticky_questions");
			}else if ($first_one == $question_polls_slug_2) {
				$specific_date = wpqa_post_meta("date_poll_questions");
			}else if ($first_one == $question_followed_slug_2) {
				$specific_date = wpqa_post_meta("date_followed_questions");
			}else if ($first_one == $question_favorites_slug_2) {
				$specific_date = wpqa_post_meta("date_favorites_questions");
			}else if ($first_one == $recent_posts_slug_2) {
				$specific_date = wpqa_post_meta("date_recent_posts");
			}else if ($first_one == $posts_visited_slug_2) {
				$specific_date = wpqa_post_meta("date_posts_visited");
			}else if ($first_one == $question_bump_slug_2) {
				$specific_date = wpqa_post_meta("date_question_bump");
			}else if ($first_one == $answers_slug_2) {
				$specific_date = wpqa_post_meta("date_answers");
			}else if ($first_one == $answers_might_like_slug_2) {
				$specific_date = wpqa_post_meta("date_answers_might_like");
			}else if ($first_one == $answers_for_you_slug_2) {
				$specific_date = wpqa_post_meta("date_answers_for_you");
			}else if ($first_one == $poll_feed_slug_2) {
				$specific_date = wpqa_post_meta("date_poll_feed");
			}else if ($first_one == $recent_knowledgebases_slug_2) {
				$specific_date = wpqa_post_meta("date_recent_knowledgebases");
			}else if ($first_one == $random_knowledgebases_slug_2) {
				$specific_date = wpqa_post_meta("date_random_knowledgebases");
			}else if ($first_one == $sticky_knowledgebases_slug_2) {
				$specific_date = wpqa_post_meta("date_sticky_knowledgebases");
			}else if ($first_one == $knowledgebases_visited_slug_2) {
				$specific_date = wpqa_post_meta("date_knowledgebases_visited");
			}else if ($first_one == $knowledgebases_voted_slug_2) {
				$specific_date = wpqa_post_meta("date_knowledgebases_voted");
			}
		}
	}
	if (isset($wp_page_template) && $wp_page_template == "template-home.php") {
		$active_points           = wpqa_options("active_points");
		$question_bump           = wpqa_options("question_bump");
		$post_number             = wpqa_post_meta("posts_per_page");
		$order_post              = wpqa_post_meta("order_page_h");
		$orderby_answers         = wpqa_post_meta("orderby_answers_h");
		$post_display            = wpqa_post_meta("question_display_r");
		$post_single_category    = wpqa_post_meta("question_single_category_r");
		$post_categories         = wpqa_post_meta("question_categories_r");
		$post_exclude_categories = wpqa_post_meta("question_exclude_categories_r");
		$post_posts              = wpqa_post_meta("question_questions_r");
	}else {
		$order_post = "DESC";
	}
	if (isset($first_one) && $first_one != "" && ($first_one == "all" || $first_one == "q-0" || $first_one == $feed_slug || $first_one == $recent_questions_slug || $first_one == $questions_for_you_slug || $first_one == $answers_slug || $first_one == $answers_might_like_slug || $first_one == $answers_for_you_slug || $first_one == $most_answers_slug || $first_one == $no_answers_slug || $first_one == $most_visit_slug || $first_one == $most_reacted_slug || $first_one == $most_vote_slug || $first_one == $random_slug || $first_one == $question_new_slug || $first_one == $question_sticky_slug || $first_one == $question_polls_slug || $first_one == $question_followed_slug || $first_one == $question_favorites_slug || ($question_bump == "on" && $active_points == "on" && ($first_one == $question_bump_slug || $first_one == $question_bump_slug_2)) || $first_one == $poll_feed_slug || $first_one == $feed_slug_2 || $first_one == $recent_questions_slug_2 || $first_one == $questions_for_you_slug_2 || $first_one == $answers_slug_2 || $first_one == $answers_might_like_slug_2 || $first_one == $answers_for_you_slug_2 || $first_one == $most_answers_slug_2 || $first_one == $no_answers_slug_2 || $first_one == $most_visit_slug_2 || $first_one == $most_reacted_slug_2 || $first_one == $most_vote_slug_2 || $first_one == $random_slug_2 || $first_one == $question_new_slug_2 || $first_one == $question_sticky_slug_2 || $first_one == $question_polls_slug_2 || $first_one == $question_followed_slug_2 || $first_one == $question_favorites_slug_2 || $first_one == $poll_feed_slug_2)) {
		$taxonomy  = wpqa_question_categories;
		$post_type = wpqa_questions_type;
	}else if (isset($first_one) && $first_one != "" && ($first_one == "k-0" || $first_one == $recent_knowledgebases_slug || $first_one == $random_knowledgebases_slug || $first_one == $sticky_knowledgebases_slug || $first_one == $knowledgebases_visited_slug || $first_one == $knowledgebases_voted_slug || $first_one == $recent_knowledgebases_slug_2 || $first_one == $random_knowledgebases_slug_2 || $first_one == $sticky_knowledgebases_slug_2 || $first_one == $knowledgebases_visited_slug_2 || $first_one == $knowledgebases_voted_slug_2)) {
		$taxonomy  = wpqa_knowledgebase_categories;
		$post_type = wpqa_knowledgebase_type;
	}else if (isset($first_one) && $first_one != "" && ($first_one == $recent_posts_slug || $first_one == $recent_posts_slug_2 || $first_one == $posts_visited_slug || $first_one == $posts_visited_slug_2)) {
		$taxonomy  = 'category';
		$post_type = apply_filters(wpqa_prefix_theme.'_recent_posts_post_type','post');
	}
}else if (isset($wp_page_template) && $wp_page_template == "template-question.php") {
	$taxonomy                = wpqa_question_categories;
	$post_type               = wpqa_questions_type;
	$orderby_post            = wpqa_post_meta("orderby_question_q");
	$order_post              = wpqa_post_meta("order_question");
	$post_display            = wpqa_post_meta("question_display_q");
	$post_single_category    = wpqa_post_meta("question_single_category_q");
	$post_categories         = wpqa_post_meta("question_categories_q");
	$post_exclude_categories = wpqa_post_meta("question_exclude_categories_q");
	$post_posts              = wpqa_post_meta("question_questions_q");
	$specific_date           = wpqa_post_meta("specific_date_q");
}else if (isset($wp_page_template) && $wp_page_template == "template-comments.php") {
	$comment_type    = wpqa_post_meta("comment_type");
	$orderby_answers = wpqa_post_meta("orderby_answers_a");
	$order_post      = wpqa_post_meta("order_answers");
	$custom_answers  = wpqa_post_meta("custom_answers");
	$specific_date   = wpqa_post_meta("specific_date_c");
	$post_pagination = wpqa_options("question_pagination");
	if ($custom_answers == "on") {
		$post_number     = wpqa_post_meta("answers_number");
		$post_pagination = wpqa_post_meta("answers_pagination");
	}
	if ($comment_type == "comments") {
		$post_type = 'post';
	}else {
		$post_type = wpqa_questions_type;
	}
}else {
	$taxonomy                = 'category';
	$post_type               = 'post';
	$orderby_post            = wpqa_post_meta("orderby_post_b");
	$order_post              = wpqa_post_meta("order_post");
	$post_display            = wpqa_post_meta("post_display_b");
	$post_single_category    = wpqa_post_meta("post_single_category_b");
	$post_categories         = wpqa_post_meta("post_categories_b");
	$post_exclude_categories = wpqa_post_meta("post_exclude_categories_b");
	$post_posts              = wpqa_post_meta("post_posts_b");
	$specific_date           = wpqa_post_meta("specific_date_b");
}

$paged       = wpqa_paged();
$post_number = (int)(isset($post_number) && $post_number != ""?$post_number:get_option("posts_per_page"));

$user_array_question = array($post__not_in);
if (isset($orderby_post)) {
	if ($orderby_post == "popular") {
		$orderby_array = array_merge($user_array_question,array('orderby' => 'comment_count','order' => $order_post));
	}else if ($orderby_post == "random") {
		$orderby_array = array_merge($user_array_question,array('orderby' => 'rand','order' => $order_post));
	}else if ($orderby_post == "most_visited") {
		$post_meta_stats = wpqa_get_meta_stats();
		$orderby_array = array('orderby' => array('post_stats_order' => $order_post),"meta_query" => array('post_stats_order' => array('type' => 'numeric',"key" => $post_meta_stats,"value" => 0,"compare" => ">=")));
	}else if ($orderby_post == "most_rated") {
		$orderby_array = array("orderby" => "meta_value_num",'order' => $order_post,"meta_query" => array(array('type' => 'numeric',"key" => "final_review","value" => 0,"compare" => ">=")));
	}else if ($orderby_post == "most_voted") {
		if ($post_type == wpqa_knowledgebase_type) {
			$orderby_array = array('orderby' => array('knowledgebase_vote_order' => $order_post),"meta_query" => array('knowledgebase_vote_order' => array('type' => 'numeric',"key" => "wpqa_rate_up","value" => 0,"compare" => ">=")));
		}else {
			$orderby_array = array('orderby' => array('question_vote_order' => $order_post),"meta_query" => array('question_vote_order' => array('type' => 'numeric',"key" => "question_vote","value" => 0,"compare" => ">=")));
		}
	}else if ($orderby_post == "most_reacted") {
		$orderby_array = array('orderby' => array('question_reacted_order' => $order_post),"meta_query" => array('question_reacted_order' => array('type' => 'numeric',"key" => "wpqa_reactions_count","value" => 0,"compare" => ">=")));
	}else if ($orderby_post == "no_answer") {
		$orderby_array = array_merge($user_array_question,array("orderby" => array("comment_count" => $order_post,"date" => $order_post)));
	}else if ($question_bump == "on" && $active_points == "on" && $orderby_post == "question_bump") {
		$orderby_array = array('orderby' => array('question_points_order' => $order_post),"meta_query" => array('question_points_order' => array('type' => 'numeric',"key" => "question_points","value" => 0,"compare" => ">=")));
	}else {
		$orderby_array = array_merge($user_array_question,array('order' => $order_post));
	}
}

$theme_sidebar = $theme_sidebar_all = wpqa_post_meta("sidebar");
if ($theme_sidebar == "default") {
	$theme_sidebar = $theme_sidebar_all = wpqa_options("sidebar_layout");
}else {
	$theme_sidebar_all = $theme_sidebar;
}

$categories_a = $exclude_categories_a = array();
if (isset($post_categories) && is_array($post_categories)) {
	$categories_a = $post_categories;
}

if (isset($post_exclude_categories) && is_array($post_exclude_categories)) {
	$exclude_categories_a = $post_exclude_categories;
}

if (isset($post_display)) {
	if ($post_display == "single_category") {
		$custom_catagories_updated = $post_single_category;
		$cats_post = array('tax_query' => array(array('taxonomy' => $taxonomy,'field' => 'id','terms' => $post_single_category,'operator' => 'IN')));
	}else if ($post_display == "categories") {
		$custom_catagories_updated = $categories_a;
		$cats_post = array('tax_query' => array(array('taxonomy' => $taxonomy,'field' => 'id','terms' => $categories_a,'operator' => 'IN')));
	}else if ($post_display == "exclude_categories") {
		$custom_catagories_updated = $exclude_categories_a;
		$cats_post = array('tax_query' => array(array('taxonomy' => $taxonomy,'field' => 'id','terms' => $exclude_categories_a,'operator' => 'NOT IN')));
	}else if ($post_display == "custom_posts") {
		$custom_posts_updated = $post_posts;
		$custom_posts = explode(",",$post_posts);
		$cats_post = array('post__in' => $custom_posts);
	}else {
		$cats_post = array();
	}
}

if (isset($specific_date)) {
	if ($specific_date == "24" || $specific_date == "48" || $specific_date == "72" || $specific_date == "96" || $specific_date == "120" || $specific_date == "144") {
		$specific_date = $specific_date." hours";
	}else if ($specific_date == "week" || $specific_date == "month" || $specific_date == "year") {
		$specific_date = "1 ".$specific_date;
	}
}
$specific_date_array = (isset($specific_date) && $specific_date != "" && $specific_date != "all"?array('date_query' => array(array('after' => $specific_date.' ago'))):array());

if (isset($wp_page_template) && $wp_page_template == "template-question.php" && isset($orderby_post) && ($orderby_post == "recent")) {
	if ($orderby_post == "recent") {
		$active_sticky = true;
		$custom_args   = array_merge($cats_post,$specific_date_array,array("post_type" => wpqa_questions_type));
		$show_sticky   = true;
	}
	$active_sticky = false;
}else if (isset($wp_page_template) && $wp_page_template == "template-knowledgebase.php" && isset($orderby_post) && ($orderby_post == "recent")) {
	if ($orderby_post == "recent") {
		$active_sticky = true;
		$custom_args   = array_merge($cats_post,$specific_date_array,array("post_type" => wpqa_knowledgebase_type));
		$show_sticky   = true;
	}
	$active_sticky = false;
}
$post_display = (isset($post_display)?$post_display:"");
$post_single_category = (isset($post_single_category)?$post_single_category:"");
$post_categories = (isset($post_categories)?$post_categories:"");
$post_exclude_categories = (isset($post_exclude_categories)?$post_exclude_categories:"");
$post_posts = (isset($post_posts)?$post_posts:"");?>