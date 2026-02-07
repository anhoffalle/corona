<?php
/**
 * Template Name: Homepage
 *
 * Homepage template with hero, top casinos, popular games, and SEO content.
 *
 * @package Corona_Theme
 */

// Get meta data
$hero_title = get_post_meta(get_the_ID(), '_home_hero_title', true);
$hero_subtitle = get_post_meta(get_the_ID(), '_home_hero_subtitle', true);
$hero_btn_text = get_post_meta(get_the_ID(), '_home_hero_btn_text', true);
$hero_btn_url = get_post_meta(get_the_ID(), '_home_hero_btn_url', true);

$casino_ids = get_post_meta(get_the_ID(), '_home_casinos', true);
$casino_ids = $casino_ids ? json_decode($casino_ids, true) : array();

$game_ids = get_post_meta(get_the_ID(), '_home_games', true);
$game_ids = $game_ids ? json_decode($game_ids, true) : array();

$casinos_title = get_post_meta(get_the_ID(), '_home_casinos_title', true) ?: (function_exists('pll__') ? pll__('Top Casinos') : __('Top Casinos', 'corona-theme'));
$games_title = get_post_meta(get_the_ID(), '_home_games_title', true) ?: (function_exists('pll__') ? pll__('Popular Games') : __('Popular Games', 'corona-theme'));

// Defaults
if (empty($hero_title)) {
	$hero_title = get_the_title();
}
if (empty($hero_btn_text)) {
	$hero_btn_text = function_exists('pll__') ? pll__('View All Casinos') : __('View All Casinos', 'corona-theme');
}

get_header();
?>

<main id="primary" class="site-main home-page">
	<div class="home-container">

		<!-- Hero Section -->
		<section class="home-hero glass">
			<div class="home-hero-content">
				<h1 class="home-hero-title"><?php echo esc_html($hero_title); ?></h1>
				<?php if ($hero_subtitle): ?>
				<p class="home-hero-subtitle"><?php echo esc_html($hero_subtitle); ?></p>
				<?php endif; ?>
				<?php if ($hero_btn_url): ?>
				<a href="<?php echo esc_url($hero_btn_url); ?>" class="btn-cta emerald-gradient">
					<?php echo esc_html($hero_btn_text); ?>
					<?php echo corona_icon('chevron-right'); ?>
				</a>
				<?php endif; ?>
			</div>
		</section>

		<!-- Top Casinos -->
		<?php if (!empty($casino_ids)): ?>
		<section class="home-section">
			<div class="home-section-header">
				<h2 class="home-section-title"><?php echo esc_html($casinos_title); ?></h2>
				<?php if ($hero_btn_url): ?>
				<a href="<?php echo esc_url($hero_btn_url); ?>" class="home-section-link">
					<?php echo function_exists('pll__') ? esc_html(pll__('View All')) : esc_html__('View All', 'corona-theme'); ?>
					<?php echo corona_icon('chevron-right'); ?>
				</a>
				<?php endif; ?>
			</div>
			<div class="home-casinos-grid">
				<?php foreach ($casino_ids as $casino_id):
					$casino = get_post($casino_id);
					if (!$casino) continue;

					$logo = get_post_meta($casino_id, '_casino_logo', true);
					if (!$logo) $logo = get_post_meta($casino_id, '_slot_image', true);
					$rating = get_post_meta($casino_id, '_casino_rating', true) ?: 5;
					$aff_link = function_exists('corona_get_casino_aff_link')
						? corona_get_casino_aff_link($casino_id)
						: get_post_meta($casino_id, '_casino_aff_link', true);
					if (!$aff_link) $aff_link = get_permalink($casino_id);

					// Get first bonus
					$bonuses = get_post_meta($casino_id, '_casino_bonuses', true);
					$bonuses = $bonuses ? json_decode($bonuses, true) : array();
					$first_bonus = !empty($bonuses) ? $bonuses[0] : null;
				?>
				<div class="home-casino-card glass">
					<?php if ($logo): ?>
					<img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($casino->post_title); ?>" class="home-casino-logo">
					<?php else: ?>
					<div class="home-casino-logo-placeholder"><?php echo esc_html(corona_mb_substr_safe($casino->post_title, 0, 2)); ?></div>
					<?php endif; ?>

					<div class="home-casino-info">
						<h3 class="home-casino-name"><?php echo esc_html($casino->post_title); ?></h3>
						<div class="home-casino-rating">
							<?php for ($i = 1; $i <= 5; $i++): ?>
							<span class="star <?php echo $i <= $rating ? 'filled' : ''; ?>">★</span>
							<?php endfor; ?>
						</div>
						<?php if ($first_bonus && !empty($first_bonus['description'])): ?>
						<p class="home-casino-bonus"><?php echo esc_html($first_bonus['description']); ?></p>
						<?php endif; ?>
					</div>

					<a href="<?php echo esc_url($aff_link); ?>" class="home-casino-btn emerald-gradient" target="_blank" rel="noopener nofollow">
						<?php echo function_exists('pll__') ? esc_html(pll__('PLAY')) : esc_html__('PLAY', 'corona-theme'); ?>
					</a>
				</div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

		<!-- Popular Games -->
		<?php if (!empty($game_ids)): ?>
		<section class="home-section">
			<div class="home-section-header">
				<h2 class="home-section-title"><?php echo esc_html($games_title); ?></h2>
			</div>
			<div class="home-games-grid">
				<?php foreach ($game_ids as $game_id):
					$game = get_post($game_id);
					if (!$game) continue;

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
						if (strpos($label, 'provider') !== false || strpos($label, 'провайдер') !== false) {
							$provider = $char['value'];
							break;
						}
					}
				?>
				<a href="<?php echo esc_url(get_permalink($game_id)); ?>" class="home-game-card">
					<?php if ($image): ?>
					<div class="home-game-image">
						<img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($game->post_title); ?>">
						<?php if ($demo_url): ?>
						<div class="home-game-play-overlay">
							<span class="home-game-play-icon">▶</span>
						</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<div class="home-game-info">
						<h3 class="home-game-name"><?php echo esc_html($game->post_title); ?></h3>
						<?php if ($provider): ?>
						<span class="home-game-provider"><?php echo esc_html($provider); ?></span>
						<?php endif; ?>
					</div>
				</a>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

		<!-- SEO Content -->
		<?php if (get_the_content()): ?>
		<section class="home-content glass">
			<div class="home-content-inner">
				<?php the_content(); ?>
			</div>
		</section>
		<?php endif; ?>

	</div>
</main>

<?php
get_footer();
