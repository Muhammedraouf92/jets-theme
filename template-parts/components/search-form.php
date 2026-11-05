<?php
/**
 * Reusable search form — used on 404.php ("try searching instead") and
 * search.php (refine the current search). Not part of primary navigation;
 * there's no site-wide search UI in the header, per PROJECT_CONTEXT.md
 * §16 ("no site search UI beyond default").
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="field search-form__field">
		<label class="visually-hidden" for="search-field"><?php echo esc_html( jets_translate( 'Search' ) ); ?></label>
		<input
			type="search"
			id="search-field"
			class="field__input"
			name="s"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			placeholder="<?php echo esc_attr( jets_translate( 'Search the site…' ) ); ?>"
		>
	</div>
	<button type="submit" class="btn btn--primary"><?php echo esc_html( jets_translate( 'Search' ) ); ?></button>
</form>
