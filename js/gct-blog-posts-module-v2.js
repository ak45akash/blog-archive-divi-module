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

        // Handle category filter change
        $('.gct-category-filter').on('change', function() {
            var $filter = $(this);
            var $container = $filter.closest('.gct-blog-posts-container');
            var $postsWrapper = $container.find('.gct-posts-wrapper');
            var postType = $filter.data('post-type');
            var categoryId = $filter.val();
            var postsPerPage = $container.data('posts-per-page');
            var moduleSettings = $container.data('module-settings');
            
            // Show loading state
            $postsWrapper.addClass('loading');
            
            // Make AJAX request
            $.ajax({
                url: gct_blog_posts_params.ajaxurl,
                type: 'POST',
                data: {
                    action: 'gct_get_filtered_posts',
                    nonce: gct_blog_posts_params.nonce,
                    post_type: postType,
                    category_id: categoryId,
                    posts_per_page: postsPerPage,
                    page: 1,
                    module_settings: moduleSettings
                },
                success: function(response) {
                    if (response.success) {
                        // Update posts wrapper
                        $postsWrapper.html(response.data.html);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                },
                complete: function() {
                    $postsWrapper.removeClass('loading');
                }
            });
        });

        // Handle load more button
        $(document).on('click', '.gct-load-more', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $container = $button.closest('.gct-blog-posts-container');
            var $postsGrid = $container.find('.gct-blog-posts-grid');
            var postType = $container.data('post-type');
            var categoryId = $container.find('.gct-category-filter').val() || 'all';
            var postsPerPage = $container.data('posts-per-page');
            var currentPage = parseInt($button.data('current-page'));
            var nextPage = currentPage + 1;
            var moduleSettings = $container.data('module-settings');
            
            // Show loading state
            $button.addClass('loading');
            
            // Make AJAX request
            $.ajax({
                url: gct_blog_posts_params.ajaxurl,
                type: 'POST',
                data: {
                    action: 'gct_get_filtered_posts',
                    nonce: gct_blog_posts_params.nonce,
                    post_type: postType,
                    category_id: categoryId,
                    posts_per_page: postsPerPage,
                    page: nextPage,
                    module_settings: moduleSettings,
                    load_more: true
                },
                success: function(response) {
                    if (response.success) {
                        // Append new posts
                        $postsGrid.append(response.data.posts_html);
                        
                        // Update or remove button based on pagination
                        if (response.data.has_more) {
                            $button.data('current-page', nextPage);
                        } else {
                            $button.parent().remove();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                },
                complete: function() {
                    $button.removeClass('loading');
                }
            });
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
            
            // Handle numbered pagination clicks
            $module.find('.gct-pagination').on('click', 'a:not(.gct-load-more):not(.gct-pagination-prev):not(.gct-pagination-next)', function(e) {
                e.preventDefault();
                
                const $link = $(this);
                if ($link.hasClass('active')) {
                    return false;
                }
                
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
                
                // Get and replace content
                getFilteredPosts($container, postType, taxonomy, categoryId, postsPerPage, page, false);
            });
            
            // Handle prev/next pagination clicks
            $module.find('.gct-pagination').on('click', '.gct-pagination-prev, .gct-pagination-next', function(e) {
                e.preventDefault();
                
                const $link = $(this);
                if ($link.hasClass('disabled')) {
                    return false;
                }
                
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
                
                // Get and replace content
                getFilteredPosts($container, postType, taxonomy, categoryId, postsPerPage, page, false);
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
                    if (append) {
                        // Extract and append the new posts only
                        const $newContent = $(response.data.html);
                        const $newPosts = $newContent.find('.gct-post-item');
                        
                        // Ensure new posts have the correct structure and styling before appending
                        $newPosts.each(function() {
                            const $post = $(this);
                            
                            // Make sure overlay has the correct structure
                            const $thumbnail = $post.find('.gct-post-thumbnail');
                            const $overlay = $thumbnail.find('.gct-post-overlay');
                            
                            // If overlay is missing or malformed, recreate it
                            if ($overlay.length === 0) {
                                $thumbnail.append('<div class="gct-post-overlay"></div>');
                            }
                            
                            // Ensure category tag has correct positioning
                            const $metaTop = $post.find('.gct-post-meta-top');
                            if ($metaTop.length && !$metaTop.is(':visible')) {
                                $metaTop.css('display', 'block');
                            }
                            
                            // Ensure metadata is properly displayed
                            const $postContent = $post.find('.gct-post-content');
                            const $meta = $postContent.find('.gct-post-meta');
                            if ($meta.length === 0) {
                                $postContent.prepend('<div class="gct-post-meta"></div>');
                            }
                        });
                        
                        // Preserve module settings for show/hide elements
                        if (moduleSettings) {
                            const settings = typeof moduleSettings === 'string' 
                                ? JSON.parse(moduleSettings) 
                                : moduleSettings;
                                
                            // IMPORTANT: For "on" values, we explicitly show the elements to ensure they're visible
                            if (settings.show_excerpt === 'off') {
                                $newPosts.find('.gct-post-excerpt').hide();
                            } else {
                                $newPosts.find('.gct-post-excerpt').show();
                            }
                            
                            if (settings.show_date === 'off') {
                                $newPosts.find('.gct-post-date').hide();
                            } else {
                                // Explicitly show the date elements
                                $newPosts.find('.gct-post-date').show();
                                // Also ensure the .gct-post-meta container is visible
                                $newPosts.find('.gct-post-meta').show();
                            }
                            
                            if (settings.show_category === 'off') {
                                $newPosts.find('.gct-post-meta-top').hide();
                                $newPosts.find('.gct-post-category').hide();
                            } else {
                                // Explicitly show the category elements
                                $newPosts.find('.gct-post-meta-top').show();
                                $newPosts.find('.gct-post-category').show();
                            }
                        }
                        
                        // Append the new posts to the existing grid
                        $container.find('.gct-blog-posts-grid').append($newPosts);
                        
                        // Update pagination
                        const $newPagination = $newContent.find('.gct-pagination');
                        if ($newPagination.length === 0) {
                            $container.find('.gct-pagination').remove();
                        } else {
                            $container.find('.gct-pagination').replaceWith($newPagination);
                        }
                    } else {
                        // Replace the entire content but preserve module settings
                        $postsWrapper.html(response.data.html);
                        
                        // Ensure all posts in the new content have proper structure
                        $postsWrapper.find('.gct-post-item').each(function() {
                            const $post = $(this);
                            
                            // Make sure overlay has the correct structure
                            const $thumbnail = $post.find('.gct-post-thumbnail');
                            const $overlay = $thumbnail.find('.gct-post-overlay');
                            
                            // If overlay is missing or malformed, recreate it
                            if ($overlay.length === 0) {
                                $thumbnail.append('<div class="gct-post-overlay"></div>');
                            }
                            
                            // Ensure category tag has correct positioning
                            const $metaTop = $post.find('.gct-post-meta-top');
                            if ($metaTop.length && !$metaTop.is(':visible')) {
                                $metaTop.css('display', 'block');
                            }
                        });
                        
                        // Apply module settings after content refresh
                        if (moduleSettings) {
                            const settings = typeof moduleSettings === 'string' 
                                ? JSON.parse(moduleSettings) 
                                : moduleSettings;
                                
                            // IMPORTANT: For "on" values, we explicitly show the elements to ensure they're visible
                            if (settings.show_excerpt === 'off') {
                                $postsWrapper.find('.gct-post-excerpt').hide();
                            } else {
                                $postsWrapper.find('.gct-post-excerpt').show();
                            }
                            
                            if (settings.show_date === 'off') {
                                $postsWrapper.find('.gct-post-date').hide();
                            } else {
                                // Explicitly show the date elements
                                $postsWrapper.find('.gct-post-date').show();
                                // Also ensure the .gct-post-meta container is visible
                                $postsWrapper.find('.gct-post-meta').show();
                            }
                            
                            if (settings.show_category === 'off') {
                                $postsWrapper.find('.gct-post-meta-top').hide();
                                $postsWrapper.find('.gct-post-category').hide();
                            } else {
                                // Explicitly show the category elements
                                $postsWrapper.find('.gct-post-meta-top').show();
                                $postsWrapper.find('.gct-post-category').show();
                            }
                        }
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