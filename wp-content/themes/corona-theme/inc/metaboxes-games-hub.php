<?php
/**
 * Games Hub Metaboxes
 *
 * @package Corona_Theme
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Check if current page uses games hub template
 */
function corona_is_games_hub_template() {
	global $post;
	if (!$post) return false;
	return get_page_template_slug($post->ID) === 'page-games-hub.php';
}

/**
 * Register metaboxes for Games Hub template
 */
function corona_games_hub_add_metaboxes() {
	if (corona_is_games_hub_template()) {
		add_meta_box(
			'games_hub_settings',
			__('Games Hub Settings', 'corona-theme'),
			'corona_games_hub_settings_callback',
			'page',
			'normal',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'corona_games_hub_add_metaboxes');

/**
 * Games Hub Settings metabox callback
 */
function corona_games_hub_settings_callback($post) {
	wp_nonce_field('corona_games_hub_nonce', 'corona_games_hub_nonce_field');

	$title = get_post_meta($post->ID, '_games_hub_title', true);
	$subtitle = get_post_meta($post->ID, '_games_hub_subtitle', true);
	$per_page = get_post_meta($post->ID, '_games_hub_per_page', true) ?: 24;
	$show_providers = get_post_meta($post->ID, '_games_hub_show_providers', true);
	?>

	<table class="form-table">
		<tr>
			<th><label for="games_hub_title"><?php _e('Page Title', 'corona-theme'); ?></label></th>
			<td>
				<input type="text" id="games_hub_title" name="games_hub_title" value="<?php echo esc_attr($title); ?>" class="regular-text" placeholder="<?php esc_attr_e('Free Online Slots & Casino Games', 'corona-theme'); ?>">
				<p class="description"><?php _e('Leave empty to use the page title.', 'corona-theme'); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="games_hub_subtitle"><?php _e('Subtitle', 'corona-theme'); ?></label></th>
			<td>
				<textarea id="games_hub_subtitle" name="games_hub_subtitle" class="large-text" rows="3" placeholder="<?php esc_attr_e('Play hundreds of free slot games from top providers', 'corona-theme'); ?>"><?php echo esc_textarea($subtitle); ?></textarea>
			</td>
		</tr>
		<tr>
			<th><label for="games_hub_per_page"><?php _e('Games Per Page', 'corona-theme'); ?></label></th>
			<td>
				<input type="number" id="games_hub_per_page" name="games_hub_per_page" value="<?php echo esc_attr($per_page); ?>" min="6" max="100" step="1" class="small-text">
				<p class="description"><?php _e('Number of games to display per page. Recommended: 24.', 'corona-theme'); ?></p>
			</td>
		</tr>
		<tr>
			<th><?php _e('Provider Filters', 'corona-theme'); ?></th>
			<td>
				<label>
					<input type="checkbox" id="games_hub_show_providers" name="games_hub_show_providers" value="1" <?php checked($show_providers, '1'); ?>>
					<?php _e('Show provider filter buttons', 'corona-theme'); ?>
				</label>
				<p class="description"><?php _e('Display filter buttons for game providers.', 'corona-theme'); ?></p>
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Save Games Hub metabox data
 */
function corona_save_games_hub_metabox($post_id) {
	// Verify nonce
	if (!isset($_POST['corona_games_hub_nonce_field']) ||
		!wp_verify_nonce($_POST['corona_games_hub_nonce_field'], 'corona_games_hub_nonce')) {
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

	// Save fields
	if (isset($_POST['games_hub_title'])) {
		update_post_meta($post_id, '_games_hub_title', sanitize_text_field($_POST['games_hub_title']));
	}

	if (isset($_POST['games_hub_subtitle'])) {
		update_post_meta($post_id, '_games_hub_subtitle', sanitize_textarea_field($_POST['games_hub_subtitle']));
	}

	if (isset($_POST['games_hub_per_page'])) {
		update_post_meta($post_id, '_games_hub_per_page', absint($_POST['games_hub_per_page']));
	}

	update_post_meta($post_id, '_games_hub_show_providers', isset($_POST['games_hub_show_providers']) ? '1' : '');
}
add_action('save_post', 'corona_save_games_hub_metabox');

/**
 * Register Polylang strings for Games Hub
 */
function corona_games_hub_register_strings() {
	if (function_exists('pll_register_string')) {
		pll_register_string('games-hub-all', 'All', 'Corona Theme');
		pll_register_string('games-hub-no-games', 'No games found.', 'Corona Theme');
	}
}
add_action('init', 'corona_games_hub_register_strings');
