<?php
/**
 * Homepage Metaboxes
 *
 * Fields:
 * - Hero title, subtitle, button
 * - Top casinos selection
 * - Popular games selection
 *
 * @package Corona_Theme
 */

if (!defined('ABSPATH')) exit;

/**
 * Register homepage metaboxes
 */
function corona_register_home_metaboxes() {
    add_meta_box(
        'home_hero',
        __('Hero Section', 'corona-theme'),
        'corona_home_hero_metabox',
        'page',
        'normal',
        'high'
    );

    add_meta_box(
        'home_casinos',
        __('Top Casinos', 'corona-theme'),
        'corona_home_casinos_metabox',
        'page',
        'normal',
        'default'
    );

    add_meta_box(
        'home_games',
        __('Popular Games', 'corona-theme'),
        'corona_home_games_metabox',
        'page',
        'normal',
        'default'
    );
}

/**
 * Check if current page uses home template
 */
function corona_is_home_template() {
    global $post;
    if (!$post) return false;
    return get_page_template_slug($post->ID) === 'page-home.php';
}

/**
 * Conditionally add metaboxes
 */
function corona_add_home_metaboxes() {
    if (corona_is_home_template()) {
        corona_register_home_metaboxes();
    }
}
add_action('add_meta_boxes', 'corona_add_home_metaboxes');

/**
 * Hero section metabox
 */
