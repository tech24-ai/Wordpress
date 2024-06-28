<?php

/* @author    2codeThemes
*  @package   WPQA/CPT
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Checkouts post type */
function wpqa_checkout_post_types_init() {
	$payment_available = wpqa_payment_available();
	if ($payment_available == true) {
		register_post_type( 'checkout',
			array(
				'label' => esc_html__('Checkouts','wpqa'),
				'labels' => array(
					'name'               => esc_html__('Checkouts','wpqa'),
					'singular_name'      => esc_html__('Checkouts','wpqa'),
					'menu_name'          => esc_html__('Checkouts','wpqa'),
					'name_admin_bar'     => esc_html_x('Checkout','name_admin_bar','wpqa'),
					'add_new'            => esc_html__('Add New','wpqa'),
					'add_new_item'       => esc_html__('Add New Checkout','wpqa'),
					'new_item'           => esc_html__('New Checkout','wpqa'),
					'edit_item'          => esc_html__('Edit Checkout','wpqa'),
					'view_item'          => esc_html__('View Checkout','wpqa'),
					'view_items'         => esc_html__('View Checkouts','wpqa'),
					'all_items'          => esc_html__('All Checkouts','wpqa'),
					'search_items'       => esc_html__('Search Checkouts','wpqa'),
					'parent_item_colon'  => esc_html__('Parent Checkout:','wpqa'),
					'not_found'          => esc_html__('No Checkouts Found.','wpqa'),
					'not_found_in_trash' => esc_html__('No Checkouts Found in Trash.','wpqa'),
				),
				'description'         => '',
				'public'              => false,
				'show_ui'             => true,
				'capability_type'     => 'post',
				'map_meta_cap'        => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => false,
				'hierarchical'        => false,
				'query_var'           => false,
				'show_in_rest'        => false,
				'has_archive'         => false,
				'menu_position'       => 20,
				'menu_icon'           => "dashicons-tickets-alt",
				'supports'            => array('title'),
			)
		);
	}
}
add_action( 'wpqa_init', 'wpqa_checkout_post_types_init', 2 );
/* Admin columns for post types */
function wpqa_checkout_columns($columns) {
	$columns["cb"]       = "<input type=\"checkbox\">";
	$columns["title"]    = esc_html__("Title","wpqa");
	$columns["author_p"] = esc_html__("Author","wpqa");
	$columns["date_p"]   = esc_html__("Date","wpqa");
	if (isset($columns["author"])) {
		unset($columns["author"]);
	}
	return $columns;
}
add_filter('manage_edit-checkout_columns','wpqa_checkout_columns');
function wpqa_checkout_custom_columns($column) {
	global $post;
	switch ( $column ) {
		case 'author_p' :
			$user_name = get_the_author_meta('display_name',$post->post_author);
			if ($user_name != "") {
				echo '<a target="_blank" href="'.wpqa_profile_url((int)$post->post_author).'"><strong>'.$user_name.'</strong></a><a class="tooltip_s" data-title="'.esc_html__("View checkouts","wpqa").'" href="'.admin_url('edit.php?post_type=checkout&author='.$post->post_author).'"></a>';
			}else {
				esc_html_e("Deleted user","wpqa");
			}
		break;
		case 'date_p' :
			$date_format = wpqa_options("date_format");
			$date_format = ($date_format?$date_format:get_option("date_format"));
			$human_time_diff = human_time_diff(get_the_time('U'), current_time('timestamp'));
			echo ($human_time_diff." ".esc_html__("ago","wpqa")." - ".esc_html(get_the_time($date_format)));
		break;
	}
}
add_action('manage_checkout_posts_custom_column','wpqa_checkout_custom_columns',2);
function wpqa_checkout_primary_column($default,$screen) {
	if ('edit-checkout' === $screen) {
		$default = 'content_p';
	}
	return $default;
}
add_filter('list_table_primary_column','wpqa_checkout_primary_column',10,2);
add_filter('manage_edit-checkout_sortable_columns','wpqa_checkout_sortable_columns');
function wpqa_checkout_sortable_columns($defaults) {
	$defaults['date_p'] = 'date';
	return $defaults;
}
/* Checkout details */
add_filter('bulk_actions-edit-checkout','wpqa_bulk_actions_checkout');
function wpqa_bulk_actions_checkout($actions) {
	unset($actions['edit']);
	return $actions;
}
add_filter('bulk_post_updated_messages','wpqa_bulk_updated_messages_checkout',1,2);
function wpqa_bulk_updated_messages_checkout($bulk_messages,$bulk_counts) {
	if (get_current_screen()->post_type == "checkout") {
		$bulk_messages['post'] = array(
			'deleted' => _n('%s checkout permanently deleted.','%s checkouts permanently deleted.',$bulk_counts['deleted'],'wpqa'),
			'trashed' => _n('%s checkout trashed.','%s checkouts trashed.',$bulk_counts['trashed'],'wpqa'),
		);
	}
	return $bulk_messages;
}
add_filter('post_row_actions','wpqa_row_actions_checkout',1,2);
function wpqa_row_actions_checkout($actions,$post) {
	if ($post->post_type == "checkout") {
		unset($actions['trash']);
		unset($actions['view']);
		unset($actions['edit']);
		$actions['inline hide-if-no-js'] = "";
	}
	return $actions;
}
function wpqa_checkout_filter() {
	global $post_type;
	if ($post_type == 'checkout') {
		$from = (isset($_GET['date-from']) && $_GET['date-from'])?$_GET['date-from'] :'';
		$to = (isset($_GET['date-to']) && $_GET['date-to'])?$_GET['date-to']:'';
		$data_js = " data-js='".json_encode(array("changeMonth" => true,"changeYear" => true,"yearRange" => "2018:+00","dateFormat" => "yy-mm-dd"))."'";

		echo '<span class="site-form-date"><input class="site-date" type="text" name="date-from" placeholder="'.esc_html__("Date From","wpqa").'" value="'.esc_attr($from).'" '.$data_js.'></span>
		<span class="site-form-date"><input class="site-date" type="text" name="date-to" placeholder="'.esc_html__("Date To","wpqa").'" value="'.esc_attr($to).'" '.$data_js.'></span>';
	}
}
add_action('restrict_manage_posts','wpqa_checkout_filter');
function wpqa_checkout_posts_query($query) {
	global $post_type,$pagenow;
	if ($pagenow == 'edit.php' && $post_type == 'checkout') {
		if (!empty($_GET['date-from']) && !empty($_GET['date-to'])) {
			$query->query_vars['date_query'][] = array(
				'after'     => sanitize_text_field($_GET['date-from']),
				'before'    => sanitize_text_field($_GET['date-to']),
				'inclusive' => true,
				'column'    => 'post_date'
			);
		}
		if (!empty($_GET['date-from']) && empty($_GET['date-to'])) {
			$today = sanitize_text_field($_GET['date-from']);
			$today = explode("-",$today);
			$query->query_vars['date_query'] = array(
				'year'  => $today[0],
				'month' => $today[1],
				'day'   => $today[2],
			);
		}
		if (empty($_GET['date-from']) && !empty($_GET['date-to'])) {
			$today = sanitize_text_field($_GET['date-to']);
			$today = explode("-",$today);
			$query->query_vars['date_query'] = array(
				'year'  => $today[0],
				'month' => $today[1],
				'day'   => $today[2],
			);
		}
		$orderby = $query->get('orderby');
		if ($orderby == 'date_p') {
			$query->query_vars('orderby','date');
		}
	}
}
add_action('pre_get_posts','wpqa_checkout_posts_query');
function wpqa_months_dropdown_checkout($return,$post_type) {
	if ($post_type == "checkout") {
		$return = true;
	}
	return $return;
}
add_filter("disable_months_dropdown","wpqa_months_dropdown_checkout",1,2);
/* Remove filter */
function wpqa_manage_checkout_tablenav($which) {
	if ($which == "top") {
		global $post_type,$pagenow;
		if ($pagenow == 'edit.php' && $post_type == 'checkout') {
			$date_from = (isset($_GET['date-from'])?esc_html($_GET['date-from']):'');
			$date_to = (isset($_GET['date-to'])?esc_html($_GET['date-to']):'');
			if ($date_from != "" || $date_to != "") {
				echo '<a class="button" href="'.admin_url('edit.php?post_type=checkout').'">'.esc_html__("Remove filters","wpqa").'</a>';
			}
		}
	}
}
add_filter("manage_posts_extra_tablenav","wpqa_manage_checkout_tablenav");?>