<?php
/**
 * The template for displaying singular post-types: pages and user-defined custom post types.
 */

use PediatricClinicSpace\TemplateFunctions\General_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

while ( have_posts() ) :
	the_post();

	the_content();

	echo General_Elements::get_sub_pagination();

	comments_template();
endwhile;
