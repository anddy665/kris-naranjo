<?php
namespace CmsmastersElementor\Modules\Wordpress\Fields\Base;

use CmsmastersElementor\Modules\Wordpress\Fields\Interfaces\Meta_Field;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


abstract class Base_Field implements Meta_Field {

	protected $parameters = array();

	protected $value = '';

	protected $attributes_to_replace = array();

	public function __construct( $parameters, $value = null ) {
		$this->parameters = $parameters;

		if ( $value ) {
			$this->value = $value;
		}
	}

	public function get_output_type() {
		return Utils::get_if_isset( $this->parameters, 'output', 'wrapper' );
	}

	public function get_attributes_string( array $keys = array( 'id' ), $set_name = true ) {
		if ( ! is_array( $keys ) ) {
			$keys = array();
		}

		$attributes = array();

		if ( $set_name && in_array( 'id', $keys, true ) ) {
			array_unshift( $keys, 'name' );

			$this->parameters['name'] = Utils::get_if_isset( $this->parameters, 'name', $this->parameters['id'] );
		}

		if ( isset( $this->parameters['attributes'] ) && is_array( $this->parameters['attributes'] ) ) {
			$extra = $this->parameters['attributes'];

			if ( isset( $extra['id'] ) ) {
				unset( $extra['id'] );
			}

			if ( isset( $extra['name'] ) ) {
				unset( $extra['name'] );
			}

			$this->parameters = array_merge( $this->parameters, $extra );

			$keys = array_merge( $keys, array_keys( $extra ) );

			unset( $extra );
		}

		foreach ( $keys as $key ) {
			$name = $this->check_attribute_to_replace( $key );

			$attributes[] = sprintf( "{$name}=\"%s\"", esc_attr( $this->parameters[ $key ] ) );
		}

		return implode( ' ', $attributes );
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	protected function add_attributes_for_replace( $attributes, $clear = false ) {
		if ( $clear ) {
			$this->attributes_to_replace = array();
		}

		foreach ( $attributes as $old_key => $new_key ) {
			if ( isset( $this->attributes_to_replace[ $old_key ] ) ) {
				return;
			}

			$this->attributes_to_replace[ $old_key ] = $new_key;
		}
	}

	public function get_attributes_for_replace() {
		return $this->attributes_to_replace;
	}

	public function check_attribute_to_replace( $attribute ) {
		if ( ! isset( $this->attributes_to_replace[ $attribute ] ) ) {
			return $attribute;
		}

		return $this->attributes_to_replace[ $attribute ];
	}

}
