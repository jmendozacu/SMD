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
        "ALTER TABLE `{$this->getTable('eav_attribute_option')}`
    ADD `image` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    ADD `additional_image` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
"
    );
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
