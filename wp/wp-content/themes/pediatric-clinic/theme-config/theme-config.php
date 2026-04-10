<?php
namespace PediatricClinicSpace\ThemeConfig;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Theme Config.
 *
 * Main class for theme config.
 */
class Theme_Config {

	/**
	 * Major versions.
	 */
	const MAJOR_VERSIONS = array();

	/**
	 * Default Colors.
	 */
	const PRIMARY_COLOR_DEFAULT = '#7792CD';
	const SECONDARY_COLOR_DEFAULT = '#2C4073';
	const TEXT_COLOR_DEFAULT = '#4A4A4A';
	const ACCENT_COLOR_DEFAULT = '#EE6F28';
	const TERTIARY_COLOR_DEFAULT = '#E7F1A8';
	const BACKGROUND_COLOR_DEFAULT = '#FFF5E9';
	const ALTERNATE_COLOR_DEFAULT = '#FFFFFF';
	const BORDER_COLOR_DEFAULT = '#7A8085';

	/**
	 * Default Typography.
	 */
	const PRIMARY_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Quicksand';
	const PRIMARY_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '700';

	const SECONDARY_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Urbanist';
	const SECONDARY_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '500';

	/* TEXT */
	const TEXT_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Urbanist';
	const TEXT_TYPOGRAPHY_DEFAULT_FONT_SIZE = array( 'size' => '20', 'unit' => 'px' );
	const TEXT_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '400';
	const TEXT_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM = 'none';
	const TEXT_TYPOGRAPHY_DEFAULT_FONT_STYLE = 'normal';
	const TEXT_TYPOGRAPHY_DEFAULT_TEXT_DECORATION = 'none';
	const TEXT_TYPOGRAPHY_DEFAULT_LINE_HEIGHT = array( 'size' => '1.4', 'unit' => 'em' );
	const TEXT_TYPOGRAPHY_DEFAULT_LETTER_SPACING = array( 'size' => '0', 'unit' => 'px' );
	const TEXT_TYPOGRAPHY_DEFAULT_WORD_SPACING = array( 'size' => '0', 'unit' => 'px' );

	/* ACCENT */
	const ACCENT_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Urbanist';
	const ACCENT_TYPOGRAPHY_DEFAULT_FONT_SIZE = array( 'size' => '16', 'unit' => 'px' );
	const ACCENT_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '500';
	const ACCENT_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM = 'none';
	const ACCENT_TYPOGRAPHY_DEFAULT_FONT_STYLE = 'normal';
	const ACCENT_TYPOGRAPHY_DEFAULT_TEXT_DECORATION = 'none';
	const ACCENT_TYPOGRAPHY_DEFAULT_LINE_HEIGHT = array( 'size' => '1.65', 'unit' => 'em' );
	const ACCENT_TYPOGRAPHY_DEFAULT_LETTER_SPACING = array( 'size' => '0', 'unit' => 'px' );
	const ACCENT_TYPOGRAPHY_DEFAULT_WORD_SPACING = array( 'size' => '0', 'unit' => 'px' );

	/* TERTIARY */
	const TERTIARY_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Urbanist';
	const TERTIARY_TYPOGRAPHY_DEFAULT_FONT_SIZE = array( 'size' => '15', 'unit' => 'px' );
	const TERTIARY_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '400';
	const TERTIARY_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM = 'none';
	const TERTIARY_TYPOGRAPHY_DEFAULT_FONT_STYLE = 'normal';
	const TERTIARY_TYPOGRAPHY_DEFAULT_TEXT_DECORATION = 'none';
	const TERTIARY_TYPOGRAPHY_DEFAULT_LINE_HEIGHT = array( 'size' => '1.60', 'unit' => 'em' );
	const TERTIARY_TYPOGRAPHY_DEFAULT_LETTER_SPACING = array( 'size' => '0', 'unit' => 'px' );
	const TERTIARY_TYPOGRAPHY_DEFAULT_WORD_SPACING = array( 'size' => '0', 'unit' => 'px' );

