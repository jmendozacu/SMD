<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->addColumn($installer->getTable('customerconnect/erp_mapping_rmastatus'), 'status_text',
    array(
    'nullable' => true,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '4G',
    'comment' => 'RMA Status Text displayed to customer'
    )
);

$conn->addColumn(
    $installer->getTable('customerconnect/erp_mapping_rmastatus'), 'is_rma_deleted',
    array(
    'nullable' => true,
    'primary' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'length' => 1,
    'default' => false,
    'comment' => 'Is RMA deleted when changed to this status'
    )
);


$status = Mage::getModel('customerconnect/erp_mapping_rmastatus');
/* @var $status Epicor_Customerconnect_Model_Erp_Mapping_Rmastatus */

$status->setCode('deleted');
$status->setStatus('Deleted');
$status->setIsRmaDeleted(true);

$status->save();

$installer->endSetup();
