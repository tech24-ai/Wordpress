<?php

/* @author    2codeThemes
*  @package   WPQA/options
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function wpqa_admin_setting_styling($default = "") {
	if (is_admin() || $default == "default") {
		$options = array();
		
		// Pull all the sidebars into an array
		$new_sidebars = wpqa_registered_sidebars();
		
		// Menus
		$menus = array();
		$all_menus = get_terms('nav_menu',array('hide_empty' => true));
		foreach ($all_menus as $menu) {
		    $menus[$menu->term_id] = $menu->name;
		}

		// Background Defaults
		$background_defaults = array(
			'color'      => '',
			'image'      => '',
			'repeat'     => 'repeat',
			'position'   => 'top center',
			'attachment' => 'scroll'
		);

		// Knowledgebase
		$activate_knowledgebase = apply_filters("wpqa_activate_knowledgebase",false);
		
		// If using image radio buttons, define a directory path
		$imagepath =  get_template_directory_uri().'/admin/images/';
		$imagepath_theme =  get_template_directory_uri().'/images/';

		$options[] = array(
			'name' => esc_html__('Sidebar','wpqa'),
			'id'   => 'sidebar',
			'icon' => 'align-none',
			'type' => 'heading'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$sidebar_elements = array(
			array(
				"type" => "text",
				"id"   => "name",
				"name" => esc_html__('Sidebar name','wpqa')
			),
		);
		
		$options[] = array(
			'id'      => "sidebars",
			'type'    => "elements",
			'sort'    => "no",
			'button'  => esc_html__('Add a new sidebar','wpqa'),
			'options' => $sidebar_elements,
		);
		
		$options[] = array(
			'name'    => esc_html__('Sidebar layout','wpqa'),
			'id'      => "sidebar_layout",
			'std'     => (has_himer() || has_knowly()?"right":"menu_sidebar"),
			'type'    => "images",
			'options' => array(
				'menu_sidebar' => $imagepath.'menu_sidebar.jpg',
				'right'        => $imagepath.'sidebar_right.jpg',
				'full'         => $imagepath.'sidebar_no.jpg',
				'left'         => $imagepath.'sidebar_left.jpg',
				'centered'     => $imagepath.'centered.jpg',
				'menu_left'    => $imagepath.'menu_left.jpg',
			)
		);
		
		$options[] = array(
			'div'       => 'div',
			'condition' => 'sidebar_layout:not(full),sidebar_layout:not(centered),sidebar_layout:not(menu_left)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Sidebar','wpqa'),
			'id'      => "sidebar_home",
			'options' => $new_sidebars,
			'type'    => 'select'
		);
		
		$options[] = array(
			'name'    => esc_html__('Sticky sidebar','wpqa'),
			'id'      => 'sticky_sidebar',
			'std'     => 'side_menu_bar',
			'type'    => 'radio',
			'options' => array(
				'sidebar'       => esc_html__('Sidebar','wpqa'),
				'nav_menu'      => esc_html__('Side menu (If enabled)','wpqa'),
				'side_menu_bar' => esc_html__('Sidebar & Side menu (If enabled)','wpqa'),
				'no_sidebar'    => esc_html__('Not active','wpqa'),
			)
		);
		
		$options[] = array(
			'name' => esc_html__('Widget icons enable or disable','wpqa'),
			'id'   => 'widget_icons',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		if (has_himer() || has_knowly()) {
			$options[] = array(
				'name'    => esc_html__('Sidebar style','wpqa'),
				'id'      => 'sidebar_style',
				'std'     => 'style_1',
				'type'    => 'radio',
				'options' => array(
					'style_1' => esc_html__('Style 1','wpqa'),
					'style_2' => esc_html__('Style 2','wpqa'),
				)
			);
		}
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'div'       => 'div',
			'operator'  => 'or',
			'condition' => 'sidebar_layout:is(menu_sidebar),sidebar_layout:is(menu_left)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Sidemenu style','wpqa'),
			'id'      => 'left_area',
			'std'     => 'menu',
			'type'    => 'radio',
			'options' => 
				array(
					"menu"    => esc_html__("Menu","wpqa"),
					"sidebar" => esc_html__("Sidebar","wpqa")
				)
		);
		
		$options[] = array(
			'name'      => esc_html__('Choose the left menu style','wpqa'),
			'id'        => "left_menu_style",
			'options'   => array('style_1' => 'Style 1','style_2' => 'Style 2','style_3' => 'Style 3'),
			'type'      => 'radio',
			'std'       => 'style_1',
			'condition' => 'left_area:not(sidebar)',
		);
		
		$options[] = array(
			'name'      => esc_html__('Sidebar 2','wpqa'),
			'id'        => "sidebar_home_2",
			'options'   => $new_sidebars,
			'type'      => 'select',
			'condition' => 'left_area:is(sidebar)',
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Sidebar on the mobile enable or disable','wpqa'),
			'desc' => esc_html__('Select ON to hide the sidebar on the mobile.','wpqa'),
			'id'   => 'hide_sidebar_mobile',
			'type' => 'checkbox',
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'    => esc_html__('Styling & Typography','wpqa'),
			'id'      => 'styling',
			'icon'    => 'art',
			'type'    => 'heading',
			'std'     => 'styling',
			'options' => array(
				"styling"    => esc_html__('Styling','wpqa'),
				"typography" => esc_html__('Typography','wpqa')
			)
		);
		
		$options[] = array(
			'name' => esc_html__('Styling','wpqa'),
			'id'   => 'styling',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Choose the site width','wpqa'),
			"id"   => "site_width",
			"type" => "sliderui",
			"std"  => "1170",
			"step" => "10",
			"min"  => "1170",
			"max"  => "1300"
		);

		if (has_discy()) {
			$options[] = array(
				'name' => esc_html__('Discoura style','wpqa'),
				'desc' => esc_html__('Select ON to activate the Discoura style','wpqa'),
				'id'   => 'discoura_style',
				'type' => 'checkbox',
			);
			
			$options[] = array(
				'name'    => esc_html__('Site style','wpqa'),
				'id'      => 'site_style',
				'std'     => 'none',
				'type'    => 'radio',
				'options' => 
					array(
						"none"    => esc_html__("Normal style","wpqa"),
						"style_1" => esc_html__("Boxed style 1","wpqa"),
						"style_2" => esc_html__("Boxed style 2","wpqa"),
						"style_3" => esc_html__("Boxed style 3 - with left menu only or sidebar only","wpqa"),
						"style_4" => esc_html__("Boxed style 4 - with left menu only or sidebar only","wpqa"),
					)
			);
		}
		
		$options[] = array(
			'name'    => esc_html__("Light/dark",'wpqa'),
			'desc'    => esc_html__("Light/dark for the site.",'wpqa'),
			'id'      => "site_skin_l",
			'std'     => "light",
			'type'    => "images",
			'options' => array(
				'light' => $imagepath.'light.jpg',
				'dark'  => $imagepath.'dark.jpg'
			)
		);
		
		$options[] = array(
			'name' => esc_html__('Skin switcher of dark and light enable or disable','wpqa'),
			'desc' => esc_html__('Select ON to enable the switcher of dark and light.','wpqa'),
			'id'   => 'skin_switcher',
			'std'  => 'on',
			'type' => 'checkbox'
		);

		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'skin_switcher:not(0)',
			'div'       => 'div'
		);

		if (has_himer() || has_knowly()) {
			$options[] = array(
				'name'    => esc_html__('Skin switcher of dark and light','wpqa'),
				'desc'    => esc_html__('Select ON to enable the switcher of dark and light.','wpqa'),
				'id'      => 'skin_switcher_position',
				'std'     => 'header',
				'options' => array(
					'header' => esc_html__('Header','wpqa'),
					'footer' => esc_html__('Footer','wpqa')
				),
				'type'    => 'radio'
			);
		}

		$options[] = array(
			'name' => esc_html__('Custom logo for the dark skin enable or disable','wpqa'),
			'id'   => 'custom_dark_logo',
			'type' => 'checkbox'
		);

		$options[] = array(
			'type'      => 'heading-2',
			'condition' => 'custom_dark_logo:not(0)',
			'div'       => 'div'
		);

		$logo_display = wpqa_options("logo_display");
		if ($logo_display == "custom_image") {
			$options[] = array(
				'name' => esc_html__('Upload the logo for the dark skin','wpqa'),
				'desc' => esc_html__('Upload your custom logo for the dark skin.','wpqa'),
				'id'   => 'dark_logo_img',
				'type' => 'upload'
			);
			
			$options[] = array(
				'name' => esc_html__('Upload the retina logo for the dark skin','wpqa'),
				'desc' => esc_html__('Upload your custom retina logo for the dark skin.','wpqa'),
				'id'   => 'dark_retina_logo',
				'type' => 'upload'
			);
		}

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Custom color for the dark skin enable or disable','wpqa'),
			'id'   => 'custom_dark_color',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'      => esc_html__('Choose the primary color for the dark skin','wpqa'),
			'id'        => 'dark_color',
			'condition' => 'custom_dark_color:not(0)',
			'type'      => 'color'
		);

		$options[] = array(
			'type' => 'heading-2',
			'div'  => 'div',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose Your Skin','wpqa'),
			'class'   => "site_skin",
			'id'      => "site_skin",
			'std'     => "default",
			'type'    => "images",
			'options' => array(
				'default'    => $imagepath.'default.jpg',
				'violet'     => $imagepath.'violet.jpg',
				'bright_red' => $imagepath.'bright_red.jpg',
				'green'      => $imagepath.'green.jpg',
				'red'        => $imagepath.'red.jpg',
				'cyan'       => $imagepath.'cyan.jpg',
				'blue'       => $imagepath.'blue.jpg',
			)
		);
		
		$options[] = array(
			'name' => esc_html__('Primary Color','wpqa'),
			'id'   => 'primary_color',
			'type' => 'color'
		);
		
		$options[] = array(
			'name'    => esc_html__('Background Type','wpqa'),
			'id'      => 'background_type',
			'std'     => 'none',
			'type'    => 'radio',
			'options' => 
				array(
					"none"              => esc_html__("None","wpqa"),
					"patterns"          => esc_html__("Patterns","wpqa"),
					"custom_background" => esc_html__("Custom Background","wpqa")
				)
		);
		
		$options[] = array(
			'name'      => esc_html__('Background Color','wpqa'),
			'id'        => 'background_color',
			'type'      => 'color',
			'condition' => 'background_type:is(patterns)'
		);
			
		$options[] = array(
			'name'      => esc_html__('Choose Pattern','wpqa'),
			'id'        => "background_pattern",
			'std'       => "bg13",
			'type'      => "images",
			'condition' => 'background_type:is(patterns)',
			'class'     => "pattern_images",
			'options'   => array(
				'bg1'  => $imagepath.'bg1.jpg',
				'bg2'  => $imagepath.'bg2.jpg',
				'bg3'  => $imagepath.'bg3.jpg',
				'bg4'  => $imagepath.'bg4.jpg',
				'bg5'  => $imagepath.'bg5.jpg',
				'bg6'  => $imagepath.'bg6.jpg',
				'bg7'  => $imagepath.'bg7.jpg',
				'bg8'  => $imagepath.'bg8.jpg',
				'bg9'  => $imagepath_theme.'patterns/bg9.png',
				'bg10' => $imagepath_theme.'patterns/bg10.png',
				'bg11' => $imagepath_theme.'patterns/bg11.png',
				'bg12' => $imagepath_theme.'patterns/bg12.png',
				'bg13' => $imagepath.'bg13.jpg',
				'bg14' => $imagepath.'bg14.jpg',
				'bg15' => $imagepath_theme.'patterns/bg15.png',
				'bg16' => $imagepath_theme.'patterns/bg16.png',
				'bg17' => $imagepath.'bg17.jpg',
				'bg18' => $imagepath.'bg18.jpg',
				'bg19' => $imagepath.'bg19.jpg',
				'bg20' => $imagepath.'bg20.jpg',
				'bg21' => $imagepath_theme.'patterns/bg21.png',
				'bg22' => $imagepath.'bg22.jpg',
				'bg23' => $imagepath_theme.'patterns/bg23.png',
				'bg24' => $imagepath_theme.'patterns/bg24.png',
			)
		);
		
		$options[] = array(
			'name'      => esc_html__('Custom Background','wpqa'),
			'id'        => 'custom_background',
			'std'       => $background_defaults,
			'type'      => 'background',
			'options'   => $background_defaults,
			'condition' => 'background_type:is(custom_background)'
		);
		
		$options[] = array(
			'name'      => esc_html__('Full Screen Background','wpqa'),
			'desc'      => esc_html__('Select ON to enable Full Screen Background','wpqa'),
			'id'        => 'full_screen_background',
			'type'      => 'checkbox',
			'condition' => 'background_type:is(custom_background)'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name' => esc_html__('Typography','wpqa'),
			'id'   => 'typography',
			'type' => 'heading-2'
		);
		
		$options[] = array(
			"name"    => esc_html__('Main font','wpqa'),
			"id"      => "main_font",
			"type"    => "typography",
			'std'     => array("face" => "Default font","color" => "","style" => "","size" => 9),
			'options' => array("color" => false,"styles" => false,"sizes" => false)
		);
		
		if (has_discy()) {
			$options[] = array(
				"name"    => esc_html__('Second font','wpqa'),
				"id"      => "second_font",
				"type"    => "typography",
				'std'     => array("face" => "Default font","color" => "","style" => "","size" => 9),
				'options' => array("color" => false,"styles" => false,"sizes" => false)
			);
		}
		
		$options[] = array(
			"name"    => esc_html__('General Typography','wpqa'),
			"id"      => "general_typography",
			"type"    => "typography",
			'options' => array('faces' => false)
		);
		
		$options[] = array(
			'name' => esc_html__('General link color','wpqa'),
			"id"   => "general_link_color",
			"type" => "color"
		);
		
		$options[] = array(
			"name"    => esc_html__('H1','wpqa'),
			"id"      => "h1",
			"type"    => "typography",
			'options' => array('faces' => false,"color" => false)
		);
		
		$options[] = array(
			"name"    => esc_html__('H2','wpqa'),
			"id"      => "h2",
			"type"    => "typography",
			'options' => array('faces' => false,"color" => false)
		);
		
		$options[] = array(
			"name"    => esc_html__('H3','wpqa'),
			"id"      => "h3",
			"type"    => "typography",
			'options' => array('faces' => false,"color" => false)
		);
		
		$options[] = array(
			"name"    => esc_html__('H4','wpqa'),
			"id"      => "h4",
			"type"    => "typography",
			'options' => array('faces' => false,"color" => false)
		);
		
		$options[] = array(
			"name"    => esc_html__('H5','wpqa'),
			"id"      => "h5",
			"type"    => "typography",
			'options' => array('faces' => false,"color" => false)
		);
		
		$options[] = array(
			"name"    => esc_html__('H6','wpqa'),
			"id"      => "h6",
			"type"    => "typography",
			'options' => array('faces' => false,"color" => false)
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options_sidebar = array(
			'default'      => $imagepath.'sidebar_default.jpg',
			'menu_sidebar' => $imagepath.'menu_sidebar.jpg',
			'right'        => $imagepath.'sidebar_right.jpg',
			'full'         => $imagepath.'sidebar_no.jpg',
			'left'         => $imagepath.'sidebar_left.jpg',
			'centered'     => $imagepath.'centered.jpg',
			'menu_left'    => $imagepath.'menu_left.jpg',
		);

		$layout_options = array(
			array("icon" => "admin-page","key" => "post","name" => esc_html_x("Post","Admin settings","wpqa")),
			array("icon" => "editor-help","key" => "question","name" => esc_html_x("Question","Admin settings","wpqa")),
			array("icon" => "groups","key" => "group","name" => esc_html_x("Group","Admin settings","wpqa")),
			array("icon" => "admin-users","key" => "author","name" => esc_html_x("Users","Admin settings","wpqa")),
			array("icon" => "search","key" => "search","name" => esc_html_x("Search","Admin settings","wpqa")),
			array("icon" => "tickets-alt","key" => "checkout","name" => esc_html_x("Checkout","Admin settings","wpqa")),
			array("icon" => "businessman","key" => "subscriptions","name" => esc_html_x("Subscriptions","Admin settings","wpqa")),
			array("icon" => "star-filled","key" => "buy_points","name" => esc_html_x("Buy points","Admin settings","wpqa")),
			array("icon" => "category","key" => "add_category","name" => esc_html_x("Add category","Admin settings","wpqa")),
			array("icon" => "editor-help","key" => "add_question","name" => esc_html_x("Ask question","Admin settings","wpqa")),
			array("icon" => "edit","key" => "edit_question","name" => esc_html_x("Edit question","Admin settings","wpqa")),
			array("icon" => "welcome-add-page","key" => "add_group","name" => esc_html_x("Add group","Admin settings","wpqa")),
			array("icon" => "groups","key" => "edit_group","name" => esc_html_x("Edit group","Admin settings","wpqa")),
			array("icon" => "welcome-write-blog","key" => "add_post","name" => esc_html_x("Add post","Admin settings","wpqa")),
			array("icon" => "editor-spellcheck","key" => "edit_post","name" => esc_html_x("Edit post","Admin settings","wpqa")),
			array("icon" => "admin-comments","key" => "edit_comment","name" => esc_html_x("Edit comment","Admin settings","wpqa")),
			array("icon" => "unlock","key" => "login","name" => esc_html_x("Login","Admin settings","wpqa")),
			array("icon" => "businessperson","key" => "signup","name" => esc_html_x("Signup","Admin settings","wpqa")),
			array("icon" => "privacy","key" => "lost_password","name" => esc_html_x("Lost password","Admin settings","wpqa"))
		);

		if ($activate_knowledgebase == true) {
			$knowledgebase_layout = array(array("icon" => "buddicons-forums","key" => "knowledgebase","name" => esc_html_x("Knowledgebase","Admin settings","wpqa")));
			$layout_options = wpqa_array_insert_after($layout_options,1,$knowledgebase_layout);
		}

		foreach ($layout_options as $key => $value) {
			$options[] = array(
				'name'    => $value["name"]." ".esc_html__('layout','wpqa'),
				'id'      => $value["key"].'_styling',
				'icon'    => $value["icon"],
				'type'    => 'heading',
			);

			$options[] = array(
				'type' => 'heading-2',
			);

			$options[] = array(
				'name'    => $value["name"]." ".esc_html__('sidebar layout','wpqa'),
				'id'      => $value["key"]."_sidebar_layout",
				'std'     => "default",
				'type'    => "images",
				'options' => $options_sidebar
			);
			
			$options[] = array(
				'name'      => $value["name"]." ".esc_html__('Page sidebar','wpqa'),
				'id'        => $value["key"]."_sidebar",
				'std'       => '',
				'options'   => $new_sidebars,
				'type'      => 'select',
				'condition' => $value["key"].'_sidebar_layout:not(full),'.$value["key"].'_sidebar_layout:not(centered),'.$value["key"].'_sidebar_layout:not(menu_left)'
			);

			$options[] = array(
				'name'      => $value["name"]." ".esc_html__('Page sidebar 2','wpqa'),
				'id'        => $value["key"]."_sidebar_2",
				'std'       => '',
				'options'   => $new_sidebars,
				'type'      => 'select',
				'operator'  => 'or',
				'condition' => $value["key"].'_sidebar_layout:is(menu_sidebar),'.$value["key"].'_sidebar_layout:is(menu_left)'
			);
			
			$options[] = array(
				'name'    => esc_html__("Light/dark",'wpqa'),
				'desc'    => esc_html__("Light/dark for",'wpqa')." ".$value["name"],
				'id'      => $value["key"]."_skin_l",
				'std'     => "default",
				'type'    => "images",
				'options' => array(
					'default' => $imagepath.'sidebar_default.jpg',
					'light'   => $imagepath.'light.jpg',
					'dark'    => $imagepath.'dark.jpg'
				)
			);
			
			$options[] = array(
				'name'    => esc_html__('Choose Your Skin','wpqa'),
				'class'   => "site_skin",
				'id'      => $value["key"]."_skin",
				'std'     => "default",
				'type'    => "images",
				'options' => array(
					'default'    => $imagepath.'default_color.jpg',
					'skin'       => $imagepath.'default.jpg',
					'violet'     => $imagepath.'violet.jpg',
					'bright_red' => $imagepath.'bright_red.jpg',
					'green'      => $imagepath.'green.jpg',
					'red'        => $imagepath.'red.jpg',
					'cyan'       => $imagepath.'cyan.jpg',
					'blue'       => $imagepath.'blue.jpg',
				)
			);
			
			$options[] = array(
				'name' => esc_html__('Primary Color','wpqa'),
				'id'   => $value["key"].'_primary_color',
				'type' => 'color'
			);
			
			$options[] = array(
				'name'    => esc_html__('Background Type','wpqa'),
				'id'      => $value["key"].'_background_type',
				'std'     => 'default',
				'type'    => 'radio',
				'options' => array(
					"default"           => esc_html__("Default","wpqa"),
					"none"              => esc_html__("None","wpqa"),
					"patterns"          => esc_html__("Patterns","wpqa"),
					"custom_background" => esc_html__("Custom Background","wpqa")
				)
			);

			$options[] = array(
				'name'      => esc_html__('Background Color','wpqa'),
				'id'        => $value["key"].'_background_color',
				'type'      => 'color',
				'condition' => $value["key"].'_background_type:is(patterns)'
			);
				
			$options[] = array(
				'name'      => esc_html__('Choose Pattern','wpqa'),
				'id'        => $value["key"]."_background_pattern",
				'std'       => "bg13",
				'type'      => "images",
				'condition' => $value["key"].'_background_type:is(patterns)',
				'class'     => "pattern_images",
				'options'   => array(
					'bg1'  => $imagepath.'bg1.jpg',
					'bg2'  => $imagepath.'bg2.jpg',
					'bg3'  => $imagepath.'bg3.jpg',
					'bg4'  => $imagepath.'bg4.jpg',
					'bg5'  => $imagepath.'bg5.jpg',
					'bg6'  => $imagepath.'bg6.jpg',
					'bg7'  => $imagepath.'bg7.jpg',
					'bg8'  => $imagepath.'bg8.jpg',
					'bg9'  => $imagepath_theme.'patterns/bg9.png',
					'bg10' => $imagepath_theme.'patterns/bg10.png',
					'bg11' => $imagepath_theme.'patterns/bg11.png',
					'bg12' => $imagepath_theme.'patterns/bg12.png',
					'bg13' => $imagepath.'bg13.jpg',
					'bg14' => $imagepath.'bg14.jpg',
					'bg15' => $imagepath_theme.'patterns/bg15.png',
					'bg16' => $imagepath_theme.'patterns/bg16.png',
					'bg17' => $imagepath.'bg17.jpg',
					'bg18' => $imagepath.'bg18.jpg',
					'bg19' => $imagepath.'bg19.jpg',
					'bg20' => $imagepath.'bg20.jpg',
					'bg21' => $imagepath_theme.'patterns/bg21.png',
					'bg22' => $imagepath.'bg22.jpg',
					'bg23' => $imagepath_theme.'patterns/bg23.png',
					'bg24' => $imagepath_theme.'patterns/bg24.png',
				)
			);

			$options[] = array(
				'name'      => esc_html__('Custom Background','wpqa'),
				'id'        => $value["key"].'_custom_background',
				'std'       => $background_defaults,
				'type'      => 'background',
				'options'   => $background_defaults,
				'condition' => $value["key"].'_background_type:is(custom_background)'
			);
				
			$options[] = array(
				'name'      => esc_html__('Full Screen Background','wpqa'),
				'desc'      => esc_html__('Select ON to enable Full Screen Background','wpqa'),
				'id'        => $value["key"].'_full_screen_background',
				'type'      => 'checkbox',
				'condition' => $value["key"].'_background_type:is(custom_background)'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
		}

		return $options;
	}
}?>