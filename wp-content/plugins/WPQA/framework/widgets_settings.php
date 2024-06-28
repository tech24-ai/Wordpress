<?php

/* @author    2codeThemes
*  @package   WPQA/framework
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Show sidebars */
add_action('wpqa_init','wpqa_wp_registered_sidebars');
function wpqa_wp_registered_sidebars() {
	$wp_registered_sidebars = array();
	foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) {
		$wp_registered_sidebars[$sidebar['id']] = $sidebar['name'];
	}
	return $wp_registered_sidebars;
}
add_action('wpqa_init','wpqa_registered_sidebars');
function wpqa_registered_sidebars() {
	$wpqa_registered_sidebars = get_option("wpqa_registered_sidebars");
	$wp_registered_sidebars = wpqa_wp_registered_sidebars();
	if (is_array($wpqa_registered_sidebars) && !empty($wpqa_registered_sidebars) && is_array($wp_registered_sidebars) && !empty($wp_registered_sidebars)) {
		$wp_registered_sidebars = array_unique(array_merge($wpqa_registered_sidebars,$wp_registered_sidebars));
	}
	$new_sidebars = array('default' => 'Default');
	if (is_array($wp_registered_sidebars) && !empty($wp_registered_sidebars)) {
		foreach ($wp_registered_sidebars as $key => $value) {
			$new_sidebars[$key] = $value;
		}
	}
	return $new_sidebars;
}
/* Show widget fields */
add_action('wpqa_init','wpqa_options_widgets');
function wpqa_options_widgets() {
	$options = wpqa_admin_widgets();
	foreach ($options as $widget_id => $fields) {
		$fields = apply_filters($widget_id.'_widget_fields_args',$fields);
		wpqa_widget_options(array (
			'id'     => $widget_id,
			'fields' => $fields
		));
	}
}
/* Widget fields */
function wpqa_widget_options($args) {
	add_action('in_widget_form','wpqa_widget_fields',10,3);
	add_filter('widget_update_callback','wpqa_save_widget',10,4);
}
/* Add extra form fields to the widget */
function wpqa_widget_fields($widget,$return,$instance) {
	$options = wpqa_admin_widgets();
	if (array_key_exists($widget->id_base,$options)) {?>
		<div class="framework_widgets framework-main">
			<?php wpqa_options_fields($instance,"","widgets",$widget,$options[$widget->id_base]);?>
		</div>
	<?php }
}
/* Save widget fields on widget save */
function wpqa_save_widget($instance,$new_instance,$old_instance,$widget) {
	$options = wpqa_admin_widgets();
	if (array_key_exists($widget->id_base,$options)) {
		foreach ($options[$widget->id_base] as $key => $value) {
			if (isset($value["id"]) && isset($value["type"]) && $value["type"] == "checkbox" && !isset($new_instance[$value["id"]])) {
				$new_instance[$value["id"]] = 0;
			}
		}
		
		if (array_key_exists("twitter-widget",$options) && $widget->id_base == "twitter-widget") {
			delete_transient('wpqa_twitter_widget_'.$widget->id.$instance['accounts']);
		}else if (array_key_exists("widget_counter",$options) && $widget->id_base == "widget_counter") {
			delete_transient('wpqa_facebook_followers');
			delete_transient('wpqa_twitter_followers');
			delete_transient('wpqa_vimeo_followers');
			delete_transient('wpqa_vimeo_page_url');
			delete_transient('wpqa_dribbble_followers');
			delete_transient('wpqa_dribbble_page_url');
			delete_transient('wpqa_youtube_followers');
			delete_transient('wpqa_pinterest_followers');
			delete_transient('wpqa_instagram_followers');
			delete_transient('wpqa_instagram_page_url');
			delete_transient('wpqa_soundcloud_followers');
			delete_transient('wpqa_soundcloud_page_url');
			delete_transient('wpqa_behance_followers');
			delete_transient('wpqa_behance_page_url');
			delete_transient('wpqa_github_followers');
			delete_transient('wpqa_github_page_url');
		}
		return $new_instance;
	}
	return $instance;
}?>