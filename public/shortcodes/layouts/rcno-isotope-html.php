<?php

/**
 * Render a list of all book reviews as an Isotope grid
 *
 * Available variables:
 *
 * @var array $posts
 * @var array $options
 * @var Rcno_Isotope_Grid_Shortcode $this
 *
 * Available functions:
 * sort_by_title()
 */
?>

<?php

// Create an empty array to take the book details.
$books = array();

if ( $posts && count( $posts ) > 0 ) {
    // Loop through each post, book title from post-meta and work on book title.
    foreach ( $posts as $book_ID ) {
        $book_details   = get_post_custom( $book_ID );
        $unsorted_title = isset( $book_details['rcno_book_title'] ) ? $book_details['rcno_book_title'][0] : '';
        $sorted_title   = $unsorted_title;
        if ( $this->variables['ignore_articles'] ) {
            $sorted_title = preg_replace( '/^(' . $this->variables['articles_list'] . ') (.+)/', '$2, $1', $unsorted_title );
        }
        $books[] = array(
            'ID'             => $book_ID,
            'sorted_title'   => $sorted_title,
            'unsorted_title' => $unsorted_title,
        );
        //usort( $books, array( $this, 'sort_by_title' ) );
    }
}

$exclude = explode( ',', $options['exclude'] );
$taxonomies = array_map( static function ( $taxonomy ) {
    return 'rcno_' . $taxonomy;
}, array_diff( $this->variables['custom_taxonomies'], $exclude ) );

if ( $options['category'] ) {
    array_unshift( $taxonomies, 'category' );
}
?>

