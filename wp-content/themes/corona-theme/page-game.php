<?php
/**
 * Template Name: Game Page
 *
 * Modern game page with demo player and slot attributes.
 *
 * @package Corona_Theme
 */

// Get meta data
$image = get_post_meta(get_the_ID(), '_game_image', true);
$demo_url = get_post_meta(get_the_ID(), '_game_demo_url', true);
$characteristics = get_post_meta(get_the_ID(), '_game_characteristics', true);
$characteristics = $characteristics ? json_decode($characteristics, true) : array();
$buttons = get_post_meta(get_the_ID(), '_game_buttons', true);
$buttons = $buttons ? json_decode($buttons, true) : array();
$game_casinos = get_post_meta(get_the_ID(), '_game_casinos', true);
$game_casinos = $game_casinos ? json_decode($game_casinos, true) : array();
$game_casino_urls = get_post_meta(get_the_ID(), '_game_casino_urls', true);
$game_casino_urls = $game_casino_urls ? json_decode($game_casino_urls, true) : array();

// Fallback to old meta
if (empty($characteristics)) {
	$old_chars = get_post_meta(get_the_ID(), '_slot_custom_characteristics', true);
	if ($old_chars) {
		$characteristics = json_decode($old_chars, true) ?: array();
	}
}
if (empty($image)) {
	$image = get_post_meta(get_the_ID(), '_slot_image', true);
}
if (empty($demo_url)) {
	$demo_url = get_post_meta(get_the_ID(), '_slot_demo_iframe', true);
}

// Extract provider for header
$provider = '';
foreach ($characteristics as $char) {
	$label = strtolower($char['label']);
	if (strpos($label, 'provider') !== false || strpos($label, 'провайдер') !== false) {
		$provider = $char['value'];
		break;
	}
}

// Get first affiliate button
$first_btn_url = '';
$first_btn_text = '';
foreach ($buttons as $btn) {
	if (!empty($btn['url'])) {
		$first_btn_url = $btn['url'];
		$first_btn_text = $btn['text'] ?: (function_exists('pll__') ? pll__('Play for Real Money') : __('Play for Real Money', 'corona-theme'));
		break;
	}
}

get_header();
?>

