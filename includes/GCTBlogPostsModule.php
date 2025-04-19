<?php

class GCT_Blog_Posts_Module extends ET_Builder_Module {
    public $slug = 'gct_blog_posts';
    public $vb_support = 'on';
    
    protected $module_credits = array(
        'module_uri' => '',
        'author'     => '',
        'author_uri' => '',
    );
    
    public function init() {
        $this->name = esc_html__('Blog Posts Grid', 'gct-blog-posts-module');
        $this->icon = 'j';
        $this->main_css_element = '%%order_class%%.gct_blog_posts';
        
        $this->settings_modal_toggles = array(
            'general' => array(
                'toggles' => array(
                    'main_content' => esc_html__('Content', 'gct-blog-posts-module'),
                    'elements' => esc_html__('Elements', 'gct-blog-posts-module'),
                ),
            ),
            'advanced' => array(
                'toggles' => array(
                    'layout' => esc_html__('Layout', 'gct-blog-posts-module'),
                    'overlay' => esc_html__('Overlay', 'gct-blog-posts-module'),
                    'text' => esc_html__('Text', 'gct-blog-posts-module'),
                ),
            ),
        );
    }
    
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
                'affects'           => array('category'),
            ),
            'posts_number' => array(
                'label'           => esc_html__('Number of Posts', 'gct-blog-posts-module'),
                'type'            => 'text',
                'option_category' => 'basic_option',
                'description'     => esc_html__('How many posts would you like to display?', 'gct-blog-posts-module'),
                'toggle_slug'     => 'main_content',
                'default'         => '6',
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
    
    public function render($attrs, $content, $render_slug) {
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
        
        // Query arguments
        $args = array(
            'post_type'      => $post_type,
            'posts_per_page' => $posts_number,
            'post_status'    => 'publish',
        );
        
        // Get posts
        $query = new WP_Query($args);
        
        // Start building output
        $output = '';
        
        // Module title
        if (!empty($module_title)) {
            $output .= sprintf('<h2 class="gct-module-title">%1$s</h2>', esc_html($module_title));
        }
        
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
            $output .= '<p>' . esc_html__('No posts found.', 'gct-blog-posts-module') . '</p>';
        }
        
        // Close posts grid
        $output .= '</div>';
        
        return $output;
    }
    
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
    }
}

new GCT_Blog_Posts_Module; 