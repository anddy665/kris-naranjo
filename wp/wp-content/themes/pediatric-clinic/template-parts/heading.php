<?php
/**
 * The template for displaying heading section.
 */

use PediatricClinicSpace\Core\Utils\Utils;
use PediatricClinicSpace\TemplateFunctions\Heading_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if (
	is_404() ||
	is_home() ||
	is_front_page() ||
	( is_single() && 'yes' !== Utils::get_kit_option( 'cmsmasters_single_heading_visibility', 'no' ) )
) {
	return;
}

$heading_parent_class = 'cmsmasters-heading';
$breadcrumbs_parent_class = 'cmsmasters-breadcrumbs';
$heading_content = '<div class="' . esc_attr( $heading_parent_class ) . '__wrap">' . Heading_Elements::get_page_title() . '</div>';
$heading_breadcrumbs_class = '';
$breadcrumbs_new_row = '';
$breadcrumbs_visibility = Utils::get_kit_option( 'cmsmasters_breadcrumbs_visibility', 'yes' );

if ( 'yes' === $breadcrumbs_visibility ) {
	$breadcrumbs_type = Utils::get_kit_option( 'cmsmasters_breadcrumbs_type', 'in_title' );
	$breadcrumbs_position = Utils::get_kit_option( 'cmsmasters_breadcrumbs_position' );
	$breadcrumbs_resp_visibility = Utils::get_kit_option( 'cmsmasters_breadcrumbs_resp_visibility', 'show' );
	$breadcrumbs_resp_visibility_class = ( 'show' !== $breadcrumbs_resp_visibility ? ' cmsmasters-breadcrumbs-resp-' . $breadcrumbs_resp_visibility : '' );
	$breadcrumbs_content = '<div class="' . esc_attr( $breadcrumbs_parent_class ) . '__wrap">' . Heading_Elements::get_page_breadcrumbs() . '</div>';

	if ( 'new_row' !== $breadcrumbs_type ) {
		$heading_breadcrumbs_class = ' cmsmasters-heading-breadcrumbs-type-' . $breadcrumbs_type . $breadcrumbs_resp_visibility_class;
	}

	if ( 'in_title' === $breadcrumbs_type ) {
		if ( 'above_title' === $breadcrumbs_position ) {
			$heading_content = $breadcrumbs_content . $heading_content;
		} else {
			$heading_content = $heading_content . $breadcrumbs_content;
		}
	} elseif ( 'new_row' === $breadcrumbs_type ) {
		$breadcrumbs_new_row = '<div class="' . esc_attr( $breadcrumbs_parent_class . $breadcrumbs_resp_visibility_class ) . '">' .
			'<div class="' . esc_attr( $breadcrumbs_parent_class ) . '__outer">' .
				'<div class="' . esc_attr( $breadcrumbs_parent_class ) . '__inner">' .
					$breadcrumbs_content .
				'</div>' .
			'</div>' .
		'</div>';
	}
}

echo '<div class="cmsmasters-headline">' .
	'<div class="' . esc_attr( $heading_parent_class . $heading_breadcrumbs_class ) . '">' .
		'<div class="' . esc_attr( $heading_parent_class ) . '__outer">' .
			'<div class="' . esc_attr( $heading_parent_class ) . '__inner">' .
				$heading_content .
			'</div>' .
		'</div>' .
	'</div>' .
	$breadcrumbs_new_row .
'</div>';
