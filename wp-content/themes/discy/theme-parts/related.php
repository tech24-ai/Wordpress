<?php if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) {
	$related_number        = $related_number_question;
	$excerpt_related_title = $related_title_question;
	$query_related         = $query_related_question;
}
$excerpt_related_title = isset($excerpt_related_title) ? $excerpt_related_title : 10;
$related_number        = $related_number ? $related_number : 4;
if (is_singular("post")) {
	$related_number_sidebar = isset($related_number_sidebar) ? $related_number_sidebar : 6;
	$related_number_sidebar = isset($related_style) && $related_style == "links"?$related_number:$related_number_sidebar;
	$related_number_full    = isset($related_number_full) && $related_number_full ? $related_number_full : 8;
	$related_number_full    = isset($related_style) && $related_style == "links"?$related_number:$related_number_full;
}

$get_question_user_id = get_post_meta($post->ID,"user_id",true);
if (isset($query_related) && $query_related == "tags" && esc_html($get_question_user_id) == "") {
	if (is_singular(wpqa_questions_type)) {
		$term_list = wp_get_post_terms($post->ID, wpqa_question_tags, array('fields' => 'ids'));
		$related_query_ = array('tax_query' => array(array('taxonomy' => wpqa_question_tags,'field' => 'id','terms' => $term_list,'operator' => 'IN')));
	}else {
		$term_list = wp_get_post_terms($post->ID, 'post_tag', array("fields" => "ids"));
		$related_query_ = array('tag__in' => $term_list);
	}
}else if (isset($query_related) && $query_related == "author" || esc_html($get_question_user_id) != "") {
	$related_query_ = (esc_html($get_question_user_id) != ""?array():array('author' => $post->post_author));
}else {
	if (is_singular(wpqa_questions_type)) {
		$categories = wp_get_post_terms($post->ID,wpqa_question_categories,array('fields' => 'ids'));
		$related_query_ = array('tax_query' => array(array('taxonomy' => wpqa_question_categories,'field' => 'id','terms' => $categories,'operator' => 'IN')));
	}else {
		$categories = get_the_category($post->ID);
		$category_ids = array();
		foreach ($categories as $l_category) {
			$category_ids[] = $l_category->term_id;
		}
		$related_query_ = array('category__in' => $category_ids);
	}
}

if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || (isset($related_style) && $related_style == "links")) {
	$args_images = array();
}else {
	$args_images = array('meta_key' => '_thumbnail_id');
}

if (is_single()) {
	if ($theme_sidebar == "centered") {
		$post_width = 269;
		$post_height = 180;
		$related_post_columns = "col6";
	}else if ($theme_sidebar == "menu_sidebar") {
		$post_width = 300;
		$post_height = 180;
		$related_post_columns = "col6";
	}else if ($theme_sidebar == "menu_left") {
		$post_width = 283;
		$post_height = 165;
		$related_post_columns = "col4";
		if (is_singular("post")) {
			$related_number = $related_number_sidebar;
		}
	}else if ($theme_sidebar == "full") {
		$post_width = 255;
		$post_height = 150;
		$related_post_columns = "col3";
		if (is_singular("post")) {
			$related_number = $related_number_full;
		}
	}else {
		$post_width = 256;
		$post_height = 150;
		$related_post_columns = "col4";
		if (is_singular("post")) {
			$related_number = $related_number_sidebar;
		}
	}
}

$show_defult_image = apply_filters('discy_show_defult_image_post',true);
if ($show_defult_image != true) {
	$post_width = "";
	$post_height = "";
}

$block_users = discy_options("block_users");
$author__not_in = array();
if ($block_users == "on") {
	$user_id = get_current_user_id();
	if ($user_id > 0) {
		$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
		if (is_array($get_block_users) && !empty($get_block_users)) {
			$author__not_in = array("author__not_in" => $get_block_users);
		}
	}
}

$args = array_merge($args_images,$related_query_,$author__not_in,array('post_type' => $post->post_type,'post__not_in' => array($post->ID),'posts_per_page'=> $related_number,'cache_results' => false,'no_found_rows' => true,"meta_query" => array((esc_html($get_question_user_id) != ""?array("type" => "numeric","key" => "user_id","value" => (int)$get_question_user_id,"compare" => "="):array()))));
$related_query = new WP_Query($args);

if (isset($query_related) && ($query_related == "tags" || $query_related == "author") && !$related_query->have_posts()) {
	if (is_singular(wpqa_questions_type)) {
		$categories = wp_get_post_terms($post->ID,wpqa_question_categories,array('fields' => 'ids'));
		$related_query_ = array('tax_query' => array(array('taxonomy' => wpqa_question_categories,'field' => 'id','terms' => $categories,'operator' => 'IN')));
	}else {
		$categories = get_the_category($post->ID);
		$category_ids = array();
		foreach ($categories as $l_category) {
			$category_ids[] = $l_category->term_id;
		}
		$related_query_ = array('category__in' => $category_ids);
	}
	$args = array_merge($args_images,$related_query_,$author__not_in,array('post_type' => $post->post_type,'post__not_in' => array($post->ID),'posts_per_page'=> $related_number,'cache_results' => false,'no_found_rows' => true,"meta_query" => array((esc_html($get_question_user_id) != ""?array("type" => "numeric","key" => "user_id","value" => (int)$get_question_user_id,"compare" => "="):array()))));
	$related_query = new WP_Query($args);
}

if ($related_query->have_posts()) {
	if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type) || (isset($query_related) && $related_style == "links")) {
		$related_style_links = true;
	}?>
	<div class="related-post<?php echo (isset($related_style_links)?" related-post-links":"").(is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)?" related-questions":"")?>">
		<div class="post-inner">
			<h3 class="section-title"><?php echo (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)?esc_html__('Related Questions',"discy"):esc_html__('Related Posts',"discy"))?></h3>
			<?php if (isset($related_style_links)) {
				echo '<ul>';
			}else {
				echo '<div class="row row-warp">';
			}
				while ( $related_query->have_posts() ) : $related_query->the_post();
					if ((isset($related_style_links)) && $excerpt_related_title > 0) {?>
						<li>
							<a<?php echo apply_filters("discy_post_link_target","")?> href="<?php the_permalink();?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>" rel="bookmark"><i class="icon-right-thin"></i><?php discy_excerpt_title($excerpt_related_title)?></a>
						</li>
					<?php }else {?>
						<div class="col <?php echo esc_attr($related_post_columns)?>">
							<div <?php post_class('clearfix');?>>
								<div class="related-image">
									<a<?php echo apply_filters("discy_post_link_target","")?> href="<?php echo esc_url( get_permalink() )?>">
										<?php echo (isset($post_width) && isset($post_height)?discy_get_aq_resize_img($post_width,$post_height):"");?>
									</a>
								</div>
								<?php if ($date_in_related == "on" || $comment_in_related == "on") {?>
									<div class="post-meta clearfix">
										<?php discy_meta($date_in_related,"",$comment_in_related)?>
									</div>
								<?php }?>
								<h2 class="post-title"><a class="post-title"<?php echo apply_filters("discy_post_link_target","")?> href="<?php echo esc_url( get_permalink() )?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>" rel="bookmark"><?php discy_excerpt_title($excerpt_related_title)?></a></h2>
							</div>
						</div>
					<?php }
				endwhile;
			if (isset($related_style_links)) {
				echo '</ul>';
			}else {
				echo '</div>';
			}?>
			<div class="clearfix"></div>
		</div>
	</div><!-- End related-post -->
<?php }
wp_reset_postdata();?>