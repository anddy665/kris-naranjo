<?php
/**
 * The template for displaying header top section.
 */

use PediatricClinicSpace\Core\Utils\Utils;
use PediatricClinicSpace\TemplateFunctions\General_Elements;
use PediatricClinicSpace\TemplateFunctions\Header_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$parent_class = 'cmsmasters-header-top';
$items_order = Utils::get_kit_option( 'cmsmasters_header_top_elements', array( 'nav' ) );
$content_out = '';

if ( ! empty( $items_order ) ) {
	foreach ( $items_order as $item ) {
		if ( 'info' === $item ) {
			$content_out .= General_Elements::get_short_info( array(
				'setting_id' => 'cmsmasters_header_top_info',
				'parent_class' => $parent_class . '-info',
			) );
		} elseif ( 'nav' === $item ) {
			$content_out .= Header_Elements::get_nav_menu( array(
				'setting_id' => 'header_top_nav',
				'parent_class' => $parent_class . '-menu',
				'location' => 'header_top_nav',
			) );
		} elseif ( 'html' === $item ) {
			$content_out .= General_Elements::get_custom_html( array(
				'setting_id' => 'cmsmasters_header_top_html',
				'parent_class' => $parent_class . '-html',
			) );
		} elseif ( 'social' === $item ) {
			$content_out .= General_Elements::get_social_icons( array(
				'setting_id' => 'cmsmasters_header_top_social',
				'parent_class' => $parent_class . '-social',
			) );
		}
	}
}

if ( in_array( 'nav', $items_order, true ) ) {
	$content_out .= Header_Elements::get_nav_burger_menu_button( array(
		'setting_id' => 'header_top_nav',
		'parent_class' => $parent_class . '-burger-menu-button',
		'location' => 'header_top_nav',
	) );
}

if ( '' === $content_out ) {
	return;
}

$toggle_open_icon = Utils::get_kit_option( 'cmsmasters_header_top_toggle_normal_icon', array() );
$toggle_open_icon = Utils::render_icon( $toggle_open_icon );
$toggle_open_icon = empty( $toggle_open_icon ) ? '<i class="cmsmasters-theme-icon-header-top-open"></i>' : $toggle_open_icon;

$toggle_close_icon = Utils::get_kit_option( 'cmsmasters_header_top_toggle_active_icon', array() );
$toggle_close_icon = Utils::render_icon( $toggle_close_icon );
$toggle_close_icon = empty( $toggle_close_icon ) ? '<i class="cmsmasters-theme-icon-header-top-close"></i>' : $toggle_close_icon;

$single_item_class = '';

if ( 1 === count( $items_order ) ) {
	$single_item_class = ' ' . $parent_class . '-single-item';
}

echo '<div class="' . esc_attr( $parent_class . $single_item_class ) . '">' .
	'<div class="' . esc_attr( $parent_class ) . '__outer">' .
		'<div class="' . esc_attr( $parent_class ) . '__inner">' .
			$content_out .
		'</div>' .
	'</div>';

	if ( 1 < count( $items_order ) ) {
		echo '<div class="' . esc_attr( $parent_class ) . '-toggle">' .
			'<span class="' . esc_attr( $parent_class ) . '-toggle__inner">' .
				'<span class="' . esc_attr( $parent_class ) . '-toggle__open">' . $toggle_open_icon . '</span>' .
				'<span class="' . esc_attr( $parent_class ) . '-toggle__close">' . $toggle_close_icon . '</span>' .
			'</span>' .
		'</div>';
	}

echo '</div>';
