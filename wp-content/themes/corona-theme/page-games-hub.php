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
$games_per_page = max(1, absint(get_post_meta(get_the_ID(), '_games_hub_per_page', true) ?: 24));
$show_providers = get_post_meta(get_the_ID(), '_games_hub_show_providers', true);

// Defaults
if (empty($hero_title)) {
	$hero_title = get_the_title();
}

// Get current page for pagination
$paged = max(1, (int) get_query_var('paged'));

// Get filter parameters
$provider_filter = isset($_GET['provider']) ? sanitize_text_field(wp_unslash($_GET['provider'])) : '';
$current_lang = function_exists('pll_current_language') ? pll_current_language() : '';

$extract_provider = static function($game_id) {
	$characteristics = get_post_meta($game_id, '_game_characteristics', true);
	$characteristics = $characteristics ? json_decode($characteristics, true) : array();
	if (empty($characteristics)) {
		$characteristics = get_post_meta($game_id, '_slot_custom_characteristics', true);
		$characteristics = $characteristics ? json_decode($characteristics, true) : array();
	}
	foreach ($characteristics as $char) {
		$label = isset($char['label']) ? strtolower($char['label']) : '';
		if (strpos($label, 'provider') !== false || strpos($label, 'РїСЂРѕРІР°Р№РґРµСЂ') !== false || strpos($label, 'proveedor') !== false) {
			return isset($char['value']) ? trim($char['value']) : '';
		}
	}
	return '';
};

// Build full IDs list first, then paginate the filtered IDs.
$all_games_args = array(
	'post_type' => 'page',
	'post_status' => 'publish',
	'posts_per_page' => -1,
	'fields' => 'ids',
	'meta_query' => array(
		array(
			'key' => '_wp_page_template',
			'value' => 'page-game.php',
		),
	),
	'orderby' => 'title',
	'order' => 'ASC',
);
if ($current_lang !== '') {
	$all_games_args['lang'] = $current_lang;
}

$all_game_ids = get_posts($all_games_args);
$provider_map = array();
$all_providers = array();
foreach ($all_game_ids as $game_id) {
	$provider = $extract_provider($game_id);
	$provider_map[$game_id] = $provider;
	if ($show_providers && $provider && !in_array($provider, $all_providers, true)) {
		$all_providers[] = $provider;
	}
}
if ($show_providers) {
	sort($all_providers);
}

$filtered_game_ids = $all_game_ids;
if ($provider_filter !== '') {
	$filtered_game_ids = array_values(array_filter(
		$all_game_ids,
		static function($game_id) use ($provider_filter, $provider_map) {
			return isset($provider_map[$game_id]) && $provider_map[$game_id] === $provider_filter;
		}
	));
}

$total = count($filtered_game_ids);
$max_num_pages = $total > 0 ? (int) ceil($total / $games_per_page) : 0;
if ($max_num_pages > 0 && $paged > $max_num_pages) {
	$paged = $max_num_pages;
}

$offset = ($paged - 1) * $games_per_page;
$paged_game_ids = array_slice($filtered_game_ids, $offset, $games_per_page);

$games_args = array(
	'post_type' => 'page',
	'post_status' => 'publish',
	'posts_per_page' => $games_per_page,
	'post__in' => !empty($paged_game_ids) ? $paged_game_ids : array(0),
	'orderby' => 'post__in',
);
if ($current_lang !== '') {
	$games_args['lang'] = $current_lang;
}
$games_query = new WP_Query($games_args);

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
				<a href="<?php echo esc_url(add_query_arg('provider', $provider, get_permalink())); ?>"
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
				$provider = isset($provider_map[$game_id]) ? $provider_map[$game_id] : '';
			?>
			<a href="<?php the_permalink(); ?>" class="games-hub-card">
				<?php if ($image): ?>
				<div class="games-hub-card-image">
					<img src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
					<?php if ($demo_url): ?>
					<div class="games-hub-play-overlay">
						<span class="games-hub-play-icon">&#9654;</span>
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
		<?php if ($max_num_pages > 1): ?>
		<nav class="games-hub-pagination">
			<?php
			echo paginate_links(array(
				'total' => $max_num_pages,
				'current' => $paged,
				'add_args' => $provider_filter !== '' ? array('provider' => $provider_filter) : false,
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
