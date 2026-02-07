<?php
/**
 * Casino Review Metaboxes
 *
 * Fields:
 * - Logo (image)
 * - Main affiliate link (URL)
 * - Characteristics (dynamic table)
 * - Bonuses (dynamic list with name, description, button text, button URL)
 * - Payment methods (simple list)
 * - Rating (1-5 stars)
 * - Extra buttons (up to 2)
 */

if (!defined('ABSPATH')) exit;

/**
 * Register casino metaboxes
 */
function corona_register_casino_metaboxes() {
    // Only show for pages with casino template
    add_meta_box(
        'casino_main_info',
        'Casino Information',
        'corona_casino_main_metabox',
        'page',
        'normal',
        'high'
    );

    add_meta_box(
        'casino_bonuses',
        'Bonuses',
        'corona_casino_bonuses_metabox',
        'page',
        'normal',
        'default'
    );

    add_meta_box(
        'casino_extra',
        'Payment Methods & Rating',
        'corona_casino_extra_metabox',
        'page',
        'normal',
        'default'
    );
}

/**
 * Check if current page uses casino template
 */
function corona_is_casino_template() {
    global $post;
    if (!$post) return false;
    return get_page_template_slug($post->ID) === 'page-casino.php';
}

/**
 * Conditionally add metaboxes
 */
function corona_add_casino_metaboxes() {
    if (corona_is_casino_template()) {
        corona_register_casino_metaboxes();
    }
}
add_action('add_meta_boxes', 'corona_add_casino_metaboxes');

/**
 * Main casino info metabox
 */
function corona_casino_main_metabox($post) {
    wp_nonce_field('corona_casino_meta', 'corona_casino_nonce');

    $logo = get_post_meta($post->ID, '_casino_logo', true);
    $aff_link = get_post_meta($post->ID, '_casino_aff_link', true);
    $tags = get_post_meta($post->ID, '_casino_tags', true);
    $tags = $tags ? json_decode($tags, true) : array();
    $characteristics = get_post_meta($post->ID, '_casino_characteristics', true);
    $characteristics = $characteristics ? json_decode($characteristics, true) : array();

    // Migrate old data if exists
    if (empty($characteristics)) {
        $old_chars = get_post_meta($post->ID, '_slot_custom_characteristics', true);
        if ($old_chars) {
            $characteristics = json_decode($old_chars, true) ?: array();
        }
    }

    // Default characteristics for new casino pages
    if (empty($characteristics)) {
        $characteristics = array(
            array('label' => __('Operator', 'corona-theme'), 'value' => ''),
            array('label' => __('License', 'corona-theme'), 'value' => ''),
            array('label' => __('Min Deposit', 'corona-theme'), 'value' => ''),
            array('label' => __('Min Withdrawal', 'corona-theme'), 'value' => '')
        );
    }

    // Migrate old image
    if (empty($logo)) {
        $old_image = get_post_meta($post->ID, '_slot_image', true);
        if ($old_image) {
            $logo = $old_image;
        }
    }
    ?>

    <table class="form-table">
        <!-- Logo -->
        <tr>
            <th><label for="casino_logo">Casino Logo</label></th>
            <td>
                <div class="casino-logo-upload">
                    <input type="hidden" id="casino_logo" name="casino_logo" value="<?php echo esc_url($logo); ?>">
                    <div class="logo-preview" id="logo-preview">
                        <?php if ($logo): ?>
                            <img src="<?php echo esc_url($logo); ?>" style="max-width: 200px; height: auto; border-radius: 8px;">
                        <?php else: ?>
                            <div style="width: 200px; height: 100px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 8px; color: #666;">
                                No image selected
                            </div>
                        <?php endif; ?>
                    </div>
                    <p style="margin: 10px 0;">
                        <button type="button" class="button" id="upload-logo-btn">Select Image</button>
                        <button type="button" class="button" id="remove-logo-btn" <?php echo !$logo ? 'style="display:none;"' : ''; ?>>Remove</button>
                    </p>
                </div>
            </td>
        </tr>

        <!-- Main Affiliate Link -->
        <tr>
            <th><label for="casino_aff_link">Main Affiliate Link</label></th>
            <td>
                <input type="url" id="casino_aff_link" name="casino_aff_link" value="<?php echo esc_url($aff_link); ?>" class="large-text">
                <p class="description">Default URL for all buttons (can be overridden per bonus)</p>
            </td>
        </tr>

        <!-- Tags for Casino List -->
        <tr>
            <th><label for="casino_tags">Tags (for Top List)</label></th>
            <td>
                <input type="text" id="casino_tags" name="casino_tags" value="<?php echo esc_attr(implode(', ', $tags)); ?>" class="large-text" placeholder="Premium Choice, Fast Payouts">
                <p class="description">Comma-separated tags shown on Top Casinos List page. Leave empty for default tags.</p>
            </td>
        </tr>
    </table>

    <!-- Characteristics -->
    <h3 style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">Characteristics</h3>
    <div id="characteristics-container">
        <?php if (!empty($characteristics)): ?>
            <?php foreach ($characteristics as $index => $char): ?>
                <div class="char-row" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                    <input type="text" name="char_labels[]" value="<?php echo esc_attr($char['label']); ?>" placeholder="Label" class="regular-text">
                    <input type="text" name="char_values[]" value="<?php echo esc_attr($char['value']); ?>" placeholder="Value" class="regular-text">
                    <button type="button" class="button remove-char">Remove</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="char-row" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                <input type="text" name="char_labels[]" value="" placeholder="Label" class="regular-text">
                <input type="text" name="char_values[]" value="" placeholder="Value" class="regular-text">
                <button type="button" class="button remove-char">Remove</button>
            </div>
        <?php endif; ?>
    </div>
    <button type="button" class="button button-primary" id="add-char">+ Add Characteristic</button>

    <script>
    jQuery(document).ready(function($) {
        // Logo upload
        var mediaUploader;
        $('#upload-logo-btn').click(function(e) {
            e.preventDefault();
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            mediaUploader = wp.media({
                title: 'Select Casino Logo',
                button: { text: 'Use this image' },
                multiple: false
            });
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#casino_logo').val(attachment.url);
                $('#logo-preview').html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto; border-radius: 8px;">');
                $('#remove-logo-btn').show();
            });
            mediaUploader.open();
        });

        $('#remove-logo-btn').click(function(e) {
            e.preventDefault();
            $('#casino_logo').val('');
            $('#logo-preview').html('<div style="width: 200px; height: 100px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 8px; color: #666;">No image selected</div>');
            $(this).hide();
        });

        // Characteristics
        $('#add-char').click(function() {
            var row = '<div class="char-row" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">' +
                '<input type="text" name="char_labels[]" value="" placeholder="Label" class="regular-text">' +
                '<input type="text" name="char_values[]" value="" placeholder="Value" class="regular-text">' +
                '<button type="button" class="button remove-char">Remove</button>' +
                '</div>';
            $('#characteristics-container').append(row);
        });

        $(document).on('click', '.remove-char', function() {
            $(this).closest('.char-row').remove();
        });
    });
    </script>
    <?php
}