function corona_home_hero_metabox($post) {
    wp_nonce_field('corona_home_meta', 'corona_home_nonce');

    $hero_title = get_post_meta($post->ID, '_home_hero_title', true);
    $hero_subtitle = get_post_meta($post->ID, '_home_hero_subtitle', true);
    $hero_btn_text = get_post_meta($post->ID, '_home_hero_btn_text', true);
    $hero_btn_url = get_post_meta($post->ID, '_home_hero_btn_url', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="home_hero_title"><?php esc_html_e('Hero Title', 'corona-theme'); ?></label></th>
            <td>
                <input type="text" id="home_hero_title" name="home_hero_title" value="<?php echo esc_attr($hero_title); ?>" class="large-text" placeholder="<?php echo esc_attr(get_the_title($post->ID)); ?>">
                <p class="description"><?php esc_html_e('Leave empty to use page title', 'corona-theme'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="home_hero_subtitle"><?php esc_html_e('Hero Subtitle', 'corona-theme'); ?></label></th>
            <td>
                <textarea id="home_hero_subtitle" name="home_hero_subtitle" class="large-text" rows="2" placeholder="<?php echo esc_attr__('Your trusted guide to online casinos...', 'corona-theme'); ?>"><?php echo esc_textarea($hero_subtitle); ?></textarea>
            </td>
        </tr>
        <tr>
            <th><label for="home_hero_btn_text"><?php esc_html_e('Button Text', 'corona-theme'); ?></label></th>
            <td>
                <input type="text" id="home_hero_btn_text" name="home_hero_btn_text" value="<?php echo esc_attr($hero_btn_text); ?>" class="regular-text" placeholder="<?php echo esc_attr__('View All Casinos', 'corona-theme'); ?>">
            </td>
        </tr>
        <tr>
            <th><label for="home_hero_btn_url"><?php esc_html_e('Button URL', 'corona-theme'); ?></label></th>
            <td>
                <input type="url" id="home_hero_btn_url" name="home_hero_btn_url" value="<?php echo esc_url($hero_btn_url); ?>" class="large-text" placeholder="https://...">
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Casinos selection metabox
 */
function corona_home_casinos_metabox($post) {
    $selected_casinos = get_post_meta($post->ID, '_home_casinos', true);
    $selected_casinos = $selected_casinos ? json_decode($selected_casinos, true) : array();

    $casinos_title = get_post_meta($post->ID, '_home_casinos_title', true);

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
    <table class="form-table">
        <tr>
            <th><label for="home_casinos_title"><?php esc_html_e('Section Title', 'corona-theme'); ?></label></th>
            <td>
                <input type="text" id="home_casinos_title" name="home_casinos_title" value="<?php echo esc_attr($casinos_title); ?>" class="regular-text" placeholder="<?php echo esc_attr__('Top Casinos', 'corona-theme'); ?>">
            </td>
        </tr>
    </table>

    <h4 style="margin-top: 20px;"><?php esc_html_e('Selected Casinos (3-5 recommended)', 'corona-theme'); ?></h4>
    <div id="home-casino-list" style="margin-bottom: 20px;">
        <?php if (!empty($selected_casinos)): ?>
            <?php foreach ($selected_casinos as $casino_id):
                $casino = get_post($casino_id);
                if (!$casino) continue;
            ?>
                <div class="home-casino-item" data-id="<?php echo esc_attr($casino_id); ?>" style="display: flex; align-items: center; gap: 10px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 5px; cursor: move;">
                    <span class="dashicons dashicons-menu" style="color: #999;"></span>
                    <span style="flex: 1;"><?php echo esc_html($casino->post_title); ?></span>
                    <button type="button" class="button button-small remove-home-casino"><?php esc_html_e('Remove', 'corona-theme'); ?></button>
                    <input type="hidden" name="home_casinos[]" value="<?php echo esc_attr($casino_id); ?>">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        <select id="add-home-casino-select" class="regular-text">
            <option value=""><?php esc_html_e('-- Select casino to add --', 'corona-theme'); ?></option>
            <?php foreach ($casinos as $casino): ?>
                <option value="<?php echo esc_attr($casino->ID); ?>" data-title="<?php echo esc_attr(wp_strip_all_tags($casino->post_title)); ?>">
                    <?php echo esc_html($casino->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="button" class="button" id="add-home-casino"><?php esc_html_e('Add Casino', 'corona-theme'); ?></button>
    </div>

    <style>
        #home-casino-list .home-casino-item.ui-sortable-placeholder {
            visibility: visible !important;
            background: #e0e0e0;
            border: 2px dashed #999;
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        var coronaI18n = window.CORONA_I18N || {};

        if ($.fn.sortable) {
            $('#home-casino-list').sortable({
                placeholder: 'home-casino-item ui-sortable-placeholder',
                handle: '.dashicons-menu'
            });
        }

        $('#add-home-casino').click(function() {
            var select = $('#add-home-casino-select');
            var id = select.val();
            var title = select.find('option:selected').data('title');

            if (!id) return;

            if ($('.home-casino-item[data-id="' + id + '"]').length) {
                alert(coronaI18n.alreadyInListCasino || '');
                return;
            }

            var $item = $('<div/>', {
                'class': 'home-casino-item',
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
                'class': 'button button-small remove-home-casino',
                text: coronaI18n.remove || ''
            }));
            $item.append($('<input/>', {
                type: 'hidden',
                name: 'home_casinos[]',
                value: id
            }));

            $('#home-casino-list').append($item);
            select.val('');
        });

        $(document).on('click', '.remove-home-casino', function() {
            $(this).closest('.home-casino-item').remove();
        });
    });
    </script>
    <?php
}

/**
 * Games selection metabox
 */
function corona_home_games_metabox($post) {
    $selected_games = get_post_meta($post->ID, '_home_games', true);
    $selected_games = $selected_games ? json_decode($selected_games, true) : array();

    $games_title = get_post_meta($post->ID, '_home_games_title', true);

    // Get current language
    $current_lang = function_exists('pll_get_post_language') ? pll_get_post_language($post->ID) : null;

    // Get all game pages
    $args = array(
        'post_type' => 'page',
        'posts_per_page' => 100,
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-game.php',
        'orderby' => 'title',
        'order' => 'ASC',
    );
    $game_query = new WP_Query($args);
    $all_games = $game_query->posts;

    // Filter by language if Polylang is active
    if ($current_lang && function_exists('pll_get_post_language')) {
        $games = array();
        foreach ($all_games as $game) {
            if (pll_get_post_language($game->ID) === $current_lang) {
                $games[] = $game;
            }
        }
    } else {
        $games = $all_games;
    }
    ?>
    <table class="form-table">
        <tr>
            <th><label for="home_games_title"><?php esc_html_e('Section Title', 'corona-theme'); ?></label></th>
            <td>
                <input type="text" id="home_games_title" name="home_games_title" value="<?php echo esc_attr($games_title); ?>" class="regular-text" placeholder="<?php echo esc_attr__('Popular Games', 'corona-theme'); ?>">
            </td>
        </tr>
    </table>

    <h4 style="margin-top: 20px;"><?php esc_html_e('Selected Games (4-6 recommended)', 'corona-theme'); ?></h4>
    <div id="home-game-list" style="margin-bottom: 20px;">
        <?php if (!empty($selected_games)): ?>
            <?php foreach ($selected_games as $game_id):
                $game = get_post($game_id);
                if (!$game) continue;
            ?>
                <div class="home-game-item" data-id="<?php echo esc_attr($game_id); ?>" style="display: flex; align-items: center; gap: 10px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 5px; cursor: move;">
                    <span class="dashicons dashicons-menu" style="color: #999;"></span>
                    <span style="flex: 1;"><?php echo esc_html($game->post_title); ?></span>
                    <button type="button" class="button button-small remove-home-game"><?php esc_html_e('Remove', 'corona-theme'); ?></button>
                    <input type="hidden" name="home_games[]" value="<?php echo esc_attr($game_id); ?>">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        <select id="add-home-game-select" class="regular-text">
            <option value=""><?php esc_html_e('-- Select game to add --', 'corona-theme'); ?></option>
            <?php foreach ($games as $game): ?>
                <option value="<?php echo esc_attr($game->ID); ?>" data-title="<?php echo esc_attr(wp_strip_all_tags($game->post_title)); ?>">
                    <?php echo esc_html($game->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="button" class="button" id="add-home-game"><?php esc_html_e('Add Game', 'corona-theme'); ?></button>
    </div>

    <style>
        #home-game-list .home-game-item.ui-sortable-placeholder {
            visibility: visible !important;
            background: #e0e0e0;
            border: 2px dashed #999;
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        var coronaI18n = window.CORONA_I18N || {};

        if ($.fn.sortable) {
            $('#home-game-list').sortable({
                placeholder: 'home-game-item ui-sortable-placeholder',
                handle: '.dashicons-menu'
            });
        }

        $('#add-home-game').click(function() {
            var select = $('#add-home-game-select');
            var id = select.val();
            var title = select.find('option:selected').data('title');

            if (!id) return;

            if ($('.home-game-item[data-id="' + id + '"]').length) {
                alert(coronaI18n.alreadyInListGame || '');
                return;
            }

            var $item = $('<div/>', {
                'class': 'home-game-item',
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
                'class': 'button button-small remove-home-game',
                text: coronaI18n.remove || ''
            }));
            $item.append($('<input/>', {
                type: 'hidden',
                name: 'home_games[]',
                value: id
            }));

            $('#home-game-list').append($item);
            select.val('');
        });

        $(document).on('click', '.remove-home-game', function() {
            $(this).closest('.home-game-item').remove();
        });
    });
    </script>
    <?php
}

