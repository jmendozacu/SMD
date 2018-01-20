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
class Epicor_Comm_Block_Adminhtml_Catalog_Product_Edit_Tab_Locations_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $product = Mage::registry('product');
        /* @var $product Epicor_Comm_Model_Product */

        $helper = Mage::helper('epicor_comm/locations');
        /* @var $helper Epicor_Comm_Helper_Locations */
        
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('locations_form', array('legend'=>$helper->__('Location')));

        $fieldset->addType('manufacturerInput', 'Epicor_Comm_Block_Adminhtml_Form_Element_Manufacturers');
        
        $fieldset->addField('location_post_url', 'hidden', array(
            'name' => 'locationPostUrl',
            'value' => $this->getUrl('adminhtml/epicorcomm_catalog_product/locationpost', array('product_id' => $product->getId()))
        ));
        $fieldset->addField('delete_message', 'hidden', array(
            'name' => 'deleteMessage',
            'value' => $helper->__('Are you sure you want to delete this location data ?')
        ));
        $fieldset->addField('id', 'hidden', array(
            'name' => 'id',
        ));
        $fieldset->addField('available_locations', 'hidden', array(
            'value' => serialize(array_keys($helper->getLocationDiff($product->getLocations()))),
            'name' => 'available_locations'
        ));
        
        $fieldset->addField('location_code', 'select', array(
            'label' => $helper->__('Location Code'),
            'required' => false,
            'name' => 'location_code',
            'values' => Mage::getModel('epicor_comm/location')->getCollection()->toOptionArray()#new Epicor_Comm_Model_Mysql4_Location_Collection()
        ));
        $fieldset->addField('stock_status', 'text', array(
            'label' => $helper->__('Stock Status'),
            'required' => false,
            'name' => 'stock_status',
        ));
        $fieldset->addField('free_stock', 'text', array(
            'label' => $helper->__('Free Stock'),
            'required' => false,
            'name' => 'free_stock',
        ));
        $fieldset->addField('minimum_order_qty', 'text', array(
            'label' => $helper->__('Minimum Order Qty'),
            'required' => false,
            'name' => 'minimum_order_qty',
        ));
        $fieldset->addField('maximum_order_qty', 'text', array(
            'label' => $helper->__('Maximum Order Qty'),
            'required' => false,
            'name' => 'maximum_order_qty',
        ));
        $fieldset->addField('lead_time_days', 'text', array(
            'label' => $helper->__('Lead Time Days'),
            'required' => false,
            'name' => 'lead_time_days',
        ));
        $fieldset->addField('lead_time_text', 'text', array(
            'label' => $helper->__('Lead Time Text'),
            'required' => false,
            'name' => 'lead_time_text',
        ));
        $fieldset->addField('supplier_brand', 'text', array(
            'label' => $helper->__('Supplier Brand'),
            'required' => false,
            'name' => 'supplier_brand',
        ));
        $fieldset->addField('tax_code', 'select', array(
            'label' => $helper->__('Tax Code'),
            'required' => false,
            'name' => 'tax_code',
            'values' => Mage::getModel('epicor_comm/config_source_producttax')->toOptionArray(true),
        ));
        $fieldset->addField('currency_code_display', 'text', array(
            'label' => $helper->__('Currency Code'),
            'required' => false,
            'name' => 'currency_code',
            'readonly' => true,
            'class' => 'disabled',
            'value' => $product->getStore()->getBaseCurrencyCode()
        ));
        $fieldset->addField('base_price', 'text', array(
            'label' => $helper->__('Base Price'),
            'required' => false,
            'name' => 'base_price',
        ));
        $fieldset->addField('cost_price', 'text', array(
            'label' => $helper->__('Cost Price'),
            'required' => false,
            'name' => 'cost_price',
        ));
        $fieldset->addField('manufacturers', 'manufacturerInput', array(
            'label' => $helper->__('Manufacturers'),
            'required' => false,
            'name' => 'manufacturers'
        ));
        $fieldset->addField('note', 'note', array(
            'text' => $helper->__('<span class="full-width-note">Leave fields blank to use product values instead of location values</span>'),
        ));
        $fieldset->addField('updateLocationSubmit', 'submit', array(
          'value'  => $helper->__('Update'),
          'onclick' => "return productLocations.rowUpdate();",
          'name' => 'updateLocationSubmit',
            'class' => 'form-button',
        ));
        $fieldset->addField('addLocationSubmit', 'submit', array(
          'value'  => $helper->__('Add'),
          'onclick' => "return productLocations.rowUpdate();",
          'name' => 'addLocationSubmit',
            'class' => 'form-button',
        ));
        $this->setForm($form);
    }
}
