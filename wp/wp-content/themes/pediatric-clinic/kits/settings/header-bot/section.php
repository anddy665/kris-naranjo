<?php
namespace PediatricClinicSpace\Kits\Settings\HeaderBot;

use PediatricClinicSpace\Kits\Settings\Base\Base_Section;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Header Bot section.
 */
class Section extends Base_Section {

	/**
	 * Get name.
	 *
	 * Retrieve the section name.
	 */
	public static function get_name() {
		return 'header-bot';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the section title.
	 */
	public static function get_title() {
		return esc_html__( 'Header Bottom', 'pediatric-clinic' );
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
			'header-bot',
			'social',
			'button',
			'search-button',
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
