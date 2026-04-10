<?php
namespace CmsmastersElementor\Modules\TemplateSections\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Animation\Classes\Animation as CmsmastersAnimation;
use CmsmastersElementor\Modules\TemplateSections\Traits\Site_Widget;
use CmsmastersElementor\Utils as CmsmastersUtils;

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
 * Addon navigation menu widget.
 *
 * Addon widget that display site navigation menu.
 *
 * @since 1.0.0
*/
class Nav_Menu extends Base_Widget {

	use Site_Widget;

	/**
	 * Horizontal text parts.
	 *
	 * @since 1.11.0
	 */
	protected $h_start;
	protected $h_end;

	/**
	 * Conditions.
	 *
	 * @since 1.11.0
	 */
	protected $megamenu_condition = array();
	protected $megamenu_conditions = array();

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
		return __( 'Navigation Menu', 'cmsmasters-elementor' );
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
		return 'cmsicon-navigation';
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
			'navigation',
			'nav',
			'menu',
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
			'widget-cmsmasters-nav-menu',
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

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-nav-menu';
	}

	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Update for new Elementor responsive mode breakpoints.
	 * Added background gradient, `Border Color`, `Box Shadow`, `Text Shadow` and `Border Type`
	 * controls on normal state for dropdown toggle.
	 * Added background gradient, `Border Color`, `Box Shadow`, `Text Shadow`, `Border Radius`,
	 * `Text Decoration` controls on hover and active state for dropdown toggle.
	 * Fixed animation for toggle icon. Deleted exclude 'box_shadow_position' in all `Box Shadow`
	 * controls for dropdown toggle.
	 * Fixed `Breakpoint` control.
	 * Added `Dropdown Position` and `Dropdown Min Width` controls for dropdown menu.
	 * Added `Box Shadow` control for dropdown lists and `Gap Between` control for main and submenu indicator icon.
	 * Changed control `Menu Alignment` to responsive.
	 * @since 1.2.0 Added `Size` control for main and submenu indicator icons. Fixed `Breakpoint` control.
	 * Fixed `Alignment` for submenu. Fixed svg icon in indicators. Fixed `Border Radius` for submenu.
	 * Fixed `Menu alignment` choosing `Breakpoints` none. Fixed opening submenu in dropdown mode.
	 * @since 1.3.1 Added `Icon Position` control for dropdown toggle.
	 * @since 1.5.1 Fixed applying styles for current menu ancestor.
	 * @since 1.6.5 Fixed menu item space between in navigation menu widget.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 * @since 1.15.0 Added `Border Radius` controls for main menu items on hover and active state.
	 * Added `Separator` controls for main menu items.
	 * @since 1.16.4 Added `Menu Name` controls for menu.
	 * @since 1.17.3 Fixed applying main & dropdown menu alignment when a menu widget is inserted into a menu widget.
	 */
	protected function register_controls() {
		$this->set_controls_properties();

		$widget_selector = $this->get_widget_selector();

		$this->start_controls_section(
			'section_menu',
			array( 'label' => __( 'General', 'cmsmasters-elementor' ) )
		);

		$menus = $this->get_available_menus();

		if ( ! empty( $menus ) ) {
			$ids = array_keys( $menus );
			$default = $ids[0];

			/* translators: Navigation Menu widget Select Menu control description. %s: Menus screen link */
			$nav_menu_description = sprintf( __( 'Add or otherwise manage menus in %s.', 'cmsmasters-elementor' ), sprintf(
				'<a href="%2$s" target="_blank">%1$s</a>',
				__( 'Menus screen', 'cmsmasters-elementor' ),
				admin_url( 'nav-menus.php' )
			) );

			$this->add_control(
				'nav_menu',
				array(
					'label' => __( 'Select Menu', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => $menus,
					'default' => $default,
					'save_default' => true,
					'separator' => 'after',
					'description' => $nav_menu_description,
				)
			);

			$this->add_control(
				'nav_menu_name',
				array(
					'label' => esc_html__( 'Menu Name', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Menu', 'cmsmasters-elementor' ),
				)
			);
		} else {
			/* translators: Navigation Menu widget no menus notice. %s: Menus screen link */
			$no_menu_message_part = sprintf( __( 'Go to the %s to create one.', 'cmsmasters-elementor' ), sprintf(
				'<a href="%2$s" target="_blank">%1$s</a>',
				__( 'Menus screen', 'cmsmasters-elementor' ),
				admin_url( 'nav-menus.php?action=edit&menu=0' )
			) );
			$no_menu_message = sprintf(
				'<strong>%1$s</strong><br>%2$s',
				__( 'There are no menus in your site.', 'cmsmasters-elementor' ),
				$no_menu_message_part
			);

			$this->add_control(
				'nav_menu',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw' => $no_menu_message,
					'separator' => 'after',
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);
		}

		$this->add_control(
			'layout',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'horizontal' => array( 'title' => __( 'Horizontal', 'cmsmasters-elementor' ) ),
					'vertical' => array( 'title' => __( 'Vertical', 'cmsmasters-elementor' ) ),
					'dropdown' => array( 'title' => __( 'Dropdown', 'cmsmasters-elementor' ) ),
				),
				'default' => 'horizontal',
				'label_block' => false,
				'render_type' => 'template',
				'toggle' => false,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'layout_megamenu_notice',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'Mega Menu Template functionality is only available for the Dropdown Layout.', 'cmsmasters-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => array(
					'layout' => 'dropdown',
				),
			)
		);

		$this->add_control(
			'dropdown_menu_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => array(
						'title' => __( 'Default', 'cmsmasters-elementor' ),
						'description' => 'Default dropdown view',
					),
					'popup' => array(
						'title' => __( 'Popup', 'cmsmasters-elementor' ),
						'description' => 'Dropdown in popup',
					),
					'offcanvas' => array(
						'title' => __( 'Offcanvas', 'cmsmasters-elementor' ),
						'description' => 'Offcanvas type of dropdown menu',
					),
				),
				'default' => 'default',
				'toggle' => false,
				'label_block' => false,
				'render_type' => 'template',
				'frontend_available' => true,
				'condition' => array( 'layout' => 'dropdown' ),
			)
		);

		$this->add_control(
			'vertical_menu_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'normal' => array(
						'title' => __( 'Normal', 'cmsmasters-elementor' ),
						'description' => 'Dropdown on the side of the menu',
					),
					'toggle' => array(
						'title' => __( 'Toggle', 'cmsmasters-elementor' ),
						'description' => 'Toggle view menu',
					),
					'accordion' => array(
						'title' => __( 'Accordion', 'cmsmasters-elementor' ),
						'description' => 'Accordion view menu',
					),
					'side' => array(
						'title' => __( 'Side', 'cmsmasters-elementor' ),
						'description' => 'Vertical menu on the side of the page. Without dropdown menu',
					),
				),
				'default' => 'normal',
				'toggle' => false,
				'render_type' => 'template',
				'frontend_available' => true,
				'condition' => array( 'layout' => 'vertical' ),
			)
		);

		$this->add_control(
			'side_description',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'Only the main menu items are displayed, subitems are hidden.', 'cmsmasters-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => array(
					'layout' => 'vertical',
					'vertical_menu_type' => 'side',
				),
			)
		);

		$this->add_responsive_control(
			'menu_alignment',
			array(
				'label' => __( 'Menu Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'flex-end' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
					'space-between' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'default' => 'flex-start',
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-menu-alignment%s-',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul,
					{{WRAPPER}} ' . $widget_selector . '__main > ul > li > a > ' . $this->get_widget_selector() . '__item-text-wrap' => 'justify-content: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'horizontal',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'vertical',
								),
								array(
									'name' => 'vertical_menu_type',
									'operator' => '=',
									'value' => 'normal',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'side_menu_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-end' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'flex-start' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
					'space-between' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-stretch',
					),
				),
				'default' => 'center',
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-side > ul' => 'justify-content: {{VALUE}};',
				),
				'condition' => array(
					'layout' => 'vertical',
					'vertical_menu_type' => 'side',
				),
			)
		);

		$this->add_control(
			'side_menu_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
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
				'default' => 'left',
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-side-position-',
				'frontend_available' => true,
				'condition' => array(
					'layout' => 'vertical',
					'vertical_menu_type' => 'side',
				),
			)
		);

		$this->add_control(
			'indicator_main_popover',
			array(
				'label' => esc_html__( 'Main Indicator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'render_type' => 'template',
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'horizontal',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'vertical',
								),
								array(
									'name' => 'vertical_menu_type',
									'operator' => '=',
									'value' => 'normal',
								),
							),
						),
					),
				),
			)
		);

		$this->start_popover();

		$this->add_control(
			'indicator_main',
			array(
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'recommended' => array(
					'fa-solid' => array(
						'arrow-circle-down',
						'arrow-down',
						'long-arrow-alt-down',
						'angle-double-down',
						'angle-down',
						'chevron-down',
						'caret-down',
					),
					'fa-regular' => array(
						'arrow-alt-circle-down',
					),
				),
				'default' => array(
					'value' => 'fas fa-chevron-down',
					'library' => 'fa-solid',
				),
				'file' => '',
				'condition' => array( 'indicator_main_popover' => 'yes' ),
			)
		);

		$main_indicator_conditions = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'indicator_main_popover',
					'operator' => '=',
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'horizontal',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'vertical',
								),
								array(
									'name' => 'vertical_menu_type',
									'operator' => '=',
									'value' => 'normal',
								),
							),
						),
					),
				),
				array(
					'name' => 'indicator_main[value]',
					'operator' => '!==',
					'value' => '',
				),
			),
		);

		$this->add_control(
			'icon_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
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
				'default' => 'right',
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-icon-position-',
				'conditions' => $main_indicator_conditions,
			)
		);

		$this->add_responsive_control(
			'indicator_main_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'default' => array(
					'unit' => 'em',
					'size' => 1,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > a ' . $widget_selector . '__arrow > span,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > a ' . $widget_selector . '__arrow > span' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > a ' . $widget_selector . '__arrow > span svg,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > a ' . $widget_selector . '__arrow > span svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $main_indicator_conditions,
			)
		);

		$this->add_responsive_control(
			'indicator_main_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'vw',
				),
				'default' => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--indicator-main-gap: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $main_indicator_conditions,
			)
		);

		$this->add_control(
			'indicator_main_animation',
			array(
				'label' => __( 'Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'rotate-left' => __( 'Rotate Left', 'cmsmasters-elementor' ),
					'rotate-right' => __( 'Rotate Right', 'cmsmasters-elementor' ),
					'rotate-opposite' => __( 'Rotate Opposite', 'cmsmasters-elementor' ),
					'opacity' => __( 'Opacity', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
				'conditions' => $main_indicator_conditions,
			)
		);

		$this->add_control(
			'main_hover_transition',
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
					'{{WRAPPER}} ' . $widget_selector . '__main > ul > li > a > span > span' => 'transition-duration: {{SIZE}}s',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'indicator_main_popover',
							'operator' => '=',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'horizontal',
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'layout',
											'operator' => '=',
											'value' => 'vertical',
										),
										array(
											'name' => 'vertical_menu_type',
											'operator' => '=',
											'value' => 'normal',
										),
									),
								),
							),
						),
						array(
							'name' => 'indicator_main[value]',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'indicator_main_animation',
							'operator' => '!==',
							'value' => 'none',
						),
					),
				),
			)
		);

		$this->end_popover();

		$breakpoints = CmsmastersUtils::get_breakpoints();

		$this->add_control(
			'dropdown_breakpoints',
			array(
				'label' => __( 'Breakpoint', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					/* translators: Tablet breakpoint %d: number in pixels. */
					'tablet' => sprintf( __( 'Tablet (< %dpx)', 'cmsmasters-elementor' ), $breakpoints['tablet'] + 1 ),
					/* translators: Mobile breakpoint %d: number in pixels. */
					'mobile' => sprintf( __( 'Mobile (< %dpx)', 'cmsmasters-elementor' ), $breakpoints['mobile'] + 1 ),
				),
				'default' => 'tablet',
				'frontend_available' => true,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-dropdown-breakpoints-',
				'condition' => array( 'layout!' => 'dropdown' ),
			)
		);

		$this->add_control(
			'open_by_click',
			array(
				'label' => __( 'Open link by click (menu item)', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'return_value' => 'true',
				'default' => 'false',
				'description' => sprintf( __( 'If link # or empty, there will be no link', 'cmsmasters-elementor' ) ),
				'condition' => array(
					'layout' => 'vertical',
					'vertical_menu_type' => array(
						'toggle',
						'accordion',
					),
				),
			)
		);

		$this->add_control(
			'dropdown_absolute',
			array(
				'label' => __( 'Overlap Content', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'Set Dropdown Menu position to absolute. On desktop, this will affect the Layout: Dropdown. When chosen Breakpoint for Tablet or Mobile this will also affect the Layouts: Dropdown, Horizontal, Vertical (except the Side type).', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-dropdown-absolute-',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'horizontal',
								),
								array(
									'name' => 'dropdown_breakpoints',
									'operator' => '!==',
									'value' => 'none',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'vertical',
								),
								array(
									'name' => 'dropdown_breakpoints',
									'operator' => '!==',
									'value' => 'none',
								),
								array(
									'name' => 'vertical_menu_type',
									'operator' => '!==',
									'value' => 'side',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'dropdown',
								),
								array(
									'name' => 'dropdown_menu_type',
									'operator' => '=',
									'value' => 'default',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_absolute_position',
			array(
				'label' => __( 'Dropdown Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left Side', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right Side', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-dropdown-absolute%s-position-',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'horizontal',
								),
								array(
									'name' => 'dropdown_breakpoints',
									'operator' => '!==',
									'value' => 'none',
								),
								array(
									'name' => 'dropdown_absolute',
									'operator' => '=',
									'value' => 'yes',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'vertical',
								),
								array(
									'name' => 'dropdown_breakpoints',
									'operator' => '!==',
									'value' => 'none',
								),
								array(
									'name' => 'vertical_menu_type',
									'operator' => '!==',
									'value' => 'side',
								),
								array(
									'name' => 'dropdown_absolute',
									'operator' => '=',
									'value' => 'yes',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'dropdown',
								),
								array(
									'name' => 'dropdown_menu_type',
									'operator' => '=',
									'value' => 'default',
								),
								array(
									'name' => 'dropdown_absolute',
									'operator' => '=',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'widget_min_width',
			array(
				'label' => __( 'Dropdown Min Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 200,
						'max' => 1920,
					),
					'vw' => array(
						'min' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-dropdown-absolute-yes nav' . $widget_selector . '__dropdown' => 'width: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'horizontal',
								),
								array(
									'name' => 'dropdown_breakpoints',
									'operator' => '!==',
									'value' => 'none',
								),
								array(
									'name' => 'dropdown_absolute',
									'operator' => '=',
									'value' => 'yes',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'vertical',
								),
								array(
									'name' => 'dropdown_breakpoints',
									'operator' => '!==',
									'value' => 'none',
								),
								array(
									'name' => 'dropdown_absolute',
									'operator' => '=',
									'value' => 'yes',
								),
								array(
									'name' => 'vertical_menu_type',
									'operator' => '!==',
									'value' => 'side',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'dropdown',
								),
								array(
									'name' => 'dropdown_menu_type',
									'operator' => '=',
									'value' => 'default',
								),
								array(
									'name' => 'dropdown_absolute',
									'operator' => '=',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'dropdown_position',
			array(
				'label' => __( 'Dropdown Placement', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left Side', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right Side', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'right',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-dropdown-position-',
				'condition' => array(
					'layout' => 'vertical',
					'vertical_menu_type' => 'normal',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_dropdown_toggle',
			array(
				'label' => __( 'Toggle Switch', 'cmsmasters-elementor' ),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'horizontal',
								),
								array(
									'name' => 'dropdown_breakpoints',
									'operator' => '!==',
									'value' => 'none',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'vertical',
								),
								array(
									'name' => 'dropdown_breakpoints',
									'operator' => '!==',
									'value' => 'none',
								),
								array(
									'name' => 'vertical_menu_type',
									'operator' => '!==',
									'value' => 'side',
								),
							),
						),
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'dropdown',
						),
					),
				),
			)
		);

		$this->add_control(
			'dropdown_toggle_description',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'This toggle will appear on resolutions below the one defined in Breakpoint settings.', 'cmsmasters-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => array(
					'layout!' => 'dropdown',
					'dropdown_breakpoints!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'toggle_align',
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
				'toggle' => true,
				'label_block' => false,
				'selectors_dictionary' => array(
					'left' => 'flex-start;',
					'center' => 'center;',
					'right' => 'flex-end;',
					'stretch' => 'stretch',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__toggle-container' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'dropdown_toggle_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'description' => 'Switch has only icon',
					),
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => 'Switch has only text',
					),
					'both' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => 'Switch has icon and text',
					),
				),
				'default' => 'icon',
				'label_block' => false,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'toggle_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => array( 'title' => __( 'Default', 'cmsmasters-elementor' ) ),
					'stacked' => array( 'title' => __( 'Stacked', 'cmsmasters-elementor' ) ),
					'framed' => array( 'title' => __( 'Framed', 'cmsmasters-elementor' ) ),
				),
				'default' => 'stacked',
				'label_block' => false,
				'prefix_class' => 'cmsmasters-toggle-view-',
			)
		);

		$this->add_control(
			'toggle_shape',
			array(
				'label' => __( 'Shape', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'square' => array( 'title' => __( 'Square', 'cmsmasters-elementor' ) ),
					'circle' => array( 'title' => __( 'Circle', 'cmsmasters-elementor' ) ),
				),
				'default' => 'square',
				'label_block' => false,
				'prefix_class' => 'cmsmasters-toggle-shape-',
				'condition' => array(
					'toggle_view!' => 'default',
					'dropdown_toggle_type' => 'icon',
					'toggle_align!' => 'stretch',
				),
			)
		);

		$this->start_controls_tabs(
			'tabs_dropdown_toggle_icon',
			array( 'condition' => array( 'dropdown_toggle_type!' => 'text' ) )
		);

		$this->start_controls_tab(
			'tab_dropdown_toggle_icon_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'dropdown_toggle_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'recommended' => array(
					'fa-solid' => array(
						'align-justify',
						'hamburger',
						'list',
					),
				),
				'file' => '',
				'show_label' => false,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_toggle_icon_active',
			array( 'label' => __( 'Active', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'dropdown_toggle_icon_active',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'recommended' => array(
					'fa-solid' => array(
						'times',
						'times-circle',
					),
					'fa-regular' => array(
						'times-circle',
					),
				),
				'file' => '',
				'show_label' => false,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'dropdown_toggle_icon_align',
			array(
				'label' => __( 'Icon Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'left' => array( 'title' => __( 'Before', 'cmsmasters-elementor' ) ),
					'right' => array( 'title' => __( 'After', 'cmsmasters-elementor' ) ),
				),
				'default' => 'left',
				'toggle' => true,
				'label_block' => false,
				'render_type' => 'template',
				'condition' => array( 'dropdown_toggle_type' => 'both' ),
			)
		);

		$this->add_control(
			'dropdown_toggle_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Menu', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'dropdown_toggle_type!' => 'icon' ),
			)
		);

		$this->add_responsive_control(
			'toggle_text_icon_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'description' => __( 'Will be applied only if Justified Alignment is chosen.', 'cmsmasters-elementor' ),
				'options' => array(
					'central' => array(
						'title' => __( 'Central', 'cmsmasters-elementor' ),
					),
					'on-sides' => array(
						'title' => __( 'On Sides', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'central',
				'label_block' => false,
				'prefix_class' => 'cmsmasters-toggle-text-icon%s-position-',
				'condition' => array( 'dropdown_toggle_type' => 'both' ),
			)
		);

		$this->add_control(
			'dropdown_toggle_gap',
			array(
				'label' => __( 'Horizontal Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__toggle.cmsmasters-icon-align-left > span.cmsmasters-toggle-icon-active + span' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__toggle.cmsmasters-icon-align-right > span.cmsmasters-toggle-icon-active + span' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'dropdown_toggle_type' => 'both' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_dropdown_menu',
			array(
				'label' => __( 'Dropdown Menu', 'cmsmasters-elementor' ),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'horizontal',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'vertical',
								),
								array(
									'name' => 'vertical_menu_type',
									'operator' => '!==',
									'value' => 'side',
								),
							),
						),
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'dropdown',
						),
					),
				),
			)
		);

		$this->add_control(
			'dropdown_popup_type_width',
			array(
				'label' => __( 'Max Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 200,
						'max' => 1920,
					),
					'%' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown.cmsmasters-menu-dropdown-type-popup > ul' => 'max-width: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type' => 'popup',
				),
			)
		);

		$this->add_control(
			'full_width',
			array(
				'label' => __( 'Full Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'Stretch the dropdown of the menu to full width.', 'cmsmasters-elementor' ),
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-nav-menu-',
				'return_value' => 'stretch',
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 200,
						'max' => 500,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li ' . $widget_selector . '__dropdown-submenu,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li ' . $widget_selector . '__dropdown-submenu' => 'width: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'horizontal',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'vertical',
								),
								array(
									'name' => 'vertical_menu_type',
									'operator' => '=',
									'value' => 'normal',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_align',
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
					'space-between' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'default' => 'flex-start',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-dropdown%s-align-',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $this->get_widget_selector() . '__dropdown-submenu ' . $this->get_widget_selector() . '__item-text-wrap,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $this->get_widget_selector() . '__dropdown-submenu ' . $this->get_widget_selector() . '__item-text-wrap,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle ' . $this->get_widget_selector() . '__item-text-wrap,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion ' . $this->get_widget_selector() . '__item-text-wrap,
					{{WRAPPER}} ' . $widget_selector . '__dropdown ' . $this->get_widget_selector() . '__item-text-wrap' => 'justify-content: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'horizontal',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'vertical',
								),
								array(
									'name' => 'vertical_menu_type',
									'operator' => '!==',
									'value' => 'side',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'dropdown',
								),
								array(
									'name' => 'dropdown_menu_type',
									'operator' => '!==',
									'value' => 'popup',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_popup_align',
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
					'space-between' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'default' => 'center',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-dropdown%s-align-',
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type' => 'popup',
				),
			)
		);

		$this->add_control(
			'indicator_submenu_popover',
			array(
				'label' => esc_html__( 'Submenu Indicator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'render_type' => 'template',
			)
		);

		$this->start_popover();

		$this->add_control(
			'indicator_submenu',
			array(
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'recommended' => array(
					'fa-solid' => array(
						'angle-down',
						'chevron-down',
						'caret-down',
						'arrow-down',
						'long-arrow-alt-down',
						'chevron-circle-down',
						'arrow-circle-down',
						'angle-right',
						'chevron-right',
						'caret-right',
						'arrow-right',
						'long-arrow-alt-right',
						'chevron-circle-right',
						'arrow-circle-right',
					),
					'fa-regular' => array(
						'arrow-alt-circle-down',
						'arrow-alt-circle-right',
					),
				),
				'default' => array(
					'value' => 'fas fa-chevron-down',
					'library' => 'fa-solid',
				),
				'file' => '',
				'condition' => array( 'indicator_submenu_popover' => 'yes' ),
			)
		);

		$dropdown_indicator_conditions = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'indicator_submenu_popover',
					'operator' => '=',
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'horizontal',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'vertical',
								),
								array(
									'name' => 'vertical_menu_type',
									'operator' => '!==',
									'value' => 'side',
								),
							),
						),
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'dropdown',
						),
					),
				),
				array(
					'name' => 'indicator_submenu[value]',
					'operator' => '!==',
					'value' => '',
				),
			),
		);

		$this->add_control(
			'dropdown_icon_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
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
				'default' => 'right',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-dropdown-icon-',
				'conditions' => $dropdown_indicator_conditions,
			)
		);

		$this->add_responsive_control(
			'indicator_submenu_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'default' => array(
					'unit' => 'em',
					'size' => 1,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ul a ' . $widget_selector . '__arrow > span,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ul a ' . $widget_selector . '__arrow > span,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle a ' . $widget_selector . '__arrow > span,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion a ' . $widget_selector . '__arrow > span,
					{{WRAPPER}} ' . $widget_selector . '__dropdown a ' . $widget_selector . '__arrow > span' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ul a ' . $widget_selector . '__arrow > span svg,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ul a ' . $widget_selector . '__arrow > span svg,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle a ' . $widget_selector . '__arrow > span svg,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion a ' . $widget_selector . '__arrow > span svg,
					{{WRAPPER}} ' . $widget_selector . '__dropdown a ' . $widget_selector . '__arrow > span svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $dropdown_indicator_conditions,
			)
		);

		$this->add_responsive_control(
			'indicator_submenu_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'vw',
				),
				'default' => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--indicator-submenu-gap: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $dropdown_indicator_conditions,
			)
		);

		$this->add_control(
			'indicator_submenu_animation',
			array(
				'label' => __( 'Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'rotate-left' => __( 'Rotate Left', 'cmsmasters-elementor' ),
					'rotate-right' => __( 'Rotate Right', 'cmsmasters-elementor' ),
					'rotate-opposite' => __( 'Rotate Opposite', 'cmsmasters-elementor' ),
					'opacity' => __( 'Opacity', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
				'conditions' => $dropdown_indicator_conditions,
			)
		);

		$this->add_control(
			'submenu_hover_transition',
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
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu a > span,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu a > span,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul > li > a > span,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle ' . $widget_selector . '__dropdown-submenu > li > a > span,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul > li > a > span,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion ' . $widget_selector . '__dropdown-submenu > li > a > span,
					{{WRAPPER}} ' . $widget_selector . '__dropdown > ul > li > a > span,
					{{WRAPPER}} ' . $widget_selector . '__dropdown ' . $widget_selector . '__dropdown-submenu > li > a > span' => 'transition-duration: {{SIZE}}s',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'indicator_submenu_popover',
							'operator' => '=',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'horizontal',
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'layout',
											'operator' => '=',
											'value' => 'vertical',
										),
										array(
											'name' => 'vertical_menu_type',
											'operator' => '!==',
											'value' => 'side',
										),
									),
								),
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'dropdown',
								),
							),
						),
						array(
							'name' => 'indicator_submenu[value]',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'indicator_submenu_animation',
							'operator' => '!==',
							'value' => 'none',
						),
					),
				),
			)
		);

		$this->end_popover();

		$this->end_controls_section();

		$this->megamenu_content_controls_section();

		$this->start_controls_section(
			'section_dropdown_popup_offcanvas',
			array(
				'label' => __( 'Popup / Offcanvas Settings', 'cmsmasters-elementor' ),
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
				),
			)
		);

		$this->add_control(
			'offcanvas_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
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
				'default' => 'right',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-offcanvas-position-',
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type' => 'offcanvas',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_offcanvas_width',
			array(
				'label' => __( 'Canvas Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 100,
						'max' => 1000,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'vw' => array(
						'min' => 0,
						'max' => 100,
					),
					'vh' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array(
					'px',
					'%',
					'vw',
					'vh',
				),
				'default' => array(
					'unit' => 'px',
					'size' => 300,
				),
				'tablet_default' => array(
					'unit' => '%',
					'size' => 40,
				),
				'mobile_default' => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-menu-dropdown-type-offcanvas' . $widget_selector . '__dropdown' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type' => 'offcanvas',
				),
			)
		);

		$this->add_control(
			'dropdown_offcanvas_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type' => 'offcanvas',
				),
			)
		);

		$this->add_control(
			'popup_offcanvas_close_heading',
			array(
				'label' => __( 'Close', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
				),
			)
		);

		$this->add_control(
			'popup_offcanvas_close_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'description' => 'Switch has only icon',
					),
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => 'Switch has only text',
					),
					'both' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => 'Switch has icon and text',
					),
				),
				'default' => 'icon',
				'label_block' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-close-type-',
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
				),
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
				'prefix_class' => 'cmsmasters-close-view-',
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
				),
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
				'prefix_class' => 'cmsmasters-close-shape-',
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
					'popup_offcanvas_close_type' => 'icon',
					'popup_close_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'close_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'recommended' => array(
					'fa-solid' => array(
						'times',
						'times-circle',
					),
					'fa-regular' => array(
						'times-circle',
					),
				),
				'file' => '',
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
					'popup_offcanvas_close_type!' => 'text',
				),
			)
		);

		$this->add_control(
			'close_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Close', 'cmsmasters-elementor' ),
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
					'popup_offcanvas_close_type!' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'popup_close_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close i + span' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close svg + span' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
					'popup_offcanvas_close_type' => 'both',
					'close_text!' => '',
				),
			)
		);

		$this->add_control(
			'overlay_close',
			array(
				'label' => __( 'Close With Click on Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'Close popup upon click/tap on overlay', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'separator' => 'before',
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
				),
			)
		);

		$this->add_control(
			'esc_close',
			array(
				'label' => __( 'Close by ESC Button Click', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
				),
			)
		);

		$this->add_control(
			'disable_scroll',
			array(
				'label' => __( 'Disable scroll', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
				),
			)
		);

		$this->end_controls_section();

		$menu_first_level = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'layout',
					'operator' => '=',
					'value' => 'horizontal',
				),
				array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'vertical',
						),
						array(
							'name' => 'vertical_menu_type',
							'operator' => 'in',
							'value' => array(
								'normal',
								'side',
							),
						),
					),
				),
			),
		);

		$this->start_controls_section(
			'section_style_main_menu',
			array(
				'label' => __( 'Main Menu Item', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => $menu_first_level,
			)
		);

		$main_menu_link = '{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-side > ul > li > a';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'main_menu_typography',
				'selector' => $main_menu_link,
			)
		);

		$this->start_controls_tabs( 'tabs_menu_item_style' );

		$this->start_controls_tab(
			'tab_main_menu_item_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'main_menu_item_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array( $main_menu_link => 'color: {{VALUE}}; fill: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'main_menu_item_bg',
			array(
				'label' => __( 'Item Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array( $main_menu_link => 'background-color: {{VALUE}}' ),
			)
		);

		$this->add_control(
			'main_menu_item_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array( $main_menu_link => 'border-color: {{VALUE}}' ),
				'condition' => array(
					'main_menu_item_border_border!' => array( 'none' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'main_menu_item_text_shadow',
				'fields_options' => array(
					'text_shadow_type' => array( 'label' => __( 'Text Shadow', 'cmsmasters-elementor' ) ),
				),
				'selector' => $main_menu_link,
			)
		);

		$this->add_responsive_control(
			'main_menu_item_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					$main_menu_link => 'border-radius: {{SIZE}}{{UNIT}}',
				),
				'conditions' => $menu_first_level,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_main_menu_item_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$main_links_hover = '{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > a:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > a:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > a:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > a:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-side > ul > li > a:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-side > ul > li > a:focus';

		$this->add_control(
			'main_menu_item_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$main_links_hover => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'main_menu_item_bg_hover',
			array(
				'label' => __( 'Item Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$main_links_hover => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'main_menu_item_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$main_links_hover => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'main_menu_item_border_border!' => array( 'none' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'main_menu_item_text_shadow_hover',
				'fields_options' => array(
					'text_shadow_type' => array( 'label' => __( 'Text Shadow', 'cmsmasters-elementor' ) ),
				),
				'selector' => $main_links_hover,
			)
		);

		$this->add_responsive_control(
			'main_menu_item_border_radius_hover',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					$main_links_hover => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $menu_first_level,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_main_menu_item_active',
			array( 'label' => __( 'Active', 'cmsmasters-elementor' ) )
		);

		$main_links_active = '{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li.current-menu-ancestor > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li.current-menu-ancestor:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li.current-menu-ancestor:focus > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li.current-menu-ancestor > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li.current-menu-ancestor:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li.current-menu-ancestor:focus > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-side > ul > li.current-menu-ancestor > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-side > ul > li.current-menu-ancestor:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-side > ul > li.current-menu-ancestor:focus > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > a' . $widget_selector . '__item-active,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > a' . $widget_selector . '__item-active:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > a' . $widget_selector . '__item-active:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > a' . $widget_selector . '__item-active,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > a' . $widget_selector . '__item-active:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > a' . $widget_selector . '__item-active:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-side > ul > li > a' . $widget_selector . '__item-active,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-side > ul > li > a' . $widget_selector . '__item-active:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-side > ul > li > a' . $widget_selector . '__item-active:focus';

		$this->add_control(
			'main_menu_item_color_active',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$main_links_active => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'main_menu_item_bg_active',
			array(
				'label' => __( 'Item Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$main_links_active => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'main_menu_item_border_color_active',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$main_links_active => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'main_menu_item_border_border!' => array( 'none' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'main_menu_item_text_shadow_active',
				'fields_options' => array(
					'text_shadow_type' => array( 'label' => __( 'Text Shadow', 'cmsmasters-elementor' ) ),
				),
				'selector' => $main_links_active,
			)
		);

		$this->add_responsive_control(
			'main_menu_item_border_radius_active',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					$main_links_active => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $menu_first_level,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'main_menu_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_responsive_control(
			'main_menu_item_horizontal_padding',
			array(
				'label' => __( 'Horizontal Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--main-item-horizontal-padding: {{SIZE}}{{UNIT}}; --main-side-item-horizontal-padding: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'side_menu_alignment!' => 'space-between' ),
			)
		);

		$this->add_responsive_control(
			'main_menu_item_vertical_padding',
			array(
				'label' => __( 'Vertical Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array( $main_menu_link => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}' ),
				'condition' => array( 'side_menu_alignment!' => 'space-between' ),
			)
		);

		$this->add_responsive_control(
			'main_menu_item_space_between',
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 100 ),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal ' . $widget_selector . '__container-inner' => 'margin-left: calc( {{SIZE}}{{UNIT}} / 2 * -1 ); margin-right: calc( {{SIZE}}{{UNIT}} / 2 * -1 );',
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal ' . $widget_selector . '__container-inner > li' => 'margin-left: calc( {{SIZE}}{{UNIT}} / 2 ); margin-right: calc( {{SIZE}}{{UNIT}} / 2 );',
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-vertical-type-normal > ul > li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-vertical-type-side > ul > li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}' => '--main-menu-item-space-between: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'side_menu_alignment!' => 'space-between' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'main_menu_item_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => _x( 'Default', 'Border Control', 'cmsmasters-elementor' ),
							'none' => _x( 'None', 'Border Control', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => '',
						'selectors' => array( $main_menu_link => 'border-style: {{VALUE}};' ),
					),
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'selectors' => array( $main_menu_link => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
				'conditions' => $menu_first_level,
			)
		);

		$this->add_control(
			'main_menu_item_separator_type',
			array(
				'label' => __( 'Separator Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
					'groove' => __( 'Groove', 'cmsmasters-elementor' ),
				),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--main-menu-item-separator-type: {{VALUE}};',
				),
				'condition' => array( 'layout' => 'horizontal' ),
			)
		);

		$this->add_control(
			'main_menu_item_separator_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--main-menu-item-separator-color: {{VALUE}};',
				),
				'condition' => array(
					'layout' => 'horizontal',
					'main_menu_item_separator_type!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'main_menu_item_separator_height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default' => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--main-menu-item-separator-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'layout' => 'horizontal',
					'main_menu_item_separator_type!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'main_menu_item_separator_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'default' => array( 'size' => 1 ),
				'selectors' => array(
					'{{WRAPPER}}' => '--main-menu-item-separator-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'layout' => 'horizontal',
					'main_menu_item_separator_type!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'main_menu_item_separator_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--main-menu-item-separator-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'layout' => 'horizontal',
					'main_menu_item_separator_type!' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_side_box',
			array(
				'label' => __( 'Side Box', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => 'vertical',
					'vertical_menu_type' => 'side',
				),
			)
		);

		$this->add_responsive_control(
			'side_box_width',
			array(
				'label' => __( 'Box Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 50,
						'max' => 500,
					),
					'%' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'size_units' => array(
					'px',
					'%',
				),
				'default' => array(
					'size' => 100,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-side > ul' => 'width: {{SIZE}}{{UNIT}};',
					'html.cmsmasters-side-position-right' => 'padding-right: {{SIZE}}{{UNIT}};',
					'html.cmsmasters-side-position-left' => 'padding-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'layout' => 'vertical',
					'vertical_menu_type' => 'side',
				),
			)
		);

		$this->add_control(
			'side_box_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'.cmsmasters-side-content-{{ID}}' . $widget_selector . '__main.cmsmasters-vertical-type-side > ul' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'layout' => 'vertical',
					'vertical_menu_type' => 'side',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'side_box_border',
				'label' => __( 'Side Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => _x( 'Default', 'Border Control', 'cmsmasters-elementor' ),
							'none' => _x( 'None', 'Border Control', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => '',
					),
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
				'selector' => '.cmsmasters-side-content-{{ID}}' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-side > ul',
				'condition' => array(
					'layout' => 'vertical',
					'vertical_menu_type' => 'side',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_dropdown_toggle_style',
			array(
				'label' => __( 'Toggle Switch', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'horizontal',
								),
								array(
									'name' => 'dropdown_breakpoints',
									'operator' => '!==',
									'value' => 'none',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'vertical',
								),
								array(
									'name' => 'dropdown_breakpoints',
									'operator' => '!==',
									'value' => 'none',
								),
								array(
									'name' => 'vertical_menu_type',
									'operator' => '!==',
									'value' => 'side',
								),
							),
						),
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'dropdown',
						),
					),
				),
			)
		);

		$this->add_control(
			'dropdown_toggle_description_style',
			array(
				'raw' => __( 'This toggle will appear on resolutions below the one defined in Breakpoint settings.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => array(
					'layout!' => 'dropdown',
					'dropdown_breakpoints!' => 'none',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'dropdown_toggle_typography',
				'fields_options' => array(
					'text_decoration' => array(
						'selectors' => array(
							'{{WRAPPER}}' => '--dropdown-toggle-text-decoration: {{VALUE}};',
						),
					),
				),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__toggle',
				'condition' => array( 'dropdown_toggle_type!' => 'icon' ),
			)
		);

		$this->start_controls_tabs( 'tabs_toggle_item_style' );

		$this->start_controls_tab(
			'tab_dropdown_toggle_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'dropdown_toggle_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__toggle' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-toggle-view-framed ' . $widget_selector . '__toggle' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_background',
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
				'condition' => array( 'toggle_view!' => 'default' ),
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__toggle' => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'dropdown_toggle_bg_background' => array(
						'color',
						'gradient',
					),
					'toggle_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_color_stop',
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
					'dropdown_toggle_bg_background' => array( 'gradient' ),
					'toggle_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'dropdown_toggle_bg_background' => array( 'gradient' ),
					'toggle_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_color_b_stop',
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
					'dropdown_toggle_bg_background' => array( 'gradient' ),
					'toggle_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_gradient_type',
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
					'dropdown_toggle_bg_background' => array( 'gradient' ),
					'toggle_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_gradient_angle',
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
					'{{WRAPPER}} ' . $widget_selector . '__toggle' => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{dropdown_toggle_bg_color_stop.SIZE}}{{dropdown_toggle_bg_color_stop.UNIT}}, {{dropdown_toggle_bg_color_b.VALUE}} {{dropdown_toggle_bg_color_b_stop.SIZE}}{{dropdown_toggle_bg_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'dropdown_toggle_bg_background' => array( 'gradient' ),
					'dropdown_toggle_bg_gradient_type' => 'linear',
					'toggle_view!' => 'default',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_gradient_position',
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
					'{{WRAPPER}} ' . $widget_selector . '__toggle' => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{dropdown_toggle_bg_color_stop.SIZE}}{{dropdown_toggle_bg_color_stop.UNIT}}, {{dropdown_toggle_bg_color_b.VALUE}} {{dropdown_toggle_bg_color_b_stop.SIZE}}{{dropdown_toggle_bg_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'dropdown_toggle_bg_background' => array( 'gradient' ),
					'dropdown_toggle_bg_gradient_type' => 'radial',
					'toggle_view!' => 'default',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-toggle-view-framed ' . $widget_selector . '__toggle' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'toggle_view' => 'framed',
					'dropdown_toggle_framed_border_style!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_toggle_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'toggle_view!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'dropdown_toggle_text_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__toggle-label',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'dropdown_toggle_box_shadow',
				'selector' => '{{WRAPPER}}.cmsmasters-toggle-view-framed ' . $widget_selector . '__toggle,
					{{WRAPPER}}.cmsmasters-toggle-view-stacked ' . $widget_selector . '__toggle',
				'condition' => array( 'toggle_view!' => 'default' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_toggle_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'dropdown_toggle_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__toggle:hover' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-toggle-view-framed ' . $widget_selector . '__toggle:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_hover_background',
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
				'condition' => array( 'toggle_view!' => 'default' ),
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__toggle:hover' => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'dropdown_toggle_bg_hover_background' => array(
						'color',
						'gradient',
					),
					'toggle_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_hover_color_stop',
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
					'dropdown_toggle_bg_hover_background' => array( 'gradient' ),
					'toggle_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_hover_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'dropdown_toggle_bg_hover_background' => array( 'gradient' ),
					'toggle_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_hover_color_b_stop',
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
					'dropdown_toggle_bg_hover_background' => array( 'gradient' ),
					'toggle_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_hover_gradient_type',
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
					'dropdown_toggle_bg_hover_background' => array( 'gradient' ),
					'toggle_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_hover_gradient_angle',
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
					'{{WRAPPER}} ' . $widget_selector . '__toggle:hover' => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{dropdown_toggle_bg_hover_color_stop.SIZE}}{{dropdown_toggle_bg_hover_color_stop.UNIT}}, {{dropdown_toggle_bg_hover_color_b.VALUE}} {{dropdown_toggle_bg_hover_color_b_stop.SIZE}}{{dropdown_toggle_bg_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'dropdown_toggle_bg_hover_background' => array( 'gradient' ),
					'dropdown_toggle_bg_hover_gradient_type' => 'linear',
					'toggle_view!' => 'default',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_hover_gradient_position',
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
					'{{WRAPPER}} ' . $widget_selector . '__toggle:hover' => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{dropdown_toggle_bg_hover_color_stop.SIZE}}{{dropdown_toggle_bg_hover_color_stop.UNIT}}, {{dropdown_toggle_bg_hover_color_b.VALUE}} {{dropdown_toggle_bg_hover_color_b_stop.SIZE}}{{dropdown_toggle_bg_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'dropdown_toggle_bg_hover_background' => array( 'gradient' ),
					'dropdown_toggle_bg_hover_gradient_type' => 'radial',
					'toggle_view!' => 'default',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bd_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-toggle-view-framed ' . $widget_selector . '__toggle:hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'toggle_view' => 'framed',
					'dropdown_toggle_framed_border_style!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_toggle_border_radius_hover',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-toggle-view-framed ' . $widget_selector . '__toggle:hover,
					{{WRAPPER}}.cmsmasters-toggle-view-stacked ' . $widget_selector . '__toggle:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'toggle_view!' => 'default' ),
			)
		);

		$this->add_control(
			'dropdown_toggle_text_decoration_hover',
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
					'{{WRAPPER}}' => '--dropdown-toggle-hover-text-decoration: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'dropdown_toggle_text_shadow_hover',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__toggle:hover ' . $widget_selector . '__toggle-label',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'dropdown_toggle_box_shadow_hover',
				'selector' => '{{WRAPPER}}.cmsmasters-toggle-view-framed ' . $widget_selector . '__toggle:hover,
					{{WRAPPER}}.cmsmasters-toggle-view-stacked ' . $widget_selector . '__toggle:hover',
				'condition' => array( 'toggle_view!' => 'default' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_toggle_active',
			array( 'label' => __( 'Active', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'dropdown_toggle_color_active',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__toggle.active' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-toggle-view-framed ' . $widget_selector . '__toggle.active' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_active_background',
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
				'condition' => array( 'toggle_view!' => 'default' ),
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_color_active',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__toggle.active' => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'dropdown_toggle_bg_active_background' => array(
						'color',
						'gradient',
					),
					'toggle_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_active_color_stop',
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
					'dropdown_toggle_bg_active_background' => array( 'gradient' ),
					'toggle_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_active_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'dropdown_toggle_bg_active_background' => array( 'gradient' ),
					'toggle_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_active_color_b_stop',
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
					'dropdown_toggle_bg_active_background' => array( 'gradient' ),
					'toggle_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_active_gradient_type',
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
					'dropdown_toggle_bg_active_background' => array( 'gradient' ),
					'toggle_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_active_gradient_angle',
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
					'{{WRAPPER}} ' . $widget_selector . '__toggle.active' => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{dropdown_toggle_bg_active_color_stop.SIZE}}{{dropdown_toggle_bg_active_color_stop.UNIT}}, {{dropdown_toggle_bg_active_color_b.VALUE}} {{dropdown_toggle_bg_active_color_b_stop.SIZE}}{{dropdown_toggle_bg_active_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'dropdown_toggle_bg_active_background' => array( 'gradient' ),
					'dropdown_toggle_bg_active_gradient_type' => 'linear',
					'toggle_view!' => 'default',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bg_active_gradient_position',
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
					'{{WRAPPER}} ' . $widget_selector . '__toggle.active' => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{dropdown_toggle_bg_active_color_stop.SIZE}}{{dropdown_toggle_bg_active_color_stop.UNIT}}, {{dropdown_toggle_bg_active_color_b.VALUE}} {{dropdown_toggle_bg_active_color_b_stop.SIZE}}{{dropdown_toggle_bg_active_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'dropdown_toggle_bg_active_background' => array( 'gradient' ),
					'dropdown_toggle_bg_active_gradient_type' => 'radial',
					'toggle_view!' => 'default',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'dropdown_toggle_bd_color_active',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-toggle-view-framed ' . $widget_selector . '__toggle.active' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'toggle_view' => 'framed',
					'dropdown_toggle_framed_border_style!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_toggle_border_radius_active',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-toggle-view-framed ' . $widget_selector . '__toggle.active,
					{{WRAPPER}}.cmsmasters-toggle-view-stacked ' . $widget_selector . '__toggle.active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'toggle_view!' => 'default' ),
			)
		);

		$this->add_control(
			'dropdown_toggle_text_decoration_active',
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
					'{{WRAPPER}}' => '--dropdown-toggle-active-text-decoration: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'dropdown_toggle_text_shadow_active',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__toggle.active ' . $widget_selector . '__toggle-label',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'dropdown_toggle_box_shadow_active',
				'selector' => '{{WRAPPER}}.cmsmasters-toggle-view-framed ' . $widget_selector . '__toggle.active,
					{{WRAPPER}}.cmsmasters-toggle-view-stacked ' . $widget_selector . '__toggle.active',
				'condition' => array( 'toggle_view!' => 'default' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'dropdown_toggle_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_responsive_control(
			'dropdown_toggle_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'toggle_view!' => 'default',
					'toggle_shape' => 'square',
				),
			)
		);

		$this->add_control(
			'dropdown_toggle_framed_border_style',
			array(
				'label' => _x( 'Border Type', 'Border Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
					'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'prefix_class' => 'cmsmasters-dropdown-toggle-border-type-',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__toggle' => 'border-style: {{VALUE}};',
				),
				'condition' => array( 'toggle_view' => 'framed' ),
			)
		);

		$this->add_responsive_control(
			'dropdown_toggle_framed_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__toggle' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'toggle_view' => 'framed',
					'dropdown_toggle_framed_border_style!' => array(
						'',
						'default',
					),
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_toggle_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__toggle i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__toggle svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'dropdown_toggle_type!' => 'text' ),
			)
		);

		$this->add_responsive_control(
			'dropdown_toggle_icon_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__toggle' => 'padding: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'dropdown_toggle_type' => 'icon',
					'toggle_view!' => 'default',
					'toggle_align!' => 'stretch',
					'toggle_shape' => 'circle',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_popup_offcanvas',
			array(
				'label' => __( 'Popup / Offcanvas', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type' => array(
						'popup',
						'offcanvas',
					),
				),
			)
		);

		$this->add_control(
			'popup_offcanvas_vertical_alignment',
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
				'prefix_class' => 'cmsmasters-popup-offcanvas-ver-alignment-',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown.cmsmasters-menu-dropdown-type-popup' => 'align-items: {{VALUE}}',
					'{{WRAPPER}} ' . $widget_selector . '__dropdown.cmsmasters-menu-dropdown-type-offcanvas' => 'justify-content: {{VALUE}}',
				),
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type' => array(
						'popup',
						'offcanvas',
					),
				),
			)
		);

		$this->add_responsive_control(
			'popup_offcanvas_vertical_margin',
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
					'{{WRAPPER}} ' . $widget_selector . '__dropdown.cmsmasters-menu-dropdown-type-popup > ul,
					{{WRAPPER}} ' . $widget_selector . '__dropdown.cmsmasters-menu-dropdown-type-offcanvas > ul' => 'margin-top: {{TOP}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				),
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type' => array(
						'popup',
						'offcanvas',
					),
					'popup_offcanvas_vertical_alignment!' => 'center',
				),
			)
		);

		$this->add_control(
			'popup_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown.cmsmasters-menu-dropdown-type-popup,
					{{WRAPPER}} ' . $widget_selector . '__dropdown.cmsmasters-menu-dropdown-type-offcanvas' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type' => array(
						'popup',
						'offcanvas',
					),
				),
			)
		);

		$this->add_control(
			'popup_offcanvas_close_style_heading',
			array(
				'label' => __( 'Close Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type' => array(
						'popup',
						'offcanvas',
					),
				),
			)
		);

		$this->add_control(
			'popup_offcanvas_close_align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'label_block' => false,
				'default' => 'flex-end',
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close-container' => 'justify-content: {{VALUE}}',
				),
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'close_top_gap',
			array(
				'label' => __( 'Top Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--dropdown-close-top-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close' => 'margin-top: {{SIZE}}{{UNIT}}; ',
				),
				'condition' => array( 'layout' => 'dropdown' ),
			)
		);

		$this->add_responsive_control(
			'close_side_gap',
			array(
				'label' => __( 'Side Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'layout' => 'dropdown',
					'popup_offcanvas_close_align!' => 'center',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_close_style' );

		$this->start_controls_tab(
			'tab_close_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'close_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close' => 'color: {{VALUE}}',
					'{{WRAPPER}}.cmsmasters-close-view-framed ' . $widget_selector . '__dropdown-close' => 'border-color: {{VALUE}}',
				),
				'condition' => array( 'popup_offcanvas_close_type!' => 'icon' ),
			)
		);

		$this->add_control(
			'close_icon_color',
			array(
				'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close i' => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}}.cmsmasters-close-view-framed.cmsmasters-close-type-icon ' . $widget_selector . '__dropdown-close' => 'border-color: {{VALUE}}',
				),
				'condition' => array( 'popup_offcanvas_close_type!' => 'text' ),
			)
		);

		$this->add_control(
			'close_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close' => 'background-color: {{VALUE}}',
				),
				'condition' => array( 'popup_close_view!' => 'default' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_close_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'close_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}}.cmsmasters-close-view-framed ' . $widget_selector . '__dropdown-close:hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array( 'popup_offcanvas_close_type!' => 'icon' ),
			)
		);

		$this->add_control(
			'close_icon_color_hover',
			array(
				'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close:hover > i' => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close:hover svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}}.cmsmasters-close-view-framed.cmsmasters-close-type-icon ' . $widget_selector . '__dropdown-close:hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array( 'popup_offcanvas_close_type!' => 'text' ),
			)
		);

		$this->add_control(
			'close_bg_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close:hover' => 'background-color: {{VALUE}}',
				),
				'condition' => array( 'popup_close_view!' => 'default' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'close_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'close_typography',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__dropdown-close ' . $widget_selector . '__dropdown-close-label',
				'condition' => array(
					'layout' => 'dropdown',
					'popup_offcanvas_close_type!' => 'icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'close_box_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__dropdown-close',
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
					'popup_close_view!' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'close_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--dropdown-close-icon-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'layout' => 'dropdown',
					'popup_offcanvas_close_type!' => 'text',
				),
			)
		);

		$this->add_responsive_control(
			'popup_close_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
					'popup_close_view!' => 'default',
					'popup_close_shape!' => 'circle',
				),
			)
		);

		$this->add_responsive_control(
			'popup_close_icon_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close' => 'padding: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
					'popup_close_view!' => 'default',
					'popup_offcanvas_close_type' => 'icon',
					'popup_close_shape' => 'circle',
				),
			)
		);

		$this->add_responsive_control(
			'popup_close_framed_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
					'popup_close_view' => 'framed',
				),
			)
		);

		$this->add_responsive_control(
			'close_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__dropdown-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'layout' => 'dropdown',
					'dropdown_menu_type!' => 'default',
					'popup_close_view!' => 'default',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_dropdown_list',
			array(
				'label' => __( 'Dropdown List', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'dropdown_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul,
					{{WRAPPER}} ' . $widget_selector . '__dropdown > ul' => 'background-color: {{VALUE}}',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_bg_color', '{{VALUE}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_top_distance',
			array(
				'label' => __( 'Gap from Top', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => -30,
						'max' => 70,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > ' . $widget_selector . '__dropdown-submenu,
					{{WRAPPER}} ' . $widget_selector . '__dropdown > ul' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__main:not(.cmsmasters-layout-dropdown) > ul > li > ' . $widget_selector . '__dropdown-submenu:before' => 'height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_top_distance', '{{SIZE}}{{UNIT}}' ),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'horizontal',
						),
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'vertical',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'dropdown',
								),
								array(
									'name' => 'dropdown_menu_type',
									'operator' => '=',
									'value' => 'default',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'dropdown_horizontal_distance',
			array(
				'label' => __( 'Horizontal Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'Gap between dropdown list columns.', 'cmsmasters-elementor' ),
				'range' => array(
					'px' => array(
						'min' => -30,
						'max' => 70,
					),
				),
				'frontend_available' => true,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_horizontal_distance', '{{SIZE}}{{UNIT}}' ),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'horizontal',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'vertical',
								),
								array(
									'name' => 'vertical_menu_type',
									'operator' => '=',
									'value' => 'normal',
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'dropdown_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => _x( 'Default', 'Border Control', 'cmsmasters-elementor' ),
							'none' => _x( 'None', 'Border Control', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => '',
						'selectors' => array(
							'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu,
							{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu,
							{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul,
							{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul,
							{{WRAPPER}} ' . $widget_selector . '__dropdown:not(.cmsmasters-menu-dropdown-type-offcanvas) > ul,
							{{WRAPPER}} ' . $widget_selector . '__dropdown.cmsmasters-menu-dropdown-type-offcanvas' => 'border-style: {{VALUE}};',
							'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_border_style', '{{VALUE}}' ),
						),
					),
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'selectors' => array(
							'{{WRAPPER}}' => '--dropdown-top-border-width: {{TOP}}{{UNIT}}; --dropdown-right-border-width: {{RIGHT}}{{UNIT}}; --dropdown-bottom-border-width: {{BOTTOM}}{{UNIT}}; --dropdown-left-border-width: {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu,
							{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu,
							{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul,
							{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul,
							{{WRAPPER}} ' . $widget_selector . '__dropdown:not(.cmsmasters-menu-dropdown-type-offcanvas) > ul,
							{{WRAPPER}} ' . $widget_selector . '__dropdown.cmsmasters-menu-dropdown-type-offcanvas' => 'border-top-width: {{TOP}}{{UNIT}}; border-right-width: {{RIGHT}}{{UNIT}}; border-bottom-width: {{BOTTOM}}{{UNIT}}; border-left-width: {{LEFT}}{{UNIT}};',
						),
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
						'selectors' => array(
							'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu,
							{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu,
							{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul,
							{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul,
							{{WRAPPER}} ' . $widget_selector . '__dropdown:not(.cmsmasters-menu-dropdown-type-offcanvas) > ul,
							{{WRAPPER}} ' . $widget_selector . '__dropdown.cmsmasters-menu-dropdown-type-offcanvas' => 'border-color: {{VALUE}};',
							'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_border_color', '{{VALUE}}' ),
						),
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul,
					{{WRAPPER}} ' . $widget_selector . '__dropdown:not(.cmsmasters-menu-dropdown-type-offcanvas) > ul,
					{{WRAPPER}} ' . $widget_selector . '__dropdown.cmsmasters-menu-dropdown-type-offcanvas' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_border_radius', '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--dropdown-padding-top: {{TOP}}{{UNIT}}; --dropdown-padding-right: {{RIGHT}}{{UNIT}}; --dropdown-padding-bottom: {{BOTTOM}}{{UNIT}}; --dropdown-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'dropdown_box_shadow',
				'exclude' => array( 'box_shadow_position' ),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul,
					{{WRAPPER}} ' . $widget_selector . '__dropdown:not(.cmsmasters-menu-dropdown-type-offcanvas) > ul',
				'fields_options' => array(
					'box_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
							'{{WRAPPER}}' => '--dropdown-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}};',
						),
					),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'horizontal',
						),
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'vertical',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'dropdown',
								),
								array(
									'name' => 'dropdown_menu_type',
									'operator' => '!==',
									'value' => 'offcanvas',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'dropdown_sublevel_heading',
			array(
				'label' => __( 'Sublevel', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'dropdown_sublevel_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul > li > ' . $widget_selector . '__dropdown-submenu,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul > li > ' . $widget_selector . '__dropdown-submenu,
					{{WRAPPER}} ' . $widget_selector . '__dropdown > ul > li > ' . $widget_selector . '__dropdown-submenu' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_sublevel_gap',
			array(
				'label' => __( 'Sublevel list Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default' => array(
					'isLinked' => false,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu,
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu,
					{{WRAPPER}} ' . $widget_selector . '__dropdown > ul > li ' . $widget_selector . '__dropdown-submenu' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_sublevel_gap_top', '{{TOP}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'dropdown_sublevel_gap_right', '{{RIGHT}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'dropdown_sublevel_gap_bottom', '{{BOTTOM}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'dropdown_sublevel_gap_left', '{{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_dropdown_item',
			array(
				'label' => __( 'Dropdown Item', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$dropdown_sublevels_level_conditions = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'layout',
					'operator' => '=',
					'value' => 'horizontal',
				),
				array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'vertical',
						),
						array(
							'name' => 'vertical_menu_type',
							'operator' => '!==',
							'value' => 'side',
						),
					),
				),
				array(
					'name' => 'layout',
					'operator' => '=',
					'value' => 'dropdown',
				),
			),
		);

		$this->add_control(
			'dropdown_main_level_heading',
			array(
				'label' => __( 'Main Level', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'conditions' => $dropdown_sublevels_level_conditions,
			)
		);

		$dropdown_links = '{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu li > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu li > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul > li > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu li > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul > li > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu li > a,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul > li > a,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul ' . $widget_selector . '__dropdown-submenu li > a';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'dropdown_main_level_typography',
				'selector' => $dropdown_links,
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => 'font-family: "{{VALUE}}";',
							'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_typography_font_family', '"{{VALUE}}"' ),
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => 'font-size: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_typography_font_size', '{{SIZE}}{{UNIT}}' ),
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => 'font-weight: {{VALUE}};',
							'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_typography_font_weight', '{{VALUE}}' ),
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => 'text-transform: {{VALUE}};',
							'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_typography_text_transform', '{{VALUE}}' ),
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => 'font-style: {{VALUE}};',
							'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_typography_font_style', '{{VALUE}}' ),
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => 'text-decoration: {{VALUE}};',
							'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_typography_text_decoration', '{{VALUE}}' ),
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => 'line-height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_typography_line_height', '{{SIZE}}{{UNIT}}' ),
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => 'letter-spacing: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_typography_letter_spacing', '{{SIZE}}{{UNIT}}' ),
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => 'word-spacing: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_typography_word_spacing', '{{SIZE}}{{UNIT}}' ),
						),
					),
				),
			)
		);

		$this->start_controls_tabs( 'tabs_dropdown_main_level_style' );

		$this->start_controls_tab(
			'tab_dropdown_main_level_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'dropdown_main_level_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$dropdown_links => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_color', '{{VALUE}}' ),
				),
			)
		);

		$this->add_control(
			'dropdown_main_level_bg',
			array(
				'label' => __( 'Item Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$dropdown_links => 'background-color: {{VALUE}}',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_bg', '{{VALUE}}' ),
				),
			)
		);

		$this->add_control(
			'dropdown_main_level_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$dropdown_links => 'border-color: {{VALUE}}',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_border_color', '{{VALUE}}' ),
				),
				'condition' => array( 'dropdown_main_level_border_border!' => 'none' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_main_level_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$dropdown_links_hover = '{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu li:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu li > a:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu li:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu li > a:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul > li > a:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul > li:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu li > a:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu li:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul > li > a:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu li > a:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul > li > a:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul > li:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu li > a:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu li:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul > li > a:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu li > a:focus,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul > li > a:hover,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul ' . $widget_selector . '__dropdown-submenu li > a:hover,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul > li > a:focus
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul ' . $widget_selector . '__dropdown-submenu li > a:focus';

		$this->add_control(
			'dropdown_main_level_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$dropdown_links_hover => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_color_hover', '{{VALUE}}' ),
				),
			)
		);

		$this->add_control(
			'dropdown_main_level_bg_hover',
			array(
				'label' => __( 'Item Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$dropdown_links_hover => 'background-color: {{VALUE}}',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_bg_hover', '{{VALUE}}' ),
				),
			)
		);

		$this->add_control(
			'dropdown_main_level_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$dropdown_links_hover => 'border-color: {{VALUE}}',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_border_color_hover', '{{VALUE}}' ),
				),
				'condition' => array( 'dropdown_main_level_border_border!' => 'none' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_main_level_active',
			array( 'label' => __( 'Active', 'cmsmasters-elementor' ) )
		);

		$dropdown_links_active = '{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu > li.current-menu-ancestor > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu > li.current-menu-ancestor:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu > li.current-menu-ancestor:focus > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu > li.current-menu-ancestor > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu > li.current-menu-ancestor:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu > li.current-menu-ancestor:focus > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu a' . $widget_selector . '__item-active,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu a' . $widget_selector . '__item-active:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu a' . $widget_selector . '__item-active:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu a' . $widget_selector . '__item-active,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu a' . $widget_selector . '__item-active:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu a' . $widget_selector . '__item-active:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul > li > a' . $widget_selector . '__item-active,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu a' . $widget_selector . '__item-active,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul > li > a' . $widget_selector . '__item-active:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu a' . $widget_selector . '__item-active:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul > li > a' . $widget_selector . '__item-active:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu a' . $widget_selector . '__item-active:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul > li > a' . $widget_selector . '__item-active,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu a' . $widget_selector . '__item-active,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul > li > a' . $widget_selector . '__item-active:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu a' . $widget_selector . '__item-active:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul > li > a' . $widget_selector . '__item-active:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu a' . $widget_selector . '__item-active:focus,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul > li.current-menu-item > a,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul ' . $widget_selector . '__dropdown-submenu li.current-menu-item > a,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul > li.current-menu-item > a:hover,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul ' . $widget_selector . '__dropdown-submenu li.current-menu-item > a:hover,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul > li.current-menu-item > a:focus,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul ' . $widget_selector . '__dropdown-submenu li.current-menu-item > a:focus';

		$this->add_control(
			'dropdown_main_level_color_active',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$dropdown_links_active => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_color_active', '{{VALUE}}' ),
				),
			)
		);

		$this->add_control(
			'dropdown_main_level_bg_active',
			array(
				'label' => __( 'Item Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$dropdown_links_active => 'background-color: {{VALUE}}',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_bg_active', '{{VALUE}}' ),
				),
			)
		);

		$this->add_control(
			'dropdown_main_level_border_color_active',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$dropdown_links_active => 'border-color: {{VALUE}}',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_border_color_active', '{{VALUE}}' ),
				),
				'condition' => array( 'dropdown_main_level_border_border!' => 'none' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'dropdown_main_level_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_responsive_control(
			'dropdown_item_main_horizontal_padding',
			array(
				'label' => __( 'Horizontal Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--dropdown-item-main-horizontal-padding: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'dropdown_align!' => 'center' ),
			)
		);

		$this->add_responsive_control(
			'dropdown_item_main_vertical_padding',
			array(
				'label' => __( 'Vertical Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array(
					$dropdown_links => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_item_main_vertical_padding', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_item_space_main_between',
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul > li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul > li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__dropdown > ul > li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__dropdown > ul ' . $widget_selector . '__dropdown-submenu li:not(:first-child)' => 'margin-top: calc( {{SIZE}}{{UNIT}} / 2 ); padding-top: calc( {{SIZE}}{{UNIT}} / 2 );',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_item_space_main_between', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'dropdown_main_level_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => _x( 'Default', 'Border Control', 'cmsmasters-elementor' ),
							'none' => _x( 'None', 'Border Control', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => '',
						'selectors' => array(
							$dropdown_links => 'border-style: {{VALUE}};',
							'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_border_style', '{{VALUE}}' ),
						),
					),
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'selectors' => array(
							$dropdown_links => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_border_width_top', '{{TOP}}{{UNIT}}' ) .
							CmsmastersUtils::prepare_css_var( 'dropdown_main_level_border_width_right', '{{RIGHT}}{{UNIT}}' ) .
							CmsmastersUtils::prepare_css_var( 'dropdown_main_level_border_width_bottom', '{{BOTTOM}}{{UNIT}}' ) .
							CmsmastersUtils::prepare_css_var( 'dropdown_main_level_border_width_left', '{{LEFT}}{{UNIT}}' ),
						),
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_main_level_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'devices' => array(
					'desktop',
					'tablet',
				),
				'selectors' => array(
					$dropdown_links => 'border-radius: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_main_level_border_radius', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_control(
			'dropdown_sublevels_level_heading',
			array(
				'label' => __( 'Custom Sublevel Styles', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $dropdown_sublevels_level_conditions,
			)
		);

		$this->add_control(
			'dropdown_sublevels_description',
			array(
				'raw' => __( 'Set these settings if you need to override all or some Main Level settings. If set, these style settings will be applied to all sublevel item display layouts, with the exception of:<br>- Normal and Side Types of Vertical Layout and all Horizontal Layouts for desktop appearance;<br>- Side Type of Vertical Layout for minimized appearance.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'conditions' => $dropdown_sublevels_level_conditions,
			)
		);

		$dropdown_sublevel_links = '{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > ' . $widget_selector . '__dropdown-submenu ul a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > ' . $widget_selector . '__dropdown-submenu ul a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu li > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu a,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul ' . $widget_selector . '__dropdown-submenu a';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'dropdown_typography',
				'selector' => $dropdown_sublevel_links,
				'conditions' => $dropdown_sublevels_level_conditions,
			)
		);

		$this->start_controls_tabs(
			'tabs_dropdown_item_style',
			array( 'conditions' => $dropdown_sublevels_level_conditions )
		);

		$this->start_controls_tab(
			'tab_dropdown_item_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'dropdown_item_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array( $dropdown_sublevel_links => 'color: {{VALUE}}; fill: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'dropdown_item_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'none',
				'selectors' => array( $dropdown_sublevel_links => 'background-color: {{VALUE}}' ),
			)
		);

		$this->add_control(
			'dropdown_item_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array( $dropdown_sublevel_links => 'border-color: {{VALUE}}' ),
				'condition' => array( 'dropdown_item_border_border!' => 'none' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_item_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$dropdown_sublevel_links_hover = '{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > ' . $widget_selector . '__dropdown-submenu ul a:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > ' . $widget_selector . '__dropdown-submenu ul a:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > ' . $widget_selector . '__dropdown-submenu ul a:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > ' . $widget_selector . '__dropdown-submenu ul a:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu li > a:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu li:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu li > a:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu a:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu li:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu a:focus,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul ' . $widget_selector . '__dropdown-submenu a:hover,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul ' . $widget_selector . '__dropdown-submenu a:focus';

		$this->add_control(
			'dropdown_item_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array( $dropdown_sublevel_links_hover => 'color: {{VALUE}}; fill: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'dropdown_item_bg_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'none',
				'selectors' => array( $dropdown_sublevel_links_hover => 'background-color: {{VALUE}}' ),
			)
		);

		$this->add_control(
			'dropdown_item_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array( $dropdown_sublevel_links_hover => 'border-color: {{VALUE}}' ),
				'condition' => array( 'dropdown_item_border_border!' => 'none' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_item_active',
			array(
				'label' => __( 'Active', 'cmsmasters-elementor' ),
			)
		);

		$dropdown_sublevel_links_active = '{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > ' . $widget_selector . '__dropdown-submenu ul > li.current-menu-ancestor > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > ' . $widget_selector . '__dropdown-submenu ul > li.current-menu-ancestor:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > ' . $widget_selector . '__dropdown-submenu ul > li.current-menu-ancestor:focus > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > ' . $widget_selector . '__dropdown-submenu ul > li.current-menu-ancestor > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > ' . $widget_selector . '__dropdown-submenu ul > li.current-menu-ancestor:hover > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > ' . $widget_selector . '__dropdown-submenu ul > li.current-menu-ancestor:focus > a,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > ' . $widget_selector . '__dropdown-submenu ul a' . $widget_selector . '__item-active,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > ' . $widget_selector . '__dropdown-submenu ul a' . $widget_selector . '__item-active:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > ' . $widget_selector . '__dropdown-submenu ul a' . $widget_selector . '__item-active:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > ' . $widget_selector . '__dropdown-submenu ul a' . $widget_selector . '__item-active,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > ' . $widget_selector . '__dropdown-submenu ul a' . $widget_selector . '__item-active:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > ' . $widget_selector . '__dropdown-submenu ul a' . $widget_selector . '__item-active:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu li > a' . $widget_selector . '__item-active,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu li > a' . $widget_selector . '__item-active:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu li > a' . $widget_selector . '__item-active:focus,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu a' . $widget_selector . '__item-active,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu a' . $widget_selector . '__item-active:hover,
			{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu a' . $widget_selector . '__item-active:focus,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul ' . $widget_selector . '__dropdown-submenu li.current-menu-item > a,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul ' . $widget_selector . '__dropdown-submenu li.current-menu-item > a:hover,
			{{WRAPPER}} ' . $widget_selector . '__dropdown > ul ' . $widget_selector . '__dropdown-submenu li.current-menu-item > a:focus';

		$this->add_control(
			'dropdown_item_color_active',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array( $dropdown_sublevel_links_active => 'color: {{VALUE}}; fill: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'dropdown_item_bg_color_active',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'none',
				'selectors' => array( $dropdown_sublevel_links_active => 'background-color: {{VALUE}}' ),
			)
		);

		$this->add_control(
			'dropdown_item_border_color_active',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array( $dropdown_sublevel_links_active => 'border-color: {{VALUE}}' ),
				'condition' => array( 'dropdown_item_border_border!' => 'none' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'dropdown_item_horizontal_padding',
			array(
				'label' => __( 'Horizontal Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--dropdown-item-sublevel-horizontal-padding: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'horizontal',
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'layout',
											'operator' => '=',
											'value' => 'vertical',
										),
										array(
											'name' => 'vertical_menu_type',
											'operator' => '!==',
											'value' => 'side',
										),
									),
								),
								array(
									'name' => 'layout',
									'operator' => '=',
									'value' => 'dropdown',
								),
							),
						),
						array(
							'name' => 'dropdown_align',
							'operator' => '!==',
							'value' => 'center',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_item_vertical_padding',
			array(
				'label' => __( 'Vertical Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array( $dropdown_sublevel_links => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};' ),
				'conditions' => $dropdown_sublevels_level_conditions,
			)
		);

		$this->add_responsive_control(
			'dropdown_item_space_between',
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul > li > ' . $widget_selector . '__dropdown-submenu ul li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul > li > ' . $widget_selector . '__dropdown-submenu ul li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__dropdown > ul ' . $widget_selector . '__dropdown-submenu li:not(:first-child)' => 'margin-top: calc( {{SIZE}}{{UNIT}} / 2 ); padding-top: calc( {{SIZE}}{{UNIT}} / 2 );',
				),
				'conditions' => $dropdown_sublevels_level_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'dropdown_item_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => _x( 'Default', 'Border Control', 'cmsmasters-elementor' ),
							'none' => _x( 'None', 'Border Control', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => '',
						'selectors' => array( $dropdown_sublevel_links => 'border-style: {{VALUE}};' ),
					),
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'selectors' => array( $dropdown_sublevel_links => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
				'conditions' => $dropdown_sublevels_level_conditions,
			)
		);

		$this->add_responsive_control(
			'dropdown_item_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array( $dropdown_sublevel_links => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
				'conditions' => $dropdown_sublevels_level_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'dropdown_item_box_shadow',
				'exclude' => array( 'box_shadow_position' ),
				'selector' => $dropdown_sublevel_links,
				'conditions' => $dropdown_sublevels_level_conditions,
			)
		);

		$this->add_control(
			'heading_dropdown_divider',
			array(
				'label' => __( 'Divider', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'dropdown_divider_type',
			array(
				'label' => __( 'Divider Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'doted' => __( 'Doted', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
					'groove' => __( 'Groove', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul > li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul > li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__dropdown > ul > li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__dropdown ' . $widget_selector . '__dropdown-submenu li:not(:first-child)' => 'border-top-style: {{VALUE}};',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_divider_type', '{{VALUE}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_divider_size',
			array(
				'label' => __( 'Divider Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 15 ),
				),
				'default' => array( 'size' => 1 ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul > li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul > li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__dropdown > ul > li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__dropdown ' . $widget_selector . '__dropdown-submenu li:not(:first-child)' => 'border-top-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_divider_size', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array( 'dropdown_divider_type!' => 'none' ),
			)
		);

		$this->add_control(
			'dropdown_divider_color',
			array(
				'label' => __( 'Divider Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-horizontal > ul ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-normal > ul ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle > ul > li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-toggle ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion > ul > li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__main.cmsmasters-layout-vertical.cmsmasters-vertical-type-accordion ' . $widget_selector . '__dropdown-submenu li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__dropdown > ul > li:not(:first-child),
					{{WRAPPER}} ' . $widget_selector . '__dropdown ' . $widget_selector . '__dropdown-submenu li:not(:first-child)' => 'border-top-color: {{VALUE}}',
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'dropdown_divider_color', '{{VALUE}}' ),
				),
				'condition' => array( 'dropdown_divider_type!' => 'none' ),
			)
		);

		$this->end_controls_section();

		$this->megamenu_container_style_controls_section();

		$this->megamenu_item_style_controls_section();

		$conditions = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'layout',
					'operator' => '=',
					'value' => 'horizontal',
				),
				array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '=',
							'value' => 'vertical',
						),
						array(
							'name' => 'vertical_menu_type',
							'operator' => '=',
							'value' => 'normal',
						),
					),
				),
			),
		);

		CmsmastersAnimation::register_sections_controls( $this, true, $conditions );
	}

	/**
	 * Set controls properties.
	 *
	 * @since 1.11.0
	 */
	protected function set_controls_properties() {
		$this->h_start = is_rtl() ? 'right' : 'left';
		$this->h_end = ! is_rtl() ? 'right' : 'left';

		$this->megamenu_condition = array(
			'layout!' => 'dropdown',
		);

		$this->megamenu_conditions = array(
			'name' => 'layout',
			'operator' => '!==',
			'value' => 'dropdown',
		);
	}

	/**
	 * Mega menu content controls section.
	 *
	 * @since 1.11.0
	 */
	protected function megamenu_content_controls_section() {
		$this->start_controls_section(
			'section_content_megamenu',
			array(
				'label' => __( 'Mega Menu', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => $this->megamenu_condition,
			)
		);

		$this->add_control(
			'megamenu_column_max_width',
			array(
				'label' => __( 'Column Max Width', 'cmsmasters-elementor' ),
				'description' => __( 'Changes will be applied correctly after updating and refreshing the page.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 100,
						'max' => 600,
					),
					'vw' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_column_max_width', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_control(
			'megamenu_text_alignment',
			array(
				'label' => __( 'Text Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_start}",
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_end}",
					),
				),
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_text_alignment', '{{VALUE}}' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Mega menu container style controls section.
	 *
	 * @since 1.11.0
	 */
	protected function megamenu_container_style_controls_section() {
		$this->start_controls_section(
			'section_style_megamenu_container',
			array(
				'label' => __( 'Mega Menu Container', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $this->megamenu_condition,
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BACKGROUND_GROUP,
			array(
				'name' => 'megamenu_container_bg',
			)
		);

		$this->add_responsive_control(
			'megamenu_container_top_gap',
			array(
				'label' => __( 'Gap from Top', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => -30,
						'max' => 70,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_container_top_gap', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'megamenu_container_horizontal_gap',
			array(
				'label' => __( 'Horizontal Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => -30,
						'max' => 70,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_container_horizontal_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'layout' => 'vertical',
					'vertical_menu_type' => 'normal',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'megamenu_container_bd',
			)
		);

		$this->add_control(
			'megamenu_container_bd_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_container_bd_radius', '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'megamenu_container_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_container_padding_top', '{{TOP}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'megamenu_container_padding_right', '{{RIGHT}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'megamenu_container_padding_bottom', '{{BOTTOM}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'megamenu_container_padding_left', '{{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BOX_SHADOW_GROUP,
			array(
				'name' => 'megamenu_container',
			)
		);

		$this->add_control(
			'megamenu_column_heading',
			array(
				'label' => __( 'Column', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'megamenu_column_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_column_padding_top', '{{TOP}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'megamenu_column_padding_right', '{{RIGHT}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'megamenu_column_padding_bottom', '{{BOTTOM}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'megamenu_column_padding_left', '{{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'megamenu_column_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_column_gap', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_control(
			'megamenu_column_divider_style',
			array(
				'label' => __( 'Divider Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => esc_html__( 'Default', 'cmsmasters-elementor' ),
					'none' => esc_html__( 'None', 'cmsmasters-elementor' ),
					'solid' => esc_html__( 'Solid', 'cmsmasters-elementor' ),
					'double' => esc_html__( 'Double', 'cmsmasters-elementor' ),
					'dotted' => esc_html__( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => esc_html__( 'Dashed', 'cmsmasters-elementor' ),
					'groove' => esc_html__( 'Groove', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_column_divider_style', '{{VALUE}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'megamenu_column_divider_width',
			array(
				'label' => __( 'Divider Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 15,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_column_divider_width', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'megamenu_column_divider_style!' => array( '', 'none' ),
				),
			)
		);

		$this->add_responsive_control(
			'megamenu_column_divider_height',
			array(
				'label' => __( 'Divider Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_column_divider_height', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'megamenu_column_divider_style!' => array( '', 'none' ),
				),
			)
		);

		$this->add_control(
			'megamenu_column_divider_color',
			array(
				'label' => __( 'Divider Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_column_divider_color', '{{VALUE}}' ),
				),
				'condition' => array(
					'megamenu_column_divider_style!' => array( '', 'none' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function megamenu_item_style_controls_section() {
		$this->start_controls_section(
			'section_style_megamenu_item',
			array(
				'label' => __( 'Mega Menu Item', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $this->megamenu_condition,
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'megamenu_item',
			)
		);

		$this->start_controls_tabs( 'megamenu_item_states_tabs' );

		$megamenu_item_states = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'active' => __( 'Active', 'cmsmasters-elementor' ),
		);

		foreach ( $megamenu_item_states as $state_key => $state_label ) {
			$this->start_controls_tab(
				"megamenu_item_states_{$state_key}_tab",
				array( 'label' => $state_label )
			);

			$this->add_control(
				"megamenu_item_{$state_key}_colors_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "megamenu_item_{$state_key}_colors_color", '{{VALUE}}' ),
					),
				)
			);

			$this->add_control(
				"megamenu_item_{$state_key}_colors_bg",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "megamenu_item_{$state_key}_colors_bg", '{{VALUE}}' ),
					),
				)
			);

			$this->add_control(
				"megamenu_item_{$state_key}_colors_bd",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "megamenu_item_{$state_key}_colors_bd", '{{VALUE}}' ),
					),
					'condition' => array( 'megamenu_item_bd_border!' => 'none' ),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'megamenu_item_horizontal_padding',
			array(
				'label' => __( 'Horizontal Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_item_horizontal_padding', '{{SIZE}}{{UNIT}}' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'megamenu_item_vertical_padding',
			array(
				'label' => __( 'Vertical Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_item_vertical_padding', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'megamenu_item_space_between',
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_item_space_between', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'megamenu_item_bd',
				'fields_options' => array(
					'width' => array( 'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ) ),
				),
				'exclude' => array( 'color' ),
			)
		);

		$this->add_control(
			'megamenu_item_bd_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_item_bd_radius', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BOX_SHADOW_GROUP,
			array(
				'name' => 'megamenu_item',
			)
		);

		$this->add_control(
			'megamenu_item_divider_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_control(
			'megamenu_item_divider_heading',
			array(
				'label' => __( 'Divider', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'megamenu_item_divider_style',
			array(
				'label' => __( 'Divider Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => esc_html__( 'Default', 'cmsmasters-elementor' ),
					'none' => esc_html__( 'None', 'cmsmasters-elementor' ),
					'solid' => esc_html__( 'Solid', 'cmsmasters-elementor' ),
					'double' => esc_html__( 'Double', 'cmsmasters-elementor' ),
					'dotted' => esc_html__( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => esc_html__( 'Dashed', 'cmsmasters-elementor' ),
					'groove' => esc_html__( 'Groove', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_item_divider_style', '{{VALUE}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'megamenu_item_divider_size',
			array(
				'label' => __( 'Divider Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 15,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_item_divider_size', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'megamenu_item_divider_style!' => array( '', 'none' ),
				),
			)
		);

		$this->add_control(
			'megamenu_item_divider_color',
			array(
				'label' => __( 'Divider Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_item_divider_color', '{{VALUE}}' ),
				),
				'condition' => array(
					'megamenu_item_divider_style!' => array( '', 'none' ),
				),
			)
		);

		$this->add_control(
			'megamenu_item_column_title_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_control(
			'megamenu_item_column_title_heading',
			array(
				'label' => __( 'Column Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'megamenu_item_column_title',
			)
		);

		$this->start_controls_tabs( 'megamenu_item_column_title_states_tabs' );

		$megamenu_item_column_title_states = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'active' => __( 'Active', 'cmsmasters-elementor' ),
		);

		foreach ( $megamenu_item_column_title_states as $state_key => $state_label ) {
			$this->start_controls_tab(
				"megamenu_item_column_title_states_{$state_key}_tab",
				array( 'label' => $state_label )
			);

			$this->add_control(
				"megamenu_item_column_title_{$state_key}_colors_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "megamenu_item_column_title_{$state_key}_colors_color", '{{VALUE}}' ),
					),
				)
			);

			$this->add_control(
				"megamenu_item_column_title_{$state_key}_colors_bg",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "megamenu_item_column_title_{$state_key}_colors_bg", '{{VALUE}}' ),
					),
				)
			);

			$this->add_control(
				"megamenu_item_column_title_{$state_key}_colors_bd",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "megamenu_item_column_title_{$state_key}_colors_bd", '{{VALUE}}' ),
					),
					'condition' => array( 'megamenu_item_column_title_bd_border!' => 'none' ),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'megamenu_item_column_title_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_item_column_title_padding_top', '{{TOP}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'megamenu_item_column_title_padding_right', '{{RIGHT}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'megamenu_item_column_title_padding_bottom', '{{BOTTOM}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'megamenu_item_column_title_padding_left', '{{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'megamenu_item_column_title_space_between',
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_item_column_title_space_between', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'megamenu_item_column_title_bd',
				'fields_options' => array(
					'width' => array( 'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ) ),
				),
				'exclude' => array( 'color' ),
			)
		);

		$this->add_control(
			'megamenu_item_column_title_bd_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_item_column_title_bd_radius', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BOX_SHADOW_GROUP,
			array(
				'name' => 'megamenu_item_column_title',
			)
		);

		$this->add_control(
			'megamenu_item_column_title_divider_heading',
			array(
				'label' => __( 'Divider', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'megamenu_item_column_title_divider_style',
			array(
				'label' => __( 'Divider Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => esc_html__( 'Default', 'cmsmasters-elementor' ),
					'none' => esc_html__( 'None', 'cmsmasters-elementor' ),
					'solid' => esc_html__( 'Solid', 'cmsmasters-elementor' ),
					'double' => esc_html__( 'Double', 'cmsmasters-elementor' ),
					'dotted' => esc_html__( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => esc_html__( 'Dashed', 'cmsmasters-elementor' ),
					'groove' => esc_html__( 'Groove', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_item_column_title_divider_style', '{{VALUE}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'megamenu_item_column_title_divider_size',
			array(
				'label' => __( 'Divider Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 15,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_item_column_title_divider_size', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'megamenu_item_column_title_divider_style!' => array( '', 'none' ),
				),
			)
		);

		$this->add_control(
			'megamenu_item_column_title_divider_color',
			array(
				'label' => __( 'Divider Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'megamenu_item_column_title_divider_color', '{{VALUE}}' ),
				),
				'condition' => array(
					'megamenu_item_column_title_divider_style!' => array( '', 'none' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render menu widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Fixed the condition for displaying the icon indicator for php 7.4.
	 * @since 1.20.1 Fixed the display of the indicator and pointer in menu items.
	 */
	protected function render() {
		$available_menus = $this->get_available_menus();

		if ( ! $available_menus ) {
			return;
		}

		$settings = $this->get_active_settings();
		$nav_menu = ( isset( $settings['nav_menu'] ) ? $settings['nav_menu'] : '' );

		if ( ! is_nav_menu( $nav_menu ) ) {
			if ( is_admin() ) {
				CmsmastersUtils::render_alert( esc_html__( 'Please choose navigation menu', 'cmsmasters-elementor' ) );
			}

			return;
		}

		$layout = ( isset( $settings['layout'] ) ? $settings['layout'] : '' );

		$args = array(
			'echo' => false,
			'menu' => $nav_menu,
			'menu_class' => $this->get_widget_class() . '__container-inner',
			'menu_id' => 'menu-' . $this->get_nav_menu_index() . '-' . $this->get_id(),
			'fallback_cb' => '__return_empty_string',
			'container' => '',
		);

		add_filter( 'nav_menu_link_attributes', array( $this, 'get_link_classes' ), 10, 4 );
		add_filter( 'nav_menu_item_args', array( $this, 'change_menu_item_css_classes' ), 10, 3 );
		add_filter( 'nav_menu_submenu_css_class', array( $this, 'get_sub_menu_classes' ), 10, 3 );
		add_filter( 'walker_nav_menu_start_el', array( $this, 'filter_walker_nav_menu_start_el' ), 10, 4 );
		add_filter( 'nav_menu_css_class', array( $this, 'filter_nav_menu_css_class' ), 10, 4 );
		add_filter( 'wp_nav_menu_objects', array( $this, 'modify_menu_items' ), 10, 2 );

		$menu_html = wp_nav_menu( $args );

		if ( empty( $menu_html ) ) {
			return;
		}

		$args['menu_id'] = ( 'cmsmasters_menu-' . $this->get_nav_menu_index() . '-' . $this->get_id() );

		remove_filter( 'nav_menu_link_attributes', array( $this, 'get_link_classes' ) );
		remove_filter( 'nav_menu_item_args', array( $this, 'change_menu_item_css_classes' ) );
		remove_filter( 'nav_menu_submenu_css_class', array( $this, 'get_sub_menu_classes' ) );
		remove_filter( 'walker_nav_menu_start_el', array( $this, 'filter_walker_nav_menu_start_el' ) );
		remove_filter( 'nav_menu_css_class', array( $this, 'filter_nav_menu_css_class' ) );
		remove_filter( 'wp_nav_menu_objects', array( $this, 'modify_menu_items' ) );

		$vertical_menu_type = ( isset( $settings['vertical_menu_type'] ) ? $settings['vertical_menu_type'] : '' );

		if ( 'dropdown' !== $layout ) {
			$nav_menu_name = ( isset( $settings['nav_menu_name'] ) ? $settings['nav_menu_name'] : '' );

			if ( '' !== $nav_menu_name ) {
				$default_menu_name = $nav_menu_name;
			} else {
				$default_menu_name = isset( $available_menus[ $nav_menu ] ) ? $available_menus[ $nav_menu ] : '';
			}

			$this->add_render_attribute(
				'main-menu',
				array(
					'class' => array(
						$this->get_widget_class() . '__main',
						$this->get_widget_class() . '__container',
						'cmsmasters-layout-' . esc_attr( $layout ),
					),
					'aria-label' => esc_attr( $default_menu_name ),
				)
			);

			$indicator_main_popover = ( isset( $settings['indicator_main_popover'] ) ? $settings['indicator_main_popover'] : '' );
			$indicator_submenu_popover = ( isset( $settings['indicator_submenu_popover'] ) ? $settings['indicator_submenu_popover'] : '' );

			if ( empty( $indicator_main_popover ) ) {
				$this->add_render_attribute( 'main-menu', 'class', 'cmsmasters-indicator-main-empty' );
			}

			if ( empty( $indicator_submenu_popover ) ) {
				$this->add_render_attribute( 'main-menu', 'class', 'cmsmasters-indicator-submenu-empty' );
			}

			$pointer = ( isset( $settings['pointer'] ) ? $settings['pointer'] : '' );

			if ( 'none' === $pointer ) {
				$this->add_render_attribute( 'main-menu', 'class', 'cmsmasters-pointer-menu-none' );
			}

			if ( 'vertical' === $layout ) {
				$this->add_render_attribute( 'main-menu', 'class', 'cmsmasters-vertical-type-' . esc_attr( $vertical_menu_type ) );

				if ( 'side' === $vertical_menu_type ) {
					$this->add_render_attribute( 'main-menu', 'class', 'cmsmasters-side-content-' . $this->get_id() );
				}

				if (
					( isset( $settings['open_by_click'] ) && $settings['open_by_click'] ) &&
					( 'toggle' === $vertical_menu_type || 'accordion' === $vertical_menu_type )
				) {
					$this->add_render_attribute( 'main-menu', 'class', 'cmsmasters-nav-menu-open-link' );
				}
			}

			echo '<nav ' . $this->get_render_attribute_string( 'main-menu' ) . '>';

				Utils::print_unescaped_internal_string( $menu_html );

			echo '</nav>';
		}

		$this->get_toggle();

		$this->get_dropdown();
	}

	/**
	 * @since 1.0.0
	 * @since 1.16.0 Fixed unnecessary animation for mega menu.
	 *
	 * @return array menus item animation class
	 */
	public function change_menu_item_css_classes( $args, $item, $depth ) {
		$settings = $this->get_active_settings();

		$layout = $settings['layout'];
		$is_main_menu = isset( $args->menu ) && $args->menu === $settings['nav_menu'];

		if (
			0 === $depth &&
			$is_main_menu &&
			( 'horizontal' === $layout || ( 'vertical' === $layout && 'normal' === $settings['vertical_menu_type'] ) ) &&
			'text' !== $settings['pointer']
		) {
			$args->link_after = '<span class="' . CmsmastersAnimation::get_animation_class() . '"></span>';
		}

		$args->cmsmasters_megamenu = array(
			'status' => $item->cmsmasters_megamenu['status'],
			'type' => $item->cmsmasters_megamenu['type'],
			'inner_type' => $item->cmsmasters_megamenu['inner_type'],
		);

		return $args;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return array menus list
	 */
	public function get_available_menus() {
		$menus = wp_list_pluck(
			wp_get_nav_menus(),
			'name',
			'term_id'
		);

		return $menus;
	}

	protected $nav_menu_index = 1;

	/**
	 * @since 1.0.0
	 *
	 * @return menu index
	 */
	protected function get_nav_menu_index() {
		return $this->nav_menu_index++;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return link classes list
	 */
	public function get_link_classes( $atts, $item, $args, $depth ) {
		$settings = $this->get_active_settings();

		$classes = ( $depth ? $this->get_widget_class() . '__dropdown-item sub' : $this->get_widget_class() . '__dropdown-item' );

		$is_anchor = false !== strpos( $atts['href'], '#' );

		if ( ! $is_anchor && in_array( 'current-menu-item', $item->classes, true ) ) {
			$classes .= ' ' . $this->get_widget_class() . '__item-active';
		}

		if ( $is_anchor ) {
			$classes .= ' ' . $this->get_widget_class() . '__item-anchor';
		}

		if ( empty( $atts['class'] ) ) {
			$atts['class'] = $classes;
		} else {
			$atts['class'] .= ' ' . $classes;
		}

		$atts['aria-label'] = 'Menu item';

		$indicator_main_animation = $settings['indicator_main_animation'];
		$indicator_submenu_animation = $settings['indicator_submenu_animation'];
		$vertical_menu_type = $settings['vertical_menu_type'];

		if ( 0 === $depth ) {
			$atts['class'] .= ' ' . $this->get_widget_class() . '__item-link-top';

			if ( isset( $indicator_main_animation ) && 'none' !== $indicator_main_animation ) {
				$atts['class'] .= ' cmsmasters-arrow-animation-' . $indicator_main_animation;
			}

			if (
				isset( $indicator_submenu_animation ) &&
				'none' !== $indicator_submenu_animation &&
				'vertical' === $settings['layout'] &&
				( 'toggle' === $vertical_menu_type || 'accordion' === $vertical_menu_type )
			) {
				$atts['class'] .= ' cmsmasters-arrow-animation-' . $indicator_submenu_animation;
			}
		} else {
			$atts['class'] .= ' ' . $this->get_widget_class() . '__item-link-sub';

			if ( isset( $indicator_submenu_animation ) && 'none' !== $indicator_submenu_animation ) {
				$atts['class'] .= ' cmsmasters-arrow-animation-' . $indicator_submenu_animation;
			}
		}

		return $atts;
	}

	/**
	 * @since 1.0.0
	 * @since 1.17.3 Added title attr.
	 *
	 * @return added tag span for link
	 */
	public function added_span_in_link( $item, $depth ) {
		$settings = $this->get_active_settings();

		$layout = $settings['layout'];
		$vertical_menu_type = $settings['vertical_menu_type'];

		$new_title = '';

		$new_title .= '<span class="' . $this->get_widget_class() . '__item-text-wrap';

		if (
			0 === $depth &&
			( 'horizontal' === $layout || ( 'vertical' === $layout && 'normal' === $vertical_menu_type ) ) &&
			'text' === $settings['pointer']
		) {
			$new_title .= ' ' . CmsmastersAnimation::get_animation_class();
		}

		$new_title .= '">' .
			'<span class="' . $this->get_widget_class() . '__item-text">' .
				$item->title .
			'</span>';

			$indicator_main = ( isset( $settings['indicator_main'] ) ? $settings['indicator_main'] : '' );
			$indicator_submenu = ( isset( $settings['indicator_submenu'] ) ? $settings['indicator_submenu'] : '' );

		if (
			( 'horizontal' === $layout || ( 'vertical' === $layout && 'normal' === $vertical_menu_type ) ) &&
			( ! empty( $indicator_main['value'] ) || ! empty( $indicator_submenu['value'] ) )
		) {
			$new_title .= '<span class="' . $this->get_widget_class() . '__arrow" role="button" title="Menu item arrow" tabindex="0">' .
				CmsmastersUtils::get_render_icon( ( 0 === $depth ) ? $indicator_main : $indicator_submenu, array( 'aria-hidden' => 'true' ) ) .
			'</span>';
		}

		if (
			( 'dropdown' === $layout || ( 'vertical' === $layout && ( 'toggle' === $vertical_menu_type || 'accordion' === $vertical_menu_type ) ) ) &&
			! empty( $indicator_submenu['value'] )
		) {
			$new_title .= '<span class="' . $this->get_widget_class() . '__arrow" role="button" title="Menu item arrow" tabindex="0">' .
				CmsmastersUtils::get_render_icon( $indicator_submenu, array( 'aria-hidden' => 'true' ) ) .
			'</span>';
		}

		$new_title .= '</span>';

		return $new_title;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return sub menu classes list
	 */
	public function get_sub_menu_classes( $classes, $args, $depth ) {
		$is_megamenu = false;

		if (
			0 === $depth &&
			isset( $args->cmsmasters_megamenu['status'] ) &&
			'enable' === $args->cmsmasters_megamenu['status'] &&
			isset( $args->cmsmasters_megamenu['type'] ) &&
			'wp-menu' === $args->cmsmasters_megamenu['type']
		) {
			$is_megamenu = true;

			$classes[] = $this->get_widget_class() . '__megamenu-wp-menu-container';
			$classes[] = 'cmsmasters-megamenu-container';
		}

		if (
			isset( $args->cmsmasters_megamenu['inner_type'] ) &&
			'standard' !== $args->cmsmasters_megamenu['inner_type']
		) {
			$is_megamenu = true;

			$classes[] = $this->get_widget_class() . '__megamenu-wp-menu-inner-list';
		}

		if ( ! $is_megamenu ) {
			$classes[] = $this->get_widget_class() . '__dropdown-submenu';
		}

		return $classes;
	}

	/**
	 * Filters a menu item's starting output.
	 *
	 * @since 1.11.0
	 *
	 * @param string $item_output The menu item's starting HTML output.
	 * @param WP_Post $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param stdClass $args An object of wp_nav_menu() arguments.
	 *
	 * @return string Filtered menu item starting output.
	 */
	public function filter_walker_nav_menu_start_el( $item_output, $item, $depth, $args ) {
		$settings = $this->get_active_settings();

		if (
			$depth > 0 ||
			! isset( $item->cmsmasters_megamenu['status'] ) ||
			'enable' !== $item->cmsmasters_megamenu['status'] ||
			! isset( $item->cmsmasters_megamenu['type'] )
		) {
			return $item_output;
		}

		if ( 'template' === $item->cmsmasters_megamenu['type'] ) {
			if ( empty( $item->cmsmasters_megamenu['template'] ) ) {
				return $item_output;
			}

			$item_output .= '<div class="' . $this->get_widget_class() . '__megamenu-template-container cmsmasters-megamenu-container">
				<div class="' . $this->get_widget_class() . '__megamenu-template-container-inner">' .
					Plugin::$instance->frontend->get_builder_content_for_display( $item->cmsmasters_megamenu['template'] ) .
				'</div>
			</div>';
		}

		return $item_output;
	}

	/**
	 * Filter nav menu css class.
	 *
	 * @since 1.11.0
	 *
	 * @param string[] $classes Array of the CSS classes that are applied to the menu item's `<li>` element.
	 * @param WP_Post $item The current menu item.
	 * @param stdClass $args An object of wp_nav_menu() arguments.
	 * @param int $depth Depth of menu item. Used for padding.
	 *
	 * @return array Filtered css class.
	 */
	public function filter_nav_menu_css_class( $classes, $item, $args, $depth ) {
		if (
			$depth > 0 ||
			! isset( $item->cmsmasters_megamenu['status'] ) ||
			'enable' !== $item->cmsmasters_megamenu['status'] ||
			! isset( $item->cmsmasters_megamenu['type'] ) ||
			(
				'template' === $item->cmsmasters_megamenu['type'] &&
				empty( $item->cmsmasters_megamenu['template'] )
			)
		) {
			return $classes;
		}

		$classes[] = 'cmsmasters-megamenu-type-' . esc_attr( $item->cmsmasters_megamenu['type'] );

		if ( 'template' === $item->cmsmasters_megamenu['type'] && ! empty( $item->cmsmasters_megamenu['template'] ) ) {
			$classes[] = 'menu-item-has-children';
		}

		return $classes;
	}

	/**
	 * Render toggle menu
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_toggle() {
		$settings = $this->get_active_settings();

		$dropdown_toggle_type = $settings['dropdown_toggle_type'];

		$this->add_render_attribute( 'toggle-container', 'class', array(
			$this->get_widget_class() . '__toggle-container',
			'cmsmasters-layout-' . esc_attr( $settings['layout'] ),
			'cmsmasters-menu-dropdown-type-' . esc_attr( $settings['dropdown_menu_type'] ),
		) );

		echo '<div ' . $this->get_render_attribute_string( 'toggle-container' ) . '>' .
			'<div class="' . $this->get_widget_class() . '__toggle cmsmasters-icon-align-' . esc_attr( $settings['dropdown_toggle_icon_align'] ) . '" role="button" tabindex="0">';

		if ( 'text' !== $dropdown_toggle_type ) {
			$dropdown_toggle_icon = ( isset( $settings['dropdown_toggle_icon'] ) ? $settings['dropdown_toggle_icon'] : '' );

			echo '<span class="cmsmasters-toggle-icon">';

			$dropdown_toggle_icon_att = array( 'aria-hidden' => 'true' );

			if ( 'icon' === $dropdown_toggle_type ) {
				$dropdown_toggle_icon_att = array_merge(
					$dropdown_toggle_icon_att,
					array( 'aria-label' => 'Menu Toggle' ),
				);
			}

			if ( '' !== $dropdown_toggle_icon['value'] ) {
				Icons_Manager::render_icon( $dropdown_toggle_icon, $dropdown_toggle_icon_att );
			} else {
				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-bars',
						'library' => 'fa-solid',
					),
					$dropdown_toggle_icon_att
				);
			}

			echo '</span>';

			$dropdown_toggle_icon_active = ( isset( $settings['dropdown_toggle_icon_active'] ) ? $settings['dropdown_toggle_icon_active'] : '' );

			echo '<span class="cmsmasters-toggle-icon-active">';

			if ( 'icon' === $dropdown_toggle_type ) {
				$dropdown_toggle_icon_att = array_merge(
					$dropdown_toggle_icon_att,
					array( 'aria-label' => 'Menu Active Toggle' ),
				);
			}

			if ( '' !== $dropdown_toggle_icon_active['value'] ) {
				Icons_Manager::render_icon( $dropdown_toggle_icon_active, $dropdown_toggle_icon_att );
			} else {
				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-times',
						'library' => 'fa-solid',
					),
					$dropdown_toggle_icon_att
				);
			}

			echo '</span>';
		}

		if ( 'icon' !== $dropdown_toggle_type ) {
			echo '<span class="' . $this->get_widget_class() . '__toggle-label">';

			$dropdown_toggle_text = $settings['dropdown_toggle_text'];

			if ( '' !== $dropdown_toggle_text ) {
				echo esc_html( $dropdown_toggle_text );
			} else {
				echo esc_html__( 'Menu', 'cmsmasters-elementor' );
			}

			echo '</span>';
		}

			echo '</div>' .
		'</div>';
	}

	/**
	 * Render dropdown type menu
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_dropdown() {
		$settings = $this->get_active_settings();

		$layout = ( isset( $settings['layout'] ) ? $settings['layout'] : '' );
		$breakpoints = ( isset( $settings['dropdown_breakpoints'] ) ? $settings['dropdown_breakpoints'] : '' );

		if (
			'none' === $breakpoints &&
			( 'horizontal' === $layout || ( 'vertical' === $layout && 'normal' === $settings['vertical_menu_type'] ) )
		) {
			return;
		}

		$dropdown_menu_type = $settings['dropdown_menu_type'];

		if ( 'popup' === $dropdown_menu_type || 'offcanvas' === $dropdown_menu_type ) {
			echo '<div class="' . $this->get_widget_class() . '__dropdown-container">';
		}

		echo $this->get_dropdown_nav();

		if ( 'popup' === $dropdown_menu_type || 'offcanvas' === $dropdown_menu_type ) {
			echo '</div>';
		}
	}

	/**
	 * Render dropdown nav for dropdown type menu
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Fixed the condition for displaying the icon indicator for php 7.4.
	 * @since 1.12.1 Check navigation menu.
	 * @since 1.20.1 Fixed the display of the indicator and pointer in menu items.
	 */
	public function get_dropdown_nav() {
		$settings = $this->get_active_settings();

		if ( ! is_nav_menu( $settings['nav_menu'] ) ) {
			if ( is_admin() ) {
				CmsmastersUtils::render_alert( esc_html__( 'Please choose navigation menu', 'cmsmasters-elementor' ) );
			}

			return;
		}

		$layout = ( isset( $settings['layout'] ) ? $settings['layout'] : '' );
		$dropdown_menu_type = ( isset( $settings['dropdown_menu_type'] ) ? $settings['dropdown_menu_type'] : '' );
		$nav_menu_name = ( isset( $settings['nav_menu_name'] ) ? $settings['nav_menu_name'] : '' );
		$available_menus = $this->get_available_menus();
		$nav_menu = ( isset( $settings['nav_menu'] ) ? $settings['nav_menu'] : '' );

		if ( '' !== $nav_menu_name ) {
			$default_menu_name = $nav_menu_name;
		} else {
			$default_menu_name = isset( $available_menus[ $nav_menu ] ) ? $available_menus[ $nav_menu ] : '';
		}

		$this->add_render_attribute(
			'dropdown-menu',
			array(
				'class' => array(
					$this->get_widget_class() . '__dropdown',
					$this->get_widget_class() . '__container',
					'cmsmasters-layout-' . esc_attr( $layout ),
					'cmsmasters-menu-dropdown-type-' . esc_attr( $dropdown_menu_type ),
				),
				'aria-label' => esc_attr( $default_menu_name ),
			)
		);

		$indicator_main_popover = ( isset( $settings['indicator_main_popover'] ) ? $settings['indicator_main_popover'] : '' );
		$indicator_submenu_popover = ( isset( $settings['indicator_submenu_popover'] ) ? $settings['indicator_submenu_popover'] : '' );

		if ( empty( $indicator_main_popover ) ) {
			$this->add_render_attribute( 'dropdown-menu', 'class', 'cmsmasters-indicator-main-empty' );
		}

		if ( empty( $indicator_submenu_popover ) ) {
			$this->add_render_attribute( 'dropdown-menu', 'class', 'cmsmasters-indicator-submenu-empty' );
		}

		$pointer = ( isset( $settings['pointer'] ) ? $settings['pointer'] : '' );

		if ( 'none' === $pointer ) {
			$this->add_render_attribute( 'dropdown-menu', 'class', 'cmsmasters-pointer-menu-none' );
		}

		$indicator_submenu_animation = $settings['indicator_submenu_animation'];

		if ( 'none' !== $indicator_submenu_animation ) {
			$this->add_render_attribute( 'dropdown-menu', 'class', 'cmsmasters-arrow-animation-' . esc_attr( $indicator_submenu_animation ) );
		}

		if ( 'vertical' === $layout ) {
			$this->add_render_attribute( 'dropdown-menu', 'class', 'cmsmasters-vertical-type-' . esc_attr( $settings['vertical_menu_type'] ) );
		}

		echo '<nav ' . $this->get_render_attribute_string( 'dropdown-menu' ) . '>';

		if ( 'offcanvas' === $dropdown_menu_type ) {
			echo $this->get_dropdown_close();
		}

		$args = array(
			'echo' => false,
			'menu' => $settings['nav_menu'],
			'menu_class' => $this->get_widget_class() . '__container-inner',
			'menu_id' => 'menu-' . $this->get_nav_menu_index() . '-' . $this->get_id(),
			'fallback_cb' => '__return_empty_string',
			'container' => '',
		);

		$args['menu_class'] .= ' cmsmasters-nav-menu-dropdown';

		$args['menu_id'] = 'cmsmasters_menu-' . $this->get_nav_menu_index() . '-' . $this->get_id();

		add_filter( 'nav_menu_submenu_css_class', array( $this, 'get_sub_menu_classes' ), 10, 3 );
		add_filter( 'nav_menu_link_attributes', array( $this, 'get_dropdown_link_classes' ), 10, 4 );
		add_filter( 'walker_nav_menu_start_el', array( $this, 'filter_walker_nav_menu_start_el' ), 10, 4 );
		add_filter( 'nav_menu_css_class', array( $this, 'filter_nav_menu_css_class' ), 10, 4 );
		add_filter( 'wp_nav_menu_objects', array( $this, 'modify_dropdown_menu_items' ), 10, 2 );

		echo wp_nav_menu( $args );

		remove_filter( 'nav_menu_submenu_css_class', array( $this, 'get_sub_menu_classes' ) );
		remove_filter( 'nav_menu_link_attributes', array( $this, 'get_dropdown_link_classes' ) );
		remove_filter( 'walker_nav_menu_start_el', array( $this, 'filter_walker_nav_menu_start_el' ) );
		remove_filter( 'nav_menu_css_class', array( $this, 'filter_nav_menu_css_class' ) );
		remove_filter( 'wp_nav_menu_objects', array( $this, 'modify_dropdown_menu_items' ) );

		if ( 'popup' === $dropdown_menu_type ) {
			echo $this->get_dropdown_close();
		}

		echo '</nav>';
	}

	/**
	 * @since 1.2.0
	 *
	 * @return dropdown link classes list and indicator icon
	 */
	public function get_dropdown_link_classes( $atts, $item, $args, $depth ) {
		$classes = ( $depth ? $this->get_widget_class() . '__dropdown-item sub' : $this->get_widget_class() . '__dropdown-item' );

		if ( empty( $atts['class'] ) ) {
			$atts['class'] = $classes;
		} else {
			$atts['class'] .= ' ' . $classes;
		}

		$atts['aria-label'] = 'Menu item';

		return $atts;
	}

	/**
	 * @since 1.2.0
	 * @since 1.17.3 Added title attr.
	 *
	 * @return added tag span for dropdown link and indicator icon
	 */
	public function added_span_in_dropdown_link( $item ) {
		$settings = $this->get_active_settings();

		$new_title = '';

		$new_title .= '<span class="' . $this->get_widget_class() . '__item-text-wrap">' .
			'<span class="' . $this->get_widget_class() . '__item-text">' .
				$item->title .
			'</span>';

			$indicator_submenu = ( isset( $settings['indicator_submenu'] ) ? $settings['indicator_submenu'] : '' );

		if ( ! empty( $indicator_submenu['value'] ) ) {
			$new_title .= '<span class="' . $this->get_widget_class() . '__arrow" role="button" title="Menu item arrow" tabindex="0">' .
				CmsmastersUtils::get_render_icon( $indicator_submenu, array( 'aria-hidden' => 'true' ) ) .
			'</span>';
		}

		$new_title .= '</span>';

		return $new_title;
	}

	/**
	 * Modify menu items
	 *
	 * Modify menu items final HTML.
	 *
	 * @since 1.15.0 Changing menu items html in WordPress version 6.7 and above.
	 */
	public function modify_menu_items( $items, $args ) {
		$settings = $this->get_active_settings();

		$depths = array();

		foreach ( $items as $item ) {
			$item->depth = ( isset( $depths[ $item->menu_item_parent ] ) ? $depths[ $item->menu_item_parent ] + 1 : 0 );
			$depths[ $item->ID ] = $item->depth;
			$layout = ( isset( $settings['layout'] ) ? $settings['layout'] : '' );
			$vertical_menu_type = ( isset( $settings['vertical_menu_type'] ) ? $settings['vertical_menu_type'] : '' );
			$depth = $item->depth;

			if ( ( 'horizontal' === $layout || ( 'vertical' === $layout && 'normal' === $vertical_menu_type ) ) && 0 === $depth ) {
				$item->title = $this->added_span_in_link( $item, $depth );
			} else {
				$item->title = $this->added_span_in_dropdown_link( $item );
			}
		}

		return $items;
	}

	/**
	 * Modify dropdown menu items
	 *
	 * Modify dropdown menu items final HTML.
	 *
	 * @since 1.15.0 Changing dropdown menu items html in WordPress version 6.7 and above.
	 */
	public function modify_dropdown_menu_items( $items, $args ) {
		foreach ( $items as $item ) {
			$item->title = $this->added_span_in_dropdown_link( $item );
		}

		return $items;
	}

	/**
	 * Render dropdown close button
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.17.3 Added title attr.
	 */
	public function get_dropdown_close() {
		$settings = $this->get_active_settings();

		$dropdown_menu_type = $settings['dropdown_menu_type'];
		$close_type = $settings['popup_offcanvas_close_type'];
		$close_text = $settings['close_text'];

		if ( 'text' === $close_type && '' === $close_text ) {
			return;
		}

		$this->add_render_attribute( 'dropdown-close-container', 'class', array(
			$this->get_widget_class() . '__dropdown-close-container',
			'cmsmasters-menu-dropdown-type-' . esc_attr( $dropdown_menu_type ),
		) );

		echo '<div ' . $this->get_render_attribute_string( 'dropdown-close-container' ) . '">';
			echo '<div class="' . $this->get_widget_class() . '__dropdown-close" role="button" title="Dropdown close" tabindex="0">';

		if ( 'text' !== $close_type ) {
			$close_icon = ( isset( $settings['close_icon'] ) ? $settings['close_icon'] : '' );
			$close_icon_att = array( 'aria-hidden' => 'true' );

			if ( 'icon' === $close_type ) {
				$close_icon_att = array_merge(
					$close_icon_att,
					array( 'aria-label' => 'Close Button' ),
				);
			}

			if ( '' !== $close_icon['value'] ) {
				Icons_Manager::render_icon( $close_icon, $close_icon_att );
			} else {
				Icons_Manager::render_icon( array(
					'value' => 'fas fa-times',
					'library' => 'fa-solid',
				), $close_icon_att );
			}
		}

		if ( 'icon' !== $close_type ) {
			echo '<span class="' . $this->get_widget_class() . '__dropdown-close-label">';

			if ( '' !== $close_text ) {
				echo esc_html( $close_text );
			} else {
				echo esc_html__( 'Close', 'cmsmasters-elementor' );
			}

			echo '</span>';
		}

			echo '</div>';
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
			array(
				'field' => 'dropdown_toggle_text',
				'type' => esc_html__( 'Dropdown Toggle Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'close_text',
				'type' => esc_html__( 'Close Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
