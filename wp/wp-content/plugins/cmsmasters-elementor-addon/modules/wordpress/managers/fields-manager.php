<?php
namespace CmsmastersElementor\Modules\Wordpress\Managers;

use CmsmastersElementor\Modules\Wordpress\Fields\Base\Base_Field;
use CmsmastersElementor\Modules\Wordpress\Module as WordpressModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Fields_Manager {

	/**
	 * Input field.
	 */
	const INPUT = 'input';

	/**
	 * Textarea field.
	 */
	const TEXTAREA = 'textarea';

	/**
	 * Select field.
	 */
	const SELECT = 'select';

	/**
	 * HTML tag field.
	 */
	const HTML_TAG = 'html-tag';

	/**
	 * Raw HTML field.
	 */
	const RAW_HTML = 'raw-html';

	/**
	 * File field.
	 */
	const FILE = 'file';

	/**
	 * Drop zone field.
	 */
	const DROPZONE = 'dropzone';

	/**
	 * Repeater field.
	 */
	const REPEATER = 'repeater';

	/**
	 * Native field types.
	 *
	 * Holds the list of natively supported field types.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $native_field_types = array();

	/**
	 * Fields.
	 *
	 * Holds the list of all the fields.
	 *
	 * @since 1.0.0
	 *
	 * @var Base_Field[]
	 */
	private static $fields = array();

	private static $field_output_type = 'wrapper';

	public function __construct() {
		self::$native_field_types = array(
			self::INPUT,
			self::TEXTAREA,
			self::SELECT,
			self::HTML_TAG,
			self::RAW_HTML,
			self::FILE,
			self::DROPZONE,
			// self::REPEATER,
		);

		$this->register_fields();
	}

	private function register_fields() {
		$native_fields = array();

		foreach ( self::$native_field_types as $type ) {
			$class_name = str_replace( '-', '', ucwords( $type, '-' ) );

			$native_fields[ $type ] = sprintf( '%1$s\Fields\%2$s', WordpressModule::MODULE_NAMESPACE, $class_name );
		}

		$additional_fields = apply_filters( 'cmsmasters_elementor/wp_post_meta/fields_manager/additional_fields', array() );

		self::$fields = array_merge( $additional_fields, $native_fields );
	}

	public static function get_supported_field_types() {
		return array_keys( self::$fields );
	}

	public static function is_type_supported( $type ) {
		return in_array( $type, self::get_supported_field_types(), true );
	}

	public static function get_field_output_type() {
		return self::$field_output_type;
	}

	public function get_field( $attributes, $value = null ) {
		if ( ! isset( $attributes['field_type'] ) ) {
			return;
		}

		$handler = self::$fields[ $attributes['field_type'] ];

		if ( ! $handler ) {
			return;
		}

		/** @var Base_Field $field_handler */
		$field_handler = new $handler( $attributes, $value );

		if ( ! $field_handler ) {
			return;
		}

		self::$field_output_type = $field_handler->get_output_type();

		return $field_handler->get_field();
	}

}
