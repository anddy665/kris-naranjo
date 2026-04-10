<?php
namespace CmsmastersElementor;

use CmsmastersElementor\Base\Base_Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon modules manager.
 *
 * Addon modules manager handler class is responsible for registering and
 * managing plugin modules.
 *
 * @since 1.0.0
 * @final
 */
final class Modules_Manager {

	/**
	 * Registered modules.
	 *
	 * Holds the list of all the registered modules.
	 *
	 * @since 1.0.0
	 *
	 * @var Base_Module[]
	 */
	private $modules = array();

	/**
	 * Initializing the Elementor modules manager.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		foreach ( $this->get_modules_names() as $module_name ) {
			$class_name = str_replace( '-', '', ucwords( $module_name, '-' ) );

			/** @var Base_Module $class */
			$class = __NAMESPACE__ . '\\Modules\\' . $class_name . '\\Module';

			if ( $class::is_active() ) {
				$this->modules[ $module_name ] = $class::instance();
			}
		}
	}

	/**
	 * Retrieve the modules names.
	 *
	 * @since 1.0.0
	 * @since 1.3.3 Add WPML module.
	 * @since 1.4.0 Add Weather module.
	 *
	 * @return string[] Modules names.
	 */
	public function get_modules_names() {
		return array(
			'ajax-widget',
			'slider',
			'animation',
			'entrance-animation',

			// Modules with Documents
			'template-sections',
			'template-pages',

			// Modules without widgets
			'wordpress',
			'settings',
			'web-fonts',
			'icon-fonts',
			'effects',
			'sticky',
			'additional-settings',
			'ribbon',
			'section-for-header',
			'popup',

			// Modules with Widgets
			'advanced-title',
			'text-editor',
			'highlight-title',
			'authorization-form',
			'authorization-links',
			'blog',
			'button',
			'share-buttons',
			'social',
			'library-template',
			'meta-data',
			'social-counter',
			'sitemap', // @since 1.3.0
			'sender',
			'weather',
			'give-wp',
			'google-maps',
			'gallery',
			'contact-form',
			'testimonials',
			'featured-box',
			'image-accordion',
			'image-scroll',
			'media',
			'mailchimp',
			'marquee',
			'animated-text',
			'table-of-contents',
			'tabs',
			'toggles',
			'tribe-events',
			'timetable',
			'woocommerce',
			'before-after',
			'progress-tracker',
			'countdown',
			'hotspot',
			'circle-progress-bar',
			'mode-switcher',

			// Modules that apply filters and actions
			'template-documents',
			'template-preview',
			'template-locations',
			'wpml',
			'polylang',
		);
	}

	/**
	 * Retrieve a specific module or all the registered modules.
	 *
	 * @since 1.0.0
	 *
	 * @param string $module_name Module name.
	 *
	 * @return mixed Base_Module|Base_Module's names
	 */
	public function get_modules( $module_name ) {
		if ( $module_name ) {
			if ( isset( $this->modules[ $module_name ] ) ) {
				return $this->modules[ $module_name ];
			}

			return null;
		}

		return $this->modules;
	}

}
