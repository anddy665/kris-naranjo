<?php
namespace CmsmastersFramework\Admin\Installer\Merlin\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_Customize_Setting' ) ) {
	require_once ABSPATH . 'wp-includes/class-wp-customize-setting.php';
}

/**
 * A class that extends WP_Customize_Setting so we can access
 * the protected updated method when importing options.
 *
 * Used in the Customizer importer.
 *
 * @since 1.0.0
 */
final class Merlin_Customizer_Option extends \WP_Customize_Setting {
	/**
	 * Import an option value for this setting.
	 *
	 * @since 1.0.0
	 * @param mixed $value The option value.
	 * @return void
	 */
	public function import( $value ) {
		$this->update( $value );
	}
}
