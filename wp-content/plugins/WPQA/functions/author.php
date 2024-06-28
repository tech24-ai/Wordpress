<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Author */
if (!function_exists('wpqa_author')) :
	function wpqa_author($author_id,$author_page = "",$owner = "",$type_post = "",$widget = "",$class = "",$cover = "",$category = "",$show_icon = "",$specific_time = "",$group = "",$group_type = "approve_decline",$blocked_users = array(),$group_moderators = array(),$group_author = "") {
		$type_post = ($category !== ""?"points":$type_post);
		if (isset($author_id) && $author_id > 0) {
			$gender = get_the_author_meta('gender',$author_id);
			$gender_class = ($gender !== ''?($gender == "male" || $gender == 1?"him":"").($gender == "female" || $gender == 2?"her":"").($gender == "other" || $gender == 3?"other":""):'');
			if ($cover == "") {
				$active_points = wpqa_options("active_points");
				if ($author_page == "grid" || $author_page == "grid_pop" || $author_page == "small" || $author_page == "simple_follow" || $author_page == "columns" || $author_page == "columns_pop") {
					/* questions */
					$questions_count = ($category !== ""?wpqa_count_posts_by_user($author_id,array(wpqa_questions_type,wpqa_asked_questions_type),"publish",$category):wpqa_count_posts_meta(wpqa_questions_type,$author_id));

					/* answers */
					$answers_count = wpqa_count_comments_meta(wpqa_questions_type,$author_id);
					
					/* the_best_answer */
					$the_best_answer = wpqa_count_best_answers_meta($author_id);
					
					/* points */
					$start_of_week = get_option("start_of_week");
					if ($start_of_week == 0) {
						$start_of_week = "Sunday";
					}else if ($start_of_week == 1) {
						$start_of_week = "Monday";
					}else if ($start_of_week == 2) {
						$start_of_week = "Tuesday";
					}else if ($start_of_week == 3) {
						$start_of_week = "Wednesday";
					}else if ($start_of_week == 4) {
						$start_of_week = "Thursday";
					}else if ($start_of_week == 5) {
						$start_of_week = "Friday";
					}else if ($start_of_week == 6) {
						$start_of_week = "Saturday";
					}
					$points_type = "points";
					if ($specific_time == "day") {
						$points_type = "points_date_".date("j-n-Y");
					}else if ($specific_time == "week") {
						$points_type = "points_date_".date("Y-m-d H:i:s",strtotime($start_of_week.' this week'));
					}else if ($specific_time == "month") {
						$points_type = "points_date_".date("n-Y");
					}else if ($specific_time == "year") {
						$points_type = "points_date_".date("Y");
					}
					$points = (int)get_user_meta($author_id,$points_type,true);
					if ($category !== "") {
						$points_type = "points_category".$category;
						if ($specific_time == "day") {
							$points_type = "points_category".$category."_date_".date("j-n-Y");
						}else if ($specific_time == "week") {
							$points_type = "points_category".$category."_date_".date("Y-m-d H:i:s",strtotime($start_of_week.' this week'));
						}else if ($specific_time == "month") {
							$points_type = "points_category".$category."_date_".date("n-Y");
						}else if ($specific_time == "year") {
							$points_type = "points_category".$category."_date_".date("Y");
						}
						$points_category_user = ($category !== ""?(int)get_user_meta($author_id,$points_type,true):"");
						$points = $points_category_user;
					}
					
					/* posts */
					$posts_count = wpqa_count_posts_meta("post",$author_id);
					
					/* comments */
					$comments_count = wpqa_count_comments_meta("post",$author_id);
				}
			}
			$author_display_name = get_the_author_meta("display_name",$author_id);
			$out = '<div class="post-section user-area user-area-'.$author_page.($author_page == "normal" || $author_page == "small_grid" || $author_page == "grid" || $author_page == "columns"?" card member-card text-center mb-2rem":"").($author_page == "simple_follow" || $author_page == "small"?" community-card community-card-layout3 d-flex flex-wrap justify-content-between":"").($author_page == "advanced"?" user-advanced":"").($class != ""?" ".$class:"").($gender_class != ""?" ".$gender_class."-user":"").($show_icon == 'on' && $widget == 'widget'?" widget-icon-user":" widget-not-icon-user").'">
				<div class="post-inner member__info community__info">';
					if ($cover == "") {
						if ((has_himer() || has_knowly()) && ($author_page == "small_grid" || $author_page == "grid" || $author_page == "columns" || $author_page == "columns_pop")) {
							$get_badge = wpqa_get_badge($author_id);
							if ($get_badge != "") {
								$out .= '<div class="d-flex justify-content-between align-items-center mb-1">
									'.$get_badge.'
								</div>';
							}else {
								$out .= '<div class="mb-4"></div>';
							}
						}
						if ($author_page == "advanced") {
							$out .= '<div class="user-head-area">';
						}
						if ($author_page == "advanced") {
							$message_button = wpqa_message_button($author_id,$cover,$owner);
							$out .= $message_button;
						}
						if ((has_himer() || has_knowly()) && ($author_page == "simple_follow" || $author_page == "small")) {
							$out .= '<div class="d-flex align-items-center">
								<div class="mr-3">';
						}
						
						$out .= wpqa_get_avatar_link(array("user_id" => $author_id,"user_name" => $author_display_name,"size" => apply_filters("wpqa_filter_avatar_size",($author_page == "small"?42:((has_discy() && $author_page == "columns") || $author_page == "columns_pop"?70:84)),$author_page,$class),"span" => "span","class" => "rounded-circle"));
						if ((has_himer() || has_knowly()) && ($author_page == "simple_follow" || $author_page == "small")) {
								$out .= '</div>
							<div>';
						}
						if ($author_page == "advanced" && (isset($message_button) && $message_button != "") && !$owner) {
							if ($class == "blocking") {
								$out .= wpqa_blocking($author_id,"",$owner);
							}else {
								$out .= wpqa_following($author_id,"",$owner,"login","","btn btn__semi__height btn__success","btn btn__semi__height btn__danger");
							}
						}
						if ($author_page == "advanced") {
							$out .= '</div>';
						}
					}
					$credential = get_the_author_meta('profile_credential',$author_id);
					$privacy_credential = wpqa_check_user_privacy($author_id,"credential");
					$out .= '<div class="user-content">
						<div class="user-inner">';
							if ($author_page == "columns" || $author_page == "columns_pop") {
								$out .= '<div class="user-data-columns">';
							}
							
							if ($cover == "") {
								$out .= '<h4 class="member__name mb-1"><a href="'.esc_url(wpqa_profile_url($author_id)).'">'.$author_display_name.'</a>'.wpqa_verified_user($author_id).'</h4>';
								if ((has_himer() || has_knowly()) && ($author_page == "simple_follow" || $author_page == "small")) {
											$out .= '
										</div>
									</div>';
								}
								if ((has_himer() || has_knowly()) && ($author_page == "grid" || $author_page == "columns" || $author_page == "columns_pop")) {
									$country = get_the_author_meta('country',$author_id);
									$city    = get_the_author_meta('city',$author_id);
									$get_countries = apply_filters('wpqa_get_countries',false);
									$privacy_country = wpqa_check_user_privacy($author_id,"country");
									$privacy_city = wpqa_check_user_privacy($author_id,"city");
									$privacy_credential = wpqa_check_user_privacy($author_id,"credential");
									if (($privacy_credential == true && isset($credential) && $credential != "") || ($privacy_city == true && isset($city) && $city != "") || ($privacy_country == true && isset($country) && $country != "" && isset($get_countries[$country]))) {
										if ($privacy_credential == true && isset($credential) && $credential != "") {
											$out .= '<span class="profile-credential member__job">'.esc_html($credential).'</span>';
										}else if (($privacy_city == true && isset($city) && $city != "") || ($privacy_country == true && isset($country) && $country != "" && isset($get_countries[$country]))) {
											$out .= '<span class="profile-credential member__job">'.(isset($city) && $city != ""?esc_html($city).", ":"").(isset($country) && $country != "" && isset($get_countries[$country])?$get_countries[$country]:"").'</span>';
										}
									}
								}
								if (has_discy() && $privacy_credential == true && $credential != "" && $author_page != "small_grid" && $author_page != "grid" && $author_page != "grid_pop" && $author_page != "small" && $author_page != "simple_follow" && $author_page != "columns" && $author_page != "columns_pop") {
									$out .= '<span class="profile-credential member__job">'.esc_html($credential).'</span>';
								}
								
								if (has_discy() && $author_page != "grid_pop" && $author_page != "small" && $author_page != "columns_pop") {
									$active_points_category = wpqa_options("active_points_category");
									if ($active_points_category != "on") {
										$out .= wpqa_get_badge($author_id);
									}
								}
								
								if (has_discy() && $author_page == "columns_pop") {
									$country = get_the_author_meta('country',$author_id);
									$city    = get_the_author_meta('city',$author_id);
									$get_countries = apply_filters('wpqa_get_countries',false);
									$privacy_country = wpqa_check_user_privacy($author_id,"country");
									$privacy_city = wpqa_check_user_privacy($author_id,"city");
									$privacy_credential = wpqa_check_user_privacy($author_id,"credential");
									if (($privacy_credential == true && isset($credential) && $credential != "") || ($privacy_city == true && isset($city) && $city != "") || ($privacy_country == true && isset($country) && $country != "" && isset($get_countries[$country]))) {
										$out .= '<div class="user-data">
											<ul>';
												if ($privacy_credential == true && isset($credential) && $credential != "") {
													$out .= '<li class="profile-credential">
														'.esc_html($credential).'
													</li>';
												}else if (($privacy_city == true && isset($city) && $city != "") || ($privacy_country == true && isset($country) && $country != "" && isset($get_countries[$country]))) {
													$out .= '<li class="city-country">
														<i class="icon-location"></i>
														'.(isset($city) && $city != ""?esc_html($city).", ":"").(isset($country) && $country != "" && isset($get_countries[$country])?$get_countries[$country]:"").'
													</li>';
												}
											$out .= '</ul>
										</div>';
									}
								}
								
								if ($author_page == "columns" || $author_page == "columns_pop") {
									$out .= '</div>';
								}
							}
							
							if ($author_page != "small_grid" && $author_page != "grid" && $author_page != "grid_pop" && $author_page != "small" && $author_page != "simple_follow" && $author_page != "columns" && $author_page != "columns_pop") {
								$meta_description = get_user_meta($author_id,"description",true);
								if ($meta_description != "") {
									$privacy_bio = wpqa_check_user_privacy($author_id,"bio");
									if ($privacy_bio != "") {
										$bio_editor = wpqa_options("bio_editor");
										if ($bio_editor == "on") {
											$out .= '<div class="bio_editor member__bio mb-0">'.$meta_description.'</div>';
										}else {
											$out .= '<p class="member__bio mb-0">'.nl2br($meta_description).'</p>';
										}
									}
								}
							}
							
							if ($author_page == "advanced") {
								/* user data */
								$country    = get_the_author_meta('country',$author_id);
								$city       = get_the_author_meta('city',$author_id);
								$age        = get_the_author_meta('age',$author_id);
								$phone      = get_the_author_meta('phone',$author_id);
								$url        = get_the_author_meta('url',$author_id);
								$credential = get_the_author_meta('profile_credential',$author_id);
								$privacy_country = wpqa_check_user_privacy($author_id,"country");
								$privacy_city = wpqa_check_user_privacy($author_id,"city");
								$privacy_age = wpqa_check_user_privacy($author_id,"age");
								$privacy_phone = wpqa_check_user_privacy($author_id,"phone");
								$privacy_gender = wpqa_check_user_privacy($author_id,"gender");
								$privacy_credential = wpqa_check_user_privacy($author_id,"credential");
								$privacy_website = wpqa_check_user_privacy($author_id,"website");
								if (($privacy_credential == true && isset($credential) && $credential != "") || ($privacy_city == true && isset($city) && $city != "") || ($privacy_country == true && isset($country) && $country != "") || ($privacy_phone == true && isset($phone) && $phone != "") || ($privacy_website == true && isset($url) && $url != "") || ($privacy_gender == true && isset($gender) && $gender != "") || ($privacy_age == true && isset($age) && $age != "")) {
									$out .= '<div class="user-data">
										<ul class="info__list list-unstyled mb-0 mt-3 d-flex justify-content-center align-items-center">';
											$out .= apply_filters("wpqa_add_user_data_filter",false,$author_id);
											$get_countries = apply_filters('wpqa_get_countries',false);
											if (($privacy_city == true && isset($city) && $city != "") || ($privacy_country == true && isset($country) && $country != "" && isset($get_countries[$country]))) {
												$out .= '<li class="city-country info__item">
													<i class="info__icon icon-location"></i>
													<span class="info__text">'.($privacy_city == true && isset($city) && $city != ""?esc_html($city).", ":"").($privacy_country == true && isset($country) && $country != "" && isset($get_countries[$country])?$get_countries[$country]:"").'</span>
												</li>';
											}
											$show_phone_profile = apply_filters("wpqa_show_phone_profile",true);
											if ($privacy_phone == true && isset($phone) && $phone != "" && $show_phone_profile == true) {
												$out .= '<li class="user-phone info__item">
													<i class="info__icon icon-phone"></i>
													<span class="info__text">'.apply_filters("wpqa_show_phone",esc_html($phone),$author_id).'</span>
												</li>';
											}
											if ($privacy_website == true && isset($url) && $url != "") {
												$out .= '<li class="user-url info__item">
													<a rel="nofollow" href="'.esc_url($url).'">
														<i class="info__icon icon-link"></i>
														<span class="info__text">'.esc_html__("Visit site","wpqa").'</span>
													</a>
												</li>';
											}
											if ($privacy_gender == true && isset($gender) && $gender != "") {
												$out .= '<li class="user-gender info__item">
													<i class="info__icon icon-heart"></i>
													<span class="info__text">'.($gender == "male" || $gender == 1?esc_html__("Male","wpqa"):"").($gender == "female" || $gender == 2?esc_html__("Female","wpqa"):"").($gender == "other" || $gender == 3?esc_html__("Other","wpqa"):"").'</span>
												</li>';
											}
											if ($privacy_age == true && isset($age) && $age != "") {
												$age = (date_create($age)?date_diff(date_create($age),date_create('today'))->y:"");
												$out .= '<li class="user-age info__item">
													<i class="info__icon icon-globe"></i>
													<span class="info__text">'.esc_html($age)." ".esc_html__("years old","wpqa").'</span>
												</li>';
											}
											if ($cover == "") {
												$author_visits = wpqa_options("author_visits");
												if ($author_visits == "on") {
													$author_stats = wpqa_get_post_stats(0,$author_id);
													$out .= '<li class="user-visits info__item">
														<i class="info__icon icon-eye"></i>
														<span class="info__text">'.wpqa_count_number($author_stats)." "._n("Visit","Visits",$author_stats,"wpqa").'</span>
													</li>';
												}
											}
											$out .= apply_filters("wpqa_profile_after_visits",false,$author_id).
										'</ul>
									</div><!-- End user-data -->';
								}
								$out .= apply_filters("wpqa_profile_advanced_filter",false,$author_id);
							}
							
							if ($author_page == "grid" || $author_page == "grid_pop" || $author_page == "small" || $author_page == "simple_follow") {
								$out .= '<div class="user-data">
									<ul class="member__stats list-unstyled mb-0 d-flex">';
										if ($type_post == "post" || $type_post == "comments") {
											if ((($show_icon == 'on' && $widget == 'widget' && $type_post == "post") || $show_icon != "on")) {
												$out .= '<li class="user-posts stats__item community__count">
													<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"posts")).'">
														'.($widget == 'widget' && $show_icon != 'on'?'':'<i class="icon-book-open"></i>').'
														<span class="stats__count">'.wpqa_count_number($posts_count).' </span><span class="stats__text">'._n("Post","Posts",$posts_count,"wpqa").'</span>
													</a>
												</li>';
											}
											if ((($show_icon == 'on' && $widget == 'widget' && $type_post == "comments") || $show_icon != "on")) {
												$out .= '<li class="user-comments stats__item community__count">
													<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"comments")).'">
														'.($widget == 'widget' && $show_icon != 'on'?'':'<i class="icon-comment"></i>').'
														<span class="stats__count">'.wpqa_count_number($comments_count).' </span><span class="stats__text">'._n("Comment","Comments",$comments_count,"wpqa").'</span>
													</a>
												</li>';
											}
										}else {
											if ((($show_icon == 'on' && $widget == 'widget' && $type_post == "question_count") || $show_icon != "on")) {
												$out .= '<li class="user-questions stats__item community__count">
													<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"questions")).'">
														'.($widget == 'widget' && $show_icon != 'on'?'':'<i class="icon-book-open"></i>').'
														<span class="stats__count">'.wpqa_count_number($questions_count).' </span><span class="stats__text">'._n("Question","Questions",$questions_count,"wpqa").'</span>
													</a>
												</li>';
											}
											if ($type_post == "the_best_answer" && (($show_icon == 'on' && $widget == 'widget') || $show_icon != "on")) {
												$out .= '<li class="user-best-answers stats__item community__count">
													<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"best_answers")).'">
														'.($widget == 'widget' && $show_icon != 'on'?'':'<i class="icon-graduation-cap"></i>').'
														<span class="stats__count">'.($the_best_answer == ""?0:wpqa_count_number($the_best_answer)).' </span><span class="stats__text">'._n("Best Answer","Best Answers",$the_best_answer,"wpqa").'</span>
													</a>
												</li>';
											}else if ($type_post == "points" && $active_points == "on" && (($show_icon == 'on' && $widget == 'widget') || $show_icon != "on")) {
												$out .= '<li class="user-points stats__item community__count">
													<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"points")).'">
														'.($widget == 'widget' && $show_icon != 'on'?'':'<i class="icon-bucket"></i>').'
														<span class="stats__count">'.($points == ""?0:wpqa_count_number($points)).' </span><span class="stats__text">'._n("Point","Points",$points,"wpqa").'</span>
													</a>
												</li>';
											}else if ((($show_icon == 'on' && $widget == 'widget' && $type_post == "answers") || $show_icon != "on")) {
												$out .= '<li class="user-answers stats__item community__count">
													<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"answers")).'">
														'.($widget == 'widget' && $show_icon != 'on'?'':'<i class="icon-comment"></i>').'
														<span class="stats__count">'.wpqa_count_number($answers_count).' </span><span class="stats__text">'._n("Answer","Answers",$answers_count,"wpqa").'</span>
													</a>
												</li>';
											}
											if ((has_himer() || has_knowly()) && ($author_page == "grid" || $author_page == "grid_pop" || $author_page == "columns")) {
												$following_you = get_user_meta($author_id,"following_you",true);
												$following_you = (is_array($following_you) && !empty($following_you)?get_users(array('fields' => 'ID','include' => $following_you,'orderby' => 'registered')):array());
												$user_follwers = (int)(is_array($following_you)?count($following_you):0);
												$out .= '<li class="user-followers stats__item community__count">
													<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"followers")).'">
														'.($widget == 'widget' && $show_icon != 'on'?'':'<i class="icon-comment"></i>').'
														<span class="stats__count">'.wpqa_count_number($user_follwers).' </span><span class="stats__text">'._n("Follower","Followers",$user_follwers,"wpqa").'</span>
													</a>
												</li>';
											}
										}
									$out .= '</ul>
								</div><!-- End user-data -->';
								
								if ($widget == "widget") {
									if ($category !== "") {
										$out .= apply_filters("wpqa_widget_before_badge",false,$category);
									}
									$out .= wpqa_get_badge($author_id,"",(isset($points_category_user) && $points_category_user !== ""?$points_category_user:""));
								}
							}
							
							if (($author_page == "small_grid" || (has_discy() && $author_page == "simple_follow")) && !$owner) {
								if ($class == "blocking") {
									$out .= wpqa_blocking($author_id,($author_page == "small_grid"?"style_4":""),$owner,"member__actions d-flex justify-content-center align-items-center","btn btn__semi__height btn__success","btn btn__semi__height btn__danger");
								}else {
									$out .= wpqa_following($author_id,($author_page == "small_grid"?"style_4":""),$owner,"","member__actions d-flex justify-content-center align-items-center","btn btn__semi__height btn__success","btn btn__semi__height btn__danger");
								}
							}
						$out .= '</div>';
						
						if ($author_page != "small_grid" && $author_page != "grid" && $author_page != "grid_pop" && $author_page != "small" && $author_page != "simple_follow" && $author_page != "columns" && $author_page != "columns_pop") {
							$twitter    = get_the_author_meta('twitter',$author_id);
							$facebook   = get_the_author_meta('facebook',$author_id);
							$tiktok     = get_the_author_meta('tiktok',$author_id);
							$linkedin   = get_the_author_meta('linkedin',$author_id);
							$youtube    = get_the_author_meta('youtube',$author_id);
							$vimeo      = get_the_author_meta('vimeo',$author_id);
							$pinterest  = get_the_author_meta('pinterest',$author_id);
							$instagram  = get_the_author_meta('instagram',$author_id);
							$user_email = get_the_author_meta('email',$author_id);
							$privacy_email = wpqa_check_user_privacy($author_id,"email");
							$privacy_social = wpqa_check_user_privacy($author_id,"social");

							$get_current_user_id = get_current_user_id();
							$is_super_admin      = is_super_admin($get_current_user_id);
							$active_moderators   = wpqa_options("active_moderators");
							if ($active_moderators == "on") {
								$moderator_categories = get_user_meta($get_current_user_id,prefix_author."moderator_categories",true);
								$moderator_categories = (is_array($moderator_categories) && !empty($moderator_categories)?$moderator_categories:array());
								$pending_posts = (($is_super_admin || $active_moderators == "on") && ($is_super_admin || (isset($moderator_categories) && is_array($moderator_categories) && !empty($moderator_categories)))?true:false);
								$moderators_permissions = wpqa_user_moderator($get_current_user_id);
								$if_user_id = get_user_by("id",$author_id);
								$user_group = wpqa_get_user_group($if_user_id);
								$moderators_available = ($is_super_admin || ($user_group != "administrator" && $pending_posts == true && isset($moderators_permissions['ban']) && $moderators_permissions['ban'] == "ban")?true:false);
							}
							$block_users = wpqa_options("block_users");
							$cover_image = wpqa_options("cover_image");
							$report_users = wpqa_options("report_users");
							if (($block_users == "on" && $cover_image != "on") || (isset($moderators_available) && $moderators_available == true) || ($privacy_email == true && $user_email != "") || ($privacy_social == true && ($facebook || $tiktok || $twitter || $linkedin || $youtube || $vimeo || $pinterest || $instagram)) || ($cover == "" && (!isset($message_button) || (isset($message_button) && $message_button == "")))) {
								$out .= '<div class="social-ul">
									<ul class="social-icons list-unstyled mb-0 mt-4 d-flex align-items-center justify-content-center">';
										if ($author_page != "single-author" && $author_page != "normal" && ($cover == "" && (!isset($message_button) || (isset($message_button) && $message_button == ""))) && !$owner) {
											$out .= '<li class="social-follow">'.($class == "blocking"?wpqa_blocking($author_id,"style_3",$owner):wpqa_following($author_id,"style_3",$owner)).'</li>';
										}
										if ($author_page != "single-author" && $cover == "" && !is_super_admin($author_id) && ((isset($moderators_available) && $moderators_available == true) || $block_users == "on")) {
											if (!$owner && isset($moderators_available) && $moderators_available == true) {
												$if_ban = (isset($if_user_id->caps["ban_group"]) && $if_user_id->caps["ban_group"] == 1?true:false);
												$out .= "<li class='ban-unban-user'><span class='small_loader loader_2'></span><a class='".($if_ban?"unban-user btn btn__secondary":"ban-user btn btn__primary")."' data-nonce='".wp_create_nonce("ban_nonce")."' href='#' data-id='".$author_id."'><span>".($if_ban?esc_html__("Unban user","wpqa"):esc_html__("Ban user","wpqa"))."</span></a></li>";
											}
											if ($block_users == "on" && !$owner && $get_current_user_id > 0) {
												$get_block_users = get_user_meta($get_current_user_id,"wpqa_block_users",true);
												$if_block = (is_array($get_block_users) && !empty($get_block_users) && in_array($author_id,$get_block_users)?true:false);
												$out .= "<li class='block-unblock-user'><span class='small_loader loader_2'></span><a class='".($if_block?"unblock-user":"block-user")."' data-nonce='".wp_create_nonce("block_nonce")."' href='#' data-id='".$author_id."'><span>".($if_block?esc_html__("Unblock user","wpqa"):esc_html__("Block user","wpqa"))."</span></a></li>";
											}
										}
										if ($privacy_social == true) {
											if ($facebook) {
												$out .= '<li class="social-facebook"><a rel="nofollow" title="Facebook" class="tooltip-n" href="'.esc_url($facebook).'" target="_blank"><i class="icon-facebook"></i></a></li>';
											}
											if ($twitter) {
												$out .= '<li class="social-twitter"><a rel="nofollow" title="Twitter" class="tooltip-n" href="'.esc_url($twitter).'" target="_blank"><i class="icon-twitter"></i></a></li>';
											}
											if ($tiktok) {
												$out .= '<li class="social-tiktok"><a rel="nofollow" title="TikTok" class="tooltip-n" href="'.esc_url($tiktok).'" target="_blank"><i class="fab fa-tiktok"></i></a></li>';
											}
											if ($linkedin) {
												$out .= '<li class="social-linkedin"><a rel="nofollow" title="Linkedin" class="tooltip-n" href="'.esc_url($linkedin).'" target="_blank"><i class="icon-linkedin"></i></a></li>';
											}
											if ($pinterest) {
												$out .= '<li class="social-pinterest"><a rel="nofollow" title="Pinterest" class="tooltip-n" href="'.esc_url($pinterest).'" target="_blank"><i class="icon-pinterest"></i></a></li>';
											}
											if ($instagram) {
												$out .= '<li class="social-instagram"><a rel="nofollow" title="Instagram" class="tooltip-n" href="'.esc_url($instagram).'" target="_blank"><i class="icon-instagram"></i></a></li>';
											}
											if ($youtube) {
												$out .= '<li class="social-youtube"><a rel="nofollow" title="Youtube" class="tooltip-n" href="'.esc_url($youtube).'" target="_blank"><i class="icon-play"></i></a></li>';
											}
											if ($vimeo) {
												$out .= '<li class="social-vimeo"><a rel="nofollow" title="Vimeo" class="tooltip-n" href="'.esc_url($vimeo).'" target="_blank"><i class="icon-vimeo"></i></a></li>';
											}
										}
										if ($privacy_email == true && $user_email != "") {
											$out .= '<li class="social-email"><a title="'.esc_html__("Email","wpqa").'" class="tooltip-n" href="mailto:'.esc_attr($user_email).'" target="_blank" rel="nofollow"><i class="icon-mail"></i></a></li>';
										}
										if ($author_page != "single-author" && $cover == "" && $report_users == "on" && !$owner && $get_current_user_id > 0) {
											$out .= "<li class='report_activated report-user-li'><span class='small_loader loader_2'></span><a class='report_user' href='".$author_id."'><span>".esc_html__("Report user","wpqa")."</span></a></li>";
										}
									$out .= '</ul>
								</div><!-- End social-ul -->';
							}
						}
					$out .= '</div><!-- End user-content -->';
					
					if ($author_page == "grid_pop" && !$owner) {
						if ($class == "blocking") {
							$out .= wpqa_blocking($author_id,"",$owner);
						}else {
							$out .= wpqa_following($author_id,"",$owner);
						}
					}
					
					if ($cover == "" && $author_page != "single-author" && $author_page != "small_grid" && $author_page != "grid" && $author_page != "grid_pop" && $author_page != "small" && $author_page != "simple_follow" && $author_page != "columns" && $author_page != "columns_pop") {
						$breadcrumbs = wpqa_options("breadcrumbs");
						$breadcrumbs_author = wpqa_options("breadcrumbs_author");
						if ($breadcrumbs == "on" && $breadcrumbs_author == "on") {
							$show_breadcrumbs = true;
						}
						if (!isset($show_breadcrumbs)) {
							$ask_question_to_users = wpqa_options("ask_question_to_users");
							if (!$owner && $ask_question_to_users == "on") {
								$out .= '<div class="ask-question ask-user-after-social"><a href="'.esc_url(wpqa_add_question_permalink("user")).'" class="button-default ask-question-user btn btn__info">'.esc_html__("Ask","wpqa")." ".$author_display_name.'</a></div>';
							}
							if ($owner) {
								$out .= '<div class="ask-user-after-social edit-profile-after-social"><a href="'.esc_url(wpqa_edit_profile_permalink()).'" class="button-default btn btn__primary">'.esc_html__("Edit profile","wpqa").'</a></div>';
							}
						}
					}
					
					if ($author_page == "columns" || $author_page == "columns_pop") {
						$out .= '<div class="user-columns-data">
							<ul class="member__stats list-unstyled mb-0 d-flex">';
								if ($type_post == "post" || $type_post == "comments") {
									$out .= '<li class="user-columns-posts stats__item">
										<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"posts")).'">
											<i class="icon-book-open"></i><span class="stats__count">'.($posts_count == ""?0:wpqa_count_number($posts_count)).' </span><span class="stats__text">'._n("Post","Posts",$posts_count,"wpqa").'</span>
										</a>
									</li>
									<li class="user-columns-comments stats__item">
										<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"comments")).'">
											<i class="icon-comment"></i><span class="stats__count">'.($comments_count == ""?0:wpqa_count_number($comments_count)).' </span><span class="stats__text">'._n("Comment","Comments",$comments_count,"wpqa").'</span>
										</a>
									</li>';
								}else {
									$out .= '<li class="user-columns-questions stats__item">
										<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"questions")).'">
											<i class="icon-book-open"></i><span class="stats__count">'.($questions_count == ""?0:wpqa_count_number($questions_count)).' </span><span class="stats__text">'._n("Question","Questions",$questions_count,"wpqa").'</span>
										</a>
									</li>
									<li class="user-columns-answers stats__item">
										<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"answers")).'">
											<i class="icon-comment"></i><span class="stats__count">'.($answers_count == ""?0:wpqa_count_number($answers_count)).' </span><span class="stats__text">'._n("Answer","Answers",$answers_count,"wpqa").'</span>
										</a>
									</li>';
								}
								if (has_discy()) {
									$out .= '<li class="user-columns-best-answers stats__item">
										<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"best_answers")).'">
											<i class="icon-graduation-cap"></i><span class="stats__count">'.($the_best_answer == ""?0:wpqa_count_number($the_best_answer)).' </span><span class="stats__text">'._n("Best Answer","Best Answers",$the_best_answer,"wpqa").'</span>
										</a>
									</li>';
									if ($active_points == "on") {
										$out .= '<li class="user-columns-points stats__item">
											<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"points")).'">
												<i class="icon-bucket"></i><span class="stats__count">'.($points == ""?0:wpqa_count_number($points)).' </span><span class="stats__text">'._n("Point","Points",$points,"wpqa").'</span>
											</a>
										</li>';
									}
								}
								if (has_himer() || has_knowly()) {
									$following_you = get_user_meta($author_id,"following_you",true);
									$following_you = (is_array($following_you) && !empty($following_you)?get_users(array('fields' => 'ID','include' => $following_you,'orderby' => 'registered')):array());
									$user_follwers = (int)(is_array($following_you)?count($following_you):0);
									$out .= '<li class="user-columns-followers stats__item">
										<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"followers")).'">
											<i class="icon-bucket"></i><span class="stats__count">'.wpqa_count_number($user_follwers).' </span><span class="stats__text">'._n("Follower","Followers",$user_follwers,"wpqa").'</span>
										</a>
									</li>';
								}
							$out .= '</ul>
						</div><!-- End user-columns-data -->';
						$out .= '<div class="user-follow-profile">
							<div class="member__actions d-flex justify-content-between">';
								if ($group > 0) {
									$view_profile = '<a class="btn btn__semi__height btn__primary w-100" href="'.wpqa_profile_url($author_id).'">'.esc_html__("View Profile","wpqa").'</a>';
									if ($group_type == "approve_decline") {
										$out .= '<div class="group_approve_decline d-flex justify-content-between">
											<div class="cover_loader wpqa_hide"><div class="small_loader loader_2"></div></div>
											<a href="#" class="button-default approve_request_group btn btn__semi__height btn__success" data-nonce="'.wp_create_nonce("wpqa_group_nonce").'" data-group="'.$group.'" data-user="'.$author_id.'">'.esc_html__("Approve","wpqa").'</a>
											<a href="#" class="button-default decline_request_group btn btn__semi__height btn__danger" data-nonce="'.wp_create_nonce("wpqa_group_nonce").'" data-group="'.$group.'" data-user="'.$author_id.'">'.esc_html__("Decline","wpqa").'</a>
										</div>';
									}else if ($group_type == "block" || $group_type == "profile") {
										if ($group_type == "profile" || !is_user_logged_in() || (isset($group_moderators) && is_array($group_moderators) && in_array($author_id,$group_moderators))) {
											$out .= $view_profile;
										}else {
											$user_id = get_current_user_id();
											if (is_super_admin($user_id) || $group_author == $user_id || ($user_id > 0 && is_array($group_moderators) && in_array($user_id,$group_moderators))) {
												if (isset($blocked_users) && is_array($blocked_users) && in_array($author_id,$blocked_users)) {
													$out .= '<div class="group_unblock d-flex justify-content-between">
														<div class="cover_loader wpqa_hide"><div class="small_loader loader_2"></div></div>
														<a href="#" class="button-default btn btn__semi__height btn__danger unblock_user_group w-100" data-nonce="'.wp_create_nonce("wpqa_group_nonce").'" data-group="'.$group.'" data-user="'.$author_id.'">'.esc_html__("Unblock","wpqa").'</a>';
												}else {
													$out .= '<div class="group_block_remove d-flex justify-content-between">
														<div class="cover_loader wpqa_hide"><div class="small_loader loader_2"></div></div>';
														if (!is_super_admin($author_id)) {
															$out .= '<a href="#" class="button-default remove_user_group btn btn__semi__height btn__primary" data-nonce="'.wp_create_nonce("wpqa_group_nonce").'" data-group="'.$group.'" data-user="'.$author_id.'">'.esc_html__("Remove","wpqa").'</a>
															<a href="#" class="button-default block_user_group btn btn__semi__height btn__danger" data-nonce="'.wp_create_nonce("wpqa_group_nonce").'" data-group="'.$group.'" data-user="'.$author_id.'">'.esc_html__("Block","wpqa").'</a>';
														}else {
															$out .= $view_profile;
														}
												}
												$out .= '</div>';
											}else {
												$out .= $view_profile;
											}
										}
									}else if ($group_type == "moderators") {
										if (!is_super_admin($author_id) && $group_author != $author_id && isset($group_moderators) && is_array($group_moderators) && in_array($author_id,$group_moderators)) {
											$out .= '<a href="#" class="button-default remove_moderator_group btn btn__semi__height btn__danger w-100" data-nonce="'.wp_create_nonce("wpqa_group_nonce").'" data-group="'.$group.'" data-user="'.$author_id.'">'.esc_html__("Remove moderator","wpqa").'</a>';
										}else {
											$out .= $view_profile;
										}
									}
								}else {
									if (!$owner) {
										if ($class == "blocking") {
											$out .= wpqa_blocking($author_id,"style_2",$owner,"","btn btn__semi__height btn__success","btn btn__semi__height btn__danger");
										}else {
											$out .= wpqa_following($author_id,(has_himer() || has_knowly()?"style_3":"style_2"),$owner,"","","btn btn__semi__height btn__success","btn btn__semi__height btn__danger");
										}
									}
									if (has_himer() || has_knowly()) {
										$message_button = wpqa_message_button($author_id,"message_users",$owner);
										$out .= ($message_button != ""?$message_button:'<a class="btn btn__semi__height btn__primary w-100" href="'.wpqa_profile_url($author_id).'">'.esc_html__("View Profile","wpqa").'</a>');
									}else {
										$out .= '<a class="btn btn__semi__height btn__primary" href="'.wpqa_profile_url($author_id).'">'.esc_html__("View Profile","wpqa").'</a>';
									}
								}
							$out .= '</div>
						</div><!-- End user-follow-profile -->';
					}
					
					$out .= '<div class="clearfix"></div>
				</div><!-- End post-inner -->';
				if ((has_himer() || has_knowly()) && $author_page == "simple_follow") {
					$out .= '<div class="community__meta d-flex justify-content-end align-items-center">';
						if (!$owner) {
							if ($class == "blocking") {
								$out .= wpqa_blocking($author_id,"style_3",$owner,"member__actions d-flex justify-content-center align-items-center","btn btn__semi__height btn__success","btn btn__semi__height btn__danger");
							}else {
								$out .= wpqa_following($author_id,"style_3",$owner,"","member__actions d-flex justify-content-center align-items-center","btn btn__semi__height btn__success","btn btn__semi__height btn__danger");
							}
						}else {
							$out .= '<a class="btn btn__semi__height btn__primary" href="'.wpqa_profile_url($author_id).'">'.esc_html__("View Profile","wpqa").'</a>';
						}
					$out .= '</div>';
				}
				if ((has_himer() || has_knowly()) && $widget == "widget") {
					if (!$owner) {
						$out .= wpqa_following($author_id,"style_1",$owner,"","member__actions d-flex justify-content-center align-items-center","btn btn__no__animation btn__success","btn btn__no__animation btn__danger");
					}
				}
			$out .= '</div><!-- End post -->';
			
			if ($author_page == "grid_pop") {
				$out .= '<div class="user-data">
					<ul>
						<li class="user-best-answers">
							<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"best_answers")).'">
								<i class="icon-graduation-cap"></i>
								'.($the_best_answer == ""?0:wpqa_count_number($the_best_answer)).' '._n("Best Answer","Best Answers",$the_best_answer,"wpqa").'
							</a>
						</li>';
						if ($active_points == "on") {
							$out .= '<li class="user-points">
								<a href="'.esc_url(wpqa_get_profile_permalink($author_id,"points")).'">
									<i class="icon-bucket"></i>
									'.($points == ""?0:wpqa_count_number($points)).' '._n("Point","Points",$points,"wpqa").'
								</a>
							</li>';
						}
					$out .= '</ul>
				</div><!-- End user-data -->';
			}
			
			return $out;
		}
	}
