<?php
namespace CmsmastersElementor\Tags\Woocommerce;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Woocommerce\Traits\Woo_Group;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters category image.
 *
 * Retrieves the product category image.
 *
 * @since 1.0.0
 */
class Category_Image extends Data_Tag {

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
		return 'category-image';
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
		return __( 'Category Image', 'cmsmasters-elementor' );
	}

	/**
	* Get categories.
	*
	* Returns an array of dynamic tag categories.
	*
	* @since 1.0.0
	*
	* @return array Tag categories.
	*/
	public function get_categories() {
		return array( TagsModule::IMAGE_CATEGORY );
	}

	/**
	* Get value.
	*
	* Returns out the value of the dynamic data tag.
	*
	* @since 1.0.0
	*
	* @return array Tag value.
	*/
	public function get_value( array $options = array() ) {
		$cat_id = 0;

		if ( is_product() ) {
			$product_data = wc_get_product();

			if ( $product_data ) {
				$cat_ids = $product_data->get_category_ids();
				if ( ! empty( $cat_ids ) ) {
					$cat_id = $cat_ids[0];
				}
			}
		} elseif ( is_product_category() ) {
			$cat_id = get_queried_object()->ID;
		}

		if ( $cat_id ) {
			$image_id = get_term_meta( $cat_id, 'thumbnail_id', true );
		}

		if ( empty( $image_id ) ) {
			return array();
		}

		$src = wp_get_attachment_image_src( $image_id, 'full' );

		return array(
			'id' => $image_id,
			'url' => $src[0],
		);
	}

}