	/* META */
	const META_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Urbanist';
	const META_TYPOGRAPHY_DEFAULT_FONT_SIZE = array( 'size' => '15', 'unit' => 'px' );
	const META_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '500';
	const META_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM = 'none';
	const META_TYPOGRAPHY_DEFAULT_FONT_STYLE = 'normal';
	const META_TYPOGRAPHY_DEFAULT_TEXT_DECORATION = 'none';
	const META_TYPOGRAPHY_DEFAULT_LINE_HEIGHT = array( 'size' => '1.60', 'unit' => 'em' );
	const META_TYPOGRAPHY_DEFAULT_LETTER_SPACING = array( 'size' => '0', 'unit' => 'px' );
	const META_TYPOGRAPHY_DEFAULT_WORD_SPACING = array( 'size' => '0', 'unit' => 'px' );

	/* TAXONOMY */
	const TAXONOMY_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Urbanist';
	const TAXONOMY_TYPOGRAPHY_DEFAULT_FONT_SIZE = array( 'size' => '12', 'unit' => 'px' );
	const TAXONOMY_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '600';
	const TAXONOMY_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM = 'uppercase';
	const TAXONOMY_TYPOGRAPHY_DEFAULT_FONT_STYLE = 'normal';
	const TAXONOMY_TYPOGRAPHY_DEFAULT_TEXT_DECORATION = 'none';
	const TAXONOMY_TYPOGRAPHY_DEFAULT_LINE_HEIGHT = array( 'size' => '1.65', 'unit' => 'em' );
	const TAXONOMY_TYPOGRAPHY_DEFAULT_LETTER_SPACING = array( 'size' => '2', 'unit' => 'px' );
	const TAXONOMY_TYPOGRAPHY_DEFAULT_WORD_SPACING = array( 'size' => '0', 'unit' => 'px' );

	/* SMALL */
	const SMALL_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Urbanist';
	const SMALL_TYPOGRAPHY_DEFAULT_FONT_SIZE = array( 'size' => '18', 'unit' => 'px' );
	const SMALL_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '400';
	const SMALL_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM = 'none';
	const SMALL_TYPOGRAPHY_DEFAULT_FONT_STYLE = 'normal';
	const SMALL_TYPOGRAPHY_DEFAULT_TEXT_DECORATION = 'none';
	const SMALL_TYPOGRAPHY_DEFAULT_LINE_HEIGHT = array( 'size' => '1.45', 'unit' => 'em' );
	const SMALL_TYPOGRAPHY_DEFAULT_LETTER_SPACING = array( 'size' => '0', 'unit' => 'px' );
	const SMALL_TYPOGRAPHY_DEFAULT_WORD_SPACING = array( 'size' => '0', 'unit' => 'px' );

	/* H1 */
	const H1_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Quicksand';
	const H1_TYPOGRAPHY_DEFAULT_FONT_SIZE = array( 'size' => '62', 'unit' => 'px' );
	const H1_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '700';
	const H1_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM = 'none';
	const H1_TYPOGRAPHY_DEFAULT_FONT_STYLE = 'normal';
	const H1_TYPOGRAPHY_DEFAULT_TEXT_DECORATION = 'none';
	const H1_TYPOGRAPHY_DEFAULT_LINE_HEIGHT = array( 'size' => '1', 'unit' => 'em' );
	const H1_TYPOGRAPHY_DEFAULT_LETTER_SPACING = array( 'size' => '-2', 'unit' => 'px' );
	const H1_TYPOGRAPHY_DEFAULT_WORD_SPACING = array( 'size' => '0', 'unit' => 'px' );

