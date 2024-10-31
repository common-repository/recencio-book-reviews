( function( $ ) {
	'use strict';

	$( function() {

		$( '.rcno-isbn-fetch.good-reads' ).on( 'click', function( e ) {
			e.preventDefault();

			$.sanitize = function( input ) {
				return input
				.replace( /<script[^>]*?>.*?<\/script>/gi, '' )
				.replace( /<[\/\!]*?[^<>]*?>/gi, '' )
				.replace( /<style[^>]*?>.*?<\/style>/gi, '' )
				.replace( /<![\s\S]*?--[ \t\n\r]*>/gi, '' )
				.trim();

			};

			$.upCase = function( str ) {
				if ( str.length ) {
					return str[0].toUpperCase() + str.slice( 1 ).toLowerCase();
				} else {
					return '';
				}
			};

			var review_title = $( '#title' );

			var title = $( '#rcno_book_title' );
			var author = $( '#new-tag-rcno_author' );
			var genre = $( '#new-tag-rcno_genre' );
			var publisher = $( '#new-tag-rcno_publisher' );
			var pub_date = $( '#rcno_book_pub_date' );
			var pub_fmt = $( '#rcno_book_pub_format' );
			var pub_edt = $( '#rcno_book_pub_edition' );
			var series = $( '#new-tag-rcno_series' );
			var p_count = $( '#rcno_book_page_count' );
			var s_number = $( '#rcno_book_series_number' );
			var gr_rvw = $( '#rcno_book_gr_review' );
			var gr_id = $( '#rcno_book_gr_id' );
			var isbn13 = $( '#rcno_book_isbn13' );
			var asin = $( '#rcno_book_asin' );
			var gr_url = $( '#rcno_book_gr_url' );

			var gr_cover_url = $( '#gr-book-cover-url' );

			var ajx_gif = $( '.rcno-ajax-loading' );
			var book_isbn = $( '#rcno_book_isbn' ).val().trim();
			var api_key = gr_options.api_key.trim();
			// https://www.goodreads.com/book/isbn/0441172717?key=################
			var url = 'https://www.goodreads.com/book/isbn/' + book_isbn + '?key=' + api_key;

			$.ajax( {
				url: my_script_vars.ajaxURL,
				type: 'POST',
				data: {
					action: 'rcno_gr_remote_get',
					gr_url: url,
                    gr_nonce: my_script_vars.rcno_gr_remote_get_nonce
				},
				beforeSend: function() {
					ajx_gif.show();
				},
				complete: function() {
					ajx_gif.hide();
				},
				success: function( res ) {
                    var json = $.xml2json(res.data.body);
                    console.log( json.GoodreadsResponse.book );
					var book = json.GoodreadsResponse.book;

					if ( book.error ) {

						$( '.book-isbn-metabox-error' ).show();

					} else {

						review_title.val(
							book.title
						);
						$('#title-prompt-text').addClass('screen-reader-text');

						if( typeof book.work.original_title === 'object') {
							title.val(
								book.title
							);
						} else {
							title.val(
								book.work.original_title
							);
						}

						if ( book.description ) {
							if (typeof tinymce !== 'undefined') {
								tinymce.get( 'rcno_book_description' ).setContent(
									book.description
								);
							} else {
								$('#rcno_book_description').html(book.description);
							}
						}


						author.val(
							book.authors.author.name
						);

						if ( $.isArray(	book.popular_shelves.shelf ) ) {
							genre.val(
								$.upCase(
									book.popular_shelves.shelf["0"].$.name )
							);
						}

						if ( typeof book.series_works === 'object' ) {
							if ( $.isArray(
									book.series_works.series_work ) ) {
								series.val(
									$.sanitize(
										book.series_works.series_work['0'].series.title )
								);
							} else {
								series.val(
									$.sanitize(
										book.series_works.series_work.series.title )
								);
							}
						} else {
							series.val( '' );
						}

						publisher.val(
							book.publisher
						);

						let pubDate = [];
						pubDate.push(parseInt(book.publication_month));
						pubDate.push(parseInt(book.publication_day));
						pubDate.push(parseInt(book.publication_year));
						pubDate = pubDate.filter(function(item) {return ! isNaN(item);});
						pub_date.val( pubDate.join('/') );

						pub_fmt.val(
							book.format
						);

						pub_edt.val(
							book.edition_information
						);

						p_count.val(
							book.num_pages
						);

						if( typeof book.series_works === 'object' ) {
							s_number.val(
								book.series_works.series_work.user_position
							);
						}

						gr_rvw.val(
							book.average_rating
						);

						gr_id.val(
							book.id
						);

						isbn13.val(
							book.isbn13
						);

						asin.val(
							book.asin
						);

						gr_url.val(
							book.url
						);

						if ( -1 === book.image_url.indexOf('nophoto') ) {

							gr_cover_url.val( book.image_url.replace('_SX98_', '_SX300_') );

							// After that, set the properties of the image and display it.
							$( '#rcno-reviews-book-cover-container' )
								.children( 'img' )
								.attr( 'src', book.image_url.replace('_SX98_', '_SX300_') )
								.show()
								.parent()
								.removeClass( 'hidden' );

							// Next, hide the anchor responsible for allowing the user to select an image
							$( '#rcno-add-book-cover' ).hide();
							$( '#rcno-remove-book-cover' ).parent().removeClass( 'hidden' );
						}



					}

				}
			} );

		} );

	} );

} )( jQuery );
