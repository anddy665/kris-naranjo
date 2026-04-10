<?php
namespace CmsmastersElementor\Controls\Groups;

use CmsmastersElementor\Controls\Groups\Base\Group_Control_Format_Date_Time;
use CmsmastersElementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Group_Control_Format_Time extends Group_Control_Format_Date_Time {

	/**
	 * @var array control fields.
	 */
	protected static $fields;

	public static function get_type() {
		return Controls_Manager::TIME_FORMAT_GROUP;
	}

	protected static function get_field_type() {
		return 'time';
	}

	protected static function get_format_label() {
		return __( 'Time Format', 'cmsmasters-elementor' );
	}

	protected static function get_main_formats() {
		return array_unique( apply_filters( 'time_formats', array( 'g:i a', 'g:i A', 'H:i' ) ) );
	}

	public static function get_field_format_option() {
		return get_option( 'time_format' );
	}

}
