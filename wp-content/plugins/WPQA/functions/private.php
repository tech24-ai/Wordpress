<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Private question */
if (!function_exists('wpqa_private')) :
	function wpqa_private($post_id,$first_user,$second_user,$post_type = wpqa_questions_type) {
		$key_private = ($post_type == wpqa_knowledgebase_type?"knowledgebase":"question");
		$key_category = ($post_type == wpqa_knowledgebase_type?wpqa_knowledgebase_categories:wpqa_question_categories);
		$get_private_post = get_post_meta($post_id,"private_".$key_private,true);
		$user_id = get_post_meta($post_id,"user_id",true);
		$user_is_comment = get_post_meta($post_id,"user_is_comment",true);
		$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
		$post_category = wp_get_post_terms($post_id,$key_category,array("fields" => "all"));
		
		$yes_private = 0;
		if (is_array($post_category) && isset($post_category[0])) {
			$wpqa_private = get_term_meta($post_category[0]->term_id,prefix_terms."private",true);
		}
		if (is_array($post_category) && isset($post_category[0])) {
			if (isset($post_category[0]) && $wpqa_private == "on") {
				if (isset($first_user) && $first_user > 0 && $first_user == $second_user) {
					$yes_private = 1;
				}
			}else if (isset($post_category[0]) && $wpqa_private != "on" && $post_category[0]->parent == 0) {
				$yes_private = 1;
			}
			
			if (isset($post_category[0]) && $post_category[0]->parent > 0) {
				$wpqa_private_parent = get_term_meta($post_category[0]->parent,prefix_terms."private",true);
				if ($wpqa_private_parent == "on" && isset($first_user) && $first_user > 0 && $first_user == $second_user) {
					$yes_private = 1;
				}else if (isset($post_category[0]) && $wpqa_private_parent == "on" && !isset($first_user)) {
					$yes_private = 0;
				}else if (isset($post_category[0]) && $wpqa_private_parent != "on") {
					$yes_private = 1;
				}
			}
		}else {
			$yes_private = 1;
		}
		$set_private = apply_filters("wpqa_set_private_".$key_private,false,$post_id,$first_user,$second_user);
		if ($set_private == true || $get_private_post == 1 || $get_private_post == "on" || ($user_id != "" && $user_id > 0 && $user_is_comment != true)) {
			$yes_private = 0;
			if ((isset($first_user) && $first_user > 0 && $first_user == $second_user) || ($user_id > 0 && $user_id == $second_user) || ($anonymously_user > 0 && $anonymously_user == $second_user)) {
				$yes_private = 1;
			}
		}
		
		if (is_super_admin($second_user)) {
			$yes_private = 1;
		}
		return $yes_private;
	}
endif;
/* Not show the post */
add_action('wp','wpqa_not_show_posts');
if (!function_exists('wpqa_not_show_posts')) :
	function wpqa_not_show_posts() {
		global $post;
		if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) {
			$user_get_current_user_id = get_current_user_id();
			$private_key = (is_singular(wpqa_knowledgebase_type)?"private_knowledgebase_content":"private_question_content");
			$private_post_content = wpqa_options("private_question_content");
			$yes_private = wpqa_private($post->ID,$post->post_author,$user_get_current_user_id,wpqa_questions_type);
			if (!is_super_admin($user_get_current_user_id) && $yes_private != 1 && $private_post_content != "on") {
				global $wp_query;
				$wp_query->set_404();
				status_header(404);
			}
		}
	}
endif;
/* Remove private posts from API */
add_filter('rest_prepare_'.wpqa_questions_type,'wpqa_remove_user_posts',10,3);
add_filter('rest_prepare_'.wpqa_asked_questions_type,'wpqa_remove_user_posts',10,3);
add_filter('rest_prepare_'.wpqa_knowledgebase_type,'wpqa_remove_user_posts',10,3);
if (!function_exists('wpqa_remove_user_posts')) :
	function wpqa_remove_user_posts($data,$post,$request) {
		$user_get_current_user_id = get_current_user_id();
		$_data = $data->data;
		$params = $request->get_params();
		$private_key = ($post->post_type == wpqa_knowledgebase_type?"private_knowledgebase":"private_question");
		$user_id = get_post_meta($_data['id'],"user_id",true);
		$user_is_comment = get_post_meta($_data['id'],"user_is_comment",true);
		$private_post = get_post_meta($_data['id'],$private_key,true);
		if (!is_super_admin($user_get_current_user_id) && ($private_post == 1 || $private_post == "on" || ($user_id != "" && $user_is_comment != true))) {
			unset($_data["id"]);
			unset($_data["guid"]);
			unset($_data["content"]);
			unset($_data["slug"]);
			unset($_data["title"]);
			unset($_data["link"]);
			unset($_data["author"]);
			unset($_data["_links"]);
			foreach($data->get_links() as $_linkKey => $_linkVal) {
				$data->remove_link($_linkKey);
			}
		}
		$data->data = $_data;
		return $data;
	}
