<?php
namespace CmsmastersElementor\Modules\GiveWp\Widgets;

use Elementor\Controls_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Give_WP_History extends Give_WP_Base {

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
		return 'cmsmasters-give-wp-history';
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
		return __( 'GiveWP History', 'cmsmasters-elementor' );
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
		return 'cmsicon-donation-history';
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
			'history',
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
			'section_history',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'id_history',
			array(
				'label' => __( 'Show ID', 'cmsmasters-elementor' ),
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
			'amount',
			array(
				'label' => __( 'Show Amount', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'status',
			array(
				'label' => __( 'Show Payment Status', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'payment_method',
			array(
				'label' => __( 'Show Payment Method', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
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

		$id_history = ( 'yes' === $settings['id_history'] ) ? true : false;
		$date = ( 'yes' === $settings['date'] ) ? true : false;
		$donor = ( 'yes' === $settings['donor'] ) ? true : false;
		$payment_method = ( 'yes' === $settings['payment_method'] ) ? true : false;
		$amount = ( 'yes' === $settings['amount'] ) ? true : false;
		$status = ( 'yes' === $settings['status'] ) ? true : false;

		return "[donation_history 
				id=\"{$id_history}\" 
				date=\"{$date}\" 
				payment_method=\"{$payment_method}\" 
				amount=\"{$amount}\" 
				status=\"{$status}\" 
				donor=\"{$donor}\"]";
	}

	public function add_filter_for_editor_content() {
		return false;
	}
}
