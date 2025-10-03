/**
 * AJAX Add to Cart for Single Product Pages
 * Prevents page refresh when adding products to cart
 */
(function ($) {
  'use strict';

  $(document).ready(function () {

    // ===== Utilities =====
    function wcAjax(endpoint) {
      if (window.wc_add_to_cart_params && window.wc_add_to_cart_params.wc_ajax_url) {
        return window.wc_add_to_cart_params.wc_ajax_url.replace('%%endpoint%%', endpoint);
      }
      if (window.__wcSingleAjax && window.__wcSingleAjax.wcAjaxUrl) {
        let u = window.__wcSingleAjax.wcAjaxUrl;
        if (!/\/$/.test(u)) u += '/';
        return u + endpoint;
      }
      return '/?wc-ajax=' + endpoint;
    }

    function resetBtn($btn, originalText) {
      $btn.removeClass('loading').attr('disabled', false);
      const $t = $btn.find('.qodef-m-text');
      if ($t.length) $t.text(originalText || 'Aggiungi al carrello');
    }

    function stripAddToCart(qs) {
      return qs.replace(/(^|&)(add-to-cart)=[^&]*/g, '');
    }

    // ===== Core handler =====
    $('form.cart').each(function () {
      const $form = $(this);

      // Track Woo’s currently selected variation (most reliable source of truth)
      let lastVariation = null; // { variation_id, attributes: {attribute_pa_color: "...", attribute_pa_size:"..."} }

      // Woo triggers this when a valid variation is found after selecting attrs
      $form.on('found_variation', function (_e, variation) {
        lastVariation = variation || null;
        if (lastVariation && lastVariation.attributes) {
          console.log('FOUND VARIATION:', lastVariation.variation_id, lastVariation.attributes);
        }
      });

      // Woo triggers this when selections are cleared/changed
      $form.on('reset_data hide_variation', function () {
        lastVariation = null;
      });

      $form.off('submit.customAjaxATC').on('submit.customAjaxATC', function (e) {
        e.preventDefault();

        const $button = $form.find('.single_add_to_cart_button');

        console.log('Form submission started');
        console.log('Button classes:', $button[0] && $button[0].className);

        if ($button.hasClass('disabled')) {
          console.log('Button is disabled - stopping submission');
          return;
        }

        const $bt = $button.find('.qodef-m-text');
        const originalText = $bt.length ? $bt.text() : null;
        $button.addClass('loading').attr('disabled', true);
        if ($bt.length) $bt.text('Aggiungendo...');

        const isVariable = $form.hasClass('variations_form');

        // Simple product → just serialize (without add-to-cart) and submit
        if (!isVariable) {
          const body = stripAddToCart($form.serialize());
          $.ajax({
            type: 'POST',
            url: wcAjax('add_to_cart'),
            data: body,
            dataType: 'json'
          }).done(handleResponse).fail(netErr).always(() => resetBtn($button, originalText));
          return;
        }

        // ===== Variable product flow =====
        const productId =
          $form.find('input[name="product_id"]').val() ||
          $form.find('button[name="add-to-cart"]').val() || '';

        const qty = $form.find('input[name="quantity"]').val() || 1;

        // If Woo hasn’t resolved a variation yet, bail early with a clear message
        const variationIdInput = $form.find('input[name="variation_id"]').val();
        const canUseLast = lastVariation && lastVariation.variation_id && lastVariation.attributes;

        if (!canUseLast) {
          // Try to prompt Woo to resolve; otherwise show message
          console.warn('No resolved variation from Woo yet. variation_id input =', variationIdInput);
          resetBtn($button, originalText);
          alert('Seleziona tutte le opzioni del prodotto.');
          return;
        }

        // Build payload using Woo’s exact keys/values (e.g., attribute_pa_color / attribute_pa_size)
        const payload = [
          { name: 'product_id', value: productId },
          { name: 'variation_id', value: lastVariation.variation_id },
          { name: 'quantity', value: qty }
        ];

        Object.keys(lastVariation.attributes || {}).forEach(function (k) {
          const v = lastVariation.attributes[k];
          if (v !== '' && v != null) payload.push({ name: k, value: v });
        });

        const body = stripAddToCart($.param(payload));
        console.log('AJAX URL:', wcAjax('add_to_cart'));
        console.log('Posting:', body);

        $.ajax({
          type: 'POST',
          url: wcAjax('add_to_cart'),
          data: body,
          dataType: 'json'
        }).done(handleResponse).fail(netErr).always(() => resetBtn($button, originalText));
      });
    });

    // ===== Response handling & modal =====
    function handleResponse(response) {
      console.log('AJAX Response:', response);
      if (response && response.fragments) {
        if (typeof window.showAddedToCartModal === 'function') window.showAddedToCartModal();
        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, null]);
        $(document.body).trigger('wc_fragment_refresh');
      } else if (response && response.error && response.product_url) {
        alert('Seleziona tutte le opzioni del prodotto prima di aggiungerlo al carrello.');
      } else if (response && response.error) {
        alert('Errore nell\'aggiungere il prodotto al carrello');
      } else {
        $(document.body).trigger('wc_fragment_refresh');
        if (typeof window.showAddedToCartModal === 'function') window.showAddedToCartModal();
      }
    }

    function netErr() {
      alert('Errore di connessione. Riprova.');
    }

    // ===== Modal controls =====
    window.showAddedToCartModal = window.showAddedToCartModal || function () {
      var $backdrop = $('#wc-added-modal-backdrop');
      $backdrop.css('display', 'flex').attr('aria-hidden', 'false');
      setTimeout(function () {
        hideAddedToCartModal();
      }, 5000);
    };

    function hideAddedToCartModal() {
      var $backdrop = $('#wc-added-modal-backdrop');
      $backdrop.css('display', 'none').attr('aria-hidden', 'true');
    }

    $(document).on('click', '#wc-added-modal-close, #wc-continue-shopping', function (e) {
      e.preventDefault();
      hideAddedToCartModal();
    });

    $(document).on('click', '#wc-added-modal-backdrop', function (e) {
      if (e.target === this) hideAddedToCartModal();
    });

    $(document).on('keydown', function (e) {
      if (e.key === 'Escape') hideAddedToCartModal();
    });

  });

})(jQuery);