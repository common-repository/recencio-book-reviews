=== Recencio Book Reviews ===
Contributors: w33zy
Donate link: https://paypal.me/wzymedia
Tags: book reviews, reviews, book library, book ratings
Requires at least: 3.0.1
Requires PHP: 5.6.25
Tested up to: 6.3
Stable tag: 1.66.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Recencio Book Reviews is a powerful and very flexible tool for managing your blog’s book review collection.
It was designed with the book reviewer in mind and built to be fast, easy and customizable.

== Description ==

Recencio Book Reviews is a powerful and very flexible tool for managing your blog’s book review collection. It was designed with the book reviewer in mind and built to be fast, easy and customizable.
The plugin even provides book reviewers the opportunity of earning residual income via adding their own book purchase affiliate links.

This plugin adds a custom post type for book reviews to your site. You can publish book reviews as standalone posts or include them in your normal posts and pages.
Organize your book reviews in any way you like. The plugin comes with some basic predefined taxonomies such as book author, genre, series, and publisher.
Creating new book taxonomies is so easy it’s up to you to decide which and how many taxonomies you want to add.

Use listings embedded in pages to make your book reviews accessible by title or taxonomies such as series or author. Or use one of the custom widgets to create tag clouds or top ten lists.

Of course, you can use all the WordPress goodies you know from posts on book reviews as well: images, videos and comments.
Despite its simplicity, it’s giving book reviewer, administrators, designers and developers all the freedom to adapt the plugin to their needs. Features you don’t need can be deactivated and are hidden from the UI.
You can even create your own book review template files to gain complete control the look and feel of book reviews.

Most importantly, all the above is SEO friendly by way of validated JSON-LD metadata for the 'Book' and 'Review' schema markup.

= Features =

* custom post type for book reviews
* 100% WordPress theme compatibility
* automatically fetch book details via its ISBN, or enter manually
* custom backend to enter book reviews fast
* easily convert existing posts to reviews
* your choice of a 5-star rating. or a criteria-based scoring system
* builtin book taxonomies such as, Author, Genre, Series and Publisher come predefined, but you can create whatever taxonomy you like.
* use default post categories and tags on book reviews as well, or disable them if you want to
* access reviews by alphabetical indices of review title or taxonomy entries such as book author or book publisher
* include book reviews in your normal posts or pages using shortcodes
* search engine friendly book review output using schema.org‘s review and book metadata as JSON-LD
* choose between 4 different templates to determine how your book review should look or create your own custom layout
* 5 custom widgets such as a book cover slider and book cover grid
* site visitor rating system via the builtin comment form, or disable it if you want to
* basic support of the new WP REST API system
* extendable 3rd party extension/module system for easy creation of new features
* have you book reviews show up in your site's RSS feed
* create book purchase links using your own affiliate code
* have your site visitors/reader leave a 5 rating via the comment form
* translation ready
* GDPR compliant
* RTL support

== Demo ==

