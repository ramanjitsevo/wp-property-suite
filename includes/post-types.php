<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Property custom post type.
 */
function wps_register_post_type() {
    $labels = array(
        'name'                  => _x('Properties', 'Post Type General Name', 'wps'),
        'singular_name'         => _x('Property', 'Post Type Singular Name', 'wps'),
        'menu_name'             => __('Properties', 'wps'),
        'name_admin_bar'        => __('Property', 'wps'),
        'archives'              => __('Property Archives', 'wps'),
        'attributes'            => __('Property Attributes', 'wps'),
        'parent_item_colon'     => __('Parent Property:', 'wps'),
        'all_items'             => __('All Properties', 'wps'),
        'add_new_item'          => __('Add New Property', 'wps'),
        'add_new'               => __('Add New', 'wps'),
        'new_item'              => __('New Property', 'wps'),
        'edit_item'             => __('Edit Property', 'wps'),
        'update_item'           => __('Update Property', 'wps'),
        'view_item'             => __('View Property', 'wps'),
        'view_items'            => __('View Properties', 'wps'),
        'search_items'          => __('Search Properties', 'wps'),
        'not_found'             => __('Not found', 'wps'),
        'not_found_in_trash'    => __('Not found in Trash', 'wps'),
        'featured_image'        => __('Featured Image', 'wps'),
        'set_featured_image'    => __('Set featured image', 'wps'),
        'remove_featured_image' => __('Remove featured image', 'wps'),
        'use_featured_image'    => __('Use as featured image', 'wps'),
        'insert_into_item'      => __('Insert into property', 'wps'),
        'uploaded_to_this_item' => __('Uploaded to this property', 'wps'),
        'items_list'            => __('Properties list', 'wps'),
        'items_list_navigation' => __('Properties list navigation', 'wps'),
        'filter_items_list'     => __('Filter properties list', 'wps'),
    );

    register_post_type('property', array(
        'label'               => __('Property', 'wps'),
        'description'         => __('Property listings for real estate', 'wps'),
        'labels'              => $labels,
        'supports'            => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
        'taxonomies'          => array('property-type', 'property-location', 'bedrooms', 'bathrooms', 'property-floor'),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-building',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'show_in_rest'        => true,
        'rest_base'           => 'properties',
        'capability_type'     => 'post',
    ));
}

/**
 * Register built-in property taxonomies.
 */
function wps_register_taxonomies() {
    wps_register_property_taxonomy('property-type', __('Property Types', 'wps'), __('Property Type', 'wps'), 'property-type');
    wps_register_property_taxonomy('property-location', __('Locations', 'wps'), __('Location', 'wps'), 'location');
    wps_register_property_taxonomy('bedrooms', __('Bedrooms', 'wps'), __('Bedroom', 'wps'), 'bedrooms');
    wps_register_property_taxonomy('bathrooms', __('Bathrooms', 'wps'), __('Bathroom', 'wps'), 'bathrooms');
    wps_register_property_taxonomy('property-floor', __('Floors', 'wps'), __('Floor', 'wps'), 'floor');
}

/**
 * Register a hierarchical property taxonomy with common labels.
 */
function wps_register_property_taxonomy($slug, $plural, $singular, $rewrite_slug) {
    register_taxonomy($slug, 'property', array(
        'labels' => array(
            'name'              => $plural,
            'singular_name'     => $singular,
            'search_items'      => sprintf(__('Search %s', 'wps'), $plural),
            'all_items'         => sprintf(__('All %s', 'wps'), $plural),
            'edit_item'         => sprintf(__('Edit %s', 'wps'), $singular),
            'update_item'       => sprintf(__('Update %s', 'wps'), $singular),
            'add_new_item'      => sprintf(__('Add New %s', 'wps'), $singular),
            'new_item_name'     => sprintf(__('New %s Name', 'wps'), $singular),
            'menu_name'         => $plural,
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => $rewrite_slug),
        'show_in_rest'      => true,
    ));
}
