<?php
/**
 * Single post template.
 *
 * @package Digi_Zoghal
 */
get_header();
?>
<main class="dz-content"><div class="shell narrow-content">
	<?php while ( have_posts() ) : the_post(); ?><article <?php post_class( 'content-card' ); ?>><h1><?php the_title(); ?></h1><div class="entry-meta"><?php echo esc_html( get_the_date() ); ?></div><?php if ( has_post_thumbnail() ) : ?><div class="entry-image"><?php the_post_thumbnail( 'large' ); ?></div><?php endif; ?><div class="entry-content"><?php the_content(); ?></div></article><?php if ( comments_open() || get_comments_number() ) : comments_template(); endif; endwhile; ?>
</div></main>
<?php get_footer(); ?>
