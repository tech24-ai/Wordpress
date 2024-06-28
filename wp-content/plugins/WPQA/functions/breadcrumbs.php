<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Breadcrumbs */
if (!function_exists('wpqa_breadcrumbs')) :
	function wpqa_breadcrumbs($text = "",$breadcrumb_right = true,$breadcrumbs_style = "style_1") {
		global $post,$wp_query;
		$active_points         = wpqa_options("active_points");
		$breadcrumbs_separator = wpqa_options("breadcrumbs_separator");
		$breadcrumbs_separator = ($breadcrumbs_separator != ""?$breadcrumbs_separator:"/");
		$breadcrumbs_skin      = wpqa_options("breadcrumbs_skin");
		$active_cover_category = wpqa_options("active_cover_category");
		$post_type             = get_post_type();
		$home                  = '<i class="icon-home font-xl mr-2"></i>'.esc_html__('Home','wpqa');
		$before_schema         = '<span class="crumbs-span">'.$breadcrumbs_separator.'</span>';
		$user_id               = get_current_user_id();
		$wpqa_get_the_title    = wpqa_get_the_title();
		if ($breadcrumbs_skin == "dark") {
			$breadcrumbs_class = "breadcrumbs-dark";
		}else if ($breadcrumbs_skin == "colored") {
			$breadcrumbs_class = "breadcrumbs-colored background-color";
		}else {
			$breadcrumbs_class = "breadcrumbs-light";
		}
		echo '<div class="breadcrumbs d-flex align-items-center justify-content-between w-100 mb-4 '.($breadcrumbs_style == "style_2"?"breadcrumbs_2 ".$breadcrumbs_class:"breadcrumbs_1").'">';
			$before = '<h1>';
			$after = '</h1>';
			if ($breadcrumbs_style == "style_2") {
				echo '<div class="the-main-container container">';
			}
				echo '<div class="breadcrumbs-wrap d-flex align-items-center justify-content-between w-100">
					<div class="breadcrumb-left">';
						if ($breadcrumbs_style == "style_2") {
							$text_filter = apply_filters("wpqa_breadcrumbs_text",false);
							if (isset($text) && $text != "") {
								echo ($before . $text . $after);
							}else if ($text_filter != "") {
								echo ($before . $text_filter . $after);
							}else if (wpqa_is_add_questions()) {
								$wpqa_add_question_user = wpqa_add_question_user();
								if ($wpqa_add_question_user > 0 && $user_id != $wpqa_add_question_user) {
									$display_name = get_the_author_meta('display_name',$wpqa_add_question_user);
								}
								echo ($before . esc_html__('Ask question', 'wpqa') . ($wpqa_add_question_user > 0 && $user_id != $wpqa_add_question_user?" ".esc_html__("to","wpqa")." ".$display_name:"") . $after);
							}else if (wpqa_is_user_profile() && wpqa_is_home_profile()) {
								$wpqa_user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
								$user_name = get_the_author_meta("display_name",$wpqa_user_id);
								echo ($before.$user_name.$after);
							}else if ($wpqa_get_the_title != "") {
								echo ($before.$wpqa_get_the_title.$after);
							}else if (is_category() || is_tag() || is_tax()) {
								echo ($before.single_cat_title('',false).$after);
							}else if (is_day()) {
								echo ($before . get_the_time('d') . $after);
							}else if (is_month()) {
								echo ($before . get_the_time('F') . $after);
							}else if (is_year()) {
								echo ($before . get_the_time('Y') . $after);
							}else if (!is_single() && !is_page() && $post_type != 'post') {
								$post_type_object = get_post_type_object($post_type);
								echo ($before . (isset($post_type_object->labels->singular_name) && !is_404()?$post_type_object->labels->singular_name:esc_html__("Error 404","wpqa")) . $after);
							}else if (is_attachment() || (is_single() && !is_attachment()) || (is_page() && !$post->post_parent) || (is_page() && $post->post_parent)) {
								echo ($before . get_the_title() . $after);
							}else if (is_tag()) {
								echo ($before . esc_html__('Posts tagged ', 'wpqa') . '"' . single_tag_title('', false) . '"' . $after);
							}else if (is_404()) {
								echo ($before . esc_html__('Error 404 ', 'wpqa') . $after);
							}
						}
						$before = $before_schema.'<span class="current">';
						$after  = '</span>';
						echo '<span class="crumbs">
							<span class="breadcrumb-item" itemscope itemtype="https://schema.org/BreadcrumbList">
								'.wpqa_breadcrumbs_schema(esc_url(home_url('/')),$home,1);
								$text_filter = apply_filters("wpqa_breadcrumbs_text",false);
								if (isset($text) && $text != "") {
									echo ($before . $text . $after);
								}else if ($text_filter != "") {
									echo ($before . $text_filter . $after);
								}else if (wpqa_is_add_questions()) {
									$wpqa_add_question_user = wpqa_add_question_user();
									if ($wpqa_add_question_user > 0 && $user_id != $wpqa_add_question_user) {
										$display_name = get_the_author_meta('display_name',$wpqa_add_question_user);
									}
									echo ($before . esc_html__('Ask question', 'wpqa') . ($wpqa_add_question_user > 0 && $user_id != $wpqa_add_question_user?" ".esc_html__("to","wpqa")." ".$display_name:"") . $after);
								}else if (wpqa_is_user_profile()) {
									$wpqa_user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
									if ($wpqa_user_id > 0) {
										if (wpqa_is_main_edit_profile_pages()) {
											$show_profile_link = true;
										}
										$user_name = get_the_author_meta("display_name",$wpqa_user_id);
										echo ($before_schema.(wpqa_is_home_profile() && !isset($show_profile_link)?$user_name:wpqa_breadcrumbs_schema(wpqa_profile_url($wpqa_user_id),$user_name,2,"yes")));
									}
									if (wpqa_user_title() && (!wpqa_is_main_edit_profile_pages() || (wpqa_is_main_edit_profile_pages() && is_user_logged_in()))) {
										echo ($before.wpqa_profile_title().$after);
									}
								}else if ($wpqa_get_the_title != "") {
									if (wpqa_is_view_posts_group() || wpqa_is_edit_posts_group()) {
										if (wpqa_is_view_posts_group()) {
											$post_id = (int)get_query_var(apply_filters('wpqa_view_posts_group','view_post_group'));
										}else {
											$post_id = (int)get_query_var(apply_filters('wpqa_edit_posts_group','edit_post_group'));
										}
										$group_id = (int)get_post_meta($post_id,"group_id",true);
										echo ($group_id > 0?$before . wpqa_breadcrumbs_schema(esc_url(get_permalink($group_id)),get_the_title($group_id),2,'yes') . $after:"");
									}
									echo ($before.$wpqa_get_the_title.$after);
								}else if (is_category() || is_tag() || is_tax()) {
									$term = $wp_query->get_queried_object();
									$taxonomy = get_taxonomy( $term->taxonomy );
									if ( isset($item) && is_array($item) && ( is_taxonomy_hierarchical( $term->taxonomy ) && $term->parent ) && $parents = wpqa_breadcrumbs_get_term_parents( $term->parent, $term->taxonomy ) )
										$item = array_merge( $item, $parents );
									$item['last'] = $term->name;
									if (isset($term->term_id)) {
										echo wpqa_get_taxonomy_parents($term->term_id,$taxonomy->name,true,$term->term_id,array(),$before_schema);
									}
									echo ($before.single_cat_title('',false).$after);
								}else if (is_day()) {
									echo ($before_schema.wpqa_breadcrumbs_schema(esc_url(get_year_link(get_the_time('Y'))),get_the_time('Y'),2,"yes")).
									($before_schema.wpqa_breadcrumbs_schema(esc_url(get_month_link(get_the_time('Y'),get_the_time('m'))),get_the_time('F'),3,"yes")).
									($before . get_the_time('d') . $after);
								}else if (is_month()) {
									echo ($before_schema.wpqa_breadcrumbs_schema(esc_url(get_year_link(get_the_time('Y'))),get_the_time('Y'),2,"yes")).
									($before . get_the_time('F') . $after);
								}else if (is_year()) {
									echo ($before . get_the_time('Y') . $after);
								}else if (is_single() && !is_attachment()) {
									if ($post_type != 'post') {
										if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
											echo ($before_schema.wpqa_breadcrumbs_schema(get_post_type_archive_link($post_type),esc_html__("Questions","wpqa"),2,'yes')).
											($before . esc_html__("Q","wpqa")." ". $post->ID . $after);
										}else if ($post_type == wpqa_knowledgebase_type) {
											echo ($before_schema.wpqa_breadcrumbs_schema(get_post_type_archive_link($post_type),esc_html__("Knowledge Base","wpqa"),2,'yes'));
											$get_term = wp_get_object_terms($post->ID,wpqa_knowledgebase_categories);
											$get_term = (isset($get_term[0])?$get_term[0]:"");
											if (is_object($get_term)) {
												echo ($before_schema.wpqa_breadcrumbs_schema(esc_url(get_term_link($get_term)),$get_term->name,0,"yes"));
											}
											echo ($before . get_the_title($post->ID) . $after);
										}else {
											$post_type_object = get_post_type_object($post_type);
								        	echo ($before_schema.wpqa_breadcrumbs_schema(esc_url(get_post_type_archive_link($post_type_object->name)),$post_type_object->labels->singular_name,2,'yes')).
											($before.get_the_title($post->ID).$after);
										}
									}else {
										$cat = get_the_category();
										if (isset($cat) && is_array($cat) && isset($cat[0])) {
											$term_id = $cat[0];
											$taxonomy = 'category';
											$list = '';
											$term = get_term( $term_id, $taxonomy );
											if ( is_wp_error( $term ) ) {
												return $term;
											}
											if ( ! $term ) {
												return $list;
											}
											$term_id = $term->term_id;
											$parents = get_ancestors( $term_id, $taxonomy, 'taxonomy' );
											array_unshift( $parents, $term_id );
											$counter = 1;
											foreach ( array_reverse( $parents ) as $term_id ) {
												$counter++;
												$parent = get_term( $term_id, $taxonomy );
												$name   = $parent->name;
												$list  .= $before_schema.wpqa_breadcrumbs_schema(esc_url(get_term_link($parent)),$name,$counter,"yes");
											}
											echo ($list);
										}
										echo ($before.get_the_title().$after);
									}
								}else if (!is_single() && !is_page() && $post_type != 'post') {
									$post_type_object = get_post_type_object($post_type);
									echo ($before . (isset($post_type_object->labels->singular_name) && !is_404()?$post_type_object->labels->singular_name:esc_html__("Error 404","wpqa")) . $after);
								}else if (is_attachment()) {
									$parent = get_post($post->post_parent);
									$cat = get_the_category($parent->ID);
									echo ($before . get_the_title() . $after);
								}else if (is_page() && !$post->post_parent) {
									echo ($before . get_the_title() . $after);
								}else if (is_page() && $post->post_parent) {
									$parent_id  = $post->post_parent;
									$breadcrumbs = array();
									$counter = 1;
									while ($parent_id) {
										$counter++;
										$page = get_page($parent_id);
										$breadcrumbs[] = wpqa_breadcrumbs_schema(esc_url(get_permalink($page->ID)),get_the_title($page->ID),$counter,'yes');
										$parent_id  = $page->post_parent;
									}
									$breadcrumbs = array_reverse($breadcrumbs);
									foreach ($breadcrumbs as $crumb) echo ($before_schema.$crumb);
									echo ($before . get_the_title() . $after);
								}else if (is_tag()) {
									echo ($before . esc_html__('Posts tagged ', 'wpqa') . '"' . single_tag_title('', false) . '"' . $after);
								}else if (is_404()) {
									echo ($before . esc_html__('Error 404 ', 'wpqa') . $after);
								}
								do_action("wpqa_filter_breadcrumb",$before,$after);
								if (get_query_var('paged')) {
									echo ($before . esc_html__('Page', 'wpqa') . ' ' . esc_html(get_query_var('paged')) . $after);
								}
							echo '</span>
						</span>';
					echo '</div><!-- End breadcrumb-left -->';
					if ($breadcrumb_right == true) {
						$live_search = wpqa_options('live_search');
						$category_filter = wpqa_options('category_filter');
						echo '<div class="breadcrumb-right d-flex align-items-center">';
							$tax_archive = apply_filters('wpqa_tax_archive',false);
							$tax_filter = apply_filters("wpqa_before_question_category",false);
							$tax_question = apply_filters("wpqa_question_category",wpqa_question_categories);
							$tax_knowledgebase = apply_filters("wpqa_knowledgebase_category",wpqa_knowledgebase_categories);
							if (wpqa_is_user_profile()) {
								if (wpqa_is_user_owner()) {
									if (!wpqa_is_user_edit_home()) {?>
										<div class="question-navigation edit-profile"><a class="btn btn__sm btn__primary" href="<?php echo esc_url(wpqa_edit_profile_permalink())?>"><i class="icon-pencil"></i><?php esc_html_e("Edit profile","wpqa")?></a></div>
									<?php }
								}else {
									$ask_question_to_users = wpqa_options("ask_question_to_users");
									if ($ask_question_to_users == "on") {
										$display_name = get_the_author_meta("display_name",$wpqa_user_id);?>
										<div class="ask-question"><a href="<?php echo esc_url(wpqa_add_question_permalink("user"))?>" class="button-default ask-question-user btn btn__sm btn__primary"><?php echo esc_html__("Ask","wpqa")." ".$display_name?></a></div>
									<?php }
								}
							}else if (!is_tag() && !is_tax(wpqa_question_tags) && !is_tax(wpqa_question_tags) && ((is_category() || (is_archive() && !is_post_type_archive() && !is_post_type_archive("group")) || is_tax($tax_question) || is_tax($tax_knowledgebase) || $tax_filter == true || $tax_archive == true || is_page_template("template-categories.php") || is_post_type_archive(wpqa_questions_type) || is_post_type_archive(wpqa_knowledgebase_type)) && $category_filter == "on")) {
								if (is_page_template("template-categories.php")) {
									$cat_search = get_post_meta($post->ID,prefix_meta.'cat_search',true);
									$cat_filter = get_post_meta($post->ID,prefix_meta.'cat_filter',true);
								}else {
									$cat_search = wpqa_options("cat_search");
									$cat_filter = "";
								}
								if ($tax_archive != true || $tax_filter == true) {
									$cats_search = (is_tax($tax_question) || $tax_filter == true || is_post_type_archive(wpqa_questions_type)?$tax_question:(isset($wp_query->query_vars) && isset($wp_query->query_vars["taxonomy"])?$wp_query->query_vars["taxonomy"]:(is_post_type_archive(wpqa_knowledgebase_type)?$tax_knowledgebase:"category")));
									$exclude = apply_filters('wpqa_exclude_question_category',array());
									$args = array_merge($exclude,array(
									'child_of'     => 0,
									'parent'       => '',
									'orderby'      => 'name',
									'order'        => 'ASC',
									'hide_empty'   => 1,
									'hierarchical' => 1,
									'exclude'      => '',
									'include'      => '',
									'number'       => '',
									'taxonomy'     => $cats_search,
									'pad_counts'   => false ));
									$options_categories = get_categories($args);
									if ((!is_page_template("template-categories.php") && isset($options_categories) && is_array($options_categories)) || $cat_search == "on" || ($cat_filter == "on" && is_page_template("template-categories.php"))) {
										$post_type = (isset($wp_query->query_vars["post_type"])?$wp_query->query_vars["post_type"]:wpqa_questions_type);?>
										<div class="search-form">
											<?php do_action("wpqa_before_select_filter");
											if ($cat_filter == "on" || is_page_template("template-categories.php")) {
												$cat_sort = get_post_meta($post->ID,prefix_meta.'cat_sort',true);
												$cat_sort = ($cat_sort != ""?$cat_sort:"name");
												$g_cat_filter = (isset($_GET["cat_filter"]) && $_GET["cat_filter"] != ""?esc_html($_GET["cat_filter"]):$cat_sort);
												echo '<form method="get" class="search-filter-form">
													<span class="styled-select cat-filter">
														<select class="form-control" name="cat_filter" onchange="this.form.submit()">
															<option value="count" '.selected($g_cat_filter,"count",false).'>'.esc_html__('Popular','wpqa').'</option>
															<option value="followers" '.selected($g_cat_filter,"followers",false).'>'.esc_html__('Followers','wpqa').'</option>
															<option value="name" '.selected($g_cat_filter,"name",false).'>'.esc_html__('Name','wpqa').'</option>
														</select>
													</span>
												</form>';
											}
											if (!is_page_template("template-categories.php") && isset($options_categories) && is_array($options_categories)) {
												if (is_tax($tax_question) || $tax_filter == true || is_post_type_archive(wpqa_questions_type)) {
													$archive_link = get_post_type_archive_link(wpqa_questions_type);
													$archive_text = esc_html__('All Questions','wpqa');
												}else if (is_tax($tax_knowledgebase) || is_post_type_archive(wpqa_knowledgebase_type)) {
													$archive_link = get_post_type_archive_link(wpqa_knowledgebase_type);
													$archive_text = esc_html__('All Articles','wpqa');
												}else {
													$pages = get_pages(array('meta_key' => '_wp_page_template','meta_value' => 'template-blog.php'));
													$archive_link = (isset($pages) && isset($pages[0]) && isset($pages[0]->ID)?get_permalink($pages[0]->ID):'');
													$archive_text = esc_html__('All Posts','wpqa');
												}?>
												<div class="search-filter-form">
													<span class="styled-select cat-filter">
														<select class="form-control home_categories">
															<option<?php echo (is_post_type_archive($post_type)?' selected="selected"':'')?> value="<?php echo esc_url($archive_link)?>"><?php echo esc_html($archive_text)?></option>
															<?php foreach ($options_categories as $category) {
																echo apply_filters("wpqa_select_filter_categories",'<option '.($cats_search != ""?selected(esc_html(get_query_var((is_category()?'cat':'term'))),(is_category()?$category->term_id:$category->slug),false):"").' value="'.get_term_link($category).'">'.esc_html($category->name).'</option>',$tax_filter,$category,$tax_question,$tax_knowledgebase);
															}?>
														</select>
													</span>
												</div>
											<?php }
											$show_follow_button = apply_filters("wpqa_show_follow_button",false);
											if ((is_tax($tax_question) || $show_follow_button != "") && $active_cover_category != "on") {
												$tax_id = (int)get_query_var('wpqa_term_id');
												if ($tax_id == 0) {
													$tax_id = (isset($term) && isset($term->term_id)?$term->term_id:0);
												}
												echo wpqa_follow_cat_button($tax_id,$user_id,"cat","","button-default btn btn__sm","","","btn__success","btn__danger");
											}
											if ($cat_search == "on") {
												if (is_page_template("template-categories.php")) {
													$cats_tax = get_post_meta($post->ID,prefix_meta.'cats_tax',true);
													$cats_tax = ($cats_tax != ""?$cats_tax:wpqa_questions_type);
												}else {
													$cats_tax = (is_tax($tax_question) || $tax_filter == true || is_post_type_archive($post_type)?$post_type:"post");
													$cats_tax = (is_tax($tax_knowledgebase) || is_post_type_archive(wpqa_knowledgebase_type)?wpqa_knowledgebase_type:$cats_tax);
												}
												echo '<form method="get" action="'.esc_url(wpqa_get_search_permalink()).'" class="search-input-form main-search-form">
													<input class="form-control search-input'.($live_search == "on"?" live-search live-search-icon":"").'"'.($live_search == "on"?" autocomplete='off'":"").' type="search" name="search" placeholder="'.esc_attr__('Type to find...','wpqa').'">';
													if ($live_search == "on") {
														echo '<div class="loader_2 search_loader"></div>
														<div class="live-search-results mt-2 search-results results-empty"></div>';
													}
													echo '<button class="button-search"><i class="icon-search"></i></button>
													<input type="hidden" name="search_type" class="search_type" value="'.apply_filters("wpqa_breadcrumb_search_type",$cats_search).'">
												</form>';
											}
											do_action("wpqa_after_select_filter");?>
										</div><!-- End search-form -->
									<?php }
								}
							}else if (is_page_template("template-tags.php") || is_tag() || is_tax(wpqa_question_tags) || is_tax(wpqa_question_tags)) {
								if (is_page_template("template-tags.php")) {
									$tag_search = get_post_meta($post->ID,prefix_meta.'tag_search',true);
									$tag_filter = get_post_meta($post->ID,prefix_meta.'tag_filter',true);
								}else {
									$tag_search = wpqa_options("tag_search");
									$tag_filter = "";
								}
								if ($tag_search == "on" || $tag_filter == "on") {
									echo '<div class="search-form">';
										if (is_page_template("template-tags.php") && $tag_filter == "on") {
											$tag_sort = get_post_meta($post->ID,prefix_meta.'tag_sort',true);
											$tag_sort = ($tag_sort != ""?$tag_sort:"name");
											$g_tag_filter = (isset($_GET["tag_filter"]) && $_GET["tag_filter"] != ""?esc_html($_GET["tag_filter"]):$tag_sort);
											echo '<form method="get" class="search-filter-form">
												<span class="styled-select tag-filter">
													<select class="form-control" name="tag_filter" onchange="this.form.submit()">
														<option value="count" '.selected($g_tag_filter,"count",false).'>'.esc_html__('Popular','wpqa').'</option>
														<option value="followers" '.selected($g_tag_filter,"followers",false).'>'.esc_html__('Followers','wpqa').'</option>
														<option value="name" '.selected($g_tag_filter,"name",false).'>'.esc_html__('Name','wpqa').'</option>
													</select>
												</span>
											</form>';
										}
										if (is_tax(wpqa_question_tags)) {
											$tax_id = (int)get_query_var('wpqa_term_id');
											echo wpqa_follow_cat_button($tax_id,$user_id,"tag","","button-default btn btn__sm","","","btn__success","btn__danger");
										}
										if ($tag_search == "on") {
											if (is_page_template("template-tags.php")) {
												$tags_tax = get_post_meta($post->ID,prefix_meta.'tags_tax',true);
												$tags_tax = ($tags_tax != ""?$tags_tax:wpqa_questions_type);
											}else {
												$tags_tax = (is_tax(wpqa_question_tags)?wpqa_questions_type:"post");
												$tags_tax = (is_tax(wpqa_knowledgebase_tags)?wpqa_knowledgebase_type:$tags_tax);
											}
											echo '<form method="get" action="'.esc_url(wpqa_get_search_permalink()).'" class="search-input-form main-search-form">
												<input class="form-control search-input'.($live_search == "on"?" live-search live-search-icon":"").'"'.($live_search == "on"?" autocomplete='off'":"").' type="search" name="search" placeholder="'.esc_attr__('Type to find...','wpqa').'">';
												if ($live_search == "on") {
													echo '<div class="loader_2 search_loader"></div>
													<div class="live-search-results mt-2 search-results results-empty"></div>';
												}
												echo '<button class="button-search"><i class="icon-search"></i></button>
												<input type="hidden" name="search_type" class="search_type" value="'.($tags_tax == "post"?"post_tag":($tags_tax == wpqa_questions_type?wpqa_question_tags:wpqa_knowledgebase_tags)).'">
											</form>';
										}
									echo '</div>';
								}
							}else if (is_page_template("template-users.php")) {
								$user_search = get_post_meta($post->ID,prefix_meta.'user_search',true);
								$user_filter = get_post_meta($post->ID,prefix_meta.'user_filter',true);
								if ($user_search == "on" || $user_filter == "on") {
									echo '<div class="search-form">';
										if ($user_filter == "on") {
											$user_sort = get_post_meta($post->ID,prefix_meta.'user_sort',true);
											$user_sort = ($user_sort != ""?$user_sort:"user_registered");
											$g_user_filter = (isset($_GET["user_filter"]) && $_GET["user_filter"] != ""?esc_html($_GET["user_filter"]):$user_sort);
											echo '<form method="get" class="search-filter-form">
												<span class="styled-select user-filter">
													<select class="form-control" name="user_filter" onchange="this.form.submit()">
														<option value="user_registered" '.selected($g_user_filter,"user_registered",false).'>'.esc_html__('Date Registered','wpqa').'</option>
														<option value="display_name" '.selected($g_user_filter,"display_name",false).'>'.esc_html__('Name','wpqa').'</option>
														<option value="ID" '.selected($g_user_filter,"ID",false).'>'.esc_html__('ID','wpqa').'</option>
														<option value="question_count" '.selected($g_user_filter,"question_count",false).'>'.esc_html__('Questions','wpqa').'</option>
														<option value="answers" '.selected($g_user_filter,"answers",false).'>'.esc_html__('Answers','wpqa').'</option>
														<option value="the_best_answer" '.selected($g_user_filter,"the_best_answer",false).'>'.esc_html__('Best Answers','wpqa').'</option>';
														if ($active_points == "on") {
															echo '<option value="points" '.selected($g_user_filter,"points",false).'>'.esc_html__('Points','wpqa').'</option>';
														}
														echo '<option value="followers" '.selected($g_user_filter,"followers",false).'>'.esc_html__('Followers','wpqa').'</option>
														<option value="post_count" '.selected($g_user_filter,"post_count",false).'>'.esc_html__('Posts','wpqa').'</option>
														<option value="comments" '.selected($g_user_filter,"comments",false).'>'.esc_html__('Comments','wpqa').'</option>
													</select>
												</span>
											</form>';
										}
										if ($user_search == "on") {
											echo '<form method="get" action="'.esc_url(wpqa_get_search_permalink()).'" class="search-input-form main-search-form">
												<input class="form-control search-input'.($live_search == "on"?" live-search live-search-icon":"").'"'.($live_search == "on"?" autocomplete='off'":"").' type="search" name="search" placeholder="'.esc_attr__('Type to find...','wpqa').'">';
												if ($live_search == "on") {
													echo '<div class="loader_2 search_loader"></div>
													<div class="live-search-results mt-2 search-results results-empty"></div>';
												}
												echo '<button class="button-search"><i class="icon-search"></i></button>
												<input type="hidden" name="search_type" class="search_type" value="users">
											</form>';
										}
									echo '</div>';
								}
							}else if (is_post_type_archive("group") || is_page_template("template-groups.php")) {
								if (is_page_template("template-groups.php")) {
									$group_search = get_post_meta($post->ID,prefix_meta.'group_search',true);
								}else {
									$group_search = wpqa_options("group_search");
								}
								if ($group_search == "on") {
									echo '<div class="search-form">';
										echo '<form method="get" action="'.esc_url(wpqa_get_search_permalink()).'" class="search-input-form main-search-form">
											<input class="form-control search-input'.($live_search == "on"?" live-search live-search-icon":"").'"'.($live_search == "on"?" autocomplete='off'":"").' type="search" name="search" placeholder="'.esc_attr__('Type to find...','wpqa').'">';
											if ($live_search == "on") {
												echo '<div class="loader_2 search_loader"></div>
												<div class="live-search-results mt-2 search-results results-empty"></div>';
											}
											echo '<button class="button-search"><i class="icon-search"></i></button>
											<input type="hidden" name="search_type" class="search_type" value="groups">
										</form>';
									echo '</div>';
								}
							}else if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) {
								$question_navigation = wpqa_options("question_navigation");
								$question_nav_category = wpqa_options("question_nav_category");
								$custom_page_setting = get_post_meta($post->ID,prefix_meta.'custom_page_setting',true);
								if ($custom_page_setting == "on") {
									$question_navigation = get_post_meta($post->ID,prefix_meta.'post_navigation',true);
									$question_nav_category = get_post_meta($post->ID,prefix_meta.'question_nav_category',true);
								}
								if ($question_navigation == "on") {
									if ($question_nav_category == "on") {
										$previous_post = get_previous_post(true,'',wpqa_question_categories);
										$next_post = get_next_post(true,'',wpqa_question_categories);
									}else {
										$previous_post = get_previous_post();
										$next_post = get_next_post();
									}?>
									<div class="question-navigation breadcrumb-navs d-flex">
										<?php if (isset($next_post) && is_object($next_post)) {?>
											<a class="nav-next breadcrumb-navs__item mr-1" href="<?php echo get_permalink($next_post->ID)?>"><span><?php esc_html_e("Next","wpqa")?></span><i class="icon-right-open"></i></a>
										<?php }
										if (isset($previous_post) && is_object($previous_post)) {?>
											<a class="nav-previous breadcrumb-navs__item" href="<?php echo get_permalink($previous_post->ID)?>"><i class="icon-left-open"></i></a>
										<?php }?>
									</div><!-- End page-navigation -->
								<?php }
								$question_stats = apply_filters('wpqa_question_stats',true);
								$the_best_answer = get_post_meta($post->ID,"the_best_answer",true);
								$count_post_all = (int)wpqa_count_comments($post->ID);
								$closed_question = get_post_meta($post->ID,"closed_question",true);
								if ($question_stats == true && ($closed_question == 1 || (isset($the_best_answer) && $the_best_answer != "" && $count_post_all > 0) || ($the_best_answer == "" && $count_post_all > 0))) {?>
									<div class="question-stats">
										<?php if ($closed_question == 1) {?>
											<span class="question-stats-closed question-closed badge-span btn__primary"><i class="icon-cancel"></i><?php esc_html_e("Closed","wpqa")?></span>
										<?php }else if (isset($the_best_answer) && $the_best_answer != "" && $count_post_all > 0) {?>
											<span class="question-stats-answered question-answered-done badge-span btn__success"><i class="icon-check"></i><?php esc_html_e("Answered","wpqa")?></span>
										<?php }else if ($the_best_answer == "" && $count_post_all > 0) {?>
											<span class="question-stats-process badge-span btn__danger"><i class="icon-flash"></i><?php esc_html_e("In Process","wpqa")?></span>
										<?php }?>
									</div><!-- End question-stats -->
								<?php }
							}else if (is_singular(wpqa_knowledgebase_type)) {
								$knowledgebase_navigation = wpqa_options("knowledgebase_navigation");
								$knowledgebase_nav_category = wpqa_options("knowledgebase_nav_category");
								$custom_page_setting = get_post_meta($post->ID,prefix_meta.'custom_page_setting',true);
								if ($custom_page_setting == "on") {
									$knowledgebase_navigation = get_post_meta($post->ID,prefix_meta.'post_navigation',true);
									$knowledgebase_nav_category = get_post_meta($post->ID,prefix_meta.'knowledgebase_nav_category',true);
								}
								if (isset($knowledgebase_navigation["breadcrumbs"]) && $knowledgebase_navigation["breadcrumbs"] == "breadcrumbs") {
									if ($knowledgebase_nav_category == "on") {
										$previous_post = get_previous_post(true,'',wpqa_knowledgebase_categories);
										$next_post = get_next_post(true,'',wpqa_knowledgebase_categories);
									}else {
										$previous_post = get_previous_post();
										$next_post = get_next_post();
									}?>
									<div class="question-navigation breadcrumb-navs d-flex">
										<?php if (isset($next_post) && is_object($next_post)) {?>
											<a class="nav-next breadcrumb-navs__item mr-1" href="<?php echo get_permalink($next_post->ID)?>"><span><?php esc_html_e("Next","wpqa")?></span><i class="icon-right-open"></i></a>
										<?php }
										if (isset($previous_post) && is_object($previous_post)) {?>
											<a class="nav-previous breadcrumb-navs__item" href="<?php echo get_permalink($previous_post->ID)?>"><i class="icon-left-open"></i></a>
										<?php }?>
									</div><!-- End page-navigation -->
								<?php }
							}
							do_action("wpqa_right_breadcrumb");
							echo '<div class="clearfix"></div>
						</div><!-- End breadcrumb-right -->';
					}
				echo '</div><!-- End breadcrumbs-wrap -->';
			if ($breadcrumbs_style == "style_2") {
				echo '</div><!-- End the-main-container -->';
			}
		echo '</div><!-- End breadcrumbs -->';
	}
