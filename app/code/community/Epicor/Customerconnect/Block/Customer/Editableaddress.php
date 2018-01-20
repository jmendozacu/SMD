<?php

/**
 * @method string getTitle()
 * @method void setTitle(string $title)
 * @method void setOnRight(bool $bool)
 * @method bool getOnRight()
 */
class Epicor_Customerconnect_Block_Customer_Editableaddress extends Epicor_Customerconnect_Block_Customer_Address
{

    public function _construct()
    {
        parent::_construct();
        $this->_addressData = array();
        $this->setTemplate('customerconnect/customer/account/address/edit.phtml');
        $this->setOnRight(false);
        $this->setHideWrapper(true);
    }

    public function isEditable()
    {
        return true;
    }

}
