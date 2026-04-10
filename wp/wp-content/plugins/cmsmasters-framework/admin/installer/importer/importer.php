<?php
namespace CmsmastersFramework\Admin\Installer\Importer;

use CmsmastersFramework\Admin\Installer\Importer\ACF;
use CmsmastersFramework\Admin\Installer\Importer\CPTUI;
use CmsmastersFramework\Admin\Installer\Importer\Elementor_Fonts;
use CmsmastersFramework\Admin\Installer\Importer\Elementor_Icons;
use CmsmastersFramework\Admin\Installer\Importer\Elementor_Importer;
use CmsmastersFramework\Admin\Installer\Importer\Elementor_Kit;
use CmsmastersFramework\Admin\Installer\Importer\Elementor_Templates;
use CmsmastersFramework\Admin\Installer\Importer\Theme_Options;
use CmsmastersFramework\Admin\Installer\Importer\WPForms;
use CmsmastersFramework\Admin\Installer\Importer\Forminator;
use CmsmastersFramework\Admin\Installer\Importer\Give_WP;
use CmsmastersFramework\Admin\Installer\Importer\WPRM_Templates;
use CmsmastersFramework\Admin\Installer\Importer\Woo_Product_Filter;
use CmsmastersFramework\Admin\Installer\Importer\Revslider;
use CmsmastersFramework\Admin\Installer\Importer\WPClever_Smart_Compare;
use CmsmastersFramework\Admin\Installer\Importer\WPClever_Smart_Quick_View;
use CmsmastersFramework\Admin\Installer\Importer\WPClever_Smart_Wishlist;
use CmsmastersFramework\Admin\Installer\Importer\WPClever_Variation_Swatches;
use CmsmastersFramework\Core\Utils\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Importer handler class is responsible for different methods on importing plugins settings.
 *
 * @since 1.0.0
 */
class Importer {

	/**
	 * Importer constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		new Theme_Options();

		new ACF();

		new CPTUI();

		new WPForms();

		new Forminator();

		new Give_WP();

		new Woo_Product_Filter();

		new WPClever_Smart_Compare();
		new WPClever_Smart_Quick_View();
		new WPClever_Smart_Wishlist();
		new WPClever_Variation_Swatches();

		new WPRM_Templates();

		new Revslider();

		new Elementor_Fonts();

		new Elementor_Icons();

		new Elementor_Kit();

		new Elementor_Templates();

		new Elementor_Importer();

		add_action( 'cmsmasters_wp_import_insert_attachment', array( $this, 'set_import_attachments_ids' ), 10, 4 );

		add_action( 'wp_import_insert_post', array( $this, 'set_import_posts_ids' ), 10, 4 );

		add_action( 'wp_import_insert_term', array( $this, 'set_import_terms_ids' ), 10, 2 );

		add_action( 'import_end', array( $this, 'update_taxonomy_and_comments_counts' ) );

		add_action( 'admin_init', array( $this, 'remove_import_temp_options' ), 100 );
	}

	/**
	 * Set import attachments ids.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post id.
	 * @param int $original_id Post original id.
	 * @param array $postdata Post data.
	 * @param array $data Data.
	 */
	public function set_import_attachments_ids( $post_id, $original_id, $postdata, $data ) {
		$demo = Utils::get_demo();

		$attachments_ids = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_attachments_ids' );

		if ( false === $attachments_ids ) {
			$attachments_ids = array();
		}

		if ( ! is_wp_error( $post_id ) && is_numeric( $post_id ) ) {
			$attachments_ids[ $original_id ] = $post_id;
		}

		update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_attachments_ids', $attachments_ids, false );
	}

	/**
	 * Set import posts ids.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post id.
	 * @param int $original_id Post original id.
	 * @param array $postdata Post data.
	 * @param array $post The Post.
	 */
	public function set_import_posts_ids( $post_id, $original_id, $postdata, $post ) {
		$demo = Utils::get_demo();

		$displayed_ids = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_displayed_ids', array() );

		$displayed_ids['post_id'][ $post['post_type'] ][ $original_id ] = $post_id;

		update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_displayed_ids', $displayed_ids, false );
	}

	/**
	 * Set import terms ids.
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id Term id.
	 * @param array $data Term data.
	 */
	public function set_import_terms_ids( $term_id, $data ) {
		$demo = Utils::get_demo();

		$displayed_ids = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_displayed_ids' );

		if ( false === $displayed_ids ) {
			$displayed_ids = array();
		}

		if ( ! isset( $data['taxonomy'] ) || ! isset( $data['id'] ) ) {
			return;
		}

		$displayed_ids['taxonomy'][ $data['taxonomy'] ][ $data['id'] ] = $term_id;

		update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_displayed_ids', $displayed_ids, false );
	}

	/**
	 * Update taxonomy and comments counts after import.
	 *
	 * @since 1.0.0
	 */
	public function update_taxonomy_and_comments_counts() {
		global $wpdb;

		// Update taxonomy count
		$term_taxonomy_ids = $wpdb->get_results( "SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy}" );

		foreach ( $term_taxonomy_ids as $term_taxonomy_id_obj ) {
			$term_taxonomy_id = $term_taxonomy_id_obj->term_taxonomy_id;

			$count_result = $wpdb->get_var( $wpdb->prepare(
				"SELECT count(*) FROM {$wpdb->term_relationships} WHERE term_taxonomy_id = %d",
				$term_taxonomy_id
			) );

			$wpdb->update(
				$wpdb->term_taxonomy,
				[ 'count' => $count_result ],
				[ 'term_taxonomy_id' => $term_taxonomy_id ]
			);
		}

		// Update comment count
		$post_ids = $wpdb->get_results( "SELECT ID FROM {$wpdb->posts}" );

		foreach ( $post_ids as $post_id_obj ) {
			$post_id = $post_id_obj->ID;

			$count_result = $wpdb->get_var( $wpdb->prepare(
				"SELECT count(*) FROM {$wpdb->comments} WHERE comment_post_ID = %d AND comment_approved = 1",
				$post_id
			) );

			$wpdb->update(
				$wpdb->posts,
				[ 'comment_count' => $count_result ],
				[ 'ID' => $post_id ]
			);
		}
	}

	/**
	 * Remove import temporary options.
	 *
	 * @since 1.0.0
	 */
	public function remove_import_temp_options() {
		$demo = Utils::get_demo();

		if ( 'imported' !== get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_content_import_status' ) ) {
			return;
		}

		delete_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_import_templates_ids' );
		delete_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_attachments_ids' );
		delete_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_displayed_ids' );
	}

}
