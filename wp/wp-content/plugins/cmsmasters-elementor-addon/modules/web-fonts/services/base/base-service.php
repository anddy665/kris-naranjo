<?php
namespace CmsmastersElementor\Modules\WebFonts\Services\Base;

use CmsmastersElementor\Modules\WebFonts\Types\Local;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


abstract class Base_Service {

	protected $directory = '';

	protected $archive_files_to_upload = array();

	protected $archive_files_to_ignore = array();

	protected $archive_rule_directories = array();

	protected $archive_directory_files_to_upload = array();

	protected $current_files_folder = '';

	protected $dir_name = '';

	protected $stylesheet_file = '';

	protected $data_file = '';

	protected $stylesheet;

	protected $settings = array();

	private $service_font_faces = array();

	private $current_font_face = '';

	private $counter = 0;

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP Strict Standards.
	 */
	public static function get_type() {}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP Strict Standards.
	 */
	public static function get_link() {}

	/**
	 * Base Local Fonts Service constructor.
	 *
	 * @param $directory
	 */
	public function __construct( $directory ) {
		$this->directory = $directory;

		if ( ! $this->is_service() ) {
			return false;
		}

		return $this;
	}

	/**
	 * is service
	 *
	 * validate that the current uploaded zip is in allowed service format
	 *
	 * @return bool
	 */
	public function is_service() {
		$service_files = array_merge( $this->archive_files_to_upload, $this->archive_files_to_ignore );

		foreach ( $service_files as $file ) {
			if ( ! $this->is_file_allowed( $file ) ) {
				return false;
			}
		}

		return true;
	}

	private function is_file_allowed( $path_name ) {
		$check = $this->directory . $path_name;

		if ( ! file_exists( $check ) ) {
			return false;
		}

		if ( '/' === substr( $path_name, -1 ) ) {
			return is_dir( $check );
		}

		return true;
	}

	public function is_valid() {
		if ( ! file_exists( $this->directory . $this->data_file ) ) {
			return false; // missing data file
		}

		return true;
	}

	public function handle_new_local_font() {
		$this->read_stylesheet_text( Utils::get_wp_filesystem() );
		$this->set_font_faces();
		$this->prepare();

		foreach ( $this->archive_files_to_ignore as $rule ) {
			if ( ']' !== substr( $rule, -1 ) ) {
				continue;
			}

			$this->add_ignore_rule( $rule );
		}
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
		$this->stylesheet = $wp_filesystem->get_contents( $this->directory . $this->stylesheet_file );
	}

	protected function set_font_faces() {
		if ( empty( $this->service_font_faces ) ) {
			preg_match_all( '/@font-face\s\{\s*([^\}]*)\s*\}/', $this->stylesheet, $font_faces );

			if ( empty( $font_faces[1] ) ) {
				return; // missing name
			}

			$this->service_font_faces = $font_faces[1];
		}

		$this->set_font_face_settings();
	}

	protected function set_font_face_settings() {
		if ( empty( $this->service_font_faces ) ) {
			return; // missing service font faces
		}

		foreach ( $this->service_font_faces as $font_face ) {
			$this->current_font_face = $font_face;

			$this->set_font_face_property( 'font-weight' );
			$this->set_font_face_property( 'font-style' );

			$this->set_font_face_src( 'local' );
			$this->set_font_face_src( 'url' );

			$this->counter++;
		}

		$this->current_font_face = '';
		$this->counter = 0;
	}

	protected function set_font_face_property( $property, $default = 'normal' ) {
		$pattern = sprintf( '/%s:\s(.*);/', $property );

		preg_match_all( $pattern, $this->current_font_face, $match );

		$match = isset( $match[1][0] ) ? $match[1][0] : $default;

		$this->settings['font_faces'][ $this->counter ][ $property ] = $match;
	}

	protected function set_font_face_src( $source ) {
		$pattern = sprintf( "/%s\('([^']*)'\)/", $source );

		preg_match_all( $pattern, $this->current_font_face, $matches );

		if ( ! empty( $matches[1] ) ) {
			foreach ( $matches[1] as $match ) {
				if ( 'url' === $source ) {
					$format = $this->extract_font_format( $match );

					if ( ! Local::is_format_supported( $format ) ) {
						continue;
					}

					$file = $this->remove_format_suffix( $match );

					$this->settings['font_faces'][ $this->counter ]['src'][ $source ][ $format ] = $file;
				} else {
					$this->settings['font_faces'][ $this->counter ]['src'][ $source ][] = $match;
				}
			}
		}
	}

