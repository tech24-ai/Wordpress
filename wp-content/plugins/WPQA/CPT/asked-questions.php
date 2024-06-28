<?php

/* @author    2codeThemes
*  @package   WPQA/CPT
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Make the questions with number */
add_filter('post_type_link','wpqa_asked_question_number_slug',10,2);
function wpqa_asked_question_number_slug($post_link,$post) {
	$asked_question_slug_numbers = wpqa_options("asked_question_slug_numbers");
	if ($asked_question_slug_numbers == "on") {
		if (wpqa_asked_questions_type != $post->post_type || 'publish' != $post->post_status) {
			return $post_link;
		}
		$asked_question_slug = wpqa_options('asked_question_slug');
		$asked_question_slug = ($asked_question_slug != ""?$asked_question_slug:wpqa_asked_questions_type);
		$post_link = str_replace('/'.$asked_question_slug.'/'.$post->post_name,'/'.$asked_question_slug.'/'.$post->ID,$post_link);
	}
	return $post_link;
}
/* Remove question slug */
add_filter('post_type_link','wpqa_asked_remove_slug',10,2);
function wpqa_asked_remove_slug($post_link,$post) {
	$remove_asked_question_slug = wpqa_options("remove_asked_question_slug");
	if ($remove_asked_question_slug == "on") {
		if (wpqa_asked_questions_type != $post->post_type || 'publish' != $post->post_status) {
			return $post_link;
		}
		$asked_question_slug = wpqa_options('asked_question_slug');
		$asked_question_slug = ($asked_question_slug != ""?$asked_question_slug:wpqa_asked_questions_type);
		$post_link = str_replace('/'.$asked_question_slug.'/','/',$post_link);
	}
	return $post_link;
}
/* Asked question post type */
if (!function_exists('wpqa_asked_question_post_type')) :
	function wpqa_asked_question_post_type() {
		$remove_asked_question_slug = wpqa_options("remove_asked_question_slug");
		$asked_question_slug = wpqa_options('asked_question_slug');
		$asked_question_slug = ($asked_question_slug != ""?$asked_question_slug:wpqa_asked_questions_type);
	   
		register_post_type(wpqa_asked_questions_type,
			array(
				'label' => esc_html__('Asked Questions','wpqa'),
				'labels' => array(
					'name'               => esc_html__('Asked Questions','wpqa'),
					'singular_name'      => esc_html__('Asked Questions','wpqa'),
					'menu_name'          => esc_html__('Asked Questions','wpqa'),
					'name_admin_bar'     => esc_html__('Asked Question','wpqa'),
					'add_new'            => esc_html__('Add New','wpqa'),
					'add_new_item'       => esc_html__('Add New Asked Question','wpqa'),
					'new_item'           => esc_html__('New Asked Question','wpqa'),
					'edit_item'          => esc_html__('Edit Asked Question','wpqa'),
					'view_item'          => esc_html__('View Asked Question','wpqa'),
					'view_items'         => esc_html__('View Asked Questions','wpqa'),
					'all_items'          => esc_html__('Asked Questions','wpqa'),
					'search_items'       => esc_html__('Search Asked Questions','wpqa'),
					'parent_item_colon'  => esc_html__('Parent Asked Question:','wpqa'),
					'not_found'          => esc_html__('No Asked Questions Found.','wpqa'),
					'not_found_in_trash' => esc_html__('No Asked Questions Found in Trash.','wpqa'),
				),
				'description'         => '',
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type='.wpqa_questions_type,
				'capability_type'     => 'post',
				'capabilities'        => array('create_posts' => 'do_not_allow'),
				'map_meta_cap'        => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => false,
				'hierarchical'        => false,
				'rewrite'             => array('slug' => apply_filters("wpqa_asked_question_slug",($remove_asked_question_slug == "on"?false:$asked_question_slug)),'hierarchical' => true,'with_front' => false),
				'query_var'           => true,
				'show_in_rest'        => true,
				'has_archive'         => false,
				'menu_position'       => 5,
				'menu_icon'           => "dashicons-editor-help",
				'supports'            => array('title','editor','comments','author'),
			)
		);

		$asked_question_slug_numbers = wpqa_options("asked_question_slug_numbers");
		if ($asked_question_slug_numbers == "on") {
			$removed = ($remove_asked_question_slug == "on"?'':$asked_question_slug.'/');
			add_rewrite_rule($removed.'([0-9]+)?$','index.php?post_type='.$asked_question_slug.'&p=$matches[1]','top');
		}
	}
endif;
add_action('wpqa_init','wpqa_asked_question_post_type',0);
/* Admin columns for post types */
add_filter('manage_edit-'.wpqa_asked_questions_type.'_columns', 'wpqa_question_columns');
add_action('manage_'.wpqa_asked_questions_type.'_posts_custom_column','wpqa_question_custom_columns',2);?>