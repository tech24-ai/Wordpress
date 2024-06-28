<?php

/* @author    2codeThemes
*  @package   WPQA/framework
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Don't load if wpqa_admin_init is already defined
if (!function_exists('wpqa_admin_init')) :
	function wpqa_admin_init() {
		if (!is_home() && !is_front_page() && !is_page() && !is_single()) {
			// Loads the required Options Framework classes.
			require_once plugin_dir_path(__FILE__)."fields.php";
			require_once plugin_dir_path(__FILE__)."admin_options.php";
			require_once plugin_dir_path(__FILE__)."options_sanitization.php";
			require_once plugin_dir_path(__FILE__)."option.php";
		}
		if (is_admin()) {
			// Instantiate the main class.
			$wpqa_admin = new wpqa_admin;
			// Instantiate the options page.
			$wpqa_admin_options = new wpqa_admin_options;
			$wpqa_admin_options->init("options");
		}
	}
	add_action('wpqa_init','wpqa_admin_init',20);
	if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'],'admin.php?page=options') === false) {
		add_action('current_screen','wpqa_admin_init');
	}
endif;