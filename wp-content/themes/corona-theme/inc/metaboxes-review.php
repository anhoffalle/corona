<?php
/**
 * Top Casinos List Metaboxes
 *
 * Fields:
 * - Page title
 * - Page subtitle
 * - Casino list selection
 * - Casino tags (optional per casino)
 */

if (!defined('ABSPATH')) exit;

/**
 * Register review page metaboxes
 */
function corona_register_review_metaboxes() {
    add_meta_box(
        'review_page_settings',
        'Top Casinos List Settings',
        'corona_review_settings_metabox',
        'page',
        'normal',
        'high'
    );
}

/**
 * Check if current page uses review template
 */
function corona_is_review_template() {
    global $post;
    if (!$post) return false;
    return get_page_template_slug($post->ID) === 'page-review.php';
}

/**
 * Conditionally add metaboxes
 */
function corona_add_review_metaboxes() {
    if (corona_is_review_template()) {
        corona_register_review_metaboxes();
    }
}
add_action('add_meta_boxes', 'corona_add_review_metaboxes');

/**
 * Review settings metabox
 */
function corona_review_settings_metabox($post) {
    wp_nonce_field('corona_review_meta', 'corona_review_nonce');

    $page_title = get_post_meta($post->ID, '_review_page_title', true);
    $page_subtitle = get_post_meta($post->ID, '_review_page_subtitle', true);
    $casino_ids = get_post_meta($post->ID, '_review_casino_list', true);
    $casino_ids = $casino_ids ? json_decode($casino_ids, true) : array();

    // Get casino pages filtered by language (Polylang support)
    $current_lang = function_exists('pll_get_post_language') ? pll_get_post_language($post->ID) : null;

    // Use WP_Query instead of get_pages (get_pages doesn't work well with meta filters)
    $query_args = array(
        'post_type' => 'page',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-casino.php',
    );

    $casino_query = new WP_Query($query_args);
    $all_casino_pages = $casino_query->posts;

    // Filter by language if Polylang is active
    $casino_pages = array();
    if ($current_lang && function_exists('pll_get_post_language')) {
        foreach ($all_casino_pages as $page) {
            $page_lang = pll_get_post_language($page->ID);
            if ($page_lang === $current_lang) {
                $casino_pages[] = $page;
            }
        }
    } else {
        $casino_pages = $all_casino_pages;
    }
    ?>

    <table class="form-table">
        <tr>
            <th><label for="review_page_title">Page Title</label></th>
            <td>
                <input type="text" id="review_page_title" name="review_page_title" value="<?php echo esc_attr($page_title); ?>" class="large-text" placeholder="Top Rated Casinos 2024">
                <p class="description">Leave empty for default. First two words will be normal, third word will be underlined (e.g. "Top Rated <u>Casinos</u> 2024")</p>
            </td>
        </tr>
        <tr>
            <th><label for="review_page_subtitle">Page Subtitle</label></th>
            <td>
                <textarea id="review_page_subtitle" name="review_page_subtitle" class="large-text" rows="2" placeholder="Our experts evaluate hundreds of online casinos..."><?php echo esc_textarea($page_subtitle); ?></textarea>
            </td>
        </tr>
    </table>

    <h3 style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">Casino List</h3>
    <p class="description" style="margin-bottom: 15px;">Select and order casinos to display. Leave empty to show all casino pages automatically.</p>

    <div id="review-casino-list" style="margin-bottom: 20px;">
        <?php if (!empty($casino_ids)): ?>
            <?php foreach ($casino_ids as $casino_id):
                $casino = get_post($casino_id);
                if (!$casino) continue;
            ?>
                <div class="review-casino-item" data-id="<?php echo esc_attr($casino_id); ?>" style="display: flex; align-items: center; gap: 10px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 5px; cursor: move;">
                    <span class="dashicons dashicons-menu" style="color: #999;"></span>
                    <span style="flex: 1;"><?php echo esc_html($casino->post_title); ?></span>
                    <button type="button" class="button button-small remove-review-casino">Remove</button>
                    <input type="hidden" name="review_casino_ids[]" value="<?php echo esc_attr($casino_id); ?>">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        <select id="add-review-casino-select" class="regular-text">
            <option value="">-- Select casino to add --</option>
            <?php foreach ($casino_pages as $casino): ?>
                <option value="<?php echo esc_attr($casino->ID); ?>" data-title="<?php echo esc_attr(wp_strip_all_tags($casino->post_title)); ?>">
                    <?php echo esc_html($casino->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="button" class="button" id="add-review-casino">Add Casino</button>
    </div>

    <style>
        #review-casino-list .review-casino-item.ui-sortable-placeholder {
            visibility: visible !important;
            background: #e0e0e0;
            border: 2px dashed #999;
        }
        #review-casino-list .review-casino-item.ui-sortable-helper {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Make list sortable
        $('#review-casino-list').sortable({
            placeholder: 'review-casino-item ui-sortable-placeholder',
            handle: '.dashicons-menu'
        });

        // Add casino
        $('#add-review-casino').click(function() {
            var select = $('#add-review-casino-select');
            var id = select.val();
            var title = select.find('option:selected').data('title');

            if (!id) return;

            // Check if already added
            if ($('.review-casino-item[data-id="' + id + '"]').length) {
                alert('This casino is already in the list');
                return;
            }

            var $item = $('<div/>', {
                'class': 'review-casino-item',
                'data-id': id,
                style: 'display: flex; align-items: center; gap: 10px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 5px; cursor: move;'
            });
            $item.append($('<span/>', {
                'class': 'dashicons dashicons-menu',
                style: 'color: #999;'
            }));
            $item.append($('<span/>', {
                style: 'flex: 1;',
                text: title
            }));
            $item.append($('<button/>', {
                type: 'button',
                'class': 'button button-small remove-review-casino',
                text: 'Remove'
            }));
            $item.append($('<input/>', {
                type: 'hidden',
                name: 'review_casino_ids[]',
                value: id
            }));

            $('#review-casino-list').append($item);
            select.val('');
        });

        // Remove casino
        $(document).on('click', '.remove-review-casino', function() {
            $(this).closest('.review-casino-item').remove();
        });
    });
    </script>
    <?php
}

