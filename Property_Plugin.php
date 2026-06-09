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
    
    // Ensure WordPress media uploader is available
    wp_enqueue_media();
    
    $price = get_post_meta($post->ID, '_property_price', true);
    $area = get_post_meta($post->ID, '_property_area', true);
    $address = get_post_meta($post->ID, '_property_address', true);
    $city = get_post_meta($post->ID, '_property_city', true);
    $state = get_post_meta($post->ID, '_property_state', true);
    $zipcode = get_post_meta($post->ID, '_property_zipcode', true);
    $country = get_post_meta($post->ID, '_property_country', true);
    $status = get_post_meta($post->ID, '_property_status', true);
    $garage = get_post_meta($post->ID, '_property_garage', true);
    $gallery_ids = get_post_meta($post->ID, '_property_gallery', true);
    $gallery_ids_array = !empty($gallery_ids) ? array_filter(explode(',', $gallery_ids)) : array();
    // Per-property agent fields
    $agent_name = get_post_meta($post->ID, '_property_agent_name', true);
    $agent_phone = get_post_meta($post->ID, '_property_agent_phone', true);
    $agent_email = get_post_meta($post->ID, '_property_agent_email', true);
    $agent_photo = get_post_meta($post->ID, '_property_agent_photo', true);
    // Additional details (repeatable)
    $additional_details_raw = get_post_meta($post->ID, '_property_additional_details', true);
    $additional_details = !empty($additional_details_raw) ? json_decode($additional_details_raw, true) : array();
    // FAQs (repeatable)
    $property_faqs_raw = get_post_meta($post->ID, '_property_faqs', true);
    $property_faqs = !empty($property_faqs_raw) ? json_decode($property_faqs_raw, true) : array();
    
    $google_api_key = get_option('property_plugin_google_api_key', '');
    
    ?>
    <div class="pp-tabs" style="display:flex; gap:16px; align-items:flex-start;">
        <div class="pp-tab-list" style="width:220px; background:#fff; border:1px solid #ccd0d4; border-radius:4px; padding:8px;">
            <button type="button" class="pp-tab-button active" data-tab="basic" style="width:100%; padding:10px; text-align:left; border:none; background:transparent; cursor:pointer;"><?php _e('Basic', 'property-plugin'); ?></button>
            <button type="button" class="pp-tab-button" data-tab="location" style="width:100%; padding:10px; text-align:left; border:none; background:transparent; cursor:pointer;"><?php _e('Location', 'property-plugin'); ?></button>
            <button type="button" class="pp-tab-button" data-tab="features" style="width:100%; padding:10px; text-align:left; border:none; background:transparent; cursor:pointer;"><?php _e('Features', 'property-plugin'); ?></button>
            <button type="button" class="pp-tab-button" data-tab="gallery" style="width:100%; padding:10px; text-align:left; border:none; background:transparent; cursor:pointer;"><?php _e('Gallery', 'property-plugin'); ?></button>
            <button type="button" class="pp-tab-button" data-tab="agent" style="width:100%; padding:10px; text-align:left; border:none; background:transparent; cursor:pointer;"><?php _e('Agent', 'property-plugin'); ?></button>
            <button type="button" class="pp-tab-button" data-tab="faq" style="width:100%; padding:10px; text-align:left; border:none; background:transparent; cursor:pointer;"><?php _e('FAQs', 'property-plugin'); ?></button>
            <button type="button" class="pp-tab-button" data-tab="additional" style="width:100%; padding:10px; text-align:left; border:none; background:transparent; cursor:pointer;"><?php _e('Additional Details', 'property-plugin'); ?></button>
        </div>

        <div class="pp-tab-content" style="flex:1; background:#fff; border:1px solid #ccd0d4; border-radius:4px; padding:16px;">
            <div class="pp-tab-panel" id="pp-panel-basic">
    <p>
        <label for="property_price"><?php _e('Price:', 'property-plugin'); ?></label><br>
        <input type="text" id="property_price" name="property_price" value="<?php echo esc_attr($price); ?>" style="width: 100%;" placeholder="e.g., 500000">
    </p>
    <p>
        <label for="property_area"><?php _e('Area (sq ft):', 'property-plugin'); ?></label><br>
        <input type="number" id="property_area" name="property_area" value="<?php echo esc_attr($area); ?>" style="width: 100%;" placeholder="e.g., 1500">
    </p>
            </div>
    <div class="pp-tab-panel" id="pp-panel-location" style="display:none;">
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
    </div>
    
    <div class="pp-tab-panel" id="pp-panel-features" style="display:none;">
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

    <p>
        <label for="property_garage"><?php _e('Garage (number of parking spaces):', 'property-plugin'); ?></label><br>
        <input type="number" id="property_garage" name="property_garage" value="<?php echo esc_attr($garage); ?>" style="width: 100%;" placeholder="e.g., 2" min="0">
    </p>
    </div>

    <div class="pp-tab-panel" id="pp-panel-gallery" style="display:none;">
    <h3><?php _e('Property Gallery', 'property-plugin'); ?></h3>
    <p class="description" style="margin-bottom: 12px;">
        <?php _e('Select multiple images from the media library to display below the featured image on the single property page.', 'property-plugin'); ?>
    </p>

    <div id="property-gallery-preview" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px;">
        <?php foreach ($gallery_ids_array as $att_id):
            $thumb_url = wp_get_attachment_image_url(intval($att_id), 'thumbnail');
            if ($thumb_url): ?>
            <div class="pp-gallery-thumb" data-id="<?php echo esc_attr($att_id); ?>"
                 style="position: relative; width: 82px; height: 82px; border: 1px solid #ccd0d4; border-radius: 4px; overflow: hidden; cursor: move;">
                <img src="<?php echo esc_url($thumb_url); ?>" style="width:100%; height:100%; object-fit:cover; display:block;">
                <button type="button" class="pp-remove-gallery-thumb"
                        data-id="<?php echo esc_attr($att_id); ?>"
                        style="position:absolute; top:2px; right:2px; background:rgba(0,0,0,0.72); color:#fff; border:none; border-radius:50%; width:20px; height:20px; cursor:pointer; line-height:18px; font-size:15px; padding:0;">×</button>
            </div>
        <?php endif; endforeach; ?>
    </div>

    <input type="hidden" id="property_gallery_ids" name="property_gallery_ids"
           value="<?php echo esc_attr(implode(',', $gallery_ids_array)); ?>">

    <button type="button" id="pp-gallery-btn" class="button button-secondary">
        <span class="dashicons dashicons-format-gallery" style="margin-right: 5px;"></span>
        <?php _e('Select Gallery Images', 'property-plugin'); ?>
    </button>
    </div>

    <div class="pp-tab-panel" id="pp-panel-agent" style="display:none;">
    <hr style="margin: 20px 0; border: 0; border-top: 1px solid #ccc;">

    <h3><?php _e('Per-Property Agent (optional)', 'property-plugin'); ?></h3>
    <p class="description"><?php _e('Set an agent specifically for this property. These values override the global settings on the frontend when present.', 'property-plugin'); ?></p>

    <table class="form-table">
        <tr>
            <th><label for="property_agent_name"><?php _e('Agent Name:', 'property-plugin'); ?></label></th>
            <td><input type="text" id="property_agent_name" name="property_agent_name" value="<?php echo esc_attr($agent_name); ?>" style="width:100%;" placeholder="Agent Name"></td>
        </tr>
        <tr>
            <th><label for="property_agent_phone"><?php _e('Agent Phone:', 'property-plugin'); ?></label></th>
            <td><input type="text" id="property_agent_phone" name="property_agent_phone" value="<?php echo esc_attr($agent_phone); ?>" style="width:100%;" placeholder="+1 (555) 123-4567"></td>
        </tr>
        <tr>
            <th><label for="property_agent_email"><?php _e('Agent Email:', 'property-plugin'); ?></label></th>
            <td><input type="email" id="property_agent_email" name="property_agent_email" value="<?php echo esc_attr($agent_email); ?>" style="width:100%;" placeholder="agent@example.com"></td>
        </tr>
        <tr>
            <th><label for="property_agent_photo"><?php _e('Agent Photo:', 'property-plugin'); ?></label></th>
            <td>
                <div class="image-upload-container" style="text-align:left;">
                    <img id="property_agent_photo_preview"
                         src="<?php echo esc_url($agent_photo); ?>"
                         style="<?php echo $agent_photo ? '' : 'display:none;'; ?>width:80px; height:80px; border-radius:50%; object-fit:cover; margin-bottom:10px;" />
                    <br/>
                    <input type="hidden" id="property_agent_photo" name="property_agent_photo" value="<?php echo esc_attr($agent_photo); ?>" />
                    <button type="button" class="button pp-upload-img" data-target="property_agent_photo"><?php _e('Upload Photo', 'property-plugin'); ?></button>
                    <button type="button" class="button pp-remove-img" data-target="property_agent_photo" <?php echo $agent_photo ? '' : 'style="display:none;"'; ?>><?php _e('Remove', 'property-plugin'); ?></button>
                </div>
            </td>
        </tr>
    </table>
    </div>

    <div class="pp-tab-panel" id="pp-panel-faq" style="display:none;">
    <h3><?php _e('Property FAQs', 'property-plugin'); ?></h3>
    <p class="description"><?php _e('Add frequently asked questions for this property. These will appear in the FAQ tab on the frontend.', 'property-plugin'); ?></p>

    <div id="property-faqs-container" style="margin-bottom:12px;">
        <?php if (!empty($property_faqs) && is_array($property_faqs)): foreach ($property_faqs as $idx => $item): ?>
            <div class="property-faq-row" data-index="<?php echo esc_attr($idx); ?>" style="display:grid; grid-template-columns:1fr; gap:8px; margin-bottom:12px; padding:12px; border:1px solid #dcdcde; border-radius:4px; background:#f6f7f7;">
                <input type="text" name="faq_question[]" value="<?php echo esc_attr($item['question'] ?? ''); ?>" placeholder="Question" style="width:100%;" />
                <textarea name="faq_answer[]" placeholder="Answer" rows="3" style="width:100%;"><?php echo esc_textarea($item['answer'] ?? ''); ?></textarea>
                <button type="button" class="button remove-property-faq" style="justify-self:start;">Remove FAQ</button>
            </div>
        <?php endforeach; endif; ?>
    </div>
    <button type="button" id="add-property-faq" class="button">Add FAQ</button>
    </div>

    <div class="pp-tab-panel" id="pp-panel-additional" style="display:none;">
    <h3><?php _e('Additional Details (repeatable)', 'property-plugin'); ?></h3>
    <p class="description"><?php _e('Add any extra labeled details (e.g., Year Built, Parking) that will show in the Additional Detail tab on the frontend.', 'property-plugin'); ?></p>

    <div id="additional-details-container" style="margin-bottom:12px;">
        <?php if (!empty($additional_details) && is_array($additional_details)): foreach ($additional_details as $idx => $item): ?>
            <div class="additional-detail-row" data-index="<?php echo esc_attr($idx); ?>" style="display:flex; gap:8px; margin-bottom:8px;">
                <input type="text" name="additional_label[]" value="<?php echo esc_attr($item['label'] ?? ''); ?>" placeholder="Label (e.g., Year Built)" style="flex:1;" />
                <input type="text" name="additional_value[]" value="<?php echo esc_attr($item['value'] ?? ''); ?>" placeholder="Value" style="width:220px;" />
                <button type="button" class="button remove-additional">Remove</button>
            </div>
        <?php endforeach; endif; ?>
    </div>
    <button type="button" id="add-additional-detail" class="button">Add Detail</button>
    </div>
        </div>
    </div>

    <style>
    .pp-tab-list .pp-tab-button.active { background:#f0f6fc; color:#2271b1; font-weight:600; border-left:3px solid #2271b1; }
    .pp-tab-list .pp-tab-button { border-left:3px solid transparent; }
    </style>

    <script>
    // Define renderGalleryPreview BEFORE jQuery ready so it's available globally
    window.renderGalleryPreview = function() {
        var ids = jQuery('#property_gallery_ids').val().split(',').filter(Boolean);
        var $preview = jQuery('#property-gallery-preview').empty();

        if (ids.length === 0) return;

        // Use AJAX to fetch thumbnail URLs
        jQuery.post(ajaxurl, {
            action: 'pp_get_gallery_thumbs',
            ids: ids.join(','),
            nonce: '<?php echo wp_create_nonce('pp_gallery_nonce'); ?>'
        }, function(response) {
            if (!response.success) return;
            response.data.forEach(function(item) {
                $preview.append(
                    '<div class="pp-gallery-thumb" data-id="' + item.id + '"' +
                    ' style="position:relative; width:82px; height:82px; border:1px solid #ccd0d4; border-radius:4px; overflow:hidden;">' +
                    '<img src="' + item.thumb + '" style="width:100%; height:100%; object-fit:cover; display:block;">' +
                    '<button type="button" class="pp-remove-gallery-thumb" data-id="' + item.id + '"' +
                    ' style="position:absolute; top:2px; right:2px; background:rgba(0,0,0,0.72); color:#fff; border:none;' +
                    ' border-radius:50%; width:20px; height:20px; cursor:pointer; line-height:18px; font-size:15px; padding:0;">×</button>' +
                    '</div>'
                );
            });
        });
    };

    jQuery(document).ready(function($) {
            // Tab switching
            $('.pp-tab-button').on('click', function() {
                var tab = $(this).data('tab');
                $('.pp-tab-button').removeClass('active');
                $(this).addClass('active');
                $('.pp-tab-panel').hide();
                $('#pp-panel-' + tab).show();
            });

            // Ensure initial gallery preview is rendered if needed
            if (typeof renderGalleryPreview === 'function') {
                renderGalleryPreview();
            }
        var ppMediaFrame;

        $('#pp-gallery-btn').on('click', function(e) {
            e.preventDefault();

            if (ppMediaFrame) { ppMediaFrame.open(); return; }

            ppMediaFrame = wp.media({
                title: '<?php _e('Select Gallery Images', 'property-plugin'); ?>',
                button: { text: '<?php _e('Add to Gallery', 'property-plugin'); ?>' },
                multiple: true,
                library: { type: 'image' }
            });

            ppMediaFrame.on('select', function() {
                var attachments = ppMediaFrame.state().get('selection').toJSON();
                var currentIds = $('#property_gallery_ids').val().split(',').filter(Boolean);

                attachments.forEach(function(att) {
                    if (currentIds.indexOf(String(att.id)) === -1) {
                        currentIds.push(String(att.id));
                    }
                });

                $('#property_gallery_ids').val(currentIds.join(','));
                renderGalleryPreview();
            });

            ppMediaFrame.open();
        });

        $('#property-gallery-preview').on('click', '.pp-remove-gallery-thumb', function(e) {
            e.preventDefault();
            var removeId = String($(this).data('id'));
            var ids = $('#property_gallery_ids').val().split(',').filter(Boolean);
            ids = ids.filter(function(id) { return id !== removeId; });
            $('#property_gallery_ids').val(ids.join(','));
            $(this).closest('.pp-gallery-thumb').remove();
        });

            // Additional details repeatable
            $('#add-additional-detail').on('click', function(e) {
                e.preventDefault();
                var idx = $('#additional-details-container .additional-detail-row').length;
                var $row = $('<div class="additional-detail-row" data-index="' + idx + '" style="display:flex; gap:8px; margin-bottom:8px;">'
                    + '<input type="text" name="additional_label[]" placeholder="Label (e.g., Year Built)" style="flex:1;" />'
                    + '<input type="text" name="additional_value[]" placeholder="Value" style="width:220px;" />'
                    + '<button type="button" class="button remove-additional">Remove</button>'
                    + '</div>');
                $('#additional-details-container').append($row);
            });

            $(document).on('click', '.remove-additional', function(e) {
                e.preventDefault();
                $(this).closest('.additional-detail-row').remove();
            });

            // Property FAQs repeatable
            $('#add-property-faq').on('click', function(e) {
                e.preventDefault();
                var idx = $('#property-faqs-container .property-faq-row').length;
                var $row = $('<div class="property-faq-row" data-index="' + idx + '" style="display:grid; grid-template-columns:1fr; gap:8px; margin-bottom:12px; padding:12px; border:1px solid #dcdcde; border-radius:4px; background:#f6f7f7;">'
                    + '<input type="text" name="faq_question[]" placeholder="Question" style="width:100%;" />'
                    + '<textarea name="faq_answer[]" placeholder="Answer" rows="3" style="width:100%;"></textarea>'
                    + '<button type="button" class="button remove-property-faq" style="justify-self:start;">Remove FAQ</button>'
                    + '</div>');
                $('#property-faqs-container').append($row);
            });

            $(document).on('click', '.remove-property-faq', function(e) {
                e.preventDefault();
                $(this).closest('.property-faq-row').remove();
            });
    });
    </script>
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

    if (isset($_POST['property_garage'])) {
        update_post_meta($post_id, '_property_garage', sanitize_text_field($_POST['property_garage']));
    }

    if (isset($_POST['property_gallery_ids'])) {
        $raw_ids = sanitize_text_field($_POST['property_gallery_ids']);
        $clean_ids = array_filter(array_map('absint', explode(',', $raw_ids)));
        update_post_meta($post_id, '_property_gallery', implode(',', $clean_ids));
    }

    // Per-property agent fields
    if (isset($_POST['property_agent_name'])) {
        update_post_meta($post_id, '_property_agent_name', sanitize_text_field($_POST['property_agent_name']));
    }
    if (isset($_POST['property_agent_phone'])) {
        update_post_meta($post_id, '_property_agent_phone', sanitize_text_field($_POST['property_agent_phone']));
    }
    if (isset($_POST['property_agent_email'])) {
        update_post_meta($post_id, '_property_agent_email', sanitize_email($_POST['property_agent_email']));
    }
    if (isset($_POST['property_agent_photo'])) {
        update_post_meta($post_id, '_property_agent_photo', esc_url_raw($_POST['property_agent_photo']));
    }

    // Property FAQs (repeatable)
    if (isset($_POST['faq_question']) && is_array($_POST['faq_question'])) {
        $questions = array_map('sanitize_text_field', $_POST['faq_question']);
        $answers = isset($_POST['faq_answer']) && is_array($_POST['faq_answer']) ? array_map('sanitize_textarea_field', $_POST['faq_answer']) : array();
        $combined = array();
        for ($i = 0; $i < count($questions); $i++) {
            $question = trim($questions[$i]);
            $answer = isset($answers[$i]) ? trim($answers[$i]) : '';
            if ($question !== '' || $answer !== '') {
                $combined[] = array('question' => $question, 'answer' => $answer);
            }
        }
        if (!empty($combined)) {
            update_post_meta($post_id, '_property_faqs', wp_json_encode($combined));
        } else {
            delete_post_meta($post_id, '_property_faqs');
        }
    } else {
        delete_post_meta($post_id, '_property_faqs');
    }

    // Additional details (repeatable)
    if (isset($_POST['additional_label']) && is_array($_POST['additional_label'])) {
        $labels = array_map('sanitize_text_field', $_POST['additional_label']);
        $values = isset($_POST['additional_value']) && is_array($_POST['additional_value']) ? array_map('sanitize_text_field', $_POST['additional_value']) : array();
        $combined = array();
        for ($i = 0; $i < count($labels); $i++) {
            $label = trim($labels[$i]);
            $value = isset($values[$i]) ? trim($values[$i]) : '';
            if ($label !== '' || $value !== '') {
                $combined[] = array('label' => $label, 'value' => $value);
            }
        }
        if (!empty($combined)) {
            update_post_meta($post_id, '_property_additional_details', wp_json_encode($combined));
        } else {
            delete_post_meta($post_id, '_property_additional_details');
        }
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
    
    // If price already contains currency symbol and formatting, return as-is
    if (preg_match('/[\$€£₹Rs]/', $price)) {
        return $price;
    }
    
    // Clean price string: remove commas, spaces, currency symbols
    $clean_price = preg_replace('/[^0-9.]/', '', $price);
    
    // Convert to number
    $numeric_price = floatval($clean_price);
    
    if ($numeric_price <= 0) {
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
    
    return $symbol . number_format($numeric_price);
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

    // Submit lead form
    register_rest_route('property-plugin/v1', '/leads', array(
        'methods' => 'POST',
        'callback' => 'property_plugin_submit_lead',
        'permission_callback' => '__return_true',
    ));
}

/**
 * Get all properties from WordPress
 */
function property_plugin_get_properties($request) {
    // Ensure the custom post type is registered
    if (!post_type_exists('property')) {
        property_plugin_register_post_type();
        property_plugin_register_taxonomies();
    }

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
        try {
            // Get taxonomies
            $property_types = wp_get_post_terms($post->ID, 'property-type', array('fields' => 'names'));
            $locations = wp_get_post_terms($post->ID, 'property-location', array('fields' => 'names'));
            $bedrooms = wp_get_post_terms($post->ID, 'bedrooms', array('fields' => 'names'));
            $bathrooms = wp_get_post_terms($post->ID, 'bathrooms', array('fields' => 'names'));
            $floors = wp_get_post_terms($post->ID, 'property-floor', array('fields' => 'names'));
            
            // Get custom taxonomies
            $custom_taxonomies = get_option('property_plugin_custom_taxonomies', array());
            $custom_taxonomy_data = array();
            if (is_array($custom_taxonomies)) {
                foreach ($custom_taxonomies as $tax) {
                    if (isset($tax['slug']) && taxonomy_exists($tax['slug'])) {
                        $terms = wp_get_post_terms($post->ID, $tax['slug'], array('fields' => 'names'));
                        $custom_taxonomy_data[$tax['slug']] = !empty($terms) ? $terms[0] : 'N/A';
                    }
                }
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
            $garage = get_post_meta($post->ID, '_property_garage', true);
            
            // Get gallery images
            $gallery = array();
            $gallery_ids_raw = get_post_meta($post->ID, '_property_gallery', true);
            if (!empty($gallery_ids_raw)) {
                $gallery_ids = array_filter(explode(',', $gallery_ids_raw));
                foreach ($gallery_ids as $att_id) {
                    $att_id = intval(trim($att_id));
                    if ($att_id > 0) {
                        $url = wp_get_attachment_image_url($att_id, 'large');
                        if ($url) {
                            $gallery[] = $url;
                        }
                    }
                }
            }
            
            // If no gallery images from attachments, use fallback URLs
            if (empty($gallery)) {
                $gallery_urls_raw = get_post_meta($post->ID, '_property_gallery_urls', true);
                if (!empty($gallery_urls_raw)) {
                    $gallery_urls = json_decode($gallery_urls_raw, true);
                    if (is_array($gallery_urls)) {
                        $gallery = $gallery_urls;
                    }
                }
            }
            
            // Get additional details safely
            $additional_details_raw = get_post_meta($post->ID, '_property_additional_details', true);
            $additional_details = array();
            if (!empty($additional_details_raw)) {
                if (is_string($additional_details_raw)) {
                    $decoded = json_decode($additional_details_raw, true);
                    $additional_details = is_array($decoded) ? $decoded : array();
                } elseif (is_array($additional_details_raw)) {
                    $additional_details = $additional_details_raw;
                }
            }
            
            // Get FAQs safely
            $faqs_raw = get_post_meta($post->ID, '_property_faqs', true);
            $faqs = array();
            if (!empty($faqs_raw)) {
                if (is_string($faqs_raw)) {
                    $decoded = json_decode($faqs_raw, true);
                    $faqs = is_array($decoded) ? $decoded : array();
                } elseif (is_array($faqs_raw)) {
                    $faqs = $faqs_raw;
                }
            }
            
            $property_data = array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'content' => $post->post_content,
                'excerpt' => $post->post_excerpt,
                'date' => $post->post_date,
                'thumbnail' => get_the_post_thumbnail_url($post->ID, 'large') ?: (get_post_meta($post->ID, '_property_thumbnail_url', true) ?: ''),
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
                'garage' => $garage ?: '',
                'gallery' => $gallery,
                // Add fallback default images if none exist
                'thumbnail_url' => get_post_meta($post->ID, '_property_thumbnail_url', true) ?: '',
                'gallery_urls' => get_post_meta($post->ID, '_property_gallery_urls', true) ?: '',
                // Per-property agent meta (optional)
                'agent' => array(
                    'name'  => get_post_meta($post->ID, '_property_agent_name', true) ?: '',
                    'phone' => get_post_meta($post->ID, '_property_agent_phone', true) ?: '',
                    'email' => get_post_meta($post->ID, '_property_agent_email', true) ?: '',
                    'photo' => get_post_meta($post->ID, '_property_agent_photo', true) ?: '',
                ),
                // Repeatable additional details stored as JSON in post meta
                'additional_details' => $additional_details,
                // Repeatable FAQs stored as JSON in post meta
                'faqs' => $faqs,
            );
            
            // Add custom taxonomy data
            $property_data = array_merge($property_data, $custom_taxonomy_data);
            
            $properties[] = $property_data;
        } catch (Exception $e) {
            error_log('[Property Plugin] Error processing property ' . $post->ID . ': ' . $e->getMessage());
            continue;
        }
    }
    
    return rest_ensure_response($properties);
}

/**
 * Get single property
 */
function property_plugin_get_property($request) {
    // Ensure the custom post type is registered
    if (!post_type_exists('property')) {
        property_plugin_register_post_type();
        property_plugin_register_taxonomies();
    }

    $id = $request['id'];
    $post = get_post($id);
    
    if (!$post || $post->post_type !== 'property') {
        return new WP_Error('not_found', 'Property not found', array('status' => 404));
    }
    
    try {
        // Get taxonomies
        $property_types = wp_get_post_terms($post->ID, 'property-type', array('fields' => 'names'));
        $locations = wp_get_post_terms($post->ID, 'property-location', array('fields' => 'names'));
        $bedrooms = wp_get_post_terms($post->ID, 'bedrooms', array('fields' => 'names'));
        $bathrooms = wp_get_post_terms($post->ID, 'bathrooms', array('fields' => 'names'));
        $floors = wp_get_post_terms($post->ID, 'property-floor', array('fields' => 'names'));
        
        // Get custom taxonomies
        $custom_taxonomies = get_option('property_plugin_custom_taxonomies', array());
        $custom_taxonomy_data = array();
        if (is_array($custom_taxonomies)) {
            foreach ($custom_taxonomies as $tax) {
                if (isset($tax['slug']) && taxonomy_exists($tax['slug'])) {
                    $terms = wp_get_post_terms($post->ID, $tax['slug'], array('fields' => 'names'));
                    $custom_taxonomy_data[$tax['slug']] = !empty($terms) ? $terms[0] : 'N/A';
                }
            }
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
        $garage = get_post_meta($post->ID, '_property_garage', true);
        
        // Get gallery images
        $gallery = array();
        $gallery_ids_raw = get_post_meta($post->ID, '_property_gallery', true);
        if (!empty($gallery_ids_raw)) {
            $gallery_ids = array_filter(explode(',', $gallery_ids_raw));
            foreach ($gallery_ids as $att_id) {
                $att_id = intval(trim($att_id));
                if ($att_id > 0) {
                    $url = wp_get_attachment_image_url($att_id, 'large');
                    if ($url) {
                        $gallery[] = $url;
                    }
                }
            }
        }
        
        // If no gallery images from attachments, use fallback URLs
        if (empty($gallery)) {
            $gallery_urls_raw = get_post_meta($post->ID, '_property_gallery_urls', true);
            if (!empty($gallery_urls_raw)) {
                $gallery_urls = json_decode($gallery_urls_raw, true);
                if (is_array($gallery_urls)) {
                    $gallery = $gallery_urls;
                }
            }
        }
        
        // Get additional details safely
        $additional_details_raw = get_post_meta($post->ID, '_property_additional_details', true);
        $additional_details = array();
        if (!empty($additional_details_raw)) {
            if (is_string($additional_details_raw)) {
                $decoded = json_decode($additional_details_raw, true);
                $additional_details = is_array($decoded) ? $decoded : array();
            } elseif (is_array($additional_details_raw)) {
                $additional_details = $additional_details_raw;
            }
        }
        
        // Get FAQs safely
        $faqs_raw = get_post_meta($post->ID, '_property_faqs', true);
        $faqs = array();
        if (!empty($faqs_raw)) {
            if (is_string($faqs_raw)) {
                $decoded = json_decode($faqs_raw, true);
                $faqs = is_array($decoded) ? $decoded : array();
            } elseif (is_array($faqs_raw)) {
                $faqs = $faqs_raw;
            }
        }
        
        $property_data = array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'date' => $post->post_date,
            'thumbnail' => get_the_post_thumbnail_url($post->ID, 'large') ?: (get_post_meta($post->ID, '_property_thumbnail_url', true) ?: ''),
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
            'garage' => $garage ?: '',
            'gallery' => $gallery,
            // Per-property agent meta (optional)
            'agent' => array(
                'name'  => get_post_meta($post->ID, '_property_agent_name', true) ?: '',
                'phone' => get_post_meta($post->ID, '_property_agent_phone', true) ?: '',
                'email' => get_post_meta($post->ID, '_property_agent_email', true) ?: '',
                'photo' => get_post_meta($post->ID, '_property_agent_photo', true) ?: '',
            ),
            // Repeatable additional details stored as JSON in post meta
            'additional_details' => $additional_details,
            // Repeatable FAQs stored as JSON in post meta
            'faqs' => $faqs,
        );
        
        // Add custom taxonomy data
        $property_data = array_merge($property_data, $custom_taxonomy_data);
        
        return rest_ensure_response($property_data);
    } catch (Exception $e) {
        error_log('[Property Plugin] Error getting property ' . $id . ': ' . $e->getMessage());
        return new WP_Error('processing_error', 'Error processing property', array('status' => 500));
    }
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
                        // CTA Section
                        'ctaImage' => get_option('property_plugin_cta_image', ''),
                        'ctaTitle' => get_option('property_plugin_cta_title', 'Want to Sell or Rent Your Property?'),
                        'ctaDescription' => get_option('property_plugin_cta_description', 'List your property with us and reach thousands of potential buyers and renters.'),
                        'ctaButtonText' => get_option('property_plugin_cta_button_text', 'Add Property Now'),
                        'ctaButtonUrl' => get_option('property_plugin_cta_button_url', '/wp-admin/post-new.php?post_type=property'),
                        'ctaBgColor' => get_option('property_plugin_cta_bg_color', '#f0f9ff'),
                        'ctaTextColor' => get_option('property_plugin_cta_text_color', '#1e3a5f'),
                        // Features Section
                        'featuresBgColor' => get_option('property_plugin_features_bg_color', '#ffffff'),
                        'featuresTextColor' => get_option('property_plugin_features_text_color', '#1f2937'),
                        'features' => array(
                            array(
                                'icon' => get_option('property_plugin_feature_1_icon', 'fas fa-trophy'),
                                'title' => get_option('property_plugin_feature_1_title', 'Trusted by Thousands'),
                                'description' => get_option('property_plugin_feature_1_description', 'Join thousands of happy clients who found their perfect property.'),
                            ),
                            array(
                                'icon' => get_option('property_plugin_feature_2_icon', 'fas fa-chart-bar'),
                                'title' => get_option('property_plugin_feature_2_title', 'Wide Range of Properties'),
                                'description' => get_option('property_plugin_feature_2_description', 'Explore a wide range of properties for sale and rent.'),
                            ),
                            array(
                                'icon' => get_option('property_plugin_feature_3_icon', 'fas fa-users'),
                                'title' => get_option('property_plugin_feature_3_title', 'Expert Agents'),
                                'description' => get_option('property_plugin_feature_3_description', 'Work with experienced agents to find the best property.'),
                            ),
                            array(
                                'icon' => get_option('property_plugin_feature_4_icon', 'fas fa-shield-alt'),
                                'title' => get_option('property_plugin_feature_4_title', 'Secure & Easy Process'),
                                'description' => get_option('property_plugin_feature_4_description', 'Enjoy a secure and hassle-free property buying or renting process.'),
                            ),
                        ),
                        // Single Property Page
                        'agentName' => get_option('property_plugin_agent_name', 'John Smith'),
                        'agentPhoto' => get_option('property_plugin_agent_photo', ''),
                        'agentRole' => get_option('property_plugin_agent_role', 'Property Agent'),
                        'agentPhone' => get_option('property_plugin_agent_phone', '+1 (555) 123-4567'),
                        'agentEmail' => get_option('property_plugin_agent_email', ''),
                        'contactFormHeading' => get_option('property_plugin_contact_form_heading', 'Get More Details'),
                        'contactFormSubtitle' => get_option('property_plugin_contact_form_subtitle', 'Schedule a tour or request more information about this property.'),
                        'featuredLabel' => get_option('property_plugin_featured_label', 'FEATURED PROPERTY'),
                        'scheduleTourUrl' => get_option('property_plugin_schedule_tour_url', ''),
                                'currentUserId' => get_current_user_id(),
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
 * Create a demo property on first activation so users see how the plugin looks
 */
function property_plugin_create_demo_property() {
    // Only run once — skip if demo already created
    if (get_option('property_plugin_demo_created')) {
        return;
    }

    // Insert the property post
    $post_id = wp_insert_post(array(
        'post_type'    => 'property',
        'post_title'   => 'Modern Luxury Villa — Beverly Hills',
        'post_content' => "This stunning modern villa offers the perfect blend of luxury, comfort, and contemporary design. Located in one of the most prestigious neighborhoods in Beverly Hills, this property features high-end finishes, spacious living areas, and breathtaking panoramic views.\n\nThe open-concept kitchen boasts premium stainless-steel appliances, a large island, and custom cabinetry. The master suite includes a walk-in closet, spa-like bathroom, and a private balcony overlooking the garden and pool.\n\nAdditional highlights include smart home automation, a home theatre, landscaped gardens, a swimming pool, and a 2-car garage.",
        'post_excerpt' => 'A stunning modern villa in Beverly Hills with pool, smart home features, and panoramic views.',
        'post_status'  => 'publish',
    ), true);

    if (is_wp_error($post_id)) {
        error_log('[Property Plugin Demo] Failed to create demo property: ' . $post_id->get_error_message());
        return;
    }

    // --- Taxonomy terms ---
    wp_set_object_terms($post_id, array('Villa'),      'property-type');
    wp_set_object_terms($post_id, array('Beverly Hills'), 'property-location');
    wp_set_object_terms($post_id, array('4'),           'bedrooms');
    wp_set_object_terms($post_id, array('3'),           'bathrooms');
    wp_set_object_terms($post_id, array('2'),           'property-floor');

    // --- Meta fields ---
    update_post_meta($post_id, '_property_price',    '850000');
    update_post_meta($post_id, '_property_area',     '2500');
    update_post_meta($post_id, '_property_address',  '1234 Sunset Boulevard, Beverly Hills, CA');
    update_post_meta($post_id, '_property_city',     'Beverly Hills');
    update_post_meta($post_id, '_property_state',    'California');
    update_post_meta($post_id, '_property_zipcode',  '90210');
    update_post_meta($post_id, '_property_country',  'United States');
    update_post_meta($post_id, '_property_status',   'for-sale');
    update_post_meta($post_id, '_property_garage',   '2');

    // --- Featured image (sideload from Unsplash) ---
    $image_url = 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1200&q=80';

    // WordPress media functions may not be loaded during activation
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $attachment_id = media_sideload_image($image_url, $post_id, 'Modern Luxury Villa — Beverly Hills', 'id');

    if (!is_wp_error($attachment_id)) {
        set_post_thumbnail($post_id, $attachment_id);

        // Build gallery: 3 extra images
        $gallery_urls = array(
            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1200&q=80',
            'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1200&q=80',
            'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=1200&q=80',
        );
        $gallery_ids = array();
        foreach ($gallery_urls as $url) {
            $att_id = media_sideload_image($url, $post_id, 'Gallery image', 'id');
            if (!is_wp_error($att_id)) {
                $gallery_ids[] = $att_id;
            }
        }
        if (!empty($gallery_ids)) {
            update_post_meta($post_id, '_property_gallery', implode(',', $gallery_ids));
        }
    } else {
        error_log('[Property Plugin Demo] Image sideload failed: ' . $attachment_id->get_error_message());
    }

    // Mark demo as created so it never runs again
    update_option('property_plugin_demo_created', $post_id);
    error_log('[Property Plugin Demo] Demo property created — Post ID: ' . $post_id);
}


/**
 * Install default data (properties + settings) from JSON files.
 * Runs once and sideloads images, creates properties and updates plugin settings.
 */
function property_plugin_install_default_data($force = false) {
    // Only run once unless forced
    if (!$force && get_option('property_plugin_default_data_installed')) {
        return;
    }

    $data_dir = PROPERTY_PLUGIN_PATH . 'data/';
    $props_file = $data_dir . 'default-properties.json';
    $settings_file = $data_dir . 'default-settings.json';

    if (!file_exists($props_file)) {
        error_log('[Property Plugin] default properties JSON not found: ' . $props_file);
        return;
    }

    // Load JSON files
    $props_json = file_get_contents($props_file);
    $props = json_decode($props_json, true);
    if (!is_array($props)) {
        error_log('[Property Plugin] Invalid default properties JSON');
        return;
    }

    // Ensure media functions available
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    error_log('[Property Plugin] Starting default data import with ' . count($props) . ' properties');

    foreach ($props as $idx => $p) {
        // Skip if a property with the same title exists
        $existing = get_page_by_title($p['title'] ?? '', OBJECT, 'property');
        if ($existing) {
            error_log('[Property Plugin] Skipping property ' . ($idx + 1) . ' - already exists: ' . ($p['title'] ?? ''));
            continue;
        }

        error_log('[Property Plugin] Importing property ' . ($idx + 1) . '/' . count($props) . ': ' . ($p['title'] ?? ''));

        $post_arr = array(
            'post_type'    => 'property',
            'post_title'   => sanitize_text_field($p['title'] ?? 'Demo Property'),
            'post_content' => wp_kses_post($p['content'] ?? ''),
            'post_excerpt' => sanitize_text_field($p['excerpt'] ?? ''),
            'post_status'  => 'publish',
        );

        $post_id = wp_insert_post($post_arr, true);
        if (is_wp_error($post_id)) {
            error_log('[Property Plugin] Failed to create demo property: ' . $post_id->get_error_message());
            continue;
        }

        error_log('[Property Plugin] Created post ID: ' . $post_id . ' for ' . ($p['title'] ?? ''));

        // Taxonomies
        if (!empty($p['property_type'])) wp_set_object_terms($post_id, array(sanitize_text_field($p['property_type'])), 'property-type');
        if (!empty($p['location'])) wp_set_object_terms($post_id, array(sanitize_text_field($p['location'])), 'property-location');
        if (!empty($p['bedrooms'])) wp_set_object_terms($post_id, array(sanitize_text_field($p['bedrooms'])), 'bedrooms');
        if (!empty($p['bathrooms'])) wp_set_object_terms($post_id, array(sanitize_text_field($p['bathrooms'])), 'bathrooms');
        if (!empty($p['floor'])) wp_set_object_terms($post_id, array(sanitize_text_field($p['floor'])), 'property-floor');

        // Meta fields
        if (isset($p['price'])) update_post_meta($post_id, '_property_price', sanitize_text_field($p['price']));
        if (isset($p['area'])) update_post_meta($post_id, '_property_area', sanitize_text_field($p['area']));
        if (isset($p['address'])) update_post_meta($post_id, '_property_address', sanitize_text_field($p['address']));
        if (isset($p['city'])) update_post_meta($post_id, '_property_city', sanitize_text_field($p['city']));
        if (isset($p['state'])) update_post_meta($post_id, '_property_state', sanitize_text_field($p['state']));
        if (isset($p['zipcode'])) update_post_meta($post_id, '_property_zipcode', sanitize_text_field($p['zipcode']));
        if (isset($p['country'])) update_post_meta($post_id, '_property_country', sanitize_text_field($p['country']));
        if (isset($p['status'])) update_post_meta($post_id, '_property_status', sanitize_text_field($p['status']));
        if (isset($p['garage'])) update_post_meta($post_id, '_property_garage', sanitize_text_field($p['garage']));

        // Featured image sideload - save original URL as fallback
        if (!empty($p['thumbnail_url'])) {
            // Always save the original URL as fallback
            update_post_meta($post_id, '_property_thumbnail_url', esc_url_raw($p['thumbnail_url']));
            
            error_log('[Property Plugin] Sideload thumbnail for post ' . $post_id . ': ' . $p['thumbnail_url']);
            $att_id = media_sideload_image($p['thumbnail_url'], $post_id, $p['title'] ?? 'Featured image', 'id');
            if (!is_wp_error($att_id)) {
                set_post_thumbnail($post_id, $att_id);
                error_log('[Property Plugin] Thumbnail sideloaded successfully, attachment ID: ' . $att_id);
            } else {
                error_log('[Property Plugin] Failed to sideload thumbnail: ' . $att_id->get_error_message());
                error_log('[Property Plugin] Using fallback URL: ' . $p['thumbnail_url']);
            }
        }

        // Gallery - save original URLs as fallback
        $gallery_ids = array();
        if (!empty($p['gallery_urls']) && is_array($p['gallery_urls'])) {
            // Always save original URLs as fallback
            update_post_meta($post_id, '_property_gallery_urls', json_encode($p['gallery_urls']));
            
            error_log('[Property Plugin] Sideload ' . count($p['gallery_urls']) . ' gallery images for post ' . $post_id);
            foreach ($p['gallery_urls'] as $gidx => $gurl) {
                error_log('[Property Plugin] Gallery image ' . ($gidx + 1) . ': ' . $gurl);
                $gatt = media_sideload_image($gurl, $post_id, 'Gallery image', 'id');
                if (!is_wp_error($gatt)) {
                    $gallery_ids[] = $gatt;
                    error_log('[Property Plugin] Gallery image ' . ($gidx + 1) . ' sideloaded, ID: ' . $gatt);
                } else {
                    error_log('[Property Plugin] Failed to sideload gallery image: ' . $gatt->get_error_message());
                }
            }
            if (!empty($gallery_ids)) {
                update_post_meta($post_id, '_property_gallery', implode(',', $gallery_ids));
                error_log('[Property Plugin] Gallery saved with ' . count($gallery_ids) . ' images');
            }
        }

        // Agent meta
        if (!empty($p['agent']) && is_array($p['agent'])) {
            $agent = $p['agent'];
            if (isset($agent['name'])) update_post_meta($post_id, '_property_agent_name', sanitize_text_field($agent['name']));
            if (isset($agent['phone'])) update_post_meta($post_id, '_property_agent_phone', sanitize_text_field($agent['phone']));
            if (isset($agent['email'])) update_post_meta($post_id, '_property_agent_email', sanitize_email($agent['email']));
            if (!empty($agent['photo'])) {
                $aatt = media_sideload_image($agent['photo'], $post_id, sanitize_text_field($agent['name'] ?? 'agent'), 'id');
                if (!is_wp_error($aatt)) {
                    update_post_meta($post_id, '_property_agent_photo', wp_get_attachment_image_url($aatt, 'thumbnail'));
                }
            }
        }

        // Additional details
        if (!empty($p['additional_details'])) {
            update_post_meta($post_id, '_property_additional_details', wp_json_encode($p['additional_details']));
        }

        // FAQs
        if (!empty($p['faqs']) && is_array($p['faqs'])) {
            update_post_meta($post_id, '_property_faqs', wp_json_encode($p['faqs']));
        }

        error_log('[Property Plugin] Imported demo property: ' . $post_id . ' - ' . ($p['title'] ?? ''));
    }

    // Install settings
    if (file_exists($settings_file)) {
        $sjson = file_get_contents($settings_file);
        $sdata = json_decode($sjson, true);
        if (is_array($sdata)) {
            // Optionally sideload bannerImage and ctaImage and agentPhoto to store attachment URLs
            if (!empty($sdata['bannerImage'])) {
                $batt = media_sideload_image($sdata['bannerImage'], 0, 'Banner image', 'id');
                if (!is_wp_error($batt)) {
                    $banner_url = wp_get_attachment_image_url($batt, 'large');
                    update_option('property_plugin_banner_image', $banner_url);
                } else {
                    update_option('property_plugin_banner_image', esc_url_raw($sdata['bannerImage']));
                }
            }

            if (!empty($sdata['ctaImage'])) {
                $catt = media_sideload_image($sdata['ctaImage'], 0, 'CTA image', 'id');
                if (!is_wp_error($catt)) {
                    $cta_url = wp_get_attachment_image_url($catt, 'large');
                    update_option('property_plugin_cta_image', $cta_url);
                } else {
                    update_option('property_plugin_cta_image', esc_url_raw($sdata['ctaImage']));
                }
            }

            if (!empty($sdata['agentPhoto'])) {
                $aatt = media_sideload_image($sdata['agentPhoto'], 0, 'Agent photo', 'id');
                if (!is_wp_error($aatt)) {
                    $agent_photo_url = wp_get_attachment_image_url($aatt, 'thumbnail');
                    update_option('property_plugin_agent_photo', $agent_photo_url);
                } else {
                    update_option('property_plugin_agent_photo', esc_url_raw($sdata['agentPhoto']));
                }
            }

            // Save remaining simple settings
            $simple_keys = array(
                'headerText' => 'property_plugin_header_text',
                'bannerSubtitle' => 'property_plugin_banner_subtitle',
                'bannerHeight' => 'property_plugin_banner_height',
                'bannerOverlay' => 'property_plugin_banner_overlay',
                'bannerOverlayColor' => 'property_plugin_banner_overlay_color',
                'primaryColor' => 'property_plugin_primary_color',
                'secondaryColor' => 'property_plugin_secondary_color',
                'textColor' => 'property_plugin_text_color',
                'backgroundColor' => 'property_plugin_background_color',
                'cardBackground' => 'property_plugin_card_background',
                'fontFamily' => 'property_plugin_font_family',
                'fontSize' => 'property_plugin_font_size',
                'propertiesPerPage' => 'property_plugin_properties_per_page',
                'cardLayout' => 'property_plugin_card_layout',
                'showBadge' => 'property_plugin_show_badge',
                'showArea' => 'property_plugin_show_area',
                'showAddress' => 'property_plugin_show_address',
                'sidebarPosition' => 'property_plugin_sidebar_position',
                'sidebarWidth' => 'property_plugin_sidebar_width',
                'enableFilters' => 'property_plugin_enable_filters',
                'enableCompare' => 'property_plugin_enable_compare',
                'enableLeadForm' => 'property_plugin_enable_lead_form',
                'leadFormTitle' => 'property_plugin_lead_form_title',
                'contactEmail' => 'property_plugin_contact_email',
                'contactPhone' => 'property_plugin_contact_phone',
                'customCSS' => 'property_plugin_custom_css',
                'googleAnalytics' => 'property_plugin_google_analytics',
                'ctaTitle' => 'property_plugin_cta_title',
                'ctaDescription' => 'property_plugin_cta_description',
                'ctaButtonText' => 'property_plugin_cta_button_text',
                'ctaButtonUrl' => 'property_plugin_cta_button_url',
                'ctaBgColor' => 'property_plugin_cta_bg_color',
                'ctaTextColor' => 'property_plugin_cta_text_color',
                'featuresBgColor' => 'property_plugin_features_bg_color',
                'featuresTextColor' => 'property_plugin_features_text_color',
                'agentName' => 'property_plugin_agent_name',
                'agentRole' => 'property_plugin_agent_role',
                'agentPhone' => 'property_plugin_agent_phone',
                'agentEmail' => 'property_plugin_agent_email',
                'contactFormHeading' => 'property_plugin_contact_form_heading',
                'contactFormSubtitle' => 'property_plugin_contact_form_subtitle',
                'featuredLabel' => 'property_plugin_featured_label',
                'scheduleTourUrl' => 'property_plugin_schedule_tour_url',
            );

            foreach ($simple_keys as $src => $opt_name) {
                if (isset($sdata[$src])) update_option($opt_name, $sdata[$src]);
            }
        }
    }

    // Mark installed
    update_option('property_plugin_default_data_installed', 1);
    error_log('[Property Plugin] Default data import complete');
}

// Hook import on admin_init to ensure default data is installed (duplicate check is safe)
add_action('admin_init', function() {
    if (!get_option('property_plugin_default_data_installed')) {
        property_plugin_install_default_data();
    }
});

/**
 * Activation hook
 */
function property_plugin_activate() {
    // Register post type and taxonomies before flushing
    property_plugin_register_post_type();
    property_plugin_register_taxonomies();

    // Create leads database table
    property_plugin_create_leads_table();

    // ALWAYS reset import flags on activation to force fresh import
    // This ensures re-installing the plugin will re-import all sample data
    delete_option('property_plugin_default_data_installed');
    delete_option('property_plugin_demo_created');
    error_log('[Property Plugin] Activation: Reset import flags for fresh data import');

    // Install default sample data (10 properties + all settings)
    property_plugin_install_default_data();

    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Set a transient to show the activation notice on next admin page load
    set_transient('property_plugin_show_activation_notice', true, 60);
}
register_activation_hook(__FILE__, 'property_plugin_activate');

// Also ensure leads table exists on every load (safe for already-activated sites)
add_action('plugins_loaded', 'property_plugin_create_leads_table');

// Install default data on admin_init if not installed (for existing installs)
add_action('admin_init', function() {
    if (!get_option('property_plugin_default_data_installed')) {
        property_plugin_install_default_data();
    }
});

/**
 * Deactivation hook
 */
function property_plugin_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'property_plugin_deactivate');

/**
 * Display admin notice after plugin activation
 */
function property_plugin_admin_notice() {
    if (!get_transient('property_plugin_show_activation_notice')) {
        return;
    }
    
    // Don't show if user dismissed it
    if (get_user_meta(get_current_user_id(), 'property_plugin_notice_dismissed', true)) {
        delete_transient('property_plugin_show_activation_notice');
        return;
    }
    
    $guide_url = admin_url('admin.php?page=property-plugin-guide');
    $settings_url = admin_url('admin.php?page=property-plugin-settings');
    ?>
    <style>
        .property-plugin-activation-notice .button .dashicons {
            line-height: 1;
            vertical-align: middle;
            margin-top: -1px;
        }
    </style>
    <div class="notice notice-success is-dismissible property-plugin-activation-notice" id="property-plugin-notice">
        <div style="display:flex; align-items:flex-start; gap:15px; padding:8px 0;">
            <div style="flex-shrink:0;">
                <span class="dashicons dashicons-building" style="font-size:40px; color:#2271b1; width:40px; height:40px;"></span>
            </div>
            <div style="flex:1;">
                <h2 style="margin:0 0 8px; color:#1d2327;">Property Plugin is Ready! 🏠</h2>
                <p style="margin:0 0 12px; font-size:14px; color:#3c434a;">
                    Your property listing plugin has been activated with <strong>10 sample properties</strong> and <strong>pre-configured settings</strong>. Here's how to get started:
                </p>
                <div style="background:#f0f6fc; border-left:4px solid #2271b1; padding:12px 15px; margin-bottom:12px;">
                    <p style="margin:0 0 8px; font-weight:600; font-size:14px;">✓ Sample Data Installed</p>
                    <p style="margin:0; font-size:13px;">
                        10 demo properties with images, details, FAQs, and agent information have been added. All settings (banner, colors, text, CTA) are pre-filled and ready to use.
                    </p>
                </div>
                <div style="background:#f0f6fc; border-left:4px solid #2271b1; padding:12px 15px; margin-bottom:12px;">
                    <p style="margin:0 0 8px; font-weight:600; font-size:14px;">Step 1: Add the shortcode to any page</p>
                    <p style="margin:0; font-size:13px;">
                        Copy <code style="background:#fff; padding:3px 8px; border:1px solid #c3c4c7; font-size:13px;">[property_plugin]</code> and paste it into any WordPress page to display your property listings with banner, search, filters, and cards.
                    </p>
                </div>
                <div style="background:#f0f6fc; border-left:4px solid #2271b1; padding:12px 15px; margin-bottom:12px;">
                    <p style="margin:0 0 8px; font-weight:600; font-size:14px;">Step 2: Customize your settings (Optional)</p>
                    <p style="margin:0; font-size:13px;">
                        Go to <strong>Property Plugin Settings</strong> to change the banner image, colors, header text, card layout, and more. Everything is already set up with professional defaults!
                    </p>
                </div>
                <p style="margin:0;">
                    <a href="<?php echo esc_url($guide_url); ?>" class="button button-primary" style="margin-right:8px;">
                        <span class="dashicons dashicons-book" style="vertical-align:middle; margin-right:4px;"></span>View Shortcode Guide
                    </a>
                    <a href="<?php echo esc_url($settings_url); ?>" class="button button-secondary">
                        <span class="dashicons dashicons-admin-generic" style="vertical-align:middle; margin-right:4px;"></span>Open Settings
                    </a>
                    <button type="button" class="button-link property-plugin-dismiss-notice" style="margin-left:15px; color:#787c82; text-decoration:underline;">
                        Don't show this again
                    </button>
                </p>
            </div>
        </div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        // Dismiss button in the notice header (X button)
        $(document).on('click', '#property-plugin-notice .notice-dismiss', function() {
            $.post(ajaxurl, {
                action: 'property_plugin_dismiss_notice',
                nonce: '<?php echo wp_create_nonce('property_plugin_dismiss_notice'); ?>'
            });
        });
        // "Don't show this again" link
        $(document).on('click', '.property-plugin-dismiss-notice', function(e) {
            e.preventDefault();
            $('#property-plugin-notice').fadeOut();
            $.post(ajaxurl, {
                action: 'property_plugin_dismiss_notice',
                nonce: '<?php echo wp_create_nonce('property_plugin_dismiss_notice'); ?>'
            });
        });
    });
    </script>
    <?php
}
add_action('admin_notices', 'property_plugin_admin_notice');

