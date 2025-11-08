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
        
        // Free shipping message after price
        add_action( 'woocommerce_single_product_summary', 'display_free_shipping_message', 15 );
        
        // Free shipping message on cart page
        add_action( 'woocommerce_before_cart', 'display_cart_free_shipping_message' );
        
        // Sticky filter button on shop page
        add_action( 'wp_footer', 'add_shop_sticky_filter_button' );
        
        // WC Trust Links below Add to Cart button
        add_action('woocommerce_single_product_summary', function () {
            echo '<div class="wc-trust-links" style="margin-top:12px; font-size:14px; line-height:1.4;">'
                . '<a href="' . esc_url( home_url('/spedizioni-e-pagamenti/') ) . '">Pagamenti e Spedizioni Sicuri</a>'
                . ' &nbsp;•&nbsp; '
                . '<a href="' . esc_url( home_url('/politica-di-reso/') ) . '">Politica di reso</a>'
                . '</div>';
        }, 36);

        // Highlight required WooCommerce checkout fields not filled in
        // Evidenzia i campi obbligatori del checkout non compilati con bordo rosso
        
        // Add CSS for error field highlighting
        add_action('wp_head', function() {
            if (!is_checkout()) return;
            ?>
            <style>
            /* WooCommerce checkout field error highlighting */
            .woocommerce-invalid input,
            .woocommerce-invalid select,
            .woocommerce-invalid textarea {
                border: 2px solid #e53935 !important;
                background-color: #ffecec !important;
                transition: all 0.3s ease !important;
            }
            
            /* Select2 dropdown error highlighting */
            .woocommerce-invalid .select2-container .select2-selection {
                border: 2px solid #e53935 !important;
                background-color: #ffecec !important;
            }
            
            /* Additional targeting for checkout validation errors */
            .checkout .form-row.woocommerce-invalid input,
            .checkout .form-row.woocommerce-invalid select,
            .checkout .form-row.woocommerce-invalid textarea {
                border: 2px solid #e53935 !important;
                background-color: #ffecec !important;
            }
            </style>
            <?php
        });
        
        // Add JavaScript for additional error highlighting
        add_action('wp_footer', function() {
            if (!is_checkout()) return;
            ?>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                
                // Function to highlight error fields
                function highlightErrorFields() {
                    // Method 1: Use error list data-id attributes
                    const errorItems = document.querySelectorAll('.woocommerce-error li[data-id]');
                    errorItems.forEach(item => {
                        const fieldId = item.getAttribute('data-id');
                        if (fieldId) {
                            const field = document.querySelector('#' + fieldId);
                            if (field) {
                                field.style.border = '2px solid #e53935';
                                field.style.backgroundColor = '#ffecec';
                                field.style.transition = 'all 0.3s ease';
                                
                                // Handle Select2 dropdowns
                                const select2Container = field.nextElementSibling;
                                if (select2Container && select2Container.classList.contains('select2-container')) {
                                    const select2Selection = select2Container.querySelector('.select2-selection');
                                    if (select2Selection) {
                                        select2Selection.style.border = '2px solid #e53935';
                                        select2Selection.style.backgroundColor = '#ffecec';
                                    }
                                }
                            }
                        }
                    });
                    
                    // Method 2: Target fields with aria-invalid="true"
                    const invalidFields = document.querySelectorAll('input[aria-invalid="true"], select[aria-invalid="true"], textarea[aria-invalid="true"]');
                    invalidFields.forEach(field => {
                        field.style.border = '2px solid #e53935';
                        field.style.backgroundColor = '#ffecec';
                        field.style.transition = 'all 0.3s ease';
                    });
                    
                    // Method 3: Target fields in woocommerce-invalid containers
                    const invalidContainers = document.querySelectorAll('.woocommerce-invalid input, .woocommerce-invalid select, .woocommerce-invalid textarea');
                    invalidContainers.forEach(field => {
                        field.style.border = '2px solid #e53935';
                        field.style.backgroundColor = '#ffecec';
                        field.style.transition = 'all 0.3s ease';
                    });
                }
                
                // Run immediately
                highlightErrorFields();
                
                // Run after AJAX updates
                if (typeof jQuery !== 'undefined') {
                    jQuery(document.body).on('updated_checkout', function() {
                        setTimeout(highlightErrorFields, 100);
                    });
                    
                    jQuery(document.body).on('checkout_error', function() {
                        setTimeout(highlightErrorFields, 100);
                    });
                }
                
                // Run after form validation attempts
                const checkoutForm = document.querySelector('form.checkout');
                if (checkoutForm) {
                    checkoutForm.addEventListener('submit', function() {
                        setTimeout(highlightErrorFields, 500);
                    });
                }
                
                // Periodically check for errors (fallback)
                setInterval(highlightErrorFields, 2000);
            });
            </script>
            <?php
        });

        // WooCommerce Account Page Login Redirect Fix
        // Evita redirect al login admin e mostra sempre il login WooCommerce
        add_action('template_redirect', function() {
            if ( is_admin() ) return; // ignora backend
            if ( ! is_user_logged_in() && is_account_page() ) {
                remove_action('template_redirect', 'wp_redirect_admin_locations');
            }
        });

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

