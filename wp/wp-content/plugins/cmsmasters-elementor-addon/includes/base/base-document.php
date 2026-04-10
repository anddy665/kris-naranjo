<?php
namespace CmsmastersElementor\Base;

use CmsmastersElementor\Modules\TemplateDocuments\Module as DocumentsModule;
use CmsmastersElementor\Modules\TemplateLocations\Module as LocationsModule;
use CmsmastersElementor\Plugin;

use Elementor\Controls_Manager;
use Elementor\Modules\Library\Documents\Library_Document;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon base document class.
 *
 * An abstract class to register new Addon documents.
 *
 * This class extends the `Elementor\Modules\Library\Documents\Library_Document`
 * class to inherit his properties and methods, and must be extended in order
 * to register new Elementor templates library documents.
 *
 * @since 1.0.0
 */
abstract class Base_Document extends Library_Document {

	const WIDGETS_CATEGORY = 'cmsmasters-elements';
	const SITE_WIDGETS_CATEGORY = 'cmsmasters-site-elements';
	const SINGULAR_WIDGETS_CATEGORY = 'cmsmasters-singular-elements';
	const ARCHIVE_WIDGETS_CATEGORY = 'cmsmasters-archive-elements';

	const WOO_WIDGETS_CATEGORY = 'cmsmasters-woo-elements';
	const WOO_SINGULAR_WIDGETS_CATEGORY = 'cmsmasters-woo-singular-elements';
	const WOO_ARCHIVE_WIDGETS_CATEGORY = 'cmsmasters-woo-archive-elements';

	const TRIBE_EVENTS_WIDGETS_CATEGORY = 'cmsmasters-tribe-events-elements';
	const TRIBE_EVENTS_SINGULAR_WIDGETS_CATEGORY = 'cmsmasters-tribe-events-singular-elements';
	const TRIBE_EVENTS_ARCHIVE_WIDGETS_CATEGORY = 'cmsmasters-tribe-events-archive-elements';

	public static $widgets_visibility = array();

	public static $addon_prefix = 'cmsmasters';

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

		$properties['support_kit'] = true;

		$properties['admin_tab_group'] = 'cmsmasters';

		$properties['edit_in_content'] = true;
		$properties['multiple'] = false;

		$properties['location_type'] = 'general';
		$properties['location_include'] = array();
		$properties['location_exclude'] = array();
		$properties['locations_category'] = 'disabled';

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
		$editor_panel_categories = parent::get_editor_panel_categories();

		$editor_panel_categories = Utils::array_inject(
			$editor_panel_categories,
			'basic',
			array(
				self::WIDGETS_CATEGORY => array(
					'title' => __( 'CMSMasters', 'cmsmasters-elementor' ),
					'active' => true,
				),
			)
		);

		$editor_panel_categories = Utils::array_inject(
			$editor_panel_categories,
			'general',
			array(
				self::WOO_WIDGETS_CATEGORY => array(
					'title' => __( 'WooCommerce', 'cmsmasters-elementor' ),
					'active' => false,
				),
			)
		);

		$editor_panel_categories = Utils::array_inject(
			$editor_panel_categories,
			'general',
			array(
				self::TRIBE_EVENTS_WIDGETS_CATEGORY => array(
					'title' => __( 'Tribe Events', 'cmsmasters-elementor' ),
					'active' => false,
				),
			)
		);

		$editor_panel_categories = Utils::array_inject(
			$editor_panel_categories,
			'general',
			array(
				self::SITE_WIDGETS_CATEGORY => array(
					'title' => __( 'Site', 'cmsmasters-elementor' ),
					'active' => false,
				),
			)
		);

		/**
		 * Filter editor panel categories.
		 *
		 * Filters the Elementor editor panel categories.
		 *
		 * @since 1.0.0
		 *
		 * @param array $settings Editor panel categories.
		 */
		$editor_panel_categories = apply_filters( 'cmsmasters_elementor/documents/editor_panel_categories', $editor_panel_categories );

		return $editor_panel_categories;
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

		$prefix = static::get_editor_widgets_prefix();

		foreach ( self::get_editor_widgets_visibility() as $widget_class => $visibility ) {
			$widget_name = str_replace( '_', '-', strtolower( $widget_class ) );

			$config['widgets_settings'][ "{$prefix}-{$widget_name}" ] = array( 'show_in_panel' => $visibility );
		}

