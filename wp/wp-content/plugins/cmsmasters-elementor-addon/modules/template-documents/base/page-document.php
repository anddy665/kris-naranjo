<?php
namespace CmsmastersElementor\Modules\TemplateDocuments\Base;

use CmsmastersElementor\Base\Base_Document;

use Elementor\Core\DocumentTypes\Post;
use Elementor\Modules\PageTemplates\Module as PageTemplatesModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters page document.
 *
 * An abstract class that provides the needed properties and methods to
 * manage and handle page documents in inheriting classes.
 *
 * @since 1.0.0
 */
abstract class Page_Document extends Base_Document {

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @since 1.0.0
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['group'] = 'pages';
		$properties['support_wp_page_templates'] = true;

		$properties = apply_filters( 'cmsmasters_elementor/documents/pages/get_properties', $properties );

		return $properties;
	}

	/**
	 * Register document controls.
	 *
	 * Used to add new controls to page documents settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		parent::register_controls();

		/** @var PageTemplatesModule $page_templates_module */
		$page_templates_module = PageTemplatesModule::instance();
		$page_templates_module->register_template_control( $this );

		Post::register_style_controls( $this );

		/**
		 * Register pages document controls.
		 *
		 * Used to add new controls to the pages document settings.
		 *
		 * Fires after Elementor registers the document controls.
		 *
		 * @since 1.0.0
		 *
		 * @param Page_Document $this Pages base document instance.
		 */
		do_action( 'cmsmasters_elementor/documents/pages/register_controls', $this );
	}

	public function get_optional_wrapper_tags() {
		return array(
			'section',
			'article',
			'nav',
			'main',
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
		return 'body.elementor-page-' . $this->get_main_id();
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

		$config['type'] = 'page';
		$config['default_route'] = 'templates/pages';

		return $config;
	}

	public static function get_preview_type_options() {
		return array( '' => __( 'Select preview', 'cmsmasters-elementor' ) );
	}

}
