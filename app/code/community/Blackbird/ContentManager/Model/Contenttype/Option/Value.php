<?php

/**
 * Catalog product option select type model
 *
 * @method Blackbird_ContentManager_Model_Resource_Product_Option_Value _getResource()
 * @method Blackbird_ContentManager_Model_Resource_Product_Option_Value getResource()
 * @method int getOptionId()
 * @method Blackbird_ContentManager_Model_Product_Option_Value setOptionId(int $value)
 * @method string getSku()
 * @method Blackbird_ContentManager_Model_Product_Option_Value setSku(string $value)
 * @method int getSortOrder()
 * @method Blackbird_ContentManager_Model_Product_Option_Value setSortOrder(int $value)
 *
 * @category    Mage
 * @package     Blackbird_ContentManager
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Blackbird_ContentManager_Model_Contenttype_Option_Value extends Mage_Core_Model_Abstract
{
    protected $_values = array();

    protected $_contenttype;

    protected $_option;

    protected function _construct()
    {
        $this->_init('contentmanager/contenttype_option_value');
    }

    public function addValue($value)
    {
        $this->_values[] = $value;
        return $this;
    }

    public function getValues()
    {
        return $this->_values;
    }

    public function setValues($values)
    {
        $this->_values = $values;
        return $this;
    }

    public function unsetValues()
    {
        $this->_values = array();
        return $this;
    }

    public function setOption(Blackbird_ContentManager_Model_ContentType_Option $option)
    {
        $this->_option = $option;
        return $this;
    }

    public function unsetOption()
    {
        $this->_option = null;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Blackbird_ContentManager_Model_Product_Option
     */
    public function getOption()
    {
        return $this->_option;
    }

    public function setContentType($contentType)
    {
        $this->_contenttype = $contentType;
        return $this;
    }

    public function getContentType()
    {
        if (is_null($this->_contenttype)) {
            $this->_contenttype = $this->getOption()->getContentType();
        }
        return $this->_contenttype;
    }

    public function saveValues()
    {
        foreach ($this->getValues() as $value) {
            $this->setData($value)
                ->setData('option_id', $this->getOption()->getId())
                ->setData('store_id', $this->getOption()->getStoreId());

            if ($this->getData('option_type_id') == '-1') {//change to 0
                $this->unsetData('option_type_id');
            } else {
                $this->setId($this->getData('option_type_id'));
            }

            if ($this->getData('is_delete') == '1') {
                if ($this->getId()) {
                    $this->deleteValues($this->getId());
                    $this->delete();
                }
            } else {
                $this->save();
            }
        }//eof foreach()
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Blackbird_ContentManager_Model_Product_Option $option
     * @return Blackbird_ContentManager_Model_Resource_Eav_Mysql4_Product_Option_Value_Collection
     */
    public function getValuesCollection(Blackbird_ContentManager_Model_ContentType_Option $option)
    {
        $collection = Mage::getResourceModel('contentmanager/contenttype_option_value_collection')
            ->addFieldToFilter('option_id', $option->getId())
            ->getValues($option->getStoreId());

        return $collection;
    }

    public function getValuesByOption($optionIds, $option_id, $store_id)
    {
        $collection = Mage::getResourceModel('contentmanager/contenttype_option_value_collection')
            ->addFieldToFilter('option_id', $option_id)
            ->getValuesByOption($optionIds, $store_id);

        return $collection;
    }

    public function deleteValue($option_id)
    {
        $this->getResource()->deleteValue($option_id);
        return $this;
    }

    public function deleteValues($option_type_id)
    {
        $this->getResource()->deleteValues($option_type_id);
        return $this;
    }
}