endif;
/* Message button */
if (!function_exists('wpqa_message_button')) :
	function wpqa_message_button($author_id,$text = "",$owner = "",$return = "",$class = "") {
		$out = "";
		$active_message = wpqa_options("active_message");
		if ($active_message == "on" && !$owner) {
			$send_message_no_register = wpqa_options("send_message_no_register");
			$received_message = esc_html(get_user_meta($author_id,'received_message',true));
			$user_id = get_current_user_id();
			$is_super_admin = is_super_admin($user_id);
			$block_message = esc_html(get_user_meta($user_id,'block_message',true));
			$user_block_message = array();
			if (is_user_logged_in()) {
				$user_block_message = get_user_meta($author_id,"user_block_message",true);
				$user_is_login = get_userdata($user_id);
				$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
			}
			$custom_permission = wpqa_options("custom_permission");
			$send_message = wpqa_options("send_message");
			if ($is_super_admin || $custom_permission != "on" || ($custom_permission == "on" && (is_user_logged_in() && !$is_super_admin && isset($roles["send_message"])) || (!is_user_logged_in() && $send_message == "on"))) {
				if ($is_super_admin || (((!is_user_logged_in() && $send_message_no_register == "on") || is_user_logged_in()) && $block_message != "on" && $received_message == "on" && (empty($user_block_message) || (isset($user_block_message) && is_array($user_block_message) && !in_array($user_id,$user_block_message))))) {
					$out .= '<div class="'.($text != ""?'send_message_text':'send_message_icon').'"><a'.($text == "message_users"?' data-user-id="'.$author_id.'"':'').' href="#" title="'.esc_html__("Send Message","wpqa").'" class="wpqa-message'.($text == "message_users"?' send-message-2':'').' tooltip-n btn btn__semi__height btn__primary'.($text != ""?' button-default':'').($class != ""?" ".$class:"").'">'.($text != ""?esc_html__("Message","wpqa"):'<i class="icon-mail"></i>').'</a></div>';
				}
			}
		}
		return $out;
	}
