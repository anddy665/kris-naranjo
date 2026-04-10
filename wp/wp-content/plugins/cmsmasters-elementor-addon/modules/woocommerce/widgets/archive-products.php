<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Modules\Woocommerce\Classes\Products_Renderer;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Archive_Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Archive Product widget.
 *
 * Addon widget that displays shop of current WooCommerce product.
 *
 * @since 1.0.0
 */
class Archive_Products extends Products {

	use Woo_Archive_Widget;

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Products Archive', 'cmsmasters-elementor' );
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
	 * @since 1.14.4
	 *
	 * @return bool true - with control, false - without control.
	 */
	public function lazyload_widget_use_control() {
		return false;
	}

	/**
	 * @since 1.0.0
	 * @since 1.9.2 Fixed archive products controls.
	 */
	protected function register_controls() {
		parent::register_controls();

		$this->remove_responsive_control( 'products_pre_page' );

		$this->update_control(
			'pagination_show',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 'yes',
			),
			array(
				'recursive' => true,
			)
		);

		$this->update_control(
			'pagination_type',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 'pagination',
			),
			array(
				'recursive' => true,
			)
		);

		$this->update_control(
			'pagination_via_ajax',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => '',
			),
			array(
				'recursive' => true,
			)
		);

		$this->update_control(
			'pagination_save_state',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 'yes',
			),
			array(
				'recursive' => true,
			)
		);

		$this->update_control(
			'allow_order',
			array(
				'default' => 'yes',
			)
		);

		$this->update_control(
			'section_query',
			array(
				'type' => 'hidden',
			)
		);

		$this->update_control(
			Products_Renderer::QUERY_CONTROL_NAME . '_post_type',
			array(
				'default' => 'current_query',
			)
		);

		$this->start_controls_section(
			'section_advanced',
			array(
				'label' => __( 'Advanced', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'nothing_found_message',
			array(
				'label' => __( 'Nothing Found Message', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'It seems we can\'t find what you\'re looking for.', 'cmsmasters-elementor' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_nothing_found_style',
			array(
				'tab' => Controls_Manager::TAB_STYLE,
				'label' => __( 'Nothing Found Message', 'cmsmasters-elementor' ),
				'condition' => array(
					'nothing_found_message!' => '',
				),
			)
		);

		$this->add_control(
			'nothing_found_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-products-nothing-found' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'nothing_found_typography',
				'selector' => '{{WRAPPER}} .elementor-products-nothing-found',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render no result message.
	 *
	 * @since 1.0.0
	 */
	public function render_no_results() {
		echo '<div class="elementor-nothing-found elementor-products-nothing-found">' .
			esc_html( $this->get_settings( 'nothing_found_message' ) ) .
		'</div>';
	}

	/**
	 * @since 1.0.0
	 * @since 1.11.8 Fixed render empty content
	 */
	public function render() {
		$this->set_wp_query( $GLOBALS['wp_query'] );

		add_filter( 'cmsmasters_elementor/modules/widgets/products/content', function ( $content ) {

			if ( empty( $content ) ) {
				$content = $this->render_no_results();
			}

			return $content;
		} );

		parent::render();
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
				'field' => 'pagination_infinite_scroll_text',
				'type' => esc_html__( 'Infinite Scroll Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_text_next',
				'type' => esc_html__( 'Next Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_text_prev',
				'type' => esc_html__( 'Previous Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_load_more_text_normal',
				'type' => esc_html__( 'Normal Button Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_load_more_text_loading',
				'type' => esc_html__( 'Loading Button Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'nothing_found_message',
				'type' => esc_html__( 'Nothing Found Message', 'cmsmasters-elementor' ),
				'editor_type' => 'AREA',
			),
		);
	}
}
