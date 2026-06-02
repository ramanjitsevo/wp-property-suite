<?php
/**
 * Plugin Name: Property Plugin
 * Description: A headless WordPress plugin using React as frontend with custom Property post type
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: property-plugin
 */

/**
 * Property Plugin - Main Plugin File
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PROPERTY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PROPERTY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PROPERTY_PLUGIN_VERSION', '1.0.0');

/**
 * Initialize the plugin
 */
function property_plugin_init() {
    // Register custom post type
    add_action('init', 'property_plugin_register_post_type');
    
    // Register taxonomies
    add_action('init', 'property_plugin_register_taxonomies');
    
    // Register REST API routes
    add_action('rest_api_init', 'property_plugin_register_routes');
    
    // Register shortcode
    add_shortcode('property_plugin', 'property_plugin_shortcode');
    
    // Enqueue scripts and styles
    add_action('wp_enqueue_scripts', 'property_plugin_enqueue_assets');
    
    // Remove sidebars and make full width when shortcode is present
    add_action('wp', 'property_plugin_handle_layout');
    
    // Add meta boxes for property details
    add_action('add_meta_boxes', 'property_plugin_add_meta_boxes');
    add_action('save_post', 'property_plugin_save_meta_boxes');
}
add_action('plugins_loaded', 'property_plugin_init');

/**
 * Register Property Custom Post Type
 */
