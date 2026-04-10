<?php
namespace CmsmastersFramework\Admin\Installer\Merlin\Includes\WpContentImporter;

use CmsmastersFramework\Admin\Installer\Merlin\Includes\WpContentImporter\WXR_Importer;
use CmsmastersFramework\Core\Utils\File_Manager;
use CmsmastersFramework\Core\Utils\Logger;
use CmsmastersFramework\Core\Utils\Utils;

use XMLReader;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * The main importer class, extending the slightly modified WP importer 2.0 class WXR_Importer
 *
 * @since 1.0.0
 */
class Class_Merlin_Importer extends WXR_Importer {

	/**
	 * Time in milliseconds, marking the beginning of the import.
	 *
	 * @since 1.0.0
	 *
	 * @var float
	 */
	private $start_time;

	protected $cmsmasters_wp_upload_basedir;
	protected $cmsmasters_wp_upload_baseurl;
	protected $cmsmasters_temp_folder_path = 'cmsmasters/' . CMSMASTERS_THEME_NAME . '/import';

	/**
	 * Content import files.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $cmsmasters_content_import_files = array();

	/**
	 * Media data to import.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $cmsmasters_import_media_data = array();

	/**
	 * Importer constructor.
	 * Look at the parent constructor for the options parameters.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options The importer options.
	 */
	public function __construct( $options = array() ) {
		parent::__construct( $options );

		$wp_upload_dir = wp_upload_dir();
		$this->cmsmasters_wp_upload_basedir = trailingslashit( $wp_upload_dir['basedir'] );
		$this->cmsmasters_wp_upload_baseurl = trailingslashit( $wp_upload_dir['baseurl'] );

		// Check, if a new AJAX request is required.
		add_filter( 'wxr_importer.pre_process.post', array( $this, 'new_ajax_request_maybe' ) );

		// WooCommerce product attributes registration.
		if ( class_exists( 'WooCommerce' ) ) {
			add_filter( 'wxr_importer.pre_process.term', array( $this, 'woocommerce_product_attributes_registration' ), 10, 1 );
		}

		add_action( 'cmsmasters_import_ready', array( $this, 'cmsmasters_delete_import_temp_folder' ) );
	}

	/**
	 * Get the XML reader for the file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Path to the XML file.
	 *
	 * @return XMLReader|boolean Reader instance on success, false otherwise.
	 */
	protected function get_reader( $file ) {
		// Avoid loading external entities for security
		$old_value = null;
		if ( function_exists( 'libxml_disable_entity_loader' ) ) {
			// $old_value = libxml_disable_entity_loader( true );
		}

		if ( ! class_exists( 'XMLReader' ) ) {
			Logger::critical( 'The XMLReader class is missing! Please install the XMLReader PHP extension on your server' );

			return false;
		}

		$reader = new XMLReader();
		$status = $reader->open( $file );

		if ( ! is_null( $old_value ) ) {
			// libxml_disable_entity_loader( $old_value );
		}

		if ( ! $status ) {
			Logger::error( 'Could not open the XML file for parsing!' );

			return false;
		}

		return $reader;
	}

	/**
	 * Get the basic import content data.
	 * Which elements are present in this import file (check possible elements in the $data variable)?
	 *
	 * @since 1.0.0
	 *
	 * @param $file
	 *
	 * @return array|bool
	 */
	public function get_basic_import_content_data( $file ) {
		$data = array(
			'users'      => false,
			'categories' => false,
			'tags'       => false,
			'terms'      => false,
			'posts'      => false,
		);

		// Get the XML reader and open the file.
		$reader = $this->get_reader( $file );

		if ( empty( $reader ) ) {
			return false;
		}

		// Start parsing!
		while ( $reader->read() ) {
			// Only deal with element opens.
			if ( $reader->nodeType !== XMLReader::ELEMENT ) {
				continue;
			}

			switch ( $reader->name ) {
				case 'wp:author':
					// Skip, if the users were already detected.
					if ( $data['users'] ) {
						$reader->next();
						break;
					}

					$node   = $reader->expand();
					$parsed = $this->parse_author_node( $node );

					// Skip, if there was an error in parsing the author node.
					if ( is_wp_error( $parsed ) ) {
						$reader->next();
						break;
					}

					$data['users'] = true;

					// Handled everything in this node, move on to the next.
					$reader->next();
					break;

				case 'item':
					// Skip, if the posts were already detected.
					if ( $data['posts'] ) {
						$reader->next();
						break;
					}

					$node   = $reader->expand();
					$parsed = $this->parse_post_node( $node );

					// Skip, if there was an error in parsing the item node.
					if ( is_wp_error( $parsed ) ) {
						$reader->next();
						break;
					}

					$data['posts'] = true;

					// Handled everything in this node, move on to the next
					$reader->next();
					break;

				case 'wp:category':
					$data['categories'] = true;

					// Handled everything in this node, move on to the next
					$reader->next();
					break;
				case 'wp:tag':
					$data['tags'] = true;

					// Handled everything in this node, move on to the next
					$reader->next();
					break;
				case 'wp:term':
					$data['terms'] = true;

					// Handled everything in this node, move on to the next
					$reader->next();
					break;
			}
		}

		return $data;
	}


