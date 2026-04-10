<?php
namespace CmsmastersElementor\Modules\GiveWp\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Give_WP_Donor_Wall extends Give_WP_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.6.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return 'cmsmasters-give-wp-donor-wall';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.6.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'GiveWP Donor Wall', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.6.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-donor-wall';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @since 1.6.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'donor',
		);
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.6.0
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'donors_per_page',
			array(
				'label' => __( 'Donors', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'label_block' => false,
				'default' => '9',
			)
		);

		$this->add_control(
			'columns',
			array(
				'label' => __( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'default' => '3',
			)
		);

		$this->add_control(
			'show_name',
			array(
				'label' => __( 'Show Name', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_avatar',
			array(
				'label' => __( 'Show Avatar', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'show_total',
			array(
				'label' => __( 'Show Total', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'show_time',
			array(
				'label' => __( 'Show Time', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'show_comments',
			array(
				'label' => __( 'Show Comments', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'comment_length',
			array(
				'label' => __( 'Comment Length', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'label_block' => false,
				'default' => '140',
				'condition' => array(
					'show_comments' => 'yes',
				),
			)
		);

		$this->add_control(
			'readmore_text',
			array(
				'label' => __( 'Readmore Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'Enter your text', 'cmsmasters-elementor' ),
				'label_block' => true,
				'condition' => array(
					'show_comments' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Query', 'cmsmasters-elementor' ),
			)
		);

		$form_list = $this->get_select_form();

		$this->add_control(
			'form_list',
			array(
				'label' => __( 'Form', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => $form_list,
				'placeholder' => __( 'Select Form', 'cmsmasters-elementor' ),
				'description' => __( 'Filter donors by form', 'cmsmasters-elementor' ),
			)
		);

		$donor_list = $this->get_select_donor();

		$this->add_control(
			'donor_list',
			array(
				'label' => __( 'Donors', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => $donor_list,
				'multiple' => true,
				'placeholder' => __( 'Select Donor', 'cmsmasters-elementor' ),
				'description' => __( 'Select the donors you want to show, otherwise all will be shown', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'only_comments',
			array(
				'label' => __( 'Only Comments', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'description' => __( 'Choose whether to display all donors or just donors who left comments.', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'anonymous',
			array(
				'label' => __( 'Anonymous', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'description' => __( 'Slide to YES to display anonymous donors?.', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label' => __( 'Order By', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'post_date' => array(
						'title' => __( 'Date', 'cmsmasters-elementor' ),
					),
					'donation_amount' => array(
						'title' => __( 'Amount', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'post_date',
				'toggle' => false,
				'render_type' => 'template',
				'label_block' => false,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'order',
			array(
				'label' => __( 'Order', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'DESC' => array(
						'title' => __( 'DESC', 'cmsmasters-elementor' ),
					),
					'ASC' => array(
						'title' => __( 'ASC', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'DESC',
				'toggle' => false,
				'render_type' => 'template',
				'label_block' => false,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'addition',
			array(
				'label' => __( 'Addition Options', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'show_company_name',
			array(
				'label' => __( 'Show Company Name', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'show_form',
			array(
				'label' => __( 'Show Form Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'heading_load_more',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Load More', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'hide_load_more',
			array(
				'label' => __( 'Hide', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'prefix_class' => 'cmsmasters-hide__load-more-',
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-hide__load-more-yes button.give-donor__load_more.give-button-with-loader' => 'display: none; visibility: hidden;',
				),
			)
		);

		$this->add_control(
			'loadmore_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'Enter your text', 'cmsmasters-elementor' ),
				'label_block' => true,
				'condition' => array(
					'hide_load_more!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'default' => 'left',
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-give-wp-widget .give-wrap ' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'hide_load_more!' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'donor_grid_style',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'donor_grid_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 60,
						'step' => 1,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-grid-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'donor_grid_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 10,
						'step' => 1,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-grid-border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'donor_grid_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 100,
						'step' => 1,
					),
					'%' => array(
						'max' => 50,
						'step' => 1,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'tablet_default' => array( 'unit' => 'px' ),
				'mobile_default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-grid-border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'donor_grid_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-grid-bg-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'donor_grid_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-grid-bd-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'donor_avatar_style',
			array(
				'label' => __( 'Donor Avatar', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_avatar' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'donor_avatar_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 60,
						'step' => 1,
					),
					'%' => array(
						'max' => 100,
						'step' => 1,
					),
					'em' => array(
						'max' => 10,
						'step' => 1,
					),
					'rem' => array(
						'max' => 10,
						'step' => 1,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
					'%',
					'em',
					'rem',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-avatar-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'donor_avatar_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 200,
						'step' => 1,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-avatar-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'donor_avatar_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 100,
						'step' => 1,
					),
					'%' => array(
						'max' => 50,
						'step' => 1,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'tablet_default' => array( 'unit' => 'px' ),
				'mobile_default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-avatar-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'donor_avatar_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-avatar-bg-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'donor_avatar_in_color',
			array(
				'label' => __( 'Initial Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-avatar-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'donor_info_style',
			array(
				'label' => __( 'Donor Info', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'show_name',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'show_time',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'show_comments',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'show_company_name',
							'operator' => '===',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'heading_name',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Name', 'cmsmasters-elementor' ),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'show_name',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'show_company_name',
							'operator' => '===',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'donor_name_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-donor-container-variation__name',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'show_name',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'show_company_name',
							'operator' => '===',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'donor_name_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-name-color: {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'show_name',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'show_company_name',
							'operator' => '===',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'donor_name_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 60,
						'step' => 1,
					),
					'%' => array(
						'max' => 100,
						'step' => 1,
					),
					'rem' => array(
						'max' => 10,
						'step' => 1,
					),
					'em' => array(
						'max' => 10,
						'step' => 1,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
					'%',
					'em',
					'rem',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-name-gap: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'show_name',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'show_company_name',
							'operator' => '===',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'heading_time',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Time', 'cmsmasters-elementor' ),
				'condition' => array(
					'show_time' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'donor_time_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-donor-wall .cmsmasters-give-wp-widget .give-wrap .give-donor-container-variation .give-donor-container-variation__timestamp',
				'condition' => array(
					'show_time' => 'yes',
				),
			)
		);

		$this->add_control(
			'donor_time_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-time-color: {{VALUE}}',
				),
				'condition' => array(
					'show_time' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'donor_time_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 60,
						'step' => 1,
					),
					'%' => array(
						'max' => 100,
						'step' => 1,
					),
					'rem' => array(
						'max' => 10,
						'step' => 1,
					),
					'em' => array(
						'max' => 10,
						'step' => 1,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
					'%',
					'em',
					'rem',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-time-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'show_time' => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_comments',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Comments', 'cmsmasters-elementor' ),
				'condition' => array(
					'show_comments' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'donor_comments_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-donor-wall .cmsmasters-give-wp-widget .give-wrap .give-donor-content__comment',
				'condition' => array(
					'show_comments' => 'yes',
				),
			)
		);

		$this->add_control(
			'donor_comments_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-comments-color: {{VALUE}}',
				),
				'condition' => array(
					'show_comments' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'donor_comments_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 60,
						'step' => 1,
					),
					'%' => array(
						'max' => 100,
						'step' => 1,
					),
					'rem' => array(
						'max' => 10,
						'step' => 1,
					),
					'em' => array(
						'max' => 10,
						'step' => 1,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
					'%',
					'em',
					'rem',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-comments-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'show_comments' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'donation_info_style',
			array(
				'label' => __( 'Donation Info', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'show_total',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'show_form',
							'operator' => '===',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'heading_form_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Form Title', 'cmsmasters-elementor' ),
				'condition' => array(
					'show_form' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'form_name_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-donor-wall .cmsmasters-give-wp-widget .give-wrap .give-donor-details__wrapper .give-donor-details__form_title',
				'condition' => array(
					'show_form' => 'yes',
				),
			)
		);

		$this->update_control(
			'form_name_typography_font_size',
			array(
				'selectors' => array(
					'{{WRAPPER}}' => '--form-title-font-size: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->update_control(
			'form_name_typography_font_weight',
			array(
				'selectors' => array(
					'{{WRAPPER}}' => '--form-title-font-weight: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'form_name_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--form-title-color: {{VALUE}}',
				),
				'condition' => array(
					'show_form' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'form_name_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 60,
						'step' => 1,
					),
					'%' => array(
						'max' => 100,
						'step' => 1,
					),
					'rem' => array(
						'max' => 10,
						'step' => 1,
					),
					'em' => array(
						'max' => 10,
						'step' => 1,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
					'%',
					'em',
					'rem',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--form-title-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'show_form' => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_amount',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Amount', 'cmsmasters-elementor' ),
				'condition' => array(
					'show_total' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'donor_amount_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-donor-wall .cmsmasters-give-wp-widget .give-wrap .give-donor-details__wrapper span, #cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-donor-wall .cmsmasters-give-wp-widget .give-wrap .give-donor-details__total',
				'condition' => array(
					'show_total' => 'yes',
				),
			)
		);

		$this->add_control(
			'donor_am_label_color',
			array(
				'label' => __( 'Label Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-am-label-color: {{VALUE}}',
				),
				'condition' => array(
					'show_total' => 'yes',
				),
			)
		);

		$this->add_control(
			'donor_am_total_color',
			array(
				'label' => __( 'Total Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--donor-am-total-color: {{VALUE}}',
				),
				'condition' => array(
					'show_total' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get shortcode widget.
	 *
	 * Retrieve the widget shortcode.
	 *
	 * @since 1.6.0
	 *
	 * @return string The widget shortcode.
	 */
	public function get_shortcode() {
		$settings = $this->get_settings_for_display();

		$donors_per_page = $settings['donors_per_page'];
		$columns = $settings['columns'];
		$show_avatar = ( 'yes' === $settings['show_avatar'] ) ? true : false;
		$show_name = ( 'yes' === $settings['show_name'] ) ? true : false;
		$show_total = ( 'yes' === $settings['show_total'] ) ? true : false;
		$show_time = ( 'yes' === $settings['show_time'] ) ? true : false;
		$show_comments = ( 'yes' === $settings['show_comments'] ) ? true : false;
		$comment_length = $settings['comment_length'];
		$donors_id = $this->get_string_ids( 'donor_list' );
		$form_id = $this->get_form_id();
		$orderby = $settings['orderby'];
		$order = $settings['order'];
		$only_comments = ( 'yes' === $settings['only_comments'] ) ? true : false;
		$anonymous = ( 'yes' === $settings['anonymous'] ) ? true : false;
		$show_company_name = ( 'yes' === $settings['show_company_name'] ) ? true : false;
		$show_form = ( 'yes' === $settings['show_form'] ) ? true : false;
		$loadmore_text = ( '' === $settings['loadmore_text'] ) ? __( 'Load More', 'cmsmasters-elementor' ) : $settings['loadmore_text'];
		$readmore_text = ( '' === $settings['readmore_text'] ) ? __( 'Read More', 'cmsmasters-elementor' ) : $settings['readmore_text'];

		$shortcode = "[give_donor_wall 
				donors_per_page=\"{$donors_per_page}\" 
				columns=\"{$columns}\" 
				show_avatar=\"{$show_avatar}\" 
				show_name=\"{$show_name}\" 
				show_time=\"{$show_time}\" 
				show_comments=\"{$show_comments}\" 
				comment_length=\"{$comment_length}\" 
				show_total=\"{$show_total}\"
				orderby=\"{$orderby}\" 
				order=\"{$order}\" 
				only_comments=\"{$only_comments}\" 
				anonymous=\"{$anonymous}\" 
				show_company_name=\"{$show_company_name}\" 
				show_form=\"{$show_form}\" 
				loadmore_text=\"{$loadmore_text}\" 
				readmore_text=\"{$readmore_text}\" 
				avatar_size=\"200\"";

				if ( ! empty( $form_id ) ) {
					$shortcode .= " form_id=\"{$form_id}\"";
				}

				if ( false !== $donors_id ) {
					$shortcode .= " ids=\"{$donors_id}\"";
				}

				$shortcode .= "]";

				return $shortcode;
	}

	/**
	 * Get form id.
	 *
	 * Retrieve the form id.
	 *
	 * @since 1.6.0
	 *
	 * @return string The id form.
	 */
	public function get_form_id() {
		$settings = $this->get_settings_for_display();
		$form_id = $settings['form_list'];

		return $form_id;
	}

	public function add_filter_for_editor_content() {
		return false;
	}
}
