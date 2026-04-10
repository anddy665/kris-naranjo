<?php
namespace PediatricClinicSpace\TemplateFunctions;

use PediatricClinicSpace\Core\Utils\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Header Elements handler class is responsible for different methods in templates.
 */
class Header_Elements {

	/**
	 * Get navigation menu.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Navigation html.
	 */
	public static function get_nav_menu( $atts = array() ) {
		$req_vars = array(
			'setting_id' => '',
			'parent_class' => '',
			'location' => '',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( '' === $setting_id || '' === $parent_class || '' === $location ) {
			return '';
		}

		$out = '';

		if ( has_nav_menu( $location ) ) {
			$burger_alignment = Utils::get_kit_option( 'cmsmasters_' . $setting_id . '_burger_container_alignment', 'wide' );

			$out .= '<div class="' . esc_attr( $parent_class ) . ' cmsmasters-menu">' .
				wp_nav_menu( apply_filters( 'cmsmasters_menu_args', array(
					'theme_location' => esc_attr( $location ),
					'container' => 'nav',
					'container_class' => "{$parent_class}__nav cmsmasters-menu__nav cmsmasters-burger-alignment-{$burger_alignment}",
					'menu_class' => "{$parent_class}__list cmsmasters-menu__list",
					'menu_id' => esc_attr( $location ),
					'echo' => false,
					'link_before' => '<span class="cmsmasters-menu__item"><span class="cmsmasters-menu__item-inner">',
					'link_after' => '</span></span>',
				) ) ) .
			'</div>';
		}

		return $out;
	}

	/**
	 * Get navigation burger menu button.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Burger menu button html.
	 */
	public static function get_nav_burger_menu_button( $atts = array() ) {
		$req_vars = array(
			'setting_id' => '',
			'parent_class' => '',
			'location' => '',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( '' === $setting_id || '' === $parent_class || '' === $location ) {
			return '';
		}

		$out = '';

		if ( has_nav_menu( $location ) ) {
			$open_icon = Utils::get_kit_option( 'cmsmasters_' . $setting_id . '_burger_button_normal_icon', array() );
			$open_icon = Utils::render_icon( $open_icon );
			$open_icon = empty( $open_icon ) ? '<i class="cmsmasters-theme-icon-burger-menu-open"></i>' : $open_icon;

			$close_icon = Utils::get_kit_option( 'cmsmasters_' . $setting_id . '_burger_button_active_icon', array() );
			$close_icon = Utils::render_icon( $close_icon );
			$close_icon = empty( $close_icon ) ? '<i class="cmsmasters-theme-icon-burger-menu-close"></i>' : $close_icon;

			$out = '<div class="' . esc_attr( $parent_class ) . ' cmsmasters-burger-menu-button">' .
				'<div class="' . esc_attr( $parent_class ) . '__outer cmsmasters-burger-menu-button__outer">' .
					'<span class="' . esc_attr( $parent_class ) . '__toggle cmsmasters-burger-menu-button__toggle">' .
						'<span class="cmsmasters-burger-menu-button__toggle-open">' . $open_icon . '</span>' .
						'<span class="cmsmasters-burger-menu-button__toggle-close">' . $close_icon . '</span>' .
					'</span>' .
				'</div>' .
			'</div>';
		}

		return $out;
	}

	/**
	 * Get search button.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Search button html.
	 */
	public static function get_search_button( $atts = array() ) {
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

		$icon = Utils::get_kit_option( $setting_id . '_icon', array() );
		$icon = Utils::render_icon( $icon );
		$icon = empty( $icon ) ? '<i class="cmsmasters-theme-icon-header-search-button"></i>' : $icon;

		$out = '<div class="' . esc_attr( $parent_class ) . '">' .
			'<div class="' . esc_attr( $parent_class ) . '__outer">' .
				'<span class="' . esc_attr( $parent_class ) . '__toggle cmsmasters-header-search-button-toggle">' . $icon . '</span>' .
			'</div>' .
		'</div>';

		return $out;
	}

	/**
	 * Get header search form.
	 *
	 * @return string Search form html.
	 */
	public static function get_header_search_form() {
		$parent_class = 'cmsmasters-header-search-form';

		$out_form = '<form method="get" action="' . esc_url( home_url( '/' ) ) . '">' .
			'<div class="' . esc_attr( $parent_class ) . '__field">' .
				'<button type="submit" class="' . esc_attr( $parent_class ) . '__button"><i class="cmsmasters-theme-icon-header-search"></i></button>' .
				'<input type="search" class="' . esc_attr( $parent_class ) . '__input" name="s" placeholder="' . esc_attr__( 'Search', 'pediatric-clinic' ) . '" value="" />' .
			'</div>' .
		'</form>';

		$out_form = apply_filters( 'cmsmasters_header_search_form_filter', $out_form );

		$out = '<div class="' . esc_attr( $parent_class ) . '">' .
			'<span class="' . esc_attr( $parent_class ) . '__close cmsmasters-theme-icon-header-search-close"></span>' .
			$out_form .
		'</div>';

		return $out;
	}

	/**
	 * Get button.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Button html.
	 */
	public static function get_button( $atts = array() ) {
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

		$text = Utils::get_kit_option( $setting_id . '_text', '' );
		$link_atts = Utils::get_kit_option( $setting_id . '_link', array() );
		$icon_atts = Utils::get_kit_option( $setting_id . '_icon', array() );
		$icon = Utils::render_icon( $icon_atts );
		$icon_position = Utils::get_kit_option( $setting_id . '_icon_position', 'before' );

		if ( '' === $text && '' === $icon ) {
			return '';
		}

		$text = ( '' !== $text ? '<span class="' . esc_attr( $parent_class ) . '__text">' . esc_html( $text ) . '</span>' : '' );
		$icon = ( '' !== $icon ? '<span class="' . esc_attr( $parent_class ) . '__icon">' . $icon . '</span>' : '' );

		if ( 'before' === $icon_position ) {
			$content = $icon . $text;
		} else {
			$content = $text . $icon;
		}

		$out = '<div class="' . esc_attr( $parent_class ) . '">' .
			'<div class="' . esc_attr( $parent_class ) . '__inner">' .
				'<a class="' . esc_attr( $parent_class ) . '__link" ' . Utils::get_link_attributes( $link_atts ) . '>' .
					$content .
				'</a>' .
			'</div>' .
		'</div>';

		return $out;
	}

}
