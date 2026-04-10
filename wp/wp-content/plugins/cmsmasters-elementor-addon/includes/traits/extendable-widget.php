<?php
namespace CmsmastersElementor\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Extendable widget trait.
 *
 * Provides special methods for extending widgets.
 *
 * @since 1.0.0
 * @since 1.0.1 Removed $condition_sets property.
 */
trait Extendable_Widget {

	/**
	 * Get HTML wrapper class.
	 *
	 * Retrieve the widget container class.
	 *
	 * Can be used to override the container class for specific widgets.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget container class.
	 */
	protected function get_html_wrapper_class() {
		$parent_html_wrapper = parent::get_html_wrapper_class();
		$parent_classes = explode( ' ', $parent_html_wrapper );

		$widget_class = $this->get_extendable_widget_class();

		if ( ! in_array( $widget_class, $parent_classes, true ) ) {
			$parent_classes[] = $widget_class;
		}

		return implode( ' ', $parent_classes );
	}

	/**
	 * Add conditions set.
	 *
	 * Creates new widget controls conditions set.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Conditions set name.
	 * @param array $condition Conditions array.
	 *
	 * @return bool Returns `false` if condition already exists, `true` on add.
	 */
	protected function add_conditions_set( $name, $condition ) {
		if ( isset( $this->condition_sets[ $name ] ) ) {
			return false;
		}

		$this->condition_sets[ $name ] = $condition;

		return true;
	}

	/**
	 * Update conditions set.
	 *
	 * Modifies widget controls conditions set.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Conditions set name.
	 * @param array $condition Conditions array.
	 *
	 * @return bool Returns `false` if condition doesn't exists or
	 * conditions are equal, `true` on update.
	 */
	protected function update_conditions_set( $name, $condition ) {
		if ( ! isset( $this->condition_sets[ $name ] ) || $condition === $this->condition_sets[ $name ] ) {
			return false;
		}

		$this->condition_sets[ $name ] = $condition;

		return true;
	}

	/**
	 * Get conditions set.
	 *
	 * Retrieves widget controls conditions set or sets.
	 *
	 * @since 1.0.0
	 *
	 * @param bool|string $name Conditions set name.
	 *
	 * @return bool|array Returns `false` if selected condition doesn't exists,
	 * all sets or selected condition set if `$name` is set.
	 */
	protected function get_conditions_set( $name = false ) {
		if ( ! $name ) {
			return $this->condition_sets;
		}

		if ( $name && isset( $this->condition_sets[ $name ] ) ) {
			return $this->condition_sets[ $name ];
		}

		return false;
	}

	/**
	 * Extend conditions array.
	 *
	 * Extends an array of widget control conditions.
	 *
	 * @since 1.0.0
	 *
	 * @param array $conditions Widget conditions.
	 * @param array $new_condition Additional condition.
	 *
	 * @return array Extended array of conditions.
	 */
	protected function extend_conditions_array( $conditions, $new_condition ) {
		$conditions['terms'] = array_map( function( $terms ) use ( $new_condition ) {
			$terms['terms'][] = $new_condition;

			return $terms;
		}, $conditions['terms'] );

		return $conditions;
	}

}