/**
 * Bonuses metabox
 */
function corona_casino_bonuses_metabox($post) {
    $bonuses = get_post_meta($post->ID, '_casino_bonuses', true);
    $bonuses = $bonuses ? json_decode($bonuses, true) : array();
    $default_aff = get_post_meta($post->ID, '_casino_aff_link', true);
    ?>

    <div id="bonuses-container">
        <?php if (!empty($bonuses)): ?>
            <?php foreach ($bonuses as $index => $bonus): ?>
                <div class="bonus-item" style="background: #f9f9f9; padding: 15px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ddd;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px;">
                        <div>
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Bonus Name</label>
                            <input type="text" name="bonus_names[]" value="<?php echo esc_attr($bonus['name']); ?>" placeholder="e.g. Welcome Bonus" class="large-text">
                        </div>
                        <div>
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Description</label>
                            <input type="text" name="bonus_descriptions[]" value="<?php echo esc_attr($bonus['description']); ?>" placeholder="e.g. 100% up to €100" class="large-text">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px;">
                        <div>
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Button Text</label>
                            <input type="text" name="bonus_btn_texts[]" value="<?php echo esc_attr($bonus['btn_text'] ?? 'Claim offer and play'); ?>" placeholder="Claim offer and play" class="large-text">
                        </div>
                        <div>
                            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Button URL (leave empty for default aff link)</label>
                            <input type="url" name="bonus_btn_urls[]" value="<?php echo esc_url($bonus['btn_url'] ?? ''); ?>" placeholder="https://..." class="large-text">
                        </div>
                    </div>
                    <button type="button" class="button remove-bonus" style="margin-top: 10px;">Remove Bonus</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <button type="button" class="button button-primary" id="add-bonus">+ Add Bonus</button>

    <script>
    jQuery(document).ready(function($) {
        $('#add-bonus').click(function() {
            var item = '<div class="bonus-item" style="background: #f9f9f9; padding: 15px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ddd;">' +
                '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px;">' +
                '<div><label style="font-weight: 600; display: block; margin-bottom: 5px;">Bonus Name</label>' +
                '<input type="text" name="bonus_names[]" value="" placeholder="e.g. Welcome Bonus" class="large-text"></div>' +
                '<div><label style="font-weight: 600; display: block; margin-bottom: 5px;">Description</label>' +
                '<input type="text" name="bonus_descriptions[]" value="" placeholder="e.g. 100% up to €100" class="large-text"></div></div>' +
                '<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px;">' +
                '<div><label style="font-weight: 600; display: block; margin-bottom: 5px;">Button Text</label>' +
                '<input type="text" name="bonus_btn_texts[]" value="Claim offer and play" placeholder="Claim offer and play" class="large-text"></div>' +
                '<div><label style="font-weight: 600; display: block; margin-bottom: 5px;">Button URL (leave empty for default aff link)</label>' +
                '<input type="url" name="bonus_btn_urls[]" value="" placeholder="https://..." class="large-text"></div></div>' +
                '<button type="button" class="button remove-bonus" style="margin-top: 10px;">Remove Bonus</button></div>';
            $('#bonuses-container').append(item);
        });

        $(document).on('click', '.remove-bonus', function() {
            $(this).closest('.bonus-item').remove();
        });
    });
    </script>
    <?php
}

