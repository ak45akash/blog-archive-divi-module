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
                
                // When changing category, reset to page 1 and replace content
                getFilteredPosts($container, postType, taxonomy, categoryId, postsPerPage, 1, false);
            });
            
            // Handle "See more" button clicks
            $module.find('.gct-pagination').on('click', '.gct-load-more', function(e) {
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
                
                // Get and append additional posts
                getFilteredPosts($container, postType, taxonomy, categoryId, postsPerPage, page, true);
            });
        });
    }
    
    /**
     * Get filtered posts via AJAX
     */
    function getFilteredPosts($container, postType, taxonomy, categoryId, postsPerPage, page, append = false) {
        const $postsWrapper = $container.find('.gct-posts-wrapper');
        const $loadMoreButton = $container.find('.gct-load-more');
        
        // Add loading state
        if ($loadMoreButton.length) {
            $loadMoreButton.text('Loading...').addClass('loading');
        }
        
        // Get module settings from data attribute to maintain consistency
        let moduleSettings = $container.data('module-settings');
        
        const data = {
            action: 'gct_get_filtered_posts',
            nonce: gct_blog_posts_params.nonce,
            post_type: postType,
            taxonomy: taxonomy,
            category_id: categoryId,
            posts_per_page: postsPerPage,
            page: page,
            module_settings: moduleSettings
        };
        
        $.ajax({
            url: gct_blog_posts_params.ajaxurl,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    // If we're appending posts (See more button)
                    if (append) {
                        // Extract the posts from the HTML response
                        const $newContent = $(response.data.html);
                        const $newPosts = $newContent.find('.gct-post-item');
                        
                        // Make sure all new posts have the correct structure
                        $newPosts.each(function() {
                            const $post = $(this);
                            
                            // Ensure all posts have the post overlay
                            if ($post.find('.gct-post-thumbnail .gct-post-overlay').length === 0) {
                                $post.find('.gct-post-thumbnail').append('<div class="gct-post-overlay"></div>');
                            }
                            
                            // Ensure the proper structure for post meta, categories, and excerpt
                            if ($post.find('.gct-post-meta').length === 0) {
                                const $date = $post.find('.gct-post-date');
                                if ($date.length && !$date.closest('.gct-post-meta').length) {
                                    $date.wrap('<div class="gct-post-meta"></div>');
                                }
                            }
                        });
                        
                        // Append the new posts to the existing grid
                        $container.find('.gct-blog-posts-grid').append($newPosts);
                        
                        // Check if there's pagination in the response
                        const $newPagination = $newContent.find('.gct-pagination');
                        
                        // If there's no pagination in the response, remove the existing pagination
                        // This means we've loaded all posts
                        if ($newPagination.length === 0) {
                            $container.find('.gct-pagination').remove();
                        } else {
                            // Otherwise, replace the pagination (for updating the See more button)
                            $container.find('.gct-pagination').replaceWith($newPagination);
                        }
                    } else {
                        // Replace the entire content (category change)
                        $postsWrapper.html(response.data.html);
                    }
                }
                
                // Remove loading state if the button still exists
                if ($loadMoreButton.length) {
                    $loadMoreButton.removeClass('loading');
                }
            },
            error: function() {
                // Remove loading state
                if ($loadMoreButton.length) {
                    $loadMoreButton.text('See more').removeClass('loading');
                }
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