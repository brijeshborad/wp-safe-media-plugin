(function ($) {
    'use strict';
console.log('Safe media js loaded');
    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    $(".safe-media-term-upload-image").on('click', function (event) {
console.log('herere');
        var frame;
        event.preventDefault();
        if (frame) {
            frame.open();
            return;
        }
        frame = wp.media({
            title: 'Select or Upload Media',
            button: {
                text: 'Use this image'
            },
            library: {
                type: [ 'image' ]
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });
        frame.on("select", function () {
            // Grab the selected attachment.
            var attachment = frame.state().get("selection").first();
            console.log(attachment);
            // $('#safe-media-image-preview-container').append(`<img src="${attachment.attributes.sizes?.medium?.url}" />`);
            $('#safe-media-term-image').val(attachment.attributes.id)
            $('#safe-media-term-image-url').val(attachment.attributes.url)

            frame.close();

        });
        frame.open();

    });

})(jQuery);
