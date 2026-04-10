<?php
namespace CmsmastersElementor\Modules\AuthorizationForm;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\AuthorizationForm\Register_Function;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters Elementor authorization form module.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

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
		return 'cmsmasters-authorization-form';
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

		$widgets = array(
			'register',
			'login',
		);

		return $widgets;
	}

	/**
	 * Init actions.
	 *
	 * Initialize module actions.
	 *
	 * @since 1.0.0
	 */
	protected function init_actions() {
		$register_function = Register_Function::instance();

		add_action( 'init', array( $register_function, 'custom_registration_function' ) );
	}
}
