<?php
/**
 * Game Page Metaboxes
 *
 * Fields:
 * - Game image
 * - Demo iframe URL
 * - Characteristics (dynamic table)
 * - Affiliate buttons (up to 3)
 */

if (!defined('ABSPATH')) exit;

/**
 * Register game metaboxes
 */
function corona_register_game_metaboxes() {
    add_meta_box(
        'game_main_info',
        __('Game Information', 'corona-theme'),
        'corona_game_main_metabox',
        'page',
        'normal',
        'high'
    );

    add_meta_box(
        'game_casinos',
        __('Where to Play (Casinos)', 'corona-theme'),
        'corona_game_casinos_metabox',
        'page',
        'normal',
        'default'
    );
}

/**
 * Check if current page uses game template
 */
function corona_is_game_template() {
    global $post;
    if (!$post) return false;
    return get_page_template_slug($post->ID) === 'page-game.php';
}

/**
 * Conditionally add metaboxes
 */
function corona_add_game_metaboxes() {
    if (corona_is_game_template()) {
        corona_register_game_metaboxes();
    }
}
add_action('add_meta_boxes', 'corona_add_game_metaboxes');

/**
 * Main game info metabox
 */
function corona_game_main_metabox($post) {
    wp_nonce_field('corona_game_meta', 'corona_game_nonce');

    $image = get_post_meta($post->ID, '_game_image', true);
    $demo_url = get_post_meta($post->ID, '_game_demo_url', true);
    $characteristics = get_post_meta($post->ID, '_game_characteristics', true);
    $characteristics = $characteristics ? json_decode($characteristics, true) : array();

    // Migrate old data if exists
    if (empty($characteristics)) {
        $old_chars = get_post_meta($post->ID, '_slot_custom_characteristics', true);
        if ($old_chars) {
            $characteristics = json_decode($old_chars, true) ?: array();
        }
    }

    // Migrate old image
    if (empty($image)) {
        $old_image = get_post_meta($post->ID, '_slot_image', true);
        if ($old_image) {
            $image = $old_image;
        }
    }

    // Migrate old demo URL
    if (empty($demo_url)) {
        $old_demo = get_post_meta($post->ID, '_slot_demo_iframe', true);
        if ($old_demo) {
            $demo_url = $old_demo;
        }
    }

    // Default characteristics for games
    if (empty($characteristics)) {
        $characteristics = array(
            array('label' => __('Provider', 'corona-theme'), 'value' => ''),
            array('label' => __('RTP', 'corona-theme'), 'value' => ''),
            array('label' => __('Volatility', 'corona-theme'), 'value' => ''),
            array('label' => __('Max Win', 'corona-theme'), 'value' => ''),
            array('label' => __('Release Date', 'corona-theme'), 'value' => '')
        );
    }
    ?>

    <table class="form-table">
        <!-- Game Image -->
        <tr>
            <th><label for="game_image"><?php esc_html_e('Game Image', 'corona-theme'); ?></label></th>
            <td>
                <div class="game-image-upload">
                    <input type="hidden" id="game_image" name="game_image" value="<?php echo esc_url($image); ?>">
                    <div class="image-preview" id="game-image-preview">
                        <?php if ($image): ?>
                            <img src="<?php echo esc_url($image); ?>" style="max-width: 300px; height: auto; border-radius: 8px;">
                        <?php else: ?>
                            <div style="width: 300px; height: 180px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 8px; color: #666;">
                                <?php esc_html_e('No image selected', 'corona-theme'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <p style="margin: 10px 0;">
                        <button type="button" class="button" id="upload-game-image-btn"><?php esc_html_e('Select Image', 'corona-theme'); ?></button>
                        <button type="button" class="button" id="remove-game-image-btn" <?php echo !$image ? 'style="display:none;"' : ''; ?>><?php esc_html_e('Remove', 'corona-theme'); ?></button>
                    </p>
                </div>
            </td>
        </tr>

        <!-- Demo URL -->
        <tr>
            <th><label for="game_demo_url"><?php esc_html_e('Demo Iframe URL', 'corona-theme'); ?></label></th>
            <td>
                <input type="url" id="game_demo_url" name="game_demo_url" value="<?php echo esc_url($demo_url); ?>" class="large-text">
                <p class="description"><?php esc_html_e('URL for the game demo iframe', 'corona-theme'); ?></p>
            </td>
        </tr>
    </table>

    <!-- Characteristics -->
    <h3 style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;"><?php esc_html_e('Game Characteristics', 'corona-theme'); ?></h3>
    <div id="game-characteristics-container">
        <?php foreach ($characteristics as $index => $char): ?>
            <div class="game-char-row" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                <input type="text" name="game_char_labels[]" value="<?php echo esc_attr($char['label']); ?>" placeholder="<?php echo esc_attr__('Label (e.g. RTP)', 'corona-theme'); ?>" class="regular-text">
                <input type="text" name="game_char_values[]" value="<?php echo esc_attr($char['value']); ?>" placeholder="<?php echo esc_attr__('Value (e.g. 96.5%)', 'corona-theme'); ?>" class="regular-text">
                <button type="button" class="button remove-game-char"><?php esc_html_e('Remove', 'corona-theme'); ?></button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="button button-primary" id="add-game-char"><?php esc_html_e('+ Add Characteristic', 'corona-theme'); ?></button>

    <script>
    jQuery(document).ready(function($) {
        var coronaI18n = window.CORONA_I18N || {};

        // Image upload
        var gameMediaUploader;
        $('#upload-game-image-btn').click(function(e) {
            e.preventDefault();
            if (gameMediaUploader) {
                gameMediaUploader.open();
                return;
            }
            gameMediaUploader = wp.media({
                title: coronaI18n.selectGameImage || '',
                button: { text: coronaI18n.useThisImage || '' },
                multiple: false
            });
            gameMediaUploader.on('select', function() {
                var attachment = gameMediaUploader.state().get('selection').first().toJSON();
                $('#game_image').val(attachment.url);
                $('#game-image-preview').html('<img src="' + attachment.url + '" style="max-width: 300px; height: auto; border-radius: 8px;">');
                $('#remove-game-image-btn').show();
            });
            gameMediaUploader.open();
        });

        $('#remove-game-image-btn').click(function(e) {
            e.preventDefault();
            $('#game_image').val('');
            $('#game-image-preview').html('<div style="width: 300px; height: 180px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 8px; color: #666;">' + (coronaI18n.noImageSelected || '') + '</div>');
            $(this).hide();
        });

        // Characteristics
        $('#add-game-char').click(function() {
            var row = '<div class="game-char-row" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">' +
                '<input type="text" name="game_char_labels[]" value="" placeholder="' + (coronaI18n.labelRtpPlaceholder || '') + '" class="regular-text">' +
                '<input type="text" name="game_char_values[]" value="" placeholder="' + (coronaI18n.valueRtpPlaceholder || '') + '" class="regular-text">' +
                '<button type="button" class="button remove-game-char">' + (coronaI18n.remove || '') + '</button>' +
                '</div>';
            $('#game-characteristics-container').append(row);
        });

        $(document).on('click', '.remove-game-char', function() {
            if ($('.game-char-row').length > 1) {
                $(this).closest('.game-char-row').remove();
            }
        });
    });
    </script>
    <?php
}

/**
 * Casinos selector metabox
 */
function corona_game_casinos_metabox($post) {
    $selected_casinos = get_post_meta($post->ID, '_game_casinos', true);
    $selected_casinos = $selected_casinos ? json_decode($selected_casinos, true) : array();

    // Get custom URLs for casinos
    $casino_urls = get_post_meta($post->ID, '_game_casino_urls', true);
    $casino_urls = $casino_urls ? json_decode($casino_urls, true) : array();

    // Get current language
    $current_lang = function_exists('pll_get_post_language') ? pll_get_post_language($post->ID) : null;

    // Get all casino pages
    $args = array(
        'post_type' => 'page',
        'posts_per_page' => 100,
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-casino.php',
        'orderby' => 'title',
        'order' => 'ASC',
    );
    $casino_query = new WP_Query($args);
    $all_casinos = $casino_query->posts;

    // Filter by language if Polylang is active
    if ($current_lang && function_exists('pll_get_post_language')) {
        $casinos = array();
        foreach ($all_casinos as $casino) {
            if (pll_get_post_language($casino->ID) === $current_lang) {
                $casinos[] = $casino;
            }
        }
    } else {
        $casinos = $all_casinos;
    }
    ?>
    <p class="description" style="margin-bottom: 15px;"><?php esc_html_e('Select casinos and optionally add custom landing page URLs for this specific game.', 'corona-theme'); ?></p>

    <div id="game-casino-list" style="margin-bottom: 20px;">
        <?php if (!empty($selected_casinos)): ?>
            <?php foreach ($selected_casinos as $casino_id):
                $casino = get_post($casino_id);
                if (!$casino) continue;
                $custom_url = isset($casino_urls[$casino_id]) ? $casino_urls[$casino_id] : '';
            ?>
                <div class="game-casino-item" data-id="<?php echo esc_attr($casino_id); ?>" style="background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 8px; overflow: hidden;">
                    <div style="display: flex; align-items: center; gap: 10px; padding: 10px; cursor: move;">
                        <span class="dashicons dashicons-menu" style="color: #999;"></span>
                        <span style="flex: 1; font-weight: 500;"><?php echo esc_html($casino->post_title); ?></span>
                        <button type="button" class="button button-small remove-game-casino"><?php esc_html_e('Remove', 'corona-theme'); ?></button>
                    </div>
                    <div style="padding: 0 10px 10px 10px;">
                        <input type="url" name="game_casino_urls[<?php echo esc_attr($casino_id); ?>]" value="<?php echo esc_url($custom_url); ?>" placeholder="<?php echo esc_attr__('Custom URL (leave empty to use default)', 'corona-theme'); ?>" class="large-text" style="font-size: 12px;">
                    </div>
                    <input type="hidden" name="game_casinos[]" value="<?php echo esc_attr($casino_id); ?>">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        <select id="add-game-casino-select" class="regular-text">
            <option value=""><?php esc_html_e('-- Select casino to add --', 'corona-theme'); ?></option>
            <?php foreach ($casinos as $casino): ?>
                <option value="<?php echo esc_attr($casino->ID); ?>" data-title="<?php echo esc_attr(wp_strip_all_tags($casino->post_title)); ?>">
                    <?php echo esc_html($casino->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="button" class="button" id="add-game-casino"><?php esc_html_e('Add Casino', 'corona-theme'); ?></button>
    </div>

    <?php if (empty($casinos)): ?>
    <p class="description" style="color: #d63638; margin-top: 10px;"><?php esc_html_e('No casino pages found. Create casino pages using the "Casino Review" template first.', 'corona-theme'); ?></p>
    <?php endif; ?>

    <style>
        #game-casino-list .game-casino-item.ui-sortable-placeholder {
            visibility: visible !important;
            background: #e0e0e0;
            border: 2px dashed #999;
        }
        #game-casino-list .game-casino-item.ui-sortable-helper {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        var coronaI18n = window.CORONA_I18N || {};

        // Make list sortable
        if ($.fn.sortable) {
            $('#game-casino-list').sortable({
                placeholder: 'game-casino-item ui-sortable-placeholder',
                handle: '.dashicons-menu'
            });
        }

        // Add casino
        $('#add-game-casino').click(function() {
            var select = $('#add-game-casino-select');
            var id = select.val();
            var title = select.find('option:selected').data('title');

            if (!id) return;

            // Check if already added
            if ($('.game-casino-item[data-id="' + id + '"]').length) {
                alert(coronaI18n.alreadyInListCasino || '');
                return;
            }

            var $item = $('<div/>', {
                'class': 'game-casino-item',
                'data-id': id,
                style: 'background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 8px; overflow: hidden;'
            });
            var $header = $('<div/>', {
                style: 'display: flex; align-items: center; gap: 10px; padding: 10px; cursor: move;'
            });
            $header.append($('<span/>', {
                'class': 'dashicons dashicons-menu',
                style: 'color: #999;'
            }));
            $header.append($('<span/>', {
                style: 'flex: 1; font-weight: 500;',
                text: title
            }));
            $header.append($('<button/>', {
                type: 'button',
                'class': 'button button-small remove-game-casino',
                text: coronaI18n.remove || ''
            }));
            $item.append($header);
            var $urlWrap = $('<div/>', {
                style: 'padding: 0 10px 10px 10px;'
            });
            $urlWrap.append($('<input/>', {
                type: 'url',
                name: 'game_casino_urls[' + id + ']',
                value: '',
                placeholder: coronaI18n.customUrlPlaceholder || '',
                'class': 'large-text',
                style: 'font-size: 12px;'
            }));
            $item.append($urlWrap);
            $item.append($('<input/>', {
                type: 'hidden',
                name: 'game_casinos[]',
                value: id
            }));

            $('#game-casino-list').append($item);
            select.val('');
        });

        // Remove casino
        $(document).on('click', '.remove-game-casino', function() {
            $(this).closest('.game-casino-item').remove();
        });
    });
    </script>
    <?php
}

