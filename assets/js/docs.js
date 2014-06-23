!(function($, undefined) {
    'use strict';

    $(function() {
        var $docs = $('.docs'),
            $docsToc = $('.docs-toc'),
            docsTocTop = $docsToc.offset().top - 50;
        if (!$docsToc.length) {
            return;
        }

        $docs.on('scroll', function() {
            var scrollTop = $docs.scrollTop();
            if (scrollTop >= docsTocTop) {
                $docsToc.addClass('fixed');
            } else {
                $docsToc.removeClass('fixed');
            }
        });
    });
})(jQuery);