!(function($, undefined) {
    'use strict';

    $(function() {
        var $docs = $('.docs'),
            $docsToc = $('.docs-toc');

        if (!$docsToc.length) {
            return;
        }
        
        var docsTocTop = $docsToc.offset().top - 50;

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