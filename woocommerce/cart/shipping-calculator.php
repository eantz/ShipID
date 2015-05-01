<?php
/**
 * Shipping Calculator
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.8
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( get_option( 'woocommerce_enable_shipping_calc' ) === 'no' || ! WC()->cart->needs_shipping() )
	return;
?>

<?php do_action( 'woocommerce_before_shipping_calculator' ); ?>

<?php 
//$wp_session = WP_Session::get_instance();
//print_r(isset($wp_session['customer_custom']) ? $wp_session['customer_custom'] : array()); 
?>

<form class="woocommerce-shipping-calculator" action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">

	<h2><a href="#" class="shipping-calculator-button"><?php _e( 'Calculate Shipping', 'woocommerce' ); ?></a></h2>

	<section class="">

		<p class="form-row form-row-wide">
			<?php 
				// echo WC()->countries->get_base_country();
				if(WC()->countries->get_base_country() == 'ID') {
					echo 'Indonesia';
					?> <input type="hidden" name="calc_shipping_country" value="ID"> <?php
				} else {
					echo 'This shipping method only accept order from Indonesia';
				}
			 ?>


		</p>

		<p class="form-row form-row-wide">
			<?php
				$current_cc = WC()->countries->get_base_country();
				$current_r  = WC()->customer->get_shipping_state();
				$states     = WC()->countries->get_states( $current_cc );

				// Hidden Input
				if ( is_array( $states ) && empty( $states ) ) {

					?><input type="hidden" name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php _e( 'State / county', 'woocommerce' ); ?>" /><?php

				// Dropdown Input
				} elseif ( is_array( $states ) ) {

					?><span>
						<?php if($current_cc == 'ID') : ?>
							<select name="calc_shipping_state" id="calc_shipping_state" class="calc_shipping_state_id" placeholder="<?php _e( 'State / county', 'woocommerce' ); ?>"></select>
							<!--<input type="hidden" name="calc_shipping_state" id="calc_shipping_state">-->
						<?php else : ?>
							<select name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php _e( 'State / county', 'woocommerce' ); ?>">
								<option value=""><?php _e( 'Select a state&hellip;', 'woocommerce' ); ?></option>
								<?php
									foreach ( $states as $ckey => $cvalue )
										echo '<option value="' . esc_attr( $ckey ) . '" ' . selected( $current_r, $ckey, false ) . '>' . __( esc_html( $cvalue ), 'woocommerce' ) .'</option>';
								?>
							</select>
						<?php endif; ?>
					</span><?php

				// Standard Input
				} else {

					?><input type="text" class="input-text" value="<?php echo esc_attr( $current_r ); ?>" placeholder="<?php _e( 'State / county', 'woocommerce' ); ?>" name="calc_shipping_state" id="calc_shipping_state" /><?php

				}
			?>
		</p>

		<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_city', false ) ) : ?>

			<p class="form-row form-row-wide">
				<?php if($current_cc == 'ID') : ?>
					<select name="calc_shipping_city" id="calc_shipping_city" class="calc_shipping_city_id"></select>
					<!--<input type="hidden" name="calc_shipping_city" id="calc_shipping_city">-->
				<?php else : ?>
					<input type="text" class="input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_city() ); ?>" placeholder="<?php _e( 'City', 'woocommerce' ); ?>" name="calc_shipping_city" id="calc_shipping_city" />
				<?php endif;?>
			</p>

		<?php endif; ?>

		<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_postcode', true ) ) : ?>

			<p class="form-row form-row-wide">
				<input type="text" class="input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_postcode() ); ?>" placeholder="<?php _e( 'Postcode / Zip', 'woocommerce' ); ?>" name="calc_shipping_postcode" id="calc_shipping_postcode" />
			</p>

		<?php endif; ?>

		<p><button type="submit" name="calc_shipping" value="1" class="button"><?php _e( 'Update Totals', 'woocommerce' ); ?></button></p>

		<?php wp_nonce_field( 'woocommerce-cart' ); ?>
	</section>
</form>

<?php do_action( 'woocommerce_after_shipping_calculator' ); ?>