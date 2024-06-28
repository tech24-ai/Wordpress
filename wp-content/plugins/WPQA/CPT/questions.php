<?php

/* @author    2codeThemes
*  @package   WPQA/CPT
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Make the questions with number */
add_filter('post_type_link','wpqa_question_number_slug',10,2);
function wpqa_question_number_slug($post_link,$post) {
	$question_slug_numbers = wpqa_options("question_slug_numbers");
	if ($question_slug_numbers == "on") {
		if (wpqa_questions_type != $post->post_type || 'publish' != $post->post_status) {
			return $post_link;
		}
		$question_slug = wpqa_options('question_slug');
		$question_slug = ($question_slug != ""?$question_slug:wpqa_questions_type);
		$post_link = str_replace('/'.$question_slug.'/'.$post->post_name,'/'.$question_slug.'/'.$post->ID,$post_link);
	}
	return $post_link;
}
/* Remove question slug */
add_filter('post_type_link','wpqa_question_remove_slug',10,2);
function wpqa_question_remove_slug($post_link,$post) {
	$remove_question_slug = wpqa_options("remove_question_slug");
	if ($remove_question_slug == "on") {
		if (wpqa_questions_type != $post->post_type || 'publish' != $post->post_status) {
			return $post_link;
		}
		$question_slug = wpqa_options('question_slug');
		$question_slug = ($question_slug != ""?$question_slug:wpqa_questions_type);
		$post_link = str_replace('/'.$question_slug.'/','/',$post_link);
	}
	return $post_link;
}
add_action('pre_get_posts','wpqa_parse_request');
function wpqa_parse_request($query) {
	$remove_question_slug = wpqa_options("remove_question_slug");
	$remove_asked_question_slug = wpqa_options("remove_asked_question_slug");
	$remove_group_slug = wpqa_options("remove_group_slug");
	$remove_knowledgebase_slug = wpqa_options("remove_knowledgebase_slug");
	if ($remove_question_slug == "on" || $remove_asked_question_slug == "on" || $remove_knowledgebase_slug == "on" || $remove_group_slug == "on") {
		if (!$query->is_main_query() || 2 != count($query->query) || !isset($query->query['page'])) {
			return;
		}
		if (!empty($query->query['name'])) {
			$query->set('post_type',array('page','post',wpqa_questions_type,wpqa_asked_questions_type,wpqa_knowledgebase_type,'group'));
		}
	}
}
/* Question post type */
if (!function_exists('wpqa_question_post_type')) :
	function wpqa_question_post_type() {
		$remove_question_slug   = wpqa_options("remove_question_slug");

		$ask_question_items     = wpqa_options("ask_question_items");
		$thumbnail              = (isset($ask_question_items["featured_image"]["value"]) && $ask_question_items["featured_image"]["value"] == "featured_image"?array("thumbnail"):array());
		
		$archive_question_slug  = wpqa_options('archive_question_slug');
		$archive_question_slug  = ($archive_question_slug != ""?$archive_question_slug:"questions");
		
		$question_slug          = wpqa_options('question_slug');
		$question_slug          = ($question_slug != ""?$question_slug:wpqa_questions_type);
		
		$category_question_slug = wpqa_options('category_question_slug');
		$category_question_slug = ($category_question_slug != ""?$category_question_slug:wpqa_question_categories);
		
		$tag_question_slug      = wpqa_options('tag_question_slug');
		$tag_question_slug      = ($tag_question_slug != ""?$tag_question_slug:wpqa_question_tags);
	   
		register_post_type(wpqa_questions_type,
			array(
				'label' => esc_html__('Questions','wpqa'),
				'labels' => array(
					'name'               => esc_html__('Questions','wpqa'),
					'singular_name'      => esc_html__('Questions','wpqa'),
					'menu_name'          => esc_html__('Questions','wpqa'),
					'name_admin_bar'     => esc_html_x('Question','name_admin_bar','wpqa'),
					'add_new'            => esc_html__('Add New','wpqa'),
					'add_new_item'       => esc_html__('Add New Question','wpqa'),
					'new_item'           => esc_html__('New Question','wpqa'),
					'edit_item'          => esc_html__('Edit Question','wpqa'),
					'view_item'          => esc_html__('View Question','wpqa'),
					'view_items'         => esc_html__('View Questions','wpqa'),
					'all_items'          => esc_html__('All Questions','wpqa'),
					'search_items'       => esc_html__('Search Questions','wpqa'),
					'parent_item_colon'  => esc_html__('Parent Question:','wpqa'),
					'not_found'          => esc_html__('No Questions Found.','wpqa'),
					'not_found_in_trash' => esc_html__('No Questions Found in Trash.','wpqa'),
				),
				'description'         => '',
				'public'              => true,
				'show_ui'             => true,
				'capability_type'     => 'post',
				'publicly_queryable'  => true,
				'exclude_from_search' => false,
				'hierarchical'        => false,
				'rewrite'             => array('slug' => apply_filters("wpqa_question_slug",($remove_question_slug == "on"?false:$question_slug)),'hierarchical' => true,'with_front' => false),
				'query_var'           => true,
				'show_in_rest'        => true,
				'has_archive'         => apply_filters("wpqa_archive_question",$archive_question_slug),
				'menu_position'       => 5,
				'menu_icon'           => "dashicons-editor-help",
				'supports'            => array_merge($thumbnail,array('title','editor','comments','author')),
				'taxonomies'          => array(wpqa_question_categories,wpqa_question_tags),
			)
		);

		$question_slug_numbers = wpqa_options("question_slug_numbers");
		if ($question_slug_numbers == "on") {
			$removed = ($remove_question_slug == "on"?'':$question_slug.'/');
			add_rewrite_rule($removed.'([0-9]+)?$','index.php?post_type='.$question_slug.'&p=$matches[1]','top');
		}
		
		$labels = array(
			'name'              => esc_html__('Question Categories','wpqa'),
			'singular_name'     => esc_html__('Question Categories','wpqa'),
			'search_items'      => esc_html__('Search Categories','wpqa'),
			'all_items'         => esc_html__('All Categories','wpqa'),
			'parent_item'       => esc_html__('Question Categories','wpqa'),
			'parent_item_colon' => esc_html__('Question Categories','wpqa'),
			'edit_item'         => esc_html__('Edit Category','wpqa'),
			'update_item'       => esc_html__('Edit','wpqa'),
			'add_new_item'      => esc_html__('Add New Category','wpqa'),
			'new_item_name'     => esc_html__('Add New Category','wpqa')
		);
		
		register_taxonomy(wpqa_question_categories,wpqa_questions_type,array(
			'hierarchical' => true,
			'labels'       => $labels,
			'show_ui'      => true,
			'query_var'    => true,
			'show_in_rest' => true,
			'rewrite'      => array('slug' => $category_question_slug,'hierarchical' => true,'with_front' => false),
		));
		
		register_taxonomy( wpqa_question_tags,
			array(wpqa_questions_type),
			array(
				'hierarchical' => false,
				'labels' => array(
					'name'              => esc_html__('Question Tags','wpqa'),
					'singular_name'     => esc_html__('Question Tags','wpqa'),
					'search_items'      => esc_html__('Search Tags','wpqa'),
					'all_items'         => esc_html__('All Tags','wpqa'),
					'parent_item'       => esc_html__('Question Tags','wpqa'),
					'parent_item_colon' => esc_html__('Question Tags','wpqa'),
					'edit_item'         => esc_html__('Edit Tag','wpqa'),
					'update_item'       => esc_html__('Edit','wpqa'),
					'add_new_item'      => esc_html__('Add New Tag','wpqa'),
					'new_item_name'     => esc_html__('Add New Tag','wpqa')
				),
				'show_ui'      => true,
				'query_var'    => true,
				'show_in_rest' => true,
				'rewrite'      => array('slug' => $tag_question_slug,'hierarchical' => true,'with_front' => false),
			)
		);
	}
