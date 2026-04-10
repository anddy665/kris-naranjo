<?php
namespace PediatricClinicSpace\Kits\Settings\Elements;

use PediatricClinicSpace\Kits\Settings\Base\Base_Section;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Elements section.
 */
class Section extends Base_Section {

	/**
	 * Get name.
	 *
	 * Retrieve the section name.
	 */
	public static function get_name() {
		return 'elements';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the section title.
	 */
	public static function get_title() {
		return esc_html__( 'Additional Elements', 'pediatric-clinic' );
	}

	/**
	 * Get icon.
	 *
	 * Retrieve the section icon.
	 */
	public static function get_icon() {
		return 'eicon-cogs';
	}

	/**
	 * Get toggles.
	 *
	 * Retrieve the section toggles.
	 */
	public static function get_toggles() {
		return array(
			'gutenberg',
			'pullquote',
			'slider-arrows',
			'slider-bullets',
			'slider-progressbar',
			'slider-fraction',
		);
	}

}
