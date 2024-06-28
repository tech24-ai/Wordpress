<?php

/* @author    2codeThemes
*  @package   WPQA/CPT
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Points post type */
function wpqa_point_post_types_init() {
	$active_points = wpqa_options("active_points");
	if ($active_points == "on") {
		register_post_type( 'point',
			array(
				'label' => esc_html__('Points','wpqa'),
				'labels' => array(
					'name'               => esc_html__('Points','wpqa'),
					'singular_name'      => esc_html__('Points','wpqa'),
					'menu_name'          => esc_html__('Points','wpqa'),
					'name_admin_bar'     => esc_html__('Points','wpqa'),
					'edit_item'          => esc_html__('Edit Point','wpqa'),
					'all_items'          => esc_html__('All Points','wpqa'),
					'search_items'       => esc_html__('Search Points','wpqa'),
					'parent_item_colon'  => esc_html__('Parent Point:','wpqa'),
					'not_found'          => esc_html__('No Points Found.','wpqa'),
					'not_found_in_trash' => esc_html__('No Points Found in Trash.','wpqa'),
				),
				'description'         => '',
				'public'              => false,
				'show_ui'             => true,
				'capability_type'     => 'post',
				'capabilities'        => array('create_posts' => 'do_not_allow'),
				'map_meta_cap'        => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => false,
				'hierarchical'        => false,
				'query_var'           => false,
				'show_in_rest'        => false,
				'has_archive'         => false,
				'menu_position'       => 5,
				'menu_icon'           => "dashicons-star-filled",
				'supports'            => array('title','editor'),
			)
		);
	}
}
add_action( 'wpqa_init', 'wpqa_point_post_types_init', 2 );
/* Admin columns for post types */
function wpqa_point_columns($old_columns) {
	$columns = array();
	$columns["cb"]        = "<input type=\"checkbox\">";
	$columns["content_p"] = esc_html_x("Point","Admin point column","wpqa");
	$columns["author_p"]  = esc_html__("Author","wpqa");
	$columns["date_p"]    = esc_html__("Date","wpqa");
	return $columns;
}
add_filter('manage_edit-point_columns','wpqa_point_columns');
function wpqa_point_custom_columns($column) {
	global $post;
	switch ( $column ) {
		case 'content_p' :
			$point_result = wpqa_point_result($post,"admin");
			echo wpqa_show_points($point_result);
		break;
		case 'author_p' :
			$user_name = get_the_author_meta('display_name',$post->post_author);
			if ($user_name != "") {
				echo '<a target="_blank" href="'.wpqa_profile_url((int)$post->post_author).'"><strong>'.$user_name.'</strong></a><a class="tooltip_s" data-title="'.esc_html__("View points","wpqa").'" href="'.admin_url('edit.php?post_type=point&author='.$post->post_author).'"><i class="dashicons dashicons-star-filled"></i></a>';
			}else {
				esc_html_e("Deleted user","wpqa");
			}
		break;
		case 'date_p' :
			$date_format = wpqa_options("date_format");
			$date_format = ($date_format?$date_format:get_option("date_format"));
			$human_time_diff = human_time_diff(get_the_time('U'), current_time('timestamp'));
			echo ($human_time_diff." ".esc_html__("ago","wpqa")." - ".esc_html(get_the_time($date_format)));
		break;
	}
}
add_action('manage_point_posts_custom_column','wpqa_point_custom_columns',2);
function wpqa_point_primary_column($default,$screen) {
	if ('edit-point' === $screen) {
		$default = 'content_p';
	}
	return $default;
}
add_filter('list_table_primary_column','wpqa_point_primary_column',10,2);
add_filter('manage_edit-point_sortable_columns','wpqa_point_sortable_columns');
function wpqa_point_sortable_columns($defaults) {
	$defaults['date_p'] = 'date';
	return $defaults;
}
/* Point details */
add_filter('bulk_actions-edit-point','wpqa_bulk_actions_point');
function wpqa_bulk_actions_point($actions) {
	unset($actions['edit']);
	return $actions;
}
add_filter('bulk_post_updated_messages','wpqa_bulk_updated_messages_point',1,2);
function wpqa_bulk_updated_messages_point($bulk_messages,$bulk_counts) {
	if (get_current_screen()->post_type == "point") {
		$bulk_messages['post'] = array(
			'deleted' => _n('%s point permanently deleted.','%s points permanently deleted.',$bulk_counts['deleted'],'wpqa'),
			'trashed' => _n('%s point trashed.','%s points trashed.',$bulk_counts['trashed'],'wpqa'),
		);
	}
	return $bulk_messages;
}
add_filter('post_row_actions','wpqa_row_actions_point',1,2);
function wpqa_row_actions_point($actions,$post) {
	if ($post->post_type == "point") {
		unset($actions['trash']);
		unset($actions['view']);
		unset($actions['edit']);
		$actions['inline hide-if-no-js'] = "";
	}
	return $actions;
}
function wpqa_point_filter() {
	global $post_type;
	if ($post_type == 'point') {
		$from = (isset($_GET['date-from']) && $_GET['date-from'])?$_GET['date-from'] :'';
		$to = (isset($_GET['date-to']) && $_GET['date-to'])?$_GET['date-to']:'';
		$data_js = " data-js='".json_encode(array("changeMonth" => true,"changeYear" => true,"yearRange" => "2018:+00","dateFormat" => "yy-mm-dd"))."'";

		echo '<span class="site-form-date"><input class="site-date" type="text" name="date-from" placeholder="'.esc_html__("Date From","wpqa").'" value="'.esc_attr($from).'" '.$data_js.'></span>
		<span class="site-form-date"><input class="site-date" type="text" name="date-to" placeholder="'.esc_html__("Date To","wpqa").'" value="'.esc_attr($to).'" '.$data_js.'></span>';
	}
}
add_action('restrict_manage_posts','wpqa_point_filter');
function wpqa_point_posts_query($query) {
	global $post_type,$pagenow;
	if ($pagenow == 'edit.php' && $post_type == 'point') {
		if (!empty($_GET['date-from']) && !empty($_GET['date-to'])) {
			$query->query_vars['date_query'][] = array(
				'after'     => sanitize_text_field($_GET['date-from']),
				'before'    => sanitize_text_field($_GET['date-to']),
				'inclusive' => true,
				'column'    => 'post_date'
			);
		}
		if (!empty($_GET['date-from']) && empty($_GET['date-to'])) {
			$today = sanitize_text_field($_GET['date-from']);
			$today = explode("-",$today);
			$query->query_vars['date_query'] = array(
				'year'  => $today[0],
				'month' => $today[1],
				'day'   => $today[2],
			);
		}
		if (empty($_GET['date-from']) && !empty($_GET['date-to'])) {
			$today = sanitize_text_field($_GET['date-to']);
			$today = explode("-",$today);
			$query->query_vars['date_query'] = array(
				'year'  => $today[0],
				'month' => $today[1],
				'day'   => $today[2],
			);
		}
		$orderby = $query->get('orderby');
		if ($orderby == 'date_p') {
			$query->query_vars('orderby','date');
		}
	}
}
add_action('pre_get_posts','wpqa_point_posts_query');
function wpqa_months_dropdown_point($return,$post_type) {
	if ($post_type == "point") {
		$return = true;
	}
	return $return;
}
add_filter("disable_months_dropdown","wpqa_months_dropdown_point",1,2);
/* Remove filter */
function wpqa_manage_point_tablenav($which) {
	if ($which == "top") {
		global $post_type,$pagenow;
		if ($pagenow == 'edit.php' && $post_type == 'point') {
			$date_from = (isset($_GET['date-from'])?esc_html($_GET['date-from']):'');
			$date_to = (isset($_GET['date-to'])?esc_html($_GET['date-to']):'');
			if ($date_from != "" || $date_to != "") {
				echo '<a class="button" href="'.admin_url('edit.php?post_type=point').'">'.esc_html__("Remove filters","wpqa").'</a>';
			}
		}
	}
}
add_filter("manage_posts_extra_tablenav","wpqa_manage_point_tablenav");
/* Post publish */
if (!function_exists('wpqa_post_publish')) :
	function wpqa_post_publish($post,$post_author,$area = '') {
		$post_id = (int)$post->ID;
		$post_type = $post->post_type;
		$point_add_post = (int)wpqa_options("point_add_".$post_type);
		$active_points = wpqa_options("active_points");
		if ($post_author > 0 && $point_add_post > 0 && $active_points == "on") {
			update_post_meta($post_id,"get_points_before","yes");
			wpqa_add_points($post_author,$point_add_post,"+","add_".$post_type,$post_id);
		}
		do_action("wpqa_after_post_publish",$post,$post_author,$area);
	}
