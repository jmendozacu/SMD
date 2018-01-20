<?php

/**
 * Version 1.0.0.0 to 1.0.0.1 upgrade
 * 
 * Removes B2b "Customer Account Summary" 
 */
if (Mage::helper('b2b')->isModuleEnabled('Epicor_FlexiTheme')) {
    $block = Mage::getModel('flexitheme/layout_block')->load('b2b/customer_account_summary', 'block_type');
    if ($block) {
        $block->delete();
    }
}
