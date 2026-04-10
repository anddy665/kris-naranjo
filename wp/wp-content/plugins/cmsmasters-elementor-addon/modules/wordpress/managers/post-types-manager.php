<?php
namespace CmsmastersElementor\Modules\Wordpress\Managers;

use CmsmastersElementor\Base\Base_Actions;
use CmsmastersElementor\Modules\Settings\Settings_Page;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Post_Types_Manager extends Base_Actions {

	private $modules_list = array();

	public function init( $module = null ) {
		if ( ! $module ) {
			return;
		}

		$this->modules_list[] = $module;
	}

	protected function init_actions() {
		// Common
		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'init', array( $this, 'register_cpt_taxonomy' ) );

		// Admin
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'register_cpt_menu' ), 50 );
		}
	}

	protected function init_filters() {
		// Common
		add_filter( 'elementor/finder/categories', array( $this, 'add_finder_cpt_items' ) );
	}

	/**
	 * Register custom post type.
	 */
	public function register_cpt() {
		foreach ( $this->modules_list as $module ) {
			if ( ! $module->cpt_labels ) {
				return;
			}

			$default_args = array(
				'labels' => $module->cpt_labels,
				'public' => false,
				'rewrite' => false,
				'show_ui' => true,
				'show_in_menu' => false,
				'show_in_nav_menus' => false,
				'exclude_from_search' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'supports' => array( 'title' ),
				'can_export' => false,
			);

			$module_args = Utils::get_if_isset( $module, 'post_type_args', array() );
			$args = array_merge( $default_args, $module_args );

			register_post_type( $module::CPT, $args );
		}
	}

	/**
	 * Register custom post type taxonomy.
	 */
	public function register_cpt_taxonomy() {
		foreach ( $this->modules_list as $module ) {
			$module_class = get_class( $module );

			if (
				! $module->cpt_labels ||
				! defined( "{$module_class}::CPT_TAXONOMY" ) ||
				! $module->cpt_tax_labels
			) {
				return;
			}

			$args = array(
				'labels' => $module->cpt_tax_labels,
				'hierarchical' => false,
				'show_ui' => true,
				'show_in_nav_menus' => false,
				'query_var' => is_admin(),
				'rewrite' => false,
				'public' => false,
			);

			register_taxonomy( $module::CPT_TAXONOMY, $module::CPT, $args );
		}
	}

	/**
	 * Add custom elementor admin settings submenu.
	 */
	public function register_cpt_menu() {
		foreach ( $this->modules_list as $module ) {
			if ( ! $module->cpt_labels || ! $module->cpt_title ) {
				return;
			}

			$capability = 'manage_options';
			$module_class = get_class( $module );

			if ( defined( "{$module_class}::CAPABILITY" ) ) {
				$capability = $module::CAPABILITY;
			}

			add_submenu_page(
				Settings_Page::PAGE_ID,
				sprintf( 'CMSMasters %s', $module->cpt_title ),
				$module->cpt_title,
				$capability,
				sprintf( 'edit.php?post_type=%s', $module::CPT )
			);
		}
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public function add_finder_cpt_items( $categories ) {
		foreach ( $this->modules_list as $module ) {
			if ( ! $module->cpt_labels || ! $module->cpt_title ) {
				return;
			}

			$finder_icon = Utils::get_if_isset( $module, 'finder_icon', 'favorite' );
			$finder_keywords = Utils::get_if_isset( $module, 'finder_keywords', array() );
			$keywords = array_merge( array( 'cmsmasters' ), $finder_keywords );

			$categories['settings']['items'][ $module::CPT ] = array(
				'title' => $module->cpt_title,
				'icon' => $finder_icon,
				'url' => sprintf( 'edit.php?post_type=%s', $module::CPT ),
				'keywords' => $keywords,
			);

			return $categories;
		}
	}

}
