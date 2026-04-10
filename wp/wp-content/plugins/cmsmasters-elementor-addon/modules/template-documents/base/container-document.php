<?php
namespace CmsmastersElementor\Modules\TemplateDocuments\Base;

use CmsmastersElementor\Base\Base_Document;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters container document.
 *
 * An abstract class that provides the needed properties and methods to
 * manage and handle container documents in inheriting classes.
 *
 * @since 1.18.0
 */
abstract class Container_Document extends Base_Document {

	/**
	 * Get properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @since 1.18.0
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['group'] = 'blocks';

		$properties = apply_filters( 'cmsmasters_elementor/documents/containers/get_properties', $properties );

		return $properties;
	}

	/**
	 * Register document controls.
	 *
	 * Used to add new controls to container documents settings.
	 *
	 * @since 1.18.0
	 */
	protected function register_controls() {
		parent::register_controls();

		/**
		 * Register Container document controls.
		 *
		 * Used to add new controls to the container document settings.
		 *
		 * Fires after Elementor registers the document controls.
		 *
		 * @since 1.18.0
		 *
		 * @param Container_Document $this Container base document instance.
		 */
		do_action( 'cmsmasters_elementor/documents/container/register_controls', $this );
	}

	/**
	 * Get CSS wrapper selector.
	 *
	 * Retrieve CSS wrapper selector for document custom styles.
	 *
	 * @since 1.18.0
	 *
	 * @return string CSS wrapper selector.
	 */
	public function get_css_wrapper_selector() {
		return '.elementor-' . $this->get_main_id();
	}

	/**
	 * Get remote library config.
	 *
	 * Retrieves Addon remote templates library config.
	 *
	 * @since 1.18.0
	 *
	 * @return array Addon templates library config.
	 */
	protected function get_remote_library_config() {
		$config = parent::get_remote_library_config();

		$config['category'] = '';

		return $config;
	}

}
