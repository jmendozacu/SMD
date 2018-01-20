<?php

/**
 * RFQ Details page buttons
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Product_Configure extends Mage_Catalog_Block_Product_View
{

    private $_status;

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('customerconnect/customer/account/rfqs/details/product_configure.phtml');
    }
    
    public function getProductOptionsHtml()
    {
        $product = Mage::registry('current_product');
        /* @var $product Epicor_Comm_Model_Product */
        
        $helper = Mage::helper('epicor_comm/product');
        /* @var $helper Epicor_Comm_Helper_Product */
        
        $child = Mage::registry('child_product');
        /* @var $child Epicor_Comm_Model_Product */
        
        $options = Mage::registry('options_data');
        
        return $helper->getProductOptionsHtml($product, $child, $options);
    }

}
