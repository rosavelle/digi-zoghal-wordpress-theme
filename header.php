<?php
/**
 * Site header.
 *
 * @package Digi_Zoghal
 */
?><!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#171915">
	<?php if ( ! has_site_icon() ) : ?>
		<link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( get_theme_file_uri( 'assets/brand/favicon-32.png' ) ); ?>">
		<link rel="icon" type="image/svg+xml" href="<?php echo esc_url( get_theme_file_uri( 'assets/brand/app-icon.svg' ) ); ?>">
		<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( get_theme_file_uri( 'assets/brand/apple-touch-icon.png' ) ); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#content"><?php esc_html_e( 'رفتن به محتوا', 'digi-zoghal' ); ?></a>

<div class="announcement">
	<div class="shell">
		<span><strong><?php esc_html_e( 'خرید عمده بدون تأیید جداگانه', 'digi-zoghal' ); ?></strong>؛ <?php esc_html_e( 'قیمت عمده از ۱۵ عددِ یک محصول', 'digi-zoghal' ); ?></span>
		<span><?php esc_html_e( 'ارسال به سراسر ایران', 'digi-zoghal' ); ?></span>
	</div>
</div>

<header class="header">
	<div class="shell header-main">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'صفحه اصلی دیجی زغال', 'digi-zoghal' ); ?>">
			<?php if ( has_custom_logo() ) : ?>
				<?php
				echo wp_get_attachment_image(
					(int) get_theme_mod( 'custom_logo' ),
					'full',
					false,
					array( 'class' => 'brand-custom', 'alt' => '' )
				);
				?>
				<span><?php bloginfo( 'name' ); ?><small><?php echo esc_html( get_bloginfo( 'description' ) ?: 'زغال و تنباکو، تکی و عمده' ); ?></small></span>
			<?php else : ?>
				<picture class="brand-art">
					<source media="(max-width: 420px)" srcset="<?php echo esc_url( get_theme_file_uri( 'assets/brand/primary-symbol.svg' ) ); ?>">
					<img src="<?php echo esc_url( get_theme_file_uri( 'assets/brand/primary-logo.svg' ) ); ?>" alt="" width="1394" height="381">
				</picture>
			<?php endif; ?>
		</a>

		<form class="header-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
			<label class="screen-reader-text" for="dz-search"><?php esc_html_e( 'جستجوی محصول', 'digi-zoghal' ); ?></label>
			<input id="dz-search" name="s" type="search" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'جستجو میان زغال، تنباکو و بسته‌ها', 'digi-zoghal' ); ?>">
			<?php if ( class_exists( 'WooCommerce' ) ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
		</form>

		<div class="actions">
			<a class="icon-btn" href="<?php echo esc_url( digi_zoghal_account_url() ); ?>" aria-label="<?php esc_attr_e( 'حساب کاربری', 'digi-zoghal' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg><span class="login-label"><?php echo is_user_logged_in() ? esc_html__( 'حساب من', 'digi-zoghal' ) : esc_html__( 'ورود', 'digi-zoghal' ); ?></span>
			</a>
			<button class="icon-btn cart-btn" type="button" data-cart-toggle aria-expanded="false" aria-label="<?php esc_attr_e( 'مشاهده سبد خرید', 'digi-zoghal' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 3h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.6L21 7H6"/><circle cx="10" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
				<span class="count dz-cart-count"><?php echo esc_html( number_format_i18n( digi_zoghal_cart_count() ) ); ?></span>
			</button>
			<button class="menu-toggle" type="button" data-menu-toggle aria-expanded="false" aria-label="<?php esc_attr_e( 'باز کردن منو', 'digi-zoghal' ); ?>"><span></span><span></span><span></span></button>
		</div>
		<nav class="nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'digi-zoghal' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'primary-menu',
					'fallback_cb'    => 'digi_zoghal_menu_fallback',
				)
			);
			?>
		</nav>
	</div>
</header>

<div class="drawer-backdrop" data-cart-close></div>
<aside class="drawer" aria-label="<?php esc_attr_e( 'سبد خرید', 'digi-zoghal' ); ?>" aria-hidden="true">
	<div class="drawer-head">
		<div><h3><?php esc_html_e( 'سبد خرید', 'digi-zoghal' ); ?></h3><span class="product-meta"><?php esc_html_e( 'کالاهای انتخاب‌شده شما', 'digi-zoghal' ); ?></span></div>
		<button class="close-btn" type="button" data-cart-close aria-label="<?php esc_attr_e( 'بستن', 'digi-zoghal' ); ?>">×</button>
	</div>
	<div class="cart-list">
		<?php if ( function_exists( 'woocommerce_mini_cart' ) ) : ?>
			<div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
		<?php else : ?>
			<div class="cart-empty"><p><?php esc_html_e( 'برای فعال شدن سبد خرید، ووکامرس را نصب کنید.', 'digi-zoghal' ); ?></p></div>
		<?php endif; ?>
	</div>
</aside>

<div id="content" class="site-content">
