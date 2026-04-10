<?php
namespace CmsmastersElementor\Modules\TemplateSections\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\TemplateSections\Traits\Site_Widget;
use CmsmastersElementor\Modules\TemplateSections\Widgets\Search_Interface;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon search widget.
 *
 * Addon widget that display site search.
 *
 * @since 1.0.0
*/
class Search extends Base_Widget implements Search_Interface {

	use Site_Widget;

	protected $form_role;
	protected $form_method;
	protected $form_add_class;
	protected $form_action;
	protected $input_type;
	protected $input_value;
	protected $input_name;
	protected $add_input_class;
	protected $custom_atts;

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		$this->form_role = 'search';
		$this->form_method = 'get';
		$this->form_add_class = '';
		$this->form_action = esc_url( home_url( '/' ) );
		$this->input_type = 'search';
		$this->input_name = 's';
		$this->input_value = get_search_query();
		$this->add_input_class = '';
		$this->custom_atts = array();
	}

	/**
	 * Get group widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.11.8
	 *
	 * @return string The widget name.
	 */
	public function get_group_name() {
		return 'cmsmasters-search';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Search', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-search';
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
			'search',
			'find',
			'popup',
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
			'widget-cmsmasters-search',
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
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Deleted default for popup trigger icon position and enabled toggle option for this control.
	 * Moved `Icon Gap` control in content `Popup Trigger` section.
	 * Added background gradient control on normal state for trigger. Added background gradient, `Border Radius`,
	 * `Text Decoration` and `Text Shadow` controls on hover state for trigger.
	 * Added 'em', '%' and 'vw' size units for `Icon Size`, `Padding`, `Gap`, `Top Gap`, `Side Gap`,
	 * `Icon Gap`, `Size` and `Min Size` controls.
	 * Added background gradient control on normal state for submit button. Added background gradient,
	 * `Border Radius` and `Text Decoration` controls on hover state for submit button.
	 * Added `Alignment` control for input field.
	 * @since 1.2.3 Fixed display of the `Padding` control for the button view.
	 * @since 1.3.3 Fixed `Icon Gap` control for icon. Fixed popup trigger alignment on responsive.
	 * @since 1.10.1 Added top icon position in `Icon Position` control.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_general_settings',
			array( 'label' => __( 'General Settings', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'type_of_search',
			array(
				'label' => __( 'Type of Search', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'classic' => __( 'Classic', 'cmsmasters-elementor' ),
					'search-popup' => __( 'Popup', 'cmsmasters-elementor' ),
				),
				'default' => 'classic',
				'label_block' => false,
				'render_type' => 'template',
				'frontend_available' => true,
				'prefix_class' => 'cmsmasters-search-type-',
			)
		);

		$this->add_control(
			'search_placeholder',
			array(
				'label' => __( 'Search Placeholder', 'cmsmasters-elementor' ),
				'placeholder' => __( 'Search...', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_popup_trigger_settings',
			array(
				'label' => __( 'Popup Trigger', 'cmsmasters-elementor' ),
				'condition' => array( 'type_of_search' => 'search-popup' ),
			)
		);

		$this->add_control(
			'popup_trigger_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'link' => array(
						'title' => __( 'Link', 'cmsmasters-elementor' ),
						'description' => 'Trigger is a link',
					),
					'button' => array(
						'title' => __( 'Button', 'cmsmasters-elementor' ),
						'description' => 'Trigger is a button',
					),
				),
				'default' => 'button',
				'label_block' => false,
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-popup-trigger-type-',
				'condition' => array( 'type_of_search' => 'search-popup' ),
			)
		);

		$this->add_control(
			'popup_trigger_content',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'description' => 'Trigger has only icon',
					),
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => 'Trigger has only text',
					),
					'both' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => 'Trigger has icon and text',
					),
				),
				'default' => 'icon',
				'label_block' => false,
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-popup-trigger-content-',
				'condition' => array( 'type_of_search' => 'search-popup' ),
			)
		);

		$this->add_control(
			'popup_trigger_label',
			array(
				'label' => __( 'Trigger Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => 'Search',
				'default' => 'Search',
				'condition' => array(
					'type_of_search' => 'search-popup',
					'popup_trigger_content!' => 'icon',
				),
			)
		);

		$this->add_control(
			'popup_trigger_icon',
			array(
				'label' => __( 'Choose Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'recommended' => array(
					'fa-solid' => array(
						'search',
						'search-dollar',
						'search-location',
						'search-minus',
						'search-plus',
					),
				),
				'label_block' => true,
				'file' => '',
				'condition' => array(
					'type_of_search' => 'search-popup',
					'popup_trigger_content!' => 'text',
				),
			)
		);

		$this->add_control(
			'popup_trigger_icon_position',
			array(
				'label' => __( 'Icon Position', 'cmsmasters-elementor' ),
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
				'prefix_class' => 'cmsmasters-popup-trigger-icon-position-',
				'condition' => array(
					'type_of_search' => 'search-popup',
					'popup_trigger_content' => 'both',
					'popup_trigger_label[value]!' => '',
					'popup_trigger_icon[value]!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'popup_trigger_icon_gap',
			array(
				'label' => __( 'Icon Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-popup-trigger-icon-position-right .elementor-widget-cmsmasters-search__popup-trigger-inner-icon + .elementor-widget-cmsmasters-search__popup-trigger-inner-label' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-popup-trigger-icon-position-left .elementor-widget-cmsmasters-search__popup-trigger-inner-icon + .elementor-widget-cmsmasters-search__popup-trigger-inner-label' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-popup-trigger-icon-position-top .elementor-widget-cmsmasters-search__popup-trigger-inner-icon + .elementor-widget-cmsmasters-search__popup-trigger-inner-label' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'popup_trigger_content' => 'both' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_popup_settings',
			array(
				'label' => __( 'Popup Settings', 'cmsmasters-elementor' ),
				'condition' => array( 'type_of_search' => 'search-popup' ),
			)
		);

		$this->add_control(
			'popup_description',
			array(
				'label' => __( 'Popup Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => array( 'type_of_search' => 'search-popup' ),
			)
		);

		$this->add_control(
			'popup_description_text',
			array(
				'label' => __( 'Popup Description Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Your description...', 'cmsmasters-elementor' ),
				'condition' => array(
					'type_of_search' => 'search-popup',
					'popup_description' => 'yes',
				),
			)
		);

		$this->add_control(
			'overlay_close',
			array(
				'label' => __( 'Close With Click on Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'Close popup upon click/tap on overlay', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'condition' => array( 'type_of_search' => 'search-popup' ),
			)
		);

		$this->add_control(
			'esc_close',
			array(
				'label' => __( 'Close by ESC Button Click', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'disable_scroll',
			array(
				'label' => __( 'Disable scroll', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array( 'type_of_search' => 'search-popup' ),
			)
		);

		$this->add_control(
			'popup_show_effect',
			array(
				'label' => __( 'Show Effect', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'fade' => __( 'Fade', 'cmsmasters-elementor' ),
					'scale' => __( 'Scale', 'cmsmasters-elementor' ),
					'move-up' => __( 'Move Up', 'cmsmasters-elementor' ),
					'move-down' => __( 'Move Down', 'cmsmasters-elementor' ),
				),
				'default' => 'move-up',
				'condition' => array( 'type_of_search' => 'search-popup' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_popup_close_content',
			array(
				'label' => __( 'Popup Close', 'cmsmasters-elementor' ),
				'condition' => array( 'type_of_search' => 'search-popup' ),
			)
		);

		$this->add_control(
			'popup_close_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'description' => 'Close button has only icon',
					),
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => 'Close button has only text',
					),
					'both' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => 'Close button has icon and text',
					),
				),
				'default' => 'icon',
				'label_block' => false,
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'popup_close_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => array( 'title' => __( 'Default', 'cmsmasters-elementor' ) ),
					'stacked' => array( 'title' => __( 'Stacked', 'cmsmasters-elementor' ) ),
					'framed' => array( 'title' => __( 'Framed', 'cmsmasters-elementor' ) ),
				),
				'default' => 'default',
				'label_block' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-popup-close-view-',
			)
		);

		$this->add_control(
			'popup_close_shape',
			array(
				'label' => __( 'Shape', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'square' => array( 'title' => __( 'Square', 'cmsmasters-elementor' ) ),
					'circle' => array( 'title' => __( 'Circle', 'cmsmasters-elementor' ) ),
				),
				'default' => 'square',
				'label_block' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-popup-close-shape-',
				'condition' => array(
					'popup_close_type' => 'icon',
					'popup_close_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'popup_close_icon_position',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'right',
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-popup-close-icon-position-',
			)
		);

		$this->add_control(
			'popup_close_cont_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_control(
			'popup_close_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'recommended' => array(
					'fa-solid' => array(
						'times',
						'times-circle',
						'window-close',
					),
					'fa-regular' => array(
						'times-circle',
					),
				),
				'file' => '',
				'condition' => array( 'popup_close_type!' => 'text' ),
			)
		);

		$this->add_control(
			'popup_close_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Close', 'cmsmasters-elementor' ),
				'condition' => array( 'popup_close_type!' => 'icon' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_submit_button',
			array( 'label' => __( 'Submit Button', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'submit_button_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array(
						'title' => __( 'None', 'cmsmasters-elementor' ),
						'description' => 'Do not show button',
					),
					'link' => array(
						'title' => __( 'Link', 'cmsmasters-elementor' ),
						'description' => 'Button has type icon',
					),
					'button' => array(
						'title' => __( 'Button', 'cmsmasters-elementor' ),
						'description' => 'Button looks like button',
					),
				),
				'default' => 'button',
				'label_block' => false,
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-submit-button-view-',
			)
		);

		$this->add_control(
			'submit_button_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'description' => 'Button has only icon',
					),
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => 'Button has only text',
					),
					'both' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => 'Button has icon and text',
					),
				),
				'default' => 'icon',
				'toggle' => false,
				'label_block' => false,
				'condition' => array( 'submit_button_view' => 'button' ),
			)
		);

		$this->add_control(
			'submit_button_label',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Search', 'cmsmasters-elementor' ),
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_type!' => 'icon',
				),
			)
		);

		$this->add_control(
			'submit_button_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'recommended' => array(
					'fa-solid' => array(
						'search',
						'search-dollar',
						'search-location',
						'search-minus',
						'search-plus',
					),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'submit_button_view',
							'operator' => '=',
							'value' => 'link',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'submit_button_view',
									'operator' => '=',
									'value' => 'button',
								),
								array(
									'name' => 'submit_button_type',
									'operator' => '!==',
									'value' => 'text',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'submit_button_icon_position',
			array(
				'label' => __( 'Icon Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-buttons-icon-position-',
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_type' => 'both',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_popup_trigger_style',
			array(
				'label' => __( 'Popup Trigger', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array( 'type_of_search' => 'search-popup' ),
			)
		);

		$this->add_responsive_control(
			'popup_trigger_alignment',
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
				'selectors_dictionary' => array(
					'left' => 'flex-start;',
					'center' => 'center;',
					'right' => 'flex-end;',
				),
				'default' => 'center',
				'toggle' => false,
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-container' => 'justify-content: {{VALUE}}',
				),
				'condition' => array( 'type_of_search' => 'search-popup' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'popup_trigger_text_typography',
				'fields_options' => array(
					'font_size' => array(
						'selectors' => array(
							'{{WRAPPER}}' => '--popup-trigger-font-size: {{SIZE}}{{UNIT}}',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{WRAPPER}}' => '--popup-trigger-text-decoration: {{VALUE}};',
						),
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner',
				'condition' => array( 'popup_trigger_content!' => 'icon' ),
			)
		);

		$this->start_controls_tabs( 'tabs_popup_trigger_style' );

		$this->start_controls_tab(
			'tab_popup_trigger_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'popup_trigger_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'popup_trigger_bg_background',
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
				'condition' => array( 'popup_trigger_type' => 'button' ),
			)
		);

		$this->add_control(
			'popup_trigger_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner' => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'popup_trigger_type' => 'button',
					'popup_trigger_bg_background' => array(
						'color',
						'gradient',
					),
				),
			)
		);

		$this->add_control(
			'popup_trigger_bg_color_stop',
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
					'popup_trigger_type' => 'button',
					'popup_trigger_bg_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'popup_trigger_bg_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'popup_trigger_type' => 'button',
					'popup_trigger_bg_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'popup_trigger_bg_color_b_stop',
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
					'popup_trigger_type' => 'button',
					'popup_trigger_bg_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'popup_trigger_bg_gradient_type',
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
					'popup_trigger_type' => 'button',
					'popup_trigger_bg_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'popup_trigger_bg_gradient_angle',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner' => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{popup_trigger_bg_color_stop.SIZE}}{{popup_trigger_bg_color_stop.UNIT}}, {{popup_trigger_bg_color_b.VALUE}} {{popup_trigger_bg_color_b_stop.SIZE}}{{popup_trigger_bg_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'popup_trigger_type' => 'button',
					'popup_trigger_bg_background' => array( 'gradient' ),
					'popup_trigger_bg_gradient_type' => 'linear',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'popup_trigger_bg_gradient_position',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner' => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{popup_trigger_bg_color_stop.SIZE}}{{popup_trigger_bg_color_stop.UNIT}}, {{popup_trigger_bg_color_b.VALUE}} {{popup_trigger_bg_color_b_stop.SIZE}}{{popup_trigger_bg_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'popup_trigger_type' => 'button',
					'popup_trigger_bg_background' => array( 'gradient' ),
					'popup_trigger_bg_gradient_type' => 'radial',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'popup_trigger_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'popup_trigger_type' => 'button',
					'popup_trigger_border_border!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'popup_trigger_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'popup_trigger_type' => 'button' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'popup_trigger_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner-label',
				'condition' => array(
					'popup_trigger_type' => 'button',
					'popup_trigger_content!' => 'icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'popup_trigger_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner',
				'condition' => array( 'popup_trigger_type' => 'button' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_popup_trigger_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'popup_trigger_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner:hover' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'popup_trigger_bg_hover_background',
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
				'condition' => array( 'popup_trigger_type' => 'button' ),
			)
		);

		$this->add_control(
			'popup_trigger_bg_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner:hover' => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'popup_trigger_type' => 'button',
					'popup_trigger_bg_hover_background' => array(
						'color',
						'gradient',
					),
				),
			)
		);

		$this->add_control(
			'popup_trigger_bg_hover_color_stop',
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
					'popup_trigger_type' => 'button',
					'popup_trigger_bg_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'popup_trigger_bg_hover_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'popup_trigger_type' => 'button',
					'popup_trigger_bg_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'popup_trigger_bg_hover_color_b_stop',
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
					'popup_trigger_type' => 'button',
					'popup_trigger_bg_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'popup_trigger_bg_hover_gradient_type',
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
					'popup_trigger_type' => 'button',
					'popup_trigger_bg_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'popup_trigger_bg_hover_gradient_angle',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner:hover' => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{popup_trigger_bg_hover_color_stop.SIZE}}{{popup_trigger_bg_hover_color_stop.UNIT}}, {{popup_trigger_bg_hover_color_b.VALUE}} {{popup_trigger_bg_hover_color_b_stop.SIZE}}{{popup_trigger_bg_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'popup_trigger_type' => 'button',
					'popup_trigger_bg_hover_background' => array( 'gradient' ),
					'popup_trigger_bg_hover_gradient_type' => 'linear',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'popup_trigger_bg_hover_gradient_position',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner:hover' => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{popup_trigger_bg_hover_color_stop.SIZE}}{{popup_trigger_bg_hover_color_stop.UNIT}}, {{popup_trigger_bg_hover_color_b.VALUE}} {{popup_trigger_bg_hover_color_b_stop.SIZE}}{{popup_trigger_bg_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'popup_trigger_type' => 'button',
					'popup_trigger_bg_hover_background' => array( 'gradient' ),
					'popup_trigger_bg_hover_gradient_type' => 'radial',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'popup_trigger_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner:hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'popup_trigger_type' => 'button',
					'popup_trigger_border_border!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'popup_trigger_border_radius_hover',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'popup_trigger_type' => 'button' ),
			)
		);

		$this->add_control(
			'popup_trigger_text_decoration_hover',
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
				'selectors' => array(
					'{{WRAPPER}}' => '--popup-trigger-hover-text-decoration: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'popup_trigger_text_shadow_hover',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner:hover .elementor-widget-cmsmasters-search__popup-trigger-inner-label',
				'condition' => array(
					'popup_trigger_type' => 'button',
					'popup_trigger_content!' => 'icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'popup_trigger_box_shadow_hover',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner:hover',
				'condition' => array( 'popup_trigger_type' => 'button' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'popup_trigger_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_responsive_control(
			'popup_trigger_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'popup_trigger_content!' => 'text' ),
			)
		);

		$this->add_responsive_control(
			'popup_trigger_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'popup_trigger_type' => 'button',
					'popup_trigger_content!' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'popup_trigger_icon_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner' => 'padding: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'popup_trigger_type' => 'button',
					'popup_trigger_content' => 'icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'popup_trigger_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'none' => __( 'None', 'cmsmasters-elementor' ),
							'default' => __( 'Default', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => 'default',
					),
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array(
								'none',
								'default',
							),
						),
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-trigger-inner',
				'condition' => array( 'popup_trigger_type' => 'button' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_popup_content_style',
			array(
				'label' => __( 'Popup Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array( 'type_of_search' => 'search-popup' ),
			)
		);

		$this->add_control(
			'popup_content_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-search-form-full-screen .elementor-widget-cmsmasters-search__popup-container' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'popup_content_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 200,
						'max' => 1000,
					),
					'%' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-content' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'popup_content_vertical_alignment',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Middle', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'default' => 'center',
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-search-form-full-screen .elementor-widget-cmsmasters-search__popup-container' => 'align-items: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'popup_content_vertical_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'allowed_dimensions' => 'vertical',
				'placeholder' => array(
					'top' => '',
					'right' => 'auto',
					'bottom' => '',
					'left' => 'auto',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-search-form-full-screen .elementor-widget-cmsmasters-search__popup-container .elementor-widget-cmsmasters-search__popup-content' => 'margin-top: {{TOP}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				),
				'condition' => array( 'popup_content_vertical_alignment!' => 'center' ),
			)
		);

		$this->add_control(
			'popup_content_description',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'popup_description' => 'yes' ),
			)
		);

		$this->add_control(
			'popup_content_description_align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'left',
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-description' => 'text-align: {{VALUE}}',
				),
				'condition' => array( 'popup_description' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'popup_content_description_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-description',
				'condition' => array( 'popup_description' => 'yes' ),
			)
		);

		$this->add_control(
			'popup_content_description_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-description' => 'color: {{VALUE}}',
				),
				'condition' => array( 'popup_description' => 'yes' ),
			)
		);

		$this->add_control(
			'popup_content_description_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-description' => 'background-color: {{VALUE}}',
				),
				'condition' => array( 'popup_description' => 'yes' ),
			)
		);

		$this->add_control(
			'popup_content_description_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-description' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'popup_description' => 'yes',
					'popup_content_description_border_border!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'popup_content_description_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'popup_description' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'popup_content_description_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-description' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'popup_description' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'popup_content_description_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'exclude' => array( 'color' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-description',
				'condition' => array( 'popup_description' => 'yes' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_popup_close_style',
			array(
				'label' => __( 'Popup Close', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array( 'type_of_search' => 'search-popup' ),
			)
		);

		$this->add_responsive_control(
			'popup_close_top_gap',
			array(
				'label' => __( 'Top Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'popup_close_side_gap',
			array(
				'label' => __( 'Side Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-popup-close-icon-position-right .elementor-widget-cmsmasters-search__popup-close' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-popup-close-icon-position-left .elementor-widget-cmsmasters-search__popup-close' => 'left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'popup_close_icon_position!' => 'center' ),
			)
		);

		$this->start_controls_tabs( 'tabs_popup_close_style' );

		$this->start_controls_tab(
			'tab_popup_close_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'popup_close_color',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}}.cmsmasters-popup-close-view-framed .elementor-widget-cmsmasters-search__popup-close' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'popup_close_bg_color',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close' => 'background-color: {{VALUE}}',
				),
				'condition' => array( 'popup_close_view!' => 'default' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_popup_close_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'popup_close_color_hover',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close:hover svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}}.cmsmasters-popup-close-view-framed .elementor-widget-cmsmasters-search__popup-close:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'popup_close_bg_color_hover',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close:hover' => 'background-color: {{VALUE}}',
				),
				'condition' => array( 'popup_close_view!' => 'default' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'popup_close_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'popup_close_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close',
				'condition' => array( 'popup_close_type!' => 'icon' ),
			)
		);

		$this->add_responsive_control(
			'popup_close_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'popup_close_type!' => 'text' ),
			)
		);

		$this->add_responsive_control(
			'popup_close_icon_gap',
			array(
				'label' => __( 'Icon Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close-icon + .elementor-widget-cmsmasters-search__popup-close-label' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'popup_close_type' => 'both' ),
			)
		);

		$this->add_responsive_control(
			'popup_close_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'popup_close_view!' => 'default',
					'popup_close_shape' => 'square',
				),
			)
		);

		$this->add_responsive_control(
			'popup_close_icon_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close' => 'padding: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'popup_close_type' => 'icon',
					'popup_close_view!' => 'default',
					'popup_close_shape' => 'circle',
				),
			)
		);

		$this->add_control(
			'popup_close_framed_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'popup_close_view' => 'framed' ),
			)
		);

		$this->add_control(
			'popup_close_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'popup_close_view!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'popup_close_box_shadow',
				'exclude' => array( 'box_shadow_position' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__popup-close',
				'condition' => array( 'popup_close_view!' => 'default' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_input_field_style',
			array(
				'label' => __( 'Input Field', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'input_field_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__field',
			)
		);

		$this->add_responsive_control(
			'input_field_alignment',
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
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-input-field%s-alignment-',
			)
		);

		$this->start_controls_tabs( 'input_field_tabs' );

		$this->start_controls_tab(
			'input_field_tab_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'input_field_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field::-webkit-input-placeholder' => 'color: {{VALUE}}; opacity: 0.6;',
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field::-moz-placeholder' => 'color: {{VALUE}}; opacity: 0.6;',
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field:-ms-input-placeholder' => 'color: {{VALUE}}; opacity: 0.6;',
				),
			)
		);

		$this->add_control(
			'input_field_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'input_field_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field' => 'border-color: {{VALUE}}',
				),
				'condition' => array( 'input_field_border_border!' => 'none' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'input_field_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__field',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'input_field_tab_focus',
			array( 'label' => __( 'Focus', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'input_field_color_focus',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field:focus' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field:focus::-webkit-input-placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field:focus::-moz-placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field:focus:-ms-input-placeholder' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'input_field_bg_color_focus',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field:focus' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'input_field_border_color_focus',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field:focus' => 'border-color: {{VALUE}}',
				),
				'condition' => array( 'input_field_border_border!' => 'none' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'input_field_box_shadow_focus',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__field:focus',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'input_field_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'none' => __( 'None', 'cmsmasters-elementor' ),
							'default' => __( 'Default', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => 'default',
					),
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array(
								'none',
								'default',
							),
						),
					),
				),
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__field',
			)
		);

		$this->add_control(
			'input_field_placeholder_color',
			array(
				'label' => __( 'Placeholder Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field::-webkit-input-placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field::-moz-placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field:-ms-input-placeholder' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'input_field_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--input-field-padding-top: {{TOP}}{{UNIT}}; --input-field-padding-right: {{RIGHT}}{{UNIT}}; --input-field-padding-bottom: {{BOTTOM}}{{UNIT}}; --input-field-padding-left: {{LEFT}}{{UNIT}};',
					'.rtl {{WRAPPER}}' => '--input-field-padding-top: {{TOP}}{{UNIT}}; --input-field-padding-right: {{LEFT}}{{UNIT}}; --input-field-padding-bottom: {{BOTTOM}}{{UNIT}}; --input-field-padding-left: {{RIGHT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'input_field_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__field,
					{{WRAPPER}} .elementor-widget-cmsmasters-search__field:hover,
					{{WRAPPER}} .elementor-widget-cmsmasters-search__field:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'form_input_show_icon',
			array(
				'label' => __( 'Input Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'no',
				'render_type' => 'template',
				'separator' => 'before',
				'prefix_class' => 'cmsmasters-form-input-icon-',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_input_icon_style',
			array(
				'label' => __( 'Input Icon', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array( 'form_input_show_icon' => 'yes' ),
			)
		);

		$this->add_control(
			'form_input_icon',
			array(
				'label' => __( 'Choose Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'recommended' => array(
					'fa-solid' => array(
						'search',
						'search-dollar',
						'search-location',
						'search-minus',
						'search-plus',
					),
				),
				'condition' => array( 'form_input_show_icon' => 'yes' ),
			)
		);

		$this->add_control(
			'form_input_icon_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__form-input-icon' => 'color: {{VALUE}}',
				),
				'condition' => array( 'form_input_show_icon' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'form_input_icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--form-input-icon-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'form_input_show_icon' => 'yes',
					'form_input_icon[value]!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'form_input_icon_side_gap',
			array(
				'label' => __( 'Side Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--form-input-icon-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'form_input_show_icon' => 'yes',
					'form_input_icon[value]!' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_submit_button_style',
			array(
				'label' => __( 'Submit Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array( 'submit_button_view!' => 'none' ),
			)
		);

		$this->add_responsive_control(
			'submit_button_button_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit' => 'margin-left: {{SIZE}}{{UNIT}};',
					'.rtl {{WRAPPER}} .elementor-widget-cmsmasters-search__submit' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: 0;',
				),
				'condition' => array( 'submit_button_view' => 'button' ),
			)
		);

		$this->add_responsive_control(
			'submit_button_link_side_gap',
			array(
				'label' => __( 'Side Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' => '--submit-button-link-side-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'submit_button_view' => 'link' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'submit_button_typography',
				'fields_options' => array(
					'font_size' => array(
						'selectors' => array(
							'{{WRAPPER}}' => '--submit-button-font-size: {{SIZE}}{{UNIT}}',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{WRAPPER}}' => '--submit-button-text-decoration: {{VALUE}};',
						),
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__submit',
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_type!' => 'icon',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_submit_button_style' );

		$this->start_controls_tab(
			'tabs_submit_button_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'submit_button_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit,
					{{WRAPPER}} .elementor-widget-cmsmasters-search__form-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_button_bg_background',
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
				'condition' => array( 'submit_button_view' => 'button' ),
			)
		);

		$this->add_control(
			'submit_button_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:before, ' .
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:after' => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_bg_background' => array(
						'color',
						'gradient',
					),
				),
			)
		);

		$this->add_control(
			'submit_button_bg_color_stop',
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
					'submit_button_view' => 'button',
					'submit_button_bg_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'submit_button_bg_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_bg_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'submit_button_bg_color_b_stop',
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
					'submit_button_view' => 'button',
					'submit_button_bg_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'submit_button_bg_gradient_type',
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
					'submit_button_view' => 'button',
					'submit_button_bg_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'submit_button_bg_gradient_angle',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:before, ' .
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:after' => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{submit_button_bg_color_stop.SIZE}}{{submit_button_bg_color_stop.UNIT}}, {{submit_button_bg_color_b.VALUE}} {{submit_button_bg_color_b_stop.SIZE}}{{submit_button_bg_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_bg_background' => array( 'gradient' ),
					'submit_button_bg_gradient_type' => 'linear',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'submit_button_bg_gradient_position',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:before, ' .
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:after' => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{submit_button_bg_color_stop.SIZE}}{{submit_button_bg_color_stop.UNIT}}, {{submit_button_bg_color_b.VALUE}} {{submit_button_bg_color_b_stop.SIZE}}{{submit_button_bg_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_bg_background' => array( 'gradient' ),
					'submit_button_bg_gradient_type' => 'radial',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'submit_button_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_border_border!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'submit_button_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'submit_button_view' => 'button' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'submit_button_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__submit-label,
					{{WRAPPER}} .elementor-widget-cmsmasters-search__form-icon',
				'fields_options' => array(
					'text_shadow_type' => array( 'label' => __( 'Text Shadow', 'cmsmasters-elementor' ) ),
				),
				'condition' => array( 'submit_button_view' => 'button' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'submit_button_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__submit',
				'condition' => array( 'submit_button_view' => 'button' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_submit_button_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'submit_button_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:hover,
					{{WRAPPER}} .elementor-widget-cmsmasters-search__form-icon:hover' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_button_bg_hover_background',
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
				'condition' => array( 'submit_button_view' => 'button' ),
			)
		);

		$this->add_control(
			'submit_button_bg_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:hover:after' => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_bg_hover_background' => array(
						'color',
						'gradient',
					),
				),
			)
		);

		$this->add_control(
			'submit_button_bg_hover_color_stop',
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
					'submit_button_view' => 'button',
					'submit_button_bg_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'submit_button_bg_hover_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_bg_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'submit_button_bg_hover_color_b_stop',
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
					'submit_button_view' => 'button',
					'submit_button_bg_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'submit_button_bg_hover_gradient_type',
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
					'submit_button_view' => 'button',
					'submit_button_bg_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'submit_button_bg_hover_gradient_angle',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:hover:after' => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{submit_button_bg_hover_color_stop.SIZE}}{{submit_button_bg_hover_color_stop.UNIT}}, {{submit_button_bg_hover_color_b.VALUE}} {{submit_button_bg_hover_color_b_stop.SIZE}}{{submit_button_bg_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_bg_hover_background' => array( 'gradient' ),
					'submit_button_bg_hover_gradient_type' => 'linear',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'submit_button_bg_hover_gradient_position',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:hover:after' => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{submit_button_bg_hover_color_stop.SIZE}}{{submit_button_bg_hover_color_stop.UNIT}}, {{submit_button_bg_hover_color_b.VALUE}} {{submit_button_bg_hover_color_b_stop.SIZE}}{{submit_button_bg_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_bg_hover_background' => array( 'gradient' ),
					'submit_button_bg_hover_gradient_type' => 'radial',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'submit_button_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_border_border!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'submit_button_border_radius_hover',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'submit_button_view' => 'button' ),
			)
		);

		$this->add_control(
			'submit_button_text_decoration_hover',
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
				'selectors' => array(
					'{{WRAPPER}}' => '--submit-button-hover-text-decoration: {{VALUE}};',
				),
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_type!' => 'icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'submit_button_text_shadow_hover',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:hover .elementor-widget-cmsmasters-search__submit-label,
					{{WRAPPER}} .elementor-widget-cmsmasters-search__form-icon:hover',
				'fields_options' => array(
					'text_shadow_type' => array( 'label' => __( 'Text Shadow', 'cmsmasters-elementor' ) ),
				),
				'condition' => array( 'submit_button_view' => 'button' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'submit_button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:hover',
				'condition' => array( 'submit_button_view' => 'button' ),
			)
		);

		$this->add_control(
			'submit_button_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit, ' .
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:before, ' .
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit:after, ' .
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__form-icon' => 'transition-duration: {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'submit_button_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'submit_button_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'none' => __( 'None', 'cmsmasters-elementor' ),
							'default' => __( 'Default', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => 'default',
					),
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array(
								'none',
								'default',
							),
						),
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-search__submit',
				'condition' => array( 'submit_button_view' => 'button' ),
			)
		);

		$this->add_responsive_control(
			'submit_button_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-search__submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'submit_button_view' => 'button' ),
			)
		);

		$this->add_responsive_control(
			'submit_button_size',
			array(
				'label' => __( 'Min Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-buttons-type-icon .elementor-widget-cmsmasters-search__submit' => 'min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_type' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'submit_button_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' => '--submit-button-icon-size: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'submit_button_view',
							'operator' => '=',
							'value' => 'link',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'submit_button_view',
									'operator' => '=',
									'value' => 'button',
								),
								array(
									'name' => 'submit_button_type',
									'operator' => '!==',
									'value' => 'text',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'submit_button_icon_gap',
			array(
				'label' => __( 'Icon Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-buttons-icon-position-right .elementor-widget-cmsmasters-search__submit-label' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}:not(.cmsmasters-buttons-icon-position-right) .elementor-widget-cmsmasters-search__submit-label' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'submit_button_view' => 'button',
					'submit_button_type' => 'both',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render search widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		echo '<div class="elementor-widget-cmsmasters-search__container">';

		if ( 'search-popup' === $settings['type_of_search'] ) {
			echo $this->get_popup_search();
		} else {
			echo $this->get_form_search();
		}

		echo '</div>';
	}

	/**
	 * Render popup search container.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_popup_search() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'search-popup-container', 'class', array(
			'elementor-widget-cmsmasters-search__popup-container',
			sprintf( 'cmsmasters-search-popup-%s-effect', $settings['popup_show_effect'] ),
		) );

		if ( 'yes' === $settings['overlay_close'] ) {
			$this->add_render_attribute( 'search-popup-container', 'class', 'cmsmasters-overlay-close' );
		}

		if ( 'yes' === $settings['disable_scroll'] ) {
			$this->add_render_attribute( 'search-popup-container', 'class', 'cmsmasters-disabled-scroll' );
		}

		echo '<div ' . $this->get_render_attribute_string( 'search-popup-container' ) . '>';

			$this->get_popup_close();

			echo '<div class="elementor-widget-cmsmasters-search__popup-content">';
				$this->get_form_search();

		if ( 'yes' === $settings['popup_description'] ) {
			echo '<div class="elementor-widget-cmsmasters-search__popup-description">' .
				esc_html( $settings['popup_description_text'] ) .
			'</div>';
		}

			echo '</div>';

		echo '</div>';

		$this->get_popup_trigger();
	}

	/**
	 * Render search form.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_form_search() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'search-from', array(
			'role' => $this->form_role,
			'method' => $this->form_method,
			'class' => "elementor-widget-cmsmasters-search__form {$this->form_add_class}",
			'action' => $this->form_action,
		) );

		$submit_button_view = $settings['submit_button_view'];

		$this->add_render_attribute( 'search-form-container', 'class', array(
			'elementor-widget-cmsmasters-search__form-container',
			'cmsmasters-submit-button-view-' . $submit_button_view,
		) );

		if ( 'button' === $submit_button_view ) {
			$this->add_render_attribute( 'search-form-container', 'class', 'cmsmasters-buttons-type-' . $settings['submit_button_type'] );
		}

		echo '<form ' . $this->get_render_attribute_string( 'search-from' ) . '>' .
			'<div ' . $this->get_render_attribute_string( 'search-form-container' ) . '>';
				$this->get_search_fields();
				$this->get_submit_button();
			echo '</div>' .
		'</form>';
	}

	public function get_search_fields() {
		$settings = $this->get_settings_for_display();

		$this->input_value = get_search_query();
		$this->input_type = 'search';
		$this->input_name = 's';

		$input_uniqid = uniqid( 'search-field-' );

		$this->add_render_attribute( 'search-field', array(
			'type' => $this->input_type,
			'id' => esc_attr( $input_uniqid ),
			'class' => "elementor-widget-cmsmasters-search__field {$this->add_input_class}",
			'value' => $this->input_value,
			'name' => $this->input_name,
		) );

		if ( ! empty( $this->custom_atts ) && is_array( $this->custom_atts ) ) {
			foreach ( $this->custom_atts as $key => $value ) {
				$this->add_render_attribute( 'search-field', $key, $value );
			}
		}

		if ( 'yes' === $settings['form_input_show_icon'] ) {
			echo '<div class="elementor-widget-cmsmasters-search__form-input-icon-container">' .
				'<span class="elementor-widget-cmsmasters-search__form-input-icon">';

			$form_input_icon_att = array(
				'aria-hidden' => 'true',
				'aria-label' => 'Input Icon',
			);

			$form_input_icon = ( isset( $settings['form_input_icon'] ) ? $settings['form_input_icon'] : '' );

			if ( '' !== $form_input_icon['value'] ) {
				Icons_Manager::render_icon( $form_input_icon, $form_input_icon_att );
			} else {
				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-search',
						'library' => 'fa-solid',
					),
					$form_input_icon_att
				);
			}

				echo '</span>';
		}

		if ( '' !== $settings['search_placeholder'] ) {
			$search_placeholder = esc_attr( $settings['search_placeholder'] );
		} else {
			$search_placeholder = esc_attr__( 'Search...', 'cmsmasters-elementor' );
		}

				$this->add_render_attribute( 'search-field', 'placeholder', $search_placeholder );

				echo '<label for="' . esc_attr( $input_uniqid ) . '" class="screen-reader-text">Search for:</label>';

				echo '<input ' . $this->get_render_attribute_string( 'search-field' ) . ' />';

		if ( 'yes' === $settings['form_input_show_icon'] ) {
			echo '</div>';
		}
	}

	/**
	 * Render submit_button.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_submit_button( $loader = '' ) {
		$settings = $this->get_settings_for_display();

		$submit_button_view = ( isset( $settings['submit_button_view'] ) ? $settings['submit_button_view'] : '' );
		$submit_button_type = ( isset( $settings['submit_button_type'] ) ? $settings['submit_button_type'] : '' );
		$submit_button_icon_att = array( 'aria-hidden' => 'true' );
		$submit_button_icon = ( isset( $settings['submit_button_icon'] ) ? $settings['submit_button_icon'] : '' );

		$this->add_render_attribute( 'cmsmasters-search-submit', 'type', 'submit' );

		if ( 'icon' === $submit_button_type || 'link' === $submit_button_view ) {
			$this->add_render_attribute( 'cmsmasters-search-submit', 'aria-label', 'Submit Button' );
		}

		if ( 'button' === $submit_button_view ) {
			$this->add_render_attribute( 'cmsmasters-search-submit', 'class', 'elementor-widget-cmsmasters-search__submit' );

			echo '<button ' . $this->get_render_attribute_string( 'cmsmasters-search-submit' ) . '>';

			if ( '' !== $loader ) {
				Utils::print_unescaped_internal_string( $loader );
			}

			if ( 'text' !== $submit_button_type ) {
				echo '<span class="elementor-widget-cmsmasters-search__submit-icon">';

				if ( ! empty( $submit_button_icon['value'] ) ) {
					Icons_Manager::render_icon( $submit_button_icon, $submit_button_icon_att );
				} else {
					Icons_Manager::render_icon(
						array(
							'value' => 'fas fa-search',
							'library' => 'fa-solid',
						),
						$submit_button_icon_att
					);
				}

				echo '</span>';
			}

			if ( 'icon' !== $submit_button_type ) {
				echo '<span class="elementor-widget-cmsmasters-search__submit-label">';

				$submit_button_label = ( isset( $settings['submit_button_label'] ) ? $settings['submit_button_label'] : '' );

				if ( '' !== $submit_button_label ) {
					echo esc_attr( $submit_button_label );
				} else {
					echo esc_attr__( 'Search', 'cmsmasters-elementor' );
				}

				echo '</span>';
			}

			echo '</button>';
		}

		if ( 'link' === $submit_button_view ) {
			$this->add_render_attribute( 'cmsmasters-search-submit', 'class', 'elementor-widget-cmsmasters-search__form-icon' );

			echo '<button ' . $this->get_render_attribute_string( 'cmsmasters-search-submit' ) . '>';

				if ( '' !== $loader ) {
					Utils::print_unescaped_internal_string( $loader );
				}

				if ( '' !== $submit_button_icon['value'] ) {
					Icons_Manager::render_icon( $submit_button_icon, $submit_button_icon_att );
				} else {
					Icons_Manager::render_icon(
						array(
							'value' => 'fas fa-search',
							'library' => 'fa-solid',
						),
						$submit_button_icon_att
					);
				}

			echo '</button>';
		}
	}

	/**
	 * Render popup close button.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.17.3 Added title attr.
	 */
	public function get_popup_close() {
		$settings = $this->get_settings_for_display();

		echo '<span class="elementor-widget-cmsmasters-search__popup-close" role="button" title="Popup close" tabindex="0">';

		$popup_close_type = ( isset( $settings['popup_close_type'] ) ? $settings['popup_close_type'] : '' );

		if ( 'text' !== $popup_close_type ) {
			echo '<span class="elementor-widget-cmsmasters-search__popup-close-icon">';

			$popup_close_icon_att = array( 'aria-hidden' => 'true' );

			if ( 'icon' === $popup_close_type ) {
				$popup_close_icon_att = array_merge(
					$popup_close_icon_att,
					array( 'aria-label' => 'Popup Close' ),
				);
			}

			$popup_close_icon = ( isset( $settings['popup_close_icon'] ) ? $settings['popup_close_icon'] : '' );

			if ( '' !== $popup_close_icon['value'] ) {
				Icons_Manager::render_icon( $popup_close_icon, $popup_close_icon_att );
			} else {
				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-times',
						'library' => 'fa-solid',
					),
					$popup_close_icon_att
				);
			}

			echo '</span>';
		}

		if ( 'icon' !== $popup_close_type ) {
			echo '<span class="elementor-widget-cmsmasters-search__popup-close-label">';

			$popup_close_text = ( isset( $settings['popup_close_text'] ) ? $settings['popup_close_text'] : '' );

			if ( '' !== $popup_close_text ) {
				echo esc_html( $popup_close_text );
			} else {
				echo esc_html__( 'Close', 'cmsmasters-elementor' );
			}

			echo '</span>';
		}

		echo '</span>';
	}

	/**
	 * Render popup trigger.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.17.3 Added title attr.
	 */
	public function get_popup_trigger() {
		$settings = $this->get_settings_for_display();

		echo '<div class="elementor-widget-cmsmasters-search__popup-trigger-container">' .
			'<div class="elementor-widget-cmsmasters-search__popup-trigger-inner" role="button" title="Search popup trigger" tabindex="0">';

		$popup_trigger_content = ( isset( $settings['popup_trigger_content'] ) ? $settings['popup_trigger_content'] : '' );

		if ( 'text' !== $popup_trigger_content ) {
			echo '<span class="elementor-widget-cmsmasters-search__popup-trigger-inner-icon">';

			$popup_trigger_icon_att = array( 'aria-hidden' => 'true' );

			if ( 'icon' === $popup_trigger_content ) {
				$popup_trigger_icon_att = array_merge(
					$popup_trigger_icon_att,
					array( 'aria-label' => 'Popup Trigger Button' ),
				);
			}

			$popup_trigger_icon = ( isset( $settings['popup_trigger_icon'] ) ? $settings['popup_trigger_icon'] : '' );

			if ( '' !== $popup_trigger_icon['value'] ) {
				Icons_Manager::render_icon( $popup_trigger_icon, $popup_trigger_icon_att );
			} else {
				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-search',
						'library' => 'fa-solid',
					),
					$popup_trigger_icon_att
				);
			}

			echo '</span>';
		}

		if ( 'icon' !== $popup_trigger_content ) {
			echo '<span class="elementor-widget-cmsmasters-search__popup-trigger-inner-label">';

			$popup_trigger_label = ( isset( $settings['popup_trigger_label'] ) ? $settings['popup_trigger_label'] : '' );

			if ( '' !== $popup_trigger_label ) {
				echo esc_html( $popup_trigger_label );
			} else {
				echo esc_html__( 'Search', 'cmsmasters-elementor' );
			}

			echo '</span>';
		}

			echo '</div>' .
		'</div>';
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
	 * Render search widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 */
	protected function content_template() {
		?>
		<div class="elementor-widget-cmsmasters-search__container"><#
			if ( 'search-popup' === settings.type_of_search ) {
				#><?php $this->get_popup_search_template(); ?><#
			}

			if ( 'search-popup' !== settings.type_of_search ) {
				#><?php $this->get_form_search_template(); ?><#
			}
		#></div>
		<?php
	}

	/**
	 * Get popup search template.
	 *
	 * Retrieves popup search admin js template part.
	 *
	 * @since 1.0.0
	 * @since 1.0.2 Fixed template output.
	 * @since 1.19.3 Fixed adding class for popup effect.
	 */
	protected function get_popup_search_template() {
		?>
		<#
		view.addRenderAttribute( 'search-popup-container', 'class', [
			'elementor-widget-cmsmasters-search__popup-container',
			`cmsmasters-search-popup-${ settings.popup_show_effect }-effect`
		] );

		if ( 'yes' === settings.overlay_close ) {
			view.addRenderAttribute( 'search-popup-container', 'class', 'cmsmasters-overlay-close' );
		}

		if ( 'yes' === settings.disable_scroll ) {
			view.addRenderAttribute( 'search-popup-container', 'class', 'cmsmasters-disabled-scroll' );
		}

		#>
		<div {{{ view.getRenderAttributeString( 'search-popup-container' ) }}}>
			<div class="elementor-widget-cmsmasters-search__popup-content">
				<?php $this->get_form_search_template(); ?>
			<#
				if ( 'yes' === settings.popup_description ) {
					#><div class="elementor-widget-cmsmasters-search__popup-description">
						{{{ settings.popup_description_text }}}
					</div><#
				}
			#>
			</div>

			<?php
			$this->get_popup_close_template();
			?>
		</div>

		<?php
		$this->get_popup_trigger_template();
	}

	public function get_form_search_template() {
		?>
		<#
		view.addRenderAttribute( 'search-from', {
			'role': 'search',
			'method': 'get',
			'class': 'elementor-widget-cmsmasters-search__form',
			'action': '<?php esc_url( home_url( '/' ) ); ?>',
		} );

		var $submit_button_view = settings.submit_button_view;

		view.addRenderAttribute( 'search-form-container', 'class', [
			'elementor-widget-cmsmasters-search__form-container',
			'cmsmasters-submit-button-view-' . $submit_button_view,
		] );

		if ( 'button' === $submit_button_view ) {
			view.addRenderAttribute( 'search-form-container', 'class', 'cmsmasters-buttons-type-' + settings.submit_button_type );
		}

		#><form {{{ view.getRenderAttributeString( 'search-from' ) }}}>
			<div {{{ view.getRenderAttributeString( 'search-form-container' ) }}}><#
				view.addRenderAttribute( 'search-field', {
					'type': 'search',
					'id': 'search-field',
					'class': 'elementor-widget-cmsmasters-search__field',
					'value': '<?php echo get_search_query(); ?>',
					'name': 's',
					'aria-hidden': 'true',
					'aria-label': 'Input Icon',
				} );

				if ( 'yes' === settings.form_input_show_icon ) {
					#><div class="elementor-widget-cmsmasters-search__form-input-icon-container">
						<span class="elementor-widget-cmsmasters-search__form-input-icon"><#

							var formInputIcon = settings.form_input_icon;

							if ( '' !== formInputIcon.value ) {
								if ( 'svg' !== formInputIcon.library ) {
									#><i {{{ view.getRenderAttributeString( 'search-field' ) }}}></i><#
								} else {
									#>{{{ elementor.helpers.renderIcon( view, formInputIcon, { 'aria-hidden': true, 'aria-label': 'Input Icon' } ).value }}}<#
								}
							} else {
								#><i class="fas fa-search" aria-hidden="true" aria-label="Input Icon"></i><#
							}

						#></span><#
				}

				var $search_placeholder = '';

				if ( '' !== settings.search_placeholder ) {
					$search_placeholder += settings.search_placeholder;
				} else {
					$search_placeholder += 'Search...';
				}

				view.addRenderAttribute( 'search-field', 'placeholder', $search_placeholder );

				#><label for="search-field" class="screen-reader-text">Search for:</label><#

				#><input {{{ view.getRenderAttributeString( 'search-field' ) }}}><#

				if ( 'yes' === settings.form_input_show_icon ) {
					#></div><#
				}#>

				<?php
				$this->get_submit_button_template();
				?>
			</div>
		</form>
		<?php
	}

	protected function get_submit_button_template() {
		?>
		<#
		var $submit_button_icon = settings.submit_button_icon;

		view.addRenderAttribute( 'submit-button-icon', 'class', [
			$submit_button_icon.value,
		] );

		view.addRenderAttribute( 'submit-button-icon', {
			'aria-hidden': 'true',
		} );

		var $submit_button_view = settings.submit_button_view;

		if ( 'icon' === settings.submit_button_view || 'link' === $submit_button_view ) {
			view.addRenderAttribute( 'submit-button-icon', 'aria-label', 'Submit Button' );
		}

		iconHTML = elementor.helpers.renderIcon( view, $submit_button_icon );

		if ( 'button' === $submit_button_view ) {
			#><button type="submit" class="elementor-widget-cmsmasters-search__submit"><#

			var $submit_button_type = settings.submit_button_type;

			if ( 'text' !== $submit_button_type ) {
				#><span class="elementor-widget-cmsmasters-search__submit-icon"><#

				if ( '' !== $submit_button_icon.value ) {
					if ( 'svg' !== $submit_button_icon.library ) {
						#><i {{{ view.getRenderAttributeString( 'submit-button-icon' ) }}}></i><#
					} else {
						if ( 'icon' === $submit_button_type ) {
							#>{{{ elementor.helpers.renderIcon( view, $submit_button_icon, { 'aria-hidden': true, 'aria-label': 'Submit Button' } ).value }}}<#
						} else {
							#>{{{ elementor.helpers.renderIcon( view, $submit_button_icon, { 'aria-hidden': true } ).value }}}<#
						}
					}
				} else {
					if ( 'icon' === $submit_button_type ) {
						#><i class="elementor-widget-cmsmasters-search__submit-icon fas fa-search" aria-hidden="true" aria-label="Submit Button"></i><#
					} else {
						#><i class="elementor-widget-cmsmasters-search__submit-icon fas fa-search" aria-hidden="true"></i><#
					}
				}

				#></span><#
			}

			if ( 'icon' !== $submit_button_type ) { 
				#><span class="elementor-widget-cmsmasters-search__submit-label"><#

					var $submit_button_label = settings.submit_button_label;

					if ( '' !== $submit_button_label ) {
						#>{{{$submit_button_label}}}<#
					} else {
						#>Search<#
					}

				#></span><#
			} 

			#></button><#
		}

		if ( 'link' === $submit_button_view ) {
			#><button type="submit" class="elementor-widget-cmsmasters-search__form-icon"><#

			if ( '' !== $submit_button_icon.value ) {
				if ( 'svg' !== $submit_button_icon.library ) {
					#><i {{{ view.getRenderAttributeString( 'submit-button-icon' ) }}}></i><#
				} else {
					#>{{{ elementor.helpers.renderIcon( view, $submit_button_icon, { 'aria-hidden': true, 'aria-label': 'Submit Button' } ).value }}}<#
				}
			} else {
				#><i class="elementor-widget-cmsmasters-search__submit-icon fas fa-search" aria-hidden="true" aria-label="Submit Button"></i><#
			}

			#></button><#
		} #>
		<?php
	}

	protected function get_popup_close_template() {
		?>
		<span class="elementor-widget-cmsmasters-search__popup-close"><#

			view.addRenderAttribute( 'popup-close-icon', 'class', [
				'elementor-widget-cmsmasters-search__popup-close-icon',
				settings.popup_close_icon.value,
			] );

			view.addRenderAttribute( 'popup-close-icon', {
				'aria-hidden': 'true',
			} );

			var $popup_close_type = settings.popup_close_type;

			if ( 'icon' === settings.popup_close_type ) {
				view.addRenderAttribute( 'popup-close-icon', 'aria-label', 'Popup Close' );
			}

			if ( 'text' !== $popup_close_type ) {
				#><span class="elementor-widget-cmsmasters-search__popup-close-icon"><#

				if ( '' !== settings.popup_close_icon.value ) {
					if ( 'svg' !== settings.popup_close_icon.library ) {
						#><i {{{ view.getRenderAttributeString( 'popup-close-icon' ) }}}></i><#
					} else {
						if ( 'icon' === settings.popup_close_type ) {
							#>{{{ elementor.helpers.renderIcon( view, settings.popup_close_icon, { 'aria-hidden': true, 'aria-label': 'Popup Close' } ).value }}}<#
						} else {
							#>{{{ elementor.helpers.renderIcon( view, settings.popup_close_icon, { 'aria-hidden': true } ).value }}}<#
						}
					}
				} else {
					if ( 'icon' === settings.popup_close_type ) {
						#><i class="eicon-close" aria-hidden="true" aria-label="Popup Close"></i><#
					} else {
						#><i class="eicon-close" aria-hidden="true"></i><#
					}
				}

				#></span><#
			}

			if ( 'icon' !== $popup_close_type ) {
				#><span class="elementor-widget-cmsmasters-search__popup-close-label"><#

				var $popup_close_text = settings.popup_close_text;

				if ( '' !== $popup_close_text ) {
					#>{{{$popup_close_text}}}<#
				} else {
					#>Close<#
				}

				#></span><#
			}

		#></span>
		<?php
	}

	protected function get_popup_trigger_template() {
		?>
		<div class="elementor-widget-cmsmasters-search__popup-trigger-container">
			<div class="elementor-widget-cmsmasters-search__popup-trigger-inner"><#

			var $popup_trigger_icon = settings.popup_trigger_icon;

			view.addRenderAttribute( 'popup-trigger-inner-icon', 'class', [
				'elementor-widget-cmsmasters-search__popup-trigger-inner-icon',
				$popup_trigger_icon.value,
			] );

			view.addRenderAttribute( 'popup-trigger-inner-icon', {
				'aria-hidden': 'true',
			} );

			var $popup_trigger_content = settings.popup_trigger_content;

			if ( 'icon' === $popup_trigger_content ) {
				view.addRenderAttribute( 'popup-trigger-inner-icon', 'aria-label', 'Popup Trigger Button' );
			}

			if ( 'text' !== $popup_trigger_content ) {
				#><span class="elementor-widget-cmsmasters-search__popup-trigger-inner-icon"><#

				if ( '' !== $popup_trigger_icon.value ) {
					if ( 'svg' !== $popup_trigger_icon.library ) {
						#><i {{{ view.getRenderAttributeString( 'popup-trigger-inner-icon' ) }}}></i><#
					} else {
						if ( 'icon' === $popup_trigger_content ) {
							#>{{{ elementor.helpers.renderIcon( view, $popup_trigger_icon, { 'aria-hidden': true, 'aria-label': 'Popup Trigger Button' } ).value }}}<#
						} else {
							#>{{{ elementor.helpers.renderIcon( view, $popup_trigger_icon, { 'aria-hidden': true } ).value }}}<#
						}
					}
				} else {
					if ( 'icon' === $popup_trigger_content ) {
						#><i class="fas fa-search" aria-hidden="true" aria-label="Popup Trigger Button"></i><#
					} else {
						#><i class="fas fa-search" aria-hidden="true"></i><#
					}
				}

				#></span><#
			}

			if ( 'icon' !== $popup_trigger_content ) {
				#><span class="elementor-widget-cmsmasters-search__popup-trigger-inner-label"><#

					var $popup_trigger_label = settings.popup_trigger_label;

					if ( '' !== $popup_trigger_label ) {
						#>{{{ settings.popup_trigger_label }}}<#
					} else {
						#>Search<#
					}

				#></span><#
			}

			#></div>
		</div>
		<?php
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
			array(
				'field' => 'search_placeholder',
				'type' => esc_html__( 'Search Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'popup_trigger_label',
				'type' => esc_html__( 'Popup Button Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'popup_description_text',
				'type' => esc_html__( 'Popup Description Text', 'cmsmasters-elementor' ),
				'editor_type' => 'AREA',
			),
			array(
				'field' => 'popup_close_text',
				'type' => esc_html__( 'Popup Close Button', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'submit_button_label',
				'type' => esc_html__( 'Submit Button', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