endif;
/* Following */
if (!function_exists('wpqa_following')) :
	function wpqa_following($author_id,$follow_style = "",$owner = "",$login = "",$class = "",$class_follow = "",$class_unfollow = "") {
		$out = "";
		if ((is_user_logged_in() && !$owner) || (!is_user_logged_in() && $login == "login")) {
			if (!is_user_logged_in() && $login == "login") {
				$activate_login = wpqa_options("activate_login");
				$out .= ($activate_login != 'disabled'?'<div class="user_follow"><a href="'.wpqa_login_permalink().'" class="login-panel'.apply_filters('wpqa_pop_up_class_login','').' tooltip-n" title="'.esc_attr__("Login","wpqa").'"><i class="icon-plus"></i></a></div>':'');
			}else {
				$following_me = get_user_meta(get_current_user_id(),"following_me",true);
				if (isset($following_me)) {
					if ($follow_style == "style_2") {
						$following_you = get_user_meta($author_id,"following_you",true);
					}
					$out .= '<div class="user_follow'.($follow_style == "style_2"?"_2":"").($follow_style == "style_3"?"_3":"").($follow_style == "style_4"?"_4":"").(!empty($following_me) && in_array($author_id,$following_me)?($follow_style == "style_4"?" user_follow_done":" user_follow_yes"):"").($class != ""?" ".$class:"").'">
						<div class="small_loader loader_2'.($follow_style == "style_2"?" user_follow_loader":"").'"></div>';
						if (!empty($following_me) && in_array($author_id,$following_me)) {
							$out .= '<a href="#" class="following_not'.($follow_style == "style_2" || $follow_style == "style_3" || $follow_style == "style_4"?"":" tooltip-n").($follow_style == "style_4"?" button-default":"").($class_unfollow != ""?" ".$class_unfollow:"").'"'.($class_follow != ""?" data-follow='".$class_follow."'":"").($class_unfollow != ""?" data-unfollow='".$class_unfollow."'":"").' data-rel="'.(int)$author_id.'" data-nonce="'.wp_create_nonce("wpqa_following_nonce").'" title="'.esc_attr__("Unfollow","wpqa").'">';
								if ($follow_style == "style_2" || $follow_style == "style_3" || $follow_style == "style_4") {
									$out .= '<span class="follow-value">'.esc_html__("Unfollow","wpqa").'</span>';
									if ($follow_style == "style_2") {
										$out .= '<span class="follow-count">'.($following_you == ""?0:wpqa_count_number($following_you)).'</span>';
									}
								}else {
									$out .= '<i class="icon-minus"></i>';
								}
							$out .= '</a>';
						}else {
							$out .= '<a href="#" class="following_you'.($follow_style == "style_2" || $follow_style == "style_3" || $follow_style == "style_4"?"":" tooltip-n").($follow_style == "style_4"?" button-default":"").($class_follow != ""?" ".$class_follow:"").'"'.($class_follow != ""?" data-follow='".$class_follow."'":"").($class_unfollow != ""?" data-unfollow='".$class_unfollow."'":"").' data-rel="'.(int)$author_id.'" data-nonce="'.wp_create_nonce("wpqa_following_nonce").'" title="'.esc_attr__("Follow","wpqa").'">';
							if ($follow_style == "style_2" || $follow_style == "style_3" || $follow_style == "style_4") {
								$out .= '<span class="follow-value">'.esc_html__("Follow","wpqa").'</span>';
								if ($follow_style == "style_2") {
									$out .= '<span class="follow-count">'.($following_you == ""?0:wpqa_count_number($following_you)).'</span>';
								}
							}else {
								$out .= '<i class="icon-plus"></i>';
							}
							$out .= '</a>';
						}
					$out .= '</div>';
				}
			}
		}
		return $out;
	}
