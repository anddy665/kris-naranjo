<?php
namespace PediatricClinicSpace\Kits\Classes;

use PediatricClinicSpace\Kits\Documents\Kit;
use PediatricClinicSpace\ThemeConfig\Theme_Config;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Kit globals class.
 *
 * Modifies Elementor default kit global settings.
 */
class Kit_Globals {

	const COLOR_PRIMARY = Global_Colors::COLOR_PRIMARY;
	const COLOR_SECONDARY = Global_Colors::COLOR_SECONDARY;
	const COLOR_TEXT = Global_Colors::COLOR_TEXT;
	const COLOR_ACCENT = Global_Colors::COLOR_ACCENT;
	const COLOR_TERTIARY = 'globals/colors?id=tertiary';
	const COLOR_BACKGROUND = 'globals/colors?id=background';
	const COLOR_ALTERNATE = 'globals/colors?id=alternate';
	const COLOR_BORDER = 'globals/colors?id=border';

	const TYPOGRAPHY_PRIMARY = Global_Typography::TYPOGRAPHY_PRIMARY;
	const TYPOGRAPHY_SECONDARY = Global_Typography::TYPOGRAPHY_SECONDARY;
	const TYPOGRAPHY_TEXT = Global_Typography::TYPOGRAPHY_TEXT;
	const TYPOGRAPHY_ACCENT = Global_Typography::TYPOGRAPHY_ACCENT;
	const TYPOGRAPHY_TERTIARY = 'globals/typography?id=tertiary';
	const TYPOGRAPHY_META = 'globals/typography?id=meta';
	const TYPOGRAPHY_TAXONOMY = 'globals/typography?id=taxonomy';
	const TYPOGRAPHY_SMALL = 'globals/typography?id=small';
	const TYPOGRAPHY_H1 = 'globals/typography?id=h1';
	const TYPOGRAPHY_H2 = 'globals/typography?id=h2';
	const TYPOGRAPHY_H3 = 'globals/typography?id=h3';
	const TYPOGRAPHY_H4 = 'globals/typography?id=h4';
	const TYPOGRAPHY_H5 = 'globals/typography?id=h5';
	const TYPOGRAPHY_H6 = 'globals/typography?id=h6';
	const TYPOGRAPHY_BUTTON = 'globals/typography?id=button';
	const TYPOGRAPHY_BLOCKQUOTE = 'globals/typography?id=blockquote';

	public function __construct() {
		$this->init_actions();
	}

	protected function init_actions() {
		// Editor
		add_action( 'cmsmasters_elementor/documents/kit/before_register_addon_kit_controls', array( $this, 'kit_change_controls' ) );

		add_action( 'cmsmasters_elementor/documents/kit/before_register_addon_kit_controls', array( $this, 'kit_globals_injection' ) );
	}

	/**
	 * Kit change controls.
	 *
	 * Modifies Elementor default kit settings controls.
	 *
	 * Fired by `cmsmasters_elementor/documents/kit/before_register_addon_kit_controls` Addon action hook.
	 *
	 * @param Kit $document Addon kit document.
	 */
	public function kit_change_controls( $document ) {
		$document->remove_control( 'site_logo' );
	}

	/**
	 * Kit globals injection.
	 *
	 * Modifies Elementor default kit global settings.
	 *
	 * Fired by `cmsmasters_elementor/documents/kit/before_register_addon_kit_controls` Addon action hook.
	 *
	 * @param Kit $document Addon kit document.
	 */
	public function kit_globals_injection( $document ) {
		$document->start_injection( array( 'of' => 'system_colors' ) );

		$document->update_control(
			'system_colors',
			array( 'default' => self::get_global_colors_defaults() ),
			array( 'recursive' => true )
		);

		$document->end_injection();

		$document->start_injection( array( 'of' => 'system_typography' ) );

		$document->update_control(
			'system_typography',
			array( 'default' => self::get_global_typography_defaults() ),
			array( 'recursive' => true )
		);

		$document->end_injection();
	}

