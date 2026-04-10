<?php
namespace PediatricClinicSpace\Modules;

use PediatricClinicSpace\Core\Utils\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Swiper handler class is responsible for swiper methods.
 */
final class Swiper {

	/**
	 * Swiper constructor.
	 *
	 * Run swiper methods.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_assets' ) );
	}

	/**
	 * Enqueue assets.
	 */
	public function wp_enqueue_assets() {
		// Enqueue style
		if ( did_action( 'elementor/loaded' ) ) {
			return;
		}

		wp_register_style(
			'swiper',
			get_template_directory_uri() . '/assets/lib/swiper/css/swiper.css',
			array(),
			'1.0.0',
			'screen'
		);
	}

	/**
	 * Get slider HTML.
	 *
	 * @param array $atts Attributes.
	 *
	 * @return string Slider HTML.
	 */
	public static function get_slider( $atts = array() ) {
		$req_vars = array(
			'items' => array(),
			'settings_key' => '',
			'columns_available' => false,
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( ! is_array( $items ) || empty( $items ) ) {
			return '';
		}

		$items_out = '';

		foreach ( $items as $item ) {
			$items_out .= '<div class="swiper-slide">' . $item . '</div>';
		}

		if ( '' === $items_out ) {
			return '';
		}

		$settings = array(
			'autoplay' => false,
			'speed' => Utils::get_kit_option( "{$settings_key}_animation_speed" ),
			'loop' => ( 'yes' === Utils::get_kit_option( "{$settings_key}_infinite" ) ? true : false ),
			'mousewheel' => ( 'yes' === Utils::get_kit_option( "{$settings_key}_mousewheel" ) ? true : false ),
			'freeMode' => ( 'yes' === Utils::get_kit_option( "{$settings_key}_free_mode" ) ? true : false ),
		);

		if ( 'yes' === Utils::get_kit_option( "{$settings_key}_autoplay" ) ) {
			$settings['autoplay'] = array(
				'delay' => Utils::get_kit_option( "{$settings_key}_autoplay_speed" ),
				'reverseDirection' => Utils::get_kit_option( "{$settings_key}_autoplay_reverse" ),
				'disableOnInteraction' => false,
			);
		}

		if ( $columns_available ) {
			$breakpoints = Utils::get_breakpoints();

			$mobile_breakpoint = '0';
			$tablet_breakpoint = strval( $breakpoints['mobile'] );
			$desktop_breakpoint = strval( $breakpoints['tablet'] );

			$settings = array_merge( $settings, array(
				'centeredSlides' => ( 'yes' === Utils::get_kit_option( "{$settings_key}_centered_slides" ) ? true : false ),
				'slidesPerView' => Utils::get_kit_option( "{$settings_key}_slides_per_view", 1 ),
				'slidesPerGroup' => Utils::get_kit_option( "{$settings_key}_slides_to_scroll", 1 ),
				'spaceBetween' => Utils::get_kit_option( "{$settings_key}_space_between", 0 ),
				'breakpoints' => array(
					$mobile_breakpoint => array(
						'slidesPerView' => Utils::get_kit_option( "{$settings_key}_slides_per_view_mobile", 1 ),
						'slidesPerGroup' => Utils::get_kit_option( "{$settings_key}_slides_to_scroll_mobile", 1 ),
						'spaceBetween' => Utils::get_kit_option( "{$settings_key}_space_between_mobile", 0 ),
					),
					$tablet_breakpoint => array(
						'slidesPerView' => Utils::get_kit_option( "{$settings_key}_slides_per_view_tablet", 1 ),
						'slidesPerGroup' => Utils::get_kit_option( "{$settings_key}_slides_to_scroll_tablet", 1 ),
						'spaceBetween' => Utils::get_kit_option( "{$settings_key}_space_between_tablet", 0 ),
					),
					$desktop_breakpoint => array(
						'slidesPerView' => Utils::get_kit_option( "{$settings_key}_slides_per_view", 1 ),
						'slidesPerGroup' => Utils::get_kit_option( "{$settings_key}_slides_to_scroll", 1 ),
						'spaceBetween' => Utils::get_kit_option( "{$settings_key}_space_between", 0 ),
					),
				),
			) );
		}

		$arrows = ( 'yes' === Utils::get_kit_option( "{$settings_key}_arrows" ) ? true : false );
		$pagination = Utils::get_kit_option( "{$settings_key}_navigation" );
		$options = array(
			'arrows' => $arrows,
			'pagination' => $pagination,
			'pause_on_hover' => false,
		);

		if ( false !== $settings['autoplay'] && 'yes' === Utils::get_kit_option( "{$settings_key}_pause_on_hover" ) ) {
			$options['pause_on_hover'] = true;
		}

		$classes = array(
			'cmsmasters-swiper',
		);

		if ( $arrows ) {
			$classes[] = 'cmsmasters-arrows-visibility-' . Utils::get_kit_option( 'cmsmasters_slider_arrows_visibility' );
			$classes[] = 'cmsmasters-arrows-text-direction-' . Utils::get_kit_option( 'cmsmasters_slider_arrows_text_direction' );
		}

		if ( 'none' !== $pagination ) {
			$classes[] = "cmsmasters-pagination-{$pagination}";

			if ( 'bullets' === $pagination ) {
				$options['bullets_type'] = Utils::get_kit_option( 'cmsmasters_slider_bullets_type' );
			}
		}

		$classes = implode( ' ', $classes );

		$out = '<div ' .
			'id="' . uniqid( 'cmsmasters-swiper-' ) . '" ' .
			'class="' . esc_attr( $classes ) . '" ' .
			'data-settings="' . esc_attr( wp_json_encode( $settings ) ) . '"' .
			'data-options="' . esc_attr( wp_json_encode( $options ) ) . '"' .
		'>' .
			'<div class="cmsmasters-swiper__container swiper swiper-container">' .
				'<div class="cmsmasters-swiper__wrapper swiper-wrapper">' .
					$items_out .
				'</div>' .
			'</div>' .
			( $arrows ? self::get_arrows() : '' ) .
			( 'none' !== $pagination ? self::get_pagination() : '' ) .
		'</div>';

		return $out;
	}

	/**
	 * Get arrows HTML.
	 *
	 * @return string Arrows HTML.
	 */
	private static function get_arrows() {
		$out = '<div class="cmsmasters-swiper__buttons">' .
		'<div class="cmsmasters-swiper__buttons-inner">';

		foreach ( array( 'prev', 'next' ) as $arrow_type ) {
			$icon = Utils::get_kit_option( "cmsmasters_slider_arrows_{$arrow_type}_icon", array() );
			$text = Utils::get_kit_option( "cmsmasters_slider_arrows_{$arrow_type}_text", '' );
			$icon_position = Utils::get_kit_option( "cmsmasters_slider_arrows_{$arrow_type}_icon_position", '' );

			$out .= '<div class="cmsmasters-swiper__button cmsmasters-' . esc_attr( $arrow_type ) . '">' .
				'<div class="cmsmasters-swiper__button-inner">' .
					( 'before' === $icon_position ? Utils::render_icon( $icon ) : '' ) .
					( '' !== $text ? '<span>' . esc_html( $text ) . '</span>' : '' ) .
					( 'after' === $icon_position ? Utils::render_icon( $icon ) : '' ) .
				'</div>' .
			'</div>';
		}

		$out .= '</div>' .
		'</div>';

		return $out;
	}

	/**
	 * Get pagination HTML.
	 *
	 * @return string Pagination HTML.
	 */
	private static function get_pagination() {
		$out = '<div class="cmsmasters-swiper__pagination">' .
			'<div class="cmsmasters-swiper__pagination-outer">' .
				'<div class="cmsmasters-swiper__pagination-items"></div>' .
			'</div>' .
		'</div>';

		return $out;
	}

}
