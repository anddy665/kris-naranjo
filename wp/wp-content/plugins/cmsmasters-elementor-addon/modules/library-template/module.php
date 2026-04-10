<?php
namespace CmsmastersElementor\Modules\LibraryTemplate;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Module extends Base_Module {

	public static $exclude_document_types = array(
		'cmsmasters_entry',
		'cmsmasters_product_entry',
		'cmsmasters_tribe_events_entry',
	);

	public function get_name() {
		return 'cmsmasters_library_template';
	}

	public function get_widgets() {
		return array( 'Template' );
	}

	protected function init_actions() {
		add_action( 'widgets_init', array( $this, 'register_wp_widgets' ) );
	}

	protected function init_filters() {
		// Editor
		add_filter( 'elementor/widgets/black_list', array( $this, 'add_to_widgets_black_list' ) );

		// Frontend
		add_filter( 'cmsmasters_elementor/frontend/settings', array( $this, 'filter_frontend_settings' ) );
	}

	/**
	 * Register WordPress widgets.
	 *
	 * Register new WordPress widgets.
	 *
	 * Fired by `widgets_init` WordPress action hook.
	 *
	 * @since 1.0.0
	 */
	public function register_wp_widgets() {
		register_widget( __NAMESPACE__ . '\WP_Widgets\Elementor_Library' );
	}

	/**
	 * Add widgets to black list.
	 *
	 * Add widgets to Elementor widgets black list and exclude them from editor.
	 *
	 * Fired by `elementor/widgets/black_list` Elementor filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $black_list Elementor widgets black list.
	 *
	 * @return array Extended Elementor widgets black list.
	 */
	public function add_to_widgets_black_list( $black_list ) {
		$black_list[] = __NAMESPACE__ . '\WP_Widgets\Elementor_Library';

		return $black_list;
	}

	/**
	 * Filter frontend settings.
	 *
	 * Filters the Addon settings for elementor frontend.
	 *
	 * Fired by `cmsmasters_elementor/frontend/settings` Addon filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Frontend settings.
	 *
	 * @return array Filtered frontend settings.
	 */
	public function filter_frontend_settings( $settings ) {
		$settings = array_replace_recursive( $settings, array(
			'i18n' => array(
				'template_id' => __( 'Template', 'cmsmasters-elementor' ),
			),
		) );

		return $settings;
	}

	public static function get_templates( $types = array() ) {
		$templates = Plugin::elementor()->templates_manager->get_source( 'local' )->get_items();

		if ( ! empty( $types ) ) {
			return wp_filter_object_list( $templates, $types, 'or' );
		}

		foreach ( self::$exclude_document_types as $document_type ) {
			$templates = wp_filter_object_list( $templates, array( 'type' => $document_type ), 'not' );
		}

		return $templates;
	}

	public static function no_templates_message() {
		return '
		<div class="elementor-widget-cmsmasters-template-empty-templates">
			<div class="empty-templates-icon">' .
				'<i class="eicon-nerd" aria-hidden="true"></i>' .
			'</div>
			<div class="empty-templates-title">' .
				__( 'You have no saved Templates in your library yet.', 'cmsmasters-elementor' ) .
			'</div>
			<div class="empty-templates-footer">' .
				sprintf(
					/* translators: Library template widget 'Empty templates' text. %s: Link to documentation - Click Here */
					esc_html__( 'If you want to learn more about Elementor library please %s.', 'cmsmasters-elementor' ),
					sprintf(
						'<a class="empty-templates-footer-url" href="%2$s" target="_blank">%1$s</a>',
						__( 'Click Here', 'cmsmasters-elementor' ),
						'https://go.elementor.com/docs-library/'
					)
				) .
			'</div>
		</div>
		';
	}
}
