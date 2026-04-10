<?php
namespace CmsmastersElementor\Modules\LibraryTemplate\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Plugin as CmsmastersPlugin;
use CmsmastersElementor\Utils;

use Elementor\Core\Base\Document;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Template extends Base_Widget {

	public function get_title() {
		return __( 'Template', 'cmsmasters-elementor' );
	}

	public function get_icon() {
		return 'cmsicon-template';
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
			'elementor',
			'template',
			'library',
			'block',
			'page',
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

	protected function register_controls() {
		$this->start_controls_section(
			'section_template',
			array(
				'label' => __( 'Template', 'cmsmasters-elementor' ),
			)
		);

		$document_types = CmsmastersPlugin::elementor()->documents->get_document_types( array(
			'admin_tab_group' => '',
			'location_type' => 'disabled',
		), 'not' );

		$this->add_control(
			'template_id',
			array(
				'label' => __( 'Choose Template', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::QUERY,
				'autocomplete' => array(
					'object' => Query_Manager::TEMPLATE_OBJECT,
					'query' => array(
						'meta_query' => array(
							array(
								'key' => Document::TYPE_META_KEY,
								'value' => array_keys( $document_types ),
								'compare' => 'IN',
							),
						),
					),
				),
				'frontend_available' => true,
			)
		);

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
		$template_id = $this->get_settings( 'template_id' );

		if ( ! $template_id ) {
			return array();
		}

		if ( ! Utils::check_template( $template_id ) ) {
			return array();
		}

		return array( $template_id );
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.11.1 Optimized loading CSS in templates.
	 */
	public function render() {
		$template_ids = $this->get_template_ids();

		if ( empty( $template_ids ) ) {
			if ( is_admin() ) {
				Utils::render_alert( esc_html__( 'Please choose your widget template!', 'cmsmasters-elementor' ) );
			}

			return;
		}

		if ( 'enable' !== $this->lazyload_widget_get_status() ) {
			CmsmastersPlugin::instance()->frontend->print_template_css( $template_ids, $this->get_id() );
		}

		printf(
			'<div class="cmsmasters-elementor-template">%s</div>',
			CmsmastersPlugin::instance()->frontend->get_widget_template( $template_ids[0] )
		);
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
