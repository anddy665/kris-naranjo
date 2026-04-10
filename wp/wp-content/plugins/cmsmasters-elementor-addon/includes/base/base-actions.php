<?php
namespace CmsmastersElementor\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


abstract class Base_Actions {

	public function __construct() {
		$this->init_actions();
		$this->init_filters();
	}

	protected function init_actions() {}

	protected function init_filters() {}

}
