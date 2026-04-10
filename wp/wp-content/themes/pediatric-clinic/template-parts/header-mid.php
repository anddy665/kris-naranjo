<?php
/**
 * The template for displaying header middle section.
 */

use PediatricClinicSpace\Core\Utils\Utils;
use PediatricClinicSpace\TemplateFunctions\General_Elements;
use PediatricClinicSpace\TemplateFunctions\Header_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$parent_class = 'cmsmasters-header-mid';
$header_type = Utils::get_kit_option( 'cmsmasters_header_mid_type', 'wide' );

echo '<div class="' . esc_attr( $parent_class ) . '">' .
	'<div class="' . esc_attr( $parent_class ) . '__outer">' .
		'<div class="' . esc_attr( $parent_class ) . '__inner cmsmasters-type-' . esc_attr( $header_type ) . '">';

echo General_Elements::get_logo( array(
	'setting_id' => 'cmsmasters_logo',
	'parent_class' => $parent_class . '-logo',
	'type' => Utils::get_kit_option( 'cmsmasters_logo_type', 'image' ),
) );

if ( 'centered' !== $header_type ) {
	$content = '';
	$content_element = Utils::get_kit_option( 'cmsmasters_header_mid_content_element', 'none' );

	if ( 'nav' === $content_element ) {
		$content .= Header_Elements::get_nav_menu( array(
			'setting_id' => 'header_mid_nav',
			'parent_class' => $parent_class . '-menu',
			'location' => 'header_mid_nav',
		) );
	} elseif ( 'info' === $content_element ) {
		$content .= General_Elements::get_short_info( array(
			'setting_id' => 'cmsmasters_header_mid_info',
			'parent_class' => $parent_class . '-info',
		) );
	} elseif ( 'html' === $content_element ) {
		$content .= General_Elements::get_custom_html( array(
			'setting_id' => 'cmsmasters_header_mid_html',
			'parent_class' => $parent_class . '-html',
		) );
	}

	if ( '' !== $content ) {
		$content = '<div class="' . esc_attr( $parent_class ) . '__content">' . $content . '</div>';
	}

	$add_content = '';
	$add_content_elements = Utils::get_kit_option( 'cmsmasters_header_mid_add_content_elements', array() );

	if ( ! empty( $add_content_elements ) ) {
		foreach ( $add_content_elements as $item ) {
			if ( 'social' === $item ) {
				$add_content .= General_Elements::get_social_icons( array(
					'setting_id' => 'cmsmasters_header_mid_social',
					'parent_class' => $parent_class . '-social',
				) );
			} elseif ( 'button' === $item ) {
				$add_content .= Header_Elements::get_button( array(
					'setting_id' => 'cmsmasters_header_mid_button',
					'parent_class' => $parent_class . '-button',
				) );
			} elseif ( 'search_button' === $item ) {
				$add_content .= Header_Elements::get_search_button( array(
					'setting_id' => 'cmsmasters_header_mid_search_button',
					'parent_class' => $parent_class . '-search-button',
				) );
			}
		}
	}

	if ( 'nav' === $content_element ) {
		$add_content .= Header_Elements::get_nav_burger_menu_button( array(
			'setting_id' => 'header_mid_nav',
			'parent_class' => $parent_class . '-burger-menu-button',
			'location' => 'header_mid_nav',
		) );
	}

	if ( '' !== $add_content ) {
		$add_content = '<div class="' . esc_attr( $parent_class ) . '__add-content' . ( empty( $add_content_elements ) ? ' cmsmasters-only-burger-button' : '' ) . '">' . $add_content . '</div>';
	}

	if ( '' !== $content || '' !== $add_content ) {
		echo '<div class="' . esc_attr( $parent_class ) . '__content-wrap">' .
			$content .
			$add_content .
		'</div>';
	}
}

echo '</div>' .
'</div>' .
'</div>';
