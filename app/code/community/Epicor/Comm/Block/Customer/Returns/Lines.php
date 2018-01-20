<?php

/**
 * Customer Orders list
 */
class Epicor_Comm_Block_Customer_Returns_Lines extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'customer_returns_lines';
        $this->_blockGroup = 'epicor_comm';
        $this->_headerText = Mage::helper('epicor_comm')->__('Lines');
    }

    protected function _postSetup()
    {
        if (Mage::registry('details_display')) {
            $this->setBoxed(true);
        }

        parent::_postSetup();
    }

}
