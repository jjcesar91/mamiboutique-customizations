<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Ensure we only run WooCommerce-related snippets if WooCommerce is active
 */
add_action( 'plugins_loaded', function () {
    if ( class_exists( 'WooCommerce' ) ) {
        /**
         * Move short description (excerpt) below the "Add to cart" button
         * Default priorities:
         *  - Title: 5
         *  - Rating: 10
         *  - Price: 10
         *  - Excerpt: 20 (we move it)
         *  - Add to cart: 30
         */

        // Size guide functionality (functions are defined in size-guide.php)
        add_action( 'woocommerce_single_product_summary', 'display_size_guide_link', 27 );
        add_action( 'wp_footer', 'add_size_guide_modal_html' );

        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
        add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 35 );
        
        // WC Trust Links below Add to Cart button
        add_action('woocommerce_single_product_summary', function () {
            echo '<div class="wc-trust-links" style="margin-top:12px; font-size:14px; line-height:1.4;">'
                . '<a href="' . esc_url( home_url('/spedizioni-e-pagamenti/') ) . '">Pagamenti e Spedizioni Sicuri</a>'
                . ' &nbsp;•&nbsp; '
                . '<a href="' . esc_url( home_url('/politica-di-reso/') ) . '">Politica di reso</a>'
                . '</div>';
        }, 36);
        
        // AJAX Add to Cart functionality
        add_action('wp_enqueue_scripts', function () {
            if (!is_product()) return;

            // Assicura aggiornamento mini-cart
            wp_enqueue_script('wc-cart-fragments');

            // JS personalizzato (now from plugin directory)
            wp_enqueue_script(
                'wc-single-ajax-add-to-cart',
                plugins_url('assets/js/wc-single-ajax-add-to-cart.js', dirname(__FILE__)),
                ['jquery'],
                '1.1',
                true
            );

            // Passo endpoint AJAX + URL checkout al JS
            wp_add_inline_script(
                'wc-single-ajax-add-to-cart',
                'window.__wcSingleAjax = {
                    wcAjaxUrl: "' . esc_js( WC_AJAX::get_endpoint('%%endpoint%%') ) . '",
                    checkoutUrl: "' . esc_js( wc_get_checkout_url() ) . '"
                };',
                'before'
            );
        }, 20);

        // Add to Cart Modal HTML
        add_action('wp_footer', function () {
            if (!is_product()) return; ?>
            <div class="wc-added-modal-backdrop" id="wc-added-modal-backdrop" aria-hidden="true">
                <div class="wc-added-modal" role="dialog" aria-live="polite" aria-label="Prodotto aggiunto al carrello">
                    <span class="wc-close" id="wc-added-modal-close" aria-label="Chiudi">✕</span>
                    <span class="wc-check">✓</span>
                    <div class="wc-title">Prodotto aggiunto al carrello</div>
                    <div class="wc-actions">
                        <button type="button" class="btn btn-outline" id="wc-continue-shopping">CONTINUA LO SHOPPING</button>
                        <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn" id="wc-go-checkout">COMPLETA L'ACQUISTO</a>
                    </div>
                </div>
            </div>
        <?php });
        
        /**
         * === Add more WooCommerce snippets below ===
         * Example:
         *
         * add_filter( 'woocommerce_get_price_html', function( $price, $product ) {
         *     if ( is_product() ) {
         *         $price .= ' <span class="iva-label">(IVA inclusa)</span>';
         *     }
         *     return $price;
         * }, 100, 2 );
         */
    }
});