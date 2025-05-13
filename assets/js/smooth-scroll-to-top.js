(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        const $button = $('#sstt-button');
        const offset = parseInt(ssttOptions.offset) || 300;
        const speed = parseInt(ssttOptions.speed) || 800;
        const animation = ssttOptions.animation || 'fade';

        // Show/hide button based on scroll position
        function toggleButton() {
            if ($(window).scrollTop() > offset) {
                $button.addClass('sstt-visible');
            } else {
                $button.removeClass('sstt-visible');
            }
        }

        // Smooth scroll to top
        function scrollToTop() {
            $('html, body').animate({
                scrollTop: 0
            }, speed);
        }

        // Handle scroll event
        $(window).on('scroll', toggleButton);

        // Handle click event
        $button.on('click', function(e) {
            e.preventDefault();
            scrollToTop();
        });

        // Initial check
        toggleButton();

        // Handle hover color change
        $button.on('mouseenter', function() {
            $(this).css('background-color', ssttOptions.hover_color);
        }).on('mouseleave', function() {
            $(this).css('background-color', ssttOptions.background_color);
        });
    });
})(jQuery); 