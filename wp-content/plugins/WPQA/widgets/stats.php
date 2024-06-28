<?php

/* @author    2codeThemes
*  @package   WPQA/widgets
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Stats */
add_action( 'widgets_init', 'wpqa_widget_stats_widget' );
function wpqa_widget_stats_widget() {
	register_widget( 'Widget_Stats' );
}

class Widget_Stats extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'stats-widget' );
		$control_ops = array( 'id_base' => 'stats-widget' );
		parent::__construct( 'stats-widget',wpqa_widgets.' - Stats', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title   = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$stats   = (isset($instance['stats'])?$instance['stats']:'');
		$style   = (isset($instance['style'])?$instance['style']:'');
		$divider = (isset($instance['divider'])?$instance['divider']:'');
		
		if (empty($divider) || $divider != "on") {
			$before_widget = str_replace('class="','class="widget-no-divider ',$before_widget);
		}
		  
		echo ($before_widget);
			if ($title) {
				echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
			}else {
				echo "<h3 class='screen-reader-text'>".esc_html__("Stats","wpqa")."</h3>";
			}?>
			<div class="widget-wrap stats-card">
				<ul class="<?php echo ($style == "style_2"?"stats-inner-2":"stats-inner list-unstyled mb-0").($style == "style_3"?" stats-inner-3 stats-card-layout2":"")?>">
					<?php if (isset($stats) && is_array($stats) && !empty($stats)) {
						$count_comment_only = wpqa_options("count_comment_only");
						foreach ($stats as $key => $value) {
							if (isset($value["value"]) && $value["value"] == $key) {?>
								<li class="stats-card__item stats-<?php echo ($value["value"])?>">
									<div class="<?php echo ($style == "style_1"?"d-flex justify-content-between stats-card__item_div":"").($style == "style_3"?"d-flex flex-column stats-card__item_div":"")?>">
										<?php if ((has_himer() || has_knowly()) && ($style == "style_1" || $style == "style_3")) {
											echo '<div class="stats-card__item__icon">';
										}
										if (has_himer() || has_knowly() || $style == "style_2") {
											if ($value["value"] == "questions") {
												echo '<i class="icon-book-open"></i>';
											}else if ($value["value"] == "knowledgebases") {
												echo '<i class="icon-folder"></i>';
											}else if ($value["value"] == "posts") {
												echo '<i class="icon-user"></i>';
											}else if ($value["value"] == "answers") {
												echo '<i class="icon-comment"></i>';
											}else if ($value["value"] == "comments") {
												echo '<i class="icon-chat"></i>';
											}else if ($value["value"] == "best_answers") {
												echo '<i class="icon-graduation-cap"></i>';
											}else if ($value["value"] == "users") {
												echo '<i class="icon-users"></i>';
											}else if ($value["value"] == "groups") {
												echo '<i class="icon-android-contacts"></i>';
											}else if ($value["value"] == "group_posts") {
												echo '<i class="icon-document-text"></i>';
											}
											do_action("wpqa_widget_stats_icons",$value);
										}
										if ((has_himer() || has_knowly()) && ($style == "style_1" || $style == "style_3")) {
											echo '</div>
											<div class="stats-card__item__text w-100">';
										}?>
										<span class="<?php echo ($style == "style_2"?"stats-text-2":"stats-text")?>">
											<?php if ($value["value"] == "questions") {
												$question_count = wp_count_posts(wpqa_questions_type);
												$questions_count = (isset($question_count->publish)?$question_count->publish:0);
												$asked_question_count = wp_count_posts(wpqa_asked_questions_type);
												$asked_questions_count = (isset($asked_question_count->publish)?$asked_question_count->publish:0);
												$questions_count = $questions_count+$asked_questions_count;
												echo _n("Question","Questions",$questions_count,"wpqa");
											}else if ($value["value"] == "knowledgebases") {
												$knowledgebase_count = wp_count_posts(wpqa_knowledgebase_type);
												$knowledgebases_count = (isset($knowledgebase_count->publish)?$knowledgebase_count->publish:0);
												echo _n("Article","Articles",$knowledgebases_count,"wpqa");
											}else if ($value["value"] == "posts") {
												$posts_count = wp_count_posts("post");
												$posts_count = (isset($posts_count->publish)?$posts_count->publish:0);
												echo _n("Post","Posts",$posts_count,"wpqa");
											}else if ($value["value"] == "answers") {
												$answers_count = wpqa_comments_of_post_type(array(wpqa_questions_type,wpqa_asked_questions_type),0,array(),"",($count_comment_only == "on"?0:""));
												echo _n("Answer","Answers",$answers_count,"wpqa");
											}else if ($value["value"] == "comments") {
												$comments_count = wpqa_comments_of_post_type("post",0,array(),"",($count_comment_only == "on"?0:""));
												echo _n("Comment","Comments",$comments_count,"wpqa");
											}else if ($value["value"] == "best_answers") {
												$best_answers_count = (int)get_option("wpqa_best_answer");
												echo _n("Best Answer","Best Answers",$best_answers_count,"wpqa");
											}else if ($value["value"] == "users") {
												$count_users = count_users();
												$users_count = 0;
												foreach ($count_users["avail_roles"] as $role => $count) {
													if ($role != "wpqa_under_review" && $role != "activation" && $role != "ban_group") {
														$users_count += $count;
													}
												}
												$users_count = (int)$users_count;
												echo _n("User","Users",$users_count,"wpqa");
											}else if ($value["value"] == "groups") {
												$groups_count = wp_count_posts("group");
												$groups_count = (isset($groups_count->publish)?$groups_count->publish:0);
												echo _n("Group","Groups",$groups_count,"wpqa");
											}else if ($value["value"] == "group_posts") {
												$group_posts_count = wpqa_count_group_posts_by_type("posts");
												echo _n("Group Post","Group Posts",$group_posts_count,"wpqa");
											}
											do_action("wpqa_widget_stats_text",$value);
											echo ($style == "style_2"?" : ":"")?>
										</span>
										<span class="<?php echo ($style == "style_2"?"stats-value-2":"stats-value")?>">
											<?php if ($value["value"] == "questions") {
												echo wpqa_count_number($questions_count);
											}else if ($value["value"] == "knowledgebases") {
												echo wpqa_count_number($knowledgebases_count);
											}else if ($value["value"] == "posts") {
												echo wpqa_count_number($posts_count);
											}else if ($value["value"] == "answers") {
												echo wpqa_count_number($answers_count);
											}else if ($value["value"] == "comments") {
												echo wpqa_count_number($comments_count);
											}else if ($value["value"] == "best_answers") {
												echo wpqa_count_number($best_answers_count);
											}else if ($value["value"] == "users") {
												echo wpqa_count_number($users_count);
											}else if ($value["value"] == "groups") {
												echo wpqa_count_number($groups_count);
											}else if ($value["value"] == "group_posts") {
												echo wpqa_count_number($group_posts_count);
											}
											do_action("wpqa_widget_stats_count",$value);?>
										</span>
										<?php if ((has_himer() || has_knowly()) && ($style == "style_1" || $style == "style_3")) {
											echo '</div>';
										}?>
									</div>
								</li>
							<?php }
						}
					}?>
				</ul>
			</div>
		<?php echo ($after_widget);
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>