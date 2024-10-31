<?php

/**
 * Class Rcno_Reviews_Get_Templates
 */
class Rcno_Reviews_Get_Templates {

	public $layouts = array();

	public function __construct() {

		$this->get_layouts_list();
	}


	/**
	 * Create a list of available layouts locally and globally.
	 */
	public function get_layouts_list() {

		// First create a list of all globally available layouts.
		$dir_name = RCNO_PLUGIN_PATH . 'public/templates/';
		$this->add_layout_to_list( $dir_name );

		// Then also add layouts available locally from the current theme (if applicable).
		$dir_name = get_stylesheet_directory() . '/rcno_templates/';
		$this->add_layout_to_list( $dir_name );
	}

	/**
	 * @param string $dir_name
	 */
	public function add_layout_to_list( $dir_name ) {
		if ( is_dir( $dir_name ) ) {
			if ( $handle = opendir( $dir_name ) ) {
				// Walk through all folders in that directory:
				while ( false !== ( $file = readdir( $handle ) ) ) {

					if ( $file !== '.' && $file !== '..' && $file !== '.svn' && $file !== '.git' && $file !== '.listing' ) {

						if ( false !== stripos( $dir_name, 'plugin' ) ) {
							$base_url = RCNO_PLUGIN_URI . 'public/templates/' . $file;
							$local    = false;
						} else {
							// $base_url = get_template_directory_uri() . '/rcno_templates/' . $file;
							$base_url = get_theme_file_uri( '/rcno_templates/' . $file );
							$local    = true;
						}

						// If we are not working with a directory jump out of this iteration
						if ( ! is_dir( $dir_name . $file ) ) {
							continue;
						}

						$this->layouts[ $file ] = array(
							'path'  => $dir_name . $file,
							'url'   => $base_url,
							'local' => $local
						);

						$this->get_layout_meta( $dir_name, $file );
					}
				}
			}
		}
	}

	/**
	 * Adds the template metadata by reading the `review.php` file
	 *
	 * @see http://stackoverflow.com/questions/11504541/get-comments-in-a-php-file
	 *
	 * @param string $dir_name
	 * @param string $file
	 *
	 * @return void
	 */
	public function get_layout_meta( $dir_name, $file ) {

		$params   = array();
		$filename = $dir_name . $file . '/review.php';

		$docComments    = array_filter( token_get_all( file_get_contents( $filename ) ), 'rcno_file_comment' );
		$fileDocComment = array_shift( $docComments );

		$regexp = "/.*\:.*\n/";
		preg_match_all( $regexp, $fileDocComment[1], $matches );

		foreach ( $matches[0] as $match ) {
			$param                       = explode( ': ', $match );
			$params[ trim( $param[0] ) ] = trim( $param[1] );
		}

		$this->layouts[ $file ]['description'] = isset( $params['Description'] ) ? $params['Description'] : '';
		$this->layouts[ $file ]['title']       = isset( $params['Layout Name'] ) ? $params['Layout Name'] : '';
		$this->layouts[ $file ]['author']      = isset( $params['Author'] ) ? $params['Author'] : '';
		$this->layouts[ $file ]['author_mail'] = isset( $params['Author Mail'] ) ? $params['Author Mail'] : '';
		$this->layouts[ $file ]['author_url']  = isset( $params['Author URL'] ) ? $params['Author URL'] : '';
		$this->layouts[ $file ]['version']     = isset( $params['Version'] ) ? $params['Version'] : '';

		if ( file_exists( $dir_name . $file . '/logo.png' ) ) {
			$this->layouts[ $file ]['logo'] = $this->layouts[ $file ]['url'] . '/logo.png';
		} else {
			$this->layouts[ $file ]['logo'] = '';
		}

		if ( file_exists( $dir_name . $file . '/screenshot.png' ) ) {
			$this->layouts[ $file ]['screenshot'] = $this->layouts[ $file ]['url'] . '/screenshot.png';
		} else {
			$this->layouts[ $file ]['screenshot'] = '';
		}

	}

}

/**
 * @param $entry
 *
 * @return bool
 */
function rcno_file_comment( $entry ) {
	return $entry[0] === T_COMMENT;
}

/**
 * @return array
 */
function layout_list() {

	$templates = new Rcno_Reviews_Get_Templates();
	$layouts   = $templates->layouts;
	$list      = array();

	foreach ( $layouts as $layout ) {
		$name          = strtolower( str_replace( ' ', '_', $layout['title'] ) );
		$list[ $name ] = array(
			'screenshot' => $layout['screenshot'],
			'title'      => $layout['title'],
			'author'     => $layout['author'],
			'version'    => $layout['version'],
		);
	}

	return $list;
}