[Recencio.com](https://recencio.com/)

== Languages ==

Recencio Book Reviews is currently available in the following languages

* English
* German

For the following languages translations are partly available:

* French
* Spanish
* Hungarian

Please help [translating](https://translate.wordpress.org/projects/wp-plugins/recencio-book-reviews/) the plugin.
This is one of the best ways to contribute to the growth and development of this plugin.

== Sponsors ==

Thanks to JetBrains for providing a copy of their [PhpStorm](https://www.jetbrains.com/?from=recencio-book-reviews) software for developing this plugin.

== Installation ==

1. Upload `rcno-reviews` folder to your website's `/wp-content/plugins/` folder
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add new book reviews via the new 'Reviews' option on your site's admin menu

== Frequently Asked Questions ==

= I am getting a 404 page when I visit a book review =

Try flushing your permalink settings. Visit Settings -> Permalinks and just save without changing anything. Also, make sure you are using the singular form of the word you are
using for your custom slug. So, instead of "books", use "book" or instead of "lists" use "list".

= How can I get support for this plugin =

Please open a new topic on the plugin's WordPress.org support page

== Screenshots ==

1. Example of reviews with correct rich snippet markup on the Google search result page
2. Choose from a choice of 4 builtin book review templates, or create your own inside your current theme
3. Remove or add your own list of stores to purchase books from
4. New book review admin page
5. Custom WordPress widgets
6. Book authors index page
7. Book reviews index page
8. Book review post using the default template
9. Book review post showing the criteria-based review score box

== Changelog ==

= 1.66.0 =
* Fixed an issue with the "Currently Reading" widget not allowing the user to select a book cover

= 1.65.0 =
* Fixed JS error on non-Recencio WP admin pages
* Display notice if `[rcno-book-list]` is used outside of a book review
* Add a new shortcode that displays specific details of a reviewed book `[rcno-book id=3723 detail="Book Description/Synopsis" wrapper="p" label="false"]`
* Remove unnecessary settings check on plugin activation
* Code cleanup

= 1.64.0 =
* Add a "cover size" option to the "recent reviews" widget
* Refactored the [rcno-table] shortcode to support multiple taxonomy terms
* Refactored the book cover to support custom URLs in all templates
* Code cleanup for PHP 8 compatibility

= 1.63.0 =
* Fixed a bug with the Selectize library not being initialized
* Updated the selectize library to v0.13.5
* Reorganized JS and CSS libraries on WP admin pages

= 1.62.0 =
* Start using WordPress' native responsive images feature with book covers
* Fixed bugs with the display of book cover images
* Added a filter to override the "no cover" image
* Increased WordPress version compatibility to 6.0
* Assorted bug fixes, code cleanup and refactoring
* Bug fixes

= 1.61.0 =
* Fixed a bug with how book covers are resolved

= 1.60.1 =
* Minor bug fix

= 1.60.0 =
* Fix a critical bug related to the Gutenberg post editor

= 1.50.1 =
* Fix a JS bug with TinyMCE and Google Books

= 1.50.0 =
* Added an option to use custom review taxonomies with regular posts

= 1.49.0 =
* First iteration of the `[rcno-table]` shortcode feature
* Fixed an issue where custom template thumbnail is not loaded from child-theme
* Code cleanup

= 1.48.1 =
* Fixed a bug with star ratings not showing on fetched reviews in the Isotope grid shortcode
* The Isotope grid shortcode now filters using the `AND` operator

= 1.48.0 =
* Updated the CSSTidy library to v1.7.3 to resolve PHP warnings
* Re-worked the "isotope" grid shortcode to support AJAX loading
* Fixed an issue where the book synopsis field is unselectable within the Gutenberg editor
* Fixed an issue where Gutenberg is adding extra `<p>` tags to review content

= 1.47.0 =
* Fixed a bug with unclosed divs in the `[rcno-tax-list]` shortcode
* Display the review ID on the WP admin review list table
* Code cleanup

= 1.46.1 =
* Fixed missing A11y markup

= 1.46.0 =
* Fixed issues with the "Currently Reading" widget
* Fixed issues with the filtering of images inside the review content
* Added a filter `rcno_taxonomy_strip_tags` to strip tags from custom taxonomies

= 1.45.0 =
* Added a new template for reviews, called "Rcno Inverse"
* Bug fixes and code cleanup

= 1.44.0 =
* Fixed a regression with non-Latin characters introduced in 1.42.0
* Fixed bug in the "Currently Reading" widget
* Added anchor tag to book covers in the "Recent Reviews" widget
* Added new params to shortcodes
* Refactored styling of the book information in reviews

= 1.43.0 =
* Fixed longstanding issues with the usage of the `the_content` filter

= 1.42.2 =
* Fixed an issue where book cover not displaying in the "Currently Reading" admin widget
* Fixed an issue with YouTube URLs were not processed inside the review content
* Made some adjustments to better work with Cyrillic languages

= 1.42.1 =
* Fixed a warning message related to registering `author` as a REST base
* Fixed a bug in the "Recent Reviews" widget

= 1.42.0 =
* Rewrote the settings page to accept both the plural and singular form of labels, aimed at improved internationalization
* Added a system to automatically flush the permalink on settings changes that affect public URLs
* Added new options to the "Recent Reviews" widget
* Fixed an issue where straight quotes where not being converted to curly quotes, see `wptexturize()`
* Fixed a PHP deprecation warning message related to the `contextual_help` hook

= 1.41.0 =
* Refactored the translation feature to better comply with WordPress' i18n standards
* Fixed a long-running issue of translations not showing up on settings page
* Thanks to @chrillebln for assisting with the DE translation

= 1.40.0 =
* Purchase links now show on reviews embedded in pages
* Recencio related errors only shown on Recencio related pages and the WP dashboard
* Fixed an issue with a missing reference to jQuery on the extension page

= 1.39.0 =
* A new 'Convert Posts to Reviews' extension to do as its name says.
* Updated the menu label for 'All Reviews'.
* Changed 'Activate/Deactivate' on extensions page to 'Enable/Disable' to avoid confusion between WP plugins and Recencio extensions.

= 1.38.0 =
* Remove 'Currently Reading' widget for non-admin users.
* Added the 'thumbnailUrl' property to the JSON-LD output.

= 1.37.0 =
* Finally fixed an issue with not correctly flushing rewrite rules on plugin activation.

= 1.36.0 =
* Prevent displaying a review shortcode inside a review post type.
* Fixed an issue with the display of half-star reviews.

= 1.35.0 =
* Added filter to book metadata items to allow renaming them via code.
* Added a new `[rcno-book-listing ids='']` shortcode .

= 1.34.2 =
* Patched a bug where installed extensions are being sent as an object instead of array.

= 1.34.1 =
* Patched a missing class method accessor.

= 1.34.0 =
* Added a new layout for the "Review Score Box" feature.
* Added a new parameter `count` for the `[rcno-tax-list]` shortcode.
* Added support for Jetpack's "publicize" feature.
* Fixed some display issues with the new review template.

= 1.33.0 =
* Fixed an issue with wrong book cover size being returned by 3rd party API.
* Fixed a potential issue where migrating a site running Recencio would cause PHP warnings.
* Add current review template's name to the WP class for easier debugging.
* Added the new "Metamor" book review template (WIP).

= 1.32.0 =
* Fixed an issue where truncated strings had an � character in languages with multi-byte characters
* Added "width" and "height" parameters to the sortable grid shortcode i.e., `[rcno-sortable-grid width=80 height=115]`
* Added "width" and "height" parameters to the reviews index shortcode i.e., `[rcno-reviews-index width=120 height=240]`
* Added a `rating` parameter to enable sorting reviews by star rating in the sortable grid shortcode i.e., `[rcno-sortable-grid rating=1]`

= 1.31.0 =
* Fixed PHP warning message in "Taxonomy List" widget.
* Switched comparison function to use closure to avoid calling the same function twice.
* Added the Hungarian translation provided by @gaborh

= 1.30.1 =
* Made the review index more descriptive with error messages.

= 1.30.0 =
* Fixed an issue where a book cover's file name can be blank.
* Fixed an issue where the book cover view file was reverted to an earlier version.

= 1.29.0 =
* Fixed JavaScript error when TinyMCE is disabled.

= 1.28.0 =
* Added a new extension that fetches and adds book cover to reviews.

= 1.27.0 =
* Added option for fixed review criteria options.
* Rewrote book data fetching feature.

= 1.26.3 =
* Fixed a bug where an embedded review without an excerpt returns an empty string instead of an auto-excerpt.

= 1.26.2 =
* Added a check to make sure we are only affecting excerpt length of reviews in the RSS feed.
* Catch an error when users use a taxonomy shortcode where the term is custom and not recognized.

= 1.26.1 =
* Fixed a JS bug in the 'Currently Reading' widget.

= 1.26.0 =
* Disable support for the new Gutenberg editor.

= 1.25.0 =
* Fixed a bug with shortcodes not rendering correctly on pages.

= 1.24.1 =
* Fixed a fatal PHP error when evaluating a function inside 'empty()'.

= 1.24.0 =
* Added a filter to the 'get_the_rcno_book_cover()' to force a specific book cover size.

= 1.23.3 =
* See v1.23.2

= 1.23.2 =
* Fixed a bug where the class 'csstidy_print' can be declared twice.

= 1.23.1 =
* Fix missing update for "Current Reading" widget in last update.

= 1.23.0 =
* Rewrote the "Currently Reading" widget using VueJS. The widget now supports multiple progress updates.
* The "Review Reviews" widget now strips shortcodes from the book's description.

= 1.22.0 =
* Added a reactive search bar to the "sortable grid" feature.

= 1.21.3 =
* Added Code Risk verification tag

= 1.21.2 =
* Fixed a bug affecting the "Add Book Cover" button on the review edit page.
* Fixed a bug affecting the loading of 3rd party JS libraries.

= 1.21.1 =
* Fixed a bug affecting the "fetch" feature.

= 1.21.0 =
* Added a social sharing extension to share your book reviews on social media networks.
* Added the WP Codemirror library for the CSS box in our settings page.

= 1.20.0 =
* Moved the Micromodal init call to the `rcno-extensions-admin` script so it is always called
* Fixed a bug with the usage of the 'posts_clauses' filter
* The filterable book grid now lists drop-down items in alphabetical order

= 1.19.0 =
* Added the option to the limit index page shortcode to specific category `[rcno-reviews-index category="review"]`

= 1.18.1 =
* Fixed a display bug with overlapping images in IsotopeJS grids

= 1.18.0 =
* Added taxonomy drop-down filters to the reviews admin column
* Currently reading widget now resets values when 'Finished' is checked
* Currently reading widget now uses a smaller book cover size
* Added span tags around book meta keys and values when displayed on the frontend
* Changed settings page select drop-downs to radio buttons for improved UX

= 1.17.0 =
* Added a new "Custom User Metadata" extension
* Add 'page states' feature to identify pages using Recencio shortcodes
* Fixed a regression bug in the calendar widget
* Fixed a bug with RSS feeds for book reviews
* Removed comment rating stars markup from comment RSS feeds

= 1.16.0 =
* Added GDPR support and compliance
* Trimmed API keys to prevent issues with extra spaces
* Re-worked the Isotope grid page to use taxonomy label to avoid i18n issues

= 1.15.1 =
* Made some adjustments to the review box styling for the default template

= 1.15.0 =
* Added an option for adding custom links to book covers in single reviews
* Fixed a bug where the comment form was displayed on pages with certain shortcodes
* Minor style fixes

= 1.14.0 =
* Added new "Extensions" feature to allow the easier addition of new features by 3rd party developers
* Added new "Author Box" extension. More extensions coming soon, e.g., Social media share buttons
* New social media profile fields added to WordPress user profile page

= 1.12.1 =
* Caught an error with non-object being returned in taxonomy list with sortable grid page
* Update translation files with new strings

= 1.12.0 =
* Added sortable grid of book reviews via the Isotope JS library
* Store book cover attachment ID to review meta for improved performance
* Added a new Upgrader class to handle post-update functions
* Normalized the minimum rating a book can receive as per the Google Rich Snippets spec
* Re-worked the book cover upload modal to only allow image upload or media gallery select
* Settings page color picker now supports RGBA colors for transparency
* Moved aggregate rating item to inside the reviewed item schema
* Added setting to disable the book or review JSON-LD schema markups from book reviews

= 1.11.2 =
* Fixed display bug in review calendar widget (an extra cell padding was added to 1st week of March)

= 1.11.1 =
* Fixed a debug issue with `flush_rewrite_rules`
* Fixed an issue with fetch book data from remote API

= 1.11.0 =
* Users are now able to select and reorder the book metadata they want displayed on reviews
* We are now using a better function to handle the display of review excerpts

= 1.10.1 =
* Updated admin translation strings

= 1.10.0 =
* Made some adjustments to the way custom taxonomies are auto-generated. This may break custom taxonomies with custom slugs
* Added the correct name formatting for translation files
* Added an option to add new articles for the sorting of titles alphabetically, useful for foreign languages
* Fixed the potential of a fatal PHP error if shortcode is included twice on a page

= 1.9.1 =
* Added a new option to use clickable title links when reviews are embedded in regular posts

= 1.9.0 =
* Added an option to import and export plugin settings
* Added an option to define custom taxonomy labels separate from the slug
* The 5 star rating box now provides an option half-star review scores e.g., 3.5 of out 5 stars
* It is now possible to disable the automatic pluralization of words used in the plugin
* Fixed an issue with the review calendar display on months with 7 days in week 4
* Fixed an issue with 3rd party plugins displaying twice when using the 'the_content' filter

= 1.8.2 =
* Added an "after review" action to use for tying in the use of additional 3rd party content below reviews

= 1.8.1 =
* Fixed an issue in how best and worst ratings are calculated for the "Reviews" schema; thanks @kimoves

= 1.8.0 =
* Added 3 new shortcodes: [rcno-book-list], [rcno-review-box] and [rcno-purchase-links]

= 1.7.1 =
* Fixed a bug with CPT slug not taking a pluralized form in some foreign languages

= 1.7.0 =
* Added new "Books in this Series" feature
* Redesigned the taxonomy index pages to use use book covers
* Moved index page setting to their own section on option page
* Refactored the book description to use a filter
* Added the post type slug to the custom taxonomy URLS
* Removed comment rating scripts and styles from posts that are not reviews

= 1.6.3 =
* Redesigned the progress bar on the currently reading widget
* Refactored the home page content to take into consideration the 'read more' tag

= 1.6.2 =
* Fixed a bug in last update that was remove content on homepage,
    see: https://wordpress.org/support/topic/strange-issue-after-new-update/

= 1.6.1 =
* Refactored the custom template functions
* Cleaned up various classes

= 1.6.0 =
* Added a book review grid index page, via MacyJS for a masonary layout
* Merged Owl Carousel theme file with main file, for reduced file load
* Book cover now uses book title meta, as most users don't provide one on file upload
* Fixed the styling of headers on the main review index page

= 1.5.1 =
* Fixed an issue with the incorrect API path for the Currently Reading dashboard widget
* Set Book Grid widget to default to 9 book for a balanced layout
* Set Recent Reviews widget to sort reviews by most recent and fixed review count option

= 1.5.0 =
* Add a new review calendar widget

= 1.4.0 =
* Add book covers to the review posts admin column
* Fixed an issue with removing custom taxonomies from the review posts admin column

= 1.3.0 =
* Added an option to ignore articles when sorting titles

= 1.2.11 =
* Added an new "currently reading" widget
* Added an setting page option to list 'uncountable' words in users native language. These words wont be singularized or pluralized by the plugin

= 1.1.10 =
* Added an option to display book covers on the book reviews index page

= 1.0.10 =
* Fixed regression from last patch

= 1.0.9 =
* Fixed an error in the review shortcodes file cause by the differences between Windows and Unix use of '/'

= 1.0.8 =
* Moved all admin scripts to footer and added version numbers

= 1.0.7 =
* Fixed issue with comments not showing when comment ratings is disabled

= 1.0.6 =
* Moved flush rewrite rule function to better handle plugin activation.
* Added flush rewrite rule function to run on plugin deactivation.
* Better handle taxonomy slugs.
* Better handle multiple user added taxonomies.
* Adjusted the styling of the recent book review widget

= 1.0.5 =
* fixed an error when fetching a taxonomy with an error, updated to v1.0.5

= 1.0.3 =
* Generate a book author's URL if one is not provided by site owner
* Cleaned up the 'Rcno_Reviews_Rest_API' class

= 1.0.2 =
* Fixed an issue where templates stop working before plugin folder name got changed

= 1.0.1 =
* Fixed an issue with white color stars on a white background on initial install

= 1.0.0 =
* Initial plugin release

== Upgrade Notice ==

= 1.42.0 =
* After installing this update, please visit the "Taxonomy" tab on the Recencio settings page and click the "Save settings" button.