endif;
/* Blocking */
if (!function_exists('wpqa_blocking')) :
	function wpqa_blocking($author_id,$block_style = "",$owner = "",$class = "",$class_block = "",$class_unblock = "") {
		$out = "";
		if (is_user_logged_in() && !$owner) {
			$get_block_users = get_user_meta(get_current_user_id(),"wpqa_block_users",true);
			$out .= '<div class="user_block'.($block_style == "style_2"?"_2":"").($block_style == "style_3"?"_3":"").($block_style == "style_4"?"_4":"").(!empty($get_block_users) && in_array($author_id,$get_block_users)?($block_style == "style_4"?" user_block_done":" user_block_yes").($class != ""?" ".$class:""):"").'">
				<div class="small_loader loader_2'.($block_style == "style_2"?" user_block_loader":"").'"></div>';
				if (!empty($get_block_users) && in_array($author_id,$get_block_users)) {
					$out .= '<a href="#" class="unblock-user-page'.($block_style == "style_2" || $block_style == "style_3" || $block_style == "style_4"?"":" tooltip-n").($block_style == "style_4"?" button-default":"").($class_unblock != ""?" ".$class_unblock:"").'" data-nonce="'.wp_create_nonce("block_nonce").'" data-rel="'.(int)$author_id.'" title="'.esc_attr__("Unblock","wpqa").'">';
						if ($block_style == "style_2" || $block_style == "style_3" || $block_style == "style_4") {
							$out .= '<span class="block-value">'.esc_html__("Unblock","wpqa").'</span>';
						}else {
							$out .= '<i class="icon-back"></i>';
						}
					$out .= '</a>';
				}else {
					$out .= '<a href="#" class="block-user-page'.($block_style == "style_2" || $block_style == "style_3" || $block_style == "style_4"?"":" tooltip-n").($block_style == "style_4"?" button-default":"").($class_block != ""?" ".$class_block:"").'" data-rel="'.(int)$author_id.'" data-nonce="'.wp_create_nonce("block_nonce").'" title="'.esc_attr__("Block","wpqa").'">';
					if ($block_style == "style_2" || $block_style == "style_3" || $block_style == "style_4") {
						$out .= '<span class="block-value">'.esc_html__("Block","wpqa").'</span>';
					}else {
						$out .= '<i class="icon-block"></i>';
					}
					$out .= '</a>';
				}
			$out .= '</div>';
		}
		return $out;
	}
endif;
/* Get verified user */
if (!function_exists('wpqa_verified_user')) :
	function wpqa_verified_user($author_id,$return = "") {
		if ($author_id > 0) {
			$verified_user = get_the_author_meta('verified_user',$author_id);
			if ($verified_user == 1 || $verified_user == "on") {
				return '<span class="verified_user tooltip-n" title="'.esc_html__("Verified","wpqa").'"><i class="icon-check"></i></span>';
			}
		}
	}
