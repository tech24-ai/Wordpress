<?php /* Admin options */
add_filter("wpqa_header_setting","discy_header_setting",1,7);
function discy_header_setting($options,$options_pages,$new_roles,$imagepath,$imagepath_theme,$new_sidebars,$menus) {
	$options[] = array(
		'name' => esc_html__('Activate all the header?','discy'),
		'desc' => esc_html__('Select ON to enable header.','discy'),
		'id'   => 'activate_header',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'activate_header:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Header height','discy'),
		'desc'    => esc_html__('Choose the header height.','discy'),
		'id'      => 'header_height',
		'std'     => 'large',
		'type'    => 'radio',
		'options' => array("large" => esc_html__("Large","discy"),"small" => esc_html__("Small","discy"))
	);
	
	$options[] = array(
		'name'    => esc_html__('Header style','discy'),
		'desc'    => esc_html__('Choose the header style.','discy'),
		'id'      => 'header_style',
		'std'     => 'normal',
		'type'    => 'radio',
		'options' => array("normal" => esc_html__("Normal","discy"),"simple" => esc_html__("Simple","discy"))
	);
	
	$options[] = array(
		'name'    => esc_html__('Header skin','discy'),
		'desc'    => esc_html__('Choose the header skin.','discy'),
		'id'      => 'header_skin',
		'std'     => 'dark',
		'type'    => 'radio',
		'options' => array("dark" => esc_html__("Dark","discy"),"light" => esc_html__("Light","discy"),"colored" => esc_html__("Colored","discy"))
	);

	$options[] = array(
		'name' => esc_html__('Logo Custom Link URL','discy'),
		'desc' => esc_html__('Enter a custom URL the site logo should link to. Leave empty to let logo link to the home page.','discy'),
		'id'   => 'custom_logo_link',
		'type' => 'text'
	);
	
	$options[] = array(
		'name'    => esc_html__('Logo display','discy'),
		'desc'    => esc_html__('Choose the logo display.','discy'),
		'id'      => 'logo_display',
		'std'     => 'custom_image',
		'type'    => 'radio',
		'options' => array("display_title" => esc_html__("Display site title","discy"),"custom_image" => esc_html__("Custom Image","discy"))
	);
	
	$options[] = array(
		'name'      => esc_html__('Upload the logo','discy'),
		'desc'      => esc_html__('Upload your custom logo.','discy'),
		'id'        => 'logo_img',
		'std'       => $imagepath_theme."logo.png",
		'type'      => 'upload',
		'condition' => 'logo_display:is(custom_image)',
		'options'   => array("height" => "logo_height","width" => "logo_width"),
	);
	
	$options[] = array(
		'name'      => esc_html__('Upload the retina logo','discy'),
		'desc'      => esc_html__('Upload your custom retina logo.','discy'),
		'id'        => 'retina_logo',
		'std'       => $imagepath_theme."logo-2x.png",
		'type'      => 'upload',
		'condition' => 'logo_display:is(custom_image)'
	);
	
	$options[] = array(
		'name'      => esc_html__('Logo height','discy'),
		"id"        => "logo_height",
		"type"      => "sliderui",
		'std'       => '45',
		"step"      => "1",
		"min"       => "0",
		"max"       => "80",
		'condition' => 'logo_display:is(custom_image)'
	);
	
	$options[] = array(
		'name'      => esc_html__('Logo width','discy'),
		"id"        => "logo_width",
		"type"      => "sliderui",
		'std'       => '137',
		"step"      => "1",
		"min"       => "0",
		"max"       => "170",
		'condition' => 'logo_display:is(custom_image)'
	);
	
	$options[] = array(
		'name' => esc_html__('Header search option','discy'),
		'desc' => esc_html__('Select ON to enable header search.','discy'),
		'id'   => 'header_search',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'      => esc_html__('Activate the bigger search bar?','discy'),
		'desc'      => esc_html__('Select ON to enable header bigger search bar.','discy'),
		'id'        => 'big_search',
		'condition' => 'header_search:not(0)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'header_style:is(simple)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Simple header button','discy'),
		'desc'    => esc_html__('Choose simple header button style from here.','discy'),
		'id'      => 'header_button',
		'options' => array(
			'question' => esc_html__('Ask A Question','discy'),
			'poll'     => esc_html__('Add A Poll','discy'),
			'post'     => esc_html__('Add A Post','discy'),
			'custom'   => esc_html__('Custom link','discy'),
		),
		'std'     => 'question',
		'type'    => 'radio'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'header_button:is(custom)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?','discy'),
		'id'      => 'header_button_target',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page","discy"),"new_page" => esc_html__("New page","discy"))
	);
	
	$options[] = array(
		'name' => esc_html__('Type the button link','discy'),
		'id'   => 'header_button_link',
		'type' => 'text'
	);
	
	$options[] = array(
		'name' => esc_html__('Type the button text','discy'),
		'id'   => 'header_button_text',
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'name' => esc_html__('User icon profile or login and register buttons','discy'),
		'desc' => esc_html__('Select ON to enable header user login area.','discy'),
		'id'   => 'header_user_login',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'condition' => 'header_user_login:not(0)',
		'type'      => 'heading-2'
	);
	
	$options[] = array(
		'name'    => esc_html__('Header user login style','discy'),
		'desc'    => esc_html__('Choose header user login style.','discy'),
		'id'      => 'user_login_style',
		'std'     => 'light',
		'type'    => 'radio',
		'options' => array("light" => esc_html__("Light","discy"),"dark" => esc_html__("Dark","discy"))
	);

	$options[] = array(
		'name' => esc_html__('The settings of header profile menu on the WordPress menus, on menu named Header Profile Menu','discy'),
		'type' => 'info'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'div'       => 'div',
		'type'      => 'heading-2',
		'condition' => 'active_message:not(0),header_style:is(simple)'
	);
	
	$options[] = array(
		'name' => esc_html__('Header messages','discy'),
		'desc' => esc_html__('Select ON to enable header messages.','discy'),
		'id'   => 'header_messages',
		'std'  => 'on',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'div'       => 'div',
		'type'      => 'heading-2',
		'condition' => 'active_message:not(0),header_style:is(simple),header_messages:not(0)'
	);
	
	$options[] = array(
		'name'    => esc_html__('Header messages style','discy'),
		'desc'    => esc_html__('Choose header messages style.','discy'),
		'id'      => 'messages_style',
		'std'     => 'light',
		'type'    => 'radio',
		'options' => array("light" => esc_html__("Light","discy"),"dark" => esc_html__("Dark","discy"))
	);
	
	$options[] = array(
		'name' => esc_html__('Header messages number','discy'),
		'desc' => esc_html__('Put the header messages number.','discy'),
		'id'   => 'messages_number',
		'std'  => 5,
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$discoura_style = discy_options("discoura_style");
	if ($discoura_style == "on") {
		$options[] = array(
			'name' => esc_html__('If you activate the discoura style, the notification icon on the header will not showing.','discy'),
			'type' => 'info'
		);
	}else {
		$options[] = array(
			'name' => esc_html__('Header notifications','discy'),
			'desc' => esc_html__('Select ON to enable header notifications.','discy'),
			'id'   => 'header_notifications',
			'std'  => 'on',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'div'       => 'div',
			'type'      => 'heading-2',
			'condition' => 'active_notifications:not(0),header_notifications:not(0)'
		);
		
		$options[] = array(
			'name'    => esc_html__('Header notifications style','discy'),
			'desc'    => esc_html__('Choose header notifications style.','discy'),
			'id'      => 'notifications_style',
			'std'     => 'light',
			'type'    => 'radio',
			'options' => array("light" => esc_html__("Light","discy"),"dark" => esc_html__("Dark","discy"))
		);
		
		$options[] = array(
			'name' => esc_html__('Header notifications number','discy'),
			'desc' => esc_html__('Put the header notifications number.','discy'),
			'id'   => 'notifications_number',
			'std'  => 5,
			'type' => 'text'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
	}
	
	$options[] = array(
		'name' => esc_html__('Fixed header option','discy'),
		'desc' => esc_html__('Select ON to enable fixed header.','discy'),
		'id'   => 'header_fixed',
		'type' => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);
	return $options;
}
add_filter("wpqa_after_options_setting","discy_after_options_setting",1,7);
function discy_after_options_setting($options,$options_pages,$new_roles,$imagepath,$imagepath_theme,$new_sidebars,$menus) {
	$options[] = array(
		'name' => esc_html__('Discy Change log','discy'),
		'icon' => 'hammer',
		'type' => 'heading',
		'link' => 'https://2code.info/docs/discy/change-log/',
	);
	
	$options[] = array(
		'name' => esc_html__('Mobile APP Change log','discy'),
		'icon' => 'lightbulb',
		'type' => 'heading',
		'link' => 'https://2code.info/docs/mobile/change-log/',
	);
	
	$options[] = array(
		'name' => esc_html__('Support','discy'),
		'icon' => 'megaphone',
		'type' => 'heading',
		'link' => 'https://2code.info/support/',
	);
	return $options;
}
add_filter("wpqa_options_advanced_setting","discy_options_advanced_setting");
function discy_options_advanced_setting($options) {
	$ask_me = discy_options("ask_me");
	$options[] = array(
		'name'    => esc_html__('Choose the your old theme if you use one of our themes before','discy'),
		'id'      => 'old_themes',
		'options' => array(
			'nothing' => esc_html__('No thing, just a new site','discy'),
			'ask_me'  => esc_html__('Ask Me','discy'),
			'himer'   => esc_html__('Himer','discy'),
		),
		'std'     => ($ask_me == 'on'?'ask_me':'nothing'),
		'type'    => 'radio'
	);
	if (!has_wpqa() || (has_wpqa() && wpqa_plugin_version < "5.9.2")) {
		$options[] = array(
			'name' => esc_html__('Import Setting','discy'),
			'desc' => esc_html__('Put here the import setting','discy'),
			'id'   => 'import_setting',
			'type' => 'import'
		);
	}
	return $options;
}?>