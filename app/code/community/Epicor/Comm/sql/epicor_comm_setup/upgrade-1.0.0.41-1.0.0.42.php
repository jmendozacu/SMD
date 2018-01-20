<?php

/**
 * Upgrade - 1.0.0.41 to 1.0.0.42
 * 
 * Adding new column to message log for Store and URL
 */
$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();

try{
	$conn->addColumn($installer->getTable('epicor_comm/message_log'), 'store', array(
		'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
		'length' => 100,
		'comment' => 'Store'
	));
} catch(Exception $ex){
	Mage::log('epicor comm upgrade-1.0.0.41-10.0.42 Script failure: ');
        Mage::log($ex);
}

try{
	$conn->addColumn($installer->getTable('epicor_comm/message_log'), 'erp_url', array(
		'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
		'length' => 100,
		'comment' => 'ERP URL request sent to'
	));
} catch(Exception $ex){
	Mage::log('epicor comm upgrade-1.0.0.41-10.0.42 Script failure: ');
        Mage::log($ex);
}

$installer->endSetup();
