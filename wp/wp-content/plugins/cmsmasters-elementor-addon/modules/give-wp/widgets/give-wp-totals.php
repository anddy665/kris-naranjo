<?php
namespace CmsmastersElementor\Modules\GiveWp\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Give_WP_Totals extends Give_WP_Base {

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
		return 'cmsmasters-give-wp-totals';
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
		return __( 'GiveWP Totals', 'cmsmasters-elementor' );
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
		return 'cmsicon-totals';
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
			'totals',
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
		$give_wp_settings = get_option( "give_settings" );

		$this->start_controls_section(
			'section_form',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'total_goal',
			array(
				'label' => __( 'Total Goal', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'label_block' => false,
				'default' => '5000',
				'description' => __( 'Set any numerical value you want as your total goal amount', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'progress_bar',
			array(
				'label' => __( 'Progress Bar', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'message',
			array(
				'label' => __( 'Message', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => array(
					'active' => true,
				),
				'description' => __( 'Any text you would like. To display the dynamic information of the total and the goal use {total} and {total_goal} respectively.', 'cmsmasters-elementor' ),
				'default' => __( 'Hey! We`ve raised {total} of the {total_goal} we are trying to raise for this campaign!.', 'cmsmasters-elementor' ),
				'label_block' => true,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'Enter your link', 'cmsmasters-elementor' ),
				'description' => __( 'Any full URL, including “https://” and/or “www” if necessary.', 'cmsmasters-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'link_text',
			array(
				'label' => __( 'Link Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'Enter your text', 'cmsmasters-elementor' ),
				'label_block' => true,
				'condition' => array(
					'link!' => '',
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
			'totals_style',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'totals_alignment',
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
					'{{WRAPPER}}' => '--totals-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'progress_hading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Progress', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'progress_bar' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'progress_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-totals .cmsmasters-give-wp-widget .give-goal-progress .raised',
				'condition' => array(
					'progress_bar' => 'yes',
				),
			)
		);

		$this->add_control(
			'fund_color',
			array(
				'label' => __( 'Title Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--fund-color: {{VALUE}}',
				),
				'condition' => array(
					'progress_bar' => 'yes',
				),
			)
		);

		$this->add_control(
			'progress_color',
			array(
				'label' => __( 'Progress Bar Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--progress-bar-color: {{VALUE}}',
				),
				'condition' => array(
					'progress_bar' => 'yes',
				),
			)
		);

		$this->add_control(
			'progress_bg_color',
			array(
				'label' => __( 'Progress Bar Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--progress-bar-bg-color: {{VALUE}}',
				),
				'condition' => array(
					'progress_bar' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'funded_gap',
			array(
				'label' => __( 'Title Gap', 'cmsmasters-elementor' ),
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
					'{{WRAPPER}}' => '--funded-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'progress_bar' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'progress_bar_gap',
			array(
				'label' => __( 'Progress Bar Gap', 'cmsmasters-elementor' ),
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
					'{{WRAPPER}}' => '--progress-bar-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'progress_bar' => 'yes',
				),
			)
		);

		$this->add_control(
			'totals_message',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Message', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'totals_message_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-totals .cmsmasters-give-wp-widget .give-totals-shortcode-wrap',
			)
		);

		$this->add_control(
			'totals_message_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--message-color: {{VALUE}}',
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
		$give_wp_settings = get_option( "give_settings" );

		add_filter( 'give_totals_progress_color', array( $this, 'bar_color' ) );

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

		$form_ids = $this->get_string_ids( 'form_list' );
		$cat_ids = $cat;
		$tag_ids = $tag;
		$total_goal = $settings['total_goal'];
		$message = $settings['message'];
		$link = $settings['link'];
		$progress_bar = ( 'yes' === $settings['progress_bar'] ) ? true : false;
		$link_text = ( '' === $settings['link_text'] ) ? __( 'Donation Now', 'cmsmasters-elementor' ) : $settings['link_text'];

		$shortcode = "[give_totals 
					  total_goal=\"{$total_goal}\" 
					  message=\"{$message}\" 
					  link=\"{$link}\" 
					  link_text=\"{$link_text}\" 
					  progress_bar=\"{$progress_bar}\"";

		if ( false !== $form_ids ) {
			$shortcode .= " ids=\"{$form_ids}\"";
		}

		if ( 'disabled' !== $give_wp_settings['categories'] && isset( $give_wp_settings['categories'] ) ) {
			if ( false !== $cat_ids && ! empty( $cat_ids ) ) {
				$shortcode .= " cats=\"{$cat_ids}\"";
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

	public function bar_color() {
		$default_color = $this->rgba_to_hex( 'primary' );
		$progress_bar_color = $default_color;

		return $progress_bar_color;
	}

	public function add_filter_for_editor_content() {
		return false;
	}
}
