<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets\Skins;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Slider\Classes\Slider;
use CmsmastersElementor\Utils as Breakpoints_Manager;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Widget_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Product_Images_Slider extends Product_Images_Base {

	protected $slider;

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
		return 'slider';
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
		return esc_html__( 'Slider', 'cmsmasters-elementor' );
	}

	/**
	 * Get scripts dependencies.
	 *
	 * Retrieve the list of scripts dependencies the widget requires.
	 *
	 * @since 1.16.0 Added dependency of connecting swiper script after elementor 3.27 version.
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'swiper' );
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.15.3 Added dependency of connecting swiper styles for widgets with swiper slider after elementor 3.26 version.
	 * @since 1.16.0 Fixed style dependencies.
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array_merge( array(
			'e-swiper',
		), parent::get_style_depends() );
	}

	/**
	 *
	 * Initializing the Addon `media carousel` widget class.
	 *
	 * @since 1.0.0
	 *
	 * @throws \Exception If arguments are missing when initializing a
	 * full widget instance.
	 *
	 * @param array $data Widget data.
	 * @param array|null $args Widget default arguments.
	 */
	public function __construct( Widget_Base $parent ) {
		parent::__construct( $parent );

		$this->slider = new Slider( $this );
	}

	/**
	 * Register skin controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Fixed error with responsive controls in elementor 3.4.0, Fixed 'width' control,
	 * fixed styles for product image.
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		parent::register_controls( $widget );

		$this->remove_control( 'zoom_position' );

		$this->remove_control( 'zoom_position_alt' );

		$this->register_controls_slider();

		$this->register_controls_thumbs();

		$this->register_thumb_controls_styles();
	}

	public function register_controls_slider() {
		$this->parent->start_injection( array(
			'of' => $this->get_id() . '_link_type',
		) );

		$this->slider->register_controls_content_per_view();

		$this->parent->end_injection();

		$this->parent->start_injection( array(
			'of' => '_skin',
		) );

		$slider_type_attr = $this->get_controls_attr( 'slider_type' );

		$this->add_control(
			'slider_type',
			$slider_type_attr
		);

		$slider_per_view_attr = $this->get_controls_attr( 'slider_per_view' );

		$this->add_control(
			'slider_per_view',
			$slider_per_view_attr
		);

		$this->update_responsive_control(
			'slider_per_view',
			array(
				'default' => '1',
				'tablet_default' => '1',
			)
		);

		$this->parent->end_injection();

		$this->slider->register_section_content();
		$this->slider->register_sections_style();

		$this->parent->update_control( 'slider_slider_navigation_heading', array(
			'type' => Controls_Manager::HIDDEN,
			'default' => '',
		) );

		$this->parent->update_control( 'slider_slider_navigation', array(
			'type' => Controls_Manager::HIDDEN,
			'default' => '',
		) );
	}

	/**
	 * Register thumbs controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.11.8 Added `Size` control and `Number per Line` control for Contain Size.
	 * Added `Hide On` control for Thumbnails.
	 */
	public function register_controls_thumbs() {
		$this->start_controls_section(
			'section_thumbs',
			array(
				'label' => esc_html__( 'Thumbnails', 'cmsmasters-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'thumb_size',
				'default' => 'thumb',
			)
		);

		$this->add_control(
			'thumb_position',
			array(
				'label' => esc_html__( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'toggle' => false,
				'default' => 'bottom',
				'prefix_class' => 'cmsmasters-thumbs-position-',
			)
		);

		$this->add_control(
			'thumb_layout',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'column' => __( 'Cover', 'cmsmasters-elementor' ),
					'self-size' => __( 'Contain', 'cmsmasters-elementor' ),
				),
				'default' => 'column',
				'label_block' => false,
			)
		);

		$this->add_responsive_control(
			'thumb_columns',
			array(
				'label' => esc_html__( 'Number per Line', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 4,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'min' => 2,
				'max' => 10,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-cols%s-',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-images__row-wrap' => '--col-num: {{VALUE}};',
				),
				'condition' => array( 'slider_thumb_layout!' => 'self-size' ),
			)
		);

		$this->add_responsive_control(
			'thumb_self_size_columns',
			array(
				'label' => esc_html__( 'Number per Line', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 4,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'min' => 2,
				'max' => 10,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-cols%s-',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-images__row-wrap' => '--col-num: {{VALUE}};',
				),
				'condition' => array( 'slider_thumb_layout' => 'self-size' ),
			)
		);

		$this->add_responsive_control(
			'thumb_wrap_width',
			array(
				'label' => __( 'Wrapper Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 50,
						'max' => 500,
					),
					'%' => array(
						'min' => 5,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--thumb-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'slider_thumb_position!' => 'bottom' ),
			)
		);

		$this->add_responsive_control(
			'thumb_gap_between',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-images__thumb-wrap' => '--thumb-margin: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'thumb_gap',
			array(
				'label' => __( 'Thumbnail Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'%' => array(
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-images__row-wrap' => '--col-margin: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$breakpoints = Breakpoints_Manager::get_breakpoints();

		$this->add_control(
			'thumb_hide_on',
			array(
				'label' => __( 'Hide On', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					/* translators: Tablet breakpoint %d: number in pixels. */
					'tablet' => sprintf( __( 'Tablet (> %dpx)', 'cmsmasters-elementor' ), $breakpoints['tablet'] ),
					/* translators: Mobile breakpoint %d: number in pixels. */
					'mobile' => sprintf( __( 'Mobile (< %dpx)', 'cmsmasters-elementor' ), $breakpoints['mobile'] ),
				),
				'default' => 'none',
				'description' => 'Hide on resolutions below chosen',
				'prefix_class' => 'cmsmasters_thumb_hide_on_',
				'render_type' => 'template',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register images controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the images settings.
	 *
	 * @since 1.0.0
	 * @since 1.6.3 Fixed product image with border radius.
	 */
	public function register_thumb_controls_styles() {
		$images = '{{WRAPPER}} .cmsmasters-slider-wrap .cmsmasters-slider, {{WRAPPER}} .elementor-widget-cmsmasters-woo-product-images__thumb-wrap img';

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
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => $images,
			)
		);

		$this->add_control(
			'images_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					$images . ', {{WRAPPER}} .cmsmasters-slider-wrap .cmsmasters-slider img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'images_shadow',
				'selector' => $images,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Retrieves row array from control.
	 *
	 * @since 1.0.0
	 *
	 * @return array Retrieves row array from control.
	 */
	public function get_controls_attr( $control_name ) {
		$control_attr = $this->parent->get_controls( 'slider_' . $control_name );

		unset( $control_attr['section'] );
		unset( $control_attr['tab'] );
		unset( $control_attr['name'] );

		$this->parent->remove_control( 'slider_' . $control_name );

		return $control_attr;
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
			$this->render_slides( $settings, $product );

			$this->render_thumb_wrap( $settings, $product );
		} else {
			printf(
				'<div class="cmsmasters-elementor__content">%s</div>',
				esc_html__( 'Not found product with current id', 'cmsmasters-elementor' )
			);
		}
	}

	/**
	 * Render Slides.
	 *
	 * Retrieve media carousel widget slides.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Fixed product image display if images are added to the gallery.
	 * @since 1.11.8 Fixed image output if only 1 image in gallery is selected.
	 * @access public
	 *
	 */
	public function render_slides( $settings, $product ) {
		$this->slider->set_widget( $this->parent );
		$attachment_ids = $product->get_gallery_image_ids();
		$has_gallery = ( 1 <= count( $attachment_ids ) );

		echo '<div class="cmsmasters-slider-wrap' . ( $has_gallery ? '' : ' only_featured' ) . '">';

		$this->slider->render( function () use ( $settings, $product, $attachment_ids, $has_gallery ) {
			empty( $attachment_ids ) ? $attachment_ids[0] = $product->get_image_id() : '';

			if ( $attachment_ids && $product->get_image_id() ) {
				$thumbnail_image_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

				if ( $thumbnail_image_src && $has_gallery ) {
					array_unshift( $attachment_ids, get_post_thumbnail_id() );
				}

				foreach ( $attachment_ids as $attachment_id ) {
					$this->slider->render_slide_open();

					$this->render_slide( $settings, $attachment_id );

					$this->slider->render_slide_close();
				}
			}
		} );

		echo '</div>';
	}

	/**
	 * Print slide for slider.
	 *
	 * Retrieves slide html of product gallery image.
	 *
	 * @since 1.0.0
	 */
	protected function render_slide( $settings, $attachment_id ) {
		$image_src = wp_get_attachment_image_src( $attachment_id, 'full' );

		$this->parent->add_render_attribute( 'lightbox_' . $attachment_id, array(
			'href' => $image_src[0],
			'data-elementor-open-lightbox' => 'yes',
			'data-elementor-lightbox-slideshow' => $this->get_id(),
		) );

		echo '<div class="elementor-widget-cmsmasters-woo-product-images__zoom images">';

		$this->render_image( $settings, $attachment_id, $image_src, 'image_size' );

		echo '</div>';
	}

	/**
	 * Print thumb wrapper.
	 *
	 * Retrieves thumb image with wrapper.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Fixed unclosed div in multiple thumbnails rows.
	 * Fixed product image display if images are added to the gallery.
	 * @since 1.11.8 Fixed image output if only 1 image in gallery is selected.
	 */
	public function render_thumb_wrap( $settings, $product ) {
		$attachment_ids = $product->get_gallery_image_ids();
		$has_gallery = ( 1 <= count( $attachment_ids ) );

		if ( $attachment_ids && $product->get_image_id() && $has_gallery ) {
			$layout = ( isset( $settings['slider_thumb_layout'] ) ? $settings['slider_thumb_layout'] : '' );
			$column_layout = ( 'self-size' === $layout ? ' self_size' : ' thumb_columns' );
			$thumb_count = 0;

			echo '<div class="elementor-widget-cmsmasters-woo-product-images__thumb-wrap' . esc_attr( $column_layout ) . '">';

			$thumbnail_image_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

			if ( $thumbnail_image_src ) {
				array_unshift( $attachment_ids, get_post_thumbnail_id() );
			}

			$columns = ( isset( $settings['slider_thumb_columns'] ) && ! empty( $settings['slider_thumb_columns'] ) ? $settings['slider_thumb_columns'] : 6 );
			$self_size_columns = ( isset( $settings['slider_thumb_self_size_columns'] ) && ! empty( $settings['slider_thumb_self_size_columns'] ) ? $settings['slider_thumb_self_size_columns'] : 6 );

			foreach ( $attachment_ids as $attachment_id ) {
				if ( 0 === ( $thumb_count % ( 'self-size' === $layout ? $self_size_columns : $columns ) ) ) {
					echo '<div class="elementor-widget-cmsmasters-woo-product-images__row-wrap">';
				}

				$this->render_thumb_images( $settings, $attachment_id, $thumb_count );

				$thumb_count++;

				if (
					( 0 === ( $thumb_count % ( 'self-size' === $layout ? $self_size_columns : $columns ) ) ) ||
					count( $attachment_ids ) === $thumb_count
				) {
					echo '</div>';
				}
			}

			echo '</div>';
		}
	}

	/**
	 * Retrieves thumb image.
	 *
	 * @since 1.0.0
	 */
	protected function render_thumb_images( $settings, $attachment_id, $thumb_count ) {
		$image_src = wp_get_attachment_image_src( $attachment_id, 'full' );

		$layout = ( isset( $settings['slider_thumb_layout'] ) ? $settings['slider_thumb_layout'] : '' );
		$column_layout = ( 'self-size' === $layout ? ' self_size' : ' thumb_columns' );

		$this->parent->add_render_attribute( 'thumb_wrapper_' . $attachment_id, array(
			'class' => 'elementor-widget-cmsmasters-woo-product-images__wrapper-item' . $column_layout,
			'data-id' => esc_attr( $thumb_count ),
			'tabindex' => '0',
		) );

		echo '<div ' . $this->parent->get_render_attribute_string( 'thumb_wrapper_' . $attachment_id ) . '>';

		$this->render_image( $settings, $attachment_id, $image_src, 'thumb_size', 'thumbnail' );

		echo '</div>';
	}
}
