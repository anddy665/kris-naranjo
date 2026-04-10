<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\TemplateSections\Widgets\Base\Breadcrumbs_Base;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Singular_Widget;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon breadcrumbs widget.
 *
 * Addon widget that display breadcrumbs of the pages.
 *
 * @since 1.0.0
 */
class Woo_Breadcrumbs extends Breadcrumbs_Base {

	use Woo_Singular_Widget;

	/**
	 * Get widget name.
	 *
	 * Retrieve widget name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmsmasters-woo-breadcrumbs';
	}

	/**
	 * Get group name.
	 *
	 * @since 1.6.5
	 *
	 * @return string Group name.
	 */
	public function get_group_name() {
		return 'cmsmasters-breadcrumbs';
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
		return __( 'Woo Breadcrumbs', 'cmsmasters-elementor' );
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
		return 'cmsicon-woo-breadcrumbs';
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
			'breadcrumbs',
			'internal links',
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
		return array_merge( parent::get_style_depends(), array(
			'widget-cmsmasters-woocommerce',
		) );
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the widget categories.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array(
			Base_Document::SITE_WIDGETS_CATEGORY,
			Base_Document::WOO_WIDGETS_CATEGORY,
			Base_Document::WOO_SINGULAR_WIDGETS_CATEGORY,
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

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-woo-breadcrumbs';
	}

	protected function register_controls() {
		$this->register_section_breadcrumbs_settings_start();

		$this->get_source();

		$this->register_section_breadcrumbs_settings_end();

		$this->register_section_additional_options_start();

		$this->register_section_additional_options_end();

		$this->register_section_breadcrumbs_style();

		parent::register_controls();

		$this->update_controls();
	}

	protected function get_source() {
		$this->add_control(
			'source',
			array(
				'label' => __( 'Source', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'cmsmasters',
				'prefix_class' => 'cmsmasters-breadcrumbs-type-',
			)
		);
	}

	protected function update_controls() {
		$this->update_control(
			'separator_type',
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array( 'title' => __( 'None', 'cmsmasters-elementor' ) ),
					'text' => array( 'title' => __( 'Text', 'cmsmasters-elementor' ) ),
				),
				'default' => 'text',
				'label_block' => false,
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-separator-type-',
			)
		);

		$this->update_control(
			'icon_separator',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
			)
		);
	}

	/**
	 * Render prefix content output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_prefix() {
		$settings = $this->get_settings_for_display();

		if ( 'yes' === $settings['prefix_show'] ) {
			echo '<div class="cmsmasters-widget-breadcrumbs__prefix">';
				$prefix_label = $settings['prefix_label'];

			if ( isset( $prefix_label ) && '' !== $prefix_label ) {
				echo esc_html( $prefix_label );
			} else {
				echo esc_html__( 'Browse:', 'cmsmasters-elementor' );
			}

			echo '</div>';
		}
	}

	/**
	 * Render breadcrumbs output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_breadcrumbs() {
		echo $this->get_homepage();

		woocommerce_breadcrumb( array(
			'delimiter' => $this->get_separator(),
			'wrap_before' => $this->get_separator(),
			'wrap_after' => '',
			'before' => '<span>',
			'after' => '</span>',
			'home' => '',
		) );
	}

	/**
	 * Render separator output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_separator() {
		$settings = $this->get_active_settings();

		if ( 'none' === $settings['separator_type'] ) {
			return;
		}

		$out = '<span class="cmsmasters-widget-breadcrumbs__sep">';

			$custom_separator = esc_html( $settings['custom_separator'] );
			$separator_value = esc_html__( '/', 'cmsmasters-elementor' );

		if ( isset( $custom_separator ) && '' !== $custom_separator ) {
			$separator_value = $custom_separator;
		}

			$out .= '<span>' .
				mb_strimwidth( $separator_value, 0, 3 ) .
			'</span>';

		$out .= '</span>';

		return $out;
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
				'field' => 'homepage_text',
				'type' => esc_html__( 'Homepage Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_separator',
				'type' => esc_html__( 'Separator Symbol', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'prefix_label',
				'type' => esc_html__( 'Prefix Label Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
