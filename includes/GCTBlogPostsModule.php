<?php
/**
 * GCT Blog Posts Module for Divi
 */
class GCT_Blog_Posts_Module extends ET_Builder_Module {
    public $slug = 'gct_blog_posts';
    public $vb_support = 'on';
    public $child_slug = '';
    public $name = 'Blog Posts Grid';
    
    protected $module_credits = array(
        'module_uri' => '',
        'author'     => '',
        'author_uri' => '',
    );
    
    /**
     * Module initialization
     */
    public function init() {
        $this->name = esc_html__('Blog Posts Grid', 'gct-blog-posts-module');
        $this->icon = 'j';
        $this->main_css_element = '%%order_class%%.gct_blog_posts';
        
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
            'overlay_icon' => array(
                'label'           => esc_html__('Overlay Icon', 'gct-blog-posts-module'),
                'type'            => 'select_icon',
                'option_category' => 'configuration',
                'default'         => 'e',
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
        if ($total_pages <= 1) {
            return '';
        }
        
        $output = '<div class="gct-pagination">';
        
        if ($pagination_type === 'numbers') {
            for ($i = 1; $i <= $total_pages; $i++) {
                $active_class = $i === $current_page ? ' active' : '';
                $output .= sprintf(
                    '<a href="#" class="gct-page-number%2$s" data-page="%1$s">%1$s</a>',
                    esc_attr($i),
                    esc_attr($active_class)
                );
            }
        } elseif ($pagination_type === 'prev_next') {
            if ($current_page > 1) {
                $output .= '<a href="#" class="gct-prev-page" data-page="' . esc_attr($current_page - 1) . '">&laquo; ' . esc_html__('Previous', 'gct-blog-posts-module') . '</a>';
            }
            
            if ($current_page < $total_pages) {
                $output .= '<a href="#" class="gct-next-page" data-page="' . esc_attr($current_page + 1) . '">' . esc_html__('Next', 'gct-blog-posts-module') . ' &raquo;</a>';
            }
        } elseif ($pagination_type === 'load_more' && $current_page < $total_pages) {
            $output .= '<a href="#" class="gct-load-more" data-page="' . esc_attr($current_page + 1) . '">' . esc_html($load_more_text) . '</a>';
        }
        
        $output .= '</div>';
        
        return $output;
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
        
        // Determine current page and category
        $current_page = isset($_GET['gct_page']) ? max(1, intval($_GET['gct_page'])) : 1;
        $selected_category = isset($_GET['gct_category']) ? sanitize_text_field($_GET['gct_category']) : 'all';
        
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
        $output = '<div class="gct-blog-posts-container" data-post-type="' . esc_attr($post_type) . '" data-posts-per-page="' . esc_attr($posts_number) . '">';
        
        // Module title
        if (!empty($module_title)) {
            $output .= sprintf('<h2 class="gct-module-title">%1$s</h2>', esc_html($module_title));
        }
        
        // Category filter
        if ($show_category_filter) {
            $output .= $this->generate_category_dropdown($post_type, $selected_category, $category_filter_label);
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
                            $taxonomy = key($taxonomies);
                            $terms = get_the_terms(get_the_ID(), $taxonomy);
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
                            <span class="et-pb-icon">%3$s</span>
                        </div>
                    </a>',
                    esc_url(get_permalink()),
                    $thumbnail_style,
                    esc_attr($this->props['overlay_icon'])
                );
                
                // Post content
                $output .= '<div class="gct-post-content">';
                
                // Post meta
                $output .= '<div class="gct-post-meta">';
                $output .= $categories;
                $output .= $date;
                $output .= '</div>';
                
                // Post title
                $output .= sprintf(
                    '<h3 class="gct-post-title"><a href="%1$s">%2$s</a></h3>',
                    esc_url(get_permalink()),
                    esc_html(get_the_title())
                );
                
                // Post excerpt
                $output .= $excerpt;
                
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
            $output .= $this->generate_pagination($total_pages, $current_page, $pagination_type, $load_more_text);
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
        
        // Post thumbnail
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-thumbnail',
            'declaration' => '
                display: block;
                position: relative;
                height: 240px;
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-color: #f5f5f5;
            ',
        ));
        
