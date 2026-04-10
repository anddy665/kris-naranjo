<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Modules\MetaData\Widgets\Meta_Data;
use CmsmastersElementor\Modules\MetaData\Module as MetaDataModule;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Singular_Widget;

use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Addon product meta data.
 *
 * @since 1.0.0
 */
class Product_Meta extends Meta_Data {

	use Woo_Singular_Widget;

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Product Meta', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_icon() {
		return 'cmsicon-product-meta';
	}

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

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_default_meta_fields() {
		return array(
			array(
				'group' => 'standard',
				'group_type_standard' => 'sku',
				'text_before' => esc_html__( 'SKU', 'cmsmasters-elementor' ) . ': ',
			),
			array(
				'group' => 'taxonomy',
				'group_type_taxonomy' => 'product_cat',
				'text_before' => esc_html__( 'Category', 'cmsmasters-elementor' ) . ': ',
			),
		);
	}

	/**
	 * @since 1.1.0
	 */
	protected static function get_allowed_post_types() {
		return array( 'product' );
	}

	/**
	 * @since 1.0.0
	 */
	private static function get_count_additional() {
		return array(
			'review' => __( 'Review', 'cmsmasters-elementor' ),
			'rating' => __( 'Rating', 'cmsmasters-elementor' ),
			'total_sales' => __( 'Total Sales', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * @since 1.0.0
	 */
	protected static function get_standard_options() {
		$standard_options = parent::get_standard_options();

		if ( wc_product_sku_enabled() ) {
			$standard_options['sku'] = __( 'SKU', 'cmsmasters-elementor' );
		}

		return $standard_options;
	}

	/**
	 * @since 1.0.0
	 */
	protected static function get_count_options() {
		$options = parent::get_count_options() + static::get_count_additional();

		if ( isset( $options['comments'] ) ) {
			unset( $options['comments'] );
		}

		return $options;
	}

	/**
	 * @since 1.0.0
	 */
	protected function render_standard() {
		parent::render_standard();

		switch ( $this->get_group_type() ) {
			case 'sku':
				$this->render_sku();
		}
	}

	/**
	 * @since 1.0.0
	 */
	protected function check() {
		$is_checked = parent::check();

		if ( $is_checked ) {
			switch ( $this->get_group_type() ) {
				case 'sku':
					$is_checked = $this->is_render_sku();

					break;
			}
		}

		return $is_checked;
	}

	/**
	 * Check sku.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_render_sku() {
		global $product;

		return $product && wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) );
	}

	/**
	 * Render sku.
	 *
	 * @since 1.0.0
	 */
	protected function render_sku() {
		if ( ! $this->is_render_sku() ) {
			return;
		}

		MetaDataModule::render_postmeta(
			'sku',
			function () {
				global $product;

				$sku = $product->get_sku();

				if ( ! $sku ) {
					$sku = esc_html__( 'N/A', 'cmsmasters-elementor' );
				}

				echo '<span class="sku_wrapper">' .
					'<span class="sku">' .
						esc_html( $sku ) .
					'</span>' .
				'</span>';
			},
			$this->get_postmeta_args()
		);
	}

	/**
	 * @since 1.0.0
	 */
	protected function render_count() {
		parent::render_count();

		$count_additional = static::get_count_additional();

		if ( ! empty( $count_additional[ $this->get_group_type() ] ) ) {
			$this->render_count_woo();
		}
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_postmeta_args() {
		$postmeta_args = parent::get_postmeta_args();
		$product = wc_get_product();

		if ( $product ) {
			switch ( $this->get_group_type() ) {
				case 'review':
					$postmeta_args['icon_default'] = array(
						'value' => 'fas fa-comment-dollar',
						'library' => 'solid',
					);

					break;
				case 'rating':
				case 'total_sales':
					$postmeta_args['icon_default'] = array(
						'value' => 'far fa-star',
						'library' => 'regular',
					);
			}
		}

		return $postmeta_args;
	}

	/**
	 * Render elements of count group.
	 *
	 * @since 1.0.0
	 */
	protected function render_count_woo() {
		$product = wc_get_product();

		if ( ! $product ) {
			return;
		}

		$group_type = $this->get_group_type();
		$args = array(
			'type' => $group_type,
		);

		switch ( $group_type ) {
			case 'review':
				$args['href'] = "{$product->get_permalink()}#tab-reviews";
				$args['count'] = $product->get_review_count();

				break;
			case 'rating':
				$args['count'] = $product->get_average_rating();

				break;
			case 'total_sales':
				$args['count'] = $product->get_total_sales();

				break;
		}

		MetaDataModule::render_count( $args, $this->get_postmeta_args() );
	}
}
