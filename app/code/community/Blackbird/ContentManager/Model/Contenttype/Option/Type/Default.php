<?php

/**
 * Catalog product option default type
 *
 * @category   Mage
 * @package    Blackbird_ContentManager
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Blackbird_ContentManager_Model_Contenttype_Option_Type_Default extends Varien_Object
{
    /**
     * Option Instance
     *
     * @var Blackbird_ContentManager_Model_Product_Option
     */
    protected $_option;

    /**
     * Product Instance
     *
     * @var Blackbird_ContentManager_Model_Product
     */
    protected $_contentType;



    /**
     * description
     *
     * @var    mixed
     */
    protected $_contentTypeOptions = array();

    /**
     * Option Instance setter
     *
     * @param Blackbird_ContentManager_Model_Product_Option $option
     * @return Blackbird_ContentManager_Model_Product_Option_Type_Default
     */
    public function setOption($option)
    {
        $this->_option = $option;
        return $this;
    }

    /**
     * Option Instance getter
     *
     * @throws Mage_Core_Exception
     * @return Blackbird_ContentManager_Model_Product_Option
     */
    public function getOption()
    {
        if ($this->_option instanceof Blackbird_ContentManager_Model_ContentType_Option) {
            return $this->_option;
        }
        Mage::throwException(Mage::helper('contentmanager')->__('Wrong option instance type in options group.'));
    }

    /**
     * Product Instance setter
     *
     * @param Blackbird_ContentManager_Model_Product $product
     * @return Blackbird_ContentManager_Model_Product_Option_Type_Default
     */
    public function setContentType($contentType)
    {
        $this->_contentType = $contentType;
        return $this;
    }

    /**
     * Product Instance getter
     *
     * @throws Mage_Core_Exception
     * @return Blackbird_ContentManager_Model_Product
     */
    public function getContentType()
    {
        if ($this->_contentType instanceof Blackbird_ContentManager_Model_ContentType) {
            return $this->_contentType;
        }
        Mage::throwException(Mage::helper('contentmanager')->__('Wrong product instance type in options group.'));
    }

    /**
     * Store Config value
     *
     * @param string $key Config value key
     * @return string
     */
    public function getConfigData($key)
    {
        return Mage::getStoreConfig('contentmanager/custom_options/' . $key);
    }

    /**
     * Validate user input for option
     *
     * @throws Mage_Core_Exception
     * @param array $values All product option values, i.e. array (option_id => mixed, option_id => mixed...)
     * @return Blackbird_ContentManager_Model_Product_Option_Type_Default
     */
    public function validateUserValue($values)
    {
        Mage::getSingleton('checkout/session')->setUseNotice(false);

        $this->setIsValid(false);

        $option = $this->getOption();
        if (!isset($values[$option->getId()]) && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
            Mage::throwException(Mage::helper('contentmanager')->__('Please specify the product required option(s).'));
        } elseif (isset($values[$option->getId()])) {
            $this->setUserValue($values[$option->getId()]);
            $this->setIsValid(true);
        }
        return $this;
    }

    /**
     * Check skip required option validation
     *
     * @return bool
     */
    public function getSkipCheckRequiredOption()
    {
        return $this->getContentType()->getSkipCheckRequiredOption() ||
            $this->getProcessMode() == Blackbird_ContentManager_Model_ContentType_Type_Abstract::PROCESS_MODE_LITE;
    }

    /**
     * Flag to indicate that custom option has own customized output (blocks, native html etc.)
     *
     * @return boolean
     */
    public function isCustomizedView()
    {
        return false;
    }

    /**
     * Return formatted option value for quote option
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getFormattedOptionValue($optionValue)
    {
        return $optionValue;
    }

    /**
     * Return option html
     *
     * @param array $optionInfo
     * @return string
     */
    public function getCustomizedView($optionInfo)
    {
        return isset($optionInfo['value']) ? $optionInfo['value'] : $optionInfo;
    }

    /**
     * Return printable option value
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getPrintableOptionValue($optionValue)
    {
        return $optionValue;
    }

    /**
     * Return formatted option value ready to edit, ready to parse
     * (ex: Admin re-order, see Mage_Adminhtml_Model_Sales_Order_Create)
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getEditableOptionValue($optionValue)
    {
        return $optionValue;
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
        return $optionValue;
    }

    /**
     * Prepare option value for info buy request
     *
     * @param string $optionValue
     * @return mixed
     */
    public function prepareOptionValueForRequest($optionValue)
    {
        return $optionValue;
    }

    /**
     * Return Price for selected option
     *
     * @param string $optionValue Prepared for cart option value
     * @param float $basePrice For percent price type
     * @return float
     */
    /*public function getOptionPrice($optionValue, $basePrice)
    {
        $option = $this->getOption();

        return $this->_getChargableOptionPrice(
            $option->getPrice(),
            $option->getPriceType() == 'percent',
            $basePrice
        );
    }*/

    /**
     * Return SKU for selected option
     *
     * @param string $optionValue Prepared for cart option value
     * @param string $skuDelimiter Delimiter for Sku parts
     * @return string
     */
    /*public function getOptionSku($optionValue, $skuDelimiter)
    {
        return $this->getOption()->getSku();
    }*/

    /**
     * Return value => key all product options (using for parsing)
     *
     * @return array Array of Product custom options, reversing option values and option ids
     */
    public function getContentTypeOptions()
    {
        if (!isset($this->_contentTypeOptions[$this->getContentType()->getId()])) {
            foreach ($this->getContentType()->getOptions() as $_option) {
                /* @var $option Blackbird_ContentManager_Model_Product_Option */
                $this->_contentTypeOptions[$this->getContentType()->getId()][$_option->getTitle()] = array('option_id' => $_option->getId());
                if ($_option->getGroupByType() == Blackbird_ContentManager_Model_ContentType_Option::OPTION_GROUP_SELECT) {
                    $optionValues = array();
                    foreach ($_option->getValues() as $_value) {
                        /* @var $value Blackbird_ContentManager_Model_Product_Option_Value */
                        $optionValues[$_value->getTitle()] = $_value->getId();
                    }
                    $this->_contentTypeOptions[$this->getContentType()->getId()][$_option->getTitle()]['values'] = $optionValues;
                } else {
                    $this->_contentTypeOptions[$this->getContentType()->getId()][$_option->getTitle()]['values'] = array();
                }
            }
        }
        if (isset($this->_contentTypeOptions[$this->getContentType()->getId()])) {
            return $this->_contentType[$this->getContentType()->getId()];
        }
        return array();
    }

    /**
     * Return final chargable price for option
     *
     * @param float $price Price of option
     * @param boolean $isPercent Price type - percent or fixed
     * @param float $basePrice For percent price type
     * @return float
     */
    protected function _getChargableOptionPrice($price, $isPercent, $basePrice)
    {
        if($isPercent) {
            return ($basePrice * $price / 100);
        } else {
            return $price;
        }
    }

}
