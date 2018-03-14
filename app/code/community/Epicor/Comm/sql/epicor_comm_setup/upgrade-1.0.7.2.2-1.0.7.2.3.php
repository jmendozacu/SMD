<?php

/**
 * Version 1.0.7.2.2 - 1.0.7.2.3 upgrade
 *
 */
$installer = $this;
$installer->startSetup();

$installHelper = Mage::helper('epicor_common/setup');
/* @var $installHelper Epicor_Common_Helper_Setup */
$installHelper->addAccessElement('Epicor_Customerconnect', 'Orders', 'exportOrdersCsv', '', 'Access', 1);
$installHelper->addAccessElement('Epicor_Customerconnect', 'Orders', 'exportOrdersXml', '', 'Access', 1);

$installer->endSetup();
