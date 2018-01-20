<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Form
 *
 * @author Paul.Ketelle
 */
class Epicor_Comm_Block_Adminhtml_Customer_Edit_Tab_Locations_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $customer = Mage::registry('current_customer');
        /* @var $customer Epicor_Comm_Model_Customer */

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('locations_form', array('legend' => Mage::helper('epicor_comm')->__('Location')));

        $linkType = $customer->getEccLocationLinkType();

        $fieldset->addField('id', 'hidden', array(
            'name' => 'id',
        ));

        $fieldset->addField('locations_source', 'select', array(
            'label' => Mage::helper('epicor_comm')->__('Location Restrictions Source'),
            'required' => false,
            'name' => 'locations_source',
            'values' => $this->_getOptions(),
        ));
        
        $data = array(
            'locations_source' => (is_null($linkType)) ? 'erp' : 'customer'
        );
        
        $form->setValues($data);
        

        $this->setForm($form);
    }

    /**
     * Gets an array of options for the dropdown
     * 
     * @return array
     */
    private function _getOptions()
    {
        $options = array();

        $options[] = array(
            'label' => $this->__('Use ERP Account Specific Locations'),
            'value' => 'erp'
        );

        $options[] = array(
            'label' => $this->__('Use Customer Specific Locations'),
            'value' => 'customer'
        );


        return $options;
    }

}
