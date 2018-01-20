<?php

class Epicor_Comm_Block_Catalog_Product_View_Options_Type_Ecc_Text extends Mage_Catalog_Block_Product_View_Options_Type_Text
{

    protected $_template = 'epicor_comm/catalog/product/view/options/type/ecc/text.phtml';

    public function getValidationClass($option)
    {
        $class = '';

        switch ($option->getEpicorValidationCode()) {
            case 'CSNS':
                $class = 'validate-csns';
                break;
            case 'email':
                $class = 'validate-email';
                break;
            case 'alphanumeric':
                $class = 'validate-alphanum';
                break;
            case 'numeric':
                $class = 'validate-number';
                break;
        }

        return $class;
    }
    
}
