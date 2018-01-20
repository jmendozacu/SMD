<?php

/**
 * Data Upgrade - 1.0.0.9 to 1.0.0.10
 * 
 * Adding in default invoice statuses
 */
$status = Mage::getModel('customerconnect/erp_mapping_invoicestatus');

$status->setCode('O');
$status->setStatus('Open');
$status->setState('Open');

$status->save();

$status = Mage::getModel('customerconnect/erp_mapping_invoicestatus');

$status->setCode('C');
$status->setStatus('Closed');
$status->setState('Closed');

$status->save();