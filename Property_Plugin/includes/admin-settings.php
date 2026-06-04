<?php
/**
 * Property Plugin - Admin Settings Page
 * Provides a user-friendly interface to customize all plugin settings
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add admin menu
 */
function property_plugin_add_admin_menu() {
    add_menu_page(
        __('Property Plugin Settings', 'property-plugin'),
        __('Property Plugin Settings', 'property-plugin'),
        'manage_options',
        'property-plugin-settings',
        'property_plugin_settings_page',
        'dashicons-building',
        6
    );
}
add_action('admin_menu', 'property_plugin_add_admin_menu');

/**
 * Register settings
 */
function property_plugin_register_settings() {
    // General Settings
    register_setting('property_plugin_general', 'property_plugin_header_text', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('property_plugin_general', 'property_plugin_properties_per_page', array('sanitize_callback' => 'absint'));
    register_setting('property_plugin_general', 'property_plugin_default_currency', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('property_plugin_general', 'property_plugin_enable_compare', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('property_plugin_general', 'property_plugin_enable_preloader', array('sanitize_callback' => 'sanitize_text_field'));
    
    // Banner Settings
    register_setting('property_plugin_banner', 'property_plugin_banner_image', array('sanitize_callback' => 'esc_url_raw'));
    register_setting('property_plugin_banner', 'property_plugin_banner_subtitle', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('property_plugin_banner', 'property_plugin_banner_height', array('sanitize_callback' => 'absint'));
    register_setting('property_plugin_banner', 'property_plugin_banner_overlay', array('sanitize_callback' => 'absint'));
    register_setting('property_plugin_banner', 'property_plugin_banner_overlay_color', array('sanitize_callback' => 'sanitize_hex_color'));
    
    // Colors & Typography
    register_setting('property_plugin_colors', 'property_plugin_primary_color', array('sanitize_callback' => 'sanitize_hex_color'));
    register_setting('property_plugin_colors', 'property_plugin_secondary_color', array('sanitize_callback' => 'sanitize_hex_color'));
    register_setting('property_plugin_colors', 'property_plugin_text_color', array('sanitize_callback' => 'sanitize_hex_color'));
    register_setting('property_plugin_colors', 'property_plugin_background_color', array('sanitize_callback' => 'sanitize_hex_color'));
    register_setting('property_plugin_colors', 'property_plugin_card_background', array('sanitize_callback' => 'sanitize_hex_color'));
    register_setting('property_plugin_colors', 'property_plugin_font_family', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('property_plugin_colors', 'property_plugin_font_size', array('sanitize_callback' => 'absint'));
    
    // Card Settings
    register_setting('property_plugin_card', 'property_plugin_card_layout', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('property_plugin_card', 'property_plugin_show_badge', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('property_plugin_card', 'property_plugin_show_area', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('property_plugin_card', 'property_plugin_show_address', array('sanitize_callback' => 'sanitize_text_field'));
    
    // Contact & Lead Form
    register_setting('property_plugin_contact', 'property_plugin_contact_email', array('sanitize_callback' => 'sanitize_email'));
    register_setting('property_plugin_contact', 'property_plugin_contact_phone', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('property_plugin_contact', 'property_plugin_enable_lead_form', array('sanitize_callback' => 'sanitize_text_field'));
    register_setting('property_plugin_contact', 'property_plugin_lead_form_title', array('sanitize_callback' => 'sanitize_text_field'));
    
    // API Keys
    register_setting('property_plugin_api', 'property_plugin_google_api_key', array('sanitize_callback' => 'sanitize_text_field'));
    
    // Advanced Settings
    register_setting('property_plugin_advanced', 'property_plugin_custom_css', array('sanitize_callback' => 'wp_strip_all_tags'));
    register_setting('property_plugin_advanced', 'property_plugin_google_analytics', array('sanitize_callback' => 'sanitize_text_field'));
}
add_action('admin_init', 'property_plugin_register_settings');

/**
 * Settings page HTML
 */
function property_plugin_settings_page() {
    ?>
    <div class="wrap property-plugin-settings">
        <div class="property-plugin-header">
            <h1><?php _e('Property Plugin Settings', 'property-plugin'); ?></h1>
            <p class="description"><?php _e('Manage all plugin settings from one place. Changes will appear on the frontend after refreshing the page.', 'property-plugin'); ?></p>
            <button type="button" class="button button-primary" id="save-all-settings"><?php _e('Save All Changes', 'property-plugin'); ?></button>
        </div>

        <div class="property-plugin-settings-container">
            <!-- Navigation Tabs -->
            <div class="property-plugin-tabs">
                <button class="tab-button active" data-tab="general">
                    <span class="dashicons dashicons-admin-generic"></span>
                    <?php _e('General', 'property-plugin'); ?>
                </button>
                <button class="tab-button" data-tab="banner">
                    <span class="dashicons dashicons-format-image"></span>
                    <?php _e('Banner & Header', 'property-plugin'); ?>
                </button>
                <button class="tab-button" data-tab="colors">
                    <span class="dashicons dashicons-admin-appearance"></span>
                    <?php _e('Colors & Typography', 'property-plugin'); ?>
                </button>
                <button class="tab-button" data-tab="card">
                    <span class="dashicons dashicons-layout"></span>
                    <?php _e('Property Card', 'property-plugin'); ?>
                </button>
                <button class="tab-button" data-tab="taxonomies">
                    <span class="dashicons dashicons-category"></span>
                    <?php _e('Custom Taxonomies', 'property-plugin'); ?>
                </button>
                <button class="tab-button" data-tab="contact">
                    <span class="dashicons dashicons-email"></span>
                    <?php _e('Contact & Lead Form', 'property-plugin'); ?>
                </button>
                <button class="tab-button" data-tab="api">
                    <span class="dashicons dashicons-admin-network"></span>
                    <?php _e('API Keys', 'property-plugin'); ?>
                </button>
                <button class="tab-button" data-tab="advanced">
                    <span class="dashicons dashicons-admin-tools"></span>
                    <?php _e('Advanced', 'property-plugin'); ?>
                </button>
            </div>

            <!-- Settings Content -->
            <div class="property-plugin-settings-content">
                
                <!-- General Settings Tab -->
                <div class="tab-content active" id="general">
                    <div class="settings-section">
                        <h2><?php _e('General Settings', 'property-plugin'); ?></h2>
                        <p class="section-description"><?php _e('Configure basic plugin settings', 'property-plugin'); ?></p>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="header_text"><?php _e('Header Text', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="header_text" name="property_plugin_header_text" 
                                           value="<?php echo esc_attr(get_option('property_plugin_header_text', 'Find Your Dream Property')); ?>" 
                                           class="regular-text" />
                                    <p class="description"><?php _e('Main heading text for the property listings page', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="properties_per_page"><?php _e('Properties Per Page', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="properties_per_page" name="property_plugin_properties_per_page" 
                                           value="<?php echo esc_attr(get_option('property_plugin_properties_per_page', '12')); ?>" 
                                           class="small-text" min="1" max="100" />
                                    <p class="description"><?php _e('Number of properties to display per page', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="default_currency"><?php _e('Default Currency', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <select id="default_currency" name="property_plugin_default_currency">
                                        <option value="USD" <?php selected(get_option('property_plugin_default_currency', 'USD'), 'USD'); ?>><?php _e('USD ($) - US Dollar', 'property-plugin'); ?></option>
                                        <option value="EUR" <?php selected(get_option('property_plugin_default_currency', 'USD'), 'EUR'); ?>><?php _e('EUR (€) - Euro', 'property-plugin'); ?></option>
                                        <option value="GBP" <?php selected(get_option('property_plugin_default_currency', 'USD'), 'GBP'); ?>><?php _e('GBP (£) - British Pound', 'property-plugin'); ?></option>
                                        <option value="INR" <?php selected(get_option('property_plugin_default_currency', 'USD'), 'INR'); ?>><?php _e('INR (₹) - Indian Rupee', 'property-plugin'); ?></option>
                                        <option value="PKR" <?php selected(get_option('property_plugin_default_currency', 'USD'), 'PKR'); ?>><?php _e('PKR (Rs) - Pakistani Rupee', 'property-plugin'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Features', 'property-plugin'); ?></th>
                                <td>
                                    <fieldset>
                                        <label>
                                            <input type="checkbox" name="property_plugin_enable_compare" 
                                                   <?php checked(get_option('property_plugin_enable_compare', '1'), '1'); ?> />
                                            <?php _e('Enable Property Compare', 'property-plugin'); ?>
                                        </label>
                                        <p class="description"><?php _e('Allow users to compare multiple properties', 'property-plugin'); ?></p>
                                    </fieldset>
                                    
                                    <fieldset>
                                        <label>
                                            <input type="checkbox" name="property_plugin_enable_preloader" 
                                                   <?php checked(get_option('property_plugin_enable_preloader', '1'), '1'); ?> />
                                            <?php _e('Enable Preloader', 'property-plugin'); ?>
                                        </label>
                                        <p class="description"><?php _e('Show loading animation while properties load', 'property-plugin'); ?></p>
                                    </fieldset>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Banner & Header Tab -->
                <div class="tab-content" id="banner">
                    <div class="settings-section">
                        <h2><?php _e('Banner & Header Settings', 'property-plugin'); ?></h2>
                        <p class="section-description"><?php _e('Customize the banner image and header appearance', 'property-plugin'); ?></p>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="banner_image"><?php _e('Banner Image', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <div class="image-upload-container">
                                        <img id="banner_image_preview" src="<?php echo esc_url(get_option('property_plugin_banner_image', '')); ?>" 
                                             style="<?php echo get_option('property_plugin_banner_image') ? '' : 'display:none;'; ?>max-width: 300px; height: auto; margin-bottom: 10px;" />
                                        <br/>
                                        <input type="hidden" id="banner_image" name="property_plugin_banner_image" 
                                               value="<?php echo esc_attr(get_option('property_plugin_banner_image', '')); ?>" />
                                        <button type="button" class="button" id="upload_banner_image"><?php _e('Upload Image', 'property-plugin'); ?></button>
                                        <button type="button" class="button" id="remove_banner_image" <?php echo get_option('property_plugin_banner_image') ? '' : 'style="display:none;"'; ?>><?php _e('Remove Image', 'property-plugin'); ?></button>
                                        <p class="description"><?php _e('Upload a banner image for the property listings page. Recommended size: 1920x600px', 'property-plugin'); ?></p>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="banner_subtitle"><?php _e('Banner Subtitle', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="banner_subtitle" name="property_plugin_banner_subtitle" 
                                           value="<?php echo esc_attr(get_option('property_plugin_banner_subtitle', 'Discover the perfect home for your family')); ?>" 
                                           class="regular-text" />
                                    <p class="description"><?php _e('Subtitle text displayed below the main heading', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="banner_height"><?php _e('Banner Height', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="range" id="banner_height" name="property_plugin_banner_height" 
                                           value="<?php echo esc_attr(get_option('property_plugin_banner_height', '400')); ?>" 
                                           min="200" max="800" class="range-slider" />
                                    <span id="banner_height_value"><?php echo esc_attr(get_option('property_plugin_banner_height', '400')); ?>px</span>
                                    <p class="description"><?php _e('Adjust the height of the banner image', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="banner_overlay"><?php _e('Banner Overlay', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="range" id="banner_overlay" name="property_plugin_banner_overlay" 
                                           value="<?php echo esc_attr(get_option('property_plugin_banner_overlay', '50')); ?>" 
                                           min="0" max="100" class="range-slider" />
                                    <span id="banner_overlay_value"><?php echo esc_attr(get_option('property_plugin_banner_overlay', '50')); ?>%</span>
                                    <p class="description"><?php _e('Dark overlay opacity on banner image (0-100%)', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="banner_overlay_color"><?php _e('Overlay Color', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="color" id="banner_overlay_color" name="property_plugin_banner_overlay_color" 
                                           value="<?php echo esc_attr(get_option('property_plugin_banner_overlay_color', '#000000')); ?>" 
                                           class="color-picker" />
                                    <p class="description"><?php _e('Choose overlay color for better text readability', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Colors & Typography Tab -->
                <div class="tab-content" id="colors">
                    <div class="settings-section">
                        <h2><?php _e('Colors & Typography', 'property-plugin'); ?></h2>
                        <p class="section-description"><?php _e('Customize colors and fonts for your property listings', 'property-plugin'); ?></p>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="primary_color"><?php _e('Primary Color', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="color" id="primary_color" name="property_plugin_primary_color" 
                                           value="<?php echo esc_attr(get_option('property_plugin_primary_color', '#2563eb')); ?>" 
                                           class="color-picker" />
                                    <p class="description"><?php _e('Main brand color used for buttons, links, and accents', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="secondary_color"><?php _e('Secondary Color', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="color" id="secondary_color" name="property_plugin_secondary_color" 
                                           value="<?php echo esc_attr(get_option('property_plugin_secondary_color', '#10b981')); ?>" 
                                           class="color-picker" />
                                    <p class="description"><?php _e('Secondary color for badges and highlights', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="text_color"><?php _e('Text Color', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="color" id="text_color" name="property_plugin_text_color" 
                                           value="<?php echo esc_attr(get_option('property_plugin_text_color', '#1f2937')); ?>" 
                                           class="color-picker" />
                                    <p class="description"><?php _e('Default text color for content', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="background_color"><?php _e('Background Color', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="color" id="background_color" name="property_plugin_background_color" 
                                           value="<?php echo esc_attr(get_option('property_plugin_background_color', '#f3f4f6')); ?>" 
                                           class="color-picker" />
                                    <p class="description"><?php _e('Page background color', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="card_background"><?php _e('Card Background Color', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="color" id="card_background" name="property_plugin_card_background" 
                                           value="<?php echo esc_attr(get_option('property_plugin_card_background', '#ffffff')); ?>" 
                                           class="color-picker" />
                                    <p class="description"><?php _e('Background color for property cards', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="font_family"><?php _e('Font Family', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <select id="font_family" name="property_plugin_font_family">
                                        <option value="Arial, sans-serif" <?php selected(get_option('property_plugin_font_family', 'Arial, sans-serif'), 'Arial, sans-serif'); ?>>Arial</option>
                                        <option value="'Helvetica Neue', sans-serif" <?php selected(get_option('property_plugin_font_family', 'Arial, sans-serif'), "'Helvetica Neue', sans-serif"); ?>>Helvetica</option>
                                        <option value="Georgia, serif" <?php selected(get_option('property_plugin_font_family', 'Arial, sans-serif'), 'Georgia, serif'); ?>>Georgia</option>
                                        <option value="'Times New Roman', serif" <?php selected(get_option('property_plugin_font_family', 'Arial, sans-serif'), "'Times New Roman', serif"); ?>>Times New Roman</option>
                                        <option value="'Courier New', monospace" <?php selected(get_option('property_plugin_font_family', 'Arial, sans-serif'), "'Courier New', monospace"); ?>>Courier New</option>
                                        <option value="Verdana, sans-serif" <?php selected(get_option('property_plugin_font_family', 'Arial, sans-serif'), 'Verdana, sans-serif'); ?>>Verdana</option>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="font_size"><?php _e('Base Font Size', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="range" id="font_size" name="property_plugin_font_size" 
                                           value="<?php echo esc_attr(get_option('property_plugin_font_size', '16')); ?>" 
                                           min="12" max="24" class="range-slider" />
                                    <span id="font_size_value"><?php echo esc_attr(get_option('property_plugin_font_size', '16')); ?>px</span>
                                    <p class="description"><?php _e('Base font size for all text', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Property Card Tab -->
                <div class="tab-content" id="card">
                    <div class="settings-section">
                        <h2><?php _e('Property Card Settings', 'property-plugin'); ?></h2>
                        <p class="section-description"><?php _e('Configure how property cards are displayed', 'property-plugin'); ?></p>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="card_layout"><?php _e('Card Layout', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <select id="card_layout" name="property_plugin_card_layout">
                                        <option value="grid" <?php selected(get_option('property_plugin_card_layout', 'grid'), 'grid'); ?>><?php _e('Grid Layout', 'property-plugin'); ?></option>
                                        <option value="list" <?php selected(get_option('property_plugin_card_layout', 'grid'), 'list'); ?>><?php _e('List Layout', 'property-plugin'); ?></option>
                                    </select>
                                    <p class="description"><?php _e('Choose how properties are displayed', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Display Options', 'property-plugin'); ?></th>
                                <td>
                                    <fieldset>
                                        <label>
                                            <input type="checkbox" name="property_plugin_show_badge" 
                                                   <?php checked(get_option('property_plugin_show_badge', '1'), '1'); ?> />
                                            <?php _e('Show Status Badge (For Sale, For Rent, etc.)', 'property-plugin'); ?>
                                        </label>
                                    </fieldset>
                                    
                                    <fieldset>
                                        <label>
                                            <input type="checkbox" name="property_plugin_show_area" 
                                                   <?php checked(get_option('property_plugin_show_area', '1'), '1'); ?> />
                                            <?php _e('Show Property Area', 'property-plugin'); ?>
                                        </label>
                                    </fieldset>
                                    
                                    <fieldset>
                                        <label>
                                            <input type="checkbox" name="property_plugin_show_address" 
                                                   <?php checked(get_option('property_plugin_show_address', '1'), '1'); ?> />
                                            <?php _e('Show Full Address', 'property-plugin'); ?>
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Custom Taxonomies Tab -->
                <div class="tab-content" id="taxonomies">
                    <div class="settings-section">
                        <h2><?php _e('Custom Taxonomies Manager', 'property-plugin'); ?></h2>
                        <p class="section-description"><?php _e('Create custom taxonomies for your properties (e.g., Floor, Year Built, Parking, etc.)', 'property-plugin'); ?></p>
                        
                        <div class="taxonomy-creator">
                            <h3><?php _e('Create New Taxonomy', 'property-plugin'); ?></h3>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="new_taxonomy_name"><?php _e('Taxonomy Name', 'property-plugin'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" id="new_taxonomy_name" name="new_taxonomy_name" 
                                               class="regular-text" placeholder="e.g., Floors" />
                                        <p class="description"><?php _e('Display name for the taxonomy (e.g., Floors, Year Built, Parking)', 'property-plugin'); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="new_taxonomy_slug"><?php _e('Taxonomy Slug', 'property-plugin'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" id="new_taxonomy_slug" name="new_taxonomy_slug" 
                                               class="regular-text" placeholder="e.g., property-floor" />
                                        <p class="description"><?php _e('Unique slug (lowercase, hyphens allowed). Example: property-floor, year-built, parking-type', 'property-plugin'); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row"><?php _e('Actions', 'property-plugin'); ?></th>
                                    <td>
                                        <button type="button" class="button button-primary" id="create-taxonomy"><?php _e('Create Taxonomy', 'property-plugin'); ?></button>
                                        <span class="taxonomy-status" id="taxonomy-status"></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="taxonomy-list">
                            <h3><?php _e('Existing Custom Taxonomies', 'property-plugin'); ?></h3>
                            <div id="custom-taxonomies-list">
                                <?php
                                $custom_taxonomies = get_option('property_plugin_custom_taxonomies', array());
                                if (!empty($custom_taxonomies)) {
                                    echo '<table class="wp-list-table widefat fixed striped">';
                                    echo '<thead><tr><th>' . __('Name', 'property-plugin') . '</th><th>' . __('Slug', 'property-plugin') . '</th><th>' . __('Actions', 'property-plugin') . '</th></tr></thead>';
                                    echo '<tbody>';
                                    foreach ($custom_taxonomies as $tax) {
                                        echo '<tr>';
                                        echo '<td>' . esc_html($tax['name']) . '</td>';
                                        echo '<td><code>' . esc_html($tax['slug']) . '</code></td>';
                                        echo '<td><button type="button" class="button delete-taxonomy" data-slug="' . esc_attr($tax['slug']) . '">' . __('Delete', 'property-plugin') . '</button></td>';
                                        echo '</tr>';
                                    }
                                    echo '</tbody></table>';
                                } else {
                                    echo '<p class="description">' . __('No custom taxonomies created yet.', 'property-plugin') . '</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact & Lead Form Tab -->
                <div class="tab-content" id="contact">
                    <div class="settings-section">
                        <h2><?php _e('Contact & Lead Form', 'property-plugin'); ?></h2>
                        <p class="section-description"><?php _e('Configure contact information and lead capture form', 'property-plugin'); ?></p>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="contact_email"><?php _e('Contact Email', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="email" id="contact_email" name="property_plugin_contact_email" 
                                           value="<?php echo esc_attr(get_option('property_plugin_contact_email', '')); ?>" 
                                           class="regular-text" />
                                    <p class="description"><?php _e('Email address for contact inquiries', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="contact_phone"><?php _e('Contact Phone', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="tel" id="contact_phone" name="property_plugin_contact_phone" 
                                           value="<?php echo esc_attr(get_option('property_plugin_contact_phone', '')); ?>" 
                                           class="regular-text" />
                                    <p class="description"><?php _e('Phone number for contact inquiries', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="enable_lead_form"><?php _e('Enable Lead Form', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="checkbox" name="property_plugin_enable_lead_form" 
                                           <?php checked(get_option('property_plugin_enable_lead_form', '1'), '1'); ?> />
                                    <p class="description"><?php _e('Show lead capture form on property details page', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="lead_form_title"><?php _e('Lead Form Title', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="lead_form_title" name="property_plugin_lead_form_title" 
                                           value="<?php echo esc_attr(get_option('property_plugin_lead_form_title', 'Interested in this property?')); ?>" 
                                           class="regular-text" />
                                    <p class="description"><?php _e('Title text for the lead capture form', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- API Keys Tab -->
                <div class="tab-content" id="api">
                    <div class="settings-section">
                        <h2><?php _e('API Keys Configuration', 'property-plugin'); ?></h2>
                        <p class="section-description"><?php _e('Configure third-party API keys for enhanced functionality', 'property-plugin'); ?></p>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="google_api_key"><?php _e('Google Maps API Key', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="google_api_key" name="property_plugin_google_api_key" 
                                           value="<?php echo esc_attr(get_option('property_plugin_google_api_key', '')); ?>" 
                                           class="regular-text code" placeholder="AIzaSy..." />
                                    <p class="description">
                                        <?php _e('Enter your Google Places API key for address autocomplete.', 'property-plugin'); ?><br/>
                                        <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank"><?php _e('Get API Key →', 'property-plugin'); ?></a>
                                    </p>
                                    <div class="api-setup-instructions" style="margin-top: 15px; padding: 15px; background: #fff; border-left: 4px solid #2271b1;">
                                        <h4 style="margin-top: 0;"><?php _e('Setup Instructions:', 'property-plugin'); ?></h4>
                                        <ol>
                                            <li><?php _e('Go to Google Cloud Console', 'property-plugin'); ?></li>
                                            <li><?php _e('Create a project or select existing', 'property-plugin'); ?></li>
                                            <li><?php _e('Enable "Places API" and "Maps JavaScript API"', 'property-plugin'); ?></li>
                                            <li><?php _e('Create API credentials', 'property-plugin'); ?></li>
                                            <li><?php _e('Paste the API key here', 'property-plugin'); ?></li>
                                        </ol>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Advanced Tab -->
                <div class="tab-content" id="advanced">
                    <div class="settings-section">
                        <h2><?php _e('Advanced Settings', 'property-plugin'); ?></h2>
                        <p class="section-description"><?php _e('Custom CSS and analytics configuration', 'property-plugin'); ?></p>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="custom_css"><?php _e('Custom CSS', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <textarea id="custom_css" name="property_plugin_custom_css" 
                                              rows="10" class="large-text code"><?php echo esc_textarea(get_option('property_plugin_custom_css', '')); ?></textarea>
                                    <p class="description"><?php _e('Add custom CSS to override plugin styles. Use with caution.', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="google_analytics"><?php _e('Google Analytics ID', 'property-plugin'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="google_analytics" name="property_plugin_google_analytics" 
                                           value="<?php echo esc_attr(get_option('property_plugin_google_analytics', '')); ?>" 
                                           class="regular-text" placeholder="G-XXXXXXXXXX" />
                                    <p class="description"><?php _e('Enter your Google Analytics 4 Measurement ID', 'property-plugin'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <div class="save-settings-footer">
            <button type="button" class="button button-primary button-large" id="save-all-settings-bottom"><?php _e('Save All Changes', 'property-plugin'); ?></button>
            <span class="save-status" id="save-status"></span>
        </div>
    </div>

    <style>
        .property-plugin-settings {
            max-width: 1200px;
            margin: 20px auto;
        }
        
        .property-plugin-header {
            background: #fff;
            padding: 20px;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .property-plugin-header h1 {
            margin: 0;
            flex: 1;
        }
        
        .property-plugin-settings-container {
            display: flex;
            gap: 20px;
        }
        
        .property-plugin-tabs {
            width: 220px;
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 10px 0;
            flex-shrink: 0;
        }
        
        .tab-button {
            width: 100%;
            padding: 12px 15px;
            border: none;
            background: none;
            text-align: left;
            cursor: pointer;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        
        .tab-button:hover {
            background: #f0f0f1;
        }
        
        .tab-button.active {
            background: #f0f6fc;
            border-left-color: #2271b1;
            color: #2271b1;
            font-weight: 600;
        }
        
        .tab-button .dashicons {
            margin-right: 8px;
            vertical-align: middle;
        }
        
        .property-plugin-settings-content {
            flex: 1;
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 20px;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .settings-section {
            margin-bottom: 30px;
        }
        
        .settings-section h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #2271b1;
        }
        
        .section-description {
            color: #646970;
            margin-top: -5px;
        }
        
        .form-table th {
            width: 200px;
            padding: 15px 10px 15px 0;
            vertical-align: top;
        }
        
        .form-table td {
            padding: 15px 10px;
        }
        
        .form-table tr {
            border-bottom: 1px solid #f0f0f1;
        }
        
        fieldset {
            margin-bottom: 15px;
        }
        
        fieldset:last-child {
            margin-bottom: 0;
        }
        
        .range-slider {
            width: 300px;
            margin-right: 10px;
        }
        
        .color-picker {
            width: 100px;
            height: 40px;
            padding: 0;
            border: 1px solid #8c8f94;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .image-upload-container {
            text-align: center;
        }
        
        .image-upload-container img {
            border: 2px dashed #ccd0d4;
            padding: 5px;
            border-radius: 4px;
        }
        
        .save-settings-footer {
            background: #fff;
            padding: 15px 20px;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .save-status {
            color: #00a32a;
            font-weight: 600;
            padding: 8px 12px;
            background: #f0f6fc;
            border-radius: 4px;
            display: inline-block;
        }
        
        .taxonomy-creator {
            background: #f0f6fc;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 30px;
            border-left: 4px solid #2271b1;
        }
        
        .taxonomy-creator h3 {
            margin-top: 0;
            color: #2271b1;
        }
        
        .taxonomy-list h3 {
            margin-bottom: 15px;
        }
        
        .taxonomy-status {
            margin-left: 10px;
            font-weight: 600;
        }
        
        .delete-taxonomy {
            color: #d63638;
            border-color: #d63638;
        }
        
        .delete-taxonomy:hover {
            background: #d63638;
            color: #fff;
        }
        
        @media (max-width: 782px) {
            .property-plugin-settings-container {
                flex-direction: column;
            }
            
            .property-plugin-tabs {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
                padding: 10px;
            }
            
            .tab-button {
                flex: 1;
                min-width: 120px;
                border-left: none;
                border-bottom: 3px solid transparent;
            }
            
            .tab-button.active {
                border-left: none;
                border-bottom-color: #2271b1;
            }
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Initialize color picker
        $('.color-picker').wpColorPicker();
        
        // Tab switching
        $('.tab-button').on('click', function() {
            var tab = $(this).data('tab');
            
            // Update active tab
            $('.tab-button').removeClass('active');
            $(this).addClass('active');
            
            // Show corresponding content
            $('.tab-content').removeClass('active');
            $('#' + tab).addClass('active');
        });
        
        // Range slider value display
        $('.range-slider').on('input', function() {
            var id = $(this).attr('id');
            var value = $(this).val();
            var suffix = id === 'banner_overlay' || id === 'overlay' ? '%' : 'px';
            $('#' + id + '_value').text(value + suffix);
        });
        
        // Image uploader
        var mediaUploader;
        $('#upload_banner_image').on('click', function(e) {
            e.preventDefault();
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: 'Choose Banner Image',
                button: {
                    text: 'Use this image'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#banner_image').val(attachment.url);
                $('#banner_image_preview').attr('src', attachment.url).show();
                $('#remove_banner_image').show();
            });
            
            mediaUploader.open();
        });
        
        $('#remove_banner_image').on('click', function() {
            $('#banner_image').val('');
            $('#banner_image_preview').attr('src', '').hide();
            $(this).hide();
        });
        
        // Save all settings
        function saveSettings() {
            var data = {
                action: 'property_plugin_save_all_settings',
                nonce: '<?php echo wp_create_nonce('property_plugin_nonce'); ?>',
                settings: {}
            };
            
            // Collect all settings from form fields
            $('.property-plugin-settings').find('input, select, textarea').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    if ($(this).attr('type') === 'checkbox') {
                        data.settings[name] = $(this).is(':checked') ? '1' : '0';
                    } else if ($(this).attr('type') === 'color') {
                        // Color picker values
                        data.settings[name] = $(this).val() || '';
                    } else {
                        data.settings[name] = $(this).val();
                    }
                }
            });
            
            $('#save-status').text('Saving...').css('color', '#646970');
            
            // Disable save buttons during save
            $('#save-all-settings, #save-all-settings-bottom').prop('disabled', true);
            
            $.post(ajaxurl, data, function(response) {
                if (response.success) {
                    $('#save-status').text('✓ Settings saved successfully! Refresh page to see changes on frontend.').css('color', '#00a32a');
                    
                    // Re-enable buttons
                    $('#save-all-settings, #save-all-settings-bottom').prop('disabled', false);
                    
                    setTimeout(function() {
                        $('#save-status').text('');
                    }, 5000);
                } else {
                    $('#save-status').text('✗ Error: ' + (response.data || 'Unknown error')).css('color', '#d63638');
                    $('#save-all-settings, #save-all-settings-bottom').prop('disabled', false);
                }
            }).fail(function() {
                $('#save-status').text('✗ Error saving settings. Please try again.').css('color', '#d63638');
                $('#save-all-settings, #save-all-settings-bottom').prop('disabled', false);
            });
        }
        
        $('#save-all-settings, #save-all-settings-bottom').on('click', saveSettings);
        
        // Keyboard shortcut: Ctrl+S to save
        $(document).on('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveSettings();
            }
        });
        
        // Auto-generate slug from name
        $('#new_taxonomy_name').on('input', function() {
            var name = $(this).val();
            var slug = name.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            if (slug && !$('#new_taxonomy_slug').val()) {
                $('#new_taxonomy_slug').val('property-' + slug);
            }
        });
        
        // Create taxonomy
        $('#create-taxonomy').on('click', function() {
            var name = $('#new_taxonomy_name').val().trim();
            var slug = $('#new_taxonomy_slug').val().trim();
            
            if (!name || !slug) {
                $('#taxonomy-status').text('✗ Please fill in both name and slug').css('color', '#d63638');
                return;
            }
            
            // Validate slug format
            if (!/^[a-z0-9-]+$/.test(slug)) {
                $('#taxonomy-status').text('✗ Slug can only contain lowercase letters, numbers, and hyphens').css('color', '#d63638');
                return;
            }
            
            $('#taxonomy-status').text('Creating...').css('color', '#646970');
            
            $.post(ajaxurl, {
                action: 'property_plugin_create_taxonomy',
                nonce: '<?php echo wp_create_nonce('property_plugin_nonce'); ?>',
                name: name,
                slug: slug
            }, function(response) {
                if (response.success) {
                    $('#taxonomy-status').text('✓ ' + response.data).css('color', '#00a32a');
                    $('#new_taxonomy_name').val('');
                    $('#new_taxonomy_slug').val('');
                    
                    // Reload page after 1.5 seconds to show new taxonomy
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $('#taxonomy-status').text('✗ ' + response.data).css('color', '#d63638');
                }
            }).fail(function() {
                $('#taxonomy-status').text('✗ Error creating taxonomy').css('color', '#d63638');
            });
        });
        
        // Delete taxonomy
        $(document).on('click', '.delete-taxonomy', function() {
            if (!confirm('Are you sure you want to delete this taxonomy? All associated terms will be removed.')) {
                return;
            }
            
            var slug = $(this).data('slug');
            var $button = $(this);
            
            $.post(ajaxurl, {
                action: 'property_plugin_delete_taxonomy',
                nonce: '<?php echo wp_create_nonce('property_plugin_nonce'); ?>',
                slug: slug
            }, function(response) {
                if (response.success) {
                    $button.closest('tr').fadeOut();
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    alert('Error: ' + response.data);
                }
            }).fail(function() {
                alert('Error deleting taxonomy');
            });
        });
    });
    </script>
    <?php
}

/**
 * Enqueue admin scripts and styles for settings page
 */
function property_plugin_admin_enqueue_scripts($hook) {
    // Only load on our settings page
    if ('toplevel_page_property-plugin-settings' !== $hook) {
        return;
    }
    
    // Enqueue WordPress media uploader
    wp_enqueue_media();
    
    // Enqueue color picker
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}
add_action('admin_enqueue_scripts', 'property_plugin_admin_enqueue_scripts');

/**
 * AJAX handler to save all settings
 */
function property_plugin_save_all_settings_ajax() {
    check_ajax_referer('property_plugin_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }
    
    $settings = isset($_POST['settings']) ? $_POST['settings'] : array();
    
    // Define which settings should use which sanitization
    $sanitize_functions = array(
        'property_plugin_header_text' => 'sanitize_text_field',
        'property_plugin_properties_per_page' => 'absint',
        'property_plugin_default_currency' => 'sanitize_text_field',
        'property_plugin_enable_compare' => 'sanitize_text_field',
        'property_plugin_enable_preloader' => 'sanitize_text_field',
        'property_plugin_banner_image' => 'esc_url_raw',
        'property_plugin_banner_subtitle' => 'sanitize_text_field',
        'property_plugin_banner_height' => 'absint',
        'property_plugin_banner_overlay' => 'absint',
        'property_plugin_banner_overlay_color' => 'sanitize_hex_color',
        'property_plugin_primary_color' => 'sanitize_hex_color',
        'property_plugin_secondary_color' => 'sanitize_hex_color',
        'property_plugin_text_color' => 'sanitize_hex_color',
        'property_plugin_background_color' => 'sanitize_hex_color',
        'property_plugin_card_background' => 'sanitize_hex_color',
        'property_plugin_font_family' => 'sanitize_text_field',
        'property_plugin_font_size' => 'absint',
        'property_plugin_card_layout' => 'sanitize_text_field',
        'property_plugin_show_badge' => 'sanitize_text_field',
        'property_plugin_show_area' => 'sanitize_text_field',
        'property_plugin_show_address' => 'sanitize_text_field',
        'property_plugin_contact_email' => 'sanitize_email',
        'property_plugin_contact_phone' => 'sanitize_text_field',
        'property_plugin_enable_lead_form' => 'sanitize_text_field',
        'property_plugin_lead_form_title' => 'sanitize_text_field',
        'property_plugin_google_api_key' => 'sanitize_text_field',
        'property_plugin_custom_css' => 'wp_strip_all_tags',
        'property_plugin_google_analytics' => 'sanitize_text_field',
    );
    
    foreach ($settings as $key => $value) {
        if (array_key_exists($key, $sanitize_functions)) {
            $sanitized_value = call_user_func($sanitize_functions[$key], $value);
            update_option($key, $sanitized_value);
        }
    }
    
    wp_send_json_success('Settings saved');
}
add_action('wp_ajax_property_plugin_save_all_settings', 'property_plugin_save_all_settings_ajax');

/**
 * AJAX handler to create custom taxonomy
 */
function property_plugin_create_taxonomy_ajax() {
    check_ajax_referer('property_plugin_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }
    
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $slug = isset($_POST['slug']) ? sanitize_key($_POST['slug']) : '';
    
    if (empty($name) || empty($slug)) {
        wp_send_json_error('Name and slug are required');
    }
    
    // Validate slug format
    if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
        wp_send_json_error('Invalid slug format. Use only lowercase letters, numbers, and hyphens');
    }
    
    // Check if taxonomy already exists
    if (taxonomy_exists($slug)) {
        wp_send_json_error('Taxonomy "' . $slug . '" already exists');
    }
    
    // Get existing custom taxonomies
    $custom_taxonomies = get_option('property_plugin_custom_taxonomies', array());
    
    // Check if slug is already in our list
    foreach ($custom_taxonomies as $tax) {
        if ($tax['slug'] === $slug) {
            wp_send_json_error('Taxonomy slug "' . $slug . '" is already registered');
        }
    }
    
    // Add to custom taxonomies list
    $custom_taxonomies[] = array(
        'name' => $name,
        'slug' => $slug,
    );
    
    update_option('property_plugin_custom_taxonomies', $custom_taxonomies);
    
    // Register the taxonomy immediately
    property_plugin_register_custom_taxonomy($slug, $name);
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    wp_send_json_success('Taxonomy "' . $name . '" created successfully!');
}
add_action('wp_ajax_property_plugin_create_taxonomy', 'property_plugin_create_taxonomy_ajax');

/**
 * AJAX handler to delete custom taxonomy
 */
function property_plugin_delete_taxonomy_ajax() {
    check_ajax_referer('property_plugin_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }
    
    $slug = isset($_POST['slug']) ? sanitize_key($_POST['slug']) : '';
    
    if (empty($slug)) {
        wp_send_json_error('Taxonomy slug is required');
    }
    
    // Get existing custom taxonomies
    $custom_taxonomies = get_option('property_plugin_custom_taxonomies', array());
    
    // Remove the taxonomy
    $found = false;
    foreach ($custom_taxonomies as $key => $tax) {
        if ($tax['slug'] === $slug) {
            $name = $tax['name'];
            unset($custom_taxonomies[$key]);
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        wp_send_json_error('Taxonomy not found');
    }
    
    // Update option
    update_option('property_plugin_custom_taxonomies', array_values($custom_taxonomies));
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    wp_send_json_success('Taxonomy deleted successfully');
}
add_action('wp_ajax_property_plugin_delete_taxonomy', 'property_plugin_delete_taxonomy_ajax');

/**
 * Register a custom taxonomy
 */
function property_plugin_register_custom_taxonomy($slug, $name) {
    $labels = array(
        'name'              => $name,
        'singular_name'     => $name,
        'search_items'      => 'Search ' . $name,
        'all_items'         => 'All ' . $name,
        'edit_item'         => 'Edit ' . $name,
        'update_item'       => 'Update ' . $name,
        'add_new_item'      => 'Add New ' . $name,
        'new_item_name'     => 'New ' . $name . ' Name',
        'menu_name'         => $name,
    );
    
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => $slug),
        'show_in_rest'      => true,
    );
    
    register_taxonomy($slug, 'property', $args);
}

/**
 * Register all custom taxonomies on init
 */
function property_plugin_register_all_custom_taxonomies() {
    $custom_taxonomies = get_option('property_plugin_custom_taxonomies', array());
    
    if (!empty($custom_taxonomies)) {
        foreach ($custom_taxonomies as $tax) {
            property_plugin_register_custom_taxonomy($tax['slug'], $tax['name']);
        }
    }
}
add_action('init', 'property_plugin_register_all_custom_taxonomies');
