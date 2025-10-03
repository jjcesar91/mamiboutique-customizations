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
        /*
        add_action('wp_enqueue_scripts', function () {
            if (!is_product()) return;

            // Assicura aggiornamento mini-cart
            wp_enqueue_script('wc-cart-fragments');

            // JS personalizzato (now from plugin directory)
            wp_enqueue_script(
                'wc-single-ajax-add-to-cart',
                plugins_url('assets/js/wc-single-ajax-add-to-cart.js', dirname(__FILE__)),
                ['jquery'],
                '1.8',
                true
            );

            // Passo endpoint AJAX + URL checkout al JS
            wp_add_inline_script(
                'wc-single-ajax-add-to-cart',
                'window.__wcSingleAjax = {
                    ajaxUrl: "' . esc_js( admin_url('admin-ajax.php') ) . '",
                    wcAjaxUrl: "' . esc_js( home_url('/') ) . '?wc-ajax=",
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
        */


        

    }
});

/**
 * Menu items with images
 * If a menu item has a Title Attribute that's a URL, prepend an <img>
 * Uses multiple approaches for maximum theme compatibility including mega menus
 */

// Primary approach: Hook into walker output (most reliable)
add_filter('walker_nav_menu_start_el', function ($item_output, $item, $depth, $args) {
    // Check if the title attribute contains a valid URL
    if (!empty($item->attr_title) && filter_var($item->attr_title, FILTER_VALIDATE_URL)) {
        // Extract the link text from the item output
        $link_text = $item->title;
        
        // Create the image tag
        $img = '<img class="menu-img" src="' . esc_url($item->attr_title) . '" alt="' . esc_attr(wp_strip_all_tags($link_text)) . '">';
        
        // Replace the link text with image + text
        $new_text = $img . '<span class="menu-text">' . $link_text . '</span>';
        
        // Replace the title in the output
        $item_output = str_replace($link_text, $new_text, $item_output);
    }
    
    return $item_output;
}, 10, 4);

// Fallback approach: Hook into title filter (for themes that support it)
add_filter('nav_menu_item_title', function ($title, $item, $args, $depth) {
    if (!empty($item->attr_title) && filter_var($item->attr_title, FILTER_VALIDATE_URL)) {
        $img = '<img class="menu-img" src="' . esc_url($item->attr_title) . '" alt="' . esc_attr(wp_strip_all_tags($title)) . '">';
        // Image + text (remove the span if you want image-only)
        $title = $img . '<span class="menu-text">' . $title . '</span>';
    }
    return $title;
}, 10, 4);

// Mega Menu approach: Use JavaScript to transform after page load
add_action('wp_footer', function() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Find all menu links with title attributes that are URLs
        var menuLinks = document.querySelectorAll('a.mega-menu-link[title], .menu a[title], nav a[title], .navbar a[title]');
        
        menuLinks.forEach(function(link) {
            var titleAttr = link.getAttribute('title');
            
            // Check if title attribute is a valid URL
            if (titleAttr && (titleAttr.startsWith('http://') || titleAttr.startsWith('https://'))) {
                var linkText = link.textContent.trim();
                
                // Create image element that fills the full width
                var img = document.createElement('img');
                img.src = titleAttr;
                img.alt = linkText;
                img.className = 'menu-img-full';
                img.style.display = 'block';
                img.style.width = '100%';
                img.style.height = 'auto';
                img.style.objectFit = 'contain';
                img.style.margin = '0';
                img.style.maxHeight = '60px'; // Adjust this as needed
                
                // Clear the link and add only the image (no text)
                link.innerHTML = '';
                link.appendChild(img);
                
                // Style the link to fill the container
                link.style.display = 'block';
                link.style.width = '100%';
                link.style.textAlign = 'center';
                link.style.padding = '10px 5px'; // Add some padding
                
                // Remove the title attribute so it doesn't show as tooltip
                link.removeAttribute('title');
            }
        });
    });
    </script>
    <?php
});