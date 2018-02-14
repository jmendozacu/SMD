<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprules
 */
$this->startSetup();

$this->run("
ALTER TABLE `{$this->getTable('amshiprules/rule')}`  
  ADD `for_admin` TINYINT NOT NULL DEFAULT 0 AFTER `is_active`,
  ADD `coupon_disable` VARCHAR(255) NOT NULL ,
  ADD `discount_id_disable` int(11) DEFAULT 0 NOT NULL ,
  ADD `time_to` int(11) DEFAULT NULL DEFAULT 0 AFTER `days` ,
  ADD `time_from` int(11) DEFAULT NULL DEFAULT 0 AFTER `days`;
");

$this->endSetup();