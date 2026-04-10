<?php
namespace CmsmastersElementor\Base;

use Elementor\Core\Base\App;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Base App class.
 *
 * An abstract class to register new Addon applications.
 *
 * This class extends the `\Elementor\Core\Base\App` class to inherit
 * his properties and methods, and must be extended in order to
 * register new Elementor applications.
 *
 * @since 1.0.0
 */
abstract class Base_App extends App {

	/**
	 * Addon settings page id.
	 */
	const PAGE_ID = 'cmsmasters';

	/**
	 * Assets library source.
	 */
	const LIB_SRC = 'assets/lib/';

	/**
	 * Ensure editor settings.
	 *
	 * Ensures that the editor `$settings` member is initialized.
	 *
	 * @since 1.0.0
	 *
	 * @return array Editor settings.
	 */
	protected function get_init_settings() {
		$settings = array(
			'version' => CMSMASTERS_ELEMENTOR_VERSION,
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'urls' => array(
				'assets' => CMSMASTERS_ELEMENTOR_ASSETS_URL,
				'modules' => CMSMASTERS_ELEMENTOR_MODULES_URL,
			),
			'i18n' => array(),
		);

		/**
		 * App settings.
		 *
		 * Filters the application settings.
		 *
		 * @since 1.0.0
		 *
		 * @param array $settings App settings.
		 */
		$settings = apply_filters( 'cmsmasters_elementor/app/settings', $settings );

		return $settings;
	}

	/**
	 * Base App class constructor.
	 *
	 * Constructs the Addon Base App class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init_actions();
		$this->init_filters();
	}

	/**
	 * Init actions.
	 *
	 * Initialize app actions.
	 *
	 * @since 1.0.0
	 */
	protected function init_actions() {}

	/**
	 * Init filters.
	 *
	 * Initialize app filters.
	 *
	 * @since 1.0.0
	 */
	protected function init_filters() {}

	/**
	 * Get assets base url
	 *
	 * Retrieve the Addon assets directory base url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Assets base url.
	 */
	final protected function get_assets_base_url() {
		return CMSMASTERS_ELEMENTOR_URL;
	}

	/**
	 * Get assets base url
	 *
	 * Retrieve the Addon assets directory base url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Assets base url.
	 */
	final protected static function get_lib_src( $subfolder = '' ) {
		$lib_src = self::LIB_SRC;

		if ( ! empty( $subfolder ) ) {
			$lib_src .= "{$subfolder}/";
		}

		return $lib_src;
	}

}
