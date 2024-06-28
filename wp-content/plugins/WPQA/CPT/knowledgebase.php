<?php

/* @author    2codeThemes
*  @package   WPQA/CPT
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Make the knowledgebases with number */
add_filter('post_type_link','wpqa_knowledgebase_number_slug',10,2);
function wpqa_knowledgebase_number_slug($post_link,$post) {
	$knowledgebase_slug_numbers = wpqa_options("knowledgebase_slug_numbers");
	if ($knowledgebase_slug_numbers == "on") {
		if (wpqa_knowledgebase_type != $post->post_type || 'publish' != $post->post_status) {
			return $post_link;
		}
		$knowledgebase_slug = wpqa_options('knowledgebase_slug');
		$knowledgebase_slug = ($knowledgebase_slug != ""?$knowledgebase_slug:wpqa_knowledgebase_type);
		$post_link = str_replace('/'.$knowledgebase_slug.'/'.$post->post_name,'/'.$knowledgebase_slug.'/'.$post->ID,$post_link);
	}
	return $post_link;
}
/* Remove knowledgebase slug */
add_filter('post_type_link','wpqa_knowledgebase_remove_slug',10,2);
function wpqa_knowledgebase_remove_slug($post_link,$post) {
	$remove_knowledgebase_slug = wpqa_options("remove_knowledgebase_slug");
	if ($remove_knowledgebase_slug == "on") {
		if (wpqa_knowledgebase_type != $post->post_type || 'publish' != $post->post_status) {
			return $post_link;
		}
		$knowledgebase_slug = wpqa_options('knowledgebase_slug');
		$knowledgebase_slug = ($knowledgebase_slug != ""?$knowledgebase_slug:wpqa_knowledgebase_type);
		$post_link = str_replace('/'.$knowledgebase_slug.'/','/',$post_link);
	}
	return $post_link;
}
/* Knowledgebase post type */
if (!function_exists('wpqa_knowledgebase_post_type')) :
	function wpqa_knowledgebase_post_type() {
		$activate_knowledgebase = apply_filters("wpqa_activate_knowledgebase",false);
		if ($activate_knowledgebase == true) {
			$remove_knowledgebase_slug   = wpqa_options("remove_knowledgebase_slug");
			
			$archive_knowledgebase_slug  = wpqa_options('archive_knowledgebase_slug');
			$archive_knowledgebase_slug  = ($archive_knowledgebase_slug != ""?$archive_knowledgebase_slug:"knowledgebases");
			
			$knowledgebase_slug          = wpqa_options('knowledgebase_slug');
			$knowledgebase_slug          = ($knowledgebase_slug != ""?$knowledgebase_slug:wpqa_knowledgebase_type);
			
			$category_knowledgebase_slug = wpqa_options('category_knowledgebase_slug');
			$category_knowledgebase_slug = ($category_knowledgebase_slug != ""?$category_knowledgebase_slug:wpqa_knowledgebase_categories);
			
			$tag_knowledgebase_slug      = wpqa_options('tag_knowledgebase_slug');
			$tag_knowledgebase_slug      = ($tag_knowledgebase_slug != ""?$tag_knowledgebase_slug:wpqa_knowledgebase_tags);
		   
			register_post_type(wpqa_knowledgebase_type,
				array(
					'label' => esc_html__('Knowledgebases','wpqa'),
					'labels' => array(
						'name'               => esc_html__('Knowledgebases','wpqa'),
						'singular_name'      => esc_html__('Knowledgebases','wpqa'),
						'menu_name'          => esc_html__('Knowledgebases','wpqa'),
						'name_admin_bar'     => esc_html__('Knowledgebase','wpqa'),
						'add_new'            => esc_html__('Add New','wpqa'),
						'add_new_item'       => esc_html__('Add New Knowledgebase','wpqa'),
						'new_item'           => esc_html__('New Knowledgebase','wpqa'),
						'edit_item'          => esc_html__('Edit Knowledgebase','wpqa'),
						'view_item'          => esc_html__('View Knowledgebase','wpqa'),
						'view_items'         => esc_html__('View Knowledgebases','wpqa'),
						'all_items'          => esc_html__('All Knowledgebases','wpqa'),
						'search_items'       => esc_html__('Search Knowledgebases','wpqa'),
						'parent_item_colon'  => esc_html__('Parent Knowledgebase:','wpqa'),
						'not_found'          => esc_html__('No Knowledgebases Found.','wpqa'),
						'not_found_in_trash' => esc_html__('No Knowledgebases Found in Trash.','wpqa'),
					),
					'description'         => '',
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'post',
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false,
					'rewrite'             => array('slug' => apply_filters("wpqa_knowledgebase_slug",($remove_knowledgebase_slug == "on"?false:$knowledgebase_slug)),'hierarchical' => true,'with_front' => false),
					'query_var'           => true,
					'show_in_rest'        => true,
					'has_archive'         => apply_filters("wpqa_archive_knowledgebase",$archive_knowledgebase_slug),
					'menu_position'       => 5,
					'menu_icon'           => "dashicons-buddicons-forums",
					'supports'            => array('title','thumbnail','editor','author'),
					'taxonomies'          => array(wpqa_knowledgebase_categories,wpqa_knowledgebase_tags),
				)
			);

			$knowledgebase_slug_numbers = wpqa_options("knowledgebase_slug_numbers");
			if ($knowledgebase_slug_numbers == "on") {
				$removed = ($remove_knowledgebase_slug == "on"?'':$knowledgebase_slug.'/');
				add_rewrite_rule($removed.'([0-9]+)?$','index.php?post_type='.$knowledgebase_slug.'&p=$matches[1]','top');
			}
			
			$labels = array(
				'name'              => esc_html__('KB Categories','wpqa'),
				'singular_name'     => esc_html__('KB Categories','wpqa'),
				'search_items'      => esc_html__('Search Categories','wpqa'),
				'all_items'         => esc_html__('All Categories','wpqa'),
				'parent_item'       => esc_html__('KB Categories','wpqa'),
				'parent_item_colon' => esc_html__('KB Categories','wpqa'),
				'edit_item'         => esc_html__('Edit Category','wpqa'),
				'update_item'       => esc_html__('Edit','wpqa'),
				'add_new_item'      => esc_html__('Add New Category','wpqa'),
				'new_item_name'     => esc_html__('Add New Category','wpqa')
			);
			
			register_taxonomy(wpqa_knowledgebase_categories,wpqa_knowledgebase_type,array(
				'hierarchical' => true,
				'labels'       => $labels,
				'show_ui'      => true,
				'query_var'    => true,
				'show_in_rest' => true,
				'rewrite'      => array('slug' => $category_knowledgebase_slug,'hierarchical' => true,'with_front' => false),
			));
			
			register_taxonomy( wpqa_knowledgebase_tags,
				array(wpqa_knowledgebase_type),
				array(
					'hierarchical' => false,
					'labels' => array(
						'name'              => esc_html__('KB Tags','wpqa'),
						'singular_name'     => esc_html__('KB Tags','wpqa'),
						'search_items'      => esc_html__('Search Tags','wpqa'),
						'all_items'         => esc_html__('All Tags','wpqa'),
						'parent_item'       => esc_html__('KB Tags','wpqa'),
						'parent_item_colon' => esc_html__('KB Tags','wpqa'),
						'edit_item'         => esc_html__('Edit Tag','wpqa'),
						'update_item'       => esc_html__('Edit','wpqa'),
						'add_new_item'      => esc_html__('Add New Tag','wpqa'),
						'new_item_name'     => esc_html__('Add New Tag','wpqa')
					),
					'show_ui'      => true,
					'query_var'    => true,
					'show_in_rest' => true,
					'rewrite'      => array('slug' => $tag_knowledgebase_slug,'hierarchical' => true,'with_front' => false),
				)
			);
		}
	}
