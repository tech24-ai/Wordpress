<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Home tab setting */
function wpqa_home_setting($get_home_tabs,$category_id = "") {
	$question_bump = wpqa_options("question_bump");
	$active_points = wpqa_options("active_points");
	include wpqa_get_template("slugs.php","includes/");
	if (isset($get_home_tabs) && is_array($get_home_tabs)) {
		$i_count = -1;
		while ($i_count < count($get_home_tabs)) {
			$array_values_tabs = array_values($get_home_tabs);
			if ((isset($array_values_tabs[$i_count]["value"]) && $array_values_tabs[$i_count]["value"] != "" && $array_values_tabs[$i_count]["value"] != "0") || (isset($array_values_tabs[$i_count]["cat"]) && $array_values_tabs[$i_count]["cat"] == "yes")) {
				$get_i = $i_count;
				if (isset($array_values_tabs[$i_count]["cat"]) && $array_values_tabs[$i_count]["cat"] == "yes") {
					$home_tabs_keys = array_keys($get_home_tabs);
					$first_one = $get_home_tabs[$home_tabs_keys[$i_count]]["value"];
					$get_term = get_term_by('id',$first_one,wpqa_question_categories);
					$first_one = (isset($get_term->slug)?$get_term->slug:$first_one);
					if (!isset($get_term->slug)) {
						$get_term = get_term_by('id',$first_one,wpqa_knowledgebase_categories);
						$first_one = (isset($get_term->slug)?$get_term->slug:$first_one);
						$k_0 = true;
					}
					if ($first_one == 0 || $first_one === "0") {
						$first_one = (isset($k_0)?"k-0":"q-0");
					}
					$get_i = "none";
				}
				break;
			}
			$i_count++;
		}
		
		if (isset($get_i) && $get_i !== "none") {
			$array_keys_tabs = array_keys($get_home_tabs);
			$first_one = $array_keys_tabs[$get_i];
			if ($first_one == "feed") {
				$first_one = $feed_slug;
			}else if ($first_one == "recent-questions") {
				$first_one = $recent_questions_slug;
			}else if ($first_one == "questions-for-you") {
				$first_one = $questions_for_you_slug;
			}else if ($first_one == "most-answers") {
				$first_one = $most_answers_slug;
			}else if ($first_one == "answers") {
				$first_one = $answers_slug;
			}else if ($first_one == "answers-might-like") {
				$first_one = $answers_might_like_slug;
			}else if ($first_one == "answers-for-you") {
				$first_one = $answers_for_you_slug;
			}else if ($first_one == "no-answers") {
				$first_one = $no_answers_slug;
			}else if ($first_one == "most-visit") {
				$first_one = $most_visit_slug;
			}else if ($first_one == "most-reacted") {
				$first_one = $most_reacted_slug;
			}else if ($first_one == "most-vote") {
				$first_one = $most_vote_slug;
			}else if ($first_one == "random") {
				$first_one = $random_slug;
			}else if ($first_one == "new-questions") {
				$first_one = $question_new_slug;
			}else if ($first_one == "sticky-questions") {
				$first_one = $question_sticky_slug;
			}else if ($first_one == "polls") {
				$first_one = $question_polls_slug;
			}else if ($first_one == "followed") {
				$first_one = $question_followed_slug;
			}else if ($first_one == "favorites") {
				$first_one = $question_favorites_slug;
			}else if ($first_one == "poll-feed") {
				$first_one = $poll_feed_slug;
			}else if ($first_one == "recent-posts") {
				$first_one = $recent_posts_slug;
			}else if ($first_one == "posts-visited") {
				$first_one = $posts_visited_slug;
			}else if ($first_one == "recent-knowledgebases") {
				$first_one = $recent_knowledgebases_slug;
			}else if ($first_one == "random-knowledgebases") {
				$first_one = $random_knowledgebases_slug;
			}else if ($first_one == "sticky-knowledgebases") {
				$first_one = $sticky_knowledgebases_slug;
			}else if ($first_one == "knowledgebases-visited") {
				$first_one = $knowledgebases_visited_slug;
			}else if ($first_one == "knowledgebases-voted") {
				$first_one = $knowledgebases_voted_slug;
			}else if ($question_bump == "on" && $active_points == "on" && $first_one == "question-bump") {
				$first_one = $question_bump_slug;
			}else if ($first_one == "feed-2") {
				$first_one = $feed_slug_2;
			}else if ($first_one == "recent-questions-2") {
				$first_one = $recent_questions_slug_2;
			}else if ($first_one == "questions-for-you-2") {
				$first_one = $questions_for_you_slug_2;
			}else if ($first_one == "most-answers-2") {
				$first_one = $most_answers_slug_2;
			}else if ($first_one == "answers-2") {
				$first_one = $answers_slug_2;
			}else if ($first_one == "answers-might-like-2") {
				$first_one = $answers_might_like_slug_2;
			}else if ($first_one == "answers-for-you-2") {
				$first_one = $answers_for_you_slug_2;
			}else if ($first_one == "no-answers-2") {
				$first_one = $no_answers_slug_2;
			}else if ($first_one == "most-visit-2") {
				$first_one = $most_visit_slug_2;
			}else if ($first_one == "most-reacted-2") {
				$first_one = $most_reacted_slug_2;
			}else if ($first_one == "most-vote-2") {
				$first_one = $most_vote_slug_2;
			}else if ($first_one == "random-2") {
				$first_one = $random_slug_2;
			}else if ($first_one == "new-questions-2") {
				$first_one = $question_new_slug_2;
			}else if ($first_one == "sticky-questions-2") {
				$first_one = $question_sticky_slug_2;
			}else if ($first_one == "polls-2") {
				$first_one = $question_polls_slug_2;
			}else if ($first_one == "followed-2") {
				$first_one = $question_followed_slug_2;
			}else if ($first_one == "favorites-2") {
				$first_one = $question_favorites_slug_2;
			}else if ($first_one == "poll-feed-2") {
				$first_one = $poll_feed_slug_2;
			}else if ($first_one == "recent-posts-2") {
				$first_one = $recent_posts_slug_2;
			}else if ($first_one == "posts-visited-2") {
				$first_one = $posts_visited_slug_2;
			}else if ($question_bump == "on" && $active_points == "on" && $first_one == "question-bump-2") {
				$first_one = $question_bump_slug_2;
			}else if ($first_one == "recent-knowledgebases-2") {
				$first_one = $recent_knowledgebases_slug_2;
			}else if ($first_one == "random-knowledgebases-2") {
				$first_one = $random_knowledgebases_slug_2;
			}else if ($first_one == "sticky-knowledgebases-2") {
				$first_one = $sticky_knowledgebases_slug_2;
			}else if ($first_one == "knowledgebases-visited-2") {
				$first_one = $knowledgebases_visited_slug_2;
			}else if ($first_one == "knowledgebases-voted-2") {
				$first_one = $knowledgebases_voted_slug_2;
			}
			do_action(wpqa_prefix_theme."_home_page_tabs",$first_one);
		}
		
		if (isset($_GET["show"]) && $_GET["show"] != "") {
			$first_one = esc_html($_GET["show"]);
		}
	}

	return (isset($first_one) && $first_one != ""?$first_one:"");
}
/* Home tabs */
if (!function_exists('wpqa_home_tabs')) :
	function wpqa_home_tabs($get_home_tabs,$first_one,$category_id = "",$tabs_menu = "",$page_template = "",$tabs_menu_icons = "yes") {
		if ($tabs_menu == "") {?>
			<div class="wrap-tabs">
				<div class="menu-tabs">
					<ul class="menu flex menu-tabs-desktop navbar-nav navbar-secondary">
		<?php }
						wpqa_home_tab_list($get_home_tabs,$first_one,$category_id,$tabs_menu,$page_template,"li",$tabs_menu_icons);
		if ($tabs_menu == "") {?>
					</ul>
					<div class="wpqa_hide mobile-tabs"><span class="styled-select"><select class="form-control home_categories"><?php wpqa_home_tab_list($get_home_tabs,$first_one,$category_id,$tabs_menu,$page_template,"option",$tabs_menu_icons);?></select></span></div>
				</div><!-- End menu-tabs -->
			</div><!-- End wrap-tabs -->
		<?php }
	}