	/**
	 * Get global colors defaults.
	 */
	public static function get_global_colors_defaults() {
		return array(
			array(
				'_id' => 'primary',
				'title' => esc_html__( 'Primary', 'pediatric-clinic' ),
				'color' => Theme_Config::PRIMARY_COLOR_DEFAULT,
			),
			array(
				'_id' => 'secondary',
				'title' => esc_html__( 'Secondary', 'pediatric-clinic' ),
				'color' => Theme_Config::SECONDARY_COLOR_DEFAULT,
			),
			array(
				'_id' => 'text',
				'title' => esc_html__( 'Text', 'pediatric-clinic' ),
				'color' => Theme_Config::TEXT_COLOR_DEFAULT,
			),
			array(
				'_id' => 'accent',
				'title' => esc_html__( 'Accent', 'pediatric-clinic' ),
				'color' => Theme_Config::ACCENT_COLOR_DEFAULT,
			),
			array(
				'_id' => 'tertiary',
				'title' => esc_html__( 'Alternate Accent', 'pediatric-clinic' ),
				'color' => Theme_Config::TERTIARY_COLOR_DEFAULT,
			),
			array(
				'_id' => 'background',
				'title' => esc_html__( 'Background', 'pediatric-clinic' ),
				'color' => Theme_Config::BACKGROUND_COLOR_DEFAULT,
			),
			array(
				'_id' => 'alternate',
				'title' => esc_html__( 'Alternate Background', 'pediatric-clinic' ),
				'color' => Theme_Config::ALTERNATE_COLOR_DEFAULT,
			),
			array(
				'_id' => 'border',
				'title' => esc_html__( 'Border', 'pediatric-clinic' ),
				'color' => Theme_Config::BORDER_COLOR_DEFAULT,
			),
		);
	}