endif;
/* Get badge */
if (!function_exists('wpqa_get_badge')) :
	function wpqa_get_badge($author_id,$return = "",$points = "",$category_points = "") {
		$custom_user_badge = get_user_meta($author_id,"custom_user_badge",true);
		if ($custom_user_badge == "on") {
			$custom_badge_name = get_user_meta($author_id,"custom_badge_name",true);
			$custom_badge_color = get_user_meta($author_id,"custom_badge_color",true);
		}
		if ($custom_user_badge == "on" && $custom_badge_name != "" && $custom_badge_color != "") {
			if ($return == "points") {
				return 0;
			}else if ($return == "color") {
				return $custom_badge_color;
			}else if ($return == "name") {
				return strip_tags(stripslashes($custom_badge_name),"<i>");
			}else if ($return == "key") {
				return 0;
			}else if ($return == "first_key") {
				return "";
			}else {
				return apply_filters('wpqa_by_points','<span class="badge-span" style="background-color: '.$custom_badge_color.'">'.strip_tags(stripslashes($custom_badge_name),"<i>").'</span>',$author_id);
			}
		}else {
			if ($category_points == "category_points") {
				$active_points_category = wpqa_options("active_points_category");
				if ($active_points_category == "on") {
					$categories_user_points = get_user_meta($author_id,"categories_user_points",true);
					if (is_array($categories_user_points) && !empty($categories_user_points)) {
						foreach ($categories_user_points as $category) {
							$points_category_user[$category] = (int)get_user_meta($author_id,"points_category".$category,true);
						}
						arsort($points_category_user);
						$first_category = (is_array($points_category_user)?key($points_category_user):"");
						$first_points = reset($points_category_user);
					}
				}
				$points = (isset($first_points)?$first_points:$points);
			}
			$badges_style = wpqa_options("badges_style");
			$author_id = (int)$author_id;
			if ($badges_style == "by_groups_points") {
				if ($author_id > 0) {
					$points = (int)($points !== ""?$points:get_user_meta($author_id,"points",true));
					$badges_groups_points = wpqa_options("badges_groups_points");
					$badges_groups_points = (is_array($badges_groups_points) && !empty($badges_groups_points)?$badges_groups_points:array());
					$points_badges = array_column($badges_groups_points,'badge_points');
					$points_badges = (is_array($points_badges) && !empty($points_badges)?$points_badges:array());
					if (is_array($points_badges) && !empty($points_badges) && is_array($badges_groups_points) && !empty($badges_groups_points)) {
						array_multisort($points_badges,SORT_ASC,$badges_groups_points);
					}
					$user_info = get_userdata($author_id);
					$group_key = wpqa_get_user_group($user_info);
					if (isset($badges_groups_points) && is_array($badges_groups_points)) {
						$badges_groups_points = array_values($badges_groups_points);
						foreach ($badges_groups_points as $badges_k => $badges_v) {
							if ($badges_v["badge_group"] == $group_key) {
								$badges_points[] = $badges_v;
							}
							
						}
						if (isset($badges_points) && is_array($badges_points)) {
							foreach ($badges_points as $key => $badge_point) {
								if ($points >= $badge_point["badge_points"]) {
									$last_key = $key;
								}
							}
						}
						if (isset($last_key)) {
							$badge_key = $last_key;
							if ($return == "points") {
								return (isset($badges_points[$badge_key]["badge_points"])?$badges_points[$badge_key]["badge_points"]:"");
							}else if ($return == "color") {
								return (isset($badges_points[$badge_key]["badge_color"])?$badges_points[$badge_key]["badge_color"]:"");
							}else if ($return == "name") {
								return (isset($badges_points[$badge_key]["badge_name"])?strip_tags(stripslashes($badges_points[$badge_key]["badge_name"]),"<i>"):"");
							}else if ($return == "key") {
								return $badge_key;
							}else if ($return == "first_key") {
								$first_badge = (isset($badges_points) && is_array($badges_points)?reset($badges_points):array());
								return (isset($first_badge['badge_points']) && $first_badge['badge_points'] == $badges_points[$badge_key]["badge_points"]?$badge_key:"");
							}else {
								return apply_filters('wpqa_by_groups_points','<span class="badge-span" style="background-color: '.(isset($badges_points[$badge_key]["badge_color"])?$badges_points[$badge_key]["badge_color"]:"").'">'.(isset($badges_points[$badge_key]["badge_name"])?strip_tags(stripslashes($badges_points[$badge_key]["badge_name"]),"<i>"):"").'</span>',$author_id);
							}
						}
					}
				}
			}else if ($badges_style == "by_groups") {
				if ($author_id > 0) {
					$badges_groups = wpqa_options("badges_groups");
					$badges_groups = (is_array($badges_groups) && !empty($badges_groups)?$badges_groups:array());
					$points_badges = array_column($badges_groups,'badge_points');
					$points_badges = (is_array($points_badges) && !empty($points_badges)?$points_badges:array());
					if (is_array($points_badges) && !empty($points_badges) && is_array($badges_groups) && !empty($badges_groups)) {
						array_multisort($points_badges,SORT_ASC,$badges_groups);
					}
					$user_info = get_userdata($author_id);
					$group_key = wpqa_get_user_group($user_info);
					if (isset($badges_groups) && is_array($badges_groups)) {
						global $wp_roles;
						$badges_groups = (is_array($badges_groups) && !empty($badges_groups)?array_values($badges_groups):array());
						$found_key = trim(array_search($group_key,array_column($badges_groups,'badge_name')));
						$user_group = $wp_roles->roles[$group_key]["name"];
						if ($return == "color") {
							return $badges_groups[$found_key]["badge_color"];
						}else if ($return == "name") {
							return translate_user_role($user_group);
						}else if ($return == "key") {
							return $found_key;
						}else if ($return == "first_key") {
							$first_badge = (isset($badges_groups) && is_array($badges_groups)?reset($badges_groups):array());
							return (isset($first_badge['badge_points']) && $first_badge['badge_points'] == $badges_groups[$found_key]["badge_points"]?$found_key:"");
						}else if (isset($badges_groups[$found_key]) && isset($badges_groups[$found_key]["badge_color"])) {
							return apply_filters('wpqa_by_groups','<span class="badge-span" style="background-color: '.$badges_groups[$found_key]["badge_color"].'">'.translate_user_role($user_group).'</span>',$author_id);
						}
					}
				}
			}else if ($badges_style == "by_questions") {
				if ($author_id > 0) {
					//$questions = ($category !== ""?wpqa_count_posts_by_user($author_id,array(wpqa_questions_type,wpqa_asked_questions_type),"publish",$category):wpqa_count_posts_meta(wpqa_questions_type,$author_id));
					$questions = wpqa_count_posts_meta(wpqa_questions_type,$author_id);
					$badges = wpqa_options("badges_questions");
					if (is_array($badges) && !empty($badges)) {
						$badges = array_values($badges);
						$questions_badges = array_column($badges,'badge_questions');
					}
					$questions_badges = (isset($questions_badges) && is_array($questions_badges) && !empty($questions_badges)?$questions_badges:array());
					if (is_array($questions_badges) && !empty($questions_badges) && is_array($badges) && !empty($badges)) {
						array_multisort($questions_badges,SORT_ASC,$badges);
					}
					if (isset($badges) && is_array($badges)) {
						foreach ($badges as $badges_k => $badges_v) {
							$badges_questions[] = $badges_v["badge_questions"];
						}
						if (isset($badges_questions) && is_array($badges_questions)) {
							foreach ($badges_questions as $key => $badge_question) {
								if ($questions >= $badge_question) {
									$last_key = $key;
								}
							}
						}
						if (isset($last_key)) {
							$badge_key = $last_key;
							if ($return == "points") {
								return $badges[$badge_key]["badge_questions"];
							}else if ($return == "color") {
								return $badges[$badge_key]["badge_color"];
							}else if ($return == "name") {
								return strip_tags(stripslashes($badges[$badge_key]["badge_name"]),"<i>");
							}else if ($return == "key") {
								return $badge_key;
							}else if ($return == "first_key") {
								$first_badge = (isset($badges) && is_array($badges)?reset($badges):array());
								return (isset($first_badge['badge_questions']) && $first_badge['badge_questions'] == $badges[$badge_key]["badge_questions"]?$badge_key:"");
							}else {
								return apply_filters('wpqa_by_questions','<span class="badge-span" style="background-color: '.$badges[$badge_key]["badge_color"].'">'.strip_tags(stripslashes($badges[$badge_key]["badge_name"]),"<i>").'</span>',$author_id);
							}
						}
					}
				}
			}else if ($badges_style == "by_answers") {
				if ($author_id > 0) {
					$answers = wpqa_count_comments_meta(wpqa_questions_type,$author_id);
					$badges = wpqa_options("badges_answers");
					if (is_array($badges) && !empty($badges)) {
						$badges = array_values($badges);
						$answers_badges = array_column($badges,'badge_answers');
					}
					$answers_badges = (isset($answers_badges) && is_array($answers_badges) && !empty($answers_badges)?$answers_badges:array());
					if (is_array($answers_badges) && !empty($answers_badges) && is_array($badges) && !empty($badges)) {
						array_multisort($answers_badges,SORT_ASC,$badges);
					}
					if (isset($badges) && is_array($badges)) {
						foreach ($badges as $badges_k => $badges_v) {
							$badges_answers[] = $badges_v["badge_answers"];
						}
						if (isset($badges_answers) && is_array($badges_answers)) {
							foreach ($badges_answers as $key => $badge_answer) {
								if ($answers >= $badge_answer) {
									$last_key = $key;
								}
							}
						}
						if (isset($last_key)) {
							$badge_key = $last_key;
							if ($return == "points") {
								return $badges[$badge_key]["badge_answers"];
							}else if ($return == "color") {
								return $badges[$badge_key]["badge_color"];
							}else if ($return == "name") {
								return strip_tags(stripslashes($badges[$badge_key]["badge_name"]),"<i>");
							}else if ($return == "key") {
								return $badge_key;
							}else if ($return == "first_key") {
								$first_badge = (isset($badges) && is_array($badges)?reset($badges):array());
								return (isset($first_badge['badge_answers']) && $first_badge['badge_answers'] == $badges[$badge_key]["badge_answers"]?$badge_key:"");
							}else {
								return apply_filters('wpqa_by_answers','<span class="badge-span" style="background-color: '.$badges[$badge_key]["badge_color"].'">'.strip_tags(stripslashes($badges[$badge_key]["badge_name"]),"<i>").'</span>',$author_id);
							}
						}
					}
				}
			}else {
				$active_points = wpqa_options("active_points");
				if ($author_id > 0 && $active_points == "on") {
					$points = (int)($points !== ""?$points:get_user_meta($author_id,"points",true));
					$badges = wpqa_options("badges");
					if (is_array($badges) && !empty($badges)) {
						$badges = array_values($badges);
						$points_badges = array_column($badges,'badge_points');
					}
					$points_badges = (isset($points_badges) && is_array($points_badges) && !empty($points_badges)?$points_badges:array());
					if (is_array($points_badges) && !empty($points_badges) && is_array($badges) && !empty($badges)) {
						array_multisort($points_badges,SORT_ASC,$badges);
					}
					if (isset($badges) && is_array($badges)) {
						foreach ($badges as $badges_k => $badges_v) {
							$badges_points[] = $badges_v["badge_points"];
						}
						if (isset($badges_points) && is_array($badges_points)) {
							foreach ($badges_points as $key => $badge_point) {
								if ($points >= $badge_point) {
									$last_key = $key;
								}
							}
						}
						if (isset($last_key)) {
							$badge_key = $last_key;
							if ($return == "points") {
								return $badges[$badge_key]["badge_points"];
							}else if ($return == "color") {
								return $badges[$badge_key]["badge_color"];
							}else if ($return == "name") {
								return strip_tags(stripslashes($badges[$badge_key]["badge_name"]),"<i>");
							}else if ($return == "key") {
								return $badge_key;
							}else if ($return == "first_key") {
								$first_badge = (isset($badges) && is_array($badges)?reset($badges):array());
								return (isset($first_badge['badge_points']) && $first_badge['badge_points'] == $badges[$badge_key]["badge_points"]?$badge_key:"");
							}else {
								return apply_filters('wpqa_by_points','<span class="badge-span" style="background-color: '.$badges[$badge_key]["badge_color"].'">'.strip_tags(stripslashes($badges[$badge_key]["badge_name"]),"<i>").'</span>',$author_id);
							}
						}
					}
				}
			}
		}
	}
