<?php
/**
 * The site's entry point.
 *
 * loads the relevant template part,
 * the loop is executed (when needed) by the relevant template part.
 */

use PediatricClinicSpace\TemplateFunctions\Main_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header();

$is_elementor_template_exist = function_exists( 'cmsmasters_template_do_location' );
$is_elementor_theme_exist = function_exists( 'elementor_theme_do_location' );

if ( is_singular() ) {
	if (
		( ! $is_elementor_template_exist || ! cmsmasters_template_do_location( 'singular' ) ) &&
		( ! $is_elementor_theme_exist || ! elementor_theme_do_location( 'single' ) )
	) {
		echo Main_Elements::main_wrapper_start();

		if ( is_page() ) {
			get_template_part( 'template-parts/page' );
		} elseif ( is_single() ) {
			get_template_part( 'template-parts/single' );
		}

		echo Main_Elements::main_wrapper_end();
	}
} elseif ( is_archive() || is_home() || is_search() ) {
	if (
		( ! $is_elementor_template_exist || ! cmsmasters_template_do_location( 'archive' ) ) &&
		( ! $is_elementor_theme_exist || ! elementor_theme_do_location( 'archive' ) )
	) {
		echo Main_Elements::main_wrapper_start();

		get_template_part( 'template-parts/archive' );

		echo Main_Elements::main_wrapper_end();
	}
} else {
	if (
		( ! $is_elementor_template_exist || ! cmsmasters_template_do_location( 'singular' ) ) &&
		( ! $is_elementor_theme_exist || ! elementor_theme_do_location( 'single' ) )
	) {
		echo Main_Elements::main_wrapper_start();

		get_template_part( 'template-parts/404' );

		echo Main_Elements::main_wrapper_end();
	}
}

get_footer();
