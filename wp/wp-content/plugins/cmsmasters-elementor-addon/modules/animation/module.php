<?php
namespace CmsmastersElementor\Modules\Animation;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\Animation\Classes\Animation;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon animation module.
 *
 * The animation class is responsible for animation module controls integration.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	/**
	 * Get module name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'animation';
	}

	/**
	 * Animation module constructor.
	 *
	 * Initializing the Addon animation module.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->add_component( 'animation', new Animation() );

		parent::__construct();
	}

	/**
	 * Get animation.
	 *
	 * Retrieve the animation module component.
	 *
	 * @return Animation
	 */
	public function get_animation() {
		return $this->get_component( 'animation' );
	}
}
