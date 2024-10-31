<?php

/**
 * Registers the stylesheet for the 'Basic' template.
 *
 * @uses wp_enqueue_style
 * @return void
 */

add_action( 'wp_enqueue_scripts', 'rcno_basic_styles' );
add_action( 'wp_enqueue_scripts', 'rcno_basic_scripts' );

function rcno_basic_styles() {
	wp_enqueue_style( 'rcno-basic-style', plugin_dir_url( __FILE__ ) . 'basic-style.css', array(), '1.0.0', 'all' );
}

function rcno_basic_scripts() {
	wp_enqueue_script( 'rcno-basic-script', plugin_dir_url( __FILE__ ) . 'basic-script.js', array( 'jquery' ), '1.0.0', true );
}