endif;
add_action('wpqa_init','wpqa_knowledgebase_post_type',0);
/* Admin columns for post types */
if (!function_exists('wpqa_knowledgebase_columns')) :
	function wpqa_knowledgebase_columns($columns) {
		$columns["cb"]       = "<input type=\"checkbox\">";
		$columns["title"]    = esc_html__("Title","wpqa");
		$columns["author-k"] = esc_html__("Author","wpqa");
		$columns["category"] = esc_html__("Category","wpqa");
		$columns["tag"]      = esc_html__("Tags","wpqa");
		$columns["date"]     = esc_html__("Date","wpqa");
		$active_post_stats = wpqa_options("active_post_stats");
		$view = ($active_post_stats == "on"?array('view' => '<span class="dashicons dashicons-visibility dashicons-before"></span>'):array());
		if (isset($columns["author"])) {
			unset($columns["author"]);
		}
		return array_merge($columns,$view);
	}
endif;
add_filter('manage_edit-'.wpqa_knowledgebase_type.'_columns', 'wpqa_knowledgebase_columns');
if (!function_exists('wpqa_knowledgebase_custom_columns')) :
	function wpqa_knowledgebase_custom_columns($column) {
		global $post;
		switch ( $column ) {
			case 'author-k' :
				$display_name = get_the_author_meta('display_name',$post->post_author);
				if ($post->post_author > 0) {
					echo '<a href="'.admin_url('edit.php?post_type='.$post->post_type.'&author='.$post->post_author).'">'.$display_name.'</a>';
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
				$knowledgebase_category = wp_get_post_terms($post->ID,wpqa_knowledgebase_categories,array("fields" => "all"));
				if (isset($knowledgebase_category[0])) {?>
					<a href="<?php echo admin_url('edit.php?'.wpqa_knowledgebase_categories.'='.$knowledgebase_category[0]->slug.'&post_type='.wpqa_knowledgebase_type);?>"><?php echo esc_html($knowledgebase_category[0]->name)?></a>
				<?php }else {
					echo '<span aria-hidden="true">-</span><span class="screen-reader-text">'.esc_html__("No category","wpqa").'</span>';
				}
			break;
			case 'tag' :
				$terms = wp_get_object_terms($post->ID,wpqa_knowledgebase_tags);
				if ($terms) :
					$terms_array = array();
					foreach ($terms as $term) :
						$terms_array[] = '<a href="'.admin_url('edit.php?'.wpqa_knowledgebase_tags.'='.$term->slug.'&post_type='.wpqa_knowledgebase_type).'">'.$term->name.'</a>';
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
add_action('manage_'.wpqa_knowledgebase_type.'_posts_custom_column','wpqa_knowledgebase_custom_columns',2);
/* Message update */
if (!function_exists('wpqa_knowledgebase_updated_messages')) :
	function wpqa_knowledgebase_updated_messages($messages) {
		global $post,$post_ID;
		$get_permalink = get_permalink($post_ID);
		$messages[wpqa_knowledgebase_type] = array(
			0 => '',
			1 => sprintf(esc_html__('Updated. %1$s View knowledgebase %2$s','wpqa'),'<a href="'.esc_url($get_permalink).'">','</a>'),
		);
		return $messages;
	}
endif;
add_filter('post_updated_messages','wpqa_knowledgebase_updated_messages');
/* Knowledgebases status */
add_filter( "views_edit-".wpqa_knowledgebase_type, "wpqa_knowledgebases_status" );
if (!function_exists('wpqa_knowledgebases_status')) :
	function wpqa_knowledgebases_status($status) {
		$get_status = (isset($_GET['types'])?esc_html($_GET['types']):'');
		
		$wp_count_knowledgebases = wp_count_posts(wpqa_knowledgebase_type);
		$count_knowledgebases = 0;
		foreach ($wp_count_knowledgebases as $key => $value) {
			$count_knowledgebases += $value;
		}
		$query_knowledgebase_count = $count_knowledgebases;

		$sticky_knowledgebases = get_option('sticky_knowledgebases');
		$query_sticky_count = (int)(is_array($sticky_knowledgebases) && !empty($sticky_knowledgebases)?count($sticky_knowledgebases):0);
		
		return array_merge( $status, array(
			'knowledgebase' => '<a href="'.admin_url('edit.php?post_type='.wpqa_knowledgebase_type.'&types=knowledgebase').'"'.($get_status == "knowledgebase"?' class="current"':'').'>'.esc_html__('Knowledgebase','wpqa').' ('.$query_knowledgebase_count.')</a>',
			'sticky' => '<a href="'.admin_url('edit.php?post_type='.wpqa_knowledgebase_type.'&types=sticky').'"'.($get_status == "sticky"?' class="current"':'').'>'.esc_html__('Sticky','wpqa').' ('.$query_sticky_count.')</a>',
		));
	}
endif;
add_action('current_screen','wpqa_knowledgebases_exclude',10,2);
if (!function_exists('wpqa_knowledgebases_exclude')) :
	function wpqa_knowledgebases_exclude($screen) {
		if ($screen->id != 'edit-'.wpqa_knowledgebase_type)
			return;
		$get_status = (isset($_GET['types'])?esc_html($_GET['types']):'');
		add_action('pre_get_posts','wpqa_list_knowledgebases');
	}
endif;
function wpqa_list_knowledgebases($query) {
	global $post_type,$pagenow;
	if ($pagenow == 'edit.php' && $post_type == wpqa_knowledgebase_type) {
		$get_status = (isset($_GET['types'])?esc_html($_GET['types']):'');
		if (is_post_type_archive(wpqa_knowledgebase_type) && $query->is_main_query()) {
			$query->query_vars['post_type'] = wpqa_knowledgebase_type;
		}else if ($get_status == "sticky") {
			$query->query_vars['meta_key'] = "sticky";
			$query->query_vars['meta_value'] = 1;
			$query->query_vars['post_type'] = wpqa_knowledgebase_type;
		}
	}
}
/* Add sticky knowledgebase widget */
if (!function_exists('wpqa_sticky_add_meta_box')) :
	function wpqa_sticky_add_meta_box() {
		if (!current_user_can('edit_others_posts'))
			return;
		add_meta_box('wpqa_sticky_knowledgebase',esc_html__('Sticky','wpqa'),'wpqa_sticky_knowledgebase',wpqa_knowledgebase_type,'side','high');
	}
endif;
/* Sticky knowledgebase */
if (!function_exists('wpqa_sticky_knowledgebase')) :
	function wpqa_sticky_knowledgebase() {?>
		<input name="sticky_knowledgebase" type="hidden" value="sticky">
		<label class="switch" for="sticky-knowledgebase">
			<input id="sticky-knowledgebase" name="sticky" type="checkbox" value="sticky" <?php checked( is_sticky() ); ?>>
			<label for="sticky-knowledgebase" data-on="<?php esc_html_e("ON","wpqa")?>" data-off="<?php esc_html_e("OFF","wpqa")?>"></label>
		</label>
		<label for="sticky-knowledgebase" class="selectit"><?php esc_html_e("Stick this knowledgebase","wpqa") ?></label>
		<?php
	}
endif;
add_action('admin_init','wpqa_sticky_add_meta_box');?>