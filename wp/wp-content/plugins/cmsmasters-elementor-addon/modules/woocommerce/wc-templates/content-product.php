<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

use CmsmastersElementor\Modules\Woocommerce\Module as WooModule;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


global $product;

$template_id = wc_get_loop_prop( WooModule::CONTROL_TEMPLATE_NAME );

// Ensure visibility.
if ( empty( $template_id ) || empty( $product ) || ! $product->is_visible() ) {
	return;
}

?>
<li <?php wc_product_class( '', $product ); ?>>
	<?php
	if ( ! Utils::check_template( $template_id ) ) {
		if ( is_admin() ) {
			Utils::render_alert( esc_html__( 'Please choose template!', 'cmsmasters-elementor' ) );
		}
	} else {
		$addon = Plugin::instance();
	
		echo $addon->frontend->get_widget_template( $template_id, false, true );
	}
	?>
</li>
