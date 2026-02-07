<?php
/**
 * Template Name: Casino Review
 *
 * Modern casino review page with EliteRoll design.
 *
 * @package Corona_Theme
 */

// Get meta data before header
$logo = get_post_meta(get_the_ID(), '_casino_logo', true);
$aff_link = function_exists('corona_get_casino_aff_link') ? corona_get_casino_aff_link(get_the_ID()) : get_post_meta(get_the_ID(), '_casino_aff_link', true);
$characteristics = get_post_meta(get_the_ID(), '_casino_characteristics', true);
$characteristics = $characteristics ? json_decode($characteristics, true) : array();
$bonuses = get_post_meta(get_the_ID(), '_casino_bonuses', true);
$bonuses = $bonuses ? json_decode($bonuses, true) : array();
$payment_methods = get_post_meta(get_the_ID(), '_casino_payment_methods', true);
$payment_methods = $payment_methods ? json_decode($payment_methods, true) : array();
$rating = get_post_meta(get_the_ID(), '_casino_rating', true) ?: 5;
$extra_buttons = get_post_meta(get_the_ID(), '_casino_extra_buttons', true);
$extra_buttons = $extra_buttons ? json_decode($extra_buttons, true) : array();

// Fallback to old meta
if (empty($characteristics)) {
	$old_chars = get_post_meta(get_the_ID(), '_slot_custom_characteristics', true);
	if ($old_chars) {
		$characteristics = json_decode($old_chars, true) ?: array();
	}
}
if (empty($logo)) {
	$logo = get_post_meta(get_the_ID(), '_slot_image', true);
}

// Get first bonus for highlight
$first_bonus = !empty($bonuses) ? $bonuses[0] : null;

get_header();
?>

