<?php

/**
 * Product Csvupload block
 * 
 * Adds products supplied in csv file to basket . 
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Cart_Product_Csvupload
        extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();
        $this->setTitle($this->__('Product Add to Basket by CSV'));
    }

}
