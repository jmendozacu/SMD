<?php

/**
 * Catalog product option date type
 *
 * @category   Mage
 * @package    Blackbird_ContentManager
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Blackbird_ContentManager_Model_Contenttype_Option_Type_Date extends Blackbird_ContentManager_Model_Contenttype_Option_Type_Default
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

        $dateValid = true;
        if ($this->_dateExists()) {
            if ($this->useCalendar()) {
                $dateValid = isset($value['date']) && preg_match('/^\d{1,4}.+\d{1,4}.+\d{1,4}$/', $value['date']);
            } else {
                $dateValid = isset($value['day']) && isset($value['month']) && isset($value['year'])
                    && $value['day'] > 0 && $value['month'] > 0 && $value['year'] > 0;
            }
        }

        $timeValid = true;
        if ($this->_timeExists()) {
            $timeValid = isset($value['hour']) && isset($value['minute'])
                && is_numeric($value['hour']) && is_numeric($value['minute']);
        }

        $isValid = $dateValid && $timeValid;

        if ($isValid) {
            $this->setUserValue(
                array(
                    'date' => isset($value['date']) ? $value['date'] : '',
                    'year' => isset($value['year']) ? intval($value['year']) : 0,
                    'month' => isset($value['month']) ? intval($value['month']) : 0,
                    'day' => isset($value['day']) ? intval($value['day']) : 0,
                    'hour' => isset($value['hour']) ? intval($value['hour']) : 0,
                    'minute' => isset($value['minute']) ? intval($value['minute']) : 0,
                    'day_part' => isset($value['day_part']) ? $value['day_part'] : '',
                    'date_internal' => isset($value['date_internal']) ? $value['date_internal'] : '',
                )
            );
        } elseif (!$isValid && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
            $this->setIsValid(false);
            if (!$dateValid) {
                Mage::throwException(Mage::helper('contentmanager')->__('Please specify date required option(s).'));
            } elseif (!$timeValid) {
                Mage::throwException(Mage::helper('contentmanager')->__('Please specify time required option(s).'));
            } else {
                Mage::throwException(Mage::helper('contentmanager')->__('Please specify the product required option(s).'));
            }
        } else {
            $this->setUserValue(null);
            return $this;
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

            $option = $this->getOption();
            if ($this->getOption()->getType() == Blackbird_ContentManager_Model_ContentType_Option::OPTION_TYPE_DATE) {
                $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
                $result = Mage::app()->getLocale()->date($optionValue, Zend_Date::ISO_8601, null, false)
                    ->toString($format);
            } elseif ($this->getOption()->getType() == Blackbird_ContentManager_Model_ContentType_Option::OPTION_TYPE_DATE_TIME) {
                $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
                $result = Mage::app()->getLocale()
                    ->date($optionValue, Varien_Date::DATETIME_INTERNAL_FORMAT, null, false)->toString($format);
            } elseif ($this->getOption()->getType() == Blackbird_ContentManager_Model_ContentType_Option::OPTION_TYPE_TIME) {
                $date = new Zend_Date($optionValue);
                $result = date($this->is24hTimeFormat() ? 'H:i' : 'h:i a', $date->getTimestamp());
            } else {
                $result = $optionValue;
            }
            $this->_formattedOptionValue = $result;
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
     * Return formatted option value ready to edit, ready to parse
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getEditableOptionValue($optionValue)
    {
        return $this->getFormattedOptionValue($optionValue);
    }

    /**
     * Parse user input value and return cart prepared value
     *
     * @param string $optionValue
     * @param array $productOptionValues Values for product option
     * @return string|null
     */
    public function parseOptionValue($optionValue, $contentTypeOptionValues)
    {
        $timestamp = strtotime($optionValue);
        if ($timestamp === false || $timestamp == -1) {
            return null;
        }

        $date = new Zend_Date($timestamp);
        return $date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    }

    /**
     * Prepare option value for info buy request
     *
     * @param string $optionValue
     * @return mixed
     */
    public function prepareOptionValueForRequest($optionValue)
    {
        $confItem = $this->getConfigurationItem();
        $infoBuyRequest = $confItem->getOptionByCode('info_buyRequest');
        try {
            $value = unserialize($infoBuyRequest->getValue());
            if (is_array($value) && isset($value['options']) && isset($value['options'][$this->getOption()->getId()])) {
                return $value['options'][$this->getOption()->getId()];
            } else {
                return array('date_internal' => $optionValue);
            }
        } catch (Exception $e) {
            return array('date_internal' => $optionValue);
        }
    }

    /**
     * Use Calendar on frontend or not
     *
     * @return boolean
     */
    public function useCalendar()
    {
        return (bool)$this->getConfigData('use_calendar');
    }

    /**
     * Time Format
     *
     * @return boolean
     */
    public function is24hTimeFormat()
    {
        return (bool)($this->getConfigData('time_format') == '24h');
    }

    /**
     * Year range start
     *
     * @return mixed
     */
    public function getYearStart()
    {
        $_range = explode(',', $this->getConfigData('year_range'));
        if (isset($_range[0]) && !empty($_range[0])) {
            return $_range[0];
        } else {
            return date('Y');
        }
    }

    /**
     * Year range end
     *
     * @return mixed
     */
    public function getYearEnd()
    {
        $_range = explode(',', $this->getConfigData('year_range'));
        if (isset($_range[1]) && !empty($_range[1])) {
            return $_range[1];
        } else {
            return date('Y');
        }
    }

    /**
     * Save internal value of option in infoBuy_request
     *
     * @param string $internalValue Datetime value in internal format
     * @return Blackbird_ContentManager_Model_Product_Option_Type_Date
     */
    protected function _setInternalInRequest($internalValue)
    {
        $requestOptions = $this->getRequest()->getOptions();
        if (!isset($requestOptions[$this->getOption()->getId()])) {
            $requestOptions[$this->getOption()->getId()] = array();
        }
        $requestOptions[$this->getOption()->getId()]['date_internal'] = $internalValue;
        $this->getRequest()->setOptions($requestOptions);
    }

    /**
     * Does option have date?
     *
     * @return boolean
     */
    protected function _dateExists()
    {
        return in_array($this->getOption()->getType(), array(
            Blackbird_ContentManager_Model_ContentType_Option::OPTION_TYPE_DATE,
            Blackbird_ContentManager_Model_ContentType_Option::OPTION_TYPE_DATE_TIME
        ));
    }

    /**
     * Does option have time?
     *
     * @return boolean
     */
    protected function _timeExists()
    {
        return in_array($this->getOption()->getType(), array(
            Blackbird_ContentManager_Model_ContentType_Option::OPTION_TYPE_DATE_TIME,
            Blackbird_ContentManager_Model_ContentType_Option::OPTION_TYPE_TIME
        ));
    }
}
