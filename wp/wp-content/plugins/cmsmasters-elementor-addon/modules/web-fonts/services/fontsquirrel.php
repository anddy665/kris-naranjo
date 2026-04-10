<?php
namespace CmsmastersElementor\Modules\WebFonts\Services;

use CmsmastersElementor\Modules\WebFonts\Services\Base\Base_Service;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Fontsquirrel extends Base_Service {

	protected $archive_files_to_ignore = array(
		'specimen_files/',
	);

	protected $data_file = 'generator_config.txt';

	private $config;

	public static function get_type() {
		return __( 'Font Squirrel', 'cmsmasters-elementor' );
	}

	public static function get_link() {
		return 'https://www.fontsquirrel.com/tools/webfont-generator';
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
	protected function read_stylesheet_text( $wp_filesystem ) {
		$this->config = json_decode(
			Utils::strip_comments( $wp_filesystem->get_contents( $this->directory . $this->data_file ), '#' )
		);

		if ( ! isset( $this->config->css_stylesheet ) ) {
			return false; // missing config stylesheet
		}

		$this->stylesheet_file = $this->config->css_stylesheet;

		$this->stylesheet = $wp_filesystem->get_contents( $this->directory . $this->stylesheet_file );
	}

	protected function prepare() {
		if ( empty( $this->settings['font_faces'] ) ) {
			return false; // missing font_faces setting
		}

		parent::prepare();

		$needle = '.';

		if (
			isset( $this->config->mode ) &&
			'basic' !== $this->config->mode &&
			isset( $this->config->filename_suffix ) &&
			! empty( $this->config->filename_suffix )
		) {
			$needle = $this->config->filename_suffix;
		}

		foreach ( $this->settings['font_faces'] as $font_face ) {
			$this->archive_files_to_ignore[] = sprintf(
				'%s-demo.html',
				strstr( end( $font_face['src']['url'] ), $needle, true )
			);
		}
	}

}
