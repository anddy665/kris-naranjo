<?php
namespace PediatricClinicSpace\TemplateFunctions;

use PediatricClinicSpace\Modules\Swiper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Post Media handler class.
 *
 * Get media by post type
 */
class Post_Media {

	/**
	 * Gets post image.
	 *
	 * @param array $atts Attributes.
	 *
	 * @return string Post image.
	 */
	public static function get_image( $atts = array() ) {
		$req_vars = array(
			'id' => get_the_ID(),
			'size' => 'full',
			'link' => true,
			'target' => '_blank',
			'placeholder' => false,
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		$img = '';

		if ( $placeholder ) {
			$img = '<span class="cmsmasters-image-placeholder">
				<span class="cmsmasters-theme-icon-image-placeholder"></span>
			</span>';
		}

		if ( has_post_thumbnail() ) {
			$img = get_the_post_thumbnail( $id, $size );
		}

		if ( '' === $img ) {
			return '';
		}

		$out = '<figure class="cmsmasters-image-wrap">' .
			( $link ? '<a href="' . esc_url( get_permalink( $id ) ) . '"' . ( false !== $target ? ' target="' . esc_attr( $target ) . '"' : '' ) . '>' : '' ) .
				$img .
			( $link ? '</a>' : '' ) .
		'</figure>';

		return $out;
	}

	/**
	 * Gets post video.
	 *
	 * @param array $atts Attributes.
	 *
	 * @return string Post video.
	 */
	public static function get_video( $atts = array() ) {
		$req_vars = array(
			'id' => get_the_ID(),
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		$out = '';

		$type = get_post_meta( $id, 'cmsmasters_post_video_type', true );

		if ( 'hosted' === $type ) {
			$hosted_id = get_post_meta( $id, 'cmsmasters_post_video_link_hosted', true );

			if ( '' !== $hosted_id ) {
				$out = wp_video_shortcode( array(
					'src' => wp_get_attachment_url( $hosted_id ),
					'class' => 'cmsmasters-video cmsmasters-lazyload lazyload',
				) );
			}
		} elseif ( 'embedded' === $type ) {
			$embed_link = get_post_meta( $id, 'cmsmasters_post_video_link_embedded', true );

			if ( '' !== $embed_link ) {
				$wp_embed = new \WP_Embed();

				$out = $wp_embed->shortcode( array(), $embed_link );
			}
		}

		if ( '' !== $out ) {
			$out = '<div class="cmsmasters-video-wrap">' . $out . '</div>';
		}

		return $out;
	}

	/**
	 * Gets post audio.
	 *
	 * @return string Post audio.
	 */
	public static function get_audio( $atts = array() ) {
		$req_vars = array(
			'id' => get_the_ID(),
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		$out = '';

		$type = get_post_meta( $id, 'cmsmasters_post_audio_type', true );

		if ( 'hosted' === $type ) {
			$hosted_id = get_post_meta( $id, 'cmsmasters_post_audio_link_hosted', true );

			if ( '' !== $hosted_id ) {
				$hosted_link = wp_get_attachment_url( $hosted_id );
				$hosted_attr = array(
					'src' => $hosted_link,
					'class' => 'cmsmasters-audio cmsmasters-lazyload lazyload',
				);

				$out = wp_audio_shortcode( $hosted_attr );
			}
		} elseif ( 'embedded' === $type ) {
			$embed_link = get_post_meta( $id, 'cmsmasters_post_audio_link_embedded', true );

			if ( '' !== $embed_link ) {
				$wp_embed = new \WP_Embed();

				$out = $wp_embed->shortcode( array( 'height' => 200 ), $embed_link );
			}
		}

		if ( '' !== $out ) {
			$out = '<div class="cmsmasters-audio-wrap">' . $out . '</div>';
		}

		return $out;
	}

	/**
	 * Gets post gallery.
	 *
	 * @return string Post gallery.
	 */
	public static function get_gallery( $atts = array() ) {
		$req_vars = array(
			'id' => get_the_ID(),
			'size' => 'thumbnail',
			'slider_settings_key' => '',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		$images_ids = get_post_meta( $id, 'cmsmasters_post_gallery_images', true );

		if ( empty( $images_ids ) ) {
			return '';
		}

		if ( ! is_array( $images_ids ) ) {
			$images_ids = explode( ',', $images_ids );
		}

		$images_out = array();

		foreach ( $images_ids as $image_id ) {
			$image_atts = wp_prepare_attachment_for_js( $image_id );

			$image_args = array(
				'alt' => ( '' !== $image_atts['alt'] ? esc_attr( $image_atts['alt'] ) : get_the_title( $id ) ),
				'title' => ( '' !== $image_atts['title'] ? esc_attr( $image_atts['title'] ) : get_the_title( $id ) ),
			);

			$image = wp_get_attachment_image( $image_id, $size, false, $image_args );

			if ( '' !== $image ) {
				$images_out[] = '<figure class="cmsmasters-slider-image-wrap">' . $image . '</figure>';
			}
		}

		$slider_atts = array(
			'items' => $images_out,
			'settings_key' => $slider_settings_key,
			'columns_available' => false,
		);

		wp_enqueue_style( 'swiper' );

		$out = Swiper::get_slider( $slider_atts );

		return $out;
	}

}
