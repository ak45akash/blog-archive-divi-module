<?php
/*
Plugin Name: GCT Blog Posts Module for Divi
Plugin URI: 
Description: A custom Divi module for displaying blog posts with filtering options
Version: 1.0.0
Author: 
Author URI: 
License: GPL2
Text Domain: gct-blog-posts-module
*/

if (!defined('ABSPATH')) {
    exit;
}

define('GCT_BPM_VERSION', '1.0.0');
define('GCT_BPM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GCT_BPM_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Initialize the Divi module
 */
function gct_blog_posts_initialize_extension() {
    if (class_exists('ET_Builder_Module')) {
        require_once GCT_BPM_PLUGIN_DIR . 'includes/GCTBlogPostsModule.php';
        
        if (function_exists('et_builder_add_main_css')) {
            et_builder_add_main_css('gct-blog-posts-module-style', GCT_BPM_PLUGIN_URL . 'css/gct-blog-posts-module.css');
        }
    }
}
add_action('et_builder_ready', 'gct_blog_posts_initialize_extension');

/**
 * Check if Divi theme or Divi Builder is active
 */
function gct_blog_posts_is_divi_active() {
    $theme = wp_get_theme();
    $is_divi_theme = ('Divi' === $theme->get('Name') || 'Divi' === $theme->get('Template'));
    $is_divi_builder = defined('ET_BUILDER_VERSION') || class_exists('ET_Builder_Plugin');
    return $is_divi_theme || $is_divi_builder;
}

/**
 * Admin notice for missing Divi
 */
function gct_blog_posts_admin_notice_missing_divi() {
    if (!gct_blog_posts_is_divi_active()) {
        $message = sprintf(
            esc_html__('"%1$s" requires Divi Theme or Divi Builder plugin to be installed and activated.', 'gct-blog-posts-module'),
            '<strong>' . esc_html__('GCT Blog Posts Module', 'gct-blog-posts-module') . '</strong>'
        );
        printf('<div class="notice notice-error"><p>%1$s</p></div>', $message);
    }
}
add_action('admin_notices', 'gct_blog_posts_admin_notice_missing_divi');

/**
 * Enqueue scripts and styles
 */
function gct_blog_posts_enqueue_scripts() {
    wp_enqueue_style('gct-blog-posts-module-style', GCT_BPM_PLUGIN_URL . 'css/gct-blog-posts-module.css', array(), GCT_BPM_VERSION);
    wp_enqueue_script('gct-blog-posts-module-script', GCT_BPM_PLUGIN_URL . 'js/gct-blog-posts-module.js', array('jquery'), GCT_BPM_VERSION, true);
    
    // Localize script for AJAX pagination and filtering
    wp_localize_script(
        'gct-blog-posts-module-script',
        'gct_blog_posts_params',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('gct_blog_posts_nonce'),
        )
    );
}
add_action('wp_enqueue_scripts', 'gct_blog_posts_enqueue_scripts');

/**
 * Plugin activation hook
 */
register_activation_hook(__FILE__, 'gct_blog_posts_activate');
function gct_blog_posts_activate() {
    if (!gct_blog_posts_is_divi_active()) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            esc_html__('This plugin requires Divi Theme or Divi Builder plugin to be installed and activated.', 'gct-blog-posts-module'),
            'Plugin dependency check',
            array('back_link' => true)
        );
    }
}

/**
 * Plugin deactivation hook
 */
register_deactivation_hook(__FILE__, 'gct_blog_posts_deactivate');
function gct_blog_posts_deactivate() {
    // Cleanup if needed
}

// Add AJAX handlers
add_action('wp_ajax_gct_get_filtered_posts', 'gct_get_filtered_posts');
add_action('wp_ajax_nopriv_gct_get_filtered_posts', 'gct_get_filtered_posts');

/**
 * AJAX handler for filtered posts
 */
