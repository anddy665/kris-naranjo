<?php
namespace PediatricClinicSpace\Modules;

use PediatricClinicSpace\Core\Utils\File_Manager;
use PediatricClinicSpace\TemplateFunctions\Main_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Gutenberg modules.
 *
 * Main class for gutenberg modules.
 */
class Gutenberg {

	/**
	 * Color palette.
	 */
	public $palette = array();

	/**
	 * Color palette styles.
	 */
	public $palette_styles = '';

	/**
	 * Gutenberg modules constructor.
	 *
	 * Run modules for gutenberg.
	 */
	public function __construct() {
		$this->set_color_palette_styles();

		add_action( 'after_setup_theme', array( $this, 'theme_support' ) );

		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );

		add_filter( 'cmsmasters_stylesheet_templates_paths_filter', array( $this, 'stylesheet_templates_paths_filter' ) );

		add_filter( 'admin_body_class', array( $this, 'editor_body_class' ) );

		add_filter( 'block_editor_settings_all', array( $this, 'filter_block_editor_settings' ) );
	}

	/**
	 * Set color palette.
	 */
	public function set_color_palette_styles() {
		$colors = array(
			'cmsmasters_colors_text',
			'cmsmasters_colors_link',
			'cmsmasters_colors_hover',
			'cmsmasters_colors_heading',
			'cmsmasters_colors_bg',
			'cmsmasters_colors_alternate',
			'cmsmasters_colors_bd',
		);

		foreach ( $colors as $key ) {
			$key = str_replace( '_', '-', $key );

			$this->palette[] = array(
				'slug' => $key,
				'color' => "var(--{$key})",
			);

			$this->palette_styles .= "
			.has-{$key}-color {
				color: var(--{$key}) !important;
			}

			.has-{$key}-background-color {
				background-color: var(--{$key}) !important;
			}
			";
		}
	}

	/**
	 * Add theme supports.
	 */
	public function theme_support() {
		if ( is_array( $this->palette ) && ! empty( $this->palette ) ) {
			add_theme_support( 'editor-color-palette', $this->palette );
		}

		add_theme_support( 'align-wide' );

		add_theme_support( 'wp-block-styles' );

		add_theme_support( 'responsive-embeds' );
	}

	/**
	 * Filter for block editor settings.
	 *
	 * @param array $editor_settings Editor settings.
	 *
	 * @return array filtered editor settings.
	 */
	public function filter_block_editor_settings( $editor_settings ) {
		$editor_settings['styles'] = array();

		return $editor_settings;
	}

	/**
	 * Enqueue assets to gutenberg editor.
	 */
	public function enqueue_editor_assets() {
		// Styles
		wp_deregister_style( 'wp-block-library-theme' );
		wp_register_style( 'wp-block-library-theme', '', array(), '1.0.0' );
		wp_deregister_style( 'wp-block-library' );
		wp_register_style( 'wp-block-library', '', array(), '1.0.0' );

		$styles_name = 'gutenberg-wp-editor';

		if ( in_array( 'gutenberg/gutenberg.php', get_option( 'active_plugins' ), true ) ) {
			wp_deregister_style( 'wp-edit-blocks' );
			wp_register_style( 'wp-edit-blocks', '', array(), '1.0.0' );

			$styles_name = 'gutenberg-plugin-editor';
		}

		wp_enqueue_style(
			'pediatric-clinic-gutenberg',
			File_Manager::get_css_assets_url( $styles_name, null, 'default', true ),
			false,
			'1.0.0',
			'all'
		);

		wp_add_inline_style( 'pediatric-clinic-gutenberg', $this->palette_styles );

		$script_deps = array( 'wp-blocks', 'wp-element', 'wp-dom-ready' );

		$screen = get_current_screen();

		if ( 'customize' === $screen->base ) {
			$script_deps[] = 'wp-customize-widgets';
		} elseif ( 'widgets' === $screen->base ) {
			$script_deps[] = 'wp-edit-widgets';
		} else {
			$script_deps[] = 'wp-edit-post';
		}

		// Scripts
		wp_enqueue_script(
			'pediatric-clinic-gutenberg',
			File_Manager::get_js_assets_url( 'gutenberg' ),
			$script_deps,
			'1.0.0',
			false
		);
	}

	/**
	 * Enqueue assets to frontend.
	 */
	public function enqueue_frontend_assets() {
		// Styles
		wp_deregister_style( 'wp-block-library-theme' );
		wp_register_style( 'wp-block-library-theme', '', array(), '1.0.0' );
		wp_deregister_style( 'wp-block-library' );
		wp_register_style( 'wp-block-library', '', array(), '1.0.0' );

		$styles_name = 'gutenberg-wp-frontend';

		if ( in_array( 'gutenberg/gutenberg.php', get_option( 'active_plugins' ), true ) ) {
			$styles_name = 'gutenberg-plugin-frontend';
		}

		wp_enqueue_style(
			'pediatric-clinic-gutenberg',
			File_Manager::get_css_template_assets_url( $styles_name, null, 'default', true ),
			array( 'pediatric-clinic-frontend' ),
			'1.0.0',
			'all'
		);

		wp_add_inline_style( 'pediatric-clinic-gutenberg', $this->palette_styles );
	}

	/**
	 * Stylesheet templates paths filter.
	 *
	 * @param array $templates_paths Templates paths.
	 *
	 * @return array Filtered templates paths.
	 */
	public function stylesheet_templates_paths_filter( $templates_paths ) {
		return array_merge( $templates_paths, array(
			File_Manager::get_responsive_css_path() . 'gutenberg-plugin-frontend.css',
			File_Manager::get_responsive_css_path() . 'gutenberg-plugin-frontend.min.css',
			File_Manager::get_responsive_css_path() . 'gutenberg-plugin-frontend-rtl.css',
			File_Manager::get_responsive_css_path() . 'gutenberg-plugin-frontend-rtl.min.css',
			File_Manager::get_responsive_css_path() . 'gutenberg-wp-frontend.css',
			File_Manager::get_responsive_css_path() . 'gutenberg-wp-frontend.min.css',
			File_Manager::get_responsive_css_path() . 'gutenberg-wp-frontend-rtl.css',
			File_Manager::get_responsive_css_path() . 'gutenberg-wp-frontend-rtl.min.css',
		) );
	}

	/**
	 * Filter for gutenberg editor container classes
	 */
	public function editor_body_class( $classes ) {
		$page_id = get_the_ID();

		if ( $page_id ) {
			$layout = Main_Elements::get_main_layout( $page_id );

			$classes .= ' cmsmasters-def-layout-' . $layout . ' cmsmasters-layout-' . $layout;
		}

		if ( 'attachment' !== get_post_type() && 'page' !== get_post_type() ) {
			$classes .= ' cmsmasters-is-single';
		}

		return $classes;
	}

}
