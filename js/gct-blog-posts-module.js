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
            
            // Handle category filter change
            $module.find('.gct-category-select').on('change', function() {
                const $select = $(this);
                const $container = $select.closest('.gct-blog-posts-container');
                const postType = $container.data('post-type');
                const postsPerPage = $container.data('posts-per-page');
                const taxonomy = $select.data('taxonomy');
                const categoryId = $select.val();
                
                // Update URL parameter without reloading
                updateUrlParam('gct_category', categoryId);
                
                // Get and update posts via AJAX
                getFilteredPosts($container, postType, taxonomy, categoryId, postsPerPage, 1);
            });
            
            // Handle pagination clicks
            $module.find('.gct-pagination a').on('click', function(e) {
                e.preventDefault();
                
                const $link = $(this);
                const $container = $link.closest('.gct-blog-posts-container');
                const postType = $container.data('post-type');
                const postsPerPage = $container.data('posts-per-page');
                const page = $link.data('page');
                let categoryId = 'all';
                let taxonomy = 'category';
                
                // Get current category if set
                const $select = $container.find('.gct-category-select');
                if ($select.length) {
                    categoryId = $select.val();
                    taxonomy = $select.data('taxonomy');
                }
                
                // Update URL parameter without reloading
                updateUrlParam('gct_page', page);
                
                // Get and update posts via AJAX
                getFilteredPosts($container, postType, taxonomy, categoryId, postsPerPage, page);
            });
        });
    }
    
    /**
     * Get filtered posts via AJAX
     */
    function getFilteredPosts($container, postType, taxonomy, categoryId, postsPerPage, page) {
        const $postsWrapper = $container.find('.gct-posts-wrapper');
        
        // Add loading state
        $postsWrapper.css('opacity', 0.5);
        
        const data = {
            action: 'gct_get_filtered_posts',
            nonce: gct_blog_posts_params.nonce,
            post_type: postType,
            taxonomy: taxonomy,
            category_id: categoryId,
            posts_per_page: postsPerPage,
            page: page
        };
        
        $.ajax({
            url: gct_blog_posts_params.ajaxurl,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    $postsWrapper.html(response.data.html);
                    
                    // Scroll to top of container
                    $('html, body').animate({
                        scrollTop: $container.offset().top - 100
                    }, 500);
                }
                
                // Remove loading state
                $postsWrapper.css('opacity', 1);
            },
            error: function() {
                // Remove loading state
                $postsWrapper.css('opacity', 1);
            }
        });
    }
    
    /**
     * Update URL parameter without page reload
     */
    function updateUrlParam(key, value) {
        if (history.pushState) {
            let searchParams = new URLSearchParams(window.location.search);
            searchParams.set(key, value);
            let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + searchParams.toString();
            window.history.pushState({path: newurl}, '', newurl);
        }
    }
})(jQuery); 