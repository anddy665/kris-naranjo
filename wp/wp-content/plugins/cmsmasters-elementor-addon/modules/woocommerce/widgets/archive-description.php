<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Modules\TemplatePages\Widgets\Archive_Description as BaseArchiveDescription;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Archive_Widget;

use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon Archive Description widget.
 *
 * Addon widget that displays current archive description.
 *
 * @since 1.0.0
 */
class Archive_Description extends BaseArchiveDescription {

	use Woo_Archive_Widget;

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
		return __( 'Products Archive Description', 'cmsmasters-elementor' );
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
		return 'cmsicon-woo-archive-description';
	}

	/**
	 * Get tag names.
	 *
	 * Retrieve widget dynamic controls tag names.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget dynamic controls tag names.
	 */
	protected function get_tag_names() {
		return array(
			'content' => 'cmsmasters-woocommerce-archive-description',
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
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		echo '<div class="entry-content">';

			echo wp_kses_post(
				wpautop(
					wp_kses(
						$this->get_settings_for_display( 'content' ),
						wp_kses_allowed_html( 'data' )
					)
				)
			);

		echo '</div>';
	}
}
