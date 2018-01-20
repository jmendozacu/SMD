<?php

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer->startSetup();
$conn = $installer->getConnection();

$conn->dropTable($this->getTable('epicor_comm/customer_return'));
$table = $conn->newTable($this->getTable('epicor_comm/customer_return'));
$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
    array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
    ), 'ID'
);

$table->addColumn(
    'erp_returns_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'Erp Returns Number'
);

$table->addColumn(
    'rma_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    'nullable' => false,
    'default' => 0
    ), 'RMA Date'
);

$table->addColumn(
    'returns_status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'returnsStatus'
);

$table->addColumn(
    'customer_reference', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'customerReference'
);

$table->addColumn(
    'email_address', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'Email Address'
);

$table->addColumn(
    'reason_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'reason Code'
);

$table->addColumn(
    'customer_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'customerCode'
);

$table->addColumn(
    'customer_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'customerName'
);

$table->addColumn(
    'credit_invoice_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'creditInvoiceNumber'
);

$table->addColumn(
    'rma_case_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'rmaCaseNumber'
);

$table->addColumn(
    'rma_contact', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'rmaContact'
);

$table->addColumn(
    'note_text', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'note Text'
);

$table->addColumn(
    'previous_erp_data', Varien_Db_Ddl_Table::TYPE_TEXT, '4G',
    array(
    'nullable' => false,
    'default' => ''
    ), 'Previous ERP Data'
);


$conn->createTable($table);


$conn->dropTable($this->getTable('epicor_comm/customer_return_line'));
$table = $conn->newTable($this->getTable('epicor_comm/customer_return_line'));
$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
    array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
    ), 'ID'
);


$table->addColumn(
    'erp_line_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'ERP line number'
);

$table->addColumn(
    'return_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
    array(
    'identity' => false,
    'unsigned' => true,
    'nullable' => false,
    'primary' => false,
    ), 'Return ID'
);

$table->addColumn(
    'product_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'Product Code'
);

$table->addColumn(
    'revision_level', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'Revision Level'
);

$table->addColumn(
    'unit_of_measure_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'Unit Of Measure Code'
);

$table->addColumn(
    'qty_ordered', Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
    array(
    'identity' => false,
    'unsigned' => true,
    'nullable' => false,
    'primary' => false,
    ), 'Qty Ordered'
);

$table->addColumn(
    'qty_returned', Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
    array(
    'identity' => false,
    'unsigned' => true,
    'nullable' => false,
    'primary' => false,
    ), 'Qty Returned'
);

$table->addColumn(
    'returns_status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'returnsStatus'
);

$table->addColumn(
    'order_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'order Number'
);

$table->addColumn(
    'order_line', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'order Line'
);

$table->addColumn(
    'order_release', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'order Release'
);


$table->addColumn(
    'shipment_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'shipment number'
);


$table->addColumn(
    'invoice_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'invoice Number'
);

$table->addColumn(
    'serial_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'serial Number'
);

$table->addColumn(
    'reason_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'reason Code'
);

$table->addColumn(
    'note_text', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable' => true,
    ), 'note Text'
);

$table->addColumn(
    'to_be_deleted', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1, array(
    'nullable' => true,
    'default' => 'N'
    ), 'To Be deleted Text'
);

$table->addColumn(
    'previous_erp_data', Varien_Db_Ddl_Table::TYPE_TEXT, '4G',
    array(
    'nullable' => false,
    'default' => ''
    ), 'Previous ERP Data'
);

$table->addIndex(
    $installer->getIdxName(
        $this->getTable('epicor_comm/customer_return_attachment'), array('return_id')
    ), 'return_id'
);

$conn->createTable($table);

$conn->dropTable($this->getTable('epicor_comm/customer_return_attachment'));
$table = $conn->newTable($this->getTable('epicor_comm/customer_return_attachment'));
$table->addColumn(
    'id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
    array(
    'identity' => true,
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
    ), 'ID'
);

$table->addColumn(
    'return_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
    array(
    'identity' => false,
    'unsigned' => true,
    'nullable' => false,
    'primary' => false,
    ), 'Return ID'
);

$table->addColumn(
    'line_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
    array(
    'identity' => false,
    'unsigned' => true,
    'nullable' => true,
    'primary' => false,
    ), 'Line ID'
);

$table->addColumn(
    'attachment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10,
    array(
    'identity' => false,
    'unsigned' => true,
    'nullable' => false,
    'primary' => false,
    ), 'Attachment ID'
);

$table->addColumn(
    'to_be_deleted', Varien_Db_Ddl_Table::TYPE_VARCHAR, 1, array(
    'nullable' => true,
    'default' => 'N'
    ), 'To Be deleted'
);

$table->addIndex(
    $installer->getIdxName(
        $this->getTable('epicor_comm/customer_return_attachment'), array('return_id')
    ), 'return_id'
);
$table->addIndex(
    $installer->getIdxName(
        $this->getTable('epicor_comm/customer_return_attachment'), array('line_id')
    ), 'line_id'
);
$table->addIndex(
    $installer->getIdxName(
        $this->getTable('epicor_comm/customer_return_attachment'), array('attachment_id')
    ), 'attachment_id'
);

$table->addForeignKey(
    $installer->getFkName(
        $this->getTable('epicor_comm/customer_return'), 'id', $this->getTable('epicor_comm/customer_return_attachment'),
        'return_id'), 'return_id', $this->getTable('epicor_comm/customer_return'), 'id',
    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);

$table->addForeignKey(
    $installer->getFkName($this->getTable('epicor_comm/customer_return_line'), 'id',
        $this->getTable('epicor_comm/customer_return_attachment'), 'line_id'), 'line_id',
    $this->getTable('epicor_comm/customer_return_line'), 'id', Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

$conn->createTable($table);
