<?php

/**
 * Registers the stylesheet for the default template.
 *
 * @uses wp_enqueue_style
 * @return void
 */

add_action( 'wp_enqueue_scripts', 'rcno_default_styles' );
add_action( 'wp_enqueue_scripts', 'rcno_default_scripts' );

function rcno_default_styles() {
	wp_enqueue_style( 'rcno-default-style', plugin_dir_url( __FILE__ ) . 'default-style.css', array(), '1.0.0', 'all' );
}

function rcno_default_scripts() {
	wp_enqueue_script( 'rcno-default-script', plugin_dir_url( __FILE__ ) . 'default-script.js', array( 'jquery' ), '1.0.0', true );
}
