<?php

class Epicor_Comm_Block_Adminhtml_Locations_Edit_Tab_Details extends Epicor_Common_Block_Adminhtml_Form_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->_title = 'Details';
    }

    protected function _prepareForm()
    {
        $location = Mage::registry('location');
        /* @var $location Epicor_Comm_Model_Location */

        $form = new Varien_Data_Form();
        $formData = Mage::getSingleton('adminhtml/session')->getFormData(true);

        if (empty($formData)) {
            $formData = $location->getData();
        }

        $fieldset = $form->addFieldset('details', array('legend' => Mage::helper('epicor_common')->__('Details')));
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */

        $fieldset->addField('code', 'text', array(
            'label' => Mage::helper('epicor_comm')->__('ERP Code'),
            'required' => true,
            'name' => 'code',
            'disabled' => $location->isObjectNew() ? false : true
        ));

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('epicor_comm')->__('Name'),
            'required' => true,
            'name' => 'name'
        ));

        $fieldset->addField('address1', 'text', array(
            'label' => Mage::helper('epicor_comm')->__('Address Line 1'),
            'name' => 'address1'
        ));
        $fieldset->addField('address2', 'text', array(
            'label' => Mage::helper('epicor_comm')->__('Address Line 2'),
            'name' => 'address2'
        ));
        $fieldset->addField('address3', 'text', array(
            'label' => Mage::helper('epicor_comm')->__('Address Line 3'),
            'name' => 'address3'
        ));

        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('epicor_comm')->__('City'),
            'name' => 'city'
        ));

        $county_id = new Varien_Data_Form_Element_Select(array(
            'label' => '',
            'name' => 'county_id',
            'no_display' => true,
        ));
        $county_id->setForm($form);
        $county_id->setId('county_id');

        $fieldset->addField('county_code', 'text', array(
            'label' => Mage::helper('epicor_comm')->__('State/Province'),
            'name' => 'county_code',
            'after_element_html' => $county_id->getElementHtml()
        ));

        $elementJs = '<script type="text/javascript">'
                . 'new RegionUpdater("country", "county_code", "county_id", '
                . $this->helper('directory')->getRegionJson()
                . ', undefined, "registered_postcode");'
                . '</script>';

        $fieldset->addField('country', 'select', array(
            'label' => Mage::helper('epicor_comm')->__('Country'),
            'name' => 'country',
            'values' => Mage::getModel('adminhtml/system_config_source_country')->toOptionArray(),
            'class' => 'countries',
            'after_element_html' => $elementJs
        ));

        $fieldset->addField('postcode', 'text', array(
            'label' => Mage::helper('epicor_comm')->__('Zip/Postal Code'),
            'name' => 'postcode'
        ));
        $fieldset->addField('email_address', 'text', array(
            'label' => Mage::helper('epicor_comm')->__('Email'),
            'name' => 'email_address'
        ));

        $fieldset->addField('telephone_number', 'text', array(
            'label' => Mage::helper('epicor_common')->__('Telephone Number'),
            'name' => 'telephone_number'
        ));
        
        $fieldset->addField('mobile_number', 'text', array(
            'label' => Mage::helper('epicor_common')->__('Mobile Number'),
            'name' => 'mobile_number'
        ));

        $fieldset->addField('fax_number', 'text', array(
            'label' => Mage::helper('epicor_common')->__('Fax Number'),
            'name' => 'fax_number'
        ));
        $fieldset->addField('sort_order', 'text', array(
            'label' => Mage::helper('epicor_common')->__('Sort Order'),
            'name' => 'sort_order'
        ));

        $form->setValues($formData);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
