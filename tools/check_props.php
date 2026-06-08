<?php
require '/var/www/html/vaidhakim/wp-load.php';
$c = wp_count_posts('property');
echo 'published:' . intval($c->publish ?? 0) . "\n";
?>
