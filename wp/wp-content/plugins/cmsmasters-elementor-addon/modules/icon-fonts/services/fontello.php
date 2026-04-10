<?php
namespace CmsmastersElementor\Modules\IconFonts\Services;

use CmsmastersElementor\Modules\IconFonts\Services\Base\Base_Service;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Fontello extends Base_Service {

	protected $archive_files_to_upload = array(
		'font/',
		'config.json',
	);

	protected $archive_files_to_ignore = array(
		'demo.html',
		'LICENSE.txt',
		'README.txt',
	);

	protected $data_file = 'config.json';

	protected $stylesheet_file = '';

	protected $config;

	public static function get_type() {
		return __( 'Fontello', 'cmsmasters-elementor' );
	}

	public static function get_link() {
		return 'http://fontello.com/';
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

		$this->stylesheet_file = sprintf( '%s.css', $this->get_name() );
		$this->archive_files_to_ignore[] = sprintf( 'css/[^%s]', $this->stylesheet_file );

		$this->remove_fontello_styling( $wp_filesystem );
	}

	private function remove_fontello_styling( $wp_filesystem ) {
		$stylesheet = sprintf( '%1$s/css/%2$s', untrailingslashit( $this->directory ), $this->stylesheet_file );

		$styles = $wp_filesystem->get_contents( $stylesheet );

		$styles = str_replace(
			array( 'margin-left: .2em;', 'margin-right: .2em;' ),
			array( '', '' ),
			$styles
		);

		$wp_filesystem->put_contents( $stylesheet, $styles );
	}

	public function get_name() {
		$name = isset( $this->config->name ) ? $this->config->name : false; // missing name
		
		if ( empty( $name ) ) {
			$name = 'fontello';
		}

		return $name;
	}

	protected function extract_icons_list() {
		if ( ! isset( $this->config->glyphs ) ) {
			return false; // missing icons list
		}

		$icons = array();

		foreach ( $this->config->glyphs as $icon ) {
			$icons[] = $icon->css;
		}

		return $icons;
	}

	protected function get_stylesheet() {
		if ( ! $this->get_name() ) {
			return false; // missing name
		}

		return $this->get_url( '/css/' . $this->stylesheet_file );
	}

	protected function get_prefix() {
		return isset( $this->config->css_prefix_text ) ?
			str_replace( '.', '', $this->config->css_prefix_text ) :
			false; // missing css_prefix_text
	}

}
