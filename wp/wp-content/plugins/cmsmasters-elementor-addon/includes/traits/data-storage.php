<?php
namespace CmsmastersElementor\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Data Storage trait.
 *
 * Collects the selected data and store it to DB options table.
 *
 * @since 1.0.0
 */
trait Data_Storage {

	/**
	 * Storage option data.
	 *
	 * Holds the storage option value.
	 *
	 * @since 1.0.0
	 *
	 * @var array Value of the option.
	 */
	protected $option_data = array();

	/**
	 * Get instance of base class.
	 *
	 * Ensures only one instance of the base class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return object Single instance of the base class.
	 */
	protected function load_data() {
		$this->option_data = get_option( static::OPTION_NAME, array() );

		return $this;
	}

	protected function update_db() {
		return update_option( static::OPTION_NAME, $this->option_data );
	}

	public function clear_data() {
		$this->option_data = array();

		return $this;
	}

	public function get_data_by_id( $data_id ) {
		if ( ! isset( $this->option_data[ $data_id ] ) ) {
			return array();
		}

		return $this->option_data[ $data_id ];
	}

	protected function add_data( $data_id, $data_record ) {
		$this->option_data[ $data_id ] = $data_record;

		return $this;
	}

	protected function remove_data( $data_id ) {
		if ( isset( $this->option_data[ $data_id ] ) ) {
			unset( $this->option_data[ $data_id ] );
		}

		return $this;
	}

}
