<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Ads */
function wpqa_check_without_ads($class = '') {
	$show_ads = true;
	$custom_ads_show = wpqa_options("custom_ads_show");
	if ($custom_ads_show == "on") {
		$adv_work_on = wpqa_options("adv_work_on");
		$ads_exclude_pages = wpqa_options("ads_exclude_pages");
		$ads_exclude_pages = ($ads_exclude_pages != ""?explode(",",$ads_exclude_pages):array());
		$ads_exclude_users = wpqa_options("ads_exclude_users");
		$ads_exclude_users = ($ads_exclude_users != ""?explode(",",$ads_exclude_users):array());
		$ads_exclude_taxes = wpqa_options("ads_exclude_taxes");
		$ads_exclude_taxes = ($ads_exclude_taxes != ""?explode(",",$ads_exclude_taxes):array());
		if (is_page() || is_single()) {
			global $post;
		}
		if (is_category() || is_tag() || is_tax() || is_archive()) {
			$term_id = (int)get_query_var('wpqa_term_id');
		}
		if (wpqa_is_user_profile()) {
			$wpqa_user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
		}
		if ((is_home() || is_front_page()) && is_array($adv_work_on) && in_array("home",$adv_work_on)) {
			$show_ads = true;
		}else if (((is_singular("group") && is_array($adv_work_on) && in_array("groups",$adv_work_on)) || ((is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) && is_array($adv_work_on) && in_array("questions",$adv_work_on)) || (is_singular(wpqa_knowledgebase_type) && is_array($adv_work_on) && in_array("knowledgebases",$adv_work_on)) || (is_singular("post") && is_array($adv_work_on) && in_array("posts",$adv_work_on)) || (is_page() && is_array($adv_work_on) && in_array("pages",$adv_work_on))) && !in_array($post->ID,$ads_exclude_pages)) {
			$show_ads = true;
		}else if (is_single() && is_array($adv_work_on) && in_array("post_type",$adv_work_on) && !in_array($post->ID,$ads_exclude_pages)) {
			$show_ads = true;
		}else if (is_post_type_archive("group") && is_array($adv_work_on) && in_array("groups",$adv_work_on) && isset($term_id) && !in_array($term_id,$ads_exclude_taxes)) {
			$show_ads = true;
		}else if (is_tax(wpqa_question_categories) && is_array($adv_work_on) && in_array("q_categories",$adv_work_on) && isset($term_id) && !in_array($term_id,$ads_exclude_taxes)) {
			$show_ads = true;
		}else if (is_post_type_archive(wpqa_questions_type) && is_array($adv_work_on) && in_array("q_categories",$adv_work_on)) {
			$show_ads = true;
		}else if (is_tax(wpqa_question_tags) && is_array($adv_work_on) && in_array("q_tags",$adv_work_on) && isset($term_id) && !in_array($term_id,$ads_exclude_taxes)) {
			$show_ads = true;
		}else if (is_tax(wpqa_knowledgebase_categories) && is_array($adv_work_on) && in_array("k_categories",$adv_work_on) && isset($term_id) && !in_array($term_id,$ads_exclude_taxes)) {
			$show_ads = true;
		}else if (is_post_type_archive(wpqa_knowledgebase_type) && is_array($adv_work_on) && in_array("k_categories",$adv_work_on)) {
			$show_ads = true;
		}else if (is_tax(wpqa_knowledgebase_tags) && is_array($adv_work_on) && in_array("k_tags",$adv_work_on) && isset($term_id) && !in_array($term_id,$ads_exclude_taxes)) {
			$show_ads = true;
		}else if (is_category() && is_array($adv_work_on) && in_array("categories",$adv_work_on) && isset($term_id) && !in_array($term_id,$ads_exclude_taxes)) {
			$show_ads = true;
		}else if (is_archive() && !is_post_type_archive(wpqa_questions_type) && !is_post_type_archive(wpqa_knowledgebase_type) && !is_post_type_archive("group") && !is_tax(wpqa_question_tags) && !is_tax(wpqa_knowledgebase_tags) && !is_tag() && !is_category() && is_array($adv_work_on) && in_array("categories",$adv_work_on)) {
			$show_ads = true;
		}else if (is_tag() && is_array($adv_work_on) && in_array("tags",$adv_work_on) && isset($term_id) && !in_array($term_id,$ads_exclude_taxes)) {
			$show_ads = true;
		}else if (wpqa_is_user_profile() && wpqa_is_home_profile() && isset($wpqa_user_id) && is_array($adv_work_on) && in_array("main_profile",$adv_work_on) && !in_array($wpqa_user_id,$ads_exclude_users)) {
			$show_ads = true;
		}else if (wpqa_is_user_profile() && !wpqa_is_home_profile() && isset($wpqa_user_id) && is_array($adv_work_on) && in_array("other_profile",$adv_work_on) && !in_array($wpqa_user_id,$ads_exclude_users)) {
			$show_ads = true;
		}else {
			$show_ads = false;
		}
	}
	$custom_permission = wpqa_options("custom_permission");
	if ($custom_permission == "on") {
		$user_id = get_current_user_id();
		if (is_user_logged_in()) {
			$user_is_login = get_userdata($user_id);
			$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
		}
		if (($custom_permission == "on" && is_user_logged_in() && !is_super_admin($user_id) && empty($roles["without_ads"])) || ($custom_permission == "on" && !is_user_logged_in())) {
			if ($class == "aalan-header") {
				$subscriptions_payment = wpqa_options("subscriptions_payment");
				if ($subscriptions_payment == 'on') {
					$show_alert = wpqa_paid_subscriptions_message(esc_html__("Do you need to remove the ads?","wpqa"),"","message-error-ads");
				}
			}
		}else {
			$show_ads = false;
		}
	}
	return $show_ads;
}
function wpqa_ads($adv_type_meta,$adv_link_meta,$adv_code_meta,$adv_href_meta,$adv_img_meta,$li = false,$page = false,$class = false,$author_cat = false,$question_columns = false) {
	$tax_filter = apply_filters(wpqa_prefix_theme."_before_question_category",false);
	$output = '';
	$show_ads = wpqa_check_without_ads($class);
	if ($show_ads == true) {
		if ($page == "on" && (is_page() || is_single())) {
			$wpqa_adv_type = wpqa_post_meta($adv_type_meta);
			$wpqa_adv_link = wpqa_post_meta($adv_link_meta);
			$wpqa_adv_code = wpqa_post_meta($adv_code_meta);
			$wpqa_adv_href = wpqa_post_meta($adv_href_meta);
			$wpqa_adv_img  = wpqa_image_url_id(wpqa_post_meta($adv_img_meta));
		}
		if ($author_cat == "on" && (wpqa_is_user_profile() || is_category() || is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true)) {
			if (wpqa_is_user_profile()) {
				$wpqa_user_id  = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
				$wpqa_adv_type = get_user_meta($wpqa_user_id,prefix_author.$adv_type_meta,true);
				$wpqa_adv_link = get_user_meta($wpqa_user_id,prefix_author.$adv_link_meta,true);
				$wpqa_adv_code = get_user_meta($wpqa_user_id,prefix_author.$adv_code_meta,true);
				$wpqa_adv_href = get_user_meta($wpqa_user_id,prefix_author.$adv_href_meta,true);
				$wpqa_adv_img  = wpqa_image_url_id(get_user_meta($wpqa_user_id,prefix_author.$adv_img_meta,true));
			}
			if (is_category() || is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true) {
				if (is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true) {
					$category_id = (isset(get_queried_object()->term_id)?get_queried_object()->term_id:"");
				}else {
					$category_id = esc_html(get_query_var('cat'));
				}
				$wpqa_adv_type = wpqa_term_meta($adv_type_meta,$category_id);
				$wpqa_adv_link = wpqa_term_meta($adv_link_meta,$category_id);
				$wpqa_adv_code = wpqa_term_meta($adv_code_meta,$category_id);
				$wpqa_adv_href = wpqa_term_meta($adv_href_meta,$category_id);
				$wpqa_adv_img = wpqa_image_url_id(wpqa_term_meta($adv_img_meta,$category_id));
			}
		}
		
		if ($author_cat == "on" && (wpqa_is_user_profile() || is_category() || is_tax(wpqa_question_categories) || is_tax(wpqa_knowledgebase_categories) || $tax_filter == true) && (($wpqa_adv_type == "display_code" && $wpqa_adv_code != "") || ($wpqa_adv_type == "custom_image" && $wpqa_adv_img != ""))) {
			$adv_type = $wpqa_adv_type;
			$adv_link = $wpqa_adv_link;
			$adv_code = $wpqa_adv_code;
			$adv_href = $wpqa_adv_href;
			$adv_img  = $wpqa_adv_img;
		}else if ($page == "on" && (is_single() || is_page()) && (($wpqa_adv_type == "display_code" && $wpqa_adv_code != "") || ($wpqa_adv_type == "custom_image" && $wpqa_adv_img != ""))) {
			$adv_type = $wpqa_adv_type;
			$adv_link = $wpqa_adv_link;
			$adv_code = $wpqa_adv_code;
			$adv_href = $wpqa_adv_href;
			$adv_img  = $wpqa_adv_img;
		}else {
			$adv_type = wpqa_options($adv_type_meta);
			$adv_link = wpqa_options($adv_link_meta);
			$adv_code = wpqa_options($adv_code_meta);
			$adv_href = wpqa_options($adv_href_meta);
			$adv_img  = wpqa_options($adv_img_meta);

			$ads_options = array(
				"adv_type" => $adv_type,
				"adv_link" => $adv_link,
				"adv_code" => $adv_code,
				"adv_href" => $adv_href,
				"adv_img"  => $adv_img
			);
			$ads_options = apply_filters(wpqa_prefix_theme."_ads_options",$ads_options,0,prefix_terms,$adv_type_meta,$adv_link_meta,$adv_code_meta,$adv_href_meta,$adv_img_meta);

			$adv_type = $ads_options["adv_type"];
			$adv_link = $ads_options["adv_link"];
			$adv_code = $ads_options["adv_code"];
			$adv_href = $ads_options["adv_href"];
			$adv_img = wpqa_image_url_id($ads_options["adv_img"]);
		}
		
		if (($adv_type == "display_code" && $adv_code != "" && $adv_code != "empty") || ($adv_type == "custom_image" && $adv_img != "")) {
			$output .= ($li == "li" || strpos($class,'post-with-columns') !== false?"":"<div class='clearfix'></div>").'
			<'.($li == "li"?"li":"div").' class="aalan'.($class != ""?" ".$class:"").'">';
				if (strpos($class,'post-with-columns') == false) {
					$output .= '<div class="clearfix"></div>';
				}
				if ($question_columns == "style_2") {
					$output .= '<div class="post-with-columns-border"></div>';
				}
				$output .= (isset($show_alert)?$show_alert:"");
				if ($adv_type == "display_code") {
					$output .= do_shortcode(stripslashes($adv_code));
				}else {
					if ($adv_href != "") {
						$output .= '<a'.($adv_link == "new_page"?" target='_blank'":"").' href="'.esc_url($adv_href).'">';
					}
					$output .= '<img alt="'.esc_attr__("aalan","wpqa").'" src="'.$adv_img.'">';
					if ($adv_href != "") {
						$output .= '</a>';
					}
				}
				if (strpos($class,'post-with-columns') == false) {
					$output .= '<div class="clearfix"></div>';
				}
			$output .= '</'.($li == "li"?"li":"div").'><!-- End aalan -->'.
			($li == "li" || strpos($class,'post-with-columns') !== false?"":"<div class='clearfix'></div>");
		}
	}
	return $output;
}
function wpqa_widget_ads($adv_type,$adv_link,$adv_href,$adv_img,$adv_code,$class = 'aalan') {
	$output = '<div class="'.$class.'">';
		if ($adv_type == "custom_image") {
			if ($adv_href != "") {$output .= '<a'.($adv_link == "new_page"?" target='_blank'":"").' href="'.esc_url($adv_href).'">';}
				$output .= '<img alt="'.esc_attr__("aalan","wpqa").'" src="'.esc_url($adv_img).'">';
			if ($adv_href != "") {$output .= '</a>';}
		}else {
			$output .= $adv_code;
		}
	$output .= '</div><!-- End aalan -->';
	return $output;
}?>