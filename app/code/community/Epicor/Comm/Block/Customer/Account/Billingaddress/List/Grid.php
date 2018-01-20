<?php

/**
 * Customer Orders list Grid config
 */
class Epicor_Comm_Block_Customer_Account_Billingaddress_List_Grid extends Epicor_Common_Block_Generic_List_Grid
{

    public function __construct()
    {
        parent::__construct();

//        $his->setRowClickCallback('editBillingAddress');

        $this->setId('customer_account_billingaddress_list');
        $this->setIdColumn('id');
        $this->setDefaultSort('name');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(false);
        $this->setMessageBase('epicor_comm');
        $this->setCustomColumns($this->_getColumns());
        $this->setExportTypeCsv(false);
        $this->setExportTypeXml(false);
        $this->setRowClickCallback('populateAddressSelect');
        $this->setCacheDisabled(true);
//        /* @var $order Epicor_Common_Model_Xmlvarien */
//        
        $addresses = array();
        $loadAddresses = true;
        $restrict = Mage::getStoreConfigFlag('Epicor_Comm/address/force_type');
        
        $transportObject = new Varien_Object;
        $transportObject->setAddresses($addresses);
        $transportObject->setLoadAddresses($loadAddresses);
        $transportObject->setRestrictByType($restrict);
        
        $cart = Mage::getSingleton('checkout/cart');
        /* @var $cart Mage_Checkout_Model_Cart */

        Mage::dispatchEvent('epicor_comm_onepage_get_checkout_addresses', array('quote' => $cart->getQuote(), 'type' => 'invoice', 'addresses' => $transportObject));
        $addresses = $transportObject->getAddresses();
        $loadAddresses = $transportObject->getLoadAddresses();
        
        if($loadAddresses) {
            $customer = Mage::getModel('customer/session')->getCustomer();
            $addresses = ($restrict) ? $customer->getAddressesByType('invoice') : $customer->getAddresses();
        }

        $this->setCustomData($addresses);
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
                'renderer' => new Epicor_Comm_Block_Customer_Address_Renderer_Address(),
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'company' => array(
                'header' => Mage::helper('epicor_comm')->__('Company'),
                'align' => 'left',
                'index' => 'company',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'street' => array(
                'header' => Mage::helper('epicor_comm')->__('Street'),
                'align' => 'left',
                'index' => 'street',
                'renderer' => new Epicor_Comm_Block_Customer_Address_Renderer_Street(),
                'type' => 'text',
                'condition' => 'LIKE',
                'filter'=>false
            ),
            'city' => array(
                'header' => Mage::helper('epicor_comm')->__('City'),
                'align' => 'left',
                'index' => 'city',
                'type' => 'text',
                'condition' => 'LIKE'
            ),
            'region' => array(
                'header' => Mage::helper('epicor_comm')->__('Region'),
                'align' => 'left',
                'index' => 'region',
                'condition' => 'LIKE'
            ),
            'postcode' => array(
                'header' => Mage::helper('epicor_comm')->__('Postcode'),
                'align' => 'left',
                'index' => 'postcode',
                'type' => 'postcode',
                'condition' => 'LIKE'
            ),
            'country_id' => array(
                'header' => Mage::helper('epicor_comm')->__('Country'),
                'align' => 'left',
                'index' => 'country_id',
                'type' => 'country',
                'condition' => 'LIKE'
            ),
        );

        return $columns;
    }

    public function getRowUrl($row)
    {
        return '#';
    }

    public function getGridUrl()
    {
//        return $this->getUrl('checkout/onepage/index', array( 'grid' => true, 'active_tab' => 'billing', 'billingSearch'=>true));
//        return $this->getUrl('checkout/onepage/billingPopupGrid');
        return $this->getUrl('epicor_comm/onepage/billingPopup');
    }

}
