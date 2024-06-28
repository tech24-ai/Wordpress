<?php /* Widget options */
add_filter("wpqa_widget_options","discy_widget_options");
function discy_widget_options($options) {
	$directory_uri = get_template_directory_uri();
	$imagepath_theme =  $directory_uri.'/images/';
	$options['about-widget'] = array(
		array(
			'name' => esc_html__('Title','discy'),
			'id'   => 'title',
			'type' => 'text',
			'std'  => 'Discy'
		),
		array(
			'name'    => esc_html__('Image URL logo','discy'),
			'id'      => 'logo',
			'type'    => 'upload',
			'std'     => $imagepath_theme.'logo-footer.png',
			'options' => array("height" => "height_logo","width" => "width_logo")
		),
		array(
			'name' => esc_html__("Height of the logo","discy"),
			"id"   => "height_logo",
			"type" => "sliderui",
			'std'  => '45',
			"step" => "1",
			"min"  => "1",
			"max"  => "200"
		),
		array(
			'name' => esc_html__("Width of the logo","discy"),
			"id"   => "width_logo",
			"type" => "sliderui",
			'std'  => '47',
			"step" => "1",
			"min"  => "1",
			"max"  => "200"
		),
		array(
			'name' => esc_html__("Margin top for the logo","discy"),
			"id"   => "margin_logo",
			"type" => "sliderui",
			'std'  => '0',
			"step" => "1",
			"min"  => "0",
			"max"  => "70"
		),
		array(
			'name' => esc_html__('About text','discy'),
			'id'   => 'text',
			'type' => 'textarea',
			'std'  => 'Discy is a social questions & Answers Engine which will help you establis your community and connect with other people.'
		),
		array(
			'name' => esc_html__("Margin top for the text","discy"),
			"id"   => "padding_text",
			"type" => "sliderui",
			'std'  => '0',
			"step" => "1",
			"min"  => "0",
			"max"  => "70"
		),
	);
	return $options;
}?>