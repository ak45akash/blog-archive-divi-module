(function($) {
    'use strict';
    
    /**
     * GCT Blog Posts Module Scripts
     */
    $(document).ready(function() {
        // Initialize the module functionality
        initGCTBlogPostsModule();
        
        // Also run after Divi's AJAX completion (for builder preview)
        $(document).on('ajaxComplete', function(event, xhr, settings) {
            initGCTBlogPostsModule();
        });
    });
    
    /**
     * Initialize the blog posts module
     */
    function initGCTBlogPostsModule() {
        $('.gct_blog_posts').each(function() {
            const $module = $(this);
            
            // Handle image loading
            $module.find('.gct-post-thumbnail').each(function() {
                const $thumbnail = $(this);
                const bgImage = $thumbnail.css('background-image');
                
                if (bgImage && bgImage !== 'none') {
                    const url = bgImage.match(/url\(['"]?([^'"]+)['"]?\)/);
                    
                    if (url && url[1]) {
                        const img = new Image();
                        img.onload = function() {
                            $thumbnail.addClass('image-loaded');
                        };
                        img.src = url[1];
                    }
                } else {
                    $thumbnail.addClass('no-image');
                }
            });
            
            // Add animation classes for better performance
            $module.find('.gct-post-item').each(function(index) {
                const $item = $(this);
                
                // Add staggered animation delay
                setTimeout(function() {
                    $item.addClass('item-visible');
                }, index * 100);
            });
        });
    }
})(jQuery); 