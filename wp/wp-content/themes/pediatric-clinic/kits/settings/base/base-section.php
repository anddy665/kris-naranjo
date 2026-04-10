<?php
namespace PediatricClinicSpace\Kits\Settings\Base;

use PediatricClinicSpace\Kits\Module as KitsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Section base class.
 *
 * Adds settings section.
 */
class Base_Section {

	/**
	 * Get ID.
	 *
	 * Retrieve the section ID.
	 */
	public static function get_id() {
		return sprintf( '%1$s-%2$s', static::get_group(), static::get_name() );
	}

	/**
	 * Get name.
	 *
	 * Retrieve the section name.
	 */
	public static function get_name() {
		return '';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the section title.
	 */
	public static function get_title() {
		return '';
	}

	/**
	 * Get group.
	 *
	 * Retrieve the section group.
	 */
	public static function get_group() {
		return 'cmsmasters-theme';
	}

	/**
	 * Get icon.
	 *
	 * Retrieve the section icon.
	 */
	public static function get_icon() {
		return 'cmsms-logo';
	}

	/**
	 * Get help URL.
	 *
	 * Retrieve the section help URL.
	 */
	public static function get_help_url() {
		$slug = self::get_id();

		return esc_url( "https://go.cmsmasters.net/{$slug}" );
	}

	/**
	 * Get additional content.
	 *
	 * Retrieve the section additional content.
	 */
	public function get_additional_content() {
		return '';
	}

	/**
	 * Get toggles.
	 *
	 * Retrieve the section toggles.
	 */
	public static function get_toggles() {
		return array();
	}

	/**
	 * Get kit namespace.
	 *
	 * @return string Kit namespace.
	 */
	public function get_kit_namespace() {
		return KitsModule::KIT_NAMESPACE;
	}

	/**
	 * Init toggles.
	 *
	 * Add section toggles.
	 */
	public function get_toggles_config() {
		$toggles = array();

		foreach ( static::get_toggles() as $toggle_name ) {
			$name = ucwords( str_replace( '-', '_', $toggle_name ), '_' );
			$group = str_replace( '-', '', ucwords( static::get_name(), '-' ) );

			$instance = $this->get_kit_namespace() . "\\Settings\\{$group}\\{$name}";

			if ( ! class_exists( $instance ) ) {
				continue;
			}

			$toggles[ $toggle_name ] = $instance;
		}

		return $toggles;
	}

}
