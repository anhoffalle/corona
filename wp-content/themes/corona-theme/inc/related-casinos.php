<?php
/**
 * Related Casinos Block
 *
 * Automatically displays related casino pages (siblings with same parent)
 *
 * @package Corona_Theme
 */

if (!defined('ABSPATH')) exit;

/**
 * Get related casino pages
 */
function corona_get_related_casinos($post_id, $limit = 4) {
    $post = get_post($post_id);
    if (!$post || !$post->post_parent) {
        return array();
    }

    $args = array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_parent' => $post->post_parent,
        'posts_per_page' => $limit + 1,
        'post__not_in' => array($post_id),
        'orderby' => 'rand',
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => '_wp_page_template',
                'value' => 'page-casino.php'
            ),
            array(
                'key' => '_slot_content_type',
                'value' => 'info'
            )
        )
    );

    $related = new WP_Query($args);
    return $related->posts;
}

/**
 * Render related casinos block
 */
function corona_render_related_casinos($post_id) {
    $settings = corona_get_related_settings();

    if (!$settings['enabled']) {
        return '';
    }

    $related = corona_get_related_casinos($post_id, $settings['count']);

    if (empty($related)) {
        return '';
    }

    $title = function_exists('pll__')
        ? pll__('Related Casinos')
        : __('Related Casinos', 'corona-theme');

    ob_start();
    ?>
    <section class="related-casinos">
        <h2 class="related-casinos-title"><?php echo esc_html($title); ?></h2>
        <div class="related-casinos-grid">
            <?php foreach ($related as $casino):
                $logo = get_post_meta($casino->ID, '_casino_logo', true);
                if (!$logo) {
                    $logo = get_post_meta($casino->ID, '_slot_image', true);
                }
                $rating = get_post_meta($casino->ID, '_casino_rating', true) ?: 5;

                $bonuses = get_post_meta($casino->ID, '_casino_bonuses', true);
                $bonuses = $bonuses ? json_decode($bonuses, true) : array();
                $first_bonus = !empty($bonuses) ? $bonuses[0] : null;

                if (!$first_bonus) {
                    $chars = get_post_meta($casino->ID, '_slot_custom_characteristics', true);
                    $chars = $chars ? json_decode($chars, true) : array();
                    foreach ($chars as $char) {
                        if (stripos($char['label'], 'bono') !== false || stripos($char['label'], 'bonus') !== false) {
                            $first_bonus = array('description' => $char['value']);
                            break;
                        }
                    }
                }
            ?>
                <a href="<?php echo get_permalink($casino->ID); ?>" class="related-casino-card">
                    <?php if ($settings['show_logo']): ?>
                        <?php if ($logo): ?>
                            <div class="related-casino-logo">
                                <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($casino->post_title); ?>">
                            </div>
                        <?php else: ?>
                            <div class="related-casino-logo related-casino-logo-placeholder">
                                <span><?php echo esc_html(mb_substr($casino->post_title, 0, 2)); ?></span>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="related-casino-info">
                        <h3 class="related-casino-name"><?php echo esc_html($casino->post_title); ?></h3>

                        <?php if ($settings['show_rating'] && $rating): ?>
                            <div class="related-casino-rating">
                                <?php echo str_repeat('★', $rating) . str_repeat('☆', 5 - $rating); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($settings['show_bonus'] && $first_bonus && !empty($first_bonus['description'])): ?>
                            <div class="related-casino-bonus">
                                <?php echo esc_html($first_bonus['description']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Auto-append related casinos to casino pages
 */
function corona_append_related_casinos($content) {
    if (!is_singular('page') || !is_main_query()) {
        return $content;
    }

    $template = get_page_template_slug();
    $content_type = get_post_meta(get_the_ID(), '_slot_content_type', true);

    if ($template === 'page-casino.php' || $content_type === 'info') {
        $related = corona_render_related_casinos(get_the_ID());
        $content .= $related;
    }

    return $content;
}
add_filter('the_content', 'corona_append_related_casinos', 20);

/**
 * Register related casinos string for Polylang
 */
function corona_register_related_strings() {
    if (function_exists('pll_register_string')) {
        pll_register_string('related_casinos', 'Related Casinos', 'Corona Theme');
    }
}
add_action('init', 'corona_register_related_strings');
