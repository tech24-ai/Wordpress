<?php

namespace BBElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * @since 1.1.0
 */
class BBP_Profile_Completion extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 * @return string Widget name.
	 * @since  1.1.0
	 * @access public
	 */
	public function get_name() {
		return 'bbp-profile-completion';
	}

	/**
	 * Retrieve the widget title.
	 * @return string Widget title.
	 * @since  1.1.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Profile Completion', 'buddyboss-theme' );
	}

	/**
	 * Retrieve the widget icon.
	 * @return string Widget icon.
	 * @since  1.1.0
	 * @access public
	 */
	public function get_icon() {
		return 'eicon-check-circle';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 * Used to determine where to display the widget in the editor.
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 * @return array Widget categories.
	 * @since  1.1.0
	 * @access public
	 */
	public function get_categories() {
		return [ 'buddyboss-elements' ];
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 * Used to set scripts dependencies required to run the widget.
	 * @return array Widget scripts dependencies.
	 * @since  1.0.0
	 * @access public
	 */
	public function get_script_depends() {
		return array( 'elementor-bb-frontend' );
	}

	/**
	 * Register the widget controls.
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 * @since  1.1.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section( 'section_content_layout', [
            'label' => esc_html__( 'Layout', 'buddyboss-theme' ),
        ] );

		$this->add_control( 'skin_style', array(
				'label'   => __( 'Skin', 'buddyboss-theme' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'circle',
				'options' => array(
					'circle' => __( 'Circle', 'buddyboss-theme' ),
					'linear' => __( 'Linear', 'buddyboss-theme' ),
				),
			) );

		$this->add_responsive_control( 'alignment', [
				'label'        => __( 'Alignment', 'buddyboss-theme' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'options'      => [
					'left'   => [
						'title' => __( 'Left', 'buddyboss-theme' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'buddyboss-theme' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'buddyboss-theme' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'      => 'right',
				'prefix_class' => 'elementor-cta-%s-completion-',
			] );

		/* Profile Groups and Profile Cover Photo VARS. */
		$profile_groups = bp_xprofile_get_groups();

		$photos_enabled_arr        = array();
		$widget_enabled_arr        = array();
		$is_profile_photo_disabled = bp_disable_avatar_uploads();
		$is_cover_photo_disabled   = bp_disable_cover_image_uploads();

		// Show Options only when Profile Photo and Cover option enabled in the Profile Settings.
		if ( ! $is_profile_photo_disabled ) {
			$photos_enabled_arr['profile_photo'] = __( 'Profile Photo', 'buddyboss-theme' );
		}
		if ( ! $is_cover_photo_disabled ) {
			$photos_enabled_arr['cover_photo'] = __( 'Cover Photo', 'buddyboss-theme' );
		}

		foreach ( $profile_groups as $single_group_details ) :

			$this->add_control( 'profile_field_' . $single_group_details->id, [
					'label'     => $single_group_details->name,
					'type'      => \Elementor\Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'label_on'  => __( 'Show', 'buddyboss-theme' ),
					'label_off' => __( 'Hide', 'buddyboss-theme' ),
				] );

		endforeach;

		foreach ( $photos_enabled_arr as $photos_value => $photos_label ) :

			$this->add_control( sanitize_title( $photos_value ), [
					'label'     => $photos_label,
					'type'      => \Elementor\Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'label_on'  => __( 'Show', 'buddyboss-theme' ),
					'label_off' => __( 'Hide', 'buddyboss-theme' ),
				] );

		endforeach;

		$this->add_control( 'switch_hide_widget', [
				'label'       => esc_html__( 'Hide Widget', 'buddyboss-theme' ),
				'description' => esc_html__( 'Hide widget once progress hits 100%', 'buddyboss-theme' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'no',
			] );

		$this->add_control( 'switch_profile_btn', [
				'label'     => esc_html__( 'Profile Complete Button', 'buddyboss-theme' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			] );

		$this->add_control( 'heading_text', [
				'label'       => __( 'Heading Text', 'buddyboss-theme' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'Complete your profile', 'buddyboss-theme' ),
				'placeholder' => __( 'Enter heading text', 'buddyboss-theme' ),
				'label_block' => true,
				'separator'   => 'before',
				'condition'   => [
					'skin_style' => 'linear',
				],
			] );

		$this->add_control( 'completion_text', [
				'label'       => __( 'Completion Text', 'buddyboss-theme' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'Complete', 'buddyboss-theme' ),
				'placeholder' => __( 'Enter completion text', 'buddyboss-theme' ),
				'label_block' => true,
				'separator'   => 'before',
			] );

		$this->add_control( 'completion_button_text', [
				'label'       => __( 'Complete Profile Button Text', 'buddyboss-theme' ),
				'description' => esc_html__( 'Button text if progress is less than 100%', 'buddyboss-theme' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'Complete Profile', 'buddyboss-theme' ),
				'placeholder' => __( 'Enter button text', 'buddyboss-theme' ),
				'label_block' => true,
				'separator'   => 'before',
				'condition'   => [
					'switch_profile_btn' => 'yes',
				],
			] );

		$this->add_control( 'edit_button_text', [
				'label'       => __( 'Edit Profile Button Text', 'buddyboss-theme' ),
				'description' => esc_html__( 'Button text once progress hits 100%', 'buddyboss-theme' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'Edit Profile', 'buddyboss-theme' ),
				'placeholder' => __( 'Enter button text', 'buddyboss-theme' ),
				'label_block' => true,
				'separator'   => 'before',
				'condition'   => [
					'switch_profile_btn' => 'yes',
				],
			] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_box', [
				'label'     => esc_html__( 'Box', 'buddyboss-theme' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'skin_style' => 'linear',
				],
			] );

		$this->add_control( 'box_width', [
				'label'     => __( 'Width', 'buddyboss-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 100,
				],
				'range'     => [
					'%' => [
						'min'  => 20,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .profile_bit.skin-linear' => 'width: {{SIZE}}%;',
				],
			] );

		$this->add_control( 'box_bgr_color', [
				'label'     => __( 'Background Color', 'buddyboss-theme' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .profile_bit.skin-linear .progress_container'   => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .profile_bit.skin-linear .profile_bit__details' => 'background-color: {{VALUE}};',
				],
			] );

		$this->add_control( 'box_border_style', [
				'label'   => __( 'Border Type', 'buddyboss-theme' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'solid'  => __( 'Solid', 'buddyboss-theme' ),
					'dashed' => __( 'Dashed', 'buddyboss-theme' ),
					'dotted' => __( 'Dotted', 'buddyboss-theme' ),
					'double' => __( 'Double', 'buddyboss-theme' ),
					'none'   => __( 'None', 'buddyboss-theme' ),
				],
			] );

		$this->add_control( 'box_border_width', [
				'label'     => __( 'Border Width', 'buddyboss-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .profile_bit.skin-linear:not(.active) .progress_container' => 'border-width: {{SIZE}}px;',
					'{{WRAPPER}} .profile_bit.skin-linear.active .progress_container'       => 'border-top-width: {{SIZE}}px;border-left-width: {{SIZE}}px;border-right-width: {{SIZE}}px;',
					'{{WRAPPER}} .profile_bit.skin-linear .profile_bit__details'            => 'border-bottom-width: {{SIZE}}px;border-left-width: {{SIZE}}px;border-right-width: {{SIZE}}px;',
				],
			] );

		$this->add_control( 'border_color', [
				'label'     => __( 'Border Color', 'buddyboss-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .profile_bit.skin-linear .progress_container'   => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .profile_bit.skin-linear .profile_bit__details' => 'border-color: {{VALUE}}',
				],
			] );

		$this->add_control( 'box_border_radius', [
				'label'      => __( 'Border Radius', 'buddyboss-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .profile_bit.skin-linear:not(.active) .progress_container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .profile_bit.skin-linear.active .progress_container'       => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .profile_bit.skin-linear .profile_bit__details'            => 'border-radius: 0 0 {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				],
			] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_progress', [
				'label' => esc_html__( 'Progress Graph', 'buddyboss-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			] );

		$this->add_control( 'progress_spacing', [
				'label'      => __( 'Spacing', 'buddyboss-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'    => '0',
					'right'  => '10',
					'bottom' => '0',
					'left'   => '10',
				],
				'selectors'  => [
					'{{WRAPPER}} .profile_bit.skin-circle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .profile_bit.skin-linear' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			] );

		$this->add_control( 'progress_active_width', [
				'label'     => __( 'Progress Graph Border Width', 'buddyboss-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 6,
				],
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					],
				],
				'selectors' => [
					/*'{{WRAPPER}} .progress_bit_graph:not(.progress_bit_graph--sm) .progress-bit__ring .progress-bit__disc' => 'border-width: {{SIZE}}px;',*/
					/*'{{WRAPPER}} .progress_bit_graph:not(.progress_bit_graph--sm) .progress-bit__ring:after' => 'border-width: {{SIZE}}px;',*/
					'{{WRAPPER}} .progress_bit_linear .progress_bit__line'  => 'height: {{SIZE}}px;',
					'{{WRAPPER}} .progress_bit_linear .progress_bit__scale' => 'height: {{SIZE}}px;',
				],
				'condition' => [
					'skin_style' => 'linear',
				],
			] );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
				'name'      => 'typography_heading',
				'label'     => __( 'Typography Heading', 'buddyboss-theme' ),
				'selector'  => '{{WRAPPER}} .progress_bit__heading h3',
				'condition' => [
					'skin_style' => 'linear',
				],
			) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
				'name'      => 'typography_progress_value',
				'label'     => __( 'Typography Progress Value', 'buddyboss-theme' ),
				'selector'  => '{{WRAPPER}} .progress_bit__data-num',
				'condition' => [
					'skin_style' => 'circle',
				],
			) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
				'name'      => 'typography_progress_info',
				'label'     => __( 'Typography Progress Info', 'buddyboss-theme' ),
				'selector'  => '{{WRAPPER}} .progress_bit__data-remark, {{WRAPPER}} .progress_bit__data-num > span',
				'condition' => [
					'skin_style' => 'circle',
				],
			) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
				'name'      => 'typography_progress_data',
				'label'     => __( 'Typography Progress Data', 'buddyboss-theme' ),
				'selector'  => '{{WRAPPER}} .progress_bit__data-remark, {{WRAPPER}} .progress_bit__data-num > span, {{WRAPPER}} .progress_bit__data-num',
				'condition' => [
					'skin_style' => 'linear',
				],
			) );

		$this->add_control( 'details_color_linear', [
				'label'     => __( 'Details Completion Background Color', 'buddyboss-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .progress_bit__data-remark'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .progress_bit__data-num > span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .progress_bit__data-num'        => 'color: {{VALUE}};',
				],
				'condition' => [
					'skin_style' => 'linear',
				],
			] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_colors', [
				'label' => esc_html__( 'Colors', 'buddyboss-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			] );

		$this->add_control( 'details_color', [
				'label'     => __( 'Details Completion Background Color', 'buddyboss-theme' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .profile_bit__details' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'skin_style' => 'circle',
				],
			] );

		$this->add_control( 'heading_color', [
				'label'     => __( 'Heading Color', 'buddyboss-theme' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .progress_bit_linear .progress_bit__heading h3'             => 'color: {{VALUE}};',
					'{{WRAPPER}} .skin-linear .progress_bit_linear .progress_bit__heading i' => 'color: {{VALUE}};',
				],
				'condition' => [
					'skin_style' => 'linear',
				],
			] );

		$this->add_control( 'completion_color', [
				'label'     => __( 'Completion Color', 'buddyboss-theme' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1CD991',
				'selectors' => [
					'{{WRAPPER}} .progress-bit__ring .progress-bit__disc'                  => 'border-color: {{VALUE}};',
					'{{WRAPPER}} ul.profile_bit__list li.completed .section_number:before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} ul.profile_bit__list li.completed .completed_staus'       => 'border-color: {{VALUE}}; color: {{VALUE}}',
					'{{WRAPPER}} .progress_bit__scale'                                     => 'background-color: {{VALUE}};',
				],
			] );

		$this->add_control( 'incomplete_color', [
				'label'     => __( 'Incomplete Color', 'buddyboss-theme' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#EF3E46',
				'selectors' => [
					'{{WRAPPER}} ul.profile_bit__list li.incomplete .section_name a'  => 'color: {{VALUE}};',
					'{{WRAPPER}} ul.profile_bit__list li.incomplete .completed_staus' => 'border-color: {{VALUE}}; color: {{VALUE}}',
				],
			] );

		$this->add_control( 'ring_border_color', [
				'label'     => __( 'Progress Border Color', 'buddyboss-theme' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#DEDFE2',
				'selectors' => [
					'{{WRAPPER}} .progress-bit__ring:after'                      => 'border-color: {{VALUE}};',
					'{{WRAPPER}} ul.profile_bit__list li .section_number:before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .progress_bit__line'                            => 'background-color: {{VALUE}};',
				],
			] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_details', [
				'label' => esc_html__( 'Details Dropdown', 'buddyboss-theme' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			] );

		$this->add_control( 'switch_heading', [
				'label'     => esc_html__( 'Show Header', 'buddyboss-theme' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'skin_style' => 'circle',
				],
			] );

		$this->add_control( 'switch_completion_icon', [
				'label'   => esc_html__( 'Show Completion Icon', 'buddyboss-theme' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			] );

		$this->add_control( 'switch_completion_status', [
				'label'   => esc_html__( 'Show Completion Status', 'buddyboss-theme' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array(
				'name'     => 'details_box_shadow',
				'label'    => __( 'Details Container Shadow', 'buddyboss-theme' ),
				'selector' => '{{WRAPPER}} .profile_bit__details',
			) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
				'name'     => 'details_typography',
				'label'    => __( 'Typography Progress Value', 'buddyboss-theme' ),
				'selector' => '{{WRAPPER}} ul.profile_bit__list li .section_name a, {{WRAPPER}} .profile_bit__heading',
			) );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_button', [
				'label'     => esc_html__( 'Button', 'buddyboss-theme' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'switch_profile_btn' => 'yes',
				],
			] );

		$this->start_controls_tabs( 'button_tabs' );

		$this->start_controls_tab( 'button_normal_tab', array(
				'label' => __( 'Normal', 'buddyboss-theme' ),
			) );

		$this->add_control( 'button_color', array(
				'label'     => __( 'Color', 'buddyboss-theme' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .profile_bit_action a.profile_bit_action__link' => 'color: {{VALUE}}',
				),
			) );

		$this->add_control( 'button_bgr_color', array(
				'label'     => __( 'Background Color', 'buddyboss-theme' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .profile_bit_action a.profile_bit_action__link' => 'background-color: {{VALUE}}',
				),
			) );

		$this->add_control( 'la_button_border_color', array(
				'label'     => __( 'Border Color', 'buddyboss-theme' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .profile_bit_action a.profile_bit_action__link' => 'border-color: {{VALUE}}',
				),
			) );

		$this->end_controls_tab();

		$this->start_controls_tab( 'button_hover_tab', array(
				'label' => __( 'Hover', 'buddyboss-theme' ),
			) );

		$this->add_control( 'button_color_hover', array(
				'label'     => __( 'Color', 'buddyboss-theme' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .profile_bit_action a.profile_bit_action__link:hover' => 'color: {{VALUE}}',
				),
			) );

		$this->add_control( 'button_bgr_color_hover', array(
				'label'     => __( 'Background Color', 'buddyboss-theme' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .profile_bit_action a.profile_bit_action__link:hover' => 'background-color: {{VALUE}}',
				),
			) );

		$this->add_control( 'la_button_border_color_hover', array(
				'label'     => __( 'Border Color', 'buddyboss-theme' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .profile_bit_action a.profile_bit_action__link:hover' => 'border-color: {{VALUE}}',
				),
			) );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control( Group_Control_Typography::get_type(), array(
				'name'     => 'button_typography',
				'label'    => __( 'Typography', 'buddyboss-theme' ),
				'selector' => '{{WRAPPER}} .profile_bit_action a.profile_bit_action__link',
			) );

		$this->add_control( 'button_padding', [
				'label'      => __( 'Button Padding', 'buddyboss-theme' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'    => '2',
					'right'  => '15',
					'bottom' => '2',
					'left'   => '15',
				],
				'selectors'  => [
					'{{WRAPPER}} .profile_bit_action a.profile_bit_action__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			] );

		$this->add_group_control( Group_Control_Border::get_type(), [
				'name'        => 'button_border',
				'label'       => __( 'Button Border', 'buddyboss-theme' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .profile_bit_action a.profile_bit_action__link',
				'separator'   => 'before',
			] );

		$this->add_control( 'button_spacing', [
				'label'     => __( 'Spacing', 'buddyboss-theme' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 10,
				],
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .profile_bit_action' => 'margin-top: {{SIZE}}px;',
				],
			] );

		$this->end_controls_section();

	}

	/**
	 * Render the widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 * @since  1.1.0
	 * @access protected
	 */
	protected function render() {

		$settings      = $this->get_settings_for_display();
		$settings_skin = $settings['skin_style'];

		if ( function_exists( 'bp_xprofile_delete_profile_completion_transient' ) ) {
			// Delete Transient.
			bp_xprofile_delete_profile_completion_transient();
		}

		$selected_groups = array();
		foreach ( $settings as $k => $v ) {
			if ( strpos( $k, 'profile_field_' ) !== false && '' !== $v ) {
				$id                = explode( 'profile_field_', $k );
				$selected_groups[] = $id[1];
			}
		}

		$profile_phototype_selected = array();

		if ( 'yes' === $settings['profile_photo'] ) {
			$profile_phototype_selected[] = 'profile_photo';
		}

		if ( 'yes' === $settings['cover_photo'] ) {
			$profile_phototype_selected[] = 'cover_photo';
		}


		// IF nothing selected then return and nothing to display.
		if ( ( empty( $selected_groups ) && empty( $profile_phototype_selected ) ) || ! is_user_logged_in() ) {
			return;
		}

		if ( function_exists( 'bp_xprofile_get_user_progress_data' ) ) {
			$user_progress  = bp_xprofile_get_user_progress_data( $selected_groups, $profile_phototype_selected, $this->get_id() );
			$progress_label = sprintf( __( '%s', 'buddyboss-theme' ), $user_progress['completion_percentage'] );

			if ( $settings['switch_hide_widget'] && $user_progress['completion_percentage'] == 100 ) { ?>
                <div class="profile_bit_wrapper profile_bit_wrapper--blank"></div>
			    <?php
			} else { ?>
                <div class="profile_bit_wrapper <?php echo $settings['switch_profile_btn'] ? 'has-profile-button' : ''; ?> ">
                    <div class="profile_bit_figure">
                        <div class="profile_bit <?php echo 'skin-' . $settings_skin; ?> border-<?php echo $settings['box_border_style']; ?>">
                            <div class="progress_container">
                                <div class="progress_bit">
                                    <div class="progress_bit_graph">
                                        <div class="progress-bit__ring <?php echo ( $user_progress['completion_percentage'] == 100 ) ? 'bb-completed' : 'bb-not-completed'; ?>" data-percentage="<?php echo esc_attr( $user_progress['completion_percentage'] ); ?>">
                                            <span class="progress-bit__left"><span class="progress-bit__disc"></span></span>
                                            <span class="progress-bit__right"><span class="progress-bit__disc"></span></span>
                                        </div>
                                    </div>
                                    <div class="progress_bit_linear">
                                        <div class="progress_bit__heading">
                                            <h3><?php echo $settings['heading_text']; ?></h3>
                                            <i class="bb-icon-angle-right"></i></div>
                                        <div class="progress_bit__line <?php echo ( $user_progress['completion_percentage'] == 100 ) ? 'bb-completed' : 'bb-not-completed'; ?>">
                                            <div class="progress_bit__scale" style="width: <?php echo esc_attr( $user_progress['completion_percentage'] ); ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="progress_bit__data">
                                        <span class="progress_bit__data-num"><?php echo esc_html( $progress_label ); ?><span><?php _e( '%', 'buddyboss-theme' ); ?></span></span>
                                        <span class="progress_bit__data-remark"><?php echo $settings['completion_text']; ?></span>
									</div>
								</div>
								<?php if ( $settings['switch_profile_btn'] &&  'linear' == $settings['skin_style'] ) { ?>
									<div class="profile_bit_action">
										<a class="profile_bit_action__link" href="<?php echo bp_loggedin_user_domain() . 'profile/edit/'; ?>"><?php echo ( $user_progress['completion_percentage'] == 100 ) ? $settings['edit_button_text'] : $settings['completion_button_text']; ?>
											<i class="bb-icon-angle-right"></i></a>
									</div>
								<?php } ?>
                            </div>
                            <div class="profile_bit__details">
								<?php if ( $settings['switch_heading'] ) : ?>
                                    <div class="profile_bit__heading">
                                        <span class="progress-num"><?php echo esc_html( $progress_label ); ?><span><?php _e( '%', 'buddyboss-theme' ); ?></span></span>
                                        <span class="progress-figure">
										<div class="progress_bit_graph progress_bit_graph--sm">
											<div class="progress-bit__ring <?php echo ( $user_progress['completion_percentage'] == 100 ) ? 'bb-completed' : 'bb-not-completed'; ?>" data-percentage="<?php echo esc_attr( $user_progress['completion_percentage'] ); ?>">
												<span class="progress-bit__left"><span class="progress-bit__disc"></span></span>
												<span class="progress-bit__right"><span class="progress-bit__disc"></span></span>
											</div>
										</div>
									</span>
                                        <span class="progress-label"><?php echo $settings['completion_text']; ?></span>
                                    </div>
								<?php endif; ?>
                                <ul class="profile_bit__list">
									<?php
									// Loop through all sections and show progress.
									foreach ( $user_progress['groups'] as $single_section_details ) :
										$user_progress_status = ( 0 === $single_section_details['completed'] && $single_section_details['total'] > 0 ) ? 'progress_not_started' : '';
										?>
                                        <li class="single_section_wrap <?php echo ( $single_section_details['is_group_completed'] ) ? esc_attr( 'completed ' ) : esc_attr( 'incomplete ' ); ?> <?php echo esc_attr( $user_progress_status ); ?>">
											<?php if ( $settings['switch_completion_icon'] ) : ?>
                                                <span class="section_number"></span>
											<?php endif; ?>
                                            <span class="section_name">
											<a href="<?php echo esc_url( $single_section_details['link'] ); ?>" class="group_link"><?php echo esc_html( $single_section_details['label'] ); ?></a>
										</span>
											<?php if ( $settings['switch_completion_status'] ) : ?>
                                                <span class="progress">
												<span class="completed_staus">
													<span class="completed_steps"><?php echo absint( $single_section_details['completed'] ); ?></span>/<span class="total_steps"><?php echo absint( $single_section_details['total'] ); ?></span>
												</span>
											</span>
											<?php endif; ?>
                                        </li>
									<?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
						<?php if ( $settings['switch_profile_btn'] &&  'circle' == $settings['skin_style']) { ?>
                            <div class="profile_bit_action">
                                <a class="profile_bit_action__link" href="<?php echo bp_loggedin_user_domain() . 'profile/edit/'; ?>"><?php echo ( $user_progress['completion_percentage'] == 100 ) ? $settings['edit_button_text'] : $settings['completion_button_text']; ?>
                                    <i class="bb-icon-angle-right"></i></a>
                            </div>
						<?php } ?>
                    </div>
                </div>
				<?php
			}
		}
	}
}
