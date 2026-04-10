<?php
namespace CmsmastersElementor\Tags\Woocommerce;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Woocommerce\Traits\Woo_Group;
use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters short description.
 *
 * Retrieves the brief product description.
 *
 * @since 1.0.0
 */
class Description extends Tag {

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
		return 'product-description';
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
		return __( 'Product Description', 'cmsmasters-elementor' );
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
		$product_data = wc_get_product();

		if ( ! $product_data ) {
			return;
		}

		echo $product_data->get_description();
	}

}