/**
 * Display free shipping message after product price
 */
function display_free_shipping_message() {
    if ( ! is_product() ) return;
    
    ?>
    <div class="free-shipping-message" style="
        background-color: #fff;
        border: 2px solid #BFA389;
        border-radius: 10px;
        padding: 5px 10px;
        margin-top: 20px;
        font-size: 16px;
        color: #BFA389;
        text-align: center;
        font-weight: 500;
        letter-spacing: 0.5px;
    ">
        Raggiungi almeno 99€ di spesa totale per ottenere la spedizione gratuita!
    </div>
    <?php
}

/**
 * Display dynamic free shipping message on cart page
 */
function display_cart_free_shipping_message() {
    if ( ! is_cart() ) return;
    
    // Get cart total
    $cart_total = WC()->cart->get_subtotal();
    $free_shipping_threshold = 99;
    
    // Calculate remaining amount
    $remaining_amount = $free_shipping_threshold - $cart_total;
    
    // Only show if cart total is less than threshold
    if ( $remaining_amount > 0 ) {
        ?>
        <div class="cart-free-shipping-message" style="
            background-color: #fff;
            border: 2px solid #BFA389;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
            font-size: 16px;
            color: #BFA389;
            text-align: center;
            font-weight: 500;
            letter-spacing: 0.5px;
        ">
            Aggiungi altri <?php echo wc_price( $remaining_amount ); ?> alla tua spesa totale per ottenere la spedizione gratuita!
        </div>
        <?php
    }
}

/**
 * Add sticky filter button and full-screen filter panel on shop page
 */
function add_shop_sticky_filter_button() {
    if ( ! is_shop() && ! is_product_category() && ! is_product_tag() ) return;
    ?>
    <!-- Sticky Filter Button -->
    <button id="sticky-filter-btn" class="sticky-filter-btn" aria-label="Apri filtri">
        <span class="filter-icon">⚙</span>
        <span class="filter-text">FILTRO</span>
    </button>
    
    <!-- Full Screen Filter Panel -->
    <div id="filter-panel" class="filter-panel">
        <div class="filter-panel-header">
            <h2>Filtri</h2>
            <button id="close-filter-btn" class="close-filter-btn" aria-label="Chiudi filtri">
                <span class="close-icon">✕</span>
            </button>
        </div>
        <div class="filter-panel-content">
            <?php echo do_shortcode('[wpf-filters id=1]'); ?>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtn = document.getElementById('sticky-filter-btn');
        const filterPanel = document.getElementById('filter-panel');
        const closeBtn = document.getElementById('close-filter-btn');
        
        if (!filterBtn || !filterPanel || !closeBtn) return;
        
        // Open filter panel
        filterBtn.addEventListener('click', function() {
            filterPanel.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        });
        
        // Close filter panel
        closeBtn.addEventListener('click', function() {
            filterPanel.classList.remove('active');
            document.body.style.overflow = ''; // Restore scrolling
        });
        
        // Close on overlay click
        filterPanel.addEventListener('click', function(e) {
            if (e.target === filterPanel) {
                filterPanel.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
        
        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && filterPanel.classList.contains('active')) {
                filterPanel.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
    </script>
    <?php
}
