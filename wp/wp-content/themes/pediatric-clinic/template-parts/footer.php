<?php
/**
 * The template for displaying footer section.
 */

use PediatricClinicSpace\Core\Utils\Utils;
use PediatricClinicSpace\TemplateFunctions\General_Elements;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


$parent_class = 'cmsmasters-footer';

echo '<footer id="footer" class="' . esc_attr( $parent_class ) . '">' .
	'<div class="' . esc_attr( $parent_class ) . '__outer">' .
		'<div class="' . esc_attr( $parent_class ) . '__inner cmsmasters-' . Utils::get_kit_option( 'cmsmasters_footer_type', 'horizontal' ) . '">';

$items_order = Utils::get_kit_option( 'cmsmasters_footer_elements', array( 'copyright' ) );

if ( ! empty( $items_order ) ) {
	foreach ( $items_order as $item ) {
		if ( 'logo' === $item ) {
			echo General_Elements::get_logo( array(
				'setting_id' => 'cmsmasters_footer_logo',
				'parent_class' => $parent_class . '-logo',
				'type' => 'image',
			) );
		} elseif ( 'nav' === $item && has_nav_menu( 'footer_nav' ) ) {
			echo '<div class="' . esc_attr( $parent_class ) . '-menu">' .
				wp_nav_menu( array(
					'theme_location' => 'footer_nav',
					'container' => 'nav',
					'container_class' => "{$parent_class}-menu__nav",
					'menu_class' => "{$parent_class}-menu__list",
					'menu_id' => 'footer_nav',
					'echo' => false,
				) ) .
			'</div>';
		} elseif ( 'info' === $item ) {
			echo General_Elements::get_short_info( array(
				'setting_id' => 'cmsmasters_footer_info',
				'parent_class' => $parent_class . '-info',
			) );
		} elseif ( 'html' === $item ) {
			echo General_Elements::get_custom_html( array(
				'setting_id' => 'cmsmasters_footer_html',
				'parent_class' => $parent_class . '-html',
			) );
		} elseif ( 'social' === $item ) {
			echo General_Elements::get_social_icons( array(
				'setting_id' => 'cmsmasters_footer_social',
				'parent_class' => $parent_class . '-social',
			) );
		} elseif ( 'copyright' === $item ) {
			echo '<div class="' . esc_attr( $parent_class ) . '-copyright">' .
				'<div class="' . esc_attr( $parent_class ) . '-copyright__outer">' .
					'<div class="' . esc_attr( $parent_class ) . '-copyright__inner">' .
						'<p>' . esc_html( Utils::get_kit_option( 'cmsmasters_footer_copyright_text', sprintf( esc_html__( 'cmsmasters &copy; %d / All Rights Reserved', 'pediatric-clinic' ), date( 'Y' ) ) ) ) . '</p>' .
					'</div>' .
				'</div>' .
			'</div>';
		}
	}
}

echo '</div>' .
'</div>' .
'</footer>';