endif;
add_action('wpqa_init','wpqa_question_post_type',0);
/* Question menus */
add_action('admin_menu','wpqa_add_admin_question');
function wpqa_add_admin_question() {
	add_submenu_page('edit.php?post_type='.wpqa_questions_type,esc_html__('Answers','wpqa'),esc_html__('Answers','wpqa'),'manage_options','edit-comments.php?comment_status=answers');
	add_submenu_page('edit.php?post_type='.wpqa_questions_type,esc_html__('Best Answers','wpqa'),esc_html__('Best Answers','wpqa'),'manage_options','edit-comments.php?comment_status=best-answers');
}
/* Remove meta boxes */
if (!function_exists('wpqa_remove_meta_boxes')) :
	function wpqa_remove_meta_boxes() {
		global $post;
		$category_single_multi = wpqa_options("category_single_multi");
		if ($category_single_multi != "multi") {
			remove_meta_box( wpqa_question_categories.'div', wpqa_questions_type, 'side' );
		}
		if (isset($post->ID) && $post->ID > 0) {
			$get_question_user_id = get_post_meta($post->ID,"user_id",true);
			if ($get_question_user_id != "") {
				remove_meta_box( 'tagsdiv-'.wpqa_question_tags, wpqa_questions_type, 'side' );
			}
		}
	}
