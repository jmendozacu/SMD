<?php

class Epicor_Common_Block_Customer_Erpaccount_Address extends Epicor_Common_Block_Adminhtml_Form_Abstract {

    public function getAddressHtml($type,$data) {
        if(!$this->getForm()){
            $this->setForm(new Varien_Data_Form());
        }
        
        return $this->_addFormAddress($type,$data,true);
    }

    protected function _addFormAddress($type, $data,$toHtml=false, $addSameAs = array(), $showPhoneFax = true) {
        
        $form = $this->getForm();
        //$form->setHtmlIdPrefix($type . '_');

        $fieldset = $form->addFieldset($type . '_address', array('legend' => Mage::helper('epicor_common')->__(ucwords($type) . ' Address')));
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        
        if(!empty($addSameAs)) {
            foreach($addSameAs as $sameType) {
                $fieldset->addField($sameType . '_' . $type, 'checkbox', array(
                    'label' => Mage::helper('epicor_common')->__('Same as ' . ucfirst($sameType)),
                    'required' => false,
                    'name' => 'same_as',
                    'class' => 'same_as'
                ));
            }
        }
        
        $fieldset->addField($type . '_name', 'text', array(
            'label' => Mage::helper('epicor_common')->__('Name'),
            'required' => true,
            'name' => $type . '[name]'
        ));

        $fieldset->addField($type . '_address1', 'text', array(
            'label' => Mage::helper('epicor_common')->__('Address Line 1'),
            'required' => true,
            'name' => $type . '[address1]'
        ));
        for ($_i = 2, $_n = Mage::helper('customer/address')->getStreetLines(); $_i <= $_n; $_i++){ 
            $fieldset->addField($type . "_address{$_i}", 'text', array(
                'label' => Mage::helper('epicor_common')->__("Address Line {$_i}"),
                'name' => $type . "[address{$_i}]"
            ));
        }    
//        $fieldset->addField($type . '_address3', 'text', array(
//            'label' => Mage::helper('epicor_common')->__('Address Line 3'),
//            'name' => $type . '[address3]'
//        ));

        $fieldset->addField($type . '_city', 'text', array(
            'label' => Mage::helper('epicor_common')->__('City'),
            'required' => true,
            'name' => $type . '[city]'
        ));

        $county_id = new Varien_Data_Form_Element_Select(array(
            'label' => '',
            'required' => true,
            'name' => $type . '[county_id]',
            'no_display' => true,
        ));
        $county_id->setForm($form);
        $county_id->setId($type . '_county_id');
        
        $fieldset->addField($type . '_county', 'text', array(
            'label' => Mage::helper('epicor_common')->__('County'),
            'required' => true,
            'name' => $type . '[county]',
            'after_element_html' =>  $county_id->getElementHtml()
        ));

        $fieldset->addField($type . '_country', 'select', array(
            'label' => Mage::helper('epicor_common')->__('Country'),
            'name' => $type . '[country]',
            'required' => true,
            'values' => Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
            'class' => 'countries'
        ));

        $fieldset->addField($type . '_postcode', 'text', array(
            'label' => Mage::helper('epicor_common')->__('Postcode'),
            'required' => true,
            'name' => $type . '[postcode]'
        ));      
        $fieldset->addField($type . '_email', 'text', array(
            'label' => Mage::helper('epicor_common')->__('Email'),
            'name' => $type . '[email]',
            'required' => false
        ));

        if($showPhoneFax){
            $phoneRequired = Mage::getStoreConfig('checkout/options/telephone_required') ? true : false;
            $mobileRequired = (Mage::getStoreConfigFlag('customer/address/display_mobile_phone') && Mage::getStoreConfigFlag('checkout/options/mobile_number_required')) ? true : false;
            $fieldset->addField($type . '_phone', 'text', array(
                'label' => Mage::helper('epicor_common')->__('Telephone Number'),
                'name' => $type . '[phone]',
                'required' => $phoneRequired
            ));
            $fieldset->addField($type . '_mobile_number', 'text', array(
                'label' => Mage::helper('epicor_common')->__('Mobile Phone Number'),
                'name' => $type . '[mobile_number]',
                'required' => $mobileRequired
            ));

            $field = $fieldset->addField($type . '_fax_number', 'text', array(
                'label' => Mage::helper('epicor_common')->__('Fax Number'),
                'name' => $type . '[fax_number]'
            ));
        }

        if ((strpos($type, 'delivery') !== false)) {
            $fieldset->addField($type . '_instructions', 'text', array(
                'label' => Mage::helper('epicor_common')->__('Instructions'),
                'name' => $type . '[instructions]',
            ));
        }
        
        $form->setData($data);
        
        if($toHtml) {
            return $fieldset->toHtml();
        }
    }

}