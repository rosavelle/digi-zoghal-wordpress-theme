<?php
/**
 * Digi Zoghal theme functions.
 *
 * @package Digi_Zoghal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DIGI_ZOGHAL_VERSION', '1.6.21' );
define( 'DIGI_ZOGHAL_WHOLESALE_MIN_QTY', 15 );

require_once get_template_directory() . '/inc/sms-otp.php';
require_once get_template_directory() . '/inc/account-address.php';

function digi_zoghal_setup() {
	load_theme_textdomain( 'digi-zoghal', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 96,
			'width'       => 96,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 480,
			'single_image_width'    => 760,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'max_rows'        => 8,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 4,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary' => __( 'منوی اصلی', 'digi-zoghal' ),
			'footer'  => __( 'منوی پایین سایت', 'digi-zoghal' ),
		)
	);
}
add_action( 'after_setup_theme', 'digi_zoghal_setup' );

function digi_zoghal_assets() {
	wp_enqueue_style(
		'digi-zoghal-fonts',
		'https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800;900&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'digi-zoghal-style', get_stylesheet_uri(), array(), DIGI_ZOGHAL_VERSION );

	if ( is_page_template( 'page-contact.php' ) || is_page( 'contact' ) ) {
		wp_enqueue_style(
			'digi-zoghal-contact',
			get_template_directory_uri() . '/assets/contact.css',
			array( 'digi-zoghal-style' ),
			DIGI_ZOGHAL_VERSION
		);
	}

	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		wp_enqueue_style(
			'digi-zoghal-account',
			get_template_directory_uri() . '/assets/account.css',
			array( 'digi-zoghal-style' ),
			DIGI_ZOGHAL_VERSION
		);

		wp_enqueue_script(
			'digi-zoghal-account',
			get_template_directory_uri() . '/assets/account.js',
			array(),
			DIGI_ZOGHAL_VERSION,
			true
		);

		wp_localize_script(
			'digi-zoghal-account',
			'digiZoghalAccount',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'digi_zoghal_otp' ),
				'redirect' => digi_zoghal_account_url(),
				'enabled'  => function_exists( 'digi_zoghal_sms_is_configured' ) && digi_zoghal_sms_is_configured(),
				'messages' => array(
					'invalidPhone' => __( 'شماره موبایل را به‌صورت ۰۹xxxxxxxxx وارد کنید.', 'digi-zoghal' ),
					'invalidCode'  => __( 'کد شش‌رقمی پیامک‌شده را وارد کنید.', 'digi-zoghal' ),
					'networkError' => __( 'ارتباط با سرور برقرار نشد. دوباره تلاش کنید.', 'digi-zoghal' ),
					'sendAgain'    => __( 'ارسال دوباره کد', 'digi-zoghal' ),
				),
			)
		);
	}

	$theme_dependencies = array();
	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
		$theme_dependencies[] = 'wc-cart-fragments';
	}

	wp_enqueue_script(
		'digi-zoghal-theme',
		get_template_directory_uri() . '/assets/theme.js',
		$theme_dependencies,
		DIGI_ZOGHAL_VERSION,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'digi_zoghal_assets' );

function digi_zoghal_shop_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

function digi_zoghal_cart_url() {
	return function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
}

function digi_zoghal_account_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
}

function digi_zoghal_contact_url() {
	$page_id = (int) get_option( 'digi_zoghal_contact_page_id', 0 );
	if ( $page_id && 'publish' === get_post_status( $page_id ) ) {
		return get_permalink( $page_id );
	}

	$page = get_page_by_path( 'contact', OBJECT, 'page' );
	return $page ? get_permalink( $page ) : home_url( '/contact/' );
}

/**
 * Create the contact page once, including when an already-active theme is updated.
 */
