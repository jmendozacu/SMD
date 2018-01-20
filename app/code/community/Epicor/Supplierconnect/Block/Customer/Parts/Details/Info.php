<?php

/**
 * Parts info data setup
 * 
 * @category   Epicor
 * @package    Epicor_Supplierconnect
 * @author     Epicor Websales Team
 */
class Epicor_Supplierconnect_Block_Customer_Parts_Details_Info extends Epicor_Supplierconnect_Block_Customer_Info {

    public function _construct() {
        parent::_construct();
        $this->setColumnCount(2);

        $this->_infoData = array();

        $partMsg = Mage::registry('supplier_connect_part_details');

        if ($partMsg) {

            $part = $partMsg->getPart();
            if ($part) {

                $helper = Mage::helper('epicor_comm/messaging');
                /* @var $helper Epicor_Comm_Helper_Messaging */

                $currency = $helper->getCurrencyMapping($part->getCurrencyCode(), Epicor_Comm_Helper_Messaging::ERP_TO_MAGENTO);

                $this->_infoData = array(
                    $this->__('Effective Date: ') => $part->getEffectiveDate() ? $this->getHelper()->getLocalDate($part->getEffectiveDate(), Epicor_Common_Helper_Data::DAY_FORMAT_MEDIUM, true) : $this->__('N/A'),
                    $this->__('Expires: ') => $part->getExpiresDate() ? $this->getHelper()->getLocalDate($part->getExpiresDate(), Epicor_Common_Helper_Data::DAY_FORMAT_MEDIUM, true) : $this->__('N/A'),
                    $this->__('Lead Days: ') => $part->getLeadDays(),
                    $this->__('Quantity On Hand: ') => $part->getQuantityOnHand(),
                    $this->__('Reference : ') => $part->getReference(),
                    $this->__('Comments : ') => $part->getPriceComments(),
                    $this->__('Currency: ') => $part->getCurrencyCode(),
                    $this->__('Minimum Price: ') => $helper->formatPrice($part->getMinimumPrice(), true, $currency),
                    $this->__('Base Unit Price: ') => $helper->formatPrice($part->getBaseUnitPrice(), true, $currency),
                    $this->__('Price Per: ') => $part->getPricePer(),
                    $this->__('Discount % : ') => $part->getDiscountPercent(),
                    $this->__('Price Modifier : ') => $part->getPriceBreakModifier()
                );
            }
        }

        $this->setTitle($this->__('Part Information'));
    }

}