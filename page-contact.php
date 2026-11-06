<?php
/**
 * Template Name: ارتباط با فروشگاه
 * Template Post Type: page
 *
 * @package Digi_Zoghal
 */

get_header();

$phone         = get_theme_mod( 'dz_phone', '' );
$whatsapp      = get_theme_mod( 'dz_whatsapp', '' );
$instagram     = get_theme_mod( 'dz_instagram', '' );
$telegram      = get_theme_mod( 'dz_telegram', '' );
$support_hours = get_theme_mod( 'dz_support_hours', __( 'شنبه تا پنجشنبه، ساعت ۹ تا ۱۸', 'digi-zoghal' ) );
$store_address = get_theme_mod( 'dz_store_address', '' );
$phone_href    = preg_replace( '/[^0-9+]/', '', digi_zoghal_normalize_phone( $phone ) );
$status        = isset( $_GET['contact_status'] ) ? sanitize_key( wp_unslash( $_GET['contact_status'] ) ) : '';
$notices       = array(
	'success' => array(
		'class' => 'success',
		'text'  => __( 'پیام شما ثبت شد. در اولین فرصت کاری با شما تماس می‌گیریم.', 'digi-zoghal' ),
	),
	'invalid' => array(
		'class' => 'error',
		'text'  => __( 'لطفاً نام، شماره موبایل، موضوع و متن پیام را به‌درستی تکمیل کنید.', 'digi-zoghal' ),
	),
	'limited' => array(
		'class' => 'error',
		'text'  => __( 'پیام قبلی شما دریافت شده است؛ لطفاً دو دقیقه دیگر دوباره تلاش کنید.', 'digi-zoghal' ),
	),
	'error'   => array(
		'class' => 'error',
		'text'  => __( 'ارسال پیام انجام نشد. لطفاً دوباره تلاش کنید.', 'digi-zoghal' ),
	),
);
?>

