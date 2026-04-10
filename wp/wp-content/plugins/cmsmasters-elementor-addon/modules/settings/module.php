<?php
namespace CmsmastersElementor\Modules\Settings;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\Settings\Settings_Page;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Settings module.
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
		return 'cmsmasters_settings';
	}

	/**
	 * Init actions.
	 *
	 * Initialize module actions.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->add_component( Settings_Page::PAGE_ID, new Settings_Page() );

		parent::__construct();
	}

}
