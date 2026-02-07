<?php
/**
 * Template Name: Games Hub
 *
 * Games hub page template - lists all games with filtering options.
 *
 * @package Corona_Theme
 */

// Get meta data
$hero_title = get_post_meta(get_the_ID(), '_games_hub_title', true);
$hero_subtitle = get_post_meta(get_the_ID(), '_games_hub_subtitle', true);
$games_per_page = get_post_meta(get_the_ID(), '_games_hub_per_page', true) ?: 24;
$show_providers = get_post_meta(get_the_ID(), '_games_hub_show_providers', true);

// Defaults
if (empty($hero_title)) {
	$hero_title = get_the_title();
}

// Get current page for pagination
$paged = get_query_var('paged') ? get_query_var('paged') : 1;

// Get filter parameters
$provider_filter = isset($_GET['provider']) ? sanitize_text_field($_GET['provider']) : '';

// Query games - find pages using game template
$args = array(
	'post_type' => 'page',
	'posts_per_page' => $games_per_page,
	'paged' => $paged,
	'meta_query' => array(
		array(
			'key' => '_wp_page_template',
			'value' => 'page-game.php',
		),
	),
	'orderby' => 'title',
	'order' => 'ASC',
);

// Apply language filter if Polylang is active
if (function_exists('pll_current_language')) {
	$args['lang'] = pll_current_language();
}

$games_query = new WP_Query($args);

// Get all providers for filter (from all games)
$all_providers = array();
if ($show_providers) {
	$all_games_args = array(
		'post_type' => 'page',
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key' => '_wp_page_template',
				'value' => 'page-game.php',
			),
		),
		'fields' => 'ids',
	);
	if (function_exists('pll_current_language')) {
		$all_games_args['lang'] = pll_current_language();
	}
	$all_games_ids = get_posts($all_games_args);

	foreach ($all_games_ids as $game_id) {
		$characteristics = get_post_meta($game_id, '_game_characteristics', true);
		$characteristics = $characteristics ? json_decode($characteristics, true) : array();
		if (empty($characteristics)) {
			$characteristics = get_post_meta($game_id, '_slot_custom_characteristics', true);
			$characteristics = $characteristics ? json_decode($characteristics, true) : array();
		}
		foreach ($characteristics as $char) {
			$label = strtolower($char['label']);
			if (strpos($label, 'provider') !== false || strpos($label, 'провайдер') !== false || strpos($label, 'proveedor') !== false) {
				$provider = trim($char['value']);
				if ($provider && !in_array($provider, $all_providers)) {
					$all_providers[] = $provider;
				}
				break;
			}
		}
	}
	sort($all_providers);
}

get_header();
?>

<main id="primary" class="site-main games-hub-page">
	<div class="games-hub-container">

		<!-- Hero Section -->
		<section class="games-hub-hero glass">
			<h1 class="games-hub-title"><?php echo esc_html($hero_title); ?></h1>
			<?php if ($hero_subtitle): ?>
			<p class="games-hub-subtitle"><?php echo esc_html($hero_subtitle); ?></p>
			<?php endif; ?>
		</section>

		<!-- Filters -->
		<?php if ($show_providers && !empty($all_providers)): ?>
		<section class="games-hub-filters">
			<div class="games-hub-filter-list">
				<a href="<?php echo esc_url(get_permalink()); ?>" class="games-hub-filter-btn <?php echo empty($provider_filter) ? 'active' : ''; ?>">
					<?php echo function_exists('pll__') ? esc_html(pll__('All')) : esc_html__('All', 'corona-theme'); ?>
				</a>
				<?php foreach ($all_providers as $provider): ?>
				<a href="<?php echo esc_url(add_query_arg('provider', urlencode($provider), get_permalink())); ?>"
				   class="games-hub-filter-btn <?php echo $provider_filter === $provider ? 'active' : ''; ?>">
					<?php echo esc_html($provider); ?>
				</a>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

		<!-- Games Count -->
		<div class="games-hub-count">
			<?php
			$total = $games_query->found_posts;
			printf(
				esc_html(_n('%s game', '%s games', $total, 'corona-theme')),
				number_format_i18n($total)
			);
			?>
		</div>

		<!-- Games Grid -->
		<?php if ($games_query->have_posts()): ?>
		<div class="games-hub-grid">
			<?php while ($games_query->have_posts()): $games_query->the_post();
				$game_id = get_the_ID();
				$image = get_post_meta($game_id, '_game_image', true);
				if (!$image) $image = get_post_meta($game_id, '_slot_image', true);
				$demo_url = get_post_meta($game_id, '_game_demo_url', true);
				if (!$demo_url) $demo_url = get_post_meta($game_id, '_slot_demo_iframe', true);

				// Get provider
				$characteristics = get_post_meta($game_id, '_game_characteristics', true);
				$characteristics = $characteristics ? json_decode($characteristics, true) : array();
				if (empty($characteristics)) {
					$characteristics = get_post_meta($game_id, '_slot_custom_characteristics', true);
					$characteristics = $characteristics ? json_decode($characteristics, true) : array();
				}
				$provider = '';
				foreach ($characteristics as $char) {
					$label = strtolower($char['label']);
					if (strpos($label, 'provider') !== false || strpos($label, 'провайдер') !== false || strpos($label, 'proveedor') !== false) {
						$provider = $char['value'];
						break;
					}
				}

				// Skip if provider filter active and doesn't match
				if ($provider_filter && $provider !== $provider_filter) {
					continue;
				}
			?>
			<a href="<?php the_permalink(); ?>" class="games-hub-card">
				<?php if ($image): ?>
				<div class="games-hub-card-image">
					<img src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
					<?php if ($demo_url): ?>
					<div class="games-hub-play-overlay">
						<span class="games-hub-play-icon">▶</span>
					</div>
					<?php endif; ?>
				</div>
				<?php else: ?>
				<div class="games-hub-card-placeholder">
					<span><?php echo esc_html(mb_substr(get_the_title(), 0, 2)); ?></span>
				</div>
				<?php endif; ?>
				<div class="games-hub-card-info">
					<h2 class="games-hub-card-title"><?php the_title(); ?></h2>
					<?php if ($provider): ?>
					<span class="games-hub-card-provider"><?php echo esc_html($provider); ?></span>
					<?php endif; ?>
				</div>
			</a>
			<?php endwhile; ?>
		</div>

		<!-- Pagination -->
		<?php if ($games_query->max_num_pages > 1): ?>
		<nav class="games-hub-pagination">
			<?php
			echo paginate_links(array(
				'total' => $games_query->max_num_pages,
				'current' => $paged,
				'prev_text' => '&laquo;',
				'next_text' => '&raquo;',
				'type' => 'list',
			));
			?>
		</nav>
		<?php endif; ?>

		<?php else: ?>
		<div class="games-hub-empty">
			<p><?php echo function_exists('pll__') ? esc_html(pll__('No games found.')) : esc_html__('No games found.', 'corona-theme'); ?></p>
		</div>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>

		<!-- SEO Content -->
		<?php if (get_the_content()): ?>
		<section class="games-hub-content glass">
			<div class="games-hub-content-inner">
				<?php the_content(); ?>
			</div>
		</section>
		<?php endif; ?>

	</div>
</main>

<?php
get_footer();
