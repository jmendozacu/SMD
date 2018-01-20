<?php

/**
 * Upgrade - 1.0.0.21 to 1.0.0.22
 * 
 * Adding extra attribute to customer addresses for address type
 */
$installer = $this;
$installer->startSetup();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */


/* * **********************************************************************
  Step : Add Attributes to Customer Addresses
 * *********************************************************************** */

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->removeAttribute('customer_address', 'address_type');

$setup->addAttribute('customer_address', 'address_type', array(
    'type' => 'text',
    'input' => 'select',
    'label' => 'Address Type',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'default' => '0',
    'visible_on_front' => 1,
    'source' => 'eav/entity_attribute_source_table',
    'option' => array('values' => array('delivery', 'invoice', 'registered', 'other')),
));

Mage::getSingleton('eav/config')
        ->getAttribute('customer_address', 'address_type')
        ->setData('used_in_forms', array(
            'adminhtml_customer_address',
            'customer_address_edit',
            'customer_register_address'
        ))
        ->save();

$installer->endSetup();