	/* H2 */
	const H2_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Quicksand';
	const H2_TYPOGRAPHY_DEFAULT_FONT_SIZE = array( 'size' => '54', 'unit' => 'px' );
	const H2_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '700';
	const H2_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM = 'none';
	const H2_TYPOGRAPHY_DEFAULT_FONT_STYLE = 'normal';
	const H2_TYPOGRAPHY_DEFAULT_TEXT_DECORATION = 'none';
	const H2_TYPOGRAPHY_DEFAULT_LINE_HEIGHT = array( 'size' => '1.1', 'unit' => 'em' );
	const H2_TYPOGRAPHY_DEFAULT_LETTER_SPACING = array( 'size' => '-2', 'unit' => 'px' );
	const H2_TYPOGRAPHY_DEFAULT_WORD_SPACING = array( 'size' => '0', 'unit' => 'px' );

	/* H3 */
	const H3_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Quicksand';
	const H3_TYPOGRAPHY_DEFAULT_FONT_SIZE = array( 'size' => '42', 'unit' => 'px' );
	const H3_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '600';
	const H3_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM = 'none';
	const H3_TYPOGRAPHY_DEFAULT_FONT_STYLE = 'normal';
	const H3_TYPOGRAPHY_DEFAULT_TEXT_DECORATION = 'none';
	const H3_TYPOGRAPHY_DEFAULT_LINE_HEIGHT = array( 'size' => '1.2', 'unit' => 'em' );
	const H3_TYPOGRAPHY_DEFAULT_LETTER_SPACING = array( 'size' => '-2', 'unit' => 'px' );
	const H3_TYPOGRAPHY_DEFAULT_WORD_SPACING = array( 'size' => '0', 'unit' => 'px' );

	/* H4 */
	const H4_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Quicksand';
	const H4_TYPOGRAPHY_DEFAULT_FONT_SIZE = array( 'size' => '30', 'unit' => 'px' );
	const H4_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '500';
	const H4_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM = 'none';
	const H4_TYPOGRAPHY_DEFAULT_FONT_STYLE = 'normal';
	const H4_TYPOGRAPHY_DEFAULT_TEXT_DECORATION = 'none';
	const H4_TYPOGRAPHY_DEFAULT_LINE_HEIGHT = array( 'size' => '1.3', 'unit' => 'em' );
	const H4_TYPOGRAPHY_DEFAULT_LETTER_SPACING = array( 'size' => '-1', 'unit' => 'px' );
	const H4_TYPOGRAPHY_DEFAULT_WORD_SPACING = array( 'size' => '0', 'unit' => 'px' );

	/* H5 */
	const H5_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Urbanist';
	const H5_TYPOGRAPHY_DEFAULT_FONT_SIZE = array( 'size' => '24', 'unit' => 'px' );
	const H5_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '500';
	const H5_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM = 'none';
	const H5_TYPOGRAPHY_DEFAULT_FONT_STYLE = 'normal';
	const H5_TYPOGRAPHY_DEFAULT_TEXT_DECORATION = 'none';
	const H5_TYPOGRAPHY_DEFAULT_LINE_HEIGHT = array( 'size' => '1.4', 'unit' => 'em' );
	const H5_TYPOGRAPHY_DEFAULT_LETTER_SPACING = array( 'size' => '0', 'unit' => 'px' );
	const H5_TYPOGRAPHY_DEFAULT_WORD_SPACING = array( 'size' => '0', 'unit' => 'px' );

	/* H6 */
	const H6_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Urbanist';
	const H6_TYPOGRAPHY_DEFAULT_FONT_SIZE = array( 'size' => '18', 'unit' => 'px' );
	const H6_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '500';
	const H6_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM = 'none';
	const H6_TYPOGRAPHY_DEFAULT_FONT_STYLE = 'normal';
	const H6_TYPOGRAPHY_DEFAULT_TEXT_DECORATION = 'none';
	const H6_TYPOGRAPHY_DEFAULT_LINE_HEIGHT = array( 'size' => '1.45', 'unit' => 'em' );
	const H6_TYPOGRAPHY_DEFAULT_LETTER_SPACING = array( 'size' => '0', 'unit' => 'px' );
	const H6_TYPOGRAPHY_DEFAULT_WORD_SPACING = array( 'size' => '0', 'unit' => 'px' );

