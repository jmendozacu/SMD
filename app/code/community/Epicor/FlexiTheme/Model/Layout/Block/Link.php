<?php

class Epicor_FlexiTheme_Model_Layout_Block_Link extends Mage_Core_Model_Abstract
{
    public function _construct()
    {    
        // Note that the web_id refers to the key field in your database table.
        $this->_init('flexitheme/layout_block_link');
    }
    
}