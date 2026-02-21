<?php
/**
 * Template Name: Top Casinos List
 *
 * Page template for displaying top rated casinos list.
 * Based on EliteRoll HomePage design.
 *
 * @package Corona_Theme
 */

// Get page meta
$page_title = get_post_meta(get_the_ID(), '_review_page_title', true) ?: sprintf(__('Top Rated Casinos %s', 'corona-theme'), date('Y'));
$page_subtitle = get_post_meta(get_the_ID(), '_review_page_subtitle', true) ?: __('Our experts evaluate hundreds of online casinos every month to bring you the safest, fastest, and most rewarding gaming experiences.', 'corona-theme');

// Get casino pages to display
$casino_ids = get_post_meta(get_the_ID(), '_review_casino_list', true);
$casino_ids = $casino_ids ? json_decode($casino_ids, true) : array();

// If no specific casinos selected, get all casino pages (filtered by language)
if (empty($casino_ids)) {
	$current_lang = function_exists('pll_get_post_language') ? pll_get_post_language(get_the_ID()) : null;

	// Use WP_Query instead of get_pages (get_pages doesn't work well with meta filters)
	$query_args = array(
		'post_type' => 'page',
		'posts_per_page' => 50,
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'meta_key' => '_wp_page_template',
		'meta_value' => 'page-casino.php',
	);

	$casino_query = new WP_Query($query_args);
	$all_casino_pages = $casino_query->posts;

	// Filter by language if Polylang is active
	if ($current_lang && function_exists('pll_get_post_language')) {
		$casino_pages = array();
		foreach ($all_casino_pages as $page) {
			if (pll_get_post_language($page->ID) === $current_lang) {
				$casino_pages[] = $page;
			}
		}
		$casino_pages = array_slice($casino_pages, 0, 10);
	} else {
		$casino_pages = array_slice($all_casino_pages, 0, 10);
	}

	$casino_ids = wp_list_pluck($casino_pages, 'ID');
}

get_header();
?>

<main id="primary" class="site-main">
	<div class="casino-container">

		<!-- Page Header -->
		<header class="top-casinos-header">
			<h2 class="top-casinos-title">
				<?php
				// Split title into words and make the third word underlined
				$title_words = explode(' ', $page_title);
				if (count($title_words) >= 3) {
					// First two words normal
					echo esc_html($title_words[0] . ' ' . $title_words[1]) . ' ';
					// Third word underlined
					echo '<em>' . esc_html($title_words[2]) . '</em>';
					// Remaining words normal
					if (count($title_words) > 3) {
						echo ' ' . esc_html(implode(' ', array_slice($title_words, 3)));
					}
				} else {
					echo esc_html($page_title);
				}
				?>
			</h2>
			<p class="top-casinos-subtitle"><?php echo esc_html($page_subtitle); ?></p>
		</header>

		<!-- Casinos List -->
		<div class="top-casinos-list">
			<?php
			$position = 1;
			foreach ($casino_ids as $casino_id):
				// Get casino data
				$casino_title = get_the_title($casino_id);
				$casino_url = get_permalink($casino_id);
				$logo = get_post_meta($casino_id, '_casino_logo', true);
				if (empty($logo)) {
					$logo = get_post_meta($casino_id, '_slot_image', true);
				}
				$rating = get_post_meta($casino_id, '_casino_rating', true) ?: 5;
				$aff_link = function_exists('corona_get_casino_aff_link') ? corona_get_casino_aff_link($casino_id) : get_post_meta($casino_id, '_casino_aff_link', true);

				// Get tags
				$tags = get_post_meta($casino_id, '_casino_tags', true);
				$tags = $tags ? json_decode($tags, true) : array();
				if (empty($tags)) {
					// Default tags based on position
					if ($position == 1) {
						$tags = array(__('Premium Choice', 'corona-theme'), __('Fast Payouts', 'corona-theme'));
					} elseif ($position == 2) {
						$tags = array(__('Trusted', 'corona-theme'), __('Crypto Friendly', 'corona-theme'));
					} else {
						$tags = array(__('Slots Focused', 'corona-theme'));
					}
				}

				// Get first bonus
				$bonuses = get_post_meta($casino_id, '_casino_bonuses', true);
				$bonuses = $bonuses ? json_decode($bonuses, true) : array();
				$first_bonus = !empty($bonuses) ? $bonuses[0] : null;
			?>
			<article class="casino-list-card glass">
				<!-- Position Number -->
				<div class="casino-list-position">
					#<?php echo str_pad($position, 2, '0', STR_PAD_LEFT); ?>
				</div>

				<!-- Logo -->
				<div class="casino-list-logo">
					<?php if ($logo): ?>
					<img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($casino_title); ?>">
					<?php else: ?>
					<div class="casino-list-logo-placeholder"><?php echo esc_html(corona_mb_substr_safe($casino_title, 0, 1)); ?></div>
					<?php endif; ?>
				</div>

				<!-- Info -->
				<div class="casino-list-info">
					<div class="casino-list-tags">
						<?php foreach ($tags as $tag): ?>
						<span class="casino-list-tag"><?php echo esc_html(strtoupper($tag)); ?></span>
						<?php endforeach; ?>
					</div>
					<h2 class="casino-list-name"><?php echo esc_html($casino_title); ?></h2>
					<div class="casino-list-rating">
						<div class="casino-list-stars">
							<?php
							$full_stars = floor($rating);
							$has_half = ($rating - $full_stars) >= 0.5;
							for ($i = 1; $i <= 5; $i++):
								if ($i <= $full_stars): ?>
									<span class="star filled">★</span>
								<?php elseif ($i == $full_stars + 1 && $has_half): ?>
									<span class="star half">★</span>
								<?php else: ?>
									<span class="star">★</span>
								<?php endif;
							endfor; ?>
						</div>
						<span class="casino-list-rating-value"><?php echo esc_html($rating); ?>/5</span>
					</div>
				</div>

				<!-- Bonus -->
				<?php if ($first_bonus): ?>
				<div class="casino-list-bonus">
					<span class="casino-list-bonus-label"><?php esc_html_e('Exclusive Bonus', 'corona-theme'); ?></span>
					<span class="casino-list-bonus-value"><?php echo esc_html($first_bonus['name']); ?></span>
					<?php if (!empty($first_bonus['description'])): ?>
					<span class="casino-list-bonus-desc"><?php echo esc_html($first_bonus['description']); ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<!-- Actions -->
				<div class="casino-list-actions">
					<a href="<?php echo esc_url($aff_link); ?>" class="casino-list-btn-primary" target="_blank" rel="noopener nofollow">
						<?php esc_html_e('Visit Casino', 'corona-theme'); ?>
					</a>
					<a href="<?php echo esc_url($casino_url); ?>" class="casino-list-btn-review">
						<?php esc_html_e('Read Full Review', 'corona-theme'); ?>
					</a>
				</div>
			</article>
			<?php
			$position++;
			endforeach;
			?>
		</div>

		<!-- Content / How We Review -->
		<?php if (get_the_content()): ?>
		<section class="how-we-review glass">
			<div class="how-we-review-content">
				<?php the_content(); ?>
			</div>
		</section>
		<?php endif; ?>

	</div>
</main>

<?php
get_footer();