	private function extract_font_format( $font ) {
		$dot_index = strrpos( $font, '.' );
		$format = substr( $font, ++$dot_index );

		return $this->remove_format_suffix( $format );
	}

	private function remove_format_suffix( $string ) {
		$stop_index = strrpos( $string, '?' );

		if ( ! $stop_index ) {
			$stop_index = strrpos( $string, '#' );
		}

		if ( $stop_index ) {
			$string = substr( $string, 0, $stop_index );
		}

		return $string;
	}

	protected function prepare() {
		$this->set_main_files_to_upload();
	}

	private function set_main_files_to_upload() {
		$this->archive_files_to_upload[] = $this->data_file;
		$this->archive_files_to_upload[] = $this->stylesheet_file;

		if ( empty( $this->settings['font_faces'] ) ) {
			return false; // missing font_faces setting
		}

		foreach ( $this->settings['font_faces'] as $font_face ) {
			foreach ( $font_face['src']['url'] as $file ) {
				$this->archive_files_to_upload[] = $file;
			}
		}
	}

	protected function add_ignore_rule( $rule ) {
		$directory = strstr( $rule, '[^', true );

		$this->archive_rule_directories[] = $directory;

		$files_rule = strstr( $rule, '[^' );
		$files_names = substr( $files_rule, 2, -1 );
		$allowed_files = explode( '|', $files_names );

		foreach ( $allowed_files as $file ) {
			$this->archive_directory_files_to_upload[] = $directory . $file;
		}
	}

	public function move_files( $post_id ) {
		$wp_filesystem = Utils::get_wp_filesystem();

		$unique_name = $this->get_unique_name();
		$upload_dir = $this->get_ensure_upload_dir( $unique_name ) . '/';

		foreach ( $wp_filesystem->dirlist( $this->directory, false, true ) as $file ) {
			if ( $this->is_file_to_ignore( $file ) ) {
				continue;
			}

			if ( $wp_filesystem->is_dir( $this->directory . $file['name'] ) ) {
				$wp_filesystem->mkdir( $upload_dir . $file['name'] );

				$this->current_files_folder = $file['name'] . '/';

				foreach ( array_keys( $file['files'] ) as $directory_file ) {
					if ( ! $this->is_directory_file_to_upload( $directory_file ) ) {
						continue;
					}

					$this->move_and_insert( $post_id, $upload_dir, $file, $directory_file );
				}
			} else {
				$this->move_and_insert( $post_id, $upload_dir, $file );
			}
		}

		$this->cleanup_temp_files();

		update_post_meta( $post_id, Local::FONT_PATH_META_KEY, $upload_dir );

		$this->dir_name = $unique_name;
		$this->directory = $upload_dir;
	}

	public function get_unique_name() {
		$name = $this->get_name();
		$basename = $name;
		$counter = 1;

		while ( ! $this->is_name_unique( $name ) ) {
			$name = "{$basename}_{$counter}";

			$counter++;
		}

		return $name;
	}

	public function get_name() {
		if ( ! isset( $this->settings['name'] ) ) {
			preg_match_all( "/font-family: '(.*)'/", $this->stylesheet, $name );

			if ( ! isset( $name[1][0] ) ) {
				return false; // missing name
			}

			$name = str_replace( array( ' ', '-' ), '_', $name[1][0] );

			$this->settings['name'] = strtolower( $name );
		}

		return $this->settings['name'];
	}

	private function is_name_unique( $name ) {
		$local_fonts_dir = $this->get_local_fonts_dir();

		return ! is_dir( "{$local_fonts_dir}/{$name}" );
	}

	protected function get_local_fonts_dir() {
		$path = Utils::get_upload_dir_parameter( 'basedir', '/elementor/cmsmasters-local-fonts' );

		/**
		 * Upload file path.
		 *
		 * Filters the path for local fonts file uploads.
		 *
		 * @param string $path
		 */
		$path = apply_filters( 'cmsmasters_elementor/web_fonts/local_fonts/dir', $path );

		$path = Utils::get_ensure_upload_dir( $path );

		return $path;
	}

	protected function get_ensure_upload_dir( $dir = '' ) {
		$path = $this->get_local_fonts_dir();

		if ( ! empty( $dir ) ) {
			$path .= "/{$dir}";
		}

		return Utils::get_ensure_upload_dir( $path );
	}

