<?php
/**
 * Footer markup.
 *
 * Full-bleed photographic panel (matches the panel--full-bleed pattern
 * used by Philosophy/Final CTA) so the footer reads as a deliberate,
 * bold close to every page rather than a bare utility bar. This is a
 * single-client theme with no Theme Options screen, so footer content
 * is hardcoded here rather than pulled from ACF:
 *   - $social_links / $contact_phone / $contact_email / $terms_url /
 *     $developer_name / $developer_url below are DUMMY placeholders —
 *     swap them for the real values.
 *   - The nav column reads the "footer" menu location; it stays empty
 *     until a menu is assigned to it in Appearance > Menus.
 *   - Privacy Policy links the real page set in Settings > Privacy when
 *     one exists, falling back to a placeholder "#" otherwise.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// TODO: replace with real social links.
$social_links = array(
	array(
		'platform' => 'Instagram',
		'url'      => 'https://www.instagram.com/jetstravel9?igsi=MXQ3ZnJra2NnZnFjZg==',
	),
	array(
		'platform' => 'Tiktok',
		'url'      => 'https://www.tiktok.com/@jetstravel9?_r=1&_t=ZS-990k1Ak2T7U',
	),
	array(
		'platform' => 'Facebook',
		'url'      => 'https://www.facebook.com/share/1Pntw5zqs7/',
	),
);

// TODO: replace with the real phone number.
$contact_phone = '+20 11 30783678';

// TODO: replace with the real email address.
$contact_email = 'info@jetstravels.com';

$whatsapp_url = jets_get_whatsapp_url();

$privacy_policy_link = get_the_privacy_policy_link();

// Real Terms of Use page (see inc/helpers.php-adjacent seed history);
// '#' fallback only if it's ever deleted/unpublished.
$terms_page = get_page_by_path( 'terms-of-use', OBJECT, 'page' );
$terms_url  = $terms_page ? get_permalink( $terms_page ) : '#';

// TODO: replace with your real name/studio and portfolio URL.
$developer_name = 'Raouf';
$developer_url  = 'https://raouf-portfolio-six.vercel.app/';
?>

</main>

<footer class="site-footer panel--full-bleed section--dark" id="colophon">
	<div class="panel__media" aria-hidden="true">
		<img
			src="<?php echo esc_url( JETS_THEME_URI . '/assets/images/footer-bg_13_11zon.webp' ); ?>"
			alt=""
			loading="lazy"
		>
	</div>
	<div class="panel__scrim panel__scrim--footer" aria-hidden="true"></div>

	<div class="panel__content container site-footer__inner">
		<div class="site-footer__columns">
			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<nav class="site-footer__nav-col site-footer__nav-col--start" aria-label="<?php esc_attr_e( 'Footer', 'jets-theme' ); ?>">
					<span class="site-footer__col-heading"><?php echo esc_html( jets_translate( 'Main Page' ) ); ?></span>
					<?php
					wp_nav_menu( array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'site-footer__menu',
						'fallback_cb'    => false,
					) );
					?>
				</nav>
			<?php endif; ?>

			<div class="site-footer__brand-col">
				<a class="site-footer__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<img
							class="site-footer__logo"
							src="<?php echo esc_url( JETS_THEME_URI . '/assets/images/logo.jpeg' ); ?>"
							alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
							width="120"
							height="120"
						>
					<?php endif; ?>
				</a>
				<p class="site-footer__tagline"><?php echo esc_html( jets_translate( 'Integrated Travel Solutions. Global Standards.' ) ); ?></p>
				<?php if ( $contact_phone || $contact_email ) : ?>
					<div class="site-footer__contact-lines">
						<?php if ( $contact_phone ) : ?>
							<a class="site-footer__contact-line" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $contact_phone ) ); ?>">
								<?php echo esc_html( $contact_phone ); ?>
							</a>
						<?php endif; ?>
						<?php if ( $contact_email ) : ?>
							<a class="site-footer__contact-line" href="mailto:<?php echo esc_attr( $contact_email ); ?>">
								<?php echo esc_html( $contact_email ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if ( $whatsapp_url ) : ?>
					<a class="whatsapp-btn whatsapp-btn--labeled" href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer">
						<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.36 5.07L2 22l5.06-1.33A9.94 9.94 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2Zm0 18.2c-1.67 0-3.22-.49-4.52-1.34l-.32-.2-3 .79.8-2.92-.21-.3A8.19 8.19 0 1 1 20.2 12 8.2 8.2 0 0 1 12 20.2Zm4.5-6.13c-.25-.12-1.45-.71-1.68-.79-.22-.08-.39-.12-.55.12-.16.25-.63.79-.77.95-.14.16-.28.18-.53.06-.25-.12-1.04-.38-1.98-1.22-.73-.65-1.23-1.46-1.37-1.7-.14-.25-.02-.38.11-.5.11-.11.25-.28.37-.42.12-.14.16-.25.24-.41.08-.16.04-.31-.02-.43-.06-.12-.55-1.32-.75-1.81-.2-.48-.4-.41-.55-.42h-.47c-.16 0-.42.06-.64.31-.22.25-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.7 2.6 4.12 3.64.58.25 1.03.4 1.38.51.58.18 1.11.16 1.53.1.47-.07 1.45-.59 1.65-1.16.2-.57.2-1.06.14-1.16-.06-.1-.22-.16-.47-.28Z"/></svg>
						<?php echo esc_html( jets_translate( 'Chat on WhatsApp' ) ); ?>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $social_links ) ) : ?>
				<nav class="site-footer__nav-col site-footer__nav-col--end" aria-label="<?php esc_attr_e( 'Social media', 'jets-theme' ); ?>">
					<span class="site-footer__col-heading"><?php echo esc_html( jets_translate( 'Social Media' ) ); ?></span>
					<ul class="site-footer__menu">
						<?php foreach ( $social_links as $link ) : ?>
							<?php if ( ! empty( $link['url'] ) ) : ?>
								<li>
									<a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
										<?php echo esc_html( $link['platform'] ); ?>
									</a>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>
		</div>

		<div class="site-footer__bottom">
			<p class="site-footer__copyright">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.
				<?php echo esc_html( jets_translate( 'All rights reserved.' ) ); ?>
			</p>
			<a class="site-footer__credit" href="<?php echo esc_url( $developer_url ); ?>" target="_blank" rel="noopener noreferrer">
				<?php
				/* translators: %s: developer/studio name. */
				echo esc_html( sprintf( jets_translate( 'Made by %s' ), $developer_name ) );
				?>
			</a>

			<div class="site-footer__legal-links">
				<?php if ( $privacy_policy_link ) : ?>
					<?php echo wp_kses_post( $privacy_policy_link ); ?>
				<?php else : ?>
					<a href="#"><?php echo esc_html( jets_translate( 'Privacy Policy' ) ); ?></a>
				<?php endif; ?>
				<a href="<?php echo esc_url( $terms_url ); ?>"><?php echo esc_html( jets_translate( 'Terms of Use' ) ); ?></a>
			</div>
		</div>

		<p class="site-footer__wordmark" aria-hidden="true">JETS TRAVEL</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
