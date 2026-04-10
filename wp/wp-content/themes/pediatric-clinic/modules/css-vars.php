<?php
namespace PediatricClinicSpace\Modules;

use PediatricClinicSpace\Core\Utils\File_Manager;
use PediatricClinicSpace\Core\Utils\Utils;

use Elementor\Core\Files\CSS\Post as Post_CSS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * CSS_Vars module.
 *
 * Main class for css vars module.
 */
class CSS_Vars {

	/**
	 * Active kit.
	 */
	private $active_kit = '';

	/**
	 * CSS_Vars module constructor.
	 */
	public function __construct() {
		$this->active_kit = Utils::get_active_kit();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_default_vars' ), 9 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_default_vars' ), 9 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_kits' ), 11 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_kits' ), 11 );
	}

	/**
	 * Enqueue default vars.
	 */
	public function enqueue_default_vars() {
		wp_enqueue_style(
			'cmsmasters-default-vars',
			File_Manager::get_css_assets_url( 'default-vars', null, 'default', false, 'theme-config/' ),
			array(),
			'1.0.0',
			'all'
		);
	}

	/**
	 * Enqueue kits.
	 */
	public function enqueue_kits() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		wp_enqueue_style(
			"elementor-post-{$this->active_kit}",
			Utils::get_upload_dir_parameter( 'baseurl', '/elementor/css/' ) . 'post-' . $this->active_kit . '.css',
			array(),
			'1.0.0',
			'all'
		);
	}

}
