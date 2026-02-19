<?php
/**
 * Mobile App Page Metaboxes
 *
 * Fields:
 * - App name, developer, rating
 * - Download URL
 * - Screenshots (with default fallback option)
 * - What's New
 * - Related casino page
 */

if (!defined('ABSPATH')) exit;

/**
 * Register app page metaboxes
 */
function corona_register_app_metaboxes() {
    add_meta_box(
        'app_main_info',
        __('App Information', 'corona-theme'),
        'corona_app_main_metabox',
        'page',
        'normal',
        'high'
    );

    add_meta_box(
        'app_screenshots',
        __('App Screenshots', 'corona-theme'),
        'corona_app_screenshots_metabox',
        'page',
        'normal',
        'default'
    );

    add_meta_box(
        'app_details',
        __('App Details', 'corona-theme'),
        'corona_app_details_metabox',
        'page',
        'normal',
        'default'
    );
}

/**
 * Check if current page uses app template
 */
function corona_is_app_template() {
    global $post;
    if (!$post) return false;
    return get_page_template_slug($post->ID) === 'page-app.php';
}

/**
 * Conditionally add metaboxes
 */
function corona_add_app_metaboxes() {
    if (corona_is_app_template()) {
        corona_register_app_metaboxes();
    }
}
add_action('add_meta_boxes', 'corona_add_app_metaboxes');

/**
 * Main app info metabox
 */
