<?php

/**
 * Version 1.0.0.3 to 1.0.0.4 upgrade
 * 
 * Removed customerconnect "Cart Quickadd"
 */

if(Mage::helper('epicor_comm')->isModuleEnabled('Epicor_FlexiTheme')) {
    if (!Mage::helper('flexitheme/setup')->findLayoutBlock('epicor_comm/cart_quickadd')) {
        Mage::helper('flexitheme/setup')->createLayoutBlock('Quick add to Cart / Wishlist', 'epicor_comm/cart_quickadd', 'epicor_comm/cart/quickadd.phtml', NULL, NULL, NULL, NULL, 'epicor_comm.cart.quickadd', NULL);
    }
}