	/**
	 * Get the number of posts (posts, pages, CPT, attachments), that the import file has.
	 *
	 * @since 1.0.0
	 *
	 * @param $file
	 *
	 * @return int
	 */
	public function get_number_of_posts_to_import( $file ) {
		$reader  = $this->get_reader( $file );
		$counter = 0;

		if ( empty( $reader ) ) {
			return $counter;
		}

		// Start parsing!
		while ( $reader->read() ) {
			// Only deal with element opens.
			if ( $reader->nodeType !== XMLReader::ELEMENT ) {
				continue;
			}

			if ( 'item' == $reader->name ) {
				$node   = $reader->expand();
				$parsed = $this->parse_post_node( $node );

				// Skip, if there was an error in parsing the item node.
				if ( is_wp_error( $parsed ) ) {
					$reader->next();
					continue;
				}

				$counter++;
			}
		}

		return $counter;
	}

	/**
	 * The main controller for the actual import stage.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Path to the WXR file for importing.
	 * @param array $options Import options (which parts to import).
	 *
	 * @return boolean
	 */
	public function import( $file, $options = array() ) {
		add_filter( 'import_post_meta_key', array( $this, 'is_valid_meta_key' ) );
		add_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) );

		// Start the import timer.
		$this->start_time = microtime( true );

		// Set the existing import data, from previous AJAX call, if any.
		$this->restore_import_data_transient();

		// Set the import options defaults.
		if ( empty( $options ) ) {
			$options = array(
				'users'      => false,
				'categories' => true,
				'tags'       => true,
				'terms'      => true,
				'posts'      => true,
			);
		}

		if ( $this->cmsmasters_set_import_media_data() ) {
			if ( ! $this->cmsmasters_import_media() ) {
				Logger::error( 'Import Media: Something went wrong. Import stopped.' );

				return false;
			}
		}

		$result = $this->import_start( $file );

		if ( is_wp_error( $result ) ) {
			Logger::error( 'Content import start error: ' . $result->get_error_message() );

			return false;
		}

		// Get the actual XML reader.
		$reader = $this->get_reader( $file );

		if ( empty( $reader ) ) {
			return false;
		}

		// Set the version to compatibility mode first
		$this->version = '1.0';

		// Reset other variables
		$this->base_url = '';

		// Start parsing!
		while ( $reader->read() ) {
			// Only deal with element opens.
			if ( $reader->nodeType !== XMLReader::ELEMENT ) {
				continue;
			}

			switch ( $reader->name ) {
				case 'wp:wxr_version':
					// Upgrade to the correct version
					$this->version = $reader->readString();

					if ( version_compare( $this->version, self::MAX_WXR_VERSION, '>' ) ) {
						Logger::warning( sprintf(
							'This WXR file (version %s) is newer than the importer (version %s) and may not be supported. Please consider updating.',
							$this->version,
							self::MAX_WXR_VERSION
						) );
					}

					// Handled everything in this node, move on to the next
					$reader->next();
					break;

				case 'wp:base_site_url':
					$this->base_url = $reader->readString();

					// Handled everything in this node, move on to the next
					$reader->next();
					break;

				case 'item':
					if ( empty( $options['posts'] ) ) {
						$reader->next();
						break;
					}

					$node   = $reader->expand();
					$parsed = $this->parse_post_node( $node );

					if ( is_wp_error( $parsed ) ) {
						$this->log_error( $parsed );

						// Skip the rest of this post
						$reader->next();
						break;
					}

					$this->process_post( $parsed['data'], $parsed['meta'], $parsed['comments'], $parsed['terms'] );

					// Handled everything in this node, move on to the next
					$reader->next();
					break;

				case 'wp:author':
					if ( empty( $options['users'] ) ) {
						$reader->next();
						break;
					}

					$node   = $reader->expand();
					$parsed = $this->parse_author_node( $node );

					if ( is_wp_error( $parsed ) ) {
						$this->log_error( $parsed );

						// Skip the rest of this post
						$reader->next();
						break;
					}

					$status = $this->process_author( $parsed['data'], $parsed['meta'] );

					if ( is_wp_error( $status ) ) {
						$this->log_error( $status );
					}

					// Handled everything in this node, move on to the next
					$reader->next();
					break;

				case 'wp:category':
					if ( empty( $options['categories'] ) ) {
						$reader->next();
						break;
					}

					$node   = $reader->expand();
					$parsed = $this->parse_term_node( $node, 'category' );

					if ( is_wp_error( $parsed ) ) {
						$this->log_error( $parsed );

						// Skip the rest of this post
						$reader->next();
						break;
					}

					$status = $this->process_term( $parsed['data'], $parsed['meta'] );

					// Handled everything in this node, move on to the next
					$reader->next();
					break;

				case 'wp:tag':
					if ( empty( $options['tags'] ) ) {
						$reader->next();
						break;
					}

					$node   = $reader->expand();
					$parsed = $this->parse_term_node( $node, 'tag' );

					if ( is_wp_error( $parsed ) ) {
						$this->log_error( $parsed );

						// Skip the rest of this post
						$reader->next();
						break;
					}

					$status = $this->process_term( $parsed['data'], $parsed['meta'] );

					// Handled everything in this node, move on to the next
					$reader->next();
					break;

				case 'wp:term':
					if ( empty( $options['terms'] ) ) {
						$reader->next();
						break;
					}

					$node   = $reader->expand();
					$parsed = $this->parse_term_node( $node );

					if ( is_wp_error( $parsed ) ) {
						$this->log_error( $parsed );

						// Skip the rest of this post
						$reader->next();
						break;
					}

					$status = $this->process_term( $parsed['data'], $parsed['meta'] );

					// Handled everything in this node, move on to the next
					$reader->next();
					break;

				default:
					// Skip this node, probably handled by something already
					break;
			}
		}

		// Now that we've done the main processing, do any required
		// post-processing and remapping.
		$this->post_process();

		if ( $this->options['aggressive_url_search'] ) {
			$this->replace_attachment_urls_in_content();
		}

		$this->remap_featured_images();

		$this->import_end();

		// Set the current importer state, so the data can be used on the next AJAX call.
		$this->set_current_importer_data();

		return true;
	}

	/**
	 * Set import media data.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function cmsmasters_set_import_media_data() {
		$data = Utils::get_import_demo_data();

		if ( empty( $data ) || empty( $data['files_url'] ) || empty( $data['files_urls']['media-data'] ) ) {
			return false;
		}

		$this->cmsmasters_content_import_files = array(
			'media-data' => $data['files_urls']['media-data'],
			'files_url' => $data['files_url'],
		);

		$media_data_file_path = $this->cmsmasters_wp_upload_basedir . $this->cmsmasters_temp_folder_path . '/media-data.json';

		if ( ! file_exists( $media_data_file_path ) ) {
			$media_data_file_path = File_Manager::download_temp_file( $this->cmsmasters_content_import_files['media-data'], 'media-data.json', $this->cmsmasters_temp_folder_path );
		}

		$all_media_data = File_Manager::get_file_contents( $media_data_file_path );

		if ( empty( $all_media_data ) ) {
			Logger::error( 'Import Media: Empty media data.' );

			$this->cmsmasters_delete_import_temp_folder();

			return false;
		}

		$all_media_data = json_decode( $all_media_data, true );

		if (
			empty( $all_media_data['archives_count'] ) ||
			empty( $all_media_data['media'] )
		) {
			Logger::error( 'Import Media: Invalid media data format.' );

			$this->cmsmasters_delete_import_temp_folder();

			return false;
		}

		$this->cmsmasters_import_media_data = $all_media_data;

		return true;
	}

	/**
	 * Import Media.
	 *
	 * Import media from API to uploads.
	 *
	 * @since 1.0.0
	 */
	public function cmsmasters_import_media() {
		if ( 'done' === get_option( CMSMASTERS_OPTIONS_PREFIX . 'import_media_status', 'pending' ) ) {
			return true;
		}

		if ( ! class_exists( '\ZipArchive' ) ) {
			Logger::error( 'Import Media: PHP Zip extension not loaded.' );

			return false;
		}

		for ( $i = 1; $i <= intval( $this->cmsmasters_import_media_data['archives_count'] ); $i++ ) {
			$media_archive_temp_path = File_Manager::download_temp_file( $this->cmsmasters_content_import_files['files_url'] . "media/media-archive-{$i}.zip", "media-archive-{$i}.zip", $this->cmsmasters_temp_folder_path );

			if ( ! file_exists( $media_archive_temp_path ) ) {
				Logger::error( 'Import Media: Unable to download media archive.' );

				continue;
			}

			$zip = new \ZipArchive();

			if ( true === $zip->open( $media_archive_temp_path ) ) {
				$zip->extractTo( $this->cmsmasters_wp_upload_basedir );
				$zip->close();

				Logger::info( sprintf( 'Import Media: Media archive %d extracted successfully.', $i ) );
			} else {
				Logger::error( sprintf( 'Import Media: Failed to open archive %d.', $i ) );
			}

			@unlink( $media_archive_temp_path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}

		Logger::info( 'Import Media: All media archives processed.' );

		update_option( CMSMASTERS_OPTIONS_PREFIX . 'import_media_status', 'done', false );

		return true;
	}

	/**
	 * Create new posts based on import information
	 *
	 * Posts marked as having a parent which doesn't exist will become top level items.
	 * Doesn't create a new post if: the post type doesn't exist, the given post ID
	 * is already noted as imported or a post with the same title and date already exists.
	 * Note that new/updated terms, comments and meta are imported for the last of the above.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data     Post data.
	 * @param array $meta     Meta data.
	 * @param array $comments Comments on the post.
	 * @param array $terms    Terms on the post.
	 */
	protected function process_post( $data, $meta, $comments, $terms ) {
		/**
		 * Pre-process post data.
		 *
		 * @param array $data Post data. (Return empty to skip.)
		 * @param array $meta Meta data.
		 * @param array $comments Comments on the post.
		 * @param array $terms Terms on the post.
		 */
		$data = apply_filters( 'wxr_importer.pre_process.post', $data, $meta, $comments, $terms );
		if ( empty( $data ) ) {
			return false;
		}

		$original_id = isset( $data['post_id'] ) ? (int) $data['post_id'] : 0;
		$parent_id = isset( $data['post_parent'] ) ? (int) $data['post_parent'] : 0;

		// Have we already processed this?
		if ( isset( $this->mapping['post'][ $original_id ] ) ) {
			return false;
		}

		$post_type_object = get_post_type_object( $data['post_type'] );

		// Is this type even valid?
		if ( ! $post_type_object ) {
			Logger::warning( sprintf(
				'Failed to import "%s": Invalid post type %s',
				$data['post_title'],
				$data['post_type']
			) );
			return false;
		}

		$post_exists = $this->post_exists( $data );
		if ( $post_exists ) {
			Logger::info( sprintf(
				'%s "%s" already exists.',
				$post_type_object->labels->singular_name,
				$data['post_title']
			) );

			// Even though this post already exists, new comments might need importing
			$this->process_comments( $comments, $original_id, $data, $post_exists );

			return false;
		}

		// Map the parent post, or mark it as one we need to fix
		$requires_remapping = false;
		if ( $parent_id ) {
			if ( isset( $this->mapping['post'][ $parent_id ] ) ) {
				$data['post_parent'] = $this->mapping['post'][ $parent_id ];
			} else {
				$meta[] = array( 'key' => '_wxr_import_parent', 'value' => $parent_id );
				$requires_remapping = true;

				$data['post_parent'] = 0;
			}
		}

		// Map the author, or mark it as one we need to fix
		$author = sanitize_user( $data['post_author'], true );
		if ( empty( $author ) ) {
			// Missing or invalid author, use default if available.
			$data['post_author'] = $this->options['default_author'];
		} elseif ( isset( $this->mapping['user_slug'][ $author ] ) ) {
			$data['post_author'] = $this->mapping['user_slug'][ $author ];
		} else {
			$meta[] = array( 'key' => '_wxr_import_user_slug', 'value' => $author );
			$requires_remapping = true;

			$data['post_author'] = (int) get_current_user_id();
		}

		// Does the post look like it contains attachment images?
		if ( preg_match( self::REGEX_HAS_ATTACHMENT_REFS, $data['post_content'] ) ) {
			$meta[] = array( 'key' => '_wxr_import_has_attachment_refs', 'value' => true );
			$requires_remapping = true;
		}

		// Whitelist to just the keys we allow
		$postdata = array(
			'import_id' => $data['post_id'],
		);
		$allowed = array(
			'post_author'    => true,
			'post_date'      => true,
			'post_date_gmt'  => true,
			'post_content'   => true,
			'post_excerpt'   => true,
			'post_title'     => true,
			'post_status'    => true,
			'post_name'      => true,
			'comment_status' => true,
			'ping_status'    => true,
			'guid'           => true,
			'post_parent'    => true,
			'menu_order'     => true,
			'post_type'      => true,
			'post_password'  => true,
		);
		foreach ( $data as $key => $value ) {
			if ( ! isset( $allowed[ $key ] ) ) {
				continue;
			}

			$postdata[ $key ] = $data[ $key ];
		}

		$postdata = apply_filters( 'wp_import_post_data_processed', wp_slash( $postdata ), $data );

		$cmsmasters_demo = Utils::get_demo();

		if ( 'attachment' === $postdata['post_type'] ) {
			if ( ! $this->options['fetch_attachments'] ) {
				Logger::notice( sprintf(
					'Skipping attachment "%s", fetching attachments disabled',
					$data['post_title']
				) );
				return false;
			}

			$cmsmasters_imported_attachments_ids = get_option( CMSMASTERS_OPTIONS_PREFIX . $cmsmasters_demo . '_import_attachments_ids' );

			if ( isset( $cmsmasters_imported_attachments_ids[ $original_id ] ) ) {
				Logger::notice( sprintf(
					'Skipping attachment "%s", this attachment already imported',
					$data['post_title']
				) );

				return false;
			}

			add_filter( 'upload_mimes', array( $this, 'upload_mimes' ) );

			if ( ! empty( $this->cmsmasters_import_media_data['media'][ $original_id ] ) ) {
				$post_id = $this->cmsmasters_process_attachment( $postdata, $meta, $this->cmsmasters_import_media_data['media'][ $original_id ] );
			} else {
				$remote_url = ! empty( $data['attachment_url'] ) ? $data['attachment_url'] : $data['guid'];

				$post_id = $this->process_attachment( $postdata, $meta, $remote_url );
			}

			if ( is_wp_error( $post_id ) ) {
				Logger::error( 'Import Attachment error: ' . $post_id->get_error_message() );

				return false;
			}

			do_action( 'cmsmasters_wp_import_insert_attachment', $post_id, $original_id, $postdata, $data );

			remove_filter( 'upload_mimes', array( $this, 'upload_mimes' ) );
		} else {
			$cmsmasters_imported_posts_ids = get_option( CMSMASTERS_OPTIONS_PREFIX . $cmsmasters_demo . '_import_displayed_ids' );

			if ( isset( $cmsmasters_imported_posts_ids[ $original_id ] ) ) {
				Logger::notice( sprintf(
					'Skipping post "%s", this post already imported',
					$data['post_title']
				) );

				return false;
			}

			$post_id = wp_insert_post( $postdata, true );

			do_action( 'wp_import_insert_post', $post_id, $original_id, $postdata, $data );
		}

		if ( is_wp_error( $post_id ) ) {
			Logger::error( sprintf(
				'Failed to import "%s" (%s)',
				$data['post_title'],
				$post_type_object->labels->singular_name
			) );

			Logger::debug( $post_id->get_error_message() );

			/**
			 * Post processing failed.
			 *
			 * @param WP_Error $post_id Error object.
			 * @param array $data Raw data imported for the post.
			 * @param array $meta Raw meta data, already processed by {@see process_post_meta}.
			 * @param array $comments Raw comment data, already processed by {@see process_comments}.
			 * @param array $terms Raw term data, already processed.
			 */
			do_action( 'wxr_importer.process_failed.post', $post_id, $data, $meta, $comments, $terms );
			return false;
		}

		// Ensure stickiness is handled correctly too
		if ( $data['is_sticky'] === '1' ) {
			stick_post( $post_id );
		}

		// map pre-import ID to local ID
		$this->mapping['post'][ $original_id ] = (int) $post_id;
		if ( $requires_remapping ) {
			$this->requires_remapping['post'][ $post_id ] = true;
		}
		$this->mark_post_exists( $data, $post_id );

		Logger::info( sprintf(
			'Imported "%s" (%s)',
			$data['post_title'],
			$post_type_object->labels->singular_name
		) );

		Logger::debug( sprintf(
			'Post %d remapped to %d',
			$original_id,
			$post_id
		) );

		// Handle the terms too
		$terms = apply_filters( 'wp_import_post_terms', $terms, $post_id, $data );

		if ( ! empty( $terms ) ) {
			$term_ids = array();

			foreach ( $terms as $term ) {
				$taxonomy = $term['taxonomy'];
				$key = sha1( $taxonomy . ':' . $term['slug'] );

				if ( isset( $this->mapping['term'][ $key ] ) ) {
					$term_ids[ $taxonomy ][] = (int) $this->mapping['term'][ $key ];
				} else {
					/**
					 * Fix for the post format "categories".
					 * The issue in this importer is, that these post formats are misused as categories in WP export
					 * (as the export data <category> item in the post export item), but they are not actually
					 * exported as wp:category items in the XML file, so they need to be inserted on the fly (here).
					 *
					 * Maybe something better can be done in the future?
					 *
					 * Original issue reported here: https://wordpress.org/support/topic/post-format-videoquotegallery-became-format-standard/#post-8447683
					 *
					 */
					if ( 'post_format' === $taxonomy ) {
						$term_exists = term_exists( $term['slug'], $taxonomy );
						$term_id = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;

						if ( empty( $term_id ) ) {
							$t = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
							if ( ! is_wp_error( $t ) ) {
								$term_id = $t['term_id'];
								$this->mapping['term'][ $key ] = $term_id;
							} else {
								Logger::warning( sprintf(
									'Failed to import term: %s - %s',
									esc_html( $taxonomy ),
									esc_html( $term['name'] )
								) );
								continue;
							}
						}

						if ( ! empty( $term_id ) ) {
							$term_ids[ $taxonomy ][] = intval( $term_id );
						}
					} // End of fix.
					else {
						$meta[] = array( 'key' => '_wxr_import_term', 'value' => $term );
						$requires_remapping = true;
					}
				}
			}

			foreach ( $term_ids as $tax => $ids ) {
				$tt_ids = wp_set_post_terms( $post_id, $ids, $tax );

				do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $data );
			}
		}

		$this->process_comments( $comments, $post_id, $data );
		$this->process_post_meta( $meta, $post_id, $data );

		if ( 'nav_menu_item' === $data['post_type'] ) {
			$this->process_menu_item_meta( $post_id, $data, $meta );
		}

		/**
		 * Post processing completed.
		 *
		 * @param int $post_id New post ID.
		 * @param array $data Raw data imported for the post.
		 * @param array $meta Raw meta data, already processed by {@see process_post_meta}.
		 * @param array $comments Raw comment data, already processed by {@see process_comments}.
		 * @param array $terms Raw term data, already processed.
		 */
		do_action( 'wxr_importer.processed.post', $post_id, $data, $meta, $comments, $terms );
	}

	/**
	 * Upload mimes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $mime_types Mime types.
	 *
	 * @return array Mime types.
	 */
	public function upload_mimes( $mime_types ) {
		$mime_types['svg'] = 'image/svg+xml';
		$mime_types['webp'] = 'image/webp';

		return $mime_types;
	}

	/**
	 * Create a new attachment.
	 *
	 * @since 1.0.0
	 *
	 * @param array $post Attachment post details from WXR.
	 * @param array $meta Attachment post meta details.
	 *
	 * @return int|WP_Error Post ID on success, WP_Error otherwise.
	 */
	protected function cmsmasters_process_attachment( $post, $meta, $import_media_data = array() ) {
		if ( empty( $import_media_data ) ) {
			return new \WP_Error( 'attachment_processing_error', __( 'Media data not found for original ID.', 'cmsmasters-framework' ) );
		}

		$file_path = $this->cmsmasters_wp_upload_basedir . $import_media_data['relative_path'];

		if ( ! file_exists( $file_path ) ) {
			return new \WP_Error( 'attachment_processing_error', sprintf( __( 'File not found in uploads directory: %s', 'cmsmasters-framework' ), $file_path ) );
		}

		$info = wp_check_filetype( $file_path );

		if ( ! $info || empty( $info['type'] ) ) {
			return new \WP_Error( 'attachment_processing_error', __( 'Invalid file type.', 'cmsmasters-framework' ) );
		}

		$post['post_mime_type'] = $info['type'];
		$post['guid'] = $import_media_data['attachment_url'];

		$post_id = wp_insert_attachment( $post, $file_path );

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		$attachment_metadata = array();

		foreach ( $meta as $meta_item ) {
			if ( '_wp_attachment_metadata' === $meta_item['key'] ) {
				$attachment_metadata = maybe_unserialize( $meta_item['value'] );

				break;
			}
		}

		if ( ! empty( $attachment_metadata ) ) {
			if ( $info['type'] === 'image/svg+xml' ) {
				unset( $attachment_metadata['sizes'] );
			}

			wp_update_attachment_metadata( $post_id, $attachment_metadata );
		} else {
			Logger::warning( sprintf( 'Attachment metadata not found for post ID %d.', $post_id ) );
		}

		$uploaded_media_url = $this->cmsmasters_wp_upload_baseurl . $import_media_data['relative_path'];

		$this->url_remap[ $import_media_data['attachment_url'] ] = $uploaded_media_url;

		if ( substr( $import_media_data['attachment_url'], 0, 8 ) === 'https://' ) {
			$insecure_url = 'http' . substr( $import_media_data['attachment_url'], 5 );
			$this->url_remap[ $insecure_url ] = $uploaded_media_url;
		}

		return $post_id;
	}

	/**
	 * Import users only.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Path to the import file.
	 */
	public function import_users( $file ) {
		return $this->import( $file, array( 'users' => true ) );
	}

	/**
	 * Import categories only.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Path to the import file.
	 */
	public function import_categories( $file ) {
		return $this->import( $file, array( 'categories' => true ) );
	}

	/**
	 * Import tags only.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Path to the import file.
	 */
	public function import_tags( $file ) {
		return $this->import( $file, array( 'tags' => true ) );
	}

	/**
	 * Import terms only.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Path to the import file.
	 */
	public function import_terms( $file ) {
		return $this->import( $file, array( 'terms' => true ) );
	}

	/**
	 * Import posts only.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Path to the import file.
	 */
	public function import_posts( $file ) {
		return $this->import( $file, array( 'posts' => true ) );
	}

	/**
	 * Check if we need to create a new AJAX request, so that server does not timeout.
	 * And fix the import warning for missing post author.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data current post data.
	 * @return array
	 */
	public function new_ajax_request_maybe( $data ) {
		$time = microtime( true ) - $this->start_time;

		// We should make a new ajax call, if the time is right.
		if ( $time > apply_filters( 'pt-importer/time_for_one_ajax_call', 25 ) ) {
			$response = apply_filters( 'pt-importer/new_ajax_request_response_data', array(
				'status'                => 'newAJAX',
				'log'                   => 'Time for new AJAX request!: ' . $time,
				'num_of_imported_posts' => count( $this->mapping['post'] ),
			) );

			// Add message to log file.
			Logger::info( 'New AJAX call!' );

			// Set the current importer state, so it can be continued on the next AJAX call.
			$this->set_current_importer_data();

			// Send the request for a new AJAX call.
			wp_send_json( $response );
		}

		// Set importing author to the current user.
		// Fixes the [WARNING] Could not find the author for ... log warning messages.
		$current_user_obj    = wp_get_current_user();
		$data['post_author'] = $current_user_obj->user_login;

		return $data;
	}

	/**
	 * Save current importer data to the DB, for later use.
	 *
	 * @since 1.0.0
	 */
	public function set_current_importer_data() {
		$data = apply_filters( 'pt-importer/set_current_importer_data', array(
			'options'            => $this->options,
			'mapping'            => $this->mapping,
			'requires_remapping' => $this->requires_remapping,
			'exists'             => $this->exists,
			'user_slug_override' => $this->user_slug_override,
			'url_remap'          => $this->url_remap,
			'featured_images'    => $this->featured_images,
		) );

		$this->save_current_import_data_transient( $data );
	}

	/**
	 * Set the importer data to the transient.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Data to be saved to the transient.
	 */
	public function save_current_import_data_transient( $data ) {
		update_option( 'pt_importer_data', $data, false );
	}

	/**
	 * Restore the importer data from the transient.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function restore_import_data_transient() {
		if ( $data = get_option( 'pt_importer_data' ) ) {
			$this->options            = empty( $data['options'] ) ? array() : $data['options'];
			$this->mapping            = empty( $data['mapping'] ) ? array() : $data['mapping'];
			$this->requires_remapping = empty( $data['requires_remapping'] ) ? array() : $data['requires_remapping'];
			$this->exists             = empty( $data['exists'] ) ? array() : $data['exists'];
			$this->user_slug_override = empty( $data['user_slug_override'] ) ? array() : $data['user_slug_override'];
			$this->url_remap          = empty( $data['url_remap'] ) ? array() : $data['url_remap'];
			$this->featured_images    = empty( $data['featured_images'] ) ? array() : $data['featured_images'];

			do_action( 'pt-importer/restore_import_data_transient' );

			return true;
		}

		return false;
	}

	/**
	 * Get the importer mapping data.
	 *
	 * @since 1.0.0
	 *
	 * @return array An empty array or an array of mapping data.
	 */
	public function get_mapping() {
		return $this->mapping;
	}

	/**
	 * Hook into the pre-process term filter of the content import and register the
	 * custom WooCommerce product attributes, so that the terms can then be imported normally.
	 *
	 * This should probably be removed once the WP importer 2.0 support is added in WooCommerce.
	 *
	 * Fixes: [WARNING] Failed to import pa_size L warnings in content import.
	 * Code from: woocommerce/includes/admin/class-wc-admin-importers.php (ver 2.6.9).
	 *
	 * Github issue: https://github.com/proteusthemes/one-click-demo-import/issues/71
	 *
	 * @since 1.0.0
	 *
	 * @param  array $date The term data to import.
	 * @return array       The unchanged term data.
	 */
	public function woocommerce_product_attributes_registration( $data ) {
		global $wpdb;

		if ( strstr( $data['taxonomy'], 'pa_' ) ) {
			if ( ! taxonomy_exists( $data['taxonomy'] ) ) {
				$attribute_name = wc_sanitize_taxonomy_name( str_replace( 'pa_', '', $data['taxonomy'] ) );

				// Create the taxonomy
				if ( ! in_array( $attribute_name, wc_get_attribute_taxonomies() ) ) {
					$attribute = array(
						'attribute_label'   => $attribute_name,
						'attribute_name'    => $attribute_name,
						'attribute_type'    => 'select',
						'attribute_orderby' => 'menu_order',
						'attribute_public'  => 0
					);
					$wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );
					delete_transient( 'wc_attribute_taxonomies' );
				}

				// Register the taxonomy now so that the import works!
				register_taxonomy(
					$data['taxonomy'],
					apply_filters( 'woocommerce_taxonomy_objects_' . $data['taxonomy'], array( 'product' ) ),
					apply_filters( 'woocommerce_taxonomy_args_' . $data['taxonomy'], array(
						'hierarchical' => true,
						'show_ui'      => false,
						'query_var'    => true,
						'rewrite'      => false,
					) )
				);
			}
		}

		return $data;
	}

	/**
	 * Delete import temp folder.
	 *
	 * @since 1.0.0
	 */
	public function cmsmasters_delete_import_temp_folder() {
		$wp_filesystem = File_Manager::get_wp_filesystem();

		if ( ! $wp_filesystem ) {
			return false;
		}

		$wp_filesystem->delete( $this->cmsmasters_wp_upload_basedir . $this->cmsmasters_temp_folder_path, true );
	}

}
