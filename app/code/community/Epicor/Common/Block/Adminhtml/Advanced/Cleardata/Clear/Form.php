<?php

/**
 * Epicor_Common_Block_Adminhtml_Advanced_Cleardata
 * 
 * Form for Clear Data
 * 
 * @category   Epicor
 * @package    Epicor_Common
 * @author     Epicor Websales Team
 */
class Epicor_Common_Block_Adminhtml_Advanced_Cleardata_Clear_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'clear_form',
            'action' => $this->getUrl('*/*/clear'),
            'method' => 'post'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('layout_block_form', array('legend' => Mage::helper('epicor_common')->__('Clear Data')));

        $fieldset->addField('products', 'checkbox', array(
            'label' => Mage::helper('epicor_common')->__('Products'),
            'name' => 'products',
            'value' => '1',
            'tabindex' => 1
        ));

        $fieldset->addField('categories', 'checkbox', array(
            'label' => Mage::helper('epicor_common')->__('Categories'),
            'name' => 'categories',
            'value' => '1',
            'tabindex' => 2
        ));

        $fieldset->addField('erpaccounts', 'checkbox', array(
            'label' => Mage::helper('epicor_common')->__('ERP Accounts'),
            'name' => 'erpaccounts',
            'value' => '1',
            'tabindex' => 3
        ));

        $fieldset->addField('customers', 'checkbox', array(
            'label' => Mage::helper('epicor_common')->__('Customers'),
            'name' => 'customers',
            'checked' => false,
            'value' => '1',
            'tabindex' => 4
        ));

        $fieldset->addField('quotes', 'checkbox', array(
            'label' => Mage::helper('epicor_common')->__('Quotes'),
            'name' => 'quotes',
            'checked' => false,
            'value' => '1',
            'tabindex' => 5
        ));

        $fieldset->addField('orders', 'checkbox', array(
            'label' => Mage::helper('epicor_common')->__('Orders'),
            'name' => 'orders',
            'checked' => false,
            'value' => '1',
            'tabindex' => 6
        ));
        
        $fieldset->addField('returns', 'checkbox', array(
            'label' => Mage::helper('epicor_common')->__('Returns'),
            'name' => 'returns',
            'checked' => false,
            'value' => '1',
            'tabindex' => 7
        ));
        
        $fieldset->addField('locations', 'checkbox', array(
            'label' => Mage::helper('epicor_common')->__('Locations'),
            'name' => 'locations',
            'checked' => false,
            'value' => '1',
            'tabindex' => 8
        ));
        
        $fieldset->addField('lists', 'checkbox', array(
            'label' => Mage::helper('epicor_common')->__('Lists'),
            'name' => 'lists',
            'checked' => false,
            'value' => '1',
            'tabindex' => 8
        ));

        return parent::_prepareForm();
    }

}
