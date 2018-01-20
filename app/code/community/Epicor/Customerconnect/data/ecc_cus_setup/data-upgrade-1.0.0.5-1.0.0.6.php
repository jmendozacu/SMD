<?php

/**
 * Version 1.0.0.4 to 1.0.0.5 upgrade
 * 
 * insert of default data to erp_mapping_erporderstatus
 * 
 */ 
$status = Mage::getModel('customerconnect/erp_mapping_erporderstatus');

$status->setCode('O');
$status->setStatus('Open');
$status->setState('Open');

$status->save();

$status = Mage::getModel('customerconnect/erp_mapping_erporderstatus');

$status->setCode('C');
$status->setStatus('Closed');
$status->setState('Closed');

$status->save();