<?php if ( $books ) { ?>
    <div class="rcno-isotope-grid-select-container">
        <?php if ( $options['search'] ) { ?>
            <div class="rcno-isotope-grid-select-wrapper">
                <label for="">
                    <span class="screen-reader-text"> <?php esc_attr_e( 'Search for', 'recencio-book-reviews' ); ?>:</span>
                    <input class="rcno-isotope-grid-search" type="text" placeholder="<?php esc_attr_e( 'Search...', 'recencio-book-reviews' ); ?>">
                </label>
            </div>
        <?php } ?>

        <?php foreach ( $taxonomies as $taxonomy ) {
            $terms = get_terms( array( 'taxonomy' => $taxonomy, 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true, ) );

            if ( is_wp_error( $terms) ) {
	            continue;
            }

            $tax = get_taxonomy( $taxonomy );
        ?>
            <div class="rcno-isotope-grid-select-wrapper">
                <label for="rcno-isotope-grid-select-<?php echo $tax->name; ?>" class="screen-reader-text">
                    <?php echo $tax->labels->name; ?>
                </label>
                <select name="rcno-isotope-grid-select" id="rcno-isotope-grid-select-<?php echo $tax->name; ?>" class="rcno-isotope-grid-select">
                    <option value="*"><?php echo apply_filters( 'rcno_isotope_select_label', sprintf( '%1$s %2$s', __( 'Select', 'recencio-book-reviews' ), $tax->labels->singular_name ), $tax->labels->singular_name ); ?></option>
                    <?php foreach ( $terms as $term ) { ?>
                        <?php if ( ! is_wp_error( $term ) ) { ?>
                            <option value=".<?php echo $term->slug; ?>"><?php echo $term->name; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
        <?php } ?>

        <?php if ( $options['rating'] ) { ?>
            <div class="rcno-isotope-grid-select-wrapper">
                <label for="rcno-isotope-grid-select-rating" class="screen-reader-text">
                    <?php esc_attr_e( 'Rating', 'recencio-book-reviews' ); ?>
                </label>
                <select name="rcno-isotope-grid-select" id="rcno-isotope-grid-select-rating" class="rcno-isotope-grid-select">
                    <option value="*"><?php esc_attr_e( 'Select Rating', 'recencio-book-reviews' ); ?></option>
                    <option value=".1"><?php esc_attr_e( '1 Star', 'recencio-book-reviews' ); ?></option>
                    <option value=".2"><?php esc_attr_e( '2 Stars', 'recencio-book-reviews' ); ?></option>
                    <option value=".3"><?php esc_attr_e( '3 Stars', 'recencio-book-reviews' ); ?></option>
                    <option value=".4"><?php esc_attr_e( '4 Stars', 'recencio-book-reviews' ); ?></option>
                    <option value=".5"><?php esc_attr_e( '5 Stars', 'recencio-book-reviews' ); ?></option>
                </select>
            </div>
        <?php } ?>
        <span class="rcno-isotope-grid-select reset">&olarr;</span>
    </div>

    <div id="rcno-isotope-grid-container" class="rcno-isotope-grid-container">
        <?php foreach ( $books as $book ) { ?>
            <div class="rcno-isotope-grid-item
                <?php echo sanitize_title( $this->template->get_the_rcno_book_meta( $book['ID'], 'rcno_book_title', '', false ) ); ?>
                <?php echo implode( ' ', $this->template->get_rcno_review_html_classes( $book['ID'] ) ); ?>
            " style="width:<?php echo esc_attr( $options['width'] ); ?>px; height:<?php echo esc_attr( $options['height'] ); ?>px">
                <?php if ( $options['rating'] ) { ?>
                    <?php echo $this->template->get_the_rcno_admin_book_rating( $book['ID'] ); ?>
                <?php } ?>
                <a href="<?php echo esc_url( get_permalink( $book['ID'] ) ); ?>">
                    <?php if ( (int) $options['width'] > 85 ) { ?>
                        <?php echo $this->template->get_the_rcno_book_cover( $book['ID'], 'rcno-book-cover-lg' ); ?>
                    <?php } else { ?>
                        <?php echo $this->template->get_the_rcno_book_cover( $book['ID'], 'rcno-book-cover-sm' ); ?>
                    <?php } ?>
                </a>
            </div>
        <?php } ?>
    </div>
    <div class="rcno-isotope-grid-load">
        <button><?php esc_attr_e( 'Load more', 'recencio-book-reviews' ); ?></button>
        <div class="rcno-loader"><div></div><div></div><div></div><div></div></div>
    </div>
<?php } else { ?>
    <h2><?php esc_html_e( 'There are no book reviews to display.', 'recencio-book-reviews' ); ?></h2>
<?php } ?>

<style type="text/css">
    .rcno-isotope-grid-load {margin:1rem 0; display:flex; justify-content:center; align-items:center;}
    .rcno-loader{display:none;position:relative;width:80px;height:80px}
    .rcno-loader div{position:absolute;top:33px;width:13px;height:13px;border-radius:50%;background:#000;animation-timing-function:cubic-bezier(0,1,1,0)}
    .rcno-loader div:nth-child(1){left:8px;animation:rcno-loader1 .6s infinite}
    .rcno-loader div:nth-child(2){left:8px;animation:rcno-loader2 .6s infinite}
    .rcno-loader div:nth-child(3){left:32px;animation:rcno-loader2 .6s infinite}
    .rcno-loader div:nth-child(4){left:56px;animation:rcno-loader3 .6s infinite}
    @keyframes rcno-loader1{0%{transform:scale(0)}100%{transform:scale(1)}}@keyframes rcno-loader3{0%{transform:scale(1)}100%{transform:scale(0)}}
    @keyframes rcno-loader2{0%{transform:translate(0,0)}100%{transform:translate(24px,0)}}
</style>

<script type="text/javascript">
    (function($) {
        'use strict';
        $(function() {

            if (window.Isotope === undefined
                || window.imagesLoaded === undefined
                || jQuery('.rcno-isotope-grid-container').length === 0) {
                return;
            }

            let qsRegex;
            let stars = $('.rcno-admin-rating .stars');

            $.each(stars, function(index, star) {
                $(star).starRating({
                    initialRating: $(star).data('review-rating'),
                    emptyColor: rcno_star_rating_vars.background_colour,
                    activeColor: rcno_star_rating_vars.star_colour,
                    useGradient: false,
                    strokeWidth: 0,
                    readOnly: true,
                });
            });

            const $grid = $('.rcno-isotope-grid-container').imagesLoaded(function () {
                // init Isotope after all images have loaded
                $grid.isotope({
                    itemSelector: '.rcno-isotope-grid-item',
                    layoutMode: 'masonry',
                });
            });

            let previousValue = '';
            let filterValues = [];
            const select = $('.rcno-isotope-grid-select');

            select.on('focus', function () {
                previousValue = $(this).val();
            })
            .on('change', function () {
                const value = $(this).val();
                filterValues = filterValues.filter(function (i) {
                    return i !== previousValue
                })
                filterValues.push(value);
                let currentValue = filterValues.join('');
                $grid.isotope({filter: currentValue});
                $(this).blur();
            });

            $('.rcno-isotope-grid-select.reset').on('click', function() {
                $grid.isotope({filter: '*'});
                select.each(function() {
                    $(this).prop('selectedIndex', 0);
                });
                $('.rcno-isotope-grid-search').val('');
                filterValues = [];
            });

            const $search = $('.rcno-isotope-grid-search').keyup(debounce(function () {
                qsRegex = new RegExp($search.val(), 'gi');
                $grid.isotope({
                    filter: function () {
                        return qsRegex ? $(this)[0].className.match(qsRegex) : true;
                    },
                });
            }, 200));

            // Debounce so filtering doesn't happen every millisecond
            function debounce(fn, threshold) {
                let timeout;
                threshold = threshold || 100;
                return function debounced() {
                    clearTimeout(timeout);
                    const args = arguments;
                    const _this = this;

                    function delayed() {
                        fn.apply(_this, args);
                    }

                    timeout = setTimeout(delayed, threshold);
                };
            }

            let page = 2;

            $('.rcno-isotope-grid-load button').on('click', function (e) {
                e.preventDefault()

                $('.rcno-isotope-grid-load button').hide()
                $('.rcno-isotope-grid-load .rcno-loader').show()

                $.ajax({
                        type: 'POST',
                        url: rcnoIsotopeVars.ajaxURL,
                        data: {
                            action: 'more_filtered_reviews',
                            nonce: rcnoIsotopeVars.nonce,
                            page:  page,
                            count:  rcnoIsotopeVars.count,
                            orderby:  rcnoIsotopeVars.orderby,
                            order:  rcnoIsotopeVars.order,
                            width:  rcnoIsotopeVars.width,
                            height:  rcnoIsotopeVars.height,
                            rating:  rcnoIsotopeVars.rating,
                        },
                    },
                ).done(function (res) {
                    if (res === '') {
                        $('.rcno-isotope-grid-load button').hide()
                        $('.rcno-isotope-grid-load .rcno-loader').hide()
                        return
                    }

                    $('.rcno-isotope-grid-load button').show()
                    $('.rcno-isotope-grid-load .rcno-loader').hide()

                    const $items = $(res) // create new item elements
                    $grid.append($items).isotope('appended', $items) // append items to grid

                    stars = $('.rcno-admin-rating .stars');

                    $.each(stars, function(index, star) {
                        $(star).starRating({
                            initialRating: $(star).data('review-rating'),
                            emptyColor: rcno_star_rating_vars.background_colour,
                            activeColor: rcno_star_rating_vars.star_colour,
                            useGradient: false,
                            strokeWidth: 0,
                            readOnly: true,
                        });
                    });

                    page++
                });
            })

        });

    })(jQuery);
</script>
