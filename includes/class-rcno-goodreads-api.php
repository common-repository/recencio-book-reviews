<?php

/**
 * GoodReads
 * PHP wrapper to communicate with GoodReads API.
 *
 * @package Nicat\GoodReads
 */
class Rcno_GoodReads_API {

	/**
	 * The Goodreads API key.
	 *
	 * @var string $key GoodReads API key
	 */
	protected $key;

	/**
	 * Main Api URL
	 */
	const DOMAIN = 'https://www.goodreads.com/';

	const AUTHORSHOW = 'author/show/';
	const AUTHORBOOKS = 'author/list/';
	const AUTHORSEARCH = 'api/author_url/';
	const ISBN = 'book/isbn/';
	const BOOKSHOW = 'book/show/';
	const SEARCH = 'search/index.xml';
	const LISTGROUPS = 'group/list/';
	const GROUPMEMBERS = 'group/members/';
	const FINDGROUP = 'group/search.xml';
	const GROUPSHOW = 'group/show/';
	const REVIEWS = 'review/show.xml';
	const REVIEWOFUSER = 'review/show_by_user_and_book.xml';
	const SERIESBYAUTHOR = 'series/list/';
	const USERSHOW = 'user/show/';


	/**
	 * GoodReads constructor.
	 *
	 * @param string $key
	 */
	public function __construct() {
		$this->key = Rcno_Reviews_Option::get_option( 'rcno_goodreads_key', '' );
	}


	public function rcno_enqueue_gr_scripts( $hook ) {

		// Disables the enqueuing of the Goodreads script on review edit screen.
		if ( true === (bool) Rcno_Reviews_Option::get_option( 'rcno_enable_goodreads' )
		     && 'good-reads' === Rcno_Reviews_Option::get_option( 'rcno_external_book_api' )
		) {

			global $post;

			if ( 'post-new.php' === $hook || 'post.php' === $hook ) {
				if ( 'rcno_review' === $post->post_type ) {
					wp_enqueue_script( 'goodreads-script', plugin_dir_url( __FILE__ ) . '../admin/js/rcno-goodreads-api.js', array( 'jquery' ), '1.0.0', false );
					wp_localize_script( 'goodreads-script', 'gr_options', array(
						'api_key' => $this->key,
					) );
				}
			}
		}

	}


	/**
	 * Generate URL for Request
	 *
	 * @param      $url
	 * @param null $params
	 *
	 * @return string
	 */
	protected function generateURL( $url, $params = null ) {
		$url       = self::DOMAIN . $url;
		$httpQuery = "?format=xml&key=" . $this->key;

		if ( is_array( $params ) ) {
			$query = ( ( ! empty( $params ) ) ? http_build_query( $params, '', '&' ) : '' );
			$url   .= $httpQuery . "&" . $query;
		} else {
			$query = rawurlencode( $params );
			$url   .= $query . $httpQuery;
		}

		return $url;
	}