/**
 * Save review page meta
 */
function corona_save_review_meta($post_id) {
    // Verify nonce
    if (!isset($_POST['corona_review_nonce']) || !wp_verify_nonce($_POST['corona_review_nonce'], 'corona_review_meta')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check if review template
    if (get_page_template_slug($post_id) !== 'page-review.php') {
        return;
    }

    // Save page title
    if (isset($_POST['review_page_title'])) {
        update_post_meta($post_id, '_review_page_title', sanitize_text_field($_POST['review_page_title']));
    }

    // Save page subtitle
    if (isset($_POST['review_page_subtitle'])) {
        update_post_meta($post_id, '_review_page_subtitle', sanitize_textarea_field($_POST['review_page_subtitle']));
    }

    // Save casino list
    if (isset($_POST['review_casino_ids'])) {
        $casino_ids = array_map('intval', $_POST['review_casino_ids']);
        update_post_meta($post_id, '_review_casino_list', json_encode($casino_ids));
    } else {
        update_post_meta($post_id, '_review_casino_list', json_encode(array()));
    }
}
add_action('save_post', 'corona_save_review_meta');

/**
 * Enqueue admin scripts for review metabox
 */
function corona_review_admin_scripts($hook) {
    if ($hook !== 'post-new.php' && $hook !== 'post.php') {
        return;
    }

    global $post;
    if (!$post || get_page_template_slug($post->ID) !== 'page-review.php') {
        // Also load for new pages that might be assigned this template
        if ($hook === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'page') {
            wp_enqueue_script('jquery-ui-sortable');
        }
        return;
    }

    wp_enqueue_script('jquery-ui-sortable');
}
add_action('admin_enqueue_scripts', 'corona_review_admin_scripts');
