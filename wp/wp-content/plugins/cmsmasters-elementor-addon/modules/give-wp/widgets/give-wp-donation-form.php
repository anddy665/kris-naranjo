<?php
namespace CmsmastersElementor\Modules\GiveWp\Widgets;

use CmsmastersElementor\Base\Base_Widget;

use Elementor\Controls_Manager;

use Give\Campaigns\ValueObjects\CampaignPageMetaKeys;
use Give\ThirdPartySupport\Elementor\Actions\RegisterWidgetEditorScripts;
use Give\ThirdPartySupport\Elementor\Traits\HasFormOptions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Give_WP_Donation_Form extends Base_Widget {

	use HasFormOptions;

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.19.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return 'cmsmasters-give-wp-donation-form';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.19.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'GiveWP Donation Form', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.19.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-form';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @since 1.19.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'giv',
			'givewp',
			'donation',
			'form',
		);
	}

	/**
	 * @since 1.19.0
	 */
	public function get_custom_help_url() {
		return 'https://givewp.com/documentation/';
	}

	/**
	 * @since 1.19.0
	 */
	protected function get_upsale_data() {
		return array();
	}

	/**
	 * @since 1.19.0
	 */
	public function get_script_depends() {
		return array( RegisterWidgetEditorScripts::DONATION_FORM_WIDGET_SCRIPT_NAME );
	}

	/**
	 * @since 1.19.0
	 */
	public function get_style_depends() {
		return array(
			RegisterWidgetEditorScripts::DONATION_FORM_WIDGET_SCRIPT_NAME,
			'widget-cmsmasters-give-wp',
		);
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.19.0
	 */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/**
	 * LazyLoad widget use control.
	 *
	 * @since 1.19.0
	 *
	 * @return bool true - with control, false - without control.
	 */
	public function lazyload_widget_use_control() {
		return true;
	}

	/**
	 * @since 1.19.0
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.19.0
	 */
	protected function register_controls() {
		$form_options_group = $this->getFormOptionsWithCampaigns();

		$this->start_controls_section(
			'donation_form_section',
			array( 'label' => esc_html__( 'Donation Form', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'form_id',
			array(
				'label' => esc_html__( 'Form', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(),
				'default' => $this->get_default_form_option( $form_options_group ),
				'groups' => $form_options_group,
			)
		);

		$this->add_control(
			'display_style',
			array(
				'label' => esc_html__( 'Display Style', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'onpage' => esc_html__( 'On Page', 'cmsmasters-elementor' ),
					'modal' => esc_html__( 'Modal', 'cmsmasters-elementor' ),
					'newTab' => esc_html__( 'New Tab', 'cmsmasters-elementor' ),
				),
				'default' => 'onpage',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'donate_button_text',
			array(
				'label' => esc_html__( 'Donate Button Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Continue to Donate', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array( 'display_style!' => 'onpage' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get default form option.
	 *
	 * Retrieve the default form option.
	 *
	 * @since 1.19.0
	 */
	public function get_default_form_option( array $form_options_group ) {
		$default = ( ! empty( $form_options_group ) ? array_key_first( $form_options_group[0]['options'] ) : '' );

		$campaign_id = get_post_meta( get_the_ID(), CampaignPageMetaKeys::CAMPAIGN_ID, true );

		if ( ! $campaign_id ) {
			return $default;
		}

		foreach ( $form_options_group as $group ) {
			if ( ! empty( $group['campaign_id'] ) && (string) $group['campaign_id'] === (string) $campaign_id ) {
				return ! empty( $group['options'] ) ? array_key_first( $group['options'] ) : $default;
			}
		}

		return $default;
	}

	/**
	 * Render give donation form widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.19.0
	 */
	public function render() {
		$settings = $this->get_settings_for_display();

		$display_style = ( $settings['display_style'] ? $settings['display_style'] : '' );
		$donate_button_text = ( $settings['donate_button_text'] ? $settings['donate_button_text'] : '' );
		$form_id = ( $settings['form_id'] ? $settings['form_id'] : '' );

		if ( empty( $form_id ) ) {
			return;
		}

		echo do_shortcode(
			sprintf(
				'[give_form display_style="%s" continue_button_title="%s" id="%s"]',
				esc_attr( $display_style ),
				esc_attr( $donate_button_text ),
				esc_attr( $form_id )
			)
		);
	}
}
