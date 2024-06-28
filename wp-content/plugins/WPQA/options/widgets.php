<?php

/* @author    2codeThemes
*  @package   WPQA/options
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Widget options */
function wpqa_admin_widgets() {
	$active_points = wpqa_options("active_points");

	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}

	// Knowledgebase
	$activate_knowledgebase = apply_filters("wpqa_activate_knowledgebase",false);

	// Type of posts
	$post_or_question = array("post" => esc_html__("Posts","wpqa"),wpqa_questions_type => esc_html__("Questions","wpqa"));
	$post_or_question_std = array("post" => "post",wpqa_questions_type => wpqa_questions_type);
	if ($activate_knowledgebase == true) {
		$post_or_question[wpqa_knowledgebase_type] = esc_html__("Knowledgebases","wpqa");
		$post_or_question_std[wpqa_knowledgebase_type] = wpqa_knowledgebase_type;
	}

	// If using image radio buttons, define a directory path
	$imagepath_theme =  get_template_directory_uri(). '/images/';
	
	$options = array();

	$options = apply_filters("wpqa_widget_options",$options);

	$options['activities-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Activities Log'
		),
		array(
			'name' => esc_html__('Item number','wpqa'),
			'id'   => 'item_number',
			'type' => 'text',
			'std'  => '5'
		),
		array(
			'name' => esc_html__('Display the more button?','wpqa'),
			'id'   => 'more_button',
			'type' => 'checkbox',
			'std'  => 'on'
		),
	);

	$options['ask-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Ask Question'
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
				'group'    => esc_html__('Create A New Group','wpqa'),
				'custom'   => esc_html__('Custom link','wpqa'),
			),
			'std'       => 'question',
		),
		array(
			'div'       => 'div',
			'condition' => 'button:is(custom)',
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

	$options['comments-post-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Comments'
		),
		array(
			'name'    => esc_html__('Post or question','wpqa'),
			'id'      => 'post_or_question',
			'options' => array("post" => esc_html__("Posts","wpqa"),wpqa_questions_type => esc_html__("Questions","wpqa")),
			'std'     => 'post',
			'type'    => 'radio'
		),
		array(
			'name'    => esc_html__('Specific date.','wpqa'),
			'desc'    => esc_html__('Select the specific date.','wpqa'),
			'id'      => "specific_date",
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
		),
		array(
			'name' => esc_html__('Display images?','wpqa'),
			'id'   => 'show_images',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Number of comments to show','wpqa'),
			'id'   => 'comments_number',
			'type' => 'text',
			'std'  => '5'
		),
		array(
			'name' => esc_html__('The number of words excerpt','wpqa'),
			'id'   => 'comment_excerpt',
			'type' => 'text',
			'std'  => '10'
		),
		array(
			'name' => esc_html__('Display date?','wpqa'),
			'id'   => 'display_date',
			'type' => 'checkbox',
			'std'  => 'on'
		),
	);

	$options['groups-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Groups'
		),
		array(
			'name'    => esc_html__('Group style','wpqa'),
			'desc'    => esc_html__('Select the groups display by.','wpqa'),
			'id'      => 'group_style',
			'options' => array(
				'style_1' => esc_html__('Show the group status','wpqa'),
				'style_2' => esc_html__('Group details (ex: users and posts)','wpqa'),
			),
			'std'     => 'style_2',
			'type'    => 'radio',
		),
		array(
			'name' => esc_html__('Number of groups to show','wpqa'),
			'id'   => 'no_of_groups',
			'type' => 'text',
			'std'  => '5'
		),
		array(
			'name'    => esc_html__('Display by','wpqa'),
			'desc'    => esc_html__('Select the groups display by.','wpqa'),
			'id'      => 'group_display',
			'options' => array(
				'all'     => esc_html__('All groups','wpqa'),
				'private' => esc_html__('Private groups','wpqa'),
				'public'  => esc_html__('Public groups','wpqa'),
			),
			'std'     => 'all',
			'type'    => 'radio',
		),
		array(
			'name'    => esc_html__('Order by','wpqa'),
			'desc'    => esc_html__('Select the groups order by.','wpqa'),
			'id'      => 'group_order',
			'options' => array(
				'date'  => esc_html__('Date','wpqa'),
				'users' => esc_html__('Users','wpqa'),
				'posts' => esc_html__('Posts','wpqa'),
			),
			'std'     => 'date',
			'type'    => 'radio',
		),
	);

	$options['login-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Login'
		),
	);

	$options['notifications-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Notifications'
		),
		array(
			'name' => esc_html__('Item number','wpqa'),
			'id'   => 'item_number',
			'type' => 'text',
			'std'  => '5'
		),
		array(
			'name' => esc_html__('Display the more button?','wpqa'),
			'id'   => 'more_button',
			'type' => 'checkbox',
			'std'  => 'on'
		),
	);

	$orderby = array(
		"recent" => esc_html__("Recent","wpqa"),
		"random" => esc_html__("Random","wpqa"),
		"popular" => esc_html__("Most Answered","wpqa").($activate_knowledgebase == true?" - ".esc_html__("Not Knowledgebases","wpqa"):""),
		"most_visited" => esc_html__("Most visited","wpqa"),
		"most_voted" => esc_html__("Most voted","wpqa").($activate_knowledgebase == true?" - ".esc_html__("Questions and Knowledgebases","wpqa"):" - ".esc_html__("Questions only","wpqa")),
		"most_reacted" => esc_html__("Most reacted","wpqa")." - ".esc_html__("Questions only","wpqa"),
		"no_response" => esc_html__("No response","wpqa").($activate_knowledgebase == true?" - ".esc_html__("Not Knowledgebases","wpqa"):"")
	);

	$options['widget_posts'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Recent Posts'
		),
		array(
			'name'    => esc_html__('Post or question','wpqa'),
			'id'      => 'post_or_question',
			'options' => $post_or_question,
			'std'     => 'post',
			'type'    => 'radio'
		),
		array(
			'name'      => esc_html__('Post style','wpqa'),
			'id'        => 'post_style',
			'condition' => 'post_or_question:is(post)',
			'options'   => array("style_1" => esc_html__("Style 1","wpqa"),"style_2" => esc_html__("Style 2","wpqa")),
			'std'       => 'style_1',
			'type'      => 'radio'
		),
		array(
			'name'      => esc_html__('Question style','wpqa'),
			'id'        => 'question_style',
			'condition' => 'post_or_question:is('.wpqa_questions_type.')',
			'options'   => array("style_1" => esc_html__("Style 1","wpqa"),"style_2" => esc_html__("Style 2","wpqa")),
			'std'       => 'style_1',
			'type'      => 'radio'
		),
		array(
			'name'      => esc_html__('Display author image?','wpqa'),
			'id'        => 'show_images',
			'type'      => 'checkbox',
			'condition' => 'post_or_question:is('.wpqa_questions_type.'),question_style:is(style_1)',
			'std'       => 'on'
		),
		array(
			'name'      => esc_html__('Display author image?','wpqa'),
			'id'        => 'show_images_post',
			'type'      => 'checkbox',
			'condition' => 'post_or_question:is(post),post_style:is(style_1)',
			'std'       => 'on'
		),
		array(
			'name' => esc_html__('The excerpt title','wpqa'),
			'id'   => 'excerpt_title',
			'type' => 'text',
			'std'  => '10'
		),
		array(
			'name'    => esc_html__('Order by','wpqa'),
			'id'      => 'orderby',
			'options' => $orderby,
			'std'     => 'recent',
			'type'    => 'select'
		),
		array(
			'name' => esc_html__('Number of items to show','wpqa'),
			'id'   => 'posts_per_page',
			'type' => 'text',
			'std'  => '5'
		),
		
		array(
			'div'       => 'div',
			'condition' => 'post_or_question:is(post)',
			'type'      => 'heading-2'
		),
		array(
			'div'       => 'div',
			'condition' => 'post_style:is(style_2)',
			'type'      => 'heading-2'
		),
		array(
			'name' => esc_html__('Display image?','wpqa'),
			'id'   => 'display_image',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Display video if there?','wpqa'),
			'id'   => 'display_video',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Display date?','wpqa'),
			'id'   => 'display_date',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
		array(
			'name' => esc_html__('Display comments?','wpqa'),
			'id'   => 'display_comment',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Excerpt post','wpqa'),
			'id'   => 'excerpt_post',
			'std'  => '0',
			'type' => 'text'
		),
		array(
			'div'       => 'div',
			'condition' => 'post_style:is(style_2)',
			'type'      => 'heading-2'
		),
		array(
			'name' => esc_html__('Activate the more post button','wpqa'),
			'desc' => esc_html__('Select ON to enable the button.','wpqa'),
			'id'   => 'blog_h_button',
			'std'  => 'on',
			'type' => 'checkbox'
		),
		array(
			'div'       => 'div',
			'condition' => 'blog_h_button:not(0)',
			'type'      => 'heading-2'
		),
		array(
			'name' => esc_html__('The text for the button','wpqa'),
			'desc' => esc_html__('Type from here the text for the button','wpqa'),
			'id'   => 'blog_h_button_text',
			'type' => 'text',
			'std'  => 'Explore Our Blog'
		),
		array(
			'name'    => esc_html__('Blog page','wpqa'),
			'desc'    => esc_html__('Select the blog page','wpqa'),
			'id'      => 'blog_h_page',
			'type'    => 'select',
			'options' => $options_pages
		),
		array(
			'name' => esc_html__("Type the blog link if you don't like a page","wpqa"),
			'id'   => 'blog_h_link',
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
		array(
			'name'    => esc_html__('Specific date.','wpqa'),
			'desc'    => esc_html__('Select the specific date.','wpqa'),
			'id'      => "specific_date_post",
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
		),
		array(
			'name'    => esc_html__('Display','wpqa'),
			'id'      => 'display',
			'options' => array("lasts" => esc_html__("Latest Posts","wpqa"),"category" => esc_html__("Single Category","wpqa"),"categories" => esc_html__("Multiple Categories","wpqa"),"exclude_categories" => esc_html__("Exclude Categories","wpqa"),"custom_posts" => esc_html__("Custom Posts","wpqa")),
			'std'     => 'recent',
			'type'    => 'select'
		),
		array(
			'name'      => esc_html__('Category','wpqa'),
			'id'        => 'category',
			'type'      => 'select_category',
			'condition' => 'display:is(category)'
		),
		array(
			'name'      => esc_html__('Categories','wpqa'),
			'id'        => 'categories',
			'type'      => 'multicheck_category',
			'condition' => 'display:is(categories)'
		),
		array(
			'name'      => esc_html__('Exclude Categories','wpqa'),
			'id'        => 'exclude_categories',
			'type'      => 'multicheck_category',
			'condition' => 'display:is(exclude_categories)'
		),
		array(
			'name'      => esc_html__('Custom posts','wpqa'),
			'id'        => 'custom_posts',
			'type'      => 'text',
			'condition' => 'display:is(custom_posts)'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
		
		array(
			'div'       => 'div',
			'condition' => 'post_or_question:is('.wpqa_questions_type.')',
			'type'      => 'heading-2'
		),
		array(
			'div'       => 'div',
			'condition' => 'question_style:is(style_2)',
			'type'      => 'heading-2'
		),
		array(
			'name' => esc_html__('Display image?','wpqa'),
			'id'   => 'display_image_question',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Display video if there?','wpqa'),
			'id'   => 'display_video_question',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Display date?','wpqa'),
			'id'   => 'display_date_question',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
		array(
			'name' => esc_html__('Display answers?','wpqa'),
			'id'   => 'display_answer',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name'    => esc_html__('Specific date.','wpqa'),
			'desc'    => esc_html__('Select the specific date.','wpqa'),
			'id'      => "specific_date",
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
		),
		array(
			'name'    => esc_html__('Display','wpqa'),
			'id'      => 'display_question',
			'options' => array("lasts" => esc_html__("Latest Questions","wpqa"),"category" => esc_html__("Single Category","wpqa"),"categories" => esc_html__("Multiple Categories","wpqa"),"exclude_categories" => esc_html__("Exclude Categories","wpqa"),"custom_posts" => esc_html__("Custom Questions","wpqa")),
			'std'     => 'recent',
			'type'    => 'select'
		),
		array(
			'name'      => esc_html__('Category','wpqa'),
			'id'        => 'category_question',
			'type'      => 'select_category',
			'taxonomy'  => wpqa_question_categories,
			'condition' => 'display_question:is(category)'
		),
		array(
			'name'      => esc_html__('Categories','wpqa'),
			'id'        => 'categories_question',
			'type'      => 'multicheck_category',
			'taxonomy'  => wpqa_question_categories,
			'condition' => 'display_question:is(categories)'
		),
		array(
			'name'      => esc_html__('Exclude Categories','wpqa'),
			'id'        => 'exclude_categories_question',
			'type'      => 'multicheck_category',
			'taxonomy'  => wpqa_question_categories,
			'condition' => 'display_question:is(exclude_categories)'
		),
		array(
			'name'      => esc_html__('Custom questions','wpqa'),
			'id'        => 'custom_questions',
			'type'      => 'text',
			'condition' => 'display_question:is(custom_posts)'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
	);

	if ($activate_knowledgebase == true) {
		$count = (int)(count($options['widget_posts'])-1);
		$options['widget_posts'] = wpqa_array_insert_after($options['widget_posts'],$count,array(
			array(
				'div'       => 'div',
				'condition' => 'post_or_question:is('.wpqa_knowledgebase_type.')',
				'type'      => 'heading-2'
			),
			array(
				'name'    => esc_html__('Specific date.','wpqa'),
				'desc'    => esc_html__('Select the specific date.','wpqa'),
				'id'      => "specific_date_knowledgebase",
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
			),
			array(
				'name'    => esc_html__('Display','wpqa'),
				'id'      => 'display_knowledgebase',
				'options' => array("lasts" => esc_html__("Latest Knowledgebases","wpqa"),"category" => esc_html__("Single Category","wpqa"),"categories" => esc_html__("Multiple Categories","wpqa"),"exclude_categories" => esc_html__("Exclude Categories","wpqa"),"custom_posts" => esc_html__("Custom Knowledgebases","wpqa")),
				'std'     => 'recent',
				'type'    => 'select'
			),
			array(
				'name'      => esc_html__('Category','wpqa'),
				'id'        => 'category_knowledgebase',
				'type'      => 'select_category',
				'taxonomy'  => wpqa_knowledgebase_categories,
				'condition' => 'display_knowledgebase:is(category)'
			),
			array(
				'name'      => esc_html__('Categories','wpqa'),
				'id'        => 'categories_knowledgebase',
				'type'      => 'multicheck_category',
				'taxonomy'  => wpqa_knowledgebase_categories,
				'condition' => 'display_knowledgebase:is(categories)'
			),
			array(
				'name'      => esc_html__('Exclude Categories','wpqa'),
				'id'        => 'exclude_categories_knowledgebase',
				'type'      => 'multicheck_category',
				'taxonomy'  => wpqa_knowledgebase_categories,
				'condition' => 'display_knowledgebase:is(exclude_categories)'
			),
			array(
				'name'      => esc_html__('Custom knowledgebases','wpqa'),
				'id'        => 'custom_knowledgebases',
				'type'      => 'text',
				'condition' => 'display_knowledgebase:is(custom_posts)'
			),
			array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			),
		));
	}

	$options['widget_profile_strength'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Profile Strength'
		),
		array(
			'name' => sprintf(esc_html__('The setting for this widget from %s Setting/User setting/Edit Profile.','wpqa'),wpqa_name_theme),
			'type' => 'info',
		),
	);

	$show = array(
		'home_page'     => esc_html__('Home page','wpqa'),
		'all_pages'     => esc_html__('All site pages','wpqa'),
		'all_posts'     => esc_html__('All single post pages','wpqa'),
		'all_questions' => esc_html__('All single question pages','wpqa'),
		'custom_pages'  => esc_html__('Custom pages','wpqa'),
	);
	if ($activate_knowledgebase == true) {
		$show = wpqa_array_insert_after($show,"all_questions",array('all_knowledgebases' => esc_html__('All single knowledgebase pages','wpqa')));
	}

	$action = array(
		'both'     => esc_html__('Both','wpqa'),
		'unlogged' => esc_html__('Unlogged users','wpqa'),
		'logged'  => esc_html__('Logged users','wpqa'),
	);

	$important_notices_array = array(
		array(
			"type" => "text",
			"id"   => "term",
			"name" => esc_html__('Term','wpqa')
		),
	);

	$important_notices = array(
		1 => array("term" => esc_html__("Find a nice title for your question","wpqa")),
		2 => array("term" => esc_html__("Add suitable tags to reach your target members","wpqa")),
		3 => array("term" => esc_html__("Enrich details, decorate with visuals","wpqa")),
		4 => array("term" => esc_html__("Pay attention to the marking and writing rules","wpqa")),
		5 => array("term" => esc_html__("Share it, be active and follow it!","wpqa")),
	);

	$options['widget_important_notices'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Important Notices'
		),
		array(
			'name' => esc_html__('You can use this widget to show the important notices for anything, for example, asking questions, adding posts, creating groups.','wpqa'),
			'type' => 'info',
		),
		array(
			'name'    => esc_html__('This widget works at all the pages, custom pages, or home page only?','wpqa'),
			'id'      => 'show',
			'type'    => 'select',
			'options' => $show
		),
		array(
			'name'      => esc_html__('Page ids','wpqa'),
			'desc'      => esc_html__('Type from here the page ids','wpqa'),
			'id'        => 'custom_pages',
			'type'      => 'text',
			'condition' => 'show:is(custom_pages)'
		),
		array(
			'name'    => esc_html__('Show this widget for logged user, unlogged users, or both of them?','wpqa'),
			'id'      => 'action',
			'type'    => 'radio',
			'std'     => 'both',
			'options' => $action
		),
		array(
			'id'      => "important_notices",
			'type'    => "elements",
			'sort'    => "yes",
			'hide'    => "yes",
			'button'  => esc_html__('Add a new term','wpqa'),
			'options' => $important_notices_array,
			'std'     => $important_notices,
		)
	);

	if (has_discy()) {
		$category_type = array(
			'with_icon'     => esc_html__('With icons','wpqa'),
			'icon_color'    => esc_html__('With icons and colors','wpqa'),
			'with_icon_1'   => esc_html__('With icons 2','wpqa'),
			'with_icon_2'   => esc_html__('With colored icons','wpqa'),
			'with_icon_3'   => esc_html__('With colored icons and box','wpqa'),
			'with_icon_4'   => esc_html__('With colored icons and box 2','wpqa'),
			"simple_follow" => esc_html__("Simple with follow","wpqa"),
			'with_cover_1'  => esc_html__('With cover','wpqa'),
			'with_cover_2'  => esc_html__('With cover and icon','wpqa'),
			'with_cover_3'  => esc_html__('With cover and small icon','wpqa'),
			"simple"        => esc_html__("Simple","wpqa"),
			"links"         => esc_html__("Links","wpqa")
		);
	}else {
		$category_type = array(
			'with_icon'     => esc_html__('With icons','wpqa'),
			'icon_color'    => esc_html__('With icons and colors','wpqa'),
			'with_icon_1'   => esc_html__('With icons 2','wpqa'),
			'with_icon_2'   => esc_html__('With colored icons','wpqa'),
			'with_icon_3'   => esc_html__('With colored icons and box','wpqa'),
			'with_icon_4'   => esc_html__('With colored icons and box 2','wpqa'),
			"simple_follow" => esc_html__("Simple with follow","wpqa"),
			'with_cover_1'  => esc_html__('With cover','wpqa'),
			'with_cover_3'  => esc_html__('With cover and small icon','wpqa'),
			"simple"        => esc_html__("Simple","wpqa"),
			"links"         => esc_html__("Links","wpqa")
		);
	}

	$options['questions_categories-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Questions Categories'
		),
		array(
			'name'    => esc_html__('Style','wpqa'),
			'id'      => 'category_type',
			'options' => $category_type,
			'std'     => 'with_icon',
			'type'    => 'radio'
		),
		array(
			'name' => esc_html__('Number of categories, put 0 for all categories','wpqa'),
			'id'   => 'cat_number',
			'type' => 'text',
			'std'  => 0
		),
		array(
			'name'      => esc_html__('Order by','wpqa'),
			'id'        => 'cat_sort',
			'std'       => "count",
			'type'      => 'select',
			'condition' => 'category_type:not(links)',
			'options'   => array(
				'count'     => esc_html__('Questions','wpqa'),
				//'answers'   => esc_html__('Answers','wpqa'),
				'followers' => esc_html__('Followers','wpqa'),
			),
		),
		array(
			'div'       => 'div',
			'condition' => 'category_type:is(links)',
			'type'      => 'heading-2'
		),
		array(
			'name' => esc_html__('Show questions counts?','wpqa'),
			'id'   => 'questions_counts',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Show the child categories accordion','wpqa'),
			'id'   => 'show_child',
			'type' => 'checkbox'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
	);

	if ($activate_knowledgebase == true) {
		$category_type = array(
			'with_icon'     => esc_html__('With icons','wpqa'),
			'icon_color'    => esc_html__('With icons and colors','wpqa'),
			'with_icon_1'   => esc_html__('With icons 2','wpqa'),
			'with_icon_2'   => esc_html__('With colored icons','wpqa'),
			'with_icon_3'   => esc_html__('With colored icons and box','wpqa'),
			'with_icon_4'   => esc_html__('With colored icons and box 2','wpqa'),
			'with_cover_1'  => esc_html__('With cover','wpqa'),
			'with_cover_3'  => esc_html__('With cover and small icon','wpqa'),
			"simple"        => esc_html__("Simple","wpqa"),
			"links"         => esc_html__("Links","wpqa")
		);

		$options['knowledgebases_categories-widget'] = array(
			array(
				'name' => esc_html__('Title','wpqa'),
				'id'   => 'title',
				'type' => 'text',
				'std'  => 'Knowledgebases Categories'
			),
			array(
				'name'    => esc_html__('Style','wpqa'),
				'id'      => 'category_type',
				'options' => $category_type,
				'std'     => 'with_icon',
				'type'    => 'radio'
			),
			array(
				'name' => esc_html__('Number of categories, put 0 for all categories','wpqa'),
				'id'   => 'cat_number',
				'type' => 'text',
				'std'  => 0
			),
			array(
				'name'      => esc_html__('Order by','wpqa'),
				'id'        => 'cat_sort',
				'std'       => "count",
				'type'      => 'select',
				'condition' => 'category_type:not(links)',
				'options'   => array(
					'count' => esc_html__('Knowledgebases','wpqa'),
					'name'  => esc_html__('Name','wpqa'),
				),
			),
			array(
				'div'       => 'div',
				'condition' => 'category_type:is(links)',
				'type'      => 'heading-2'
			),
			array(
				'name' => esc_html__('Show knowledgebases counts?','wpqa'),
				'id'   => 'knowledgebases_counts',
				'type' => 'checkbox',
				'std'  => 'on'
			),
			array(
				'name' => esc_html__('Show the child categories accordion','wpqa'),
				'id'   => 'show_child',
				'type' => 'checkbox'
			),
			array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			),
		);
	}

	$options['related-widget'] = array(
		array(
			'name' => ($activate_knowledgebase == true?esc_html__('This widget will show at single questions, knowledgebases, or posts only to show the related questions, or posts.','wpqa'):esc_html__('This widget will show at single questions, or posts only to show the related questions, or posts.','wpqa')),
			'type' => 'info',
		),
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Related Posts'
		),
		array(
			'name'    => esc_html__('Post or question','wpqa'),
			'id'      => 'post_or_question',
			'options' => $post_or_question,
			'std'     => $post_or_question_std,
			'type'    => 'multicheck'
		),
		array(
			'div'       => 'div',
			'condition' => 'post_or_question:has(post)',
			'type'      => 'heading-2'
		),
		array(
			'name' => esc_html__('Posts','wpqa'),
			'type' => 'info',
		),
		array(
			'name' => esc_html__('Number of items to show','wpqa'),
			'id'   => 'related_number_post',
			'type' => 'text',
			'std'  => '5'
		),
		array(
			'name'    => esc_html__('Post style','wpqa'),
			'id'      => 'post_style',
			'options' => array("style_1" => esc_html__("Style 1","wpqa"),"style_2" => esc_html__("Style 2","wpqa")),
			'std'     => 'style_1',
			'type'    => 'radio'
		),
		array(
			'name'      => esc_html__('Display author image?','wpqa'),
			'id'        => 'show_images_post',
			'type'      => 'checkbox',
			'condition' => 'post_style:is(style_1)',
			'std'       => 'on',
		),
		array(
			'name' => esc_html__('The excerpt title','wpqa'),
			'id'   => 'excerpt_title_post',
			'type' => 'text',
			'std'  => '10',
		),
		array(
			'name' => esc_html__('Display comments meta?','wpqa'),
			'id'   => 'display_comment_meta',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'div'       => 'div',
			'condition' => 'post_style:is(style_2)',
			'type'      => 'heading-2'
		),
		array(
			'name' => esc_html__('Display image?','wpqa'),
			'id'   => 'display_image_post',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Display video if there?','wpqa'),
			'id'   => 'display_video_post',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Display date?','wpqa'),
			'id'   => 'display_date_post',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
		array(
			'name' => esc_html__('Excerpt post','wpqa'),
			'id'   => 'excerpt_post',
			'std'  => '0',
			'type' => 'text'
		),
		array(
			'name'    => esc_html__('Query type','wpqa'),
			'id'      => 'query_related_post',
			'options' => array(
				'categories' => esc_html__('Posts in the same categories','wpqa'),
				'tags'       => esc_html__('Posts in the same tags (If not found, posts with the same categories will be shown)','wpqa'),
				'author'     => esc_html__('Posts by the same author','wpqa'),
			),
			'std'     => 'categories',
			'type'    => 'radio'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
		
		array(
			'div'       => 'div',
			'condition' => 'post_or_question:has('.wpqa_questions_type.')',
			'type'      => 'heading-2'
		),
		array(
			'name' => esc_html__('Questions','wpqa'),
			'type' => 'info',
		),
		array(
			'name' => esc_html__('Number of items to show','wpqa'),
			'id'   => 'related_number_question',
			'type' => 'text',
			'std'  => '5'
		),
		array(
			'name'    => esc_html__('Question style','wpqa'),
			'id'      => 'question_style',
			'options' => array("style_1" => esc_html__("Style 1","wpqa"),"style_2" => esc_html__("Style 2","wpqa")),
			'std'     => 'style_1',
			'type'    => 'radio'
		),
		array(
			'name'      => esc_html__('Display author image?','wpqa'),
			'id'        => 'show_images_question',
			'type'      => 'checkbox',
			'condition' => 'question_style:is(style_1)',
			'std'       => 'on',
		),
		array(
			'name' => esc_html__('The excerpt title','wpqa'),
			'id'   => 'excerpt_title_question',
			'type' => 'text',
			'std'  => '10',
		),
		array(
			'div'       => 'div',
			'condition' => 'question_style:is(style_2)',
			'type'      => 'heading-2'
		),
		array(
			'name' => esc_html__('Display image?','wpqa'),
			'id'   => 'display_image_question',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Display video if there?','wpqa'),
			'id'   => 'display_video_question',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Display date?','wpqa'),
			'id'   => 'display_date_question',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Excerpt question','wpqa'),
			'id'   => 'excerpt_question',
			'std'  => '40',
			'type' => 'text'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
		array(
			'name' => esc_html__('Display answers?','wpqa'),
			'id'   => 'display_answers',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name'    => esc_html__('Query type','wpqa'),
			'id'      => 'query_related_question',
			'options' => array(
				'categories' => esc_html__('Questions in the same categories','wpqa'),
				'tags'       => esc_html__('Questions in the same tags (If not found, questions with the same categories will be shown)','wpqa'),
				'author'     => esc_html__('Questions by the same author','wpqa'),
			),
			'std'     => 'categories',
			'type'    => 'radio'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
	);

	if ($activate_knowledgebase == true) {
		$count = (int)(count($options['related-widget'])-1);
		$options['related-widget'] = wpqa_array_insert_after($options['related-widget'],$count,array(
			array(
				'div'       => 'div',
				'condition' => 'post_or_question:has('.wpqa_knowledgebase_type.')',
				'type'      => 'heading-2'
			),
			array(
				'name' => esc_html__('Knowledgebases','wpqa'),
				'type' => 'info',
			),
			array(
				'name' => esc_html__('Number of items to show','wpqa'),
				'id'   => 'related_number_knowledgebase',
				'type' => 'text',
				'std'  => '5'
			),
			array(
				'name' => esc_html__('The excerpt title','wpqa'),
				'id'   => 'excerpt_title_knowledgebase',
				'type' => 'text',
				'std'  => '10',
			),
			array(
				'name'    => esc_html__('Query type','wpqa'),
				'id'      => 'query_related_knowledgebase',
				'options' => array(
					'categories' => esc_html__('Knowledgebases in the same categories','wpqa'),
					'tags'       => esc_html__('Knowledgebases in the same tags (If not found, posts with the same categories will be shown)','wpqa'),
					'author'     => esc_html__('Knowledgebases by the same author','wpqa'),
				),
				'std'     => 'categories',
				'type'    => 'radio'
			),
			array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			),
		));
	}

	$options['rules-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Group Rules'
		),
	);

	$options['signup-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Signup'
		),
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

	if (has_discy()) {
		$stats_style = array(
			'style_1' => 'Style 1',
			'style_2' => 'Style 2',
		);

		$theme_stats = array(
				array(
				'name' => esc_html__('Display divider?','wpqa'),
				'id'   => 'divider',
				'type' => 'checkbox',
				'std'  => 'on'
			)
		);
	}else {
		$stats_style = array(
			'style_1' => 'Style 1',
			'style_2' => 'Style 2',
			'style_3' => 'Style 3',
		);

		$theme_stats = array();
	}

	$stats_widget = array_merge(array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text'
		),
		array(
			'name'    => esc_html__('Choose the stats show','wpqa'),
			'id'      => 'stats',
			'type'    => 'multicheck',
			'sort'    => 'yes',
			'std'     => $stats_array,
			'options' => $stats_array
		),
		array(
			'name'    => esc_html__('Style','wpqa'),
			'id'      => 'style',
			'options' => $stats_style,
			'std'     => 'style_1',
			'type'    => 'radio'
		),
	),$theme_stats);
	
	$options['stats-widget'] = $stats_widget;

	$options['tabs-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Tabs'
		),
		array(
			'name'    => esc_html__('Post or question','wpqa'),
			'id'      => 'post_or_question',
			'options' => $post_or_question,
			'std'     => 'post',
			'type'    => 'radio'
		),
		array(
			'name'      => esc_html__("Select What's tabs show","wpqa"),
			'id'        => 'tabs',
			'type'      => 'multicheck',
			'sort'      => 'yes',
			'std'       => array(
				"display_posts"    => array("sort" => esc_html__("Display posts?","wpqa"),"value" => "display_posts"),
				"display_comments" => array("sort" => esc_html__("Display comments?","wpqa"),"value" => "display_comments"),
				"display_tags"     => array("sort" => esc_html__("Display tags?","wpqa"),"value" => "display_tags"),
			),
			'options'   => array(
				"display_posts"    => array("sort" => esc_html__("Display posts?","wpqa"),"value" => "display_posts"),
				"display_comments" => array("sort" => esc_html__("Display comments?","wpqa"),"value" => "display_comments"),
				"display_tags"     => array("sort" => esc_html__("Display tags?","wpqa"),"value" => "display_tags"),
			),
			'condition' => 'post_or_question:is(post)'
		),
		array(
			'name'      => esc_html__("Select What's tabs show","wpqa"),
			'id'        => 'tabs_questions',
			'type'      => 'multicheck',
			'sort'      => 'yes',
			'std'       => array(
				"display_posts"    => array("sort" => esc_html__("Display questions?","wpqa"),"value" => "display_posts"),
				"display_comments" => array("sort" => esc_html__("Display answers?","wpqa"),"value" => "display_comments"),
				"display_tags"     => array("sort" => esc_html__("Display tags?","wpqa"),"value" => "display_tags"),
			),
			'options'   => array(
				"display_posts"    => array("sort" => esc_html__("Display questions?","wpqa"),"value" => "display_posts"),
				"display_comments" => array("sort" => esc_html__("Display answers?","wpqa"),"value" => "display_comments"),
				"display_tags"     => array("sort" => esc_html__("Display tags?","wpqa"),"value" => "display_tags"),
			),
			'condition' => 'post_or_question:is('.wpqa_questions_type.')'
		),
		array(
			'div'       => 'div',
			'condition' => 'post_or_question:is(post),tabs:has(display_posts)',
			'type'      => 'heading-2'
		),
		array(
			'name'    => esc_html__('Post style','wpqa'),
			'id'      => 'post_style',
			'options' => array("style_1" => esc_html__("Style 1","wpqa"),"style_2" => esc_html__("Style 2","wpqa")),
			'std'     => 'style_1',
			'type'    => 'radio'
		),
		array(
			'name' => esc_html__('Number of posts to show','wpqa'),
			'id'   => 'posts_per_page',
			'type' => 'text',
			'std'  => '5',
		),
		array(
			'name'      => esc_html__('Display author image?','wpqa'),
			'id'        => 'show_images_post',
			'type'      => 'checkbox',
			'condition' => 'post_style:is(style_1)',
			'std'       => 'on',
		),
		array(
			'name' => esc_html__('The excerpt title','wpqa'),
			'id'   => 'excerpt_title_post',
			'type' => 'text',
			'std'  => '10',
		),
		array(
			'name'    => esc_html__('Order by','wpqa'),
			'id'      => 'orderby_post',
			'options' => array("recent" => esc_html__("Recent","wpqa"),"random" => esc_html__("Random","wpqa"),"popular" => esc_html__("Most Commented","wpqa"),"most_visited" => esc_html__("Most visited","wpqa"),"no_response" => esc_html__("No response","wpqa")),
			'std'     => 'popular',
			'type'    => 'select'
		),
		array(
			'name' => esc_html__('Display comments meta?','wpqa'),
			'id'   => 'display_comment_meta',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		
		array(
			'div'       => 'div',
			'condition' => 'post_or_question:is(post)',
			'type'      => 'heading-2'
		),
		array(
			'div'       => 'div',
			'condition' => 'post_style:is(style_2)',
			'type'      => 'heading-2'
		),
		array(
			'name' => esc_html__('Display image?','wpqa'),
			'id'   => 'display_image',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Display video if there?','wpqa'),
			'id'   => 'display_video',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Display date?','wpqa'),
			'id'   => 'display_date_2',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
		array(
			'name' => esc_html__('Excerpt post','wpqa'),
			'id'   => 'excerpt_post',
			'std'  => '0',
			'type' => 'text'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
		array(
			'name'    => esc_html__('Specific date.','wpqa'),
			'desc'    => esc_html__('Select the specific date.','wpqa'),
			'id'      => "specific_date_post",
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
		),
		array(
			'name'    => esc_html__('Display','wpqa'),
			'id'      => 'display',
			'options' => array("lasts" => esc_html__("Latest Posts","wpqa"),"category" => esc_html__("Single Category","wpqa"),"categories" => esc_html__("Multiple Categories","wpqa"),"exclude_categories" => esc_html__("Exclude Categories","wpqa"),"custom_posts" => esc_html__("Custom Posts","wpqa")),
			'std'     => 'recent',
			'type'    => 'select'
		),
		array(
			'name'      => esc_html__('Category','wpqa'),
			'id'        => 'category',
			'type'      => 'select_category',
			'condition' => 'display:is(category)'
		),
		array(
			'name'      => esc_html__('Categories','wpqa'),
			'id'        => 'categories',
			'type'      => 'multicheck_category',
			'condition' => 'display:is(categories)'
		),
		array(
			'name'      => esc_html__('Exclude Categories','wpqa'),
			'id'        => 'exclude_categories',
			'type'      => 'multicheck_category',
			'condition' => 'display:is(exclude_categories)'
		),
		array(
			'name'      => esc_html__('Custom posts','wpqa'),
			'id'        => 'custom_posts',
			'type'      => 'text',
			'condition' => 'display:is(custom_posts)'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
		
		array(
			'div'       => 'div',
			'condition' => 'post_or_question:is('.wpqa_questions_type.'),tabs_questions:has(display_posts)',
			'type'      => 'heading-2'
		),
		array(
			'name'    => esc_html__('Question style','wpqa'),
			'id'      => 'question_style',
			'options' => array("style_1" => esc_html__("Style 1","wpqa"),"style_2" => esc_html__("Style 2","wpqa")),
			'std'     => 'style_1',
			'type'    => 'radio'
		),
		array(
			'name' => esc_html__('Number of questions to show','wpqa'),
			'id'   => 'questions_per_page',
			'type' => 'text',
			'std'  => '5',
		),
		array(
			'name'      => esc_html__('Display author image?','wpqa'),
			'id'        => 'show_images',
			'type'      => 'checkbox',
			'condition' => 'question_style:is(style_1)',
			'std'       => 'on',
		),
		array(
			'name' => esc_html__('The excerpt title','wpqa'),
			'id'   => 'excerpt_title',
			'type' => 'text',
			'std'  => '10',
		),
		array(
			'name'    => esc_html__('Order by','wpqa'),
			'id'      => 'orderby',
			'options' => array("recent" => esc_html__("Recent","wpqa"),"random" => esc_html__("Random","wpqa"),"popular" => esc_html__("Most Answered","wpqa"),"most_visited" => esc_html__("Most visited","wpqa"),"most_voted" => esc_html__("Most voted","wpqa"),"most_reacted" => esc_html__("Most reacted","wpqa"),"no_response" => esc_html__("No response","wpqa")),
			'std'     => 'popular',
			'type'    => 'select',
		),
		array(
			'div'       => 'div',
			'condition' => 'question_style:is(style_2)',
			'type'      => 'heading-2'
		),
		array(
			'name' => esc_html__('Display image?','wpqa'),
			'id'   => 'display_image_question',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Display video if there?','wpqa'),
			'id'   => 'display_video_question',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name' => esc_html__('Display date?','wpqa'),
			'id'   => 'display_date_2_question',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
		array(
			'name' => esc_html__('Display answers meta?','wpqa'),
			'id'   => 'display_answer_meta',
			'type' => 'checkbox',
			'std'  => 'on'
		),
		array(
			'name'    => esc_html__('Specific date.','wpqa'),
			'desc'    => esc_html__('Select the specific date.','wpqa'),
			'id'      => "specific_date",
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
		),
		array(
			'name'    => esc_html__('Display','wpqa'),
			'id'      => 'display_question',
			'options' => array("lasts" => esc_html__("Latest Questions","wpqa"),"category" => esc_html__("Single Category","wpqa"),"categories" => esc_html__("Multiple Categories","wpqa"),"exclude_categories" => esc_html__("Exclude Categories","wpqa"),"custom_posts" => esc_html__("Custom Questions","wpqa")),
			'std'     => 'recent',
			'type'    => 'select'
		),
		array(
			'name'      => esc_html__('Category','wpqa'),
			'id'        => 'category_question',
			'type'      => 'select_category',
			'taxonomy'  => wpqa_question_categories,
			'condition' => 'display_question:is(category)'
		),
		array(
			'name'      => esc_html__('Categories','wpqa'),
			'id'        => 'categories_question',
			'type'      => 'multicheck_category',
			'taxonomy'  => wpqa_question_categories,
			'condition' => 'display_question:is(categories)'
		),
		array(
			'name'      => esc_html__('Exclude Categories','wpqa'),
			'id'        => 'exclude_categories_question',
			'type'      => 'multicheck_category',
			'taxonomy'  => wpqa_question_categories,
			'condition' => 'display_question:is(exclude_categories)'
		),
		array(
			'name'      => esc_html__('Custom questions','wpqa'),
			'id'        => 'custom_questions',
			'type'      => 'text',
			'condition' => 'display_question:is(custom_posts)'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
		
		array(
			'div'       => 'div',
			'condition' => 'post_or_question:is(post),tabs:has(display_comments)',
			'type'      => 'heading-2'
		),
		array(
			'name' => esc_html__('Comments','wpqa'),
			'type' => 'info',
		),
		array(
			'name'    => esc_html__('Specific date.','wpqa'),
			'desc'    => esc_html__('Select the specific date.','wpqa'),
			'id'      => "specific_date_comments",
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
		),
		array(
			'name' => esc_html__('The number of words excerpt comments','wpqa'),
			'id'   => 'excerpt_comment',
			'type' => 'text',
			'std'  => '10',
		),
		array(
			'name' => esc_html__('Number of comments to show','wpqa'),
			'id'   => 'comments_number',
			'type' => 'text',
			'std'  => '5',
		),
		array(
			'name' => esc_html__('Display author image?','wpqa'),
			'id'   => 'images_comment',
			'type' => 'checkbox',
			'std'  => 'on',
		),
		array(
			'name' => esc_html__('Display date?','wpqa'),
			'id'   => 'display_date_post',
			'type' => 'checkbox',
			'std'  => 'on',
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
		
		array(
			'div'       => 'div',
			'condition' => 'post_or_question:is('.wpqa_questions_type.'),tabs_questions:has(display_comments)',
			'type'      => 'heading-2'
		),
		array(
			'name' => esc_html__('Answers','wpqa'),
			'type' => 'info',
		),
		array(
			'name'    => esc_html__('Specific date.','wpqa'),
			'desc'    => esc_html__('Select the specific date.','wpqa'),
			'id'      => "specific_date_answers",
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
		),
		array(
			'name' => esc_html__('The number of words excerpt answers','wpqa'),
			'id'   => 'excerpt_answer',
			'type' => 'text',
			'std'  => '10',
		),
		array(
			'name' => esc_html__('Number of answers to show','wpqa'),
			'id'   => 'answers_number',
			'type' => 'text',
			'std'  => '5',
		),
		array(
			'name' => esc_html__('Display author image?','wpqa'),
			'id'   => 'images_answer',
			'type' => 'checkbox',
			'std'  => 'on',
		),
		array(
			'name' => esc_html__('Display date?','wpqa'),
			'id'   => 'display_date',
			'type' => 'checkbox',
			'std'  => 'on',
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
	);

	if ($activate_knowledgebase == true) {
		$count = (int)(count($options['tabs-widget'])-1);

		$options['tabs-widget'] = wpqa_array_insert_after($options['tabs-widget'],$count,array(
			array(
				'div'       => 'div',
				'condition' => 'post_or_question:is('.wpqa_knowledgebase_type.'),tabs_knowledgebases:has(display_posts)',
				'type'      => 'heading-2'
			),
			array(
				'name' => esc_html__('Number of knowledgebases to show','wpqa'),
				'id'   => 'knowledgebases_per_page',
				'type' => 'text',
				'std'  => '5',
			),
			array(
				'name' => esc_html__('The excerpt title','wpqa'),
				'id'   => 'excerpt_title_knowledgebase',
				'type' => 'text',
				'std'  => '10',
			),
			array(
				'name'    => esc_html__('Order by','wpqa'),
				'id'      => 'orderby_knowledgebase',
				'options' => array("recent" => esc_html__("Recent","wpqa"),"random" => esc_html__("Random","wpqa"),"most_visited" => esc_html__("Most visited","wpqa"),"most_voted" => esc_html__("Most voted","wpqa")),
				'std'     => 'popular',
				'type'    => 'select'
			),
			array(
				'name'    => esc_html__('Specific date.','wpqa'),
				'desc'    => esc_html__('Select the specific date.','wpqa'),
				'id'      => "specific_date_knowledgebase",
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
			),
			array(
				'name'    => esc_html__('Display','wpqa'),
				'id'      => 'display_knowledgebase',
				'options' => array("lasts" => esc_html__("Latest Knowledgebases","wpqa"),"category" => esc_html__("Single Category","wpqa"),"categories" => esc_html__("Multiple Categories","wpqa"),"exclude_categories" => esc_html__("Exclude Categories","wpqa"),"custom_posts" => esc_html__("Custom Knowledgebases","wpqa")),
				'std'     => 'recent',
				'type'    => 'select'
			),
			array(
				'name'      => esc_html__('Category','wpqa'),
				'id'        => 'category_knowledgebase',
				'type'      => 'select_category',
				'taxonomy'  => wpqa_knowledgebase_categories,
				'condition' => 'display_knowledgebase:is(category)'
			),
			array(
				'name'      => esc_html__('Categories','wpqa'),
				'id'        => 'categories_knowledgebase',
				'type'      => 'multicheck_category',
				'taxonomy'  => wpqa_knowledgebase_categories,
				'condition' => 'display_knowledgebase:is(categories)'
			),
			array(
				'name'      => esc_html__('Exclude Categories','wpqa'),
				'id'        => 'exclude_categories_knowledgebase',
				'type'      => 'multicheck_category',
				'taxonomy'  => wpqa_knowledgebase_categories,
				'condition' => 'display_knowledgebase:is(exclude_categories)'
			),
			array(
				'name'      => esc_html__('Custom knowledgebases','wpqa'),
				'id'        => 'custom_knowledgebases',
				'type'      => 'text',
				'condition' => 'display_knowledgebase:is(custom_posts)'
			),
			array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			),
		));

		$options['tabs-widget'] = wpqa_array_insert_after($options['tabs-widget'],3,array(
			array(
				'name'      => esc_html__("Select What's tabs show","wpqa"),
				'id'        => 'tabs_knowledgebases',
				'type'      => 'multicheck',
				'sort'      => 'yes',
				'std'       => array(
					"display_posts"    => array("sort" => esc_html__("Display knowledgebases?","wpqa"),"value" => "display_posts"),
					"display_tags"     => array("sort" => esc_html__("Display tags?","wpqa"),"value" => "display_tags"),
				),
				'options'   => array(
					"display_posts"    => array("sort" => esc_html__("Display knowledgebases?","wpqa"),"value" => "display_posts"),
					"display_tags"     => array("sort" => esc_html__("Display tags?","wpqa"),"value" => "display_tags"),
				),
				'condition' => 'post_or_question:is('.wpqa_knowledgebase_type.')'
			),
		));
	}

	$options['tag_cloud'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Trending Tags'
		),
		array(
			'name' => esc_html__('Number of tags to show','wpqa'),
			'id'   => 'number_tags',
			'type' => 'text',
			'std'  => '21'
		),
	);

	$user_sort = array(
		"user_registered" => "Register",
		"display_name"    => "Name",
		"ID"              => "ID",
		"question_count"  => "Questions",
		"answers"         => "Answers",
		"the_best_answer" => "Best Answers",
		"points"          => "Points",
		"post_count"      => "Posts",
		"comments"        => "Comments"
	);
	
	if ($active_points != "on") {
		unset($user_sort["points"]);
	}
	
	$options['users-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Users'
		),
		array(
			'name' => esc_html__('Add crown for the users','wpqa'),
			'id'   => 'crown_king',
			'type' => 'checkbox',
		),
		array(
			'name' => esc_html__('Number of users to show','wpqa'),
			'id'   => 'user_number',
			'type' => 'text',
			'std'  => '3'
		),
		array(
			'name'    => esc_html__('Choose the user roles show','wpqa'),
			'id'      => 'user_group',
			'options' => wpqa_options_roles(),
			'std'     => array("administrator","editor","author","contributor","subscriber"),
			'type'    => 'multicheck'
		),
		array(
			'name' => esc_html__('This option will work only if you activated, Question settings/Questions category settings/Activate the points by category.','wpqa'),
			'type' => 'info',
		),
		array(
			'name' => esc_html__('Display widget at categories only with sort by points?','wpqa'),
			'id'   => 'points_categories',
			'type' => 'checkbox',
		),
		array(
			'name'      => esc_html__('Order by','wpqa'),
			'id'        => 'user_sort',
			'options'   => $user_sort,
			'std'       => 'user_registered',
			'condition' => 'points_categories:is(0)',
			'type'      => 'select'
		),
		array(
			'div'       => 'div',
			'condition' => 'user_sort:is(points)',
			'type'      => 'heading-2'
		),
		array(
			'name' => esc_html__('This option will work only if you activated, Question settings/General settings/Activate the points sort with specific days.','wpqa'),
			'type' => 'info',
		),
		array(
			'name' => esc_html__('Do you need to sort your users by points for specific day, week, month or year?','wpqa'),
			'id'   => 'specific_points',
			'type' => 'checkbox',
		),
		array(
			'name'      => esc_html__('Specific time','wpqa'),
			'id'        => 'specific_time',
			'options'   => array("day" => esc_html__("Day","wpqa"),"week" => esc_html__("Week","wpqa"),"month" => esc_html__("Month","wpqa"),"year" => esc_html__("Year","wpqa")),
			'std'       => 'day',
			'condition' => 'specific_points:not(0)',
			'type'      => 'radio'
		),
		array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		),
		array(
			'name'    => esc_html__('Order','wpqa'),
			'id'      => 'user_order',
			'options' => array("DESC" => esc_html__("Descending","wpqa"),"ASC" => esc_html__("Ascending","wpqa")),
			'std'     => 'DESC',
			'type'    => 'select'
		),
		array(
			'name'      => esc_html__('Do you need to activate icon for the main storable?, it works for Questions, Answers, Best Answers, Points, Posts, or Comments.','wpqa'),
			'id'        => 'show_icon',
			'type'      => 'checkbox',
			'condition' => 'points_categories:is(0)',
		),
	);
	
	$options['adv250x250-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Adv 250x250'
		),
		array(
			'name'    => esc_html__('Advertising type','wpqa'),
			'id'      => 'adv_type',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		),
		array(
			'name'      => esc_html__('Image URL','wpqa'),
			'id'        => 'adv_img',
			'type'      => 'upload',
			'condition' => 'adv_type:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Advertising url','wpqa'),
			'id'        => 'adv_href',
			'type'      => 'text',
			'condition' => 'adv_type:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'adv_link',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'adv_type:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		),
		array(
			'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
			'id'        => 'adv_code',
			'type'      => 'textarea',
			'condition' => 'adv_type:is(display_code)'
		),
	);
	
	$options['adv120x600-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Adv 120x600'
		),
		array(
			'name'    => esc_html__('Advertising type','wpqa'),
			'id'      => 'adv_type',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		),
		array(
			'name'      => esc_html__('Image URL','wpqa'),
			'id'        => 'adv_img',
			'type'      => 'upload',
			'condition' => 'adv_type:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Advertising url','wpqa'),
			'id'        => 'adv_href',
			'type'      => 'text',
			'condition' => 'adv_type:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'adv_link',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'adv_type:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		),
		array(
			'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
			'id'        => 'adv_code',
			'type'      => 'textarea',
			'condition' => 'adv_type:is(display_code)'
		),
	);
	
	$options['adv234x60-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Adv 234x60'
		),
		array(
			'name'    => esc_html__('Advertising type','wpqa'),
			'id'      => 'adv_type',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		),
		array(
			'name'      => esc_html__('Image URL','wpqa'),
			'id'        => 'adv_img',
			'type'      => 'upload',
			'condition' => 'adv_type:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Advertising url','wpqa'),
			'id'        => 'adv_href',
			'type'      => 'text',
			'condition' => 'adv_type:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'adv_link',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'adv_type:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		),
		array(
			'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
			'id'        => 'adv_code',
			'type'      => 'textarea',
			'condition' => 'adv_type:is(display_code)'
		),
	);
	
	$options['adv120x240-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Adv 120x240'
		),
		
		array(
			'name'    => esc_html__('Advertising type 1','wpqa'),
			'id'      => 'adv_type_1',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		),
		array(
			'name'      => esc_html__('Image URL','wpqa'),
			'id'        => 'adv_img_1',
			'type'      => 'upload',
			'condition' => 'adv_type_1:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Advertising url','wpqa'),
			'id'        => 'adv_href_1',
			'type'      => 'text',
			'condition' => 'adv_type_1:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'adv_link_1',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'adv_type_1:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		),
		array(
			'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
			'id'        => 'adv_code_1',
			'type'      => 'textarea',
			'condition' => 'adv_type_1:is(display_code)'
		),
		
		array(
			'name'    => esc_html__('Advertising type 2','wpqa'),
			'id'      => 'adv_type_2',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		),
		array(
			'name'      => esc_html__('Image URL','wpqa'),
			'id'        => 'adv_img_2',
			'type'      => 'upload',
			'condition' => 'adv_type_2:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Advertising url','wpqa'),
			'id'        => 'adv_href_2',
			'type'      => 'text',
			'condition' => 'adv_type_2:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'adv_link_2',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'adv_type_2:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		),
		array(
			'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
			'id'        => 'adv_code_2',
			'type'      => 'textarea',
			'condition' => 'adv_type_2:is(display_code)'
		),
		
		array(
			'name'    => esc_html__('Advertising type 3','wpqa'),
			'id'      => 'adv_type_3',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		),
		array(
			'name'      => esc_html__('Image URL','wpqa'),
			'id'        => 'adv_img_3',
			'type'      => 'upload',
			'condition' => 'adv_type_3:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Advertising url','wpqa'),
			'id'        => 'adv_href_3',
			'type'      => 'text',
			'condition' => 'adv_type_3:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'adv_link_3',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'adv_type_3:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		),
		array(
			'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
			'id'        => 'adv_code_3',
			'type'      => 'textarea',
			'condition' => 'adv_type_3:is(display_code)'
		),
		
		array(
			'name'    => esc_html__('Advertising type 4','wpqa'),
			'id'      => 'adv_type_4',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		),
		array(
			'name'      => esc_html__('Image URL','wpqa'),
			'id'        => 'adv_img_4',
			'type'      => 'upload',
			'condition' => 'adv_type_4:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Advertising url','wpqa'),
			'id'        => 'adv_href_4',
			'type'      => 'text',
			'condition' => 'adv_type_4:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'adv_link_4',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'adv_type_4:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		),
		array(
			'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
			'id'        => 'adv_code_4',
			'type'      => 'textarea',
			'condition' => 'adv_type_4:is(display_code)'
		),
	);
	
	$options['adv125x125-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Adv 125x125'
		),
		
		array(
			'name'    => esc_html__('Advertising type 1','wpqa'),
			'id'      => 'adv_type_1',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		),
		array(
			'name'      => esc_html__('Image URL','wpqa'),
			'id'        => 'adv_img_1',
			'type'      => 'upload',
			'condition' => 'adv_type_1:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Advertising url','wpqa'),
			'id'        => 'adv_href_1',
			'type'      => 'text',
			'condition' => 'adv_type_1:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'adv_link_1',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'adv_type_1:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		),
		array(
			'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
			'id'        => 'adv_code_1',
			'type'      => 'textarea',
			'condition' => 'adv_type_1:is(display_code)'
		),
		
		array(
			'name'    => esc_html__('Advertising type 2','wpqa'),
			'id'      => 'adv_type_2',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		),
		array(
			'name'      => esc_html__('Image URL','wpqa'),
			'id'        => 'adv_img_2',
			'type'      => 'upload',
			'condition' => 'adv_type_2:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Advertising url','wpqa'),
			'id'        => 'adv_href_2',
			'type'      => 'text',
			'condition' => 'adv_type_2:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'adv_link_2',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'adv_type_2:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		),
		array(
			'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
			'id'        => 'adv_code_2',
			'type'      => 'textarea',
			'condition' => 'adv_type_2:is(display_code)'
		),
		
		array(
			'name'    => esc_html__('Advertising type 3','wpqa'),
			'id'      => 'adv_type_3',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		),
		array(
			'name'      => esc_html__('Image URL','wpqa'),
			'id'        => 'adv_img_3',
			'type'      => 'upload',
			'condition' => 'adv_type_3:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Advertising url','wpqa'),
			'id'        => 'adv_href_3',
			'type'      => 'text',
			'condition' => 'adv_type_3:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'adv_link_3',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'adv_type_3:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		),
		array(
			'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
			'id'        => 'adv_code_3',
			'type'      => 'textarea',
			'condition' => 'adv_type_3:is(display_code)'
		),
		
		array(
			'name'    => esc_html__('Advertising type 4','wpqa'),
			'id'      => 'adv_type_4',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
		),
		array(
			'name'      => esc_html__('Image URL','wpqa'),
			'id'        => 'adv_img_4',
			'type'      => 'upload',
			'condition' => 'adv_type_4:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Advertising url','wpqa'),
			'id'        => 'adv_href_4',
			'type'      => 'text',
			'condition' => 'adv_type_4:is(custom_image)'
		),
		array(
			'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
			'id'        => 'adv_link_4',
			'std'       => "new_page",
			'type'      => 'select',
			'condition' => 'adv_type_4:is(custom_image)',
			'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
		),
		array(
			'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
			'id'        => 'adv_code_4',
			'type'      => 'textarea',
			'condition' => 'adv_type_4:is(display_code)'
		),
	);
	
	$options['social-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Follow'
		),
	);
	
	$options['subscribe-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Subscribe'
		),
		array(
			'name' => esc_html__('Newsletter action','wpqa'),
			'id'   => 'newsletter_action',
			'type' => 'text',
		),
	);
	
	$options['widget_counter'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Social Statistics'
		),
		array(
			'name' => esc_html__('Facebook Page ID/Name','wpqa'),
			'id'   => 'facebook',
			'type' => 'text',
			'std'  => '2code.info'
		),
		array(
			'name' => esc_html__('Twitter','wpqa'),
			'id'   => 'twitter',
			'type' => 'text',
			'std'  => '2codeThemes'
		),
		array(
			'name' => esc_html__('Channel id','wpqa'),
			'id'   => 'youtube',
			'type' => 'text',
			'std'  => 'UCht9cayN2rRaXk5VgMJtAsA'
		),
		array(
			'name' => esc_html__('Vimeo Page ID/Name','wpqa'),
			'id'   => 'vimeo',
			'type' => 'text',
			'std'  => 'vimeo'
		),
		array(
			'name' => esc_html__('Dribbble Page ID/Name','wpqa'),
			'id'   => 'dribbble',
			'type' => 'text',
			'std'  => 'begha'
		),
		array(
			'name' => esc_html__('Pinterest','wpqa'),
			'id'   => 'pinterest',
			'type' => 'text',
			'std'  => 'https://www.pinterest.com/envato/'
		),
		array(
			'name' => esc_html__('Instagram','wpqa'),
			'id'   => 'instagram',
			'type' => 'text',
			'std'  => 'kaboompics'
		),
		array(
			'name' => esc_html__('Behance','wpqa'),
			'id'   => 'behance',
			'type' => 'text',
			'std'  => 'begha'
		),
		array(
			'name' => esc_html__('Soundcloud','wpqa'),
			'id'   => 'soundcloud',
			'type' => 'text',
			'std'  => 'envato'
		),
		array(
			'name' => esc_html__('Github','wpqa'),
			'id'   => 'github',
			'type' => 'text',
			'std'  => 'kailoon'
		),
		array(
			'name' => esc_html__('Your socials numbers is saved in the cache each hour if you want delete the cache now click on Save.','wpqa'),
			'type' => 'info',
		),
	);
	
	$options['facebook-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Facebook'
		),
		array(
			'name' => esc_html__('Facebook link','wpqa'),
			'id'   => 'facebook_link',
			'type' => 'text',
			'std'  => 'https://www.facebook.com/2code.info'
		),
		array(
			'name' => esc_html__('Width','wpqa'),
			'id'   => 'width',
			'type' => 'text',
			'std'  => (has_himer() || has_knowly()?'311':'229')
		),
		array(
			'name' => esc_html__('Height','wpqa'),
			'id'   => 'height',
			'type' => 'text',
			'std'  => '214'
		),
		array(
			'name' => esc_html__('Background','wpqa'),
			'id'   => 'background',
			'type' => 'color',
			'std'  => '#FFFFFF'
		),
		array(
			'name' => esc_html__('Border color','wpqa'),
			'id'   => 'border_color',
			'type' => 'color',
			'std'  => '#dedede'
		),
	);
	
	$options['video-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Video'
		),
		array(
			'name' => esc_html__('Height','wpqa'),
			'id'   => 'height',
			'type' => 'text',
			'std'  => '200'
		),
		array(
			'name'    => esc_html__('Video Type','wpqa'),
			'id'      => 'video_type',
			'options' => array("youtube" => esc_html__("Youtube","wpqa"),"vimeo" => esc_html__("Vimeo","wpqa"),"daily" => esc_html__("Dailymotion","wpqa"),"facebook" => esc_html__("Facebook video","wpqa"),"embed" => esc_html__("Embed Code","wpqa")),
			'std'     => 'youtube',
			'type'    => 'select'
		),
		array(
			'name'      => esc_html__('Video id','wpqa'),
			'desc'      => esc_html__('Put the Video ID here: https://www.youtube.com/watch?v=JuyB7NO0EYY Ex: "JuyB7NO0EYY"','wpqa'),
			'id'        => 'video_id',
			'type'      => 'text',
			'condition' => 'video_type:not(embed)',
		),
		array(
			'name'      => esc_html__('Embed Code','wpqa'),
			'id'        => 'embed_code',
			'type'      => 'textarea',
			'condition' => 'video_type:is(embed)',
		),
	);
	
	$options['twitter-widget'] = array(
		array(
			'name' => esc_html__('Title','wpqa'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Latest Tweets'
		),
		array(
			'name' => esc_html__('Number of tweets to show','wpqa'),
			'id'   => 'no_of_tweets',
			'type' => 'text',
			'std'  => '5'
		),
		array(
			'name' => esc_html__('Twitter username','wpqa'),
			'id'   => 'accounts',
			'type' => 'text',
			'std'  => '2codeThemes'
		),
	);
	
	return $options;
}?>