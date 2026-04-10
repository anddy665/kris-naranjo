<?php
namespace CmsmastersElementor\Modules\TribeEvents\Widgets\Base_Events;

use CmsmastersElementor\Controls_Manager as CmsmastersManagerControls;
use CmsmastersElementor\Modules\TribeEvents\Module as TribeEventsModule;
use CmsmastersElementor\Modules\TribeEvents\Widgets\Base_Events\Base_Events_Elements;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Addon tribe events widget class.
 *
 * An abstract class to register new Tribe Events widgets.
 *
 * @since 1.13.0
 */
abstract class Base_Events_Customizable extends Base_Events_Elements {

	/**
	 * Displays a tribe events at the template ID.
	 *
	 * @var int
	 */
	protected $template_id = false;

	/**
	 * @since 1.13.0
	 * @since 1.16.6 Add checking template.
	 */
	protected function init( $data ) {
		parent::init( $data );

		$template_id = (int) $this->get_settings_for_display( TribeEventsModule::CONTROL_TEMPLATE_NAME );

		if ( CmsmastersUtils::check_template( $template_id ) ) {
			$this->template_id = $template_id;
		}
	}

	/**
	 * @since 1.13.0
	 */
	public function register_controls() {
		parent::register_controls();

		$this->injection_section_layout();
	}

	/**
	 * Register tribe events controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.13.0
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_style_section_controls() {
		$this->register_event_section_style();

		$this->register_event_customize_section_style();

		$this->register_event_customize_image_section_style();

		$this->register_event_customize_date_section_style();

		$this->register_event_customize_meta_section_style();

		$this->register_event_customize_title_section_style();

		$this->register_event_customize_venue_section_style();

		$this->register_event_customize_excerpt_section_style();

		$this->register_event_customize_button_section_style();

		parent::register_style_section_controls();
	}

	/**
	 * Get HTML wrapper class.
	 *
	 * Retrieve the widget container class. Can be used to override the
	 * container class for specific widgets.
	 *
	 * @since 1.13.0
	 *
	 * @return string
	 */
	protected function get_html_wrapper_class() {
		return parent::get_html_wrapper_class() . ' elementor-widget-cmsmasters-tribe-events-similar';
	}

	/**
	 * @since 1.13.0
	 */
	public function get_wrap_classes() {
		if ( $this->is_setting_as_default( 'event_template' ) ) {
			return array_merge( parent::get_wrap_classes(), array( 'cmsmasters-tribe-events--type-default' ) );
		}
	}

