<?php

/* @author    2codeThemes
*  @package   WPQA/widgets
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Questions Categories */
add_action( 'widgets_init', 'wpqa_widget_questions_categories_widget' );
function wpqa_widget_questions_categories_widget() {
	register_widget( 'Widget_Questions_Categories' );
}

function wpqa_term_post_count($taxonomy = 'category',$term = '',$args = array()) {
	$exclude = apply_filters('wpqa_exclude_question_category',array());
	$cat = get_terms(array_merge($exclude,array('taxonomy' => $taxonomy,'term_taxonomy_id' => $term,'hide_empty' => true)));
	$count = (isset($cat[0]->count)?(int)$cat[0]->count:0);
	if ($term != '' && $term > 0) {
		$args = array('child_of' => $term);
		$args = array_merge($exclude,$args);
		$tax_terms = get_terms($taxonomy,$args);
		if (is_array($tax_terms) && !empty($tax_terms)) {
			//$count = 0;
			foreach ($tax_terms as $tax_term) {
				//$count +=$tax_term->count;
			}
		}
	}
	return $count;
}
function wpqa_hierarchical_category($category = 0,$questions_counts = '',$taxonomy = 'category') {
	$r = '';
	$exclude = apply_filters('wpqa_exclude_question_category',array());
	$args = array(
		'parent' => $category,
		'hide_empty' => false,
	);
	$next = get_terms($taxonomy,array_merge($exclude,$args));
	if ($next) {
		$levels = 0;
		$r .= '<li class="categories-child-child">
			<ul>';
				foreach ($next as $cat) {
					//$count = wpqa_term_post_count($taxonomy,$cat->cat_ID,array('post_type' => wpqa_questions_type));
					$count = $cat->count;
					$levels ++;
					$r .= '<li class="categories-main-child categories-child-'.$levels.'"><a href="'.get_term_link($cat).'"'.'>'.$cat->name;
					if ($questions_counts == "on") {
						$r .= '<span class="question-category-main"> <span>(</span> <span class="question-category-span">'.esc_html($count).'</span> <span>'._n("Question","Questions",$count,"wpqa").'</span> <span>)</span> </span>';
					}
					$r .= '</a>';
					$r .= $cat->term_id !== 0?wpqa_hierarchical_category($cat->term_id,$questions_counts,$taxonomy):null;
				}
				$r .= '</li>
			</ul>
		</li>';
	}
	return $r;
}

