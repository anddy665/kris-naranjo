<?php
namespace PediatricClinicSpace\Core;

use PediatricClinicSpace\Admin\Admin;
use PediatricClinicSpace\Core\Traits\Singleton;
use PediatricClinicSpace\Core\Utils\File_Manager;
use PediatricClinicSpace\Core\Utils\Utils;
use PediatricClinicSpace\Kits\Module as Kits;
use PediatricClinicSpace\Modules\Modules;
use PediatricClinicSpace\TemplateFunctions\General_Elements;
use PediatricClinicSpace\TemplateFunctions\Main_Elements;
use PediatricClinicSpace\ThemeConfig\Theme_Config;
use PediatricClinicSpace\ThemeConfig\Theme_Plugins;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Core class for theme.
 *
 * Includes all theme modules.
 */
class Core {

	/**
	 * Instantiate singleton trait.
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @var array $_instances Array with instance of the class.
	 * @method object[] instance() Single instance of the class.
	 */
	use Singleton;

	/**
	 * Core constructor.
	 *
	 * Run modules for theme.
	 */
	public function __construct() {
		$this->actions();

		new Theme_Config();

		new Modules();

		new Admin();

		if ( did_action( 'elementor/loaded' ) ) {
			new Kits();
		}

		new Theme_Plugins();
	}

