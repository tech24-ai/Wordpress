<?php

/* @author    2codeThemes
*  @package   WPQA/widgets
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Users */
add_action( 'widgets_init', 'wpqa_widget_users_widget' );
function wpqa_widget_users_widget() {
	register_widget( 'Widget_Users' );
}

class Widget_Users extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'users-widget' );
		$control_ops = array( 'id_base' => 'users-widget' );
		parent::__construct( 'users-widget',wpqa_widgets.' - Users', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$active_points = wpqa_options("active_points");
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
		$title           = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$user_number     = (int)(isset($instance['user_number'])?$instance['user_number']:5);
		$user_number     = ($user_number > 0?$user_number:5);
		$user_sort       = (isset($instance['user_sort'])?esc_html($instance['user_sort']):'');
		$specific_points = (isset($instance['specific_points'])?esc_html($instance['specific_points']):"");
		$specific_time   = (isset($instance['specific_time'])?esc_html($instance['specific_time']):"");
		$user_order      = (isset($instance['user_order'])?esc_html($instance['user_order']):'');
		$user_group      = (isset($instance['user_group'])?$instance['user_group']:'');
		$points_cat      = (isset($instance['points_categories'])?esc_html($instance['points_categories']):"");
		$crown_king      = (isset($instance['crown_king'])?esc_html($instance['crown_king']):"");
		$show_icon       = (isset($instance['show_icon'])?esc_html($instance['show_icon']):"");
		$points_category = wpqa_options("active_points_category");
		$points_specific = wpqa_options("active_points_specific");
		if ($points_cat !== "on" || ($points_cat === "on" && $points_category == "on" && is_tax(wpqa_question_categories))) {
			if ($points_category == "on" && is_tax(wpqa_question_categories)) {
				$user_sort = "points";
			}
			echo ($before_widget);
				if ($title) {
					echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
				}else {
					echo "<h3 class='screen-reader-text'>".esc_html__("Users","wpqa")."</h3>";
				}?>
				<div class="widget-wrap">
					<?php echo "<div class='user-section user-section-small row row-warp row-boot user-not-normal".($crown_king == "on"?" widget-user-crown":"")."'>";
						$role__in = array('role__in' => (isset($user_group) && is_array($user_group)?$user_group:array()));
						$user_sort = (isset($user_sort) && $user_sort != ""?$user_sort:"user_registered");
						if (($user_sort == "points" && $active_points == "on") || $user_sort == "the_best_answer" || $user_sort == "post_count" || $user_sort == "question_count" || $user_sort == "answers" || $user_sort == "comments") {
							if ($user_sort == "the_best_answer") {
								$meta_query = "wpqa_count_best_answers";
							}else if ($user_sort == "post_count") {
								$meta_query = "wpqa_posts_count";
							}else if ($user_sort == "question_count") {
								$meta_query = "wpqa_questions_count";
							}else if ($user_sort == "answers") {
								$meta_query = "wpqa_answers_count";
							}else if ($user_sort == "comments") {
								$meta_query = "wpqa_comments_count";
							}else if ($user_sort == "points" && $active_points == "on") {
								$meta_query = "points";
								if ($points_specific == "on" && $specific_points == "on") {
									if ($specific_time == "day") {
										$meta_query = "points_date_".date("j-n-Y");
									}else if ($specific_time == "week") {
										$meta_query = "points_date_".date("Y-m-d H:i:s",strtotime($start_of_week.' this week'));
									}else if ($specific_time == "month") {
										$meta_query = "points_date_".date("n-Y");
									}else if ($specific_time == "year") {
										$meta_query = "points_date_".date("Y");
									}
								}
								if ($points_category == "on" && is_tax(wpqa_question_categories)) {
									$term_id  = (int)get_query_var('wpqa_term_id');
									$meta_query = "points_category".$term_id;
									if ($points_specific == "on" && $specific_points == "on") {
										$meta_query = "points_category".$term_id;
										if ($specific_time == "day") {
											$meta_query = "points_category".$term_id."_date_".date("j-n-Y");
										}else if ($specific_time == "week") {
											$meta_query = "points_category".$term_id."_date_".date("Y-m-d H:i:s",strtotime($start_of_week.' this week'));
										}else if ($specific_time == "month") {
											$meta_query = "points_category".$term_id."_date_".date("n-Y");
										}else if ($specific_time == "year") {
											$meta_query = "points_category".$term_id."_date_".date("Y");
										}
									}
								}
							}
							$args = array_merge($role__in,array(
								'meta_query'  => array(array("key" => $meta_query,"value" => 0,"compare" => ">")),
								'orderby'     => 'meta_value_num',
								'order'       => $user_order,
								'number'      => $user_number,
								'fields'      => 'ID',
								'count_total' => false,
							));
							$query = new WP_User_Query($args);
							$get_results = true;
						}else {
							if ($user_sort != "user_registered" && $user_sort != "display_name" && $user_sort != "ID") {
								$user_sort = "user_registered";
							}
							$args = array_merge($role__in,array(
								'orderby'     => $user_sort,
								'order'       => $user_order,
								'number'      => $user_number,
								'fields'      => 'ID',
								'count_total' => false,
							));
							$query = new WP_User_Query($args);
							$get_results = true;
						}
						
						if (isset($query)) {
							$query = (isset($get_results)?$query->get_results():$query);
							foreach ($query as $user) {
								$user = (isset($user->ID)?$user->ID:$user);
								if ($points_cat !== "on" && $points_category == "on" && is_tax(wpqa_question_categories)) {
									$categories_user_points = get_user_meta($user,"categories_user_points",true);
									if (is_array($categories_user_points) && !empty($categories_user_points)) {
										foreach ($categories_user_points as $category) {
											$points_category_user[$category] = (int)get_user_meta($user,"points_category".$category,true);
										}
										arsort($points_category_user);
										$first_category = (is_array($points_category_user)?key($points_category_user):"");
										$first_points = reset($points_category_user);
									}
								}
								$owner_widget = false;
								if (get_current_user_id() == $user) {
									$owner_widget = true;
								}
								echo "<div class='col col12 col-boot-12'>".wpqa_author($user,"small",$owner_widget,($user_sort == "post_count"?"post":$user_sort),"widget","","",(isset($term_id) && $term_id !== ""?$term_id:(isset($first_points) && $first_points !== ""?$first_points:"")),(isset($show_icon) && $show_icon == "on"?$show_icon:""),($points_specific == "on" && $specific_points == "on" && $specific_time != ""?$specific_time:""))."</div>";
							}
						}?>
					</div>
				</div>
			<?php echo ($after_widget);
		}
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>