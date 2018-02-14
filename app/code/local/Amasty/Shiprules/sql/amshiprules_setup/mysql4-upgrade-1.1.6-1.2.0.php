<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprules
 */
$this->startSetup();

$this->run("
ALTER TABLE `{$this->getTable('amshiprules/rule')}` ADD `out_of_stock` TINYINT NOT NULL DEFAULT 0 AFTER `is_active`;
");

$this->endSetup();