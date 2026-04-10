<?php
namespace CmsmastersElementor\Modules\Blog\Widgets\Ticker_Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Slider extends Base {

	public function get_id() {
		return 'slider';
	}

	public function get_title() {
		return __( 'Slider', 'cmsmasters-elementor' );
	}

	/**
	 * Register controls.
	 *
	 * @since 1.5.0 Fixed notice "_skin".
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->section_slider();
		$this->section_style_navigation();
	}

	protected function section_slider() {
		$this->start_controls_section(
			'section_slider',
			array(
				'label' => __( 'Slider', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'navigation',
			array(
				'label' => __( 'Navigation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'arrows',
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'arrows' => __( 'Arrows', 'cmsmasters-elementor' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'animation_effect',
			array(
				'label' => __( 'Animation Effect', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => array(
					'slide' => __( 'Slide', 'cmsmasters-elementor' ),
					'fade' => __( 'Fade', 'cmsmasters-elementor' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'animation_speed',
			array(
				'label' => __( 'Animation Speed', 'cmsmasters-elementor' ) . ' (' . __( 'ms', 'cmsmasters-elementor' ) . ')',
				'type' => Controls_Manager::NUMBER,
				'default' => 500,
				'frontend_available' => true,
			)
		);

		$this->start_controls_tabs( 'arrow_tabs' );

		foreach ( array(
			'prev' => __( 'Prev', 'cmsmasters-elementor' ),
			'next' => __( 'Next', 'cmsmasters-elementor' ),
		) as $key => $label ) {
			$this->start_controls_tab(
				'arrow_' . $key,
				array(
					'label' => $label,
					'condition' => array( $this->get_control_id( 'navigation!' ) => '' ),
				)
			);

			$this->add_control(
				"arrow_{$key}_icon",
				array(
					'label' => __( 'Icon', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::ICONS,
					'default' => array(
						'value' => ( 'prev' === $key ? 'fas fa-chevron-left' : 'fas fa-chevron-right' ),
						'library' => 'fa-solid',
					),
					'render_type' => 'template',
					'condition' => array( $this->get_control_id( 'navigation!' ) => '' ),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function section_style_navigation() {
		$this->start_controls_section(
			'section_style_navigation',
			array(
				'label' => __( 'Navigation', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( $this->get_control_id( 'navigation!' ) => '' ),
			)
		);

		$this->add_responsive_control(
			'navigation_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-navigation-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( $this->get_control_id( 'navigation!' ) => '' ),
			)
		);

		$this->add_control(
			'navigation_button_heading',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( $this->get_control_id( 'navigation!' ) => '' ),
			)
		);

		$this->add_control(
			'navigation_button_full_height',
			array(
				'label' => __( 'Full Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors_dictionary' => array(
					'no' => 'center',
					'yes' => 'stretch',
				),
				'selectors' => array(
					'{{WRAPPER}} .swiper-buttons-wrap' => 'align-self: {{VALUE}};',
				),
				'condition' => array( $this->get_control_id( 'navigation!' ) => '' ),
			)
		);

		$this->start_controls_tabs( 'navigation_style_tabs', array(
			'condition' => array( $this->get_control_id( 'navigation!' ) => '' ),
		) );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $key => $label ) {
			if ( 'normal' === $key ) {
				$selector = '{{WRAPPER}} .swiper-button';
			} else {
				$selector = '{{WRAPPER}} .swiper-button:hover, {{WRAPPER}} swiper-button:focus';
			}

			$this->start_controls_tab(
				'navigation_style_tab_' . $key,
				array(
					'label' => $label,
					'condition' => array( $this->get_control_id( 'navigation!' ) => '' ),
				)
			);

			$this->add_control(
				'navigation_color_' . $key,
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'color: {{VALUE}}; fill: {{VALUE}};',
					),
					'condition' => array( $this->get_control_id( 'navigation!' ) => '' ),
				)
			);

			$this->add_control(
				'navigation_bg_' . $key,
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'background-color: {{VALUE}};',
					),
					'condition' => array( $this->get_control_id( 'navigation!' ) => '' ),
				)
			);

			$this->add_control(
				'navigation_bd_' . $key,
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id( 'navigation!' ) => '',
						$this->get_control_id( 'navigation_button_border_border!' ) => '',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'navigation_button_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => array( $this->get_control_id( 'navigation!' ) => '' ),
			)
		);

		$this->add_responsive_control(
			'navigation_button_size',
			array(
				'label' => __( 'Button Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 20,
						'max' => 50,
					),
					'%' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .swiper-button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'navigation!' ) => '',
					$this->get_control_id( 'navigation_button_full_height!' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'navigation_gap_between',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .swiper-button + .swiper-button' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( $this->get_control_id( 'navigation!' ) => '' ),
			)
		);

		$this->add_responsive_control(
			'navigation_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 20 ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .swiper-button' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( $this->get_control_id( 'navigation!' ) => '' ),
			)
		);

		$this->add_responsive_control(
			'navigation_button_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .swiper-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'navigation!' ) => '',
					$this->get_control_id( 'navigation_button_full_height' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'navigation_button_border',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} .swiper-button',
			)
		);

		$this->add_responsive_control(
			'navigation_button_border_radius',
			array(
				'label' => __( 'Botder Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .swiper-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( $this->get_control_id( 'navigation!' ) => '' ),
			)
		);

		$this->end_controls_section();
	}

	public function render_arrow() {
		$settings = $this->parent->get_settings();

		if ( ! $settings[ $this->get_control_id( 'navigation' ) ] ) {
			return;
		}

		echo '<div class="swiper-buttons-wrap">';

		foreach ( array( 'prev', 'next' ) as $key ) {
			if ( $settings[ 'slider_arrow_' . $key . '_icon' ] ) {
				echo '<div class="swiper-button swiper-button-' . $key . '">';

					Icons_Manager::render_icon(
						$settings[ 'slider_arrow_' . $key . '_icon' ],
						array( 'aria-hidden' => 'true' )
					);

				echo '</div>';
			}
		}

		echo '</div>';
	}

	protected function render_posts() {
		parent::render_posts();

		$this->render_arrow();
	}

	/**
	 * Render each slides.
	 *
	 * @since 1.7.4 Fix for v8.45 swiper slider.
	 */
	protected function render_posts_loop() {
		echo '<div class="swiper cmsmasters-swiper-container">' .
			'<div class="swiper-wrapper">';
				parent::render_posts_loop();
			echo '</div>' .
		'</div>';
	}

	protected function render_post() {
		echo '<div class="swiper-slide">';
			parent::render_post();
		echo '</div>';
	}

}
