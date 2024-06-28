<?php

/* @author    2codeThemes
*  @package   WPQA/framework
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Admin class */
class wpqa_admin {

	static function &_wpqa_admin_options($page = "options",$default = "") {
		static $options = null;
		if ( !$options ) {
	        // Load options from options.php file (if it exists)
	        $white_label_array = apply_filters("wpqa_white_label_array",array());
			$white_label_settings = false;
			$white_label_option = "";
			if (is_array($white_label_array) && !empty($white_label_array)) {
				foreach ($white_label_array as $value) {
					if ($page == $value) {
						$white_label_settings = true;
						$white_label_option = $value;
					}
				}
			}
	        if ( $page == "widgets" && function_exists( 'wpqa_admin_widgets' ) ) {
            	$options = wpqa_admin_widgets();
            }else if ( $page == "term" && function_exists( 'wpqa_admin_terms' ) ) {
            	$options = wpqa_admin_terms();
            }else if ( $page == "meta" && function_exists( 'wpqa_admin_meta' ) ) {
            	$options = wpqa_admin_meta();
            }else if ( $page == "options" && isset($_GET['page']) && $_GET['page'] == "options" && function_exists( 'wpqa_admin_setting_options' ) ) {
				$options = wpqa_admin_setting_options($default);
			}else if ( $page == "styling" && isset($_GET['page']) && $_GET['page'] == "styling" && function_exists( 'wpqa_admin_setting_styling' ) ) {
				$options = wpqa_admin_setting_styling($default);
			}else if ( $page == "mobile" && isset($_GET['page']) && $_GET['page'] == "mobile" && function_exists( 'wpqa_admin_setting_mobile' ) ) {
				$options = wpqa_admin_setting_mobile($default);
			}else if ( $white_label_settings == true && $page == $white_label_option && isset($_GET['page']) && $_GET['page'] == $white_label_option && function_exists( 'wpqa_admin_option_setting' ) ) {
				$options = wpqa_admin_option_setting($default,$page);
			}
	        // Allow setting/manipulating options via filters
	        $options = apply_filters( wpqa_prefix_theme.'_'.$page, $options, $page, $default );
		}
		return $options;
	}
}

/* Admin options */
class wpqa_admin_options {

    /* Hook in the scripts and styles */
    public function init($page = "options") {
    	$support_activate = wpqa_updater();
		if ($support_activate) {
			// Gets options to load
			if ($page == "meta") {
				wpqa_admin_meta();
			}else if ($page == "widgets") {
				wpqa_admin_widgets();
			}else if ($page == "terms") {
				wpqa_admin_terms();
			}else {
	    		$options = & wpqa_admin::_wpqa_admin_options($page);
	    	}

			// Checks if options are available
	    	add_action('admin_menu',array($this,'wpqa_add_admin'),13);
		}
    }

