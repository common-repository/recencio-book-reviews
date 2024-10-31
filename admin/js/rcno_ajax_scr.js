/**
 * The jquery class handling the shortcode insertion for reviews
 *
 * @since      1.0.0
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin
 * @author     wzyMedia <wzy@outlook.com>
 */

var rcnoReviewSc;

(function ($) {
    var editor, searchTimer, River, Query,
        inputs = {},
        rivers = {},
        isTouch = ( 'ontouchend' in document );

    rcnoReviewSc = {
        timeToTriggerRiver: 150,
        minRiverAJAXDuration: 200,
        riverBottomThreshold: 5,
        keySensitivity: 100,
        lastSearch: '',
        textarea: '',

        init: function () {
            inputs.wrap = $('#rcno-modal-wrap-scr');
            inputs.dialog = $('#rcno-modal-form-scr');
            inputs.backdrop = $('#rcno-modal-backdrop-scr');
            inputs.submit = $('#rcno-modal-submit-scr');
            inputs.close = $('#rcno-modal-close-scr');
            // URL
            inputs.id = $('#review-id-field');
            inputs.nonce = window.rcno_vars.rcno_ajax_nonce; // $('#rcno_ajax_nonce');
            // Secondary options
            inputs.title = $('#retitle-title-field');
            // Advanced Options
            inputs.optlink = $('#rcno-modal-scr-options-link');
            inputs.opticon = $('#rcno-modal-scr-options-link i');
            inputs.optpanel = $('#rcno-modal-scr-options-panel');
            inputs.taxonomy = $('#review-taxonomy');
            inputs.openInNewTab = $('#link-target-checkbox');
            inputs.search = $('#rcno-search-field');
            inputs.excerpt = $('#rcno-embed-excerpt');
            inputs.excerpt.prop("checked", false);
            inputs.nodesc = $('#rcno-embed-nodesc');
            inputs.nodesc.prop("checked", false);

            // Build Rivers
            rivers.search = new River($('#rcno-search-results'));
            rivers.recent = new River($('#rcno-most-recent-results'));
            rivers.elements = inputs.dialog.find('.query-results');

            // Get search notice text
            inputs.queryNotice = $('#query-notice-message');
            inputs.queryNoticeTextDefault = inputs.queryNotice.find('.query-notice-default');
            inputs.queryNoticeTextHint = inputs.queryNotice.find('.query-notice-hint');

            // Bind event handlers
            inputs.dialog.keydown(rcnoReviewSc.keydown);
            inputs.dialog.keyup(rcnoReviewSc.keyup);
            inputs.submit.click(function (event) {
                event.preventDefault();
                rcnoReviewSc.update();
            });
            inputs.close.add(inputs.backdrop).add('#rcno-modal-cancel-scr a').click(function (event) {
                event.preventDefault();
                rcnoReviewSc.close();
            });

            //Action whe a review is selected from the list...
            rivers.elements.on('river-select', rcnoReviewSc.updateFields);

            // Display 'hint' message when search field or 'query-results' box are focused
            inputs.search.on('focus.rcnoReviewSc', function () {
                inputs.queryNoticeTextDefault.hide();
                inputs.queryNoticeTextHint.removeClass('screen-reader-text').show();
            }).on('blur.rcnoReviewSc', function () {
                inputs.queryNoticeTextDefault.show();
                inputs.queryNoticeTextHint.addClass('screen-reader-text').hide();
            });

            inputs.search.keyup(function () {
                var self = this;

                window.clearTimeout(searchTimer);
                searchTimer = window.setTimeout(function () {
                    rcnoReviewSc.searchInternalLinks.call(self);
                }, 500);
            });

            /* Button to open dialog */
            $('body').on('click', '#rcno-add-review-button', function (event) {
                editor_id = jQuery('#rcno-add-review-button').attr("data_editor");
                window.rcnoReviewSc.open(editor_id);
            });
        },

        open: function (editorId) {
            var ed;

            rcnoReviewSc.range = null;

            if (editorId) {
                window.wpActiveEditor = editorId;
            }

            if (!window.wpActiveEditor) {
                return;
            }

            this.textarea = $('#' + window.wpActiveEditor).get(0);

            if (typeof tinymce !== 'undefined') {
                ed = tinymce.get(wpActiveEditor);

                if (ed && !ed.isHidden()) {
                    editor = ed;
                } else {
                    editor = null;
                }

                if (editor && tinymce.isIE) {
                    editor.windowManager.bookmark = editor.selection.getBookmark();
                }
            }

            if (!rcnoReviewSc.isMCE() && document.selection) {
                this.textarea.focus();
                this.range = document.selection.createRange();
            }

            inputs.wrap.show();
            inputs.backdrop.show();

            rcnoReviewSc.refresh();
            $(document).trigger('rcnoReviewSc-open', inputs.wrap);
        },

        isMCE: function () {
            return editor && !editor.isHidden();
        },

        refresh: function () {
            // Refresh rivers (clear links, check visibility)
            rivers.search.refresh();
            rivers.recent.refresh();

            if (rcnoReviewSc.isMCE()) {
                rcnoReviewSc.mceRefresh();
            } else {
                rcnoReviewSc.setDefaultValues();
            }

            if (isTouch) {
                // Close the onscreen keyboard
                inputs.id.focus().blur();
            } else {
                // Focus the URL field and highlight its contents.
                // If this is moved above the selection changes,
                // IE will show a flashing cursor over the dialog.
                inputs.id.focus()[0].select();
            }

            // Load the most recent results if this is the first time opening the panel.
            if (!rivers.recent.ul.children().length) {
                rivers.recent.ajax();
            }
        },

        mceRefresh: function () {
            var e;

            // If link exists, select proper values.
            if (e = editor.dom.getParent(editor.selection.getNode(), 'A')) {
                // Set URL and description.
                inputs.id.val(editor.dom.getAttrib(e, 'href'));
                inputs.title.val(editor.dom.getAttrib(e, 'title'));
                // Set open in new tab.
                inputs.openInNewTab.prop('checked', ( '_blank' === editor.dom.getAttrib(e, 'target') ));
                // Update save prompt.
                inputs.submit.val(rcnoReviewScL10n.update);

                // If there's no link, set the default values.
            } else {
                rcnoReviewSc.setDefaultValues();
            }
        },

        close: function () {
            if (!rcnoReviewSc.isMCE()) {
                rcnoReviewSc.textarea.focus();

                if (rcnoReviewSc.range) {
                    rcnoReviewSc.range.moveToBookmark(rcnoReviewSc.range.getBookmark());
                    rcnoReviewSc.range.select();
                }
            } else {
                editor.focus();
            }

            inputs.backdrop.hide();
            inputs.wrap.hide();
            $(document).trigger('rcnoReviewSc-close', inputs.wrap);
        },

        update: function () {
            if (rcnoReviewSc.isMCE())
                rcnoReviewSc.mceUpdate();
            else
                rcnoReviewSc.htmlUpdate();
        },

        //Build the shortcode here!
        htmlUpdate: function () {
            var attrs, html, begin, end, cursor, title, selection,
                textarea = rcnoReviewSc.textarea;

            if (!textarea)
                return;

            var out = "[";

            if (inputs.id.val == "" || inputs.title.val == "") {
                return;
            }

            out += "rcno-reviews";
            out += " id=" + inputs.id.val();

            if (inputs.excerpt.prop("checked") == true) {
                out += " excerpt=1";
            }
            if (inputs.nodesc.prop("checked") == true) {
                out += " nodesc=1";
            }

            out += "]\n";

            // Insert shortcode
            if (document.selection && rcnoReviewSc.range) {
                // IE
                // Note: If no text is selected, IE will not place the cursor
                //       inside the closing tag.
                textarea.focus();
                rcnoReviewSc.range.text = out;
            } else if (typeof textarea.selectionStart !== 'undefined') {
                // W3C
                begin = textarea.selectionStart;
                end = textarea.selectionEnd;
                selection = textarea.value.substring(begin, end);
                cursor = begin + out.length;
                textarea.value = textarea.value.substring(0, begin) + out +
                    textarea.value.substring(end, textarea.value.length);
                // Update cursor position
                textarea.selectionStart = textarea.selectionEnd = cursor;
            }

            rcnoReviewSc.close();
            textarea.focus();
        },

        mceUpdate: function () {
            var out = "";

            if (inputs.id.val() == "" || inputs.title.val() == "") {
                return;
            }

            out += "[rcno-reviews";
            out += " id=" + inputs.id.val();

            if (inputs.excerpt.prop("checked") === true) {
                out += " excerpt=1";
            }
            if (inputs.nodesc.prop("checked") === true) {
                out += " nodesc=1";
            }
            out += " ]<br/>";

            rcnoReviewSc.close();
            editor.focus();

            tinyMCE.activeEditor.execCommand('mceReplaceContent', false, out);
        },

        updateFields: function (e, li) {
            inputs.id.val(li.children('.item-id').val());
            inputs.title.val(li.hasClass('no-title') ? '' : li.children('.item-title').text());
        },

        setDefaultValues: function () {
            // Set id to default
            inputs.id.val('');
            // Set description to default.
            inputs.title.val('');

            // Update save prompt.
            inputs.submit.val(rcnoReviewsScL10n.save);
        },

        searchInternalLinks: function () {
            var t = $(this), waiting,
                search = t.val();

            if (search.length > 2) {
                rivers.recent.hide();
                rivers.search.show();

                // Don't search if the keypress didn't change the title.
                if (rcnoReviewSc.lastSearch == search)
                    return;

                rcnoReviewSc.lastSearch = search;
                waiting = t.parent().find('.spinner').show();

                rivers.search.change(search);
                rivers.search.ajax(function () {
                    waiting.hide();
                });
            } else {
                rivers.search.hide();
                rivers.recent.show();
            }
        },

        next: function () {
            rivers.search.next();
            rivers.recent.next();
        },

        prev: function () {
            rivers.search.prev();
            rivers.recent.prev();
        },

        keydown: function (event) {
            var fn, id,
                key = $.ui.keyCode;

            if (key.ESCAPE === event.keyCode) {
                rcnoReviewSc.close();
                event.stopImmediatePropagation();
            } else if (key.TAB === event.keyCode) {
                id = event.target.id;

                // wp-link-submit must always be the last focusable element in the dialog.
                // following focusable elements will be skipped on keyboard navigation.
                if (id === 'wp-link-submit' && !event.shiftKey) {
                    inputs.close.focus();
                    event.preventDefault();
                } else if (id === 'wp-link-close' && event.shiftKey) {
                    inputs.submit.focus();
                    event.preventDefault();
                }
            }

            if (event.keyCode !== key.UP && event.keyCode !== key.DOWN) {
                return;
            }

            if (document.activeElement &&
                ( document.activeElement.id === 'link-title-field' || document.activeElement.id === 'url-field' )) {
                return;
            }

            fn = event.keyCode === key.UP ? 'prev' : 'next';
            clearInterval(rcnoReviewSc.keyInterval);
            rcnoReviewSc[fn]();
            rcnoReviewSc.keyInterval = setInterval(rcnoReviewSc[fn], rcnoReviewSc.keySensitivity);
            event.preventDefault();
        },

        keyup: function (event) {
            var key = $.ui.keyCode;

            if (event.which === key.UP || event.which === key.DOWN) {
                clearInterval(rcnoReviewSc.keyInterval);
                event.preventDefault();
            }
        },

        delayedCallback: function (func, delay) {
            var timeoutTriggered, funcTriggered, funcArgs, funcContext;

            if (!delay)
                return func;

            setTimeout(function () {
                if (funcTriggered)
                    return func.apply(funcContext, funcArgs);
                // Otherwise, wait.
                timeoutTriggered = true;
            }, delay);

            return function () {
                if (timeoutTriggered)
                    return func.apply(this, arguments);
                // Otherwise, wait.
                funcArgs = arguments;
                funcContext = this;
                funcTriggered = true;
            };
        }
    };

    River = function (element, search) {
        var self = this;
        this.element = element;
        this.ul = element.children('ul');
        this.contentHeight = element.children('#link-selector-height');
        this.waiting = element.find('.river-waiting');

        this.change(search);
        this.refresh();

        $('#rcno-link .query-results, #rcno-link #link-selector').scroll(function () {
            self.maybeLoad();
        });
        element.on('click', 'li', function (event) {
            self.select($(this), event);
        });
    };

    $.extend(River.prototype, {
        refresh: function () {
            this.deselect();
            this.visible = this.element.is(':visible');
        },
        show: function () {
            if (!this.visible) {
                this.deselect();
                this.element.show();
                this.visible = true;
            }
        },
        hide: function () {
            this.element.hide();
            this.visible = false;
        },
        // Selects a list item and triggers the river-select event.
        select: function (li, event) {
            var liHeight, elHeight, liTop, elTop;

            if (li.hasClass('unselectable') || li == this.selected)
                return;

            this.deselect();
            this.selected = li.addClass('selected');
            // Make sure the element is visible
            liHeight = li.outerHeight();
            elHeight = this.element.height();
            liTop = li.position().top;
            elTop = this.element.scrollTop();

            if (liTop < 0) // Make first visible element
                this.element.scrollTop(elTop + liTop);
            else if (liTop + liHeight > elHeight) // Make last visible element
                this.element.scrollTop(elTop + liTop - elHeight + liHeight);

            // Trigger the river-select event
            this.element.trigger('river-select', [li, event, this]);
        },
        deselect: function () {
            if (this.selected)
                this.selected.removeClass('selected');
            this.selected = false;
        },
        prev: function () {
            if (!this.visible)
                return;

            var to;
            if (this.selected) {
                to = this.selected.prev('li');
                if (to.length)
                    this.select(to);
            }
        },
        next: function () {
            if (!this.visible)
                return;

            var to = this.selected ? this.selected.next('li') : $('li:not(.unselectable):first', this.element);
            if (to.length)
                this.select(to);
        },
        ajax: function (callback) {
            var self = this,
                delay = this.query.page == 1 ? 0 : rcnoReviewSc.minRiverAJAXDuration,
                response = rcnoReviewSc.delayedCallback(function (results, params) {
                    self.process(results, params);
                    if (callback)
                        callback(results, params);
                }, delay);

            this.query.ajax(response);
        },
        change: function (search) {
            if (this.query && this._search == search)
                return;

            this._search = search;
            this.query = new Query(search);
            this.element.scrollTop(0);
        },
        process: function (results, params) {
            var list = '', alt = true, classes = '',
                firstPage = params.page == 1;

            if (!results) {
                if (firstPage) {
                    list += '<li class="unselectable no-matches-found"><span class="item-title"><em>' +
                        rcnoReviewsScL10n.noMatchesFound + '</em></span></li>';
                }
            } else {
                $.each(results, function () {
                    classes = alt ? 'alternate' : '';
                    classes += this.title ? '' : ' no-title';
                    list += classes ? '<li class="' + classes + '">' : '<li>';
                    list += '<input type="hidden" class="item-id" value="' + this.id + '" />';
                    list += '<span class="item-title">';
                    list += this.title ? this.title : rcnoReviewsScL10n.noTitle;
                    list += '</span><span class="item-info">' + rcnoReviewsScL10n.review + '</span></li>';
                    alt = !alt;
                });
            }

            this.ul[firstPage ? 'html' : 'append'](list);
        },
        maybeLoad: function () {
            var self = this,
                el = this.element,
                bottom = el.scrollTop() + el.height();

            if (!this.query.ready() || bottom < this.contentHeight.height() - rcnoReviewSc.riverBottomThreshold)
                return;

            setTimeout(function () {
                var newTop = el.scrollTop(),
                    newBottom = newTop + el.height();

                if (!self.query.ready() || newBottom < self.contentHeight.height() - rcnoReviewSc.riverBottomThreshold)
                    return;

                self.waiting.show();
                el.scrollTop(newTop + self.waiting.outerHeight());

                self.ajax(function () {
                    self.waiting.hide();
                });
            }, rcnoReviewSc.timeToTriggerRiver);
        }
    });


    Query = function (search) {
        this.page = 1;
        this.allLoaded = false;
        this.querying = false;
        this.search = search;
    };

    $.extend(Query.prototype, {
        ready: function () {
            return !( this.querying || this.allLoaded );
        },
        ajax: function (callback) {
            var self = this,
                query = {
                    action: 'rcno_get_results',
                    page: this.page,
                    'rcno_ajax_nonce': inputs.nonce
                };

            if (this.search)
                query.search = this.search;

            this.querying = true;

            $.post(ajaxurl, query, function (r) {
                self.page++;
                self.querying = false;
                self.allLoaded = !r;
                callback(r, query);
            }, 'json');
        }
    });

    $(document).ready(rcnoReviewSc.init);
})(jQuery);