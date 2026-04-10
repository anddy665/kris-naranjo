<?php
namespace CmsmastersElementor\Modules\Woocommerce\Documents;

use CmsmastersElementor\Modules\TemplateLocations\Module as LocationsModule;
use CmsmastersElementor\Modules\TemplatePages\Documents\Singular;
use CmsmastersElementor\Modules\Woocommerce\Module as WooModule;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Document;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Product_Singular extends Singular {

	use Woo_Document;

	/**
	 * @since 1.0.0
	 * @since 1.19.3 Added `Add To Cart` widget display in the Product Singular template.
	 */
	public static $widgets_visibility = array(
		'Product_Image' => false,
		'Product_Content' => true,
		'Product_Add_To_Cart' => true,
	);

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @since 1.0.0
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['location_include'] = array( 'product' );
		$properties['locations_category'] = 'child';

		$properties = apply_filters( 'cmsmasters_elementor/documents/woo_singular/get_properties', $properties );

		return $properties;
	}

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Document name.
	 */
	public function get_name() {
		return 'cmsmasters_product_singular';
	}

	/**
	 * Get document title.
	 *
	 * Retrieve the document title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Document title.
	 */
	public static function get_title() {
		return __( 'Product', 'cmsmasters-elementor' );
	}

	/**
	 * Get editor panel categories.
	 *
	 * Retrieve the list of categories the element belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Editor panel categories.
	 */
	protected static function get_editor_panel_categories() {
		$categories = array(
			self::WOO_SINGULAR_WIDGETS_CATEGORY => array( 'title' => self::get_title() ),
			self::WOO_WIDGETS_CATEGORY => array(
				'title' => __( 'WooCommerce', 'cmsmasters-elementor' ),
				'active' => true,
			),
		);

		if ( Utils::is_pro() ) {
			$categories['woocommerce-elements-single'] = array(
				'title' => __( 'Product', 'cmsmasters-elementor' ),
				'active' => true,
			);

			$categories['woocommerce-elements'] = array(
				'title' => __( 'WooCommerce', 'cmsmasters-elementor' ),
				'active' => true,
			);
		}

		$categories += parent::get_editor_panel_categories();

		unset( $categories[ self::SINGULAR_WIDGETS_CATEGORY ] );

		if ( Utils::is_pro() ) {
			unset( $categories['theme-elements-single'] );
		}

		return $categories;
	}

	/**
	 * Get container attributes.
	 *
	 * Retrieve the document container attributes.
	 *
	 * @since 1.0.0
	 *
	 * @return string Container attributes.
	 */
	public function get_container_attributes() {
		$attributes = parent::get_container_attributes();

		if ( is_singular() ) {
			$attributes['class'] .= ' product';
		}

		return $attributes;
	}

	// protected function get_remote_library_config() {
	// 	$config = parent::get_remote_library_config();

	// 	$config['category'] = 'single product';

	// 	return $config;
	// }

	protected function register_controls() {
		parent::register_controls();

		$this->update_control(
			'preview_type',
			array( 'type' => Controls_Manager::HIDDEN )
		);
	}

	/**
	 * Before document content.
	 *
	 * Runs before document content render.
	 *
	 * @since 1.0.0
	 */
	public function before_get_content() {
		parent::before_get_content();

		global $product;

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( get_the_ID() );
		}

		do_action( 'woocommerce_before_single_product' );
	}

	/**
	 * After document content.
	 *
	 * Runs after document content render.
	 *
	 * @since 1.0.0
	 */
	public function after_get_content() {
		do_action( 'woocommerce_after_single_product' );

		parent::after_get_content();
	}

	protected function set_default_preview() {
		if ( ! empty( $this->preview_type_default ) ) {
			return;
		}

		$latest_post = get_posts( array(
			'post_type' => WooModule::$post_type,
			'fields' => 'ids',
			'numberposts' => 1,
		) );

		if ( empty( $latest_post ) ) {
			return;
		}

		$this->preview_type_default = sprintf( 'singular/%s', WooModule::$post_type );
		$this->preview_id_default = $latest_post[0];
	}

	public function get_locations_default() {
		$default_locations = parent::get_locations_default();

		if ( ! empty( $default_locations ) ) {
			return $default_locations;
		}

		/** @var LocationsModule $locations_module */
		$locations_module = LocationsModule::instance();
		$rules_manager = $locations_module->get_rules_manager();

		if ( $rules_manager->get_rule_instance( WooModule::$post_type ) ) {
			$default_locations[] = array(
				'stmt' => 'include',
				'main' => 'singular',
				'addl' => WooModule::$post_type,
			);
		}

		return $default_locations;
	}

}