	/* Define menu options (still limited to appearance section) */
	function wpqa_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null ) {
	    global $menu, $admin_page_hooks, $_registered_pages, $_parent_pages;
	 
	    $menu_slug = plugin_basename( $menu_slug );
	 
	    $admin_page_hooks[$menu_slug] = sanitize_title( $menu_title );
	 
	    $hookname = get_plugin_page_hookname( $menu_slug, '' );
	 
	    if ( !empty( $function ) && !empty( $hookname ) && current_user_can( $capability ) )
	        add_action( $hookname, $function );
	 
	    if ( empty($icon_url) ) {
	        $icon_url = 'dashicons-admin-generic';
	        $icon_class = 'menu-icon-generic ';
	    } else {
	        $icon_url = set_url_scheme( $icon_url );
	        $icon_class = '';
	    }
	 
	    $new_menu = array( $menu_title, $capability, $menu_slug, $page_title, 'menu-top ' . $icon_class . $hookname, $hookname, $icon_url );
	 
	    if ( null === $position ) {
	        $menu[] = $new_menu;
	    } elseif ( isset( $menu[ "$position" ] ) ) {
	        $position = $position + substr( base_convert( md5( $menu_slug . $menu_title ), 16, 10 ) , -5 ) * 0.00001;
	        $menu[ "$position" ] = $new_menu;
	    } else {
	        $menu[ $position ] = $new_menu;
	    }
	 
	    $_registered_pages[$hookname] = true;
	 
	    // No parent as top level
	    $_parent_pages[$menu_slug] = false;
	 
	    return $hookname;
	}
	
	function wpqa_add_admin() {
		$support_activate = wpqa_updater();
		if ($support_activate) {
			$this->wpqa_menu_page(wpqa_name_theme.' Settings',wpqa_name_theme,'manage_options','options',array($this,'options_page'),'dashicons-admin-site');
			add_submenu_page('options',esc_html__('Styling Settings','wpqa'),esc_html__('Styling','wpqa'),'manage_options','styling',array($this,'styling_page'));
			if (function_exists('mobile_api_options')) {
				add_submenu_page('options',esc_html__('Mobile APP','wpqa'),esc_html__('Mobile APP','wpqa'),'manage_options','mobile',array($this,'mobile_page'));
			}
			$white_label_array = apply_filters("wpqa_white_label_array",array());
			$white_label_settings = false;
			if (is_array($white_label_array) && !empty($white_label_array)) {
				foreach ($white_label_array as $value) {
					if ($value != "") {
						add_submenu_page('options',ucfirst($value).' '.esc_html__('Settings','wordpress'),ucfirst($value),'manage_options',$value,function () use ($value) {
							if (isset($_GET['page']) && $_GET['page'] == $value) {
								$this->setting_pages(wpqa_prefix_theme."_".$value."_options",$value);
							}
                		});
					}
				}
			}
		}
	}

	/* Options panel */
	function options_page() {
		$this->setting_pages("","options");
	}

	/* Styling panel */
	function styling_page() {
		$this->setting_pages(wpqa_styling_options,"styling");
	}

	/* Mobile panel */
	function mobile_page() {
		$this->setting_pages(wpqa_mobile_options,"mobile");
	}

	/* Builds out the options panel */
	function setting_pages($option_name,$page_name) {
		do_action(wpqa_prefix_theme.'_options_page');
		if (defined("wpqa_new_plugin_version") && wpqa_plugin_version != wpqa_new_plugin_version) {?>
			<div class="notice notice-error notice-update-wpqa framework-notice settings-error"> 
				<p>
					<strong>
						<span><?php esc_html_e("Required update plugin:","wpqa")?> <em>WPQA - The WordPress Questions And Answers Plugin</em>.</span>
						<span><a href="<?php echo admin_url('admin.php?page=install-required-plugins&plugin_status=update')?>"><?php esc_html_e("Begin updating plugin","wpqa")?></a></span>
					</strong>
				</p>
			</div>
		<?php }?>
		<div id="framework-admin-wrap" class="framework-admin">
			<?php if (!function_exists('mobile_api_options')) {?>
				<a class="app-img" href="https://2code.info/checkout/pay_for_apps/33664/" target="_blank"><img alt="<?php echo wpqa_name_theme?> Mobile Application" src="https://drive.2code.info/discount/960x100-<?php echo wpqa_prefix_theme?>.png"></a>
				<section id="footer_call_to_action" class="gray_section call_to_action">
					<div class="container main_content_area">
						<div class="row section">
							<div class="section_container col col12">
								<div class="section_inner_container">
									<div class="row section_inner">
										<div class="col col7"> 
											<div class="main_section_left_title main_section_title">Test Application!</div>
											<div class="main_section_left_content main_section_content">Test <?php echo wpqa_name_theme?> application demo on Google Play and App Store.</div>
										</div>
										<div class="col col5">
											<div class="row">
												<div class="col col6 col-app">
													<a target="_blank" title="Download Android App" href="<?php echo wpqa_android_url()?>">
														<img alt="Play Store" src="https://2code.info/mobile/google_play.png">
													</a>
												</div>
												<div class="col col6 col-app">
													<a target="_blank" href="<?php echo wpqa_ios_url()?>" title="Download IOS App">
														<img alt="App Store" src="https://2code.info/mobile/app_store.png">
													</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
			<?php }?>
			<form action="<?php echo admin_url('admin.php?page='.$page_name)?>" id="main_options_form" method="post">
				<div class="framework-admin-header">
					<a href="<?php echo wpqa_theme_url_tf?>" target="_blank"><i class="dashicons-before dashicons-admin-tools"></i><?php echo wpqa_name_theme.($page_name != ""?" - ".$page_name:"")?></a>
					<div class="framework_search">
						<input type="search" placeholder="<?php esc_attr_e('Type Search Words','wpqa')?>">
						<div class="search-results results-empty"></div>
					</div>
					<input type="submit" class="button-primary framework_save" name="update_options" value="<?php esc_attr_e( 'Save Options', "wpqa" ); ?>">
					<div class="framework_social">
						<ul>
							<li class="framework_social_facebook"><a class="framework_social_f" href="https://www.facebook.com/2code.info" target="_blank"><i class="dashicons dashicons-facebook"></i></a></li>
							<li class="framework_social_twitter"><a class="framework_social_t" href="https://www.twitter.com/2codeThemes" target="_blank"><i class="dashicons dashicons-twitter"></i></a></li>
							<li class="framework_social_site"><a class="framework_social_e" href="https://2code.info/" target="_blank"><i class="dashicons dashicons-email-alt"></i></a></li>
							<li class="framework_social_docs"><a class="framework_social_s" href="https://2code.info/docs/<?php echo wpqa_prefix_theme?>/" target="_blank"><i class="dashicons dashicons-sos"></i></a></li>
						</ul>
					</div>
					<div class="clear"></div>
				</div>
				<div class="framework-admin-content">
					<h2 class="nav-tab-wrapper"><?php echo wpqa_admin_fields_class::wpqa_admin_tabs($page_name); ?></h2>
					<?php settings_errors( 'options-framework' ); ?>
					<div id="framework-admin-metabox" class="metabox-holder">
						<div id="framework-admin" class="framework-main postbox">
							<?php wpqa_admin_fields_class::wpqa_admin_fields(array(),$option_name,$page_name);
							wp_nonce_field('saving_nonce','saving_nonce',true,true)?>
							<div class="vpanel-loading"></div>
							<div id="ajax-saving"><i class="dashicons dashicons-yes"></i><?php esc_html_e("Saved","wpqa")?></div>
							<div id="ajax-reset"><i class="dashicons dashicons-info"></i><?php esc_html_e("Reseted","wpqa")?></div>
							<div id="ajax-load"><i class="dashicons dashicons-info"></i><?php esc_html_e("Loading the page and reclick on the button again","wpqa")?></div>
						</div><!-- End container -->
					</div>
					<?php do_action(wpqa_prefix_theme.'_admin_after');?>
				</div>
				<div class="clear"></div>
				<div class="framework-admin-footer">
					<input type="submit" class="button-primary framework_save" name="update_options" value="<?php esc_attr_e( 'Save Options', "wpqa" ); ?>">
					<input type="hidden" name="action" id="framework-admin-action" value="wpqa_update_options">
					<input type="hidden" id="framework-admin-settings" value="<?php echo esc_attr($page_name)?>">
					<div id="loading"></div>
					<input type="submit" class="reset-button button-secondary" id="reset_c" name="reset" value="<?php esc_attr_e( 'Restore Defaults', "wpqa" ); ?>">
					<div class="clear"></div>
				</div>
			</form>
		</div><!-- End wrap -->
		<?php
	}

	/* Get the default values for all the theme options */
	function get_default_values($page = "options") {
		$output = array();
		$config = & wpqa_admin::_wpqa_admin_options($page,"default");
		foreach ( (array) $config as $option ) {
			if ( ! isset( $option['id'] ) ) {
				continue;
			}
			if ( ! isset( $option['std'] ) ) {
				continue;
			}
			if ( ! isset( $option['type'] ) ) {
				continue;
			}
			$output[$option['id']] = $option['std'];
		}
		return $output;
	}
}

