<?php

/* @author    2codeThemes
*  @package   WPQA/widgets
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Related */
add_action( 'widgets_init', 'wpqa_widget_related_widget' );
function wpqa_widget_related_widget() {
	register_widget( 'Widget_Related' );
}
class Widget_Related extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'related-widget' );
		$control_ops = array( 'id_base' => 'related-widget' );
		$widget_name = wpqa_widgets.' - Related Questions or Posts';
		$activate_knowledgebase = apply_filters("wpqa_activate_knowledgebase",false);
		if ($activate_knowledgebase == true) {
			$widget_name = wpqa_widgets.' - Related Questions, Knowledgebases or Posts';
		}
		parent::__construct( 'related-widget', $widget_name, $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		if (is_singular("post") || is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || is_singular(wpqa_knowledgebase_type)) {
			global $post;
			extract( $args );
			$title            = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
			$post_or_question = (isset($instance['post_or_question'])?$instance['post_or_question']:array(wpqa_questions_type => wpqa_questions_type));
			if (is_array($post_or_question) && !empty($post_or_question)) {
				foreach ($post_or_question as $key => $value) {
					if ((is_singular($value) && $key == $value && $value != wpqa_questions_type && $value != wpqa_knowledgebase_type) || ((is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) && $key == $value && $value == wpqa_questions_type) || (is_singular(wpqa_knowledgebase_type) && $key == $value && $value == wpqa_knowledgebase_type)) {
						$related_number   = (int)(isset($instance['related_number_'.$value])?$instance['related_number_'.$value]:5);
						$show_images      = (isset($instance['show_images_'.$value])?esc_html($instance['show_images_'.$value]):'');
						$excerpt_title    = (isset($instance['excerpt_title_'.$value])?esc_html($instance['excerpt_title_'.$value]):'');
						$display_comments = ($value == "post"?(isset($instance['display_comment_meta'])?esc_html($instance['display_comment_meta']):''):(isset($instance['display_answers'])?esc_html($instance['display_answers']):''));
						$query_related    = (isset($instance['query_related_'.$value])?esc_html($instance['query_related_'.$value]):'');
						$post_style       = (isset($instance[$value.'_style'])?$instance[$value.'_style']:"");
						$display_image    = (isset($instance['display_image_'.$value])?$instance['display_image_'.$value]:"");
						$display_video    = (isset($instance['display_video_'.$value])?$instance['display_video_'.$value]:"");
						$display_date     = (isset($instance['display_date_'.$value])?$instance['display_date_'.$value]:"");
						$excerpt_post     = (isset($instance['excerpt_'.$value])?$instance['excerpt_'.$value]:"");
						$related_number   = ($related_number > 0?$related_number:5);
						$excerpt_title    = ($excerpt_title > 0?$excerpt_title:10);
						
						$get_question_user_id = get_post_meta($post->ID,"user_id",true);
						if ($value == "post") {
							$cat_taxonomy = "category";
							$tag_taxonomy = "post_tag";
						}else if ($value == wpqa_knowledgebase_type) {
							$cat_taxonomy = wpqa_knowledgebase_categories;
							$tag_taxonomy = wpqa_knowledgebase_tags;
						}else {
							$cat_taxonomy = wpqa_question_categories;
							$tag_taxonomy = wpqa_question_tags;
						}
						$term_list = wp_get_post_terms($post->ID, $tag_taxonomy, array("fields" => "ids"));
						if (isset($term_list) && !empty($term_list) && $query_related == "tags") {
							$related_query_ = array('tax_query' => array(array('taxonomy' => $tag_taxonomy,'field' => 'id','terms'  => $term_list,'operator' => 'IN')));
						}else if ($query_related == "author" || esc_html($get_question_user_id) != "") {
							$related_query_ = (esc_html($get_question_user_id) != ""?array():array('author' => $post->post_author));
						}else {
							$categories = get_the_terms($post->ID,$cat_taxonomy);
							$category_ids = array();
							if (isset($categories) && is_array($categories)) {
								foreach ($categories as $l_category) {
									$category_ids[] = $l_category->term_id;
								}
							}
							$related_query_ = array('tax_query' => array(array('taxonomy' => $cat_taxonomy,'field' => 'id','terms'  => $category_ids,'operator' => 'IN')));
						}
						
						$args = array_merge($related_query_,array('post_type' => ($value != wpqa_questions_type && $value != wpqa_asked_questions_type?$value:(esc_html($get_question_user_id) != ""?wpqa_asked_questions_type:wpqa_questions_type)),'post__not_in' => array($post->ID),'posts_per_page'=> $related_number,'cache_results' => false,'no_found_rows' => true,"meta_query" => array((esc_html($get_question_user_id) != ""?array("type" => "numeric","key" => "user_id","value" => (int)$get_question_user_id,"compare" => "="):array()))));
						
						$args = array(
							"excerpt_title"    => $excerpt_title,
							"show_images"      => $show_images,
							"post_or_question" => $value,
							"display_comment"  => $display_comments,
							"custom_args"      => $args,
							"post_style"       => $post_style,
							"display_image"    => $display_image,
							"display_video"    => $display_video,
							"display_date"     => $display_date,
							"no_query"         => "no_query",
							"excerpt_post"     => $excerpt_post
						);
						$wpqa_posts = wpqa_posts($args);
						
						if (($query_related == "tags" || $query_related == "author") && $wpqa_posts == "no_query") {
							$categories = get_the_terms($post->ID,$cat_taxonomy);
							$category_ids = array();
							if (isset($categories) && !empty($categories)) {
								foreach ($categories as $l_category) {
									$category_ids[] = (isset($l_category->term_id)?$l_category->term_id:"");
								}
							}
							
							$related_query_ = array('tax_query' => array(array('taxonomy' => $cat_taxonomy,'field' => 'id','terms'  => $category_ids,'operator' => 'IN')));
							
							$args = array_merge($related_query_,array('post_type' => ($value != wpqa_questions_type && $value != wpqa_asked_questions_type?$value:(esc_html($get_question_user_id) != ""?wpqa_asked_questions_type:wpqa_questions_type)),'post__not_in' => array($post->ID),'posts_per_page'=> $related_number,'cache_results' => false,'no_found_rows' => true,"meta_query" => array((esc_html($get_question_user_id) != ""?array("type" => "numeric","key" => "user_id","value" => (int)$get_question_user_id,"compare" => "="):array()))));

							$args = array(
								"excerpt_title"    => $excerpt_title,
								"show_images"      => $show_images,
								"post_or_question" => $value,
								"display_comment"  => $display_comments,
								"custom_args"      => $args,
								"post_style"       => $post_style,
								"display_image"    => $display_image,
								"display_video"    => $display_video,
								"display_date"     => $display_date,
								"no_query"         => "no_query",
								"excerpt_post"     => $excerpt_post
							);
							$wpqa_posts = wpqa_posts($args);
						}
						
						if ($wpqa_posts != "no_query") {
							echo ($before_widget);
								if ($title) {
									$related_title = esc_html__("Related Posts","wpqa");
									if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) {
										$related_title = esc_html__("Related Questions","wpqa");
									}else if (is_singular(wpqa_knowledgebase_type)) {
										$related_title = esc_html__("Related Articles","wpqa");
									}
									echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":$related_title).$after_title).($title == "empty"?"</div>":"");
								}else {
									echo "<h3 class='screen-reader-text'>".$related_title."</h3>";
								}
								echo ($wpqa_posts);
								
							echo ($after_widget);
						}
					}
				}
			}
		}
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>