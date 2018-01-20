<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

if (!$conn->tableColumnExists($installer->getTable('epicor_salesrep/account'), 'catalog_access')) {
    $conn->addColumn($this->getTable('epicor_salesrep/account'), 'catalog_access', array(
        'nullable' => true,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'default' => null,
        'length' => 1,
        'comment' => 'Catalog Acces'
    ));
}

$installer->endSetup();

$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->addAttribute('customer', 'ecc_salesrep_catalog_access', array(
    'group' => 'General',
    'label' => 'Sales Rep Can Access Catalog',
    'type' => 'varchar',
    'input' => 'select',
    'required' => false,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'source' => 'epicor_salesrep/eav_attribute_data_yesnonulloption',
    'default' => ''
));


$entityTypeId = $installer->getEntityTypeId('customer');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttributeToGroup(
        $entityTypeId, $attributeSetId, $attributeGroupId, 'ecc_salesrep_catalog_access', '2000'  //sort_order
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'ecc_salesrep_catalog_access');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();