		return $config;
	}

	/**
	 * Get editor widgets visibility.
	 *
	 * Retrieve editor panel widgets visibility.
	 *
	 * @since 1.0.0
	 *
	 * @return array Editor panel widgets visibility.
	 */
	public static function get_editor_widgets_visibility() {
		return static::$widgets_visibility;
	}

	/**
	 * Get editor widgets prefix.
	 *
	 * Retrieve editor panel Addon widgets prefix.
	 *
	 * @since 1.0.0
	 *
	 * @return string Editor panel widgets prefix.
	 */
	public static function get_editor_widgets_prefix() {
		return static::$addon_prefix;
	}

	/**
	 * Render document output on the frontend.
	 *
	 * Used to generate the final HTML displayed on the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Document data.
	 *
	 * @return string Generated document content.
	 */
	public function render_element( $data ) {
		/**
		 * Before render element.
		 *
		 * Fires before document content render.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cmsmasters_elementor/documents/before_render_element' );

		$render_html = parent::render_element( $data );

		/**
		 * After render element.
		 *
		 * Fires after document content render.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cmsmasters_elementor/documents/after_render_element' );

		return $render_html;
	}

	/**
	 * Get container attributes.
	 *
	 * Retrieve the document container attributes.
	 *
	 * @since 1.0.0
	 *
	 * @return array Container attributes.
	 */
	public function get_container_attributes() {
		$attributes = parent::get_container_attributes();

		if ( is_admin() ) {
			$attributes['data-elementor-title'] = static::get_title();
		}

		/**
		 * Filter document container attributes.
		 *
		 * Filters the document container attributes.
		 *
		 * @since 1.0.0
		 *
		 * @param array $attributes Container attributes.
		 */
		$attributes = apply_filters( 'cmsmasters_elementor/documents/container_attributes', $attributes );

		return $attributes;
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

		$this->start_injection( array(
			'of' => 'post_status',
			'fallback' => array( 'of' => 'post_title' ),
		) );

		$this->add_control(
			'cmsmasters_document_export_id',
			array(
				'label' => __( 'Template Document ID', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => $this->get_main_id(),
				'export' => true,
				'classes' => 'elementor-control-type-hidden',
			)
		);

		$this->add_control(
			'cmsmasters_document_export_url',
			array(
				'label' => __( 'Template Site URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => get_site_url(),
				'export' => true,
				'classes' => 'elementor-control-type-hidden',
			)
		);

		$this->end_injection();

		$this->inject_html_tag_control();

		/**
		 * Register Addon document controls.
		 *
		 * Used to add new controls to the global document settings.
		 *
		 * Fires after Elementor registers the document controls.
		 *
		 * @since 1.0.0
		 *
		 * @param Base_Document $this Addon base document instance.
		 */
		do_action( 'cmsmasters_elementor/documents/register_controls', $this );
	}

	/**
	 * Inject HTML tag control.
	 *
	 * Injects the control to choose the HTML tag for implementing
	 * document that uses optional wrapper tags to replace `div`.
	 *
	 * @since 1.0.0
	 */
	private function inject_html_tag_control() {
		$wrapper_tags = $this->get_optional_wrapper_tags();

		if ( ! $wrapper_tags ) {
			return;
		}

		array_unshift( $wrapper_tags, 'div' );

		// combines to key->value array
		$options = array_combine( $wrapper_tags, $wrapper_tags );

		$this->start_injection( array(
			'of' => 'post_status',
			'fallback' => array( 'of' => 'post_title' ),
		) );

		$this->add_control(
			'content_wrapper_html_tag',
			array(
				'label' => __( 'HTML Tag', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'div',
				'options' => $options,
				'separator' => 'before',
			)
		);

		$this->end_injection();
	}

	public function get_optional_wrapper_tags() {
		return array(
			'section',
			'article',
			'aside',
			'header',
			'footer',
			'nav',
			'main',
		);
	}

	/**
	 * Get edit url.
	 *
	 * Retrieves implementing document edit url.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_edit_url() {
		$url = parent::get_edit_url();

		if ( isset( $_GET['action'] ) && 'elementor_new_post' === $_GET['action'] ) {
			$url .= ''; // add `#cmsmasters|#cmsmasters_templates_library` on templates library integration
		}

		return $url;
	}

	/**
	 * Get the document elements raw data.
	 *
	 * Retrieve the document raw elements data, including the id, type, settings,
	 * child elements and whether it is an inner elements.
	 *
	 * The data with the HTML used always to display the data, but the Elementor
	 * editor uses the raw data without the HTML in order not to render the data
	 * again.
	 *
	 * @since 1.0.0
	 *
	 * @param null $data Optional. Elements data. Default is null, without data.
	 * @param bool $with_html_content Optional. Whether to return the data with
	 * HTML content or without. Default is false, without HTML.
	 *
	 * @return array Document elements raw data.
	 */
	public function get_elements_raw_data( $data = null, $with_html_content = false ) {
		/**
		 * Before get the document elements raw data.
		 *
		 * Fires before document retrieve the elements raw data.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cmsmasters_elementor/documents/before_get_elements_raw_data' );

		$editor_data = parent::get_elements_raw_data( $data, $with_html_content );

		/**
		 * After get the document elements raw data.
		 *
		 * Fires after document retrieve the elements raw data.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cmsmasters_elementor/documents/after_get_elements_raw_data' );

		return $editor_data;
	}

	/**
	 * Print element with wrapper.
	 *
	 * Used to generate the element final HTML inside user-selected
	 * wrapper tag on the frontend and the editor.
	 *
	 * @since 1.0.0
	 */
	public function print_elements_with_wrapper( $elements_data = null ) {
		$html_attributes = Utils::render_html_attributes( $this->get_container_attributes() );
		$settings = $this->get_settings_for_display();

		$wrapper_tag = ( $this->get_optional_wrapper_tags() ) ? $settings['content_wrapper_html_tag'] : 'div';

		if ( ! $elements_data ) {
			$elements_data = $this->get_elements_data();
		}

		echo "<{$wrapper_tag} {$html_attributes}>";
		?>
			<div class="elementor-inner">
				<div class="elementor-section-wrap">
					<?php $this->print_elements( $elements_data ); ?>
				</div>
			</div>
		<?php
		echo "</{$wrapper_tag}>";
	}

	/**
	 * Retrieve document content.
	 *
	 * Used to render and return the document content with all the Elementor elements.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $with_css Optional. Whether to retrieve the content with
	 * CSS or not. Default is false.
	 *
	 * @return string The post content.
	 */
	public function get_content( $with_css = false ) {
		$this->before_get_content();

		$content = parent::get_content( $with_css );

		$this->after_get_content();

		return $content;
	}

	/**
	 * Before document content.
	 *
	 * Runs before document content render.
	 *
	 * @since 1.0.0
	 */
	public function before_get_content() {
		/**
		 * Before get document content.
		 *
		 * Fires before document retrieve the content.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cmsmasters_elementor/documents/before_get_content' );
	}

	/**
	 * After document content.
	 *
	 * Runs after document content render.
	 *
	 * @since 1.0.0
	 */
	public function after_get_content() {
		/**
		 * After get document content.
		 *
		 * Fires after document retrieve the content.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cmsmasters_elementor/documents/after_get_content' );
	}

	/**
	 * Save template type.
	 *
	 * Set document name as template type meta key.
	 *
	 * @since 1.0.0
	 */
	public function save_template_type() {
		parent::save_template_type();

		do_action( 'cmsmasters_elementor/documents/after_save_template_type' );
	}

	/**
	 * Save settings.
	 *
	 * Save document settings to the database.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Document settings.
	 */
	public function save_settings( $settings ) {
		/**
		 * Filter Addon document settings saving.
		 *
		 * Filters the Addon document settings before saving.
		 *
		 * @since 1.0.0
		 *
		 * @param array $settings Document settings.
		 */
		$settings = apply_filters( 'cmsmasters_elementor/documents/before_save_settings', $settings );

		parent::save_settings( $settings );
	}

	public static function get_preview_type_options() {
		return array();
	}

	public function get_preview_type_default() {
		return '';
	}

	public function get_preview_id_default() {
		return '';
	}

	public function get_locations_default() {
		/** @var DocumentsModule $documents_module */
		$documents_module = DocumentsModule::instance();
		$document = $documents_module->get_document( $this->get_main_id() );

		/** @var LocationsModule $locations_module */
		$locations_module = LocationsModule::instance();
		$rules_manager = $locations_module->get_rules_manager();

		$document_locations = $rules_manager->get_document_locations( $document );

		return ! empty( $document_locations ) ? $document_locations : array();
	}

	public function get_document_location_type() {
		return $this->get_content_editable() ?
			sprintf( '%1$s_%2$s', self::$addon_prefix, $this->get_location_type() ) :
			$this->get_name();
	}

	public function get_location_type() {
		$property = self::get_property( 'location_type' );

		return $property ? $property : '';
	}

	public function get_content_editable() {
		$property = self::get_property( 'edit_in_content' );

		return $property ? $property : false;
	}

	public function get_location_filter() {
		$include = self::get_property( 'location_include' );
		$exclude = self::get_property( 'location_exclude' );

		$options = array();

		if ( ! empty( $include ) ) {
			$options['include'] = $include;
		} elseif ( ! empty( $exclude ) ) {
			$options['exclude'] = $exclude;
		}

		if ( empty( $options ) ) {
			return false;
		}

		return $options;
	}

	/**
	 * Print content.
	 *
	 * Used to add an builder for the preview mode or render and print
	 * the document content with all the Elementor elements.
	 *
	 * @since 1.0.0
	 */
	public function print_content() {
		$elementor_preview = Plugin::elementor()->preview;

		if ( $elementor_preview->is_preview_mode( $this->get_main_id() ) ) {
			echo $elementor_preview->builder_wrapper( '' );
		} else {
			echo $this->get_content();
		}
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
