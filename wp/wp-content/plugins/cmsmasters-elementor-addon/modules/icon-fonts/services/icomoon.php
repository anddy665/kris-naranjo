<?php
namespace CmsmastersElementor\Modules\IconFonts\Services;

use CmsmastersElementor\Modules\IconFonts\Services\Base\Base_Service;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Icomoon extends Base_Service {

	protected $archive_files_to_upload = array(
		'fonts/',
		'selection.json',
		'style.css',
	);

	protected $archive_files_to_ignore = array(
		'demo-files/',
		'demo.html',
		'Read Me.txt',
	);

	protected $stylesheet_file = 'style.css';

	protected $data_file = 'selection.json';

	protected $config;

	public static function get_type() {
		return __( 'IcoMoon', 'cmsmasters-elementor' );
	}

	public static function get_link() {
		return 'https://icomoon.io/app/';
	}

	/**
	 * Undocumented function
	 *
	 * Description.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Filesystem_Base $wp_filesystem
	 */
	protected function prepare( $wp_filesystem ) {
		$this->config = json_decode( $wp_filesystem->get_contents( $this->directory . $this->data_file ) );
	}

	public function get_name() {
		return isset( $this->config->metadata->name ) ?
			$this->config->metadata->name :
			false; // missing name
	}

	protected function extract_icons_list() {
		if ( ! isset( $this->config->icons ) ) {
			return false; // missing icons list
		}

		$icons = array();

		foreach ( $this->config->icons as $icon ) {
			$icons[] = $icon->properties->name;
		}

		return $icons;
	}

	protected function get_prefix() {
		return isset( $this->config->preferences->fontPref->prefix ) ?
			$this->config->preferences->fontPref->prefix :
			false; // missing css_prefix_text
	}

}
