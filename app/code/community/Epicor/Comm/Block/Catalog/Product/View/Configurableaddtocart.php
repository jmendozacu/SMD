<?php

/**
 * Quick add block
 * 
 * Displays the quick add to Basket / wishlist block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Catalog_Product_View_Configurableaddtocart extends Mage_Core_Block_Template {

    public function _construct() {
        parent::_construct(); 
    }
    public function getQty(){
        return Mage::app()->getRequest()->getParam('qty');
    }

}
