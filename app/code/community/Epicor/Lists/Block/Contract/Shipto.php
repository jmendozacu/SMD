<?php

/**
 * Shipto select page grid
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Contract_Shipto extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'contract_shipto';
        $this->_blockGroup = 'epicor_lists';
        $this->_headerText = Mage::helper('epicor_lists')->__('Select Ship To');
    }

    protected function _postSetup()
    {
        $this->setBoxed(true);
        parent::_postSetup();
    }

}
