<?php
namespace CmsmastersElementor\Modules\GiveWp\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Give_WP_Goal extends Give_WP_Base {

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
		return 'cmsmasters-give-wp-goal';
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
		return __( 'GiveWP Goal', 'cmsmasters-elementor' );
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
		return 'cmsicon-goal';
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
			'goal',
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

		if ( empty( $this->get_select_form() ) ) {
			$this->error_section();

			return;
		}

		$this->start_controls_section(
			'section_goal',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$form_list = $this->get_select_form();
		$form_list_keys = array_keys( $form_list );

		if ( empty( $this->get_select_form() ) ) {
			$default = '';
		} else {
			$default = array_reverse( $form_list_keys )[0];
		}

		$this->add_control(
			'form_list',
			array(
				'label' => __( 'Select Form', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => $form_list,
				'default' => $default,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'show_text',
			array(
				'label' => __( 'Show Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_bar',
			array(
				'label' => __( 'Show Bar', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'goal_style',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'goal_alignment',
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
				'selectors' => array(
					'{{WRAPPER}}' => '--goal-align: {{VALUE}};',
				),
				'condition' => array(
					'show_text' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'goal_progress_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-goal .cmsmasters-give-wp-widget .give-goal-progress .raised',
				'condition' => array(
					'show_text' => 'yes',
				),
			)
		);

		$this->update_control(
			'goal_progress_typography_font_size',
			array(
				'selectors' => array(
					'{{WRAPPER}}' => '--goal-font-size: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'goal_title_color',
			array(
				'label' => __( 'Title Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--goal-title-color: {{VALUE}}',
				),
				'condition' => array(
					'show_text' => 'yes',
				),
			)
		);

		$this->add_control(
			'goal_progress_color',
			array(
				'label' => __( 'Progress Bar Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--goal-progress-bar-color: {{VALUE}}',
				),
				'condition' => array(
					'show_bar' => 'yes',
				),
			)
		);

		$this->add_control(
			'goal_progress_bg_color',
			array(
				'label' => __( 'Progress Bar Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--goal-progress-bar-bg-color: {{VALUE}}',
				),
				'condition' => array(
					'show_bar' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'goal_title_gap',
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
					'{{WRAPPER}}' => '--goal-title-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'show_text' => 'yes',
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

		if ( empty( $this->get_select_form() ) ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		$form_id = $this->get_form_id();
		$show_text = ( 'yes' === $settings['show_text'] ) ? true : false;
		$show_bar = ( 'yes' === $settings['show_bar'] ) ? true : false;

		return "[give_goal 
				id=\"{$form_id}\" 
				show_text=\"{$show_text}\" 
				show_bar=\"{$show_bar}\"]";
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
