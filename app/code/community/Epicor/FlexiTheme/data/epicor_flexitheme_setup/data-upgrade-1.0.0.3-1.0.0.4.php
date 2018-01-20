<?php

/**
 * Version 1.0.0.3 to 1.0.0.4 upgrade
 * 
 * Removes B2b customer account summary (it's moved to customerconnect)
 * Removes Customerconnect cart quickadd and adds it into comm
 */
$helper = Mage::helper('flexitheme/setup');
/* @var $helper Epicor_Flexitheme_Helper_Setup */
/* * **********************************************************************
  Step : Populate Layout Blocks
 * *********************************************************************** */

if ($helper->isModuleEnabled('Epicor_B2b')) {
    $block = Mage::getModel('flexitheme/layout_block')->load('b2b/customer_account_summary', 'block_type');
    if ($block) {
        $block->delete();
    }
}

if ($helper->isModuleEnabled('Epicor_Customerconnect')) {
    $block = Mage::getModel('flexitheme/layout_block')->load('customerconnect/cart_quickadd', 'block_type');
    if ($block) {
        $block->delete();
    }
}

if ($helper->isModuleEnabled('Epicor_Comm')) {
    if (!$helper->findLayoutBlock('epicor_comm/cart_quickadd')) {
        $helper->createLayoutBlock('Quick add to Cart / Wishlist', 'epicor_comm/cart_quickadd', 'epicor_comm/cart/quickadd.phtml', NULL, NULL, NULL, NULL, 'epicor_comm.cart.quickadd', NULL);
    }
}