	private function is_file_to_ignore( $file ) {
		$filename = $file['name'];

		if ( 'd' === $file['type'] ) {
			$filename .= '/';
		}

		if ( in_array( $filename, $this->archive_files_to_ignore, true ) ) {
			return true;
		}

		return false;
	}

	private function is_directory_file_to_upload( $file ) {
		$current_folder_is_rule_folder = in_array(
			$this->current_files_folder,
			$this->archive_rule_directories,
			true
		);

		if ( ! $current_folder_is_rule_folder ) {
			return true;
		}

		$current_file_need_to_upload = in_array(
			$this->current_files_folder . $file,
			$this->archive_directory_files_to_upload,
			true
		);

		if ( ! $current_file_need_to_upload ) {
			return false;
		}

		return true;
	}

	private function move_and_insert( $post_id, $upload_dir, $file, $directory_file = '' ) {
		$name = $file['name'];

		if ( $directory_file ) {
			$name .= "/{$directory_file}";
		}

		$directory = $this->directory . $name;
		$file_to_insert = $upload_dir . $name;

		Utils::get_wp_filesystem()->move( $directory, $file_to_insert );

		$this->insert_attachment( $this->get_url( $name ), $file_to_insert, $post_id );
	}

	private function insert_attachment( $file_url, $filename, $post_id = 0 ) {
		$attachment = array(
			'post_type' => 'attachment',
			'post_parent' => $post_id,
			'guid' => $file_url,
			'file' => $filename,
		);

		return wp_insert_attachment( $attachment );
	}

	protected function get_url( $filename = '' ) {
		return $this->get_file_url( $this->dir_name . $filename );
	}

	/**
	 * Gets the URL to uploaded file.
	 *
	 * @param $file_name
	 *
	 * @return string
	 */
	protected function get_file_url( $file_name ) {
		$url = Utils::get_upload_dir_parameter( 'baseurl', '/elementor/cmsmasters-local-fonts/' ) . $file_name;

		/**
		 * Upload file URL.
		 *
		 * Filters the URL to a file uploaded using Elementor forms.
		 *
		 * @since 1.0.0
		 *
		 * @param string $url File URL.
		 * @param string $file_name File name.
		 */
		$url = apply_filters( 'cmsmasters_elementor/web_fonts/local_fonts/url', $url, $file_name );

		return $url;
	}

	protected function cleanup_temp_files() {
		Utils::get_wp_filesystem()->rmdir( $this->directory, true );
	}

	public function build_config() {
		$name = $this->get_name();
		$label = str_replace( '_', ' ', $name );

		$config = array(
			'name' => $name,
			'label' => ucwords( $label ),
			'url' => str_replace( array( 'http://', 'https://' ), '//', $this->get_stylesheet() ),
			'dir' => str_replace( array( 'http://', 'https://' ), '//', $this->get_url( '/' ) ),
			'enqueue' => $this->get_enqueue(),
			'service_type' => static::get_type(),
		);

		$config['font_faces'] = $this->extract_font_faces_list();
		$config['styles'] = $this->extract_font_styles();
		$config['count'] = count( $config['styles'] );
		$config['formats'] = $this->extract_file_formats_list();

		return $config;
	}

	protected function extract_font_faces_list() {
		return $this->settings['font_faces'];
	}

	private function extract_font_styles() {
		$styles = array();

		foreach ( $this->extract_font_faces_list() as $font_face ) {
			$weight = $font_face['font-weight'];
			$style = $font_face['font-style'];

			switch ( $weight ) {
				case 'normal':
					$weight = 400;

					break;
				case 'bold':
					$weight = 700;

					break;
			}

			$styles[] = array(
				'weight' => (int) $weight,
				'style' => $style,
			);
		}

		$weight_column = array_column( $styles, 'weight' );
		$style_column = array_column( $styles, 'style' );

		array_multisort(
			$weight_column,
			SORT_ASC,
			SORT_NUMERIC,
			$style_column,
			SORT_DESC,
			SORT_STRING,
			$styles
		);

		unset( $styles['weight'] );
		unset( $styles['style'] );

		return $styles;
	}

	private function extract_file_formats_list() {
		$font_faces = $this->extract_font_faces_list();
		$formats = array_keys( $font_faces[0]['src']['url'] );

		rsort( $formats );

		return array_unique( $formats );
	}

	protected function get_stylesheet() {
		if ( ! $this->get_name() ) {
			return false; // missing name
		}

		return $this->get_url( '/' . $this->stylesheet_file );
	}

	protected function get_enqueue() {
		return false;
	}

}
