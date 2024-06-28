<?php

/* @author    2codeThemes
*  @package   WPQA/options
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Meta options */
function wpqa_admin_meta() {
	global $post,$pagenow;
	$options = array();
	if (is_admin() && ($pagenow == "post-new.php" || ((isset($_REQUEST['post']) && $_REQUEST['post'] != "") || (isset($_REQUEST['post_ID']) && $_REQUEST['post_ID'] != "")) && ($pagenow == "post.php" || $pagenow == "themes.php"))) {
		$options = apply_filters('wpqa_the_meta_options',$options,$post,$pagenow);
	}
	return $options;
}
/* Meta feed */
add_filter(wpqa_prefix_theme."_options_question_feed","wpqa_options_question_feed",1,2);
function wpqa_options_question_feed($options,$home = '') {
	$options[] = array(
		'name'    => esc_html__('Show the recent questions for unlogged users on feed page or show message must login to see the feed?','wpqa'),
		'id'      => prefix_meta.'login_'.$home.'feed',
		'type'    => 'radio',
		'std'     => 'recent',
		'options' => array(
			"recent" => esc_html__('Recent Questions','wpqa'),
			"login"  => esc_html__('Must login','wpqa'),
		)
	);

	$feed = array(
		"users" => array("sort" => esc_html__('Users','wpqa'),"value" => "users"),
		"cats"  => array("sort" => esc_html__('Categories','wpqa'),"value" => "cats"),
		"tags"  => array("sort" => esc_html__('Tags ','wpqa'),"value" => "tags"),
	);
	
	$options[] = array(
		'name'    => esc_html__('Select the sections you want to show at feed','wpqa'),
		'id'      => prefix_meta.$home.'feed',
		'type'    => 'multicheck',
		'sort'    => 'yes',
		'std'     => $feed,
		'options' => $feed
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => prefix_meta.$home.'feed:has(users)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'type' => 'info',
		'name' => esc_html__('Users section setting','wpqa')
	);
	
	$options[] = array(
		'name' => esc_html__('Number of other users to be followed for the user to make their feed','wpqa'),
		"id"   => prefix_meta."users_".$home."feed",
		"type" => "sliderui",
		'std'  => '6',
		"step" => "1",
		"min"  => "0",
		"max"  => "10"
	);

	$options[] = array(
		'name'  => esc_html__('Show the users in slider?','wpqa'),
		'id'    => prefix_meta.'users_slider_'.$home.'feed',
		'type'  => 'checkbox',
	);

	$options[] = array(
		'name'  => esc_html__('Show load more for the users?','wpqa'),
		'id'    => prefix_meta.'users_more_'.$home.'feed',
		'type'  => 'checkbox',
	);

	$options[] = array(
		'name'      => esc_html__('Custom link for the users page?','wpqa'),
		'id'        => prefix_meta.'custom_link_users_'.$home.'feed',
		'condition' => prefix_meta.'users_more_'.$home.'feed:not(0)',
		'type'      => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Order by','wpqa'),
		'id'      => prefix_meta.'user_sort_'.$home.'feed',
		'std'     => "points",
		'type'    => 'select',
		'options' => array(
			'points'          => esc_html__('Points','wpqa'),
			'the_best_answer' => esc_html__('Best Answers','wpqa'),
			'answers'         => esc_html__('Answers','wpqa'),
			'question_count'  => esc_html__('Questions','wpqa'),
			'followers'       => esc_html__('Followers','wpqa'),
		),
	);

	$options[] = array(
		'name'    => esc_html__('Users style','wpqa'),
		'id'      => prefix_meta.'user_style_'.$home.'feed',
		'options' => array(
			'columns'       => esc_html__('Columns','wpqa'),
			'small_grid'    => esc_html__('Small grid with follow','wpqa'),
			'simple_follow' => esc_html__('Simple with follow','wpqa'),
			'small'         => esc_html__('Small','wpqa'),
			'grid'          => esc_html__('Grid','wpqa'),
			'normal'        => esc_html__('Normal','wpqa'),
		),
		'std'     => 'columns',
		'type'    => 'radio'
	);
		
	$options[] = array(
		'name'      => esc_html__("Activate the masonry style?","wpqa"),
		'id'        => prefix_meta.'masonry_user_style_'.$home.'feed',
		'type'      => 'checkbox',
		'condition' => prefix_meta.'user_style_'.$home.'feed:is(small_grid),'.prefix_meta.'user_style_'.$home.'feed:is(columns),'.prefix_meta.'user_style_'.$home.'feed:is(small),'.prefix_meta.'user_style_'.$home.'feed:is(grid)',
		'operator'  => 'or',
	);
	
	$options[] = array(
		'name' => esc_html__('Users per page','wpqa'),
		'id'   => prefix_meta.'users_per_'.$home.'feed',
		'std'  => '6',
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => prefix_meta.$home.'feed:has(cats)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'type' => 'info',
		'name' => esc_html__('Categories section setting','wpqa')
	);
	
	$options[] = array(
		'name' => esc_html__('Number of categories to be followed by the user to make their feed','wpqa'),
		"id"   => prefix_meta."categories_".$home."feed",
		"type" => "sliderui",
		'std'  => '6',
		"step" => "1",
		"min"  => "1",
		"max"  => "10"
	);

	$options[] = array(
		'name'  => esc_html__('Show the categories in slider?','wpqa'),
		'id'    => prefix_meta.'cats_slider_'.$home.'feed',
		'type'  => 'checkbox',
	);

	$options[] = array(
		'name'  => esc_html__('Show load more for the categories?','wpqa'),
		'id'    => prefix_meta.'cats_more_'.$home.'feed',
		'type'  => 'checkbox',
	);

	$options[] = array(
		'name'      => esc_html__('Custom link for the categories page?','wpqa'),
		'id'        => prefix_meta.'custom_link_cats_'.$home.'feed',
		'condition' => prefix_meta.'cats_more_'.$home.'feed:not(0)',
		'type'      => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Order by','wpqa'),
		'id'      => prefix_meta.'cat_sort_'.$home.'feed',
		'std'     => "count",
		'type'    => 'select',
		'options' => array(
			'count'     => esc_html__('Questions','wpqa'),
			//'answers'   => esc_html__('Answers','wpqa'),
			'followers' => esc_html__('Followers','wpqa'),
		),
	);

	if (has_discy()) {
		$cat_style = array(
			"with_icon"     => esc_html__("With icon","wpqa"),
			"icon_color"    => esc_html__("With icon and colors","wpqa"),
			'with_icon_1'   => esc_html__('With icon 2','wpqa'),
			'with_icon_2'   => esc_html__('With colored icon','wpqa'),
			'with_icon_3'   => esc_html__('With colored icon and box','wpqa'),
			'with_icon_4'   => esc_html__('With colored icon and box 2','wpqa'),
			'with_cover_1'  => esc_html__('With cover','wpqa'),
			'with_cover_2'  => esc_html__('With cover and icon','wpqa'),
			'with_cover_3'  => esc_html__('With cover and small icon','wpqa'),
			'with_cover_4'  => esc_html__('With big cover','wpqa'),
			'with_cover_5'  => esc_html__('With big cover and icon','wpqa'),
			'with_cover_6'  => esc_html__('With big cover and small icon','wpqa'),
			'simple_follow' => esc_html__('Simple with follow','wpqa'),
			'simple'        => esc_html__('Simple','wpqa'),
		);
	}else {
		$cat_style = array(
			"with_icon"     => esc_html__("With icon","wpqa"),
			"icon_color"    => esc_html__("With icon and colors","wpqa"),
			'with_icon_1'   => esc_html__('With icon 2','wpqa'),
			'with_icon_2'   => esc_html__('With colored icon','wpqa'),
			'with_icon_3'   => esc_html__('With colored icon and box','wpqa'),
			'with_icon_4'   => esc_html__('With colored icon and box 2','wpqa'),
			'with_cover_1'  => esc_html__('With cover','wpqa'),
			'with_cover_3'  => esc_html__('With cover and small icon','wpqa'),
			'with_cover_4'  => esc_html__('With big cover','wpqa'),
			'with_cover_6'  => esc_html__('With big cover and small icon','wpqa'),
			'simple_follow' => esc_html__('Simple with follow','wpqa'),
			'simple'        => esc_html__('Simple','wpqa'),
		);
	}

	$options[] = array(
		'name'    => esc_html__('Categories style','wpqa'),
		'id'      => prefix_meta.'cat_style_'.$home.'feed',
		'options' => $cat_style,
		'std'     => 'simple_follow',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Categories per page','wpqa'),
		'id'   => prefix_meta.'cat_per_'.$home.'feed',
		'std'  => '6',
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => prefix_meta.$home.'feed:has(tags)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'type' => 'info',
		'name' => esc_html__('Tags section setting','wpqa')
	);
	
	$options[] = array(
		'name' => esc_html__('Number of tags to be followed by the user make their feed','wpqa'),
		"id"   => prefix_meta."tags_".$home."feed",
		"type" => "sliderui",
		'std'  => '6',
		"step" => "1",
		"min"  => "0",
		"max"  => "10"
	);

	$options[] = array(
		'name'  => esc_html__('Show the tags in slider?','wpqa'),
		'id'    => prefix_meta.'tags_slider_'.$home.'feed',
		'type'  => 'checkbox',
	);

	$options[] = array(
		'name'  => esc_html__('Show load more for the tags?','wpqa'),
		'id'    => prefix_meta.'tags_more_'.$home.'feed',
		'type'  => 'checkbox',
	);

	$options[] = array(
		'name'      => esc_html__('Custom link for the tags page?','wpqa'),
		'id'        => prefix_meta.'custom_link_tags_'.$home.'feed',
		'condition' => prefix_meta.'tags_more_'.$home.'feed:not(0)',
		'type'      => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Order by','wpqa'),
		'id'      => prefix_meta.'tag_sort_'.$home.'feed',
		'std'     => "count",
		'type'    => 'select',
		'options' => array(
			'count'     => esc_html__('Questions','wpqa'),
			//'answers'   => esc_html__('Answers','wpqa'),
			'followers' => esc_html__('Followers','wpqa'),
		),
	);
	
	$options[] = array(
		'name' => esc_html__('Tags per page','wpqa'),
		'id'   => prefix_meta.'tag_per_'.$home.'feed',
		'std'  => '6',
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	return $options;
}
/* Check Post type */
function wpqa_is_post_type($post_types = array("post")) {
	if (isset($post_types) && is_array($post_types)) {
		$screen = get_current_screen();
		if (in_array($screen->post_type,$post_types)) {
			return true;
		}
	}
}
/* Options */
add_filter("wpqa_the_meta_options","wpqa_the_meta_options",1,3);
function wpqa_the_meta_options($options,$post,$pagenow) {
	if (is_admin() && ($pagenow == "post-new.php" || ((isset($_REQUEST['post']) && $_REQUEST['post'] != "") || (isset($_REQUEST['post_ID']) && $_REQUEST['post_ID'] != "")) && ($pagenow == "post.php" || $pagenow == "themes.php"))) {
		$post_id = (isset($post->ID) && $post->ID > 0?$post->ID:0);

		$activate_currencies = wpqa_options("activate_currencies");

		$wpqa_options_roles = wpqa_options_roles();

		// Background Defaults
		$background_defaults = array(
			'color' => '',
			'image' => '',
			'repeat' => 'repeat',
			'position' => 'top center',
			'attachment'=>'scroll'
		);

		// Pull all the pages into an array
		$options_pages = array();
		$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
		$options_pages[''] = esc_html__('Select a page:','wpqa');
		foreach ($options_pages_obj as $page) {
			$options_pages[$page->ID] = $page->post_title;
		}
		
		// Pull all the sidebars into an array
		$new_sidebars = wpqa_registered_sidebars();
		
		// Pull all the menus into an array
		$menus = array();
		$all_menus = get_terms('nav_menu',array('hide_empty' => true));
		foreach ($all_menus as $menu) {
			$menus[$menu->term_id] = $menu->name;
		}
		
		// Share
		$share_array = array(
			"share_facebook" => array("sort" => "Facebook","value" => "share_facebook"),
			"share_twitter"  => array("sort" => "Twitter","value" => "share_twitter"),
			"share_linkedin" => array("sort" => "LinkedIn","value" => "share_linkedin"),
			"share_whatsapp" => array("sort" => "WhatsApp","value" => "share_whatsapp"),
		);
		
		// Question meta
		$question_meta_std = array(
			"author_by"         => "author_by",
			"question_date"     => "question_date",
			"category_question" => "category_question",
			"question_answer"   => "question_answer",
			"question_views"    => "question_views",
			"bump_meta"         => "bump_meta",
		);
		
		$question_meta_options = array(
			"author_by"         => esc_html__('Author by','wpqa'),
			"question_date"     => esc_html__('Date meta','wpqa'),
			"category_question" => esc_html__('Category question','wpqa'),
			"question_answer"   => esc_html__('Answer meta','wpqa'),
			"question_views"    => esc_html__('Views stats','wpqa'),
			"bump_meta"         => esc_html__('Bump question meta','wpqa'),
		);
		
		// Post meta
		$post_meta_std = array(
			"category_post" => "category_post",
			"title_post"    => "title_post",
			"author_by"     => "author_by",
			"post_date"     => "post_date",
			"post_comment"  => "post_comment",
			"post_views"    => "post_views",
		);
		
		$post_meta_options = array(
			"category_post" => esc_html__('Category post - Work at 1 column only','wpqa'),
			"title_post"    => esc_html__('Title post','wpqa'),
			"author_by"     => esc_html__('Author by - Work at 1 column only','wpqa'),
			"post_date"     => esc_html__('Date meta','wpqa'),
			"post_comment"  => esc_html__('Comment meta','wpqa'),
			"post_views"    => esc_html__("Views stats","wpqa"),
		);

		// Knowledgebase
		$activate_knowledgebase = apply_filters("wpqa_activate_knowledgebase",false);
		
		// If using image radio buttons, define a directory path
		$imagepath =  get_template_directory_uri(). '/admin/images/';
		$imagepath_theme =  get_template_directory_uri(). '/images/';

		$options = apply_filters(wpqa_prefix_theme.'_options_before_meta_options',$options,$post);
		
		if (wpqa_is_post_type(array(wpqa_questions_type,wpqa_asked_questions_type))) {
			$options[] = array(
				'name' => esc_html__('Question settings','wpqa'),
				'id'   => 'question_settings',
				'icon' => 'editor-help',
				'type' => 'heading'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);
			
			if ($post_id > 0) {
				$question_poll = get_post_meta($post_id,"question_poll",true);
				$show_the_anonymously = apply_filters(wpqa_prefix_theme."_show_the_anonymously",true);
				
				$question_html = '<div class="custom-meta-field">';
					if ($show_the_anonymously == true && $post->post_author == 0) {
						$anonymously_question = get_post_meta($post_id,"anonymously_question",true);
						$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
						if (($anonymously_question == "on" || $anonymously_question == 1) && $anonymously_user != "") {
							$question_username = esc_html__('Anonymous','wpqa');
							$question_email = 0;
						}else {
							$question_username = get_post_meta($post_id,"question_username",true);
							$question_email = get_post_meta($post_id,"question_email",true);
							$question_username = ($question_username != ""?$question_username:esc_html__('Anonymous','wpqa'));
							$question_email = ($question_email != ""?$question_email:"");
						}
						$question_html .= '<ul>
							<li><div class="clear"></div><br><span class="dashicons dashicons-admin-users"></span> '.esc_html($question_username).'</li>';
							if ($question_email != "") {
								$question_html .= '<li><div class="clear"></div><br><span class="dashicons dashicons-email-alt"></span> '.esc_html($question_email).'</li>';
							}
						$question_html .= '</ul>';
					}
					
					if (wpqa_is_post_type(array(wpqa_asked_questions_type))) {
						$get_question_user_id = get_post_meta($post_id,"user_id",true);
						$display_name = get_the_author_meta('display_name',$get_question_user_id);
						if (isset($display_name) && $display_name != "") {
							$question_html .= '<ul>
								<li><div class="clear"></div><br><span class="dashicons dashicons-admin-users"></span> '.esc_html__('This question has asked to','wpqa').' <a target="_blank" href="'.wpqa_profile_url($get_question_user_id).'">'.esc_html($display_name).'</a></li>
							</ul>
							<div class="no-user-question"></div>';
						}
					}else {
						$added_file = get_post_meta($post_id,"added_file",true);
						if ($added_file != "") {
							$question_html .= '<ul><li><div class="clear"></div><br><a href="'.wp_get_attachment_url($added_file).'">'.esc_html__('Attachment','wpqa').'</a> - <a class="delete-this-attachment single-attachment" href="'.$added_file.'">'.esc_html__('Delete','wpqa').'</a></li></ul>';
						}
						$attachment_m = get_post_meta($post_id,"attachment_m",true);
						if (isset($attachment_m) && is_array($attachment_m) && !empty($attachment_m)) {
							$question_html .= '<ul>';
								foreach ($attachment_m as $key => $value) {
									$question_html .= '<li><div class="clear"></div><br><a href="'.wp_get_attachment_url($value["added_file"]).'">'.esc_html__('Attachment','wpqa').'</a> - <a class="delete-this-attachment" href="'.$value["added_file"].'">'.esc_html__('Delete','wpqa').'</a></li>';
								}
							$question_html .= '</ul>';
						}
					}
				$question_html .= '</div>';
				
				$options[] = array(
					'type'    => 'content',
					'content' => $question_html,
				);

				if ($post_id > 0) {
					$html_content = '<a class="button fix-comments" data-post="'.$post_id.'" href="'.admin_url("post.php?post=".$post_id."&action=edit").'">'.esc_html__("Fix the answers count","wpqa").'</a>';
					$options[] = array(
						'name' => $html_content,
						'type' => 'info'
					);
				}
			}
			
			if ($post_id > 0 && wpqa_is_post_type(array(wpqa_questions_type))) {
				$question_html = '<div class="custom-meta-field">';
					$asks = get_post_meta($post_id,"ask",true);
					$wpqa_poll = get_post_meta($post_id,"wpqa_poll",true);
					if ($question_poll != "" && $question_poll == "on") {
						if (isset($wpqa_poll) && is_array($wpqa_poll)) {
							$i = 0;
							$question_html .= '<div class="custom-meta-label"><label>'.esc_html__('Stats of Users','wpqa').'</label></div><div class="clear"></div><br>';
							foreach ($wpqa_poll as $wpqa_polls):$i++;
								$question_html .= (isset($asks[$wpqa_polls['id']]['title']) && $asks[$wpqa_polls['id']]['title'] != ''?esc_html( $asks[$wpqa_polls['id']]['title'] ).' --- ':'').(isset($wpqa_polls['value']) && $wpqa_polls['value'] != 0?stripslashes( $wpqa_polls['value'] ):0)." Votes <br>";
								if (isset($wpqa_polls['user_ids']) && is_array($wpqa_polls['user_ids'])) {
									foreach ($wpqa_polls['user_ids'] as $key => $value) {
										if ($value > 0) {
											$user_name = get_the_author_meta("display_name",$value);
											if (isset($user_name) && $user_name != "") {
												$question_html .= '<div class="vpanel_checkbox_input"><p class="description">'.$user_name.' '.esc_html__('Has vote for','wpqa').' '.(isset($asks[$wpqa_polls['id']]['title']) && $asks[$wpqa_polls['id']]['title'] != ''?esc_html( $asks[$wpqa_polls['id']]['title'] ):'').'</p></div>';
											}
										}else {
											$question_html .= '<div class="vpanel_checkbox_input"><p class="description">'.esc_html__('Unregistered user has vote for','wpqa').' '.(isset($asks[$wpqa_polls['id']]['title']) && $asks[$wpqa_polls['id']]['title'] != ''?esc_html( $asks[$wpqa_polls['id']]['title'] ):'').'</p></div>';
										}
									}
									$question_html .= '<br>';
								}
							endforeach;
						}
					}
				$question_html .= '</div>';
				
				$options = apply_filters(wpqa_prefix_theme.'_options_before_question_poll',$options);

				$pay_answer = wpqa_options("pay_answer");
				if ($pay_answer == "on") {
					$payment_type_answer = wpqa_options("payment_type_answer");
					$multi_currencies = wpqa_options("multi_currencies");
					$options[] = array(
						'name' => esc_html__('Do you need to activate the custom option for this question to allow the user pay to answer?','wpqa'),
						'id'   => 'custom_pay_answer',
						'type' => 'checkbox'
					);

					$options[] = array(
						'type'      => 'heading-2',
						'condition' => 'custom_pay_answer:not(0)',
						'div'       => 'div'
					);

					$options[] = array(
						'name' => esc_html__('Do you need to activate the pay to answer in this question?','wpqa'),
						'id'   => 'pay_answer',
						'std'  => 'on',
						'type' => 'checkbox'
					);

					$options[] = array(
						'type'      => 'heading-2',
						'condition' => 'pay_answer:not(0)',
						'div'       => 'div'
					);

					if ($payment_type_answer != "points" && $activate_currencies != "on") {
						$options[] = array(
							"name" => esc_html__("What's the price to add a new answer?","wpqa"),
							"desc" => esc_html__("Type here price to add a new answer","wpqa"),
							"id"   => "pay_answer_payment",
							"type" => "text",
							'std'  => 10
						);
					}

					if ($activate_currencies == "on" && is_array($multi_currencies) && !empty($multi_currencies)) {
						$options[] = array(
							'name' => esc_html__("What's the price to add a new answer?","wpqa"),
							'type' => 'info'
						);
						foreach ($multi_currencies as $key_currency => $value_currency) {
							if ($value_currency != "0") {
								$options[] = array(
									"name" => esc_html__("Price for","wpqa")." ".$value_currency,
									"id"   => "pay_answer_payment_".strtolower($value_currency),
									"type" => "text",
									'std'  => 10
								);
							}
						}
					}

					if ($payment_type_answer == "points" || $payment_type_answer == "payments_points") {
						$options[] = array(
							"name" => esc_html__("How many points to add a new answer?","wpqa"),
							"desc" => esc_html__("Type here points of the payment to add a new answer","wpqa"),
							"id"   => "answer_payment_points",
							"type" => "text",
							'std'  => 20
						);
					}

					$options[] = array(
						'type' => 'heading-2',
						'end'  => 'end',
						'div'  => 'div'
					);

					$options[] = array(
						'type' => 'heading-2',
						'end'  => 'end',
						'div'  => 'div'
					);
				}

				$options[] = array(
					'name' => esc_html__("Question settings","wpqa"),
					'type' => 'info'
				);

				$options[] = array(
					'name' => esc_html__('Is this question is a poll?','wpqa'),
					'id'   => 'question_poll',
					'type' => 'checkbox'
				);

				$options = apply_filters(wpqa_prefix_theme.'_options_after_question_poll',$options);

				$options[] = array(
					'type'      => 'heading-2',
					'condition' => 'question_poll:not(0),question_poll:not(2)',
					'div'       => 'div'
				);
				
				$options[] = array(
					'name' => esc_html__('Poll title','wpqa'),
					'desc' => esc_html__('Put here the poll title if you need to add custom title.','wpqa'),
					'id'   => prefix_meta."question_poll_title",
					'type' => 'text',
				);

				$question_poll_array = array(
					array(
						"type" => "text",
						"id"   => "title",
						"name" => esc_html__('Title','wpqa'),
					),
					array(
						"type" => "hidden_id",
						"id"   => "id"
					),
				);
				
				$poll_image = wpqa_options("poll_image");
				$question_poll_image = array();
				if ($poll_image == "on") {
					$question_poll_image = array(
						array(
							"type" => "upload",
							"id"   => "image",
							"name" => esc_html__('Image','wpqa'),
						)
					);
				}
				
				$options[] = array(
					'id'      => "ask",
					'type'    => "elements",
					'button'  => esc_html__('Add a new option to poll','wpqa'),
					'hide'    => "yes",
					'options' => array_merge($question_poll_array,$question_poll_image)
				);
				
				$options[] = array(
					'type'    => 'content',
					'content' => $question_html,
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'end'  => 'end',
					'div'  => 'div'
				);
			}
			
			if (wpqa_is_post_type(array(wpqa_questions_type))) {
				$hide_question_categories = apply_filters(wpqa_prefix_theme.'_hide_question_categories',false);
				$category_single_multi = wpqa_options("category_single_multi");
				if ($hide_question_categories == false && $category_single_multi != "multi") {
					$options[] = array(
						'name'        => esc_html__('Choose from here the question category.','wpqa'),
						'id'          => prefix_meta.'question_category',
						'option_none' => esc_html__('Select a Category','wpqa'),
						'type'        => 'select_category',
						'taxonomy'    => wpqa_question_categories,
						'selected'    => 's_f_category'
					);
				}
				
				$options[] = array(
					'name' => esc_html__('Video description','wpqa'),
					'desc' => esc_html__('Add a Video to describe the problem better.','wpqa'),
					'id'   => 'video_description',
					'type' => 'checkbox',
				);
				
				$options[] = array(
					'name'      => esc_html__('Video type','wpqa'),
					'id'        => 'video_type',
					'type'      => 'select',
					'options'   => array(
						'youtube'  => esc_html__("Youtube","wpqa"),
						'vimeo'    => esc_html__("Vimeo","wpqa"),
						'daily'    => esc_html__("Dailymotion","wpqa"),
						'facebook' => esc_html__("Facebook","wpqa"),
						'tiktok'   => esc_html__("TiTtok","wpqa"),
					),
					'std'       => 'youtube',
					'condition' => 'video_description:not(0)',
					'desc'      => esc_html__('Choose from here the video type.','wpqa'),
				);
				
				$options[] = array(
					'name'      => esc_html__('Video ID','wpqa'),
					'desc'      => esc_html__('Put the Video ID here: https://www.youtube.com/watch?v=sdUUx5FdySs Ex: "sdUUx5FdySs".','wpqa'),
					'id'        => "video_id",
					'condition' => 'video_description:not(0)',
					'type'      => 'text',
				);
				
				$ask_question_items = wpqa_options("ask_question_items");
				if (isset($ask_question_items["featured_image"]["value"]) && $ask_question_items["featured_image"]["value"] == "featured_image") {
					$options[] = array(
						'name' => esc_html__('Custom featured image size','wpqa'),
						'desc' => esc_html__('Select ON to set the custom featured image size.','wpqa'),
						'id'   => prefix_meta.'custom_featured_image_size',
						'type' => 'checkbox'
					);
					
					$options[] = array(
						'type'      => 'heading-2',
						'condition' => prefix_meta.'custom_featured_image_size:not(0)',
						'div'       => 'div'
					);
					
					$options[] = array(
						"name" => esc_html__("Featured image width","wpqa"),
						"id"   => prefix_meta."featured_image_width",
						"type" => "sliderui",
						"std"  => "260",
						"step" => "1",
						"min"  => "50",
						"max"  => "600"
					);
					
					$options[] = array(
						"name" => esc_html__("Featured image height","wpqa"),
						"id"   => prefix_meta."featured_image_height",
						"type" => "sliderui",
						"std"  => "185",
						"step" => "1",
						"min"  => "50",
						"max"  => "600"
					);
					
					$options[] = array(
						'type' => 'heading-2',
						'end'  => 'end',
						'div'  => 'div'
					);
				}
			}
			
			$options[] = array(
				'name' => esc_html__('Notification by e-mail','wpqa'),
				'desc' => esc_html__('Get notified by email when someone answers this question','wpqa'),
				'id'   => 'remember_answer',
				'type' => 'checkbox',
			);
			
			$options[] = array(
				'name' => esc_html__('Private question?','wpqa'),
				'desc' => esc_html__('This question is a private question?','wpqa'),
				'id'   => 'private_question',
				'type' => 'checkbox',
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);

			$options = apply_filters(wpqa_prefix_theme.'_options_before_answer_tabs',$options);

			$options[] = array(
				'type' => 'heading-2',
				'name' => esc_html__('Custom answer tabs','wpqa')
			);
			
			$options[] = array(
				'name' => esc_html__('Choose a custom setting for the answer tabs','wpqa'),
				'id'   => prefix_meta.'custom_answer_tabs',
				'type' => 'checkbox'
			);

			$answers_tabs = array(
				"votes"  => array("sort" => esc_html__('Voted','wpqa'),"value" => "votes"),
				"oldest" => array("sort" => esc_html__('Oldest','wpqa'),"value" => "oldest"),
				"recent" => array("sort" => esc_html__('Recent','wpqa'),"value" => "recent"),
				"random" => array("sort" => esc_html__('Random','wpqa'),"value" => ""),
			);

			if (has_himer() || has_knowly() || has_questy()) {
				$answers_tabs["reacted"] = array("sort" => esc_html__('Reacted','wpqa'),"value" => "");
			}
			
			$options[] = array(
				'name'      => esc_html__('Tabs at the answers','wpqa'),
				'desc'      => esc_html__('Select the tabs at the answers on the question page.','wpqa'),
				'id'        => prefix_meta.'answers_tabs',
				'type'      => 'multicheck',
				'sort'      => 'yes',
				'std'       => $answers_tabs,
				'options'   => $answers_tabs,
				'condition' => prefix_meta.'custom_answer_tabs:not(0)'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
		}
		
		if (wpqa_is_post_type(array(wpqa_knowledgebase_type))) {
			$options[] = array(
				'name' => esc_html__('Knowledgebase settings','wpqa'),
				'id'   => 'knowledgebase_settings',
				'icon' => 'buddicons-forums',
				'type' => 'heading'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Private knowledgebase?','wpqa'),
				'desc' => esc_html__('This knowledgebase is a private knowledgebase?','wpqa'),
				'id'   => 'private_knowledgebase',
				'type' => 'checkbox',
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
		}
		
		if (wpqa_is_post_type(array("post","page",wpqa_questions_type,wpqa_asked_questions_type,wpqa_knowledgebase_type,"group"))) {
			if (wpqa_is_post_type(array("post","page",wpqa_questions_type,wpqa_asked_questions_type,wpqa_knowledgebase_type))) {
				$options[] = array(
					'name' => esc_html__('Call to action','wpqa'),
					'id'   => 'call_to_action',
					'icon' => 'welcome-widgets-menus',
					'type' => 'heading'
				);
				
				$options[] = array(
					'type' => 'heading-2'
				);

				$options[] = array(
					'name' => esc_html__('Custom call to action','wpqa'),
					'id'   => prefix_meta.'custom_call_action',
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'div'       => 'div',
					'type'      => 'heading-2',
					'condition' => prefix_meta.'custom_call_action:not(0)'
				);

				$options[] = array(
					'name' => esc_html__('Activate the call to action','wpqa'),
					'desc' => esc_html__('Select ON to enable the Call to action.','wpqa'),
					'id'   => prefix_meta.'call_action',
					'type' => 'checkbox',
					'std'  => (has_discy('on')?'on':0)
				);
				
				$options[] = array(
					'div'       => 'div',
					'type'      => 'heading-2',
					'condition' => prefix_meta.'call_action:not(0)'
				);
				
				$options[] = array(
					'name'    => esc_html__('Action skin','wpqa'),
					'desc'    => esc_html__('Choose the action skin.','wpqa'),
					'id'      => prefix_meta.'action_skin',
					'std'     => 'dark',
					'type'    => 'radio',
					'options' => array("light" => esc_html__("Light","wpqa"),"dark" => esc_html__("Dark","wpqa"),"colored" => esc_html__("Colored","wpqa"))
				);
				
				$options[] = array(
					'name'    => esc_html__('Action style','wpqa'),
					'desc'    => esc_html__('Choose action style from here.','wpqa'),
					'id'      => prefix_meta.'action_style',
					'options' => array(
						'style_1'  => 'Style 1',
						'style_2'  => 'Style 2',
					),
					'std'     => 'style_1',
					'type'    => 'radio'
				);
				
				$options[] = array(
					'name'    => esc_html__('Action image or video','wpqa'),
					'id'      => prefix_meta.'action_image_video',
					'options' => array(
						'image' => esc_html__('Image','wpqa'),
						'video' => esc_html__('Video','wpqa'),
					),
					'std'     => 'image',
					'type'    => 'radio'
				);
				
				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'action_image_video:not(video)',
					'type'      => 'heading-2'
				);
				
				$options[] = array(
					'name'    => esc_html__('Upload the background','wpqa'),
					'id'      => prefix_meta.'action_background',
					'type'    => 'background',
					'options' => array('color' => '','image' => ''),
					'std'     => array(
						'image' => $imagepath_theme."action.png"
					)
				);
				
				$options[] = array(
					"name" => esc_html__('Choose the background opacity','wpqa'),
					"desc" => esc_html__('Choose the background opacity from here','wpqa'),
					"id"   => prefix_meta."action_opacity",
					"type" => "sliderui",
					'std'  => 50,
					"step" => "5",
					"min"  => "0",
					"max"  => "100"
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);
				
				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'action_image_video:is(video)',
					'type'      => 'heading-2'
				);

				$options[] = array(
					'name'    => esc_html__('Video type','wpqa'),
					'id'      => prefix_meta.'action_video_type',
					'type'    => 'select',
					'options' => array(
						'youtube'  => esc_html__("Youtube","wpqa"),
						'vimeo'    => esc_html__("Vimeo","wpqa"),
						'daily'    => esc_html__("Dailymotion","wpqa"),
						'facebook' => esc_html__("Facebook video","wpqa"),
						'tiktok'   => esc_html__("TikTok video","wpqa"),
						'html5'    => esc_html__("HTML 5","wpqa"),
						'embed'    => esc_html__("Custom embed","wpqa"),
					),
					'std'     => 'youtube',
					'desc'    => esc_html__('Choose from here the video type','wpqa'),
				);
				
				$options[] = array(
					'name'      => esc_html__('Custom embed','wpqa'),
					'desc'      => esc_html__('Put your Custom embed html','wpqa'),
					'id'        => prefix_meta."action_custom_embed",
					'type'      => 'textarea',
					'cols'      => "40",
					'rows'      => "8",
					'condition' => prefix_meta.'action_video_type:is(embed)'
				);
				
				$options[] = array(
					'name'      => esc_html__('Video ID','wpqa'),
					'id'        => prefix_meta.'action_video_id',
					'desc'      => esc_html__('Put the Video ID here: https://www.youtube.com/watch?v=JuyB7NO0EYY Ex: "JuyB7NO0EYY"','wpqa'),
					'type'      => 'text',
					'operator'  => 'or',
					'condition' => prefix_meta.'action_video_type:is(youtube),'.prefix_meta.'action_video_type:is(vimeo),'.prefix_meta.'action_video_type:is(daily),'.prefix_meta.'action_video_type:is(facebook),'.prefix_meta.'action_video_type:is(tiktok)'
				);
				
				$options[] = array(
					'name'      => esc_html__('Mp4 video','wpqa'),
					'id'        => prefix_meta.'action_video_mp4',
					'desc'      => esc_html__('Put mp4 video here','wpqa'),
					'type'      => 'text',
					'condition' => prefix_meta.'action_video_type:is(html5)'
				);
				
				$options[] = array(
					'name'      => esc_html__('M4v video','wpqa'),
					'id'        => prefix_meta.'action_video_m4v',
					'desc'      => esc_html__('Put m4v video here','wpqa'),
					'type'      => 'text',
					'condition' => prefix_meta.'action_video_type:is(html5)'
				);
				
				$options[] = array(
					'name'      => esc_html__('Webm video','wpqa'),
					'id'        => prefix_meta.'action_video_webm',
					'desc'      => esc_html__('Put webm video here','wpqa'),
					'type'      => 'text',
					'condition' => prefix_meta.'action_video_type:is(html5)'
				);
				
				$options[] = array(
					'name'      => esc_html__('Ogv video','wpqa'),
					'id'        => prefix_meta.'action_video_ogv',
					'desc'      => esc_html__('Put ogv video here','wpqa'),
					'type'      => 'text',
					'condition' => prefix_meta.'action_video_type:is(html5)'
				);
				
				$options[] = array(
					'name'      => esc_html__('Wmv video','wpqa'),
					'id'        => prefix_meta.'action_video_wmv',
					'desc'      => esc_html__('Put wmv video here','wpqa'),
					'type'      => 'text',
					'condition' => prefix_meta.'action_video_type:is(html5)'
				);
				
				$options[] = array(
					'name'      => esc_html__('Flv video','wpqa'),
					'id'        => prefix_meta.'action_video_flv',
					'desc'      => esc_html__('Put flv video here','wpqa'),
					'type'      => 'text',
					'condition' => prefix_meta.'action_video_type:is(html5)'
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);
				
				$options[] = array(
					'name' => esc_html__('The headline','wpqa'),
					'desc' => esc_html__('Type the Headline from here','wpqa'),
					'id'   => prefix_meta.'action_headline',
					'type' => 'text',
					'std'  => "Share & grow the world's knowledge!"
				);
				
				$options[] = array(
					'name'     => esc_html__('The paragraph','wpqa'),
					'desc'     => esc_html__('Type the Paragraph from here','wpqa'),
					'id'       => prefix_meta.'action_paragraph',
					'type'     => apply_filters(wpqa_prefix_theme.'_action_paragraph','textarea'),
					'std'      => 'We want to connect the people who have knowledge to the people who need it, to bring together people with different perspectives so they can understand each other better, and to empower everyone to share their knowledge.'
				);
				
				$options[] = array(
					'name'    => esc_html__('Action button','wpqa'),
					'desc'    => esc_html__('Choose Action button style from here.','wpqa'),
					'id'      => prefix_meta.'action_button',
					'options' => array(
						'signup'   => esc_html__('Create A New Account','wpqa'),
						'login'    => esc_html__('Login','wpqa'),
						'question' => esc_html__('Ask A Question','wpqa'),
						'poll'     => esc_html__('Add A Poll','wpqa'),
						'post'     => esc_html__('Add A Post','wpqa'),
						'custom'   => esc_html__('Custom link','wpqa'),
					),
					'std'     => 'signup',
					'type'    => 'radio'
				);
				
				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'action_button:is(custom)',
					'type'      => 'heading-2'
				);
				
				$options[] = array(
					'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
					'id'      => prefix_meta.'action_button_target',
					'std'     => "new_page",
					'type'    => 'select',
					'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
				);
				
				$options[] = array(
					'name' => esc_html__('Type the button link','wpqa'),
					'id'   => prefix_meta.'action_button_link',
					'type' => 'text'
				);
				
				$options[] = array(
					'name' => esc_html__('Type the button text','wpqa'),
					'id'   => prefix_meta.'action_button_text',
					'type' => 'text'
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);
				
				$options[] = array(
					'name'    => esc_html__('The call to action works for "Unlogged users", "Logged users" or both','wpqa'),
					'desc'    => esc_html__('Choose the call to action works for "Unlogged users", "Logged users" or both.','wpqa'),
					'id'      => prefix_meta.'action_logged',
					'options' => array(
						'unlogged' => esc_html__('Unlogged users','wpqa'),
						'logged'   => esc_html__('Logged users','wpqa'),
						'both'     => esc_html__('Both','wpqa'),
					),
					'std'     => 'unlogged',
					'type'    => 'radio',
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);

				$options[] = array(
					'type' => 'heading-2',
					'end'  => 'end'
				);

				$options[] = array(
					'name' => esc_html__('Popup Notification','wpqa'),
					'id'   => 'popup_notification',
					'icon' => 'lightbulb',
					'type' => 'heading'
				);
				
				$options[] = array(
					'type' => 'heading-2'
				);

				$options[] = array(
					'name' => esc_html__('Custom popup notification','wpqa'),
					'id'   => prefix_meta.'custom_popup_notification',
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'div'       => 'div',
					'type'      => 'heading-2',
					'condition' => prefix_meta.'custom_popup_notification:not(0)'
				);
				
				$options[] = array(
					'name' => esc_html__('Note: the last popup notification only will show not all the popup notifications.','wpqa'),
					'type' => 'info'
				);

				$options[] = array(
					'name'    => esc_html__('Show the popup notification for the user one time only or forever','wpqa'),
					'id'      => prefix_meta.'popup_notification_time',
					'options' => array(
						'one_time' => esc_html__('One time','wpqa'),
						'for_ever' => esc_html__('Forever','wpqa'),
					),
					'std'     => 'one_time',
					'type'    => 'radio'
				);

				$options[] = array(
					'name'    => esc_html__('Choose the roles or users for the popup notification','wpqa'),
					'desc'    => esc_html__('Choose from here which roles or users you want to send the popup notification.','wpqa'),
					'id'      => prefix_meta.'popup_notification_groups_users',
					'options' => array(
						'groups' => esc_html__('Roles','wpqa'),
						'users'  => esc_html__('Users','wpqa'),
					),
					'std'     => 'groups',
					'type'    => 'radio'
				);

				global $wp_roles;
				$new_roles = array();
				foreach ($wp_roles->roles as $key => $value) {
					$new_roles[$key] = $value['name'];
				}

				$options[] = array(
					'name'      => esc_html__("Choose the roles you need to send the popup notification.","wpqa"),
					'id'        => prefix_meta.'popup_notification_groups',
					'condition' => prefix_meta.'popup_notification_groups_users:not(users)',
					'type'      => 'multicheck',
					'options'   => $new_roles,
					'std'       => array('administrator' => 'administrator','editor' => 'editor','contributor' => 'contributor','subscriber' => 'subscriber','author' => 'author'),
				);

				$options[] = array(
					'name'      => esc_html__('Specific user ids','wpqa'),
					'id'        => prefix_meta.'popup_notification_specific_users',
					'condition' => prefix_meta.'popup_notification_groups_users:is(users)',
					'type'      => 'text'
				);
				
				$options[] = array(
					'name'     => esc_html__('Popup notification text','wpqa'),
					'id'       => prefix_meta.'popup_notification_text',
					'type'     => 'editor',
					'settings' => array("media_buttons" => true,"textarea_rows" => 10)
				);

				$options[] = array(
					'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
					'id'      => prefix_meta.'popup_notification_button_target',
					'std'     => "new_page",
					'type'    => 'select',
					'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
				);
				
				$options[] = array(
					'name' => esc_html__('Type the button link','wpqa'),
					'id'   => prefix_meta.'popup_notification_button_url',
					'type' => 'text'
				);
				
				$options[] = array(
					'name' => esc_html__('Type the button text','wpqa'),
					'id'   => prefix_meta.'popup_notification_button_text',
					'type' => 'text'
				);
				
				$options[] = array(
					'name' => esc_html__('You must save your options before sending the popup notification.','wpqa'),
					'type' => 'info'
				);

				$html_content = '<a href="#" class="button button-primary send-popup-notification" data-post="'.$post_id.'">'.esc_html__('Send the popup notification','wpqa').'</a>';
				
				$options[] = array(
					'name' => $html_content,
					'type' => 'info'
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);

				$options[] = array(
					'type' => 'heading-2',
					'end'  => 'end'
				);

				$options[] = array(
					'name' => esc_html__('Slider settings','wpqa'),
					'id'   => 'sliders',
					'icon' => 'images-alt2',
					'type' => 'heading'
				);
				
				$options[] = array(
					'type' => 'heading-2'
				);

				$options[] = array(
					'name' => esc_html__('Custom slider','wpqa'),
					'id'   => prefix_meta.'custom_sliders',
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'div'       => 'div',
					'type'      => 'heading-2',
					'condition' => prefix_meta.'custom_sliders:not(0)'
				);

				$options[] = array(
					'name' => esc_html__('Activate the slider or not','wpqa'),
					'desc' => esc_html__('Select ON to enable the posts area.','wpqa'),
					'id'   => prefix_meta.'slider_h',
					'type' => 'checkbox',
					'std'  => 'on'
				);
				
				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'slider_h:not(0)',
					'type'      => 'heading-2'
				);

				$options[] = array(
					'name'    => esc_html__('Slider works for "Unlogged users", "Logged users" or both','wpqa'),
					'id'      => prefix_meta.'slider_h_logged',
					'options' => array(
						'unlogged' => esc_html__('Unlogged users','wpqa'),
						'logged'   => esc_html__('Logged users','wpqa'),
						'both'     => esc_html__('Both','wpqa'),
					),
					'std'     => 'both',
					'type'    => 'radio',
				);

				$options[] = array(
					'name'    => esc_html__('Choose the slider that works with the theme or add your custom slider by inserting the code or shortcodes','wpqa'),
					'id'      => prefix_meta.'custom_slider',
					'options' => array(
						'slider' => esc_html__('Theme slider','wpqa'),
						'custom' => esc_html__('Custom slider','wpqa'),
					),
					'std'     => 'slider',
					'type'    => 'radio',
				);

				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'custom_slider:is(slider)',
					'type'      => 'heading-2'
				);

				$options[] = array(
					'name' => esc_html__('Slider height','wpqa'),
					"id"   => prefix_meta."slider_height",
					"type" => "sliderui",
					"step" => "50",
					"min"  => "400",
					"max"  => "1000",
					"std"  => "500"
				);

				$slide_elements = array(
					array(
						"type" => "color",
						"id"   => "color",
						"name" => esc_html__('Color','wpqa')
					),
					array(
						"type" => "upload",
						"id"   => "image",
						"name" => esc_html__('Image','wpqa')
					),
					array(
						"type"  => "slider",
						"name"  => esc_html__('Choose the background opacity','wpqa'),
						"id"    => "opacity",
						"std"   => "0",
						"step"  => "1",
						"min"   => "0",
						"max"   => "100",
						"value" => "0"
					),
					array(
						"type"    => "radio",
						"id"      => "align",
						"name"    => esc_html__('Align','wpqa'),
						'options' => array(
							'left'   => esc_html__('Left','wpqa'),
							'center' => esc_html__('Center','wpqa'),
							'right'  => esc_html__('Right','wpqa'),
						),
						'std'     => 'left',
					),
					array(
						"type"      => "radio",
						"id"        => "login",
						"name"      => esc_html__('Login or Signup','wpqa'),
						'options'   => array(
							'none'   => esc_html__('None','wpqa'),
							'login'  => esc_html__('Login','wpqa'),
							'signup' => esc_html__('Signup','wpqa'),
						),
						'condition' => '[%id%]align:not(center),[%id%]button_block:not(block)',
						'std'       => 'login',
					),
					array(
						"type" => "text",
						"id"   => "title",
						"name" => esc_html__('Title','wpqa')
					),
					array(
						"type" => "text",
						"id"   => "title_2",
						"name" => esc_html__('Second title','wpqa')
					),
					array(
						"type" => "textarea",
						"id"   => "paragraph",
						"name" => esc_html__('Paragraph','wpqa')
					),
					array(
						"type"    => "radio",
						"id"      => "button_block",
						"name"    => esc_html__('Button or Block','wpqa'),
						'options' => array(
							'none'   => esc_html__('None','wpqa'),
							'button' => esc_html__('button','wpqa'),
							'block'  => esc_html__('Block','wpqa'),
						),
						'std'     => 'none',
					),
					array(
						"type"      => "radio",
						"id"        => "block",
						"name"      => esc_html__('Block','wpqa'),
						'options'   => array(
							'search'   => esc_html__('Search','wpqa'),
							'question' => esc_html__('Ask A Question','wpqa'),
						),
						'condition' => '[%id%]button_block:is(block)',
						'std'       => 'search',
					),
					array(
						"type"      => "radio",
						"id"        => "button",
						"name"      => esc_html__('Button','wpqa'),
						'options'   => array(
							'signup'   => esc_html__('Create A New Account','wpqa'),
							'login'    => esc_html__('Login','wpqa'),
							'question' => esc_html__('Ask A Question','wpqa'),
							'poll'     => esc_html__('Add A Poll','wpqa'),
							'post'     => esc_html__('Add A Post','wpqa'),
							'custom'   => esc_html__('Custom link','wpqa'),
						),
						'condition' => '[%id%]button_block:is(button)',
						'std'       => 'signup',
					),
					array(
						"type"      => "radio",
						"id"        => "button_style",
						"name"      => esc_html__('Button style','wpqa'),
						'options'   => array(
							'style_1' => esc_html__('Style 1','wpqa'),
							'style_2' => esc_html__('Style 2','wpqa'),
							'style_3' => esc_html__('Style 3','wpqa'),
						),
						'condition' => '[%id%]button_block:is(button)',
						'std'       => 'style_1',
					),
					array(
						'div'       => 'div',
						'condition' => '[%id%]button:is(custom),[%id%]button_block:is(button)',
						'type'      => 'heading-2'
					),
					array(
						'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
						'id'      => 'button_target',
						'std'     => "new_page",
						'type'    => 'select',
						'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
					),
					array(
						'name' => esc_html__('Type the button link','wpqa'),
						'id'   => 'button_link',
						'type' => 'text'
					),
					array(
						'name' => esc_html__('Type the button text','wpqa'),
						'id'   => 'button_text',
						'type' => 'text'
					),
					array(
						'type' => 'heading-2',
						'div'  => 'div',
						'end'  => 'end'
					),
				);
				
				$options[] = array(
					'id'      => prefix_meta."add_slides",
					'type'    => "elements",
					'button'  => esc_html__('Add a new slide','wpqa'),
					'hide'    => "yes",
					'options' => $slide_elements,
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);
				
				$options[] = array(
					'id'        => prefix_meta."custom_slides",
					'type'      => "textarea",
					'name'      => esc_html__('Add your custom slide or shortcode','wpqa'),
					'condition' => prefix_meta.'custom_slider:is(custom)',
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);

				$options[] = array(
					'type' => 'heading-2',
					'end'  => 'end'
				);

				if (has_himer() || has_knowly()) {
					$options[] = array(
						'name' => esc_html__('Hero settings','wpqa'),
						'id'   => 'hero',
						'icon' => 'welcome-widgets-menus',
						'type' => 'heading'
					);
					
					$options[] = array(
						'type' => 'heading-2'
					);

					$options[] = array(
						'name' => esc_html__('Custom hero','wpqa'),
						'id'   => prefix_meta.'custom_heros',
						'type' => 'checkbox'
					);
					
					$options[] = array(
						'div'       => 'div',
						'type'      => 'heading-2',
						'condition' => prefix_meta.'custom_heros:not(0)'
					);

					$options[] = array(
						'name' => esc_html__('Activate the hero or not','wpqa'),
						'desc' => esc_html__('Select ON to enable the posts area.','wpqa'),
						'id'   => prefix_meta.'hero_h',
						'type' => 'checkbox',
						'std'  => 'on'
					);
					
					$options[] = array(
						'div'       => 'div',
						'condition' => prefix_meta.'hero_h:not(0)',
						'type'      => 'heading-2'
					);
					
					$options[] = array(
						'name'    => esc_html__('Hero works for "Unlogged users", "Logged users" or both','wpqa'),
						'id'      => prefix_meta.'hero_h_logged',
						'options' => array(
							'unlogged' => esc_html__('Unlogged users','wpqa'),
							'logged'   => esc_html__('Logged users','wpqa'),
							'both'     => esc_html__('Both','wpqa'),
						),
						'std'     => 'both',
						'type'    => 'radio',
					);

					$options[] = array(
						'name'    => esc_html__('Hero layout','wpqa'),
						'id'      => prefix_meta."hero_layout",
						'std'     => "right",
						'type'    => "images",
						'options' => array(
							'right' => $imagepath.'hero-right.png',
							'full'  => $imagepath.'hero-full.png',
							'left'  => $imagepath.'hero-left.png',
						)
					);

					$options[] = array(
						'div'       => 'div',
						'condition' => prefix_meta.'hero_layout:not(full)',
						'type'      => 'heading-2'
					);
					
					$options[] = array(
						'name' => esc_html__('Sidebar of hero settings','wpqa'),
						'type' => 'info'
					);

					$options[] = array(
						'name'    => esc_html__('Sidebar style','wpqa'),
						'id'      => prefix_meta.'hero_sidebar',
						'options' => array(
							'stats'        => esc_html__("Stats","wpqa"),
							'small_banner' => esc_html__("Posts with small banner","wpqa"),
						),
						'std'     => 'stats',
						'type'    => 'radio'
					);

					$options[] = array(
						'div'       => 'div',
						'condition' => prefix_meta.'hero_sidebar:is(stats)',
						'type'      => 'heading-2'
					);

					$stats_array = array(
						"questions"    => array("sort" => esc_html__("Questions","wpqa"),"value" => "questions"),
						"answers"      => array("sort" => esc_html__("Answers","wpqa"),"value" => "answers"),
						"posts"        => array("sort" => esc_html__("Posts","wpqa"),"value" => "posts"),
						"comments"     => array("sort" => esc_html__("Comments","wpqa"),"value" => "comments"),
						"best_answers" => array("sort" => esc_html__("Best Answers","wpqa"),"value" => "best_answers"),
						"users"        => array("sort" => esc_html__("Users","wpqa"),"value" => "users"),
						"groups"       => array("sort" => esc_html__("Groups","wpqa"),"value" => "groups"),
						"group_posts"  => array("sort" => esc_html__("Group Posts","wpqa"),"value" => "group_posts"),
					);

					if ($activate_knowledgebase == true) {
						$stats_array["knowledgebases"] = array("sort" => esc_html__("Knowledgebases","wpqa"),"value" => "knowledgebases");
					}

					$stats_array = apply_filters(wpqa_prefix_theme."_widget_stats_array",$stats_array);

					$options[] = array(
						'name'    => esc_html__('Choose the stats','wpqa'),
						'id'      => prefix_meta.'hero_stats',
						'type'    => 'multicheck',
						'sort'    => 'yes',
						'std'     => $stats_array,
						'options' => $stats_array
					);

					$options[] = array(
						'name'    => esc_html__('Style','wpqa'),
						'id'      => prefix_meta.'hero_stats_style',
						'options' => array(
							'style_1' => 'Style 1',
							'style_2' => 'Style 2',
							'style_3' => 'Style 3',
						),
						'std'     => 'style_1',
						'type'    => 'radio'
					);
					
					$options[] = array(
						'type' => 'heading-2',
						'div'  => 'div',
						'end'  => 'end'
					);
					
					$options[] = array(
						'name' => esc_html__('Content of hero settings','wpqa'),
						'type' => 'info'
					);
					
					$options[] = array(
						'type' => 'heading-2',
						'div'  => 'div',
						'end'  => 'end'
					);

					$options[] = array(
						'name'      => esc_html__('Choose the hero that works with the theme or add your custom hero by inserting the code or shortcodes','wpqa'),
						'id'        => prefix_meta.'custom_hero',
						'options'   => array(
							'posts'  => esc_html__('Posts','wpqa'),
							'slider' => esc_html__('Theme Slider','wpqa'),
							'custom' => esc_html__('Custom Slider','wpqa'),
						),
						'std'     => 'posts',
						'type'    => 'radio',
					);

					$options[] = array(
						'name'      => esc_html__('Questions or posts','wpqa'),
						'id'        => prefix_meta.'hero_posts',
						'options'   => array(
							'questions' => esc_html__('Questions','wpqa'),
							'posts'     => esc_html__('Posts','wpqa')
						),
						'condition' => prefix_meta.'hero_sidebar:is(small_banner),'.prefix_meta.'custom_hero:is(posts)',
						'std'       => 'questions',
						'operator'  => 'or',
						'type'      => 'radio'
					);

					$options[] = array(
						'div'       => 'div',
						'condition' => prefix_meta.'custom_hero:is(slider)',
						'type'      => 'heading-2'
					);

					$options[] = array(
						'name' => esc_html__('Hero height','wpqa'),
						"id"   => prefix_meta."hero_height",
						"type" => "sliderui",
						"step" => "50",
						"min"  => "400",
						"max"  => "1000",
						"std"  => "500"
					);

					$slide_elements = array(
						array(
							"type" => "color",
							"id"   => "color",
							"name" => esc_html__('Color','wpqa')
						),
						array(
							"type" => "upload",
							"id"   => "image",
							"name" => esc_html__('Image','wpqa')
						),
						array(
							"type"  => "slider",
							"name"  => esc_html__('Choose the background opacity','wpqa'),
							"id"    => "opacity",
							"std"   => "0",
							"step"  => "1",
							"min"   => "0",
							"max"   => "100",
							"value" => "0"
						),
						array(
							"type" => "text",
							"id"   => "title",
							"name" => esc_html__('Title','wpqa')
						),
						array(
							"type" => "textarea",
							"id"   => "paragraph",
							"name" => esc_html__('Paragraph','wpqa')
						),
						array(
							"type" => "checkbox",
							"id"   => "button_active",
							"name" => esc_html__('Button enable or disable?','wpqa'),
							'std'  => 'on',
						),
						array(
							'div'       => 'div',
							'condition' => '[%id%]button_active:is(on)',
							'type'      => 'heading-2'
						),
						array(
							"type"    => "radio",
							"id"      => "button",
							"name"    => esc_html__('Button','wpqa'),
							'options' => array(
								'signup'   => esc_html__('Create A New Account','wpqa'),
								'login'    => esc_html__('Login','wpqa'),
								'question' => esc_html__('Ask A Question','wpqa'),
								'poll'     => esc_html__('Add A Poll','wpqa'),
								'post'     => esc_html__('Add A Post','wpqa'),
								'custom'   => esc_html__('Custom link','wpqa'),
							),
							'std'     => 'signup',
						),
						array(
							"type"    => "radio",
							"id"      => "button_style",
							"name"    => esc_html__('Button style','wpqa'),
							'options' => array(
								'style_3' => esc_html__('Style 1','wpqa'),
								'style_2' => esc_html__('Style 2','wpqa'),
								'style_1' => esc_html__('Style 3','wpqa'),
							),
							'std'     => 'style_3',
						),
						array(
							'div'       => 'div',
							'condition' => '[%id%]button:is(custom)',
							'type'      => 'heading-2'
						),
						array(
							'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
							'id'      => 'button_target',
							'std'     => "new_page",
							'type'    => 'select',
							'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
						),
						array(
							'name' => esc_html__('Type the button link','wpqa'),
							'id'   => 'button_link',
							'type' => 'text'
						),
						array(
							'name' => esc_html__('Type the button text','wpqa'),
							'id'   => 'button_text',
							'type' => 'text'
						),
						array(
							'type' => 'heading-2',
							'div'  => 'div',
							'end'  => 'end'
						),
						array(
							'type' => 'heading-2',
							'div'  => 'div',
							'end'  => 'end'
						),
					);
					
					$options[] = array(
						'id'      => prefix_meta."add_hero_slides",
						'type'    => "elements",
						'button'  => esc_html__('Add a new slide','wpqa'),
						'hide'    => "yes",
						'options' => $slide_elements,
					);
					
					$options[] = array(
						'type' => 'heading-2',
						'div'  => 'div',
						'end'  => 'end'
					);
					
					$options[] = array(
						'id'        => prefix_meta."custom_hero_slides",
						'type'      => "textarea",
						'name'      => esc_html__('Add your custom slide or shortcode','wpqa'),
						'condition' => prefix_meta.'custom_hero:is(custom)',
					);
					
					$options[] = array(
						'type' => 'heading-2',
						'div'  => 'div',
						'end'  => 'end'
					);

					$options[] = array(
						'div'       => 'div',
						'condition' => prefix_meta.'hero_posts:is(questions),'.prefix_meta.'custom_hero:is(posts),hero_layout:not(full)',
						'type'      => 'heading-2'
					);

					$options[] = array(
						'name' => esc_html__('Make the big questions banner with answers or not','wpqa'),
						'id'   => prefix_meta."hero_questions_answer",
						'std'  => "on",
						'type' => "checkbox",
					);
					
					$options[] = array(
						'name'      => esc_html__('Style of the answer','wpqa'),
						'desc'      => esc_html__('Choose the style of the answer.','wpqa'),
						'id'        => prefix_meta.'hero_questions_answer_style',
						'std'       => 'male_female',
						'type'      => 'radio',
						'condition' => prefix_meta.'hero_questions_answer:is(on)',
						'options'   => array(
							"male_female" => esc_html__("Male and female answers","wpqa"),
							"male"        => esc_html__("Male answer","wpqa"),
							"female"      => esc_html__("Female answer","wpqa"),
							"best_answer" => esc_html__("Best answer","wpqa")
						)
					);
					
					$options[] = array(
						'type' => 'heading-2',
						'div'  => 'div',
						'end'  => 'end'
					);

					$options[] = array(
						'div'       => 'div',
						'condition' => prefix_meta.'hero_layout:is(full),'.prefix_meta.'custom_hero:is(posts))',
						'operator'  => 'or',
						'type'      => 'heading-2'
					);
					
					$options[] = array(
						'name'    => esc_html__('Style of the posts','wpqa'),
						'desc'    => esc_html__('Choose the style of the posts.','wpqa'),
						'id'      => prefix_meta.'hero_posts_full',
						'std'     => 'banner_article',
						'type'    => 'radio',
						'options' => array(
							"banner_article" => esc_html__("Big banner and posts like article style","wpqa"),
							"big_banner"     => esc_html__("Big banner","wpqa"),
							"article_style"  => esc_html__("Like article style","wpqa"),
							"small_banner"   => esc_html__("Posts with small banner","wpqa")
						)
					);
					
					$options[] = array(
						'type' => 'heading-2',
						'div'  => 'div',
						'end'  => 'end'
					);

					$options[] = array(
						'div'       => 'div',
						'condition' => prefix_meta.'custom_hero:is(posts)',
						'operator'  => 'or',
						'type'      => 'heading-2'
					);
					
					$options[] = array(
						'name'      => esc_html__('Note: this option will not work if you choose stats in the sidebar of the hero and do not choose the slider of the posts.','wpqa'),
						'condition' => prefix_meta.'hero_sidebar:is(stats),'.prefix_meta.'hero_posts_slider:not(on)',
						'type'      => 'info'
					);

					$options[] = array(
						'name' => esc_html__('Add the number of posts you want to show','wpqa'),
						'id'   => prefix_meta."hero_posts_number",
						'type' => "text",
						'std'  => "3",
					);

					$options[] = array(
						'name' => esc_html__('Make the posts as slider','wpqa'),
						'id'   => prefix_meta."hero_posts_slider",
						'type' => "checkbox",
					);

					$options[] = array(
						'name'      => esc_html__('Show the meta of the post','wpqa'),
						'id'        => prefix_meta."hero_small_posts_meta",
						'type'      => "checkbox",
						'std'       => "on",
						'condition' => prefix_meta.'hero_posts_full:is(small_banner)',
					);
					
					$options[] = array(
						'type' => 'heading-2',
						'div'  => 'div',
						'end'  => 'end'
					);
					
					$options[] = array(
						'type' => 'heading-2',
						'div'  => 'div',
						'end'  => 'end'
					);

					$options[] = array(
						'type' => 'heading-2',
						'end'  => 'end'
					);
				}
			}

			$options = apply_filters(wpqa_prefix_theme.'_options_before_pages_options',$options);

			if (wpqa_is_post_type(array("group"))) {
				$options[] = array(
					'name' => esc_html__('Group Options','wpqa'),
					'id'   => 'group_settings',
					'icon' => 'groups',
					'type' => 'heading'
				);
				
				$options[] = array(
					'type' => 'heading-2'
				);

				$options[] = array(
					'name'    => esc_html__('Group privacy','wpqa'),
					'id'      => 'group_privacy',
					'std'     => 'public',
					'type'    => 'radio',
					'options' => 
						array(
							"public"  => esc_html__("Public group","wpqa"),
							"private" => esc_html__("Private group","wpqa")
						)
				);

				$options[] = array(
					'name'    => esc_html__('Group invitation','wpqa'),
					'id'      => 'group_invitation',
					'std'     => 'all',
					'type'    => 'radio',
					'options' => 
						array(
							"all"              => esc_html__("All group members","wpqa"),
							"admin_moderators" => esc_html__("Admin and moderators","wpqa"),
							"admin"            => esc_html__("Admin only","wpqa")
						)
				);

				$options[] = array(
					'name'    => esc_html__('Group posts','wpqa'),
					'id'      => 'group_allow_posts',
					'std'     => 'all',
					'type'    => 'radio',
					'options' => 
						array(
							"all"              => esc_html__("All group members","wpqa"),
							"admin_moderators" => esc_html__("Admin and moderators","wpqa"),
							"admin"            => esc_html__("Admin only","wpqa")
						)
				);

				$options[] = array(
					'name' => esc_html__('Activate comments in this group?','wpqa'),
					'desc' => esc_html__('Select ON to active the comments in this group','wpqa'),
					'id'   => 'group_comments',
					'std'  => 'on',
					'type' => 'checkbox'
				);

				$options[] = array(
					'name' => esc_html__('Upload the group photo, that represents this group','wpqa'),
					'id'   => "group_image",
					'type' => 'upload',
				);

				$options[] = array(
					'name' => esc_html__('Upload the group cover','wpqa'),
					'id'   => "group_cover",
					'type' => 'upload',
				);

				$options[] = array(
					'name'     => esc_html__('Group rules','wpqa'),
					'id'       => 'group_rules',
					'type'     => 'editor',
					'settings' => array("media_buttons" => true,"textarea_rows" => 10)
				);

				$options[] = array(
					'type' => 'heading-2',
					'end'  => 'end'
				);
			}

			$options[] = array(
				'name' => esc_html__('Pages Options','wpqa'),
				'id'   => 'page_settings',
				'icon' => 'admin-site',
				'type' => 'heading'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);

			$options[] = array(
				'name' => esc_html__('Activate this page for logged users?','wpqa'),
				'desc' => esc_html__('Select ON to activate this page for logged users only','wpqa'),
				'id'   => prefix_meta.'logged_only',
				'type' => 'checkbox'
			);
			
			if (wpqa_is_post_type(array("page"))) {
				$options[] = array(
					'name' => esc_html__('Activate this page for "Unlogged users", "Unconfirmed users" and "Unreviewed users"?','wpqa'),
					'desc' => esc_html__('Select ON to activate this page for "Unlogged users", "Unconfirmed users" and "Unreviewed users"','wpqa'),
					'id'   => prefix_meta.'login_only',
					'type' => 'checkbox'
				);

				$options[] = array(
					'name' => esc_html__('Activate this page for banned users?','wpqa'),
					'desc' => esc_html__('Select ON to activate this page for banned users','wpqa'),
					'id'   => prefix_meta.'banned_only',
					'type' => 'checkbox'
				);
			}

			$options = apply_filters(wpqa_prefix_theme.'_options_before_sidebar_layout',$options);
			
			$options_sidebar = array(
				'default'      => $imagepath.'sidebar_default.jpg',
				'menu_sidebar' => $imagepath.'menu_sidebar.jpg',
				'right'        => $imagepath.'sidebar_right.jpg',
				'full'         => $imagepath.'sidebar_no.jpg',
				'left'         => $imagepath.'sidebar_left.jpg',
				'centered'     => $imagepath.'centered.jpg',
				'menu_left'    => $imagepath.'menu_left.jpg',
			);
			
			$options[] = array(
				'name'    => esc_html__('Sidebar layout','wpqa'),
				'id'      => prefix_meta."sidebar",
				'std'     => "default",
				'type'    => "images",
				'options' => $options_sidebar
			);
			
			$options[] = array(
				'name'      => esc_html__('Select your sidebar','wpqa'),
				'id'        => prefix_meta.'what_sidebar',
				'options'   => $new_sidebars,
				'type'      => 'select',
				'condition' => prefix_meta.'sidebar:not(full),'.prefix_meta.'sidebar:not(centered),'.prefix_meta.'sidebar:not(menu_left)'
			);

			$options[] = array(
				'name'    => esc_html__('Main menu','wpqa'),
				'id'      => prefix_meta.'custom_main_menu',
				'std'     => 'default',
				'type'    => 'radio',
				'options' => 
					array(
						"default" => esc_html__("Default","wpqa"),
						"custom"  => esc_html__("Custom","wpqa")
					)
			);

			$menu_array = array(esc_html__('Select a menu:','wpqa'))+$menus;
			$options[] = array(
				'name'      => esc_html__('Select your main menu','wpqa'),
				'id'        => prefix_meta.'main_menu',
				'options'   => $menu_array,
				'type'      => 'select',
				'condition' => prefix_meta.'custom_main_menu:is(custom)'
			);

			$left_area = wpqa_options("left_area");
			if ($left_area == "sidebar") {
				$options[] = array(
					'name'      => esc_html__('Select your sidebar 2 if you activated it for the left menu','wpqa'),
					'id'        => prefix_meta.'what_sidebar_2',
					'options'   => $new_sidebars,
					'type'      => 'select',
					'condition' => prefix_meta.'sidebar:not(full),'.prefix_meta.'sidebar:not(centered),'.prefix_meta.'sidebar:not(right),'.prefix_meta.'sidebar:not(left)'
				);
			}else {
				$menu_array = array(esc_html__('Select a menu:','wpqa'))+$menus;
				$options[] = array(
					'name'      => esc_html__('Select your menu','wpqa'),
					'id'        => prefix_meta.'left_menu',
					'options'   => $menu_array,
					'type'      => 'select',
					'condition' => prefix_meta.'sidebar:not(full),'.prefix_meta.'sidebar:not(centered),'.prefix_meta.'sidebar:not(right),'.prefix_meta.'sidebar:not(left)'
				);
			}

			if (has_himer() || has_knowly()) {
				$options[] = array(
					'name'    => esc_html__('Second menu','wpqa'),
					'id'      => prefix_meta.'custom_second_menu',
					'std'     => 'default',
					'type'    => 'radio',
					'options' => 
						array(
							"default" => esc_html__("Default","wpqa"),
							"on"      => esc_html__("ON","wpqa"),
							"off"     => esc_html__("OFF","wpqa")
						)
				);

				$menu_array = array(esc_html__('Select a menu:','wpqa'))+$menus;
				$options[] = array(
					'name'    => esc_html__('Select your second menu','wpqa'),
					'id'      => prefix_meta.'second_menu',
					'options' => $menu_array,
					'type'    => 'select',
				);
			}

			$options[] = array(
				'name'    => esc_html__("Light/dark",'wpqa'),
				'desc'    => esc_html__("Light/dark for the page / post.",'wpqa'),
				'id'      => prefix_meta."post_skin_l",
				'std'     => "default",
				'type'    => "images",
				'options' => array(
					'default' => $imagepath.'sidebar_default.jpg',
					'light'   => $imagepath.'light.jpg',
					'dark'    => $imagepath.'dark.jpg'
				)
			);
			
			$options[] = array(
				'name'    => esc_html__('Choose Your Skin','wpqa'),
				'class'   => "site_skin",
				'id'      => prefix_meta."skin",
				'std'     => "default",
				'type'    => "images",
				'options' => array(
					'default'    => $imagepath.'default_color.jpg',
					'skin'       => $imagepath.'default.jpg',
					'violet'     => $imagepath.'violet.jpg',
					'bright_red' => $imagepath.'bright_red.jpg',
					'green'      => $imagepath.'green.jpg',
					'red'        => $imagepath.'red.jpg',
					'cyan'       => $imagepath.'cyan.jpg',
					'blue'       => $imagepath.'blue.jpg',
				)
			);
			
			$options[] = array(
				'name' => esc_html__('Primary Color','wpqa'),
				'id'   => prefix_meta."primary_color",
				'type' => 'color'
			);
			
			$options[] = array(
				'name'    => esc_html__('Background Type','wpqa'),
				'id'      => prefix_meta.'background_type',
				'std'     => 'default',
				'type'    => 'radio',
				'options' => 
					array(
						"default"           => esc_html__("Default","wpqa"),
						"none"              => esc_html__("None","wpqa"),
						"patterns"          => esc_html__("Patterns","wpqa"),
						"custom_background" => esc_html__("Custom Background","wpqa")
					)
			);
		
			$options[] = array(
				'name'      => esc_html__('Background Color','wpqa'),
				'id'        => prefix_meta."background_color",
				'type'      => 'color',
				'condition' => prefix_meta.'background_type:is(patterns)'
			);
				
			$options[] = array(
				'name'      => esc_html__('Choose Pattern','wpqa'),
				'id'        => prefix_meta."background_pattern",
				'std'       => "bg13",
				'type'      => "images",
				'condition' => prefix_meta.'background_type:is(patterns)',
				'class'     => "pattern_images",
				'options'   => array(
					'bg1'  => $imagepath.'bg1.jpg',
					'bg2'  => $imagepath.'bg2.jpg',
					'bg3'  => $imagepath.'bg3.jpg',
					'bg4'  => $imagepath.'bg4.jpg',
					'bg5'  => $imagepath.'bg5.jpg',
					'bg6'  => $imagepath.'bg6.jpg',
					'bg7'  => $imagepath.'bg7.jpg',
					'bg8'  => $imagepath.'bg8.jpg',
					'bg9'  => $imagepath_theme.'patterns/bg9.png',
					'bg10' => $imagepath_theme.'patterns/bg10.png',
					'bg11' => $imagepath_theme.'patterns/bg11.png',
					'bg12' => $imagepath_theme.'patterns/bg12.png',
					'bg13' => $imagepath.'bg13.jpg',
					'bg14' => $imagepath.'bg14.jpg',
					'bg15' => $imagepath_theme.'patterns/bg15.png',
					'bg16' => $imagepath_theme.'patterns/bg16.png',
					'bg17' => $imagepath.'bg17.jpg',
					'bg18' => $imagepath.'bg18.jpg',
					'bg19' => $imagepath.'bg19.jpg',
					'bg20' => $imagepath.'bg20.jpg',
					'bg21' => $imagepath_theme.'patterns/bg21.png',
					'bg22' => $imagepath.'bg22.jpg',
					'bg23' => $imagepath_theme.'patterns/bg23.png',
					'bg24' => $imagepath_theme.'patterns/bg24.png',
				)
			);
		
			$options[] = array(
				'name'      => esc_html__('Custom Background','wpqa'),
				'id'        => prefix_meta.'custom_background',
				'std'       => $background_defaults,
				'options'   => $background_defaults,
				'type'      => 'background',
				'condition' => prefix_meta.'background_type:is(custom_background)'
			);
				
			$options[] = array(
				'name'      => esc_html__('Full Screen Background','wpqa'),
				'desc'      => esc_html__('Select ON to enable Full Screen Background','wpqa'),
				'id'        => prefix_meta.'full_screen_background',
				'type'      => 'checkbox',
				'condition' => prefix_meta.'background_type:is(custom_background)'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
		}
		
		if (wpqa_is_post_type(array("post"))) {
			$options[] = array(
				'name' => esc_html__('Post head Options','wpqa'),
				'id'   => 'post_head_options',
				'icon' => 'schedule',
				'type' => 'heading'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);

			$options = apply_filters(wpqa_prefix_theme.'_meta_before_head_post',$options);

			if ($post_id > 0) {
				$html_content = '<a class="button fix-comments" data-post="'.$post_id.'" href="'.admin_url("post.php?post=".$post_id."&action=edit").'">'.esc_html__("Fix the comments count","wpqa").'</a>';
				$options[] = array(
					'name' => $html_content,
					'type' => 'info'
				);
			}
			
			$options[] = array(
				'name'    => esc_html__('Head post','wpqa'),
				'id'      => 'what_post',
				'type'    => 'select',
				'options' => array(
					'none'			 => esc_html__("None","wpqa"),
					'image'			 => esc_html__("Featured Image","wpqa"),
					'image_lightbox' => esc_html__("Image With Lightbox","wpqa"),
					'google'		 => esc_html__("Google Map","wpqa"),
					'slideshow'		 => esc_html__("Slideshow","wpqa"),
					'video'			 => esc_html__("Video","wpqa"),
					/*
					'quote'			 => esc_html__("Quote","wpqa"),
					'link'			 => esc_html__("Link","wpqa"),
					'twitter'		 => esc_html__("Twitter","wpqa"),
					'facebook'		 => esc_html__("Facebook","wpqa"),
					'instagram'		 => esc_html__("Instagram","wpqa"),
					*/
					'soundcloud'	 => esc_html__("Soundcloud","wpqa"),
					'audio'	         => esc_html__("Audio","wpqa"),
				),
				'std'     => 'image',
				'desc'    => esc_html__('Choose from here the post type','wpqa'),
			);
			
			$options[] = array(
				'type'      => 'heading-2',
				'operator'  => 'or',
				'condition' => 'what_post:is(image),what_post:is(image_lightbox)',
				'div'       => 'div'
			);
			
			$options[] = array(
				'name'    => esc_html__('Featured image style','wpqa'),
				'desc'    => esc_html__('Featured image style from here.','wpqa'),
				'id'      => prefix_meta.'featured_image_style',
				'std'     => 'default',
				'options' => array(
					'default'     => 'Default',
					'style_270'   => '270x180',
					'style_140'   => '140x140',
					'custom_size' => esc_html__('Custom size','wpqa'),
				),
				'type'    => 'radio'
			);
			
			$options[] = array(
				'type'      => 'heading-2',
				'condition' => prefix_meta.'featured_image_style:is(custom_size)',
				'div'       => 'div'
			);
			
			$options[] = array(
				"name"  => esc_html__('Featured image width','wpqa'),
				"id"    => prefix_meta."featured_image_width",
				'class' => 'width_50',
				"type"  => "sliderui",
				"step"  => "1",
				"min"   => "140",
				"max"   => "500"
			);
			
			$options[] = array(
				"name"  => esc_html__('Featured image height','wpqa'),
				"id"    => prefix_meta."featured_image_height",
				'class' => 'width_50',
				"type"  => "sliderui",
				"step"  => "1",
				"min"   => "140",
				"max"   => "500"
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end',
				'div'  => 'div'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end',
				'div'  => 'div'
			);
			
			$options[] = array(
				'name'      => esc_html__('Google map','wpqa'),
				'desc'      => esc_html__('Put your google map html','wpqa'),
				'id'        => prefix_meta."google",
				'type'      => 'textarea',
				'condition' => 'what_post:is(google)',
				'cols'      => "40",
				'rows'      => "8"
			);
			
			$options[] = array(
				'name'      => esc_html__('Audio URL MP3','wpqa'),
				'desc'      => esc_html__('Put your audio URL MP3','wpqa'),
				'id'        => prefix_meta."audio",
				'type'      => 'text',
				'condition' => 'what_post:is(audio)',
			);
			
			$options[] = array(
				'name'      => esc_html__('Slideshow ?','wpqa'),
				'id'        => prefix_meta.'slideshow_type',
				'type'      => 'select',
				'options'   => array(
					'custom_slide'  => esc_html__("Custom Slideshow","wpqa"),
					'upload_images' => esc_html__("Upload your images","wpqa"),
				),
				'std'       => 'custom_slide',
				'condition' => 'what_post:is(slideshow)'
			);
			
			$slide_elements = array(
				array(
					"type" => "upload",
					"id"   => "image_url",
					"name" => esc_html__('Image URL','wpqa')
				),
				array(
					"type" => "text",
					"id"   => "slide_link",
					"name" => esc_html__('Slide Link','wpqa')
				)
			);
			
			$options[] = array(
				'id'        => prefix_meta.'slideshow_post',
				'type'      => "elements",
				'hide'      => "yes",
				'button'    => esc_html__('Add a new slide','wpqa'),
				'options'   => $slide_elements,
				'condition' => 'what_post:is(slideshow),'.prefix_meta.'slideshow_type:is(custom_slide)',
			);
			
			$options[] = array(
				'name'      => esc_html__('Upload your images','wpqa'),
				'id'        => prefix_meta."upload_images",
				'type'      => 'upload_images',
				'condition' => 'what_post:is(slideshow),'.prefix_meta.'slideshow_type:is(upload_images)',
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => 'what_post:is(video)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Video type','wpqa'),
				'id'      => prefix_meta.'video_post_type',
				'type'    => 'select',
				'options' => array(
					'youtube'  => esc_html__("Youtube","wpqa"),
					'vimeo'    => esc_html__("Vimeo","wpqa"),
					'daily'    => esc_html__("Dailymotion","wpqa"),
					'facebook' => esc_html__("Facebook video","wpqa"),
					'tiktok'   => esc_html__("TikTok video","wpqa"),
					'html5'    => esc_html__("HTML 5","wpqa"),
					'embed'    => esc_html__("Custom embed","wpqa"),
				),
				'std'     => 'youtube',
				'desc'    => esc_html__('Choose from here the video type','wpqa'),
			);
			
			$options[] = array(
				'name'      => esc_html__('Custom embed','wpqa'),
				'desc'      => esc_html__('Put your Custom embed html','wpqa'),
				'id'        => prefix_meta."custom_embed",
				'type'      => 'textarea',
				'cols'      => "40",
				'rows'      => "8",
				'condition' => prefix_meta.'video_post_type:is(embed)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Video ID','wpqa'),
				'id'        => prefix_meta.'video_post_id',
				'desc'      => esc_html__('Put the Video ID here: https://www.youtube.com/watch?v=JuyB7NO0EYY Ex: "JuyB7NO0EYY"','wpqa'),
				'type'      => 'text',
				'operator'  => 'or',
				'condition' => prefix_meta.'video_post_type:is(youtube),'.prefix_meta.'video_post_type:is(vimeo),'.prefix_meta.'video_post_type:is(daily),'.prefix_meta.'video_post_type:is(facebook),'.prefix_meta.'video_post_type:is(tiktok)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Video Image','wpqa'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
				'id'        => prefix_meta.'video_image',
				'type'      => 'upload',
				'condition' => prefix_meta.'video_post_type:is(html5)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Mp4 video','wpqa'),
				'id'        => prefix_meta.'video_mp4',
				'desc'      => esc_html__('Put mp4 video here','wpqa'),
				'type'      => 'text',
				'condition' => prefix_meta.'video_post_type:is(html5)'
			);
			
			$options[] = array(
				'name'      => esc_html__('M4v video','wpqa'),
				'id'        => prefix_meta.'video_m4v',
				'desc'      => esc_html__('Put m4v video here','wpqa'),
				'type'      => 'text',
				'condition' => prefix_meta.'video_post_type:is(html5)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Webm video','wpqa'),
				'id'        => prefix_meta.'video_webm',
				'desc'      => esc_html__('Put webm video here','wpqa'),
				'type'      => 'text',
				'condition' => prefix_meta.'video_post_type:is(html5)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Ogv video','wpqa'),
				'id'        => prefix_meta.'video_ogv',
				'desc'      => esc_html__('Put ogv video here','wpqa'),
				'type'      => 'text',
				'condition' => prefix_meta.'video_post_type:is(html5)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Wmv video','wpqa'),
				'id'        => prefix_meta.'video_wmv',
				'desc'      => esc_html__('Put wmv video here','wpqa'),
				'type'      => 'text',
				'condition' => prefix_meta.'video_post_type:is(html5)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Flv video','wpqa'),
				'id'        => prefix_meta.'video_flv',
				'desc'      => esc_html__('Put flv video here','wpqa'),
				'type'      => 'text',
				'condition' => prefix_meta.'video_post_type:is(html5)'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			/*
			$options[] = array(
				'name'      => esc_html__('Quote content','wpqa'),
				'id'        => prefix_meta.'quote_content',
				'desc'      => esc_html__('Put here the quote content','wpqa'),
				'type'      => 'textarea',
				'cols'      => "40",
				'rows'      => "8",
				'condition' => 'what_post:is(quote)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Author','wpqa'),
				'id'        => prefix_meta.'quote_author',
				'desc'      => esc_html__('Put here the quote author','wpqa'),
				'type'      => 'text',
				'condition' => 'what_post:is(quote)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Quote style','wpqa'),
				'id'        => prefix_meta.'quote_style',
				'type'      => 'select',
				'options'   => array(
					'full' => esc_html__("Full post","wpqa"),
					'box'  => esc_html__("Block box","wpqa"),
				),
				'std'       => 'full',
				'desc'      => esc_html__('Choose from here the quote style','wpqa'),
				'condition' => 'what_post:is(quote)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Quote icon color','wpqa'),
				'id'        => prefix_meta.'quote_icon_color',
				'desc'      => esc_html__('Put here the quote icon color','wpqa'),
				'type'      => 'color',
				'condition' => 'what_post:is(quote)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Quote color','wpqa'),
				'id'        => prefix_meta.'quote_color',
				'desc'      => esc_html__('Put here the quote color','wpqa'),
				'type'      => 'color',
				'condition' => 'what_post:is(quote)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Link title','wpqa'),
				'id'        => prefix_meta.'link_title',
				'desc'      => esc_html__('Put here the link title','wpqa'),
				'type'      => 'text',
				'condition' => 'what_post:is(link)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Link','wpqa'),
				'id'        => prefix_meta.'link',
				'desc'      => esc_html__('Put here the link','wpqa'),
				'type'      => 'text',
				'condition' => 'what_post:is(link)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Link target','wpqa'),
				'id'        => prefix_meta.'link_target',
				'type'      => 'select',
				'options'   => array(
					'style_1' => esc_html__("Same window","wpqa"),
					'style_2' => esc_html__("New window","wpqa"),
				),
				'std'       => 'style_1',
				'desc'      => esc_html__('Choose from here the Link target','wpqa'),
				'condition' => 'what_post:is(link)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Link style','wpqa'),
				'id'        => prefix_meta.'link_style',
				'type'      => 'select',
				'options'   => array(
					'full' => esc_html__("Full post","wpqa"),
					'box'  => esc_html__("Block box","wpqa"),
				),
				'std'       => 'full',
				'desc'      => esc_html__('Choose from here the link style','wpqa'),
				'condition' => 'what_post:is(link)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Link icon color','wpqa'),
				'id'        => prefix_meta.'link_icon_color',
				'desc'      => esc_html__('Put here the link icon color','wpqa'),
				'type'      => 'color',
				'condition' => 'what_post:is(link)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Link color','wpqa'),
				'id'        => prefix_meta.'link_color',
				'desc'      => esc_html__('Put here the link color','wpqa'),
				'type'      => 'color',
				'condition' => 'what_post:is(link)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Link icon hover color','wpqa'),
				'id'        => prefix_meta.'link_icon_hover_color',
				'desc'      => esc_html__('Put here the link icon hover color','wpqa'),
				'type'      => 'color',
				'condition' => 'what_post:is(link)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Link hover color','wpqa'),
				'id'        => prefix_meta.'link_hover_color',
				'desc'      => esc_html__('Put here the link hover color','wpqa'),
				'type'      => 'color',
				'condition' => 'what_post:is(link)'
			);
			*/
			$options[] = array(
				'name'      => esc_html__('Soundcloud embed','wpqa'),
				'id'        => prefix_meta.'soundcloud_embed',
				'desc'      => esc_html__('Put here the soundcloud embed','wpqa'),
				'type'      => 'text',
				'condition' => 'what_post:is(soundcloud)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Soundcloud height','wpqa'),
				'id'        => prefix_meta.'soundcloud_height',
				'desc'      => esc_html__('Put here the soundcloud height','wpqa'),
				'type'      => 'text',
				'std'       => '150',
				'condition' => 'what_post:is(soundcloud)'
			);
			/*
			$options[] = array(
				'name'      => esc_html__('Twitter embed','wpqa'),
				'id'        => prefix_meta.'twitter_embed',
				'desc'      => esc_html__('Put here the twitter embed','wpqa'),
				'type'      => 'text',
				'condition' => 'what_post:is(twitter)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Facebook embed','wpqa'),
				'id'        => prefix_meta.'facebook_embed',
				'desc'      => esc_html__('Put here the facebook embed','wpqa'),
				'type'      => 'textarea',
				'condition' => 'what_post:is(facebook)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Instagram embed','wpqa'),
				'id'        => prefix_meta.'instagram_embed',
				'desc'      => esc_html__('Put here the instagram embed','wpqa'),
				'type'      => 'textarea',
				'condition' => 'what_post:is(instagram)'
			);
			
			$options[] = array(
				'type'      => 'heading-2',
				'operator'  => 'or',
				'condition' => 'what_post:is(quote),what_post:is(link),what_post:is(soundcloud),what_post:is(facebook),what_post:is(twitter),what_post:is(instagram)',
				'div'       => 'div'
			);
			
			$options[] = array(
				"name"       => esc_html__('Padding top','wpqa'),
				"id"         => prefix_meta."padding_top",
				'class'      => 'width_50',
				"type"       => "slider",
				'std'        => '30',
				"js_options" => array(
					"step" => "on",
					"min"  => 0,
					"max"  => 200,
				),
			);
			
			$options[] = array(
				"name"       => esc_html__('Padding right','wpqa'),
				"id"         => prefix_meta."padding_right",
				'class'      => 'width_50',
				"type"       => "slider",
				'std'        => '30',
				"js_options" => array(
					"step" => "on",
					"min"  => 0,
					"max"  => 200,
				),
			);
			
			$options[] = array(
				"name"       => esc_html__('Padding bottom','wpqa'),
				"id"         => prefix_meta."padding_bottom",
				'class'      => 'width_50',
				"type"       => "slider",
				'std'        => '30',
				"js_options" => array(
					"step" => "on",
					"min"  => 0,
					"max"  => 200,
				),
			);
			
			$options[] = array(
				"name"       => esc_html__('Padding left','wpqa'),
				"id"         => prefix_meta."padding_left",
				'class'      => 'width_50',
				"type"       => "slider",
				'std'        => '30',
				"js_options" => array(
					"step" => "on",
					"min"  => 0,
					"max"  => 200,
				),
			);
			
			$options[] = array(
				'name'    => esc_html__('Background','wpqa'),
				'id'      => prefix_meta.'post_head_background',
				'std'     => $background_defaults,
				'options' => $background_defaults,
				'type'    => 'background'
			);
			
			$options[] = array(
				'name' => esc_html__('Full Screen Background','wpqa'),
				'desc' => esc_html__('Select ON to enable Full Screen Background','wpqa'),
				'id'   => prefix_meta.'post_head_background_full',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name' => esc_html__('Transparent Background color ?','wpqa'),
				'id'   => prefix_meta.'post_head_background_transparent',
				'type' => 'checkbox',
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			*/
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
		}

		if (wpqa_is_post_type(array("page"))) {
			$options = apply_filters(wpqa_prefix_theme."_meta_before_home",$options);

			$options[] = array(
				'name'      => esc_html__('Home settings','wpqa'),
				'id'        => 'home_setting',
				'icon'      => 'admin-home',
				'type'      => 'heading',
				'template'  => 'template-home.php'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Show the search box?','wpqa'),
				'id'   => prefix_meta.'search_box',
				'type' => 'checkbox',
			);
			
			$options[] = array(
				'name'      => esc_html__('Show the select of search type on the search box?','wpqa'),
				'id'        => prefix_meta.'search_type_box',
				'type'      => 'checkbox',
				'condition' => 'search_box:not(0)',
			);
			
			$options[] = array(
				'name' => esc_html__('Show the ask question box?','wpqa'),
				'id'   => prefix_meta.'ask_question_box',
				'type' => 'checkbox',
				'std'  => 'on'
			);

			if (has_himer() || has_knowly()) {
				$options[] = array(
					'name'  => esc_html__('Which menu do you want to show the homepage tabs?','wpqa'),
					'id'    => 'tabs_menu_select',
					'type'  => 'radio',
					'std'   => 'default',
					'options' => array(
						'default'	  => esc_html__('Default','wpqa'),
						'second_menu' => esc_html__('Second menu','wpqa'),
						'left_menu'	  => esc_html__('Left menu','wpqa'),
					),
					'save'  => 'option'
				);

				$options[] = array(
					'name'      => esc_html__('The icons of the tabs enable or disable?','wpqa'),
					'id'        => prefix_meta.'tabs_menu_icons',
					'type'      => 'checkbox',
					'condition' => 'tabs_menu_select:not(default)',
				);
			}else {
				$options[] = array(
					'name'  => esc_html__('Show the tabs at the left menu?','wpqa'),
					'id'    => 'tabs_menu',
					'type'  => 'checkbox',
					'save'  => 'option'
				);
			}
			
			$options[] = array(
				'name'        => esc_html__('Choose the categories show','wpqa'),
				'id'          => prefix_meta.'categories_show',
				'type'        => 'custom_addition',
				'show_option' => esc_html__('All Question Categories','wpqa'),
				'taxonomy'    => wpqa_question_categories,
				'addto'       => prefix_meta.'home_tabs',
				'toadd'       => 'yes',
				'option_none' => 'q-0'
			);

			if ($activate_knowledgebase == true) {
				$options[] = array(
					'name'        => esc_html__('Choose the categories show','wpqa'),
					'id'          => prefix_meta.'categories_show_knowledgebase',
					'type'        => 'custom_addition',
					'show_option' => esc_html__('All Knowledgebase Categories','wpqa'),
					'taxonomy'    => wpqa_knowledgebase_categories,
					'addto'       => prefix_meta.'home_tabs',
					'toadd'       => 'yes',
					'option_none' => 'k-0'
				);
			}
			
			$home_tabs = array(
				"feed"                 => array("sort" => esc_html__('Feed','wpqa'),"value" => "feed"),
				"recent-questions"     => array("sort" => esc_html__('Recent Questions','wpqa'),"value" => "recent-questions"),
				"questions-for-you"    => array("sort" => esc_html__('Questions For You','wpqa'),"value" => ""),
				"most-answers"         => array("sort" => esc_html__('Most Answered','wpqa'),"value" => "most-answers"),
				"answers"              => array("sort" => esc_html__('Answers','wpqa'),"value" => "answers"),
				"no-answers"           => array("sort" => esc_html__('No Answers','wpqa'),"value" => "no-answers"),
				"most-visit"           => array("sort" => esc_html__('Most Visited','wpqa'),"value" => "most-visit"),
				"most-vote"            => array("sort" => esc_html__('Most Voted','wpqa'),"value" => "most-vote"),
				"random"               => array("sort" => esc_html__('Random Questions','wpqa'),"value" => "random"),
				"question-bump"        => array("sort" => esc_html__('Bump Question','wpqa'),"value" => ""),
				"new-questions"        => array("sort" => esc_html__('New Questions','wpqa'),"value" => ""),
				"sticky-questions"     => array("sort" => esc_html__('Sticky Questions','wpqa'),"value" => ""),
				"polls"                => array("sort" => esc_html__('Poll Questions','wpqa'),"value" => ""),
				"followed"             => array("sort" => esc_html__('Followed Questions','wpqa'),"value" => ""),
				"favorites"            => array("sort" => esc_html__('Favorite Questions','wpqa'),"value" => ""),
				"answers-might-like"   => array("sort" => esc_html__('Answers You Might Like','wpqa'),"value" => ""),
				"answers-for-you"      => array("sort" => esc_html__('Answers For You','wpqa'),"value" => ""),
				"poll-feed"            => array("sort" => esc_html__('Poll Feed','wpqa'),"value" => ""),
				"recent-posts"         => array("sort" => esc_html__('Recent Posts','wpqa'),"value" => ""),
				"posts-visited"        => array("sort" => esc_html__('Most Visited Posts','wpqa'),"value" => ""),
				
				"feed-2"               => array("sort" => esc_html__('Feed With Time','wpqa'),"value" => ""),
				"recent-questions-2"   => array("sort" => esc_html__('Recent Questions With Time','wpqa'),"value" => ""),
				"questions-for-you-2"  => array("sort" => esc_html__('Questions For You With Time','wpqa'),"value" => ""),
				"most-answers-2"       => array("sort" => esc_html__('Most Answered With Time','wpqa'),"value" => ""),
				"answers-2"            => array("sort" => esc_html__('Answers With Time','wpqa'),"value" => ""),
				"no-answers-2"         => array("sort" => esc_html__('No Answers With Time','wpqa'),"value" => ""),
				"most-visit-2"         => array("sort" => esc_html__('Most Visited With Time','wpqa'),"value" => ""),
				"most-vote-2"          => array("sort" => esc_html__('Most Voted With Time','wpqa'),"value" => ""),
				"random-2"             => array("sort" => esc_html__('Random Questions With Time','wpqa'),"value" => ""),
				"question-bump-2"      => array("sort" => esc_html__('Bump Question With Time','wpqa'),"value" => ""),
				"new-questions-2"      => array("sort" => esc_html__('New Questions With Time','wpqa'),"value" => ""),
				"sticky-questions-2"   => array("sort" => esc_html__('Sticky Questions With Time','wpqa'),"value" => ""),
				"polls-2"              => array("sort" => esc_html__('Poll Questions With Time','wpqa'),"value" => ""),
				"followed-2"           => array("sort" => esc_html__('Followed Questions With Time','wpqa'),"value" => ""),
				"favorites-2"          => array("sort" => esc_html__('Favorite Questions With Time','wpqa'),"value" => ""),
				"answers-might-like-2" => array("sort" => esc_html__('Answers You Might Like With Time','wpqa'),"value" => ""),
				"answers-for-you-2"    => array("sort" => esc_html__('Answers For You With Time','wpqa'),"value" => ""),
				"poll-feed-2"          => array("sort" => esc_html__('Poll Feed With Time','wpqa'),"value" => ""),
				"recent-posts-2"       => array("sort" => esc_html__('Recent Posts With Time','wpqa'),"value" => ""),
				"posts-visited-2"      => array("sort" => esc_html__('Most Visited Posts With Time','wpqa'),"value" => ""),
			);

			if (has_himer() || has_knowly() || has_questy()) {
				$home_tabs['most-reacted'] = array("sort" => esc_html__('Most Reacted','wpqa'),"value" => "");

				$home_tabs['most-reacted-2'] = array("sort" => esc_html__('Most Reacted with time','wpqa'),"value" => "");
			}

			if ($activate_knowledgebase == true) {
				$home_tabs['recent-knowledgebases'] = array("sort" => esc_html__('Recent Knowledgebases','wpqa'),"value" => "");
				$home_tabs['random-knowledgebases'] = array("sort" => esc_html__('Random Knowledgebases','wpqa'),"value" => "");
				$home_tabs['sticky-knowledgebases'] = array("sort" => esc_html__('Sticky Knowledgebases','wpqa'),"value" => "");
				$home_tabs['knowledgebases-visited'] = array("sort" => esc_html__('Most Visited Knowledgebases','wpqa'),"value" => "");
				$home_tabs['knowledgebases-voted'] = array("sort" => esc_html__('Most Voted Knowledgebases','wpqa'),"value" => "");

				$home_tabs['recent-knowledgebases-2'] = array("sort" => esc_html__('Recent Articles with time','wpqa'),"value" => "");
				$home_tabs['random-knowledgebases-2'] = array("sort" => esc_html__('Random Articles with time','wpqa'),"value" => "");
				$home_tabs['sticky-knowledgebases-2'] = array("sort" => esc_html__('Sticky Articles with time','wpqa'),"value" => "");
				$home_tabs['knowledgebases-visited-2'] = array("sort" => esc_html__('Most Visited Articles with time','wpqa'),"value" => "");
				$home_tabs['knowledgebases-voted-2'] = array("sort" => esc_html__('Most Voted Articles with time','wpqa'),"value" => "");
			}

			$home_tabs = apply_filters(wpqa_prefix_theme."_meta_home_tabs",$home_tabs);
			
			$options[] = array(
				'name'    => esc_html__('Select the tabs you want to show','wpqa'),
				'id'      => prefix_meta.'home_tabs',
				'type'    => 'multicheck',
				'sort'    => 'yes',
				'std'     => $home_tabs,
				'options' => $home_tabs
			);
			
			$options[] = array(
				'name'      => esc_html__('Show the categories filter?','wpqa'),
				'id'        => prefix_meta.'categories_filter',
				'condition' => 'tabs_menu:not(on)',
				'type'      => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'home_tabs:has(feed)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'home_tabs:has(recent-questions)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Display by','wpqa'),
				'id'      => prefix_meta."question_display_r",
				'type'    => 'select',
				'options' => array(
					'lasts'	             => esc_html__('Lasts','wpqa'),
					'single_category'    => esc_html__('Single category','wpqa'),
					'categories'         => esc_html__('Multiple categories','wpqa'),
					'exclude_categories' => esc_html__('Exclude categories','wpqa'),
					'custom_posts'	     => esc_html__('Custom questions','wpqa'),
				),
				'std'     => 'lasts',
			);
			
			$options[] = array(
				'name'      => esc_html__('Single category','wpqa'),
				'id'        => prefix_meta.'question_single_category_r',
				'type'      => 'select_category',
				'condition' => prefix_meta.'question_display_r:is(single_category)',
				'taxonomy'  => wpqa_question_categories,
			);
			
			$options[] = array(
				'name'      => esc_html__('Question categories','wpqa'),
				'desc'      => esc_html__('Select the question categories.','wpqa'),
				'id'        => prefix_meta."question_categories_r",
				'type'      => 'multicheck_category',
				'condition' => prefix_meta.'question_display_r:is(categories)',
				'taxonomy'  => wpqa_question_categories,
			);
			
			$options[] = array(
				'name'      => esc_html__('Exclude Question categories','wpqa'),
				'desc'      => esc_html__('Select the exclude question categories.','wpqa'),
				'id'        => prefix_meta."question_exclude_categories_r",
				'type'      => 'multicheck_category',
				'condition' => prefix_meta.'question_display_r:is(exclude_categories)',
				'taxonomy'  => wpqa_question_categories,
			);
			
			$options[] = array(
				'name'      => esc_html__('Question ids','wpqa'),
				'desc'      => esc_html__('Type the question ids.','wpqa'),
				'id'        => prefix_meta."question_questions_r",
				'condition' => prefix_meta.'question_display_r:is(custom_posts)',
				'type'      => 'text',
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name'    => esc_html__('Pagination style','wpqa'),
				'desc'    => esc_html__('Choose pagination style from here.','wpqa'),
				'id'      => prefix_meta.'pagination_home',
				'options' => array(
					'standard'        => esc_html__('Standard','wpqa'),
					'pagination'      => esc_html__('Pagination','wpqa'),
					'load_more'       => esc_html__('Load more','wpqa'),
					'infinite_scroll' => esc_html__('Infinite scroll','wpqa'),
					'none'            => esc_html__('None','wpqa'),
				),
				'std'   => 'pagination',
				'type'  => 'radio',
			);
			
			$options[] = array(
				'name' => esc_html__('Items per page','wpqa'),
				'desc' => esc_html__('Put the items per page.','wpqa'),
				'id'   => prefix_meta.'posts_per_page',
				'std'  => '5',
				'type' => 'text'
			);
			
			$options[] = array(
				'name'    => esc_html__('Order','wpqa'),
				'id'      => prefix_meta.'order_page_h',
				'std'     => "DESC",
				'type'    => 'radio',
				'options' => array(
					'DESC' => esc_html__('Descending','wpqa'),
					'ASC'  => esc_html__('Ascending','wpqa'),
				),
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type'      => 'heading-2',
				'condition' => prefix_meta.'home_tabs:has(feed),'.prefix_meta.'home_tabs:has(feed-2),'.prefix_meta.'home_tabs:has(poll-feed),'.prefix_meta.'home_tabs:has(poll-feed-2)',
				'operator'  => 'or',
				'name'      => esc_html__('Custom setting for feed tabs','wpqa')
			);

			$options = apply_filters(wpqa_prefix_theme.'_options_question_feed',$options,"home_");

			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);

			$options[] = array(
				'type'      => 'heading-2',
				'condition' => prefix_meta.'home_tabs:has(feed-2),'.prefix_meta.'home_tabs:has(recent-questions-2),'.prefix_meta.'home_tabs:has(questions-for-you-2),'.prefix_meta.'home_tabs:has(most-answers-2),'.prefix_meta.'home_tabs:has(question-bump-2),'.prefix_meta.'home_tabs:has(new-questions-2),'.prefix_meta.'home_tabs:has(sticky-questions-2),'.prefix_meta.'home_tabs:has(polls-2),'.prefix_meta.'home_tabs:has(followed-2),'.prefix_meta.'home_tabs:has(favorites-2),'.prefix_meta.'home_tabs:has(answers-2),'.prefix_meta.'home_tabs:has(answers-might-like-2),'.prefix_meta.'home_tabs:has(answers-for-you-2),'.prefix_meta.'home_tabs:has(most-visit-2),'.prefix_meta.'home_tabs:has(most-vote-2),'.prefix_meta.'home_tabs:has(random-2),'.prefix_meta.'home_tabs:has(no-answers-2),'.prefix_meta.'home_tabs:has(poll-feed-2),'.prefix_meta.'home_tabs:has(recent-posts-2),'.prefix_meta.'home_tabs:has(posts-visited-2)'.(has_himer() || has_knowly() || has_questy()?','.prefix_meta.'home_tabs:has(most-reacted-2)':'').($activate_knowledgebase == true?','.prefix_meta.'home_tabs:has(recent-knowledgebases-2),'.prefix_meta.'home_tabs:has(random-knowledgebases-2),'.prefix_meta.'home_tabs:has(sticky-knowledgebases-2),'.prefix_meta.'home_tabs:has(knowledgebases-visited-2),'.prefix_meta.'home_tabs:has(knowledgebases-voted-2)':''),
				'operator'  => 'or',
				'name'      => esc_html__('Time frame for the tabs','wpqa')
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for feed tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for feed tab.','wpqa'),
				'id'        => prefix_meta."date_feed",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(feed-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for recent questions tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for recent questions tab.','wpqa'),
				'id'        => prefix_meta."date_recent_questions",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(recent-questions-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for questions for you tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for questions for you tab.','wpqa'),
				'id'        => prefix_meta."date_questions_for_you",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(questions-for-you-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for most answered tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for most answered tab.','wpqa'),
				'id'        => prefix_meta."date_most_answered",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(most-answers-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for bump question tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for bump question tab.','wpqa'),
				'id'        => prefix_meta."date_question_bump",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(question-bump-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for answers tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for answers tab.','wpqa'),
				'id'        => prefix_meta."date_answers",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(answers-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for answers you might like tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for answers you might like tab.','wpqa'),
				'id'        => prefix_meta."date_answers_might_like",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(answers-might-like-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for answers for you tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for answers for you tab.','wpqa'),
				'id'        => prefix_meta."date_answers_for_you",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(answers-for-you-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for most visited tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for most visited tab.','wpqa'),
				'id'        => prefix_meta."date_most_visited",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(most-visit-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for most voted tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for most voted tab.','wpqa'),
				'id'        => prefix_meta."date_most_voted",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(most-vote-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			if (has_himer() || has_knowly() || has_questy()) {
				$options[] = array(
					'name'      => esc_html__('Specific date for most reacted tab.','wpqa'),
					'desc'      => esc_html__('Select the specific date for most reacted tab.','wpqa'),
					'id'        => prefix_meta."date_most_reacted",
					'std'       => "all",
					'type'      => "radio",
					'condition' => prefix_meta.'home_tabs:has(most-reacted-2)',
					'options'   => array(
						'all'   => esc_html__('All The Time','wpqa'),
						'24'    => esc_html__('Last 24 Hours','wpqa'),
						'48'    => esc_html__('Last 2 Days','wpqa'),
						'72'    => esc_html__('Last 3 Days','wpqa'),
						'96'    => esc_html__('Last 4 Days','wpqa'),
						'120'   => esc_html__('Last 5 Days','wpqa'),
						'144'   => esc_html__('Last 6 Days','wpqa'),
						'week'  => esc_html__('Last Week','wpqa'),
						'month' => esc_html__('Last Month','wpqa'),
						'year'  => esc_html__('Last Year','wpqa'),
					)
				);
			}

			$options[] = array(
				'name'      => esc_html__('Specific date for no answers tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for no answers tab.','wpqa'),
				'id'        => prefix_meta."date_no_answers",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(no-answers-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for poll feed tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for poll feed tab.','wpqa'),
				'id'        => prefix_meta."date_poll_feed",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(poll-feed-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for recent posts tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for recent posts tab.','wpqa'),
				'id'        => prefix_meta."date_recent_posts",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(recent-posts-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for posts visited tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for posts visited tab.','wpqa'),
				'id'        => prefix_meta."date_posts_visited",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(posts-visited-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for random questions tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for random questions tab.','wpqa'),
				'id'        => prefix_meta."date_random_questions",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(random-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for new questions tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for new questions tab.','wpqa'),
				'id'        => prefix_meta."date_new_questions",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(new-questions-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for sticky questions tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for sticky questions tab.','wpqa'),
				'id'        => prefix_meta."date_sticky_questions",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(sticky-questions-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for poll questions tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for poll questions tab.','wpqa'),
				'id'        => prefix_meta."date_poll_questions",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(polls-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for followed questions tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for followed questions tab.','wpqa'),
				'id'        => prefix_meta."date_followed_questions",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(followed-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$options[] = array(
				'name'      => esc_html__('Specific date for favorite questions tab.','wpqa'),
				'desc'      => esc_html__('Select the specific date for favorite questions tab.','wpqa'),
				'id'        => prefix_meta."date_favorites_questions",
				'std'       => "all",
				'type'      => "radio",
				'condition' => prefix_meta.'home_tabs:has(favorites-2)',
				'options'   => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			if ($activate_knowledgebase == true) {
				$options[] = array(
					'name'      => esc_html__('Specific date for recent articles tab.','wpqa'),
					'desc'      => esc_html__('Select the specific date for recent articles tab.','wpqa'),
					'id'        => prefix_meta."date_recent_knowledgebases",
					'std'       => "all",
					'type'      => "radio",
					'condition' => prefix_meta.'home_tabs:has(recent-knowledgebases-2)',
					'options'   => array(
						'all'   => esc_html__('All The Time','wpqa'),
						'24'    => esc_html__('Last 24 Hours','wpqa'),
						'48'    => esc_html__('Last 2 Days','wpqa'),
						'72'    => esc_html__('Last 3 Days','wpqa'),
						'96'    => esc_html__('Last 4 Days','wpqa'),
						'120'   => esc_html__('Last 5 Days','wpqa'),
						'144'   => esc_html__('Last 6 Days','wpqa'),
						'week'  => esc_html__('Last Week','wpqa'),
						'month' => esc_html__('Last Month','wpqa'),
						'year'  => esc_html__('Last Year','wpqa'),
					)
				);

				$options[] = array(
					'name'      => esc_html__('Specific date for random articles tab.','wpqa'),
					'desc'      => esc_html__('Select the specific date for random articles tab.','wpqa'),
					'id'        => prefix_meta."date_random_knowledgebases",
					'std'       => "all",
					'type'      => "radio",
					'condition' => prefix_meta.'home_tabs:has(random-knowledgebases-2)',
					'options'   => array(
						'all'   => esc_html__('All The Time','wpqa'),
						'24'    => esc_html__('Last 24 Hours','wpqa'),
						'48'    => esc_html__('Last 2 Days','wpqa'),
						'72'    => esc_html__('Last 3 Days','wpqa'),
						'96'    => esc_html__('Last 4 Days','wpqa'),
						'120'   => esc_html__('Last 5 Days','wpqa'),
						'144'   => esc_html__('Last 6 Days','wpqa'),
						'week'  => esc_html__('Last Week','wpqa'),
						'month' => esc_html__('Last Month','wpqa'),
						'year'  => esc_html__('Last Year','wpqa'),
					)
				);

				$options[] = array(
					'name'      => esc_html__('Specific date for sticky articles tab.','wpqa'),
					'desc'      => esc_html__('Select the specific date for sticky articles tab.','wpqa'),
					'id'        => prefix_meta."date_sticky_knowledgebases",
					'std'       => "all",
					'type'      => "radio",
					'condition' => prefix_meta.'home_tabs:has(sticky-knowledgebases-2)',
					'options'   => array(
						'all'   => esc_html__('All The Time','wpqa'),
						'24'    => esc_html__('Last 24 Hours','wpqa'),
						'48'    => esc_html__('Last 2 Days','wpqa'),
						'72'    => esc_html__('Last 3 Days','wpqa'),
						'96'    => esc_html__('Last 4 Days','wpqa'),
						'120'   => esc_html__('Last 5 Days','wpqa'),
						'144'   => esc_html__('Last 6 Days','wpqa'),
						'week'  => esc_html__('Last Week','wpqa'),
						'month' => esc_html__('Last Month','wpqa'),
						'year'  => esc_html__('Last Year','wpqa'),
					)
				);

				$options[] = array(
					'name'      => esc_html__('Specific date for articles visited tab.','wpqa'),
					'desc'      => esc_html__('Select the specific date for articles visited tab.','wpqa'),
					'id'        => prefix_meta."date_knowledgebases_visited",
					'std'       => "all",
					'type'      => "radio",
					'condition' => prefix_meta.'home_tabs:has(knowledgebases-visited-2)',
					'options'   => array(
						'all'   => esc_html__('All The Time','wpqa'),
						'24'    => esc_html__('Last 24 Hours','wpqa'),
						'48'    => esc_html__('Last 2 Days','wpqa'),
						'72'    => esc_html__('Last 3 Days','wpqa'),
						'96'    => esc_html__('Last 4 Days','wpqa'),
						'120'   => esc_html__('Last 5 Days','wpqa'),
						'144'   => esc_html__('Last 6 Days','wpqa'),
						'week'  => esc_html__('Last Week','wpqa'),
						'month' => esc_html__('Last Month','wpqa'),
						'year'  => esc_html__('Last Year','wpqa'),
					)
				);

				$options[] = array(
					'name'      => esc_html__('Specific date for articles voted tab.','wpqa'),
					'desc'      => esc_html__('Select the specific date for articles voted tab.','wpqa'),
					'id'        => prefix_meta."date_knowledgebases_voted",
					'std'       => "all",
					'type'      => "radio",
					'condition' => prefix_meta.'home_tabs:has(knowledgebases-voted-2)',
					'options'   => array(
						'all'   => esc_html__('All The Time','wpqa'),
						'24'    => esc_html__('Last 24 Hours','wpqa'),
						'48'    => esc_html__('Last 2 Days','wpqa'),
						'72'    => esc_html__('Last 3 Days','wpqa'),
						'96'    => esc_html__('Last 4 Days','wpqa'),
						'120'   => esc_html__('Last 5 Days','wpqa'),
						'144'   => esc_html__('Last 6 Days','wpqa'),
						'week'  => esc_html__('Last Week','wpqa'),
						'month' => esc_html__('Last Month','wpqa'),
						'year'  => esc_html__('Last Year','wpqa'),
					)
				);
			}

			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);

			$meta_tabs_condition = apply_filters(wpqa_prefix_theme."_meta_tabs_condition",prefix_meta.'home_tabs:has(feed),'.prefix_meta.'home_tabs:has(recent-questions),'.prefix_meta.'home_tabs:has(questions-for-you),'.prefix_meta.'home_tabs:has(most-answers),'.prefix_meta.'home_tabs:has(question-bump),'.prefix_meta.'home_tabs:has(new-questions),'.prefix_meta.'home_tabs:has(sticky-questions),'.prefix_meta.'home_tabs:has(polls),'.prefix_meta.'home_tabs:has(followed),'.prefix_meta.'home_tabs:has(favorites),'.prefix_meta.'home_tabs:has(answers),'.prefix_meta.'home_tabs:has(answers-might-like),'.prefix_meta.'home_tabs:has(answers-for-you),'.prefix_meta.'home_tabs:has(most-visit),'.prefix_meta.'home_tabs:has(most-vote),'.prefix_meta.'home_tabs:has(random),'.prefix_meta.'home_tabs:has(no-answers),'.prefix_meta.'home_tabs:has(recent-posts),'.prefix_meta.'home_tabs:has(posts-visited),'.prefix_meta.'home_tabs:has(recent-questions-2),'.prefix_meta.'home_tabs:has(questions-for-you-2),'.prefix_meta.'home_tabs:has(most-answers-2),'.prefix_meta.'home_tabs:has(question-bump-2),'.prefix_meta.'home_tabs:has(new-questions-2),'.prefix_meta.'home_tabs:has(sticky-questions-2),'.prefix_meta.'home_tabs:has(polls-2),'.prefix_meta.'home_tabs:has(followed-2),'.prefix_meta.'home_tabs:has(favorites-2),'.prefix_meta.'home_tabs:has(answers-2),'.prefix_meta.'home_tabs:has(answers-might-like-2),'.prefix_meta.'home_tabs:has(answers-for-you-2),'.prefix_meta.'home_tabs:has(most-visit-2),'.prefix_meta.'home_tabs:has(most-vote-2),'.prefix_meta.'home_tabs:has(random-2),'.prefix_meta.'home_tabs:has(no-answers-2),'.prefix_meta.'home_tabs:has(recent-posts-2),'.prefix_meta.'home_tabs:has(posts-visited-2)'.(has_himer() || has_knowly() || has_questy()?','.prefix_meta.'home_tabs:has(most-reacted),'.prefix_meta.'home_tabs:has(most-reacted-2)':'').($activate_knowledgebase == true?','.prefix_meta.'home_tabs:has(recent-knowledgebases),'.prefix_meta.'home_tabs:has(random-knowledgebases),'.prefix_meta.'home_tabs:has(sticky-knowledgebases),'.prefix_meta.'home_tabs:has(knowledgebases-visited),'.prefix_meta.'home_tabs:has(knowledgebases-voted),'.prefix_meta.'home_tabs:has(recent-knowledgebases-2),'.prefix_meta.'home_tabs:has(random-knowledgebases-2),'.prefix_meta.'home_tabs:has(sticky-knowledgebases-2),'.prefix_meta.'home_tabs:has(knowledgebases-visited-2),'.prefix_meta.'home_tabs:has(knowledgebases-voted-2)':''));

			$options[] = array(
				'type'      => 'heading-2',
				'condition' => $meta_tabs_condition,
				'operator'  => 'or',
				'name'      => esc_html__('Custom setting for the slugs','wpqa')
			);

			$options[] = array(
				'name'      => esc_html__('Feed slug','wpqa'),
				'id'        => prefix_meta.'feed_slug',
				'std'       => 'feed',
				'condition' => prefix_meta.'home_tabs:has(feed)',
				'type'      => 'text'
			);

			$options[] = array(
				'name'      => esc_html__('Recent questions slug','wpqa'),
				'id'        => prefix_meta.'recent_questions_slug',
				'std'       => 'recent-questions',
				'condition' => prefix_meta.'home_tabs:has(recent-questions)',
				'type'      => 'text'
			);

			$options[] = array(
				'name'      => esc_html__('Questions for you slug','wpqa'),
				'id'        => prefix_meta.'questions_for_you_slug',
				'std'       => 'questions-for-you',
				'condition' => prefix_meta.'home_tabs:has(questions-for-you)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Most answered slug','wpqa'),
				'id'        => prefix_meta.'most_answers_slug',
				'std'       => 'most-answered',
				'condition' => prefix_meta.'home_tabs:has(most-answers)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Bump question slug','wpqa'),
				'id'        => prefix_meta.'question_bump_slug',
				'std'       => 'question-bump',
				'condition' => prefix_meta.'home_tabs:has(question-bump)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('New questions slug','wpqa'),
				'id'        => prefix_meta.'question_new_slug',
				'std'       => 'new',
				'condition' => prefix_meta.'home_tabs:has(new-questions)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Question sticky slug','wpqa'),
				'id'        => prefix_meta.'question_sticky_slug',
				'std'       => 'sticky',
				'condition' => prefix_meta.'home_tabs:has(sticky-questions)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Question polls slug','wpqa'),
				'id'        => prefix_meta.'question_polls_slug',
				'std'       => 'polls',
				'condition' => prefix_meta.'home_tabs:has(polls)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Question followed slug','wpqa'),
				'id'        => prefix_meta.'question_followed_slug',
				'std'       => 'followed',
				'condition' => prefix_meta.'home_tabs:has(followed)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Question favorites slug','wpqa'),
				'id'        => prefix_meta.'question_favorites_slug',
				'std'       => 'favorites',
				'condition' => prefix_meta.'home_tabs:has(favorites)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Answers slug','wpqa'),
				'id'        => prefix_meta.'answers_slug',
				'std'       => 'answers',
				'condition' => prefix_meta.'home_tabs:has(answers)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Answers you might like slug','wpqa'),
				'id'        => prefix_meta.'answers_might_like_slug',
				'std'       => 'answers-might-like',
				'condition' => prefix_meta.'home_tabs:has(answers-might-like)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Answers for you slug','wpqa'),
				'id'        => prefix_meta.'answers_for_you_slug',
				'std'       => 'answers-for-you',
				'condition' => prefix_meta.'home_tabs:has(answers-for-you)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Most visited slug','wpqa'),
				'id'        => prefix_meta.'most_visit_slug',
				'std'       => 'most-visited',
				'condition' => prefix_meta.'home_tabs:has(most-visit)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Most voted slug','wpqa'),
				'id'        => prefix_meta.'most_vote_slug',
				'std'       => 'most-voted',
				'condition' => prefix_meta.'home_tabs:has(most-vote)',
				'type'      => 'text'
			);

			if (has_himer() || has_knowly() || has_questy()) {
				$options[] = array(
					'name'      => esc_html__('Most reacted slug','wpqa'),
					'id'        => prefix_meta.'most_reacted_slug',
					'std'       => 'most-reacted',
					'condition' => prefix_meta.'home_tabs:has(most-reacted)',
					'type'      => 'text'
				);
			}
			
			$options[] = array(
				'name'      => esc_html__('Random slug','wpqa'),
				'id'        => prefix_meta.'random_slug',
				'std'       => 'random',
				'condition' => prefix_meta.'home_tabs:has(random)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('No answers slug','wpqa'),
				'id'        => prefix_meta.'no_answers_slug',
				'std'       => 'no-answers',
				'condition' => prefix_meta.'home_tabs:has(no-answers)',
				'type'      => 'text'
			);

			$options[] = array(
				'name'      => esc_html__('Poll feed slug','wpqa'),
				'id'        => prefix_meta.'poll_feed_slug',
				'std'       => 'poll-feed',
				'condition' => prefix_meta.'home_tabs:has(poll-feed)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Recent posts slug','wpqa'),
				'id'        => prefix_meta.'recent_posts_slug',
				'std'       => 'recent-posts',
				'condition' => prefix_meta.'home_tabs:has(recent-posts)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Posts visited slug','wpqa'),
				'id'        => prefix_meta.'posts_visited_slug',
				'std'       => 'posts-visited',
				'condition' => prefix_meta.'home_tabs:has(posts-visited)',
				'type'      => 'text'
			);

			$options[] = array(
				'name'      => esc_html__('Feed with time slug','wpqa'),
				'id'        => prefix_meta.'feed_slug_2',
				'std'       => 'feed-time',
				'condition' => prefix_meta.'home_tabs:has(feed-2)',
				'type'      => 'text'
			);

			$options[] = array(
				'name'      => esc_html__('Recent questions with time slug','wpqa'),
				'id'        => prefix_meta.'recent_questions_slug_2',
				'std'       => 'recent-questions-time',
				'condition' => prefix_meta.'home_tabs:has(recent-questions-2)',
				'type'      => 'text'
			);

			$options[] = array(
				'name'      => esc_html__('Questions for you with time slug','wpqa'),
				'id'        => prefix_meta.'questions_for_you_slug_2',
				'std'       => 'questions-for-you-time',
				'condition' => prefix_meta.'home_tabs:has(questions-for-you-2)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Most answered with time slug','wpqa'),
				'id'        => prefix_meta.'most_answers_slug_2',
				'std'       => 'most-answered-time',
				'condition' => prefix_meta.'home_tabs:has(most-answers-2)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Bump question with time slug','wpqa'),
				'id'        => prefix_meta.'question_bump_slug_2',
				'std'       => 'question-bump-time',
				'condition' => prefix_meta.'home_tabs:has(question-bump-2)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('New questions with time slug','wpqa'),
				'id'        => prefix_meta.'question_new_slug_2',
				'std'       => 'new-time',
				'condition' => prefix_meta.'home_tabs:has(new-questions-2)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Question sticky with time slug','wpqa'),
				'id'        => prefix_meta.'question_sticky_slug_2',
				'std'       => 'sticky-time',
				'condition' => prefix_meta.'home_tabs:has(sticky-questions-2)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Question polls with time slug','wpqa'),
				'id'        => prefix_meta.'question_polls_slug_2',
				'std'       => 'polls-time',
				'condition' => prefix_meta.'home_tabs:has(polls-2)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Question followed with time slug','wpqa'),
				'id'        => prefix_meta.'question_followed_slug_2',
				'std'       => 'followed-time',
				'condition' => prefix_meta.'home_tabs:has(followed-2)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Question favorites with time slug','wpqa'),
				'id'        => prefix_meta.'question_favorites_slug_2',
				'std'       => 'favorites-time',
				'condition' => prefix_meta.'home_tabs:has(favorites-2)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Answers with time slug','wpqa'),
				'id'        => prefix_meta.'answers_slug_2',
				'std'       => 'answers-time',
				'condition' => prefix_meta.'home_tabs:has(answers-2)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Answers you might like with time slug','wpqa'),
				'id'        => prefix_meta.'answers_might_like_slug_2',
				'std'       => 'answers-might-like-time',
				'condition' => prefix_meta.'home_tabs:has(answers-might-like-2)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Answers for you with time slug','wpqa'),
				'id'        => prefix_meta.'answers_for_you_slug_2',
				'std'       => 'answers-for-you-time',
				'condition' => prefix_meta.'home_tabs:has(answers-for-you-2)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Most visited with time slug','wpqa'),
				'id'        => prefix_meta.'most_visit_slug_2',
				'std'       => 'most-visited-time',
				'condition' => prefix_meta.'home_tabs:has(most-visit-2)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Most voted with time slug','wpqa'),
				'id'        => prefix_meta.'most_vote_slug_2',
				'std'       => 'most-voted-time',
				'condition' => prefix_meta.'home_tabs:has(most-vote-2)',
				'type'      => 'text'
			);

			if (has_himer() || has_knowly() || has_questy()) {
				$options[] = array(
					'name'      => esc_html__('Most reacted with time slug','wpqa'),
					'id'        => prefix_meta.'most_reacted_slug_2',
					'std'       => 'most-reacted-time',
					'condition' => prefix_meta.'home_tabs:has(most-reacted-2)',
					'type'      => 'text'
				);
			}
			
			$options[] = array(
				'name'      => esc_html__('Random with time slug','wpqa'),
				'id'        => prefix_meta.'random_slug_2',
				'std'       => 'random-time',
				'condition' => prefix_meta.'home_tabs:has(random-2)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('No answers with time slug','wpqa'),
				'id'        => prefix_meta.'no_answers_slug_2',
				'std'       => 'no-answers-time',
				'condition' => prefix_meta.'home_tabs:has(no-answers-2)',
				'type'      => 'text'
			);

			$options[] = array(
				'name'      => esc_html__('Poll feed with time slug','wpqa'),
				'id'        => prefix_meta.'poll_feed_slug_2',
				'std'       => 'poll-feed-time',
				'condition' => prefix_meta.'home_tabs:has(poll-feed-2)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Recent posts with time slug','wpqa'),
				'id'        => prefix_meta.'recent_posts_slug_2',
				'std'       => 'recent-posts-time',
				'condition' => prefix_meta.'home_tabs:has(recent-posts-2)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Posts visited with time slug','wpqa'),
				'id'        => prefix_meta.'posts_visited_slug_2',
				'std'       => 'posts-visited-time',
				'condition' => prefix_meta.'home_tabs:has(posts-visited-2)',
				'type'      => 'text'
			);

			if ($activate_knowledgebase == true) {
				$options[] = array(
					'name'      => esc_html__('Recent knowledgebases slug','wpqa'),
					'id'        => prefix_meta.'recent_knowledgebases_slug',
					'std'       => 'recent-knowledgebases',
					'condition' => prefix_meta.'home_tabs:has(recent-knowledgebases)',
					'type'      => 'text'
				);
				
				$options[] = array(
					'name'      => esc_html__('Random knowledgebases slug','wpqa'),
					'id'        => prefix_meta.'random_knowledgebases_slug',
					'std'       => 'random-knowledgebases',
					'condition' => prefix_meta.'home_tabs:has(random-knowledgebases)',
					'type'      => 'text'
				);
				
				$options[] = array(
					'name'      => esc_html__('Sticky knowledgebases slug','wpqa'),
					'id'        => prefix_meta.'sticky_knowledgebases_slug',
					'std'       => 'sticky-knowledgebases',
					'condition' => prefix_meta.'home_tabs:has(sticky-knowledgebases)',
					'type'      => 'text'
				);
				
				$options[] = array(
					'name'      => esc_html__('Knowledgebases visited slug','wpqa'),
					'id'        => prefix_meta.'knowledgebases_visited_slug',
					'std'       => 'knowledgebases-visited',
					'condition' => prefix_meta.'home_tabs:has(knowledgebases-visited)',
					'type'      => 'text'
				);
				
				$options[] = array(
					'name'      => esc_html__('Knowledgebases voted slug','wpqa'),
					'id'        => prefix_meta.'knowledgebases_voted_slug',
					'std'       => 'knowledgebases-voted',
					'condition' => prefix_meta.'home_tabs:has(knowledgebases-voted)',
					'type'      => 'text'
				);

				$options[] = array(
					'name'      => esc_html__('Recent knowledgebases with time slug','wpqa'),
					'id'        => prefix_meta.'recent_knowledgebases_slug_2',
					'std'       => 'recent-knowledgebases-time',
					'condition' => prefix_meta.'home_tabs:has(recent-knowledgebases-2)',
					'type'      => 'text'
				);
				
				$options[] = array(
					'name'      => esc_html__('Random knowledgebases with time slug','wpqa'),
					'id'        => prefix_meta.'random_knowledgebases_slug_2',
					'std'       => 'random-knowledgebases-time',
					'condition' => prefix_meta.'home_tabs:has(random-knowledgebases-2)',
					'type'      => 'text'
				);
				
				$options[] = array(
					'name'      => esc_html__('Sticky knowledgebases with time slug','wpqa'),
					'id'        => prefix_meta.'sticky_knowledgebases_slug_2',
					'std'       => 'sticky-knowledgebases-time',
					'condition' => prefix_meta.'home_tabs:has(sticky-knowledgebases-2)',
					'type'      => 'text'
				);
				
				$options[] = array(
					'name'      => esc_html__('Knowledgebases visited with time slug','wpqa'),
					'id'        => prefix_meta.'knowledgebases_visited_slug_2',
					'std'       => 'knowledgebases-visited-time',
					'condition' => prefix_meta.'home_tabs:has(knowledgebases-visited-2)',
					'type'      => 'text'
				);
				
				$options[] = array(
					'name'      => esc_html__('Knowledgebases voted with time slug','wpqa'),
					'id'        => prefix_meta.'knowledgebases_voted_slug_2',
					'std'       => 'knowledgebases-voted-time',
					'condition' => prefix_meta.'home_tabs:has(knowledgebases-voted-2)',
					'type'      => 'text'
				);
			}

			$options = apply_filters(wpqa_prefix_theme.'_meta_tabs',$options);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'home_tabs:has(feed),'.prefix_meta.'home_tabs:has(recent-questions),'.prefix_meta.'home_tabs:has(questions-for-you),'.prefix_meta.'home_tabs:has(most-answers),'.prefix_meta.'home_tabs:has(question-bump),'.prefix_meta.'home_tabs:has(new-questions),'.prefix_meta.'home_tabs:has(sticky-questions),'.prefix_meta.'home_tabs:has(polls),'.prefix_meta.'home_tabs:has(followed),'.prefix_meta.'home_tabs:has(favorites),'.prefix_meta.'home_tabs:has(most-visit),'.prefix_meta.'home_tabs:has(most-vote),'.prefix_meta.'home_tabs:has(random),'.prefix_meta.'home_tabs:has(no-answers),'.prefix_meta.'home_tabs:has(poll-feed),'.prefix_meta.'home_tabs:has(feed-2),'.prefix_meta.'home_tabs:has(recent-questions-2),'.prefix_meta.'home_tabs:has(questions-for-you-2),'.prefix_meta.'home_tabs:has(most-answers-2),'.prefix_meta.'home_tabs:has(question-bump-2),'.prefix_meta.'home_tabs:has(new-questions-2),'.prefix_meta.'home_tabs:has(sticky-questions-2),'.prefix_meta.'home_tabs:has(polls-2),'.prefix_meta.'home_tabs:has(followed-2),'.prefix_meta.'home_tabs:has(favorites-2),'.prefix_meta.'home_tabs:has(most-visit-2),'.prefix_meta.'home_tabs:has(most-vote-2),'.prefix_meta.'home_tabs:has(random-2),'.prefix_meta.'home_tabs:has(no-answers-2),'.prefix_meta.'home_tabs:has(poll-feed-2)'.(has_himer() || has_knowly() || has_questy()?','.prefix_meta.'home_tabs:has(most-reacted),'.prefix_meta.'home_tabs:has(most-reacted-2)':''),
				'operator'  => 'or',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'name' => esc_html__('Custom setting for the questions','wpqa')
			);
			
			$options[] = array(
				'name' => esc_html__('Choose a custom setting for the questions','wpqa'),
				'id'   => prefix_meta.'custom_home_question',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'custom_home_question:not(0)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Question style','wpqa'),
				'desc'    => esc_html__('Choose question style from here.','wpqa'),
				'id'      => prefix_meta.'question_columns_h',
				'options' => array(
					'style_1' => esc_html__('1 column','wpqa'),
					'style_2' => esc_html__('2 columns','wpqa')." - ".esc_html__('Works with sidebar, full width, and left menu only.','wpqa'),
				),
				'std'   => 'style_1',
				'type'  => 'radio',
			);
			
			$options[] = array(
				'name'      => esc_html__("Activate the masonry style?","wpqa"),
				'id'        => prefix_meta.'masonry_style_h',
				'type'      => 'checkbox',
				'condition' => prefix_meta.'question_columns_h:is(style_2)',
			);
			
			$options[] = array(
				'name' => esc_html__('Activate the author image in questions loop?','wpqa'),
				'desc' => esc_html__('Enable or disable author image in questions loop?','wpqa'),
				'id'   => prefix_meta.'author_image_h',
				'std'  => "on",
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name' => esc_html__('Activate the vote in loop?','wpqa'),
				'desc' => esc_html__('Enable or disable vote in loop?','wpqa'),
				'id'   => prefix_meta.'vote_question_loop_h',
				'std'  => "on",
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name'      => esc_html__('Select ON to hide the dislike at questions loop','wpqa'),
				'desc'      => esc_html__('If you put it ON the dislike will not show.','wpqa'),
				'id'        => prefix_meta.'question_loop_dislike_h',
				'type'      => 'checkbox',
				'condition' => prefix_meta.'vote_question_loop_h:not(0)',
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON to show the poll in questions loop','wpqa'),
				'id'   => prefix_meta.'question_poll_loop_h',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON to hide the excerpt in questions','wpqa'),
				'id'   => prefix_meta.'excerpt_questions_h',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'excerpt_questions_h:is(0)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Excerpt question','wpqa'),
				'desc' => esc_html__('Put here the excerpt question.','wpqa'),
				'id'   => prefix_meta.'question_excerpt_h',
				'std'  => 40,
				'type' => 'text'
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON to activate the read more button in questions','wpqa'),
				'id'   => prefix_meta.'read_more_question_h',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name'      => esc_html__('Select ON to activate the read more by jQuery in questions','wpqa'),
				'id'        => prefix_meta.'read_jquery_question_h',
				'type'      => 'checkbox',
				'condition' => prefix_meta.'read_more_question_h:not(0)',
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON to activate to see some answers and add a new answer by jQuery in questions','wpqa'),
				'id'   => prefix_meta.'answer_question_jquery_h',
				'type' => 'checkbox',
			);
			
			$options[] = array(
				'name' => esc_html__('Activate the follow button at questions loop','wpqa'),
				'desc' => esc_html__('Select ON if you want to activate the follow button at questions loop.','wpqa'),
				'id'   => prefix_meta.'question_follow_loop_h',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name' => esc_html__('Enable or disable Tags at loop?','wpqa'),
				'id'   => prefix_meta.'question_tags_loop_h',
				'std'  => 'on',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name' => (has_himer() || has_knowly() || has_questy()?esc_html__('Activate the answer at the loop by best answer, most voted, most reacted, last answer or first answer','wpqa'):esc_html__('Activate the answer at the loop by best answer, most voted, last answer or first answer','wpqa')),
				'id'   => prefix_meta.'question_answer_loop_h',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'question_answer_loop_h:not(0)',
				'type'      => 'heading-2'
			);

			$answer_show = array(
				'best'   => esc_html__('Best answer','wpqa'),
				'vote'   => esc_html__('Most voted','wpqa'),
				'last'   => esc_html__('Last answer','wpqa'),
				'oldest' => esc_html__('First answer','wpqa'),
			);

			if (has_himer() || has_knowly() || has_questy()) {
				$answer_show['reacted'] = esc_html__('Most reacted','wpqa');
			}
			
			$options[] = array(
				'name'    => esc_html__('Answer type','wpqa'),
				'desc'    => esc_html__("Choose what's the answer you need to show from here.","wpqa"),
				'id'      => prefix_meta.'question_answer_show_h',
				'options' => $answer_show,
				'std'     => 'best',
				'type'    => 'radio'
			);

			$options[] = array(
				'name'    => esc_html__('Answer place','wpqa'),
				'desc'    => esc_html__("Choose where's the answer to be placed - before or after question meta.","wpqa"),
				'id'      => prefix_terms.'question_answer_place_h',
				'options' => array(
					'before' => esc_html__('Before question meta','wpqa'),
					'after'  => esc_html__('After question meta','wpqa'),
				),
				'std'     => 'after',
				'type'    => 'radio'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name'    => esc_html__('Select the meta options','wpqa'),
				'id'      => prefix_meta.'question_meta_h',
				'type'    => 'multicheck',
				'std'     => $question_meta_std,
				'options' => $question_meta_options
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'home_tabs:has(recent-posts),'.prefix_meta.'home_tabs:has(recent-posts-2),'.prefix_meta.'home_tabs:has(posts-visited),'.prefix_meta.'home_tabs:has(posts-visited-2)',
				'operator'  => 'or',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'name' => esc_html__('Custom setting for the posts','wpqa')
			);
			
			$options[] = array(
				'name' => esc_html__('Choose a custom setting for the posts','wpqa'),
				'id'   => prefix_meta.'custom_home_blog',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'custom_home_blog:not(0)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Post style','wpqa'),
				'desc'    => esc_html__('Choose post style from here.','wpqa'),
				'id'      => prefix_meta.'post_style_h',
				'options' => array(
					'style_1' => esc_html__('1 column','wpqa'),
					'style_2' => esc_html__('List style','wpqa'),
					'style_3' => esc_html__('Columns','wpqa'),
				),
				'std'   => 'style_1',
				'type'  => 'radio',
			);
			
			$options[] = array(
				'name'      => esc_html__("Activate the masonry style?","wpqa"),
				'id'        => prefix_meta.'masonry_style_h',
				'type'      => 'checkbox',
				'condition' => prefix_meta.'post_style_h:is(style_3)',
			);
			
			$options[] = array(
				'name' => esc_html__('Hide the featured image in the loop','wpqa'),
				'desc' => esc_html__('Select ON to hide the featured image in the loop.','wpqa'),
				'id'   => prefix_meta.'featured_image_h',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'id'        => prefix_meta."sort_meta_title_image_h",
				'condition' => prefix_meta.'post_style_h:is(style_3)',
				'std'       => array(
								array("value" => "image",'name' => esc_html__('Image','wpqa'),"default" => "yes"),
								array("value" => "meta_title",'name' => esc_html__('Meta and title','wpqa'),"default" => "yes"),
							),
				'type'      => "sort",
				'options'   => array(
								array("value" => "image",'name' => esc_html__('Image','wpqa'),"default" => "yes"),
								array("value" => "meta_title",'name' => esc_html__('Meta and title','wpqa'),"default" => "yes"),
							)
			);
			
			$options[] = array(
				'name' => esc_html__('Read more enable or disable','wpqa'),
				'id'   => prefix_meta.'read_more_h',
				'std'  => "on",
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name' => esc_html__('Excerpt post','wpqa'),
				'desc' => esc_html__('Put here the excerpt post.','wpqa'),
				'id'   => prefix_meta.'post_excerpt_h',
				'std'  => 40,
				'type' => 'text'
			);
			
			$options[] = array(
				'name'    => esc_html__('Select the meta options','wpqa'),
				'id'      => prefix_meta.'post_meta_h',
				'type'    => 'multicheck',
				'std'     => $post_meta_std,
				'options' => $post_meta_options
			);
			
			$options[] = array(
				'name'      => esc_html__('Select the share options','wpqa'),
				'id'        => prefix_meta.'post_share_h',
				'condition' => prefix_meta.'post_style_h:not(style_3)',
				'type'      => 'multicheck',
				'sort'      => 'yes',
				'std'       => $share_array,
				'options'   => $share_array
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'home_tabs:has(answers),'.prefix_meta.'home_tabs:has(answers-might-like),'.prefix_meta.'home_tabs:has(answers-for-you),'.prefix_meta.'home_tabs:has(answers-2),'.prefix_meta.'home_tabs:has(answers-might-like-2),'.prefix_meta.'home_tabs:has(answers-for-you-2)',
				'operator'  => 'or',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'name' => esc_html__('Custom setting for the answers','wpqa')
			);

			$orderby_answers = array(
				'recent' => esc_html__('Recent','wpqa'),
				'oldest' => esc_html__('Oldest','wpqa'),
				'votes'  => esc_html__('Voted','wpqa'),
			);

			if (has_himer() || has_knowly() || has_questy()) {
				$orderby_answers['reacted'] = esc_html__('Reacted','wpqa');
			}
			
			$options[] = array(
				'name'    => esc_html__('Order by','wpqa'),
				'desc'    => esc_html__('Select the answers order by.','wpqa'),
				'id'      => prefix_meta."orderby_answers_h",
				'std'     => "recent",
				'type'    => "radio",
				'options' => $orderby_answers
			);
			
			$options[] = array(
				'name' => esc_html__('Choose a custom setting for the answers','wpqa'),
				'id'   => prefix_meta.'custom_home_answer',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'custom_home_answer:not(0)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Activate the author image in answers?','wpqa'),
				'desc' => esc_html__('Author image in answers enable or disable.','wpqa'),
				'id'   => prefix_meta.'answers_image_h',
				'std'  => "on",
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON to activate the vote at answers','wpqa'),
				'desc' => esc_html__('Select ON to enable the vote at the answers.','wpqa'),
				'id'   => prefix_meta.'active_vote_answer_h',
				'std'  => "on",
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name'      => esc_html__('Select ON to hide the dislike at answers','wpqa'),
				'desc'      => esc_html__('If you put it ON the dislike will not show.','wpqa'),
				'id'        => prefix_meta.'show_dislike_answers_h',
				'type'      => 'checkbox',
				'condition' => prefix_meta.'active_vote_answer_h:not(0)',
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name'     => esc_html__('Blog setting','wpqa'),
				'id'       => 'loop_setting',
				'icon'     => 'admin-page',
				'type'     => 'heading',
				'template' => 'template-blog.php'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);

			$options[] = array(
				'name'    => esc_html__('Specific date.','wpqa'),
				'desc'    => esc_html__('Select the specific date.','wpqa'),
				'id'      => prefix_meta."specific_date_b",
				'std'     => "all",
				'type'    => "radio",
				'options' => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);
			
			$options[] = array(
				'name'    => esc_html__('Order by','wpqa'),
				'desc'    => esc_html__('Select the post order by.','wpqa'),
				'id'      => prefix_meta."orderby_post_b",
				'std'     => "recent",
				'type'    => "radio",
				'options' => array(
					'recent'  => esc_html__('Recent','wpqa'),
					'popular' => esc_html__('Most Commented','wpqa'),
					'random'  => esc_html__('Random','wpqa'),
				)
			);
			
			$options[] = array(
				'name'    => esc_html__('Order','wpqa'),
				'id'      => prefix_meta.'order_post',
				'std'     => "DESC",
				'type'    => 'radio',
				'options' => array(
					'DESC' => esc_html__('Descending','wpqa'),
					'ASC'  => esc_html__('Ascending','wpqa'),
				),
			);
			
			$options[] = array(
				'name'    => esc_html__('Display by','wpqa'),
				'id'      => prefix_meta."post_display_b",
				'type'    => 'select',
				'options' => array(
					'lasts'	             => esc_html__('Lasts','wpqa'),
					'single_category'    => esc_html__('Single category','wpqa'),
					'categories'         => esc_html__('Multiple categories','wpqa'),
					'exclude_categories' => esc_html__('Exclude categories','wpqa'),
					'custom_posts'	     => esc_html__('Custom posts','wpqa'),
				),
				'std'     => 'lasts',
			);
			
			$options[] = array(
				'name'      => esc_html__('Single category','wpqa'),
				'id'        => prefix_meta.'post_single_category_b',
				'type'      => 'select_category',
				'condition' => prefix_meta.'post_display_b:is(single_category)',
			);
			
			$options[] = array(
				'name'      => esc_html__('Post categories','wpqa'),
				'desc'      => esc_html__('Select the post categories.','wpqa'),
				'id'        => prefix_meta."post_categories_b",
				'type'      => 'multicheck_category',
				'condition' => prefix_meta.'post_display_b:is(categories)',
			);
			
			$options[] = array(
				'name'      => esc_html__('Post exclude categories','wpqa'),
				'desc'      => esc_html__('Select the post exclude categories.','wpqa'),
				'id'        => prefix_meta."post_exclude_categories_b",
				'type'      => 'multicheck_category',
				'condition' => prefix_meta.'post_display_b:is(exclude_categories)',
			);
			
			$options[] = array(
				'name'      => esc_html__('Post ids','wpqa'),
				'desc'      => esc_html__('Type the post ids.','wpqa'),
				'id'        => prefix_meta."post_posts_b",
				'type'      => 'text',
				'condition' => prefix_meta.'post_display_b:is(custom_posts)',
			);
			
			$options[] = array(
				'name' => esc_html__('Choose a custom setting for this blog page','wpqa'),
				'id'   => prefix_meta.'custom_blog_setting',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'custom_blog_setting:not(0)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Post style','wpqa'),
				'desc'    => esc_html__('Choose post style from here.','wpqa'),
				'id'      => prefix_meta.'post_style_b',
				'options' => array(
					'style_1' => esc_html__('1 column','wpqa'),
					'style_2' => esc_html__('List style','wpqa'),
					'style_3' => esc_html__('Columns','wpqa'),
				),
				'std'   => 'style_1',
				'type'  => 'radio',
			);
			
			$options[] = array(
				'name'      => esc_html__("Activate the masonry style?","wpqa"),
				'id'        => prefix_meta.'masonry_style_b',
				'type'      => 'checkbox',
				'condition' => prefix_meta.'post_style_b:is(style_3)',
			);
			
			$options[] = array(
				'name' => esc_html__('Hide the featured image in the loop','wpqa'),
				'desc' => esc_html__('Select ON to hide the featured image in the loop.','wpqa'),
				'id'   => prefix_meta.'featured_image_b',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'id'        => prefix_meta."sort_meta_title_image_b",
				'condition' => prefix_meta.'post_style_b:is(style_3)',
				'std'       => array(
								array("value" => "image",'name' => esc_html__('Image','wpqa'),"default" => "yes"),
								array("value" => "meta_title",'name' => esc_html__('Meta and title','wpqa'),"default" => "yes"),
							),
				'type'      => "sort",
				'options'   => array(
								array("value" => "image",'name' => esc_html__('Image','wpqa'),"default" => "yes"),
								array("value" => "meta_title",'name' => esc_html__('Meta and title','wpqa'),"default" => "yes"),
							)
			);
			
			$options[] = array(
				'name' => esc_html__('Read more enable or disable','wpqa'),
				'id'   => prefix_meta.'read_more_b',
				'std'  => "on",
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name' => esc_html__('Excerpt post','wpqa'),
				'desc' => esc_html__('Put here the excerpt post.','wpqa'),
				'id'   => prefix_meta.'post_excerpt_b',
				'std'  => 40,
				'type' => 'text'
			);
			
			$options[] = array(
				'name' => esc_html__('Posts number','wpqa'),
				'desc' => esc_html__('put the posts number','wpqa'),
				'id'   => prefix_meta.'post_number_b',
				'type' => 'text',
				'std'  => "10"
			);
			
			$options[] = array(
				'name'    => esc_html__('Pagination style','wpqa'),
				'desc'    => esc_html__('Choose pagination style from here.','wpqa'),
				'id'      => prefix_meta.'post_pagination_b',
				'options' => array(
					'standard'        => esc_html__('Standard','wpqa'),
					'pagination'      => esc_html__('Pagination','wpqa'),
					'load_more'       => esc_html__('Load more','wpqa'),
					'infinite_scroll' => esc_html__('Infinite scroll','wpqa'),
					'none'            => esc_html__('None','wpqa'),
				),
				'std'     => 'pagination',
				'type'    => 'radio',
			);
			
			$options[] = array(
				'name'    => esc_html__('Select the meta options','wpqa'),
				'id'      => prefix_meta.'post_meta_b',
				'type'    => 'multicheck',
				'std'     => $post_meta_std,
				'options' => $post_meta_options
			);
			
			$options[] = array(
				'name'      => esc_html__('Select the share options','wpqa'),
				'id'        => prefix_meta.'post_share_b',
				'condition' => prefix_meta.'post_style_b:not(style_3)',
				'type'      => 'multicheck',
				'sort'      => 'yes',
				'std'       => $share_array,
				'options'   => $share_array
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name'     => esc_html__('Comment settings','wpqa'),
				'id'       => 'comments_settings',
				'icon'     => 'admin-comments',
				'type'     => 'heading',
				'template' => 'template-comments.php'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Comment type','wpqa'),
				'desc'    => esc_html__('Select the comment type.','wpqa'),
				'id'      => prefix_meta."comment_type",
				'std'     => "answers",
				'type'    => "radio",
				'options' => array(
					'answers'  => esc_html__('Answers','wpqa'),
					'comments' => esc_html__('Comments','wpqa'),
				)
			);

			$options[] = array(
				'name'    => esc_html__('Specific date.','wpqa'),
				'desc'    => esc_html__('Select the specific date.','wpqa'),
				'id'      => prefix_meta."specific_date_c",
				'std'     => "all",
				'type'    => "radio",
				'options' => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);

			$orderby_answers = array(
				'date'  => esc_html__('Date','wpqa'),
				'votes' => esc_html__('Voted - Works at answers only.','wpqa'),
			);

			if (has_himer() || has_knowly() || has_questy()) {
				$orderby_answers['reacted'] = esc_html__('Reacted - Works at answers only.','wpqa');
			}
			
			$options[] = array(
				'name'    => esc_html__('Order by','wpqa'),
				'desc'    => esc_html__('Select the comments order by.','wpqa'),
				'id'      => prefix_meta."orderby_answers_a",
				'std'     => "date",
				'type'    => "radio",
				'options' => $orderby_answers
			);
			
			$options[] = array(
				'name'    => esc_html__('Order','wpqa'),
				'id'      => prefix_meta.'order_answers',
				'std'     => "DESC",
				'type'    => 'radio',
				'options' => array(
					'DESC' => esc_html__('Descending','wpqa'),
					'ASC'  => esc_html__('Ascending','wpqa'),
				),
			);
			
			$options[] = array(
				'name' => esc_html__('Choose a custom setting for the comments','wpqa'),
				'id'   => prefix_meta.'custom_answers',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'custom_answers:not(0)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Comments per page','wpqa'),
				'desc' => esc_html__('put the comments per page','wpqa'),
				'id'   => prefix_meta.'answers_number',
				'type' => 'text',
				'std'  => "10"
			);
			
			$options[] = array(
				'name'    => esc_html__('Pagination style','wpqa'),
				'desc'    => esc_html__('Choose pagination style from here.','wpqa'),
				'id'      => prefix_meta.'answers_pagination',
				'options' => array(
					'standard'        => esc_html__('Standard','wpqa'),
					'pagination'      => esc_html__('Pagination','wpqa'),
					'load_more'       => esc_html__('Load more','wpqa'),
					'infinite_scroll' => esc_html__('Infinite scroll','wpqa'),
					'none'            => esc_html__('None','wpqa'),
				),
				'std'     => 'pagination',
				'type'    => 'radio',
			);
			
			$options[] = array(
				'name' => esc_html__('Activate the author image in comments?','wpqa'),
				'desc' => esc_html__('Author image in comments enable or disable.','wpqa'),
				'id'   => prefix_meta.'answers_image_a',
				'std'  => "on",
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'comment_type:not(comments)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON to activate the vote at answers','wpqa'),
				'desc' => esc_html__('Select ON to enable the vote at the answers.','wpqa'),
				'id'   => prefix_meta.'active_vote_answer_a',
				'std'  => "on",
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name'      => esc_html__('Select ON to hide the dislike at answers','wpqa'),
				'desc'      => esc_html__('If you put it ON the dislike will not show.','wpqa'),
				'id'        => prefix_meta.'show_dislike_answers_a',
				'type'      => 'checkbox',
				'condition' => prefix_meta.'active_vote_answer_a:not(0)',
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);

			$options[] = array(
				'name'     => esc_html__('Groups settings','wpqa'),
				'id'       => 'groupss_settings',
				'icon'     => 'groups',
				'type'     => 'heading',
				'template' => 'template-groups.php'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Display by','wpqa'),
				'desc'    => esc_html__('Select the groups display by.','wpqa'),
				'id'      => prefix_meta.'group_display_g',
				'options' => array(
					'all'     => esc_html__('All groups','wpqa'),
					'private' => esc_html__('Private groups','wpqa'),
					'public'  => esc_html__('Public groups','wpqa'),
				),
				'std'     => 'all',
				'type'    => 'radio',
			);

			$options[] = array(
				'name'    => esc_html__('Order by','wpqa'),
				'desc'    => esc_html__('Select the groups order by.','wpqa'),
				'id'      => prefix_meta.'group_order_g',
				'options' => array(
					'date'  => esc_html__('Date','wpqa'),
					'users' => esc_html__('Users','wpqa'),
					'posts' => esc_html__('Posts','wpqa'),
				),
				'std'     => 'date',
				'type'    => 'radio',
			);
			
			$options[] = array(
				'name' => esc_html__('Choose a custom setting for this groups page','wpqa'),
				'id'   => prefix_meta.'custom_group_setting',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'custom_group_setting:not(0)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Groups per page','wpqa'),
				'desc' => esc_html__('put the groups per page','wpqa'),
				'id'   => prefix_meta.'group_number',
				'type' => 'text',
				'std'  => "10"
			);
			
			$options[] = array(
				'name'    => esc_html__('Pagination style','wpqa'),
				'desc'    => esc_html__('Choose pagination style from here.','wpqa'),
				'id'      => prefix_meta.'group_pagination',
				'options' => array(
					'standard'        => esc_html__('Standard','wpqa'),
					'pagination'      => esc_html__('Pagination','wpqa'),
					'load_more'       => esc_html__('Load more','wpqa'),
					'infinite_scroll' => esc_html__('Infinite scroll','wpqa'),
					'none'            => esc_html__('None','wpqa'),
				),
				'std'     => 'pagination',
				'type'    => 'radio',
			);
			
			$options[] = array(
				'name' => esc_html__('Show search at groups page','wpqa'),
				'desc' => esc_html__('Show search at groups page from the breadcrumb','wpqa'),
				'id'   => prefix_meta."group_search",
				'type' => 'checkbox',
				'std'  => 'on',
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name'     => esc_html__('Question settings','wpqa'),
				'id'       => 'questions_settings',
				'icon'     => 'editor-help',
				'type'     => 'heading',
				'template' => 'template-question.php'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);

			$orderby_question = array(
				'feed'          => esc_html__('Custom Feed','wpqa'),
				'recent'        => esc_html__('Recent','wpqa'),
				'popular'       => esc_html__('Most Answered','wpqa'),
				'random'        => esc_html__('Random','wpqa'),
				'most_visited'  => esc_html__('Most Visited','wpqa'),
				'most_voted'    => esc_html__('Most Voted','wpqa'),
				'no_answer'     => esc_html__('No Answers','wpqa'),
				'question_bump' => esc_html__('Bump Question','wpqa'),
				'new'           => esc_html__('New Questions - Without Sticky','wpqa'),
				'sticky'        => esc_html__('Sticky Questions','wpqa'),
				'polls'         => esc_html__('Poll Questions','wpqa'),
				'followed'      => esc_html__('Followed Questions','wpqa'),
				'favorites'     => esc_html__('Favorite Questions','wpqa'),
			);

			if (has_himer() || has_knowly() || has_questy()) {
				$orderby_question['reacted'] = esc_html__('Most Reacted','wpqa');
			}
			
			$options[] = array(
				'name'    => esc_html__('Display questions by','wpqa'),
				'desc'    => esc_html__('Select the question display by.','wpqa'),
				'id'      => prefix_meta."orderby_question_q",
				'std'     => "feed",
				'type'    => "select",
				'options' => $orderby_question
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'orderby_question_q:is(feed)',
				'type'      => 'heading-2'
			);

			$options[] = array(
				'type' => 'info',
				'name' => esc_html__('Custom setting for feed','wpqa')
			);

			$options = apply_filters(wpqa_prefix_theme.'_options_question_feed',$options);

			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'name'    => esc_html__('Specific date.','wpqa'),
				'desc'    => esc_html__('Select the specific date.','wpqa'),
				'id'      => prefix_meta."specific_date_q",
				'std'     => "all",
				'type'    => "radio",
				'options' => array(
					'all'   => esc_html__('All The Time','wpqa'),
					'24'    => esc_html__('Last 24 Hours','wpqa'),
					'48'    => esc_html__('Last 2 Days','wpqa'),
					'72'    => esc_html__('Last 3 Days','wpqa'),
					'96'    => esc_html__('Last 4 Days','wpqa'),
					'120'   => esc_html__('Last 5 Days','wpqa'),
					'144'   => esc_html__('Last 6 Days','wpqa'),
					'week'  => esc_html__('Last Week','wpqa'),
					'month' => esc_html__('Last Month','wpqa'),
					'year'  => esc_html__('Last Year','wpqa'),
				)
			);
			
			$options[] = array(
				'name'    => esc_html__('Order','wpqa'),
				'id'      => prefix_meta.'order_question',
				'std'     => "DESC",
				'type'    => 'radio',
				'options' => array(
					'DESC' => esc_html__('Descending','wpqa'),
					'ASC'  => esc_html__('Ascending','wpqa'),
				),
			);
			
			$options[] = array(
				'name'    => esc_html__('Display by','wpqa'),
				'id'      => prefix_meta."question_display_q",
				'type'    => 'select',
				'options' => array(
					'lasts'	             => esc_html__('Lasts','wpqa'),
					'single_category'    => esc_html__('Single category','wpqa'),
					'categories'         => esc_html__('Multiple categories','wpqa'),
					'exclude_categories' => esc_html__('Exclude categories','wpqa'),
					'custom_posts'	     => esc_html__('Custom questions','wpqa'),
				),
				'std'     => 'lasts',
			);
			
			$options[] = array(
				'name'      => esc_html__('Single category','wpqa'),
				'id'        => prefix_meta.'question_single_category_q',
				'type'      => 'select_category',
				'condition' => prefix_meta.'question_display_q:is(single_category)',
				'taxonomy'  => wpqa_question_categories,
			);
			
			$options[] = array(
				'name'      => esc_html__('Question categories','wpqa'),
				'desc'      => esc_html__('Select the question categories.','wpqa'),
				'id'        => prefix_meta."question_categories_q",
				'type'      => 'multicheck_category',
				'condition' => prefix_meta.'question_display_q:is(categories)',
				'taxonomy'  => wpqa_question_categories,
			);
			
			$options[] = array(
				'name'      => esc_html__('Exclude Question categories','wpqa'),
				'desc'      => esc_html__('Select the exclude question categories.','wpqa'),
				'id'        => prefix_meta."question_exclude_categories_q",
				'type'      => 'multicheck_category',
				'condition' => prefix_meta.'question_display_q:is(exclude_categories)',
				'taxonomy'  => wpqa_question_categories,
			);
			
			$options[] = array(
				'name'      => esc_html__('Question ids','wpqa'),
				'desc'      => esc_html__('Type the question ids.','wpqa'),
				'id'        => prefix_meta."question_questions_q",
				'condition' => prefix_meta.'question_display_q:is(custom_posts)',
				'type'      => 'text',
			);
			
			$options[] = array(
				'name' => esc_html__('Choose a custom setting for this questions page','wpqa'),
				'id'   => prefix_meta.'custom_question_setting',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'custom_question_setting:not(0)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Questions per page','wpqa'),
				'desc' => esc_html__('put the questions per page','wpqa'),
				'id'   => prefix_meta.'question_number',
				'type' => 'text',
				'std'  => "10"
			);
			
			$options[] = array(
				'name'    => esc_html__('Pagination style','wpqa'),
				'desc'    => esc_html__('Choose pagination style from here.','wpqa'),
				'id'      => prefix_meta.'question_pagination',
				'options' => array(
					'standard'        => esc_html__('Standard','wpqa'),
					'pagination'      => esc_html__('Pagination','wpqa'),
					'load_more'       => esc_html__('Load more','wpqa'),
					'infinite_scroll' => esc_html__('Infinite scroll','wpqa'),
					'none'            => esc_html__('None','wpqa'),
				),
				'std'     => 'pagination',
				'type'    => 'radio',
			);
			
			$options[] = array(
				'name'    => esc_html__('Question style','wpqa'),
				'desc'    => esc_html__('Choose question style from here.','wpqa'),
				'id'      => prefix_meta.'question_columns',
				'options' => array(
					'style_1' => esc_html__('1 column','wpqa'),
					'style_2' => esc_html__('2 columns','wpqa')." - ".esc_html__('Works with sidebar, full width, and left menu only.','wpqa'),
				),
				'std'     => 'style_1',
				'type'    => 'radio',
			);
			
			$options[] = array(
				'name'      => esc_html__("Activate the masonry style?","wpqa"),
				'id'        => prefix_meta.'masonry_style',
				'type'      => 'checkbox',
				'condition' => prefix_meta.'question_columns:is(style_2)',
			);
			
			$options[] = array(
				'name' => esc_html__('Activate the author image in questions loop?','wpqa'),
				'desc' => esc_html__('Enable or disable author image in questions loop?','wpqa'),
				'id'   => prefix_meta.'author_image',
				'std'  => "on",
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name' => esc_html__('Activate the vote in loop?','wpqa'),
				'desc' => esc_html__('Enable or disable vote in loop?','wpqa'),
				'id'   => prefix_meta.'vote_question_loop',
				'std'  => "on",
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name'      => esc_html__('Select ON to hide the dislike at questions loop','wpqa'),
				'desc'      => esc_html__('If you put it ON the dislike will not show.','wpqa'),
				'id'        => prefix_meta.'question_loop_dislike',
				'type'      => 'checkbox',
				'condition' => prefix_meta.'vote_question_loop:not(0)',
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON to show the poll in questions loop','wpqa'),
				'id'   => prefix_meta.'question_poll_loop',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON to hide the excerpt in questions','wpqa'),
				'id'   => prefix_meta.'excerpt_questions',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'excerpt_questions:is(0)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Excerpt question','wpqa'),
				'desc' => esc_html__('Put here the excerpt question.','wpqa'),
				'id'   => prefix_meta.'question_excerpt',
				'std'  => 40,
				'type' => 'text'
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON to activate the read more button in questions','wpqa'),
				'id'   => prefix_meta.'read_more_question',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name'      => esc_html__('Select ON to activate the read more by jQuery in questions','wpqa'),
				'id'        => prefix_meta.'read_jquery_question',
				'type'      => 'checkbox',
				'condition' => prefix_meta.'read_more_question:not(0)',
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON to activate to see some answers and add a new answer by jQuery in questions','wpqa'),
				'id'   => prefix_meta.'answer_question_jquery',
				'type' => 'checkbox',
			);
			
			$options[] = array(
				'name' => esc_html__('Activate the follow button at questions loop','wpqa'),
				'desc' => esc_html__('Select ON if you want to activate the follow button at questions loop.','wpqa'),
				'id'   => prefix_meta.'question_follow_loop',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name' => esc_html__('Enable or disable Tags at loop?','wpqa'),
				'id'   => prefix_meta.'question_tags_loop',
				'std'  => 'on',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name' => (has_himer() || has_knowly() || has_questy()?esc_html__('Activate the answer at the loop by best answer, most voted, most reacted, last answer or first answer','wpqa'):esc_html__('Activate the answer at the loop by best answer, most voted, last answer or first answer','wpqa')),
				'id'   => prefix_meta.'question_answer_loop',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'question_answer_loop:not(0)',
				'type'      => 'heading-2'
			);

			$answer_show = array(
				'best'   => esc_html__('Best answer','wpqa'),
				'vote'   => esc_html__('Most voted','wpqa'),
				'last'   => esc_html__('Last answer','wpqa'),
				'oldest' => esc_html__('First answer','wpqa'),
			);

			if (has_himer() || has_knowly() || has_questy()) {
				$answer_show['reacted'] = esc_html__('Most reacted','wpqa');
			}
			
			$options[] = array(
				'name'    => esc_html__('Answer type','wpqa'),
				'desc'    => esc_html__("Choose what's the answer you need to show from here.","wpqa"),
				'id'      => prefix_meta.'question_answer_show',
				'options' => $answer_show,
				'std'     => 'best',
				'type'    => 'radio'
			);

			$options[] = array(
				'name'    => esc_html__('Answer place','wpqa'),
				'desc'    => esc_html__("Choose where's the answer to be placed - before or after question meta.","wpqa"),
				'id'      => prefix_terms.'question_answer_place',
				'options' => array(
					'before' => esc_html__('Before question meta','wpqa'),
					'after'  => esc_html__('After question meta','wpqa'),
				),
				'std'     => 'after',
				'type'    => 'radio'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name'    => esc_html__('Select the meta options','wpqa'),
				'id'      => prefix_meta.'question_meta_q',
				'type'    => 'multicheck',
				'std'     => $question_meta_std,
				'options' => $question_meta_options
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name'     => esc_html__('FAQ settings','wpqa'),
				'id'       => 'faqs_settings',
				'icon'     => 'info',
				'type'     => 'heading',
				'template' => 'template-faqs.php'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'id'      => prefix_meta."faqs",
				'type'    => "elements",
				'button'  => esc_html__('Add a new faq','wpqa'),
				'hide'    => "yes",
				'options' => array(
					array(
						"type" => "text",
						"id"   => "text",
						"name" => esc_html__('Title','wpqa'),
					),
					array(
						"type" => "textarea",
						"id"   => "textarea",
						"name" => esc_html__('Content','wpqa'),
					),
				),
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name'     => esc_html__('User settings','wpqa'),
				'id'       => 'users_settings',
				'icon'     => 'admin-users',
				'type'     => 'heading',
				'template' => 'template-users.php'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Show search at users page','wpqa'),
				'desc' => esc_html__('Show search at users page from the breadcrumb','wpqa'),
				'id'   => prefix_meta."user_search",
				'type' => 'checkbox',
				'std'  => 'on',
			);
			
			$options[] = array(
				'name' => esc_html__('Show filter at users page','wpqa'),
				'desc' => esc_html__('Show filter at users page from the breadcrumb','wpqa'),
				'id'   => prefix_meta."user_filter",
				'type' => 'checkbox',
				'std'  => 'on',
			);
			
			$options[] = array(
				'name' => esc_html__('Users per page','wpqa'),
				'desc' => esc_html__('Put the users per page.','wpqa'),
				'id'   => prefix_meta.'users_per_page',
				'std'  => '10',
				'type' => 'text'
			);
			
			$options[] = array(
				'name'    => esc_html__('Choose the user based on roles or specific user ids','wpqa'),
				'id'      => prefix_meta.'user_shows',
				'type'    => 'radio',
				'std'     => "roles",
				'options' => array("roles" => esc_html__("Roles","wpqa"),"users" => esc_html__("Specific user ids","wpqa")),
			);

			$options[] = array(
				'name'      => esc_html__('Specific user ids','wpqa'),
				'id'        => prefix_meta.'user_specific_users',
				'type'      => 'text',
				'condition' => prefix_meta.'user_shows:is(users)'
			);

			$options[] = array(
				'name'      => esc_html__('Choose the user roles show','wpqa'),
				'id'        => prefix_meta.'user_group',
				'type'      => 'multicheck',
				'std'       => array("editor","administrator","author","contributor","subscriber"),
				'options'   => $wpqa_options_roles,
				'condition' => prefix_meta.'user_shows:not(users)'
			);
			
			$options[] = array(
				'name'    => esc_html__('Order by','wpqa'),
				'id'      => prefix_meta.'user_sort',
				'std'     => "register",
				'type'    => 'select',
				'options' => array(
					'user_registered' => esc_html__('Register','wpqa'),
					'display_name'    => esc_html__('Name','wpqa'),
					'ID'              => esc_html__('ID','wpqa'),
					'question_count'  => esc_html__('Questions','wpqa'),
					'answers'         => esc_html__('Answers','wpqa'),
					'the_best_answer' => esc_html__('Best Answers','wpqa'),
					'points'          => esc_html__('Points','wpqa'),
					'post_count'      => esc_html__('Posts','wpqa'),
					'comments'        => esc_html__('Comments','wpqa'),
					'followers'       => esc_html__('Followers','wpqa'),
				),
			);
			
			$options[] = array(
				'name'    => esc_html__('Users style','wpqa'),
				'desc'    => esc_html__('Choose the users style.','wpqa'),
				'id'      => prefix_meta.'user_style',
				'options' => array(
					'columns'       => esc_html__('Columns','wpqa'),
					'small_grid'    => esc_html__('Small grid with follow','wpqa'),
					'simple_follow' => esc_html__('Simple with follow','wpqa'),
					'small'         => esc_html__('Small','wpqa'),
					'grid'          => esc_html__('Grid','wpqa'),
					'normal'        => esc_html__('Normal','wpqa'),
				),
				'std'     => 'columns',
				'type'    => 'radio'
			);
			
			$options[] = array(
				'name'      => esc_html__("Activate the masonry style?","wpqa"),
				'id'        => prefix_meta.'masonry_user_style',
				'type'      => 'checkbox',
				'condition' => prefix_meta.'user_style:is(small_grid),'.prefix_meta.'user_style:is(columns),'.prefix_meta.'user_style:is(small),'.prefix_meta.'user_style:is(grid)',
				'operator'  => 'or',
			);
			
			$options[] = array(
				'name'    => esc_html__('Order','wpqa'),
				'id'      => prefix_meta.'user_order',
				'std'     => "DESC",
				'type'    => 'radio',
				'options' => array(
					'DESC' => esc_html__('Descending','wpqa'),
					'ASC'  => esc_html__('Ascending','wpqa'),
				),
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name'     => esc_html__('Category settings','wpqa'),
				'id'       => 'categories_settings',
				'icon'     => 'category',
				'type'     => 'heading',
				'template' => 'template-categories.php'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Show search at categories page','wpqa'),
				'desc' => esc_html__('Show search at categories page from the breadcrumb','wpqa'),
				'id'   => prefix_meta."cat_search",
				'type' => 'checkbox',
				'std'  => 'on',
			);
			
			$options[] = array(
				'name' => esc_html__('Show filter at categories page','wpqa'),
				'desc' => esc_html__('Show filter at categories page from the breadcrumb','wpqa'),
				'id'   => prefix_meta."cat_filter",
				'type' => 'checkbox',
				'std'  => 'on',
			);
			
			$options[] = array(
				'name' => esc_html__('Categories per page','wpqa'),
				'desc' => esc_html__('Put the categories per page.','wpqa'),
				'id'   => prefix_meta.'cats_per_page',
				'std'  => '50',
				'type' => 'text'
			);

			$cats_tax = array(
				wpqa_questions_type => esc_html__('Question categories','wpqa'),
				'post'              => esc_html__('Post categories','wpqa'),
			);

			if ($activate_knowledgebase == true) {
				$cats_tax[wpqa_knowledgebase_type] = esc_html__('Knowledgebase categories','wpqa');
			}
			
			$options[] = array(
				'name'    => esc_html__('Categories type','wpqa'),
				'id'      => prefix_meta.'cats_tax',
				'std'     => wpqa_questions_type,
				'type'    => 'radio',
				'options' => $cats_tax,
			);

			if (has_discy()) {
				$cat_style = array(
					"with_icon"     => esc_html__("With icon","wpqa"),
					"icon_color"    => esc_html__("With icon and colors","wpqa"),
					'with_icon_1'   => esc_html__('With icon 2','wpqa'),
					'with_icon_2'   => esc_html__('With colored icon','wpqa'),
					'with_icon_3'   => esc_html__('With colored icon and box','wpqa'),
					'with_icon_4'   => esc_html__('With colored icon and box 2','wpqa'),
					'with_cover_1'  => esc_html__('With cover','wpqa'),
					'with_cover_2'  => esc_html__('With cover and icon','wpqa'),
					'with_cover_3'  => esc_html__('With cover and small icon','wpqa'),
					'with_cover_4'  => esc_html__('With big cover','wpqa'),
					'with_cover_5'  => esc_html__('With big cover and icon','wpqa'),
					'with_cover_6'  => esc_html__('With big cover and small icon','wpqa'),
					'simple_follow' => esc_html__('Simple with follow','wpqa'),
					'simple'        => esc_html__('Simple','wpqa'),
				);
			}else {
				$cat_style = array(
					"with_icon"     => esc_html__("With icon","wpqa"),
					"icon_color"    => esc_html__("With icon and colors","wpqa"),
					'with_icon_1'   => esc_html__('With icon 2','wpqa'),
					'with_icon_2'   => esc_html__('With colored icon','wpqa'),
					'with_icon_3'   => esc_html__('With colored icon and box','wpqa'),
					'with_icon_4'   => esc_html__('With colored icon and box 2','wpqa'),
					'with_cover_1'  => esc_html__('With cover','wpqa'),
					'with_cover_3'  => esc_html__('With cover and small icon','wpqa'),
					'with_cover_4'  => esc_html__('With big cover','wpqa'),
					'with_cover_6'  => esc_html__('With big cover and small icon','wpqa'),
					'simple_follow' => esc_html__('Simple with follow','wpqa'),
					'simple'        => esc_html__('Simple','wpqa'),
				);
			}

			$options[] = array(
				'name'      => esc_html__('Categories style at categories page','wpqa'),
				'desc'      => esc_html__('Choose the categories style.','wpqa'),
				'id'        => prefix_meta.'cat_style_pages',
				'options'   => $cat_style,
				'std'       => 'simple_follow',
				'condition' => prefix_meta.'cats_tax:is('.wpqa_questions_type.'),'.prefix_meta.'cats_tax:is('.wpqa_knowledgebase_type.')',
				'operator'  => 'or',
				'type'      => 'radio'
			);
			
			$options[] = array(
				'name'    => esc_html__('Order by','wpqa'),
				'id'      => prefix_meta.'cat_sort',
				'std'     => "count",
				'type'    => 'radio',
				'options' => array(
					'count'     => esc_html__('Popular','wpqa'),
					'followers' => esc_html__('Followers - for question tags only','wpqa'),
					'name'      => esc_html__('Name','wpqa'),
				),
			);
			
			$options[] = array(
				'name'    => esc_html__('Order','wpqa'),
				'id'      => prefix_meta.'cat_order',
				'std'     => "DESC",
				'type'    => 'radio',
				'options' => array(
					'DESC' => esc_html__('Descending','wpqa'),
					'ASC'  => esc_html__('Ascending','wpqa'),
				),
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name'     => esc_html__('Tag settings','wpqa'),
				'id'       => 'tags_settings',
				'icon'     => 'tag',
				'type'     => 'heading',
				'template' => 'template-tags.php'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Show search at tags page','wpqa'),
				'desc' => esc_html__('Show search at tags page from the breadcrumb','wpqa'),
				'id'   => prefix_meta."tag_search",
				'type' => 'checkbox',
				'std'  => 'on',
			);
			
			$options[] = array(
				'name' => esc_html__('Show filter at tags page','wpqa'),
				'desc' => esc_html__('Show filter at tags page from the breadcrumb','wpqa'),
				'id'   => prefix_meta."tag_filter",
				'type' => 'checkbox',
				'std'  => 'on',
			);
			
			$options[] = array(
				'name' => esc_html__('Tags per page','wpqa'),
				'desc' => esc_html__('Put the tags per page.','wpqa'),
				'id'   => prefix_meta.'tags_per_page',
				'std'  => '50',
				'type' => 'text'
			);

			$tags_tax = array(
				wpqa_questions_type => esc_html__('Question tags','wpqa'),
				'post'              => esc_html__('Post tags','wpqa'),
			);

			if ($activate_knowledgebase == true) {
				$tags_tax[wpqa_knowledgebase_type] = esc_html__('Knowledgebase tags','wpqa');
			}
			
			$options[] = array(
				'name'    => esc_html__('Tags type','wpqa'),
				'id'      => prefix_meta.'tags_tax',
				'std'     => wpqa_questions_type,
				'type'    => 'radio',
				'options' => $tags_tax,
			);
			
			$options[] = array(
				'name'    => esc_html__('Order by','wpqa'),
				'id'      => prefix_meta.'tag_sort',
				'std'     => "count",
				'type'    => 'radio',
				'options' => array(
					'count'     => esc_html__('Popular','wpqa'),
					'followers' => esc_html__('Followers - for question tags only','wpqa'),
					'name'      => esc_html__('Name','wpqa'),
				),
			);
			
			$options[] = array(
				'name'    => esc_html__('Tags style','wpqa'),
				'desc'    => esc_html__('Choose the tags style.','wpqa'),
				'id'      => prefix_meta.'tag_style',
				'options' => array(
					'simple_follow' => esc_html__('Simple with follow - for question tags only','wpqa'),
					'advanced'      => esc_html__('Advanced','wpqa'),
					'simple'        => esc_html__('Simple','wpqa'),
				),
				'std'     => 'simple_follow',
				'type'    => 'radio'
			);
			
			$options[] = array(
				'name'    => esc_html__('Order','wpqa'),
				'id'      => prefix_meta.'tag_order',
				'std'     => "DESC",
				'type'    => 'radio',
				'options' => array(
					'DESC' => esc_html__('Descending','wpqa'),
					'ASC'  => esc_html__('Ascending','wpqa'),
				),
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name'     => esc_html__('Sitemap settings','wpqa'),
				'id'       => 'sitemaps_settings',
				'icon'     => 'portfolio',
				'type'     => 'heading',
				'template' => 'template-sitemaps.php'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Choose sections to show','wpqa'),
				'id'      => prefix_meta.'sitemaps_sections',
				'type'    => 'multicheck',
				'std'     => array("posts","archive_m","archive_y","categories","pages","authors","tags"),
				'options' => array(
					'posts'      => 'Latest Posts',
					'archive_m'  => 'Archive by Month',
					'archive_y'  => 'Archive by Year',
					'categories' => 'Categories',
					'pages'      => 'Pages',
					'authors'    => 'Authors',
					'tags'       => 'Tags',
				),
			);
			
			$options[] = array(
				'name'    => esc_html__('Toggle or accordion?','wpqa'),
				'id'      => prefix_meta."toggle_accordion",
				'type'    => 'select',
				'options' => array(
					'toggle'	=> esc_html__('Toggle','wpqa'),
					'accordion'	=> esc_html__('Accordion','wpqa'),
				),
				'std'     => 'toggle',
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);

			$options[] = array(
				'name'     => esc_html__('Badges & Point settings','wpqa'),
				'id'      => 'badges',
				'icon'    => 'star-filled',
				'type'     => 'heading',
				'template' => 'template-badges.php'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Points columns?','wpqa'),
				'id'      => prefix_meta."badges_points_columns",
				'type'    => 'radio',
				'options' => array(
					'2col'	=> esc_html__('2 columns','wpqa'),
					'3col'	=> esc_html__('3 columns','wpqa'),
				),
				'std'     => '2col',
			);
			
			$options[] = array(
				'name' => esc_html__('Buy points section enable or disable','wpqa'),
				'id'   => prefix_meta."buy_points_section",
				'type' => 'checkbox',
				'std'  => 'on',
			);

			$options = apply_filters(wpqa_prefix_theme.'_options_after_badges',$options);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name'     => esc_html__('Landing settings','wpqa'),
				'id'       => 'landing_settings',
				'icon'     => 'portfolio',
				'type'     => 'heading',
				'template' => 'template-landing.php'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);

			$options[] = array(
				'name' => esc_html__('Custom logo for the landing page','wpqa'),
				'desc' => esc_html__('Select ON to set the custom logo for the landing page.','wpqa'),
				'id'   => prefix_meta.'custom_logo',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'type'      => 'heading-2',
				'condition' => prefix_meta.'custom_logo:not(0)',
				'div'       => 'div'
			);

			$options[] = array(
				'name'    => esc_html__('Logo for the landing page','wpqa'),
				'id'      => prefix_meta.'logo_landing',
				'type'    => 'upload',
				'options' => array("height" => prefix_meta."logo_landing_height","width" => prefix_meta."logo_landing_width"),
			);

			$options[] = array(
				'name' => esc_html__('Logo retina for the landing popup','wpqa'),
				'id'   => prefix_meta.'logo_landing_retina',
				'type' => 'upload'
			);
			
			$options[] = array(
				'name' => esc_html__('Logo height','wpqa'),
				"id"   => prefix_meta."logo_landing_height",
				"type" => "sliderui",
				'std'  => '45',
				"step" => "1",
				"min"  => "0",
				"max"  => "80"
			);
			
			$options[] = array(
				'name' => esc_html__('Logo width','wpqa'),
				"id"   => prefix_meta."logo_landing_width",
				"type" => "sliderui",
				'std'  => '137',
				"step" => "1",
				"min"  => "0",
				"max"  => "170"
			);

			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end',
				'div'  => 'div'
			);
			
			$options[] = array(
				'name'    => esc_html__('Add from here the page to work when the landing page is selected at the home page, and the user is already logged in.','wpqa'),
				'id'      => prefix_meta.'home_page',
				'type'    => 'select',
				'options' => $options_pages
			);
			
			$options[] = array(
				'name'    => esc_html__('Form style','wpqa'),
				'desc'    => esc_html__('Choose the form style from here.','wpqa'),
				'id'      => prefix_meta.'form_style',
				'options' => array(
					'login'    => esc_html__('Login','wpqa'),
					'register' => esc_html__('Register','wpqa'),
				),
				'std'     => 'login',
				'type'    => 'radio'
			);

			$options[] = array(
				'name'    => esc_html__('Page style','wpqa'),
				'desc'    => esc_html__('Choose page style from here.','wpqa'),
				'id'      => prefix_meta.'register_style',
				'options' => array(
					'style_1' => 'Style 1',
					'style_2' => 'Style 2',
				),
				'std'     => 'style_1',
				'type'    => 'radio'
			);
			
			$options[] = array(
				'name'    => esc_html__('Upload the background','wpqa'),
				'desc'    => esc_html__('Upload the background for the un-register page','wpqa'),
				'id'      => prefix_meta.'register_background',
				'type'    => 'background',
				'options' => array('color' => '','image' => ''),
				'std'     => array(
					'color' => '#272930',
					'image' => $imagepath_theme."register.png"
				)
			);
			
			$options[] = array(
				"name" => esc_html__('Choose the background opacity','wpqa'),
				"desc" => esc_html__('Choose the background opacity from here','wpqa'),
				"id"   => prefix_meta."register_opacity",
				"type" => "sliderui",
				'std'  => 30,
				"step" => "5",
				"min"  => "0",
				"max"  => "100"
			);
			
			$options[] = array(
				'name'    => esc_html__("Choose from here which menu will show for un-registered users.","wpqa"),
				'id'      => prefix_meta.'register_menu',
				'type'    => 'select',
				'options' => $menus
			);
			
			$options[] = array(
				'name' => esc_html__('The headline','wpqa'),
				'desc' => esc_html__('Type the Headline from here','wpqa'),
				'id'   => prefix_meta.'register_headline',
				'type' => 'text',
				'std'  => "Join the world's  biggest Q & A network!"
			);
			
			$options[] = array(
				'name' => esc_html__('The paragraph','wpqa'),
				'desc' => esc_html__('Type the Paragraph from here','wpqa'),
				'id'   => prefix_meta.'register_paragraph',
				'type' => 'textarea',
				'std'  => "Login to our social questions & Answers Engine to ask questions answer people's questions & connect with other people."
			);

			if (has_himer() || has_knowly()) {
				$options[] = array(
					'name' => esc_html__('Big button on the header enable or disable?','wpqa'),
					'desc' => esc_html__('Select ON to enable the big button.','wpqa'),
					'id'   => prefix_meta.'register_big_button',
					'std'  => 'on',
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'register_big_button:not(0)',
					'type'      => 'heading-2'
				);
				
				$options[] = array(
					'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
					'id'      => prefix_meta.'register_big_button_target',
					'std'     => "new_page",
					'type'    => 'select',
					'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
				);
				
				$options[] = array(
					'name' => esc_html__('Type the button link','wpqa'),
					'id'   => prefix_meta.'register_big_button_link',
					'type' => 'text'
				);
				
				$options[] = array(
					'name' => esc_html__('Type the button text','wpqa'),
					'id'   => prefix_meta.'register_big_button_text',
					'type' => 'text'
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);

				$options[] = array(
					'name'    => esc_html__('First button','wpqa'),
					'desc'    => esc_html__('Choose button type.','wpqa'),
					'id'      => prefix_meta.'register_first_button',
					'options' => array(
						'off'    => esc_html__('OFF','wpqa'),
						'login'  => esc_html__('Login','wpqa'),
						'signup' => esc_html__('Signup','wpqa'),
					),
					'std'     => 'signup',
					'type'    => 'radio'
				);
				
				$options[] = array(
					'name' => esc_html__('Second button enable or disable?','wpqa'),
					'desc' => esc_html__('Select ON to enable the second button.','wpqa'),
					'id'   => prefix_meta.'register_second_button',
					'std'  => 'on',
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'register_second_button:not(0)',
					'type'      => 'heading-2'
				);
				
				$options[] = array(
					'name'    => esc_html__('Open the page in same page or a new page?','wpqa'),
					'id'      => prefix_meta.'register_second_button_target',
					'std'     => "new_page",
					'type'    => 'select',
					'options' => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
				);
				
				$options[] = array(
					'name' => esc_html__('Type the button link','wpqa'),
					'id'   => prefix_meta.'register_second_button_link',
					'type' => 'text'
				);
				
				$options[] = array(
					'name' => esc_html__('Type the button text','wpqa'),
					'id'   => prefix_meta.'register_second_button_text',
					'type' => 'text'
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);
			}
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);

			$options = apply_filters(wpqa_prefix_theme.'_options_after_landing',$options);
		}
		
		if (wpqa_is_post_type(array("post","page",wpqa_questions_type,wpqa_asked_questions_type,wpqa_knowledgebase_type))) {
			$options[] = array(
				'name' => esc_html__('Custom page settings','wpqa'),
				'id'   => 'custom_page_settings',
				'icon' => 'admin-page',
				'type' => 'heading'
			);
			
			if (wpqa_is_post_type(array("page","post"))) {
				$options[] = array(
					'type' => 'heading-2',
					'name' => esc_html__('Custom sections','wpqa')
				);
				
				$options[] = array(
					'name' => esc_html__('Choose a custom sections','wpqa'),
					'id'   => prefix_meta.'custom_sections',
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'custom_sections:not(0)',
					'type'      => 'heading-2'
				);
				
				if (wpqa_is_post_type(array("page"))) {
					$order_sections = array(
						"author"      => array("sort" => esc_html__('About the author','wpqa'),"value" => "author"),
						"advertising" => array("sort" => esc_html__('Advertising','wpqa'),"value" => "advertising"),
						"comments"    => array("sort" => esc_html__('Comments','wpqa'),"value" => "comments"),
					);
				}else {
					$order_sections = array(
						"author"        => array("sort" => esc_html__('About the author','wpqa'),"value" => "author"),
						"next_previous" => array("sort" => esc_html__('Next and Previous articles','wpqa'),"value" => "next_previous"),
						"advertising"   => array("sort" => esc_html__('Advertising','wpqa'),"value" => "advertising"),
						"related"       => array("sort" => esc_html__('Related articles','wpqa'),"value" => "related"),
						"comments"      => array("sort" => esc_html__('Comments','wpqa'),"value" => "comments"),
					);
				}
				
				$options[] = array(
					'name'    => esc_html__('Sort your sections','wpqa'),
					'id'      => prefix_meta.'order_sections',
					'type'    => 'multicheck',
					'sort'    => 'yes',
					'std'     => $order_sections,
					'options' => $order_sections
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'end'  => 'end'
				);
			}
			
			$options[] = array(
				'type' => 'heading-2',
				'name' => esc_html__('Custom page setting','wpqa')
			);
			
			$options[] = array(
				'name' => esc_html__('Choose a custom page setting','wpqa'),
				'id'   => prefix_meta.'custom_page_setting',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'custom_page_setting:not(0)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name'    => esc_html__('Sticky sidebar','wpqa'),
				'id'      => prefix_meta.'sticky_sidebar_s',
				'std'     => 'side_menu_bar',
				'type'    => 'radio',
				'options' => array(
					'sidebar'       => esc_html__('Sidebar','wpqa'),
					'nav_menu'      => esc_html__('Side menu (If enabled)','wpqa'),
					'side_menu_bar' => esc_html__('Sidebar & Side menu (If enabled)','wpqa'),
					'no_sidebar'    => esc_html__('Not active','wpqa'),
				)
			);
			
			$options[] = array(
				'name' => esc_html__('Breadcrumbs','wpqa'),
				'desc' => esc_html__('Select ON to enable the breadcrumbs.','wpqa'),
				'id'   => prefix_meta.'breadcrumbs',
				'std'  => 'on',
				'type' => 'checkbox'
			);
			
			if (wpqa_is_post_type(array("post","page"))) {
				$options[] = array(
					'name' => esc_html__('Hide the featured image in the single page','wpqa'),
					'desc' => esc_html__('Select ON to hide the featured image in the single page.','wpqa'),
					'id'   => prefix_meta.'featured_image',
					'type' => 'checkbox'
				);
			}
			
			if (wpqa_is_post_type(array("page"))) {
				$options[] = array(
					'name' => esc_html__('Title enable or disable','wpqa'),
					'id'   => prefix_meta.'post_title',
					'std'  => "on",
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'name'      => esc_html__('Title style','wpqa'),
					'desc'      => esc_html__('Choose title style from here.','wpqa'),
					'id'        => prefix_meta.'post_title_style',
					'std'       => 'style_1',
					'options'   => array(
						'style_1' => 'Style 1',
						'style_2' => 'Style 2',
					),
					'condition' => prefix_meta.'post_title:not(0)',
					'type'      => 'radio'
				);
				
				$options[] = array(
					'name'      => esc_html__('Title icon','wpqa'),
					'desc'      => esc_html__('Type the title icon from here like "icon-mail".','wpqa'),
					'id'        => prefix_meta.'post_title_icon',
					'type'      => 'text',
					'condition' => prefix_meta.'post_title:not(0),'.prefix_meta.'post_title_style:is(style_2)'
				);
			}
			
			if (wpqa_is_post_type(array(wpqa_questions_type,wpqa_asked_questions_type))) {
				$options[] = array(
					'name' => esc_html__('Activate the author image in single?','wpqa'),
					'desc' => esc_html__('Author image in single enable or disable.','wpqa'),
					'id'   => prefix_meta.'author_image_single',
					'std'  => "on",
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'name' => esc_html__('Activate the vote in single?','wpqa'),
					'desc' => esc_html__('Vote in single enable or disable.','wpqa'),
					'id'   => prefix_meta.'vote_question_single',
					'std'  => "on",
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'name'      => esc_html__('Select ON to hide the dislike at questions single','wpqa'),
					'desc'      => esc_html__('If you put it ON the dislike will not show.','wpqa'),
					'id'        => prefix_meta.'question_single_dislike',
					'condition' => prefix_meta.'vote_question_single:not(0)',
					'type'      => 'checkbox'
				);
				
				$options[] = array(
					'name' => esc_html__('Activate close and open questions','wpqa'),
					'desc' => esc_html__('Select ON if you want activate close and open questions.','wpqa'),
					'id'   => prefix_meta.'question_close',
					'std'  => "on",
					'type' => 'checkbox'
				);
			}
			
			if (wpqa_is_post_type(array("post",wpqa_questions_type,wpqa_asked_questions_type,wpqa_knowledgebase_type))) {
				$options[] = array(
					'name' => esc_html__('Tags enable or disable','wpqa'),
					'id'   => prefix_meta.'post_tags',
					'std'  => "on",
					'type' => 'checkbox'
				);
			}

			if ((has_himer() || has_knowly()) && wpqa_is_post_type(array("post"))) {
				$options[] = array(
					'name' => esc_html__('Newsletter enable or disable','wpqa'),
					'id'   => prefix_meta.'newsletter_blog',
					'std'  => 'on',
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'name'      => esc_html__('Newsletter action','wpqa'),
					'id'        => prefix_meta.'newsletter_action',
					'condition' => prefix_meta.'newsletter_blog:is(on)',
					'type'      => 'text'
				);
			}
			
			if (wpqa_is_post_type(array("post",wpqa_questions_type,wpqa_asked_questions_type,wpqa_knowledgebase_type))) {
				if (wpqa_is_post_type(array(wpqa_questions_type,wpqa_asked_questions_type))) {
					if (wpqa_is_post_type(array(wpqa_asked_questions_type))) {
						$meta_std = array(
							"author_by"       => "author_by",
							"post_date"       => "post_date",
							"asked_to"        => "asked_to",
							"question_views"  => "question_views",
							"question_answer" => "question_answer",
						);
						
						$meta_options = array(
							"author_by"       => esc_html__('Author by','wpqa'),
							"post_date"       => esc_html__('Date meta','wpqa'),
							"asked_to"        => esc_html__('Asked to','wpqa'),
							"question_answer" => esc_html__('Answer meta','wpqa'),
							"question_views"  => esc_html__('Views stats','wpqa'),
						);
					}else {
						$meta_std = array(
							"author_by"       => "author_by",
							"post_date"       => "post_date",
							"category_post"   => "category_post",
							"question_views"  => "question_views",
							"question_answer" => "question_answer",
						);
						
						$meta_options = array(
							"author_by"       => esc_html__('Author by','wpqa'),
							"post_date"       => esc_html__('Date meta','wpqa'),
							"category_post"   => esc_html__('Category question','wpqa'),
							"question_answer" => esc_html__('Answer meta','wpqa'),
							"question_views"  => esc_html__('Views stats','wpqa'),
						);
					}
				}else if (wpqa_is_post_type(array(wpqa_knowledgebase_type))) {
					$meta_std = array(
						"author_by"              => "author_by",
						"knowledgebase_date"     => "knowledgebase_date",
						"category_knowledgebase" => "category_knowledgebase",
						"knowledgebase_views"    => "knowledgebase_views",
						"knowledgebase_votes"    => "knowledgebase_votes",
						"knowledgebase_read"     => "knowledgebase_read",
						"knowledgebase_print"    => "knowledgebase_print",
					);
					
					$meta_options = array(
						"author_by"              => esc_html__('Author by','wpqa'),
						"knowledgebase_date"     => esc_html__('Date meta','wpqa'),
						"category_knowledgebase" => esc_html__('Category knowledgebase','wpqa'),
						"knowledgebase_views"    => esc_html__('Views stats','wpqa'),
						"knowledgebase_votes"    => esc_html__('Votes','wpqa'),
						"knowledgebase_read"     => esc_html__('Read time','wpqa'),
						"knowledgebase_print"    => esc_html__('Print','wpqa'),
					);
				}else {
					$meta_std = $post_meta_std;
					
					$meta_options = array(
						"category_post" => esc_html__('Category post','wpqa'),
						"title_post"    => esc_html__('Title post','wpqa'),
						"author_by"     => esc_html__('Author by','wpqa'),
						"post_date"     => esc_html__('Date meta','wpqa'),
						"post_comment"  => esc_html__('Comment meta','wpqa'),
						"post_views"    => esc_html__("Views stats","wpqa"),
					);
				}
				
				$options[] = array(
					'name'    => esc_html__('Select the meta options','wpqa'),
					'id'      => prefix_meta.'post_meta',
					'type'    => 'multicheck',
					'std'     => $meta_std,
					'options' => $meta_options
				);
			}
			
			if (wpqa_is_post_type(array(wpqa_questions_type,wpqa_asked_questions_type))) {
				$options[] = array(
					'name' => esc_html__('Activate user can add the question to favorites','wpqa'),
					'desc' => esc_html__('Select ON if you want the user can add the questions to favorites.','wpqa'),
					'id'   => prefix_meta.'question_favorite',
					'std'  => "on",
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'name' => esc_html__('Activate user can follow the questions','wpqa'),
					'desc' => esc_html__('Select ON if you want the user can follow the questions.','wpqa'),
					'id'   => prefix_meta.'question_follow',
					'std'  => "on",
					'type' => 'checkbox'
				);
			}
			
			$options[] = array(
				'name'    => esc_html__('Select the share options','wpqa'),
				'id'      => prefix_meta.'post_share',
				'type'    => 'multicheck',
				'sort'    => 'yes',
				'std'     => $share_array,
				'options' => $share_array
			);
			
			if (wpqa_is_post_type(array(wpqa_questions_type,wpqa_asked_questions_type))) {
				$options[] = array(
					'name' => esc_html__('Related questions','wpqa'),
					'type' => 'info'
				);

				$options[] = array(
					'name' => esc_html__('Navigation enable or disable','wpqa'),
					'desc' => esc_html__('Navigation ( next and previous ) enable or disable.','wpqa'),
					'id'   => prefix_meta.'post_navigation',
					'std'  => "on",
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'name'      => esc_html__('Navigation question for the same category only?','wpqa'),
					'desc'      => esc_html__('Navigation question (next and previous questions) for the same category only?','wpqa'),
					'id'        => prefix_meta.'question_nav_category',
					'condition' => prefix_meta.'post_navigation:not(0)',
					'std'       => 'on',
					'type'      => 'checkbox'
				);
				
				$options[] = array(
					'name' => esc_html__('Answers enable or disable','wpqa'),
					'desc' => esc_html__('Answers enable or disable.','wpqa'),
					'id'   => prefix_meta.'post_comments',
					'std'  => "on",
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'name' => esc_html__('Related questions after content enable or disable','wpqa'),
					'desc' => esc_html__('Select ON if you want to activate the related questions after the content.','wpqa'),
					'id'   => prefix_meta.'question_related',
					'type' => 'checkbox'
				);
			}
			
			if (wpqa_is_post_type(array(wpqa_knowledgebase_type))) {
				$options[] = array(
					'name' => esc_html__('Didn\'t find answer section','wpqa'),
					'type' => 'info'
				);
				
				$options[] = array(
					'name' => esc_html__('Didn\'t find answer section enable or disable','wpqa'),
					'id'   => prefix_meta.'didnt_find_answer',
					'std'  => 'on',
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'didnt_find_answer:not(0)',
					'type'      => 'heading-2'
				);
				
				$options[] = array(
					'name'    => esc_html__('Didn\'t find answer style','wpqa'),
					'id'      => prefix_meta.'didnt_find_answer_style',
					'options' => array(
						'style_1' => esc_html__('Style 1 - buttons for yes and no','wpqa'),
						'style_2' => esc_html__('Style 2 - buttons for yes and no (The same of style 1 but with small change on the title)','wpqa'),
						'style_3' => esc_html__('Style 3 - react rate','wpqa'),
						'style_4' => esc_html__('Style 4 - react rate','wpqa'),
					),
					'std'     => 'style_1',
					'type'    => 'radio'
				);
				
				$options[] = array(
					'name' => esc_html__('Didn\'t find answer link enable or disable','wpqa'),
					'desc' => esc_html__('Didn\'t find answer link to set page for it.','wpqa'),
					'id'   => prefix_meta.'didnt_find_answer_link',
					'std'  => 'on',
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'didnt_find_answer_link:not(0)',
					'type'      => 'heading-2'
				);
				
				$options[] = array(
					'name'    => esc_html__('Didn\'t find answer page','wpqa'),
					'id'      => prefix_meta.'didnt_find_answer_page',
					'options' => array(
						'contact'  => esc_html__('Contact page (If you activate it will assign it)','wpqa'),
						'question' => esc_html__('Ask question page','wpqa'),
						'custom'   => esc_html__('Custom link','wpqa'),
					),
					'std'     => 'contact',
					'type'    => 'radio'
				);
				
				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'didnt_find_answer_page:is(custom)',
					'type'      => 'heading-2'
				);
				
				$options[] = array(
					'name' => esc_html__("Type the link if you don't like above","wpqa"),
					'id'   => prefix_meta.'didnt_find_answer_custom',
					'type' => 'text'
				);
				
				$options[] = array(
					'name' => esc_html__("Type the text of the link","wpqa"),
					'id'   => prefix_meta.'didnt_find_answer_text',
					'type' => 'text'
				);

				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);

				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);

				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);
				
				$options[] = array(
					'name' => esc_html__('Related articles','wpqa'),
					'type' => 'info'
				);

				$options[] = array(
					'name'    => esc_html__('Navigation enable or disable','wpqa'),
					'desc'    => esc_html__('Navigation ( next and previous ) enable or disable.','wpqa'),
					'id'      => prefix_meta.'post_navigation',
					'type'    => 'multicheck',
					'std'     => array(
						"breadcrumbs"   => "breadcrumbs",
						"after_content" => "after_content",
					),
					'options' => array(
						"breadcrumbs"   => esc_html__('Breadcrumbs','wpqa'),
						"after_content" => esc_html__("After content","wpqa"),
					)
				);
				
				$options[] = array(
					'name'      => esc_html__('Navigation knowledgebase for the same category only?','wpqa'),
					'desc'      => esc_html__('Navigation knowledgebase (next and previous knowledgebases) for the same category only?','wpqa'),
					'id'        => prefix_meta.'knowledgebase_nav_category',
					'condition' => prefix_meta.'post_navigation:has(breadcrumbs),'.prefix_meta.'post_navigation:has(after_content)',
					'operator'  => 'or',
					'std'       => 'on',
					'type'      => 'checkbox'
				);
				
				$options[] = array(
					'name' => esc_html__('Related knowledgebases after content enable or disable','wpqa'),
					'desc' => esc_html__('Select ON if you want to activate the related knowledgebases after the content.','wpqa'),
					'id'   => prefix_meta.'knowledgebase_related',
					'type' => 'checkbox'
				);
			}
			
			if (wpqa_is_post_type(array("post"))) {
				$options[] = array(
					'name'      => esc_html__('Navigation post for the same category only?','wpqa'),
					'desc'      => esc_html__('Navigation post (next and previous posts) for the same category only?','wpqa'),
					'id'        => prefix_meta.'post_nav_category',
					'condition' => prefix_meta.'order_sections:has(next_previous)',
					'std'       => 'on',
					'type'      => 'checkbox'
				);
			}
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			if (wpqa_is_post_type(array("post"))) {
				$options[] = array(
					'type'      => 'heading-2',
					'condition' => prefix_meta.'custom_page_setting:not(0),'.prefix_meta.'order_sections:has(related)',
					'name'      => esc_html__('Related posts','wpqa')
				);
				
				$options[] = array(
					'name'    => esc_html__('Related style','wpqa'),
					'desc'    => esc_html__('Choose related style from here.','wpqa'),
					'id'      => prefix_meta.'related_style',
					'std'     => 'style_1',
					'options' => array(
						'style_1' => 'Style 1',
						'links'   => 'Style 2',
					),
					'type'    => 'radio'
				);
				
				$options[] = array(
					'name' => esc_html__('Related posts number','wpqa'),
					'desc' => esc_html__('Type related posts number from here','wpqa'),
					'id'   => prefix_meta.'related_number',
					'std'  => 4,
					'type' => 'text'
				);
				
				$options[] = array(
					'name'    => esc_html__('Query type','wpqa'),
					'desc'    => esc_html__('Select what will the related posts show.','wpqa'),
					'id'      => prefix_meta.'query_related',
					'std'     => 'categories',
					'options' => array(
						'categories' => esc_html__('Posts in the same categories','wpqa'),
						'tags'       => esc_html__('Posts in the same tags (If not find any tags will show by the same categories)','wpqa'),
						'author'     => esc_html__('Posts by the same author','wpqa'),
					),
					'type'    => 'radio'
				);
				
				$options[] = array(
					'name' => esc_html__('Excerpt title in related','wpqa'),
					'desc' => esc_html__('Type excerpt title in related from here.','wpqa'),
					'id'   => prefix_meta.'excerpt_related_title',
					'std'  => 10,
					'type' => 'text'
				);
				
				$options[] = array(
					'name'      => esc_html__('Comment in related enable or disable','wpqa'),
					'id'        => prefix_meta.'comment_in_related',
					'std'       => "on",
					'condition' => prefix_meta.'related_style:is(style_1)',
					'type'      => 'checkbox'
				);
				
				$options[] = array(
					'name'      => esc_html__('Date in related enable or disable','wpqa'),
					'id'        => prefix_meta.'date_in_related',
					'std'       => "on",
					'condition' => prefix_meta.'related_style:is(style_1)',
					'type'      => 'checkbox'
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'end'  => 'end'
				);
			}

			if (wpqa_is_post_type(array(wpqa_questions_type,wpqa_asked_questions_type))) {
				$options[] = array(
					'type'      => 'heading-2',
					'condition' => prefix_meta.'custom_page_setting:not(0),'.prefix_meta.'question_related:not(0)',
					'name'      => esc_html__('Related questions','wpqa')
				);
				
				$options[] = array(
					'name'    => esc_html__('Related position','wpqa'),
					'desc'    => esc_html__('Choose the related position from here.','wpqa'),
					'id'      => prefix_meta.'question_related_position',
					'std'     => 'after_content',
					'options' => array(
						'after_content' => esc_html__('After content','wpqa'),
						'after_answers' => esc_html__('After answers','wpqa'),
					),
					'type'    => 'radio'
				);
				
				$options[] = array(
					'name' => esc_html__('Related questions number','wpqa'),
					'desc' => esc_html__('Type the number of related questions from here','wpqa'),
					'id'   => prefix_meta.'related_number',
					'std'  => 5,
					'type' => 'text'
				);
				
				$options[] = array(
					'name'    => esc_html__('Query type','wpqa'),
					'desc'    => esc_html__('Select how many related questions will show.','wpqa'),
					'id'      => prefix_meta.'query_related',
					'std'     => 'categories',
					'options' => array(
						'categories' => esc_html__('Questions in the same categories','wpqa'),
						'tags'       => esc_html__('Questions in the same tags (If not found, questions with the same categories will be shown)','wpqa'),
						'author'     => esc_html__('Questions by the same author','wpqa'),
					),
					'type'    => 'radio'
				);
				
				$options[] = array(
					'name' => esc_html__('Excerpt title in related','wpqa'),
					'desc' => esc_html__('Type excerpt title in related from here.','wpqa'),
					'id'   => prefix_meta.'excerpt_related_title',
					'std'  => 20,
					'type' => 'text'
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'end'  => 'end'
				);
			}
			
			if (wpqa_is_post_type(array(wpqa_knowledgebase_type))) {
				$options[] = array(
					'type'      => 'heading-2',
					'condition' => prefix_meta.'custom_page_setting:not(0),'.prefix_meta.'knowledgebase_related:not(0)',
					'name'      => esc_html__('Related knowledgebases','wpqa')
				);
				
				$options[] = array(
					'name' => esc_html__('Related knowledgebases number','wpqa'),
					'desc' => esc_html__('Type the number of related knowledgebases from here','wpqa'),
					'id'   => prefix_meta.'related_number',
					'std'  => 5,
					'type' => 'text'
				);
				
				$options[] = array(
					'name'    => esc_html__('Query type','wpqa'),
					'desc'    => esc_html__('Select how many related knowledgebases will show.','wpqa'),
					'id'      => prefix_meta.'query_related',
					'std'     => 'categories',
					'options' => array(
						'categories' => esc_html__('Knowledgebases in the same categories','wpqa'),
						'tags'       => esc_html__('Knowledgebases in the same tags (If not found, knowledgebases with the same categories will be shown)','wpqa'),
						'author'     => esc_html__('Knowledgebases by the same author','wpqa'),
					),
					'type'    => 'radio'
				);
				
				$options[] = array(
					'name' => esc_html__('Excerpt title in related','wpqa'),
					'desc' => esc_html__('Type excerpt title in related from here.','wpqa'),
					'id'   => prefix_meta.'excerpt_related_title',
					'std'  => 20,
					'type' => 'text'
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'end'  => 'end'
				);
			}
		}
		
		if (wpqa_is_post_type(array("post","page",wpqa_questions_type,wpqa_asked_questions_type,wpqa_knowledgebase_type))) {
			$options[] = array(
				'name' => esc_html__('Advertising','wpqa'),
				'id'   => 'advertising',
				'icon' => 'admin-post',
				'type' => 'heading'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'name' => esc_html__('Advertising after header 1','wpqa')
			);
			
			$options[] = array(
				'name'    => esc_html__('Advertising type','wpqa'),
				'id'      => prefix_meta.'header_adv_type_1',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','wpqa'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
				'id'        => prefix_meta.'header_adv_img_1',
				'condition' => prefix_meta.'header_adv_type_1:is(custom_image)',
				'type'      => 'upload'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising url','wpqa'),
				'id'        => prefix_meta.'header_adv_href_1',
				'std'       => '#',
				'condition' => prefix_meta.'header_adv_type_1:is(custom_image)',
				'type'      => 'text'
			);

			$options[] = array(
				'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'        => prefix_meta.'header_adv_link_1',
				'std'       => "new_page",
				'type'      => 'select',
				'condition' => prefix_meta.'header_adv_type_1:is(custom_image)',
				'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
				'id'        => prefix_meta.'header_adv_code_1',
				'condition' => prefix_meta.'header_adv_type_1:is(display_code)',
				'type'      => 'textarea'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'name' => esc_html__('Advertising inner single page sections','wpqa')
			);
			
			$options[] = array(
				'name'    => esc_html__('Advertising type','wpqa'),
				'id'      => prefix_meta.'share_adv_type',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','wpqa'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
				'id'        => prefix_meta.'share_adv_img',
				'type'      => 'upload',
				'condition' => prefix_meta.'share_adv_type:is(custom_image)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising url','wpqa'),
				'id'        => prefix_meta.'share_adv_href',
				'std'       => '#',
				'type'      => 'text',
				'condition' => prefix_meta.'share_adv_type:is(custom_image)'
			);

			$options[] = array(
				'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'        => prefix_meta.'share_adv_link',
				'std'       => "new_page",
				'type'      => 'select',
				'condition' => prefix_meta.'share_adv_type:is(custom_image)',
				'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
				'id'        => prefix_meta.'share_adv_code',
				'type'      => 'textarea',
				'condition' => prefix_meta.'share_adv_type:is(display_code)'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);

			$options[] = array(
				'type' => 'heading-2',
				'name' => esc_html__('Advertising after left menu','wpqa')
			);
			
			$options[] = array(
				'name'    => esc_html__('Advertising type','wpqa'),
				'id'      => prefix_meta.'left_menu_adv_type',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','wpqa'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
				'id'        => prefix_meta.'left_menu_adv_img',
				'type'      => 'upload',
				'condition' => prefix_meta.'left_menu_adv_type:is(custom_image)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising url','wpqa'),
				'id'        => prefix_meta.'left_menu_adv_href',
				'std'       => '#',
				'type'      => 'text',
				'condition' => prefix_meta.'left_menu_adv_type:is(custom_image)'
			);

			$options[] = array(
				'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'        => prefix_meta.'left_menu_adv_link',
				'std'       => "new_page",
				'type'      => 'select',
				'condition' => prefix_meta.'left_menu_adv_type:is(custom_image)',
				'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
				'id'        => prefix_meta.'left_menu_adv_code',
				'type'      => 'textarea',
				'condition' => prefix_meta.'left_menu_adv_type:is(display_code)'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'name' => esc_html__('Advertising after content','wpqa')
			);
			
			$options[] = array(
				'name'    => esc_html__('Advertising type','wpqa'),
				'id'      => prefix_meta.'content_adv_type',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','wpqa'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
				'id'        => prefix_meta.'content_adv_img',
				'type'      => 'upload',
				'condition' => prefix_meta.'content_adv_type:is(custom_image)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising url','wpqa'),
				'id'        => prefix_meta.'content_adv_href',
				'std'       => '#',
				'type'      => 'text',
				'condition' => prefix_meta.'content_adv_type:is(custom_image)'
			);

			$options[] = array(
				'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'        => prefix_meta.'content_adv_link',
				'std'       => "new_page",
				'type'      => 'select',
				'condition' => prefix_meta.'content_adv_type:is(custom_image)',
				'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
				'id'        => prefix_meta.'content_adv_code',
				'type'      => 'textarea',
				'condition' => prefix_meta.'content_adv_type:is(display_code)'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
		}
		
		if (wpqa_is_post_type(array("page","post",wpqa_questions_type,wpqa_asked_questions_type,wpqa_knowledgebase_type))) {
			$options[] = array(
				'name' => esc_html__('Custom CSS code','wpqa'),
				'id'   => 'css_meta',
				'icon' => 'editor-code',
				'type' => 'heading'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Custom CSS','wpqa'),
				'desc' => esc_html__('Put the Custom CSS.','wpqa'),
				'id'   => prefix_meta.'footer_css',
				'rows' => 10,
				'type' => 'textarea'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
		}

		if (wpqa_is_post_type(array("checkout"))) {
			$options[] = array(
				'name' => esc_html__('Checkout settings','wpqa'),
				'id'   => 'checkout_meta',
				'icon' => 'tickets-alt',
				'type' => 'heading'
			);
			
			$options[] = array(
				'type' => 'heading-2'
			);

			$options[] = array(
				'name'     => esc_html__('Payment link','wpqa'),
				'readonly' => 'readonly',
				'unset'    => 'unset',
				'type'     => 'text',
				'id'       => 'checkout_link',
				'val'      => wpqa_checkout_link("pay",$post_id),
				'copy'     => 'copy',
			);

			$options[] = array(
				'name' => esc_html__("You can use it like that:","wpqa")."<br>[wpqa_payment id='".$post_id."']Pay to see it[/wpqa_payment]<br>[wpqa_payment id='".$post_id."']Pay to see it[/else_wpqa_payment]You can see it without payment[/wpqa_payment]",
				'type' => 'info'
			);

			$options[] = array(
				'name'     => esc_html__('Shortcode','wpqa'),
				'readonly' => 'readonly',
				'unset'    => 'unset',
				'type'     => 'text',
				'id'       => 'checkout_shortcode',
				'val'      => '[wpqa_payment id="'.$post_id.'"]Pay to see it[/wpqa_payment]',
				'copy'     => 'copy',
			);

			$options[] = array(
				'name' => esc_html__('Name of the button?','wpqa'),
				'id'   => prefix_meta.'button_name',
				'std'  => 'Pay now',
				'type' => 'text'
			);

			$options[] = array(
				'name'    => esc_html__('When a user gets paid, what kind of actions do you want them to take?','wpqa'),
				'desc'    => esc_html__('Choose the type of actions.','wpqa'),
				'id'      => prefix_meta.'payment_action',
				'std'     => 'role',
				'type'    => 'radio',
				'options' => array("role" => esc_html__("Get a new role","wpqa"),"download" => esc_html__("Download a file or product","wpqa"))
			);

			$payment_group = $wpqa_options_roles;
			unset($payment_group["administrator"]);

			$options[] = array(
				'name'      => esc_html__('Paid role','wpqa'),
				'desc'      => esc_html__('Select the paid role','wpqa'),
				'id'        => prefix_meta.'payment_role',
				'condition' => prefix_meta.'payment_action:is(role)',
				'std'       => 'author',
				'type'      => 'select',
				'options'   => $payment_group
			);

			$options[] = array(
				'name'      => esc_html__('Upload the file or the product you want the user to download it after buying the service','wpqa'),
				'id'        => prefix_meta.'payment_file',
				'condition' => prefix_meta.'payment_action:is(download)',
				'type'      => 'upload',
			);
			
			$options[] = array(
				'name'    => esc_html__('Type of payment','wpqa'),
				'desc'    => esc_html__('Choose the type of payment.','wpqa'),
				'id'      => prefix_meta.'payment_kind',
				'std'     => 'payment',
				'type'    => 'radio',
				'options' => array("payment" => esc_html__("One time payment","wpqa"),"subscribe" => esc_html__("Subscribe payment","wpqa"))
			);

			$options[] = array(
				'name'    => esc_html__('Payment way','wpqa'),
				'desc'    => esc_html__('Choose the payment way for the subscriptions','wpqa'),
				'id'      => prefix_meta.'payment_type',
				'std'     => 'payments',
				'type'    => 'radio',
				'options' => array(
					"payments"        => esc_html__('Payment methods','wpqa'),
					"points"          => esc_html__('By points','wpqa'),
					"payments_points" => esc_html__('Payment methods and points','wpqa')
				)
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'payment_kind:is(payment)',
				'type'      => 'heading-2'
			);

			if ($activate_currencies != "on") {
				$options[] = array(
					"name"      => esc_html__("What's the price to pay?","wpqa"),
					"desc"      => esc_html__("Type here price to pay","wpqa"),
					"id"        => prefix_meta."payment_price",
					"type"      => "text",
					'condition' => prefix_meta.'payment_type:not(points)',
					'std'       => 10
				);
			}else {
				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'payment_type:not(points)',
					'type'      => 'heading-2'
				);
				
				if (is_array($multi_currencies) && !empty($multi_currencies)) {
					$options[] = array(
						'name' => esc_html__("What's the price to pay?","wpqa"),
						'type' => 'info'
					);
					foreach ($multi_currencies as $key_currency => $value_currency) {
						if ($value_currency != "0") {
							$options[] = array(
								"name" => esc_html__("Price for","wpqa")." ".$value_currency,
								"id"   => prefix_meta."payment_price_".strtolower($value_currency),
								"type" => "text",
								'std'  => 10
							);
						}
					}
				}

				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);
			}

			$options[] = array(
				"name"      => esc_html__("How many points to pay?","wpqa"),
				"desc"      => esc_html__("Type here points of the payment to pay","wpqa"),
				"id"        => prefix_meta."payment_points",
				"type"      => "text",
				'condition' => prefix_meta.'payment_type:has(points),'.prefix_meta.'payment_type:has(payments_points)',
				'operator'  => 'or',
				'std'       => 20
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'payment_kind:is(subscribe)',
				'type'      => 'heading-2'
			);

			$options[] = array(
				'name' => esc_html__('Cancel the subscription','wpqa'),
				'desc' => esc_html__('Select ON to active the cancel subscription button for the users.','wpqa'),
				'id'   => prefix_meta.'cancel_subscription',
				'type' => 'checkbox'
			);

			$options[] = array(
				'name'    => esc_html__('Select the options for the subscriptions','wpqa'),
				'id'      => prefix_meta.'subscriptions_options',
				'type'    => 'radio',
				'std'     => array(
					"monthly"  => "monthly",
					"3months"  => "3months",
					"6months"  => "6months",
					"yearly"   => "yearly",
					"lifetime" => "lifetime",
				),
				'options' => array(
					"monthly"  => esc_html__('Monthly','wpqa'),
					"3months"  => esc_html__('Three months','wpqa'),
					"6months"  => esc_html__('Six months','wpqa'),
					"yearly"   => esc_html__('Yearly','wpqa'),
					"lifetime" => esc_html__('Lifetime','wpqa'),
				)
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'payment_type:has(payments),'.prefix_meta.'payment_type:has(payments_points)',
				'operator'  => 'or',
				'type'      => 'heading-2'
			);

			if ($activate_currencies != "on") {
				$options[] = array(
					"name"      => esc_html__("What's the price to subscribe monthly?",'wpqa'),
					"id"        => prefix_meta."subscribe_monthly",
					"type"      => "text",
					'condition' => prefix_meta.'subscriptions_options:is(monthly)',
					'std'       => 10
				);

				$options[] = array(
					"name"      => esc_html__("What's the price to subscribe for three months?",'wpqa'),
					"id"        => prefix_meta."subscribe_3months",
					"type"      => "text",
					'condition' => prefix_meta.'subscriptions_options:is(3months)',
					'std'       => 25
				);

				$options[] = array(
					"name"      => esc_html__("What's the price to subscribe for six months?",'wpqa'),
					"id"        => prefix_meta."subscribe_6months",
					"type"      => "text",
					'condition' => prefix_meta.'subscriptions_options:is(6months)',
					'std'       => 45
				);

				$options[] = array(
					"name"      => esc_html__("What's the price to subscribe yearly?",'wpqa'),
					"id"        => prefix_meta."subscribe_yearly",
					"type"      => "text",
					'condition' => prefix_meta.'subscriptions_options:is(yearly)',
					'std'       => 80
				);

				$options[] = array(
					"name"      => esc_html__("What's the price to subscribe for two years?",'wpqa'),
					"id"        => prefix_meta."subscribe_2years",
					"type"      => "text",
					'condition' => prefix_meta.'subscriptions_options:is(2years)',
					'std'       => 80
				);

				$options[] = array(
					"name"      => esc_html__("What's the price to subscribe lifetime?",'wpqa'),
					"id"        => prefix_meta."subscribe_lifetime",
					"type"      => "text",
					'condition' => prefix_meta.'subscriptions_options:is(lifetime)',
					'std'       => 200
				);
			}else {
				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'subscriptions_options:is(monthly)',
					'type'      => 'heading-2'
				);
				
				if (is_array($multi_currencies) && !empty($multi_currencies)) {
					$options[] = array(
						'name' => esc_html__("What's the price to subscribe monthly?","wpqa"),
						'type' => 'info'
					);
					foreach ($multi_currencies as $key_currency => $value_currency) {
						if ($value_currency != "0") {
							$options[] = array(
								"name" => esc_html__("Price for","wpqa")." ".$value_currency,
								"id"   => prefix_meta."subscribe_monthly_".strtolower($value_currency),
								"type" => "text",
								'std'  => 10
							);
						}
					}
				}

				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);
				
				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'subscriptions_options:is(3months)',
					'type'      => 'heading-2'
				);
				
				if (is_array($multi_currencies) && !empty($multi_currencies)) {
					$options[] = array(
						'name' => esc_html__("What's the price to subscribe three months?","wpqa"),
						'type' => 'info'
					);
					foreach ($multi_currencies as $key_currency => $value_currency) {
						if ($value_currency != "0") {
							$options[] = array(
								"name" => esc_html__("Price for","wpqa")." ".$value_currency,
								"id"   => prefix_meta."subscribe_3months_".strtolower($value_currency),
								"type" => "text",
								'std'  => 25
							);
						}
					}
				}

				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);
				
				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'subscriptions_options:is(6months)',
					'type'      => 'heading-2'
				);
				
				if (is_array($multi_currencies) && !empty($multi_currencies)) {
					$options[] = array(
						'name' => esc_html__("What's the price to subscribe six months?","wpqa"),
						'type' => 'info'
					);
					foreach ($multi_currencies as $key_currency => $value_currency) {
						if ($value_currency != "0") {
							$options[] = array(
								"name" => esc_html__("Price for","wpqa")." ".$value_currency,
								"id"   => prefix_meta."subscribe_6months_".strtolower($value_currency),
								"type" => "text",
								'std'  => 45
							);
						}
					}
				}

				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);
				
				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'subscriptions_options:is(yearly)',
					'type'      => 'heading-2'
				);
				
				if (is_array($multi_currencies) && !empty($multi_currencies)) {
					$options[] = array(
						'name' => esc_html__("What's the price to subscribe yearly?","wpqa"),
						'type' => 'info'
					);
					foreach ($multi_currencies as $key_currency => $value_currency) {
						if ($value_currency != "0") {
							$options[] = array(
								"name" => esc_html__("Price for","wpqa")." ".$value_currency,
								"id"   => prefix_meta."subscribe_yearly_".strtolower($value_currency),
								"type" => "text",
								'std'  => 80
							);
						}
					}
				}

				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);

				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'subscriptions_options:is(2years)',
					'type'      => 'heading-2'
				);
				
				if (is_array($multi_currencies) && !empty($multi_currencies)) {
					$options[] = array(
						'name' => esc_html__("What's the price to subscribe for two years?","wpqa"),
						'type' => 'info'
					);
					foreach ($multi_currencies as $key_currency => $value_currency) {
						if ($value_currency != "0") {
							$options[] = array(
								"name" => esc_html__("Price for","wpqa")." ".$value_currency,
								"id"   => prefix_meta."subscribe_2years_".strtolower($value_currency),
								"type" => "text",
								'std'  => 80
							);
						}
					}
				}

				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);

				$options[] = array(
					'div'       => 'div',
					'condition' => prefix_meta.'subscriptions_options:is(lifetime)',
					'type'      => 'heading-2'
				);
				
				if (is_array($multi_currencies) && !empty($multi_currencies)) {
					$options[] = array(
						'name' => esc_html__("What's the price to subscribe lifetime?","wpqa"),
						'type' => 'info'
					);
					foreach ($multi_currencies as $key_currency => $value_currency) {
						if ($value_currency != "0") {
							$options[] = array(
								"name" => esc_html__("Price for","wpqa")." ".$value_currency,
								"id"   => prefix_meta."subscribe_lifetime_".strtolower($value_currency),
								"type" => "text",
								'std'  => 200
							);
						}
					}
				}

				$options[] = array(
					'type' => 'heading-2',
					'div'  => 'div',
					'end'  => 'end'
				);
			}

			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'payment_type:has(points),'.prefix_meta.'payment_type:has(payments_points)',
				'operator'  => 'or',
				'type'      => 'heading-2'
			);

			$options[] = array(
				'name' => esc_html__("Price with points to allow the users to subscribe","wpqa"),
				'type' => 'info'
			);

			$options[] = array(
				"name"      => esc_html__("What's the points to subscribe monthly?",'wpqa'),
				"id"        => prefix_meta."subscribe_monthly_points",
				"type"      => "text",
				'condition' => prefix_meta.'subscriptions_options:is(monthly)',
				'std'       => 100
			);

			$options[] = array(
				"name"      => esc_html__("What's the points to subscribe for three months?",'wpqa'),
				"id"        => prefix_meta."subscribe_3months_points",
				"type"      => "text",
				'condition' => prefix_meta.'subscriptions_options:is(3months)',
				'std'       => 250
			);

			$options[] = array(
				"name"      => esc_html__("What's the points to subscribe for six months?",'wpqa'),
				"id"        => prefix_meta."subscribe_6months_points",
				"type"      => "text",
				'condition' => prefix_meta.'subscriptions_options:is(6months)',
				'std'       => 400
			);

			$options[] = array(
				"name"      => esc_html__("What's the points to subscribe yearly?",'wpqa'),
				"id"        => prefix_meta."subscribe_yearly_points",
				"type"      => "text",
				'condition' => prefix_meta.'subscriptions_options:is(yearly)',
				'std'       => 700
			);

			$options[] = array(
				"name"      => esc_html__("What's the points to subscribe for two years?",'wpqa'),
				"id"        => prefix_meta."subscribe_2years_points",
				"type"      => "text",
				'condition' => prefix_meta.'subscriptions_options:is(2years)',
				'std'       => 700
			);

			$options[] = array(
				"name"      => esc_html__("What's the points to subscribe for lifetime?",'wpqa'),
				"id"        => prefix_meta."subscribe_lifetime_points",
				"type"      => "text",
				'condition' => prefix_meta.'subscriptions_options:is(lifetime)',
				'std'       => 2000
			);

			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
		}

		$options = apply_filters(wpqa_prefix_theme.'_all_meta_options',$options,$post);
	}
	return $options;
}?>