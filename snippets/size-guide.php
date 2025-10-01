<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Size Guide Modal Functions
 * Functions are called from woocommerce.php via add_action
 */

/**
 * Display size guide link on product page
 */
function display_size_guide_link() {
    echo '<div class="size-guide-wrapper" style="display: block; width: 100%; clear: both; margin: 15px 0; float: none;"><a href="#" id="size-guide-link" class="size-guide-link" onclick="openSizeGuideModal(); return false;">Guida alle taglie</a></div><br style="clear: both; line-height: 0; height: 0;">';
}

/**
 * Add modal HTML to footer on product pages
 */
function add_size_guide_modal_html() {
    if ( ! is_product() ) {
        return;
    }
    ?>
    <!-- Size Guide Modal -->
    <div id="sizeGuideModal" class="size-guide-modal" style="display: none;">
        <div class="size-guide-modal-overlay" onclick="closeSizeGuideModal()"></div>
        <div class="size-guide-modal-content">
            <div class="size-guide-modal-header">
                <h3>Guida alle taglie</h3>
                <span class="size-guide-modal-close" onclick="closeSizeGuideModal()">&times;</span>
            </div>
            <div class="size-guide-modal-body">
                <table class="size-chart-table">
                    <thead>
                        <tr>
                            <th>TAGLIA</th>
                            <th>Vestibilità in CM</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1m</td>
                            <td>55 cm</td>
                        </tr>
                        <tr>
                            <td>3m</td>
                            <td>62 cm</td>
                        </tr>
                        <tr>
                            <td>6m</td>
                            <td>68 cm</td>
                        </tr>
                        <tr>
                            <td>9m</td>
                            <td>74 cm</td>
                        </tr>
                        <tr>
                            <td>12m</td>
                            <td>80 cm</td>
                        </tr>
                        <tr>
                            <td>18m</td>
                            <td>86 cm</td>
                        </tr>
                        <tr>
                            <td>24m</td>
                            <td>92 cm</td>
                        </tr>
                        <tr>
                            <td>36m</td>
                            <td>98 cm</td>
                        </tr>
                        <tr>
                            <td>4a</td>
                            <td>104 cm</td>
                        </tr>
                        <tr>
                            <td>5a</td>
                            <td>110 cm</td>
                        </tr>
                        <tr>
                            <td>6a</td>
                            <td>116 cm</td>
                        </tr>
                        <tr>
                            <td>7a</td>
                            <td>122 cm</td>
                        </tr>
                        <tr>
                            <td>8a</td>
                            <td>128 cm</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function openSizeGuideModal() {
        document.getElementById('sizeGuideModal').style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent background scroll
    }

    function closeSizeGuideModal() {
        document.getElementById('sizeGuideModal').style.display = 'none';
        document.body.style.overflow = 'auto'; // Restore scroll
    }

    // Close modal when pressing Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeSizeGuideModal();
        }
    });

    // Move size guide link to the correct position
    document.addEventListener('DOMContentLoaded', function() {
        var sizeGuideWrapper = document.querySelector('.size-guide-wrapper');
        var variationsTable = document.querySelector('.variations');
        var singleVariationWrap = document.querySelector('.single_variation_wrap');
        
        if (sizeGuideWrapper && variationsTable && singleVariationWrap) {
            // Move the size guide link after the variations table but before single_variation_wrap
            singleVariationWrap.parentNode.insertBefore(sizeGuideWrapper, singleVariationWrap);
            
            // Add proper styling to ensure it's on its own line
            sizeGuideWrapper.style.display = 'block';
            sizeGuideWrapper.style.width = '100%';
            sizeGuideWrapper.style.margin = '15px 0';
            sizeGuideWrapper.style.clear = 'both';
        }
    });
    </script>
    <?php
}