/**
 * Extra info metabox (payment methods, rating, extra buttons)
 */
function corona_casino_extra_metabox($post) {
    $payment_methods = get_post_meta($post->ID, '_casino_payment_methods', true);
    $payment_methods = $payment_methods ? json_decode($payment_methods, true) : array();
    $rating = get_post_meta($post->ID, '_casino_rating', true) ?: 5;

    $extra_buttons = get_post_meta($post->ID, '_casino_extra_buttons', true);
    $extra_buttons = $extra_buttons ? json_decode($extra_buttons, true) : array();
    ?>

    <table class="form-table">
        <!-- Rating -->
        <tr>
            <th><label for="casino_rating">Rating</label></th>
            <td>
                <select id="casino_rating" name="casino_rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php selected($rating, $i); ?>><?php echo str_repeat('★', $i) . str_repeat('☆', 5 - $i); ?> (<?php echo $i; ?>)</option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
    </table>

    <!-- Payment Methods -->
    <h3 style="margin-top: 20px;">Payment Methods</h3>
    <div id="payments-container">
        <?php if (!empty($payment_methods)): ?>
            <?php foreach ($payment_methods as $method): ?>
                <div class="payment-row" style="display: flex; gap: 10px; margin-bottom: 8px; align-items: center;">
                    <input type="text" name="payment_methods[]" value="<?php echo esc_attr($method); ?>" placeholder="e.g. Visa, Bitcoin" class="regular-text">
                    <button type="button" class="button remove-payment">Remove</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="payment-row" style="display: flex; gap: 10px; margin-bottom: 8px; align-items: center;">
                <input type="text" name="payment_methods[]" value="" placeholder="e.g. Visa, Bitcoin" class="regular-text">
                <button type="button" class="button remove-payment">Remove</button>
            </div>
        <?php endif; ?>
    </div>
    <button type="button" class="button" id="add-payment">+ Add Payment Method</button>

    <!-- Extra Buttons -->
    <h3 style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">Extra Buttons (optional)</h3>
    <p class="description">Additional CTA buttons (e.g. "Visit Casino", "See All Bonuses")</p>

    <div id="extra-buttons-container">
        <?php
        $btn_count = max(2, count($extra_buttons));
        for ($i = 0; $i < 2; $i++):
            $btn = isset($extra_buttons[$i]) ? $extra_buttons[$i] : array('text' => '', 'url' => '');
        ?>
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-bottom: 10px;">
                <input type="text" name="extra_btn_texts[]" value="<?php echo esc_attr($btn['text']); ?>" placeholder="Button text" class="regular-text">
                <input type="url" name="extra_btn_urls[]" value="<?php echo esc_url($btn['url']); ?>" placeholder="Button URL" class="large-text">
            </div>
        <?php endfor; ?>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('#add-payment').click(function() {
            var row = '<div class="payment-row" style="display: flex; gap: 10px; margin-bottom: 8px; align-items: center;">' +
                '<input type="text" name="payment_methods[]" value="" placeholder="e.g. Visa, Bitcoin" class="regular-text">' +
                '<button type="button" class="button remove-payment">Remove</button></div>';
            $('#payments-container').append(row);
        });

        $(document).on('click', '.remove-payment', function() {
            if ($('.payment-row').length > 1) {
                $(this).closest('.payment-row').remove();
            }
        });
    });
    </script>
    <?php
}

