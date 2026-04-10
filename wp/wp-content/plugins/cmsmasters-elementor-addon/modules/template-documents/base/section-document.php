<?php
namespace CmsmastersElementor\Modules\TemplateDocuments\Base;

use CmsmastersElementor\Base\Base_Document;

use Elementor\Core\DocumentTypes\Post;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters section document.
 *
 * An abstract class that provides the needed properties and methods to
 * manage and handle section documents in inheriting classes.
 *
 * @since 1.0.0
 */
abstract class Section_Document extends Base_Document {

	/**
	 * Get properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @since 1.0.0
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['group'] = 'blocks';

		$properties = apply_filters( 'cmsmasters_elementor/documents/sections/get_properties', $properties );

		return $properties;
	}

	/**
	 * Register document controls.
	 *
	 * Used to add new controls to section documents settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		parent::register_controls();

		Post::register_style_controls( $this );

		/**
		 * Register Section document controls.
		 *
		 * Used to add new controls to the section document settings.
		 *
		 * Fires after Elementor registers the document controls.
		 *
		 * @since 1.0.0
		 *
		 * @param Section_Document $this Section base document instance.
		 */
		do_action( 'cmsmasters_elementor/documents/section/register_controls', $this );
	}

	public function get_optional_wrapper_tags() {
		return array(
			'section',
			'article',
			'aside',
		);
	}

	/**
	 * Get CSS wrapper selector.
	 *
	 * Retrieve CSS wrapper selector for document custom styles.
	 *
	 * @since 1.0.0
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
	 * @since 1.0.0
	 *
	 * @return array Addon templates library config.
	 */
	protected function get_remote_library_config() {
		$config = parent::get_remote_library_config();

		$config['category'] = '';

		return $config;
	}

}