function digi_zoghal_maybe_create_contact_page() {
	if ( get_option( 'digi_zoghal_contact_page_initialized' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$page = get_page_by_path( 'contact', OBJECT, 'page' );
	if ( $page ) {
		$page_id = (int) $page->ID;
	} else {
		$page_id = wp_insert_post(
			array(
				'post_title'   => __( 'ارتباط با فروشگاه', 'digi-zoghal' ),
				'post_name'    => 'contact',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			),
			true
		);
	}

	if ( ! is_wp_error( $page_id ) && $page_id ) {
		update_option( 'digi_zoghal_contact_page_id', (int) $page_id );
		update_option( 'digi_zoghal_contact_page_initialized', 1 );
	}
}
add_action( 'after_switch_theme', 'digi_zoghal_maybe_create_contact_page' );
add_action( 'admin_init', 'digi_zoghal_maybe_create_contact_page' );

/**
 * Keep the customer area focused on the sections used by this store.
 */
function digi_zoghal_account_menu_items( $items ) {
	$labels = array(
		'dashboard'       => __( 'نمای کلی', 'digi-zoghal' ),
		'orders'          => __( 'سفارش‌ها', 'digi-zoghal' ),
		'edit-address'    => __( 'آدرس‌ها', 'digi-zoghal' ),
		'edit-account'    => __( 'اطلاعات حساب', 'digi-zoghal' ),
		'customer-logout' => __( 'خروج', 'digi-zoghal' ),
	);
	$menu   = array();

	foreach ( $labels as $endpoint => $label ) {
		if ( isset( $items[ $endpoint ] ) ) {
			$menu[ $endpoint ] = $label;
		}
	}

	return $menu;
}
add_filter( 'woocommerce_account_menu_items', 'digi_zoghal_account_menu_items' );

/**
 * Keep account registration available and collect credentials explicitly.
 */
function digi_zoghal_enable_account_registration() {
	return 'yes';
}
add_filter( 'pre_option_woocommerce_enable_myaccount_registration', 'digi_zoghal_enable_account_registration' );

function digi_zoghal_disable_generated_registration_fields() {
	return 'no';
}
add_filter( 'pre_option_woocommerce_registration_generate_username', 'digi_zoghal_disable_generated_registration_fields' );
add_filter( 'pre_option_woocommerce_registration_generate_password', 'digi_zoghal_disable_generated_registration_fields' );

/**
 * Convert Persian/Arabic digits and common Iranian phone prefixes to 09xxxxxxxxx.
 */
function digi_zoghal_normalize_phone( $phone ) {
	$phone = strtr(
		(string) $phone,
		array(
			'۰' => '0',
			'۱' => '1',
			'۲' => '2',
			'۳' => '3',
			'۴' => '4',
			'۵' => '5',
			'۶' => '6',
			'۷' => '7',
			'۸' => '8',
			'۹' => '9',
			'٠' => '0',
			'١' => '1',
			'٢' => '2',
			'٣' => '3',
			'٤' => '4',
			'٥' => '5',
			'٦' => '6',
			'٧' => '7',
			'٨' => '8',
			'٩' => '9',
		)
	);
	$phone = preg_replace( '/[^0-9+]/', '', $phone );

	if ( 0 === strpos( $phone, '+98' ) ) {
		$phone = '0' . substr( $phone, 3 );
	} elseif ( 0 === strpos( $phone, '0098' ) ) {
		$phone = '0' . substr( $phone, 4 );
	} elseif ( 0 === strpos( $phone, '98' ) && 12 === strlen( $phone ) ) {
		$phone = '0' . substr( $phone, 2 );
	}

	return $phone;
}

function digi_zoghal_is_phone_input( $value ) {
	return 1 === preg_match( '/^[0-9۰-۹٠-٩+()\-\s]+$/u', (string) $value );
}

function digi_zoghal_normalize_digits( $value ) {
	return preg_replace( '/\D+/', '', digi_zoghal_normalize_phone( $value ) );
}

function digi_zoghal_validate_national_id( $value ) {
	$national_id = digi_zoghal_normalize_digits( $value );
	if ( ! preg_match( '/^\d{10}$/', $national_id ) || preg_match( '/^(\d)\1{9}$/', $national_id ) ) {
		return false;
	}

	$sum = 0;
	for ( $index = 0; $index < 9; $index++ ) {
		$sum += (int) $national_id[ $index ] * ( 10 - $index );
	}

	$remainder = $sum % 11;
	$control   = $remainder < 2 ? $remainder : 11 - $remainder;
	return (int) $national_id[9] === $control;
}

/**
 * Normalize phone-shaped login and registration requests before WooCommerce handles them.
 */
function digi_zoghal_normalize_account_request() {
	if ( empty( $_POST['username'] ) ) {
		return;
	}

	$raw_username = sanitize_text_field( wp_unslash( $_POST['username'] ) );
	if ( isset( $_POST['register'] ) || digi_zoghal_is_phone_input( $raw_username ) ) {
		$_POST['username'] = digi_zoghal_normalize_phone( $raw_username );
	}
}
add_action( 'wp_loaded', 'digi_zoghal_normalize_account_request', 5 );

function digi_zoghal_phone_candidates( $phone ) {
	$phone = digi_zoghal_normalize_phone( $phone );
	if ( ! preg_match( '/^09\d{9}$/', $phone ) ) {
		return array();
	}

	$without_zero = substr( $phone, 1 );
	return array( $phone, '+98' . $without_zero, '98' . $without_zero, '0098' . $without_zero );
}

function digi_zoghal_get_user_by_phone( $phone ) {
	$candidates = digi_zoghal_phone_candidates( $phone );
	if ( empty( $candidates ) ) {
		return false;
	}

	$users = get_users(
		array(
			'number'     => 1,
			'count_total' => false,
			'meta_query' => array(
				array(
					'key'     => 'billing_phone',
					'value'   => $candidates,
					'compare' => 'IN',
				),
			),
		)
	);

	return ! empty( $users ) ? $users[0] : false;
}

/**
 * Let customers sign in with the phone saved on their WooCommerce account.
 */
function digi_zoghal_authenticate_by_phone( $user, $username, $password ) {
	if ( $user instanceof WP_User || is_wp_error( $user ) || empty( $username ) || empty( $password ) || ! digi_zoghal_is_phone_input( $username ) ) {
		return $user;
	}

	$phone = digi_zoghal_normalize_phone( $username );
	if ( ! preg_match( '/^09\d{9}$/', $phone ) ) {
		return $user;
	}

	$phone_user = get_user_by( 'login', $phone );
	if ( ! $phone_user ) {
		$phone_user = digi_zoghal_get_user_by_phone( $phone );
	}

	if ( ! $phone_user ) {
		return $user;
	}

	return wp_authenticate_username_password( null, $phone_user->user_login, $password );
}
add_filter( 'authenticate', 'digi_zoghal_authenticate_by_phone', 15, 3 );

function digi_zoghal_registration_errors( $errors, $username, $email ) {
	$phone                 = digi_zoghal_normalize_phone( $username );
	$password              = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';
	$password_confirmation = isset( $_POST['password_confirmation'] ) ? (string) wp_unslash( $_POST['password_confirmation'] ) : '';
	$national_id           = isset( $_POST['national_id'] ) ? digi_zoghal_normalize_digits( wp_unslash( $_POST['national_id'] ) ) : '';
	$registration_token    = isset( $_POST['registration_token'] ) ? sanitize_text_field( wp_unslash( $_POST['registration_token'] ) ) : '';

	if ( ! preg_match( '/^09\d{9}$/', $phone ) ) {
		$errors->add( 'registration-error-invalid-phone', __( 'شماره موبایل را به‌صورت ۰۹xxxxxxxxx وارد کنید.', 'digi-zoghal' ) );
	} elseif ( username_exists( $phone ) || digi_zoghal_get_user_by_phone( $phone ) ) {
		$errors->add( 'registration-error-phone-exists', __( 'این شماره موبایل قبلاً ثبت شده است. از بخش ورود استفاده کنید.', 'digi-zoghal' ) );
	}

	if ( empty( $_POST['first_name'] ) ) {
		$errors->add( 'registration-error-first-name', __( 'لطفاً نام خود را وارد کنید.', 'digi-zoghal' ) );
	}
	if ( empty( $_POST['last_name'] ) ) {
		$errors->add( 'registration-error-last-name', __( 'لطفاً نام خانوادگی خود را وارد کنید.', 'digi-zoghal' ) );
	}
	if ( ! digi_zoghal_validate_national_id( $national_id ) ) {
		$errors->add( 'registration-error-national-id', __( 'کد ملی واردشده معتبر نیست.', 'digi-zoghal' ) );
	}

	if ( '' === $password_confirmation || $password !== $password_confirmation ) {
		$errors->add( 'registration-error-password-mismatch', __( 'رمز عبور و تکرار آن یکسان نیستند.', 'digi-zoghal' ) );
	}

	if ( ! function_exists( 'digi_zoghal_sms_is_configured' ) || ! digi_zoghal_sms_is_configured() ) {
		$errors->add( 'registration-error-phone-verification', __( 'ثبت‌نام پیامکی موقتاً در دسترس نیست.', 'digi-zoghal' ) );
	} elseif ( preg_match( '/^09\d{9}$/', $phone ) ) {
		$validation = digi_zoghal_otp_validate_registration_token( $phone, $registration_token );
		if ( is_wp_error( $validation ) ) {
			$errors->add( 'registration-error-phone-verification', $validation->get_error_message() );
		}
	}

	return $errors;
}
add_filter( 'woocommerce_registration_errors', 'digi_zoghal_registration_errors', 10, 3 );

function digi_zoghal_save_registration_profile( $customer_id ) {
	$phone      = isset( $_POST['username'] ) ? digi_zoghal_normalize_phone( wp_unslash( $_POST['username'] ) ) : '';
	$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
	$national_id = isset( $_POST['national_id'] ) ? digi_zoghal_normalize_digits( wp_unslash( $_POST['national_id'] ) ) : '';
	$registration_token = isset( $_POST['registration_token'] ) ? sanitize_text_field( wp_unslash( $_POST['registration_token'] ) ) : '';

	update_user_meta( $customer_id, 'billing_phone', $phone );
	update_user_meta( $customer_id, 'billing_first_name', $first_name );
	update_user_meta( $customer_id, 'billing_last_name', $last_name );
	update_user_meta( $customer_id, 'billing_national_id', $national_id );
	digi_zoghal_otp_consume_registration_token( $registration_token );

	if ( preg_match( '/^09\d{9}$/', $phone ) ) {
		delete_transient( digi_zoghal_otp_transient_key( 'otp', $phone ) );
		delete_transient( digi_zoghal_otp_transient_key( 'cooldown', $phone ) );
	}

	wp_update_user(
		array(
			'ID'           => $customer_id,
			'first_name'   => $first_name,
			'last_name'    => $last_name,
			'display_name' => trim( $first_name . ' ' . $last_name ) ?: $phone,
		)
	);
}
add_action( 'woocommerce_created_customer', 'digi_zoghal_save_registration_profile' );

/**
 * Add an editable phone field to account details for existing customers.
 */
function digi_zoghal_edit_account_phone_field() {
	$user_id = get_current_user_id();
	woocommerce_form_field(
		'account_phone',
		array(
			'type'         => 'tel',
			'label'        => __( 'شماره موبایل', 'digi-zoghal' ),
			'required'     => true,
			'autocomplete' => 'tel',
			'input_class'  => array( 'input-text' ),
		),
		get_user_meta( $user_id, 'billing_phone', true )
	);
}
add_action( 'woocommerce_edit_account_form_start', 'digi_zoghal_edit_account_phone_field' );

function digi_zoghal_validate_account_phone( $errors, $user ) {
	$phone = isset( $_POST['account_phone'] ) ? digi_zoghal_normalize_phone( wp_unslash( $_POST['account_phone'] ) ) : '';
	if ( ! preg_match( '/^09\d{9}$/', $phone ) ) {
		$errors->add( 'account_phone', __( 'شماره موبایل را به‌صورت ۰۹xxxxxxxxx وارد کنید.', 'digi-zoghal' ) );
		return;
	}

	$phone_user = digi_zoghal_get_user_by_phone( $phone );
	if ( $phone_user && (int) $phone_user->ID !== (int) $user->ID ) {
		$errors->add( 'account_phone_exists', __( 'این شماره موبایل متعلق به حساب دیگری است.', 'digi-zoghal' ) );
	}
}
add_action( 'woocommerce_save_account_details_errors', 'digi_zoghal_validate_account_phone', 10, 2 );

function digi_zoghal_save_account_phone( $user_id ) {
	if ( isset( $_POST['account_phone'] ) ) {
		update_user_meta( $user_id, 'billing_phone', digi_zoghal_normalize_phone( wp_unslash( $_POST['account_phone'] ) ) );
	}
}
add_action( 'woocommerce_save_account_details', 'digi_zoghal_save_account_phone' );

function digi_zoghal_product_category_url( $slug ) {
	if ( taxonomy_exists( 'product_cat' ) ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			$url = get_term_link( $term );
			if ( ! is_wp_error( $url ) ) {
				return $url;
			}
		}
	}

	return digi_zoghal_shop_url();
}

function digi_zoghal_cart_count() {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		return WC()->cart->get_cart_contents_count();
	}
	return 0;
}

