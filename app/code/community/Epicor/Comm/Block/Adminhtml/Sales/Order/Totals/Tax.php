<?php

class Epicor_Comm_Block_Adminhtml_Sales_Order_Totals_Tax extends Mage_Adminhtml_Block_Sales_Order_Totals_Tax
{

    public function getTemplateFile()
    {
        $this->setTemplate('epicor_comm/sales/order/totals/tax.phtml');
        return parent::getTemplateFile();
    }

}