class Widget_Questions_Categories extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'questions_categories-widget' );
		$control_ops = array( 'id_base' => 'questions_categories-widget' );
		parent::__construct( 'questions_categories-widget',wpqa_widgets.' - Questions Categories', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title            = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$questions_counts = (isset($instance['questions_counts'])?esc_html($instance['questions_counts']):'');
		$show_child       = (isset($instance['show_child'])?esc_html($instance['show_child']):'');
		$category_type    = (isset($instance['category_type'])?esc_html($instance['category_type']):"");
		$cat_sort         = (isset($instance['cat_sort'])?esc_html($instance['cat_sort']):"count");
		$cat_number       = (int)(isset($instance['cat_number'])?$instance['cat_number']:"");
		$cats_tax         = wpqa_question_categories;
		$user_id          = get_current_user_id();
		$cat_style        = $category_type;
		$widget           = true;
		echo ($before_widget);
			if ($title) {
				echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
			}else {
				echo "<h3 class='screen-reader-text'>".esc_html__("Questions Categories","wpqa")."</h3>";
			}?>
			<div class="widget-wrap">
				<?php $rand_w = (isset($args['widget_id'])?$args['widget_id']:rand(1,1000));
				if ($category_type == "simple" || $category_type == "simple_follow" || $category_type == "with_icon" || $category_type == "icon_color" || $category_type == "with_icon_1" || $category_type == "with_icon_2" || $category_type == "with_icon_3" || $category_type == "with_icon_4" || $category_type == "with_cover_1" || $category_type == "with_cover_2" || $category_type == "with_cover_3") {
					$follow_category = wpqa_options("follow_category");
					$cat_sort = ($cat_sort == "followers"?"meta_value_num":$cat_sort);
					$meta_query = ($cat_sort == "meta_value_num"?array('meta_query' => array("relation" => "or",array("key" => "cat_follow_count","compare" => "NOT EXISTS"),array("key" => "cat_follow_count","value" => 0,"compare" => ">="))):array());
					$exclude = apply_filters('wpqa_exclude_question_category',array());
					$args = array_merge($exclude,$meta_query,array(
						'order'      => "DESC",
						'orderby'    => $cat_sort,
						'number'     => $cat_number,
						'hide_empty' => false
					));
					$args = apply_filters('wpqa_question_category_widget_args',$args);
					$terms = get_terms($cats_tax,$args);
					if (!empty($terms) && !is_wp_error($terms)) {
						$term_list = '<div class="row row-boot row-warp widget-cats-sections cat_widget_'.$category_type.'">';
							foreach ($terms as $term) {
								$tax_id = $term->term_id;
								$category_icon = get_term_meta($tax_id,prefix_terms."category_icon",true);
								if ($follow_category == "on") {
									$cat_follow = get_term_meta($tax_id,"cat_follow",true);
								}
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
								<?php $exclude = apply_filters('wpqa_exclude_question_category',array());
								$args = array_merge($exclude,array(
								'parent'       => ($show_child == "on"?0:""),
								'orderby'      => 'name',
								'order'        => 'ASC',
								'hide_empty'   => false,
								'hierarchical' => 1,
								'taxonomy'     => $cats_tax,
								'pad_counts'   => false,
								'number'       => $cat_number));
								$args = apply_filters('wpqa_question_category_widget_args',$args);
								$options_categories = get_categories($args);
								foreach ($options_categories as $category) {
									//$count = wpqa_term_post_count($cats_tax,$category->cat_ID,array('post_type' => wpqa_questions_type));
									$count = $category->count;
									if ($show_child == "on") {
										$exclude = apply_filters('wpqa_exclude_question_category',array());
										$children = get_terms($cats_tax,array_merge($exclude,array('parent' => $category->cat_ID,'hide_empty' => false)));
									}?>
									<li>
										<?php if ($show_child == "on" && isset($children) && is_array($children) && !empty($children)) {?>
											<div class="accordion-content accordion-item">
												<h4 class="accordion-title accordion__header accordion__title collapsed" data-toggle="collapse" data-target="#collapse<?php echo esc_attr("cat_".$rand_w."_".$category->cat_ID)?>" aria-expanded="true">
										<?php }?>
											<a<?php echo ($show_child == "on" && isset($children) && is_array($children) && !empty($children)?' target="_blank"':'').($show_child == "on"?' class="'.(isset($children) && is_array($children) && !empty($children)?"link-child":"link-not-child").'"':'')?> href="<?php echo get_term_link($category)?>"><?php echo esc_html($category->name);
												if ($questions_counts == "on") {?>
													<span class="question-category-main"> <span>(</span> <span class="question-category-span"><?php echo esc_html($count)."</span> <span>"._n("Question","Questions",$count,"wpqa")?></span> <span>)</span> </span>
												<?php }
												if ($show_child == "on" && isset($children) && is_array($children) && !empty($children)) {?>
													<i class="icon-plus"></i>
												<?php }?>
											</a>
										<?php if ($show_child == "on" && isset($children) && is_array($children) && !empty($children)) {?>
											</h4>
										<?php }
										if ($show_child == "on" && isset($children) && is_array($children) && !empty($children)) {?>
												<div class="accordion-inner collapse" id="collapse<?php echo esc_attr("cat_".$rand_w."_".$category->cat_ID)?>" data-parent=".accordion">
													<ul>
														<?php echo wpqa_hierarchical_category($category->cat_ID,$questions_counts,$cats_tax)?>
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