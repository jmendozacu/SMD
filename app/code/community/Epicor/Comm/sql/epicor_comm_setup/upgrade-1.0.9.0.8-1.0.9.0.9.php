<?php

/**
 * Upgrade - upgrade-1.0.9.0.8-1.0.9.0.9
 * 
 * Adding customer  custom address option 
 */

/************************************************************************
Step : Add Attributes to Customers and Erp table
*************************************************************************/


$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();
 

// add column to erp account table
$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$erpTableName = $installer->getTable('epicor_comm/erp_customer_group');

if (!$conn->tableColumnExists($erpTableName, 'allow_shipping_address_create')) {
    $conn->addColumn($erpTableName, 
            'allow_shipping_address_create',
            array(
            'identity'  => false,
            'nullable'  => true,
            'primary'   => false,
            'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
            'default'   => null,
            'comment'   => 'Allow Shipping Address Creation'
            ));
}

if (!$conn->tableColumnExists($erpTableName, 'allow_billing_address_create')) {
    $conn->addColumn($erpTableName, 
            'allow_billing_address_create',
            array(
            'identity'  => false,
            'nullable'  => true,
            'primary'   => false,
            'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
            'default'   => null,
            'comment'   => 'Allow Billing Address Creation'
            ));

    $sql = "UPDATE epicor_comm_erp_customer_group as t1 INNER JOIN epicor_comm_erp_customer_group as t2 ON t1.entity_id = t2.entity_id SET t1.allow_shipping_address_create = t2.custom_address_allowed,t1.allow_billing_address_create = t2.custom_address_allowed\n";
    $conn->exec($sql);
}
 

$installer->removeAttribute( 'customer', 'ecc_allow_ship_address_create' );
$installer->addAttribute('customer', 'ecc_allow_ship_address_create', array(
    'label' => 'Allow Shipping Address Creation',
    'type' => 'int',
    'input' => 'select',
    'visible' => false,
    'required' => false,
    'user_defined' => 1,
    'sort_order'=> 10,
    'source' => 'epicor_comm/eav_attribute_data_yesnonulloption',
    'default' =>'2'
));


$installer->removeAttribute( 'customer', 'ecc_allow_bill_address_create' );
$installer->addAttribute('customer', 'ecc_allow_bill_address_create', array(
    'label' => 'Allow Billing Address Creation',
    'type' => 'int',
    'input' => 'select',
    'visible' => false,
    'required' => false,
    'user_defined' => 1,
    'sort_order'=> 12,
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
 'ecc_allow_ship_address_create',
 '101'  //sort_order
);

$installer->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'ecc_allow_bill_address_create',
 '103'  //sort_order
);


$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'ecc_allow_bill_address_create');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'ecc_allow_ship_address_create');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();

$customerEntityPresent = $installer->getAttribute('customer','custom_address_allowed');
if(!empty($customerEntityPresent)) {
    $customerEntityInt = $installer->getTable('customer_entity_int');

    $customerAttId = $installer->getAttribute($entityTypeId,'custom_address_allowed');
    $customerAddressAttributeId = $customerAttId['attribute_id'];
    
    $shipAddressAttId = $installer->getAttribute($entityTypeId,'ecc_allow_ship_address_create');
    $shipAddressCreateattributeId = $shipAddressAttId['attribute_id'];
    $sqlShipCreate = "INSERT INTO $customerEntityInt (entity_type_id, attribute_id, entity_id, value) SELECT entity_type_id, $shipAddressCreateattributeId, entity_id, value FROM customer_entity_int
    WHERE attribute_id = $customerAddressAttributeId";
    $conn->exec($sqlShipCreate);

    $billAddressAttId = $installer->getAttribute($entityTypeId,'ecc_allow_bill_address_create');
    $billAddressCreateattributeId = $billAddressAttId['attribute_id'];
    $sqlBillCreate = "INSERT INTO $customerEntityInt (entity_type_id, attribute_id, entity_id, value) SELECT entity_type_id, $billAddressCreateattributeId, entity_id, value FROM customer_entity_int
    WHERE attribute_id = $customerAddressAttributeId";
    $conn->exec($sqlBillCreate);

    //Remove the customer address allowed in customer attribute
    $installer->removeAttribute( 'customer', 'custom_address_allowed' );
    $conn->dropColumn($installer->getTable('epicor_comm/erp_customer_group'),'custom_address_allowed');
}
$installer->endSetup();