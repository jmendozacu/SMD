<?php

/**
 * Version 1.0.0.36 to 1.0.0.37 upgrade
 * 
 * Fixing bad data in erp images
 */

$installer = $this;
$installer->startSetup();

$tablePrefix = (string)Mage::getConfig()->getTablePrefix();
$installer->run("UPDATE ".$tablePrefix."catalog_product_entity_text SET value = 'a:0:{}' WHERE attribute_id IN(164,171) AND value LIKE 's:%'");

$installer->endSetup();