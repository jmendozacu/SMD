<?php

/**
 * RFQ details js block
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */

class Epicor_Comm_Block_Catalog_Product_Ewa_Translation extends Epicor_Common_Block_Js_Translation
{

    protected function _construct()
    {
        parent::_construct();
        
        $translations = array(
            /* skin/frontend/base/default/epicor/comm/js/configurableAddToCart.js */
            'Warning: Your changes will be lost if you close. Click OK if you are you sure you want to close without saving.' => $this->__('Warning: Your changes will be lost if you close. Click OK if you are you sure you want to close without saving.'),
        );
        
        $this->setTranslations($translations);
    }

}
