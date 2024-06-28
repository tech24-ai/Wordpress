<?php

/* @author    2codeThemes
*  @package   WPQA/shortcodes
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function wpqa_payment_shortcode($atts,$content = null) {
	$a = shortcode_atts( array(
	    'id' => 'id',
	), $atts );
	$post_id = (int)$a['id'];
	if (strpos($content,'[/else_wpqa_payment]') !== false) {
		$content_parts = preg_split('/\[\/else_wpqa_payment\]/',$content,2);
		if (count($content_parts) === 2) {
			$content = $content_parts[0];
			$else_content = $content_parts[1];
		}else {
			$content = '';
			$else_content = '';
		}
	}

	$wpqa_allow_to_pay = get_post_meta($post_id,"wpqa_allow_to_pay",true);
	$user_id = get_current_user_id();
	if (is_super_admin($user_id) || (is_array($wpqa_allow_to_pay) && !empty($wpqa_allow_to_pay) && in_array($user_id,$wpqa_allow_to_pay))) {
		$content = $content.wpqa_download_button_payment($post_id,true,"","<br>");
		return $content;
	}else {
		$post = get_post($post_id);
		if (isset($post->ID) && $post->ID > 0) {
			$button_name = get_post_meta($post->ID,prefix_meta.'button_name',true);
			$button_name = ($button_name != ""?esc_html($button_name):esc_html__("Pay now","wpqa"));
			$payment_pages_target = wpqa_options("payment_pages_target");
			$payment_pages_target = ($payment_pages_target == "new_page"?"_blank":"_self");
			$payment_link = '<div class="clearfix"></div><a href="'.wpqa_checkout_link("pay",$post_id).'" target="'.$payment_pages_target.'" class="button-default btn btn__primary btn__sm">'.$button_name.'</a>';
		}
		$else_content = (isset($else_content)?$else_content:'').(isset($payment_link)?$payment_link:'');
		return $else_content;;
	}
}?>