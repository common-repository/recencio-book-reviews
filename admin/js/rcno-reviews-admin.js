( function( $ ) {
	'use strict';

	$( function() {

		// Settings page upload button
		$( '.rcno_reviews_settings_upload_button.rcno_default_cover' )
		.on( 'click', function( e ) {
			e.preventDefault();

			var custom_uploader = wp.media( {
				title: 'Custom File',
				button: {
					text: 'Upload File'
				},
				multiple: false  // Set this to true to allow multiple files to be selected
			} )
			.on( 'select', function() {
				var attachment = custom_uploader.state()
				.get( 'selection' )
				.first()
				.toJSON();
				$( '.rcno_reviews_upload_field' ).val( attachment.url );

			} )
			.open();
		} );

		// Settings page upload button
		$( '#rcno_settings_export' ).on( 'click', function( e ) {
			e.preventDefault();
			$.ajax( {
				type: 'post',
				url: my_script_vars.ajaxURL,
				data: {
					action: 'rcno_settings_export',
					settings_download_nonce: my_script_vars.rcno_settings_download_nonce
				}
			} )
			.done( function( response, status, xhr ) {
				var filename = 'rcno-reviews-settings-' +
					(new Date()).toISOString().replace(/[^0-9]/g, "").slice(0,15) +
					'.json';

				var type = xhr.getResponseHeader( 'Content-Type' );
				var blob = new Blob( [JSON.stringify( response )],
					{type: type} );

				if ( typeof window.navigator.msSaveBlob !== 'undefined' ) {
					window.navigator.msSaveBlob( blob, filename );
				} else {
					var URL = window.URL || window.webkitURL;
					var downloadUrl = URL.createObjectURL( blob );

					if ( filename ) {
						// use HTML5 a[download] attribute to specify filename
						var a = document.createElement( 'a' );
						// safari doesn't support this yet
						if ( typeof a.download === 'undefined' ) {
							window.location = downloadUrl;
						} else {
							a.href = downloadUrl;
							a.download = filename;
							document.body.appendChild( a );
							a.click();
						}
					} else {
						window.location = downloadUrl;
					}

					setTimeout(
						function() { URL.revokeObjectURL( downloadUrl ); },
						100 ); // cleanup
				}
			} )
			.error( function( data ) {
				console.log( data.responseText );
			} );

		} );

		// Settings page up load button
		$( '#rcno_settings_import' ).on( 'change', function( e ) {
			e.preventDefault();

			var file = e.target.files[0];
			var ext = file.name.match( /\.[0-9a-z]+$/i );
			var reader = new FileReader();
			var file_data;

			if ( ext[0] === '.json' ) {
				reader.readAsText( file );
				reader.onload = function() {
					file_data = reader.result;

					$.ajax( {
						type: 'post',
						url: my_script_vars.ajaxURL,
						data: {
							action: 'rcno_settings_import',
							settings_import_nonce: my_script_vars.rcno_settings_import_nonce,
							file_data: file_data
						}
					} )
					.success( function( data ) {
						alert( 'Settings restored, please refresh your browser page.' );
						console.log( data );
					} )
					.error( function( data ) {
						alert( 'Settings not restored. Required information is missing from uploaded file.' );
						console.log( data.responseText );
					} );
				};
			}
		} );

		// Adds and removes 'checked' class on review template selection
		$( '.template-label-image' ).on( 'click', function() {
			var x = $( this );
			$( '.template-label-image' ).removeClass( 'checked' );
			$( x ).addClass( 'checked' );
		} );

		// Disables end user editing certain plugin options
		var author_tax = $('#rcno_reviews_settings\\[rcno_taxonomy_selection\\]\\[author\\]');

		// The author taxonomy must always be enabled.
		if ( author_tax.is( ':checked' ) ) {
			author_tax.attr( 'disabled', true );
		}

		// The author taxonomy can't be hierarchical.
		$( '#rcno_reviews_settings\\[rcno_author_hierarchical\\]' )
		.attr( 'disabled', true );
		$( '#rcno_reviews_settings\\[rcno_series_hierarchical\\]' )
		.attr( 'disabled', true );
		//$( '#rcno_reviews_settings\\[rcno_show_isbn\\]' ).attr( 'disabled', true );

		// Reset settings page options via AJAX. Is this necessary?
		$( '.rcno-reset-button' ).on( 'click', function( e ) {
			e.preventDefault();
			$.ajax( {
				type: 'post',
				url: my_script_vars.ajaxURL,
				data: {
					action: 'reset_all_options',
					reset_nonce: my_script_vars.rcno_reset_nonce
				}
			} );
			alert( my_script_vars.rcno_settings_reset_msg );
		} );

	} );

	// Image uploader using the WP media uploader
	function renderMediaUploader() {

		var file_frame, image_data;

		/**
		 * If an instance of file_frame already exists, then we can open it
		 * rather than creating a new instance.
		 */
		if ( undefined !== file_frame ) {
			file_frame.open();
			return;
		}

		/**
		 * If we're this far, then an instance does not exist, so we need to
		 * create our own.
		 *
		 * Here, use the wp.media library to define the settings of the Media
		 * Uploader. We're opting to use the 'post' frame which is a template
		 * defined in WordPress core and are initializing the file frame
		 * with the 'insert' state.
		 *
		 * We're also not allowing the user to select more than one image.
		 */
		file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Upload Book Cover',
			button: {
				text: 'Use this image'
			},
			multiple: false
		});

		/**
		 * Setup an event handler for what to do when an image has been
		 * selected.
		 *
		 * Since we're using the 'view' state when initializing
		 * the file_frame, we need to make sure that the handler is attached
		 * to the insert event.
		 */
		file_frame.on( 'select', function() {

			// Read the JSON data returned from the Media Uploader.
			var json = file_frame.state().get( 'selection' ).first().toJSON();
			console.log(json);

			// First, make sure that we have the URL of an image to display.
			if ( 0 > $.trim( json.url.length ) ) {
				return;
			}

			// After that, set the properties of the image and display it.
			$( '#rcno-reviews-book-cover-container' )
			.children( 'img' )
			.attr( 'src', json.url )
			.attr( 'alt', json.alt )
			.attr( 'title', json.title )
			.attr( 'data-id', json.id )
			.show()
			.parent()
			.removeClass( 'hidden' );

			// Next, hide the anchor responsible for allowing the user to select an image
			$( '#rcno-add-book-cover' ).hide();
			$( '#rcno-remove-book-cover' ).parent().removeClass( 'hidden' );
			$( '#rcno-reviews-book-cover-src' ).val( json.url );
			$( '#rcno-reviews-book-cover-title' ).val( json.title );
			$( '#rcno-reviews-book-cover-alt' ).val( json.alt );
			$( '#rcno-reviews-book-cover-id' ).val( json.id );
		} );

		// To use the 'Insert from URL' feature.
		file_frame.state('embed').on( 'select', function() {
			var state = file_frame.state(),
				type = state.get('type'),
				embed = state.props.toJSON();

			// Show JSON for embedded URL
			console.log(embed);
		});

		// Now display the actual file_frame
		file_frame.open();
	}

	function resetUploadForm( $ ) {

		// First, we'll hide the image
		$('#rcno-reviews-book-cover-container').children('img').hide();
		$('#rcno-add-book-cover').show();

		// Finally, we add the 'hidden' class back to this anchor's parent
		$('#rcno-remove-book-cover').addClass('hidden');
		$('#rcno-reviews-book-cover-src').val('');
		$('#rcno-reviews-book-cover-title').val('');
		$('#rcno-reviews-book-cover-alt').val('');
		$('#rcno-reviews-book-cover-id').val('');
	}

	function renderFeaturedImage( $ ) {

		/* If a thumbnail URL has been associated with this image
		 * Then we need to display the image and the reset link.
		 */
		if ( '' !== $( '#rcno-reviews-book-cover-src' ).val()?.trim() ) {
			$( '#rcno-reviews-book-cover-container' ).removeClass( 'hidden' );
			$( '#rcno-add-book-cover' ).parent().hide();
			$( '#rcno-remove-book-cover' ).parent().removeClass( 'hidden' );
		}

	}

	$( function() {
		renderFeaturedImage( $ );

		$( '#rcno-add-book-cover' ).on( 'click', function( e ) {
			e.preventDefault();

			// Display the media uploader.
			renderMediaUploader();
		} );

		$( '#rcno-remove-book-cover' ).on( 'click', function( e ) {
			e.preventDefault();

			// Remove the image, toggle the anchors.
			resetUploadForm( $ );
		} );
	} );

	$( function() {
		if ( typeof $().starRating !== 'function') return;
		$( '#rcno-star-rating' ).starRating({
			initialRating: parseFloat( my_script_vars.rcno_admin_rating ),
			starSize: 25,
			disableAfterRate: false,
			emptyColor: '#ccc',
			hoverColor: '#ffd700',
			activeColor: '#ffd700',
			ratedColor: '#ffd700',
			useGradient: false,
			strokeWidth: 0,
			callback: function( currentRating ){
				$( '#rcno_admin_rating' ).val( currentRating );
			}
		});
	});

    // Adds default WP color picker UI to settings page
	if ( 'undefined' !== typeof $.minicolors ) {
        $( '.rcno-color-input' ).minicolors({
            format: 'rgb',
            opacity: true,
            swatches: [
                '#F44336', '#E91E63', '#9C27B0', '#673AB7', '#2196F3', '#03A9F4', '#00BCD4',
                '#009688', '#4CAF50', '#8BC34A', '#CDDC39', '#FFEB3B', '#FFC107', '#FF9800',
            ]
        });
	}

    // Adds selectize.js support to text boxes on settings page
    if ( 'undefined' !== typeof window.Selectize ) {

        $('#rcno_reviews_settings\\[rcno_taxonomy_selection\\]').selectize({
            create: true,
            plugins: ['remove_button', 'restore_on_backspace', 'drag_drop']
        });

        $('#rcno_reviews_settings\\[rcno_store_purchase_links\\]')
            .selectize({
                create: true,
                plugins: ['remove_button', 'restore_on_backspace', 'drag_drop']
            });

        $('#rcno_reviews_settings\\[rcno_no_pluralization\\]').selectize({
            create: true,
            plugins: ['remove_button', 'restore_on_backspace', 'drag_drop']
        });

        $('#rcno_reviews_settings\\[rcno_reviews_ignored_articles_list\\]').selectize({
            create: true,
            plugins: ['remove_button', 'restore_on_backspace', 'drag_drop']
        });

        $('#rcno_reviews_settings\\[rcno_custom_review_score_criteria\\]').selectize({
            create: true,
            plugins: ['remove_button', 'restore_on_backspace', 'drag_drop']
        });

        var book_meta_keys = [];
        $.each(my_script_vars.rcno_book_meta_keys, function (key, value) {
            book_meta_keys.push({key: key, value: value});
        });

        $('#rcno_reviews_settings\\[rcno_book_details_meta\\]').selectize({
            maxItems: null,
            valueField: 'key',
            labelField: 'value',
            searchField: 'value',
            options: book_meta_keys,
            create: false,
            plugins: ['remove_button', 'restore_on_backspace', 'drag_drop'],
        });
    }

    // Codemirror settings for the CSS box in our settings page.
    if ( 'undefined' !== typeof wp.codeEditor ) {
        $(function () {
            var css_box = $('.rcno-css-box');
            var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror,
                {
                    indentUnit: 2,
                    tabSize: 2,
                    mode: 'css',
                }
            );
            if(css_box.length > 0) {
                var editor = wp.codeEditor.initialize(css_box, editorSettings);
            }
        });
    }

} )( jQuery );
