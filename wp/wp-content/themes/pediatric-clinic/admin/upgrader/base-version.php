<?php
namespace PediatricClinicSpace\Admin\Upgrader;

use PediatricClinicSpace\Admin\Upgrader\Upgrader_Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Base upgrades version.
 */
class Base_Version {

	/**
	 * Minor versions.
	 *
	 * @var array
	 */
	protected $minor_versions = array();

	/**
	 * Check minor versions.
	 *
	 * @var bool
	 */
	protected $check_minor;

	/**
	 * Version upgrades constructor.
	 */
	public function __construct( $check_minor ) {
		$this->check_minor = $check_minor;

		$this->init_upgrades();
	}

	/**
	 * Init upgrades.
	 */
	protected function init_upgrades() {
		foreach ( $this->minor_versions as $version ) {
			if ( $this->check_minor && intval( $version ) <= intval( Upgrader_Utils::get_minor_version() ) ) {
				continue;
			}

			call_user_func( array( $this, str_replace( '-', '_', "upgrade_v_{$version}" ) ) );
		}
	}

}
