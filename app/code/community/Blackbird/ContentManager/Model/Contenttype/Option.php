<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category	Blackbird
 * @package		Blackbird_ContentManager
 * @copyright	Copyright (c) 2014 Blackbird Content Manager (http://black.bird.eu)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version		
 */

class Blackbird_ContentManager_Model_Contenttype_Option extends Mage_Core_Model_Abstract
{
    const OPTION_GROUP_TEXT   = 'text';
    const OPTION_GROUP_FILE   = 'file';
    const OPTION_GROUP_SELECT = 'select';
    const OPTION_GROUP_DATE   = 'date';
    const OPTION_GROUP_RELATION = 'relation';

    const OPTION_TYPE_FIELD     = 'field';
    const OPTION_TYPE_AREA      = 'area';
    const OPTION_TYPE_PASSWORD  = 'password';
    const OPTION_TYPE_FILE      = 'file';
    const OPTION_TYPE_IMAGE      = 'image';
    const OPTION_TYPE_DROP_DOWN = 'drop_down';
    const OPTION_TYPE_RADIO     = 'radio';
    const OPTION_TYPE_CHECKBOX  = 'checkbox';
    const OPTION_TYPE_MULTIPLE  = 'multiple';
    const OPTION_TYPE_DATE      = 'date';
    const OPTION_TYPE_DATE_TIME = 'date_time';
    const OPTION_TYPE_PRODUCT    = 'product';
    const OPTION_TYPE_CATEGORY    = 'category';
    const OPTION_TYPE_CONTENT    = 'content';
    const OPTION_TYPE_ATTRIBUTE    = 'attribute';

    protected $_contentType;

    protected $_options = array();

    protected $_valueInstance;

    protected $_values = array();

    protected function _construct()
    {
        $this->_init('contentmanager/contenttype_option');
    }

    /**
     * Add value of option to values array
     *
     * @param Blackbird_ContentManager_Model_Product_Option_Value $value
     * @return Blackbird_ContentManager_Model_Product_Option
     */
    public function addValue(Blackbird_ContentManager_Model_ContentType_Option_Value $value)
    {
        $this->_values[$value->getId()] = $value;
        return $this;
    }

    /**
     * Get value by given id
     *
     * @param int $valueId
     * @return Blackbird_ContentManager_Model_Product_Option_Value
     */
    public function getValueById($valueId)
    {
        if (isset($this->_values[$valueId])) {
            return $this->_values[$valueId];
        }

        return null;
    }

    public function getValues()
    {
        return $this->_values;
    }

    /**
     * Retrieve value instance
     *
     * @return Blackbird_ContentManager_Model_Product_Option_Value
     */
    public function getValueInstance()
    {
        if (!$this->_valueInstance) {
            $this->_valueInstance = Mage::getSingleton('contentmanager/contenttype_option_value');
        }
        return $this->_valueInstance;
    }

    /**
     * Add option for save it
     *
     * @param array $option
     * @return Blackbird_ContentManager_Model_Product_Option
     */
    public function addOption($option)
    {
        $this->_options[] = $option;
        return $this;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Set options for array
     *
     * @param array $options
     * @return Blackbird_ContentManager_Model_Product_Option
     */
    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Set options to empty array
     *
     * @return Blackbird_ContentManager_Model_Product_Option
     */
    public function unsetOptions()
    {
        $this->_options = array();
        return $this;
    }

    /**
     * Retrieve product instance
     *
     * @return Blackbird_ContentManager_Model_Product
     */
    public function getContentType()
    {
        return $this->_contentType;
    }

    /**
     * Set product instance
     *
     * @param Blackbird_ContentManager_Model_Product $product
     * @return Blackbird_ContentManager_Model_Product_Option
     */
    public function setContentType(Blackbird_ContentManager_Model_Contenttype $contentType = null)
    {
        $this->_contentType = $contentType;
        return $this;
    }

    /**
     * Get group name of option by given option type
     *
     * @param string $type
     * @return string
     */
    public function getGroupByType($type = null)
    {
        if (is_null($type)) {
            $type = $this->getType();
        }
        $optionGroupsToTypes = array(
            self::OPTION_TYPE_FIELD => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_AREA => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_PASSWORD => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_FILE => self::OPTION_GROUP_FILE,
            self::OPTION_TYPE_IMAGE => self::OPTION_GROUP_FILE,
            self::OPTION_TYPE_DROP_DOWN => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_RADIO => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_CHECKBOX => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_MULTIPLE => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_DATE => self::OPTION_GROUP_DATE,
            self::OPTION_TYPE_DATE_TIME => self::OPTION_GROUP_DATE,
            self::OPTION_TYPE_PRODUCT => self::OPTION_GROUP_RELATION,
            self::OPTION_TYPE_CATEGORY => self::OPTION_GROUP_RELATION,
            self::OPTION_TYPE_CONTENT => self::OPTION_GROUP_RELATION,
            self::OPTION_TYPE_ATTRIBUTE => self::OPTION_GROUP_RELATION,
        );

        return isset($optionGroupsToTypes[$type])?$optionGroupsToTypes[$type]:'';
    }

    /**
     * Group model factory
     *
     * @param string $type Option type
     * @return Blackbird_ContentManager_Model_Product_Option_Group_Abstract
     */
    public function groupFactory($type)
    {
        $group = $this->getGroupByType($type);
        if (!empty($group)) {
            return Mage::getModel('contentmanager/contenttype_option_type_' . $group);
        }
        Mage::throwException(Mage::helper('contentmanager')->__('Wrong option type to get group instance.'));
    }

    /**
     * Save options.
     *
     * @return Blackbird_ContentManager_Model_Product_Option
     */
    public function saveOptions()
    {
        foreach ($this->getOptions() as $option) {
            $this->setData($option)
                ->setData('ct_id', $this->getContentType()->getId())
                ->setData('store_id', $this->getContentType()->getStoreId());
            
            if ($this->getData('option_id') == '0') {
                $this->unsetData('option_id');
            } else {
                $this->setId($this->getData('option_id'));
            }

            $isEdit = (bool)$this->getId()? true:false;

            if ($this->getData('is_delete') == '1') {
                if ($isEdit) {
                    $this->getValueInstance()->deleteValue($this->getId());
                    //$this->deletePrices($this->getId());
                    $this->deleteTitles($this->getId());
                    $this->delete();
                }
            } else {
                if ($this->getData('previous_type') != '') {
                    $previousType = $this->getData('previous_type');

                    /**
                     * if previous option has different group from one is came now
                     * need to remove all data of previous group
                     */
                    if ($this->getGroupByType($previousType) != $this->getGroupByType($this->getData('type'))) {

                        switch ($this->getGroupByType($previousType)) {
                            case self::OPTION_GROUP_SELECT:
                                $this->unsetData('values');
                                if ($isEdit) {
                                    $this->getValueInstance()->deleteValue($this->getId());
                                }
                                break;
                            case self::OPTION_GROUP_FILE:
                                $this->setData('file_extension', '');
                                $this->setData('image_size_x', '0');
                                $this->setData('image_size_y', '0');
                                break;
                            case self::OPTION_GROUP_TEXT:
                                $this->setData('max_characters', '0');
                                break;
                            case self::OPTION_GROUP_DATE:
                                break;
                            case self::OPTION_GROUP_RELATION:
                                $this->setData('content_type', '0');
                                $this->setData('attribute', '0');
                                break;
                        }
                    }
                }
                $this->save();            
            }
        }//eof foreach()
        return $this;
    }

    protected function _afterSave()
    {
        $this->getValueInstance()->unsetValues();
        if (is_array($this->getData('values'))) {
            foreach ($this->getData('values') as $value) {
                $this->getValueInstance()->addValue($value);
            }

            $this->getValueInstance()->setOption($this)
                ->saveValues();
        } elseif ($this->getGroupByType($this->getType()) == self::OPTION_GROUP_SELECT) {
            Mage::throwException(Mage::helper('contentmanager')->__('Select type options required values rows.'));
        }

        return parent::_afterSave();
    }

    /**
     * Delete titles of option
     *
     * @param int $option_id
     * @return Blackbird_ContentManager_Model_Product_Option
     */
    public function deleteTitles($option_id)
    {
        $this->getResource()->deleteTitles($option_id);
        return $this;
    }

    /**
     * get Product Option Collection
     *
     * @param Blackbird_ContentManager_Model_Product $product
     * @return Blackbird_ContentManager_Model_Resource_Product_Option_Collection
     */
    public function getContentTypeOptionCollection(Blackbird_ContentManager_Model_Contenttype $contentType)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('ct_id', $contentType->getId())
            ->addTitleToResult($contentType->getStoreId())
            ->setOrder('sort_order', 'asc')
            ->setOrder('title', 'asc');

        if ($this->getAddRequiredFilter()) {
            $collection->addRequiredFilter($this->getAddRequiredFilterValue());
        }

        $collection->addValuesToResult($contentType->getStoreId());
        return $collection;
    }

    /**
     * Get collection of values for current option
     *
     * @return Blackbird_ContentManager_Model_Resource_Eav_Mysql4_Product_Option_Value_Collection
     */
    public function getValuesCollection()
    {
        $collection = $this->getValueInstance()
            ->getValuesCollection($this);

        return $collection;
    }

    /**
     * Get collection of values by given option ids
     *
     * @param array $optionIds
     * @param int $store_id
     * @return unknown
     */
    public function getOptionValuesByOptionId($optionIds, $store_id)
    {
        $collection = Mage::getModel('contentmanager/contenttype_option_value')
            ->getValuesByOption($optionIds, $this->getId(), $store_id);

        return $collection;
    }

    /**
     * Clearing object's data
     *
     * @return Blackbird_ContentManager_Model_Product_Option
     */
    protected function _clearData()
    {
        $this->_data = array();
        $this->_values = array();
        return $this;
    }

    /**
     * Clearing cyclic references
     *
     * @return Blackbird_ContentManager_Model_Product_Option
     */
    protected function _clearReferences()
    {
        if (!empty($this->_values)) {
            foreach ($this->_values as $value) {
                $value->unsetOption();
            }
        }
        return $this;
    }
}
