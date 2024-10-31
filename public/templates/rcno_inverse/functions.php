<?php

/**
 * Registers the stylesheet for the default template.
 *
 * @uses wp_enqueue_style
 * @return void
 */

add_action( 'wp_enqueue_scripts', 'rcno_inverse_styles' );
add_action( 'wp_enqueue_scripts', 'rcno_inverse_scripts' );

function rcno_inverse_styles() {
	wp_enqueue_style( 'rcno-inverse-style', plugin_dir_url( __FILE__ ) . 'inverse-style.css', array(), '1.0.0', 'all' );
}

function rcno_inverse_scripts() {
	wp_enqueue_script( 'rcno-inverse-script', plugin_dir_url( __FILE__ ) . 'inverse-script.js', array( 'jquery' ), '1.0.0', true );
}
