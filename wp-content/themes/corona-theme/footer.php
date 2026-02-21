<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Corona_Theme
 */

?>

	<footer id="colophon" class="site-footer">
		<div class="footer-container">

			<!-- Footer Top -->
			<div class="footer-top">
				<!-- Logo & Description -->
				<div class="footer-brand">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo" rel="home">
							<span class="logo-icon">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24">
									<circle cx="12" cy="12" r="10"></circle>
									<path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
									<line x1="12" y1="17" x2="12.01" y2="17"></line>
								</svg>
							</span>
							<span class="logo-text"><?php bloginfo( 'name' ); ?></span>
						</a>
					<?php endif; ?>

					<?php
					$footer_description = corona_pll_option_text(
						get_theme_mod( 'corona_footer_description', '' ),
						'corona_footer_description',
						'Corona Theme - Customizer'
					);
					if ( $footer_description ) :
					?>
						<p class="footer-description"><?php echo esc_html( $footer_description ); ?></p>
					<?php endif; ?>

					<!-- Social Links -->
					<?php
					$social_links = array(
						'twitter' => get_theme_mod( 'corona_social_twitter', '' ),
						'facebook' => get_theme_mod( 'corona_social_facebook', '' ),
						'instagram' => get_theme_mod( 'corona_social_instagram', '' ),
						'telegram' => get_theme_mod( 'corona_social_telegram', '' ),
					);
					$has_social = array_filter( $social_links );
					if ( ! empty( $has_social ) ) :
					?>
					<div class="footer-social">
						<?php if ( $social_links['twitter'] ) : ?>
						<a href="<?php echo esc_url( $social_links['twitter'] ); ?>" class="social-link" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__( 'Twitter', 'corona-theme' ); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
								<path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path>
							</svg>
						</a>
						<?php endif; ?>
						<?php if ( $social_links['facebook'] ) : ?>
						<a href="<?php echo esc_url( $social_links['facebook'] ); ?>" class="social-link" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__( 'Facebook', 'corona-theme' ); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
								<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
							</svg>
						</a>
						<?php endif; ?>
						<?php if ( $social_links['instagram'] ) : ?>
						<a href="<?php echo esc_url( $social_links['instagram'] ); ?>" class="social-link" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__( 'Instagram', 'corona-theme' ); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
								<rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
								<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
								<line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
							</svg>
						</a>
						<?php endif; ?>
						<?php if ( $social_links['telegram'] ) : ?>
						<a href="<?php echo esc_url( $social_links['telegram'] ); ?>" class="social-link" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__( 'Telegram', 'corona-theme' ); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
								<path d="M21.5 2.5l-19 9 6 2 2 6 4-4 5 4 2-17z"></path>
							</svg>
						</a>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>

				<!-- Footer Menus -->
				<?php if ( has_nav_menu( 'footer-1' ) || has_nav_menu( 'footer-2' ) ) : ?>
				<div class="footer-menus">
					<?php if ( has_nav_menu( 'footer-1' ) ) : ?>
					<div class="footer-menu-col">
						<h4 class="footer-menu-title"><?php esc_html_e( 'Quick Links', 'corona-theme' ); ?></h4>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer-1',
								'menu_class'     => 'footer-menu',
								'depth'          => 1,
							)
						);
						?>
					</div>
					<?php endif; ?>
					<?php if ( has_nav_menu( 'footer-2' ) ) : ?>
					<div class="footer-menu-col">
						<h4 class="footer-menu-title"><?php esc_html_e( 'Information', 'corona-theme' ); ?></h4>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer-2',
								'menu_class'     => 'footer-menu',
								'depth'          => 1,
							)
						);
						?>
					</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</div>

			<!-- Responsible Gaming Notice -->
			<?php
			$responsible_gaming = corona_pll_option_text(
				get_theme_mod( 'corona_responsible_gaming', '' ),
				'corona_responsible_gaming',
				'Corona Theme - Customizer'
			);
			if ( $responsible_gaming ) :
			?>
			<div class="responsible-gaming">
				<div class="responsible-gaming-icon">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="24" height="24">
						<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
						<line x1="12" y1="9" x2="12" y2="13"></line>
						<line x1="12" y1="17" x2="12.01" y2="17"></line>
					</svg>
				</div>
				<p class="responsible-gaming-text"><?php echo wp_kses_post( $responsible_gaming ); ?></p>
			</div>
			<?php endif; ?>

			<!-- Footer Bottom -->
			<div class="footer-bottom">
				<p class="copyright">
					&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'corona-theme' ); ?>
				</p>

				<?php
				// Age restriction badge
				$age_restriction = corona_pll_option_text(
					get_theme_mod( 'corona_age_restriction', '18+' ),
					'corona_age_restriction',
					'Corona Theme - Customizer'
				);
				if ( $age_restriction ) :
				?>
				<div class="age-badge">
					<?php echo esc_html( $age_restriction ); ?>
				</div>
				<?php endif; ?>
			</div>

		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
