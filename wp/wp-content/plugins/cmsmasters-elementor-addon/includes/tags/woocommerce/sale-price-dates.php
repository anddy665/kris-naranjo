<?php
namespace CmsmastersElementor\Tags\Woocommerce;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Woocommerce\Traits\Woo_Group;
use Elementor\Core\DynamicTags\Tag;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters sale price dates.
 *
 * Retrieves the whether or not the product is on sale price dates.
 *
 * @since 1.9.2
 */
class Sale_Price_Dates extends Tag {

	use Base_Tag, Woo_Group;

	/**
	* Get tag name.
	*
	* Returns the name of the dynamic tag.
	*
	* @since 1.9.2
	*
	* @return string Tag name.
	*/
	public static function tag_name() {
		return 'product-sale-price-dates';
	}

	/**
	* Get tag title.
	*
	* Returns the title of the dynamic tag.
	*
	* @since 1.9.2
	*
	* @return string Tag title.
	*/
	public static function tag_title() {
		return __( 'Product Sale Price Dates', 'cmsmasters-elementor' );
	}

	/**
	* Register advanced section controls.
	*
	* Registers the advanced section controls of the dynamic tag.
	*
	* Keep Empty to avoid default dynamic tag advanced section.
	*
	* @since 1.9.2
	*/
	protected function register_advanced_section() {}

	/**
	* Register controls.
	*
	* Registers the controls of the dynamic tag.
	*
	* @since 1.9.2
	*
	* @return void Tag controls.
	*/
	protected function register_controls() {
		$this->add_control(
			'posts_in',
			array(
				'label' => __( 'Search & select entries to show.', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => CmsmastersControls::QUERY,
				'options' => array(),
				'autocomplete' => array(
					'object' => Query_Manager::POST_OBJECT,
					'query' => array(
						'post_type' => array( 'product' ),
					),
					'display' => 'detailed',
				),
				'export' => false,
			)
		);

		$this->add_control(
			'posts_dates',
			array(
				'label' => __( 'Dates', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'start' => __( 'Start', 'cmsmasters-elementor' ),
					'end' => __( 'End', 'cmsmasters-elementor' ),
				),
				'label_block' => false,
				'toggle' => false,
				'default' => 'end',
				'condition' => array( 'posts_in!' => '' ),
			)
		);
	}

	/**
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.9.2
	*
	* @return void Tag render result.
	*/
	public function render() {
		$settings = $this->get_settings();

		$post_id = ( isset( $settings['posts_in'] ) && '' !== $settings['posts_in'] ? $settings['posts_in'] : get_the_ID() );

		$product_data = wc_get_product( $post_id );

		if ( ! $product_data ) {
			return;
		}

		$sale_start_date = $product_data->get_date_on_sale_from( 'edit' );
		$sale_end_date = $product_data->get_date_on_sale_to( 'edit' );

		$select_date = ( isset( $settings['posts_dates'] ) && 'start' === $settings['posts_dates'] ? $sale_start_date : $sale_end_date );

		if ( $select_date ) {
			$timestamp = strtotime( $select_date );
			$gmt_offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
			$formatted_date = gmdate( 'Y-m-d H:i', $timestamp + $gmt_offset );

			echo wp_kses_post( $formatted_date );
		}
	}

}
