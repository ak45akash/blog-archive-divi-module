<?php
/*
Plugin Name: GCT Blog Posts Module for Divi
Plugin URI: 
Description: A custom Divi module for displaying blog posts with filtering options
Version: 1.0.0
Author: Akash
Author URI: https://iakash.dev
License: GPL2
Text Domain: gct-blog-posts-module
*/

if (!defined('ABSPATH')) {
    exit;
}

define('GCT_BPM_VERSION', '1.0.1');
define('GCT_BPM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GCT_BPM_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Initialize the Divi module
 */
function gct_blog_posts_initialize_extension() {
    if (class_exists('ET_Builder_Module')) {
        require_once GCT_BPM_PLUGIN_DIR . 'includes/GCTBlogPostsModule.php';
        
        // Critical: instantiate the module here
        new GCT_BlogPostsModule();
        
        if (function_exists('et_builder_add_main_css')) {
            et_builder_add_main_css('gct-blog-posts-module-style-v2', GCT_BPM_PLUGIN_URL . 'css/gct-blog-posts-module-v2.css');
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
    wp_enqueue_style('gct-blog-posts-module-style-v2', GCT_BPM_PLUGIN_URL . 'css/gct-blog-posts-module-v2.css', array(), GCT_BPM_VERSION);
    wp_enqueue_script('gct-blog-posts-module-script-v2', GCT_BPM_PLUGIN_URL . 'js/gct-blog-posts-module-v2.js', array('jquery'), GCT_BPM_VERSION, true);
    
    // Localize script for AJAX pagination and filtering
    wp_localize_script(
        'gct-blog-posts-module-script-v2',
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
    global $GCT_BlogPostsModule;
    
    // Check if module is initialized
    if (!isset($GCT_BlogPostsModule) || !is_object($GCT_BlogPostsModule)) {
        // If not, initialize it
        require_once GCT_BPM_PLUGIN_DIR . 'includes/GCTBlogPostsModule.php';
        $GCT_BlogPostsModule = new GCT_BlogPostsModule();
    }
    
    // Call the AJAX handler method
    $GCT_BlogPostsModule->ajax_get_filtered_posts();
    
    // This is needed to terminate execution properly
    wp_die();
} 