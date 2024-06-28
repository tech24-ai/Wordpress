<?php

/* @author    2codeThemes
*  @package   WPQA/options
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function wpqa_admin_option_setting($default = "",$page_name = "") {
	if (is_admin() || $default == "default") {
		$options = array();
		if ((isset($_GET['page']) && $_GET['page'] == $page_name) || ($default != "" && $default == $page_name)) {
			$options = apply_filters("wpqa_options_settings_".$page_name,$options);
		}
		return $options;
	}
}?>