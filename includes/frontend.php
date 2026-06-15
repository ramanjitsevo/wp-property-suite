<?php
if (!defined('ABSPATH')) {
    exit;
}

function wps_handle_layout() {
    global $post;
    
    if (wps_post_has_search_shortcode($post)) {
        // Remove default WordPress sidebars
        remove_action('sidebar', 'dynamic_sidebar');
        
        // Add custom CSS to hide sidebars and make content full width
        add_action('wp_head', 'wps_custom_styles', 100);
        
        // Add body class for custom styling
        add_filter('body_class', 'wps_add_body_class');
        
        // Force full width template if theme supports it
        add_filter('template_include', 'wps_force_full_width');
    }
}

/**
 * Check whether the current post contains a supported property search shortcode.
 */
function wps_post_has_search_shortcode($post) {
    if (!is_a($post, 'WP_Post')) {
        return false;
    }

    return has_shortcode($post->post_content, 'wps_search') || has_shortcode($post->post_content, 'wps');
}

/**
 * Force full width template
 */
function wps_force_full_width($template) {
    // Try to find a full-width template in the theme
    $full_width_template = locate_template(array('template-fullwidth.php', 'full-width.php', 'page-fullwidth.php'));
    
    if ($full_width_template) {
        return $full_width_template;
    }
    
    return $template;
}

/**
 * Add custom styles to hide sidebars and make full width
 */