function corona_app_main_metabox($post) {
    wp_nonce_field('corona_app_meta', 'corona_app_nonce');

    $logo = get_post_meta($post->ID, '_app_logo', true);
    $app_name = get_post_meta($post->ID, '_app_name', true);
    $developer = get_post_meta($post->ID, '_app_developer', true);
    $app_url = get_post_meta($post->ID, '_app_download_url', true);
    $casino_page_id = get_post_meta($post->ID, '_app_casino_page', true);

    // Get casino pages for dropdown
    $current_lang = function_exists('pll_get_post_language') ? pll_get_post_language($post->ID) : null;
    $casino_query = new WP_Query(array(
        'post_type' => 'page',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-casino.php',
    ));
    $all_casinos = $casino_query->posts;

    // Filter by language
    $casino_pages = array();
    if ($current_lang && function_exists('pll_get_post_language')) {
        foreach ($all_casinos as $page) {
            if (pll_get_post_language($page->ID) === $current_lang) {
                $casino_pages[] = $page;
            }
        }
    } else {
        $casino_pages = $all_casinos;
    }
    ?>

    <table class="form-table">
        <tr>
            <th><label for="app_logo"><?php esc_html_e('App Icon', 'corona-theme'); ?></label></th>
            <td>
                <div class="app-logo-upload">
                    <input type="hidden" id="app_logo" name="app_logo" value="<?php echo esc_url($logo); ?>">
                    <div class="logo-preview" id="app-logo-preview">
                        <?php if ($logo): ?>
                            <img src="<?php echo esc_url($logo); ?>" style="max-width: 128px; height: auto; border-radius: 22%;">
                        <?php else: ?>
                            <div style="width: 128px; height: 128px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 22%; color: #666;">
                                <?php esc_html_e('No icon', 'corona-theme'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <p style="margin: 10px 0;">
                        <button type="button" class="button" id="upload-app-logo-btn"><?php esc_html_e('Select Icon', 'corona-theme'); ?></button>
                        <button type="button" class="button" id="remove-app-logo-btn" <?php echo !$logo ? 'style="display:none;"' : ''; ?>><?php esc_html_e('Remove', 'corona-theme'); ?></button>
                    </p>
                    <p class="description"><?php esc_html_e('Leave empty to use logo from linked casino page', 'corona-theme'); ?></p>
                </div>
            </td>
        </tr>

        <tr>
            <th><label for="app_name"><?php esc_html_e('App Name', 'corona-theme'); ?></label></th>
            <td>
                <input type="text" id="app_name" name="app_name" value="<?php echo esc_attr($app_name); ?>" class="large-text" placeholder="<?php echo esc_attr__('Rabona Casino App', 'corona-theme'); ?>">
                <p class="description"><?php esc_html_e('Leave empty to use page title', 'corona-theme'); ?></p>
            </td>
        </tr>

        <tr>
            <th><label for="app_developer"><?php esc_html_e('Developer', 'corona-theme'); ?></label></th>
            <td>
                <input type="text" id="app_developer" name="app_developer" value="<?php echo esc_attr($developer); ?>" class="regular-text" placeholder="<?php echo esc_attr__('Games Ltd.', 'corona-theme'); ?>">
            </td>
        </tr>

        <tr>
            <th><label for="app_download_url"><?php esc_html_e('Download URL', 'corona-theme'); ?></label></th>
            <td>
                <input type="url" id="app_download_url" name="app_download_url" value="<?php echo esc_url($app_url); ?>" class="large-text" placeholder="https://apps.apple.com/...">
            </td>
        </tr>

        <tr>
            <th><label for="app_casino_page"><?php esc_html_e('Related Casino', 'corona-theme'); ?></label></th>
            <td>
                <select id="app_casino_page" name="app_casino_page" class="regular-text">
                    <option value=""><?php esc_html_e('-- Select casino --', 'corona-theme'); ?></option>
                    <?php foreach ($casino_pages as $casino): ?>
                        <option value="<?php echo esc_attr($casino->ID); ?>" <?php selected($casino_page_id, $casino->ID); ?>>
                            <?php echo esc_html($casino->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php esc_html_e('Link to casino review page (for "Back to Review" link and logo fallback)', 'corona-theme'); ?></p>
            </td>
        </tr>
    </table>

    <script>
    jQuery(document).ready(function($) {
        var coronaI18n = window.CORONA_I18N || {};

        var mediaUploader;
        $('#upload-app-logo-btn').click(function(e) {
            e.preventDefault();
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            mediaUploader = wp.media({
                title: coronaI18n.selectAppIcon || '',
                button: { text: coronaI18n.useThisImage || '' },
                multiple: false
            });
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#app_logo').val(attachment.url);
                $('#app-logo-preview').html('<img src="' + attachment.url + '" style="max-width: 128px; height: auto; border-radius: 22%;">');
                $('#remove-app-logo-btn').show();
            });
            mediaUploader.open();
        });

        $('#remove-app-logo-btn').click(function(e) {
            e.preventDefault();
            $('#app_logo').val('');
            $('#app-logo-preview').html('<div style="width: 128px; height: 128px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 22%; color: #666;">' + (coronaI18n.noIcon || '') + '</div>');
            $(this).hide();
        });
    });
    </script>
    <?php
}

/**
 * Screenshots metabox
 */
