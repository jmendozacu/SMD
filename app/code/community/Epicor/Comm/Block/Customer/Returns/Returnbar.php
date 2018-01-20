<?php

/**
 * Returns creation page, Products block
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Customer_Returns_Returnbar extends Epicor_Comm_Block_Customer_Returns_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('epicor_comm/customer/returns/return_bar.phtml');
    }
}