endif;
/* Get the user stats */
if (!function_exists('wpqa_get_user_stats')) :
	function wpqa_get_user_stats($wpqa_user_id,$user_stats,$active_points,$show_point_favorite) {
		$out = '';
		$author_widget = wpqa_options("author_widget");
		$out .= apply_filters("wpqa_action_before_user_stats",false,$wpqa_user_id);
		if (isset($user_stats["questions"]) && $user_stats["questions"] == "questions") {
			$add_questions = wpqa_count_posts_meta(wpqa_questions_type,$wpqa_user_id);
		}

		if (isset($user_stats["answers"]) && $user_stats["answers"] == "answers") {
			$add_answer = wpqa_count_comments_meta(wpqa_questions_type,$wpqa_user_id);
		}

		if (isset($user_stats["best_answers"]) && $user_stats["best_answers"] == "best_answers") {
			$the_best_answer = wpqa_count_best_answers_meta($wpqa_user_id);
		}

		if ($active_points == "on" && isset($user_stats["points"]) && $user_stats["points"] == "points") {
			$points = (int)get_user_meta($wpqa_user_id,"points",true);
		}else {
			unset($user_stats["points"]);
		}

		$active_groups = wpqa_options("active_groups");
		if ($active_groups == "on" && isset($user_stats["groups"]) && $user_stats["groups"] == "groups") {
			$add_groups = wpqa_count_posts_meta("group",$wpqa_user_id);
		}

		if ($active_groups == "on" && isset($user_stats["group_posts"]) && $user_stats["group_posts"] == "group_posts") {
			$add_group_posts = wpqa_count_posts_meta("group_post",$wpqa_user_id);
		}

		if (isset($user_stats["posts"]) && $user_stats["posts"] == "posts") {
			$add_posts = wpqa_count_posts_meta("post",$wpqa_user_id);
		}

		if (isset($user_stats["comments"]) && $user_stats["comments"] == "comments") {
			$add_comment = wpqa_count_comments_meta("post",$wpqa_user_id);
		}

		$out .= apply_filters("wpqa_before_user_stats",false,$wpqa_user_id);

		if ((isset($user_stats["questions"]) && $user_stats["questions"] == "questions") || (isset($user_stats["answers"]) && $user_stats["answers"] == "answers") || (isset($user_stats["best_answers"]) && $user_stats["best_answers"] == "best_answers") || ($active_points == "on" && isset($user_stats["points"]) && $user_stats["points"] == "points") || ($active_groups == "on" && isset($user_stats["groups"]) && $user_stats["groups"] == "groups") || ($active_groups == "on" && isset($user_stats["group_posts"]) && $user_stats["group_posts"] == "group_posts") || (isset($user_stats["posts"]) && $user_stats["posts"] == "posts") || (isset($user_stats["comments"]) && $user_stats["comments"] == "comments") || ($author_widget == "on" && isset($user_stats["visits"]) && $user_stats["visits"] == "visits") || ($author_widget == "on" && isset($user_stats["i_follow"]) && $user_stats["i_follow"] == "i_follow") || ($author_widget == "on" && isset($user_stats["followers"]) && $user_stats["followers"] == "followers")) {
			if (count($user_stats) == 1) {
				$column_user = "col12";
			}else if (count($user_stats) == 2) {
				$column_user = "col6";
			}else if (count($user_stats) == 3) {
				$column_user = "col4";
			}else {
				$column_user = "col3";
			}
			$out .= '<div class="user-stats block-section-div stats-card">
				<ul class="row row-warp row-boot list-unstyled mb-0 d-flex justify-content-between">';
					if ($author_widget == "on" && isset($user_stats["visits"]) && $user_stats["visits"] == "visits") {
						$author_stats = wpqa_get_post_stats(0,$wpqa_user_id);
						$out .= '<li class="col col-boot-sm-6 stats-card__item '.esc_attr($column_user).' user-visits">
							<div class="d-flex justify-content-between stats-card__item_div">
								<i class="icon-eye"></i>
								<div class="stats-card__item__text w-100">
									<span>'.($author_stats == ""?0:wpqa_count_number($author_stats)).'</span>
									<h4>'._n("Visit","Visits",$author_stats,"wpqa").'</h4>
								</div>
							</div>
						</li>';
					}
					if (isset($user_stats["questions"]) && $user_stats["questions"] == "questions") {
						$out .= '<li class="col col-boot-sm-6 stats-card__item '.esc_attr($column_user).' user-questions">
							<div class="d-flex justify-content-between stats-card__item_div">
								<a class="stats-card__item_a" href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"questions")).'"></a>
								<i class="icon-book-open"></i>
								<div class="stats-card__item__text w-100">
									<span>'.($add_questions == ""?0:wpqa_count_number($add_questions)).'</span>
									<h4>'._n("Question","Questions",$add_questions,"wpqa").'</h4>
								</div>
							</div>
						</li>';
					}
					if (isset($user_stats["answers"]) && $user_stats["answers"] == "answers") {
						$out .= '<li class="col col-boot-sm-6 stats-card__item '.esc_attr($column_user).' user-answers">
							<div class="d-flex justify-content-between stats-card__item_div">
								<a class="stats-card__item_a" href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"answers")).'"></a>
								<i class="icon-comment"></i>
								<div class="stats-card__item__text w-100">
									<span>'.($add_answer == ""?0:wpqa_count_number($add_answer)).'</span>
									<h4>'._n("Answer","Answers",$add_answer,"wpqa").'</h4>
								</div>
							</div>
						</li>';
					}
					if (isset($user_stats["best_answers"]) && $user_stats["best_answers"] == "best_answers") {
						$out .= '<li class="col col-boot-sm-6 stats-card__item '.esc_attr($column_user).' user-best-answers">
							<div class="d-flex justify-content-between stats-card__item_div">
								<a class="stats-card__item_a" href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"best_answers")).'"></a>
								<i class="icon-graduation-cap"></i>
								<div class="stats-card__item__text w-100">
									<span>'.($the_best_answer == ""?0:wpqa_count_number($the_best_answer)).'</span>
									<h4>'._n("Best Answer","Best Answers",$the_best_answer,"wpqa").'</h4>
								</div>
							</div>
						</li>';
					}
					if ($active_points == "on" && isset($user_stats["points"]) && $user_stats["points"] == "points") {
						$out .= '<li class="col col-boot-sm-6 stats-card__item '.esc_attr($column_user).' user-points">
							<div class="d-flex justify-content-between stats-card__item_div">';
								if ($show_point_favorite == "on" || wpqa_is_user_owner()) {
									$out .= '<a class="stats-card__item_a" href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"points")).'"></a>';
								}
								$out .= '<i class="icon-bucket"></i>
								<div class="stats-card__item__text w-100">
									<span>'.($points == ""?0:wpqa_count_number($points)).'</span>
									<h4>'._n("Point","Points",$points,"wpqa").'</h4>
								</div>
							</div>
						</li>';
					}
					if ($active_groups == "on" && isset($user_stats["groups"]) && $user_stats["groups"] == "groups") {
						$out .= '<li class="col col-boot-sm-6 stats-card__item '.esc_attr($column_user).' user-groups">
							<div class="d-flex justify-content-between stats-card__item_div">
								<a class="stats-card__item_a" href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"groups")).'"></a>
								<i class="icon-network"></i>
								<div class="stats-card__item__text w-100">
									<span>'.($add_groups == ""?0:wpqa_count_number($add_groups)).'</span>
									<h4>'._n("Group","Groups",$add_groups,"wpqa").'</h4>
								</div>
							</div>
						</li>';
					}
					if ($active_groups == "on" && isset($user_stats["group_posts"]) && $user_stats["group_posts"] == "group_posts") {
						$out .= '<li class="col col-boot-sm-6 stats-card__item '.esc_attr($column_user).' user-group_posts">
							<div class="d-flex justify-content-between stats-card__item_div">
								<i class="icon-newspaper"></i>
								<div class="stats-card__item__text w-100">
									<span>'.($add_group_posts == ""?0:wpqa_count_number($add_group_posts)).'</span>
									<h4>'._n("Group Post","Group Posts",$add_group_posts,"wpqa").'</h4>
								</div>
							</div>
						</li>';
					}
					if (isset($user_stats["posts"]) && $user_stats["posts"] == "posts") {
						$out .= '<li class="col col-boot-sm-6 stats-card__item '.esc_attr($column_user).' user-posts">
							<div class="d-flex justify-content-between stats-card__item_div">
								<a class="stats-card__item_a" href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"posts")).'"></a>
								<i class="icon-doc-text"></i>
								<div class="stats-card__item__text w-100">
									<span>'.($add_posts == ""?0:wpqa_count_number($add_posts)).'</span>
									<h4>'._n("Post","Posts",$add_posts,"wpqa").'</h4>
								</div>
							</div>
						</li>';
					}
					if (isset($user_stats["comments"]) && $user_stats["comments"] == "comments") {
						$out .= '<li class="col col-boot-sm-6 stats-card__item '.esc_attr($column_user).' user-comments">
							<div class="d-flex justify-content-between stats-card__item_div">
								<a class="stats-card__item_a" href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"comments")).'"></a>
								<i class="icon-chat"></i>
								<div class="stats-card__item__text w-100">
									<span>'.($add_comment == ""?0:wpqa_count_number($add_comment)).'</span>
									<h4>'._n("Comment","Comments",$add_comment,"wpqa").'</h4>
								</div>
							</div>
						</li>';
					}
					if ($author_widget == "on" && (isset($user_stats["i_follow"]) && $user_stats["i_follow"] == "i_follow") || (isset($user_stats["followers"]) && $user_stats["followers"] == "followers")) {
						$author__not_in = array();
						$block_users = wpqa_options("block_users");
						if ($block_users == "on") {
							$user_id = $wpqa_user_id;
							if ($user_id > 0) {
								$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
								if (is_array($get_block_users) && !empty($get_block_users)) {
									$author__not_in = $get_block_users;
								}
							}
						}
						$following_me  = get_user_meta($wpqa_user_id,"following_me",true);
						if (is_array($following_me) && !empty($following_me)) {
							$following_me = array_diff($following_me,$author__not_in);
						}
						$following_me  = (is_array($following_me) && !empty($following_me)?get_users(array('fields' => 'ID','include' => $following_me,'orderby' => 'registered')):array());
						$following_you = get_user_meta($wpqa_user_id,"following_you",true);
						if (is_array($following_you) && !empty($following_you)) {
							$following_you = array_diff($following_you,$author__not_in);
						}
						$following_you = (is_array($following_you) && !empty($following_you)?get_users(array('fields' => 'ID','include' => $following_you,'orderby' => 'registered')):array());
					}
					if ($author_widget == "on" && isset($user_stats["followers"]) && $user_stats["followers"] == "followers") {
						$followers = (isset($following_you) && is_array($following_you)?count($following_you):0);
						$out .= '<li class="col col-boot-sm-6 stats-card__item '.esc_attr($column_user).' user-followers">
							<div class="d-flex justify-content-between stats-card__item_div">
								<a class="stats-card__item_a" href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"followers")).'"></a>
								<i class="icon-users"></i>
								<div class="stats-card__item__text w-100">
									<span>'.wpqa_count_number($followers).'</span>
									<h4>'._n("Follower","Followers",$followers,"wpqa").'</h4>
								</div>
							</div>
						</li>';
					}
					if ($author_widget == "on" && isset($user_stats["i_follow"]) && $user_stats["i_follow"] == "i_follow") {
						$following = (isset($following_me) && is_array($following_me)?count($following_me):0);
						$out .= '<li class="col col-boot-sm-6 stats-card__item '.esc_attr($column_user).' user-following">
							<div class="d-flex justify-content-between stats-card__item_div">
								<a class="stats-card__item_a" href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"following")).'"></a>
								<i class="icon-users"></i>
								<div class="stats-card__item__text w-100">
									<span>'.wpqa_count_number($following).'</span>
									<h4>'._n("Member","Members",$following,"wpqa").'</h4>
								</div>
							</div>
						</li>';
					}
				$out .= '</ul>';
				$out .= apply_filters("wpqa_after_user_stats",false,$wpqa_user_id);
				$active_points_category = wpqa_options("active_points_category");
				if ($active_points_category == "on") {
					$categories_user_points = get_user_meta($wpqa_user_id,"categories_user_points",true);
					if (is_array($categories_user_points) && !empty($categories_user_points)) {
						$display_name = get_the_author_meta('display_name',$wpqa_user_id);
						$out .= "<ul class='row row-warp row-boot user-points-categories'>
							<li class='col col-boot'>
								<div>
									<h5><i class='icon-graduation-cap'></i>".$display_name." ".(count($categories_user_points) > 1?esc_html__("has been qualified at the following categories","wpqa"):esc_html__("has been qualified at the following category","wpqa"))."</h5>
									<ul>";
										$category_with_points = array();
										foreach ($categories_user_points as $category) {
											$category_with_points[$category] = (int)get_user_meta($wpqa_user_id,"points_category".$category,true);
										}
										arsort($category_with_points);
										foreach ($category_with_points as $category => $points) {
											$get_term = get_term($category,wpqa_question_categories);
											$term_filter = apply_filters("wpqa_user_points_categories",true,$get_term);
											if ($term_filter == true && isset($get_term->slug)) {
												$out .= "<li>
													<i class='icon-bucket'></i>
													".apply_filters("wpqa_filter_categories_points","<a href='".get_term_link($get_term)."'>".$get_term->name."</a> (".$points." "._n("point","points",$points,"wpqa").") ".wpqa_get_badge($wpqa_user_id,"",$points),$get_term,$points,$wpqa_user_id)."
												</li>";
											}
										}
									$out .= "</ul>
								</div>
							</li>
						</ul>";
					}
				}
			$out .= '</div><!-- End user-stats -->';
		}
		return $out;
	}
