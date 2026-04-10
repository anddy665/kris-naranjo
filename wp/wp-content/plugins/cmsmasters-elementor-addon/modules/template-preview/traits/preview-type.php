<?php
namespace CmsmastersElementor\Modules\TemplatePreview\Traits;

use CmsmastersElementor\Modules\Woocommerce\Module as WooModule;
use CmsmastersElementor\Modules\TribeEvents\Module as TribeEventsModule;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Preview type trait.
 *
 * Provides preview type methods for template documents.
 *
 * @since 1.0.0
 */
trait Preview_Type {

	public static function get_singular_preview_type_options_choices( $woo = true, $tribe_events = true ) {
		return array(
			'singular' => array(
				'label' => __( 'Singular', 'cmsmasters-elementor' ),
				'options' => static::get_singular_options_choices( $woo, $tribe_events ),
			),
			'error_404' => __( '404 Error page', 'cmsmasters-elementor' ),
		);
	}

	public static function get_singular_options_choices( $woo = true, $tribe_events = true ) {
		$post_type_args = ( $woo && WooModule::is_active() || $tribe_events && TribeEventsModule::is_active() ) ? array( 'exclude' => '' ) : array();
		$post_types_list = Utils::filter_public_post_types( $post_type_args, true );
		$post_types_list['attachment'] = get_post_type_object( 'attachment' )->label;

		$singular_options = array();

		foreach ( $post_types_list as $post_type => $post_type_label ) {
			$singular_options[ 'singular/' . $post_type ] = $post_type_label;
		}

		return $singular_options;
	}

	public static function get_archive_preview_type_options_choices( $woo = true, $tribe_events = true ) {
		$options = array( 'post_type_archive/post' => __( 'Recent posts', 'cmsmasters-elementor' ) );

		$post_type_args = ( $woo && WooModule::is_active() || $tribe_events && TribeEventsModule::is_active() ) ? array( 'exclude' => '' ) : array();

		foreach ( array_keys( Utils::filter_public_post_types( $post_type_args ) ) as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );

			if ( $post_type_object->has_archive ) {
				/* translators: Preview dynamic content field archive options. %s: Post type name */
				$options[ "post_type_archive/{$post_type}" ] = sprintf( __( '%s archive', 'cmsmasters-elementor' ), $post_type_object->label );
			}

			$object_taxonomies = get_object_taxonomies( $post_type, 'objects' );
			$filtered_taxonomies_object = wp_filter_object_list( $object_taxonomies, array(
				'public' => true,
				'show_in_nav_menus' => true,
			) );

			foreach ( $filtered_taxonomies_object as $slug => $object ) {
				/* translators: Preview dynamic content field archive options. %s: Taxonomy name */
				$options[ "taxonomy/{$slug}" ] = sprintf( __( '%s archive', 'cmsmasters-elementor' ), $object->label );
			}
		}

		$options += array(
			'archive/date' => __( 'Date archive', 'cmsmasters-elementor' ),
			'archive/author' => __( 'Author archive', 'cmsmasters-elementor' ),
		);

		return array(
			'archive' => array(
				'label' => __( 'Archive', 'cmsmasters-elementor' ),
				'options' => $options,
			),
			'page/search' => __( 'Search results', 'cmsmasters-elementor' ),
		);
	}

}
