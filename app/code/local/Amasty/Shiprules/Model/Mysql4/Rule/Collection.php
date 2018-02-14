<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprules
 */ 
class Amasty_Shiprules_Model_Mysql4_Rule_Collection extends Amasty_Commonrules_Model_Resource_Rule_Collection
{
    public function _construct()
    {
        $this->_init('amshiprules/rule');
    }
}