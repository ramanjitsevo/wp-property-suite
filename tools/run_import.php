<?php
// Small runner to trigger the plugin's default data installer.
require '/var/www/html/vaidhakim/wp-load.php';

if (function_exists('property_plugin_install_default_data')) {
    try {
        property_plugin_install_default_data();
        echo "IMPORT_OK\n";
    } catch (Throwable $e) {
        echo "EXCEPTION: " . $e->getMessage() . "\n";
    }
} else {
    echo "NO_FUNC\n";
}
