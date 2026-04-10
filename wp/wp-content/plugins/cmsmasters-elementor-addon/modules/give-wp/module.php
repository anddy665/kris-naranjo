<?php
namespace CmsmastersElementor\Modules\GiveWp;

use CmsmastersElementor\Base\Base_Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}



class Module extends Base_Module {

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.6.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-give-wp';
	}

	/**
	 * Module activation.
	 *
	 * Check if module is active.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public static function is_active() {
		return class_exists( 'Give' );
	}

	/**
	 * Add actions initialization.
	 *
	 * @since 1.10.0
	 */
	protected function init_actions() {
		add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts.
	 *
	 * Enqueue all the frontend scripts.
	 *
	 * @since 1.10.0
	 * @access public
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'perfect-scrollbar-js' );
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.6.0
	 *
	 * @return array Module widgets.
	 */
	public function get_widgets() {
		$widgets = array();

		if ( class_exists( 'Give' ) ) {
			$widgets[] = 'Give_WP_Forms';
			$widgets[] = 'Give_WP_Donor_Wall';
			$widgets[] = 'Give_WP_Form_Grid';
			$widgets[] = 'Give_WP_Receipt';
			$widgets[] = 'Give_WP_History';
			$widgets[] = 'Give_WP_Totals';
			$widgets[] = 'Give_WP_Goal';
			$widgets[] = 'Give_WP_Donor_Dashboard';
			$widgets[] = 'Give_WP_Profile_Editor';
			$widgets[] = 'Give_WP_Multi_Form_Goal';
			$widgets[] = 'Give_WP_Donation_Form';
			$widgets[] = 'Give_WP_Donation_Form_Grid';
		}

		return $widgets;
	}
}
