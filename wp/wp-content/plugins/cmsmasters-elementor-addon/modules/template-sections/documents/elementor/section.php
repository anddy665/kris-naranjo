<?php
namespace CmsmastersElementor\Modules\TemplateSections\Documents\Elementor;

use CmsmastersElementor\Modules\TemplateDocuments\Base\Section_Document;
use CmsmastersElementor\Modules\TemplatePreview\Traits\Preview_Type;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Section extends Section_Document {

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
		return 'section';
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
		return __( 'Section', 'cmsmasters-elementor' );
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

		$properties = apply_filters( 'cmsmasters_elementor/documents/elementor_section/get_properties', $properties );

		return $properties;
	}

	/**
	 * Register document controls.
	 *
	 * Used to add new controls to section documents settings.
	 *
	 * @since 1.1.0
	 */
	protected function register_controls() {
		parent::register_controls();

		if ( $this->is_edit() ) {
			$this->register_preview_controls();
		}

		/**
		 * Register Elementor Section document controls.
		 *
		 * Used to add new controls to the Elementor section document settings.
		 *
		 * Fires after Elementor registers the document controls.
		 *
		 * @since 1.0.0
		 *
		 * @param Section_Document $this Section base document instance.
		 */
		do_action( 'cmsmasters_elementor/documents/elementor_section/register_controls', $this );
	}

	/**
	 * Check edit mode.
	 *
	 * Checks if document opened in edit mode.
	 *
	 * @since 1.1.0
	 */
	public function is_edit() {
		if ( ! is_admin() ) {
			return false;
		}

		$document_id = Utils::get_document_id();

		return $document_id && $document_id === $this->get_main_id();
	}

	/**
	 * Register preview controls.
	 *
	 * Creates additional document preview controls.
	 *
	 * @since 1.1.0
	 */
	protected function register_preview_controls() {
		$this->start_injection( array(
			'of' => 'preview_settings_heading',
			'at' => 'before',
		) );

		$this->add_responsive_control(
			'preview_width',
			array(
				'label' => __( 'Preview Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 100,
						'max' => 1500,
					),
					'%' => array(
						'min' => 10,
						'max' => 100,
					),
					'vh' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'size_units' => array( 'px', '%', 'vw' ),
				'selectors' => array(
					'.elementor.elementor-edit-area > .elementor-section-wrap > *' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; margin: 0 auto;',
				),
			)
		);

		$this->end_injection();
	}

	/**
	 * Get editor widgets panel config.
	 *
	 * Modifies editor widgets panel config, such as widgets settings or
	 * elements categories.
	 *
	 * @since 1.0.0
	 *
	 * @return array Modified editor panel config.
	 */
	public static function get_editor_panel_config() {
		$config = parent::get_editor_panel_config();

		$config['widgets_settings']['cmsmasters-post-content'] = array( 'show_in_panel' => true );
		$config['widgets_settings']['cmsmasters-woo-product-content'] = array( 'show_in_panel' => true );
		$config['widgets_settings']['cmsmasters-tribe-events-event-content'] = array( 'show_in_panel' => true );

		return $config;
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
