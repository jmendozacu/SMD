<?php

/**
 * Upgrade - 1.0.0.72 to 1.0.0.73
 * 
 * Adding customer add custom address option 
 */
/* * **********************************************************************
  Step : Add Attributes to Customers
 * *********************************************************************** */


$installer = Mage::getResourceModel('customer/setup', 'default_setup');
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();


// add column to erp account table
$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$conn->changeColumn(
    $this->getTable('epicor_comm/erp_mapping_cardtype'), 'card_description', 'payment_method',
    array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 255,
    'identity' => false,
    'nullable' => false,
    'primary' => false,
    )
);

$setupCardTypeList = array(
    array('payment_method' => 'all', 'magento_code' => 'AE', 'erp_code' => 'AE'),
    array('payment_method' => 'all', 'magento_code' => 'VI', 'erp_code' => 'VI'),
    array('payment_method' => 'all', 'magento_code' => 'DI', 'erp_code' => 'DI'),
    array('payment_method' => 'all', 'magento_code' => 'SM', 'erp_code' => 'SM'),
    array('payment_method' => 'all', 'magento_code' => 'JCB', 'erp_code' => 'JCB'),
    array('payment_method' => 'all', 'magento_code' => 'LA', 'erp_code' => 'LA'),
    array('payment_method' => 'all', 'magento_code' => 'SO', 'erp_code' => 'SO'),
    array('payment_method' => 'all', 'magento_code' => 'OT', 'erp_code' => 'OT'),
);


foreach ($setupCardTypeList as $cardType) {
    $cardtype = Mage::getModel('epicor_comm/erp_mapping_cardtype')->getCollection()
        ->addFieldToFilter('magento_code', array('eq' => $cardType['magento_code']))
        ->addFieldToFilter('erp_code', array('eq' => $cardType['erp_code']))
        ->addFieldToFilter('payment_method', array('eq' => $cardType['payment_method']))
        ->getFirstItem();

    if ($cardtype->isObjectNew()) {
        $cardtype = Mage::getModel('epicor_comm/erp_mapping_cardtype');
    }
    $cardtype->setPaymentMethod($cardType['payment_method'])
        ->setMagentoCode($cardType['magento_code'])
        ->setErpCode($cardType['erp_code'])
        ->save();
}

$installer->endSetup();
