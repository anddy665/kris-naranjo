<?php
namespace CmsmastersElementor\Modules\TribeEvents\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\TribeEvents\Traits\Tribe_Events_Singular_Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Event date widget.
 *
 * Addon widget that displays date of current Event.
 *
 * @since 1.13.0
 */
class Event_Date extends Base_Widget {

	use Tribe_Events_Singular_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.13.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Event Date', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.13.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-event-date';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.13.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'event',
			'date',
			'time',
			'start',
			'end',
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

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-tribe-events-event-date';
	}

	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
	}

	protected function register_controls() {
		$this->register_event_date_controls_content();

		$this->register_general_controls_style();

		$this->register_event_date_additional_controls_style();
	}

	protected function register_event_date_controls_content() {
		$this->start_controls_section(
			'event_date_section_content',
			array(
				'label' => __( 'Date', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'event_date_switcher',
			array(
				'label' => __( 'Date', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
					),
					'period' => array(
						'title' => __( 'Period', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'start',
			)
		);

		$this->add_control(
			'event_date_orientation',
			array(
				'label' => __( 'Orientation', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'vertical' => array(
						'title' => __( 'Vertical', 'cmsmasters-elementor' ),
					),
					'horizontal' => array(
						'title' => __( 'Horizontal', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'vertical',
				'prefix_class' => 'cmsmasters-event-date-orientation-',
				'condition' => array( 'event_date_switcher!' => 'period' ),
			)
		);

		$this->add_control(
			'event_date_first',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => array(
						'title' => __( 'Default', 'cmsmasters-elementor' ),
					),
					'custom' => array(
						'title' => __( 'Custom', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'default',
				'condition' => array( 'event_date_switcher!' => 'period' ),
			)
		);

		$this->add_control(
			'event_date_first_format',
			array(
				'label' => __( 'Date Format', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'D' => gmdate( 'D' ),
					'D d F' => gmdate( 'D d F' ),
					'F j, Y' => gmdate( 'F j, Y' ),
					'm/d/Y' => gmdate( 'm/d/Y' ),
					'd/m/Y' => gmdate( 'd/m/Y' ),
					'custom' => esc_html__( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => 'F j, Y',
				'condition' => array(
					'event_date_switcher!' => 'period',
					'event_date_first' => 'custom',
				),
			)
		);

		$this->add_control(
			'event_date_first_custom_format',
			array(
				'label' => esc_html__( 'Custom Format', 'cmsmasters-elementor' ),
				'description' => sprintf( '<a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank">%s</a>', esc_html__( 'Documentation on date and time formatting', 'cmsmasters-elementor' ) ),
				'condition' => array(
					'event_date_switcher!' => 'period',
					'event_date_first' => 'custom',
					'event_date_first_format' => 'custom',
				),
			)
		);

		$this->add_control(
			'event_date_additional',
			array(
				'label' => __( 'Additional Date', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array(
						'title' => __( 'None', 'cmsmasters-elementor' ),
					),
					'default' => array(
						'title' => __( 'Default', 'cmsmasters-elementor' ),
					),
					'custom' => array(
						'title' => __( 'Custom', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'none',
				'separator' => 'before',
				'condition' => array( 'event_date_switcher!' => 'period' ),
			)
		);

		$this->add_control(
			'event_date_additional_format',
			array(
				'label' => __( 'Date Format', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'j' => gmdate( 'j' ),
					'D d F' => gmdate( 'D d F' ),
					'F j, Y' => gmdate( 'F j, Y' ),
					'm/d/Y' => gmdate( 'm/d/Y' ),
					'd/m/Y' => gmdate( 'd/m/Y' ),
					'custom' => esc_html__( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => 'F j, Y',
				'condition' => array(
					'event_date_switcher!' => 'period',
					'event_date_additional' => 'custom',
				),
			)
		);

		$this->add_control(
			'event_date_additional_custom_format',
			array(
				'label' => esc_html__( 'Custom Format', 'cmsmasters-elementor' ),
				'description' => sprintf( '<a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank">%s</a>', esc_html__( 'Documentation on date and time formatting', 'cmsmasters-elementor' ) ),
				'condition' => array(
					'event_date_switcher!' => 'period',
					'event_date_additional' => 'custom',
					'event_date_additional_format' => 'custom',
				),
			)
		);

		$this->add_control(
			'event_date_period',
			array(
				'label' => __( 'Period', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => array(
						'title' => __( 'Default', 'cmsmasters-elementor' ),
					),
					'custom' => array(
						'title' => __( 'Custom', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'default',
				'condition' => array( 'event_date_switcher' => 'period' ),
			)
		);

		$this->add_control(
			'event_date_period_format',
			array(
				'label' => __( 'Period Format', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'j' => gmdate( 'j' ),
					'D d F' => gmdate( 'D d F' ),
					'F j, Y' => gmdate( 'F j, Y' ),
					'm/d/Y' => gmdate( 'm/d/Y' ),
					'd/m/Y' => gmdate( 'd/m/Y' ),
					'custom' => esc_html__( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => 'F j, Y',
				'condition' => array(
					'event_date_switcher' => 'period',
					'event_date_period' => 'custom',
				),
			)
		);

		$this->add_control(
			'event_date_period_custom_format',
			array(
				'label' => esc_html__( 'Custom Format', 'cmsmasters-elementor' ),
				'description' => sprintf( '<a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank">%s</a>', esc_html__( 'Documentation on date and time formatting', 'cmsmasters-elementor' ) ),
				'condition' => array(
					'event_date_switcher' => 'period',
					'event_date_period' => 'custom',
					'event_date_period_format' => 'custom',
				),
			)
		);

		$this->add_control(
			'event_date_period_coma',
			array(
				'label' => __( 'Coma', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => array( 'event_date_switcher' => 'period' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_general_controls_style() {
		$this->start_controls_section(
			'event_date_section_style',
			array(
				'label' => __( 'Date', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'event_date_hor_align',
			array(
				'label' => __( 'Horizontel Alignment', 'cmsmasters-elementor' ),
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
					'{{WRAPPER}}' => '--event-date-hor-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'event_date_ver_align',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
					),
					'baseline' => array(
						'title' => __( 'Baseline', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}}' => '--event-date-ver-align: {{VALUE}};',
				),
				'condition' => array(
					'event_date_orientation' => 'horizontal',
					'event_date_additional!' => 'none',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'event_date_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'event_date_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-date-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'event_date_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
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
					'{{WRAPPER}}' => '--event-date-gap: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'event_date_switcher',
							'operator' => '===',
							'value' => 'period',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'event_date_switcher',
									'operator' => '!==',
									'value' => 'period',
								),
								array(
									'name' => 'event_date_additional',
									'operator' => '!==',
									'value' => 'none',
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'event_date_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->end_controls_section();
	}

	protected function register_event_date_additional_controls_style() {
		$this->start_controls_section(
			'event_date_additional_section_style',
			array(
				'label' => __( 'Additional Date', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array( 'event_date_additional!' => 'none' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'event_date_additional_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} .cmsmasters-additional_date',
				'condition' => array( 'event_date_additional!' => 'none' ),
			)
		);

		$this->add_control(
			'event_date_additional_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-additional_date' => '--event-date-color: {{VALUE}};',
				),
				'condition' => array( 'event_date_additional!' => 'none' ),
			)
		);

		$this->add_control(
			'event_date_additional_background_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-additional_date' => '--event-date-background-color: {{VALUE}};',
				),
				'condition' => array( 'event_date_additional!' => 'none' ),
			)
		);

		$this->add_responsive_control(
			'event_date_additional_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-additional_date' => ' --event-date-padding-top: {{TOP}}{{UNIT}}; --event-date-padding-right: {{RIGHT}}{{UNIT}}; --event-date-padding-bottom: {{BOTTOM}}{{UNIT}}; --event-date-padding-left: {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'event_date_additional!' => 'none' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'event_date_additional_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-date-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}} .cmsmasters-additional_date',
				'condition' => array( 'event_date_additional!' => 'none' ),
			)
		);

		$this->end_controls_section();
	}

	protected function get_format( $level ) {
		$settings = $this->get_settings_for_display();

		$format = '';

		$date = ( isset( $settings[ "event_date_{$level}" ] ) ? $settings[ "event_date_{$level}" ] : '' );
		$event_date_format = ( isset( $settings[ "event_date_{$level}_format" ] ) ? $settings[ "event_date_{$level}_format" ] : '' );
		$event_date_custom_format = ( isset( $settings[ "event_date_{$level}_custom_format" ] ) ? $settings[ "event_date_{$level}_custom_format" ] : '' );

		if ( 'custom' === $date && 'custom' !== $event_date_format ) {
			$format = $event_date_format;
		} elseif ( 'custom' === $date && 'custom' === $event_date_format && '' !== $event_date_custom_format ) {
			$format = $event_date_custom_format;
		} else {
			$format = ( 'F j, Y' );
		}

		return $format;
	}

	/**
	 * Get event date.
	 *
	 * @since 1.13.0
	 * @since 1.15.0 Date translation fixed.
	 */
	protected function get_date( $switcher, $date, $period ) {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'date', 'class', array(
			$this->get_widget_class() . '__' . $switcher . '-date',
			$this->get_widget_class() . '__date',
		) );

		echo '<div ' . $this->get_render_attribute_string( 'date' ) . '>';

		if ( 'start' === $switcher || 'end' === $switcher ) {
			echo '<div class="' . $this->get_widget_class() . '__' . esc_attr( $switcher ) . '-date-first cmsmasters-first-date">' .
				date_i18n( $this->get_format( 'first' ), strtotime( $date ) ) .
			'</div>';

			$event_date_additional = ( isset( $settings['event_date_additional'] ) ? $settings['event_date_additional'] : '' );

			if ( 'none' !== $event_date_additional ) {
				echo '<div class="' . $this->get_widget_class() . '__' . esc_attr( $switcher ) . '-date-additional cmsmasters-additional_date">' .
					date_i18n( $this->get_format( 'additional' ), strtotime( $date ) ) .
				'</div>';
			}
		} elseif ( 'period' === $switcher ) {
			$coma = ( isset( $settings['event_date_period_coma'] ) ? $settings['event_date_period_coma'] : '' );

			$start = new \DateTime( $date );
			$end = new \DateTime( $period );

			$interval = $start->diff( $end );

			$items = array(
				'y' => 'years',
				'm' => 'months',
				'd' => 'days',
				'h' => 'hours',
				'i' => 'minutes',
				's' => 'seconds',
			);

			$keys = array_keys( $items );
			$last_key = end( $keys );

			foreach ( $items as $item => $value ) {
				if ( $interval->$item > 0 ) {
					echo '<div class="' . $this->get_widget_class() . '__' . esc_attr( $switcher ) . '-' . esc_attr( $value ) . '">';

						$formatted_output = $interval->format( '%' . esc_html( $item ) . ' ' . esc_html( $value ) . ( $last_key !== $item && $coma ? ',' : '' ) );

						echo esc_html( $formatted_output );

					echo '</div>';
				}
			}
		}

		echo '</div>';
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.13.0
	 */
	protected function render() {
		if ( ! get_post() ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		$event_data = tribe_get_event();

		if ( ! $event_data ) {
			return;
		}

		$event_id = $event_data->ID;

		$event_start_date = get_post_meta( $event_id, '_EventStartDate', true );
		$event_end_date = get_post_meta( $event_id, '_EventEndDate', true );

		$date_switcher = ( isset( $settings['event_date_switcher'] ) ? $settings['event_date_switcher'] : '' );
		$start_date = ( isset( $event_start_date ) ? $event_start_date : '' );
		$end_date = ( isset( $event_end_date ) ? $event_end_date : '' );

		if ( '' !== $end_date || '' !== $start_date ) {
			if ( 'start' === $date_switcher && '' !== $start_date ) {
				$this->get_date( $date_switcher, $start_date, '' );
			} elseif ( 'end' === $date_switcher && '' !== $end_date ) {
				$this->get_date( $date_switcher, $end_date, '' );
			} elseif ( 'period' === $date_switcher && '' !== $start_date && '' !== $end_date ) {
				$this->get_date( $date_switcher, $start_date, $end_date );
			}
		}
	}
}
