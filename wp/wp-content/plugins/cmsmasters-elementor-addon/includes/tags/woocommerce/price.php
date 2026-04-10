<?php
namespace CmsmastersElementor\Tags\Woocommerce;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Woocommerce\Traits\Woo_Group;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * CMSMasters price.
 *
 * Retrieves the price of a product.
 *
 * @since 1.0.0
 */
class Price extends Tag {

	use Base_Tag, Woo_Group;

	/**
	* Get tag name.
	*
	* Returns the name of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag name.
	*/
	public static function tag_name() {
		return 'product-price';
	}

	/**
	* Get tag title.
	*
	* Returns the title of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag title.
	*/
	public static function tag_title() {
		return __( 'Product Price', 'cmsmasters-elementor' );
	}

	/**
	* Register controls.
	*
	* Registers the controls of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return void Tag controls.
	*/
	protected function register_controls() {
		$this->add_control(
			'format',
			array(
				'label' => __( 'Format', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'both' => __( 'Both', 'cmsmasters-elementor' ),
					'original' => __( 'Original', 'cmsmasters-elementor' ),
					'sale' => __( 'Sale', 'cmsmasters-elementor' ),
				),
				'default' => 'original',
			)
		);
	}

	/**
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return void Tag render result.
	*/
	public function render() {
		$product_date = wc_get_product();

		if ( ! $product_date ) {
			return;
		}

		$format = $this->get_settings( 'format' );
		$price = '';

		if ( 'both' === $format ) {
			$price = $product_date->get_price_html();
		} elseif ( 'original' === $format ) {
			$price = wc_price( $product_date->get_regular_price() ) . $product_date->get_price_suffix();
		} elseif ( 'sale' === $format && $product_date->is_on_sale() ) {
			$price = wc_price( $product_date->get_sale_price() ) . $product_date->get_price_suffix();
		}

		echo $price;
	}

}