function digi_zoghal_cart_subtotal() {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		return WC()->cart->get_cart_subtotal();
	}
	return '';
}

function digi_zoghal_cart_fragments( $fragments ) {
	ob_start();
	?>
	<span class="count dz-cart-count"><?php echo esc_html( number_format_i18n( digi_zoghal_cart_count() ) ); ?></span>
	<?php
	$fragments['.dz-cart-count'] = ob_get_clean();

	ob_start();
	?>
	<span class="dz-cart-subtotal"><?php echo wp_kses_post( digi_zoghal_cart_subtotal() ); ?></span>
	<?php
	$fragments['.dz-cart-subtotal'] = ob_get_clean();

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'digi_zoghal_cart_fragments' );

function digi_zoghal_body_classes( $classes ) {
	if ( class_exists( 'WooCommerce' ) ) {
		$classes[] = 'digi-zoghal-has-woocommerce';
	}
	return $classes;
}
add_filter( 'body_class', 'digi_zoghal_body_classes' );

/**
 * Add wholesale fields to simple product pricing.
 */
function digi_zoghal_wholesale_fields() {
	if ( ! function_exists( 'woocommerce_wp_text_input' ) ) {
		return;
	}

	woocommerce_wp_text_input(
		array(
			'id'                => '_dz_wholesale_price',
			'label'             => __( 'قیمت عمده', 'digi-zoghal' ),
			'description'       => __( 'قیمت هر واحد برای خرید ۱۵ عدد یا بیشتر از همین محصول.', 'digi-zoghal' ),
			'desc_tip'          => true,
			'data_type'         => 'price',
			'custom_attributes' => array( 'min' => '0', 'step' => 'any' ),
		)
	);
}
add_action( 'woocommerce_product_options_pricing', 'digi_zoghal_wholesale_fields' );

