<?php
namespace CmsmastersElementor\Modules\GiveWp\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Give_WP_Multi_Form_Goal extends Give_WP_Base {

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
		return 'cmsmasters-give-wp-multi-form-goal';
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
		return __( 'GiveWP Multi-Form Goal', 'cmsmasters-elementor' );
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
		return 'cmsicon-multi-form-goal';
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
			'multi',
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

		$give_wp_settings = get_option( "give_settings" );

		$this->start_controls_section(
			'section_form',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'image',
			array(
				'label' => __( 'Choose Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => array(
					'active' => true,
				),
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_control(
			'goal',
			array(
				'label' => __( 'Goal', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'label_block' => false,
				'default' => '1000',
				'description' => __( 'Set any numerical value you want as your total goal amount', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'heading',
			array(
				'label' => __( 'Heading', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => __( 'Heading', 'cmsmasters-elementor' ),
				'placeholder' => __( 'Enter your text', 'cmsmasters-elementor' ),
				'label_block' => true,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'summary',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'Enter your text', 'cmsmasters-elementor' ),
				'label_block' => true,
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
				'label' => __( 'Select Forms', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => $form_list,
				'multiple' => true,
				'description' => __( 'Use this to list your GiveWP form that you want to combine for the total.', 'cmsmasters-elementor' ),
			)
		);

		if ( 'enabled' === $give_wp_settings['categories'] && isset( $give_wp_settings['categories'] ) ) {
			$cat = $this->get_select_taxonomy( 'give_forms_category' );

			$this->add_control(
				'cat_list',
				array(
					'label' => __( 'Categories', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'options' => $cat,
					'multiple' => true,
					'description' => __( 'Selected GiveWP form category that you want to combine into one total.', 'cmsmasters-elementor' ),
				)
			);
		}

		if ( 'enabled' === $give_wp_settings['tags'] && isset( $give_wp_settings['tags'] ) ) {
			$tag = $this->get_select_taxonomy( 'give_forms_tag' );

			$this->add_control(
				'tag_list',
				array(
					'label' => __( 'Tags', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'options' => $tag,
					'multiple' => true,
					'description' => __( 'Selected GiveWP form category that you want to combine into one total.', 'cmsmasters-elementor' ),
				)
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'addition',
			array(
				'label' => __( 'Addition Options', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'date',
			array(
				'label' => __( 'End Date', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DATE_TIME,
				'label_block' => false,
				'description' => __( 'Define when the multi-form goal should come to an end.', 'cmsmasters-elementor' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'form_multi_goal_style',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'multi_goal_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--multi-goal-bg-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'multi_goal_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--multi-goal-bd-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'multi_goal_prgb_color',
			array(
				'label' => __( 'Progress Bar Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--multi-goal-prgb-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'multi_goal_prgb_bg_color',
			array(
				'label' => __( 'Progress Bar Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--multi-goal-prgb-bg-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'multi_goal_border_width',
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
					'{{WRAPPER}}' => '--multi-goal-border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'multi_goal_border_radius',
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
					'{{WRAPPER}}' => '--multi-goal-border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'multi_goal_content_style',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'heading',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'summary',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'multi_goal_alignment',
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
				'separator' => 'after',
				'selectors' => array(
					'{{WRAPPER}}' => '--multi-goal-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'multi_goal_heading_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Heading', 'cmsmasters-elementor' ),
				'condition' => array(
					'heading!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'multi_goal_title_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-multi-form-goal .cmsmasters-give-wp-widget .give-multi-form-goal-block .give-multi-form-goal-block__text h2',
				'condition' => array(
					'heading!' => '',
				),
			)
		);

		$this->add_control(
			'multi_goal_title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--multi-goal-title-color: {{VALUE}}',
				),
				'condition' => array(
					'heading!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'multi_goal_title_gap',
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
					'{{WRAPPER}}' => '--multi-goal-title-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'heading!' => '',
				),
			)
		);

		$this->add_control(
			'multi_goal_heading_desc',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'condition' => array(
					'summary!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'multi_goal_desc_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-multi-form-goal .cmsmasters-give-wp-widget .give-multi-form-goal-block .give-multi-form-goal-block__text p',
				'condition' => array(
					'summary!' => '',
				),
			)
		);

		$this->add_control(
			'multi_goal_desc_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--multi-goal-desc-color: {{VALUE}}',
				),
				'condition' => array(
					'summary!' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'form_multi_goal_footer',
			array(
				'label' => __( 'Footer', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'multi_goal_footer_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'after',
				'selectors' => array(
					'{{WRAPPER}}' => '--multi-goal-footer-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'multi_goal_heading_value',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Value', 'cmsmasters-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'multi_goal_value_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-multi-form-goal .cmsmasters-give-wp-widget .give-multi-form-goal-block .give-progress-bar-block::part(stat-total-value), #cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-multi-form-goal .cmsmasters-give-wp-widget .give-multi-form-goal-block .give-progress-bar-block::part(stat-count-value), #cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-multi-form-goal .cmsmasters-give-wp-widget .give-multi-form-goal-block .give-progress-bar-block::part(stat-goal-value), #cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-multi-form-goal .cmsmasters-give-wp-widget .give-multi-form-goal-block .give-progress-bar-block::part(stat-time-value)',
			)
		);

		$this->add_control(
			'multi_goal_value_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--multi-goal-value-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'multi_goal_value_gap',
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
					'{{WRAPPER}}' => '--multi-goal-value-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'multi_goal_heading_label',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Label', 'cmsmasters-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'multi_goal_label_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-multi-form-goal .cmsmasters-give-wp-widget .give-multi-form-goal-block .give-progress-bar-block::part(stat-total-label), #cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-multi-form-goal .cmsmasters-give-wp-widget .give-multi-form-goal-block .give-progress-bar-block::part(stat-count-label), #cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-multi-form-goal .cmsmasters-give-wp-widget .give-multi-form-goal-block .give-progress-bar-block::part(stat-goal-label), #cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-multi-form-goal .cmsmasters-give-wp-widget .give-multi-form-goal-block .give-progress-bar-block::part(stat-time-label)',
			)
		);

		$this->add_control(
			'multi_goal_label_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--multi-goal-label-color: {{VALUE}}',
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
		$give_wp_settings = get_option( "give_settings" );

		if ( 'disabled' === $give_wp_settings['categories'] || ! isset( $give_wp_settings['categories'] ) ) {
			$cat = '';
		} else {
			$cat = $this->get_string_ids( 'cat_list' );
		}

		if ( 'disabled' === $give_wp_settings['tags'] || ! isset( $give_wp_settings['tags'] ) ) {
			$tag = '';
		} else {
			$tag = $this->get_string_ids( 'tag_list' );
		}

		$default_color = $this->rgba_to_hex( 'primary' );

		$form_ids = $this->get_string_ids( 'form_list' );
		$cat_ids = $cat;
		$tag_ids = $tag;
		$color = $default_color;
		$goal = $settings['goal'];
		$date = $settings['date'];
		$image = $settings['image']['url'];
		$heading = $settings['heading'];
		$summary = $settings['summary'];

		$shortcode = "[give_multi_form_goal 
					   goal=\"{$goal}\" 
					   color=\"{$color}\" 
					   image=\"{$image}\" 
					   summary=\"{$summary}\" 
					   heading=\"{$heading}\"";

		if ( ! empty( $date ) ) {
			$shortcode .= " enddate=\"{$date}\"";
		}

		if ( false !== $form_ids ) {
			$shortcode .= " ids=\"{$form_ids}\"";
		}

		if ( 'disabled' !== $give_wp_settings['categories'] && isset( $give_wp_settings['categories'] ) ) {
			if ( false !== $cat_ids && ! empty( $cat_ids ) ) {
				$shortcode .= " categories=\"{$cat_ids}\"";
			}
		}

		if ( 'disabled' !== $give_wp_settings['tags'] && isset( $give_wp_settings['tags'] ) ) {
			if ( false !== $tag_ids && ! empty( $tag_ids ) ) {
				$shortcode .= " tags=\"{$tag_ids}\"";
			}
		}

		$shortcode .= "]";

		return $shortcode;
	}

	public function add_filter_for_editor_content() {
		return false;
	}
}