endif;
add_action('do_meta_boxes','wpqa_remove_meta_boxes');
/* Admin columns for post types */
if (!function_exists('wpqa_question_columns')) :
	function wpqa_question_columns($columns){
		$columns["cb"]       = "<input type=\"checkbox\">";
		$columns["title"]    = esc_html__("Title","wpqa");
		$columns["type"]     = esc_html__("Type","wpqa");
		$columns["author-q"] = esc_html__("Author","wpqa");
		$columns["category"] = esc_html__("Category","wpqa");
		$columns["tag"]      = esc_html__("Tags","wpqa");
		$columns["comments"] = "<span class='vers comment-grey-bubble' title='".esc_attr__("Answers","wpqa")."'><span class='screen-reader-text'>".esc_html__("Answers","wpqa")."</span></span>";
		$columns["date"]     = esc_html__("Date","wpqa");
		$active_post_stats = wpqa_options("active_post_stats");
		$view = ($active_post_stats == "on"?array('view' => '<span class="dashicons dashicons-visibility dashicons-before"></span>'):array());
		if (isset($columns["author"])) {
			unset($columns["author"]);
		}
		return array_merge($columns,$view);
	}
endif;
add_filter('manage_edit-'.wpqa_questions_type.'_columns', 'wpqa_question_columns');
if (!function_exists('wpqa_question_custom_columns')) :
	function wpqa_question_custom_columns($column) {
		global $post;
		switch ( $column ) {
			case 'type' :
				$question_poll = get_post_meta($post->ID,'question_poll',true);
				if ($question_poll == "on") {
					echo '<a href="'.admin_url('edit.php?post_type='.$post->post_type.'&types=poll').'">'.esc_html__("Poll","wpqa").'</a>';
				}else {
					echo '<a href="'.admin_url('edit.php?post_type='.$post->post_type.'&types=question').'">'.esc_html_x("Question","Admin type link","wpqa").'</a>';
				}
			break;
			case 'author-q' :
				$display_name = get_the_author_meta('display_name',$post->post_author);
				if ($post->post_author > 0) {
					echo '<a href="'.admin_url('edit.php?post_type='.$post->post_type.'&author='.$post->post_author).'">'.$display_name.'</a>';
				}else {
					$anonymously_question = get_post_meta($post->ID,'anonymously_question',true);
					$anonymously_user = get_post_meta($post->ID,'anonymously_user',true);
					if (($anonymously_question == "on" || $anonymously_question == 1) && $anonymously_user != "") {
						$display_name_anonymous = get_the_author_meta('display_name',$anonymously_user);
						echo esc_html__("Anonymous","wpqa")." - <a href='".wpqa_profile_url($anonymously_user)."' target='_blank'>".$display_name_anonymous."</a>";
					}else {
						$question_username = get_post_meta($post->ID,'question_username',true);
						$question_username = ($question_username != ""?$question_username:esc_html__("Anonymous","wpqa"));
						echo esc_html($question_username);
					}
				}
				$user_id = get_post_meta($post->ID,'user_id',true);
				if ($user_id != "") {
					$display_name = get_the_author_meta('display_name',$user_id);
					echo "<br>".esc_html__("Asked to","wpqa")." <a href='".wpqa_profile_url($user_id)."' target='_blank'>".$display_name."</a>";
				}
				$save_ip_address = wpqa_options("save_ip_address");
				if ($save_ip_address == "on") {
					$get_ip_address = get_post_meta($post->ID,'wpqa_ip_address',true);
					if ($get_ip_address != "") {
						echo "<br>".$get_ip_address;
					}
				}
			break;
			case 'category' :
				$question_category = wp_get_post_terms($post->ID,wpqa_question_categories,array("fields" => "all"));
				if (isset($question_category[0])) {?>
					<a href="<?php echo admin_url('edit.php?'.wpqa_question_categories.'='.$question_category[0]->slug.'&post_type='.wpqa_questions_type);?>"><?php echo esc_html($question_category[0]->name)?></a>
				<?php }else {
					echo '<span aria-hidden="true">-</span><span class="screen-reader-text">'.esc_html__("No category","wpqa").'</span>';
				}
			break;
			case 'tag' :
				$terms = wp_get_object_terms($post->ID,wpqa_question_tags);
				if ($terms) :
					$terms_array = array();
					foreach ($terms as $term) :
						$terms_array[] = '<a href="'.admin_url('edit.php?'.wpqa_question_tags.'='.$term->slug.'&post_type='.wpqa_questions_type).'">'.$term->name.'</a>';
					endforeach;
					echo implode(', ',$terms_array);
				else:
					echo '<span aria-hidden="true">-</span><span class="screen-reader-text">'.esc_html__("No tags","wpqa").'</span>';
				endif;
			break;
			case 'view' :
				$post_stats = wpqa_get_post_stats($post->ID);
				echo wpqa_count_number($post_stats)." "._n("View","Views",$post_stats,"wpqa");
			break;
		}
	}
