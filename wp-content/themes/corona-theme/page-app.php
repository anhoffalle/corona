<?php
/**
 * Template Name: Mobile App
 *
 * App Store style page for casino mobile applications.
 *
 * @package Corona_Theme
 */

// Get meta data
$logo = get_post_meta(get_the_ID(), '_app_logo', true);
$app_name = get_post_meta(get_the_ID(), '_app_name', true) ?: get_the_title();
$developer = get_post_meta(get_the_ID(), '_app_developer', true);
if (empty($developer)) {
	$developer = function_exists('corona_get_default_app_developer') ? corona_get_default_app_developer() : 'Games Ltd.';
}
$rating = get_post_meta(get_the_ID(), '_app_rating', true);
if (empty($rating)) {
	$rating = function_exists('corona_get_default_app_rating') ? corona_get_default_app_rating() : '4.8';
}
$ratings_count = get_post_meta(get_the_ID(), '_app_ratings_count', true) ?: '4.2K';
$category_rank = get_post_meta(get_the_ID(), '_app_category_rank', true) ?: '#1';
$age_rating = get_post_meta(get_the_ID(), '_app_age_rating', true) ?: '18+';
$app_size = get_post_meta(get_the_ID(), '_app_size', true) ?: '142.8 MB';
$app_version = get_post_meta(get_the_ID(), '_app_version', true) ?: '4.8.2';
$app_url = get_post_meta(get_the_ID(), '_app_download_url', true);
$whats_new = get_post_meta(get_the_ID(), '_app_whats_new', true);
$casino_page_id = get_post_meta(get_the_ID(), '_app_casino_page', true);

// Affiliate link fallback: app page → casino page → default
$aff_link = $app_url;
if (empty($aff_link) && $casino_page_id) {
	$aff_link = get_post_meta($casino_page_id, '_casino_aff_link', true);
}
if (empty($aff_link) && function_exists('corona_get_default_aff_link')) {
	$aff_link = corona_get_default_aff_link();
}

// Screenshots logic: custom > default (if enabled) > none
$screenshots = get_post_meta(get_the_ID(), '_app_screenshots', true);
$screenshots = $screenshots ? json_decode($screenshots, true) : array();

// If no custom screenshots and "use default" is enabled, get default screenshots
if (empty($screenshots)) {
	$use_default = get_post_meta(get_the_ID(), '_app_use_default_screenshots', true);
	if ($use_default && function_exists('corona_get_default_app_screenshots')) {
		$screenshots = corona_get_default_app_screenshots();
	}
}

// Fallback to casino meta
if (empty($logo) && $casino_page_id) {
	$logo = get_post_meta($casino_page_id, '_casino_logo', true);
	if (empty($logo)) {
		$logo = get_post_meta($casino_page_id, '_slot_image', true);
	}
}

// Rating bars distribution
$rating_bars = array(
	array('stars' => 5, 'percent' => 85),
	array('stars' => 4, 'percent' => 10),
	array('stars' => 3, 'percent' => 3),
	array('stars' => 2, 'percent' => 1),
	array('stars' => 1, 'percent' => 1),
);

get_header();
?>

