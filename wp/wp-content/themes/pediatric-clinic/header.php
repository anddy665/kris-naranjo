<?php
/**
 * The template for displaying the header.
 *
 * This is the template that displays all of the <head> section, opens the <body> tag and adds the site's header
 */

use PediatricClinicSpace\TemplateFunctions\General_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<!doctype html>
<html <?php language_attributes(); General_Elements::html_tag_class(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>
<body id="cmsmasters_body" <?php body_class(); ?>>
<?php
wp_body_open();

if (
	( ! function_exists( 'cmsmasters_template_do_location' ) || ! cmsmasters_template_do_location( 'header' ) ) &&
	( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) )
) {
	get_template_part( 'template-parts/header' );

	get_template_part( 'template-parts/heading' );
}
