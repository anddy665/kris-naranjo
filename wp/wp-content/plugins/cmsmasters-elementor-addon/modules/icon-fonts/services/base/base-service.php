<?php
namespace CmsmastersElementor\Modules\IconFonts\Services\Base;

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

	protected $config;

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
	 * Undocumented function
	 *
	 * Description.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Filesystem_Base $wp_filesystem
	 */
	abstract protected function prepare( $wp_filesystem );

	abstract public function get_name();

	abstract protected function extract_icons_list();

	/**
	 * Icon Set Base constructor.
	 *
	 * @param $directory
	 */
	public function __construct( $directory ) {
		$this->directory = $directory;

		if ( ! $this->is_icon_set() ) {
			return false;
		}

		return $this;
	}

	/**
	 * is icon set
	 *
	 * validate that the current uploaded zip is in this icon set format
	 *
	 * @return bool
	 */
	public function is_icon_set() {
		$icon_set_files = array_merge( $this->archive_files_to_upload, $this->archive_files_to_ignore );

		foreach ( $icon_set_files as $file ) {
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

	public function handle_new_icon_set() {
		$this->prepare( Utils::get_wp_filesystem() );

		foreach ( $this->archive_files_to_ignore as $rule ) {
			if ( ']' !== substr( $rule, -1 ) ) {
				continue;
			}

			$this->add_ignore_rule( $rule );
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

		update_post_meta( $post_id, '_cmsmasters_icons_set_path', $upload_dir );

		$this->dir_name = $unique_name;
		$this->directory = $upload_dir;
	}

	public function get_unique_name() {
		$name = $this->get_name();
		$basename = $name;
		$counter = 1;

		while ( ! $this->is_name_unique( $name ) ) {
			$name = "{$basename}-{$counter}";

			$counter++;
		}

		return $name;
	}

	private function is_name_unique( $name ) {
		$icon_sets_dir = $this->get_icon_sets_dir();

		return ! is_dir( "{$icon_sets_dir}/{$name}" );
	}

	protected function get_icon_sets_dir() {
		$path = Utils::get_upload_dir_parameter( 'basedir', '/elementor/cmsmasters-local-icons' );

		/**
		 * Upload file path.
		 *
		 * Filters the path for local icons file uploads.
		 *
		 * @param string $path
		 */
		$path = apply_filters( 'cmsmasters_elementor/icon_fonts/local_icons/dir', $path );

		$path = Utils::get_ensure_upload_dir( $path );

		return $path;
	}

	protected function get_ensure_upload_dir( $dir = '' ) {
		$path = $this->get_icon_sets_dir();

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
		$url = Utils::get_upload_dir_parameter( 'baseurl', '/elementor/cmsmasters-local-icons/' ) . $file_name;

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
		$url = apply_filters( 'cmsmasters_elementor/icon_fonts/local_icons/url', $url, $file_name );

		return $url;
	}

	protected function cleanup_temp_files() {
		Utils::get_wp_filesystem()->rmdir( $this->directory, true );
	}

	public function build_config() {
		$name = $this->get_name();
		$label = str_replace( array( '-', '_' ), ' ', $name );

		$icon_set_config = array(
			'name' => $name,
			'label' => ucwords( $label ),
			'labelIcon' => $this->get_label_icon(),
			'ver' => $this->get_version(),
			'prefix' => $this->get_prefix(),
			'displayPrefix' => $this->get_display_prefix(),
			'url' => str_replace( array( 'http://', 'https://' ), '//', $this->get_stylesheet() ),
			'enqueue' => $this->get_enqueue(),
			'custom_icons_type' => static::get_type(),
		);

		$icons = $this->extract_icons_list();

		$icon_set_config['count'] = count( $icons );
		$icon_set_config['icons'] = $icons;

		if ( 25 < $icon_set_config['count'] ) {
			$icon_set_config['fetchJson'] = $this->store_icons_list_json( $icons );
		}

		return $icon_set_config;
	}

	protected function get_label_icon() {
		return 'eicon eicon-folder';
	}

	protected function get_version() {
		return '1.0.0';
	}

	protected function get_prefix() {
		return '';
	}

	protected function get_display_prefix() {
		return '';
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

	private function store_icons_list_json( $icons ) {
		$upload_dir = $this->get_ensure_upload_dir( $this->dir_name );
		$json_file = "{$upload_dir}/cmsmasters_icons_list.js";
		$json_icons = wp_json_encode( array( 'icons' => $icons ) );

		Utils::get_wp_filesystem()->put_contents( $json_file, $json_icons );

		return $this->get_url( '/cmsmasters_icons_list.js' );
	}

}
