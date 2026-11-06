<?php
/**
 * Front page template.
 *
 * @package Digi_Zoghal
 */
get_header();

$shop_url     = digi_zoghal_shop_url();
$coconut_url  = dz_catalog_destination( 'coconut-charcoal', 'زغال نارگیلی' );
$molded_url   = dz_catalog_destination( 'molded-charcoal', 'زغال قالبی' );
$tobacco_url  = digi_zoghal_product_category_url( 'tobacco' );
$products     = array();
$product_loop = null;
$pressed_url  = $molded_url;

if ( class_exists( 'WooCommerce' ) ) {
	$pressed_url = dz_pressed_product_url( $molded_url );

	$product_loop = new WP_Query(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 8,
			'meta_key'       => 'total_sales',
			'orderby'        => array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ),
		)
	);
}
?>
<main id="top">
	<section class="hero">
		<div class="shell">
			<?php
			$banners = array(
				array( 'coconut', __( 'زغال نارگیلی', 'digi-zoghal' ), __( 'زغال نارگیلی مکعبی، از بسته‌های کوچک تا خرید کارتنی', 'digi-zoghal' ), __( 'مشاهده زغال نارگیلی', 'digi-zoghal' ), $coconut_url ),
				array( 'pressed', __( 'زغال فشرده و قالبی', 'digi-zoghal' ), __( 'از سکه‌ای و مکعبی تا مدل‌های گل‌دار و شیاردار', 'digi-zoghal' ), __( 'مشاهده زغال قالبی', 'digi-zoghal' ), $pressed_url ),
				array( 'industrial', __( 'ماشین‌آلات تولید زغال', 'digi-zoghal' ), __( 'میکسر، پرکن، دستگاه بسته‌بندی و انواع کوره', 'digi-zoghal' ), __( 'مشاهده ماشین‌آلات', 'digi-zoghal' ), dz_catalog_destination( 'industrial-equipment', 'ماشین‌آلات صنعتی' ) ),
			);
			$banner_assets = get_template_directory_uri() . '/assets/banners/';
			?>
			<h1 class="screen-reader-text"><?php esc_html_e( 'فروشگاه دیجی‌زغال', 'digi-zoghal' ); ?></h1>
			<div class="hero-slideshow" data-hero-slideshow role="region" aria-label="<?php esc_attr_e( 'بنرهای فروشگاه', 'digi-zoghal' ); ?>">
				<?php foreach ( $banners as $index => list( $file, $title, $description, $label, $url ) ) : ?>
					<div id="hero-slide-<?php echo esc_attr( $index + 1 ); ?>" class="hero-slide" data-hero-slide<?php echo $index ? ' hidden' : ''; ?>>
						<img src="<?php echo esc_url( $banner_assets . $file . '-1680.webp' ); ?>"
							srcset="<?php echo esc_url( $banner_assets . $file . '-840.webp' ); ?> 840w, <?php echo esc_url( $banner_assets . $file . '-1680.webp' ); ?> 1672w"
							sizes="(max-width: 640px) calc(100vw - 24px), (max-width: 1260px) calc(100vw - 40px), 1220px"
							width="1672" height="941" alt="" <?php echo $index ? 'loading="lazy"' : 'fetchpriority="high"'; ?>>
						<div class="hero-copy">
							<h2><?php echo esc_html( $title ); ?></h2>
							<p><?php echo esc_html( $description ); ?></p>
							<div class="hero-cta">
								<a class="btn btn-primary" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
								<?php if ( 0 === $index ) : ?><a class="btn hero-secondary" href="#wholesale"><?php esc_html_e( 'شرایط خرید عمده', 'digi-zoghal' ); ?></a><?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
				<div class="hero-controls" data-hero-controls hidden>
					<?php foreach ( $banners as $index => $banner ) : ?>
						<button type="button" data-hero-dot aria-controls="hero-slide-<?php echo esc_attr( $index + 1 ); ?>" aria-pressed="<?php echo $index ? 'false' : 'true'; ?>" aria-label="<?php echo esc_attr( $banner[1] ); ?>"></button>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="trust" id="benefits">
				<div class="trust-item"><span class="trust-icon">🚚</span><span><strong><?php esc_html_e( 'ارسال سراسری', 'digi-zoghal' ); ?></strong><small><?php esc_html_e( 'جزئیات ارسال در صفحهٔ تسویه', 'digi-zoghal' ); ?></small></span></div>
				<div class="trust-item"><span class="trust-icon">💳</span><span><strong><?php esc_html_e( 'پرداخت بانکی', 'digi-zoghal' ); ?></strong><small><?php esc_html_e( 'جزئیات روش پرداخت در صفحهٔ تسویه', 'digi-zoghal' ); ?></small></span></div>
				<div class="trust-item"><span class="trust-icon">📦</span><span><strong><?php esc_html_e( 'پیگیری سفارش', 'digi-zoghal' ); ?></strong><small><?php esc_html_e( 'مشاهدهٔ وضعیت سفارش در حساب', 'digi-zoghal' ); ?></small></span></div>
				<div class="trust-item"><span class="trust-icon">✓</span><span><strong><?php esc_html_e( 'قیمت عمده روشن', 'digi-zoghal' ); ?></strong><small><?php esc_html_e( 'بدون درخواست یا تأیید جداگانه', 'digi-zoghal' ); ?></small></span></div>
			</div>
		</div>
	</section>

	<section class="section category-section">
		<div class="shell">
			<div class="section-head"><div><span class="section-kicker"><?php esc_html_e( 'دسته‌بندی فروشگاه', 'digi-zoghal' ); ?></span><h2><?php esc_html_e( 'سریع به محصول مناسب برسید', 'digi-zoghal' ); ?></h2></div><a class="text-link" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'دیدن همه', 'digi-zoghal' ); ?> <span aria-hidden="true">←</span></a></div>
			<?php
			$category_cards = array(
				array( __( 'زغال نارگیلی', 'digi-zoghal' ), __( 'مکعبی و بسته‌های عمده', 'digi-zoghal' ), $coconut_url, 'coconut' ),
				array( __( 'زغال قالبی', 'digi-zoghal' ), __( 'یکدست و ماندگار', 'digi-zoghal' ), $molded_url, 'pressed' ),
				array( __( 'تنباکو', 'digi-zoghal' ), __( 'بسته‌ای و کیلویی', 'digi-zoghal' ), $tobacco_url, 'tobacco' ),
				array( __( 'خرید عمده', 'digi-zoghal' ), __( 'شرایط و قیمت خرید عمده', 'digi-zoghal' ), '#wholesale', 'wholesale' ),
			);
			?>
			<div class="categories">
				<?php foreach ( $category_cards as list( $title, $description, $url, $image ) ) : ?>
					<a class="category" href="<?php echo esc_url( $url ); ?>">
						<div class="category-copy">
							<strong><?php echo esc_html( $title ); ?></strong>
							<span><?php echo esc_html( $description ); ?></span>
							<span class="category-arrow" aria-hidden="true">←</span>
						</div>
						<span class="category-art category-art--<?php echo esc_attr( $image ); ?>" aria-hidden="true"></span>
					</a>
				<?php endforeach; ?>
			</div>
			<nav class="dz-category-links" aria-label="<?php esc_attr_e( 'دسته‌های بیشتر فروشگاه', 'digi-zoghal' ); ?>">
				<a href="<?php echo esc_url( dz_catalog_destination( 'barbecue-charcoal', 'زغال کبابی' ) ); ?>">زغال کبابی</a>
				<a href="<?php echo esc_url( dz_catalog_destination( 'wood-charcoal', 'زغال چوبی' ) ); ?>">زغال چوبی</a>
				<a href="<?php echo esc_url( dz_catalog_destination( 'charcoal-accessories', 'لوازم جانبی زغال' ) ); ?>">لوازم جانبی زغال</a>
				<a href="<?php echo esc_url( dz_catalog_destination( 'furnaces', 'انواع کوره' ) ); ?>">انواع کوره</a>
				<a href="<?php echo esc_url( dz_catalog_destination( 'industrial-equipment', 'ماشین‌آلات صنعتی' ) ); ?>">ماشین‌آلات صنعتی</a>
			</nav>
			<a class="dz-pressed-feature" href="<?php echo esc_url( $pressed_url ); ?>">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/catalog/product-pressed-assortment.webp' ) ); ?>" width="900" height="900" loading="lazy" alt="">
				<span><strong>زغال فشرده و قالبی</strong><small>سکه‌ای، مکعبی، قلبی، گل‌دار و شیاردار</small><span class="text-link">مشاهده زغال قالبی ←</span></span>
			</a>
		</div>
	</section>

	<section class="catalog" id="products">
		<div class="shell">
			<div class="section-head"><div><span class="section-kicker"><?php esc_html_e( 'انتخاب فروشگاه', 'digi-zoghal' ); ?></span><h2><?php esc_html_e( 'محصولات پرفروش', 'digi-zoghal' ); ?></h2></div><a class="text-link" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'همه محصولات', 'digi-zoghal' ); ?> <span aria-hidden="true">←</span></a></div>
			<?php if ( $product_loop && $product_loop->have_posts() ) : ?>
				<div class="products">
					<?php
					while ( $product_loop->have_posts() ) :
						$product_loop->the_post();
						$product = wc_get_product( get_the_ID() );
						if ( ! $product ) {
							continue;
						}
						$max_quantity    = $product->get_max_purchase_quantity();
						?>
						<article class="product-card">
							<a class="product-visual" href="<?php the_permalink(); ?>">
								<?php echo wp_kses_post( $product->get_image() ); ?>
								<span class="stock-badge <?php echo $product->is_in_stock() ? '' : 'out'; ?>"><?php echo $product->is_in_stock() ? esc_html__( 'موجود', 'digi-zoghal' ) : esc_html__( 'ناموجود', 'digi-zoghal' ); ?></span>
							</a>
							<div class="product-body">
								<span class="product-meta"><?php echo wp_kses_post( wc_get_product_category_list( $product->get_id(), '، ' ) ); ?></span>
								<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<div class="price-stack"><div class="price-line"><?php if ( ! dz_is_industrial( $product->get_id() ) ) : ?><small><?php esc_html_e( 'قیمت تکی', 'digi-zoghal' ); ?></small><?php endif; ?><strong><?php echo wp_kses_post( $product->get_price_html() ); ?></strong></div></div>
								<div class="card-actions">
									<?php if ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) : ?>
										<div class="dz-buy-control" data-dz-buy-control>
											<div class="dz-qty-picker" aria-label="<?php echo esc_attr( sprintf( __( 'تعداد %s', 'digi-zoghal' ), $product->get_name() ) ); ?>">
												<button type="button" data-dz-qty="increase" aria-label="<?php esc_attr_e( 'افزایش تعداد', 'digi-zoghal' ); ?>">+</button>
												<input class="dz-qty-input" type="number" min="1" <?php echo $max_quantity > 0 ? 'max="' . esc_attr( $max_quantity ) . '"' : ''; ?> step="1" value="1" inputmode="numeric" data-dz-qty-input aria-label="<?php esc_attr_e( 'تعداد', 'digi-zoghal' ); ?>">
												<button type="button" data-dz-qty="decrease" aria-label="<?php esc_attr_e( 'کاهش تعداد', 'digi-zoghal' ); ?>">−</button>
											</div>
											<a class="add-btn button product_type_simple add_to_cart_button ajax_add_to_cart" href="<?php echo esc_url( add_query_arg( 'quantity', 1, $product->add_to_cart_url() ) ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'افزودن %s به سبد خرید', 'digi-zoghal' ), $product->get_name() ) ); ?>" rel="nofollow"><?php echo esc_html( $product->add_to_cart_text() ); ?></a>
										</div>
									<?php else : ?>
										<a class="add-btn" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"><?php echo esc_html( $product->add_to_cart_text() ); ?></a>
									<?php endif; ?>
									<a class="quick-btn" href="<?php the_permalink(); ?>" aria-label="<?php esc_attr_e( 'جزئیات محصول', 'digi-zoghal' ); ?>">←</a>
								</div>
							</div>
						</article>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			<?php else : ?>
				<div class="empty-store"><span>◆</span><h3><?php esc_html_e( 'فروشگاه آمادهٔ دریافت محصولات است', 'digi-zoghal' ); ?></h3><p><?php esc_html_e( 'پس از نصب ووکامرس و افزودن محصولات، کارت‌های واقعی فروشگاه اینجا نمایش داده می‌شوند.', 'digi-zoghal' ); ?></p><?php if ( current_user_can( 'manage_options' ) ) : ?><a class="btn btn-primary" href="<?php echo esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) ); ?>"><?php esc_html_e( 'راه‌اندازی ووکامرس', 'digi-zoghal' ); ?></a><?php endif; ?></div>
			<?php endif; ?>
		</div>
	</section>

	<section class="section" id="wholesale">
		<div class="shell">
			<div class="wholesale-panel">
				<div><span class="eyebrow"><?php esc_html_e( 'قیمت عمده خودکار', 'digi-zoghal' ); ?></span><h2><?php esc_html_e( 'هرچه بیشتر بردارید، قیمت واحد کمتر می‌شود', 'digi-zoghal' ); ?></h2><p><?php esc_html_e( 'با افزودن ۱۵ عدد یا بیشتر از یک محصول دارای قیمت عمده، نرخ عمده برای تمام تعداد همان محصول اعمال می‌شود.', 'digi-zoghal' ); ?></p><a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-primary"><?php esc_html_e( 'انتخاب محصول', 'digi-zoghal' ); ?></a></div>
				<div class="tiers"><div class="tier"><span><?php esc_html_e( '۱ تا ۱۴ عدد', 'digi-zoghal' ); ?></span><strong><?php esc_html_e( 'قیمت تکی', 'digi-zoghal' ); ?></strong></div><div class="tier"><span><?php esc_html_e( '۱۵ عدد و بیشتر', 'digi-zoghal' ); ?></span><strong><?php esc_html_e( 'قیمت عمده', 'digi-zoghal' ); ?></strong></div><div class="tier"><span><?php esc_html_e( 'در سبد خرید', 'digi-zoghal' ); ?></span><strong><?php esc_html_e( 'محاسبه خودکار', 'digi-zoghal' ); ?></strong></div></div>
			</div>
			<div class="banner"><div><h2><?php esc_html_e( 'سفارش را آماده کنید؛ ارسال برای تمام ایران انجام می‌شود', 'digi-zoghal' ); ?></h2><p><?php esc_html_e( 'جزئیات ارسال و مبلغ نهایی سفارش را در صفحهٔ تسویه بررسی کنید.', 'digi-zoghal' ); ?></p></div><a class="btn" href="<?php echo esc_url( digi_zoghal_cart_url() ); ?>"><?php esc_html_e( 'مشاهده سبد خرید', 'digi-zoghal' ); ?></a></div>
		</div>
	</section>
	<?php if ( ! dz_about_page() ) : ?>
		<section class="section" id="about">
			<div class="shell">
				<h2><?php esc_html_e( 'درباره ما', 'digi-zoghal' ); ?></h2>
				<?php dz_render_about_copy(); ?>
				<a class="text-link" href="<?php echo esc_url( digi_zoghal_contact_url() ); ?>"><?php esc_html_e( 'ارتباط با فروشگاه', 'digi-zoghal' ); ?></a>
			</div>
		</section>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