endif;
/* Feed request */
add_filter('request','wpqa_feed_request');
if (!function_exists('wpqa_feed_request')) :
	function wpqa_feed_request ($qv) {
		if (isset($qv['feed']) && !isset($qv['post_type'])) {
			$qv['post_type'] = array('post',wpqa_questions_type,wpqa_asked_questions_type,wpqa_knowledgebase_type);
		}
		return $qv;
	}
endif;
/* Remove private posts from feed */
add_action('pre_get_posts','wpqa_feed_private_post');
if (!function_exists('wpqa_feed_private_post')) :
	function wpqa_feed_private_post($query) {
		if (is_admin() || !$query->is_main_query())
			return;
		
		if (is_category()) {
			$cat_name  = esc_html(get_query_var('category_name'));
			$cat_id    = esc_html(get_query_var('cat'));
			$get_term  = ($cat_name != ""?get_term_by('slug',$cat_name,'category'):get_term_by('id',$cat_id,'category'));
			$term_id   = (isset($get_term->term_id)?$get_term->term_id:0);
			$term_name = (isset($get_term->name)?$get_term->name:"");
			$custom_blog_setting = wpqa_term_meta("custom_blog_setting",$term_id);
			if ($custom_blog_setting == "on") {
				$post_number = wpqa_term_meta("post_number",$term_id);
			}
		}else if (is_tag()) {
			$get_term  = get_term_by('slug',esc_html(get_query_var('tag')),'post_tag');
			$term_id   = (isset($get_term->term_id)?$get_term->term_id:0);
			$term_name = (isset($get_term->name)?$get_term->name:"");
		}else if (is_tax()) {
			if (isset($query->tax_query->queries[0]["terms"][0]) && isset($query->tax_query->queries[0]["taxonomy"])) {
				$get_term     = get_term_by('slug',esc_html($query->tax_query->queries[0]["terms"][0]),$query->tax_query->queries[0]["taxonomy"]);
				$term_id      = (isset($get_term->term_id)?$get_term->term_id:0);
				$term_name    = (isset($get_term->name)?$get_term->name:"");
				$tax_filter   = apply_filters(wpqa_prefix_theme."_before_question_category",false);
				$tax_question = apply_filters(wpqa_prefix_theme."_question_category",wpqa_question_categories);
				if (is_tax(wpqa_question_categories) || $tax_filter == true) {
					$custom_question_setting = wpqa_term_meta("custom_question_setting",$term_id);
					if ($custom_question_setting == "on") {
						$post_number = wpqa_term_meta("question_number",$term_id);
					}
				}
			}
		}else if (is_embed()) {
			$post_type = (isset($query->query_vars) && isset($query->query_vars['post_type'])?$query->query_vars['post_type']:'');
			if ($post_type != '' && ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type)) {
				$remove_question_slug = wpqa_options('remove_question_slug');
				$remove_asked_question_slug = wpqa_options('remove_asked_question_slug');
				$remove_knowledgebase_slug = wpqa_options('remove_knowledgebase_slug');
				$question_slug_numbers = wpqa_options('question_slug_numbers');
				$asked_question_slug_numbers = wpqa_options('asked_question_slug_numbers');
				$knowledgebase_slug_numbers = wpqa_options('knowledgebase_slug_numbers');
				if (($post_type == wpqa_questions_type && ($remove_question_slug == 'on' || $question_slug_numbers == 'on')) || ($post_type == wpqa_asked_questions_type && ($remove_asked_question_slug == 'on' || $asked_question_slug_numbers == 'on')) || ($post_type == wpqa_knowledgebase_type && ($remove_knowledgebase_slug == 'on' || $knowledgebase_slug_numbers == 'on'))) {
					$post_id = (isset($query->query_vars) && isset($query->query_vars[$post_type])?$query->query_vars[$post_type]:'');
					if ($post_id != '') {
						$get_post = get_post($post_id);
						if (isset($get_post->post_name)) {
							$query->set($post_type,$get_post->post_name);
						}
					}
				}
			}
		}
		
		if (isset($term_id) && $term_id > 0) {
			$query->set('wpqa_term_id',$term_id);
		}
		
		if (isset($term_name) && $term_name != "") {
			$query->set('wpqa_term_name',$term_name);
		}
		
		if (isset($post_number) && $post_number > 0) {
			$query->set('posts_per_page',$post_number);
		}

		if (is_feed()) {
			$query->set('meta_query',
				array(
					array(
						array("key" => "private_question","compare" => "NOT EXISTS"),
						array("key" => "private_knowledgebase","compare" => "NOT EXISTS")
					)
				)
			);
		}
	}
endif;
/* Remove private posts from sitemaps */
add_action('wp_sitemaps_posts_query_args','wpqa_sitemaps_private_post',1,2);
if (!function_exists('wpqa_sitemaps_private_post')) :
	function wpqa_sitemaps_private_post($query,$post_type) {
		if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type || $post_type == wpqa_knowledgebase_type) && is_array($query)) {
			$private_key = ($post_type == wpqa_knowledgebase_type?"private_knowledgebase":"private_question");
			$args = array('meta_query' => 
				array(
					array("key" => $private_key,"compare" => "NOT EXISTS")
				)
			);
			$query = array_merge($query,$args);
		}
		return $query;
	}
endif;?>