function digi_zoghal_save_wholesale_fields( $product ) {
	$price = isset( $_POST['_dz_wholesale_price'] ) ? wc_format_decimal( wp_unslash( $_POST['_dz_wholesale_price'] ) ) : '';

	if ( (float) $price > 0 ) {
		$product->update_meta_data( '_dz_wholesale_price', $price );
	} else {
		$product->delete_meta_data( '_dz_wholesale_price' );
	}
}
add_action( 'woocommerce_admin_process_product_object', 'digi_zoghal_save_wholesale_fields' );

function digi_zoghal_cart_wholesale_rate( $item ) {
	$product = $item['data'];
	$price   = (float) $product->get_meta( '_dz_wholesale_price', true );

	return $price > 0 && $item['quantity'] >= DIGI_ZOGHAL_WHOLESALE_MIN_QTY ? $price : null;
}

function digi_zoghal_apply_wholesale_price( $cart ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return;
	}

	// Restore the pre-wholesale price if the same product object is recalculated below the threshold.
	static $original_prices;
	$original_prices = $original_prices ?? new SplObjectStorage();
	foreach ( $cart->get_cart() as $item ) {
		$product = $item['data'];
		$rate    = digi_zoghal_cart_wholesale_rate( $item );

		if ( null !== $rate ) {
			if ( ! $original_prices->contains( $product ) ) {
				$original_prices[ $product ] = $product->get_price( 'edit' );
			}
			$product->set_price( $rate );
		} elseif ( $original_prices->contains( $product ) ) {
			$product->set_price( $original_prices[ $product ] );
			$original_prices->detach( $product );
		}
	}
}

