<?php
namespace CmsmastersElementor\Modules\TribeEvents\Widgets;

use CmsmastersElementor\Modules\Blog\Classes\Border_Columns;
use CmsmastersElementor\Modules\TribeEvents\Module as TribeEventsModule;
use CmsmastersElementor\Modules\TribeEvents\Traits\Tribe_Events_Widget;
use CmsmastersElementor\Modules\TribeEvents\Widgets\Base_Events\Base_Events_Customizable;

use Elementor\Controls_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon events grid widget.
 *
 * Addon widget that displays events grid.
 *
 * @since 1.13.0
 */
class Events_Grid extends Base_Events_Customizable {

	use Tribe_Events_Widget;

	const DEFAULT_COLUMNS_AND_ROWS = 4;

	/**
	 * Border Columns instance.
	 *
	 * @since 1.13.0
	 *
	 * @var Border_Columns
	 */
	private $border_columns;

	/**
	 * @since 1.13.0
	 */
	public function get_title() {
		return __( 'Events Grid', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.13.0
	 */
	public function get_icon() {
		return 'cmsicon-events-grid';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.13.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array_unique(
			array_merge(
				parent::get_unique_keywords(),
				array(
					'events',
					'grig',
					'masonry',
				)
			)
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
	 * Register shop widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.13.0
	 */
	public function register_controls() {
		parent::register_controls();

		$this->injection_section_content_layout();

		$this->injection_section_style_layout();
	}

	/**
	 * Register tribe events grid controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.13.0
	 */
	protected function injection_section_content_layout() {
		$this->start_injection( array( 'of' => TribeEventsModule::CONTROL_TEMPLATE_NAME ) );

		$this->add_control(
			'posts_per_page',
			array(
				'label' => __( 'Events', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => self::DEFAULT_COLUMNS_AND_ROWS,
				'min' => 1,
				'condition' => array( self::QUERY_CONTROL_PREFIX . '_post_type!' => 'current_query' ),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label' => __( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 12,
				'default' => self::DEFAULT_COLUMNS_AND_ROWS,
				'tablet_default' => 3,
				'mobile_default' => 1,
				'frontend_available' => true,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-columns: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'masonry',
			array(
				'label' => __( 'Masonry', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'return_value' => 'masonry',
				'prefix_class' => 'cmsmasters--',
				'render_type' => 'ui',
				'separator' => 'before',
				'frontend_available' => true,
				'condition' => array( 'columns!' => 1 ),
			)
		);

		$this->end_injection();
	}

	/**
	 * Register events grid widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.13.0
	 */
	protected function injection_section_style_layout() {
		$this->start_injection(
			array(
				'at' => 'before',
				'of' => 'event_section_style',
			)
		);

		$this->start_controls_section(
			'section_style_layout',
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
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 150,
					),
					'%' => array(
						'min' => 0,
						'max' => 25,
						'step' => 0.5,
					),
				),
				'size_units' => array( 'px' ),
				'frontend_available' => true,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-gap-column: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'columns!' => 1 ),
			)
		);

		$this->add_responsive_control(
			'rows_gap',
			array(
				'label' => __( 'Rows Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 150,
					),
					'%' => array(
						'min' => 0,
						'max' => 25,
						'step' => 0.5,
					),
				),
				'size_units' => array( 'px' ),
				'frontend_available' => true,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-gap-row: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->border_columns->add_controls();

		$this->update_control(
			'border_columns_type',
			array(
				'label' => __( 'Separators', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->update_control(
			'border_columns_color',
			array( 'label' => __( 'Color', 'cmsmasters-elementor' ) )
		);

		$this->update_control(
			'border_vertical_width',
			array( 'label' => __( 'Vertical Width', 'cmsmasters-elementor' ) )
		);

		$this->add_responsive_control(
			'border_horizontal_width',
			array(
				'label' => __( 'Horizontal Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 5,
					),
				),
				'render_type' => 'ui',
				'size_units' => array( 'px' ),
				'frontend_available' => true,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-tribe-events__event::after' => 'border-bottom-width: {{SIZE}}{{UNIT}}; border-style: {{border_columns_type.VALUE}}; bottom: calc(-1 * (var(--cmsmasters-gap-row) / 2) - ({{SIZE}}{{UNIT}} / 2));',
				),
				'condition' => array( 'border_columns_type!' => '' ),
			)
		);

		$this->add_control(
			'border_row_color',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 'true',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-tribe-events__event::after' => ' border-color: {{border_columns_color.VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->end_injection();
	}

	/**
	 * Register tribe events grid controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.13.0
	 */
	protected function get_controls_to_hide_for_template() {
		$controls_ids = parent::get_controls_to_hide_for_template();

		$controls_ids[] = 'image_ratio';
		$controls_ids[] = 'image_ratio_switcher';

		return $controls_ids;
	}

	/**
	 * Events Grid Widget constructor.
	 *
	 * Initializing the widget events grid class.
	 *
	 * @since 1.13.0
	 *
	 * @param array $data Widget data.
	 * @param array|null $args Widget default arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		$this->border_columns = new Border_Columns( $this );

		parent::__construct( $data, $args );
	}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.13.0
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array(
			array(
				'field' => 'read_more_text',
				'type' => esc_html__( 'Read More Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_load_more_text_normal',
				'type' => esc_html__( 'Load More Text (Normal state)', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_load_more_text_loading',
				'type' => esc_html__( 'Load More Text (Loading state)', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_infinite_scroll_text',
				'type' => esc_html__( 'Infinite Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_text_prev',
				'type' => esc_html__( 'Pagination Previous Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_text_next',
				'type' => esc_html__( 'Pagination Next Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