/* Admin fields */
class wpqa_admin_fields_class {

	/**
	 * Generates the tabs that are used in the options menu
	 */
	static function wpqa_admin_tabs($page = "options",$options_arrgs = array(),$post_id = "") {
		$counter = 0;
		$options = $options_arrgs;
		if (empty($options_arrgs)) {
			$options = & wpqa_admin::_wpqa_admin_options($page);
		}
		if (isset($options) && is_array($options) && !empty($options)) {
			$menu = $class = $target = '';
			$wp_page_template = ($page == "meta" && isset($post_id)?get_post_meta($post_id,"_wp_page_template",true):"");
			foreach ( $options as $value ) {
				// Heading for Navigation
				if ( isset($value['type']) && $value['type'] == "heading" ) {
					$counter++;
					$class = ! empty( $value['id'] ) ? $value['id'] : $value['name'];
					$class = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower($class) ).'-tab';
					if ( ! array_key_exists( 'template', $value ) || ! is_string( $value['template'] ) ) {
						$value['template'] = '';
					}
					$template = empty( $value['template'] ) ? '' : ' data-template="'. esc_attr( $value['template'] ) .'"';
					if (isset($value['template']) && $value['template'] != "" && $value['template'] != $wp_page_template) {
						$class .= ' hide';
					}
					if (isset( $value['link'] ) && $value['link'] != '') {
						$target =  ' target="_blank"';
						$class .= ' custom-link';
					}
					$link = isset( $value['link'] ) && $value['link'] != '' ? esc_url($value['link']) : esc_attr('#options-group-'.$counter);
					$menu .= '<a'.$target.$template.' id="options-group-'.$counter.'-tab" class="nav-tab '.$class.'" title="'.esc_attr($value['name']).'" href="'.$link.'"><span class="options-name'.(isset($value['new']) && $value['new'] != ""?' options-name-new':'').'">'.esc_html($value['name']).(isset($value['new']) && $value['new'] != ''?'<span>'.esc_html__('New','wpqa').'</span>':'').'</span>'.(isset($value['icon']) && $value['icon'] != ''?'<span class="dashicons dashicons-'.esc_attr($value['icon']).'"></span>':'').'</a>';
				}
			}
			return $menu;
		}
	}

	/**
	 * Generates the options fields that are used in the form.
	 */
	static function wpqa_admin_fields($settings = array(),$option_name = "",$page = "options",$post_term = null,$options_arrgs = array()) {
		global $pagenow;
	
		wpqa_options_fields($settings,$option_name,$page,$post_term,$options_arrgs);

		// Outputs closing div if there tabs
		$white_label_array = apply_filters("wpqa_white_label_array",array());
		$white_label_settings = false;
		if (is_array($white_label_array) && !empty($white_label_array)) {
			foreach ($white_label_array as $value) {
				if ($page = $value) {
					$white_label_settings = true;
				}
			}
		}
		if ( $page == "options" || $page == "styling" || $page == "mobile" || $page == "meta" || (($pagenow == "admin.php" || $pagenow == "themes.php") && $white_label_settings == true) ) {
			echo '</div>';
		}
	}

}
/* Field is visible */
function wpqa_field_is_visible( $condition, $operator, $fields, $values ) {
	
	if ( ! is_string( $condition ) || empty( $condition ) ) {
		return true;
	}

	if ( ! is_array( $fields ) ) {
		$fields = array();
	}

	if ( ! is_array( $values ) ) {
		$values = array();
	}
	
	$field_values = array();
	foreach ( $fields as $v ) {
		if (isset($v['id'])) {
			$field_values[ $v['id'] ] = array_key_exists( $v['id'], $values ) ? $values[ $v['id'] ] : ( array_key_exists( 'std', $v ) ? $v['std'] : '' );
		}
	}
	
	$bool_arr = array();
	$cond_arr = array_map( function($v) { $l = substr($v, -1); if ( $l != ')' ) { $v .= ')'; } return $v; }, explode( '),', $condition ) );
	
	foreach ( $cond_arr as $v ) {

		$bool = false;

		preg_match( '#^([a-z0-9_]+)\:(not|is|has|has_not)\(([a-z0-9-_\,]+)\)$#', trim( $v ), $match );

		if ( ! empty( $match ) ) {

			$id = $match[1];
			$op = $match[2];
			$val = $match[3];

			if ( in_array( $op, array( 'is', 'not' ) ) ) {
				if ($val == "empty" && ($op == 'is' || $op == 'not')) {
					if ($op == 'not') {
						$bool = ( $field_values[$id] != "" );
					}else {
						$bool = ( $field_values[$id] == "" );
					}
				}else {
					$bool = ( array_key_exists( $id, $field_values ) && $field_values[$id] == $val );
					
					if ( $op == 'not' ) {
						$bool = ( ! $bool );
					}
				}
			}else if ( in_array( $op, array( 'has', 'has_not' ) ) ) {
				if ( ! array_key_exists( $id, $field_values ) ) {
					$field_values[$id] = array();
				}

				if ( is_string( $field_values[$id] ) ) {
					$field_values[$id] = array_filter( explode( ',', $field_values[$id] ), function( $mv ) { return trim( $mv ); } );
				}

				if ( ! is_array( $field_values[$id] ) ) {
					$field_values[$id] = array();
				}
				
				if (isset($field_values[$id][$val])) {
					$bool = ((isset($field_values[$id][$val]["value"]) && $field_values[$id][$val]["value"] == $val) || (isset($field_values[$id][$val]) && $field_values[$id][$val] == 1) || (isset($field_values[$id][$val]) && $field_values[$id][$val] == $val));
				}else {
					$val = array_filter( explode( ',', $val ), function( $mv ) { return trim( $mv ); } );
					$bool = ((array_intersect($val,$field_values[$id]) == $val) || (count($field_values[$id]) == 1 && end($field_values[$id]) == 'all'));
				}
				
				if ( $op == 'has_not' ) {
					$bool = ( ! $bool );
				}
			}

		}

		$bool_arr[] = $bool;
	}

	if ( $operator == 'or' ) {
		return in_array( true, $bool_arr, true );
	}else {
		return ( ! in_array( false, $bool_arr, true ) );
	}
}
/* Categories checklist */
function wpqa_categories_checklist_admin($args = array()) {
	$defaults = array(
		'selected_cats' => false,
		'taxonomy' => 'category',
	);
	$params = apply_filters( 'wp_terms_checklist_args', $args, 0 );
	$r = wp_parse_args( $params, $defaults );
	require_once ABSPATH . 'wp-admin/includes/class-walker-category-checklist.php';
	$walker = new Walker_Category_Checklist;
	$taxonomy = $r['taxonomy'];
	$args = array( 'taxonomy' => $taxonomy );
	$args['name'] = $r['name'];
	$args['id'] = $r['id'];
	$args['selected_cats'] = $r['selected_cats'];
	$exclude = apply_filters('wpqa_exclude_question_category',array());
	$categories = (array) get_terms( $taxonomy, array_merge($exclude,array( 'get' => 'all' ) ) );
	
	$output = call_user_func_array( array( $walker, 'walk' ), array( $categories, 0, $args ) );
	$output = str_replace( 'name="post_category[]"', 'name="'.$args['name'].'[]"', $output );
	$output = str_replace( 'name="tax_input['.$taxonomy.'][]"', 'name="'.$args['name'].'[]"', $output );
	$output = str_replace( '<li id="'.$taxonomy.'-', '<li id="'.$args['name'].$taxonomy.'-', $output );
	$output = str_replace( 'id="'.$taxonomy.'-', 'id="'.$args['name'].$taxonomy.'-', $output );
	$output = str_replace( 'id="in-'.$taxonomy.'-', 'id="'.$args['name'].'in-'.$taxonomy.'-', $output );
	$output = str_replace( '<label class="selectit">', '<label class="selectit switch widget-switch">', $output );
	
	return $output;
}
/* Option images */
function wpqa_option_images($value_id = '',$value_width = '',$value_height = '',$value_options = '',$val = '',$value_class = '',$option_name = '',$name_id = '',$data_attr = '',$add_value_id = '') {
	$output = '';
	$name = $option_name .($add_value_id != 'no'?'['. $value_id .']':'');
	$width = (isset($value_width) && $value_width != ""?" width='".$value_width."' style='box-sizing: border-box;-moz-box-sizing: border-box;-weblit-box-sizing: border-box;'":"");
	$height = (isset($value_height) && $value_height != ""?" height='".$value_height."' style='box-sizing: border-box;-moz-box-sizing: border-box;-weblit-box-sizing: border-box;'":"");
	foreach ( $value_options as $key => $option ) {
		$selected = '';
		if ( $val != '' && ($val == $key) ) {
			$selected = ' framework-radio-img-selected';
		}
		$output .= '<div>
			<div class="framework-radio-img-label">' . esc_html( $key ) . '</div>
			<input type="radio" data-attr="' . esc_attr( $data_attr ) . '" class="framework-radio-img-radio framework-form-control" value="' . esc_attr( $key ) . '" '.($name_id != "no"?' id="' . esc_attr( $value_id .'_'. $key) . '" name="' . esc_attr( $name ) . '"':'').' '. checked( $val, $key, false ) .'>
			<img'.$width.$height.' src="' . esc_url( $option ) . '" data-value="' . esc_attr( $key ) . '" alt="' . $option .'" class="framework-radio-img-img '.(isset($value_class)?esc_attr($value_class):'').'' . $selected .'" '.($name_id != "no"?'onclick="document.getElementById(\''. esc_attr($value_id .'_'. $key) .'\').checked=true;"':'').'>
		</div>';
	}
	return $output;
}
/* Option sliderui */
function wpqa_option_sliderui($value_min = '',$value_max = '',$value_step = '',$value_edit = '',$val = '',$value_id = '',$option_name = '',$element = '',$bracket = '',$widget = '') {
	$output = $min = $max = $step = $edit = '';
	
	if(!isset($value_min)){ $min  = '0'; }else{ $min = $value_min; }
	if(!isset($value_max)){ $max  = $min + 1; }else{ $max = $value_max; }
	if(!isset($value_step)){ $step  = '1'; }else{ $step = $value_step; }
	
	if (!isset($value_edit)) { 
		$edit  = ' readonly="readonly"'; 
	}else {
		$edit  = '';
	}
	
	if ($val == '') $val = $min;

	$val = (int)$val;
	
	//values
	$data = 'data-id="'.(isset($element) && $element != ""?$element:$value_id).'" data-val="'.$val.'" data-min="'.$min.'" data-max="'.$max.'" data-step="'.$step.'"';
	
	//html output
	$output .= '<input type="text" name="'.$option_name.'" id="'.(isset($element) && $element != ""?$element:$value_id).'" value="'. $val .'" class="mini framework-form-control" '. $edit .' />';
	$output .= '<div id="'.(isset($element) && $element != ""?$element:$value_id).'-slider" class="v_sliderui" '. $data .'></div>';
	return $output;
}?>