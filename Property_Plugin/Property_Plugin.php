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

// Include admin settings
require_once PROPERTY_PLUGIN_PATH . 'includes/admin-settings.php';

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
        'taxonomies'            => array('property-type', 'property-location', 'bedrooms', 'bathrooms', 'property-floor'),
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
    
    // Floor taxonomy
    register_taxonomy('property-floor', 'property', array(
        'labels' => array(
            'name'              => __('Floors', 'property-plugin'),
            'singular_name'     => __('Floor', 'property-plugin'),
            'search_items'      => __('Search Floors', 'property-plugin'),
            'all_items'         => __('All Floors', 'property-plugin'),
            'edit_item'         => __('Edit Floor', 'property-plugin'),
            'update_item'       => __('Update Floor', 'property-plugin'),
            'add_new_item'      => __('Add New Floor', 'property-plugin'),
            'new_item_name'     => __('New Floor Name', 'property-plugin'),
            'menu_name'         => __('Floors', 'property-plugin'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'floor'),
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
    $city = get_post_meta($post->ID, '_property_city', true);
    $state = get_post_meta($post->ID, '_property_state', true);
    $zipcode = get_post_meta($post->ID, '_property_zipcode', true);
    $country = get_post_meta($post->ID, '_property_country', true);
    $status = get_post_meta($post->ID, '_property_status', true);
    
    $google_api_key = get_option('property_plugin_google_api_key', '');
    
    ?>
    <p>
        <label for="property_price"><?php _e('Price:', 'property-plugin'); ?></label><br>
        <input type="text" id="property_price" name="property_price" value="<?php echo esc_attr($price); ?>" style="width: 100%;" placeholder="e.g., 500000">
    </p>
    <p>
        <label for="property_area"><?php _e('Area (sq ft):', 'property-plugin'); ?></label><br>
        <input type="number" id="property_area" name="property_area" value="<?php echo esc_attr($area); ?>" style="width: 100%;" placeholder="e.g., 1500">
    </p>
    
    <hr style="margin: 20px 0; border: 0; border-top: 1px solid #ccc;">
    
    <h3><?php _e('Location Details', 'property-plugin'); ?></h3>
    
    <p>
        <label for="property_address"><?php _e('Full Address:', 'property-plugin'); ?></label><br>
        <input type="text" id="property_address" name="property_address" value="<?php echo esc_attr($address); ?>" style="width: 100%;" placeholder="Start typing to autocomplete..." class="google-autocomplete-address">
        <?php if ($google_api_key): ?>
        <script>
        jQuery(document).ready(function($) {
            if (typeof google === 'undefined') {
                var script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key=<?php echo esc_js($google_api_key); ?>&libraries=places';
                script.async = true;
                script.defer = true;
                script.onload = function() {
                    initAutocomplete();
                };
                document.head.appendChild(script);
            } else {
                initAutocomplete();
            }
            
            function initAutocomplete() {
                var input = document.getElementById('property_address');
                if (!input) return;
                
                var autocomplete = new google.maps.places.Autocomplete(input, {
                    types: ['geocode']
                });
                
                autocomplete.setFields(['address_component']);
                
                autocomplete.addListener('place_changed', function() {
                    var place = autocomplete.getPlace();
                    
                    var components = {};
                    for (var component of place.address_components) {
                        components[component.types[0]] = component.long_name;
                    }
                    
                    $('#property_city').val(components.locality || components.sublocality || '');
                    $('#property_state').val(components.administrative_area_level_1 || '');
                    $('#property_zipcode').val(components.postal_code || '');
                    $('#property_country').val(components.country || '');
                });
            }
        });
        </script>
        <?php endif; ?>
        <p class="description"><?php _e('Start typing an address and select from suggestions', 'property-plugin'); ?></p>
    </p>
    
    <table class="form-table" style="margin-top: 15px;">
        <tr>
            <th><label for="property_city"><?php _e('City:', 'property-plugin'); ?></label></th>
            <td><input type="text" id="property_city" name="property_city" value="<?php echo esc_attr($city); ?>" style="width: 100%;" placeholder="City"></td>
        </tr>
        <tr>
            <th><label for="property_state"><?php _e('State:', 'property-plugin'); ?></label></th>
            <td><input type="text" id="property_state" name="property_state" value="<?php echo esc_attr($state); ?>" style="width: 100%;" placeholder="State"></td>
        </tr>
        <tr>
            <th><label for="property_zipcode"><?php _e('Zip Code:', 'property-plugin'); ?></label></th>
            <td><input type="text" id="property_zipcode" name="property_zipcode" value="<?php echo esc_attr($zipcode); ?>" style="width: 100%;" placeholder="Zip Code"></td>
        </tr>
        <tr>
            <th><label for="property_country"><?php _e('Country:', 'property-plugin'); ?></label></th>
            <td><input type="text" id="property_country" name="property_country" value="<?php echo esc_attr($country); ?>" style="width: 100%;" placeholder="Country"></td>
        </tr>
    </table>
    
    <hr style="margin: 20px 0; border: 0; border-top: 1px solid #ccc;">
    
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
    
    if (isset($_POST['property_city'])) {
        update_post_meta($post_id, '_property_city', sanitize_text_field($_POST['property_city']));
    }
    
    if (isset($_POST['property_state'])) {
        update_post_meta($post_id, '_property_state', sanitize_text_field($_POST['property_state']));
    }
    
    if (isset($_POST['property_zipcode'])) {
        update_post_meta($post_id, '_property_zipcode', sanitize_text_field($_POST['property_zipcode']));
    }
    
    if (isset($_POST['property_country'])) {
        update_post_meta($post_id, '_property_country', sanitize_text_field($_POST['property_country']));
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
 * Format price with selected currency
 */
function property_plugin_format_price($price) {
    if (!$price) {
        return 'N/A';
    }
    
    $currency = get_option('property_plugin_default_currency', 'USD');
    
    $currency_symbols = array(
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'INR' => '₹',
        'PKR' => 'Rs ',
    );
    
    $symbol = isset($currency_symbols[$currency]) ? $currency_symbols[$currency] : '$';
    
    return $symbol . number_format($price);
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
        $floors = wp_get_post_terms($post->ID, 'property-floor', array('fields' => 'names'));
        
        // Get custom taxonomies
        $custom_taxonomies = get_option('property_plugin_custom_taxonomies', array());
        $custom_taxonomy_data = array();
        foreach ($custom_taxonomies as $tax) {
            $terms = wp_get_post_terms($post->ID, $tax['slug'], array('fields' => 'names'));
            $custom_taxonomy_data[$tax['slug']] = !empty($terms) ? $terms[0] : 'N/A';
        }
        
        // Get meta data
        $price = get_post_meta($post->ID, '_property_price', true);
        $area = get_post_meta($post->ID, '_property_area', true);
        $address = get_post_meta($post->ID, '_property_address', true);
        $city = get_post_meta($post->ID, '_property_city', true);
        $state = get_post_meta($post->ID, '_property_state', true);
        $zipcode = get_post_meta($post->ID, '_property_zipcode', true);
        $country = get_post_meta($post->ID, '_property_country', true);
        $status = get_post_meta($post->ID, '_property_status', true);
        
        $property_data = array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'date' => $post->post_date,
            'thumbnail' => get_the_post_thumbnail_url($post->ID, 'large'),
            'price' => property_plugin_format_price($price),
            'area' => $area ? $area . ' sq ft' : 'N/A',
            'address' => $address ?: 'Address not available',
            'city' => $city ?: '',
            'state' => $state ?: '',
            'zipcode' => $zipcode ?: '',
            'country' => $country ?: '',
            'status' => $status ?: 'for-sale',
            'property_type' => !empty($property_types) ? $property_types[0] : 'Property',
            'location' => !empty($locations) ? $locations[0] : 'Location',
            'bedrooms' => !empty($bedrooms) ? $bedrooms[0] : 'N/A',
            'bathrooms' => !empty($bathrooms) ? $bathrooms[0] : 'N/A',
            'floor' => !empty($floors) ? $floors[0] : 'N/A',
        );
        
        // Add custom taxonomy data
        $property_data = array_merge($property_data, $custom_taxonomy_data);
        
        $properties[] = $property_data;
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
    $floors = wp_get_post_terms($post->ID, 'property-floor', array('fields' => 'names'));
    
    // Get custom taxonomies
    $custom_taxonomies = get_option('property_plugin_custom_taxonomies', array());
    $custom_taxonomy_data = array();
    foreach ($custom_taxonomies as $tax) {
        $terms = wp_get_post_terms($post->ID, $tax['slug'], array('fields' => 'names'));
        $custom_taxonomy_data[$tax['slug']] = !empty($terms) ? $terms[0] : 'N/A';
    }
    
    // Get meta data
    $price = get_post_meta($post->ID, '_property_price', true);
    $area = get_post_meta($post->ID, '_property_area', true);
    $address = get_post_meta($post->ID, '_property_address', true);
    $city = get_post_meta($post->ID, '_property_city', true);
    $state = get_post_meta($post->ID, '_property_state', true);
    $zipcode = get_post_meta($post->ID, '_property_zipcode', true);
    $country = get_post_meta($post->ID, '_property_country', true);
    $status = get_post_meta($post->ID, '_property_status', true);
    
    $property_data = array(
        'id' => $post->ID,
        'title' => $post->post_title,
        'content' => $post->post_content,
        'excerpt' => $post->post_excerpt,
        'date' => $post->post_date,
        'thumbnail' => get_the_post_thumbnail_url($post->ID, 'large'),
        'price' => property_plugin_format_price($price),
        'area' => $area ? $area . ' sq ft' : 'N/A',
        'address' => $address ?: 'Address not available',
        'city' => $city ?: '',
        'state' => $state ?: '',
        'zipcode' => $zipcode ?: '',
        'country' => $country ?: '',
        'status' => $status ?: 'for-sale',
        'property_type' => !empty($property_types) ? $property_types[0] : 'Property',
        'location' => !empty($locations) ? $locations[0] : 'Location',
        'bedrooms' => !empty($bedrooms) ? $bedrooms[0] : 'N/A',
        'bathrooms' => !empty($bathrooms) ? $bathrooms[0] : 'N/A',
        'floor' => !empty($floors) ? $floors[0] : 'N/A',
    );
    
    // Add custom taxonomy data
    $property_data = array_merge($property_data, $custom_taxonomy_data);
    
    return rest_ensure_response($property_data);
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
        'floors' => get_terms(array(
            'taxonomy' => 'property-floor',
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
                
                // Pass REST API URL and settings to React
                wp_localize_script('property-plugin-react', 'propertyPluginData', array(
                    'apiUrl' => esc_url_raw(rest_url('property-plugin/v1')),
                    'nonce' => wp_create_nonce('wp_rest'),
                    'settings' => array(
                        'headerText' => get_option('property_plugin_header_text', 'Find Your Dream Property'),
                        'bannerSubtitle' => get_option('property_plugin_banner_subtitle', 'Discover the perfect home for your family'),
                        'bannerImage' => get_option('property_plugin_banner_image', ''),
                        'bannerHeight' => get_option('property_plugin_banner_height', '400'),
                        'bannerOverlay' => get_option('property_plugin_banner_overlay', '50'),
                        'bannerOverlayColor' => get_option('property_plugin_banner_overlay_color', '#000000'),
                        'primaryColor' => get_option('property_plugin_primary_color', '#2563eb'),
                        'secondaryColor' => get_option('property_plugin_secondary_color', '#10b981'),
                        'textColor' => get_option('property_plugin_text_color', '#1f2937'),
                        'backgroundColor' => get_option('property_plugin_background_color', '#f3f4f6'),
                        'cardBackground' => get_option('property_plugin_card_background', '#ffffff'),
                        'fontFamily' => get_option('property_plugin_font_family', 'Arial, sans-serif'),
                        'fontSize' => get_option('property_plugin_font_size', '16'),
                        'propertiesPerPage' => get_option('property_plugin_properties_per_page', '12'),
                        'cardLayout' => get_option('property_plugin_card_layout', 'grid'),
                        'showBadge' => get_option('property_plugin_show_badge', '1'),
                        'showArea' => get_option('property_plugin_show_area', '1'),
                        'showAddress' => get_option('property_plugin_show_address', '1'),
                        'sidebarPosition' => get_option('property_plugin_sidebar_position', 'left'),
                        'sidebarWidth' => get_option('property_plugin_sidebar_width', '280'),
                        'enableFilters' => get_option('property_plugin_enable_filters', '1'),
                        'enableCompare' => get_option('property_plugin_enable_compare', '1'),
                        'enableLeadForm' => get_option('property_plugin_enable_lead_form', '1'),
                        'leadFormTitle' => get_option('property_plugin_lead_form_title', 'Interested in this property?'),
                        'contactEmail' => get_option('property_plugin_contact_email', ''),
                        'contactPhone' => get_option('property_plugin_contact_phone', ''),
                        'customCSS' => get_option('property_plugin_custom_css', ''),
                        'googleAnalytics' => get_option('property_plugin_google_analytics', ''),
                    )
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
                
                // Enqueue Font Awesome
                wp_enqueue_style(
                    'font-awesome',
                    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
                    array(),
                    '6.5.1'
                );
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