	/**
	 * Get template IDs.
	 *
	 * @since 1.16.6
	 *
	 * @return array Template IDs.
	 */
	public function get_template_ids() {
		if ( $this->is_setting_as_default( 'event_template' ) ) {
			return array();
		}

		if ( empty( $this->template_id ) ) {
			return array();
		}

		return array( $this->template_id );
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.13.0
	 * @since 1.16.6 Optimized loading CSS in templates.
	 */
	public function render() {
		if ( ! $this->is_setting_as_default( 'event_template' ) ) {
			$template_ids = $this->get_template_ids();

			if ( empty( $template_ids ) ) {
				if ( is_admin() ) {
					/* translators: Tribe Events widgets undefined template warning. %s: Tribe Events widget title */
					CmsmastersUtils::render_alert( sprintf( esc_html__( 'Please choose your custom "%s" widget template!', 'cmsmasters-elementor' ), $this->get_title() ) );
				}

				return;
			}

			if ( 'enable' !== $this->lazyload_widget_get_status() ) {
				Plugin::instance()->frontend->print_template_css( $template_ids, $this->get_id() );
			}
		}

		parent::render();
	}

	/**
	 * Register tribe events controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.13.0
	 */
	public function injection_section_layout() {
		$this->start_injection( array(
			'of' => 'section_layout',
			'at' => 'start',
			'type' => 'section',
		) );

		$this->add_control(
			'event_template',
			array(
				'label' => esc_html__( 'Event Template', 'cmsmasters-elementor' ),
				'type' => CmsmastersManagerControls::CHOOSE_TEXT,
				'options' => array(
					'default' => esc_html__( 'Default', 'cmsmasters-elementor' ),
					'custom' => esc_html__( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'render_type' => 'template',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			TribeEventsModule::CONTROL_TEMPLATE_NAME,
			array(
				'label' => __( 'Choose Entry Template', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => CmsmastersManagerControls::QUERY,
				'autocomplete' => array(
					'object' => Query_Manager::TEMPLATE_OBJECT,
					'query' => array(
						'meta_query' => array(
							array(
								'key' => Document::TYPE_META_KEY,
								'value' => 'cmsmasters_tribe_events_entry',
							),
						),
					),
				),
				'frontend_available' => true,
				'condition' => array( 'event_template' => array( 'custom' ) ),
			)
		);

		$this->end_injection();
	}

	/**
	 * Register event box controls.
	 *
	 * @since 1.13.0
	 */
	protected function register_event_section_style() {
		$condition = array( 'event_template' => 'default' );

		$this->start_controls_section(
			'event_section_style',
			array(
				'label' => __( 'Event', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
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
					'{{WRAPPER}}' => '--event-text-align: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->start_controls_tabs(
			'event_tabs',
			array( 'condition' => $condition )
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$this->start_controls_tab(
				"event_{$main_key}_tab",
				array(
					'label' => $label,
					'condition' => $condition,
				)
			);

			$this->add_control(
				"event_{$main_key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--event-{$main_key}-bg-color: {{VALUE}}",
					),
					'condition' => $condition,
				)
			);

			$this->add_control(
				"event_{$main_key}_border_color",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--event-{$main_key}-border-color: {{VALUE}}",
					),
					'condition' => array_merge(
						$condition,
						array( 'event_border_type!' => 'none' )
					),
				)
			);

			$this->add_responsive_control(
				"event_{$main_key}_border_radius",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px' ),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--event-{$main_key}-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}",
					),
					'condition' => $condition,
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "event_{$main_key}_box_shadow",
					'label' => esc_html__( 'Box Shadow', 'cmsmasters-elementor' ),
					'fields_options' => array(
						'box_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => "--event-{$main_key}-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};",
							),
						),
					),
					'selector' => '{{WRAPPER}}',
					'condition' => $condition,
				)
			);

			$this->end_controls_tab();

		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'event_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--event-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_border_type',
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--event-border-type: {{VALUE}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_border_width',
			array(
				'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-border-top-width: {{TOP}}{{UNIT}}; --event-border-right-width: {{RIGHT}}{{UNIT}}; --event-border-bottom-width: {{BOTTOM}}{{UNIT}}; --event-border-left-width: {{LEFT}}{{UNIT}};',
				),
				'condition' => array_merge(
					$condition,
					array(
						'event_border_type!' => array(
							'',
							'none',
						),
					)
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_event_customize_section_style() {
		$this->start_controls_section(
			'event_customize_section_style',
			array(
				'label' => esc_html__( 'Customize', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'event_template' => 'default' ),
			)
		);

		$this->add_control(
			'event_customize_section_elements',
			array(
				'label' => esc_html__( 'Select sections of the cart to customize:', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => array(
					'event_customize_image' => esc_html__( 'Image', 'cmsmasters-elementor' ),
					'event_customize_date' => esc_html__( 'Date', 'cmsmasters-elementor' ),
					'event_customize_meta' => esc_html__( 'Meta', 'cmsmasters-elementor' ),
					'event_customize_title' => esc_html__( 'Title', 'cmsmasters-elementor' ),
					'event_customize_venue' => esc_html__( 'Venue', 'cmsmasters-elementor' ),
					'event_customize_excerpt' => esc_html__( 'Excerpt', 'cmsmasters-elementor' ),
					'event_customize_button' => esc_html__( 'Button', 'cmsmasters-elementor' ),
				),
				'render_type' => 'template',
				'label_block' => true,
				'condition' => array( 'event_template' => 'default' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register event image controls.
	 *
	 * @since 1.13.0
	 */
	protected function register_event_customize_image_section_style() {
		$section_condition = array(
			'event_template' => 'default',
			'event_customize_section_elements' => 'event_customize_image',
		);

		$condition = array_merge(
			$section_condition,
			array( 'event_customize_image_show' => 'yes' )
		);

		$this->start_controls_section(
			'event_customize_image_section_style',
			array(
				'label' => __( 'Customize Image', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $section_condition,
			)
		);

		$this->add_control(
			'event_customize_image_show',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => $section_condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_image_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-image-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_image_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-image-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_image_border_type',
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-image-border-type: {{VALUE}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_image_border_width',
			array(
				'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-image-border-top-width: {{TOP}}{{UNIT}}; --event-customize-image-border-right-width: {{RIGHT}}{{UNIT}}; --event-customize-image-border-bottom-width: {{BOTTOM}}{{UNIT}}; --event-customize-image-border-left-width: {{LEFT}}{{UNIT}};',
				),
				'condition' => array_merge(
					$condition,
					array(
						'event_customize_image_border_type!' => array(
							'',
							'none',
						),
					)
				),
			)
		);

		$this->add_control(
			'event_customize_image_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-image-border-color: {{VALUE}};',
				),
				'condition' => array_merge(
					$condition,
					array( 'event_customize_image_border_type!' => 'none' )
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register event date controls.
	 *
	 * @since 1.13.0
	 */
	protected function register_event_customize_date_section_style() {
		$section_condition = array(
			'event_template' => 'default',
			'event_customize_section_elements' => 'event_customize_date',
		);

		$condition = array_merge(
			$section_condition,
			array( 'event_customize_date_show' => 'yes' )
		);

		$this->start_controls_section(
			'event_customize_date_section_style',
			array(
				'label' => __( 'Customize Date', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $section_condition,
			)
		);

		$this->add_control(
			'event_customize_date_show',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => $section_condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_date_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'flex-end' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-date-alignment: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'event_customize_date_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_date_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-date-color: {{VALUE}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_date_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-date-bg-color: {{VALUE}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_date_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-date-border-color: {{VALUE}};',
				),
				'condition' => array_merge(
					$condition,
					array( 'event_customize_date_border_type!' => 'none' )
				),
			)
		);

		$this->add_responsive_control(
			'event_customize_date_side_gap',
			array(
				'label' => __( 'Side Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-date-side-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_date_top_gap',
			array(
				'label' => __( 'Top Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-date-top-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_date_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-date-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_date_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-date-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_date_border_type',
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-date-border-type: {{VALUE}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_date_border_width',
			array(
				'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-date-border-top-width: {{TOP}}{{UNIT}}; --event-customize-date-border-right-width: {{RIGHT}}{{UNIT}}; --event-customize-date-border-bottom-width: {{BOTTOM}}{{UNIT}}; --event-customize-date-border-left-width: {{LEFT}}{{UNIT}};',
				),
				'condition' => array_merge(
					$condition,
					array(
						'event_customize_date_border_type!' => array(
							'',
							'none',
						),
					)
				),
			)
		);

		$this->add_control(
			'event_customize_date_without_featured_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Without Featured', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_date_without_featured_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-date-without-featured-alignment: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_date_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-date-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_date_weekday_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Weekday', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => $condition,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'event_customize_date_weekday_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-weekday-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-weekday-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-weekday-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-weekday-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-weekday-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-weekday-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-weekday-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-weekday-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-date-weekday-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_date_weekday_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-date-weekday-color: {{VALUE}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_date_weekday_space_between',
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-date-weekday-space-between: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register event Meta controls.
	 *
	 * @since 1.13.0
	 */
	protected function register_event_customize_meta_section_style() {
		$section_condition = array(
			'event_template' => 'default',
			'event_customize_section_elements' => 'event_customize_meta',
		);

		$full_conditions = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'event_template',
					'operator' => '===',
					'value' => 'default',
				),
				array(
					'name' => 'event_customize_section_elements',
					'operator' => '===',
					'value' => 'event_customize_meta',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'event_customize_meta_category_show',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'event_customize_meta_cost_show',
							'operator' => '===',
							'value' => 'yes',
						),
					),
				),
			),
		);

		$category_condition = array_merge(
			$section_condition,
			array( 'event_customize_meta_category_show' => 'yes' )
		);

		$cost_condition = array_merge(
			$section_condition,
			array( 'event_customize_meta_cost_show' => 'yes' )
		);

		$this->start_controls_section(
			'event_customize_meta_section_style',
			array(
				'label' => __( 'Customize Meta', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $section_condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_meta_horizontal_align',
			array(
				'label' => __( 'Horizontal Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'flex-end' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
					'space-between' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'default' => 'space-between',
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-meta-horizontal-align: {{VALUE}}',
				),
				'conditions' => $full_conditions,
			)
		);

		$this->add_responsive_control(
			'event_customize_meta_vertical_align',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => CmsmastersManagerControls::CHOOSE_TEXT,
				'options' => array(
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
					),
					'baseline' => array(
						'title' => __( 'Baseline', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'center',
				'label_block' => false,
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-meta-vertical-align: {{VALUE}}',
				),
				'conditions' => $full_conditions,
			)
		);

		$this->add_responsive_control(
			'event_customize_meta_space_between',
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-meta-space-between: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $full_conditions,
			)
		);

		$this->add_responsive_control(
			'event_customize_meta_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'em' => array(
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-meta-gap: {{SIZE}}{{UNIT}}',
				),
				'conditions' => $full_conditions,
			)
		);

		$this->add_control(
			'event_customize_meta_adaptive',
			array(
				'label' => __( 'Adaptive', 'cmsmasters-elementor' ),
				'type' => CmsmastersManagerControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array(
						'title' => __( 'None', 'cmsmasters-elementor' ),
					),
					'tablet' => array(
						'title' => __( 'Tablet', 'cmsmasters-elementor' ),
					),
					'mobile' => array(
						'title' => __( 'Mobile', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'mobile',
				'label_block' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-meta-breakpoints-',
				'conditions' => $full_conditions,
			)
		);

		$this->add_control(
			'event_customize_meta_adaptive_description',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'Adaptive will appear on resolutions below the one defined in Breakpoint settings. Also, on adaptive will be applied only horizontal alignment (left, center, and right).', 'cmsmasters-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'event_template',
							'operator' => '===',
							'value' => 'default',
						),
						array(
							'name' => 'event_customize_section_elements',
							'operator' => '===',
							'value' => 'event_customize_meta',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'event_customize_meta_category_show',
									'operator' => '===',
									'value' => 'yes',
								),
								array(
									'name' => 'event_customize_meta_cost_show',
									'operator' => '===',
									'value' => 'yes',
								),
							),
						),
						array(
							'name' => 'event_customize_meta_adaptive',
							'operator' => '!==',
							'value' => 'none',
						),
					),
				),
			)
		);

		$this->add_control(
			'event_customize_meta_category_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Category', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => $section_condition,
			)
		);

		$this->add_control(
			'event_customize_meta_category_show',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => $section_condition,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'event_customize_meta_category_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-category-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-category-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-category-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-category-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-category-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-category-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-category-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-category-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-category-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => $category_condition,
			)
		);

		$this->add_control(
			'event_customize_meta_category_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-meta-category-color: {{VALUE}}',
				),
				'condition' => $category_condition,
			)
		);

		$this->add_control(
			'event_customize_meta_category_link_color',
			array(
				'label' => __( 'Link Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-meta-category-link-color: {{VALUE}}',
				),
				'condition' => $category_condition,
			)
		);

		$this->add_control(
			'event_customize_meta_category_link_hover_color',
			array(
				'label' => __( 'Link Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-meta-category-link-hover-color: {{VALUE}}',
				),
				'condition' => $category_condition,
			)
		);

		$this->add_control(
			'event_customize_meta_cost_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Cost', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => $section_condition,
			)
		);

		$this->add_control(
			'event_customize_meta_cost_show',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => $section_condition,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'event_customize_meta_cost_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-cost-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-cost-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-cost-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-cost-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-cost-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-cost-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-cost-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-cost-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-meta-cost-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => $cost_condition,
			)
		);

		$this->add_control(
			'event_customize_meta_cost_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-meta-cost-color: {{VALUE}}',
				),
				'condition' => $cost_condition,
			)
		);