endif;
if (!function_exists('wpqa_home_tab_list')) :
	function wpqa_home_tab_list($get_home_tabs,$first_one,$category_id = "",$tabs_menu = "",$page_template = "",$list_child = "li",$tabs_menu_icons = "yes") {
		$question_bump = wpqa_options("question_bump");
		$active_points = wpqa_options("active_points");
		include wpqa_get_template("slugs.php","includes/");
		$last_url = array();
		if (is_array($get_home_tabs) && !empty($get_home_tabs)) {
			foreach ($get_home_tabs as $key => $value) {
				if ((isset($get_home_tabs[$key]["sort"]) && isset($get_home_tabs[$key]["value"]) && $get_home_tabs[$key]["value"] != "" && $get_home_tabs[$key]["value"] != "0") || (isset($get_home_tabs[$key]["value"]) && isset($get_home_tabs[$key]["cat"]))) {
					if (isset($get_home_tabs[$key]["value"]) && isset($get_home_tabs[$key]["cat"])) {
						if (is_numeric($get_home_tabs[$key]["value"]) && $get_home_tabs[$key]["value"] > 0) {
							$get_tax = get_term($get_home_tabs[$key]["value"]);
							if (isset($get_tax->term_id) && $get_tax->term_id > 0) {
								$last_url[$key]["link"] = $get_tax->slug;
								$category_icon = get_term_meta($get_home_tabs[$key]["value"],prefix_terms."category_icon",true);
								if ($category_icon != "") {
									$last_url[$key]["class"] = $category_icon;
								}
							}else {
								$last_url[$key]["link"] = str_ireplace("cat-","",$key);
							}
						}else {
							$last_url[$key]["link"] = str_ireplace("cat-","",$key);
						}
					}else {
						if ($key == "feed") {
							$last_url[$key]["link"] = $feed_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "recent-questions") {
							$last_url[$key]["link"] = $recent_questions_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "questions-for-you") {
							$last_url[$key]["link"] = $questions_for_you_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "most-answers") {
							$last_url[$key]["link"] = $most_answers_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "answers") {
							$last_url[$key]["link"] = $answers_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "answers-might-like") {
							$last_url[$key]["link"] = $answers_might_like_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "answers-for-you") {
							$last_url[$key]["link"] = $answers_for_you_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "no-answers") {
							$last_url[$key]["link"] = $no_answers_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "most-visit") {
							$last_url[$key]["link"] = $most_visit_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "most-reacted") {
							$last_url[$key]["link"] = $most_reacted_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "most-vote") {
							$last_url[$key]["link"] = $most_vote_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "random") {
							$last_url[$key]["link"] = $random_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "new-questions") {
							$last_url[$key]["link"] = $question_new_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "sticky-questions") {
							$last_url[$key]["link"] = $question_sticky_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "polls") {
							$last_url[$key]["link"] = $question_polls_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "followed") {
							$last_url[$key]["link"] = $question_followed_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "favorites") {
							$last_url[$key]["link"] = $question_favorites_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "poll-feed") {
							$last_url[$key]["link"] = $poll_feed_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "recent-posts") {
							$last_url[$key]["link"] = $recent_posts_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "posts-visited") {
							$last_url[$key]["link"] = $posts_visited_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($question_bump == "on" && $active_points == "on" && $key == "question-bump") {
							$last_url[$key]["link"] = $question_bump_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "recent-knowledgebases") {
							$last_url[$key]["link"] = $recent_knowledgebases_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "random-knowledgebases") {
							$last_url[$key]["link"] = $random_knowledgebases_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "sticky-knowledgebases") {
							$last_url[$key]["link"] = $sticky_knowledgebases_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "knowledgebases-visited") {
							$last_url[$key]["link"] = $knowledgebases_visited_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "knowledgebases-voted") {
							$last_url[$key]["link"] = $knowledgebases_voted_slug;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "feed-2") {
							$last_url[$key]["link"] = $feed_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "recent-questions-2") {
							$last_url[$key]["link"] = $recent_questions_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "questions-for-you-2") {
							$last_url[$key]["link"] = $questions_for_you_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "most-answers-2") {
							$last_url[$key]["link"] = $most_answers_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "answers-2") {
							$last_url[$key]["link"] = $answers_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "answers-might-like-2") {
							$last_url[$key]["link"] = $answers_might_like_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "answers-for-you-2") {
							$last_url[$key]["link"] = $answers_for_you_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "no-answers-2") {
							$last_url[$key]["link"] = $no_answers_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "most-visit-2") {
							$last_url[$key]["link"] = $most_visit_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "most-reacted-2") {
							$last_url[$key]["link"] = $most_reacted_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "most-vote-2") {
							$last_url[$key]["link"] = $most_vote_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "random-2") {
							$last_url[$key]["link"] = $random_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "new-questions-2") {
							$last_url[$key]["link"] = $question_new_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "sticky-questions-2") {
							$last_url[$key]["link"] = $question_sticky_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "polls-2") {
							$last_url[$key]["link"] = $question_polls_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "followed-2") {
							$last_url[$key]["link"] = $question_followed_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "favorites-2") {
							$last_url[$key]["link"] = $question_favorites_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "poll-feed-2") {
							$last_url[$key]["link"] = $poll_feed_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "recent-posts-2") {
							$last_url[$key]["link"] = $recent_posts_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "posts-visited-2") {
							$last_url[$key]["link"] = $posts_visited_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($question_bump == "on" && $active_points == "on" && $key == "question-bump-2") {
							$last_url[$key]["link"] = $question_bump_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "recent-knowledgebases-2") {
							$last_url[$key]["link"] = $recent_knowledgebases_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "random-knowledgebases-2") {
							$last_url[$key]["link"] = $random_knowledgebases_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "sticky-knowledgebases-2") {
							$last_url[$key]["link"] = $sticky_knowledgebases_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "knowledgebases-visited-2") {
							$last_url[$key]["link"] = $knowledgebases_visited_slug_2;
							$last_url[$key]["link-2"] = $key;
						}else if ($key == "knowledgebases-voted-2") {
							$last_url[$key]["link"] = $knowledgebases_voted_slug_2;
							$last_url[$key]["link-2"] = $key;
						}
						$last_url = apply_filters(wpqa_prefix_theme."_filter_home_page_tabs_last_url",(isset($last_url)?$last_url:$key),$key);
						do_action(wpqa_prefix_theme."_home_page_tabs_list",$key,(isset($last_url)?$last_url:$key));
					}
				}
			}
		}
		if (isset($last_url) && is_array($last_url) && !empty($last_url)) {
			foreach ($last_url as $last_key => $last_value) {
				if (isset($last_value["link"])) {
					if ($tabs_menu != "" && $tabs_menu > 0) {
						$get_url = esc_url(add_query_arg("show",esc_html($last_value["link"]),get_the_permalink($tabs_menu)));
					}else {
						if ($category_id != "") {
							$term = get_term($category_id);
						}
						$get_url = esc_url(add_query_arg("show",esc_html($last_value["link"]),($page_template != ""?get_the_permalink($page_template):($category_id != ""?get_term_link((isset($term) && isset($term->taxonomy)?$term:$category_id),(isset($term) && isset($term->taxonomy)?$term->taxonomy:wpqa_question_categories)):home_url('/')))));
					}
					if ($list_child == "li") {?>
						<li class="menu-item<?php echo (isset($first_one) && $first_one != "" && $first_one == $last_value["link"]?" active-tab":"")?>">
							<a href="<?php echo esc_url($get_url)?>">
							<?php if (($tabs_menu_icons === "on" || $tabs_menu_icons === "yes") && $tabs_menu != "" && $tabs_menu > 0 && isset($last_value["class"]) && $last_value["class"] != "") {?>
								<i class="<?php echo esc_attr($last_value["class"])?>"></i>
							<?php }
					}else {?>
						<option<?php echo (isset($first_one) && $first_one != "" && $first_one == $last_value["link"]?" selected='selected'":"")?> value="<?php echo esc_url($get_url)?>">
					<?php }
							if (isset($last_value["link"]) && (isset($get_home_tabs[$last_value["link"]]["sort"]) || (isset($last_value["link-2"]) && isset($get_home_tabs[$last_value["link-2"]]["sort"])))) {
								if ($last_value["link"] == $feed_slug) {
									esc_html_e("Feed","wpqa");
								}else if ($last_value["link"] == $recent_questions_slug) {
									esc_html_e("Recent Questions","wpqa");
								}else if ($last_value["link"] == $questions_for_you_slug) {
									esc_html_e("Questions For You","wpqa");
								}else if ($last_value["link"] == $most_answers_slug) {
									esc_html_e("Most Answered","wpqa");
								}else if ($last_value["link"] == $answers_slug) {
									esc_html_e("Answers","wpqa");
								}else if ($last_value["link"] == $answers_might_like_slug) {
									esc_html_e("Answers You Might Like","wpqa");
								}else if ($last_value["link"] == $answers_for_you_slug) {
									esc_html_e("Answers For You","wpqa");
								}else if ($last_value["link"] == $no_answers_slug) {
									esc_html_e("No Answers","wpqa");
								}else if ($last_value["link"] == $most_visit_slug) {
									esc_html_e("Most Visited","wpqa");
								}else if ($last_value["link"] == $most_reacted_slug) {
									esc_html_e("Most Reacted","wpqa");
								}else if ($last_value["link"] == $most_vote_slug) {
									esc_html_e("Most Voted","wpqa");
								}else if ($last_value["link"] == $random_slug) {
									esc_html_e("Random","wpqa");
								}else if ($last_value["link"] == $question_new_slug) {
									esc_html_e("New Questions","wpqa");
								}else if ($last_value["link"] == $question_sticky_slug) {
									esc_html_e("Sticky Questions","wpqa");
								}else if ($last_value["link"] == $question_polls_slug) {
									esc_html_e("Polls","wpqa");
								}else if ($last_value["link"] == $question_followed_slug) {
									esc_html_e("Followed Questions","wpqa");
								}else if ($last_value["link"] == $question_favorites_slug) {
									esc_html_e("Favorite Questions","wpqa");
								}else if ($last_value["link"] == $poll_feed_slug) {
									esc_html_e("Poll feed","wpqa");
								}else if ($last_value["link"] == $recent_posts_slug) {
									esc_html_e("Recent Posts","wpqa");
								}else if ($last_value["link"] == $posts_visited_slug) {
									esc_html_e("Most Visited Posts","wpqa");
								}else if ($question_bump == "on" && $active_points == "on" && $last_value["link"] == $question_bump_slug) {
									esc_html_e("Bump Question","wpqa");
								}else if ($last_value["link"] == $recent_knowledgebases_slug) {
									esc_html_e("Recent Articles","wpqa");
								}else if ($last_value["link"] == $random_knowledgebases_slug) {
									esc_html_e("Random Articles","wpqa");
								}else if ($last_value["link"] == $sticky_knowledgebases_slug) {
									esc_html_e("Sticky Articles","wpqa");
								}else if ($last_value["link"] == $knowledgebases_visited_slug) {
									esc_html_e("Most Visited Articles","wpqa");
								}else if ($last_value["link"] == $knowledgebases_voted_slug) {
									esc_html_e("Most Voted Articles","wpqa");
								}else if ($last_value["link"] == $feed_slug_2) {
									esc_html_e("Feed With Time","wpqa");
								}else if ($last_value["link"] == $recent_questions_slug_2) {
									esc_html_e("Recent Questions With Time","wpqa");
								}else if ($last_value["link"] == $questions_for_you_slug_2) {
									esc_html_e("Questions For You With Time","wpqa");
								}else if ($last_value["link"] == $most_answers_slug_2) {
									esc_html_e("Most Answered With Time","wpqa");
								}else if ($last_value["link"] == $answers_slug_2) {
									esc_html_e("Answers With Time","wpqa");
								}else if ($last_value["link"] == $answers_might_like_slug_2) {
									esc_html_e("Answers You Might Like With Time","wpqa");
								}else if ($last_value["link"] == $answers_for_you_slug_2) {
									esc_html_e("Answers For You With Time","wpqa");
								}else if ($last_value["link"] == $no_answers_slug_2) {
									esc_html_e("No Answers With Time","wpqa");
								}else if ($last_value["link"] == $most_visit_slug_2) {
									esc_html_e("Most Visited With Time","wpqa");
								}else if ($last_value["link"] == $most_reacted_slug_2) {
									esc_html_e("Most Reacted With Time","wpqa");
								}else if ($last_value["link"] == $most_vote_slug_2) {
									esc_html_e("Most Voted With Time","wpqa");
								}else if ($last_value["link"] == $random_slug_2) {
									esc_html_e("Random With Time","wpqa");
								}else if ($last_value["link"] == $question_new_slug_2) {
									esc_html_e("New Questions With Time","wpqa");
								}else if ($last_value["link"] == $question_sticky_slug_2) {
									esc_html_e("Sticky Questions With Time","wpqa");
								}else if ($last_value["link"] == $question_polls_slug_2) {
									esc_html_e("Polls With Time","wpqa");
								}else if ($last_value["link"] == $question_followed_slug_2) {
									esc_html_e("Followed Questions With Time","wpqa");
								}else if ($last_value["link"] == $question_favorites_slug_2) {
									esc_html_e("Favorite Questions With Time","wpqa");
								}else if ($last_value["link"] == $poll_feed_slug_2) {
									esc_html_e("Poll feed With Time","wpqa");
								}else if ($last_value["link"] == $recent_posts_slug_2) {
									esc_html_e("Recent Posts With Time","wpqa");
								}else if ($last_value["link"] == $posts_visited_slug_2) {
									esc_html_e("Most Visited Posts With Time","wpqa");
								}else if ($question_bump == "on" && $active_points == "on" && $last_value["link"] == $question_bump_slug_2) {
									esc_html_e("Bump Question With Time","wpqa");
								}else if ($last_value["link"] == $recent_knowledgebases_slug_2) {
									esc_html_e("Recent Articles With Time","wpqa");
								}else if ($last_value["link"] == $random_knowledgebases_slug_2) {
									esc_html_e("Random Articles With Time","wpqa");
								}else if ($last_value["link"] == $sticky_knowledgebases_slug_2) {
									esc_html_e("Sticky Articles With Time","wpqa");
								}else if ($last_value["link"] == $knowledgebases_visited_slug_2) {
									esc_html_e("Most Visited Articles With Time","wpqa");
								}else if ($last_value["link"] == $knowledgebases_voted_slug_2) {
									esc_html_e("Most Voted Articles With Time","wpqa");
								}
								do_action(wpqa_prefix_theme."_home_page_tabs_text",$last_value["link"]);
							}else if (isset($get_home_tabs[$last_key]["value"])) {
								if (is_numeric($get_home_tabs[$last_key]["value"]) && $get_home_tabs[$last_key]["value"] > 0) {
									$get_tax = get_term($get_home_tabs[$last_key]["value"]);
									if (isset($get_tax->term_id) && $get_tax->term_id > 0) {
										echo esc_html($get_tax->name);
									}
								}else {
									echo (isset($last_value["link"]) && $last_value["link"] == "k-0"?esc_html__("All Articles","wpqa"):esc_html__("All Question","wpqa"));
								}
							}
					if ($list_child == "li") {?>
							</a>
						</li>
					<?php }else {?>
						</option>
					<?php }
				}
			}
		}
	}
endif;
/* Loop tabs */
add_filter(wpqa_prefix_theme."_home_page_tabs_available","wpqa_home_page_tabs_available",1,4);
function wpqa_home_page_tabs_available($tabs_available,$get_home_tabs,$first_one = "",$category_id = 0) {
	$question_bump = wpqa_options("question_bump");
	$active_points = wpqa_options("active_points");
	include wpqa_get_template("slugs.php","includes/");
	if ($first_one != "" && (($first_one == $feed_slug && isset($get_home_tabs["feed"]["value"]) && $get_home_tabs["feed"]["value"] != "" && $get_home_tabs["feed"]["value"] != "0") || ($first_one == $recent_questions_slug && isset($get_home_tabs["recent-questions"]["value"]) && $get_home_tabs["recent-questions"]["value"] != "" && $get_home_tabs["recent-questions"]["value"] != "0") || ($first_one == $questions_for_you_slug && isset($get_home_tabs["questions-for-you"]["value"]) && $get_home_tabs["questions-for-you"]["value"] != "" && $get_home_tabs["questions-for-you"]["value"] != "0") || ($first_one == $most_answers_slug && isset($get_home_tabs["most-answers"]["value"]) && $get_home_tabs["most-answers"]["value"] != "" && $get_home_tabs["most-answers"]["value"] != "0") || ($first_one == $no_answers_slug && isset($get_home_tabs["no-answers"]["value"]) && $get_home_tabs["no-answers"]["value"] != "" && $get_home_tabs["no-answers"]["value"] != "0") || ($first_one == $most_visit_slug && isset($get_home_tabs["most-visit"]["value"]) && $get_home_tabs["most-visit"]["value"] != "" && $get_home_tabs["most-visit"]["value"] != "0") || ($first_one == $most_reacted_slug && isset($get_home_tabs["most-reacted"]["value"]) && $get_home_tabs["most-reacted"]["value"] != "" && $get_home_tabs["most-reacted"]["value"] != "0") || ($first_one == $most_vote_slug && isset($get_home_tabs["most-vote"]["value"]) && $get_home_tabs["most-vote"]["value"] != "" && $get_home_tabs["most-vote"]["value"] != "0") || ($first_one == $random_slug && isset($get_home_tabs["random"]["value"]) && $get_home_tabs["random"]["value"] != "" && $get_home_tabs["random"]["value"] != "0") || ($first_one == $question_new_slug && isset($get_home_tabs["new-questions"]["value"]) && $get_home_tabs["new-questions"]["value"] != "" && $get_home_tabs["new-questions"]["value"] != "0") || ($first_one == $question_sticky_slug && isset($get_home_tabs["sticky-questions"]["value"]) && $get_home_tabs["sticky-questions"]["value"] != "" && $get_home_tabs["sticky-questions"]["value"] != "0") || ($first_one == $question_polls_slug && isset($get_home_tabs["polls"]["value"]) && $get_home_tabs["polls"]["value"] != "" && $get_home_tabs["polls"]["value"] != "0") || ($first_one == $question_followed_slug && isset($get_home_tabs["followed"]["value"]) && $get_home_tabs["followed"]["value"] != "" && $get_home_tabs["followed"]["value"] != "0") || ($first_one == $question_favorites_slug && isset($get_home_tabs["favorites"]["value"]) && $get_home_tabs["favorites"]["value"] != "" && $get_home_tabs["favorites"]["value"] != "0") || ($first_one == $poll_feed_slug && isset($get_home_tabs["poll-feed"]["value"]) && $get_home_tabs["poll-feed"]["value"] != "" && $get_home_tabs["poll-feed"]["value"] != "0") || ($first_one == $recent_posts_slug && isset($get_home_tabs["recent-posts"]["value"]) && $get_home_tabs["recent-posts"]["value"] != "" && $get_home_tabs["recent-posts"]["value"] != "0") || ($first_one == $posts_visited_slug && isset($get_home_tabs["posts-visited"]["value"]) && $get_home_tabs["posts-visited"]["value"] != "" && $get_home_tabs["posts-visited"]["value"] != "0") || ($question_bump == "on" && $active_points == "on" && $first_one == $question_bump_slug && isset($get_home_tabs["question-bump"]["value"]) && $get_home_tabs["question-bump"]["value"] != "" && $get_home_tabs["question-bump"]["value"] != "0") || ($first_one == $feed_slug_2 && isset($get_home_tabs["feed-2"]["value"]) && $get_home_tabs["feed-2"]["value"] != "" && $get_home_tabs["feed-2"]["value"] != "0") || ($first_one == $recent_questions_slug_2 && isset($get_home_tabs["recent-questions-2"]["value"]) && $get_home_tabs["recent-questions-2"]["value"] != "" && $get_home_tabs["recent-questions-2"]["value"] != "0") || ($first_one == $questions_for_you_slug_2 && isset($get_home_tabs["questions-for-you-2"]["value"]) && $get_home_tabs["questions-for-you-2"]["value"] != "" && $get_home_tabs["questions-for-you-2"]["value"] != "0") || ($first_one == $most_answers_slug_2 && isset($get_home_tabs["most-answers-2"]["value"]) && $get_home_tabs["most-answers-2"]["value"] != "" && $get_home_tabs["most-answers-2"]["value"] != "0") || ($first_one == $no_answers_slug_2 && isset($get_home_tabs["no-answers-2"]["value"]) && $get_home_tabs["no-answers-2"]["value"] != "" && $get_home_tabs["no-answers-2"]["value"] != "0") || ($first_one == $most_visit_slug_2 && isset($get_home_tabs["most-visit-2"]["value"]) && $get_home_tabs["most-visit-2"]["value"] != "" && $get_home_tabs["most-visit-2"]["value"] != "0") || ($first_one == $most_reacted_slug_2 && isset($get_home_tabs["most-reacted-2"]["value"]) && $get_home_tabs["most-reacted-2"]["value"] != "" && $get_home_tabs["most-reacted-2"]["value"] != "0") || ($first_one == $most_vote_slug_2 && isset($get_home_tabs["most-vote-2"]["value"]) && $get_home_tabs["most-vote-2"]["value"] != "" && $get_home_tabs["most-vote-2"]["value"] != "0") || ($first_one == $random_slug_2 && isset($get_home_tabs["random-2"]["value"]) && $get_home_tabs["random-2"]["value"] != "" && $get_home_tabs["random-2"]["value"] != "0") || ($first_one == $question_new_slug_2 && isset($get_home_tabs["new-questions-2"]["value"]) && $get_home_tabs["new-questions-2"]["value"] != "" && $get_home_tabs["new-questions-2"]["value"] != "0") || ($first_one == $question_sticky_slug_2 && isset($get_home_tabs["sticky-questions-2"]["value"]) && $get_home_tabs["sticky-questions-2"]["value"] != "" && $get_home_tabs["sticky-questions-2"]["value"] != "0") || ($first_one == $question_polls_slug_2 && isset($get_home_tabs["polls-2"]["value"]) && $get_home_tabs["polls-2"]["value"] != "" && $get_home_tabs["polls-2"]["value"] != "0") || ($first_one == $question_followed_slug_2 && isset($get_home_tabs["followed-2"]["value"]) && $get_home_tabs["followed-2"]["value"] != "" && $get_home_tabs["followed-2"]["value"] != "0") || ($first_one == $question_favorites_slug_2 && isset($get_home_tabs["favorites-2"]["value"]) && $get_home_tabs["favorites-2"]["value"] != "" && $get_home_tabs["favorites-2"]["value"] != "0") || ($first_one == $poll_feed_slug_2 && isset($get_home_tabs["poll-feed-2"]["value"]) && $get_home_tabs["poll-feed-2"]["value"] != "" && $get_home_tabs["poll-feed-2"]["value"] != "0") || ($first_one == $recent_posts_slug_2 && isset($get_home_tabs["recent-posts-2"]["value"]) && $get_home_tabs["recent-posts-2"]["value"] != "" && $get_home_tabs["recent-posts-2"]["value"] != "0") || ($first_one == $posts_visited_slug_2 && isset($get_home_tabs["posts-visited-2"]["value"]) && $get_home_tabs["posts-visited-2"]["value"] != "" && $get_home_tabs["posts-visited-2"]["value"] != "0") || ($question_bump == "on" && $active_points == "on" && $first_one == $question_bump_slug_2 && isset($get_home_tabs["question-bump-2"]["value"]) && $get_home_tabs["question-bump-2"]["value"] != "" && $get_home_tabs["question-bump-2"]["value"] != "0") || ($first_one == $answers_slug && isset($get_home_tabs["answers"]["value"]) && $get_home_tabs["answers"]["value"] != "" && $get_home_tabs["answers"]["value"] != "0") || ($first_one == $answers_might_like_slug && isset($get_home_tabs["answers-might-like"]["value"]) && $get_home_tabs["answers-might-like"]["value"] != "" && $get_home_tabs["answers-might-like"]["value"] != "0") || ($first_one == $answers_for_you_slug && isset($get_home_tabs["answers-for-you"]["value"]) && $get_home_tabs["answers-for-you"]["value"] != "" && $get_home_tabs["answers-for-you"]["value"] != "0") || ($first_one == $answers_slug_2 && isset($get_home_tabs["answers-2"]["value"]) && $get_home_tabs["answers-2"]["value"] != "" && $get_home_tabs["answers-2"]["value"] != "0") || ($first_one == $answers_might_like_slug_2 && isset($get_home_tabs["answers-might-like-2"]["value"]) && $get_home_tabs["answers-might-like-2"]["value"] != "" && $get_home_tabs["answers-might-like-2"]["value"] != "0") || ($first_one == $answers_for_you_slug_2 && isset($get_home_tabs["answers-for-you-2"]["value"]) && $get_home_tabs["answers-for-you-2"]["value"] != "" && $get_home_tabs["answers-for-you-2"]["value"] != "0") || ($first_one == $recent_knowledgebases_slug && isset($get_home_tabs["recent-knowledgebases"]["value"]) && $get_home_tabs["recent-knowledgebases"]["value"] != "" && $get_home_tabs["recent-knowledgebases"]["value"] != "0") || ($first_one == $random_knowledgebases_slug && isset($get_home_tabs["random-knowledgebases"]["value"]) && $get_home_tabs["random-knowledgebases"]["value"] != "" && $get_home_tabs["random-knowledgebases"]["value"] != "0") || ($first_one == $sticky_knowledgebases_slug && isset($get_home_tabs["sticky-knowledgebases"]["value"]) && $get_home_tabs["sticky-knowledgebases"]["value"] != "" && $get_home_tabs["sticky-knowledgebases"]["value"] != "0") || ($first_one == $knowledgebases_visited_slug && isset($get_home_tabs["knowledgebases-visited"]["value"]) && $get_home_tabs["knowledgebases-visited"]["value"] != "" && $get_home_tabs["knowledgebases-visited"]["value"] != "0") || ($first_one == $knowledgebases_voted_slug && isset($get_home_tabs["knowledgebases-voted"]["value"]) && $get_home_tabs["knowledgebases-voted"]["value"] != "" && $get_home_tabs["knowledgebases-voted"]["value"] != "0") || ($first_one == $recent_knowledgebases_slug_2 && isset($get_home_tabs["recent-knowledgebases-2"]["value"]) && $get_home_tabs["recent-knowledgebases-2"]["value"] != "" && $get_home_tabs["recent-knowledgebases-2"]["value"] != "0") || ($first_one == $random_knowledgebases_slug_2 && isset($get_home_tabs["random-knowledgebases-2"]["value"]) && $get_home_tabs["random-knowledgebases-2"]["value"] != "" && $get_home_tabs["random-knowledgebases-2"]["value"] != "0") || ($first_one == $sticky_knowledgebases_slug_2 && isset($get_home_tabs["sticky-knowledgebases-2"]["value"]) && $get_home_tabs["sticky-knowledgebases-2"]["value"] != "" && $get_home_tabs["sticky-knowledgebases-2"]["value"] != "0") || ($first_one == $knowledgebases_visited_slug_2 && isset($get_home_tabs["knowledgebases-visited-2"]["value"]) && $get_home_tabs["knowledgebases-visited-2"]["value"] != "" && $get_home_tabs["knowledgebases-visited-2"]["value"] != "0") || ($first_one == $knowledgebases_voted_slug_2 && isset($get_home_tabs["knowledgebases-voted-2"]["value"]) && $get_home_tabs["knowledgebases-voted-2"]["value"] != "" && $get_home_tabs["knowledgebases-voted-2"]["value"] != "0"))) {
		$tabs_available = true;
	}
	return $tabs_available;
}?>