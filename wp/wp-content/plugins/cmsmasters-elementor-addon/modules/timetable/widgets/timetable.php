<?php

namespace CmsmastersElementor\Modules\Timetable\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Plugin;

use mp_timetable\plugin_core\classes\Shortcode;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Timetable extends Base_Widget {

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.6.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Timetable', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.6.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-table';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.6.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'timetable',
			'table',
			'calendar',
			'event',
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.16.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'widget-cmsmasters-timetable',
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

	public function get_posts_type( $post_type ) {
		$args   = array(
			'post_type' => $post_type,
			'numberposts' => '-1',
		);

		$posts  = get_posts( $args );
		$array = array();

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$array[ $post->ID ] = $post->post_title;
			}
		}

		return $array;
	}

	public function get_terms_type( $term_type ) {

		$args = array(
			'taxonomy'   => $term_type,
			'orderby' => 'count',
			'hide_empty' => false,
		);

		$terms = get_terms( $args );

		$array = array();

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$array[ $term->term_id ] = $term->name;
			}
		}

		return $array;
	}

	private static function show_shortcode( $attributes ) {
		foreach ( $attributes as $key => $value ) {
			if ( is_array( $value ) ) {
				$attributes[ $key ] = implode( ',', $value );
			}

			if ( 'sub_title' === $key ) {
				$attributes['sub-title'] = $attributes[ $key ];
				unset( $attributes[ $key ] );
			}
		}

		echo Shortcode::get_instance()->show_shortcode( $attributes );
	}

	public static function elementor_render_timetable( $attributes ) {

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		self::show_shortcode( $attributes );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.6.0
	 */
	protected function register_controls() {
		$this->content_control();
		$this->style_control();
	}

	/**
	 * Register widget list content section.
	 *
	 * Adds timetable widget `timetable content` settings section controls.
	 *
	 * @since 1.6.0
	 */
	protected function content_control() {
		$this->general_controls();
		$this->filter_content_controls();
		$this->additional_controls();
	}

	/**
	 * Register widget timetable list content section.
	 *
	 * Adds timetable widget content settings controls.
	 *
	 * @since 1.6.0
	 */
	protected function general_controls() {
		$this->start_controls_section(
			'general_settings',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'event-title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'timeslot',
			array(
				'label' => __( 'Time', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'event-subtitle',
			array(
				'label' => __( 'Subtitle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'event-description',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'event-user',
			array(
				'label' => __( 'Event Head', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => '',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register filter settings tab for widget timetable.
	 *
	 * Adds filter settings controls for widget timetable.
	 *
	 * @since 1.6.0
	 */
	protected function filter_content_controls() {
		$this->start_controls_section(
			'filter_settings',
			array(
				'label' => __( 'Filter', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'view',
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'default' => 'dropdown_list',
				'label_block' => false,
				'options' => array(
					'dropdown_list' => __( 'Dropdown', 'cmsmasters-elementor' ),
					'tabs' => __( 'Tabs', 'cmsmasters-elementor' ),
				),
			)
		);

		$this->add_control(
			'view_sort',
			array(
				'label' => __( 'Items Order', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'default' => '',
				'label_block' => false,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'menu_order' => __( 'Menu Order', 'cmsmasters-elementor' ),
					'post_title' => __( 'Title', 'cmsmasters-elementor' ),
				),
			)
		);

		$this->add_control(
			'hide_label',
			array(
				'label' => __( 'All Events', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => '',
				'condition' => array(
					'view' => 'tabs',
				),
			)
		);

		$this->add_control(
			'label',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'All Events', 'cmsmasters-elementor' ),
				'label_block' => false,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'view',
									'operator' => '===',
									'value' => 'tabs',
								),
								array(
									'name' => 'hide_label',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'view',
									'operator' => '===',
									'value' => 'dropdown_list',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'col',
			array(
				'label' => __( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_posts_type( 'mp-column' ),
				'separator' => 'before',
				'label_block' => true,
				'multiple' => true,
			)
		);

		$this->add_control(
			'events',
			array(
				'label' => __( 'Specific Events', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_posts_type( 'mp-event' ),
				'multiple' => true,
				'label_block' => true,
			)
		);

		$this->add_control(
			'event_categ',
			array(
				'label' => __( 'Event Categories', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $this->get_terms_type( 'mp-event_category' ),
				'multiple' => true,
				'label_block' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register additional settings tab for widget timetable.
	 *
	 * Adds additional settings controls for widget timetable.
	 *
	 * @since 1.6.0
	 */
	protected function additional_controls() {
		$this->start_controls_section(
			'additional_settings',
			array(
				'label' => __( 'Additional', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'disable_event_url',
			array(
				'label' => __( 'Disable Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'hide_hrs',
			array(
				'label' => __( 'Hours Column Visibility', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'hide_empty_rows',
			array(
				'label' => __( 'Hide Empty Rows', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'group',
			array(
				'label' => __( 'Merge Cells With Common Events', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'increment',
			array(
				'label' => __( 'Time Frame', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'label_block' => false,
				'options' => array(
					'1' => __( 'Hour (1h)', 'cmsmasters-elementor' ),
					'0.5' => __( 'Half hour (30min)', 'cmsmasters-elementor' ),
					'0.25' => __( 'Quarter hour (15min)', 'cmsmasters-elementor' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'column_width',
			array(
				'label' => __( 'Column Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'label_block' => false,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'auto' => __( 'Auto', 'cmsmasters-elementor' ),
					'fixed' => __( 'Fixed', 'cmsmasters-elementor' ),
				),
			)
		);

		$this->add_control(
			'responsive',
			array(
				'label' => __( 'Mobile behavior', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'label_block' => false,
				'options' => array(
					'0' => __( 'Table', 'cmsmasters-elementor' ),
					'1' => __( 'List', 'cmsmasters-elementor' ),
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget list content section.
	 *
	 * Adds timetable widget `timetable style` settings section controls.
	 *
	 * @since 1.6.0
	 */
	protected function style_control() {
		$this->event_controls();
		$this->event_item_control();
		$this->head_controls();
		$this->filter_style_controls();
		$this->responive_list_controls();
		$this->rsponsive_event_item_controls();
	}

	/**
	 * Register event settings tab for widget timetable.
	 *
	 * Adds event settings controls for widget timetable.
	 *
	 * @since 1.6.0
	 */
	protected function event_controls() {
		$this->start_controls_section(
			'event_style',
			array(
				'label' => __( 'Event', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_event_general',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'General', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'text_align_horizontal',
			array(
				'label' => __( 'Horizontal alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'center',
				'toggle' => false,
				'options' => array(
					'left' => array(
						'title' => __( 'left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
			)
		);

		$this->add_control(
			'text_align_vertical',
			array(
				'label'     => __( 'Vertical alignment', 'cmsmasters-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'middle',
				'toggle'    => false,
				'options'   => array(
					'top'       => array(
						'title' => __( 'top', 'cmsmasters-elementor' ),
						'icon'  => 'eicon-v-align-top',
					),
					'middle'    => array(
						'title' => __( 'middle', 'cmsmasters-elementor' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'bottom'    => array(
						'title' => __( 'bottom', 'cmsmasters-elementor' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
			)
		);

		$this->add_control(
			'event_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-bd-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'row_height',
			array(
				'label' => __( 'Row Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 150 ),
				),
				'default' => array(
					'unit' => 'px',
					'size' => 45,
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 5 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-border-width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'event_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-event-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_time_column',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Time Column', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'hide_hrs' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'time_column_typography',
				'selector' => '{{WRAPPER}} .mptt-shortcode-wrapper .mptt-shortcode-table tbody td.mptt-shortcode-hours',
				'condition' => array(
					'hide_hrs' => 'yes',
				),
			)
		);

		$this->add_control(
			'time_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-time-color: {{VALUE}};',
				),
				'condition' => array(
					'hide_hrs' => 'yes',
				),
			)
		);

		$this->add_control(
			'time_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-time-bg-color: {{VALUE}};',
				),
				'condition' => array(
					'hide_hrs' => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_tablet',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Tablet', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'tablet_border_radius',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array( 'max' => 100 ),
					'%' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-tablet-border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register additional settings tab for widget timetable.
	 *
	 * Adds additional settings controls for widget timetable.
	 *
	 * @since 1.6.0
	 */
	protected function event_item_control() {
		$event_items = array(
			'event-title' => __( 'Title', 'cmsmasters-elementor' ),
			'timeslot' => __( 'Time', 'cmsmasters-elementor' ),
			'event-subtitle' => __( 'Sub Title', 'cmsmasters-elementor' ),
			'event-description' => __( 'Description', 'cmsmasters-elementor' ),
			'event-user' => __( 'Event Head', 'cmsmasters-elementor' ),
		);

		$this->start_controls_section(
			'event_items',
			array(
				'label' => __( 'Event Items', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		foreach ( $event_items as $event_item => $label ) {
			$this->add_control(
				"heading_{$event_item}",
				array(
					'type' => Controls_Manager::HEADING,
					'label' => $label,
					'condition' => array(
						$event_item => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => "{$event_item}_typography",
					'selector' => "{{WRAPPER}} .mptt-shortcode-wrapper .mptt-shortcode-table tbody td .mptt-event-container .{$event_item}",
					'condition' => array(
						$event_item => 'yes',
					),
				)
			);

			$this->add_control(
				"{$event_item}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-timetable-{$event_item}-color: {{VALUE}};",
					),
					'condition' => array(
						$event_item => 'yes',
					),
				)
			);

			if ( 'event-title' === $event_item ) {
				$this->add_control(
					'title_color_hover',
					array(
						'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => array(
							'{{WRAPPER}}' => '--cmsmasters-timetable-title-color-hover: {{VALUE}};',
						),
						'condition' => array(
							$event_item => 'yes',
							'disable_event_url!' => 'yes',
						),
					)
				);
			}

			if ( 'event-user' !== $event_item ) {
				$this->add_control(
					"{$event_item}_gap",
					array(
						'label' => __( 'Gap', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => array( 'px' ),
						'range' => array(
							'px' => array( 'max' => 30 ),
						),
						'separator' => 'after',
						'selectors' => array(
							'{{WRAPPER}}' => "--cmsmasters-timetable-{$event_item}-gap: {{SIZE}}{{UNIT}}",
						),
						'condition' => array(
							$event_item => 'yes',
						),
					)
				);
			}
		}

		$this->end_controls_section();
	}

	 /**
	 * Register Head settings tab for widget timetable.
	 *
	 * Adds Head settings controls for widget timetable.
	 *
	 * @since 1.6.0
	 */
	protected function head_controls() {
		$this->start_controls_section(
			'head_style',
			array(
				'label' => __( 'Table Head', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'thead_typography',
				'selector' => '{{WRAPPER}} .mptt-shortcode-wrapper .mptt-shortcode-table tr.mptt-shortcode-row th',
				'separator' => 'before',

			)
		);

		$this->add_control(
			'thead_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-thead-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'thead_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-thead-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'thead_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-thead-bd-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'thead_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 5 ),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-thead-border-width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'timetable_thead_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-thead-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	 /**
	 * Register filter settings tab for widget timetable.
	 *
	 * Adds filter settings controls for widget timetable.
	 *
	 * @since 1.6.0
	 */
	protected function filter_style_controls() {
		$this->start_controls_section(
			'filter_style',
			array(
				'label' => __( 'Filter', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'view' => 'tabs',
				),
			)
		);

		$this->add_control(
			'heading_filter_item_style',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Item', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'filter_items_align',
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
					'stretch' => array(
						'title' => __( 'Stretch', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-stretch',
					),
				),
				'prefix_class' => 'cmsmasters-timetable__align%s-',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'filter_typography',
				'selector' => '{{WRAPPER}} .mptt-shortcode-wrapper .mptt-navigation-tabs li a',
				'separator' => 'before',

			)
		);

		$this->start_controls_tabs( 'filter_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'active' => __( 'Active', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$this->start_controls_tab(
				"filter_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"filter_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-timetable-filter-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"filter_bg_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-timetable-filter-bg-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"filter_bd_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-timetable-filter-bd-color-{$key}: {{VALUE}};",
					),
					'condition' => array(
						'border_timetable_item_border!' => '',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'filter_h_item_gap',
			array(
				'label' => __( 'Items Horizontal Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 40 ),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-filter-h-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'filter_v_item_gap',
			array(
				'label' => __( 'Items Vertical Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 40 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-filter-v-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_timetable_item',
				'selector' => '{{WRAPPER}} .mptt-shortcode-wrapper .mptt-navigation-tabs li a',
				'separator' => 'before',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'timetable_item_bdr',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-item-bdr: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'timetable_item_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-item-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'heading_filter_conteiner_style',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Conteiner', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'filter_container_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-filter-container-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_container_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 100 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-container-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'timetable_container_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-container-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'filter_style_dropdown',
			array(
				'label' => __( 'Filter', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'view' => 'dropdown_list',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'filter_typography_dropdown',
				'selector' => '{{WRAPPER}} .mptt-shortcode-wrapper .mptt-navigation-select',
			)
		);

		$this->start_controls_tabs( 'filter_tabs_dropdown' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'focus' => __( 'Focus', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$this->start_controls_tab(
				"filter_tab_dropdown_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"filter_dropdown_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-timetable-filter-dropdown-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"filter_dropdown_bg_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-timetable-filter-dropdown-bg-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"filter_dropdown_bd_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-timetable-filter-dropdown-bd-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_responsive_control(
				"timetable_dropdown_bdr_{$key}",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-timetable-dropdown-bdr-{$key}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_dropdown',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => __( 'None', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
					),
					'width' => array(
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
				'selector' => '{{WRAPPER}} .mptt-shortcode-wrapper .mptt-navigation-select',
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'filter_dropdown_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 100 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-container-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'filter_dropdown_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array( 'max' => 1000 ),
					'%' => array( 'max' => 100 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-dropdown-width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'timetable_dropdown_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-dropdown-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	 /**
	 * Register responsive event list settings tab for widget timetable.
	 *
	 * Adds responsive event list settings controls for widget timetable.
	 *
	 * @since 1.6.0
	 */
	protected function responive_list_controls() {
		$this->start_controls_section(
			'responsive_event_list',
			array(
				'label' => __( 'Responsive Event List', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'responsive!' => '0',
				),
			)
		);

		$this->add_control(
			'heading_column_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Column Title', 'cmsmasters-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title_column_typography',
				'selector' => '{{WRAPPER}} .mptt-shortcode-wrapper .mptt-shortcode-list .mptt-column .mptt-column-title',
				'condition' => array(
					'hide_hrs' => 'yes',
				),
			)
		);

		$this->add_control(
			'title_column_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-title-column-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_column_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
					'rem',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-title-column-mrg: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_responsive_event',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Event', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'responsive_event_bd_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 10 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-responsive-event-bd-width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'responsive_event_bd_gap',
			array(
				'label' => __( 'Border Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 100 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-responsive-event-bd-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'responsive_event_gap',
			array(
				'label' => __( 'Event Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 100 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-responsive-event-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'responsive_event_list_margin',
			array(
				'label' => __( 'List Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
					'rem',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-timetable-event-list-mrg: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register responsive event items settings tab for widget timetable.
	 *
	 * Adds responsive event items settings controls for widget timetable.
	 *
	 * @since 1.6.0
	 */
	protected function rsponsive_event_item_controls() {
		$event_items = array(
			'event-title' => __( 'Title', 'cmsmasters-elementor' ),
			'timeslot' => __( 'Time', 'cmsmasters-elementor' ),
			'event-subtitle' => __( 'Sub Title', 'cmsmasters-elementor' ),
			'event-description' => __( 'Description', 'cmsmasters-elementor' ),
			'event-user' => __( 'Event Head', 'cmsmasters-elementor' ),
		);

		$this->start_controls_section(
			'responsive_event_items',
			array(
				'label' => __( 'Responsive Event Items', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'responsive!' => '0',
				),
			)
		);

		foreach ( $event_items as $event_item => $label ) {
			$this->add_control(
				"responsive_heading_{$event_item}",
				array(
					'type' => Controls_Manager::HEADING,
					'label' => $label,
					'condition' => array(
						$event_item => 'yes',
					),
				)
			);

			if ( 'event-title' === $event_item ) {
				$typography = "{{WRAPPER}} .mptt-shortcode-wrapper .mptt-shortcode-list .mptt-column .mptt-events-list .mptt-list-event, {{WRAPPER}} .mptt-shortcode-wrapper .mptt-shortcode-list .mptt-column .mptt-events-list .mptt-event-title";
			} else {
				$typography = "{{WRAPPER}} .mptt-shortcode-wrapper .mptt-shortcode-list .mptt-column .mptt-events-list .{$event_item}";
			}

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => "responsive_{$event_item}_typography",
					'selector' => $typography,
					'condition' => array(
						$event_item => 'yes',
					),
				)
			);

			$this->add_control(
				"responsive_{$event_item}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-timetable-responsive-{$event_item}-color: {{VALUE}};",
					),
					'condition' => array(
						$event_item => 'yes',
					),
				)
			);

			if ( 'event-title' === $event_item ) {
				$this->add_control(
					'responsive_title_color_hover',
					array(
						'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => array(
							'{{WRAPPER}}' => '--cmsmasters-timetable-responsive-title-color-hover: {{VALUE}};',
						),
						'condition' => array(
							$event_item => 'yes',
							'disable_event_url!' => 'yes',
						),
					)
				);
			}

			if ( 'event-title' !== $event_item ) {
				$this->add_control(
					"responsive_{$event_item}_gap",
					array(
						'label' => __( 'Gap', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => array( 'px' ),
						'range' => array(
							'px' => array( 'max' => 30 ),
						),
						'selectors' => array(
							'{{WRAPPER}}' => "--cmsmasters-timetable-responsive-{$event_item}-gap: {{SIZE}}{{UNIT}}",
						),
						'condition' => array(
							$event_item => 'yes',
						),
					)
				);
			}

			$this->add_control(
				"responsive_{$event_item}_divider",
				array(
					'type' => Controls_Manager::DIVIDER,
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.6.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( 'tabs' === $settings['view'] ) {
			$label_hide = 'yes' === $settings['hide_label'] ? '0' : '1';
		} else {
			$label_hide = '0';
		}

		$label = '' === $settings['label'] ? __( 'All Events', 'cmsmasters-elementor' ) : $settings['label'];
		$font_size = '';
		$col = $settings['col'];
		$events = $settings['events'];
		$event_categ = $settings['event_categ'];
		$title = 'yes' === $settings['event-title'] ? '1' : '0';
		$time = 'yes' === $settings['timeslot'] ? '1' : '0';
		$subtitle = 'yes' === $settings['event-subtitle'] ? '1' : '0';
		$description = 'yes' === $settings['event-description'] ? '1' : '0';
		$user = 'yes' === $settings['event-user'] ? '1' : '0';
		$hide_label = $label_hide;
		$hide_hrs = 'yes' === $settings['hide_hrs'] ? '0' : '1';
		$hide_empty_rows = 'yes' === $settings['hide_empty_rows'] ? '1' : '0';
		$group = 'yes' === $settings['group'] ? '1' : '0';
		$disable_event_url = 'yes' === $settings['disable_event_url'] ? '1' : '0';
		$row_height = $settings['row_height']['size'];
		$view_sort = $settings['view_sort'];
		$increment = $settings['increment'];
		$view = $settings['view'];
		$text_align_horizontal = $settings['text_align_horizontal'];
		$text_align_vertical = $settings['text_align_vertical'];
		$column_width = $settings['column_width'];
		$responsive = $settings['responsive'];
		$id = '';
		$custom_class = '';

		$attributes = array(
			'col' => $col,
			'events' => $events,
			'event_categ' => $event_categ,
			'label' => $label,
			'font_size' => $font_size,
			'time' => $time,
			'custom_class' => $custom_class,
			'id' => $id,
			'table_layout' => $column_width,
			'increment' => $increment,
			'view' => $view,
			'view_sort' => $view_sort,
			'hide_label' => $hide_label,
			'hide_hrs' => $hide_hrs,
			'hide_empty_rows' => $hide_empty_rows,
			'title' => $title,
			'sub_title' => $subtitle,
			'description' => $description,
			'user' => $user,
			'group' => $group,
			'disable_event_url' => $disable_event_url,
			'text_align' => $text_align_horizontal,
			'row_height' => $row_height,
			'responsive' => $responsive,
			'text_align_vertical' => $text_align_vertical,
		);

		$this->elementor_render_timetable( $attributes );
	}
}
