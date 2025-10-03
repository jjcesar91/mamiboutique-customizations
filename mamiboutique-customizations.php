<?php
/**
 * Plugin Name: Mami Boutique Customizations
 * Description: Central place for your site-specific PHP snippets (WooCommerce, theme tweaks, etc.).
 * Version:     1.0.32
 * Author:      Julio Cesar Plascencia
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SITE_SNIPPETS_PATH', plugin_dir_path( __FILE__ ) );

// Load snippet groups
require_once SITE_SNIPPETS_PATH . 'snippets/woocommerce.php';
require_once SITE_SNIPPETS_PATH . 'snippets/size-guide.php';
require_once SITE_SNIPPETS_PATH . 'snippets/styles.php';

/**
 * OPTIONAL: enqueue a custom CSS file for small tweaks.
 * Uncomment to use and create /assets/custom.css
 */
// add_action('wp_enqueue_scripts', function() {
//     wp_enqueue_style('site-snippets-custom', plugins_url('assets/custom.css', __FILE__), [], '1.0');
// });