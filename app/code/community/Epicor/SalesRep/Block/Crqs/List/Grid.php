<?php

/**
 * Customer RFQ list Grid config
 * 
 * Note: columns for this grid are configured in the Magento Admin: Configuration > Customer Connect
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_SalesRep_Block_Crqs_List_Grid extends Epicor_Common_Block_Generic_List_Search
{

    private $_allowEdit;

    public function __construct()
    {
        parent::__construct();

        $this->setId('customerconnect_rfqs');
        $this->setDefaultSort('quote_number');
        $this->setDefaultDir('desc');
        $this->setMessageBase('customerconnect');
        $this->setMessageType('crqs');
        $this->setIdColumn('quote_number');
        $this->initColumns();
        $this->setExportTypeCsv(array('text' => 'CSV', 'url' => '*/*/exportCsv'));
        $this->setExportTypeXml(array('text' => 'XML', 'url' => '*/*/exportXml'));

        $customerSession = Mage::getSingleton('customer/session');
        /* @var $customerSession Mage_Customer_Model_Session */

        $customer = $customerSession->getCustomer();
        /* @var $customer Epicor_Comm_Model_Customer */

        $salesRepAccount = $customer->getSalesRepAccount();
        /* @var $salesRepAccount Epicor_SalesRep_Model_Account */

        $erpAccounts = $salesRepAccount->getStoreMasqueradeAccounts();

        $erpAccountsCodes = array();
        foreach ($erpAccounts as $erpAccount) {
            /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
            $erpAccountsCodes[] = $erpAccount->getAccountNumber();
        }

        $_POST['selectederpaccts'] = Mage::helper('epicor_comm')->urlEncode(join(',', $erpAccountsCodes));
    }

    public function getRowUrl($row)
    {

        $url = null;
        $accessHelper = Mage::helper('epicor_common/access');
        /* @var $helper Epicor_Common_Helper_Access */

        $msgHelper = Mage::helper('epicor_comm/messaging');
        /* @var $msgHelper Epicor_Comm_Helper_Messaging */
        $enabled = $msgHelper->isMessageEnabled('customerconnect', 'crqd');

        if ($enabled && $accessHelper->customerHasAccess('Epicor_SalesRep', 'Crqs', 'details', '', 'Access')) {

            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */

            $quoteDetails = array(
                'erp_account' => $row->getAccountNumber(),
                'quote_number' => $row->getQuoteNumber(),
                'quote_sequence' => $row->getQuoteSequence()
            );

            $requested = $helper->urlEncode($helper->encrypt(serialize($quoteDetails)));
            $url = Mage::getUrl('*/*/details', array('quote' => $requested));
        }

        return $url;
    }

    protected function initColumns()
    {
        parent::initColumns();

        $columns = $this->getCustomColumns();

        $newColumns = array(
            'account_number' => array(
                'header' => Mage::helper('customerconnect')->__('Account Number'),
                'align' => 'left',
                'index' => 'account_number',
                'renderer'  => 'Epicor_SalesRep_Block_Crqs_List_Renderer_AccountShortcode',
                'type' => 'text',
                'filter' => false
            )
        );

        $columns = array_merge_recursive($newColumns, $columns);
        $this->setCustomColumns($columns);
    }

}
