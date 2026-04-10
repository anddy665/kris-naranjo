<?php
namespace CmsmastersElementor\Modules\ShareButtons\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Share_Buttons extends Base_Widget {

	private static $networks = array();

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
		return __( 'Share Buttons', 'cmsmasters-elementor' );
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
		return 'cmsicon-share';
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
			'share',
			'sharing',
			'social',
			'button',
			'icon',
			'like',
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
			'widget-cmsmasters-share-buttons',
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
	 * Share buttons widget constructor.
	 *
	 * Initializing the share buttons widget class.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Widget data. Default is an empty array.
	 * @param array|null $args Optional. Widget default arguments. Default is null.
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		$this->set_default_networks();
	}

	/**
	 * Share buttons widget constructor.
	 *
	 * Initializing the share buttons widget class.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Widget data. Default is an empty array.
	 * @param array|null $args Optional. Widget default arguments. Default is null.
	 */
	private function set_default_networks() {
		$default_network = array(
			'delicious' => array(
				'label' => __( 'Delicious', 'cmsmasters-elementor' ),
				'url' => 'https://del.icio.us/save?url={url}&title={title}',
			),
			'digg' => array(
				'label' => __( 'Digg', 'cmsmasters-elementor' ),
				'url' => 'https://digg.com/submit?url={url}',
			),
			'email' => array(
				'label' => __( 'Email', 'cmsmasters-elementor' ),
				'class' => 'cmsmasters-email',
				'url' => 'mailto:?subject={title}&body={text}{url}',
				'target' => '',
			),
			'facebook' => array(
				'label' => __( 'Facebook', 'cmsmasters-elementor' ),
				'url' => 'https://www.facebook.com/sharer.php?u={url}',
			),
			'linkedin' => array(
				'label' => __( 'LinkedIn', 'cmsmasters-elementor' ),
				'url' => 'https://www.linkedin.com/shareArticle?mini=true&url={url}&title={title}&summary={text}&source={url}',
			),
			'ok' => array(
				'label' => __( 'OK', 'cmsmasters-elementor' ),
				'url' => 'https://connect.ok.ru/offer?url={url}&imageUrl={image}&title={title}',
			),
			'pinterest' => array(
				'label' => __( 'Pinterest', 'cmsmasters-elementor' ),
				'class' => 'cmsmasters-pinterest',
				'url' => 'https://www.pinterest.com/pin/create/button/',
			),
			'pocket' => array(
				'label' => __( 'Pocket', 'cmsmasters-elementor' ),
				'url' => 'https://getpocket.com/edit?url={url}',
			),
			'print' => array(
				'label' => __( 'Print', 'cmsmasters-elementor' ),
				'class' => 'cmsmasters-print',
				'url' => '',
				'target' => '',
			),
			'reddit' => array(
				'label' => __( 'Reddit', 'cmsmasters-elementor' ),
				'url' => 'https://reddit.com/submit?url={url}&title={title}',
			),
			'skype' => array(
				'label' => __( 'Skype', 'cmsmasters-elementor' ),
				'url' => 'https://web.skype.com/share?url={url}',
			),
			'stumbleupon' => array(
				'label' => __( 'StumbleUpon', 'cmsmasters-elementor' ),
				'url' => 'https://www.stumbleupon.com/submit?url={url}',
			),
			'telegram' => array(
				'label' => __( 'Telegram', 'cmsmasters-elementor' ),
				'url' => 'https://telegram.me/share/url?url={url}&text={text}',
			),
			'tumblr' => array(
				'label' => __( 'Tumblr', 'cmsmasters-elementor' ),
				'url' => 'https://tumblr.com/share/link?url={url}',
			),
			'twitter' => array(
				'label' => __( 'X Twitter', 'cmsmasters-elementor' ),
				'url' => 'https://twitter.com/intent/tweet?url={url}&text={text}',
			),
			'vk' => array(
				'label' => __( 'VK', 'cmsmasters-elementor' ),
				'url' => 'https://vkontakte.ru/share.php?url={url}&title={title}&image={image}&description={text}',
			),
			'whatsapp' => array(
				'label' => __( 'WhatsApp', 'cmsmasters-elementor' ),
				'url' => 'https://api.whatsapp.com/send?text={title}{text}{url}',
			),
			'xing' => array(
				'label' => __( 'XING', 'cmsmasters-elementor' ),
				'url' => 'https://www.xing.com/app/user?op=share&url={url}',
			),
		);

		foreach ( $default_network as $network_name => $network_props ) {
			self::$networks[ $network_name ] = array(
				'class' => '',
				'target' => '_blank',
			);

			foreach ( $network_props as $key => $value ) {
				self::$networks[ $network_name ][ $key ] = $value;
			}
		}

		/**
		 * Filters networks.
		 *
		 * Filters share buttons widget networks array.
		 *
		 * @since 1.0.0
		 *
		 * @param array $networks Default networks.
		 */
		self::$networks = apply_filters( 'cmsmasters_elementor/widgets/share_buttons/networks', self::$networks );
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
	 * @since 1.2.3 Fixed error with responsive controls in elementor 3.4.0
	 * @since 1.3.1 Fixed `Box Shadow` control in norman and hover state.
	 * @since 1.3.3 Added support custom breakpoints.
	 * @since 1.11.6 Added `Min Size` control for `View` icon in Share Buttons widget.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_buttons_content',
			array( 'label' => __( 'Share Buttons', 'cmsmasters-elementor' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'button',
			array(
				'label' => __( 'Network', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'options' => wp_list_pluck( self::get_networks(), 'label' ),
				'default' => 'facebook',
				'label_block' => false,
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'recommended' => $this->get_recommended_icons(),
			)
		);

		$repeater->add_control(
			'text',
			array(
				'label' => __( 'Custom Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
			)
		);

		$repeater->end_controls_tabs();

		$this->add_control(
			'share_buttons',
			array(
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => array(
					array(
						'button' => 'facebook',
						'icon' => array(
							'value' => 'fab fa-facebook-f',
							'library' => 'fa-brands',
						),
					),
					array(
						'button' => 'twitter',
						'icon' => array(
							'value' => 'fab fa-x-twitter',
							'library' => 'fa-brands',
						),
					),
					array(
						'button' => 'pinterest',
						'icon' => array(
							'value' => 'fab fa-pinterest-p',
							'library' => 'fa-brands',
						),
					),
					array(
						'button' => 'email',
						'icon' => array(
							'value' => 'far fa-envelope',
							'library' => 'fa-regular',
						),
					),
				),
				'title_field' => '<i class="{{ icon.value }}" aria-hidden="true"></i> <span style="text-transform: capitalize;">{{{ button }}}</span>',
			)
		);

		$this->add_control(
			'view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'description' => __( 'Show Only Icons', 'cmsmasters-elementor' ),
					),
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => __( 'Show Only Text', 'cmsmasters-elementor' ),
					),
					'icon-text' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => __( 'Show Icon & Text', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'icon-text',
				'toggle' => false,
				'label_block' => false,
				'separator' => 'before',
				'prefix_class' => 'cmsmasters-share-buttons__view-',
				'render_type' => 'template',
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label' => __( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'auto' => __( 'Auto', 'cmsmasters-elementor' ),
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
					'6' => 6,
					'7' => 7,
					'8' => 8,
					'9' => 9,
					'10' => 10,
				),
				'prefix_class' => 'cmsmasters-share-buttons__columns%s-',
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__item' => 'width: calc(100% / {{SIZE}});',
				),
			)
		);

		$this->add_control(
			'icon_position',
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
				'prefix_class' => 'cmsmasters-share-buttons__align-',
				'condition' => array(
					'view' => array(
						'icon-text',
						'text',
					),
				),
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
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
				'prefix_class' => 'cmsmasters-share-buttons__position%s-',
				'condition' => array(
					'columns!' => '1',
				),
			)
		);

		$this->add_control(
			'share_url_type',
			array(
				'label' => __( 'Target URL', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'current' => array(
						'title' => __( 'Current', 'cmsmasters-elementor' ),
						'description' => __( 'Current Page', 'cmsmasters-elementor' ),
					),
					'custom' => array(
						'title' => __( 'Custom', 'cmsmasters-elementor' ),
						'description' => __( 'Custom URL', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'current',
				'label_block' => false,
				'toggle' => false,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'share_url',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'dynamic' => array(
					'active' => true,
				),
				'show_external' => false,
				'show_label' => false,
				'condition' => array( 'share_url_type' => 'custom' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_buttons_style',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'columns_gap',
			array(
				'label' => __( 'Columns Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 10 ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__wrapper .elementor-widget-cmsmasters-share-buttons__item' => 'padding: calc({{rows_gap.SIZE}}{{rows_gap.UNIT}} / 2) calc({{SIZE}}{{UNIT}} / 2)',
					'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__wrapper' => 'margin: calc(-{{rows_gap.SIZE}}{{rows_gap.UNIT}} / 2) calc(-{{SIZE}}{{UNIT}} / 2)',
				),
			)
		);

		$this->add_responsive_control(
			'rows_gap',
			array(
				'label' => __( 'Rows Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 10 ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'render_type' => 'ui',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_buttons_style_content',
			array(
				'label' => __( 'Share Button settings', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_count',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__btn-text',
				'condition' => array(
					'view' => array(
						'icon-text',
						'text',
					),
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'min' => 0.5,
						'max' => 4,
						'step' => 0.1,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'tablet_default' => array( 'unit' => 'px' ),
				'mobile_default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__btn-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'view!' => 'text' ),
			)
		);

		$this->add_responsive_control(
			'icon_min_size',
			array(
				'label' => __( 'Min Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'min' => 0.5,
						'max' => 4,
						'step' => 0.1,
					),
				),
				'size_units' => array(
					'px',
					'em',
					'vw',
					'vh',
					'custom',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--icon-min-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'view' => 'icon' ),
			)
		);

		$this->add_responsive_control(
			'icon_pdd',
			array(
				'label' => __( 'Icon Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'tablet_default' => array( 'unit' => 'px' ),
				'mobile_default' => array( 'unit' => 'px' ),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__btn .elementor-widget-cmsmasters-share-buttons__btn-icon' => 'padding: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'view!' => 'text' ),
			)
		);

		$this->add_control(
			'rotate',
			array(
				'label' => __( 'Icon Rotate', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'deg' => array( 'step' => 10 ),
				),
				'size_units' => array( 'deg' ),
				'default' => array( 'unit' => 'deg' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__btn' => 'transform:rotate({{SIZE}}{{UNIT}});',
					'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__btn .elementor-widget-cmsmasters-share-buttons__btn-icon i' => 'transform:rotate(-{{SIZE}}{{UNIT}});',
				),
				'condition' => array(
					'view' => array( 'icon' ),
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label' => __( 'Button Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__item .elementor-widget-cmsmasters-share-buttons__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
				'condition' => array(
					'view' => array(
						'icon-text',
						'text',
					),
				),
			)
		);

		$this->add_responsive_control(
			'gap_between',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 150,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
						'step' => 0.1,
					),
					'em' => array(
						'min' => 0,
						'max' => 20,
						'step' => 0.1,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
					'%',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}:not(.cmsmasters-share-buttons__align-center).cmsmasters-share-buttons__align-right .elementor-widget-cmsmasters-share-buttons__wrapper .elementor-widget-cmsmasters-share-buttons__item .elementor-widget-cmsmasters-share-buttons__btn .elementor-widget-cmsmasters-share-buttons__btn-text' => 'padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}:not(.cmsmasters-share-buttons__align-center).cmsmasters-share-buttons__align-left .elementor-widget-cmsmasters-share-buttons__wrapper .elementor-widget-cmsmasters-share-buttons__item .elementor-widget-cmsmasters-share-buttons__btn .elementor-widget-cmsmasters-share-buttons__btn-text' => 'padding-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-share-buttons__align-center .elementor-widget-cmsmasters-share-buttons__wrapper .elementor-widget-cmsmasters-share-buttons__item .elementor-widget-cmsmasters-share-buttons__btn .elementor-widget-cmsmasters-share-buttons__btn-text' => 'padding-top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'view' => array( 'icon-text' ),
				),
			)
		);

		$this->add_responsive_control(
			'button_height',
			array(
				'label' => __( 'Button Min Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 20,
						'max' => 200,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__btn' => 'min-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'view' => array(
						'icon-text',
						'text',
					),
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_button',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__btn',
				'exclude' => array( 'color' ),
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'color_source',
									'operator' => '===',
									'value' => 'official',
								),
								array(
									'name' => 'icon_official_color',
									'operator' => '===',
									'value' => 'yes',
								),
							),
						),
						array(
							'name' => 'color_source',
							'operator' => '===',
							'value' => 'custom',
						),
					),
				),
			)
		);

		$this->update_control(
			'border_button_border',
			array(
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
					'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
				),
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__item .elementor-widget-cmsmasters-share-buttons__btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'color_source',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => array(
					'official' => __( 'Official', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom', 'cmsmasters-elementor' ),
					'gradient' => __( 'Gradient', 'cmsmasters-elementor' ),
				),
				'default' => 'official',
				'prefix_class' => 'cmsmasters-share-buttons__color-',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'icon_official_color',
			array(
				'label' => __( 'Content Official Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'cmsmasters-share-buttons__color-icon-',
				'condition' => array(
					'color_source' => 'official',
				),
			)
		);

		$this->add_control(
			'color_icon_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__wrapper .elementor-widget-cmsmasters-share-buttons__item .elementor-widget-cmsmasters-share-buttons__item-inner .elementor-widget-cmsmasters-share-buttons__btn' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__wrapper .elementor-widget-cmsmasters-share-buttons__item .elementor-widget-cmsmasters-share-buttons__item-inner .elementor-widget-cmsmasters-share-buttons__btn-icon' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'color_source' => 'official',
					'icon_official_color' => 'yes',
				),
			)
		);

		$this->add_control(
			'color_icon_bd',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__wrapper .elementor-widget-cmsmasters-share-buttons__item .elementor-widget-cmsmasters-share-buttons__item-inner .elementor-widget-cmsmasters-share-buttons__btn' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'color_source' => 'official',
					'icon_official_color' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'share_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'hover' === $key ) ? ':hover' : '';
			$selector = "{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__wrapper .elementor-widget-cmsmasters-share-buttons__item .elementor-widget-cmsmasters-share-buttons__item-inner{$state} .elementor-widget-cmsmasters-share-buttons__btn";

			$this->start_controls_tab(
				"share_tab_{$key}",
				array(
					'label' => $label,
					'condition' => array(
						'color_source!' => 'official',
					),
				)
			);

			$this->add_control(
				"color_text_{$key}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'render_type' => 'ui',
					'selectors' => array(
						$selector => 'color: {{VALUE}};',
					),
					'condition' => array(
						'view' => array(
							'icon-text',
							'text',
						),
						'color_source!' => 'official',
					),
				)
			);

			$this->add_control(
				"background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						'view' => array(
							'icon-text',
							'text',
						),
						'color_source' => array( 'custom' ),
					),
				)
			);

			$this->add_control(
				"color_icon_{$key}",
				array(
					'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector . ' .elementor-widget-cmsmasters-share-buttons__btn-icon' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'color_source!' => 'official',
						'view!' => 'text',
					),
				)
			);

			$this->add_control(
				"background_color_icon_{$key}",
				array(
					'label' => __( 'Icon Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector . ' .elementor-widget-cmsmasters-share-buttons__btn-icon' => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						'color_source' => array( 'custom' ),
						'view!' => 'text',
					),
				)
			);

			$this->add_control(
				"border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'render_type' => 'ui',
					'selectors' => array(
						$selector => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'color_source' => array( 'custom' ),
					),
				)
			);

			if ( 'normal' === $key ) {
				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name' => 'background',
						'types' => array( 'gradient' ),
						'label_block' => true,
						'fields_options' => array(
							'background' => array(
								'type' => Controls_Manager::HIDDEN,
								'default' => 'gradient',
							),
							'gradient_angle' => array(
								'default' => array(
									'unit' => 'deg',
									'size' => 90,
								),
							),
						),
						'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__wrapper .elementor-widget-cmsmasters-share-buttons__item .elementor-widget-cmsmasters-share-buttons__item-inner .elementor-widget-cmsmasters-share-buttons__btn, {{WRAPPER}}.cmsmasters-share-buttons__view-icon .elementor-widget-cmsmasters-share-buttons__wrapper .elementor-widget-cmsmasters-share-buttons__item .elementor-widget-cmsmasters-share-buttons__item-inner .elementor-widget-cmsmasters-share-buttons__btn-icon',
						'condition' => array(
							'color_source' => array( 'gradient' ),
						),
					)
				);
			}

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "box_shadow_{$key}",
					'selector' => "{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__wrapper .elementor-widget-cmsmasters-share-buttons__item .elementor-widget-cmsmasters-share-buttons__item-inner{$state} .elementor-widget-cmsmasters-share-buttons__btn, {{WRAPPER}}.cmsmasters-share-buttons__view-icon .elementor-widget-cmsmasters-share-buttons__wrapper .elementor-widget-cmsmasters-share-buttons__item .elementor-widget-cmsmasters-share-buttons__item-inner{$state} .elementor-widget-cmsmasters-share-buttons__btn .elementor-widget-cmsmasters-share-buttons__btn-icon",
					'condition' => array(
						'color_source!' => 'official',
					),
				)
			);

			if ( 'hover' === $key ) {
				$this->add_control(
					'button_transition',
					array(
						'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'range' => array(
							's' => array(
								'min' => 0,
								'max' => 5,
								'step' => 0.1,
							),
						),
						'size_units' => array( 's' ),
						'default' => array( 'unit' => 's' ),
						'selectors' => array(
							'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__btn' => 'transition-duration:{{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__btn .elementor-widget-cmsmasters-share-buttons__btn-icon' => 'transition-duration:{{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .elementor-widget-cmsmasters-share-buttons__btn .elementor-widget-cmsmasters-share-buttons__btn-inner' => 'transition-duration:{{SIZE}}{{UNIT}};',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public static function get_networks( $network_name = false ) {
		if ( ! $network_name ) {
			return self::$networks;
		}

		if ( ! isset( self::$networks[ $network_name ] ) ) {
			return false;
		}

		return self::$networks[ $network_name ];
	}

	public function get_recommended_icons() {
		$recommended_icons = array(
			'fa-brands' => array(
				'delicious',
				'digg',
				'facebook',
				'facebook-f',
				'facebook-square',
				'get-pocket',
				'linkedin',
				'linkedin-in',
				'odnoklassniki',
				'odnoklassniki-square',
				'pinterest',
				'pinterest-p',
				'pinterest-square',
				'reddit',
				'reddit-alien',
				'reddit-square',
				'skype',
				'stumbleupon',
				'stumbleupon-circle',
				'telegram',
				'telegram-plane',
				'tumblr',
				'tumblr-square',
				'twitter',
				'twitter-square',
				'x-twitter',
				'x-twitter-square',
				'vk',
				'whatsapp',
				'whatsapp-square',
				'xing',
				'xing-square',
			),
			'fa-regular' => array( 'envelope' ),
			'fa-solid' => array(
				'envelope',
				'print',
			),
		);

		/**
		 * Filters recommended icons.
		 *
		 * Filters share buttons widget recommended icons array.
		 *
		 * @since 1.0.0
		 *
		 * @param array $recommended_icons Recommended icons.
		 */
		$recommended_icons = apply_filters( 'cmsmasters_elementor/widgets/share_buttons/recommended_icons', $recommended_icons );

		return $recommended_icons;
	}

	protected function render() {
		$settings = $this->get_active_settings();

		if ( empty( $settings['share_buttons'] ) ) {
			return;
		}

		$show_icon = array(
			'icon-text',
			'icon',
		);
		$show_text = array(
			'icon-text',
			'text',
		);

		$widget_name = 'elementor-widget-' . $this->get_name();

		$this->add_render_attribute( 'wrapper', 'class', "{$widget_name}__wrapper" );

		$this->add_render_attribute( 'btn-icon', 'class', "{$widget_name}__btn-icon" );
		$this->add_render_attribute( 'btn-text', 'class', "{$widget_name}__btn-text" );
		$this->add_render_attribute( 'btn-title', 'class', "{$widget_name}__btn-title" );

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

		foreach ( $settings['share_buttons'] as $button ) {
			$network = self::get_networks( $button['button'] );

			if ( ! $network ) {
				continue;
			}

			$this->add_render_attribute( 'item', array(
				'class' => "{$widget_name}__item elementor-repeater-item-" . esc_attr( $button['_id'] ),
			), null, true );

			$this->add_render_attribute( 'btn', array(
				'class' => "{$widget_name}__btn {$widget_name}__btn-" . esc_attr( $button['button'] ),
			), null, true );

			$button_text = ( $button['text'] ) ? $button['text'] : $network['label'];

			echo '<div ' . $this->get_render_attribute_string( 'item' ) . '>' .
				'<a 
					class="' . $widget_name . '__item-inner ' . esc_attr( $network['class'] ) . '" 
					href="' . esc_url( $this->get_url( $button ) ) . '" 
					target="' . esc_attr( $network['target'] ) . '"' .
					( 'pinterest' === $button['button'] ? ' data-pin-do="buttonBookmark" data-pin-custom="true"' : '' ) .
					( in_array( $settings['view'], array( 'icon' ), true ) && 'pinterest' !== $button['button'] ? ' aria-label="' . esc_attr( $button_text ) . '"' : '' ) .
				'>' .
					'<span ' . $this->get_render_attribute_string( 'btn' ) . '>';

			if ( in_array( $settings['view'], $show_icon, true ) ) {
				echo '<span ' . $this->get_render_attribute_string( 'btn-icon' ) . '>';

				if ( $button['icon']['value'] && $button['icon']['library'] ) {
					Icons_Manager::render_icon( $button['icon'], array( 'aria-hidden' => 'true' ) );
				} else {
					echo '<i class="fas fa-ban" aria-hidden="true"></i>';
				}

				echo '</span>';
			}

			if ( in_array( $settings['view'], $show_text, true ) ) {
				echo '<span ' . $this->get_render_attribute_string( 'btn-text' ) . '>' .
					'<span ' . $this->get_render_attribute_string( 'btn-title' ) . '>' .
						esc_html( $button_text ) .
					'</span>' .
				'</span>';
			}

			echo '</span>' .
				'</a>' .
			'</div>';
		}

		echo '</div>';
	}

	private function get_url( $button ) {
		$link = get_permalink();
		$text = '';
		$title = esc_html( get_the_title() );
		$image = ( 'email' === $button['button'] ) ? rawurlencode( $link ) : urlencode_deep( self::get_share_image_url() );

		$settings = $this->get_active_settings();
		$network = self::get_networks( $button['button'] );

		if ( 'custom' === $settings['share_url_type'] ) {
			$link = $settings['share_url']['url'];
		}

		$link = ( 'email' === $button['button'] ) ? rawurlencode( $link ) : urlencode_deep( $link );

		if ( is_single() ) {
			$text = esc_html( get_the_excerpt() );
		}

		$url = str_replace(
			array(
				'{url}',
				'{text}',
				'{title}',
				'{image}',
			),
			array(
				$link,
				$text,
				$title,
				$image,
			),
			$network['url']
		);

		return $url;
	}

	private static function get_share_image_url() {
		if ( has_post_thumbnail() ) {
			return wp_get_attachment_url( get_post_thumbnail_id() );
		}

		preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', do_shortcode( get_the_content() ), $img_matches );

		if ( ! empty( $img_matches[1][0] ) ) {
			$url = $img_matches[1][0];
		} else {
			$url = self::get_logo_url();
		}

		/**
		 * Filters default image URL.
		 *
		 * Filters share buttons widget default image URL if post has
		 * no attached image.
		 *
		 * @since 1.0.0
		 *
		 * @param string $url Default share image URL.
		 * @param string $content Post content.
		 */
		$url = apply_filters( 'cmsmasters_elementor/widgets/share_buttons/default_share_img_url', $url, get_the_content() );

		return $url;
	}

	/**
	 * Get Logo Url.
	 *
	 * @since 1.0.0
	 * @since 1.2.1 Fixed logo url.
	 *
	 * @return string The logo url.
	 */
	private static function get_logo_url() {
		if ( has_custom_logo() ) {
			$image_src = wp_get_attachment_image_src( get_custom_logo(), 'full' );

			$logo_url = $image_src[0];
		}

		if ( empty( $logo_url ) ) {
			// TODO: Change to real default theme logo image url.
			$logo_url = get_parent_theme_file_uri( 'theme-config/images/logo.svg' );
		}

		/**
		 * Filters logo URL.
		 *
		 * Filters share buttons widget logo image URL.
		 *
		 * @since 1.0.0
		 *
		 * @param string $logo_url Logo image URL.
		 */
		$logo_url = apply_filters( 'cmsmasters_elementor/widgets/share_buttons/logo_url', $logo_url );

		return $logo_url;
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
			'share_buttons' => array(
				array(
					'field' => 'text',
					'type' => esc_html__( 'Custom Label', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
			),
		);
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
			'share_url' => array(
				'field' => 'url',
				'type' => esc_html__( 'Custom Link', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
		);
	}
}
