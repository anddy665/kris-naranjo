<?php
namespace CmsmastersElementor\Modules\TableOfContents;

use CmsmastersElementor\Base\Base_Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * CMSMasters Elementor Tabs module.
 *
 * @since 1.12.0
 */
class Module extends Base_Module {

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.12.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'table_of_contents';
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.12.0
	 *
	 * @return array Module widgets.
	 */
	public function get_widgets() {
		return array( 'Table_Of_Contents' );
	}

}
