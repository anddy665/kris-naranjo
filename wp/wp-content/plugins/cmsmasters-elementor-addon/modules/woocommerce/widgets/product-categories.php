<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Widget;
use CmsmastersElementor\Modules\Woocommerce\Widgets\Base_Widgets\Base_Product_Categories;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Categories extends Base_Product_Categories {

	use Woo_Widget;

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
		return esc_html__( 'Product Categories', 'cmsmasters-elementor' );
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
		return 'cmsicon-product-categories-grid';
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

	protected function register_controls() {
		parent::register_controls();

		$this->injection_product_categories_controls_content();

		$this->injection_product_categories_controls_style();
	}

	/**
	 * Injection product category content controls.
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

		$this->add_responsive_control(
			'product_categories_columns',
			array(
				'label' => esc_html__( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 12,
				'default' => 4,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'required' => true,
				'device_args' => $this->get_devices_default_args(),
				'min_affected_device' => array(
					Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
					Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-product-category-columns: {{VALUE}};',
				),
			)
		);

		$this->end_injection();
	}

	/**
	 * Injection product category style controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.11.0
	 */
	protected function injection_product_categories_controls_style() {
		$this->start_injection(
			array(
				'of' => 'product_categories_section_style',
				'at' => 'after',
			)
		);

		$this->add_control(
			'product_categories_column_gap',
			array(
				'label' => esc_html__( 'Columns Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-product-categories-gap-column: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'product_categories_row_gap',
			array(
				'label' => esc_html__( 'Rows Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-product-categories-gap-row: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_injection();
	}

	protected function render_items() {
		$settings = $this->get_settings();

		$product_categories_columns = ( isset( $settings['product_categories_columns'] ) ? $settings['product_categories_columns'] : 1 );

		if ( parent::get_product_categories() ) {
			echo '<div class="cmsmasters-woo-product-categories columns-' . esc_attr( $product_categories_columns ) . '">';

			foreach ( parent::get_product_categories() as $category ) {
				parent::get_product_category( $category );
			}

			echo '</div>';
		}
	}

	public function render() {
		parent::render();

		$this->render_items();
	}

	public function render_plain_content() {
		echo wp_kses_post( $this->render_items() );
	}
}
