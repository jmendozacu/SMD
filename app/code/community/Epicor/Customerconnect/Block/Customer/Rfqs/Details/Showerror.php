<?php

/**
 * RFQ Details page js error show (shows an error in a child iframe to be displayed in parent window)
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Showerror extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('customerconnect/customer/account/rfqs/details/show_error.phtml');
    }

    public function getError()
    {
        return Mage::registry('rfq_error');
    }
    public function getErrorMessage()
    {
        //additional message
        $message = Mage::registry('message_error');
        if(isset($message['text'])){ 
            //remove line breaks
            $message['text'] = preg_replace( "/\r|\n/", "", $message['text']);
            return $message;
        }else{
            return array();
        }
    }

    public function getDescriptionError()
    {
        return Mage::registry('rfq_error_description');
    }

}