	/**
	 * Get global typography defaults.
	 */
	public static function get_global_typography_defaults() {
		$typography_key = Global_Typography::TYPOGRAPHY_GROUP_PREFIX . 'typography';
		$font_family_key = Global_Typography::TYPOGRAPHY_GROUP_PREFIX . 'font_family';
		$font_size_key = Global_Typography::TYPOGRAPHY_GROUP_PREFIX . 'font_size';
		$font_weight_key = Global_Typography::TYPOGRAPHY_GROUP_PREFIX . 'font_weight';
		$text_transform_key = Global_Typography::TYPOGRAPHY_GROUP_PREFIX . 'text_transform';
		$font_style_key = Global_Typography::TYPOGRAPHY_GROUP_PREFIX . 'font_style';
		$text_decoration_key = Global_Typography::TYPOGRAPHY_GROUP_PREFIX . 'text_decoration';
		$line_height_key = Global_Typography::TYPOGRAPHY_GROUP_PREFIX . 'line_height';
		$letter_spacing_key = Global_Typography::TYPOGRAPHY_GROUP_PREFIX . 'letter_spacing';
		$word_spacing_key = Global_Typography::TYPOGRAPHY_GROUP_PREFIX . 'word_spacing';

		return array(
			array(
				'_id' => 'primary',
				'title' => esc_html__( 'Primary', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::PRIMARY_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_weight_key => Theme_Config::PRIMARY_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
			),
			array(
				'_id' => 'secondary',
				'title' => esc_html__( 'Secondary', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::SECONDARY_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_weight_key => Theme_Config::SECONDARY_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
			),
			array(
				'_id' => 'text',
				'title' => esc_html__( 'Text', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::TEXT_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_size_key => Theme_Config::TEXT_TYPOGRAPHY_DEFAULT_FONT_SIZE,
				$font_weight_key => Theme_Config::TEXT_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
				$text_transform_key => Theme_Config::TEXT_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM,
				$font_style_key => Theme_Config::TEXT_TYPOGRAPHY_DEFAULT_FONT_STYLE,
				$text_decoration_key => Theme_Config::TEXT_TYPOGRAPHY_DEFAULT_TEXT_DECORATION,
				$line_height_key => Theme_Config::TEXT_TYPOGRAPHY_DEFAULT_LINE_HEIGHT,
				$letter_spacing_key => Theme_Config::TEXT_TYPOGRAPHY_DEFAULT_LETTER_SPACING,
				$word_spacing_key => Theme_Config::TEXT_TYPOGRAPHY_DEFAULT_WORD_SPACING,
			),
			array(
				'_id' => 'accent',
				'title' => esc_html__( 'Accent', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::ACCENT_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_size_key => Theme_Config::ACCENT_TYPOGRAPHY_DEFAULT_FONT_SIZE,
				$font_weight_key => Theme_Config::ACCENT_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
				$text_transform_key => Theme_Config::ACCENT_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM,
				$font_style_key => Theme_Config::ACCENT_TYPOGRAPHY_DEFAULT_FONT_STYLE,
				$text_decoration_key => Theme_Config::ACCENT_TYPOGRAPHY_DEFAULT_TEXT_DECORATION,
				$line_height_key => Theme_Config::ACCENT_TYPOGRAPHY_DEFAULT_LINE_HEIGHT,
				$letter_spacing_key => Theme_Config::ACCENT_TYPOGRAPHY_DEFAULT_LETTER_SPACING,
				$word_spacing_key => Theme_Config::ACCENT_TYPOGRAPHY_DEFAULT_WORD_SPACING,
			),
			array(
				'_id' => 'tertiary',
				'title' => esc_html__( 'Alternate Accent', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::TERTIARY_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_size_key => Theme_Config::TERTIARY_TYPOGRAPHY_DEFAULT_FONT_SIZE,
				$font_weight_key => Theme_Config::TERTIARY_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
				$text_transform_key => Theme_Config::TERTIARY_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM,
				$font_style_key => Theme_Config::TERTIARY_TYPOGRAPHY_DEFAULT_FONT_STYLE,
				$text_decoration_key => Theme_Config::TERTIARY_TYPOGRAPHY_DEFAULT_TEXT_DECORATION,
				$line_height_key => Theme_Config::TERTIARY_TYPOGRAPHY_DEFAULT_LINE_HEIGHT,
				$letter_spacing_key => Theme_Config::TERTIARY_TYPOGRAPHY_DEFAULT_LETTER_SPACING,
				$word_spacing_key => Theme_Config::TERTIARY_TYPOGRAPHY_DEFAULT_WORD_SPACING,
			),
			array(
				'_id' => 'meta',
				'title' => esc_html__( 'Meta', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::META_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_size_key => Theme_Config::META_TYPOGRAPHY_DEFAULT_FONT_SIZE,
				$font_weight_key => Theme_Config::META_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
				$text_transform_key => Theme_Config::META_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM,
				$font_style_key => Theme_Config::META_TYPOGRAPHY_DEFAULT_FONT_STYLE,
				$text_decoration_key => Theme_Config::META_TYPOGRAPHY_DEFAULT_TEXT_DECORATION,
				$line_height_key => Theme_Config::META_TYPOGRAPHY_DEFAULT_LINE_HEIGHT,
				$letter_spacing_key => Theme_Config::META_TYPOGRAPHY_DEFAULT_LETTER_SPACING,
				$word_spacing_key => Theme_Config::META_TYPOGRAPHY_DEFAULT_WORD_SPACING,
			),
			array(
				'_id' => 'taxonomy',
				'title' => esc_html__( 'Alternate Meta', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::TAXONOMY_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_size_key => Theme_Config::TAXONOMY_TYPOGRAPHY_DEFAULT_FONT_SIZE,
				$font_weight_key => Theme_Config::TAXONOMY_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
				$text_transform_key => Theme_Config::TAXONOMY_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM,
				$font_style_key => Theme_Config::TAXONOMY_TYPOGRAPHY_DEFAULT_FONT_STYLE,
				$text_decoration_key => Theme_Config::TAXONOMY_TYPOGRAPHY_DEFAULT_TEXT_DECORATION,
				$line_height_key => Theme_Config::TAXONOMY_TYPOGRAPHY_DEFAULT_LINE_HEIGHT,
				$letter_spacing_key => Theme_Config::TAXONOMY_TYPOGRAPHY_DEFAULT_LETTER_SPACING,
				$word_spacing_key => Theme_Config::TAXONOMY_TYPOGRAPHY_DEFAULT_WORD_SPACING,
			),
			array(
				'_id' => 'small',
				'title' => esc_html__( 'Small', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::SMALL_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_size_key => Theme_Config::SMALL_TYPOGRAPHY_DEFAULT_FONT_SIZE,
				$font_weight_key => Theme_Config::SMALL_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
				$text_transform_key => Theme_Config::SMALL_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM,
				$font_style_key => Theme_Config::SMALL_TYPOGRAPHY_DEFAULT_FONT_STYLE,
				$text_decoration_key => Theme_Config::SMALL_TYPOGRAPHY_DEFAULT_TEXT_DECORATION,
				$line_height_key => Theme_Config::SMALL_TYPOGRAPHY_DEFAULT_LINE_HEIGHT,
				$letter_spacing_key => Theme_Config::SMALL_TYPOGRAPHY_DEFAULT_LETTER_SPACING,
				$word_spacing_key => Theme_Config::SMALL_TYPOGRAPHY_DEFAULT_WORD_SPACING,
			),
			array(
				'_id' => 'h1',
				'title' => esc_html__( 'H1', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::H1_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_size_key => Theme_Config::H1_TYPOGRAPHY_DEFAULT_FONT_SIZE,
				$font_weight_key => Theme_Config::H1_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
				$text_transform_key => Theme_Config::H1_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM,
				$font_style_key => Theme_Config::H1_TYPOGRAPHY_DEFAULT_FONT_STYLE,
				$text_decoration_key => Theme_Config::H1_TYPOGRAPHY_DEFAULT_TEXT_DECORATION,
				$line_height_key => Theme_Config::H1_TYPOGRAPHY_DEFAULT_LINE_HEIGHT,
				$letter_spacing_key => Theme_Config::H1_TYPOGRAPHY_DEFAULT_LETTER_SPACING,
				$word_spacing_key => Theme_Config::H1_TYPOGRAPHY_DEFAULT_WORD_SPACING,
			),
			array(
				'_id' => 'h2',
				'title' => esc_html__( 'H2', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::H2_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_size_key => Theme_Config::H2_TYPOGRAPHY_DEFAULT_FONT_SIZE,
				$font_weight_key => Theme_Config::H2_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
				$text_transform_key => Theme_Config::H2_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM,
				$font_style_key => Theme_Config::H2_TYPOGRAPHY_DEFAULT_FONT_STYLE,
				$text_decoration_key => Theme_Config::H2_TYPOGRAPHY_DEFAULT_TEXT_DECORATION,
				$line_height_key => Theme_Config::H2_TYPOGRAPHY_DEFAULT_LINE_HEIGHT,
				$letter_spacing_key => Theme_Config::H2_TYPOGRAPHY_DEFAULT_LETTER_SPACING,
				$word_spacing_key => Theme_Config::H2_TYPOGRAPHY_DEFAULT_WORD_SPACING,
			),
			array(
				'_id' => 'h3',
				'title' => esc_html__( 'H3', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::H3_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_size_key => Theme_Config::H3_TYPOGRAPHY_DEFAULT_FONT_SIZE,
				$font_weight_key => Theme_Config::H3_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
				$text_transform_key => Theme_Config::H3_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM,
				$font_style_key => Theme_Config::H3_TYPOGRAPHY_DEFAULT_FONT_STYLE,
				$text_decoration_key => Theme_Config::H3_TYPOGRAPHY_DEFAULT_TEXT_DECORATION,
				$line_height_key => Theme_Config::H3_TYPOGRAPHY_DEFAULT_LINE_HEIGHT,
				$letter_spacing_key => Theme_Config::H3_TYPOGRAPHY_DEFAULT_LETTER_SPACING,
				$word_spacing_key => Theme_Config::H3_TYPOGRAPHY_DEFAULT_WORD_SPACING,
			),
			array(
				'_id' => 'h4',
				'title' => esc_html__( 'H4', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::H4_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_size_key => Theme_Config::H4_TYPOGRAPHY_DEFAULT_FONT_SIZE,
				$font_weight_key => Theme_Config::H4_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
				$text_transform_key => Theme_Config::H4_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM,
				$font_style_key => Theme_Config::H4_TYPOGRAPHY_DEFAULT_FONT_STYLE,
				$text_decoration_key => Theme_Config::H4_TYPOGRAPHY_DEFAULT_TEXT_DECORATION,
				$line_height_key => Theme_Config::H4_TYPOGRAPHY_DEFAULT_LINE_HEIGHT,
				$letter_spacing_key => Theme_Config::H4_TYPOGRAPHY_DEFAULT_LETTER_SPACING,
				$word_spacing_key => Theme_Config::H4_TYPOGRAPHY_DEFAULT_WORD_SPACING,
			),
			array(
				'_id' => 'h5',
				'title' => esc_html__( 'H5', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::H5_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_size_key => Theme_Config::H5_TYPOGRAPHY_DEFAULT_FONT_SIZE,
				$font_weight_key => Theme_Config::H5_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
				$text_transform_key => Theme_Config::H5_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM,
				$font_style_key => Theme_Config::H5_TYPOGRAPHY_DEFAULT_FONT_STYLE,
				$text_decoration_key => Theme_Config::H5_TYPOGRAPHY_DEFAULT_TEXT_DECORATION,
				$line_height_key => Theme_Config::H5_TYPOGRAPHY_DEFAULT_LINE_HEIGHT,
				$letter_spacing_key => Theme_Config::H5_TYPOGRAPHY_DEFAULT_LETTER_SPACING,
				$word_spacing_key => Theme_Config::H5_TYPOGRAPHY_DEFAULT_WORD_SPACING,
			),
			array(
				'_id' => 'h6',
				'title' => esc_html__( 'H6', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::H6_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_size_key => Theme_Config::H6_TYPOGRAPHY_DEFAULT_FONT_SIZE,
				$font_weight_key => Theme_Config::H6_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
				$text_transform_key => Theme_Config::H6_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM,
				$font_style_key => Theme_Config::H6_TYPOGRAPHY_DEFAULT_FONT_STYLE,
				$text_decoration_key => Theme_Config::H6_TYPOGRAPHY_DEFAULT_TEXT_DECORATION,
				$line_height_key => Theme_Config::H6_TYPOGRAPHY_DEFAULT_LINE_HEIGHT,
				$letter_spacing_key => Theme_Config::H6_TYPOGRAPHY_DEFAULT_LETTER_SPACING,
				$word_spacing_key => Theme_Config::H6_TYPOGRAPHY_DEFAULT_WORD_SPACING,
			),
			array(
				'_id' => 'button',
				'title' => esc_html__( 'Button', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::BUTTON_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_size_key => Theme_Config::BUTTON_TYPOGRAPHY_DEFAULT_FONT_SIZE,
				$font_weight_key => Theme_Config::BUTTON_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
				$text_transform_key => Theme_Config::BUTTON_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM,
				$font_style_key => Theme_Config::BUTTON_TYPOGRAPHY_DEFAULT_FONT_STYLE,
				$text_decoration_key => Theme_Config::BUTTON_TYPOGRAPHY_DEFAULT_TEXT_DECORATION,
				$line_height_key => Theme_Config::BUTTON_TYPOGRAPHY_DEFAULT_LINE_HEIGHT,
				$letter_spacing_key => Theme_Config::BUTTON_TYPOGRAPHY_DEFAULT_LETTER_SPACING,
				$word_spacing_key => Theme_Config::BUTTON_TYPOGRAPHY_DEFAULT_WORD_SPACING,
			),
			array(
				'_id' => 'blockquote',
				'title' => esc_html__( 'Blockquote', 'pediatric-clinic' ),
				$typography_key => 'custom',
				$font_family_key => Theme_Config::BLOCKQUOTE_TYPOGRAPHY_DEFAULT_FONT_FAMILY,
				$font_size_key => Theme_Config::BLOCKQUOTE_TYPOGRAPHY_DEFAULT_FONT_SIZE,
				$font_weight_key => Theme_Config::BLOCKQUOTE_TYPOGRAPHY_DEFAULT_FONT_WEIGHT,
				$text_transform_key => Theme_Config::BLOCKQUOTE_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM,
				$font_style_key => Theme_Config::BLOCKQUOTE_TYPOGRAPHY_DEFAULT_FONT_STYLE,
				$text_decoration_key => Theme_Config::BLOCKQUOTE_TYPOGRAPHY_DEFAULT_TEXT_DECORATION,
				$line_height_key => Theme_Config::BLOCKQUOTE_TYPOGRAPHY_DEFAULT_LINE_HEIGHT,
				$letter_spacing_key => Theme_Config::BLOCKQUOTE_TYPOGRAPHY_DEFAULT_LETTER_SPACING,
				$word_spacing_key => Theme_Config::BLOCKQUOTE_TYPOGRAPHY_DEFAULT_WORD_SPACING,
			),
		);
	}

}
