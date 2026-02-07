<?php
/**
 * Corona Theme Customizer Settings
 *
 * Global settings for the theme
 *
 * @package Corona_Theme
 */

if (!defined('ABSPATH')) exit;

/**
 * Add Customizer settings
 */
function corona_theme_customizer($wp_customize) {

    // ==========================================
    // SECTION: Global Affiliate Settings
    // ==========================================
    $wp_customize->add_section('corona_affiliate_section', array(
        'title' => __('Affiliate Settings', 'corona-theme'),
        'priority' => 100,
    ));

    // Default Affiliate Link
    $wp_customize->add_setting('corona_default_aff_link', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('corona_default_aff_link', array(
        'label' => __('Default Affiliate Link', 'corona-theme'),
        'description' => __('Used when casino page has no specific affiliate link', 'corona-theme'),
        'section' => 'corona_affiliate_section',
        'type' => 'url',
    ));

    // Default Button Text
    $wp_customize->add_setting('corona_default_btn_text', array(
        'default' => 'Play Now',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('corona_default_btn_text', array(
        'label' => __('Default Button Text', 'corona-theme'),
        'section' => 'corona_affiliate_section',
        'type' => 'text',
    ));

    // Default Bonus Button Text
    $wp_customize->add_setting('corona_default_bonus_btn_text', array(
        'default' => 'Claim offer and play',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('corona_default_bonus_btn_text', array(
        'label' => __('Default Bonus Button Text', 'corona-theme'),
        'section' => 'corona_affiliate_section',
        'type' => 'text',
    ));

    // App Button Text
    $wp_customize->add_setting('corona_app_btn_text', array(
        'default' => 'Get',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('corona_app_btn_text', array(
        'label' => __('App Button Text', 'corona-theme'),
        'description' => __('Button text on app pages', 'corona-theme'),
        'section' => 'corona_affiliate_section',
        'type' => 'text',
    ));

    // App Purchases Text
    $wp_customize->add_setting('corona_app_purchases_text', array(
        'default' => 'In-App Purchases',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('corona_app_purchases_text', array(
        'label' => __('App Purchases Text', 'corona-theme'),
        'description' => __('Text shown below the button', 'corona-theme'),
        'section' => 'corona_affiliate_section',
        'type' => 'text',
    ));

    // ==========================================
    // SECTION: Header Settings
    // ==========================================
    $wp_customize->add_section('corona_header_section', array(
        'title' => __('Header Settings', 'corona-theme'),
        'priority' => 105,
    ));

    // Header CTA Text
    $wp_customize->add_setting('corona_header_cta_text', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('corona_header_cta_text', array(
        'label' => __('CTA Button Text', 'corona-theme'),
        'description' => __('Leave empty to hide the button', 'corona-theme'),
        'section' => 'corona_header_section',
        'type' => 'text',
    ));

    // Header CTA URL
    $wp_customize->add_setting('corona_header_cta_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('corona_header_cta_url', array(
        'label' => __('CTA Button URL', 'corona-theme'),
        'section' => 'corona_header_section',
        'type' => 'url',
    ));

    // ==========================================
    // SECTION: Footer Settings
    // ==========================================
    $wp_customize->add_section('corona_footer_section', array(
        'title' => __('Footer Settings', 'corona-theme'),
        'priority' => 110,
    ));

    // Footer Description
    $wp_customize->add_setting('corona_footer_description', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('corona_footer_description', array(
        'label' => __('Footer Description', 'corona-theme'),
        'description' => __('Short text about your site', 'corona-theme'),
        'section' => 'corona_footer_section',
        'type' => 'textarea',
    ));

    // Responsible Gaming Text
    $wp_customize->add_setting('corona_responsible_gaming', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('corona_responsible_gaming', array(
        'label' => __('Responsible Gaming Notice', 'corona-theme'),
        'description' => __('Displays as a warning box. HTML allowed.', 'corona-theme'),
        'section' => 'corona_footer_section',
        'type' => 'textarea',
    ));

    // Age Restriction Badge
    $wp_customize->add_setting('corona_age_restriction', array(
        'default' => '18+',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('corona_age_restriction', array(
        'label' => __('Age Restriction Badge', 'corona-theme'),
        'description' => __('e.g. 18+, 21+', 'corona-theme'),
        'section' => 'corona_footer_section',
        'type' => 'text',
    ));

    // Social Links
    $wp_customize->add_setting('corona_social_twitter', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('corona_social_twitter', array(
        'label' => __('Twitter/X URL', 'corona-theme'),
        'section' => 'corona_footer_section',
        'type' => 'url',
    ));

    $wp_customize->add_setting('corona_social_facebook', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('corona_social_facebook', array(
        'label' => __('Facebook URL', 'corona-theme'),
        'section' => 'corona_footer_section',
        'type' => 'url',
    ));

    $wp_customize->add_setting('corona_social_instagram', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('corona_social_instagram', array(
        'label' => __('Instagram URL', 'corona-theme'),
        'section' => 'corona_footer_section',
        'type' => 'url',
    ));

    $wp_customize->add_setting('corona_social_telegram', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('corona_social_telegram', array(
        'label' => __('Telegram URL', 'corona-theme'),
        'section' => 'corona_footer_section',
        'type' => 'url',
    ));

    // ==========================================
    // SECTION: App Page Defaults
    // ==========================================
    $wp_customize->add_section('corona_app_section', array(
        'title' => __('App Page Settings', 'corona-theme'),
        'priority' => 115,
    ));

    // Default App Screenshots
    $wp_customize->add_setting('corona_default_app_screenshots', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('corona_default_app_screenshots', array(
        'label' => __('Default App Screenshots', 'corona-theme'),
        'description' => __('Enter screenshot URLs, one per line. These will be used when "Use Default Screenshots" is enabled on app pages.', 'corona-theme'),
        'section' => 'corona_app_section',
        'type' => 'textarea',
    ));

    // Default Developer Name
    $wp_customize->add_setting('corona_default_app_developer', array(
        'default' => 'Games Ltd.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('corona_default_app_developer', array(
        'label' => __('Default Developer Name', 'corona-theme'),
        'section' => 'corona_app_section',
        'type' => 'text',
    ));

    // Default App Rating
    $wp_customize->add_setting('corona_default_app_rating', array(
        'default' => '4.8',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('corona_default_app_rating', array(
        'label' => __('Default App Rating', 'corona-theme'),
        'section' => 'corona_app_section',
        'type' => 'text',
    ));

    // ==========================================
    // SECTION: Related Casinos
    // ==========================================
    $wp_customize->add_section('corona_related_section', array(
        'title' => __('Related Casinos', 'corona-theme'),
        'priority' => 120,
    ));

    // Enable/Disable
    $wp_customize->add_setting('corona_related_enabled', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('corona_related_enabled', array(
        'label' => __('Show Related Casinos block', 'corona-theme'),
        'section' => 'corona_related_section',
        'type' => 'checkbox',
    ));

    // Show Logo
    $wp_customize->add_setting('corona_related_show_logo', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('corona_related_show_logo', array(
        'label' => __('Show Logo', 'corona-theme'),
        'section' => 'corona_related_section',
        'type' => 'checkbox',
    ));

    // Show Rating
    $wp_customize->add_setting('corona_related_show_rating', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('corona_related_show_rating', array(
        'label' => __('Show Rating', 'corona-theme'),
        'section' => 'corona_related_section',
        'type' => 'checkbox',
    ));

    // Show Bonus
    $wp_customize->add_setting('corona_related_show_bonus', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('corona_related_show_bonus', array(
        'label' => __('Show Bonus', 'corona-theme'),
        'section' => 'corona_related_section',
        'type' => 'checkbox',
    ));

    // Count
    $wp_customize->add_setting('corona_related_count', array(
        'default' => 4,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('corona_related_count', array(
        'label' => __('Number of casinos to show', 'corona-theme'),
        'section' => 'corona_related_section',
        'type' => 'select',
        'choices' => array(
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            8 => '8',
        ),
    ));
}
add_action('customize_register', 'corona_theme_customizer');

/**
 * Get default affiliate link
 */
function corona_get_default_aff_link() {
    return get_theme_mod('corona_default_aff_link', '');
}

/**
 * Get default button text
 */
function corona_get_default_btn_text() {
    return get_theme_mod('corona_default_btn_text', 'Play Now');
}

/**
 * Get default bonus button text
 */
function corona_get_default_bonus_btn_text() {
    return get_theme_mod('corona_default_bonus_btn_text', 'Claim offer and play');
}

/**
 * Get app button text
 */
function corona_get_app_btn_text() {
    $text = get_theme_mod('corona_app_btn_text', 'Get');
    return function_exists('pll__') ? pll__($text) : $text;
}

/**
 * Get app purchases text
 */
function corona_get_app_purchases_text() {
    $text = get_theme_mod('corona_app_purchases_text', 'In-App Purchases');
    return function_exists('pll__') ? pll__($text) : $text;
}

/**
 * Get affiliate link for a casino (with fallback to global)
 */
function corona_get_casino_aff_link($post_id) {
    $link = get_post_meta($post_id, '_casino_aff_link', true);
    if (empty($link)) {
        $link = corona_get_default_aff_link();
    }
    return $link;
}

/**
 * Get related casinos settings
 */
function corona_get_related_settings() {
    return array(
        'enabled' => get_theme_mod('corona_related_enabled', true),
        'show_logo' => get_theme_mod('corona_related_show_logo', true),
        'show_rating' => get_theme_mod('corona_related_show_rating', true),
        'show_bonus' => get_theme_mod('corona_related_show_bonus', true),
        'count' => get_theme_mod('corona_related_count', 4),
    );
}

/**
 * Get default app screenshots
 */
function corona_get_default_app_screenshots() {
    $screenshots_text = get_theme_mod('corona_default_app_screenshots', '');
    if (empty($screenshots_text)) {
        return array();
    }
    $lines = explode("\n", $screenshots_text);
    $screenshots = array();
    foreach ($lines as $line) {
        $url = trim($line);
        if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
            $screenshots[] = $url;
        }
    }
    return $screenshots;
}

/**
 * Get default app developer name
 */
function corona_get_default_app_developer() {
    return get_theme_mod('corona_default_app_developer', 'Games Ltd.');
}

/**
 * Get default app rating
 */
function corona_get_default_app_rating() {
    return get_theme_mod('corona_default_app_rating', '4.8');
}
