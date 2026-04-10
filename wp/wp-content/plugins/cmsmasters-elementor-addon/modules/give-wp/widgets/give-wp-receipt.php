<?php
namespace CmsmastersElementor\Modules\GiveWp\Widgets;

use Elementor\Controls_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Give_WP_Receipt extends Give_WP_Base {

	/**
	 * @since 1.24.0
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.6.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return 'cmsmasters-give-wp-receipt';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.6.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'GiveWP Receipt', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.6.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-receipt';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @since 1.6.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'receipt',
		);
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.6.0
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_goal',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'donor',
			array(
				'label' => __( 'Show Donor', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'company_name',
			array(
				'label' => __( 'Show Company Name', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'date',
			array(
				'label' => __( 'Show Date', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'price',
			array(
				'label' => __( 'Show Total Donation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'payment_status',
			array(
				'label' => __( 'Show Donation Status', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'payment_id',
			array(
				'label' => __( 'Show Donation ID', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'payment_method',
			array(
				'label' => __( 'Show Payment Method', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'status_notice',
			array(
				'label' => __( 'Show Status Notice', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'error',
			array(
				'label' => __( 'Error Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => array(
					'active' => true,
				),
				'default' => __( 'You are missing the donation id to view this donation receipt.', 'cmsmasters-elementor' ),
				'label_block' => true,
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get shortcode widget.
	 *
	 * Retrieve the widget shortcode.
	 *
	 * @since 1.6.0
	 *
	 * @return string The widget shortcode.
	 */
	public function get_shortcode() {
		$settings = $this->get_settings_for_display();

		$price = ( 'yes' === $settings['price'] ) ? true : false;
		$donor = ( 'yes' === $settings['donor'] ) ? true : false;
		$date = ( 'yes' === $settings['date'] ) ? true : false;
		$payment_method = ( 'yes' === $settings['payment_method'] ) ? true : false;
		$payment_id = ( 'yes' === $settings['payment_id'] ) ? true : false;
		$company_name = ( 'yes' === $settings['company_name'] ) ? true : false;
		$payment_status = ( 'yes' === $settings['payment_status'] ) ? true : false;
		$status_notice = ( 'yes' === $settings['status_notice'] ) ? true : false;
		$error = $settings['error'];

		return "[give_receipt 
				price=\"{$price}\" 
				date=\"{$date}\" 
				payment_method=\"{$payment_method}\" 
				payment_id=\"{$payment_id}\" 
				company_name=\"{$company_name}\" 
				payment_status=\"{$payment_status}\" 
				status_notice=\"{$status_notice}\" 
				error=\"{$error}\" 
				donor=\"{$donor}\"]";
	}

	public function add_filter_for_editor_content() {
		return false;
	}
}