function corona_app_screenshots_metabox($post) {
    $screenshots = get_post_meta($post->ID, '_app_screenshots', true);
    $screenshots = $screenshots ? json_decode($screenshots, true) : array();
    $use_default = get_post_meta($post->ID, '_app_use_default_screenshots', true);
    ?>

    <table class="form-table">
        <tr>
            <th><label><?php esc_html_e('Use Default Screenshots', 'corona-theme'); ?></label></th>
            <td>
                <label>
                    <input type="checkbox" name="app_use_default_screenshots" value="1" <?php checked($use_default, '1'); ?>>
                    <?php esc_html_e('Use default screenshots from theme settings', 'corona-theme'); ?>
                </label>
                <p class="description"><?php esc_html_e('If enabled, default screenshots from Customizer will be shown when no custom screenshots are uploaded', 'corona-theme'); ?></p>
            </td>
        </tr>
    </table>

    <h4><?php esc_html_e('Custom Screenshots', 'corona-theme'); ?></h4>
    <p class="description"><?php esc_html_e('Upload screenshots specific to this app. These override default screenshots.', 'corona-theme'); ?></p>

    <div id="app-screenshots-container" style="display: flex; flex-wrap: wrap; gap: 10px; margin: 15px 0;">
        <?php if (!empty($screenshots)): ?>
            <?php foreach ($screenshots as $i => $screenshot): ?>
                <div class="app-screenshot-item" style="position: relative;">
                    <img src="<?php echo esc_url($screenshot); ?>" style="width: 100px; height: 180px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                    <button type="button" class="button-link remove-screenshot" style="position: absolute; top: -8px; right: -8px; background: #dc3545; color: white; border-radius: 50%; width: 20px; height: 20px; padding: 0; line-height: 18px; text-align: center;">&times;</button>
                    <input type="hidden" name="app_screenshots[]" value="<?php echo esc_url($screenshot); ?>">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <button type="button" class="button" id="add-app-screenshot"><?php esc_html_e('+ Add Screenshots', 'corona-theme'); ?></button>

    <script>
    jQuery(document).ready(function($) {
        var coronaI18n = window.CORONA_I18N || {};

        var screenshotUploader;
        $('#add-app-screenshot').click(function(e) {
            e.preventDefault();
            if (screenshotUploader) {
                screenshotUploader.open();
                return;
            }
            screenshotUploader = wp.media({
                title: coronaI18n.selectAppScreenshots || '',
                button: { text: coronaI18n.addScreenshots || '' },
                multiple: true
            });
            screenshotUploader.on('select', function() {
                var attachments = screenshotUploader.state().get('selection').toJSON();
                attachments.forEach(function(attachment) {
                    var html = '<div class="app-screenshot-item" style="position: relative;">' +
                        '<img src="' + attachment.url + '" style="width: 100px; height: 180px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">' +
                        '<button type="button" class="button-link remove-screenshot" style="position: absolute; top: -8px; right: -8px; background: #dc3545; color: white; border-radius: 50%; width: 20px; height: 20px; padding: 0; line-height: 18px; text-align: center;">&times;</button>' +
                        '<input type="hidden" name="app_screenshots[]" value="' + attachment.url + '">' +
                        '</div>';
                    $('#app-screenshots-container').append(html);
                });
            });
            screenshotUploader.open();
        });

        $(document).on('click', '.remove-screenshot', function() {
            $(this).closest('.app-screenshot-item').remove();
        });
    });
    </script>
    <?php
}

/**
 * App details metabox
 */
