<?php

/* @author    2codeThemes
*  @package   WPQA/options
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function wpqa_admin_setting_mobile($default = "") {
	if (is_admin() || $default == "default") {
		$options = array();
		
		// Pull all the pages into an array
		$options_pages = array();
		$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
		$options_pages[''] = 'Select a page:';
		foreach ($options_pages_obj as $page) {
			$options_pages[$page->ID] = $page->post_title;
		}

		$options = apply_filters(wpqa_prefix_theme."_options_after_general_setting",$options,$options_pages);

		return $options;
	}
}?>