/**
 * Save casino meta
 */
function corona_save_casino_meta($post_id) {
    // Verify nonce
    if (!isset($_POST['corona_casino_nonce']) || !wp_verify_nonce($_POST['corona_casino_nonce'], 'corona_casino_meta')) {
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

    // Check if casino template
    if (get_page_template_slug($post_id) !== 'page-casino.php') {
        return;
    }

    // Save logo
    if (isset($_POST['casino_logo'])) {
        update_post_meta($post_id, '_casino_logo', esc_url_raw($_POST['casino_logo']));
    }

    // Save affiliate link
    if (isset($_POST['casino_aff_link'])) {
        update_post_meta($post_id, '_casino_aff_link', esc_url_raw($_POST['casino_aff_link']));
    }

    // Save tags
    if (isset($_POST['casino_tags'])) {
        $tags_string = sanitize_text_field($_POST['casino_tags']);
        if (!empty($tags_string)) {
            $tags = array_map('trim', explode(',', $tags_string));
            $tags = array_filter($tags); // Remove empty values
            update_post_meta($post_id, '_casino_tags', json_encode(array_values($tags), JSON_UNESCAPED_UNICODE));
        } else {
            delete_post_meta($post_id, '_casino_tags');
        }
    }

    // Save characteristics
    if (isset($_POST['char_labels']) && isset($_POST['char_values'])) {
        $labels = array_map('sanitize_text_field', $_POST['char_labels']);
        $values = array_map('sanitize_text_field', $_POST['char_values']);
        $characteristics = array();
        for ($i = 0; $i < count($labels); $i++) {
            if (!empty($labels[$i]) || !empty($values[$i])) {
                $characteristics[] = array(
                    'label' => $labels[$i],
                    'value' => $values[$i]
                );
            }
        }
        update_post_meta($post_id, '_casino_characteristics', json_encode($characteristics, JSON_UNESCAPED_UNICODE));
    }

    // Save bonuses
    if (isset($_POST['bonus_names'])) {
        $names = array_map('sanitize_text_field', $_POST['bonus_names']);
        $descriptions = array_map('sanitize_text_field', $_POST['bonus_descriptions']);
        $btn_texts = array_map('sanitize_text_field', $_POST['bonus_btn_texts']);
        $btn_urls = array_map('esc_url_raw', $_POST['bonus_btn_urls']);

        $bonuses = array();
        for ($i = 0; $i < count($names); $i++) {
            if (!empty($names[$i]) || !empty($descriptions[$i])) {
                $bonuses[] = array(
                    'name' => $names[$i],
                    'description' => $descriptions[$i],
                    'btn_text' => $btn_texts[$i] ?: 'Claim offer and play',
                    'btn_url' => $btn_urls[$i]
                );
            }
        }
        update_post_meta($post_id, '_casino_bonuses', json_encode($bonuses, JSON_UNESCAPED_UNICODE));
    }

    // Save payment methods
    if (isset($_POST['payment_methods'])) {
        $methods = array_filter(array_map('sanitize_text_field', $_POST['payment_methods']));
        update_post_meta($post_id, '_casino_payment_methods', json_encode(array_values($methods), JSON_UNESCAPED_UNICODE));
    }

    // Save rating
    if (isset($_POST['casino_rating'])) {
        update_post_meta($post_id, '_casino_rating', intval($_POST['casino_rating']));
    }

    // Save extra buttons
    if (isset($_POST['extra_btn_texts'])) {
        $texts = array_map('sanitize_text_field', $_POST['extra_btn_texts']);
        $urls = array_map('esc_url_raw', $_POST['extra_btn_urls']);

        $buttons = array();
        for ($i = 0; $i < count($texts); $i++) {
            if (!empty($texts[$i]) && !empty($urls[$i])) {
                $buttons[] = array(
                    'text' => $texts[$i],
                    'url' => $urls[$i]
                );
            }
        }
        update_post_meta($post_id, '_casino_extra_buttons', json_encode($buttons, JSON_UNESCAPED_UNICODE));
    }
}
add_action('save_post', 'corona_save_casino_meta');

/**
 * Enqueue media scripts for metabox
 */
function corona_casino_admin_scripts($hook) {
    if ($hook !== 'post-new.php' && $hook !== 'post.php') {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'corona_casino_admin_scripts');