add_filter( 'woocommerce_cart_item_price', function ( $html, $item ) {
	$rate = digi_zoghal_cart_wholesale_rate( $item );
	if ( null === $rate ) {
		return $html;
	}

	// A mini-cart can be rendered before WooCommerce recalculates this request's product objects.
	$product = clone $item['data'];
	$product->set_price( $rate );
	return WC()->cart->get_product_price( $product );
}, 20, 2 );

add_action( 'woocommerce_before_calculate_totals', 'digi_zoghal_apply_wholesale_price', 20 );

function digi_zoghal_wholesale_price_html( $price_html, $product ) {
	$price   = (float) $product->get_meta( '_dz_wholesale_price', true );

	if ( $price > 0 ) {
		$price_html .= sprintf(
			'<small class="dz-wholesale-note">%1$s %2$s: %3$s</small>',
			esc_html__( 'عمده از', 'digi-zoghal' ),
			esc_html( number_format_i18n( DIGI_ZOGHAL_WHOLESALE_MIN_QTY ) ),
			wp_kses_post( wc_price( $price ) )
		);
	}

	return $price_html;
}
add_filter( 'woocommerce_get_price_html', 'digi_zoghal_wholesale_price_html', 20, 2 );

/**
 * Add a quantity selector to simple products in shop and category loops.
 */
