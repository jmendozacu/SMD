<?php

/**
 * Catalog product option select type
 *
 * @category   Mage
 * @package    Blackbird_ContentManager
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Blackbird_ContentManager_Model_Contenttype_Option_Type_Select extends Blackbird_ContentManager_Model_Contenttype_Option_Type_Default
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
        $value = $this->getUserValue();

        if (empty($value) && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
            $this->setIsValid(false);
            Mage::throwException(Mage::helper('contentmanager')->__('Please specify the product required option(s).'));
        }
        if (!$this->_isSingleSelection()) {
            $valuesCollection = $option->getOptionValuesByOptionId($value, $this->getContentType()->getStoreId())
                ->load();
            if ($valuesCollection->count() != count($value)) {
                $this->setIsValid(false);
                Mage::throwException(Mage::helper('contentmanager')->__('Please specify the product required option(s).'));
            }
        }
        return $this;
    }

    /**
     * Return formatted option value for quote option
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getFormattedOptionValue($optionValue)
    {
        if ($this->_formattedOptionValue === null) {
            $this->_formattedOptionValue = Mage::helper('core')->htmlEscape(
                $this->getEditableOptionValue($optionValue)
            );
        }
        return $this->_formattedOptionValue;
    }

    /**
     * Return printable option value
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getPrintableOptionValue($optionValue)
    {
        return $this->getFormattedOptionValue($optionValue);
    }

    /**
     * Return wrong product configuration message
     *
     * @return string
     */
    protected function _getWrongConfigurationMessage()
    {
        return Mage::helper('contentmanager')->__('Some of the products below do not have all the required options. Please edit them and configure all the required options.');
    }

    /**
     * Return formatted option value ready to edit, ready to parse
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getEditableOptionValue($optionValue)
    {
        $option = $this->getOption();
        $result = '';
        if (!$this->_isSingleSelection()) {
            foreach (explode(',', $optionValue) as $_value) {
                if ($_result = $option->getValueById($_value)) {
                    $result .= $_result->getTitle() . ', ';
                } else {
                    if ($this->getListener()) {
                        $this->getListener()
                                ->setHasError(true)
                                ->setMessage(
                                    $this->_getWrongConfigurationMessage()
                                );
                        $result = '';
                        break;
                    }
                }
            }
            $result = Mage::helper('core/string')->substr($result, 0, -2);
        } elseif ($this->_isSingleSelection()) {
            if ($_result = $option->getValueById($optionValue)) {
                $result = $_result->getTitle();
            } else {
                if ($this->getListener()) {
                    $this->getListener()
                            ->setHasError(true)
                            ->setMessage(
                                $this->_getWrongConfigurationMessage()
                            );
                }
                $result = '';
            }
        } else {
            $result = $optionValue;
        }
        return $result;
    }

    /**
     * Parse user input value and return cart prepared value, i.e. "one, two" => "1,2"
     *
     * @param string $optionValue
     * @param array $productOptionValues Values for product option
     * @return string|null
     */
    public function parseOptionValue($optionValue, $contentTypeOptionValues)
    {
        $_values = array();
        if (!$this->_isSingleSelection()) {
            foreach (explode(',', $optionValue) as $_value) {
                $_value = trim($_value);
                if (array_key_exists($_value, $contentTypeOptionValues)) {
                    $_values[] = $contentTypeOptionValues[$_value];
                }
            }
        } elseif ($this->_isSingleSelection() && array_key_exists($optionValue, $contentTypeOptionValues)) {
            $_values[] = $contentTypeOptionValues[$optionValue];
        }
        if (count($_values)) {
            return implode(',', $_values);
        } else {
            return null;
        }
    }

    /**
     * Prepare option value for info buy request
     *
     * @param string $optionValue
     * @return mixed
     */
    public function prepareOptionValueForRequest($optionValue)
    {
        if (!$this->_isSingleSelection()) {
            return explode(',', $optionValue);
        }
        return $optionValue;
    }

    /**
     * Return Price for selected option
     *
     * @param string $optionValue Prepared for cart option value
     * @return float
     */
    public function getOptionPrice($optionValue, $basePrice)
    {
        $option = $this->getOption();
        $result = 0;

        if (!$this->_isSingleSelection()) {
            foreach(explode(',', $optionValue) as $value) {
                if ($_result = $option->getValueById($value)) {
                    $result += $this->_getChargableOptionPrice(
                        $_result->getPrice(),
                        $_result->getPriceType() == 'percent',
                        $basePrice
                    );
                } else {
                    if ($this->getListener()) {
                        $this->getListener()
                                ->setHasError(true)
                                ->setMessage(
                                    $this->_getWrongConfigurationMessage()
                                );
                        break;
                    }
                }
            }
        } elseif ($this->_isSingleSelection()) {
            if ($_result = $option->getValueById($optionValue)) {
                $result = $this->_getChargableOptionPrice(
                    $_result->getPrice(),
                    $_result->getPriceType() == 'percent',
                    $basePrice
                );
            } else {
                if ($this->getListener()) {
                    $this->getListener()
                            ->setHasError(true)
                            ->setMessage(
                                $this->_getWrongConfigurationMessage()
                            );
                }
            }
        }

        return $result;
    }

    /**
     * Return SKU for selected option
     *
     * @param string $optionValue Prepared for cart option value
     * @param string $skuDelimiter Delimiter for Sku parts
     * @return string
     */
    public function getOptionSku($optionValue, $skuDelimiter)
    {
        $option = $this->getOption();

        if (!$this->_isSingleSelection()) {
            $skus = array();
            foreach(explode(',', $optionValue) as $value) {
                if ($optionSku = $option->getValueById($value)) {
                    $skus[] = $optionSku->getSku();
                } else {
                    if ($this->getListener()) {
                        $this->getListener()
                                ->setHasError(true)
                                ->setMessage(
                                    $this->_getWrongConfigurationMessage()
                                );
                        break;
                    }
                }
            }
            $result = implode($skuDelimiter, $skus);
        } elseif ($this->_isSingleSelection()) {
            if ($result = $option->getValueById($optionValue)) {
                return $result->getSku();
            } else {
                if ($this->getListener()) {
                    $this->getListener()
                            ->setHasError(true)
                            ->setMessage(
                                $this->_getWrongConfigurationMessage()
                            );
                }
                return '';
            }
        } else {
            $result = parent::getOptionSku($optionValue, $skuDelimiter);
        }

        return $result;
    }

    /**
     * Check if option has single or multiple values selection
     *
     * @return boolean
     */
    protected function _isSingleSelection()
    {
        $_single = array(
            Blackbird_ContentManager_Model_ContentType_Option::OPTION_TYPE_DROP_DOWN,
            Blackbird_ContentManager_Model_ContentType_Option::OPTION_TYPE_RADIO,
        );
        return in_array($this->getOption()->getType(), $_single);
    }
}
