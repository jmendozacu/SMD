<?php

/**
 * Product option edit tab override
 * 
 * Adds new field types for EWA
 * 
 * @category   Epicor
 * @package    Epicor_Comm
 * @author     Epicor Websales Team
 */
class Epicor_Comm_Block_Adminhtml_Catalog_Product_Edit_Tab_Options_Option extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('epicor_comm/catalog/product/edit/options/option.phtml');
    }

    /**
     * Retrieve html templates for different types of product custom options
     *
     * @return string
     */
    public function getTemplatesHtml()
    {
        $templates = parent::getTemplatesHtml();

        $templates .= "\n" . $this->getChildHtml('ewa_option_type');
        $templates .= "\n" . $this->getChildHtml('ecc_text_option_type');

        return $templates;
    }

    public function getOptionValues()
    {
        parent::getOptionValues();

        if (!empty($this->_values)) {

            $optionsArr = array_reverse($this->getProduct()->getOptions(), true);
            $options = array();
            foreach ($optionsArr as $option) {
                $options[$option->getOptionId()] = $option;
            }

            $values = array();

            foreach ($this->_values as $option) {

                $pOption = $options[$option->getId()];

                $option->setEpicorCode($pOption->getEpicorCode());
                $option->setEpicorDefaultValue($pOption->getEpicorDefaultValue());
                $option->setEpicorValidationCode($pOption->getEpicorValidationCode());

                $values[] = $option;
            }

            $this->_values = $values;
        }

        return $this->_values;
    }

}
