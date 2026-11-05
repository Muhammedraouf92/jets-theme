<?php
/**
 * Search results. Loops across whatever post types WordPress's default
 * search query matched (pages, services — `service` is public with no
 * exclude_from_search flag, so it's included automatically) rather than
 * assuming one; each result is labeled with its post type so a service
 * doesn't look identical to a plain page in the list.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="section" aria-labelledby="search-results-heading">
	<div class="container container--narrow">
		<div class="section-heading section-heading--center reveal" data-reveal>
			<span class="section-heading__eyebrow"><?php echo esc_html( jets_translate( 'Search Results for:' ) ); ?></span>
			<h1 class="section-heading__title" id="search-results-heading">&ldquo;<?php echo esc_html( get_search_query() ); ?>&rdquo;</h1>
		</div>

		<?php get_template_part( 'template-parts/components/search-form' ); ?>
	</div>
</section>

<section class="section section--tight">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="search-results-list">
				<?php
				while ( have_posts() ) :
					the_post();
					$jets_result_type = get_post_type_object( get_post_type() );
					?>
					<article <?php post_class( 'card search-result' ); ?>>
						<?php if ( $jets_result_type ) : ?>
							<span class="search-result__type"><?php echo esc_html( $jets_result_type->labels->singular_name ); ?></span>
						<?php endif; ?>
						<h2 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="card__desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
						<a class="btn btn--text card__link" href="<?php the_permalink(); ?>">
							<?php echo esc_html( jets_translate( 'Read more' ) ); ?> <span class="icon">&rarr;</span>
						</a>
					</article>
				<?php endwhile; ?>
			</div>

			<?php
			the_posts_pagination( array(
				'prev_text' => esc_html( jets_translate( 'Previous' ) ),
				'next_text' => esc_html( jets_translate( 'Next' ) ),
			) );
			?>
		<?php else : ?>
			<div class="search-no-results reveal" data-reveal>
				<p><?php echo esc_html( jets_translate( "We couldn't find anything matching your search. Try different keywords, or browse our services below." ) ); ?></p>
				<a class="btn btn--primary" href="<?php echo esc_url( get_post_type_archive_link( 'service' ) ); ?>">
					<?php echo esc_html( jets_translate( 'View All Services' ) ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
