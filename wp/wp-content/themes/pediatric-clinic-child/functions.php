<?php
/**
 * Theme functions and definitions.
 */
function pediatricclinic_child_enqueue_styles() {
wp_enqueue_style( 'pediatric-clinic-child-style',
get_stylesheet_directory_uri() . '/style.css',
array(),
wp_get_theme()->get('Version')
);
}

add_action( 'wp_enqueue_scripts', 'pediatricclinic_child_enqueue_styles', 11 );