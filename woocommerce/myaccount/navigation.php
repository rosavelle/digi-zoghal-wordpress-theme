<?php
/**
 * Customer account navigation.
 *
 * @package Digi_Zoghal
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$display_name = $current_user->display_name ? $current_user->display_name : __( 'کاربر دیجی زغال', 'digi-zoghal' );
$phone        = get_user_meta( $current_user->ID, 'billing_phone', true );
$initial      = function_exists( 'mb_substr' ) ? mb_substr( trim( $display_name ), 0, 1, 'UTF-8' ) : substr( trim( $display_name ), 0, 1 );
$menu_items   = wc_get_account_menu_items();
$icons        = array(
	'dashboard'       => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>',
	'orders'          => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h14v16H5z"/><path d="M8 9h8M8 13h8M8 17h5"/></svg>',
	'edit-address'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>',
	'edit-account'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>',
	'customer-logout' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 5H5v14h5M14 8l4 4-4 4M18 12H9"/></svg>',
);
?>
<nav class="woocommerce-MyAccount-navigation dz-account-sidebar" aria-label="<?php esc_attr_e( 'بخش‌های حساب کاربری', 'digi-zoghal' ); ?>">
	<div class="dz-account-user">
		<span class="dz-account-avatar" aria-hidden="true"><?php echo esc_html( $initial ); ?></span>
		<span class="dz-account-user-copy">
			<strong><?php echo esc_html( $display_name ); ?></strong>
			<small><?php echo esc_html( $phone ? $phone : $current_user->user_email ); ?></small>
		</span>
	</div>

	<ul>
		<?php foreach ( $menu_items as $endpoint => $label ) : ?>
			<?php $is_current = wc_is_current_account_menu_item( $endpoint ); ?>
			<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"<?php echo $is_current ? ' aria-current="page"' : ''; ?>>
					<span class="dz-account-menu-icon"><?php echo $icons[ $endpoint ] ?? ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span><?php echo esc_html( $label ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<p class="dz-account-side-note">
		<?php esc_html_e( 'سفارش‌ها و اطلاعات این بخش مستقیماً از حساب واقعی شما در فروشگاه خوانده می‌شوند.', 'digi-zoghal' ); ?>
	</p>
</nav>
