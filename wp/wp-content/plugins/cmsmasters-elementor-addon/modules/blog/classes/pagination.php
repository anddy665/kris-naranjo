<?php
namespace CmsmastersElementor\Modules\Blog\Classes;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersElementorControlsManager;
use CmsmastersElementor\Controls\Groups\Group_Control_Button_Background;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Addon pagination.
 *
 * Addon widget that displays pagination.
 *
 * @since 1.0.0
 */
class Pagination {

	/**
	 * Addon widget.
	 *
	 * @since 1.0.0
	 *
	 * @var Base_Widget Addon base widget class.
	 */
	protected $widget;

	/**
	 * The WordPress Query object.
	 *
	 * @var \WP_Query
	 */
	protected $wp_query;

	/**
	 * Prefix of query control.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $query_control_prefix;

	/**
	 * Conditions of controls.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $conditions;

	/**
	 * Pagination initial class constructor.
	 *
	 * @param Base_Widget $widget
	 * @param string $query_control_prefix
	 */
	public function __construct( Base_Widget $widget, $query_control_prefix ) {
		$this->widget = $widget;
		$this->query_control_prefix = $query_control_prefix;
	}

	/**
	 * Register pagination controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	public function register_controls_content() {
		$has_pagination_show_control = (bool) $this->widget->get_controls( 'pagination_show' );
		$load_more_states = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'loading' => __( 'Loading', 'cmsmasters-elementor' ),
		);

		$section_args = array(
			'label' => __( 'Pagination', 'cmsmasters-elementor' ),
			'tab' => Controls_Manager::TAB_CONTENT,
		);

		if ( $has_pagination_show_control ) {
			$section_args['condition'] = array(
				'pagination_show!' => '',
			);
		}

		$this->widget->start_controls_section( 'section_pagination', $section_args );

		$this->widget->add_control(
			'pagination_show',
			array(
				'label' => __( 'Pagination', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'default' => 'yes',
			)
		);

		$this->widget->add_control(
			'pagination_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'pagination',
				'prefix_class' => 'cmsmasters-pagination--',
				'options' => array(
					'pagination' => __( 'Pagination', 'cmsmasters-elementor' ),
					'infinite_scroll' => __( 'Infinite Scroll', 'cmsmasters-elementor' ),
					'load_more' => __( 'Load More', 'cmsmasters-elementor' ),
				),
				'render_type' => 'template',
				'frontend_available' => true,
				'condition' => array(
					'pagination_show!' => '',
				),
			)
		);

		$this->widget->add_control(
			'pagination_button_prefix_class',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 'with-button',
				'prefix_class' => 'cmsmasters-pagination--',
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => array(
						'infinite_scroll',
						'load_more',
					),
				),
			)
		);

		$this->widget->add_control(
			'pagination_view_type',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => CmsmastersElementorControlsManager::CHOOSE_TEXT,
				'default' => 'numbers_and_prev_next',
				'prefix_class' => 'cmsmasters-pagination-pagination-type--',
				'label_block' => false,
				'options' => array(
					'prev_next' => array(
						'title' => __( 'Prev/Next', 'cmsmasters-elementor' ),
						'description' => __( 'Show previous/next buttons', 'cmsmasters-elementor' ),
					),
					'numbers' => array(
						'title' => __( 'Numbers', 'cmsmasters-elementor' ),
						'description' => __( 'Show page numbers', 'cmsmasters-elementor' ),
					),
					'numbers_and_prev_next' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => __( 'Show both previous/next buttons and numbers', 'cmsmasters-elementor' ),
					),
				),
				'render_type' => 'template',
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'pagination',
				),
			)
		);

		$this->widget->add_control(
			'pagination_item_page_range',
			array(
				'label' => __( 'Page Range', 'cmsmasters-elementor' ),
				'description' => __( 'Choose how many pages should be shown on the start and the end of pagination and to either side of current pages.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'default' => 2,
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'pagination',
					'pagination_view_type' => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->widget->add_control(
			'pagination_via_ajax',
			array(
				'label' => __( 'Load via AJAX', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'separator' => 'before',
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'pagination',
				),
			)
		);

		$this->widget->add_control(
			'pagination_save_state',
			array(
				'label' => __( 'Save Page Number', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'Set Yes to save page number in URL.', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'frontend_available' => true,
				'render_type' => 'none',
				'condition' => array(
					'pagination_show!' => '',
					'pagination_via_ajax!' => '',
					'pagination_type' => 'pagination',
				),
			)
		);

		$this->widget->add_control(
			'pagination_scroll_into_view',
			array(
				'label' => __( 'Scroll Into View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'render_type' => 'none',
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'pagination',
					'pagination_via_ajax!' => '',
				),
			)
		);

		$this->widget->start_controls_tabs(
			'pagination_load_more_content',
			array(
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'load_more',
				),
			)
		);

		foreach ( $load_more_states as $state => $state_label ) {
			$is_normal = 'normal' === $state;

			if ( $is_normal ) {
				$icon_switcher_default = '';
				$placeholder = __( 'Load More', 'cmsmasters-elementor' );
			} else {
				$icon_switcher_default = 'yes';
				$placeholder = __( 'Loading', 'cmsmasters-elementor' ) . '...';
			}

			$loadmore_control_args_icon = array(
				'label' => __( 'Custom Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'load_more',
					"pagination_load_more_icon_switcher_{$state}!" => '',
				),
			);

			$this->widget->start_controls_tab(
				"pagination_load_more_content_{$state}",
				array( 'label' => $state_label )
			);

			$this->widget->add_control(
				"pagination_load_more_text_{$state}",
				array(
					'label' => __( 'Button Text', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => $placeholder,
					'default' => $placeholder,
					'frontend_available' => true,
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'load_more',
					),
				)
			);

			$this->widget->add_control(
				"pagination_load_more_icon_switcher_{$state}",
				array(
					'label' => __( 'Enable Icon', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => $icon_switcher_default,
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'load_more',
					),
				)
			);

			$this->widget->add_control( "pagination_load_more_icon_{$state}", $loadmore_control_args_icon );

			$this->widget->add_control(
				"pagination_load_more_icon_dir_{$state}",
				array(
					'label' => __( 'Icon Position', 'cmsmasters-elementor' ),
					'type' => CmsmastersElementorControlsManager::CHOOSE_TEXT,
					'label_block' => false,
					'default' => '',
					'options' => array(
						'' => __( 'Before', 'cmsmasters-elementor' ),
						'row-reverse' => __( 'After', 'cmsmasters-elementor' ),
					),
					'selectors' => array(
						"{{WRAPPER}} .next.page-numbers .cmsmasters-pagination-loadmore-state-{$state} .cmsmasters-pagination-page-numbers__inner" => 'flex-direction: {{VALUE}}',
					),
					'condition' => array(
						'pagination_type' => 'load_more',
						"pagination_load_more_icon_switcher_{$state}!" => '',
					),
				)
			);

			if ( ! $is_normal ) {
				$this->widget->add_control(
					'pagination_load_more_icon_spin',
					array(
						'label' => __( 'Spin Icon', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SWITCHER,
						'default' => 'yes',
						'prefix_class' => 'cmsmasters-pagination--icon-skin-',
						'condition' => array(
							'pagination_show!' => '',
							'pagination_type' => 'load_more',
							"pagination_load_more_icon_switcher_{$state}!" => '',
						),
					)
				);
			}

			$this->widget->end_controls_tab();
		}

		$this->widget->end_controls_tabs();

		$this->widget->add_control(
			'pagination_infinite_toggle_content',
			array(
				'label' => __( 'Preview Infinite Preloader', 'cmsmasters-elementor' ),
				'text' => __( 'Preview', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::BUTTON,
				'event' => 'cmsmasters:pagination:infinite_scroll:toggle',
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
				),
			)
		);

		$this->widget->add_control(
			'pagination_infinite_scroll_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Loading', 'cmsmasters-elementor' ) . '...',
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
				),
			)
		);

		$this->widget->add_control(
			'pagination_infinite_scroll_icon_switcher',
			array(
				'label' => __( 'Enable Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
				),
			)
		);

		$this->widget->add_control(
			'pagination_infinite_scroll_icon',
			array(
				'label' => __( 'Custom Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'recommended' => array(
					'fa-solid' => array(
						'circle-notch',
						'cog',
						'redo',
						'redo-alt',
						'spinner',
					),
				),
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
					'pagination_infinite_scroll_icon_switcher!' => '',
				),
			)
		);

		$this->widget->add_control(
			'pagination_prev_next_icon_switcher',
			array(
				'label' => __( 'Enable Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'pagination',
					'pagination_view_type' => array( 'numbers_and_prev_next', 'prev_next' ),
				),
			)
		);

		$this->widget->start_controls_tabs(
			'pagination_prev_next_tabs',
			array(
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'pagination',
					'pagination_view_type' => array( 'numbers_and_prev_next', 'prev_next' ),
				),
			)
		);

		$states = array(
			'prev' => __( 'Previous', 'cmsmasters-elementor' ),
			'next' => __( 'Next', 'cmsmasters-elementor' ),
		);

		foreach ( $states as $state => $state_label ) {
			$is_prev = 'prev' === $state;
			$selector = '{{WRAPPER}}';

			if ( $is_prev ) {
				$selector .= ' a.prev.page-numbers';
			} else {
				$selector .= ' a.next.page-numbers';
			}

			$control_args_icon = array(
				'label' => __( 'Custom Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'render_type' => 'template',
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'pagination',
					'pagination_prev_next_icon_switcher!' => '',
					'pagination_view_type' => array( 'numbers_and_prev_next', 'prev_next' ),
				),
			);

			$this->widget->start_controls_tab(
				"pagination_tab_{$state}",
				array(
					'label' => $state_label,
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'pagination',
						'pagination_view_type' => array( 'numbers_and_prev_next', 'prev_next' ),
					),
				)
			);

			$this->widget->add_control(
				"pagination_text_{$state}",
				array(
					'label' => __( 'Text', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'pagination',
						'pagination_view_type' => array( 'numbers_and_prev_next', 'prev_next' ),
					),
				)
			);

			if ( $is_prev ) {
				$control_args_icon['recommended'] = array(
					'fa-regular' => array(
						'arrow-alt-circle-left',
						'caret-square-left',
					),
					'fa-solid' => array(
						'angle-double-left',
						'angle-left',
						'arrow-alt-circle-left',
						'arrow-circle-left',
						'arrow-left',
						'caret-left',
						'caret-square-left',
						'chevron-circle-left',
						'chevron-left',
						'long-arrow-alt-left',
					),
				);
			} else {
				$control_args_icon['recommended'] = array(
					'fa-regular' => array(
						'arrow-alt-circle-right',
						'caret-square-right',
					),
					'fa-solid' => array(
						'angle-double-right',
						'angle-right',
						'arrow-alt-circle-right',
						'arrow-circle-right',
						'arrow-right',
						'caret-right',
						'caret-square-right',
						'chevron-circle-right',
						'chevron-right',
						'long-arrow-alt-right',
					),
				);
			}

			$this->widget->add_control( "pagination_icon_{$state}", $control_args_icon );

			$this->widget->end_controls_tab();
		}

		$this->widget->end_controls_tabs();

		$this->widget->end_controls_section();

		$this->query_control();
	}

	/**
	 * Register pagination controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	private function query_control() {
		$control_name_post_type = "{$this->query_control_prefix}_post_type";

		if ( $this->widget->get_controls( $control_name_post_type ) ) {
			$this->widget->update_control(
				$control_name_post_type,
				array(
					'frontend_available' => true,
				)
			);

			$this->widget->start_injection(
				array(
					'of' => $control_name_post_type,
				)
			);

			$this->widget->add_control(
				'query_control_prefix',
				array(
					'type' => Controls_Manager::HIDDEN,
					'frontend_available' => true,
					'default' => $this->query_control_prefix,
				)
			);

			$this->widget->end_injection();
		}
	}

	/**
	 * Register pagination controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	public function register_controls_style() {
		$this->register_controls_style_general();
		$this->register_controls_style_infinite_scroll();
		$this->register_controls_style_load_more();
		$this->register_controls_style_number();
		$this->register_controls_style_arrows();
	}

	/**
	 * Register pagination controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	public function register_controls_style_general() {
		$this->widget->start_controls_section(
			'section_pagination_style',
			array(
				'label' => __( 'Pagination', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pagination_show!' => '',
				),
			)
		);

		$this->widget->add_control(
			'pagination_align',
			array(
				'label' => __( 'Layout Type', 'cmsmasters-elementor' ),
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
					'space-between' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-stretch',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.page-numbers' => 'justify-content: {{VALUE}};',
				),
				'condition' => array(
					'pagination_show!' => '',
				),
			)
		);

		$this->widget->add_control(
			'pagination_fill',
			array(
				'label' => __( 'Fill', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'yes',
				'prefix_class' => 'cmsmasters-pagination-fullwidth--',
				'condition' => array(
					'pagination_align' => 'space-between',
				),
			)
		);

		$this->widget->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'pagination',
				'selector' => '{{WRAPPER}} ul.page-numbers',
				'exclude' => array(
					'text_decoration',
				),
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{WRAPPER}} ul.page-numbers' => '--cmsmasters-pagination-fm: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{WRAPPER}} ul.page-numbers' => '--cmsmasters-pagination-fz: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{WRAPPER}} ul.page-numbers' => '--cmsmasters-pagination-fw: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{WRAPPER}} ul.page-numbers' => '--cmsmasters-pagination-tt: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{WRAPPER}} ul.page-numbers' => '--cmsmasters-pagination-fs: {{VALUE}};',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{WRAPPER}} ul.page-numbers' => '--cmsmasters-pagination-lh: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{WRAPPER}} ul.page-numbers' => '--cmsmasters-pagination-lt: {{SIZE}}{{UNIT}};',
						),
					),
				),
				'condition' => array(
					'pagination_show!' => '',
				),
			)
		);

		$this->widget->add_control(
			'pagination_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.page-numbers' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->widget->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'pagination_wrap_border',
				'selector' => '{{WRAPPER}} ul.page-numbers',
				'condition' => array(
					'pagination_show!' => '',
				),
			)
		);

		$this->widget->add_responsive_control(
			'pagination_bdrs',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ul.page-numbers' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'pagination_show!' => '',
				),
			)
		);

		$this->widget->add_responsive_control(
			'pagination_container_space',
			array(
				'label' => __( 'Top Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'max' => 200,
						'min' => -100,
					),
					'%' => array(
						'max' => 100,
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.page-numbers' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type!' => 'infinite_scroll',
				),
			)
		);

		$this->widget->add_responsive_control(
			'pagination_container_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} ul.page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => array(
					'pagination_show!' => '',
				),
			)
		);

		$this->widget->end_controls_section();
	}

	/**
	 * Register pagination controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Fix conditions for icon switcher.
	 * @since 1.2.3 Fixed error with responsive controls in elementor 3.4.0.
	 */
	public function register_controls_style_arrows() {
		$condition = array(
			'pagination_show!' => '',
			'pagination_type' => 'pagination',
			'pagination_view_type' => array( 'numbers_and_prev_next', 'prev_next' ),
		);
		$conditions_icon = array(
			'terms' => array(
				array(
					'name' => 'pagination_prev_next_icon_switcher',
					'operator' => '!=',
					'value' => '',
				),
			),
		);

		$conditions_text = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'pagination_text_prev',
					'operator' => '!=',
					'value' => '',
				),
				array(
					'name' => 'pagination_text_next',
					'operator' => '!=',
					'value' => '',
				),
			),
		);

		$conditions_icon_with_text = array(
			'relation' => 'or',
			'terms' => array(
				$conditions_icon,
				$conditions_text,
			),
		);

		$this->widget->start_controls_section(
			'section_pagination_style_arrows',
			array(
				'label' => __( 'Pagination: Prev/Next', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
				'conditions' => $conditions_icon_with_text,
			)
		);

		$this->widget->add_responsive_control(
			'pagination_arrows_size',
			array(
				'label' => __( 'Min Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 100,
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .page-numbers.prev, {{WRAPPER}} .page-numbers.next' => 'min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => $condition,
			)
		);

		$this->widget->start_controls_tabs( 'pagination_arrows_tabs' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $name => $label ) {
			$selector_normal = '{{WRAPPER}} .page-numbers.prev, {{WRAPPER}} .page-numbers.next';
			$selector = $selector_normal;

			if ( 'hover' === $name ) {
				$selector = '{{WRAPPER}} .page-numbers.prev:hover, {{WRAPPER}} .page-numbers.next:hover';
			}

			$this->widget->start_controls_tab(
				"pagination_arrows_tab_{$name}",
				array(
					'label' => $label,
				)
			);

			$this->widget->add_control(
				"pagination_arrows_icon_color_{$name}",
				array(
					'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$selector} .cmsmasters-wrap-icon" => 'color: {{VALUE}};',
					),
					'conditions' => $conditions_icon,
				)
			);

			$this->widget->add_control(
				"pagination_arrows_text_color_{$name}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'color: {{VALUE}};',
					),
					'conditions' => $conditions_text,
				)
			);

			$this->widget->add_control(
				"pagination_arrows_bg_color_{$name}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->widget->add_control(
				"pagination_arrows_border_color_{$name}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'pagination_arrows_type_border!' => array( '', 'none' ),
					),
				)
			);

			$this->widget->add_control(
				"pagination_arrows_text_decoration_{$name}",
				array(
					'label' => __( 'Text Decoration', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => '',
					'options' => array(
						'' => __( 'Default', 'cmsmasters-elementor' ),
						'none' => __( 'Disable', 'cmsmasters-elementor' ),
						'underline' => __( 'Underline', 'cmsmasters-elementor' ),
						'overline' => __( 'Overline', 'cmsmasters-elementor' ),
						'line-through' => __( 'Line Through', 'cmsmasters-elementor' ),
					),
					'selectors' => array(
						$selector => 'text-decoration: {{VALUE}};',
					),
				)
			);

			$this->widget->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "pagination_arrows_shadow_{$name}",
					'selector' => $selector,
				)
			);

			$this->widget->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "pagination_arrows_text_shadow_{$name}",
					'fields_options' => array(
						'text_shadow' => array(
							'horizontal' => 0,
							'vertical' => 0,
							'blur' => 5,
							'color' => 'rgba(0,0,0,0.7)',
						),
					),
					'selector' => $selector,
				)
			);

			if ( 'hover' === $name ) {
				$this->widget->add_control(
					"pagination_arrows_animation_duration_{$name}",
					array(
						'label' => __( 'Animation Duration', 'cmsmasters-elementor' ) . ' (ms)',
						'type' => Controls_Manager::SLIDER,
						'range' => array(
							'px' => array(
								'min' => 0,
								'max' => 3000,
							),
						),
						'selectors' => array(
							$selector_normal => 'transition-duration: {{SIZE}}ms',
						),
					)
				);
			}

			$this->widget->end_controls_tab();
		}

		$this->widget->end_controls_tabs();

		$this->widget->add_control(
			'pagination_arrows_type_border',
			array(
				'label' => __( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'none' => __( 'Disable', 'cmsmasters-elementor' ),
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
					'groove' => __( 'Groove', 'cmsmasters-elementor' ),
				),
				'frontend_available' => true,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .page-numbers.prev,' .
					'{{WRAPPER}} .page-numbers.next' => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->widget->add_control(
			'pagination_arrows_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'range' => array(
					'px' => array(
						'min' => 0,
					),
					'em' => array(
						'min' => 0,
					),
				),
				'size_units' => array( 'em', 'px' ),
				'default' => array(
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .page-numbers.prev,' .
					'{{WRAPPER}} .page-numbers.next' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => array(
					'pagination_arrows_type_border!' => array( '', 'none' ),
				),
			)
		);

		$this->widget->add_control(
			'pagination_arrows_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .page-numbers.prev,' .
					'{{WRAPPER}} .page-numbers.next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->widget->add_responsive_control(
			'pagination_space_arrows',
			array(
				'label' => __( 'Side Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 100,
						'min' => -1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.page-numbers' => '--cmsmasters-arrows-space: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'pagination_align!' => 'space-between',
				),
			)
		);

		$this->widget->add_responsive_control(
			'pagination_arrows_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 100,
						'min' => 1,
					),
					'em' => array(
						'max' => 5,
						'min' => 0.1,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .page-numbers.prev .cmsmasters-wrap-icon,' .
					'{{WRAPPER}} .page-numbers.next .cmsmasters-wrap-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'size_units' => array( 'px', 'em' ),
				'conditions' => $conditions_icon,
			)
		);

		$this->widget->add_responsive_control(
			'pagination_icon_spacing',
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 50,
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.page-numbers' => '--cmsmasters-icon-spacing: {{SIZE}}{{UNIT}}',
				),
				'conditions' => $conditions_icon_with_text,
			)
		);

		$this->widget->add_control(
			'previous_next_hide_tablet_mobile',
			array(
				'label' => __( 'Hide Text On Tablet/Mobile', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'selectors' => array(
					'(tablet){{WRAPPER}} .page-numbers.prev .cmsmasters-text, {{WRAPPER}} .page-numbers.next .cmsmasters-text' => 'display: none;',
					'(tablet){{WRAPPER}} .page-numbers.prev .cmsmasters-wrap-icon, {{WRAPPER}} .page-numbers.next .cmsmasters-wrap-icon' => 'margin: 0;',
				),
				'conditions' => $conditions_icon_with_text,
			)
		);

		$this->widget->add_responsive_control(
			'pagination_arrows_item_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .page-numbers.prev,' .
					'{{WRAPPER}} .page-numbers.next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->widget->end_controls_section();
	}

	/**
	 * Register pagination controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Fix conditions for icon switcher.
	 */
	public function register_controls_style_infinite_scroll() {
		$selector = '{{WRAPPER}} .page-numbers.next .cmsmasters-theme-button';
		$selector_bg = "{$selector}::before";

		$this->widget->start_controls_section(
			'section_pagination_style_infinite_scroll',
			array(
				'label' => __( 'Pagination: Infinite Scroll', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
				),
			)
		);

		$this->widget->add_control(
			'pagination_infinite_toggle_style',
			array(
				'label' => __( 'Preview Infinite Preloader', 'cmsmasters-elementor' ),
				'text' => __( 'Preview', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::BUTTON,
				'event' => 'cmsmasters:pagination:infinite_scroll:toggle',
				'separator' => 'after',
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
				),
			)
		);

		$this->widget->add_group_control(
			Group_Control_Button_Background::get_type(),
			array(
				'name' => 'pagination_infinite_scroll_bg',
				'selector' => $selector_bg,
				'exclude' => array( 'color' ),
			)
		);

		$this->widget->start_injection( array( 'of' => 'pagination_infinite_scroll_bg_background' ) );

		$this->widget->add_control(
			'pagination_infinite_scroll_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$selector_bg => '--button-bg-color: {{VALUE}}; background-color: var( --button-bg-color );',
				),
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
				),
			)
		);

		$this->widget->end_injection();

		$this->widget->add_control(
			'pagination_infinite_scroll_icon_color',
			array(
				'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$selector} .cmsmasters-wrap-icon" => 'color: {{VALUE}};',
				),
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
				),
				'condition' => array(
					'pagination_infinite_scroll_icon_switcher!' => '',
				),
			)
		);

		$this->widget->add_control(
			'pagination_infinite_scroll_text_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => '--cmsmasters-button-normal-colors-color: {{VALUE}};',
				),
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
				),
			)
		);

		$this->widget->add_control(
			'pagination_infinite_scroll_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => '--cmsmasters-button-normal-colors-bd: {{VALUE}};',
				),
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
					'pagination_infinite_scroll_type_border!' => array( '', 'none' ),
				),
			)
		);

		$this->widget->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'pagination_infinite_scroll_shadow',
				'selector' => $selector,
				'fields_options' => array(
					'box_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-normal-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
						),
					),
				),
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
				),
			)
		);

		$this->widget->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'pagination_infinite_scroll_text_shadow',
				'selector' => $selector,
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
				),
			)
		);

		$this->widget->add_control(
			'pagination_infinite_scroll_text_decoration',
			array(
				'label' => __( 'Text Decoration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'none' => __( 'Disable', 'cmsmasters-elementor' ),
					'underline' => __( 'Underline', 'cmsmasters-elementor' ),
					'overline' => __( 'Overline', 'cmsmasters-elementor' ),
					'line-through' => __( 'Line Through', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					$selector => '--cmsmasters-button-text-decoration: {{VALUE}};',
				),
			)
		);

		$this->widget->add_control(
			'pagination_infinite_scroll_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					$selector => '--cmsmasters-button-normal-bd-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
				),
			)
		);

		$this->widget->add_control(
			'pagination_infinite_scroll_type_border',
			array(
				'label' => __( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'none' => __( 'Disable', 'cmsmasters-elementor' ),
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
					'groove' => __( 'Groove', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					$selector => '--cmsmasters-button-normal-bd-style: {{VALUE}};',
				),
				'separator' => 'before',
				'frontend_available' => true,
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
				),
			)
		);

		$this->widget->add_control(
			'pagination_infinite_scroll_border_width',
			array(
				'label' => __( 'Border Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'range' => array(
					'px' => array(
						'min' => 0,
					),
					'em' => array(
						'min' => 0,
					),
				),
				'size_units' => array( 'em', 'px' ),
				'default' => array(
					'unit' => 'px',
				),
				'selectors' => array(
					$selector => '--cmsmasters-button-normal-bd-width-top: {{TOP}}{{UNIT}}; --cmsmasters-button-normal-bd-width-right: {{RIGHT}}{{UNIT}}; --cmsmasters-button-normal-bd-width-bottom: {{BOTTOM}}{{UNIT}}; --cmsmasters-button-normal-bd-width-left: {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
					'pagination_infinite_scroll_type_border!' => array( '', 'none' ),
				),
			)
		);

		$this->widget->add_control(
			'pagination_infinite_scroll_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					$selector => '--cmsmasters-button-padding-top: {{TOP}}{{UNIT}}; --cmsmasters-button-padding-right: {{RIGHT}}{{UNIT}}; --cmsmasters-button-padding-bottom: {{BOTTOM}}{{UNIT}}; --cmsmasters-button-padding-left: {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'infinite_scroll',
				),
			)
		);

		$this->widget->add_responsive_control(
			'pagination_infinite_scroll_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 100,
						'min' => 1,
					),
					'em' => array(
						'max' => 5,
						'min' => 0.1,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					"{$selector} .cmsmasters-wrap-icon" => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'size_units' => array( 'px', 'em' ),
				'condition' => array(
					'pagination_infinite_scroll_icon_switcher!' => '',
				),
			)
		);

		$this->widget->add_responsive_control(
			'pagination_infinite_scroll_icon_spacing',
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 50,
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.page-numbers' => '--cmsmasters-icon-spacing: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'pagination_infinite_scroll_icon_switcher!' => '',
				),
			)
		);

		$this->widget->end_controls_section();
	}

	/**
	 * Register pagination controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Fixed `Background Color` control for load more button in hover and loading state.
	 * Added `Typography` control for load more button. Removed `Text Decoration` control
	 * for load more button on normal state. Fixed `Text Color`, `Border Color`, `Border Radius`
	 * and `Text Decoration` for load more button.
	 */
	public function register_controls_style_load_more() {
		$selector = '{{WRAPPER}} .cmsmasters-pagination-loadmore-state-normal .cmsmasters-theme-button';
		$states = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'loading' => __( 'Loading', 'cmsmasters-elementor' ),
		);

		$conditions_icon = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'pagination_load_more_icon_normal[value]',
					'operator' => '!=',
					'value' => '',
				),
				array(
					'name' => 'pagination_load_more_icon_loading[value]',
					'operator' => '!=',
					'value' => '',
				),
			),
		);

		$this->widget->start_controls_section(
			'section_pagination_style_load_more',
			array(
				'label' => __( 'Pagination: Load More', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'load_more',
				),
			)
		);

		$this->widget->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'pagination_load_more_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-theme-button',
			)
		);

		$this->widget->start_controls_tabs( 'pagination_tabs_load_more' );

		foreach ( $states as $state => $state_label ) {
			$selector_state = $selector;
			$bdrs_prefix_control = '';
			$css_var_old_state_prefix = '';

			if ( 'hover' === $state ) {
				$css_var_old_state_prefix = '-hover';
			}

			if ( 'loading' === $state ) {
				$css_var_state_prefix = 'normal';
			} else {
				$css_var_state_prefix = "{$state}";
			}

			if ( 'normal' !== $state ) {
				$bdrs_prefix_control = "_{$state}";
			}

			$this->widget->start_controls_tab(
				"pagination_tab_load_more_{$state}",
				array(
					'label' => $state_label,
				)
			);

			$selector_state_bg = $selector_state . '::before, ' . $selector_state . '::after';

			switch ( $state ) {
				case 'hover':
					$selector_state_bg = $selector_state . '::after';

					break;
				case 'loading':
					$selector_state = '{{WRAPPER}} .cmsmasters-pagination-loadmore-state-loading .cmsmasters-theme-button';

					$selector_state_bg = $selector_state . '::before';

					break;
			}

			$this->widget->add_group_control(
				Group_Control_Button_Background::get_type(),
				array(
					'name' => "pagination_load_more_{$state}_bg",
					'selector' => $selector_state_bg,
					'exclude' => array( 'color' ),
				)
			);

			$this->widget->start_injection( array( 'of' => "pagination_load_more_{$state}_bg_background" ) );

			$this->widget->add_control(
				"pagination_load_more_bg_color_{$state}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_state_bg => '--button-bg-color: {{VALUE}};' .
						'background-color: var( --button-bg-color );',
					),
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'load_more',
					),
				)
			);

			$this->widget->end_injection();

			switch ( $state ) {
				case 'hover':
					$selector_state .= ':hover';

					break;
			}

			$this->widget->add_control(
				"pagination_load_more_icon_color_{$state}",
				array(
					'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$selector_state} .cmsmasters-wrap-icon" => 'color: {{VALUE}};',
					),
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'load_more',
					),
					'conditions' => $conditions_icon,
				)
			);

			$this->widget->add_control(
				"pagination_load_more_text_color_{$state}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_state => "--cmsmasters-button-{$css_var_state_prefix}-colors-color: {{VALUE}};" .
						"color: var( --cmsmasters-button-{$css_var_state_prefix}-colors-color );",
					),
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'load_more',
					),
				)
			);

			$this->widget->add_control(
				"pagination_load_more_border_color_{$state}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_state => "--cmsmasters-button-{$css_var_state_prefix}-colors-bd: {{VALUE}};" .
						"border-color: var( --cmsmasters-button-{$css_var_state_prefix}-colors-color );",
					),
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'load_more',
						'pagination_load_more_type_border!' => array( 'none' ),
					),
				)
			);

			$this->widget->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "pagination_load_more_shadow_{$state}",
					'selector' => $selector_state,
					'fields_options' => array(
						'box_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => "--cmsmasters-button-{$css_var_state_prefix}-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};",
							),
						),
					),
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'load_more',
					),
				)
			);

			$this->widget->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "pagination_load_more_text_shadow_{$state}",
					'selector' => $selector_state,
					'fields_options' => array(
						'text_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--cmsmasters-button-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
							),
						),
					),
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'load_more',
					),
				)
			);

			if ( 'normal' !== $state ) {
				$this->widget->add_control(
					"pagination_load_more_text_decoration_{$state}",
					array(
						'label' => __( 'Text Decoration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SELECT,
						'default' => '',
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => __( 'Disable', 'cmsmasters-elementor' ),
							'underline' => __( 'Underline', 'cmsmasters-elementor' ),
							'overline' => __( 'Overline', 'cmsmasters-elementor' ),
							'line-through' => __( 'Line Through', 'cmsmasters-elementor' ),
						),
						'selectors' => array(
							$selector_state => "--cmsmasters-button-{$css_var_old_state_prefix}-text-decoration: {{VALUE}};" .
							"text-decoration: var( --cmsmasters-button-{$css_var_old_state_prefix}-text-decoration );",
						),
					)
				);
			}

			$this->widget->add_control(
				"pagination_load_more_border_radius{$bdrs_prefix_control}",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'range' => array(
						'%' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'selectors' => array(
						$selector_state => "--cmsmasters-button-{$css_var_state_prefix}-bd-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};" .
						"border-radius: var( --cmsmasters-button-{$css_var_state_prefix}-bd-radius );",
					),
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'load_more',
					),
				)
			);

			$this->widget->end_controls_tab();
		}

		$this->widget->end_controls_tabs();

		$this->widget->add_control(
			'pagination_load_more_type_border',
			array(
				'label' => __( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'none' => __( 'Disable', 'cmsmasters-elementor' ),
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
					'groove' => __( 'Groove', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					$selector => '--cmsmasters-button-normal-bd-style: {{VALUE}};',
				),
				'separator' => 'before',
				'frontend_available' => true,
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'load_more',
				),
			)
		);

		$this->widget->add_control(
			'pagination_load_more_border_width',
			array(
				'label' => __( 'Border Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'range' => array(
					'px' => array(
						'min' => 0,
					),
					'em' => array(
						'min' => 0,
					),
				),
				'size_units' => array( 'em', 'px' ),
				'default' => array(
					'unit' => 'px',
				),
				'selectors' => array(
					$selector => '--cmsmasters-button-normal-bd-width-top: {{TOP}}{{UNIT}}; --cmsmasters-button-normal-bd-width-right: {{RIGHT}}{{UNIT}}; --cmsmasters-button-normal-bd-width-bottom: {{BOTTOM}}{{UNIT}}; --cmsmasters-button-normal-bd-width-left: {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'load_more',
					'pagination_load_more_type_border!' => array( '', 'none' ),
				),
			)
		);

		$this->widget->add_responsive_control(
			'pagination_load_more_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					$selector => '--cmsmasters-button-padding-top: {{TOP}}{{UNIT}}; --cmsmasters-button-padding-right: {{RIGHT}}{{UNIT}}; --cmsmasters-button-padding-bottom: {{BOTTOM}}{{UNIT}}; --cmsmasters-button-padding-left: {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'load_more',
				),
			)
		);

		$this->widget->add_responsive_control(
			'pagination_load_more_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 100,
						'min' => 1,
					),
					'em' => array(
						'max' => 5,
						'min' => 0.1,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					"{$selector} .cmsmasters-wrap-icon" => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'size_units' => array( 'px', 'em' ),
				'conditions' => $conditions_icon,
			)
		);

		$this->widget->add_responsive_control(
			'pagination_load_more_icon_spacing',
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 50,
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.page-numbers' => '--cmsmasters-icon-spacing: {{SIZE}}{{UNIT}}',
				),
				'conditions' => $conditions_icon,
			)
		);

		$this->widget->end_controls_section();
	}

	/**
	 * Register pagination controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	public function register_controls_style_number() {
		$condition = array(
			'pagination_show!' => '',
			'pagination_type' => 'pagination',
			'pagination_view_type' => array( 'numbers_and_prev_next', 'numbers' ),
		);

		$this->widget->start_controls_section(
			'section_pagination_style_numbers',
			array(
				'label' => __( 'Pagination: Numbers', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
			)
		);

		$this->widget->add_responsive_control(
			'pagination_item_size',
			array(
				'label' => __( 'Min Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 100,
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .page-numbers:not(ul):not(.prev):not(.next)' => 'min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => $condition,
			)
		);

		$this->widget->start_controls_tabs( 'pagination_tabs' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'active' => __( 'Active', 'cmsmasters-elementor' ),
		) as $name => $label ) {
			$selector_normal = '{{WRAPPER}} .page-numbers:not(ul):not(.prev):not(.next)';
			$selector = $selector_normal;

			switch ( $name ) {
				case 'hover':
					$selector = '{{WRAPPER}} .page-numbers:not(ul):not(.prev):not(.next):not(.dots):hover';

					break;
				case 'active':
					$selector = '{{WRAPPER}} .page-numbers:not(ul):not(.prev):not(.next).current';

					break;
			}

			$this->widget->start_controls_tab(
				"pagination_tab_{$name}",
				array(
					'label' => $label,
				)
			);

			$this->widget->add_control(
				"pagination_text_color_{$name}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'color: {{VALUE}};',
					),
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'pagination',
						'pagination_view_type' => array( 'numbers_and_prev_next', 'numbers' ),
					),
				)
			);

			$this->widget->add_control(
				"pagination_bg_color_{$name}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'pagination',
						'pagination_view_type' => array( 'numbers_and_prev_next', 'numbers' ),
					),
				)
			);

			$this->widget->add_control(
				"pagination_border_color_{$name}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'pagination',
						'pagination_item_type_border!' => array( '', 'none' ),
						'pagination_view_type' => array( 'numbers_and_prev_next', 'numbers' ),
					),
				)
			);

			$this->widget->add_control(
				"pagination_numbers_text_decoration_{$name}",
				array(
					'label' => __( 'Text Decoration', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => '',
					'options' => array(
						'' => __( 'Default', 'cmsmasters-elementor' ),
						'none' => __( 'None', 'cmsmasters-elementor' ),
						'underline' => __( 'Underline', 'cmsmasters-elementor' ),
						'overline' => __( 'Overline', 'cmsmasters-elementor' ),
						'line-through' => __( 'Line Through', 'cmsmasters-elementor' ),
					),
					'selectors' => array(
						$selector => 'text-decoration: {{VALUE}};',
					),
				)
			);

			$this->widget->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "pagination_shadow_{$name}",
					'selector' => $selector,
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'pagination',
						'pagination_view_type' => array( 'numbers_and_prev_next', 'numbers' ),
					),
				)
			);

			$this->widget->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "pagination_text_shadow_{$name}",
					'selector' => $selector,
					'fields_options' => array(
						'text_shadow' => array(
							'horizontal' => 0,
							'vertical' => 0,
							'blur' => 5,
							'color' => 'rgba(0,0,0,0.7)',
						),
					),
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'pagination',
						'pagination_view_type' => array( 'numbers_and_prev_next', 'numbers' ),
					),
				)
			);

			if ( 'hover' === $name ) {
				$this->widget->add_control(
					"pagination_animation_duration_{$name}",
					array(
						'label' => __( 'Animation Duration', 'cmsmasters-elementor' ) . ' (ms)',
						'type' => Controls_Manager::SLIDER,
						'range' => array(
							'px' => array(
								'min' => 0,
								'max' => 3000,
							),
						),
						'selectors' => array(
							$selector_normal => 'transition-duration: {{SIZE}}ms',
						),
					)
				);
			}

			$this->widget->end_controls_tab();
		}

		$this->widget->end_controls_tabs();

		$this->widget->add_responsive_control(
			'pagination_number_space',
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 100,
						'min' => -1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.page-numbers' => '--cmsmasters-number-space: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'pagination',
					'pagination_align!' => 'space-between',
					'pagination_view_type' => array( 'numbers_and_prev_next', 'numbers' ),
				),
			)
		);

		$this->widget->add_control(
			'pagination_item_type_border',
			array(
				'label' => __( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'none' => __( 'Disable', 'cmsmasters-elementor' ),
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
					'groove' => __( 'Groove', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .page-numbers:not(ul):not(.prev):not(.next)' => 'border-style: {{VALUE}};',
				),
				'frontend_available' => true,
				'separator' => 'before',
				'condition' => $condition,
			)
		);

		$this->widget->add_control(
			'pagination_border_width',
			array(
				'label' => __( 'Border Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'range' => array(
					'px' => array(
						'min' => 0,
					),
					'em' => array(
						'min' => 0,
					),
				),
				'size_units' => array( 'em', 'px' ),
				'default' => array(
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .page-numbers:not(ul):not(.prev):not(.next)' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => 'pagination',
					'pagination_item_type_border!' => array( '', 'none' ),
					'pagination_view_type' => array( 'numbers_and_prev_next', 'numbers' ),
				),
			)
		);

		$this->widget->add_control(
			'pagination_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .page-numbers:not(ul):not(.prev):not(.next)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => $condition,
			)
		);

		$this->widget->add_control(
			'pagination_item_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .page-numbers:not(ul):not(.prev):not(.next)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => $condition,
			)
		);

		$this->widget->end_controls_section();
	}

	/**
	 * Check if ready to display.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_render() {
		$settings = $this->widget->get_settings_for_display();

		if (
			! $settings['pagination_show'] ||
			1 >= $this->wp_query->max_num_pages ||
			(
				(
					$this->is_load_more() ||
					$this->is_infinite_scroll()
				) &&
				max( $this->wp_query->query_vars['paged'], 1 ) >= $this->wp_query->max_num_pages
			)
		) {
			return false;
		}

		return true;
	}

	/**
	 * Render paginated links for blog pages.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( ! $this->is_render() ) {
			return;
		}

		$paginate_links = paginate_links( $this->get_pagination_args() );

		$search = array(
			'<a class="next page-numbers"',
			'<a class="prev page-numbers"',
		);

		$replace = array(
			'<a aria-label="Next Page" class="next page-numbers" tabindex="0"',
			'<a aria-label="Previous Page" class="prev page-numbers" tabindex="0"',
		);

		echo str_replace( $search, $replace, $paginate_links );
	}

	/**
	 * Check if loadmore ready to display.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_load_more() {
		return 'load_more' === $this->widget->get_settings_for_display( 'pagination_type' );
	}

	/**
	 * Check if infinite scroll ready to display.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_infinite_scroll() {
		return 'infinite_scroll' === $this->widget->get_settings_for_display( 'pagination_type' );
	}

	/**
	 * Check if current query is archive.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_current_query() {
		return 'current_query' === $this->widget->get_settings_for_display( "{$this->query_control_prefix}_post_type" );
	}

	/**
	 * Get array arguments for generating paginated links for archives.
	 *
	 * @since 1.0.0
	 * @since 1.11.6 Fixed ajax pagination state.
	 *
	 * @return array
	 */
	public function get_pagination_args() {
		global $wp_rewrite;

		$load_more = $this->is_load_more();
		$infinite_scroll = $this->is_infinite_scroll();
		$is_arrows = $this->is_arrows();
		$space_number = $this->widget->get_settings_for_display( 'pagination_item_page_range' );
		$only_arrow = in_array(
			$this->widget->get_settings_for_display( 'pagination_view_type' ),
			array(
				'prev_next',
			),
			true
		);

		$args = array(
			'current' => $this->get_paged(),
			'prev_next' => $is_arrows,
			'total' => (int) $this->wp_query->max_num_pages,
			'type' => 'list',
			'end_size' => $space_number,
			'mid_size' => $space_number,
		);

		if ( $this->is_current_query() ) {
			// Setting up default values based on the current URL.

			if ( Utils::is_ajax() ) {
				$request_uri_old = $_SERVER['REQUEST_URI'];

				if ( Utils::is_edit_mode() ) {
					$post_id = Utils::get_document_id();
					$page_url = Plugin::elementor()->documents->get( $post_id )->get_wp_preview_url();
				} else {
					$page_url = wp_get_referer();
				}

				$parsed_url = wp_parse_url( $page_url );
				$request_uri = $parsed_url['path'];

				if ( isset( $parsed_url['query'] ) ) {
					$request_uri .= "?{$parsed_url['query']}";
				}

				$_SERVER['REQUEST_URI'] = $request_uri;
			} else {
				$args['total'] = (int) $GLOBALS['wp_query']->max_num_pages;
			}

			$pagenum_link = html_entity_decode( get_pagenum_link() );

			if ( isset( $request_uri_old ) ) {
				$_SERVER['REQUEST_URI'] = $request_uri_old;
			}

			$url_parts = explode( '?', $pagenum_link );

			// Append the format placeholder to the base URL.
			$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

			// URL base depends on permalink settings.
			$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
			$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

			$pagenum_link = preg_replace( '/\/page\/(\d)*/', '', $pagenum_link );

			$args['base'] = $pagenum_link;
			$args['format'] = $format;
		} else {
			$paged_name = $this->get_paged_name();

			$args['base'] = esc_url_raw(
				add_query_arg(
					$paged_name,
					'%#%',
					( Utils::is_ajax() ? wp_get_referer() : false )
				)
			);
			$args['format'] = "?{$paged_name}=%#%";
		}

		if ( $is_arrows ) {
			$args['next_text'] = $this->get_html_next();
			$args['prev_text'] = $this->get_html_prev();
		}

		if (
			$only_arrow ||
			$load_more ||
			$infinite_scroll
		) {
			$args['end_size'] = 0;
			$args['mid_size'] = 0;
		}

		if ( $load_more || $infinite_scroll ) {
			$args['prev_next'] = true;

			if ( $load_more ) {
				$args['next_text'] = $this->get_html_load_more();
			} elseif ( $infinite_scroll ) {
				$args['next_text'] = $this->get_html_infinite_scroll();
			}
		}

		return apply_filters( 'cmsmasters_elementor/pagination/args', $args );
	}

	/**
	 * Get query name of current widget.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_paged_name() {
		return "cmsmasters-page-{$this->widget->get_id()}";
	}

	/**
	 * The current page number. Default is 'paged' query var.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_paged() {
		$paged_name = $this->get_paged_name();
		$page = 1;

		if ( $this->is_current_query() && get_query_var( 'paged' ) ) {
			$page = (int) get_query_var( 'paged' );
		} elseif ( isset( $_GET[ $paged_name ] ) ) {
			$page = $_GET[ $paged_name ];
		} elseif ( $this->wp_query instanceof \WP_Query ) {
			$page = $this->wp_query->get( 'paged' );
		}

		return max( 1, $page );
	}

	/**
	 * Check for render prev/next
	 *
	 * @since 1.0.3
	 */
	public function has_prev_next_text() {
		$settings = $this->widget->get_settings_for_display();

		return $settings['pagination_text_prev'] || $settings['pagination_text_next'];
	}

	/**
	 * Check if ready to display.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Fix for icon switcher.
	 *
	 * @return bool
	 */
	public function is_arrows() {
		$settings = $this->widget->get_settings_for_display();

		return (
			( $settings['pagination_prev_next_icon_switcher'] || $this->has_prev_next_text() ) &&
			in_array(
				$settings['pagination_view_type'],
				array(
					'numbers_and_prev_next',
					'prev_next',
				),
				true
			)
		);
	}

	/**
	 * The previous page html.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_html_prev() {
		return $this->get_html_arrows( 'prev' );
	}

	/**
	 * The next page html.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_html_next() {
		return $this->get_html_arrows( 'next' );
	}

	/**
	 * The prev/next page html.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 * @since 1.0.3 Fix for icon switcher.
	 * @since 1.17.3 Fixed arrow icon display.
	 *
	 * @return string
	 */
	protected function get_html_arrows( $settings_name ) {
		$settings = $this->widget->get_settings_for_display();

		if ( ! $settings['pagination_prev_next_icon_switcher'] && ! $this->has_prev_next_text() ) {
			return;
		}

		$icon_setting = $settings[ "pagination_icon_{$settings_name}" ];

		if ( empty( $icon_setting['value'] ) && $settings['pagination_prev_next_icon_switcher'] ) {
			if ( 'prev' === $settings_name ) {
				$icon_class_default = 'fa-long-arrow-alt-left';
			} elseif ( 'next' === $settings_name ) {
				$icon_class_default = 'fa-long-arrow-alt-right';
			}

			$icon_setting = array(
				'value' => "fas {$icon_class_default}",
				'library' => 'fa-solid',
			);
		}

		$text = $settings[ "pagination_text_{$settings_name}" ];

		$html = '';

		if ( $text ) {
			$html .= '<span class="cmsmasters-text">' . esc_html( $text ) . '</span>';
		}

		$arrows_icon_att = array( 'aria-hidden' => 'true' );
		$arrows_aria_label = ( 'prev' === $settings_name ? 'Prev Arrow' : 'Next Arrow' );

		if ( ! $text ) {
			$arrows_icon_att = array_merge(
				$arrows_icon_att,
				array( 'aria-label' => $arrows_aria_label ),
			);
		}

		$html .= Utils::get_render_icon( $icon_setting, $arrows_icon_att );

		return $html;
	}

	/**
	 * Get pagination button.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Fix for icon switcher.
	 *
	 * @return string
	 */
	protected function get_html_button( $icon_setting, $text_setting ) {
		ob_start();

		echo '<div class="cmsmasters-theme-button">' .
		'<div class="cmsmasters-pagination-page-numbers__inner">';

		Utils::render_icon( $icon_setting, array( 'aria-hidden' => 'true' ) );

		if ( $text_setting ) {
			echo '<span class="cmsmasters-text">' .
				esc_html( $text_setting ) .
			'</span>';
		}

		echo '</div>' .
		'</div>';

		return ob_get_clean();
	}

	/**
	 * Get infinite scroll.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Fix for icon switcher.
	 *
	 * @return string
	 */
	protected function get_html_infinite_scroll() {
		$icon_setting = $this->widget->get_settings_for_display( 'pagination_infinite_scroll_icon' );
		$icon_switcher_value = $this->widget->get_settings_for_display( 'pagination_infinite_scroll_icon_switcher' );
		$text_setting = $this->widget->get_settings_fallback( 'pagination_infinite_scroll_text' );

		if ( empty( $icon_setting['value'] ) && $icon_switcher_value ) {
			$icon_setting = array(
				'value' => 'fas fa-spinner',
				'library' => 'fa-solid',
			);
		}

		return $this->get_html_button( $icon_setting, $text_setting );
	}

	/**
	 * Get load more.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Fix for icon switcher.
	 * @since 1.14.4 Fixed empty `Load More` button text.
	 */
	protected function get_html_load_more() {
		$states = array( 'normal', 'loading' );
		$loadmore_html = '';

		foreach ( $states as $state ) {
			$icon_setting = $this->widget->get_settings_for_display( "pagination_load_more_icon_{$state}" );
			$text_setting = $this->widget->get_settings_for_display( "pagination_load_more_text_{$state}" );
			$text_setting = ( isset( $text_setting ) && ! empty( $text_setting ) ? $text_setting : '' );
			$icon_switcher_value = $this->widget->get_settings_for_display( "pagination_load_more_icon_switcher_{$state}" );

			if ( 'loading' === $state && empty( $icon_setting['value'] ) && $icon_switcher_value ) {
				$icon_setting = array(
					'value' => 'fas fa-spinner',
					'library' => 'fa-solid',
				);
			}

			$loadmore_html .= '<div class="' . esc_attr( "cmsmasters-pagination-loadmore-state-{$state}" ) . '">' .
				$this->get_html_button( $icon_setting, $text_setting ) .
			'</div>';
		}

		return $loadmore_html;
	}

	/**
	 * Set the WordPress query object.
	 *
	 * @param \WP_Query $wp_query
	 */
	public function set_wp_query( \WP_Query $wp_query ) {
		$this->wp_query = $wp_query;
	}

}
