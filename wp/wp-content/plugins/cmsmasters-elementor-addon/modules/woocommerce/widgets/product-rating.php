<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Singular_Widget;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Core\Files\Assets\Svg\Svg_Handler;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Product_Rating extends Base_Widget {

	use Woo_Singular_Widget;

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
		return __( 'Product Rating', 'cmsmasters-elementor' );
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
		return 'cmsicon-product-rating';
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
			'rating',
			'review',
			'comments',
			'stars',
		);
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
			'widget-cmsmasters-woocommerce',
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
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_product_rating_content',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_single_empty_rating',
			array(
				'label' => esc_html__( 'Always Show Rating Stars', 'cmsmasters-elementor' ),
				'description' => esc_html__( 'Rating stars will be shown even in case there are no ratings', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'show_link_review',
			array(
				'label' => esc_html__( 'Show a Link to Reviews', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
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
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'prefix_class' => 'cmsmasters-product-rating__align-',
			)
		);

		$this->add_control(
			'icon_heading',
			array(
				'label' => __( 'Rating Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'content_icon_tabs' );

		$this->start_controls_tab(
			'content_rating_empty',
			array( 'label' => __( 'Empty', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'empty_rating_icon',
			array(
				'type' => Controls_Manager::ICONS,
				'description' => __( 'For correct displaying variations of the same icon should be used (eg.: star icons both for empty and filled options).<br>You can also choose either Empty or Filled icon to be used for both states.', 'cmsmasters-elementor' ),
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'far fa-star',
					'library' => 'fa-regular',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'content_rating_filled',
			array( 'label' => __( 'Filled', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'filled_rating_icon',
			array(
				'type' => Controls_Manager::ICONS,
				'description' => __( 'For correct displaying variations of the same icon should be used (eg.: star icons both for empty and filled options).<br>You can also choose either Empty or Filled icon to be used for both states.', 'cmsmasters-elementor' ),
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-star',
					'library' => 'fa-regular',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_product_rating_style',
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 40,
						'step' => 1,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-rating__html-filled i,' .
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-rating__html-filled svg' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-rating__html-empty i,' .
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-rating__html-empty svg' => 'font-size: {{SIZE}}{{UNIT}}',
				),
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'icon_spacing',
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-rating__html-empty' => 'margin: 0 calc(-{{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-rating__html-empty i,' .
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-rating__html-empty svg' => 'margin: 0 calc({{SIZE}}{{UNIT}}/2)',
				),
				'render_type' => 'template',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'icon_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-rating__html-empty > i, {{WRAPPER}} .elementor-widget-cmsmasters-woo-product-rating__html-empty > svg',
			)
		);

		$this->start_controls_tabs( 'icon_tabs' );

		$this->start_controls_tab(
			'rating_empty',
			array( 'label' => __( 'Empty', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'empty_icon_color',
			array(
				'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-rating__html-empty i,' .
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-rating__html-empty svg' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'rating_filled',
			array( 'label' => __( 'Filled', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'filled_icon_color',
			array(
				'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-rating__content .elementor-widget-cmsmasters-woo-product-rating__html-filled i,' .
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-rating__content .elementor-widget-cmsmasters-woo-product-rating__html-filled svg' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'space_between',
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default' => array(
					'size' => 10,
					'unit' => 'px',
				),
				'range' => array(
					'em' => array(
						'min' => 0,
						'max' => 4,
						'step' => 0.1,
					),
					'px' => array(
						'min' => 0,
						'max' => 50,
						'step' => 1,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'.woocommerce:not(.rtl) {{WRAPPER}} .cmsmasters-review-link' => 'margin-left: {{SIZE}}{{UNIT}}',
					'.woocommerce.rtl {{WRAPPER}} .cmsmasters-review-link' => 'margin-right: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'link_heading',
			array(
				'label' => __( 'Link to Reviews ', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-review-link',
			)
		);

		$this->start_controls_tabs( 'link_tabs' );

		$this->start_controls_tab(
			'link_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'link_color',
			array(
				'label' => __( 'Link Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-review-link' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'link_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'link_color_hover',
			array(
				'label' => __( 'Link Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-review-link:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'link_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 0.2,
				),
				'range' => array(
					'px' => array(
						'max' => 2,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-review-link' => 'transition: all {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.11.6 Fixed render icons in widget.
	 */
	protected function render() {
		if ( ! post_type_supports( 'product', 'comments' ) ) {
			return;
		}

		$product = wc_get_product();

		if ( empty( $product ) || ! wc_review_ratings_enabled() ) {
			return;
		}

		$settings = $this->get_active_settings();

		if ( 'yes' === $settings['show_single_empty_rating'] || 0 < $product->get_rating_count() ) {

			$empty_icon_value = CmsmastersUtils::get_render_icon( $settings['empty_rating_icon'], array( 'aria-hidden' => 'true' ), $with_wrap = false );
			$filled_icon_value = CmsmastersUtils::get_render_icon( $settings['filled_rating_icon'], array( 'aria-hidden' => 'true' ), $with_wrap = false );

			if ( ! empty( $empty_icon_value ) || ! empty( $filled_icon_value ) ) {
				$filled_percent = $product->get_average_rating() / 5 * 100;
				$reviews_link = '';

				if ( comments_open() && 'yes' === $settings['show_link_review'] ) {
					$review_count = $product->get_review_count();

					$reviews_link .= sprintf(
						'<a href="#reviews" class="cmsmasters-review-link" rel="nofollow">(%s)</a>',
						sprintf(
							/* translators: Product Rating WooCommerce widget customer reviews. %s: Reviews count */
							_n( '%s customer review', '%s customer reviews', $review_count, 'cmsmasters-elementor' ),
							sprintf( '<span class="count">%s</span>', esc_html( $review_count ) )
						)
					);
				}

				$empty_icon = ( ! empty( $empty_icon_value ) ) ? $empty_icon_value : $filled_icon_value;
				$filled_icon = ( ! empty( $filled_icon_value ) ) ? $filled_icon_value : $empty_icon_value;

				echo '<div class="elementor-widget-cmsmasters-woo-product-rating__content">' .
					'<div class="elementor-widget-cmsmasters-woo-product-rating__html-empty">' .
						$this->generate_rating_stars( $empty_icon ) .
						'<div class="elementor-widget-cmsmasters-woo-product-rating__html-filled" style="width:' . esc_attr( $filled_percent ) . '%;">' .
							$this->generate_rating_stars( $filled_icon ) .
						'</div>' .
					'</div>';

					Utils::print_unescaped_internal_string( $reviews_link );

				echo '</div>';
			}
		}
	}

	/**
	 * Get rating stars.
	 *
	 * Retrieves rating stars html.
	 *
	 * @since 1.0.0
	 *
	 * @return string Retrieves rating stars html.
	 */
	public function generate_rating_stars( $icon_class ) {
		$icons = '';

		foreach ( range( 1, 5 ) as $star_number ) {
			$icons .= $icon_class;
		}

		return $icons;
	}

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.0.0
	 */
	public function render_plain_content() {}
}
