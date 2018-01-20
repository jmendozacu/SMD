<?php

/**
 * RFQ editable options display
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */
class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Options extends Mage_Core_Block_Template
{

    private $_methods;
    private $_method;

    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('customerconnect/customer/account/rfqs/details/options.phtml');
        $this->setTitle($this->__('Options'));
    }

    public function _toHtml()
    {
        $rfq = Mage::registry('customer_connect_rfq_details');
        $html = '';

        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */
        $rfq = base64_encode(serialize($helper->varienToArray($rfq)));
        $html = '<input type="hidden" name="old_data" value="' . $rfq . '" />';
        if(Mage::registry('rfq_duplicate')) {
            $html .= '<input type="hidden" name="is_duplicate" value="1" />';
        }
        $html .= parent::_toHtml();
        return $html;
    }

    public function getWebReference()
    {
        $rfq = Mage::registry('customer_connect_rfq_details');
        /* @var $rfq Epicor_Common_Model_Xmlvarien */

        if ($rfq->getWebReference()) {
            $webRef = $rfq->getWebReference();
        } else {
            $rfqHelper = Mage::helper('customerconnect/rfq');
            /* @var $rfqHelper Epicor_Customerconnect_Helper_Rfq */
            
            $webRef = $rfqHelper->getNextRfqWebRef();
        }

        return $webRef;
    }

    public function getRequiredDate()
    {
        $rfq = Mage::registry('customer_connect_rfq_details');

        $helper = Mage::helper('customerconnect');
        /* @var $helper Epicor_Customerconnect_Helper_Data */

        $date = $rfq->getRequiredDate();
        $data = '';

        if (!empty($date)) {
            try {
                $data = $helper->getLocalDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            } catch (Exception $ex) {
                $data = $date;
            }
        }

        return $data;
    }

    public function getMappedShippingMethod()
    {
        if (!$this->_method) {
            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */
            $rfq = Mage::registry('customer_connect_rfq_details');

            $this->_method = $helper->getShippingMethodMapping(
                $rfq->getDeliveryMethod(), Epicor_Comm_Helper_Messaging::ERP_TO_MAGENTO, false
            );
        }

        return $this->_method;
    }

    public function getShippingMethodsData()
    {
        if (!$this->_methods) {
            $helper = Mage::helper('customerconnect');
            /* @var $helper Epicor_Customerconnect_Helper_Data */

            $carriers = $helper->getShippingmethodList(true);

            $carriers['other'] = array(
                'value' => array(
                    'other_other' => array(
                        'value' => 'other',
                        'label' => $this->__('Please Specify Below'),
                    )
                ),
                'label' => $this->__('Other')
            );

            $this->_methods = $carriers;
        }

        return $this->_methods;
    }

    public function getRfqShippingMethodValue()
    {
        $carriers = $this->getShippingMethodsData();
        $mappedValue = $this->getMappedShippingMethod();

        $isOther = true;

        foreach ($carriers as $carrier) {
            foreach ($carrier['value'] as $method) {
                if ($method['value'] == $mappedValue) {
                    $isOther = false;
                }
            }
        }

        if ($mappedValue && $isOther) {
            $mappedValue = 'other';
        }

        return $mappedValue;
    }

    public function getRfqShippingMethodLabel()
    {
        $carriers = $this->getShippingMethodsData();
        $mapped = $this->getMappedShippingMethod();
        $label = $mapped;

        foreach ($this->getShippingMethodsData() as $carrier) {
            foreach ($carrier['value'] as $method) {
                if ($method['value'] == $mapped) {
                    $label = $carrier['label'] . ' - ' . $method['label'];
                }
            }
        }

        if ($label == $mapped) {
            $label = $this->__('Other') . ' - ' . $label;
        }

        return $label;
    }

    public function getJsonConfig()
    {
        $config = array(
            'priceFormat' => Mage::app()->getLocale()->getJsPriceFormat(),
        );

        return Mage::helper('core')->jsonEncode($config);
    }

}
