<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shiprules
 */
class Amasty_Shiprules_Model_Rule_Condition_Address extends Amasty_Commonrules_Model_Rule_Condition_Address
{
    public function loadAttributeOptions()
    {
        $attributes = array(
            'package_value'    => Mage::helper('salesrule')->__('Subtotal'),
//removed because we have an option in the configuration now, but if this setting is already set it will continue to be checked
//            'package_value_with_discount'   => Mage::helper('salesrule')->__('Subtotal with discount'),
            'package_qty'      => Mage::helper('salesrule')->__('Total Items Quantity'),
            'package_weight'   => Mage::helper('salesrule')->__('Total Weight'),
            'dest_postcode'    => Mage::helper('salesrule')->__('Shipping Postcode'),
            'dest_region_id'   => Mage::helper('salesrule')->__('Shipping State/Province'),
            'dest_country_id'  => Mage::helper('salesrule')->__('Shipping Country'),
            'dest_city'        => Mage::helper('salesrule')->__('Shipping City'),
            'dest_street'      => Mage::helper('salesrule')->__('Shipping Address Line'),
        );

        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'package_value': case 'package_weight': case 'package_qty':
                return 'numeric';

            case 'dest_country_id': case 'dest_region_id':
                return 'select';
        }
        return 'string';
    }

    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'dest_country_id': case 'dest_region_id':
                return 'select';
        }
        return 'text';
    }

    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'dest_country_id':
                    $options = Mage::getModel('adminhtml/system_config_source_country')
                        ->toOptionArray();
                    break;

                case 'dest_region_id':
                    $options = Mage::getModel('adminhtml/system_config_source_allregion')
                        ->toOptionArray();
                    break;

                default:
                    $options = array();
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }
    
    public function getOperatorSelectOptions()
    {
        $operators = $this->getOperatorOption();
        if ($this->getAttribute() == 'dest_street') {
             $operators = array(
                '{}'  => Mage::helper('rule')->__('contains'),
                '!{}' => Mage::helper('rule')->__('does not contain'), 
                '{%'  => Mage::helper('rule')->__('starts from'),           
                '%}'  => Mage::helper('rule')->__('ends with'),           
             );
        }

        return parent::_getOperatorOptions($operators);
    }

    public function validate(Varien_Object $object)
    {
        return Mage_Rule_Model_Condition_Abstract::validate($object);
    }
}