<?php

/* @author    2codeThemes
*  @package   WPQA/includes
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Has Discy theme */
if (!function_exists('has_discy')) {
	function has_discy() {
		$get_theme_name = get_option("get_theme_name");
		return ($get_theme_name == "discy"?true:false);
	}
}

/* Has Himer theme */
if (!function_exists('has_himer')) {
	function has_himer() {
		$get_theme_name = get_option("get_theme_name");
		return ($get_theme_name == "himer"?true:false);
	}
}

/* Has Knowly theme */
if (!function_exists('has_knowly')) {
	function has_knowly() {
		$get_theme_name = get_option("get_theme_name");
		return ($get_theme_name == "knowly"?true:false);
	}
}

/* Has Questy theme */
if (!function_exists('has_questy')) {
	function has_questy() {
		$get_theme_name = get_option("get_theme_name");
		return ($get_theme_name == "questy"?true:false);
	}
}

/* Has Ask Me theme */
if (!function_exists('has_askme')) {
	function has_askme() {
		$get_theme_name = get_option("get_theme_name");
		return ($get_theme_name == "askme"?true:false);
	}
}

/* Theme name */
function wpqa_name_theme() {
	if (has_discy()) {
		$theme_name = "Discy";
	}else if (has_himer()) {
		$theme_name = "Himer";
	}else if (has_knowly()) {
		$theme_name = "Knowly";
	}else if (has_questy()) {
		$theme_name = "Questy";
	}else if (has_askme()) {
		$theme_name = "Ask Me";
	}else {
		$theme_name = "Theme";
	}
	return $theme_name;
}

/* Theme URL */
function wpqa_theme_url() {
	if (has_discy()) {
		$theme_url = "https://1.envato.market/drV57";
	}else if (has_himer()) {
		$theme_url = "https://1.envato.market/2rgWD0";
	}else if (has_knowly()) {
		$theme_url = "https://1.envato.market/2rgWD0";
	}else if (has_questy()) {
		$theme_url = "https://1.envato.market/2rgWD0";
	}
	return (isset($theme_url)?$theme_url:"");
}

/* Mobile app android URL */
function wpqa_android_url() {
	if (has_discy()) {
		$app_url = "https://play.google.com/store/apps/details?id=app.ask.application";
	}else if (has_himer()) {
		$app_url = "https://play.google.com/store/apps/details?id=app.himer";
	}else if (has_knowly()) {
		$app_url = "https://1.envato.market/2rgWD0";
	}else if (has_questy()) {
		$app_url = "https://1.envato.market/2rgWD0";
	}
	return (isset($app_url)?$app_url:"");
}

/* Mobile app ios URL */
function wpqa_ios_url() {
	if (has_discy()) {
		$app_url = "https://apps.apple.com/app/discy/id1535374585";
	}else if (has_himer()) {
		$app_url = "https://apps.apple.com/app/himer/id1604445650";
	}else if (has_knowly()) {
		$app_url = "https://1.envato.market/2rgWD0";
	}else if (has_questy()) {
		$app_url = "https://1.envato.market/2rgWD0";
	}
	return (isset($app_url)?$app_url:"");
}

/* Icons text and URL */
function wpqa_icons_text_url() {
	if (has_discy()) {
		$text = sprintf(esc_html__('Icon (use %1$s entypo %2$s like: facebook)','wpqa'),'<a href="https://2code.info/demo/themes/Discy/entypo/" target="_blank">','</a>');
	}else if (has_himer()) {
		$text = sprintf(esc_html__('Icon (use %1$s ionicons %2$s like: icon-social-facebook)','wpqa'),'<a href="https://2code.info/demo/themes/Himer/ionicons/" target="_blank">','</a>');
	}else if (has_knowly()) {
		$text = sprintf(esc_html__('Icon (use %1$s ionicons %2$s like: icon-social-facebook)','wpqa'),'<a href="https://2code.info/demo/themes/Knowly/ionicons/" target="_blank">','</a>');
	}else if (has_questy()) {
		$text = sprintf(esc_html__('Icon (use %1$s ionicons %2$s like: icon-social-facebook)','wpqa'),'<a href="https://2code.info/demo/themes/Himer/ionicons/" target="_blank">','</a>');
	}
	return (isset($text)?$text:"");
}

/* Export options */
function wpqa_export_options() {
	$export = array(wpqa_options,wpqa_styling_options,wpqa_mobile_options);
	$export = apply_filters("wpqa_export_settings",$export);
	$current_options = array();
	foreach ($export as $option) {
		$get_option_ = get_option($option);
		if ($get_option_) {
			$current_options[$option] = $get_option_;
		}else {
			$current_options[$option] = array();
		}
	}
	$current_options_e = json_encode($current_options);
	$current_options_e = base64_encode($current_options_e);
	return $current_options_e;
}

/* Theme updater */
function wpqa_updater() {
	if (has_discy() && function_exists('discy_updater')) {
		$return = discy_updater()->is_active();
	}else if (has_himer() && function_exists('himer_updater')) {
		$return = himer_updater()->is_active();
	}else if (has_knowly() && function_exists('knowly_updater')) {
		$return = knowly_updater()->is_active();
	}else if (has_questy() && function_exists('questy_updater')) {
		$return = questy_updater()->is_active();
	}
	return (isset($return)?$return:false);
}

/* Theme meta */
function wpqa_theme_meta($date = "",$category = "",$comment = "",$asked = "",$icons = "",$views = "",$post_id = 0,$post = object) {
	if (has_discy()) {
		discy_meta($date,$category,$comment,$asked,$icons,$views,$post_id,$post);
	}else if (has_himer()) {
		himer_meta($date,$category,$comment,$asked,$icons,$views,$post_id,$post);
	}else if (has_knowly()) {
		knowly_meta($date,$category,$comment,$asked,$icons,$views,$post_id,$post);
	}else if (has_questy()) {
		questy_meta($date,$category,$comment,$asked,$icons,$views,$post_id,$post);
	}
}

/* Theme color */
function wpqa_theme_color() {
	if (has_discy()) {
		$theme_color = "#2d6ff7";
	}else if (has_himer()) {
		$theme_color = "#2e6ffd";
	}else if (has_knowly()) {
		$theme_color = "#1eb88d";
	}else if (has_questy()) {
		$theme_color = "#1173ee";
	}else {
		$theme_color = "#ff7361";
	}
	return $theme_color;
}

/* Theme color */
function wpqa_input_button() {
	if (has_himer()) {
		$input_button = "button";
	}else if (has_knowly()) {
		$input_button = "button";
	}else if (has_questy()) {
		$input_button = "button";
	}else {
		$input_button = "input";
	}
	return $input_button;
}

/* Second menu */
function wpqa_second_menu() {
	if (has_himer() || has_knowly()) {
		if (is_author() || wpqa_is_user_profile()) {
			$author_tabs = wpqa_options("author_tabs");
			$second_menu = ($author_tabs == "on"?false:true);
		}
	}else {
		$second_menu = true;
	}
	return (isset($second_menu)?$second_menu:"");
}?>