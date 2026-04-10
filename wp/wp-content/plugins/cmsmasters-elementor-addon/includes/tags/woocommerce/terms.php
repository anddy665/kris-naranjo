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
 * CMSMasters terms.
 *
 * Retrieve a product terms as a list.
 *
 * @since 1.0.0
 */
class Terms extends Tag {

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
	public function get_name() {
		return 'product-terms';
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
		return __( 'Product Terms', 'cmsmasters-elementor' );
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
		$fields = array();
		$filter_args = array(
			'show_in_nav_menus' => true,
			'object_type' => array( 'product' ),
		);

		$taxonomies = get_taxonomies( $filter_args, 'objects' );

		foreach ( $taxonomies as $taxonomy => $value ) {
			$fields[ $taxonomy ] = $value->label;
		}

		$this->add_control(
			'taxonomy',
			array(
				'label' => __( 'Taxonomy', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $fields,
				'default' => 'product_cat',
			)
		);

		$this->add_control(
			'separator',
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => ', ',
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
			return;
		}

		$settings = $this->get_settings();

		$terms = get_the_term_list( get_the_ID(), $settings['taxonomy'], '', $settings['separator'] );

		echo $terms;
	}

}
