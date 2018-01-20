<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Lineinfo extends Epicor_Supplierconnect_Block_Customer_Info
{

    public function _construct()
    {
        parent::_construct();

        $rfq = Mage::registry('supplier_connect_rfq_details');

        $quantityRequested = '';
        if ($rfq->getQuantities()) {
            foreach ($rfq->getQuantities()->getasarrayQuantity() as $quantity) {
                $quantityRequested .= round($quantity->getValue()) . ', ';
            }

            $quantityRequested = rtrim($quantityRequested, ', ');
        }

        $this->_infoData = array(
            $this->__('Part Number: ') => $rfq->getProductCode(),
            $this->__('Description: ') => $rfq->getDescription(),
            $this->__('Unit Of Measure: ') => $rfq->getUnitOfMeasureDescription(),
            $this->__('Quantities Requested: ') => $quantityRequested,
            $this->__('Response: ') => $rfq->getResponse(),
        );

        $this->_extraData = array(
            $this->__('Line Comments') => $rfq->getLineComments(),
        );

        $this->setTitle($this->__('Line Information'));
        $this->setOnRight(true);
        $this->setColumnCount(1);
    }
}