function digi_zoghal_loop_quantity_picker( $html, $product, $args ) {
	if ( ! ( is_shop() || is_product_taxonomy() ) ) {
		return $html;
	}

	if ( ! $product || ! $product->is_type( 'simple' ) || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return $html;
	}

	$max_quantity = $product->get_max_purchase_quantity();
	$max_attr     = $max_quantity > 0 ? ' max="' . esc_attr( $max_quantity ) . '"' : '';

	return sprintf(
		'<div class="dz-buy-control dz-loop-buy-control" data-dz-buy-control><div class="dz-qty-picker" aria-label="%1$s"><button type="button" data-dz-qty="increase" aria-label="%2$s">+</button><input class="dz-qty-input" type="number" min="1"%3$s step="1" value="1" inputmode="numeric" data-dz-qty-input aria-label="%1$s"><button type="button" data-dz-qty="decrease" aria-label="%4$s">−</button></div>%5$s</div>',
		esc_attr( sprintf( __( 'تعداد %s', 'digi-zoghal' ), $product->get_name() ) ),
		esc_attr__( 'افزایش تعداد', 'digi-zoghal' ),
		$max_attr,
		esc_attr__( 'کاهش تعداد', 'digi-zoghal' ),
		$html
	);
}
add_filter( 'woocommerce_loop_add_to_cart_link', 'digi_zoghal_loop_quantity_picker', 10, 3 );

function digi_zoghal_loop_columns() {
	return 4;
}
add_filter( 'loop_shop_columns', 'digi_zoghal_loop_columns' );

function digi_zoghal_woocommerce_wrappers() {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	add_action(
		'woocommerce_before_main_content',
		function () {
			echo '<main class="dz-content"><div class="shell">';
		},
		10
	);
	add_action(
		'woocommerce_after_main_content',
		function () {
			echo '</div></main>';
		},
		10
	);
}
add_action( 'after_setup_theme', 'digi_zoghal_woocommerce_wrappers', 20 );

