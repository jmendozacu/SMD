<?php

/**
 * Version 1.0.0.3 to 1.0.0.4 upgrade
 * 
 * Removed customerconnect "Cart Quickadd"
 */
if (Mage::helper('customerconnect')->isModuleEnabled('Epicor_FlexiTheme')) {
    $block = Mage::getModel('flexitheme/layout_block')->load('customerconnect/cart_quickadd', 'block_type');
    if ($block) {
        $block->delete();
    }
}
