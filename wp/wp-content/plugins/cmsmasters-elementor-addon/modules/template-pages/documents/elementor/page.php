<?php
namespace CmsmastersElementor\Modules\TemplatePages\Documents\Elementor;

use CmsmastersElementor\Modules\TemplateDocuments\Base\Page_Document;
use CmsmastersElementor\Modules\TemplatePreview\Traits\Preview_Type;
use CmsmastersElementor\Utils;

use Elementor\Utils as ElementorUtils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Page extends Page_Document {

	use Preview_Type;

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Document name.
	 */
	public function get_name() {
		return 'page';
	}

	/**
	 * Get document title.
	 *
	 * Retrieve the document title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Document title.
	 */
	public static function get_title() {
		return __( 'Page', 'cmsmasters-elementor' );
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

		$properties['admin_tab_group'] = 'library';

		$properties = apply_filters( 'cmsmasters_elementor/documents/elementor/page/get_properties', $properties );

		return $properties;
	}

	/**
	 * Get editor panel categories.
	 *
	 * Retrieve the list of categories the element belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Editor panel categories.
	 */
	protected static function get_editor_panel_categories() {
		$categories = ElementorUtils::array_inject(
			parent::get_editor_panel_categories(),
			self::SITE_WIDGETS_CATEGORY,
			array(
				self::SINGULAR_WIDGETS_CATEGORY => array(
					'title' => __( 'Singular', 'cmsmasters-elementor' ),
					'active' => false,
				),
			)
		);

		if ( ! Utils::is_pro() ) {
			$categories['pro-elements']['active'] = false;
		}

		return $categories;
	}

	/**
	 * Register document controls.
	 *
	 * Used to add new controls to documents settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		parent::register_controls();

		/**
		 * Register Page document controls.
		 *
		 * Used to add new controls to the Page document settings.
		 *
		 * Fires after Elementor registers the document controls.
		 *
		 * @since 1.0.0
		 *
		 * @param Section_Document $this Page base document instance.
		 */
		do_action( 'cmsmasters_elementor/documents/elementor/page/register_controls', $this );
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

}
