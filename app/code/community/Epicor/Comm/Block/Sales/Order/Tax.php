<?php

class Epicor_Comm_Block_Sales_Order_Tax extends Mage_Tax_Block_Sales_Order_Tax
{

    public function getTemplateFile()
    {
        $this->setTemplate('epicor_comm/tax/order/tax.phtml');
        return parent::getTemplateFile();
    }

}
