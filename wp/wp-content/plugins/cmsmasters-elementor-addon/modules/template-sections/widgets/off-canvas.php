<?php
namespace CmsmastersElementor\Modules\TemplateSections\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Animation\Classes\Animation as AnimationModule;
use CmsmastersElementor\Modules\TemplateSections\Traits\Site_Widget;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Plugin as CmsmastersPlugin;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon offcanvas area widget.
 *
 * Addon widget that display site offcanvas area.
 *
 * @since 1.0.0
 */
class Off_Canvas extends Base_Widget {

	use Site_Widget;

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
		return $this->get_name_prefix() . 'offcanvas';
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
		return __( 'Offcanvas', 'cmsmasters-elementor' );
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
		return 'cmsicon-offcanvas';
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
			'offcanvas',
			'off-canvas',
			'canvas',
			'navigation',
			'nav',
			'menu',
			'template',
			'page',
			'section',
			'block',
		);
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the widget requires.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget script dependencies.
	 */
	public function get_script_depends() {
		return array_merge( array( 'perfect-scrollbar-js' ), parent::get_script_depends() );
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.16.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'widget-cmsmasters-offcanvas',
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

	private $box_to_down_count = 0;

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-offcanvas';
	}

	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
	}

	/**
	 * LazyLoad widget use control.
	 *
	 * @since 1.11.1
	 *
	 * @return bool true - with control, false - without control.
	 */
	public function lazyload_widget_use_control() {
		return true;
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added `Position` control for trigger.
	 * Added background gradient, `Border Color`, `Box Shadow`, `Text Shadow` and `Border Type`
	 * controls on normal state for trigger.
	 * Added background gradient, `Border Color`, `Box Shadow`, `Text Shadow`, `Border Radius`,
	 * `Text Decoration` controls on hover and active state for trigger.
	 * Fixed animation for trigger icon. Deleted exclude 'box_shadow_position' in all `Box Shadow`
	 * controls for trigger. Added 'em', '%' and 'vw' size units for `Icon Size` and `Padding` controls.
	 * Added `Background Overlay` control for container overlay. Added new content type `Site Logo`
	 * and used him to default. Added new controls for content type `Site Logo` in content and style section.
	 * @since 1.2.0 Added `Top Gap` control for dropdown menu. Fixed the visibility of the Padding control
	 * for the trigger button.
	 * @since 1.2.1 Added `Box Shadow` control for canvas.
	 * @since 1.2.3 Fixed path in selectors for `Width`, `Background Color`, `Padding`, `Border` and
	 * `Box Shadow` controls for offcanvas content.
	 * @since 1.3.8 Added controls for the menu item indicator.
	 * @since 1.5.0 Added `Thumb` and `Track` controls for scrollbar.
	 * @since 1.10.0 Add second logo functionality for mode switcher.
	 * @since 1.10.1 Added `Position` control for trigger icon.
	 * @since 1.11.4 Added `Side Gap` control for scroll bar.
	 * @since 1.11.9 Fixed display of container templates in saved sections.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 * @since 1.14.4 Added `Side Gap` control for close button.
	 */
	protected function register_controls() {
		$widget_selector = $this->get_widget_selector();

		$this->start_controls_section(
			'section_general',
			array( 'label' => __( 'General', 'cmsmasters-elementor' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'frontend_available' => true,
			)
		);

		$content_types = array(
			'logo' => __( 'Site Logo', 'cmsmasters-elementor' ),
			'custom' => __( 'Custom Content', 'cmsmasters-elementor' ),
			'navigation' => __( 'Navigation', 'cmsmasters-elementor' ),
			'sidebar' => __( 'Sidebar', 'cmsmasters-elementor' ),
			'section' => __( 'Saved Section', 'cmsmasters-elementor' ),
			'template' => __( 'Saved Page Template', 'cmsmasters-elementor' ),
		);

		if ( CmsmastersUtils::is_pro() ) {
			$content_types['widget'] = __( 'Global Widget', 'cmsmasters-elementor' );
		}

		$repeater->add_control(
			'content_type',
			array(
				'label' => __( 'Content Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $content_types,
				'default' => 'logo',
			)
		);

		$repeater->add_control(
			'logo_image_source',
			array(
				'label' => __( 'Source', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => __( 'Website Logo', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom Image', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'label_block' => false,
				'condition' => array( 'content_type' => 'logo' ),
			)
		);

		$repeater->add_control(
			'logo_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'image' => array(
						'title' => __( 'Image', 'cmsmasters-elementor' ),
						'description' => __( 'Show only Image', 'cmsmasters-elementor' ),
					),
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => __( 'Show only Text', 'cmsmasters-elementor' ),
					),
				),
				'default' => CmsmastersUtils::get_kit_option( 'cmsmasters_logo_type', 'image' ),
				'label_block' => false,
				'prefix_class' => 'cmsmasters-logo-type-',
				'render_type' => 'template',
				'condition' => array(
					'content_type' => 'logo',
					'logo_image_source' => 'custom',
				),
			)
		);

		$repeater->add_control(
			'logo_image',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => array(
					'content_type' => 'logo',
					'logo_image_source' => 'custom',
					'logo_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'logo_image_retina',
			array(
				'label' => esc_html__( 'Retina Logo Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array(
					'content_type' => 'logo',
					'logo_image_source' => 'custom',
					'logo_type' => 'image',
					'logo_image[id]!' => '',
				),
			)
		);

		$repeater->start_popover();

			$repeater->add_control(
				'logo_image_2x',
				array(
					'type' => Controls_Manager::MEDIA,
					'condition' => array(
						'content_type' => 'logo',
						'logo_image_source' => 'custom',
						'logo_type' => 'image',
						'logo_image[id]!' => '',
						'logo_image_retina' => 'yes',
					),
				)
			);

		$repeater->end_popover();

		$repeater->add_control(
			'logo_image_second_toggle',
			array(
				'label' => esc_html__( 'Second Logo Image', 'cmsmasters-elementor' ),
				'description' => sprintf(
					'%1$s <a href="https://docs.cmsmasters.net/mode-switcher/" target="_blank">%2$s</a>.',
					__( 'Image that will be applied when using the', 'cmsmasters-elementor' ),
					__( 'Mode Switcher', 'cmsmasters-elementor' )
				),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array(
					'content_type' => 'logo',
					'logo_image_source' => 'custom',
					'logo_type' => 'image',
					'logo_image[id]!' => '',
				),
			)
		);

		$repeater->start_popover();

		$repeater->add_control(
			'logo_image_second',
			array(
				'label' => esc_html__( 'Second Logo Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => array(
					'content_type' => 'logo',
					'logo_image_source' => 'custom',
					'logo_type' => 'image',
					'logo_image[id]!' => '',
					'logo_image_second_toggle' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'logo_image_2x_second',
			array(
				'label' => esc_html__( 'Second Retina Logo Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => array(
					'content_type' => 'logo',
					'logo_image_source' => 'custom',
					'logo_type' => 'image',
					'logo_image[id]!' => '',
					'logo_image_second_toggle' => 'yes',
				),
			)
		);

		$repeater->end_popover();

		$site_logo_title_text = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_title_text', '' );

		$repeater->add_control(
			'logo_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => ( '' !== $site_logo_title_text ? $site_logo_title_text : get_bloginfo( 'name' ) ),
				'label_block' => false,
				'condition' => array(
					'content_type' => 'logo',
					'logo_image_source' => 'custom',
					'logo_type' => 'text',
				),
			)
		);

		$repeater->add_control(
			'logo_link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array(
						'title' => __( 'None', 'cmsmasters-elementor' ),
					),
					'home' => array(
						'title' => __( 'Home', 'cmsmasters-elementor' ),
					),
					'custom' => array(
						'title' => __( 'Custom', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'home',
				'label_block' => false,
				'render_type' => 'template',
				'condition' => array( 'content_type' => 'logo' ),
			)
		);

		$repeater->add_control(
			'logo_custom_url',
			array(
				'label' => __( 'Custom Logo Url', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'condition' => array( 'logo_link' => 'custom' ),
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::WYSIWYG,
				'dynamic' => array( 'active' => true ),
				'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'cmsmasters-elementor' ),
				'condition' => array( 'content_type' => 'custom' ),
			)
		);

		$menus = $this->get_available_menus();

		if ( ! empty( $menus ) ) {
			$repeater->add_control(
				'nav_menu',
				array(
					'label' => __( 'Select Menu', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => $menus,
					'default' => array_keys( $menus )[0],
					'save_default' => true,
					'description' => sprintf( __( 'Go to the <a href="%s" target="_blank">Menus screen</a> to manage your menus.', 'cmsmasters-elementor' ), admin_url( 'nav-menus.php' ) ),
					'condition' => array( 'content_type' => 'navigation' ),
				)
			);
		} else {
			$repeater->add_control(
				'nav_menu',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw' => sprintf( __( '<strong>There are no menus in your site.</strong><br>Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', 'cmsmasters-elementor' ), admin_url( 'nav-menus.php?action=edit&menu=0' ) ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'condition' => array( 'content_type' => 'navigation' ),
				)
			);
		}

		global $wp_registered_sidebars;

		if ( $wp_registered_sidebars ) {
			$repeater->add_control(
				'sidebar',
				array(
					'label' => __( 'Choose Sidebar', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => $this->get_sidebars( 'default_key' ),
					'options' => $this->get_sidebars( 'options' ),
					'condition' => array( 'content_type' => 'sidebar' ),
				)
			);
		} else {
			$repeater->add_control(
				'sidebar',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __( 'No sidebars were found.', 'cmsmasters-elementor' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'condition' => array( 'content_type' => 'sidebar' ),
				)
			);
		}

		if ( ! array_key_exists( 'no_template', $this->get_page_template_options( 'section' ) ) || ! array_key_exists( 'no_template', $this->get_page_template_options( 'container' ) ) ) {
			$repeater->add_control(
				'saved_section',
				array(
					'label' => __( 'Choose Section', 'cmsmasters-elementor' ),
					'label_block' => true,
					'show_label' => false,
					'type' => CmsmastersControls::QUERY,
					'autocomplete' => array(
						'object' => Query_Manager::TEMPLATE_OBJECT,
						'query' => array(
							'meta_query' => array(
								array(
									'key' => Document::TYPE_META_KEY,
									'value' => array(
										'section',
										'container',
									),
								),
							),
						),
					),
					'condition' => array( 'content_type' => 'section' ),
				)
			);
		} else {
			$repeater->add_control(
				'saved_section',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw' => sprintf( __( '<strong>There are no saved sections or containers in your site.</strong><br>Go to the <a href="%s" target="_blank">Saved Section</a> to create one.', 'cmsmasters-elementor' ), admin_url( 'edit.php?post_type=elementor_library&tabs_group=library&elementor_library_type=section' ) ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'condition' => array( 'content_type' => 'section' ),
				)
			);
		}

		if ( ! array_key_exists( 'no_template', $this->get_page_template_options( 'page' ) ) ) {
			$repeater->add_control(
				'template_id',
				array(
					'label' => __( 'Choose Template', 'cmsmasters-elementor' ),
					'label_block' => true,
					'show_label' => false,
					'type' => CmsmastersControls::QUERY,
					'autocomplete' => array(
						'object' => Query_Manager::TEMPLATE_OBJECT,
						'query' => array(
							'meta_query' => array(
								array(
									'key' => Document::TYPE_META_KEY,
									'value' => 'page',
								),
							),
						),
					),
					'condition' => array( 'content_type' => 'template' ),
				)
			);
		} else {
			$repeater->add_control(
				'template_id',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw' => sprintf( __( '<strong>There are no templates in your site.</strong><br>Go to the <a href="%s" target="_blank">Saved Templates</a> to create one.', 'cmsmasters-elementor' ), admin_url( 'edit.php?post_type=elementor_library&tabs_group=library&elementor_library_type=page' ) ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition' => array( 'content_type' => 'template' ),
				)
			);
		}

		if ( ! array_key_exists( 'no_template', $this->get_page_template_options( 'widget' ) ) ) {
			$repeater->add_control(
				'saved_widget',
				array(
					'label' => __( 'Choose Widget', 'cmsmasters-elementor' ),
					'label_block' => true,
					'show_label' => false,
					'type' => CmsmastersControls::QUERY,
					'autocomplete' => array(
						'object' => Query_Manager::TEMPLATE_OBJECT,
						'query' => array(
							'meta_query' => array(
								array(
									'key' => Document::TYPE_META_KEY,
									'value' => 'widget',
								),
							),
						),
					),
					'export' => false,
					'condition' => array( 'content_type' => 'widget' ),
				)
			);
		} else {
			$repeater->add_control(
				'saved_widget',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw' => sprintf( __( '<strong>There are no saved global widgets in your site.</strong><br>Go to the <a href="%s" target="_blank">Global Widget</a> to create one.', 'cmsmasters-elementor' ), admin_url( 'edit.php?post_type=elementor_library&tabs_group=library&elementor_library_type=widget' ) ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'condition' => array( 'content_type' => 'widget' ),
				)
			);
		}

		$repeater->add_control(
			'offcanvas_item_style',
			array(
				'label' => esc_html__( 'Style', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
			)
		);

		$repeater->start_popover();

		$repeater->add_responsive_control(
			'offcanvas_alignment',
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
					'.cmsmasters-offcanvas-content-{{ID}} {{CURRENT_ITEM}} ' . $widget_selector . '__custom-container-cont-inner' => 'text-align: {{VALUE}};',
				),
				'condition' => array( 'offcanvas_item_style' => 'yes' ),
			)
		);

		$repeater->add_responsive_control(
			'offcanvas_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} {{CURRENT_ITEM}} ' . $widget_selector . '__custom-container-cont-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'offcanvas_item_style' => 'yes' ),
			)
		);

		$repeater->add_responsive_control(
			'content_margin_bottom',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} {{CURRENT_ITEM}} ' . $widget_selector . '__custom-container-cont' => 'margin-top: calc( {{SIZE}}{{UNIT}} / 2 ); padding-top: calc( {{SIZE}}{{UNIT}} / 2 );',
				),
				'condition' => array( 'offcanvas_item_style' => 'yes' ),
			)
		);

		$repeater->add_control(
			'content_title_color',
			array(
				'label' => __( 'Title Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body {{CURRENT_ITEM}} ' . $widget_selector . '__custom-container-cont-inner > ' . $widget_selector . '__custom-widget-title' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'offcanvas_item_style' => 'yes',
					'title!' => '',
				),
			)
		);

		$repeater->add_control(
			'content_text_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body {{CURRENT_ITEM}} ' . $widget_selector . '__custom-container-cont-inner ' . $widget_selector . '__menu-container,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body {{CURRENT_ITEM}} ' . $widget_selector . '__custom-container-cont-inner ' . $widget_selector . '__menu-container a,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body {{CURRENT_ITEM}} ' . $widget_selector . '__custom-container-cont-inner > div,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body {{CURRENT_ITEM}} ' . $widget_selector . '__custom-container-cont-inner .widget *' => 'color: {{VALUE}}',
				),
				'condition' => array( 'offcanvas_item_style' => 'yes' ),
			)
		);

		$repeater->add_control(
			'content_custom_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'template',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} {{CURRENT_ITEM}} ' . $widget_selector . '__custom-container-cont-inner' => 'background-color: {{VALUE}}',
				),
				'condition' => array( 'offcanvas_item_style' => 'yes' ),
			)
		);

		$repeater->end_popover();

		$repeater->add_control(
			'box_to_down',
			array(
				'label' => __( 'Stick to bottom', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'render_type' => 'template',
				'default' => 'false',
			)
		);

		$this->add_control(
			'content_block',
			array(
				'label' => __( 'Canvas Items', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'content_type' => 'logo' ),
				),
				'show_label' => false,
				'title_field' => '<# if ( \'logo\' === content_type ) { #>Site Logo<# } else if ( \'custom\' === content_type ) { #>Content<# } else if ( \'navigation\' === content_type ) { #>Navigation<# } else if ( \'sidebar\' === content_type ) { #>Sidebar<# } else if ( \'section\' === content_type ) { #>Section<# } else if ( \'template\' === content_type ) { #>Page<# } if ( \'\' !== title ) { #> - {{{ title }}}<# } if ( \'true\' === box_to_down ) { #><i class="fas fa-long-arrow-alt-down" style="margin: 0 0 0 10px"></i><# } #>',
				'render_type' => 'template',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'canvas_position',
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
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'canvas_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'vh' ),
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
				'default' => array(
					'unit' => 'px',
					'size' => 300,
				),
				'tablet_default' => array(
					'unit' => '%',
					'size' => 35,
				),
				'mobile_default' => array(
					'unit' => '%',
					'size' => 50,
				),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}}.elementor-widget-cmsmasters-offcanvas__content' => 'width: {{SIZE}}{{UNIT}}',
					'.cmsmasters-offcanvas-content-{{ID}}.elementor-widget-cmsmasters-offcanvas__content.cmsmasters-canvas-position-left' => 'left: -{{SIZE}}{{UNIT}}',
					'.cmsmasters-offcanvas-content-{{ID}}.elementor-widget-cmsmasters-offcanvas__content.cmsmasters-canvas-position-right' => 'right: -{{SIZE}}{{UNIT}}',
					".cmsmasters-offcanvas-content-open-{{ID}}.cmsmasters-offcanvas-content-push.cmsmasters-offcanvas-content-left {$widget_selector}__container" => 'left: {{SIZE}}{{UNIT}}',
					".cmsmasters-offcanvas-content-open-{{ID}}.cmsmasters-offcanvas-content-push.cmsmasters-offcanvas-content-right {$widget_selector}__container" => 'left: -{{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'animation_type',
			array(
				'label' => __( 'Animation Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'slide' => array( 'title' => __( 'Slide', 'cmsmasters-elementor' ) ),
					'push' => array( 'title' => __( 'Push', 'cmsmasters-elementor' ) ),
				),
				'default' => 'slide',
				'label_block' => false,
				'render_type' => 'template',
				'frontend_available' => true,
				'toggle' => false,
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_trigger',
			array( 'label' => __( 'Trigger', 'cmsmasters-elementor' ) )
		);

		$this->add_responsive_control(
			'trigger_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
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
					'stretch' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'default' => 'center',
				'toggle' => false,
				'label_block' => false,
				'selectors_dictionary' => array(
					'left' => 'flex-start;',
					'center' => 'center;',
					'right' => 'flex-end;',
					'stretch' => 'stretch',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__trigger-container' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'trigger_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
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
				'separator' => 'before',
			)
		);

		$this->add_control(
			'trigger_view',
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
				'prefix_class' => 'cmsmasters-trigger-view-',
			)
		);

		$this->add_control(
			'trigger_shape',
			array(
				'label' => __( 'Shape', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'square' => array( 'title' => __( 'Square', 'cmsmasters-elementor' ) ),
					'circle' => array( 'title' => __( 'Circle', 'cmsmasters-elementor' ) ),
				),
				'default' => 'square',
				'label_block' => false,
				'prefix_class' => 'cmsmasters-trigger-shape-',
				'condition' => array(
					'trigger_view!' => 'default',
					'trigger_type' => 'icon',
					'trigger_alignment!' => 'stretch',
				),
			)
		);

		$this->start_controls_tabs(
			'tabs_trigger_icon',
			array( 'condition' => array( 'trigger_type!' => 'text' ) )
		);

		$this->start_controls_tab(
			'tab_trigger_icon_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'trigger_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'recommended' => array(
					'fa-solid' => array(
						'bars',
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
			'tab_trigger_icon_active',
			array( 'label' => __( 'Active', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'trigger_icon_active',
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
			'trigger_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'More', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'trigger_type!' => 'icon' ),
			)
		);

		$this->add_responsive_control(
			'trigger_text_icon_position',
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
				'prefix_class' => 'cmsmasters-trigger-text-icon%s-position-',
				'condition' => array( 'trigger_type' => 'both' ),
			)
		);

		$this->add_control(
			'trigger_icon_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'row' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'column' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'row-reverse' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'row',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-trigger-icon-direction: {{VALUE}}',
				),
				'condition' => array( 'trigger_type' => 'both' ),
			)
		);

		$this->add_responsive_control(
			'trigger_icon_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-trigger-icon-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'trigger_type' => 'both' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_close',
			array( 'label' => __( 'Close', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'close_button_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inside' => array(
						'title' => __( 'Inside', 'cmsmasters-elementor' ),
						'description' => 'Button will be inside box',
					),
					'outside' => array(
						'title' => __( 'Outside', 'cmsmasters-elementor' ),
						'description' => 'Button will be outside box',
					),
				),
				'default' => 'inside',
				'label_block' => false,
			)
		);

		$this->add_control(
			'close_button_horizontal_alignment',
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
				'default' => '',
				'condition' => array( 'close_button_position' => 'inside' ),
			)
		);

		$this->add_control(
			'close_button_vertical_alignment',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'top' => array( 'title' => __( 'Top', 'cmsmasters-elementor' ) ),
					'middle' => array( 'title' => __( 'Middle', 'cmsmasters-elementor' ) ),
				),
				'default' => 'top',
				'label_block' => false,
				'condition' => array( 'close_button_position' => 'outside' ),
			)
		);

		$this->add_responsive_control(
			'close_button_ver_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vh' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close-container' => 'top: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'close_button_position' => 'outside',
					'close_button_vertical_alignment' => 'top',
				),
			)
		);

		$this->add_control(
			'close_button_type',
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
			'close_button_view',
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
			)
		);

		$this->add_control(
			'close_button_shape',
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
				'condition' => array(
					'close_button_type' => 'icon',
					'close_button_horizontal_alignment!' => 'stretch',
					'close_button_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'close_button_cont_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_control(
			'close_button_icon',
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
				'condition' => array( 'close_button_type!' => 'text' ),
			)
		);

		$this->add_control(
			'close_button_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Close', 'cmsmasters-elementor' ),
				'condition' => array( 'close_button_type!' => 'icon' ),
			)
		);

		$this->add_control(
			'overlay_close',
			array(
				'label' => __( 'Close With Click on Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'Close popup upon click/tap on overlay', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'frontend_available' => true,
				'separator' => 'before',
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_canvas',
			array(
				'label' => __( 'Canvas', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'box_align',
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
				'render_type' => 'template',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__custom-container-cont-inner' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'box_bg',
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'default' => 'classic',
				'selector' => '.cmsmasters-offcanvas-content-{{ID}}.elementor-widget-cmsmasters-offcanvas__content',
			)
		);

		$this->add_responsive_control(
			'box_margin_bottom',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__custom-container-cont' => 'margin-top: calc( {{SIZE}}{{UNIT}} / 2 ); padding-top: calc( {{SIZE}}{{UNIT}} / 2 );',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'separator' => 'before',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}}.elementor-widget-cmsmasters-offcanvas__content' => '--box-padding-top: {{TOP}}{{UNIT}}; --box-padding-right: {{RIGHT}}{{UNIT}}; --box-padding-bottom: {{BOTTOM}}{{UNIT}}; --box-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'box_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'selectors' => array(
							'.cmsmasters-offcanvas-content-{{ID}}.elementor-widget-cmsmasters-offcanvas__content' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close-container.cmsmasters-position-outside' => 'padding-left: {{LEFT}}{{UNIT}}; margin-top: -{{TOP}}{{UNIT}};',
						),
					),
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '.cmsmasters-offcanvas-content-{{ID}}.elementor-widget-cmsmasters-offcanvas__content',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'overlay_box_shadow',
				'selector' => '.cmsmasters-offcanvas-content-{{ID}}.elementor-widget-cmsmasters-offcanvas__content',
			)
		);

		$this->add_control(
			'overlay_bg_overlay',
			array(
				'label' => __( 'Background Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--overlay-bg-overlay: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'scrollbar',
			array(
				'label' => __( 'Scroll Bar', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'scrollbar_track_bg',
			array(
				'label' => __( 'Track', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .ps__rail-y:hover,' .
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .ps__rail-y:focus,' .
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .ps__rail-y.ps--clicking,' .
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .ps__rail-x:hover,' .
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .ps__rail-x:focus,' .
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .ps__rail-x.ps--clicking' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'scrollbar_thumb_bg',
			array(
				'label' => __( 'Thumb', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .ps__thumb-y,' .
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .ps__thumb-x' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'scrollbar_side_gap',
			array(
				'label' => __( 'Side Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--scrollbar-side-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_item',
			array(
				'label' => __( 'Item', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'item_title_heading',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'item_title_alignment',
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
				'label_block' => false,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__custom-container-cont-inner > ' . $widget_selector . '__custom-widget-title,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .widget > .widget-title' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'item_title_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__custom-container-cont-inner > ' . $widget_selector . '__custom-widget-title,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .widget > .widget-title',
			)
		);

		$this->add_control(
			'item_title_color',
			array(
				'label' => __( 'Title Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__custom-container-cont-inner > ' . $widget_selector . '__custom-widget-title,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .widget > .widget-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'item_title_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__custom-container-cont-inner > ' . $widget_selector . '__custom-widget-title,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .widget > .widget-title' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'item_title_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__custom-container-cont-inner > ' . $widget_selector . '__custom-widget-title,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .widget > .widget-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'trigger_type!' => 'text' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'item_title_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
					),
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__custom-container-cont-inner > ' . $widget_selector . '__custom-widget-title,
				.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .widget > .widget-title',
			)
		);

		$this->add_control(
			'item_title_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__custom-container-cont-inner > ' . $widget_selector . '__custom-widget-title,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .widget > .widget-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_title_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__custom-container-cont-inner > ' . $widget_selector . '__custom-widget-title,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .widget > .widget-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_text_heading',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'item_text_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__custom-widget-content,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .widget *',
			)
		);

		$this->add_control(
			'item_text_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__custom-widget-content,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .widget *' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'item_link_heading',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'item_links_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__custom-widget-content a,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .widget * a',
			)
		);

		$this->start_controls_tabs( 'tabs_item_links_style' );

		$this->start_controls_tab(
			'tab_item_links_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'item_links_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__custom-widget-content a'  => 'color: {{VALUE}}',
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .widget * a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_links_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'item_links_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__custom-widget-content a:hover,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body .widget * a:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'item_divider_heading',
			array(
				'label' => __( 'Divider', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'item_divider_type',
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
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__custom-container-cont' => 'border-top-style: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_divider_size',
			array(
				'label' => __( 'Divider Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 15 ),
				),
				'default' => array( 'size' => 1 ),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__custom-container-cont' => 'border-top-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'item_divider_type!' => 'none' ),
			)
		);

		$this->add_control(
			'item_divider_color',
			array(
				'label' => __( 'Divider Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__custom-container-cont' => 'border-top-color: {{VALUE}};',
				),
				'condition' => array( 'item_divider_type!' => 'none' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_trigger',
			array(
				'label' => __( 'Trigger', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'offcanvas_trigger_typography',
				'fields_options' => array(
					'text_decoration' => array(
						'selectors' => array(
							'{{WRAPPER}}' => '--trigger-text-decoration: {{VALUE}};',
						),
					),
				),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__trigger',
				'condition' => array( 'trigger_type!' => 'icon' ),
			)
		);

		$this->start_controls_tabs( 'tabs_trigger_style' );

		$this->start_controls_tab(
			'tab_trigger_style_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'trigger_primary',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__trigger' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-trigger-view-framed ' . $widget_selector . '__trigger' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'trigger_bg_background',
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
				'condition' => array( 'trigger_view!' => 'default' ),
			)
		);

		$this->add_control(
			'trigger_secondary',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__trigger' => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'trigger_bg_background' => array(
						'color',
						'gradient',
					),
					'trigger_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'trigger_bg_color_stop',
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
					'trigger_bg_background' => array( 'gradient' ),
					'trigger_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bg_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'trigger_bg_background' => array( 'gradient' ),
					'trigger_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bg_color_b_stop',
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
					'trigger_bg_background' => array( 'gradient' ),
					'trigger_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bg_gradient_type',
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
					'trigger_bg_background' => array( 'gradient' ),
					'trigger_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bg_gradient_angle',
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
					'{{WRAPPER}} ' . $widget_selector . '__trigger' => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{trigger_bg_color_stop.SIZE}}{{trigger_bg_color_stop.UNIT}}, {{trigger_bg_color_b.VALUE}} {{trigger_bg_color_b_stop.SIZE}}{{trigger_bg_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'trigger_bg_background' => array( 'gradient' ),
					'trigger_bg_gradient_type' => 'linear',
					'trigger_view!' => 'default',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bg_gradient_position',
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
					'{{WRAPPER}} ' . $widget_selector . '__trigger' => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{trigger_bg_color_stop.SIZE}}{{trigger_bg_color_stop.UNIT}}, {{trigger_bg_color_b.VALUE}} {{trigger_bg_color_b_stop.SIZE}}{{trigger_bg_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'trigger_bg_background' => array( 'gradient' ),
					'trigger_bg_gradient_type' => 'radial',
					'trigger_view!' => 'default',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-trigger-view-framed ' . $widget_selector . '__trigger' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'trigger_view' => 'framed',
					'trigger_framed_border_style!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'trigger_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'trigger_view!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'trigger_text_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__trigger-label',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'trigger_box_shadow',
				'selector' => '{{WRAPPER}}.cmsmasters-trigger-view-framed ' . $widget_selector . '__trigger,
					{{WRAPPER}}.cmsmasters-trigger-view-stacked ' . $widget_selector . '__trigger',
				'condition' => array( 'trigger_view!' => 'default' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_trigger_style_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'trigger_primary_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__trigger:hover' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-trigger-view-framed ' . $widget_selector . '__trigger:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'trigger_bg_hover_background',
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
				'condition' => array( 'trigger_view!' => 'default' ),
			)
		);

		$this->add_control(
			'trigger_secondary_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__trigger:hover' => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'trigger_bg_hover_background' => array(
						'color',
						'gradient',
					),
					'trigger_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'trigger_bg_hover_color_stop',
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
					'trigger_bg_hover_background' => array( 'gradient' ),
					'trigger_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bg_hover_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'trigger_bg_hover_background' => array( 'gradient' ),
					'trigger_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bg_hover_color_b_stop',
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
					'trigger_bg_hover_background' => array( 'gradient' ),
					'trigger_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bg_hover_gradient_type',
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
					'trigger_bg_hover_background' => array( 'gradient' ),
					'trigger_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bg_hover_gradient_angle',
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
					'{{WRAPPER}} ' . $widget_selector . '__trigger:hover' => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{trigger_bg_hover_color_stop.SIZE}}{{trigger_bg_hover_color_stop.UNIT}}, {{trigger_bg_hover_color_b.VALUE}} {{trigger_bg_hover_color_b_stop.SIZE}}{{trigger_bg_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'trigger_bg_hover_background' => array( 'gradient' ),
					'trigger_bg_hover_gradient_type' => 'linear',
					'trigger_view!' => 'default',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bg_hover_gradient_position',
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
					'{{WRAPPER}} ' . $widget_selector . '__trigger:hover' => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{trigger_bg_hover_color_stop.SIZE}}{{trigger_bg_hover_color_stop.UNIT}}, {{trigger_bg_hover_color_b.VALUE}} {{trigger_bg_hover_color_b_stop.SIZE}}{{trigger_bg_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'trigger_bg_hover_background' => array( 'gradient' ),
					'trigger_bg_hover_gradient_type' => 'radial',
					'trigger_view!' => 'default',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bd_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-trigger-view-framed ' . $widget_selector . '__trigger:hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'trigger_view' => 'framed',
					'trigger_framed_border_style!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'trigger_border_radius_hover',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-trigger-view-framed ' . $widget_selector . '__trigger:hover,
					{{WRAPPER}}.cmsmasters-trigger-view-stacked ' . $widget_selector . '__trigger:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'trigger_view!' => 'default' ),
			)
		);

		$this->add_control(
			'trigger_text_decoration_hover',
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
					'{{WRAPPER}}' => '--trigger-hover-text-decoration: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'trigger_text_shadow_hover',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__trigger:hover ' . $widget_selector . '__trigger-label',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'trigger_box_shadow_hover',
				'selector' => '{{WRAPPER}}.cmsmasters-trigger-view-framed ' . $widget_selector . '__trigger:hover,
					{{WRAPPER}}.cmsmasters-trigger-view-stacked ' . $widget_selector . '__trigger:hover',
				'condition' => array( 'trigger_view!' => 'default' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_trigger_style_active',
			array( 'label' => __( 'Active', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'trigger_primary_active',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__trigger.trigger-active' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-trigger-view-framed ' . $widget_selector . '__trigger.trigger-active' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'trigger_bg_active_background',
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
				'condition' => array( 'trigger_view!' => 'default' ),
			)
		);

		$this->add_control(
			'trigger_secondary_active',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__trigger.trigger-active' => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'trigger_bg_active_background' => array(
						'color',
						'gradient',
					),
					'trigger_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'trigger_bg_active_color_stop',
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
					'trigger_bg_active_background' => array( 'gradient' ),
					'trigger_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bg_active_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'trigger_bg_active_background' => array( 'gradient' ),
					'trigger_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bg_active_color_b_stop',
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
					'trigger_bg_active_background' => array( 'gradient' ),
					'trigger_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bg_active_gradient_type',
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
					'trigger_bg_active_background' => array( 'gradient' ),
					'trigger_view!' => 'default',
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bg_active_gradient_angle',
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
					'{{WRAPPER}} ' . $widget_selector . '__trigger.trigger-active' => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{trigger_bg_active_color_stop.SIZE}}{{trigger_bg_active_color_stop.UNIT}}, {{trigger_bg_active_color_b.VALUE}} {{trigger_bg_active_color_b_stop.SIZE}}{{trigger_bg_active_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'trigger_bg_active_background' => array( 'gradient' ),
					'trigger_bg_active_gradient_type' => 'linear',
					'trigger_view!' => 'default',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bg_active_gradient_position',
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
					'{{WRAPPER}} ' . $widget_selector . '__trigger.trigger-active' => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{trigger_bg_active_color_stop.SIZE}}{{trigger_bg_active_color_stop.UNIT}}, {{trigger_bg_active_color_b.VALUE}} {{trigger_bg_active_color_b_stop.SIZE}}{{trigger_bg_active_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'trigger_bg_active_background' => array( 'gradient' ),
					'trigger_bg_active_gradient_type' => 'radial',
					'trigger_view!' => 'default',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'trigger_bd_color_active',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-trigger-view-framed ' . $widget_selector . '__trigger.trigger-active' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'trigger_view' => 'framed',
					'trigger_framed_border_style!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'trigger_border_radius_active',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-trigger-view-framed ' . $widget_selector . '__trigger.trigger-active,
					{{WRAPPER}}.cmsmasters-trigger-view-stacked ' . $widget_selector . '__trigger.trigger-active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'trigger_view!' => 'default' ),
			)
		);

		$this->add_control(
			'trigger_text_decoration_active',
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
					'{{WRAPPER}}' => '--trigger-active-text-decoration: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'trigger_text_shadow_active',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__trigger.trigger-active ' . $widget_selector . '__trigger-label',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'trigger_box_shadow_active',
				'selector' => '{{WRAPPER}}.cmsmasters-trigger-view-framed ' . $widget_selector . '__trigger.trigger-active,
					{{WRAPPER}}.cmsmasters-trigger-view-stacked ' . $widget_selector . '__trigger.trigger-active',
				'condition' => array( 'trigger_view!' => 'default' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'trigger_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_responsive_control(
			'trigger_icon_size',
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
					'{{WRAPPER}} ' . $widget_selector . '__trigger i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__trigger svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'trigger_type!' => 'text' ),
			)
		);

		$this->add_responsive_control(
			'trigger_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'trigger_view!' => 'default',
					'trigger_shape' => 'square',
				),
			)
		);

		$this->add_responsive_control(
			'trigger_icon_padding',
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
					'{{WRAPPER}} ' . $widget_selector . '__trigger' => 'padding: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'trigger_alignment!' => 'stretch',
					'trigger_type' => 'icon',
					'trigger_view!' => 'default',
					'trigger_shape' => 'circle',
				),
			)
		);

		$this->add_control(
			'trigger_framed_border_style',
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
				'prefix_class' => 'cmsmasters-trigger-border-type-',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__trigger' => 'border-style: {{VALUE}};',
				),
				'condition' => array( 'trigger_view' => 'framed' ),
			)
		);

		$this->add_responsive_control(
			'trigger_framed_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__trigger' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'trigger_view' => 'framed',
					'trigger_framed_border_style!' => array(
						'',
						'default',
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_close',
			array(
				'label' => __( 'Close', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'close_bottom_gap',
			array(
				'label' => __( 'Bottom Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'vh',
					'vw',
				),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close-container.cmsmasters-position-inside' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'close_button_position' => 'inside' ),
			)
		);

		$this->add_responsive_control(
			'close_side_gap',
			array(
				'label' => __( 'Side Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'vh',
					'vw',
				),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close-container' => '--close-side-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'close_button_position' => 'inside' ),
			)
		);

		$this->start_controls_tabs( 'tabs_popup_close_style' );

		$this->start_controls_tab(
			'tab_close_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'close_primary',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close' => 'color: {{VALUE}}',
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close svg' => 'fill: {{VALUE}}',
					'.cmsmasters-offcanvas-content-{{ID}} .cmsmasters-close-view-framed ' . $widget_selector . '__close' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'close_secondary',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close' => 'background-color: {{VALUE}}',
				),
				'condition' => array( 'close_button_view!' => 'default' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_close_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'close_primary_hover',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close:hover' => 'color: {{VALUE}}',
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close:hover svg' => 'fill: {{VALUE}}',
					'.cmsmasters-offcanvas-content-{{ID}} .cmsmasters-close-view-framed ' . $widget_selector . '__close:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'close_secondary_hover',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close:hover' => 'background-color: {{VALUE}}',
				),
				'condition' => array( 'close_button_view!' => 'default' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'close_button_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'close_typography',
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close-label',
				'condition' => array( 'close_button_type!' => 'icon' ),
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
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'close_button_type!' => 'text' ),
			)
		);

		$this->add_responsive_control(
			'close_icon_gap',
			array(
				'label' => __( 'Icon Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close ' . $widget_selector . '__close-icon + span' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'close_button_type' => 'both' ),
			)
		);

		$this->add_responsive_control(
			'close_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'close_button_view!' => 'default',
					'close_button_shape!' => 'circle',
				),
			)
		);

		$this->add_responsive_control(
			'close_icon_padding',
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
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close' => 'padding: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'close_button_type' => 'icon',
					'close_button_view!' => 'default',
					'popup_close_shape' => 'circle',
				),
			)
		);

		$this->add_control(
			'close_framed_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'close_button_view' => 'framed' ),
			)
		);

		$this->add_control(
			'close_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'close_button_view!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'close_box_shadow',
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__close',
				'condition' => array( 'close_button_view!' => 'default' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_site_logo',
			array(
				'label' => __( 'Site Logo', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'site_logo_image_heading',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'site_logo_image_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'%',
					'px',
				),
				'range' => array(
					'%' => array(
						'min' => 15,
						'max' => 100,
					),
					'px' => array(
						'min' => 50,
						'max' => 500,
					),
				),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'site_logo_image_max_width',
			array(
				'label' => __( 'Max Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'%',
					'px',
				),
				'range' => array(
					'%' => array(
						'min' => 15,
						'max' => 100,
					),
					'px' => array(
						'min' => 50,
						'max' => 500,
					),
				),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo img' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'site_logo_image_effects_tabs' );

		$this->start_controls_tab(
			'site_logo_image_normal_tab',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'site_logo_image_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo img' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'site_logo_image_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo img' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'site_logo_image_border_border!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'site_logo_image_box_shadow',
				'exclude' => array( 'box_shadow_position' ),
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo img',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'site_logo_image_css_filters',
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo img',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'site_logo_image_hover_tab',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'site_logo_image_bg_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo img:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'site_logo_image_bd_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo img:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'site_logo_image_border_border!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'site_logo_image_box_shadow_hover',
				'exclude' => array( 'box_shadow_position' ),
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo img:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'site_logo_image_css_filters_hover',
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo img:hover',
			)
		);

		$this->add_control(
			'site_logo_image_bg_hover_transition',
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
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo img' => 'transition-duration: {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'site_logo_image_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'separator' => 'before',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'site_logo_image_border',
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo img',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'site_logo_image_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'site_logo_title_heading',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'site_logo_title_typography',
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo-title,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo-title > a',
			)
		);

		$this->start_controls_tabs( 'site_logo_title_tabs' );

		$this->start_controls_tab(
			'site_logo_title_normal_tab',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'site_logo_title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo-title,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo-title > a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'site_logo_title_shadow',
				'fields_options' => array(
					'text_shadow_type' => array( 'label' => __( 'Text Shadow', 'cmsmasters-elementor' ) ),
				),
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo-title,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo-title > a',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'site_logo_title_hover_tab',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'site_logo_title_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo-title:hover,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo-title:hover > a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'site_logo_title_shadow_hover',
				'fields_options' => array(
					'text_shadow_type' => array( 'label' => __( 'Text Shadow', 'cmsmasters-elementor' ) ),
				),
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo-title:hover,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo-title:hover > a',
			)
		);

		$this->add_control(
			'site_logo_title_hover_transition',
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
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__site-logo-title' => 'transition-duration: {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_menu',
			array(
				'label' => __( 'Menu', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'menu_main_heading',
			array(
				'label' => __( 'Main', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'menu_main_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner li > a',
			)
		);

		$this->start_controls_tabs( 'tabs_menu_main' );

		$this->start_controls_tab(
			'tab_menu_main_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'menu_main_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner li > a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_main_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'menu_main_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-container ' . $widget_selector . '__menu-inner li > a:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_main_active',
			array( 'label' => __( 'Active', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'menu_main_color_active',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner li.current-menu-item > a,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner li.current-menu-item > a:hover,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner li > a.focus' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'menu_main_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner li' => 'padding-top: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'indicator_main_popover',
			array(
				'label' => esc_html__( 'Main Indicator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'render_type' => 'template',
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

		$this->add_control(
			'indicator_main_position',
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
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner li > a ' . $widget_selector . '__arrow > span' => 'font-size: {{SIZE}}{{UNIT}};',
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner li > a ' . $widget_selector . '__arrow > span svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'indicator_main_popover' => 'yes' ),
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
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner li > a ' . $widget_selector . '__arrow.cmsmasters-indicator-position-right' => 'padding-left: {{SIZE}}{{UNIT}};',
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner li > a ' . $widget_selector . '__arrow.cmsmasters-indicator-position-left' => 'padding-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'indicator_main_popover' => 'yes' ),
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
			)
		);

		$this->add_control(
			'indicator_main_transition',
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
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner > li > a > span > span' => 'transition-duration: {{SIZE}}s',
				),
				'condition' => array(
					'indicator_main[value]!' => '',
					'indicator_main_animation!' => 'none',
				),
			)
		);

		$this->end_popover();

		$this->add_control(
			'menu_dropdown_heading',
			array(
				'label' => __( 'Dropdown', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'menu_dropdown_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner ul > li > a',
			)
		);

		$this->start_controls_tabs( 'tabs_menu_dropdown' );

		$this->start_controls_tab(
			'tab_menu_dropdown_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'menu_dropdown_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner ul > li > a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_dropdown_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'menu_dropdown_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-container ' . $widget_selector . '__menu-inner ul > li > a:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_dropdown_active',
			array( 'label' => __( 'Active', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'menu_dropdown_color_active',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner ul > li.current-menu-item > a,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner ul > li.current-menu-item > a:hover,
					.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner ul > li > a.focus' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'menu_dropdown_offset',
			array(
				'label' => __( 'Side Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}}.cmsmasters-offcanvas-alignment-left ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner ul' => 'padding-left: {{SIZE}}{{UNIT}}',
					'.cmsmasters-offcanvas-content-{{ID}}.cmsmasters-offcanvas-alignment-right ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner ul' => 'padding-right: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'box_align!' => 'center' ),
			)
		);

		$this->add_responsive_control(
			'menu_dropdown_top_gap',
			array(
				'label' => __( 'Top Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner ul' => 'padding-top: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'menu_dropdown_gap',
			array(
				'label' => __( 'Item Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner ul > li' => 'padding-top: {{SIZE}}{{UNIT}}',
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
				'condition' => array( 'indicator_submenu_popover' => 'yes' ),
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
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner ul > li > a ' . $widget_selector . '__arrow > span' => 'font-size: {{SIZE}}{{UNIT}};',
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner ul > li > a ' . $widget_selector . '__arrow > span svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'indicator_submenu_popover' => 'yes' ),
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
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner ul > li > a ' . $widget_selector . '__arrow.cmsmasters-indicator-position-right' => 'padding-left: {{SIZE}}{{UNIT}};',
					'.cmsmasters-offcanvas-content-{{ID}} ' . $widget_selector . '__body ' . $widget_selector . '__menu-inner ul > li > a ' . $widget_selector . '__arrow.cmsmasters-indicator-position-left' => 'padding-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'indicator_submenu_popover' => 'yes' ),
			)
		);

		$this->end_popover();

		$this->end_controls_section();
	}

	/**
	 * Get template IDs.
	 *
	 * @since 1.11.1
	 * @since 1.12.1 Add checking template.
	 *
	 * @return array Template IDs.
	 */
	public function get_template_ids() {
		$template_ids = array();

		$content_block = $this->get_settings_for_display( 'content_block' );

		foreach ( $content_block as $item ) {

			if ( ! in_array( $item['content_type'], array( 'section', 'template', 'widget' ) ) ) {
				continue;
			}

			$content_type = $item['content_type'];

			if ( 'template' === $content_type ) {
				$template_id = $item['template_id'];
			} elseif ( 'template' !== $content_type ) {
				if ( isset( $item[ "saved_{$content_type}" ] ) ) {
					$template_id = $item[ "saved_{$content_type}" ];
				} else {
					continue;
				}
			}

			if ( ! CmsmastersUtils::check_template( $template_id ) ) {
				continue;
			}

			$template_ids[] = $template_id;
		}

		return $template_ids;
	}

	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.11.1 Optimized loading CSS in templates.
	 */
	public function render() {
		$template_ids = $this->get_template_ids();

		if ( ! empty( $template_ids ) && 'enable' !== $this->lazyload_widget_get_status() ) {
			/** @var Addon $addon */
			$addon = CmsmastersPlugin::instance();

			$addon->frontend->print_template_css( $template_ids, $this->get_id() );
		}

		echo '<div class="elementor-widget-cmsmasters-offcanvas__wrapper">';

			$this->get_trigger();

			$this->get_content();

			echo '<div class="elementor-widget-cmsmasters-offcanvas__container__overlay"></div>';

		echo '</div>';
	}

	/**
	 * Return trigger for offcanvas output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_trigger() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			'cmsmasters-offcanvas-trigger-button',
			array(
				'class' => 'elementor-widget-cmsmasters-offcanvas__trigger',
				'role' => 'button',
				'tabindex' => '0',
				'aria-label' => 'Open Offcanvas',
			)
		);

		$trigger_type = $settings['trigger_type'];

		if ( 'both' === $trigger_type ) {
			$this->add_render_attribute( 'cmsmasters-offcanvas-trigger-button', 'class', 'cmsmasters-trigger-both' );
		}

		echo '<div class="elementor-widget-cmsmasters-offcanvas__trigger-container">' .
			'<div ' . $this->get_render_attribute_string( 'cmsmasters-offcanvas-trigger-button' ) . '>';

		$trigger_text = ( isset( $settings['trigger_text'] ) ? $settings['trigger_text'] : '' );

		if ( 'text' !== $trigger_type ) {
			$trigger_icon = $settings['trigger_icon'];

			echo '<span class="elementor-widget-cmsmasters-offcanvas__trigger-icon">';

			$trigger_icon_att = array( 'aria-hidden' => 'true' );

			if ( 'icon' === $trigger_type ) {
				$trigger_icon_att = array_merge(
					$trigger_icon_att,
					array( 'aria-label' => 'Offcanvas Trigger' ),
				);
			}

			if ( '' !== $trigger_icon['value'] ) {
				Icons_Manager::render_icon( $trigger_icon, $trigger_icon_att );
			} else {
				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-bars',
						'library' => 'fa-solid',
					),
					$trigger_icon_att
				);
			}

			echo '</span>';

			$trigger_icon_active = $settings['trigger_icon_active'];

			echo '<span class="elementor-widget-cmsmasters-offcanvas__trigger-icon-active">';

			if ( 'icon' === $trigger_type ) {
				$trigger_icon_att = array_merge(
					$trigger_icon_att,
					array( 'aria-label' => 'Offcanvas Active Trigger' ),
				);
			}

			if ( '' !== $trigger_icon_active['value'] ) {
				Icons_Manager::render_icon( $trigger_icon_active, $trigger_icon_att );
			} else {
				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-times',
						'library' => 'fa-solid',
					),
					$trigger_icon_att
				);
			}

			echo '</span>';
		}

		if ( 'icon' !== $trigger_type ) {
			echo '<span class="elementor-widget-cmsmasters-offcanvas__trigger-label">';

			if ( '' !== $trigger_text ) {
				echo esc_html( $trigger_text );
			} else {
				echo esc_html__( 'More', 'cmsmasters-elementor' );
			}

			echo '</span>';
		}

			echo '</div>' .
		'</div>';
	}

	/**
	 * Return content in offcanvas output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added output site logo for content type `Site Logo`.
	 */
	public function get_content() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'cmsmasters-offcanvas-content', 'class', array(
			'elementor-widget-cmsmasters-offcanvas__content',
			'cmsmasters-offcanvas-content-' . $this->get_id(),
			'cmsmasters-canvas-animation-type-' . $settings['animation_type'],
			'cmsmasters-canvas-position-' . $settings['canvas_position'],
			'cmsmasters-offcanvas-alignment-' . $settings['box_align'],
		) );

		echo '<div ' . $this->get_render_attribute_string( 'cmsmasters-offcanvas-content' ) . '>';
			$this->get_close();

		foreach ( $settings['content_block'] as $item ) {
			if ( '' !== $item['box_to_down'] ) {
				$this->box_to_down_count++;
			}
		}

		$content_block_all_down = '';

		if ( count( $settings['content_block'] ) === $this->box_to_down_count ) {
			$content_block_all_down = ' cmsmasters-block-all-down';
		}

			echo '<div class="elementor-widget-cmsmasters-offcanvas__body">' .
				'<div class="elementor-widget-cmsmasters-offcanvas__body-container' . esc_attr( $content_block_all_down ) . '">';

		foreach ( $settings['content_block'] as $item ) {
			if ( '' === $item['box_to_down'] ) {
				$item['box_to_down'] = 'false';
			}

			$item_bg_enable = ( 'yes' === $item['offcanvas_item_style'] && '' !== $item['content_custom_bg'] ? ' cmsmasters_item_bg_enable' : '' );

			echo '<div class="elementor-widget-cmsmasters-offcanvas__custom-container ' .
				'elementor-repeater-item-' . esc_attr( $item['_id'] ) .
				' cmsmasters-box-down-' . esc_attr( $item['box_to_down'] ) . '">';

				echo '<div class="elementor-widget-cmsmasters-offcanvas__custom-container-cont">' .
					'<div class="elementor-widget-cmsmasters-offcanvas__custom-container-cont-inner' . esc_attr( $item_bg_enable ) . '">';

			if ( '' !== $item['title'] ) {
				echo '<h3 class="elementor-widget-cmsmasters-offcanvas__custom-widget-title">' .
					esc_html( $item['title'] ) .
				'</h3>';
			}

			switch ( $item['content_type'] ) {
				case 'logo':
					echo '<div class="elementor-widget-cmsmasters-offcanvas__site-logo">';

					if ( ! empty( $this->get_logo_wrapper( $item ) ) ) {
						$this->get_logo_wrapper( $item );
					}

					if ( ! empty( $this->get_text_wrapper( $item ) ) ) {
						$this->get_text_wrapper( $item );
					}

					echo '</div>';

					break;
				case 'custom':
					echo '<div class="elementor-widget-cmsmasters-offcanvas__custom-widget-content">' .
						wp_kses_post( $item['description'] ) .
					'</div>';

					break;
				case 'navigation':
					if ( ! $this->get_available_menus() ) {
						break;
					}

					if ( ! is_nav_menu( $item['nav_menu'] ) ) {
						if ( is_admin() ) {
							CmsmastersUtils::render_alert( esc_html__( 'Please choose navigation menu', 'cmsmasters-elementor' ) );
						}

						break;
					}

					$args = array(
						'menu' => $item['nav_menu'],
						'menu_id' => 'menu-' . $this->get_nav_menu_index() . '-' . $this->get_id(),
						'menu_class' => 'elementor-widget-cmsmasters-offcanvas__menu-inner',
						'container' => '',
						'echo' => false,
						'fallback_cb' => '__return_empty_string',
					);

					add_filter( 'nav_menu_link_attributes', array( $this, 'get_link_classes' ), 10, 4 );
					add_filter( 'wp_nav_menu_objects', array( $this, 'modify_menu_items' ), 10, 2 );

					$menu_html = wp_nav_menu( $args );

					remove_filter( 'nav_menu_link_attributes', array( $this, 'get_link_classes' ) );
					remove_filter( 'wp_nav_menu_objects', array( $this, 'modify_menu_items' ) );

					if ( empty( $menu_html ) ) {
						break;
					}

					echo '<nav class="elementor-widget-cmsmasters-offcanvas__menu-container">';

						Utils::print_unescaped_internal_string( $menu_html );

					echo '</nav>';

					break;
				case 'sidebar':
					global $wp_registered_sidebars;

					if ( ! $wp_registered_sidebars ) {
						break;
					}

					echo $this->get_dynamic_sidebar( $item['sidebar'] );

					break;
				case 'section':
				case 'template':
				case 'widget':
					$content_type = $item['content_type'];

					if ( 'template' === $content_type ) {
						$template_id = $item['template_id'];
						$template_name = 'Saved Page';
					} elseif ( 'section' === $content_type ) {
						$template_id = '';

						if ( isset( $item[ "saved_{$content_type}" ] ) ) {
							$template_id = $item[ "saved_{$content_type}" ];
						}

						$template_name = 'Saved Section';
					} else {
						$template_id = $item[ "saved_{$content_type}" ];
						$template_name = 'Global Widget';
					}

					if ( '-1' === $template_id || ! $template_id ) {
						if ( is_admin() ) {
							/* translators: Offcanvas widget template not selected warning. %s: Name of content type */
							CmsmastersUtils::render_alert( sprintf( esc_html__( 'Please choose your %s template!', 'cmsmasters-elementor' ), $template_name ) );
						}

						break;
					}

					echo $this->get_widget_template( $template_id, esc_html( $content_type ) );

					break;
			}

					echo '</div>' .
				'</div>' .
			'</div>';
		}

		echo '</div>' .
			'</div>' .
		'</div>';
	}

	/**
	 * Returns logo
	 *
	 * @return string Image logo HTML markup.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added get logo wrapper for content type `Site Logo`.
	 */
	public function get_logo_wrapper( $item ) {
		$site_logo_type = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_type', 'image' );
		$widget_image_source = ( isset( $item['logo_image_source'] ) ? $item['logo_image_source'] : '' );
		$widget_type = ( isset( $item['logo_type'] ) ? $item['logo_type'] : '' );

		if ( 'text' === $widget_type || ( 'default' === $widget_image_source && 'text' === $site_logo_type ) ) {
			return;
		}

		$is_linked = $this->get_is_linked( $item );

		echo ( $is_linked ? $this->is_linked_start( $item ) : '' );

			$this->get_logo_image( $item );

		echo ( $is_linked ? '</a>' : '' );
	}

	/**
	 * Get logo image
	 *
	 * @param array $item Item data.
	 *
	 * @return string Get Logo Image
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added get logo image for content type `Site Logo`.
	 * @since 1.2.1 Fix: svg logo.
	 * @since 1.10.0 Add second logo functionality for mode switcher.
	 */
	public function get_logo_image( $item ) {
		$site_logo_title = ( get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : esc_html__( 'Site logo', 'cmsmasters-elementor' ) );
		$logo_out = '';

		if ( ! empty( $this->get_img_logo_retina_id( $item, 'second' ) ) ) {
			$logo_out .= $this->get_logo_img( array(
				'parent_class' => 'elementor-widget-cmsmasters-offcanvas',
				'id' => $this->get_img_logo_retina_id( $item, 'second' ),
				'title' => $site_logo_title,
				'type' => 'retina',
				'state' => 'second',
			) );
		}

		if ( ! empty( $this->get_img_logo_id( $item, 'second' ) ) ) {
			$logo_out .= $this->get_logo_img( array(
				'parent_class' => 'elementor-widget-cmsmasters-offcanvas',
				'id' => $this->get_img_logo_id( $item, 'second' ),
				'title' => $site_logo_title,
				'type' => 'normal',
				'state' => 'second',
			) );
		}

		if ( ! empty( $this->get_img_logo_retina_id( $item ) ) ) {
			$logo_out .= $this->get_logo_img( array(
				'parent_class' => 'elementor-widget-cmsmasters-offcanvas',
				'id' => $this->get_img_logo_retina_id( $item ),
				'title' => $site_logo_title,
				'type' => 'retina',
			) );
		}

		if ( ! empty( $this->get_img_logo_id( $item ) ) ) {
			$logo_out .= $this->get_logo_img( array(
				'parent_class' => 'elementor-widget-cmsmasters-offcanvas',
				'id' => $this->get_img_logo_id( $item ),
				'title' => $site_logo_title,
				'type' => 'normal',
			) );
		}

		if ( empty( $logo_out ) ) {
			$logo_out = '<img' .
				' class="elementor-widget-cmsmasters-offcanvas__site-logo-img"' .
				' src="' . get_parent_theme_file_uri( 'theme-config/images/logo.svg' ) . '"' .
				' alt="' . esc_attr( $site_logo_title ) . '" />';
		}

		Utils::print_unescaped_internal_string( $logo_out );
	}

	/**
	 * Get logo image id.
	 *
	 * @param array $item Item data.
	 * @param string $state Main/second state.
	 *
	 * @return string Logo image id.
	 *
	 * @since 1.2.1
	 * @since 1.10.0 Add second logo functionality for mode switcher.
	 */
	public function get_img_logo_id( $item, $state = 'main' ) {
		$suffix = ( 'main' !== $state ? "_{$state}" : '' );

		$site_logo_type = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_type', 'image' );
		$site_logo = CmsmastersUtils::get_kit_option( "cmsmasters_logo_image{$suffix}", array( 'id' => '' ) );

		$widget_image_source = ( isset( $item['logo_image_source'] ) ? $item['logo_image_source'] : '' );
		$widget_type = ( isset( $item['logo_type'] ) ? $item['logo_type'] : '' );
		$widget_image = ( isset( $item[ "logo_image{$suffix}" ] ) ? $item[ "logo_image{$suffix}" ] : '' );

		$logo = '';

		// Get Logo URL
		if ( 'default' === $widget_image_source && 'image' === $site_logo_type && ! empty( $site_logo['id'] ) ) {
			$logo = $site_logo;
		}

		if ( 'custom' === $widget_image_source && 'image' === $widget_type ) {
			if ( ! empty( $widget_image['id'] ) ) {
				$logo = $widget_image;
			}

			if ( 'main' === $state && empty( $widget_image['id'] ) && 'image' === $site_logo_type && ! empty( $site_logo['id'] ) ) {
				$logo = $site_logo;
			}
		}

		return ( ! empty( $logo ) ? $logo['id'] : '' );
	}

	/**
	 * Get logo image retina id.
	 *
	 * @param array $item Item data.
	 * @param string $state Main/second state.
	 *
	 * @return string Logo image retina id.
	 *
	 * @since 1.2.1
	 * @since 1.10.0 Add second logo functionality for mode switcher.
	 */
	public function get_img_logo_retina_id( $item, $state = 'main' ) {
		$suffix = ( 'main' !== $state ? "_{$state}" : '' );

		$site_logo_type = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_type', 'image' );
		$site_logo = CmsmastersUtils::get_kit_option( "cmsmasters_logo_image{$suffix}", array( 'id' => '' ) );
		$site_logo_retina_toggle = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_retina_toggle' );
		$site_logo_retina_toggle = ( 'main' !== $state ? 'yes' : $site_logo_retina_toggle );
		$site_logo_second_toggle = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_second_toggle' );
		$site_logo_second_toggle = ( 'main' !== $state ? $site_logo_second_toggle : 'yes' );
		$site_logo_retina = CmsmastersUtils::get_kit_option( "cmsmasters_logo_retina_image{$suffix}", array( 'id' => '' ) );

		$widget_image_source = ( isset( $item['logo_image_source'] ) ? $item['logo_image_source'] : '' );
		$widget_type = ( isset( $item['logo_type'] ) ? $item['logo_type'] : '' );
		$widget_image = ( isset( $item['logo_image'] ) ? $item['logo_image'] : '' );
		$widget_logo_retina_toggle = ( isset( $item['logo_image_retina'] ) ? $item['logo_image_retina'] : '' );
		$widget_logo_retina_toggle = ( 'main' !== $state ? 'yes' : $widget_logo_retina_toggle );
		$widget_logo_second_toggle = ( isset( $item['logo_image_second_toggle'] ) ? $item['logo_image_second_toggle'] : '' );
		$widget_logo_second_toggle = ( 'main' !== $state ? $widget_logo_second_toggle : 'yes' );
		$widget_logo_retina = ( isset( $item[ "logo_image_2x{$suffix}" ] ) ? $item[ "logo_image_2x{$suffix}" ] : '' );

		$logo_retina = '';

		// Get Logo URL
		if (
			'default' === $widget_image_source &&
			'image' === $site_logo_type &&
			! empty( $site_logo['id'] ) &&
			'yes' === $site_logo_retina_toggle &&
			'yes' === $site_logo_second_toggle &&
			! empty( $site_logo_retina['id'] )
		) {
			$logo_retina = $site_logo_retina;
		}

		if ( 'custom' === $widget_image_source && 'image' === $widget_type ) {
			if (
				! empty( $widget_image['id'] ) &&
				'yes' === $widget_logo_retina_toggle &&
				'yes' === $widget_logo_second_toggle &&
				! empty( $widget_logo_retina['id'] )
			) {
				$logo_retina = $widget_logo_retina;
			}

			if (
				empty( $widget_image['id'] ) &&
				'image' === $site_logo_type &&
				! empty( $site_logo['id'] ) &&
				'yes' === $site_logo_retina_toggle &&
				'yes' === $site_logo_second_toggle &&
				! empty( $site_logo_retina )
			) {
				$logo_retina = $site_logo_retina;
			}
		}

		return ( ! empty( $logo_retina ) ? $logo_retina['id'] : '' );
	}

	/**
	 * Get logo image.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Logo image html.
	 *
	 * @since 1.2.1
	 * @since 1.2.2 Fix for Site Logo image sizes.
	 * @since 1.10.0 Add second logo functionality for mode switcher.
	 */
	public function get_logo_img( $atts = array(), $type = 'normal' ) {
		$req_vars = array(
			'parent_class' => '',
			'id' => '',
			'title' => '',
			'type' => $type,
			'state' => 'main',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( empty( $id ) || empty( $parent_class ) ) {
			return '';
		}

		$img_data = wp_get_attachment_image_src( $id, 'full' );

		if ( empty( $img_data ) ) {
			return '';
		}

		$img_atts = array(
			'src="' . $img_data[0] . '"',
			'alt="' . $title . '"',
			'title="' . $title . '"',
		);

		if ( 'retina' === $type ) {
			$img_atts[] = 'width="' . round( intval( $img_data[1] ) / 2 ) . '"';
			$img_atts[] = 'height="' . round( intval( $img_data[2] ) / 2 ) . '"';
			$img_atts[] = 'class="' . esc_attr( "{$parent_class}__site-logo-retina-img {$parent_class}__site-logo-{$state}" ) . '"';
		} else {
			$img_atts[] = 'class="' . esc_attr( "{$parent_class}__site-logo-img {$parent_class}__site-logo-{$state}" ) . '"';
		}

		return '<img ' . implode( ' ', $img_atts ) . '/>';
	}

	/**
	 * Check if logo is linked.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added link check for logo in content type `Site Logo`.
	 */
	public function get_is_linked( $item ) {
		if ( 'none' === $item['logo_link'] ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if logo is linked.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added get link for logo in content type `Site Logo`.
	 */
	public function is_linked_start( $item ) {
		$link = '';
		$logo_link = isset( $item['logo_link'] ) ? esc_attr( $item['logo_link'] ) : 'none';

		if ( 'home' === $logo_link ) {
			$link .= '<a href="' . home_url() . '" class="elementor-widget-cmsmasters-offcanvas__site-logo-link">';
		} elseif ( 'custom' === $logo_link ) {
			$logo_custom_url = ( isset( $item['logo_custom_url'] ) ? esc_attr( $item['logo_custom_url']['url'] ) : '' );

			if ( '' !== $logo_custom_url ) {
				$link .= '<a' .
					' href="' . $logo_custom_url . '"' .
					' class="elementor-widget-cmsmasters-offcanvas__site-logo-link"' .
					( $item['logo_custom_url']['is_external'] ? ' target="_blank"' : '' ) .
					( $item['logo_custom_url']['nofollow'] ? ' rel="nofollow"' : '' ) .
					'>';
			}
		}

		return $link;
	}


	/**
	 * Returns logo text
	 *
	 * @return string Text logo HTML markup.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added get text wrapper for content type `Site Logo`.
	 */
	public function get_text_wrapper( $item ) {
		$logo_image_source = ( isset( $item['logo_image_source'] ) ? $item['logo_image_source'] : '' );
		$site_logo_type = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_type', 'image' );
		$logo_type = ( isset( $item['logo_type'] ) ? $item['logo_type'] : '' );
		$is_linked = $this->get_is_linked( $item );

		if (
			( 'default' === $logo_image_source && 'text' === $site_logo_type ) ||
			( 'custom' === $logo_image_source && 'text' === $logo_type )
		) {
			echo '<h1 class="elementor-widget-cmsmasters-offcanvas__site-logo-title">' .
				( $is_linked ? $this->is_linked_start( $item ) : '' ) .
					$this->get_logo_title_text( $item ) .
				( $is_linked ? '</a>' : '' ) .
			'</h1>';
		}
	}

	/**
	 * Returns logo text
	 *
	 * @return string Text logo HTML markup.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added get logo title text for content type `Site Logo`.
	 */
	public function get_logo_title_text( $item ) {
		$title = get_bloginfo( 'name' );
		$logo_title = ( isset( $item['logo_title'] ) ? $item['logo_title'] : '' );
		$site_logo_title_text = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_title_text', '' );

		$title = esc_html( $logo_title );

		if ( empty( $logo_title ) && ! empty( $site_logo_title_text ) ) {
			$title = esc_html( $site_logo_title_text );
		}

		return $title;
	}

	/**
	 * Adds template to content of some item.
	 *
	 * @since 1.0.0
	 * @since 1.11.1 Optimized loading CSS in templates.
	 * @since 1.12.1 Add checking template.
	 */
	public function get_widget_template( $template_id, $type ) {
		if ( ! CmsmastersUtils::check_template( $template_id ) ) {
			if ( is_admin() ) {
				if ( 'section' === $type ) {
					$message = __( 'Please choose your saved section or container template!', 'cmsmasters-elementor' );
				} elseif ( 'page' === $type ) {
					$message = __( 'Please choose your saved page template!', 'cmsmasters-elementor' );
				} else {
					$message = __( 'Please choose your saved global widget!', 'cmsmasters-elementor' );
				}

				CmsmastersUtils::render_alert( esc_html( $message ) );
			}

			return;
		}

		/** @var Addon $addon */
		$addon = CmsmastersPlugin::instance();

		return $addon->frontend->get_widget_template( $template_id );
	}

	/**
	 * @since 1.0.0
	 *
	 * @return string sidebar content
	 */
	public function get_dynamic_sidebar( $name ) {
		$contents = '';

		ob_start();

		dynamic_sidebar( $name );

		$contents = ob_get_clean();

		return $contents;
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
	 * @return int index
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

		$is_anchor = false !== strpos( $atts['href'], '#' );
		$classes = '';

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

		$atts['class'] .= ' ' . $this->get_widget_class() . '__item';

		$indicator_main_animation = ( isset( $settings['indicator_main_animation'] ) ? $settings['indicator_main_animation'] : '' );

		if ( isset( $indicator_main_animation ) && 'none' !== $indicator_main_animation ) {
			$atts['class'] .= ' cmsmasters-arrow-animation-' . $indicator_main_animation;
		}

		if ( 0 === $depth ) {
			$atts['class'] .= ' ' . $this->get_widget_class() . '__main-item';
		} else {
			$atts['class'] .= ' ' . $this->get_widget_class() . '__submenu-item';
		}

		return $atts;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return added tag span for link
	 */
	public function added_span_in_link( $item, $depth ) {
		$settings = $this->get_active_settings();

		$new_title = '';

		$new_title .= '<span class="' . $this->get_widget_class() . '__item-text">' .
			$item->title .
		'</span>';

		$indicator_position = ( isset( $settings['indicator_main_position'] ) ? $settings['indicator_main_position'] : '' );

		if ( 0 === $depth ) {
			$indicator = ( isset( $settings['indicator_main'] ) ? $settings['indicator_main'] : '' );
		} else {
			$indicator = ( isset( $settings['indicator_submenu'] ) ? $settings['indicator_submenu'] : '' );
		}

		if ( ! empty( $indicator['value'] ) ) {
			$new_title .= '<span class="' . $this->get_widget_class() . '__arrow cmsmasters-indicator-position-' . $indicator_position . '">' .
				CmsmastersUtils::get_render_icon( $indicator, array( 'tabindex' => '0' ) ) .
			'</span>';
		}

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
			$depth = $item->depth;

			$item->title = $this->added_span_in_link( $item, $depth );
		}

		return $items;
	}

	/**
	 * Return close button output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.17.3 Added title attr.
	 */
	public function get_close() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'cmsmasters-offcanvas-close-container', 'class', array(
			'elementor-widget-cmsmasters-offcanvas__close-container',
			'cmsmasters-close-hor-align-' . $settings['close_button_horizontal_alignment'],
			'cmsmasters-close-ver-align-' . $settings['close_button_vertical_alignment'],
			'cmsmasters-position-' . $settings['close_button_position'],
			'cmsmasters-close-view-' . $settings['close_button_view'],
		) );

		$close_button_shape = ( isset( $settings['close_button_shape'] ) ? $settings['close_button_shape'] : '' );

		if ( isset( $close_button_shape ) ) {
			$this->add_render_attribute( 'cmsmasters-offcanvas-close-container', 'class', 'cmsmasters-close-shape-' . $close_button_shape );
		}

		echo '<div ' . $this->get_render_attribute_string( 'cmsmasters-offcanvas-close-container' ) . '>' .
			'<div class="elementor-widget-cmsmasters-offcanvas__close" role="button" title="Offcanvas close" tabindex="0">';

		$close_button_type = ( isset( $settings['close_button_type'] ) ? $settings['close_button_type'] : '' );

		if ( 'text' !== $close_button_type ) {
			$close_button_icon = ( isset( $settings['close_button_icon'] ) ? $settings['close_button_icon'] : '' );
			$close_button_icon_att = array( 'aria-hidden' => 'true' );

			if ( 'icon' === $close_button_type ) {
				$close_button_icon_att = array_merge(
					$close_button_icon_att,
					array( 'aria-label' => 'Close Button' ),
				);
			}

			if ( '' !== $close_button_icon['value'] ) {
				echo '<span class="elementor-widget-cmsmasters-offcanvas__close-icon">';
					Icons_Manager::render_icon( $close_button_icon, $close_button_icon_att );
				echo '</span>';
			} else {
				echo '<span class="elementor-widget-cmsmasters-offcanvas__close-icon">';

					Icons_Manager::render_icon(
						array(
							'value' => 'fas fa-times',
							'library' => 'fa-solid',
						),
						$close_button_icon_att
					);

				echo '</span>';
			}
		}

		if ( 'icon' !== $close_button_type ) {
			echo '<span class="elementor-widget-cmsmasters-offcanvas__close-label">';

			$close_button_text = ( isset( $settings['close_button_text'] ) ? $settings['close_button_text'] : '' );

			if ( '' !== $close_button_text ) {
				echo esc_html( $close_button_text );
			} else {
				echo esc_html__( 'Close', 'cmsmasters-elementor' );
			}

			echo '</span>';
		}

		echo '</div>' .
		'</div>';
	}


	/**
	 * @since 1.0.0
	 *
	 * @return array.
	 */
	public function get_sidebars( $data = '' ) {
		global $wp_registered_sidebars;

		$options = array();

		if ( ! $wp_registered_sidebars ) {
			$options[''] = __( 'No sidebars were found', 'cmsmasters-elementor' );
		} else {
			$options[''] = __( 'No Sidebar', 'cmsmasters-elementor' );

			foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar ) {
				$options[ $sidebar_id ] = $sidebar['name'];
			}
		}

		if ( 'default_key' === $data ) {
			$default_key = array_keys( $options );
			$default_key = array_shift( $default_key );

			return $default_key;
		} elseif ( 'options' === $data ) {
			return $options;
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @return array saved templates.
	 */
	public function get_page_template_options( $type = '' ) {
		$page_templates = $this->cmsmasters_page_templates( $type );

		$options[-1] = __( 'Select', 'cmsmasters-elementor' );

		if ( count( $page_templates ) ) {
			foreach ( $page_templates as $id => $name ) {
				$options[ $id ] = $name;
			}
		} else {
			$options['no_template'] = __( 'No saved templates found!', 'cmsmasters-elementor' );
		}

		return $options;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return array templates.
	 */
	public function cmsmasters_page_templates( $type = '' ) {
		$args = array(
			'post_type' => 'elementor_library',
			'posts_per_page' => -1,
		);

		if ( $type ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'elementor_library_type',
					'field' => 'slug',
					'terms' => $type,
				),
			);
		}

		$page_templates = get_posts( $args );

		$options = array();

		if ( ! empty( $page_templates ) && ! is_wp_error( $page_templates ) ) {
			foreach ( $page_templates as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
		}

		return $options;
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
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 */
	protected function content_template() {}

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
				'field' => 'trigger_text',
				'type' => esc_html__( 'Trigger Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'close_button_text',
				'type' => esc_html__( 'Close Button Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}

	/**
	 * Get fields_in_item config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields in item config.
	 */
	public static function get_wpml_fields_in_item() {
		return array(
			'content_block' => array(
				array(
					'field' => 'title',
					'type' => esc_html__( 'Title Text', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'logo_title',
					'type' => esc_html__( 'Logo Title Text', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				'logo_custom_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'Logo Custom Url', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				array(
					'field' => 'description',
					'type' => esc_html__( 'Description', 'cmsmasters-elementor' ),
					'editor_type' => 'VISUAL',
				),
			),
		);
	}
}
