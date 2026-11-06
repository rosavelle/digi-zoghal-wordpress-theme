<?php
/**
 * Comments template.
 *
 * @package Digi_Zoghal
 */
if ( post_password_required() ) {
	return;
}
?>
<section class="content-card comments-area" id="comments">
	<?php if ( have_comments() ) : ?><h2><?php esc_html_e( 'دیدگاه‌ها', 'digi-zoghal' ); ?></h2><ol class="comment-list"><?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true ) ); ?></ol><?php the_comments_pagination(); endif; ?>
	<?php comment_form(); ?>
</section>
