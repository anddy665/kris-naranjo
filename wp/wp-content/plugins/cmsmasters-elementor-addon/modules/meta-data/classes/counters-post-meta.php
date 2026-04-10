<?php
namespace CmsmastersElementor\Modules\MetaData\Classes;

use CmsmastersElementor\Modules\MetaData\Module as MetaDataModule;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters Elementor counter postmeta.
 *
 * @since 1.0.0
 */
class Counters_Post_Meta {
	const PREFIX = 'cmsmasters_pm';

	/**
	 * Type of postmeta.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Settings of postmeta.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * Counter Postmeta constructor.
	 *
	 * Initializing the meta data class.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param string $type Type postmeta.
	 * @param array|null $settings Settings postmeta.
	 */
	public function __construct( $type, $settings = array() ) {
		$settings_default = array(
			'remove' => true,
		);

		$this->type = $type;
		$this->settings = array_merge( $settings_default, $settings );

		add_action( "wp_ajax_{$this->get_name()}", array( $this, 'count_change' ) );
		add_action( "wp_ajax_nopriv_{$this->get_name()}", array( $this, 'count_change' ) );
	}

	/**
	 * Get type.
	 *
	 * Retrieve the postmeta type.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get name.
	 *
	 * Retrieve the postmeta name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_name() {
		return join( '_', array( self::PREFIX, $this->get_type() ) );
	}

	/**
	 * Get counter.
	 *
	 * Retrieve the counter postmeta.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_count( $post_id = null ) {
		$post = get_post( $post_id );

		return (int) get_post_meta( $post->ID, $this->get_name(), true );
	}

	/**
	 * Get current ip address for postmeta.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_ips_pm_name() {
		return join( '_', array( $this->get_name(), 'ips' ) );
	}

	/**
	 * Check active ip address.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function exist( $post_id = null ) {
		$ips = $this->get_ips( $post_id );

		return in_array( Utils::get_client_ip_as_key(), $ips, true );
	}

	/**
	 * Get active ip addresses.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_ips( $post_id = null ) {
		$post = get_post( $post_id );
		$ips = get_post_meta( $post->ID, $this->get_ips_pm_name(), true );

		if ( ! $ips ) {
			return array();
		}

		return $ips;
	}

	/**
	 * Remove client ip from the list of ip addresses.
	 *
	 * @since 1.0.0
	 */
	private function remove_ip( $post = null ) {
		$post = get_post( $post );
		$ips = $this->get_ips( $post->ID );
		$ip_key = array_search( Utils::get_client_ip_as_key(), $ips, true );

		if ( $ip_key || 0 === $ip_key ) {
			unset( $ips[ $ip_key ] );
		}

		update_post_meta( $post->ID, $this->get_ips_pm_name(), $ips );
	}


	/**
	 * Add client ip to the list of ip addresses.
	 *
	 * @since 1.0.0
	 *
	 * @param int|null $post_id
	 */
	private function add_ip( $post_id = null ) {
		$post = get_post( $post_id );
		$ips = $this->get_ips( $post_id );

		$ips[] = Utils::get_client_ip_as_key();

		update_post_meta( $post->ID, $this->get_ips_pm_name(), $ips );
	}

	/**
	 * Ajax handler.
	 *
	 * User interaction handler.
	 *
	 * @since 1.0.0
	 */
	public function count_change() {
		if ( ! check_ajax_referer( MetaDataModule::instance()->get_name(), 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => 'Nonce code has not been installed or does not match' ) );
		}

		$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : false;

		if ( ! $post_id ) {
			wp_send_json_error( array( 'message' => 'Required fields have not been added' ) );
		}

		$count_old = $this->get_count( $post_id );
		$exist = $this->exist( $post_id );
		$count_new = $count_old;

		if ( ! $exist ) {
			$count_new++;

			$this->add_ip( $post_id );
		} elseif ( $this->settings['remove'] ) {
			$count_new--;

			$count_new = max( 0, $count_new );

			$this->remove_ip( $post_id );
		}

		if ( $count_new !== $count_old ) {
			update_post_meta( $post_id, $this->get_name(), $count_new );
		}

		wp_send_json_success( array(
			'count' => $count_new,
			'active' => ! $exist,
		) );
	}
}
