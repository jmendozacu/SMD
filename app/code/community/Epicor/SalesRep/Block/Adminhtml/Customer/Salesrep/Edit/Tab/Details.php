<?php

class Epicor_SalesRep_Block_Adminhtml_Customer_Salesrep_Edit_Tab_Details extends Epicor_Common_Block_Adminhtml_Form_Abstract implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    public function __construct()
    {
        parent::__construct();
        $this->_title = 'Details';
    }

    public function canShowTab()
    {
        return true;
    }

    public function getTabLabel()
    {
        return 'Details';
    }

    public function getTabTitle()
    {
        return 'Details';
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $salesRep = Mage::registry('salesrep_account');
        /* @var $salesRep Epicor_SalesRep_Model_Account */

        $form = new Varien_Data_Form();
        $formData = Mage::getSingleton('adminhtml/session')->getFormData(true);

        if (empty($formData)) {
            $formData = $salesRep->getData();
        }

        $fieldset = $form->addFieldset('details', array('legend' => Mage::helper('epicor_salesrep')->__('Sales Rep Account Details')));
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */

        $fieldset->addField('sales_rep_id', 'text', array(
            'label' => Mage::helper('epicor_salesrep')->__('Sales Rep Account Number'),
            'required' => true,
            'name' => 'sales_rep_id',
            'disabled' => $salesRep->isObjectNew() ? false : true
        ));

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('epicor_salesrep')->__('Name'),
            'required' => true,
            'name' => 'sales_rep_name'
        ));

        $fieldset->addField('catalog_access', 'select', array(
            'label' => Mage::helper('epicor_salesrep')->__('Sales Reps Can Access Catalog'),
            'required' => false,
            'name' => 'catalog_access',
            'values' => array(
                array(
                    'label' => $this->__('Global Default'),
                    'value' => null
                ),
                array(
                    'label' => $this->__('Yes'),
                    'value' => 'Y'
                ),
                array(
                    'label' => $this->__('No'),
                    'value' => 'N'
                )
            )
        ));

        $form->setValues($formData);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
