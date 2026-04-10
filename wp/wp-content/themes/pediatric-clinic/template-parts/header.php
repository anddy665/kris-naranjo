<?php
use PediatricClinicSpace\Core\Utils\Utils;
use PediatricClinicSpace\TemplateFunctions\Header_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$header_mid_add_content_elements = Utils::get_kit_option( 'cmsmasters_header_mid_add_content_elements', array() );
$header_bot_add_content_elements = Utils::get_kit_option( 'cmsmasters_header_bot_add_content_elements', array() );

if (
	in_array( 'search_button', $header_mid_add_content_elements, true ) ||
	in_array( 'search_button', $header_bot_add_content_elements, true )
) {
	echo Header_Elements::get_header_search_form();
}
?>
<header id="header" class="cmsmasters_header">
<?php
if ( 'yes' === Utils::get_kit_option( 'cmsmasters_header_top_visibility', 'no' ) ) {
	get_template_part( 'template-parts/header-top' );
}

get_template_part( 'template-parts/header-mid' );

if ( 'yes' === Utils::get_kit_option( 'cmsmasters_header_bot_visibility', 'yes' ) ) {
	get_template_part( 'template-parts/header-bot' );
}
?>
</header>
