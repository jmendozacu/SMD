<?php

class Epicor_Comm_Block_Adminhtml_Customer_Erpaccount_New_Tab_Address extends Epicor_Common_Block_Customer_Erpaccount_Address
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('epicor_comm/customer/erpaccount/new/addresses.phtml');
    }
    
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $formData = Mage::getSingleton('adminhtml/session')->getFormData(true);
        $data = array();

        if (isset($formData['registered'])) {
            foreach ($formData['registered'] as $key => $value) {
                $data['registered_'.$key] = $value;
            }
        } else {
            $data['registered_country'] = Mage::helper('core')->getDefaultCountry();
        }
        
        if (isset($formData['invoice'])) {
            foreach ($formData['invoice'] as $key => $value) {
                $data['invoice_'.$key] = $value;
            }
        } else {
            $data['invoice_country'] = Mage::helper('core')->getDefaultCountry();
        }
        
        if (isset($formData['delivery'])) {
            foreach ($formData['delivery'] as $key => $value) {
                $data['delivery_'.$key] = $value;
            }
        } else {
            $data['delivery_country'] = Mage::helper('core')->getDefaultCountry();
        }

        $showRegisteredAddress = Mage::getStoreConfigFlag('epicor_b2b/registration/registered_address');
        $showInvoiceAddress = Mage::getStoreConfigFlag('epicor_b2b/registration/invoice_address');
        $showDeliveryAddress = Mage::getStoreConfigFlag('epicor_b2b/registration/delivery_address');

        $form->setValues($data);
        $this->setForm($form);
        $arrayDependencies = array();
        if ($showRegisteredAddress) {
            $this->_addFormAddress('registered', array(), false, $arrayDependencies, Mage::getStoreConfigFlag('epicor_b2b/registration/registered_address_phone_fax'));
            $arrayDependencies[] = 'registered';
        }
        if ($showInvoiceAddress) {
            $this->_addFormAddress('invoice', array(), false, $arrayDependencies, Mage::getStoreConfigFlag('epicor_b2b/registration/invoice_address_phone_fax'));
            $arrayDependencies[] = 'invoice';
        }
        if ($showDeliveryAddress) {
            $this->_addFormAddress('delivery', array(), false, $arrayDependencies, Mage::getStoreConfigFlag('epicor_b2b/registration/delivery_address_phone_fax'));
        }
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
