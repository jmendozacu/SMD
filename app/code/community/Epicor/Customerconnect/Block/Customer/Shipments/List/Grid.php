<?php

/**
 * Customer Shipments list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Customer Connect
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Shipments_List_Grid extends Epicor_Common_Block_Generic_List_Search
{

    public function __construct()
    {
        parent::__construct();
        
        $this->setFooterPagerVisibility(true);
        $this->setId('customerconnect_shipments');
        $this->setDefaultSort('shipment_date');
        $this->setDefaultDir('desc');
        $this->setMessageBase('customerconnect');
        $this->setMessageType('cuss');
        $this->setIdColumn('packing_slip');
        $this->initColumns();
        $this->setExportTypeCsv(array('text' => 'CSV', 'url' => '*/*/exportShipmentsCsv'));
        $this->setExportTypeXml(array('text' => 'XML', 'url' => '*/*/exportShipmentsXml'));
    }

    public function getRowUrl($row)
    {

        $url = null;
        $accessHelper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        if ($accessHelper->customerHasAccess('Epicor_Customerconnect', 'Shipments', 'details', '', 'Access')) {
            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */
            $erpAccountNumber = $helper->getErpAccountNumber();
            $shipDetails = $helper->encrypt($erpAccountNumber . ']:[' . $row->getId() . ']:[' . $row->getOrderNumber());
            $shipment = $helper->urlEncode($shipDetails);
            $url = Mage::getUrl('*/*/details', array('shipment' => $shipment));
        }

        return $url;
    }

    protected function initColumns()
    {
        parent::initColumns();

        $columns = $this->getCustomColumns();

        $helper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        if ($helper->customerHasAccess('Epicor_Customerconnect', 'Shipments', 'reorder', '', 'Access')) {
            if (Mage::getStoreConfig('sales/reorder/allow')) {
                $columns['reorder'] = array(
                    'header' => Mage::helper('epicor_comm')->__('Reorder'),
                    'type' => 'text',
                    'filter' => false,
                    'sortable' => false,
                    'renderer' => new Epicor_Customerconnect_Block_Customer_Shipments_List_Renderer_Reorder(),
                );
            }
        }

        $this->setCustomColumns($columns);
    }

}
