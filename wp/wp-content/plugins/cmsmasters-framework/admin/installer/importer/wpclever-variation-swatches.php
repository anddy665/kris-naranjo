<?php
namespace CmsmastersFramework\Admin\Installer\Importer;

use CmsmastersFramework\Admin\Installer\Importer\WPClever_Importer_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPClever_Variation_Swatches handler class is responsible for different methods on importing "WPClever Variation Swatches" plugin.
 *
 * @since 1.0.0
 */
class WPClever_Variation_Swatches extends WPClever_Importer_Base {

	/**
	 * Module data.
	 *
	 * @since 1.0.0
	 */
	const MODULE_NAME = 'variation-swatches';
	const MODULE_OPTION_NAME = 'wpcvs_settings';

	/**
	 * Activation status.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Activation status.
	 */
	public static function activation_status() {
		return class_exists( 'WPCleverWpcvs' );
	}

}
