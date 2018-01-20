<?php

/**
 * Parts list grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Supplier Connect
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Parts_List_Grid extends Epicor_Common_Block_Generic_List_Search {

    public function __construct() {
        parent::__construct();

        $this->setId('supplierconnect_parts');
        $this->setDefaultSort('product_code');
        $this->setDefaultDir('desc');
        $this->setMessageBase('supplierconnect');
        $this->setMessageType('spls');
        $this->setIdColumn('product_code');
        $this->initColumns();
    }

    public function getRowUrl($row) {

        $url = null;
        $accessHelper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        if ($accessHelper->customerHasAccess('Epicor_Supplierconnect', 'Parts', 'details', '', 'Access')) {
            $helper = Mage::helper('supplierconnect');
            /* @var $helper Epicor_Supplierconnect_Helper_Data */
            $erp_account_number = $helper->getSupplierAccountNumber();

            $partDetails = array(
                'erp_account' => $erp_account_number,
                'product_code' => $row->getId(),
                'operational_code' => $row->getOperationalCode(),
                'effective_date' => $row->getEffectiveDate(),
                'unit_of_measure_code' => $row->getUnitOfMeasureCode()
            );

            $requested = $helper->urlEncode($helper->encrypt(serialize($partDetails)));
            $url = Mage::getUrl('*/*/details', array('part' => $requested));
        }

        return $url;
    }

}