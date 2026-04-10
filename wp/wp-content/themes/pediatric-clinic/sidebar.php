<?php
/**
 * The sidebar containing the main widget area
 */

use PediatricClinicSpace\TemplateFunctions\General_Elements;
use PediatricClinicSpace\TemplateFunctions\Main_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$layout = Main_Elements::get_main_layout();

if ( 'fullwidth' === $layout ) {
	return;
}

$page_id = General_Elements::get_singular_id();

if ( $page_id ) {
	$sidebar_id = get_post_meta( $page_id, 'cmsmasters_sidebar_id', true );
}

echo '<div class="cmsmasters-sidebar">';

if (
	isset( $sidebar_id ) &&
	is_dynamic_sidebar( $sidebar_id ) &&
	is_active_sidebar( $sidebar_id )
) {
	dynamic_sidebar( $sidebar_id );
} elseif ( is_home() || is_archive() ) {
	if ( is_active_sidebar( 'sidebar_archive' ) ) {
		dynamic_sidebar( 'sidebar_archive' );
	} elseif ( is_active_sidebar( 'sidebar_default' ) ) {
		dynamic_sidebar( 'sidebar_default' );
	}
} elseif ( is_search() ) {
	if ( is_active_sidebar( 'sidebar_search' ) ) {
		dynamic_sidebar( 'sidebar_search' );
	} elseif ( is_active_sidebar( 'sidebar_default' ) ) {
		dynamic_sidebar( 'sidebar_default' );
	}
} elseif ( is_active_sidebar( 'sidebar_default' ) ) {
	dynamic_sidebar( 'sidebar_default' );
}

echo '</div>';
