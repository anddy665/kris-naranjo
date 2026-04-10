<?php
/**
 * The template for displaying the footer.
 *
 * Contains the body & html closing tags.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if (
	( ! function_exists( 'cmsmasters_template_do_location' ) || ! cmsmasters_template_do_location( 'footer' ) ) &&
	( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) )
) {
	get_template_part( 'template-parts/footer-widgets' );
	get_template_part( 'template-parts/footer' );
}

echo '<span class="cmsmasters-responsive-width"></span>';

wp_footer();
?>
</body>
</html>
