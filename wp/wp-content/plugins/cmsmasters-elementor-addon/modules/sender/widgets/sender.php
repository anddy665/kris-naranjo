<?php
namespace CmsmastersElementor\Modules\Sender\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Sender extends Base_Widget {

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.15.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Sender', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.15.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-sender';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.15.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'sender',
			'email marketing',
			'contact form',
			'newsletter subscription',
			'subscription form',
		);
	}

	/**
	 * @since 1.24.0
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.16.0
	 * @since 1.17.1 Fixed display icons.
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		$style_depends = array(
			'widget-cmsmasters-sender',
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
	 * Outputs elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-sender';
	}

	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
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
	 * @since 1.15.0
	 */

	protected function register_controls() {
		$this->register_general_controls_content();

		if ( ! empty( get_option( 'elementor_sender_api_key' ) ) ) {
			$this->register_button_controls_content();

			$this->register_layout_controls_style();

			$this->register_fields_controls_style();

			$this->register_button_controls_style();

			$this->register_terms_use_controls_style();
		}
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.15.0
	 */
	protected function register_general_controls_content() {
		$api_key = get_option( 'elementor_sender_api_key' );

		if ( empty( $api_key ) ) {
			$this->start_controls_section(
				'section_warning',
				array( 'label' => __( 'Sender', 'cmsmasters-elementor' ) )
			);

			$this->add_control(
				'warning',
				array(
					'raw' => __( 'Please go to the  ', 'cmsmasters-elementor' ) . '<a href="' . esc_url( admin_url( 'admin.php?page=cmsmasters-addon-settings' ) ) . '" target="_blank">' . __( 'settings page', 'cmsmasters-elementor' ) . '</a>' . __( ' and add your Sender api key', 'cmsmasters-elementor' ),
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
			array( 'label' => __( 'Sender', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'simple_form',
			array(
				'label' => __( 'Simple Form', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'description' => __( 'If Simple Form button is enabled, only email field is available', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'render_type' => 'template',
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'simple_form_button_position',
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
				'selectors_dictionary' => array(
					'left' => 'row-reverse',
					'bottom' => 'column',
					'right' => 'row',
				),
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}}' => '--simple-form-button-position: {{VALUE}};',
				),
				'condition' => array( 'simple_form' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'button_alignment',
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
					'stretch' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'default' => 'left',
				'toggle' => false,
				'selectors_dictionary' => array(
					'left' => 'flex-start',
					'center' => 'center',
					'right' => 'flex-end',
					'stretch' => 'stretch',
				),
				'prefix_class' => 'cmsmasters-button-alignment-',
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}}' => '--button-alignment: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'simple_form',
									'operator' => '===',
									'value' => 'yes',
								),
								array(
									'name' => 'simple_form_button_position',
									'operator' => '===',
									'value' => 'bottom',
								),
							),
						),
						array(
							'name' => 'simple_form',
							'operator' => '===',
							'value' => '',
						),
					),
				),
			)
		);

		$this->add_control(
			'multiple_list',
			array(
				'label' => __( 'Fields List', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => array(
					'name' => esc_html__( 'Name', 'cmsmasters-elementor' ),
				),
				'condition' => array( 'simple_form' => '' ),
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
					'placeholders' => array(
						'title' => __( 'Placeholders', 'cmsmasters-elementor' ),
						'description' => __( 'Show only placeholders', 'cmsmasters-elementor' ),
					),
					'both' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => __( 'Show both, labels & placeholders', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'placeholders',
				'separator' => 'before',
				'toggle' => false,
			)
		);

		$this->add_control(
			'name_heading',
			array(
				'label' => esc_html__( 'Name', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'simple_form' => '',
					'multiple_list' => 'name',
				),
			)
		);

		$this->add_control(
			'name_required',
			array(
				'label' => __( 'Required', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'condition' => array(
					'simple_form' => '',
					'multiple_list' => 'name',
				),
			)
		);

		$this->add_control(
			'full_name',
			array(
				'label' => __( 'Use a Last Name?', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'condition' => array(
					'simple_form' => '',
					'multiple_list' => 'name',
				),
			)
		);

		$this->add_control(
			'terms_use',
			array(
				'label' => __( 'Terms of Use', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => '',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'terms_use_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Your personal text', 'cmsmasters-elementor' ),
				'condition' => array( 'terms_use' => 'yes' ),
			)
		);

		$this->add_control(
			'terms_use_url',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'show_external' => true,
				'label_block' => false,
				'placeholder' => __( 'http(s)://your-link.com', 'cmsmasters-elementor' ),
				'description' => __( 'Use complete (absolute) URL\'s, including http(s)://', 'cmsmasters-elementor' ),
				'condition' => array( 'terms_use' => 'yes' ),
			)
		);

		$this->add_control(
			'terms_use_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'row' => array(
						'title' => __( 'With Button', 'cmsmasters-elementor' ),
					),
					'column-reverse' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
					),
					'column' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'row',
				'toggle' => false,
				'label_block' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-sender-terms-use-position-',
				'selectors' => array(
					'{{WRAPPER}}' => '--terms-use-position: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'simple_form',
									'operator' => '===',
									'value' => 'yes',
								),
								array(
									'name' => 'simple_form_button_position',
									'operator' => '===',
									'value' => 'bottom',
								),
								array(
									'name' => 'terms_use',
									'operator' => '===',
									'value' => 'yes',
								),
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'name' => 'button_alignment',
											'operator' => '===',
											'value' => 'left',
										),
										array(
											'name' => 'button_alignment',
											'operator' => '===',
											'value' => 'right',
										),
									),
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'simple_form',
									'operator' => '!==',
									'value' => 'yes',
								),
								array(
									'name' => 'terms_use',
									'operator' => '===',
									'value' => 'yes',
								),
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'name' => 'button_alignment',
											'operator' => '===',
											'value' => 'left',
										),
										array(
											'name' => 'button_alignment',
											'operator' => '===',
											'value' => 'right',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'terms_use_position_vert',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'column-reverse' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
					),
					'column' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'column-reverse',
				'toggle' => false,
				'label_block' => false,
				'prefix_class' => 'cmsmasters-sender-terms-use-position-',
				'selectors' => array(
					'{{WRAPPER}}' => '--terms-use-position: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'simple_form',
									'operator' => '===',
									'value' => 'yes',
								),
								array(
									'name' => 'simple_form_button_position',
									'operator' => '===',
									'value' => 'bottom',
								),
								array(
									'name' => 'terms_use',
									'operator' => '===',
									'value' => 'yes',
								),
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'name' => 'button_alignment',
											'operator' => '===',
											'value' => 'center',
										),
										array(
											'name' => 'button_alignment',
											'operator' => '===',
											'value' => 'stretch',
										),
									),
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'simple_form',
									'operator' => '!==',
									'value' => 'yes',
								),
								array(
									'name' => 'terms_use',
									'operator' => '===',
									'value' => 'yes',
								),
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'name' => 'button_alignment',
											'operator' => '===',
											'value' => 'center',
										),
										array(
											'name' => 'button_alignment',
											'operator' => '===',
											'value' => 'stretch',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'terms_use_alignment',
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
				'default' => 'left',
				'toggle' => false,
				'selectors_dictionary' => array(
					'left' => 'flex-start',
					'center' => 'center',
					'right' => 'flex-end',
					'stretch' => 'stretch',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--terms-use-alignment: {{VALUE}};',
				),
				'condition' => array( 'terms_use' => 'yes' ),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'simple_form',
									'operator' => '===',
									'value' => 'yes',
								),
								array(
									'name' => 'simple_form_button_position',
									'operator' => '===',
									'value' => 'bottom',
								),
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'name' => 'button_alignment',
											'operator' => '===',
											'value' => 'center',
										),
										array(
											'name' => 'button_alignment',
											'operator' => '===',
											'value' => 'stretch',
										),
									),
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'simple_form',
									'operator' => '===',
									'value' => 'yes',
								),
								array(
									'name' => 'simple_form_button_position',
									'operator' => '===',
									'value' => 'bottom',
								),
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'name' => 'button_alignment',
											'operator' => '===',
											'value' => 'left',
										),
										array(
											'name' => 'button_alignment',
											'operator' => '===',
											'value' => 'right',
										),
									),
								),
								array(
									'name' => 'terms_use_position',
									'operator' => '!==',
									'value' => 'row',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'simple_form',
									'operator' => '===',
									'value' => 'yes',
								),
								array(
									'name' => 'simple_form_button_position',
									'operator' => '!==',
									'value' => 'bottom',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'simple_form',
									'operator' => '!==',
									'value' => 'yes',
								),
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'relation' => 'and',
											'terms' => array(
												array(
													'relation' => 'or',
													'terms' => array(
														array(
															'name' => 'button_alignment',
															'operator' => '===',
															'value' => 'left',
														),
														array(
															'name' => 'button_alignment',
															'operator' => '===',
															'value' => 'right',
														),
													),
												),
												array(
													'name' => 'terms_use_position',
													'operator' => '!==',
													'value' => 'row',
												),
											),
										),
										array(
											'relation' => 'or',
											'terms' => array(
												array(
													'name' => 'button_alignment',
													'operator' => '===',
													'value' => 'center',
												),
												array(
													'name' => 'button_alignment',
													'operator' => '===',
													'value' => 'stretch',
												),
											),
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'terms_use_arrangement',
			array(
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'label' => __( 'Arrangement', 'cmsmasters-elementor' ),
				'options' => array(
					'' => array(
						'title' => __( 'Together', 'cmsmasters-elementor' ),
					),
					'space-between' => array(
						'title' => __( 'Side', 'cmsmasters-elementor' ),
					),
				),
				'default' => '',
				'label_block' => false,
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}}' => '--terms-use-arrangement: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'simple_form',
											'operator' => '===',
											'value' => 'yes',
										),
										array(
											'name' => 'simple_form_button_position',
											'operator' => '===',
											'value' => 'bottom',
										),
									),
								),
								array(
									'name' => 'simple_form',
									'operator' => '!==',
									'value' => 'yes',
								),
							),
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'button_alignment',
									'operator' => '===',
									'value' => 'left',
								),
								array(
									'name' => 'button_alignment',
									'operator' => '===',
									'value' => 'right',
								),
							),
						),
						array(
							'name' => 'terms_use',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'terms_use_position',
							'operator' => '===',
							'value' => 'row',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.15.0
	 */
	protected function register_button_controls_content() {
		$this->start_controls_section(
			'section_button_controls',
			array( 'label' => __( 'Button', 'cmsmasters-elementor' ) )
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
				'placeholder' => __( 'Subscribed', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array( 'button_type!' => 'icon' ),
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
					'library' => 'regular',
				),
				'condition' => array( 'button_type!' => 'text' ),
			)
		);

		$this->add_control(
			'button_icon_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'row' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'row-reverse' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'row',
				'selectors' => array(
					'{{WRAPPER}}' => '--button-icon-direction: {{VALUE}}',
				),
				'condition' => array(
					'button_type!' => 'text',
					'button_icon[value]!' => '',
				),
			)
		);

		$this->add_control(
			'spinner_heading',
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
					'library' => 'solid',
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
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.15.0
	 */
	protected function register_layout_controls_style() {
		$this->start_controls_section(
			'section_layout_style',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'simple_form' => '',
					'multiple_list!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'layout_row_gap',
			array(
				'label' => __( 'Rows Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => '20' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--row-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'simple_form' => '',
					'multiple_list!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'layout_column_gap',
			array(
				'label' => __( 'Columns Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => '20' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--column-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'simple_form' => '',
					'multiple_list!' => '',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.15.0
	 */
	protected function register_fields_controls_style() {
		$this->start_controls_section(
			'section_field_style',
			array(
				'label' => __( 'Fields', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'label_heading',
			array(
				'label' => esc_html__( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array( 'label_placeholder!' => 'placeholders' ),
			)
		);

		$this->add_responsive_control(
			'label_alignment',
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
				'selectors' => array(
					'{{WRAPPER}}' => '--label-alignment: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'label_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--label-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--label-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--label-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--label-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--label-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--label-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--label-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--label-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--label-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'label_placeholder!' => 'placeholders' ),
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--label-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'label_gap',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--label-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'label_placeholder!' => 'placeholders' ),
			)
		);

		$this->add_control(
			'field_heading',
			array(
				'label' => esc_html__( 'Field', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

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
				'selectors' => array(
					'{{WRAPPER}}' => '--field-alignment: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'field_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--field-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--field-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--field-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--field-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--field-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--field-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--field-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--field-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--field-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->start_controls_tabs( 'field_tabs' );

		foreach ( array(
			'normal' => __( 'Default', 'cmsmasters-elementor' ),
			'focus' => __( 'Focus', 'cmsmasters-elementor' ),
		) as $key => $label ) {
			$this->start_controls_tab(
				"field_{$key}_tab",
				array( 'label' => $label )
			);

			$this->add_control(
				"field_{$key}_text_color",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--field-{$key}-color: {{VALUE}};",
					),
				)
			);

			if ( 'normal' === $key ) {
				$this->add_control(
					"field_{$key}_placeholder",
					array(
						'label' => __( 'Placeholder Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}}' => "--field-{$key}-placeholder-color: {{VALUE}};",
						),
						'condition' => array( 'label_placeholder!' => 'label' ),
					)
				);
			}

			$this->add_control(
				"field_{$key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--field-{$key}-bg-color: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"field_{$key}_border_color",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--field-{$key}-border-color: {{VALUE}};",
					),
					'condition' => array(
						'field_border_type!' => array( 'none' ),
					),
				)
			);

			$this->add_responsive_control(
				"field_{$key}_border_radius",
				array(
					'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--field-{$key}-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "field_{$key}_box_shadow",
					'label' => esc_html__( 'Box Shadow', 'cmsmasters-elementor' ),
					'fields_options' => array(
						'box_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => "--field-{$key}-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};",
							),
						),
					),
					'selector' => '{{WRAPPER}}',
				)
			);

			if ( 'focus' === $key ) {
				$this->add_control(
					"field_{$key}_transition_duration",
					array(
						'label' => esc_html__( 'Transition Duration', 'cmsmasters-elementor' ) . ' (ms)',
						'type' => Controls_Manager::SLIDER,
						'range' => array(
							'px' => array(
								'min' => 0,
								'max' => 3000,
							),
						),
						'selectors' => array(
							'{{WRAPPER}}' => "--field-{$key}-transition-duration: {{SIZE}}ms;",
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

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
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--field-padding-top: {{TOP}}{{UNIT}}; --field-padding-right: {{RIGHT}}{{UNIT}}; --field-padding-bottom: {{BOTTOM}}{{UNIT}}; --field-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'field_border_type',
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => "--field-border-style: {{VALUE}};",
				),
			)
		);

		$this->add_responsive_control(
			'field_border_width',
			array(
				'label' => esc_html__( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--field-border-top-width: {{TOP}}{{UNIT}}; --field-border-right-width: {{RIGHT}}{{UNIT}}; --field-border-bottom-width: {{BOTTOM}}{{UNIT}}; --field-border-left-width: {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'field_border_type!' => array(
						'',
						'none',
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.15.0
	 * @since 1.17.1 Fixed applied button loader icon color.
	 */
	protected function register_button_controls_style() {
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
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--button-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--button-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--button-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--button-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--button-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--button-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--button-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--button-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--button-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'button_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => "--button-gap: {{SIZE}}{{UNIT}};",
				),
			)
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $key => $label ) {
			$this->start_controls_tab(
				"button_{$key}_tab",
				array( 'label' => $label )
			);

			$this->add_control(
				"button_{$key}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--button-{$key}-color: {{VALUE}};",
					),
					'condition' => array( 'button_type!' => 'icon' ),
				)
			);

			$this->add_control(
				"button_{$key}_icon_color",
				array(
					'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--button-{$key}-icon-color: {{VALUE}};",
					),
					'condition' => array(
						'button_type!' => 'text',
						'button_icon[value]!' => '',
					),
				)
			);

			$state = ( 'normal' === $key ? ':before' : ':after' );
			$buttons_bg_selector = "{{WRAPPER}} button{$state}";

			$this->add_group_control(
				CmsmastersControls::BUTTON_BACKGROUND_GROUP,
				array(
					'name' => "button_{$key}_bg_group",
					'exclude' => array( 'color' ),
					'selector' => $buttons_bg_selector,
				)
			);

			$this->start_injection( array( 'of' => "button_{$key}_bg_group_background" ) );

			$this->add_control(
				"button_{$key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$buttons_bg_selector => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
					),
					'condition' => array(
						"button_{$key}_bg_group_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->end_injection();

			$this->add_control(
				"button_{$key}_border_color",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--button-{$key}-border-color: {{VALUE}};",
					),
					'condition' => array(
						'button_border_type!' => array(
							'',
						),
					),
				)
			);

			$this->add_responsive_control(
				"button_{$key}_border_radius",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--button-{$key}-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "button_{$key}_text_shadow",
					'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
					'fields_options' => array(
						'text_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => "--button-{$key}-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};",
							),
						),
					),
					'selector' => '{{WRAPPER}}',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "button_{$key}_box_shadow",
					'label' => esc_html__( 'Box Shadow', 'cmsmasters-elementor' ),
					'fields_options' => array(
						'box_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => "--button-{$key}-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};",
							),
						),
					),
					'selector' => '{{WRAPPER}}',
				)
			);

			$this->end_controls_tab();

		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--button-padding-top: {{TOP}}{{UNIT}}; --button-padding-right: {{RIGHT}}{{UNIT}}; --button-padding-bottom: {{BOTTOM}}{{UNIT}}; --button-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_border_type',
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => "--button-border-style: {{VALUE}};",
				),
			)
		);

		$this->add_responsive_control(
			'button_border_width',
			array(
				'label' => esc_html__( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--button-border-top-width: {{TOP}}{{UNIT}}; --button-border-right-width: {{RIGHT}}{{UNIT}}; --button-border-bottom-width: {{BOTTOM}}{{UNIT}}; --button-border-left-width: {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'button_border_type!' => array(
						'',
						'none',
					),
				),
			)
		);

		$this->add_control(
			'button_icon_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'button_type!' => 'text',
					'button_icon[value]!' => '',
				),
			)
		);

		$this->add_control(
			'button_icon_size',
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
					'{{WRAPPER}}' => "--button-icon-size: {{SIZE}}{{UNIT}};",
				),
				'condition' => array(
					'button_type!' => 'text',
					'button_icon[value]!' => '',
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
					'{{WRAPPER}}' => "--button-icon-gap: {{SIZE}}{{UNIT}};",
				),
				'condition' => array(
					'button_type!' => 'text',
					'button_icon[value]!' => '',
				),
			)
		);

		$this->add_control(
			'spinner_style_heading',
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
					'{{WRAPPER}}' => "--spinner-size: {{SIZE}}{{UNIT}};",
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
					'{{WRAPPER}}' => '--button-loader-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.15.0
	 */
	protected function register_terms_use_controls_style() {
		$this->start_controls_section(
			'section_terms_use_style',
			array(
				'label' => __( 'Terms of Use', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'terms_use' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'terms_use_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--terms-use-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--terms-use-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--terms-use-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--terms-use-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--terms-use-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--terms-use-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--terms-use-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--terms-use-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--terms-use-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'terms_use' => 'yes' ),
			)
		);

		$this->add_control(
			'terms_use_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--terms-use-color: {{VALUE}};',
				),
				'condition' => array( 'terms_use' => 'yes' ),
			)
		);

		$this->add_control(
			'terms_use_hover_color',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--terms-use-hover-color: {{VALUE}};',
				),
				'condition' => array(
					'terms_use' => 'yes',
					'terms_use_url[url]!' => '',
				),
			)
		);

		$this->add_control(
			'terms_use_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default' => array( 'size' => '10' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--terms-use-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'terms_use' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get Custom Border Type Options
	 *
	 * Return a set of border options to be used in different WooCommerce widgets.
	 *
	 * This will be used in cases where the Group Border Control could not be used.
	 *
	 * @since 1.15.0
	 *
	 * @return array
	 */
	public static function get_custom_border_type_options() {
		return array(
			'' => esc_html__( 'Default', 'cmsmasters-elementor' ),
			'none' => esc_html__( 'None', 'cmsmasters-elementor' ),
			'solid' => esc_html__( 'Solid', 'cmsmasters-elementor' ),
			'double' => esc_html__( 'Double', 'cmsmasters-elementor' ),
			'dotted' => esc_html__( 'Dotted', 'cmsmasters-elementor' ),
			'dashed' => esc_html__( 'Dashed', 'cmsmasters-elementor' ),
			'groove' => esc_html__( 'Groove', 'cmsmasters-elementor' ),
		);
	}

	protected function form_field( $field, $type, $label, $placeholder, $name, $unique_id ) {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'field-group', 'class', array(
			$this->get_widget_class() . '__field-group',
			$this->get_widget_class() . '__field-group-' . $type,
		) );

		echo '<div ' . $this->get_render_attribute_string( 'field-group' ) . '>';

			$label_placeholder = ( isset( $settings['label_placeholder'] ) ? $settings['label_placeholder'] : '' );

			if ( 'placeholders' !== $label_placeholder ) {
				echo '<label class="' . $this->get_widget_class() . '__label" for="' . esc_attr( $unique_id ) . '">' .
					esc_html( $label ) .
				'</label>';
			}

			$required = ( isset( $settings[ $field . '_required' ] ) && 'yes' === $settings[ $field . '_required' ] ? true : false );

			echo '<input 
				id="' . esc_attr( $unique_id ) . '" 
				class="' . $this->get_widget_class() . '__field" 
				type="' . esc_attr( $type ) . '" 
				placeholder="' . ( 'label' !== $label_placeholder ? esc_attr( $placeholder ) . ( $required ? '*' : '' ) : '' ) . '" 
				name="' . esc_attr( $name ) . '"' .
				( 'placeholders' === $label_placeholder ? ' aria-label="' . esc_attr( $name ) . '"' : '' ) .
				( $required ? ' required' : '' ) .
				' />';

		echo '</div>';
	}

	/**
	 * Render form button output on the frontend.
	 *
	 * @since 1.15.0
	 *
	 */
	protected function get_button_inner() {
		$settings = $this->get_settings_for_display();

		$button_type = ( isset( $settings['button_type'] ) ? $settings['button_type'] : '' );
		$button_icon = ( isset( $settings['button_icon'] ) ? $settings['button_icon'] : '' );
		$button_text = ( isset( $settings['button_text'] ) && ! empty( $settings['button_text'] ) ? $settings['button_text'] : 'Subscribed' );

		if ( 'text' !== $button_type ) {
			echo '<span class="' . $this->get_widget_class() . '__button-icon">';

			$button_icon_att = array( 'aria-hidden' => 'true' );

			if ( 'icon' === $button_type ) {
				$button_icon_att = array_merge(
					$button_icon_att,
					array( 'aria-label' => 'Submit Button' ),
				);
			}

			if ( '' !== $button_icon['value'] ) {
				Icons_Manager::render_icon( $button_icon, $button_icon_att );
			} else {
				Icons_Manager::render_icon(
					array(
						'value' => 'far fa-envelope',
						'library' => 'fa-regular',
					),
					$button_icon_att
				);
			}

			echo '</span>';
		}

		if ( 'icon' !== $button_type ) {
			echo '<span class="' . $this->get_widget_class() . '__button-text">' .
				esc_html( $button_text ) .
			'</span>';
		}
	}

	/**
	 * Render form button output on the frontend.
	 *
	 * @since 1.15.0
	 *
	 */
	protected function get_terms_use() {
		echo '<div class="' . $this->get_widget_class() . '__terms-use">';

			$this->terms_of_use();

		echo '</div>';
	}

	/**
	 * Get get a fields with a terms of use.
	 *
	 * Retrieve a fields with settings for the terms of use.
	 *
	 * @since 1.15.0
	 *
	 */
	protected function terms_of_use() {
		$settings = $this->get_settings_for_display();

		if ( $settings['terms_use'] ) {

			$terms_id = 'terms-use-' . uniqid( 'terms-use' );

			$this->add_render_attribute(
				array(
					'terms_input' => array(
						'class' => array(
							$this->get_widget_class() . '__check-box',
						),
						'id' => esc_attr( $terms_id ),
						'type' => 'checkbox',
						'name' => 'terms-use',
					),
					'terms_label' => array(
						'class' => array(
							$this->get_widget_class() . '__terms-label',
						),
						'for' => esc_attr( $terms_id ),
					),
					'terms_link' => array(
						'class' => $this->get_widget_class() . '__terms-link',
					),
				)
			);

			echo '<div ' . $this->get_render_attribute_string( 'terms_group' ) . '>';

				$tag = 'span';
				$terms_text = ( empty( $settings['terms_use_text'] ) ? esc_html__( 'Make sure you agree to the terms of service', 'cmsmasters-elementor' ) : esc_html( $settings['terms_use_text'] ) );

				if ( '' !== $settings['terms_use_url']['url'] ) {
					$tag = 'a';

					$this->add_link_attributes( 'terms_link', $settings['terms_use_url'] );
				}

				echo '<div class="' . $this->get_widget_class() . '__terms-wrapper">
					<input ' . $this->get_render_attribute_string( 'terms_input' ) . ' required>
					<label ' . $this->get_render_attribute_string( 'terms_label' ) . '>
						<' . Utils::validate_html_tag( $tag ) . ' ' . $this->get_render_attribute_string( 'terms_link' ) . '>';

							Utils::print_unescaped_internal_string( $terms_text );

						echo '</' . Utils::validate_html_tag( $tag ) . '>
					</label>
				</div>
			</div>';
		}
	}

	/**
	 * Render sender widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.15.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( get_option( 'elementor_sender_api_key' ) ) ) {
			return;
		}

		$simple_form = ( isset( $settings['simple_form'] ) ? $settings['simple_form'] : '' );
		$full_name = ( isset( $settings['full_name'] ) ? $settings['full_name'] : '' );
		$multiple_list = ( isset( $settings['multiple_list'] ) ? $settings['multiple_list'] : array() );
		$simple_form_button_position = ( isset( $settings['simple_form_button_position'] ) ? $settings['simple_form_button_position'] : '' );

		echo '<form class="' . ( $simple_form ? 'simple_form' : 'multiple_form' ) . ( $simple_form_button_position ? ' cmsmasters-form-button-position-' . esc_attr( $simple_form_button_position ) : '' ) . '">' .
			'<div class="' . $this->get_widget_class() . '__field-groups">';

				if ( ! $simple_form && ! empty( $multiple_list ) ) {
					foreach ( $multiple_list as $item ) {
						echo $this->form_field( $item, 'text', 'First name:', 'Your first name', 'name', uniqid( 'text-' ) ); // XSS ok.

						if ( 'name' === $item && 'none' !== $item && $full_name ) {
							echo $this->form_field( $item, 'text', 'Last name:', 'Your last name', 'last-name', uniqid( 'text-' ) ); // XSS ok.
						}
					}
				}

				echo $this->form_field( 'req', 'email', 'Email:', 'Enter your email', 'email', uniqid( 'email-' ) ) . // XSS ok.
			'</div>' .
			'<div class="' . $this->get_widget_class() . '__button-wrap">';

				$this->add_render_attribute(
					'button',
					array(
						'class' => array( $this->get_widget_class() . '__button' ),
						'name' => 'submit',
						'type' => 'submit',
					),
				);

				$button_type = ( isset( $settings['button_type'] ) ? $settings['button_type'] : '' );

				if ( 'icon' === $button_type ) {
					$this->add_render_attribute( 'button', 'aria-label', 'Submit Button' );
				}

				if ( 'yes' === $settings['terms_use'] ) {
					$this->add_render_attribute( 'button', 'disabled', 'disabled' );
				}

				echo '<button ' . $this->get_render_attribute_string( 'button' ) . '>';

					$this->get_button_inner();

					echo '<span class="loader">';

						$icon_loader = ( isset( $settings['icon_loader'] ) ? $settings['icon_loader'] : '' );

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

					echo '</span>' .
				'</button>';

				if ( ! $simple_form || ( $simple_form && 'bottom' === $simple_form_button_position ) ) {
					$this->get_terms_use();
				}

			echo '</div>' .
		'</form>';

		if ( $simple_form && ( 'left' === $simple_form_button_position || 'right' === $simple_form_button_position ) ) {
			$this->get_terms_use();
		}
	}
}
