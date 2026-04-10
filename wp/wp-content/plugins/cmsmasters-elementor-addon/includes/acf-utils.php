<?php
namespace CmsmastersElementor;

use Elementor\Controls_Manager as ElementorControlsManager;
use Elementor\Core\DynamicTags\Base_Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Advanced Custom Fields plugin utils handler class is responsible for
 * different utility methods used by current plugin.
 *
 * @since 1.1.0
 */
class Acf_Utils {

	/**
	 * Get control options.
	 *
	 * Retrieves the Advanced Custom Fields plugin control options fields.
	 *
	 * @since 1.1.0
	 * @since 1.2.0 Added repeatable fields support.
	 *
	 * @param array $types
	 * @param bool $is_repeater
	 *
	 * @return array Control options.
	 */
	public static function get_control_options( $types, $is_repeater = false ) {
		if ( ! class_exists( '\acf' ) || ! function_exists( 'acf_get_field_groups' ) ) {
			return array();
		}

		$acf_field_groups = acf_get_field_groups();

		$groups = array();

		foreach ( $acf_field_groups as $field_group ) {
			if ( isset( $field_group['ID'] ) && ! empty( $field_group['ID'] ) ) {
				$fields = acf_get_fields( $field_group['ID'] );
			} else {
				$fields = acf_get_fields( $field_group );
			}

			if ( ! is_array( $fields ) ) {
				continue;
			}

			$options = ( ! $is_repeater ) ?
				self::get_group_fields_options( $fields, $types ) :
				self::get_repeater_group_fields_options( $fields, $types );

			if ( empty( $options ) ) {
				continue;
			}

			if ( 1 === count( $options ) ) {
				$options = array( -1 => '---' ) + $options;
			}

			$groups[] = array(
				'label' => $field_group['title'],
				'options' => $options,
			);
		}

		return $groups;
	}

	/**
	 * Get group fields options.
	 *
	 * Retrieves the Advanced Custom Fields plugin control group fields options.
	 *
	 * @since 1.1.0
	 *
	 * @param array $fields
	 * @param array $types
	 * @param array $group_field
	 *
	 * @return array
	 */
	public static function get_group_fields_options( $fields, $types, $group_field = array() ) {
		$options = array();

		foreach ( $fields as $field ) {
			if ( isset( $field['sub_fields'] ) && ! empty( $field['sub_fields'] ) ) {
				if ( 'repeater' === $field['type'] ) {
					continue;
				}

				$sub_options = self::get_group_fields_options( $field['sub_fields'], $types, $field );

				$options = array_merge( $options, $sub_options );
			} else {
				if ( ! in_array( $field['type'], $types, true ) ) {
					continue;
				}

				$key = "{$field['key']}:{$field['name']}";
				$label = $field['label'];

				if ( ! empty( $group_field ) ) {
					$key = "{$group_field['key']}:{$key}";
					$group_label = $group_field['label'];

					$label = "{$group_label}: {$label}";
				}

				$options[ $key ] = $label;
			}
		}

		return $options;
	}

	/**
	 * Get repeater group fields options.
	 *
	 * Retrieves the Advanced Custom Fields plugin control repeater group fields options.
	 *
	 * @since 1.2.0
	 *
	 * @param array $fields
	 * @param array $types
	 * @param array $group_field
	 *
	 * @return array
	 */
	public static function get_repeater_group_fields_options( $fields, $types, $group_field = array() ) {
		$options = array();

		foreach ( $fields as $field ) {
			if ( isset( $field['sub_fields'] ) && ! empty( $field['sub_fields'] ) ) {
				if ( 'repeater' !== $field['type'] ) {
					continue;
				}

				$sub_options = self::get_repeater_group_fields_options( $field['sub_fields'], $types, $field );

				$options = array_merge( $options, $sub_options );
			} else {
				if ( ! in_array( $field['type'], $types, true ) || empty( $group_field ) ) {
					continue;
				}

				$key = "cmsms_rep:{$group_field['key']}:{$field['key']}:{$field['name']}";
				$label = "{$group_field['label']}: {$field['label']}";

				$options[ $key ] = $label;
			}
		}

		return $options;
	}

	/**
	 * Add key control.
	 *
	 * Creates Advanced Custom Fields plugin meta field key control.
	 *
	 * @since 1.1.0
	 * @since 1.2.0 Added repeatable fields support.
	 *
	 * @param Base_Tag $tag Base dynamic tag instance class.
	 * @param bool $is_repeater Whether to show only repeater fields.
	 */
	public static function add_key_control( Base_Tag $tag, $is_repeater = false ) {
		$tag->add_control(
			'key',
			array(
				'label' => __( 'Key', 'cmsmasters-elementor' ),
				'type' => ElementorControlsManager::SELECT,
				'groups' => self::get_control_options( $tag->get_supported_fields(), $is_repeater ),
			)
		);

		if ( $is_repeater ) {
			$tag->add_control(
				'warning',
				array(
					'type' => ElementorControlsManager::RAW_HTML,
					'raw' => __( 'Please do not use this dynamic tag on not repeatable controls.', 'cmsmasters-elementor' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				)
			);
		}
	}

	/**
	 * Get key field.
	 *
	 * Retrieves Advanced Custom Fields plugin selected meta field data.
	 *
	 * @since 1.1.0
	 * @since 1.2.0 Added repeatable fields support.
	 *
	 * @param Base_Tag $tag Base dynamic tag instance class.
	 *
	 * @return array Selected ACF meta field data array.
	 */
	public static function get_key_field( Base_Tag $tag ) {
		$key = $tag->get_settings( 'key' );

		if ( empty( $key ) ) {
			return array();
		}

		$keys = array_reverse( explode( ':', $key ) );

		list( $meta_key, $field_key, $group_key, $is_repeater ) = array_pad( $keys, 4, false );

		if ( 'options' === $field_key ) {
			$field = get_field_object( $meta_key, $field_key );
		} else {
			$field = get_field_object( $field_key );
		}

		if ( $field && ! empty( $field['type'] ) ) {
			$value = $field['value'];

			if ( $group_key ) {
				$group = get_field( $group_key );

				if ( 'cmsms_rep' === $is_repeater ) {
					$value = array();

					if ( $group && is_array( $group ) ) {
						foreach ( $group as $group_item ) {
							$value[] = isset( $group_item[ $meta_key ] ) ? $group_item[ $meta_key ] : '';
						}
					}
				} else {
					$value = isset( $group[ $meta_key ] ) ? $group[ $meta_key ] : '';
				}
			}
		} else {
			$value = get_field( $meta_key );
		}

		return array(
			$field,
			$value,
			$meta_key,
		);
	}

}