/**
 * AJAX handler to dismiss the activation notice permanently
 */
function property_plugin_dismiss_notice_ajax() {
    check_ajax_referer('property_plugin_dismiss_notice', 'nonce');
    update_user_meta(get_current_user_id(), 'property_plugin_notice_dismissed', true);
    delete_transient('property_plugin_show_activation_notice');
    wp_send_json_success();
}
add_action('wp_ajax_property_plugin_dismiss_notice', 'property_plugin_dismiss_notice_ajax');

/**
 * Create leads database table on plugin activation
 */
function property_plugin_create_leads_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'property_leads';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        property_id bigint(20) DEFAULT NULL,
        property_title varchar(255) DEFAULT '',
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        phone varchar(50) DEFAULT '',
        message text DEFAULT '',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY email (email),
        KEY property_id (property_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Handle lead form submission via REST API
 */
function property_plugin_submit_lead($request) {
    $params = $request->get_json_params();

    // Validate required fields
    $name    = sanitize_text_field($params['name']    ?? '');
    $email   = sanitize_email($params['email']        ?? '');
    $phone   = sanitize_text_field($params['phone']   ?? '');
    $message = sanitize_textarea_field($params['message'] ?? '');
    $property_id    = intval($params['propertyId']    ?? 0);
    $property_title = sanitize_text_field($params['propertyTitle'] ?? '');

    if (empty($name) || empty($email)) {
        error_log('[Property Plugin Lead] ERROR: Missing required fields - name or email');
        return new WP_Error('missing_fields', 'Name and email are required.', array('status' => 400));
    }

    if (!is_email($email)) {
        error_log('[Property Plugin Lead] ERROR: Invalid email address: ' . $email);
        return new WP_Error('invalid_email', 'Please provide a valid email address.', array('status' => 400));
    }

    // Save lead to database
    global $wpdb;
    $table_name = $wpdb->prefix . 'property_leads';

    $inserted = $wpdb->insert(
        $table_name,
        array(
            'property_id'    => $property_id,
            'property_title' => $property_title,
            'name'           => $name,
            'email'          => $email,
            'phone'          => $phone,
            'message'        => $message,
            'created_at'     => current_time('mysql'),
        ),
        array('%d', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    if ($inserted === false) {
        error_log('[Property Plugin Lead] ERROR: Database insert failed. DB error: ' . $wpdb->last_error);
        return new WP_Error('db_error', 'Could not save lead. Please try again.', array('status' => 500));
    }

    $lead_id = $wpdb->insert_id;
    error_log('[Property Plugin Lead] SUCCESS: Lead #' . $lead_id . ' saved for ' . $email);

    // --- Send notification email via wp_mail() (routed through WP Mail SMTP / Mailtrap) ---
    $to = get_option('property_plugin_contact_email', get_option('admin_email'));

    $subject = sprintf('[New Lead] %s — %s', $name, $property_title ?: 'Property Inquiry');

    $body  = "A new lead has been submitted on your property website.\n\n";
    $body .= "--- Lead Details ---\n";
    $body .= "Name:     $name\n";
    $body .= "Email:    $email\n";
    $body .= "Phone:    $phone\n";
    $body .= "Message:  $message\n\n";
    $body .= "--- Property ---\n";
    $body .= "Property ID:    $property_id\n";
    $body .= "Property Title: $property_title\n\n";
    $body .= "--- Meta ---\n";
    $body .= "Submitted at: " . current_time('mysql') . "\n";
    $body .= "Lead ID:      $lead_id\n";

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );

    $mail_sent = wp_mail($to, $subject, $body, $headers);

    if ($mail_sent) {
        error_log('[Property Plugin Lead] Email sent successfully to ' . $to . ' (Lead #' . $lead_id . ')');
    } else {
        error_log('[Property Plugin Lead] WARNING: wp_mail() returned false for Lead #' . $lead_id . '. Check WP Mail SMTP settings.');
    }

    return rest_ensure_response(array(
        'success' => true,
        'leadId'  => $lead_id,
        'emailSent' => $mail_sent,
        'message' => 'Your enquiry has been submitted successfully. We will contact you shortly.',
    ));
}

/**
 * AJAX: Return thumbnail URLs for gallery preview in admin meta box
 */
function property_plugin_get_gallery_thumbs_ajax() {
    check_ajax_referer('pp_gallery_nonce', 'nonce');

    $ids = isset($_POST['ids']) ? array_filter(array_map('absint', explode(',', $_POST['ids']))) : array();
    $result = array();

    foreach ($ids as $id) {
        $thumb = wp_get_attachment_image_url($id, 'thumbnail');
        if ($thumb) {
            $result[] = array('id' => $id, 'thumb' => $thumb);
        }
    }

    wp_send_json_success($result);
}
add_action('wp_ajax_pp_get_gallery_thumbs', 'property_plugin_get_gallery_thumbs_ajax');