function corona_app_details_metabox($post) {
    $rating = get_post_meta($post->ID, '_app_rating', true);
    $ratings_count = get_post_meta($post->ID, '_app_ratings_count', true);
    $category_rank = get_post_meta($post->ID, '_app_category_rank', true);
    $age_rating = get_post_meta($post->ID, '_app_age_rating', true);
    $app_size = get_post_meta($post->ID, '_app_size', true);
    $app_version = get_post_meta($post->ID, '_app_version', true);
    $whats_new = get_post_meta($post->ID, '_app_whats_new', true);
    ?>

    <table class="form-table">
        <tr>
            <th><label for="app_rating"><?php esc_html_e('Rating', 'corona-theme'); ?></label></th>
            <td>
                <input type="text" id="app_rating" name="app_rating" value="<?php echo esc_attr($rating); ?>" class="small-text" placeholder="4.8">
                <span class="description"><?php esc_html_e('e.g., 4.8', 'corona-theme'); ?></span>
            </td>
        </tr>

        <tr>
            <th><label for="app_ratings_count"><?php esc_html_e('Ratings Count', 'corona-theme'); ?></label></th>
            <td>
                <input type="text" id="app_ratings_count" name="app_ratings_count" value="<?php echo esc_attr($ratings_count); ?>" class="small-text" placeholder="4.2K">
            </td>
        </tr>

        <tr>
            <th><label for="app_category_rank"><?php esc_html_e('Category Rank', 'corona-theme'); ?></label></th>
            <td>
                <input type="text" id="app_category_rank" name="app_category_rank" value="<?php echo esc_attr($category_rank); ?>" class="small-text" placeholder="#1">
            </td>
        </tr>

        <tr>
            <th><label for="app_age_rating"><?php esc_html_e('Age Rating', 'corona-theme'); ?></label></th>
            <td>
                <input type="text" id="app_age_rating" name="app_age_rating" value="<?php echo esc_attr($age_rating); ?>" class="small-text" placeholder="18+">
            </td>
        </tr>

        <tr>
            <th><label for="app_size"><?php esc_html_e('App Size', 'corona-theme'); ?></label></th>
            <td>
                <input type="text" id="app_size" name="app_size" value="<?php echo esc_attr($app_size); ?>" class="small-text" placeholder="142.8 MB">
            </td>
        </tr>

        <tr>
            <th><label for="app_version"><?php esc_html_e('Version', 'corona-theme'); ?></label></th>
            <td>
                <input type="text" id="app_version" name="app_version" value="<?php echo esc_attr($app_version); ?>" class="small-text" placeholder="4.8.2">
            </td>
        </tr>

        <tr>
            <th><label for="app_whats_new"><?php esc_html_e("What's New", 'corona-theme'); ?></label></th>
            <td>
                <?php
                wp_editor($whats_new, 'app_whats_new', array(
                    'textarea_name' => 'app_whats_new',
                    'textarea_rows' => 5,
                    'media_buttons' => false,
                    'teeny' => true,
                    'quicktags' => array('buttons' => 'strong,em,ul,li'),
                ));
                ?>
                <p class="description"><?php esc_html_e('Recent changes and updates', 'corona-theme'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save app meta
 */
function corona_save_app_meta($post_id) {
    // Verify nonce
    if (!isset($_POST['corona_app_nonce']) || !wp_verify_nonce($_POST['corona_app_nonce'], 'corona_app_meta')) {
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

    // Check if app template
    if (get_page_template_slug($post_id) !== 'page-app.php') {
        return;
    }

    // Save main info
    $fields = array(
        'app_logo' => 'esc_url_raw',
        'app_name' => 'sanitize_text_field',
        'app_developer' => 'sanitize_text_field',
        'app_download_url' => 'esc_url_raw',
        'app_casino_page' => 'intval',
        'app_rating' => 'sanitize_text_field',
        'app_ratings_count' => 'sanitize_text_field',
        'app_category_rank' => 'sanitize_text_field',
        'app_age_rating' => 'sanitize_text_field',
        'app_size' => 'sanitize_text_field',
        'app_version' => 'sanitize_text_field',
    );

    foreach ($fields as $field => $sanitize) {
        if (isset($_POST[$field])) {
            $value = call_user_func($sanitize, $_POST[$field]);
            if (!empty($value)) {
                update_post_meta($post_id, '_' . $field, $value);
            } else {
                delete_post_meta($post_id, '_' . $field);
            }
        }
    }

    // Save what's new
    if (isset($_POST['app_whats_new'])) {
        update_post_meta($post_id, '_app_whats_new', wp_kses_post($_POST['app_whats_new']));
    }

    // Save screenshots
    if (isset($_POST['app_screenshots']) && is_array($_POST['app_screenshots'])) {
        $screenshots = array_map('esc_url_raw', $_POST['app_screenshots']);
        $screenshots = array_filter($screenshots);
        update_post_meta($post_id, '_app_screenshots', json_encode(array_values($screenshots)));
    } else {
        delete_post_meta($post_id, '_app_screenshots');
    }

    // Save use default screenshots option
    if (isset($_POST['app_use_default_screenshots'])) {
        update_post_meta($post_id, '_app_use_default_screenshots', '1');
    } else {
        delete_post_meta($post_id, '_app_use_default_screenshots');
    }
}
add_action('save_post', 'corona_save_app_meta');

