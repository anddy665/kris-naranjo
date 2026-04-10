<?php
namespace CmsmastersFramework\Admin\Installer\Importer;

use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\Logger;
use CmsmastersFramework\Core\Utils\Utils;

use Elementor\Utils as Elementor_Utils;
use CmsmastersElementor\Utils as CmsmastersElementor_Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Elementor Importer handler class is responsible for different methods on importing "Elementor" plugin elements.
 *
 * @since 1.0.0
 */
class Elementor_Importer {

	/**
	 * Elementor Templates Import constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! self::activation_status() || ! API_Requests::check_token_status() ) {
			return;
		}

		add_action( 'merlin_after_all_import', array( $this, 'end_import' ) );
	}

	/**
	 * Activation status.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Activation status.
	 */
	public static function activation_status() {
		return ( did_action( 'elementor/loaded' ) && class_exists( 'Cmsmasters_Elementor_Addon' ) );
	}

	/**
	 * End import.
	 *
	 * Fires on import_end action.
	 *
	 * @since 1.0.0
	 */
	public function end_import() {
		Logger::info( 'Start import remapping ids process' );

		$demo = Utils::get_demo();

		$displayed_ids = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_displayed_ids' );

		if ( false === $displayed_ids ) {
			$displayed_ids = array();
		}

		do_action( 'cmsmasters_replace_elementor_locations_id', $displayed_ids );

		$this->change_import_elements_ids();
		$this->change_megamenu_import_templates_ids();

		update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_content_import_status', 'imported', false );

		Logger::info( 'Finish import remapping process' );
	}

