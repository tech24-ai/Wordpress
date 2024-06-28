<?php

/* @author    2codeThemes
*  @package   WPQA/widgets
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Knowledgebases Categories */
add_action( 'widgets_init', 'wpqa_widget_knowledgebases_categories_widget' );
function wpqa_widget_knowledgebases_categories_widget() {
	$activate_knowledgebase = apply_filters("wpqa_activate_knowledgebase",false);
	if ($activate_knowledgebase == true) {
		register_widget( 'Widget_Knowledgebases_Categories' );
	}
}

class Widget_Knowledgebases_Categories extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'knowledgebases_categories-widget' );
		$control_ops = array( 'id_base' => 'knowledgebases_categories-widget' );
		parent::__construct( 'knowledgebases_categories-widget',wpqa_widgets.' - Knowledgebases Categories', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title                 = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$knowledgebases_counts = (isset($instance['knowledgebases_counts'])?esc_html($instance['knowledgebases_counts']):'');
		$show_child            = (isset($instance['show_child'])?esc_html($instance['show_child']):'');
		$category_type         = (isset($instance['category_type'])?esc_html($instance['category_type']):"");
		$cat_sort              = (isset($instance['cat_sort'])?esc_html($instance['cat_sort']):"count");
		$cat_number            = (int)(isset($instance['cat_number'])?$instance['cat_number']:"");
		$cats_tax              = wpqa_knowledgebase_categories;
		$user_id               = get_current_user_id();
		$cat_style             = $category_type;
		$widget                = true;
		echo ($before_widget);
			if ($title) {
				echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
			}else {
				echo "<h3 class='screen-reader-text'>".esc_html__("Knowledgebases Categories","wpqa")."</h3>";
			}?>
			<div class="widget-wrap">
				<?php $rand_w = (isset($args['widget_id'])?$args['widget_id']:rand(1,1000));
				if ($category_type == "simple" || $category_type == "with_icon" || $category_type == "icon_color" || $category_type == "with_icon_1" || $category_type == "with_icon_2" || $category_type == "with_icon_3" || $category_type == "with_icon_4" || $category_type == "with_cover_1" || $category_type == "with_cover_2" || $category_type == "with_cover_3") {
					$exclude = apply_filters('wpqa_exclude_knowledgebase_category',array());
					$args = array_merge($exclude,array(
						'order'      => "DESC",
						'orderby'    => $cat_sort,
						'number'     => $cat_number,
						'hide_empty' => false
					));
					$args = apply_filters('wpqa_knowledgebase_category_widget_args',$args);
					$terms = get_terms($cats_tax,$args);
					if (!empty($terms) && !is_wp_error($terms)) {
						$term_list = '<div class="row row-boot row-warp widget-cats-sections cat_widget_'.$category_type.'">';
							foreach ($terms as $term) {
								$tax_id = $term->term_id;
								$category_icon = get_term_meta($tax_id,prefix_terms."category_icon",true);
								$tax_col = "col12 col-boot-sm-12";
								include locate_template("theme-parts/show-categories.php");
							}
						echo ($term_list);
					}
				}else {?>
					<div class="widget_questions_categories">
						<?php if ($show_child == "on") {?>
							<div class="widget_child_categories">
								<div class="categories-toggle-accordion accordion toggle-accordion">
						<?php }?>
							<ul>
								<?php $exclude = apply_filters('wpqa_exclude_knowledgebase_category',array());
								$args = array_merge($exclude,array(
								'parent'       => ($show_child == "on"?0:""),
								'orderby'      => 'name',
								'order'        => 'ASC',
								'hide_empty'   => false,
								'hierarchical' => 1,
								'taxonomy'     => $cats_tax,
								'pad_counts'   => false,
								'number'       => $cat_number));
								$args = apply_filters('wpqa_knowledgebase_category_widget_args',$args);
								$options_categories = get_categories($args);
								foreach ($options_categories as $category) {
									//$count = wpqa_term_post_count($cats_tax,$category->cat_ID,array('post_type' => wpqa_knowledgebases_type));
									$count = $category->count;
									if ($show_child == "on") {
										$exclude = apply_filters('wpqa_exclude_knowledgebase_category',array());
										$children = get_terms($cats_tax,array_merge($exclude,array('parent' => $category->cat_ID,'hide_empty' => false)));
									}?>
									<li>
										<?php if ($show_child == "on" && isset($children) && is_array($children) && !empty($children)) {?>
											<div class="accordion-content accordion-item">
												<h4 class="accordion-title accordion__header accordion__title collapsed" data-toggle="collapse" data-target="#collapse<?php echo esc_attr("cat_".$rand_w."_".$category->cat_ID)?>" aria-expanded="true">
										<?php }?>
											<a<?php echo ($show_child == "on" && isset($children) && is_array($children) && !empty($children)?' target="_blank"':'').($show_child == "on"?' class="'.(isset($children) && is_array($children) && !empty($children)?"link-child":"link-not-child").'"':'')?> href="<?php echo get_term_link($category)?>"><?php echo esc_html($category->name);
												if ($knowledgebases_counts == "on") {?>
													<span class="question-category-main"> <span>(</span> <span class="question-category-span"><?php echo esc_html($count)."</span> <span>"._n("Article","Articles",$count,"wpqa")?></span> <span>)</span> </span>
												<?php }if ($show_child == "on" && isset($children) && is_array($children) && !empty($children)) {?>
													<i class="icon-plus"></i>
												<?php }?>
											</a>
										<?php if ($show_child == "on" && isset($children) && is_array($children) && !empty($children)) {?>
											</h4>
										<?php }
										if ($show_child == "on" && isset($children) && is_array($children) && !empty($children)) {?>
												<div class="accordion-inner collapse" id="collapse<?php echo esc_attr("cat_".$rand_w."_".$category->cat_ID)?>" data-parent=".accordion">
													<ul>
														<?php echo wpqa_hierarchical_category($category->cat_ID,$knowledgebases_counts,$cats_tax)?>
													</ul>
												</div>
											</div>
										<?php }?>	
									</li>
								<?php }?>
							</ul>
						<?php if ($show_child == "on") {?>
								</div>
							</div>
						<?php }?>
					</div>
				<?php }?>
			</div>
		<?php echo ($after_widget);
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>