	/**
	 * Register actions and filters hooks.
	 */
	protected function actions() {
		add_filter( 'upload_dir', array( $this, 'add_ssl_to_upload_dir_url' ) );

		add_filter( 'elementor/core/responsive/get_stylesheet_templates', array( $this, 'get_responsive_stylesheet_templates' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );

		add_filter( 'cmsmasters_layout_filter', array( $this, 'filter_layout' ) );

		add_filter( 'body_class', array( $this, 'body_class_filter' ) );

		add_action( 'wp_head', array( $this, 'theme_pingback_header' ) );

		add_action( 'wp_head', array( $this, 'add_theme_color_meta_tag' ) );

		add_action( 'widgets_init', array( $this, 'register_widget_areas' ) );

		add_action( 'after_setup_theme', array( $this, 'register_translations' ) );

		add_action( 'after_setup_theme', array( $this, 'register_nav_menu_locations' ) );

		add_action( 'after_setup_theme', array( $this, 'register_theme_support' ) );

		add_filter( 'wp_page_menu_args', array( $this, 'page_menu_args' ) );

		add_filter( 'get_search_form', array( $this, 'filter_search_form' ) );

		add_filter( 'the_password_form', array( $this, 'filter_password_form' ) );

		add_filter( 'excerpt_more', function() {
			return '...';
		}, 10 );

		$this->register_image_sizes();
	}

	public function add_ssl_to_upload_dir_url( $args ) {
		$args['url'] = set_url_scheme( $args['url'] );
		$args['baseurl'] = set_url_scheme( $args['baseurl'] );

		return $args;
	}

	/**
	 * Extend stylesheets templates.
	 *
	 * Extend templates array with responsive stylesheets.
	 *
	 * Fired by `elementor/core/responsive/get_stylesheet_templates` Elementor filter hook.
	 *
	 * @param string[] $templates Templates array.
	 *
	 * @return string[] Filtered templates array.
	 */
	public function get_responsive_stylesheet_templates( $templates ) {
		$templates_paths = array(
			File_Manager::get_responsive_css_path() . 'frontend.css',
			File_Manager::get_responsive_css_path() . 'frontend.min.css',
			File_Manager::get_responsive_css_path() . 'frontend-rtl.css',
			File_Manager::get_responsive_css_path() . 'frontend-rtl.min.css',
		);

		$templates_paths = apply_filters( 'cmsmasters_stylesheet_templates_paths_filter', $templates_paths );

		foreach ( $templates_paths as $template_path ) {
			$file_name = 'pediatric-clinic-' . basename( $template_path );

			$templates[ $file_name ] = $template_path;
		}

		return $templates;
	}

	/**
	 * Enqueue frontend assets.
	 */
	public function enqueue_frontend_assets() {
		// Enqueue root style
		wp_enqueue_style(
			'pediatric-clinic-root-style',
			get_template_directory_uri() . '/style.css',
			array(),
			'1.0.0'
		);

		// Enqueue theme icons style
		wp_enqueue_style(
			'elementor-icons',
			File_Manager::get_css_assets_url( 'elementor-icons', 'assets/icons/css/' ),
			array(),
			'1.0.0',
			'screen'
		);

		wp_enqueue_style(
			'pediatric-clinic-frontend',
			File_Manager::get_css_template_assets_url( 'frontend', null, 'default', true ),
			'1.0.0',
			'screen'
		);

		// Enqueue frontend scripts.
		wp_enqueue_script(
			'pediatric-clinic-frontend',
			File_Manager::get_js_assets_url( 'frontend' ),
			array( 'jquery', 'imagesloaded' ),
			'1.0.0',
			true
		);

		$breakpoints = Utils::get_breakpoints();

		wp_localize_script( 'pediatric-clinic-frontend', 'cmsmasters_localize_vars', array(
			'tablet_breakpoint' => $breakpoints['tablet'],
			'tablet_max_breakpoint' => $breakpoints['tablet_max'],
			'mobile_breakpoint' => $breakpoints['mobile'],
			'mobile_max_breakpoint' => $breakpoints['mobile_max'],
			'assets_data' => array(
				'script' => array(
					'swiper' => array(
						'src' => File_Manager::get_js_assets_url( 'swiper', 'assets/lib/swiper/js/', true ),
					),
				),
			),
		) );

		// Enqueue comment reply script
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Filter main layout.
	 *
	 * @param string $layout Main layout.
	 *
	 * @return string
	 */
	public function filter_layout( $layout ) {
		if ( ! function_exists( 'cmsmasters_template_location_exists' ) ) {
			return $layout;
		}

		if ( is_singular() && cmsmasters_template_location_exists( 'singular', true ) ) {
			$layout = 'fullwidth';
		} elseif (
			(
				is_archive() ||
				is_home() ||
				is_search()
			) &&
			cmsmasters_template_location_exists( 'archive', true )
		) {
			$layout = 'fullwidth';
		}

		return $layout;
	}

	/**
	 * Filter body classes.
	 *
	 * @param array $classes Existing classes.
	 *
	 * @return array
	 */
	public function body_class_filter( $classes ) {
		$layout = Main_Elements::get_main_layout();

		$classes[] = 'cmsmasters-content-layout-' . esc_attr( $layout );

		return $classes;
	}

	/**
	 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
	 */
	public function theme_pingback_header() {
		if ( is_singular() && pings_open() ) {
			echo '<link rel="pingback" href="' . esc_url( get_bloginfo( 'pingback_url' ) ) . '">';
		}
	}

	/**
	 * Add Theme Color Meta Tag
	 */
	public function add_theme_color_meta_tag() {
		$mobile_theme_color = Utils::get_kit_option( 'cmsmasters_mobile_theme_color' );

		if ( ! empty( $mobile_theme_color ) ) {
			echo '<meta name="theme-color" content="' . esc_attr( $mobile_theme_color ) . '">';
		}
	}

	/**
	 * Register widget areas.
	 */
	public function register_widget_areas() {
		if ( ! function_exists( 'register_sidebars' ) ) {
			return;
		}

		register_sidebar(
			array(
				'name' => esc_html__( 'Sidebar', 'pediatric-clinic' ),
				'id' => 'sidebar_default',
				'description' => esc_html__( 'Widgets in this area will be shown in all left and right sidebars.', 'pediatric-clinic' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside>',
				'before_title' => '<h6 class="widgettitle">',
				'after_title' => '</h6>',
			)
		);

		register_sidebar(
			array(
				'name' => esc_html__( 'Archive Sidebar', 'pediatric-clinic' ),
				'id' => 'sidebar_archive',
				'description' => esc_html__( 'Widgets in this area will be shown in all left and right sidebars on archives pages.', 'pediatric-clinic' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside>',
				'before_title' => '<h6 class="widgettitle">',
				'after_title' => '</h6>',
			)
		);

		register_sidebar(
			array(
				'name' => esc_html__( 'Search Sidebar', 'pediatric-clinic' ),
				'id' => 'sidebar_search',
				'description' => esc_html__( 'Widgets in this area will be shown in all left and right sidebars on archives pages.', 'pediatric-clinic' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside>',
				'before_title' => '<h6 class="widgettitle">',
				'after_title' => '</h6>',
			)
		);

		register_sidebar(
			array(
				'name' => esc_html__( 'Footer 1', 'pediatric-clinic' ),
				'id' => 'footer-1',
				'description' => esc_html__( 'Widgets in this area will be shown in footer area.', 'pediatric-clinic' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside>',
				'before_title' => '<h6 class="widgettitle">',
				'after_title' => '</h6>',
			)
		);

		register_sidebar(
			array(
				'name' => esc_html__( 'Footer 2', 'pediatric-clinic' ),
				'id' => 'footer-2',
				'description' => esc_html__( 'Widgets in this area will be shown in footer area.', 'pediatric-clinic' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside>',
				'before_title' => '<h6 class="widgettitle">',
				'after_title' => '</h6>',
			)
		);

		register_sidebar(
			array(
				'name' => esc_html__( 'Footer 3', 'pediatric-clinic' ),
				'id' => 'footer-3',
				'description' => esc_html__( 'Widgets in this area will be shown in footer area.', 'pediatric-clinic' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside>',
				'before_title' => '<h6 class="widgettitle">',
				'after_title' => '</h6>',
			)
		);

		register_sidebar(
			array(
				'name' => esc_html__( 'Footer 4', 'pediatric-clinic' ),
				'id' => 'footer-4',
				'description' => esc_html__( 'Widgets in this area will be shown in footer area.', 'pediatric-clinic' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside>',
				'before_title' => '<h6 class="widgettitle">',
				'after_title' => '</h6>',
			)
		);

		register_sidebar(
			array(
				'name' => esc_html__( 'Footer 5', 'pediatric-clinic' ),
				'id' => 'footer-5',
				'description' => esc_html__( 'Widgets in this area will be shown in footer area.', 'pediatric-clinic' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget' => '</aside>',
				'before_title' => '<h6 class="widgettitle">',
				'after_title' => '</h6>',
			)
		);
	}

	/**
	 * Register theme translations.
	 */
	public function register_translations() {
		load_theme_textdomain( 'pediatric-clinic', get_template_directory() . '/theme-config/languages' );
	}

	/**
	 * Register navigation menu locations.
	 */
	public function register_nav_menu_locations() {
		register_nav_menus(
			array(
				'header_top_nav' => esc_html__( 'Header Top Navigation', 'pediatric-clinic' ),
				'header_mid_nav' => esc_html__( 'Header Middle Navigation', 'pediatric-clinic' ),
				'header_bot_nav' => esc_html__( 'Header Bottom Navigation', 'pediatric-clinic' ),
				'footer_nav' => esc_html__( 'Footer Navigation', 'pediatric-clinic' ),
			)
		);
	}

	/**
	 * Register support for various WordPress features.
	 */
	public function register_theme_support() {
		// Add post formats
		add_theme_support( 'post-formats', array(
			'image',
			'gallery',
			'video',
			'audio',
		) );

		// Enable support for Post Thumbnails on posts and pages.
		add_theme_support( 'post-thumbnails' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Switch default core markup for search form, comment form, and comments to output valid HTML5.
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Set the content width in pixels, based on the theme's design and stylesheet
		if ( ! isset( $content_width ) ) {
			$content_width = 1160;
		}
	}

	/**
	 * Register showing home page on default WordPress pages menu.
	 *
	 * @param array $args Menu arguments.
	 *
	 * @return array Changed menu arguments.
	 */
	public function page_menu_args( $args ) {
		$args['show_home'] = true;

		return $args;
	}

	/**
	 * Register image sizes.
	 */
	public function register_image_sizes() {
		$image_sizes = Utils::get_theme_option( 'image_sizes', array() );

		if ( ! is_array( $image_sizes ) || empty( $image_sizes ) ) {
			return false;
		}

		foreach ( $image_sizes as $key => $args ) {
			if (
				! isset( $args['width'] ) ||
				! is_numeric( $args['width'] ) ||
				! isset( $args['height'] ) ||
				! is_numeric( $args['height'] ) ||
				! isset( $args['crop'] ) ||
				! is_numeric( $args['crop'] )
			) {
				continue;
			}

			$crop = ( '1' === $args['crop'] ? true : false );

			add_image_size(
				'cmsmasters-thumb-' . $key,
				$args['width'],
				$args['height'],
				$crop
			);
		}
	}

	/**
	 * Filters the HTML output of the search form.
	 *
	 * @return string Filtered HTML.
	 */
	public function filter_search_form() {
		return General_Elements::get_search_form();
	}

	/**
	 * Filters the HTML output for the protected post password form.
	 *
	 * @return string Filtered HTML.
	 */
	public function filter_password_form() {
		$output = '<p>' . esc_html__( 'This content is password protected. To view it please enter your password below:', 'pediatric-clinic' ) . '</p>' .
		'<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form cmsmasters-post-password-form" method="post">' .
			'<input name="post_password" type="password" size="20" placeholder="' . esc_attr__( 'Password', 'pediatric-clinic' ) . '" />' .
			'<input type="submit" name="Submit" value="' . esc_attr_x( 'Enter', 'post password form', 'pediatric-clinic' ) . '" />' .
		'</form>';

		return $output;
	}

}