<main id="primary" class="site-main">
<?php while (have_posts()) : the_post(); ?>
	<div class="casino-container">
		<div class="casino-layout">

			<!-- Main Content -->
			<div class="casino-main">

				<!-- Hero Section -->
				<div class="casino-hero glass">
					<div class="casino-hero-inner">
						<div class="casino-hero-top">
							<!-- Logo -->
							<?php if ($logo): ?>
							<div class="casino-logo-wrapper">
								<div class="casino-logo-glow"></div>
								<img src="<?php echo esc_url($logo); ?>" alt="<?php the_title_attribute(); ?>" class="casino-logo-img">
							</div>
							<?php endif; ?>

							<!-- Info -->
							<div class="casino-info">
								<div class="casino-badges">
									<span class="badge badge-gold"><?php esc_html_e('Premium Choice', 'corona-theme'); ?></span>
									<span class="badge badge-emerald"><?php esc_html_e('Fast Payouts', 'corona-theme'); ?></span>
								</div>

								<h2 class="casino-title"><?php the_title(); ?></h2>

								<div class="casino-rating-wrapper">
									<div class="casino-stars">
										<?php for ($i = 1; $i <= 5; $i++): ?>
											<span class="casino-star <?php echo $i <= $rating ? 'filled' : ''; ?>">
												<?php echo corona_icon('star'); ?>
											</span>
										<?php endfor; ?>
										<span class="casino-rating-value"><?php echo esc_html($rating); ?>/5</span>
									</div>

									<div class="rating-divider"></div>

									<?php
									// Find license in characteristics
									$license = '';
									foreach ($characteristics as $char) {
										if (stripos($char['label'], 'licen') !== false) {
											$license = $char['value'];
											break;
										}
									}
									if ($license): ?>
									<div class="casino-license">
										<?php echo corona_icon('shield-check'); ?>
										<span><?php echo esc_html($license); ?></span>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<!-- Bottom CTA -->
						<div class="casino-hero-bottom">
							<div class="casino-verdict">
								<p class="casino-verdict-label"><?php esc_html_e('Expert Verdict', 'corona-theme'); ?></p>
								<p class="casino-verdict-text">
									<?php esc_html_e('Highly Recommended', 'corona-theme'); ?>
									<?php echo corona_icon('check-circle'); ?>
								</p>
							</div>

							<div class="casino-cta-wrapper">
								<a href="<?php echo esc_url($aff_link); ?>" class="btn-cta emerald-gradient" target="_blank" rel="noopener nofollow">
									<?php esc_html_e('VISIT CASINO', 'corona-theme'); ?>
									<?php echo corona_icon('external-link'); ?>
								</a>
								<p class="casino-cta-disclaimer"><?php esc_html_e('T&Cs Apply | 18+ Only | Play Responsibly', 'corona-theme'); ?></p>
							</div>
						</div>
					</div>
				</div>

				<!-- Bonus Card -->
				<?php if ($first_bonus): ?>
				<div class="bonus-card-wrapper gold-gradient">
					<div class="bonus-card">
						<div class="bonus-card-content">
							<div class="bonus-card-label">
								<?php echo corona_icon('trophy'); ?>
								<span><?php esc_html_e('Exclusive Bonus', 'corona-theme'); ?></span>
							</div>
							<h2 class="bonus-card-title"><?php echo esc_html($first_bonus['name']); ?></h2>
							<?php if (!empty($first_bonus['description'])): ?>
							<p class="bonus-card-description"><?php echo esc_html($first_bonus['description']); ?></p>
							<?php endif; ?>
						</div>
						<a href="<?php echo esc_url($first_bonus['btn_url'] ?: $aff_link); ?>" class="btn-bonus gold-gradient" target="_blank" rel="noopener nofollow">
							<?php echo esc_html($first_bonus['btn_text'] ?: __('GET BONUS', 'corona-theme')); ?>
							<?php echo corona_icon('chevron-right'); ?>
						</a>
					</div>
				</div>
				<?php endif; ?>

				<!-- Characteristics Table -->
				<?php if (!empty($characteristics)): ?>
				<section class="characteristics-section glass">
					<div class="characteristics-header">
						<div class="characteristics-icon">
							<?php echo corona_icon('info'); ?>
						</div>
						<h2 class="characteristics-title"><?php esc_html_e('Full Brand Characteristics', 'corona-theme'); ?></h2>
					</div>
					<table class="characteristics-table">
						<tbody>
							<?php foreach ($characteristics as $char): ?>
							<tr>
								<th><?php echo esc_html($char['label']); ?></th>
								<td><?php echo esc_html($char['value']); ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</section>
				<?php endif; ?>

				<!-- Additional Bonuses -->
				<?php if (count($bonuses) > 1): ?>
				<section class="bonuses-section glass">
					<h2 class="characteristics-title"><?php esc_html_e('All Bonuses', 'corona-theme'); ?></h2>
					<div class="bonuses-list">
						<?php foreach (array_slice($bonuses, 1) as $bonus): ?>
						<div class="bonus-item">
							<div>
								<h3 class="bonus-item-title"><?php echo esc_html($bonus['name']); ?></h3>
								<?php if (!empty($bonus['description'])): ?>
								<p class="bonus-item-desc"><?php echo esc_html($bonus['description']); ?></p>
								<?php endif; ?>
							</div>
							<a href="<?php echo esc_url($bonus['btn_url'] ?: $aff_link); ?>" class="btn-bonus btn-bonus-small gold-gradient" target="_blank" rel="noopener nofollow">
								<?php echo esc_html($bonus['btn_text'] ?: __('Claim Bonus', 'corona-theme')); ?>
							</a>
						</div>
						<?php endforeach; ?>
					</div>
				</section>
				<?php endif; ?>

				<!-- Expert Review / Content -->
				<section class="expert-review glass">
					<h2 class="expert-review-title"><?php esc_html_e('Detailed Expert Verdict', 'corona-theme'); ?></h2>
					<div class="expert-review-content">
						<?php the_content(); ?>
					</div>
				</section>

			</div>

		</div>
	</div>
<?php endwhile; ?>
</main>

<?php
get_footer();