function digi_zoghal_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'digi_zoghal_contact',
		array(
			'title'    => __( 'اطلاعات تماس دیجی‌زغال', 'digi-zoghal' ),
			'priority' => 35,
		)
	);

	$fields = array(
		'dz_phone'         => array(
			'label'    => __( 'شماره تماس', 'digi-zoghal' ),
			'sanitize' => 'sanitize_text_field',
			'type'     => 'text',
		),
		'dz_whatsapp'      => array(
			'label'    => __( 'لینک واتساپ', 'digi-zoghal' ),
			'sanitize' => 'esc_url_raw',
			'type'     => 'url',
		),
		'dz_instagram'     => array(
			'label'    => __( 'لینک اینستاگرام', 'digi-zoghal' ),
			'sanitize' => 'esc_url_raw',
			'type'     => 'url',
		),
		'dz_telegram'      => array(
			'label'    => __( 'لینک تلگرام', 'digi-zoghal' ),
			'sanitize' => 'esc_url_raw',
			'type'     => 'url',
		),
		'dz_contact_email' => array(
			'label'    => __( 'ایمیل دریافت پیام‌های تماس', 'digi-zoghal' ),
			'sanitize' => 'sanitize_email',
			'type'     => 'email',
		),
		'dz_support_hours' => array(
			'label'    => __( 'ساعت پاسخ‌گویی', 'digi-zoghal' ),
			'sanitize' => 'sanitize_text_field',
			'type'     => 'text',
			'default'  => __( 'شنبه تا پنجشنبه، ساعت ۹ تا ۱۸', 'digi-zoghal' ),
		),
		'dz_store_address' => array(
			'label'    => __( 'نشانی فروشگاه (اختیاری)', 'digi-zoghal' ),
			'sanitize' => 'sanitize_textarea_field',
			'type'     => 'textarea',
		),
	);

	foreach ( $fields as $setting => $field ) {
		$wp_customize->add_setting(
			$setting,
			array(
				'default'           => isset( $field['default'] ) ? $field['default'] : '',
				'sanitize_callback' => $field['sanitize'],
			)
		);
		$wp_customize->add_control(
			$setting,
			array(
				'label'   => $field['label'],
				'section' => 'digi_zoghal_contact',
				'type'    => $field['type'],
			)
		);
	}
}
add_action( 'customize_register', 'digi_zoghal_customize_register' );

/**
 * Receive contact messages without requiring a separate form plugin.
 */
