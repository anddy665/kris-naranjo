<?php
namespace CmsmastersElementor\Modules\AuthorizationLinks\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Group_Control_Typography;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Authorization_Links extends Base_Widget {

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
	 * @since 1.0.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return 'cmsmasters-authorization-links';
	}

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
		return __( 'Authorization Links', 'cmsmasters-elementor' );
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
		return 'cmsicon-authorization-links';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'login',
			'user',
			'authorization',
		);
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
			'widget-cmsmasters-authorization-links',
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
	 * @since 1.0.0
	 * @since 1.1.0 Added group control 'BUTTON_BACKGROUND_GROUP', added gradient for button,
	 * added 'text-decoration' on hover for button, fixed 'condition',
	 * added 'border-radius' on hover.
	 * @since 1.10.1 Added `Layout` control for authorization links.
	 * @since 1.10.1 Fixed conditions
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_login_register',
			array(
				'label' => $this->label_login_register(),
			)
		);

		$this->add_control(
			'heading_Login',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Login', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'show_login',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'login_page',
			array(
				'type' => Controls_Manager::URL,
				'label' => __( 'Login Page URL', 'cmsmasters-elementor' ),
				'default' => array(
					'url' => wp_login_url(),
				),
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'description' => __( 'Note: A link is used that redirects to the default login page.', 'cmsmasters-elementor' ),
				'condition' => array(
					'show_login' => 'yes',
				),
			)
		);

		$this->add_control(
			'login_text',
			array(
				'label' => __( 'Log In Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Log In', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'show_login' => 'yes',
				),
			)
		);

		$this->add_control(
			'prefix_text_login',
			array(
				'label' => __( 'Prefix Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Prefix', 'cmsmasters-elementor' ),
				'condition' => array(
					'show_login' => 'yes',
				),
			)
		);

		$this->add_control(
			'icon_login',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-sign-in-alt',
					'library' => 'solid',
				),
				'separator' => 'before',
				'condition' => array(
					'show_login' => 'yes',
				),
				'recommended' => $this->get_recommended_icons(),
			)
		);

		if ( get_option( 'users_can_register' ) ) {
			$this->add_control(
				'heading_register',
				array(
					'type' => Controls_Manager::HEADING,
					'label' => __( 'Register', 'cmsmasters-elementor' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'show_register',
				array(
					'label' => __( 'Show', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
					'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				)
			);

			$this->add_control(
				'register_page',
				array(
					'type' => Controls_Manager::URL,
					'label' => __( 'Register Page URL', 'cmsmasters-elementor' ),
					'default' => array(
						'url' => wp_registration_url(),
					),
					'dynamic' => array(
						'active' => true,
					),
					'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
					'description' => __( 'Note: A link is used that redirects to the default register page.', 'cmsmasters-elementor' ),
					'condition' => array(
						'show_register' => 'yes',
					),
				)
			);

			$this->add_control(
				'register_text',
				array(
					'label' => __( 'Register Text', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => __( 'Register', 'cmsmasters-elementor' ),
					'separator' => 'before',
					'condition' => array(
						'show_register' => 'yes',
					),
				)
			);

			$this->add_control(
				'prefix_text_register',
				array(
					'label' => __( 'Prefix Text', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => __( 'Prefix', 'cmsmasters-elementor' ),
					'condition' => array(
						'show_register' => 'yes',
					),
				)
			);

			$this->add_control(
				'icon_register',
				array(
					'label' => __( 'Icon', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::ICONS,
					'default' => array(
						'value' => 'fas fa-user-plus',
						'library' => 'solid',
					),
					'separator' => 'before',
					'condition' => array(
						'show_register' => 'yes',
					),
					'recommended' => $this->get_recommended_icons(),
				)
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_logout_account',
			array(
				'label' => __( 'Log Out/Account', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'heading_logout',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Log Out', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'show_logout',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'redirect_after_logout',
			array(
				'type' => Controls_Manager::URL,
				'label' => __( 'Redirect After Logout', 'cmsmasters-elementor' ),
				'default' => array(
					'url' => home_url(),
				),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'description' => __( 'Note: If you do not keep the link, the redirect will be made to the home page.', 'cmsmasters-elementor' ),
				'condition' => array(
					'show_logout' => 'yes',
				),
			)
		);

		$this->add_control(
			'logout_text',
			array(
				'label' => __( 'Log Out Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Log Out', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'show_logout' => 'yes',
				),
			)
		);

		$this->add_control(
			'prefix_text_logout',
			array(
				'label' => __( 'Prefix Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Prefix', 'cmsmasters-elementor' ),
				'condition' => array(
					'show_logout' => 'yes',
				),
			)
		);

		$this->add_control(
			'icon_logout',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-sign-out-alt',
					'library' => 'solid',
				),
				'separator' => 'before',
				'condition' => array(
					'show_logout' => 'yes',
				),
				'recommended' => $this->get_recommended_icons(),
			)
		);

		$this->add_control(
			'heading_account',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Account', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'account_page',
			array(
				'type' => Controls_Manager::URL,
				'label' => __( 'My Account Page URL', 'cmsmasters-elementor' ),
				'default' => array(
					'url' => '#',
				),
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'description' => __( 'Note: for an registered user.', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'account_text',
			array(
				'label' => __( 'My Account Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'My Account', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'account_page[url]!' => '',
				),
			)
		);

		$this->add_control(
			'prefix_text_account',
			array(
				'label' => __( 'Prefix Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Prefix', 'cmsmasters-elementor' ),
				'condition' => array(
					'account_page[url]!' => '',
				),
			)
		);

		$this->add_control(
			'icon_account',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-user',
					'library' => 'solid',
				),
				'separator' => 'before',
				'condition' => array(
					'account_page[url]!' => '',
				),
				'recommended' => $this->get_recommended_icons(),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'addition_options',
			array(
				'label' => __( 'Addition Options', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'reverse',
			array(
				'label' => __( 'Reverse', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'prefix_class' => 'cmsmasters-authorization-links__align-reverse-',
			)
		);

		if ( get_option( 'users_can_register' ) ) {
			$conditions_separator = array(
				'relation' => 'or',
				'terms' => array(
					array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'show_login',
								'operator' => '===',
								'value' => 'yes',
							),
							array(
								'name' => 'show_register',
								'operator' => '===',
								'value' => 'yes',
							),
						),
					),
					array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'show_logout',
								'operator' => '===',
								'value' => 'yes',
							),
							array(
								'name' => 'account_page[url]',
								'operator' => '!==',
								'value' => '',
							),
						),
					),
				),
			);
		} else {
			$conditions_separator = array(
				'relation' => 'or',
				'terms' => array(
					array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'show_login',
								'operator' => '===',
								'value' => 'yes',
							),
						),
					),
					array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'show_logout',
								'operator' => '===',
								'value' => 'yes',
							),
							array(
								'name' => 'account_page[url]',
								'operator' => '!==',
								'value' => '',
							),
						),
					),
				),
			);
		}

		$this->add_control(
			'separator',
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'conditions' => $conditions_separator,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		if ( get_option( 'users_can_register' ) ) {
			$prefix_conditions = array(
				'relation' => 'or',
				'terms' => array(
					array(
						'name' => 'prefix_text_login',
						'operator' => '!==',
						'value' => '',
					),
					array(
						'name' => 'prefix_text_logout',
						'operator' => '!==',
						'value' => '',
					),
					array(
						'name' => 'prefix_text_register',
						'operator' => '!==',
						'value' => '',
					),
					array(
						'name' => 'prefix_text_account',
						'operator' => '!==',
						'value' => '',
					),
				),
			);
		} else {
			$prefix_conditions = array(
				'relation' => 'or',
				'terms' => array(
					array(
						'name' => 'prefix_text_login',
						'operator' => '!==',
						'value' => '',
					),
					array(
						'name' => 'prefix_text_logout',
						'operator' => '!==',
						'value' => '',
					),
					array(
						'name' => 'prefix_text_account',
						'operator' => '!==',
						'value' => '',
					),
				),
			);
		}

		$this->add_control(
			'Prefix_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Link Prefix', 'cmsmasters-elementor' ),
				'conditions' => $prefix_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'prefix_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__prefix',
				'conditions' => $prefix_conditions,
			)
		);

		if ( get_option( 'users_can_register' ) ) {
			$text_conditions = array(
				'relation' => 'or',
				'terms' => array(
					array(
						'name' => 'login_text',
						'operator' => '!==',
						'value' => '',
					),
					array(
						'name' => 'logout_text',
						'operator' => '!==',
						'value' => '',
					),
					array(
						'name' => 'register_text',
						'operator' => '!==',
						'value' => '',
					),
					array(
						'name' => 'account_text',
						'operator' => '!==',
						'value' => '',
					),
				),
			);
		} else {
			$text_conditions = array(
				'relation' => 'or',
				'terms' => array(
					array(
						'name' => 'login_text',
						'operator' => '!==',
						'value' => '',
					),
					array(
						'name' => 'logout_text',
						'operator' => '!==',
						'value' => '',
					),
					array(
						'name' => 'account_text',
						'operator' => '!==',
						'value' => '',
					),
				),
			);
		}

		$this->add_control(
			'typography_link',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Link Text', 'cmsmasters-elementor' ),
				'conditions' => $text_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'link_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__text',
				'conditions' => $text_conditions,
			)
		);

		$this->add_control(
			'alignment',
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
					'justify' => array(
						'title' => __( 'Justify', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'separator' => 'before',
				'prefix_class' => 'cmsmasters-authorization-links__align-',
			)
		);

		$this->add_control(
			'direction',
			array(
				'label' => __( 'Direction', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'row' => array(
						'title' => __( 'Horizontal', 'cmsmasters-elementor' ),
					),
					'column' => array(
						'title' => __( 'Vertical', 'cmsmasters-elementor' ),
					),
				),
				'toggle' => false,
				'default' => 'horizontal',
				'label_block' => false,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-item-direction: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'gap_link',
			array(
				'label' => __( 'Link Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 5 ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__wrapper > div' => 'margin: 0 {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__wrapper' => 'margin: 0 -{{SIZE}}{{UNIT}};',
				),
			)
		);

		if ( get_option( 'users_can_register' ) ) {
			$icon_conditions = array(
				'relation' => 'or',
				'terms' => array(
					array(
						'name' => 'icon_login[value]',
						'operator' => '!==',
						'value' => '',
					),
					array(
						'name' => 'icon_logout[value]',
						'operator' => '!==',
						'value' => '',
					),
					array(
						'name' => 'icon_register[value]',
						'operator' => '!==',
						'value' => '',
					),
					array(
						'name' => 'icon_account[value]',
						'operator' => '!==',
						'value' => '',
					),
				),
			);
		} else {
			$icon_conditions = array(
				'relation' => 'or',
				'terms' => array(
					array(
						'name' => 'icon_login[value]',
						'operator' => '!==',
						'value' => '',
					),
					array(
						'name' => 'icon_logout[value]',
						'operator' => '!==',
						'value' => '',
					),
					array(
						'name' => 'icon_account[value]',
						'operator' => '!==',
						'value' => '',
					),
				),
			);
		}

		$this->add_control(
			'gap_icon',
			array(
				'label' => __( 'Icon Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__wrapper > div > a i + span' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__wrapper > div > a i + svg' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $icon_conditions,
			)
		);

		$this->add_control(
			'gap_prefix',
			array(
				'label' => __( 'Prefix Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__wrapper > div > span' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $prefix_conditions,
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} a > i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} a > svg' => 'width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $icon_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_link',
				'selector' => '{{WRAPPER}} a',
				'separator' => 'before',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'link_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'separator_type',
			array(
				'label' => __( 'Separator Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'groove' => __( 'Groove', 'cmsmasters-elementor' ),
					'ridge' => __( 'Ridge', 'cmsmasters-elementor' ),
				),
				'default' => 'solid',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__separator' => 'border-right-style: {{VALUE}};',
				),
				'condition' => array(
					'separator' => 'yes',
				),
				'conditions' => $conditions_separator,
			)
		);

		$this->add_control(
			'separator_width',
			array(
				'label' => __( 'Separator Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__separator' => 'border-right-width:{{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'separator' => 'yes',
				),
				'conditions' => $conditions_separator,
			)
		);

		$this->add_control(
			'separator_color',
			array(
				'label' => __( 'Separator Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__separator' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'separator' => 'yes',
				),
				'conditions' => $conditions_separator,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_login_style',
			array(
				'label' => __( 'Login', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_login' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'login_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'hover' === $key ) ? ':hover' : '';
			$selector = "{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__wrapper-login{$state}";

			$this->start_controls_tab(
				"login_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$element = ( 'hover' === $key ) ? ':after' : ':before';

			$this->add_control(
				"login_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector . ' .elementor-widget-cmsmasters-authorization-links__text' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'login_text!' => '',
					),
				)
			);

			$this->add_control(
				"login_button_bg_{$key}_background",
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
				"login_background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						"{$selector} > a{$element}" => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						"login_button_bg_{$key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"login_button_bg_{$key}_color_stop",
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
						"login_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"login_button_bg_{$key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						"login_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"login_button_bg_{$key}_color_b_stop",
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
						"login_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"login_button_bg_{$key}_gradient_type",
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
						"login_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"login_button_bg_{$key}_gradient_angle",
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
						"{$selector} > a{$element}" => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{login_button_bg_{$key}_color_stop.SIZE}}{{login_button_bg_{$key}_color_stop.UNIT}}, {{login_button_bg_{$key}_color_b.VALUE}} {{login_button_bg_{$key}_color_b_stop.SIZE}}{{login_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"login_button_bg_{$key}_background" => array( 'gradient' ),
						"login_button_bg_{$key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"login_button_bg_{$key}_gradient_position",
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
						"{$selector} > a{$element}" => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{login_button_bg_{$key}_color_stop.SIZE}}{{login_button_bg_{$key}_color_stop.UNIT}}, {{login_button_bg_{$key}_color_b.VALUE}} {{login_button_bg_{$key}_color_b_stop.SIZE}}{{login_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"login_button_bg_{$key}_background" => array( 'gradient' ),
						"login_button_bg_{$key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			if ( 'normal' === $key ) {
				$this->add_control(
					"login_prefix_color_{$key}",
					array(
						'label' => __( 'Prefix Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__wrapper-login .elementor-widget-cmsmasters-authorization-links__prefix' => 'color: {{VALUE}};',
						),
						'condition' => array(
							'prefix_text_login!' => '',
						),
					)
				);
			}

			$this->add_control(
				"login_icon_color_{$key}",
				array(
					'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector . ' > a > i' => 'color: {{VALUE}};',
						$selector . ' > a > svg' => 'fill: {{VALUE}};',
					),
					'condition' => array(
						'icon_login[value]!' => '',
					),
				)
			);

			$this->add_control(
				"login_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector . ' > a' => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'border_link_border!' => '',
					),
				)
			);

			if ( 'normal' === $key ) {
				$this->add_responsive_control(
					'login_border_radius',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							$selector . ' > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			} else {
				$this->add_responsive_control(
					"login_border_radius_{$key}",
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							$selector . ' > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_control(
					"login_text_decoration_{$key}",
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
						'default' => '',
						'selectors' => array(
							$selector . ' > a .elementor-widget-cmsmasters-authorization-links__text' => 'text-decoration: {{VALUE}};',
						),
					)
				);
			}

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "login_text_shadow_{$key}",
					'selector' => $selector . ' > a',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "login_box_shadow_{$key}",
					'selector' => $selector . ' > a',
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_logout_style',
			array(
				'label' => __( 'Logout', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_logout' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'logout_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'hover' === $key ) ? ':hover' : '';
			$selector = "{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__wrapper-logout{$state}";

			$this->start_controls_tab(
				"logout_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$element = ( 'hover' === $key ) ? ':after' : ':before';

			$this->add_control(
				"logout_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector . ' .elementor-widget-cmsmasters-authorization-links__text' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'logout_text!' => '',
					),
				)
			);

			$this->add_control(
				"logout_button_bg_{$key}_background",
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
				"logout_background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						"{$selector} > a{$element}" => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						"logout_button_bg_{$key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"logout_button_bg_{$key}_color_stop",
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
						"logout_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"logout_button_bg_{$key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						"logout_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"logout_button_bg_{$key}_color_b_stop",
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
						"logout_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"logout_button_bg_{$key}_gradient_type",
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
						"logout_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"logout_button_bg_{$key}_gradient_angle",
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
						"{$selector} > a{$element}" => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{logout_button_bg_{$key}_color_stop.SIZE}}{{logout_button_bg_{$key}_color_stop.UNIT}}, {{logout_button_bg_{$key}_color_b.VALUE}} {{logout_button_bg_{$key}_color_b_stop.SIZE}}{{logout_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"logout_button_bg_{$key}_background" => array( 'gradient' ),
						"logout_button_bg_{$key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"logout_button_bg_{$key}_gradient_position",
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
						"{$selector} > a{$element}" => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{logout_button_bg_{$key}_color_stop.SIZE}}{{logout_button_bg_{$key}_color_stop.UNIT}}, {{logout_button_bg_{$key}_color_b.VALUE}} {{logout_button_bg_{$key}_color_b_stop.SIZE}}{{logout_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"logout_button_bg_{$key}_background" => array( 'gradient' ),
						"logout_button_bg_{$key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			if ( 'normal' === $key ) {
				$this->add_control(
					"logout_prefix_color_{$key}",
					array(
						'label' => __( 'Prefix Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__wrapper-logout .elementor-widget-cmsmasters-authorization-links__prefix' => 'color: {{VALUE}};',
						),
						'condition' => array(
							'prefix_text_logout!' => '',
						),
					)
				);
			}

			$this->add_control(
				"logout_icon_color_{$key}",
				array(
					'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector . ' > a > i' => 'color: {{VALUE}};',
						$selector . ' > a > svg' => 'fill: {{VALUE}};',
					),
					'condition' => array(
						'icon_logout[value]!' => '',
					),
				)
			);

			$this->add_control(
				"logout_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector . ' > a' => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'border_link_border!' => '',
					),
				)
			);

			if ( 'normal' === $key ) {
				$this->add_responsive_control(
					'logout_border_radius',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							$selector . ' > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			} else {
				$this->add_responsive_control(
					"logout_border_radius_{$key}",
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							$selector . ' > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_control(
					"logout_text_decoration_{$key}",
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
						'default' => '',
						'selectors' => array(
							$selector . ' > a .elementor-widget-cmsmasters-authorization-links__text' => 'text-decoration: {{VALUE}};',
						),
					)
				);
			}

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "logout_text_shadow_{$key}",
					'selector' => $selector . ' > a',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "logout_box_shadow_{$key}",
					'selector' => $selector . ' > a',
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();

		if ( get_option( 'users_can_register' ) ) {
			$this->start_controls_section(
				'section_register_style',
				array(
					'label' => __( 'Register', 'cmsmasters-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
					'condition' => array(
						'show_register' => 'yes',
					),
				)
			);

			$this->start_controls_tabs( 'register_tabs' );

			$colors = array(
				'normal' => __( 'Normal', 'cmsmasters-elementor' ),
				'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			);

			foreach ( $colors as $key => $label ) {
				$state = ( 'hover' === $key ) ? ':hover' : '';
				$selector = "{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__wrapper-register{$state}";

				$this->start_controls_tab(
					"register_tab_{$key}",
					array(
						'label' => $label,
					)
				);

				$element = ( 'hover' === $key ) ? ':after' : ':before';

				$this->add_control(
					"register_color_{$key}",
					array(
						'label' => __( 'Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector . ' .elementor-widget-cmsmasters-authorization-links__text' => 'color: {{VALUE}};',
						),
						'condition' => array(
							'register_text!' => '',
						),
					)
				);

				$this->add_control(
					"register_button_bg_{$key}_background",
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
					"register_background_color_{$key}",
					array(
						'label' => __( 'Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => array(
							"{$selector} > a{$element}" => '--button-bg-color: {{VALUE}}; ' .
								'background: var( --button-bg-color );',
						),
						'condition' => array(
							"register_button_bg_{$key}_background" => array(
								'color',
								'gradient',
							),
						),
					)
				);

				$this->add_control(
					"register_button_bg_{$key}_color_stop",
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
							"register_button_bg_{$key}_background" => array( 'gradient' ),
						),
						'of_type' => 'gradient',
					)
				);

				$this->add_control(
					"register_button_bg_{$key}_color_b",
					array(
						'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'default' => '#f2295b',
						'render_type' => 'ui',
						'condition' => array(
							"register_button_bg_{$key}_background" => array( 'gradient' ),
						),
						'of_type' => 'gradient',
					)
				);

				$this->add_control(
					"register_button_bg_{$key}_color_b_stop",
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
							"register_button_bg_{$key}_background" => array( 'gradient' ),
						),
						'of_type' => 'gradient',
					)
				);

				$this->add_control(
					"register_button_bg_{$key}_gradient_type",
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
							"register_button_bg_{$key}_background" => array( 'gradient' ),
						),
						'of_type' => 'gradient',
					)
				);

				$this->add_control(
					"register_button_bg_{$key}_gradient_angle",
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
							"{$selector} > a{$element}" => 'background-color: transparent; ' .
								"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{register_button_bg_{$key}_color_stop.SIZE}}{{register_button_bg_{$key}_color_stop.UNIT}}, {{register_button_bg_{$key}_color_b.VALUE}} {{register_button_bg_{$key}_color_b_stop.SIZE}}{{register_button_bg_{$key}_color_b_stop.UNIT}})",
						),
						'condition' => array(
							"register_button_bg_{$key}_background" => array( 'gradient' ),
							"register_button_bg_{$key}_gradient_type" => 'linear',
						),
						'separator' => 'after',
						'of_type' => 'gradient',
					)
				);

				$this->add_control(
					"register_button_bg_{$key}_gradient_position",
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
							"{$selector} > a{$element}" => 'background-color: transparent; ' .
								"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{register_button_bg_{$key}_color_stop.SIZE}}{{register_button_bg_{$key}_color_stop.UNIT}}, {{register_button_bg_{$key}_color_b.VALUE}} {{register_button_bg_{$key}_color_b_stop.SIZE}}{{register_button_bg_{$key}_color_b_stop.UNIT}})",
						),
						'condition' => array(
							"register_button_bg_{$key}_background" => array( 'gradient' ),
							"register_button_bg_{$key}_gradient_type" => 'radial',
						),
						'separator' => 'after',
						'of_type' => 'gradient',
					)
				);

				if ( 'normal' === $key ) {
					$this->add_control(
						"register_prefix_color_{$key}",
						array(
							'label' => __( 'Prefix Color', 'cmsmasters-elementor' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__wrapper-register .elementor-widget-cmsmasters-authorization-links__prefix' => 'color: {{VALUE}};',
							),
							'condition' => array(
								'prefix_text_register!' => '',
							),
						)
					);
				}

				$this->add_control(
					"register_icon_color_{$key}",
					array(
						'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector . ' > a > i' => 'color: {{VALUE}};',
							$selector . ' > a > svg' => 'fill: {{VALUE}};',
						),
						'condition' => array(
							'icon_register[value]!' => '',
						),
					)
				);

				$this->add_control(
					"register_border_color_{$key}",
					array(
						'label' => __( 'Border Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector . ' > a' => 'border-color: {{VALUE}};',
						),
						'condition' => array(
							'border_link_border!' => '',
						),
					)
				);

				if ( 'normal' === $key ) {
					$this->add_responsive_control(
						'register_border_radius',
						array(
							'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
							'type' => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', '%' ),
							'selectors' => array(
								$selector . ' > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							),
						)
					);
				} else {
					$this->add_responsive_control(
						"register_border_radius_{$key}",
						array(
							'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
							'type' => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', '%' ),
							'selectors' => array(
								$selector . ' > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							),
						)
					);

					$this->add_control(
						"register_text_decoration_{$key}",
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
							'default' => '',
							'selectors' => array(
								$selector . ' > a .elementor-widget-cmsmasters-authorization-links__text' => 'text-decoration: {{VALUE}};',
							),
						)
					);
				}

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name' => "register_text_shadow_{$key}",
						'selector' => $selector . ' > a',
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name' => "register_box_shadow_{$key}",
						'selector' => $selector . ' > a',
					)
				);

				$this->end_controls_tab();
			}

			$this->end_controls_tabs();

			$this->end_controls_section();
		}

		$this->start_controls_section(
			'section_account_style',
			array(
				'label' => __( 'My Account', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'account_page[url]!' => '',
				),
			)
		);

		$this->start_controls_tabs( 'account_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'hover' === $key ) ? ':hover' : '';
			$selector = "{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__wrapper-account{$state}";

			$this->start_controls_tab(
				"account_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$element = ( 'hover' === $key ) ? ':after' : ':before';

			$this->add_control(
				"account_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector . ' .elementor-widget-cmsmasters-authorization-links__text' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'account_text!' => '',
					),
				)
			);

			$this->add_control(
				"account_button_bg_{$key}_background",
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
				"account_background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						"{$selector} > a{$element}" => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						"account_button_bg_{$key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"account_button_bg_{$key}_color_stop",
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
						"account_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"account_button_bg_{$key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						"account_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"account_button_bg_{$key}_color_b_stop",
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
						"account_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"account_button_bg_{$key}_gradient_type",
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
						"account_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"account_button_bg_{$key}_gradient_angle",
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
						"{$selector} > a{$element}" => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{account_button_bg_{$key}_color_stop.SIZE}}{{account_button_bg_{$key}_color_stop.UNIT}}, {{account_button_bg_{$key}_color_b.VALUE}} {{account_button_bg_{$key}_color_b_stop.SIZE}}{{account_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"account_button_bg_{$key}_background" => array( 'gradient' ),
						"account_button_bg_{$key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"account_button_bg_{$key}_gradient_position",
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
						"{$selector} > a{$element}" => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{account_button_bg_{$key}_color_stop.SIZE}}{{account_button_bg_{$key}_color_stop.UNIT}}, {{account_button_bg_{$key}_color_b.VALUE}} {{account_button_bg_{$key}_color_b_stop.SIZE}}{{account_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"account_button_bg_{$key}_background" => array( 'gradient' ),
						"account_button_bg_{$key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			if ( 'normal' === $key ) {
				$this->add_control(
					"account_prefix_color_{$key}",
					array(
						'label' => __( 'Prefix Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .elementor-widget-cmsmasters-authorization-links__wrapper-account .elementor-widget-cmsmasters-authorization-links__prefix' => 'color: {{VALUE}};',
						),
						'condition' => array(
							'prefix_text_account!' => '',
						),
					)
				);
			}

			$this->add_control(
				"account_icon_color_{$key}",
				array(
					'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector . ' > a > i' => 'color: {{VALUE}};',
						$selector . ' > a > svg' => 'fill: {{VALUE}};',
					),
					'condition' => array(
						'icon_account[value]!' => '',
					),
				)
			);

			$this->add_control(
				"account_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector . ' > a' => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'border_link_border!' => '',
					),
				)
			);

			if ( 'normal' === $key ) {
				$this->add_responsive_control(
					'account_border_radius',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							$selector . ' > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			} else {
				$this->add_responsive_control(
					"account_border_radius_{$key}",
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							$selector . ' > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_control(
					"account_text_decoration_{$key}",
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
						'default' => '',
						'selectors' => array(
							$selector . ' > a .elementor-widget-cmsmasters-authorization-links__text' => 'text-decoration: {{VALUE}};',
						),
					)
				);
			}

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "account_text_shadow_{$key}",
					'selector' => $selector . ' > a',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "account_box_shadow_{$key}",
					'selector' => $selector . ' > a',
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get widgets label login/register.
	 *
	 * Retrieve widgets label login/register.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function label_login_register() {
		if ( get_option( 'users_can_register' ) ) {
			return __( 'Log In/Register', 'cmsmasters-elementor' );
		} else {
			return __( 'Log In', 'cmsmasters-elementor' );
		}
	}

	public function get_recommended_icons() {
		$recommended_icons = array(
			'fa-regular' => array(
				'user',
				'user-circle',
			),
			'fa-solid' => array(
				'sign-in-alt',
				'sign-out-alt',
				'user',
				'user-alt',
				'user-check',
				'user-circle',
				'user-plus',
				'user-lock',
				'user-secret',
				'users',
			),
		);

		/**
		 * Filters recommended icons.
		 *
		 * Filters widget recommended icons array.
		 *
		 * @since 1.0.0
		 *
		 * @param array $recommended_icons Recommended icons.
		 */
		$recommended_icons = apply_filters( 'cmsmasters_elementor/widgets/authorization_links/recommended_icons', $recommended_icons );

		return $recommended_icons;
	}

	/**
	 * Render widget.
	 *
	 * Outputs the widget HTML code on the frontend.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$is_editor = Plugin::$instance->editor->is_edit_mode();
		$settings = $this->get_settings_for_display();
		$show = '';
		$hide = '';

		$this->add_render_attribute(
			array(
				'login-wrapper' => array(
					'class' => 'elementor-widget-cmsmasters-authorization-links__wrapper-login',
				),

				'logout-wrapper' => array(
					'class' => 'elementor-widget-cmsmasters-authorization-links__wrapper-logout',
				),

				'register-wrapper' => array(
					'class' => 'elementor-widget-cmsmasters-authorization-links__wrapper-register',
				),

				'account-wrapper' => array(
					'class' => 'elementor-widget-cmsmasters-authorization-links__wrapper-account',
				),
			)
		);

		if ( $is_editor ) {
			$this->add_render_attribute(
				array(
					'login-wrapper' => array(
						'class' => 'elementor-widget-cmsmasters-authorization-links__show',
					),

					'logout-wrapper' => array(
						'class' => 'elementor-widget-cmsmasters-authorization-links__hide',
					),

					'register-wrapper' => array(
						'class' => 'elementor-widget-cmsmasters-authorization-links__show',
					),

					'account-wrapper' => array(
						'class' => 'elementor-widget-cmsmasters-authorization-links__hide',
					),
				)
			);
		}

		$this->add_render_attribute(
			array(
				'login' => array(
					'class' => 'elementor-widget-cmsmasters-authorization-links__link',
					'aria-label' => 'Login',
				),

				'logout' => array(
					'class' => 'elementor-widget-cmsmasters-authorization-links__link',
					'aria-label' => 'Logout',
				),

				'register' => array(
					'class' => 'elementor-widget-cmsmasters-authorization-links__link',
					'aria-label' => 'Register',
				),

				'account' => array(
					'class' => 'elementor-widget-cmsmasters-authorization-links__link',
					'aria-label' => 'Account',
				),
			)
		);

		echo '<div class="elementor-widget-cmsmasters-authorization-links__wrapper">';

		if ( ! is_user_logged_in() || ( is_user_logged_in() && $is_editor ) ) {
			if ( $settings['show_login'] ) {
				$this->add_link_attributes( 'login', $settings['login_page'] );

				echo '<div ' . $this->get_render_attribute_string( 'login-wrapper' ) . '>';

				if ( '' !== $settings['prefix_text_login'] ) {
					echo '<span class="elementor-widget-cmsmasters-authorization-links__prefix">' . esc_html( $settings['prefix_text_login'] ) . '</span>';
				}

					echo '<a ' . $this->get_render_attribute_string( 'login' ) . '>';

				if ( '' !== $settings['icon_login'] ) {
					$login_icon_att = array( 'aria-hidden' => 'true' );

					if ( '' === $settings['login_text'] ) {
						$login_icon_att = array_merge(
							$login_icon_att,
							array( 'aria-label' => 'Login' ),
						);
					}

					Icons_Manager::render_icon( $settings['icon_login'], $login_icon_att );
				}

				if ( '' !== $settings['login_text'] ) {
					echo '<span class="elementor-widget-cmsmasters-authorization-links__text">' . esc_html( $settings['login_text'] ) . '</span>';
				}

					echo '</a>
				</div>';
			}
		}

		if ( is_user_logged_in() || ( is_user_logged_in() && $is_editor ) ) {
			if ( $settings['show_logout'] ) {

				$url = esc_url( wp_logout_url( $settings['redirect_after_logout']['url'] ) );
				$link = "href='{$url}'";
				$target = '';
				$nofollow = '';

				if ( '' !== $settings['redirect_after_logout']['is_external'] ) {
					$target = ' target="_blank"';
				}

				if ( '' !== $settings['redirect_after_logout']['nofollow'] ) {
					$nofollow = ' rel="nofollow"';
				}

				echo '<div ' . $this->get_render_attribute_string( 'logout-wrapper' ) . '>';

				if ( '' !== $settings['prefix_text_logout'] ) {
					echo '<span class="elementor-widget-cmsmasters-authorization-links__prefix">' . esc_html( $settings['prefix_text_logout'] ) . '</span>';
				}

					echo '<a ' . $link . $target . $nofollow . $this->get_render_attribute_string( 'logout' ) . '>';

				if ( '' !== $settings['icon_logout'] ) {
					$logout_icon_att = array( 'aria-hidden' => 'true' );

					if ( '' === $settings['logout_text'] ) {
						$logout_icon_att = array_merge(
							$logout_icon_att,
							array( 'aria-label' => 'Logout' ),
						);
					}

					Icons_Manager::render_icon( $settings['icon_logout'], $logout_icon_att );
				}

				if ( '' !== $settings['logout_text'] ) {
					echo '<span class="elementor-widget-cmsmasters-authorization-links__text">' . esc_html( $settings['logout_text'] ) . '</span>';
				}

					echo '</a>
				</div>';
			}
		}

		if ( get_option( 'users_can_register' ) ) {
			if ( $settings['separator'] && $settings['show_login'] && $settings['show_register'] || ( $settings['separator'] && $settings['show_logout'] && '' !== $settings['account_page']['url'] ) ) {
				echo '<span class="elementor-widget-cmsmasters-authorization-links__separator"></span>';
			}
		} else {
			if ( $settings['separator'] && $settings['show_login'] || ( $settings['separator'] && $settings['show_logout'] && '' !== $settings['account_page']['url'] ) ) {
				echo '<span class="elementor-widget-cmsmasters-authorization-links__separator"></span>';
			}
		}

		if ( ! is_user_logged_in() && get_option( 'users_can_register' ) || ( is_user_logged_in() && $is_editor && get_option( 'users_can_register' ) ) ) {
			if ( $settings['show_register'] ) {
				$this->add_link_attributes( 'register', $settings['register_page'] );

				echo '<div ' . $this->get_render_attribute_string( 'register-wrapper' ) . '>';

				if ( '' !== $settings['prefix_text_register'] ) {
					echo '<span class="elementor-widget-cmsmasters-authorization-links__prefix">' . esc_html( $settings['prefix_text_register'] ) . '</span>';
				}

					echo '<a ' . $this->get_render_attribute_string( 'register' ) . '>';

				if ( '' !== $settings['icon_register'] ) {
					$register_icon_att = array( 'aria-hidden' => 'true' );

					if ( '' === $settings['register_text'] ) {
						$register_icon_att = array_merge(
							$register_icon_att,
							array( 'aria-label' => 'Register' ),
						);
					}

					Icons_Manager::render_icon( $settings['icon_register'], $register_icon_att );
				}

				if ( '' !== $settings['register_text'] ) {
					echo '<span class="elementor-widget-cmsmasters-authorization-links__text">' . esc_html( $settings['register_text'] ) . '</span>';
				}

					echo '</a>
				</div>';
			}
		}

		if ( is_user_logged_in() || ( is_user_logged_in() && $is_editor ) ) {
			$this->add_link_attributes( 'account', $settings['account_page'] );

			if ( '' !== $settings['account_page']['url'] ) {
				echo '<div ' . $this->get_render_attribute_string( 'account-wrapper' ) . '>';

				if ( '' !== $settings['prefix_text_account'] ) {
					echo '<span class="elementor-widget-cmsmasters-authorization-links__prefix">' . esc_html( $settings['prefix_text_account'] ) . '</span>';
				}

					echo '<a ' . $this->get_render_attribute_string( 'account' ) . '>';

				if ( '' !== $settings['icon_account'] ) {
					$account_icon_att = array( 'aria-hidden' => 'true' );

					if ( '' === $settings['account_text'] ) {
						$account_icon_att = array_merge(
							$account_icon_att,
							array( 'aria-label' => 'Account' ),
						);
					}

					Icons_Manager::render_icon( $settings['icon_account'], $account_icon_att );
				}

				if ( '' !== $settings['account_text'] ) {
					echo '<span class="elementor-widget-cmsmasters-authorization-links__text">' . esc_html( $settings['account_text'] ) . '</span>';
				}

					echo '</a>
				</div>';
			}
		}

		echo '</div>';
	}

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.0.0
	 */
	public function render_plain_content() {}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array(
			'login_page' => array(
				'field' => 'url',
				'type' => esc_html__( 'Login Page URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'login_text',
				'type' => esc_html__( 'Log In Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'prefix_text_login',
				'type' => esc_html__( 'Prefix Login Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			'register_page' => array(
				'field' => 'url',
				'type' => esc_html__( 'Register Page URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'register_text',
				'type' => esc_html__( 'Register Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'prefix_text_register',
				'type' => esc_html__( 'Prefix Register Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			'redirect_after_logout' => array(
				'field' => 'url',
				'type' => esc_html__( 'Redirect After Logout', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'logout_text',
				'type' => esc_html__( 'Log Out Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'prefix_text_logout',
				'type' => esc_html__( 'Prefix Text Logout', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			'account_page' => array(
				'field' => 'url',
				'type' => esc_html__( 'Account Page URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'account_text',
				'type' => esc_html__( 'Account Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'prefix_text_account',
				'type' => esc_html__( 'Prefix Account Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}