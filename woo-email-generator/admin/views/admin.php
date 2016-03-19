<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Woo_Email_Generator
 * @author    Max Kostinevich <contact@maxkostinevich.com>
 * @license   GPL-2.0+
 * @link      https://maxkostinevich.com
 * @copyright 2015 Max Kostinevich
 */
?>

<?php
/**
 *-----------------------------------------
 * Do not delete this line
 * Added for security reasons: http://codex.wordpress.org/Theme_Development#Template_Files
 *-----------------------------------------
 */
defined('ABSPATH') or die("Direct access to the script does not allowed");
?>

<div class="wrap wooemail-admin-page">

	<div id="popup-generated-html" style="display: none">
		<div class="popup-inner-content">
			<div class="btn-nav-bar"><button id="wooemail-btn-preview-html" class="button-primary">Preview HTML</button></div>
			<div><textarea class="generated-html"></textarea></div>
		</div>
	</div>

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<div id="poststuff">
		<div class="wooemail-columns-wrapper">

			<!-- left sidebar -->
			<div class="wooemail-column-left">

				<div class="meta-box-sortables ui-sortable1">
					<div class="postbox">
						<div class="inside">
							<div class="wooemail-search-wrapper">
								<form method="post" id="wooemail-form-search">
									<input type="text" placeholder="Search products.." name="wooemail-search" id="wooemail-search" class="all-options" /> <input type="submit" name="wooemail-btn-search" id="wooemail-btn-search" class="button-secondary" value="Search">
								</form>
							</div>
							<div id="wooemail-products" class="wooemail-products-wrapper">
								<?php
								$args = array(
									'post_type' => 'product',
									'paged' => 1,
									'posts_per_page' => -1

								);
								$the_query = new WP_Query($args);
								if ($the_query->have_posts()) :
									while ($the_query->have_posts()):
										$the_query->the_post();
										$product_id = get_the_ID();
										$product = wc_get_product( $product_id );
								?>
								<div class="wooemail-product-item">
									<div class="product-picture"><?php echo $product->get_image('wooemail-size');?></div>
									<div class="product-info" data-product-id="<?php echo $product_id;?>">
										<div class="product-title"><?php echo $product->get_title();?></div>
										<div  class="product-price"><span class="price-title">Price:</span> <?php echo wc_price($product->get_price());?></div>
										<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
											<div class="product-sku"><?php _e( 'SKU:', 'woocommerce' ); ?> <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'woocommerce' ); ?></span></div>
										<?php endif; ?>
										<div class="product-descr"><?php echo wp_trim_words($product->post->post_excerpt,22,'...');?></div>
									</div>
									<div class="product-buy-now"><img src="<?php echo plugins_url( 'assets/btn-buy-now.png',dirname(dirname(__FILE__)));?>"></div>
								</div>
								<?php endwhile; endif; wp_reset_postdata(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- end left sidebar -->


			<!-- main content -->
			<div class="wooemail-column-right">

				<div class="meta-box-sortables ui-sortable1">
					<div class="postbox">
						<div class="inside">
							<div class="wooemail-template-options">
								<label><input type="checkbox" id="wooemail-options-vat"> Include TAX</label>
								<input type="button" class="button-primary" id="btn-generate-html" value="Generate HTML">
							</div>
							<div id="wooemail-product-container">
								<div class="template-placeholder">
									Move items here <br><br>
									<img src="<?php echo plugins_url( '../assets/img/icon-drop.png', __FILE__ );?>">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- end main content -->


		</div>
		<!-- end post-body-->

		<br class="clear">
	</div>
	<!-- end poststuff -->

</div>





