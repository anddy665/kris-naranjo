<?php
namespace PediatricClinicSpace\Kits\Documents;

use PediatricClinicSpace\Kits\Module as KitsModule;
use PediatricClinicSpace\Kits\Settings\Base\Base_Section;
use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Core\Kits\Documents\Kit as BaseKit;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Theme style class.
 *
 * Adds settings for theme style.
 */
class Kit extends BaseKit {

	/**
	 * Addon kit document settings tabs.
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * Addon kit document class constructor.
	 *
	 * Initializing the Addon kit document initial file class.
	 *
	 * @param array $data Document data.
	 */
	public function __construct( $data = array() ) {
		parent::__construct( $data );

		$this->init_settings_tabs();

		$this->init_filters();
	}

	/**
	 * Change css wrapper selector for kit selectors.
	 */
	public function get_css_wrapper_selector() {
		return ':root';
	}

	/**
	 * Init settings tabs.
	 *
	 * Initialize Addon kit document settings tabs toggles.
	 */
	protected function init_settings_tabs() {
		$sections = $this->get_sections();

		foreach ( $sections as $section => $instance ) {
			if ( ! class_exists( $instance ) ) {
				continue;
			}

			$this->add_settings_tab( $section, $instance );
		}
	}

	/**
	 * Get sections.
	 *
	 * Get kit document settings sections.
	 */
	protected function get_sections() {
		$locations = get_option( 'cmsmasters_elementor_documents_locations', array() );

		$hide_header = false;
		$hide_archive = false;
		$hide_search = false;
		$hide_single = false;
		$hide_footer = false;

		foreach ( $locations as $key => $templates ) {
			if ( 'cmsmasters_header' === $key ) {
				$hide_header = $this->check_sitewide_location( $templates, 'include/general' );
			}

			if ( 'cmsmasters_archive' === $key ) {
				$hide_archive = $this->check_sitewide_location( $templates, 'include/archive' );

				$hide_search = $this->check_sitewide_location( $templates, 'include/archive/search' );
			}

			if ( 'cmsmasters_singular' === $key ) {
				$hide_single = $this->check_sitewide_location( $templates, 'include/singular' );
			}

			if ( 'cmsmasters_footer' === $key ) {
				$hide_footer = $this->check_sitewide_location( $templates, 'include/general' );
			}
		}

		$sections = array(
			'general',
			'elements',
		);

		if ( false === $hide_header ) {
			$sections = array_merge( $sections, array(
				'header-top',
				'header-mid',
				'header-bot',
				'heading',
			) );
		}

		$sections[] = 'main';

		if ( false === $hide_archive ) {
			$sections[] = 'archive';
		}

		if ( false === $hide_search ) {
			$sections[] = 'search';
		}

		if ( false === $hide_single ) {
			$sections[] = 'single';
		}

		$sections[] = 'sidebar-widgets';

		if ( false === $hide_footer ) {
			$sections = array_merge( $sections, array(
				'footer-widgets',
				'footer',
			) );
		}

		$sections[] = 'page-preloader';

		if ( class_exists( 'Cmsmasters_Elementor_Addon' ) ) {
			$sections = array_merge( $sections, array(
				'lazyload-widget',
				'mode-switcher',
			) );
		}

		foreach ( $sections as $section ) {
			$name = str_replace( '-', '', ucwords( $section, '-' ) );
			$instance = KitsModule::KIT_NAMESPACE . "\\Settings\\{$name}\\Section";

			$sections[ $section ] = $instance;
		}

		return apply_filters( 'cmsmasters_theme_kit_sections', $sections );
	}

