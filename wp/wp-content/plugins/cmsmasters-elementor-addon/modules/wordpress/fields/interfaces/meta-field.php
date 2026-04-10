<?php
namespace CmsmastersElementor\Modules\Wordpress\Fields\Interfaces;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


interface Meta_Field {

	public function get_name();

	public function get_field();

}
