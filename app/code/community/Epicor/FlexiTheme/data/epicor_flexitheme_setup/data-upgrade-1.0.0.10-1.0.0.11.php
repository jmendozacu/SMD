<?php

/**
 * Version 1.0.0.9 to 1.0.0.10 upgrade
 * 
 * Fixes cart sidebar flexitheme xml
 */

//$layout_block = Mage::getModel('flexitheme/layout_block')->load('checkout/cart_sidebar','block_type');
///* @var $layout_block FlexiTheme_Model_Layout_Block */
//
//$block_xml = ' <block type="epicor_comm/cart_product_csvupload" name="csvupload" translate="label" template="epicor_comm/cart/product/csvupload.phtml>
//                    <label>Add Product to Cart By Csv</label>
//                </block>';
//      
////        <block type="core/text_list" name="cart_sidebar.extra_actions" as="extra_actions" translate="label" module="checkout">
////                    <label>Shopping Cart Sidebar Extra Actions</label>
////                </block>''
//';
//
//$layout_block->setBlockXml($block_xml);
//$layout_block->save();


/* * **********************************************************************
  Step : Populate Layout Blocks
 * *********************************************************************** */
$helper = Mage::helper('flexitheme/setup');
/* @var $helper Epicor_FlexiTheme_Helper_Setup */

if ($helper->isModuleEnabled('Epicor_Comm')) {   
    if (!$helper->findLayoutBlock('epicor_comm/cart_product_csvupload')) {
        $helper->createLayoutBlock('Cart Update By Csv', 'epicor_comm/cart_product_csvupload', 'epicor_comm/cart/product/csvupload.phtml', NULL, NULL, NULL, NULL, 'csvupload', NULL);
    }
}
