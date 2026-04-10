<?php
namespace PediatricClinicSpace\TemplateFunctions;

use PediatricClinicSpace\Core\Utils\Excerpt;
use PediatricClinicSpace\Core\Utils\Utils;
use PediatricClinicSpace\TemplateFunctions\Post_Media;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Post Elements handler class is responsible for different methods in templates.
 */
class Post_Elements {

	/**
	 * Get post heading.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Post heading HTML.
	 */
	public static function get_post_heading( $atts = array() ) {
		$req_vars = array(
			'parent_class' => '',
			'id' => get_the_ID(),
			'tag' => 'h3',
			'link' => true,
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( get_the_title( $id ) === $id || '' === $parent_class ) {
			return '';
		}

		$parent_class = "{$parent_class}-title";
		$section_container_class = '';

		if ( is_single() ) {
			$section_container_class = ' cmsmasters-section-container';

			if ( 'yes' !== Utils::get_kit_option( 'cmsmasters_single_heading_visibility', 'yes' ) ) {
				$tag = 'h1';
			}
		}

		$title_class = array(
			"{$parent_class}__tag",
			'entry-title',
		);

		$out = '<header class="' . esc_attr( $parent_class . $section_container_class ) . ' entry-header">' .
			'<' . esc_html( $tag ) . ' class="' . esc_attr( join( ' ', $title_class ) ) . '">' .
				( $link ? '<a href="' . esc_url( get_permalink( $id ) ) . '">' : '' ) .
					get_the_title( $id ) .
				( $link ? '</a>' : '' ) .
			'</' . esc_html( $tag ) . '>' .
		'</header>';

		return wp_kses_post( $out );
	}

	/**
	 * Get post media.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Post media HTML.
	 */
	public static function get_post_media( $atts = array() ) {
		$req_vars = array(
			'parent_class' => '',
			'id' => get_the_ID(),
			'format' => get_post_format(),
			'img_size' => 'full',
			'gallery_img_size' => 'full',
			'slider_settings_key' => '',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( '' === $parent_class ) {
			return '';
		}

		$out = '';

		if ( false === $format || 'image' === $format ) {
			$out = Post_Media::get_image( array(
				'size' => $img_size,
				'link' => true,
			) );
		} elseif ( 'video' === $format ) {
			$out = Post_Media::get_video();
		} elseif ( 'audio' === $format ) {
			$out = Post_Media::get_audio();
		} elseif ( 'gallery' === $format ) {
			$out = Post_Media::get_gallery( array(
				'size' => $gallery_img_size,
				'slider_settings_key' => $slider_settings_key,
			) );
		}

		$section_container_class = '';

		if ( is_single() ) {
			$section_container_class = ' cmsmasters-section-container';
		}

		if ( '' !== $out ) {
			$out = '<div class="' . esc_attr( $parent_class ) . '-media' . esc_attr( $section_container_class ) . '">' .
				'<div class="' . esc_attr( $parent_class ) . '-media__inner">' .
					$out .
				'</div>' .
			'</div>';
		}

		return $out;
	}

	/**
	 * Gets post terms.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Post terms HTML.
	 */
	public static function get_post_terms( $atts = array() ) {
		$req_vars = array(
			'parent_class' => '',
			'id' => get_the_ID(),
			'hierarchical' => true,
			'taxonomy' => '',
			'before' => '',
			'sep' => '',
			'after' => '',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( '' === $parent_class ) {
			return '';
		}

		$cat_taxes = array();
		$tag_taxes = array();

		$post_taxes = get_taxonomies( array(
			'object_type' => array( get_post_type() ),
			'show_in_nav_menus' => true,
		), 'objects' );

		foreach ( $post_taxes as $tax_key => $tax_args ) {
			if ( $tax_args->hierarchical ) {
				$cat_taxes[] = $tax_key;
			} else {
				$tag_taxes[] = $tax_key;
			}
		}

		$current_tax = '';

		if ( $hierarchical && isset( $cat_taxes[0] ) ) {
			$current_tax = $cat_taxes[0];
		} elseif ( ! $hierarchical && isset( $tag_taxes[0] ) ) {
			$current_tax = $tag_taxes[0];
		}

		if ( '' !== $taxonomy ) {
			$current_tax = $taxonomy;
		}

		if ( '' === $current_tax ) {
			return '';
		}

		$terms = get_the_term_list( $id, $current_tax, $before, $sep, $after );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}

		return '<div class="' . esc_attr( $parent_class ) . '-' . esc_attr( $current_tax ) . '">' .
			$terms .
		'</div>';
	}

	/**
	 * Get post date.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Post date HTML.
	 */
	public static function get_post_date( $atts = array() ) {
		$req_vars = array(
			'parent_class' => '',
			'id' => get_the_ID(),
			'format' => get_option( 'date_format' ),
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( '' === $parent_class ) {
			return '';
		}

		if ( 'product' === get_post_type() ) {
			return '';
		}

		$date = get_the_date( $format );
		$modified_date = get_the_modified_date();

		$out = '<time class="published" title="' . esc_attr( $date ) . '">' .
			esc_html( $date ) .
		'</time>' .
		'<time class="cmsmasters-dn date updated" title="' . esc_attr( $modified_date ) . '">' .
			esc_html( $modified_date ) .
		'</time>';

		if ( ! is_singular() && '' === get_the_title( $id ) ) {
			$out = '<a href="' . esc_url( get_permalink( $id ) ) . '">' . $out . '</a>';
		}

		$out = '<div class="' . esc_attr( $parent_class ) . '-date">' . $out . '</div>';

		return $out;
	}

	/**
	 * Get post author.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Post author HTML.
	 */
	public static function get_post_author( $atts = array() ) {
		$req_vars = array(
			'parent_class' => '',
			'prefix' => false,
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( '' === $parent_class ) {
			return '';
		}

		if ( 'product' === get_post_type() ) {
			return '';
		}

		$out = '<div class="' . esc_attr( $parent_class ) . '-author">' .
			( $prefix ? esc_html( $prefix ) . ' ' : '' ) .
			'<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr__( 'Posts by', 'pediatric-clinic' ) . ' ' . esc_attr( get_the_author_meta( 'display_name' ) ) . '" class="vcard author">' .
				'<span class="fn" rel="author">' .
					esc_html( get_the_author_meta( 'display_name' ) ) .
				'</span>' .
			'</a>' .
		'</div>';

		return $out;
	}

	/**
	 * Get post comments.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Post comments HTML.
	 */
	public static function get_post_comments( $atts = array() ) {
		$req_vars = array(
			'parent_class' => '',
			'type' => 'text',
			'icon_position' => 'left',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( ! comments_open() || '' === $parent_class ) {
			return '';
		}

		$out = '<div class="' . esc_attr( $parent_class ) . '-comments">';

		if ( 'icon' === $type ) {
			$out .= '<a class="cmsmasters-theme-icon-comment cmsmasters-' . esc_attr( $icon_position ) . '" href="' . esc_url( get_comments_link() ) . '" title="' . esc_attr__( 'Comment on', 'pediatric-clinic' ) . ' ' . esc_attr( get_the_title() ) . '">' .
				'<span>' . esc_html( get_comments_number() ) . '</span>' .
			'</a>';
		} else {
			$out .= esc_html__( 'Comments', 'pediatric-clinic' ) . ' (' .
			'<a href="' . esc_url( get_comments_link() ) . '" title="' . esc_attr__( 'Comment on', 'pediatric-clinic' ) . ' ' . esc_attr( get_the_title() ) . '">' .
				esc_html( get_comments_number() ) .
			'</a>' .
			')';
		}

		$out .= '</div>';

		return $out;
	}

	/**
	 * Get post meta.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Post meta HTML.
	 */
	public static function get_post_meta( $atts = array() ) {
		$req_vars = array(
			'parent_class' => '',
			'meta_key' => '',
			'elements' => array(),
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( ! is_array( $elements ) || '' === $meta_key || '' === $parent_class ) {
			return '';
		}

		if ( empty( $elements ) ) {
			return '';
		}

		$out = '';

		foreach ( $elements as $element ) {
			if ( 'categories' === $element ) {
				$out .= self::get_post_terms( array(
					'parent_class' => $parent_class,
					'hierarchical' => true,
					'before' => apply_filters( 'cmsmasters_theme_get_post_meta_categories_before', '' ),
					'sep' => apply_filters( 'cmsmasters_theme_get_post_meta_categories_sep', ', ' ),
					'after' => apply_filters( 'cmsmasters_theme_get_post_meta_categories_after', '' ),
				) );
			} elseif ( 'author' === $element ) {
				$out .= self::get_post_author( array(
					'parent_class' => $parent_class,
					'prefix' => apply_filters( 'cmsmasters_theme_get_post_meta_author_prefix', false ),
				) );
			} elseif ( 'date' === $element ) {
				$out .= self::get_post_date( array(
					'parent_class' => $parent_class,
				) );
			} elseif ( 'comments' === $element ) {
				$out .= self::get_post_comments( array(
					'parent_class' => $parent_class,
				) );
			} elseif ( 'tags' === $element ) {
				$out .= self::get_post_terms( array(
					'parent_class' => $parent_class,
					'hierarchical' => false,
					'before' => apply_filters( 'cmsmasters_theme_get_post_meta_tags_before', '<span>' . esc_html__( 'In', 'pediatric-clinic' ) . ': </span>' ),
					'sep' => apply_filters( 'cmsmasters_theme_get_post_meta_tags_sep', ', ' ),
					'after' => apply_filters( 'cmsmasters_theme_get_post_meta_tags_after', '' ),
				) );
			}
		}

		if ( '' !== $out ) {
			$parent_class = $parent_class . '-' . $meta_key;
			$section_container_class = '';

			if ( is_single() ) {
				$section_container_class = ' cmsmasters-section-container';
			}

			$out = '<div class="' . esc_attr( $parent_class . $section_container_class ) . ' entry-meta">' .
				'<div class="' . esc_attr( $parent_class ) . '__inner">' . $out . '</div>' .
			'</div>';
		}

		return $out;
	}

	/**
	 * Gets post excerpt.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Post excerpt.
	 */
	public static function get_post_excerpt( $atts = array() ) {
		$req_vars = array(
			'parent_class' => '',
			'length' => 55,
			'more' => '...',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( '' === $parent_class ) {
			return '';
		}

		$excerpt = new Excerpt( $length, $more );

		$out = $excerpt->get_excerpt();

		if ( '' !== $out ) {
			$out = '<div class="' . esc_attr( $parent_class ) . '-content entry-content">' .
				'<p>' . $out . '</p>' .
			'</div>';
		}

		return $out;
	}

	/**
	 * Get post more button.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Post more button HTML.
	 */
	public static function get_post_more( $atts = array() ) {
		$req_vars = array(
			'parent_class' => '',
			'text' => '',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( '' === $parent_class ) {
			return '';
		}

		$out = '';

		if ( '' !== $text ) {
			$parent_class = $parent_class . '-more';

			$out = '<div class="' . esc_attr( $parent_class ) . '">' .
				'<a class="' . esc_attr( $parent_class ) . '__link" href="' . esc_url( get_permalink( get_the_ID() ) ) . '">' . esc_html( $text ) . '</a>' .
			'</div>';
		}

		return $out;
	}

}