endif;
add_action('manage_'.wpqa_questions_type.'_posts_custom_column','wpqa_question_custom_columns',2);
/* Message update */
if (!function_exists('wpqa_question_updated_messages')) :
	function wpqa_question_updated_messages($messages) {
		global $post,$post_ID;
		$get_permalink = get_permalink($post_ID);
		$messages[wpqa_questions_type] = array(
			0 => '',
			1 => sprintf(esc_html__('Updated. %1$s View question %2$s','wpqa'),'<a href="'.esc_url($get_permalink).'">','</a>'),
		);
		$messages[wpqa_asked_questions_type] = array(
			0 => '',
			1 => sprintf(esc_html__('Updated. %1$s View question %2$s','wpqa'),'<a href="'.esc_url($get_permalink).'">','</a>'),
		);
		return $messages;
	}
endif;
add_filter('post_updated_messages','wpqa_question_updated_messages');
/* Questions status */
add_filter( "views_edit-".wpqa_questions_type, "wpqa_questions_status" );
if (!function_exists('wpqa_questions_status')) :
	function wpqa_questions_status($status) {
		$get_status = (isset($_GET['types'])?esc_html($_GET['types']):'');
		
		$query_poll_count = wpqa_meta_count("question_poll","on");
		$wp_count_questions = wp_count_posts(wpqa_questions_type);
		$count_questions = 0;
		foreach ($wp_count_questions as $key => $value) {
			$count_questions += $value;
		}
		$query_question_count = $count_questions-$query_poll_count;

		$sticky_questions = get_option('sticky_questions');
		$query_sticky_count = (int)(is_array($sticky_questions) && !empty($sticky_questions)?count($sticky_questions):0);

		$wp_count_asked_questions = wp_count_posts(wpqa_asked_questions_type);
		$count_asked_questions = 0;
		foreach ($wp_count_asked_questions as $key => $value) {
			$count_asked_questions += $value;
		}
		
		return array_merge( $status, array(
			'question' => '<a href="'.admin_url('edit.php?post_type='.wpqa_questions_type.'&types=question').'"'.($get_status == "question"?' class="current"':'').'>'.esc_html_x('Question','Admin type link','wpqa').' ('.$query_question_count.')</a>',
			'poll' => '<a href="'.admin_url('edit.php?post_type='.wpqa_questions_type.'&types=poll').'"'.($get_status == "poll"?' class="current"':'').'>'.esc_html__('Poll','wpqa').' ('.$query_poll_count.')</a>',
			'sticky' => '<a href="'.admin_url('edit.php?post_type='.wpqa_questions_type.'&types=sticky').'"'.($get_status == "sticky"?' class="current"':'').'>'.esc_html__('Sticky','wpqa').' ('.$query_sticky_count.')</a>',
			'asked' => '<a href="'.admin_url('edit.php?post_type='.wpqa_asked_questions_type).'"'.($get_status == "asked"?' class="current"':'').'>'.esc_html__('Asked Questions','wpqa').' ('.$count_asked_questions.')</a>',
		));
	}
endif;
add_action('current_screen','wpqa_questions_exclude',10,2);
if (!function_exists('wpqa_questions_exclude')) :
	function wpqa_questions_exclude($screen) {
		if ($screen->id != 'edit-'.wpqa_questions_type)
			return;
		$get_status = (isset($_GET['types'])?esc_html($_GET['types']):'');
		add_action('pre_get_posts','wpqa_list_questions');
	}
