<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets\Skins;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Skin_Base as ElementorSkinBase;
use Elementor\Widget_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


abstract class Product_Images_Base extends ElementorSkinBase {

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
			'widget-cmsmasters-woocommerce',
		);
	}

	protected function _register_controls_actions() {
		add_action( 'elementor/element/cmsmasters-woo-product-images/section_content/after_section_end', array( $this, 'register_controls' ) );
	}

	/**
	 * Register skin controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.6.1 Fixed notice _skin.
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->parent->start_injection( array(
			'of' => '_skin',
		) );

		$this->register_base_general_controls();

		$this->parent->end_injection();

		$this->register_zoom_controls();

		$this->general_section_extend();

		$this->register_controls_styles();

		$this->lightbox_section_styles();
	}

	protected function register_base_general_controls() {
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'image_size', // Actually its `image_size`
				'default' => 'full',
			)
		);

		$this->add_control(
			'link_type',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'lightbox' => __( 'Lightbox', 'cmsmasters-elementor' ),
					'zoom' => __( 'Zoom', 'cmsmasters-elementor' ),
				),
				'default' => 'zoom',
				'render_type' => 'template',
				'frontend_available' => true,
				'label_block' => false,
			)
		);
	}

	public function register_zoom_controls() {
		$this->start_controls_section(
			'section_zoom_options',
			array(
				'label' => __( 'Zoom Options', 'cmsmasters-elementor' ),
				'condition' => array(
					$this->get_id() . '_link_type' => array( 'zoom' ),
				),
			)
		);

		$this->add_responsive_control(
			'zoom_position',
			array(
				'label' => esc_html__( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'inside' => array(
						'title' => __( 'Inside', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'toggle' => false,
				'default' => 'right',
				'tablet_default' => 'inside',
				'mobile_default' => 'inside',
				'prefix_class' => 'cmsmasters-zoom-position%s-',
			)
		);

		$this->add_control(
			'zoom_ratio',
			array(
				'label' => esc_html__( 'Zoom Ratio', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 1,
				'min' => 1,
				'max' => 2,
				'step' => 0.1,
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'zoom_gap',
			array(
				'label' => esc_html__( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-images__zoom-wrap' => '--zoom-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_id() . '_zoom_position!' => array( 'inside' ),
				),
			)
		);

		$this->end_controls_section();
	}

	public function general_section_extend() {}

	public function register_controls_styles() {}

	public function lightbox_section_styles() {
		$this->start_controls_section(
			'section_lightbox_style',
			array(
				'label' => __( 'Lightbox', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_id() . '_link_type' => array( 'lightbox' ),
				),
			)
		);

		$id = $this->get_id();

		$this->add_control(
			'lightbox_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'#elementor-lightbox-slideshow-' . $id => 'background-color: {{VALUE}};',
					'#elementor-lightbox-' . $id => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lightbox_ui_color',
			array(
				'label' => __( 'UI Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'#elementor-lightbox-slideshow-' . $id . ' .dialog-lightbox-close-button, #elementor-lightbox-slideshow-' . $id . ' .elementor-swiper-button' => 'color: {{VALUE}};',
					'#elementor-lightbox-' . $id . ' .dialog-lightbox-close-button, #elementor-lightbox-' . $id . ' .elementor-swiper-button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lightbox_ui_hover_color',
			array(
				'label' => __( 'UI Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'#elementor-lightbox-slideshow-' . $id . ' .dialog-lightbox-close-button:hover, #elementor-lightbox-slideshow-' . $id . ' .elementor-swiper-button:hover' => 'color: {{VALUE}};',
					'#elementor-lightbox-' . $id . ' .dialog-lightbox-close-button:hover, #elementor-lightbox-' . $id . ' .elementor-swiper-button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
 * Retrieves image from single product gallery.
 *
 * @since 1.0.0
 * @since 1.11.6 Fixed choose image size for product image thumbnail.
 *
 * @return string Image with certain parameters.
 */
public function render_image( $settings, $id, $full_src, $control_name = 'image_size', $thumbnail = '' ) {
	$filter_callback = function () use ( $control_name ) {
		return $this->get_widget_size_image( $control_name );
	};

	add_filter( 'woocommerce_gallery_image_size', $filter_callback );

	$this->replace_html( $settings, $id, $full_src, $thumbnail );

	remove_filter( 'woocommerce_gallery_image_size', $filter_callback );
}

	/**
	 * Replace html.
	 *
	 * @since 1.4.0
	 * @since 1.14.7 Fixed image thumbnail repeating in lightbox.
	 *
	 * @return string image.
	 */
	public function replace_html( $settings, $id, $full_src, $thumbnail ) {
		$str = wc_get_gallery_image_html( $id, true );
		$control_id = $this->get_id();

		$slider_skin = ( isset( $settings['_skin'] ) && 'slider' === $settings['_skin'] );

		if ( 'lightbox' === $settings[ $control_id . '_link_type' ] && ( ! $slider_skin || ( $slider_skin && 'thumbnail' !== $thumbnail ) ) ) {
			$data = "href='{$full_src[0]}' data-elementor-open-lightbox='yes' data-elementor-lightbox-slideshow='{$control_id}'";
		} else {
			$data = '';
		}

		$source = array(
			'href="' . esc_url( $full_src[0] ) . '"',
		);

		$replace = array(
			$data,
		);

		echo str_replace( $source, $replace, $str );
	}

	/**
	 * Get image custom size.
	 *
	 * @since 1.4.0
	 * @since 1.11.6 Fixed choose image size for product image thumbnail.
	 *
	 * @return array image size.
	 */
	public function get_widget_size_image( $control_name ) {
		$settings   = $this->parent->get_settings_for_display();
		$control_id = $this->get_id();
		$image_id   = get_post_thumbnail_id();

		if ( ! $image_id ) {
			return null;
		}

		$size_key = $control_id . '_' . $control_name . '_size';

		if ( ! isset( $settings[ $size_key ] ) ) {
			return null;
		}

		$image_size = $settings[ $size_key ];

		if ( 'custom' === $image_size ) {
			$custom_size_key = $control_id . '_' . $control_name . '_custom_dimension';

			if (
				isset( $settings[ $custom_size_key ]['width'] ) &&
				isset( $settings[ $custom_size_key ]['height'] )
			) {
				$width  = (int) $settings[ $custom_size_key ]['width'];
				$height = (int) $settings[ $custom_size_key ]['height'];

				return array( $width, $height );
			} else {
				return null;
			}
		}

		$image_src = wp_get_attachment_image_src( $image_id, $image_size );

		if ( ! is_array( $image_src ) || count( $image_src ) < 3 ) {
			return null;
		}

		$width  = $image_src[1];
		$height = $image_src[2];

		return array( $width, $height );
	}

	/**
	 * Print opening wrap for gallery.
	 *
	 * @since 1.0.0
	 */
	public function open_wrap() {
		$this->parent->add_render_attribute( 'main', array(
			'class' => array(
				'elementor-widget-cmsmasters-woo-product-images__wrapper',
				'cmsmasters-images-skin__' . esc_attr( $this->get_id() ),
				'images',
			),
		) );

		echo '<div ' . $this->parent->get_render_attribute_string( 'main' ) . '>';
	}

	/**
	 * Print closing wrap for gallery.
	 *
	 * @since 1.0.0
	 */
	public function close_wrap() {
		echo '</div>';
	}
}
