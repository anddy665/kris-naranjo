<?php
namespace PediatricClinicSpace\Kits\Classes;

use Elementor\Frontend as FrontendBase;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon kits frontend class.
 *
 * Kits frontend handler class is responsible for enqueue kit fonts and
 * icons for Elementor frontend and editor.
 */
class Frontend extends FrontendBase {

	/**
	 * Active kit.
	 */
	private $active_kit = '';

	/**
	 * Frontend constructor.
	 *
	 * Enqueue kit fonts and icons to frontend and editor.
	 */
	public function __construct() {
		$this->active_kit = get_option( 'elementor_active_kit', '' );

		if ( ! is_numeric( $this->active_kit ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_assets' ) );
	}

	public function block_editor_assets() {
		$meta = get_post_meta( $this->active_kit, '_elementor_css', true );

		if ( ! empty( $meta['fonts'] ) ) {
			foreach ( $meta['fonts'] as $font ) {
				$this->enqueue_font( $font );
			}
		}

		$this->register_styles();
		$this->enqueue_styles();
		$this->print_fonts_links();
	}

}
