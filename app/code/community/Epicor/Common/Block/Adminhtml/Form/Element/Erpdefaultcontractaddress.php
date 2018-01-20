<?php

class Epicor_Common_Block_Adminhtml_Form_Element_Erpdefaultcontractaddress extends Varien_Data_Form_Element_Abstract
{

    protected $_element;



    /**
     * @return string
     */
    public function getElementHtml()
    {
            $selectHtml = '<div id="appendcontractaddress"></div>';

            return $selectHtml;
    }



}
