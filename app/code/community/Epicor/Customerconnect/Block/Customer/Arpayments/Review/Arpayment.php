<?php
/**
 * AR Payments Payment
 *
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Arpayments_Review_Arpayment extends Mage_Core_Block_Template
{
    
    public function __construct() {
        parent::__construct();
    } 
    
    public function Combinevalues($postParams) {
        $helper = Mage::helper('customerconnect/arpayments');
        /* @var $helper Epicor_Customerconnect_Helper_Arpayments */ 
        return $helper->Combinevalues($postParams);
    }
    
    public function CombineTotalAmount($postParams) {
        $helper = Mage::helper('customerconnect/arpayments');
        /* @var $helper Epicor_Customerconnect_Helper_Arpayments */ 
        return $helper->CombineTotalAmount($postParams);
    }    
    
}