<?php

class Epicor_Supplierconnect_Block_Customer_Rfqs_Details_Priceinfo extends Mage_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('supplierconnect/customer/rfq/priceinfo.phtml');
        $this->setTitle($this->__('Price Information'));
    }

    /**
     * Gets Price Per options from the RFQ provided
     * 
     * @param Epicor_Common_Model_Xmlvarien $rfq
     * 
     * @return array
     */
    public function getPricePerOptions($rfq)
    {
        $labels = array(
            'E' => '/1',
            'C' => '/100',
            'M' => '/1000',
        );

        $options = array();

        if ($rfq->getPricePerOptions()) {
            foreach ($rfq->getPricePerOptions()->getasarrayOption() as $option) {
                if (isset($labels[$option])) {
                    $options[$option] = $labels[$option];
                }
            }
        }

        if (empty($options)) {
            $options = $labels;
        }

        return $options;
    }

    /**
     * Gets Price Break Modifier from the RFQ provided
     * 
     * @param Epicor_Common_Model_Xmlvarien $rfq
     * 
     * @return array
     */
    public function getPriceBreakModifierOptions($rfq)
    {
        $labels = array(
            '$' => 'Flat Unit Price',
            '%' => 'Percentage of Base',
        );

        $options = array();

        if ($rfq->getPriceBreakModifierOptions()) {
            foreach ($rfq->getPriceBreakModifierOptions()->getasarrayOption() as $option) {
                if (isset($labels[$option])) {
                    $options[$option] = $labels[$option];
                }
            }
        }

        if (empty($options)) {
            $options = $labels;
        }

        return $options;
    }

    /**
     * Gets Days difference between Effective and expires date
     * 
     * @param Epicor_Common_Model_Xmlvarien $rfq
     * 
     * @return integer
     */
    public function getDays($rfq)
    {
        $days = '';
        if ($rfq->getExpiresDate()) {
            
            $datetime1 = new DateTime(date('Y-m-d 00:00:00',strtotime($rfq->getEffectiveDate())));
            $datetime2 = new DateTime(date('Y-m-d 23:59:59',strtotime($rfq->getExpiresDate())));
            $days = $datetime1->diff($datetime2)->days;
        }

        return $days;
    }

}