<main id="primary" class="site-main game-page">
<?php while (have_posts()) : the_post(); ?>
	<div class="game-container">

		<!-- Game Header -->
		<div class="game-header">
			<div class="game-header-info">
				<?php if ($provider): ?>
				<span class="game-provider"><?php echo esc_html($provider); ?></span>
				<?php endif; ?>
				<h2 class="game-title"><?php the_title(); ?></h2>
			</div>
		</div>

		<!-- Demo Player Area -->
		<div class="game-player-wrapper">
			<div class="game-player" id="game-player">
				<?php if ($demo_url): ?>
				<!-- Overlay (shown when demo not active) -->
				<div class="game-player-overlay" id="player-overlay">
					<?php if ($image): ?>
					<img src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>" class="game-player-bg">
					<?php endif; ?>
					<div class="game-player-overlay-content">
						<div class="game-play-button" id="play-button">
							<span class="play-triangle"></span>
						</div>
						<h2 class="game-player-title">
							<?php
							$ready_text = function_exists('pll__') ? pll__('Ready to play %s?') : __('Ready to play %s?', 'corona-theme');
							printf(esc_html($ready_text), get_the_title());
							?>
						</h2>
						<p class="game-player-subtitle">
							<?php echo function_exists('pll__') ? esc_html(pll__('Experience the game in free mode before playing for real money.')) : esc_html__('Experience the game in free mode before playing for real money.', 'corona-theme'); ?>
						</p>
						<?php if ($first_btn_url): ?>
						<a href="<?php echo esc_url($first_btn_url); ?>" class="game-real-money-btn" target="_blank" rel="noopener nofollow">
							<?php echo esc_html($first_btn_text); ?>
							<span class="btn-arrow">→</span>
						</a>
						<?php endif; ?>
					</div>
				</div>
				<!-- Iframe (shown when demo active) -->
				<div class="game-iframe-wrapper" id="iframe-wrapper" style="display: none;">
					<iframe id="game-iframe" allowfullscreen allow="autoplay; fullscreen"></iframe>
				</div>
				<button class="game-close-demo" id="close-demo" style="display: none;">
					<?php echo function_exists('pll__') ? esc_html(pll__('Close Demo')) : esc_html__('Close Demo', 'corona-theme'); ?>
				</button>
				<?php elseif ($image): ?>
				<!-- Just image, no demo -->
				<div class="game-player-overlay">
					<img src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>" class="game-player-bg">
					<div class="game-player-overlay-content">
						<h2 class="game-player-title"><?php echo esc_html(get_the_title()); ?></h2>
						<?php if ($first_btn_url): ?>
						<a href="<?php echo esc_url($first_btn_url); ?>" class="game-real-money-btn" target="_blank" rel="noopener nofollow">
							<?php echo esc_html($first_btn_text); ?>
							<span class="btn-arrow">→</span>
						</a>
						<?php endif; ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Where to Play -->
		<?php if (!empty($game_casinos)): ?>
		<section class="characteristics-section glass">
			<div class="characteristics-header">
				<div class="characteristics-icon">
					<?php echo corona_icon('external-link'); ?>
				</div>
				<h2 class="characteristics-title">
					<?php echo function_exists('pll__') ? esc_html(pll__('Where to Play')) : esc_html__('Where to Play', 'corona-theme'); ?>
				</h2>
			</div>
			<div class="game-casinos-list">
				<?php foreach ($game_casinos as $casino_id):
					$casino_title = get_the_title($casino_id);
					$casino_logo = get_post_meta($casino_id, '_casino_logo', true);
					if (!$casino_logo) {
						$casino_logo = get_post_meta($casino_id, '_slot_image', true);
					}
					// Check for custom URL first, then fall back to casino's default
					$casino_aff_link = '';
					if (!empty($game_casino_urls[$casino_id])) {
						$casino_aff_link = $game_casino_urls[$casino_id];
					} else {
						$casino_aff_link = function_exists('corona_get_casino_aff_link')
							? corona_get_casino_aff_link($casino_id)
							: get_post_meta($casino_id, '_casino_aff_link', true);
					}
					if (empty($casino_aff_link)) {
						$casino_aff_link = get_permalink($casino_id);
					}
				?>
				<a href="<?php echo esc_url($casino_aff_link); ?>" class="game-casino-item" target="_blank" rel="noopener nofollow">
					<div class="game-casino-info">
						<?php if ($casino_logo): ?>
						<img src="<?php echo esc_url($casino_logo); ?>" alt="<?php echo esc_attr($casino_title); ?>" class="game-casino-logo">
						<?php else: ?>
						<div class="game-casino-logo-placeholder"><?php echo esc_html(mb_substr($casino_title, 0, 2)); ?></div>
						<?php endif; ?>
						<span class="game-casino-name"><?php echo esc_html($casino_title); ?></span>
					</div>
					<span class="game-casino-play-btn"><?php echo function_exists('pll__') ? esc_html(pll__('PLAY')) : esc_html__('PLAY', 'corona-theme'); ?></span>
				</a>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

		<!-- Single Column Content -->
		<div class="game-content">

			<!-- Slot Attributes - Using characteristics table design -->
			<?php if (!empty($characteristics)): ?>
			<section class="characteristics-section glass">
				<div class="characteristics-header">
					<div class="characteristics-icon">
						<?php echo corona_icon('info'); ?>
					</div>
					<h2 class="characteristics-title">
						<?php echo function_exists('pll__') ? esc_html(pll__('Slot Attributes')) : esc_html__('Slot Attributes', 'corona-theme'); ?>
					</h2>
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

			<!-- Game Overview / Content -->
			<?php if (get_the_content()): ?>
			<section class="game-overview glass">
				<div class="characteristics-header">
					<div class="characteristics-icon">
						<?php echo corona_icon('file-text'); ?>
					</div>
					<h2 class="characteristics-title">
						<?php echo function_exists('pll__') ? esc_html(pll__('Game Overview')) : esc_html__('Game Overview', 'corona-theme'); ?>
					</h2>
				</div>
				<div class="game-overview-content">
					<?php the_content(); ?>
				</div>
			</section>
			<?php endif; ?>

		</div>

	</div>
<?php endwhile; ?>
</main>

<script>
(function() {
	var demoUrl = <?php echo json_encode($demo_url); ?>;
	if (!demoUrl) return;

	var playBtn = document.getElementById('play-button');
	var overlay = document.getElementById('player-overlay');
	var iframeWrapper = document.getElementById('iframe-wrapper');
	var iframe = document.getElementById('game-iframe');
	var closeBtn = document.getElementById('close-demo');

	function startDemo(e) {
		e.preventDefault();
		if (!iframe || !overlay || !iframeWrapper) {
			console.error('Game demo elements not found');
			return;
		}
		iframe.src = demoUrl;
		overlay.style.display = 'none';
		iframeWrapper.style.display = 'block';
		if (closeBtn) closeBtn.style.display = 'block';
		iframe.focus();
	}

	function closeDemo(e) {
		e.preventDefault();
		iframe.src = '';
		iframeWrapper.style.display = 'none';
		if (closeBtn) closeBtn.style.display = 'none';
		overlay.style.display = 'flex';
	}

	if (playBtn) {
		playBtn.onclick = startDemo;
		playBtn.ontouchend = startDemo;
	}
	if (closeBtn) {
		closeBtn.onclick = closeDemo;
		closeBtn.ontouchend = closeDemo;
	}
})();
</script>

<?php
get_footer();
