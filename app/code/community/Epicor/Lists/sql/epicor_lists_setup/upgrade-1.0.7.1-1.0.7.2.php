<?php

/**
 * Hiding given attributes from account information page
 */
$installer = $this;
$installer->startSetup();

$installHelper = Mage::helper('epicor_common/setup');
$process_attribute = array('ecc_contract_shipto_default', 'ecc_contract_shipto_date', 'ecc_contract_shipto_prompt', 'ecc_contract_header_selection', 'ecc_contract_header_prompt', 'ecc_contract_header_always', 'ecc_contract_line_selection', 'ecc_contract_line_prompt', 'ecc_contract_line_always');
foreach ($process_attribute as $proc_attr) {
    $installHelper->attributeVisibilityInForm($proc_attr);
}

$installer->endSetup();
