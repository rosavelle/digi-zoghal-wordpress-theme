<?php
/**
 * Main index template.
 *
 * @package Digi_Zoghal
 */
get_header();
?>
<main class="dz-content">
	<div class="shell content-grid">
		<section>
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'content-card' ); ?>><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><div class="entry-meta"><?php echo esc_html( get_the_date() ); ?></div><?php the_excerpt(); ?></article>
			<?php endwhile; the_posts_pagination(); else : ?><div class="content-card"><h2><?php esc_html_e( 'مطلبی پیدا نشد', 'digi-zoghal' ); ?></h2></div><?php endif; ?>
		</section>
	</div>
</main>
<?php get_footer(); ?>