	/* BUTTON */
	const BUTTON_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Urbanist';
	const BUTTON_TYPOGRAPHY_DEFAULT_FONT_SIZE = array( 'size' => '14', 'unit' => 'px' );
	const BUTTON_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '600';
	const BUTTON_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM = 'uppercase';
	const BUTTON_TYPOGRAPHY_DEFAULT_FONT_STYLE = 'normal';
	const BUTTON_TYPOGRAPHY_DEFAULT_TEXT_DECORATION = 'none';
	const BUTTON_TYPOGRAPHY_DEFAULT_LINE_HEIGHT = array( 'size' => '1.45', 'unit' => 'em' );
	const BUTTON_TYPOGRAPHY_DEFAULT_LETTER_SPACING = array( 'size' => '1', 'unit' => 'px' );
	const BUTTON_TYPOGRAPHY_DEFAULT_WORD_SPACING = array( 'size' => '0', 'unit' => 'px' );

	/* BLOCKQUOTE */
	const BLOCKQUOTE_TYPOGRAPHY_DEFAULT_FONT_FAMILY = 'Quicksand';
	const BLOCKQUOTE_TYPOGRAPHY_DEFAULT_FONT_SIZE = array( 'size' => '32', 'unit' => 'px' );
	const BLOCKQUOTE_TYPOGRAPHY_DEFAULT_FONT_WEIGHT = '500';
	const BLOCKQUOTE_TYPOGRAPHY_DEFAULT_TEXT_TRANSFORM = 'none';
	const BLOCKQUOTE_TYPOGRAPHY_DEFAULT_FONT_STYLE = 'normal';
	const BLOCKQUOTE_TYPOGRAPHY_DEFAULT_TEXT_DECORATION = 'none';
	const BLOCKQUOTE_TYPOGRAPHY_DEFAULT_LINE_HEIGHT = array( 'size' => '1.45', 'unit' => 'em' );
	const BLOCKQUOTE_TYPOGRAPHY_DEFAULT_LETTER_SPACING = array( 'size' => '-1', 'unit' => 'px' );
	const BLOCKQUOTE_TYPOGRAPHY_DEFAULT_WORD_SPACING = array( 'size' => '0', 'unit' => 'px' );

	protected $fonts = array(
		'Urbanist' => array(
			'400',
			'500',
			'600',
		),
		'Quicksand' => array(
			'500',
			'600',
			'700',
		),
	);

