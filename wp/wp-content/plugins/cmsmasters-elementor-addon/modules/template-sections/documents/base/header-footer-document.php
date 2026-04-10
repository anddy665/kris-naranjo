<?php
namespace CmsmastersElementor\Modules\TemplateSections\Documents\Base;

use CmsmastersElementor\Modules\TemplateDocuments\Base\Section_Document;
use CmsmastersElementor\Modules\TemplatePreview\Traits\Preview_Type;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters header & footer documents.
 *
 * An abstract class that provides the needed properties and methods to
 * manage and handle header & footer documents in inheriting classes.
 *
 * @since 1.0.0
 */
abstract class Header_Footer_Document extends Section_Document {

	use Preview_Type;

	/**
	 * Get editor panel categories.
	 *
	 * Retrieve the Elementor editor panel widgets categories.
	 * Moved selected category to editor widgets panel top as active.
	 *
	 * @since 1.0.0
	 *
	 * @return array Editor panel categories.
	 */
	protected static function get_editor_panel_categories() {
		$categories = array(
			self::SITE_WIDGETS_CATEGORY => array( 'title' => __( 'Site', 'cmsmasters-elementor' ) ),
		);

		if ( Utils::is_pro() ) {
			$categories['theme-elements'] = array(
				'title' => __( 'Site', 'cmsmasters-elementor' ),
				'active' => true,
			);
		}

		$categories += parent::get_editor_panel_categories();

		if ( ! Utils::is_pro() ) {
			$categories['pro-elements']['active'] = false;
		}

		return $categories;
	}

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

		$properties['edit_in_content'] = false;
		$properties['locations_category'] = 'parent';

		$properties = apply_filters( 'cmsmasters_elementor/documents/header_footer/get_properties', $properties );

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

		/**
		 * Register Header and Footer document controls.
		 *
		 * Used to add new controls to the header and footer document settings.
		 *
		 * Fires after Elementor registers the document controls.
		 *
		 * @since 1.0.0
		 *
		 * @param Header_Footer_Document $this Header and footer base document instance.
		 */
		do_action( 'cmsmasters_elementor/documents/header_footer/register_controls', $this );
	}

	public function get_optional_wrapper_tags() {
		return array(
			'section',
			'header',
			'footer',
			'nav',
		);
	}

	/**
	 * @since 1.0.0
	 *
	 * @return array Document preview type options.
	 */
	public static function get_preview_type_options() {
		return array_merge(
			array( '' => __( 'Select preview', 'cmsmasters-elementor' ) ),
			self::get_archive_preview_type_options_choices(),
			self::get_singular_preview_type_options_choices()
		);
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

		$config['category'] = str_replace( 'cmsmasters_', '', $this->get_name() );

		return $config;
	}

}
