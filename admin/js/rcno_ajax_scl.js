/**
 * The jquery class handling the shortcode insertion for review listings
 *
 * @since      1.0.0
 * @package    Rcno_Reviews
 * @subpackage Rcno_Reviews/admin
 * @author     wzyMedia <wzy@outlook.com>
 */
var rcnoListingsSc;

(function ($) {
    var editor,
        inputs = {},
        isTouch = ( 'ontouchend' in document ),
        body = $('body');

    rcnoListingsSc = {
        timeToTriggerRiver: 150,
        minRiverAJAXDuration: 200,
        riverBottomThreshold: 5,
        keySensitivity: 100,
        lastSearch: '',
        textarea: '',

        init: function () {
            inputs.wrap = $('#rcno-modal-wrap-scl');
            inputs.dialog = $('#rcno-modal-form-scl');
            inputs.backdrop = $('#rcno-modal-backdrop-scl');
            inputs.submit = $('#rcno-modal-submit-scl');
            inputs.close = $('#rcno-modal-close-scl');
            // URL
            inputs.id = $('#review-id-field');
            inputs.nonce = window.rcno_vars.rcno_ajax_nonce; // $('#rcno_ajax_nonce');
            // Secondary options
            inputs.title = $('#review-title-field');
            // Advanced Options
            inputs.taxonomy = $('#review-taxonomy');

            // Bind event handlers
            inputs.dialog.keydown(rcnoListingsSc.keydown);
            inputs.dialog.keyup(rcnoListingsSc.keyup);
            inputs.submit.click(function (event) {
                event.preventDefault();
                rcnoListingsSc.update();
            });
            inputs.close.add(inputs.backdrop).add('#rcno-modal-cancel-scl a').click(function (event) {
                event.preventDefault();
                rcnoListingsSc.close();
            });
            body.on('click', '#review-taxonomy', function (event) {
                event.preventDefault();
                $('input:radio[name=rcno-modal-scl-mode]')[0].checked = true;
            });

            /* Button to open the modal dialog */
            body.on('click', '#rcno-add-listings-button', function (event) {
                editor_id = jQuery('#rcno-add-listings-button').attr("data_editor");
                window.rcnoListingsSc.open(editor_id);
            });
        },

        open: function (editorId) {
            var ed;

            rcnoListingsSc.range = null;

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

            if (!rcnoListingsSc.isMCE() && document.selection) {
                this.textarea.focus();
                this.range = document.selection.createRange();
            }

            inputs.wrap.show();
            inputs.backdrop.show();

            rcnoListingsSc.refresh();
            $(document).trigger('rcnoListingsSc-open', inputs.wrap);
        },

        isMCE: function () {
            return editor && !editor.isHidden();
        },

        refresh: function () {
//		// Refresh rivers (clear links, check visibility)
//		rivers.search.refresh();
//		rivers.recent.refresh();

            if (rcnoListingsSc.isMCE()) {
                rcnoListingsSc.mceRefresh();
            } else {
                rcnoListingsSc.setDefaultValues();
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
                inputs.submit.val(rcnoListingsSc.update);

                // If there's no link, set the default values.
            } else {
                rcnoListingsSc.setDefaultValues();
            }
        },

        close: function () {
            if (!rcnoListingsSc.isMCE()) {
                rcnoListingsSc.textarea.focus();

                if (rcnoListingsSc.range) {
                    rcnoListingsSc.range.moveToBookmark(rcnoListingsSc.range.getBookmark());
                    rcnoListingsSc.range.select();
                }
            } else {
                editor.focus();
            }

            inputs.backdrop.hide();
            inputs.wrap.hide();
            $(document).trigger('rcnoListingsSc-close', inputs.wrap);
        },

        update: function () {
            if (rcnoListingsSc.isMCE()) {
                rcnoListingsSc.mceUpdate();
            } else {
                rcnoListingsSc.htmlUpdate();
            }
        },

        //Build the shortcode here!
        htmlUpdate: function () {
            var attrs, html, begin, end, cursor, title, selection,
                textarea = rcnoListingsSc.textarea;

            if (! textarea) {
              return;
            }

            var out = "[";

            sel = $("input[name='rcno-modal-scl-mode']:checked");
            switch (sel.val()) {
                case 'rcno-tax-list':
                    out += "rcno-tax-list ";
                    out += "tax=\"" + $("#rcno-modal-form-scl select option:selected").val() + "\"";
                    break;
                case 'rcno-reviews-index':
                    out += "rcno-reviews-index";
                    break;
                case 'rcno-reviews-grid':
                    out += "rcno-reviews-grid";
                    break;
				case 'rcno-reviews-isotope':
					out += "rcno-sortable-grid";
					break;
                default:
                    alert( 'Error' + sel.val());
                    return;
            }

            out += "]\n";

            // Insert Shortcode
            if (document.selection && rcnoListingsSc.range) {
                // IE
                // Note: If no text is selected, IE will not place the cursor
                //       inside the closing tag.
                textarea.focus();
                rcnoListingsSc.range.text = out;
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

            rcnoListingsSc.close();
            textarea.focus();
        },

        mceUpdate: function () {
            var link;
            var out = "[";
            sel = $("input[name='rcno-modal-scl-mode']:checked");
            switch (sel.val()) {
                case 'rcno-tax-list':
                    out += 'rcno-tax-list ';
                    out += 'tax="' + $('#rcno-modal-form-scl select option:selected').val() + '"';
                    break;
                case 'rcno-reviews-index':
                    out += 'rcno-reviews-index';
                    break;
                case 'rcno-reviews-grid':
                    out += 'rcno-reviews-grid';
                    break;
                case 'rcno-reviews-isotope':
                    out += 'rcno-sortable-grid';
                    break;
                default:
                    alert('Error:' + sel.val());
                    return;
            }
            out += "]<br/>";

            rcnoListingsSc.close();
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
            inputs.submit.val(rcnoListingsScL10n.save);
        },

        keydown: function (event) {
            var fn, id,
                key = $.ui.keyCode;

            if (key.ESCAPE === event.keyCode) {
                rcnoListingsSc.close();
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
            clearInterval(rcnoListingsSc.keyInterval);
            rcnoListingsSc[fn]();
            rcnoListingsSc.keyInterval = setInterval(rcnoListingsSc[fn], rcnoListingsSc.keySensitivity);
            event.preventDefault();
        },

        keyup: function (event) {
            var key = $.ui.keyCode;

            if (event.which === key.UP || event.which === key.DOWN) {
                clearInterval(rcnoListingsSc.keyInterval);
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
    $(document).ready(rcnoListingsSc.init);
})(jQuery);
