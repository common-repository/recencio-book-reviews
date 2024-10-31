<?php

/**
 * Registers the stylesheet for the default template.
 *
 * @uses wp_enqueue_style
 * @return void
 */

add_action( 'wp_enqueue_scripts', 'rcno_metamor_styles' );
add_action( 'wp_enqueue_scripts', 'rcno_metamor_scripts' );

function rcno_metamor_styles() {
	wp_enqueue_style( 'rcno-metamor-style', plugin_dir_url( __FILE__ ) . 'metamor-style.css', array(), '1.0.0', 'all' );
}

function rcno_metamor_scripts() {
	wp_enqueue_script( 'rcno-metamor-script', plugin_dir_url( __FILE__ ) . 'metamor-script.js', array( 'jquery' ), '1.0.0', true );
}
