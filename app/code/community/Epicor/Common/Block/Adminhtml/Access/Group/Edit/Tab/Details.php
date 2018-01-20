<?php

/**
 * 
 * Access group detail edit tab
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Adminhtml_Access_Group_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form {

    public function getGroup() {

        if (!$this->_accessright) {
            $this->_accessright = Mage::registry('access_group_data');
        }
        return $this->_accessright;
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('form_form', array('legend' => Mage::helper('adminhtml')->__('Item information')));

        $fieldset->addType('account_selector', 'Epicor_Comm_Block_Adminhtml_Form_Element_Erpaccount');
        
        $fieldset->addField('entity_name', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Access Group'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'entity_name',
        ));

        $fieldset->addField('type', 'select', array(
            'label' => Mage::helper('adminhtml')->__('Type'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'type',
            'values' => array('B2B' => 'B2B', 'B2C' => 'B2C', 'supplier' => 'Supplier')
        ));
        
        $fieldset->addField('erp_account_id', 'account_selector', array(
            'label' => Mage::helper('adminhtml')->__('ERP Account'),
            'name' => 'erp_account_id'
        ));

        $form->setValues($this->getGroup());
        return parent::_prepareForm();
    }

}
