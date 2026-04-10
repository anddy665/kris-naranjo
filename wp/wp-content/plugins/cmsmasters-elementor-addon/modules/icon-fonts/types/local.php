<?php
namespace CmsmastersElementor\Modules\IconFonts\Types;

use CmsmastersElementor\Base\Base_Actions;
use CmsmastersElementor\Modules\IconFonts\Module as IconFontsModule;
use CmsmastersElementor\Modules\IconFonts\Services\Base\Base_Service;
use CmsmastersElementor\Modules\Wordpress\Managers\Fields_Manager;
use CmsmastersElementor\Modules\Wordpress\Managers\Post_Meta_Manager;
use CmsmastersElementor\Modules\Wordpress\Module as WordpressModule;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Core\Common\Modules\Ajax\Module as Ajax;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Local extends Base_Actions {

	const CPT = 'cmsmasters_icons';

	const ICONS_OPTION_NAME = 'cmsmasters_local_icons_config';

	const ICONS_META_KEY = 'cmsmasters_icons_set_config';

	/**
	 * Available services.
	 *
	 * @since 1.0.0
	 *
	 * @var Base_Service[]
	 */
	private static $services = array();

	public $cpt_labels = array();

	public $cpt_title = '';

	public $finder_keywords = array();

	public $meta_box = array();

	public $current_post_id = 0;

	/**
	 * Addon WordPress module.
	 *
	 * @since 1.0.0
	 *
	 * @var WordpressModule
	 */
	private $wordpress_module;

	/**
	 * Addon WordPress post meta manager.
	 *
	 * @since 1.0.0
	 *
	 * @var Post_Meta_Manager
	 */
	private $postmeta;

	public function get_name() {
		return __( 'Local Icons', 'cmsmasters-elementor' );
	}

	public function get_type() {
		return 'local';
	}

	/**
	 * @since 1.0.1
	 */
	public function get_cpt() {
		return self::CPT;
	}

	public function __construct() {
		$this->init_services();

		$this->wordpress_module = WordpressModule::instance();

		$this->set_module_post_type();

		parent::__construct();
	}

	private function init_services() {
		$services = array(
			'icomoon',
			'fontastic',
			'fontello',
		);

		foreach ( $services as $service ) {
			self::$services[ $service ] = sprintf(
				'%1$s\Services\%2$s',
				IconFontsModule::MODULE_NAMESPACE,
				Utils::generate_class_name( $service )
			);
		}
	}

	private function set_module_post_type() {
		$this->cpt_labels = array(
			'name' => _x( 'Local Icons', 'Custom Post Type Name', 'cmsmasters-elementor' ),
			'singular_name' => _x( 'Icons Set', 'Custom Post Type Singular Name', 'cmsmasters-elementor' ),
			'add_new' => __( 'Add New', 'cmsmasters-elementor' ),
			'add_new_item' => __( 'Add New Icons Set', 'cmsmasters-elementor' ),
			'edit_item' => __( 'Edit Icons Set', 'cmsmasters-elementor' ),
			'new_item' => __( 'New Icons Set', 'cmsmasters-elementor' ),
			'all_items' => __( 'All Icons', 'cmsmasters-elementor' ),
			'view_item' => __( 'View Icons Set', 'cmsmasters-elementor' ),
			'search_items' => __( 'Search Icons Set', 'cmsmasters-elementor' ),
			'not_found' => __( 'No icons found', 'cmsmasters-elementor' ),
			'not_found_in_trash' => __( 'No icons found in trash', 'cmsmasters-elementor' ),
			'parent_item_colon' => '',
			'menu_name' => _x( 'Local Icons', 'Custom Post Type Menu Name', 'cmsmasters-elementor' ),
		);

		$this->cpt_title = $this->cpt_labels['menu_name'];
		$this->finder_keywords = array(
			'icons',
			'local',
			'custom',
		);

		$this->wordpress_module->get_post_types_manager()->init( $this );
	}

	protected function init_actions() {
		// Admin
		if ( is_admin() ) {
			add_action( 'add_meta_boxes_' . self::CPT, array( $this, 'add_meta_box' ) );
			add_action( 'save_post_' . self::CPT, array( $this, 'save_post_meta' ), 20 );
			add_action( 'admin_head', array( $this, 'modify_post_type_admin_pages' ) );
			add_action( 'current_screen', array( $this, 'add_local_icons_templates' ) );
		}

		// Admin Delete & Status Change
		add_action( 'transition_post_status', array( $this, 'transition_post_status' ), 10, 3 );
		add_action( 'before_delete_post', array( $this, 'handle_delete_icon_set' ) );

		// Ajax.
		add_action( 'elementor/ajax/register_actions', array( $this, 'register_ajax_actions' ) );
	}

	protected function init_filters() {
		// Admin
		if ( is_admin() ) {
			add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		}

		// Editor
		add_filter( 'elementor/icons_manager/additional_tabs', array( $this, 'register_icon_libraries_control' ) );

		if ( ! Utils::is_pro() ) {
			// Editor
			add_filter( 'elementor/editor/localize_settings', array( $this, 'add_custom_icons_button_url' ) );
		}
	}

	public function add_meta_box() {
		$this->set_module_post_meta();
	}

	private function set_module_post_meta() {
		$this->meta_box = array(
			'name' => 'cmsmasters-local-icons-metabox',
			'label' => __( 'Icons Set', 'cmsmasters-elementor' ),
		);

		$this->postmeta = $this->wordpress_module->get_post_meta_manager( $this );

		$this->register_meta_fields();

		$this->postmeta->render();
	}

	private function register_meta_fields() {
		$this->postmeta->open_fields_box( $this->meta_box['name'] );

		$this->postmeta->add_field(
			'open_div',
			Fields_Manager::HTML_TAG,
			array(
				'attributes' => array( 'class' => $this->meta_box['name'] ),
			)
		);

		$this->postmeta->add_field(
			'zip_upload',
			Fields_Manager::DROPZONE,
			array(
				'accept' => implode( ',', array(
					'zip',
					'application/zip',
					'application/x-zip',
					'application/x-zip-compressed',
					'application/octet-stream',
				) ),
				'description' => sprintf(
					/* translators: Addon Local Icons Set dropbox meta field description. %s: links to available services */
					__( 'you can use %s .zip files', 'cmsmasters-elementor' ),
					self::get_upload_field_services()
				),
			)
		);

		$this->postmeta->add_field(
			'close_div',
			Fields_Manager::HTML_TAG,
			array( 'close' => true )
		);

		$this->postmeta->add_field(
			self::ICONS_META_KEY,
			Fields_Manager::INPUT,
			array(
				'type' => 'hidden',
				'value' => self::get_icons_set_config( get_the_ID() ),
			)
		);

		$this->postmeta->add_field(
			self::CPT . '_nonce',
			Fields_Manager::INPUT,
			array(
				'type' => 'hidden',
				'value' => wp_create_nonce( self::CPT ),
			)
		);
	}

	public static function get_upload_field_services() {
		$links = array();

		foreach ( self::$services as $service ) {
			$links[] = sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( $service::get_link() ), esc_attr( $service::get_type() ) );
		}

		$last_link = array_pop( $links );
		$services = implode( ', ', $links );

		return sprintf( "{$services} %s {$last_link}", __( 'or', 'cmsmasters-elementor' ) );
	}

	public static function get_icons_set_config( $id ) {
		return get_post_meta( $id, self::ICONS_META_KEY, true );
	}

	public function save_post_meta( $post_id ) {
		// If this is an autosave, our form has not been submitted,
		// so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Check if our nonce is set and verify if it is valid.
		if (
			! isset( $_POST[ self::CPT . '_nonce' ] ) ||
			! wp_verify_nonce( $_POST[ self::CPT . '_nonce' ], self::CPT )
		) {
			return $post_id;
		}

		// Remove post meta if no data
		if ( ! isset( $_POST[ self::ICONS_META_KEY ] ) ) {
			return delete_post_meta( $post_id, self::ICONS_META_KEY );
		}

		// Sanitize json data
		$json = json_decode( stripslashes_deep( $_POST[ self::ICONS_META_KEY ] ), true );

		foreach ( $json as $property => $value ) {
			$json[ $property ] = Post_Meta_Manager::sanitize_field_recursive( $value );
		}

		// All good save the files array
		update_post_meta( $post_id, self::ICONS_META_KEY, wp_json_encode( $json ) );

		// Force refresh of list in options table
		self::remove_local_icons_config_option();
	}

	public static function remove_local_icons_config_option() {
		delete_option( self::ICONS_OPTION_NAME );
	}

	/**
	 * Regenerate local icons.
	 *
	 * @since 1.17.4
	 */
	public static function regenerate_local_icons() {
		$icons = new \WP_Query( array(
			'post_type' => self::CPT,
			'posts_per_page' => -1,
			'post_status' => 'publish',
		) );

		foreach ( $icons->posts as $icon_set ) {
			$icon_set_config = json_decode( self::get_icons_set_config( $icon_set->ID ), true );

			$uploads_url = wp_get_upload_dir()['baseurl'];
			$pattern = '/^.*?(?=elementor\/cmsmasters-local-icons)/';

			$icon_set_config['url'] = preg_replace( $pattern, $uploads_url . '/', $icon_set_config['url'] );

			update_post_meta( $icon_set->ID, self::ICONS_META_KEY, wp_json_encode( $icon_set_config ) );
		}

		self::remove_local_icons_config_option();
		self::get_local_icons_config();
	}

	/**
	 * Modify Local Icons post type admin listing page
	 */
	public function modify_post_type_admin_pages() {
		global $typenow;

		if ( self::CPT !== $typenow ) {
			return;
		}

		// Listing
		add_filter( 'months_dropdown_results', '__return_empty_array' );
		add_filter( 'screen_options_show_screen', '__return_false' );

		// Listing Columns
		add_filter( 'manage_' . self::CPT . '_posts_columns', array( $this, 'manage_columns' ), 100 );
		add_action( 'manage_' . self::CPT . '_posts_custom_column', array( $this, 'render_columns' ), 10, 2 );

		// Listing Names
		add_filter( 'display_post_states', array( $this, 'display_post_states' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'listing_hide_quick_edit' ), 10, 2 );

		// Singular
		add_filter( 'enter_title_here', array( $this, 'update_default_title_text' ), 10, 2 );
	}

	/**
	 * Define which columns to display in font manager admin listing
	 *
	 * @return array
	 */
	public function manage_columns() {
		return array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Icons Set Name', 'cmsmasters-elementor' ),
			'icons-set-preview' => __( 'Preview', 'cmsmasters-elementor' ),
			'icons-set-prefix' => __( 'CSS Prefix', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * Render preview column in local icons post type admin listing
	 *
	 * @param $column
	 * @param $post_id
	 */
	public function render_columns( $column, $post_id ) {
		$data = json_decode( self::get_icons_set_config( $post_id ) );

		if ( 'icons-set-preview' === $column ) {
			if (
				! empty( $data->count ) &&
				! empty( $data->prefix ) &&
				! empty( $data->icons ) &&
				! empty( $data->name ) &&
				! empty( $data->url ) &&
				! empty( $data->ver )
			) {
				echo '<span>';

				$count = ( 20 > $data->count ) ? $data->count : 20;

				for ( $i = 0; $i < $count; $i++ ) {
					printf( '<i class="%1$s%2$s"></i>', $data->prefix, $data->icons[ $i ] );

					wp_enqueue_style(
						"cmsmasters-admin-icons-{$data->name}",
						$data->url,
						array(),
						$data->ver
					);
				}

				echo '</span>';
			}
		}

		if ( 'icons-set-prefix' === $column ) {
			if ( ! empty( $data->prefix ) ) {
				printf( '<pre>%s</pre>', esc_html( '.' . $data->prefix ) );
			}
		}
	}

	public function display_post_states( $post_states, $post ) {
		if (
			'publish' !== $post->post_status ||
			self::CPT !== $post->post_type
		) {
			return $post_states;
		}

		$data = json_decode( self::get_icons_set_config( $post->ID ) );

		if ( ! empty( $data->count ) ) {
			printf( '<span class="items-count">%d</span>', $data->count );
		}

		return $post_states;
	}

	public function listing_hide_quick_edit( $actions, $post ) {
		if ( self::CPT !== $post->post_type ) {
			return $actions;
		}

		unset( $actions['inline hide-if-no-js'] );

		return $actions;
	}

	public function update_default_title_text( $title, $post ) {
		if (
			isset( $post->post_type ) &&
			self::CPT === $post->post_type
		) {
			return __( 'Icons Set name', 'cmsmasters-elementor' );
		}

		return $title;
	}

	public function add_local_icons_templates( $current_screen ) {
		if (
			'post' !== $current_screen->base ||
			self::CPT !== $current_screen->id
		) {
			return;
		}

		Plugin::elementor()->common->add_template( IconFontsModule::MODULE_DIR . '/templates/templates.php' );
	}

	public function transition_post_status( $new_status, $old_status, $post ) {
		if ( self::CPT !== $post->post_type ) {
			return;
		}

		if ( 'publish' === $old_status && 'publish' !== $new_status ) {
			self::remove_local_icons_config_option();
		}
	}

	public function handle_delete_icon_set( $post_id ) {
		if ( self::CPT !== get_post_type( $post_id ) ) {
			return;
		}

		// remove all assets related to this icon set
		$attachments = get_attached_media( '', $post_id );

		foreach ( $attachments as $attachment ) {
			wp_delete_attachment( $attachment->ID, 'true' );
		}

		// remove icon set assets directory
		$icons_set_dir = get_post_meta( $post_id, '_cmsmasters_icons_set_path', true );

		if ( ! empty( $icons_set_dir ) && is_dir( $icons_set_dir ) ) {
			Utils::get_wp_filesystem()->rmdir( $icons_set_dir, true );
		}

		// Force refresh of local icons list in options table
		self::remove_local_icons_config_option();
	}

	public function register_ajax_actions( Ajax $ajax ) {
		$ajax->register_ajax_action( 'cmsmasters_icon_fonts_local_icons_set_upload', array( $this, 'local_icons_set_upload_handler' ) );
	}

	public function local_icons_set_upload_handler( $data ) {
		$this->current_post_id = $data['post_id'];

		$extract_directory = Utils::upload_and_extract_zip();

		if ( is_wp_error( $extract_directory ) ) {
			return $extract_directory;
		}

		foreach ( self::get_supported_services() as $handler ) {
			/**
			 * @var Base_Service $service_handler
			 */
			$service_handler = new $handler( $extract_directory );

			if ( ! $service_handler || ! $service_handler->is_valid() ) {
				continue;
			}

			$service_handler->handle_new_icon_set();

			$service_handler->move_files( $this->current_post_id );

			$config = $service_handler->build_config();

			// Notify about duplicate prefix
			if ( self::icons_set_prefix_exists( $config['prefix'] ) ) {
				$config['duplicatedPrefix'] = true;
			}

			return array( 'config' => $config );
		}

		return new \WP_Error( 'unsupported_zip_format', __( 'The zip file provided is not supported!', 'cmsmasters-elementor' ) );
	}

	public static function get_supported_services() {
		$additional_services = apply_filters( 'cmsmasters_elementor/icon_fonts/local_icons/additional_services', array() );

		return array_merge( $additional_services, self::$services );
	}

	public static function icons_set_prefix_exists( $prefix ) {
		$config = self::get_local_icons_config();

		if ( empty( $config ) ) {
			return false;
		}

		foreach ( $config as $icon_config ) {
			if ( $prefix === $icon_config['prefix'] ) {
				return true;
			}
		}

		return false;
	}

	public function post_updated_messages( $messages ) {
		$revision = false;

		if ( isset( $_GET['revision'] ) ) {
			$revision = sprintf(
				/* translators: Addon Local Icons post type revision message. %s: date and time of the revision */
				__( 'Icons Set restored to revision from %s.', 'cmsmasters-elementor' ),
				wp_post_revision_title( (int) $_GET['revision'], false )
			);
		}

		$messages[ self::CPT ] = array(
			0 => '', // Unused. Messages starts at index 1.
			1 => __( 'Icons Set updated.', 'cmsmasters-elementor' ),
			2 => __( 'Custom field updated.', 'cmsmasters-elementor' ),
			3 => __( 'Custom field deleted.', 'cmsmasters-elementor' ),
			4 => __( 'Icons Set updated.', 'cmsmasters-elementor' ),
			5 => $revision,
			6 => __( 'Icons Set saved.', 'cmsmasters-elementor' ),
			7 => __( 'Icons Set saved.', 'cmsmasters-elementor' ),
			8 => __( 'Icons Set submitted.', 'cmsmasters-elementor' ),
			9 => __( 'Icons Set updated.', 'cmsmasters-elementor' ),
			10 => __( 'Icons Set draft updated.', 'cmsmasters-elementor' ),
		);

		return $messages;
	}

	public function register_icon_libraries_control( $additional_sets ) {
		return array_merge( $additional_sets, self::get_local_icons_config() );
	}

	public static function get_local_icons_config() {
		$config = get_option( self::ICONS_OPTION_NAME, false );

		if ( false === $config ) {
			$icons = new \WP_Query( array(
				'post_type' => self::CPT,
				'posts_per_page' => -1,
				'post_status' => 'publish',
			) );

			$config = array();

			foreach ( $icons->posts as $icon_set ) {
				$icon_set_config = json_decode( self::get_icons_set_config( $icon_set->ID ), true );

				$icon_set_config['custom_icon_post_id'] = $icon_set->ID;
				$icon_set_config['label'] = $icon_set->post_title;

				if ( isset( $icon_set_config['fetchJson'] ) ) {
					unset( $icon_set_config['icons'] );
				}

				$config[ $icon_set_config['name'] ] = $icon_set_config;
			}

			update_option( self::ICONS_OPTION_NAME, $config );
		}

		return $config;
	}

	public function add_custom_icons_button_url( $config ) {
		$config['customIconsURL'] = admin_url( 'edit.php?post_type=' . self::CPT );

		return $config;
	}

}
