<?php
namespace PediatricClinicSpace\Kits\Settings\Archive;

use PediatricClinicSpace\Kits\Settings\Base\Base_Section;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Archive section.
 */
class Section extends Base_Section {

	/**
	 * Get name.
	 *
	 * Retrieve the section name.
	 */
	public static function get_name() {
		return 'archive';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the section title.
	 */
	public static function get_title() {
		return esc_html__( 'Archive', 'pediatric-clinic' );
	}

	/**
	 * Get icon.
	 *
	 * Retrieve the section icon.
	 */
	public static function get_icon() {
		return 'eicon-archive';
	}

	/**
	 * Get toggles.
	 *
	 * Retrieve the section toggles.
	 */
	public static function get_toggles() {
		return array(
			'archive',
			'post',
			'media',
			'title',
			'content',
			'meta_first',
			'meta_second',
			'more',
			'pagination',
		);
	}

}
