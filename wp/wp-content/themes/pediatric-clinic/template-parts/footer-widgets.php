<?php
/**
 * The template for displaying footer widgets section.
 */

use PediatricClinicSpace\Core\Utils\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$visibility = Utils::get_kit_option( 'cmsmasters_footer_widgets_visibility' );

if ( 'hide' === $visibility ) {
	return;
}

$columns = Utils::get_kit_option( 'cmsmasters_footer_widgets_columns' );

$sidebars_count = 0;

for ( $i = 1; $i <= $columns; $i++ ) {
	if ( is_active_sidebar( 'footer-' . $i ) ) {
		$sidebars_count += 1;
	}
}

if ( $sidebars_count < 1 ) {
	return;
}

if ( '1' === $columns ) {
	$layout = '11';
} elseif ( '5' === $columns ) {
	$layout = '1515151515';
} else {
	$layout = Utils::get_kit_option( 'cmsmasters_footer_widgets_layout_' . $columns );
}

if ( '1' === $columns ) {
	$tablet_layout = $layout;
} else {
	$tablet_layout = Utils::get_kit_option( 'cmsmasters_footer_widgets_tablet_layout_from_' . $columns );
}

if ( '1' === $tablet_layout ) {
	$tablet_layout = '11';
} elseif ( '2' === $tablet_layout ) {
	$tablet_layout = '1212';
} elseif ( '3' === $tablet_layout ) {
	$tablet_layout = '131313';
} elseif ( '4' === $tablet_layout ) {
	$tablet_layout = '14141414';
} elseif ( '5' === $tablet_layout ) {
	$tablet_layout = '1515151515';
}

$parent_class = 'cmsmasters-footer-widgets';
$footer_widgets_classes = array( $parent_class );

if ( 'show' !== $visibility ) {
	$footer_widgets_classes[] = "cmsmasters-{$visibility}";
}

echo '<div class="' . esc_attr( join( ' ', $footer_widgets_classes ) ) . '">' .
	'<div class="' . esc_attr( $parent_class ) . '__outer">';

$footer_widgets_inner_classes = array(
	"{$parent_class}__inner",
	"cmsmasters-layout-{$layout}",
	"cmsmasters-tablet-layout-{$tablet_layout}",
);

if ( true === Utils::get_kit_option( 'cmsmasters_footer_widgets_tablet_columns_reverse', false ) ) {
	$footer_widgets_inner_classes[] = 'cmsmasters-tablet-reverse';
}

if ( true === Utils::get_kit_option( 'cmsmasters_footer_widgets_mobile_columns_reverse', false ) ) {
	$footer_widgets_inner_classes[] = 'cmsmasters-mobile-reverse';
}

echo '<div class="' . esc_attr( join( ' ', $footer_widgets_inner_classes ) ) . '">';

for ( $i = 1; $i <= $columns; $i++ ) {
	if ( is_active_sidebar( 'footer-' . $i ) ) {
		$footer_widgets_area_classes = array(
			"{$parent_class}__area",
			'cmsmasters-' . esc_attr( $i ),
		);

		if ( true === Utils::get_kit_option( 'cmsmasters_footer_widgets_tablet_hide_' . $i, false ) ) {
			$footer_widgets_area_classes[] = 'cmsmasters-tablet-hide';
		}

		if ( true === Utils::get_kit_option( 'cmsmasters_footer_widgets_mobile_hide_' . $i, false ) ) {
			$footer_widgets_area_classes[] = 'cmsmasters-mobile-hide';
		}

		echo '<div class="' . esc_attr( join( ' ', $footer_widgets_area_classes ) ) . '">';
			dynamic_sidebar( 'footer-' . $i );
		echo '</div>';
	}
}

echo '</div>' .
'</div>' .
'</div>';
