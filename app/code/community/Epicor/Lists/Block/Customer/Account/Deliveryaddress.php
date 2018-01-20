<?php

/**
 * Setting button for adding new List 
 *
 * @category   Epicor
 * @package    Epicor_Lists
 * @author     Epicor Websales Team
 */
class Epicor_Lists_Block_Customer_Account_Deliveryaddress extends Epicor_Common_Block_Generic_List
{

    protected function _setupGrid()
    {
        $this->_controller = 'customer_account_deliveryaddress';
        $this->_blockGroup = 'epicor_lists';
        $this->_headerText = $this->__('Delivery Addresses');
        $this->_removeButton('add');
    }

    protected function _postSetup()
    {
        $this->setBoxed(true);
        parent::_postSetup();
    }

}
