<?php
namespace CmsmastersElementor\Modules\GiveWp\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

use WPML\Collect\Support\Arr;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Give_WP_Form_Grid extends Give_WP_Base {

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
		return 'cmsmasters-give-wp-form-grid';
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
		return __( 'GiveWP Form Grid - Deprecated', 'cmsmasters-elementor' );
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
		return 'cmsicon-form-grid';
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
			'form',
			'grid',
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
			'section_general',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'display_style',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'redirect' => array(
						'title' => __( 'Redirect', 'cmsmasters-elementor' ),
					),
					'modal_reveal' => array(
						'title' => __( 'Reveal', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'modal_reveal',
				'toggle' => false,
				'render_type' => 'template',
				'label_block' => false,
			)
		);

		$this->add_control(
			'forms_per_page',
			array(
				'label' => __( 'Forms', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'label_block' => false,
				'default' => '9',
				'separator' => 'before',
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

		if ( 'enabled' === $give_wp_settings['form_featured_img'] && isset( $give_wp_settings['form_featured_img'] ) ) {
			$this->add_control(
				'show_featured_image',
				array(
					'label' => __( 'Show Featured Image', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'label_off' => __( 'No', 'cmsmasters-elementor' ),
					'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name' => 'image_size', // Actually its `image_size`
					'default' => 'full',
					'condition' => array(
						'show_featured_image' => 'yes',
					),
				)
			);
		}

		$this->add_control(
			'show_goal',
			array(
				'label' => __( 'Show Goal', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_donate_button',
			array(
				'label' => __( 'Show Donate Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'donate_button_text',
			array(
				'label' => __( 'Donate Button Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'Enter your text', 'cmsmasters-elementor' ),
				'label_block' => true,
				'condition' => array(
					'show_donate_button' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_excerpt',
			array(
				'label' => __( 'Show Excerpt', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label' => __( 'Excerpt Length', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'label_block' => false,
				'default' => '140',
				'condition' => array(
					'show_excerpt' => 'yes',
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

		$this->add_control(
			'inc_exc',
			array(
				'label' => __( 'Forms', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inc' => array(
						'title' => __( 'Include', 'cmsmasters-elementor' ),
					),
					'exc' => array(
						'title' => __( 'Exclude', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'inc',
				'toggle' => false,
				'render_type' => 'template',
				'label_block' => false,
				'separator' => 'before',
			)
		);

		$form_list = $this->get_select_form();

		$this->add_control(
			'form_list',
			array(
				'label' => __( 'Forms', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => $form_list,
				'multiple' => true,
				'show_label' => false,
				'placeholder' => __( 'Select Form', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label' => __( 'Order By', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'date' => __( 'Date', 'cmsmasters-elementor' ),
					'title' => __( 'Title', 'cmsmasters-elementor' ),
					'amount_donated' => __( 'Amount Donated', 'cmsmasters-elementor' ),
					'number_donations' => __( 'Number Donations', 'cmsmasters-elementor' ),
					'menu_order' => __( 'Menu Order', 'cmsmasters-elementor' ),
					'post__in' => __( 'Post In', 'cmsmasters-elementor' ),
					'closest_to_goal' => __( 'Closest To Goal', 'cmsmasters-elementor' ),
				),
				'default' => 'date',
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

		$this->add_control(
			'hide_load_more',
			array(
				'label' => __( 'Hide Pagination', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'prefix_class' => 'cmsmasters-hide__load-more-',
				'render_type' => 'template',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-hide__load-more-yes .give-page-numbers' => 'display: none !important; visibility: hidden !important;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'form_grid_style',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'grid_gap',
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
					'{{WRAPPER}}' => '--grid-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'grid_border_width',
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
					'{{WRAPPER}}' => '--grid-border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'grid_border_radius',
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
					'{{WRAPPER}}' => '--grid-border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'grid_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--grid-bg-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'grid_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--grid-bd-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'form_grid_title_style',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'grid_title_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-form-grid .cmsmasters-give-wp-widget .give-form-grid-content .give-form-grid-content__title',
			)
		);

		$this->update_control(
			'grid_title_typography_font_size',
			array(
				'selectors' => array(
					'{{WRAPPER}}' => '--title-font-size: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->update_control(
			'grid_title_typography_font_weight',
			array(
				'selectors' => array(
					'{{WRAPPER}}' => '--title-font-weight: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'grid_title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--grid-title-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'grid_title_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--grid-title-hover-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'grid_title_gap',
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
					'{{WRAPPER}}' => '--grid-title-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'form_grid_desc_style',
			array(
				'label' => __( 'Excerpt', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_excerpt' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'grid_desc_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-form-grid .cmsmasters-give-wp-widget .give-form-grid-content .give-form-grid-content__text',
			)
		);

		$this->add_control(
			'grid_desc_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--grid-desc-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'grid_desc_gap',
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
					'{{WRAPPER}}' => '--grid-desc-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'form_grid_button_style',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_donate_button' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'grid_button_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-form-grid .cmsmasters-give-wp-widget .give-form-grid-content button',
			)
		);

		$this->add_control(
			'grid_button_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--grid-button-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'grid_button_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--grid-button-hover-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'form_grid_goal_style',
			array(
				'label' => __( 'Goal', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_goal' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'grid_goal_gap',
			array(
				'label' => __( 'Goal Gap', 'cmsmasters-elementor' ),
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
					'{{WRAPPER}}' => '--grid-goal-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_bar',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Progress Bar', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'grid_bar_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--grid-bar-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'grid_bar_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--grid-bar-bg-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'grid_bar_gap',
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
					'{{WRAPPER}}' => '--grid-bar-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_amount',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Amount', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'grid_amount_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-form-grid .cmsmasters-give-wp-widget .give-form-grid-progress .form-grid-raised__details .amount',
			)
		);

		$this->update_control(
			'grid_amount_typography_font_weight',
			array(
				'selectors' => array(
					'{{WRAPPER}}' => '--amount-font-weight: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'amount_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--amount-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'heading_goal',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Goal', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'grid_goal_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}}.elementor-widget-cmsmasters-give-wp-form-grid .cmsmasters-give-wp-widget .give-form-grid-progress .form-grid-raised__details .goal',
			)
		);

		$this->add_control(
			'goal_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--goal-color: {{VALUE}}',
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

		if ( 'disabled' === $give_wp_settings['form_featured_img'] || ! isset( $give_wp_settings['form_featured_img'] ) ) {
			$state_image = false;
			$size = '';
		} else {
			$state_image = ( 'yes' === $settings['show_featured_image'] ) ? true : false;
			$size = $settings['image_size_size'];
		}

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

		$display_style = $settings['display_style'];
		$forms_per_page = $settings['forms_per_page'];
		$columns = $settings['columns'];
		$show_title = true;
		$show_featured_image = $state_image;
		$image_size = $size;
		$tag_list = $tag;
		$cat_list = $cat;
		$show_goal = ( 'yes' === $settings['show_goal'] ) ? true : false;
		$progress_bar_color = '#000';
		$show_donate_button = ( 'yes' === $settings['show_donate_button'] ) ? true : false;
		$show_excerpt = ( 'yes' === $settings['show_excerpt'] ) ? true : false;
		$excerpt_length = $settings['excerpt_length'];
		$forms = $this->include_or_exclude();
		$orderby = $settings['orderby'];
		$order = $settings['order'];
		$donate_button_text = ( '' === $settings['donate_button_text'] ) ? __( 'Donate Now', 'cmsmasters-elementor' ) : $settings['donate_button_text'];

		$shortcode = "[give_form_grid
					  display_style=\"{$display_style}\"
					  forms_per_page=\"{$forms_per_page}\"
					  columns=\"{$columns}\"
					  show_title=\"{$show_title}\"
					  show_donate_button=\"{$show_donate_button}\"
					  show_excerpt=\"{$show_excerpt}\"
					  excerpt_length=\"{$excerpt_length}\"
					  show_goal=\"{$show_goal}\"
					  progress_bar_color=\"{$progress_bar_color}\"
					  orderby=\"{$orderby}\"
					  order=\"{$order}\"
					  donate_button_text=\"{$donate_button_text}\"";

		if ( 'disabled' !== $give_wp_settings['categories'] && isset( $give_wp_settings['categories'] ) ) {
			if ( false !== $cat_list && ! empty( $cat_list ) ) {
				$shortcode .= " cats=\"{$cat_list}\"";
			}
		}

		if ( 'disabled' !== $give_wp_settings['tags'] && isset( $give_wp_settings['tags'] ) ) {
			if ( false !== $tag_list && ! empty( $tag_list ) ) {
				$shortcode .= " tags=\"{$tag_list}\"";
			}
		}

		if ( false !== $state_image ) {
			$shortcode .= " image_size=\"{$image_size}\"";
		}

		if ( false !== $forms ) {
			$shortcode .= $forms;
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

	/**
	 * Get form id.
	 *
	 * Retrieve the form id.
	 *
	 * @since 1.6.0
	 *
	 * @return string The id form.
	 */
	public function include_or_exclude() {
		$settings = $this->get_settings_for_display();
		$tabs = $settings['inc_exc'];

		$form_list = $this->get_string_ids( 'form_list' );

		$forms = false;

		if ( 'inc' === $tabs ) {
			if ( false !== $form_list ) {
				$forms = " ids=\"{$form_list}\"";
			}
		}

		if ( 'exc' === $tabs ) {
			if ( false !== $form_list ) {
				$forms = " exclude=\"{$form_list}\"";
			}
		}

		return $forms;
	}

	public function add_filter_for_editor_content() {
		return false;
	}
}
