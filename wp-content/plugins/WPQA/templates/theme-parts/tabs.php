<?php include wpqa_get_template("slugs.php","includes/");

$active_points = wpqa_options("active_points");
$question_bump = wpqa_options("question_bump");
if (isset($tab_category) && $tab_category == true) {
	$get_home_tabs = wpqa_options("category_tabs");
	$tabs_menu_select = $tabs_menu = "";
}else if (isset($tab_tag) && $tab_tag == true) {
	$get_home_tabs = wpqa_options("tag_tabs");
	$tabs_menu_select = $tabs_menu = "";
}else {
	$post_id_main      = (isset($post_id_main)?$post_id_main:"");
	$search_box        = wpqa_post_meta("search_box",$post_id_main);
	$search_type_box   = wpqa_post_meta("search_type_box",$post_id_main);
	$ask_question_box  = wpqa_post_meta("ask_question_box",$post_id_main);
	$get_home_tabs     = wpqa_post_meta("home_tabs",$post_id_main);
	$categories_filter = wpqa_post_meta("categories_filter",$post_id_main);
	$tabs_menu         = get_option("tabs_menu");
	$tabs_menu_select  = get_option("tabs_menu_select");
}

$first_one = wpqa_home_setting($get_home_tabs,((isset($tab_category) && $tab_category == true) || (isset($tab_tag) && $tab_tag == true)?$category_id:""));

if (isset($get_home_tabs) && is_array($get_home_tabs) && !empty($get_home_tabs)) {
	foreach ($get_home_tabs as $key => $value) {
		if (isset($value["cat"]) && $value["cat"] === "yes" && isset($value["value"]) && $value["value"] > 0) {
			$get_tax_tabs = get_term_by('id',$value["value"],wpqa_question_categories);
			if (!isset($get_tax_tabs->term_id)) {
				$get_tax_tabs = get_term_by('id',$value["value"],wpqa_knowledgebase_categories);
			}
			if (isset($get_tax_tabs->term_id) && isset($first_one) && $first_one == $get_tax_tabs->slug) {
				$get_home_tabs[$key][$get_tax_tabs->taxonomy] = $get_tax_tabs->term_id;
				$get_home_tabs[$first_one][$get_tax_tabs->taxonomy] = $get_tax_tabs->term_id;
			}
		}
	}
}

if (!isset($tab_category) && !isset($tab_tag)) {
	if ($categories_filter == "on") {
		$exclude = apply_filters('wpqa_exclude_question_category',array());
		$args = array_merge($exclude,array(
			'child_of'     => 0,
			'orderby'      => 'name',
			'order'        => 'ASC',
			'hide_empty'   => 1,
			'hierarchical' => 1,
			'taxonomy'     => wpqa_question_categories
		));
		$options_categories = get_categories($args);
	}

	if ($search_box == "on") {
		$search_type = wpqa_search_type();
		$show_button = false;
		$show_search_form_filter = apply_filters("wpqa_show_search_form_filter",true,$search_type);
		if ($show_search_form_filter == true) {
			include wpqa_get_template(array('search-form.php'));
		}else {
			do_action("wpqa_action_search_form",$search_type);
		}
	}

	if ($ask_question_box == "on") {?>
		<div class="ask-box-question wpqa-question card<?php echo apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_question','')?>">
			<div class="d-flex align-items-center">
				<?php $user_id = get_current_user_id();
				do_action("wpqa_user_avatar",array("user_id" => $user_id,"size" => 30,"class" => "search-area__avatar mt-1 mr-3 rounded-circle"));?>
				<div class="box-question search-area__form flex-grow-1 form-control">
					<i class="icon-chat"></i><?php esc_html_e("What's your question?","wpqa")?>
					<a href="<?php echo wpqa_add_question_permalink()?>" class="wpqa-question<?php echo apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_question','')?>"></a>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	<?php }
}?>
<div id="row-tabs-home" class="row row-boot row-tabs">
	<?php if ($tabs_menu != "on" && ($tabs_menu_select == "default" || $tabs_menu_select == "")) {
		if (isset($get_home_tabs) && is_array($get_home_tabs)) {
			if (isset($first_one) && $first_one != "") {?>
				<div class="col <?php echo (isset($options_categories) && is_array($options_categories) && $categories_filter == "on"?apply_filters(wpqa_prefix_theme."_col9_tab","col9 col-boot-sm-9"):"col12 col-boot-sm-12")?>">
					<?php wpqa_home_tabs($get_home_tabs,$first_one,(isset($category_id) && $category_id > 0?$category_id:0),"",(isset($post_id_main) && $post_id_main != ""?$post_id_main:""));?>
				</div><!-- End col9 -->
			<?php }
		}
		
		if (isset($options_categories) && is_array($options_categories) && $categories_filter == "on") {
			if (isset($_POST['home_categories'])) {
				wp_safe_redirect(esc_url($_POST['home_categories']));
				exit;
			}?>
			<div class="col <?php echo apply_filters(wpqa_prefix_theme."_col3_tab","col3 col-boot-sm-3")?>">
				<div class="categories-home">
					<div class="search-form">
						<?php do_action(wpqa_prefix_theme."_before_select_filter");?>
						<div class="search-filter-form">
							<span class="styled-select cat-filter">
								<select class="form-control home_categories">
									<option value="<?php echo get_post_type_archive_link(wpqa_questions_type)?>"><?php esc_html_e('All Questions','wpqa')?></option>
									<?php foreach ($options_categories as $category) {?>
										<option value="<?php echo get_term_link($category)?>"><?php echo esc_html($category->name)?></option>
									<?php }?>
								</select>
							</span>
						</div>
						<?php do_action(wpqa_prefix_theme."_after_select_filter");?>
					</div><!-- End search-form -->
				</div><!-- End categories-home -->
			</div><!-- End col3 -->
		<?php }
	}?>
