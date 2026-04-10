<?php
namespace PediatricClinicSpace\TemplateFunctions;

use PediatricClinicSpace\Core\Utils\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * General Elements handler class is responsible for different methods in templates.
 */
class General_Elements {

	/**
	 * Get singular id.
	 *
	 * @return string singular id.
	 */
	public static function get_singular_id() {
		$id = false;

		if ( is_singular() ) {
			$id = get_the_ID();
		}

		return apply_filters( 'cmsmasters_singular_id_filter', $id );
	}

	/**
	 * Render html tag class.
	 */
	public static function html_tag_class() {
		$css_classes = apply_filters( 'cmsmasters_html_class', array() );

		if ( ! is_array( $css_classes ) || empty( $css_classes ) ) {
			return;
		}

		echo ' class="' . esc_attr( implode( ' ', $css_classes ) ) . '"';
	}

	/**
	 * Get logo.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Logo html.
	 */
	public static function get_logo( $atts = array() ) {
		$req_vars = array(
			'setting_id' => '',
			'parent_class' => '',
			'type' => 'image',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( '' === $setting_id || '' === $parent_class ) {
			return '';
		}

		$logo_title = ( get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : esc_html__( 'Site logo', 'pediatric-clinic' ) );

		if ( 'text' === $type ) {
			$logo_title_text = Utils::get_kit_option( $setting_id . '_title_text', '' );
			$logo_subtitle = Utils::get_kit_option( $setting_id . '_subtitle_text', '' );

			if ( '' === $logo_title_text ) {
				$logo_title_text = $logo_title;
			}

			$logo_out = '<span class="' . esc_attr( $parent_class ) . '__text">' .
				'<span class="' . esc_attr( $parent_class ) . '__title">' . esc_html( $logo_title_text ) . '</span>' .
				( '' !== $logo_subtitle ? '<span class="' . esc_attr( $parent_class ) . '__subtitle">' . esc_html( $logo_subtitle ) . '</span>' : '' ) .
			'</span>';
		} else {
			$logo_out = '';
			$logo_img_data = Utils::get_kit_option( $setting_id . '_image', array( 'id' => '' ) );

			if ( ! empty( $logo_img_data['id'] ) ) {
				if ( 'yes' === Utils::get_kit_option( $setting_id . '_second_toggle', false ) ) {
					$logo_retina_img_data_second = Utils::get_kit_option( $setting_id . '_retina_image_second', array( 'id' => '' ) );

					if ( ! empty( $logo_retina_img_data_second['id'] ) ) {
						$logo_out .= self::get_logo_img( array(
							'parent_class' => $parent_class,
							'id' => $logo_retina_img_data_second['id'],
							'title' => $logo_title,
							'type' => 'retina',
							'state' => 'second',
						) );
					}

					$logo_img_data_second = Utils::get_kit_option( $setting_id . '_image_second', array( 'id' => '' ) );

					if ( ! empty( $logo_img_data_second['id'] ) ) {
						$logo_out .= self::get_logo_img( array(
							'parent_class' => $parent_class,
							'id' => $logo_img_data_second['id'],
							'title' => $logo_title,
							'type' => 'normal',
							'state' => 'second',
						) );
					}
				}

				$logo_retina_img_data = Utils::get_kit_option( $setting_id . '_retina_image', array( 'id' => '' ) );

				if (
					'yes' === Utils::get_kit_option( $setting_id . '_retina_toggle', false ) &&
					! empty( $logo_retina_img_data['id'] )
				) {
					$logo_out .= self::get_logo_img( array(
						'parent_class' => $parent_class,
						'id' => $logo_retina_img_data['id'],
						'title' => $logo_title,
						'type' => 'retina',
					) );
				}

				$logo_out .= self::get_logo_img( array(
					'parent_class' => $parent_class,
					'id' => $logo_img_data['id'],
					'title' => $logo_title,
					'type' => 'normal',
				) );
			}

			if ( empty( $logo_out ) ) {
				$logo_out = '<img class="' . esc_attr( $parent_class ) . '__img" src="' . get_parent_theme_file_uri( 'theme-config/images/logo.svg' ) . '" alt="' . esc_attr( $logo_title ) . '" />';
			}
		}

		return '<div class="' . esc_attr( $parent_class ) . '">' .
			'<div class="' . esc_attr( $parent_class ) . '__outer">' .
				'<a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( $logo_title ) . '" class="' . esc_attr( $parent_class ) . '__link">' .
					$logo_out .
				'</a>' .
			'</div>' .
		'</div>';
	}

	/**
	 * Get logo image.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Logo image html.
	 */
	public static function get_logo_img( $atts = array() ) {
		$req_vars = array(
			'parent_class' => '',
			'id' => '',
			'title' => '',
			'type' => 'normal',
			'state' => 'main',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( empty( $id ) || empty( $parent_class ) ) {
			return '';
		}

		$img_data = wp_get_attachment_image_src( $id, 'full' );

		if ( empty( $img_data ) ) {
			return '';
		}

		$img_atts = array(
			'src="' . $img_data[0] . '"',
			'alt="' . $title . '"',
			'title="' . $title . '"',
		);

		if ( 'retina' === $type ) {
			$img_atts[] = 'width="' . round( intval( $img_data[1] ) / 2 ) . '"';
			$img_atts[] = 'height="' . round( intval( $img_data[2] ) / 2 ) . '"';
			$img_atts[] = 'class="' . esc_attr( "{$parent_class}__retina-img {$parent_class}-{$state}" ) . '"';
		} else {
			$img_atts[] = 'class="' . esc_attr( "{$parent_class}__img {$parent_class}-{$state}" ) . '"';
		}

		return '<img ' . implode( ' ', $img_atts ) . '/>';
	}

	/**
	 * Get custom HTML.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Custom html.
	 */
	public static function get_custom_html( $atts = array() ) {
		$req_vars = array(
			'setting_id' => '',
			'parent_class' => '',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( '' === $setting_id || '' === $parent_class ) {
			return '';
		}

		$out = '';
		$content = Utils::get_kit_option( $setting_id . '_text', '' );

		if ( '' !== $content ) {
			$out .= '<div class="' . esc_attr( $parent_class ) . '">' .
				'<div class="' . esc_attr( $parent_class ) . '__outer">' .
					'<div class="' . esc_attr( $parent_class ) . '__inner">' .
						$content .
					'</div>' .
				'</div>' .
			'</div>';
		}

		return $out;
	}

	/**
	 * Get short info items.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Short info items html.
	 */
	public static function get_short_info( $atts = array() ) {
		$req_vars = array(
			'setting_id' => '',
			'parent_class' => '',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( '' === $setting_id || '' === $parent_class ) {
			return '';
		}

		$items = Utils::get_kit_option( $setting_id . '_items', array() );

		if ( ! is_array( $items ) || empty( $items ) ) {
			return '';
		}

		$items_out = '';
		$icon_position = Utils::get_kit_option( $setting_id . '_icon_position', 'before' );

		foreach ( $items as $item ) {
			if ( ! isset( $item['icon'] ) && ! isset( $item['text'] ) ) {
				continue;
			}

			$icon = '';
			$text = '';
			$link = ( isset( $item['link'] ) ? $item['link'] : array() );

			if ( isset( $item['icon'] ) ) {
				$icon = Utils::render_icon( $item['icon'] );

				if ( '' !== $icon ) {
					$icon = '<span class="' . esc_attr( $parent_class ) . '__item-icon">' . $icon . '</span>';
				};
			}

			if ( isset( $item['text'] ) && '' !== $item['text'] ) {
				$text = '<span class="' . esc_attr( $parent_class ) . '__item-text">' . esc_html( $item['text'] ) . '</span>';
			}

			if ( 'before' === $icon_position ) {
				$content = $icon . $text;
			} else {
				$content = $text . $icon;
			}

			$content = '<span class="' . esc_attr( $parent_class ) . '__item-inner">' . $content . '</span>';

			$items_out .= '<li class="' . esc_attr( $parent_class ) . '__item">' .
				Utils::get_html_in_link( $content, $link ) .
			'</li>';
		}

		if ( '' === $items_out ) {
			return '';
		}

		$out = '<div class="' . esc_attr( $parent_class ) . '">' .
			'<ul class="' . esc_attr( $parent_class ) . '__list">' .
				$items_out .
			'</ul>' .
		'</div>';

		return $out;
	}

	/**
	 * Get social icons.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Social icons html.
	 */
	public static function get_social_icons( $atts = array() ) {
		$req_vars = array(
			'setting_id' => '',
			'parent_class' => '',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( '' === $setting_id || '' === $parent_class ) {
			return '';
		}

		$items = Utils::get_kit_option( $setting_id . '_items', array() );

		if ( ! is_array( $items ) || empty( $items ) ) {
			return '';
		}

		$items_out = '';

		foreach ( $items as $item ) {
			if ( ! isset( $item['icon'] ) ) {
				continue;
			}

			$icon = Utils::render_icon( $item['icon'] );

			if ( '' === $icon ) {
				continue;
			}

			$link = ( isset( $item['link'] ) ? $item['link'] : array() );

			$items_out .= '<li class="' . esc_attr( $parent_class ) . '__item">' .
				Utils::get_html_in_link(
					'<span class="' . esc_attr( $parent_class ) . '__item-icon elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">' . $icon . '</span>',
					$link
				) .
			'</li>';
		}

		if ( '' === $items_out ) {
			return '';
		}

		$out = '<div class="' . esc_attr( $parent_class ) . '">' .
			'<ul class="' . esc_attr( $parent_class ) . '__list">' .
				$items_out .
			'</ul>' .
		'</div>';

		return $out;
	}

	/**
	 * Get pagination.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Pagination HTML.
	 */
	public static function get_pagination( $atts = array() ) {
		$req_vars = array(
			'parent_class' => '',
			'max_num_pages' => null,
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

		if ( null === $max_num_pages ) {
			global $wp_query;

			$max_num_pages = $wp_query->max_num_pages;
		}

		if ( 2 > $max_num_pages ) {
			return '';
		}

		$format = '?paged=%#%';

		if ( get_query_var( 'paged' ) ) {
			$current = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
			$current = get_query_var( 'page' );

			$format = '/page/%#%';
		} else {
			$current = 1;
		}

		$args = array(
			'base' => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
			'format' => $format,
			'total' => $max_num_pages,
			'current' => $current,
			'show_all' => false,
			'end_size' => 1,
			'mid_size' => 1,
			'prev_next' => true,
			'prev_text' => '<span class="cmsmasters-theme-icon-pagination-' . ( ! is_rtl() ? 'prev' : 'next' ) . '"><span>' . esc_html( apply_filters( 'cmsmasters_pagination_prev_text_filter', '' ) ) . '</span></span>',
			'next_text' => '<span class="cmsmasters-theme-icon-pagination-' . ( ! is_rtl() ? 'next' : 'prev' ) . '"><span>' . esc_html( apply_filters( 'cmsmasters_pagination_next_text_filter', '' ) ) . '</span></span>',
			'type' => 'list',
			'add_args' => false,
			'add_fragment' => '',
		);

		if ( get_query_var( 's' ) ) {
			$search_query = get_query_var( 's' );

			$args['add_args'] = array(
				's' => rawurlencode( $search_query ),
			);
		}

		$links = paginate_links( $args );

		if ( $links && '' !== $links ) {
			$out = '<div class="' . esc_attr( $parent_class ) . '-pagination cmsmasters-pagination">' .
				paginate_links( $args ) .
			'</div>';
		}

		return $out;
	}

	/**
	 * Get pagination.
	 *
	 * @return string Sub Pagination HTML.
	 */
	public static function get_sub_pagination() {
		return wp_link_pages( array(
			'before' => '<div class="cmsmasters-subpage-nav"><span class="cmsmasters-subpage-nav__title">' . esc_html__( 'Pages', 'pediatric-clinic' ) . ':</span><div class="cmsmasters-subpage-nav__items">',
			'after' => '</div></div>',
			'link_before' => '<span>',
			'link_after' => '</span>',
			'echo' => false,
		) );
	}

	/**
	 * Get search form.
	 *
	 * @return string search form HTML.
	 */
	public static function get_search_form( $plugin = false ) {
		$parent_class = 'cmsmasters-search-form';

		$placeholder_text = ( 'woocommerce' === $plugin ? esc_html__( 'Search products&hellip;', 'pediatric-clinic' ) : esc_html__( 'Search...', 'pediatric-clinic' ) );

		$out = '<form class="' . esc_attr( $parent_class ) . '" method="get" action="' . esc_url( home_url( '/' ) ) . '">
			<input type="search" class="' . esc_attr( $parent_class ) . '__input" placeholder="' . esc_attr( $placeholder_text ) . '" value="' . get_search_query() . '" name="s" />
			<button type="submit" class="' . esc_attr( $parent_class ) . '__button"><i class="cmsmasters-theme-icon-search-button"></i></button>' .
			( 'woocommerce' === $plugin ? '<input type="hidden" name="post_type" value="product" />' : '' ) .
		'</form>';

		return $out;
	}

}
