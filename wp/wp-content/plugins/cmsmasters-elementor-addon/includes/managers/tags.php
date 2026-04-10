<?php
namespace CmsmastersElementor;

use CmsmastersElementor\Modules\Woocommerce\Module as WooModule;
use CmsmastersElementor\Modules\TribeEvents\Module as TribeEventsModule;

use Elementor\Core\DynamicTags\Manager as DynamicTagsManager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters tags manager.
 *
 * CMSMasters tags manager handler class is responsible for registering and
 * managing plugin dynamic tags and tag groups.
 *
 * @since 1.0.0
 * @final
 */
final class Tags_Manager {

	/**
	 * Action dynamic tags group.
	 */
	const ACTION_GROUP = 'action';

	/**
	 * Archive dynamic tags group.
	 */
	const ARCHIVE_GROUP = 'archive';

	/**
	 * Author dynamic tags group.
	 */
	const AUTHOR_GROUP = 'author';

	/**
	 * Comments dynamic tags group.
	 */
	const COMMENTS_GROUP = 'comments';

	/**
	 * Media dynamic tags group.
	 */
	const MEDIA_GROUP = 'media';

	/**
	 * Post dynamic tags group.
	 */
	const POST_GROUP = 'post';

	/**
	 * Site dynamic tags group.
	 */
	const SITE_GROUP = 'site';

	/**
	 * Taxonomy dynamic tags group.
	 */
	const TAXONOMY_GROUP = 'tax';

	/**
	 * WooCommerce dynamic tags group.
	 */
	const WOO_GROUP = 'woocommerce';

	/**
	 * Tribe Events dynamic tags group.
	 */
	const TRIBE_EVENTS_GROUP = 'tribe-events';

	/**
	 * ACF dynamic tags group.
	 */
	const ACF_GROUP = 'acf';

	/**
	 * Paid Memberships Pro dynamic tags group.
	 */
	const PMPRO_GROUP = 'pmpro';

	/**
	 * Dynamic tags groups.
	 *
	 * @since 1.0.0
	 *
	 * @var array List of dynamic tags groups created by Addon.
	 */
	private $tags_groups;

	/**
	 * Dynamic tags list.
	 *
	 * @since 1.0.0
	 *
	 * @var array List of dynamic tags created by Addon.
	 */
	private $tags_list;

	/**
	 * Dynamic tags abbreviations list.
	 *
	 * @since 1.0.0
	 *
	 * @var array List of dynamic tags class part abbreviations.
	 */
	public $abbr_array = array(
		'id',
		'url',
		'sku',
	);

	/**
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->set_available_groups();
		$this->set_tags_list();

		$this->init_actions();
	}

	/**
	 * Set available Addon dynamic tags groups.
	 *
	 * Register an array of available Addon dynamic tags groups.
	 *
	 * @since 1.0.0
	 *
	 * @return array Available dynamic tags groups.
	 */
	private function set_available_groups() {
		$available_groups = array(
			self::ACTION_GROUP => __( 'Action', 'cmsmasters-elementor' ),
			self::ARCHIVE_GROUP => __( 'Archive', 'cmsmasters-elementor' ),
			self::AUTHOR_GROUP => __( 'Author', 'cmsmasters-elementor' ),
			self::COMMENTS_GROUP => __( 'Comments', 'cmsmasters-elementor' ),
			self::MEDIA_GROUP => __( 'Media', 'cmsmasters-elementor' ),
			self::POST_GROUP => __( 'Post', 'cmsmasters-elementor' ),
			self::SITE_GROUP => __( 'Site', 'cmsmasters-elementor' ),
		);

		if ( class_exists( WooModule::class ) ) {
			$available_groups[ self::WOO_GROUP ] = __( 'WooCommerce', 'cmsmasters-elementor' );
		}

		if ( class_exists( TribeEventsModule::class ) ) {
			$available_groups[ self::TRIBE_EVENTS_GROUP ] = __( 'Tribe Events', 'cmsmasters-elementor' );
		}

		if ( class_exists( '\acf' ) && function_exists( 'acf_get_field_groups' ) ) {
			$available_groups[ self::ACF_GROUP ] = __( 'ACF', 'cmsmasters-elementor' );
		}

		if ( function_exists( 'pmpro_is_plugin_active' ) ) {
			$available_groups[ self::PMPRO_GROUP ] = __( 'Paid Memberships Pro', 'cmsmasters-elementor' );
		}

		foreach ( $available_groups as $key => $title ) {
			$this->tags_groups[ $key ] = array( 'title' => $title );
		}
	}

