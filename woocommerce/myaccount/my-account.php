<?php
/**
 * Customer account layout.
 *
 * @package Digi_Zoghal
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="dz-account-page">
	<div class="dz-account-layout">
		<?php do_action( 'woocommerce_account_navigation' ); ?>

		<main class="dz-account-main">
			<div class="woocommerce-MyAccount-content">
				<?php do_action( 'woocommerce_account_content' ); ?>
			</div>
		</main>
	</div>
</div>
