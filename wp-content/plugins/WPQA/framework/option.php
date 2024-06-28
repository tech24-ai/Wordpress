<?php

/* @author    2codeThemes
*  @package   WPQA/framework
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Save default options */
$wpqa_custom_queries = get_option("wpqa_custom_queries");
$wpqa_custom_queries = (is_array($wpqa_custom_queries) && !empty($wpqa_custom_queries)?$wpqa_custom_queries:array());
if ((!get_option(wpqa_options) && !isset($wpqa_custom_queries["wpqa_options"])) || (!get_option(wpqa_styling_options) && !isset($wpqa_custom_queries["wpqa_styling_options"])) || (!get_option(wpqa_mobile_options) && !isset($wpqa_custom_queries["wpqa_mobile_options"]))) {
	$wpqa_admin_options = new wpqa_admin_options;
	if (!get_option(wpqa_options) && !isset($wpqa_custom_queries["wpqa_options"])) {
		$wpqa_custom_queries["wpqa_options"] = true;
		update_option("wpqa_custom_queries",$wpqa_custom_queries);
		$default_options = $wpqa_admin_options->get_default_values("options");
		add_option(wpqa_options,$default_options);
	}
	if (!get_option(wpqa_styling_options) && !isset($wpqa_custom_queries["wpqa_styling_options"])) {
		$wpqa_custom_queries["wpqa_styling_options"] = true;
		update_option("wpqa_custom_queries",$wpqa_custom_queries);
		$default_options = $wpqa_admin_options->get_default_values("styling");
		add_option(wpqa_styling_options,$default_options);
	}
	if (!get_option(wpqa_mobile_options) && !isset($wpqa_custom_queries["wpqa_mobile_options"])) {
		$wpqa_custom_queries["wpqa_mobile_options"] = true;
		update_option("wpqa_custom_queries",$wpqa_custom_queries);
		$default_options = $wpqa_admin_options->get_default_values("mobile");
		add_option(wpqa_mobile_options,$default_options);
	}
}else {
	$wpqa_custom_queries["wpqa_options"] = true;
	$wpqa_custom_queries["wpqa_styling_options"] = true;
	$wpqa_custom_queries["wpqa_mobile_options"] = true;
	update_option("wpqa_custom_queries",$wpqa_custom_queries);
}
$white_label_array = apply_filters("wpqa_white_label_array",array());
if (is_array($white_label_array) && !empty($white_label_array)) {
	foreach ($white_label_array as $value) {
		if ($value != "" && !get_option(wpqa_prefix_theme."_".$value."_options") && !isset($wpqa_custom_queries[wpqa_prefix_theme."_".$value."_options"])) {
			$wpqa_admin_options = new wpqa_admin_options;
			$default_options = $wpqa_admin_options->get_default_values($value);
			if (is_array($default_options) && !empty($default_options)) {
				$wpqa_custom_queries[wpqa_prefix_theme."_".$value."_options"] = true;
				update_option("wpqa_custom_queries",$wpqa_custom_queries);
				update_option(wpqa_prefix_theme."_".$value."_options",$default_options);
			}
		}
	}
}?>