		/**
	 * Theme_Config constructor.
	 */
	public function __construct() {
		$this->define_constants();

		add_action( 'cmsmasters_first_setup', array( $this, 'first_setup_actions' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_default_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_default_assets' ) );
		add_filter( 'cmsmasters_theme_get_post_meta_author_prefix', function ( $prefix ) {
			return __( 'by ', 'pediatric-clinic' );
		} );
	}

		/**
	 * Define constants.
	 */
	public function define_constants() {
		/**
		 * Product key.
		 */
		define( 'CMSMASTERS_THEME_PRODUCT_KEY', '7065646961747269632d636c696e6963' );

		/**
		 * Import type.
		 *
		 * demos - import content and all data from demo; apply all data from demo;
		 * kit - import content and all data from demo; apply only kit from demo;
		 * only_kit - import content and all data from main demo, and kit from current demo; apply only kit from demo;
		 */
		define( 'CMSMASTERS_THEME_IMPORT_TYPE', 'only_kit' );

		/**
		 * Marketplace.
		 *
		 * themeforest - theme for themeforest
		 * envato-elements - theme for envato-elements
		 * templatemonster - theme for templatemonster
		 */
		define( 'CMSMASTERS_THEME_MARKETPLACE', 'envato-elements' );
	}

	/**
	 * Actions on first setup.
	 */
	public function first_setup_actions() {
		$cpt_support = get_option( 'elementor_cpt_support', array( 'post', 'page', 'e-landing-page' ) );

		if ( is_array( $cpt_support ) ) {
			if ( ! in_array( 'product', $cpt_support ) ) {
				$cpt_support[] = 'product';
			}

			if ( ! in_array( 'cmsms_profile', $cpt_support ) ) {
				$cpt_support[] = 'cmsms_profile';
			}

			if ( ! in_array( 'services', $cpt_support ) ) {
				$cpt_support[] = 'services';
			}

			if ( ! in_array( 'projects', $cpt_support ) ) {
				$cpt_support[] = 'projects';
			}
		}

		update_option( 'elementor_cpt_support', $cpt_support );
	}

		/**
	 * Enqueue default assets.
	 */
	public function enqueue_default_assets() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			wp_enqueue_style(
				'pediatric-clinic-default-fonts',
				$this->get_default_fonts(),
				array(),
				'1.0.0',
				'screen'
			);
		}

		if ( did_action( 'elementor/loaded' ) && ! class_exists( 'Cmsmasters_Elementor_Addon' ) ) {
			foreach ( $this->fonts as $font => $weights ) {
				$font = str_replace( ' ', '-', strtolower( $font ) ) . '-local';

				wp_enqueue_style(
					'pediatric-clinic-font-' . $font,
					get_template_directory_uri() . '/theme-config/assets/fonts/' . $font . '/stylesheet.css',
					array(),
					'1.0.0',
					'screen'
				);
			}
		}

		if ( ! did_action( 'elementor/loaded' ) || ! class_exists( 'Cmsmasters_Elementor_Addon' ) ) {
			$default_styles = '.wp-block-widget-area h2.wp-block-heading,
			.widget h2 {
				font-family: var(--cmsmasters-h6-font-family);
				font-weight: var(--cmsmasters-h6-font-weight);
				font-style: var(--cmsmasters-h6-font-style);
				text-transform: var(--cmsmasters-h6-text-transform);
				text-decoration: var(--cmsmasters-h6-text-decoration);
				font-size: var(--cmsmasters-h6-font-size);
				line-height: var(--cmsmasters-h6-line-height);
				letter-spacing: var(--cmsmasters-h6-letter-spacing);
				word-spacing: var(--cmsmasters-h6-word-spacing);
			}

			.wp-block-button .wp-block-button__link {
				border-radius: 50px;
			}

			@media only screen and (max-width: 767px) {
				:root {
					--e-global-typography-text-font-size: 17px;
					--e-global-typography-meta-font-size: 15px;
					--e-global-typography-taxonomy-font-size: 12px;
					--e-global-typography-small-font-size: 15px;
					--e-global-typography-h1-font-size: 52px;
					--e-global-typography-h2-font-size: 40px;
					--e-global-typography-h3-font-size: 30px;
					--e-global-typography-h4-font-size: 24px;
					--e-global-typography-h5-font-size: 20px;
					--e-global-typography-h6-font-size: 14px;
					--e-global-typography-button-font-size: 14px;
					--e-global-typography-blockquote-font-size: 22px;
				}

				body {
					--cmsmasters-archive-compact-media-width: 100%;
					--cmsmasters-archive-media-box-margin-right: 0;
					--cmsmasters-archive-media-box-margin-bottom: 40px;
					--cmsmasters-search-compact-media-width: 100%;
					--cmsmasters-search-media-box-margin-right: 0;
					--cmsmasters-search-media-box-margin-bottom: 40px;
				}
			}';

			wp_add_inline_style( 'pediatric-clinic-root-style', $default_styles );
		}
	}

	/**
	 * Get default fonts.
	 */
	public function get_default_fonts() {
		$families = array();

		foreach ( $this->fonts as $font => $weights ) {
			$families[] = str_replace( ' ', '+', $font ) . '%3A' . implode( '%2C', $weights );
		}

		return 'https://fonts.googleapis.com/css?family=' . implode( '%7C', $families );
	}
}
