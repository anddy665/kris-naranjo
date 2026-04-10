<?php
/**
 * The template for displaying archive post items.
 */

use PediatricClinicSpace\Core\Utils\Utils;
use PediatricClinicSpace\TemplateFunctions\General_Elements;
use PediatricClinicSpace\TemplateFunctions\Post_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$template = 'archive';

if ( is_search() ) {
	$template = 'search';
}

$parent_class = "cmsmasters-{$template}";
$template_type = Utils::get_kit_option( "cmsmasters_{$template}_type" );

$wrapper_classes = array(
	$parent_class,
	"cmsmasters-{$template_type}",
);

if ( 'grid' === $template_type ) {
	$wrapper_classes[] = 'cmsmasters-' . Utils::get_kit_option( "cmsmasters_{$template}_grid_style" );
} elseif ( 'compact' === $template_type ) {
	$media_position = Utils::get_kit_option( "cmsmasters_{$template}_compact_media_position" );

	if ( 'hide' !== $media_position ) {
		$wrapper_classes[] = 'cmsmasters-media-' . $media_position;
	}
}

echo '<div class="' . esc_attr( join( ' ', $wrapper_classes ) ) . '">';

if ( is_search() && ! have_posts() ) {
	echo '<div class="' . $parent_class . '__no-posts">' .
		'<h3 class="' . $parent_class . '__no-posts-title">' . esc_html__( 'Nothing found', 'pediatric-clinic' ) . '</h3>' .
		'<div class="' . $parent_class . '__no-posts-description">' .
			'<p>' . esc_html__( 'Sorry, no posts matched your criteria. Please try another search.', 'pediatric-clinic' ) . '</p>' .
			'<p>' . esc_html__( 'You might want to consider some of our suggestions to get better results:', 'pediatric-clinic' ) . '</p>' .
			'<ul>' .
				'<li>' . esc_html__( 'Check your spelling.', 'pediatric-clinic' ) . '</li>' .
				'<li>' . esc_html__( 'Try a similar keyword, for example: tablet instead of laptop.', 'pediatric-clinic' ) . '</li>' .
				'<li>' . esc_html__( 'Try using more than one keyword.', 'pediatric-clinic' ) . '</li>' .
			'</ul>' .
		'</div>' .
	'</div>';
}

while ( have_posts() ) :
	the_post();

	$elements_out = '';
	$elements = Utils::get_kit_option( "cmsmasters_{$template}_{$template_type}_elements" );

	if ( ! empty( $elements ) ) {
		foreach ( $elements as $element ) {
			if ( 'media' === $element ) {
				$elements_out .= Post_Elements::get_post_media( array(
					'parent_class' => "{$parent_class}-post",
					'img_size' => Utils::get_kit_option( "cmsmasters_{$template}_media_image_size" ),
					'gallery_img_size' => Utils::get_kit_option( "cmsmasters_{$template}_media_gallery_image_size" ),
					'slider_settings_key' => "cmsmasters_{$template}_media_slider",
				) );
			} elseif ( 'title' === $element ) {
				$elements_out .= Post_Elements::get_post_heading( array(
					'parent_class' => "{$parent_class}-post",
					'link' => true,
				) );
			} elseif ( 'meta_first' === $element || 'meta_second' === $element ) {
				$elements_out .= Post_Elements::get_post_meta( array(
					'parent_class' => "{$parent_class}-post",
					'meta_key' => $element,
					'elements' => Utils::get_kit_option( "cmsmasters_{$template}_{$element}_elements" ),
				) );
			} elseif ( 'content' === $element ) {
				$elements_out .= Post_Elements::get_post_excerpt( array(
					'parent_class' => "{$parent_class}-post",
					'length' => Utils::get_kit_option( "cmsmasters_{$template}_content_excerpt_length" ),
				) );
			} elseif ( 'more' === $element ) {
				$elements_out .= Post_Elements::get_post_more( array(
					'parent_class' => "{$parent_class}-post",
					'text' => Utils::get_kit_option( "cmsmasters_{$template}_more_text", '' ),
				) );
			}
		}
	}

	if ( '' !== $elements_out ) {
		$elements_out = '<div class="' . esc_attr( $parent_class ) . '-post__inner">' .
			$elements_out .
		'</div>';
	}

	$compact_media = '';

	if ( 'compact' === $template_type && 'hide' !== $media_position ) {
		$compact_media = Post_Elements::get_post_media( array(
			'parent_class' => "{$parent_class}-post",
			'img_size' => Utils::get_kit_option( "cmsmasters_{$template}_media_image_size" ),
			'gallery_img_size' => Utils::get_kit_option( "cmsmasters_{$template}_media_gallery_image_size" ),
			'slider_settings_key' => "cmsmasters_{$template}_media_slider",
		) );
	}

	if ( '' !== $elements_out || '' !== $compact_media ) {
		$post_classes = get_post_class();
		$post_classes[] = "{$parent_class}-post";

		echo '<article id="post-' . esc_attr( get_the_ID() ) . '" class="' . esc_attr( join( ' ', $post_classes ) ) . '">' .
			'<div class="' . esc_attr( $parent_class ) . '-post__outer">' .
				$compact_media .
				$elements_out .
			'</div>' .
		'</article>';
	}

endwhile;

echo '</div>';

echo General_Elements::get_pagination( array(
	'parent_class' => $parent_class,
) );
