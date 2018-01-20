<?php

class Epicor_FlexiTheme_Model_Mysql4_Layout_Block_Link_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('flexitheme/layout_block_link');
    }
}