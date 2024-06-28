<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Image button */
add_action('wpqa_init','wpqa_editor_buttons');
function wpqa_editor_buttons() {
	add_filter('tiny_mce_before_init','wpqa_remove_editor_wp_adv');
	add_filter('mce_buttons','wpqa_remove_tiny_mce_buttons');
	add_filter('mce_buttons_2','wpqa_remove_tiny_mce_buttons_2',99);
	$custom_upload_image = apply_filters("wpqa_custom_upload_image",true);
	if ($custom_upload_image == true) {
		$custom_permission = wpqa_options("custom_permission");
		$upload_images = wpqa_options("upload_images");
		$user_id = get_current_user_id();
		if ($user_id > 0) {
			$user_info = get_userdata($user_id);
			$user_is_login = get_userdata($user_id);
			$roles = $user_info->allcaps;
		}
		$allow_to_upload = apply_filters('wpqa_allow_to_upload',true);
		if ($allow_to_upload == true && ($custom_permission != "on" || (!is_user_logged_in() && $upload_images == "on") || (is_user_logged_in() && $custom_permission == "on" && isset($roles["upload_files"]) && $roles["upload_files"] == 1))) {
			add_filter("mce_external_plugins","wpqa_external_buttons");
			add_filter("mce_buttons","wpqa_register_buttons");
		}
	}
}
function wpqa_external_buttons($plugin_array) {
	$plugin_array['WPQA'] = plugins_url('image.js',__FILE__);
	return $plugin_array;
}
function wpqa_register_buttons($buttons) {
	array_push($buttons,"wpqa_custom_image");
	return $buttons;
}
add_action('wp_ajax_wpqa_editor_upload_image','wpqa_editor_upload_image');
add_action('wp_ajax_nopriv_wpqa_editor_upload_image','wpqa_editor_upload_image');
function wpqa_editor_upload_image() {
	$result = array();
	if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) :
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		$file = array(
			'name'	   => $_FILES['image']['name']['file'],
			'type'	   => $_FILES['image']['type']['file'],
			'tmp_name' => $_FILES['image']['tmp_name']['file'],
			'error'	   => $_FILES['image']['error']['file'],
			'size'	   => $_FILES['image']['size']['file']
		);
		$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/webp","image/gif","image/tiff","image/ico");
		if (!in_array($_FILES['image']['type'],$types)) :
			$result["success"] = 0;
			$result["error"] = esc_html__("Attachment Error! Please upload image only.","wpqa");
		endif;
		
		$attachment = wp_handle_upload($file,array('test_form' => false),current_time('mysql'));
		if (isset($attachment['error'])) :
			$result["success"] = 0;
			$result["error"] = esc_html__("Attachment Error: ","wpqa") . $attachment['error'];
		endif;

		if (isset($attachment['type']) && isset($attachment['file'])) :
			$user_id = get_current_user_id();
			$attachment_data = array(
				'post_mime_type' => $attachment['type'],
				'post_title'	 => preg_replace('/\.[^.]+$/','',basename($attachment['file'])),
				'post_content'   => '',
				'post_status'	 => 'inherit',
				'post_author'    => ($user_id > 0?$user_id:0),
			);
			$attachment_id = wp_insert_attachment($attachment_data,$attachment['file'],0);
			$attachment_metadata = wp_generate_attachment_metadata($attachment_id,$attachment['file']);
			wp_update_attachment_metadata($attachment_id,$attachment_metadata);
		endif;
		if (isset($attachment["url"]) && $attachment["url"] != "") {
			$result["success"] = $attachment["url"];
		}
	endif;
	echo json_encode($result);
	die();
}
/* Remove wp_adv button */
function wpqa_remove_editor_wp_adv($in) {
	$in['wordpress_adv_hidden'] = false;
	return $in;
}
/* Remove unlink from editor and add block editor */
function wpqa_remove_tiny_mce_buttons($buttons) {
	$remove = array('unlink','blockquote','wp_adv');
	$buttons[] = 'hcb_select';
	return array_diff($buttons,$remove);
}
/* Remove block editor from editor and add unlink */
function wpqa_remove_tiny_mce_buttons_2($buttons) {
	$remove = array('hcb_select');
	$buttons[] = 'unlink';
	$buttons[] = 'blockquote';
	return array_diff($buttons,$remove);
}?>