endif;
function wpqa_list_questions($query) {
	global $post_type,$pagenow;
	if ($pagenow == 'edit.php' && $post_type == wpqa_questions_type) {
		$get_status = (isset($_GET['types'])?esc_html($_GET['types']):'');
		if ($get_status == "poll") {
			$query->query_vars['meta_key'] = "question_poll";
			$query->query_vars['meta_value'] = "on";
			$query->query_vars['post_type'] = wpqa_questions_type;
		}else if ($get_status == "sticky") {
			$query->query_vars['meta_key'] = "sticky";
			$query->query_vars['meta_value'] = 1;
			$query->query_vars['post_type'] = wpqa_questions_type;
		}else if ($get_status == "question") {
			$query->query_vars['meta_key'] = "question_poll";
			$query->query_vars['meta_value'] = 2;
			$query->query_vars['post_type'] = wpqa_questions_type;
		}else if (is_post_type_archive(wpqa_questions_type) && $query->is_main_query()) {
			//$query->query_vars['post_type'] = array(wpqa_questions_type,wpqa_asked_questions_type);
			//$query->set('post_type',array(wpqa_questions_type,wpqa_asked_questions_type));
			$query->query_vars['post_type'] = wpqa_questions_type;
		}
	}
}
/* Add meta boxes */
add_action('add_meta_boxes','wpqa_builder_meta_boxes');
if (!function_exists('wpqa_builder_meta_boxes')) :
	function wpqa_builder_meta_boxes($post_type) {
		if (in_array($post_type,array('post',wpqa_questions_type,wpqa_asked_questions_type,'group'))) {
			if ($post_type == "post") {
				$delete = esc_html__('Delete post','wpqa');
			}else if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
				$delete = esc_html__('Delete question','wpqa');
			}else if ($post_type == wpqa_knowledgebase_type) {
				$delete = esc_html__('Delete knowledgebase','wpqa');
			}else if ($post_type == "group") {
				$delete = esc_html__('Delete group','wpqa');
			}
			add_meta_box ('wpqa_delete_post_meta',$delete,'wpqa_delete_post_meta',$post_type,'side');
		}
	}
endif;
/* Delete post questions */
if (!function_exists('wpqa_delete_post_meta')) :
	function wpqa_delete_post_meta() {
		global $post;?>
		<div class="minor-publishing">
			<div class="custom-meta-field">
				<div class="custom-meta-label">
					<label for="wpqa_delete_reason"><?php esc_html_e('Reason if you need to remove it.',"wpqa")?></label>
				</div>
				<div class="custom-meta-input wpqa_checkbox_input">
					<input type="text" class="custom-meta-input" name="wpqa_delete_reason" id="wpqa_delete_reason" value="<?php echo esc_attr(get_post_meta($post->ID,"wpqa_delete_reason",true));?>">
				</div>
				<div class="clear"></div><br>
				<div class="submitbox"><a href="#" class="submitdelete delete-question-post" data-div-id="wpqa_delete_reason" data-nonce="<?php echo wp_create_nonce("wpqa_delete_nonce")?>" data-id="<?php echo esc_attr($post->ID);?>" data-action="wpqa_delete_question_post" data-location="<?php echo esc_url(($post->post_type == wpqa_questions_type || $post->post_type == wpqa_asked_questions_type || $post->post_type == wpqa_knowledgebase_type?admin_url('edit.php?post_type='.$post->post_type):admin_url('edit.php')))?>"><?php esc_html_e('Delete?',"wpqa")?></a></div>
			</div>
		</div>
		<?php
	}
endif;
/* Add sticky question widget */
if (!function_exists('wpqa_sticky_add_meta_box')) :
	function wpqa_sticky_add_meta_box() {
		if (!current_user_can('edit_others_posts'))
			return;
		add_meta_box('wpqa_sticky_question',esc_html__('Sticky','wpqa'),'wpqa_sticky_question',wpqa_questions_type,'side','high');
	}
endif;
/* Sticky question */
if (!function_exists('wpqa_sticky_question')) :
	function wpqa_sticky_question() {?>
		<input name="sticky_question" type="hidden" value="sticky">
		<label class="switch" for="sticky-question">
			<input id="sticky-question" name="sticky" type="checkbox" value="sticky" <?php checked( is_sticky() ); ?>>
			<label for="sticky-question" data-on="<?php esc_html_e("ON","wpqa")?>" data-off="<?php esc_html_e("OFF","wpqa")?>"></label>
		</label>
		<label for="sticky-question" class="selectit"><?php esc_html_e("Stick this question","wpqa") ?></label>
		<?php
	}
endif;
add_action('admin_init','wpqa_sticky_add_meta_box');?>