<?php
/**
 * Customer login and registration.
 *
 * @package Digi_Zoghal
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_customer_login_form' );

$posted_username  = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
$posted_email     = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
$posted_firstname = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
$posted_lastname  = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
$posted_national  = isset( $_POST['national_id'] ) ? digi_zoghal_normalize_digits( wp_unslash( $_POST['national_id'] ) ) : '';
$registration_token = isset( $_POST['registration_token'] ) ? sanitize_text_field( wp_unslash( $_POST['registration_token'] ) ) : '';
$show_registration = isset( $_POST['register'] ) && '' !== $registration_token;
$otp_enabled      = function_exists( 'digi_zoghal_sms_is_configured' ) && digi_zoghal_sms_is_configured();
?>

<section class="dz-auth-page" aria-labelledby="dz-auth-title">
	<header class="dz-auth-intro">
		<div>
			<span class="dz-auth-eyebrow"><?php esc_html_e( 'حساب دیجی زغال', 'digi-zoghal' ); ?></span>
			<h1 id="dz-auth-title"><?php esc_html_e( 'خوش آمدید', 'digi-zoghal' ); ?></h1>
			<p><?php echo esc_html( $otp_enabled ? __( 'با شماره موبایل و کد پیامکی وارد شوید یا حساب تازه بسازید.', 'digi-zoghal' ) : __( 'با شماره موبایل، ایمیل یا نام کاربری و رمز عبور وارد شوید.', 'digi-zoghal' ) ); ?></p>
		</div>
		<div class="dz-auth-intro-badge" aria-hidden="true">
			<span>✓</span>
			<small><?php esc_html_e( 'خرید سریع و امن', 'digi-zoghal' ); ?></small>
		</div>
	</header>

	<div class="dz-auth-grid <?php echo esc_attr( $show_registration ? 'is-registration' : 'is-phone-entry' ); ?>" data-dz-auth-grid>
		<section class="dz-auth-card dz-auth-card-login" data-dz-login-card <?php echo $show_registration ? 'hidden' : ''; ?>>
			<div class="dz-auth-card-head">
				<span class="dz-auth-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24"><path d="M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
				</span>
				<div>
					<h2><?php esc_html_e( 'ورود به حساب', 'digi-zoghal' ); ?></h2>
					<p><?php echo esc_html( $otp_enabled ? __( 'کد ورود برای شماره موبایل شما ارسال می‌شود.', 'digi-zoghal' ) : __( 'برای پیگیری سفارش‌ها وارد شوید.', 'digi-zoghal' ) ); ?></p>
				</div>
			</div>

			<?php if ( $otp_enabled ) : ?>
				<div class="dz-otp-login" data-dz-otp>
					<div class="form-row form-row-wide">
						<label for="dz-otp-phone"><?php esc_html_e( 'شماره موبایل', 'digi-zoghal' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
						<input type="tel" class="input-text" id="dz-otp-phone" autocomplete="tel" inputmode="numeric" value="<?php echo esc_attr( $posted_username ); ?>" placeholder="09123456789">
					</div>
					<button type="button" class="button dz-auth-submit" data-dz-otp-send><?php esc_html_e( 'دریافت کد ورود', 'digi-zoghal' ); ?></button>

					<div class="dz-otp-code-stage" data-dz-otp-code-stage hidden>
						<div class="form-row form-row-wide">
							<label for="dz-otp-code"><?php esc_html_e( 'کد ۶ رقمی پیامک‌شده', 'digi-zoghal' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
							<input type="text" class="input-text" id="dz-otp-code" autocomplete="one-time-code" inputmode="numeric" maxlength="6" pattern="[0-9۰-۹٠-٩]{6}" placeholder="ــــــ">
						</div>
						<div class="dz-otp-timer" data-dz-otp-timer role="timer" aria-live="polite">
							<span data-dz-otp-timer-label><?php esc_html_e( 'زمان باقی‌مانده برای واردکردن کد', 'digi-zoghal' ); ?></span>
							<strong data-dz-otp-timer-value>۰۲:۰۰</strong>
						</div>
						<div class="dz-otp-actions">
							<button type="button" class="button dz-auth-submit" data-dz-otp-verify><?php esc_html_e( 'ورود با کد', 'digi-zoghal' ); ?></button>
							<button type="button" class="dz-otp-change" data-dz-otp-change><?php esc_html_e( 'تغییر شماره', 'digi-zoghal' ); ?></button>
						</div>
					</div>
					<p class="dz-otp-status" data-dz-otp-status role="status" aria-live="polite"></p>
				</div>

				<div class="dz-auth-divider"><span><?php esc_html_e( 'یا', 'digi-zoghal' ); ?></span></div>
				<details class="dz-password-login" data-dz-password-login<?php if ( isset( $_POST['login'] ) ) { echo ' open'; } ?>>
					<summary><?php esc_html_e( 'ورود با رمز عبور', 'digi-zoghal' ); ?></summary>
			<?php endif; ?>

			<form class="woocommerce-form woocommerce-form-login login dz-password-form" method="post" novalidate>
				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="username"><?php esc_html_e( 'شماره موبایل، ایمیل یا نام کاربری', 'digi-zoghal' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
					<input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="username" id="username" autocomplete="username" dir="ltr" value="<?php echo esc_attr( $posted_username ); ?>" required aria-required="true">
				</p>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="password"><?php esc_html_e( 'رمز عبور', 'digi-zoghal' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
					<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true">
				</p>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<div class="dz-auth-options">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever">
						<span><?php esc_html_e( 'مرا به خاطر بسپار', 'digi-zoghal' ); ?></span>
					</label>
					<a href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php esc_html_e( 'رمز را فراموش کرده‌اید؟', 'digi-zoghal' ); ?></a>
				</div>

				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="woocommerce-button button woocommerce-form-login__submit dz-auth-submit" name="login" value="<?php esc_attr_e( 'ورود', 'digi-zoghal' ); ?>">
					<?php esc_html_e( 'ورود به حساب کاربری', 'digi-zoghal' ); ?>
				</button>

				<?php do_action( 'woocommerce_login_form_end' ); ?>
			</form>

			<?php if ( $otp_enabled ) : ?>
				</details>
			<?php endif; ?>
		</section>

		<form method="post" class="woocommerce-form woocommerce-form-register register dz-auth-card dz-auth-card-register" data-dz-registration-form <?php echo $show_registration ? '' : 'hidden'; ?> <?php do_action( 'woocommerce_register_form_tag' ); ?>>
			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<div class="dz-auth-card-head">
				<span class="dz-auth-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24"><path d="M10 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/><path d="M3 21a7 7 0 0 1 14 0M19 8v6M16 11h6"/></svg>
				</span>
				<div>
					<h2><?php esc_html_e( 'تکمیل حساب برای اولین ورود', 'digi-zoghal' ); ?></h2>
					<p><?php esc_html_e( 'شماره شما تأیید شد؛ این اطلاعات را فقط یک‌بار وارد کنید.', 'digi-zoghal' ); ?></p>
				</div>
			</div>

			<input type="hidden" name="registration_token" value="<?php echo esc_attr( $registration_token ); ?>" data-dz-registration-token>

			<div class="dz-auth-name-row">
				<p class="woocommerce-form-row form-row">
					<label for="reg_first_name"><?php esc_html_e( 'نام', 'digi-zoghal' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="first_name" id="reg_first_name" autocomplete="given-name" value="<?php echo esc_attr( $posted_firstname ); ?>" required aria-required="true">
				</p>
				<p class="woocommerce-form-row form-row">
					<label for="reg_last_name"><?php esc_html_e( 'نام خانوادگی', 'digi-zoghal' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="last_name" id="reg_last_name" autocomplete="family-name" value="<?php echo esc_attr( $posted_lastname ); ?>" required aria-required="true">
				</p>
			</div>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_username"><?php esc_html_e( 'شماره موبایل', 'digi-zoghal' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input type="tel" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="tel" inputmode="numeric" value="<?php echo esc_attr( $posted_username ); ?>" placeholder="09123456789" readonly required aria-required="true">
			</p>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_email"><?php esc_html_e( 'ایمیل', 'digi-zoghal' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo esc_attr( $posted_email ); ?>" required aria-required="true">
			</p>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_national_id"><?php esc_html_e( 'کد ملی', 'digi-zoghal' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="national_id" id="reg_national_id" autocomplete="off" inputmode="numeric" maxlength="10" pattern="[0-9۰-۹٠-٩]{10}" value="<?php echo esc_attr( $posted_national ); ?>" required aria-required="true">
			</p>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_password"><?php esc_html_e( 'رمز عبور', 'digi-zoghal' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true">
			</p>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_password_confirmation"><?php esc_html_e( 'تکرار رمز عبور', 'digi-zoghal' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password_confirmation" id="reg_password_confirmation" autocomplete="new-password" required aria-required="true">
			</p>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<p class="dz-auth-privacy"><?php esc_html_e( 'با ساخت حساب، قوانین خرید و حریم خصوصی فروشگاه را می‌پذیرید.', 'digi-zoghal' ); ?></p>
			<a class="dz-registration-reset" href="<?php echo esc_url( digi_zoghal_account_url() ); ?>"><?php esc_html_e( 'تغییر شماره یا دریافت کد تازه', 'digi-zoghal' ); ?></a>
			<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
			<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit dz-auth-submit" name="register" value="<?php esc_attr_e( 'ثبت‌نام', 'digi-zoghal' ); ?>">
				<?php esc_html_e( 'ساخت حساب کاربری', 'digi-zoghal' ); ?>
			</button>

			<?php do_action( 'woocommerce_register_form_end' ); ?>
		</form>
	</div>
</section>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>

