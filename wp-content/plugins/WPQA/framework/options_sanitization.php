<?php

/* @author    2codeThemes
*  @package   WPQA/framework
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Text */

add_filter( 'framework_sanitize_text', 'sanitize_text_field' );

/* Password */

add_filter( 'framework_sanitize_password', 'sanitize_text_field' );

/* sliderui,sections,sort,select,select,radio,images,textarea */

$framework_sanitize = array("sliderui","sections","sort","select","select_category","multicheck_category","radio","images","textarea","elements","roles","upload_images");
foreach ($framework_sanitize as $key => $value) {
	add_filter( 'framework_sanitize_'.$value, 'wpqa_sanitize_enum', 10, 2);
}

/* Checkbox */

function wpqa_sanitize_checkbox( $input ) {
	if ( $input ) {
		$output = 'on';
	} else {
		$output = 0;
	}
	return $output;
}
add_filter( 'framework_sanitize_checkbox', 'wpqa_sanitize_checkbox' );

/* Multicheck */

function wpqa_sanitize_multicheck( $input, $option ) {
	$output = array();
	if (isset($option["sort"]) && $option["sort"] == "yes") {
		$output = $input;
	}else {
		if ( is_array( $input ) ) {
			foreach( $option['options'] as $key => $value ) {
				if (isset($input[$key]) && $value == "on") {
					$output[$key] = false;
				}
			}
			foreach( $input as $key => $value ) {
				if (isset($input[$key]) && $value == "on") {
					$output[$key] = "on";
				}else {
					$output[$key] = false;
				}
			}
		}
	}
	return $output;
}
add_filter( 'framework_sanitize_multicheck', 'wpqa_sanitize_multicheck', 10, 2 );

/* Color Picker */

add_filter( 'framework_sanitize_color', 'wpqa_sanitize_hex' );

/* Uploader */

function wpqa_sanitize_upload( $input ) {
	$output = '';
	$filetype = wp_check_filetype($input);
	if ( $filetype["ext"] ) {
		$output = $input;
	}
	return $output;
}
add_filter( 'framework_sanitize_upload', 'wpqa_sanitize_upload' );

/* Editor */

function wpqa_sanitize_editor($input) {
	if ( current_user_can( 'unfiltered_html' ) ) {
		$output = $input;
	}
	else {
		global $allowedtags;
		$output = wpautop(wp_kses( $input, $allowedtags));
	}
	return $output;
}
add_filter( 'framework_sanitize_editor', 'wpqa_sanitize_editor' );

/* Allowed Tags */

function wpqa_sanitize_allowedtags( $input ) {
	global $allowedtags;
	$output = wpautop( wp_kses( $input, $allowedtags ) );
	return $output;
}

/* Allowed Post Tags */

function wpqa_sanitize_allowedposttags( $input ) {
	global $allowedposttags;
	$output = wpautop(wp_kses( $input, $allowedposttags));
	return $output;
}
add_filter( 'framework_sanitize_info', 'wpqa_sanitize_allowedposttags' );

/* Check that the key value sent is valid */

function wpqa_sanitize_enum( $input, $option ) {
	$output = $input;
	return $output;
}

/* Background */

function wpqa_sanitize_background( $input ) {
	$output = wp_parse_args( $input, array(
		'color' => '',
		'image'  => '',
		'repeat'  => 'repeat',
		'position' => 'top center',
		'attachment' => 'scroll'
	) );
	
	if (isset($input['color'])) {
		$output['color'] = apply_filters( 'framework_sanitize_hex', $input['color'] );
	}
	if (isset($input['image'])) {
		$output['image'] = apply_filters( 'framework_sanitize_upload', $input['image'] );
	}
	if (isset($input['repeat'])) {
		$output['repeat'] = apply_filters( 'framework_background_repeat', $input['repeat'] );
	}
	if (isset($input['position'])) {
		$output['position'] = apply_filters( 'framework_background_position', $input['position'] );
	}
	if (isset($input['attachment'])) {
		$output['attachment'] = apply_filters( 'framework_background_attachment', $input['attachment'] );
	}
	return $output;
}
add_filter( 'framework_sanitize_background', 'wpqa_sanitize_background' );

function wpqa_sanitize_background_repeat( $value ) {
	$recognized = wpqa_recognized_background_repeat();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'framework_default_background_repeat', current( $recognized ) );
}
add_filter( 'framework_background_repeat', 'wpqa_sanitize_background_repeat' );

function wpqa_sanitize_background_position( $value ) {
	$recognized = wpqa_recognized_background_position();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'framework_default_background_position', current( $recognized ) );
}
add_filter( 'framework_background_position', 'wpqa_sanitize_background_position' );

function wpqa_sanitize_background_attachment( $value ) {
	$recognized = wpqa_recognized_background_attachment();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'framework_default_background_attachment', current( $recognized ) );
}
add_filter( 'framework_background_attachment', 'wpqa_sanitize_background_attachment' );


/* Typography */

function wpqa_sanitize_typography( $input, $option ) {

	$output = wp_parse_args( $input, array(
		'size'  => '',
		'face'  => '',
		'style' => '',
		'color' => ''
	) );

	$output['face']  = apply_filters( 'framework_font_face', $output['face'] );
	$output['size']  = apply_filters( 'framework_font_size', $output['size'] );
	$output['style'] = apply_filters( 'framework_font_style', $output['style'] );
	$output['color'] = apply_filters( 'framework_sanitize_color', $output['color'] );
	return $output;
}
add_filter( 'framework_sanitize_typography', 'wpqa_sanitize_typography', 10, 2 );