</div><!-- End row -->

<?php $get_loop = $orderby_post = "";
$tabs_available = apply_filters(wpqa_prefix_theme."_home_page_tabs_available",false,$get_home_tabs,(isset($first_one)?$first_one:""),(isset($category_id) && $category_id > 0?$category_id:0));
if (isset($first_one) && $first_one != "" && $tabs_available == true) {
	$get_loop = true;
}else if (isset($first_one) && ($first_one == "q-0" && isset($get_home_tabs["cat-q-0"]["value"]) && $get_home_tabs["cat-q-0"]["value"] != "" && $get_home_tabs["cat-q-0"]["value"] == "q-0") || (isset($get_home_tabs[$first_one][wpqa_question_categories]) && $get_home_tabs[$first_one][wpqa_question_categories] != "")) {
	$get_loop = true;
	$its_post_type = wpqa_questions_type;
	if (isset($get_home_tabs[$first_one][wpqa_question_categories]) && $get_home_tabs[$first_one][wpqa_question_categories] != "") {
		$get_term_id = $get_home_tabs[$first_one][wpqa_question_categories];
		$get_term_tax = wpqa_question_categories;
	}
}else if (isset($first_one) && ($first_one == "k-0" && isset($get_home_tabs["cat-k-0"]["value"]) && $get_home_tabs["cat-k-0"]["value"] != "" && $get_home_tabs["cat-k-0"]["value"] == "k-0") || (isset($get_home_tabs[$first_one][wpqa_knowledgebase_categories]) && $get_home_tabs[$first_one][wpqa_knowledgebase_categories] != "")) {
	$get_loop = true;
	$its_post_type = wpqa_knowledgebase_type;
	if (isset($get_home_tabs[$first_one][wpqa_knowledgebase_categories]) && $get_home_tabs[$first_one][wpqa_knowledgebase_categories] != "") {
		$get_term_id = $get_home_tabs[$first_one][wpqa_knowledgebase_categories];
		$get_term_tax = wpqa_knowledgebase_categories;
	}
}else {
	echo '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, this page is not found.","wpqa").'</p></div>';
}

if ($get_loop == true) {
	if (isset($first_one) && $first_one != "") {
		if ($first_one == $most_answers_slug || $first_one == $most_answers_slug_2) {
			$orderby_post = "popular";
		}else if ($first_one == $no_answers_slug || $first_one == $no_answers_slug_2) {
			$orderby_post = "no_answer";
		}else if ($first_one == $most_visit_slug || $first_one == $most_visit_slug_2 || $first_one == $posts_visited_slug || $first_one == $posts_visited_slug_2 || $first_one == $knowledgebases_visited_slug || $first_one == $knowledgebases_visited_slug_2) {
			$orderby_post = "most_visited";
		}else if ($first_one == $most_vote_slug || $first_one == $most_vote_slug_2 || $first_one == $knowledgebases_voted_slug || $first_one == $knowledgebases_voted_slug_2) {
			$orderby_post = "most_voted";
		}else if ($first_one == $most_reacted_slug || $first_one == $most_reacted_slug_2) {
			$orderby_post = "most_reacted";
		}else if ($first_one == $random_slug || $first_one == $random_slug_2 || $first_one == $random_knowledgebases_slug || $first_one == $random_knowledgebases_slug_2) {
			$orderby_post = "random";
		}else if ($question_bump == "on" && $active_points == "on" && ($first_one == $question_bump_slug || $first_one == $question_bump_slug_2)) {
			$orderby_post = "question_bump";
		}
		
		if ($first_one == $recent_knowledgebases_slug || $first_one == $random_knowledgebases_slug || $first_one == $sticky_knowledgebases_slug || $first_one == $knowledgebases_visited_slug || $first_one == $knowledgebases_voted_slug || $first_one == $recent_knowledgebases_slug_2 || $first_one == $random_knowledgebases_slug_2 || $first_one == $sticky_knowledgebases_slug_2 || $first_one == $knowledgebases_visited_slug_2 || $first_one == $knowledgebases_voted_slug_2) {
			$its_post_type = wpqa_knowledgebase_type;
		}else if ($first_one != $recent_posts_slug && $first_one != $recent_posts_slug_2 && $first_one != $posts_visited_slug && $first_one != $posts_visited_slug_2) {
			$its_post_type = wpqa_questions_type;
		}
		
		include wpqa_get_template("loop.php","theme-parts/");
	}
}?>