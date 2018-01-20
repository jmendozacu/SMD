<?php

/**
 * @package Mage_Core
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$connection
        ->dropForeignKey($installer->getTable('epicor_faqs/vote'), $installer->getFkName(
                        $this->getTable('admin/user'), 'user_id', $this->getTable('epicor_faqs/vote'), 'user_id'
                )
        )
        ->dropIndex($installer->getTable('epicor_faqs/vote'), $installer->getTable('epicor_faqs/vote') . '_user_id_idx');

$connection
        ->dropColumn($installer->getTable('epicor_faqs/vote'), 'user_id');
$connection
        ->addColumn($this->getTable('epicor_faqs/vote'), 'customer_id', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'unsigned' => true,
            'nullable' => false,
            'comment' => 'Customer ID'
                )
);
$connection
        ->addIndex(
            $this->getTable('epicor_faqs/vote'),
            $installer->getIdxName(
                $this->getTable('epicor_faqs/vote'), 
                array('customer_id')
            ),
            'customer_id');
$connection
        ->addForeignKey(
                $installer->getFkName(
                        $this->getTable('customer/entity'), 'entity_id', $this->getTable('epicor_faqs/vote'), 'customer_id'
                ), $this->getTable('epicor_faqs/vote'), 'customer_id', $this->getTable('customer/entity'), 'entity_id', Varien_Db_Ddl_Table::ACTION_NO_ACTION, Varien_Db_Ddl_Table::ACTION_NO_ACTION
);
$installer->endSetup();
