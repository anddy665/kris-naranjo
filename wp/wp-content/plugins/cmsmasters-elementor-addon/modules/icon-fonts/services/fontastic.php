<?php
namespace CmsmastersElementor\Modules\IconFonts\Services;

use CmsmastersElementor\Modules\IconFonts\Services\Base\Base_Service;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Fontastic extends Base_Service {

	protected $archive_files_to_upload = array(
		'fonts/',
		'icons-reference.html',
		'styles.css',
	);

	protected $stylesheet_file = 'styles.css';

	protected $data_file = 'icons-reference.html'; // TODO: check usage, and try to replace with styles

	protected $config;

	protected $settings = array();

	public static function get_type() {
		return __( 'Fontastic', 'cmsmasters-elementor' );
	}

	public static function get_link() {
		return 'http://app.fontastic.me/';
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
		$this->config = $wp_filesystem->get_contents( $this->directory . $this->stylesheet_file );
	}

	public function get_name() {
		if ( ! isset( $this->settings['name'] ) ) {
			preg_match_all( '/font-family: "(.*)"/', $this->config, $name );

			if ( ! isset( $name[1][0] ) ) {
				return false; // missing name
			}

			$this->settings['name'] = $name[1][0];
		}

		return $this->settings['name'];
	}

	protected function extract_icons_list() {
		$pattern = sprintf( '/\.%s(.*)\:before\s\{/', $this->get_prefix() ); // TODO: check if it works

		preg_match_all( $pattern, $this->config, $icons_matches );

		if ( empty( $icons_matches[1] ) ) {
			return false; // missing icons list
		}

		$icons = array();

		foreach ( $icons_matches[1] as $icon ) {
			$icons[] = $icon;
		}

		return $icons;
	}

	protected function get_prefix() {
		if ( ! isset( $this->settings['prefix'] ) ) {
			preg_match_all( '/class\^="(.*)?"/', $this->config, $prefix );

			if ( ! isset( $prefix[1][0] ) ) {
				return false; // missing css_prefix_text
			}

			$this->settings['prefix'] = $prefix[1][0];
		}

		return $this->settings['prefix'];
	}

}