endif;
/* Check user privacy */
if (!function_exists('wpqa_check_user_privacy')) :
	function wpqa_check_user_privacy($user_id,$privacy_key) {
		$get_current_user_id = get_current_user_id();
		$is_super_admin = is_super_admin($get_current_user_id);
		$privacy_value = get_user_meta($user_id,"privacy_".$privacy_key,true);
		$privacy_value = ($privacy_value != ""?$privacy_value:wpqa_options("privacy_".$privacy_key));
		$return = false;
		if ($privacy_value == "" || $privacy_value == "public" || $is_super_admin || ($privacy_value == "members" && is_user_logged_in()) || ($privacy_value == "me" && $get_current_user_id > 0 && $get_current_user_id == $user_id)) {
			$return = true;
		}
		return $return;
	}
endif;
/* Author widget */
if (!function_exists('wpqa_author_widget')) :
	function wpqa_author_widget() {
		$out = '';
		$author_widgets = wpqa_options("author_widgets");
		$author_role = wpqa_options("author_role");
		$user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
		$gender = get_the_author_meta('gender',$user_id);
		$privacy_gender = wpqa_check_user_privacy($user_id,"gender");
		$gender_class = ($gender !== ''?($gender == "male" || $gender == 1?"him":"").($gender == "female" || $gender == 2?"her":"").($gender == "other" || $gender == 3?"other":""):'');
		$age = get_the_author_meta('age',$user_id);
		$privacy_age = wpqa_check_user_privacy($user_id,"age");
		$country = get_the_author_meta('country',$user_id);
		$city = get_the_author_meta('city',$user_id);
		$credential = get_the_author_meta('profile_credential',$user_id);
		$get_countries = apply_filters('wpqa_get_countries',false);
		$privacy_country = wpqa_check_user_privacy($user_id,"country");
		$privacy_city = wpqa_check_user_privacy($user_id,"city");
		$privacy_credential = wpqa_check_user_privacy($user_id,"credential");
		
		if (isset($author_widgets) && is_array($author_widgets) && !empty($author_widgets)) {
			foreach ($author_widgets as $key => $value) {
				if ($key == "about" && isset($value["value"]) && $value["value"] == "about") {
					$author_display_name = get_the_author_meta("display_name",$user_id);
					$user = get_userdata($user_id);
					$roles_obj = new WP_Roles();
					$roles_names_array = $roles_obj->get_names();
					$user_group = wpqa_get_user_group($user);
					$role_name = (isset($roles_names_array[$user_group]) && $roles_names_array[$user_group] != ""?$roles_names_array[$user_group]:"");
					$if_user_subscribe = wpqa_check_if_user_subscribe($user_id);
					if (($author_role == "on" && $role_name != "") || $if_user_subscribe) {
						$out .= '<div class="card member-card'.($gender_class != ""?" ".$gender_class."-user":"").' text-center mb-0"><div class="member__tag">'.($if_user_subscribe?esc_html__("Pro member","wpqa"):translate_user_role($role_name)).'</div></div>';
					}
					$active_points_category = wpqa_options("active_points_category");
					if ($active_points_category == "on") {
						$categories_user_points = get_user_meta($user_id,"categories_user_points",true);
						if (is_array($categories_user_points) && !empty($categories_user_points)) {
							foreach ($categories_user_points as $category) {
								$points_category_user[$category] = (int)get_user_meta($user_id,"points_category".$category,true);
							}
							arsort($points_category_user);
							$first_category = (is_array($points_category_user)?key($points_category_user):"");
							$first_points = reset($points_category_user);
						}
					}
					$out .= '<section class="widget card member-card him-user text-center">
						<div class="member__info">
							<div class="d-flex justify-content-between align-items-center mb-1">
								'.wpqa_get_badge($user_id,"",(isset($first_points) && $first_points != ""?$first_points:""));
								$get_current_user_id = get_current_user_id();
								$is_super_admin      = is_super_admin($get_current_user_id);
								$active_moderators   = wpqa_options("active_moderators");
								if ($active_moderators == "on") {
									$moderator_categories = get_user_meta($get_current_user_id,prefix_author."moderator_categories",true);
									$moderator_categories = (is_array($moderator_categories) && !empty($moderator_categories)?$moderator_categories:array());
									$pending_posts = ($is_super_admin || (isset($moderator_categories) && is_array($moderator_categories) && !empty($moderator_categories))?true:false);
									$moderators_permissions = wpqa_user_moderator($get_current_user_id);
									$if_user_id = get_user_by("id",$user_id);
									$user_group = wpqa_get_user_group($if_user_id);
									$moderators_available = ($is_super_admin || ($user_group != "administrator" && $pending_posts == true && isset($moderators_permissions['ban']) && $moderators_permissions['ban'] == "ban")?true:false);
								}
								$block_users = wpqa_options("block_users");
								$report_users = wpqa_options("report_users");
								$owner = wpqa_is_user_owner();
								$ask_question_to_users = wpqa_options("ask_question_to_users");
								$ask_user = ($ask_question_to_users == "on" && !$owner?"<div class='ask-question'><a href='".esc_url(wpqa_add_question_permalink("user"))."' class='button-default ask-question-user btn btn__semi__height btn__secondary'>".esc_html__("Ask","wpqa")." ".$author_display_name."</a></div>":"");
								$follow_user_1 = wpqa_following($user_id,"style_3",$owner,"","","follow_link_menu","unfollow_link_menu");
								$follow_user_2 = wpqa_following($user_id,"style_3",$owner,"","","follow_link_menu btn btn__semi__height btn__success","unfollow_link_menu btn btn__semi__height btn__danger");
								$send_message = wpqa_message_button($user_id,"text",$owner);
								if (($ask_user != "" && $follow_user_1 != "") || $owner || ($report_users == "on" && !$owner && $get_current_user_id > 0) || (!$owner && (isset($moderators_available) && $moderators_available == true)) || ($block_users == "on" && !$owner && $get_current_user_id > 0)) {
									$out .= '<div class="dropdown custom-dropdown">
										<button class="member__setting-btn" type="button" data-toggle="dropdown" aria-haspopup="true"
											aria-expanded="false">
											<i class="icon-android-settings"></i>
										</button>
										<div class="dropdown-menu">
											<ul>';
												if ($owner) {
													$out .= "<li class='edit-profile-cover dropdown-item'><a href='".wpqa_edit_profile_permalink()."'><i class='icon-cog'></i>".esc_html__("Edit profile","wpqa")."</a></li>";
												}
												if ($block_users == "on" && !$owner && $get_current_user_id > 0 && !is_super_admin($user_id)) {
													$get_block_users = get_user_meta($get_current_user_id,"wpqa_block_users",true);
													$if_block = (is_array($get_block_users) && !empty($get_block_users) && in_array($user_id,$get_block_users)?true:false);
													$out .= "<li class='block-unblock-user dropdown-item'><span class='small_loader loader_2'></span><a class='".($if_block?"unblock-user":"block-user")."' data-nonce='".wp_create_nonce("block_nonce")."' href='#' data-id='".$user_id."'><i class='".($if_block?"icon-back":"icon-block")."'></i><span>".($if_block?esc_html__("Unblock user","wpqa"):esc_html__("Block user","wpqa"))."</span></a></li>";
												}
												if (!$owner && (isset($moderators_available) && $moderators_available == true) && !is_super_admin($user_id)) {
													$if_ban = (isset($if_user_id->caps["ban_group"]) && $if_user_id->caps["ban_group"] == 1?true:false);
													$out .= "<li class='ban-unban-user dropdown-item'><span class='small_loader loader_2'></span><a class='".($if_ban?"unban-user":"ban-user")."' data-nonce='".wp_create_nonce("ban_nonce")."' href='#' data-id='".$user_id."'><i class='".($if_ban?"icon-back":"icon-cancel-circled")."'></i><span>".($if_ban?esc_html__("Unban user","wpqa"):esc_html__("Ban user","wpqa"))."</span></a></li>";
												}
												if ($report_users == "on" && !$owner && $get_current_user_id > 0) {
													$out .= "<li class='report_activated report-user-li dropdown-item'><span class='small_loader loader_2'></span><a class='report_user' href='".$user_id."'><i class='icon-flag'></i><span>".esc_html__("Report user","wpqa")."</span></a></li>";
												}
												if ($ask_user != "" && $follow_user_1 != "") {
													$out .= "<li class='follow-user-li dropdown-item'><span class='small_loader loader_2'></span>".$follow_user_1."</li>";
												}
											$out .= '</ul>
										</div>
									</div>';
								}
							$out .= '</div>
							'.wpqa_get_avatar_link(array("user_id" => $user_id,"user_name" => $author_display_name,"size" => 90,"span" => "span","class" => "rounded-circle")).'
							<h3 class="member__name mb-0">'.$author_display_name.'</h3>';
							if (($privacy_credential == true && isset($credential) && $credential != "") || ($privacy_city == true && isset($city) && $city != "") || ($privacy_country == true && isset($country) && $country != "" && isset($get_countries[$country]))) {
								if ($privacy_credential == true && isset($credential) && $credential != "") {
									$out .= '<p class="member__job">'.esc_html($credential).'</p>';
								}else if (($privacy_city == true && isset($city) && $city != "") || ($privacy_country == true && isset($country) && $country != "" && isset($get_countries[$country]))) {
									$out .= '<p class="member__job">'.(isset($city) && $city != ""?esc_html($city).", ":"").(isset($country) && $country != "" && isset($get_countries[$country])?$get_countries[$country]:"").'</p>';
								}
							}
							$meta_description = get_user_meta($user_id,"description",true);
							if ($meta_description != "") {
								$privacy_bio = wpqa_check_user_privacy($user_id,"bio");
								if ($privacy_bio != "") {
									$bio_editor = wpqa_options("bio_editor");
									if ($bio_editor == "on") {
										$out .= '<div class="bio_editor member__bio mb-0">'.$meta_description.'</div>';
									}else {
										$out .= '<p class="member__bio mb-0">'.nl2br($meta_description).'</p>';
									}
								}
							}
						$out .= '</div><!-- /.member__info -->
						<div class="member__actions d-flex '.(($ask_user != "" || $send_message != "") && $follow_user_2 != ""?"justify-content-between":"justify-content-center").'">
							'.($ask_user != ""?$ask_user:$follow_user_2).$send_message.'
						</div><!-- /.member__actions -->
					</section><!-- /.member-card -->';
				}else if ($key == "statistics" && isset($value["value"]) && $value["value"] == "statistics") {
					$user_stats = wpqa_options("user_stats");
					$author_visits = wpqa_options("author_visits");
					if ($author_visits == "on") {
						$user_stats["visits"] = "visits";
					}
					$show_point_favorite = get_user_meta($user_id,"show_point_favorite",true);
					$active_points = wpqa_options("active_points");
					$out .= '<section class="widget card stats-widget stats-card">
						<div class="card-header d-flex align-items-center"><h2 class="card-title mb-0 d-flex align-items-center"><i class="icon-folder font-xxl card-title__icon"></i><span>'.esc_html__("User Statistics","wpqa").'</span></h2></div>
						'.wpqa_get_user_stats($user_id,$user_stats,$active_points,$show_point_favorite).'
					</section><!-- /.stats-card -->';
				}else if ($key == "information" && isset($value["value"]) && $value["value"] == "information") {
					$phone = get_the_author_meta('phone',$user_id);
					$privacy_phone = wpqa_check_user_privacy($user_id,"phone");
					if (($privacy_city == true && isset($city) && $city != "") || ($privacy_country == true && isset($country) && $country != "" && isset($get_countries[$country])) || ($privacy_credential == true && isset($credential) && $credential != "") || ($privacy_phone == true && isset($phone) && $phone != "") || ($privacy_gender == true && isset($gender) && $gender != "")) {
						$out .= '<section class="widget card info-card info-card-widget">
							<div class="card-header d-flex align-items-center"><h2 class="card-title mb-0 d-flex align-items-center"><i class="icon-folder font-xxl card-title__icon"></i><span>'.esc_html__("User Information","wpqa").'</span></h2></div>
							<div class="widget-wrap">
								<ul class="info__list list-unstyled mb-0">';
									if (($privacy_city == true && isset($city) && $city != "") || ($privacy_country == true && isset($country) && $country != "" && isset($get_countries[$country]))) {
										$out .= '<li class="info__item info_country_city d-flex align-items-center">
											<i class="info__icon icon-location"></i><span class="info__text">'.(isset($city) && $city != ""?esc_html($city).", ":"").(isset($country) && $country != "" && isset($get_countries[$country])?$get_countries[$country]:"").'</span>
										</li>';
									}
									if ($privacy_credential == true && isset($credential) && $credential != "") {
										$out .= '<li class="info__item info_credential d-flex align-items-center">
											<i class="info__icon icon-info"></i><span class="info__text">'.esc_html($credential).'</span>
										</li>';
									}
									if ($privacy_phone == true && isset($phone) && $phone != "") {
										$out .= '<li class="info__item info_phone d-flex align-items-center">
											<i class="info__icon icon-phone"></i><span class="info__text">'.esc_html($phone).'</span>
										</li>';
									}
									if ($privacy_gender == true && isset($gender) && $gender != "") {
										$out .= '<li class="info__item info_gender d-flex align-items-center">
											<i class="info__icon icon-heart"></i><span class="info__text">'.($gender == "male" || $gender == 1?esc_html__("Male","wpqa"):"").($gender == "female" || $gender == 2?esc_html__("Female","wpqa"):"").($gender == "other" || $gender == 3?esc_html__("Other","wpqa"):"").'</span>
										</li>';
									}
									if ($privacy_age == true && isset($age) && $age != "") {
										$age = (date_create($age)?date_diff(date_create($age),date_create('today'))->y:"");
										$out .= '<li class="info__item info_age d-flex align-items-center">
											<i class="info__icon icon-globe"></i><span class="info__text">'.esc_html($age)." ".esc_html__("years old","wpqa").'</span>
										</li>';
									}
								$out .= '</ul>
							</div><!-- /.widget-wrap -->
						</section><!-- /.info-card -->';
					}
				}else if ($key == "social" && isset($value["value"]) && $value["value"] == "social") {
					$author_social_widget = wpqa_options("author_social_widget");
					$twitter    = get_the_author_meta('twitter',$user_id);
					$facebook   = get_the_author_meta('facebook',$user_id);
					$tiktok     = get_the_author_meta('tiktok',$user_id);
					$linkedin   = get_the_author_meta('linkedin',$user_id);
					$youtube    = get_the_author_meta('youtube',$user_id);
					$vimeo      = get_the_author_meta('vimeo',$user_id);
					$pinterest  = get_the_author_meta('pinterest',$user_id);
					$instagram  = get_the_author_meta('instagram',$user_id);
					$user_email = get_the_author_meta('email',$user_id);
					$privacy_email = wpqa_check_user_privacy($user_id,"email");
					$privacy_social = wpqa_check_user_privacy($user_id,"social");
					if (($privacy_email == true && $user_email != "") || ($privacy_social == true && ($facebook || $tiktok || $twitter || $linkedin || $youtube || $vimeo || $pinterest || $instagram))) {
						$out .= '<section class="widget card social-card-widget '.($author_social_widget == "style_2"?"social-card":"social-card-layout2").'">
							<div class="card-header d-flex align-items-center"><h2 class="card-title mb-0 d-flex align-items-center"><i class="icon-folder font-xxl card-title__icon"></i><span>'.esc_html__("Social Profiles","wpqa").'</span></h2></div>
							<div class="widget-wrap">
								<ul class="social__list list-unstyled mb-0'.($author_social_widget == "style_2"?"":" d-flex flex-wrap").'">';
									if ($privacy_social == true) {
										if ($facebook) {
											$out .= '<li class="social-facebook">
												<a class="social__link'.($author_social_widget == "style_2"?"":" facebook").' d-flex align-items-center" href="'.esc_url($facebook).'" target="_blank" rel="nofollow">
													<i class="social__icon icon-facebook"></i><span class="social__text">Facebook</span>
												</a>
											</li>';
										}
										if ($twitter) {
											$out .= '<li class="social-twitter">
												<a class="social__link'.($author_social_widget == "style_2"?"":" twitter").' d-flex align-items-center" href="'.esc_url($twitter).'" target="_blank" rel="nofollow">
													<i class="social__icon icon-twitter"></i><span class="social__text">Twitter</span>
												</a>
											</li>';
										}
										if ($tiktok) {
											$out .= '<li class="social-tiktok">
												<a class="social__link'.($author_social_widget == "style_2"?"":" tiktok").' d-flex align-items-center" href="'.esc_url($tiktok).'" target="_blank" rel="nofollow">
													<i class="social__icon fab fa-tiktok"></i><span class="social__text">Tiktok</span>
												</a>
											</li>';
										}
										if ($linkedin) {
											$out .= '<li class="social-linkedin">
												<a class="social__link'.($author_social_widget == "style_2"?"":" linkedin").' d-flex align-items-center" href="'.esc_url($linkedin).'" target="_blank" rel="nofollow">
													<i class="social__icon icon-linkedin"></i><span class="social__text">Linkedin</span>
												</a>
											</li>';
										}
										if ($pinterest) {
											$out .= '<li class="social-pinterest">
												<a class="social__link'.($author_social_widget == "style_2"?"":" pinterest").' d-flex align-items-center" href="'.esc_url($pinterest).'" target="_blank" rel="nofollow">
													<i class="social__icon icon-pinterest"></i><span class="social__text">Pinterest</span>
												</a>
											</li>';
										}
										if ($instagram) {
											$out .= '<li class="social-instagram">
												<a class="social__link'.($author_social_widget == "style_2"?"":" instagram").' d-flex align-items-center" href="'.esc_url($instagram).'" target="_blank" rel="nofollow">
													<i class="social__icon icon-instagram"></i><span class="social__text">Instagram</span>
												</a>
											</li>';
										}
										if ($youtube) {
											$out .= '<li class="social-youtube">
												<a class="social__link'.($author_social_widget == "style_2"?"":" youtube").' d-flex align-items-center" href="'.esc_url($youtube).'" target="_blank" rel="nofollow">
													<i class="social__icon icon-play"></i><span class="social__text">Youtube</span>
												</a>
											</li>';
										}
										if ($vimeo) {
											$out .= '<li class="social-vimeo">
												<a class="social__link'.($author_social_widget == "style_2"?"":" vimeo").' d-flex align-items-center" href="'.esc_url($vimeo).'" target="_blank" rel="nofollow">
													<i class="social__icon icon-vimeo"></i><span class="social__text">Vimeo</span>
												</a>
											</li>';
										}
									}
									if ($privacy_email == true && $user_email != "") {
										$out .= '<li>
											<a class="social__link social-mail d-flex align-items-center" href="mailto:'.esc_attr($user_email).'" target="_blank" rel="nofollow">
												<i class="social__icon icon-mail"></i><span class="social__text">'.esc_html__("Email","wpqa").'</span>
											</a>
										</li>';
									}
								$out .= '</ul>
							</div><!-- /.widget-wrap -->
						</section><!-- /.social-card -->';
					}
				}else if ($key == "awards" && isset($value["value"]) && $value["value"] == "awards") {
					$out .= '<section class="widget card awards-card-widget awards-card">
						<div class="card-header d-flex align-items-center"><h2 class="card-title mb-0 d-flex align-items-center"><i class="icon-folder font-xxl card-title__icon"></i><span>'.esc_html__("User Awards","wpqa").'</span></h2></div>
						<div class="widget-wrap">
							<ul class="awards__list d-flex flex-wrap list-unstyled mb-0">
								<li><img src="assets/images/awards/1.png" class="rounded-circle" alt="award" title="award"></li>
								<li><img src="assets/images/awards/2.png" class="rounded-circle" alt="award" title="award"></li>
								<li><img src="assets/images/awards/3.png" class="rounded-circle" alt="award" title="award"></li>
								<li><img src="assets/images/awards/4.png" class="rounded-circle" alt="award" title="award"></li>
							</ul>
						</div><!-- /.widget-wrap -->
					</section><!-- /.social-card -->';
				}
			}
		}
		return $out;
	}
