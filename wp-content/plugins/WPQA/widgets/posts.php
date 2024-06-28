<?php

/* @author    2codeThemes
*  @package   WPQA/widgets
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Posts */
add_action( 'widgets_init', 'wpqa_widget_posts_widget' );
function wpqa_widget_posts_widget() {
	register_widget( 'Widget_posts' );
}
class Widget_posts extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget-posts' );
		$control_ops = array( 'id_base' => 'widget_posts' );
		parent::__construct( 'widget_posts',wpqa_widgets.' - Posts', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title                    = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$orderby                  = (isset($instance['orderby'])?esc_html($instance['orderby']):'');
		$posts_per_page           = (int)(isset($instance['posts_per_page'])?$instance['posts_per_page']:5);
		$excerpt_title            = (isset($instance['excerpt_title'])?esc_html($instance['excerpt_title']):'');
		
		$post_or_question         = (isset($instance['post_or_question'])?esc_html($instance['post_or_question']):'');
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
		$blog_h_button            = (isset($instance['blog_h_button'])?$instance['blog_h_button']:"");
		$blog_h_button_text       = (isset($instance['blog_h_button_text'])?$instance['blog_h_button_text']:"");
		$blog_h_page              = (isset($instance['blog_h_page'])?$instance['blog_h_page']:"");
		$blog_h_link              = (isset($instance['blog_h_link'])?$instance['blog_h_link']:"");
		$post_style               = (isset($instance['post_style'])?$instance['post_style']:"");
		$display_comment          = (isset($instance['display_comment'])?$instance['display_comment']:"");
		$show_images              = (isset($instance['show_images_post'])?$instance['show_images_post']:"");
		$display_image            = (isset($instance['display_image'])?$instance['display_image']:"");
		$display_video            = (isset($instance['display_video'])?$instance['display_video']:"");
		$display_date             = (isset($instance['display_date'])?$instance['display_date']:"");
		$specific_date            = (isset($instance['specific_date_post'])?$instance['specific_date_post']:"");
		$excerpt_post             = (int)(isset($instance['excerpt_post'])?$instance['excerpt_post']:40);

		if ($post_or_question == wpqa_questions_type) {
			$post_style      = (isset($instance['question_style'])?$instance['question_style']:"");
			$show_images     = (isset($instance['show_images'])?$instance['show_images']:"");
			$display_comment = (isset($instance['display_answer'])?$instance['display_answer']:"");
			$display_image   = (isset($instance['display_image_question'])?$instance['display_image_question']:"");
			$display_video   = (isset($instance['display_video_question'])?$instance['display_video_question']:"");
			$display_date    = (isset($instance['display_date_question'])?$instance['display_date_question']:"");
			$specific_date   = (isset($instance['specific_date'])?$instance['specific_date']:"");
		}else if ($post_or_question == wpqa_knowledgebase_type) {
			$specific_date = (isset($instance['specific_date_knowledgebase'])?$instance['specific_date_knowledgebase']:"");
		}
		
		echo ($before_widget);
			if ($title) {
				echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
			}else {
				echo "<h3 class='screen-reader-text'>".esc_html__("Posts","wpqa")."</h3>";
			}?>
			<div class="widget-wrap">
				<?php $args = array(
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
					"display_image"            => $display_image,
					"display_video"            => $display_video,
					"display_date"             => $display_date,
					"blog_h_button"            => $blog_h_button,
					"blog_h_button_text"       => $blog_h_button_text,
					"blog_h_page"              => $blog_h_page,
					"blog_h_link"              => $blog_h_link,
					"post_style"               => $post_style,
					"excerpt_post"             => $excerpt_post,
					"specific_date"            => $specific_date
				);
				echo wpqa_posts($args);?>
			</div>
		<?php echo ($after_widget);
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>