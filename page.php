<?php
/**
 * Page template.
 *
 * @package Digi_Zoghal
 */
get_header();
?>
<main class="dz-content">
	<?php if ( function_exists( 'is_account_page' ) && is_account_page() ) : ?>
		<div class="shell">
			<?php while ( have_posts() ) : the_post(); ?>
				<div class="entry-content"><?php the_content(); ?></div>
			<?php endwhile; ?>
		</div>
	<?php else : ?>
		<div class="shell narrow-content">
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'content-card' ); ?>><h1><?php the_title(); ?></h1><div class="entry-content"><?php
					$about_page = dz_about_page();
					if ( $about_page && get_the_ID() === $about_page->ID ) {
						dz_render_about_copy();
					} else {
						the_content();
					}
				?></div></article>
			<?php endwhile; ?>
		</div>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