endif;
/* Get taxonomy parents */
if (!function_exists('wpqa_get_taxonomy_parents')) :
	function wpqa_get_taxonomy_parents( $id, $taxonomy = 'category', $link = false,$main_id = '', $visited = array(), $before = "" ) {
		$out = '';
		$parent = get_term( $id, $taxonomy );
		if ( is_wp_error( $parent ) ) {
			return $parent;
		}
		$name = $parent->name;
		if ( $parent->parent && ( $parent->parent != $parent->term_id ) && is_array($visited) && !in_array( $parent->parent, $visited ) ) {
			$visited[] = $parent->parent;
			$out .= $before.wpqa_get_taxonomy_parents( $parent->parent, $taxonomy, $link, $visited, $before );
		}
		if ( $link ) {
			if ($parent->term_id != $main_id) {
				$out .= '<a href="' . esc_url( get_term_link( $parent ) ) . '" title="' . esc_attr( $parent->name ) . '">'.$name.'</a>';
			}
		}else {
			$out .= $name;
		}
		return $out;
	}
endif;
/* Get term parents */
if (!function_exists('wpqa_breadcrumbs_get_term_parents')) :
	function wpqa_breadcrumbs_get_term_parents( $parent_id = '', $taxonomy = '' ) {
		$html = array();
		$parents = array();
		if ( empty( $parent_id ) || empty( $taxonomy ) )
			return $parents;
		$counter = 1;
		while ( $parent_id ) {
			$counter++;
			$parent = get_term( $parent_id, $taxonomy );
			$parents[] = wpqa_breadcrumbs_schema(esc_url(get_term_link($parent)),$parent->name,$counter,"yes");
			$parent_id = $parent->parent;
		}
		if ( $parents )
			$parents = array_reverse( $parents );
		return $parents;
	}
endif;
/* Breadcrumbs schema */
if (!function_exists('wpqa_breadcrumbs_schema')) :
	function wpqa_breadcrumbs_schema($link = '',$name = '',$position = '',$current = '') {
		$return = '<span'.($current != ''?' class="current"':'').' itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
			<meta itemprop="position" content="'.$position.'">';
			if ($link != '') {
				$return .= '<a itemprop="item" href="'.$link.'" title="'.esc_attr(str_replace('<i class="icon-home font-xl mr-2"></i>','',$name)).'">';
			}
				$return .= '<span itemprop="name">'.$name.'</span>';
			if ($link != '') {
				$return .= '</a>';
			}
		$return .= '</span>';
		return $return;
	}
endif;?>