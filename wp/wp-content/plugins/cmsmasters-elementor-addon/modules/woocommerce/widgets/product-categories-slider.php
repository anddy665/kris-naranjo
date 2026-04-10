<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Widget;
use CmsmastersElementor\Modules\Woocommerce\Widgets\Base_Widgets\Base_Product_Categories;
use CmsmastersElementor\Modules\Slider\Classes\Slider;

use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Categories_Slider extends Base_Product_Categories {

	use Woo_Widget;

	/**
	 * Slider instance.
	 *
	 * @since 1.11.0
	 *
	 * @var Slider
	 */
	protected $slider;

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.11.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Product Categories Slider', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.11.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-product-categories-slider';
	}

	/**
	 * Get unique widget keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.11.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'woocommerce',
			'shop',
			'store',
			'categories',
			'product',
			'carousel',
			'slider',
		);
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
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 *
	 * Initializing the product category slider widget class.
	 *
	 * @since 1.11.0
	 *
	 * @throws \Exception If arguments are missing when initializing a
	 * full widget instance.
	 *
	 * @param array $data Widget data.
	 * @param array|null $args Widget default arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		$this->slider = new Slider( $this );

		parent::__construct( $data, $args );
	}

	protected function register_controls() {
		parent::register_product_categories_controls_content();

		$this->injection_product_categories_controls_content();

		parent::register_product_categories_query_controls_content();

		$this->slider->register_section_content();

		$this->slider->register_section_style_style_layout();

		parent::register_product_categories_controls_style();

		parent::register_product_categories_image_controls_style();

		parent::register_product_categories_title_controls_style();

		$this->slider->register_section_style_arrows();

		$this->slider->register_section_style_bullets();

		$this->slider->register_section_style_fraction();

		$this->slider->register_section_style_progressbar();

		$this->slider->register_section_style_scrollbar();
	}

	/**
	 * Injection product category slider content controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.11.0
	 */
	protected function injection_product_categories_controls_content() {
		$this->start_injection(
			array(
				'of' => 'product_categories_number',
				'at' => 'after',
			)
		);

		$this->slider->register_controls_content_per_view();

		$this->end_injection();
	}

	protected function render_items() {
		if ( parent::get_product_categories() ) {
			foreach ( parent::get_product_categories() as $category ) {
				$this->slider->render_slide_open();

				parent::get_product_category( $category );

				$this->slider->render_slide_close();
			}
		}
	}

	public function render() {
		parent::render();

		echo '<div class="cmsmasters-woo-product-categories">';

			$this->slider->render( function () {
				$this->render_items();
			} );

		echo '</div>';
	}

	public function render_plain_content() {
		echo wp_kses_post( $this->render_items() );
	}
}
