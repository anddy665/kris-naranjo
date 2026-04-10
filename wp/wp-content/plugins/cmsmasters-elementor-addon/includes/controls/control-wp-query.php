<?php
namespace CmsmastersElementor\Controls;

use CmsmastersElementor\Controls_Manager;

use Elementor\Control_Select2;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon query control.
 *
 * A base control for creating query control.
 * Displays a select box control based on select2 jQuery plugin.
 *
 * It accepts an array in which the `key` is the value and the `value` is the
 * option name. Set `multiple` to `true` to allow multiple value selection.
 *
 * @since 1.0.0
 */
class Control_Wp_Query extends Control_Select2 {

	/**
	 * Retrieve the control type.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_type() {
		return Controls_Manager::QUERY;
	}

	/**
	 * Get control default settings.
	 *
	 * Retrieve the default settings of the control. Used to return the
	 * default settings while initializing the control.
	 *
	 * The 'query' settings array argument can be used for passing the
	 * query args in the structure and format used by \WP_Query class.
	 *
	 * @since 1.0.0
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return array_merge( parent::get_default_settings(), array( 'query' => '' ) );
	}

}
