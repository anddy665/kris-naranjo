<?php
namespace CmsmastersElementor\Modules\TemplatePages\Traits;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Utils as AddonUtils;

use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


trait Panel_Categories {

	/**
	 * Get editor panel categories.
	 *
	 * Retrieve the list of categories the element belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Editor panel categories.
	 */
	protected static function get_editor_panel_categories() {
		$editor_panel_categories = parent::get_editor_panel_categories();

		$editor_panel_categories = Utils::array_inject(
			$editor_panel_categories,
			'basic',
			array(
				Base_Document::WIDGETS_CATEGORY => array(
					'title' => __( 'CMSMasters', 'cmsmasters-elementor' ),
					'active' => true,
				),
			)
		);

		$editor_panel_categories = Utils::array_inject(
			$editor_panel_categories,
			'general',
			array(
				Base_Document::WOO_WIDGETS_CATEGORY => array(
					'title' => __( 'WooCommerce', 'cmsmasters-elementor' ),
					'active' => false,
				),
			)
		);

		$editor_panel_categories = Utils::array_inject(
			$editor_panel_categories,
			'general',
			array(
				Base_Document::TRIBE_EVENTS_WIDGETS_CATEGORY => array(
					'title' => __( 'Tribe Events', 'cmsmasters-elementor' ),
					'active' => false,
				),
			)
		);

		$editor_panel_categories = Utils::array_inject(
			$editor_panel_categories,
			'general',
			array(
				Base_Document::SITE_WIDGETS_CATEGORY => array(
					'title' => __( 'Site', 'cmsmasters-elementor' ),
					'active' => false,
				),
			)
		);

		$editor_panel_categories = Utils::array_inject(
			$editor_panel_categories,
			Base_Document::SITE_WIDGETS_CATEGORY,
			array(
				Base_Document::SINGULAR_WIDGETS_CATEGORY => array(
					'title' => __( 'Singular', 'cmsmasters-elementor' ),
					'active' => false,
				),
			)
		);

		if ( ! AddonUtils::is_pro() ) {
			$editor_panel_categories['pro-elements']['active'] = false;
		}

		return $editor_panel_categories;
	}

}
