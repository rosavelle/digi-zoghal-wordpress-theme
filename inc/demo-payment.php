<?php
/**
 * Explicitly enabled simulation for disposable local or staging stores.
 */

defined( 'ABSPATH' ) || exit;

if (
	! defined( 'DIGI_ZOGHAL_DEMO_PAYMENT' ) ||
	true !== DIGI_ZOGHAL_DEMO_PAYMENT ||
	! in_array( wp_get_environment_type(), array( 'local', 'staging' ), true )
) {
	return;
}

function dz_demo_payment_settings( $settings ) {
	return array_replace(
		(array) $settings,
		array(
			'enabled' => 'yes',
			'title' => __( 'پرداخت آزمایشی', 'digi-zoghal' ),
			'description' => __( 'سفارش آموزشی است و هیچ مبلغی دریافت نمی‌شود.', 'digi-zoghal' ),
			'instructions' => __( 'سفارش آزمایشی ثبت شد. نیازی به واریز وجه نیست.', 'digi-zoghal' ),
			'enable_for_methods' => array(),
			'enable_for_virtual' => 'yes',
		)
	);
}

add_filter( 'option_woocommerce_cod_settings', 'dz_demo_payment_settings' );
add_filter( 'default_option_woocommerce_cod_settings', 'dz_demo_payment_settings' );
add_filter( 'woocommerce_cod_process_payment_order_status', function () {
	return 'on-hold';
} );
