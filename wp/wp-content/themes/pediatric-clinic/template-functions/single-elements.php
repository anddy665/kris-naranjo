<?php
namespace PediatricClinicSpace\TemplateFunctions;

use PediatricClinicSpace\Modules\Swiper;
use PediatricClinicSpace\TemplateFunctions\Post_Media;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Single Elements handler class is responsible for different methods in templates.
 */
class Single_Elements {

	/**
	 * Get single navigation.
	 *
	 * @return string Single navigation HTML.
	 */
	public static function get_single_nav( $atts = array() ) {
		$req_vars = array(
			'id' => get_the_ID(),
			'tax' => 'none',
			'text_above_prev' => '',
			'text_above_next' => '',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		$parent_class = 'cmsmasters-single-nav';
		$in_same_term = ( 'none' === $tax ? false : true );
		$tax = ( 'none' === $tax ? 'category' : $tax );
		$prev_post_link = get_previous_post_link(
			'%link',
			'<span class="' . esc_attr( $parent_class ) . '__arrow cmsmasters-theme-icon-single-nav-prev"></span>' .
			( '' !== $text_above_prev ? '<span class="' . esc_attr( $parent_class ) . '__text-above ' . esc_attr( $parent_class ) . '__text-above-prev">' . esc_html( $text_above_prev ) . '</span>' : '' ) .
			'<span class="' . esc_attr( $parent_class ) . '__text">%title</span>',
			$in_same_term,
			'',
			$tax
		);
		$next_post_link = get_next_post_link(
			'%link',
			( '' !== $text_above_next ? '<span class="' . esc_attr( $parent_class ) . '__text-above ' . esc_attr( $parent_class ) . '__text-above-next">' . esc_html( $text_above_next ) . '</span>' : '' ) .
			'<span class="' . esc_attr( $parent_class ) . '__text">%title</span>' .
			'<span class="' . esc_attr( $parent_class ) . '__arrow cmsmasters-theme-icon-single-nav-next"></span>',
			$in_same_term,
			'',
			$tax
		);

		if ( '' === $prev_post_link && '' === $next_post_link ) {
			return '';
		}

		$out = '<div class="' . esc_attr( $parent_class ) . ' cmsmasters-section-container">' .
			'<div class="' . esc_attr( $parent_class ) . '__inner">' .
				'<span class="' . esc_attr( $parent_class ) . '__prev">' .
					$prev_post_link .
				'</span>' .
				'<span class="' . esc_attr( $parent_class ) . '__next">' .
					$next_post_link .
				'</span>' .
			'</div>' .
		'</div>';

		return $out;
	}

	/**
	 * Get single author.
	 *
	 * @return string Single author HTML.
	 */
	public static function get_single_author( $atts = array() ) {
		$req_vars = array(
			'title_text' => '',
			'title_tag' => 'h4',
			'author_tag' => 'h5',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		$description = get_the_author_meta( 'description' );

		if ( '' === $description ) {
			return '';
		}

		$out = '';
		$parent_class = 'cmsmasters-single-author';
		$email = get_the_author_meta( 'user_email' );
		$name = get_the_author();
		$url = get_the_author_meta( 'url' );

		$out .= '<div class="' . esc_attr( $parent_class ) . ' cmsmasters-section-container">';

		$out .= '<' . esc_html( $title_tag ) . ' class="' . esc_attr( $parent_class ) . '__title">' . esc_html( $title_text ) . '</' . esc_html( $title_tag ) . '>';

		$out .= '<div class="' . esc_attr( $parent_class ) . '__inner">' .
			'<figure class="' . esc_attr( $parent_class ) . '__avatar">' .
				get_avatar( $email, 90, get_option( 'avatar_default' ) ) .
			'</figure>' .
			'<div class="' . esc_attr( $parent_class ) . '__content-wrap">' .
				'<' . esc_html( $author_tag ) . ' class="' . esc_attr( $parent_class ) . '__name vcard author"><span class="fn" rel="author">' . esc_html( $name ) . '</span></' . esc_html( $author_tag ) . '>' .
				'<div class="' . esc_attr( $parent_class ) . '__content">' .
					'<p>' . str_replace( "\n", '<br />', $description ) . '</p>' .
				'</div>';

		if ( '' !== $url ) {
			$out .= '<div class="' . esc_attr( $parent_class ) . '__link-wrap">' .
				'<a class="' . esc_attr( $parent_class ) . '__link" href="' . esc_url( $url ) . '" title="' . esc_attr( $name ) . ' ' . esc_attr__( 'website', 'pediatric-clinic' ) . '" target="_blank">' . esc_html( $url ) . '</a>' .
			'</div>';
		}

			$out .= '</div>' .
		'</div>' .
		'</div>';

		return $out;
	}

	/**
	 * Get single slider.
	 *
	 * @return string Single slider HTML.
	 */
	public static function get_single_slider( $atts = array() ) {
		$req_vars = array(
			'id' => get_the_ID(),
			'title_text' => '',
			'title_tag' => 'h4',
			'item_tag' => 'h6',
			'order' => 'recent',
			'count' => 8,
			'img_size' => 'full',
			'slider_settings_key' => '',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		$out = '';

		$query_args = array(
			'posts_per_page' => $count,
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1,
			'post__not_in' => array( $id ),
			'post_type' => get_post_type( $id ),
		);

		$terms = array();

		if ( 'recent' !== $order ) {
			$current_terms = get_the_terms( $id, $order );

			if ( is_array( $current_terms ) && ! empty( $current_terms ) ) {
				foreach ( $current_terms as $term ) {
					$terms[] = $term->term_id;
				}
			}
		}

		if ( ! empty( $terms ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => $order,
					'field' => 'term_id',
					'terms' => $terms,
				),
			);
		}

		$parent_class = 'cmsmasters-single-slider';

		$query = new \WP_Query( $query_args );

		if ( $query->have_posts() ) {
			$out .= '<div class="' . esc_attr( $parent_class ) . ' cmsmasters-section-container">' .
			'<' . esc_html( $title_tag ) . ' class="' . esc_attr( $parent_class ) . '__title">' .
				esc_html( $title_text ) .
			'</' . esc_html( $title_tag ) . '>' .
			'<div class="' . esc_attr( $parent_class ) . '__inner">';

			$items = array();

			while ( $query->have_posts() ) :
				$query->the_post();

				$image_atts = array(
					'size' => $img_size,
					'link' => true,
				);

				$item = '<div class="' . esc_attr( $parent_class ) . '__item">' .
					Post_Media::get_image( $image_atts ) .
					'<' . esc_html( $item_tag ) . ' class="' . esc_attr( $parent_class ) . '__item-title">' .
						'<a href="' . esc_url( get_permalink() ) . '" target="_blank">' .
							get_the_title() .
						'</a>' .
					'</' . esc_html( $item_tag ) . '>' .
				'</div>';

				$items[] = $item;
			endwhile;

			$slider_atts = array(
				'items' => $items,
				'settings_key' => $slider_settings_key,
				'columns_available' => true,
			);

			wp_enqueue_style( 'swiper' );

			$out .= Swiper::get_slider( $slider_atts );

			$out .= '</div>' .
			'</div>';
		}

		wp_reset_postdata();

		return $out;
	}

}
