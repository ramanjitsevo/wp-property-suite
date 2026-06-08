<?php
require '/var/www/html/vaidhakim/wp-load.php';

echo "option property_plugin_default_data_installed: ";
var_export(get_option('property_plugin_default_data_installed'));
echo "\n";

$c = wp_count_posts('property');
echo 'published: ' . intval($c->publish ?? 0) . "\n";

$props = get_posts(array('post_type'=>'property','posts_per_page'=>10,'post_status'=>'publish','orderby'=>'date','order'=>'DESC'));
foreach ($props as $p) {
    echo sprintf("ID:%d Title:%s Date:%s\n", $p->ID, $p->post_title, $p->post_date);
}

// Show option values for banner and agent name
echo "banner_image option: " . get_option('property_plugin_banner_image','(none)') . "\n";
echo "agent_name option: " . get_option('property_plugin_agent_name','(none)') . "\n";