function wpqa_sanitize_font_size( $value ) {
	$recognized = wpqa_recognized_font_sizes();
	$value_check = preg_replace('/px/','', $value);
	if ( in_array( (int) $value_check, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'framework_default_font_size', $recognized );
}
add_filter( 'framework_font_size', 'wpqa_sanitize_font_size' );

function wpqa_sanitize_font_style( $value ) {
	$recognized = wpqa_recognized_font_styles();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'framework_default_font_style', current( $recognized ) );
}
add_filter( 'framework_font_style', 'wpqa_sanitize_font_style' );


function wpqa_sanitize_font_face( $value ) {
	return $value;
}
add_filter( 'framework_font_face', 'wpqa_sanitize_font_face' );

/**
 * Get recognized background repeat settings
 *
 * @return   array
 *
 */
function wpqa_recognized_background_repeat() {
	$default = array(
		'no-repeat' => esc_html__( 'No Repeat', "wpqa" ),
		'repeat-x'  => esc_html__( 'Repeat Horizontally', "wpqa" ),
		'repeat-y'  => esc_html__( 'Repeat Vertically', "wpqa" ),
		'repeat'    => esc_html__( 'Repeat All', "wpqa" ),
		);
	return apply_filters( 'framework_recognized_background_repeat', $default );
}

/**
 * Get recognized background positions
 *
 * @return   array
 *
 */
function wpqa_recognized_background_position() {
	$default = array(
		'top left'      => esc_html__( 'Top Left', "wpqa" ),
		'top center'    => esc_html__( 'Top Center', "wpqa" ),
		'top right'     => esc_html__( 'Top Right', "wpqa" ),
		'center left'   => esc_html__( 'Middle Left', "wpqa" ),
		'center center' => esc_html__( 'Middle Center', "wpqa" ),
		'center right'  => esc_html__( 'Middle Right', "wpqa" ),
		'bottom left'   => esc_html__( 'Bottom Left', "wpqa" ),
		'bottom center' => esc_html__( 'Bottom Center', "wpqa" ),
		'bottom right'  => esc_html__( 'Bottom Right', "wpqa")
		);
	return apply_filters( 'framework_recognized_background_position', $default );
}

/**
 * Get recognized background attachment
 *
 * @return   array
 *
 */
function wpqa_recognized_background_attachment() {
	$default = array(
		'scroll' => esc_html__( 'Scroll Normally', "wpqa" ),
		'fixed'  => esc_html__( 'Fixed in Place', "wpqa")
		);
	return apply_filters( 'framework_recognized_background_attachment', $default );
}

/**
 * Sanitize a color represented in hexidecimal notation.
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @param    string    The value that this function should return if it cannot be recognized as a color.
 * @return   string
 *
 */

function wpqa_sanitize_hex( $hex, $default = '' ) {
	if ( wpqa_validate_hex( $hex ) ) {
		return $hex;
	}
	return $default;
}

/**
 * Get recognized font sizes.
 *
 * Returns an indexed array of all recognized font sizes.
 * Values are integers and represent a range of sizes from
 * smallest to largest.
 *
 * @return   array
 */

function wpqa_recognized_font_sizes() {
	$sizes = range( 9, 71 );
	$sizes = apply_filters( 'framework_recognized_font_sizes', $sizes );
	$sizes = array_map( 'absint', $sizes );
	return $sizes;
}

/**
 * Get recognized font faces.
 *
 * Returns an array of all recognized font faces.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 *
 */
function wpqa_recognized_font_faces() {
	$default = array(
		'arial'     => 'Arial',
		'verdana'   => 'Verdana, Geneva',
		'trebuchet' => 'Trebuchet',
		'georgia'   => 'Georgia',
		'times'     => 'Times New Roman',
		'tahoma'    => 'Tahoma, Geneva',
		'palatino'  => 'Palatino',
		'helvetica' => 'Helvetica*'
		);
	return apply_filters( 'framework_recognized_font_faces', $default );
}

/**
 * Get recognized font styles.
 *
 * Returns an array of all recognized font styles.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 *
 */
function wpqa_recognized_font_styles() {
	$default = array(
		'default'     => esc_html__("Style","wpqa"),
		'normal'      => esc_html__( 'Normal', "wpqa" ),
		'italic'      => esc_html__( 'Italic', "wpqa" ),
		'bold'        => esc_html__( 'Bold', "wpqa" ),
		'bold italic' => esc_html__( 'Bold Italic', "wpqa" )
		);
	return apply_filters( 'framework_recognized_font_styles', $default );
}

/**
 * Is a given string a color formatted in hexidecimal notation?
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @return   bool
 *
 */

function wpqa_validate_hex( $hex ) {
	$hex = trim( $hex );
	/* Strip recognized prefixes. */
	if ( 0 === strpos( $hex, '#' ) ) {
		$hex = substr( $hex, 1 );
	}
	elseif ( 0 === strpos( $hex, '%23' ) ) {
		$hex = substr( $hex, 3 );
	}
	/* Regex match. */
	if ( 0 === preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
		return false;
	}
	else {
		return true;
	}
}?>