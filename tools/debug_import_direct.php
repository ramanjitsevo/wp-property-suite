<?php
require '/var/www/html/vaidhakim/wp-load.php';

$props_file = __DIR__ . '/../data/default-properties.json';
if (!file_exists($props_file)) { echo "missing props file\n"; exit; }
$props = json_decode(file_get_contents($props_file), true);
if (!is_array($props)) { echo "invalid json\n"; exit; }

foreach ($props as $p) {
    $title = sanitize_text_field($p['title'] ?? 'Demo');
    $existing = get_page_by_title($title, OBJECT, 'property');
    if ($existing) { echo "exists: " . $title . " (ID: " . $existing->ID . ")\n"; continue; }
    $post_arr = array(
        'post_type' => 'property',
        'post_title' => $title,
        'post_content' => wp_kses_post($p['content'] ?? ''),
        'post_excerpt' => sanitize_text_field($p['excerpt'] ?? ''),
        'post_status' => 'publish',
    );
    $post_id = wp_insert_post($post_arr, true);
    if (is_wp_error($post_id)) {
        echo "ERROR inserting: " . $title . " => " . $post_id->get_error_message() . "\n";
    } else {
        echo "INSERTED ID:" . $post_id . " Title:" . $title . "\n";
    }
}

$c = wp_count_posts('property');
echo 'published now: ' . intval($c->publish ?? 0) . "\n";
