<?php
/**
 * Customer product favorites.
 *
 * @package Digi_Zoghal
 */

defined( 'ABSPATH' ) || exit;

function digi_zoghal_favorite_ids() {
	return wp_parse_id_list( (array) get_user_meta( get_current_user_id(), '_dz_favorite_products', true ) );
}

function digi_zoghal_favorite_form( $product_id, $saved ) {
	?>
	<form class="dz-favorite-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="dz_update_favorite">
		<input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>">
		<input type="hidden" name="operation" value="<?php echo $saved ? 'remove' : 'save'; ?>">
		<?php wp_nonce_field( 'dz_favorite_' . $product_id ); ?>
		<button class="button" type="submit"<?php if ( $saved ) : ?> aria-label="<?php echo esc_attr( sprintf( __( 'حذف %s از علاقه‌مندی‌ها', 'digi-zoghal' ), get_the_title( $product_id ) ) ); ?>"<?php endif; ?>><?php echo esc_html( $saved ? 'حذف از علاقه‌مندی‌ها' : 'ذخیره در علاقه‌مندی‌ها' ); ?></button>
	</form>
	<?php
}

add_action( 'woocommerce_single_product_summary', function () {
	if ( ! is_user_logged_in() ) {
		echo '<p><a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">برای ذخیرهٔ محصول وارد حساب شوید</a></p>';
		return;
	}
	$product_id = get_queried_object_id();
	digi_zoghal_favorite_form( $product_id, in_array( $product_id, digi_zoghal_favorite_ids(), true ) );
}, 35 );

add_action( 'admin_post_dz_update_favorite', function () {
	$product_id = absint( $_POST['product_id'] ?? 0 );
	check_admin_referer( 'dz_favorite_' . $product_id );
	$ids = digi_zoghal_favorite_ids();
	if ( 'remove' === ( $_POST['operation'] ?? '' ) ) {
		$ids = array_values( array_diff( $ids, array( $product_id ) ) );
	} elseif ( 'save' === ( $_POST['operation'] ?? '' ) && 'product' === get_post_type( $product_id ) && 'publish' === get_post_status( $product_id ) ) {
		$ids = wp_parse_id_list( array_merge( $ids, array( $product_id ) ) );
	}
	update_user_meta( get_current_user_id(), '_dz_favorite_products', $ids );
	wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) . '#dz-favorites', 303 );
	exit;
} );

add_action( 'woocommerce_account_dashboard', function () {
	$products = array_filter( array_map( 'wc_get_product', digi_zoghal_favorite_ids() ), function ( $product ) {
		return $product && 'publish' === $product->get_status();
	} );
	?>
	<section class="dz-account-card dz-favorites" id="dz-favorites">
		<div class="dz-favorites-heading">
			<h2>علاقه‌مندی‌های من</h2>
			<span><?php echo esc_html( sprintf( __( '%s محصول', 'digi-zoghal' ), number_format_i18n( count( $products ) ) ) ); ?></span>
		</div>
		<?php if ( ! $products ) : ?>
			<div class="dz-account-empty">
				<p>هنوز محصولی ذخیره نکرده‌اید. هر محصولی را که می‌خواهید بعداً دوباره ببینید، از صفحهٔ همان محصول ذخیره کنید.</p>
				<a class="btn btn-primary" href="<?php echo esc_url( digi_zoghal_shop_url() ); ?>">دیدن محصولات</a>
			</div>
		<?php else : ?>
			<ul class="dz-favorites-grid">
				<?php foreach ( $products as $product ) : ?>
					<li>
						<a class="dz-favorite-product" href="<?php echo esc_url( $product->get_permalink() ); ?>">
							<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'alt' => '' ) ) ); ?>
							<span>
								<strong><?php echo esc_html( $product->get_name() ); ?></strong>
								<span class="dz-favorite-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
							</span>
						</a>
						<?php digi_zoghal_favorite_form( $product->get_id(), true ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</section>
	<?php
} );