function gct_get_filtered_posts() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'gct_blog_posts_nonce')) {
        wp_send_json_error(array('message' => 'Invalid security token.'));
    }
    
    // Get parameters
    $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'post';
    $taxonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : 'category';
    $category_id = isset($_POST['category_id']) ? sanitize_text_field($_POST['category_id']) : 'all';
    $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 6;
    $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
    
    // Query arguments
    $args = array(
        'post_type'      => $post_type,
        'posts_per_page' => $posts_per_page,
        'post_status'    => 'publish',
        'paged'          => $page,
    );
    
    // Add taxonomy query if category is selected
    if ($category_id !== 'all' && $taxonomy) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $category_id,
            ),
        );
    }
    
    // Get posts
    $query = new WP_Query($args);
    
    // Calculate total pages for pagination
    $total_pages = $query->max_num_pages;
    
    ob_start();
    
    // Start posts grid
    echo '<div class="gct-blog-posts-grid posts-per-row-3">';
    
    // Loop through posts
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            // Get featured image
            $has_thumbnail = has_post_thumbnail();
            $thumbnail = $has_thumbnail ? get_the_post_thumbnail_url(get_the_ID(), 'large') : '';
            $thumbnail_style = $has_thumbnail ? sprintf('style="background-image:url(%1$s);"', esc_url($thumbnail)) : '';
            
            // Get categories
            $categories = '';
            if ($post_type === 'post') {
                $post_categories = get_the_category();
                if (!empty($post_categories)) {
                    $categories = '<div class="gct-post-categories">';
                    foreach ($post_categories as $category) {
                        $categories .= sprintf(
                            '<span class="gct-post-category">%1$s</span>',
                            esc_html($category->name)
                        );
                    }
                    $categories .= '</div>';
                }
            } else {
                $taxonomies = get_object_taxonomies($post_type, 'objects');
                if (!empty($taxonomies)) {
                    $taxonomy_key = key($taxonomies);
                    $terms = get_the_terms(get_the_ID(), $taxonomy_key);
                    if (!empty($terms) && !is_wp_error($terms)) {
                        $categories = '<div class="gct-post-categories">';
                        foreach ($terms as $term) {
                            $categories .= sprintf(
                                '<span class="gct-post-category">%1$s</span>',
                                esc_html($term->name)
                            );
                        }
                        $categories .= '</div>';
                    }
                }
            }
            
            // Get date
            $date = sprintf(
                '<div class="gct-post-date">%1$s</div>',
                esc_html(get_the_date())
            );
            
            // Get excerpt
            $raw_excerpt = get_the_excerpt();
            $trimmed_excerpt = wp_trim_words($raw_excerpt, 30, '...');
            $excerpt = sprintf(
                '<div class="gct-post-excerpt">%1$s</div>',
                esc_html($trimmed_excerpt)
            );
            
            // Build post item
            echo '<article class="gct-post-item">';
            
            // Post thumbnail with overlay
            echo sprintf(
                '<a href="%1$s" class="gct-post-thumbnail" %2$s>
                    <div class="gct-post-overlay">
                        <span class="et-pb-icon">&#xe089;</span>
                    </div>
                </a>',
                esc_url(get_permalink()),
                $thumbnail_style
            );
            
            // Post content
            echo '<div class="gct-post-content">';
            
            // Post meta
            echo '<div class="gct-post-meta">';
            echo $categories;
            echo $date;
            echo '</div>';
            
            // Post title
            echo sprintf(
                '<h3 class="gct-post-title"><a href="%1$s">%2$s</a></h3>',
                esc_url(get_permalink()),
                esc_html(get_the_title())
            );
            
            // Post excerpt
            echo $excerpt;
            
            // Close post content
            echo '</div>';
            
            // Close post item
            echo '</article>';
        }
        
        // Reset post data
        wp_reset_postdata();
    } else {
        echo '<p class="gct-no-posts">' . esc_html__('No posts found.', 'gct-blog-posts-module') . '</p>';
    }
    
    // Close posts grid
    echo '</div>';
    
    // Add pagination
    echo '<div class="gct-pagination">';
    
    // Numbers pagination
    for ($i = 1; $i <= $total_pages; $i++) {
        $active_class = $i === $page ? ' active' : '';
        echo sprintf(
            '<a href="#" class="gct-page-number%2$s" data-page="%1$s">%1$s</a>',
            esc_attr($i),
            esc_attr($active_class)
        );
    }
    
    echo '</div>';
    
    $html = ob_get_clean();
    
    wp_send_json_success(array(
        'html' => $html,
        'page' => $page,
        'total_pages' => $total_pages,
    ));
} 