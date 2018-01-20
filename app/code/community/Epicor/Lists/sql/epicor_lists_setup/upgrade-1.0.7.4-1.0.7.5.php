<?php

/**
 * Hiding given attributes from account information page
 */
$installer = $this;
$installer->startSetup();

$installHelper = Mage::helper('epicor_common/setup');
$process_attribute = array('ecc_default_contract', 'ecc_default_contract_address', 'ecc_contracts_filter');
foreach ($process_attribute as $proc_attr) {
    $installHelper->attributeVisibilityInForm($proc_attr);
}

$installer->endSetup();
