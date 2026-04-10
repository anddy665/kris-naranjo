<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Animation\Classes\Animation as AnimationModule;
use CmsmastersElementor\Modules\Tabs\Widgets\Tabs;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Singular_Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Product_Data_Tabs extends Tabs {

	use Woo_Singular_Widget;

	/**
	 * Get group name.
	 *
	 * @since 1.6.5
	 *
	 * @return string Group name.
	 */
	public function get_group_name() {
		return 'cmsmasters-tabs';
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
		return __( 'Product Data Tabs', 'cmsmasters-elementor' );
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
		return 'cmsicon-product-tabs';
	}

	/**
	 * Get script depends.
	 *
	 * @since 1.0.0
	 *
	 * @return array Get script depends.
	 */
	public function get_script_depends() {
		return array( 'wc-single-product' );
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
		return array_merge( parent::get_style_depends(), array(
			'widget-cmsmasters-woocommerce',
		) );
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
	 * Get HTML wrapper class.
	 *
	 * Retrieve the widget container class.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget container class.
	 */
	protected function get_html_wrapper_class() {
		return 'cmsmasters-widget-tabs';
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		parent::register_controls();

		$this->remove_control( 'tabs' );

		$this->start_injection(
			array(
				'of' => 'tabs_type',
				'at' => 'before',
			)
		);

		$this->add_control(
			'tabs_select',
			array(
				'label' => __( 'Tabs', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::SELECTIZE,
				'options' => array(
					'description' => __( 'Description', 'cmsmasters-elementor' ),
					'additional_information' => __( 'Additional', 'cmsmasters-elementor' ),
					'reviews' => __( 'Reviews', 'cmsmasters-elementor' ),
				),
				'default' => array(
					'description',
					'additional_information',
					'reviews',
				),
				'separator' => 'before',
				'label_block' => true,
				'frontend_available' => true,
				'multiple' => true,
			)
		);

		$this->end_injection();

		$this->remove_control( 'tab_content_text_alignment' );
		$this->remove_control( 'tab_content_typography' );
		$this->remove_control( 'tab_content_text_color' );
		$this->remove_control( 'tab_list_ver_alignment' );
		$this->remove_control( 'tab_list_item_style_icon_position' );
		$this->remove_control( 'tab_list_style_icon' );
		$this->remove_control( 'tab_list_item_style_icon_size' );
		$this->remove_control( 'tab_list_item_style_icon_gap' );
		$this->remove_control( 'section_style_tab_icon' );

		$this->start_injection(
			array(
				'of' => 'section_tab_content_style',
				'type' => 'section',
			)
		);

		$this->add_control(
			'disable_content_heading',
			array(
				'label' => __( 'Disable Content Heading', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'prefix_class' => 'cmsmasters-disable-heading-',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'content_heading_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-tab > h2, {{WRAPPER}} .woocommerce-Reviews-title',
				'condition' => array( 'disable_content_heading' => '' ),
			)
		);

		$this->add_control(
			'content_heading_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-tab > h2, {{WRAPPER}} .woocommerce-Reviews-title' => 'color: {{VALUE}}',
				),
				'condition' => array( 'disable_content_heading' => '' ),
			)
		);

		$this->end_injection();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		$settings = $this->get_active_settings();
		$tabs = $settings['tabs_select'];
		$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

		if ( ! empty( $product_tabs ) ) {
			echo '<div class="cmsmasters-tabs">';
				$this->print_product_tabs_list( $settings, $product_tabs );
				$this->print_product_tabs_content( $tabs, $product_tabs );
			echo '</div>';
		}

		?>
		<script>
			jQuery( '.cmsmasters-tabs, #rating' ).trigger( 'init' );
		</script>
		<?php
	}

	/**
	 * Print tabs list.
	 *
	 * Retrieves tabs list.
	 *
	 * @since 1.0.0
	 * @since 1.10.0 Fixed responsive tabs
	 */
	public function print_product_tabs_list( $settings, $product_tabs ) {
		$title_tag = $settings['title_tag'];

		$animation_class = AnimationModule::get_animation_class();
		$tab_count = 1;

		echo '<div class="cmsmasters-tabs-list-wrapper">
			<ul class="cmsmasters-tabs-list" role="tablist">';

			foreach ( $settings['tabs_select'] as $key ) {
				$item_attr = array(
					'product_tabs' => $product_tabs,
					'key' => $key,
					'title_tag' => $title_tag,
					'animation_class' => $animation_class,
					'tab_count' => $tab_count,
				);

				$is_isset = isset( $item_attr['product_tabs'][ $key ] );

				if ( $is_isset ) {
					++$tab_count;

					$this->tabs_list_item( $item_attr );
				}
			}

			echo '</ul>
		</div>';
	}

	/**
	 * Print tabs list item.
	 *
	 * Retrieves tabs list item html.
	 *
	 * @since 1.0.0
	 */
	private function tabs_list_item( $item_attr ) {
		$key = $item_attr['key'];

		if ( ! isset( $item_attr['product_tabs'][ $key ] ) ) {
			return;
		}

		$tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $key );

		$this->add_render_attribute( $tab_title_setting_key, array(
			'id' => 'cmsmasters-product-tabs-list-item-' . esc_attr( $key ),
			'class' => array( 'cmsmasters-tabs-list-item', esc_attr( $item_attr['animation_class'] ) ),
			'data-tab' => esc_attr( $item_attr['tab_count'] ),
			'role' => 'tab',
			'aria-selected' => 'true',
			'tabindex' => '0',
		) );

		echo '<li ' . $this->get_render_attribute_string( $tab_title_setting_key ) . '>' .
			'<a href="#' . esc_attr( $key ) . '" class="cmsmasters-tab-title">';

				$title_tag = ( isset( $item_attr['title_tag'] ) ? $item_attr['title_tag'] : 'h6' );

				echo '<' . Utils::validate_html_tag( $title_tag ) . ' class="cmsmasters-tab-title__text">' .
					wp_kses_post(
						apply_filters( 'woocommerce_product_' . $key . '_tab_title',
							$item_attr['product_tabs'][ $key ]['title'],
							$key
						)
					) .
				'</' . Utils::validate_html_tag( $title_tag ) . '>' .
			'</a>' .
		'</li>';
	}

	/**
	 * Get tabs content.
	 *
	 * Retrieves all tabs content html.
	 *
	 * @since 1.0.0
	 */
	public function print_product_tabs_content( $tabs, $product_tabs ) {
		$animation_class = AnimationModule::get_animation_class();
		$tab_count = 1;

		echo '<div class="cmsmasters-tabs_wrap" role="tabpanel">';

		foreach ( $tabs as $key ) {
			$item_attr = array(
				'product_tabs' => $product_tabs,
				'key' => $key,
				'animation_class' => $animation_class,
				'tab_count' => $tab_count,
			);

			$is_isset = isset( $item_attr['product_tabs'][ $key ] );

			if ( $is_isset ) {
				++$tab_count;

				$this->content_item( $item_attr );
			}
		}

		echo '</div>';
	}

	/**
	 * Get tabs content.
	 *
	 * Retrieves html content of each tab.
	 *
	 * @since 1.0.0
	 * @since 1.19.1 Changed the output of the short description to the description.
	 */
	private function content_item( $item_attr ) {
		$key = $item_attr['key'];

		if ( ! isset( $item_attr['product_tabs'][ $key ] ) ) {
			return;
		}

		$callback = $item_attr['product_tabs'][ $key ]['callback'];

		$tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $key );

		$this->add_render_attribute( $tab_content_setting_key, array(
			'id' => 'cmsmasters-product-tab-content-' . esc_attr( $key ),
			'class' => 'cmsmasters-tab',
			'data-tab' => esc_attr( $item_attr['tab_count'] ),
		) );

		$this->print_product_accordion_start( $item_attr );

		echo '<div ' . $this->get_render_attribute_string( $tab_content_setting_key ) . '>';

		if ( 'description' === $key ) {
			global $product;

			if ( $product ) {
				$description = the_content();

				if ( ! empty( $description ) ) {
					echo wp_kses_post( $description );
				}
			}
		} elseif ( 'description' !== $key && isset( $callback ) ) {
			call_user_func( $callback,
				$key,
				$item_attr['product_tabs'][ $key ]
			);
		}

		echo '</div>';

		$this->print_product_accordion_end();
	}

	/**
	 * Print accordion html.
	 *
	 * Retrieves accordion html start.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Fixed render icons for Woo data tabs.
	 */
	private function print_product_accordion_start( $item_attr ) {
		$settings = $this->get_active_settings();

		if ( '' === $settings['tabs_responsive'] ) {
			return;
		}

		$title_tag = $settings['title_tag'];
		$key = $item_attr['key'];

		if ( ! isset( $item_attr['product_tabs'][ $key ] ) ) {
			return;
		}

		echo '<div class="cmsmasters-accordion-item-wrap">';

		$accordion_title_setting_key = $this->get_repeater_setting_key( 'accordion_title', 'tabs', $key );

		$this->add_render_attribute( $accordion_title_setting_key, array(
			'id' => 'cmsmasters-product-tabs-accordion-item-' . esc_attr( $key ),
			'class' => array( 'cmsmasters-tabs-list-item', 'cmsmasters-accordion-item', esc_attr( $item_attr['animation_class'] ) ),
			'data-tab' => esc_attr( $item_attr['tab_count'] ),
		) );

		$is_icon = 'yes' === $settings['accordion_icon_tabs_enable'];
		$icon = '';

		if ( $is_icon ) {
			$icon = $this->print_accordion_icon( $settings, true );
		}

		echo '<div ' . $this->get_render_attribute_string( $accordion_title_setting_key ) . '>' .
			'<a href="#' . esc_attr( $key ) . '" class="cmsmasters-tab-title">';

				Utils::print_unescaped_internal_string( $icon ); // XSS ok.

				echo '<span class="cmsmasters-tab-title__text-wrap">' .
					'<' . Utils::validate_html_tag( $title_tag ) . ' class="cmsmasters-tab-title__text">' .
						wp_kses_post(
							apply_filters( 'woocommerce_product_' . $key . '_tab_title',
								$item_attr['product_tabs'][ $key ]['title'],
								$key
							)
						) .
					'</' . Utils::validate_html_tag( $title_tag ) . '>' .
				'</span>' .
			'</a>' .
		'</div>';
	}

	/**
	 * Print accordion html.
	 *
	 * Retrieves accordion html end.
	 *
	 * @since 1.0.0
	 */
	private function print_product_accordion_end() {
		if ( '' === $this->get_settings( 'tabs_responsive' ) ) {
			return;
		} else {
			echo '</div>';
		}
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
