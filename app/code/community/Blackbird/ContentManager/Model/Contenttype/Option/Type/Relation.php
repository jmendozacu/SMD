<?php

/**
 * Catalog product option text type
 *
 * @category   Mage
 * @package    Blackbird_ContentManager
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Blackbird_ContentManager_Model_Contenttype_Option_Type_Relation extends Blackbird_ContentManager_Model_Contenttype_Option_Type_Default
{
    /**
     * Validate user input for option
     *
     * @throws Mage_Core_Exception
     * @param array $values All product option values, i.e. array (option_id => mixed, option_id => mixed...)
     * @return Blackbird_ContentManager_Model_Product_Option_Type_Default
     */
    public function validateUserValue($values)
    {
        parent::validateUserValue($values);

        $option = $this->getOption();
        $value = trim($this->getUserValue());

        // Check requires option to have some value
        if (strlen($value) == 0 && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
            $this->setIsValid(false);
            Mage::throwException(Mage::helper('contentmanager')->__('Please specify the product\'s required option(s).'));
        }

        // Check maximal length limit
        $maxCharacters = $option->getMaxCharacters();
        if ($maxCharacters > 0 && Mage::helper('core/string')->strlen($value) > $maxCharacters) {
            $this->setIsValid(false);
            Mage::throwException(Mage::helper('contentmanager')->__('The text is too long'));
        }

        $this->setUserValue($value);
        return $this;
    }

    /**
     * Return formatted option value for quote option
     *
     * @param string $value Prepared for cart option value
     * @return string
     */
    public function getFormattedOptionValue($value)
    {
        return Mage::helper('core')->htmlEscape($value);
    }
}