<main class="dz-contact-page">
	<section class="contact-hero">
		<div class="shell contact-hero-inner">
			<div class="contact-hero-copy">
				<span class="contact-kicker"><?php esc_html_e( 'ارتباط با فروشگاه', 'digi-zoghal' ); ?></span>
				<h1><?php esc_html_e( 'هر سؤالی دارید، ما کنار شماییم', 'digi-zoghal' ); ?></h1>
				<p><?php esc_html_e( 'برای انتخاب محصول، پیگیری سفارش، خرید عمده یا هر موضوع دیگری با ما در ارتباط باشید. راهی را انتخاب کنید که برایتان راحت‌تر است.', 'digi-zoghal' ); ?></p>
			</div>
			<div class="contact-availability" aria-label="<?php esc_attr_e( 'زمان پاسخ‌گویی فروشگاه', 'digi-zoghal' ); ?>">
				<span class="contact-live-dot" aria-hidden="true"></span>
				<div>
					<strong><?php esc_html_e( 'زمان پاسخ‌گویی', 'digi-zoghal' ); ?></strong>
					<span><?php echo esc_html( $support_hours ); ?></span>
				</div>
			</div>
		</div>
	</section>

	<div class="shell contact-shell">
		<?php if ( $phone_href || $whatsapp || $instagram || $telegram ) : ?>
		<section class="contact-methods" aria-labelledby="contact-methods-title">
			<div class="contact-section-heading">
				<div>
					<span><?php esc_html_e( 'تماس مستقیم', 'digi-zoghal' ); ?></span>
					<h2 id="contact-methods-title"><?php esc_html_e( 'سریع‌ترین راه‌های ارتباطی', 'digi-zoghal' ); ?></h2>
				</div>
				<p><?php esc_html_e( 'برای پیگیری سفارش، شماره سفارش را آماده داشته باشید.', 'digi-zoghal' ); ?></p>
			</div>

			<div class="contact-method-grid">
				<?php if ( $phone_href ) : ?>
					<a class="contact-method-card contact-method-phone" href="tel:<?php echo esc_attr( $phone_href ); ?>">
					<span class="contact-method-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.63a2 2 0 0 1-.45 2.11L8 9.73a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.85.29 1.73.5 2.63.62A2 2 0 0 1 22 16.92z"/></svg></span>
					<span class="contact-method-copy"><small><?php esc_html_e( 'تماس تلفنی', 'digi-zoghal' ); ?></small><strong><?php echo esc_html( $phone ); ?></strong></span>
					<span class="contact-method-arrow" aria-hidden="true">←</span>
				</a><?php endif; ?>

				<?php if ( $whatsapp ) : ?>
					<a class="contact-method-card" href="<?php echo esc_url( $whatsapp ); ?>" target="_blank" rel="noopener noreferrer">
					<span class="contact-method-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.5 9 9 0 0 1-3.8-.9L3 21l1.8-5a8.5 8.5 0 1 1 16.2-4.5z"/><path d="M8.4 8.2c.5 3 2.3 4.8 5.4 5.4"/></svg></span>
					<span class="contact-method-copy"><small><?php esc_html_e( 'گفت‌وگوی سریع', 'digi-zoghal' ); ?></small><strong><?php echo esc_html__( 'واتساپ', 'digi-zoghal' ); ?></strong></span>
					<span class="contact-method-arrow" aria-hidden="true">←</span>
				</a><?php endif; ?>

				<?php if ( $instagram ) : ?>
					<a class="contact-method-card" href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer">
					<span class="contact-method-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" class="contact-icon-fill"/></svg></span>
					<span class="contact-method-copy"><small><?php esc_html_e( 'اخبار و محصولات', 'digi-zoghal' ); ?></small><strong><?php echo esc_html__( 'اینستاگرام', 'digi-zoghal' ); ?></strong></span>
					<span class="contact-method-arrow" aria-hidden="true">←</span>
				</a><?php endif; ?>

				<?php if ( $telegram ) : ?>
					<a class="contact-method-card" href="<?php echo esc_url( $telegram ); ?>" target="_blank" rel="noopener noreferrer">
					<span class="contact-method-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m22 2-7 20-4-8-8-4 19-8z"/><path d="m11 14 4-4"/></svg></span>
					<span class="contact-method-copy"><small><?php esc_html_e( 'پشتیبانی و اطلاع‌رسانی', 'digi-zoghal' ); ?></small><strong><?php echo esc_html__( 'تلگرام', 'digi-zoghal' ); ?></strong></span>
					<span class="contact-method-arrow" aria-hidden="true">←</span>
				</a><?php endif; ?>
			</div>
		</section>

		<?php endif; ?>

		<section class="contact-main-grid" aria-label="<?php esc_attr_e( 'فرم و راهنمای تماس', 'digi-zoghal' ); ?>">
			<div class="contact-form-card" id="contact-form">
				<div class="contact-card-heading">
					<span><?php esc_html_e( 'فرم تماس', 'digi-zoghal' ); ?></span>
					<h2><?php esc_html_e( 'برایمان پیام بگذارید', 'digi-zoghal' ); ?></h2>
					<p><?php esc_html_e( 'اطلاعات زیر کمک می‌کند دقیق‌تر و سریع‌تر پاسخ بدهیم.', 'digi-zoghal' ); ?></p>
				</div>

				<?php if ( isset( $notices[ $status ] ) ) : ?>
					<div class="contact-notice is-<?php echo esc_attr( $notices[ $status ]['class'] ); ?>" role="alert"><?php echo esc_html( $notices[ $status ]['text'] ); ?></div>
				<?php endif; ?>

				<form class="contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="digi_zoghal_contact_message">
					<?php wp_nonce_field( 'digi_zoghal_contact_message', 'digi_zoghal_contact_nonce' ); ?>
					<div class="contact-honeypot" aria-hidden="true">
						<label for="company-website"><?php esc_html_e( 'وب‌سایت شرکت', 'digi-zoghal' ); ?></label>
						<input id="company-website" type="text" name="company_website" tabindex="-1" autocomplete="off">
					</div>

					<div class="contact-field-row">
						<label class="contact-field"><span><?php esc_html_e( 'نام و نام خانوادگی', 'digi-zoghal' ); ?> <b>*</b></span><input type="text" name="contact_name" minlength="2" maxlength="80" autocomplete="name" required></label>
						<label class="contact-field"><span><?php esc_html_e( 'شماره موبایل', 'digi-zoghal' ); ?> <b>*</b></span><input type="tel" name="contact_phone" inputmode="numeric" pattern="09[0-9۰-۹]{9}" maxlength="11" placeholder="۰۹xxxxxxxxx" autocomplete="tel" required></label>
					</div>
					<div class="contact-field-row">
						<label class="contact-field"><span><?php esc_html_e( 'موضوع پیام', 'digi-zoghal' ); ?> <b>*</b></span><select name="contact_topic" required><option value="" selected disabled><?php esc_html_e( 'انتخاب کنید', 'digi-zoghal' ); ?></option><option value="order"><?php esc_html_e( 'پیگیری سفارش', 'digi-zoghal' ); ?></option><option value="product"><?php esc_html_e( 'راهنمای انتخاب محصول', 'digi-zoghal' ); ?></option><option value="wholesale"><?php esc_html_e( 'خرید عمده', 'digi-zoghal' ); ?></option><option value="payment"><?php esc_html_e( 'پرداخت و ارسال', 'digi-zoghal' ); ?></option><option value="other"><?php esc_html_e( 'سایر موارد', 'digi-zoghal' ); ?></option></select></label>
						<label class="contact-field"><span><?php esc_html_e( 'شماره سفارش', 'digi-zoghal' ); ?> <small><?php esc_html_e( '(اختیاری)', 'digi-zoghal' ); ?></small></span><input type="text" name="contact_order" inputmode="numeric" maxlength="30" placeholder="مثلاً ۱۲۳۴"></label>
					</div>
					<label class="contact-field"><span><?php esc_html_e( 'ایمیل', 'digi-zoghal' ); ?> <small><?php esc_html_e( '(اختیاری)', 'digi-zoghal' ); ?></small></span><input type="email" name="contact_email" maxlength="120" autocomplete="email" placeholder="name@example.com"></label>
					<label class="contact-field"><span><?php esc_html_e( 'متن پیام', 'digi-zoghal' ); ?> <b>*</b></span><textarea name="contact_message" rows="6" minlength="10" maxlength="2000" placeholder="موضوع را با جزئیات کوتاه برایمان بنویسید…" required></textarea></label>
					<div class="contact-form-submit">
						<button class="btn btn-primary" type="submit"><?php esc_html_e( 'ارسال پیام', 'digi-zoghal' ); ?><span aria-hidden="true">←</span></button>
						<p><?php esc_html_e( 'اطلاعات شما فقط برای پاسخ‌گویی به همین درخواست استفاده می‌شود.', 'digi-zoghal' ); ?></p>
					</div>
				</form>
			</div>

			<aside class="contact-info-column">
				<div class="contact-info-card">
					<span class="contact-info-badge"><?php esc_html_e( 'قبل از ارسال پیام', 'digi-zoghal' ); ?></span>
					<h2><?php esc_html_e( 'شاید پاسخ شما همین‌جاست', 'digi-zoghal' ); ?></h2>
					<div class="contact-faq">
						<details><summary><?php esc_html_e( 'چطور سفارشم را پیگیری کنم؟', 'digi-zoghal' ); ?></summary><p><?php esc_html_e( 'وارد حساب کاربری شوید و از بخش «سفارش‌ها» وضعیت سفارش را ببینید. برای بررسی بیشتر، شماره سفارش را در پیام بنویسید.', 'digi-zoghal' ); ?></p></details>
						<details><summary><?php esc_html_e( 'برای خرید عمده از کجا شروع کنم؟', 'digi-zoghal' ); ?></summary><p><?php esc_html_e( 'موضوع «خرید عمده» را در فرم انتخاب کنید و نوع محصول و تعداد تقریبی را بنویسید تا راهنمایی‌تان کنیم.', 'digi-zoghal' ); ?></p></details>
						<details><summary><?php esc_html_e( 'برای انتخاب محصول مشاوره می‌دهید؟', 'digi-zoghal' ); ?></summary><p><?php esc_html_e( 'بله؛ کاربرد و مقدار مصرفتان را بنویسید تا گزینه مناسب‌تری پیشنهاد کنیم.', 'digi-zoghal' ); ?></p></details>
					</div>
				</div>

				<div class="contact-summary-card">
					<div><span><?php esc_html_e( 'ساعات پاسخ‌گویی', 'digi-zoghal' ); ?></span><strong><?php echo esc_html( $support_hours ); ?></strong></div>
					<?php if ( $store_address ) : ?><div><span><?php esc_html_e( 'نشانی فروشگاه', 'digi-zoghal' ); ?></span><strong><?php echo nl2br( esc_html( $store_address ) ); ?></strong></div><?php endif; ?>
					<div><span><?php esc_html_e( 'پیشنهاد برای پاسخ سریع‌تر', 'digi-zoghal' ); ?></span><strong><?php esc_html_e( 'موضوع و شماره سفارش را کامل بنویسید', 'digi-zoghal' ); ?></strong></div>
				</div>
			</aside>
		</section>

		<?php while ( have_posts() ) : the_post(); ?>
			<?php if ( trim( get_the_content() ) ) : ?><section class="contact-editor-content entry-content"><?php the_content(); ?></section><?php endif; ?>
		<?php endwhile; ?>
	</div>
</main>

<?php get_footer(); ?>
