<?php

/* @author    2codeThemes
*  @package   WPQA/framework
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Get uploader */
function wpqa_options_uploader($_id,$_value,$_desc = '',$_name = '',$no_id_name = '',$options = array(),$page = '',$post_term = object,$options_name = wpqa_options) {
	// Gets the unique option id
	$white_label_array = apply_filters("wpqa_white_label_array",array());
	$white_label_settings = false;
	if (is_array($white_label_array) && !empty($white_label_array)) {
		foreach ($white_label_array as $value) {
			if (strpos($_SERVER['REQUEST_URI'],'page='.$value) !== false) {
				$white_label_settings = true;
			}
		}
	}
	$option_name = (strpos($_SERVER['REQUEST_URI'],'page=options') !== false || strpos($_SERVER['REQUEST_URI'],'page=styling') !== false || strpos($_SERVER['REQUEST_URI'],'page=mobile') !== false || $white_label_settings == true?$options_name:wpqa_meta);
	$output = $id = $class = $int = $value = $value_id = $name = '';
	$id = strip_tags( strtolower( $_id ) );
	
	// If a value is passed and we don't have a stored value, use the value that's passed through.
	if (is_array($_value) && !empty($_value) && array_key_exists('url',$_value) && array_key_exists('id',$_value)) {
		$value = $_value["url"];
		$value_id = $_value["id"];
	}else if (!is_array($_value) && $_value != '' && $value == '') {
		$value = $_value;
	}
	
	if (isset($value) && $value != "" && is_numeric($value)) {
		$value_id = $value;
		$value = wp_get_attachment_url($value_id);
	}

	$value_id = (int)$value_id;

	if ($value_id == 0 && $value != "") {
		if (strpos($value,esc_url(home_url('/'))) !== false && strpos($value,"themes/".get_template()."/image") === false) {
			$value_id = wpqa_get_attachment_id($value);
		}
	}
	
	if ($_name != '') {
		$name = $_name;
	}else {
		$name = $option_name.'['.$id.']';
	}
	$image_attrs = (isset($options['height'])?'data-height="'.esc_attr(($page == 'widgets'?$post_term->get_field_id($options['height']):$options['height'])).'" ':'');
	$image_attrs = (isset($options['width'])?'data-width="'.esc_attr(($page == 'widgets'?$post_term->get_field_id($options['width']):$options['width'])).'" ':'').$image_attrs;
	
	if ( $value ) {
		$class = ' has-file';
	}
	$output .= '<div class="form-upload-images">
	<input class="image_id" type="hidden" '.($no_id_name == 'no'?'data-attr="'.esc_attr($name).'][id"':'name="'.esc_attr($name).'[id]"').' value="' . (int)$value_id . '">
	<input '.($no_id_name == 'no'?'attr-id="'.$id.'"':'id="'.$id.'"').' class="upload' . esc_attr($class) . '" type="text" '.($no_id_name == 'no'?'data-attr="'.esc_attr($name).'][url"':'name="'.esc_attr($name).'[url]"').' value="' . esc_attr($value) . '" placeholder="' . esc_attr__('No file chosen', "wpqa") .'">';
	if ( function_exists( 'wp_enqueue_media' ) ) {
		if ( ( $value == '' ) ) {
			$output .= '<input '.$image_attrs.($no_id_name == 'no'?'data-attr="upload-'.esc_attr($id).'"':'id="upload-'.esc_attr($id).'"').' class="upload-button button" type="button" value="' . esc_attr__( 'Upload', "wpqa" ) . '">';
		}else {
			$output .= '<input '.$image_attrs.($no_id_name == 'no'?'data-attr="remove-'.esc_attr($id).'"':'id="remove-'.esc_attr($id).'"').' class="remove-file button" type="button" value="' . esc_attr__( 'Remove', "wpqa" ) . '">';
		}
	}else {
		$output .= '<p><i>' . esc_html__( 'Upgrade your version of WordPress for full media support.', "wpqa" ) . '</i></p>';
	}
	$output .= '</div>';
	if ( $_desc != '' ) {
		$output .= '<span class="framework-metabox-desc">' . $_desc . '</span>';
	}

	$output .= '<div class="screenshot" '.($no_id_name == 'no'?'data-attr="'.$id.'-image"':'id="'.$id.'-image"').'>';

	if ( $value != '' ) {
		$remove = '<a class="remove-image">'.esc_html__("Remove","wpqa").'</a>';
		$image = preg_match('/\.(jpg|jpeg|png|gif|ico)$/',$value);
		if ( $image ) {
			$output .= '<img src="' . $value . '" alt="' . $value . '">' . $remove;
		}else {
			$parts = explode( "/", $value );
			for( $i = 0; $i < sizeof( $parts ); ++$i ) {
				$title = $parts[$i];
			}

			// No output preview if it's not an image.
			$output .= '';

			// Standard generic output if it's not an image.
			$title = esc_html__( 'View File', "wpqa" );
			$output .= '<div class="no-image"><span class="file_link"><a href="' . esc_url($value) . '" target="_blank" rel="external">'.$title.'</a></span></div>';
		}
	}
	$output .= '</div>';
	return $output;
}
/* Get fields */
function wpqa_options_fields($settings = array(),$option_name = "",$page = "options",$post_term = 0,$options_arrgs = array()) {
	global $allowedtags;
	$white_label_array = apply_filters("wpqa_white_label_array",array());
	$white_label_settings = false;
	if (is_array($white_label_array) && !empty($white_label_array)) {
		foreach ($white_label_array as $value) {
			if ($page == $value) {
				$white_label_settings = true;
			}
		}
	}
	$page = ($page == 'author'?'user':$page);
	$page = ($page == 'meta'?'post':$page);
	$wp_page_template = ($page == "post" && isset($post_term) && $post_term > 0?get_post_meta($post_term,"_wp_page_template",true):"");
	if ($option_name != "") {
		$settings = get_option($option_name);
		if (!is_array($settings) || (is_array($settings) && empty($settings))) {
			$settings = get_option(wpqa_options);
		}
	}else if ($page != "widgets") {
		$framework_admin_settings = get_option(wpqa_options);
		// Gets the unique option id
		if ( isset( $framework_admin_settings['id'] ) ) {
			$option_name = $framework_admin_settings['id'];
		}else {
			$option_name = wpqa_options;
		}
		if ($page == "options") {
			$settings = get_option($option_name);
		}
	}
	
	$options = $options_arrgs;
	if (empty($options_arrgs)) {
		$options = & wpqa_admin::_wpqa_admin_options($page);
	}
	
	$counter = 0;
	$menu = '';
	$values = array();
	
	if (is_array($options) && !empty($options)) {
		foreach ($options as $value) {

			$val = $val_terms = $select_value = $output = '';

			// If there is a description save it for labels
			$explain_value = '';
			if ( isset( $value['desc'] ) ) {
				$explain_value = $value['desc'];
			}

			// Wrap all options
			if ($value['type'] != "heading") {

				// Keep all ids lowercase with no spaces
				if (isset($value['id'])) {
					$value_name_id = $value['id'];
					$value['id'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($value['id']) );
					//$value['id'] = $value['id'];
					$id = 'section-'.($page == "widgets"?$post_term->get_field_id( $value['id'] ):$value['id']);
				}

				$class = 'section';
				$wrap_class = 'wrap_class';
				$options_group = 'options-group';
				if ( isset( $value['type'] ) ) {
					$class .= ' section-'.$value['type'].' framework-form-'.$value['type'];
				}
				if ( isset( $value['class'] ) ) {
					$class .= ' '.$value['class'];
				}
				
				if ( ! array_key_exists( 'operator', $value ) || ! in_array( $value['operator'], array( 'and', 'or' ) ) ) {
					$value['operator'] = 'and';
				}

				if ( ! array_key_exists( 'condition', $value ) || ! is_string( $value['condition'] ) ) {
					$value['condition'] = '';
				}
				
				if ($value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != "group" && $value['type'] != "content" && $value['type'] != "html" && $value['type'] != "info") {
					// Set default value to $val
					if ( isset( $value['std'] ) ) {
						$val = $value['std'];
					}
					
					$field_id = esc_html(($page == 'widgets'?$post_term->get_field_id($value['id']):(isset($value['id'])?$value['id']:'')));
					if (($page == "options" || $page == "styling" || $page == "mobile" || $white_label_settings == true) && isset($value['unset'])) {
						$field_name = "";
					}else {
						$field_name = esc_html(($page == 'widgets'?$post_term->get_field_name($value['id']):($page == 'post' || $page == 'term' || $page == 'user'?$field_id:$option_name.'['.$field_id.']')));
					}
				}
				
				// If the option is already saved, override $val
				if ($value['type'] != 'heading' && $value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != "group" && $value['type'] != 'info' && $value['type'] != "content" && $value['type'] != "html" && !isset($value['readonly'])) {
					if (isset($post_term) || (isset($settings[($value['id'])]) && isset($value['id']) && ($value['type'] != "editor" || ($value['type'] == "editor" && $settings[($value['id'])] != "")))) {
						if ($page == "widgets") {
							$val = (isset($settings[$value['id']])?$settings[$value['id']]:(isset($val)?$val:""));
						}else if ($page == "post" && isset($post_term)) {
							if (isset($value['save']) && $value['save'] == "option") {
								$val_terms = get_option($field_name);
							}else {
								$val_terms = get_post_meta($post_term,$field_name,true);
							}
						}else if ($page == "term" && isset($post_term)) {
							$val_terms = get_term_meta($post_term,$field_name,true);
						}else if ($page == "user" && isset($post_term)) {
							$val_terms = get_user_meta($post_term,$field_name,true);
						}else if ($page == "options" || $page == "styling" || $page == "mobile" || $white_label_settings == true) {
							$val = $settings[$field_id];
						}
						
						if ($page == 'post' || $page == 'term' || $page == 'user') {
							if (metadata_exists($page,$post_term,$field_name)) {
								$val = $val_terms;
							}
						}
						
						// Striping slashes of non-array options
						if (!is_array($val)) {
							$val = stripslashes($val);
						}
					}
				}
				
				$val = ($page == "widgets" && isset($value['id']) && isset($value['type']) && $value['type'] == "checkbox" && isset($settings[$value['id']])?$settings[$value['id']]:$val);

				$val = (isset($value['val'])?$value['val']:$val);
				
				if ($value['type'] != 'heading' && $value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != "group" && $value['type'] != 'info' && $value['type'] != "content" && $value['type'] != "html" && !isset($value['readonly']) && array_key_exists( 'id', $value )) {
					$values[ $value['id'] ] = ($value['type'] == 'checkbox' && $val == ""?0:$val);
				}
				
				if ( ! wpqa_field_is_visible( $value['condition'], $value['operator'], $options, $values ) ) {
					$class .= ' hide';
					$wrap_class .= ' hide';
					$options_group .= ' hide';
				}
				
				$condition = empty( $value['condition'] ) ? '' : ' data-condition="'. esc_attr( $value['condition'] ) .'"';
				$operator = empty( $condition ) ? '' : ' data-operator="'. esc_attr( $value['operator'] ) .'"';
				
				if ($value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != "group" && $value['type'] != "content" && $value['type'] != "html" && $value['type'] != "info" && $value['type'] != "uniq_id" && $value['type'] != 'hidden') {
					$output .= '<div data-type="'.$value['type'].'"'.(isset($value['id'])?' data-id="'.esc_attr( $value['id'] ).'"':'').( $condition ).( $operator ).' id="'.esc_attr( $id ).'" class="'.esc_attr( $class ).'"'.(isset($value['margin']) && $value['margin'] != ""?" style='margin:".$value['margin']."'":"").'>';
					$output .= '<div class="name-with-desc">';
					if (isset($value['name'])) {
						$output .= '<h4 class="heading">'.$value['name'].'</h4>';
					}
					$output .= '<div class="option">';
					if ($value['type'] != "heading" && $value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != "group" && $value['type'] != "info" && $value['type'] != "uniq_id" && $value['type'] != "content" && $value['type'] != "html" && $value['type'] != 'hidden') {
						if ($value['type'] == "checkbox" && $explain_value != "") {
							$output .= '<label class="explain explain-checkbox" for="'.$field_id.'">'.wp_kses($explain_value,$allowedtags).'</label>';
						}else if ($value['type'] == "checkbox" && $explain_value != "") {
							$output .= '<div class="explain">'.wp_kses($explain_value,$allowedtags).'</div>';
						}else if ($explain_value != "") {
							$output .= '<div class="explain framework_help"><div>'.wp_kses($explain_value,$allowedtags).'</div></div>';
						}
					}
					if ( $value['type'] != 'editor' && $value['type'] != 'upload' && $value['type'] != 'background' && $value['type'] != 'sidebar' && $value['type'] != 'badges' && $value['type'] != 'coupons' && $value['type'] != 'roles' ) {
						$output .= '</div></div><div class="controls'.(isset($value['limit-height'])?' limit-height':'').'">';
					}else if ( $value['type'] == 'upload' || $value['type'] == 'background' ) {
						$output .= '</div></div><div class="controls controls-upload">';
					}else if ( $value['type'] == 'sidebar' ) {
						$output .= '</div></div><div class="controls controls-sidebar">';
					}else if ( $value['type'] == 'badges' ) {
						$output .= '</div></div><div class="controls controls-badges">';
					}else if ( $value['type'] == 'coupons' ) {
						$output .= '</div></div><div class="controls controls-coupons">';
					}else if ( $value['type'] == 'roles' ) {
						$output .= '</div></div><div class="controls controls-role">';
					}else {
						$output .= '</div></div><div>';
					}
				}
			}

			if (has_filter('framework_'.$value['type'])) {
				$output .= apply_filters('framework_'.$value['type'],$option_name,$value,$val);
			}
			
			if (isset($field_name) && isset($value['type'])) {
				$val = apply_filters('framework_'.$value['type'].'_'.$field_name,$val);
			}
			
			if (isset($value['type'])) {
				$output = apply_filters('framework_'.$value['type'].'_field',$output,$value,$val,(isset($option_name)?$option_name:""),(isset($field_name)?$field_name:""),(isset($field_id)?$field_id:""));
			}
			switch ( $value['type'] ) {
			// Content
			case 'content':
				if ( isset( $value['content'] ) ) {
					$output .= '<div class="'.esc_attr( $class ).'" id="'.(isset($value['id']) && $value['id'] != ""?$value['id']:"").'" '.( $condition ).( $operator ).'>'.$value['content'].'</div>';
				}
				break;

			// HTML
			case 'html':
				if ( isset( $value['html'] ) ) {
					$output .= '<div class="'.esc_attr( $class ).'" id="'.(isset($value['id']) && $value['id'] != ""?$value['id']:"").'" '.( $condition ).( $operator ).'>'.$value['html'].'</div>';
				}
				break;
			
			// Hidden input
			case 'hidden':
				$output .= '<input name="'.$field_name.'" type="hidden" value="' . esc_attr( $val ) . '">';
				break;
			
			// Text input
			case 'text':
				$output .= '<input'.($field_id != ''?' id="'.$field_id.'"':'').' class="framework-input framework-form-control'.(isset($value['copy'])?' framework-input-copy':'').'" name="'.$field_name.'" type="text" value="' . esc_attr( $val ) . '"'.(isset($value['readonly'])?' readonly':'').'>';
				if (isset($value['copy'])) {
					$output .= '<span class="dashicons dashicons-admin-page framework-icon-copy tooltip_s" data-title="'.esc_html__("Copy","wpqa").'"></span>';
				}
				break;

			// Date input
			case 'date':
				$output .= '<input readonly="readonly" id="'.$field_id.'" class="framework-input framework-form-control framework-date"'.(isset($value['js']) && $value['js'] != ""?" data-js='".json_encode($value['js'])."'":"").' name="'.$field_name.'" type="text" value="' . esc_attr( $val ) . '">';
				break;
			
			// Password input
			case 'password':
				$output .= '<input id="'.$field_id.'" class="framework-input framework-form-control framework-password-input" name="'.$field_name.'" type="text" value="' . esc_attr( $val ) . '">';
				break;

			// Textarea
			case 'textarea':
				$rows = '8';

				if ( isset( $value['settings']['rows'] ) ) {
					$custom_rows = $value['settings']['rows'];
					if ( is_numeric( $custom_rows ) ) {
						$rows = $custom_rows;
					}
				}

				$val = stripslashes( $val );
				$output .= '<textarea id="'.$field_id.'" class="framework-input framework-form-control" name="'.$field_name.'" rows="' . $rows . '">' . esc_textarea( $val ) . '</textarea>';
				break;
			
			// Select custom additions
			case 'custom_addition':
				if (isset($value['addto']) && $value['addto'] != "") {
					$field_id = $value['addto'];
				}else {
					$field_id = $value['id'];
				}
				$field_type = (isset($value['addition'])?$value['addition']:'cat');
				if (isset($value['options'])) {
					$select_options = '<select id="">';
					foreach ($value['options'] as $key_options => $value_options) {
						$select_options .= '<option value="'.$key_options.'">'.$value_options.'</option>';
					}
					$select_options .= '</select>';
				}else {
					$select_options = wp_dropdown_categories(array(
						'taxonomy'          => (isset($value['taxonomy']) && $value['taxonomy'] != ""?$value['taxonomy']:wpqa_question_categories),
					    'orderby'           => 'name',
					    'echo'              => 0,
					    'hide_empty'        => 0,
					    'hierarchical'      => 1,
					    'id'                => (isset($field_id) && $field_id != ""?$field_id:""),
					    'name'              => "",
					    'show_option_none'  => (isset($value['show_option']) && $value['show_option'] != ""?esc_html($value['show_option']):esc_html__('Show Categories','wpqa')),
					    'option_none_value' => (isset($value['option_none']) && $value['option_none'] != ""?esc_html($value['option_none']):0),
					));
				}
				$output .= '
				<div class="styled-select">'.$select_options.'</div>
				<div class="addition_tabs">';
					if (empty($value['addto'])) {
						$output .= '<ul id="'.(isset($field_id) && $field_id != ""?$field_id:"").'-ul" class="sort-sections sort-sections-ul">';
							$i = 0;
							if (isset($val) && is_array($val)) {
								foreach ($val as $key_a => $value_a) {
									if (isset($value['values'])) {
										$object = $value['values'];
										$object_name = $object[$value_a];
									}else {
										$object = get_term_by('id',$value_a,(isset($value['taxonomy']) && $value['taxonomy'] != ""?$value['taxonomy']:wpqa_question_categories));
										$object_name = $object->name;
									}
									$i++;
									$output .= '<li class="li class="additions-li"" id="'.(isset($field_id) && $field_id != ""?$field_id:"").'_additions_li_'.$value_a.'"><div class="widget-head">
										<span>'.((isset($value['option_none']) && $value['option_none'] != "" && $value_a == $value['option_none']) || $value_a == "0"?esc_html__('All Categories','wpqa'):$object_name).'</span></div><input name="'.(isset($field_id) && $field_id != ""?$field_id:"").'['.$field_type.'-'.$value_a.']" value="'.$value_a.'" type="hidden">
										<div>
											<a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a>
											<a class="del-builder-item"><span class="dashicons dashicons-trash"></span></a>
										</div>
									</li>';
								}
							}
						$output .= '</ul>';
					}
				$output .= '</div>
				<div class="clear"></div>
				<div class="add-item add-item-2 add-item-6 add-item-7" data-type="'.$field_type.'" data-toadd="'.(isset($value['toadd']) && $value['toadd'] != ""?$value['toadd']:"").'" data-addto="'.(isset($field_id) && $field_id != ""?$field_id:"").'" data-id="'.(isset($field_id) && $field_id != ""?$field_id:"").'_additions" data-name="'.(isset($field_id) && $field_id != ""?esc_attr(($page == 'widgets'?$post_term->get_field_name($value['id']):($page == 'post' || $page == 'term' || $page == 'user'?$field_id:$option_name.'['.$field_id.']'))):"").'">'.(isset($value["button"])?$value["button"]:esc_html__("Add category","wpqa")).'</div>
				<div class="clear"></div>';
				break;
			
			// Select category
			case 'select_category':
				if (isset($value['selected']) && $value['selected'] == "s_f_category") {
					$category = current(wp_get_object_terms($post_term,wpqa_question_categories));
					if (!isset($category->name)) $category = '';
				}
				$output .= '<div class="styled-select">'.
					wp_dropdown_categories(array(
						'show_option_none'  => (isset($value['option_none']) && $value['option_none'] != ""?$value['option_none']:0),
					    'orderby'           => 'name',
					    'hide_empty'        => 0,
					    'hierarchical'      => 1,
					    'echo'              => 0,
					    'class'             => (isset($value['class']) && $value['class'] != ""?$value['class']:""),
					    'name'              => $field_name,
					    'id'                => $field_id,
					    'selected'          => (isset($category->term_id) && $category->term_id != ""?$category->term_id:(isset($val) && $val != ""?$val:"")),
					    'taxonomy'          => (isset($value['taxonomy']) && $value['taxonomy'] != ""?$value['taxonomy']:"category")
					)).
				"</div>";
				break;
			
			// Multicheck category
			case 'multicheck_category':
				$output .= '<div class="framework_checklist framework_scroll"><ul class="categorychecklist framework_categorychecklist">'.
				wpqa_categories_checklist_admin(array("taxonomy" => (isset($value['taxonomy']) && $value['taxonomy'] != ""?$value['taxonomy']:"category"),"id" => $field_id,"name" => $field_name,"selected_cats" => (isset($val) && is_array($val)?$val:""))).
				'</ul></div>';
				break;
			
			// Slider
			case 'sliderui':
				$output .= wpqa_option_sliderui($value['min'],$value['max'],$value['step'],(isset($value['edit']) && $value['edit'] != ""?$value['edit']:""),$val,$field_id,$field_name);
				break;
			
			// Sections
			case 'sections':
				$output .= '<ul id="'.$value['id'].'" class="sort-sections">';
					$order_sections_li = $val;
					if (empty($order_sections_li)) {
						$order_sections_li = array(1 => "author",2 => "next_previous",3 => "advertising",4 => "related",5 => "comments");
					}
					$order_sections = $order_sections_li;
					$i = 0;
					
					$array_not_found = array("next_previous","advertising","author","related","comments");
					foreach ($array_not_found as $key_not => $value_not) {
						if (!in_array($value_not,$order_sections)) {
							array_push($order_sections,$value_not);
						}
					}
					
					foreach ($order_sections as $key_r => $value_r) {
						$i++;
						if ($value_r == "") {
							unset($order_sections[$key_r]);
						}else {
							$output .= '<li id="'.esc_attr($value_r).'">
								<div class="widget-head">
									<span>';
										if ($value_r == "next_previous") {
											$output .= esc_html__("Next and Previous articles","wpqa");
										}else if ($value_r == "advertising") {
											$output .= esc_html__("Advertising","wpqa");
										}else if ($value_r == "author") {
											$output .= esc_html__("About the author","wpqa");
										}else if ($value_r == "related") {
											$output .= esc_html__("Related articles","wpqa");
										}else if ($value_r == "comments") {
											$output .= esc_html__("Comments","wpqa");
										}
									$output .= '</span>
									<a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a>
								</div>
								<input name="'.esc_attr( $option_name . '[' . $value['id'] . ']['.esc_attr($i).']' ).'" value="';if ($value_r == "next_previous") {$output .= esc_attr("next_previous");}else if ($value_r == "advertising") {$output .= esc_attr("advertising");}else if ($value_r == "author") {$output .= esc_attr("author");}else if ($value_r == "related") {$output .= esc_attr("related");}else if ($value_r == "comments") {$output .= esc_attr("comments");}$output .= '" type="hidden">
							</li>';
						}
					}
				$output .= '</ul>';
				break;
			
			// Sort
			case 'sort':
				$output .= '<ul id="'.$value['id'].'" class="sort-sections sort-sections-ul">';
					$sort_sections = $val;
					if (empty($sort_sections) || (count($sort_sections) <> count($value['options']))) {
						if (isset($value['merge']) && !empty($value['merge']) && is_array($value['merge'])) {
							foreach ($value['merge'] as $key_merge => $value_merge) {
								$sort_sections = (!in_array($value_merge,$sort_sections)?array_merge($sort_sections,array($value_merge)):$sort_sections);
							}
						}
					}else {
						if (isset($value['merge']) && !empty($value['merge']) && is_array($value['merge'])) {
							foreach ($value['merge'] as $key_merge => $value_merge) {
								$sort_sections = (!in_array($value_merge,$sort_sections)?array_merge($sort_sections,array($value_merge)):$sort_sections);
							}
						}
					}
					$i = 0;
					
					$array_not_found = $value['options'];
					foreach ($array_not_found as $key_not => $value_not) {
						if (!in_array($value_not,$sort_sections) && !array_key_exists('default',$value_not)) {
							array_push($sort_sections,$value_not);
						}
					}
					
					if (isset($sort_sections) && is_array($sort_sections)) {
						foreach ($sort_sections as $key => $value_for) {
							$i++;
							$output .= '<li id="elements_'.$value['id'].'_'.esc_attr($i).'">
								<div class="widget-head"><span>'.ucfirst(isset($value_for["name"]) && is_array($value_for["name"]) && isset($value_for["name"]["value"])?esc_attr($value_for["name"]["value"]):esc_attr($value_for["name"])).'</span><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a>'.(isset($value["delete"]) && $value["delete"] == "yes" && isset($value_for['getthe']) && $value_for['getthe'] != ""?'<a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a>':'').'</div>';
								if (isset($value_for['getthe']) && $value_for['getthe'] != "") {
									$output .= '<div class="widget-content">';
								}
								
								foreach ($value_for as $key_a => $value_a) {
									if ($key_a != "getthe" && isset($value_for['getthe']) && $value_for['getthe'] != "") {
										$output .= '<h4>'.$key_a.'</h4>';
									}
									if (is_array($value_for[$key_a]) && array_key_exists("type",$value_for[$key_a]) && $value_for[$key_a]["type"] != "" && $value_for[$key_a]["type"] != "text" && $key_a != "getthe") {
										if ($value_for[$key_a]["type"] == "textarea") {
											$output .= '<textarea name="'.esc_attr( $field_name.'['.esc_attr($i).']['.$key_a.'][value]' ).'" rows="8" class="framework-input framework-form-control">'.$value_for[$key_a]["value"].'</textarea>';
										}
									}else {
										$output .= '<input name="'.esc_attr( $field_name.'['.esc_attr($i).']['.$key_a.']'.(isset($value_for["default"]) && $value_for["default"] == "yes"?"":"[value]") ).'" value="'.(isset($value_for[$key_a]) && is_array($value_for[$key_a])?esc_attr($value_for[$key_a]["value"]):esc_attr($value_for[$key_a])).'" type="'.($key_a != "getthe" && isset($value_for['getthe']) && $value_for['getthe'] != ""?"text":"hidden").'">';
									}
									if (!isset($value_for["default"]) && $key_a != "getthe") {
										$output .= '<input name="'.esc_attr( $field_name.'['.esc_attr($i).']['.$key_a.']'.(isset($value_for["default"]) && $value_for["default"] == "yes"?"":"[value]") ).'" value="'.(isset($value_for[$key_a]) && is_array($value_for[$key_a])?esc_attr($value_for[$key_a]["value"]):esc_attr($value_for[$key_a])).'" type="'.($key_a != "getthe" && isset($value_for['getthe']) && $value_for['getthe'] != ""?"text":"hidden").'">';
									}
									if ($key_a != "getthe" && $key_a != "default" && empty($value_for["default"])) {
										$output .= '<input name="'.esc_attr( $field_name.'['.esc_attr($i).']['.$key_a.']'.(isset($value_for["default"]) && $value_for["default"] == "yes"?"":"[type]") ).'" value="'.(isset($value_for["name"]) && is_array($value_for["name"])?esc_attr($value_for["name"]["type"]):"text").'" type="hidden">';
									}
								}
								if (isset($value_for['getthe']) && $value_for['getthe'] != "") {
									$output .= '</div';
								}
							$output .= '</li>';
						}
					}
				$output .= '</ul>';
				break;
			
			// Elements
			case 'elements':
				if ($page == "widgets") {
					$field_widget_id = "widget-".$post_term->id_base."[".$post_term->number."]";
				}
				$output .= '<div class="all_elements">
					<ul class="sort-sections not-sort not-add-item '.(!isset($value['not_hide']) && isset($value['hide']) && $value['hide'] == "yes"?"framework_hidden":"framework_not_hidden").'"'.(isset($value['addto']) && $value['addto'] != ""?" data-to='".$value['addto']."'":"").'>
						<li>';
							if (isset($value["title"]) && $value["title"] != "") {
								$output .= '<a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a>';
							}else {
								$output .= '<div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a></div>';
							}
							$output .= '<div class="widget-content">';
								foreach ($value['options'] as $key_e => $value_e) {
									$class = 'section '.(isset($value['not_hide']) && is_array($value['not_hide']) && !empty($value['not_hide']) && isset($value_e['id']) && !in_array($value_e['id'],$value['not_hide']) && isset($value['hide']) && $value['hide'] == "yes"?"section_hidden_all":"section_not_hidden_all");
									$wrap_class = 'wrap_class';
									$options_group = 'options-group';
									if ( isset( $value_e['type'] ) ) {
										$class .= ' section-'.$value_e['type'].' framework-form-'.$value_e['type'];
									}
									if ( isset( $value_e['class'] ) ) {
										$class .= ' '.$value_e['class'];
									}
									
									if ( ! array_key_exists( 'operator', $value_e ) || ! in_array( $value_e['operator'], array( 'and', 'or' ) ) ) {
										$value_e['operator'] = 'and';
									}
					
									if ( ! array_key_exists( 'condition', $value_e ) || ! is_string( $value_e['condition'] ) ) {
										$value_e['condition'] = '';
									}
									
									$condition = empty( $value_e['condition'] ) ? '' : ' data-condition="'. esc_attr( $value_e['condition'] ) .'"';
									$operator = empty( $condition ) ? '' : ' data-operator="'. esc_attr( $value_e['operator'] ) .'"';
									
									if ($value_e["type"] != "heading-2" && $value_e['type'] != "heading-3" && $value_e['type'] != "group" && $value_e['type'] != "info" && $value_e['type'] != "uniq_id") {
										$output .= '<div data-attr="'.$value_e['id'].'" data-type="'.$value_e["type"].'" '.( $condition ).( $operator ).' class="'.esc_attr( $class ).'">'.(isset($value_e["name"]) && $value_e["name"] != ''?'<div class="name-with-desc"><h4 class="heading">'.$value_e["name"].'</h4></div>':'').
										'<div class="all-option">';
									}
										if ($value_e["type"] == "images") {
											$output .= '<div class="image_element">'.
												wpqa_option_images($field_id,'','',$value_e["options"],$value_e["std"],'',$option_name,'no',$value_e["id"]).
											'</div>';
										}else if ($value_e["type"] == "upload") {
											$output .= "<div class='controls controls-upload'>".wpqa_options_uploader($value_e["id"],"",null,$value_e["id"],"no",array(),$page,$post_term,$option_name)."</div>";
										}else if ($value_e["type"] == "select_category") {
											if (isset($value_e['selected']) && $value_e['selected'] == "s_f_category") {
												$category = current(wp_get_object_terms($post_term,wpqa_question_categories));
												if (!isset($category->name)) $category = '';
											}
											$output .= '<div class="styled-select" data-attr="'.$value_e["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'">'.
												wp_dropdown_categories(array(
													'show_option_none' => (isset($value_e['option_none']) && $value_e['option_none'] != ""?$value_e['option_none']:0),
												    'orderby'          => 'name',
												    'hide_empty'       => 0,
												    'hierarchical'     => 1,
												    'echo'             => 0,
												    'name'             => "",
												    'id'               => "",
												    'class'            => "check-parent-class".(isset($value_e['class']) && $value_e['class'] != ""?" ".$value_e['class']:""),
												    'selected'         => (isset($category->term_id) && $category->term_id != ""?$category->term_id:""),
												    'taxonomy'         => (isset($value_e['taxonomy']) && $value_e['taxonomy'] != ""?$value_e['taxonomy']:"category")
												)).
											"</div>";
										}else if ($value_e["type"] == "select") {
											$output .= '<div class="styled-select"><select data-attr="'.$value_e["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'" class="framework-input framework-form-control" '.(isset($value_e['multiple']) && $value_e['multiple'] != ""?"multiple":"").'>
											'.(isset($value_e['first'])?'<option>'.$value_e['first'].'</option>':'');
											foreach ($value_e['options'] as $key => $option ) {
												$output .= '<option'. (isset($value_e['multiple']) && $value_e['multiple'] != ""?(isset($value_e['std']) && is_array($value_e['std']) && in_array($key,$value_e['std'])?' selected="selected"':""):(isset($value_e['std'])?selected( $value_e['std'], $key, false ):"")) .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
											}
											$output .= '</select></div>';
										}else if ($value_e["type"] == "radio") {
											foreach ($value_e['options'] as $key => $option ) {
												$output .= '<div class="framework-radio-div"><input '.(isset($value_e['std'])?checked($value_e['std'], $key, false ):"").' data-attr="'.$value_e["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'" class="framework-input framework-form-control framework-radio" type="radio" value="'. esc_attr( $key ) . '"><label>' . esc_html( $option ) . '</label></div>';
											}
										}else if ($value_e["type"] == "textarea") {
											$rows = '8';
											if ( isset( $value['settings']['rows'] ) ) {
												$custom_rows = $value['settings']['rows'];
												if ( is_numeric( $custom_rows ) ) {
													$rows = $custom_rows;
												}
											}
											$output .= '<textarea data-attr="'.$value_e["id"].'" class="framework-input framework-form-control'.(isset($value_e['std'])?" framework-form-std":" framework-form-not-std").'" rows="'.$rows.'">'.(isset($value_e['std'])?stripslashes($value_e['std']):"").'</textarea>';
										}else if ($value_e["type"] == "heading-2" || $value_e['type'] == "heading-3") {
											if ( isset($value_e['end']) && $value_e['end'] == "end" ) {
												if ( isset($value_e['div']) && $value_e['div'] == "div" ) {
													$output .= '</div>';
												}else {
													$output .= '</div></div>';
												}
											}else {
												if ( isset($value_e['div']) && $value_e['div'] == "div" ) {
													$output .= '<div class="'.$wrap_class.'" id="'.(isset($value_e['id']) && $value_e['id'] != ""?"wrap_".$value_e['id']:"").'"'.( $condition ).( $operator ).'>';
												}else {
													$id_class = '';
													$id_class = ! empty($value_e['id'])?$value_e['id']:(isset($value_e['name']) && $value_e['name'] != ""?$value_e['name']:"");
													$id_class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($id_class) );
													$output .= '<div'.(isset($value_e['id'])?' id="head-'.$value_e['id'].'"':'').' class="'.$options_group.(isset($value_e['id'])?' head-group head-'.$value_e['id']:'').'"'.( $condition ).( $operator ).'>';
													if ( isset($value_e['name']) ) {
														$output .= '<h4 class="vpanel-head-2">' . esc_html( $value_e['name'] ) . '</h4>';
													}
													$output .= '<div class="framework-group-2 ' . $id_class . '">';
												}
											}
										}else if ($value_e['type'] == "group") {
											if ( isset($value_e['end']) && $value_e['end'] == "end" ) {
												$output .= '</div></div>';
											}else {
												$id_class = '';
												$id_class = ! empty($value_e['id'])?$value_e['id']:(isset($value_e['name']) && $value_e['name'] != ""?$value_e['name']:"");
												$id_class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($id_class) );
												$output .= '<div'.(isset($value_e['id'])?' id="head-'.$value_e['id'].'"':'').' class="custom-group '.$options_group.(isset($value_e['id'])?' head-group head-'.$value_e['id']:'').'"'.( $condition ).( $operator ).'>';
												if ( isset($value_e['name']) ) {
													$output .= '<h4 class="vpanel-head-2">' . esc_html( $value_e['name'] ) . '</h4>';
												}
												$output .= '<div class="framework-group-2 ' . $id_class . '">';
											}
										}else if ($value_e["type"] == "multicheck") {
											$value_option = (isset($value_e['std'])?$value_e['std']:array());
											$output .= '<ul id="'.(isset($value_e['id']) && $value_e['id'] != ""?$value_e['id']:"").'-ul"'.(isset($value_e['sort']) && $value_e['sort'] == "yes"?' class="sort-sections sort-sections-ul"':'').'>';
											if (isset($value_e['sort']) && $value_e['sort'] == "yes") {
												$k_sort = 0;
												if (isset($val) && !empty($val) && is_array($val)) {
													$value_option = $val;
												}else {
													$value_option = $value_e['options'];
												}
											}else {
												$value_option = $value_e['options'];
											}
											
											if ($value_e['options'] != $val) {
												if (isset($val) && is_array($val)) {
													foreach ($val as $key_s => $key_s) {
														if (!isset($value_e['options'][$key_s]) && !isset($val[$key_s]["cat"]) && !isset($val[$key_s]["page"]) && !isset($val[$key_s]["builder"])) {
															unset($value_option[$key_s]);
														}
													}
												}
												if (isset($value_e['options']) && is_array($value_e['options'])) {
													foreach ($value_e['options'] as $key_s => $value_s) {
														if (!isset($val[$key_s])) {
															$value_option = array_merge($value_option,array($key_s => $value_s));
														}
													}
												}
											}
											
											foreach ($value_option as $key => $option) {
												$o_option = $option;
												if (!isset($o_option["builder"])) {
													$checked = '';
													if (isset($value_e['values']) && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))) {
														$label = $value_e['values'][$option["value"]];
														$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value_e["strtolower"]) && $value_e["strtolower"] == "not"?$key:strtolower($key)));
													}else if (isset($value_e['sort']) && $value_e['sort'] == "yes") {
														$k_sort++;
														$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value_e["strtolower"]) && $value_e["strtolower"] == "not"?$key:strtolower($key)));
														if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
															if ($val[$key]["cat"] != "yes" && ($val[$option]["value"] == 0 || $val[$option]["value"] === 0)) {
																$val[$option]["value"] = "q-0";
															}
															if (is_numeric($val[$option]["value"])) {
																$label = get_term($val[$option]["value"]);
																$label = (isset($label->name)?$label->name:"");
															}else if ($val[$option]["value"] === "q-0") {
																$label = esc_html__("All Question Categories","wpqa");
															}else if ($val[$option]["value"] === "k-0") {
																$label = esc_html__("All Knowledgebase Categories","wpqa");
															}else {
																$label = esc_html__("All Categories","wpqa");
															}
														}else if (isset($val[$key]["page"]) && $val[$key]["page"] == "yes") {
															if (is_numeric($val[$option]["value"])) {
																$label = get_the_title($val[$option]["value"]);
															}
														}else {
															$label = (isset($value_e['options'][$option]["sort"])?$value_e['options'][$option]["sort"]:"");
														}
													}else {
														$label = $o_option;
														$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value_e["strtolower"]) && $value_e["strtolower"] == "not"?$key:strtolower($key)));
													}

													$id = $option_name."_".$field_id.'_1_'. $value_e["id"] . '-'. $option;
													$name = $field_name.'[1]['.$value_e["id"].']['.$option.']';

													$value_e_std = (isset($value_e['std'])?$value_e['std']:array());
													
													if ( isset($value_e_std[$option]) ) {
														if (isset($value_e['sort']) && $value_e['sort'] == "yes") {
															if (isset($value_e_std[$option]["value"])) {
																$checked = checked($value_e_std[$option]["value"], $option, false);
															}
														}else {
															if (isset($value_e_std[$option])) {
																$checked = checked($value_e_std[$option], $option, false);
															}
														}
													}

													$output .= '<li'.(isset($value_e['sort']) && $value_e['sort'] == "yes" && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))?" class='additions-li' id='".$value_e['id']."_additions_li_".$val[$key]["value"]."'":'').'>';
														if (isset($value_e['sort']) && $value_e['sort'] == "yes") {
															$output .= '<div class="widget-head"><div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a>'.((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")?'<a class="del-cat-item del-builder-item"><span class="dashicons dashicons-trash"></span></a>':'').'</div>';
															if ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")) {
																if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
																	$item_type = 'cat';
																}else {
																	$item_type = 'page';
																}
																$name_sort = (isset($value_e['id']) && $value_e['id'] != ""?esc_html(($page == 'widgets'?$post_term->get_field_name($value_e['id']):($page == 'post' || $page == 'term' || $page == 'user'?$value_e['id']:$option_name.'['.$value_e['id'].']'))):"");
																$output .= '<input value="yes" type="hidden"><input value="'.$val[$key]["value"].'" type="hidden">';
															}else {
																$output .= '<input type="hidden" value="'.esc_html( $label ).'">';
															}
														}
														if (isset($o_option["default"]) || (isset($val[$key]) && is_array($val[$key]) && (array_key_exists('cat',$val[$key]) || array_key_exists('page',$val[$key])))) {
															if (isset($o_option["default"])) {
																$output .= '<input value="'.$option.'" type="hidden">
																<input value="yes" type="hidden">';
															}
														}else {
															$output .= '<label class="switch" for="">
																<input data-attr="'.$value_e["id"].'" data-multicheck="'.$option.'" value="0" class="not-increase" type="hidden">
																<input data-attr="'.$value_e["id"].'" data-multicheck="'.$option.'" class="checkbox framework-input framework-form-control" value="'.$option.'" type="checkbox" '. $checked .'>
																<label for="'.esc_attr($id).'" data-on="'.esc_attr__("ON","wpqa").'" data-off="'.esc_attr__("OFF","wpqa").'"></label>
															</label>';
														}
														$output .= '<label for="'.esc_attr($id).'">' . esc_html( $label ) . '</label>';
														if (isset($value_e['sort']) && $value_e['sort'] == "yes") {
															$output .= '</div>';
														}
													$output .= '</li>';
												}
											}
											$output .= '</ul>';
										}else if ($value_e["type"] == "checkbox") {
											$output .= '<label class="switch" for="">
												<input data-attr="'.$value_e["id"].'" class="checkbox framework-input framework-form-control" value="on" type="checkbox" '.checked( (isset($value_e['std'])?$value_e['std']:""), "on", false).'>
												<label for="" data-on="'.esc_attr__("ON","wpqa").'" data-off="'.esc_attr__("OFF","wpqa").'"></label>
											</label>';
										}else if ($value_e["type"] == "elements") {
											$output .= '<div class="all_elements">
												<ul class="sort-sections not-sort not-add-item '.(!isset($value_e['not_hide']) && isset($value_e['hide']) && $value_e['hide'] == "yes"?"framework_hidden":"framework_not_hidden").'"'.(isset($value_e['addto']) && $value_e['addto'] != ""?" data-to='".$value_e['addto']."'":"").'>
													<li>';
														if (isset($value_e["title"]) && $value_e["title"] != "") {
															$output .= '<a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a>';
														}else {
															$output .= '<div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a></div>';
														}
														$output .= '<div class="widget-content">';
															foreach ($value_e['options'] as $key_j_e => $value_j_e) {
																$class = 'section '.(isset($value_e['not_hide']) && is_array($value_e['not_hide']) && !empty($value_e['not_hide']) && isset($value_j_e['id']) && !in_array($value_j_e['id'],$value_e['not_hide']) && isset($value_e['hide']) && $value_e['hide'] == "yes"?"section_hidden_all":"section_not_hidden_all");
																$wrap_class = 'wrap_class';
																$options_group = 'options-group';
																if ( isset( $value_j_e['type'] ) ) {
																	$class .= ' section-'.$value_j_e['type'].' framework-form-'.$value_j_e['type'];
																}
																if ( isset( $value_j_e['class'] ) ) {
																	$class .= ' '.$value_j_e['class'];
																}
																
																if ( ! array_key_exists( 'operator', $value_j_e ) || ! in_array( $value_j_e['operator'], array( 'and', 'or' ) ) ) {
																	$value_j_e['operator'] = 'and';
																}
												
																if ( ! array_key_exists( 'condition', $value_j_e ) || ! is_string( $value_j_e['condition'] ) ) {
																	$value_j_e['condition'] = '';
																}
																
																$condition = empty( $value_j_e['condition'] ) ? '' : ' data-condition="'. esc_attr( $value_j_e['condition'] ) .'"';
																$operator = empty( $condition ) ? '' : ' data-operator="'. esc_attr( $value_j_e['operator'] ) .'"';
																
																if ($value_j_e["type"] != "heading-2" && $value_j_e['type'] != "heading-3" && $value_j_e['type'] != "group" && $value_j_e['type'] != "info" && $value_j_e['type'] != "uniq_id") {
																	$output .= '<div data-attr="'.$value_j_e['id'].'" data-type="'.$value_j_e["type"].'" '.( $condition ).( $operator ).' class="'.esc_attr( $class ).'">'.(isset($value_j_e["name"]) && $value_j_e["name"] != ''?'<div class="name-with-desc"><h4 class="heading">'.$value_j_e["name"].'</h4></div>':'').
																	'<div class="all-option">';
																}
																	if ($value_j_e["type"] == "images") {
																		$output .= '<div class="image_element">'.
																			wpqa_option_images($field_id,'','',$value_j_e["options"],$value_j_e["std"],'',$option_name,'no',$value_j_e["id"]).
																		'</div>';
																	}else if ($value_j_e["type"] == "upload") {
																		$output .= "<div class='controls controls-upload'>".wpqa_options_uploader($value_j_e["id"],"",null,$value_j_e["id"],"no",array(),$page,$post_term,$option_name)."</div>";
																	}else if ($value_j_e["type"] == "select_category") {
																		if (isset($value_j_e['selected']) && $value_j_e['selected'] == "s_f_category") {
																			$category = current(wp_get_object_terms($post_term,wpqa_question_categories));
																			if (!isset($category->name)) $category = '';
																		}
																		$output .= '<div class="styled-select" data-attr="'.$value_j_e["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'">'.
																			wp_dropdown_categories(array(
																				'show_option_none' => (isset($value_j_e['option_none']) && $value_j_e['option_none'] != ""?$value_j_e['option_none']:0),
																			    'orderby'          => 'name',
																			    'hide_empty'       => 0,
																			    'hierarchical'     => 1,
																			    'echo'             => 0,
																			    'name'             => "",
																			    'id'               => "",
																			    'class'            => "check-parent-class".(isset($value_j_e['class']) && $value_j_e['class'] != ""?" ".$value_j_e['class']:""),
																			    'selected'         => (isset($category->term_id) && $category->term_id != ""?$category->term_id:""),
																			    'taxonomy'         => (isset($value_j_e['taxonomy']) && $value_j_e['taxonomy'] != ""?$value_j_e['taxonomy']:"category")
																			)).
																		"</div>";
																	}else if ($value_j_e["type"] == "select") {
																		$output .= '<div class="styled-select"><select data-attr="'.$value_j_e["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'" class="framework-input framework-form-control" '.(isset($value_j_e['multiple']) && $value_j_e['multiple'] != ""?"multiple":"").'>
																		'.(isset($value_j_e['first'])?'<option>'.$value_j_e['first'].'</option>':'');
																		foreach ($value_j_e['options'] as $key => $option ) {
																			$output .= '<option'. (isset($value_j_e['multiple']) && $value_j_e['multiple'] != ""?(isset($value_j_e['std']) && is_array($value_j_e['std']) && in_array($key,$value_j_e['std'])?' selected="selected"':""):(isset($value_j_e['std'])?selected( $value_j_e['std'], $key, false ):"")) .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
																		}
																		$output .= '</select></div>';
																	}else if ($value_j_e["type"] == "radio") {
																		foreach ($value_j_e['options'] as $key => $option ) {
																			$output .= '<div class="framework-radio-div"><input '.(isset($value_j_e['std'])?checked( $value_j_e['std'], $key, false ):"").' data-attr="'.$value_j_e["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'" class="framework-input framework-form-control framework-radio" type="radio" value="'. esc_attr( $key ) . '"><label>' . esc_html( $option ) . '</label></div>';
																		}
																	}else if ($value_j_e["type"] == "textarea") {
																		$rows = '8';
																		if ( isset( $value_j_e['settings']['rows'] ) ) {
																			$custom_rows = $value_j_e['settings']['rows'];
																			if ( is_numeric( $custom_rows ) ) {
																				$rows = $custom_rows;
																			}
																		}
																		$output .= '<textarea data-attr="'.$value_j_e["id"].'" class="framework-input framework-form-control'.(isset($value_j_e['std'])?" framework-form-std":" framework-form-not-std").'" rows="'.$rows.'">'.(isset($value_j_e['std'])?stripslashes($value_j_e['std']):"").'</textarea>';
																	}else if ($value_j_e["type"] == "heading-2" || $value_j_e['type'] == "heading-3") {
																		if ( isset($value_j_e['end']) && $value_j_e['end'] == "end" ) {
																			if ( isset($value_j_e['div']) && $value_j_e['div'] == "div" ) {
																				$output .= '</div>';
																			}else {
																				$output .= '</div></div>';
																			}
																		}else {
																			if ( isset($value_j_e['div']) && $value_j_e['div'] == "div" ) {
																				$output .= '<div class="'.$wrap_class.'" id="'.(isset($value_j_e['id']) && $value_j_e['id'] != ""?"wrap_".$value_j_e['id']:"").'"'.( $condition ).( $operator ).'>';
																			}else {
																				$id_class = '';
																				$id_class = ! empty($value_j_e['id'])?$value_j_e['id']:(isset($value_j_e['name']) && $value_j_e['name'] != ""?$value_j_e['name']:"");
																				$id_class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($id_class) );
																				$output .= '<div'.(isset($value_j_e['id'])?' id="head-'.$value_j_e['id'].'"':'').' class="'.$options_group.(isset($value_j_e['id'])?' head-group head-'.$value_j_e['id']:'').'"'.( $condition ).( $operator ).'>';
																				if ( isset($value_j_e['name']) ) {
																					$output .= '<h4 class="vpanel-head-2">' . esc_html( $value_j_e['name'] ) . '</h4>';
																				}
																				$output .= '<div class="framework-group-2 ' . $id_class . '">';
																			}
																		}
																	}else if ($value_j_e['type'] == "group") {
																		if ( isset($value_j_e['end']) && $value_j_e['end'] == "end" ) {
																			$output .= '</div></div>';
																		}else {
																			$id_class = '';
																			$id_class = ! empty($value_j_e['id'])?$value_j_e['id']:(isset($value_j_e['name']) && $value_j_e['name'] != ""?$value_j_e['name']:"");
																			$id_class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($id_class) );
																			$output .= '<div'.(isset($value_j_e['id'])?' id="head-'.$value_j_e['id'].'"':'').' class="custom-group '.$options_group.(isset($value_j_e['id'])?' head-group head-'.$value_j_e['id']:'').'"'.( $condition ).( $operator ).'>';
																			if ( isset($value_j_e['name']) ) {
																				$output .= '<h4 class="vpanel-head-2">' . esc_html( $value_j_e['name'] ) . '</h4>';
																			}
																			$output .= '<div class="framework-group-2 ' . $id_class . '">';
																		}
																	}else if ($value_j_e["type"] == "multicheck") {
																		$value_option = (isset($value_j_e['std'])?$value_j_e['std']:array());
																		$output .= '<ul id="'.(isset($value_j_e['id']) && $value_j_e['id'] != ""?$value_j_e['id']:"").'-ul"'.(isset($value_j_e['sort']) && $value_j_e['sort'] == "yes"?' class="sort-sections sort-sections-ul"':'').'>';
																		if (isset($value_j_e['sort']) && $value_j_e['sort'] == "yes") {
																			$k_sort = 0;
																			if (isset($val) && !empty($val) && is_array($val)) {
																				$value_option = $val;
																			}else {
																				$value_option = $value_j_e['options'];
																			}
																		}else {
																			$value_option = $value_j_e['options'];
																		}
																		
																		if ($value_j_e['options'] != $val) {
																			if (isset($val) && is_array($val)) {
																				foreach ($val as $key_s => $key_s) {
																					if (!isset($value_j_e['options'][$key_s]) && !isset($val[$key_s]["cat"]) && !isset($val[$key_s]["page"]) && !isset($val[$key_s]["builder"])) {
																						unset($value_option[$key_s]);
																					}
																				}
																			}
																			if (isset($value_j_e['options']) && is_array($value_j_e['options'])) {
																				foreach ($value_j_e['options'] as $key_s => $value_s) {
																					if (!isset($val[$key_s])) {
																						$value_option = array_merge($value_option,array($key_s => $value_s));
																					}
																				}
																			}
																		}
																		
																		foreach ($value_option as $key => $option) {
																			$o_option = $option;
																			if (!isset($o_option["builder"])) {
																				$checked = '';
																				if (isset($value_j_e['values']) && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))) {
																					$label = $value_j_e['values'][$option["value"]];
																					$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value_j_e["strtolower"]) && $value_j_e["strtolower"] == "not"?$key:strtolower($key)));
																				}else if (isset($value_j_e['sort']) && $value_j_e['sort'] == "yes") {
																					$k_sort++;
																					$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value_j_e["strtolower"]) && $value_j_e["strtolower"] == "not"?$key:strtolower($key)));
																					if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
																						if ($val[$key]["cat"] != "yes" && ($val[$option]["value"] == 0 || $val[$option]["value"] === 0)) {
																							$val[$option]["value"] = "q-0";
																						}
																						if (is_numeric($val[$option]["value"])) {
																							$label = get_term($val[$option]["value"]);
																							$label = (isset($label->name)?$label->name:"");
																						}else if ($val[$option]["value"] === "q-0") {
																							$label = esc_html__("All Question Categories","wpqa");
																						}else if ($val[$option]["value"] === "k-0") {
																							$label = esc_html__("All Knowledgebase Categories","wpqa");
																						}else {
																							$label = esc_html__("All Categories","wpqa");
																						}
																					}else if (isset($val[$key]["page"]) && $val[$key]["page"] == "yes") {
																						if (is_numeric($val[$option]["value"])) {
																							$label = get_the_title($val[$option]["value"]);
																						}
																					}else {
																						$label = (isset($value_j_e['options'][$option]["sort"])?$value_j_e['options'][$option]["sort"]:"");
																					}
																				}else {
																					$label = $o_option;
																					$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value_j_e["strtolower"]) && $value_j_e["strtolower"] == "not"?$key:strtolower($key)));
																				}

																				$id = $option_name."_".$field_id.'_1_'. $value_j_e["id"] . '-'. $option;
																				$name = $field_name.'[1]['.$value_j_e["id"].']['.$option.']';

																				$value_j_e_std = (isset($value_j_e['std'])?$value_j_e['std']:array());

																				if ( isset($value_j_e_std[$option]) ) {
																					if (isset($value_j_e['sort']) && $value_j_e['sort'] == "yes") {
																						if (isset($value_j_e_std[$option]["value"])) {
																							$checked = checked($value_j_e_std[$option]["value"], $option, false);
																						}
																					}else {
																						if (isset($value_j_e_std[$option])) {
																							$checked = checked($value_j_e_std[$option], $option, false);
																						}
																					}
																				}
																				
																				$output .= '<li'.(isset($value_j_e['sort']) && $value_j_e['sort'] == "yes" && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))?" class='additions-li' id='".$value_j_e['id']."_additions_li_".$val[$key]["value"]."'":'').'>';
																					if (isset($value_j_e['sort']) && $value_j_e['sort'] == "yes") {
																						$output .= '<div class="widget-head"><div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a>'.((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")?'<a class="del-cat-item del-builder-item"><span class="dashicons dashicons-trash"></span></a>':'').'</div>';
																						if ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")) {
																							if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
																								$item_type = 'cat';
																							}else {
																								$item_type = 'page';
																							}
																							$name_sort = (isset($value_j_e['id']) && $value_j_e['id'] != ""?esc_html(($page == 'widgets'?$post_term->get_field_name($value_j_e['id']):($page == 'post' || $page == 'term' || $page == 'user'?$value_j_e['id']:$option_name.'['.$value_j_e['id'].']'))):"");
																							$output .= '<input value="yes" type="hidden"><input value="'.$val[$key]["value"].'" type="hidden">';
																						}else {
																							$output .= '<input type="hidden" value="'.esc_html( $label ).'">';
																						}
																					}
																					if (isset($o_option["default"]) || (isset($val[$key]) && is_array($val[$key]) && (array_key_exists('cat',$val[$key]) || array_key_exists('page',$val[$key])))) {
																						if (isset($o_option["default"])) {
																							$output .= '<input value="'.$option.'" type="hidden">
																							<input value="yes" type="hidden">';
																						}
																					}else {
																						$output .= '<label class="switch" for="">
																							<input data-attr="'.$value_j_e["id"].'" data-multicheck="'.$option.'" value="0" class="not-increase" type="hidden">
																							<input data-attr="'.$value_j_e["id"].'" data-multicheck="'.$option.'" class="checkbox framework-input framework-form-control" value="'.$option.'" type="checkbox" '. $checked .'>
																							<label for="'.esc_attr($id).'" data-on="'.esc_attr__("ON","wpqa").'" data-off="'.esc_attr__("OFF","wpqa").'"></label>
																						</label>';
																					}
																					$output .= '<label for="'.esc_attr($id).'">' . esc_html( $label ) . '</label>';
																					if (isset($value_j_e['sort']) && $value_j_e['sort'] == "yes") {
																						$output .= '</div>';
																					}
																				$output .= '</li>';
																			}
																		}
																		$output .= '</ul>';
																	}else if ($value_j_e["type"] == "checkbox") {
																		$output .= '<label class="switch" for="">
																			<input data-attr="'.$value_j_e["id"].'" class="checkbox framework-input framework-form-control" value="on" type="checkbox" '.checked( (isset($value_j_e['std'])?$value_j_e['std']:""), "on", false).'>
																			<label for="" data-on="'.esc_attr__("ON","wpqa").'" data-off="'.esc_attr__("OFF","wpqa").'"></label>
																		</label>';
																	}else if ($value_j_e["type"] == "info") {
																		$output .= '<div data-type="'.$value_j_e['type'].'"'.(isset($value_j_e['id'])?' data-id="'.esc_attr( $value_j_e['id'] ).'"':"").( $condition ).( $operator ).(isset($value_j_e['id'])?' id="'.esc_attr( $value_j_e['id'] ).'"':'').' class="'.esc_attr( $class ).'">';
																		if ( isset($value_j_e['name']) ) {
																			$output .= '<div class="alert-message'.(isset($value_j_e['alert']) && $value_j_e['alert'] != ""?" ".$value_j_e['alert']:"").'"><p><span>' . $value_j_e['name'] . '</span></p></div>';
																		}
																		if ( isset( $value_j_e['desc'] ) ) {
																			$output .= apply_filters('framework_sanitize_info', $value_j_e['desc'] );
																		}
																		$output .= '</div>';
																	}else {
																		if ($value_j_e["type"] == "slider") {
																			$output .= '<div class="section-sliderui">';
																		}
																		$output .= '<input class="'.(isset($value_j_e['std'])?"framework-form-std":"framework-form-not-std").($value_j_e["type"] == "color"?" framework-colors":"").($value_j_e["type"] == "date"?" builder-datepicker":"").($value_j_e["type"] == "slider"?" mini":"").'"'.($value_j_e["type"] == "date" && isset($value_j_e['js']) && $value_j_e['js'] != ""?" data-js='".json_encode($value_j_e['js'])."'":"").(isset($value_j_e['title']) && $value_j_e['title'] != ""?" data-title='".$value_j_e['title']."'":"").' value="'.(isset($value_j_e['value']) && $value_j_e['value'] != ""?$value_j_e['value']:(isset($value_j_e['std'])?esc_attr($value_j_e['std']):"")).'" data-attr="'.$value_j_e["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'" data-value="'.(isset($value_j_e['value']) && $value_j_e['value'] != ""?$value_j_e['value']:"").'" type="'.($value_j_e["type"] == "hidden" || $value_j_e["type"] == "hidden_id" || $value_j_e["type"] == "uniq_id"?"hidden":"text").'">';
																		if ($value_j_e["type"] == "slider") {
																			$data = 'data-id="slider-id" data-val="'.$value_j_e['value'].'" data-min="'.$value_j_e['min'].'" data-max="'.$value_j_e['max'].'" data-step="'.$value_j_e['step'].'"';
																			$output .= '<div id="slider-id-slider" class="v_slidersui" '. $data .'></div></div>';
																		}
																	}
																	if (isset($value_j_e['addto']) && $value_j_e['addto'] != "") {
																		$output .= '<input data-attr="'.$value_j_e['id'].'][type" value="'.$value_j_e["type"].'" type="hidden">';
																	}
																if ($value_j_e["type"] != "heading-2" && $value_j_e['type'] != "heading-3" && $value_j_e['type'] != "group" && $value_j_e['type'] != "info" && $value_j_e['type'] != "uniq_id") {
																	$output .= '</div></div>';
																}
															}
														$output .= '</div>
													</li>
												</ul>
											</div>
											<ul class="sort-sections sort-sections-with sort-sections-ul sort-sections-elements"></ul>
											<div class="clear"></div>';
											if ($page == "widgets") {
												$field_widget_name = $field_widget_id."[".$value_e['id']."]";
											}
											$output .= '<input'.($page == "post" || $page == "term" || $page == "user" || $page == "widgets"?" data-class='no_theme_options'":" data-options='".$option_name."'").' data-attr="'.$value_e["id"].'" class="add_element'.($page == "post" || $page == "term" || $page == "user" || $page == "widgets"?" no_theme_options":"").(isset($value_e['addto']) && $value_e['addto'] != ""?" add_element_to":"").'" type="button" value="'.(isset($value_e['button']) && $value_e['button'] != ""?$value_e['button']:esc_html__("+ Add a new element","wpqa")).'"'.(isset($field_id) && $field_id != ""?" data-id='".$field_id.'_1_'.$value_e['id']."' data-name='[".$field_id.'][1]['.$value_e['id']."]' data-first='".$field_id."' data-second='".$value_e['id']."'":"").(isset($field_widget_name) && $field_widget_name != ""?" data-widget='".$field_widget_name."' data-widget-id='".$field_widget_id."' data-first-widget='".$value_e["id"]."'":"").(isset($value_e['title']) && $value_e['title'] != ""?" data-title='".$value_e['title']."'":"").'>
											<span data-js="1" class="elements_elements_js '.$field_id.'_1_'.$value_e['id'].'_j"></span>';
										}else if ($value_e["type"] == "info") {
											$output .= '<div data-type="'.$value_e['type'].'"'.(isset($value_e['id'])?' data-id="'.esc_attr( $value_e['id'] ).'"':"").( $condition ).( $operator ).(isset($value_e['id'])?' id="'.esc_attr( $value_e['id'] ).'"':'').' class="'.esc_attr( $class ).'">';
											if ( isset($value_e['name']) ) {
												$output .= '<div class="alert-message'.(isset($value_e['alert']) && $value_e['alert'] != ""?" ".$value_e['alert']:"").'"><p><span>' . $value_e['name'] . '</span></p></div>';
											}
											if ( isset( $value_e['desc'] ) ) {
												$output .= apply_filters('framework_sanitize_info', $value_e['desc'] );
											}
											$output .= '</div>';
										}else {
											if ($value_e["type"] == "slider") {
												$output .= '<div class="section-sliderui">';
											}
											$input_value = (isset($value_e['value']) && $value_e['value'] != ""?$value_e['value']:(isset($value_e['std'])?esc_attr($value_e['std']):""));
											if ($value_e["type"] == "slider") {
												$input_value = (int)$input_value;
											}
											$output .= '<input class="'.(isset($value_e['std'])?"framework-form-std":"framework-form-not-std").($value_e["type"] == "color"?" framework-colors":"").($value_e["type"] == "date"?" builder-datepicker":"").($value_e["type"] == "slider"?" mini":"").'"'.($value_e["type"] == "date" && isset($value_e['js']) && $value_e['js'] != ""?" data-js='".json_encode($value_e['js'])."'":"").(isset($value_e['title']) && $value_e['title'] != ""?" data-title='".$value_e['title']."'":"").' value="'.$input_value.'" data-attr="'.(isset($value_e["id"])?$value_e["id"]:'').(isset($value['addto']) && $value['addto'] != ""?"][value":"").'" data-value="'.(isset($value_e['value']) && $value_e['value'] != ""?$value_e['value']:"").'" type="'.($value_e["type"] == "hidden" || $value_e["type"] == "hidden_id" || $value_e["type"] == "uniq_id"?"hidden":"text").'">';
											if ($value_e["type"] == "slider") {
												$data = 'data-id="slider-id" data-val="'.$value_e['value'].'" data-min="'.$value_e['min'].'" data-max="'.$value_e['max'].'" data-step="'.$value_e['step'].'"';
												$output .= '<div id="slider-id-slider" class="v_slidersui" '. $data .'></div></div>';
											}
										}
										if (isset($value['addto']) && $value['addto'] != "") {
											$output .= '<input data-attr="'.$value_e['id'].'][type" value="'.$value_e["type"].'" type="hidden">';
										}
									if ($value_e["type"] != "heading-2" && $value_e['type'] != "heading-3" && $value_e['type'] != "group" && $value_e['type'] != "info" && $value_e['type'] != "uniq_id") {
										$output .= '</div></div>';
									}
								}
							$output .= '</div>
						</li>
					</ul>
				</div>
				
				<ul class="sort-sections sort-sections-with sort-sections-ul'.(isset($val) && is_array($val) && !empty($val) && !isset($value['addto'])?'':' sort-sections-empty').'" id="'.(isset($field_id) && $field_id != ""?$field_id:"").'">';
					if (isset($val) && is_array($val) && !empty($val) && !isset($value['addto'])) {
						foreach ($val as $key_first_s => $value_first_s) {
							$output .= '<li id="elements_'.$field_id.'_'.$key_first_s.'">';
								if (isset($value["title"]) && $value["title"] != "") {
									$output .= '<div class="widget-head"><span>'.esc_html($value_first_s["name"]).'</span><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a></div>';
								}else {
									$output .= '<div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a></div>';
								}
								$output .= '<div class="widget-content">';
									foreach ($value['options'] as $key_l => $value_l) {
										$class = 'section';
										$wrap_class = 'wrap_class';
										$options_group = 'options-group';
										if ( isset( $value_l['type'] ) ) {
											$class .= ' section-'.$value_l['type'].' framework-form-'.$value_l['type'];
										}
										if ( isset( $value_l['class'] ) ) {
											$class .= ' '.$value_l['class'];
										}
										
										if ( ! array_key_exists( 'operator', $value_l ) || ! in_array( $value_l['operator'], array( 'and', 'or' ) ) ) {
											$value_l['operator'] = 'and';
										}
						
										if ( ! array_key_exists( 'condition', $value_l ) || ! is_string( $value_l['condition'] ) ) {
											$value_l['condition'] = '';
										}
										
										$condition = empty( $value_l['condition'] ) ? '' : ' data-condition="'.  str_ireplace('[%id%]', $option_name."_".$field_id."_".$key_first_s."_", esc_attr( $value_l['condition'] ))  .'"';
										$operator = empty( $condition ) ? '' : ' data-operator="'. esc_attr( $value_l['operator'] ) .'"'; 
										if ($value_l["type"] != "heading-2" && $value_l['type'] != "heading-3" && $value_l['type'] != "group" && $value_l['type'] != "info" && $value_l['type'] != "uniq_id") {
											$output .= '<div data-type="'.$value_l["type"].'" data-id="'.$option_name."_".$field_id."_".$key_first_s."_".$value_l['id'].'" id="section-'.$option_name."_".$field_id."_".$key_first_s."_".$value_l['id'].'"'.( $condition ).( $operator ).' class="'.esc_attr( $class ).'">'.(isset($value_l["name"]) && $value_l["name"] != ''?'<div class="name-with-desc"><h4 class="heading">'.$value_l["name"].'</h4></div>':'').
											'<div class="all-option">';
										}
											if ($value_l["type"] == "images") {
												$output .= '<div class="image_element">'.
												wpqa_option_images(esc_html($option_name).'_'.$field_id.'_'.$key_first_s.'_'.$value_l['id'],'','',$value_l["options"],$value_first_s[$value_l['id']],'',$field_name.'['.$key_first_s.']['.$value_l['id'].']','',$value_l["id"],'no').
												'</div>';
											}else if ($value_l["type"] == "upload") {
												$output .= "<div class='controls controls-upload'>".wpqa_options_uploader($field_id.'_'.$key_first_s.'_'.$value_l['id'],(isset($value_first_s[$value_l['id']])?$value_first_s[$value_l['id']]:""),null,$field_name.'['.$key_first_s.']['.$value_l['id'].']',null,array(),$page,$post_term,$option_name)."</div>";
											}else if ($value_l["type"] == "select_category") {
												if (isset($value_l['selected']) && $value_l['selected'] == "s_f_category") {
													$category = current(wp_get_object_terms($post_term,wpqa_question_categories));
													if (!isset($category->name)) $category = '';
												}
												$output .= '<div class="styled-select" data-attr="'.$value_l["id"].'">'.
													wp_dropdown_categories(array(
														'show_option_none' => (isset($value_l['option_none']) && $value_l['option_none'] != ""?$value_l['option_none']:0),
													    'orderby'          => 'name',
													    'hide_empty'       => 0,
													    'hierarchical'     => 1,
													    'echo'             => 0,
													    'class'            => "check-parent-class".(isset($value_l['class']) && $value_l['class'] != ""?" ".$value_l['class']:"")."",
													    'name'             => $field_name.'['.$key_first_s.']['.$value_l['id'].']',
													    'id'               => $option_name."_".$field_id.'_'.$key_first_s.'_'.$value_l['id'],
													    'selected'         => (isset($category->term_id) && $category->term_id != ""?$category->term_id:(isset($value_first_s[$value_l['id']]) && $value_first_s[$value_l['id']] != ""?$value_first_s[$value_l['id']]:"")),
													    'taxonomy'         => (isset($value_l['taxonomy']) && $value_l['taxonomy'] != ""?$value_l['taxonomy']:"category")
													)).
												"</div>";
											}else if ($value_l["type"] == "select") {
												$output .= '<div class="styled-select"><select data-attr="'.$value_l["id"].'" class="framework-input framework-form-control" '.(isset($value_l['multiple']) && $value_l['multiple'] != ""?"multiple":"").' name="'.$field_name.'['.$key_first_s.']['.$value_l['id'].']'.(isset($value_l['multiple']) && $value_l['multiple'] != ""?"[]":"").'" id="'.$option_name."_".$field_id.'_'.$key_first_s.'_'.$value_l['id'].'">
												'.(isset($value_l['first'])?'<option>'.$value_l['first'].'</option>':'');
												foreach ($value_l['options'] as $key => $option ) {
													$output .= '<option'. (isset($value_l['multiple']) && $value_l['multiple'] != ""?(isset($value_first_s[$value_l['id']]) && is_array($value_first_s[$value_l['id']]) && in_array($key,$value_first_s[$value_l['id']])?' selected="selected"':""):(isset($value_first_s[$value_l['id']])?selected( $value_first_s[$value_l['id']], $key, false ):"")) .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
												}
												$output .= '</select></div>';
											}else if ($value_l["type"] == "radio") {
												foreach ($value_l['options'] as $key => $option ) {
													$output .= '<div class="framework-radio-div"><input name="'.$field_name.'['.$key_first_s.']['.$value_l['id'].']" id="'.$option_name."_".$field_id.'_'.$key_first_s.'_'.$value_l['id'].'_'.$key.'" data-attr="'.$value_l["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'" class="framework-input framework-form-control framework-radio" type="radio" value="'. esc_attr( $key ) . '" '.(isset($value_first_s[$value_l['id']])?checked( $value_first_s[$value_l['id']], $key, false ):"").'><label for="'.$option_name."_".$field_id.'_'.$key_first_s.'_'.$value_l['id'].'_'.$key.'">' . esc_html( $option ) . '</label></div>';
												}
											}else if ($value_l["type"] == "textarea") {
												$rows = '8';
												if ( isset( $value['settings']['rows'] ) ) {
													$custom_rows = $value['settings']['rows'];
													if ( is_numeric( $custom_rows ) ) {
														$rows = $custom_rows;
													}
												}
												$output .= '<textarea data-attr="'.$value_l["id"].'" class="framework-input framework-form-control'.(isset($value_l['std'])?" framework-form-std":" framework-form-not-std").'" rows="'.$rows.'" name="'.$field_name.'['.$key_first_s.']['.$value_l['id'].']" id="'.$option_name."_".$field_id.'_'.$key_first_s.'_'.$value_l['id'].'">'.stripslashes(isset($value_first_s[$value_l['id']])?$value_first_s[$value_l['id']]:"").'</textarea>';
											}else if ($value_l["type"] == "slider") {
												$output .= '<div class="section-sliderui">'.
													wpqa_option_sliderui($value_l["min"],$value_l["max"],$value_l["step"],'',$value_first_s[$value_l['id']],$field_id.']['.$key_first_s.']['.$value_l['id'],$field_name.'['.$key_first_s.']['.$value_l['id'].']',esc_html($option_name).'_'.$field_id.'_'.$key_first_s.'_'.$value_l['id']).
												'</div>';
											}else if ($value_l["type"] == "heading-2" || $value_l['type'] == "heading-3") {
												if ( isset($value_l['end']) && $value_l['end'] == "end" ) {
													if ( isset($value_l['div']) && $value_l['div'] == "div" ) {
														$output .= '</div>';
													}else {
														$output .= '</div></div>';
													}
												}else {
													if ( isset($value_l['div']) && $value_l['div'] == "div" ) {
														$output .= '<div class="'.$wrap_class.'" id="'.(isset($value_l['id']) && $value_l['id'] != ""?"wrap_".$value_l['id']:"").'"'.( $condition ).( $operator ).'>';
													}else {
														$id_class = '';
														$id_class = ! empty($value_l['id'])?$value_l['id']:(isset($value_l['name']) && $value_l['name'] != ""?$value_l['name']:"");
														$id_class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($id_class) );
														$output .= '<div'.(isset($value_l['id'])?' id="head-'.$value_l['id'].'"':'').' class="'.$options_group.(isset($value_l['id'])?' head-group head-'.$value_l['id']:'').'"'.( $condition ).( $operator ).'>';
														if ( isset($value_l['name']) ) {
															$output .= '<h4 class="vpanel-head-2">' . esc_html( $value_l['name'] ) . '</h4>';
														}
														$output .= '<div class="framework-group-2 ' . $id_class . '">';
													}
												}
											}else if ($value_l['type'] == "group") {
												if ( isset($value_l['end']) && $value_l['end'] == "end" ) {
													$output .= '</div></div>';
												}else {
													$id_class = '';
													$id_class = ! empty($value_l['id'])?$value_l['id']:(isset($value_l['name']) && $value_l['name'] != ""?$value_l['name']:"");
													$id_class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($id_class) );
													$output .= '<div'.(isset($value_l['id'])?' id="head-'.$value_l['id'].'"':'').' class="custom-group '.$options_group.(isset($value_l['id'])?' head-group head-'.$value_l['id']:'').'"'.( $condition ).( $operator ).'>';
													if ( isset($value_l['name']) ) {
														$output .= '<h4 class="vpanel-head-2">' . esc_html( $value_l['name'] ) . '</h4>';
													}
													$output .= '<div class="framework-group-2 ' . $id_class . '">';
												}
											}else if ($value_l["type"] == "multicheck") {
												$value_option = (isset($value_l['std'])?$value_l['std']:array());
												$output .= '<ul id="'.(isset($value_l['id']) && $value_l['id'] != ""?$value_l['id']:"").'-ul"'.(isset($value_l['sort']) && $value_l['sort'] == "yes"?' class="sort-sections sort-sections-ul"':'').'>';
												if (isset($value_l['sort']) && $value_l['sort'] == "yes") {
													$k_sort = 0;
													if (isset($val) && !empty($val) && is_array($val)) {
														$value_option = $val;
													}else {
														$value_option = $value_l['options'];
													}
												}else {
													$value_option = $value_l['options'];
												}
												
												if ($value_l['options'] != $val) {
													if (isset($val) && is_array($val)) {
														foreach ($val as $key_s => $key_s) {
															if (!isset($value_l['options'][$key_s]) && !isset($val[$key_s]["cat"]) && !isset($val[$key_s]["page"]) && !isset($val[$key_s]["builder"])) {
																unset($value_option[$key_s]);
															}
														}
													}
													if (isset($value_l['options']) && is_array($value_l['options'])) {
														foreach ($value_l['options'] as $key_s => $value_s) {
															if (!isset($val[$key_s])) {
																$value_option = array_merge($value_option,array($key_s => $value_s));
															}
														}
													}
												}
												
												foreach ($value_option as $key => $option) {
													$o_option = $option;
													if (!isset($o_option["builder"])) {
														$checked = '';
														if (isset($value_l['values']) && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))) {
															$label = $value_l['values'][$option["value"]];
															$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value_l["strtolower"]) && $value_l["strtolower"] == "not"?$key:strtolower($key)));
														}else if (isset($value_l['sort']) && $value_l['sort'] == "yes") {
															$k_sort++;
															$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value_l["strtolower"]) && $value_l["strtolower"] == "not"?$key:strtolower($key)));
															if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
																if ($val[$key]["cat"] != "yes" && ($val[$option]["value"] == 0 || $val[$option]["value"] === 0)) {
																	$val[$option]["value"] = "q-0";
																}
																if (is_numeric($val[$option]["value"])) {
																	$label = get_term($val[$option]["value"]);
																	$label = (isset($label->name)?$label->name:"");
																}else if ($val[$option]["value"] === "q-0") {
																	$label = esc_html__("All Question Categories","wpqa");
																}else if ($val[$option]["value"] === "k-0") {
																	$label = esc_html__("All Knowledgebase Categories","wpqa");
																}else {
																	$label = esc_html__("All Categories","wpqa");
																}
															}else if (isset($val[$key]["page"]) && $val[$key]["page"] == "yes") {
																if (is_numeric($val[$option]["value"])) {
																	$label = get_the_title($val[$option]["value"]);
																}
															}else {
																$label = (isset($value_l['options'][$option]["sort"])?$value_l['options'][$option]["sort"]:"");
															}
														}else {
															$label = $o_option;
															$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value_l["strtolower"]) && $value_l["strtolower"] == "not"?$key:strtolower($key)));
														}

														$id = $option_name."_".$field_id.'_'.$key_first_s . '_'. $value_l["id"] . '-'. $option;
														$name = $field_name.'['.$key_first_s.']['.$value_l["id"].']['.$option.']';
														
														if ( isset($val[$key_first_s][$value_l["id"]][$option]) ) {
															if (isset($value_l['sort']) && $value_l['sort'] == "yes") {
																if (isset($val[$key_first_s][$value_l["id"]][$option]["value"])) {
																	$checked = checked($val[$key_first_s][$value_l["id"]][$option]["value"], $option, false);
																}
															}else {
																if (isset($val[$key_first_s][$value_l["id"]][$option])) {
																	$checked = checked($val[$key_first_s][$value_l["id"]][$option], $option, false);
																}
															}
														}
														$output .= '<li'.(isset($value_l['sort']) && $value_l['sort'] == "yes" && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))?" class='additions-li' id='".$value_l['id']."_additions_li_".$val[$key]["value"]."'":'').'>';
															if (isset($value_l['sort']) && $value_l['sort'] == "yes") {
																$output .= '<div class="widget-head"><div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a>'.((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")?'<a class="del-cat-item del-builder-item"><span class="dashicons dashicons-trash"></span></a>':'').'</div>';
																if ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")) {
																	if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
																		$item_type = 'cat';
																	}else {
																		$item_type = 'page';
																	}
																	$name_sort = (isset($value_l['id']) && $value_l['id'] != ""?esc_html(($page == 'widgets'?$post_term->get_field_name($value_l['id']):($page == 'post' || $page == 'term' || $page == 'user'?$value_l['id']:$option_name.'['.$value_l['id'].']'))):"");
																	$output .= '<input name="'.$name_sort.'['.$item_type.'-'.$val[$key]["value"].']['.$item_type.']" value="yes" type="hidden"><input name="'.$name_sort.'['.$item_type.'-'.$val[$key]["value"].'][value]" value="'.$val[$key]["value"].'" type="hidden">';
																}else {
																	$output .= '<input type="hidden" name="'.esc_attr( $name.'[sort]' ).'" value="'.esc_html( $label ).'">';
																}
															}
															if (isset($o_option["default"]) || (isset($val[$key]) && is_array($val[$key]) && (array_key_exists('cat',$val[$key]) || array_key_exists('page',$val[$key])))) {
																if (isset($o_option["default"])) {
																	$output .= '<input value="'.$option.'" type="hidden" name="'.esc_attr( $name.(isset($value_l['sort']) && $value_l['sort'] == "yes"?'[value]':'') ).'">
																	<input value="yes" type="hidden" name="'.esc_attr( $name.'[default]' ).'">';
																}
															}else {
																$output .= '<label class="switch" for="'.esc_attr($id).'">
																	<input value="0" type="hidden" class="not-increase" name="'.esc_attr($name.(isset($value_l['sort']) && $value_l['sort'] == "yes"?'[value]':'') ).'">
																	<input id="'.esc_attr($id).'" value="'.$option.'" class="checkbox framework-input framework-form-control" type="checkbox" name="'.esc_attr( $name.(isset($value_l['sort']) && $value_l['sort'] == "yes"?'[value]':'') ).'" '. $checked .'>
																	<label for="'.esc_attr($id).'" data-on="'.esc_attr__("ON","wpqa").'" data-off="'.esc_attr__("OFF","wpqa").'"></label>
																</label>';
															}
															$output .= '<label for="'.esc_attr($id).'">' . esc_html( $label ) . '</label>';
															if (isset($value_l['sort']) && $value_l['sort'] == "yes") {
																$output .= '</div>';
															}
														$output .= '</li>';
													}
												}
												$output .= '</ul>';
											}else if ($value_l["type"] == "checkbox") {
												$output .= '<label class="switch" for="'.$field_id.'_'.$key_first_s.'_'.$value_l['id'].'">
													<input id="'.$field_id.'_'.$key_first_s.'_'.$value_l['id'].'" class="checkbox framework-input framework-form-control" value="on" type="checkbox" name="'.$field_name.'['.$key_first_s.']['.$value_l['id'].']" '.checked( (isset($value_first_s[$value_l['id']])?$value_first_s[$value_l['id']]:""), "on", false).'>
													<label for="'.$field_id.'_'.$key_first_s.'_'.$value_l['id'].'" data-on="'.esc_attr__("ON","wpqa").'" data-off="'.esc_attr__("OFF","wpqa").'"></label>
												</label>';
											}else if ($value_l["type"] == "elements") {
												$output .= '<div class="all_elements">
													<ul class="sort-sections not-sort not-add-item '.(!isset($value_l['not_hide']) && isset($value_l['hide']) && $value_l['hide'] == "yes"?"framework_hidden":"framework_not_hidden").'"'.(isset($value_l['addto']) && $value_l['addto'] != ""?" data-to='".$value_l['addto']."'":"").'>
														<li>';
															if (isset($value_l["title"]) && $value_l["title"] != "") {
																$output .= '<a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a>';
															}else {
																$output .= '<div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a></div>';
															}
															$output .= '<div class="widget-content">';
																foreach ($value_l['options'] as $key_j_e => $value_j_l) {
																	$class = 'section '.(isset($value_l['not_hide']) && is_array($value_l['not_hide']) && !empty($value_l['not_hide']) && isset($value_j_l['id']) && !in_array($value_j_l['id'],$value_l['not_hide']) && isset($value_l['hide']) && $value_l['hide'] == "yes"?"section_hidden_all":"section_not_hidden_all");
																	$wrap_class = 'wrap_class';
																	$options_group = 'options-group';
																	if ( isset( $value_j_l['type'] ) ) {
																		$class .= ' section-'.$value_j_l['type'].' framework-form-'.$value_j_l['type'];
																	}
																	if ( isset( $value_j_l['class'] ) ) {
																		$class .= ' '.$value_j_l['class'];
																	}
																	
																	if ( ! array_key_exists( 'operator', $value_j_l ) || ! in_array( $value_j_l['operator'], array( 'and', 'or' ) ) ) {
																		$value_j_l['operator'] = 'and';
																	}
													
																	if ( ! array_key_exists( 'condition', $value_j_l ) || ! is_string( $value_j_l['condition'] ) ) {
																		$value_j_l['condition'] = '';
																	}
																	
																	$condition = empty( $value_j_l['condition'] ) ? '' : ' data-condition="'. esc_attr( $value_j_l['condition'] ) .'"';
																	$operator = empty( $condition ) ? '' : ' data-operator="'. esc_attr( $value_j_l['operator'] ) .'"';
																	
																	if ($value_j_l["type"] != "heading-2" && $value_j_l['type'] != "heading-3" && $value_j_l['type'] != "group" && $value_j_l['type'] != "info" && $value_j_l['type'] != "uniq_id") {
																		$output .= '<div data-attr="'.$value_j_l['id'].'" data-type="'.$value_j_l["type"].'" '.( $condition ).( $operator ).' class="'.esc_attr( $class ).'">'.(isset($value_j_l["name"]) && $value_j_l["name"] != ''?'<div class="name-with-desc"><h4 class="heading">'.$value_j_l["name"].'</h4></div>':'').
																		'<div class="all-option">';
																	}
																		if ($value_j_l["type"] == "images") {
																			$output .= '<div class="image_element">'.
																				wpqa_option_images($field_id,'','',$value_j_l["options"],$value_j_l["std"],'',$option_name,'no',$value_j_l["id"]).
																			'</div>';
																		}else if ($value_j_l["type"] == "upload") {
																			$output .= "<div class='controls controls-upload'>".wpqa_options_uploader($value_j_l["id"],"",null,$value_j_l["id"],"no",array(),$page,$post_term,$option_name)."</div>";
																		}else if ($value_j_l["type"] == "select_category") {
																			if (isset($value_j_l['selected']) && $value_j_l['selected'] == "s_f_category") {
																				$category = current(wp_get_object_terms($post_term,wpqa_question_categories));
																				if (!isset($category->name)) $category = '';
																			}
																			$output .= '<div class="styled-select" data-attr="'.$value_j_l["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'">'.
																				wp_dropdown_categories(array(
																					'show_option_none' => (isset($value_j_l['option_none']) && $value_j_l['option_none'] != ""?$value_j_l['option_none']:0),
																				    'orderby'          => 'name',
																				    'hide_empty'       => 0,
																				    'hierarchical'     => 1,
																				    'echo'             => 0,
																				    'name'             => "",
																				    'id'               => "",
																				    'class'            => "check-parent-class".(isset($value_j_l['class']) && $value_j_l['class'] != ""?" ".$value_j_l['class']:""),
																				    'selected'         => (isset($category->term_id) && $category->term_id != ""?$category->term_id:""),
																				    'taxonomy'         => (isset($value_j_l['taxonomy']) && $value_j_l['taxonomy'] != ""?$value_j_l['taxonomy']:"category")
																				)).
																			"</div>";
																		}else if ($value_j_l["type"] == "select") {
																			$output .= '<div class="styled-select"><select data-attr="'.$value_j_l["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'" class="framework-input framework-form-control" '.(isset($value_j_l['multiple']) && $value_j_l['multiple'] != ""?"multiple":"").'>
																			'.(isset($value_j_l['first'])?'<option>'.$value_j_l['first'].'</option>':'');
																			foreach ($value_j_l['options'] as $key => $option ) {
																				$output .= '<option'. (isset($value_j_l['multiple']) && $value_j_l['multiple'] != ""?(isset($value_j_l['std']) && is_array($value_j_l['std']) && in_array($key,$value_j_l['std'])?' selected="selected"':""):(isset($value_j_l['std'])?selected( $value_j_l['std'], $key, false ):"")) .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
																			}
																			$output .= '</select></div>';
																		}else if ($value_j_l["type"] == "radio") {
																			foreach ($value_j_l['options'] as $key => $option ) {
																				$output .= '<div class="framework-radio-div"><input '.(isset($value_j_l['std'])?checked( $value_j_l['std'], $key, false ):"").' data-attr="'.$value_j_l["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'" class="framework-input framework-form-control framework-radio" type="radio" value="'. esc_attr( $key ) . '"><label>' . esc_html( $option ) . '</label></div>';
																			}
																		}else if ($value_j_l["type"] == "textarea") {
																			$rows = '8';
																			if ( isset( $value_j_l['settings']['rows'] ) ) {
																				$custom_rows = $value_j_l['settings']['rows'];
																				if ( is_numeric( $custom_rows ) ) {
																					$rows = $custom_rows;
																				}
																			}
																			$output .= '<textarea data-attr="'.$value_j_l["id"].'" class="framework-input framework-form-control'.(isset($value_j_l['std'])?" framework-form-std":" framework-form-not-std").'" rows="'.$rows.'">'.(isset($value_j_l['std'])?stripslashes($value_j_l['std']):"").'</textarea>';
																		}else if ($value_j_l["type"] == "heading-2" || $value_j_l['type'] == "heading-3") {
																			if ( isset($value_j_l['end']) && $value_j_l['end'] == "end" ) {
																				if ( isset($value_j_l['div']) && $value_j_l['div'] == "div" ) {
																					$output .= '</div>';
																				}else {
																					$output .= '</div></div>';
																				}
																			}else {
																				if ( isset($value_j_l['div']) && $value_j_l['div'] == "div" ) {
																					$output .= '<div class="'.$wrap_class.'" id="'.(isset($value_j_l['id']) && $value_j_l['id'] != ""?"wrap_".$value_j_l['id']:"").'"'.( $condition ).( $operator ).'>';
																				}else {
																					$id_class = '';
																					$id_class = ! empty($value_j_l['id'])?$value_j_l['id']:(isset($value_j_l['name']) && $value_j_l['name'] != ""?$value_j_l['name']:"");
																					$id_class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($id_class) );
																					$output .= '<div'.(isset($value_j_l['id'])?' id="head-'.$value_j_l['id'].'"':'').' class="'.$options_group.(isset($value_j_l['id'])?' head-group head-'.$value_j_l['id']:'').'"'.( $condition ).( $operator ).'>';
																					if ( isset($value_j_l['name']) ) {
																						$output .= '<h4 class="vpanel-head-2">' . esc_html( $value_j_l['name'] ) . '</h4>';
																					}
																					$output .= '<div class="framework-group-2 ' . $id_class . '">';
																				}
																			}
																		}else if ($value_j_l['type'] == "group") {
																			if ( isset($value_j_l['end']) && $value_j_l['end'] == "end" ) {
																				$output .= '</div></div>';
																			}else {
																				$id_class = '';
																				$id_class = ! empty($value_j_l['id'])?$value_j_l['id']:(isset($value_j_l['name']) && $value_j_l['name'] != ""?$value_j_l['name']:"");
																				$id_class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($id_class) );
																				$output .= '<div'.(isset($value_j_l['id'])?' id="head-'.$value_j_l['id'].'"':'').' class="custom-group '.$options_group.(isset($value_j_l['id'])?' head-group head-'.$value_j_l['id']:'').'"'.( $condition ).( $operator ).'>';
																				if ( isset($value_j_l['name']) ) {
																					$output .= '<h4 class="vpanel-head-2">' . esc_html( $value_j_l['name'] ) . '</h4>';
																				}
																				$output .= '<div class="framework-group-2 ' . $id_class . '">';
																			}
																		}else if ($value_j_l["type"] == "multicheck") {
																			$value_option = (isset($value_j_l['std'])?$value_j_l['std']:array());
																			$output .= '<ul id="'.(isset($value_j_l['id']) && $value_j_l['id'] != ""?$value_j_l['id']:"").'-ul"'.(isset($value_j_l['sort']) && $value_j_l['sort'] == "yes"?' class="sort-sections sort-sections-ul"':'').'>';
																			if (isset($value_j_l['sort']) && $value_j_l['sort'] == "yes") {
																				$k_sort = 0;
																				if (isset($val) && !empty($val) && is_array($val)) {
																					$value_option = $val;
																				}else {
																					$value_option = $value_j_l['options'];
																				}
																			}else {
																				$value_option = $value_j_l['options'];
																			}
																			
																			if ($value_j_l['options'] != $val) {
																				if (isset($val) && is_array($val)) {
																					foreach ($val as $key_s => $key_s) {
																						if (!isset($value_j_l['options'][$key_s]) && !isset($val[$key_s]["cat"]) && !isset($val[$key_s]["page"]) && !isset($val[$key_s]["builder"])) {
																							unset($value_option[$key_s]);
																						}
																					}
																				}
																				if (isset($value_j_l['options']) && is_array($value_j_l['options'])) {
																					foreach ($value_j_l['options'] as $key_s => $value_s) {
																						if (!isset($val[$key_s])) {
																							$value_option = array_merge($value_option,array($key_s => $value_s));
																						}
																					}
																				}
																			}
																			
																			foreach ($value_option as $key => $option) {
																				$o_option = $option;
																				if (!isset($o_option["builder"])) {
																					$checked = '';
																					if (isset($value_j_l['values']) && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))) {
																						$label = $value_j_l['values'][$option["value"]];
																						$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value_j_l["strtolower"]) && $value_j_l["strtolower"] == "not"?$key:strtolower($key)));
																					}else if (isset($value_j_l['sort']) && $value_j_l['sort'] == "yes") {
																						$k_sort++;
																						$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value_j_l["strtolower"]) && $value_j_l["strtolower"] == "not"?$key:strtolower($key)));
																						if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
																							if ($val[$key]["cat"] != "yes" && ($val[$option]["value"] == 0 || $val[$option]["value"] === 0)) {
																								$val[$option]["value"] = "q-0";
																							}
																							if (is_numeric($val[$option]["value"])) {
																								$label = get_term($val[$option]["value"]);
																								$label = (isset($label->name)?$label->name:"");
																							}else if ($val[$option]["value"] === "q-0") {
																								$label = esc_html__("All Question Categories","wpqa");
																							}else if ($val[$option]["value"] === "k-0") {
																								$label = esc_html__("All Knowledgebase Categories","wpqa");
																							}else {
																								$label = esc_html__("All Categories","wpqa");
																							}
																						}else if (isset($val[$key]["page"]) && $val[$key]["page"] == "yes") {
																							if (is_numeric($val[$option]["value"])) {
																								$label = get_the_title($val[$option]["value"]);
																							}
																						}else {
																							$label = (isset($value_j_l['options'][$option]["sort"])?$value_j_l['options'][$option]["sort"]:"");
																						}
																					}else {
																						$label = $o_option;
																						$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value_j_l["strtolower"]) && $value_j_l["strtolower"] == "not"?$key:strtolower($key)));
																					}

																					$id = $option_name."_".$field_id.'_'.$key_first_s . '_'. $value_j_l["id"] . '-'. $option;
																					$name = $field_name.'['.$key_first_s.']['.$value_j_l["id"].']['.$option.']';

																					$value_j_l_std = (isset($value_j_l['std'])?$value_j_l['std']:array());

																					if ( isset($value_j_l_std[$option]) ) {
																						if (isset($value_j_l['sort']) && $value_j_l['sort'] == "yes") {
																							if (isset($value_j_l_std[$option]["value"])) {
																								$checked = checked($value_j_l_std[$option]["value"], $option, false);
																							}
																						}else {
																							if (isset($value_j_l_std[$option])) {
																								$checked = checked($value_j_l_std[$option], $option, false);
																							}
																						}
																					}
																					
																					$output .= '<li'.(isset($value_j_l['sort']) && $value_j_l['sort'] == "yes" && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))?" class='additions-li' id='".$value_j_l['id']."_additions_li_".$val[$key]["value"]."'":'').'>';
																						if (isset($value_j_l['sort']) && $value_j_l['sort'] == "yes") {
																							$output .= '<div class="widget-head"><div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a>'.((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")?'<a class="del-cat-item del-builder-item"><span class="dashicons dashicons-trash"></span></a>':'').'</div>';
																							if ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")) {
																								if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
																									$item_type = 'cat';
																								}else {
																									$item_type = 'page';
																								}
																								$name_sort = (isset($value_j_l['id']) && $value_j_l['id'] != ""?esc_html(($page == 'widgets'?$post_term->get_field_name($value_j_l['id']):($page == 'post' || $page == 'term' || $page == 'user'?$value_j_l['id']:$option_name.'['.$value_j_l['id'].']'))):"");
																								$output .= '<input value="yes" type="hidden"><input value="'.$val[$key]["value"].'" type="hidden">';
																							}else {
																								$output .= '<input type="hidden" value="'.esc_html( $label ).'">';
																							}
																						}
																						if (isset($o_option["default"]) || (isset($val[$key]) && is_array($val[$key]) && (array_key_exists('cat',$val[$key]) || array_key_exists('page',$val[$key])))) {
																							if (isset($o_option["default"])) {
																								$output .= '<input value="'.$option.'" type="hidden">
																								<input value="yes" type="hidden">';
																							}
																						}else {
																							$output .= '<label class="switch" for="">
																								<input data-attr="'.$value_j_l["id"].'" data-multicheck="'.$option.'" value="0" class="not-increase" type="hidden">
																								<input data-attr="'.$value_j_l["id"].'" data-multicheck="'.$option.'" class="checkbox framework-input framework-form-control" value="'.$option.'" type="checkbox" '. $checked .'>
																								<label for="'.esc_attr($id).'" data-on="'.esc_attr__("ON","wpqa").'" data-off="'.esc_attr__("OFF","wpqa").'"></label>
																							</label>';
																						}
																						$output .= '<label for="'.esc_attr($id).'">' . esc_html( $label ) . '</label>';
																						if (isset($value_j_l['sort']) && $value_j_l['sort'] == "yes") {
																							$output .= '</div>';
																						}
																					$output .= '</li>';
																				}
																			}
																			$output .= '</ul>';
																		}else if ($value_j_l["type"] == "checkbox") {
																			$output .= '<label class="switch" for="">
																				<input data-attr="'.$value_j_l["id"].'" class="checkbox framework-input framework-form-control" value="on" type="checkbox" '.checked( (isset($value_j_l['std'])?$value_j_l['std']:""), "on", false).'>
																				<label for="" data-on="'.esc_attr__("ON","wpqa").'" data-off="'.esc_attr__("OFF","wpqa").'"></label>
																			</label>';
																		}else if ($value_j_l["type"] == "info") {
																			$output .= '<div data-type="'.$value_j_l['type'].'"'.(isset($value_j_l['id'])?' data-id="'.esc_attr( $value_j_l['id'] ).'"':"").( $condition ).( $operator ).(isset($value_j_l['id'])?' id="'.esc_attr( $value_j_l['id'] ).'"':'').' class="'.esc_attr( $class ).'">';
																			if ( isset($value_j_l['name']) ) {
																				$output .= '<div class="alert-message'.(isset($value_j_l['alert']) && $value_j_l['alert'] != ""?" ".$value_j_l['alert']:"").'"><p><span>' . $value_j_l['name'] . '</span></p></div>';
																			}
																			if ( isset( $value_j_l['desc'] ) ) {
																				$output .= apply_filters('framework_sanitize_info', $value_j_l['desc'] );
																			}
																			$output .= '</div>';
																		}else {
																			if ($value_j_l["type"] == "slider") {
																				$output .= '<div class="section-sliderui">';
																			}
																			$input_value = (isset($value_j_l['value']) && $value_j_l['value'] != ""?$value_j_l['value']:(isset($value_j_l['std'])?esc_attr($value_j_l['std']):""));
																			if ($value_j_l["type"] == "slider") {
																				$input_value = (int)$input_value;
																			}
																			$output .= '<input class="'.(isset($value_j_l['std'])?"framework-form-std":"framework-form-not-std").($value_j_l["type"] == "color"?" framework-colors":"").($value_j_l["type"] == "date"?" builder-datepicker":"").($value_j_l["type"] == "slider"?" mini":"").'"'.($value_j_l["type"] == "date" && isset($value_j_l['js']) && $value_j_l['js'] != ""?" data-js='".json_encode($value_j_l['js'])."'":"").(isset($value_j_l['title']) && $value_j_l['title'] != ""?" data-title='".$value_j_l['title']."'":"").' value="'.$input_value.'" data-attr="'.$value_j_l["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'" data-value="'.(isset($value_j_l['value']) && $value_j_l['value'] != ""?$value_j_l['value']:"").'" type="'.($value_j_l["type"] == "hidden" || $value_j_l["type"] == "hidden_id" || $value_j_l["type"] == "uniq_id"?"hidden":"text").'">';
																			if ($value_j_l["type"] == "slider") {
																				$data = 'data-id="slider-id" data-val="'.$value_j_l['value'].'" data-min="'.$value_j_l['min'].'" data-max="'.$value_j_l['max'].'" data-step="'.$value_j_l['step'].'"';
																				$output .= '<div id="slider-id-slider" class="v_slidersui" '. $data .'></div></div>';
																			}
																		}
																		if (isset($value_j_l['addto']) && $value_j_l['addto'] != "") {
																			$output .= '<input data-attr="'.$value_j_l['id'].'][type" value="'.$value_j_l["type"].'" type="hidden">';
																		}
																	if ($value_j_l["type"] != "heading-2" && $value_j_l['type'] != "heading-3" && $value_j_l['type'] != "group" && $value_j_l['type'] != "info" && $value_j_l['type'] != "uniq_id") {
																		$output .= '</div></div>';
																	}
																}
															$output .= '</div>
														</li>
													</ul>
												</div>

												<ul class="sort-sections sort-sections-with sort-sections-ul sort-sections-elements'.(isset($val[$key_first_s][$value_l["id"]]) && is_array($val[$key_first_s][$value_l["id"]]) && !empty($val[$key_first_s][$value_l["id"]]) && !isset($value_l['addto'])?'':' sort-sections-empty').'" id="'.(isset($field_id) && $field_id != ""?$field_id.'_'.$key_first_s.'_'.$value_l['id']:"").'">';
													if (isset($val[$key_first_s][$value_l["id"]]) && is_array($val[$key_first_s][$value_l["id"]]) && !empty($val[$key_first_s][$value_l["id"]]) && !isset($value_l['addto'])) {
														foreach ($val[$key_first_s][$value_l["id"]] as $key_second_s => $value_second_s) {
															$field_name_second = ($field_name != ''?$field_name.'[':'').$key_first_s.($field_name != ''?']':'').'['.$value_l["id"].']['.$key_second_s.']['.$value_j_l["id"].']';
															if ($page == "widgets") {
																$field_name_second = "widget-".$post_term->id_base."[".$post_term->number."][".$value_l["id"].']['.$key_second_s.']['.$value_j_l["id"]."]";
															}
															$id_second = ($option_name != ''?$option_name.'_':'').$field_id.'_'.$key_first_s.'_'.$key_second_s;
															$field_id_second = $id_second.'_'.$value_j_l['id'];
															$output .= '<li id="elements_'.$field_id.'_'.$key_first_s.'_'.$key_second_s.'">';
																if (isset($value_l["title"]) && $value_l["title"] != "") {
																	$output .= '<div class="widget-head"><span>'.esc_html($value_second_s["name"]).'</span><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a></div>';
																}else {
																	$output .= '<div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a></div>';
																}
																$output .= '<div class="widget-content">';
																	foreach ($value_l['options'] as $key_j_l => $value_j_l) {
																		$class = 'section';
																		$wrap_class = 'wrap_class';
																		$options_group = 'options-group';
																		if ( isset( $value_j_l['type'] ) ) {
																			$class .= ' section-'.$value_j_l['type'].' framework-form-'.$value_j_l['type'];
																		}
																		if ( isset( $value_j_l['class'] ) ) {
																			$class .= ' '.$value_j_l['class'];
																		}
																		
																		if ( ! array_key_exists( 'operator', $value_j_l ) || ! in_array( $value_j_l['operator'], array( 'and', 'or' ) ) ) {
																			$value_j_l['operator'] = 'and';
																		}
														
																		if ( ! array_key_exists( 'condition', $value_j_l ) || ! is_string( $value_j_l['condition'] ) ) {
																			$value_j_l['condition'] = '';
																		}
																		
																		$condition = empty( $value_j_l['condition'] ) ? '' : ' data-condition="'.  str_ireplace('[%id%]', $id_second."_", esc_attr( $value_j_l['condition'] ))  .'"';
																		$operator = empty( $condition ) ? '' : ' data-operator="'. esc_attr( $value_j_l['operator'] ) .'"'; 
																		if ($value_j_l["type"] != "heading-2" && $value_j_l['type'] != "heading-3" && $value_j_l['type'] != "group" && $value_j_l['type'] != "info" && $value_j_l['type'] != "uniq_id") {
																			$output .= '<div data-type="'.$value_j_l["type"].'" data-id="'.$field_id_second.'" id="section-'.$field_id_second.'"'.( $condition ).( $operator ).' class="'.esc_attr( $class ).'">'.(isset($value_j_l["name"]) && $value_j_l["name"] != ''?'<div class="name-with-desc"><h4 class="heading">'.$value_j_l["name"].'</h4></div>':'').
																			'<div class="all-option">';
																		}
																			if ($value_j_l["type"] == "images") {
																				$output .= '<div class="image_element">'.
																				wpqa_option_images($field_id_second,'','',$value_j_l["options"],$value_second_s[$value_j_l['id']],'',$field_name_second,'',$value_j_l["id"],'no').
																				'</div>';
																			}else if ($value_j_l["type"] == "upload") {
																				$output .= "<div class='controls controls-upload'>".wpqa_options_uploader($field_id.'_'.$key_first_s.'_'.$key_second_s.'_'.$value_j_l['id'],(isset($value_second_s[$value_j_l['id']])?$value_second_s[$value_j_l['id']]:""),null,$field_name_second,null,array(),$page,$post_term,$option_name)."</div>";
																			}else if ($value_j_l["type"] == "select_category") {
																				if (isset($value_j_l['selected']) && $value_j_l['selected'] == "s_f_category") {
																					$category = current(wp_get_object_terms($post_term,wpqa_question_categories));
																					if (!isset($category->name)) $category = '';
																				}
																				$output .= '<div class="styled-select" data-attr="'.$value_j_l["id"].'">'.
																					wp_dropdown_categories(array(
																						'show_option_none' => (isset($value_j_l['option_none']) && $value_j_l['option_none'] != ""?$value_j_l['option_none']:0),
																					    'orderby'          => 'name',
																					    'hide_empty'       => 0,
																					    'hierarchical'     => 1,
																					    'echo'             => 0,
																					    'class'            => "check-parent-class".(isset($value_j_l['class']) && $value_j_l['class'] != ""?" ".$value_j_l['class']:"")."",
																					    'name'             => $field_name_second,
																					    'id'               => $field_id_second,
																					    'selected'         => (isset($category->term_id) && $category->term_id != ""?$category->term_id:(isset($value_second_s[$value_j_l['id']]) && $value_second_s[$value_j_l['id']] != ""?$value_second_s[$value_j_l['id']]:"")),
																					    'taxonomy'         => (isset($value_j_l['taxonomy']) && $value_j_l['taxonomy'] != ""?$value_j_l['taxonomy']:"category")
																					)).
																				"</div>";
																			}else if ($value_j_l["type"] == "select") {
																				$output .= '<div class="styled-select"><select data-attr="'.$value_j_l["id"].'" class="framework-input framework-form-control" '.(isset($value_j_l['multiple']) && $value_j_l['multiple'] != ""?"multiple":"").' name="'.$field_name_second.(isset($value_j_l['multiple']) && $value_j_l['multiple'] != ""?"[]":"").'" id="'.$field_id_second.'">
																				'.(isset($value_j_l['first'])?'<option>'.$value_j_l['first'].'</option>':'');
																				foreach ($value_j_l['options'] as $key => $option) {
																					$output .= '<option'. (isset($value_j_l['multiple']) && $value_j_l['multiple'] != ""?(isset($value_second_s[$value_j_l['id']]) && is_array($value_second_s[$value_j_l['id']]) && in_array($key,$value_second_s[$value_j_l['id']])?' selected="selected"':""):(isset($value_second_s[$value_j_l['id']])?selected($value_second_s[$value_j_l['id']], $key, false ):"")) .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
																				}
																				$output .= '</select></div>';
																			}else if ($value_j_l["type"] == "radio") {
																				foreach ($value_j_l['options'] as $key => $option ) {
																					$output .= '<div class="framework-radio-div"><input name="'.$field_name_second.'" id="'.$field_id_second.'_'.$key.'" data-attr="'.$value_j_l["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'" class="framework-input framework-form-control framework-radio" type="radio" value="'. esc_attr( $key ) . '" '.(isset($value_second_s[$value_j_l['id']])?checked( $value_second_s[$value_j_l['id']], $key, false ):"").'><label for="'.$field_id_second.'_'.$key.'">' . esc_html( $option ) . '</label></div>';
																				}
																			}else if ($value_j_l["type"] == "textarea") {
																				$rows = '8';
																				if ( isset( $value_j_l['settings']['rows'] ) ) {
																					$custom_rows = $value_j_l['settings']['rows'];
																					if ( is_numeric( $custom_rows ) ) {
																						$rows = $custom_rows;
																					}
																				}
																				$output .= '<textarea data-attr="'.$value_j_l["id"].'" class="framework-input framework-form-control'.(isset($value_j_l['std'])?" framework-form-std":" framework-form-not-std").'" rows="'.$rows.'" name="'.$field_name_second.'" id="'.$field_id_second.'">'.stripslashes(isset($value_second_s[$value_j_l['id']])?$value_second_s[$value_j_l['id']]:"").'</textarea>';
																			}else if ($value_j_l["type"] == "slider") {
																				$output .= '<div class="section-sliderui">'.
																					wpqa_option_sliderui($value_j_l["min"],$value_j_l["max"],$value_j_l["step"],'',$value_second_s[$value_j_l['id']],$field_id.']['.$key_first_s.']['.$key_second_s.']['.$value_j_l['id'],$field_name_second,$field_id_second).
																				'</div>';
																			}else if ($value_j_l["type"] == "heading-2" || $value_j_l['type'] == "heading-3") {
																				if ( isset($value_j_l['end']) && $value_j_l['end'] == "end" ) {
																					if ( isset($value_j_l['div']) && $value_j_l['div'] == "div" ) {
																						$output .= '</div>';
																					}else {
																						$output .= '</div></div>';
																					}
																				}else {
																					if ( isset($value_j_l['div']) && $value_j_l['div'] == "div" ) {
																						$output .= '<div class="'.$wrap_class.'" id="'.(isset($value_j_l['id']) && $value_j_l['id'] != ""?"wrap_".$value_j_l['id']:"").'"'.( $condition ).( $operator ).'>';
																					}else {
																						$id_class = '';
																						$id_class = ! empty($value_j_l['id'])?$value_j_l['id']:(isset($value_j_l['name']) && $value_j_l['name'] != ""?$value_j_l['name']:"");
																						$id_class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($id_class) );
																						$output .= '<div'.(isset($value_j_l['id'])?' id="head-'.$value_j_l['id'].'"':'').' class="'.$options_group.(isset($value_j_l['id'])?' head-group head-'.$value_j_l['id']:'').'"'.( $condition ).( $operator ).'>';
																						if ( isset($value_j_l['name']) ) {
																							$output .= '<h4 class="vpanel-head-2">' . esc_html( $value_j_l['name'] ) . '</h4>';
																						}
																						$output .= '<div class="framework-group-2 ' . $id_class . '">';
																					}
																				}
																			}else if ($value_j_l['type'] == "group") {
																				if ( isset($value_j_l['end']) && $value_j_l['end'] == "end" ) {
																					$output .= '</div></div>';
																				}else {
																					$id_class = '';
																					$id_class = ! empty($value_j_l['id'])?$value_j_l['id']:(isset($value_j_l['name']) && $value_j_l['name'] != ""?$value_j_l['name']:"");
																					$id_class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($id_class) );
																					$output .= '<div'.(isset($value_j_l['id'])?' id="head-'.$value_j_l['id'].'"':'').' class="custom-group '.$options_group.(isset($value_j_l['id'])?' head-group head-'.$value_j_l['id']:'').'"'.( $condition ).( $operator ).'>';
																					if ( isset($value_j_l['name']) ) {
																						$output .= '<h4 class="vpanel-head-2">' . esc_html( $value_j_l['name'] ) . '</h4>';
																					}
																					$output .= '<div class="framework-group-2 ' . $id_class . '">';
																				}
																			}else if ($value_j_l["type"] == "multicheck") {
																				$value_option = (isset($value_j_l['std'])?$value_j_l['std']:array());
																				$output .= '<ul id="'.(isset($value_j_l['id']) && $value_j_l['id'] != ""?$key_first_s."_".$value_l["id"]."_".$key_second_s."_".$value_j_l['id']:"").'-ul"'.(isset($value_j_l['sort']) && $value_j_l['sort'] == "yes"?' class="sort-sections sort-sections-ul"':'').'>';
																				if (isset($value_j_l['sort']) && $value_j_l['sort'] == "yes") {
																					$k_sort = 0;
																					if (isset($val) && !empty($val) && is_array($val)) {
																						$value_option = $val;
																					}else {
																						$value_option = $value_j_l['options'];
																					}
																				}else {
																					$value_option = $value_j_l['options'];
																				}
																				
																				if ($value_j_l['options'] != $val) {
																					if (isset($val) && is_array($val)) {
																						foreach ($val as $key_s => $key_s) {
																							if (!isset($value_j_l['options'][$key_s]) && !isset($val[$key_s]["cat"]) && !isset($val[$key_s]["page"]) && !isset($val[$key_s]["builder"])) {
																								unset($value_option[$key_s]);
																							}
																						}
																					}
																					if (isset($value_j_l['options']) && is_array($value_j_l['options'])) {
																						foreach ($value_j_l['options'] as $key_s => $value_s) {
																							if (!isset($val[$key_s])) {
																								$value_option = array_merge($value_option,array($key_s => $value_s));
																							}
																						}
																					}
																				}
																				
																				foreach ($value_option as $key => $option) {
																					$o_option = $option;
																					if (!isset($o_option["builder"])) {
																						$checked = '';
																						if (isset($value_j_l['values']) && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))) {
																							$label = $value_j_l['values'][$option["value"]];
																							$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value_j_l["strtolower"]) && $value_j_l["strtolower"] == "not"?$key:strtolower($key)));
																						}else if (isset($value_j_l['sort']) && $value_j_l['sort'] == "yes") {
																							$k_sort++;
																							$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value_j_l["strtolower"]) && $value_j_l["strtolower"] == "not"?$key:strtolower($key)));
																							if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
																								if ($val[$key]["cat"] != "yes" && ($val[$option]["value"] == 0 || $val[$option]["value"] === 0)) {
																									$val[$option]["value"] = "q-0";
																								}
																								if (is_numeric($val[$option]["value"])) {
																									$label = get_term($val[$option]["value"]);
																									$label = (isset($label->name)?$label->name:"");
																								}else if ($val[$option]["value"] === "q-0") {
																									$label = esc_html__("All Question Categories","wpqa");
																								}else if ($val[$option]["value"] === "k-0") {
																									$label = esc_html__("All Knowledgebase Categories","wpqa");
																								}else {
																									$label = esc_html__("All Categories","wpqa");
																								}
																							}else if (isset($val[$key]["page"]) && $val[$key]["page"] == "yes") {
																								if (is_numeric($val[$option]["value"])) {
																									$label = get_the_title($val[$option]["value"]);
																								}
																							}else {
																								$label = (isset($value_j_l['options'][$option]["sort"])?$value_j_l['options'][$option]["sort"]:"");
																							}
																						}else {
																							$label = $o_option;
																							$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value_j_l["strtolower"]) && $value_j_l["strtolower"] == "not"?$key:strtolower($key)));
																						}

																						$id = $field_id_second.'-'.$option;
																						$name = $field_name_second.'['.$option.']';
																						
																						if (isset($val[$key_first_s][$value_l["id"]][$key_second_s][$value_j_l["id"]][$option]) ) {
																							if (isset($value_j_l['sort']) && $value_j_l['sort'] == "yes") {
																								if (isset($val[$key_first_s][$value_l["id"]][$key_second_s][$value_j_l["id"]][$option]["value"])) {
																									$checked = checked($val[$key_first_s][$value_l["id"]][$key_second_s][$value_j_l["id"]][$option]["value"], $option, false);
																								}
																							}else {
																								if (isset($val[$key_first_s][$value_l["id"]][$key_second_s][$value_j_l["id"]][$option])) {
																									$checked = checked($val[$key_first_s][$value_l["id"]][$key_second_s][$value_j_l["id"]][$option], $option, false);
																								}
																							}
																						}
																						$output .= '<li'.(isset($value_j_l['sort']) && $value_j_l['sort'] == "yes" && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))?" class='additions-li' id='".$value_j_l['id']."_additions_li_".$val[$key]["value"]."'":'').'>';
																							if (isset($value_j_l['sort']) && $value_j_l['sort'] == "yes") {
																								$output .= '<div class="widget-head"><div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a>'.((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")?'<a class="del-cat-item del-builder-item"><span class="dashicons dashicons-trash"></span></a>':'').'</div>';
																								if ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")) {
																									if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
																										$item_type = 'cat';
																									}else {
																										$item_type = 'page';
																									}
																									$name_sort = (isset($value_j_l['id']) && $value_j_l['id'] != ""?esc_html(($page == 'widgets'?$post_term->get_field_name($value_j_l['id']):($page == 'post' || $page == 'term' || $page == 'user'?$value_j_l['id']:$option_name.'['.$value_j_l['id'].']'))):"");
																									$output .= '<input name="'.$name_sort.'['.$item_type.'-'.$val[$key]["value"].']['.$item_type.']" value="yes" type="hidden"><input name="'.$name_sort.'['.$item_type.'-'.$val[$key]["value"].'][value]" value="'.$val[$key]["value"].'" type="hidden">';
																								}else {
																									$output .= '<input type="hidden" name="'.esc_attr( $name.'[sort]' ).'" value="'.esc_html( $label ).'">';
																								}
																							}
																							if (isset($o_option["default"]) || (isset($val[$key]) && is_array($val[$key]) && (array_key_exists('cat',$val[$key]) || array_key_exists('page',$val[$key])))) {
																								if (isset($o_option["default"])) {
																									$output .= '<input value="'.$option.'" type="hidden" name="'.esc_attr( $name.(isset($value_j_l['sort']) && $value_j_l['sort'] == "yes"?'[value]':'') ).'">
																									<input value="yes" type="hidden" name="'.esc_attr( $name.'[default]' ).'">';
																								}
																							}else {
																								$output .= '<label class="switch" for="'.esc_attr($id).'">
																									<input value="0" type="hidden" class="not-increase" name="'.esc_attr($name.(isset($value_j_l['sort']) && $value_j_l['sort'] == "yes"?'[value]':'') ).'">
																									<input id="'.esc_attr($id).'" value="'.$option.'" class="checkbox framework-input framework-form-control" type="checkbox" name="'.esc_attr( $name.(isset($value_j_l['sort']) && $value_j_l['sort'] == "yes"?'[value]':'') ).'" '. $checked .'>
																									<label for="'.esc_attr($id).'" data-on="'.esc_attr__("ON","wpqa").'" data-off="'.esc_attr__("OFF","wpqa").'"></label>
																								</label>';
																							}
																							$output .= '<label for="'.esc_attr($id).'">' . esc_html( $label ) . '</label>';
																							if (isset($value_j_l['sort']) && $value_j_l['sort'] == "yes") {
																								$output .= '</div>';
																							}
																						$output .= '</li>';
																					}
																				}
																				$output .= '</ul>';
																			}else if ($value_j_l["type"] == "checkbox") {
																				$output .= '<label class="switch" for="'.$field_id.'_'.$key_first_s.'_'.$key_second_s.'_'.$value_j_l['id'].'">
																					<input id="'.$field_id.'_'.$key_first_s.'_'.$key_second_s.'_'.$value_j_l['id'].'" class="checkbox framework-input framework-form-control" value="on" type="checkbox" name="'.$field_name_second.'" '.checked( (isset($value_second_s[$value_j_l['id']])?$value_second_s[$value_j_l['id']]:""), "on", false).'>
																					<label for="'.$field_id.'_'.$key_first_s.'_'.$key_second_s.'_'.$value_j_l['id'].'" data-on="'.esc_attr__("ON","wpqa").'" data-off="'.esc_attr__("OFF","wpqa").'"></label>
																				</label>';
																			}else if ($value_j_l["type"] == "info") {
																				$output .= '<div data-type="'.$value_j_l['type'].'"'.(isset($value_j_l['id'])?' data-id="'.esc_attr( $value_j_l['id'] ).'"':"").( $condition ).( $operator ).(isset($value_j_l['id'])?' id="'.esc_attr( $value_j_l['id'] ).'"':'').' class="'.esc_attr( $class ).'">';
																				if ( isset($value_j_l['name']) ) {
																					$output .= '<div class="alert-message'.(isset($value_j_l['alert']) && $value_j_l['alert'] != ""?" ".$value_j_l['alert']:"").'"><p><span>' . $value_j_l['name'] . '</span></p></div>';
																				}
																				if ( isset( $value_j_l['desc'] ) ) {
																					$output .= apply_filters('framework_sanitize_info', $value_j_l['desc'] );
																				}
																				$output .= '</div>';
																			}else {
																				$input_value = stripslashes(htmlspecialchars(isset($value_second_s[$value_j_l['id']])?$value_second_s[$value_j_l['id']]:""));
																				if ($value_j_l["type"] == "slider") {
																					$input_value = (int)$input_value;
																				}
																				$output .= '<input class="'.(isset($value_j_l['std'])?"framework-form-std":"framework-form-not-std").($value_j_l["type"] == "color"?" framework-color":"").($value_j_l["type"] == "date"?" builder-datepicker":"").($value_j_l["type"] == "slider"?" mini":"").'"'.($value_j_l["type"] == "date" && isset($value_j_l['js']) && $value_j_l['js'] != ""?" data-js='".json_encode($value_j_l['js'])."'":"").' name="'.$field_name_second.'" type="'.($value_j_l["type"] == "hidden" || $value_j_l["type"] == "hidden_id" || $value_j_l["type"] == "uniq_id"?"hidden":"text").'" value="'.$input_value.'">';
																			}
																		if ($value_j_l["type"] != "heading-2" && $value_j_l['type'] != "heading-3" && $value_j_l['type'] != "group" && $value_j_l['type'] != "info" && $value_j_l['type'] != "uniq_id") {
																			$output .= '</div></div>';
																		}
																	}
																$output .= '</div>
															</li>';
														}
													}
												$output .= '</ul>
												<div class="clear"></div>';
												if ($page == "widgets") {
													$field_widget_name = $field_widget_id."[".$value_l["id"].']['.$key_first_s.']['.$value_j_l["id"]."]";
													$field_widget_name = ($field_name != ''?$field_name.'[':'').$key_first_s.($field_name != ''?']':'').'['.$value_l["id"].']';
												}
												$output .= '<input'.($page == "post" || $page == "term" || $page == "user" || $page == "widgets"?" data-class='no_theme_options'":" data-options='".$option_name."'").' class="add_element add_element_elements'.($page == "post" || $page == "term" || $page == "user" || $page == "widgets"?" no_theme_options":"").(isset($value_l['addto']) && $value_l['addto'] != ""?" add_element_to":"").'" type="button" value="'.(isset($value_l['button']) && $value_l['button'] != ""?$value_l['button']:esc_html__("+ Add a new element","wpqa")).'"'.(isset($field_id) && $field_id != ""?" data-id='".$field_id.'_'.$key_first_s.'_'.$value_l['id']."' data-name='[".$field_id.']['.$key_first_s.']['.$value_e['id']."]'":"").(isset($field_widget_name) && $field_widget_name != ""?" data-widget='".$field_widget_name."' data-widget-id='".$field_widget_id."' data-first-widget='".$value_l["id"]."'":"").(isset($value_l['title']) && $value_l['title'] != ""?" data-title='".$value_l['title']."'":"").'>
												<span data-js="'.esc_js((isset($key_second_s)?$key_second_s:0)+1).'" class="'.$field_id.'_'.$key_first_s.'_'.$value_l['id'].'_j"></span>';
											}else if ($value_l["type"] == "info") {
												$output .= '<div data-type="'.$value_l['type'].'"'.(isset($value_l['id'])?' data-id="'.esc_attr( $value_l['id'] ).'"':"").( $condition ).( $operator ).(isset($value_l['id'])?' id="'.esc_attr( $value_l['id'] ).'"':'').' class="'.esc_attr( $class ).'">';
												if ( isset($value_l['name']) ) {
													$output .= '<div class="alert-message'.(isset($value_l['alert']) && $value_l['alert'] != ""?" ".$value_l['alert']:"").'"><p><span>' . $value_l['name'] . '</span></p></div>';
												}
												if ( isset( $value_l['desc'] ) ) {
													$output .= apply_filters('framework_sanitize_info', $value_l['desc'] );
												}
												$output .= '</div>';
											}else {
												$input_value = stripslashes(htmlspecialchars(isset($value_first_s[$value_l['id']])?$value_first_s[$value_l['id']]:""));
												if ($value_l["type"] == "slider") {
													$input_value = (int)$input_value;
												}
												$output .= '<input class="'.(isset($value_l['std'])?"framework-form-std":"framework-form-not-std").($value_l["type"] == "color"?" framework-color":"").($value_l["type"] == "date"?" builder-datepicker":"").($value_l["type"] == "slider"?" mini":"").'"'.($value_l["type"] == "date" && isset($value_l['js']) && $value_l['js'] != ""?" data-js='".json_encode($value_l['js'])."'":"").' name="'.$field_name.'['.$key_first_s.']['.$value_l['id'].']" type="'.($value_l["type"] == "hidden" || $value_l["type"] == "hidden_id" || $value_l["type"] == "uniq_id"?"hidden":"text").'" value="'.$input_value.'">';
											}
										if ($value_l["type"] != "heading-2" && $value_l['type'] != "heading-3" && $value_l['type'] != "group" && $value_l['type'] != "info" && $value_l['type'] != "uniq_id") {
											$output .= '</div></div>';
										}
									}
								$output .= '</div>
							</li>';
						}
					}
				$output .= '</ul>
				<div class="clear"></div>';
				if ($page == "widgets") {
					$field_widget_name = $field_widget_id."[".$value['id']."]";
				}
				$output .= '<input'.($page == "post" || $page == "term" || $page == "user" || $page == "widgets"?" data-class='no_theme_options'":" data-options='".$option_name."'").' class="add_element'.($page == "post" || $page == "term" || $page == "user" || $page == "widgets"?" no_theme_options":"").(isset($value['addto']) && $value['addto'] != ""?" add_element_to":"").'" type="button" value="'.(isset($value['button']) && $value['button'] != ""?$value['button']:esc_html__("+ Add a new element","wpqa")).'"'.(isset($field_id) && $field_id != ""?" data-id='".$field_id."'":"").(isset($field_widget_name) && $field_widget_name != ""?" data-widget='".$field_widget_name."' data-widget-id='".$field_widget_id."' data-first-widget='".$value['id']."'":"").(isset($value['title']) && $value['title'] != ""?" data-title='".$value['title']."'":"").'>
				<span data-js="'.esc_js((isset($key_first_s)?$key_first_s:0)+1).'" class="'.$field_id.'_j"></span>';
				break;
			
			// Upload images
			case 'upload_images';
				$output .= '<div class="images-uploaded">
					<a data-id="'.$field_id.'" data-name="'.$field_name.'" class="upload_image_button upload_image_button_m" href="#">'.esc_html__("Upload","wpqa").'</a>
					<div class="clear"></div>
					<ul id="'.$field_id.'">';
						$val = (isset($val) && is_array($val)?$val:array());
						if (isset($val) && is_array($val)) {
							foreach ($val as $value_image) {
								$image_url = wp_get_attachment_image($value_image,"thumbnail");
								$output .= "<li id='".$field_id."-item-".$value_image."' class='multi-images'>
									<div class='multi-image'>
										".$image_url."
										<input name='".$field_name."[]' type='hidden' value='".$value_image."'>
										<div class='image-overlay'></div>
										<div class='image-media-bar'>
											<a class='image-edit-media' title='".esc_attr__("Edit","wpqa")."' href='post.php?post=".$value_image."&amp;action=edit' target='_blank'>
												<span class='dashicons dashicons-edit'></span>
											</a>
											<a href='#' class='image-remove-media' title='".esc_attr__("Remove","wpqa")."'>
												<span class='dashicons dashicons-no-alt'></span>
											</a>
										</div>
									</div>
								</li>";
							}
						}
					$output .= '</ul>
				</div>';
				break;
			
			// Role
			case 'roles':
				global $wp_roles;
				$resort_roles = $group_roles = array();
				$val = (isset($val) && is_array($val)?$val:array());
				$k = 0;
				$default_roles = $wp_roles->roles;
				$roles = array_merge_recursive($default_roles,$val);
				if (isset($roles["administrator"])) {
					unset($roles["administrator"]);
				}
				if (isset($roles["ban_group"])) {
					unset($roles["ban_group"]);
				}
				$subscriptions_payment = wpqa_options("subscriptions_payment");
				$role_plans = wpqa_subscription_role_options("role");
				$default_group = wpqa_options("default_group");
				$default_group = ($default_group != ""?$default_group:"subscriber");
				$confirm_email = wpqa_options("confirm_email");
				$activation_role = "activation";
				$output .= '
				<input id="role_name" type="text" name="role_name" value="">
				<input id="role_add" data-id="'.$field_id.'" type="button" class="add_element not_add_element" value="'.esc_html__("Add a new role","wpqa").'">
				<div class="clear"></div>
				<ul id="roles_list" class="not-sort sort-sections sort-sections-ul roles_list">';
					if (isset($roles) && is_array($roles) && !empty($roles)) {
						foreach ($roles as $key_rol => $value_rol) {
							if (isset($roles[$default_group]) && $default_group == $key_rol) {
								$group_roles[$key_rol] = $value_rol;
								unset($roles[$default_group]);
							}else if (is_array($role_plans) && isset($role_plans[$key_rol]) && $subscriptions_payment == "on") {
								$group_roles[$key_rol] = $value_rol;
								unset($roles[$key_rol]);
							}else if (isset($roles[$activation_role]) && $activation_role == $key_rol && $confirm_email == "on") {
								$group_roles[$key_rol] = $value_rol;
								unset($roles[$activation_role]);
							}else {
								$resort_roles[$key_rol] = $value_rol;
							}
						}
					}
					$resort_roles = array_merge($group_roles,$resort_roles);
					if (isset($resort_roles) && is_array($resort_roles) && !empty($resort_roles)) {
						foreach ($resort_roles as $key_rol => $value_rol) {$k++;
							$last_id = (isset($value_rol["id"])?esc_html($value_rol["id"]):$key_rol);
							$role_name = (isset($value_rol["group"])?esc_html($value_rol["group"]):(isset($value_rol["name"])?esc_html($value_rol["name"]):esc_html($value_rol["id"])));
							$role_id = (isset($value_rol["id"])?esc_html($value_rol["id"]):"");
							$subscriber_group = ($role_id == $default_group?esc_html__("Default Group","wpqa"):"");
							$paid_group = (is_array($role_plans) && isset($role_plans[$key_rol]) && $subscriptions_payment == "on"?esc_html__("Paid Group","wpqa"):"");
							$activation_group = ($role_id == $activation_role && $confirm_email == "on"?esc_html__("Activation Group","wpqa"):"");
							$user_groups = ($subscriber_group != "" || $paid_group != "" || $activation_group != ""?" - (".$subscriber_group.$paid_group.$activation_group.")":"");
							$output .= '<li><div class="widget-head">'.$role_name.$user_groups.(isset($value_rol["new"]) && $value_rol["new"] == "new"?'<a class="del-roles-item del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a>':'').'</div>
								<div class="widget-content">
									<div class="widget-content-div">';
										if (isset($value_rol["new"]) && $value_rol["new"] == "new") {
											$output .= '<input id="'.$field_id.'['.$last_id.'][group]" type="hidden" name="'.$field_name.'['.$last_id.'][group]" value="'.esc_attr($value_rol["group"]).'">
											<input id="'.$field_id.'['.$last_id.'][new]" type="hidden" name="'.$field_name.'['.$last_id.'][new]" value="'.(isset($value_rol["new"]) && $value_rol["new"] != ""?esc_attr($value_rol["new"]):"new").'">';
										}
										$output .= '<input type="hidden" class="group_role_name" name="'.$field_name.'['.$last_id.'][id]" value="'.(isset($key_rol) && is_string($key_rol) && isset($value_rol["id"]) && isset($wp_roles->roles[$key_rol]) && is_array($wp_roles->roles[$key_rol])?esc_attr($value_rol["id"]):$last_id).'">
										<div class="clearfix"></div>';
										$roles_array = wpqa_roles_array();
										if (isset($roles_array) && !empty($roles_array)) {
											foreach ($roles_array as $roles_key => $roles_value) {
												$output .= '<div class="section section-checkbox">
													<div class="option">
														<div class="controls">
															<label class="switch" for="'.$field_id.'['.$last_id.']['.$roles_key.']">
																<input id="'.$field_id.'['.$last_id.']['.$roles_key.']" value="on" class="checkbox framework-input framework-form-control" type="checkbox" name="'.$field_name.'['.$last_id.']['.$roles_key.']" '. (isset($value_rol[$roles_key])?checked($value_rol[$roles_key],"on",false):"on") .'>
																<label for="'.$field_id.'['.$last_id.']['.$roles_key.']" data-on="'.esc_attr__("ON","wpqa").'" data-off="'.esc_attr__("OFF","wpqa").'"></label>
															</label>
															'.($roles_value != ""?'<label class="explain explain-checkbox" for="'.$field_id.'['.$last_id.']['.$roles_key.']">'.$roles_value.'</label>':'').'
														</div>
													</div>
												</div>';
											}
										}
										$roles_array = wpqa_roles_text_array();
										if (isset($roles_array) && !empty($roles_array)) {
											foreach ($roles_array as $roles_key => $roles_value) {
												$output .= '<div class="section section-text framework-form-text">
													<div class="name-with-desc"><h4 class="heading">'.$roles_value.'</h4></div>
													<div class="controls">
														<input id="'.$field_id.'['.$last_id.']['.$roles_key.']" value="'.(isset($value_rol[$roles_key]) && $value_rol[$roles_key] >= 0?(int)$value_rol[$roles_key]:0).'" class="framework-input framework-form-control" type="text" name="'.$field_name.'['.$last_id.']['.$roles_key.']">
													</div>
												</div>';
											}
										}
									$output .= '</div>
								</div>
							</li>';
						}
					}
				$output .= '</ul><div class="clear"></div>';
				break;
			
			// Select Box
			case 'select':
				$output .= '<div class="styled-select"><select class="framework-input framework-form-control" '.(isset($value['multiple']) && $value['multiple'] != ""?"multiple":"").' name="' . esc_attr( $field_name.(isset($value['multiple']) && $value['multiple'] != ""?"[]":"") ) . '" id="'.$field_id.'">
				'.(isset($value['first'])?'<option>'.$value['first'].'</option>':'');
				foreach ($value['options'] as $key => $option ) {
					$output .= '<option'. (isset($value['multiple']) && $value['multiple'] != ""?(isset($val) && is_array($val) && in_array($key,$val)?' selected="selected"':""):selected( $val, $key, false )) .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
				}
				$output .= '</select></div>';
				break;

			// Select advanced
			case 'select_advanced':
				$output .= '<select class="framework-input framework-form-control" '.(isset($value['multiple']) && $value['multiple'] != ""?"multiple":"").' name="' . esc_attr( $field_name.'[]' ) . '" id="'.$field_id.'">';
				foreach ($value['options'] as $key => $option ) {
					$output .= '<option'. selected( $val, $key, false ) .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
				}
				$output .= '</select>';
				break;
			
			// Radio Box
			case 'radio':
				foreach ($value['options'] as $key => $option) {
					$id = $option_name . '-' . $field_id .'-'. $key;
					$output .= '<div class="framework-radio-div"><input class="framework-input framework-form-control framework-radio'.(isset($value['class'])?" ".esc_attr($value['class']):'').'" type="radio" name="'.$field_name.'" id="' . esc_attr( $id ) . '" value="'. esc_attr( $key ) . '" '. checked( $val, $key, false) .'><label for="' . esc_attr( $id ) . '">' . esc_html( $option ) . '</label></div>';
				}
				break;

			// Image Selectors
			case 'images':
				$output .= wpqa_option_images($field_id,(isset($value['width']) && $value['width'] != ""?$value['width']:""),(isset($value['height']) && $value['height'] != ""?$value['height']:""),$value['options'],$val,(isset($value['class']) && $value['class'] != ""?" ".$value['class']:""),$field_name,"","","no");
				break;

			// Checkbox
			case 'checkbox':
				$output .= '<label class="switch" for="'.$field_id.'">
					<input id="'.$field_id.'" class="checkbox framework-input framework-form-control" value="on" type="checkbox" name="'.$field_name.'" '.checked( $val, "on", false).'>
					<label for="'.$field_id.'" data-on="'.esc_attr__("ON","wpqa").'" data-off="'.esc_attr__("OFF","wpqa").'"></label>
				</label>';
				break;

			// Multicheck
			case 'multicheck':
				$value_option = (isset($value['std'])?$value['std']:array());
				$output .= '<ul id="'.(isset($value['id']) && $value['id'] != ""?$value['id']:"").'-ul"'.(isset($value['sort']) && $value['sort'] == "yes"?' class="sort-sections sort-sections-ul"':'').'>';
				if (isset($value['sort']) && $value['sort'] == "yes") {
					$k_sort = 0;
					if (isset($val) && !empty($val) && is_array($val)) {
						$value_option = $val;
					}else {
						$value_option = $value['options'];
					}
				}else {
					$value_option = $value['options'];
				}
				
				if ($value['options'] != $val) {
					if (isset($val) && is_array($val)) {
						foreach ($val as $key_s => $key_s) {
							if (!isset($value['options'][$key_s]) && !isset($val[$key_s]["cat"]) && !isset($val[$key_s]["page"]) && !isset($val[$key_s]["builder"])) {
								unset($value_option[$key_s]);
							}
						}
					}
					if (isset($value['options']) && is_array($value['options'])) {
						foreach ($value['options'] as $key_s => $value_s) {
							if (!isset($val[$key_s])) {
								$value_option = array_merge($value_option,array($key_s => $value_s));
							}
						}
					}
				}
				
				foreach ($value_option as $key => $option) {
					$o_option = $option;
					$output = apply_filters(wpqa_prefix_theme."_show_multicheck_field",$output,$value_option,$key,$o_option,$val,$option_name,$field_name,$field_id);
					if (!isset($o_option["builder"])) {
						$checked = '';
						if (isset($value['values']) && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))) {
							$label = $value['values'][$option["value"]];
							$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value["strtolower"]) && $value["strtolower"] == "not"?$key:strtolower($key)));
						}else if (isset($value['sort']) && $value['sort'] == "yes") {
							$k_sort++;
							$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value["strtolower"]) && $value["strtolower"] == "not"?$key:strtolower($key)));
							if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
								if ($val[$key]["cat"] != "yes" && ($val[$option]["value"] == 0 || $val[$option]["value"] === 0)) {
									$val[$option]["value"] = "q-0";
								}
								if (is_numeric($val[$option]["value"])) {
									$label = get_term($val[$option]["value"]);
									$label = (isset($label->name)?$label->name:"");
								}else if ($val[$option]["value"] === "q-0") {
									$label = esc_html__("All Question Categories","wpqa");
								}else if ($val[$option]["value"] === "k-0") {
									$label = esc_html__("All Knowledgebase Categories","wpqa");
								}else {
									$label = esc_html__("All Categories","wpqa");
								}
							}else if (isset($val[$key]["page"]) && $val[$key]["page"] == "yes") {
								if (is_numeric($val[$option]["value"])) {
									$label = get_the_title($val[$option]["value"]);
								}
							}else {
								$label = (isset($value['options'][$option]["sort"])?$value['options'][$option]["sort"]:"");
							}
						}else {
							$label = $o_option;
							$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value["strtolower"]) && $value["strtolower"] == "not"?$key:strtolower($key)));
						}
						
						$id = $option_name . '-' . $field_id . '-'. $option;
						$name = $field_name.'[' . $option .']';
						
						if ( isset($val[$option]) ) {
							if (isset($value['sort']) && $value['sort'] == "yes") {
								if (isset($val[$option]["value"])) {
									$checked = checked($val[$option]["value"], $option, false);
								}
							}else {
								if (isset($val[$option])) {
									$checked = checked($val[$option], $option, false);
								}
							}
						}
						$output .= '<li'.(isset($value['sort']) && $value['sort'] == "yes" && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))?" class='additions-li' id='".$value['id']."_additions_li_".$val[$key]["value"]."'":'').'>';
							if (isset($value['sort']) && $value['sort'] == "yes") {
								$output .= '<div class="widget-head"><div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a>'.((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")?'<a class="del-cat-item del-builder-item"><span class="dashicons dashicons-trash"></span></a>':'').'</div>';
								if ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")) {
									if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
										$item_type = 'cat';
									}else {
										$item_type = 'page';
									}
									$name_sort = (isset($value['id']) && $value['id'] != ""?esc_html(($page == 'widgets'?$post_term->get_field_name($value['id']):($page == 'post' || $page == 'term' || $page == 'user'?$value['id']:$option_name.'['.$value['id'].']'))):"");
									$output .= '<input name="'.$name_sort.'['.$item_type.'-'.$val[$key]["value"].']['.$item_type.']" value="yes" type="hidden"><input name="'.$name_sort.'['.$item_type.'-'.$val[$key]["value"].'][value]" value="'.$val[$key]["value"].'" type="hidden">';
								}else {
									$output .= '<input type="hidden" name="'.esc_attr( $name.'[sort]' ).'" value="'.esc_html( $label ).'">';
								}
							}
							if (isset($o_option["default"]) || (isset($val[$key]) && is_array($val[$key]) && (array_key_exists('cat',$val[$key]) || array_key_exists('page',$val[$key])))) {
								if (isset($o_option["default"])) {
									$output .= '<input value="'.$option.'" type="hidden" name="'.esc_attr( $name.(isset($value['sort']) && $value['sort'] == "yes"?'[value]':'') ).'">
									<input value="yes" type="hidden" name="'.esc_attr( $name.'[default]' ).'">';
								}
							}else {
								$output .= '<label class="switch" for="'.esc_attr($id).'">
									<input value="0" type="hidden" name="'.esc_attr( $name.(isset($value['sort']) && $value['sort'] == "yes"?'[value]':'') ).'">
									<input id="'.esc_attr($id).'" value="'.$option.'" class="checkbox framework-input framework-form-control" type="checkbox" name="'.esc_attr( $name.(isset($value['sort']) && $value['sort'] == "yes"?'[value]':'') ).'" '. $checked .'>
									<label for="'.esc_attr($id).'" data-on="'.esc_attr__("ON","wpqa").'" data-off="'.esc_attr__("OFF","wpqa").'"></label>
								</label>';
							}
							$output .= '<label for="'.esc_attr($id).'">' . esc_html( $label ) . '</label>';
							if (isset($value['sort']) && $value['sort'] == "yes") {
								$output .= '</div>';
							}
						$output .= '</li>';
					}
				}
				$output .= '</ul>';
				break;

			// Color picker
			case 'color':
				$default_color = '';
				if ( isset($value['std']) ) {
					if ( $val !=  $value['std'] )
						$default_color = ' data-default-color="' .$value['std'] . '" ';
				}
				$output .= '<input name="'.$field_name.'" id="'.$field_id.'" class="framework-color '.(isset($value['class'])?esc_attr($value['class']):'').'"  type="text" value="' . esc_attr( $val ) . '"' . $default_color .'>';
				break;

			// Uploader
			case 'upload':
				$output .= wpqa_options_uploader($field_id,$val,null,$field_name,null,(isset($value['options'])?$value['options']:array()),$page,$post_term,$option_name);
				break;

			// Typography
			case 'typography':
				
				unset( $font_size, $font_style, $font_face, $font_color );
				$font_size = $font_face = $font_style = $font_color = '';

				$typography_defaults = array(
					'size' => '',
					'face' => '',
					'style' => '',
					'color' => ''
				);

				$typography_stored = wp_parse_args( $val, $typography_defaults );

				$typography_options = array(
					'sizes' => wpqa_recognized_font_sizes(),
					'faces' => wpqa_recognized_font_faces(),
					'styles' => wpqa_recognized_font_styles(),
					'color' => true
				);

				if ( isset( $value['options'] ) ) {
					$typography_options = wp_parse_args( $value['options'], $typography_options );
				}

				// Font Size
				if ( $typography_options['sizes'] ) {
					$font_size = '<select class="framework-typography framework-typography-size" name="' . esc_attr( $field_name.'[size]' ) . '" id="' . esc_attr( $field_id . '_size' ) . '">';
					$sizes = $typography_options['sizes'];
					$font_size .= '<option value="" ' . selected( "default", "default", false ) . '>'.esc_html__("Size","wpqa").'</option>';
					foreach ( $sizes as $i ) {
						$size = $i . 'px';
						$font_size .= '<option value="' . esc_attr( $size ) . '" ' . (isset($typography_stored['size']) && is_string($typography_stored['size'])?selected( $typography_stored['size'], $size, false ):"") . '>' . esc_html( $size ) . '</option>';
					}
					$font_size .= '</select>';
				}

				// Font Face
				if ( $typography_options['faces'] ) {
					$font_face = '<input class="framework-typography framework-typography-face" name="' . esc_attr( $field_name.'[face]' ) . '" id="' . esc_attr( $field_id . '_face' ) . '" value="'.$typography_stored['face'].'">';
				}

				// Font Styles
				if ( $typography_options['styles'] ) {
					$font_style = '<select class="framework-typography framework-typography-style" name="'.$field_name.'[style]" id="'. $field_id.'_style">';
					$styles = $typography_options['styles'];
					foreach ( $styles as $key => $style ) {
						$font_style .= '<option value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['style'], $key, false ) . '>'. $style .'</option>';
					}
					$font_style .= '</select>';
				}

				// Font Color
				if ( $typography_options['color'] ) {
					$default_color = '';
					if ( isset($value['std']['color']) ) {
						if ( $val !=  $value['std']['color'] )
							$default_color = ' data-default-color="' .$value['std']['color'] . '" ';
					}
					$font_color = '<input name="' . esc_attr( $field_name.'[color]' ) . '" id="' . esc_attr( $field_id . '_color' ) . '" class="framework-color framework-typography-color"  type="text" value="' . esc_attr( $typography_stored['color'] ) . '"' . $default_color .'>';
				}

				// Allow modification/injection of typography fields
				$typography_fields = compact( 'font_size', 'font_face', 'font_style', 'font_color' );
				$typography_fields = apply_filters( 'framework_typography_fields', $typography_fields, $typography_stored, $option_name, $value );
				$output .= implode( '', $typography_fields );

				break;

			// Background
			case 'background':
				$background = $val;
				if (isset($value['options']) && is_array($value['options']) && isset($value['options']['color'])) {
					// Background Color
					$default_color = '';
					if ( isset( $value['std']['color'] ) ) {
						if ( $val !=  $value['std']['color'] )
							$default_color = ' data-default-color="' .$value['std']['color'] . '" ';
					}
					$output .= '<input name="' . esc_attr( $field_name.'[color]' ) . '" id="' . esc_attr( $field_id . '_color' ) . '" class="framework-color framework-background-color"  type="text" value="'.(isset($background['color'])?esc_attr($background['color']):"").'"' . $default_color .'>';
				}
				
				// Background Image
				$background_image = (isset($background['image']) && $background['image'] != ""?$background['image']:"");
				if (isset($value['options']) && is_array($value['options']) && isset($value['options']['image'])) {
					$output .= wpqa_options_uploader($field_id,$background_image,null,esc_html($field_name.'[image]'),null,array(),$page,$post_term,$option_name);
				}
				$id_class = 'framework-background-properties '.(isset($value['class'])?esc_attr($value['class']):'').'';
				if ( !empty($background_image) ) {
					$id_class .= ' hide';
				}
				$output .= '<div class="' . esc_attr( $id_class ) . '">';
				if (isset($value['options']) && is_array($value['options']) && isset($value['options']['repeat'])) {
					// Background Repeat
					$output .= '<select class="framework-background framework-background-repeat" name="' . esc_attr( $field_name.'[repeat]'  ) . '" id="' . esc_attr( $field_id . '_repeat' ) . '">';
					$repeats = wpqa_recognized_background_repeat();
		
					foreach ($repeats as $key => $repeat) {
						$output .= '<option value="' . esc_attr( $key ) . '" ' . selected((isset($background['repeat'])?esc_attr($background['repeat']):""), $key, false ) . '>'. esc_html( $repeat ) . '</option>';
					}
					$output .= '</select>';
				}
				if (isset($value['options']) && is_array($value['options']) && isset($value['options']['position'])) {
					// Background Position
					$output .= '<select class="framework-background framework-background-position" name="' . esc_attr( $field_name.'[position]' ) . '" id="' . esc_attr( $field_id . '_position' ) . '">';
					$positions = wpqa_recognized_background_position();
		
					foreach ($positions as $key=>$position) {
						$output .= '<option value="' . esc_attr( $key ) . '" ' . selected((isset($background['position'])?esc_attr($background['position']):""), $key, false ) . '>'. esc_html( $position ) . '</option>';
					}
					$output .= '</select>';
				}
				if (isset($value['options']) && is_array($value['options']) && isset($value['options']['attachment'])) {
					// Background Attachment
					$output .= '<select class="framework-background framework-background-attachment" name="' . esc_attr( $field_name.'[attachment]' ) . '" id="' . esc_attr( $field_id . '_attachment' ) . '">';
					$attachments = wpqa_recognized_background_attachment();
		
					foreach ($attachments as $key => $attachment) {
						$output .= '<option value="' . esc_attr( $key ) . '" ' . selected((isset($background['attachment'])?esc_attr($background['attachment']):""), $key, false ) . '>' . esc_html( $attachment ) . '</option>';
					}
					$output .= '</select>';
				}
				$output .= '</div>';

				break;

			// export
			case 'export':
				$output .= '<textarea id="'.$field_id.'" class="framework-input framework-form-control builder_select" rows="8">' . esc_textarea($value['export']) . '</textarea>';
				break;
			
			// import
			case 'import':
				$output .= '<textarea id="'.$field_id.'" class="framework-input framework-form-control" name="'.$field_name.'" rows="8"></textarea>';
				break;
				
			// Editor
			case 'editor':
				$rich_editing = get_user_meta(get_current_user_id(), 'rich_editing', true);
				if ($rich_editing == true) {
					$output .= '<div class="framework_editor"></div>';
				}
				echo stripcslashes($output);
				$default_editor_settings = array(
					'textarea_name' => $field_name,
					'media_buttons' => "framework_editor",
					'tinymce' => array( 'plugins' => 'wordpress' )
				);
				$editor_settings = array();
				if ( isset( $value['settings'] ) ) {
					$editor_settings = $value['settings'];
				}
				$editor_settings = apply_filters("framework_editor_settings",$editor_settings,$field_id);
				$editor_settings = array_merge($default_editor_settings,$editor_settings);
				wp_editor($val,$field_id,$editor_settings);
				$output = '';
				break;
			
			// Info
			case 'info':
				$output .= '<div data-type="'.$value['type'].'"'.(isset($value['id'])?' data-id="'.esc_attr( $value['id'] ).'"':"").( $condition ).( $operator ).(isset($value['id'])?' id="'.esc_attr( $value['id'] ).'"':'').' class="'.esc_attr( $class ).'">';
				if ( isset($value['name']) ) {
					$output .= '<div class="alert-message'.(isset($value['alert']) && $value['alert'] != ""?" ".$value['alert']:"").'"><p><span>' . $value['name'] . '</span></p></div>';
				}
				if ( isset( $value['desc'] ) ) {
					$output .= apply_filters('framework_sanitize_info', $value['desc'] );
				}
				$output .= '</div>';
				break;

			// Heading for Navigation
			case 'heading':
				$counter++;
				if ( $counter >= 2 ) {
					$output .= '</div>';
				}
				$id_class = '';
				$id_class = ! empty($value['id'])?$value['id']:$value['name'];
				$id_class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($id_class) );
				if ( ! array_key_exists( 'template', $value ) || ! is_string( $value['template'] ) ) {
					$value['template'] = '';
				}
				$template = empty( $value['template'] ) ? '' : ' data-template="'. esc_attr( $value['template'] ) .'"';
				if (isset($value['template']) && $value['template'] != "" && $value['template'] != $wp_page_template) {
					$id_class .= ' hide';
				}
				$output .= '<div'.$template.' id="options-group-'.$counter.'" class="framework-group '.$id_class.'">
				<h3>'.(isset($value['icon']) && $value['icon'] != ''?'<span class="dashicons dashicons-'.$value['icon'].'"></span>':''). esc_html( $value['name'] ) . '</h3>';
				if (isset($value['options'])) {
					$output .= '<ul class="framework_tabs"'.(isset($value['std']) && $value['std'] != ""?' data-std="head-' . esc_attr( $value['std'] ) . '"':'').'>';
					$k_a = 0;
					foreach ( $value['options'] as $key_h => $value_h ) {
						$k_a++;
						$output .= '<li><a title="' . esc_attr( $value_h ) . '" href="' . esc_attr( 'head-'.  $key_h ) . '">' . esc_html( $value_h ) . '</a></li>';
					}
					$output .= '</ul>';
				}
				break;
				
			case 'heading-2':
				if ( isset($value['end']) && $value['end'] == "end" ) {
					if ( isset($value['div']) && $value['div'] == "div" ) {
						$output .= '</div>';
					}else {
						$output .= '</div></div></div>';
					}
				}else {
					if ( isset($value['div']) && $value['div'] == "div" ) {
						$output .= '<div class="'.$wrap_class.'" id="'.(isset($value['id']) && $value['id'] != ""?"wrap_".$value['id']:"").'"'.( $condition ).( $operator ).'>';
						if ( isset($value['name']) ) {
							$output .= '<h4 class="vpanel-head-2">' . esc_html( $value['name'] ) . '</h4>';
						}
					}else {
						$id_class = '';
						$id_class = ! empty($value['id'])?$value['id']:(isset($value['name']) && $value['name'] != ""?$value['name']:"");
						$id_class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($id_class) );
						$output .= '<div'.($page == "options" || $page == "styling" || $page == "mobile" || $white_label_settings == true?" class='main-head'":"").(isset($value['id'])?' id="main-head-'.$value['id'].'"':'').'>
						<div'.(isset($value['id'])?' id="head-'.$value['id'].'"':'').' class="'.$options_group.(isset($value['id'])?' head-group head-'.$value['id']:'').'"'.( $condition ).( $operator ).'>';
						if ( isset($value['name']) ) {
							$output .= '<h4 class="vpanel-head-2">' . esc_html( $value['name'] ) . '</h4>';
						}
						$output .= '<div class="framework-group-2 ' . $id_class . '">';
					}
				}
				break;

			case 'group':
				if ( isset($value['end']) && $value['end'] == "end" ) {
					$output .= '</div></div>';
				}else {
					$id_class = '';
					$id_class = ! empty($value['id'])?$value['id']:(isset($value['name']) && $value['name'] != ""?$value['name']:"");
					$id_class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($id_class) );
					$id_class = ($id_class != ""?" ".$id_class:"");
					$output .= '<div'.(isset($value['id'])?' id="head-'.$value['id'].'"':'').' class="custom-group '.$options_group.(isset($value['id'])?' head-group head-'.$value['id']:'').'"'.( $condition ).( $operator ).'>';
					if ( isset($value['name']) ) {
						$output .= '<h4 class="vpanel-head-2">' . esc_html( $value['name'] ) . '</h4>';
					}
					$output .= '<div class="framework-group-2 ' . $id_class . '">';
				}
				break;
			}

			if (isset($value['type'])) {
				if ($value['type'] != "heading" && $value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != "group" && $value['type'] != "info" && $value['type'] != "uniq_id" && $value['type'] != "content" && $value['type'] != "html" && $value['type'] != 'hidden') {
					$output .= '</div></div>';
				}
			}

			echo stripcslashes($output);
		}
	}
}?>