		$this->add_control(
			'event_customize_meta_cost_text_stroke_width',
			array(
				'label' => __( 'Stroke Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
					'size' => '',
				),
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
					'em' => array(
						'min' => 0,
						'max' => 0.2,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-meta-cost-text-stroke-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => $cost_condition,
			)
		);

		$this->add_control(
			'event_customize_meta_cost_text_stroke_color',
			array(
				'label' => __( 'Stroke Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-meta-cost-text-stroke-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'event_template',
							'operator' => '===',
							'value' => 'default',
						),
						array(
							'name' => 'event_customize_section_elements',
							'operator' => '===',
							'value' => 'event_customize_meta',
						),
						array(
							'name' => 'event_customize_meta_cost_show',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'event_customize_meta_cost_text_stroke_width[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register event title controls.
	 *
	 * @since 1.13.0
	 */
	protected function register_event_customize_title_section_style() {
		$condition = array(
			'event_template' => 'default',
			'event_customize_section_elements' => 'event_customize_title',
		);

		$this->start_controls_section(
			'event_customize_title_section_style',
			array(
				'label' => __( 'Customize Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'event_customize_title_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-title-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-title-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-title-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-title-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-title-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-title-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-title-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-title-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-title-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-title-color: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_title_hover_color',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-title-hover-color: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_title_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'em' => array(
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-title-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_title_text_stroke_width',
			array(
				'label' => __( 'Stroke Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
					'size' => '',
				),
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
					'em' => array(
						'min' => 0,
						'max' => 0.2,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-title-text-stroke-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_title_text_stroke_color',
			array(
				'label' => __( 'Stroke Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-title-text-stroke-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'event_template',
							'operator' => '===',
							'value' => 'default',
						),
						array(
							'name' => 'event_customize_section_elements',
							'operator' => '===',
							'value' => 'event_customize_title',
						),
						array(
							'name' => 'event_customize_title_text_stroke_width[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register event venue controls.
	 *
	 * @since 1.13.0
	 */
	protected function register_event_customize_venue_section_style() {
		$section_condition = array(
			'event_template' => 'default',
			'event_customize_section_elements' => 'event_customize_venue',
		);

		$condition = array_merge(
			$section_condition,
			array( 'event_customize_venue_show' => 'yes' )
		);

		$this->start_controls_section(
			'event_customize_venue_section_style',
			array(
				'label' => __( 'Customize Venue', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $section_condition,
			)
		);

		$this->add_control(
			'event_customize_venue_show',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => $section_condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_venue_align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'flex-end' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-venue-align: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'event_customize_venue_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-venue-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-venue-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-venue-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-venue-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-venue-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-venue-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-venue-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-venue-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-venue-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => $condition,
			)
		);

		if ( ! class_exists( 'Tribe__Events__Pro__Main' ) ) {
			$this->add_control(
				'event_customize_venue_color',
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--event-customize-venue-color: {{VALUE}}',
					),
					'condition' => $condition,
				)
			);
		}

		$this->add_control(
			'event_customize_venue_link_color',
			array(
				'label' => __( 'Link Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-venue-link-color: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_venue_hover_color',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-venue-hover-color: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_venue_space_between',
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-venue-space-between: {{SIZE}}{{UNIT}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_venue_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'em' => array(
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-venue-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_venue_icon_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_venue_icon_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-venue-icon-color: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_venue_icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-venue-icon-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => $condition,
			)
		);
		$this->add_responsive_control(
			'event_customize_venue_icon_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-venue-icon-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => $condition,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register event excerpt controls.
	 *
	 * @since 1.13.0
	 */
	protected function register_event_customize_excerpt_section_style() {
		$section_condition = array(
			'event_template' => 'default',
			'event_customize_section_elements' => 'event_customize_excerpt',
		);

		$condition = array_merge(
			$section_condition,
			array( 'event_customize_excerpt_show' => 'yes' )
		);

		$this->start_controls_section(
			'event_customize_excerpt_section_style',
			array(
				'label' => __( 'Customize Excerpt', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $section_condition,
			)
		);

		$this->add_control(
			'event_customize_excerpt_show',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => $section_condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_excerpt_align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
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
					'{{WRAPPER}}' => '--event-customize-excerpt-align: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'event_customize_excerpt_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-excerpt-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-excerpt-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-excerpt-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-excerpt-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-excerpt-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-excerpt-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-excerpt-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-excerpt-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-excerpt-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_excerpt_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-excerpt-color: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_excerpt_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'em' => array(
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-excerpt-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->end_controls_section();
	}

	protected function register_event_customize_button_section_style() {
		$section_condition = array(
			'event_template' => 'default',
			'event_customize_section_elements' => 'event_customize_button',
		);

		$condition = array_merge(
			$section_condition,
			array( 'event_customize_button_show' => 'yes' )
		);

		$this->start_controls_section(
			'event_customize_button_section_style',
			array(
				'label' => esc_html__( 'Customize Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $section_condition,
			)
		);

		$this->add_control(
			'event_customize_button_show',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => $section_condition,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'event_customize_button_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-button-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-button-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-button-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-button-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-button-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-button-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-button-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-button-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-customize-button-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => $condition,
			)
		);

		$this->start_controls_tabs(
			'event_customize_button_tabs',
			array( 'condition' => $condition )
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$state = ( 'normal' === $main_key ? ':before' : ':after' );
			$button_bg_selector = "{{WRAPPER}} .cmsmasters-tribe-events__event-read-more-button{$state}";

			$this->start_controls_tab(
				"event_customize_button_{$main_key}_tab",
				array(
					'label' => $label,
					'condition' => $condition,
				)
			);

			$this->add_control(
				"event_customize_button_{$main_key}_color",
				array(
					'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--event-customize-button-{$main_key}-color: {{VALUE}};",
					),
					'condition' => $condition,
				)
			);

			$this->add_control(
				"event_customize_button_{$main_key}_bg_group_background",
				array(
					'label' => __( 'Background Type', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'color' => array(
							'title' => __( 'Color', 'cmsmasters-elementor' ),
							'icon' => 'eicon-paint-brush',
						),
						'gradient' => array(
							'title' => __( 'Gradient', 'cmsmasters-elementor' ),
							'icon' => 'eicon-barcode',
						),
					),
					'default' => 'color',
					'toggle' => false,
					'render_type' => 'ui',
					'condition' => $condition,
				)
			);

			$this->add_control(
				"event_customize_button_{$main_key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$button_bg_selector => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array_merge(
						$condition,
						array(
							"event_customize_button_{$main_key}_bg_group_background" => array(
								'color',
								'gradient',
							),
						),
					),
				)
			);

			$this->add_control(
				"event_customize_button_{$main_key}_bg_group_color_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 0,
					),
					'render_type' => 'ui',
					'condition' => array_merge(
						$condition,
						array(
							"event_customize_button_{$main_key}_bg_group_background" => array( 'gradient' ),
						),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"event_customize_button_{$main_key}_bg_group_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array_merge(
						$condition,
						array(
							"event_customize_button_{$main_key}_bg_group_background" => array( 'gradient' ),
						),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"event_customize_button_{$main_key}_bg_group_color_b_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'render_type' => 'ui',
					'condition' => array_merge(
						$condition,
						array(
							"event_customize_button_{$main_key}_bg_group_background" => array( 'gradient' ),
						),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"event_customize_button_{$main_key}_bg_group_gradient_type",
				array(
					'label' => __( 'Type', 'cmsmasters-elementor' ),
					'label_block' => false,
					'type' => CmsmastersManagerControls::CHOOSE_TEXT,
					'options' => array(
						'linear' => __( 'Linear', 'cmsmasters-elementor' ),
						'radial' => __( 'Radial', 'cmsmasters-elementor' ),
					),
					'default' => 'linear',
					'render_type' => 'ui',
					'condition' => array_merge(
						$condition,
						array(
							"event_customize_button_{$main_key}_bg_group_background" => array( 'gradient' ),
						),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"event_customize_button_{$main_key}_bg_group_gradient_angle",
				array(
					'label' => __( 'Angle', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'deg' ),
					'default' => array(
						'unit' => 'deg',
						'size' => 180,
					),
					'range' => array(
						'deg' => array( 'step' => 10 ),
					),
					'selectors' => array(
						$button_bg_selector => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{event_customize_button_{$main_key}_bg_group_color_stop.SIZE}}{{event_customize_button_{$main_key}_bg_group_color_stop.UNIT}}, {{event_customize_button_{$main_key}_bg_group_color_b.VALUE}} {{event_customize_button_{$main_key}_bg_group_color_b_stop.SIZE}}{{event_customize_button_{$main_key}_bg_group_color_b_stop.UNIT}})",
					),
					'condition' => array_merge(
						$condition,
						array(
							"event_customize_button_{$main_key}_bg_group_background" => array( 'gradient' ),
							"event_customize_button_{$main_key}_bg_group_gradient_type" => 'linear',
						),
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"event_customize_button_{$main_key}_bg_group_gradient_position",
				array(
					'label' => __( 'Position', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => array(
						'center center' => __( 'Center Center', 'cmsmasters-elementor' ),
						'center left' => __( 'Center Left', 'cmsmasters-elementor' ),
						'center right' => __( 'Center Right', 'cmsmasters-elementor' ),
						'top center' => __( 'Top Center', 'cmsmasters-elementor' ),
						'top left' => __( 'Top Left', 'cmsmasters-elementor' ),
						'top right' => __( 'Top Right', 'cmsmasters-elementor' ),
						'bottom center' => __( 'Bottom Center', 'cmsmasters-elementor' ),
						'bottom left' => __( 'Bottom Left', 'cmsmasters-elementor' ),
						'bottom right' => __( 'Bottom Right', 'cmsmasters-elementor' ),
					),
					'default' => 'center center',
					'selectors' => array(
						$button_bg_selector => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{event_customize_button_{$main_key}_bg_group_color_stop.SIZE}}{{event_customize_button_{$main_key}_bg_group_color_stop.UNIT}}, {{event_customize_button_{$main_key}_bg_group_color_b.VALUE}} {{event_customize_button_{$main_key}_bg_group_color_b_stop.SIZE}}{{event_customize_button_{$main_key}_bg_group_color_b_stop.UNIT}})",
					),
					'condition' => array_merge(
						$condition,
						array(
							"event_customize_button_{$main_key}_bg_group_background" => array( 'gradient' ),
							"event_customize_button_{$main_key}_bg_group_gradient_type" => 'radial',
						),
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"event_customize_button_{$main_key}_border_color",
				array(
					'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--event-customize-button-{$main_key}-border-color: {{VALUE}};",
					),
					'condition' => array_merge(
						$condition,
						array( 'event_customize_button_border_type!' => 'none' )
					),
				)
			);

			$this->add_responsive_control(
				"event_customize_button_{$main_key}_border_radius",
				array(
					'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--event-customize-button-{$main_key}-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
					'condition' => $condition,
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "event_customize_button_{$main_key}_text_shadow",
					'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
					'fields_options' => array(
						'text_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => "--event-customize-button-{$main_key}-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};",
							),
						),
					),
					'selector' => '{{WRAPPER}}',
					'condition' => $condition,
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "event_customize_button_{$main_key}_box_shadow",
					'label' => esc_html__( 'Box Shadow', 'cmsmasters-elementor' ),
					'fields_options' => array(
						'box_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => "--event-customize-button-{$main_key}-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};",
							),
						),
					),
					'selector' => '{{WRAPPER}}',
					'condition' => $condition,
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'event_customize_button_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-button-padding-top: {{TOP}}{{UNIT}}; --event-customize-button-padding-right: {{RIGHT}}{{UNIT}}; --event-customize-button-padding-bottom: {{BOTTOM}}{{UNIT}}; --event-customize-button-padding-left: {{LEFT}}{{UNIT}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'event_customize_button_border_type',
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-button-border-type: {{VALUE}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'event_customize_button_border_width',
			array(
				'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--event-customize-button-border-top-width: {{TOP}}{{UNIT}}; --event-customize-button-border-right-width: {{RIGHT}}{{UNIT}}; --event-customize-button-border-bottom-width: {{BOTTOM}}{{UNIT}}; --event-customize-button-border-left-width: {{LEFT}}{{UNIT}};',
				),
				'condition' => array_merge(
					$condition,
					array(
						'event_customize_button_border_type!' => array(
							'',
							'none',
						),
					)
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @since 1.13.0
	 * @since 1.16.6 Optimized loading CSS in templates.
	 */
	protected function render_event_inner() {
		$settings = $this->get_settings_for_display();

		if ( $this->is_setting_as_default( 'event_template' ) ) {
			$show_image = ( isset( $settings['event_customize_image_show'] ) ? $settings['event_customize_image_show'] : false );

			if ( $this->is_setting_as_default( 'event_template' ) && ( false === $show_image || 'yes' === $show_image ) ) {
				$this->render_event_thumbnail();
			}

			echo '<div class="cmsmasters-tribe-events__event-cont-inner">';

			$image_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

			if ( ! has_post_thumbnail() || ! $image_src || '' === $show_image ) {
				$show_date = ( isset( $settings['event_customize_date_show'] ) ? $settings['event_customize_date_show'] : false );

				if ( false === $show_date || 'yes' === $show_date ) {
					$this->render_event_start_date();
				}
			}

			$this->render_event_meta_wrap();

			$this->render_event_title();

			$show_venue = ( isset( $settings['event_customize_venue_show'] ) ? $settings['event_customize_venue_show'] : false );

			if ( false === $show_venue || 'yes' === $show_venue ) {
				$this->render_event_venue();
			}

			$show_excerpt = ( isset( $settings['event_customize_excerpt_show'] ) ? $settings['event_customize_excerpt_show'] : false );

			if ( false === $show_excerpt || 'yes' === $show_excerpt ) {
				$this->render_event_excerpt();
			}

			$show_button = ( isset( $settings['event_customize_button_show'] ) ? $settings['event_customize_button_show'] : false );

			if ( false === $show_button || 'yes' === $show_button ) {
				$this->render_read_more();
			}

			echo '</div>';
		} else {
			echo Plugin::instance()->frontend->get_widget_template( $this->template_id, false, true );
		}
	}

	/**
	 * Display the event thumbnail.
	 *
	 * @since 1.13.0
	 */
	protected function render_event_thumbnail() {
		$settings = $this->get_settings_for_display();

		if ( ! has_post_thumbnail() ) {
			return;
		}

		$image_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

		if ( $image_src ) {
			$image_src = $image_src[0];
			$title = '';

			if ( null !== get_the_title() ) {
				$title = get_the_title();
			}

			echo '<div class="cmsmasters-tribe-events__event-thumbnail">';
				$show_date = ( isset( $settings['event_customize_date_show'] ) ? $settings['event_customize_date_show'] : false );

			if ( false === $show_date || 'yes' === $show_date ) {
				$this->render_event_start_date();
			}

				echo '<a href="' . esc_attr( get_permalink() ) . '" class="cmsmasters-tribe-events__event-thumbnail__inner">' .
					sprintf( '<img src="%s" alt="%s" />', esc_url( $image_src ), esc_attr( $title ) ) .
				'</a>' .
			'</div>';
		}
	}

	/**
	 * Display the event start date.
	 *
	 * @since 1.13.0
	 * @since 1.16.4 Date translation fixed.
	 */
	protected function render_event_start_date() {
		$event_data = tribe_get_event();

		$start_date = get_post_meta( $event_data->ID, '_EventStartDate', true );

		if ( ! $start_date ) {
			return;
		}

		echo '<div class="cmsmasters-tribe-events__event-start-date">' .
			'<div class="cmsmasters-tribe-events__event-start-date-inner">' .
				'<span class="cmsmasters-tribe-events__event-start-date-day">' .
					wp_kses_post( date_i18n( 'd', strtotime( $start_date ) ) ) .
				'</span>' .
				'<span class="cmsmasters-tribe-events__event-start-date-weekday">' .
					wp_kses_post( date_i18n( 'D', strtotime( $start_date ) ) ) .
				'</span>' .
			'</div>' .
		'</div>';
	}

	/**
	 * Render HTML representation of current event taxonomy terms.
	 *
	 * @since 1.13.0
	 */
	public function render_taxonomy() {
		$terms = get_the_terms( get_the_ID(), 'tribe_events_cat' );

		if ( $terms && ! is_wp_error( $terms ) ) {
			echo '<div class="cmsmasters-tribe-events__event-category">';

			$term_links = array();

			foreach ( $terms as $term ) {
				$term_links[] = '<a href="' . esc_url( get_term_link( $term ) ) . '">' .
					esc_html( $term->name ) .
				'</a>';
			}

			echo wp_kses_post( implode( ', ', $term_links ) );

			echo '</div>';
		}
	}

	/**
	 * Display the event cost.
	 *
	 * @since 1.13.0
	 */
	protected function render_event_cost() {
		$event_data = tribe_get_event();

		echo '<div class="cmsmasters-tribe-events__event-cost">' .
			wp_kses_post( $event_data->cost ) .
		'</div>';
	}

	/**
	 * Display the event category and cost.
	 *
	 * @since 1.13.0
	 */
	protected function render_event_meta_wrap() {
		$settings = $this->get_settings_for_display();

		$terms = get_the_terms( get_the_ID(), 'tribe_events_cat' );
		$show_meta = ( isset( $settings['event_customize_meta_category_show'] ) ? $settings['event_customize_meta_category_show'] : false );
		$event_data = tribe_get_event();
		$cost = $event_data->cost;
		$show_cost = ( isset( $settings['event_customize_meta_cost_show'] ) ? $settings['event_customize_meta_cost_show'] : false );

		if (
			( $terms && ! is_wp_error( $terms ) && ( false === $show_meta || 'yes' === $show_meta ) ) ||
			( $cost && ( false === $show_cost || 'yes' === $show_cost ) )
		) {
			echo '<div class="cmsmasters-tribe-events__event-meta-wrap">';

			if ( $terms && ! is_wp_error( $terms ) && ( false === $show_meta || 'yes' === $show_meta ) ) {
				$this->render_taxonomy();
			}

			if ( $cost && ( false === $show_cost || 'yes' === $show_cost ) ) {
				echo $this->render_event_cost();
			}

			echo '</div>';
		}
	}

	/**
	 * Display the event title.
	 *
	 * @since 1.13.0
	 */
	protected function render_event_title() {
		$title = get_the_title();

		if ( ! $title ) {
			$title = '(' . esc_html__( 'No Title', 'cmsmasters-elementor' ) . ')';
		}

		echo '<div class="cmsmasters-tribe-events__event-heading">' .
			'<h4 class="cmsmasters-tribe-events__event-title">' .
				'<a href="' . esc_url( get_permalink() ) . '">' .
					wp_kses_post( $title ) .
				'</a>' .
			'</h4>' .
		'</div>';
	}

	/**
	 * Print venue icon.
	 *
	 * @since 1.13.0
	 *
	 * @return array Venue icon control.
	 */
	protected function print_venue_icon( $icon ) {
		$icons = array(
			'venue_title' => 'fas fa-map-marker-alt',
			'venue_phone' => 'fas fa-phone-alt',
			'venue_website' => 'fas fa-link',
		);

		return $icons[ $icon ];
	}

	/**
	 * Display the event venue.
	 *
	 * @since 1.13.0
	 */
	protected function render_event_venue() {
		$event_data = tribe_get_event();

		$venue = $event_data->venues[0];

		if ( ! $venue ) {
			return;
		}

		echo '<div class="cmsmasters-tribe-events__event-venue">' .
			'<span class="cmsmasters-tribe-events__event-venue-title">';

		if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
			echo '<a href="' . esc_url( $venue->permalink ) . '">';
		} else {
			echo '<span>';
		}
					Icons_Manager::render_icon(
						array(
							'value' => $this->print_venue_icon( 'venue_title' ),
							'library' => 'fa-solid',
						),
						array( 'aria-hidden' => 'true' )
					);

					echo wp_kses_post( $venue->post_title );

		if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
			echo '</a>';
		} else {
			echo '</span>';
		}

			echo '</span>';

		if ( $venue->phone ) {
			echo '<span class="cmsmasters-tribe-events__event-venue-phone">' .
				'<a href="tel:' . esc_attr( $venue->phone ) . '">';

					Icons_Manager::render_icon(
						array(
							'value' => $this->print_venue_icon( 'venue_phone' ),
							'library' => 'fa-solid',
						),
						array( 'aria-hidden' => 'true' )
					);

					echo wp_kses_post( $venue->phone ) .
				'</a>' .
			'</span>';
		}

		if ( $venue->website ) {
			echo '<span class="cmsmasters-tribe-events__event-venue-website">' .
				'<a href="' . esc_url( $venue->website ) . '">';

					Icons_Manager::render_icon(
						array(
							'value' => $this->print_venue_icon( 'venue_website' ),
							'library' => 'fa-solid',
						),
						array( 'aria-hidden' => 'true' )
					);

					echo wp_kses_post( $venue->website ) .
				'</a>' .
			'</span>';
		}

		echo '</div>';
	}

	/**
	 * Display the event excerpt.
	 *
	 * @since 1.13.0
	 */
	protected function render_event_excerpt() {
		if ( ! get_the_excerpt() ) {
			return;
		}

		$has_excerpt = has_excerpt();

		if ( $has_excerpt ) {
			add_filter( 'wp_trim_excerpt', array( $this, 'filter_wp_trim_excerpt' ) );
		} else {
			add_filter( 'excerpt_length', array( $this, 'filter_excerpt_length' ), 20 );
			add_filter( 'excerpt_more', array( $this, 'filter_excerpt_more' ), 20 );
		}

		echo '<div class="cmsmasters-tribe-events__event-excerpt">' .
			esc_html( get_the_excerpt() ) .
		'</div>';

		if ( $has_excerpt ) {
			remove_filter( 'wp_trim_excerpt', array( $this, 'filter_wp_trim_excerpt' ) );
		} else {
			remove_filter( 'excerpt_length', array( $this, 'filter_excerpt_length' ), 20 );
			remove_filter( 'excerpt_more', array( $this, 'filter_excerpt_more' ), 20 );
		}
	}

	/**
	 * Get text after a trimmed excerpt.
	 *
	 * @since 1.13.0
	 *
	 * @return string
	 */
	public function filter_excerpt_more() {
		return '...';
	}

	/**
	 * Crop excerpt.
	 *
	 * @param string $excerpt
	 *
	 * @return string
	 */
	public function filter_wp_trim_excerpt( $excerpt ) {
		return wp_trim_words( $excerpt, $this->filter_excerpt_length(), $this->filter_excerpt_more() );
	}

	/**
	 * Get maximum number of words in a event excerpt.
	 *
	 * @since 1.13.0
	 *
	 * @return int
	 */
	public function filter_excerpt_length() {
		// return $this->get_settings_fallback( 'excerpt_length' );
		return 20;
	}

	/**
	 * Display the read more.
	 *
	 * @since 1.13.0
	 * @since 1.17.3 Fixed clickability default event button.
	 */
	public function render_read_more() {
		echo '<div class="cmsmasters-tribe-events__event-read-more">' .
			'<a class="cmsmasters-tribe-events__event-read-more-button cmsmasters-theme-button" href="' . esc_url( get_permalink() ) . '">' .
				'<span>' .
					esc_html__( 'Read More', 'cmsmasters-elementor' ) .
				'</span>' .
			'</a>' .
		'</div>';
	}

	/**
	 * Get class for default styling.
	 *
	 * @since 1.13.0
	 *
	 * @return string
	 */
	public static function get_css_class() {
		return 'cmsmasters-tirbe-events--type-default';
	}
}
