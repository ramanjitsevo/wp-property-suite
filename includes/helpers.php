<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * General helper functions for WP Property Suite
 */

/**
 * Format price with selected currency
 */
function wps_format_price($price) {
    if (!$price) {
        return 'N/A';
    }
    
    if (preg_match('/[\$€£₹Rs]/', $price)) {
        return $price;
    }
    
    $clean_price = preg_replace('/[^0-9.]/', '', $price);
    $numeric_price = floatval($clean_price);
    if ($numeric_price <= 0) {
        return 'N/A';
    }
    $currency = get_option('wps_default_currency', 'USD');
    $currency_symbols = array(
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'INR' => '₹',        
    );
    $symbol = isset($currency_symbols[$currency]) ? $currency_symbols[$currency] : '$';
    return $symbol . number_format($numeric_price);
}

/**
 * Return the contact email, falling back to the WordPress admin email.
 */
function wps_get_contact_email() {
    $email = get_option('wps_contact_email', '');
    return is_email($email) ? $email : get_option('admin_email');
}
