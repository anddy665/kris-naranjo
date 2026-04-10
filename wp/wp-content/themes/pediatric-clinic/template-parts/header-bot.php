<?php
/**
 * The template for displaying header bottom section.
 */

use PediatricClinicSpace\Core\Utils\Utils;
use PediatricClinicSpace\TemplateFunctions\General_Elements;
use PediatricClinicSpace\TemplateFunctions\Header_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$parent_class = 'cmsmasters-header-bot';
$header_type = Utils::get_kit_option( 'cmsmasters_header_bot_type', 'centered' );

$nav_menu_out = Header_Elements::get_nav_menu( array(
	'setting_id' => 'header_bot_nav',
	'parent_class' => $parent_class . '-menu',
	'location' => 'header_bot_nav',
) );

$add_content_out = '';

if ( 'centered' !== $header_type ) {
	$add_content_elements = Utils::get_kit_option( 'cmsmasters_header_bot_add_content_elements', array() );

	if ( ! empty( $add_content_elements ) ) {
		foreach ( $add_content_elements as $item ) {
			if ( 'social' === $item ) {
				$add_content_out .= General_Elements::get_social_icons( array(
					'setting_id' => 'cmsmasters_header_bot_social',
					'parent_class' => $parent_class . '-social',
				) );
			} elseif ( 'button' === $item ) {
				$add_content_out .= Header_Elements::get_button( array(
					'setting_id' => 'cmsmasters_header_bot_button',
					'parent_class' => $parent_class . '-button',
				) );
			} elseif ( 'search_button' === $item ) {
				$add_content_out .= Header_Elements::get_search_button( array(
					'setting_id' => 'cmsmasters_header_bot_search_button',
					'parent_class' => $parent_class . '-search-button',
				) );
			}
		}
	}
}

if ( '' !== $nav_menu_out ) {
	$add_content_out .= Header_Elements::get_nav_burger_menu_button( array(
		'setting_id' => 'header_bot_nav',
		'parent_class' => $parent_class . '-burger-menu-button',
		'location' => 'header_bot_nav',
	) );
}

if ( '' !== $add_content_out ) {
	$add_content_out = '<div class="' . esc_attr( $parent_class ) . '__add-content">' . $add_content_out . '</div>';
}

if ( '' === $nav_menu_out && '' === $add_content_out ) {
	return;
}

echo '<div class="' . esc_attr( $parent_class ) . '">' .
	'<div class="' . esc_attr( $parent_class ) . '__outer">' .
		'<div class="' . esc_attr( $parent_class ) . '__inner cmsmasters-type-' . esc_attr( $header_type ) . '">' .
			$nav_menu_out .
			$add_content_out .
		'</div>' .
	'</div>' .
'</div>';