endif;
/* Add points for the user */
if (!function_exists('wpqa_add_points')) :
	function wpqa_add_points($user_id,$points,$relation,$message,$post_id = 0,$comment_id = 0,$another_user_id = 0,$points_type = "points",$items = true) {
		$points = apply_filters("wpqa_add_points_filter",$points,$user_id,$relation,$message,$post_id,$comment_id,$another_user_id,$points_type,$items);
		if ($points > 0) {
			$active_points_specific = wpqa_options("active_points_specific");
			if ($active_points_specific == "on" && $points_type == "points") {
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
				$points_type_specific = "points_date_".date("j-n-Y");
				$points_user = (int)get_user_meta($user_id,$points_type_specific,true);
				$points_user = (int)($relation == "+"?$points_user+$points:$points_user-$points);
				update_user_meta($user_id,$points_type_specific,($points_user > 0?$points_user:0));

				$points_type_specific = "points_date_".date("Y-m-d H:i:s",strtotime($start_of_week.' this week'));
				$points_user = (int)get_user_meta($user_id,$points_type_specific,true);
				$points_user = (int)($relation == "+"?$points_user+$points:$points_user-$points);
				update_user_meta($user_id,$points_type_specific,($points_user > 0?$points_user:0));

				$points_type_specific = "points_date_".date("n-Y");
				$points_user = (int)get_user_meta($user_id,$points_type_specific,true);
				$points_user = (int)($relation == "+"?$points_user+$points:$points_user-$points);
				update_user_meta($user_id,$points_type_specific,($points_user > 0?$points_user:0));

				$points_type_specific = "points_date_".date("Y");
				$points_user = (int)get_user_meta($user_id,$points_type_specific,true);
				$points_user = (int)($relation == "+"?$points_user+$points:$points_user-$points);
				update_user_meta($user_id,$points_type_specific,($points_user > 0?$points_user:0));
			}
			if ($items == true) {
				if ($points_type == "points") {
					wpqa_insert_points($user_id,($another_user_id > 0?$another_user_id:""),$points,$relation,($post_id > 0?$post_id:""),($comment_id > 0?$comment_id:""),$message);
				}else {
					$_points = (int)get_user_meta($user_id,$user_id."_".$points_type,true);
					$_points++;
					update_user_meta($user_id,$user_id."_".$points_type,$_points);
					add_user_meta($user_id,$user_id."_".$points_type."_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$points,$relation,$message,($post_id > 0?$post_id:""),($comment_id > 0?$comment_id:""),"time" => current_time('timestamp'),"user_id" => ($another_user_id > 0?$another_user_id:"")));
				}
			}
			$points_user = (int)get_user_meta($user_id,$points_type,true);
			$points_user = (int)($relation == "+"?$points_user+$points:$points_user-$points);
			update_user_meta($user_id,$points_type,($points_user > 0?$points_user:0));

			$active_points_category = wpqa_options("active_points_category");
			if ($active_points_category == "on" && $points_type == "points") {
				$categories = wp_get_post_terms($post_id,wpqa_question_categories,array('fields' => 'ids'));
				$categories = (is_array($categories) && !empty($categories)?$categories:get_post_meta($post_id,"question_category",true));
				if (isset($categories) && is_array($categories) && !empty($categories)) {
					foreach ($categories as $category) {
						$categories_user_points = get_user_meta($user_id,"categories_user_points",true);
						if (empty($categories_user_points)) {
							update_user_meta($user_id,"categories_user_points",array($category));
						}else if (is_array($categories_user_points) && !in_array($category,$categories_user_points)) {
							update_user_meta($user_id,"categories_user_points",array_merge($categories_user_points,array($category)));
						}
						$_points_category = (int)get_user_meta($user_id,$user_id."_points_category".$category,true);
						$_points_category++;

						update_user_meta($user_id,$user_id."_points_category".$category,($_points_category > 0?$_points_category:0));
						add_user_meta($user_id,$user_id."_points_category_".$category.$_points_category,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$points,$relation,$message,($post_id > 0?$post_id:""),($comment_id > 0?$comment_id:""),"time" => current_time('timestamp'),"user_id" => ($another_user_id > 0?$another_user_id:"")));

						$points_category_user = (int)get_user_meta($user_id,"points_category".$category,true);
						$points_category_user = (int)($relation == "+"?$points_category_user+$points:$points_category_user-$points);
						update_user_meta($user_id,"points_category".$category,($points_category_user > 0?$points_category_user:0));

						if ($active_points_specific == "on") {
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

							$points_type_specific = "points_category".$category."_date_".date("j-n-Y");
							$points_user = (int)get_user_meta($user_id,$points_type_specific,true);
							$points_user = (int)($relation == "+"?$points_user+$points:$points_user-$points);
							update_user_meta($user_id,$points_type_specific,($points_user > 0?$points_user:0));

							$points_type_specific = "points_category".$category."_date_".date("Y-m-d H:i:s",strtotime($start_of_week.' this week'));
							$points_user = (int)get_user_meta($user_id,$points_type_specific,true);
							$points_user = (int)($relation == "+"?$points_user+$points:$points_user-$points);
							update_user_meta($user_id,$points_type_specific,($points_user > 0?$points_user:0));

							$points_type_specific = "points_category".$category."_date_".date("n-Y");
							$points_user = (int)get_user_meta($user_id,$points_type_specific,true);
							$points_user = (int)($relation == "+"?$points_user+$points:$points_user-$points);
							update_user_meta($user_id,$points_type_specific,($points_user > 0?$points_user:0));

							$points_type_specific = "points_category".$category."_date_".date("Y");
							$points_user = (int)get_user_meta($user_id,$points_type_specific,true);
							$points_user = (int)($relation == "+"?$points_user+$points:$points_user-$points);
							update_user_meta($user_id,$points_type_specific,($points_user > 0?$points_user:0));
						}
					}
				}
			}
		}
	}
