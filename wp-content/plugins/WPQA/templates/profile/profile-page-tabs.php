<?php

/* @author    2codeThemes
*  @package   WPQA/templates/profile
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$profile_page_menu = 'profile_page_menu';
$locations = get_nav_menu_locations();
if (isset($locations[$profile_page_menu])) {
    $menu_profile_items = wp_get_nav_menu_items($locations[$profile_page_menu]);
	if ($author_widget == "on" && is_array($menu_profile_items) && !empty($menu_profile_items)) {
		$i_count = 0;
		foreach ($menu_profile_items as $menu_key => $menu_value) {
			if (strpos($menu_value->url,'#wpqa-') !== false) {
				$first_one = str_ireplace("#wpqa-","",$menu_value->url);
				if ($user_id > 0 && $first_one != "") {
					update_user_meta($user_id,"wpqa_first_profile_tab",$first_one);
				}
				break;
			}
			$i_count++;
		}
	}
}
if (!isset($locations[$profile_page_menu])) {
	$profile_page_menu_available = true;
}