<?php
namespace CmsmastersElementor\Modules\ContactForm;

use CmsmastersElementor\Base\Base_Module;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters Elementor Contact form 7 module.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	/**
	 * Add actions initialization.
	 *
	 * Register actions for the Preview app.
	 *
	 * @since 1.1.0
	 */
	protected function init_actions() {
		if ( class_exists( 'Forminator' ) ) {
			add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-contact-form';
	}

	/**
	 * Module activation.
	 *
	 * Check if module is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_active() {
		return function_exists( 'wpcf7' ) || function_exists( 'wpforms' ) || class_exists( 'Forminator' );
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.0.0
	 *
	 * @return array Module widgets.
	 */
	public function get_widgets() {
		$widgets = array();

		if ( function_exists( 'wpcf7' ) ) {
			$widgets[] = 'Contact_Form_Seven';
		}

		if ( function_exists( 'wpforms' ) ) {
			$widgets[] = 'WP_Form';
		}

		if ( class_exists( 'Forminator' ) ) {
			$widgets[] = 'CMS_Forminator';

		}

		return $widgets;
	}


	/**
	 * Enqueue scripts
	 *
	 * Connects the necessary scripts to the preview.
	 *
	 * @since 1.1.0
	 */
	public function enqueue_scripts() {
		if ( class_exists( 'Forminator' ) ) {
			$form_obj = new \Forminator_GFBlock_Forms();

			$form_obj->load_assets();
		}
	}
}
