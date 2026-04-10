<?php
namespace CmsmastersElementor\Modules\Popup;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Modules\Popup\Documents\Popup;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\TemplateLibrary\Source_Local;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends Base_Module {

	/**
	 * Tracks popup IDs already rendered on this request to prevent duplicates.
	 *
	 * @since 1.24.0
	 *
	 * @var array
	 */
	private $rendered_popups = array();

	/**
	 * Cached popup posts to avoid duplicate DB queries.
	 *
	 * @since 1.24.0
	 *
	 * @var array|null
	 */
	private $popups = null;

	/**
	 * Whether popup scripts have already been enqueued on this request.
	 *
	 * @since 1.24.0
	 *
	 * @var bool
	 */
	private $scripts_enqueued = false;

	/**
	 * Get module name.
	 *
	 * Retrieve the CMSMasters Popup module name.
	 *
	 * @since 1.9.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-popup';
	}

	/**
	 * Add filters initialization.
	 *
	 * Register filters for the Blog module.
	 *
	 * @since 1.9.0
	 * @since 1.17.5 Fixed popup widget assets in lazyload widget.
	 * @since 1.24.0 Fixed popup script enqueue.
	 */
	protected function init_filters() {
		add_filter( 'cmsmasters_elementor/documents/set_document_types', array( $this, 'set_document_types' ) );

		$popups = $this->get_popups();

		if ( ! empty( $popups ) && ! is_wp_error( $popups ) ) {
			add_filter( 'elementor/widget/render_content', array( $this, 'add_popup_to_widgets' ), 10, 2 );
		}
	}

	/**
	 * Add actions initialization.
	 *
	 * @since 1.9.0
	 * @since 1.24.0 Scripts are now enqueued lazily inside render_popup_template().
	 */
	protected function init_actions() {
		$popups = $this->get_popups();

		if ( ! empty( $popups ) && ! is_wp_error( $popups ) ) {
			add_action( 'cmsmasters_elementor/frontend/lazyload_widget_enqueue_template_assets', array( $this, 'lazyload_widget_add_popup_template_widgets_assets' ) );
		}
	}

	/**
	 * Set popup module document.
	 *
	 * Fired by `cmsmasters_elementor/documents/set_document_types` Addon filter hook.
	 *
	 * @since 1.9.0
	 *
	 * @return array
	 */
	public function set_document_types( $document_types ) {
		$module_document_types = array(
			'cmsmasters_popup' => Popup::get_class_full_name(),
		);

		$document_types = array_merge( $document_types, $module_document_types );

		return $document_types;
	}

	/**
	 * Add popup to widgets.
	 *
	 * Fired by `elementor/widget/render_content` Addon filter hook.
	 *
	 * @since 1.9.0
	 * @since 1.9.1 Fixed empty template id.
	 * @since 1.9.2 Fixed popup for multiple links.
	 *
	 * @return string HTML
	 */
	public function add_popup_to_widgets( $widget_content, $widget ) {
		$settings = $widget->get_settings();

		foreach ( $settings as $key => $values ) {
			if ( '__dynamic__' === $key && ! empty( $values ) ) {
				$widget_content .= $this->render_popup_template( $values );
			} elseif ( is_array( $values ) ) {
				foreach ( $values as $key_inner => $value_inner ) {
					if ( ! empty( $value_inner['__dynamic__'] ) ) {
						$widget_content .= $this->render_popup_template( $value_inner['__dynamic__'] );
					}
				}
			}
		}

		return $widget_content;
	}

	/**
	 * Lazyload Widget add popup template widgets assets.
	 *
	 * @since 1.17.5
	 *
	 * @param object $widget Widget.
	 */
	public function lazyload_widget_add_popup_template_widgets_assets( $widget ) {
		$settings = $widget->get_settings();

		foreach ( $settings as $key => $values ) {
			if ( '__dynamic__' === $key && ! empty( $values ) ) {
				$this->lazyload_widget_parse_popup_template_widgets_assets( $values );
			} elseif ( is_array( $values ) ) {
				foreach ( $values as $key_inner => $value_inner ) {
					if ( ! empty( $value_inner['__dynamic__'] ) ) {
						$this->lazyload_widget_parse_popup_template_widgets_assets( $value_inner['__dynamic__'] );
					}
				}
			}
		}
	}

