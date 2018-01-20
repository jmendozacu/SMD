<?php
/************************************************************************
Step : Update Translation Table to add is_custom column
*************************************************************************/
$installer = $this;
$installer->startSetup();
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$conn->addColumn($installer->getTable('flexitheme/translation_data'), 'is_custom', array(
    'identity' => false,
    'nullable' => false,
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'primary' => false,
    'default' => 0,
    'comment' => 'User defined phrase to translate'
));

$installer->endSetup();