endif;
/* Pay to user */
if (!function_exists('wpqa_pay_to_user')) :
	function wpqa_pay_to_user($user_id = 0,$user = object) {
		$return = false;
		$activate_pay_to_users = wpqa_options("activate_pay_to_users");
		if ($activate_pay_to_users == "on") {
			if (!isset($user->ID) && $user_id > 0) {
				$user = get_userdata($user_id);
			}
			if (isset($user->allcaps)) {
				$roles = (isset($user->allcaps)?$user->allcaps:array());
			}
			$is_super_admin = is_super_admin($user_id);
			$custom_permission = wpqa_options("custom_permission");
			if ($is_super_admin || $custom_permission != "on" || ($custom_permission == "on" && $user_id > 0 && isset($roles["pay_to_user"]) && $roles["pay_to_user"] == 1)) {
				$return = true;
			}
		}
		return $return;
	}
endif;
/* Index author pages */
add_filter('wp_robots','wpqa_profile_wp_robots');
if (!function_exists('wpqa_profile_wp_robots')) :
	function wpqa_profile_wp_robots($robots) {
		if (is_author() || wpqa_is_user_profile()) {
			$index_author = wpqa_options("index_author");
			$index_author_pages = wpqa_options("index_author_pages");
			if ($index_author == "on" && ($index_author_pages == "on" || ($index_author_pages != "on" && wpqa_is_home_profile()))) {
				$robots['noindex']  = false;
				$robots['index']  = true;
				$robots['nofollow'] = false;
			}else {
				$robots['noindex']  = true;
				$robots['nofollow'] = true;
			}
		}
		return $robots;
	}
endif;
add_filter('rank_math/frontend/robots','wpqa_profile_rank_math_index',99);
if (!function_exists('wpqa_profile_rank_math_index')) :
	function wpqa_profile_rank_math_index($robots) {
		if (is_author() || wpqa_is_user_profile()) {
			$index_author = wpqa_options("index_author");
			$index_author_pages = wpqa_options("index_author_pages");
			if ($index_author == "on" && ($index_author_pages == "on" || ($index_author_pages != "on" && wpqa_is_home_profile()))) {
				$robots['index'] = 'index';
			}else {
				$robots['index'] = 'noindex';
			}
		}
		return $robots;
	}
endif;
/* Get author group */
if (!function_exists('wpqa_get_user_group')) :
	function wpqa_get_user_group($user) {
		$user = (isset($user->ID)?$user:get_userdata($user));
		$user_group = (isset($user->roles) && is_array($user->roles)?reset($user->roles):(isset($user->caps) && is_array($user->caps)?key($user->caps):""));
		return $user_group;
	}
endif;?>