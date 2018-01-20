<?php

/**
 * Parts price info setup
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Parts_Details_Price extends Epicor_Supplierconnect_Block_Customer_Info {

    public function _construct() {
        parent::_construct();

        $this->setColumnCount(2);

        $part = Mage::registry('supplier_connect_part_details');
        $this->_infoData = array(
            $this->__('Part Number: ') => $part->getProductCode(),
            $this->__('Subcontract Operation: ') => $part->getPart()->getSubcontractOperation(),
        );
        $this->setTitle($this->__('Price Information'));
    }

}