<?php
use CmsmastersElementor\Modules\TemplateLocations\Module as LocationsModule;
use CmsmastersElementor\Utils;
use CmsmastersElementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


function get_cmsmasters_locations_manager() {
	/** @var LocationsModule $locations_module */
	$locations_module = LocationsModule::instance();

	return $locations_module->get_locations_manager();
}

function cmsmasters_template_location_exists( $location, $check_match = false ) {
	if ( Utils::is_pro() && Utils::use_theme_builder() ) {
		if ( 'singular' === $location ) {
			$location = 'single';
		}

		return elementor_location_exits( $location, $check_match );
	}

	return get_cmsmasters_locations_manager()->location_exists( $location, $check_match );
}

function cmsmasters_template_do_location( $location ) {
	if ( Utils::is_edit_mode() || Utils::is_preview_mode() ) {
		$document_id = $_REQUEST['elementor-preview'];

		$document = Plugin::elementor()->documents->get( $document_id );

		$document_class_name = $document::get_class_full_name();

		if ( false !== strpos( $document_class_name, 'CmsmastersElementor' ) ) {
			return get_cmsmasters_locations_manager()->do_template_location( $location );
		} elseif ( false !== strpos( $document_class_name, 'ElementorPro' ) ) {
			return elementor_theme_do_location( $location );
		}
	}

	if ( Utils::is_pro() && Utils::use_theme_builder() ) {
		if ( 'singular' === $location ) {
			$location = 'single';
		}

		if ( elementor_theme_do_location( $location ) ) {
			return true;
		} elseif ( 'single' === $location ) {
			$location = 'singular';
		}
	}

	return get_cmsmasters_locations_manager()->do_template_location( $location );
}
