/**
 * AJAX Add to Cart for Single Product Pages
 * Prevents page refresh when adding products to cart
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle add to cart form submission
        $('form.cart').on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $button = $form.find('.single_add_to_cart_button');
            
            // Don't proceed if button is disabled
            if ($button.hasClass('disabled') || $button.hasClass('wc-variation-selection-needed')) {
                return;
            }
            
            // Show loading state
            $button.addClass('loading').attr('disabled', true);
            var originalText = $button.find('.qodef-m-text').text();
            $button.find('.qodef-m-text').text('Aggiungendo...');
            
            // Prepare form data
            var formData = $form.serialize();
            formData += '&action=woocommerce_add_to_cart_variable_rc';
            
            // AJAX request
            $.ajax({
                type: 'POST',
                url: window.__wcSingleAjax.wcAjaxUrl.replace('%%endpoint%%', 'add_to_cart'),
                data: formData,
                success: function(response) {
                    if (response.error) {
                        // Handle error
                        console.error('Add to cart error:', response);
                        alert('Errore nell\'aggiungere il prodotto al carrello');
                    } else {
                        // Success - show modal
                        showAddedToCartModal();
                        
                        // Update cart fragments (mini cart, etc.)
                        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);
                    }
                },
                error: function() {
                    alert('Errore di connessione. Riprova.');
                },
                complete: function() {
                    // Reset button state
                    $button.removeClass('loading').attr('disabled', false);
                    $button.find('.qodef-m-text').text(originalText);
                }
            });
        });
        
        // Show the "added to cart" modal
        function showAddedToCartModal() {
            var $backdrop = $('#wc-added-modal-backdrop');
            $backdrop.css('display', 'flex').attr('aria-hidden', 'false');
            
            // Auto hide after 5 seconds
            setTimeout(function() {
                hideAddedToCartModal();
            }, 5000);
        }
        
        // Hide the modal
        function hideAddedToCartModal() {
            var $backdrop = $('#wc-added-modal-backdrop');
            $backdrop.css('display', 'none').attr('aria-hidden', 'true');
        }
        
        // Modal close handlers
        $(document).on('click', '#wc-added-modal-close, #wc-continue-shopping', function(e) {
            e.preventDefault();
            hideAddedToCartModal();
        });
        
        // Close modal when clicking backdrop
        $(document).on('click', '#wc-added-modal-backdrop', function(e) {
            if (e.target === this) {
                hideAddedToCartModal();
            }
        });
        
        // Close modal with Escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                hideAddedToCartModal();
            }
        });
    });
    
})(jQuery);