/**
 * Save homepage meta
 */
function corona_save_home_meta($post_id) {
    // Verify nonce
    if (!isset($_POST['corona_home_nonce']) || !wp_verify_nonce($_POST['corona_home_nonce'], 'corona_home_meta')) {
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

    // Check if home template
    if (get_page_template_slug($post_id) !== 'page-home.php') {
        return;
    }

    // Save hero fields
    if (isset($_POST['home_hero_title'])) {
        update_post_meta($post_id, '_home_hero_title', sanitize_text_field(wp_unslash($_POST['home_hero_title'])));
    }
    if (isset($_POST['home_hero_subtitle'])) {
        update_post_meta($post_id, '_home_hero_subtitle', sanitize_textarea_field(wp_unslash($_POST['home_hero_subtitle'])));
    }
    if (isset($_POST['home_hero_btn_text'])) {
        update_post_meta($post_id, '_home_hero_btn_text', sanitize_text_field(wp_unslash($_POST['home_hero_btn_text'])));
    }
    if (isset($_POST['home_hero_btn_url'])) {
        update_post_meta($post_id, '_home_hero_btn_url', esc_url_raw(wp_unslash($_POST['home_hero_btn_url'])));
    }

    // Save section titles
    if (isset($_POST['home_casinos_title'])) {
        update_post_meta($post_id, '_home_casinos_title', sanitize_text_field(wp_unslash($_POST['home_casinos_title'])));
    }
    if (isset($_POST['home_games_title'])) {
        update_post_meta($post_id, '_home_games_title', sanitize_text_field(wp_unslash($_POST['home_games_title'])));
    }

    // Save casinos
    if (isset($_POST['home_casinos'])) {
        $casinos = array_map('absint', wp_unslash($_POST['home_casinos']));
        $casinos = array_filter($casinos);
        update_post_meta($post_id, '_home_casinos', json_encode($casinos));
    } else {
        update_post_meta($post_id, '_home_casinos', json_encode(array()));
    }

    // Save games
    if (isset($_POST['home_games'])) {
        $games = array_map('absint', wp_unslash($_POST['home_games']));
        $games = array_filter($games);
        update_post_meta($post_id, '_home_games', json_encode($games));
    } else {
        update_post_meta($post_id, '_home_games', json_encode(array()));
    }
}
add_action('save_post', 'corona_save_home_meta');

/**
 * Enqueue admin scripts for home metabox
 */
function corona_home_admin_scripts($hook) {
    if ($hook !== 'post-new.php' && $hook !== 'post.php') {
        return;
    }

    global $post;
    if (!$post || get_page_template_slug($post->ID) !== 'page-home.php') {
        if ($hook === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'page') {
            wp_enqueue_script('jquery-ui-sortable');
        }
        return;
    }

    wp_enqueue_script('jquery-ui-sortable');
}
add_action('admin_enqueue_scripts', 'corona_home_admin_scripts');

