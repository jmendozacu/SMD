<?php

/**
 * Entity register log details renderer
 *
 * @category   Epicor
 * @package    Epicor_Comm
 * @author Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Advanced_Entity_Register_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    private $_typesMap = array(
        'ErpAccount' => 'CUS - ERP Account',
        'SupplierErpAccount' => 'SUSP - ERP Account',
        'ErpAddress' => 'CUS / CAD - ERP Addresses',
        'Related' => 'ALT - Related',
        'UpSell' => 'ALT - UpSell',
        'CrossSell' => 'ALT - CrossSell',
        'CustomerSku' => 'CPN - Customer Sku',
        'CategoryProduct' => 'SGP - Category Products',
        'Category' => 'STG - Categories',
        'Product' => 'STK - Products',
        'Customer' => 'CUCO - Customer Contacts',
        'Supplier' => 'SUCO - Supplier Contacts'
    );

    /**
     * Render column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $data = $row->getData($this->getColumn()->getIndex());

        return isset($this->_typesMap[$data]) ? $this->_typesMap[$data] : $data;
    }

}
