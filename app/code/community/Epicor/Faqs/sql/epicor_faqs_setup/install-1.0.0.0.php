<?php
/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
/**
 * Creating table epicor_faqs
 */
$installer->getConnection()->dropTable($this->getTable('epicor_faqs/faqs'));
$installer->getConnection()->dropTable($this->getTable('epicor_faqs/vote'));
$table = $installer->getConnection()
        ->newTable($installer->getTable('epicor_faqs/faqs'))
        ->addColumn('faqs_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'identity' => true,
            'nullable' => false,
            'primary' => true,
                ), 'Entity id')
        ->addColumn('weight', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => false,
            'nullable' => false,
                ), 'Weight')
        ->addColumn('question', Varien_Db_Ddl_Table::TYPE_TEXT, '1M', array(
            'nullable' => true,
                ), 'Question')
        ->addColumn('answer', Varien_Db_Ddl_Table::TYPE_TEXT, '1M', array(
            'nullable' => true,
            'default' => null,
                ), 'Answer')
        ->addColumn('stores', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
            'default' => null,
                ), 'Stores')
        ->addColumn('useful', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => false,
            'nullable' => false,
            'default' => 0
                ), 'Useful votes')
        ->addColumn('useless', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => false,
            'nullable' => false,
            'default' => 0
                ), 'Useless votes')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => true,
            'default' => null,
                ), 'Creation Time')
        ->setComment('Faqs item');

$installer->getConnection()->createTable($table);
//Votes table
$table = $installer->getConnection()
        ->newTable($installer->getTable('epicor_faqs/vote'))
        ->addColumn('vote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'identity' => true,
            'nullable' => false,
            'primary' => true,
                ), 'Entity id')
        ->addColumn('faqs_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => true,
                ), 'F.A.Q. ID')
        ->addIndex(
        $installer->getIdxName(
            $this->getTable('epicor_faqs/vote'),
            array('faqs_id')
        ),
        'faqs_id')
        ->addForeignKey(
                $installer->getFkName(
                $this->getTable('epicor_faqs/faqs'), 
                'faqs_id', 
                $this->getTable('epicor_faqs/vote'),
                'faqs_id'), 
                'faqs_id', 
                $this->getTable('epicor_faqs/faqs'), 'faqs_id', 
                Varien_Db_Ddl_Table::ACTION_SET_NULL, 
                Varien_Db_Ddl_Table::ACTION_NO_ACTION)
        ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => true,
                ), 'User ID')
        ->addIndex(
        $installer->getIdxName(
            $this->getTable('epicor_faqs/vote'),
            array('user_id')
        ),
                'user_id') 
        ->addForeignKey(
                $installer->getFkName(
                $this->getTable('admin/user'), 
                'user_id', 
                $this->getTable('epicor_faqs/vote'),
                'user_id'), 
                'user_id', 
                $this->getTable('admin/user'), 'user_id', 
                Varien_Db_Ddl_Table::ACTION_SET_NULL, 
                Varien_Db_Ddl_Table::ACTION_NO_ACTION)

        ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TINYINT, 1, array(
            'nullable' => false,
                ), 'Value');
$installer->getConnection()->createTable($table);
$installer->endSetup();
