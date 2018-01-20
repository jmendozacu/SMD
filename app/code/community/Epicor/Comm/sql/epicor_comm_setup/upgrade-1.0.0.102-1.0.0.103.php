<?php

/**
 * Upgrade - 1.0.0.102 to 1.0.0.103
 * 
 * Adding customer add custom address option 
 */
/* * **********************************************************************
  Step : Add Attributes to Customers
 * *********************************************************************** */


$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();


// add column to erp account table
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
//$conn->dropColumn($installer->getTable('epicor_comm/erp_customer_group'),'custom_address_allowed'); 
$conn->addColumn($installer->getTable('epicor_comm/erp_customer_group'), 'allow_masquerade', array(
    'identity' => false,
    'nullable' => true,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'default' => null,
    'comment' => 'Allow Masquerade'
));

$conn->addColumn($installer->getTable('epicor_comm/erp_customer_group'), 'allow_masquerade_cart_clear', array(
    'identity' => false,
    'nullable' => true,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'default' => null,
    'comment' => 'Allow Masquerade Cart Clear'
));

$conn->addColumn($installer->getTable('epicor_comm/erp_customer_group'), 'allow_masquerade_cart_reprice', array(
    'identity' => false,
    'nullable' => true,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'default' => null,
    'comment' => 'Allow Masquerade Cart Reprice'
));

// add column to customer form 
//$installer->removeAttribute( 'customer', 'custom_address_allowed' );
$installer->addAttribute('customer', 'allow_masquerade', array(
    'label' => 'Allowed to Masquerade as Child Account',
    'type' => 'int',
    'input' => 'select',
    'visible' => false,
    'required' => false,
    'user_defined' => 0,
    'sort_order' => 5,
    'source' => 'epicor_comm/eav_attribute_data_yesnonulloption',
    'default' => '2'
));

$installer->addAttribute('customer', 'allow_masquerade_cart_clear', array(
    'label' => 'Allowed to Clear Cart before on Masquerading as Child Account',
    'type' => 'int',
    'input' => 'select',
    'visible' => false,
    'required' => false,
    'user_defined' => 0,
    'sort_order' => 5,
    'source' => 'epicor_comm/eav_attribute_data_yesnonulloption',
    'default' => '2'
));

$installer->addAttribute('customer', 'allow_masquerade_cart_reprice', array(
    'label' => '0',
    'type' => 'int',
    'input' => 'select',
    'visible' => false,
    'required' => false,
    'user_defined' => 0,
    'sort_order' => 5,
    'source' => 'epicor_comm/eav_attribute_data_yesnonulloption',
    'default' => '2'
));

$entityTypeId = $installer->getEntityTypeId('customer');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'allow_masquerade', '200'  //sort_order
);
$installer->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'allow_masquerade_cart_clear', '220'  //sort_order
);
$installer->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'allow_masquerade_cart_reprice', '230'  //sort_order
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'allow_masquerade');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'allow_masquerade_cart_clear');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'allow_masquerade_cart_reprice');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();


$installer->endSetup();
