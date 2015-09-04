<?php
/**
 * Woo Email Generator.
 *
 * @package   Woo_Email_Generator
 * @author    Max Kostinevich <contact@maxkostinevich.com>
 * @license   GPL-2.0+
 * @link      https://maxkostinevich.com
 * @copyright 2015 Max Kostinevich
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * @package Woo_Email_Generator
 * @author  Max Kostinevich <contact@maxkostinevich.com>
 */
class Woo_Email_Generator {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'woo-email-generator';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		add_image_size( 'wooemail-size', 265, 215, true );

		// Add AJAX support to search products
		add_action('wp_ajax_wooemail_ajax_search', array($this, 'handle_ajax_search'));
		// Add AJAX support to generate HTML-template
		add_action('wp_ajax_wooemail_ajax_generate_html', array($this, 'handle_ajax_generate_html'));
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}


	/**
	 * Handle AJAX calls to search products
	 *
	 * @since    1.0.0
	 */
	public function handle_ajax_search(){
		// Security check
		check_ajax_referer( 'wooemail_ajax_search', 'nonce' );

		if(isset($_POST['search'])){
			$search=trim($_POST['search']);

			if(is_numeric($search)){ //if search is integer - search in product IDs
				$search=(int)$search;
			   $args = array(
				   'post__in' => array($search),
				   'post_type' => 'product',
				   'paged' => $paged,
				   'posts_per_page' => -1

			   );
			}else{
				$args = array(
					's' => $search,
					'post_type' => 'product',
					'paged' => $paged,
					'posts_per_page' => -1

				);
			}

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
						<div class="product-buy-now"><img src="<?php echo plugins_url( 'assets/btn-buy-now.png',dirname(__FILE__));?>"></div>
					</div>
				<?php endwhile; else:
				echo '<h3>Nothing found</h3>';
				endif;
				wp_reset_postdata();

		}else {
			echo '';
		}
		die();
	}



	/**
	 * Handle AJAX calls to generate HTML-template
	 *
	 * @since    1.0.0
	 */
	public function handle_ajax_generate_html(){
		// Security check
		check_ajax_referer( 'wooemail_ajax_generate_html', 'nonce' );

		if(isset($_POST['products'])){
			$products=$_POST['products'];
			$optionsVAT=$_POST['optionsVAT'];

			$templatePath=plugin_dir_path(dirname(__FILE__)).'assets';

			$templateMain=file_get_contents($templatePath.'/template-main.html');
			$templateProduct=file_get_contents($templatePath.'/template-product.html');
			$templateRow=file_get_contents($templatePath.'/template-product-row.html');
			$templateDivider=file_get_contents($templatePath.'/template-divider.html');

			$productsHTML=array();

			$generatedHTML='<html><body>'."\r\n";

			$productVATText=($optionsVAT==1)?'incl. TAX':'excl. TAX';
			$productIndex=1;

			// Generate Single Product HTML
			foreach($products as $productID){

				$product = wc_get_product( $productID );



				//Get product Image URL

				//$productImage=$product->get_image('wooemail-size')
				$productImage=wp_get_attachment_image_src( get_post_thumbnail_id($productID), 'wooemail-size' );
				if (!$productImage[0]){$productImage='';}else{ $productImage=$productImage[0];}

				$product_price=($optionsVAT==1)?wc_price($product->get_price_including_tax()):wc_price($product->get_price_excluding_tax());


				$productTemplateTags=array(
					'[[PRODUCT-ALIGN]]',
					'[[PRODUCT-PERMALINK]]',
					'[[PRODUCT-IMAGE]]',
					'[[PRODUCT-TITLE]]',
					'[[PRODUCT-PRICE]]',
					'[[PRODUCT-VAT]]',
					'[[PRODUCT-BTN-BUY]]');
				$productTemplateTagsValues=array(
					($productIndex % 2 == 0)?'right':'left',
					$product->get_permalink(),
					$productImage,
					$product->get_title(),
					$product_price,
					$productVATText,
					'<img src="'.plugins_url( 'assets/btn-buy-now.png',dirname(__FILE__)).'">'
				);
				// Compose the product template with actual values
				$productHTML=str_replace($productTemplateTags,$productTemplateTagsValues,$templateProduct);
				array_push($productsHTML,$productHTML);
				$productIndex++;

			}

			// Group products into rows (2 per column)
			$temlateItemList='';
			$productsRow=array_chunk($productsHTML,2);
			foreach($productsRow as $row){
				$items=implode(" ",$row);
				$rowHTML=str_replace('[[PRODUCT-ROW]]',$items,$templateRow);
				$temlateItemList.=$rowHTML;
			}

			// Generate Final HTML
			echo str_replace('[[ITEM-LIST]]',$temlateItemList,$templateMain);

		}else {
			echo '';
		}
		die();
	}







}