function wps_custom_styles() {
    ?>
    <style>
        /* Hide WordPress theme sidebars only (NOT the property plugin sidebar) */
        .site-sidebar,
        .theme-sidebar,
        #secondary,
        .widget-area,
        aside.widget-area,
        .sidebar-primary,
        .sidebar-secondary,
        #sidebar-primary,
        #sidebar-secondary,
        .wp-sidebar,
        .theme-widget-area,
        div#secondary,
        section.widget-area:not(.properties-sidebar),
        aside:not(.properties-sidebar):not([class*="wps"]) {display: none !important; width: 0 !important; visibility: hidden !important; }
        
        /* Make WordPress content area full width */
        .content-area,
        #primary,
        .site-content,
        .site-main,
        .main-content,
        .content-wrapper,
        div#primary,
        .site-content > .container,
        .site-content > .container-fluid,
        main.site-main {width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; float: none !important; }
        
        /* Remove WordPress container constraints */
        .site-content .container,
        #main .container,
        .container.content-area,
        .wrapper.site-content { max-width: 100% !important; width: 100% !important; padding: 0 !important; }
        
        /* Hide WordPress page title and comments */
        .entry-title,
        .page-title,
        h1.entry-title,
        .post-title,
        .page-header,
        .entry-header,
        #comments,
        .comments-area { display: none !important; }
        
        /* Ensure property plugin container takes full width */
        .wps-container { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
        
        /* IMPORTANT: Keep property plugin sidebar visible */
        .wps-app .properties-sidebar,
        .wps-app aside.properties-sidebar,
        .wps-container .properties-sidebar,
        aside.properties-sidebar {display: block !important; width: 300px !important; visibility: visible !important; opacity: 1 !important; }

        @media(max-width: 768px){
            .wps-app .properties-sidebar,
            .wps-app aside.properties-sidebar,
            .wps-container .properties-sidebar,
            aside.properties-sidebar { display: block !important; width: 100% !important; visibility: visible !important; opacity: 1 !important;  }

        }
    </style>
    <?php
}

/**
 * Add custom body class
 */
function wps_add_body_class($classes) {
    $classes[] = 'wps-full-width';
    $classes[] = 'wps-no-sidebar';
    $classes[] = 'wps-page';
    return $classes;
}

function wps_shortcode($atts) {
    // Unique ID for the container
    $container_id = 'wps-root-' . uniqid();

    wps_enqueue_assets(true);
    
    // Add viewport meta tag for responsive design (only if not already present)
    if (!has_action('wp_head', 'wps_viewport_meta')) {
        add_action('wp_head', 'wps_viewport_meta');
    }
    
    ob_start();
    ?>
    <div id="<?php echo esc_attr($container_id); ?>" class="wps-container" data-container-id="<?php echo esc_attr($container_id); ?>"></div>
    <script>
        console.log('WP Property Suite Shortcode rendered - Container: <?php echo esc_js($container_id); ?>');
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Enqueue React assets
 */
function wps_enqueue_assets($force = false) {
    // Check if we're on a page with the shortcode
    global $post;
    
    // Debug: Log when this function runs
    error_log('WP Property Suite: Enqueue assets function called');
    
    if ($force || wps_post_has_search_shortcode($post)) {
        error_log('WP Property Suite: Shortcode found on page');
        
        $build_path = WPS_PLUGIN_PATH . 'build';
        error_log('WP Property Suite: Build path = ' . $build_path);
        
        // Check if build exists
        if (file_exists($build_path)) {
            error_log('WP Property Suite: Build folder exists');
            
            // Get the built files (with hash)
            $js_files = glob(WPS_PLUGIN_PATH . 'build/static/js/main.*.js');
            $css_files = glob(WPS_PLUGIN_PATH . 'build/static/css/main.*.css');
            
            error_log('WP Property Suite: JS files found = ' . count($js_files));
            error_log('WP Property Suite: CSS files found = ' . count($css_files));
            
            if (!empty($js_files)) {
                $js_file = basename($js_files[0]);
                $css_file = !empty($css_files) ? basename($css_files[0]) : null;
                
                error_log('WP Property Suite: Enqueuing JS = ' . $js_file);
                
                wp_enqueue_script(
                    'wps-react',
                    WPS_PLUGIN_URL . 'build/static/js/' . $js_file,
                    array(),
                    WPS_PLUGIN_VERSION,
                    true
                );
                
                // Pass REST API URL and settings to React
                wp_localize_script('wps-react', 'propertyPluginData', array(
                    'apiUrl' => esc_url_raw(rest_url('wps/v1')),
                    'nonce' => wp_create_nonce('wp_rest'),
                    'settings' => array(
                        'headerText' => get_option('wps_header_text', 'Find Your Dream Property'),
                        'bannerSubtitle' => get_option('wps_banner_subtitle', 'Discover the perfect home for your family'),
                        'bannerImage' => get_option('wps_banner_image', ''),
                        'bannerHeight' => get_option('wps_banner_height', '400'),
                        'bannerOverlay' => get_option('wps_banner_overlay', '50'),
                        'bannerOverlayColor' => get_option('wps_banner_overlay_color', '#000000'),
                        'primaryColor' => get_option('wps_primary_color', '#2563eb'),
                        'secondaryColor' => get_option('wps_secondary_color', '#10b981'),
                        'textColor' => get_option('wps_text_color', '#1f2937'),
                        'backgroundColor' => get_option('wps_background_color', '#f3f4f6'),
                        'cardBackground' => get_option('wps_card_background', '#ffffff'),
                        'fontFamily' => get_option('wps_font_family', 'Arial, sans-serif'),
                        'fontSize' => get_option('wps_font_size', '16'),
                        'propertiesPerPage' => get_option('wps_properties_per_page', '12'),
                        'showBadge' => get_option('wps_show_badge', '1'),
                        'showArea' => get_option('wps_show_area', '1'),
                        'showAddress' => get_option('wps_show_address', '1'),
                        'sidebarPosition' => get_option('wps_sidebar_position', 'left'),
                        'sidebarWidth' => get_option('wps_sidebar_width', '280'),
                        'enableFilters' => get_option('wps_enable_filters', '1'),
                        'enableCompare' => get_option('wps_enable_compare', '1'),
                        'enableLeadForm' => get_option('wps_enable_lead_form', '1'),
                        'leadFormTitle' => get_option('wps_lead_form_title', 'Interested in this property?'),
                        'contactEmail' => wps_get_contact_email(),
                        'contactPhone' => get_option('wps_contact_phone', ''),
                        'customCSS' => get_option('wps_custom_css', ''),
                        'googleAnalytics' => get_option('wps_google_analytics', ''),
                        // CTA Section
                        'ctaImage' => get_option('wps_cta_image', ''),
                        'ctaTitle' => get_option('wps_cta_title', 'Want to Sell or Rent Your Property?'),
                        'ctaDescription' => get_option('wps_cta_description', 'List your property with us and reach thousands of potential buyers and renters.'),
                        'ctaButtonText' => get_option('wps_cta_button_text', 'Add Property Now'),
                        'ctaButtonUrl' => get_option('wps_cta_button_url', '/wp-admin/post-new.php?post_type=property'),
                        'ctaBgColor' => get_option('wps_cta_bg_color', '#f0f9ff'),
                        'ctaTextColor' => get_option('wps_cta_text_color', '#1e3a5f'),
                        // Features Section
                        'featuresBgColor' => get_option('wps_features_bg_color', '#ffffff'),
                        'featuresTextColor' => get_option('wps_features_text_color', '#1f2937'),
                        'features' => array(
                            array(
                                'icon' => get_option('wps_feature_1_icon', 'fas fa-trophy'),
                                'title' => get_option('wps_feature_1_title', 'Trusted by Thousands'),
                                'description' => get_option('wps_feature_1_description', 'Join thousands of happy clients who found their perfect property.'),
                            ),
                            array(
                                'icon' => get_option('wps_feature_2_icon', 'fas fa-chart-bar'),
                                'title' => get_option('wps_feature_2_title', 'Wide Range of Properties'),
                                'description' => get_option('wps_feature_2_description', 'Explore a wide range of properties for sale and rent.'),
                            ),
                            array(
                                'icon' => get_option('wps_feature_3_icon', 'fas fa-users'),
                                'title' => get_option('wps_feature_3_title', 'Expert Agents'),
                                'description' => get_option('wps_feature_3_description', 'Work with experienced agents to find the best property.'),
                            ),
                            array(
                                'icon' => get_option('wps_feature_4_icon', 'fas fa-shield-alt'),
                                'title' => get_option('wps_feature_4_title', 'Secure & Easy Process'),
                                'description' => get_option('wps_feature_4_description', 'Enjoy a secure and hassle-free property buying or renting process.'),
                            ),
                        ),
                        // Single Property Page
                        'agentName' => get_option('wps_agent_name', 'John Smith'),
                        'agentPhoto' => get_option('wps_agent_photo', ''),
                        'agentRole' => get_option('wps_agent_role', 'Property Agent'),
                        'agentPhone' => get_option('wps_agent_phone', '+1 (555) 123-4567'),
                        'agentEmail' => get_option('wps_agent_email', ''),
                        'contactFormHeading' => get_option('wps_contact_form_heading', 'Get More Details'),
                        'contactFormSubtitle' => get_option('wps_contact_form_subtitle', 'Schedule a tour or request more information about this property.'),
                        'featuredLabel' => get_option('wps_featured_label', 'FEATURED PROPERTY'),
                        'scheduleTourUrl' => get_option('wps_schedule_tour_url', ''),
                        'socialFacebook' => get_option('wps_social_facebook', ''),
                        'socialTwitter' => get_option('wps_social_twitter', ''),
                        'socialLinkedin' => get_option('wps_social_linkedin', ''),
                        'socialInstagram' => get_option('wps_social_instagram', ''),
                                'currentUserId' => get_current_user_id(),
                    )
                ));
                
                // Enqueue CSS if exists
                if ($css_file) {
                    error_log('WP Property Suite: Enqueuing CSS = ' . $css_file);
                    wp_enqueue_style(
                        'wps-styles',
                        WPS_PLUGIN_URL . 'build/static/css/' . $css_file,
                        array(),
                        WPS_PLUGIN_VERSION
                    );
                }
                
                // Enqueue Font Awesome
                wp_enqueue_style(
                    'font-awesome',
                    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
                    array(),
                    '6.5.1'
                );
            } else {
                error_log('WP Property Suite: No JS files found!');
            }
        } else {
            error_log('WP Property Suite: Build folder does NOT exist at ' . $build_path);
        }
    } else {
        error_log('WP Property Suite: Shortcode NOT found on this page');
    }
}

function wps_viewport_meta() {
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">' . "\n";
}
