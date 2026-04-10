<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets\Skins;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Images_Grid extends Product_Images_Base {

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
		return 'grid';
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
		return esc_html__( 'Grid', 'cmsmasters-elementor' );
	}

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-woo-product-images';
	}

	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
	}

	/**
	 * Register skin controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.2.3 Added `Product Image` control for product image. Added `Gap Between`, `Border`,
	 * `Border Radius`, `Box Shadow` controls for product gallery images.
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		parent::register_controls( $widget );

		$this->register_grid_controls_styles();
	}

	public function general_section_extend() {
		$widget_selector = $this->get_widget_selector();

		$this->parent->start_injection( array(
			'of' => '_skin',
		) );

		$this->add_control(
			'view_image',
			array(
				'label' => esc_html__( 'Product Image', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'block' => __( 'Featured', 'cmsmasters-elementor' ),
					'column' => __( 'In Column', 'cmsmasters-elementor' ),
				),
				'default' => 'block',
				'frontend_available' => true,
				'label_block' => false,
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label' => esc_html__( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 2,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'min' => 1,
				'max' => 6,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-cols%s-',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__wrapper' => '--col-num: {{VALUE}};',
				),
			)
		);

		$this->parent->end_injection();

		$this->parent->start_injection( array(
			'of' => $this->get_id() . '_link_type',
		) );

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
					'{{WRAPPER}} ' . $widget_selector . '__wrapper' => '--col-margin: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->parent->end_injection();
	}

	public function register_grid_controls_styles() {
		$widget_selector = $this->get_widget_selector();

		$this->start_controls_section(
			'section_product_images_style',
			array(
				'label' => esc_html__( 'Images', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'images_border',
				'label' => esc_html__( 'Border', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__wrapper-item',
			)
		);

		$this->add_control(
			'images_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__wrapper-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'images_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__wrapper-item',
			)
		);

		$this->add_control(
			'images_gallery_style',
			array(
				'label' => __( 'Gallery', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'images_gallery_gap_between',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__wrapper-item + ' . $widget_selector . '__row-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'images_gallery_border',
				'label' => esc_html__( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => _x( 'Default', 'Border Control', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => 'default',
					),
				),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__row-wrap ' . $widget_selector . '__wrapper-item',
			)
		);

		$this->add_control(
			'images_gallery_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__row-wrap ' . $widget_selector . '__wrapper-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'images_gallery_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__row-wrap ' . $widget_selector . '__wrapper-item',
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

		$settings = $this->parent->get_active_settings();

		if ( ! empty( $product ) ) {
			$this->render_grid_images( $settings, $product );
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
	 * @since 1.2.3 Fixed unclosed div in multiple rows. Fixed product image display in grid skin
	 * if images are added to the gallery.
	 * @since 1.3.1 Fixed unclosed div in multiple rows.
	 * @since 1.11.8 Fixed image output if only 1 image in gallery is selected.
	 */
	protected function render_grid_images( $settings, $product ) {
		$attachment_ids = $product->get_gallery_image_ids();
		$has_gallery = ( 1 <= count( $attachment_ids ) );

		empty( $attachment_ids ) ? $attachment_ids[0] = $product->get_image_id() : '';

		$thumb_count = 0;

		$this->open_wrap();

		$thumbnail_image_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
		$view_image = ( isset( $settings['grid_view_image'] ) ? $settings['grid_view_image'] : '' );

		if (
			$thumbnail_image_src &&
			(
				'block' === $view_image ||
				( 'column' === $view_image && 1 === count( $attachment_ids ) )
			)
		) {
			$this->open_grid_wrap( $settings, get_post_thumbnail_id(), $thumbnail_image_src );

			$this->render_image( $settings, get_post_thumbnail_id(), $thumbnail_image_src, 'thumb_size', 'thumbnail' );

			$this->close_grid_wrap( $settings );
		}

		if ( $attachment_ids && $product->get_image_id() && $has_gallery ) {
			if ( 'column' === $view_image ) {
				array_unshift( $attachment_ids, get_post_thumbnail_id() );
			}

			foreach ( $attachment_ids as $attachment_id ) {
				$gallery_image_src = wp_get_attachment_image_src( $attachment_id, 'full' );
				$grid_columns = $settings['grid_columns'];
				$count = $thumb_count % $grid_columns;

				if ( $gallery_image_src ) {
					if ( 0 === ( $count % $settings['grid_columns'] ) ) {
						echo '<div class="' . $this->get_widget_class() . '__row-wrap cmsmasters_gallery_column">';
					}

					$this->open_grid_wrap( $settings, $attachment_id, $gallery_image_src );

					$this->render_image( $settings, $attachment_id, $gallery_image_src, 'image_size' );

					$this->close_grid_wrap( $settings );

					$thumb_count++;

					if (
						0 === ( $thumb_count % $settings['grid_columns'] ) ||
						count( $attachment_ids ) === $thumb_count
					) {
						echo '</div>';
					}
				}
			}
		}

		$this->close_wrap();
	}

	protected function open_grid_wrap( $settings, $image_id, $image_src ) {
		$this->parent->add_render_attribute( 'image_wrapper_' . $image_id, array(
			'class' => '' . $this->get_widget_class() . '__wrapper-item',
		) );

		if ( 'zoom' === $settings['grid_link_type'] ) {
			$this->parent->add_render_attribute( 'image_wrapper_' . $image_id, array(
				'class' => '' . $this->get_widget_class() . '__zoom',
			) );
		}

		echo '<div ' . $this->parent->get_render_attribute_string( 'image_wrapper_' . $image_id ) . '>';
	}

	protected function close_grid_wrap( $settings ) {
		echo '</div>';
	}
}