<main id="primary" class="site-main app-page">
<?php while (have_posts()) : the_post(); ?>
	<div class="app-container">

		<!-- App Header -->
		<div class="app-header">
			<div class="app-icon-wrapper">
				<?php if ($logo): ?>
				<img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($app_name); ?>" class="app-icon-img">
				<?php else: ?>
				<div class="app-icon-placeholder"><?php echo esc_html(corona_mb_substr_safe($app_name, 0, 1)); ?></div>
				<?php endif; ?>
				<span class="app-badge"><?php echo esc_html(corona_pll__("Editors' Choice")); ?></span>
			</div>

			<div class="app-info">
				<h2 class="app-title"><?php echo esc_html($app_name); ?></h2>
				<p class="app-developer"><?php echo esc_html($developer); ?></p>

				<div class="app-stats">
					<div class="app-stat">
						<span class="app-stat-value"><?php echo esc_html($rating); ?> <span class="app-stat-star">★</span></span>
						<span class="app-stat-label"><?php echo esc_html($ratings_count); ?> <?php echo esc_html(corona_pll__('Ratings')); ?></span>
					</div>
					<div class="app-stat-divider"></div>
					<div class="app-stat">
						<span class="app-stat-value"><?php echo esc_html($category_rank); ?></span>
						<span class="app-stat-label"><?php echo esc_html(corona_pll__('in Casino')); ?></span>
					</div>
					<div class="app-stat-divider"></div>
					<div class="app-stat">
						<span class="app-stat-value"><?php echo esc_html($age_rating); ?></span>
						<span class="app-stat-label"><?php echo esc_html(corona_pll__('Age')); ?></span>
					</div>
				</div>

				<div class="app-actions">
					<a href="<?php echo $aff_link ? esc_url($aff_link) : '#'; ?>" class="app-btn app-btn-primary" <?php echo $aff_link ? 'target="_blank" rel="noopener nofollow"' : ''; ?>>
						<?php echo esc_html(corona_get_app_btn_text()); ?>
					</a>
				</div>
				<p class="app-purchases"><?php echo esc_html(corona_get_app_purchases_text()); ?></p>
			</div>
		</div>

		<!-- Screenshots -->
		<?php if (!empty($screenshots)): ?>
		<section class="app-screenshots">
			<h2 class="app-section-title"><?php echo esc_html(corona_pll__('Screenshots')); ?></h2>
			<div class="app-screenshots-carousel">
				<?php foreach ($screenshots as $i => $screenshot): ?>
				<div class="app-screenshot">
					<img src="<?php echo esc_url($screenshot); ?>" alt="<?php printf(esc_attr__('Screenshot %d', 'corona-theme'), $i + 1); ?>">
				</div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

		<!-- What's New -->
		<?php if ($whats_new): ?>
		<section class="app-whats-new-section">
			<div class="app-whats-new">
				<h3 class="app-whats-new-title"><?php echo esc_html(corona_pll__("What's New")); ?></h3>
				<div class="app-whats-new-meta">
					<span><?php echo esc_html(corona_pll__('Version')); ?> <?php echo esc_html($app_version); ?></span>
				</div>
				<div class="app-whats-new-content">
					<?php echo wp_kses_post($whats_new); ?>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<!-- Ratings & Reviews -->
		<section class="app-ratings">
			<h2 class="app-section-title"><?php echo esc_html(corona_pll__('Ratings & Reviews')); ?></h2>
			<div class="app-ratings-content">
				<div class="app-ratings-score">
					<span class="app-ratings-number"><?php echo esc_html($rating); ?></span>
					<span class="app-ratings-label"><?php echo esc_html(corona_pll__('out of 5')); ?></span>
				</div>

				<div class="app-ratings-bars">
					<?php foreach ($rating_bars as $bar): ?>
					<div class="app-ratings-bar-row">
						<div class="app-ratings-stars">
							<?php for ($i = 0; $i < $bar['stars']; $i++): ?>
							<span class="app-ratings-dot"></span>
							<?php endfor; ?>
						</div>
						<div class="app-ratings-bar">
							<div class="app-ratings-bar-fill" style="width: <?php echo esc_attr($bar['percent']); ?>%"></div>
						</div>
					</div>
					<?php endforeach; ?>
					<div class="app-ratings-count"><?php echo esc_html($ratings_count); ?> <?php echo esc_html(corona_pll__('Ratings')); ?></div>
				</div>
			</div>
		</section>

		<!-- Information -->
		<section class="app-information">
			<h2 class="app-section-title"><?php echo esc_html(corona_pll__('Information')); ?></h2>
			<div class="app-info-table">
				<?php
				$info_items = array(
					array('label' => corona_pll__('Provider'), 'value' => $developer),
					array('label' => corona_pll__('Size'), 'value' => $app_size),
					array('label' => corona_pll__('Category'), 'value' => corona_pll__('Games: Casino')),
					array('label' => corona_pll__('Compatibility'), 'value' => corona_pll__('Requires iOS 15.0 or later')),
					array('label' => corona_pll__('Languages'), 'value' => corona_pll__('English, Russian, Spanish, German')),
					array('label' => corona_pll__('Age Rating'), 'value' => $age_rating . ' (' . corona_pll__('Simulated Gambling') . ')'),
					array('label' => corona_pll__('Copyright'), 'value' => '© ' . date('Y') . ' ' . $app_name . ' ' . corona_pll__('Inc.')),
				);
				foreach ($info_items as $item):
				?>
				<div class="app-info-row">
					<span class="app-info-label"><?php echo esc_html($item['label']); ?></span>
					<span class="app-info-value"><?php echo esc_html($item['value']); ?></span>
				</div>
				<?php endforeach; ?>
			</div>
		</section>

		<!-- Page Content -->
		<?php if (trim(strip_tags(get_the_content()))): ?>
		<div class="app-page-content">
			<?php the_content(); ?>
		</div>
		<?php endif; ?>

		<!-- Back to Casino Link -->
		<?php if ($casino_page_id): ?>
		<div class="app-back-link">
			<a href="<?php echo esc_url(get_permalink($casino_page_id)); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><polyline points="15 18 9 12 15 6"></polyline></svg>
				<?php echo esc_html(corona_pll__('Back to Casino Review')); ?>
			</a>
		</div>
		<?php endif; ?>

		<!-- Disclaimer -->
		<div class="app-disclaimer">
			<?php echo esc_html(corona_pll__('Apple, the Apple logo, iPhone, and iPad are trademarks of Apple Inc., registered in the U.S. and other countries and regions. App Store is a service mark of Apple Inc. Google Play and the Google Play logo are trademarks of Google LLC.')); ?>
		</div>

	</div>
<?php endwhile; ?>
</main>

<script>
(function() {
	var c = document.querySelector('.app-screenshots-carousel');
	if (!c) return;
	var drag = false, sx, sl;
	c.onmousedown = function(e) {
		e.preventDefault();
		drag = true;
		sx = e.clientX;
		sl = c.scrollLeft;
		c.classList.add('dragging');
	};
	window.onmouseup = function() {
		drag = false;
		c.classList.remove('dragging');
	};
	window.onmousemove = function(e) {
		if (drag) c.scrollLeft = sl - (e.clientX - sx);
	};
})();
</script>

<?php
get_footer();
