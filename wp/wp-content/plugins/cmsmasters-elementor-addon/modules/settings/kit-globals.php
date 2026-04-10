<?php
namespace CmsmastersElementor\Modules\Settings;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon kit globals class.
 *
 * Modifies Elementor default kit global settings.
 *
 * @since 1.0.0
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

}
