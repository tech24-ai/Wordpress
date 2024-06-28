<?php $its_question = $its_knowledgebase = false;$updated_answers = wpqa_options("updated_answers");
$show_the_content = apply_filters(wpqa_prefix_theme."_show_the_content",true,(isset($wp_page_template)?$wp_page_template:""));
if (empty($blog_h) && ((is_single() || is_page()) && $show_the_content == true && isset($wp_page_template) && $wp_page_template != "" && $wp_page_template != "template-contact.php" && $wp_page_template != "template-faqs.php" && $wp_page_template != "template-landing.php" && $wp_page_template != "template-users.php" && $wp_page_template != "template-tags.php" && $wp_page_template != "template-categories.php")) {
	include wpqa_get_template("the-content.php","theme-parts/");
}
$k_ad_p                   = -1;
$zero_index = $post_count = 0;
$not_fount_error          = true;
$post_id_main             = (isset($post_id_main)?$post_id_main:"");
$pagination_show          = "yes";
$ask_question_to_users    = wpqa_options("ask_question_to_users");
$pay_ask                  = wpqa_options("pay_ask");
$first_one                = (isset($first_one) && $first_one != ""?$first_one:"");
$last_one                 = (isset($last_one) && $last_one != ""?$last_one:"");
$get_user_var             = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
$user_id                  = get_current_user_id();
$is_super_admin           = is_super_admin($user_id);
$question_bump            = wpqa_options("question_bump");
$active_points            = wpqa_options("active_points");
$custom_category          = (((isset($tab_category) && $tab_category == true) || (isset($tab_tag) && $tab_tag == true)) && isset($custom_args)?$custom_args:array());

$block_users = wpqa_options("block_users");
$author__not_in = $author__not_in_comments = array();
if ($block_users == "on" && !wpqa_is_user_profile()) {
	if ($user_id > 0) {
		$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
		if (is_array($get_block_users) && !empty($get_block_users)) {
			$author__not_in = array("author__not_in" => $get_block_users);
			$author__not_in_comments = array("post_author__not_in" => $get_block_users,"author__not_in" => $get_block_users);
		}
	}
}

include wpqa_get_template("slugs.php","includes/");
$loop_page = "loop-setting.php";
if (isset($its_post_type) && ($its_post_type == wpqa_questions_type || $its_post_type == wpqa_asked_questions_type)) {
	$loop_page = "question-setting.php";
}else if (isset($its_post_type) && $its_post_type == wpqa_knowledgebase_type) {
	$loop_page = "knowledgebase-setting.php";
}
if (!isset($loop_setting) || (isset($loop_setting) && $loop_setting != str_replace(".php","",$loop_page))) {
	include wpqa_get_template($loop_page,"includes/");
}

