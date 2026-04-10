<?php
namespace PediatricClinicSpace\Woocommerce\CmsmastersFramework\Kits\Settings\Woocommerce;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Gutenberg settings.
 */
class Woocommerce_Pages extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'pages';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'WooCommerce Pages', 'pediatric-clinic' );
	}

	/**
	 * Get control ID prefix.
	 *
	 * Retrieve the control ID prefix.
	 *
	 * @return string Control ID prefix.
	 */
	protected static function get_control_id_prefix() {
		$toggle_name = self::get_toggle_name();

		return parent::get_control_id_prefix() . "_{$toggle_name}";
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		$this->add_control(
			'woocommerce_pages_intro',
			array(
				'raw' => esc_html__( 'Select the pages you want to use as your default WooCommerce shop pages', 'pediatric-clinic' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			)
		);

		$autocomplete = array(
			'object' => Query_Manager::POST_OBJECT,
			'query' => array(
				'post_type' => array( 'page' ),
			),
		);

		$this->add_control(
			'woocommerce_cart_page_id',
			array(
				'label' => esc_html__( 'Cart', 'pediatric-clinic' ),
				'type' => CmsmastersControls::QUERY,
				'select2options' => array(
					'placeholder' => esc_html__( 'Select a page', 'pediatric-clinic' ),
				),
				'autocomplete' => $autocomplete,
				'default' => get_option( 'woocommerce_cart_page_id', '' ),
			)
		);

		$this->add_control(
			'woocommerce_checkout_page_id',
			array(
				'label' => esc_html__( 'Checkout', 'pediatric-clinic' ),
				'type' => CmsmastersControls::QUERY,
				'select2options' => array(
					'placeholder' => esc_html__( 'Select a page', 'pediatric-clinic' ),
				),
				'autocomplete' => $autocomplete,
				'default' => get_option( 'woocommerce_checkout_page_id', '' ),
			)
		);

		$this->add_control(
			'woocommerce_myaccount_page_id',
			array(
				'label' => esc_html__( 'My Account', 'pediatric-clinic' ),
				'type' => CmsmastersControls::QUERY,
				'select2options' => array(
					'placeholder' => esc_html__( 'Select a page', 'pediatric-clinic' ),
				),
				'autocomplete' => $autocomplete,
				'default' => get_option( 'woocommerce_myaccount_page_id', '' ),
			)
		);

		$this->add_control(
			'woocommerce_terms_page_id',
			array(
				'label' => esc_html__( 'Terms & Conditions', 'pediatric-clinic' ),
				'type' => CmsmastersControls::QUERY,
				'select2options' => array(
					'placeholder' => esc_html__( 'Select a page', 'pediatric-clinic' ),
				),
				'autocomplete' => $autocomplete,
				'default' => get_option( 'woocommerce_terms_page_id', '' ),
			)
		);

		$this->add_control(
			'woocommerce_purchase_summary_page_id',
			array(
				'label' => esc_html__( 'Purchase Summary', 'pediatric-clinic' ),
				'type' => CmsmastersControls::QUERY,
				'select2options' => array(
					'placeholder' => esc_html__( 'Select a page', 'pediatric-clinic' ),
				),
				'autocomplete' => $autocomplete,
				'default' => get_option( 'cmsmasters_pages_woocommerce_purchase_summary_page_id', '' ),
			)
		);

		$this->add_control(
			'woocommerce_pages_notice',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Note: Changes you make here will also be reflected in the WooCommerce settings on your WP dashboard', 'pediatric-clinic' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);
	}

	public function on_save( $data ) {
		if (
			! isset( $data['settings']['post_status'] ) ||
			$data['settings']['post_status'] !== Document::STATUS_PUBLISH ||
			0 === strpos( current_action(), 'update_option_' )
		) {
			return;
		}

		$ec_wc_key_mapping = array(
			'cmsmasters_pages_woocommerce_cart_page_id' => 'woocommerce_cart_page_id',
			'cmsmasters_pages_woocommerce_checkout_page_id' => 'woocommerce_checkout_page_id',
			'cmsmasters_pages_woocommerce_myaccount_page_id' => 'woocommerce_myaccount_page_id',
			'cmsmasters_pages_woocommerce_terms_page_id' => 'woocommerce_terms_page_id',
			'cmsmasters_pages_woocommerce_purchase_summary_page_id' => 'cmsmasters_pages_woocommerce_purchase_summary_page_id',
		);

		foreach ( $ec_wc_key_mapping as $ec_key => $wc_key ) {
			if ( array_key_exists( $ec_key, $data['settings'] ) ) {
				$value = $data['settings'][ $ec_key ] ? $data['settings'][ $ec_key ] : '';

				update_option( $wc_key, $value );
			}
		}
	}

}
