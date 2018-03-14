<?php

/**
 * Customer Orders list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Customer Connect 
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Orders_List_Grid extends Epicor_Common_Block_Generic_List_Search
{

    public function __construct()
    {
        parent::__construct();
        
        $this->setFooterPagerVisibility(true);
        $this->setId('customerconnect_orders');
        $this->setDefaultSort('order_number');
        $this->setDefaultDir('desc');
        $this->setMessageBase('customerconnect');
        $this->setMessageType('cuos');
        $this->setIdColumn('order_number');
        $this->initColumns();
        $this->setExportTypeCsv(array('text' => 'CSV', 'url' => '*/*/exportOrdersCsv'));
        $this->setExportTypeXml(array('text' => 'XML', 'url' => '*/*/exportOrdersXml'));
    }

    public function getRowUrl($row)
    {

        $url = null;
        $accessHelper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        if ($accessHelper->customerHasAccess('Epicor_Customerconnect', 'Orders', 'details', '', 'Access')) {
            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */
            $erp_account_number = $helper->getErpAccountNumber();
            $order_requested = $helper->urlEncode($helper->encrypt($erp_account_number . ']:[' . $row->getId()));
            $url = Mage::getUrl('*/*/details', array('order' => $order_requested));
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

        if ($helper->customerHasAccess('Epicor_Customerconnect', 'Orders', 'reorder', '', 'Access')) {
            if (Mage::getStoreConfig('sales/reorder/allow')) {
                $columns['reorder'] = array(
                    'header' => Mage::helper('epicor_comm')->__('Reorder'),
                    'type' => 'text',
                    'filter' => false,
                    'sortable' => false,
                    'renderer' => new Epicor_Customerconnect_Block_Customer_Dashboard_Orders_Renderer_Reorder(),
                    'is_system' => true
                );
            }
        }

        $this->setCustomColumns($columns);
    }

}
