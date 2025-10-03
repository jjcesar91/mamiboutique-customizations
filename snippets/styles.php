<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Global Custom Styles
 * This file contains all custom CSS styles for the site
 */

/**
 * Add custom styles to the site
 */
function add_custom_site_styles() {
    ?>
    <style>
    /* Size Guide Wrapper Styles */
    .size-guide-wrapper {
        display: block !important;
        width: 100% !important;
        margin: 15px 0 20px 0 !important;
        clear: both !important;
        float: none !important;
        position: relative !important;
    }
    .size-guide-wrapper::after {
        content: "";
        display: block;
        clear: both;
        height: 0;
        width: 100%;
    }

    /* Size Guide Link Styles */
    .size-guide-link {
        display: inline-block;
        color: #BFA389;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }
    .size-guide-link:hover {
        color: #BFA389;
        text-decoration: underline;
    }

    /* WC Trust Links - Same styling as size guide link */
    .wc-trust-links,
    .wc-trust-links a {
        display: inline-block;
        color: #BFA389 !important;
        text-decoration: none !important;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }
    .wc-trust-links:hover,
    .wc-trust-links a:hover {
        color: #BFA389 !important;
        text-decoration: underline !important;
    }

    /* WooCommerce Cart Form Styles */
    .woocommerce div.product form.cart {
        clear: both !important;
        display: block !important;
        width: 100% !important;
        margin-top: 10px !important;
    }
    .woocommerce div.product form.cart div.quantity,
    .woocommerce div.product form.cart .single_add_to_cart_button {
        clear: none !important;
        float: left !important;
        margin-right: 10px !important;
    }

    /* Size Guide Modal Styles */
    .size-guide-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
    }
    .size-guide-modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }
    .size-guide-modal-content {
        position: relative;
        background-color: #fff;
        margin: 5% auto;
        padding: 0;
        width: 90%;
        max-width: 600px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-height: 80vh;
        overflow-y: auto;
    }
    .size-guide-modal-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .size-guide-modal-header h3 {
        margin: 0;
        font-size: 18px;
        color: #333;
    }
    .size-guide-modal-close {
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        color: #999;
        line-height: 1;
    }
    .size-guide-modal-close:hover {
        color: #000;
    }
    .size-guide-modal-body {
        padding: 20px;
    }

    /* Size Chart Table Styles */
    .size-chart-table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        margin-top: 10px;
    }
    .size-chart-table th {
        background-color: #f8f8f8;
        font-weight: bold;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 15px 20px;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
        color: #333;
    }
    .size-chart-table td {
        padding: 12px 20px;
        font-size: 14px;
        color: #666;
        border-bottom: 1px solid #f0f0f0;
    }
    .size-chart-table tbody tr:hover {
        background-color: #fafafa;
    }
    .size-chart-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .size-guide-modal-content {
            width: 95%;
            margin: 10% auto;
            max-height: 70vh;
        }
    }

    /* AJAX Add to Cart Modal Styles */
    .wc-added-modal-backdrop {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0,0,0,.35);
        z-index: 99998;
    }
    .wc-added-modal {
        max-width: 980px;
        width: 90%;
        background: #cc8a3a;
        color: #fff;
        border-radius: 10px;
        box-shadow: 0 8px 30px rgba(0,0,0,.25);
        position: relative;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 18px;
    }
    .wc-added-modal .wc-check {
        font-size: 18px;
        margin-right: 6px;
    }
    .wc-added-modal .wc-title {
        font-weight: 700;
        font-size: 18px;
        flex: 1;
    }
    .wc-added-modal .wc-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .wc-added-modal .btn {
        background: #fff;
        border: 0;
        border-radius: 8px;
        padding: 10px 16px;
        font-weight: 700;
        letter-spacing: .06em;
        color: #222;
        cursor: pointer;
        text-decoration: none;
    }
    .wc-added-modal .btn-outline {
        background: transparent;
        border: 2px solid rgba(255,255,255,.85);
        color: #fff;
    }
    .wc-added-modal .wc-close {
        position: absolute;
        top: 10px;
        right: 12px;
        color: #fff;
        opacity: .9;
        cursor: pointer;
        font-size: 18px;
        line-height: 1;
    }
    @media (max-width: 640px) {
        .wc-added-modal {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
            padding: 16px;
        }
        .wc-added-modal .wc-actions {
            justify-content: center;
        }
        .wc-added-modal .wc-close {
            top: 8px;
            right: 10px;
        }
    }

    /* Menu Items with Images */
    .menu img.menu-img,
    ul.menu img.menu-img,
    nav img.menu-img,
    .navbar img.menu-img,
    .navigation img.menu-img,
    .mega-menu img.menu-img,
    .mega-sub-menu img.menu-img,
    a.mega-menu-link img.menu-img {
        display: inline-block !important;
        width: 24px !important;
        height: 24px !important;
        object-fit: cover !important;
        margin-right: .5rem !important;
        vertical-align: middle !important;
    }
    .menu .menu-text,
    ul.menu .menu-text,
    nav .menu-text,
    .navbar .menu-text,
    .navigation .menu-text,
    .mega-menu .menu-text,
    .mega-sub-menu .menu-text,
    a.mega-menu-link .menu-text {
        vertical-align: middle !important;
    }
    
    /* Full-width menu images (image-only menu items) */
    .menu img.menu-img-full,
    ul.menu img.menu-img-full,
    nav img.menu-img-full,
    .navbar img.menu-img-full,
    .navigation img.menu-img-full,
    .mega-menu img.menu-img-full,
    .mega-sub-menu img.menu-img-full,
    a.mega-menu-link img.menu-img-full {
        display: block !important;
        width: 100% !important;
        height: auto !important;
        object-fit: contain !important;
        margin: 0 !important;
        max-height: 60px !important;
    }
    
    /* Style the mega menu items with full-width images */
    .mega-menu-item a.mega-menu-link:has(img.menu-img-full),
    .mega-sub-menu li a:has(img.menu-img-full) {
        display: block !important;
        width: 100% !important;
        text-align: center !important;
        padding: 10px 5px !important;
        box-sizing: border-box !important;
    }
    </style>
    <?php
}

// Hook the styles to wp_head globally
add_action( 'wp_head', 'add_custom_site_styles' );