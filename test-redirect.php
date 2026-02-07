<?php
require_once('wp-load.php');

echo "=== Checking pll_home_url ===\n";

$current_lang = 'el';
$home_url = function_exists('pll_home_url') ? pll_home_url($current_lang) : 'N/A';
echo "pll_home_url('el'): $home_url\n";

$front_page_id = get_option('page_on_front');
echo "Front page ID: $front_page_id\n";

$translated_id = function_exists('pll_get_post') ? pll_get_post($front_page_id, $current_lang) : 0;
echo "Greek front page ID: $translated_id\n";

$page_permalink = get_permalink($translated_id);
echo "Greek page permalink: $page_permalink\n";

echo "\n=== Check redirect_canonical filter ===\n";
global $wp_filter;
if (isset($wp_filter['redirect_canonical'])) {
    foreach ($wp_filter['redirect_canonical']->callbacks as $priority => $callbacks) {
        foreach ($callbacks as $id => $callback) {
            echo "[$priority] " . (is_string($callback['function']) ? $callback['function'] : 'closure') . "\n";
        }
    }
}
