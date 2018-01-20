<?php

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();
$conn=$installer->getConnection();

$conn->addColumn($installer->getTable('sales/order'), 
        'print_pick_note',
        array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'length'    => 1,
        'default'   => 0,
        'comment'   => 'Printed Pick Note'
        ));

$installer->endSetup();