	/**
	 * @since 1.0.0
	 * @since 1.2.0 Added repeatable field types.
	 */
	private function set_tags_list() {
		$tags_list_groups = array(
			self::ACTION_GROUP => array(
				'lightbox',
				'popup',
			),
			self::ARCHIVE_GROUP => array(
				'title',
				'description',
				'url', // Uppercase class name
				'meta',
			),
			self::AUTHOR_GROUP => array(
				'name',
				'info',
				'profile-picture',
				'url', // Uppercase class name
				'meta',
			),
			self::COMMENTS_GROUP => array(
				'number',
				'url', // Uppercase class name
			),
			self::MEDIA_GROUP => array(
				'featured-image-data',
			),
			self::POST_GROUP => array(
				'id', // Uppercase class name
				'title',
				'excerpt',
				'featured-image',
				'featured-image-id', // Uppercase class name
				'featured-image-url', // Uppercase class name
				'url', // Uppercase class name
				'terms',
				'date',
				'time',
				'attachments',
				'custom-field',
			),
			self::SITE_GROUP => array(
				'logo',
				'title',
				'tagline',
				'home-url', // Uppercase class name
				'internal-url', // Uppercase class name
				'page-title',
				'user-info',
				'user-profile-picture',
				'current-date-time',
			),
		);

		if ( class_exists( WooModule::class ) ) {
			$tags_list_groups[ self::WOO_GROUP ] = array(
				'title',
				'image',
				'image-id', // Uppercase class name
				'image-url', // Uppercase class name
				'price',
				'terms',
				'description',
				'short-description',
				'product-url', // Uppercase class name
				'gallery',
				'rating',
				'sale-price-dates',
				'sale',
				'stock',
				'product-sku', // Uppercase class name
				'archive-description',
				'category-image',
			);
		}

		if ( class_exists( TribeEventsModule::class ) ) {
			$tags_list_groups[ self::TRIBE_EVENTS_GROUP ] = array(
				'cost',
				'description',
				'event-dates',
				'event-url',
				'image',
				'image-id',
				'image-url',
				'organizer-email',
				'organizer-phone',
				'organizer-title',
				'organizer-url',
				'organizer-website',
				'short-description',
				'terms',
				'title',
				'venue-address',
				'venue-city',
				'venue-country',
				'venue-map-url',
				'venue-phone',
				'venue-state-province',
				'venue-title',
				'venue-url',
				'venue-website',
				'venue-zip',
			);
		}

		if ( class_exists( '\acf' ) && function_exists( 'acf_get_field_groups' ) ) {
			$tags_list_groups[ self::ACF_GROUP ] = array(
				'acf-url', // Uppercase class name
				'color',
				'file',
				'gallery',
				'image',
				'number',
				'text',
				'repeater-url',
				'repeater-color',
				'repeater-file',
				'repeater-gallery',
				'repeater-image',
				'repeater-number',
				'repeater-text',
			);
		}

		if ( function_exists( 'pmpro_is_plugin_active' ) ) {
			$tags_list_groups[ self::PMPRO_GROUP ] = array(
				'checkout-url', // Uppercase class name
			);
		}

		foreach ( $tags_list_groups as $group => $tags_list ) {
			$this->add_to_tags_list( $group, $tags_list );
		}
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param string $group
	 * @param array $tags_list
	 */
	private function add_to_tags_list( $group, $tags_list ) {
		foreach ( $tags_list as $tag ) {
			if ( in_array( $tag, $this->abbr_array, true ) ) {
				$tag = $group . '-' . strtoupper( $tag );
			}

			$group = str_replace( ' ', '', ucwords( str_replace( '-', ' ', $group ) ) );

			$tag_name = $group . '\\' . str_replace( '-', '_', ucwords( $tag, '-' ) );

			$this->tags_list[] = $tag_name;
		}
	}

	/**
	 * Add Addon tags manager init actions.
	 *
	 * @since 1.0.0
	 * @since 1.6.5 Fixed elementor deprecation.
	 */
	private function init_actions() {
		add_action( 'elementor/dynamic_tags/register', array( $this, 'register_groups' ) );
		add_action( 'elementor/dynamic_tags/register', array( $this, 'register_tags' ) );

		add_filter( 'cmsmasters_elementor/autoloader/classes_map', array( $this, 'filter_autoloader_classes_map' ) );
	}

	/**
	 * Register Addon dynamic tags groups.
	 *
	 * This method extends a list of all the supported dynamic tags groups.
	 *
	 * Fired by `elementor/dynamic_tags/register` Elementor plugin action hook.
	 *
	 * @since 1.0.0
	 * @since 1.6.5 Fixed elementor deprecation.
	 *
	 * @param DynamicTagsManager Dynamic tags manager.
	 */
	public function register_groups( $dynamic_tags ) {
		foreach ( $this->tags_groups as $group_id => $group_title ) {
			$dynamic_tags->register_group( $group_id, $group_title );
		}
	}

	/**
	 * Register Addon dynamic tags classes.
	 *
	 * This method extends a list of all the supported dynamic tags by initializing
	 * each one of appropriate dynamic tags files.
	 *
	 * Fired by `elementor/dynamic_tags/register` Elementor plugin action hook.
	 *
	 * @since 1.0.0
	 * @since 1.6.5 Fixed elementor deprecation.
	 *
	 * @param DynamicTagsManager Dynamic tags manager.
	 */
	public function register_tags( $dynamic_tags ) {
		foreach ( $this->tags_list as $tag_class_name ) {
			$class_name = __NAMESPACE__ . '\\Tags\\' . $tag_class_name;

			$dynamic_tags->register( new $class_name() );
		}
	}

	/**
	 * Filter Addon autoloader classes map array.
	 *
	 * This method extends Addon autoloader classes map with dynamic tags files.
	 *
	 * Fired by `cmsmasters_elementor/autoloader/classes_map` Addon filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array Autoloader classes map array.
	 *
	 * @return array Filtered Addon autoloader classes map array.
	 */
	public function filter_autoloader_classes_map( $classes_map ) {
		foreach ( $this->tags_list as $class_name ) {
			$file_name = str_replace( '\\', '/', str_replace( '_', '-', strtolower( $class_name ) ) );

			if ( preg_match( '/^tribeevents\//', $file_name ) ) {
				$file_name = preg_replace( '/^tribeevents\//', 'tribe-events/', $file_name );
			}

			$classes_map[ 'Tags\\' . $class_name ] = 'includes/tags/' . $file_name . '.php';
		}

		return $classes_map;
	}

}
