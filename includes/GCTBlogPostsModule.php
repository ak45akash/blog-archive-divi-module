<?php
/**
 * GCT Blog Posts Module for Divi
 */
class GCT_BlogPostsModule extends ET_Builder_Module {
    public $slug = 'gct_blog_posts';
    public $vb_support = 'on';
    
    /**
     * Module initialization
     */
    public function init() {
        $this->name = esc_html__('Blog Posts Grid', 'gct-blog-posts-module');
        $this->icon = 'j';
        $this->main_css_element = '%%order_class%%';
        
        $this->settings_modal_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_content' => esc_html__('Content', 'gct-blog-posts-module'),
                    'elements' => esc_html__('Elements', 'gct-blog-posts-module'),
                    'filter' => esc_html__('Filter & Pagination', 'gct-blog-posts-module'),
                ),
            ),
            'advanced' => array(
                'toggles' => array(
                    'layout' => esc_html__('Layout', 'gct-blog-posts-module'),
                    'overlay' => esc_html__('Overlay', 'gct-blog-posts-module'),
                    'filter_styles' => esc_html__('Filter Styles', 'gct-blog-posts-module'),
                    'pagination_styles' => esc_html__('Pagination Styles', 'gct-blog-posts-module'),
                    'text' => esc_html__('Text', 'gct-blog-posts-module'),
                ),
            ),
        );
    }
    
    /**
     * Get module fields
     */
    public function get_fields() {
        $post_types = $this->get_post_types();
        
        return array(
            'module_title' => array(
                'label'           => esc_html__('Module Title', 'gct-blog-posts-module'),
                'type'            => 'text',
                'option_category' => 'basic_option',
                'description'     => esc_html__('Enter a title for your module.', 'gct-blog-posts-module'),
                'toggle_slug'     => 'main_content',
            ),
            'post_type' => array(
                'label'             => esc_html__('Post Type', 'gct-blog-posts-module'),
                'type'              => 'select',
                'option_category'   => 'basic_option',
                'options'           => $post_types,
                'default'           => 'post',
                'description'       => esc_html__('Select which post type to display.', 'gct-blog-posts-module'),
                'toggle_slug'       => 'main_content',
                'affects'           => array('category_filter'),
            ),
            'category_filter' => array(
                'label'             => esc_html__('Default Category', 'gct-blog-posts-module'),
                'type'              => 'select',
                'option_category'   => 'basic_option',
                'options'           => $this->get_categories_for_post_type('post'),
                'default'           => 'all',
                'description'       => esc_html__('Select the default category to display.', 'gct-blog-posts-module'),
                'toggle_slug'       => 'main_content',
                'show_if'           => array('post_type' => 'post'),
            ),
            'posts_number' => array(
                'label'           => esc_html__('Posts Per Page', 'gct-blog-posts-module'),
                'type'            => 'text',
                'option_category' => 'basic_option',
                'description'     => esc_html__('How many posts would you like to display per page?', 'gct-blog-posts-module'),
                'toggle_slug'     => 'main_content',
                'default'         => '6',
            ),
            'show_category_filter' => array(
                'label'           => esc_html__('Show Category Filter', 'gct-blog-posts-module'),
                'type'            => 'yes_no_button',
                'option_category' => 'configuration',
                'options'         => array(
                    'on'  => esc_html__('Yes', 'gct-blog-posts-module'),
                    'off' => esc_html__('No', 'gct-blog-posts-module'),
                ),
                'default'         => 'on',
                'toggle_slug'     => 'filter',
                'affects'         => array('category_filter_label'),
            ),
            'category_filter_label' => array(
                'label'           => esc_html__('Category Filter Label', 'gct-blog-posts-module'),
                'type'            => 'text',
                'option_category' => 'basic_option',
                'description'     => esc_html__('Enter a label for the category filter dropdown.', 'gct-blog-posts-module'),
                'toggle_slug'     => 'filter',
                'default'         => 'Category',
                'show_if'         => array('show_category_filter' => 'on'),
            ),
            'show_pagination' => array(
                'label'           => esc_html__('Show Pagination', 'gct-blog-posts-module'),
                'type'            => 'yes_no_button',
                'option_category' => 'configuration',
                'options'         => array(
                    'on'  => esc_html__('Yes', 'gct-blog-posts-module'),
                    'off' => esc_html__('No', 'gct-blog-posts-module'),
                ),
                'default'         => 'on',
                'toggle_slug'     => 'filter',
            ),
            'pagination_type' => array(
                'label'           => esc_html__('Pagination Type', 'gct-blog-posts-module'),
                'type'            => 'select',
                'option_category' => 'configuration',
                'options'         => array(
                    'numbers'     => esc_html__('Numbers', 'gct-blog-posts-module'),
                    'prev_next'   => esc_html__('Prev/Next', 'gct-blog-posts-module'),
                    'load_more'   => esc_html__('Load More Button', 'gct-blog-posts-module'),
                ),
                'default'         => 'numbers',
                'toggle_slug'     => 'filter',
                'show_if'         => array('show_pagination' => 'on'),
            ),
            'load_more_text' => array(
                'label'           => esc_html__('Load More Button Text', 'gct-blog-posts-module'),
                'type'            => 'text',
                'option_category' => 'basic_option',
                'description'     => esc_html__('Text for the load more button.', 'gct-blog-posts-module'),
                'toggle_slug'     => 'filter',
                'default'         => 'See more',
                'show_if'         => array(
                    'show_pagination' => 'on',
                    'pagination_type' => 'load_more',
                ),
            ),
            'show_category' => array(
                'label'           => esc_html__('Show Category', 'gct-blog-posts-module'),
                'type'            => 'yes_no_button',
                'option_category' => 'configuration',
                'options'         => array(
                    'on'  => esc_html__('Yes', 'gct-blog-posts-module'),
                    'off' => esc_html__('No', 'gct-blog-posts-module'),
                ),
                'default'         => 'on',
                'toggle_slug'     => 'elements',
            ),
            'show_date' => array(
                'label'           => esc_html__('Show Date', 'gct-blog-posts-module'),
                'type'            => 'yes_no_button',
                'option_category' => 'configuration',
                'options'         => array(
                    'on'  => esc_html__('Yes', 'gct-blog-posts-module'),
                    'off' => esc_html__('No', 'gct-blog-posts-module'),
                ),
                'default'         => 'on',
                'toggle_slug'     => 'elements',
            ),
            'show_excerpt' => array(
                'label'           => esc_html__('Show Excerpt', 'gct-blog-posts-module'),
                'type'            => 'yes_no_button',
                'option_category' => 'configuration',
                'options'         => array(
                    'on'  => esc_html__('Yes', 'gct-blog-posts-module'),
                    'off' => esc_html__('No', 'gct-blog-posts-module'),
                ),
                'default'         => 'on',
                'toggle_slug'     => 'elements',
            ),
            'excerpt_length' => array(
                'label'           => esc_html__('Excerpt Length', 'gct-blog-posts-module'),
                'type'            => 'range',
                'option_category' => 'configuration',
                'default'         => '150',
                'range_settings'  => array(
                    'min'  => '10',
                    'max'  => '300',
                    'step' => '10',
                ),
                'show_if'         => array(
                    'show_excerpt' => 'on',
                ),
                'toggle_slug'     => 'elements',
            ),
            'posts_per_row' => array(
                'label'           => esc_html__('Posts Per Row', 'gct-blog-posts-module'),
                'type'            => 'range',
                'option_category' => 'layout',
                'default'         => '3',
                'range_settings'  => array(
                    'min'  => '1',
                    'max'  => '6',
                    'step' => '1',
                ),
                'mobile_options'  => true,
                'toggle_slug'     => 'layout',
            ),
            'overlay_color' => array(
                'label'           => esc_html__('Overlay Color', 'gct-blog-posts-module'),
                'type'            => 'color-alpha',
                'custom_color'    => true,
                'default'         => 'rgba(0,0,0,0.6)',
                'toggle_slug'     => 'overlay',
            ),
        );
    }
    
    /**
     * Get post types
     */
    public function get_post_types() {
        $post_types = get_post_types(array('public' => true), 'objects');
        $options = array();
        
        foreach ($post_types as $post_type) {
            if ($post_type->name !== 'attachment') {
                $options[$post_type->name] = $post_type->label;
            }
        }
        
        return $options;
    }
    
    /**
     * Get categories for post type
     */
    public function get_categories_for_post_type($post_type) {
        if ($post_type === 'post') {
            $taxonomy = 'category';
        } else {
            // Get taxonomies for this post type
            $taxonomies = get_object_taxonomies($post_type, 'objects');
            $taxonomy = !empty($taxonomies) ? key($taxonomies) : '';
        }
        
        if (!$taxonomy) {
            return array();
        }
        
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => true,
        ));
        
        $options = array(
            'all' => esc_html__('All Categories', 'gct-blog-posts-module'),
        );
        
        if (!is_wp_error($terms) && !empty($terms)) {
            foreach ($terms as $term) {
                $options[$term->term_id] = $term->name;
            }
        }
        
        return $options;
    }
    
    /**
     * Get taxonomy for post type
     */
    private function get_taxonomy_for_post_type($post_type) {
        if ($post_type === 'post') {
            return 'category';
        } else {
            $taxonomies = get_object_taxonomies($post_type, 'objects');
            return !empty($taxonomies) ? key($taxonomies) : '';
        }
    }
    
    /**
     * Generate category dropdown
     */
    private function generate_category_dropdown($post_type, $selected_category = 'all', $label = 'Category') {
        $taxonomy = $this->get_taxonomy_for_post_type($post_type);
        
        if (!$taxonomy) {
            return '';
        }
        
        $categories = $this->get_categories_for_post_type($post_type);
        
        $output = '<div class="gct-category-filter">';
        $output .= '<label>' . esc_html($label) . '</label>';
        $output .= '<select class="gct-category-select" data-taxonomy="' . esc_attr($taxonomy) . '">';
        
        foreach ($categories as $value => $name) {
            $output .= sprintf(
                '<option value="%1$s" %2$s>%3$s</option>',
                esc_attr($value),
                selected($selected_category, $value, false),
                esc_html($name)
            );
        }
        
        $output .= '</select>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Generate pagination
     */
    private function generate_pagination($total_pages, $current_page = 1, $pagination_type = 'numbers', $load_more_text = 'See more') {
        // If there's only one page or no pages, don't show pagination
        if ($total_pages <= 1) {
            return '';
        }
        
        // Only if there are more pages to load
        if ($current_page < $total_pages) {
            $output = '<div class="gct-pagination">';
            $output .= '<a href="#" class="gct-load-more" data-page="' . esc_attr($current_page + 1) . '">' . esc_html($load_more_text) . '</a>';
            $output .= '</div>';
            return $output;
        }
        
        // If we're on the last page, don't show pagination
        return '';
    }
    
    /**
     * Module render method
     */
    public function render($attrs, $content = null, $render_slug) {
        // Add custom CSS
        $this->add_custom_styles($render_slug);
        
        // Get module settings
        $module_title = $this->props['module_title'];
        $post_type = $this->props['post_type'];
        $posts_number = intval($this->props['posts_number']);
        $show_category = $this->props['show_category'] === 'on';
        $show_date = $this->props['show_date'] === 'on';
        $show_excerpt = $this->props['show_excerpt'] === 'on';
        $excerpt_length = intval($this->props['excerpt_length']);
        $posts_per_row = intval($this->props['posts_per_row']);
        $show_category_filter = $this->props['show_category_filter'] === 'on';
        $category_filter_label = $this->props['category_filter_label'];
        $show_pagination = $this->props['show_pagination'] === 'on';
        $pagination_type = $this->props['pagination_type'];
        $load_more_text = $this->props['load_more_text'];
        $category_filter = isset($this->props['category_filter']) ? $this->props['category_filter'] : 'all';
        
        // Store module settings as data attributes for AJAX consistency
        $module_settings = htmlspecialchars(json_encode(array(
            'show_category' => $show_category,
            'show_date' => $show_date,
            'show_excerpt' => $show_excerpt,
            'excerpt_length' => $excerpt_length,
            'posts_per_row' => $posts_per_row,
            'show_pagination' => $show_pagination,
            'pagination_type' => $pagination_type,
            'load_more_text' => $load_more_text,
        )), ENT_QUOTES, 'UTF-8');
        
        // Determine current page and category
        $current_page = isset($_GET['gct_page']) ? max(1, intval($_GET['gct_page'])) : 1;
        $selected_category = isset($_GET['gct_category']) ? sanitize_text_field($_GET['gct_category']) : $category_filter;
        
        // Check if we're handling an AJAX request
        $is_ajax = defined('DOING_AJAX') && DOING_AJAX && isset($_POST['action']) && $_POST['action'] === 'gct_get_filtered_posts';
        
        // If this is an AJAX request, get settings from POST data
        if ($is_ajax && isset($_POST['module_settings'])) {
            $ajax_settings = json_decode(stripslashes($_POST['module_settings']), true);
            if (is_array($ajax_settings)) {
                $show_category = isset($ajax_settings['show_category']) ? $ajax_settings['show_category'] : $show_category;
                $show_date = isset($ajax_settings['show_date']) ? $ajax_settings['show_date'] : $show_date;
                $show_excerpt = isset($ajax_settings['show_excerpt']) ? $ajax_settings['show_excerpt'] : $show_excerpt;
                $excerpt_length = isset($ajax_settings['excerpt_length']) ? $ajax_settings['excerpt_length'] : $excerpt_length;
                $posts_per_row = isset($ajax_settings['posts_per_row']) ? $ajax_settings['posts_per_row'] : $posts_per_row;
                $show_pagination = isset($ajax_settings['show_pagination']) ? $ajax_settings['show_pagination'] : $show_pagination;
                $pagination_type = isset($ajax_settings['pagination_type']) ? $ajax_settings['pagination_type'] : $pagination_type;
                $load_more_text = isset($ajax_settings['load_more_text']) ? $ajax_settings['load_more_text'] : $load_more_text;
            }
        }
        
        // Query arguments
        $args = array(
            'post_type'      => $post_type,
            'posts_per_page' => $posts_number,
            'post_status'    => 'publish',
            'paged'          => $current_page,
        );
        
        // Add taxonomy query if category is selected
        if ($selected_category !== 'all') {
            $taxonomy = $this->get_taxonomy_for_post_type($post_type);
            if ($taxonomy) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => $taxonomy,
                        'field'    => 'term_id',
                        'terms'    => $selected_category,
                    ),
                );
            }
        }
        
        // Get posts
        $query = new WP_Query($args);
        
        // Calculate total pages for pagination
        $total_pages = $query->max_num_pages;
        
        // Start building output
        $output = '<div class="gct-blog-posts-container" data-post-type="' . esc_attr($post_type) . '" data-posts-per-page="' . esc_attr($posts_number) . '" data-module-settings="' . $module_settings . '">';
        
        // Module title (left-aligned)
        if (!empty($module_title)) {
            $output .= sprintf('<h2 class="gct-blog-post-module-title" style="text-align: left;">%1$s</h2>', esc_html($module_title));
        }
        
        // Category filter
        if ($show_category_filter) {
            $output .= sprintf(
                '<div class="gct-blog-posts-filter">
                    <select class="gct-category-filter" data-post-type="%1$s">
                        %2$s
                    </select>
                </div>',
                esc_attr($post_type),
                $this->generate_category_dropdown($post_type, $selected_category, $category_filter_label)
            );
        }
        
        // Posts container for AJAX updates
        $output .= '<div class="gct-posts-wrapper">';
        
        // Start posts grid
        $output .= sprintf(
            '<div class="gct-blog-posts-grid posts-per-row-%1$s">',
            esc_attr($posts_per_row)
        );
        
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
                if ($show_category) {
                    if ($post_type === 'post') {
                        $post_categories = get_the_category();
                        if (!empty($post_categories)) {
                            // Get only the first category
                            $category = $post_categories[0];
                            $categories = sprintf(
                                '<span class="gct-post-category">%1$s</span>',
                                esc_html($category->name)
                            );
                        }
                    } else {
                        $taxonomies = get_object_taxonomies($post_type, 'objects');
                        if (!empty($taxonomies)) {
                            $taxonomy = key($taxonomies);
                            $terms = get_the_terms(get_the_ID(), $taxonomy);
                            if (!empty($terms) && !is_wp_error($terms)) {
                                // Get only the first term
                                $term = $terms[0];
                                $categories = sprintf(
                                    '<span class="gct-post-category">%1$s</span>',
                                    esc_html($term->name)
                                );
                            }
                        }
                    }
                }
                
                // Get date
                $date = '';
                if ($show_date) {
                    $date = sprintf(
                        '<div class="gct-post-date">%1$s</div>',
                        esc_html(get_the_date())
                    );
                }
                
                // Get excerpt
                $excerpt = '';
                if ($show_excerpt) {
                    $raw_excerpt = get_the_excerpt();
                    $trimmed_excerpt = wp_trim_words($raw_excerpt, $excerpt_length / 5, '...');
                    $excerpt = sprintf(
                        '<div class="gct-post-excerpt">%1$s</div>',
                        esc_html($trimmed_excerpt)
                    );
                }
                
                // Build post item
                $output .= '<article class="gct-post-item">';
                
                // Post thumbnail with overlay
                $output .= sprintf(
                    '<a href="%1$s" class="gct-post-thumbnail" %2$s>
                        <div class="gct-post-overlay">
                        </div>
                    </a>',
                    esc_url(get_permalink()),
                    $thumbnail_style
                );
                
                // Post content
                $output .= '<div class="gct-post-content">';
                
                // Category below image 
                if ($show_category === 'on' || $show_category === true) {
                    $output .= '<span class="gct-post-category">BURIAL &amp; CREMATION</span>';
                }
                
                // Post date
                $output .= '<div class="gct-post-meta">';
                $output .= $date;
                $output .= '</div>';
                
                // Post title
                $output .= sprintf(
                    '<h3 class="gct-post-title"><a href="%1$s">%2$s</a></h3>',
                    esc_url(get_permalink()),
                    esc_html(get_the_title())
                );
                
                // Post excerpt - only show if explicitly set to 'on'
                if ($show_excerpt === 'on' || $show_excerpt === true) {
                    $output .= $excerpt;
                }
                
                // Close post content
                $output .= '</div>';
                
                // Close post item
                $output .= '</article>';
            }
            
            // Reset post data
            wp_reset_postdata();
        } else {
            $output .= '<p class="gct-no-posts">' . esc_html__('No posts found.', 'gct-blog-posts-module') . '</p>';
        }
        
        // Close posts grid
        $output .= '</div>';
        
        // Add pagination if enabled
        if ($show_pagination) {
            // Always use 'load_more' pagination type rather than using the setting
            $output .= $this->generate_pagination($total_pages, $current_page, 'load_more', $load_more_text);
        }
        
        // Close posts wrapper
        $output .= '</div>';
        
        // Close container
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Add custom styles
     */
    public function add_custom_styles($render_slug) {
        // Base font styles
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%%, %%order_class%% .gct-module-title, %%order_class%% .gct-post-category, %%order_class%% .gct-post-date, %%order_class%% .gct-post-title, %%order_class%% .gct-post-excerpt, %%order_class%% .gct-category-filter label, %%order_class%% .gct-category-filter select, %%order_class%% .gct-load-more, %%order_class%% .gct-no-posts, %%order_class%% h1, %%order_class%% h2, %%order_class%% h3, %%order_class%% h4, %%order_class%% h5, %%order_class%% h6, %%order_class%% p, %%order_class%% button, %%order_class%% a',
            'declaration' => 'font-family: \'Libre Franklin\', sans-serif;',
        ));
        
        // Grid layout
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-blog-posts-grid',
            'declaration' => 'display: grid; grid-gap: 30px;',
        ));
        
        // Posts per row - Desktop
        $posts_per_row = intval($this->props['posts_per_row']);
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-blog-posts-grid',
            'declaration' => sprintf('grid-template-columns: repeat(%1$s, 1fr);', $posts_per_row),
        ));
        
        // Posts per row - Tablet
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-blog-posts-grid',
            'declaration' => 'grid-template-columns: repeat(2, 1fr);',
            'media_query' => ET_Builder_Element::get_media_query('max_width_980'),
        ));
        
        // Posts per row - Mobile
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-blog-posts-grid',
            'declaration' => 'grid-template-columns: repeat(1, 1fr);',
            'media_query' => ET_Builder_Element::get_media_query('max_width_767'),
        ));
        
        // Post item styling
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-item',
            'declaration' => 'position: relative; transition: all 0.3s ease; border-radius: 20px; overflow: hidden; box-shadow: 0 1px 5px rgba(0,0,0,0.08); background-color: #fff; display: flex; flex-direction: column;',
        ));
        
        // Post item hover - no transform
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-item:hover',
            'declaration' => 'box-shadow: 0 3px 10px rgba(0,0,0,0.1);',
        ));
        
        // Post thumbnail
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-thumbnail',
            'declaration' => 'display: block; position: relative; height: 220px; background-size: cover; background-position: center; background-repeat: no-repeat; background-color: #f5f5f5; width: 100%; transition: transform 0.3s ease; border-radius: 20px 20px 0 0;',
        ));
        
        // Post thumbnail hover effect
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-item:hover .gct-post-thumbnail',
            'declaration' => 'transform: scale(1.05);',
        ));
        
        // Post overlay effect
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-overlay',
            'declaration' => 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; transition: background-color 0.3s ease;',
        ));
        
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-item:hover .gct-post-overlay',
            'declaration' => 'background-color: rgba(0,0,0,0.2);',
        ));
        
        // Category below image
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-content .gct-post-category',
            'declaration' => 'display: block; padding: 0; background-color: transparent; color: #F7941C; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; order: -2;',
        ));
        
        // Date meta section
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-meta',
            'declaration' => 'margin-bottom: 10px; display: flex; flex-wrap: wrap; gap: 10px; order: -1;',
        ));
        
        // Post content
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-content',
            'declaration' => 'padding: 20px; flex-grow: 1; display: flex; flex-direction: column; border-top: none; overflow: hidden;',
        ));
        
        // Post title hover
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-title a:hover',
            'declaration' => 'color: #F7941C !important;',
        ));
    }
    
    /**
     * AJAX handler for filtered posts
     */
    public function ajax_get_filtered_posts() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'gct_blog_posts_nonce')) {
            wp_send_json_error(array('message' => 'Invalid security token.'));
        }
        
        // Get parameters
        $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'post';
        $category_id = isset($_POST['category_id']) ? sanitize_text_field($_POST['category_id']) : 'all';
        $posts_per_page = isset($_POST['posts_per_page']) ? intval($_POST['posts_per_page']) : 6;
        $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
        $load_more = isset($_POST['load_more']) && $_POST['load_more'] === 'true';
        
        // Module settings
        $module_settings = isset($_POST['module_settings']) ? $_POST['module_settings'] : '';
        if (is_string($module_settings)) {
            $module_settings = json_decode(stripslashes($module_settings), true);
        }
        
        // Query arguments
        $args = array(
            'post_type'      => $post_type,
            'posts_per_page' => $posts_per_page,
            'post_status'    => 'publish',
            'paged'          => $page,
        );
        
        // Add taxonomy query if category is selected
        if ($category_id !== 'all') {
            $taxonomy = $this->get_taxonomy_for_post_type($post_type);
            if ($taxonomy) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => $taxonomy,
                        'field'    => 'term_id',
                        'terms'    => $category_id,
                    ),
                );
            }
        }
        
        // Get posts
        $query = new WP_Query($args);
        
        // Calculate total pages for pagination
        $total_pages = $query->max_num_pages;
        $has_more = $page < $total_pages;
        
        // Start output buffer
        ob_start();
        
        // If this is a load more request, just return the posts HTML
        if ($load_more) {
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $this->render_post_item();
                }
                wp_reset_postdata();
            }
            $posts_html = ob_get_clean();
            
            wp_send_json_success(array(
                'posts_html' => $posts_html,
                'has_more'   => $has_more,
            ));
        } else {
            // For regular filter, return the entire grid
            if ($query->have_posts()) {
                echo '<div class="gct-blog-posts-grid posts-per-row-' . esc_attr($module_settings['posts_per_row']) . '">';
                while ($query->have_posts()) {
                    $query->the_post();
                    $this->render_post_item();
                }
                echo '</div>';
                wp_reset_postdata();
            } else {
                echo '<p class="gct-no-posts">' . esc_html__('No posts found.', 'gct-blog-posts-module') . '</p>';
            }
            
            // Generate pagination
            $pagination_html = '';
            if ($module_settings['show_pagination']) {
                $pagination_html = $this->generate_pagination($total_pages, $page, 'load_more', $module_settings['load_more_text']);
            }
            
            $html = ob_get_clean();
            
            wp_send_json_success(array(
                'html'       => $html,
                'pagination' => $pagination_html,
                'has_more'   => $has_more,
            ));
        }
    }
    
    /**
     * Helper function to render a post item
     */
    private function render_post_item() {
        // Get featured image
        $has_thumbnail = has_post_thumbnail();
        $thumbnail = $has_thumbnail ? get_the_post_thumbnail_url(get_the_ID(), 'large') : '';
        $thumbnail_style = $has_thumbnail ? sprintf('style="background-image:url(%1$s);"', esc_url($thumbnail)) : '';
        
        // Start post item
        echo '<article class="gct-post-item">';
        
        // Post thumbnail with overlay
        echo sprintf(
            '<a href="%1$s" class="gct-post-thumbnail" %2$s>
                <div class="gct-post-overlay">
                </div>
            </a>',
            esc_url(get_permalink()),
            $thumbnail_style
        );
        
        // Post content
        echo '<div class="gct-post-content">';
        
        // Get post type and determine if we should show category
        $post_type = get_post_type();
        $show_category = true; // Default to true
        
        // Get category
        if ($show_category) {
            if ($post_type === 'post') {
                $post_categories = get_the_category();
                if (!empty($post_categories)) {
                    echo sprintf(
                        '<span class="gct-post-category">%1$s</span>',
                        esc_html($post_categories[0]->name)
                    );
                }
            } else {
                $taxonomies = get_object_taxonomies($post_type, 'objects');
                if (!empty($taxonomies)) {
                    $taxonomy_key = key($taxonomies);
                    $terms = get_the_terms(get_the_ID(), $taxonomy_key);
                    if (!empty($terms) && !is_wp_error($terms)) {
                        echo sprintf(
                            '<span class="gct-post-category">%1$s</span>',
                            esc_html($terms[0]->name)
                        );
                    }
                }
            }
        }
        
        // Post meta with date
        echo '<div class="gct-post-meta">';
        echo sprintf('<div class="gct-post-date">%1$s</div>', esc_html(get_the_date()));
        echo '</div>';
        
        // Post title
        echo sprintf(
            '<h3 class="gct-post-title"><a href="%1$s">%2$s</a></h3>',
            esc_url(get_permalink()),
            esc_html(get_the_title())
        );
        
        // Post excerpt
        $show_excerpt = true; // Default to true
        if ($show_excerpt) {
            $excerpt_length = 150;
            $raw_excerpt = get_the_excerpt();
            $trimmed_excerpt = wp_trim_words($raw_excerpt, $excerpt_length / 5, '...');
            echo sprintf(
                '<div class="gct-post-excerpt">%1$s</div>',
                esc_html($trimmed_excerpt)
            );
        }
        
        // Close post content
        echo '</div>';
        
        // Close post item
        echo '</article>';
    }
}

// This line is essential for Divi to recognize and initialize the module
new GCT_BlogPostsModule; 