	/**
	 * Request via CURL to API URL
	 *
	 * @param string $url
	 *
	 * @return bool|null|string
	 * @throws Exception
	 */
	protected function curlRequest( $url ) {

		try {
			$response = wp_remote_get( $url );

			if ( is_wp_error( $response ) ) {
				return new WP_Error( 'goodreads_api_fetch_failed', __( 'There was an error fetch data from GoodReads' ) );
			}

			$response = $response[ 'body' ];

			return $response;

		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Convert XML to Object
	 * Used SimpleXMLElement
	 *
	 * @param string $xml
	 *
	 * @return \SimpleXMLElement
	 */
	public function parseXML( $xml ) {
		return simplexml_load_string( $xml );
	}

	/**
	 * Get object by url and extra params
	 *
	 * @param string $url
	 * @param array  $params
	 * @param bool   $append
	 * @param bool   $raw
	 *
	 * @return \SimpleXMLElement|string
	 */
	protected function getData( $url, $params = array(), $append = false, $raw = true ) {
		/* Generate Url with http query */
		$url = $this->generateURL( $url, $params, $append );

		/* Get response from api */
		$xml = $this->curlRequest( $url );

		if ( $raw ) {
			return $xml;
		}

		/* Parse XML to Object */
		$parsed = $this->parseXML( $xml );

		return $parsed;
	}

	/**
	 * Get Author ID By Author Name
	 *
	 * @param int $name Author Name
	 *
	 * @return int|null
	 */
	public function authorIDByName( $name ) {
		$get = $this->getData( static::AUTHORSEARCH, $name );

		return $get->author ? (int) $get->author->attributes()[ 0 ] : null;
	}

	/**
	 * Show Author By ID
	 *
	 * @param int $id Author ID
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function authorByID( $id ) {
		$get = $this->getData( static::AUTHORSHOW, $id );

		return $get ? $get->author : $get;
	}

	/**
	 * Author Books
	 *
	 * @param     $id
	 * @param int $page Page number. Default is 1
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function authorBooks( $id, $page = 1 ) {
		$params = [
			'id'   => $id,
			'page' => $page,
		];
		$get    = $this->getData( self::AUTHORBOOKS, $params );

		return $get ? $get->author : $get;
	}

	/**
	 * Get Author By Name
	 *
	 * @param string $name Author Name
	 *
	 * @return null|\SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function authorByName( $name ) {
		/* Find author id */
		$id = $this->authorIDByName( $name );

		if ( ! $id ) {
			return null;
		}

		/* Get Author by ID */
		$author = $this->authorByID( $id );

		return $author;
	}

	/**
	 * Get Book By ID
	 *
	 * @param int|string $id Book id
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function book( $id ) {
		$get = $this->getData( self::BOOKSHOW . $id . ".xml" );

		return $get ? $get->book : $get;
	}

	/**
	 * Get Book By ISBN
	 *
	 * @param int|string $id ISBN number.
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function bookByISBN( $id, $raw = true ) {
		$get = $this->getData( self::ISBN, $id, $raw );

		return is_object( $get ) ? $get->book : $get;
	}

	/**
	 * Search Book for All filter
	 *
	 * @param string $name
	 * @param array  $searchParams
	 * @param int    $page
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function searchBook( $name, $searchParams = [], $page = 1 ) {
		$params = [
			'q'    => $name,
			'page' => $page,
		];

		if ( ! empty( $searchParams ) ) {
			$params = array_merge( $params, $searchParams );
		}

		$get = $this->getData( static::SEARCH, $params );

		if ( $get ) {
			$search = $get->search;
			foreach ( $search->children() as $child ) {
				if ( strpos( $child->getName(), '-' ) !== false ) {
					$key            = str_replace( '-', '_', $child->getName() );
					$search->{$key} = $child;
					echo( $search->{$child->getName()} );
				}
			}
		}

		return $get ? $search : $get;
	}

	/**
	 * Search Books by Title
	 *
	 * @param string $name Book Name
	 * @param int    $page
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function searchBookByName( $name, $page = 1 ) {
		$params = [
			'search' => [
				'field' => 'title',
			],
		];

		return $this->searchBook( $name, $params, $page );
	}

	/**
	 * Search Books By Author Name
	 *
	 * @param string $title Author Name
	 * @param int    $page  Page number. Default is 1
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function searchBookByAuthorName( $title, $page = 1 ) {
		$params = [
			'search' => [
				'field' => 'author',
			],
		];

		return $this->searchBook( $title, $params, $page );
	}

	/**
	 * Get User groups
	 *
	 * @param  int   $id
	 * @param string $sort One of 'my_activity', 'members', 'last_activity', 'title' ('members' will sort by number
	 *                     of members in the group)
	 * @param int    $page
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function groupsOfUser( $id, $sort = 'members', $page = 1 ) {
		$params = [
			'sort' => $sort,
			'page' => $page,
		];

		$get = $this->getData( self::LISTGROUPS . $id . '.xml', $params );

		return $get ? $get->groups : $get;
	}

	/**
	 * Get Group Members
	 *
	 * @param int         $id   Group ID
	 * @param string|bool $search
	 * @param string|bool $sort One of 'last_online', 'num_comments', 'date_joined', 'num_books', 'first_name'
	 * @param int         $page Page number. Default is 1
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function groupMembers( $id, $search = false, $sort = false, $page = 1 ) {
		$params = [
			'page' => $page,
		];

		if ( $search ) {
			$params[ 'search' ] = $search;
		}

		if ( $sort ) {
			$params[ 'sort' ] = $sort;
		}

		$get = $this->getData( self::GROUPMEMBERS . $id . '.xml', $params );

		return $get ? $get->group_users : $get;
	}

	/**
	 * Find Group
	 *
	 * @param string $name Group name
	 * @param int    $page Page number. Default is 1
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function findGroup( $name, $page = 1 ) {
		$params = [
			'q'    => $name,
			'page' => $page,
		];

		$get = $this->getData( self::FINDGROUP, $params );

		return $get ? $get->groups : $get;
	}

	/**
	 * Get Information about group by ID
	 *
	 * @param int    $id   Group ID
	 * @param string $sort Field to sort topics by. One of 'comments_count', 'title', 'updated_at', 'views'
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function group( $id, $sort = 'title' ) {
		$params = [
			'sort' => $sort,
		];

		$get = $this->getData( self::GROUPSHOW . $id . 'xml', $params );

		return $get ? $get->group : $get;
	}

	/**
	 * Get details of Review by ID
	 *
	 * @param int $id Review ID
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function review( $id ) {
		$params = [
			'id' => $id,
		];
		$get    = $this->getData( self::REVIEWS, $params );

		return $get ? $get->review : $get;
	}

	/**
	 * User review on given Book
	 *
	 * @param int $userId User ID
	 * @param int $bookId Book ID
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function userReviewByBook( $userId, $bookId ) {
		$params = [
			'user_id' => $userId,
			'book_id' => $bookId,
		];
		$get    = $this->getData( self::REVIEWOFUSER, $params );

		return $get ? $get->review : $get;
	}

	/**
	 * Get Series of Author by ID
	 *
	 * @param int $id Author ID
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function seriesByAuthor( $id ) {
		$get = $this->getData( self::SERIESBYAUTHOR . $id . '.xml' );

		return $get ? $get->series_works : $get;
	}

	/**
	 * User information by User ID
	 *
	 * @param integer $id User id
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function userInfoByID( $id ) {
		$params = [
			'id' => $id,
		];
		$get    = $this->getData( self::USERSHOW, $params );

		return $get ? $get->user : $get;
	}

	/**
	 * User information by Username
	 *
	 * @param string $username Username
	 *
	 * @return \SimpleXMLElement|\SimpleXMLElement[]
	 */
	public function userInfoByUsername( $username ) {
		$params = [
			'username' => $username,
		];
		$get    = $this->getData( self::USERSHOW, $params );

		return $get ? $get->user : $get;
	}

	public function gr_ajax_save_post_meta() {

		$success   = false;
		$review_id = (int) $_POST[ 'review_id' ];
		$gr_isbn   = (int) $_POST[ 'gr_isbn' ];

		$book           = $this->bookByISBN( $gr_isbn );
		$gr_description = (string) $book->description;

		if ( '' === $gr_description ) {
			return false;
		}

		if ( update_post_meta( $review_id, 'rcno_book_description', strip_tags( $gr_description ) ) ) {
			$success = true;
		}

		if ( update_post_meta( $review_id, 'rcno_book_isbn', $gr_isbn ) ) {
			$success = true;
		}

		if ( $success ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}

	}
	}