	/**
	 * Change elements ids on import.
	 *
	 * @since 1.0.0
	 */
	public function change_import_elements_ids() {
		$post_ids = Utils::get_elementor_post_ids();

		if ( empty( $post_ids ) ) {
			return;
		}

		Logger::info( 'Starting the process of remapping the IDs of imported data' );

		$demo = Utils::get_demo();

		$patterns = array();

		$templates_ids = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_import_templates_ids', array() );

		if ( ! empty( $templates_ids ) ) {
			$patterns[] = array(
				'regex' => '/"(blog_template_id|post_featured_template_id|post_regular_template_id|saved_section|template_id|cmsmasters_template_id|saved_template|cms_popup_id)"\s*:\s*"(\d+)"/',
				'replacement' => function ( $matches ) use ( $templates_ids ) {
					$field_name = $matches[1];
					$old_id = (int) $matches[2];
					$new_id = $templates_ids[ $old_id ] ?? $old_id;

					return "\"{$field_name}\":\"{$new_id}\"";
				},
			);

			$patterns[] = array(
				'regex' => '/\[(elementor-tag[^\]]*?name=\\\"cmsmasters-action-popup\\\"[^\]]*?)\]/',
				'replacement' => function ( $matches ) use ( $templates_ids ) {
					$full_tag = $matches[1];

					if ( preg_match( '/settings=\\\"(.*?)\\\"/', $full_tag, $settings_match ) ) {
						$settings = json_decode( urldecode( $settings_match[1] ), true );

						if ( empty( $settings['popup_id'] ) ) {
							return $matches[0];
						}

						$old_id = (int) $settings['popup_id'];
						$settings['popup_id'] = $templates_ids[ $old_id ] ?? $old_id;

						$settings = urlencode( wp_json_encode( $settings ) );

						$full_tag = str_replace( $settings_match[1], $settings, $full_tag );
					}

					return "[{$full_tag}]";
				},
			);
		}

		$displayed_ids = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_displayed_ids', array() );
		$displayed_post_ids = isset( $displayed_ids['post_id'] ) ? $this->rearrange_displayed_ids( $displayed_ids['post_id'] ) : array();
		$displayed_taxonomy_ids = isset( $displayed_ids['taxonomy'] ) ? $this->rearrange_displayed_ids( $displayed_ids['taxonomy'] ) : array();

		if ( ! empty( $displayed_post_ids ) || ! empty( $displayed_taxonomy_ids ) ) {
			$patterns[] = array(
				'regex' => '/"([^"]*?(posts_in|posts_not_in|fallback_posts_in|form_list|donor_list|include_term_ids|exclude_term_ids|cat_list|tag_list|selected_authors|nav_menu))"\s*:\s*(\[[^\]]*\]|"\d+")/',
				'replacement' => function ( $matches ) use ( $displayed_post_ids, $displayed_taxonomy_ids ) {
					$data = $matches[3];

					if ( '[]' === $data ) {
						return $matches[0];
					}

					$field_name = $matches[1];
					$field_type = $matches[2];

					$is_array = ( '[' === $data[0] );
					$old_ids = $is_array ? explode( ',', trim( $data, '[]" ' ) ) : array( trim( $data, '"' ) );

					if ( in_array( $field_type, array( 'posts_in', 'posts_not_in', 'fallback_posts_in', 'form_list', 'donor_list' ), true ) ) {
						$ids_group = $displayed_post_ids;
					} elseif ( in_array( $field_type, array( 'include_term_ids', 'exclude_term_ids', 'cat_list', 'tag_list', 'nav_menu' ), true ) ) {
						$ids_group = $displayed_taxonomy_ids;
					} elseif ( 'selected_authors' === $field_type ) {
						return "\"{$field_name}\":[]";
					} else {
						return $matches[0];
					}

					$new_ids = array();

					foreach ( $old_ids as $old_id ) {
						$old_id = trim( $old_id, '" ' );

						$new_ids[] = isset( $ids_group[ $old_id ] ) ? '"' . $ids_group[ $old_id ] . '"' : '"' . $old_id . '"';
					}

					$replacement = $is_array ? '[' . implode( ',', $new_ids ) . ']' : $new_ids[0];

					return "\"{$field_name}\":{$replacement}";
				},
			);

			$patterns[] = array(
				'regex' => '/\[(elementor-tag[^\]]*?name=\\\"cmsmasters-site-internal-url\\\"[^\]]*?)\]/',
				'replacement' => function ( $matches ) use ( $displayed_post_ids, $displayed_taxonomy_ids ) {
					$full_tag = $matches[1];

					if ( preg_match( '/settings=\\\"(.*?)\\\"/', $full_tag, $settings_match ) ) {
						$settings = json_decode( urldecode( $settings_match[1] ), true );

						if ( ! empty( $settings['post_id'] ) ) {
							$old_id = (int) $settings['post_id'];
							$settings['post_id'] = $displayed_post_ids[ $old_id ] ?? $old_id;
						}

						if ( ! empty( $settings['taxonomy_id'] ) ) {
							$old_id = (int) $settings['taxonomy_id'];
							$settings['taxonomy_id'] = $displayed_taxonomy_ids[ $old_id ] ?? $old_id;
						}

						$settings = urlencode( wp_json_encode( $settings ) );

						$full_tag = str_replace( $settings_match[1], $settings, $full_tag );
					}

					return "[{$full_tag}]";
				},
			);

			$patterns[] = array(
				'regex' => '/("settings"\s*:\s*\{\s*"wp"\s*:\s*\{\s*.*?"id"\s*:\s*")(\d+)/s',
				'replacement' => function ( $matches ) use ( $displayed_post_ids ) {
					$old_id = $matches[2];
					$new_id = isset( $displayed_post_ids[ $old_id ] ) ? $displayed_post_ids[ $old_id ] : $old_id;

					return $matches[1] . $new_id;
				},
			);
		}

		$attachments_ids = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_attachments_ids', array() );

		if ( ! empty( $attachments_ids ) ) {
			$wp_uploads_dir = wp_get_upload_dir();

			$patterns[] = array(
				'regex' => '/\{[^{}]*wp-content\\\\?\/uploads[^{}]*\}/',
				'replacement' => function ( $matches ) use ( $attachments_ids, $wp_uploads_dir ) {
					$match = json_decode( $matches[0], true );

					if ( ! is_array( $match ) || ! isset( $match['id'] ) || ! isset( $match['url'] ) ) {
						Logger::debug( sprintf(
							'Invalid JSON structure: %1$s',
							$matches[0]
						) );

						return $matches[0];
					}

					$old_id = (int) $match['id'];
					$old_url = $match['url'];

					if ( false !== strpos( $old_url, $wp_uploads_dir['baseurl'] ) ) {
						return $matches[0];
					}

					$new_id = $attachments_ids[ $old_id ] ?? $old_id;
					$new_url = wp_get_attachment_url( $new_id );

					if ( ! $new_url ) {
						$new_id = '';
						$new_url = $old_url;

						if (
							false !== strpos( $new_url, '.cmsmasters.net' ) ||
							false !== strpos( $new_url, '.seaside-themes.com' )
						) {
							$filename = basename( parse_url( $new_url, PHP_URL_PATH ) );
							$potential_path = trailingslashit( $wp_uploads_dir['path'] ) . $filename;

							if ( file_exists( $potential_path ) ) {
								$attachment_id = attachment_url_to_postid( trailingslashit( $wp_uploads_dir['url'] ) . $filename );

								if ( $attachment_id ) {
									$new_id = $attachment_id;
									$new_url = wp_get_attachment_url( $attachment_id );
								}
							} else {
								$new_media_data = CmsmastersElementor_Utils::download_media( $new_url );

								$new_id = $new_media_data['id'];
								$new_url = $new_media_data['url'];
							}

							if ( isset( $match['source'] ) ) {
								unset( $match['source'] );
							}
						}
					}

					$match['id'] = $new_id;
					$match['url'] = $new_url;

					return wp_json_encode( $match );
				},
			);

			$patterns[] = array(
				'regex' => '/src=\\\?\"[^\"]*\.(cmsmasters\.net|seaside-themes\.com)[^\"]*\\\\?\/wp-content\\\\?\/uploads\\\\?\/(?:sites\\\\?\/\d+\\\\?\/)?([^\"]+)\\\?\"/',
				'replacement' => function ( $matches ) use ( $wp_uploads_dir ) {
					$url_path = stripslashes( $matches[2] );

					$full_path = $wp_uploads_dir['basedir'] . '/' . $url_path;

					$img_formats = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp', 'tiff', 'tif', 'ico', 'heic', 'svg', 'webp' );
					$file_ext = strtolower( pathinfo( $url_path, PATHINFO_EXTENSION ) );

					if ( file_exists( $full_path ) ) {
						$new_url = $wp_uploads_dir['baseurl'] . '/' . $url_path;
					} elseif ( in_array( $file_ext, $img_formats, true ) ) {
						$new_url = Elementor_Utils::get_placeholder_image_src();
					} else {
						$new_url = '';
					}

					$new_url = trim( wp_json_encode( $new_url ), '"' );

					return 'src=\"' . $new_url . '\"';
				},
			);
		}

		$callback_replacements = array();
		$regex_patterns = array();

		foreach ( $patterns as $pattern ) {
			$regex_patterns[] = $pattern['regex'];
			$callback_replacements[] = $pattern['replacement'];
		}

		foreach ( $post_ids as $post_id ) {
			$post_type = get_post_type( $post_id );

			if ( 'revision' === $post_type ) {
				continue;
			}

			$elementor_data = get_post_meta( $post_id, '_elementor_data', true );

			if ( empty( $elementor_data ) ) {
				continue;
			}

			Logger::info( sprintf(
				'Start remapping imported data in %1$s %2$d',
				$post_type,
				$post_id,
			) );

			$elementor_data = preg_replace_callback_array(
				array_combine( $regex_patterns, $callback_replacements ),
				$elementor_data
			);

			update_post_meta( $post_id, '_elementor_data', wp_slash( $elementor_data ) );

			Logger::info( sprintf(
				'Finish remapping imported data in %1$s %2$d',
				$post_type,
				$post_id,
			) );
		}

		Logger::info( 'Finishing the process of remapping the IDs of imported data' );
	}

