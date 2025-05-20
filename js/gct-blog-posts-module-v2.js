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

        // Remove duplicate event handlers to prevent conflicts
        $(document).off('change', '.gct-category-filter');
        $(document).off('change', '.gct-category-select');
        
        // Handle category filter change
        $(document).on('change', '.gct-category-select', function() {
            var $select = $(this);
            var $container = $select.closest('.gct-blog-posts-container');
            var $postsWrapper = $container.find('.gct-posts-wrapper');
            var postType = $container.data('post-type');
            var categoryId = $select.val();
            var postsPerPage = $container.data('posts-per-page');
            var moduleSettings = $container.data('module-settings');
            var taxonomy = $select.data('taxonomy') || 'category';
            
            // Update URL parameter without reloading
            updateUrlParam('gct_category', categoryId);
            
            // Store the current posts per row value to maintain grid layout
            var currentPostsPerRow = $container.find('.gct-blog-posts-grid').attr('class').match(/posts-per-row-(\d+)/)[1];
            
            // Show loading state immediately
            $postsWrapper.addClass('loading');
            
            // Clear existing content to show loading state more clearly
            $postsWrapper.find('.gct-blog-posts-grid').html('<div class="loading-indicator"></div>');
            
            // Make AJAX request
            $.ajax({
                url: gct_blog_posts_params.ajaxurl,
                type: 'POST',
                data: {
                    action: 'gct_get_filtered_posts',
                    nonce: gct_blog_posts_params.nonce,
                    post_type: postType,
                    category_id: categoryId,
                    taxonomy: taxonomy,
                    posts_per_page: postsPerPage,
                    page: 1,
                    module_settings: moduleSettings
                },
                success: function(response) {
                    if (response.success) {
                        // Update posts wrapper
                        $postsWrapper.html(response.data.html);
                        
                        // Ensure the correct posts-per-row class is maintained
                        $postsWrapper.find('.gct-blog-posts-grid').addClass('posts-per-row-' + currentPostsPerRow);
                        
                        // Ensure post items have the correct styling
                        $postsWrapper.find('.gct-post-item').each(function() {
                            var $post = $(this);
                            
                            // Make sure all posts have the right structure
                            if (!$post.find('.gct-post-overlay').length) {
                                $post.find('.gct-post-thumbnail').append('<div class="gct-post-overlay"></div>');
                            }
                            
                            // Ensure post content is set to flex
                            $post.find('.gct-post-content').css({
                                'display': 'flex',
                                'flex-direction': 'column'
                            });
                            
                            // Ensure category has proper styling
                            $post.find('.gct-post-category').css({
                                'color': '#F7941C',
                                'font-weight': '600',
                                'text-transform': 'uppercase',
                                'letter-spacing': '0.5px'
                            });
                        });
                        
                        // Apply any module settings that might be needed
                        applyModuleSettings($container, moduleSettings);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    // Show error message in the posts wrapper
                    $postsWrapper.html('<p class="gct-no-posts">Error loading posts. Please try again.</p>');
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
        
        // Store the current posts per row value to maintain grid layout
        const currentPostsPerRow = $container.find('.gct-blog-posts-grid').attr('class').match(/posts-per-row-(\d+)/)?.[1] || 3;
        
        // Add loading state to wrapper
        $postsWrapper.addClass('loading');
        
        // If not appending, show loading indicator
        if (!append) {
            $postsWrapper.find('.gct-blog-posts-grid').html('<div class="loading-indicator"></div>');
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
            module_settings: moduleSettings,
            load_more: append
        };
        
        $.ajax({
            url: gct_blog_posts_params.ajaxurl,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    if (append) {
                        // Extract and append the new posts only
                        const $newPosts = $(response.data.posts_html);
                        
                        // Properly style each new post before appending
                        $newPosts.each(function() {
                            const $post = $(this);
                            
                            // Make sure overlay is correct
                            if (!$post.find('.gct-post-overlay').length) {
                                $post.find('.gct-post-thumbnail').append('<div class="gct-post-overlay"></div>');
                            }
                            
                            // Ensure post content is set to flex
                            $post.find('.gct-post-content').css({
                                'display': 'flex',
                                'flex-direction': 'column'
                            });
                            
                            // Ensure category has proper styling
                            $post.find('.gct-post-category').css({
                                'color': '#F7941C',
                                'font-weight': '600',
                                'text-transform': 'uppercase',
                                'letter-spacing': '0.5px'
                            });
                        });
                        
                        // Append the new posts to the existing grid
                        $container.find('.gct-blog-posts-grid').append($newPosts);
                        
                        // Update button state
                        if (!response.data.has_more) {
                            $container.find('.gct-pagination').remove();
                        }
                    } else {
                        // Replace the entire content
                        $postsWrapper.html(response.data.html);
                        
                        // Ensure the grid has the correct posts-per-row class
                        $postsWrapper.find('.gct-blog-posts-grid').addClass('posts-per-row-' + currentPostsPerRow);
                        
                        // Ensure all posts have the correct styling
                        $postsWrapper.find('.gct-post-item').each(function() {
                            const $post = $(this);
                            
                            // Make sure all posts have the right structure
                            if (!$post.find('.gct-post-overlay').length) {
                                $post.find('.gct-post-thumbnail').append('<div class="gct-post-overlay"></div>');
                            }
                            
                            // Ensure post content is set to flex
                            $post.find('.gct-post-content').css({
                                'display': 'flex',
                                'flex-direction': 'column'
                            });
                            
                            // Ensure category has proper styling
                            $post.find('.gct-post-category').css({
                                'color': '#F7941C',
                                'font-weight': '600',
                                'text-transform': 'uppercase',
                                'letter-spacing': '0.5px'
                            });
                        });
                    }
                    
                    // Apply any module settings that might be needed
                    applyModuleSettings($container, moduleSettings);
                } else {
                    console.error('Error loading posts:', response.data?.message || 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
            },
            complete: function() {
                $postsWrapper.removeClass('loading');
            }
        });
    }
    
    /**
     * Apply module settings to control visibility of elements
     */
    function applyModuleSettings($container, settings) {
        // Parse settings if they're a string
        if (typeof settings === 'string') {
            try {
                settings = JSON.parse(settings);
            } catch (e) {
                console.error('Error parsing module settings:', e);
                return;
            }
        }
        
        if (!settings) return;
        
        const $posts = $container.find('.gct-post-item');
        
        // Apply visibility settings
        if (settings.show_excerpt === 'off') {
            $posts.find('.gct-post-excerpt').hide();
        } else {
            $posts.find('.gct-post-excerpt').show();
        }
        
        if (settings.show_date === 'off') {
            $posts.find('.gct-post-date').hide();
            $posts.find('.gct-post-meta').hide();
        } else {
            $posts.find('.gct-post-date').show();
            $posts.find('.gct-post-meta').show();
        }
        
        if (settings.show_category === 'off') {
            $posts.find('.gct-post-meta-top').hide();
            $posts.find('.gct-post-category').hide();
        } else {
            $posts.find('.gct-post-meta-top').show();
            $posts.find('.gct-post-category').show();
        }
        
        // Apply specific styles for event category posts
        $posts.each(function() {
            const $post = $(this);
            
            // Check for event date/location elements to identify event posts
            if ($post.find('.gct-event-date, .gct-event-location').length > 0) {
                // Apply specific styles for event posts
                $post.find('.gct-event-date, .gct-event-location').css({
                    'font-size': '14px',
                    'color': '#666',
                    'margin-bottom': '8px',
                    'line-height': '1.4',
                    'margin-top': '5px'
                });
                
                $post.find('.gct-event-label').css({
                    'font-weight': '600',
                    'color': '#254B45'
                });
            }
            
            // Common styles for all posts
            $post.find('.gct-post-title a').css({
                'color': '#254B45',
                'text-decoration': 'none',
                'transition': 'color 0.3s ease'
            });
            
            $post.find('.gct-post-excerpt').css({
                'font-size': '14px',
                'line-height': '1.5',
                'color': '#666',
                'margin-top': 'auto'
            });
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