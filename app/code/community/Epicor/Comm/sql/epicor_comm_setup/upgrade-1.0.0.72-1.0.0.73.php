<?php

/**
 * Upgrade - 1.0.0.72 to 1.0.0.73
 * 
 * Adding customer add custom address option 
 */

/************************************************************************
Step : Add Attributes to Customers
*************************************************************************/


$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();
 

// add column to erp account table
$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
//$conn->dropColumn($installer->getTable('epicor_comm/erp_customer_group'),'custom_address_allowed'); 
$conn->addColumn($installer->getTable('epicor_comm/erp_customer_group'), 
        'custom_address_allowed',
        array(
        'identity'  => false,
        'nullable'  => true,
        'primary'   => false,
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'default'   => null,
        'comment'   => 'Custom Address Allowed'
        ));

// add column to customer form 
//$installer->removeAttribute( 'customer', 'custom_address_allowed' );
$installer->addAttribute('customer', 'custom_address_allowed', array(
    'label' => 'Custom Address Allowed',
    'type' => 'int',
    'input' => 'select',
    'visible' => false,
    'required' => false,
    'user_defined' => 1,
    'sort_order'=> 5,
    'source' => 'epicor_comm/eav_attribute_data_yesnonulloption',
    
    'default' =>'2'
));

$entityTypeId     = $installer->getEntityTypeId('customer');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'custom_address_allowed',
 '10'  //sort_order
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'custom_address_allowed');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();


$installer->endSetup();