	/**
	 * Lazyload Widget parse popup template widgets assets.
	 *
	 * @since 1.17.5
	 *
	 * @param array $values Settings values.
	 */
	public function lazyload_widget_parse_popup_template_widgets_assets( $values = array() ) {
		if ( empty( $values ) ) {
			return '';
		}

		foreach ( $values as $key => $value ) {
			if ( false === strpos( $value, 'cmsmasters-action-popup' ) ) {
				continue;
			}

			preg_match( '/settings="(.*?)"/', $value, $matches_settings );
			$decoded_settings = urldecode( $matches_settings[1] );
			$decoded_settings = json_decode( $decoded_settings );
			$popup_id = esc_attr( $decoded_settings->popup_id );

			if ( empty( $popup_id ) || 'cmsmasters_popup' !== Source_Local::get_template_type( $popup_id ) ) {
				continue;
			}

			Plugin::instance()->frontend->lazyload_widget_enqueue_popup_template_widgets_assets( $popup_id );
		}
	}

	/**
	 * Render popup template.
	 *
	 * @since 1.11.1
	 * @since 1.24.0 Fixed popup template rendering.
	 *
	 * @return string HTML
	 */
	public function render_popup_template( $values = array() ) {
		if ( empty( $values ) ) {
			return '';
		}

		$popup = '';

		foreach ( $values as $key => $value ) {
			if ( false === strpos( $value, 'cmsmasters-action-popup' ) ) {
				continue;
			}

			preg_match( '/settings="(.*?)"/', $value, $matches_settings );
			$decoded_settings = urldecode( $matches_settings[1] );
			$decoded_settings = json_decode( $decoded_settings );
			$popup_id = esc_attr( $decoded_settings->popup_id );

			// Skip if popup ID is empty, template type is not cmsmasters_popup, or popup ID has already been rendered.
			if (
				empty( $popup_id ) ||
				'cmsmasters_popup' !== Source_Local::get_template_type( $popup_id ) ||
				in_array( $popup_id, $this->rendered_popups, true )
			) {
				continue;
			}

			// Add popup ID to rendered popups array to prevent duplicates.
			$this->rendered_popups[] = $popup_id;

			// Enqueue popup scripts on first actual render — not on every page.
			if ( ! $this->scripts_enqueued ) {
				wp_enqueue_script( 'perfect-scrollbar-js' );
				wp_enqueue_script( 'jquery-ui-datepicker' );
				$this->scripts_enqueued = true;
			}

			/** @var Plugin $addon */
			$addon = Plugin::instance();
			$frontend = $addon->frontend;

			$frontend->print_template_css( array( $popup_id ), $popup_id );

			$popup .= "<div class='animated elementor-popup-modal cmsmasters-elementor-popup cmsmasters-elementor-popup-" . $popup_id . "' data-popup-id='" . $popup_id . "'>" . $frontend->get_widget_template( $popup_id ) . "</div>";
		}

		return $popup;
	}

	/**
	 * Get popup posts.
	 *
	 * @since 1.9.0
	 * @since 1.24.0 Fixed popup posts retrieval.
	 *
	 * @return array|null
	 */
	public function get_popups() {
		if ( null === $this->popups ) {
			$this->popups = get_posts(
				array(
					'post_type' => 'elementor_library',
					'meta_query' => array(
						array(
							'key' => Document::TYPE_META_KEY,
							'value' => 'cmsmasters_popup',
						),
					),
					'numberposts' => -1,
				)
			);
		}

		return $this->popups;
	}

	/**
	 * Retrieve widget classes name.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public function get_widgets() {
		return array(
			'Time_Popup',
		);
	}
}
