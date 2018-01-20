<?php


$installer = $this;
$installer->startSetup();
$conn=$installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */
$conn->addColumn($installer->getTable('epicor_comm/erp_customer_group'), 
        'pre_reg_password',
        array(
        'identity'  => false,
        'nullable'  => false,
        'primary'   => false,
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 23,
        'default'   => '',
        'comment'   => 'Pre Register password'
        ));
		
try {
    $hadSolarsoft = count($conn->fetchAll("SELECT * from solarsoft_comm_erp_customer_group"));
} catch (Exception $exc) {
    $hadSolarsoft = false;
}
if ($hadSolarsoft) {
    $to = $this->getTable('epicor_comm/customer_erpaccount');
    $from = 'solarsoft_comm_erp_customer_group';
    $sql = "UPDATE $to as dest set pre_reg_password = (SELECT pre_reg_password from $from as src where dest.entity_id = src.entity_id);\n";
    $conn->exec($sql);
}		
$installer->endSetup();