function property_plugin_register_post_type() {
    $labels = array(
        'name'                  => _x('Properties', 'Post Type General Name', 'property-plugin'),
        'singular_name'         => _x('Property', 'Post Type Singular Name', 'property-plugin'),
        'menu_name'             => __('Properties', 'property-plugin'),
        'name_admin_bar'        => __('Property', 'property-plugin'),
        'archives'              => __('Property Archives', 'property-plugin'),
        'attributes'            => __('Property Attributes', 'property-plugin'),
        'parent_item_colon'     => __('Parent Property:', 'property-plugin'),
        'all_items'             => __('All Properties', 'property-plugin'),
        'add_new_item'          => __('Add New Property', 'property-plugin'),
        'add_new'               => __('Add New', 'property-plugin'),
        'new_item'              => __('New Property', 'property-plugin'),
        'edit_item'             => __('Edit Property', 'property-plugin'),
        'update_item'           => __('Update Property', 'property-plugin'),
        'view_item'             => __('View Property', 'property-plugin'),
        'view_items'            => __('View Properties', 'property-plugin'),
        'search_items'          => __('Search Properties', 'property-plugin'),
        'not_found'             => __('Not found', 'property-plugin'),
        'not_found_in_trash'    => __('Not found in Trash', 'property-plugin'),
        'featured_image'        => __('Featured Image', 'property-plugin'),
        'set_featured_image'    => __('Set featured image', 'property-plugin'),
        'remove_featured_image' => __('Remove featured image', 'property-plugin'),
        'use_featured_image'    => __('Use as featured image', 'property-plugin'),
        'insert_into_item'      => __('Insert into property', 'property-plugin'),
        'uploaded_to_this_item' => __('Uploaded to this property', 'property-plugin'),
        'items_list'            => __('Properties list', 'property-plugin'),
        'items_list_navigation' => __('Properties list navigation', 'property-plugin'),
        'filter_items_list'     => __('Filter properties list', 'property-plugin'),
    );
    
    $args = array(
        'label'                 => __('Property', 'property-plugin'),
        'description'           => __('Property listings for real estate', 'property-plugin'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
        'taxonomies'            => array('property-type', 'property-location', 'bedrooms', 'bathrooms'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-building',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'show_in_rest'          => true,
        'rest_base'             => 'properties',
        'capability_type'       => 'post',
    );
    
    register_post_type('property', $args);
}

/**
 * Register Property Taxonomies
 */
function property_plugin_register_taxonomies() {
    // Property Type taxonomy
    register_taxonomy('property-type', 'property', array(
        'labels' => array(
            'name'              => __('Property Types', 'property-plugin'),
            'singular_name'     => __('Property Type', 'property-plugin'),
            'search_items'      => __('Search Property Types', 'property-plugin'),
            'all_items'         => __('All Property Types', 'property-plugin'),
            'edit_item'         => __('Edit Property Type', 'property-plugin'),
            'update_item'       => __('Update Property Type', 'property-plugin'),
            'add_new_item'      => __('Add New Property Type', 'property-plugin'),
            'new_item_name'     => __('New Property Type Name', 'property-plugin'),
            'menu_name'         => __('Property Types', 'property-plugin'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'property-type'),
        'show_in_rest'      => true,
    ));
    
    // Property Location taxonomy
    register_taxonomy('property-location', 'property', array(
        'labels' => array(
            'name'              => __('Locations', 'property-plugin'),
            'singular_name'     => __('Location', 'property-plugin'),
            'search_items'      => __('Search Locations', 'property-plugin'),
            'all_items'         => __('All Locations', 'property-plugin'),
            'edit_item'         => __('Edit Location', 'property-plugin'),
            'update_item'       => __('Update Location', 'property-plugin'),
            'add_new_item'      => __('Add New Location', 'property-plugin'),
            'new_item_name'     => __('New Location Name', 'property-plugin'),
            'menu_name'         => __('Locations', 'property-plugin'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'location'),
        'show_in_rest'      => true,
    ));
    
    // Bedrooms taxonomy
    register_taxonomy('bedrooms', 'property', array(
        'labels' => array(
            'name'              => __('Bedrooms', 'property-plugin'),
            'singular_name'     => __('Bedroom', 'property-plugin'),
            'search_items'      => __('Search Bedrooms', 'property-plugin'),
            'all_items'         => __('All Bedrooms', 'property-plugin'),
            'edit_item'         => __('Edit Bedroom', 'property-plugin'),
            'update_item'       => __('Update Bedroom', 'property-plugin'),
            'add_new_item'      => __('Add New Bedroom', 'property-plugin'),
            'new_item_name'     => __('New Bedroom', 'property-plugin'),
            'menu_name'         => __('Bedrooms', 'property-plugin'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'bedrooms'),
        'show_in_rest'      => true,
    ));
    
    // Bathrooms taxonomy
    register_taxonomy('bathrooms', 'property', array(
        'labels' => array(
            'name'              => __('Bathrooms', 'property-plugin'),
            'singular_name'     => __('Bathroom', 'property-plugin'),
            'search_items'      => __('Search Bathrooms', 'property-plugin'),
            'all_items'         => __('All Bathrooms', 'property-plugin'),
            'edit_item'         => __('Edit Bathroom', 'property-plugin'),
            'update_item'       => __('Update Bathroom', 'property-plugin'),
            'add_new_item'      => __('Add New Bathroom', 'property-plugin'),
            'new_item_name'     => __('New Bathroom', 'property-plugin'),
            'menu_name'         => __('Bathrooms', 'property-plugin'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'bathrooms'),
        'show_in_rest'      => true,
    ));
}

/**
 * Add Meta Boxes for Property Details
 */
function property_plugin_add_meta_boxes() {
    add_meta_box(
        'property_details',
        __('Property Details', 'property-plugin'),
        'property_plugin_meta_box_callback',
        'property',
        'normal',
        'high'
    );
}

/**
 * Meta Box Callback
 */
function property_plugin_meta_box_callback($post) {
    wp_nonce_field('property_plugin_meta_box', 'property_plugin_meta_box_nonce');
    
    $price = get_post_meta($post->ID, '_property_price', true);
    $area = get_post_meta($post->ID, '_property_area', true);
    $address = get_post_meta($post->ID, '_property_address', true);
    $status = get_post_meta($post->ID, '_property_status', true);
    
    ?>
    <p>
        <label for="property_price"><?php _e('Price:', 'property-plugin'); ?></label><br>
        <input type="text" id="property_price" name="property_price" value="<?php echo esc_attr($price); ?>" style="width: 100%;" placeholder="e.g., 500000">
    </p>
    <p>
        <label for="property_area"><?php _e('Area (sq ft):', 'property-plugin'); ?></label><br>
        <input type="number" id="property_area" name="property_area" value="<?php echo esc_attr($area); ?>" style="width: 100%;" placeholder="e.g., 1500">
    </p>
    <p>
        <label for="property_address"><?php _e('Full Address:', 'property-plugin'); ?></label><br>
        <input type="text" id="property_address" name="property_address" value="<?php echo esc_attr($address); ?>" style="width: 100%;" placeholder="e.g., 123 Main St, New York, USA">
    </p>
    <p>
        <label for="property_status"><?php _e('Status:', 'property-plugin'); ?></label><br>
        <select id="property_status" name="property_status" style="width: 100%;">
            <option value="for-sale" <?php selected($status, 'for-sale'); ?>><?php _e('For Sale', 'property-plugin'); ?></option>
            <option value="for-rent" <?php selected($status, 'for-rent'); ?>><?php _e('For Rent', 'property-plugin'); ?></option>
            <option value="sold" <?php selected($status, 'sold'); ?>><?php _e('Sold', 'property-plugin'); ?></option>
            <option value="rented" <?php selected($status, 'rented'); ?>><?php _e('Rented', 'property-plugin'); ?></option>
        </select>
    </p>
    <?php
}

/**
 * Save Meta Box Data
 */
function property_plugin_save_meta_boxes($post_id) {
    if (!isset($_POST['property_plugin_meta_box_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['property_plugin_meta_box_nonce'], 'property_plugin_meta_box')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['property_price'])) {
        update_post_meta($post_id, '_property_price', sanitize_text_field($_POST['property_price']));
    }
    
    if (isset($_POST['property_area'])) {
        update_post_meta($post_id, '_property_area', sanitize_text_field($_POST['property_area']));
    }
    
    if (isset($_POST['property_address'])) {
        update_post_meta($post_id, '_property_address', sanitize_text_field($_POST['property_address']));
    }
    
    if (isset($_POST['property_status'])) {
        update_post_meta($post_id, '_property_status', sanitize_text_field($_POST['property_status']));
    }
}

/**
 * Handle layout changes when shortcode is present
 */
function property_plugin_handle_layout() {
    global $post;
    
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'property_plugin')) {
        // Remove default WordPress sidebars
        remove_action('sidebar', 'dynamic_sidebar');
        
        // Add custom CSS to hide sidebars and make content full width
        add_action('wp_head', 'property_plugin_custom_styles', 100);
        
        // Add body class for custom styling
        add_filter('body_class', 'property_plugin_add_body_class');
        
        // Force full width template if theme supports it
        add_filter('template_include', 'property_plugin_force_full_width');
    }
}

/**
 * Force full width template
 */
function property_plugin_force_full_width($template) {
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
function property_plugin_custom_styles() {
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
        aside:not(.properties-sidebar):not([class*="property-plugin"]) {
            display: none !important;
            width: 0 !important;
            visibility: hidden !important;
        }
        
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
        main.site-main {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            float: none !important;
        }
        
        /* Remove WordPress container constraints */
        .site-content .container,
        #main .container,
        .container.content-area,
        .wrapper.site-content {
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
        }
        
        /* Hide WordPress page title and comments */
        .entry-title,
        .page-title,
        h1.entry-title,
        .post-title,
        .page-header,
        .entry-header,
        #comments,
        .comments-area {
            display: none !important;
        }
        
        /* Ensure property plugin container takes full width */
        .property-plugin-container {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* IMPORTANT: Keep property plugin sidebar visible */
        .property-plugin-app .properties-sidebar,
        .property-plugin-app aside.properties-sidebar,
        .property-plugin-container .properties-sidebar,
        aside.properties-sidebar {
            display: block !important;
            width: 280px !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
    </style>
    <?php
}

/**
 * Add custom body class
 */
function property_plugin_add_body_class($classes) {
    $classes[] = 'property-plugin-full-width';
    $classes[] = 'property-plugin-no-sidebar';
    $classes[] = 'property-plugin-page';
    return $classes;
}

/**
 * Register REST API routes
 */
function property_plugin_register_routes() {
    register_rest_route('property-plugin/v1', '/properties', array(
        'methods' => 'GET',
        'callback' => 'property_plugin_get_properties',
        'permission_callback' => '__return_true',
    ));
    
    register_rest_route('property-plugin/v1', '/properties/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'property_plugin_get_property',
        'permission_callback' => '__return_true',
    ));
    
    // Get taxonomy terms
    register_rest_route('property-plugin/v1', '/taxonomies', array(
        'methods' => 'GET',
        'callback' => 'property_plugin_get_taxonomies',
        'permission_callback' => '__return_true',
    ));
}

/**
 * Get all properties from WordPress
 */
function property_plugin_get_properties($request) {
    $args = array(
        'post_type' => 'property',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    $posts = get_posts($args);
    $properties = array();
    
    foreach ($posts as $post) {
        // Get taxonomies
        $property_types = wp_get_post_terms($post->ID, 'property-type', array('fields' => 'names'));
        $locations = wp_get_post_terms($post->ID, 'property-location', array('fields' => 'names'));
        $bedrooms = wp_get_post_terms($post->ID, 'bedrooms', array('fields' => 'names'));
        $bathrooms = wp_get_post_terms($post->ID, 'bathrooms', array('fields' => 'names'));
        
        // Get meta data
        $price = get_post_meta($post->ID, '_property_price', true);
        $area = get_post_meta($post->ID, '_property_area', true);
        $address = get_post_meta($post->ID, '_property_address', true);
        $status = get_post_meta($post->ID, '_property_status', true);
        
        $properties[] = array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'date' => $post->post_date,
            'thumbnail' => get_the_post_thumbnail_url($post->ID, 'large'),
            'price' => $price ? '$' . number_format($price) : 'N/A',
            'area' => $area ? $area . ' sq ft' : 'N/A',
            'address' => $address ?: 'Address not available',
            'status' => $status ?: 'for-sale',
            'property_type' => !empty($property_types) ? $property_types[0] : 'Property',
            'location' => !empty($locations) ? $locations[0] : 'Location',
            'bedrooms' => !empty($bedrooms) ? $bedrooms[0] : 'N/A',
            'bathrooms' => !empty($bathrooms) ? $bathrooms[0] : 'N/A',
        );
    }
    
    return rest_ensure_response($properties);
}

/**
 * Get single property
 */
function property_plugin_get_property($request) {
    $id = $request['id'];
    $post = get_post($id);
    
    if (!$post || $post->post_type !== 'property') {
        return new WP_Error('not_found', 'Property not found', array('status' => 404));
    }
    
    // Get taxonomies
    $property_types = wp_get_post_terms($post->ID, 'property-type', array('fields' => 'names'));
    $locations = wp_get_post_terms($post->ID, 'property-location', array('fields' => 'names'));
    $bedrooms = wp_get_post_terms($post->ID, 'bedrooms', array('fields' => 'names'));
    $bathrooms = wp_get_post_terms($post->ID, 'bathrooms', array('fields' => 'names'));
    
    // Get meta data
    $price = get_post_meta($post->ID, '_property_price', true);
    $area = get_post_meta($post->ID, '_property_area', true);
    $address = get_post_meta($post->ID, '_property_address', true);
    $status = get_post_meta($post->ID, '_property_status', true);
    
    $property = array(
        'id' => $post->ID,
        'title' => $post->post_title,
        'content' => $post->post_content,
        'excerpt' => $post->post_excerpt,
        'date' => $post->post_date,
        'thumbnail' => get_the_post_thumbnail_url($post->ID, 'large'),
        'price' => $price ? '$' . number_format($price) : 'N/A',
        'area' => $area ? $area . ' sq ft' : 'N/A',
        'address' => $address ?: 'Address not available',
        'status' => $status ?: 'for-sale',
        'property_type' => !empty($property_types) ? $property_types[0] : 'Property',
        'location' => !empty($locations) ? $locations[0] : 'Location',
        'bedrooms' => !empty($bedrooms) ? $bedrooms[0] : 'N/A',
        'bathrooms' => !empty($bathrooms) ? $bathrooms[0] : 'N/A',
    );
    
    return rest_ensure_response($property);
}

/**
 * Get all taxonomy terms
 */
function property_plugin_get_taxonomies($request) {
    $taxonomies = array(
        'property_types' => get_terms(array(
            'taxonomy' => 'property-type',
            'hide_empty' => true,
        )),
        'locations' => get_terms(array(
            'taxonomy' => 'property-location',
            'hide_empty' => true,
        )),
        'bedrooms' => get_terms(array(
            'taxonomy' => 'bedrooms',
            'hide_empty' => true,
        )),
        'bathrooms' => get_terms(array(
            'taxonomy' => 'bathrooms',
            'hide_empty' => true,
        )),
    );
    
    return rest_ensure_response($taxonomies);
}

/**
 * Shortcode callback to display React app
 */
function property_plugin_shortcode($atts) {
    // Unique ID for the container
    $container_id = 'property-plugin-root-' . uniqid();
    
    ob_start();
    ?>
    <div id="<?php echo esc_attr($container_id); ?>" class="property-plugin-container" data-container-id="<?php echo esc_attr($container_id); ?>"></div>
    <script>
        console.log('Property Plugin Shortcode rendered - Container: <?php echo esc_js($container_id); ?>');
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Enqueue React assets
 */
function property_plugin_enqueue_assets() {
    // Check if we're on a page with the shortcode
    global $post;
    
    // Debug: Log when this function runs
    error_log('Property Plugin: Enqueue assets function called');
    
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'property_plugin')) {
        error_log('Property Plugin: Shortcode found on page');
        
        $build_path = PROPERTY_PLUGIN_PATH . 'build';
        error_log('Property Plugin: Build path = ' . $build_path);
        
        // Check if build exists
        if (file_exists($build_path)) {
            error_log('Property Plugin: Build folder exists');
            
            // Get the built files (with hash)
            $js_files = glob(PROPERTY_PLUGIN_PATH . 'build/static/js/main.*.js');
            $css_files = glob(PROPERTY_PLUGIN_PATH . 'build/static/css/main.*.css');
            
            error_log('Property Plugin: JS files found = ' . count($js_files));
            error_log('Property Plugin: CSS files found = ' . count($css_files));
            
            if (!empty($js_files)) {
                $js_file = basename($js_files[0]);
                $css_file = !empty($css_files) ? basename($css_files[0]) : null;
                
                error_log('Property Plugin: Enqueuing JS = ' . $js_file);
                
                wp_enqueue_script(
                    'property-plugin-react',
                    PROPERTY_PLUGIN_URL . 'build/static/js/' . $js_file,
                    array(),
                    PROPERTY_PLUGIN_VERSION,
                    true
                );
                
                // Pass REST API URL to React
                wp_localize_script('property-plugin-react', 'propertyPluginData', array(
                    'apiUrl' => esc_url_raw(rest_url('property-plugin/v1')),
                    'nonce' => wp_create_nonce('wp_rest'),
                ));
                
                // Enqueue CSS if exists
                if ($css_file) {
                    error_log('Property Plugin: Enqueuing CSS = ' . $css_file);
                    wp_enqueue_style(
                        'property-plugin-styles',
                        PROPERTY_PLUGIN_URL . 'build/static/css/' . $css_file,
                        array(),
                        PROPERTY_PLUGIN_VERSION
                    );
                }
            } else {
                error_log('Property Plugin: No JS files found!');
            }
        } else {
            error_log('Property Plugin: Build folder does NOT exist at ' . $build_path);
        }
    } else {
        error_log('Property Plugin: Shortcode NOT found on this page');
    }
}

/**
 * Activation hook
 */
function property_plugin_activate() {
    // Register post type and taxonomies before flushing
    property_plugin_register_post_type();
    property_plugin_register_taxonomies();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'property_plugin_activate');

/**
 * Deactivation hook
 */
function property_plugin_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'property_plugin_deactivate');
