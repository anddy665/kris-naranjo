<?php
namespace CmsmastersElementor\Controls;

use CmsmastersElementor\Controls_Manager;

use Elementor\Control_Repeater;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Control_Custom_Repeater extends Control_Repeater {

	public function get_type() {
		return Controls_Manager::CUSTOM_REPEATER;
	}

	protected function get_default_settings() {
		$settings = parent::get_default_settings();

		$settings['prevent_empty'] = false;
		$settings['classes'] = 'elementor-control-type-repeater';

		return $settings;
	}

}