function digi_zoghal_handle_contact_message() {
	$fallback_url = digi_zoghal_contact_url();
	$referer      = wp_get_referer();
	$redirect_url = $referer ? wp_validate_redirect( $referer, $fallback_url ) : $fallback_url;
	$redirect_url = remove_query_arg( 'contact_status', $redirect_url );

	if ( 'POST' !== strtoupper( isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '' ) ) {
		wp_safe_redirect( add_query_arg( 'contact_status', 'error', $redirect_url ) . '#contact-form' );
		exit;
	}

	$nonce = isset( $_POST['digi_zoghal_contact_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['digi_zoghal_contact_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'digi_zoghal_contact_message' ) ) {
		wp_safe_redirect( add_query_arg( 'contact_status', 'error', $redirect_url ) . '#contact-form' );
		exit;
	}

	// Silently accept bot-filled submissions so the honeypot is not disclosed.
	if ( ! empty( $_POST['company_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'contact_status', 'success', $redirect_url ) . '#contact-form' );
		exit;
	}

	$name    = isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '';
	$phone   = isset( $_POST['contact_phone'] ) ? digi_zoghal_normalize_phone( wp_unslash( $_POST['contact_phone'] ) ) : '';
	$email   = isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '';
	$topic   = isset( $_POST['contact_topic'] ) ? sanitize_key( wp_unslash( $_POST['contact_topic'] ) ) : '';
	$order   = isset( $_POST['contact_order'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_order'] ) ) : '';
	$message = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ) ) : '';

	$topics = array(
		'order'     => __( 'پیگیری سفارش', 'digi-zoghal' ),
		'product'   => __( 'راهنمای انتخاب محصول', 'digi-zoghal' ),
		'wholesale' => __( 'خرید عمده', 'digi-zoghal' ),
		'payment'   => __( 'پرداخت و ارسال', 'digi-zoghal' ),
		'other'     => __( 'سایر موارد', 'digi-zoghal' ),
	);

	$name_length    = function_exists( 'mb_strlen' ) ? mb_strlen( $name ) : strlen( $name );
	$message_length = function_exists( 'mb_strlen' ) ? mb_strlen( $message ) : strlen( $message );
	$is_valid       = $name_length >= 2
		&& preg_match( '/^09\d{9}$/', $phone )
		&& isset( $topics[ $topic ] )
		&& $message_length >= 10
		&& $message_length <= 2000
		&& ( '' === $email || is_email( $email ) );

	if ( ! $is_valid ) {
		wp_safe_redirect( add_query_arg( 'contact_status', 'invalid', $redirect_url ) . '#contact-form' );
		exit;
	}

	$remote_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$rate_key       = 'dz_contact_' . substr( hash_hmac( 'sha256', $remote_address, wp_salt( 'nonce' ) ), 0, 32 );
	if ( get_transient( $rate_key ) ) {
		wp_safe_redirect( add_query_arg( 'contact_status', 'limited', $redirect_url ) . '#contact-form' );
		exit;
	}
	set_transient( $rate_key, 1, 2 * MINUTE_IN_SECONDS );

	$recipient = sanitize_email( get_theme_mod( 'dz_contact_email', '' ) );
	if ( ! $recipient ) {
		$recipient = sanitize_email( get_option( 'admin_email' ) );
	}

	$site_name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$subject   = sprintf( '[%1$s] %2$s', $site_name, $topics[ $topic ] );
	$body      = implode(
		"\n",
		array(
			sprintf( __( 'نام: %s', 'digi-zoghal' ), $name ),
			sprintf( __( 'موبایل: %s', 'digi-zoghal' ), $phone ),
			sprintf( __( 'ایمیل: %s', 'digi-zoghal' ), $email ? $email : '—' ),
			sprintf( __( 'موضوع: %s', 'digi-zoghal' ), $topics[ $topic ] ),
			sprintf( __( 'شماره سفارش: %s', 'digi-zoghal' ), $order ? $order : '—' ),
			'',
			$message,
		)
	);
	$headers   = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( $email ) {
		$headers[] = sprintf( 'Reply-To: %1$s <%2$s>', $name, $email );
	}

	$status = wp_mail( $recipient, $subject, $body, $headers ) ? 'success' : 'error';
	wp_safe_redirect( add_query_arg( 'contact_status', $status, $redirect_url ) . '#contact-form' );
	exit;
}
add_action( 'admin_post_digi_zoghal_contact_message', 'digi_zoghal_handle_contact_message' );
add_action( 'admin_post_nopriv_digi_zoghal_contact_message', 'digi_zoghal_handle_contact_message' );

function digi_zoghal_menu_fallback() {
	?>
	<ul class="primary-menu dz-menu-fallback">
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'digi-zoghal' ); ?></a></li>
		<?php $catalog_links = dz_catalog_category_links(); ?>
		<li<?php if ( $catalog_links ) { echo ' class="menu-item-has-children"'; } ?>>
			<a href="<?php echo esc_url( digi_zoghal_shop_url() ); ?>"><?php esc_html_e( 'فروشگاه', 'digi-zoghal' ); ?></a>
			<?php dz_render_catalog_links( $catalog_links, 'sub-menu' ); ?>
		</li>
		<li><a href="<?php echo esc_url( home_url( '/#wholesale' ) ); ?>"><?php esc_html_e( 'خرید عمده', 'digi-zoghal' ); ?></a></li>
		<li><a href="<?php echo esc_url( dz_about_url() ); ?>"><?php esc_html_e( 'درباره ما', 'digi-zoghal' ); ?></a></li>
		<li><a href="<?php echo esc_url( digi_zoghal_contact_url() ); ?>"><?php esc_html_e( 'تماس با ما', 'digi-zoghal' ); ?></a></li>
	</ul>
	<?php
}


require_once get_template_directory() . '/inc/account-favorites.php';

require_once get_template_directory() . '/inc/industrial-quotes.php';





add_filter( 'gettext_woocommerce', function ( $translated, $original ) {
	$labels = array(
		'Error:' => 'خطا:',
		'Username is required.' => 'شماره موبایل، ایمیل یا نام کاربری را وارد کنید.',
		'Username is required' => 'شماره موبایل، ایمیل یا نام کاربری را وارد کنید.',
		'Password is required.' => 'رمز عبور را وارد کنید.',
		'The password field is empty.' => 'رمز عبور را وارد کنید.',
	);
	return $labels[ $original ] ?? $translated;
}, 10, 2 );


require_once get_template_directory() . '/inc/catalog-menu.php';
require_once get_template_directory() . '/inc/catalog-images.php';
require_once get_template_directory() . '/inc/demo-payment.php';


require_once get_template_directory() . '/inc/store-copy.php';
