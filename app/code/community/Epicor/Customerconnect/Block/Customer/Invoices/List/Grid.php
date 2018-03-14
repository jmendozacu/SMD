<?php

/**
 * Customer Invoices list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Customer Connect
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Invoices_List_Grid extends Epicor_Common_Block_Generic_List_Search
{

    public function __construct()
    {
        parent::__construct();
        
        $this->setFooterPagerVisibility(true);
        $this->setId('customerconnect_invoices_grid');
        $this->setMessageBase('customerconnect');
        $this->setMessageType('cuis');
        $this->setIdColumn('invoice_number');
        
        
        $this->initColumns();
        $this->setExportTypeCsv(array('text' => 'CSV', 'url' => '*/*/exportInvoicesCsv'));
        $this->setExportTypeXml(array('text' => 'XML', 'url' => '*/*/exportInvoicesXml'));
    }

    public function getRowUrl($row)
    {
        $url = null;
        $accessHelper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        if ($accessHelper->customerHasAccess('Epicor_Customerconnect', 'Invoices', 'details', '', 'Access')) {
            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */
            $erpAccountNumber = $helper->getErpAccountNumber();
            $invoice = $helper->urlEncode($helper->encrypt($erpAccountNumber . ']:[' . $row->getId()));
            $params = array('invoice' => $invoice, 'attribute_type' => $row->get_attributesType());
            $url = Mage::getUrl('*/*/details', $params);
        }

        return $url;
    }

    protected function initColumns()
    {
        parent::initColumns();

        $columns = $this->getCustomColumns();

        if (Mage::helper('epicor_lists/frontend_contract')->contractsDisabled()) {
            unset($columns['contracts_contract_code']);
        }

        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        if ($helper->customerHasAccess('Epicor_Customerconnect', 'Invoices', 'reorder', '', 'Access')) {
            if (Mage::getStoreConfig('sales/reorder/allow')) {
                $columns['reorder'] = array(
                    'header' => Mage::helper('epicor_comm')->__('Reorder'),
                    'type' => 'text',
                    'filter' => false,
                    'sortable' => false,
                    'renderer' => new Epicor_Customerconnect_Block_Customer_Invoices_List_Renderer_Reorder(),
                );
            }
        }

        $this->setCustomColumns($columns);
    }

}
