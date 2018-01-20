<?php
/**
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

try {
    $installer->run(
        "
    ALTER TABLE `{$this->getTable('catalog_eav_attribute')}`
        ADD `image` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
    "
    );
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
