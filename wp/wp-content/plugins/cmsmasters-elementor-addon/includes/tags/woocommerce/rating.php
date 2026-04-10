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
 * CMSMasters rating.
 *
 * Retrieves the product rating.
 *
 * @since 1.0.0
 */
class Rating extends Tag {

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
		return 'product-rating';
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
		return __( 'Product Rating', 'cmsmasters-elementor' );
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
					'average_rating' => __( 'Average Rating', 'cmsmasters-elementor' ),
					'rating_count' => __( 'Rating Count', 'cmsmasters-elementor' ),
					'review_count' => __( 'Review Count', 'cmsmasters-elementor' ),
				),
				'default' => 'average_rating',
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
		$product_data = wc_get_product();

		if ( ! $product_data ) {
			return '';
		}

		$format = $this->get_settings( 'format' );
		$rating = '';

		if ( 'average_rating' === $format ) {
			$rating = $product_data->get_average_rating();
		} elseif ( 'rating_count' === $format ) {
			$rating = $product_data->get_rating_count();
		} elseif ( 'review_count' === $format ) {
			$rating = $product_data->get_review_count();
		}

		echo $rating;
	}

}
