<?php
namespace CmsmastersElementor\Modules\Mailchimp\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Mailchimp\Module;
use CmsmastersElementor\Utils as Breakpoints_Manager;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Mailchimp extends Base_Widget {

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Mailchimp', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-mailchimp';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'mailchimp',
			'email marketing',
			'contact form',
			'newsletter subscription',
			'subscription form',
		);
	}

	/**
	 * Specifying caching of the widget by default.
	 *
	 * @since 1.14.0
	 */
	protected function is_dynamic_content(): bool {
		return false;
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.0.0
	 * @since 1.16.0 Fixed style dependencies.
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		$style_depends = array(
			'widget-cmsmasters-mailchimp',
		);

		if ( Icons_Manager::is_migration_allowed() ) {
			$style_depends = array_merge( $style_depends, array(
				'elementor-icons-fa-solid',
				'elementor-icons-fa-brands',
				'elementor-icons-fa-regular',
			) );
		}

		return $style_depends;
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * Should be inherited and register new controls using `add_control()`,
	 * `add_responsive_control()` and `add_group_control()`, inside control
	 * wrappers like `start_controls_section()`, `start_controls_tabs()` and
	 * `start_controls_tab()`.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added group control 'BUTTON_BACKGROUND_GROUP', added gradient for button,
	 * added 'text-decoration' on hover for button, added border none for button and fields, fixed settings page url,
	 * added 'border-radius' on hover, fixed row gap for 'Simple Form'.
	 * @since 1.2.0 Added responsive control 'button_position, fixes for dependencies and conditions'.
	 * @since 1.2.3 Fixed error with responsive controls in elementor 3.4.0
	 * @since 1.3.3 Added support custom breakpoints.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 * @since 1.16.4 Fixed default email and preloader icon.
	 */

	protected function register_controls() {
		if ( empty( get_option( 'elementor_mailchimp_api_key' ) ) ) {
			$this->start_controls_section(
				'section_warning',
				array(
					'label' => __( 'Mailchimp', 'cmsmasters-elementor' ),
				)
			);

			$this->add_control(
				'warning',
				array(
					'raw' => __( 'Please go to the  ', 'cmsmasters-elementor' ) . '<a href="' . esc_url( admin_url( 'admin.php?page=cmsmasters-addon-settings' ) ) . '" target="_blank">' . __( 'settings page', 'cmsmasters-elementor' ) . '</a>' . __( ' and add your Mailchimp api key', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'render_type' => 'ui',
				)
			);

			$this->end_controls_section();

			return;
		}

		$this->start_controls_section(
			'section_api_settings',
			array(
				'label' => __( 'Mailchimp Settings', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'action_form',
			array(
				'label' => __( 'Form Action', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'subscribe' => __( 'Subscribe', 'cmsmasters-elementor' ),
					'unsubscribe' => __( 'Unsubscribe', 'cmsmasters-elementor' ),
				),
				'default' => 'subscribe',
				'toggle' => false,
				'frontend_available' => true,
			)
		);

		/**
		 * @var Module
		 */
		$module = Module::instance();
		$list = $module->mailchimp_lists();
		$devices = Breakpoints_Manager::get_devices();

		$this->add_control(
			'mailchimp_list',
			array(
				'label' => __( 'Mailchimp List', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'default' => array_keys( $list ),
				'options' => $list,
			)
		);

		$this->add_control(
			'inline_button',
			array(
				'label' => __( 'Simple Form', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'description' => __( 'If one line button is enabled, only email field is available', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'prefix_class' => 'cmsmasters-mailchimp__inline-button-',
				'render_type' => 'template',
			)
		);

		$this->add_responsive_control(
			'button_position',
			array(
				'label' => __( 'Button Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'right',
				'label_block' => false,
				'toggle' => false,
				'prefix_class' => 'cmsmasters-mailchimp__button-position%s-',
				'render_type' => 'template',
				'condition' => array(
					'inline_button!' => '',
				),
				'device_args' => array(
					$devices['tablet'] => array(
						'default' => 'right',
					),
					$devices['mobile'] => array(
						'default' => 'bottom',
					),
				),
			)
		);

		$this->add_control(
			'mp_name',
			array(
				'label' => __( 'Name', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'optional' => __( 'Optional', 'cmsmasters-elementor' ),
					'req' => __( 'Required', 'cmsmasters-elementor' ),
				),
				'default' => 'optional',
				'toggle' => false,
				'condition' => array(
					'action_form' => 'subscribe',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_control(
			'mp_full_name',
			array(
				'label' => __( 'Use a Last Name?', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'mp_name!' => 'none',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_control(
			'address',
			array(
				'label' => __( 'Address', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'optional' => __( 'Optional', 'cmsmasters-elementor' ),
					'req' => __( 'Required', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
				'description' => __( 'Notice: When Optional Address is selected address fields must be either ALL empty or ALL filled.', 'cmsmasters-elementor' ),
				'toggle' => false,
				'condition' => array(
					'action_form' => 'subscribe',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_control(
			'phone',
			array(
				'label' => __( 'Phone', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'optional' => __( 'Optional', 'cmsmasters-elementor' ),
					'req' => __( 'Required', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
				'toggle' => false,
				'condition' => array(
					'action_form' => 'subscribe',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_control(
			'bday',
			array(
				'label' => __( 'Birthday Date', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'optional' => __( 'Optional', 'cmsmasters-elementor' ),
					'req' => __( 'Required', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
				'toggle' => false,
				'condition' => array(
					'action_form' => 'subscribe',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_control(
			'terms_use',
			array(
				'label' => __( 'Terms of Use', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'frontend_available' => true,
				'condition' => array(
					'action_form' => 'subscribe',
				),
			)
		);

		$this->add_control(
			'confirmation_message',
			array(
				'label' => __( 'Confirmation Message ', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'frontend_available' => true,
				'condition' => array(
					'action_form' => 'unsubscribe',
				),
			)
		);

		$this->add_control(
			'terms_use_url',
			array(
				'type' => Controls_Manager::URL,
				'default' => array(
					'url' => '#',
				),
				'show_label' => false,
				'show_external' => true,
				'placeholder' => __( 'http(s)://your-link.com', 'cmsmasters-elementor' ),
				'description' => __( 'Use complete (absolute) URL\'s, including http(s)://', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'terms_use' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'terms_use_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'right',
				'label_block' => false,
				'toggle' => false,
				'prefix_class' => 'cmsmasters-mailchimp__terms-use-position%s-',
				'condition' => array(
					'inline_button!' => 'yes',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'action_form',
									'operator' => '===',
									'value' => 'subscribe',
								),
								array(
									'name' => 'terms_use',
									'operator' => '===',
									'value' => 'yes',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'action_form',
									'operator' => '===',
									'value' => 'unsubscribe',
								),
								array(
									'name' => 'confirmation_message',
									'operator' => '===',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'label_placeholder',
			array(
				'label' => __( 'Labels/Placeholders Visibility', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'label' => array(
						'title' => __( 'Labels', 'cmsmasters-elementor' ),
						'description' => __( 'Show only labels', 'cmsmasters-elementor' ),
					),
					'Placeholders' => array(
						'title' => __( 'Placeholders', 'cmsmasters-elementor' ),
						'description' => __( 'Show only placeholders', 'cmsmasters-elementor' ),
					),
					'both' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => __( 'Show both, labels & placeholders', 'cmsmasters-elementor' ),
					),
				),
				'separator' => 'before',
				'default' => 'label',
				'toggle' => false,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional',
			array(
				'label' => __( 'Additional Options', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'double_optin',
			array(
				'label' => __( 'Double Opt-in', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'description' => __( 'We strongly suggest keeping double opt-in enabled. Disabling double opt-in may affect your GDPR compliance.', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
				),
			)
		);

		$this->add_control(
			'update_existing',
			array(
				'label' => __( 'Update Existing Subscribers', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'description' => __( 'overwrites data when re-subscribing.', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
				),
			)
		);

		$this->add_control(
			'hide_form',
			array(
				'label' => __( 'Hide Form After Subscription', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'description' => __( 'hides form after successful subscription.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'redirect',
			array(
				'label' => __( 'Redirect URL After Subscription', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'description' => __( 'Your "subscribed" message will not show when redirecting to another page, so make sure to let your visitors know they were successfully subscribed.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'redirect_url',
			array(
				'type' => Controls_Manager::URL,
				'label' => __( 'Redirect URL', 'cmsmasters-elementor' ),
				'show_external' => false,
				'options' => false,
				'default' => array(
					'url' => '#',
				),
				'placeholder' => __( 'http(s)://your-link.com', 'cmsmasters-elementor' ),
				'description' => __( 'Use complete (absolute) URLs, including http(s)://', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array(
					'redirect' => 'yes',
				),
			)
		);

		$this->add_control(
			'tag',
			array(
				'label' => __( 'Subscriber tags', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => 'Your tags',
				'description' => __( 'The listed tags will be applied to all new subscribers added by this form. Separate multiple values with a comma. ","', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'action_form' => 'subscribe',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_lb_plc',
			array(
				'label' => __( 'Fields Text', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'mp_first_name_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'First Name', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'action_form' => 'subscribe',
					'mp_name!' => 'none',
				),
			)
		);

		$this->add_control(
			'mp_first_name_label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'First Name', 'cmsmasters-elementor' ),
				'condition' => array(
					'label_placeholder!' => 'Placeholders',
					'action_form' => 'subscribe',
					'mp_name!' => 'none',
				),
			)
		);

		$this->add_control(
			'mp_first_name_placeholder',
			array(
				'label' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'First Name', 'cmsmasters-elementor' ),
				'condition' => array(
					'mp_name!' => 'none',
					'action_form' => 'subscribe',
					'label_placeholder!' => 'label',
				),
			)
		);

		$this->add_control(
			'mp_last_name_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Last Name', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'action_form' => 'subscribe',
					'mp_name!' => 'none',
					'mp_full_name' => 'yes',
				),
			)
		);

		$this->add_control(
			'mp_last_name_label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Last Name', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'label_placeholder!' => 'Placeholders',
					'mp_name!' => 'none',
					'mp_full_name' => 'yes',
				),
			)
		);

		$this->add_control(
			'mp_last_name_placeholder',
			array(
				'label' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Last Name', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'mp_name!' => 'none',
					'mp_full_name' => 'yes',
					'label_placeholder!' => 'label',
				),
			)
		);

		$this->add_control(
			'email_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Email', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'email_label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Email', 'cmsmasters-elementor' ),
				'condition' => array(
					'label_placeholder!' => 'Placeholders',
				),
			)
		);

		$this->add_control(
			'email_placeholder',
			array(
				'label' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Email', 'cmsmasters-elementor' ),
				'condition' => array(
					'label_placeholder!' => 'label',
				),
			)
		);

		$this->add_control(
			'address_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Address', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'address!' => 'none',
					'action_form' => 'subscribe',
				),
			)
		);

		$this->add_control(
			'additionally_label',
			array(
				'label' => __( 'Street Address Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Street Address', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'label_placeholder!' => 'Placeholders',
					'address!' => 'none',
				),
			)
		);

		$this->add_control(
			'additionally_placeholder',
			array(
				'label' => __( 'Street Address Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Street Address', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'label_placeholder!' => 'label',
					'address!' => 'none',
				),
			)
		);

		$this->add_control(
			'city_label',
			array(
				'label' => __( 'City Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'City', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'label_placeholder!' => 'Placeholders',
					'address!' => 'none',
				),
			)
		);

		$this->add_control(
			'city_placeholder',
			array(
				'label' => __( 'City Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'City', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'label_placeholder!' => 'label',
					'address!' => 'none',
				),
			)
		);

		$this->add_control(
			'state_label',
			array(
				'label' => __( 'State/Prov/Region Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'State/Prov/Region', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'label_placeholder!' => 'Placeholders',
					'address!' => 'none',
				),
			)
		);

		$this->add_control(
			'state_placeholder',
			array(
				'label' => __( 'State/Prov/Region Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'State/Prov/Region', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'label_placeholder!' => 'label',
					'address!' => 'none',
				),
			)
		);

		$this->add_control(
			'zip_label',
			array(
				'label' => __( 'Zip/Postal Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Zip/Postal', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'label_placeholder!' => 'Placeholders',
					'address!' => 'none',
				),
			)
		);

		$this->add_control(
			'zip_placeholder',
			array(
				'label' => __( 'Zip/Postal Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Zip/Postal', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'label_placeholder!' => 'label',
					'address!' => 'none',
				),
			)
		);

		$this->add_control(
			'country_label',
			array(
				'label' => __( 'Country Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Country', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'label_placeholder!' => 'Placeholders',
					'address!' => 'none',
				),
			)
		);

		$this->add_control(
			'country_placeholder',
			array(
				'label' => __( 'Country Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Country', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'label_placeholder!' => 'label',
					'address!' => 'none',
				),
			)
		);

		$this->add_control(
			'phone_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Phone', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'action_form' => 'subscribe',
					'phone!' => 'none',
				),
			)
		);

		$this->add_control(
			'phone_label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Phone', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'label_placeholder!' => 'Placeholders',
					'phone!' => 'none',
				),

			)
		);

		$this->add_control(
			'phone_placeholder',
			array(
				'label' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Phone', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'phone!' => 'none',
					'label_placeholder!' => 'label',
				),
			)
		);

		$this->add_control(
			'bday_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Birthday Date', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'action_form' => 'subscribe',
					'bday!' => 'none',
				),
			)
		);

		$this->add_control(
			'bday_label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Birthday Date', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'label_placeholder!' => 'Placeholders',
					'bday!' => 'none',
				),

			)
		);

		$this->add_control(
			'bday_placeholder',
			array(
				'label' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'mm.dd', 'cmsmasters-elementor' ),
				'condition' => array(
					'action_form' => 'subscribe',
					'bday!' => 'none',
					'label_placeholder!' => 'label',
				),
			)
		);

		$this->add_control(
			'terms_use_text',
			array(
				'label' => __( 'Terms of Use', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Your personal text', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'terms_use' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_settings_button',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'button_content',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Content', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'button_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => __( 'Show only text', 'cmsmasters-elementor' ),
					),
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'description' => __( 'Show only icon', 'cmsmasters-elementor' ),
					),
					'both' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => __( 'Show both, text & icon', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'text',
				'toggle' => false,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Subscribed/Unsubscribed', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array(
					'button_type!' => 'icon',
				),
			)
		);

		$this->add_control(
			'button_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'recommended' => array(
					'fa-regular' => array(
						'envelope',
						'envelope-open',
					),
				),
				'default' => array(
					'value' => 'far fa-envelope',
					'library' => 'fa-regular',
				),
				'condition' => array(
					'button_type!' => 'text',
				),
			)
		);

		$this->add_control(
			'spinner_heading_settings',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Loader', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'icon_loader',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-spinner',
					'library' => 'fa-solid',
				),
				'recommended' => array(
					'fa-solid' => array(
						'sync-alt',
						'spinner',
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_messages',
			array(
				'label' => __( 'Messages', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'success_text',
			array(
				'label' => __( 'Success', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Thank you for subscribing to the newsletter', 'cmsmasters-elementor' ),
				'description' => __( 'The text that shows when an email address is successfully subscribed to the selected list(s).', 'cmsmasters-elementor' ),
				'label_block' => true,
				'condition' => array(
					'action_form' => 'subscribe',
					'double_optin!' => 'yes',
				),
			)
		);

		$this->add_control(
			'success_optin_text',
			array(
				'label' => __( 'Success', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Please go to your email address and confirm', 'cmsmasters-elementor' ),
				'description' => __( 'The text that shows when an email address is successfully subscribed to the selected list(s).', 'cmsmasters-elementor' ),
				'label_block' => true,
				'condition' => array(
					'action_form' => 'subscribe',
					'double_optin' => 'yes',
				),
			)
		);

		$this->add_control(
			'already_subscribed',
			array(
				'label' => __( 'Already Subscribed', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Given email address is already subscribed, thank you!', 'cmsmasters-elementor' ),
				'description' => __( 'The text that shows when the given email is already subscribed to the selected list(s).', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'label_block' => true,
				'condition' => array(
					'action_form' => 'subscribe',
					'update_existing!' => 'yes',
				),
			)
		);

		$this->add_control(
			'update',
			array(
				'label' => __( 'User Update', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Thank you, your records have been updated!', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'description' => __( 'The text that shows when an existing subscriber is updated.', 'cmsmasters-elementor' ),
				'label_block' => true,
				'condition' => array(
					'action_form' => 'subscribe',
					'update_existing' => 'yes',
				),
			)
		);

		$this->add_control(
			'unsubscribed',
			array(
				'label' => __( 'Unsubscribed', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'You were successfully unsubscribed', 'cmsmasters-elementor' ),
				'description' => __( 'When using the unsubscribe method, this is the text that shows when the given email address is successfully unsubscribed from the selected list(s).', 'cmsmasters-elementor' ),
				'label_block' => true,
				'condition' => array(
					'action_form!' => 'subscribe',
				),
			)
		);

		$this->add_control(
			'not_subscribed',
			array(
				'label' => __( 'Not Unsubscribed', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Given email address is not subscribed.', 'cmsmasters-elementor' ),
				'description' => __( 'When using the unsubscribe method, this is the text that shows when the given email address is not on the selected list(s).', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'label_block' => true,
				'condition' => array(
					'action_form!' => 'subscribe',
				),
			)
		);

		$this->add_control(
			'general_error_text',
			array(
				'label' => __( 'General Error', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Oops. Something went wrong. Please try again later', 'cmsmasters-elementor' ),
				'description' => __( 'The text that shows when a general error occured.', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout_style',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label' => __( 'Rows Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => '20',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__wrapper' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.elementor-widget-cmsmasters-mailchimp.cmsmasters-mailchimp__inline-button-yes .elementor-widget-cmsmasters-mailchimp__field-gap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label' => __( 'Columns Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => '20',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__fields-wrapper .elementor-widget-cmsmasters-mailchimp__field-group' => 'padding: 0 calc({{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__fields-wrapper' => 'margin: 0 calc(-{{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__button-inline-wrapper' => 'margin: 0 calc(-{{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}}.cmsmasters-mailchimp__inline-button-yes .elementor-widget-cmsmasters-mailchimp__fields-wrapper .elementor-widget-cmsmasters-mailchimp__field-outer' => 'padding: 0 calc({{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}}.cmsmasters-mailchimp__inline-button-yes .elementor-widget-cmsmasters-mailchimp__wrapper'  => '--gap-column: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_fname',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Name Fields Width', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'mp_name!' => 'none',
					'action_form' => 'subscribe',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'columns_fname',
			array(
				'label' => __( 'First Name', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'20' => '20%',
					'25' => '25%',
					'30' => '30%',
					'33' => '33%',
					'40' => '40%',
					'50' => '50%',
					'60' => '60%',
					'67' => '67%',
					'70' => '70%',
					'75' => '75%',
					'80' => '80%',
					'100' => '100%',
				),
				'default' => '50',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group-fname' => 'width: {{SIZE}}%;',
				),
				'condition' => array(
					'mp_name!' => 'none',
					'action_form' => 'subscribe',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'columns_lname',
			array(
				'label' => __( 'Last Name', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'20' => '20%',
					'25' => '25%',
					'30' => '30%',
					'33' => '33%',
					'40' => '40%',
					'50' => '50%',
					'60' => '60%',
					'67' => '67%',
					'70' => '70%',
					'75' => '75%',
					'80' => '80%',
					'100' => '100%',
				),
				'default' => '50',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group-lname' => 'width: {{SIZE}}%;',
				),
				'condition' => array(
					'mp_name!' => 'none',
					'mp_full_name' => 'yes',
					'action_form' => 'subscribe',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_email',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Email Field Width', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'columns_email',
			array(
				'label' => __( 'Email', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'20' => '20%',
					'25' => '25%',
					'30' => '30%',
					'33' => '33%',
					'40' => '40%',
					'50' => '50%',
					'60' => '60%',
					'67' => '67%',
					'70' => '70%',
					'75' => '75%',
					'80' => '80%',
					'100' => '100%',
				),
				'default' => '100',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group-email' => 'width: {{SIZE}}%;',
				),
				'condition' => array(
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_address',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Address Fields Width', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'action_form' => 'subscribe',
					'address!' => 'none',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'columns_additionally',
			array(
				'label' => __( 'Street Address', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'20' => '20%',
					'25' => '25%',
					'30' => '30%',
					'33' => '33%',
					'40' => '40%',
					'50' => '50%',
					'60' => '60%',
					'67' => '67%',
					'70' => '70%',
					'75' => '75%',
					'80' => '80%',
					'100' => '100%',
				),
				'default' => '100',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group-additionally' => 'width: {{SIZE}}%;',
				),
				'condition' => array(
					'action_form' => 'subscribe',
					'address!' => 'none',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'columns_city',
			array(
				'label' => __( 'City', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'20' => '20%',
					'25' => '25%',
					'30' => '30%',
					'33' => '33%',
					'40' => '40%',
					'50' => '50%',
					'60' => '60%',
					'67' => '67%',
					'70' => '70%',
					'75' => '75%',
					'80' => '80%',
					'100' => '100%',
				),
				'default' => '100',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group-city' => 'width: {{SIZE}}%;',
				),
				'condition' => array(
					'action_form' => 'subscribe',
					'address!' => 'none',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'columns_state',
			array(
				'label' => __( 'State/Prov/Region', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'20' => '20%',
					'25' => '25%',
					'30' => '30%',
					'33' => '33%',
					'40' => '40%',
					'50' => '50%',
					'60' => '60%',
					'67' => '67%',
					'70' => '70%',
					'75' => '75%',
					'80' => '80%',
					'100' => '100%',
				),
				'default' => '100',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group-state' => 'width: {{SIZE}}%;',
				),
				'condition' => array(
					'action_form' => 'subscribe',
					'address!' => 'none',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'columns_zip',
			array(
				'label' => __( 'Zip/Postal', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'20' => '20%',
					'25' => '25%',
					'30' => '30%',
					'33' => '33%',
					'40' => '40%',
					'50' => '50%',
					'60' => '60%',
					'67' => '67%',
					'70' => '70%',
					'75' => '75%',
					'80' => '80%',
					'100' => '100%',
				),
				'default' => '100',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group-zip' => 'width: {{SIZE}}%;',
				),
				'condition' => array(
					'action_form' => 'subscribe',
					'address!' => 'none',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'columns_country',
			array(
				'label' => __( 'Country', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'20' => '20%',
					'25' => '25%',
					'30' => '30%',
					'33' => '33%',
					'40' => '40%',
					'50' => '50%',
					'60' => '60%',
					'67' => '67%',
					'70' => '70%',
					'75' => '75%',
					'80' => '80%',
					'100' => '100%',
				),
				'default' => '100',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group-country' => 'width: {{SIZE}}%;',
				),
				'condition' => array(
					'action_form' => 'subscribe',
					'address!' => 'none',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_phone',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Phone Field Width', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'action_form' => 'subscribe',
					'phone!' => 'none',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'columns_phone',
			array(
				'label' => __( 'Phone', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'20' => '20%',
					'25' => '25%',
					'30' => '30%',
					'33' => '33%',
					'40' => '40%',
					'50' => '50%',
					'60' => '60%',
					'67' => '67%',
					'70' => '70%',
					'75' => '75%',
					'80' => '80%',
					'100' => '100%',
				),
				'default' => '100',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group-phone' => 'width: {{SIZE}}%;',
				),
				'condition' => array(
					'action_form' => 'subscribe',
					'phone!' => 'none',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_bday',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Birthday Date Field Width', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'bday!' => 'none',
					'action_form' => 'subscribe',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'columns_bday',
			array(
				'label' => __( 'Birthday Date', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'20' => '20%',
					'25' => '25%',
					'30' => '30%',
					'33' => '33%',
					'40' => '40%',
					'50' => '50%',
					'60' => '60%',
					'67' => '67%',
					'70' => '70%',
					'75' => '75%',
					'80' => '80%',
					'100' => '100%',
				),
				'default' => '100',
				'selectors' => array(
					'action_form' => 'subscribe',
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group-bday' => 'width: {{SIZE}}%;',
				),
				'condition' => array(
					'bday!' => 'none',
					'action_form' => 'subscribe',
					'inline_button!' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_field_style',
			array(
				'label' => __( 'Fields', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'field_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group .elementor-widget-cmsmasters-mailchimp__field',
			)
		);

		$this->start_controls_tabs( 'field_tabs' );

		$colors = array(
			'normal' => __( 'Default', 'cmsmasters-elementor' ),
			'focus' => __( 'Focus', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'focus' === $key ) ? ':focus' : '';
			$selector = "{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group .elementor-widget-cmsmasters-mailchimp__field{$state}";

			$this->start_controls_tab(
				"field_form_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"field_text_color_{$key}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"field_placeholder_{$key}",
				array(
					'label' => __( 'Placeholder Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group .elementor-widget-cmsmasters-mailchimp__field{$state}::-webkit-input-placeholder" => 'color: {{VALUE}};',
						"{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group .elementor-widget-cmsmasters-mailchimp__field{$state}::-ms-input-placeholder" => 'color: {{VALUE}};',
						"{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group .elementor-widget-cmsmasters-mailchimp__field{$state}::placeholder" => 'color: {{VALUE}};',
					),
					'condition' => array(
						'label_placeholder!' => 'label',
					),
				)
			);

			$this->add_control(
				"field_background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"field_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'border_field_border!' => array(
							'none',
						),
					),
				)
			);

			if ( 'normal' === $key ) {
				$this->add_responsive_control(
					'field_border_radius',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group .elementor-widget-cmsmasters-mailchimp__field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			} else {
				$this->add_responsive_control(
					'field_border_radius_focus',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group .elementor-widget-cmsmasters-mailchimp__field:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			}

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "box_shadow_{$key}",
					'selector' => $selector,
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'fields_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group .elementor-widget-cmsmasters-mailchimp__field' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_field',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group .elementor-widget-cmsmasters-mailchimp__field',
				'separator' => 'before',
				'exclude' => array( 'color' ),
			)
		);

		$this->update_control(
			'border_field_border',
			array(
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
					'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'field_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__field-group .elementor-widget-cmsmasters-mailchimp__field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_labels',
			array(
				'label' => __( 'Labels', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'label_placeholder!' => 'Placeholders',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__wrapper label:not([for="subscribed-radio"]):not([for="unsubscribed-radio"])',
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__wrapper label:not([for="subscribed-radio"]):not([for="unsubscribed-radio"])' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'label_gap',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => '5',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__wrapper label:not([for="subscribed-radio"]):not([for="unsubscribed-radio"])' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_terms_use',
			array(
				'label' => __( 'Terms of Use/Confirmation Message', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'action_form',
									'operator' => '===',
									'value' => 'subscribe',
								),
								array(
									'name' => 'terms_use',
									'operator' => '===',
									'value' => 'yes',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'action_form',
									'operator' => '===',
									'value' => 'unsubscribe',
								),
								array(
									'name' => 'confirmation_message',
									'operator' => '===',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'terms_use_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__wrapper .elementor-widget-cmsmasters-mailchimp__terms-link',
			)
		);

		$this->add_control(
			'terms_use_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__wrapper .elementor-widget-cmsmasters-mailchimp__terms-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'terms_use_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__wrapper .elementor-widget-cmsmasters-mailchimp__terms-link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$terms_use_align_arg = array(
			'conditions' => array(
				'relation' => 'or',
				'terms' => array(
					array(
						'name' => 'terms_use_position_{{cmsmasters_device}}',
						'operator' => '===',
						'value' => 'top',
					),
					array(
						'name' => 'inline_button',
						'operator' => '===',
						'value' => 'yes',
					),
				),
			),
		);

		$this->add_responsive_control(
			'terms_use_align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'toggle' => false,
				'prefix_class' => 'cmsmasters-mailchimp__terms-use-align%s-',
				'default' => 'start',
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'terms_use_position',
							'operator' => '===',
							'value' => 'top',
						),
						array(
							'name' => 'inline_button',
							'operator' => '===',
							'value' => 'yes',
						),
					),
				),
				'device_args' => Breakpoints_Manager::get_devices_args( $terms_use_align_arg ),
			)
		);

		$margin = is_rtl() ? 'margin-right' : 'margin-left';

		$this->add_control(
			'terms_use_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => '5',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__wrapper .elementor-widget-cmsmasters-mailchimp__terms-link' => '' . $margin . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__button .elementor-widget-cmsmasters-mailchimp__button-text',
			)
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'disabled' => __( 'Disabled', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'hover' === $key ) ? ':hover' : '';
			$bgcolor = ( 'disabled' === $key ) ? '#c0c0c0' : '';
			$content_color = ( 'disabled' === $key ) ? '#ffffff' : '';
			$disabled = ( 'disabled' === $key ) ? '[disabled]' : '';
			$text_decoration = ( 'disabled' === $key ) ? 'none' : '';
			$selector = "{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__button{$state}{$disabled}";

			$this->start_controls_tab(
				"button_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$element = ( 'hover' === $key ) ? ':after' : ':before';

			$this->add_control(
				"button_text_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => $content_color,
					'separator' => 'before',
					'selectors' => array(
						$selector . ' .elementor-widget-cmsmasters-mailchimp__button-text' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"button_bg_{$key}_background",
				array(
					'label' => __( 'Background Type', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'color' => array(
							'title' => __( 'Color', 'cmsmasters-elementor' ),
							'icon' => 'eicon-paint-brush',
						),
						'gradient' => array(
							'title' => __( 'Gradient', 'cmsmasters-elementor' ),
							'icon' => 'eicon-barcode',
						),
					),
					'default' => 'color',
					'toggle' => false,
					'render_type' => 'ui',
				)
			);

			$this->add_control(
				"button_background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => $bgcolor,
					'selectors' => array(
						"{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__button{$disabled}{$element}" => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						"button_bg_{$key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"button_bg_{$key}_color_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 0,
					),
					'render_type' => 'ui',
					'condition' => array(
						"button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_{$key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						"button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_{$key}_color_b_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'render_type' => 'ui',
					'condition' => array(
						"button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_{$key}_gradient_type",
				array(
					'label' => __( 'Type', 'cmsmasters-elementor' ),
					'label_block' => false,
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'linear' => __( 'Linear', 'cmsmasters-elementor' ),
						'radial' => __( 'Radial', 'cmsmasters-elementor' ),
					),
					'default' => 'linear',
					'render_type' => 'ui',
					'condition' => array(
						"button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_{$key}_gradient_angle",
				array(
					'label' => __( 'Angle', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'deg' ),
					'default' => array(
						'unit' => 'deg',
						'size' => 180,
					),
					'range' => array(
						'deg' => array( 'step' => 10 ),
					),
					'selectors' => array(
						"{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__button{$disabled}{$element}" => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{button_bg_{$key}_color_stop.SIZE}}{{button_bg_{$key}_color_stop.UNIT}}, {{button_bg_{$key}_color_b.VALUE}} {{button_bg_{$key}_color_b_stop.SIZE}}{{button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"button_bg_{$key}_background" => array( 'gradient' ),
						"button_bg_{$key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_{$key}_gradient_position",
				array(
					'label' => __( 'Position', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => array(
						'center center' => __( 'Center Center', 'cmsmasters-elementor' ),
						'center left' => __( 'Center Left', 'cmsmasters-elementor' ),
						'center right' => __( 'Center Right', 'cmsmasters-elementor' ),
						'top center' => __( 'Top Center', 'cmsmasters-elementor' ),
						'top left' => __( 'Top Left', 'cmsmasters-elementor' ),
						'top right' => __( 'Top Right', 'cmsmasters-elementor' ),
						'bottom center' => __( 'Bottom Center', 'cmsmasters-elementor' ),
						'bottom left' => __( 'Bottom Left', 'cmsmasters-elementor' ),
						'bottom right' => __( 'Bottom Right', 'cmsmasters-elementor' ),
					),
					'default' => 'center center',
					'selectors' => array(
						"{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__button{$disabled}{$element}" => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{button_bg_{$key}_color_stop.SIZE}}{{button_bg_{$key}_color_stop.UNIT}}, {{button_bg_{$key}_color_b.VALUE}} {{button_bg_{$key}_color_b_stop.SIZE}}{{button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"button_bg_{$key}_background" => array( 'gradient' ),
						"button_bg_{$key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_icon_color_{$key}",
				array(
					'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => $content_color,
					'selectors' => array(
						$selector . ' .elementor-widget-cmsmasters-mailchimp__button-icon' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'button_type!' => 'text',
					),
				)
			);

			$this->add_control(
				"button_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => $bgcolor,
					'selectors' => array(
						$selector => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'button_border_border!' => array(
							'',
						),
					),
				)
			);

			if ( 'normal' === $key ) {
				$this->add_responsive_control(
					'button_border_radius',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			} else {
				$this->add_responsive_control(
					"button_border_radius_{$key}",
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_control(
					"button_text_decoration_{$key}",
					array(
						'label' => __( 'Text Decoration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SELECT,
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => _x( 'None', 'Typography Control', 'cmsmasters-elementor' ),
							'underline' => _x( 'Underline', 'Typography Control', 'cmsmasters-elementor' ),
							'overline' => _x( 'Overline', 'Typography Control', 'cmsmasters-elementor' ),
							'line-through' => _x( 'Line Through', 'Typography Control', 'cmsmasters-elementor' ),
						),
						'default' => $text_decoration,
						'selectors' => array(
							$selector . ' .elementor-widget-cmsmasters-mailchimp__button-text' => 'text-decoration: {{VALUE}};',
						),
					)
				);
			}

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "button_shadow_text_{$key}",
					'selector' => $selector . ' .elementor-widget-cmsmasters-mailchimp__button-text, ' .
					$selector . ' .elementor-widget-cmsmasters-mailchimp__button-icon',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "button_box_shadow_{$key}",
					'selector' => $selector,
				)
			);

			$this->end_controls_tab();

		}

		$this->end_controls_tabs();

		$align_arg = array(
			'conditions' => array(
				'relation' => 'or',
				'terms' => array(
					array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'inline_button',
								'operator' => '===',
								'value' => '',
							),
							array(
								'name' => 'terms_use',
								'operator' => '===',
								'value' => '',
							),
						),
					),
					array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'inline_button',
								'operator' => '===',
								'value' => '',
							),
							array(
								'name' => 'terms_use',
								'operator' => '===',
								'value' => 'yes',
							),
							array(
								'name' => 'terms_use_position_{{cmsmasters_device}}',
								'operator' => '===',
								'value' => 'top',
							),
						),
					),
					array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'inline_button',
								'operator' => '!==',
								'value' => '',
							),
							array(
								'name' => 'button_position_{{cmsmasters_device}}',
								'operator' => '===',
								'value' => 'bottom',
							),
						),
					),
					array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'action_form',
								'operator' => '===',
								'value' => 'unsubscribe',
							),
							array(
								'name' => 'confirmation_message',
								'operator' => '!==',
								'value' => 'yes',
							),
						),
					),
				),
			),
		);

		$this->add_responsive_control(
			'align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
					'stretch' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'toggle' => false,
				'prefix_class' => 'cmsmasters-mailchimp__button-align%s-',
				'default' => 'start',
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'inline_button',
									'operator' => '===',
									'value' => '',
								),
								array(
									'name' => 'terms_use',
									'operator' => '===',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'inline_button',
									'operator' => '===',
									'value' => '',
								),
								array(
									'name' => 'terms_use',
									'operator' => '===',
									'value' => 'yes',
								),
								array(
									'name' => 'terms_use_position',
									'operator' => '===',
									'value' => 'top',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'inline_button',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'name' => 'button_position',
									'operator' => '===',
									'value' => 'bottom',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'action_form',
									'operator' => '===',
									'value' => 'unsubscribe',
								),
								array(
									'name' => 'confirmation_message',
									'operator' => '!==',
									'value' => 'yes',
								),
							),
						),
					),
				),
				'device_args' => Breakpoints_Manager::get_devices_args( $align_arg ),
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__button .elementor-widget-cmsmasters-mailchimp__button-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__button .elementor-widget-cmsmasters-mailchimp__button-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'separator' => 'before',
				'condition' => array(
					'button_type!' => 'text',
				),
			)
		);

		$this->add_control(
			'icon_gap',
			array(
				'label' => __( 'Icon Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__button .elementor-widget-cmsmasters-mailchimp__button-icon' => 'padding-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'button_type' => 'both',
				),
			)
		);

		$this->add_responsive_control(
			'proportions',
			array(
				'label' => __( 'Button Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => '%',
					'size' => '40',
				),
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 400,
					),
					'%' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__button-inline-wrapper' => '--button-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'inline_button' => 'yes',
					'button_position!' => 'bottom',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), array(
				'name' => 'button_border',
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => __( 'None', 'cmsmasters-elementor' ),
							'default' => __( 'Default', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'prefix_class' => 'cmsmasters-mailchimp__button-border-',
					),
					'width' => array(
						'condition' => array(
							'border!' => array(
								'',
								'default',
							),
						),
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__button',
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'button_text_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'_settings',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Loader', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'spinner_size',
			array(
				'label' => __( 'Loader Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => '22',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__button-preloader i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__button-preloader svg' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_loader_color',
			array(
				'label' => __( 'Loader Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__button-preloader' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_valid',
			array(
				'label' => __( 'Message', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'valid_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__valid-message, {{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__error-message, {{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__notice-message',
			)
		);

		$this->add_control(
			'heading_valid',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Success', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'valid_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__valid-message' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'valid_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__valid-message' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'valid_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__valid-message' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'heading_invalid',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Error', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'invalid_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__error-message' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'invalid_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__error-message' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'invalid_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__error-message' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'align_valid',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'toggle' => false,
				'default' => 'left',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__valid-message' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__error-message' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__notice-message' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), array(
				'name' => 'valid_border',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__valid-message, {{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__error-message, {{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__notice-message',
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'valid_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__valid-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__error-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__notice-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'valid_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__valid-message' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__error-message' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-mailchimp__notice-message' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render form item on the frontend.
	 *
	 * @since 1.0.0
	 */
	protected function render_form_item( $settings ) {
		$form_fields = array(
			'mp_name',
			'email',
			'address',
			'phone',
			'bday',
		);

		foreach ( $form_fields as $form_field ) {
			if ( method_exists( $this, $form_field ) ) {
				$action_form = ( isset( $settings['action_form'] ) ? $settings['action_form'] : '' );
				$field_name = ( isset( $settings[ $form_field ] ) ? $settings[ $form_field ] : '' );
				$inline_button = ( isset( $settings['inline_button'] ) ? $settings['inline_button'] : '' );

				if ( 'subscribe' === $action_form && 'none' !== $field_name && 'yes' !== $inline_button && 'email' !== $form_field ) {
					$this->$form_field();
				}

				if ( 'email' === $form_field ) {
					$this->$form_field();
				}
			}
		}
	}

	/**
	 * Render mailchimp widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$api_key = get_option( 'elementor_mailchimp_api_key' );

		if ( ! empty( $api_key ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'elementor-widget-cmsmasters-mailchimp__wrapper' );

			$this->add_render_attribute( 'form', array(
				'class' => 'elementor-widget-cmsmasters-mailchimp__form',
				'method' => 'POST',
			) );

			$this->add_render_attribute( 'field_group', 'class', 'elementor-widget-cmsmasters-mailchimp__field-group' );
			$this->add_render_attribute( 'terms_group', 'class',
				array(
					'elementor-widget-cmsmasters-mailchimp__field-group',
					'elementor-widget-cmsmasters-mailchimp__terms-group',
				)
			);

			echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'<form ' . $this->get_render_attribute_string( 'form' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '<div class="elementor-widget-cmsmasters-mailchimp__fields-wrapper">';

						$this->render_form_item( $settings );

					echo '</div>';

			if ( 'yes' !== $settings['inline_button'] ) {
				echo '<div class="elementor-widget-cmsmasters-mailchimp__terms-use-position">';

					$this->terms_of_use();

					$this->submit();

				echo '</div>';
			}

					echo '<input type="hidden" name="action-form" value="' . esc_attr( $settings['action_form'] ) . '">';

				echo '</form>';

				$this->valid_message();

				$this->error_message();

			echo '</div>';
		}
	}

	/**
	 * Get a countries.
	 *
	 * Retrieve a countries for address.
	 *
	 * @since 1.0.0
	 *
	 */
	public static function get_countries() {
		$countries = array(
			'AF' => 'Afghanistan',
			'AX' => 'Aland Islands',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
			'AG' => 'Antigua and Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia',
			'BQ' => 'Bonaire, Saint Eustatius and Saba',
			'BA' => 'Bosnia and Herzegovina',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island',
			'BR' => 'Brazil',
			'IO' => 'British Indian Ocean Territory',
			'VG' => 'British Virgin Islands',
			'BN' => 'Brunei',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'BI' => 'Burundi',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CA' => 'Canada',
			'CV' => 'Cape Verde',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island',
			'CC' => 'Cocos Islands',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CW' => 'Curacao',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'CD' => 'Democratic Republic of the Congo',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'TL' => 'East Timor',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands',
			'FO' => 'Faroe Islands',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'TF' => 'French Southern Territories',
			'GA' => 'Gabon',
			'GM' => 'Gambia',
			'GE' => 'Georgia',
			'DE' => 'Germany',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'GN' => 'Guinea',
			'GW' => 'Guinea-Bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HM' => 'Heard Island and McDonald Islands',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'CI' => 'Ivory Coast',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'XK' => 'Kosovo',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Laos',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MK' => 'Macedonia',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MH' => 'Marshall Islands',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte',
			'MX' => 'Mexico',
			'FM' => 'Micronesia',
			'MD' => 'Moldova',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'MM' => 'Myanmar',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'NU' => 'Niue',
			'NF' => 'Norfolk Island',
			'KP' => 'North Korea',
			'MP' => 'Northern Mariana Islands',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PW' => 'Palau',
			'PS' => 'Palestinian Territory',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'CG' => 'Republic of the Congo',
			'RE' => 'Reunion',
			'RO' => 'Romania',
			'RU' => 'Russia',
			'RW' => 'Rwanda',
			'BL' => 'Saint Barthelemy',
			'SH' => 'Saint Helena',
			'KN' => 'Saint Kitts and Nevis',
			'LC' => 'Saint Lucia',
			'MF' => 'Saint Martin',
			'PM' => 'Saint Pierre and Miquelon',
			'VC' => 'Saint Vincent and the Grenadines',
			'WS' => 'Samoa',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome and Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SX' => 'Sint Maarten',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia and the South Sandwich Islands',
			'KR' => 'South Korea',
			'SS' => 'South Sudan',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SJ' => 'Svalbard and Jan Mayen',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syria',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TT' => 'Trinidad and Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks and Caicos Islands',
			'TV' => 'Tuvalu',
			'VI' => 'U.S. Virgin Islands',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'GB' => 'United Kingdom',
			'US' => 'United States',
			'UM' => 'United States Minor Outlying Islands',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VA' => 'Vatican',
			'VE' => 'Venezuela',
			'VN' => 'Vietnam',
			'WF' => 'Wallis and Futuna',
			'EH' => 'Western Sahara',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe',
		);

		foreach ( $countries as $index => $country ) {
			echo '<option value="' . esc_attr( $index ) . '">' .
				esc_html( $country ) .
			'</option>';
		}
	}

	/**
	 * Get before email button inline wrapper.
	 *
	 * Retrieve before email button inline wrapper.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function before_email_button_inline_wrapper( $inline_button, $field_label ) {
		$this->add_render_attribute(
			array(
				'button_inline' => array(
					'class' => 'elementor-widget-cmsmasters-mailchimp__button-inline-wrapper',
				),
				'email_outer' => array(
					'class' => 'elementor-widget-cmsmasters-mailchimp__field-outer',
				),
			)
		);

		if ( ! empty( $inline_button ) ) {
			$this->add_render_attribute( 'email_outer', 'class', 'elementor-widget-cmsmasters-mailchimp__field-gap' );
		}

		echo '<div ' . $this->get_render_attribute_string( 'button_inline' ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'<div ' . $this->get_render_attribute_string( 'email_outer' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'<label ' . $this->get_render_attribute_string( 'label_email' ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$field_label . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'</label>';
	}

	/**
	 * Get after email button inline wrapper.
	 *
	 * Retrieve after email button inline wrapper.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function after_email_button_inline_wrapper( $inline_button, $field_label ) {
			echo '</div>';

			$this->submit();

			echo '<div class="elementor-widget-cmsmasters-mailchimp__terms-use-position">';
				$this->terms_of_use();
			echo '</div>';

		echo '</div>';
	}

	/**
	 * Get form field.
	 *
	 * Retrieve a form field with settings.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function get_form_field( $groupe, $field, $defaul_label, $field_placeholder_text ) {
		$settings = $this->get_settings_for_display();

		$field_star = '';
		$field_required = '';
		$field_placeholder_star = '';
		$label_placeholder = ( isset( $settings['label_placeholder'] ) ? $settings['label_placeholder'] : '' );
		$field_uniqid = $this->field_id( str_replace( '_', '-', $field ) . '-' );
		$field_req = ( isset( $settings[ $groupe ] ) ? $settings[ $groupe ] : '' );

		if ( 'req' === $field_req || 'email' === $field ) {
			$field_star = '*';
			$field_required = 'required';
		}

		if ( 'label' !== $label_placeholder && ( 'email' === $field || 'req' === $field_req ) ) {
			$field_placeholder_star = '*';
		}

		$this->add_render_attribute(
			array(
				'field_group_' . $field => array(
					'class' => array(
						'elementor-widget-cmsmasters-mailchimp__field-group',
						'elementor-widget-cmsmasters-mailchimp__field-group-' . $field,
					),
				),
				'label_' . $field => array(
					'for' => $field_uniqid,
					'class' => 'elementor-widget-cmsmasters-mailchimp__field-label' . ( 'Placeholders' === $label_placeholder ? ' screen-reader-text' : '' ),
				),
				$field . '_form_field' => array(
					'id' => $field_uniqid,
					'class' => array(
						'elementor-widget-cmsmasters-mailchimp__field',
					),
					'name' => str_replace( '_', '-', $field ),
					'placeholder' => $field_placeholder_text . $field_placeholder_star,
				),
			)
		);

		if ( 'country' !== $field && 'email' !== $field ) {
			$this->add_render_attribute( $field . '_form_field', 'type', 'text' );
		}

		if ( 'email' === $field ) {
			$this->add_render_attribute( $field . '_form_field', array(
				'type' => 'email',
				'required' => 'required',
			) );
		}

		echo '<div ' . $this->get_render_attribute_string( 'field_group_' . $field ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			$field_label_control = ( isset( $settings[ $field . '_label' ] ) ? $settings[ $field . '_label' ] : '' );
			$field_label = ( empty( $field_label_control ) ? esc_html( $defaul_label ) : esc_html( $field_label_control ) );

			echo '<label ' . $this->get_render_attribute_string( 'label_' . $field ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$field_label . $field_star . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'</label>';

			$inline_button = ( isset( $settings['inline_button'] ) ? $settings['inline_button'] : '' );

			if ( 'email' === $field && ! empty( $inline_button ) ) {
				$this->before_email_button_inline_wrapper( $inline_button, $field_label );
			}

			if ( 'country' === $field ) {
				echo '<select size="1" ' . $this->get_render_attribute_string( $field . '_select' ) . $field_required . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

					$this->get_countries();

				echo '</select>';
			} else {
				echo '<input size="1" ' . $this->get_render_attribute_string( $field . '_form_field' ) . $field_required . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			if ( 'email' === $field && ! empty( $inline_button ) ) {
				$this->after_email_button_inline_wrapper( $inline_button, $field_label );
			}

		echo '</div>';
	}

	/**
	 * Get get a field with a name.
	 *
	 * Retrieve a field with settings for the name.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function mp_name() {
		$settings = $this->get_settings_for_display();

		$label_placeholder = ( isset( $settings['label_placeholder'] ) ? $settings['label_placeholder'] : '' );
		$mp_name_placeholder_text = '';

		if ( 'label' !== $label_placeholder ) {
			$mp_name_placeholder = ( isset( $settings['mp_first_name_placeholder'] ) ? $settings['mp_first_name_placeholder'] : '' );
			$mp_name_placeholder_text = ( ! empty( $mp_name_placeholder ) ? esc_attr( $mp_name_placeholder ) : esc_html__( 'First Name', 'cmsmasters-elementor' ) );
		}

		$this->get_form_field( 'mp_name', 'mp_first_name', 'First Name', $mp_name_placeholder_text );

		$mp_full_name = ( isset( $settings['mp_full_name'] ) ? $settings['mp_full_name'] : '' );

		if ( 'yes' === $mp_full_name ) {
			$mp_last_name_placeholder_text = '';

			if ( 'label' !== $label_placeholder ) {
				$mp_last_name_placeholder = ( isset( $settings['mp_last_name_placeholder'] ) ? $settings['mp_last_name_placeholder'] : '' );
				$mp_last_name_placeholder_text = ( ! empty( $mp_last_name_placeholder ) ? esc_attr( $mp_last_name_placeholder ) : esc_html__( 'Last Name', 'cmsmasters-elementor' ) );
			}

			$this->get_form_field( 'mp_name', 'mp_last_name', 'Last Name', $mp_last_name_placeholder_text );
		}
	}

	/**
	 * Get get a field with a email.
	 *
	 * Retrieve a field with settings for the email.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Reworking the structure and logic of displaying the width of the button (so as not to break existing widgets).
	 * @since 1.2.1 Fixed empty CSS Var for W3C validator.
	 *
	 */
	protected function email() {
		$settings = $this->get_settings_for_display();

		$label_placeholder = ( isset( $settings['label_placeholder'] ) ? $settings['label_placeholder'] : '' );
		$email_placeholder_text = '';

		if ( 'label' !== $label_placeholder ) {
			$email_placeholder = ( isset( $settings['email_placeholder'] ) ? $settings['email_placeholder'] : '' );
			$email_placeholder_text = ( ! empty( $email_placeholder ) ? esc_attr( $email_placeholder ) : esc_html__( 'Email', 'cmsmasters-elementor' ) );
		}

		$this->get_form_field( 'email', 'email', 'Email', $email_placeholder_text );
	}

	/**
	 * Get get a fields with a address.
	 *
	 * Retrieve a fields with settings for the address.
	 *
	 * @since 1.0.0
	 * @since 1.16.4 Fixed label placeholder.
	 *
	 */
	protected function address() {
		$settings = $this->get_settings_for_display();

		$label_placeholder = ( isset( $settings['label_placeholder'] ) ? $settings['label_placeholder'] : '' );
		$additionally_placeholder_text = '';
		$city_placeholder_text = '';
		$state_placeholder_text = '';
		$zip_placeholder_text = '';
		$country_placeholder_text = '';

		if ( 'label' !== $label_placeholder ) {
			$additionally_placeholder = ( isset( $settings['additionally_placeholder'] ) ? $settings['additionally_placeholder'] : '' );
			$additionally_placeholder_text = ( ! empty( $additionally_placeholder ) ? esc_attr( $additionally_placeholder ) : esc_html__( 'Additionally', 'cmsmasters-elementor' ) );

			$city_placeholder = ( isset( $settings['city_placeholder'] ) ? $settings['city_placeholder'] : '' );
			$city_placeholder_text = ( ! empty( $city_placeholder ) ? esc_attr( $city_placeholder ) : esc_html__( 'City', 'cmsmasters-elementor' ) );

			$state_placeholder = ( isset( $settings['state_placeholder'] ) ? $settings['state_placeholder'] : '' );
			$state_placeholder_text = ( ! empty( $state_placeholder ) ? esc_attr( $state_placeholder ) : esc_html__( 'State/Prov/Region', 'cmsmasters-elementor' ) );

			$zip_placeholder = ( isset( $settings['zip_placeholder'] ) ? $settings['zip_placeholder'] : '' );
			$zip_placeholder_text = ( ! empty( $zip_placeholder ) ? esc_attr( $zip_placeholder ) : esc_html__( 'Zip/Postal', 'cmsmasters-elementor' ) );

			$country_placeholder = ( isset( $settings['country_placeholder'] ) ? $settings['country_placeholder'] : '' );
			$country_placeholder_text = ( ! empty( $country_placeholder ) ? esc_attr( $country_placeholder ) : esc_html__( 'Country', 'cmsmasters-elementor' ) );
		}

		$this->get_form_field( 'address', 'additionally', 'Street Address', $additionally_placeholder_text );

		$this->get_form_field( 'address', 'city', 'City', $city_placeholder_text );

		$this->get_form_field( 'address', 'state', 'Sate/Prov/Region', $state_placeholder_text );

		$this->get_form_field( 'address', 'zip', 'Zip/Postal', $zip_placeholder_text );

		$this->get_form_field( 'address', 'country', 'Country', $country_placeholder_text );
	}

	/**
	 * Get get a fields with a phone.
	 *
	 * Retrieve a fields with settings for the phone.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function phone() {
		$settings = $this->get_settings_for_display();

		$label_placeholder = ( isset( $settings['label_placeholder'] ) ? $settings['label_placeholder'] : '' );
		$phone_placeholder_text = '';

		if ( 'label' !== $label_placeholder ) {
			$phone_placeholder = ( isset( $settings['phone_placeholder'] ) ? $settings['phone_placeholder'] : '' );
			$phone_placeholder_text = ( ! empty( $phone_placeholder ) ? esc_attr( $phone_placeholder ) : esc_html__( 'Phone', 'cmsmasters-elementor' ) );
		}

		$this->get_form_field( 'phone', 'phone', 'Phone', $phone_placeholder_text );
	}

	/**
	 * Get get a fields with a birth day.
	 *
	 * Retrieve a fields with settings for the birth day.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function bday() {
		$settings = $this->get_settings_for_display();

		$label_placeholder = ( isset( $settings['label_placeholder'] ) ? $settings['label_placeholder'] : '' );
		$bday_placeholder_text = '';

		if ( 'label' !== $label_placeholder ) {
			$bday_placeholder = ( isset( $settings['bday_placeholder'] ) ? $settings['bday_placeholder'] : '' );
			$bday_placeholder_text = ( ! empty( $mp_name_placeholder ) ? esc_attr( $mp_name_placeholder ) : esc_html__( 'mm.dd', 'cmsmasters-elementor' ) );
		}

		$this->get_form_field( 'bday', 'bday', 'Month/Day - Birthday', $bday_placeholder_text );
	}

	/**
	 * Get get a fields with a terms of use.
	 *
	 * Retrieve a fields with settings for the terms of use.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function terms_of_use() {
		$settings = $this->get_settings_for_display();

		if ( $settings['terms_use'] || $settings['confirmation_message'] ) {
			$terms_id = $this->field_id( 'terms-use-' );

			$this->add_render_attribute(
				array(
					'terms_input' => array(
						'class' => array(
							'elementor-widget-cmsmasters-mailchimp__check-box',
						),
						'id' => $terms_id,
						'type' => 'checkbox',
						'name' => 'terms-use',
					),
					'terms_label' => array(
						'class' => array(
							'elementor-widget-cmsmasters-mailchimp__terms-label',
						),
						'for' => $terms_id,
					),
					'terms_link' => array(
						'class' => 'elementor-widget-cmsmasters-mailchimp__terms-link',
					),
				)
			);

			echo '<div ' . $this->get_render_attribute_string( 'terms_group' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

				$tag = 'span';
				$terms_text = ( empty( $settings['terms_use_text'] ) ? esc_html__( 'Do you really want to unsubscribe?', 'cmsmasters-elementor' ) : esc_html( $settings['terms_use_text'] ) );

			if ( 'subscribe' === $settings['action_form'] ) {
				$terms_text = ( empty( $settings['terms_use_text'] ) ? esc_html__( 'Make sure you agree to the terms of service', 'cmsmasters-elementor' ) : esc_html( $settings['terms_use_text'] ) );

				if ( '' !== $settings['terms_use_url']['url'] ) {
					$tag = 'a';
					$this->add_link_attributes( 'terms_link', $settings['terms_use_url'] );
				}
			}

				echo '<div class="elementor-widget-cmsmasters-mailchimp__terms-wrapper">' .
					'<input ' . $this->get_render_attribute_string( 'terms_input' ) . ' required>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'<label ' . $this->get_render_attribute_string( 'terms_label' ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'<' . $tag . ' ' . $this->get_render_attribute_string( 'terms_link' ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$terms_text . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'</' . $tag . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'</label>' .
				'</div>' .
			'</div>';
		}
	}

	/**
	 * Get a submit text.
	 *
	 * Retrieve a submit text with settings.
	 *
	 * @since 1.16.4
	 *
	 */
	protected function submit_text( $settings ) {
		$text = __( 'Subscribe', 'cmsmasters-elementor' );

		if ( 'unsubscribe' === $settings['action_form'] ) {
			$text = __( 'Unsubscribe', 'cmsmasters-elementor' );
		}

		$button_text = ( empty( $settings['button_text'] ) ? $text : esc_html( $settings['button_text'] ) );

		echo '<span class="elementor-widget-cmsmasters-mailchimp__button-text">' .
			$button_text . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'</span>';
	}

	/**
	 * Get a submit icon.
	 *
	 * Retrieve a submit icon with settings.
	 *
	 * @since 1.16.4
	 *
	 */
	protected function submit_icon( $settings ) {
		$button_icon = ( isset( $settings['button_icon'] ) ? $settings['button_icon'] : '' );

		echo '<span class="elementor-widget-cmsmasters-mailchimp__button-icon">';

		if ( '' !== $button_icon['value'] ) {
			Icons_Manager::render_icon( $button_icon, array( 'aria-hidden' => 'true' ) );
		} else {
			Icons_Manager::render_icon(
				array(
					'value' => 'far fa-envelope',
					'library' => 'fa-regular',
				),
				array( 'aria-hidden' => 'true' )
			);
		}

		echo '</span>';
	}

	/**
	 * Get a submit icon preloader.
	 *
	 * Retrieve a submit icon preloader with settings.
	 *
	 * @since 1.16.4
	 *
	 */
	protected function submit_icon_preloader( $settings ) {
		$icon_loader = ( isset( $settings['icon_loader'] ) ? $settings['icon_loader'] : '' );

		echo '<span class="elementor-widget-cmsmasters-mailchimp__button-preloader">';

		if ( '' !== $icon_loader['value'] ) {
			Icons_Manager::render_icon( $icon_loader, array( 'aria-hidden' => 'true' ) );
		} else {
			Icons_Manager::render_icon(
				array(
					'value' => 'fas fa-spinner',
					'library' => 'fa-solid',
				),
				array( 'aria-hidden' => 'true' )
			);
		}

		echo '</span>';
	}

	/**
	 * Get a submit.
	 *
	 * Retrieve a submit with settings.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function submit() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			array(
				'submit_group' => array(
					'class' => array(
						'elementor-widget-cmsmasters-mailchimp__field-group',
						'elementor-widget-cmsmasters-mailchimp__field-type-submit',
					),
				),
				'button' => array(
					'class' => array(
						'elementor-widget-cmsmasters-mailchimp__button',
					),
					'name' => 'submit',
					'type' => 'submit',
				),
			)
		);

		if ( $settings['terms_use'] || $settings['confirmation_message'] ) {
			$this->add_render_attribute( 'button', 'disabled', 'disabled' );
		}

		$button_type = ( isset( $settings['button_type'] ) ? $settings['button_type'] : '' );

		if ( 'icon' === $button_type ) {
			$this->add_render_attribute( 'button', 'aria-label', 'Submit Button' );
		}

		echo '<div ' . $this->get_render_attribute_string( 'submit_group' ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'<button ' . $this->get_render_attribute_string( 'button' ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'<span class="elementor-widget-cmsmasters-mailchimp__button-content">';

					if ( 'text' !== $button_type ) {
						$this->submit_icon( $settings );
					}

					if ( 'icon' !== $button_type ) {
						$this->submit_text( $settings );
					}

				echo '</span>';

				$this->submit_icon_preloader( $settings );

			echo '</button>
		</div>';
	}

	/**
	 * Get a valid message.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function valid_message() {
		$settings = $this->get_settings_for_display();

		$success_text = empty( $settings['success_text'] ) ? __( 'Thank you for subscribing to the newsletter.', 'cmsmasters-elementor' ) : esc_html( $settings['success_text'] );

		echo '<p class="elementor-widget-cmsmasters-mailchimp__valid-message elementor-widget-cmsmasters-mailchimp__field-group">' . $success_text . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Get a error message.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function error_message() {
		$settings = $this->get_settings_for_display();

		$general = empty( $settings['general_error_text'] ) ? __( 'Oops. Something went wrong. Please try again later.', 'cmsmasters-elementor' ) : esc_html( $settings['general_error_text'] );

		echo '<p class="elementor-widget-cmsmasters-mailchimp__error-message elementor-widget-cmsmasters-mailchimp__field-group">' . $general . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Fields ID.
	 *
	 * @since 1.3.2
	 *
	 */
	protected function field_id( $name ) {
		$id = uniqid( $name );

		return $id;
	}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array(
			'terms_use_url' => array(
				'field' => 'url',
				'type' => esc_html__( 'Terms Url', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			'redirect_url' => array(
				'field' => 'url',
				'type' => esc_html__( 'Redirect Url', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'tag',
				'type' => esc_html__( 'Tag', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'mp_first_name_label',
				'type' => esc_html__( 'First Name Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'mp_first_name_placeholder',
				'type' => esc_html__( 'First Name Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'mp_last_name_label',
				'type' => esc_html__( 'Last Name Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'mp_last_name_placeholder',
				'type' => esc_html__( 'Last Name Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'email_label',
				'type' => esc_html__( 'Email Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'email_placeholder',
				'type' => esc_html__( 'Email Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'additionally_label',
				'type' => esc_html__( 'Street Address Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'additionally_placeholder',
				'type' => esc_html__( 'Street Address Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'city_label',
				'type' => esc_html__( 'City Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'city_placeholder',
				'type' => esc_html__( 'City Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'state_label',
				'type' => esc_html__( 'State/Prov/Region Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'state_placeholder',
				'type' => esc_html__( 'State/Prov/Region Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'zip_label',
				'type' => esc_html__( 'Zip/Postal Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'zip_placeholder',
				'type' => esc_html__( 'Zip/Postal Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'country_label',
				'type' => esc_html__( 'Country Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'country_placeholder',
				'type' => esc_html__( 'Country Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'phone_label',
				'type' => esc_html__( 'Phone Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'phone_placeholder',
				'type' => esc_html__( 'Phone Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'bday_label',
				'type' => esc_html__( 'Birthday Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'bday_placeholder',
				'type' => esc_html__( 'Birthday Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'terms_use_text',
				'type' => esc_html__( 'Terms Use Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'button_text',
				'type' => esc_html__( 'Button Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'success_text',
				'type' => esc_html__( 'Success Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'success_optin_text',
				'type' => esc_html__( 'Success Option Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'already_subscribed',
				'type' => esc_html__( 'Already Subscribed', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'update',
				'type' => esc_html__( 'User Update Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'unsubscribed',
				'type' => esc_html__( 'Unsubscribed Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'not_subscribed',
				'type' => esc_html__( 'Not Unsubscribed Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'general_error_text',
				'type' => esc_html__( 'General Error Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
