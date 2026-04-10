<?php
/**
 * The template for displaying single post.
 */

use PediatricClinicSpace\Core\Utils\Utils;
use PediatricClinicSpace\TemplateFunctions\General_Elements;
use PediatricClinicSpace\TemplateFunctions\Post_Elements;
use PediatricClinicSpace\TemplateFunctions\Single_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

while ( have_posts() ) {
	the_post();

	$parent_class = 'cmsmasters-single';
	$elements_out = '';
	$elements = Utils::get_kit_option( 'cmsmasters_single_elements', array() );

	if ( ! empty( $elements ) ) {
		foreach ( $elements as $element ) {
			if ( 'media' === $element ) {
				$elements_out .= Post_Elements::get_post_media( array(
					'parent_class' => "{$parent_class}-post",
					'img_size' => Utils::get_kit_option( 'cmsmasters_single_media_image_size' ),
					'gallery_img_size' => Utils::get_kit_option( 'cmsmasters_single_media_gallery_image_size', 'full' ),
					'slider_settings_key' => 'cmsmasters_single_media_slider',
				) );
			} elseif ( 'title' === $element ) {
				$elements_out .= Post_Elements::get_post_heading( array(
					'parent_class' => "{$parent_class}-post",
					'link' => false,
				) );
			} elseif ( 'meta_first' === $element || 'meta_second' === $element ) {
				$elements_out .= Post_Elements::get_post_meta( array(
					'parent_class' => "{$parent_class}-post",
					'meta_key' => $element,
					'elements' => Utils::get_kit_option( "cmsmasters_single_{$element}_elements", array() ),
				) );
			} elseif ( 'content' === $element ) {
				ob_start();

				the_content();

				$content = ob_get_clean();

				if ( false === $content ) {
					continue;
				}

				$elements_out .= '<div class="' . esc_attr( $parent_class ) . '-post-content entry-content">' .
					$content .
					General_Elements::get_sub_pagination() .
				'</div>';
			}
		}
	}

	$blocks_out = '';
	$blocks = Utils::get_kit_option( 'cmsmasters_single_blocks', array() );

	if ( ! empty( $blocks ) ) {
		foreach ( $blocks as $block ) {
			if ( 'nav' === $block ) {
				$blocks_out .= Single_Elements::get_single_nav( array(
					'tax' => Utils::get_kit_option( 'cmsmasters_single_nav_tax', 'none' ),
					'text_above_prev' => Utils::get_kit_option( 'cmsmasters_single_nav_text_above_prev', '' ),
					'text_above_next' => Utils::get_kit_option( 'cmsmasters_single_nav_text_above_next', '' ),
				) );
			} elseif ( 'author' === $block ) {
				$blocks_out .= Single_Elements::get_single_author( array(
					'title_text' => Utils::get_kit_option( 'cmsmasters_single_author_title_text', '' ),
				) );
			} elseif ( 'more_posts' === $block ) {
				$breakpoints = Utils::get_breakpoints();

				$mobile_breakpoint = '0';
				$tablet_breakpoint = strval( $breakpoints['mobile'] );
				$desktop_breakpoint = strval( $breakpoints['tablet'] );

				$blocks_out .= Single_Elements::get_single_slider( array(
					'title_text' => Utils::get_kit_option( 'cmsmasters_single_more_posts_title_text', '' ),
					'order' => Utils::get_kit_option( 'cmsmasters_single_more_posts_order', 'recent' ),
					'count' => Utils::get_kit_option( 'cmsmasters_single_more_posts_count' ),
					'img_size' => Utils::get_kit_option( 'cmsmasters_single_more_posts_image_size' ),
					'slider_settings_key' => 'cmsmasters_single_more_posts_slider',
				) );
			}
		}
	}

	echo '<article id="post-' . get_the_ID() . '" class="' . join( ' ', get_post_class( $parent_class . '-post' ) ) . '">' .
		$elements_out .
	'</article>' .
	$blocks_out;

	comments_template();
}
