<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControlsManager;
use CmsmastersElementor\Modules\AjaxWidget\Module as AjaxWidgetModule;
use CmsmastersElementor\Modules\Settings\Settings_Page;
use CmsmastersElementor\Modules\SocialCounter\Widgets\Items;
use CmsmastersElementor\Modules\SocialCounter\Widgets\Skins;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Social Counter widget.
 *
 * Addon widget that displays current social counter.
 *
 * @since 1.0.0
 */
class Social_Counter extends Base_Widget {

	/**
	 * @since 1.0.0
	 */
	protected $_has_template_content = false; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Instance social counter skin.
	 *
	 * @var Items\Base
	 */
	private $social_item;

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Social Counter', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_icon() {
		return 'cmsicon-social-counter';
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
			'social',
			'counter',
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
			'widget-cmsmasters-social-counter',
		);

		if ( Icons_Manager::is_migration_allowed() ) {
			$style_depends = array_merge( $style_depends, array(
				'elementor-icons-fa-solid',
				'elementor-icons-fa-brands',
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
	 * Get Default column.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	protected function get_default_column() {
		return 4;
	}

	/**
	 * Get default order of types.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_default_order() {
		return array( 'icon', 'numbers', 'title' );
	}

	/**
	 * Register widget skins.
	 *
	 * This method is activated while initializing the widget base class.
	 * It is used to assign skins to widgets with `add_skin()` method.
	 *
	 * @since 1.0.0
	 */
	protected function register_skins() {
		$this->add_skin( new Skins\Box( $this ) );
		$this->add_skin( new Skins\Side( $this ) );
		$this->add_skin( new Skins\Tooltip( $this ) );
	}

	/**
	 * @since 1.0.0
	 */
	public function register_controls() {
		$this->register_controls_content();
		$this->register_controls_socials();
		$this->register_controls_style_layout();
		$this->register_controls_style_content();
		$this->register_controls_notice();
	}

	/**
	 * Register social-counter controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_content() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label' => __( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => $this->get_default_column(),
				'mobile_default' => '1',
				'options' => array(
					'' => esc_attr__( 'Auto', 'cmsmasters-elementor' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-social-counter .social-item' => 'width: calc(100% / {{VALUE}});',
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label' => __( 'Order', 'cmsmasters-elementor' ),
				'type' => CmsmastersControlsManager::SELECTIZE,
				'label_block' => true,
				'options' => array(
					'icon' => __( 'Icon', 'cmsmasters-elementor' ),
					'title' => __( 'Title', 'cmsmasters-elementor' ),
					'numbers' => __( 'Numbers', 'cmsmasters-elementor' ),
				),
				'multiple' => true,
				'default' => $this->get_default_order(),
			)
		);

		$this->add_control(
			'social_items',
			array(
				'label' => __( 'Socials', 'cmsmasters-elementor' ),
				'type' => CmsmastersControlsManager::SELECTIZE,
				'multiple' => true,
				'options' => $this->get_social_items_options(),
				'default' => array(
					Items\Facebook::get_name(),
					Items\Pinterest::get_name(),
					Items\Reddit::get_name(),
					Items\Dribbble::get_name(),
				),
			)
		);

		$this->add_control(
			'alignment',
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
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-social-counter-inner' => 'justify-content: {{VALUE}};',
				),
				'condition' => array( 'columns' => '' ),
			)
		);

		$this->add_control(
			'in_new_window',
			array(
				'label' => __( 'Open in new tab', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'counter_align',
			array(
				'label' => __( 'Counter Align', 'cmsmasters-elementor' ),
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
				'default' => 'flex-start',
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-social-counter-inner' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	public function controls_contains_conditions( $value = '' ) {
		return array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'order',
					'operator' => 'contains',
					'value' => $value,
				),
			),
		);
	}

	/**
	 * Register social-counter controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_socials() {
		foreach ( self::get_social_classes() as $social_name => $social_item_class ) {
			$social_item_types = $social_item_class::get_types();
			$control_id = "type_{$social_name}";
			$control_args = array(
				'type' => Controls_Manager::HIDDEN,
				'default' => $social_item_class::get_type_default(),
			);

			if ( count( $social_item_types ) > 1 ) {
				$control_args['label'] = esc_html__( 'Counter By', 'cmsmasters-elementor' );
				$control_args['type'] = Controls_Manager::SELECT;
				$control_args['options'] = $social_item_types;
			}

			$this->start_controls_section(
				"section_content_{$social_name}",
				array(
					'label' => $social_item_class::get_label(),
					'tab' => Controls_Manager::TAB_CONTENT,
					'condition' => array(
						'social_items' => $social_name,
					),
				)
			);

			$this->add_control( $control_id, $control_args );

			$this->add_control(
				"icon_type_{$social_name}",
				array(
					'label' => __( 'Icon', 'cmsmasters-elementor' ),
					'type' => CmsmastersControlsManager::CHOOSE_TEXT,
					'label_block' => false,
					'default' => 'official',
					'options' => array(
						'official' => array(
							'title' => __( 'Official', 'cmsmasters-elementor' ),
						),
						'custom' => array(
							'title' => __( 'Custom', 'cmsmasters-elementor' ),
						),
					),
					'conditions' => $this->controls_contains_conditions( 'icon' ),
				)
			);

			$this->add_control(
				"icon_{$social_name}",
				array(
					'label' => __( 'Choose', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::ICONS,
					'default' => $social_item_class::get_default_icon(),
					'recommended' => $this->get_recommended_icons(),
					'description' => esc_html__( 'If empty then official icon', 'cmsmasters-elementor' ),
					'condition' => array(
						"icon_type_{$social_name}" => 'custom',
					),
					'conditions' => $this->controls_contains_conditions( 'icon' ),
				)
			);

			$this->add_control(
				"url_type_{$social_name}",
				array(
					'label' => __( 'URL type', 'cmsmasters-elementor' ),
					'type' => CmsmastersControlsManager::CHOOSE_TEXT,
					'default' => 'default',
					'label_block' => false,
					'separator' => 'before',
					'options' => array(
						'default' => array(
							'title' => __( 'Default', 'cmsmasters-elementor' ),
						),
						'custom' => array(
							'title' => __( 'custom', 'cmsmasters-elementor' ),
						),
					),
				)
			);

			$this->add_control(
				"url_{$social_name}",
				array(
					'label' => __( 'URL Custom', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'condition' => array(
						'url_type' => 'custom',
					),
				)
			);

			$this->add_control(
				"title_type_{$social_name}",
				array(
					'label' => __( 'Title Type', 'cmsmasters-elementor' ),
					'type' => CmsmastersControlsManager::CHOOSE_TEXT,
					'label_block' => false,
					'separator' => 'before',
					'default' => 'default',
					'options' => array(
						'default' => array(
							'title' => __( 'Default', 'cmsmasters-elementor' ),
						),
						'custom' => array(
							'title' => __( 'custom', 'cmsmasters-elementor' ),
						),
					),
				)
			);

			$this->add_control(
				"title_{$social_name}",
				array(
					'type' => Controls_Manager::TEXT,
					'label' => __( 'Title', 'cmsmasters-elementor' ),
					'condition' => array(
						"title_type_{$social_name}" => 'custom',
					),
				)
			);

			$this->start_controls_tabs( "social_tabs_{$social_name}" );

			$states = array(
				'normal' => __( 'Normal', 'cmsmasters-elementor' ),
				'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			);

			foreach ( $states as $state => $label ) {
				$selector_item = "{{WRAPPER}} .social-item[data-name=\"{$social_name}\"] .social-link";

				if ( 'hover' === $state ) {
					$selector_item .= ':hover';
				}

				$this->start_controls_tab(
					"social_tab_{$social_name}_{$state}",
					array(
						'label' => $label,
					)
				);

				$this->add_control(
					"color_numbers_{$social_name}_{$state}",
					array(
						'label' => __( 'Numbers Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'render_type' => 'ui',
						'selectors' => array(
							"{$selector_item} .social-numbers" => 'color: {{VALUE}};',
						),
						'conditions' => $this->controls_contains_conditions( 'title' ),
					)
				);

				$this->add_control(
					"color_title_{$social_name}_{$state}",
					array(
						'label' => __( 'Title Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'render_type' => 'ui',
						'selectors' => array(
							"{$selector_item} .social-title" => 'color: {{VALUE}};',
						),
						'conditions' => $this->controls_contains_conditions( 'title' ),
					)
				);

				$this->end_controls_tab();
			}

			$this->end_controls_tabs();

			$this->end_controls_section();
		}
	}

	/**
	 * Register social-counter controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 * @since 1.7.5 Fixed social counter.
	 */
	protected function register_controls_notice() {
		$this->start_injection( array(
			'of' => '_skin',
			'at' => 'before',
		) );

		$notice_link = sprintf( '<a href="%2$s" target="_blank">%1$s</a>',
			__( 'CMSMasters Integration', 'cmsmasters-elementor' ),
			esc_url( Settings_Page::get_url() )
		);

		/* translators: Social Counter widget empty API keys notice. %s: Addon Integration settings link */
		$notice_html = sprintf( __( 'Set your API keys in the %s settings.', 'cmsmasters-elementor' ), $notice_link );

		$this->add_control(
			'notice',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => $notice_html,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
			)
		);

		$this->end_injection();
	}

	/**
	 * Register social-counter controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_style_content() {
		$this->start_controls_section(
			'section_style_common_style',
			array(
				'label' => __( 'Common Style', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'order',
							'operator' => 'contains',
							'value' => 'icon',
						),
						array(
							'name' => 'order',
							'operator' => 'contains',
							'value' => 'numbers',
						),
						array(
							'name' => 'order',
							'operator' => 'contains',
							'value' => 'title',
						),
					),
				),
			)
		);

		$this->add_control(
			'icon_heading',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $this->controls_contains_conditions( 'icon' ),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 80,
					),
					'em' => array(
						'min' => 0.1,
						'max' => 5,
						'step' => 0.1,
					),
				),
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					'{{WRAPPER}} .social-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .social-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $this->controls_contains_conditions( 'icon' ),
			)
		);

		$this->add_control(
			'color_icon_normal',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}} .social-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'conditions' => $this->controls_contains_conditions( 'icon' ),
			)
		);

		$this->add_control(
			'color_icon_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}} .social-link:hover .social-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'conditions' => $this->controls_contains_conditions( 'icon' ),
			)
		);

		$this->add_responsive_control(
			'icon_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .social-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'conditions' => $this->controls_contains_conditions( 'icon' ),
			)
		);

		$this->add_control(
			'numbers_heading',
			array(
				'label' => __( 'Numbers', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $this->controls_contains_conditions( 'numbers' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_numbers',
				'label' => __( 'Numbers Typography', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .social-numbers',
				'exclude' => array(
					'line_height',
					'text_transform',
				),
				'conditions' => $this->controls_contains_conditions( 'numbers' ),
			)
		);

		$this->add_control(
			'color_numbers_normal',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}} .social-numbers' => 'color: {{VALUE}};',
				),
				'conditions' => $this->controls_contains_conditions( 'numbers' ),
			)
		);

		$this->add_control(
			'color_numbers_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}} .social-link:hover .social-numbers' => 'color: {{VALUE}};',
				),
				'conditions' => $this->controls_contains_conditions( 'numbers' ),
			)
		);

		$this->add_control(
			'numbers_format',
			array(
				'label' => __( 'Format', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'short',
				'options' => array(
					'short' => __( 'Short', 'cmsmasters-elementor' ) . ' (' . $this->get_numbers_by_format( 123456, 'short' ) . ')',
					'comma_separator' => __( 'Comma separator', 'cmsmasters-elementor' ) . ' (' . $this->get_numbers_by_format( 123456, 'comma_separator' ) . ')',
					'full' => __( 'Full', 'cmsmasters-elementor' ) . ' (' . $this->get_numbers_by_format( 123456, 'full' ) . ')',
				),
				'conditions' => $this->controls_contains_conditions( 'numbers' ),
			)
		);

		$this->add_responsive_control(
			'numbers_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .social-numbers' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => array( '_skin!' => 'tooltip' ),
				'conditions' => $this->controls_contains_conditions( 'numbers' ),
			)
		);

		$this->add_control(
			'title_heading',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $this->controls_contains_conditions( 'title' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_title',
				'label' => __( 'Title Typography', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .social-title',
				'conditions' => $this->controls_contains_conditions( 'title' ),
			)
		);

		$this->add_control(
			'color_title_normal',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}} .social-title' => 'color: {{VALUE}};',
				),
				'conditions' => $this->controls_contains_conditions( 'title' ),
			)
		);

		$this->add_control(
			'color_title_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}} .social-link:hover .social-title' => 'color: {{VALUE}};',
				),
				'conditions' => $this->controls_contains_conditions( 'title' ),
			)
		);

		$this->add_responsive_control(
			'title_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .social-link-inner .social-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'conditions' => $this->controls_contains_conditions( 'title' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register social-counter controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_style_layout() {
		$this->start_controls_section(
			'section_style_layout',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'order',
							'operator' => 'contains',
							'value' => 'icon',
						),
						array(
							'name' => 'order',
							'operator' => 'contains',
							'value' => 'numbers',
						),
						array(
							'name' => 'order',
							'operator' => 'contains',
							'value' => 'title',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'gap_column',
			array(
				'label' => __( 'Columns Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-social-counter .social-item' => 'padding-left: calc( {{SIZE}}{{UNIT}} / 2 ); padding-right: calc( {{SIZE}}{{UNIT}} / 2 );',
					'{{WRAPPER}} .cmsmasters-social-counter-inner' => 'margin-left: calc( -{{SIZE}}{{UNIT}} / 2 ); margin-right: calc( -{{SIZE}}{{UNIT}} / 2 );',
				),
				'condition' => array( 'columns!' => '1' ),
			)
		);

		$this->add_responsive_control(
			'gap_row',
			array(
				'label' => __( 'Rows Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-social-counter .social-item' => 'padding-top: calc( {{SIZE}}{{UNIT}} / 2 ); padding-bottom: calc( {{SIZE}}{{UNIT}} / 2 );',
					'{{WRAPPER}} .cmsmasters-social-counter-inner' => 'margin-top: calc( -{{SIZE}}{{UNIT}} / 2 ); margin-bottom: calc( -{{SIZE}}{{UNIT}} / 2 );',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 *
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_social_items_options() {
		static $options = array();

		if ( empty( $options ) ) {
			foreach ( self::get_social_classes() as $social_item_name => $social_item_class ) {
				$options[ $social_item_name ] = $social_item_class::get_label();
			}
		}

		return $options;
	}

	public static function get_social_classes() {
		return array(
			Items\Behance::get_name() => Items\Behance::get_class_full_name(),
			Items\Dribbble::get_name() => Items\Dribbble::get_class_full_name(),
			Items\Facebook::get_name() => Items\Facebook::get_class_full_name(),
			Items\Pinterest::get_name() => Items\Pinterest::get_class_full_name(),
			Items\Reddit::get_name() => Items\Reddit::get_class_full_name(),
			Items\Soundcloud::get_name() => Items\Soundcloud::get_class_full_name(),
			Items\Twitch::get_name() => Items\Twitch::get_class_full_name(),
			Items\Twitter::get_name() => Items\Twitter::get_class_full_name(),
			Items\Vimeo::get_name() => Items\Vimeo::get_class_full_name(),
			Items\Youtube::get_name() => Items\Youtube::get_class_full_name(),
		);
	}

	public static function get_social_labels() {
		return array(
			Items\Behance::get_name() => Items\Behance::get_label(),
			Items\Dribbble::get_name() => Items\Dribbble::get_label(),
			Items\Facebook::get_name() => Items\Facebook::get_label(),
			Items\Pinterest::get_name() => Items\Pinterest::get_label(),
			Items\Reddit::get_name() => Items\Reddit::get_label(),
			Items\Soundcloud::get_name() => Items\Soundcloud::get_label(),
			Items\Twitch::get_name() => Items\Twitch::get_label(),
			Items\Twitter::get_name() => Items\Twitter::get_label(),
			Items\Vimeo::get_name() => Items\Vimeo::get_label(),
			Items\Youtube::get_name() => Items\Youtube::get_label(),
		);
	}

	public function get_recommended_icons() {
		return array(
			'fa-brands' => array(
				Items\Behance::get_name(),
				Items\Dribbble::get_name(),
				Items\Facebook::get_name(),
				Items\Pinterest::get_name(),
				Items\Reddit::get_name(),
				Items\Soundcloud::get_name(),
				Items\Twitch::get_name(),
				Items\Twitter::get_name(),
				Items\Vimeo::get_name(),
				Items\Youtube::get_name(),
			),
		);
	}

	private function set_social_item( Items\Base $social_item ) {
		$this->social_item = $social_item;
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public function loop_socials( $callback, array $social_items = array() ) {
		if ( empty( $social_items ) ) {
			$social_items = $this->get_settings_for_display( 'social_items' );
		}

		$settings = $this->get_settings_for_display();

		foreach ( $social_items as $social_item ) {
			if ( ! is_string( $social_item ) ) {
				continue;
			}

			/**
			 * @var Items\Base
			 */
			$social_class = $this->get_social_item_class( $social_item );

			if ( ! $social_class && ! $social_class instanceof Items\Base ) {
				continue;
			}

			$icon = false;

			if ( 'custom' === $settings[ "icon_type_{$social_item}" ] ) {
				$icon = $settings[ "icon_{$social_item}" ];
			}

			if (
				! $icon ||
				(
					is_array( $icon ) &&
					! $icon['value']
				)
			) {
				$icon = $social_class::get_default_icon();
			}

			$social_obj = new $social_class(
				array(
					'icon' => $icon,
					'type' => $settings[ "type_{$social_item}" ],
					'url' => $settings[ "url_{$social_item}" ],
					'title' => $settings[ "title_{$social_item}" ],
					'title_type' => $settings[ "title_type_{$social_item}" ],
				)
			);

			$this->set_social_item( $social_obj );

			if ( ! $this->check_pre_show() ) {
				continue;
			}

			call_user_func( $callback );
		}
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param Items\Base|false
	 */
	public function get_social_item_class( $item_name ) {
		return CmsmastersUtils::get_if_isset( self::get_social_classes(), $item_name );
	}

	public function check_pre_show() {
		$social_item = $this->get_social_item();

		return (
			$social_item->check_pre_show() &&
			(
				! AjaxWidgetModule::is_active_ajax() ||
				$social_item->check_numbers( $this->get_numbers() )
			)
		);
	}

	public function get_social_item() {
		return $this->social_item;
	}

	public function get_numbers() {
		$social_item = $this->get_social_item();

		if ( AjaxWidgetModule::is_active_ajax() && ! $social_item->check_cache_expire() ) {
			return $social_item->get_numbers();
		}

		return $social_item->get_numbers_cache();
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public function get_data_for_ajax( $items ) {
		$data = array();

		$this->loop_socials( function () use ( &$data, $items ) {
			$social_item = $this->get_social_item();
			$cache_name = $social_item->get_cache_id();

			if ( in_array( $cache_name, $items, true ) && $this->check_pre_show() ) {
				$data[ $cache_name ] = $this->get_numbers_by_format();
			}
		} );

		return $data;
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public function get_numbers_by_format( $numbers = null, $format = '' ) {
		if ( ! $format ) {
			$format = $this->get_settings_for_display( 'numbers_format' );
		}

		if ( ! $numbers ) {
			$numbers = $this->get_numbers();
		}

		if ( ! $numbers && 0 !== $numbers ) {
			$numbers = 0;
		}

		switch ( $format ) {
			case 'full':
				return $numbers;
			case 'comma_separator':
				return number_format( $numbers );
			case 'short':
			default:
				return CmsmastersUtils::short_number( $numbers );
		}
	}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		$social_field = array();

		foreach ( self::get_social_labels() as $social_name => $label ) {
			$social_url = array(
				'field' => "url_{$social_name}",
				'type' => $label . esc_html__( ' URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			);

			$social_title = array(
				'field' => "title_{$social_name}",
				'type' => $label . esc_html__( ' Title', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			);

			array_push( $social_field, $social_url, $social_title );
		}

		return $social_field;
	}
}
