<?php

/**
 * EWA option type
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Catalog_Product_Edit_Tab_Options_Type_Ewa extends
Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('epicor_comm/catalog/product/edit/options/type/ewa.phtml');
    }

}
