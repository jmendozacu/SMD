<?php

class Epicor_Common_Block_Adminhtml_Access_Right_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form {


    public function getRight() {

        if (!$this->_accessright) {
            $this->_accessright = Mage::registry('access_right_data');
        }
        return $this->_accessright;
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('form_form', array('legend' => Mage::helper('adminhtml')->__('Item information')));

        $fieldset->addField('entity_name', 'text', array(
            'label' => Mage::helper('adminhtml')->__('Access Right'),
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


        $form->setValues($this->getRight());
        return parent::_prepareForm();
    }

}


