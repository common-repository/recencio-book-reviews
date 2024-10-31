<?php

/**
 * Creates and handles the functionalities of the metaboxes attached to the 'Author' taxonomy.
 *
 * This class adds a 'Author's URL' field to the rcno_author custom taxonomy add and it page.
 * It makes use of the 'update_term_meta' function available in WP since version 4.4.
 *
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin
 * @author     wzyMedia <wzy@outlook.com>
 */
class Rcno_Author_Taxonomy_Metabox {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $plugin_name     The ID of this plugin.
	 * @param   string $version         The current version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Add a metabox for the author's URL information to the rcno_author taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @param object $taxonomy	The current taxonomy object.
	 * @return void
	 */
	public function rcno_author_taxonomy_metabox( $taxonomy ) {

		include __DIR__ . '/views/rcno-author-taxonomy-metabox.php';
	}

	/**
	 * Check the presence of, sanitizes then saves the author taxonomy URL field.
	 *
	 * @since 1.0.0
	 *
	 * @uses  update_term_meta()
	 * @uses  wp_verify_nonce()
	 * @uses  sanitize_text_field()
	 *
	 * @param int $term_id 	The ID of the new term being created or edited.
	 *
	 * @return void
	 */
	public function rcno_save_author_taxonomy_metadata( $term_id ) {

		if ( isset( $_POST['rcno_author_taxonomy_url'] ) && wp_verify_nonce( $_POST['rcno_aut_tax_url_nonce'], 'rcno_save_author_taxonomy' ) ) {

			$aut_tax_url = sanitize_text_field( $_POST['rcno_author_taxonomy_url'] );
			update_term_meta( $term_id, 'rcno_author_taxonomy_url', $aut_tax_url );
		}
	}

}
