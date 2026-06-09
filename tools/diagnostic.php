<?php
/**
 * Property Plugin - Diagnostic Tool
 * Access: yoursite.com/wp-content/plugins/Property_Plugin/tools/diagnostic.php
 */

// Load WordPress
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';

// Only allow logged-in admins
if (!current_user_can('manage_options')) {
    wp_die('Access denied. Please log in as an administrator.');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Property Plugin Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2271b1; border-bottom: 3px solid #2271b1; padding-bottom: 10px; }
        h2 { color: #1d2327; margin-top: 30px; }
        .success { color: #00a32a; font-weight: bold; }
        .error { color: #d63638; font-weight: bold; }
        .warning { color: #dba617; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        th { background: #2271b1; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .info-box { background: #f0f6fc; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Property Plugin Diagnostic Tool</h1>
        
        <?php
        // Check 1: Plugin file exists
        echo '<h2>1. Plugin Files Check</h2>';
        $plugin_file = dirname(__FILE__) . '/../Property_Plugin.php';
        if (file_exists($plugin_file)) {
            echo '<p class="success">✓ Main plugin file exists</p>';
        } else {
            echo '<p class="error">✗ Main plugin file NOT found</p>';
        }
        
        $data_file = dirname(__FILE__) . '/../data/default-properties.json';
        if (file_exists($data_file)) {
            echo '<p class="success">✓ Default properties JSON exists</p>';
            $json_content = file_get_contents($data_file);
            $properties = json_decode($json_content, true);
            if (is_array($properties)) {
                echo '<p class="success">✓ JSON is valid, contains ' . count($properties) . ' properties</p>';
            } else {
                echo '<p class="error">✗ JSON is invalid</p>';
            }
        } else {
            echo '<p class="error">✗ Default properties JSON NOT found</p>';
        }
        
        // Check 2: Custom Post Type
        echo '<h2>2. Custom Post Type Check</h2>';
        $post_type_exists = post_type_exists('property');
        if ($post_type_exists) {
            echo '<p class="success">✓ Property post type is registered</p>';
        } else {
            echo '<p class="error">✗ Property post type is NOT registered</p>';
            echo '<p class="warning">⚠ Try visiting Settings > Permalinks and click "Save Changes" to flush rewrite rules</p>';
        }
        
        // Check 3: Properties Count
        echo '<h2>3. Properties in Database</h2>';
        $args = array(
            'post_type' => 'property',
            'posts_per_page' => -1,
            'post_status' => 'any',
        );
        $properties = get_posts($args);
        $total_properties = count($properties);
        
        if ($total_properties > 0) {
            echo '<p class="success">✓ Found ' . $total_properties . ' properties in database</p>';
        } else {
            echo '<p class="error">✗ No properties found in database</p>';
            echo '<p class="warning">⚠ Sample data was not imported. Try deactivating and reactivating the plugin.</p>';
        }
        
        // Check 4: REST API
        echo '<h2>4. REST API Endpoint Check</h2>';
        $api_url = rest_url('property-plugin/v1/properties');
        echo '<p>API URL: <code class="code">' . esc_url($api_url) . '</code></p>';
        
        // Test API call
        $response = wp_remote_get($api_url);
        if (is_wp_error($response)) {
            echo '<p class="error">✗ API Error: ' . esc_html($response->get_error_message()) . '</p>';
        } else {
            $status_code = wp_remote_retrieve_response_code($response);
            if ($status_code === 200) {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
                if (is_array($data)) {
                    echo '<p class="success">✓ API is working! Returns ' . count($data) . ' properties</p>';
                } else {
                    echo '<p class="error">✗ API returned invalid data</p>';
                }
            } else {
                echo '<p class="error">✗ API returned HTTP status: ' . $status_code . '</p>';
                $body = wp_remote_retrieve_body($response);
                echo '<pre>' . esc_html(substr($body, 0, 500)) . '</pre>';
            }
        }
        
        // Check 5: Sample Properties Table
        if ($total_properties > 0) {
            echo '<h2>5. Sample Properties Details</h2>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Title</th><th>Status</th><th>Has Thumbnail?</th><th>Has Gallery?</th><th>Gallery Count</th></tr>';
            
            foreach (array_slice($properties, 0, 10) as $prop) {
                $has_thumb = has_post_thumbnail($prop->ID) ? '✓ Yes' : '✗ No';
                $gallery_raw = get_post_meta($prop->ID, '_property_gallery', true);
                $gallery_count = !empty($gallery_raw) ? count(explode(',', $gallery_raw)) : 0;
                $has_gallery = $gallery_count > 0 ? '✓ Yes (' . $gallery_count . ')' : '✗ No';
                
                echo '<tr>';
                echo '<td>' . $prop->ID . '</td>';
                echo '<td>' . esc_html($prop->post_title) . '</td>';
                echo '<td>' . esc_html(get_post_meta($prop->ID, '_property_status', true)) . '</td>';
                echo '<td>' . $has_thumb . '</td>';
                echo '<td>' . $has_gallery . '</td>';
                echo '<td>' . $gallery_count . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        
        // Check 6: Settings
        echo '<h2>6. Plugin Settings Check</h2>';
        $settings_installed = get_option('property_plugin_default_data_installed');
        if ($settings_installed) {
            echo '<p class="success">✓ Default data installation flag is set</p>';
        } else {
            echo '<p class="error">✗ Default data installation flag is NOT set</p>';
        }
        
        $banner_image = get_option('property_plugin_banner_image');
        if (!empty($banner_image)) {
            echo '<p class="success">✓ Banner image is set: <code class="code">' . esc_url($banner_image) . '</code></p>';
        } else {
            echo '<p class="warning">⚠ Banner image is NOT set</p>';
        }
        
        $header_text = get_option('property_plugin_header_text');
        if (!empty($header_text)) {
            echo '<p class="success">✓ Header text is set: <code class="code">' . esc_html($header_text) . '</code></p>';
        } else {
            echo '<p class="warning">⚠ Header text is NOT set</p>';
        }
        
        // Check 7: Media Library
        echo '<h2>7. Media Library Check</h2>';
        $media_args = array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'posts_per_page' => -1,
        );
        $media_items = get_posts($media_args);
        $property_images = array_filter($media_items, function($media) {
            return $media->post_parent > 0 && get_post_type($media->post_parent) === 'property';
        });
        
        echo '<p>Total media items: <strong>' . count($media_items) . '</strong></p>';
        echo '<p>Property images: <strong>' . count($property_images) . '</strong></p>';
        
        if (count($property_images) > 0) {
            echo '<p class="success">✓ Property images found in media library</p>';
        } else {
            echo '<p class="error">✗ No property images in media library</p>';
            echo '<p class="warning">⚠ Images may not have been sideloaded during import</p>';
        }
        
        // Check 8: Error Log
        echo '<h2>8. Recent Error Log Entries</h2>';
        $debug_log = WP_CONTENT_DIR . '/debug.log';
        if (file_exists($debug_log)) {
            $log_content = file_get_contents($debug_log);
            $property_logs = array_filter(explode("\n", $log_content), function($line) {
                return strpos($line, '[Property Plugin]') !== false;
            });
            $recent_logs = array_slice($property_logs, -20);
            
            if (!empty($recent_logs)) {
                echo '<pre style="background:#f0f0f0; padding:15px; max-height:400px; overflow-y:auto;">';
                foreach (array_reverse($recent_logs) as $log) {
                    echo esc_html($log) . "\n";
                }
                echo '</pre>';
            } else {
                echo '<p class="warning">No Property Plugin entries in debug log</p>';
            }
        } else {
            echo '<p class="warning">Debug log not found. Enable WP_DEBUG_LOG in wp-config.php to see detailed logs.</p>';
        }
        
        // Solutions
        echo '<h2>9. Quick Fixes</h2>';
        echo '<div class="info-box">';
        echo '<h3>If you see errors above, try these steps:</h3>';
        echo '<ol>';
        echo '<li><strong>Deactivate and Reactivate:</strong> Go to Plugins, deactivate Property Plugin, then activate it again</li>';
        echo '<li><strong>Flush Permalinks:</strong> Go to Settings > Permalinks and click "Save Changes"</li>';
        echo '<li><strong>Check PHP Error Log:</strong> Look for errors in your server error log or wp-content/debug.log</li>';
        echo '<li><strong>Enable Debug Mode:</strong> Add to wp-config.php:<br>';
        echo '<code class="code">define(\'WP_DEBUG\', true);</code><br>';
        echo '<code class="code">define(\'WP_DEBUG_LOG\', true);</code></li>';
        echo '<li><strong>Check File Permissions:</strong> Ensure wp-content/uploads is writable (755 or 775)</li>';
        echo '</ol>';
        echo '</div>';
        
        // API Test Link
        echo '<h2>10. Test Links</h2>';
        echo '<ul>';
        echo '<li><a href="' . esc_url($api_url) . '" target="_blank">Test REST API Endpoint</a></li>';
        echo '<li><a href="' . admin_url('edit.php?post_type=property') . '">View Properties in Admin</a></li>';
        echo '<li><a href="' . admin_url('admin.php?page=property-plugin-settings') . '">Plugin Settings</a></li>';
        echo '</ul>';
        ?>
        
        <hr style="margin: 40px 0;">
        <p style="color: #666; font-size: 12px;">Diagnostic tool generated on <?php echo current_time('Y-m-d H:i:s'); ?></p>
    </div>
</body>
</html>