/**
 * Save game meta
 */
function corona_save_game_meta($post_id) {
    // Verify nonce
    if (!isset($_POST['corona_game_nonce']) || !wp_verify_nonce($_POST['corona_game_nonce'], 'corona_game_meta')) {
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

    // Check if game template
    if (get_page_template_slug($post_id) !== 'page-game.php') {
        return;
    }

    // Save image
    if (isset($_POST['game_image'])) {
        update_post_meta($post_id, '_game_image', esc_url_raw($_POST['game_image']));
    }

    // Save demo URL
    if (isset($_POST['game_demo_url'])) {
        update_post_meta($post_id, '_game_demo_url', esc_url_raw($_POST['game_demo_url']));
    }

    // Save characteristics
    if (isset($_POST['game_char_labels']) && isset($_POST['game_char_values'])) {
        $labels = array_map('sanitize_text_field', $_POST['game_char_labels']);
        $values = array_map('sanitize_text_field', $_POST['game_char_values']);
        $characteristics = array();
        for ($i = 0; $i < count($labels); $i++) {
            if (!empty($labels[$i]) || !empty($values[$i])) {
                $characteristics[] = array(
                    'label' => $labels[$i],
                    'value' => $values[$i]
                );
            }
        }
        update_post_meta($post_id, '_game_characteristics', json_encode($characteristics, JSON_UNESCAPED_UNICODE));
    }

    // Save casinos
    if (isset($_POST['game_casinos'])) {
        $casinos = array_map('absint', $_POST['game_casinos']);
        $casinos = array_filter($casinos);
        update_post_meta($post_id, '_game_casinos', json_encode($casinos));
    } else {
        update_post_meta($post_id, '_game_casinos', json_encode(array()));
    }

    // Save custom casino URLs
    if (isset($_POST['game_casino_urls']) && is_array($_POST['game_casino_urls'])) {
        $urls = array();
        foreach ($_POST['game_casino_urls'] as $casino_id => $url) {
            $casino_id = absint($casino_id);
            $url = esc_url_raw($url);
            if ($casino_id && $url) {
                $urls[$casino_id] = $url;
            }
        }
        update_post_meta($post_id, '_game_casino_urls', json_encode($urls));
    } else {
        update_post_meta($post_id, '_game_casino_urls', json_encode(array()));
    }
}
add_action('save_post', 'corona_save_game_meta');

/**
 * Enqueue admin scripts for game metabox
 */
function corona_game_admin_scripts($hook) {
    if ($hook !== 'post-new.php' && $hook !== 'post.php') {
        return;
    }

    global $post;
    if (!$post || get_page_template_slug($post->ID) !== 'page-game.php') {
        // Also load for new pages that might be assigned this template
        if ($hook === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'page') {
            wp_enqueue_script('jquery-ui-sortable');
        }
        return;
    }

    wp_enqueue_script('jquery-ui-sortable');
}
add_action('admin_enqueue_scripts', 'corona_game_admin_scripts');

