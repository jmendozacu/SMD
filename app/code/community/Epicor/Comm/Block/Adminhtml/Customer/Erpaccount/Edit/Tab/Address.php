<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Address extends Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_Edit_Tab_Abstract {

    public function __construct() {
        parent::_construct();
        $this->_title='Addresses';
        $this->setTemplate('epicor_comm/customer/erpaccount/edit/erpgroupaddress.phtml');
    }
    public function getOtherAddresses() {
        $deladdressCode = $this->getErpCustomer()->getDefaultDeliveryAddressCode();
        $delinvCode = $this->getErpCustomer()->getDefaultInvoiceAddressCode();

        $collection = Mage::getModel('epicor_comm/customer_erpaccount_address')->getCollection();
        $collection->addFilter('erp_customer_group_code', $this->getErpCustomer()->getErpCode());
        $collection->addFieldToFilter('erp_code', array('neq' => $deladdressCode));
        $collection->addFieldToFilter('erp_code', array('neq' => $delinvCode));
        $collection->load();

        return $collection->getItems();
    }

    public function renderAddress($erp_address_code, $erp_address = null) {


        if ($erp_address_code != null || $erp_address == null)
        {
             $collection = Mage::getModel('epicor_comm/customer_erpaccount_address')->getCollection();
            $collection->addFilter('erp_customer_group_code', $this->getErpCustomer()->getErpCode());
            $collection->addFilter('erp_code',$erp_address_code);
            $erp_address = $collection->getFirstItem();      
        }           

        $address = '';
        $address .= (!is_null($erp_address->getErpCode())) ? 'ERP Code: '.$erp_address->getErpCode() . '<br />' : '';
        $address .= ($erp_address->getName()) ? $erp_address->getName() . '<br />' : '';
        $address .= ($erp_address->getAddress1()) ? $erp_address->getAddress1() . '<br />' : '';
        $address .= ($erp_address->getAddress2()) ? $erp_address->getAddress2() . '<br />' : '';
        $address .= ($erp_address->getAddress3()) ? $erp_address->getAddress3() . '<br />' : '';
        $address .= ($erp_address->getCity()) ? $erp_address->getCity() . '<br />' : '';
        $address .= ($erp_address->getCounty()) ? $erp_address->getCounty() . '<br />' : '';
        $address .= ($erp_address->getCountry()) ? $erp_address->getCountry() . '<br />' : '';
        $address .= ($erp_address->getPostcode()) ? $erp_address->getPostcode() . '<br />' : '';
        $address .= ($erp_address->getPhone()) ? 'T : ' . $erp_address->getPhone() . '<br />' : '';
        $address .= ($erp_address->getMobileNumber()) ? 'M : ' . $erp_address->getMobileNumber() . '<br />' : '';
        $address .= ($erp_address->getFax()) ? 'F : ' . $erp_address->getFax() . '<br />' : '';
        $address .= ($erp_address->getEmail()) ? 'E : ' . $erp_address->getEmail() . '<br />' : '';
        $address .= ($erp_address->getInstructions());

        if ($address != '') {
            $html = '<address>';
            $html .= $address;
            $html .= '</address>';
        } else {
            $html = '<br />No Address Set';
        }
        return $html;
    }

}