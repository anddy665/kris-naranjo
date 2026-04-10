<?php
namespace PediatricClinicSpace\Kits\Settings\HeaderTop;

use PediatricClinicSpace\Kits\Settings\Base\Base_Section;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Header Top section.
 */
class Section extends Base_Section {

	/**
	 * Get name.
	 *
	 * Retrieve the section name.
	 */
	public static function get_name() {
		return 'header-top';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the section title.
	 */
	public static function get_title() {
		return esc_html__( 'Header Top', 'pediatric-clinic' );
	}

	/**
	 * Get icon.
	 *
	 * Retrieve the section icon.
	 */
	public static function get_icon() {
		return 'eicon-header';
	}

	/**
	 * Get toggles.
	 *
	 * Retrieve the section toggles.
	 */
	public static function get_toggles() {
		return array(
			'header-top',
			'toggle',
			'info',
			'html',
			'social',
			'nav-title-item',
			'nav-dropdown-container',
			'nav-dropdown-item',
			'nav-burger-button',
			'nav-burger-container',
			'nav-burger-title-item',
			'nav-burger-dropdown-item',
		);
	}

}