	/**
	 * Rearrange displayed ids.
	 *
	 * @since 1.0.0
	 *
	 * @param array $displayed_ids Displayed ids.
	 *
	 * @return array Displayed ids.
	 */
	private function rearrange_displayed_ids( $displayed_ids = array() ) {
		$out_ids = array();

		foreach ( $displayed_ids as $ids ) {
			foreach ( $ids as $old_id => $new_id ) {
				$out_ids[ $old_id ] = $new_id;
			}
		}

		return $out_ids;
	}

	/**
	 * Change templates ids in mega menu items on import.
	 *
	 * @since 1.0.0
	 *
	 * @param array $templates_ids Templates ids.
	 */
	protected function change_megamenu_import_templates_ids() {
		$menus = wp_get_nav_menus();

		if ( empty( $menus ) ) {
			return;
		}

		$demo = Utils::get_demo();

		$templates_ids = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_import_templates_ids' );

		if ( empty( $templates_ids ) ) {
			return;
		}

		Logger::info( 'Starting the process of remapping templates ids in mega menu items' );

		foreach ( $menus as $menu ) {
			$menu_items = wp_get_nav_menu_items( $menu->term_id );

			if ( empty( $menu_items ) ) {
				continue;
			}

			Logger::info( sprintf(
				'Start remapping imported data in menu %1$s %2$d',
				$menu->name,
				$menu->term_id,
			) );

			foreach ( $menu_items as $menu_item ) {
				$meta_data = get_post_meta( $menu_item->ID, '_cmsmasters_megamenu', true );

				if ( empty( $meta_data['template'] ) ) {
					continue;
				}

				$old_id = $meta_data['template'];

				if ( ! isset( $templates_ids[ $old_id ] ) ) {
					continue;
				}

				$meta_data['template'] = strval( $templates_ids[ $old_id ] );

				update_post_meta( $menu_item->ID, '_cmsmasters_megamenu', $meta_data );
			}

			Logger::info( sprintf(
				'Finish remapping imported data in menu %1$s %2$d',
				$menu->name,
				$menu->term_id,
			) );
		}

		Logger::info( 'Finishing the process of remapping templates ids in mega menu items' );
	}

}