        // Post overlay - normal state
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-overlay',
            'declaration' => sprintf('
                position: absolute;
                top: 0;
                left: 0;
                width: 100%%;
                height: 100%%;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: opacity 0.3s ease;
                background-color: %1$s;
            ', $this->props['overlay_color']),
        ));
        
        // Post overlay - hover state
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-thumbnail:hover .gct-post-overlay',
            'declaration' => 'opacity: 1;',
        ));
        
        // Overlay icon
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-overlay .et-pb-icon',
            'declaration' => '
                color: #ffffff;
                font-size: 32px;
            ',
        ));
        
        // Post content
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-content',
            'declaration' => 'padding: 20px 0;',
        ));
        
        // Post meta
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-meta',
            'declaration' => 'margin-bottom: 10px; display: flex; flex-wrap: wrap; gap: 10px;',
        ));
        
        // Post category
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-category',
            'declaration' => '
                display: inline-block;
                padding: 3px 8px;
                background-color: #f0f0f0;
                color: #666;
                font-size: 12px;
                border-radius: 3px;
                margin-right: 5px;
            ',
        ));
        
        // Post date
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-date',
            'declaration' => 'font-size: 14px; color: #999;',
        ));
        
        // Post title
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-title',
            'declaration' => 'margin-top: 0; margin-bottom: 15px; font-size: 20px; line-height: 1.3;',
        ));
        
        // Post title link
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-title a',
            'declaration' => 'color: inherit; text-decoration: none;',
        ));
        
        // Post excerpt
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-post-excerpt',
            'declaration' => 'font-size: 16px; line-height: 1.5; color: #666;',
        ));
        
        // Module title
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-module-title',
            'declaration' => 'margin-bottom: 30px; font-size: 32px;',
        ));
        
        // Category filter
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-category-filter',
            'declaration' => 'margin-bottom: 30px; display: flex; align-items: center; flex-wrap: wrap; gap: 10px;',
        ));
        
        // Category filter label
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-category-filter label',
            'declaration' => 'margin-right: 10px; font-weight: 600;',
        ));
        
        // Category filter select
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-category-filter select',
            'declaration' => 'padding: 8px 30px 8px 12px; background-color: #f7f7f7; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' width=\'24\' height=\'24\'%3E%3Cpath fill=\'none\' d=\'M0 0h24v24H0z\'/%3E%3Cpath d=\'M12 13.172l4.95-4.95 1.414 1.414L12 16 5.636 9.636 7.05 8.222z\'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; background-size: 16px;',
        ));
        
        // Pagination container
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-pagination',
            'declaration' => 'margin-top: 40px; display: flex; justify-content: center; flex-wrap: wrap; gap: 5px;',
        ));
        
        // Pagination item
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-pagination a',
            'declaration' => 'display: inline-block; padding: 8px 16px; margin: 0 5px; color: #666; background-color: #f0f0f0; text-decoration: none; border-radius: 4px; transition: all 0.3s ease;',
        ));
        
        // Pagination active item
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-pagination a.active',
            'declaration' => 'background-color: #2ea3f2; color: #fff;',
        ));
        
        // Pagination item hover
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-pagination a:hover',
            'declaration' => 'background-color: #e0e0e0;',
        ));
        
        // Pagination active item hover
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-pagination a.active:hover',
            'declaration' => 'background-color: #0c71c3;',
        ));
        
        // Load more button
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-load-more',
            'declaration' => 'display: inline-block; padding: 10px 30px; background-color: #f0f0f0; color: #666; text-decoration: none; border-radius: 4px; transition: all 0.3s ease; cursor: pointer;',
        ));
        
        // Load more button hover
        ET_Builder_Element::set_style($render_slug, array(
            'selector'    => '%%order_class%% .gct-load-more:hover',
            'declaration' => 'background-color: #e0e0e0;',
        ));
    }
}

// This line is essential for Divi to recognize and initialize the module
new GCT_Blog_Posts_Module(); 