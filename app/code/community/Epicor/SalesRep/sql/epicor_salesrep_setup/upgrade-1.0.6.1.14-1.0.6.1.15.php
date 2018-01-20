<?php

$helper = Mage::helper('epicor_common/setup');
/* @var $helper Epicor_Common_Helper_Setup */

$helper->regenerateModuleElements('Epicor_SalesRep');

$salesRepGroup = $helper->loadAccessGroupByName('ECC Default - Sales Rep Access');

$right = $helper->loadAccessRightByName('Customerconnect - Rfqs - Add/Edit');
$element = $helper->addAccessElement('Epicor_Customerconnect', 'Rfqs', '*', '', 'Access');
$helper->addAccessRightElementById($right->getId(), $element->getId());

$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

// Add company column to Sales Rep Account Table
$tableName = $installer->getTable('epicor_salesrep/account');

if ($conn->tableColumnExists($tableName, 'company')) {
    $conn->modifyColumn($tableName, 'company', array(
        'nullable' => true,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,   // use type_text, not varchar, as it has been deprecated
        'comment' => 'column company'
    ));
} else {
    $conn->addColumn($tableName, 'company', array(
        'nullable' => true,
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'column company'
    ));
}    

$installer->endSetup();
