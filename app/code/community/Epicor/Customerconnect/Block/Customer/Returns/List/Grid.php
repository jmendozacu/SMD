<?php

/**
 * Customer RETURN list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Customer Connect
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Returns_List_Grid extends Epicor_Common_Block_Generic_List_Search
{

    public function __construct()
    {
        parent::__construct();
        
        $this->setFooterPagerVisibility(true);
        $this->setId('customerconnect_returns');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setMessageBase('epicor_comm');
        $this->setMessageType('crrs');
        $this->setIdColumn('erp_returns_number');
        $this->initColumns();
        $this->setExportTypeCsv(array('text' => 'CSV', 'url' => '*/*/exportCsv'));
        $this->setExportTypeXml(array('text' => 'XML', 'url' => '*/*/exportXml'));
    }

    public function getRowUrl($row)
    {

        $url = null;
        $accessHelper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */
        if ($accessHelper->customerHasAccess('Epicor_Customerconnect', 'Returns', 'details', '', 'Access')) {
            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */
            $erpAccountNum = $helper->getErpAccountNumber();

            $quoteDetails = array(
                'erp_account' => $erpAccountNum,
                'erp_returns_number' => $row->getErpReturnsNumber()
            );

            $requested = $helper->urlEncode($helper->encrypt(serialize($quoteDetails)));
            $url = Mage::getUrl('*/*/details', array('return' => $requested));
        }

        return $url;
    }

}
