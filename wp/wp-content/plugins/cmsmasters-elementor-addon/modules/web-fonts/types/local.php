<?php
namespace CmsmastersElementor\Modules\WebFonts\Types;

use CmsmastersElementor\Base\Base_Actions;
use CmsmastersElementor\Modules\WebFonts\Module as WebFontsModule;
use CmsmastersElementor\Modules\WebFonts\Services\Base\Base_Service;
use CmsmastersElementor\Modules\Wordpress\Managers\Fields_Manager;
use CmsmastersElementor\Modules\Wordpress\Managers\Post_Meta_Manager;
use CmsmastersElementor\Modules\Wordpress\Module as WordpressModule;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use Elementor\Core\Files\CSS\Base as CSSFilesBase;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Local extends Base_Actions {

	const CPT = 'cmsms_local_fonts';

	const FONTS_OPTION_NAME = 'cmsmasters_local_fonts_config';

	const FONTS_META_KEY = 'cmsmasters_local_font_config';

	const STYLES_META_KEY = 'cmsmasters_local_font_styles';

	const FONT_PATH_META_KEY = '_cmsmasters_local_font_path';

	/**
	 * Available services.
	 *
	 * @since 1.0.0
	 *
	 * @var Base_Service[]
	 */
	private static $services = array();

	private static $supported_formats = array();

	private $enqueued_fonts = array();

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

	public static function get_name() {
		return __( 'Local Fonts', 'cmsmasters-elementor' );
	}

	public static function get_type() {
		return 'local';
	}

	/**
	 * @since 1.0.1
	 *
	 * @return string
	 */
	public function get_cpt() {
		return self::CPT;
	}

	public function __construct() {
		$this->init_services();
		$this->set_supported_formats();

		$this->wordpress_module = WordpressModule::instance();

		$this->set_module_post_type();

		parent::__construct();
	}

	private function init_services() {
		$services = array(
			'transfonter',
			'fontsquirrel',
		);

		foreach ( $services as $service ) {
			self::$services[ $service ] = sprintf(
				'%1$s\Services\%2$s',
				WebFontsModule::MODULE_NAMESPACE,
				Utils::generate_class_name( $service )
			);
		}
	}

	private function set_supported_formats() {
		$supported_formats = array(
			'woff2',
			'woff',
			'ttf',
			'otf',
			'svg',
			'eot',
		);

		self::$supported_formats = apply_filters( 'cmsmasters_elementor/web_fonts/local_fonts/supported_font_formats', $supported_formats );
	}

	public static function get_supported_formats() {
		return self::$supported_formats;
	}

	public static function is_format_supported( $format ) {
		$supported_formats = self::get_supported_formats();

		return in_array( $format, $supported_formats, true );
	}

	private function set_module_post_type() {
		$this->cpt_labels = array(
			'name' => _x( 'Local Fonts', 'Custom Post Type Name', 'cmsmasters-elementor' ),
			'singular_name' => _x( 'Local Font', 'Custom Post Type Singular Name', 'cmsmasters-elementor' ),
			'add_new' => __( 'Add New', 'cmsmasters-elementor' ),
			'add_new_item' => __( 'Add New Font', 'cmsmasters-elementor' ),
			'edit_item' => __( 'Edit Font', 'cmsmasters-elementor' ),
			'new_item' => __( 'New Font', 'cmsmasters-elementor' ),
			'all_items' => __( 'All Fonts', 'cmsmasters-elementor' ),
			'view_item' => __( 'View Font', 'cmsmasters-elementor' ),
			'search_items' => __( 'Search Font', 'cmsmasters-elementor' ),
			'not_found' => __( 'No fonts found', 'cmsmasters-elementor' ),
			'not_found_in_trash' => __( 'No fonts found in trash', 'cmsmasters-elementor' ),
			'parent_item_colon' => '',
			'menu_name' => _x( 'Local Fonts', 'Custom Post Type Menu Name', 'cmsmasters-elementor' ),
		);

		$this->cpt_title = $this->cpt_labels['menu_name'];
		$this->finder_keywords = array(
			'fonts',
			'custom',
			'local',
			'web',
			'self-hosted',
		);

		$this->wordpress_module->get_post_types_manager()->init( $this );
	}

	protected function init_actions() {
		// Admin
		if ( is_admin() ) {
			add_action( 'add_meta_boxes_' . self::CPT, array( $this, 'add_meta_box' ) );
			add_action( 'save_post_' . self::CPT, array( $this, 'save_post_meta' ), 20 );
			add_action( 'admin_head', array( $this, 'modify_post_type_admin_pages' ) );
			add_action( 'current_screen', array( $this, 'add_local_font_templates' ) );
		}

		// Admin Delete & Status Change
		add_action( 'transition_post_status', array( $this, 'transition_post_status' ), 10, 3 );
		add_action( 'before_delete_post', array( $this, 'handle_delete_font' ) );

		// Common
		add_action( 'elementor/css-file/post/parse', array( $this, 'enqueue_fonts' ) );
		add_action( 'elementor/css-file/global/parse', array( $this, 'enqueue_fonts' ) );

		// Ajax
		add_action( 'elementor/ajax/register_actions', array( $this, 'register_ajax_actions' ) );
	}

	protected function init_filters() {
		// Admin
		if ( is_admin() ) {
			add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		}

		// Editor
		add_filter( 'elementor/fonts/groups', array( $this, 'register_fonts_groups' ) );
		add_filter( 'elementor/fonts/additional_fonts', array( $this, 'register_fonts_in_control' ) );
	}

	public function add_meta_box() {
		$this->set_module_post_meta();
	}

	private function set_module_post_meta() {
		$this->meta_box = array(
			'name' => 'cmsmasters-local-font-metabox',
			'label' => __( 'Local Font', 'cmsmasters-elementor' ),
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
					/* translators: Addon Local Font dropbox meta field description. %s: links to available services */
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
			'fonts_description',
			Fields_Manager::HTML_TAG,
			array(
				'tag' => 'div',
				'content' => $this->get_fonts_description(),
				'attributes' => array(
					'class' => 'fonts_description',
					'style' => 'display: none;', // temp
				),
			)
		);

		$this->postmeta->add_field(
			self::FONTS_META_KEY,
			Fields_Manager::INPUT,
			array(
				'type' => 'hidden',
				'value' => self::get_local_font_config( get_the_ID() ),
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

		$styles = self::get_local_font_styles( get_the_ID() );

		if ( $styles ) {
			$this->postmeta->add_field(
				'font_style_tag',
				Fields_Manager::HTML_TAG,
				array(
					'tag' => 'style',
					'content' => $styles,
					'attributes' => array( 'type' => 'text/css' ),
				)
			);
		}
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

	public function get_fonts_description() {
		$types = self::get_file_type_description();
		$separator = false;
		$description = '';

		foreach ( $types as $format => $text ) {
			if ( ! self::is_format_supported( $format ) ) {
				continue;
			}

			if ( $separator ) {
				$description .= '<br>';
			} else {
				$separator = true;
			}

			$description .= sprintf( '<strong>%1$s</strong> - %2$s', $format, $text );
		}

		return $description;
	}

	public static function get_file_type_description( $format = false ) {
		$descriptions = array(
			'woff2' => __( 'The Web Open Font Format 2.0, Provides 30% Better Compression and Used by Super Modern Browsers.', 'cmsmasters-elementor' ),
			'woff' => __( 'The Web Open Font Format, Used by All Modern Browsers.', 'cmsmasters-elementor' ),
			'ttf' => __( 'TrueType Fonts, Used for better support of Safari, Android and iOS.', 'cmsmasters-elementor' ),
			'otf' => __( 'OpenType Fonts, Used for better support of Safari, Android and iOS.', 'cmsmasters-elementor' ),
			'svg' => __( 'SVG fonts allow SVG to be used as glyphs when displaying text, Used by Legacy iOS', 'cmsmasters-elementor' ),
			'eot' => __( 'Embedded OpenType, Used by IE6-IE9 Browsers', 'cmsmasters-elementor' ),
		);

		$descriptions = apply_filters( 'cmsmasters_elementor/web_fonts/local_fonts/file_type_descriptions', $descriptions );

		if ( $format ) {
			return Utils::get_if_isset( $descriptions, $format, false );
		}

		return $descriptions;
	}

	public static function get_local_font_config( $id ) {
		return get_post_meta( $id, self::FONTS_META_KEY, true );
	}

	public static function get_local_font_styles( $id ) {
		return get_post_meta( $id, self::STYLES_META_KEY, true );
	}

	public static function get_local_font_config_json( $id, $assoc = false ) {
		return json_decode( self::get_local_font_config( $id ), $assoc );
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

		// Check if nonce is set and verify if it is valid
		if (
			! isset( $_POST[ self::CPT . '_nonce' ] ) ||
			! wp_verify_nonce( $_POST[ self::CPT . '_nonce' ], self::CPT )
		) {
			return $post_id;
		}

		// Remove post meta if no data
		if ( ! isset( $_POST[ self::FONTS_META_KEY ] ) ) {
			delete_post_meta( $post_id, self::FONTS_META_KEY );
			delete_post_meta( $post_id, self::STYLES_META_KEY );

			return $post_id;
		}

		// Sanitize json data
		$json = json_decode( stripslashes_deep( $_POST[ self::FONTS_META_KEY ] ), true );

		foreach ( $json as $property => $value ) {
			$json[ $property ] = Post_Meta_Manager::sanitize_field_recursive( $value );
		}

		// All good save the files array
		update_post_meta( $post_id, self::FONTS_META_KEY, wp_json_encode( $json ) );
		update_post_meta( $post_id, self::STYLES_META_KEY, self::generate_font_faces( $json ) );

		// Force refresh of list in options table
		self::refresh_local_fonts_config();
	}

	public static function refresh_local_fonts_config() {
		self::remove_local_fonts_config();
		self::generate_local_fonts_config();
	}

	public static function remove_local_fonts_config() {
		delete_option( self::FONTS_OPTION_NAME );
	}

	private static function generate_local_fonts_config() {
		$fonts = new \WP_Query( array(
			'post_type' => self::CPT,
			'posts_per_page' => -1,
			'post_status' => 'publish',
		) );

		$config = array();

		foreach ( $fonts->posts as $font ) {
			$font_config = self::get_local_font_config_json( $font->ID, true );

			$font_config['id'] = $font->ID;

			$config[ $font_config['name'] ] = $font_config;
		}

		update_option( self::FONTS_OPTION_NAME, $config );

		return $config;
	}

	/**
	 * Regenerate local fonts.
	 *
	 * @since 1.17.4
	 */
	public static function regenerate_local_fonts() {
		$fonts = new \WP_Query( array(
			'post_type' => self::CPT,
			'posts_per_page' => -1,
			'post_status' => 'publish',
		) );

		foreach ( $fonts->posts as $font ) {
			$font_config = self::get_local_font_config_json( $font->ID, true );
			$uploads_url = wp_get_upload_dir()['baseurl'];
			$pattern = '/^.*?(?=elementor\/cmsmasters-local-fonts)/';

			$font_config['url'] = preg_replace( $pattern, $uploads_url . '/', $font_config['url'] );
			$font_config['dir'] = preg_replace( $pattern, $uploads_url . '/', $font_config['dir'] );

			update_post_meta( $font->ID, self::FONTS_META_KEY, wp_json_encode( $font_config ) );
			update_post_meta( $font->ID, self::STYLES_META_KEY, self::generate_font_faces( $font_config ) );
		}

		self::refresh_local_fonts_config();
	}

	public static function generate_font_faces( $font_json ) {
		if ( ! is_array( $font_json ) ) {
			return false;
		}

		$font_faces = '';

		foreach ( $font_json['font_faces'] as $font ) {
			if ( ! isset( $font['src'] ) ) {
				continue;
			}

			$font_face = sprintf( '@font-face {%s', PHP_EOL ) .
				sprintf( "\tfont-family: '%s';%s", $font_json['label'], PHP_EOL ) .
				sprintf( "\tfont-weight: %s;%s", $font['font-weight'], PHP_EOL ) .
				sprintf( "\tfont-style: %s;%s", $font['font-style'], PHP_EOL ) .
				self::generate_font_sources( $font, $font_json ) .
			'}';

			$font_faces .= sprintf( "{$font_face}%s", PHP_EOL . PHP_EOL );
		}

		return $font_faces;
	}

	private static function generate_font_sources( $font, $json ) {
		$sources = '';

		$src = $font['src'];
		$urls = Utils::get_if_isset( $src, 'url', false );

		if (
			in_array( 'eot', self::$supported_formats, true ) &&
			$urls &&
			isset( $urls['eot'] )
		) {
			$sources .= sprintf( "\tsrc: url('%s');%s", esc_url( $json['dir'] . $urls['eot'] ), PHP_EOL );
		}

		$source_array = array();

		$locals = Utils::get_if_isset( $src, 'local', false );

		if ( $locals ) {
			foreach ( $locals as $local ) {
				$source_array[] = sprintf( "local('%s')", esc_attr( $local ) );
			}
		}

		foreach ( self::set_formats_order( $json['formats'] ) as $type ) {
			if (
				! in_array( $type, self::$supported_formats, true ) ||
				! $urls ||
				! isset( $urls[ $type ] )
			) {
				continue;
			}

			if ( 'svg' === $type ) {
				$urls[ $type ] .= sprintf( '#%s', str_replace( ' ', '', strtolower( $json['label'] ) ) );
			}

			$source_array[] = self::get_font_url( $type, $json['dir'] . $urls[ $type ] );
		}

		if ( ! empty( $source_array ) ) {
			$sources_str = implode( sprintf( ",%s\t\t", PHP_EOL ), $source_array );

			$sources .= sprintf( "\tsrc: {$sources_str};%s", PHP_EOL );
		}

		return $sources;
	}

	private static function set_formats_order( $formats ) {
		$eot = array_search( 'eot', $formats, true );

		if ( $eot || 0 === $eot ) {
			unset( $formats[ $eot ] );

			array_unshift( $formats, 'eot' );
		}

		$svg = array_search( 'svg', $formats, true );

		if ( $svg || 0 === $svg ) {
			unset( $formats[ $svg ] );

			$formats[] = 'svg';
		}

		return $formats;
	}

	private static function get_font_url( $type, $url ) {
		$src = sprintf( "url('%s') ", esc_url( $url ) );

		switch ( $type ) {
			case 'woff2':
			case 'woff':
			case 'svg':
				$src .= "format('{$type}')";

				break;
			case 'ttf':
				$src .= "format('truetype')";

				break;
			case 'otf':
				$src .= "format('opentype')";

				break;
			case 'eot':
				$src = sprintf( "url('%s?#iefix') format('embedded-opentype')", esc_url( $url ) );

				break;
		}

		return $src;
	}

	/**
	 * Modify Local Fonts post type admin listing page
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
			'title' => __( 'Font Family', 'cmsmasters-elementor' ),
			'local-font-styles' => __( 'Styles', 'cmsmasters-elementor' ),
			'local-font-preview' => __( 'Preview', 'cmsmasters-elementor' ),
			'local-font-files' => __( 'Formats', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * Render preview column in local fonts post type admin listing
	 *
	 * @param $column
	 * @param $post_id
	 */
	public function render_columns( $column, $post_id ) {
		$data = self::get_local_font_config_json( $post_id );

		if ( 'local-font-styles' === $column ) {
			$weight_labels = WebFontsModule::get_font_weight_labels();

			foreach ( $data->styles as $font_style ) {
				$style = ( 'normal' !== $font_style->style ) ?
					sprintf( ' %s', ucfirst( $font_style->style ) ) :
					'';

				printf(
					'<span style="font-style: %1$s;"><strong>%2$s</strong> %3$s%4$s</span>',
					$font_style->style,
					$font_style->weight,
					$weight_labels[ $font_style->weight ],
					$style
				);
			}
		}

		if ( 'local-font-preview' === $column ) {
			$font_styles = self::get_local_font_styles( $post_id );

			if ( ! $font_styles ) {
				return;
			}

			foreach ( $data->styles as $font_style ) {
				echo Utils::generate_html_tag(
					'span',
					array(
						'style' => sprintf(
							'font-family: \'%1$s\'; font-weight: %2$s; font-style: %3$s;',
							get_the_title( $post_id ),
							$font_style->weight,
							$font_style->style
						),
					),
					WebFontsModule::$font_preview_phrase
				);
			}

			echo Utils::generate_html_tag( 'style', array( 'type' => 'text/css' ), $font_styles );
		}

		if ( 'local-font-files' === $column ) {
			printf( '<pre>%s</pre>', implode( '<br>', $data->formats ) );
		}
	}

	public function display_post_states( $post_states, $post ) {
		if (
			self::CPT !== $post->post_type ||
			'publish' !== $post->post_status
		) {
			return $post_states;
		}

		$data = self::get_local_font_config_json( $post->ID );

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
			return __( 'Font Family (font name)', 'cmsmasters-elementor' );
		}

		return $title;
	}

	public function add_local_font_templates( $current_screen ) {
		if (
			'post' !== $current_screen->base ||
			self::CPT !== $current_screen->id
		) {
			return;
		}

		Plugin::elementor()->common->add_template( WebFontsModule::MODULE_DIR . '/templates/templates.php' );
	}

	public function transition_post_status( $new_status, $old_status, $post ) {
		if ( self::CPT !== $post->post_type ) {
			return;
		}

		if ( 'publish' === $old_status && 'publish' !== $new_status ) {
			self::remove_local_fonts_config();
		}
	}

	public function handle_delete_font( $post_id ) {
		if ( self::CPT !== get_post_type( $post_id ) ) {
			return;
		}

		// Remove all assets related to this local font
		$attachments = get_attached_media( '', $post_id );

		foreach ( $attachments as $attachment ) {
			wp_delete_attachment( $attachment->ID, 'true' );
		}

		// Remove local font assets directory
		$font_path = get_post_meta( $post_id, self::FONT_PATH_META_KEY, true );

		if ( ! empty( $font_path ) && is_dir( $font_path ) ) {
			Utils::get_wp_filesystem()->rmdir( $font_path, true );
		}

		// Force refresh of local fonts list config
		self::remove_local_fonts_config();
	}

	/**
	 * Enqueue fonts.
	 *
	 * Enqueue local fonts styles.
	 *
	 * @since 1.0.0
	 *
	 * @param CSSFilesBase $post_css
	 */
	public function enqueue_fonts( $post_css ) {
		$used_fonts = $post_css->get_fonts();

		$local_fonts = self::get_local_fonts_list();
		$fonts = self::get_fonts();

		foreach ( $used_fonts as $font_family ) {
			if (
				! isset( $local_fonts[ $font_family ] ) ||
				in_array( $font_family, $this->enqueued_fonts, true )
			) {
				continue;
			}

			$selected_fonts = array_filter( $fonts, function( $attributes ) use ( $font_family ) {
				return $font_family === $attributes['label'];
			} );

			$current_font = current( $selected_fonts );

			$this->enqueue_font( $current_font['id'], $post_css );

			$this->enqueued_fonts[] = $font_family;
		}
	}

	/**
	 * Get fonts.
	 *
	 * Gets fonts list form the database.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $force Force regenerate fonts list if is set to true.
	 *
	 * @return array|bool Fonts list, or false if empty.
	 */
	public static function get_fonts( $force = false ) {
		static $fonts = false;

		if ( false !== $fonts && ! $force ) {
			return $fonts;
		}

		if ( $force ) {
			self::refresh_local_fonts_config();
		}

		$fonts = self::get_local_fonts_config();

		return $fonts;
	}

	/**
	 * Enqueue font.
	 *
	 * Enqueue selected local font styles.
	 *
	 * @since 1.0.0
	 *
	 * @param int $font_id
	 * @param CSSFilesBase $post_css
	 */
	public function enqueue_font( $font_id, $post_css ) {
		$fonts_styles = sprintf(
			"\n/* Start local fonts styles */\n%s/* End local fonts styles */",
			self::get_local_font_styles( $font_id )
		);

		$post_css->get_stylesheet()->add_raw_css( $fonts_styles );
	}

	public function register_ajax_actions( Ajax $ajax ) {
		$ajax->register_ajax_action( 'cmsmasters_local_font_upload', array( $this, 'local_font_upload_handler' ) );
	}

	public function local_font_upload_handler( $data ) {
		$this->current_post_id = $data['post_id'];

		$extract_directory = Utils::upload_and_extract_zip();

		if ( is_wp_error( $extract_directory ) ) {
			return $extract_directory;
		}

		foreach ( self::get_supported_services() as $handler ) {
			/**
			 * @var Base_Service $service
			 */
			$service = new $handler( $extract_directory );

			if ( ! $service || ! $service->is_valid() ) {
				continue;
			}

			$service->handle_new_local_font();

			$service->move_files( $this->current_post_id );

			$config = $service->build_config();

			// Notify about font duplicate
			if ( self::font_family_exists( $config ) ) {
				$config['duplicatedFont'] = true;
			}

			return array( 'config' => $config );
		}

		return new \WP_Error( 'unsupported_zip_format', __( 'The zip file provided is not supported!', 'cmsmasters-elementor' ) );
	}

	public static function get_supported_services() {
		$additional_services = apply_filters( 'cmsmasters_elementor/web_fonts/local_fonts/additional_services', array() );

		return array_merge( $additional_services, self::$services );
	}

	public static function font_family_exists( $font_family_config ) {
		return self::check_font_family( $font_family_config['label'] );
	}

	public static function check_font_family( $font_family, $return_type = 'boolean' ) {
		$fonts_config = self::get_local_fonts_config();

		if ( empty( $fonts_config ) ) {
			return false;
		}

		foreach ( $fonts_config as $font_config ) {
			if ( $font_family === $font_config['label'] ) {
				if ( 'id' === $return_type ) {
					return $font_config['id'];
				} else {
					return true;
				}
			}
		}

		return false;
	}

	public static function get_local_fonts_config() {
		$config = get_option( self::FONTS_OPTION_NAME, false );

		if ( false === $config ) {
			$config = self::generate_local_fonts_config();
		}

		return $config;
	}

	public function post_updated_messages( $messages ) {
		$revision = false;

		if ( isset( $_GET['revision'] ) ) {
			$revision = sprintf(
				/* translators: Addon Extra Icons post type revision message. %s: date and time of the revision */
				__( 'Font restored to revision from %s.', 'cmsmasters-elementor' ),
				wp_post_revision_title( (int) $_GET['revision'], false )
			);
		}

		$messages[ self::CPT ] = array(
			0 => '', // Unused. Messages starts at index 1.
			1 => __( 'Font updated.', 'cmsmasters-elementor' ),
			2 => __( 'Custom field updated.', 'cmsmasters-elementor' ),
			3 => __( 'Custom field deleted.', 'cmsmasters-elementor' ),
			4 => __( 'Font updated.', 'cmsmasters-elementor' ),
			5 => $revision,
			6 => __( 'Font saved.', 'cmsmasters-elementor' ),
			7 => __( 'Font saved.', 'cmsmasters-elementor' ),
			8 => __( 'Font submitted.', 'cmsmasters-elementor' ),
			9 => __( 'Font updated.', 'cmsmasters-elementor' ),
			10 => __( 'Font draft updated.', 'cmsmasters-elementor' ),
		);

		return $messages;
	}

	public function register_fonts_groups( $font_groups ) {
		$new_groups = array();

		$new_groups[ self::get_type() ] = self::get_name();

		return array_merge( $new_groups, $font_groups );
	}

	public function register_fonts_in_control( $fonts ) {
		$local_fonts = self::get_local_fonts_list();
		$fonts = self::get_fonts();

		if ( ! $local_fonts ) {
			return $fonts;
		}

		return array_merge( $local_fonts, $fonts );
	}

	private static function get_local_fonts_list() {
		$local_fonts_config = self::get_local_fonts_config();

		if ( empty( $local_fonts_config ) ) {
			return false;
		}

		$local_fonts = array();

		foreach ( $local_fonts_config as $font_config ) {
			$local_fonts[ $font_config['label'] ] = self::get_type();
		}

		return $local_fonts;
	}

}
