<?php

/**
 * Translation bank
 * 
 * Add any translations here that are from models/ helpers in the comm module
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Translationbank extends Mage_Core_Block_Template
{

    /**
     * Class constructor
     */
    public function _construct()
    {
        parent::_construct();
        
        $this->__('Product Codes:');
        $this->__('Product Code:');
    }

}
