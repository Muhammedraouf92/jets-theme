<?php
/**
 * Single service card — shared by the homepage Services carousel
 * (section-services.php) and the Services archive grid (archive-service.php)
 * so both stay visually identical without duplicating markup.
 *
 * Expected $args: [ 'service' => WP_Post, 'index' => int ] (index only
 * affects icon fallback cycling when no Icon Slug is set).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$service = $args['service'];
$index   = $args['index'] ?? 0;
$meta    = jets_get_service_meta( $service );
?>
<article class="card card--service">
	<span class="card__icon" aria-hidden="true">
		<?php echo jets_get_service_icon( ! empty( $meta['icon'] ) ? $meta['icon'] : get_the_title( $service ), $index ); ?>
	</span>
	<h3 class="card__title"><?php echo esc_html( get_the_title( $service ) ); ?></h3>
	<?php if ( $meta['short_description'] ) : ?>
		<p class="card__desc"><?php echo esc_html( $meta['short_description'] ); ?></p>
	<?php endif; ?>
	<?php if ( ! empty( $meta['value_badges'] ) ) : ?>
		<div class="card__badges">
			<?php foreach ( array_slice( $meta['value_badges'], 0, 3 ) as $badge_label ) : ?>
				<span class="badge badge--sm"><?php echo esc_html( $badge_label ); ?></span>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<a class="btn btn--text card__link" href="<?php echo esc_url( get_permalink( $service ) ); ?>">
		<?php echo esc_html( jets_translate( 'Read more' ) ); ?> <span class="icon">&rarr;</span>
	</a>
</article>