	/**
	 * Check sitewide location.
	 *
	 * @param array $templates Templates.
	 * @param string $sitewide_location Sitewide location.
	 *
	 * @return bool
	 */
	public function check_sitewide_location( $templates = array(), $sitewide_location = 'include/general' ) {
		$result = false;

		foreach ( $templates as $template_id => $template_args ) {
			if ( is_array( $template_args ) && in_array( $sitewide_location, $template_args, true ) ) {
				$result = true;

				foreach ( $template_args as $template_location ) {
					if ( false !== strpos( $template_location, 'exclude/' ) ) {
						$result = false;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Add settings tab.
	 *
	 * Add new Addon kit document settings tab toggles.
	 *
	 * @param string $id Addon kit document tab id.
	 * @param Base_Section $instance Addon kit document tab class instance.
	 */
	public function add_settings_tab( $id, $instance ) {
		$name = "cmsmasters-theme-{$id}";

		if ( ! isset( $this->settings[ $name ] ) ) {
			$this->settings[ $name ] = array();
		} elseif ( isset( $this->settings[ $name ]['tab'] ) ) {
			return;
		}

		/** @var Base_Section $section */
		$section = new $instance();

		$this->settings[ $name ]['tab'] = $section;
		$this->settings[ $name ]['toggles'] = array();

		foreach ( $section->get_toggles_config() as $toggle_id => $toggle_instance ) {
			$this->settings[ $name ]['toggles'][ $toggle_id ] = new $toggle_instance( $this, $name );
		}
	}

	/**
	 * Save settings tab.
	 *
	 * Save Addon kit document settings tab toggles controls.
	 *
	 * @param array $data Kit document data.
	 *
	 * @return array Addon kit document controls data.
	 */
	public function save( $data ) {
		$saved = parent::save( $data );

		if ( ! $saved ) {
			return $saved;
		}

		// Should set is_saving to true, to avoid infinite loop when updating
		// settings like: 'site_name" or "site_description".
		$this->set_is_saving( true );

		foreach ( $this->settings as $settings_tab ) {
			if ( empty( $settings_tab ) ) {
				continue;
			}

			/** @var Settings_Tab_Base $setting_tab */
			$toggles = $settings_tab['toggles'];

			foreach ( $toggles as $toggle ) {
				$toggle->on_save( $data );
			}
		}

		$this->set_is_saving( false );

		// When deleting a global color or typo, the css variable still exists in the frontend
		// but without any value and it makes the element to be un styled even if there is a default style for the base element,
		// for that reason this method removes css files of the entire site.
		Plugin::instance()->files_manager->clear_cache();

		return $saved;
	}

	/**
	 * Register controls.
	 *
	 * Register Addon kit document settings tab toggle controls.
	 */
	protected function register_controls() {
		/**
		 * Before register Elementor kit controls section.
		 *
		 * Fires before default Elementor kit document controls
		 * section are registered.
		 *
		 * @param Document $this Addon kit document.
		 */
		do_action( 'cmsmasters_elementor/documents/kit/before_register_elementor_kit_controls', $this );

		parent::register_controls();

		/**
		 * Before register Addon kit control sections.
		 *
		 * Fires before default Elementor kit document Addon
		 * control sections are registered.
		 *
		 * @param Document $this Addon kit document.
		 */
		do_action( 'cmsmasters_elementor/documents/kit/before_register_addon_kit_controls', $this );

		foreach ( $this->settings as $settings_tab ) {
			if ( empty( $settings_tab ) ) {
				continue;
			}

			/** @var Settings_Tab_Base $setting_tab */
			$toggles = $settings_tab['toggles'];

			foreach ( $toggles as $toggle ) {
				$toggle->register_controls();
			}
		}

		/**
		 * After register Addon kit control sections.
		 *
		 * Fires after default Elementor kit document Addon kit
		 * control sections are registered.
		 *
		 * @param Document $this Addon kit document.
		 */
		do_action( 'cmsmasters_elementor/documents/kit/after_register_addon_kit_controls', $this );
	}

	/**
	 * Get initial config.
	 *
	 * Retrieve Elementor editor initial config.
	 */
	protected function get_initial_config() {
		$config = parent::get_initial_config();

		foreach ( $this->settings as $setting ) {
			/** @var Base_Section $setting_tab */
			$setting_tab = $setting['tab'];

			$id = $setting_tab::get_id();

			$config['tabs'][ $id ] = array(
				'title' => $setting_tab::get_title(),
				'icon' => $setting_tab::get_icon(),
				'group' => $setting_tab::get_group(),
				'helpUrl' => $setting_tab::get_help_url(),
				'additionalContent' => $setting_tab->get_additional_content(),
			);
		}

		return $config;
	}

	/**
	 * Add filters initialization.
	 *
	 * Register filters for the Addon kit document.
	 */
	protected function init_filters() {
		// Editor
		add_filter( 'elementor/editor/localize_settings', array( $this, 'localize_settings' ) );
	}

	/**
	 * Localize settings.
	 *
	 * Add new localized settings for the kit document.
	 *
	 * Fired by `elementor/editor/localize_settings` Elementor filter.
	 *
	 * @param array $settings Localized settings.
	 *
	 * @return array Localized settings.
	 */
	public function localize_settings( $settings ) {
		$settings = array_replace_recursive( $settings, array(
			'i18n' => array(
				'cmsmasters_theme' => esc_html__( 'Theme Settings', 'pediatric-clinic' ),
				'design_system' => esc_html__( 'Design System', 'pediatric-clinic' ),
				'theme_style' => esc_html__( 'Theme Style', 'pediatric-clinic' ),
				'settings' => esc_html__( 'Settings', 'pediatric-clinic' ),
				'additional_settings' => esc_html__( 'Additional Settings', 'pediatric-clinic' ),
			),
		) );

		return $settings;
	}

}
