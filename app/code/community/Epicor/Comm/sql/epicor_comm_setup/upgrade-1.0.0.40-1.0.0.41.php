<?php

/**
 * Upgrade - 1.0.0.40 to 1.0.0.41
 * 
 * WSO-827 - Adding Brand information storing per erpaccount/address so we can update them when the store changes
 * WSO-849 - Adding type columns to erp addresses so addresses can have multiple types
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

/*
 * Phase 1 - add new columns
 */

// WSO-827 - Adding Brand information storing per erpaccount/address so we can update them when the store changes

$conn->addColumn($installer->getTable('epicor_comm/customer_erpaccount'), 'brand_refresh', array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'default' => false,
    'comment' => 'Flag to say if this erp account needs its brands refreshing'
));

$conn->addColumn($installer->getTable('epicor_comm/customer_erpaccount'), 'brands', array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'default' => '',
    'comment' => 'Brand information for the account'
));

$conn->addColumn($installer->getTable('epicor_comm/customer_erpaccount_address'), 'brands', array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'default' => '',
    'comment' => 'Brand information for the address'
));

// WSO-849 - Adding type columns to erp addresses & customer addresses so addresses can have multiple types

$conn->addColumn($installer->getTable('epicor_comm/customer_erpaccount_address'), 'is_registered', array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'default' => false,
    'comment' => 'Flag to say if this address is a registered type address'
));

$conn->addColumn($installer->getTable('epicor_comm/customer_erpaccount_address'), 'is_invoice', array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'default' => false,
    'comment' => 'Flag to say if this address is an invoice type address'
));

$conn->addColumn($installer->getTable('epicor_comm/customer_erpaccount_address'), 'is_delivery', array(
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'default' => false,
    'comment' => 'Flag to say if this address is a delivery type address'
));



$setup->addAttribute('customer_address', 'is_registered', array(
    'type' => 'int',
    'input' => 'boolean',
    'label' => 'Is Registered Address',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 0,
    'default' => false,
    'visible_on_front' => 1,
));

$setup->addAttribute('customer_address', 'is_delivery', array(
    'type' => 'int',
    'input' => 'boolean',
    'label' => 'Is Delivery Address',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 0,
    'default' => false,
    'visible_on_front' => 1,
));

$setup->addAttribute('customer_address', 'is_invoice', array(
    'type' => 'int',
    'input' => 'boolean',
    'label' => 'Is Billing Address',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 0,
    'default' => false,
    'visible_on_front' => 1,
));

Mage::getSingleton('eav/config')
        ->getAttribute('customer_address', 'is_registered')
        ->setData('used_in_forms', array(
            'adminhtml_customer_address',
            'customer_address_edit',
            'customer_register_address'
        ))
        ->save();

Mage::getSingleton('eav/config')
        ->getAttribute('customer_address', 'is_delivery')
        ->setData('used_in_forms', array(
            'adminhtml_customer_address',
            'customer_address_edit',
            'customer_register_address'
        ))
        ->save();

Mage::getSingleton('eav/config')
        ->getAttribute('customer_address', 'is_invoice')
        ->setData('used_in_forms', array(
            'adminhtml_customer_address',
            'customer_address_edit',
            'customer_register_address'
        ))
        ->save();

/*
 * Phase 2 - migrate data from old columns to new
 */

$table = $installer->getTable('epicor_comm/customer_erpaccount_address');

$installer->run('UPDATE '.$table.' SET is_registered = "1" WHERE type = "registered"');
$installer->run('UPDATE '.$table.' SET is_invoice = "1" WHERE  type = "invoice"');
$installer->run('UPDATE '.$table.' SET is_delivery = "1" WHERE  type = "delivery" OR type = "shipping"');

$collection = Mage::getResourceModel('customer/address_collection');
$collection->addAttributeToSelect('*');
/* @var $collection Mage_Customer_Model_Entity_Address_Collection */

$attribute = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'erp_address_type');
/* @var $attribute Mage_Customer_Model_Attribute */

foreach($collection->getItems() as $address) {
    $address->setData('is_registered',false);
    $address->setData('is_delivery',false);
    $address->setData('is_invoice',false);
    
    if($address->getData('erp_address_type')) {
        $optionId = $attribute->getSource()->getOptionText($address->getData('erp_address_type'));
        $address->setData('is_'.$optionId,true);
    }
    
    $address->save();
}

/*
 * Phase 3 - remove old columns
 */

$conn->dropColumn($installer->getTable('epicor_comm/customer_erpaccount_address'), 'type');
$setup->removeAttribute('customer_address', 'erp_address_type');

$installer->endSetup();
