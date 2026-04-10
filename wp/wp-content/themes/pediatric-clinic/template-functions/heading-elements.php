<?php
namespace PediatricClinicSpace\TemplateFunctions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Heading Elements handler class is responsible for different methods in templates.
 */
class Heading_Elements {

	/**
	 * Get page title.
	 *
	 * Retrieve the page title based on the queried object.
	 *
	 * @return string Page title.
	 */
	public static function get_page_title( $context = true ) {
		$title = '';

		$title = apply_filters( 'cmsmasters_page_title_filter_before', $title );

		if ( '' !== $title ) {
			return $title;
		}

		if ( is_singular() ) {
			$title = get_the_title();
		} elseif ( is_search() ) {
			/* translators: Search query page title. 1: Search query string */
			$title = sprintf( esc_html__( 'Search Results for: %s', 'pediatric-clinic' ), get_search_query() );

			if ( get_query_var( 'paged' ) ) {
				/* translators: Paged search query title part. 1: Page number */
				$title .= sprintf( esc_html__( '&nbsp;&ndash; Page %s', 'pediatric-clinic' ), get_query_var( 'paged' ) );
			}
		} elseif ( ! $context ) {
			if ( is_category() ) {
				$title = single_cat_title( '', false );
			} elseif ( is_tag() ) {
				$title = single_tag_title( '', false );
			} elseif ( is_author() ) {
				$title = '<span class="vcard">' . get_the_author() . '</span>';
			} elseif ( is_year() ) {
				$title = get_the_date( _x( 'Y', 'yearly archives date format', 'pediatric-clinic' ) );
			} elseif ( is_month() ) {
				$title = get_the_date( _x( 'F Y', 'monthly archives date format', 'pediatric-clinic' ) );
			} elseif ( is_day() ) {
				$title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'pediatric-clinic' ) );
			} elseif ( is_tax( 'post_format' ) ) {
				$title = get_the_archive_title();
			} elseif ( is_post_type_archive() ) {
				$title = post_type_archive_title( '', false );
			} elseif ( is_tax() ) {
				$title = single_term_title( '', false );
			} else {
				$title = get_the_archive_title();
			}
		} elseif ( $context ) {
			$title = get_the_archive_title();
		}

		$title = apply_filters( 'cmsmasters_page_title_filter_after', $title );

		return '<h1 class="cmsmasters-heading__title">' . $title . '</h1>';
	}

	/**
	 * Get page breadcrumbs.
	 *
	 * @return string Page breadcrumbs.
	 */
	public static function get_page_breadcrumbs( $sep_text = ' / ' ) {
		global $post;

		$sep = '<span class="cmsmasters-breadcrumbs__sep">' . esc_html( $sep_text ) . '</span>';
		$year_format = get_the_time( 'Y' );
		$month_format = get_the_time( 'F' );
		$month_number_format = get_the_time( 'n' );
		$day_format = get_the_time( 'd' );
		$day_full_format = get_the_time( 'l' );
		$url_year = get_year_link( $year_format );
		$url_month = get_month_link( $year_format, $month_number_format );

		$out = '';
		$out = apply_filters( 'cmsmasters_page_breadcrumbs_filter_before', $out );

		if ( '' !== $out ) {
			$out = $out;
		} elseif ( is_single() ) {
			$category = get_the_category();
			$num_cat = count( $category );

			if ( $num_cat < 1 ) {
				$out .= '<span>' . get_the_title( get_the_ID() ) . '</span>';
			} elseif ( $num_cat >= 1 ) {
				$out .= get_category_parents( $category[0], true, $sep ) . ' <span>' . get_the_title( get_the_ID() ) . '</span>';
			}
		} elseif ( is_category() ) {
			global $cat;

			$multiple_cats = get_category_parents( $cat, true, $sep );

			if ( is_string( $multiple_cats ) ) {
				$multiple_cats_array = explode( $sep, $multiple_cats );
			} else {
				$category = get_queried_object();

				$multiple_cats_array = array( $category->name );
			}

			$multiple_cats_array = array_diff( $multiple_cats_array, array( '' ) );

			foreach ( $multiple_cats_array as $single_cat ) {
				if ( end( $multiple_cats_array ) !== $single_cat ) {
					$out .= wp_kses_post( stripslashes( $single_cat ) );
					$out .= wp_kses_post( $sep );
				} else {
					$out .= '<span>' . single_cat_title( '', false ) . '</span>';
				}
			}
		} elseif ( is_tag() ) {
			$out .= '<span>' . single_tag_title( '', false ) . '</span>';
		} elseif ( is_day() ) {
			$out .= '<a href="' . esc_url( $url_year ) . '">' . esc_html( $year_format ) . '</a>' . $sep .
			'<a href="' . esc_url( $url_month ) . '">' . esc_html( $month_format ) . '</a>' . $sep .
			'<span>' . esc_html( $day_format ) . ' (' . esc_html( $day_full_format ) . ')</span>';
		} elseif ( is_month() ) {
			$out .= '<a href="' . esc_url( $url_year ) . '">' . esc_html( $year_format ) . '</a>' . $sep . '<span>' . esc_html( $month_format ) . '</span>';
		} elseif ( is_year() ) {
			$out .= '<span>' . esc_html( $year_format ) . '</span>';
		} elseif ( is_search() ) {
			$out .= '<span>' . esc_html__( 'Search results for', 'pediatric-clinic' ) . ': "' . esc_html( get_search_query() ) . '"</span>';
		} elseif ( is_page() && ! $post->post_parent ) {
			$out .= '<span>' . get_the_title( get_the_ID() ) . '</span>';
		} elseif ( is_page() && $post->post_parent ) {
			$post_array = get_post_ancestors( $post );
			krsort( $post_array );

			foreach ( $post_array as $key => $postid ) {
				$post_ids = get_post( $postid );

				$title = $post_ids->post_title;

				$out .= '<a href="' . esc_url( get_permalink( $post_ids ) ) . '">' . esc_html( $title ) . '</a>' . $sep;
			}

			$out .= '<span>' . get_the_title( get_the_ID() ) . '</span>';
		} elseif ( is_author() ) {
			$out .= '<span>' . esc_html( get_the_author() ) . '</span>';
		} elseif ( is_tax( 'post_format' ) ) {
			if ( is_tax( 'post_format', 'post-format-gallery' ) ) {
				$out .= '<span>' . esc_html_x( 'Galleries', 'post format archive title', 'pediatric-clinic' ) . '</span>';
			} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
				$out .= '<span>' . esc_html_x( 'Images', 'post format archive title', 'pediatric-clinic' ) . '</span>';
			} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
				$out .= '<span>' . esc_html_x( 'Videos', 'post format archive title', 'pediatric-clinic' ) . '</span>';
			} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
				$out .= '<span>' . esc_html_x( 'Audio', 'post format archive title', 'pediatric-clinic' ) . '</span>';
			}
		} elseif ( is_post_type_archive() ) {
			$out .= '<span>' . esc_html( post_type_archive_title( '', false ) ) . '</span>';
		} elseif ( is_tax() ) {
			$out .= '<span>' . esc_html( single_term_title( '', false ) ) . '</span>';
		} else {
			$out .= '<span>' . esc_html__( 'No breadcrumbs', 'pediatric-clinic' ) . '</span>';
		}

		$out = apply_filters( 'cmsmasters_page_breadcrumbs_filter_after', $out );

		return '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'pediatric-clinic' ) . '</a>' . $sep . $out;
	}

}
