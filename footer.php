<?php
/**
 * Site footer.
 *
 * @package Digi_Zoghal
 */
$phone     = get_theme_mod( 'dz_phone', '' );
$whatsapp  = get_theme_mod( 'dz_whatsapp', '' );
$instagram = get_theme_mod( 'dz_instagram', '' );
$telegram  = get_theme_mod( 'dz_telegram', '' );
?>
</div>

<footer id="footer">
	<div class="shell">
		<div class="footer-grid">
			<div>
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'صفحه اصلی دیجی زغال', 'digi-zoghal' ); ?>">
					<img class="brand-footer" src="<?php echo esc_url( get_theme_file_uri( 'assets/brand/reverse-logo.svg' ) ); ?>" alt="" width="1394" height="381" loading="lazy">
				</a>
				<p><?php esc_html_e( 'مسیر ساده خرید تکی و عمده زغال وارداتی و تنباکو؛ با قیمت‌گذاری روشن، پرداخت بانکی و پیگیری سفارش.', 'digi-zoghal' ); ?></p>
			</div>
			<div>
				<h4><?php esc_html_e( 'راهنمای مشتری', 'digi-zoghal' ); ?></h4>
				<ul><li><a href="<?php echo esc_url( home_url( '/#wholesale' ) ); ?>"><?php esc_html_e( 'خرید عمده', 'digi-zoghal' ); ?></a></li><li><a href="<?php echo esc_url( home_url( '/#benefits' ) ); ?>"><?php esc_html_e( 'خرید و پرداخت', 'digi-zoghal' ); ?></a></li><li><a href="<?php echo esc_url( digi_zoghal_account_url() ); ?>"><?php esc_html_e( 'حساب کاربری', 'digi-zoghal' ); ?></a></li><li><a href="<?php echo esc_url( dz_about_url() ); ?>"><?php esc_html_e( 'درباره ما', 'digi-zoghal' ); ?></a></li></ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'فروشگاه', 'digi-zoghal' ); ?></h4>
				<?php dz_render_catalog_links( dz_catalog_category_links(), 'dz-footer-catalog' ); ?>
			</div>
			<div>
				<h4><a href="<?php echo esc_url( digi_zoghal_contact_url() ); ?>"><?php esc_html_e( 'ارتباط با فروشگاه', 'digi-zoghal' ); ?></a></h4>
				<div class="contact-pending">
					<?php if ( $phone ) : ?><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a><?php endif; ?>
					<?php if ( $whatsapp ) : ?><a href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'واتساپ', 'digi-zoghal' ); ?></a><?php endif; ?>
					<?php if ( $instagram ) : ?><a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'اینستاگرام', 'digi-zoghal' ); ?></a><?php endif; ?>
					<?php if ( $telegram ) : ?><a href="<?php echo esc_url( $telegram ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'تلگرام', 'digi-zoghal' ); ?></a><?php endif; ?>
					<a href="<?php echo esc_url( digi_zoghal_contact_url() ); ?>"><?php esc_html_e( 'فرم تماس با فروشگاه ←', 'digi-zoghal' ); ?></a>
				</div>
			</div>
		</div>
		<div class="copyright"><span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></span><span><?php esc_html_e( 'تمام حقوق محفوظ است.', 'digi-zoghal' ); ?></span></div>
	</div>
</footer>

<?php if ( ! is_singular( 'product' ) || ! dz_is_industrial( get_queried_object_id() ) ) : ?>
<a class="mobile-cart" href="<?php echo esc_url( digi_zoghal_cart_url() ); ?>"><strong><?php esc_html_e( 'سبد خرید', 'digi-zoghal' ); ?> · <span class="dz-cart-count"><?php echo esc_html( number_format_i18n( digi_zoghal_cart_count() ) ); ?></span> <?php esc_html_e( 'کالا', 'digi-zoghal' ); ?></strong><span class="dz-cart-subtotal"><?php echo wp_kses_post( digi_zoghal_cart_subtotal() ); ?></span></a>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>


