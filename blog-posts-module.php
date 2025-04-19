<?php
/**
 * Plugin Name: GCT Blog Posts Module
 * Plugin URI: 
 * Description: A custom Divi module for displaying blog posts with filtering options
 * Version: 1.0.0
 * Author: 
 * Author URI: 
 * License: GPL2
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the module to Divi Builder
 */
function gct_blog_posts_module_init() {
    if (class_exists('ET_Builder_Module')) {
        include_once('includes/GCTBlogPostsModule.php');
    }
}
add_action('et_builder_ready', 'gct_blog_posts_module_init');

/**
 * Enqueue scripts and styles
 */
function gct_blog_posts_module_enqueue_scripts() {
    wp_enqueue_style('gct-blog-posts-module-style', plugin_dir_url(__FILE__) . 'css/gct-blog-posts-module.css', array(), '1.0.0');
    wp_enqueue_script('gct-blog-posts-module-script', plugin_dir_url(__FILE__) . 'js/gct-blog-posts-module.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'gct_blog_posts_module_enqueue_scripts');

/**
 * Create necessary directories and files if they don't exist
 */
function gct_blog_posts_module_activate() {
    // Create includes directory if it doesn't exist
    if (!file_exists(plugin_dir_path(__FILE__) . 'includes')) {
        mkdir(plugin_dir_path(__FILE__) . 'includes', 0755);
    }
    
    // Create css directory if it doesn't exist
    if (!file_exists(plugin_dir_path(__FILE__) . 'css')) {
        mkdir(plugin_dir_path(__FILE__) . 'css', 0755);
    }
    
    // Create js directory if it doesn't exist
    if (!file_exists(plugin_dir_path(__FILE__) . 'js')) {
        mkdir(plugin_dir_path(__FILE__) . 'js', 0755);
    }
}
register_activation_hook(__FILE__, 'gct_blog_posts_module_activate'); 