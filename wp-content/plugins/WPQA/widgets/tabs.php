<?php

/* @author    2codeThemes
*  @package   WPQA/widgets
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Tabs */
add_action( 'widgets_init', 'wpqa_widget_tabs_widget' );
function wpqa_widget_tabs_widget() {
	register_widget( 'Widget_Tabs' );
}
class Widget_Tabs extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'tabs-widget' );
		$control_ops = array( 'id_base' => 'tabs-widget' );
		parent::__construct( 'tabs-widget',wpqa_widgets.' - Tabs', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$rand_w                   = (isset($args['widget_id'])?$args['widget_id']:rand(1,1000));
		$title                    = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$post_or_question         = (isset($instance['post_or_question'])?esc_html($instance['post_or_question']):'');
		$tabs                     = (isset($instance['tabs'])?$instance['tabs']:'');
		$posts_per_page           = (int)(isset($instance['posts_per_page'])?$instance['posts_per_page']:5);
		$display_comment          = (isset($instance['display_comment_meta'])?esc_html($instance['display_comment_meta']):'');
		$display_date             = (isset($instance['display_date_post'])?esc_html($instance['display_date_post']):'');
		$show_images              = (isset($instance['show_images_post'])?esc_html($instance['show_images_post']):'');
		$orderby                  = (isset($instance['orderby_post'])?esc_html($instance['orderby_post']):'');
		$excerpt_title            = (int)(isset($instance['excerpt_title_post'])?$instance['excerpt_title_post']:10);
		$images_comment           = (isset($instance['images_comment'])?esc_html($instance['images_comment']):'');
		$excerpt_comment          = (int)(isset($instance['excerpt_comment'])?$instance['excerpt_comment']:10);
		$comments_number          = (int)(isset($instance['comments_number'])?$instance['comments_number']:5);
		$post_style               = (isset($instance['post_style'])?$instance['post_style']:"");
		$display_image            = (isset($instance['display_image'])?$instance['display_image']:"");
		$display_video            = (isset($instance['display_video'])?$instance['display_video']:"");
		$display_date_2           = (isset($instance['display_date_2'])?$instance['display_date_2']:"");
		$date_comments            = (isset($instance['specific_date_comments'])?$instance['specific_date_comments']:"");
		$display                  = (isset($instance['display'])?esc_html($instance['display']):'');
		$category                 = (isset($instance['category'])?esc_html($instance['category']):'');
		$categories               = (isset($instance['categories'])?$instance['categories']:array());
		$e_categories             = (isset($instance['exclude_categories'])?$instance['exclude_categories']:array());
		$custom_posts             = (isset($instance['custom_posts'])?$instance['custom_posts']:"");
		$display_question         = (isset($instance['display_question'])?esc_html($instance['display_question']):'');
		$category_question        = (isset($instance['category_question'])?esc_html($instance['category_question']):'');
		$categories_question      = (isset($instance['categories_question'])?$instance['categories_question']:array());
		$e_cats_question          = (isset($instance['exclude_categories_question'])?$instance['exclude_categories_question']:array());
		$custom_questions         = (isset($instance['custom_questions'])?$instance['custom_questions']:"");
		$display_knowledgebase    = (isset($instance['display_knowledgebase'])?esc_html($instance['display_knowledgebase']):'');
		$category_knowledgebase   = (isset($instance['category_knowledgebase'])?esc_html($instance['category_knowledgebase']):'');
		$categories_knowledgebase = (isset($instance['categories_knowledgebase'])?$instance['categories_knowledgebase']:array());
		$e_cats_knowledgebase     = (isset($instance['exclude_categories_knowledgebase'])?$instance['exclude_categories_knowledgebase']:array());
		$custom_knowledgebases    = (isset($instance['custom_knowledgebases'])?$instance['custom_knowledgebases']:"");
		$excerpt_post             = (isset($instance['excerpt_post'])?$instance['excerpt_post']:"");
		$specific_date            = (isset($instance['specific_date_post'])?$instance['specific_date_post']:"");

		if ($post_or_question == wpqa_questions_type) {
			$tabs            = (isset($instance['tabs_questions'])?$instance['tabs_questions']:'');
			$posts_per_page  = (int)(isset($instance['questions_per_page'])?$instance['questions_per_page']:5);
			$display_comment = (isset($instance['display_answer_meta'])?esc_html($instance['display_answer_meta']):'');
			$display_date    = (isset($instance['display_date'])?esc_html($instance['display_date']):'');
			$show_images     = (isset($instance['show_images'])?esc_html($instance['show_images']):'');
			$orderby         = (isset($instance['orderby'])?esc_html($instance['orderby']):'');
			$excerpt_title   = (int)(isset($instance['excerpt_title'])?$instance['excerpt_title']:10);
			$images_comment  = (isset($instance['images_answer'])?esc_html($instance['images_answer']):'');
			$excerpt_comment = (int)(isset($instance['excerpt_answer'])?$instance['excerpt_answer']:10);
			$comments_number = (int)(isset($instance['answers_number'])?$instance['answers_number']:5);
			$specific_date   = (isset($instance['specific_date'])?$instance['specific_date']:"");
			$post_style      = (isset($instance['question_style'])?$instance['question_style']:"");
			$display_image   = (isset($instance['display_image_question'])?$instance['display_image_question']:"");
			$display_video   = (isset($instance['display_video_question'])?$instance['display_video_question']:"");
			$display_date_2  = (isset($instance['display_date_2_question'])?$instance['display_date_2_question']:"");
			$date_comments   = (isset($instance['specific_date_answers'])?$instance['specific_date_answers']:"");
		}else if ($post_or_question == wpqa_knowledgebase_type) {
			$tabs           = (isset($instance['tabs_knowledgebases'])?$instance['tabs_knowledgebases']:'');
			$posts_per_page = (int)(isset($instance['knowledgebases_per_page'])?$instance['knowledgebases_per_page']:5);
			$orderby        = (isset($instance['orderby_knowledgebase'])?esc_html($instance['orderby_knowledgebase']):'');
			$excerpt_title  = (int)(isset($instance['excerpt_title_knowledgebase'])?$instance['excerpt_title_knowledgebase']:10);
			$specific_date  = (isset($instance['specific_date_knowledgebase'])?$instance['specific_date_knowledgebase']:"");
		}
		
		if (isset($tabs) && is_array($tabs) && !empty($tabs)) {?>
			<div class='widget card tabs-wrap widget-tabs'>
				<div class="widget-title widget-title-tabs">
					<ul class="tabs tabs<?php echo esc_attr($rand_w);?>">
						<?php foreach ($tabs as $key => $value) {
							if (isset($value["value"])) {
								if ($value["value"] == "display_posts") {?>
									<li class="tab"><a href="#"><?php if ($orderby == "no_response" && $post_or_question != wpqa_knowledgebase_type) {echo ($post_or_question == wpqa_questions_type?esc_html__('No answers','wpqa'):esc_html__('No comments','wpqa'));}elseif ($orderby == "most_reacted") {esc_html_e('Most reacted','wpqa');}elseif ($orderby == "most_voted") {esc_html_e('Most voted','wpqa');}elseif ($orderby == "most_visited") {esc_html_e('Most visited','wpqa');}elseif ($orderby == "most_rated") {esc_html_e('Most rated','wpqa');}elseif ($orderby == "popular") {esc_html_e('Popular','wpqa');}elseif ($orderby == "random") {esc_html_e('Random','wpqa');}else {esc_html_e('Recent','wpqa');}?></a></li>
								<?php }else if ($value["value"] == "display_comments" && $post_or_question != wpqa_knowledgebase_type) {?>
									<li class="tab"><a href="#"><?php echo ($post_or_question == wpqa_questions_type?esc_html__('Answers','wpqa'):esc_html__('Comments','wpqa'))?></a></li>
								<?php }else if ($value["value"] == "display_tags") {?>
									<li class="tab"><a href="#"><?php esc_html_e('Tags','wpqa')?></a></li>
								<?php }
							}
						}?>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="widget-wrap">
					<?php foreach ($tabs as $key => $value) {
						if (isset($value["value"])) {
							if ($value["value"] == "display_posts") {
								echo "<div class='widget-posts tab-inner-wrap tab-inner-wrap".esc_attr($rand_w)."'>";
									$args = array(
										"posts_per_page"           => $posts_per_page,
										"orderby"                  => $orderby,
										"excerpt_title"            => $excerpt_title,
										"show_images"              => $show_images,
										"post_or_question"         => $post_or_question,
										"display_comment"          => $display_comment,
										"display"                  => $display,
										"category"                 => $category,
										"categories"               => $categories,
										"e_categories"             => $e_categories,
										"custom_posts"             => $custom_posts,
										"display_question"         => $display_question,
										"category_question"        => $category_question,
										"categories_question"      => $categories_question,
										"e_cats_question"          => $e_cats_question,
										"custom_questions"         => $custom_questions,
										"display_knowledgebase"    => $display_knowledgebase,
										"category_knowledgebase"   => $category_knowledgebase,
										"categories_knowledgebase" => $categories_knowledgebase,
										"e_cats_knowledgebase"     => $e_cats_knowledgebase,
										"custom_knowledgebases"    => $custom_knowledgebases,
										"post_style"               => $post_style,
										"display_date"             => $display_date_2,
										"display_image"            => $display_image,
										"display_video"            => $display_video,
										"excerpt_post"             => $excerpt_post,
										"specific_date"            => $specific_date
									);
									echo wpqa_posts($args);
								echo "</div>";
							}else if ($value["value"] == "display_comments" && $post_or_question != wpqa_knowledgebase_type) {
								echo "<div class='tab-inner-wrap tab-inner-wrap".esc_attr($rand_w)."'>";
									$args = array(
										'post_or_question' => $post_or_question,
										'comments_number'  => $comments_number,
										'comment_excerpt'  => $excerpt_comment,
										'show_images'      => $images_comment,
										'display_date'     => $display_date,
										'specific_date'    => $date_comments,
									);
									wpqa_comments($args);
								echo "</div>";
							}else if ($value["value"] == "display_tags") {
								echo "<div class='tab-inner-wrap tab-inner-wrap".esc_attr($rand_w)."'><div class='tagcloud'>";
									if ($post_or_question == wpqa_questions_type) {
										$tag_type = array('taxonomy' => wpqa_question_tags);
										$tag_tax = wpqa_question_tags;
									}else if ($post_or_question == wpqa_knowledgebase_type) {
										$tag_type = array('taxonomy' => wpqa_knowledgebase_tags);
										$tag_tax = wpqa_knowledgebase_tags;
									}else {
										$tag_type = array();
										$tag_tax = "post_tag";
									}
									$args = array_merge(array('smallest' => 8,'largest' => 22,'unit' => 'pt','number' => 0,'topic_count_text_callback' => 'wpqa_'.$tag_tax.'_callback'),$tag_type);
									wp_tag_cloud($args);
								echo "</div></div>";
							}
						}
					}?>
					<script type='text/javascript'>
						jQuery(document).ready(function(){
							jQuery("ul.tabs<?php echo esc_js($rand_w);?>").tabs(".tab-inner-wrap<?php echo esc_js($rand_w)?>",{tabs: "li",effect:"slide",fadeInSpeed:100});
						});
					</script>
				</div>
			</div>
			<?php
		}
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>