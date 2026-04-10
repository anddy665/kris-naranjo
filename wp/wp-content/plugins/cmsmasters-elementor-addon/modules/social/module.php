<?php
namespace CmsmastersElementor\Modules\Social;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\Social\Classes\Facebook_SDK_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters elementor social module.
 *
 * CMSMasters elementor social module handler class is responsible for
 * registering and managing group.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	const URL_TYPE_CURRENT_PAGE = 'current_page';
	const URL_TYPE_CUSTOM = 'custom';

	const URL_FORMAT_PLAIN = 'plain';
	const URL_FORMAT_PRETTY = 'pretty';

	/**
	 * Get module name.
	 *
	 * Retrieve the CMSMasters social module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-social';
	}

	/**
	 * Get widget names.
	 *
	 * Retrieve the CMSMasters social widget names.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget names.
	 */
	public function get_widgets() {
		return array(
			'Facebook',
			'Pinterest',
			'Twitter',
		);
	}

	public function __construct() {
		parent::__construct();

		$this->add_component( 'facebook_sdk', new Facebook_SDK_Manager() );
	}

}
