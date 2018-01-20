<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Comm_Block_Customer_Account_Masqueradesearch_List_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

        $this->setId('customer_account_masqueradesearch_list');
        $this->setIdColumn('id');
        $this->setDefaultSort('name');       
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(false);
        $this->setMessageBase('epicor_comm');
        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        $this->setRowClickCallback('populateMasqueradeSelect');
        $this->setCacheDisabled(true);
        $erpAccountId = Mage::getSingleton('customer/session')->getCustomer()->getErpaccountId();
        $erpaccount = Mage::helper('epicor_comm')->getErpAccountInfo($erpAccountId);
//        /* @var $order Epicor_Common_Model_Xmlvarien */
//        
        $children = $erpaccount->getChildAccounts();
        $erpaccount = array();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $account = Mage::getModel('epicor_salesrep/account')->load($customer->getSalesRepAccountId());
        foreach ($account->getStoreMasqueradeAccounts() as $account) {
            if ($this->isColumnEnabled('invoice_address')) {
                $addressTxt = $this->_getAddressText($account, 'default_invoice_address_code');
                $account->setInvoiceAddress($addressTxt);
            }
            if ($this->isColumnEnabled('default_shipping_address')) {
                $addressTxt = $this->_getAddressText($account, 'default_delivery_address_code');
                $account->setDefaultShippingAddress($addressTxt);
            }
            $erpaccount[$account->getName() . $account->getId()] = $account;    // save account using name as a key
        }
        $this->setCustomData($erpaccount);
    }

    protected function _toHtml()
    {
        $html = parent::_toHtml(false);
        return $html;
    }

    protected function _getColumns()
    {
        $columns = array(
            'entity_id' => array(
                'header' => Mage::helper('epicor_comm')->__('id'),
                'align' => 'left',
                'index' => 'entity_id',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'name' => array(
                'header' => Mage::helper('epicor_comm')->__('Name'),
                'align' => 'left',
                'index' => 'name',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
        );

        if ($this->isColumnEnabled('account_number')) {
            $columns['account_number'] = array(
                'header' => Mage::helper('epicor_comm')->__('Account Number'),
                'align' => 'left',
                'index' => 'account_number',
                'condition' => 'LIKE'
            );
        }
        
        if ($this->isColumnEnabled('short_code')) {
            $columns['short_code'] = array(
                'header' => Mage::helper('epicor_comm')->__('Short Code'),
                'align' => 'left',
                'index' => 'short_code',
                'type' => 'text',
                'condition' => 'LIKE'
            );
        }
        
        if ($this->isColumnEnabled('invoice_address')) {
            $columns['invoice_address'] = array(
                'header' => Mage::helper('epicor_comm')->__('Invoice Address'),
                'align' => 'left',
                'index' => 'invoice_address',
                'type' => 'text',
                'condition' => 'LIKE'
            );
        }
        
        if ($this->isColumnEnabled('default_shipping_address')) {
            $columns['default_shipping_address'] = array(
                'header' => Mage::helper('epicor_comm')->__('Default shipping Address'),
                'align' => 'left',
                'index' => 'default_shipping_address',
                'type' => 'text',
                'condition' => 'LIKE'
            );
        }
        return $columns;
    }
    
    private function _getAddressText($account, $addressCodeField)
    {
        $text = '';
        
        $addressCollection = Mage::getModel('epicor_comm/customer_erpaccount_address')->getCollection();
        /* @var $addressCollection Epicor_Comm_Model_Mysql4_Customer_Erpaccount_Address_Collection */
        $addressCollection->addFieldToFilter('erp_code', $account->getData($addressCodeField));
        $addressCollection->addFieldToFilter('erp_customer_group_code', $account->getErpCode());
        $address = $addressCollection->getFirstItem();
        /* @var $address Epicor_Comm_Model_Customer_Erpaccount_Address */
        
        if ($address) {
            $addressFields = array('name', 'address1', 'address2', 'address3', 'city', 'county', 'country', 'postcode');
            $glue = '';
            foreach ($addressFields as $field) {
                $fieldData = trim($address->getData($field));
                if ($fieldData && !empty($fieldData)) {
                    $text .= $glue . $fieldData;
                    $glue = ', ';
                }
            }
        }

        return $text;
    }
    
    public function isColumnEnabled($column)
    {
        return Mage::getStoreConfig('epicor_salesrep/masquerade_search/' . $column);
    }

    public function getRowUrl($row)
    {
        return $row->getEntityId();
    }

}