endif;
/* Insert a new point */
function wpqa_insert_points($user_id = "",$another_user_id = "",$points = 0,$relation = "",$post_id = "",$comment_id = "",$text = "") {
	$active_points = wpqa_options("active_points");
	if ($active_points == "on") {
		$insert_data = array(
			'post_title'  => $text,
			'post_status' => "publish",
			'post_author' => $user_id,
			'post_type'   => "point"
		);
		remove_all_actions('save_post');
		$point_id = wp_insert_post($insert_data);
		if ($point_id == 0 || is_wp_error($point_id)) {
			error_log(esc_html__("Error in post.","wpqa"));
		}else {
			$variables = array();
			if ($another_user_id != "") {
				$variables["point_another_user_id"] = $another_user_id;
			}
			if ($points != "") {
				$variables["wpqa_points"] = $points;
			}
			if ($relation != "") {
				$variables["point_relation"] = $relation;
			}
			if ($post_id != "") {
				$variables["point_post_id"] = $post_id;
			}
			if ($comment_id != "") {
				$variables["point_comment_id"] = $comment_id;
			}
			if (is_array($variables) && !empty($variables)) {
				foreach ($variables as $key => $value) {
					update_post_meta($point_id,$key,$value);
				}
			}
			do_action("wpqa_action_points",$point_id,$user_id,$another_user_id,$points,$relation,$post_id,$comment_id,$text);
		}
	}
}
/* Get point result */
function wpqa_point_result($post,$admin = "") {
	$another_user_id = get_post_meta($post->ID,"point_another_user_id",true);
	$points = get_post_meta($post->ID,"wpqa_points",true);
	$relation = get_post_meta($post->ID,"point_relation",true);
	$post_id = get_post_meta($post->ID,"point_post_id",true);
	$comment_id = get_post_meta($post->ID,"point_comment_id",true);

	$point_result = array();
	$point_result["text"] = get_the_title($post->ID);
	$point_result["user_id"] = $post->post_author;
	$date_format = wpqa_options("date_format");
	$date_format = ($date_format?$date_format:get_option("date_format"));
	$time_format = wpqa_options("time_format");
	$time_format = ($time_format?$time_format:get_option("time_format"));
	$point_result["time"] = sprintf(esc_html__('%1$s at %2$s','wpqa'),get_the_time($date_format,$post->ID),get_the_time($time_format,$post->ID));
	if ($another_user_id != "") {
		$point_result["another_user_id"] = $another_user_id;
	}
	$point_result["points"] = ($points != ""?$points:0);
	$point_result["relation"] = ($relation != ""?$relation:"+");
	if ($post_id != "") {
		$point_result["post_id"] = $post_id;
	}
	if ($comment_id != "") {
		$point_result["comment_id"] = $comment_id;
	}
	return $point_result;
}
/* Show points */
if (!function_exists('wpqa_show_points')) :
	function wpqa_show_points($point_array) {
		$output = "";
		$output .= "<div class='notification__body'>
			<span class='point-span ".(isset($point_array["relation"]) && $point_array["relation"] == "+"?"point-span-plus":"point-span-minus")."'>".$point_array["relation"].$point_array["points"]."</span>";
			if (!empty($point_array["post_id"])) {
				$get_the_permalink = get_the_permalink($point_array["post_id"]);
				$get_post_status = get_post_status($point_array["post_id"]);
			}
			if (!empty($point_array["comment_id"])) {
				$get_comment = get_comment($point_array["comment_id"]);
			}
			if (!empty($whats_type_result["user_id"])) {
				$get_user_url = wpqa_profile_url($whats_type_result["user_id"]);
			}
			
			if (!empty($point_array["post_id"]) && !empty($point_array["comment_id"]) && $get_post_status != "trash" && isset($get_comment) && $get_comment->comment_approved != "spam" && $get_comment->comment_approved != "trash") {
				$output .= '<a class="notification__question notification__question-dark" href="'.get_the_permalink($point_array["post_id"]).(isset($point_array["comment_id"])?"#comment-".$point_array["comment_id"]:"").'">';
			}else if (!empty($point_array["post_id"]) && (empty($point_array["comment_id"])) && $get_post_status != "trash" && isset($get_the_permalink) && $get_the_permalink != "") {
				$output .= '<a class="notification__question notification__question-dark" href="'.get_the_permalink($point_array["post_id"]).'">';
			}else if (!empty($whats_type_result["user_id"]) && isset($get_user_url) && $get_user_url != "") {
				$output .= '<a class="author__name" href="'.esc_url($get_user_url).'">';
			}
				$result_text = apply_filters("wpqa_points_text",false,$point_array["text"]);
				if ($result_text != "") {
					$output .= $result_text;
				}else if ($point_array["text"] != "add_facebook" && $point_array["text"] != "add_tiktok" && $point_array["text"] != "add_twitter" && $point_array["text"] != "add_youtube" && $point_array["text"] != "add_vimeo" && $point_array["text"] != "add_linkedin" && $point_array["text"] != "add_instagram" && $point_array["text"] != "add_pinterest" && $point_array["text"] != "remove_facebook" && $point_array["text"] != "remove_tiktok" && $point_array["text"] != "remove_twitter" && $point_array["text"] != "remove_youtube" && $point_array["text"] != "remove_vimeo" && $point_array["text"] != "remove_linkedin" && $point_array["text"] != "remove_instagram" && $point_array["text"] != "remove_pinterest" && $point_array["text"] != "sticky_points" && $point_array["text"] != "subscribe_points" && $point_array["text"] != "ask_points" && $point_array["text"] != "answer_points" && $point_array["text"] != "buy_questions_points" && $point_array["text"] != "post_points" && $point_array["text"] != "buy_posts_points" && $point_array["text"] != "buy_points" && $point_array["text"] != "refund_points" && $point_array["text"] != "voting_question" && $point_array["text"] != "polling_question" && $point_array["text"] != "voting_answer" && $point_array["text"] != "rating_question" && $point_array["text"] != "rating_answer" && $point_array["text"] != "user_unfollow" && $point_array["text"] != "user_follow" && $point_array["text"] != "bump_question" && $point_array["text"] != "select_best_answer" && $point_array["text"] != "cancel_best_answer" && $point_array["text"] != "answer_question" && $point_array["text"] != "add_question" && $point_array["text"] != "add_post" && $point_array["text"] != "question_point" && $point_array["text"] != "gift_site" && $point_array["text"] != "points_referral" && $point_array["text"] != "referral_membership" && $point_array["text"] != "admin_add_points" && $point_array["text"] != "admin_remove_points" && $point_array["text"] != "point_back" && $point_array["text"] != "point_removed" && $point_array["text"] != "delete_answer" && $point_array["text"] != "delete_best_answer" && $point_array["text"] != "delete_follow_user" && $point_array["text"] != "delete_question" && $point_array["text"] != "delete_post" && $point_array["text"] != "rewarded_adv") {
					$output .= ($point_array["text"]);
				}else if ($point_array["text"] == "add_facebook") {
					$output .= esc_html__("You have added your Facebook link.","wpqa");
				}else if ($point_array["text"] == "add_tiktok") {
					$output .= esc_html__("You have added your TikTok link.","wpqa");
				}else if ($point_array["text"] == "add_twitter") {
					$output .= esc_html__("You have added your Twitter link.","wpqa");
				}else if ($point_array["text"] == "add_youtube") {
					$output .= esc_html__("You have added your Youtube link.","wpqa");
				}else if ($point_array["text"] == "add_vimeo") {
					$output .= esc_html__("You have added your Vimeo link.","wpqa");
				}else if ($point_array["text"] == "add_linkedin") {
					$output .= esc_html__("You have added your Linkedin link.","wpqa");
				}else if ($point_array["text"] == "add_instagram") {
					$output .= esc_html__("You have added your Instagram link.","wpqa");
				}else if ($point_array["text"] == "add_pinterest") {
					$output .= esc_html__("You have added your Pinterest link.","wpqa");
				}else if ($point_array["text"] == "remove_facebook") {
					$output .= esc_html__("You have removed your Facebook link.","wpqa");
				}else if ($point_array["text"] == "remove_tiktok") {
					$output .= esc_html__("You have removed your TikTok link.","wpqa");
				}else if ($point_array["text"] == "remove_twitter") {
					$output .= esc_html__("You have removed your Twitter link.","wpqa");
				}else if ($point_array["text"] == "remove_youtube") {
					$output .= esc_html__("You have removed your Youtube link.","wpqa");
				}else if ($point_array["text"] == "remove_vimeo") {
					$output .= esc_html__("You have removed your Vimeo link.","wpqa");
				}else if ($point_array["text"] == "remove_linkedin") {
					$output .= esc_html__("You have removed your Linkedin link.","wpqa");
				}else if ($point_array["text"] == "remove_instagram") {
					$output .= esc_html__("You have removed your Instagram link.","wpqa");
				}else if ($point_array["text"] == "remove_pinterest") {
					$output .= esc_html__("You have removed your Pinterest link.","wpqa");
				}else if ($point_array["text"] == "sticky_points") {
					$output .= esc_html__("You have stickied your question by points.","wpqa");
				}else if ($point_array["text"] == "ask_points") {
					$output .= esc_html__("You have bought to ask a question by points.","wpqa");
				}else if ($point_array["text"] == "answer_points") {
					$output .= esc_html__("You have bought to add an answer by points.","wpqa");
				}else if ($point_array["text"] == "buy_questions_points") {
					$output .= esc_html__("You have bought to ask questions by points.","wpqa");
				}else if ($point_array["text"] == "post_points") {
					$output .= esc_html__("You have bought to add a post by points.","wpqa");
				}else if ($point_array["text"] == "buy_posts_points") {
					$output .= esc_html__("You have bought to add posts by points.","wpqa");
				}else if ($point_array["text"] == "subscribe_points") {
					$output .= esc_html__("You have subscribed to paid membership by points.","wpqa");
				}else if ($point_array["text"] == "buy_points") {
					$output .= esc_html__("You have bought new points.","wpqa");
				}else if ($point_array["text"] == "refund_points") {
					$output .= esc_html__("You got a refund for your buying of the points.","wpqa");
				}else if ($point_array["text"] == "voting_question" || $point_array["text"] == "rating_question") {
					$output .= esc_html__("Voted your question.","wpqa");
				}else if ($point_array["text"] == "polling_question") {
					$output .= esc_html__("Polled a question.","wpqa");
				}else if ($point_array["text"] == "voting_answer" || $point_array["text"] == "rating_answer") {
					$output .= esc_html__("Voted your answer.","wpqa");
				}else if ($point_array["text"] == "user_follow") {
					$output .= esc_html__("User followed You.","wpqa");
				}else if ($point_array["text"] == "user_unfollow") {
					$output .= esc_html__("User unfollowed You.","wpqa");
				}else if ($point_array["text"] == "bump_question") {
					$output .= esc_html__("Discount points to bump question.","wpqa");
				}else if ($point_array["text"] == "select_best_answer") {
					$output .= esc_html__("Chosen your answer as Best answer.","wpqa");
				}else if ($point_array["text"] == "cancel_best_answer") {
					$output .= esc_html__("Canceled your answer as the best answer.","wpqa");
				}else if ($point_array["text"] == "answer_question") {
					$output .= esc_html__("You have answered the question.","wpqa");
				}else if ($point_array["text"] == "add_question") {
					$output .= esc_html__("Added a new question.","wpqa");
				}else if ($point_array["text"] == "delete_question") {
					$output .= esc_html__("Deleted your question.","wpqa");
				}else if ($point_array["text"] == "delete_post") {
					$output .= esc_html__("Deleted your post.","wpqa");
				}else if ($point_array["text"] == "add_post") {
					$output .= esc_html__("Added a new post.","wpqa");
				}else if ($point_array["text"] == "gift_site") {
					$output .= esc_html__("Gift of the site.","wpqa");
				}else if ($point_array["text"] == "points_referral") {
					$output .= esc_html__("Refer a new user.","wpqa");
				}else if ($point_array["text"] == "referral_membership") {
					$output .= esc_html__("Refer a new user for paid membership.","wpqa");
				}else if ($point_array["text"] == "question_point") {
					$output .= esc_html__("Points have been deducted for asking a question.","wpqa");
				}else if ($point_array["text"] == "admin_add_points") {
					$output .= esc_html__("The administrator added points for you.","wpqa");
				}else if ($point_array["text"] == "admin_remove_points") {
					$output .= esc_html__("The administrator removed points from you.","wpqa");
				}else if ($point_array["text"] == "point_back") {
					$output .= esc_html__("Your points have been added because the Best answer was selected.","wpqa");
				}else if ($point_array["text"] == "point_removed") {
					$output .= esc_html__("Your point has been removed because the Best answer was removed.","wpqa");
				}else if ($point_array["text"] == "delete_answer") {
					$output .= esc_html__("Your answer was removed.","wpqa");
				}else if ($point_array["text"] == "delete_best_answer") {
					$output .= esc_html__("Deleted your best answer.","wpqa");
				}else if ($point_array["text"] == "delete_follow_user") {
					$output .= esc_html__("Deleted your following user.","wpqa");
				}else if ($point_array["text"] == "rewarded_adv") {
					$output .= esc_html__("Get points from the rewarded adv.","wpqa");
				}
			if ((!empty($point_array["post_id"]) && !empty($point_array["comment_id"]) && $get_post_status != "trash" && isset($get_comment) && $get_comment->comment_approved != "spam" && $get_comment->comment_approved != "trash") || (!empty($point_array["post_id"]) && (empty($point_array["comment_id"])) && $get_post_status != "trash" && isset($get_the_permalink) && $get_the_permalink != "") || (!empty($whats_type_result["user_id"]) && isset($get_user_url) && $get_user_url != "")) {
				$output .= "</a>";
			}
		return $output;	
	}
endif;?>