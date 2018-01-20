<?php

class Epicor_Customerconnect_Block_Adminhtml_Config_Form_Field_Crqs_Mapping
        extends Mage_Core_Block_Html_Select
{

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    public function setColumnName($value)
    {
        return $this->setExtraParams('rel="#{index}" style="width:200px"');
    }

    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->addOption('quote_number', $this->__('quoteNumber'));
            $this->addOption('quote_sequence', $this->__('quoteSequence'));
            $this->addOption('quote_date', $this->__('quoteDate'));
            $this->addOption('due_date', $this->__('dueDate'));
            $this->addOption('description', $this->__('description'));
            $this->addOption('currency_code', $this->__('currencyCode'));
            $this->addOption('original_value', $this->__('originalValue'));
            $this->addOption('customer_reference', $this->__('customerReference'));
            $this->addOption('quote_status', $this->__('quoteStatus'));
            $this->addOption('quote_entered', $this->__('quoteEntered'));
            $this->addOption('quote_delivery_address', $this->__('quoteDeliveryAddress'));
            $this->addOption('quote_delivery_address_customer_code', $this->__('quoteDeliveryAddress > customerCode'));
            $this->addOption('quote_delivery_address_name', $this->__('quoteDeliveryAddress > name'));
            $this->addOption('quote_delivery_address_address1', $this->__('quoteDeliveryAddress > address1'));
            $this->addOption('quote_delivery_address_address2', $this->__('quoteDeliveryAddress > address2'));
            $this->addOption('quote_delivery_address_address3', $this->__('quoteDeliveryAddress > address3'));
            $this->addOption('quote_delivery_address_city', $this->__('quoteDeliveryAddress > city'));
            $this->addOption('quote_delivery_address_county', $this->__('quoteDeliveryAddress > county'));
            $this->addOption('quote_delivery_address_country', $this->__('quoteDeliveryAddress > country'));
            $this->addOption('quote_delivery_address_postcode', $this->__('quoteDeliveryAddress > postcode'));
            $this->addOption('quote_delivery_address_telephone_number', $this->__('quoteDeliveryAddress > telephoneNumber'));
            $this->addOption('quote_delivery_address_fax_number', $this->__('quoteDeliveryAddress > faxNumber'));
            $this->addOption('contracts_contract_code', $this->__('contractCode'));
        }
        return parent::_toHtml();
    }

}
