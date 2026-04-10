<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets\Skins;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Product_Images_Anchor extends Product_Images_Base {

	/**
	 * Get skin id.
	 *
	 * Retrieve skin id.
	 *
	 * @since 1.0.0
	 *
	 * @return string Skin id.
	 */
	public function get_id() {
		return 'anchor';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Anchor', 'cmsmasters-elementor' );
	}

	public function general_section_extend() {
		$this->parent->start_injection( array(
			'of' => $this->get_id() . '_link_type',
		) );

		$this->add_control(
			'navigation_position',
			array(
				'label' => esc_html__( 'Navigation Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'left',
				'separator' => 'before',
				'toggle' => false,
				'selectors_dictionary' => array(
					'left' => 'flex-direction: row;',
					'right' => 'flex-direction: row-reverse;',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-images__anchor-wrap' => '{{VALUE}};',
				),
				'prefix_class' => 'cmsmasters-position-',
			)
		);

		$this->parent->end_injection();
	}

	public function register_controls_styles() {
		$this->register_controls_images_style();

		$this->register_controls_controller_style();
	}

	public function register_controls_images_style() {
		$this->start_controls_section(
			'section_gallery_images_style',
			array(
				'label' => esc_html__( 'Images', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'gap_between',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'%' => array(
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-images__wrapper' => '--col-margin: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'gallery_images_border',
				'label' => esc_html__( 'Border', 'cmsmasters-elementor' ),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-images__wrapper-item',
			)
		);

		$this->add_control(
			'gallery_images_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-images__wrapper-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'gallery_images_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-images__wrapper-item',
			)
		);

		$this->end_controls_section();
	}

	public function register_controls_controller_style() {
		$this->start_controls_section(
			'section_controller_style',
			array(
				'label' => esc_html__( 'Navigation Bullets', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'navigation_bullets_position',
			array(
				'label' => esc_html__( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'isLinked' => false,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-images__nav-wrap' => 'margin-top: {{TOP}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}} .controller-item__bullet' => 'margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-images__anchor-wrap' => '--navigation-margin-left: {{LEFT}}{{UNIT}}; --navigation-margin-right: {{RIGHT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bullets_size',
			array(
				'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 3,
						'max' => 30,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-images__anchor-wrap' => '--bullet-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bullets_gap',
			array(
				'label' => esc_html__( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-images__anchor-wrap' => '--bullet-gap-between: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'bullets_border',
				'label' => esc_html__( 'Border', 'cmsmasters-elementor' ),
				'placeholder' => '1px',
				'default' => '1px',
				'exclude' => array( 'color' ),
				'selector' => '{{WRAPPER}} .controller-item__bullet',
			)
		);

		$this->start_controls_tabs( 'bullets_style_tabs' );

		$this->start_controls_tab(
			'bullets_normal_styles',
			array(
				'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'bullets_normal_background_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .controller-item__bullet' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'bullets_normal_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .controller-item__bullet' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'anchor_bullets_border_border!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'bullets_normal_shadow',
				'selector' => '{{WRAPPER}} .controller-item__bullet',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'bullets_hover_styles',
			array(
				'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'bullets_hover_background_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .controller-item__bullet:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'bullets_hover_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .controller-item__bullet:hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'anchor_bullets_border_border!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'bullets_hover_shadow',
				'selector' => '{{WRAPPER}} .controller-item__bullet:hover',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'bullets_current_styles',
			array(
				'label' => esc_html__( 'Current', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'bullets_current_background_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .current-item .controller-item__bullet' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'bullets_current_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .current-item .controller-item__bullet' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'anchor_bullets_border_border!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'bullets_current_shadow',
				'selector' => '{{WRAPPER}} .current-item .controller-item__bullet',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'bullets_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .controller-item__bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render skin output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		global $product;

		$settings = $this->parent->get_settings();

		if ( ! empty( $product ) ) {
			$this->render_anchor_images( $settings, $product );
		} else {
			printf(
				'<div class="cmsmasters-elementor__content">%s</div>',
				esc_html__( 'Not found product with current id', 'cmsmasters-elementor' )
			);
		}
	}

	/**
	 * Print images.
	 *
	 * Prints images from product gallery.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Fixed product image display if images are added to the gallery.
	 * @since 1.11.8 Fixed image output if only 1 image in gallery is selected.
	 */
	protected function render_anchor_images( $settings, $product ) {
		$attachment_ids = $product->get_gallery_image_ids();
		$has_gallery = ( 1 <= count( $attachment_ids ) );

		empty( $attachment_ids ) ? $attachment_ids[0] = $product->get_image_id() : '';

		if ( $attachment_ids && $product->get_image_id() ) {
			echo '<div class="elementor-widget-cmsmasters-woo-product-images__anchor-wrap">';

			$thumbnail_image_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

			if ( $thumbnail_image_src && $has_gallery ) {
				$this->render_anchor_nav( $attachment_ids );

				array_unshift( $attachment_ids, get_post_thumbnail_id() );
			}

			$this->open_wrap();

			foreach ( $attachment_ids as $attachment_id ) {
				$image_src = wp_get_attachment_image_src( $attachment_id, 'full' );

				$this->parent->add_render_attribute( 'image_wrapper_' . $attachment_id, array(
					'class' => 'elementor-widget-cmsmasters-woo-product-images__wrapper-item',
					'id' => esc_attr( $attachment_id ),
				) );

				if ( 'zoom' === $settings['anchor_link_type'] ) {
					$this->parent->add_render_attribute( 'image_wrapper_' . $attachment_id, array(
						'class' => 'elementor-widget-cmsmasters-woo-product-images__zoom',
					) );
				}

				echo '<div ' . $this->parent->get_render_attribute_string( 'image_wrapper_' . $attachment_id ) . '>';

				$this->render_image( $settings, $attachment_id, $image_src, 'image_size' );

				echo '</div>';
			}

			$this->close_wrap();

			echo '</div>';
		}
	}

	/**
	 * Print navigation.
	 *
	 * Prints images navigation for product gallery.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Fixed product image display if images are added to the gallery.
	 * @since 1.11.8 Fixed image output if only 1 image in gallery is selected.
	 */
	protected function render_anchor_nav( $attachment_ids ) {
		echo '<ul class="elementor-widget-cmsmasters-woo-product-images__nav-wrap">';

		array_unshift( $attachment_ids, get_post_thumbnail_id() );

		foreach ( $attachment_ids as $attachment_id ) {
			printf(
				'<li class="elementor-widget-cmsmasters-woo-product-images__nav-item"><a href="#%s" aria-label="Product image navigation bullet" data-index="%s"><span class="controller-item__bullet"></span></a></li>',
				$attachment_id,
				$attachment_id
			);
		}

		echo '</ul>';
	}
}