if (empty($blog_h) && ((((isset($tab_category) && $tab_category == true) || (isset($tab_tag) && $tab_tag == true)) && ($first_one == $answers_slug || $first_one == $answers_slug_2))) || (isset($wp_page_template) && (($wp_page_template == "template-home.php" && is_user_logged_in() && isset($first_one) && $first_one != "" && ($first_one == $answers_might_like_slug || $first_one == $answers_for_you_slug || $first_one == $answers_might_like_slug_2 || $first_one == $answers_for_you_slug_2))) || (isset($wp_page_template) && (($wp_page_template == "template-home.php" && isset($first_one) && $first_one != "" && ($first_one == $answers_slug || $first_one == $answers_slug_2 || (!is_user_logged_in() && ($first_one == $answers_might_like_slug || $first_one == $answers_for_you_slug || $first_one == $answers_might_like_slug_2 || $first_one == $answers_for_you_slug_2)))) || $wp_page_template == "template-comments.php")))) {
	if (!isset($templates)) {
		include wpqa_get_template("templates.php","includes/");
	}
	$paged     = wpqa_paged();
	$current   = max(1,$paged);
	$cat_posts = $tag_posts = $posts_array = array();
	if ((((isset($tab_category) && $tab_category == true) || (isset($tab_tag) && $tab_tag == true)) && ($first_one == $answers_slug || $first_one == $answers_slug_2)) || (isset($wp_page_template) && ($wp_page_template == "template-home.php" && is_user_logged_in() && isset($first_one) && $first_one != "" && ($first_one == $answers_might_like_slug || $first_one == $answers_for_you_slug || $first_one == $answers_might_like_slug_2 || $first_one == $answers_for_you_slug_2)))) {
		$rows_per_page = get_option('posts_per_page');
		$offset        = ($paged -1) * $rows_per_page;
		$current       = max(1,$paged);
		if (isset($wp_page_template) && (($wp_page_template == "template-home.php" && isset($first_one) && $first_one != "" && ($first_one == $answers_might_like_slug || $first_one == $answers_might_like_slug_2)))) {
			$user_cat_follow = get_user_meta($user_id,"user_cat_follow",true);
			$category_list = (is_array($user_cat_follow) && !empty($user_cat_follow)?$user_cat_follow:array());
			$user_tag_follow = get_user_meta($user_id,"user_tag_follow",true);
			$tag_list = (is_array($user_tag_follow) && !empty($user_tag_follow)?$user_tag_follow:array());
			$tag_posts = get_objects_in_term($tag_list,wpqa_question_tags);
		}else if (isset($wp_page_template) && (($wp_page_template == "template-home.php" && isset($first_one) && $first_one != "" && ($first_one == $answers_for_you_slug || $first_one == $answers_for_you_slug_2)))) {
			$category_list = get_user_meta($user_id,"wpqa_for_you_cats",true);
			$tag_list = get_user_meta($user_id,"wpqa_for_you_tags",true);
			$tag_posts = get_objects_in_term($tag_list,wpqa_question_tags);
		}else {
			$exclude = apply_filters('wpqa_exclude_question_category',array());
			if ($category_id != "" && $category_id > 0) {
				$term = get_term($category_id);
			}
			$get_tax_name  = (isset($term) && isset($term->taxonomy)?$term->taxonomy:wpqa_question_categories);
			$categories    = get_terms($get_tax_name,array_merge($exclude,array('child_of' => $category_id,'hide_empty' => false)));
			$category_list =  array($category_id);
			foreach ($categories as $term) {
				$category_list[] = (int)$term->term_id;
			}
		}

		$cat_posts = get_objects_in_term($category_list,(isset($get_tax_name)?$get_tax_name:wpqa_question_categories));
		$posts = array_merge($tag_posts,$cat_posts);
		$posts_array = array("post__in" => $posts);
		$count_custom_posts = (isset($posts) && is_array($posts) && !empty($posts)?implode(",",$posts):(isset($posts) && !is_array($posts)?$posts:""));
	}
	
	if ($orderby_answers == 'reacted' && ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type)) {
		$args = array('order' => (isset($order_post)?$order_post:'DESC'),'orderby' => 'meta_value_num','meta_key' => 'wpqa_reactions_count');
	}else if ($orderby_answers == 'votes' && ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type)) {
		$args = array('order' => (isset($order_post)?$order_post:'DESC'),'orderby' => 'meta_value_num','meta_key' => 'comment_vote');
	}else if ($orderby_answers == 'oldest') {
		$args = array('order' => 'ASC','orderby' => 'comment_date');
	}else {
		$args = array('order' => (isset($order_post) && $orderby_answers == 'date'?$order_post:'DESC'),'orderby' => 'comment_date');
	}
	if (!function_exists('wpqa_comments_clauses')) :
		function wpqa_comments_clauses($clauses) {
			global $wpdb;
			$clauses["groupby"] = "{$wpdb->comments}.comment_post_ID";
			return $clauses;
		}
	endif;
	add_filter('comments_clauses','wpqa_comments_clauses');
	$total = wpqa_count_custom_comments($post_type,(isset($specific_date) && $specific_date != ""?$specific_date:""),(isset($count_custom_posts) && $count_custom_posts != ""?$count_custom_posts:""));
	$total_banned = ($block_users == "on" && isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)?wpqa_count_custom_banned_comments($post_type,(isset($specific_date) && $specific_date != ""?$specific_date:""),(isset($count_custom_posts) && $count_custom_posts != ""?$count_custom_posts:""),(isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)?$get_block_users:array())):0);
	$total = $total - $total_banned;
	$max_num_pages = ceil($total/$post_number);
	$offset = ($paged -1) * $post_number;
	$comments_all = (isset($posts) && is_array($posts) && !empty($posts)?get_comments(array_merge($author__not_in_comments,$specific_date_array,$posts_array,$args,array('status' => 'approve','post_type' => $post_type,'number' => $post_number,'offset' => $offset))):array());
	if (!empty($comments_all)) {
		if (isset($post_pagination) && ($post_pagination == "pagination" || $post_pagination == "standard")) {
			$pagination_args = array(
				'total'     => $max_num_pages,
				'current'   => $current,
				'show_all'  => false,
				'prev_text' => ($post_pagination == 'standard'?'<span>'.esc_html__('New Answers',"wpqa").'</span><i class="icon-arrow-right-c"></i>':'<i class="icon-arrow-left-b"></i>'),
				'next_text' => ($post_pagination == 'standard'?'<i class="icon-arrow-left-c"></i><span>'.esc_html__('Old Answers',"wpqa").'</span>':'<i class="icon-arrow-right-b"></i>'),
			);
			if (!get_option('permalink_structure')) {
				$pagination_args['base'] = esc_url_raw(add_query_arg('paged','%#%'));
			}
		}?>
		<div class="page-content commentslist block-section-div">
			<ol class="commentlist clearfix">
				<?php $k_ad  = -1;
				$zero_comment_index = 0;
				foreach ($comments_all as $comment) {
					$k_ad++;
					$yes_private = wpqa_private($comment->comment_post_ID,get_post($comment->comment_post_ID)->post_author,$user_id);
					if ($yes_private == 1) {
							$comment_id = esc_html($comment->comment_ID);
							wpqa_comment($comment,"","",($post_type == "post"?"comment":"answer"),"",$k_ad,"",
								array(
									"answer_question_id" => (isset($post_id_main)?$post_id_main:""),
									"custom_home_answer" => (isset($wp_page_template) && $wp_page_template == "template-home.php" && isset($first_one) && $first_one != "" && ($first_one == $answers_slug || $first_one == $answers_might_like_slug || $first_one == $answers_for_you_slug || $first_one == $answers_slug_2 || $first_one == $answers_might_like_slug_2 || $first_one == $answers_for_you_slug_2)?wpqa_post_meta("custom_home_answer",$post_id_main):""),
									"custom_answers"     => (isset($wp_page_template) && $wp_page_template == "template-comments.php"?wpqa_post_meta("custom_answers",$post_id_main):""),
									"comment_with_title" => true,
									"show_replies"       => false
								)
							);?>
						</li>
					<?php }else {?>
						<li class="comment">
							<div class="comment-body clearfix">
								<?php echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry it is a private answer.","wpqa").'</p></div>';?>
							</div>
						</li>
					<?php }
				}?>
			</ol>
		</div>
	<?php }else {
		echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?esc_html__("There are no answers yet.","wpqa"):esc_html__("There are no comments yet.","wpqa")).'</p></div>';
	}
	if (isset($post_pagination) && ($post_pagination == "pagination" || $post_pagination == "standard") && $comments_all && $pagination_args["total"] > 1) {?>
		<div class="main-pagination"><div class='comment-pagination <?php echo ("standard" == $post_pagination?"standard-pagination page-navigation page-navigation-before":"pagination")?>'><?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?></div></div>
	<?php }else if (isset($post_pagination) && ($post_pagination == "infinite_scroll" || $post_pagination == "load_more")) {
		$it_answer_pagination = true;
		wpqa_load_pagination(array(
			"post_pagination" => (isset($post_pagination)?$post_pagination:"pagination"),
			"max_num_pages" => (isset($max_num_pages)?$max_num_pages:""),
			"it_answer_pagination" => (isset($it_answer_pagination)?$it_answer_pagination:false),
			"its_post_type" => (isset($its_post_type)?$its_post_type:false),
			"wpqa_query" => (isset($query_wp)?$query_wp:null),
			"it_comment_pagination" => (isset($post_type) && $post_type == "post"?true:false),
		));
	}
	remove_filter('comments_clauses','wpqa_comments_clauses');
}else {
	if ((wpqa_is_user_profile()) || (isset($wp_page_template) && $wp_page_template == "template-home.php")) {
		$show_custom_error = true;
	}
	
	$array_data = array();
	
	$tax_filter = apply_filters(wpqa_prefix_theme."_before_question_category",false);
	$tax_question = apply_filters(wpqa_prefix_theme."_question_category",wpqa_question_categories);
	$tax_knowledgebase = apply_filters(wpqa_prefix_theme."_knowledgebase_category",wpqa_knowledgebase_categories);
	if (isset($blog_h) && $blog_h == "blog_h") {
		$blog_h = "blog_h";
		if (!isset($templates)) {
			include wpqa_get_template("templates.php","includes/");
		}
		$array_data = array_merge($orderby_array,(isset($cats_post) && is_array($cats_post)?$cats_post:array()),$specific_date_array,array("posts_per_page" => $post_number,"post_type" => "post"));
	}else if ((((isset($tab_category) && $tab_category == true)) || (isset($tab_tag) && $tab_tag == true)) || (isset($wp_page_template) && ($wp_page_template == "template-question.php" || $wp_page_template == "template-knowledgebase.php" || $wp_page_template == "template-blog.php" || $wp_page_template == "template-home.php"))) {
		if (!isset($templates)) {
			include wpqa_get_template("templates.php","includes/");
		}
		$loop_query = apply_filters(wpqa_prefix_theme."_before_loop_query",false,(isset($first_one) && $first_one != ""?$first_one:false));
		if ($loop_query == true) {
			$array_data    = apply_filters(wpqa_prefix_theme."_loop_array_data",false,(isset($first_one) && $first_one != ""?$first_one:false));
			$active_sticky = $show_sticky = $post_not_true = false;
		}else if (((((isset($tab_category) && $tab_category == true)) || (isset($tab_tag) && $tab_tag == true)) || (isset($wp_page_template) && ($wp_page_template == "template-question.php" || $wp_page_template == "template-knowledgebase.php" || $wp_page_template == "template-home.php"))) && isset($orderby_post) && ($orderby_post == "popular" || $orderby_post == "most_visited" || $orderby_post == "most_reacted" || $orderby_post == "most_voted")) {
			$active_sticky = false;
			$array_data    = array_merge($custom_category,$orderby_array,(isset($cats_post) && is_array($cats_post)?$cats_post:array()),$specific_date_array,array("post_type" => $post_type,"ignore_sticky_posts" => 1,"paged" => $paged,"posts_per_page" => $post_number));
		}else if (isset($wp_page_template) && $wp_page_template == "template-home.php" && isset($first_one) && ($first_one == "all" || $first_one == "q-0")) {
			$active_sticky = $show_sticky = $post_not_true = true;
			$custom_args   = array("post_type" => wpqa_questions_type);
			$array_data    = array_merge($orderby_array,array("post_type" => $post_type,"ignore_sticky_posts" => 1,"paged" => $paged,"posts_per_page" => $post_number));
		}else if (isset($wp_page_template) && $wp_page_template == "template-home.php" && isset($first_one) && $first_one != "" && is_string($first_one) && isset($get_term_id) && is_numeric($get_term_id) && $get_term_id > 0) {
			$active_sticky = $show_sticky = $post_not_true = true;
			$custom_args   = array("post_type" => $its_post_type,"tax_query" => array(array("taxonomy" => $get_term_tax,"field" => "id","terms" => $get_term_id)));
			$array_data    = array_merge($orderby_array,$cats_post,array("post_type" => $post_type,"ignore_sticky_posts" => 1,"paged" => $paged,"posts_per_page" => $post_number,"tax_query" => array(array("taxonomy" => $get_term_tax,"field" => "slug","terms" => $first_one))));
		}else if ((isset($wp_page_template) && $wp_page_template == "template-question.php" && isset($orderby_post) && $orderby_post == "sticky") || (((((isset($tab_category) && $tab_category == true)) || (isset($tab_tag) && $tab_tag == true)) || (isset($wp_page_template) && $wp_page_template == "template-home.php")) && isset($first_one) && $first_one != "" && ($first_one == $question_sticky_slug || $first_one == $question_sticky_slug_2))) {
			$active_sticky = $sticky_only = $show_sticky = true;
			$custom_args   = array_merge($custom_category,$specific_date_array,array("post_type" => wpqa_questions_type));
		}else if ((isset($wp_page_template) && $wp_page_template == "template-question.php" && isset($orderby_post) && $orderby_post == "polls") || (((((isset($tab_category) && $tab_category == true)) || (isset($tab_tag) && $tab_tag == true)) || (isset($wp_page_template) && $wp_page_template == "template-home.php")) && isset($first_one) && $first_one != "" && ($first_one == $question_polls_slug || $first_one == $question_polls_slug_2))) {
			$active_sticky = $show_sticky = $post_not_true = true;
			$custom_args   = array("post_type" => wpqa_questions_type);
			$poll_array    = array("ignore_sticky_posts" => 1,"meta_query" => array(array("key" => "question_poll","value" => "on","compare" => "LIKE")));
			$array_data    = array_merge($custom_category,$specific_date_array,$poll_array,array("post_type" => wpqa_questions_type,"paged" => $paged,"posts_per_page" => $post_number));
		}else if ((isset($wp_page_template) && $wp_page_template == "template-question.php" && isset($orderby_post) && $orderby_post == "followed") || (((((isset($tab_category) && $tab_category == true)) || (isset($tab_tag) && $tab_tag == true)) || (isset($wp_page_template) && $wp_page_template == "template-home.php")) && isset($first_one) && $first_one != "" && ($first_one == $question_followed_slug || $first_one == $question_followed_slug_2))) {
			$active_sticky = $show_sticky = $post_not_true = false;
			$no_followed   = true;
			$following_questions_user = get_user_meta($user_id,"following_questions",true);
			if (is_array($following_questions_user) && !empty($following_questions_user) && count($following_questions_user) > 0) {
				$array_data = array_merge($custom_category,$specific_date_array,array("post_type" => wpqa_questions_type,"paged" => $paged,"post__in" => $following_questions_user));
			}
		}else if ((isset($wp_page_template) && $wp_page_template == "template-question.php" && isset($orderby_post) && $orderby_post == "favorites") || (((((isset($tab_category) && $tab_category == true)) || (isset($tab_tag) && $tab_tag == true)) || (isset($wp_page_template) && $wp_page_template == "template-home.php")) && isset($first_one) && $first_one != "" && ($first_one == $question_favorites_slug || $first_one == $question_favorites_slug_2))) {
			$active_sticky = $show_sticky = $post_not_true = false;
			$no_favorites  = true;
			$old_themes    = wpqa_options("old_themes");
			if ($old_themes == "ask_me") {
				$user_login    = get_userdata($user_id);
				$old_favorites = get_user_meta($user_id,$user_login->user_login."_favorites",true);
				if (isset($old_favorites) && !empty($old_favorites)) {
					update_user_meta($user_id,$user_id."_favorites",$old_favorites);
					delete_user_meta($user_id,$user_login->user_login."_favorites");
				}
			}
			$array_favorites = apply_filters("wpqa_user_array_favorites",array(),$user_id);
			$_favorites = get_user_meta($user_id,$user_id."_favorites",true);
			$_favorites = (is_array($_favorites) && !empty($_favorites)?$_favorites:array());
			$array_favorites = (is_array($array_favorites) && !empty($array_favorites)?$array_favorites:array());
			$array_favorites = array_merge($array_favorites,$_favorites);
			if (is_array($array_favorites) && !empty($array_favorites) && count($array_favorites) > 0) {
				$array_data = array_merge($custom_category,$specific_date_array,array("post_type" => wpqa_questions_type,"paged" => $paged,"post__in" => $array_favorites));
				$array_data = apply_filters("wpqa_home_tab_favorites",$array_data);
			}
		}else if (isset($wp_page_template) && $wp_page_template == "template-home.php" && isset($first_one) && ($first_one == "all" || $first_one == "k-0")) {
			$active_sticky = $show_sticky = $post_not_true = true;
			$custom_args   = array("post_type" => wpqa_knowledgebase_type);
			$array_data    = array_merge($orderby_array,array("post_type" => $post_type,"ignore_sticky_posts" => 1,"paged" => $paged,"posts_per_page" => $post_number));
		}else if ((isset($wp_page_template) && $wp_page_template == "template-knowledgebase.php" && isset($orderby_post) && $orderby_post == "sticky") || ((isset($wp_page_template) && $wp_page_template == "template-home.php") && isset($first_one) && $first_one != "" && ($first_one == $sticky_knowledgebases_slug || $first_one == $sticky_knowledgebases_slug_2))) {
			$active_sticky = $sticky_only = $show_sticky = true;
			$custom_args   = array_merge($custom_category,$specific_date_array,array("post_type" => wpqa_knowledgebase_type));
		}else {
			$post_not_true = true;
			if (($first_one == $feed_slug && is_user_logged_in()) || ($first_one == $feed_slug_2 && is_user_logged_in()) || ($first_one == $poll_feed_slug && is_user_logged_in()) || ($first_one == $poll_feed_slug_2 && is_user_logged_in()) || (isset($wp_page_template) && $wp_page_template == "template-question.php" && $orderby_post == "feed" && is_user_logged_in())) {
				include wpqa_get_template("feed-setting.php","theme-parts/");
				if ($user_following_if == "yes" && $cat_following_if == "yes" && $tag_following_if == "yes" && ($all_following != "" || $user_following != "")) {
					$post_type_feed = apply_filters(wpqa_prefix_theme."_post_type_feed",false,$wpdb);
					$user_following = (isset($user_following) && $user_following != ""?$user_following.",".$user_id:$user_id);
					$feed_updated = "COALESCE((SELECT MAX(comment_date) FROM $wpdb->comments wpc WHERE wpc.comment_post_id = $wpdb->posts.id),$wpdb->posts.post_date)";
					$order_by_updated = ($updated_answers == "on"?$feed_updated:"$wpdb->posts.post_date");
					$is_poll_feed = ($first_one == $poll_feed_slug && is_user_logged_in()) || ($first_one == $poll_feed_slug_2 && is_user_logged_in()?true:false);
					$custom_where = "(
						".($all_following != ""?"$wpdb->term_relationships.term_taxonomy_id IN (".$all_following.")".($user_following != ""?" OR":"")." ":"")."
						".($user_following != ""?"$wpdb->posts.post_author IN (".$user_following.") ":"")."
					)
					".(isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)?" AND $wpdb->posts.post_author NOT IN (".implode(",",$get_block_users).") ":"").
					(isset($is_poll_feed) && $is_poll_feed == true?"AND ( mt2.meta_key = 'question_poll' AND mt2.meta_value = 'on')":"")."
					".($specific_date_feed != ""?"AND ( $wpdb->posts.post_date >= '".$specific_date_feed."' ) ":"")."
					AND ($wpdb->posts.post_type = '".wpqa_questions_type."' ".$post_type_feed.")
					AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private')";

					$all_following_left_join = ($all_following != "" || $post_type_feed != ""?"LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) ":"");
					$poll_feed_left_join = (isset($is_poll_feed) && $is_poll_feed == true?"LEFT JOIN $wpdb->postmeta AS mt2 ON ($wpdb->posts.ID = mt2.post_id )":"");
					
					$count_total = $total_query = $wpdb->get_var($wpdb->prepare(
						"SELECT COUNT(*)
						from (
							SELECT DISTINCT $wpdb->posts.ID
							FROM $wpdb->posts
							$all_following_left_join
							$poll_feed_left_join
							WHERE 1=%s AND $custom_where
						) derived_count_table",1)
					);
					
					$query_sql = $wpdb->prepare(
						"SELECT DISTINCT $wpdb->posts.*
						FROM $wpdb->posts
						$all_following_left_join
						$poll_feed_left_join
						WHERE 1=%s AND $custom_where
						ORDER BY $order_by_updated DESC
						LIMIT $post_number OFFSET $offset",1
					);
					
					$query = $wpdb->get_results($query_sql);
					if (is_array($query) && !empty($query)) :?>
						<div class="post-articles<?php echo (isset($its_post_type) && (wpqa_questions_type == $its_post_type || wpqa_asked_questions_type == $its_post_type)?" question-articles".(isset($question_columns) && $question_columns == "style_2" && isset($count_total) && $count_total > 0?" row row-boot row-warp":""):"").($post_pagination == "none"?" no-pagination":"").(isset($blog_h) && $blog_h == "blog_h"?" post-articles-blog-h":"").(isset($post_style) && $post_style == "style_3" && isset($count_total) && $count_total > 0?" row row-boot row-warp":"").(isset($masonry_style) && $masonry_style == "on" && isset($count_total) && $count_total > 0?" isotope":"")?>">
							<?php foreach ($query as $post) {
								setup_postdata($post->ID);
								$k_ad_p++;
								include wpqa_get_template("loop-action.php","theme-parts/");
							}?>
						</div>
					<?php else :
						include wpqa_get_template("content-none.php","theme-parts/");
					endif;
					wpqa_load_pagination(array(
						"post_pagination" => (isset($post_pagination)?$post_pagination:"pagination"),
						"max_num_pages" => (isset($total_query)?ceil($total_query/$post_number):""),
						"it_answer_pagination" => (isset($it_answer_pagination)?$it_answer_pagination:false),
						"its_post_type" => (isset($its_post_type)?$its_post_type:false),
						"wpqa_query" => (isset($query_wp)?$query_wp:null),
					));
					if (is_array($query) && !empty($query)) {
						wp_reset_postdata();
					}
				}else {
					include wpqa_get_template("feed.php","theme-parts/");
				}
				$custom_sql = true;
			}else if (($first_one == $feed_slug && !is_user_logged_in()) || $first_one == $recent_questions_slug || ($first_one == $feed_slug_2 && !is_user_logged_in()) || ($first_one == $poll_feed_slug && !is_user_logged_in()) || ($first_one == $poll_feed_slug_2 && !is_user_logged_in()) || (isset($wp_page_template) && $wp_page_template == "template-question.php" && $orderby_post == "feed" && !is_user_logged_in()) || $first_one == $recent_questions_slug_2 || (!is_user_logged_in() && ($first_one == $questions_for_you_slug || $first_one == $questions_for_you_slug_2)) || $first_one == $recent_knowledgebases_slug || $first_one == $recent_knowledgebases_slug_2) {
				if (isset($wp_page_template) && $wp_page_template == "template-question.php" && $orderby_post == "feed") {
					$show_login = wpqa_post_meta("login_feed",$post_id_main);
				}else {
					$show_login = wpqa_post_meta("login_home_feed",$post_id_main);
				}
				$activate_login = wpqa_options("activate_login");
				if ($activate_login != 'disabled' && $show_login == "login" && (($first_one == $feed_slug && !is_user_logged_in()) || ($first_one == $feed_slug_2 && !is_user_logged_in()) || ($first_one == $poll_feed_slug && !is_user_logged_in()) || ($first_one == $poll_feed_slug_2 && !is_user_logged_in()) || (isset($wp_page_template) && $wp_page_template == "template-question.php" && $orderby_post == "feed" && !is_user_logged_in()))) {
					$no_feed_questions = true;
					echo wpqa_login_message(esc_html__("You must login to see your feed.","wpqa"),"warning","block");
				}else {
					$is_poll_feed  = ($first_one == $poll_feed_slug && !is_user_logged_in()) || ($first_one == $poll_feed_slug_2 && !is_user_logged_in()?true:false);
					$active_sticky = true;
					$custom_args   = array_merge((is_array($custom_category)?$custom_category:array()),(isset($cats_post) && is_array($cats_post)?$cats_post:array()),$specific_date_array,array("post_type" => ($first_one == $recent_knowledgebases_slug || $first_one == $recent_knowledgebases_slug_2?wpqa_knowledgebase_type:wpqa_questions_type)));
					$show_sticky   = true;
					if ($updated_answers == "on" && $first_one != $recent_knowledgebases_slug && $first_one != $recent_knowledgebases_slug_2) {
						$show_loop_updated = true;
						$custom_sql = true;
					}
				}
			}else if (is_user_logged_in() && ($first_one == $questions_for_you_slug || $first_one == $questions_for_you_slug_2)) {
				$category_list = get_user_meta($user_id,"wpqa_for_you_cats",true);
				$tag_list = get_user_meta($user_id,"wpqa_for_you_tags",true);
				$active_sticky = true;
				$cats_post     = array('tax_query' => array('relation' => 'OR',array('taxonomy' => wpqa_question_categories,'field' => 'id','terms' => $category_list,'operator' => 'IN'),array('taxonomy' => wpqa_question_tags,'field' => 'id','terms' => $tag_list,'operator' => 'IN')));
				$custom_args   = array_merge($cats_post,$specific_date_array,array("post_type" => wpqa_questions_type));
				$show_sticky   = true;
				if ($updated_answers == "on") {
					$category_list_updated = (is_array($category_list) && !empty($category_list)?implode(",",$category_list):"");
					$tag_list_updated = (is_array($tag_list) && !empty($tag_list)?implode(",",$tag_list):"");
					$all_tax_updated = ($category_list_updated != ""?$category_list_updated:"").($category_list_updated != "" && $tag_list_updated != ""?",":"").($tag_list_updated != ""?$tag_list_updated:"");
					$show_loop_updated = true;
					$custom_sql = true;
				}
			}else if (isset($wp_page_template) && $wp_page_template == "template-question.php" && $orderby_post == "recent") {
				$show_sticky = true;
				if ($updated_answers == "on") {
					$show_loop_updated = true;
					$custom_sql = true;
				}
			}else if (isset($wp_page_template) && $wp_page_template == "template-knowledgebase.php" && $orderby_post == "recent") {
				$show_sticky = true;
			}else if (isset($wp_page_template) && $wp_page_template == "template-blog.php" && $orderby_post == "recent") {
				$show_sticky = true;
			}
			$sticky_posts = ($post_type == "post"?array():array("ignore_sticky_posts" => 1));
			$array_data = apply_filters(wpqa_prefix_theme."_last_query_loop_page",array_merge((is_array($custom_category)?$custom_category:array()),$orderby_array,(isset($cats_post) && is_array($cats_post)?$cats_post:array()),$specific_date_array,$sticky_posts,array("post_type" => $post_type,"paged" => $paged,"posts_per_page" => $post_number)),$first_one);
		}
		if ((((((isset($tab_category) && $tab_category == true)) || (isset($tab_tag) && $tab_tag == true)) || (isset($wp_page_template) && $wp_page_template == "template-question.php") || (isset($wp_page_template) && $wp_page_template == "template-home.php"))) && ($orderby_post == "no_answer" || ($question_bump == "on" && $active_points == "on" && $orderby_post == "question_bump"))) {
			$array_data = array_merge($array_data,array("comment_count" => "0"));
		}
		$pagination_show   = "yes";
		$show_custom_error = true;
	}else if (is_category()) {
		$active_sticky = $show_sticky = $post_not_true = true;
		$array_data = array("posts_per_page" => $post_number,"cat" => $category_id,"post_type" => "post","paged" => $paged);
	}else if (is_tag()) {
		$active_sticky = $show_sticky = $post_not_true = true;
		$array_data = array("posts_per_page" => $post_number,"tag_id" => $category_id,"post_type" => "post","paged" => $paged);
	}else if (is_post_type_archive(wpqa_questions_type) && is_archive(wpqa_questions_type)) {
		$poll_array = array();
		if (isset($_GET["type"]) && $_GET["type"] == "poll") {
			$poll_array = array("ignore_sticky_posts" => 1,"meta_query" => array(array("key" => "question_poll","value" => "on","compare" => "LIKE")));
		}
		$poll_array = apply_filters(wpqa_prefix_theme."_poll_array",$poll_array);
		$post_not_true = true;
		$array_data = array_merge($poll_array,array("post_type" => wpqa_questions_type,"paged" => $paged));
	}else if (is_tax(wpqa_question_categories) || $tax_filter == true) {
		$question_numbers = array();
		if (isset($post_number) && $post_number > 0) {
			$question_numbers = array("posts_per_page" => $post_number);
		}
		$post_not_true = true;
		$array_data = apply_filters(wpqa_prefix_theme."_args_question_category",array_merge($question_numbers,array("ignore_sticky_posts" => 1,"post_type" => wpqa_questions_type,"paged" => $paged,"tax_query" => array(array("taxonomy" => $tax_question,"field" => "id","terms" => $category_id,'operator' => 'IN')))),$tax_question,$paged,$question_numbers,$category_id);
	}else if (is_tax(wpqa_question_tags)) {
		$post_not_true = true;
		$array_data = array_merge(array("ignore_sticky_posts" => 1,"post_type" => wpqa_questions_type,"paged" => $paged,"tax_query" => array(array("taxonomy" => wpqa_question_tags,"field" => "id","terms" => $category_id,"operator" => "IN"))));
	}else if (is_post_type_archive(wpqa_knowledgebase_type) && is_archive(wpqa_knowledgebase_type)) {
		$post_not_true = true;
		$array_data = array("post_type" => wpqa_knowledgebase_type,"paged" => $paged);
	}else if (is_tax(wpqa_knowledgebase_categories) || $tax_filter == true) {
		$knowledgebase_numbers = array();
		if (isset($post_number) && $post_number > 0) {
			$knowledgebase_numbers = array("posts_per_page" => $post_number);
		}
		$post_not_true = true;
		$array_data = apply_filters(wpqa_prefix_theme."_args_knowledgebase_category",array_merge($knowledgebase_numbers,array("ignore_sticky_posts" => 1,"post_type" => wpqa_knowledgebase_type,"paged" => $paged,"tax_query" => array(array("taxonomy" => $tax_knowledgebase,"field" => "id","terms" => $category_id,'operator' => 'IN')))),$tax_knowledgebase,$paged,$knowledgebase_numbers,$category_id);
	}else if (is_tax(wpqa_knowledgebase_tags)) {
		$post_not_true = true;
		$array_data = array_merge(array("ignore_sticky_posts" => 1,"post_type" => wpqa_knowledgebase_type,"paged" => $paged,"tax_query" => array(array("taxonomy" => wpqa_knowledgebase_tags,"field" => "id","terms" => $category_id,"operator" => "IN"))));
	}else if ($last_one == "questions" || $last_one == "knowledgebases" || $last_one == "posts") {
		$post_type_last_one = ($last_one == "questions"?wpqa_questions_type:($last_one == "knowledgebases"?wpqa_knowledgebase_type:"post"));
		$post_type_last_one = apply_filters(wpqa_prefix_theme."_question_posts_profile_post_type",$post_type_last_one,$last_one);
		$array_data = array("author" => $get_user_var,"post_type" => $post_type_last_one,"paged" => $paged,"ignore_sticky_posts" => 1);
	}else if ($last_one == "polls") {
		$array_data = array("author" => $get_user_var,"post_type" => wpqa_questions_type,"paged" => $paged,"meta_query" => array(array("key" => "question_poll","value" => "on","compare" => "=")));
	}else if ($last_one == "favorites") {
		$old_themes = wpqa_options("old_themes");
		if ($old_themes == "ask_me") {
			$user_login = get_userdata($get_user_var);
			$old_favorites = get_user_meta($get_user_var,$user_login->user_login."_favorites",true);
			if (isset($old_favorites) && !empty($old_favorites)) {
				update_user_meta($get_user_var,$get_user_var."_favorites",$old_favorites);
				delete_user_meta($get_user_var,$user_login->user_login."_favorites");
			}
		}
		$array_favorites = apply_filters("wpqa_user_array_favorites",array(),$get_user_var);
		$_favorites = get_user_meta($get_user_var,$get_user_var."_favorites",true);
		$_favorites = (is_array($_favorites) && !empty($_favorites)?$_favorites:array());
		$array_favorites = (is_array($array_favorites) && !empty($array_favorites)?$array_favorites:array());
		$array_favorites = array_merge($array_favorites,$_favorites);
		if (is_array($array_favorites) && !empty($array_favorites) && count($array_favorites) > 0) {
			$array_data = array("post_type" => wpqa_questions_type,"paged" => $paged,"post__in" => $array_favorites);
		}
	}else if ($ask_question_to_users == "on" && ($last_one == "asked" || $last_one == "my-asked" || $last_one == "asked-questions")) {
		if ($last_one == "my-asked") {
			$meta_asked = $author__not_in = array();
			$asked_args = array("key" => "user_id","compare" => "EXISTS");
			$author_args = array("author" => $get_user_var);
		}else {
			$asked_args = array("type" => "numeric","key" => "user_id","value" => (int)$get_user_var,"compare" => "=");
			$author_args = array();
			if ($last_one == "asked") {
				$meta_asked = array("key" => "user_is_comment","value" => true,"compare" => "=");
			}else {
				$meta_asked = array("key" => "user_is_comment","compare" => "NOT EXISTS");
			}
		}
		$array_data = array_merge($author_args,array("post_type" => wpqa_asked_questions_type,"paged" => $paged,"meta_query" => array(array_merge(array($meta_asked),array($asked_args)))));
	}else if ($pay_ask == "on" && ($last_one == "paid-questions")) {
		$array_data = array("author" => $get_user_var,"post_type" => wpqa_questions_type,"paged" => $paged,"meta_query" => array(array('type' => 'numeric',"key" => "_paid_question","value" => 'paid',"compare" => "=")));
	}else if ($last_one == "followed") {
		$following_questions_user = get_user_meta($get_user_var,"following_questions",true);
		if (is_array($following_questions_user) && !empty($following_questions_user) && count($following_questions_user) > 0) {
			$array_data = array("post_type" => wpqa_questions_type,"paged" => $paged,"post__in" => $following_questions_user);
		}
	}else if ($last_one == "followers-questions" || $last_one == "followers-posts") {
		$following_me = get_user_meta($get_user_var,"following_me",true);
		if (is_array($following_me) && !empty($following_me) && isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)) {
			$following_me = array_diff($following_me,$get_block_users);
		}
		if (is_array($following_me) && count($following_me) > 0) {
			$array_data = array("post_type" => ($last_one == "followers-questions"?wpqa_questions_type:"post"),"paged" => $paged,"author__in" => $following_me,"ignore_sticky_posts" => 1);
		}
	}else if (wpqa_is_pending_questions() || wpqa_is_pending_posts() && ($is_super_admin || $active_moderators == "on") && wpqa_is_user_owner() && ($is_super_admin || (isset($moderator_categories) && is_array($moderator_categories) && !empty($moderator_categories)))) {
		$post_type_pending = array(wpqa_questions_type,wpqa_asked_questions_type);
		$taxonomy_pending = wpqa_question_categories;
		if (wpqa_is_pending_posts()) {
			$post_type_pending = "post";
			$taxonomy_pending = "category";
		}
		$array_data = array("post_status" => "draft","ignore_sticky_posts" => 1,"paged" => $paged,"post_type" => $post_type_pending);
		if (is_array($moderator_categories) && !empty($moderator_categories) && in_array("0",$moderator_categories)) {
			$found_key = trim(array_search("0",$moderator_categories));
			$moderator_categories[$found_key+1] = "q-0";
		}
		$last_moderator_categories = wpqa_remove_item_by_value($moderator_categories,"q-0");
		$last_moderator_categories = wpqa_remove_item_by_value($last_moderator_categories,"p-0");
		if (!$is_super_admin) {
			if (((is_string($post_type_pending) && ($post_type_pending == wpqa_questions_type || $post_type_pending == wpqa_asked_questions_type)) || (is_array($post_type_pending) && (in_array(wpqa_questions_type,$post_type_pending) || in_array(wpqa_asked_questions_type,$post_type_pending)))) && !in_array("q-0",$moderator_categories)) {
				$categories_posts = array('tax_query' => array(array('taxonomy' => $taxonomy_pending,'field' => 'id','terms' => $last_moderator_categories,'operator' => 'IN')));
			}else if (((is_string($post_type_pending) && $post_type_pending == "post") || (is_array($post_type_pending) && in_array("post",$post_type_pending))) && !in_array("p-0",$moderator_categories)) {
				$categories_posts = array('tax_query' => array(array('taxonomy' => $taxonomy_pending,'field' => 'id','terms' => $last_moderator_categories,'operator' => 'IN')));
			}
		}
		$categories_posts = (isset($categories_posts) && is_array($categories_posts) && !empty($categories_posts)?$categories_posts:array());
		$array_data = array_merge($array_data,$categories_posts);
	}else {
		if (!isset($no_feed_questions)) {
			$array_data = apply_filters(wpqa_prefix_theme."_last_query_for_loop_page",array(),$paged,$post_number);
		}
	}

	if (isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users) && isset($array_data["author"]) && $array_data["author"] != "" && in_array($array_data["author"],$get_block_users)) {
		$get_block_users = wpqa_remove_item_by_value($get_block_users,$array_data["author"]);
	}

	$sticky_questions = apply_filters(wpqa_prefix_theme."_sticky_questions",get_option('sticky_questions'));
	$sticky_knowledgebases = apply_filters(wpqa_prefix_theme."_sticky_knowledgebases",get_option('sticky_knowledgebases'));
	$all_sticky_posts = apply_filters(wpqa_prefix_theme."_sticky_posts",get_option('sticky_posts'));
	$sticky_questions = (is_array($sticky_questions) && !empty($sticky_questions)?$sticky_questions:array());
	$sticky_knowledgebases = (is_array($sticky_knowledgebases) && !empty($sticky_knowledgebases)?$sticky_knowledgebases:array());
	$all_sticky_posts = (is_array($all_sticky_posts) && !empty($all_sticky_posts)?$all_sticky_posts:array());
	$post__not_in = array();
	if (!isset($no_feed_questions) && isset($post_not_true) && $post_not_true == true && ((isset($sticky_questions) && is_array($sticky_questions) && !empty($sticky_questions)) || (isset($sticky_knowledgebases) && is_array($sticky_knowledgebases) && !empty($sticky_knowledgebases)) || (isset($all_sticky_posts) && is_array($all_sticky_posts) && !empty($all_sticky_posts)))) {
		$last_sticky_posts = array_merge($sticky_questions,$sticky_knowledgebases,$all_sticky_posts);
		$post__not_in = array("post__not_in" => $last_sticky_posts);
		$array_data = array_merge($post__not_in,(is_array($array_data)?$array_data:array()));
	}

	if (isset($array_data) && is_array($array_data) && !empty($array_data)) {
		$array_data = array_merge($author__not_in,$array_data);
	}

	$array_data = apply_filters("wpqa_loop_array_data",(isset($array_data)?$array_data:array()),(isset($last_one)?$last_one:""));

	if (!isset($no_feed_questions) && isset($array_data) && is_array($array_data) && !empty($array_data)) {
		$query_wp = new WP_Query($array_data);
	}?>
	<section class="loop-section<?php echo ((isset($post_style) && $post_style == "style_3") || (isset($question_columns) && $question_columns == "style_2")?" section-post-with-columns":"")?>"<?php echo ("questions" == $last_one || $last_one == "asked" || $last_one == "my-asked" || $last_one == "asked-questions" || $last_one == "paid-questions" || $last_one == "polls" || $last_one == "followed" || $last_one == "favorites" || $last_one == "followers-questions" || $last_one == "posts" || $last_one == "followers-posts" || (wpqa_is_pending_questions() || wpqa_is_pending_posts())?" id='section-".wpqa_user_title()."'":"")?>>
		<?php if ((isset($no_favorites) || $last_one == "favorites") && empty($array_favorites)) {
			echo "<div class='alert-message alert-message-warning'><i class='icon-flag'></i><p>".esc_html__("There are no questions at favorite yet.","wpqa")."</p></div>";
		}else if ((isset($no_followed) || $last_one == "followed") && empty($following_questions_user)) {
			echo "<div class='alert-message alert-message-warning'><i class='icon-flag'></i><p>".esc_html__("There are no questions you followed yet.","wpqa")."</p></div>";
		}else {
			if (isset($its_post_type) && ($its_post_type == wpqa_questions_type || $its_post_type == wpqa_asked_questions_type)) {
				$page_tamplate = true;
				$its_question = true;
			}else if (isset($its_post_type) && $its_post_type == wpqa_knowledgebase_type) {
				$its_knowledgebase = true;
			}
			$page_tamplate   = (isset($page_tamplate)?$page_tamplate:'');
			$post_pagination = (isset($post_pagination)?$post_pagination:'');
			if ($page_tamplate != true) {
				$post_pagination = wpqa_options("post_pagination");
			}
			if ($updated_answers == "on" && isset($show_loop_updated)) {
				include wpqa_get_template("loop-updated.php","theme-parts/");
			}
			if (!isset($custom_sql)) :
				if (!empty($sticky_questions) || !empty($sticky_knowledgebases) || !empty($all_sticky_posts) || isset($custom_args) || (isset($query_wp) && $query_wp->have_posts()) || isset($has_have_posts) || (!isset($has_have_posts) && have_posts() && empty($array_data))) :
					if (isset($blog_h) || empty($wp_page_template) || (isset($wp_page_template) && $wp_page_template != "template-users.php" && $wp_page_template != "template-contact.php" && $wp_page_template != "template-faqs.php" && $wp_page_template != "template-categories.php" && $wp_page_template != "template-tags.php")) :
						$max_num_pages = (isset($query_wp->max_num_pages)?$query_wp->max_num_pages:"");
						if (isset($query_wp)) {
							$count_total = $query_wp->found_posts;
							$max_num_pages = ceil($count_total/(isset($post_number) && $post_number > 0?$post_number:get_option('posts_per_page')));
						}
						$more_link = get_next_posts_link("",$max_num_pages);?>
						<h2 class="screen-reader-text"><?php echo esc_html(get_bloginfo('name','display'))." ".esc_html__("Latest","wpqa")." ";printf("%s",(isset($its_question) && $its_question == true?esc_html__("Questions","wpqa"):(isset($its_knowledgebase) && $its_knowledgebase == true?esc_html__("Knowledgebases","wpqa"):esc_html__("Articles","wpqa"))))?></h2>
						<div class="post-articles<?php echo (isset($its_question) && $its_question == true?" question-articles".(isset($question_columns) && $question_columns == "style_2" && isset($count_total) && $count_total > 0?" row row-boot row-warp":""):"").(isset($its_knowledgebase) && $its_knowledgebase == true?" knowledgebase-articles":"").($post_pagination == "none"?" no-pagination":"").(isset($blog_h) && $blog_h == "blog_h"?" post-articles-blog-h":"").(empty($more_link)?" articles-no-pagination":"").(isset($post_style) && $post_style == "style_3" && isset($count_total) && $count_total > 0?" row row-boot row-warp":"").(isset($masonry_style) && $masonry_style == "on" && isset($count_total) && $count_total > 0?" isotope":"")?>">
							<?php if (isset($show_sticky) && $show_sticky == true) {
								if (isset($its_question) && $its_question == true) {
									include wpqa_get_template("sticky-question-settings.php","theme-parts/");
									include wpqa_get_template("sticky-question.php","theme-parts/");
								}else if (isset($its_knowledgebase) && $its_knowledgebase == true) {
									include wpqa_get_template("sticky-knowledgebase-settings.php","theme-parts/");
									include wpqa_get_template("sticky-knowledgebase.php","theme-parts/");
								}else {
									include wpqa_get_template("sticky-post.php","theme-parts/");
								}
								$active_sticky = false;
							}
					endif;
					if ((isset($is_questions_sticky) && $is_questions_sticky == true) || (isset($is_knowledgebases_sticky) && $is_knowledgebases_sticky == true) || (isset($is_posts_sticky) && $is_posts_sticky == true)) {
						$not_show_none = true;
					}
					if (!isset($sticky_only)) :
						if (isset($query_wp)) {
							if ($query_wp->have_posts()) :
								$wp_reset_postdata = true;
								while ($query_wp->have_posts()) : $query_wp->the_post();
									$k_ad_p++;
									if (isset($loop_query) && $loop_query == true) {
										do_action(wpqa_prefix_theme."_loop_include_content",(isset($first_one) && $first_one != ""?$first_one:false),$post_id_main);
									}else {
										include wpqa_get_template("loop-action.php","theme-parts/");
									}
								endwhile;
							else :
								if (!isset($not_show_none)) {
									include wpqa_get_template("content-none.php","theme-parts/");
								}
							endif;
						}else if (!isset($no_feed_questions)) {
							if (isset($has_have_posts)) {
								include wpqa_get_template("loop-action.php","theme-parts/");
							}else {
								if (have_posts()) :
									while (have_posts()) : the_post();
										$k_ad_p++;
										include wpqa_get_template("loop-action.php","theme-parts/");
									endwhile;
								else :
									if (!isset($not_show_none)) {
										include wpqa_get_template("content-none.php","theme-parts/");
									}
								endif;
							}
						}
					endif;
					if (empty($wp_page_template) || (isset($wp_page_template) && $wp_page_template != "template-users.php" && $wp_page_template != "template-contact.php" && $wp_page_template != "template-faqs.php" && $wp_page_template != "template-categories.php" && $wp_page_template != "template-tags.php")) :?>
						</div><!-- End post-articles -->
						<?php wpqa_load_pagination(array(
							"post_pagination" => (isset($post_pagination)?$post_pagination:"pagination"),
							"max_num_pages" => (isset($max_num_pages)?$max_num_pages:""),
							"it_answer_pagination" => (isset($it_answer_pagination)?$it_answer_pagination:false),
							"its_post_type" => (isset($its_post_type)?$its_post_type:false),
							"wpqa_query" => (isset($query_wp)?$query_wp:null),
						));
					endif;
				else :
					if (!isset($no_feed_questions)) {
						if ((isset($blog_h) && $blog_h == "blog_h") || (isset($show_custom_error) && $show_custom_error == true && (!isset($is_questions_sticky) || (isset($is_questions_sticky) && $is_questions_sticky != true) || !isset($is_knowledgebases_sticky) || (isset($is_knowledgebases_sticky) && $is_knowledgebases_sticky != true)))) {
							echo "<div class='alert-message alert-message-warning'><i class='icon-flag'></i><p>".(isset($its_question) && $its_question == true?esc_html__("There are no questions yet.","wpqa"):(isset($its_knowledgebase) && $its_knowledgebase == true?esc_html__("There are no knowledgebases yet.","wpqa"):esc_html__("There are no posts yet.","wpqa")))."</p></div>";
						}else {
							if (!is_author() && !isset($not_show_none)) {
								include wpqa_get_template("content-none.php","theme-parts/");
							}
						}
					}
				endif;
			endif;
		}
		
		$GLOBALS['wp_query'] = $GLOBALS['wp_the_query'];
		
		if (isset($wp_reset_postdata)) {
			wp_reset_postdata();
		}else {
			wp_reset_query();
		}?>
	</section><!-- End section -->
<?php }?>